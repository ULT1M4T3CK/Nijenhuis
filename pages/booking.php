<?php
/**
 * Booking Page - Nijenhuis Botenverhuur
 */
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/../components/csp.php';
nijenhuis_send_csp_header();
$basePath = getBasePath();
$bookingPoaReservationPct = defined('BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT') ? (float) BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT : 10.0;
$bookingPaymentMethods = function_exists('getCheckoutPaymentMethods') ? getCheckoutPaymentMethods() : ['ideal', 'bancontact', 'pay_on_arrival'];
$pageTitle = 'Boek je boot';
$pageDescription = 'Boek je boot bij Nijenhuis Botenverhuur. Voltooi je reservering voor een perfecte dag op het water in de Weerribben.';
$canonicalUrl = SITE_URL . '/booking';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <link rel="alternate" hreflang="x-default" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <link rel="alternate" hreflang="nl" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <link rel="alternate" hreflang="en" href="<?php echo htmlspecialchars($canonicalUrl); ?>?lang=en">
    <link rel="alternate" hreflang="de" href="<?php echo htmlspecialchars($canonicalUrl); ?>?lang=de">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle . ' - ' . SITE_NAME); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo SITE_URL . assetPath('frontend/Images/banner-img.jpg'); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle . ' - ' . SITE_NAME); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo SITE_URL . assetPath('frontend/Images/banner-img.jpg'); ?>">
    <meta name="twitter:image:alt" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <link rel="icon" type="image/svg+xml" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('pages/pages-consolidated.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/pages/booking.css'); ?>">
    <script>window.CHECKOUT_ADMIN_FEE_PERCENT=<?php echo json_encode((float) BOOKING_ADMIN_FEE_PERCENT); ?>;window.CHECKOUT_POA_RESERVATION_FEE_PERCENT=<?php echo json_encode($bookingPoaReservationPct); ?>;</script>
    <script src="<?php echo assetPath('frontend/src/js/core/security.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/core/shared.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/core/translation.js'); ?>"></script>
