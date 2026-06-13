<?php
// Server-side proxy for Mollie API and Booking Handler
// Loads API key from environment and prevents exposing it to the client
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');
// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
// Only advertise HSTS over HTTPS; sending it on plain HTTP is ignored and misleading in dev.
$__mollieHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
    || (($_SERVER['SERVER_PORT'] ?? '') === '443');
if ($__mollieHttps) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}
header('Cross-Origin-Resource-Policy: same-origin');

// Load shared components
require_once __DIR__ . '/components/data_access.php';
require_once __DIR__ . '/components/pricing_engine.php';
require_once __DIR__ . '/components/security.php';

// Load .env file
loadEnvSafe(__DIR__ . '/.env');

require_once __DIR__ . '/components/config.php';

// Load centralized CORS configuration
require_once __DIR__ . '/components/cors.php';

// Handle CORS preflight requests using centralized function
handleCorsPreflight();

// Strict same-origin / allowed-origin check. Rejects requests that don't provide
// any browser-origin signal (Origin or Referer), which closes the previous
// default-allow hole that let non-browser clients hit payment endpoints.
function isSameOrigin() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $allowedOrigins = getAllowedOrigins();

    $matchesAllowed = function(string $candidate) use ($allowedOrigins, $host): bool {
        if ($candidate === '') return false;
        $clean = rtrim($candidate, '/');
        if (in_array($clean, $allowedOrigins, true)) return true;
        $parsed = parse_url($candidate);
        if (!isset($parsed['host'])) return false;
        return strtolower($parsed['host']) === strtolower(explode(':', $host)[0]);
    };

    if ($origin !== '') {
        if (setCorsHeadersSafe($origin, $allowedOrigins, false)) {
            return true;
        }
        return $matchesAllowed($origin);
    }

    // No Origin header: require Referer on the allowlist. Fail closed otherwise.
    if ($referer !== '') {
        return $matchesAllowed($referer);
    }

    return false;
}

