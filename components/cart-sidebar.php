<?php
/**
 * Shopping Cart Sidebar Component
 * Included in footer to be available globally
 */
if (!defined('BOOKING_ADMIN_FEE_PERCENT')) {
    require_once __DIR__ . '/config.php';
}
?>
<!-- Cart Sidebar -->
<div id="cartSidebar" class="cart-sidebar">
    <div class="cart-sidebar-header">
        <h3 data-i18n="cart_title">🛒 Winkelwagen</h3>
        <button class="cart-sidebar-close"
                onclick="toggleCartSidebar()"
                aria-label="Sluiten"
                data-i18n="cart_close_aria"
                data-i18n-attr="aria-label">&times;</button>
    </div>
    <div class="cart-sidebar-content" id="cartSidebarContent">
        <p class="cart-empty-message" data-i18n="cart_empty">Je winkelwagen is leeg</p>
    </div>
    <div class="cart-sidebar-footer" id="cartSidebarFooter" style="display: none;">
        <div class="cart-total">
            <span data-i18n="cart_total_label">Totaal:</span>
            <span id="cartTotalPrice">€0.00</span>
        </div>
        <button onclick="validateAndCheckout()" class="btn btn-primary cart-checkout-btn" data-i18n="cart_checkout_btn">Afrekenen</button>
        <button class="btn btn-secondary cart-clear-btn" onclick="clearCart()" data-i18n="cart_clear_btn">Wissen</button>
    </div>
</div>
<div id="cartSidebarOverlay" class="cart-sidebar-overlay" onclick="toggleCartSidebar()"></div>