</head>
<body data-page="booking">
    <div class="booking-page">
        <article class="booking-seo-intro" style="max-width: 560px; margin: 0 auto 1.25rem; padding: 0 1rem; color: rgba(255,255,255,0.92); font-size: 0.95rem; line-height: 1.6; text-align: center;">
            <h2 style="margin: 0 0 0.5rem; font-size: 1.1rem; font-weight: 600; color: #fff;">Online reserveren bij Nijenhuis Botenverhuur</h2>
            <p style="margin: 0 0 0.75rem;">Vul hieronder je gewenste datum, boottype en contactgegevens in. Je ziet direct een prijsindicatie en kunt de reservering veilig afronden. Wij verhuren electrosloepen, zeilboten, kano’s en SUP-boards in Wanneperveen, aan de rand van Nationaal Park Weerribben-Wieden — ideaal voor een dagtocht richting Giethoorn of Belterwiede.</p>
            <p style="margin: 0;">Geen vaarbewijs nodig voor onze boten; voor vertrek krijg je korte instructie en een vaarkaart. Vragen? Bel <a href="tel:+31522281528" style="color: #fff; text-decoration: underline;">0522&nbsp;281&nbsp;528</a> of gebruik het <a href="/contact" style="color: #fff; text-decoration: underline;">contactformulier</a>.</p>
        </article>
        <div class="booking-container">
            <div class="booking-card">
                <div class="booking-header">
                    <img src="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>" alt="<?php echo SITE_NAME; ?>" class="booking-logo">
                    <h1 class="booking-title" data-i18n="booking_title">Boek je boot</h1>
                    <p class="booking-subtitle" data-i18n="booking_subtitle">Je boot is beschikbaar! Vul je gegevens in om je reservering te bevestigen.</p>
                </div>
                
                <div class="booking-content">
                    <div class="error-message" id="errorMessage"></div>
                    <div class="success-message" id="successMessage"></div>
                    
                    <form id="bookingForm" class="booking-form">
                        <h3 style="margin-bottom: var(--spacing-lg); color: var(--text-primary);" data-i18n="booking_details_title">Reserveringsgegevens</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bookingDate" data-i18n="booking_date_label">Selecteer datum *</label>
                                <input type="date" id="bookingDate" name="bookingDate" required min="">
                            </div>
                            <div class="form-group">
                                <label for="bookingEndDate" data-i18n="booking_end_date_label">Einddatum *</label>
                                <input type="date" id="bookingEndDate" name="bookingEndDate" required disabled>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="boatType" data-i18n="booking_boat_label">Selecteer boot *</label>
                            <select id="boatType" name="boatType" required>
                                <option value="" data-i18n="booking_select_boat">-- Selecteer een boot --</option>
                            </select>
                        </div>
                        
                        <div class="form-group" id="quantityGroup">
                            <label for="boatQuantity" data-i18n="booking_quantity_label">Aantal boten *</label>
                            <select id="boatQuantity" name="boatQuantity" required>
                                <option value="1">1</option>
                            </select>
                        </div>
                        
                        <!-- Options Container -->
                        <div id="optionsContainer" style="display: none; margin-bottom: var(--spacing-md); background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <h4 style="margin-top: 0; margin-bottom: 10px; font-size: 1rem;" data-i18n="booking_options_title">Extra opties</h4>
                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" id="useMotor" name="useMotor">
                                    <span class="checkmark"></span>
                                    <span class="label-text" data-i18n="booking_option_motor">Motor erbij huren?</span>
                                </label>
                                <div id="motorPriceInfo" style="font-size: 0.85em; color: var(--text-secondary); margin-left: 30px; margin-top: 5px;"></div>
                            </div>
                        </div>

                        <!-- Total Price Display -->
                        <div class="total-price-display" id="totalPriceDisplay" style="display: none;">
                            <div class="total-price-content">
                                <span class="total-price-label" data-i18n="booking_total_price">Totale prijs:</span>
                                <span class="total-price-amount" id="totalPriceAmount">€0.00</span>
                            </div>
                        </div>
                        
                        <div class="booking-summary" style="margin-top: var(--spacing-xl); margin-bottom: var(--spacing-xl);">
                            <h3 class="summary-title" data-i18n="booking_summary_title">Reserveringsoverzicht</h3>
                            <div class="summary-item">
                                <span class="summary-label" data-i18n="booking_summary_date">Datum:</span>
                                <span class="summary-value" id="summaryDate">-</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label" data-i18n="booking_summary_boat">Boot type:</span>
                                <span class="summary-value" id="summaryBoatType">-</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label" data-i18n="booking_summary_duration">Duur:</span>
                                <span class="summary-value" id="summaryDuration">-</span>
                            </div>
                            <div class="summary-item" id="bookingSummaryPriceRow">
                                <span class="summary-label" data-i18n="booking_summary_price">Totale prijs:</span>
                                <span class="summary-value" id="summaryPrice" style="color: var(--primary-color); font-weight: 600; font-size: 1.2em;">-</span>
                            </div>
                            <div id="bookingPoaInlineRows" class="booking-summary-poa-rows" hidden>
                                <div class="summary-item"><span class="summary-label" id="bookingPoaLblSub"></span><span class="summary-value" id="bookingPoaValSub"></span></div>
                                <div class="summary-item" id="bookingPoaRowAdmin"><span class="summary-label" id="bookingPoaLblAdm"></span><span class="summary-value" id="bookingPoaValAdm"></span></div>
                                <div class="summary-item"><span class="summary-label" id="bookingPoaLblOnline"></span><span class="summary-value booking-summary-poa-rows__online" id="bookingPoaValOnline"></span></div>
                                <div class="summary-item"><span class="summary-label" id="bookingPoaLblArr"></span><span class="summary-value" id="bookingPoaValArr"></span></div>
                                <div class="summary-item booking-summary-poa-rows__trip-total"><span class="summary-label" id="bookingPoaLblTrip"></span><span class="summary-value" id="bookingPoaValTrip"></span></div>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label" data-i18n="booking_summary_status">Status:</span>
                                <span class="summary-value" id="summaryStatus" style="color: var(--success-color); font-weight: 600;" data-i18n="booking_status_select">Selecteer bovenstaande opties</span>
                            </div>
                        </div>

                        <h3 style="margin-top: var(--spacing-2xl); margin-bottom: var(--spacing-lg); color: var(--text-primary);" data-i18n="booking_your_info">Jouw gegevens</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="customerName" data-i18n="booking_name_label">Volledige naam *</label>
                                <input type="text" id="customerName" name="customerName" required>
                            </div>
                            <div class="form-group">
                                <label for="customerEmail" data-i18n="booking_email_label">E-mailadres *</label>
                                <input type="email" id="customerEmail" name="customerEmail" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="customerPhone" data-i18n="booking_phone_label">Telefoonnummer *</label>
                                <input type="tel" id="customerPhone" name="customerPhone" required>
                            </div>
                            <div class="form-group">
                                <label for="customerAddress" data-i18n="booking_address_label">Adres (optioneel)</label>
                                <input type="text" id="customerAddress" name="customerAddress">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="bookingArrivalTime" data-i18n="checkout_arrival_time_label">Aankomsttijd *</label>
                                <select id="bookingArrivalTime" name="arrivalTime" required class="form-input">
                                    <option value="">--</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="bookingCityOfOrigin" data-i18n="checkout_city_label">Woonplaats *</label>
                                <input type="text" id="bookingCityOfOrigin" name="cityOfOrigin" required class="form-input" autocomplete="address-level2" placeholder="Bijv. Amsterdam">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="specialRequests" data-i18n="booking_notes_label">Speciale verzoeken of opmerkingen</label>
                            <textarea id="specialRequests" name="specialRequests" data-i18n="booking_notes_placeholder" data-i18n-attr="placeholder" placeholder="Eventuele speciale wensen..."></textarea>
                        </div>

                        <div class="booking-payment-methods-wrap">
                            <h3 id="booking-payment-heading" class="booking-payment-methods-title" data-i18n="checkout_payment_info_title">Betaalmethode</h3>
                            <p class="booking-payment-methods-intro" data-i18n="checkout_payment_info_body">Kies hieronder hoe je wilt betalen.</p>

                            <?php
                                $bWalletMethods = defined('CHECKOUT_WALLET_METHODS') ? CHECKOUT_WALLET_METHODS : [];
                                $bRadioMethods = array_values(array_filter($bookingPaymentMethods, fn($m) => !in_array($m, $bWalletMethods, true)));
                            ?>

                            <?php if ($bWalletMethods) : ?>
                            <?php $walletMethods = $bWalletMethods; include __DIR__ . '/../components/wallet-buttons.php'; ?>
                            <?php endif; ?>

                            <fieldset class="checkout-payment-methods booking-payment-methods" aria-labelledby="booking-payment-heading">
                                <div class="checkout-payment-methods__options">
                                    <?php foreach ($bRadioMethods as $bidx => $bMethod) :
                                        $bIsPoa = $bMethod === (defined('CHECKOUT_PAY_ON_ARRIVAL_METHOD') ? CHECKOUT_PAY_ON_ARRIVAL_METHOD : 'pay_on_arrival');
                                        ?>
                                        <label class="checkout-payment-method<?php echo $bIsPoa ? ' checkout-payment-method--pay-on-arrival' : ''; ?>">
                                            <input type="radio" name="paymentMethod" value="<?php echo htmlspecialchars($bMethod, ENT_QUOTES, 'UTF-8'); ?>" class="checkout-payment-method__input" <?php echo $bidx === 0 ? 'checked' : ''; ?> required>
                                            <span class="checkout-payment-method__label">
                                                <span class="checkout-payment-method__text" data-i18n="checkout_method_<?php echo htmlspecialchars($bMethod, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($bMethod, ENT_QUOTES, 'UTF-8'); ?></span><?php if ($bIsPoa) : ?>
                                                <span class="checkout-payment-method__poa-hint" data-i18n="checkout_pay_on_arrival_inline"></span><?php endif; ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </fieldset>
                            <p id="bookingPoaFeeExplain" class="checkout-pay-on-arrival-note checkout-poa-fee-explain" hidden></p>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" data-i18n="booking_confirm_btn">Reservering bevestigen</button>
                            <a href="/" class="btn btn-secondary" data-i18n="booking_back_btn">Terug naar home</a>
                        </div>
                    </form>
                    
                    <div class="loading" id="loading">
                        <div class="spinner"></div>
                        <p data-i18n="booking_processing">Je reservering wordt verwerkt...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Boat Data Service -->
    <script src="<?php echo assetPath('js/boat-data-service.js'); ?>"></script>
    <script src="<?php echo assetPath('mollie_api.js'); ?>"></script>

    <script src="<?php echo assetPath('frontend/src/js/pages/booking.js'); ?>"></script>
    <!-- Chatbot Widget -->
</body>
</html>

