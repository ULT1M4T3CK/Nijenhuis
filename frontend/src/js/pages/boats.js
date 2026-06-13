/**
 * BOOTVERHUUR PAGE LOGIC
 * Refactored from botenverhuur.php
 */
(function (window) {
    'use strict';

    // Dependencies
    const { useBoatData, useBookingAvailability } = window;

    // State
    let currentCategory = 'all';
    let currentCalendarBoat = null;
    let currentCalendarDate = new Date();
    let selectionStartDate = null;
    let selectionEndDate = null;

    // Services
    const boatData = useBoatData();
    const bookingService = useBookingAvailability();

    // DOM Elements - lazily retrieved or cached? 
    // Best to retrieve when needed to accept dynamic updates, or cache in init.

    async function init() {
        console.log('Initializing Boats Page (Calendar Only)...');
        initScrollReveal();
        initAnchorListReveal();
        setTimeout(initAnchorListReveal, 600);

        if (typeof window.refreshI18n === 'function') {
            const origRefresh = window.refreshI18n;
            window.refreshI18n = function () {
                origRefresh();
                initAnchorListReveal();
            };
        }

        // Check for boat in URL (Calendar Auto-open)
        const urlParams = new URLSearchParams(window.location.search);
        const boatIdParam = urlParams.get('boat');
        if (boatIdParam) {
            showAvailabilityCalendar(boatIdParam);
        }

        // Subscribe to booking updates to refresh calendar if open
        bookingService.subscribe(() => {
            if (currentCalendarBoat && document.getElementById('availabilityCalendarModal').classList.contains('active')) {
                renderCalendar();
            }
        });
    }

    // --- Calendar Logic ---

    function showAvailabilityCalendar(boatId) {
        boatData.getById(boatId).then(boat => {
            if (!boat) return;

            currentCalendarBoat = boat;
            currentCalendarDate = new Date();

            // Re-init calendar state
            // If April < Month < Oct, use current, else reset to next season
            const m = currentCalendarDate.getMonth();
            if (m < 3) {
                currentCalendarDate.setMonth(3); currentCalendarDate.setDate(1);
            } else if (m > 9) {
                currentCalendarDate.setFullYear(currentCalendarDate.getFullYear() + 1);
                currentCalendarDate.setMonth(3); currentCalendarDate.setDate(1);
            }

            selectionStartDate = null;
            selectionEndDate = null;

            // Only update DOM if modal exists
            const modal = document.getElementById('availabilityCalendarModal');
            if (!modal) return;

            document.getElementById('calendarBoatName').textContent = `${boat.name} - Beschikbaarheid`;

            // Motor Options
            const optionsContainer = document.getElementById('boatOptions');
            optionsContainer.innerHTML = '';
            if (boat.id === 'sailboat-4-5') {
                const label = document.createElement('label');
                label.style.cssText = 'display: inline-flex; align-items: center; gap: 8px; cursor: pointer; user-select: none; font-weight: 500;';
                label.innerHTML = `<input type="checkbox" id="useMotor"> Met buitenboordmotor (+ meerprijs)`;
                label.querySelector('input').addEventListener('change', updateSelectionInfo);
                optionsContainer.appendChild(label);
            }

            updateNavButtons();
            updateSelectionInfo();
            renderCalendar();

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Close info modal if open
            const infoModal = document.getElementById('boatInfoModal');
            if (infoModal) infoModal.classList.remove('active');
        });
    }

    function renderCalendar() {
        const grid = document.getElementById('calendarGrid');
        const monthYearEl = document.getElementById('currentMonthYear');
        if (!grid || !monthYearEl) return;

        const year = currentCalendarDate.getFullYear();
        const month = currentCalendarDate.getMonth();

        const monthNames = ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'];
        monthYearEl.textContent = `${monthNames[month]} ${year}`;

        grid.innerHTML = '';

        // Header
        ['Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za', 'Zo'].forEach(day => {
            const d = document.createElement('div');
            d.className = 'calendar-day-header';
            d.textContent = day;
            grid.appendChild(d);
        });

        const firstDay = (new Date(year, month, 1).getDay() + 6) % 7;
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date(); today.setHours(0, 0, 0, 0);

        // Empties
        for (let i = 0; i < firstDay; i++) {
            const e = document.createElement('div');
            e.className = 'calendar-day empty';
            grid.appendChild(e);
        }

        // Days
        for (let day = 1; day <= daysInMonth; day++) {
            const dayEl = document.createElement('div');
            dayEl.className = 'calendar-day';
            dayEl.textContent = day;

            const dateObj = new Date(year, month, day);
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

            // Check status
            // Season check
            const isSeason = (month >= 3 && month <= 9 && !(month === 9 && day > 31)); // Oct 31 is valid
            const isPast = dateObj < today;

            if (!isSeason || isPast) {
                dayEl.classList.add(isPast ? 'past' : 'unavailable'); // out of season looks unavailable
            } else {
                // Check actual availability
                // Pass 1 as boatTotalConfig? The hook needs it? 
                // Wait, checkAvailability needs to know TOTAL boats to verify saturation.
                // We pass currentCalendarBoat.total
                const available = bookingService.checkAvailability(currentCalendarBoat.id, dateStr, dateStr, currentCalendarBoat.total ?? 1);

                if (!available) {
                    dayEl.classList.add('unavailable');
                } else {
                    dayEl.onclick = () => handleDateClick(dateObj);
                }
            }

            // Selection Highlights
            if (selectionStartDate && selectionEndDate) {
                if (dateObj >= selectionStartDate && dateObj <= selectionEndDate) dayEl.classList.add('selected-range');
                if (dateObj.getTime() === selectionStartDate.getTime()) dayEl.classList.add('selected-start');
                if (dateObj.getTime() === selectionEndDate.getTime()) dayEl.classList.add('selected-end');
            } else if (selectionStartDate) {
                if (dateObj.getTime() === selectionStartDate.getTime()) dayEl.classList.add('selected-start');
            }

            if (dateObj.getTime() === today.getTime()) dayEl.classList.add('today');

            grid.appendChild(dayEl);
        }
    }

    function handleDateClick(date) {
        if (!selectionStartDate || (selectionStartDate && selectionEndDate)) {
            selectionStartDate = date;
            selectionEndDate = null;
        } else {
            if (date >= selectionStartDate) {
                selectionEndDate = date;
            } else {
                selectionStartDate = date;
                selectionEndDate = null;
            }
        }
        updateSelectionInfo();
        renderCalendar();
    }

    function updateSelectionInfo() {
        // UI Updates...
        const infoBox = document.getElementById('selectionInfo');
        const bookBtn = document.getElementById('bookSelectedBoat');
        const priceText = document.getElementById('selectionPriceText');
        const errorBox = document.getElementById('selectionError');
        const rangeText = document.getElementById('selectionRangeText');

        if (!infoBox) return;

        errorBox.style.display = 'none';

        if (!selectionStartDate) {
            infoBox.style.display = 'none';
            bookBtn.classList.add('disabled');
            bookBtn.textContent = '📅 Selecteer een datum';
            return;
        }

        infoBox.style.display = 'block';
        const startStr = selectionStartDate.toLocaleDateString('nl-NL');

        if (!selectionEndDate) {
            rangeText.textContent = `Van: ${startStr} (kies einddatum)`;
            priceText.textContent = 'Totaal: ...';
            bookBtn.classList.add('disabled');
            bookBtn.textContent = '📅 Kies einddatum';
        } else {
            const endStr = selectionEndDate.toLocaleDateString('nl-NL');
            rangeText.textContent = `Van: ${startStr} Tot: ${endStr}`;

            // Check range availability again
            const sDate = selectionStartDate.toISOString().split('T')[0];
            const eDate = selectionEndDate.toISOString().split('T')[0];
            const valid = bookingService.checkAvailability(currentCalendarBoat.id, sDate, eDate, currentCalendarBoat.total ?? 1);

            if (!valid) {
                errorBox.style.display = 'block';
                priceText.style.display = 'none';
                bookBtn.classList.add('disabled');
                bookBtn.textContent = '⛔ Niet beschikbaar';
            } else {
                priceText.style.display = 'block';
                const days = Math.ceil(Math.abs(selectionEndDate - selectionStartDate) / (1000 * 60 * 60 * 24)) + 1;

                const useMotor = document.getElementById('useMotor')?.checked || false;
                const price = bookingService.calculatePrice(currentCalendarBoat, days, useMotor);

                priceText.textContent = `Totaal: €${price} (${days} dagen)`;

                bookBtn.classList.remove('disabled');
                bookBtn.textContent = '🛒 Toevoegen aan winkelwagen';
                bookBtn.removeAttribute('href');
                bookBtn.onclick = async (e) => {
                    e.preventDefault();
                    if (!window.CartManager) {
                        // Fallback if CartManager not loaded
                        window.location.href = `booking.php?boatType=${currentCalendarBoat.id}&date=${sDate}&endDate=${eDate}${useMotor ? '&motor=true' : ''}`;
                        return;
                    }

                    const success = await window.CartManager.addItem(currentCalendarBoat.id, sDate, eDate, useMotor);
                    if (success) {
                        document.getElementById('availabilityCalendarModal').classList.remove('active');
                        document.body.style.overflow = '';
                    }
                };
            }
        }
    }

    function updateNavButtons() {
        const prevBtn = document.getElementById('prevMonth');
        const nextBtn = document.getElementById('nextMonth');
        if (!prevBtn || !nextBtn) return;

        const m = currentCalendarDate.getMonth();
        // Limits: April(3) - Oct(9)
        const isApril = m === 3;
        const isOct = m === 9;

        prevBtn.disabled = isApril;
        prevBtn.style.opacity = isApril ? '0.3' : '1';

        nextBtn.disabled = isOct;
        nextBtn.style.opacity = isOct ? '0.3' : '1';
    }

    // Modal Events
    function setupModalListeners() {
        document.getElementById('prevMonth')?.addEventListener('click', () => {
            if (currentCalendarDate.getMonth() > 3) {
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
                renderCalendar();
                updateNavButtons();
            }
        });
        document.getElementById('nextMonth')?.addEventListener('click', () => {
            if (currentCalendarDate.getMonth() < 9) {
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
                renderCalendar();
                updateNavButtons();
            }
        });

        // Closers
        document.getElementById('calendarModalClose')?.addEventListener('click', () => {
            document.getElementById('availabilityCalendarModal').classList.remove('active');
            document.body.style.overflow = '';
        });

        // Outside click handled by CSS/HTML structure or specific listener?
        // Reuse logic from old code
        document.getElementById('availabilityCalendarModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // Helper functions
    function escapeHTML(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function t(key, params = {}) {
        let text = window.getTranslation ? window.getTranslation(key) : key;
        Object.keys(params).forEach(param => {
            text = text.replace(`{${param}}`, params[param]);
        });
        return text;
    }

    // --- Scroll reveal & anchor list animations ---

    function initScrollReveal() {
        const cards = document.querySelectorAll('.reveal-card');
        if (!cards.length) return;

        if (!('IntersectionObserver' in window)) {
            cards.forEach(el => el.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        cards.forEach(el => observer.observe(el));
    }

    function initAnchorListReveal() {
        const lists = document.querySelectorAll('.anchor-list');
        if (!lists.length) return;

        const revealList = (list) => {
            if (list.classList.contains('is-revealed')) return;
            list.classList.add('is-revealed');
        };

        if (!('IntersectionObserver' in window)) {
            lists.forEach(revealList);
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    revealList(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        lists.forEach(list => observer.observe(list));
    }

    // Re-run after i18n injects list HTML
    window.addEventListener('languageChanged', () => {
        initAnchorListReveal();
    });

    // Export public API
    window.BoatsPage = {
        init,
        showCalendar: showAvailabilityCalendar
    };

    // Auto-init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            init();
            setupModalListeners();
        });
    } else {
        init();
        setupModalListeners();
    }

})(window);