if (!isSameOrigin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

$mollieApiKey = getenv('MOLLIE_API_KEY') ?: '';
$mockMollieActive = mockMolliePaymentsEnabled();
if ($mollieApiKey === '' && !$mockMollieActive) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Mollie API key not configured.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
// SECURITY: Sanitize GET/POST parameters
$action = sanitizeText($_GET['action'] ?? ($_POST['action'] ?? ''));
$baseUrl = 'https://api.mollie.com/v2';
$bookingsFile = nijenhuis_data_path('bookings.json');
$boatsFile = nijenhuis_data_path('boats.json');

/**
 * Map Mollie payment status -> booking status used by admin UI.
 * - paid => paid (green / "Betaald")
 * - failed/expired/canceled => canceled (red / "Geannuleerd")
 * - open/pending => pending (transient)
 */
/**
 * Return a public-safe view of a booking for status endpoints hit from the
 * browser. Strips PII (name, email, phone, address, city, free-form notes)
 * so an attacker who guesses a bookingId cannot enumerate customer data.
 */
function publicBookingView(array $booking): array {
    $publicKeys = [
        'id', 'cartId', 'date', 'endDate', 'boatType', 'boatName',
        'numberOfDays', 'quantity', 'amount', 'rentalAmount',
        'administrationFee', 'administrationFeePercent',
        'reservationFee', 'reservationFeeRentalPortion',
        'reservationFeeAdminOnReservation', 'reservationFeePercent',
        'balanceDueOnArrival', 'payOnArrivalReservation', 'paymentMethod',
        'arrivalTime', 'status', 'paymentStatus', 'paymentId',
        'source', 'createdAt', 'updatedAt',
    ];
    $out = [];
    foreach ($publicKeys as $k) {
        if (array_key_exists($k, $booking)) {
            $out[$k] = $booking[$k];
        }
    }
    return $out;
}

function mapMollieStatusToBookingStatus($paymentStatus) {
    $paymentStatus = strtolower((string)$paymentStatus);
    if ($paymentStatus === 'paid') return 'paid';
    if (in_array($paymentStatus, ['failed', 'expired', 'canceled'], true)) return 'canceled';
    if (in_array($paymentStatus, ['open', 'pending'], true)) return 'pending';
    // Unknown or transitional states should not flip bookings to canceled automatically
    return null;
}

/**
 * Persist booking status updates in bookings.json for a single booking.
 * Returns updated booking array (or null if not found).
 */
function syncSingleBookingPaymentStatus($bookingId, $paymentStatus) {
    global $bookingsFile;
    $newBookingStatus = mapMollieStatusToBookingStatus($paymentStatus);
    if ($newBookingStatus === null) return null;

    $bookings = loadJsonSafe($bookingsFile);
    $updatedBooking = null;
    $changed = false;

    foreach ($bookings as &$b) {
        if (($b['id'] ?? '') !== $bookingId) continue;
        $prevStatus = $b['status'] ?? '';
        $b['paymentStatus'] = $paymentStatus;
        $b['updatedAt'] = date('c');
        $mappedStatus = $newBookingStatus;
        if ($mappedStatus === 'paid'
            && isPayOnArrivalMethod((string)($b['paymentMethod'] ?? ''))
            && !empty($b['payOnArrivalReservation'])) {
            $mappedStatus = 'confirmed';
        }
        if ($prevStatus !== $mappedStatus) {
            $b['status'] = $mappedStatus;
            $changed = true;
        }
        $updatedBooking = $b;
        break;
    }

    if ($updatedBooking && $changed) {
        saveJsonSafe($bookingsFile, $bookings);
    }

    if ($updatedBooking && strtolower((string)$paymentStatus) === 'paid') {
        bookingConfirmationTrySendAfterSync($bookingsFile, $bookings, $bookingId);
    }

    return $updatedBooking;
}

/**
 * Persist booking status updates in bookings.json for all bookings in a cart.
 * Returns updated bookings array (possibly empty).
 */
function syncCartBookingsPaymentStatus($cartId, $paymentStatus) {
    global $bookingsFile;
    $newBookingStatus = mapMollieStatusToBookingStatus($paymentStatus);
    if ($newBookingStatus === null) return [];

    $bookings = loadJsonSafe($bookingsFile);
    $updated = [];
    $touched = false;

    foreach ($bookings as &$b) {
        if (($b['cartId'] ?? '') !== $cartId) continue;
        $prevStatus = $b['status'] ?? '';
        $b['paymentStatus'] = $paymentStatus;
        $b['updatedAt'] = date('c');
        $mappedStatus = $newBookingStatus;
        if ($mappedStatus === 'paid'
            && isPayOnArrivalMethod((string)($b['paymentMethod'] ?? ''))
            && !empty($b['payOnArrivalReservation'])) {
            $mappedStatus = 'confirmed';
        }
        if ($prevStatus !== $mappedStatus) {
            $b['status'] = $mappedStatus;
        }
        $updated[] = $b;
        $touched = true;
    }

    if ($touched) {
        saveJsonSafe($bookingsFile, $bookings);
    }

    if ($touched && strtolower((string)$paymentStatus) === 'paid') {
        bookingConfirmationTrySendCartAfterSync($bookingsFile, $bookings, $cartId);
    }

    return $updated;
}

/**
 * After syncing paid status without a Mollie webhook (localhost mock), send confirmation once.
 * Production webhooks set confirmationEmailSent; this path is idempotent.
 */
function bookingConfirmationTrySendAfterSync(string $bookingsFile, array &$bookings, string $bookingId): void {
    if (!function_exists('curl_init')) {
        return;
    }
    require_once __DIR__ . '/components/booking_confirmation_email.php';
    foreach ($bookings as &$b) {
        if (($b['id'] ?? '') !== $bookingId) {
            continue;
        }
        $ps = strtolower((string)($b['paymentStatus'] ?? ''));
        $st = $b['status'] ?? '';
        if ($ps !== 'paid' || !in_array($st, ['paid', 'confirmed'], true)) {
            return;
        }
        if (!empty($b['confirmationEmailSent'])) {
            return;
        }
        $sent = sendBookingConfirmationEmail($b, true);
        $b['confirmationEmailSent'] = $sent;
        if ($sent) {
            $b['confirmationEmailSentAt'] = date('c');
        }
        saveJsonSafe($bookingsFile, $bookings);
        return;
    }
    unset($b);
}

/**
 * Same as bookingConfirmationTrySendAfterSync for multi-boat cart (one email for the first row, like the webhook).
 */
function bookingConfirmationTrySendCartAfterSync(string $bookingsFile, array &$bookings, string $cartId): void {
    if (!function_exists('curl_init')) {
        return;
    }
    require_once __DIR__ . '/components/booking_confirmation_email.php';
    $firstIdx = null;
    foreach ($bookings as $idx => $b) {
        if (($b['cartId'] ?? '') !== $cartId) {
            continue;
        }
        $ps = strtolower((string)($b['paymentStatus'] ?? ''));
        $st = $b['status'] ?? '';
        if ($ps !== 'paid' || !in_array($st, ['paid', 'confirmed'], true)) {
            return;
        }
        if ($firstIdx === null) {
            $firstIdx = $idx;
        }
    }
    if ($firstIdx === null || !empty($bookings[$firstIdx]['confirmationEmailSent'])) {
        return;
    }
    $sent = sendBookingConfirmationEmail($bookings[$firstIdx], true);
    $bookings[$firstIdx]['confirmationEmailSent'] = $sent;
    if ($sent) {
        $bookings[$firstIdx]['confirmationEmailSentAt'] = date('c');
    }
    saveJsonSafe($bookingsFile, $bookings);
}

/**
 * When true, checkout skips Mollie (including pay-on-arrival reservation) and getCartStatus/getBookingStatus
 * treat mock_* payments as paid. Requires MOCK_MOLLIE_PAYMENTS=1 and a non-production context:
 * development host (see isDevelopment()) or APP_ENV one of development|dev|local.
 */
function mockMolliePaymentsEnabled(): bool {
    $flag = getenv('MOCK_MOLLIE_PAYMENTS');
    if ($flag === false || $flag === '') {
        return false;
    }
    $flag = strtolower(trim((string)$flag));
    if (!in_array($flag, ['1', 'true', 'yes', 'on'], true)) {
        return false;
    }
    $appEnv = strtolower(trim((string)(getenv('APP_ENV') ?: '')));
    $envAllowsMock = in_array($appEnv, ['development', 'dev', 'local'], true);
    return (function_exists('isDevelopment') && isDevelopment()) || $envAllowsMock;
}

/** Synthetic Mollie-style payment payload for mock / local testing. */
function mockMolliePaymentPayload(string $paymentId, float $totalEuro): array {
    return [
        'id' => $paymentId,
        'status' => 'paid',
        'amount' => [
            'currency' => 'EUR',
            'value' => number_format($totalEuro, 2, '.', ''),
        ],
    ];
}

/**
 * @return array{subtotal: float, administrationFee: float, grandTotal: float}
 */
function bookingAdministrativeFeeBreakdown(float $rentalSubtotal): array {
    $pct = defined('BOOKING_ADMIN_FEE_PERCENT') ? (float) BOOKING_ADMIN_FEE_PERCENT : 0.0;
    $fee = round($rentalSubtotal * ($pct / 100.0), 2);
    $grand = round($rentalSubtotal + $fee, 2);
    return [
        'subtotal' => round($rentalSubtotal, 2),
        'administrationFee' => $fee,
        'grandTotal' => $grand,
    ];
}

/**
 * Pay-on-arrival deposit: reservation % of rental plus administration fee on that slice only (not on full rental).
 *
 * @return array{rentalPortion: float, administrationOnReservation: float, total: float}
 */
function payOnArrivalReservationFeeBreakdown(float $rentalSubtotal): array {
    $resPct = defined('BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT')
        ? (float) BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT
        : 10.0;
    $adminPct = defined('BOOKING_ADMIN_FEE_PERCENT') ? (float) BOOKING_ADMIN_FEE_PERCENT : 0.0;
    $r = max(0.0, $rentalSubtotal);
    $rentalPortion = round($r * ($resPct / 100.0), 2);
    $administrationOnReservation = round($rentalPortion * ($adminPct / 100.0), 2);
    $total = round($rentalPortion + $administrationOnReservation, 2);
    return [
        'rentalPortion' => $rentalPortion,
        'administrationOnReservation' => $administrationOnReservation,
        'total' => $total,
    ];
}

function payOnArrivalReservationFeeTotal(float $rentalSubtotal): float {
    return payOnArrivalReservationFeeBreakdown($rentalSubtotal)['total'];
}

/**
 * Per cart line: deposit from that line's rental + admin on that slice. Rounding adjusted on last line vs cart total.
 *
 * @param array<int, array<string, mixed>> $validatedItems prices must still be rental-only totals per line
 * @return array<int, array<string, mixed>>
 */
function allocatePayOnArrivalReservationAcrossCartLines(array $validatedItems, float $rentalSubtotal): array {
    $n = count($validatedItems);
    if ($n === 0) {
        return $validatedItems;
    }
    $target = payOnArrivalReservationFeeBreakdown($rentalSubtotal)['total'];
    $breakdowns = [];
    $sum = 0.0;
    foreach ($validatedItems as $item) {
        $rental = round((float)($item['price'] ?? 0), 2);
        $bd = payOnArrivalReservationFeeBreakdown($rental);
        $breakdowns[] = $bd;
        $sum += $bd['total'];
    }
    $diff = round($target - $sum, 2);
    if (abs($diff) >= 0.01 && $n > 0) {
        $last = &$breakdowns[$n - 1];
        $last['total'] = round($last['total'] + $diff, 2);
        $last['administrationOnReservation'] = round($last['total'] - $last['rentalPortion'], 2);
        unset($last);
    }
    $out = [];
    foreach ($validatedItems as $i => $item) {
        $b = $breakdowns[$i];
        $item['lineReservationFee'] = $b['total'];
        $item['lineReservationRentalPortion'] = $b['rentalPortion'];
        $item['lineReservationAdminOnSlice'] = $b['administrationOnReservation'];
        $out[] = $item;
    }
    return $out;
}

/**
 * Split administration fee across cart lines; sums of line totals equal grandTotal.
 * Sets rentalPrice, lineAdministrationFee, and replaces price with line total (rental + allocated fee).
 *
 * @param array<int, array<string, mixed>> $validatedItems
 * @return array<int, array<string, mixed>>
 */
function allocateAdministrativeFeeAcrossCartLines(array $validatedItems, float $rentalSubtotal, float $feeTotal, float $grandTotal): array {
    $n = count($validatedItems);
    if ($n === 0) {
        return $validatedItems;
    }
    $allocated = [];
    $feeSoFar = 0.0;
    $totalSoFar = 0.0;
    foreach ($validatedItems as $i => $item) {
        $rental = round((float)($item['price'] ?? 0), 2);
        $isLast = ($i === $n - 1);
        if ($isLast) {
            $lineFee = round($feeTotal - $feeSoFar, 2);
            $lineTotal = round($grandTotal - $totalSoFar, 2);
        } else {
            $share = $rentalSubtotal > 0 ? ($rental / $rentalSubtotal) : (1.0 / $n);
            $lineFee = round($feeTotal * $share, 2);
            $lineTotal = round($rental + $lineFee, 2);
            $feeSoFar += $lineFee;
            $totalSoFar += $lineTotal;
        }
        $item['rentalPrice'] = $rental;
        $item['lineAdministrationFee'] = $lineFee;
        $item['price'] = $lineTotal;
        $allocated[] = $item;
    }
    return $allocated;
}

/**
 * Build expanded cart booking rows (one per boat). Does not persist.
 *
 * @param array $validatedItems cart lines from createCartPayment validation
 * @param string $status initial booking status (e.g. pending)
 */
function buildCartCheckoutBookingRows(
    string $cartId,
    string $paymentId,
    array $validatedItems,
    array $input,
    string $customerName,
    string $customerEmail,
    string $customerPhone,
    string $arrivalTime,
    string $cityOfOrigin,
    string $status
): array {
    $rows = [];
    $bookingCounter = 0;
    foreach ($validatedItems as $item) {
        $itemQuantity = $item['quantity'] ?? 1;
        $lineTotal = (float)($item['price'] ?? 0);
        $lineRental = isset($item['rentalPrice']) ? (float)$item['rentalPrice'] : $lineTotal;
        $lineFee = isset($item['lineAdministrationFee']) ? (float)$item['lineAdministrationFee'] : 0.0;
        $lineRes = isset($item['lineReservationFee']) ? (float)$item['lineReservationFee'] : 0.0;
        $lineResRp = isset($item['lineReservationRentalPortion']) ? (float)$item['lineReservationRentalPortion'] : 0.0;
        $lineResAd = isset($item['lineReservationAdminOnSlice']) ? (float)$item['lineReservationAdminOnSlice'] : 0.0;
        $pricePerBoat = $lineTotal / $itemQuantity;
        $rentalPerBoat = $lineRental / $itemQuantity;
        $feePerBoat = $lineFee / $itemQuantity;
        $resFeePerBoat = $lineRes / $itemQuantity;
        $resRpPerBoat = $lineResRp / $itemQuantity;
        $resAdPerBoat = $lineResAd / $itemQuantity;
        $tripPerBoat = round($pricePerBoat, 2);
        $adminStoredPerBoat = round($feePerBoat, 2);
        if ($lineRes > 0) {
            $tripPerBoat = round($rentalPerBoat + $resAdPerBoat, 2);
            $adminStoredPerBoat = round($resAdPerBoat, 2);
        }
        for ($q = 0; $q < $itemQuantity; $q++) {
            $bookingId = $cartId . '_' . $bookingCounter;
            $row = [
                'id' => $bookingId,
                'cartId' => $cartId,
                'date' => $item['startDate'],
                'endDate' => $item['endDate'],
                'boatType' => $item['boatId'],
                'boatName' => $item['boatName'] ?? $item['boatId'],
                'numberOfDays' => $item['days'],
                'quantity' => 1,
                'engineOption' => (!empty($item['useMotor']) && ($item['boatId'] ?? '') === 'sailboat-4-5') ? 'with' : 'without',
                'customerName' => $customerName,
                'customerEmail' => $customerEmail,
                'customerPhone' => $customerPhone,
                'customerAddress' => sanitizeText($input['customerAddress'] ?? ''),
                'arrivalTime' => $arrivalTime,
                'cityOfOrigin' => $cityOfOrigin,
                'notes' => sanitizeText($input['notes'] ?? 'Cart checkout'),
                'status' => $status,
                'amount' => $tripPerBoat,
                'source' => 'cart',
                'createdAt' => date('c'),
                'updatedAt' => date('c'),
            ];
            if ($paymentId !== '') {
                $row['paymentId'] = $paymentId;
            }
            if ($lineFee > 0 || isset($item['rentalPrice'])) {
                $row['rentalAmount'] = round($rentalPerBoat, 2);
                $row['administrationFee'] = $adminStoredPerBoat;
            }
            if ($lineRes > 0) {
                $rPart = round($resFeePerBoat, 2);
                $row['reservationFee'] = $rPart;
                $row['payOnArrivalReservation'] = true;
                $row['balanceDueOnArrival'] = round($tripPerBoat - $rPart, 2);
                $row['reservationFeeRentalPortion'] = round($resRpPerBoat, 2);
                $row['reservationFeeAdminOnReservation'] = round($resAdPerBoat, 2);
                if (defined('BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT')) {
                    $row['reservationFeePercent'] = (float) BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT;
                }
            }
            $rows[] = $row;
            $bookingCounter++;
        }
    }
    return $rows;
}

// Wrapper for shared pricing logic
function getPrice($boatType, $numberOfDays, $useMotor = false) {
    global $boatsFile;
    $boats = loadJsonSafe($boatsFile);
    return calculateBoatPrice($boatType, $numberOfDays, $boats, $useMotor);
}

function isBoatAvailableForRange($boatId, $startDate, $endDate, $boats, $bookings) {
    $boat = null;
    foreach ($boats as $b) {
        if (($b['id'] ?? '') === $boatId) {
            $boat = $b;
            break;
        }
    }

    if (!$boat) {
        return [false, 'boat_not_found'];
    }

    $totalBoats = $boat['total'] ?? 1;
    $dateToCheck = new DateTime($startDate);
    $endObj = new DateTime($endDate);

    while ($dateToCheck <= $endObj) {
        $dateStr = $dateToCheck->format('Y-m-d');
        $bookedCount = 0;

        foreach ($bookings as $booking) {
            $status = $booking['status'] ?? '';

            // IMPORTANT: Non-blocking statuses must NOT consume inventory.
            // Per instructed logic (admin + frontend availability), only "active/paid" bookings block.
            // So these statuses are treated as non-blocking:
            // - cancelled/canceled/payment-rejected/failed/expired/rejected
            // - open/pending/not-confirmed (incomplete/abandoned payments)
            // - temporary (holds)
            if (in_array($status, [
                'canceled', 'cancelled', 'payment-rejected', 'failed', 'expired', 'rejected',
                'open', 'pending', 'not-confirmed',
                'temporary'
            ], true)) {
                continue;
            }
            if (($booking['boatType'] ?? '') !== $boatId) {
                continue;
            }

            $bookingStart = $booking['date'] ?? null;
            $bookingEnd = $booking['endDate'] ?? $bookingStart;
            if (!$bookingStart) {
                continue;
            }

            if ($dateStr >= $bookingStart && $dateStr <= $bookingEnd) {
                $bookedCount++;
            }
        }

        if ($bookedCount >= $totalBoats) {
            return [false, $dateStr];
        }

        $dateToCheck->modify('+1 day');
    }

    return [true, null];
}


function forwardRequest($url, $method = 'GET', $payload = null, $mollieApiKey = '') {
    $headers = [
        'Authorization: Bearer ' . $mollieApiKey,
        'Content-Type: application/json'
    ];
    
    // Try curl first, fall back to file_get_contents
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload ? json_encode($payload) : '{}');
        }
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$httpCode, $response];
    } else {
        // Fallback to file_get_contents (for environments without curl)
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers) . "\r\n",
                'content' => ($method === 'POST' && $payload) ? json_encode($payload) : '',
                'ignore_errors' => true,
                'timeout' => 15
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        // Parse HTTP response code from headers
        $httpCode = 500;
        if (isset($http_response_header) && is_array($http_response_header)) {
            foreach ($http_response_header as $header) {
                if (preg_match('/^HTTP\/\d+\.\d+\s+(\d+)/', $header, $matches)) {
                    $httpCode = (int)$matches[1];
                    break;
                }
            }
        }
        
        return [$httpCode, $response ?: ''];
    }
}

