/**
 * Booking Page Logic
 * Handles the standalone booking form.
 */
class BookingPage {
    constructor() {
        this.boats = [];
        this.currentLang = 'nl';
        this.locales = { nl: 'nl-NL', en: 'en-GB', de: 'de-DE' };
        /** Mirrors CHECKOUT_PAY_ON_ARRIVAL_METHOD in PHP. */
        this.payOnArrivalMethod = 'pay_on_arrival';
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
                this.refreshBoatAvailabilityForSelectedDates();
            });
        }
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
                this.syncBookingPayOnArrivalPaymentOption();
                this.refreshBookingPoaPaySummary();
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

        dateInput.addEventListener('change', async (e) => {
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
            this.populateBookingArrivalTimeOptions(e.target.value);
            this.syncBookingPayOnArrivalPaymentOption();
            await this.refreshBoatAvailabilityForSelectedDates();
            await this.updateQuantityDropdown();
            this.updateSummary();
        });

        endDateInput.addEventListener('change', async () => {
            await this.refreshBoatAvailabilityForSelectedDates();
            await this.updateQuantityDropdown();
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

        this.populateBookingArrivalTimeOptions(dateInput.value);
        this.syncBookingPayOnArrivalPaymentOption();
    }

    populateBookingArrivalTimeOptions(bookingDate) {
        const arrivalTimeSelect = document.getElementById('bookingArrivalTime');
        if (!arrivalTimeSelect) return;

        const placeholders = {
            nl: '-- Selecteer tijd --',
            en: '-- Select time --',
            de: '-- Uhrzeit wählen --'
        };
        const placeholder = placeholders[this.currentLang] || placeholders.nl;
        arrivalTimeSelect.innerHTML = `<option value="">${this.escapeHtml(placeholder)}</option>`;

        if (!bookingDate) {
            arrivalTimeSelect.disabled = true;
            return;
        }

        arrivalTimeSelect.disabled = false;

        const startHour = 9;
        const endHour = 18;
        const intervals = [0, 15, 30, 45];

        for (let hour = startHour; hour <= endHour; hour++) {
            for (const minute of intervals) {
                if (hour === endHour && minute > 0) break;
                const hourStr = hour.toString().padStart(2, '0');
                const minuteStr = minute.toString().padStart(2, '0');
                const timeValue = `${hourStr}:${minuteStr}`;
                const option = document.createElement('option');
                option.value = timeValue;
                option.textContent = timeValue;
                arrivalTimeSelect.appendChild(option);
            }
        }
        this.syncBookingPayOnArrivalPaymentOption();
    }

    escapeHtml(text) {
        const s = String(text ?? '');
        const div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    parseArrivalMinutes(timeVal) {
        if (!timeVal || typeof timeVal !== 'string') return null;
        const m = timeVal.trim().match(/^(\d{1,2}):(\d{2})/);
        if (!m) return null;
        return parseInt(m[1], 10) * 60 + parseInt(m[2], 10);
    }

    syncBookingPayOnArrivalPaymentOption() {
        const poaMethod = this.payOnArrivalMethod;
        const arrivalEl = document.getElementById('bookingArrivalTime');
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
        const feeEx = document.getElementById('bookingPoaFeeExplain');
        if (feeEx && typeof window.getTranslation === 'function') {
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
        this.refreshBookingPoaPaySummary();
    }

    refreshBookingPoaPaySummary() {
        const inline = document.getElementById('bookingPoaInlineRows');
        const priceRow = document.getElementById('bookingSummaryPriceRow');
        const poaMethod = this.payOnArrivalMethod;
        const poaChecked = document.querySelector(`input[name="paymentMethod"][value="${poaMethod}"]:checked`);
        const arrivalEl = document.getElementById('bookingArrivalTime');
        const minutes = arrivalEl && arrivalEl.value ? this.parseArrivalMinutes(arrivalEl.value) : null;
        const allowed = minutes != null && minutes <= 11 * 60;
        const t = (key) => (window.getTranslation ? window.getTranslation(key) : key);
        const sub = typeof this._bookingRentalSubtotal === 'number' ? this._bookingRentalSubtotal : 0;
        const grand = typeof this._bookingGrandTotal === 'number' ? this._bookingGrandTotal : 0;

        if (!inline) return;

        const totalPriceDisplay = document.getElementById('totalPriceDisplay');

        if (!poaChecked || !allowed || sub <= 0 || grand <= 0) {
            inline.hidden = true;
            if (priceRow) priceRow.style.display = '';
            if (totalPriceDisplay && grand > 0) {
                totalPriceDisplay.style.display = 'block';
            }
            return;
        }

        const resPct = typeof window.CHECKOUT_POA_RESERVATION_FEE_PERCENT === 'number' ? window.CHECKOUT_POA_RESERVATION_FEE_PERCENT : 10;
        const adminPctPoa = typeof window.CHECKOUT_ADMIN_FEE_PERCENT === 'number' ? window.CHECKOUT_ADMIN_FEE_PERCENT : 0;
        const resBase = Math.round(sub * (resPct / 100) * 100) / 100;
        const adminOnSlice = Math.round((resBase * (adminPctPoa / 100)) * 100) / 100;
        const reservation = Math.round((resBase + adminOnSlice) * 100) / 100;
        const poaGrand = Math.round((sub + adminOnSlice) * 100) / 100;
        const balance = Math.round((poaGrand - reservation) * 100) / 100;
        const resPctDisplay = Number.isInteger(resPct) ? String(resPct) : String(resPct);
        const adminPctDisplay = Number.isInteger(adminPctPoa) ? String(adminPctPoa) : String(adminPctPoa);
        const admLbl = t('checkout_poa_admin_slice_label')
            .replace(/\{admin_percent\}/g, adminPctDisplay)
            .replace(/\{res_percent\}/g, resPctDisplay);

        const setTxt = (id, text) => {
            const el = document.getElementById(id);
            if (el) el.textContent = text;
        };
        setTxt('bookingPoaLblSub', t('checkout_poa_rental_label'));
        setTxt('bookingPoaValSub', `€${sub.toFixed(2)}`);
        setTxt('bookingPoaLblAdm', admLbl);
        setTxt('bookingPoaValAdm', `€${adminOnSlice.toFixed(2)}`);
        setTxt('bookingPoaLblOnline', t('checkout_poa_row_pay_online'));
        setTxt('bookingPoaValOnline', `€${reservation.toFixed(2)}`);
        setTxt('bookingPoaLblArr', t('checkout_poa_row_on_arrival'));
        setTxt('bookingPoaValArr', `€${balance.toFixed(2)}`);
        setTxt('bookingPoaLblTrip', t('checkout_total_trip'));
        setTxt('bookingPoaValTrip', `€${poaGrand.toFixed(2)}`);

        const admRow = document.getElementById('bookingPoaRowAdmin');
        if (admRow) admRow.hidden = adminPctPoa <= 0;

        inline.hidden = false;
        if (priceRow) priceRow.style.display = 'none';
        if (totalPriceDisplay) {
            totalPriceDisplay.style.display = 'none';
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
        await this.refreshBoatAvailabilityForSelectedDates();

        const urlParams = new URLSearchParams(window.location.search);
        const boatTypeFromUrl = urlParams.get('boatType');
        if (boatTypeFromUrl) {
            const option = boatSelect.querySelector(`option[value="${boatTypeFromUrl}"]`);
            if (option) {
                boatSelect.value = boatTypeFromUrl;
                this.updateOptionsVisibility();
                await this.updateQuantityDropdown();
            }
            setTimeout(() => this.updateSummary(), 0);
        }
    }

    getBoatAvailabilityMessageElement() {
        const boatSelect = document.getElementById('boatType');
        if (!boatSelect) return null;
        const hostGroup = boatSelect.closest('.form-group');
        if (!hostGroup) return null;

        let messageEl = document.getElementById('bookingBoatAvailabilityMessage');
        if (!messageEl) {
            messageEl = document.createElement('div');
            messageEl.id = 'bookingBoatAvailabilityMessage';
            messageEl.style.cssText = 'display:none;margin-top:0.5rem;color:#b45309;font-size:0.9rem;';
            hostGroup.appendChild(messageEl);
        }
        return messageEl;
    }

    setBoatAvailabilityMessage(message) {
        const messageEl = this.getBoatAvailabilityMessageElement();
        if (!messageEl) return;
        if (!message) {
            messageEl.textContent = '';
            messageEl.style.display = 'none';
            return;
        }
        messageEl.textContent = message;
        messageEl.style.display = 'block';
    }

    getSelectedDateRange() {
        const dateInput = document.getElementById('bookingDate');
        const endDateInput = document.getElementById('bookingEndDate');
        const startDate = dateInput ? dateInput.value : null;
        const endDate = (endDateInput ? endDateInput.value : null) || startDate;
        return { startDate, endDate };
    }

    async getAvailableBoatIdSet(startDate, endDate) {
        if (!startDate) return null;

        try {
            const endpoint = (window.location.protocol === 'file:' || window.location.hostname === '')
                ? 'http://localhost:8000/admin/booking-handler.py'
                : `${window.location.origin}/admin/booking-handler.php`;

            const response = await fetch(`${endpoint}?action=getAvailableBoats&date=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate || startDate)}`, {
                method: 'GET',
                credentials: 'include',
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                return null;
            }

            const data = await response.json();
            const ids = data?.data?.availableBoatIds;
            if (data?.success && Array.isArray(ids)) {
                return new Set(ids);
            }
        } catch (e) {
            console.error('Error loading available boats:', e);
        }

        return null;
    }

    async refreshBoatAvailabilityForSelectedDates() {
        const { startDate, endDate } = this.getSelectedDateRange();
        const boatSelect = document.getElementById('boatType');
        const previousBoat = boatSelect ? boatSelect.value : '';
        const availableBoatIds = await this.getAvailableBoatIdSet(startDate, endDate);
        this.populateBoatSelect(this.boats, { availableBoatIds, hasDateFilter: !!startDate });
        const nextBoat = boatSelect ? boatSelect.value : '';

        if (previousBoat !== nextBoat) {
            this.updateOptionsVisibility();
            await this.updateQuantityDropdown();
            this.updateSummary();
        } else if (!nextBoat) {
            await this.updateQuantityDropdown();
            this.updateSummary();
        }
    }

    populateBoatSelect(boats, options = {}) {
        const boatSelect = document.getElementById('boatType');
        const currentValue = boatSelect.value;
        const availableBoatIds = options.availableBoatIds instanceof Set ? options.availableBoatIds : null;
        const hasDateFilter = !!options.hasDateFilter;
        const selectTexts = {
            nl: '-- Selecteer een boot --',
            en: '-- Select a boat --',
            de: '-- Boot wählen --'
        };
        boatSelect.innerHTML = `<option value="">${selectTexts[this.currentLang] || selectTexts.nl}</option>`;

        let filteredBoats = boats || [];
        if (boats && boats.length) {
            const sortedBoats = [...boats].sort((a, b) => {
                const categoryOrder = ['electric', 'sailing', 'canoe', 'sup'];
                const categoryA = categoryOrder.indexOf(a.category || 'other');
                const categoryB = categoryOrder.indexOf(b.category || 'other');
                if (categoryA !== categoryB) return categoryA - categoryB;
                return (a.orderId || 999) - (b.orderId || 999);
            });

            filteredBoats = availableBoatIds
                ? sortedBoats.filter((boat) => availableBoatIds.has(boat.id))
                : sortedBoats;

            filteredBoats.forEach(boat => {
                const option = document.createElement('option');
                option.value = boat.id;
                option.textContent = boat.name;
                option.dataset.pricePerDay = boat.pricePerDay || 0;
                option.dataset.pricing = JSON.stringify(boat.pricing || {});
                option.dataset.pricingWithEngine = JSON.stringify(boat.pricingWithEngine || null);
                option.dataset.availableDays = JSON.stringify(boat.availableDays || [1, 2, 3, 4, 5, 6, 7]);
                boatSelect.appendChild(option);
            });
        }

        if (hasDateFilter && availableBoatIds && filteredBoats.length === 0) {
            this.setBoatAvailabilityMessage('Geen boten beschikbaar voor de geselecteerde datum.');
        } else {
            this.setBoatAvailabilityMessage('');
        }

        if (currentValue) {
            const option = boatSelect.querySelector(`option[value="${currentValue}"]`);
            if (option) {
                boatSelect.value = currentValue;
            } else {
                boatSelect.value = '';
            }
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
        return boat ? (boat.total ?? 1) : 1;
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

        document.querySelectorAll('input[name="paymentMethod"]').forEach((r) => {
            r.addEventListener('change', () => this.syncBookingPayOnArrivalPaymentOption());
        });

        document.querySelectorAll('.checkout-wallet-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const method = btn.dataset.method;
                const form = document.getElementById('bookingForm');
                const radios = form.querySelectorAll('input[name="paymentMethod"]');
                radios.forEach(r => r.checked = false);
                let radio = form.querySelector(`input[name="paymentMethod"][value="${method}"]`);
                if (!radio) {
                    radio = document.createElement('input');
                    radio.type = 'hidden';
                    radio.name = 'paymentMethod';
                    radio.value = method;
                    form.appendChild(radio);
                }
                radio.checked = true;
                this.submitBooking();
            });
        });
        const bookingArrival = document.getElementById('bookingArrivalTime');
        if (bookingArrival) {
            bookingArrival.addEventListener('change', () => this.syncBookingPayOnArrivalPaymentOption());
        }
        this.syncBookingPayOnArrivalPaymentOption();
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

        let rental = 0;
        let grand = 0;
        if (boatType && numberOfDays > 0) {
            rental = this.calculatePrice(boatType, numberOfDays, useMotor) * quantity;
            const pct = typeof window.CHECKOUT_ADMIN_FEE_PERCENT === 'number' ? window.CHECKOUT_ADMIN_FEE_PERCENT : 0;
            const adminFee = Math.round(rental * (pct / 100) * 100) / 100;
            grand = Math.round((rental + adminFee) * 100) / 100;
            this._bookingRentalSubtotal = rental;
            this._bookingGrandTotal = grand;
        } else {
            this._bookingRentalSubtotal = 0;
            this._bookingGrandTotal = 0;
        }

        const totalPriceDisplay = document.getElementById('totalPriceDisplay');
        const totalPriceAmount = document.getElementById('totalPriceAmount');

        if (boatType && numberOfDays > 0) {
            totalPriceAmount.textContent = `€${grand.toFixed(2)}`;
            totalPriceDisplay.style.display = 'block';
        } else {
            totalPriceDisplay.style.display = 'none';
        }

        if (date && boatType && numberOfDays > 0 && dateValid) {
            document.getElementById('summaryPrice').textContent = `€${grand.toFixed(2)}`;
            document.getElementById('summaryStatus').textContent = this.getStatusText(true);
            document.getElementById('summaryStatus').style.color = 'var(--success-color)';
        } else {
            document.getElementById('summaryPrice').textContent = '-';
            document.getElementById('summaryStatus').textContent = this.getStatusText(false);
            document.getElementById('summaryStatus').style.color = 'var(--text-secondary)';
        }

        this.refreshBookingPoaPaySummary();
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

        const paymentMethod = (formData.get('paymentMethod') || '').toString().trim();
        if (!paymentMethod) {
            const msg =
                typeof window.getTranslation === 'function'
                    ? window.getTranslation('checkout_error_payment_method')
                    : 'Kies een betaalmethode.';
            this.showError(msg);
            return;
        }

        const arrivalTime = (formData.get('arrivalTime') || '').toString().trim();
        const cityOfOrigin = (formData.get('cityOfOrigin') || '').toString().trim();
        if (!arrivalTime || !cityOfOrigin) {
            const msg =
                typeof window.getTranslation === 'function'
                    ? window.getTranslation('checkout_error_fields')
                    : 'Vul alle verplichte velden in.';
            this.showError(msg);
            return;
        }

        if (paymentMethod === this.payOnArrivalMethod) {
            const mins = this.parseArrivalMinutes(arrivalTime);
            if (mins == null || mins > 11 * 60) {
                const msg =
                    typeof window.getTranslation === 'function'
                        ? window.getTranslation('checkout_error_pay_on_arrival_time')
                        : 'Met betalen bij aankomst mag de aankomsttijd niet later zijn dan 11:00.';
                this.showError(msg);
                return;
            }
        }

        const validationResult = this.validateSeasonDate(date);
        if (!validationResult.valid) {
            this.showError(validationResult.message);
            return;
        }

        const bookingData = {
            date: date,
            boatType: boatType,
            numberOfDays: numberOfDays,
            quantity: quantity,
            withEngine: useMotor,
            useMotor: useMotor,
            paymentMethod: paymentMethod,
            customerName: formData.get('customerName'),
            customerEmail: formData.get('customerEmail'),
            customerPhone: formData.get('customerPhone'),
            customerAddress: formData.get('customerAddress'),
            arrivalTime,
            cityOfOrigin,
            notes: formData.get('specialRequests'),
            status: 'canceled',
            createdAt: new Date().toISOString()
        };

        this.showLoading(true);

        try {
            // SECURITY: Booking is persisted server-side by createMolliePayment().
            // We previously also wrote the full bookingData (name, email, phone,
            // address, notes) to localStorage as a fallback, which exposed
            // customer PII to any script running on the domain (incl. XSS) and
            // persisted it across sessions. The server is the source of truth,
            // so only keep an id on the object for the call below.
            bookingData.id = 'booking_' + Date.now();

            await this.createMolliePayment(bookingData);
        } catch (error) {
            console.error('Payment error:', error);
            if (error && error.message === 'payment_method' && typeof window.getTranslation === 'function') {
                this.showError(window.getTranslation('checkout_error_payment_method'));
                this.showLoading(false);
                return;
            }
            if (error && error.message === 'customer_details_required' && typeof window.getTranslation === 'function') {
                this.showError(window.getTranslation('checkout_error_fields'));
                this.showLoading(false);
                return;
            }
            if (error && error.message === 'pay_on_arrival_arrival_invalid' && typeof window.getTranslation === 'function') {
                this.showError(window.getTranslation('checkout_error_pay_on_arrival_time'));
                this.showLoading(false);
                return;
            }
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
