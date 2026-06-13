<?php
// ========================================================================
// UNIFIED EXTERNAL API - Nijenhuis Botenverhuur
// Authentication: X-API-Key or Authorization: Bearer <key>
//
// BOOKING_API_KEY   - boats, availability, prepareBooking
// INVENTORY_API_KEY - boats, availability (read-only subset)
//
// Actions (POST JSON body):  checkAvailability | prepareBooking
// Actions (GET query string): boats | availability
//
// NOTE: This API never creates bookings or initiates payments.
// prepareBooking validates availability and returns a pre-filled
// booking URL that the user completes themselves on the website.
// ========================================================================

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering
if (!ob_get_level()) {
    ob_start();
}

header('Content-Type: application/json');

// Load shared components
require_once __DIR__ . '/../components/data_access.php';
require_once __DIR__ . '/../components/pricing_engine.php';
require_once __DIR__ . '/../components/security.php';
require_once __DIR__ . '/../components/microsoft_graph_mail.php';
require_once __DIR__ . '/../components/availability.php';

// Load .env file safely FIRST (before CORS check)
loadEnvSafe(__DIR__ . '/../.env');

// Load centralized CORS configuration
require_once __DIR__ . '/../components/cors.php';

// Handle CORS preflight requests using centralized function
handleCorsPreflight();

// Set CORS headers for actual requests using centralized function
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (!empty($origin)) {
    if (!setCorsHeadersSafe($origin, null, false)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Origin not allowed']);
        exit;
    }
}

// Configuration (operational JSON under data/)
$bookingsFile    = nijenhuis_data_path('bookings.json');
$boatsFile       = nijenhuis_data_path('boats.json');
$bookingApiKey   = getenv('BOOKING_API_KEY')   ?: ($_ENV['BOOKING_API_KEY']   ?? '');
$inventoryApiKey = getenv('INVENTORY_API_KEY') ?: ($_ENV['INVENTORY_API_KEY'] ?? '');
$mollieApiKey    = getenv('MOLLIE_API_KEY')    ?: ($_ENV['MOLLIE_API_KEY']    ?? '');

/**
 * Extract the API key from request headers (X-API-Key or Authorization: Bearer).
 * Checks both getallheaders() and $_SERVER for PHP-FPM compatibility.
 */
function getProvidedApiKey(): string {
    // $_SERVER is the most reliable source in PHP-FPM + nginx environments
    if (!empty($_SERVER['HTTP_X_API_KEY'])) {
        return trim($_SERVER['HTTP_X_API_KEY']);
    }

    // getallheaders() fallback (Apache / some FPM configs)
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    if (isset($headers['X-API-Key']))     return trim($headers['X-API-Key']);
    if (isset($headers['x-api-key']))     return trim($headers['x-api-key']);

    // Authorization: Bearer <key>
    $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? ($headers['Authorization'] ?? '');
    if (!empty($auth) && preg_match('/Bearer\s+(.+)$/i', $auth, $m)) {
        return trim($m[1]);
    }

    return '';
}

/**
 * Validate request: accept either BOOKING_API_KEY or INVENTORY_API_KEY.
 * Used for all read-only operations (boats list, availability checks).
 * Returns 'booking' | 'inventory' to indicate which key was used.
 */
function validateApiKey(): string {
    global $bookingApiKey, $inventoryApiKey;

    $provided = getProvidedApiKey();

    if (!empty($bookingApiKey) && hash_equals($bookingApiKey, $provided)) {
        return 'booking';
    }
    if (!empty($inventoryApiKey) && hash_equals($inventoryApiKey, $provided)) {
        return 'inventory';
    }

    if (empty($bookingApiKey) && empty($inventoryApiKey)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'API Configuration Error: No keys configured']);
        exit;
    }

    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Invalid API Key']);
    exit;
}

// Authenticate every request - accepts either key
$authenticatedAs = validateApiKey();

// HELPER FUNCTIONS (Wrappers for shared components)

function loadBookings($file) {
    return loadJsonSafe($file);
}

function saveBookings($file, $bookings) {
    return saveJsonSafe($file, $bookings);
}

function loadBoats($file) {
    return loadJsonSafe($file);
}

function generateId() {
    return uniqid() . '_' . time();
}


// Replaced local calculatePrice with shared component wrapper
function getPrice(string $boatType, int $numberOfDays): float {
    global $boatsFile;
    $boats = loadBoats($boatsFile);
    return (float) calculateBoatPrice($boatType, $numberOfDays, $boats);
}

