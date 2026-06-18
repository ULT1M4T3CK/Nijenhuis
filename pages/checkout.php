<?php
/**
 * Checkout Page - Nijenhuis Botenverhuur
 * Process cart and redirect to Mollie payment
 */
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/../components/csp.php';
nijenhuis_send_csp_header();
$checkoutAdminFeePercent = defined('BOOKING_ADMIN_FEE_PERCENT') ? (float) BOOKING_ADMIN_FEE_PERCENT : 0.0;
$checkoutPoaReservationPercent = defined('BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT') ? (float) BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT : 10.0;
$checkoutPaymentMethods = function_exists('getCheckoutPaymentMethods') ? getCheckoutPaymentMethods() : ['ideal', 'bancontact', 'pay_on_arrival'];
$pageTitle = 'Afrekenen';
$pageDescription = 'Voltooi je reservering bij Nijenhuis Botenverhuur.';
$canonicalUrl = SITE_URL . '/checkout';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <?php include __DIR__ . '/../components/gtag.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">
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
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/pages/checkout.css'); ?>">
    <script src="<?php echo assetPath('frontend/src/js/core/security.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/core/shared.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/core/translation.js'); ?>"></script>
</head>
<body class="page-checkout" data-page="checkout">
    
    <!-- Top Bar with Language Switcher -->
    <?php include __DIR__ . '/../components/topbar.php'; ?>

    <!-- Main Content -->
    <div class="checkout-page-container">
        <div class="container container--checkout-wide">
            <div class="checkout-card" id="checkoutCard">
                <a href="/" class="checkout-brand" aria-label="Nijenhuis — home">
                    <img src="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>" alt="Nijenhuis Botenverhuur" class="checkout-brand-logo" width="180" height="61">
                </a>

                <div class="error-message" id="errorMessage"></div>

                <div class="empty-cart-message" id="emptyCartMessage" style="display: none;">
                    <h2 data-i18n="checkout_empty_cart_title">Je winkelwagen is leeg</h2>
                    <p data-i18n="checkout_empty_cart_desc">Voeg boten toe aan je winkelwagen om te kunnen afrekenen.</p>
                    <a href="/" class="btn btn-primary checkout-empty-cta" data-i18n="checkout_empty_cart_btn">Naar botenverhuur</a>
                </div>

                <div id="checkoutMainFlow">
                    <div class="checkout-hero">
                        <h1 class="checkout-secure-title" data-i18n="checkout_secure_title">Veilig afrekenen</h1>
                        <ol class="checkout-steps" aria-label="Stappen">
                            <li class="checkout-steps__item checkout-steps__item--active">
                                <span class="checkout-steps__num">1</span>
                                <span class="checkout-steps__label" data-i18n="checkout_step_details">Gegevens</span>
                            </li>
                            <li class="checkout-steps__sep" aria-hidden="true">›</li>
                            <li class="checkout-steps__item">
                                <span class="checkout-steps__num">2</span>
                                <span class="checkout-steps__label" data-i18n="checkout_step_payment">Betaling</span>
                            </li>
                            <li class="checkout-steps__sep" aria-hidden="true">›</li>
                            <li class="checkout-steps__item">
                                <span class="checkout-steps__num">3</span>
                                <span class="checkout-steps__label" data-i18n="checkout_step_confirm">Bevestiging</span>
                            </li>
                        </ol>
                    </div>

                    <div id="checkoutContent" class="checkout-layout">
                        <form id="checkoutForm" class="checkout-form checkout-main">
                            <h2 class="checkout-section-heading" data-i18n="checkout_your_details">Jouw gegevens</h2>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="customerName" data-i18n="checkout_name_label">Volledige naam *</label>
                                    <input type="text" id="customerName" name="customerName" required class="form-input" autocomplete="name">
                                </div>
                                <div class="form-group">
                                    <label for="customerEmail" data-i18n="checkout_email_label">E-mailadres *</label>
                                    <input type="email" id="customerEmail" name="customerEmail" required class="form-input" autocomplete="email">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="customerPhone" data-i18n="checkout_phone_label">Telefoonnummer *</label>
                                    <input type="tel" id="customerPhone" name="customerPhone" required class="form-input" autocomplete="tel">
                                </div>
                                <div class="form-group">
                                    <label for="customerAddress" data-i18n="checkout_address_label">Adres (optioneel)</label>
                                    <input type="text" id="customerAddress" name="customerAddress" class="form-input" autocomplete="street-address">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="arrivalTime" data-i18n="checkout_arrival_time_label">Aankomsttijd *</label>
                                    <select id="arrivalTime" name="arrivalTime" required class="form-input">
                                        <option value="">-- Selecteer tijd --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="cityOfOrigin" data-i18n="checkout_city_label">Woonplaats *</label>
                                    <input type="text" id="cityOfOrigin" name="cityOfOrigin" required class="form-input" placeholder="Bijv. Amsterdam" autocomplete="address-level2">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group form-group--full">
                                    <label for="specialRequests" data-i18n="checkout_notes_label">Opmerkingen (optioneel)</label>
                                    <textarea id="specialRequests" name="specialRequests" rows="3" class="form-input" data-i18n="checkout_notes_placeholder" data-i18n-attr="placeholder"></textarea>
                                </div>
                            </div>

                            <div class="checkout-payment-info">
                                <h3 id="checkout-payment-heading" class="checkout-section-heading checkout-section-heading--sub" data-i18n="checkout_payment_info_title">Betaalmethode</h3>
                                <p class="checkout-payment-info__text" data-i18n="checkout_payment_info_body">Kies hieronder hoe je wilt betalen. Daarna ga je door naar de beveiligde betaalpagina van Mollie om de betaling af te ronden.</p>

                                <?php
                                    $walletMethods = defined('CHECKOUT_WALLET_METHODS') ? CHECKOUT_WALLET_METHODS : [];
                                    $radioMethods = array_values(array_filter($checkoutPaymentMethods, fn($m) => !in_array($m, $walletMethods, true)));
                                ?>

                                <?php if ($walletMethods) : ?>
                                <?php include __DIR__ . '/../components/wallet-buttons.php'; ?>
                                <?php endif; ?>

                                <fieldset class="checkout-payment-methods" aria-labelledby="checkout-payment-heading">
                                    <div class="checkout-payment-methods__options">
                                        <?php foreach ($radioMethods as $idx => $mMethod) :
                                            $isPoa = $mMethod === (defined('CHECKOUT_PAY_ON_ARRIVAL_METHOD') ? CHECKOUT_PAY_ON_ARRIVAL_METHOD : 'pay_on_arrival');
                                            ?>
                                            <label class="checkout-payment-method<?php echo $isPoa ? ' checkout-payment-method--pay-on-arrival' : ''; ?>">
                                                <input type="radio" name="paymentMethod" value="<?php echo htmlspecialchars($mMethod, ENT_QUOTES, 'UTF-8'); ?>" class="checkout-payment-method__input" <?php echo $idx === 0 ? 'checked' : ''; ?> required>
                                                <span class="checkout-payment-method__label">
                                                    <span class="checkout-payment-method__text" data-i18n="checkout_method_<?php echo htmlspecialchars($mMethod, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($mMethod, ENT_QUOTES, 'UTF-8'); ?></span><?php if ($isPoa) : ?>
                                                    <span class="checkout-payment-method__poa-hint" data-i18n="checkout_pay_on_arrival_inline"></span><?php endif; ?>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </fieldset>
                                <p id="checkoutPoaFeeExplain" class="checkout-pay-on-arrival-note checkout-poa-fee-explain" hidden></p>
                            </div>

                            <div class="checkout-policy-notice">
                                <h4 class="checkout-policy-title" data-i18n="checkout_policy_title">Belangrijke informatie</h4>
                                <ul class="checkout-policy-list">
                                    <li data-i18n="checkout_policy_cancellation">Bij annulering wordt een annuleringsvergoeding van 10% van het totaalbedrag in rekening gebracht.</li>
                                    <li data-i18n="checkout_policy_contact">Voor wijzigingen neem je telefonisch contact met ons op via +31 522 281 528.</li>
                                    <li data-i18n="checkout_policy_location">Onze locatie: Veneweg 199, 7946 LP Wanneperveen</li>
                                </ul>
                            </div>

                            <div class="checkout-actions checkout-actions--footer">
                                <a href="/" class="btn btn-secondary" data-i18n="checkout_home_btn">🏠 Naar Website</a>
                                <button type="submit" class="btn btn-primary checkout-actions__submit-mobile" data-i18n="checkout_pay_btn">💳 Betalen</button>
                            </div>
                        </form>

                        <aside class="checkout-sidebar" aria-label="Reserveringsoverzicht">
                            <div class="cart-summary" id="cartSummary">
                                <h3 class="cart-summary-title" data-i18n="checkout_booking_summary">Reserveringsoverzicht</h3>
                                <div id="cartItems"></div>
                                <div class="cart-summary-totals" id="cartSummaryTotals">
                                    <div class="cart-total-row cart-total-row--grand" id="cartGrandTotalRow">
                                        <span id="cartGrandLabel">Totaal te betalen:</span>
                                        <span class="total-price" id="cartTotal">€0.00</span>
                                    </div>
                                </div>
                                <div id="depositNoteContainer"></div>
                                <button type="submit" form="checkoutForm" class="btn btn-primary btn-block checkout-sidebar-submit" data-i18n="checkout_pay_btn">💳 Betalen</button>
                                <ul class="checkout-trust">
                                    <li>
                                        <span class="checkout-trust__icon" aria-hidden="true">🔒</span>
                                        <span class="checkout-trust__text" data-i18n="checkout_trust_secure">Veilige betaling via Mollie</span>
                                    </li>
                                    <li>
                                        <span class="checkout-trust__icon" aria-hidden="true">📞</span>
                                        <span class="checkout-trust__text" data-i18n="checkout_trust_support">Hulp nodig? Bel ons</span>
                                    </li>
                                    <li>
                                        <span class="checkout-trust__icon" aria-hidden="true">↩</span>
                                        <span class="checkout-trust__text" data-i18n="checkout_trust_policy">Annuleringsvoorwaarden in het blok hiernaast</span>
                                    </li>
                                </ul>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?php echo assetPath('js/boat-data-service.js'); ?>"></script>
    <script src="<?php echo assetPath('frontend/src/js/booking/cart.js'); ?>"></script>
    <script>
        window.CHECKOUT_ADMIN_FEE_PERCENT = <?php echo json_encode($checkoutAdminFeePercent); ?>;
        window.CHECKOUT_POA_RESERVATION_FEE_PERCENT = <?php echo json_encode($checkoutPoaReservationPercent); ?>;
    </script>
    <script>
        class CheckoutPage {
            constructor() {
                this.cart = null;
                this.init();
            }
            
            /**
             * Remove item from cart
             * @param {string} itemId - Cart item ID to remove
             */
            removeCartItem(itemId) {
                if (!this.cart) {
                    console.error('Cart not initialized');
                    return;
                }
                
                // Confirm removal
                if (!confirm(window.getTranslation('checkout_confirm_remove_item'))) {
                    return;
                }
                
                // Remove item from cart
                if (this.cart.removeItem(itemId)) {
                    // Re-render cart
                    this.renderCart();
                    
                    // Show notification if available
                    if (window.showNotification) {
                        window.showNotification(window.getTranslation('checkout_notification_removed'), 'success');
                    }
                } else {
                    console.error('Failed to remove item from cart');
                    if (window.showNotification) {
                        window.showNotification(window.getTranslation('checkout_notification_remove_error'), 'error');
                    }
                }
            }

            async updateItemQuantity(itemId, newQuantity) {
                if (!this.cart) {
                    console.error('Cart not initialized');
                    return;
                }

                const item = this.cart.getItems().find(i => i.id === itemId);
                if (!item) {
                    console.error('Cart item not found:', itemId);
                    return;
                }

                const quantity = parseInt(newQuantity);
                if (isNaN(quantity) || quantity < 1) {
                    console.error('Invalid quantity:', newQuantity);
                    return;
                }

                // Get max available from boat data first (quick check)
                const boat = this.cart.getBoatById(item.boatId);
                const boatMax = boat ? (boat.total ?? 10) : 10;
                
                if (quantity > boatMax) {
                    alert(`Helaas zijn er maar ${boatMax} boot(en) beschikbaar voor deze periode.`);
                    // Reset dropdown to current quantity
                    const select = document.querySelector(`.checkout-quantity-select[data-item-id="${itemId}"]`);
                    if (select) select.value = item.quantity || 1;
                    return;
                }

                // Update the item quantity immediately (availability will be checked at checkout)
                if (this.cart.updateItem(itemId, item.startDate, item.endDate, quantity)) {
                    this.renderCart();
                    
                    // Check availability in background (non-blocking)
                    try {
                        const endpoint = (window.location.protocol === 'file:' || window.location.hostname === '')
                            ? 'http://localhost:8000/admin/booking-handler.py'
                            : `${window.location.origin}/admin/booking-handler.php`;

                        const response = await fetch(`${endpoint}?action=checkAvailability&boatType=${encodeURIComponent(item.boatId)}&date=${encodeURIComponent(item.startDate)}&endDate=${encodeURIComponent(item.endDate)}`, {
                            method: 'GET',
                            credentials: 'include',
                            headers: { 'Accept': 'application/json' }
                        });

                        let maxAvailable = boatMax;
                        if (response.ok) {
                            // Check if response has content before parsing
                            const text = await response.text();
                            if (text && text.trim()) {
                                try {
                                    const data = JSON.parse(text);
                                    if (data.success && data.data && data.data.availableCount !== undefined) {
                                        maxAvailable = data.data.availableCount;
                                    }
                                } catch (parseError) {
                                    console.warn('Failed to parse availability response:', parseError);
                                }
                            }
                        }
                        
                        if (quantity > maxAvailable) {
                            alert(`Let op: Er zijn maar ${maxAvailable} boot(en) beschikbaar voor deze periode. De beschikbaarheid wordt gecontroleerd bij het afrekenen.`);
                            // Update dropdown to reflect actual availability
                            const select = document.querySelector(`.checkout-quantity-select[data-item-id="${itemId}"]`);
                            if (select && maxAvailable < quantity) {
                                // Reset to max available if current selection exceeds it
                                this.cart.updateItem(itemId, item.startDate, item.endDate, maxAvailable);
                                this.renderCart();
                            }
                        }
                    } catch (error) {
                        console.warn('Background availability check failed (non-critical):', error);
                        // Don't block the update if availability check fails
                    }

                } else {
                    alert('Kan aantal niet updaten.');
                    // Reset dropdown
                    const select = document.querySelector(`.checkout-quantity-select[data-item-id="${itemId}"]`);
                    if (select) select.value = item.quantity || 1;
                }
            }

            buildQuantityOptions(itemId, boatId, startDate, endDate, currentQuantity) {
                // Get boat data to determine max quantity
                const boat = this.cart.getBoatById(boatId);
                const boatTotal = boat ? (boat.total ?? 10) : 10;
                const currentQty = currentQuantity || 1;
                
                // Start with boat total as max (will be refined by availability check)
                let options = '';
                for (let i = 1; i <= boatTotal; i++) {
                    options += `<option value="${i}" ${i === currentQty ? 'selected' : ''}>${i}</option>`;
                }
                
                // Update with real availability async (this will refine options based on actual availability)
                this.updateCheckoutQuantityDropdown(itemId, boatId, startDate, endDate, currentQty, boatTotal);
                return options;
            }

            async updateCheckoutQuantityDropdown(itemId, boatId, startDate, endDate, currentQuantity, fallbackMax) {
                const quantitySelect = document.querySelector(`.checkout-quantity-select[data-item-id="${itemId}"]`);
                if (!quantitySelect) return;

                try {
                    const endpoint = (window.location.protocol === 'file:' || window.location.hostname === '')
                        ? 'http://localhost:8000/admin/booking-handler.py'
                        : `${window.location.origin}/admin/booking-handler.php`;

                    const response = await fetch(`${endpoint}?action=checkAvailability&boatType=${encodeURIComponent(boatId)}&date=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}`, {
                        method: 'GET',
                        credentials: 'include',
                        headers: { 'Accept': 'application/json' }
                    });

                    // Get boat total for fallback and validation
                    const boat = this.cart.getBoatById(boatId);
                    const boatTotal = boat ? (boat.total ?? fallbackMax) : fallbackMax;
                    const currentQty = currentQuantity || 1;
                    
                    let maxQuantity = boatTotal; // Default to boat total
                    
                    if (response.ok) {
                        // Check if response has content before parsing
                        const text = await response.text();
                        if (text && text.trim()) {
                            try {
                                const data = JSON.parse(text);
                                console.log('[Checkout] Availability API response:', data);
                                
                                // Check different possible response formats
                                let availableCount = null;
                                if (data.success && data.data) {
                                    // Format: {success: true, data: {availableCount: X}}
                                    availableCount = data.data.availableCount;
                                } else if (data.availableCount !== undefined) {
                                    // Format: {availableCount: X}
                                    availableCount = data.availableCount;
                                } else if (data.available !== undefined && data.availableCount !== undefined) {
                                    // Alternative format
                                    availableCount = data.availableCount;
                                }
                                
                                if (availableCount !== null && availableCount !== undefined) {
                                    console.log(`[Checkout] Availability check for ${boatId}:`, {
                                        availableCountFromAPI: availableCount,
                                        currentQuantityInCart: currentQty,
                                        boatTotal: boatTotal
                                    });
                                    
                                    // The API returns availableCount based on existing bookings in the database.
                                    // Cart items are NOT in the database yet, so they shouldn't be counted by the API.
                                    // The availableCount tells us how many are available RIGHT NOW.
                                    // Since the cart item isn't in the database, availableCount already includes capacity for it.
                                    // However, if the user has currentQty in cart, we need to ensure they can select at least that much.
                                    // The max they can select is: min(availableCount + currentQty, boatTotal)
                                    // This ensures:
                                    // - If availableCount = 2 and currentQty = 1: max = min(3, 2) = 2 ✓
                                    // - If availableCount = 1 and currentQty = 1: max = min(2, 2) = 2 ✓
                                    // - If availableCount = 0 and currentQty = 1: max = min(1, 2) = 1 (fully booked except their cart item)
                                    
                                    maxQuantity = Math.min(availableCount + currentQty, boatTotal);
                                    
                                    console.log(`[Checkout] Calculated max quantity: ${maxQuantity} (availableCount: ${availableCount} + currentQty: ${currentQty} = ${availableCount + currentQty}, capped at boat total: ${boatTotal})`);
                                } else {
                                    console.warn('[Checkout] availableCount not found in response, using boat total:', boatTotal);
                                    maxQuantity = boatTotal;
                                }
                            } catch (parseError) {
                                console.warn('[Checkout] Failed to parse availability response:', parseError, 'Response text:', text);
                                maxQuantity = boatTotal;
                            }
                        } else {
                            console.warn('[Checkout] Empty response from availability API, using boat total:', boatTotal);
                            maxQuantity = boatTotal;
                        }
                    } else {
                        console.warn('[Checkout] Availability API request failed, status:', response.status, 'using boat total:', boatTotal);
                        maxQuantity = boatTotal;
                    }

                    // Preserve zero capacity so unavailable boats cannot be increased.
                    maxQuantity = Math.max(0, maxQuantity);

                    // Update this specific quantity select
                    quantitySelect.innerHTML = '';
                    for (let i = 1; i <= maxQuantity; i++) {
                        const option = document.createElement('option');
                        option.value = i;
                        option.textContent = i;
                        if (i === currentQuantity) option.selected = true;
                        quantitySelect.appendChild(option);
                    }
                } catch (e) {
                    console.error('Error updating checkout quantity dropdown:', e);
                    // Fallback: show at least current quantity
                    quantitySelect.innerHTML = '';
                    const fallbackQty = Math.max(currentQuantity || 1, 1);
                    for (let i = 1; i <= Math.max(fallbackQty, fallbackMax); i++) {
                        const option = document.createElement('option');
                        option.value = i;
                        option.textContent = i;
                        if (i === currentQuantity) option.selected = true;
                        quantitySelect.appendChild(option);
                    }
                }
            }
            
            async init() {
                // Initialize translation listener
                window.addEventListener('languageChanged', () => {
                   this.renderCart(); 
                });
                
                // Keep checkout in sync with cart changes (e.g. from sidebar edits)
                window.addEventListener('cartUpdated', () => {
                   this.renderCart(); 
                });

                // Wait for cart to be ready
                if (window.CartManager) {
                    this.cart = window.CartManager;
                }
                
                // Double check if boat data is loaded, otherwise wait or load
                if (this.cart) {
                     await this.cart.loadBoatData();
                     this.renderCart();
                     this.setupForm();
                } else {
                     window.addEventListener('cartReady', async (e) => {
                        this.cart = e.detail.cart;
                        await this.cart.loadBoatData(); // Ensure data inside cart is ready
                        this.renderCart();
                        this.setupForm();
                    });
                }
            }
            
            /**
             * Generate time options for arrival time dropdown
             * Only shows times from 09:00 to 18:00 in 15-minute intervals (00, 15, 30, 45)
             * @param {string} bookingDate - The booking date (YYYY-MM-DD format)
             */
            populateArrivalTimeOptions(bookingDate) {
                const arrivalTimeSelect = document.getElementById('arrivalTime');
                if (!arrivalTimeSelect) return;

                // Clear existing options except the first placeholder
                arrivalTimeSelect.innerHTML = '<option value="">-- Selecteer tijd --</option>';

                // Only populate if we have a valid booking date
                if (!bookingDate) {
                    arrivalTimeSelect.disabled = true;
                    return;
                }

                // Enable the select
                arrivalTimeSelect.disabled = false;

                // Generate time slots from 09:00 to 18:00 in 15-minute intervals
                const startHour = 9;
                const endHour = 18;
                const intervals = [0, 15, 30, 45]; // Quarterly steps

                for (let hour = startHour; hour <= endHour; hour++) {
                    for (const minute of intervals) {
                        // Skip 18:15, 18:30, 18:45 (only allow up to 18:00)
                        if (hour === endHour && minute > 0) {
                            break;
                        }

                        const hourStr = hour.toString().padStart(2, '0');
                        const minuteStr = minute.toString().padStart(2, '0');
                        const timeValue = `${hourStr}:${minuteStr}`;
                        const timeDisplay = `${hourStr}:${minuteStr}`;

                        const option = document.createElement('option');
                        option.value = timeValue;
                        option.textContent = timeDisplay;
                        arrivalTimeSelect.appendChild(option);
                    }
                }
                this.syncPayOnArrivalPaymentOption();
            }

            /** Minutes from midnight for HH:MM; null if invalid. */
            parseArrivalMinutes(timeVal) {
                if (!timeVal || typeof timeVal !== 'string') return null;
                const m = timeVal.trim().match(/^(\d{1,2}):(\d{2})/);
                if (!m) return null;
                return parseInt(m[1], 10) * 60 + parseInt(m[2], 10);
            }

            syncPayOnArrivalPaymentOption() {
                const poaMethod = '<?php echo htmlspecialchars(defined('CHECKOUT_PAY_ON_ARRIVAL_METHOD') ? CHECKOUT_PAY_ON_ARRIVAL_METHOD : 'pay_on_arrival', ENT_QUOTES, 'UTF-8'); ?>';
                const arrivalEl = document.getElementById('arrivalTime');
                const minutes = arrivalEl && arrivalEl.value ? this.parseArrivalMinutes(arrivalEl.value) : null;
                const allowed = minutes != null && minutes <= 11 * 60;
                const poaInput = document.querySelector(`input[name="paymentMethod"][value="${poaMethod}"]`);
                const poaLabel = poaInput ? poaInput.closest('label') : null;
                if (poaInput && poaLabel) {
                    if (!allowed) {
                        poaInput.disabled = true;
                        poaLabel.classList.add('checkout-payment-method--disabled');
                        if (poaInput.checked) {
                            poaInput.checked = false;
                            const firstOther = document.querySelector(`input[name="paymentMethod"]:not([value="${poaMethod}"])`);
                            if (firstOther) firstOther.checked = true;
                        }
                    } else {
                        poaInput.disabled = false;
                        poaLabel.classList.remove('checkout-payment-method--disabled');
                    }
                }
                const feeEx = document.getElementById('checkoutPoaFeeExplain');
                if (feeEx && window.getTranslation) {
                    const poaChecked2 = document.querySelector(`input[name="paymentMethod"][value="${poaMethod}"]:checked`);
                    const pPct = typeof window.CHECKOUT_POA_RESERVATION_FEE_PERCENT === 'number' ? window.CHECKOUT_POA_RESERVATION_FEE_PERCENT : 10;
                    const pctDisplay = Number.isInteger(pPct) ? String(pPct) : String(pPct);
                    const adminPctEx = typeof window.CHECKOUT_ADMIN_FEE_PERCENT === 'number' ? window.CHECKOUT_ADMIN_FEE_PERCENT : 0;
                    const adminPctDisplay = Number.isInteger(adminPctEx) ? String(adminPctEx) : String(adminPctEx);
                    if (adminPctEx <= 0) {
                        feeEx.textContent = window.getTranslation('checkout_poa_fee_explain_no_admin_fee').replace(/\{percent\}/g, pctDisplay);
                    } else {
                        feeEx.textContent = window.getTranslation('checkout_poa_fee_explain')
                            .replace(/\{percent\}/g, pctDisplay)
                            .replace(/\{admin_percent\}/g, adminPctDisplay);
                    }
                    feeEx.hidden = !poaChecked2 || !allowed;
                }
                this.refreshPoaPaySummary();
            }

            refreshPoaPaySummary() {
                const poaMethod = '<?php echo htmlspecialchars(defined('CHECKOUT_PAY_ON_ARRIVAL_METHOD') ? CHECKOUT_PAY_ON_ARRIVAL_METHOD : 'pay_on_arrival', ENT_QUOTES, 'UTF-8'); ?>';
                const poaChecked = document.querySelector(`input[name="paymentMethod"][value="${poaMethod}"]:checked`);
                const arrivalEl = document.getElementById('arrivalTime');
                const minutes = arrivalEl && arrivalEl.value ? this.parseArrivalMinutes(arrivalEl.value) : null;
                const allowed = minutes != null && minutes <= 11 * 60;
                const grandLabel = document.getElementById('cartGrandLabel');
                const totalEl = document.getElementById('cartTotal');
                const t = (key) => (window.getTranslation ? window.getTranslation(key) : key);

                if (!grandLabel || !totalEl) return;

                const sub = typeof this._checkoutSubtotal === 'number' ? this._checkoutSubtotal : 0;
                const grand = typeof this._checkoutGrand === 'number' ? this._checkoutGrand : 0;

                if (!poaChecked || !allowed || (sub <= 0 && grand <= 0)) {
                    grandLabel.textContent = t('checkout_total');
                    if (typeof this._checkoutGrand === 'number') {
                        totalEl.textContent = `€${this._checkoutGrand.toFixed(2)}`;
                    }
                    return;
                }
                const resPct = typeof window.CHECKOUT_POA_RESERVATION_FEE_PERCENT === 'number' ? window.CHECKOUT_POA_RESERVATION_FEE_PERCENT : 10;
                const adminPctPoa = typeof window.CHECKOUT_ADMIN_FEE_PERCENT === 'number' ? window.CHECKOUT_ADMIN_FEE_PERCENT : 0;
                const resBase = Math.round(sub * (resPct / 100) * 100) / 100;
                const adminOnSlice = Math.round((resBase * (adminPctPoa / 100)) * 100) / 100;
                const reservation = Math.round((resBase + adminOnSlice) * 100) / 100;

                grandLabel.textContent = t('checkout_total');
                totalEl.textContent = `€${reservation.toFixed(2)}`;
            }

            renderCart() {
                if (!this.cart) return;

                const items = this.cart.getItems();
                const total = this.cart.getTotal();
                const checkoutCard = document.getElementById('checkoutCard');
                
                const checkoutPageContainer = document.querySelector('.checkout-page-container');
                
                const mainFlow = document.getElementById('checkoutMainFlow');
                if (items.length === 0) {
                    document.getElementById('emptyCartMessage').style.display = 'block';
                    if (mainFlow) mainFlow.style.display = 'none';
                    if (checkoutCard) {
                        checkoutCard.classList.add('empty-cart');
                    }
                    if (checkoutPageContainer) {
                        checkoutPageContainer.classList.add('empty-state');
                    }
                    this._checkoutSubtotal = 0;
                    this._checkoutAdminFee = 0;
                    this._checkoutGrand = 0;
                    this.populateArrivalTimeOptions(null);
                    this.refreshPoaPaySummary();
                    return;
                } else {
                    document.getElementById('emptyCartMessage').style.display = 'none';
                    if (mainFlow) mainFlow.style.display = '';
                    if (checkoutCard) {
                        checkoutCard.classList.remove('empty-cart');
                    }
                    if (checkoutPageContainer) {
                        checkoutPageContainer.classList.remove('empty-state');
                    }
                }

                // Get the first item's start date for arrival time selection
                const firstItem = items[0];
                const bookingDate = firstItem ? firstItem.startDate : null;
                
                // Populate arrival time options based on the booking date
                this.populateArrivalTimeOptions(bookingDate);
                
                const itemsContainer = document.getElementById('cartItems');
                let html = '';
                let totalDeposit = 0;
                
                // Helper to get translated string locally
                const t = (key) => window.getTranslation ? window.getTranslation(key) : key;
                
                items.forEach(item => {
                    const dateRange = this.cart.formatDateRange(item.startDate, item.endDate);
                    
                    // Note: Ideally boat names should be keys for translation, but we use the name from DB for now
                    const dayLabel = item.days === 1 ? t('checkout_day') : t('checkout_days');
                    
                    // Check deposit (multiply by quantity)
                    const boat = this.cart.getBoatById(item.boatId);
                    if (boat && boat.deposit) {
                        const itemQuantity = item.quantity || 1;
                        totalDeposit += Number(boat.deposit) * itemQuantity;
                    }

                    const imgSrc = this.escapeHTML(this.normalizeBoatImageUrl(boat));
                    const imgAlt = this.escapeHTML(item.boatName);
                    
                    html += `
                        <div class="cart-summary-item" data-item-id="${item.id}">
                            <div class="cart-item-thumb">
                                <img src="${imgSrc}" alt="${imgAlt}" width="76" height="76" loading="lazy" decoding="async">
                            </div>
                            <div class="cart-item-info">
                                <div class="cart-item-name">${this.escapeHTML(item.boatName)}</div>
                                <div class="cart-item-dates">${dateRange} (${item.days} ${dayLabel})</div>
                                <div class="cart-item-quantity">
                                    <label class="cart-item-qty-label">${this.escapeHTML(t('checkout_qty_label'))}</label>
                                    <select class="checkout-quantity-select" data-item-id="${item.id}" onchange="checkoutPage.updateItemQuantity('${item.id}', this.value)">
                                        ${this.buildQuantityOptions(item.id, item.boatId, item.startDate, item.endDate, item.quantity || 1)}
                                    </select>
                                </div>
                            </div>
                            <div class="cart-item-price">€${item.price.toFixed(2)}</div>
                            <button type="button" class="cart-item-remove" onclick="checkoutPage.removeCartItem('${item.id}')" title="Verwijderen">
                                Verwijderen
                            </button>
                        </div>
                    `;
                });
                
                itemsContainer.innerHTML = html;
                const pct = typeof window.CHECKOUT_ADMIN_FEE_PERCENT === 'number' ? window.CHECKOUT_ADMIN_FEE_PERCENT : 0;
                const subtotal = total;
                const adminFee = Math.round(subtotal * (pct / 100) * 100) / 100;
                const grandTotal = Math.round((subtotal + adminFee) * 100) / 100;
                const totalEl = document.getElementById('cartTotal');
                if (totalEl) totalEl.textContent = `€${grandTotal.toFixed(2)}`;
                
                // Handle Deposit Note
                const depositContainer = document.getElementById('depositNoteContainer');
                if (totalDeposit > 0) {
                    const depositMsg = t('checkout_deposit_note').replace('{amount}', totalDeposit.toFixed(2));
                    depositContainer.innerHTML = `
                        <div class="deposit-note">
                            <span>ℹ️</span>
                            <span>${this.escapeHTML(depositMsg)}</span>
                        </div>
                    `;
                    depositContainer.style.display = 'block';
                } else {
                    depositContainer.style.display = 'none';
                }

                this._checkoutSubtotal = subtotal;
                this._checkoutAdminFee = adminFee;
                this._checkoutGrand = grandTotal;
                this.refreshPoaPaySummary();
            }
            
            setupForm() {
                const form = document.getElementById('checkoutForm');
                if (!form) return;
                
                // Clone to remove old listeners
                const newForm = form.cloneNode(true);
                form.parentNode.replaceChild(newForm, form);
                
                newForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.processCheckout();
                });

                newForm.querySelectorAll('.checkout-wallet-btn').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const method = btn.dataset.method;
                        const radios = newForm.querySelectorAll('input[name="paymentMethod"]');
                        radios.forEach(r => r.checked = false);
                        let radio = newForm.querySelector(`input[name="paymentMethod"][value="${method}"]`);
                        if (!radio) {
                            radio = document.createElement('input');
                            radio.type = 'hidden';
                            radio.name = 'paymentMethod';
                            radio.value = method;
                            newForm.appendChild(radio);
                        }
                        radio.checked = true;
                        this.processCheckout();
                    });
                });

                const poaMethod = '<?php echo htmlspecialchars(defined('CHECKOUT_PAY_ON_ARRIVAL_METHOD') ? CHECKOUT_PAY_ON_ARRIVAL_METHOD : 'pay_on_arrival', ENT_QUOTES, 'UTF-8'); ?>';
                newForm.querySelectorAll('input[name="paymentMethod"]').forEach((r) => {
                    r.addEventListener('change', () => this.syncPayOnArrivalPaymentOption());
                });
                const arrivalEl = document.getElementById('arrivalTime');
                if (arrivalEl) {
                    arrivalEl.addEventListener('change', () => this.syncPayOnArrivalPaymentOption());
                }
                this.syncPayOnArrivalPaymentOption();
            }
            
            async processCheckout() {
                const t = (key) => window.getTranslation ? window.getTranslation(key) : key;
                
                const form = document.getElementById('checkoutForm');
                const formData = new FormData(form);
                
                const customerData = {
                    customerName: formData.get('customerName'),
                    customerEmail: formData.get('customerEmail'),
                    customerPhone: formData.get('customerPhone'),
                    customerAddress: formData.get('customerAddress') || '',
                    arrivalTime: formData.get('arrivalTime'),
                    cityOfOrigin: formData.get('cityOfOrigin'),
                    notes: formData.get('specialRequests') || ''
                };
                
                // Validate
                if (!customerData.customerName || !customerData.customerEmail || !customerData.customerPhone || !customerData.arrivalTime || !customerData.cityOfOrigin) {
                    this.showError(t('checkout_error_fields'));
                    return;
                }
                
                if (!this.isValidEmail(customerData.customerEmail)) {
                    this.showError(t('checkout_error_email'));
                    return;
                }

                const paymentMethod = (formData.get('paymentMethod') || '').toString().trim();
                if (!paymentMethod) {
                    this.showError(t('checkout_error_payment_method'));
                    return;
                }
                const poaVal = '<?php echo htmlspecialchars(defined('CHECKOUT_PAY_ON_ARRIVAL_METHOD') ? CHECKOUT_PAY_ON_ARRIVAL_METHOD : 'pay_on_arrival', ENT_QUOTES, 'UTF-8'); ?>';
                if (paymentMethod === poaVal) {
                    const mins = this.parseArrivalMinutes(customerData.arrivalTime);
                    if (mins == null || mins > 11 * 60) {
                        this.showError(t('checkout_error_pay_on_arrival_time'));
                        return;
                    }
                }
                
                const submitBtn = form.querySelector('button[type="submit"]');
                const sidebarSubmit = document.querySelector('.checkout-sidebar-submit');
                const originalBtnText = submitBtn ? submitBtn.textContent : '';
                const originalSidebarText = sidebarSubmit ? sidebarSubmit.textContent : '';
                const busyLabel = t('checkout_loading');

                const setSubmitBusy = (busy) => {
                    if (submitBtn) {
                        submitBtn.disabled = busy;
                        submitBtn.textContent = busy ? busyLabel : originalBtnText;
                    }
                    if (sidebarSubmit) {
                        sidebarSubmit.disabled = busy;
                        sidebarSubmit.textContent = busy ? busyLabel : originalSidebarText;
                    }
                };

                setSubmitBusy(true);

                try {
                    // Prepare cart items for API
                    const items = this.cart.getItems();
                    const total = this.cart.getTotal();
                    
                    // Create payment via server
                    const paymentData = {
                        action: 'createCartPayment',
                        items: items.map(item => ({
                            boatId: item.boatId,
                            boatName: item.boatName,
                            startDate: item.startDate,
                            endDate: item.endDate,
                            days: item.days,
                            quantity: item.quantity || 1,
                            price: item.price,
                            useMotor: item.useMotor || false
                        })),
                        total: total,
                        paymentMethod,
                        ...customerData
                    };
                    
                    const response = await fetch('<?php echo assetPath('mollie_api.php'); ?>?action=createCartPayment', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(paymentData)
                    });
                    
                    const result = await response.json();
                    
                    // Handle unavailable items (409 Conflict)
                    if (response.status === 409 && result.unavailableItems && result.unavailableItems.length > 0) {
                        const boatNames = result.unavailableItems.map(i => i.boatName).join(', ');
                        const messageTemplate = window.getTranslation('checkout_error_unavailable_boats');
                        const message = (messageTemplate || '').replace('{boats}', boatNames);
                        this.showError(message);
                        // Also show notification
                        this.showNotification(message, 'error');
                        setSubmitBusy(false);
                        return;
                    }
                    
                    if (result.success && result.paymentUrl) {
                        // Clear cart and redirect to Mollie
                        this.cart.clear();
                        window.location.href = result.paymentUrl;
                    } else if (result._links && result._links.checkout) {
                        // Alternative response format
                        this.cart.clear();
                        window.location.href = result._links.checkout.href;
                    } else {
                        let errorMsg = t('checkout_error_general');
                        if (result.error === 'payment_method_required' || result.error === 'payment_method_invalid') {
                            errorMsg = t('checkout_error_payment_method');
                        } else if (result.error === 'pay_on_arrival_arrival_invalid') {
                            errorMsg = t('checkout_error_pay_on_arrival_time');
                        } else if (result.message) {
                            errorMsg = result.message;
                        }
                        this.showError(errorMsg);
                        this.showNotification(errorMsg, 'error');
                        setSubmitBusy(false);
                    }
                } catch (error) {
                    console.error('Checkout error:', error);
                    const errorMsg = t('checkout_error_general');
                    this.showError(errorMsg);
                    this.showNotification(errorMsg, 'error');
                    setSubmitBusy(false);
                }
            }
            
            showError(message) {
                const errorDiv = document.getElementById('errorMessage');
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
                setTimeout(() => { errorDiv.style.display = 'none'; }, 5000);
            }
            
            showNotification(message, type = 'info') {
                // Try to use shared notification system first
                if (window.NijenhuisShared && window.NijenhuisShared.showNotification) {
                    window.NijenhuisShared.showNotification(message, type);
                    return;
                }
                
                // Fallback: create notification manually
                const notification = document.createElement('div');
                notification.className = `notification notification-${type}`;
                
                const content = document.createElement('div');
                content.className = 'notification-content';
                
                const msg = document.createElement('div');
                msg.className = 'notification-message';
                msg.textContent = message;
                
                const closeBtn = document.createElement('button');
                closeBtn.className = 'notification-close';
                closeBtn.setAttribute('aria-label', 'Sluiten');
                closeBtn.textContent = '×';
                closeBtn.addEventListener('click', () => notification.remove());
                
                content.appendChild(msg);
                content.appendChild(closeBtn);
                notification.appendChild(content);
                document.body.appendChild(notification);
                
                // Auto-remove after 8 seconds (longer for errors)
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transition = 'opacity 0.3s';
                    setTimeout(() => notification.remove(), 300);
                }, type === 'error' ? 8000 : 5000);
            }
            
            
            /* showLoading removed */
            
            
            isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }
            
            escapeHTML(str) {
                if (!str) return '';
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            /** Web URL for boat list image (boats.json uses ../frontend/Images/... paths). */
            normalizeBoatImageUrl(boat) {
                const raw = (boat && (boat.image || boat.headerImage)) || '';
                if (!raw) {
                    return '/frontend/Images/Boats/tender-720/tender-720-10-12.jpg';
                }
                if (/^https?:\/\//i.test(raw)) {
                    return raw;
                }
                let p = String(raw).replace(/\\/g, '/');
                if (p.startsWith('../')) {
                    p = p.slice(3);
                }
                if (!p.startsWith('/')) {
                    p = '/' + p;
                }
                return p;
            }
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            window.checkoutPage = new CheckoutPage();
        });
    </script>
    <!-- Chatbot Widget -->
</body>
</html>
