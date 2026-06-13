<?php
/**
 * Server-side booking confirmation PDF (FPDF) for email attachment.
 * Text is transliterated to ISO-8859-1 for core fonts; amounts use "EUR" prefix.
 */
require_once __DIR__ . '/data_paths.php';
require_once __DIR__ . '/config.php';

/**
 * Cash security deposit (borg) for this booking row: boat deposit × quantity from data/boats.json.
 *
 * @param array<string, mixed> $booking
 */
function booking_confirmation_security_deposit_euros(array $booking): float {
    $boatsFile = nijenhuis_data_path('boats.json');
    if (!is_readable($boatsFile)) {
        return 0.0;
    }
    $boats = json_decode((string)file_get_contents($boatsFile), true);
    if (!is_array($boats)) {
        return 0.0;
    }
    $boatId = (string)($booking['boatType'] ?? '');
    $qty = (int)($booking['quantity'] ?? 1);
    if ($qty < 1) {
        $qty = 1;
    }
    foreach ($boats as $b) {
        if (($b['id'] ?? '') === $boatId) {
            $d = (float)($b['deposit'] ?? 0);
            return round(max(0, $d) * $qty, 2);
        }
    }
    return 0.0;
}

/**
 * Pay-on-arrival reservation breakdown when all fields match checkout / payment-success.
 *
 * @param array<string, mixed> $booking
 * @return array{
 *   rental: float,
 *   admin: float,
 *   total: float,
 *   rr: float,
 *   rad: float,
 *   reservationFee: float,
 *   balance: float,
 *   showSlice: bool
 * }|null
 */
function booking_confirmation_poa_breakdown(array $booking): ?array {
    if (empty($booking['payOnArrivalReservation'])) {
        return null;
    }
    if (!isset($booking['rentalAmount'], $booking['administrationFee'], $booking['reservationFee'], $booking['balanceDueOnArrival'])) {
        return null;
    }
    $rental = (float)$booking['rentalAmount'];
    $admin = (float)$booking['administrationFee'];
    $total = (float)($booking['amount'] ?? 0);
    if ($total <= 0) {
        $total = round($rental + $admin, 2);
    }
    $reservationFee = (float)$booking['reservationFee'];
    $balance = (float)$booking['balanceDueOnArrival'];
    $rr = isset($booking['reservationFeeRentalPortion']) ? (float)$booking['reservationFeeRentalPortion'] : null;
    $rad = isset($booking['reservationFeeAdminOnReservation']) ? (float)$booking['reservationFeeAdminOnReservation'] : null;
    $showSlice = $rr !== null && $rad !== null && ($rr > 0 || $rad > 0);

    return [
        'rental' => $rental,
        'admin' => $admin,
        'total' => $total,
        'rr' => $rr ?? 0.0,
        'rad' => $rad ?? 0.0,
        'reservationFee' => $reservationFee,
        'balance' => $balance,
        'showSlice' => $showSlice,
    ];
}

/**
 * Long weekday date for POA summary (matches payment-success style).
 *
 * @return string Single day or "start t/m end" when range differs
 */