// API ROUTING

$method = $_SERVER['REQUEST_METHOD'];
// SECURITY: Sanitize GET parameter
$action = sanitizeText($_GET['action'] ?? '');

// GET REQUESTS
if ($method === 'GET') {
    switch ($action) {
        case 'boats':
            // Rate limiting for public endpoint
            $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            if (!checkRateLimitAtomic($clientIp, 100, 60)) { // 100 requests per minute
                http_response_code(429);
                echo json_encode(['success' => false, 'message' => 'Rate limit exceeded. Please try again later.']);
                exit;
            }
            
            $boats = loadBoats($boatsFile);
            $response = [];
            foreach ($boats as $boat) {
                // Return simplified structure (sanitize output)
                $response[] = [
                    'id' => sanitizeText($boat['id'] ?? ''),
                    'name' => sanitizeText($boat['name'] ?? ''),
                    'capacity' => (int)($boat['passengerCount'] ?? 0),
                    'priceDay' => (float)($boat['pricePerDay'] ?? 0),
                    'available' => (bool)($boat['available'] ?? false)
                ];
            }
            echo json_encode(['success' => true, 'data' => $response]);
            break;

        case 'availability':
            // Rate limiting
            $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            if (!checkRateLimitAtomic($clientIp, 100, 60)) {
                http_response_code(429);
                echo json_encode(['success' => false, 'message' => 'Rate limit exceeded. Please try again later.']);
                exit;
            }

            // SECURITY: Sanitize and validate GET parameters
            $date        = sanitizeText($_GET['date'] ?? '');
            $duration    = max(1, (int) sanitizeText($_GET['duration'] ?? '1'));
            $boatTypeRaw = sanitizeText($_GET['boatType'] ?? '');

            if (empty($date)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Date parameter required (YYYY-MM-DD)']);
                exit;
            }

            $dateObj = DateTime::createFromFormat('Y-m-d', $date);
            if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD']);
                exit;
            }

            // Compute endDate from duration
            $endDateObj = clone $dateObj;
            $endDateObj->modify('+' . ($duration - 1) . ' days');
            $endDate = $endDateObj->format('Y-m-d');

            $bookings = loadBookings($bookingsFile);
            $boats    = loadBoats($boatsFile);

            if (!empty($boatTypeRaw)) {
                // Single-boat response (AlBot / VYBR!S path)
                $resolvedBoat = resolveBoatType($boats, $boatTypeRaw);

                if (!$resolvedBoat) {
                    echo json_encode([
                        'success'        => true,
                        'available'      => false,
                        'availableCount' => 0,
                        'totalCount'     => 0,
                        'boatId'         => null,
                        'boatName'       => $boatTypeRaw,
                        'pricePerDay'    => 0,
                        'duration'       => $duration,
                        'totalPrice'     => 0,
                        'date'           => $date,
                        'endDate'        => $endDate,
                    ]);
                    exit;
                }

                $boatId     = $resolvedBoat['id'];
                $result     = checkBoatAvailability($bookings, $boats, $boatId, $date, $endDate);
                $totalPrice = $result['available']
                    ? (float) calculateBoatPrice($boatId, $duration, $boats)
                    : 0.0;

                echo json_encode([
                    'success'        => true,
                    'available'      => (bool) $result['available'],
                    'availableCount' => (int) ($result['availableCount'] ?? 0),
                    'totalCount'     => (int) ($result['totalBoats'] ?? 0),
                    'boatId'         => $boatId,
                    'boatName'       => $resolvedBoat['name'] ?? $boatId,
                    'pricePerDay'    => (float) ($resolvedBoat['pricePerDay'] ?? 0),
                    'duration'       => $duration,
                    'totalPrice'     => $totalPrice,
                    'date'           => $date,
                    'endDate'        => $endDate,
                    'reason'         => $result['reason'] ?? null,
                ]);
                exit;
            }

            // No boatType: return full list (backward-compatible for frontend)
            $availability = [];
            foreach ($boats as $boat) {
                $boatId = $boat['id'] ?? '';
                $result = checkBoatAvailability($bookings, $boats, $boatId, $date, $endDate);
                $availability[] = [
                    'boatId'         => $boatId,
                    'boatName'       => $boat['name'] ?? $boatId,
                    'available'      => (bool) $result['available'],
                    'availableCount' => (int) ($result['availableCount'] ?? 0),
                    'totalCount'     => (int) ($result['totalBoats'] ?? $boat['total'] ?? 0),
                    'pricePerDay'    => (float) ($boat['pricePerDay'] ?? 0),
                    'duration'       => $duration,
                    'totalPrice'     => $result['available']
                        ? (float) calculateBoatPrice($boatId, $duration, $boats)
                        : 0.0,
                ];
            }

            echo json_encode([
                'success'  => true,
                'date'     => $date,
                'endDate'  => $endDate,
                'duration' => $duration,
                'data'     => $availability,
            ]);
            break;

        default:
            echo json_encode([
                'success'  => true,
                'message'  => 'Nijenhuis External API',
                'actions'  => [
                    'GET  ?action=boats'                          => 'List all boats with pricing',
                    'GET  ?action=availability&date=&boatType=&duration=' => 'Check availability',
                    'POST checkAvailability'                      => 'Check availability (JSON body)',
                    'POST prepareBooking'                         => 'Validate + return pre-filled booking URL',
                ],
            ]);
            break;
    }
}

