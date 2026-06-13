<?php
/**
 * ========================================================================
 * SHARED AVAILABILITY LOGIC - Nijenhuis Botenverhuur
 * ========================================================================
 * Shared by admin/api.php (external/AlBot) and admin/booking-handler.php.
 * Do NOT put request-handling or output here - pure functions only.
 */

/**
 * Resolve a boat type string to a boat record using flexible matching.
 * Tries (in order): exact ID → case-insensitive ID → substring of name.
 *
 * @param array  $boats    Full boats array from boats.json
 * @param string $boatType Input from caller (slug, display name, or partial name)
 * @return array|null      The matched boat record, or null if no match
 */
function resolveBoatType(array $boats, string $boatType): ?array {
    $input = strtolower(trim($boatType));

    // 1. Exact ID match (fast path - no normalisation needed)
    foreach ($boats as $b) {
        if (($b['id'] ?? '') === $boatType) {
            return $b;
        }
    }

    // 2. Case-insensitive ID match
    foreach ($boats as $b) {
        if (strtolower($b['id'] ?? '') === $input) {
            return $b;
        }
    }

    // 3. Substring match: input contained in name, or name contained in input
    foreach ($boats as $b) {
        $name = strtolower($b['name'] ?? '');
        if ($name !== '' && (strpos($name, $input) !== false || strpos($input, $name) !== false)) {
            return $b;
        }
    }

    return null;
}

/**
 * Count active bookings per date for a specific boat type over a date range.
 * Returns an associative array: date string (Y-m-d) => integer booking count.
 *
 * @param array  $bookings  All booking records
 * @param string $boatType  Canonical boat ID (slug)
 * @param string $startDate Y-m-d
 * @param string $endDate   Y-m-d
 * @return array<string, int>
 */
function countBookingsForBoatOnDateRange(array $bookings, string $boatType, string $startDate, string $endDate): array {
    $counts = [];

    // Initialise all dates in range to zero
    $current = new DateTime($startDate);
    $end     = new DateTime($endDate);
    while ($current <= $end) {
        $counts[$current->format('Y-m-d')] = 0;
        $current->modify('+1 day');
    }

    foreach ($bookings as $booking) {
        $status    = $booking['status'] ?? '';
        $paymentId = $booking['paymentId'] ?? '';
        $hasOnlinePaymentId = !empty($paymentId) && strpos($paymentId, 'manual_') !== 0;

        // Non-blocking terminal states
        if (in_array($status, ['payment-rejected', 'cancelled', 'canceled', 'failed', 'expired'], true)) {
            continue;
        }

        // Incomplete online payments (user still at checkout) - do not block
        if ($hasOnlinePaymentId && in_array($status, ['pending', 'open', 'not-confirmed'], true)) {
            continue;
        }

        // Cart placeholders - do not block
        if ($status === 'temporary') {
            continue;
        }

        // Only count matching boat type
        if (($booking['boatType'] ?? '') !== $boatType) {
            continue;
        }

        $bookingStart    = $booking['date'] ?? null;
        $bookingEnd      = $booking['endDate'] ?? $bookingStart;
        if (!$bookingStart) {
            continue;
        }

        $bookingQuantity = isset($booking['quantity']) ? max(1, (int) $booking['quantity']) : 1;

        $bStart = new DateTime($bookingStart);
        $bEnd   = new DateTime($bookingEnd);

        while ($bStart <= $bEnd) {
            $dateStr = $bStart->format('Y-m-d');
            if (isset($counts[$dateStr])) {
                $counts[$dateStr] += $bookingQuantity;
            }
            $bStart->modify('+1 day');
        }
    }

    return $counts;
}

/**
 * Check whether a boat type is available for a given date range.
 * Also validates season boundaries and booking-window constraints.
 *
 * @param array  $bookings  All booking records
 * @param array  $boats     All boat records
 * @param string $boatType  Canonical boat ID (slug)
 * @param string $startDate Y-m-d
 * @param string $endDate   Y-m-d
 * @return array {
 *   available: bool,
 *   availableCount?: int,
 *   totalBoats?: int,
 *   bookedCount?: int,
 *   reason?: string,
 *   message?: string,
 *   date?: string
 * }
 */
function checkBoatAvailability(array $bookings, array $boats, string $boatType, string $startDate, string $endDate): array {
    // === Season validation ===
    $startObj    = new DateTime($startDate);
    $rentalMonth = (int) $startObj->format('n');
    $rentalDay   = (int) $startObj->format('j');

    $seasonStartM = defined('SEASON_START_MONTH') ? SEASON_START_MONTH : 4;
    $seasonStartD = defined('SEASON_START_DAY')   ? SEASON_START_DAY   : 1;
    $seasonEndM   = defined('SEASON_END_MONTH')   ? SEASON_END_MONTH   : 10;
    $seasonEndD   = defined('SEASON_END_DAY')     ? SEASON_END_DAY     : 31;

    $inSeason = false;
    if ($rentalMonth > $seasonStartM && $rentalMonth < $seasonEndM) {
        $inSeason = true;
    } elseif ($rentalMonth === $seasonStartM && $rentalDay >= $seasonStartD) {
        $inSeason = true;
    } elseif ($rentalMonth === $seasonEndM && $rentalDay <= $seasonEndD) {
        $inSeason = true;
    }

    if (!$inSeason) {
        return [
            'available' => false,
            'reason'    => 'out_of_season_date',
            'message'   => 'Geselecteerde datum valt buiten het vaarseizoen.',
        ];
    }

    // === Booking-window validation ===
    $today      = new DateTime();
    $curYear    = (int) $today->format('Y');
    $rentalYear = (int) $startObj->format('Y');

    if ($rentalYear > $curYear) {
        return [
            'available' => false,
            'reason'    => 'booking_not_open_yet',
            'message'   => "Reserveringen voor $rentalYear openen pas op 1 januari $rentalYear.",
        ];
    }

    if ($rentalYear === $curYear) {
        $openM    = defined('BOOKING_OPEN_MONTH') ? BOOKING_OPEN_MONTH : 1;
        $openD    = defined('BOOKING_OPEN_DAY')   ? BOOKING_OPEN_DAY   : 1;
        $openDate = new DateTime("$curYear-$openM-$openD");

        if ($today < $openDate) {
            return [
                'available' => false,
                'reason'    => 'booking_not_open_yet',
                'message'   => "Reserveringen voor $curYear openen pas op " . $openDate->format('d-m-Y') . '.',
            ];
        }
    }

    // === Find boat record ===
    $boat = null;
    foreach ($boats as $b) {
        if (($b['id'] ?? '') === $boatType) {
            $boat = $b;
            break;
        }
    }

    if (!$boat) {
        return ['available' => false, 'reason' => 'boat_not_found'];
    }

    $totalBoats = (int) ($boat['total'] ?? 1);
    $counts     = countBookingsForBoatOnDateRange($bookings, $boatType, $startDate, $endDate);

    $maxBookedCount = 0;
    $maxBookedDate  = null;

    foreach ($counts as $date => $count) {
        if ($count >= $totalBoats) {
            return [
                'available'   => false,
                'reason'      => 'fully_booked',
                'date'        => $date,
                'bookedCount' => $count,
                'totalBoats'  => $totalBoats,
            ];
        }
        if ($count > $maxBookedCount) {
            $maxBookedCount = $count;
            $maxBookedDate  = $date;
        }
    }

    return [
        'available'      => true,
        'availableCount' => $totalBoats - $maxBookedCount,
        'totalBoats'     => $totalBoats,
        'bookedCount'    => $maxBookedCount,
    ];
}
