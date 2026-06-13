<?php
// Start session and verify admin authentication
require_once 'admin-auth.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserveringsbeheer - Nijenhuis Botenverhuur</title>
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <link rel="stylesheet" href="admin-consolidated.css">
    <link rel="icon" type="image/svg+xml" href="../frontend/Images/logo-white.svg">
    <style>
        /* Improved Booking Item Styles */
        .day-booking-item {
            display: grid;
            grid-template-columns: minmax(150px, 2fr) minmax(150px, 2fr) 100px 100px 100px auto; 
            gap: 15px;
            align-items: center;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            background: white;
            transition: all 0.3s ease;
            cursor: pointer;
            color: #000000 !important; /* Always black text */
        }
        
        .day-booking-item:hover {
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        /* Status Colors */
        /* Status Colors - STRICT MAPPING */
        /* Manual = Yellow */
        .day-booking-item.status-manual {
            background-color: #fff3cd !important; /* Yellow */
            border-color: #ffecb5 !important;
            color: #000000 !important;
        }
        .day-booking-item.status-manual:hover {
            background-color: #ffe69c !important;
        }

        /* Pay on arrival (pending, no online payment) = Yellow */
        .day-booking-item.status-pay-on-arrival {
            background-color: #fff3cd !important;
            border-color: #ffecb5 !important;
            color: #000000 !important;
        }
        .day-booking-item.status-pay-on-arrival:hover {
            background-color: #ffe69c !important;
        }

        /* Paid / Picked Up = Green */
        .day-booking-item.status-paid,
        .day-booking-item.status-confirmed-paid, /* Legacy */
        .day-booking-item.status-success { /* Legacy */
            background-color: #d4edda !important; /* Green */
            border-color: #c3e6cb !important;
            color: #000000 !important;
        }
        
        /* Picked Up specific */
        .day-booking-item.status-picked_up {
            background-color: #d4edda !important; /* Green */
            border-color: #c3e6cb !important;
            color: #000000 !important;
        }

        /* Canceled = Red */
        .day-booking-item.status-canceled,
        .day-booking-item.status-failed,
        .day-booking-item.status-rejected,
        .day-booking-item.status-pending, /* Treat pending as canceled/red per request */
        .day-booking-item.status-open {
            background-color: #f8d7da !important; /* Red */
            border-color: #f5c6cb !important;
            color: #000000 !important;
        }
        .day-booking-item.status-canceled:hover {
            background-color: #f5c6cb !important;
        }

        /* Text Styles */
        .booking-col {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #000000 !important;
        }
        .booking-name { font-weight: 600; color: #000000 !important; }
        .booking-boat { color: #000000 !important; }
        .booking-arrival-time { color: #000000 !important; font-size: 0.9em; }
        .booking-duration { color: #000000 !important; font-size: 0.9em; }
        .booking-price { color: #000000 !important; font-weight: 500; text-align: right; }
        
        .force-strikethrough { text-decoration: line-through; opacity: 1; color: #000000 !important; }

        /* Action Buttons */
        .btn-action-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .btn-confirm-payment {
            background: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85em;
            cursor: pointer;
            transition: background 0.2s;
            font-weight: 500;
        }
        .btn-confirm-payment:hover { background: #218838; }

        .expand-arrow {
            font-size: 0.8em;
            color: #666; /* Slightly lighter for arrow only */
            transition: transform 0.3s;
        }
        .day-booking-item.expanded .expand-arrow { transform: rotate(180deg); }

        /* Expanded Details */
        .booking-details-expand {
            grid-column: 1 / -1;
            padding-top: 15px;
            margin-top: 5px;
            border-top: 1px solid rgba(0,0,0,0.1);
            display: none;
            cursor: default;
            color: #000000 !important;
        }
        .day-booking-item.expanded .booking-details-expand { display: block; }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            font-size: 0.9em;
            margin-bottom: 10px;
            color: #000000 !important;
        }

        /* Status & Action Button Styles */
        /* Picked Up: Green background + Strikethrough on text, NOT buttons */
        .status-picked-up,
        .status-picked_up { 
            background-color: #d4edda !important; /* Same green as paid */
            border-color: #c3e6cb !important;
            color: #000000 !important;
            opacity: 0.9; 
        }
        
        .status-picked-up .booking-col,
        .status-picked_up .booking-col {
            text-decoration: line-through;
            opacity: 0.7;
        }

        .status-picked-up .btn-action-group,
        .status-picked-up .booking-details-expand,
        .status-picked_up .btn-action-group,
        .status-picked_up .booking-details-expand {
            text-decoration: none !important;
            opacity: 1 !important;
        }
        
        .btn-action { margin-right: 5px; padding: 4px 8px; font-size: 0.85em; border-radius: 4px; border: none; cursor: pointer; color: white !important; }
        .btn-edit { background-color: #17a2b8; }
        .btn-paid { background-color: #28a745; }
        .btn-picked-up { background-color: #6c757d; }
        .btn-revert { background-color: #ffc107; color: #000 !important; } /* Yellow/Orange for undo */
        .btn-delete { background-color: #dc3545; }

    </style>
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="admin-nav">
        <div class="admin-nav-container">
            <img src="../frontend/Images/logo-white.svg" alt="Nijenhuis" class="admin-nav-logo" style="height: 40px; width: auto;">
            <div class="admin-nav-links">
                <a href="admin-static.php" class="admin-nav-link">Dashboard</a>
                <a href="boat-management.php" class="admin-nav-link">Bootbeheer</a>
                <a href="booking-management.php" class="admin-nav-link active">Reserveringsbeheer</a>
                <a href="booking-history.php" class="admin-nav-link">Boekingsgeschiedenis</a>
                <a href="for-sale-management.php" class="admin-nav-link">Te koop</a>
            </div>
            <button class="admin-nav-logout" onclick="logout()">Uitloggen</button>
        </div>
    </nav>

    <div class="booking-management">
    <!-- Admin Header Section -->
    <div class="admin-header">
        <div class="admin-container">
            <h1 class="admin-title">Reserveringsbeheer</h1>
            <p class="admin-subtitle">Nijenhuis Botenverhuur - Overzicht</p>
        </div>
    </div>
        
        <div class="management-container">
            <!-- Add Booking Action Removed (Moved to Portal) -->

            <!-- Status Box Section -->
            <div class="status-box-container">
                <div class="status-box">
                    <div class="status-text">Momenteel geen nieuwe reserveringen..</div>
                    <div class="status-time" id="lastUpdateTime">Laatst geupdate: <span id="updateTime"></span></div>
                </div>
            </div>
            
            <!-- Three-Month Calendar -->
            <div class="calendar-months-container" id="calendarMonthsContainer">
                <!-- Calendars will be populated here -->
            </div>
            
            <!-- Day Bookings Section -->
            <div id="dayBookingsSection" class="day-bookings-section" style="display: none;">
                <div class="day-bookings-header">
                    <div class="day-bookings-header-main">
                        <button type="button" class="day-bookings-nav-btn" id="dayBookingsPrevDay" aria-label="Vorige dag">‹</button>
                        <h2 class="day-bookings-title" id="dayBookingsTitle">Reserveringen</h2>
                        <button type="button" class="day-bookings-nav-btn" id="dayBookingsNextDay" aria-label="Volgende dag">›</button>
                    </div>
                    <button type="button" class="close-day-bookings" onclick="closeDayBookings()" aria-label="Sluiten">&times;</button>
                </div>
                <div id="dayBookingsContent" class="day-bookings-content">
                    <!-- Bookings will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Day Bookings Modal -->
    <div id="dayBookingsModal" class="day-bookings-modal">
        <div class="day-bookings-modal-content">
            <div class="day-bookings-modal-header">
                <h2 class="day-bookings-modal-title" id="dayBookingsModalTitle">Reserveringen</h2>
                <button class="close-day-modal" onclick="closeDayBookingsModal()">&times;</button>
            </div>
            <div id="dayBookingsModalBody">
                <!-- Bookings will be populated here -->
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Booking Modal -->
    <div id="addBookingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalHeaderTitle">Handmatige reservering toevoegen</h3>
                <span class="close" onclick="closeAddBookingModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addBookingForm">
                    <input type="hidden" id="editBookingId" name="bookingId">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-md);">
                        <div class="form-group">
                            <label for="bookingDate">Startdatum</label>
                            <input type="date" id="bookingDate" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="bookingEndDate">Einddatum (optioneel)</label>
                            <input type="date" id="bookingEndDate" name="endDate">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bookingBoatType">Boottype</label>
                        <select id="bookingBoatType" name="boatType" required>
                            <option value="">Selecteer boottype</option>
                            <!-- Boat options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bookingCustomerName">Klantnaam</label>
                        <input type="text" id="bookingCustomerName" name="customerName" required>
                    </div>
                    <div class="form-group">
                        <label for="bookingCustomerEmail">E-mail</label>
                        <input type="email" id="bookingCustomerEmail" name="customerEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="bookingCustomerPhone">Telefoon</label>
                        <input type="tel" id="bookingCustomerPhone" name="customerPhone" required>
                    </div>
                    <div class="form-group">
                        <label for="bookingArrivalTime">Aankomsttijd *</label>
                        <select id="bookingArrivalTime" name="arrivalTime" required>
                            <option value="">-- Selecteer tijd --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bookingCityOfOrigin">Woonplaats *</label>
                        <input type="text" id="bookingCityOfOrigin" name="cityOfOrigin" required placeholder="Bijv. Amsterdam">
                    </div>
                    <div class="form-group">
                        <label for="bookingNotes">Notities</label>
                        <textarea id="bookingNotes" name="notes" rows="3"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" id="submitBookingBtn" class="btn btn-primary">Reservering opslaan</button>
                        <button type="button" class="btn btn-secondary" onclick="closeAddBookingModal()">Annuleren</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const adminSession = { csrfToken: '' };

        function detectAdminEndpoint() {
            if (window.location.protocol === 'file:' || window.location.hostname === '') {
                return 'http://localhost:8000/admin/booking-handler.py';
            }
            return `${window.location.origin}/admin/booking-handler.php`;
        }

        async function refreshAdminSession() {
            try {
                const endpoint = detectAdminEndpoint();
                const sessionUrl = endpoint.includes('.php') 
                    ? endpoint.replace('booking-handler.php', 'booking-handler.php?action=session')
                    : endpoint.replace('booking-handler.py', 'booking-handler.py?action=session');
                const response = await fetch(sessionUrl, {
                    method: 'GET',
                    credentials: 'include'
                });
                if (!response.ok) {
                    console.error('Session check failed: HTTP', response.status);
                    return false;
                }
                
                const responseText = await response.text();
                if (!responseText || responseText.trim() === '') {
                    console.error('Session check failed: Empty response');
                    return false;
                }
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Session check failed: Invalid JSON', responseText);
                    return false;
                }
                
                if (result.success && result.authenticated) {
                    adminSession.csrfToken = result.csrfToken || '';
                    return true;
                } else {
                    console.error('Session check failed: Not authenticated', result);
                    return false;
                }
            } catch (error) {
                console.error('Session validation error:', error);
            }
            return false;
        }

        async function getCsrfToken() {
            if (!adminSession.csrfToken) {
                await refreshAdminSession();
            }
            return adminSession.csrfToken || '';
        }

        class BookingManagementSystem {
            constructor() {
                // Use an admin-only cache key so it doesn't get overwritten by public pages
                this.bookingsStorageKey = 'nijenhuis_admin_bookings';
                this.bookings = [];
                this.currentMonthOffset = 0; // Track month navigation offset
                this.init();
            }
            
            async checkAuth() {
                const isAuthenticated = await refreshAdminSession();
                if (!isAuthenticated) {
                    window.location.href = '../pages/admin-login.php';
                    return false;
                }
                return true;
            }
            
            async init() {
                console.log('Initializing BookingManagementSystem...');
                
                // Check authentication first
                const isAuthenticated = await this.checkAuth();
                if (!isAuthenticated) return;
                
                // Setup event listeners first (synchronous)
                this.setupEventListeners();
                this.setBookingSeasonDates();
                
                // Render calendar immediately (don't wait for bookings)
                const calendarContainer = document.getElementById('calendarMonthsContainer');
                if (calendarContainer) {
                    console.log('Calendar container found, rendering calendar...');
                    calendarContainer.style.display = 'grid';
                    calendarContainer.style.visibility = 'visible';
                    calendarContainer.style.opacity = '1';
                    
                    // Render calendar structure immediately
                    this.renderThreeMonthCalendar();
                    this.updateStatusTime();
                } else {
                    console.error('Calendar container not found!');
                }
                
                // Load bookings asynchronously (non-blocking)
                try {
                    await this.loadBookings();
                    console.log('Bookings loaded:', this.bookings.length);
                } catch (e) {
                    console.warn('Could not load bookings from server, using localStorage:', e.message);
                    // Fallback to localStorage is handled in loadBookings()
                }
                
                // Render bookings on calendar after loading
                // Render bookings on calendar after loading
                setTimeout(() => {
                    this.renderBookingsOnCalendar();
                    
                    // Open bookings for current Season Date by default
                    // Logic: If today < April 1st, show April 1st.
                    // If today > Oct 31st, show April 1st next year? (Or handled by season logic)
                    const today = new Date();
                    let initialDate = today;
                    const seasonStartMonth = 3; // April
                    const seasonEndMonth = 9; // Oct
                    
                    if (today.getMonth() < seasonStartMonth) {
                        initialDate = new Date(today.getFullYear(), seasonStartMonth, 1);
                    } else if (today.getMonth() > seasonEndMonth) {
                         initialDate = new Date(today.getFullYear() + 1, seasonStartMonth, 1);
                    }
                    
                    this.showDayBookings(initialDate);
                }, 100);
                
                // Populate boat options in modal from localStorage
                this.populateBoatOptions();
                
                // Set up auto-refresh for bookings every 30 seconds
                setInterval(async () => {
                    try {
                        await this.loadBookings();
                        this.renderThreeMonthCalendar();
                        setTimeout(() => {
                            this.renderBookingsOnCalendar();
                        }, 100);
                        this.updateStatusBox();
                    } catch (e) {
                        console.warn('Auto-refresh failed:', e.message);
                    }
                }, 30000);
            }
            
            formatDateLocal(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
            
            formatDateEuropean(dateString) {
                if (!dateString) return '';
                try {
                    const date = new Date(dateString);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${day}-${month}-${year}`;
                } catch (e) {
                    return dateString;
                }
            }
            
            getBoatById(boatType) {
                try {
                    const storedBoats = localStorage.getItem('nijenhuis_boats');
                    if (storedBoats) {
                        const boats = JSON.parse(storedBoats);
                        return boats.find(b => b.id === boatType) || null;
                    }
                } catch (e) {
                    console.error('Error loading boat:', e);
                }
                return null;
            }
            
            /** Recalculate rent from local fleet + dates only (may be 0 if data missing or invalid). */
            calculateBookingPriceRecalculated(booking) {
                const boat = this.getBoatById(booking.boatType);
                if (!boat) {
                    return 0;
                }
                
                // Calculate number of days
                let numberOfDays = 1;
                if (booking.endDate && booking.endDate !== booking.date) {
                    const start = new Date(booking.date);
                    const end = new Date(booking.endDate);
                    numberOfDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;
                }
                
                // Select pricing array: use pricingWithEngine if motor option was selected (only boats that offer it)
                const useMotor = (booking.engineOption === 'with') && boat.pricingWithEngine && Array.isArray(boat.pricingWithEngine) && boat.pricingWithEngine.length > 0;
                let pricing = boat.pricing;
                let pricePerDay = Number(boat.pricePerDay || 0);
                
                if (useMotor) {
                    pricing = boat.pricingWithEngine;
                    if (pricing[0] > 0) pricePerDay = Number(pricing[0]);
                }
                
                if (numberOfDays === 1) {
                    return pricePerDay;
                } else if (numberOfDays >= 2 && numberOfDays <= 7) {
                    // Use tiered pricing if available
                    if (pricing && Array.isArray(pricing) && pricing[numberOfDays - 1] !== undefined) {
                        return Number(pricing[numberOfDays - 1]);
                    }
                    // Fallback to daily rate
                    return pricePerDay * numberOfDays;
                } else if (numberOfDays > 7) {
                    // Weekly price + extra days
                    let weeklyPrice = 0;
                    if (pricing && Array.isArray(pricing) && pricing[6] !== undefined) {
                        weeklyPrice = Number(pricing[6]);
                    } else {
                        weeklyPrice = pricePerDay * 7;
                    }
                    
                    if (weeklyPrice > 0) {
                        const extraDays = numberOfDays - 7;
                        const costPerExtraDay = weeklyPrice / 7;
                        return weeklyPrice + (extraDays * costPerExtraDay);
                    }
                }
                
                return 0;
            }

            /** Display price: prefer recalculation when valid; otherwise use stored booking amount (fixes admin €0 when local boats are stale). */
            calculateBookingPrice(booking) {
                const stored = Number(booking.amount ?? 0);
                const rec = this.calculateBookingPriceRecalculated(booking);
                if (Number.isFinite(rec) && rec > 0) return rec;
                if (stored > 0) return stored;
                return Number.isFinite(rec) ? rec : 0;
            }

            getBoatDisplayName(boatType) {
                // Try to get from localStorage boats first
                try {
                    const storedBoats = localStorage.getItem('nijenhuis_boats');
                    if (storedBoats) {
                        const boats = JSON.parse(storedBoats);
                        const boat = boats.find(b => b.id === boatType);
                        if (boat && boat.name) {
                            return boat.name;
                        }
                    }
                } catch (e) {
                    console.error('Error loading boat name:', e);
                }
                
                // Fallback to default names
                const boatNames = {
                    'classic-tender-720': 'Classic tender 720 10/12 pers',
                    'electrosloop-10': 'Electrosloep 10 pers',
                    'classic-tender-570': 'Classic tender 570 8 pers',
                    'electrosloop-8': 'Electrosloep 8 pers',
                    'sailboat-4-5': 'Zeilboot 4/5 pers',
                    'sailpunter-3-4': 'Zeilpunter 3/4 pers',
                    'electroboat-5': 'Electroboot 5 pers',
                    'canoe-3': 'Canadese kano 3 pers',
                    'kayak-2': 'Kajak 2 pers',
                    'kayak-1': 'Kajak 1 pers',
                    'sup-board': 'SUP board 1 pers'
                };
                
                return boatNames[boatType] || boatType;
            }
            
            populateBoatOptions() {
                const select = document.getElementById('bookingBoatType');
                if (!select) return;
                
                // Clear existing options except the first placeholder
                while (select.options.length > 1) {
                    select.remove(1);
                }
                
                // Try to get boats from localStorage
                try {
                    const storedBoats = localStorage.getItem('nijenhuis_boats');
                    if (storedBoats) {
                        const boats = JSON.parse(storedBoats);
                        
                        // Sort by category and orderId
                        const categoryOrder = ['electric', 'sailing', 'canoe', 'sup'];
                        const sortedBoats = [...boats].sort((a, b) => {
                            const catA = categoryOrder.indexOf(a.category || 'other');
                            const catB = categoryOrder.indexOf(b.category || 'other');
                            if (catA !== catB) return catA - catB;
                            return (a.orderId || 999) - (b.orderId || 999);
                        });
                        
                        sortedBoats.forEach(boat => {
                            const option = document.createElement('option');
                            option.value = boat.id;
                            option.textContent = boat.name;
                            select.appendChild(option);
                        });
                        
                        console.log(`Populated ${sortedBoats.length} boat options from localStorage`);
                        return;
                    }
                } catch (e) {
                    console.error('Error loading boats from localStorage:', e);
                }
                
                // Fallback to default options if localStorage is empty
                const defaultBoats = [
                    { id: 'classic-tender-720', name: 'Classic tender 720 10/12 pers' },
                    { id: 'classic-tender-570', name: 'Classic Tender 570 8 pers' },
                    { id: 'electrosloop-10', name: 'Electrosloep 10 pers' },
                    { id: 'electrosloop-8', name: 'Electrosloep 8 pers' },
                    { id: 'electroboat-5', name: 'Electrosloep 5 pers' },
                    { id: 'sailboat-4-5', name: 'Zeilboot 4/5 pers' },
                    { id: 'sailpunter-3-4', name: 'Zeilpunter 3/4 pers' },
                    { id: 'canoe-3', name: 'Canadese kano 3 pers' },
                    { id: 'kayak-2', name: 'Kajak 2 pers' },
                    { id: 'kayak-1', name: 'Kajak 1 pers' },
                    { id: 'sup-board', name: 'SUP board 1 pers' }
                ];
                
                defaultBoats.forEach(boat => {
                    const option = document.createElement('option');
                    option.value = boat.id;
                    option.textContent = boat.name;
                    select.appendChild(option);
                });
                
                console.log('Populated boat options from defaults');
            }
            
            updateStatusTime() {
                const updateTimeElement = document.getElementById('updateTime');
                if (updateTimeElement) {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    updateTimeElement.textContent = `${hours}:${minutes}:${seconds}`;
                    
                    // Update every second
                    setInterval(() => {
                        const now = new Date();
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        const seconds = String(now.getSeconds()).padStart(2, '0');
                        updateTimeElement.textContent = `${hours}:${minutes}:${seconds}`;
                    }, 1000);
                }
            }
            
            renderThreeMonthCalendar() {
                const container = document.getElementById('calendarMonthsContainer');
                if (!container) {
                    console.error('Calendar container not found!');
                    return;
                }
                
                // Ensure container is visible
                container.style.display = 'grid';
                container.style.visibility = 'visible';
                container.style.opacity = '1';
                
                container.innerHTML = '';
                
                // Season: April (3) to October (9)
                const SEASON_START_MONTH = 3; // April (0-indexed)
                const SEASON_END_MONTH = 9;   // October (0-indexed)
                
                const today = new Date();
                let year = today.getFullYear();
                
                // If we're past Oct, show next year's season
                if (today.getMonth() > SEASON_END_MONTH) {
                    year = today.getFullYear() + 1;
                }
                
                // Calculate starting month based on offset, but clamp to season
                let startMonthIndex = SEASON_START_MONTH + this.currentMonthOffset;
                
                // Clamp: can't go before April
                if (startMonthIndex < SEASON_START_MONTH) {
                    startMonthIndex = SEASON_START_MONTH;
                    this.currentMonthOffset = 0;
                }
                
                // Clamp: can't show beyond October (need at least 3 months visible, so max start is Aug)
                const maxStartMonth = SEASON_END_MONTH - 2; // August (showing Aug, Sep, Oct)
                if (startMonthIndex > maxStartMonth) {
                    startMonthIndex = maxStartMonth;
                    this.currentMonthOffset = maxStartMonth - SEASON_START_MONTH;
                }
                
                const months = [
                    new Date(year, startMonthIndex, 1),
                    new Date(year, startMonthIndex + 1, 1),
                    new Date(year, startMonthIndex + 2, 1)
                ];
                
                // Determine if we can navigate
                const canGoPrev = startMonthIndex > SEASON_START_MONTH;
                const canGoNext = startMonthIndex < maxStartMonth;
                
                console.log('Rendering calendar for months:', months.map(m => `${m.getMonth() + 1}/${m.getFullYear()}`));
                
                months.forEach((monthDate, index) => {
                    const monthCalendar = this.createMonthCalendar(monthDate, index === 0, index === months.length - 1, canGoPrev, canGoNext);
                    if (monthCalendar) {
                        container.appendChild(monthCalendar);
                    }
                });
                
                console.log('Calendar rendered. Container children:', container.children.length);
            }
            
            createMonthCalendar(monthDate, isFirst, isLast, canGoPrev = true, canGoNext = true) {
                const monthDiv = document.createElement('div');
                monthDiv.className = 'calendar-month';
                
                // Month header
                const headerDiv = document.createElement('div');
                headerDiv.className = 'calendar-month-header';
                
                // Left arrow (previous months) - only show on first month and if can go prev
                if (isFirst && canGoPrev) {
                    const prevBtn = document.createElement('button');
                    prevBtn.className = 'calendar-nav-arrow';
                    prevBtn.textContent = '‹';
                    prevBtn.onclick = () => {
                        this.currentMonthOffset -= 1;
                        this.renderThreeMonthCalendar();
                        setTimeout(() => {
                            this.renderBookingsOnCalendar();
                        }, 100);
                    };
                    headerDiv.appendChild(prevBtn);
                } else {
                    headerDiv.appendChild(document.createElement('div')); // Spacer
                }
                
                const monthNames = ['januari', 'februari', 'maart', 'april', 'mei', 'juni',
                    'juli', 'augustus', 'september', 'oktober', 'november', 'december'];
                const monthTitle = document.createElement('div');
                monthTitle.className = 'calendar-month-title';
                monthTitle.textContent = `${monthNames[monthDate.getMonth()]} ${monthDate.getFullYear()}`;
                headerDiv.appendChild(monthTitle);
                
                // Right arrow (next months) - only show on last month and if can go next
                if (isLast && canGoNext) {
                    const nextBtn = document.createElement('button');
                    nextBtn.className = 'calendar-nav-arrow';
                    nextBtn.textContent = '›';
                    nextBtn.onclick = () => {
                        this.currentMonthOffset += 1;
                        this.renderThreeMonthCalendar();
                        setTimeout(() => {
                            this.renderBookingsOnCalendar();
                        }, 100);
                    };
                    headerDiv.appendChild(nextBtn);
                } else {
                    headerDiv.appendChild(document.createElement('div')); // Spacer
                }
                
                // Calendar grid
                const gridDiv = document.createElement('div');
                gridDiv.className = 'calendar-month-grid';
                
                // Day headers
                const dayNames = ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'];
                dayNames.forEach(day => {
                    const dayHeader = document.createElement('div');
                    dayHeader.className = 'calendar-day-header';
                    dayHeader.textContent = day;
                    gridDiv.appendChild(dayHeader);
                });
                
                // Get first day of month and adjust for Monday = 0
                const firstDay = new Date(monthDate.getFullYear(), monthDate.getMonth(), 1);
                const lastDay = new Date(monthDate.getFullYear(), monthDate.getMonth() + 1, 0);
                const startDate = new Date(firstDay);
                
                // Adjust to Monday (Dutch calendar starts with Monday)
                const dayOfWeek = firstDay.getDay();
                const daysToSubtract = dayOfWeek === 0 ? 6 : dayOfWeek - 1; // If Sunday, go back 6 days
                startDate.setDate(startDate.getDate() - daysToSubtract);
                
                // Generate 42 days (6 weeks)
                for (let i = 0; i < 42; i++) {
                    const currentDate = new Date(startDate);
                    currentDate.setDate(startDate.getDate() + i);
                    
                    const dayCell = document.createElement('div');
                    dayCell.className = 'calendar-day-cell';
                    
                    // Check if day is from other month
                    const isOtherMonth = currentDate.getMonth() !== monthDate.getMonth();
                    if (isOtherMonth) {
                        dayCell.classList.add('other-month');
                    }
                    
                    dayCell.textContent = currentDate.getDate();
                    
                    // Add click handler for days (only if not other month)
                    if (!isOtherMonth) {
                        dayCell.onclick = () => {
                            this.showDayBookings(currentDate);
                        };
                    }
                    
                    gridDiv.appendChild(dayCell);
                }
                
                monthDiv.appendChild(headerDiv);
                monthDiv.appendChild(gridDiv);
                
                return monthDiv;
            }
            
            navigateCalendar(direction) {
                this.currentMonthOffset += direction;
                this.renderThreeMonthCalendar();
                setTimeout(() => {
                    this.renderBookingsOnCalendar();
                }, 100);
            }

            getSeasonCalendarYear() {
                const today = new Date();
                const SEASON_END_MONTH = 9;
                let year = today.getFullYear();
                if (today.getMonth() > SEASON_END_MONTH) {
                    year = today.getFullYear() + 1;
                }
                return year;
            }

            ensureCalendarShowsDate(date) {
                const SEASON_START = 3;
                const SEASON_END = 9;
                const maxStart = SEASON_END - 2;
                const calYear = this.getSeasonCalendarYear();
                if (date.getFullYear() !== calYear) return;

                const m = date.getMonth();
                if (m < SEASON_START || m > SEASON_END) return;

                const startMonth = SEASON_START + this.currentMonthOffset;
                if (m >= startMonth && m <= startMonth + 2) return;

                let newStart = Math.min(Math.max(m - 1, SEASON_START), maxStart);
                if (m === SEASON_START) newStart = SEASON_START;
                if (m === SEASON_END) newStart = maxStart;
                this.currentMonthOffset = newStart - SEASON_START;
                this.renderThreeMonthCalendar();
            }

            /** Unique calendar days (noon) that have at least one booking, same rules as the calendar indicators. */
            getSortedReservationDates() {
                const byKey = new Map();
                for (const booking of this.bookings) {
                    if (!booking.date) continue;
                    try {
                        const startDate = new Date(booking.date);
                        startDate.setHours(12, 0, 0, 0);
                        const endDate = booking.endDate ? new Date(booking.endDate) : new Date(booking.date);
                        endDate.setHours(12, 0, 0, 0);
                        let cur = new Date(startDate);
                        while (cur.getTime() <= endDate.getTime()) {
                            const key = `${cur.getFullYear()}-${String(cur.getMonth() + 1).padStart(2, '0')}-${String(cur.getDate()).padStart(2, '0')}`;
                            if (!byKey.has(key)) {
                                byKey.set(key, new Date(cur));
                            }
                            cur = new Date(cur);
                            cur.setDate(cur.getDate() + 1);
                            cur.setHours(12, 0, 0, 0);
                        }
                    } catch (e) { /* skip */ }
                }
                return Array.from(byKey.values()).sort((a, b) => a.getTime() - b.getTime());
            }

            findNextReservationDay(fromDate) {
                const sorted = this.getSortedReservationDates();
                const from = new Date(fromDate);
                from.setHours(12, 0, 0, 0);
                const t = from.getTime();
                for (const d of sorted) {
                    if (d.getTime() > t) return d;
                }
                return null;
            }

            findPrevReservationDay(fromDate) {
                const sorted = this.getSortedReservationDates();
                const from = new Date(fromDate);
                from.setHours(12, 0, 0, 0);
                const t = from.getTime();
                for (let i = sorted.length - 1; i >= 0; i--) {
                    if (sorted[i].getTime() < t) return sorted[i];
                }
                return null;
            }

            updateDayBookingsNavButtons(forDate) {
                const dayPrev = document.getElementById('dayBookingsPrevDay');
                const dayNext = document.getElementById('dayBookingsNextDay');
                const d = new Date(forDate);
                d.setHours(12, 0, 0, 0);
                if (dayPrev) {
                    dayPrev.disabled = !this.findPrevReservationDay(d);
                }
                if (dayNext) {
                    dayNext.disabled = !this.findNextReservationDay(d);
                }
            }

            navigateDayBookings(delta) {
                const base = window.currentViewingDate ? new Date(window.currentViewingDate) : new Date();
                base.setHours(12, 0, 0, 0);
                const target = delta > 0 ? this.findNextReservationDay(base) : this.findPrevReservationDay(base);
                if (!target) return;
                this.ensureCalendarShowsDate(target);
                this.showDayBookings(target);
                setTimeout(() => {
                    this.renderBookingsOnCalendar();
                }, 100);
            }
            
            showDayBookings(date) {
                window.currentViewingDate = date;
                const section = document.getElementById('dayBookingsSection');
                const sectionTitle = document.getElementById('dayBookingsTitle');
                const sectionContent = document.getElementById('dayBookingsContent');
                
                if (!section || !sectionTitle || !sectionContent) return;
                
                const monthNames = ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'];
                const formattedDate = `${date.getDate()} ${monthNames[date.getMonth()]} ${date.getFullYear()}`;
                sectionTitle.textContent = `Reserveringen voor ${formattedDate}`;
                
                const checkDate = new Date(date);
                checkDate.setHours(12, 0, 0, 0);

                const rawDayBookings = this.bookings.filter(booking => {
                    if (!booking.date) return false;
                    try {
                        const startDate = new Date(booking.date);
                        startDate.setHours(12, 0, 0, 0);
                        const endDate = booking.endDate ? new Date(booking.endDate) : new Date(booking.date);
                        endDate.setHours(12, 0, 0, 0);
                        return checkDate >= startDate && checkDate <= endDate;
                    } catch (e) { return false; }
                });
                
                // One entry per boat: expand any booking with quantity > 1 into separate rows (legacy data).
                // New bookings are already one record per boat (quantity=1) from backend.
                const dayBookings = [];
                rawDayBookings.forEach(booking => {
                    const qty = parseInt(booking.quantity) || 1;
                    if (qty <= 1) {
                        dayBookings.push(booking);
                    } else {
                        for (let i = 0; i < qty; i++) {
                            const entry = Object.assign({}, booking);
                            entry._expandedIndex = i + 1;
                            entry._expandedTotal = qty;
                            // Amount per boat for display
                            entry.amount = (booking.amount || 0) / qty;
                            dayBookings.push(entry);
                        }
                    }
                });
                
                if (dayBookings.length > 0) {
                    // Sort: Picked up items go to bottom, then by arrival time (early to late)
                    dayBookings.sort((a, b) => {
                        const aPickedUp = a.status === 'picked_up';
                        const bPickedUp = b.status === 'picked_up';
                        if (aPickedUp && !bPickedUp) return 1;
                        if (!aPickedUp && bPickedUp) return -1;
                        
                        // Sort by arrival time (early to late)
                        const aTime = a.arrivalTime || a.arrival_time || '99:99'; // Put missing times at end
                        const bTime = b.arrivalTime || b.arrival_time || '99:99';
                        
                        // Compare times (HH:MM format)
                        if (aTime < bTime) return -1;
                        if (aTime > bTime) return 1;
                        return 0;
                    });
                }
                
                sectionContent.innerHTML = '';
                
                if (dayBookings.length === 0) {
                    sectionContent.innerHTML = `<p style="text-align: center; color: var(--text-secondary); padding: var(--spacing-xl);">Geen reserveringen voor deze datum.</p>`;
                } else {
                    dayBookings.forEach(booking => {
                        const bookingItem = document.createElement('div');
                        
                        bookingItem.dataset.id = booking.id;
                        let durationStr = '1 dag';
                        if (booking.endDate && booking.endDate !== booking.date) {
                            const start = new Date(booking.date);
                            const end = new Date(booking.endDate);
                            const diffDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;
                            durationStr = `${diffDays} dagen`;
                        }

                        const isPayOnArrivalBooking = (booking.paymentMethod || '') === 'pay_on_arrival';
                        const incompleteOnlineStatuses = ['open', 'pending', 'not-confirmed'];

                        const isPickedUp = booking.status === 'picked_up';
                        const isPaid = (booking.status === 'paid' || booking.status === 'confirmed-paid' || booking.status === 'success' || isPickedUp);
                        
                        // Treat pending/open as canceled (Red) until paid online — except pay-on-arrival
                        const isCanceled = ['canceled', 'cancelled', 'payment-rejected', 'failed', 'expired', 'rejected'].includes(booking.status)
                            || (incompleteOnlineStatuses.includes(booking.status) && !isPayOnArrivalBooking);
                        
                        // Clean status for display
                        let displayStatus = booking.status;
                        
                        // Determine class
                        let statusClass = 'status-manual'; // Default fallback
                        
                        if (booking.status === 'manual' && booking.source === 'receptie') {
                            displayStatus = 'Receptie';
                            statusClass = 'status-manual';
                        } else if (booking.status === 'manual') {
                            displayStatus = 'Handmatig';
                            statusClass = 'status-manual';
                        } else if (isPayOnArrivalBooking && (incompleteOnlineStatuses.includes(booking.status) || booking.status === 'confirmed')) {
                            displayStatus = 'Betalen bij aankomst';
                            statusClass = 'status-pay-on-arrival';
                        } else if (booking.status === 'paid' || booking.status === 'confirmed-paid' || booking.status === 'success') {
                            displayStatus = 'Betaald';
                            statusClass = 'status-paid';
                        } else if (booking.status === 'picked_up') {
                            displayStatus = 'Afgehaald';
                            statusClass = 'status-picked_up';
                        } else if (['canceled', 'cancelled', 'payment-rejected', 'failed', 'expired', 'rejected'].includes(booking.status)) {
                            displayStatus = 'Geannuleerd';
                            statusClass = 'status-canceled';
                        } else if (['pending', 'open', 'not-confirmed'].includes(booking.status)) {
                            // Online payments that didn't complete = Canceled/Red
                            displayStatus = 'Geannuleerd'; 
                            statusClass = 'status-canceled';
                        }

                        bookingItem.className = `day-booking-item ${statusClass}`;
                        
                        // Calculate booking price
                        const bookingPrice = this.calculateBookingPrice(booking);
                        const priceDisplay = `€${bookingPrice.toFixed(2)}`;
                        
                        // Get customer name - try different possible field names
                        const customerName = booking.customerName || booking.customer_name || booking.name || 'Onbekend';
                        
                        // Format arrival time for display (handle both HH:MM and HH:MM:SS formats)
                        const arrivalTime = booking.arrivalTime || booking.arrival_time || '-';
                        const formattedArrivalTime = arrivalTime !== '-' && arrivalTime.includes(':') 
                            ? arrivalTime.substring(0, 5) // Take only HH:MM part
                            : arrivalTime;

                        // Main content structure
                        // Show boat number indicator for expanded legacy quantity entries
                        const boatRow = this.getBoatById(booking.boatType);
                        const engineLabel = (booking.engineOption === 'with' && boatRow && boatRow.pricingWithEngine && Array.isArray(boatRow.pricingWithEngine) && boatRow.pricingWithEngine.length > 0) ? ' + motor' : '';
                        const boatDisplayName = this.getBoatDisplayName(booking.boatType) + engineLabel +
                            (booking._expandedTotal ? ` (${booking._expandedIndex}/${booking._expandedTotal})` : '');
                        bookingItem.innerHTML = `
                            <div class="booking-col booking-name">${this.escapeHTML(customerName)}</div>
                            <div class="booking-col booking-boat">${this.escapeHTML(boatDisplayName)}</div>
                            <div class="booking-col booking-arrival-time">${this.escapeHTML(formattedArrivalTime)}</div>
                            <div class="booking-col booking-duration">${durationStr}</div>
                            <div class="booking-col booking-price">${priceDisplay}</div>
                            
                            <div class="btn-action-group">
                                <strong>${displayStatus}</strong>
                                <span class="expand-arrow">▼</span>
                            </div>
                        `;

                        // Details section
                        const detailsExpand = document.createElement('div');
                        detailsExpand.className = 'booking-details-expand';
                        
                        // Format dates in European format
                        const formattedStartDate = this.formatDateEuropean(booking.date);
                        const formattedEndDate = booking.endDate ? this.formatDateEuropean(booking.endDate) : null;
                        const dateDisplay = formattedEndDate ? `${formattedStartDate} t/m ${formattedEndDate}` : formattedStartDate;
                        
                        const detailsGrid = document.createElement('div');
                        detailsGrid.className = 'details-grid';
                        const cityOfOrigin = booking.cityOfOrigin || booking.city_of_origin || '-';
                        const paymentMethodLabel = this.formatPaymentMethodLabel(booking.paymentMethod);
                        const resFee = booking.reservationFee != null ? `€${Number(booking.reservationFee).toFixed(2)}` : '';
                        const balDue = booking.balanceDueOnArrival != null ? `€${Number(booking.balanceDueOnArrival).toFixed(2)}` : '';
                        const resRent = booking.reservationFeeRentalPortion != null ? `€${Number(booking.reservationFeeRentalPortion).toFixed(2)}` : '';
                        const resAdmSlice = booking.reservationFeeAdminOnReservation != null ? `€${Number(booking.reservationFeeAdminOnReservation).toFixed(2)}` : '';
                        let poaMoneyRows = '';
                        if (resFee && balDue) {
                            poaMoneyRows = resRent && resAdmSlice
                                ? `<div><strong>Reservering — huurdeel:</strong> ${resRent}</div><div><strong>Administratie op reserveringsdeel:</strong> ${resAdmSlice}</div><div><strong>Reserveringsbijdrage totaal (niet-restitueerbaar):</strong> ${resFee}</div><div><strong>Resterend bij aankomst:</strong> ${balDue}</div>`
                                : `<div><strong>Reserveringsbijdrage (niet-restitueerbaar):</strong> ${resFee}</div><div><strong>Resterend bij aankomst:</strong> ${balDue}</div>`;
                        }
                        detailsGrid.innerHTML = `
                            <div><strong>Datum:</strong> ${dateDisplay}</div>
                            <div><strong>Prijs:</strong> ${priceDisplay}</div>
                            <div><strong>Betaalmethode:</strong> ${this.escapeHTML(paymentMethodLabel)}</div>
                            ${poaMoneyRows}
                            <div><strong>Email:</strong> ${this.escapeHTML(booking.customerEmail || booking.customer_email || '-')}</div>
                            <div><strong>Telefoon:</strong> ${this.escapeHTML(booking.customerPhone || booking.customer_phone || '-')}</div>
                            <div><strong>Woonplaats:</strong> ${this.escapeHTML(cityOfOrigin)}</div>
                            <div style="grid-column: 1 / -1;"><strong>Notities:</strong> ${this.escapeHTML(booking.notes || 'Geen notities')}</div>
                        `;
                        
                        const actionsDiv = document.createElement('div');
                        actionsDiv.style.display = 'flex';
                        actionsDiv.style.gap = '10px';
                        actionsDiv.style.justifyContent = 'flex-end'; // Fixed typo jautify-content
                        actionsDiv.style.marginTop = '10px';
                        
                        // For canceled bookings: edit (change/copy details) + delete. Edit does NOT create new bookings, status stays canceled.
                        if (isCanceled) {
                            const editBtn = document.createElement('button');
                            editBtn.className = 'btn-action btn-edit';
                            editBtn.textContent = 'Bewerken';
                            editBtn.onclick = (e) => {
                                e.stopPropagation();
                                this.showEditBookingModal(booking.id);
                            };
                            actionsDiv.appendChild(editBtn);
                            const deleteBtn = document.createElement('button');
                            deleteBtn.className = 'btn-action btn-delete';
                            deleteBtn.textContent = 'Verwijderen';
                            deleteBtn.onclick = (e) => {
                                e.stopPropagation();
                                this.deleteBooking(booking.id);
                            };
                            actionsDiv.appendChild(deleteBtn);
                        } else {
                            // Normal booking buttons
                            const editBtn = document.createElement('button');
                            editBtn.className = 'btn-action btn-edit';
                            editBtn.textContent = 'Bewerken';
                            editBtn.onclick = (e) => {
                                e.stopPropagation();
                                this.showEditBookingModal(booking.id);
                            };
                            actionsDiv.appendChild(editBtn);
                            
                            if (!isPaid) {
                                const paidBtn = document.createElement('button');
                                paidBtn.className = 'btn-action btn-paid';
                                paidBtn.textContent = 'Betaald';
                                paidBtn.onclick = (e) => {
                                    e.stopPropagation();
                                    this.markAsPaid(booking.id);
                                };
                                actionsDiv.appendChild(paidBtn);
                            } else if (isPaid && !isPickedUp) {
                                 // Allow undoing payment
                                const undoPaidBtn = document.createElement('button');
                                undoPaidBtn.className = 'btn-action btn-revert';
                                undoPaidBtn.textContent = 'Niet Betaald';
                                undoPaidBtn.onclick = (e) => {
                                    e.stopPropagation();
                                    this.markAsUnpaid(booking.id);
                                };
                                actionsDiv.appendChild(undoPaidBtn);
                            }
                            
                            if (!isPickedUp) {
                                const pickupBtn = document.createElement('button');
                                pickupBtn.className = 'btn-action btn-picked-up';
                                pickupBtn.textContent = 'Afgehaald';
                                pickupBtn.onclick = (e) => {
                                    e.stopPropagation();
                                    this.markAsPickedUp(booking.id);
                                };
                                actionsDiv.appendChild(pickupBtn);
                            } else if (isPickedUp) {
                                 // Allow undoing pickup -> Back to PAID
                                const undoPickupBtn = document.createElement('button');
                                undoPickupBtn.className = 'btn-action btn-revert';
                                undoPickupBtn.textContent = 'Terug naar Betaald';
                                undoPickupBtn.style.marginRight = '5px';
                                undoPickupBtn.onclick = (e) => {
                                    e.stopPropagation();
                                    this.markAsNotPickedUp(booking.id);
                                };
                                actionsDiv.appendChild(undoPickupBtn);

                                 // Allow undoing pickup -> Back to CANCELED/MANUAL (Deep Revert)
                                const isOnline = !!booking.paymentId;
                                const deepRevertBtn = document.createElement('button');
                                deepRevertBtn.className = 'btn-action btn-revert';
                                deepRevertBtn.textContent = isOnline ? 'Terug naar Geannuleerd' : 'Terug naar Handmatig';
                                deepRevertBtn.onclick = (e) => {
                                    e.stopPropagation();
                                    if(isOnline) this.markAsUnpaid(booking.id); // Reverts to canceled
                                    else this.markAsManual(booking.id); // Reverts to manual
                                };
                                actionsDiv.appendChild(deepRevertBtn);
                            }
                            
                            const deleteBtn = document.createElement('button');
                            deleteBtn.className = 'btn-action btn-delete';
                            deleteBtn.textContent = 'Verwijderen';
                            deleteBtn.onclick = (e) => {
                                e.stopPropagation();
                                this.deleteBooking(booking.id);
                            };
                            actionsDiv.appendChild(deleteBtn);
                        }
                        
                        detailsExpand.appendChild(detailsGrid);
                        detailsExpand.appendChild(actionsDiv);
                        
                        bookingItem.appendChild(detailsExpand);
                        
                        // Toggle Expand
                        bookingItem.addEventListener('click', (e) => {
                            // Ignore clicks on buttons/interactive elements to avoid conflict
                            if (e.target.tagName === 'BUTTON' || e.target.closest('button')) return;
                            bookingItem.classList.toggle('expanded');
                        });
                        
                        sectionContent.appendChild(bookingItem);
                    });
                }
                
                section.style.display = 'block';
                this.updateDayBookingsNavButtons(date);
            }



            async markAsPaid(bookingId) {
                if(!confirm('Markeer deze reservering als betaald?')) return;
                await this.updateBookingStatus(bookingId, 'paid');
            }

            async markAsUnpaid(bookingId) {
                if(!confirm('Markeer deze reservering als NIET betaald? (Status wordt teruggezet naar Geannuleerd)')) return;
                await this.updateBookingStatus(bookingId, 'canceled');
            }

            async markAsManual(bookingId) {
                if(!confirm('Markeer deze reservering als Handmatig? (Status wordt teruggezet naar Handmatig)')) return;
                await this.updateBookingStatus(bookingId, 'manual');
            }

            async markAsPickedUp(bookingId) {
                if(!confirm('Markeer deze boot als afgehaald?')) return;
                await this.updateBookingStatus(bookingId, 'picked_up');
            }

            async markAsNotPickedUp(bookingId) {
                if(!confirm('Markeer deze boot als NIET afgehaald? (Status wordt teruggezet naar Betaald)')) return;
                await this.updateBookingStatus(bookingId, 'paid');
            }

            async updateBookingStatus(bookingId, status) {
                try {
                    const endpoint = this.detectServerEndpoint();
                    const bookingIndex = this.bookings.findIndex(b => b.id === bookingId);
                    if (bookingIndex === -1) return;

                    // 1. Optimistic Update: Update local state immediately
                    const originalStatus = this.bookings[bookingIndex].status;
                    this.bookings[bookingIndex].status = status;
                    
                    // 2. Refresh UI immediately
                    if (window.currentViewingDate) {
                        this.showDayBookings(window.currentViewingDate);
                    } else {
                        // Fallback: if we don't know the date, try to find it from the booking
                        const bookingDate = new Date(this.bookings[bookingIndex].date);
                        this.showDayBookings(bookingDate); // This might accidentally switch views if we are not careful, but better than nothing. 
                        // Actually, if window.currentViewingDate is not set, we are likely not in the day view. 
                        // But since these buttons are ONLY in the day view, it should be set.
                    }

                    // 3. Send update to server
                    const csrf = await getCsrfToken();
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        credentials: 'include',
                        body: JSON.stringify({
                            action: 'updateBookingStatus',
                            bookingId: bookingId,
                            status: status,
                            csrfToken: csrf
                        })
                    });
                    
                    // Check if response is ok and has content before parsing JSON
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const text = await response.text();
                    if (!text) {
                        // Empty response - assume success (some servers return empty on success)
                        this.loadBookings();
                        return;
                    }
                    
                    let result;
                    try {
                        result = JSON.parse(text);
                    } catch (parseError) {
                        console.error('Failed to parse JSON response:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                    
                    if (result.success) {
                        // 4. Background consistency check
                        this.loadBookings(); 
                    } else {
                        // Revert on failure
                        alert(result.message || 'Kon status niet updaten');
                        this.bookings[bookingIndex].status = originalStatus;
                        if (window.currentViewingDate) this.showDayBookings(window.currentViewingDate);
                    }
                } catch (e) {
                    console.error('Error updating status:', e);
                    alert('Er ging iets mis: ' + (e.message || 'Onbekende fout'));
                    // Revert status on error
                    if (this.bookings[bookingIndex] && originalStatus) {
                        this.bookings[bookingIndex].status = originalStatus;
                        if (window.currentViewingDate) this.showDayBookings(window.currentViewingDate);
                    }
                }
            }


            
            setBookingSeasonDates() {
                const dateInput = document.getElementById('bookingDate');
                if (!dateInput) return;

                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const currentYear = today.getFullYear();
                const currentMonth = today.getMonth();
                const currentDate = today.getDate();

                // Admin can add bookings for current or next season
                // Season: April 1 - October 31
                // Bookings open March 1 for users, but admin can always add
                let bookingYear = currentYear;
                let minDateStr, maxDateStr;
                
                if (currentMonth >= 2 && currentMonth <= 9) {
                    // March - October: current year's season
                    bookingYear = currentYear;
                    if (currentMonth === 2) {
                        // March - allow from April 1
                        minDateStr = `${bookingYear}-04-01`;
                    } else {
                        // April-October - allow from today
                        minDateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(currentDate).padStart(2, '0')}`;
                    }
                    maxDateStr = `${bookingYear}-10-31`;
                } else if (currentMonth >= 10) {
                    // November - December: next year's season
                    bookingYear = currentYear + 1;
                    minDateStr = `${bookingYear}-04-01`;
                    maxDateStr = `${bookingYear}-10-31`;
                } else {
                    // January - February: current year's season (starting April)
                    bookingYear = currentYear;
                    minDateStr = `${bookingYear}-04-01`;
                    maxDateStr = `${bookingYear}-10-31`;
                }

                dateInput.setAttribute('min', minDateStr);
                dateInput.setAttribute('max', maxDateStr);
                
                // Store for validation
                this.bookingYear = bookingYear;
                this.seasonStartDate = `${bookingYear}-04-01`;
                this.seasonEndDate = `${bookingYear}-10-31`;
            }
            
            async loadBookings() {
                // Always load from localStorage first for immediate availability
                try {
                    const storedBookings = localStorage.getItem(this.bookingsStorageKey);
                    if (storedBookings) {
                        this.bookings = JSON.parse(storedBookings);
                        console.log('Loaded bookings from localStorage:', this.bookings.length);
                    }
                } catch (e) {
                    console.error('Error loading bookings from localStorage:', e);
                    this.bookings = [];
                }
                
                // Then try to fetch from server (non-blocking update)
                try {
                    const endpoint = this.detectServerEndpoint();
                    const csrf = await getCsrfToken();
                    
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 3000); // 3 second timeout
                    
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': csrf
                        },
                        credentials: 'include',
                        body: JSON.stringify({ action: 'getBookings' }),
                        signal: controller.signal
                    });
                    
                    clearTimeout(timeoutId);
                    
                    if (response.ok) {
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            const data = await response.json();
                            if (data.success && Array.isArray(data.bookings)) {
                                this.bookings = data.bookings;
                                // Save to localStorage as backup
                                localStorage.setItem(this.bookingsStorageKey, JSON.stringify(this.bookings));
                                console.log('Updated bookings from server:', this.bookings.length);
                                if (data.csrfToken) {
                                    adminSession.csrfToken = data.csrfToken;
                                }
                            }
                        }
                    }
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        console.warn('Could not fetch bookings from server, using localStorage:', error.message);
                    }
                    // Continue with localStorage data already loaded
                }
                
                this.updateStatusBox();
            }
            
            detectServerEndpoint() {
                // Only use Python handler for file:// protocol (opening HTML files directly)
                // When served via any web server (including localhost PHP), use PHP handler
                if (window.location.protocol === 'file:' || window.location.hostname === '') {
                    return 'http://localhost:8000/admin/booking-handler.py';
                }
                return `${window.location.origin}/admin/booking-handler.php`;
            }
            
            updateStatusBox() {
                const now = new Date();
                const recentBookings = this.bookings.filter(booking => {
                    const bookingDate = new Date(booking.createdAt);
                    const hoursDiff = (now - bookingDate) / (1000 * 60 * 60);
                    return hoursDiff < 24; // Bookings from last 24 hours
                });
                
                const statusBox = document.querySelector('.status-box .status-text');
                if (statusBox) {
                    if (recentBookings.length > 0) {
                        statusBox.textContent = `${recentBookings.length} nieuwe reservering(en) in de laatste 24 uur.`;
                    } else {
                        statusBox.textContent = 'Momenteel geen nieuwe reserveringen..';
                    }
                }
            }
            
            setupEventListeners() {
                document.getElementById('addBookingForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.addManualBooking();
                });
                
                // Setup calendar event listeners
                const calendarInput = document.getElementById('bookingCalendar');
                if (calendarInput) {
                    calendarInput.addEventListener('change', () => {
                        this.filterBookingsByDate();
                    });
                }
                
                // Listen for booking status changes
                window.addEventListener('bookingStatusChanged', (e) => {
                    console.log('Booking status changed:', e.detail);
                    this.loadBookings().then(() => {
                        this.renderBookings();
                        this.renderArchive();
                    });
                });
                
                // Archive year selector
                const archiveYearSelect = document.getElementById('archiveYearSelect');
                if (archiveYearSelect) {
                    archiveYearSelect.addEventListener('change', () => {
                        this.renderArchive();
                    });
                }

                const dayPrev = document.getElementById('dayBookingsPrevDay');
                const dayNext = document.getElementById('dayBookingsNextDay');
                if (dayPrev) {
                    dayPrev.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.navigateDayBookings(-1);
                    });
                }
                if (dayNext) {
                    dayNext.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.navigateDayBookings(1);
                    });
                }
            }
            
            async saveBookings() {
                // Save to localStorage first
                localStorage.setItem(this.bookingsStorageKey, JSON.stringify(this.bookings));
                
                // Also try to save to server if we have the booking ID
                // Note: This would need server support for individual booking updates
            }
            
            formatPaymentMethodLabel(method) {
                const m = (method == null ? '' : String(method)).toLowerCase().trim();
                const labels = {
                    ideal: 'iDEAL',
                    bancontact: 'Bancontact',
                    creditcard: 'Credit / debitcard',
                    pay_on_arrival: 'Betalen bij aankomst'
                };
                return labels[m] || (method ? String(method) : '-');
            }

            // Helper function to escape HTML and prevent XSS
            escapeHTML(text) {
                if (text == null) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            renderBookings() {
                // Note: bookingsList doesn't exist in calendar view, only in list view
                // For calendar view, we show bookings on calendar days instead
                this.renderBookingsOnCalendar();
            }
            
            renderBookingsOnCalendar() {
                // Render bookings as indicators on calendar days
                const calendarMonths = document.querySelectorAll('.calendar-month');
                
                calendarMonths.forEach(monthElement => {
                    const monthTitle = monthElement.querySelector('.calendar-month-title');
                    if (!monthTitle) return;
                    
                    // Extract month and year from title (e.g., "november 2025")
                    const titleText = monthTitle.textContent.toLowerCase();
                    const monthNames = ['januari', 'februari', 'maart', 'april', 'mei', 'juni',
                        'juli', 'augustus', 'september', 'oktober', 'november', 'december'];
                    const monthIndex = monthNames.findIndex(name => titleText.startsWith(name));
                    const year = parseInt(titleText.match(/\d{4}/)?.[0] || new Date().getFullYear());
                    
                    if (monthIndex === -1) return;
                    
                    const dayCells = monthElement.querySelectorAll('.calendar-day-cell:not(.other-month)');
                    dayCells.forEach(dayCell => {
                        // Remove existing indicators
                        const existingIndicators = dayCell.querySelectorAll('[data-booking-indicator]');
                        existingIndicators.forEach(ind => ind.remove());
                        
                        const dayNumber = parseInt(dayCell.textContent);
                        if (isNaN(dayNumber)) return;
                        
                        try {
                            const cellDate = new Date(year, monthIndex, dayNumber);
                            cellDate.setHours(12, 0, 0, 0); // Noon to match
                            
                            // Find bookings for this date (range check)
                            const dayBookings = this.bookings.filter(b => {
                                if (!b.date) return false;
                                try {
                                    const startDate = new Date(b.date);
                                    startDate.setHours(12, 0, 0, 0);
                                    
                                    const endDate = b.endDate ? new Date(b.endDate) : new Date(b.date);
                                    endDate.setHours(12, 0, 0, 0);
                                    
                                    return cellDate >= startDate && cellDate <= endDate;
                                } catch (e) {
                                    return false;
                                }
                            });
                            
                            if (dayBookings.length > 0) {
                                // Add booking indicator
                                const indicator = document.createElement('div');
                                indicator.setAttribute('data-booking-indicator', 'true');
                                indicator.style.cssText = 'background: var(--primary-color); color: white; font-size: 10px; padding: 2px 4px; border-radius: 3px; margin-top: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center;';
                                
                                // Count total boats (accounting for quantity field on legacy bookings)
                                const count = dayBookings.reduce((sum, b) => sum + (parseInt(b.quantity) || 1), 0);
                                indicator.textContent = `${count} reservering${count !== 1 ? 'en' : ''}`;
                                indicator.title = `${count} reservering${count !== 1 ? 'en' : ''} op deze datum`;
                                
                                // If it's a multi-day booking, maybe change color or style?
                                // For now, keep it simple/consistent with single day.
                                
                                dayCell.appendChild(indicator);
                            }
                        } catch (error) {
                            console.error('Error rendering booking for date:', error);
                        }
                    });
                });
            }
            
            renderBookingsWithFilter() {
                // This method is for list view (if we add one later)
                const bookingsList = document.getElementById('bookingsList');
                if (!bookingsList) return; // No list view in calendar page
                
                bookingsList.innerHTML = '';
                
                if (this.bookings.length === 0) {
                    const emptyMsg = document.createElement('p');
                    emptyMsg.style.cssText = 'text-align: center; color: var(--text-secondary); padding: var(--spacing-2xl);';
                    emptyMsg.textContent = 'Geen reserveringen gevonden. Voeg een handmatige reservering toe om te beginnen.';
                    bookingsList.appendChild(emptyMsg);
                    return;
                }
                
                // Get filtered bookings
                const filteredBookings = this.getFilteredBookings();
                
                if (filteredBookings.length === 0) {
                    const selectedDate = document.getElementById('bookingCalendar').value;
                    const emptyMsg = document.createElement('p');
                    emptyMsg.style.cssText = 'text-align: center; color: var(--text-secondary); padding: var(--spacing-2xl);';
                    if (selectedDate) {
                        emptyMsg.textContent = `Geen reserveringen gevonden voor ${new Date(selectedDate).toLocaleDateString('nl-NL')}.`;
                    } else {
                        emptyMsg.textContent = 'Geen reserveringen gevonden. Voeg een handmatige reservering toe om te beginnen.';
                    }
                    bookingsList.appendChild(emptyMsg);
                    return;
                }
                
                // Sort bookings by date (newest first)
                const sortedBookings = [...filteredBookings].sort((a, b) => new Date(b.date) - new Date(a.date));
                
                sortedBookings.forEach(booking => {
                    const bookingItem = document.createElement('div');
                    
                    // Escape all user input to prevent XSS
                    const safeStatus = this.escapeHTML(booking.status || '');
                    const safeCustomerName = this.escapeHTML(booking.customerName || '');
                    const safeDate = this.escapeHTML(booking.date || '');
                    const safeBoatName = this.escapeHTML(this.getBoatName(booking.boatType));
                    const safeEmail = this.escapeHTML(booking.customerEmail || '');
                    const safePhone = this.escapeHTML(booking.customerPhone || '');
                    const safeNotes = booking.notes ? this.escapeHTML(booking.notes) : '';
                    const safeId = this.escapeHTML(booking.id || '');
                    
                    bookingItem.className = `booking-item ${safeStatus}`;
                    
                    // Use DOM manipulation instead of innerHTML for safer rendering
                    const statusDiv = document.createElement('div');
                    statusDiv.className = `booking-status status-${safeStatus}`;
                    statusDiv.textContent = safeStatus;
                    
                    const infoDiv = document.createElement('div');
                    infoDiv.className = 'booking-info';
                    
                    const nameH4 = document.createElement('h4');
                    nameH4.textContent = safeCustomerName;
                    
                    const dateP = document.createElement('p');
                    let dateDisplay = safeDate;
                    if (booking.endDate && booking.endDate !== booking.date) {
                         // Calculate duration
                         const start = new Date(booking.date);
                         const end = new Date(booking.endDate);
                         const diffTime = Math.abs(end - start);
                         const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                         dateDisplay += ` t/m ${this.escapeHTML(booking.endDate)} (${diffDays} dagen)`;
                    }
                    dateP.innerHTML = `<strong>Datum:</strong> ${dateDisplay}`;
                    
                    const boatP = document.createElement('p');
                    boatP.innerHTML = `<strong>Boot:</strong> ${safeBoatName}`;
                    
                    const contactP = document.createElement('p');
                    contactP.innerHTML = `<strong>Contact:</strong> ${safeEmail} | ${safePhone}`;
                    
                    infoDiv.appendChild(nameH4);
                    infoDiv.appendChild(dateP);
                    infoDiv.appendChild(boatP);
                    infoDiv.appendChild(contactP);
                    
                    if (safeNotes) {
                        const notesP = document.createElement('p');
                        notesP.innerHTML = `<strong>Notities:</strong> ${safeNotes}`;
                        infoDiv.appendChild(notesP);
                    }
                    
                    const statusSelect = document.createElement('select');
                    statusSelect.className = 'status-select';
                    statusSelect.id = `status-${safeId}`;
                    statusSelect.addEventListener('change', (e) => {
                        bookingSystem.updateBookingStatus(safeId, e.target.value);
                    });
                    
                    const options = [
                        { value: 'manual', label: 'Handmatig' },
                        { value: 'success', label: 'Succesvol' },
                        { value: 'rejected', label: 'Afgewezen' },
                        { value: 'picked-up', label: 'Opgehaald' }
                    ];
                    
                    options.forEach(opt => {
                        const option = document.createElement('option');
                        option.value = opt.value;
                        option.textContent = opt.label;
                        if (booking.status === opt.value) {
                            option.selected = true;
                        }
                        statusSelect.appendChild(option);
                    });
                    
                    const saveBtn = document.createElement('button');
                    saveBtn.className = 'save-btn';
                    saveBtn.textContent = 'Opslaan';
                    saveBtn.addEventListener('click', () => {
                        bookingSystem.saveBookingStatus(safeId);
                    });
                    
                    bookingItem.appendChild(statusDiv);
                    bookingItem.appendChild(infoDiv);
                    bookingItem.appendChild(statusSelect);
                    bookingItem.appendChild(saveBtn);
                    
                    bookingsList.appendChild(bookingItem);
                });
            }
            
            getFilteredBookings() {
                const selectedDate = document.getElementById('bookingCalendar').value;
                if (!selectedDate) {
                    return this.bookings;
                }
                return this.bookings.filter(booking => {
                    // Check if selected date is within booking range
                    const checkDate = new Date(selectedDate);
                    checkDate.setHours(12, 0, 0, 0);
                    
                    try {
                        const startDate = new Date(booking.date);
                        startDate.setHours(12, 0, 0, 0);
                        
                        const endDate = booking.endDate ? new Date(booking.endDate) : new Date(booking.date);
                        endDate.setHours(12, 0, 0, 0);
                        
                        return checkDate >= startDate && checkDate <= endDate;
                    } catch (e) {
                        return false;
                    }
                });
            }
            
            filterBookingsByDate() {
                this.renderBookingsWithFilter();
            }
            
            getBoatName(boatId) {
                const boatNames = {
                    'classic-tender-720': 'Classic Tender 720 (10/12 pers)',
                    'electrosloop-10': 'Electrosloep voor 10 pers',
                    'electrosloop-8': 'Electrosloep voor 8 pers',
                    'electroboat-5': 'Electrosloep voor 5 pers',
                    'sailboat-4-5': 'Zeilboot (4/5 pers)',
                    'sailpunter-3-4': 'Zeilpunter (3/4 pers)',
                    'canoe-3': 'Canadese kano (3 pers)',
                    'kayak-2': 'Kajak (2 pers)',
                    'kayak-1': 'Kajak (1 pers)',
                    'sup-board': 'SUP board (1 pers)'
                };
                return boatNames[boatId] || boatId;
            }
            
            async addManualBooking() {
                const form = document.getElementById('addBookingForm');
                const formData = new FormData(form);
                
                const bookingId = formData.get('bookingId');
                const date = formData.get('date');
                const endDate = formData.get('endDate');
                
                // Validate date is within booking season (April 1 - October 31)
                const selectedDate = new Date(date + 'T00:00:00');
                const selectedMonth = selectedDate.getMonth();

                if (selectedMonth < 3 || selectedMonth > 9) {
                    alert(`Boekingen zijn alleen mogelijk van 1 april tot 31 oktober. De bootverhuur is gesloten buiten dit seizoen.`);
                    return;
                }
                
                let numberOfDays = 1;
                if (endDate) {
                    const start = new Date(date + 'T00:00:00');
                    const end = new Date(endDate + 'T00:00:00');
                    const diffTime = end - start;
                    numberOfDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    
                    if (numberOfDays < 1) {
                        alert('Einddatum moet na de startdatum liggen.');
                        return;
                    }
                }
                
                const bookingData = {
                    date: date,
                    endDate: endDate || date,
                    numberOfDays: numberOfDays,
                    boatType: formData.get('boatType'),
                    customerName: formData.get('customerName'),
                    customerEmail: formData.get('customerEmail'),
                    customerPhone: formData.get('customerPhone'),
                    arrivalTime: formData.get('arrivalTime'),
                    cityOfOrigin: formData.get('cityOfOrigin'),
                    notes: formData.get('notes') || '',
                    status: bookingId ? undefined : 'manual' // Don't change status on edit if already exists
                };
                
                try {
                    const endpoint = this.detectServerEndpoint();
                    const csrf = await getCsrfToken();
                    
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': csrf
                        },
                        credentials: 'include',
                        body: JSON.stringify({ 
                            action: bookingId ? 'updateBooking' : 'createBooking',
                            bookingId: bookingId,
                            bookingData: bookingData,
                            csrfToken: csrf
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        console.log('Booking saved to server');
                        await this.loadBookings();
                        this.renderBookings();
                        this.closeAddBookingModal();
                        form.reset();
                        
                        // If we were viewing a day, refresh it
                        if (window.currentViewingDate) {
                            this.showDayBookings(window.currentViewingDate);
                        }
                    } else {
                        // Handle specific error cases
                        if (response.status === 409) {
                            alert('Let op: ' + (result.message || 'De boot is niet beschikbaar.'));
                        } else {
                            alert('Fout bij opslaan: ' + (result.message || 'Onbekende fout'));
                        }
                    }
                } catch (error) {
                    console.error('Error saving manual booking:', error);
                    alert('Er is een fout opgetreden bij het opslaan naar de server.');
                }
            }

            // Populate arrival time options (09:00 to 18:00 in 15-minute intervals)
            populateArrivalTimeOptions() {
                const arrivalTimeSelect = document.getElementById('bookingArrivalTime');
                if (!arrivalTimeSelect) return;

                // Clear existing options except the first placeholder
                arrivalTimeSelect.innerHTML = '<option value="">-- Selecteer tijd --</option>';

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
            }

            showEditBookingModal(bookingId) {
                const booking = this.bookings.find(b => b.id === bookingId);
                if (!booking) return;

                // Populate arrival time options
                this.populateArrivalTimeOptions();

                document.getElementById('modalHeaderTitle').textContent = 'Reservering bewerken';
                document.getElementById('editBookingId').value = booking.id;
                document.getElementById('bookingDate').value = booking.date;
                document.getElementById('bookingEndDate').value = booking.endDate || booking.date;
                document.getElementById('bookingBoatType').value = booking.boatType;
                document.getElementById('bookingCustomerName').value = booking.customerName;
                document.getElementById('bookingCustomerEmail').value = booking.customerEmail;
                document.getElementById('bookingCustomerPhone').value = booking.customerPhone;
                document.getElementById('bookingArrivalTime').value = booking.arrivalTime || booking.arrival_time || '';
                document.getElementById('bookingCityOfOrigin').value = booking.cityOfOrigin || booking.city_of_origin || '';
                document.getElementById('bookingNotes').value = booking.notes || '';
                
                const modal = document.getElementById('addBookingModal');
                modal.style.display = 'block';
                modal.classList.add('active');
            }

            async deleteBooking(bookingId) {
                if (!confirm('Weet je zeker dat je deze reservering wilt verwijderen?')) return;

                try {
                    const endpoint = this.detectServerEndpoint();
                    const csrf = await getCsrfToken();
                    
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': csrf
                        },
                        credentials: 'include',
                        body: JSON.stringify({ 
                            action: 'deleteBooking',
                            bookingId: bookingId,
                            csrfToken: csrf
                        })
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        console.log('Booking deleted from server');
                        await this.loadBookings();
                        this.renderBookings();
                        
                        // If we were viewing a day, refresh it
                        if (window.currentViewingDate) {
                            this.showDayBookings(window.currentViewingDate);
                        }
                    } else {
                        alert('Fout bij verwijderen: ' + (result.message || 'Onbekende fout'));
                    }
                } catch (error) {
                    console.error('Error deleting booking:', error);
                    alert('Er is een fout opgetreden bij het verwijderen.');
                }
            }
            
            // Note: updateBookingStatus is defined earlier in the class (around line 885)
            // Do not duplicate it here as it would overwrite the correct implementation
            
            saveBookingStatus(bookingId) {
                // Status is already updated in updateBookingStatus
                this.saveBookings();
            }
            
            showAddBookingModal() {
                const form = document.getElementById('addBookingForm');
                if (form) form.reset();
                const editId = document.getElementById('editBookingId');
                if (editId) editId.value = '';
                const header = document.getElementById('modalHeaderTitle');
                if (header) header.textContent = 'Handmatige reservering toevoegen';
                
                // Populate arrival time options
                this.populateArrivalTimeOptions();
                
                document.getElementById('addBookingModal').style.display = 'block';
            }
            
            closeAddBookingModal() {
                const modal = document.getElementById('addBookingModal');
                modal.style.display = 'none';
                modal.classList.remove('active');
            }
        }
        
        // Global functions
        function showAddBookingModal() {
            bookingSystem.showAddBookingModal();
        }
        
        function closeAddBookingModal() {
            bookingSystem.closeAddBookingModal();
        }
        
        function closeDayBookingsModal() {
            const modal = document.getElementById('dayBookingsModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
        
        function closeDayBookings() {
            const section = document.getElementById('dayBookingsSection');
            if (section) {
                section.style.display = 'none';
            }
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const dayModal = document.getElementById('dayBookingsModal');
            if (event.target === dayModal) {
                closeDayBookingsModal();
            }
            const addModal = document.getElementById('addBookingModal');
            if (event.target === addModal) {
                closeAddBookingModal();
            }
        };
        
        async function logout() {
            try {
                const endpoint = (window.location.protocol === 'file:' || window.location.hostname === '')
                    ? 'http://localhost:8000/admin/booking-handler.py'
                    : `${window.location.origin}/admin/booking-handler.php`;
                
                const csrf = await getCsrfToken();
                await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrf
                    },
                    body: JSON.stringify({
                        action: 'logout',
                        csrfToken: csrf
                    })
                });
            } catch (error) {
                console.error('Logout request failed:', error);
            }
            
            localStorage.removeItem('adminAuthenticated');
            localStorage.removeItem('adminUser');
            sessionStorage.removeItem('adminLoginTime');
            sessionStorage.removeItem('adminSessionToken');
            sessionStorage.removeItem('csrfToken');
            window.location.href = '../pages/admin-login.php';
        }
        
        function clearBookingDateFilter() {
            document.getElementById('bookingCalendar').value = '';
            bookingSystem.renderBookings();
        }
        
        // Initialize the booking management system when DOM is ready
        let bookingSystem;
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                bookingSystem = new BookingManagementSystem();
                window.bookingSystem = bookingSystem;
            });
        } else {
            bookingSystem = new BookingManagementSystem();
            window.bookingSystem = bookingSystem;
        }
    </script>
</body>
</html>