// POST REQUESTS - AlBot sends Content-Type: application/json
elseif ($method === 'POST') {
    $inputRaw = file_get_contents('php://input');
    $input    = validateJsonInput($inputRaw);

    if ($input === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
        exit;
    }

    // Derive action from JSON body (AlBot sends { "action": "...", ... })
    $action = sanitizeText($input['action'] ?? $_GET['action'] ?? '');

    // -----------------------------------------------------------------------
    // ACTION: checkAvailability
    // AlBot POST payload: { "action": "checkAvailability", "boatType": "...",
    //                       "date": "YYYY-MM-DD", "duration": N }
    // -----------------------------------------------------------------------
    if ($action === 'checkAvailability') {
        $boatTypeRaw = sanitizeText($input['boatType'] ?? '');
        $date        = sanitizeText($input['date'] ?? '');
        $duration    = max(1, (int) ($input['duration'] ?? 1));

        if (empty($date)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'date is required (YYYY-MM-DD)']);
            exit;
        }

        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD']);
            exit;
        }

        $endDateObj = clone $dateObj;
        $endDateObj->modify('+' . ($duration - 1) . ' days');
        $endDate = $endDateObj->format('Y-m-d');

        $bookings = loadBookings($bookingsFile);
        $boats    = loadBoats($boatsFile);

        if (empty($boatTypeRaw)) {
            // No specific boat: return availability for all boats
            $availability = [];
            foreach ($boats as $boat) {
                $boatId = $boat['id'] ?? '';
                $result = checkBoatAvailability($bookings, $boats, $boatId, $date, $endDate);
                $availability[] = [
                    'boatId'         => $boatId,
                    'boatName'       => $boat['name'] ?? $boatId,
                    'available'      => (bool) $result['available'],
                    'availableCount' => (int) ($result['availableCount'] ?? 0),
                    'totalCount'     => (int) ($result['totalBoats'] ?? $boat['total'] ?? 0),
                    'pricePerDay'    => (float) ($boat['pricePerDay'] ?? 0),
                    'duration'       => $duration,
                    'totalPrice'     => $result['available']
                        ? (float) calculateBoatPrice($boatId, $duration, $boats)
                        : 0.0,
                ];
            }
            echo json_encode([
                'success'  => true,
                'date'     => $date,
                'endDate'  => $endDate,
                'duration' => $duration,
                'data'     => $availability,
            ]);
            exit;
        }

        // Flexible boat resolution - accepts slug, display name, or partial name
        $resolvedBoat = resolveBoatType($boats, $boatTypeRaw);

        if (!$resolvedBoat) {
            echo json_encode([
                'success'        => true,
                'available'      => false,
                'availableCount' => 0,
                'totalCount'     => 0,
                'boatId'         => null,
                'boatName'       => $boatTypeRaw,
                'pricePerDay'    => 0,
                'duration'       => $duration,
                'totalPrice'     => 0,
                'date'           => $date,
                'endDate'        => $endDate,
            ]);
            exit;
        }

        $boatId     = $resolvedBoat['id'];
        $result     = checkBoatAvailability($bookings, $boats, $boatId, $date, $endDate);
        $totalPrice = $result['available']
            ? (float) calculateBoatPrice($boatId, $duration, $boats)
            : 0.0;

        echo json_encode([
            'success'        => true,
            'available'      => (bool) $result['available'],
            'availableCount' => (int) ($result['availableCount'] ?? 0),
            'totalCount'     => (int) ($result['totalBoats'] ?? 0),
            'boatId'         => $boatId,
            'boatName'       => $resolvedBoat['name'] ?? $boatId,
            'pricePerDay'    => (float) ($resolvedBoat['pricePerDay'] ?? 0),
            'duration'       => $duration,
            'totalPrice'     => $totalPrice,
            'date'           => $date,
            'endDate'        => $endDate,
            'reason'         => $result['reason'] ?? null,
        ]);
        exit;
    }

    // -----------------------------------------------------------------------
    // ACTION: prepareBooking
    // Validates availability and returns a pre-filled booking URL.
    // Nothing is stored. No payment is initiated.
    // The user clicks the URL and completes the booking on the website.
    //
    // POST payload: { "action": "prepareBooking",
    //                 "boatType": "Electrosloep 8",   (flexible name or slug)
    //                 "date": "YYYY-MM-DD",
    //                 "duration": 2 }                 (or "endDate": "YYYY-MM-DD")
    // -----------------------------------------------------------------------
    if ($action === 'prepareBooking') {
        $boatTypeRaw = sanitizeText($input['boatType'] ?? '');
        $date        = sanitizeText($input['date'] ?? '');
        $duration    = max(1, (int) ($input['duration'] ?? 1));

        if (empty($date)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'date is required (YYYY-MM-DD)']);
            exit;
        }

        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD']);
            exit;
        }

        // Honour explicit endDate if provided, otherwise derive from duration
        if (!empty($input['endDate'])) {
            $endDateObj = DateTime::createFromFormat('Y-m-d', sanitizeText($input['endDate']));
            if (!$endDateObj || $endDateObj < $dateObj) {
                $endDateObj = clone $dateObj;
            }
            $duration = $dateObj->diff($endDateObj)->days + 1;
        } else {
            $endDateObj = clone $dateObj;
            $endDateObj->modify('+' . ($duration - 1) . ' days');
        }
        $endDate = $endDateObj->format('Y-m-d');

        $boats        = loadBoats($boatsFile);
        $resolvedBoat = !empty($boatTypeRaw) ? resolveBoatType($boats, $boatTypeRaw) : null;

        if (!empty($boatTypeRaw) && !$resolvedBoat) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Boat not found: ' . $boatTypeRaw]);
            exit;
        }

        // Check availability when a specific boat was requested
        $available      = true;
        $availableCount = null;
        $reason         = null;

        if ($resolvedBoat) {
            $bookings  = loadBookings($bookingsFile);
            $avail     = checkBoatAvailability($bookings, $boats, $resolvedBoat['id'], $date, $endDate);
            $available      = (bool) $avail['available'];
            $availableCount = (int) ($avail['availableCount'] ?? 0);
            $reason         = $avail['reason'] ?? null;
        }

        // Pricing (0 when unavailable or no specific boat given)
        $pricePerDay = $resolvedBoat ? (float) ($resolvedBoat['pricePerDay'] ?? 0) : 0.0;
        $totalPrice  = ($resolvedBoat && $available)
            ? (float) calculateBoatPrice($resolvedBoat['id'], $duration, $boats)
            : 0.0;

        // Build the pre-filled booking URL using the clean-URL route
        $protocol   = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host       = $_SERVER['HTTP_HOST'] ?? 'nijenhuis-botenverhuur.com';
        $baseUrl    = $protocol . '://' . $host;

        $params = ['date' => $date, 'endDate' => $endDate];
        if ($resolvedBoat) {
            $params['boatType'] = $resolvedBoat['id'];
        }
        $bookingUrl = $baseUrl . '/booking?' . http_build_query($params);

        echo json_encode([
            'success'        => true,
            'available'      => $available,
            'availableCount' => $availableCount,
            'boatId'         => $resolvedBoat ? $resolvedBoat['id'] : null,
            'boatName'       => $resolvedBoat ? ($resolvedBoat['name'] ?? '') : null,
            'pricePerDay'    => $pricePerDay,
            'duration'       => $duration,
            'totalPrice'     => $totalPrice,
            'date'           => $date,
            'endDate'        => $endDate,
            'reason'         => $reason,
            'bookingUrl'     => $bookingUrl,
            'message'        => $available
                ? 'Share the bookingUrl with the user to complete their reservation.'
                : 'Boat is not available for the selected dates.',
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unknown action. Supported: checkAvailability, prepareBooking']);
}
?>
