/**
 * Shopping Cart Manager for Nijenhuis Botenverhuur
 * Manages cart state in localStorage and provides cart operations
 */

// Guard against duplicate loading
if (typeof CartManager !== 'undefined') {
    console.warn('cart.js: CartManager already defined, skipping duplicate load');
} else {

    class CartManager {
        constructor() {
            this.storageKey = 'nijenhuis_cart';
            this.items = [];
            this.loadFromStorage();
            this.boats = [];
            this.loadBoatData();
        }

        async loadBoatData() {
            try {
                if (window.BoatDataService) {
                    this.boats = await window.BoatDataService.getAllBoats();
                } else {
                    const stored = localStorage.getItem('nijenhuis_boats');
                    if (stored) this.boats = JSON.parse(stored);
                }
            } catch (e) {
                console.error('CartManager: Error loading boat data:', e);
            }
        }

        /**
         * Add item to cart
         * @param {string} boatId - Boat ID
         * @param {string} startDate - Start date (YYYY-MM-DD)
         * @param {string} endDate - End date (YYYY-MM-DD)
         * @param {boolean} useMotor - Use outboard motor option
         * @returns {boolean} Success
         */
        /**
         * Add item to cart
         * @param {string} boatId - Boat ID
         * @param {string} startDate - Start date (YYYY-MM-DD)
         * @param {string} endDate - End date (YYYY-MM-DD)
         * @param {boolean} useMotor - Use outboard motor option
         * @param {number} quantity - Number of boats (default: 1)
         * @returns {Promise<boolean>} Success
         */
        async addItem(boatId, startDate, endDate, useMotor = false, quantity = 1) {
            // Calculate days
            const start = new Date(startDate);
            const end = new Date(endDate);
            const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;

            // Get boat info
            const boat = this.getBoatById(boatId);
            if (!boat) {
                console.error('CartManager: Boat not found:', boatId);
                return false;
            }

            // Only zeilboot 4/5 has an outboard option; ignore stale useMotor for e.g. zeilpunter
            useMotor = Boolean(useMotor && boatId === 'sailboat-4-5');

            // Calculate price per boat
            const pricePerBoat = this.calculatePrice(boatId, days, useMotor);
            const totalPrice = pricePerBoat * quantity;

            // Count how many of this boat are already in cart for overlapping dates
            let alreadyInCart = 0;
            this.items.forEach(item => {
                if (item.boatId === boatId && 
                    this.datesOverlap(item.startDate, item.endDate, startDate, endDate)) {
                    alreadyInCart += (item.quantity || 1);
                }
            });

            // Check availability from backend
            try {
                const endpoint = (window.location.protocol === 'file:' || window.location.hostname === '')
                    ? 'http://localhost:8000/admin/booking-handler.py'
                    : `${window.location.origin}/admin/booking-handler.php`;

                const response = await fetch(`${endpoint}?action=checkAvailability&boatType=${encodeURIComponent(boatId)}&date=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}`, {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });

                let availableCount = boat.total ?? 10; // Fallback to boat total
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.data) {
                        if (data.data.available === false) {
                            availableCount = 0;
                        } else if (data.data.availableCount !== undefined) {
                            availableCount = data.data.availableCount;
                        }
                    } else if (data.available === false) {
                        availableCount = 0;
                    }
                } else {
                    throw new Error(`Availability check failed with status ${response.status}`);
                }

                // Calculate remaining availability: backend available - already in cart
                const remainingAvailable = availableCount - alreadyInCart;

                // Check if requested quantity exceeds remaining availability
                if (quantity > remainingAvailable) {
                    // Show user-friendly notification
                    const toast = document.createElement('div');
                    toast.style.cssText = `
                        position: fixed; top: 20px; right: 20px; z-index: 10002;
                        background: #dc3545; color: white; padding: 15px 25px;
                        border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        font-family: sans-serif; animation: slideIn 0.3s ease-out;
                    `;
                    const strong = document.createElement('strong');
                    strong.textContent = 'Niet beschikbaar';
                    toast.appendChild(strong);
                    toast.appendChild(document.createElement('br'));
                    
                    if (remainingAvailable <= 0) {
                        const message = document.createTextNode(`Er zijn geen ${boat.name || 'boten'} meer beschikbaar voor deze periode.`);
                        toast.appendChild(message);
                    } else {
                        const boatText = boat.name || 'boten';
                        const message = document.createTextNode(`Er zijn maar ${remainingAvailable} ${boatText} beschikbaar voor deze periode.`);
                        toast.appendChild(message);
                    }
                    
                    document.body.appendChild(toast);
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.transition = 'opacity 0.5s';
                        setTimeout(() => toast.remove(), 500);
                    }, 5000);
                    
                    return false;
                }
            } catch (error) {
                console.warn('Availability check failed, blocking add:', error);
                return false;
            }

            // Add to local cart (availability checked above)
            this.items.push({
                id: 'cart_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
                boatId,
                boatName: boat.name,
                startDate,
                endDate,
                days,
                price: totalPrice,
                pricePerBoat: pricePerBoat,
                quantity: quantity,
                useMotor
            });

            this.saveToStorage();
            this.dispatchCartUpdate();

            // Show success notification
            const toast = document.createElement('div');
            toast.style.cssText = `
            position: fixed; top: 20px; right: 20px; z-index: 10002;
            background: #28a745; color: white; padding: 15px 25px;
            border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-family: sans-serif; animation: slideIn 0.3s ease-out;
        `;
            // SECURITY: Use textContent and create elements safely to prevent XSS
            toast.textContent = '';
            const strong = document.createElement('strong');
            strong.textContent = 'Gelukt!';
            toast.appendChild(strong);
            toast.appendChild(document.createElement('br'));
            toast.appendChild(document.createTextNode('Boot toegevoegd aan winkelwagen.'));
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.5s';
                setTimeout(() => toast.remove(), 500);
            }, 4000);

            return true;
        }

        /**
         * Remove item from cart by ID
         * @param {string} itemId - Cart item ID
         */
        removeItem(itemId) {
            const initialLength = this.items.length;
            this.items = this.items.filter(item => item.id !== itemId);

            if (this.items.length !== initialLength) {
                this.saveToStorage();
                this.dispatchCartUpdate();
                return true;
            }
            return false;
        }

        /**
         * Update item dates and quantity
         * @param {string} itemId - Cart item ID
         * @param {string} startDate - New start date (YYYY-MM-DD)
         * @param {string} endDate - New end date (YYYY-MM-DD)
         * @param {number} quantity - Number of boats (default: 1)
         * @returns {boolean} Success
         */
        updateItem(itemId, startDate, endDate, quantity = 1) {
            const index = this.items.findIndex(item => item.id === itemId);
            if (index === -1) return false;

            const item = this.items[index];

            // Recalculate days
            const start = new Date(startDate);
            const end = new Date(endDate);
            const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;

            if (days < 1) return false;

            // Recalculate price per boat
            const effectiveMotor = item.boatId === 'sailboat-4-5' && item.useMotor;
            const pricePerBoat = this.calculatePrice(item.boatId, days, effectiveMotor);
            const totalPrice = pricePerBoat * quantity;

            // Update item
            this.items[index] = {
                ...item,
                useMotor: effectiveMotor,
                startDate,
                endDate,
                days,
                price: totalPrice,
                pricePerBoat: pricePerBoat,
                quantity: quantity
            };

            console.log('Cart item updated:', this.items[index]);
            this.saveToStorage();
            this.dispatchCartUpdate();
            return true;
        }

        /**
         * Get all cart items
         * @returns {Array} Cart items
         */
        getItems() {
            return [...this.items];
        }

        /**
         * Get cart item count
         * @returns {number} Number of items
         */
        getCount() {
            return this.items.length;
        }

        /**
         * Get total price of all items
         * @returns {number} Total price
         */
        getTotal() {
            return this.items.reduce((sum, item) => sum + item.price, 0);
        }

        /**
         * Clear all items from cart
         */
        clear() {
            this.items = [];
            this.saveToStorage();
            this.dispatchCartUpdate();
        }

        /**
         * Check if cart is empty
         * @returns {boolean}
         */
        isEmpty() {
            return this.items.length === 0;
        }

        /**
         * Save cart to localStorage
         */
        saveToStorage() {
            try {
                localStorage.setItem(this.storageKey, JSON.stringify({
                    items: this.items,
                    updatedAt: new Date().toISOString()
                }));
            } catch (e) {
                console.error('CartManager: Error saving to storage:', e);
            }
        }

        /**
         * Load cart from localStorage
         */
        loadFromStorage() {
            try {
                const stored = localStorage.getItem(this.storageKey);
                if (stored) {
                    const data = JSON.parse(stored);
                    this.items = data.items || [];

                    // Clean up expired items (dates in the past)
                    const today = new Date().toISOString().split('T')[0];
                    this.items = this.items.filter(item => item.startDate >= today);

                    const beforeSanitize = JSON.stringify(this.items);
                    this.items = this.items.map(item => ({
                        ...item,
                        useMotor: Boolean(item.useMotor && item.boatId === 'sailboat-4-5')
                    }));

                    if (this.items.length !== (data.items || []).length || JSON.stringify(this.items) !== beforeSanitize) {
                        this.saveToStorage(); // Save cleaned / sanitized cart
                    }
                }
            } catch (e) {
                console.error('CartManager: Error loading from storage:', e);
                this.items = [];
            }
        }

        /**
         * Get boat by ID
         * @param {string} boatId - Boat ID
         * @returns {Object|null} Boat object or null
         */
        getBoatById(boatId) {
            if (this.boats && this.boats.length > 0) {
                return this.boats.find(b => b.id === boatId) || null;
            }

            // Fallback to localStorage
            try {
                const stored = localStorage.getItem('nijenhuis_boats');
                if (stored) {
                    const boats = JSON.parse(stored);
                    return boats.find(b => b.id === boatId) || null;
                }
            } catch (e) { }

            return null;
        }

        /**
         * Calculate price for boat rental
         * @param {string} boatId - Boat ID
         * @param {number} days - Number of days
         * @param {boolean} useMotor - Whether to use outboard motor (for sailboat)
         * @returns {number} Price
         */
        calculatePrice(boatId, days, useMotor = false) {
            const boat = this.getBoatById(boatId);
            if (!boat) return 0;

            let pricing = boat.pricing;
            let pricePerDay = Number(boat.pricePerDay || 0);

            // Special handling for sailboat engine
            if (useMotor && boatId === 'sailboat-4-5' && boat.pricingWithEngine) {
                pricing = boat.pricingWithEngine;
                // Approximate pricePerDay from first element if needed
                if (pricing[0]) pricePerDay = pricing[0]; // Assumption
            }

            if (days === 1) {
                return pricePerDay;
            } else if (days >= 2 && days <= 7) {
                if (pricing && pricing[days - 1] !== undefined) {
                    return Number(pricing[days - 1]);
                }
                return pricePerDay * days;
            } else if (days > 7) {
                // > 7 Days: Weekly price + (1/7th of weekly price per extra day)
                let weeklyPrice = 0;

                if (pricing && pricing[6] !== undefined) {
                    weeklyPrice = Number(pricing[6]);
                } else {
                    weeklyPrice = pricePerDay * 7;
                }

                if (weeklyPrice > 0) {
                    const extraDays = days - 7;
                    const costPerExtraDay = weeklyPrice / 7;
                    return weeklyPrice + (extraDays * costPerExtraDay);
                }
            }

            return 0;
        }

        /**
         * Check if two date ranges overlap
         */
        datesOverlap(start1, end1, start2, end2) {
            return start1 <= end2 && start2 <= end1;
        }

        /**
         * Dispatch custom event for cart updates
         */
        dispatchCartUpdate() {
            window.dispatchEvent(new CustomEvent('cartUpdated', {
                detail: {
                    count: this.getCount(),
                    total: this.getTotal(),
                    items: this.getItems()
                }
            }));
        }

        /**
         * Format date for display
         * @param {string} dateStr - Date string (YYYY-MM-DD)
         * @param {string} lang - Language code
         * @returns {string} Formatted date
         */
        formatDate(dateStr, lang = 'nl') {
            const locales = { nl: 'nl-NL', en: 'en-GB', de: 'de-DE' };
            const date = new Date(dateStr + 'T00:00:00');
            return date.toLocaleDateString(locales[lang] || 'nl-NL', {
                day: 'numeric',
                month: 'short'
            });
        }

        /**
         * Format date range for display
         * @param {string} startDate - Start date
         * @param {string} endDate - End date
         * @param {string} lang - Language code
         * @returns {string} Formatted range
         */
        formatDateRange(startDate, endDate, lang = 'nl') {
            if (startDate === endDate) {
                return this.formatDate(startDate, lang);
            }
            return `${this.formatDate(startDate, lang)} - ${this.formatDate(endDate, lang)}`;
        }
    }

    // Create global instance
    window.CartManager = new CartManager();

    // Notify when cart is ready
    document.addEventListener('DOMContentLoaded', () => {
        window.dispatchEvent(new CustomEvent('cartReady', {
            detail: { cart: window.CartManager }
        }));

        // Initialize UI
        updateCartUI();
        window.CartManager.setupEditModal();
    });

    // ====== GLOBAL CART UI FUNCTIONS ======

    window.toggleCartSidebar = function () {
        const sidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('cartSidebarOverlay');

        if (sidebar && overlay) {
            const wasOpen = sidebar.classList.contains('active');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');

            if (sidebar.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
                // Dispatch custom event when cart closes
                if (wasOpen) {
                    window.dispatchEvent(new CustomEvent('cartSidebarClosed'));
                }
            }
        }
    };

    window.updateCartUI = function () {
        const cart = window.CartManager;
        if (!cart) return;

        // Elements
        const floatingIcon = document.getElementById('cartFloatingIcon');
        const badge = document.getElementById('cartBadge');

        const navCount = document.getElementById('navCartCount');

        const content = document.getElementById('cartSidebarContent');
        const footer = document.getElementById('cartSidebarFooter');
        const totalPrice = document.getElementById('cartTotalPrice');

        const count = cart.getCount();
        const total = cart.getTotal();
        const items = cart.getItems();

        // Update Floating Icon (if exists)
        if (floatingIcon && badge) {
            if (count > 0) {
                floatingIcon.style.display = 'flex';
                badge.textContent = count;
            } else {
                floatingIcon.style.display = 'none';
            }
        }

        // Update Nav Icon (if exists)
        if (navCount) {
            if (count > 0) {
                navCount.style.display = 'flex';
                navCount.textContent = count;
            } else {
                navCount.style.display = 'none';
            }
        }

        // Update Sidebar
        if (content && footer && totalPrice) {
            if (count === 0) {
                content.innerHTML = '<p class="cart-empty-message">Uw winkelwagen is leeg</p>';
                footer.style.display = 'none';
            } else {
                let html = '';
                items.forEach(item => {
                    const dateRange = cart.formatDateRange(item.startDate, item.endDate);
                    const safeName = item.boatName.replace(/[&<>"']/g, function (m) {
                        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
                    });

                    // Use the actual quantity from the item (should be updated if quantity was changed)
                    const currentQuantity = item.quantity || 1;
                    
                    console.log('Rendering cart item:', item.id, 'quantity:', currentQuantity, 'price:', item.price);
                    
                    // Build quantity dropdown options starting with just current quantity
                    // Will be updated with real availability (total - booked) async
                    let quantityOptions = '';
                    const initialMax = Math.max(currentQuantity, 1);
                    for (let i = 1; i <= initialMax; i++) {
                        const selected = i === currentQuantity ? 'selected' : '';
                        quantityOptions += `<option value="${i}" ${selected}>${i}</option>`;
                    }
                    
                    // Get boat for fallback max (used if API fails)
                    const boat = cart.getBoatById(item.boatId);
                    const fallbackMax = boat ? (boat.total ?? 10) : 10;
                    
                    // Update dropdown with real availability (async, non-blocking)
                    // This will expand options up to availableCount (total - booked)
                    window.updateCartItemQuantityDropdown(item.id, item.boatId, item.startDate, item.endDate, currentQuantity, fallbackMax);
                    
                    html += `
                    <div class="cart-item" data-item-id="${item.id}">
                        <div class="cart-item-info">
                            <div class="cart-item-name">
                                ${safeName}
                                ${item.useMotor && item.boatId === 'sailboat-4-5' ? '<br><small style="color:#666; font-weight:normal;">+ Buitenboordmotor</small>' : ''}
                            </div>
                            <div class="cart-item-dates">
                                ${dateRange} (${item.days} ${item.days === 1 ? 'dag' : 'dagen'})
                                <button type="button" class="btn-link" style="padding:0; margin-left:5px; color:var(--primary-color); background:none; border:none; cursor:pointer;" onclick="openCartEditModal('${item.id}', '${item.startDate}', '${item.endDate}', ${currentQuantity})">✏️</button>
                            </div>
                            <div class="cart-item-quantity" style="margin-top: 8px; display: flex; align-items: center; gap: 8px;">
                                <label style="font-size: 0.9rem; color: var(--text-secondary);">Aantal:</label>
                                <select class="cart-quantity-select" data-item-id="${item.id}" style="padding: 4px 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.9rem; min-width: 60px;" onchange="updateCartItemQuantity('${item.id}', this.value)">
                                    ${quantityOptions}
                                </select>
                            </div>
                            <div class="cart-item-price">€${item.price.toFixed(2)}</div>
                        </div>
                        <button class="cart-item-remove" onclick="removeFromCart('${item.id}')" title="Verwijderen">×</button>
                    </div>
                `;
                });
                content.innerHTML = html;
                totalPrice.textContent = `€${total.toFixed(2)}`;
                footer.style.display = 'block';
            }
        }

        // Ensure checkout button state is always consistent (important for browser back/forward cache)
        resetCheckoutButtonState();
    };

    function resetCheckoutButtonState() {
        const btn = document.querySelector('.cart-checkout-btn');
        if (!btn) return;

        const cart = window.CartManager;
        const hasItems = !!cart && !cart.isEmpty();

        // If the browser restored a disabled button from history (bfcache), re-enable it.
        // Keep it disabled only when cart is empty.
        btn.disabled = !hasItems;
        // Restore label if it got stuck on "Controleren..."
        if (hasItems) {
            btn.textContent = 'Afrekenen';
        }
    }

    window.removeFromCart = function (itemId) {
        const cart = window.CartManager;
        if (cart.removeItem(itemId)) {
            updateCartUI();
        }
    };

    window.updateCartItemQuantity = async function (itemId, newQuantity) {
        console.log('updateCartItemQuantity called:', itemId, newQuantity);
        
        if (!window.CartManager) {
            console.error('CartManager not available');
            alert('Winkelwagen is niet beschikbaar. Ververs de pagina.');
            return;
        }
        
        const cart = window.CartManager;
        const item = cart.getItems().find(i => i.id === itemId);
        
        if (!item) {
            console.error('Cart item not found:', itemId, 'Available items:', cart.getItems().map(i => i.id));
            alert('Item niet gevonden in winkelwagen.');
            return;
        }

        const quantity = parseInt(newQuantity);
        if (isNaN(quantity) || quantity < 1) {
            console.error('Invalid quantity:', newQuantity);
            alert('Ongeldig aantal.');
            return;
        }
        
        // If quantity hasn't changed, do nothing
        if (item.quantity === quantity) {
            console.log('Quantity unchanged, skipping update');
            return;
        }

        // Get max available from boat data first (quick check)
        const boat = cart.getBoatById(item.boatId);
        const boatMax = boat ? (boat.total ?? 10) : 10;
        
        if (quantity > boatMax) {
            alert(`Helaas zijn er maar ${boatMax} boot(en) beschikbaar voor deze periode.`);
            // Reset dropdown to current quantity
            const select = document.querySelector(`.cart-quantity-select[data-item-id="${itemId}"]`);
            if (select) select.value = item.quantity || 1;
            return;
        }

        // Update the item quantity immediately (availability will be checked at checkout)
        console.log('Updating item quantity:', itemId, 'from', item.quantity, 'to', quantity);
        if (cart.updateItem(itemId, item.startDate, item.endDate, quantity)) {
            console.log('Item updated successfully, refreshing UI');
            // Update UI immediately
            if (typeof updateCartUI === 'function') {
                updateCartUI();
            } else if (typeof window.updateCartUI === 'function') {
                window.updateCartUI();
            } else {
                console.error('updateCartUI function not found');
                // Force UI update by dispatching event
                cart.dispatchCartUpdate();
            }
            
            // Also check availability in background and update dropdown if needed
            try {
                const endpoint = (window.location.protocol === 'file:' || window.location.hostname === '')
                    ? 'http://localhost:8000/admin/booking-handler.py'
                    : `${window.location.origin}/admin/booking-handler.php`;

                const response = await fetch(`${endpoint}?action=checkAvailability&boatType=${encodeURIComponent(item.boatId)}&date=${encodeURIComponent(item.startDate)}&endDate=${encodeURIComponent(item.endDate)}`, {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Accept': 'application/json' }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.data && (data.data.availableCount !== undefined || data.data.available === false)) {
                        const maxAvailable = data.data.available === false ? 0 : data.data.availableCount;
                        if (quantity > maxAvailable) {
                            alert(`Let op: Er zijn maar ${maxAvailable} boot(en) beschikbaar voor deze periode. De beschikbaarheid wordt gecontroleerd bij het afrekenen.`);
                            // Update dropdown to reflect actual availability
                            const select = document.querySelector(`.cart-quantity-select[data-item-id="${itemId}"]`);
                            if (select && maxAvailable < quantity) {
                                // Reset to max available if current selection exceeds it
                                cart.updateItem(itemId, item.startDate, item.endDate, maxAvailable);
                                window.updateCartUI();
                            }
                        }
                    }
                }
            } catch (error) {
                console.warn('Background availability check failed (non-critical):', error);
                // Don't block the update if availability check fails
            }
        } else {
            console.error('Failed to update item');
            alert('Kan aantal niet updaten.');
            // Reset dropdown
            const select = document.querySelector(`.cart-quantity-select[data-item-id="${itemId}"]`);
            if (select) select.value = item.quantity || 1;
        }
    };

    window.clearCart = function () {
        if (confirm('Weet u zeker dat u de winkelwagen wilt legen?')) {
            window.CartManager.clear();
            updateCartUI();
            toggleCartSidebar();
        }
    };

    // Modal Logic
    CartManager.prototype.setupEditModal = function () {
        if (document.getElementById('cartEditModal')) return;

        const modalHTML = `
    <div id="cartEditModal" style="display:none; position:fixed; z-index:10001; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.4); align-items:center; justify-content:center;">
        <div style="background-color:#fefefe; margin:auto; padding:20px; border:1px solid #888; width:90%; max-width:400px; border-radius:10px; position:relative;">
            <h3 style="margin-top:0;">Wijzig reservering</h3>
            <div style="margin-bottom:15px;">
                <input type="hidden" id="cartEditItemId">
                <div style="margin-bottom:10px;">
                    <label style="display:block; margin-bottom:5px;">Startdatum</label>
                    <input type="date" id="cartEditStartDate" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div style="margin-bottom:10px;">
                    <label style="display:block; margin-bottom:5px;">Einddatum</label>
                    <input type="date" id="cartEditEndDate" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div style="margin-bottom:10px;">
                    <label style="display:block; margin-bottom:5px;">Aantal boten</label>
                    <select id="cartEditQuantity" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                        <option value="1">1</option>
                    </select>
                </div>
            </div>
            <div style="text-align:right;">
                <button type="button" onclick="closeCartEditModal()" style="padding:8px 12px; margin-right:5px; background:#ddd; border:none; border-radius:4px; cursor:pointer;">Annuleren</button>
                <button type="button" onclick="saveCartEditDate()" style="padding:8px 12px; background:var(--primary-color, #0071BB); color:white; border:none; border-radius:4px; cursor:pointer;">Opslaan</button>
            </div>
        </div>
    </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
    };

    window.openCartEditModal = function (itemId, startDate, endDate, quantity = 1) {
        document.getElementById('cartEditItemId').value = itemId;
        document.getElementById('cartEditStartDate').value = startDate;
        document.getElementById('cartEditEndDate').value = endDate;
        const quantityInput = document.getElementById('cartEditQuantity');
        if (quantityInput) {
            quantityInput.value = quantity;
        }

        // Season Logic
        const today = new Date();
        const currentYear = today.getFullYear();
        const currentMonth = today.getMonth(); // 0-11

        let bookingOpen = true;
        let minDateStr, maxDateStr;

        // Booking Window: Jan 1 - Oct 31
        // Rental Window: Apr 1 - Oct 31

        if (currentMonth <= 9) { // Jan - Oct
            bookingOpen = true;
            const seasonStart = `${currentYear}-04-01`;
            const todayStr = new Date().toISOString().split('T')[0];
            minDateStr = todayStr > seasonStart ? todayStr : seasonStart;
            maxDateStr = `${currentYear}-10-31`;
        } else {
            // Nov-Dec: Season closed
            bookingOpen = false;
            minDateStr = `${currentYear + 1}-04-01`; // Next season
            maxDateStr = `${currentYear + 1}-10-31`;
        }

        const startInput = document.getElementById('cartEditStartDate');
        const endInput = document.getElementById('cartEditEndDate');

        startInput.min = minDateStr;
        startInput.max = maxDateStr;
        endInput.min = minDateStr;
        endInput.max = maxDateStr;

        document.getElementById('cartEditModal').style.display = 'flex';

        if (!bookingOpen) {
            // Disable inputs if season is closed
            startInput.disabled = true;
            endInput.disabled = true;
            // Optionally show message
            alert("Het boekingsseizoen voor dit jaar is gesloten. Reserveringen voor volgend seizoen openen op 1 januari.");
        } else {
            startInput.disabled = false;
            endInput.disabled = false;

            // Ensure end date constraint
            startInput.onchange = function () {
                endInput.min = this.value;
                if (endInput.value && endInput.value < this.value) {
                    endInput.value = this.value;
                }
                // Update quantity dropdown when dates change
                const item = cart.getItems().find(i => i.id === itemId);
                if (item) {
                    const boatId = item.boatId;
                    const newStartDate = startInput.value;
                    const newEndDate = endInput.value || newStartDate;
                    updateCartEditQuantityDropdown(boatId, newStartDate, newEndDate, parseInt(quantityInput.value) || 1);
                }
            };
            
            endInput.onchange = function () {
                // Update quantity dropdown when end date changes
                const item = cart.getItems().find(i => i.id === itemId);
                if (item) {
                    const boatId = item.boatId;
                    const newStartDate = startInput.value;
                    const newEndDate = endInput.value || newStartDate;
                    updateCartEditQuantityDropdown(boatId, newStartDate, newEndDate, parseInt(quantityInput.value) || 1);
                }
            };
            
            // Update quantity dropdown when modal opens
            const item = cart.getItems().find(i => i.id === itemId);
            if (item) {
                const boatId = item.boatId;
                const newStartDate = startInput.value;
                const newEndDate = endInput.value || newStartDate;
                updateCartEditQuantityDropdown(boatId, newStartDate, newEndDate, quantity);
            }
        }
    };

    window.closeCartEditModal = function () {
        document.getElementById('cartEditModal').style.display = 'none';
    };

    window.saveCartEditDate = function () {
        const itemId = document.getElementById('cartEditItemId').value;
        const startDate = document.getElementById('cartEditStartDate').value;
        const endDate = document.getElementById('cartEditEndDate').value;
        const quantityInput = document.getElementById('cartEditQuantity');
        const quantity = parseInt(quantityInput?.value || '1');

        if (!startDate || !endDate) return;
        if (startDate > endDate) {
            alert('Einddatum moet na startdatum liggen');
            return;
        }

        if (window.CartManager.updateItem(itemId, startDate, endDate, quantity)) {
            closeCartEditModal();
        } else {
            alert('Kan reservering niet updaten.');
        }
    };

    window.updateCartItemQuantityDropdown = async function (itemId, boatId, startDate, endDate, currentQuantity = 1, fallbackMax = 10) {
        const quantitySelect = document.querySelector(`.cart-quantity-select[data-item-id="${itemId}"]`);
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

            let maxQuantity = fallbackMax;
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data && data.data.availableCount !== undefined) {
                    maxQuantity = data.data.availableCount;
                } else {
                    // Fallback: get from boat data
                    const cart = window.CartManager;
                    const boat = cart.getBoatById(boatId);
                    if (boat) maxQuantity = boat.total ?? fallbackMax;
                }
            } else {
                // Fallback: get from boat data
                const cart = window.CartManager;
                const boat = cart.getBoatById(boatId);
                if (boat) maxQuantity = boat.total ?? fallbackMax;
            }

            // Update dropdown options
            quantitySelect.innerHTML = '';
            for (let i = 1; i <= maxQuantity; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                if (i === currentQuantity) option.selected = true;
                quantitySelect.appendChild(option);
            }
        } catch (e) {
            console.error('Error updating quantity dropdown:', e);
            // Keep existing options on error
        }
    }

    async function updateCartEditQuantityDropdown(boatId, startDate, endDate, currentQuantity = 1) {
        const quantitySelect = document.getElementById('cartEditQuantity');
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

            let maxQuantity = 10;
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data && data.data.availableCount !== undefined) {
                    maxQuantity = data.data.availableCount;
                } else {
                    // Fallback: get from boat data
                    const cart = window.CartManager;
                    const boat = cart.getBoatById(boatId);
                    if (boat) maxQuantity = boat.total ?? 10;
                }
            } else {
                // Fallback: get from boat data
                const cart = window.CartManager;
                const boat = cart.getBoatById(boatId);
                if (boat) maxQuantity = boat.total ?? 10;
            }

            quantitySelect.innerHTML = '';
            for (let i = 1; i <= maxQuantity; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                if (i === currentQuantity) option.selected = true;
                quantitySelect.appendChild(option);
            }
        } catch (e) {
            console.error('Error updating quantity dropdown:', e);
            // Fallback: show up to 10
            quantitySelect.innerHTML = '';
            for (let i = 1; i <= 10; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                if (i === currentQuantity) option.selected = true;
                quantitySelect.appendChild(option);
            }
        }
    }

    // Listen for cart updates
    window.addEventListener('cartUpdated', window.updateCartUI);

    // When navigating back from checkout, browsers may restore disabled DOM state.
    // pageshow runs on normal load AND bfcache restore.
    window.addEventListener('pageshow', () => {
        try {
            if (typeof window.updateCartUI === 'function') window.updateCartUI();
            resetCheckoutButtonState();
        } catch (e) { /* no-op */ }
    });

    // Validate availability and proceed to checkout
    window.validateAndCheckout = async function () {
        const cart = window.CartManager;
        if (!cart || cart.isEmpty()) {
            showCartNotification('Uw winkelwagen is leeg.', 'warning');
            return;
        }

        const items = cart.getItems();
        const checkoutBtn = document.querySelector('.cart-checkout-btn');

        // Show loading state
        if (checkoutBtn) {
            checkoutBtn.disabled = true;
            checkoutBtn.textContent = 'Controleren...';
        }

        try {
            // Determine endpoint (shared env detection)
            const endpoint = (window.AppConfig && typeof window.AppConfig.detectServerEndpoint === 'function')
                ? window.AppConfig.detectServerEndpoint('booking-handler.php')
                : (window.location.origin + '/admin/booking-handler.php');

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'validateCartAvailability',
                    items: items.map(item => ({
                        boatId: item.boatId,
                        boatName: item.boatName,
                        startDate: item.startDate,
                        endDate: item.endDate
                    }))
                })
            });

            let result;
            try {
                result = await response.json();
            } catch (e) {
                // If response is not valid JSON, show generic error
                console.error('Invalid JSON response:', e);
                showCartNotification('Er ging iets mis bij het controleren van beschikbaarheid. Probeer het opnieuw.', 'error');
                if (checkoutBtn) {
                    checkoutBtn.disabled = false;
                    checkoutBtn.textContent = 'Afrekenen';
                }
                return;
            }

            if (!response.ok || !result.success) {
                // Show error with unavailable items using styled notification
                let message = 'Er zijn boten niet meer beschikbaar.';
                
                if (result.unavailableItems && result.unavailableItems.length > 0) {
                    const names = result.unavailableItems.map(i => i.boatName || i.boatId).join(', ');
                    message = `Helaas zijn de volgende boot(en) inmiddels niet meer beschikbaar: ${names}. Verwijder deze uit uw winkelwagen en probeer het opnieuw.`;
                } else if (result.message) {
                    message = result.message;
                }
                
                console.error('Checkout validation failed:', result);
                showCartNotification(message, 'error');
                // Restore button state on error
                if (checkoutBtn) {
                    checkoutBtn.disabled = false;
                    checkoutBtn.textContent = 'Afrekenen';
                }
                return;
            }

            // All items available - proceed to checkout
            // Simple path resolution that works from any page
            const currentPath = window.location.pathname;
            
            // If we're in /pages/ directory, use relative path
            // Otherwise, use absolute path from root
            let checkoutPath;
            if (currentPath.includes('/pages/') && currentPath.split('/pages/')[1]) {
                // Already in pages directory
                checkoutPath = 'checkout.php';
            } else {
                // Use absolute path from site root
                checkoutPath = '/pages/checkout.php';
            }
            
            // Redirect to checkout
            window.location.href = checkoutPath;

        } catch (error) {
            console.error('Validation error:', error);
            const errorMessage = 'Er ging iets mis bij het controleren van beschikbaarheid. Probeer het opnieuw.';
            showCartNotification(errorMessage, 'error');
            // Restore button state on error
            if (checkoutBtn) {
                checkoutBtn.disabled = false;
                checkoutBtn.textContent = 'Afrekenen';
            }
        }
    };

    // Helper function to show notifications (uses shared.js notification if available, otherwise creates one)
    function showCartNotification(message, type = 'info') {
        // Ensure we have a message
        if (!message || message.trim() === '') {
            message = type === 'error' ? 'Er is een fout opgetreden.' : 'Melding';
        }
        
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
        msg.textContent = message || 'Melding';
        
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

} // End of else guard for duplicate CartManager

// Ensure updateCartItemQuantity is always available (even if CartManager was already defined)
if (typeof window.updateCartItemQuantity === 'undefined') {
    window.updateCartItemQuantity = async function (itemId, newQuantity) {
        console.log('updateCartItemQuantity (fallback) called:', itemId, newQuantity);
        if (window.CartManager && typeof window.updateCartItemQuantity !== 'undefined') {
            // Use the main implementation if available
            return window.updateCartItemQuantity(itemId, newQuantity);
        }
        alert('Winkelwagen functionaliteit is nog niet geladen. Ververs de pagina.');
    };
}