/**
 * Validate paymentMethod from JSON input against getCheckoutPaymentMethods().
 *
 * @return array{0: bool, 1: string} [ok, method id or error code: payment_method_required|payment_method_invalid]
 */
function validatePaymentMethodInput(array $input): array {
    $allowed = function_exists('getCheckoutPaymentMethods') ? getCheckoutPaymentMethods() : ['ideal', 'bancontact', 'pay_on_arrival'];
    $raw = $input['paymentMethod'] ?? null;
    if ($raw === null || $raw === '') {
        return [false, 'payment_method_required'];
    }
    $methodId = strtolower(trim((string) $raw));
    if (!in_array($methodId, $allowed, true)) {
        return [false, 'payment_method_invalid'];
    }
    return [true, $methodId];
}

function isPayOnArrivalMethod(string $id): bool {
    $const = defined('CHECKOUT_PAY_ON_ARRIVAL_METHOD') ? CHECKOUT_PAY_ON_ARRIVAL_METHOD : 'pay_on_arrival';
    return $id === $const;
}

/** Latest allowed arrival time for pay-on-arrival: 11:00 (inclusive). */
function arrivalTimeAllowsPayOnArrival(string $timeRaw): bool {
    $timeRaw = trim($timeRaw);
    if ($timeRaw === '') {
        return false;
    }
    if (!preg_match('/^(\d{1,2}):(\d{2})/', $timeRaw, $m)) {
        return false;
    }
    $h = (int) $m[1];
    $min = (int) $m[2];
    $minutes = $h * 60 + $min;
    return $minutes <= 11 * 60;
}