function booking_confirmation_format_date_long_nl(?string $date, ?string $endDate): string {
    $months = [1 => 'januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];
    $days = ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'];
    $fmt = static function (int $ts) use ($months, $days): string {
        $w = (int)date('w', $ts);
        $j = (int)date('j', $ts);
        $n = (int)date('n', $ts);
        $y = (int)date('Y', $ts);
        return $days[$w] . ' ' . $j . ' ' . ($months[$n] ?? date('F', $ts)) . ' ' . $y;
    };
    $d = $date ? strtotime((string)$date) : false;
    if ($d === false) {
        return '-';
    }
    $e = ($endDate && (string)$endDate !== '') ? strtotime((string)$endDate) : false;
    if ($e !== false && date('Y-m-d', $e) !== date('Y-m-d', $d)) {
        return $fmt($d) . ' t/m ' . $fmt($e);
    }
    return $fmt($d);
}

/**
 * @return string English long date or range
 */
function booking_confirmation_format_date_long_en(?string $date, ?string $endDate): string {
    $months = [1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $fmt = static function (int $ts) use ($months, $days): string {
        $w = (int)date('w', $ts);
        $j = (int)date('j', $ts);
        $n = (int)date('n', $ts);
        $y = (int)date('Y', $ts);
        return $days[$w] . ' ' . $j . ' ' . ($months[$n] ?? date('F', $ts)) . ' ' . $y;
    };
    $d = $date ? strtotime((string)$date) : false;
    if ($d === false) {
        return '-';
    }
    $e = ($endDate && (string)$endDate !== '') ? strtotime((string)$endDate) : false;
    if ($e !== false && date('Y-m-d', $e) !== date('Y-m-d', $d)) {
        return $fmt($d) . ' to ' . $fmt($e);
    }
    return $fmt($d);
}

require_once __DIR__ . '/vendor/fpdf/fpdf.php';

/**
 * @param array<string, mixed> $booking
 */
function booking_confirmation_pdf_resolve_boat_name(array $booking): string {
    $boatName = $booking['boatType'] ?? 'Onbekend';
    $boatsFile = nijenhuis_data_path('boats.json');
    if (file_exists($boatsFile)) {
        $boats = json_decode(file_get_contents($boatsFile), true);
        if (is_array($boats)) {
            foreach ($boats as $b) {
                if (($b['id'] ?? '') === ($booking['boatType'] ?? '')) {
                    return (string)($b['name'] ?? $boatName);
                }
            }
        }
    }
    return (string)$boatName;
}

function booking_confirmation_pdf_s(string $s): string {
    $s = (string)$s;
    $o = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $s);
    return $o !== false ? $o : $s;
}

function booking_confirmation_pdf_money(float $n): string {
    return 'EUR ' . number_format($n, 2, ',', '.');
}

/**
 * Local filesystem path to boat JPEG/PNG for FPDF Image(), or null.
 *
 * @param array<string, mixed> $booking
 */
function booking_confirmation_boat_image_local_path(array $booking): ?string {
    $boatsFile = nijenhuis_data_path('boats.json');
    if (!is_readable($boatsFile)) {
        return null;
    }
    $boats = json_decode((string)file_get_contents($boatsFile), true);
    if (!is_array($boats)) {
        return null;
    }
    $boatId = (string)($booking['boatType'] ?? '');
    foreach ($boats as $b) {
        if (($b['id'] ?? '') !== $boatId) {
            continue;
        }
        $rel = (string)($b['headerImage'] ?? $b['image'] ?? '');
        if ($rel === '') {
            return null;
        }
        $rel = preg_replace('#^\.\./#', '', $rel);
        $full = realpath(__DIR__ . '/../' . $rel);
        if ($full && is_readable($full) && preg_match('/\.(jpe?g|png)$/i', $full)) {
            return $full;
        }
        return null;
    }
    return null;
}

/**
 * Draw one POA price row (label left, amount right) at current Y; returns new Y.
 * Optional $labelX / $amountRightX place the row in the hero column (right of boat image).
 */
function booking_confirmation_pdf_poa_row(
    FPDF $pdf,
    float $m,
    float $pageW,
    float $y,
    string $label,
    string $amountRight,
    bool $boldLabel = false,
    ?float $labelX = null,
    ?float $amountRightX = null
): float {
    $lx = $labelX ?? $m;
    $arx = $amountRightX ?? ($pageW - $m);
    $amountW = 42.0;
    $labelW = max(22.0, $arx - $lx - $amountW);
    $pdf->SetXY($lx, $y);
    $pdf->SetFont('Helvetica', $boldLabel ? 'B' : '', 9);
    $pdf->SetTextColor(66, 71, 80);
    $pdf->Cell($labelW, 5.2, booking_confirmation_pdf_s($label), 0, 0, 'L');
    $pdf->SetFont('Helvetica', $boldLabel ? 'B' : '', 9);
    $pdf->SetTextColor(0, 49, 94);
    $pdf->SetXY($arx - $amountW, $y);
    $pdf->Cell($amountW, 5.2, booking_confirmation_pdf_s($amountRight), 0, 1, 'R');
    return $y + 5.5;
}

/** Prijsoverzicht card: #0C2D48 */
function booking_confirmation_pdf_poa_navy_rgb(): array {
    return [12, 45, 72];
}

/** text-gray-700 */
function booking_confirmation_pdf_poa_label_gray_rgb(): array {
    return [55, 65, 81];
}

/**
 * Main cost rows: gray-700 label, navy amount (matches HTML card).
 */
function booking_confirmation_pdf_poa_twocol(FPDF $pdf, float $lx, float $rightX, float $y, string $label, string $rightText): float {
    $amountW = 42.0;
    $labelW = max(22.0, $rightX - $lx - $amountW);
    [$lgR, $lgG, $lgB] = booking_confirmation_pdf_poa_label_gray_rgb();
    [$r, $g, $b] = booking_confirmation_pdf_poa_navy_rgb();
    $pdf->SetXY($lx, $y);
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor($lgR, $lgG, $lgB);
    $pdf->Cell($labelW, 5.5, booking_confirmation_pdf_s($label), 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor($r, $g, $b);
    $pdf->SetXY($rightX - $amountW, $y);
    $pdf->Cell($amountW, 5.5, booking_confirmation_pdf_s($rightText), 0, 1, 'R');

    return $y + 6.0;
}

/**
 * Paid-at-reservation slice: left "Label: EUR x" gray, same amount right navy.
 */
function booking_confirmation_pdf_poa_twocol_slice_dup(FPDF $pdf, float $lx, float $rightX, float $y, string $label, string $moneySame): float {
    $amountW = 42.0;
    $labelW = max(22.0, $rightX - $lx - $amountW);
    [$lgR, $lgG, $lgB] = booking_confirmation_pdf_poa_label_gray_rgb();
    [$r, $g, $b] = booking_confirmation_pdf_poa_navy_rgb();
    $leftLine = $label . ': ' . $moneySame;
    $pdf->SetXY($lx, $y);
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor($lgR, $lgG, $lgB);
    $pdf->Cell($labelW, 5.5, booking_confirmation_pdf_s($leftLine), 0, 0, 'L');
    $pdf->SetFont('Helvetica', '', 10);
    $pdf->SetTextColor($r, $g, $b);
    $pdf->SetXY($rightX - $amountW, $y);
    $pdf->Cell($amountW, 5.5, booking_confirmation_pdf_s($moneySame), 0, 1, 'R');

    return $y + 6.0;
}

/**
 * Totaal betaald row: bold navy both columns.
 */
function booking_confirmation_pdf_poa_twocol_both_bold(FPDF $pdf, float $lx, float $rightX, float $y, string $label, string $rightText): float {
    $amountW = 42.0;
    $labelW = max(22.0, $rightX - $lx - $amountW);
    [$r, $g, $b] = booking_confirmation_pdf_poa_navy_rgb();
    $lab = $label;
    if ($lab !== '' && $lab[strlen($lab) - 1] !== ':') {
        $lab .= ':';
    }
    $pdf->SetXY($lx, $y);
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->SetTextColor($r, $g, $b);
    $pdf->Cell($labelW, 5.8, booking_confirmation_pdf_s($lab), 0, 0, 'L');
    $pdf->SetXY($rightX - $amountW, $y);
    $pdf->Cell($amountW, 5.8, booking_confirmation_pdf_s($rightText), 0, 1, 'R');

    return $y + 6.5;
}

/**
 * #D9EAF5 box, border #0C2D48; title ~20px, amount ~24px; footer ~13px.
 */
function booking_confirmation_pdf_poa_arrival_box(FPDF $pdf, float $x, float $y, float $w, string $totalMoney, bool $showInclusiefBorg): float {
    $boxH = $showInclusiefBorg ? 22.0 : 16.0;
    $pdf->SetFillColor(217, 234, 245);
    [$r, $g, $b] = booking_confirmation_pdf_poa_navy_rgb();
    $pdf->SetDrawColor($r, $g, $b);
    $pdf->SetLineWidth(0.35);
    $pdf->Rect($x, $y, $w, $boxH, 'FD');

    $innerPad = 3.2;
    $usable = $w - 2 * $innerPad;
    $leftW = $usable * 0.55;
    $rightW = $usable - $leftW;
    $pdf->SetXY($x + $innerPad, $y + 5.2);
    $pdf->SetFont('Helvetica', 'B', 15);
    $pdf->SetTextColor($r, $g, $b);
    $pdf->Cell($leftW, 7, booking_confirmation_pdf_s('Totaal bij aankomst:'), 0, 0, 'L');
    $pdf->SetFont('Helvetica', 'B', 18);
    $pdf->Cell($rightW, 7, booking_confirmation_pdf_s($totalMoney), 0, 1, 'R');

    if ($showInclusiefBorg) {
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->SetXY($x + 3.2, $y + $boxH - 4.8);
        $pdf->Cell($w - 6.4, 4, booking_confirmation_pdf_s('Inclusief borg'), 0, 0, 'R');
    }

    return $y + $boxH + 2;
}

/**
 * @param array{rental: float, admin: float, rr: float, rad: float, reservationFee: float, balance: float, showSlice: bool} $p
 */
function booking_confirmation_pdf_poa_trip_date_extra_height(string $tripDateLong): float {
    $tripDateLong = trim($tripDateLong);
    if ($tripDateLong === '') {
        return 0.0;
    }
    $len = function_exists('mb_strlen') ? (int) mb_strlen($tripDateLong, 'UTF-8') : strlen($tripDateLong);
    $lines = max(1, (int) ceil($len / 36));

    return 5.0 + $lines * 5.2 + 4.0;
}

function booking_confirmation_pdf_poa_card_height(array $p, float $borg, bool $hasDuration, string $tripDateLong = ''): float {
    $pad = 8.5;
    $h = $pad * 2 + 18.0 + ($hasDuration ? 10.0 : 0.0) + 5.0;
    $h += booking_confirmation_pdf_poa_trip_date_extra_height($tripDateLong);
    $h += 5.8 * 3;
    $h += 6.5;
    $h += 8.0;
    $h += 8.0;
    $h += 4.0 + ($borg > 0 ? 26.0 : 20.0);
    $h += 7.0;

    return $h;
}

/**
 * POA price card (~420px / 111mm template): centered block, generous padding, blue total box.
 *
 * @param array{rental: float, admin: float, rr: float, rad: float, reservationFee: float, balance: float, showSlice: bool} $p
 * @return float Y position below the card (for continuing the page)
 */
function booking_confirmation_pdf_render_poa_price_card(
    FPDF $pdf,
    float $cardX,
    float $cardTop,
    float $cardW,
    float $borg,
    array $p,
    string $durationSub,
    string $tripDateLong = '',
    ?array &$layoutMeta = null
): float {
    $w = $cardW;
    $rx = $cardX;
    $ry = $cardTop;
    $amountRx = $cardX + $cardW;
    $hasDur = $durationSub !== '';
    $cardH = booking_confirmation_pdf_poa_card_height($p, $borg, $hasDur, $tripDateLong);
    $pdf->SetFillColor(220, 222, 226);
    $pdf->Rect($rx + 0.5, $ry + 0.9, $w, $cardH, 'F');
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetDrawColor(209, 213, 219);
    $pdf->SetLineWidth(0.3);
    $pdf->Rect($rx, $ry, $w, $cardH, 'FD');

    $pad = 8.5;
    $innerL = $rx + $pad;
    $innerR = $amountRx - $pad;
    $y = $ry + $pad;
    [$nr, $ng, $nb] = booking_confirmation_pdf_poa_navy_rgb();

    if ($tripDateLong !== '') {
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->SetTextColor(78, 95, 121);
        $pdf->SetXY($innerL, $y);
        $pdf->Cell($w - 2 * $pad, 4, booking_confirmation_pdf_s('DATUM VAN VAART'), 0, 1, 'L');
        $y = $pdf->GetY() + 1;
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetTextColor(0, 49, 94);
        $pdf->SetXY($innerL, $y);
        $pdf->MultiCell($w - 2 * $pad, 5, booking_confirmation_pdf_s($tripDateLong), 0, 'L');
        $y = $pdf->GetY() + 3;
    }

    $pdf->SetFont('Helvetica', 'B', 16);
    $pdf->SetTextColor($nr, $ng, $nb);
    $pdf->SetXY($innerL, $y);
    $pdf->Cell($w - 2 * $pad, 8, booking_confirmation_pdf_s('Prijsoverzicht'), 0, 1, 'L');
    $y = $pdf->GetY() + 0.5;
    if ($hasDur) {
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetTextColor($nr, $ng, $nb);
        $pdf->SetXY($innerL, $y);
        $pdf->Cell($w - 2 * $pad, 7, booking_confirmation_pdf_s($durationSub), 0, 1, 'L');
        $y = $pdf->GetY() + 2;
    } else {
        $y += 1;
    }

    $y += 3;
    $y = booking_confirmation_pdf_poa_twocol($pdf, $innerL, $innerR, $y, 'Huur', booking_confirmation_pdf_money($p['rental']));
    if ($borg > 0) {
        $y = booking_confirmation_pdf_poa_twocol($pdf, $innerL, $innerR, $y, 'Borg bij aankomst', booking_confirmation_pdf_money($borg));
    } else {
        $y = booking_confirmation_pdf_poa_twocol($pdf, $innerL, $innerR, $y, 'Borg bij aankomst', 'Geen volgens boottype');
    }

    $y += 2;
    $pdf->SetDrawColor(229, 231, 235);
    $pdf->SetLineWidth(0.35);
    $pdf->Line($innerL, $y + 0.5, $innerR, $y + 0.5);
    $y += 5.5;

    $y += 0.5;
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->SetTextColor($nr, $ng, $nb);
    $pdf->SetXY($innerL, $y);
    $pdf->Cell($w - 2 * $pad, 6, booking_confirmation_pdf_s('Betaald bij reservering'), 0, 1, 'L');
    $y = $pdf->GetY() + 0.8;

    $y += 0.5;
    $y = booking_confirmation_pdf_poa_twocol_both_bold(
        $pdf,
        $innerL,
        $innerR,
        $y,
        'Totaal betaald (niet-restitueerbaar)',
        booking_confirmation_pdf_money($p['reservationFee'])
    );

    $y += 1;
    $totArr = round($p['balance'] + $borg, 2);
    $y = booking_confirmation_pdf_poa_arrival_box(
        $pdf,
        $innerL,
        $y,
        $innerR - $innerL,
        booking_confirmation_pdf_money($totArr),
        $borg > 0
    );

    $rectBottom = $ry + $cardH;
    $contentBottom = max($rectBottom, $y + 4.0);
    if ($layoutMeta !== null) {
        $layoutMeta['cardAlignBottom'] = $contentBottom;
    }

    return $y + 4;
}

function booking_confirmation_pdf_merged_borg_arrival_est_height(
    float $innerW,
    string $depNote,
    string $arrivalUur,
    string $siteAddr,
    bool $borgPositive
): float {
    $len = static function (string $s): int {
        return function_exists('mb_strlen') ? (int) mb_strlen($s, 'UTF-8') : strlen($s);
    };
    $charsPerLine = max(14, (int) ($innerW / 2.0));
    $pad = 5.0;
    $h = $pad + 4 + 6 + 8;
    if ($borgPositive) {
        $h += 10;
    }
    $h += max(1, (int) ceil($len($depNote) / $charsPerLine)) * 4.2;
    $h += 11 + 6 + 8;
    $h += max(1, (int) ceil($len($arrivalUur) / $charsPerLine)) * 4;
    $h += 8 + 4 + max(1, (int) ceil($len($siteAddr) / $charsPerLine)) * 4;
    $h += $pad + 8;

    return $h;
}

/**
 * Borg + aankomst in one column under boat image (POA); matches client PDF merged block.
 */
function booking_confirmation_pdf_draw_merged_borg_arrival_column(
    FPDF $pdf,
    float $boxX,
    float $boxY,
    float $colW,
    float $boxH,
    float $borg,
    string $depNote,
    string $arrivalHourLine,
    string $siteAddr
): void {
    $pad = 5.0;
    $textX = $boxX + $pad + 1;
    $innerW = max(20.0, $colW - 2 * $pad - 2);
    $pdf->SetFillColor(243, 243, 249);
    $pdf->Rect($boxX, $boxY, $colW, $boxH, 'F');
    $pdf->SetDrawColor(194, 198, 209);
    $pdf->SetLineWidth(0.2);
    $pdf->Rect($boxX, $boxY, $colW, $boxH, 'D');
    $pdf->SetDrawColor(0, 49, 94);
    $pdf->SetLineWidth(0.7);
    $pdf->Line($boxX, $boxY, $boxX, $boxY + $boxH);

    $yy = $boxY + $pad + 4;
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->SetTextColor(0, 49, 94);
    $pdf->SetXY($textX, $yy);
    $pdf->Cell($innerW, 6, booking_confirmation_pdf_s('Borg bij aankomst'), 0, 1, 'L');
    $yy = $pdf->GetY() + 1;
    if ($borg > 0) {
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->SetXY($textX, $yy);
        $pdf->Cell($innerW, 8, booking_confirmation_pdf_s(booking_confirmation_pdf_money($borg)), 0, 1, 'L');
        $yy = $pdf->GetY() + 2;
    }
    $pdf->SetFont('Helvetica', '', 8.5);
    $pdf->SetTextColor(66, 71, 80);
    $pdf->SetXY($textX, $yy);
    $pdf->MultiCell($innerW, 4, booking_confirmation_pdf_s($depNote), 0, 'L');
    $yy = $pdf->GetY() + 5;
    $pdf->SetDrawColor(229, 231, 235);
    $pdf->SetLineWidth(0.35);
    $pdf->Line($textX, $yy, $boxX + $colW - $pad, $yy);
    $yy += 6;
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->SetTextColor(0, 49, 94);
    $pdf->SetXY($textX, $yy);
    $pdf->Cell($innerW, 6, booking_confirmation_pdf_s('Aankomst'), 0, 1, 'L');
    $yy = $pdf->GetY() + 2;
    $pdf->SetFont('Helvetica', 'B', 6.5);
    $pdf->SetTextColor(66, 71, 80);
    $pdf->SetXY($textX, $yy);
    $pdf->Cell($innerW, 4, booking_confirmation_pdf_s('AANKOMST / CHECK-IN'), 0, 1, 'L');
    $yy += 4;
    $pdf->SetFont('Helvetica', '', 9);
    $pdf->SetTextColor(25, 28, 32);
    $pdf->SetXY($textX, $yy);
    $pdf->MultiCell($innerW, 4, booking_confirmation_pdf_s($arrivalHourLine), 0, 'L');
    $yy = $pdf->GetY() + 3;
    $pdf->SetFont('Helvetica', 'B', 6.5);
    $pdf->SetTextColor(66, 71, 80);
    $pdf->SetXY($textX, $yy);
    $pdf->Cell($innerW, 4, booking_confirmation_pdf_s('LOCATIE'), 0, 1, 'L');
    $yy += 4;
    $pdf->SetFont('Helvetica', '', 9);
    $pdf->SetXY($textX, $yy);
    $pdf->MultiCell($innerW, 4, booking_confirmation_pdf_s($siteAddr), 0, 'L');
}

/**
 * Server-side PDF aligned with payment-success.js layout (hero, breakdown, borg/arrival, policy, footer).
 *
 * @param array<string, mixed> $booking
 * @return string|null PDF binary or null on failure
 */
function build_booking_confirmation_pdf_bytes(array $booking): ?string {
    try {
        $m = 18.0;
        $pageW = 210.0;
        $pageH = 297.0;
        $maxW = $pageW - 2 * $m;
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(true, 16);
        $pdf->AddPage();
        $pdf->SetTitle(booking_confirmation_pdf_s('Nijenhuis reservering'));

        $pdf->SetFillColor(249, 249, 255);
        $pdf->Rect(0, 0, $pageW, $pageH, 'F');

        $id = (string)($booking['id'] ?? '');
        $boat = booking_confirmation_pdf_resolve_boat_name($booking);
        $date = $booking['date'] ?? '';
        $dateStr = $date ? date('d-m-Y', strtotime((string)$date)) : '-';
        $endDate = isset($booking['endDate']) ? strtotime((string)$booking['endDate']) : false;
        $startTs = $date ? strtotime((string)$date) : false;
        if ($endDate && $startTs && date('Y-m-d', $endDate) !== date('Y-m-d', $startTs)) {
            $dateStr .= ' t/m ' . date('d-m-Y', $endDate);
        }
        $arrival = (string)($booking['arrivalTime'] ?? '-');
        $days = (int)($booking['numberOfDays'] ?? 1);
        $site = defined('SITE_NAME') ? (string)SITE_NAME : 'Nijenhuis Botenverhuur';
        $siteAddr = defined('SITE_ADDRESS') && defined('SITE_POSTAL') && defined('SITE_COUNTRY')
            ? trim((string)SITE_ADDRESS . ', ' . (string)SITE_POSTAL . ' ' . (string)SITE_COUNTRY)
            : 'Veneweg 199, 7946 LP Wanneperveen';

        $topY = 12.0;
        $badgeW = 34.0;
        $badgeH = 15.0;
        $pdf->SetFillColor(0, 51, 102);
        $pdf->Rect($m, $topY, $badgeW, $badgeH, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->Text($m + 6, $topY + 9, 'NIJENHUIS');

        $pdf->SetFont('Helvetica', 'B', 14);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetXY($m, $topY + 4);
        $pdf->Cell($maxW, 6, booking_confirmation_pdf_s('Reserveringsbewijs - ' . $site), 0, 1, 'R');
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetTextColor(66, 71, 80);
        $pdf->SetX($m);
        $pdf->Cell($maxW, 4, booking_confirmation_pdf_s('Reserveringsnummer: ' . $id), 0, 1, 'R');
        $pdf->SetFont('Helvetica', '', 7.5);
        $pdf->SetX($m);
        $pdf->Cell($maxW, 4, booking_confirmation_pdf_s('Aangemaakt op: ' . date('Y-m-d')), 0, 1, 'R');

        $pdf->SetTextColor(25, 28, 32);
        $y = $topY + $badgeH + 11;
        $colGap = 7.0;

        $poaBreak = booking_confirmation_poa_breakdown($booking);
        $rental = isset($booking['rentalAmount']) ? (float)$booking['rentalAmount'] : null;
        $admin = isset($booking['administrationFee']) ? (float)$booking['administrationFee'] : null;
        $total = (float)($booking['amount'] ?? 0);
        $borg = booking_confirmation_security_deposit_euros($booking);
        $durSub = $days . ' ' . ($days === 1 ? 'dag' : 'dagen');
        $willPoaHero = $poaBreak !== null || !empty($booking['payOnArrivalReservation']);

        $heroPoaInColumn = false;
        $poaCardParams = null;
        if ($poaBreak !== null) {
            $heroPoaInColumn = true;
            $poaCardParams = [
                'rental' => $poaBreak['rental'],
                'admin' => $poaBreak['admin'],
                'rr' => $poaBreak['rr'],
                'rad' => $poaBreak['rad'],
                'reservationFee' => $poaBreak['reservationFee'],
                'balance' => $poaBreak['balance'],
                'showSlice' => $poaBreak['showSlice'],
            ];
        } elseif (!empty($booking['payOnArrivalReservation'])) {
            $heroPoaInColumn = true;
            $rrP = isset($booking['reservationFeeRentalPortion']) ? (float)$booking['reservationFeeRentalPortion'] : 0.0;
            $radP = isset($booking['reservationFeeAdminOnReservation']) ? (float)$booking['reservationFeeAdminOnReservation'] : 0.0;
            $showSliceP = $rrP > 0 || $radP > 0;
            $poaCardParams = [
                'rental' => $rental ?? 0.0,
                'admin' => $admin ?? 0.0,
                'rr' => $rrP,
                'rad' => $radP,
                'reservationFee' => (float)($booking['reservationFee'] ?? 0),
                'balance' => (float)($booking['balanceDueOnArrival'] ?? 0),
                'showSlice' => $showSliceP,
            ];
        }

        $heroImgW = $poaCardParams !== null ? $maxW * 0.47 : $maxW * 0.56;
        $heroImgH = $heroImgW * ($poaCardParams !== null ? 0.68 : 0.75);
        $imgX = $m;
        $imgY = $y;

        $pdf->SetFillColor(237, 237, 243);
        $pdf->Rect($imgX, $imgY, $heroImgW, $heroImgH, 'F');
        $boatPath = booking_confirmation_boat_image_local_path($booking);
        if ($boatPath !== null) {
            try {
                $pdf->Image($boatPath, $imgX, $imgY, $heroImgW, $heroImgH);
            } catch (Throwable $e) {
                error_log('booking PDF boat image: ' . $e->getMessage());
            }
        }

        $pdf->SetFillColor(255, 219, 200);
        $chipPad = 3.5;
        $chipW = min($heroImgW - 4, 80);
        $chipH = 12;
        $chipX = $imgX + $heroImgW - $chipW - 3;
        $chipY = $imgY + $heroImgH - $chipH - 2;
        $pdf->Rect($chipX, $chipY, $chipW, $chipH, 'F');
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->SetTextColor(50, 19, 0);
        $pdf->SetXY($chipX + $chipPad, $chipY + 4);
        $chipBoat = function_exists('mb_substr') ? mb_substr($boat, 0, 42, 'UTF-8') : substr($boat, 0, 42);
        $pdf->Cell($chipW - 2 * $chipPad, 4, booking_confirmation_pdf_s($chipBoat), 0, 1, 'L');

        $rx = $m + $heroImgW + $colGap;

        $poaPriceCardBottomY = null;
        if ($poaCardParams !== null) {
            $cardW = $maxW - $heroImgW - $colGap;
            $tripLong = booking_confirmation_format_date_long_nl($booking['date'] ?? null, $booking['endDate'] ?? null);
            $poaLayout = [];
            $y = booking_confirmation_pdf_render_poa_price_card($pdf, $rx, $imgY, $cardW, $borg, $poaCardParams, $durSub, $tripLong, $poaLayout);
            $poaPriceCardBottomY = isset($poaLayout['cardAlignBottom']) ? (float) $poaLayout['cardAlignBottom'] : $imgY + booking_confirmation_pdf_poa_card_height(
                $poaCardParams,
                $borg,
                $durSub !== '',
                $tripLong
            );
            $heroVisualBottom = max($imgY + $heroImgH, $y, $poaPriceCardBottomY);
        } else {
            $ry = $imgY + 5;
            $pdf->SetFont('Helvetica', 'B', 7);
            $pdf->SetTextColor(78, 95, 121);
            $pdf->Text($rx, $ry, booking_confirmation_pdf_s('DATUM VAN VAART'));
            $ry += 5;
            $pdf->SetFont('Helvetica', 'B', 14);
            $pdf->SetTextColor(0, 49, 94);
            $pdf->Text($rx, $ry, booking_confirmation_pdf_s($dateStr));
            $ry += 9;

            if (!$willPoaHero) {
                $pdf->SetFont('Helvetica', '', 8);
                $pdf->SetTextColor(66, 71, 80);
                $pdf->Text($rx, $ry, booking_confirmation_pdf_s($durSub));
                $ry += 6;
            }

            $heroTotal = $total;
            $pdf->SetFont('Helvetica', 'B', 7);
            $pdf->SetTextColor(78, 95, 121);
            $pdf->Text($rx, $ry, booking_confirmation_pdf_s('TOTAALBEDRAG'));
            $ry += 8;
            $pdf->SetFont('Helvetica', 'B', 22);
            $pdf->SetTextColor(0, 49, 94);
            $pdf->Text($rx, $ry, booking_confirmation_pdf_money($heroTotal));
            $ry += 11;
            $pdf->SetFont('Helvetica', '', 8);
            $pdf->SetTextColor(66, 71, 80);
            $pdf->SetXY($rx, $ry);
            $pdf->MultiCell($maxW - $heroImgW - $colGap, 4, booking_confirmation_pdf_s('Huur inclusief BTW, betaald via Mollie.'));
            $ry = $pdf->GetY();
            $heroVisualBottom = max($imgY + $heroImgH, $ry + 6);
        }

        $y = $heroVisualBottom + 8;

        if (!$heroPoaInColumn && $rental !== null && $admin !== null) {
            $pdf->SetDrawColor(194, 198, 209);
            $pdf->Line($m, $y, $pageW - $m, $y);
            $y += 6;
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetTextColor(0, 51, 102);
            $pdf->SetXY($m, $y);
            $pdf->Cell($maxW, 6, booking_confirmation_pdf_s('Prijsoverzicht'), 0, 1, 'L');
            $y += 7;
            $y = booking_confirmation_pdf_poa_row($pdf, $m, $pageW, $y, 'Huur', booking_confirmation_pdf_money($rental));
            $y = booking_confirmation_pdf_poa_row($pdf, $m, $pageW, $y, 'Totaal', booking_confirmation_pdf_money($total), true);
            $y += 6;
        } elseif (!$heroPoaInColumn) {
            $pdf->SetDrawColor(194, 198, 209);
            $pdf->Line($m, $y, $pageW - $m, $y);
            $y += 6;
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY($m, $y);
            $pdf->Cell($maxW, 6, booking_confirmation_pdf_s('Prijsoverzicht'), 0, 1, 'L');
            $y += 7;
            $y = booking_confirmation_pdf_poa_row($pdf, $m, $pageW, $y, 'Totaalprijs', booking_confirmation_pdf_money($total), true);
            $y += 6;
        }

        $pad = 5.0;
        $depNote = $borg > 0
            ? ('Let op: voor de gehuurde boot dient een borg van ' . number_format($borg, 2, ',', '.') . ' EUR contant te worden voldaan bij aankomst.')
            : 'Geen borg vereist voor dit boottype.';

        if ($poaCardParams !== null) {
            $underY = $imgY + $heroImgH + 4;
            /** Align merged column bottom to POA price card bottom (not hero image bottom). */
            $rightPanelBottomY = $poaPriceCardBottomY !== null ? (float) $poaPriceCardBottomY : $underY;
            $voidFillH = max(0.0, $rightPanelBottomY - $underY);
            $innerW = $heroImgW - 2 * $pad - 2;
            $arrivalLine = $arrival . ' uur';
            $contentH = booking_confirmation_pdf_merged_borg_arrival_est_height(
                $innerW,
                $depNote,
                $arrivalLine,
                $siteAddr,
                $borg > 0
            );
            $mergeY = $underY;
            $boxH = max($contentH, $voidFillH, 48.0);
            if ($mergeY + $boxH > $pageH - 50) {
                $pdf->AddPage();
                $pdf->SetFillColor(249, 249, 255);
                $pdf->Rect(0, 0, $pageW, $pageH, 'F');
                $mergeY = $m;
                /** Price card is on previous page — only fit content. */
                $boxH = max($contentH, 48.0);
            } else {
                $boxH = max($contentH, 48.0, max(0.0, $rightPanelBottomY - $mergeY));
            }
            booking_confirmation_pdf_draw_merged_borg_arrival_column(
                $pdf,
                $imgX,
                $mergeY,
                $heroImgW,
                $boxH,
                $borg,
                $depNote,
                $arrivalLine,
                $siteAddr
            );
            $y = max($heroVisualBottom, $mergeY + $boxH) + 10;
        } else {
            $half = ($maxW - $colGap) / 2;
            $leftX = $m;
            $rightX = $m + $half + $colGap;
            $leftH = $pad + 16 + ($borg > 0 ? 10 : 4) + 12;
            $rightH = $pad + 28;
            $rowH = max($leftH, $rightH, 40);

            if ($y + $rowH > $pageH - 50) {
                $pdf->AddPage();
                $pdf->SetFillColor(249, 249, 255);
                $pdf->Rect(0, 0, $pageW, $pageH, 'F');
                $y = $m;
            }

            $pdf->SetFillColor(243, 243, 249);
            $pdf->Rect($leftX, $y, $half, $rowH, 'F');
            $pdf->SetDrawColor(0, 49, 94);
            $pdf->SetLineWidth(0.7);
            $pdf->Line($leftX, $y, $leftX, $y + $rowH);

            $yy = $y + $pad + 4;
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetTextColor(0, 49, 94);
            $pdf->SetXY($leftX + $pad, $yy);
            $pdf->Cell($half - 2 * $pad, 6, booking_confirmation_pdf_s('Borg bij aankomst'), 0, 1, 'L');
            $yy += 8;
            if ($borg > 0) {
                $pdf->SetFont('Helvetica', 'B', 16);
                $pdf->SetTextColor(0, 49, 94);
                $pdf->SetXY($leftX + $pad, $yy);
                $pdf->Cell($half - 2 * $pad, 8, booking_confirmation_pdf_s(booking_confirmation_pdf_money($borg)), 0, 1, 'L');
                $yy += 10;
            }
            $pdf->SetFont('Helvetica', '', 8.5);
            $pdf->SetTextColor(66, 71, 80);
            $pdf->SetXY($leftX + $pad, $yy);
            $pdf->MultiCell($half - 2 * $pad, 4, booking_confirmation_pdf_s($depNote));

            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetDrawColor(194, 198, 209);
            $pdf->SetLineWidth(0.2);
            $pdf->Rect($rightX, $y, $half, $rowH, 'FD');

            $yy = $y + $pad + 4;
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetTextColor(0, 49, 94);
            $pdf->SetXY($rightX + $pad, $yy);
            $pdf->Cell($half - 2 * $pad, 6, booking_confirmation_pdf_s('Aankomst'), 0, 1, 'L');
            $yy += 8;
            $pdf->SetFont('Helvetica', 'B', 6.5);
            $pdf->SetTextColor(66, 71, 80);
            $pdf->SetXY($rightX + $pad, $yy);
            $pdf->Cell($half - 2 * $pad, 4, booking_confirmation_pdf_s('AANKOMST / CHECK-IN'), 0, 1, 'L');
            $yy += 4;
            $pdf->SetFont('Helvetica', '', 9);
            $pdf->SetTextColor(25, 28, 32);
            $pdf->SetXY($rightX + $pad, $yy);
            $pdf->MultiCell($half - 2 * $pad, 4, booking_confirmation_pdf_s($arrival . ' uur'));
            $yy += 12;
            $pdf->SetFont('Helvetica', 'B', 6.5);
            $pdf->SetTextColor(66, 71, 80);
            $pdf->SetXY($rightX + $pad, $yy);
            $pdf->Cell($half - 2 * $pad, 4, booking_confirmation_pdf_s('LOCATIE'), 0, 1, 'L');
            $yy += 4;
            $pdf->SetFont('Helvetica', '', 9);
            $pdf->SetXY($rightX + $pad, $yy);
            $pdf->MultiCell($half - 2 * $pad, 4, booking_confirmation_pdf_s($siteAddr));

            $y += $rowH + 10;
        }

        $cw = ($maxW - 10) / 3;
        $pdf->SetDrawColor(194, 198, 209);
        $pdf->SetLineWidth(0.15);
        $pdf->Line($m, $y, $pageW - $m, $y);
        $y += 8;
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetTextColor(0, 49, 94);
        $pdf->Text($m, $y, booking_confirmation_pdf_s('VOORWAARDEN & PRAKTISCH'));
        $y += 7;

        $cancelPoa = !empty($booking['payOnArrivalReservation'])
            ? 'De online betaalde reserveringsbijdrage is niet-restitueerbaar.'
            : 'Annuleringsbeleid: informeer tijdig. Bij annulering kan een vergoeding van toepassing zijn.';
        $bring = 'Zonbescherming en comfortabele kleding. Borg contant bij aankomst volgens reservering.';
        $practical = (defined('SITE_PHONE') ? 'Tel. ' . SITE_PHONE . '. ' : '') . $siteAddr;
        $cols = [
            [$cancelPoa, $bring, $practical],
        ];
        $titles = ['Annuleringsbeleid', 'Wat mee te nemen', 'Contact & locatie'];
        $bodies = $cols[0];
        $cBottom = $y;
        for ($i = 0; $i < 3; $i++) {
            $cx = $m + $i * ($cw + 5);
            $pdf->SetFont('Helvetica', 'B', 9);
            $pdf->SetTextColor(25, 28, 32);
            $pdf->SetXY($cx, $y);
            $pdf->MultiCell($cw, 4, booking_confirmation_pdf_s($titles[$i]));
            $cy = $y + 6;
            $pdf->SetFont('Helvetica', '', 8);
            $pdf->SetTextColor(66, 71, 80);
            $pdf->SetXY($cx, $cy);
            $pdf->MultiCell($cw, 3.8, booking_confirmation_pdf_s($bodies[$i]));
            $cBottom = max($cBottom, $cy + 18);
        }
        $y = $cBottom + 8;

        $wish = 'Wij wensen u een behouden vaart en een fijne dag op het water in de Weerribben!';
        $fh = 14;
        if ($y + $fh > $pageH - 14) {
            $pdf->AddPage();
            $pdf->SetFillColor(249, 249, 255);
            $pdf->Rect(0, 0, $pageW, $pageH, 'F');
            $y = $m;
        }
        $pdf->SetFillColor(0, 72, 132);
        $pdf->Rect($m, $y, $maxW, $fh, 'F');
        $pdf->SetTextColor(190, 215, 255);
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->SetXY($m + 4, $y + 5);
        $pdf->MultiCell($maxW - 8, 4.5, booking_confirmation_pdf_s($wish));
        $y += $fh + 8;

        $pdf->SetDrawColor(210, 210, 220);
        $pdf->Line($m, $y, $pageW - $m, $y);
        $pdf->SetFont('Helvetica', '', 7.5);
        $pdf->SetTextColor(114, 119, 129);
        $foot = (defined('SITE_URL') ? preg_replace('#^https?://#', '', (string)SITE_URL) : 'nijenhuis-botenverhuur.com');
        if (defined('SITE_PHONE')) {
            $foot .= '  |  Tel. ' . SITE_PHONE;
        }
        $pdf->SetXY($m, $y + 3);
        $pdf->Cell($maxW, 4, booking_confirmation_pdf_s($foot), 0, 1, 'C');

        $out = $pdf->Output('S');
        return is_string($out) ? $out : null;
    } catch (Throwable $e) {
        error_log('build_booking_confirmation_pdf_bytes: ' . $e->getMessage());
        return null;
    }
}
