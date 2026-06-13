/**
 * Booking Page Logic
 * Handles the standalone booking form.
 */
class BookingPage {
    constructor() {
        this.boats = [];
        this.currentLang = 'nl';
        this.locales = { nl: 'nl-NL', en: 'en-GB', de: 'de-DE' };
        document.addEventListener('change', (e) => {
            if (e.target && e.target.name === 'bookingPaymentMethod') {
                this.onBookingPaymentMethodChange();
            }
        });
        this.init();
    }

    async init() {
        this.detectLanguage();
        this.setupDatePicker();
        await this.loadBoats();
        this.loadBookingData();
        this.setupEventListeners();
        
        // Ensure quantity field is visible on initial load
        await this.updateQuantityDropdown();

        if (window.BoatDataService) {
            window.BoatDataService.subscribe((boats) => {
                this.boats = boats;
                this.populateBoatSelect(boats);
                this.updateOptionsVisibility();
                this.updateSummary();
            });
        }

        this.loadIdealIssuersBooking();
        this.onBookingPaymentMethodChange();
    }

    async loadIdealIssuersBooking() {
        const sel = document.getElementById('bookingIdealIssuer');
        if (!sel || !window.MollieIdealCard) return;
        try {
            const data = await window.MollieIdealCard.fetchIdealIssuers('');
            if (!data.success || !Array.isArray(data.issuers)) return;
            const placeholder = sel.querySelector('option[value=""]');
            const phText = placeholder ? placeholder.textContent : '';
            sel.innerHTML = '';
            const opt0 = document.createElement('option');
            opt0.value = '';
            opt0.textContent = phText || '—';
            sel.appendChild(opt0);
            data.issuers.forEach((issuer) => {
                const o = document.createElement('option');
                o.value = issuer.id;
                o.textContent = issuer.name || issuer.id;
                sel.appendChild(o);
            });
        } catch (err) {
            console.warn('iDEAL issuers:', err);
        }
    }

    onBookingPaymentMethodChange() {
        const form = document.getElementById('bookingForm');
        if (!form) return;
        const checked = form.querySelector('input[name="bookingPaymentMethod"]:checked');
        const pm = checked ? checked.value : 'ideal';
        const idealWrap = document.getElementById('bookingIdealIssuerWrap');
        if (idealWrap) idealWrap.style.display = pm === 'ideal' ? 'block' : 'none';
    }

    detectLanguage() {
        const storedLang = localStorage.getItem('selected-language');
        if (storedLang && ['nl', 'en', 'de'].includes(storedLang)) {
            this.currentLang = storedLang;
        }
        document.documentElement.lang = this.currentLang;

        window.addEventListener('languageChanged', (e) => {
            if (e.detail && e.detail.language) {
                this.currentLang = e.detail.language;
                this.refreshSeasonMessage();
                this.updateOptionsVisibility();
                this.updateSummary();
            }
        });
    }

    refreshSeasonMessage() {
        if (!this.bookingOpen) {
            const today = new Date();
            const currentMonth = today.getMonth();
            const currentYear = today.getFullYear();

            if (currentMonth >= 10) {
                this.showBookingClosedMessage(currentYear, this.bookingYear);
            } else {
                this.showBookingNotYetOpenMessage(this.bookingYear);
            }
        }
    }

