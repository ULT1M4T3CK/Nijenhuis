<?php
/**
 * FOOTER Component - Site footer with contact info and navigation
 */

if (!defined('BOOKING_ADMIN_FEE_PERCENT')) {
    require_once __DIR__ . '/config.php';
}
$checkoutAdminFeePercentJs = defined('BOOKING_ADMIN_FEE_PERCENT') ? (float) BOOKING_ADMIN_FEE_PERCENT : 0.0;

$basePath = $basePath ?? getBasePath();
$currentYear = date('Y');
?>
<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-section">
                <img src="<?php echo assetPath(ltrim(PATH_LOGO, '/')); ?>" data-i18n-alt="alt_logo" alt="<?php echo SITE_NAME; ?>" style="height: 60px; margin-bottom: 15px;">
                <p data-i18n="footer_p">Hier begint jouw avontuur in de prachtige Weerribben!</p>
            </div>
            
            <div class="footer-section">
                <ul class="footer-links">
                    <li data-i18n="footer_company_name"><?php echo SITE_NAME; ?></li>
                    <li data-i18n="footer_company_location"><?php echo SITE_TAGLINE; ?></li>
                    <li data-i18n="footer_company_address"><?php echo SITE_ADDRESS; ?></li>
                    <li data-i18n="footer_company_postal"><?php echo SITE_POSTAL; ?></li>
                    <li data-i18n="footer_company_phone">Tel: <?php echo SITE_PHONE; ?></li>
                    <li data-i18n="footer_company_kvk">Kvk: <?php echo SITE_KVK; ?></li>
                    <li data-i18n="footer_company_btw">Btw nr: <?php echo SITE_BTW; ?></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3 class="footer-social-heading" style="font-size: 1rem; margin: 0 0 0.75rem; color: #fff;">Volg ons</h3>
                <ul class="footer-links footer-social-links" style="display: flex; flex-wrap: wrap; gap: 0.75rem 1rem; list-style: none; padding: 0; margin: 0;">
                    <li><a href="https://www.facebook.com/NijenhuisBotenverhuur" target="_blank" rel="noopener noreferrer" aria-label="Nijenhuis op Facebook">Facebook</a></li>
                    <li><a href="https://www.tripadvisor.nl/Attraction_Review-g1901647-d12925340-Reviews-Nijenhuis_Botenverhuur-Wanneperveen_Overijssel_Province.html" target="_blank" rel="noopener noreferrer" aria-label="Nijenhuis op TripAdvisor">TripAdvisor</a></li>
                    <li><a href="https://www.google.com/maps/place/?q=place_id:ChIJL2z_MFpJxkcRQJGDXYF7oJU" target="_blank" rel="noopener noreferrer" aria-label="Nijenhuis op Google Maps">Google Maps</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <ul class="footer-links">
                    <?php foreach ($FOOTER_NAV_COL1 as $item): ?>
                    <li><a data-i18n="<?php echo $item['i18n']; ?>" href="<?php echo $item['href']; ?>"><?php echo $item['label']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="footer-section">
                <ul class="footer-links">
                    <?php foreach ($FOOTER_NAV_COL2 as $item): ?>
                    <li><a data-i18n="<?php echo $item['i18n']; ?>" href="<?php echo $item['href']; ?>"><?php echo $item['label']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p data-i18n="footer_rights">&copy; <?php echo $currentYear; ?> <a href="https://ultimAItech.com" target="_blank" rel="noopener noreferrer">ultimAItech</a>. Alle rechten voorbehouden.</p>
            <div class="admin-link">
                <a href="<?php echo htmlspecialchars(assetPath('pages/admin-login.php')); ?>" class="admin-button" rel="nofollow noopener" style="color: #ccc; text-decoration: none; font-size: 12px; opacity: 0.7;">Admin</a>
            </div>
        </div>
    </div>
</footer>

<script>window.CHECKOUT_ADMIN_FEE_PERCENT=<?php echo json_encode($checkoutAdminFeePercentJs); ?>;</script>
<!-- Deferred JavaScript (non-critical, loaded at end of body for performance) -->
<script src="<?php echo assetPath(ltrim(PATH_JS_TRANSLATION, '/')); ?>" defer></script>
<script src="<?php echo assetPath(ltrim(PATH_JS_PAYMENT, '/')); ?>" defer></script>
<script src="<?php echo assetPath(ltrim(PATH_JS_CART, '/')); ?>" defer></script>

<?php if (!empty($includeBoatData)): ?>
<script src="<?php echo assetPath(ltrim(PATH_BOAT_DATA, '/')); ?>" defer></script>
<?php endif; ?>

<?php if (!empty($additionalScripts)): ?>
<?php foreach ($additionalScripts as $script): ?>
<script src="<?php echo assetPath(ltrim($script, '/')); ?>" defer></script>
<?php endforeach; ?>
<?php endif; ?>

<!-- Chatbot Widget -->
<!-- Cart Sidebar -->
<?php include __DIR__ . '/cart-sidebar.php'; ?>

<!-- Booking Modal (if booking system is used on this page) -->
<?php if (!empty($includeBookingModal) || !empty($includeBookingSystem)): ?>
<?php include __DIR__ . '/booking-modal.php'; ?>
<?php endif; ?>
