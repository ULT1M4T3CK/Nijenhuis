<?php
require_once __DIR__ . '/data_paths.php';
/**
 * Shared booking confirmation email
 * Used for both online (paid) and manual bookings.
 * Emails are sent from no-reply@nijenhuis-botenverhuur.com (via MS_GRAPH_MAILBOX).
 *
 * @param array $booking Booking data
 * @param bool $isPaidOnline True for Mollie-paid bookings, false for manual (no payment mention)
 * @return bool Success
 */
/**
 * Absolute boat image URL for HTML email (many clients block relative URLs).
 */
function booking_confirmation_boat_image_abs_url(?string $boatTypeId): string {
    $boatTypeId = (string)$boatTypeId;
    $boatsFile = nijenhuis_data_path('boats.json');
    if (!is_readable($boatsFile)) {
        return '';
    }
    $boats = json_decode((string)file_get_contents($boatsFile), true);
    if (!is_array($boats)) {
        return '';
    }
    foreach ($boats as $b) {
        if (($b['id'] ?? '') !== $boatTypeId) {
            continue;
        }
        $rel = (string)($b['headerImage'] ?? $b['image'] ?? '');
        if ($rel === '') {
            return '';
        }
        $rel = preg_replace('#^\.\./#', '/', $rel);
        if ($rel !== '' && $rel[0] !== '/') {
            $rel = '/' . $rel;
        }
        return 'https://nijenhuis-botenverhuur.com' . $rel;
    }
    return '';
}