    setupDatePicker() {
        const dateInput = document.getElementById('bookingDate');
        const endDateInput = document.getElementById('bookingEndDate');
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const currentYear = today.getFullYear();
        const currentMonth = today.getMonth();
        const currentDate = today.getDate();

        let bookingYear = currentYear;
        let bookingOpen = false;
        let minDateStr, maxDateStr;

        // Season Logic (Unified via config would be better, but keeping logic for now)
        if (currentMonth <= 9) { // Jan - Oct
            bookingOpen = true;
            bookingYear = currentYear;

            const seasonStart = `${bookingYear}-04-01`;
            const todayStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(currentDate).padStart(2, '0')}`;

            minDateStr = todayStr > seasonStart ? todayStr : seasonStart;
            maxDateStr = `${bookingYear}-10-31`;
        } else {
            bookingOpen = false;
            bookingYear = currentYear + 1;
            minDateStr = `${bookingYear}-04-01`;
            maxDateStr = `${bookingYear}-10-31`;
        }

        this.bookingYear = bookingYear;
        this.bookingOpen = bookingOpen;
        this.seasonStartDate = `${bookingYear}-04-01`;
        this.seasonEndDate = `${bookingYear}-10-31`;
        this.minDate = minDateStr;
        this.maxDate = maxDateStr;

        dateInput.setAttribute('min', minDateStr);
        dateInput.setAttribute('max', maxDateStr);

        if (!dateInput.value) {
            dateInput.value = minDateStr;
        }

        endDateInput.setAttribute('max', maxDateStr);

        if (!bookingOpen) {
            if (currentMonth >= 10) {
                this.showBookingClosedMessage(currentYear, bookingYear);
            } else {
                this.showBookingNotYetOpenMessage(bookingYear);
            }
            dateInput.disabled = true;
            endDateInput.disabled = true;
        }

        dateInput.addEventListener('change', (e) => {
            this.validateSelectedDate(e.target.value);

            if (e.target.value) {
                endDateInput.disabled = false;
                endDateInput.min = e.target.value;

                if (endDateInput.value && endDateInput.value < e.target.value) {
                    endDateInput.value = e.target.value;
                }
                if (!endDateInput.value) {
                    endDateInput.value = e.target.value;
                }
            } else {
                endDateInput.disabled = true;
                endDateInput.value = '';
            }
            this.updateSummary();
        });

        endDateInput.addEventListener('change', (e) => {
            this.updateSummary();
        });

        const urlParams = new URLSearchParams(window.location.search);
        const dateFromUrl = urlParams.get('date');
        const endDateFromUrl = urlParams.get('endDate');

        if (dateFromUrl && bookingOpen) {
            if (dateFromUrl >= minDateStr && dateFromUrl <= maxDateStr) {
                dateInput.value = dateFromUrl;

                endDateInput.disabled = false;
                endDateInput.min = dateFromUrl;

                if (endDateFromUrl && endDateFromUrl >= dateFromUrl && endDateFromUrl <= maxDateStr) {
                    endDateInput.value = endDateFromUrl;
                } else {
                    endDateInput.value = dateFromUrl;
                }

                dateInput.dispatchEvent(new Event('change'));
            }
        }
    }

    validateSelectedDate(dateStr) {
        if (!dateStr) return false;

        const selectedDate = new Date(dateStr + 'T00:00:00');
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const msgs = this.getSeasonMessages()[this.currentLang] || this.getSeasonMessages().nl;

        if (selectedDate < today) {
            this.showDateError(msgs.pastDate);
            document.getElementById('bookingDate').value = '';
            return false;
        }

        if (dateStr < this.seasonStartDate || dateStr > this.seasonEndDate) {
            this.showDateOutOfRangeError(dateStr);
            document.getElementById('bookingDate').value = '';
            return false;
        }

        this.hideDateError();
        return true;
    }

    showDateOutOfRangeError(dateStr) {
        const locale = this.locales[this.currentLang] || 'nl-NL';
        const formattedDate = new Date(dateStr + 'T00:00:00').toLocaleDateString(locale, {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
        const msgs = this.getSeasonMessages()[this.currentLang] || this.getSeasonMessages().nl;
        this.showDateError(msgs.outOfRange(formattedDate));
    }

    showDateError(message) {
        const errorDiv = document.getElementById('errorMessage');
        if (errorDiv) {
            errorDiv.innerHTML = message;
            errorDiv.style.display = 'block';
        }
    }

    hideDateError() {
        const errorDiv = document.getElementById('errorMessage');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }

    getSeasonMessages() {
        return {
            nl: {
                closed: (closedYear, nextYear) => `<strong>Het boekingsseizoen voor ${closedYear} is gesloten.</strong><br>Reserveringen voor seizoen ${nextYear} openen op <strong>1 januari ${nextYear}</strong>.`,
                notOpen: (year) => `<strong>Reserveringen zijn nog niet geopend.</strong><br>Reserveringen voor seizoen ${year} openen op <strong>1 januari ${year}</strong>.`,
                pastDate: 'U kunt geen reservering maken voor een datum in het verleden.',
                wrongYear: (year) => `Boekingen zijn alleen mogelijk voor seizoen ${year}.`,
                outOfSeason: (year) => `Boekingen zijn alleen mogelijk van 1 januari tot 31 oktober ${year}.`,
                notYetOpen: (year) => `Reserveringen voor seizoen ${year} openen op 1 januari ${year}.`,
                outOfRange: (date) => `De geselecteerde datum (${date}) valt buiten het boekingsseizoen.`
            },
            en: {
                closed: (closedYear, nextYear) => `<strong>The booking season for ${closedYear} is closed.</strong><br>Reservations for season ${nextYear} open on <strong>1 January ${nextYear}</strong>.`,
                notOpen: (year) => `<strong>Reservations are not yet open.</strong><br>Reservations for season ${year} open on <strong>1 January ${year}</strong>.`,
                pastDate: 'You cannot make a reservation for a date in the past.',
                wrongYear: (year) => `Bookings are only possible for season ${year}.`,
                outOfSeason: (year) => `Bookings are only possible from 1 January to 31 October ${year}.`,
                notYetOpen: (year) => `Reservations for season ${year} open on 1 January ${year}.`,
                outOfRange: (date) => `The selected date (${date}) is outside the booking season.`
            },
            de: {
                closed: (closedYear, nextYear) => `<strong>Die Buchungssaison für ${closedYear} ist geschlossen.</strong><br>Reservierungen für Saison ${nextYear} öffnen am <strong>1. Januar ${nextYear}</strong>.`,
                notOpen: (year) => `<strong>Reservierungen sind noch nicht geöffnet.</strong><br>Reservierungen für Saison ${year} öffnen am <strong>1. Januar ${year}</strong>.`,
                pastDate: 'Sie können keine Reservierung für ein vergangenes Datum vornehmen.',
                wrongYear: (year) => `Buchungen sind nur für Saison ${year} möglich.`,
                outOfSeason: (year) => `Buchungen sind nur vom 1. Januar bis 31. Oktober ${year} möglich.`,
                notYetOpen: (year) => `Reservierungen für Saison ${year} öffnen am 1. Januar ${year}.`,
                outOfRange: (date) => `Das ausgewählte Datum (${date}) liegt außerhalb der Buchungssaison.`
            }
        };
    }

    showBookingClosedMessage(closedYear, nextYear) {
        const errorDiv = document.getElementById('errorMessage');
        if (errorDiv) {
            const msgs = this.getSeasonMessages()[this.currentLang] || this.getSeasonMessages().nl;
            errorDiv.innerHTML = msgs.closed(closedYear, nextYear);
            errorDiv.style.display = 'block';
            errorDiv.style.background = '#fff3cd';
            errorDiv.style.color = '#856404';
        }
    }

    showBookingNotYetOpenMessage(bookingYear) {
        const errorDiv = document.getElementById('errorMessage');
        if (errorDiv) {
            const msgs = this.getSeasonMessages()[this.currentLang] || this.getSeasonMessages().nl;
            errorDiv.innerHTML = msgs.notOpen(bookingYear);
            errorDiv.style.display = 'block';
            errorDiv.style.background = '#d1ecf1';
            errorDiv.style.color = '#0c5460';
        }
    }

    async loadBoats() {
        const boatSelect = document.getElementById('boatType');
        const selectTexts = {
            nl: '-- Selecteer een boot --',
            en: '-- Select a boat --',
            de: '-- Boot wählen --'
        };
        boatSelect.innerHTML = `<option value="">${selectTexts[this.currentLang] || selectTexts.nl}</option>`;

        let boats = [];
        try {
            if (window.BoatDataService) {
                boats = await window.BoatDataService.getAllBoats();
            } else {
                const stored = localStorage.getItem('nijenhuis_boats');
                if (stored) boats = JSON.parse(stored);
            }
        } catch (e) {
            console.error('Error loading boats:', e);
        }

        this.boats = boats;
        this.populateBoatSelect(boats);

        const urlParams = new URLSearchParams(window.location.search);
        const boatTypeFromUrl = urlParams.get('boatType');
        if (boatTypeFromUrl) {
            boatSelect.value = boatTypeFromUrl;
            setTimeout(() => this.updateSummary(), 0);
        }
    }

    populateBoatSelect(boats) {
        const boatSelect = document.getElementById('boatType');
        const currentValue = boatSelect.value;
        const selectTexts = {
            nl: '-- Selecteer een boot --',
            en: '-- Select a boat --',
            de: '-- Boot wählen --'
        };
        boatSelect.innerHTML = `<option value="">${selectTexts[this.currentLang] || selectTexts.nl}</option>`;

        if (boats && boats.length) {
            const sortedBoats = [...boats].sort((a, b) => {
                const categoryOrder = ['electric', 'sailing', 'canoe', 'sup'];
                const categoryA = categoryOrder.indexOf(a.category || 'other');
                const categoryB = categoryOrder.indexOf(b.category || 'other');
                if (categoryA !== categoryB) return categoryA - categoryB;
                return (a.orderId || 999) - (b.orderId || 999);
            });

            sortedBoats.forEach(boat => {
                const option = document.createElement('option');
                option.value = boat.id;
                option.textContent = boat.name;
                option.dataset.pricePerDay = boat.pricePerDay || 0;
                option.dataset.pricing = JSON.stringify(boat.pricing || {});
                option.dataset.pricingWithEngine = JSON.stringify(boat.pricingWithEngine || null);
                option.dataset.availableDays = JSON.stringify(boat.availableDays || [1, 2, 3, 4, 5, 6, 7]);
                boatSelect.appendChild(option);
            });

            if (currentValue) boatSelect.value = currentValue;
        }
    }

    loadBookingData() {
        this.updateSummary();
    }

    getBoatName(boatType) {
        if (this.boats && this.boats.length > 0) {
            const boat = this.boats.find(b => b.id === boatType);
            if (boat && boat.name) return boat.name;
        }
        return boatType;
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const locale = this.locales[this.currentLang] || 'nl-NL';
        return date.toLocaleDateString(locale, {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }

    getDurationText(days) {
        const texts = {
            nl: { one: '1 Dag', many: 'Dagen' },
            en: { one: '1 Day', many: 'Days' },
            de: { one: '1 Tag', many: 'Tage' }
        };
        const t = texts[this.currentLang] || texts.nl;
        return days === 1 ? t.one : `${days} ${t.many}`;
    }

    getStatusText(available) {
        const texts = {
            nl: { available: 'Beschikbaar ✓', select: 'Selecteer bovenstaande opties' },
            en: { available: 'Available ✓', select: 'Please select options above' },
            de: { available: 'Verfügbar ✓', select: 'Bitte wählen Sie die obigen Optionen' }
        };
        const t = texts[this.currentLang] || texts.nl;
        return available ? t.available : t.select;
    }

    updateOptionsVisibility() {
        const boatSelect = document.getElementById('boatType');
        const selectedOption = boatSelect.options[boatSelect.selectedIndex];
        const optionsContainer = document.getElementById('optionsContainer');
        const useMotorCheckbox = document.getElementById('useMotor');
        const boatId = boatSelect.value;

        // Only Zeilboot (sailboat-4-5) offers optional outboard; ignore stray pricingWithEngine on other boats
        if (boatId !== 'sailboat-4-5') {
            optionsContainer.style.display = 'none';
            useMotorCheckbox.checked = false;
            return;
        }

        if (selectedOption && selectedOption.dataset.pricingWithEngine) {
            const pricingWithEngine = JSON.parse(selectedOption.dataset.pricingWithEngine);
            if (pricingWithEngine) {
                optionsContainer.style.display = 'block';
                return;
            }
        }

        optionsContainer.style.display = 'none';
        useMotorCheckbox.checked = false;
    }

    async updateQuantityDropdown() {
        const boatSelect = document.getElementById('boatType');
        const quantitySelect = document.getElementById('boatQuantity');
        const quantityGroup = document.getElementById('quantityGroup');
        const dateInput = document.getElementById('bookingDate');
        const endDateInput = document.getElementById('bookingEndDate');

        if (!boatSelect || !quantitySelect || !quantityGroup) return;

        // Always show the quantity field
        quantityGroup.style.display = 'flex';

        const boatId = boatSelect.value;
        const startDate = dateInput.value;
        const endDate = endDateInput.value || startDate;

        // If no boat or date selected, show default options and disable
        if (!boatId || !startDate) {
            quantitySelect.innerHTML = '<option value="1">1</option>';
            quantitySelect.disabled = true;
            return;
        }

        // Get boat info
        const boat = this.boats.find(b => b.id === boatId);
        if (!boat) {
            quantitySelect.innerHTML = '<option value="1">1</option>';
            quantitySelect.disabled = true;
            return;
        }

        // Enable the select
        quantitySelect.disabled = false;

        // Get available count for date range (this is already total - booked)
        const availableCount = await this.getAvailableBoatCount(boatId, startDate, endDate);
        const maxQuantity = availableCount;

        // Populate dropdown
        quantitySelect.innerHTML = '';
        if (maxQuantity <= 0) {
            quantitySelect.innerHTML = '<option value="0">Niet beschikbaar</option>';
            quantitySelect.disabled = true;
            return;
        }

        for (let i = 1; i <= maxQuantity; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            quantitySelect.appendChild(option);
        }

        // Set default to 1 if current value is invalid
        if (parseInt(quantitySelect.value) > maxQuantity || !quantitySelect.value) {
            quantitySelect.value = '1';
        }
    }

    async getAvailableBoatCount(boatId, startDate, endDate) {
        try {
            // Determine endpoint
            const endpoint = (window.location.protocol === 'file:' || window.location.hostname === '')
                ? 'http://localhost:8000/admin/booking-handler.py'
                : `${window.location.origin}/admin/booking-handler.php`;

            const response = await fetch(`${endpoint}?action=checkAvailability&boatType=${encodeURIComponent(boatId)}&date=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}`, {
                method: 'GET',
                credentials: 'include',
                headers: { 'Accept': 'application/json' }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    if (data.data.availableCount !== undefined) {
                        return data.data.availableCount;
                    }
                    if (data.data.available === true && data.data.availableCount !== undefined) {
                        return data.data.availableCount;
                    }
                }
            }
        } catch (e) {
            console.error('Error checking availability:', e);
        }

        // Fallback: get boat total from local data
        const boat = this.boats.find(b => b.id === boatId);
        return boat ? (boat.total || 1) : 1;
    }

    setupEventListeners() {
        document.getElementById('bookingForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitBooking();
        });

        const quantitySelect = document.getElementById('boatQuantity');
        if (quantitySelect) {
            quantitySelect.addEventListener('change', () => {
                this.updateSummary();
            });
        }

        document.getElementById('boatType').addEventListener('change', async () => {
            this.updateOptionsVisibility();
            await this.updateQuantityDropdown();
            this.updateSummary();
        });

        document.getElementById('useMotor').addEventListener('change', () => {
            this.updateSummary();
        });

        // Update quantity when dates change
        document.getElementById('bookingDate').addEventListener('change', async () => {
            await this.updateQuantityDropdown();
            this.updateSummary();
        });

        document.getElementById('bookingEndDate').addEventListener('change', async () => {
            await this.updateQuantityDropdown();
            this.updateSummary();
        });
    }

    validateSeasonDate(dateString) {
        const selectedDate = new Date(dateString + 'T00:00:00');
        const selectedYear = selectedDate.getFullYear();
        const selectedMonth = selectedDate.getMonth();
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const msgs = this.getSeasonMessages()[this.currentLang] || this.getSeasonMessages().nl;

        if (selectedDate < today) {
            return { valid: false, message: msgs.pastDate };
        }

        if (selectedYear !== this.bookingYear) {
            return { valid: false, message: msgs.wrongYear(this.bookingYear) };
        }

        if (selectedMonth > 9) { // > Oct
            return { valid: false, message: msgs.outOfSeason(this.bookingYear) };
        }

        if (!this.bookingOpen) {
            return { valid: false, message: msgs.notYetOpen(this.bookingYear) };
        }

        return { valid: true };
    }

    updateSummary() {
        const dateInput = document.getElementById('bookingDate');
        const endDateInput = document.getElementById('bookingEndDate');
        const boatSelect = document.getElementById('boatType');
        const useMotorCheckbox = document.getElementById('useMotor');
        const useMotor = useMotorCheckbox.checked;

        const date = dateInput.value;
        const endDate = endDateInput.value;
        const boatType = boatSelect.value;
        const quantity = parseInt(document.getElementById('boatQuantity')?.value || '1');
        const motorPriceInfo = document.getElementById('motorPriceInfo');

        if (useMotorCheckbox.parentElement.parentElement.parentElement.style.display !== 'none') {
            const texts = {
                nl: 'Toeslag voor motor wordt berekend in totaalprijs',
                en: 'Surcharge for motor is calculated in total price',
                de: 'Aufpreis für Motor wird im Gesamtpreis berechnet'
            };
            motorPriceInfo.textContent = texts[this.currentLang] || texts.nl;
        }

        let numberOfDays = 0;
        if (date && endDate) {
            const start = new Date(date);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            numberOfDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        }

        let dateValid = false;
        if (date) {
            const validationResult = this.validateSeasonDate(date);
            dateValid = validationResult.valid;
        }

        document.getElementById('summaryDate').textContent = date ? this.formatDate(date) : '-';
        document.getElementById('summaryBoatType').textContent = boatType ? this.getBoatName(boatType) : '-';
        document.getElementById('summaryDuration').textContent = numberOfDays > 0 ? this.getDurationText(numberOfDays) : '-';

        const totalPriceDisplay = document.getElementById('totalPriceDisplay');
        const totalPriceAmount = document.getElementById('totalPriceAmount');

        if (boatType && numberOfDays > 0) {
            const price = this.calculatePrice(boatType, numberOfDays, useMotor) * quantity;
            totalPriceAmount.textContent = `€${price.toFixed(2)}`;
            totalPriceDisplay.style.display = 'block';
        } else {
            totalPriceDisplay.style.display = 'none';
        }

        if (date && boatType && numberOfDays > 0 && dateValid) {
            const price = this.calculatePrice(boatType, numberOfDays, useMotor) * quantity;
            document.getElementById('summaryPrice').textContent = `€${price.toFixed(2)}`;
            document.getElementById('summaryStatus').textContent = this.getStatusText(true);
            document.getElementById('summaryStatus').style.color = 'var(--success-color)';
        } else {
            document.getElementById('summaryPrice').textContent = '-';
            document.getElementById('summaryStatus').textContent = this.getStatusText(false);
            document.getElementById('summaryStatus').style.color = 'var(--text-secondary)';
        }
    }

    async submitBooking() {
        const form = document.getElementById('bookingForm');
        const formData = new FormData(form);

        const date = formData.get('bookingDate');
        const endDate = formData.get('bookingEndDate');
        const boatType = formData.get('boatType');
        const quantity = parseInt(formData.get('boatQuantity') || '1');

        const useMotor = document.getElementById('useMotor').checked;

        let numberOfDays = 1;
        if (date && endDate) {
            const start = new Date(date);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            numberOfDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        }

        if (!date || !boatType) {
            const selectErrors = {
                nl: 'Selecteer alstublieft een datum en boottype.',
                en: 'Please select a date and boat type.',
                de: 'Bitte wählen Sie ein Datum und einen Bootstyp.'
            };
            this.showError(selectErrors[this.currentLang] || selectErrors.nl);
            return;
        }

        const validationResult = this.validateSeasonDate(date);
        if (!validationResult.valid) {
            this.showError(validationResult.message);
            return;
        }

        const pmInput = form.querySelector('input[name="bookingPaymentMethod"]:checked');
        const paymentMethod = pmInput ? pmInput.value : 'ideal';

        const bookingData = {
            date: date,
            boatType: boatType,
            numberOfDays: numberOfDays,
            quantity: quantity,
            withEngine: useMotor,
            useMotor: useMotor,
            engineOption: useMotor ? 'with' : 'without',
            customerName: formData.get('customerName'),
            customerEmail: formData.get('customerEmail'),
            customerPhone: formData.get('customerPhone'),
            customerAddress: formData.get('customerAddress'),
            notes: formData.get('specialRequests'),
            status: 'canceled',
            createdAt: new Date().toISOString()
        };

        bookingData.paymentMethod = paymentMethod;
        if (paymentMethod === 'ideal') {
            const iss = formData.get('bookingIdealIssuer');
            if (iss) bookingData.issuer = iss;
        }

        this.showLoading(true);

        try {
            const existingBookings = JSON.parse(localStorage.getItem('nijenhuis_bookings') || '[]');
            bookingData.id = 'booking_' + Date.now();
            existingBookings.push(bookingData);
            localStorage.setItem('nijenhuis_bookings', JSON.stringify(existingBookings));

            await this.createMolliePayment(bookingData);
        } catch (error) {
            console.error('Payment error:', error);
            const paymentErrors = {
                nl: 'Er is een fout opgetreden bij het verwerken van uw betaling. Probeer het opnieuw.',
                en: 'There was an error processing your payment. Please try again.',
                de: 'Bei der Verarbeitung Ihrer Zahlung ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.'
            };
            this.showError(paymentErrors[this.currentLang] || paymentErrors.nl);
            this.showLoading(false);
        }
    }

    async createMolliePayment(bookingData) {
        const result = await createMolliePaymentJS(bookingData);
        if (result._links && result._links.checkout) {
            window.location.href = result._links.checkout.href;
        } else {
            console.error('Mollie API Error:', result);
            throw new Error(result.message || 'No payment URL received');
        }
    }

    calculatePrice(boatType, numberOfDays = 1, withEngine = false) {
        let boats = this.boats;
        if (!boats || boats.length === 0) {
            const stored = localStorage.getItem('nijenhuis_boats');
            if (stored) boats = JSON.parse(stored);
        }

        if (boats && boats.length > 0) {
            const boat = boats.find(b => b.id === boatType);
            if (boat) {
                let usePricingWithEngine = false;
                if (withEngine && boat.pricingWithEngine && Object.keys(boat.pricingWithEngine).length > 0) {
                    usePricingWithEngine = true;
                }

                if (numberOfDays === 1 && boat.pricePerDay && !usePricingWithEngine) {
                    return Number(boat.pricePerDay);
                } else if (numberOfDays === 1 && usePricingWithEngine && boat.pricingWithEngine["0"]) {
                    return Number(boat.pricingWithEngine["0"]);
                }

                const pricingSource = usePricingWithEngine ? boat.pricingWithEngine : boat.pricing;

                if (numberOfDays >= 1 && numberOfDays <= 7) {
                    if (pricingSource && pricingSource[String(numberOfDays - 1)] !== undefined) {
                        return Number(pricingSource[String(numberOfDays - 1)]);
                    }
                    if (!usePricingWithEngine && boat.pricePerDay) return Number(boat.pricePerDay) * numberOfDays;
                } else if (numberOfDays > 7) {
                    let weeklyPrice = 0;
                    if (pricingSource && pricingSource["6"] !== undefined) {
                        weeklyPrice = Number(pricingSource["6"]);
                    } else if (!usePricingWithEngine && boat.pricePerDay) {
                        weeklyPrice = Number(boat.pricePerDay) * 7;
                    }

                    if (weeklyPrice > 0) {
                        const extraDays = numberOfDays - 7;
                        const costPerExtraDay = weeklyPrice / 7;
                        return weeklyPrice + (extraDays * costPerExtraDay);
                    }
                }
            }
        }
        return 0;
    }

    showError(message) {
        const errorDiv = document.getElementById('errorMessage');
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }

    showLoading(show) {
        const loading = document.getElementById('loading');
        if (loading) {
            loading.style.display = show ? 'block' : 'none';
        }
        const btn = document.querySelector('button[type="submit"]');
        if (btn) btn.disabled = show;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.BookingPageInstance = new BookingPage();
});
