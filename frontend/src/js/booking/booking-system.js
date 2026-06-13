// ========================================================================
// BOOKING SYSTEM JAVASCRIPT
// Handles booking form submission and modal interactions
// ========================================================================

class BookingSystem {
    constructor() {
        this.currentBooking = null;
        this.boatsStorageKey = 'nijenhuis_boats';
        // Always use PHP handler (Booking Handler)
        this.adminEndpoint = '/admin/booking-handler.php';
        this.init();
    }

    init() {
        // Ensure booking modal exists
        this.ensureBookingModalExists();
        this.setupEventListeners();
        // React to boat data changes across tabs and within the same window
        window.addEventListener('storage', (e) => {
            if (e.key === this.boatsStorageKey) {
                this.updateBookingSummary();
            }
        });
        window.addEventListener('boatsUpdated', () => {
            this.updateBookingSummary();
        });
        // Reset button state when cart sidebar closes
        window.addEventListener('cartSidebarClosed', () => {
            this.resetButtonState();
        });
    }
    
    /**
     * Ensure booking modal HTML structure exists
     * Creates it dynamically if not found in the DOM
     */
    ensureBookingModalExists() {
        let modal = document.getElementById('bookingModal');
        if (modal) {
            // Modal exists, ensure it has required elements
            this.ensureModalElements();
            return;
        }
        
        // Create modal structure
        modal = document.createElement('div');
        modal.id = 'bookingModal';
        modal.className = 'booking-modal';
        const adminPctRaw =
            typeof window !== 'undefined' && typeof window.CHECKOUT_ADMIN_FEE_PERCENT === 'number'
                ? window.CHECKOUT_ADMIN_FEE_PERCENT
                : 4;
        const adminPctVal =
            adminPctRaw === Math.floor(adminPctRaw) ? Math.floor(adminPctRaw) : adminPctRaw;
        const adminFeeParamsAttr = JSON.stringify({ percent: adminPctVal });
        modal.innerHTML = `
            <div class="booking-modal-content">
                <div class="booking-modal-header">
                    <h2 id="modalTitle">Reservering Voltooien</h2>
                    <button class="booking-modal-close" id="closeBookingModal" aria-label="Sluiten">&times;</button>
                </div>
                <div class="booking-modal-body">
                    <div id="availabilityCheck" class="availability-check hidden">
                        <div class="loading-spinner">
                            <div class="spinner"></div>
                            <p>Beschikbaarheid controleren...</p>
                        </div>
                    </div>
                    <form id="bookingDetailsForm" class="booking-details-form hidden">
                        <div class="booking-summary">
                            <h3>Reserveringsoverzicht</h3>
                            <div class="summary-item">
                                <span class="summary-label">Datum:</span>
                                <span class="summary-value" id="summaryDate">-</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Boot:</span>
                                <span class="summary-value" id="summaryBoat">-</span>
                            </div>
                        </div>
                        <p class="admin-fee-disclosure booking-modal__admin-fee-note" data-i18n="admin_fee_disclosure_note" data-i18n-params='${adminFeeParamsAttr}'></p>
                        <div class="form-group">
                            <label for="rentalEndDate">Einddatum (optioneel)</label>
                            <input type="date" id="rentalEndDate" name="rentalEndDate">
                        </div>
                        <div class="form-group" id="engineOptionContainer" style="display: none;">
                            <label class="checkbox-container">
                                <input type="checkbox" id="engineOption" name="engineOption" value="with">
                                <span class="checkmark"></span>
                                <span class="label-text">Met buitenboordmotor (+ meerprijs)</span>
                            </label>
                        </div>
                        <div class="form-actions">
                            <button type="button" id="addToCartBtn" class="btn btn-secondary">🛒 Toevoegen aan winkelwagen</button>
                            <button type="button" id="directCheckoutBtn" class="btn btn-primary">💳 Direct Afrekenen</button>
                            <button type="button" id="cancelBooking" class="btn btn-outline">Annuleren</button>
                        </div>
                    </form>
                    <div id="bookingSuccess" class="booking-success hidden">
                        <div class="success-icon">✅</div>
                        <h3>Reservering Geslaagd!</h3>
                        <p>Uw reservering is bevestigd.</p>
                        <div class="booking-id">
                            <strong>Reservering ID:</strong> <span id="bookingId"></span>
                        </div>
                        <button type="button" id="closeSuccessModal" class="btn btn-primary">Sluiten</button>
                    </div>
                    <div id="bookingError" class="booking-error hidden">
                        <div class="error-icon">❌</div>
                        <h3>Fout</h3>
                        <p id="errorMessage">Er is een fout opgetreden.</p>
                        <button type="button" id="retryBooking" class="btn btn-primary">Opnieuw proberen</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        if (typeof window.refreshI18n === 'function') {
            window.refreshI18n();
        }
        this.ensureModalElements();
    }
    
    /**
     * Ensure all required modal elements exist and are wired up
     */
    ensureModalElements() {
        // Wire up add to cart button if it exists
        const addToCartBtn = document.getElementById('addToCartBtn');
        if (addToCartBtn && !addToCartBtn.dataset.wired) {
            addToCartBtn.addEventListener('click', () => this.handleAddToCart());
            addToCartBtn.dataset.wired = 'true';
        }
        
        // Wire up direct checkout button if it exists
        const directCheckoutBtn = document.getElementById('directCheckoutBtn');
        if (directCheckoutBtn && !directCheckoutBtn.dataset.wired) {
            directCheckoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleDirectCheckout();
            });
            directCheckoutBtn.dataset.wired = 'true';
        }
    }

    setupEventListeners() {
        // Booking form submission
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.addEventListener('submit', (e) => this.handleBookingFormSubmit(e));
        }

        // Modal close buttons
        const closeModal = document.getElementById('closeBookingModal');
        const cancelBooking = document.getElementById('cancelBooking');
        const closeSuccessModal = document.getElementById('closeSuccessModal');
        const retryBooking = document.getElementById('retryBooking');

        if (closeModal) closeModal.addEventListener('click', () => this.closeModal());
        if (cancelBooking) cancelBooking.addEventListener('click', () => this.closeModal());
        if (closeSuccessModal) closeSuccessModal.addEventListener('click', () => this.closeModal());
        if (retryBooking) retryBooking.addEventListener('click', () => this.retryBooking());

        // Close modal on outside click
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) this.closeModal();
            });
        }

        // Booking details form submission
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        if (bookingDetailsForm) {
            bookingDetailsForm.addEventListener('submit', (e) => this.handleBookingDetailsSubmit(e));
            
            // Wire up direct checkout button if it exists
            const directCheckoutBtn = document.getElementById('directCheckoutBtn');
            if (directCheckoutBtn) {
                directCheckoutBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.handleDirectCheckout();
                });
            }
        }
    }

    handleBookingFormSubmit(e) {
        e.preventDefault();

        const submitBtn = e.target.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.dataset.originalText = submitBtn.textContent;
            submitBtn.textContent = 'Even geduld...';
        }

        const formData = new FormData(e.target);
        const date = formData.get('date');
        const boatType = formData.get('boatType');

        if (!date || !boatType) {
            this.showError('Vul alle verplichte velden in.');
            this.resetButtonState();
            return;
        }

        // Store current booking data
        this.currentBooking = {
            date: date,
            boatType: boatType
        };

        // Show modal and check availability
        this.showModal();
        this.checkAvailability(date, boatType);
    }

    resetButtonState() {
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            const submitBtn = bookingForm.querySelector('button[type="submit"]');
            if (submitBtn && submitBtn.disabled) {
                submitBtn.disabled = false;
                if (submitBtn.dataset.originalText) {
                    submitBtn.textContent = submitBtn.dataset.originalText;
                }
            }
        }
    }

    async checkAvailability(date, boatType) {
        try {
            // Show loading state
            this.showLoadingState();

            // Check if boat is available on the selected date
            const isAvailable = await this.isBoatAvailable(date, boatType);

            this.resetButtonState();

            if (isAvailable) {
                this.showBookingForm();
            } else {
                this.showUnavailableMessage(date, boatType);
            }
        } catch (error) {
            console.error('Error checking availability:', error);
            this.showError('Er is een fout opgetreden bij het controleren van de beschikbaarheid.');
            this.resetButtonState();
        }
    }

    async isBoatAvailable(date, boatType) {
        try {
            // Use secure availability endpoint without exposing bookings
            const response = await fetch(this.adminEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action: 'checkAvailabilityPublic', date, boatType })
            });
            if (response.ok) {
                const data = await response.json();
                if (data && data.success && typeof data.available === 'boolean') return data.available;
                if (data && data.data && typeof data.data.available === 'boolean') return data.data.available;
            }
            // If unavailable or error, do not block booking flow
            return true;
        } catch (error) {
            console.error('Error checking availability:', error);
            return true;
        }
    }

    showLoadingState() {
        const availabilityCheck = document.getElementById('availabilityCheck');
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        const bookingSuccess = document.getElementById('bookingSuccess');
        const bookingError = document.getElementById('bookingError');

        if (availabilityCheck) availabilityCheck.classList.remove('hidden');
        if (bookingDetailsForm) bookingDetailsForm.classList.add('hidden');
        if (bookingSuccess) bookingSuccess.classList.add('hidden');
        if (bookingError) bookingError.classList.add('hidden');
    }

    showBookingForm() {
        // Skip availability success popup; calendar handles availability
        const availabilityCheck = document.getElementById('availabilityCheck');
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');

        if (availabilityCheck) {
            while (availabilityCheck.firstChild) {
                availabilityCheck.removeChild(availabilityCheck.firstChild);
            }
            availabilityCheck.classList.add('hidden');
        }

        if (bookingDetailsForm) {
            bookingDetailsForm.classList.remove('hidden');
            
            // Ensure direct checkout button exists and is wired up
            this.ensureDirectCheckoutButton();
        }
    }
    
    /**
     * Ensure direct checkout button exists in booking details form
     * Creates it if it doesn't exist and wires up the event handler
     */
    ensureDirectCheckoutButton() {
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        if (!bookingDetailsForm) return;
        
        // Check if button already exists
        let directCheckoutBtn = document.getElementById('directCheckoutBtn');
        
        if (!directCheckoutBtn) {
            // Find form actions container or create one
            let formActions = bookingDetailsForm.querySelector('.form-actions');
            if (!formActions) {
                // Look for submit button to add button near it
                const submitBtn = bookingDetailsForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    formActions = submitBtn.parentElement;
                } else {
                    // Create form actions container
                    formActions = document.createElement('div');
                    formActions.className = 'form-actions';
                    bookingDetailsForm.appendChild(formActions);
                }
            }
            
            // Create direct checkout button
            directCheckoutBtn = document.createElement('button');
            directCheckoutBtn.id = 'directCheckoutBtn';
            directCheckoutBtn.type = 'button';
            directCheckoutBtn.className = 'btn btn-primary';
            directCheckoutBtn.textContent = '💳 Direct Afrekenen';
            directCheckoutBtn.style.cssText = 'margin-left: 10px;';
            
            // Insert before submit button if it exists, otherwise append
            const submitBtn = bookingDetailsForm.querySelector('button[type="submit"]');
            if (submitBtn && submitBtn.parentElement === formActions) {
                formActions.insertBefore(directCheckoutBtn, submitBtn);
            } else {
                formActions.appendChild(directCheckoutBtn);
            }
        }
        
        // Wire up event handler (remove old listeners first)
        const newBtn = directCheckoutBtn.cloneNode(true);
        directCheckoutBtn.parentNode.replaceChild(newBtn, directCheckoutBtn);
        newBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.handleDirectCheckout();
        });
    }

    handleAddToCart() {
        if (!this.currentBooking) return;

        // Add to cart
        const startDate = this.currentBooking.date;
        const endDate = document.getElementById('rentalEndDate') ? document.getElementById('rentalEndDate').value : startDate;
        const engineSelect = document.getElementById('engineOption');
        const useMotor = engineSelect && engineSelect.value === 'with';
        const quantitySelect = document.getElementById('boatQuantity');
        const quantity = quantitySelect ? parseInt(quantitySelect.value || '1') : 1;

        // Assuming CartManager is globally available from cart.js
        if (window.CartManager) {
            // addItem is now async
            window.CartManager.addItem(this.currentBooking.boatType, startDate, endDate, useMotor, quantity)
                .then(success => {
                    if (success) {
                        // Reset button state immediately so buttons are ready when cart closes
                        this.resetButtonState();
                        this.closeModal(true);
                        // Notification is now handled in cart.js
                        window.toggleCartSidebar(); // Open cart sidebar to show it happened
                    } else {
                        // Error alert is handled in cart.js or here if needed, 
                        // but cart.js alert covers network/backend errors.
                        // We can leave this empty or handle specific cases if cart.js returns false without alert.
                    }
                });
        }
    }

    handleDirectCheckout() {
        if (!this.currentBooking) return;

        const startDate = this.currentBooking.date;
        const endDate = document.getElementById('rentalEndDate') ? document.getElementById('rentalEndDate').value : startDate;
        const engineSelect = document.getElementById('engineOption');
        const useMotor = engineSelect && engineSelect.value === 'with';
        const quantitySelect = document.getElementById('boatQuantity');
        const quantity = quantitySelect ? parseInt(quantitySelect.value || '1') : 1;

        if (window.CartManager) {
            // addItem is now async
            window.CartManager.addItem(this.currentBooking.boatType, startDate, endDate, useMotor, quantity)
                .then(success => {
                    if (success) {
                        // Reset button state before redirecting
                        this.resetButtonState();
                        window.location.href = '/pages/checkout.php';
                    }
                });
        }
    }

    async showUnavailableMessage(date, boatType) {
        const availabilityCheck = document.getElementById('availabilityCheck');
        const boatName = await this.getBoatDisplayName(boatType);
        const formattedDate = new Date(date).toLocaleDateString('nl-NL', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        if (availabilityCheck) {
            // Clear existing content safely
            while (availabilityCheck.firstChild) {
                availabilityCheck.removeChild(availabilityCheck.firstChild);
            }

            // Create unavailable message safely
            const messageDiv = document.createElement('div');
            messageDiv.className = 'unavailable-message';

            const iconDiv = document.createElement('div');
            iconDiv.className = 'unavailable-icon';
            iconDiv.textContent = '❌';

            const heading = document.createElement('h3');
            heading.textContent = 'Niet beschikbaar';

            const description = document.createElement('p');
            description.textContent = `De ${boatName} is helaas niet beschikbaar op ${formattedDate}.`;

            const suggestion = document.createElement('p');
            suggestion.textContent = 'Probeer een andere datum of een ander type boot.';

            const closeButton = document.createElement('button');
            closeButton.className = 'btn btn-outline';
            closeButton.textContent = 'Sluiten';
            closeButton.onclick = () => this.closeModal();

            messageDiv.appendChild(iconDiv);
            messageDiv.appendChild(heading);
            messageDiv.appendChild(description);
            messageDiv.appendChild(suggestion);
            messageDiv.appendChild(closeButton);

            availabilityCheck.appendChild(messageDiv);
        }
    }

    async updateBookingSummary() {
        if (!this.currentBooking) return;

        const summaryDate = document.getElementById('summaryDate');
        const summaryBoat = document.getElementById('summaryBoat');

        if (summaryDate) {
            const date = new Date(this.currentBooking.date);
            summaryDate.textContent = date.toLocaleDateString('nl-NL', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        if (summaryBoat) {
            summaryBoat.textContent = await this.getBoatDisplayName(this.currentBooking.boatType);
        }
    }

    async getBoatDisplayName(boatType) {
        // Use BoatDataService as single source of truth
        try {
            if (window.BoatDataService) {
                return await window.BoatDataService.getBoatDisplayName(boatType);
            }
            // Fallback to localStorage if BoatDataService not available
            const storedBoats = localStorage.getItem(this.boatsStorageKey);
            if (storedBoats) {
                const boats = JSON.parse(storedBoats);
                const boat = boats.find(b => b.id === boatType);
                if (boat && boat.name) return boat.name;
            }
        } catch (e) {
            // ignore and fallback
        }
        // Fallback map if no admin data available
        const fallback = {
            'classic-tender-720': 'Classic tender 720 (10/12 pers)',
            'electrosloop-10': 'Electrosloep 10 pers',
            'classic-tender-570': 'Classic tender 570 (8 pers)',
            'electrosloop-8': 'Electrosloep 8 pers',
            'sailboat-4-5': 'Zeilboot 4/5 pers',
            'sailpunter-3-4': 'Zeilpunter 3/4 pers',
            'electroboat-5': 'Electroboot 5 pers',
            'canoe-3': 'Canadese kano 3 pers',
            'kayak-2': 'Kajak 2 pers',
            'kayak-1': 'Kajak 1 pers',
            'sup-board': 'SUP board 1 pers'
        };
        return fallback[boatType] || boatType;
    }

    async handleBookingDetailsSubmit(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        let bookingData = {
            date: this.currentBooking.date,
            boatType: this.currentBooking.boatType,
            customerName: formData.get('customerName'),
            customerEmail: formData.get('customerEmail'),
            customerPhone: formData.get('customerPhone'),
            notes: formData.get('customerNotes') || '',
            formType: 'booking'
        };

        // Validate and sanitize booking data using security utilities
        if (typeof window.SecurityUtils !== 'undefined') {
            const validation = window.SecurityUtils.validateBookingData(bookingData);
            if (!validation.isValid) {
                this.showError(validation.errors.join(', '));
                return;
            }
            // Use sanitized data
            bookingData = validation.sanitizedData;
        } else {
            // Fallback validation if SecurityUtils not loaded
            if (!bookingData.customerName || !bookingData.customerEmail || !bookingData.customerPhone) {
                this.showError('Vul alle verplichte velden in.');
                return;
            }
        }

        try {
            // Show loading state
            this.showLoadingState();

            const response = await fetch(this.adminEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(bookingData)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Process response
            const result = await response.json();

            if (result.success) {
                this.showSuccessMessage(result.bookingId);
            } else {
                this.showError(result.message || 'Er is een fout opgetreden bij het verwerken van uw reservering.');
            }
        } catch (error) {
            console.error('Error submitting booking:', error);
            this.showError(`Er is een fout opgetreden bij het verwerken van uw reservering. (${error.message})`);
        }
    }

    showSuccessMessage(bookingId) {
        const availabilityCheck = document.getElementById('availabilityCheck');
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        const bookingSuccess = document.getElementById('bookingSuccess');
        const bookingError = document.getElementById('bookingError');

        if (availabilityCheck) availabilityCheck.classList.add('hidden');
        if (bookingDetailsForm) bookingDetailsForm.classList.add('hidden');
        if (bookingError) bookingError.classList.add('hidden');
        if (bookingSuccess) bookingSuccess.classList.remove('hidden');

        // Set booking ID
        const bookingIdElement = document.getElementById('bookingId');
        if (bookingIdElement) {
            bookingIdElement.textContent = bookingId;
        }

        // Reset form
        this.resetForms();
    }

    showError(message) {
        const availabilityCheck = document.getElementById('availabilityCheck');
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        const bookingSuccess = document.getElementById('bookingSuccess');
        const bookingError = document.getElementById('bookingError');
        const errorMessage = document.getElementById('errorMessage');

        if (availabilityCheck) availabilityCheck.classList.add('hidden');
        if (bookingDetailsForm) bookingDetailsForm.classList.add('hidden');
        if (bookingSuccess) bookingSuccess.classList.add('hidden');
        if (bookingError) bookingError.classList.remove('hidden');

        if (errorMessage) {
            errorMessage.textContent = message;
        }
    }

    retryBooking() {
        if (this.currentBooking) {
            this.checkAvailability(this.currentBooking.date, this.currentBooking.boatType);
        }
    }

    showModal() {
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal(keepValues = false) {
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Reset everything
        if (!keepValues) {
            this.resetForms();
            this.currentBooking = null;
        }

        // Reset modal content
        this.resetModalContent();
    }

    resetForms() {
        const bookingForm = document.getElementById('bookingForm');
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');

        if (bookingForm) bookingForm.reset();
        if (bookingDetailsForm) bookingDetailsForm.reset();
    }

    resetModalContent() {
        const availabilityCheck = document.getElementById('availabilityCheck');
        if (availabilityCheck) {
            // Clear existing content safely
            while (availabilityCheck.firstChild) {
                availabilityCheck.removeChild(availabilityCheck.firstChild);
            }

            // Create loading spinner safely
            const spinnerDiv = document.createElement('div');
            spinnerDiv.className = 'loading-spinner';

            const spinner = document.createElement('div');
            spinner.className = 'spinner';

            const text = document.createElement('p');
            text.textContent = 'Beschikbaarheid controleren...';

            spinnerDiv.appendChild(spinner);
            spinnerDiv.appendChild(text);
            availabilityCheck.appendChild(spinnerDiv);
        }
    }
}

// Initialize the booking system when the page loads
document.addEventListener('DOMContentLoaded', () => {
    new BookingSystem();
}); 