function sendBookingConfirmationEmail($booking, $isPaidOnline = true) {
    if (!function_exists('sendGraphMail')) {
        require_once __DIR__ . '/microsoft_graph_mail.php';
    }
    require_once __DIR__ . '/booking_confirmation_pdf.php';

    $to = $booking['customerEmail'] ?? '';
    if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        error_log('Booking confirmation email: invalid or missing customerEmail for booking ' . ($booking['id'] ?? 'unknown'));
        return false;
    }

    $subject = 'Boeking Bevestigd - Nijenhuis Botenverhuur';

    // Get boat name
    $boatName = $booking['boatType'] ?? 'Onbekend';
    $boatsFile = nijenhuis_data_path('boats.json');
    if (file_exists($boatsFile)) {
        $boats = json_decode(file_get_contents($boatsFile), true);
        if (is_array($boats)) {
            foreach ($boats as $b) {
                if (($b['id'] ?? '') === ($booking['boatType'] ?? '')) {
                    $boatName = $b['name'] ?? $boatName;
                    break;
                }
            }
        }
    }

    $date = date('d-m-Y', strtotime($booking['date'] ?? 'now'));
    $endDate = isset($booking['endDate']) ? date('d-m-Y', strtotime($booking['endDate'])) : $date;
    $dateStr = ($date === $endDate) ? $date : "$date tot $endDate";
    $dateStrEn = ($date === $endDate) ? $date : "$date to $endDate";

    $name = htmlspecialchars($booking['customerName'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $amount = number_format((float)($booking['amount'] ?? 0), 2, ',', '.');
    $boatNameEsc = htmlspecialchars($boatName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $rentalRowNl = '';
    $rentalRowEn = '';
    if (isset($booking['rentalAmount'])) {
        $rAmt = number_format((float)$booking['rentalAmount'], 2, ',', '.');
        $rentalRowNl = "<tr><td>Huur</td><td>€{$rAmt}</td></tr>";
        $rentalRowEn = "<tr><td>Rental</td><td>€{$rAmt}</td></tr>";
    }
    $arrivalTime = htmlspecialchars($booking['arrivalTime'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $notes = htmlspecialchars($booking['notes'] ?? '-', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $bookingId = htmlspecialchars($booking['id'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $numDays = (int)($booking['numberOfDays'] ?? 1);
    $poaRes = !empty($booking['payOnArrivalReservation']);
    $poaBreak = $poaRes ? booking_confirmation_poa_breakdown($booking) : null;

    $borgNum = $poaRes ? booking_confirmation_security_deposit_euros($booking) : 0.0;
    $borg = number_format($borgNum, 2, ',', '.');

    $dateNlLong = htmlspecialchars(
        booking_confirmation_format_date_long_nl($booking['date'] ?? null, $booking['endDate'] ?? null),
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    );
    $dateEnLong = htmlspecialchars(
        booking_confirmation_format_date_long_en($booking['date'] ?? null, $booking['endDate'] ?? null),
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    );

    $priceBlockNl = '';
    $priceBlockEn = '';
    $poaCalcNl = '';
    $poaCalcEn = '';
    $poaTotalNl = '';
    $poaTotalEn = '';
    if ($poaBreak !== null) {
        $totCashNum = round($poaBreak['balance'] + $borgNum, 2);
        $totCash = number_format($totCashNum, 2, ',', '.');
        $borgRowNl = $borgNum > 0
            ? "<tr><td style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;width:58%;'>Borg (contant bij aankomst)</td><td style='padding:8px 0;border-bottom:1px solid #eee;text-align:right;'>€{$borg}</td></tr>"
                . "<tr><td style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;'><strong>Totaal contant bij aankomst (huur + borg)</strong></td><td style='padding:8px 0;border-bottom:1px solid #eee;text-align:right;'><strong>€{$totCash}</strong></td></tr>"
            : "<tr><td style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;'>Borg (contant bij aankomst)</td><td style='padding:8px 0;border-bottom:1px solid #eee;'>Geen volgens boottype</td></tr>";
        $borgRowEn = $borgNum > 0
            ? "<tr><td style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;width:58%;'>Security deposit (cash on arrival)</td><td style='padding:8px 0;border-bottom:1px solid #eee;text-align:right;'>€{$borg}</td></tr>"
                . "<tr><td style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;'><strong>Total cash on arrival (rental + deposit)</strong></td><td style='padding:8px 0;border-bottom:1px solid #eee;text-align:right;'><strong>€{$totCash}</strong></td></tr>"
            : "<tr><td style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;'>Security deposit (cash on arrival)</td><td style='padding:8px 0;border-bottom:1px solid #eee;'>None for this boat type</td></tr>";

        $rFmt = number_format($poaBreak['rental'], 2, ',', '.');
        $rfFmt = number_format($poaBreak['reservationFee'], 2, ',', '.');
        $balFmt = number_format($poaBreak['balance'], 2, ',', '.');
        $tdL = "style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;width:58%;'";
        $tdR = "style='padding:8px 0;border-bottom:1px solid #eee;text-align:right;'";
        $poaCalcNl = "<tr><td {$tdL}>Huur</td><td {$tdR}>€{$rFmt}</td></tr>"
            . "<tr><td {$tdL}>Reserveringsbijdrage (betaald, niet-restitueerbaar)</td><td {$tdR}>€{$rfFmt}</td></tr>"
            . "<tr><td {$tdL}>Nog te betalen bij aankomst</td><td {$tdR}>€{$balFmt}</td></tr>"
            . $borgRowNl;
        $poaCalcEn = "<tr><td {$tdL}>Rental</td><td {$tdR}>€{$rFmt}</td></tr>"
            . "<tr><td {$tdL}>Reservation fee (paid, non-refundable)</td><td {$tdR}>€{$rfFmt}</td></tr>"
            . "<tr><td {$tdL}>Due on arrival</td><td {$tdR}>€{$balFmt}</td></tr>"
            . $borgRowEn;
        $poaTotalNl = '€' . $rFmt;
        $poaTotalEn = '€' . $rFmt;
    } elseif ($poaRes) {
        $rf = number_format((float)($booking['reservationFee'] ?? 0), 2, ',', '.');
        $balNum = round((float)($booking['balanceDueOnArrival'] ?? 0), 2);
        $bal = number_format($balNum, 2, ',', '.');
        $totCash = number_format(round($balNum + $borgNum, 2), 2, ',', '.');
        $borgRowNl = $borgNum > 0
            ? "<tr><td style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;'>Borg (contant bij aankomst)</td><td style='padding:8px 0;border-bottom:1px solid #eee;text-align:right;'>€{$borg}</td></tr>"
                . "<tr><td style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;'><strong>Totaal contant bij aankomst (huur + borg)</strong></td><td style='padding:8px 0;border-bottom:1px solid #eee;text-align:right;'><strong>€{$totCash}</strong></td></tr>"
            : "<tr><td style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;'>Borg (contant bij aankomst)</td><td style='padding:8px 0;border-bottom:1px solid #eee;'>Geen volgens boottype</td></tr>";
        $borgRowEn = $borgNum > 0
            ? "<tr><td style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;'>Security deposit (cash on arrival)</td><td style='padding:8px 0;border-bottom:1px solid #eee;text-align:right;'>€{$borg}</td></tr>"
                . "<tr><td style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;'><strong>Total cash on arrival (rental + deposit)</strong></td><td style='padding:8px 0;border-bottom:1px solid #eee;text-align:right;'><strong>€{$totCash}</strong></td></tr>"
            : "<tr><td style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;'>Security deposit (cash on arrival)</td><td style='padding:8px 0;border-bottom:1px solid #eee;'>None for this boat type</td></tr>";
        $tdL = "style='padding:8px 0;border-bottom:1px solid #eee;font-weight:bold;color:#555;width:58%;'";
        $tdR = "style='padding:8px 0;border-bottom:1px solid #eee;text-align:right;'";
        $rentNl = '';
        $rentEn = '';
        if (isset($booking['rentalAmount'])) {
            $rAmtFb = number_format((float)$booking['rentalAmount'], 2, ',', '.');
            $rentNl = "<tr><td {$tdL}>Huur</td><td {$tdR}>€{$rAmtFb}</td></tr>";
            $rentEn = "<tr><td {$tdL}>Rental</td><td {$tdR}>€{$rAmtFb}</td></tr>";
        }
        $poaCalcNl = $rentNl
            . "<tr><td {$tdL}>Reserveringsbijdrage (betaald, niet-restitueerbaar)</td><td {$tdR}>€{$rf}</td></tr>"
            . "<tr><td {$tdL}>Nog te betalen bij aankomst</td><td {$tdR}>€{$bal}</td></tr>"
            . $borgRowNl;
        $poaCalcEn = $rentEn
            . "<tr><td {$tdL}>Reservation fee (paid, non-refundable)</td><td {$tdR}>€{$rf}</td></tr>"
            . "<tr><td {$tdL}>Due on arrival</td><td {$tdR}>€{$bal}</td></tr>"
            . $borgRowEn;
        $poaTotalNl = '€' . $amount;
        $poaTotalEn = '€' . $amount;
    } else {
        $priceBlockNl = $rentalRowNl . "<tr><td>Totaalprijs</td><td>€{$amount}</td></tr>";
        $priceBlockEn = $rentalRowEn . "<tr><td>Total Price</td><td>€{$amount}</td></tr>";
    }

    $poaHeroNl = '';
    $poaHeroEn = '';
    if ($poaRes && $poaCalcNl !== '') {
        $boatImgAbs = booking_confirmation_boat_image_abs_url($booking['boatType'] ?? '');
        $boatImgUrlEsc = htmlspecialchars($boatImgAbs, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $imgCell = $boatImgAbs !== ''
            ? "<img src=\"{$boatImgUrlEsc}\" alt=\"{$boatNameEsc}\" width=\"240\" style=\"width:100%;max-width:240px;height:auto;border-radius:8px;display:block;border:0;\" />"
            : "<div style=\"width:100%;max-width:240px;height:160px;background:#e2e8f0;border-radius:8px;\"></div>";
        $poaHeroNl = "
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='margin:20px 0;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;border-collapse:collapse;'>
                <tr>
                    <td valign='top' style='width:42%;background:#f8fafc;padding:16px;'>
                        {$imgCell}
                        <p style='margin:12px 0 0;font-size:15px;font-weight:bold;color:#1e293b;'>{$boatNameEsc}</p>
                        <p style='margin:4px 0 0;font-size:14px;color:#64748b;'>{$dateStr}</p>
                    </td>
                    <td valign='top' style='padding:16px 18px;'>
                        <p style='margin:0 0 4px;font-size:26px;font-weight:bold;color:#003366;line-height:1.2;'>{$poaTotalNl}</p>
                        <p style='margin:0 0 14px;font-size:14px;color:#64748b;line-height:1.4;'>{$dateNlLong}<br><span style='font-size:13px;'>Totaal (bij aankomst)</span></p>
                        <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;margin:0;'>{$poaCalcNl}</table>
                    </td>
                </tr>
            </table>";
        $poaHeroEn = "
            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='margin:20px 0;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;border-collapse:collapse;'>
                <tr>
                    <td valign='top' style='width:42%;background:#f8fafc;padding:16px;'>
                        {$imgCell}
                        <p style='margin:12px 0 0;font-size:15px;font-weight:bold;color:#1e293b;'>{$boatNameEsc}</p>
                        <p style='margin:4px 0 0;font-size:14px;color:#64748b;'>{$dateStrEn}</p>
                    </td>
                    <td valign='top' style='padding:16px 18px;'>
                        <p style='margin:0 0 4px;font-size:26px;font-weight:bold;color:#003366;line-height:1.2;'>{$poaTotalEn}</p>
                        <p style='margin:0 0 14px;font-size:14px;color:#64748b;line-height:1.4;'>{$dateEnLong}<br><span style='font-size:13px;'>Total (on arrival)</span></p>
                        <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;margin:0;'>{$poaCalcEn}</table>
                    </td>
                </tr>
            </table>";
    }

    if ($poaRes) {
        $footerPoaNl = "<p style='color:#334155;font-size:13px;margin:0 0 8px;line-height:1.5;'>Let op: De online betaalde reserveringsbijdrage (borg) is niet-restitueerbaar.</p>";
        $footerPoaEn = "<p style='color:#334155;font-size:13px;margin:0 0 8px;line-height:1.5;'>Note: The reservation fee paid online (security deposit) is non-refundable.</p>";
        $footerCancelNl = '';
        $footerCancelEn = '';
    } else {
        $footerPoaNl = '';
        $footerPoaEn = '';
        $footerCancelNl = "<p class='warning'>Let op: Een annuleringsvergoeding van 10% van het totaalbedrag is van toepassing.</p>";
        $footerCancelEn = "<p class='warning' style='margin-top: 10px;'>Note: A cancellation fee of 10% of the total amount applies.</p>";
    }

    if ($isPaidOnline && $poaRes) {
        $introNl = 'Bedankt voor je boeking bij Nijenhuis Botenverhuur! We hebben je niet-restitueerbare reserveringsbijdrage ontvangen. Je reservering is bevestigd; het restbedrag betaal je bij aankomst en is verrekend met deze bijdrage.';
        $introEn = 'Thank you for your booking with Nijenhuis Botenverhuur! We have received your non-refundable reservation fee. Your reservation is confirmed; the remaining balance is due on arrival and will be settled after deducting this contribution.';
    } elseif ($isPaidOnline) {
        $introNl = 'Bedankt voor je boeking bij Nijenhuis Botenverhuur! Je betaling is succesvol ontvangen en je reservering is voltooid.';
        $introEn = 'Thank you for your booking with Nijenhuis Botenverhuur! Your payment has been successfully received and your reservation is confirmed.';
    } else {
        $introNl = 'Bedankt voor je boeking bij Nijenhuis Botenverhuur! Je reservering is bevestigd.';
        $introEn = 'Thank you for your booking with Nijenhuis Botenverhuur! Your reservation is confirmed.';
    }

    if ($poaRes) {
        $detailsTableNl = $poaHeroNl . "
            <table class='details-table'>
                <tr><td>Aankomsttijd</td><td>$arrivalTime</td></tr>
                <tr><td>Aantal Dagen</td><td>$numDays</td></tr>
                <tr><td>Opmerkingen</td><td>$notes</td></tr>
            </table>";
        $detailsTableEn = $poaHeroEn . "
            <table class='details-table'>
                <tr><td>Arrival Time</td><td>$arrivalTime</td></tr>
                <tr><td>Number of Days</td><td>$numDays</td></tr>
                <tr><td>Notes</td><td>$notes</td></tr>
            </table>";
    } else {
        $detailsTableNl = "
            <table class='details-table'>
                <tr><td>Boot</td><td>$boatNameEsc</td></tr>
                <tr><td>Datum</td><td>$dateStr</td></tr>
                <tr><td>Aankomsttijd</td><td>$arrivalTime</td></tr>
                <tr><td>Aantal Dagen</td><td>$numDays</td></tr>
                $priceBlockNl
                <tr><td>Opmerkingen</td><td>$notes</td></tr>
            </table>";
        $detailsTableEn = "
            <table class='details-table'>
                <tr><td>Boat</td><td>$boatNameEsc</td></tr>
                <tr><td>Date</td><td>$dateStrEn</td></tr>
                <tr><td>Arrival Time</td><td>$arrivalTime</td></tr>
                <tr><td>Number of Days</td><td>$numDays</td></tr>
                $priceBlockEn
                <tr><td>Notes</td><td>$notes</td></tr>
            </table>";
    }

    $message = "
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; }
        .header { background: #003366; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .details-table td { padding: 12px; border-bottom: 1px solid #eee; }
        .details-table td:first-child { font-weight: bold; width: 40%; color: #555; }
        .footer { background: #f8f9fa; padding: 20px; font-size: 12px; color: #666; text-align: center; border-top: 1px solid #eee; }
        .warning { color: #e53e3e; font-weight: bold; }
        .divider { border-top: 2px solid #003366; margin: 40px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <img src='https://nijenhuis-botenverhuur.com/frontend/Images/logo-white.png' alt='Nijenhuis Botenverhuur' style='height: 60px; margin-bottom: 15px;'>
            <h1 style='color: white; margin: 0; font-size: 24px;'>Boeking Bevestigd</h1>
        </div>
        <div class='content'>
            <p>Beste $name,</p>
            <p>$introNl</p>
            <div style='background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0;'>
                <strong>Reservering ID:</strong> $bookingId
            </div>
            $detailsTableNl
            <p>We kijken ernaar uit je te verwelkomen op onze locatie:</p>
            <p><strong>Veneweg 199<br>7946 LP Wanneperveen</strong></p>
            <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;'>
                <strong>Let op:</strong> Dit e-mailadres wordt niet gemonitord. Wil je wijzigingen doorgeven? Neem dan telefonisch contact met ons op via <strong>+31 522 281 528</strong>.
            </div>
            <div class='divider'></div>
            <p>Dear $name,</p>
            <p>$introEn</p>
            <div style='background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0;'>
                <strong>Booking ID:</strong> $bookingId
            </div>
            $detailsTableEn
            <p>We look forward to welcoming you at our location:</p>
            <p><strong>Veneweg 199<br>7946 LP Wanneperveen</strong></p>
            <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;'>
                <strong>Please note:</strong> This mailbox is not monitored. If you wish to make changes to your booking, please contact us by phone at <strong>+31 522 281 528</strong>.
            </div>
        </div>
        <div class='footer'>
            $footerPoaNl
            $footerCancelNl
            $footerPoaEn
            $footerCancelEn
            <p>&copy; " . date('Y') . " Nijenhuis Botenverhuur.</p>
            <p>Tel: +31 522 281 528</p>
        </div>
    </div>
</body>
</html>
";

    $mailError = null;
    $attachments = [];
    $pdfBytes = build_booking_confirmation_pdf_bytes($booking);
    if ($pdfBytes !== null && $pdfBytes !== '') {
        $safeId = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $booking['id'] ?? 'booking');
        $attachments[] = [
            'name' => 'nijenhuis-reservering-' . $safeId . '.pdf',
            'contentType' => 'application/pdf',
            'content' => $pdfBytes,
        ];
    }

    $sent = sendGraphMail(
        $to,
        $subject,
        $message,
        'HTML',
        'no-reply@nijenhuis-botenverhuur.com',
        [],
        [],
        true,
        null,
        $attachments,
        $mailError
    );

    if (!$sent && $mailError) {
        $errMsg = json_encode($mailError);
        error_log('Booking confirmation email failed: ' . $errMsg);
        error_log('Booking confirmation: check MS_GRAPH_TENANT_ID, MS_GRAPH_CLIENT_ID, MS_GRAPH_CLIENT_SECRET, MS_GRAPH_MAILBOX in .env. See documentation/MICROSOFT_GRAPH_EMAIL.md');
    }

    return $sent;
}