try {
    if ($method === 'POST' && $action === 'createPayment') {
        $inputRaw = file_get_contents('php://input');
        $input = validateJsonInput($inputRaw);
        
        if ($input === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
            exit;
        }

        [$pmOk, $pmResult] = validatePaymentMethodInput($input);
        if (!$pmOk) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $pmResult]);
            exit;
        }
        $chosenPaymentMethod = $pmResult;
        
        // Calculate price on server for security
        $boatType = $input['boatType'] ?? '';
        $days = (int)($input['numberOfDays'] ?? 1);
        $useMotor = !empty($input['useMotor']) || !empty($input['withEngine']) || (($input['engineOption'] ?? 'without') === 'with');
        if ($boatType !== 'sailboat-4-5') {
            $useMotor = false;
        }
        $price = getPrice($boatType, $days, $useMotor);
        
        if ($price <= 0) throw new Exception('Invalid boat or price');

        $startDate = $input['date'] ?? '';
        if (empty($startDate)) {
            throw new Exception('Date is required');
        }

        $endDateObj = new DateTime($startDate);
        $endDateObj->modify('+' . max(0, $days - 1) . ' day');
        $endDate = $endDateObj->format('Y-m-d');

        // Availability check to prevent overbooking
        $boats = loadJsonSafe($boatsFile);
        $bookings = loadJsonSafe($bookingsFile);
        [$isAvailable, $blockedDate] = isBoatAvailableForRange($boatType, $startDate, $endDate, $boats, $bookings);
        if (!$isAvailable) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => 'Selected boat is not available for the chosen date(s).',
                'blockedDate' => $blockedDate
            ]);
            exit;
        }
        
        // Sanitize input data before creating booking
        $sanitizedInput = sanitizeBookingData($input);
        
        // Prepare booking data (amount charged includes administrative fee)
        $bookingId = 'tr_' . time() . '_' . bin2hex(random_bytes(4));
        $feeBreakdownSingle = bookingAdministrativeFeeBreakdown((float)$price);
        $grandTotalSingle = $feeBreakdownSingle['grandTotal'];
        $feePercentStrSingle = (string)(defined('BOOKING_ADMIN_FEE_PERCENT') ? BOOKING_ADMIN_FEE_PERCENT : 0.0);
        $newBooking = [
            'id' => $bookingId,
            'date' => $startDate,
            'endDate' => $endDate,
            'boatType' => $sanitizedInput['boatType'] ?? $boatType,
            'numberOfDays' => $days,
            'customerName' => $sanitizedInput['customerName'] ?? '',
            'customerEmail' => $sanitizedInput['customerEmail'] ?? '',
            'customerPhone' => $sanitizedInput['customerPhone'] ?? '',
            'notes' => $sanitizedInput['notes'] ?? '',
            'status' => 'pending',
            'amount' => $grandTotalSingle,
            'rentalAmount' => $feeBreakdownSingle['subtotal'],
            'administrationFee' => $feeBreakdownSingle['administrationFee'],
            'paymentMethod' => $chosenPaymentMethod,
            'source' => 'online',
            'createdAt' => date('c'),
            'updatedAt' => date('c')
        ];
        
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $isLocalhost = strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false;
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';

        if (isPayOnArrivalMethod($chosenPaymentMethod)) {
            $arrivalTimeSingle = sanitizeText($input['arrivalTime'] ?? '');
            $citySingle = sanitizeText($input['cityOfOrigin'] ?? '');
            if ($arrivalTimeSingle === '' || $citySingle === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'customer_details_required']);
                exit;
            }
            if (!arrivalTimeAllowsPayOnArrival($arrivalTimeSingle)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'pay_on_arrival_arrival_invalid']);
                exit;
            }
            $poaResBreakSingle = payOnArrivalReservationFeeBreakdown($feeBreakdownSingle['subtotal']);
            $reservationFeeSingle = $poaResBreakSingle['total'];
            $tripTotalPoaSingle = round(
                $feeBreakdownSingle['subtotal'] + $poaResBreakSingle['administrationOnReservation'],
                2
            );
            $balanceDueSingle = round($tripTotalPoaSingle - $reservationFeeSingle, 2);
            $newBooking['amount'] = $tripTotalPoaSingle;
            $newBooking['administrationFee'] = $poaResBreakSingle['administrationOnReservation'];
            $resPctStrSingle = (string)(defined('BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT')
                ? BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT
                : 10.0);

            $newBooking['arrivalTime'] = $arrivalTimeSingle;
            $newBooking['cityOfOrigin'] = $citySingle;
            $newBooking['customerAddress'] = sanitizeText($input['customerAddress'] ?? '');
            $newBooking['paymentMethod'] = CHECKOUT_PAY_ON_ARRIVAL_METHOD;
            $newBooking['reservationFee'] = $reservationFeeSingle;
            $newBooking['reservationFeeRentalPortion'] = $poaResBreakSingle['rentalPortion'];
            $newBooking['reservationFeeAdminOnReservation'] = $poaResBreakSingle['administrationOnReservation'];
            $newBooking['balanceDueOnArrival'] = $balanceDueSingle;
            $newBooking['payOnArrivalReservation'] = true;
            $newBooking['reservationFeePercent'] = (float) $resPctStrSingle;

            $redirectBooking = $protocol . '://' . $host . '/pages/payment-success.php?bookingId=' . rawurlencode($bookingId);

            if (mockMolliePaymentsEnabled()) {
                $mockPid = 'mock_' . bin2hex(random_bytes(12));
                $newBooking['paymentId'] = $mockPid;
                $bookingsSave = loadJsonSafe($bookingsFile);
                $bookingsSave[] = $newBooking;
                saveJsonSafe($bookingsFile, $bookingsSave);
                echo json_encode([
                    'id' => $mockPid,
                    'status' => 'open',
                    'amount' => ['currency' => 'EUR', 'value' => number_format($reservationFeeSingle, 2, '.', '')],
                    '_links' => [
                        'checkout' => ['href' => $redirectBooking],
                    ],
                    'metadata' => [
                        'bookingId' => $bookingId,
                        'paymentKind' => 'pay_on_arrival_reservation',
                    ],
                ]);
                exit;
            }

            $molliePoaPayload = [
                'amount' => [
                    'currency' => 'EUR',
                    'value' => number_format($reservationFeeSingle, 2, '.', ''),
                ],
                'description' => 'Reserveringsbijdrage (niet-restitueerbaar) – Nijenhuis boothuur',
                'redirectUrl' => $redirectBooking,
                'metadata' => [
                    'bookingId' => $bookingId,
                    'paymentKind' => 'pay_on_arrival_reservation',
                    'payOnArrival' => '1',
                    'reservationFee' => number_format($reservationFeeSingle, 2, '.', ''),
                    'reservationFeeRentalPortion' => number_format($poaResBreakSingle['rentalPortion'], 2, '.', ''),
                    'reservationFeeAdminOnReservation' => number_format($poaResBreakSingle['administrationOnReservation'], 2, '.', ''),
                    'balanceDueOnArrival' => number_format($balanceDueSingle, 2, '.', ''),
                    'grandTotal' => number_format($tripTotalPoaSingle, 2, '.', ''),
                    'rentalSubtotal' => number_format($feeBreakdownSingle['subtotal'], 2, '.', ''),
                    'administrationFee' => number_format($feeBreakdownSingle['administrationFee'], 2, '.', ''),
                    'administrationFeePercent' => $feePercentStrSingle,
                    'reservationFeePercent' => $resPctStrSingle,
                ],
            ];
            if (!$isLocalhost) {
                $molliePoaPayload['webhookUrl'] = $protocol . '://' . $host . '/webhook/mollie';
            }

            [$poaCode, $poaResp] = forwardRequest($baseUrl . '/payments', 'POST', $molliePoaPayload, $mollieApiKey);
            $poaResult = json_decode($poaResp, true);
            if ($poaCode === 201 && isset($poaResult['id'])) {
                $newBooking['paymentId'] = $poaResult['id'];
                $bookingsSave = loadJsonSafe($bookingsFile);
                $bookingsSave[] = $newBooking;
                saveJsonSafe($bookingsFile, $bookingsSave);
                echo $poaResp;
            } else {
                http_response_code($poaCode ?: 500);
                echo $poaResp ?: json_encode(['success' => false, 'message' => 'Failed to create reservation payment']);
            }
            exit;
        }
        
        // Create Mollie Payment
        // Skip webhookUrl for localhost (Mollie can't reach it)
        $molliePayload = [
            'amount' => [
                'currency' => 'EUR',
                'value' => number_format($grandTotalSingle, 2, '.', '')
            ],
            'description' => 'Boat rental: ' . $boatType . ' for ' . $days . ' day(s)',
            'method' => $chosenPaymentMethod,
            'redirectUrl' => $protocol . '://' . $host . '/pages/payment-success.php?bookingId=' . $bookingId,
            'metadata' => [
                'bookingId' => $bookingId,
                'chosenMethod' => $chosenPaymentMethod,
                'rentalSubtotal' => number_format($feeBreakdownSingle['subtotal'], 2, '.', ''),
                'administrationFee' => number_format($feeBreakdownSingle['administrationFee'], 2, '.', ''),
                'administrationFeePercent' => $feePercentStrSingle,
                'grandTotal' => number_format($grandTotalSingle, 2, '.', ''),
            ]
        ];
        
        // Only add webhookUrl for production (Mollie rejects localhost webhooks)
        if (!$isLocalhost) {
            // Canonical webhook endpoint (works with Python proxy or PHP fallback in /webhook/mollie)
            $molliePayload['webhookUrl'] = $protocol . '://' . $host . '/webhook/mollie';
        }
        
        [$code, $resp] = forwardRequest($baseUrl . '/payments', 'POST', $molliePayload, $mollieApiKey);
        $mollieResult = json_decode($resp, true);
        
        if ($code === 201 && isset($mollieResult['id'])) {
            $newBooking['paymentId'] = $mollieResult['id'];
            
            // Save to bookings.json safely
            $bookings = loadJsonSafe($bookingsFile);
            $bookings[] = $newBooking;
            saveJsonSafe($bookingsFile, $bookings);
            
            echo $resp;
        } else {
            http_response_code($code ?: 500);
            echo $resp ?: json_encode(['success' => false, 'message' => 'Failed to create Mollie payment']);
        }
        exit;
    }
    
    if ($method === 'GET' && $action === 'getBookingStatus') {
        // SECURITY: Sanitize GET parameter
        $bookingId = sanitizeText($_GET['bookingId'] ?? '');
        if ($bookingId === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'bookingId is required']);
            exit;
        }
        
        $bookings = loadJsonSafe($bookingsFile);
        
        $booking = null;
        foreach ($bookings as $b) {
            if ($b['id'] === $bookingId) {
                $booking = $b;
                break;
            }
        }
        
        if (!$booking) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            exit;
        }

        // Legacy: pay-on-arrival before reservation fee (no Mollie payment on file)
        $legacyPayOnArrival = isPayOnArrivalMethod((string)($booking['paymentMethod'] ?? ''))
            && ($booking['paymentId'] ?? '') === ''
            && empty($booking['payOnArrivalReservation']);
        if ($legacyPayOnArrival) {
            echo json_encode([
                'success' => true,
                'booking' => publicBookingView($booking),
                'payment' => null,
                'payOnArrival' => true,
            ]);
            exit;
        }

        if (!isset($booking['paymentId']) || $booking['paymentId'] === '') {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Booking not found or no payment associated']);
            exit;
        }

        $pid = (string)($booking['paymentId'] ?? '');
        if (str_starts_with($pid, 'mock_') && mockMolliePaymentsEnabled()) {
            $mockAmt = !empty($booking['payOnArrivalReservation'])
                ? (float)($booking['reservationFee'] ?? 0)
                : (float)($booking['amount'] ?? 0);
            $paymentData = mockMolliePaymentPayload($pid, $mockAmt);
            $synced = syncSingleBookingPaymentStatus($bookingId, 'paid');
            if ($synced) {
                $booking = $synced;
            } else {
                $booking['paymentStatus'] = 'paid';
            }
            echo json_encode(['success' => true, 'booking' => publicBookingView($booking), 'payment' => $paymentData]);
            exit;
        }

        if ($mollieApiKey === '') {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Mollie API key not configured.']);
            exit;
        }
        
        [$code, $resp] = forwardRequest($baseUrl . '/payments/' . rawurlencode($booking['paymentId']), 'GET', null, $mollieApiKey);
        http_response_code($code);
        
        // Merge booking data with payment status for convenience
        $paymentData = json_decode($resp, true);
        if (isset($paymentData['status'])) {
            $paymentStatus = $paymentData['status'];

            // Ensure the booking in bookings.json is updated immediately (webhook fallback).
            $synced = syncSingleBookingPaymentStatus($bookingId, $paymentStatus);
            if ($synced) {
                $booking = $synced;
            } else {
                // At minimum return the payment status for UI
                $booking['paymentStatus'] = $paymentStatus;
            }

            echo json_encode(['success' => true, 'booking' => publicBookingView($booking), 'payment' => $paymentData]);
        } else {
            echo $resp;
        }
        exit;
    }

    if ($method === 'GET' && $action === 'getCartStatus') {
        $cartId = $_GET['cartId'] ?? '';
        if ($cartId === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'cartId is required']);
            exit;
        }

        $bookings = loadJsonSafe($bookingsFile);
        $cartBookings = array_values(array_filter($bookings, function($b) use ($cartId) {
            return ($b['cartId'] ?? '') === $cartId;
        }));

        if (empty($cartBookings)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Cart not found']);
            exit;
        }

        $paymentId = $cartBookings[0]['paymentId'] ?? '';
        $cartLegacyPoa = isPayOnArrivalMethod((string)($cartBookings[0]['paymentMethod'] ?? ''))
            && $paymentId === ''
            && empty($cartBookings[0]['payOnArrivalReservation']);
        if ($cartLegacyPoa) {
            echo json_encode([
                'success' => true,
                'bookings' => array_map('publicBookingView', $cartBookings),
                'payment' => null,
                'payOnArrival' => true,
            ]);
            exit;
        }

        if (empty($paymentId)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'No payment associated with cart']);
            exit;
        }

        if (str_starts_with((string)$paymentId, 'mock_') && mockMolliePaymentsEnabled()) {
            $sum = 0;
            foreach ($cartBookings as $b) {
                if (!empty($b['payOnArrivalReservation'])) {
                    $sum += (float)($b['reservationFee'] ?? 0);
                } else {
                    $sum += (float)($b['amount'] ?? 0);
                }
            }
            $paymentData = mockMolliePaymentPayload((string)$paymentId, $sum);
            $syncedBookings = syncCartBookingsPaymentStatus($cartId, 'paid');
            if (!empty($syncedBookings)) {
                $cartBookings = $syncedBookings;
            } else {
                $bookings = loadJsonSafe($bookingsFile);
                $cartBookings = array_values(array_filter($bookings, function ($b) use ($cartId) {
                    return ($b['cartId'] ?? '') === $cartId;
                }));
            }
            echo json_encode([
                'success' => true,
                'bookings' => array_map('publicBookingView', $cartBookings),
                'payment' => $paymentData,
            ]);
            exit;
        }

        if ($mollieApiKey === '') {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Mollie API key not configured.']);
            exit;
        }

        [$code, $resp] = forwardRequest($baseUrl . '/payments/' . rawurlencode($paymentId), 'GET', null, $mollieApiKey);
        http_response_code($code);

        $paymentData = json_decode($resp, true);
        if (isset($paymentData['status'])) {
            $paymentStatus = $paymentData['status'];

            // Webhook fallback: sync all cart bookings immediately
            $syncedBookings = syncCartBookingsPaymentStatus($cartId, $paymentStatus);
            if (!empty($syncedBookings)) {
                $cartBookings = $syncedBookings;
            }
            $cartBookings = array_map('publicBookingView', $cartBookings);
            echo json_encode([
                'success' => true,
                'bookings' => $cartBookings,
                'payment' => $paymentData
            ]);
        } else {
            echo $resp;
        }
        exit;
    }

    if ($method === 'GET' && $action === 'getPaymentStatus') {
        // SECURITY: The payment id alone is enough to proxy arbitrary Mollie
        // records, so require the caller to prove they know a matching
        // booking/cart id from our own database before we forward to Mollie.
        $paymentId = sanitizeText($_GET['paymentId'] ?? '');
        $bookingId = sanitizeText($_GET['bookingId'] ?? '');
        $cartId = sanitizeText($_GET['cartId'] ?? '');
        if ($paymentId === '' || ($bookingId === '' && $cartId === '')) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'paymentId and bookingId/cartId are required']);
            exit;
        }

        $bookings = loadJsonSafe($bookingsFile);
        $matched = false;
        foreach ($bookings as $b) {
            if (($b['paymentId'] ?? '') !== $paymentId) continue;
            if ($bookingId !== '' && ($b['id'] ?? '') === $bookingId) { $matched = true; break; }
            if ($cartId !== '' && ($b['cartId'] ?? '') === $cartId) { $matched = true; break; }
        }
        if (!$matched) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Payment not found for given booking']);
            exit;
        }

        // Handle mock payments locally to avoid a network hop.
        if (str_starts_with($paymentId, 'mock_') && mockMolliePaymentsEnabled()) {
            echo json_encode(mockMolliePaymentPayload($paymentId, 0.0));
            exit;
        }

        [$code, $resp] = forwardRequest($baseUrl . '/payments/' . rawurlencode($paymentId), 'GET', null, $mollieApiKey);
        http_response_code($code);
        echo $resp ?: json_encode(['success' => false, 'message' => 'Failed to fetch payment status']);
        exit;
    }

    // ===== CART CHECKOUT - Multiple Items =====
    if ($method === 'POST' && $action === 'createCartPayment') {
        $inputRaw = file_get_contents('php://input');
        $input = validateJsonInput($inputRaw);
        
        if ($input === null || !isset($input['items']) || !is_array($input['items']) || count($input['items']) === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid cart data']);
            exit;
        }
        
        // SECURITY: Limit cart size to prevent abuse
        $maxCartSize = 10;
        if (count($input['items']) > $maxCartSize) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Cart too large. Maximum $maxCartSize items allowed."]);
            exit;
        }
        
        // Sanitize customer data
        $customerData = sanitizeBookingData([
            'customerName' => $input['customerName'] ?? '',
            'customerEmail' => $input['customerEmail'] ?? '',
            'customerPhone' => $input['customerPhone'] ?? '',
            'arrivalTime' => $input['arrivalTime'] ?? '',
            'cityOfOrigin' => $input['cityOfOrigin'] ?? ''
        ]);
        
        $customerName = $customerData['customerName'];
        $customerEmail = $customerData['customerEmail'];
        $customerPhone = $customerData['customerPhone'];
        $arrivalTime = sanitizeText($input['arrivalTime'] ?? '');
        $cityOfOrigin = sanitizeText($input['cityOfOrigin'] ?? '');
        
        if (empty($customerName) || empty($customerEmail) || empty($customerPhone) || empty($arrivalTime) || empty($cityOfOrigin)) {
            throw new Exception('Customer details required (including arrival time and city of origin)');
        }

        [$pmOk, $pmResult] = validatePaymentMethodInput($input);
        if (!$pmOk) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $pmResult]);
            exit;
        }
        $chosenPaymentMethod = $pmResult;

        if (isPayOnArrivalMethod($chosenPaymentMethod) && !arrivalTimeAllowsPayOnArrival($arrivalTime)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'pay_on_arrival_arrival_invalid']);
            exit;
        }
        
        // Load boats and bookings for availability check
        $boats = loadJsonSafe($boatsFile);
        $bookings = loadJsonSafe($bookingsFile);
        
        // Calculate total on server (validate client prices)
        $serverTotal = 0;
        $validatedItems = [];
        $unavailableItems = [];
        
        foreach ($input['items'] as $item) {
            // SECURITY: Validate cart item structure
            if (!isset($item['boatId'], $item['startDate'])) {
                throw new Exception('Invalid cart item structure');
            }
            
            // SECURITY: Sanitize item data
            $boatId = sanitizeText($item['boatId'] ?? '');
            $startDate = sanitizeText($item['startDate'] ?? '');
            $endDate = sanitizeText($item['endDate'] ?? $startDate);
            
            // Validate dates
            $start = DateTime::createFromFormat('Y-m-d', $startDate);
            $end = DateTime::createFromFormat('Y-m-d', $endDate);
            if (!$start || !$end) {
                throw new Exception('Invalid date format in cart item');
            }
            
            // Calculate days
            $days = $start->diff($end)->days + 1;
            
            // Get quantity (default to 1 for backward compatibility)
            $quantity = isset($item['quantity']) ? max(1, intval($item['quantity'])) : 1;
            
            // Determine if engine/motor option is selected (only Zeilboot sailboat-4-5)
            $itemUseMotor = !empty($item['useMotor']) || (($item['engineOption'] ?? 'without') === 'with');
            if ($boatId !== 'sailboat-4-5') {
                $itemUseMotor = false;
            }
            
            // Calculate server-side price per boat (with engine option)
            $pricePerBoat = getPrice($boatId, $days, $itemUseMotor);
            
            if ($pricePerBoat <= 0) {
                throw new Exception("Invalid boat or price for: $boatId");
            }
            
            // Calculate total price for all boats
            $serverPrice = $pricePerBoat * $quantity;
            
            // Check availability - ensure boat wasn't booked in the meantime
            $boat = null;
            foreach ($boats as $b) {
                if ($b['id'] === $boatId) {
                    $boat = $b;
                    break;
                }
            }
            
            if (!$boat) {
                throw new Exception("Boat not found: $boatId");
            }
            
            $totalBoats = $boat['total'] ?? 1;
            
            // Check if requested quantity exceeds total available
            if ($quantity > $totalBoats) {
                $unavailableItems[] = [
                    'boatName' => $item['boatName'] ?? $boatId,
                    'boatId' => $boatId,
                    'blockedDate' => $startDate,
                    'reason' => 'insufficient_quantity',
                    'requested' => $quantity,
                    'available' => $totalBoats
                ];
                continue; // Skip adding to validated items
            }
            
            // Count existing bookings for this boat on these dates (excluding temporary from same cart session)
            $cartItemId = $item['id'] ?? null;
            $dateToCheck = new DateTime($startDate);
            $endObj = new DateTime($endDate);
            $isAvailable = true;
            $blockedDate = null;
            $maxBookedCount = 0;
            
            while ($dateToCheck <= $endObj) {
                $dateStr = $dateToCheck->format('Y-m-d');
                $bookedCount = 0;
                
                foreach ($bookings as $booking) {
                    $status = $booking['status'] ?? '';
                    
                    // IMPORTANT: Non-blocking statuses must NOT consume inventory.
                    if (in_array($status, [
                        'canceled', 'cancelled', 'payment-rejected', 'failed', 'expired', 'rejected',
                        'open', 'pending', 'not-confirmed',
                        'temporary'
                    ], true)) {
                        continue;
                    }
                    // Skip if different boat type
                    if (($booking['boatType'] ?? '') !== $boatId) {
                        continue;
                    }
                    
                    $bookingStart = $booking['date'] ?? null;
                    $bookingEnd = $booking['endDate'] ?? $bookingStart;
                    if (!$bookingStart) continue;
                    
                    // Check if date falls within this booking's range
                    if ($dateStr >= $bookingStart && $dateStr <= $bookingEnd) {
                        // Count quantity for this booking (default to 1 for backward compatibility)
                        $bookingQuantity = isset($booking['quantity']) ? max(1, intval($booking['quantity'])) : 1;
                        $bookedCount += $bookingQuantity;
                    }
                }
                
                // Check if we have enough boats available for the requested quantity
                $availableCount = $totalBoats - $bookedCount;
                if ($availableCount < $quantity) {
                    $isAvailable = false;
                    $blockedDate = $dateStr;
                    break;
                }
                
                if ($bookedCount > $maxBookedCount) {
                    $maxBookedCount = $bookedCount;
                }
                
                $dateToCheck->modify('+1 day');
            }
            
            if (!$isAvailable) {
                $unavailableItems[] = [
                    'boatName' => $item['boatName'] ?? $boatId,
                    'boatId' => $boatId,
                    'blockedDate' => $blockedDate,
                    'reason' => 'insufficient_availability',
                    'requested' => $quantity,
                    'available' => max(0, $totalBoats - $maxBookedCount)
                ];
                continue; // Skip adding to validated items
            }
            
            $serverTotal += $serverPrice;
            $validatedItems[] = [
                'boatId' => $boatId,
                'boatName' => $item['boatName'] ?? $boatId,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'days' => $days,
                'quantity' => $quantity,
                'price' => $serverPrice,
                'useMotor' => $itemUseMotor
            ];
        }
        
        // If any items are unavailable, return error
        if (!empty($unavailableItems)) {
            http_response_code(409); // Conflict
            $boatNames = array_map(function($i) { return $i['boatName']; }, $unavailableItems);
            echo json_encode([
                'success' => false,
                'message' => 'Helaas, de volgende boot(en) zijn inmiddels niet meer beschikbaar: ' . implode(', ', $boatNames) . '. Verwijder deze uit uw winkelwagen en probeer het opnieuw.',
                'unavailableItems' => $unavailableItems
            ]);
            exit;
        }
        
        // Ensure we still have items to process
        if (empty($validatedItems)) {
            throw new Exception('No available items to checkout');
        }

        $rentalSubtotal = round($serverTotal, 2);
        if (isPayOnArrivalMethod($chosenPaymentMethod)) {
            $validatedItems = allocatePayOnArrivalReservationAcrossCartLines(
                $validatedItems,
                $rentalSubtotal
            );
        }
        $feeBreakdown = bookingAdministrativeFeeBreakdown($rentalSubtotal);
        $validatedItems = allocateAdministrativeFeeAcrossCartLines(
            $validatedItems,
            $feeBreakdown['subtotal'],
            $feeBreakdown['administrationFee'],
            $feeBreakdown['grandTotal']
        );
        $grandTotal = $feeBreakdown['grandTotal'];
        $administrationFee = $feeBreakdown['administrationFee'];
        $feePercentStr = (string)(defined('BOOKING_ADMIN_FEE_PERCENT') ? BOOKING_ADMIN_FEE_PERCENT : 0.0);
        
        // Generate shared payment ID
        $cartId = 'cart_' . time() . '_' . bin2hex(random_bytes(4));
        
        // Create Mollie Payment for total (reuse localhost detection from single payment)
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $isLocalhost = strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false;
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        
        // Generate description
        $description = count($validatedItems) === 1 
            ? 'Boot huur: ' . $validatedItems[0]['boatName']
            : count($validatedItems) . ' boten huur - Nijenhuis Botenverhuur';

        if (isPayOnArrivalMethod($chosenPaymentMethod)) {
            $poaCartBr = payOnArrivalReservationFeeBreakdown($rentalSubtotal);
            $reservationTotalCart = $poaCartBr['total'];
            $tripTotalCart = round($rentalSubtotal + $poaCartBr['administrationOnReservation'], 2);
            $balanceDueCart = round($tripTotalCart - $reservationTotalCart, 2);
            $adminFeePoaCart = $poaCartBr['administrationOnReservation'];
            $resPctStrCart = (string)(defined('BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT')
                ? BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT
                : 10.0);
            $cartRedirect = $protocol . '://' . $host . '/pages/payment-success.php?cartId=' . rawurlencode($cartId);

            if ($mockMollieActive) {
                $mockPayId = 'mock_' . bin2hex(random_bytes(12));
                $bookings = loadJsonSafe($bookingsFile);
                $newRows = buildCartCheckoutBookingRows(
                    $cartId,
                    $mockPayId,
                    $validatedItems,
                    $input,
                    $customerName,
                    $customerEmail,
                    $customerPhone,
                    $arrivalTime,
                    $cityOfOrigin,
                    'pending'
                );
                foreach ($newRows as &$poaRow) {
                    $poaRow['paymentMethod'] = CHECKOUT_PAY_ON_ARRIVAL_METHOD;
                }
                unset($poaRow);
                foreach ($newRows as $poaRow) {
                    $bookings[] = $poaRow;
                }
                saveJsonSafe($bookingsFile, $bookings);
                echo json_encode([
                    'success' => true,
                    'cartId' => $cartId,
                    'total' => $tripTotalCart,
                    'rentalSubtotal' => $rentalSubtotal,
                    'administrationFee' => $adminFeePoaCart,
                    'administrationFeePercent' => (float)$feePercentStr,
                    'reservationFee' => $reservationTotalCart,
                    'reservationFeePercent' => (float)$resPctStrCart,
                    'itemCount' => count($validatedItems),
                    'paymentId' => $mockPayId,
                    'paymentUrl' => $cartRedirect,
                    'mockPayment' => true,
                    'payOnArrival' => true,
                ]);
                exit;
            }

            if ($mollieApiKey === '') {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Mollie API key not configured.']);
                exit;
            }

            $molliePoaCartPayload = [
                'amount' => [
                    'currency' => 'EUR',
                    'value' => number_format($reservationTotalCart, 2, '.', ''),
                ],
                'description' => 'Reserveringsbijdrage (niet-restitueerbaar) – Nijenhuis winkelwagen',
                'redirectUrl' => $cartRedirect,
                'metadata' => [
                    'cartId' => $cartId,
                    'paymentKind' => 'pay_on_arrival_reservation',
                    'payOnArrival' => '1',
                    'reservationFee' => number_format($reservationTotalCart, 2, '.', ''),
                    'reservationFeeRentalPortion' => number_format($poaCartBr['rentalPortion'], 2, '.', ''),
                    'reservationFeeAdminOnReservation' => number_format($poaCartBr['administrationOnReservation'], 2, '.', ''),
                    'balanceDueOnArrival' => number_format($balanceDueCart, 2, '.', ''),
                    'grandTotal' => number_format($tripTotalCart, 2, '.', ''),
                    'rentalSubtotal' => number_format($rentalSubtotal, 2, '.', ''),
                    'administrationFee' => number_format($adminFeePoaCart, 2, '.', ''),
                    'administrationFeePercent' => $feePercentStr,
                    'reservationFeePercent' => $resPctStrCart,
                    'itemCount' => (string)count($validatedItems),
                ],
            ];
            if (!$isLocalhost) {
                $molliePoaCartPayload['webhookUrl'] = $protocol . '://' . $host . '/webhook/mollie';
            }

            [$poaCartCode, $poaCartResp] = forwardRequest($baseUrl . '/payments', 'POST', $molliePoaCartPayload, $mollieApiKey);
            $poaCartResult = json_decode($poaCartResp, true);
            if ($poaCartCode === 201 && isset($poaCartResult['id'])) {
                $realPayId = $poaCartResult['id'];
                $bookings = loadJsonSafe($bookingsFile);
                $newRows = buildCartCheckoutBookingRows(
                    $cartId,
                    $realPayId,
                    $validatedItems,
                    $input,
                    $customerName,
                    $customerEmail,
                    $customerPhone,
                    $arrivalTime,
                    $cityOfOrigin,
                    'pending'
                );
                foreach ($newRows as &$poaRow) {
                    $poaRow['paymentMethod'] = CHECKOUT_PAY_ON_ARRIVAL_METHOD;
                }
                unset($poaRow);
                foreach ($newRows as $poaRow) {
                    $bookings[] = $poaRow;
                }
                saveJsonSafe($bookingsFile, $bookings);
                echo json_encode([
                    'success' => true,
                    'cartId' => $cartId,
                    'total' => $tripTotalCart,
                    'rentalSubtotal' => $rentalSubtotal,
                    'administrationFee' => $adminFeePoaCart,
                    'administrationFeePercent' => (float)$feePercentStr,
                    'reservationFee' => $reservationTotalCart,
                    'reservationFeePercent' => (float)$resPctStrCart,
                    'itemCount' => count($validatedItems),
                    'paymentId' => $realPayId,
                    'paymentUrl' => $poaCartResult['_links']['checkout']['href'] ?? null,
                    'payOnArrival' => true,
                ]);
            } else {
                http_response_code($poaCartCode ?: 500);
                echo $poaCartResp ?: json_encode(['success' => false, 'message' => 'Failed to create reservation payment']);
            }
            exit;
        }

        // Local dev: skip Mollie, create bookings, redirect straight to success page
        if ($mockMollieActive) {
            $paymentId = 'mock_' . bin2hex(random_bytes(12));
            $bookings = loadJsonSafe($bookingsFile);
            $newRows = buildCartCheckoutBookingRows(
                $cartId,
                $paymentId,
                $validatedItems,
                $input,
                $customerName,
                $customerEmail,
                $customerPhone,
                $arrivalTime,
                $cityOfOrigin,
                'pending'
            );
            foreach ($newRows as &$row) {
                $row['paymentMethod'] = $chosenPaymentMethod;
            }
            unset($row);
            foreach ($newRows as $row) {
                $bookings[] = $row;
            }
            saveJsonSafe($bookingsFile, $bookings);
            $successUrl = $protocol . '://' . $host . '/pages/payment-success.php?cartId=' . rawurlencode($cartId);
            echo json_encode([
                'success' => true,
                'cartId' => $cartId,
                'total' => $grandTotal,
                'rentalSubtotal' => $rentalSubtotal,
                'administrationFee' => $administrationFee,
                'administrationFeePercent' => (float)$feePercentStr,
                'itemCount' => count($validatedItems),
                'paymentId' => $paymentId,
                'paymentUrl' => $successUrl,
                'mockPayment' => true,
            ]);
            exit;
        }

        if ($mollieApiKey === '') {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Mollie API key not configured.']);
            exit;
        }
        
        $molliePayload = [
            'amount' => [
                'currency' => 'EUR',
                'value' => number_format($grandTotal, 2, '.', '')
            ],
            'description' => $description,
            'method' => $chosenPaymentMethod,
            'redirectUrl' => $protocol . '://' . $host . '/pages/payment-success.php?cartId=' . $cartId,
            'metadata' => [
                'cartId' => $cartId,
                'chosenMethod' => $chosenPaymentMethod,
                'itemCount' => (string)count($validatedItems),
                'rentalSubtotal' => number_format($feeBreakdown['subtotal'], 2, '.', ''),
                'administrationFee' => number_format($feeBreakdown['administrationFee'], 2, '.', ''),
                'administrationFeePercent' => $feePercentStr,
                'grandTotal' => number_format($grandTotal, 2, '.', ''),
            ]
        ];
        
        // Only add webhookUrl for production
        if (!$isLocalhost) {
            // Canonical webhook endpoint (works with Python proxy or PHP fallback in /webhook/mollie)
            $molliePayload['webhookUrl'] = $protocol . '://' . $host . '/webhook/mollie';
        }
        
        [$code, $resp] = forwardRequest($baseUrl . '/payments', 'POST', $molliePayload, $mollieApiKey);
        $mollieResult = json_decode($resp, true);
        
        if ($code === 201 && isset($mollieResult['id'])) {
            $paymentId = $mollieResult['id'];
            
            // Reload bookings to be safe (though likelihood of change is low in milliseconds)
            $bookings = loadJsonSafe($bookingsFile);
            $newRows = buildCartCheckoutBookingRows(
                $cartId,
                $paymentId,
                $validatedItems,
                $input,
                $customerName,
                $customerEmail,
                $customerPhone,
                $arrivalTime,
                $cityOfOrigin,
                'pending'
            );
            foreach ($newRows as &$row) {
                $row['paymentMethod'] = $chosenPaymentMethod;
            }
            unset($row);
            foreach ($newRows as $row) {
                $bookings[] = $row;
            }
            
            saveJsonSafe($bookingsFile, $bookings);
            echo json_encode([
                'success' => true,
                'cartId' => $cartId,
                'total' => $grandTotal,
                'rentalSubtotal' => $rentalSubtotal,
                'administrationFee' => $administrationFee,
                'administrationFeePercent' => (float)$feePercentStr,
                'itemCount' => count($validatedItems),
                'paymentId' => $paymentId,
                'paymentUrl' => $mollieResult['_links']['checkout']['href'] ?? null
            ]);
        } else {
            http_response_code($code ?: 500);
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to create payment',
                'details' => $mollieResult['detail'] ?? 'Unknown error'
            ]);
        }
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
} catch (Throwable $e) {
    http_response_code(500);
    // Log full detail server-side; return a generic message to the client so
    // we don't leak internal validation or backend errors.
    error_log('mollie_api error: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}
?>
