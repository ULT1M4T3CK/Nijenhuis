/**
 * useBookingAvailability Hook
 * 
 * Manages fetching bookings, polling for updates, and checking
 * real-time availability of boats against the booking calendar.
 */
(function (window) {
    'use strict';

    // State (closure)
    let _bookings = [];
    let _subscribers = [];
    let _isPolling = false;
    let _pollIntervalId = null;
    let _lastHash = '';

    // Constants
    const STORAGE_KEY = 'nijenhuis_bookings';
    const POLL_INTERVAL = 30000; // 30 sec

    // Helper: secure access to global config
    const getEndpoint = () => {
        if (window.AppConfig && window.AppConfig.detectServerEndpoint) {
            return window.AppConfig.detectServerEndpoint();
        }
        return '../admin/booking-handler.php';
    };

    function useBookingAvailability() {

        /**
         * Fetch bookings from server
         */
        const fetchBookings = async () => {
            try {
                const endpoint = getEndpoint();
                const res = await fetch(`${endpoint}?action=getPublicBookings`, {
                    method: 'POST',
                    body: JSON.stringify({ action: 'getPublicBookings' }),
                    headers: { 'Content-Type': 'application/json' }
                });

                if (res.ok) {
                    const data = await res.json();
                    if (data.success && Array.isArray(data.bookings)) {
                        _bookings = data.bookings;
                        // Cache
                        try {
                            localStorage.setItem(STORAGE_KEY, JSON.stringify(_bookings));
                        } catch (e) { }
                        return _bookings;
                    }
                }
            } catch (e) {
                console.warn('Failed to fetch bookings:', e);
                // Fallback to cache
                const stored = localStorage.getItem(STORAGE_KEY);
                if (stored) _bookings = JSON.parse(stored);
            }
            return _bookings;
        };

        /**
         * Check if a specific boat is available for a date range
         * Logic extracted from old botenverhuur.php
         */
        const checkAvailability = (boatId, startDate, endDate, boatTotalConfig = 1) => {
            if (!_bookings || !_bookings.length) return true; // Assume available if no data? Or false? 
            // In original code: if bookings empty, they return true (loop doesn't run)

            // Parse dates in local timezone to avoid UTC conversion issues
            const parseLocalDate = (dateStr) => {
                const parts = dateStr.split('-');
                return new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            };

            const start = parseLocalDate(startDate);
            const end = parseLocalDate(endDate || startDate);
            start.setHours(0, 0, 0, 0);
            end.setHours(0, 0, 0, 0);

            let currentDate = new Date(start);

            // Iterate through every day in the range
            while (currentDate <= end) {
                // Count active bookings for this boat type on this day
                const activeBookings = _bookings.filter(b => {
                    if (b.boatType !== boatId) return false;

                    // Filter by status (blocking statuses only)
                    // STRICT BLOCKING STATUSES: Only these block the calendar
                    const blocking = ['success', 'manual', 'paid', 'picked_up', 'confirmed', 'confirmed-paid'];
                    // Canceled, pending (online inc), failed, open do NOT block.
                    // Explicitly skip non-blocking statuses
                    if (['canceled', 'cancelled', 'payment-rejected', 'failed', 'expired', 'pending', 'open', 'not-confirmed'].includes(b.status)) return false;

                    // Block if in blocking list
                    if (!blocking.includes(b.status)) return false;

                    const bStart = parseLocalDate(b.date);
                    const bEnd = b.endDate ? parseLocalDate(b.endDate) : parseLocalDate(b.date);
                    bStart.setHours(0, 0, 0, 0);
                    bEnd.setHours(0, 0, 0, 0);

                    return currentDate >= bStart && currentDate <= bEnd;
                });

                if (activeBookings.length >= boatTotalConfig) {
                    return false; // Fully booked on this specific day
                }

                currentDate.setDate(currentDate.getDate() + 1);
            }

            return true;
        };

        /**
         * Calculate price for a booking
         * Every day after 7 days costs 1/7th of the weekly price.
         * @param {Object} boat Boat object from BoatData
         * @param {number} days Number of days
         * @param {boolean} useMotor Whether using motor (for sailboats)
         */
        const calculatePrice = (boat, days, useMotor = false) => {
            if (!boat) return 0;

            let pricing = boat.pricing;
            let pricePerDay = Number(boat.pricePerDay || 0);

            if (useMotor && boat.pricingWithEngine && Object.keys(boat.pricingWithEngine).length > 0) {
                pricing = boat.pricingWithEngine;
                const first = boat.pricingWithEngine[0] ?? boat.pricingWithEngine['0'];
                if (first != null) pricePerDay = Number(first);
            }

            if (days === 1) return pricePerDay;

            if (days >= 2 && days <= 7) {
                const key = String(days - 1);
                if (pricing && pricing[key] != null) return Number(pricing[key]);
                return pricePerDay * days;
            }

            if (days > 7) {
                const weekPrice = (pricing && pricing['6'] != null) ? Number(pricing['6']) : pricePerDay * 7;
                if (weekPrice > 0) {
                    const extraDays = days - 7;
                    const costPerExtraDay = weekPrice / 7;
                    return weekPrice + (extraDays * costPerExtraDay);
                }
            }

            return pricePerDay * days;
        };

        // Polling Logic
        const _hash = (data) => JSON.stringify(data.map(b => `${b.id}-${b.status}`).sort());

        const startPolling = () => {
            if (_isPolling) return;
            _isPolling = true;

            // Initial fetch
            fetchBookings().then(() => {
                _lastHash = _hash(_bookings);
                notify();
            });

            _pollIntervalId = setInterval(async () => {
                const newBookings = await fetchBookings();
                const newHash = _hash(newBookings);
                if (newHash !== _lastHash) {
                    _lastHash = newHash;
                    notify();
                }
            }, POLL_INTERVAL);
        };

        const subscribe = (cb) => {
            _subscribers.push(cb);
            // If first subscriber, start polling
            if (_subscribers.length === 1) startPolling();
            // Immediate callback
            cb(_bookings);

            return () => {
                _subscribers = _subscribers.filter(s => s !== cb);
            };
        };

        const notify = () => {
            _subscribers.forEach(cb => cb(_bookings));
        };

        return {
            refresh: fetchBookings,
            checkAvailability,
            calculatePrice,
            subscribe,
            getBookings: () => _bookings
        };
    }

    window.useBookingAvailability = useBookingAvailability;

})(window);
