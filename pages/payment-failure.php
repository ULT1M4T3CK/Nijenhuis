<?php
/**
 * Payment Failure Page - Nijenhuis Botenverhuur
 */
require_once __DIR__ . '/../components/config.php';
$basePath = '..';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <?php include __DIR__ . '/../components/gtag.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Betaling Mislukt - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Je betaling kon niet worden verwerkt. Probeer het opnieuw of neem contact met ons op.">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('pages/pages-consolidated.css'); ?>">
    <script src="<?php echo assetPath('frontend/src/js/core/translation.js'); ?>" defer></script>
    <script src="<?php echo assetPath('frontend/src/js/booking/mollie-payment.js'); ?>" defer></script>
</head>
<body>
    <?php include __DIR__ . '/../components/gtm-body.php'; ?>
    <div class="payment-failure-page">
        <div class="failure-card">
            <div class="failure-icon">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </div>
            
            <h1 class="failure-title" data-i18n="payment_failure_title">Betaling Mislukt</h1>
            <p class="failure-subtitle" data-i18n="payment_failure_subtitle">Helaas kon je betaling niet worden verwerkt. Probeer het opnieuw of neem contact met ons op.</p>
            
            <div id="paymentStatusInfo" class="payment-status-info" style="display: none; margin: 20px 0; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                <p style="margin: 0; font-size: 0.9em;" id="statusMessage"></p>
            </div>
            
            <div class="failure-actions">
                <a href="/booking" class="btn btn-primary" data-i18n="payment_failure_try_again">Opnieuw proberen</a>
                <a href="/" class="btn btn-secondary" data-i18n="payment_failure_back">Terug naar Home</a>
            </div>
            
            <div class="help-text">
                <h3 data-i18n="payment_failure_help_title">Hulp nodig?</h3>
                <p data-i18n="payment_failure_help_intro">Als je problemen blijft ondervinden met de betaling:</p>
                <p>• <span data-i18n="payment_failure_help_1">Controleer of je betaalgegevens correct zijn</span></p>
                <p>• <span data-i18n="payment_failure_help_2">Zorg dat je voldoende saldo hebt</span></p>
                <p>• <span data-i18n="payment_failure_help_3">Probeer een andere betaalmethode</span></p>
                <p>• <span data-i18n="payment_failure_help_4">Neem direct contact met ons op</span> <strong><?php echo SITE_PHONE; ?></strong></p>
            </div>
        </div>
    </div>

    <script>
        // Check payment status on page load
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const paymentId = urlParams.get('payment_id');
            const bookingId = urlParams.get('bookingId');
            const cartId = urlParams.get('cartId');

            // Webhook fallback: ensure the booking/cart is persisted as canceled on the server.
            // (mollie_api.php will fetch Mollie status and update admin/bookings.json)
            if (bookingId || cartId) {
                const statusUrl = bookingId
                    ? `/mollie_api.php?action=getBookingStatus&bookingId=${encodeURIComponent(bookingId)}`
                    : `/mollie_api.php?action=getCartStatus&cartId=${encodeURIComponent(cartId)}`;
                fetch(statusUrl).catch(() => { /* best-effort */ });
            }
            
            if (paymentId && window.molliePayment) {
                // Check if payment actually failed or is still pending
                window.molliePayment.getPaymentStatus(paymentId)
                    .then(status => {
                        const statusInfo = document.getElementById('paymentStatusInfo');
                        const statusMessage = document.getElementById('statusMessage');
                        
                        if (window.molliePayment.isPaymentFailed(status)) {
                            // Payment confirmed as failed
                            statusInfo.style.display = 'block';
                            statusInfo.style.background = '#f8d7da';
                            statusInfo.style.borderLeftColor = '#dc3545';
                            const template = window.getTranslation('payment_failure_status_failed');
                            statusMessage.textContent = template.replace('{status}', status.toUpperCase());
                        } else if (window.molliePayment.isPaymentCompleted(status)) {
                            // Payment actually succeeded - redirect to success page
                            const bookingId = urlParams.get('bookingId');
                            const redirectUrl = bookingId 
                                ? `payment-success.php?bookingId=${bookingId}&payment_id=${paymentId}`
                                : `payment-success.php?payment_id=${paymentId}`;
                            window.location.href = redirectUrl;
                        } else {
                            // Payment still pending
                            statusInfo.style.display = 'block';
                            const template = window.getTranslation('payment_failure_status_pending');
                            statusMessage.textContent = template.replace('{status}', status.toUpperCase());
                        }
                    })
                    .catch(error => {
                        console.error('Error checking payment status:', error);
                    });
            }
        });
    </script>
    <!-- Chatbot Widget -->
</body>
</html>
