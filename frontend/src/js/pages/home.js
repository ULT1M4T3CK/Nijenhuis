/**
 * Home Page Script
 * Handles the Hero booking form and the Boat Fleet grid on the homepage.
 * Uses useBoatData and useBookingAvailability hooks.
 */

window.HomePage = (function () {
    const boatsStorageKey = 'nijenhuis_boats';

    // Dependencies
    const bookingService = window.useBookingAvailability ? window.useBookingAvailability() : {
        checkAvailability: async () => true // Fallback if hook missing
    };

    // ============================================
    // STATE & HELPERS
    // ============================================
    let seasonStartMonth = 4; // April
    let seasonEndMonth = 10;  // October
    let bookingYear = new Date().getFullYear();
    let bookingOpen = true;
    let seasonEndDate = '';
    
    // Calendar state
    let calendarState = {
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        selectedStartDate: null,
        selectedEndDate: null,
        isSelecting: false,
        currentBoatId: null
    };

    // ============================================
    // INIT
    // ============================================
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            // Restore button state FIRST, before any other initialization
            restoreButtonState();
            
            initSeasonLogic();
            await populateBoatDropdown(); // Populate dropdown first
            initBookingForm();
            
            // Restore button state again after form initialization to ensure it's enabled
            setTimeout(() => restoreButtonState(), 100);
        } catch (error) {
            console.error('Error initializing booking form:', error);
            // Ensure button is enabled even if there's an error
            restoreButtonState();
        }

        // Subscribe to booking service to ensure data loads and updates interactively across tabs/polling
        if (bookingService && bookingService.subscribe) {
            bookingService.subscribe((bookings) => {
                // When bookings update, re-render with current selected date
                const dateVal = calendarState.selectedStartDate || 
                               (document.getElementById('date') ? document.getElementById('date').value : null);
                renderBoatFleetSection(dateVal);
                if (dateVal) populateBoatDropdown(dateVal);
            });
        }

        // Reset button state when cart sidebar closes
        window.addEventListener('cartSidebarClosed', () => {
            restoreButtonState();
        });

        // Initial render (will likely rely on cache until subscribe fires)
        // Don't show availability until a date is selected
        renderBoatFleetSection(null);
    });
    
    /**
     * Restore button text to "Boek nu" if returning from checkout
     * Also ensures button is enabled
     */
    function restoreButtonState() {
        const form = document.getElementById('bookingForm');
        if (!form) return;
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;
        
        // CRITICAL: Always enable the button - remove disabled attribute completely
        submitBtn.disabled = false;
        submitBtn.removeAttribute('disabled');
        
        // Remove any disabled class that might be applied
        submitBtn.classList.remove('disabled');
        
        // Check if we're returning from checkout (check URL params or referrer)
        const urlParams = new URLSearchParams(window.location.search);
        const referrer = document.referrer;
        const isReturningFromCheckout = referrer.includes('checkout.php') || 
                                        urlParams.has('from') && urlParams.get('from') === 'checkout';
        
        // Restore button text if it was changed
        if (submitBtn.textContent === 'Versturen...' || submitBtn.textContent === 'Versturen') {
            // Get original text from data attribute or default
            const originalText = submitBtn.dataset.originalText || 'Boek nu';
            submitBtn.textContent = originalText;
        } else if (isReturningFromCheckout) {
            // Ensure button is in correct state when returning
            submitBtn.textContent = 'Boek nu';
        }
        
        // Store original text if not already stored
        if (!submitBtn.dataset.originalText) {
            submitBtn.dataset.originalText = submitBtn.textContent || 'Boek nu';
        }
        
        // Final check: ensure button is enabled and remove any disabled styling
        submitBtn.disabled = false;
        submitBtn.removeAttribute('disabled');
        submitBtn.style.opacity = '1';
        submitBtn.style.cursor = 'pointer';
    }

    // ============================================
    // BOAT DROPDOWN LOGIC
    // ============================================
    function getBoatAvailabilityMessageElement() {
        const boatSelect = document.getElementById('boatType');
        if (!boatSelect) return null;
        const hostGroup = boatSelect.closest('.form-group');
        if (!hostGroup) return null;

        let messageEl = document.getElementById('boatAvailabilityMessage');
        if (!messageEl) {
            messageEl = document.createElement('div');
            messageEl.id = 'boatAvailabilityMessage';
            messageEl.style.cssText = 'display:none;margin-top:0.5rem;color:#b45309;font-size:0.9rem;';
            hostGroup.appendChild(messageEl);
        }
        return messageEl;
    }

    function setBoatAvailabilityMessage(message) {
        const messageEl = getBoatAvailabilityMessageElement();
        if (!messageEl) return;
        if (!message) {
            messageEl.textContent = '';
            messageEl.style.display = 'none';
            return;
        }
        messageEl.textContent = message;
        messageEl.style.display = 'block';
    }

    function getSelectedDateRange(fallbackStartDate = null) {
        const dateInput = document.getElementById('date');
        const endDateInput = document.getElementById('rentalEndDate');
        const startDate = fallbackStartDate || (dateInput ? dateInput.value : '') || calendarState.selectedStartDate;
        const endDate = (endDateInput ? endDateInput.value : '') || calendarState.selectedEndDate || startDate;

        return {
            startDate: startDate || null,
            endDate: endDate || null
        };
    }

    async function getAvailableBoatIdSet(startDate, endDate) {
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

    async function populateBoatDropdown(checkDate = null) {
        const boatSelect = document.getElementById('boatType');
        if (!boatSelect) return;

        // Preserve selection
        const currentValue = boatSelect.value;
        const { startDate, endDate } = getSelectedDateRange(checkDate);

        let boats = [];
        if (window.BoatDataService) {
            boats = await window.BoatDataService.getAllBoats();
        } else {
            boats = JSON.parse(localStorage.getItem(boatsStorageKey) || '[]');
        }

        const availableBoatIds = await getAvailableBoatIdSet(startDate, endDate);

        // Keep default option
        const defaultOption = boatSelect.options[0];
        boatSelect.innerHTML = '';
        boatSelect.appendChild(defaultOption);

        let filteredBoats = boats;
        if (boats && boats.length) {
            const sorted = [...boats].sort((a, b) => (a.orderId || 99) - (b.orderId || 99));
            filteredBoats = availableBoatIds
                ? sorted.filter((boat) => availableBoatIds.has(boat.id))
                : sorted;

            for (const boat of filteredBoats) {
                const opt = document.createElement('option');
                opt.value = boat.id;
                opt.textContent = boat.name;
                // Add pricing data for calculatePrice
                opt.dataset.price = boat.pricePerDay || 0;

                boatSelect.appendChild(opt);
            }
        }

        if (startDate && availableBoatIds && filteredBoats.length === 0) {
            setBoatAvailabilityMessage('Geen boten beschikbaar voor de geselecteerde datum.');
        } else {
            setBoatAvailabilityMessage('');
        }

        let nextValue = '';
        if (currentValue) {
            const opt = boatSelect.querySelector(`option[value="${currentValue}"]`);
            if (opt) {
                nextValue = currentValue;
            }
        } else {
            // Restore selection if in URL (only on initial load really)
            const urlParams = new URLSearchParams(window.location.search);
            const boatParam = urlParams.get('boat');
            if (boatParam) {
                const opt = boatSelect.querySelector(`option[value="${boatParam}"]`);
                if (opt) nextValue = boatParam;
            }
        }

        boatSelect.value = nextValue;
        calendarState.currentBoatId = nextValue || null;

        const selectionChanged = currentValue && nextValue !== currentValue;
        if (selectionChanged) {
            await updateEngineOption();
            await updateQuantityDropdown();
            updatePriceDisplay();

            const calendarContainer = document.getElementById('calendarContainer');
            if (calendarContainer && calendarContainer.style.display !== 'none') {
                renderCalendar();
            }
        } else if (!nextValue) {
            await updateEngineOption();
            await updateQuantityDropdown();
            updatePriceDisplay();
        }
    }

    // ============================================
    // SEASON & FORM LOGIC
    // ============================================
    function initSeasonLogic() {
        const dateInput = document.getElementById('date');
        if (!dateInput) return;

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const currentYear = today.getFullYear();
        const currentMonth = today.getMonth(); // 0-11
        const currentDate = today.getDate();

        // Season Config from window if available, else defaults
        const startMonthConfig = window.SiteConfig?.seasonStart?.month || 4;
        const endMonthConfig = window.SiteConfig?.seasonEnd?.month || 10;

        seasonStartMonth = startMonthConfig;
        seasonEndMonth = endMonthConfig;

        if (currentMonth < endMonthConfig) {
            bookingOpen = true;
            bookingYear = currentYear;

            const seasonStart = `${bookingYear}-${String(startMonthConfig).padStart(2, '0')}-${String(window.SiteConfig?.seasonStart?.day || 1).padStart(2, '0')}`;
            const todayStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(currentDate).padStart(2, '0')}`;

            const minDateStr = todayStr > seasonStart ? todayStr : seasonStart;
            const maxDateStr = `${bookingYear}-${String(endMonthConfig).padStart(2, '0')}-${String(window.SiteConfig?.seasonEnd?.day || 31).padStart(2, '0')}`;

            dateInput.min = minDateStr;
            dateInput.max = maxDateStr;
            seasonEndDate = maxDateStr;

            if (!dateInput.value) {
                dateInput.value = minDateStr;
                // Initial dropdown populate with default date
                populateBoatDropdown(minDateStr);
            }
        } else {
            // Season closed
            bookingOpen = false;
            bookingYear = currentYear + 1;
            const minDateStr = `${bookingYear}-${String(startMonthConfig).padStart(2, '0')}-${String(window.SiteConfig?.seasonStart?.day || 1).padStart(2, '0')}`;
            const maxDateStr = `${bookingYear}-${String(endMonthConfig).padStart(2, '0')}-${String(window.SiteConfig?.seasonEnd?.day || 31).padStart(2, '0')}`;

            dateInput.min = minDateStr;
            dateInput.max = maxDateStr;
            seasonEndDate = maxDateStr;

            dateInput.disabled = true;
            if (document.getElementById('rentalEndDate')) document.getElementById('rentalEndDate').disabled = true;
            showBookingClosedMessage(bookingYear, currentMonth);
        }

        window.bookingYear = bookingYear;
        window.bookingOpen = bookingOpen;
        window.seasonEndDate = seasonEndDate;
    }

    function showBookingClosedMessage(year, currentMonth) {
        const formContainer = document.querySelector('.booking-form-modern');
        if (!formContainer) return;

        let messageDiv = document.getElementById('bookingSeasonMessage');
        if (!messageDiv) {
            messageDiv = document.createElement('div');
            messageDiv.id = 'bookingSeasonMessage';
            messageDiv.style.cssText = 'background: #fff3cd; color: #856404; border: 1px solid #ffc107; padding: 15px; border-radius: 8px; margin-bottom: 15px; text-align: center;';
            formContainer.querySelector('.form-header').after(messageDiv);
        }

        // SECURITY: Use textContent and create elements safely to prevent XSS
        messageDiv.textContent = '';
        const strong1 = document.createElement('strong');
        strong1.textContent = currentMonth >= 10 ? 'Het boekingsseizoen is gesloten.' : 'Reserveringen zijn nog niet geopend.';
        messageDiv.appendChild(strong1);
        messageDiv.appendChild(document.createElement('br'));
        const text = document.createTextNode('Reserveringen voor seizoen ' + year + ' openen op ');
        messageDiv.appendChild(text);
        const strong2 = document.createElement('strong');
        strong2.textContent = '1 maart ' + year;
        messageDiv.appendChild(strong2);
        messageDiv.appendChild(document.createTextNode('.'));
    }

    function initBookingForm() {
        // Boat Type Change
        const boatSelect = document.getElementById('boatType');
        if (boatSelect) {
            boatSelect.addEventListener('change', async () => {
                calendarState.currentBoatId = boatSelect.value;
                
                // If zeilpunter is selected and there's a multi-day selection, reset to single day
                if (boatSelect.value === 'sailpunter-3-4' && 
                    calendarState.selectedStartDate && 
                    calendarState.selectedEndDate && 
                    calendarState.selectedStartDate !== calendarState.selectedEndDate) {
                    // Reset to single day booking
                    calendarState.selectedEndDate = calendarState.selectedStartDate;
                    updateDateInputs();
                }
                
                await updateEngineOption();
                await updateQuantityDropdown();
                updatePriceDisplay();
                // Update calendar if it's open
                const calendarContainer = document.getElementById('calendarContainer');
                if (calendarContainer && calendarContainer.style.display !== 'none') {
                    renderCalendar();
                }
            });
        }

        // Engine Option Change
        const engineSelect = document.getElementById('engineOption');
        if (engineSelect) {
            engineSelect.addEventListener('change', () => {
                updateEngineOption();
                updatePriceDisplay();
            });
        }

        // Quantity Change
        const quantitySelect = document.getElementById('boatQuantity');
        if (quantitySelect) {
            quantitySelect.addEventListener('change', () => {
                updatePriceDisplay();
            });
        }

        // Initialize Calendar
        initCalendar();

        // Form Submit
        const form = document.getElementById('bookingForm');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Ensure button is enabled before submission
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.removeAttribute('disabled');
                }
                
                await handleFormSubmit();
            });
        }

        // Add to Cart Button
        const addToCartBtn = document.getElementById('addToCartBtn');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                await handleAddToCart();
            });
        }

        // URL Params
        const urlParams = new URLSearchParams(window.location.search);
        const boatParam = urlParams.get('boat');
        if (boatParam && boatSelect) {
            boatSelect.value = boatParam;
            calendarState.currentBoatId = boatParam;
            updateEngineOption();
            updateQuantityDropdown();
        }
        // Check if there is a boat ID as the path to open modal automatically (e.g. /classic-tender-720)
        const pathName = window.location.pathname.substring(1).replace(/\/$/, "");
        if (pathName) {
            const ignorePaths = ['botenverhuur', 'vakantiehuis', 'te-koop', 'camping', 'vaarkaart', 'contact', 'booking', 'checkout', 'giethoorn', 'belt-schutsloot', 'wanneperveen', 'veelgestelde-vragen', 'offline', 'admin'];
            if (!ignorePaths.includes(pathName) && !pathName.includes('.php') && !pathName.includes('pages/')) {
                setTimeout(() => {
                    if (window.HomePage && window.HomePage.openBoatInfo) {
                        window.HomePage.openBoatInfo(pathName);
                    }
                }, 300); // slight delay to make sure data is loaded
            }
        }
    }

    // ============================================
    // CALENDAR FUNCTIONS
    // ============================================
    function initCalendar() {
        const dateRangeInput = document.getElementById('dateRange');
        const calendarContainer = document.getElementById('calendarContainer');
        
        if (!dateRangeInput || !calendarContainer) {
            console.warn('Calendar initialization failed: dateRangeInput or calendarContainer not found', {
                dateRangeInput: !!dateRangeInput,
                calendarContainer: !!calendarContainer
            });
            return;
        }

        // Open on the current month during the booking season; before April, start at April.
        const today = new Date();
        const currentMonth = today.getMonth();
        calendarState.currentMonth = Math.min(Math.max(currentMonth, 3), 9);
        calendarState.currentYear = today.getFullYear();

        // Get initial boat selection
        const boatSelect = document.getElementById('boatType');
        if (boatSelect && boatSelect.value) {
            calendarState.currentBoatId = boatSelect.value;
        }

        // Flag to track if calendar was just opened
        let calendarJustOpened = false;
        let openingClickTime = 0;
        let isOpening = false;
        let openingEventTarget = null;

        // Click handler to open calendar - use multiple event types for better compatibility
        const openCalendar = (e) => {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                openingEventTarget = e.target;
            }
            
            const isOpen = calendarContainer.style.display !== 'none' && calendarContainer.style.display !== '';
            
            if (!isOpen) {
                // Opening the calendar
                isOpening = true;
                calendarJustOpened = true;
                openingClickTime = Date.now();
                toggleCalendar();
                
                // Reset flags after a longer delay to prevent immediate closing
                setTimeout(() => {
                    isOpening = false;
                    calendarJustOpened = false;
                    openingEventTarget = null;
                }, 300);
            } else {
                // Only toggle if both dates are selected (allow closing when complete)
                if (calendarState.selectedStartDate && calendarState.selectedEndDate) {
                    toggleCalendar();
                }
            }
        };
        
        // Attach multiple event listeners for maximum compatibility
        dateRangeInput.addEventListener('mousedown', (e) => {
            e.stopPropagation();
            e.stopImmediatePropagation();
            isOpening = true;
            openingEventTarget = e.target;
        });
        
        dateRangeInput.addEventListener('click', openCalendar, true); // Use capture phase
        dateRangeInput.addEventListener('pointerdown', (e) => {
            e.stopPropagation();
            e.stopImmediatePropagation();
            isOpening = true;
            openingEventTarget = e.target;
        });
        
        // Also handle touch events for mobile
        dateRangeInput.addEventListener('touchend', (e) => {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            openCalendar(e);
        }, true);
        
        // Make sure the input is interactive
        dateRangeInput.setAttribute('tabindex', '0');
        dateRangeInput.setAttribute('role', 'button');
        dateRangeInput.setAttribute('aria-haspopup', 'true');
        dateRangeInput.setAttribute('aria-expanded', 'false');

        // Also handle focus (for keyboard navigation)
        dateRangeInput.addEventListener('focus', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = calendarContainer.style.display !== 'none' && calendarContainer.style.display !== '';
            
            if (!isOpen) {
                isOpening = true;
                calendarJustOpened = true;
                openingClickTime = Date.now();
                openingEventTarget = e.target;
                toggleCalendar();
                setTimeout(() => {
                    isOpening = false;
                    calendarJustOpened = false;
                    openingEventTarget = null;
                }, 300);
            }
        });
        
        // Ensure input is clickable (readonly inputs can sometimes have pointer-events issues)
        dateRangeInput.style.pointerEvents = 'auto';
        dateRangeInput.style.cursor = 'pointer';

        // Close calendar when clicking outside
        let outsideClickHandler = null;
        const handleOutsideClick = (e) => {
            // Don't process if we're currently opening the calendar
            if (isOpening) {
                return;
            }
            
            // Don't process if calendar is closed
            if (!calendarContainer || calendarContainer.style.display === 'none' || calendarContainer.style.display === '') {
                return;
            }
            
            // Prevent closing if calendar was just opened (within last 500ms for safety)
            const timeSinceOpen = Date.now() - openingClickTime;
            if (calendarJustOpened || timeSinceOpen < 500) {
                return;
            }
            
            // Don't close if the click target is the same as the opening event target
            if (openingEventTarget && (e.target === openingEventTarget || e.target.contains(openingEventTarget))) {
                return;
            }
            
            // IMPORTANT: Don't close if we have a start date but no end date (user is selecting end date)
            // This ensures calendar stays open until both dates are selected
            if (calendarState.selectedStartDate && !calendarState.selectedEndDate) {
                return; // Keep calendar open so user can select end date
            }
            
            // Don't close if clicking inside calendar, on the input, or on close button
            const isClickInsideCalendar = calendarContainer.contains(e.target);
            const isClickOnInput = e.target === dateRangeInput || 
                                   dateRangeInput.contains(e.target) || 
                                   e.target.closest('#dateRange') ||
                                   e.target.id === 'dateRange' ||
                                   e.target.closest('.input-icon') ||
                                   e.target.closest('label[for="dateRange"]');
            const isClickOnCloseBtn = e.target.closest('.calendar-close-btn');
            
            // Only close if clicking outside AND both dates are selected (or no dates selected)
            const bothDatesSelected = calendarState.selectedStartDate && calendarState.selectedEndDate;
            const noDatesSelected = !calendarState.selectedStartDate && !calendarState.selectedEndDate;
            
            if (!isClickInsideCalendar && !isClickOnInput && !isClickOnCloseBtn && (bothDatesSelected || noDatesSelected)) {
                calendarContainer.style.display = 'none';
                dateRangeInput.setAttribute('aria-expanded', 'false');
            }
        };
        
        // Add event listener with delay to prevent immediate closing
        // Use a longer delay to ensure calendar has time to open and render
        setTimeout(() => {
            outsideClickHandler = handleOutsideClick;
            document.addEventListener('click', outsideClickHandler, true); // Use capture phase
        }, 300);

        // Update calendar position on scroll to keep it aligned with the input field
        const updateCalendarPosition = () => {
            if (calendarContainer && calendarContainer.style.display !== 'none' && calendarContainer.style.display !== '') {
                const dateRangeInput = document.getElementById('dateRange');
                if (!dateRangeInput) return;
                
                const inputRect = dateRangeInput.getBoundingClientRect();
                // For fixed positioning, use viewport coordinates directly.
                // Also clamp width/left so we never create horizontal overflow on small screens.
                const viewportPadding = 12;
                const maxWidth = Math.min(480, Math.max(280, window.innerWidth - (viewportPadding * 2)));
                const desiredWidth = Math.min(maxWidth, Math.max(280, inputRect.width));
                const left = Math.min(
                    Math.max(viewportPadding, inputRect.left),
                    Math.max(viewportPadding, window.innerWidth - desiredWidth - viewportPadding)
                );
                calendarContainer.style.top = (inputRect.bottom + 8) + 'px';
                calendarContainer.style.left = left + 'px';
                calendarContainer.style.width = desiredWidth + 'px';
            }
        };
        
        window.addEventListener('scroll', updateCalendarPosition, true);
        window.addEventListener('resize', updateCalendarPosition);

        // Don't render calendar initially - only when opened
        // renderCalendar(); // Removed - calendar will render when opened
    }

    function toggleCalendar() {
        const calendarContainer = document.getElementById('calendarContainer');
        const dateRangeInput = document.getElementById('dateRange');
        
        if (!calendarContainer || !dateRangeInput) {
            console.error('Calendar container or input not found in toggleCalendar', {
                calendarContainer: !!calendarContainer,
                dateRangeInput: !!dateRangeInput
            });
            return;
        }

        const isHidden = calendarContainer.style.display === 'none' || !calendarContainer.style.display || calendarContainer.style.display === '';
        
        if (isHidden) {
            // Calculate position relative to input field
            const inputRect = dateRangeInput.getBoundingClientRect();
            
            // Ensure calendar is in the DOM (move to body if needed for proper positioning)
            // This prevents z-index and overflow issues from parent containers
            if (calendarContainer.parentElement !== document.body) {
                document.body.appendChild(calendarContainer);
            }
            
            // Position calendar below input, anchored to viewport (fixed positioning)
            // getBoundingClientRect() already gives viewport-relative coordinates
            calendarContainer.style.position = 'fixed';
            // Clamp width/left so the calendar never overflows the viewport (mobile-friendly)
            const viewportPadding = 12; // keep a small margin from screen edges
            const maxWidth = Math.min(480, Math.max(280, window.innerWidth - (viewportPadding * 2)));
            const desiredWidth = Math.min(maxWidth, Math.max(280, inputRect.width));
            const left = Math.min(
                Math.max(viewportPadding, inputRect.left),
                Math.max(viewportPadding, window.innerWidth - desiredWidth - viewportPadding)
            );
            calendarContainer.style.top = (inputRect.bottom + 8) + 'px';
            calendarContainer.style.left = left + 'px';
            calendarContainer.style.width = desiredWidth + 'px';
            calendarContainer.style.display = 'block';
            calendarContainer.style.zIndex = '10000';
            calendarContainer.style.visibility = 'visible';
            calendarContainer.style.opacity = '1';
            calendarContainer.style.pointerEvents = 'auto';
            calendarContainer.style.backgroundColor = 'white';
            
            // Update aria attribute
            dateRangeInput.setAttribute('aria-expanded', 'true');
            
            renderCalendar();
        } else {
            calendarContainer.style.display = 'none';
            dateRangeInput.setAttribute('aria-expanded', 'false');
        }
    }

    function renderCalendar() {
        const calendarContainer = document.getElementById('calendarContainer');
        if (!calendarContainer) return;

        const monthNames = ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 
                          'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'];
        const dayNames = ['Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za', 'Zo'];

        // Only show months from April to October
        const startMonth = 3; // April (0-indexed)
        const endMonth = 9;   // October (0-indexed)
        
        // Ensure we're within season months
        if (calendarState.currentMonth < startMonth) {
            calendarState.currentMonth = startMonth;
        }
        if (calendarState.currentMonth > endMonth) {
            calendarState.currentMonth = endMonth;
        }

        const currentYear = calendarState.currentYear;
        const currentMonth = calendarState.currentMonth;

        // Get first day of month and number of days
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDayOfWeek = (firstDay.getDay() + 6) % 7;

        // Check if current boat is zeilpunter (sailpunter-3-4)
        const boatSelect = document.getElementById('boatType');
        const isZeilpunter = boatSelect && boatSelect.value === 'sailpunter-3-4';
        
        // Build calendar HTML - Close button above month switcher
        let html = `
            <div class="booking-calendar">
                <div class="calendar-header-inline">
                    <button class="calendar-close-btn" id="calendarCloseBtn" aria-label="Sluiten" title="Sluiten" style="position: absolute; top: 0.5rem; right: 0.5rem;">&times;</button>
                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-top: 2rem;">
                        <button class="calendar-nav-btn-inline" id="prevMonthBtn" ${currentMonth === startMonth && currentYear === new Date().getFullYear() ? 'disabled' : ''}>❮</button>
                        <h3 style="flex: 1; text-align: center; margin: 0;">${monthNames[currentMonth]} ${currentYear}</h3>
                        <button class="calendar-nav-btn-inline" id="nextMonthBtn" ${currentMonth === endMonth ? 'disabled' : ''}>❯</button>
                    </div>
                    ${isZeilpunter ? '<div style="text-align: center; margin-top: 0.75rem; padding: 0.5rem; background: #fff3cd; color: #856404; border-radius: 0.5rem; font-size: 0.85rem; border: 1px solid #ffeeba;">ℹ️ De zeilpunter kan alleen voor één dag worden geboekt</div>' : ''}
                </div>
                <div class="calendar-grid-inline">
        `;

        // Day headers
        dayNames.forEach(day => {
            html += `<div class="calendar-day-header-inline">${day}</div>`;
        });

        // Empty cells for days before month starts
        for (let i = 0; i < startingDayOfWeek; i++) {
            html += `<div class="calendar-day-inline empty-inline"></div>`;
        }

        // Days of the month
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Check availability for all dates in parallel
        const availabilityPromises = [];
        const dateArray = [];

        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(currentYear, currentMonth, day);
            // Create date string in YYYY-MM-DD format using local timezone (not UTC)
            const year = currentYear;
            const month = String(currentMonth + 1).padStart(2, '0');
            const dayStr = String(day).padStart(2, '0');
            const dateStr = `${year}-${month}-${dayStr}`;
            
            // Check if date is in season (April 1 - October 31)
            const isInSeason = (currentMonth >= 3 && currentMonth <= 9) && 
                             (currentMonth > 3 || day >= 1) && 
                             (currentMonth < 9 || day <= 31);
            
            if (!isInSeason) {
                html += `<div class="calendar-day-inline empty-inline"></div>`;
                continue;
            }

            dateArray.push({ day, date, dateStr });
            
            // Check availability if boat is selected
            if (calendarState.currentBoatId) {
                availabilityPromises.push(
                    getBoatTotal(calendarState.currentBoatId).then(total => {
                        // checkAvailability is synchronous, so call it directly
                        const available = bookingService.checkAvailability(
                            calendarState.currentBoatId, 
                            dateStr, 
                            dateStr,
                            total
                        );
                        return { dateStr, available };
                    })
                );
            } else {
                availabilityPromises.push(Promise.resolve({ dateStr, available: true }));
            }
        }

        // Wait for all availability checks
        Promise.all(availabilityPromises).then(availabilityResults => {
            const availabilityMap = {};
            availabilityResults.forEach(result => {
                availabilityMap[result.dateStr] = result.available;
            });

            // Build HTML with availability
            let finalHtml = html;
            
            dateArray.forEach(({ day, date, dateStr }) => {
                // Check if date is in the past
                const isPast = date < today;
                
                // Get availability
                const isAvailable = availabilityMap[dateStr] !== false;

                // Check if date is in selected range
                let className = 'calendar-day-inline';
                if (isPast) {
                    className += ' past-inline';
                } else if (!isAvailable) {
                    className += ' unavailable-inline';
                } else {
                    className += ' available-inline';
                }

                if (dateStr === `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`) {
                    className += ' today-inline';
                }

                if (calendarState.selectedStartDate && dateStr === calendarState.selectedStartDate) {
                    className += ' selected-start-inline';
                } else if (calendarState.selectedEndDate && dateStr === calendarState.selectedEndDate) {
                    className += ' selected-end-inline';
                } else if (calendarState.selectedStartDate && calendarState.selectedEndDate) {
                    const start = new Date(calendarState.selectedStartDate);
                    const end = new Date(calendarState.selectedEndDate);
                    if (date >= start && date <= end) {
                        className += ' selected-range-inline';
                    }
                }

                finalHtml += `<div class="${className}" data-date="${dateStr}" ${isPast || !isAvailable ? '' : 'onclick="window.HomePage.selectDate(\'' + dateStr + '\')"'}>${day}</div>`;
            });

            finalHtml += `
                    </div>
                </div>
            `;

            calendarContainer.innerHTML = finalHtml;

            // Re-add navigation handlers
            const prevBtn = document.getElementById('prevMonthBtn');
            const nextBtn = document.getElementById('nextMonthBtn');
            
            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    if (calendarState.currentMonth > startMonth) {
                        calendarState.currentMonth--;
                    } else if (calendarState.currentYear > new Date().getFullYear()) {
                        calendarState.currentMonth = endMonth;
                        calendarState.currentYear--;
                    }
                    renderCalendar();
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    if (calendarState.currentMonth < endMonth) {
                        calendarState.currentMonth++;
                    } else {
                        calendarState.currentMonth = startMonth;
                        calendarState.currentYear++;
                    }
                    renderCalendar();
                });
            }

            // Close button handler
            const closeBtn = document.getElementById('calendarCloseBtn');
            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    const calendarContainer = document.getElementById('calendarContainer');
                    const dateRangeInput = document.getElementById('dateRange');
                    if (calendarContainer) {
                        calendarContainer.style.display = 'none';
                        if (dateRangeInput) {
                            dateRangeInput.setAttribute('aria-expanded', 'false');
                        }
                    }
                });
            }
        });


    }

    async function getBoatTotal(boatId) {
        if (!window.BoatDataService) return 1;
        try {
            const boats = await window.BoatDataService.getAllBoats();
            const boat = boats.find(b => b.id === boatId);
            return boat ? (boat.total ?? 1) : 1;
        } catch (e) {
            // Fallback to localStorage
            const stored = localStorage.getItem('nijenhuis_boats');
            if (stored) {
                const boats = JSON.parse(stored);
                const boat = boats.find(b => b.id === boatId);
                return boat ? (boat.total ?? 1) : 1;
            }
            return 1;
        }
    }

    // Global function for date selection (called from inline onclick)
    function selectDate(dateStr) {
        // Parse date string to Date object for comparison
        const parseLocalDate = (dateStr) => {
            const parts = dateStr.split('-');
            return new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
        };
        
        const clickedDate = parseLocalDate(dateStr);
        
        // Check if current boat is zeilpunter (sailpunter-3-4) - only allows one day bookings
        const boatSelect = document.getElementById('boatType');
        const isZeilpunter = boatSelect && boatSelect.value === 'sailpunter-3-4';
        
        // If clicking the same date that's already selected as start, treat as single day booking
        if (calendarState.selectedStartDate === dateStr && !calendarState.selectedEndDate) {
            // Single day booking - set end date to same as start date
            calendarState.selectedEndDate = dateStr;
            calendarState.isSelecting = false;
            updateDateInputs();
            updateQuantityDropdown().then(() => {
                updatePriceDisplay();
            });
            // Update boat fleet availability
            renderBoatFleetSection(calendarState.selectedStartDate);
            // Close calendar after both dates are selected (single day)
            const calendarContainer = document.getElementById('calendarContainer');
            if (calendarContainer) {
                setTimeout(() => {
                    calendarContainer.style.display = 'none';
                }, 300);
            }
            renderCalendar();
            return;
        }
        
        // If we have a start date but no end date, we're selecting the end date
        if (calendarState.selectedStartDate && !calendarState.selectedEndDate) {
            // For zeilpunter, force end date to be same as start date (one day only)
            if (isZeilpunter) {
                calendarState.selectedEndDate = calendarState.selectedStartDate;
                calendarState.isSelecting = false;
                updateDateInputs();
                updateQuantityDropdown().then(() => {
                    updatePriceDisplay();
                });
                renderBoatFleetSection(calendarState.selectedStartDate);
                // Close calendar
                const calendarContainer = document.getElementById('calendarContainer');
                if (calendarContainer) {
                    setTimeout(() => {
                        calendarContainer.style.display = 'none';
                    }, 300);
                }
                renderCalendar();
                return;
            }
            
            // Complete selection for other boats
            const start = parseLocalDate(calendarState.selectedStartDate);
            
            if (clickedDate < start) {
                // Swap if end is before start
                calendarState.selectedEndDate = calendarState.selectedStartDate;
                calendarState.selectedStartDate = dateStr;
            } else {
                calendarState.selectedEndDate = dateStr;
            }
            calendarState.isSelecting = false;
            
            // Update hidden inputs and price
            updateDateInputs();
            updateQuantityDropdown().then(() => {
                updatePriceDisplay();
            });
            // Update boat fleet availability
            renderBoatFleetSection(calendarState.selectedStartDate);
            // Close calendar after both dates are selected
            const calendarContainer = document.getElementById('calendarContainer');
            if (calendarContainer) {
                setTimeout(() => {
                    calendarContainer.style.display = 'none';
                }, 300);
            }
        } else {
            // Start new selection (either no start date, or both dates are set - start fresh)
            calendarState.selectedStartDate = dateStr;
            
            // For zeilpunter, automatically set end date to same as start date (one day only)
            if (isZeilpunter) {
                calendarState.selectedEndDate = dateStr;
                calendarState.isSelecting = false;
                // Close calendar immediately for zeilpunter since it's single day only
                const calendarContainer = document.getElementById('calendarContainer');
                if (calendarContainer) {
                    setTimeout(() => {
                        calendarContainer.style.display = 'none';
                    }, 300);
                }
            } else {
                calendarState.selectedEndDate = null;
                calendarState.isSelecting = true;
            }
            
            // Update inputs - for single day, we'll show price immediately
            updateDateInputs();
            // Show price even for single day (will be updated when end date is set or on second click)
            updatePriceDisplay();
            // Update boat fleet availability
            renderBoatFleetSection(calendarState.selectedStartDate);
            // Keep calendar open when selecting first date (unless it's zeilpunter)
        }
        
        renderCalendar();
    };

    /**
     * Format date to European format (DD/MM/YYYY)
     * @param {string} dateStr - Date string in YYYY-MM-DD format
     * @returns {string} Formatted date in DD/MM/YYYY format
     */
    function formatDateEuropean(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        // Convert YYYY-MM-DD to DD/MM/YYYY
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }

    function updateDateInputs() {
        const dateInput = document.getElementById('date');
        const endDateInput = document.getElementById('rentalEndDate');
        const dateRangeInput = document.getElementById('dateRange');

        // Parse dates in local timezone
        const parseLocalDate = (dateStr) => {
            const parts = dateStr.split('-');
            return new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
        };

        if (dateInput && calendarState.selectedStartDate) {
            dateInput.value = calendarState.selectedStartDate;
        }

        if (endDateInput && calendarState.selectedEndDate) {
            endDateInput.value = calendarState.selectedEndDate;
        } else if (endDateInput && calendarState.selectedStartDate) {
            endDateInput.value = calendarState.selectedStartDate;
        }

        // Update display input with European format (DD/MM/YYYY)
        if (dateRangeInput) {
            if (calendarState.selectedStartDate && calendarState.selectedEndDate) {
                const startStr = formatDateEuropean(calendarState.selectedStartDate);
                const endStr = formatDateEuropean(calendarState.selectedEndDate);
                // Show single date if start and end are the same
                if (calendarState.selectedStartDate === calendarState.selectedEndDate) {
                    dateRangeInput.value = startStr;
                } else {
                    dateRangeInput.value = `${startStr} - ${endStr}`;
                }
            } else if (calendarState.selectedStartDate) {
                dateRangeInput.value = formatDateEuropean(calendarState.selectedStartDate);
            } else {
                dateRangeInput.value = '';
            }
        }
        
        // Update boat fleet availability when date changes
        if (calendarState.selectedStartDate) {
            renderBoatFleetSection(calendarState.selectedStartDate);
        }

        populateBoatDropdown(calendarState.selectedStartDate);
    }

    function validateBookingDate(dateStr) {
        const selectedDate = new Date(dateStr + 'T00:00:00');
        const selectedYear = selectedDate.getFullYear();
        const selectedMonth = selectedDate.getMonth();
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) return { valid: false, message: 'Datum in het verleden.' };
        if (selectedYear !== bookingYear) return { valid: false, message: `Alleen seizoen ${bookingYear}.` };

        // Approxcheck (months 0-indexed)
        if (selectedMonth < (seasonStartMonth - 1) || selectedMonth > (seasonEndMonth - 1)) {
            return { valid: false, message: 'Buiten het seizoen.' };
        }
        return { valid: true };
    }

    async function updateEngineOption() {
        const boatType = document.getElementById('boatType').value;
        const row = document.getElementById('engineOptionRow');
        if (!row) return;

        if (boatType === 'sailboat-4-5') {
            row.style.display = 'flex';

            // Update prices in dropdown text
            let boats = [];
            if (window.BoatDataService) {
                try {
                    boats = await window.BoatDataService.getAllBoats();
                } catch (e) {
                    // Fallback to localStorage
                    const stored = localStorage.getItem('nijenhuis_boats');
                    if (stored) boats = JSON.parse(stored);
                }
            } else {
                // Fallback to localStorage
                const stored = localStorage.getItem('nijenhuis_boats');
                if (stored) boats = JSON.parse(stored);
            }
            
            const boat = boats.find(b => b.id === 'sailboat-4-5');
            if (boat) {
                const engineOptionSelect = document.getElementById('engineOption');
                const withoutPrice = boat.pricePerDay || 70;
                const withPrice = (boat.pricingWithEngine && boat.pricingWithEngine[0]) ? boat.pricingWithEngine[0] : (withoutPrice + 15);

                const opts = engineOptionSelect.options;
                if (opts[0]) opts[0].textContent = `Zonder motor (€${withoutPrice})`;
                if (opts[1]) opts[1].textContent = `Met motor (€${withPrice})`;
            }
        } else {
            row.style.display = 'none';
            const engineOptionSelect = document.getElementById('engineOption');
            if (engineOptionSelect) engineOptionSelect.value = 'without';
        }
    }

    async function updateQuantityDropdown() {
        const boatSelect = document.getElementById('boatType');
        const quantitySelect = document.getElementById('boatQuantity');
        const quantityRow = document.getElementById('quantityRow');
        const dateInput = document.getElementById('date');
        const endDateInput = document.getElementById('rentalEndDate');

        if (!boatSelect || !quantitySelect || !quantityRow) return;

        const boatId = boatSelect.value;
        const startDate = dateInput ? dateInput.value : (calendarState.selectedStartDate || null);
        const endDate = endDateInput ? endDateInput.value : (calendarState.selectedEndDate || calendarState.selectedStartDate || startDate);

        if (!boatId || !startDate) {
            quantityRow.style.display = 'none';
            return;
        }

        // Get boat info
        let boats = [];
        if (window.BoatDataService) {
            try {
                boats = await window.BoatDataService.getAllBoats();
            } catch (e) {
                const stored = localStorage.getItem('nijenhuis_boats');
                if (stored) boats = JSON.parse(stored);
            }
        } else {
            const stored = localStorage.getItem('nijenhuis_boats');
            if (stored) boats = JSON.parse(stored);
        }

        const boat = boats.find(b => b.id === boatId);
        if (!boat) {
            quantityRow.style.display = 'none';
            return;
        }

        // Get available count for date range (this is already total - booked)
        const availableCount = await getAvailableBoatCount(boatId, startDate, endDate);
        const maxQuantity = availableCount;

        if (maxQuantity <= 0) {
            quantityRow.style.display = 'none';
            return;
        }

        // Populate dropdown
        quantitySelect.innerHTML = '';
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

        quantityRow.style.display = 'flex';
    }

    async function getAvailableBoatCount(boatId, startDate, endDate) {
        try {
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
        let boats = [];
        if (window.BoatDataService) {
            try {
                boats = await window.BoatDataService.getAllBoats();
            } catch (e) {
                const stored = localStorage.getItem('nijenhuis_boats');
                if (stored) boats = JSON.parse(stored);
            }
        } else {
            const stored = localStorage.getItem('nijenhuis_boats');
            if (stored) boats = JSON.parse(stored);
        }
        const boat = boats.find(b => b.id === boatId);
        return boat ? (boat.total ?? 1) : 1;
    }

    function updatePriceDisplay() {
        const boatSelect = document.getElementById('boatType');
        const priceDisplay = document.getElementById('priceDisplay');
        const priceAmount = document.getElementById('priceAmount');
        const dateInput = document.getElementById('date');
        const endDateInput = document.getElementById('rentalEndDate');
        const engineSelect = document.getElementById('engineOption');

        if (!boatSelect || !boatSelect.value || !priceDisplay || !priceAmount) return;

        const boatId = boatSelect.value;
        const sDate = dateInput ? dateInput.value : (calendarState.selectedStartDate || null);
        // For single day bookings, use start date as end date if no end date is selected
        const eDate = endDateInput ? endDateInput.value : (calendarState.selectedEndDate || calendarState.selectedStartDate || sDate);
        // Only sailboat-4-5 has a motor option; hidden select may still be 'with' from a previous sailboat selection
        const useMotor = boatId === 'sailboat-4-5' && engineSelect && engineSelect.value === 'with';

        // Show price if start date is selected (even for single day bookings)
        if (!sDate) {
            priceDisplay.style.display = 'none';
            return;
        }
        
        // For single day bookings, use start date as end date
        const effectiveEndDate = eDate || sDate;

        // Parse dates in local timezone
        const parseLocalDate = (dateStr) => {
            const parts = dateStr.split('-');
            return new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
        };

        const start = parseLocalDate(sDate);
        const end = parseLocalDate(effectiveEndDate);
        const days = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;

        if (days < 1) {
            priceDisplay.style.display = 'none';
            return;
        }

        // Use CartManager to calculate price (already uses 1/7th for extra days after 7)
        if (window.CartManager) {
            const quantitySelect = document.getElementById('boatQuantity');
            const quantity = quantitySelect ? parseInt(quantitySelect.value || '1') : 1;
            const pricePerBoat = window.CartManager.calculatePrice(boatId, days, useMotor);
            const totalPrice = pricePerBoat * quantity;
            priceAmount.textContent = `€${totalPrice.toFixed(2)}`;
            if (days > 1) {
                priceAmount.textContent += ` voor ${days} dagen`;
            } else {
                priceAmount.textContent += ` voor 1 dag`;
            }
            if (quantity > 1) {
                priceAmount.textContent += ` (${quantity}x)`;
            }
            priceDisplay.style.display = 'block';
        } else {
            priceDisplay.style.display = 'none';
        }
    }

    async function handleFormSubmit() {
        const boatType = document.getElementById('boatType').value;
        const dateInput = document.getElementById('date');
        const endDateInput = document.getElementById('rentalEndDate');
        const date = dateInput ? dateInput.value : (calendarState.selectedStartDate || null);
        // For single day bookings, use start date as end date if no end date is set
        let endDate = endDateInput ? endDateInput.value : (calendarState.selectedEndDate || calendarState.selectedStartDate || date);

        if (!boatType || !date) {
            alert('Vul alle verplichte velden in.');
            return;
        }

        // Zeilpunter can only be booked for one day
        if (boatType === 'sailpunter-3-4' && date !== endDate) {
            alert('De zeilpunter kan alleen voor één dag worden geboekt. De einddatum is automatisch aangepast naar de startdatum.');
            endDate = date;
            // Update the end date input and calendar state
            if (endDateInput) {
                endDateInput.value = date;
            }
            calendarState.selectedEndDate = date;
            updateDateInputs();
        }

        // Store original button text before navigation
        const form = document.getElementById('bookingForm');
        const submitBtn = form ? form.querySelector('button[type="submit"]') : null;
        if (submitBtn && !submitBtn.dataset.originalText) {
            submitBtn.dataset.originalText = submitBtn.textContent || 'Boek nu';
        }

        // Add to cart and redirect to checkout
        if (window.CartManager) {
            const engineSelect = document.getElementById('engineOption');
            const useMotor = boatType === 'sailboat-4-5' && engineSelect && engineSelect.value === 'with';
            const quantitySelect = document.getElementById('boatQuantity');
            const quantity = quantitySelect ? parseInt(quantitySelect.value || '1') : 1;

            // Only continue to checkout after the cart accepts the item.
            try {
                const success = await window.CartManager.addItem(boatType, date, endDate, useMotor, quantity);
                if (!success) {
                    restoreButtonState();
                    return;
                }
            } catch (e) {
                console.warn('Cart add failed, staying on booking form:', e);
                restoreButtonState();
                return;
            }
            window.location.href = '/pages/checkout.php';
        } else {
            // Fallback: redirect with query params if CartManager not available
            const url = `/pages/checkout.php?date=${encodeURIComponent(date)}&boatType=${encodeURIComponent(boatType)}&endDate=${encodeURIComponent(endDate)}`;
            window.location.href = url;
        }
    }

    async function handleAddToCart() {
        const boatType = document.getElementById('boatType').value;
        const dateInput = document.getElementById('date');
        const endDateInput = document.getElementById('rentalEndDate');
        const date = dateInput ? dateInput.value : (calendarState.selectedStartDate || null);
        let endDate = endDateInput ? endDateInput.value : (calendarState.selectedEndDate || calendarState.selectedStartDate || date);

        if (!boatType || !date) {
            alert('Vul alle verplichte velden in.');
            return;
        }

        // Zeilpunter can only be booked for one day
        if (boatType === 'sailpunter-3-4' && date !== endDate) {
            alert('De zeilpunter kan alleen voor één dag worden geboekt. De einddatum is automatisch aangepast naar de startdatum.');
            endDate = date;
            // Update the end date input and calendar state
            if (endDateInput) {
                endDateInput.value = date;
            }
            calendarState.selectedEndDate = date;
            updateDateInputs();
        }

        if (window.CartManager) {
            const engineSelect = document.getElementById('engineOption');
            const useMotor = boatType === 'sailboat-4-5' && engineSelect && engineSelect.value === 'with';
            const quantitySelect = document.getElementById('boatQuantity');
            const quantity = quantitySelect ? parseInt(quantitySelect.value || '1') : 1;
            
            const success = await window.CartManager.addItem(boatType, date, endDate, useMotor, quantity);
            if (success) {
                // Reset button state immediately so buttons are ready when cart closes
                restoreButtonState();
                // Open cart sidebar to show the item was added
                if (window.toggleCartSidebar) {
                    window.toggleCartSidebar();
                }
            }
        } else {
            alert('Winkelwagen is niet beschikbaar. Probeer het later opnieuw.');
        }
    }

    // ============================================
    // FLEET RENDERING (Index Specific)
    // ============================================
    async function renderBoatFleetSection(specificDate = null) {
        const grid = document.getElementById('boatFleetGrid');
        if (!grid) return;

        // Get boats
        let boats = [];
        if (window.BoatDataService) {
            boats = await window.BoatDataService.getAllBoats();
        } else {
            // Fallback minimal
            boats = JSON.parse(localStorage.getItem(boatsStorageKey) || '[]');
        }

        if (boats.length === 0) return; // Wait for data load

        // Get selected date - use specificDate parameter, or get from calendar state, or use date input
        const dateInput = document.getElementById('date');
        const selectedDate = specificDate || 
                           calendarState.selectedStartDate || 
                           (dateInput ? dateInput.value : null);

        grid.innerHTML = '';
        const sorted = [...boats].sort((a, b) => (a.orderId || 99) - (b.orderId || 99));

        // Process boats and check availability
        for (const boat of sorted) {
            // Check availability if date is selected
            let isAvailable = true;
            if (selectedDate && bookingService && bookingService.checkAvailability) {
                // Get boat total (synchronously from the boats array we already have)
                const boatTotal = boat.total ?? 1;
                isAvailable = bookingService.checkAvailability(boat.id, selectedDate, selectedDate, boatTotal);
            }

            // Construct Image URL (assumes specific structure)
            // Use basePath logic if possible, or relative
            // The images paths in original code were: 'Boats/tender-720/...'
            // We need to map them or use boat.image if available
            // Hardcoded map from original index.php to preserve images
            const boatImages = {
                'classic-tender-720': 'Boats/tender-720/tender-720-10-12.jpg',
                'electrosloop-10': 'Boats/electrosloop-10.jpg',
                'classic-tender-570': 'Boats/tender-570/27725985231061602092021.jpg',
                'electrosloop-8': 'Boats/electrosloop-8.jpg',
                'sailboat-4-5': 'Boats/zeilboot-4-5.jpg',
                'sailpunter-3-4': 'Boats/sailpunter-3-4.jpg',
                'electroboat-5': 'Boats/electroboat-5.jpg',
                'canoe-3': 'Boats/canoe-3.jpg',
                'kayak-2': 'Boats/kayak-2.jpg',
                'kayak-1': 'Boats/kayak-1.jpg',
                'sup-board': 'Boats/sub-1.jpg'
            };

            const imgPath = boatImages[boat.id] || 'banner-img.jpg';
            // Assuming we are in /pages/index.php, images are in ../frontend/Images
            // Note: index.php rendered PHP logic for path. We use relative path here.
            // If this script runs on /pages/index.php, relative path to images:
            const imgUrl = `../frontend/Images/${imgPath}`;

            const card = document.createElement('div');
            card.className = 'service-card';
            card.style.display = 'flex';
            card.style.flexDirection = 'column';
            card.style.padding = '0';
            card.style.overflow = 'hidden';
            card.style.position = 'relative';
            card.style.minHeight = 'auto'; // override CSS

            // Grey out unavailable boats
            if (!isAvailable && selectedDate) {
                card.style.opacity = '0.6';
                card.style.filter = 'grayscale(0.5)';
            } else {
                card.style.opacity = '1';
                card.style.filter = 'none';
            }

            const safeName = boat.name.replace(/'/g, "\\'");

            // Status badge - only show if date is selected
            const statusBadge = selectedDate ? (
                isAvailable 
                    ? `<div style="position: absolute; top: 1rem; right: 1rem; background: #10b981; color: white; padding: 0.4rem 0.8rem; border-radius: 1rem; font-weight: bold; z-index: 10; font-size: 0.85rem; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">Beschikbaar</div>`
                    : `<div style="position: absolute; top: 1rem; right: 1rem; background: #ef4444; color: white; padding: 0.4rem 0.8rem; border-radius: 1rem; font-weight: bold; z-index: 10; font-size: 0.85rem; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">Verhuurd</div>`
            ) : '';

            card.innerHTML = `
                <div style="position: relative; width: 100%; height: 200px; overflow: hidden;">
                    ${statusBadge}
                    <img src="${imgUrl}" alt="${boat.name}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;">
                </div>
                <div style="padding: 0.75rem 1rem; width: 100%;">
                    <h3 style="margin: 0 0 0.5rem; font-size: 1.1rem; text-align: center;">${boat.name}</h3>
                    <div class="boat-actions" style="display: flex; gap: 0.5rem; flex-direction: row;">
                         <a href="/${boat.id}" class="btn btn-outline" style="flex: 1; padding: 0.6rem; font-size: 0.9rem; text-decoration: none; display: flex; align-items: center; justify-content: center;" onclick="event.preventDefault(); window.HomePage.openBoatInfo('${boat.id}'); window.history.pushState(null, '', '/${boat.id}');">
                            Meer Info
                        </a>
                        <button class="btn" style="flex: 1; padding: 0.6rem; font-size: 0.9rem; ${!isAvailable && selectedDate ? 'opacity: 0.5; cursor: not-allowed;' : ''}" 
                                onclick="${!isAvailable && selectedDate ? 'return false;' : `window.HomePage.selectBoatFromGrid('${boat.id}')`}" 
                                ${!isAvailable && selectedDate ? 'disabled' : ''}>
                            Boeken
                        </button>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        }
    }

    function petsAllowedForBoatId(boatId) {
        return ['canoe-3', 'sailpunter-3-4', 'electroboat-5'].includes(boatId);
    }

    // --- Boat Modal Slideshow Controller ---
    const boatSlideshow = (() => {
        let currentIndex = 0;
        let isTransitioning = false;
        let autoInterval = null;
        let allImages = [];
        let fullscreenCurrentIndex = 0;
        let fullscreenKeyHandler = null;

        function init(images) {
            allImages = images || [];
            currentIndex = 0;
            isTransitioning = false;
            clearAutoInterval();
            if (allImages.length > 1) {
                autoInterval = setInterval(() => {
                    if (!isTransitioning) changeSlide(1);
                }, 5000);
            }
        }

        function destroy() {
            clearAutoInterval();
            closeFullscreen();
            allImages = [];
            currentIndex = 0;
        }

        function clearAutoInterval() {
            if (autoInterval) { clearInterval(autoInterval); autoInterval = null; }
        }

        function showSlide(index) {
            if (isTransitioning) return;
            isTransitioning = true;

            const container = document.querySelector('#boatInfoBody .modal-slideshow-main');
            if (!container) { isTransitioning = false; return; }
            const slides = container.querySelectorAll('.slide');
            const thumbnails = document.querySelectorAll('#boatInfoBody .modal-slideshow-thumbnail');
            const total = slides.length;
            if (total === 0) { isTransitioning = false; return; }

            if (index < 0) index = total - 1;
            if (index >= total) index = 0;

            slides.forEach(s => s.classList.remove('active'));
            thumbnails.forEach(t => t.classList.remove('active'));
            if (slides[index]) slides[index].classList.add('active');
            if (thumbnails[index]) thumbnails[index].classList.add('active');
            currentIndex = index;

            setTimeout(() => { isTransitioning = false; }, 600);
        }

        function changeSlide(dir) {
            showSlide(currentIndex + dir);
        }

        function goToSlide(index) {
            showSlide(index);
        }

        function openFullscreen() {
            if (!allImages.length) return;
            closeFullscreen();

            const overlay = document.createElement('div');
            overlay.className = 'fullscreen-slideshow-overlay';
            overlay.id = 'boatFullscreenSlideshow';

            let html = '<button class="fullscreen-close" onclick="window.HomePage.slideshow.closeFullscreen()" aria-label="Sluiten">&times;</button>';
            if (allImages.length > 1) {
                html += '<button class="fullscreen-btn prev" onclick="window.HomePage.slideshow.fullscreenChangeSlide(-1)" aria-label="Vorige"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg></button>';
                html += '<button class="fullscreen-btn next" onclick="window.HomePage.slideshow.fullscreenChangeSlide(1)" aria-label="Volgende"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg></button>';
            }

            html += '<div class="fullscreen-slideshow-main">';
            allImages.forEach((src, i) => {
                html += `<img src="${src}" alt="Foto ${i + 1}" class="fullscreen-slide${i === currentIndex ? ' active' : ''}" loading="lazy">`;
            });
            html += '</div>';

            if (allImages.length > 1) {
                html += '<div class="fullscreen-thumbnails">';
                allImages.forEach((src, i) => {
                    html += `<div class="fullscreen-thumbnail${i === currentIndex ? ' active' : ''}" onclick="window.HomePage.slideshow.fullscreenGoToSlide(${i})"><img src="${src}" alt="Thumbnail ${i + 1}"></div>`;
                });
                html += '</div>';
            }

            overlay.innerHTML = html;
            document.body.appendChild(overlay);
            document.body.style.overflow = 'hidden';
            fullscreenCurrentIndex = currentIndex;

            fullscreenKeyHandler = function(e) {
                if (e.key === 'Escape') closeFullscreen();
                else if (e.key === 'ArrowLeft') fullscreenChangeSlide(-1);
                else if (e.key === 'ArrowRight') fullscreenChangeSlide(1);
            };
            document.addEventListener('keydown', fullscreenKeyHandler);
        }

        function closeFullscreen() {
            const overlay = document.getElementById('boatFullscreenSlideshow');
            if (overlay) {
                if (fullscreenKeyHandler) {
                    document.removeEventListener('keydown', fullscreenKeyHandler);
                    fullscreenKeyHandler = null;
                }
                overlay.remove();
                const modal = document.getElementById('boatInfoModal');
                if (modal && modal.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                }
            }
        }

        function fullscreenChangeSlide(dir) {
            const main = document.querySelector('#boatFullscreenSlideshow .fullscreen-slideshow-main');
            if (!main) return;
            const slides = main.querySelectorAll('.fullscreen-slide');
            const thumbs = document.querySelectorAll('#boatFullscreenSlideshow .fullscreen-thumbnail');
            const total = slides.length;
            if (!total) return;

            fullscreenCurrentIndex += dir;
            if (fullscreenCurrentIndex >= total) fullscreenCurrentIndex = 0;
            else if (fullscreenCurrentIndex < 0) fullscreenCurrentIndex = total - 1;

            slides.forEach((s, i) => s.classList.toggle('active', i === fullscreenCurrentIndex));
            thumbs.forEach((t, i) => t.classList.toggle('active', i === fullscreenCurrentIndex));
        }

        function fullscreenGoToSlide(index) {
            const main = document.querySelector('#boatFullscreenSlideshow .fullscreen-slideshow-main');
            if (!main) return;
            const slides = main.querySelectorAll('.fullscreen-slide');
            const thumbs = document.querySelectorAll('#boatFullscreenSlideshow .fullscreen-thumbnail');
            if (index < 0 || index >= slides.length) return;

            fullscreenCurrentIndex = index;
            slides.forEach((s, i) => s.classList.toggle('active', i === index));
            thumbs.forEach((t, i) => t.classList.toggle('active', i === index));
        }

        return { init, destroy, showSlide, changeSlide, goToSlide, openFullscreen, closeFullscreen, fullscreenChangeSlide, fullscreenGoToSlide };
    })();

    async function openBoatInfo(boatId) {
        const modal = document.getElementById('boatInfoModal');
        const body = document.getElementById('boatInfoBody');
        const closeBtn = document.getElementById('boatInfoClose');
        if (!modal || !body) {
            // Fallback if modal markup isn't present
            window.location.href = 'botenverhuur.php';
            return;
        }

        let boat = null;
        try {
            if (window.BoatDataService) {
                boat = await window.BoatDataService.getBoatById(boatId);
            } else {
                const boats = JSON.parse(localStorage.getItem(boatsStorageKey) || '[]');
                boat = boats.find(b => b.id === boatId) || null;
            }
        } catch (e) { /* ignore */ }

        if (!boat) return;

        const allowed = petsAllowedForBoatId(boatId);
        const petsText = allowed ? 'Toegestaan (kano/zeilpunter/electroboot)' : 'Niet toegestaan';

        const escapeHTML = (text) => {
            if (text == null) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        const formatEUR = (n) => {
            const num = Number(n || 0);
            return `€${num.toFixed(2)}`;
        };

        const getPriceForDays = (days, pricingArr, basePerDay) => {
            const perDay = Number(basePerDay || 0);
            const arr = Array.isArray(pricingArr) ? pricingArr : (pricingArr && typeof pricingArr === 'object' ? pricingArr : []);
            const key = days - 1;
            // Many boats have pricing[0] = 0; always prefer pricePerDay for 1 day.
            if (days === 1) return perDay > 0 ? perDay : (Number(arr[0] ?? arr['0'] ?? 0) || 0);
            const candidate = Number(arr[key] ?? arr[String(key)] ?? 0);
            if (candidate > 0) return candidate;
            return perDay * days;
        };

        const buildPricingTable = (title, pricingArr) => {
            const availableDays = Array.isArray(boat.availableDays) && boat.availableDays.length
                ? boat.availableDays
                : [1, 2, 3, 4, 5, 6, 7];

            const rows = [];
            for (let d = 1; d <= 7; d++) {
                if (!availableDays.includes(d)) continue;
                const price = getPriceForDays(d, pricingArr, boat.pricePerDay);
                if (!price) continue;
                rows.push({ d, price });
            }

            // Week price = 7 days (if available), else computed
            const weekPrice = getPriceForDays(7, pricingArr, boat.pricePerDay);
            const extraDayPrice = weekPrice > 0 ? (weekPrice / 7) : 0;

            const rowsHtml = rows.map(r => `
                <tr>
                    <td style="padding: 10px 12px; border-bottom: 1px solid #eee;">${r.d === 1 ? '1 dag' : `${r.d} dagen`}</td>
                    <td style="padding: 10px 12px; border-bottom: 1px solid #eee; text-align: right; font-weight: 700; white-space: nowrap;">${formatEUR(r.price)}</td>
                </tr>
            `).join('');

            return `
                <div class="boat-modal-section boat-modal-section-scroll" style="margin-bottom: 18px;">
                    <div class="boat-modal-section-header">
                        <h3>${escapeHTML(title)}</h3>
                    </div>
                    <div class="boat-modal-section-body">
                        <table style="width: 100%; border-collapse: collapse; background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding: 10px 12px; font-size: 0.9rem; color: #374151; border-bottom: 1px solid #e5e7eb;">Duur</th>
                                <th style="text-align: right; padding: 10px 12px; font-size: 0.9rem; color: #374151; border-bottom: 1px solid #e5e7eb;">Prijs</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rowsHtml || `<tr><td colspan="2" style="padding: 10px 12px; color:#6b7280;">-</td></tr>`}
                        </tbody>
                        </table>
                        ${rows.length ? `
                            <div style="margin-top: 10px; padding: 10px 12px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 10px; color: #856404; font-size: 0.95rem;">
                                <strong>Meer dan 1 week?</strong> Elke extra dag boven een week kost <strong>${formatEUR(extraDayPrice)}</strong> (1/7 van de weekprijs).
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        };

        /** Single pricing table for sailboat: Days | With engine | Without engine. */
        const buildSailboatPricingSection = () => {
            const availableDays = Array.isArray(boat.availableDays) && boat.availableDays.length
                ? boat.availableDays
                : [1, 2, 3, 4, 5, 6, 7];
            const pad = '8px 10px';
            const thStyle = `text-align: left; padding: ${pad}; font-size: 0.9rem; color: #374151; border-bottom: 1px solid #e5e7eb;`;
            const thRight = thStyle.replace('left', 'right');
            const tdStyle = `padding: ${pad}; border-bottom: 1px solid #eee; font-size: 0.9rem;`;
            const tdRight = `${tdStyle} text-align: right; font-weight: 700; white-space: nowrap;`;

            const hasEngine = boat.pricingWithEngine && (
                Array.isArray(boat.pricingWithEngine) ? boat.pricingWithEngine.length : Object.keys(boat.pricingWithEngine).length
            ) > 0;
            const pe = boat.pricingWithEngine;
            const baseMet = hasEngine
                ? ((typeof pe === 'object' && pe !== null && (pe[0] ?? pe['0']) != null) ? Number(pe[0] ?? pe['0']) : (Number(boat.pricePerDay || 0) + 15))
                : null;

            const rows = [];
            for (let d = 1; d <= 7; d++) {
                if (!availableDays.includes(d)) continue;
                const priceZonder = getPriceForDays(d, boat.pricing, boat.pricePerDay);
                const priceMet = hasEngine && baseMet != null ? getPriceForDays(d, boat.pricingWithEngine, baseMet) : 0;
                if (!priceZonder && !priceMet) continue;
                rows.push({ d, priceZonder, priceMet: priceMet || 0 });
            }

            const weekZonder = getPriceForDays(7, boat.pricing, boat.pricePerDay);
            const weekMet = hasEngine && baseMet != null ? getPriceForDays(7, boat.pricingWithEngine, baseMet) : 0;
            const extraZonder = weekZonder > 0 ? weekZonder / 7 : 0;
            const extraMet = weekMet > 0 ? weekMet / 7 : 0;
            const showNote = rows.length > 0;

            const rowsHtml = rows.map(r => `
                <tr>
                    <td style="${tdStyle}">${r.d === 1 ? '1 dag' : `${r.d} dagen`}</td>
                    <td style="${tdRight}">${hasEngine && r.priceMet ? formatEUR(r.priceMet) : '-'}</td>
                    <td style="${tdRight}">${r.priceZonder ? formatEUR(r.priceZonder) : '-'}</td>
                </tr>
            `).join('');

            const noteHtml = showNote ? `
                <div style="margin-top: 10px; padding: 10px 12px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 10px; color: #856404; font-size: 0.95rem;">
                    <strong>Meer dan 1 week?</strong> Extra dag:${hasEngine ? ` met motor <strong>${formatEUR(extraMet)}</strong>,` : ''} zonder motor <strong>${formatEUR(extraZonder)}</strong> (1/7 van de weekprijs).
                </div>
            ` : '';

            return `
                <div class="boat-modal-section boat-modal-section-scroll" style="margin-bottom: 18px;">
                    <div class="boat-modal-section-header">
                        <h3>Tarieven</h3>
                    </div>
                    <div class="boat-modal-section-body">
                        <table style="width: 100%; border-collapse: collapse; background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
                            <thead>
                                <tr>
                                    <th style="${thStyle}">Duur</th>
                                    <th style="${thRight}">Met motor</th>
                                    <th style="${thRight}">Zonder motor</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rowsHtml || `<tr><td colspan="3" style="${tdStyle} color:#6b7280;">-</td></tr>`}
                            </tbody>
                        </table>
                        ${noteHtml}
                    </div>
                </div>
            `;
        };

        // Image mapping (same as fleet card)
        const boatImages = {
            'classic-tender-720': 'Boats/tender-720/tender-720-10-12.jpg',
            'electrosloop-10': 'Boats/electrosloop-10.jpg',
            'classic-tender-570': 'Boats/tender-570/27725985231061602092021.jpg',
            'electrosloop-8': 'Boats/electrosloop-8.jpg',
            'sailboat-4-5': 'Boats/zeilboot-4-5.jpg',
            'sailpunter-3-4': 'Boats/sailpunter-3-4.jpg',
            'electroboat-5': 'Boats/electroboat-5.jpg',
            'canoe-3': 'Boats/canoe-3.jpg',
            'kayak-2': 'Boats/kayak-2.jpg',
            'kayak-1': 'Boats/kayak-1.jpg',
            'sup-board': 'Boats/sub-1.jpg'
        };
        const primaryImgUrl = `../frontend/Images/${boatImages[boatId] || 'banner-img.jpg'}`;

        const galleryImages = (() => {
            const list = [];
            const seen = new Set();
            const add = (p) => { if (p && !seen.has(p)) { seen.add(p); list.push(p); } };
            add(primaryImgUrl);
            (boat.photos || []).forEach(p => add(p?.url || p));
            return list;
        })();
        const hasMultiple = galleryImages.length > 1;

        const slidesHtml = galleryImages.map((src, i) =>
            `<img src="${escapeHTML(src)}" alt="${escapeHTML(boat.name)} - ${i + 1}" class="slide${i === 0 ? ' active' : ''}" loading="${i === 0 ? 'eager' : 'lazy'}" onerror="this.style.display='none'">`
        ).join('');

        const navBtnsHtml = hasMultiple ? `
            <button class="modal-slideshow-btn prev" onclick="event.stopPropagation(); window.HomePage.slideshow.changeSlide(-1)" aria-label="Vorige">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button class="modal-slideshow-btn next" onclick="event.stopPropagation(); window.HomePage.slideshow.changeSlide(1)" aria-label="Volgende">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
            </button>` : '';

        const fullscreenBtnHtml = `
            <button class="modal-fullscreen-btn" onclick="event.stopPropagation(); window.HomePage.slideshow.openFullscreen()" aria-label="Volledig scherm" title="Volledig scherm">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
            </button>`;

        const thumbnailsHtml = hasMultiple ? `
            <div class="modal-slideshow-thumbnails">
                ${galleryImages.map((src, i) =>
                    `<div class="modal-slideshow-thumbnail${i === 0 ? ' active' : ''}" onclick="window.HomePage.slideshow.goToSlide(${i})">
                        <img src="${escapeHTML(src)}" alt="Thumbnail ${i + 1}" loading="lazy">
                    </div>`
                ).join('')}
            </div>` : '';

        const price = Number(boat.pricePerDay || 0);
        const capacity = boat.passengerCount || boat.capacity || '';
        const description = escapeHTML(boat.description || '');
        const deposit = Number(boat.deposit || 0);

        const pricingHtml = (boat.id === 'sailboat-4-5')
            ? buildSailboatPricingSection()
            : buildPricingTable('Tarieven', boat.pricing);

        body.innerHTML = `
            <div class="boat-modal-header" style="flex-direction: column; padding: 0;">
                <div class="modal-slideshow">
                    <div class="modal-slideshow-main" onclick="window.HomePage.slideshow.openFullscreen()" style="cursor: pointer;">
                        ${slidesHtml}
                    </div>
                    ${navBtnsHtml}
                    ${fullscreenBtnHtml}
                    ${thumbnailsHtml}
                </div>
                <div class="boat-modal-title" style="padding: 24px 30px;">
                    <h2>${escapeHTML(boat.name)}</h2>
                    <p class="boat-modal-price">${formatEUR(price)} / dag</p>
                    <p class="boat-modal-capacity">${escapeHTML(capacity)}</p>
                </div>
            </div>
            <div class="boat-modal-body">
                <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 18px;">
                    <div>
                        <div class="boat-modal-section boat-modal-section-scroll">
                            <div class="boat-modal-section-header">
                                <h3>Beschrijving</h3>
                            </div>
                            <div class="boat-modal-section-body">
                                <p>${description || '-'}</p>
                            </div>
                        </div>

                        <div class="boat-modal-section boat-modal-section-scroll">
                            <div class="boat-modal-section-header">
                                <h3>Specificaties</h3>
                            </div>
                            <div class="boat-modal-section-body">
                                <div class="specifications-grid">
                                    <div class="spec-item"><strong>Borg:</strong> ${formatEUR(deposit)}</div>
                                    <div class="spec-item"><strong>Capaciteit:</strong> ${escapeHTML(capacity || '-')}</div>
                                    <div class="spec-item"><strong>Huisdieren:</strong> ${escapeHTML(petsText)}</div>
                                    <div class="spec-item"><strong>Boottype:</strong> ${escapeHTML(boat.id || '')}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        ${pricingHtml}
                    </div>
                </div>

                <style>
                    @media (max-width: 860px) {
                        #boatInfoBody .boat-modal-body > div[style*="grid-template-columns"] {
                            grid-template-columns: 1fr !important;
                        }
                    }
                </style>
                <div class="boat-modal-section" style="display:flex; gap: 12px; justify-content:flex-end;">
                    <button class="btn btn-outline" type="button" onclick="document.getElementById('boatInfoClose')?.click()">Sluiten</button>
                    <button class="btn btn-primary" type="button" onclick="document.getElementById('boatInfoClose')?.click(); window.HomePage.selectBoatFromGrid('${boat.id}')">Boeken</button>
                </div>
            </div>
        `;
        boatSlideshow.init(galleryImages);

        const closeModal = () => {
            boatSlideshow.destroy();
            modal.classList.remove('active');
            document.body.style.overflow = '';
            const pathName = window.location.pathname.substring(1).replace(/\/$/, "");
            if (pathName) {
                const ignorePaths = ['botenverhuur', 'vakantiehuis', 'te-koop', 'camping', 'vaarkaart', 'contact', 'booking', 'checkout', 'giethoorn', 'belt-schutsloot', 'wanneperveen', 'veelgestelde-vragen', 'offline', 'admin'];
                if (!ignorePaths.includes(pathName) && !pathName.includes('.php') && !pathName.includes('pages/')) {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.pathname = '/';
                    window.history.pushState(null, '', currentUrl.toString());
                }
            }
        };

        // Ensure close works + overlay click closes
        if (closeBtn && !closeBtn.dataset.bound) {
            closeBtn.addEventListener('click', closeModal);
            closeBtn.dataset.bound = '1';
        }
        if (!modal.dataset.bound) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal();
                }
            });
            modal.dataset.bound = '1';
        }

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Select boat from grid and open booking form
    function selectBoatFromGrid(boatId) {
        const boatSelect = document.getElementById('boatType');
        if (!boatSelect) return;

        boatSelect.value = boatId;
        calendarState.currentBoatId = boatId;
        updateEngineOption();
        updatePriceDisplay();

        // Scroll to booking form and open calendar
        const bookingForm = document.getElementById('booking');
        if (bookingForm) {
            bookingForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        const dateRangeInput = document.getElementById('dateRange');
        if (dateRangeInput) {
            dateRangeInput.focus();
            toggleCalendar();
        }
    }

    // Public API
    return {
        selectBoatFromGrid,
        openBoatInfo,
        selectDate,
        slideshow: boatSlideshow
    };

})();
