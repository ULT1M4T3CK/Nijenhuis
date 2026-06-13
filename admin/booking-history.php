<?php
// Start session and verify admin authentication
require_once 'admin-auth.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boekingsgeschiedenis - Nijenhuis Botenverhuur</title>
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <link rel="stylesheet" href="admin-consolidated.css">
    <link rel="icon" type="image/svg+xml" href="../frontend/Images/logo-white.svg">
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="admin-nav">
        <div class="admin-nav-container">
            <img src="../frontend/Images/logo-white.svg" alt="Nijenhuis" class="admin-nav-logo" style="height: 40px; width: auto;">
            <div class="admin-nav-links">
                <a href="admin-static.php" class="admin-nav-link">Dashboard</a>
                <a href="boat-management.php" class="admin-nav-link">Bootbeheer</a>
                <a href="booking-management.php" class="admin-nav-link">Reserveringsbeheer</a>
                <a href="booking-history.php" class="admin-nav-link active">Boekingsgeschiedenis</a>
                <a href="for-sale-management.php" class="admin-nav-link">Te koop</a>
            </div>
            <button class="admin-nav-logout" onclick="logout()">Uitloggen</button>
        </div>
    </nav>

    <!-- Admin Header Section -->
    <div class="admin-header">
        <div class="admin-container">
            <h1 class="admin-title">Boekingsgeschiedenis</h1>
            <p class="admin-subtitle">Nijenhuis Botenverhuur - Overzicht van alle boekingen</p>
        </div>
    </div>
    
    <div class="booking-history">
        <div class="management-container">
            
            <!-- Main Content Layout -->
            <div class="content-layout">
                <!-- Filters Section (Left Side) -->
                <div class="filters-section">
                    <div class="filters-grid">
                        <!-- Search Filters -->
                        <div class="filter-group">
                            <label for="nameFilter" class="filter-label">Naam</label>
                            <input type="text" id="nameFilter" class="filter-select" placeholder="Zoek op naam..." style="padding: 8px;">
                        </div>
                        
                        <div class="filter-group">
                            <label for="emailFilter" class="filter-label">Email</label>
                            <input type="text" id="emailFilter" class="filter-select" placeholder="Zoek op email..." style="padding: 8px;">
                        </div>

                        <div class="filter-group">
                            <label for="yearFilter" class="filter-label">Jaar</label>
                            <select id="yearFilter" class="filter-select">
                                <option value="">Alle jaren</option>
                                <!-- Year options will be populated dynamically -->
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="monthFilter" class="filter-label">Maand</label>
                            <select id="monthFilter" class="filter-select">
                                <option value="">Alle maanden</option>
                                <option value="0">Januari</option>
                                <option value="1">Februari</option>
                                <option value="2">Maart</option>
                                <option value="3">April</option>
                                <option value="4">Mei</option>
                                <option value="5">Juni</option>
                                <option value="6">Juli</option>
                                <option value="7">Augustus</option>
                                <option value="8">September</option>
                                <option value="9">Oktober</option>
                                <option value="10">November</option>
                                <option value="11">December</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="dayFilter" class="filter-label">Dag</label>
                            <select id="dayFilter" class="filter-select">
                                <option value="">Alle dagen</option>
                                <!-- Day options will be populated dynamically (1-31) -->
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="boatFilter" class="filter-label">Boot</label>
                            <select id="boatFilter" class="filter-select">
                                <option value="">Alle boten</option>
                                <!-- Boat options will be populated dynamically -->
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="daysFilter" class="filter-label">Aantal dagen</label>
                            <select id="daysFilter" class="filter-select">
                                <option value="">Alle duur</option>
                                <option value="1">1 dag</option>
                                <option value="2">2 dagen</option>
                                <option value="3">3 dagen</option>
                                <option value="4">4 dagen</option>
                                <option value="5">5 dagen</option>
                                <option value="6">6 dagen</option>
                                <option value="7">7 dagen</option>
                                <option value="7+">Meer dan 7 dagen</option>
                            </select>
                        </div>
                        
                        <div class="filter-actions">
                            <button type="button" class="btn btn-primary" onclick="applyFilters()">Filter toepassen</button>
                            <button type="button" class="btn btn-secondary" onclick="clearFilters()">Filters wissen</button>
                        </div>
                    </div>
                </div>
                
                <!-- Bookings List (Right Side) -->
                <div id="bookingsList" class="bookings-list">
                    <!-- Bookings will be populated here -->
                </div>
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
                if (!response.ok) return false;
                const result = await response.json();
                if (result.success && result.authenticated) {
                    adminSession.csrfToken = result.csrfToken || '';
                    return true;
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

        class BookingHistorySystem {
            constructor() {
                // Use an admin-only cache key so it doesn't get overwritten by public pages
                this.bookingsStorageKey = 'nijenhuis_admin_bookings';
                this.boatsStorageKey = 'nijenhuis_boats';
                this.bookings = [];
                this.boats = [];
                this.filteredBookings = [];
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
                const isAuthenticated = await this.checkAuth();
                if (!isAuthenticated) return;
                
                await this.loadData();
                this.setupBoatFilter();
                this.setupYearFilter();
                this.setupDayFilter();
                this.setupEventListeners();
                this.applyFilters();
            }
            
            async loadData() {
                // Load bookings
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
                        body: JSON.stringify({ action: 'getHistory', csrfToken: csrf })
                    });
                    
                    if (response.ok) {
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            const data = await response.json();
                            if (data.success && Array.isArray(data.bookings)) {
                                this.bookings = data.bookings;
                                localStorage.setItem(this.bookingsStorageKey, JSON.stringify(this.bookings));
                                return;
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error loading bookings from server:', error);
                }
                
                // Fallback to localStorage
                const storedBookings = localStorage.getItem(this.bookingsStorageKey);
                this.bookings = storedBookings ? JSON.parse(storedBookings) : [];
                
                // Load boats
                const storedBoats = localStorage.getItem(this.boatsStorageKey);
                this.boats = storedBoats ? JSON.parse(storedBoats) : [];
            }
            
            detectServerEndpoint() {
                // Only use Python handler for file:// protocol (opening HTML files directly)
                // When served via any web server (including localhost PHP), use PHP handler
                if (window.location.protocol === 'file:' || window.location.hostname === '') {
                    return 'http://localhost:8000/admin/booking-handler.py';
                }
                return `${window.location.origin}/admin/booking-handler.php`;
            }
            
            setupBoatFilter() {
                const boatFilter = document.getElementById('boatFilter');
                if (!boatFilter) return;
                
                // Get unique boats from bookings
                const boatIds = new Set();
                this.bookings.forEach(booking => {
                    if (this.isCanceledHistoryStatus(booking)) return;
                    if (booking.boatType) {
                        boatIds.add(booking.boatType);
                    }
                });
                
                // Add boats from localStorage
                this.boats.forEach(boat => {
                    boatIds.add(boat.id);
                });
                
                // Populate boat filter
                boatIds.forEach(boatId => {
                    const option = document.createElement('option');
                    option.value = boatId;
                    option.textContent = this.getBoatDisplayName(boatId);
                    boatFilter.appendChild(option);
                });
            }
            
            getBoatDisplayName(boatId) {
                // Try to get from localStorage boats first
                try {
                    const boat = this.boats.find(b => b.id === boatId);
                    if (boat && boat.name) {
                        return boat.name;
                    }
                } catch (e) {
                    console.error('Error loading boat name:', e);
                }
                
                // Fallback to default names
                const boatNames = {
                    'classic-tender-720': 'Classic Tender 720 10/12 pers',
                    'electrosloop-10': 'Electrosloep 10 pers',
                    'classic-tender-570': 'Classic Tender 570 8 pers',
                    'electrosloop-8': 'Electrosloep 8 pers',
                    'sailboat-4-5': 'Zeilboot 4/5 pers',
                    'sailpunter-3-4': 'Zeilpunter 3/4 pers',
                    'electroboat-5': 'Electroboot 5 pers',
                    'canoe-3': 'Canadese kano 3 pers',
                    'kayak-2': 'Kajak 2 pers',
                    'kayak-1': 'Kajak 1 pers',
                    'sup-board': 'SUP board 1 pers'
                };
                
                return boatNames[boatId] || boatId;
            }
            
            setupYearFilter() {
                const yearFilter = document.getElementById('yearFilter');
                if (!yearFilter) return;
                
                // Get all unique years from past bookings
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                const years = new Set();
                this.bookings.forEach(booking => {
                    if (this.isCanceledHistoryStatus(booking)) return;
                    if (booking.date) {
                        try {
                            const date = new Date(booking.date);
                            date.setHours(0, 0, 0, 0);
                            if (date < today) {
                                years.add(date.getFullYear());
                            }
                        } catch (e) {
                            console.error('Error parsing booking date:', e);
                        }
                    }
                });
                
                // Add years from 2026 to 2040
                for (let year = 2026; year <= 2040; year++) {
                    years.add(year);
                }
                
                // Sort years descending
                const sortedYears = Array.from(years).sort((a, b) => b - a);
                
                sortedYears.forEach(year => {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    yearFilter.appendChild(option);
                });
            }
            
            setupDayFilter() {
                this.updateDayFilter();
            }
            
            updateDayFilter() {
                const dayFilter = document.getElementById('dayFilter');
                if (!dayFilter) return;
                
                const monthFilter = document.getElementById('monthFilter');
                const yearFilter = document.getElementById('yearFilter');
                
                const selectedMonth = monthFilter ? monthFilter.value : '';
                const selectedYear = yearFilter ? yearFilter.value : '';
                
                // Clear existing day options except "Alle dagen"
                while (dayFilter.children.length > 1) {
                    dayFilter.removeChild(dayFilter.lastChild);
                }
                
                // If no month is selected, show all days 1-31
                if (selectedMonth === '') {
                    for (let day = 1; day <= 31; day++) {
                        const option = document.createElement('option');
                        option.value = day;
                        option.textContent = day;
                        dayFilter.appendChild(option);
                    }
                    return;
                }
                
                // Calculate days in the selected month
                let daysInMonth = 31;
                const month = parseInt(selectedMonth);
                
                // Months with 30 days: April (3), June (5), September (8), November (10)
                if (month === 3 || month === 5 || month === 8 || month === 10) {
                    daysInMonth = 30;
                }
                // February (1): 28 or 29 days depending on leap year
                else if (month === 1) {
                    if (selectedYear) {
                        const year = parseInt(selectedYear);
                        // Leap year: divisible by 4, but not by 100 unless also by 400
                        const isLeapYear = (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
                        daysInMonth = isLeapYear ? 29 : 28;
                    } else {
                        // If no year selected, default to 29 to cover leap years
                        daysInMonth = 29;
                    }
                }
                // All other months have 31 days
                
                // Add days for the selected month
                for (let day = 1; day <= daysInMonth; day++) {
                    const option = document.createElement('option');
                    option.value = day;
                    option.textContent = day;
                    dayFilter.appendChild(option);
                }
            }
            
            isLeapYear(year) {
                return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
            }
            
            setupEventListeners() {
                const yearFilter = document.getElementById('yearFilter');
                const monthFilter = document.getElementById('monthFilter');
                const dayFilter = document.getElementById('dayFilter');
                const boatFilter = document.getElementById('boatFilter');
                const daysFilter = document.getElementById('daysFilter');
                
                if (yearFilter) {
                    yearFilter.addEventListener('change', () => {
                        this.updateDayFilter();
                        this.applyFilters();
                    });
                }
                
                if (monthFilter) {
                    monthFilter.addEventListener('change', () => {
                        this.updateDayFilter();
                        this.applyFilters();
                    });
                }
                
                if (dayFilter) {
                    dayFilter.addEventListener('change', () => {
                        this.applyFilters();
                    });
                }
                
                if (boatFilter) {
                    boatFilter.addEventListener('change', () => {
                        this.applyFilters();
                    });
                }
                
                if (daysFilter) {
                    daysFilter.addEventListener('change', () => {
                        this.applyFilters();
                    });
                }

                // Search Listeners
                const nameFilter = document.getElementById('nameFilter');
                const emailFilter = document.getElementById('emailFilter');
                
                if (nameFilter) {
                    nameFilter.addEventListener('input', () => this.applyFilters());
                }
                if (emailFilter) {
                    emailFilter.addEventListener('input', () => this.applyFilters());
                }
            }
            
            applyFilters() {
                const yearFilter = document.getElementById('yearFilter');
                const monthFilter = document.getElementById('monthFilter');
                const dayFilter = document.getElementById('dayFilter');
                const boatFilter = document.getElementById('boatFilter');
                const daysFilter = document.getElementById('daysFilter');
                
                const selectedYear = yearFilter ? yearFilter.value : '';
                const selectedMonth = monthFilter ? monthFilter.value : '';
                const selectedDay = dayFilter ? dayFilter.value : '';
                const selectedBoat = boatFilter ? boatFilter.value : '';
                const selectedDays = daysFilter ? daysFilter.value : '';
                
                const nameSearch = document.getElementById('nameFilter') ? document.getElementById('nameFilter').value.toLowerCase().trim() : '';
                const emailSearch = document.getElementById('emailFilter') ? document.getElementById('emailFilter').value.toLowerCase().trim() : '';
                
                // Get current date to determine past bookings
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                // Filter past bookings
                this.filteredBookings = this.bookings.filter(booking => {
                     if (this.isCanceledHistoryStatus(booking)) {
                         return false;
                     }

                     // Name Filter
                     if (nameSearch && (!booking.customerName || !booking.customerName.toLowerCase().includes(nameSearch))) {
                         return false;
                     }
                     
                     // Email Filter
                     if (emailSearch && (!booking.customerEmail || !booking.customerEmail.toLowerCase().includes(emailSearch))) {
                         return false;
                     }

                    if (!booking.date) return false;
                    
                    try {
                        const bookingDate = new Date(booking.date);
                        bookingDate.setHours(0, 0, 0, 0);
                        
                        // Only show past bookings
                        if (bookingDate >= today) return false;
                        
                        // Filter by year
                        if (selectedYear) {
                            const year = bookingDate.getFullYear();
                            if (year !== parseInt(selectedYear)) return false;
                        }
                        
                        // Filter by month
                        if (selectedMonth !== '') {
                            const month = bookingDate.getMonth();
                            if (month !== parseInt(selectedMonth)) return false;
                        }
                        
                        // Filter by day
                        if (selectedDay) {
                            const day = bookingDate.getDate();
                            if (day !== parseInt(selectedDay)) return false;
                        }
                        
                        // Filter by boat
                        if (selectedBoat && booking.boatType !== selectedBoat) {
                            return false;
                        }
                        
                        // Filter by days
                        if (selectedDays) {
                            // Check if booking has duration/numberOfDays field
                            const bookingDays = booking.numberOfDays || booking.duration || booking.number_of_days || booking.days || 1;
                            
                            if (selectedDays === '7+') {
                                if (bookingDays < 7) return false;
                            } else {
                                const selectedDaysNum = parseInt(selectedDays);
                                if (bookingDays !== selectedDaysNum) return false;
                            }
                        }
                        
                        return true;
                    } catch (e) {
                        return false;
                    }
                });
                
                this.renderBookings();
            }

            isCanceledHistoryStatus(booking) {
                const status = booking.status || '';
                const incompleteOnlineStatuses = ['open', 'pending', 'not-confirmed'];
                return ['canceled', 'cancelled', 'payment-rejected', 'failed', 'expired', 'rejected'].includes(status)
                    || incompleteOnlineStatuses.includes(status);
            }
            
            renderBookings() {
                const bookingsList = document.getElementById('bookingsList');
                if (!bookingsList) return;
                
                bookingsList.innerHTML = '';
                
                if (this.filteredBookings.length === 0) {
                    bookingsList.innerHTML = '<div class="empty-state">Geen boekingsgeschiedenis gevonden.</div>';
                    return;
                }
                
                // One entry per boat: expand any booking with quantity > 1 (existing reservations)
                const expandedBookings = [];
                this.filteredBookings.forEach(booking => {
                    const qty = parseInt(booking.quantity) || 1;
                    if (qty <= 1) {
                        expandedBookings.push(booking);
                    } else {
                        for (let i = 0; i < qty; i++) {
                            const entry = Object.assign({}, booking);
                            entry._expandedIndex = i + 1;
                            entry._expandedTotal = qty;
                            entry.amount = (booking.amount || 0) / qty;
                            expandedBookings.push(entry);
                        }
                    }
                });
                
                // Group bookings by year, month, and day
                const bookingsByYear = {};
                
                expandedBookings.forEach(booking => {
                    try {
                        const date = new Date(booking.date);
                        const year = date.getFullYear();
                        const month = date.getMonth();
                        const day = date.getDate();
                        
                        if (!bookingsByYear[year]) {
                            bookingsByYear[year] = {};
                        }
                        
                        if (!bookingsByYear[year][month]) {
                            bookingsByYear[year][month] = {};
                        }
                        
                        if (!bookingsByYear[year][month][day]) {
                            bookingsByYear[year][month][day] = [];
                        }
                        
                        bookingsByYear[year][month][day].push(booking);
                    } catch (e) {
                        console.error('Error processing booking date:', e);
                    }
                });
                
                // Sort years descending
                const sortedYears = Object.keys(bookingsByYear)
                    .map(y => parseInt(y))
                    .sort((a, b) => b - a);
                
                const monthNames = ['januari', 'februari', 'maart', 'april', 'mei', 'juni',
                    'juli', 'augustus', 'september', 'oktober', 'november', 'december'];
                
                sortedYears.forEach(year => {
                    const yearGroup = document.createElement('div');
                    yearGroup.className = 'year-group';
                    
                    const yearHeader = document.createElement('div');
                    yearHeader.className = 'year-header';
                    yearHeader.textContent = year;
                    yearGroup.appendChild(yearHeader);
                    
                    const monthsContainer = document.createElement('div');
                    monthsContainer.className = 'months-container';
                    
                    // Sort months descending
                    const sortedMonths = Object.keys(bookingsByYear[year])
                        .map(m => parseInt(m))
                        .sort((a, b) => b - a);
                    
                    sortedMonths.forEach(month => {
                        const monthGroup = document.createElement('div');
                        monthGroup.className = 'month-group';
                        
                        const monthHeader = document.createElement('div');
                        monthHeader.className = 'month-header';
                        monthHeader.textContent = monthNames[month] || `Maand ${month + 1}`;
                        monthGroup.appendChild(monthHeader);
                        
                        const daysContainer = document.createElement('div');
                        daysContainer.className = 'days-container';
                        
                        // Sort days descending
                        const sortedDays = Object.keys(bookingsByYear[year][month])
                            .map(d => parseInt(d))
                            .sort((a, b) => b - a);
                        
                        sortedDays.forEach(day => {
                            const dayGroup = document.createElement('div');
                            dayGroup.className = 'day-group';
                            
                            const dayHeader = document.createElement('div');
                            dayHeader.className = 'day-header';
                            dayHeader.textContent = `${day} ${monthNames[month]}`;
                            dayGroup.appendChild(dayHeader);
                            
                            bookingsByYear[year][month][day].forEach(booking => {
                                const bookingItem = this.createBookingItem(booking);
                                dayGroup.appendChild(bookingItem);
                            });
                            
                            daysContainer.appendChild(dayGroup);
                        });
                        
                        monthGroup.appendChild(daysContainer);
                        monthsContainer.appendChild(monthGroup);
                    });
                    
                    yearGroup.appendChild(monthsContainer);
                    bookingsList.appendChild(yearGroup);
                });
            }
            
            createBookingItem(booking) {
                const item = document.createElement('div');
                item.className = `history-booking-item ${this.getHistoryStatusClass(booking)}`;
                item.dataset.id = booking.id || '';

                const customerName = booking.customerName || booking.customer_name || booking.name || 'Onbekende klant';
                const boatLabel = this.getHistoryBoatLabel(booking);
                const arrivalTime = booking.arrivalTime || booking.arrival_time || '-';
                const formattedArrivalTime = arrivalTime !== '-' && arrivalTime.includes(':') ? arrivalTime.substring(0, 5) : arrivalTime;
                const bookingDays = this.getBookingDays(booking);
                const durationLabel = `${bookingDays} ${bookingDays === 1 ? 'dag' : 'dagen'}`;
                const priceLabel = this.formatMoney(booking.amount);
                const statusLabel = this.formatHistoryStatus(booking);
                const dateDisplay = this.formatDateRange(booking);
                const paymentMethod = this.formatPaymentMethodLabel(booking.paymentMethod);
                const cityOfOrigin = booking.cityOfOrigin || booking.city_of_origin || '-';
                const notes = booking.notes || 'Geen notities';

                item.innerHTML = `
                    <div class="booking-col booking-name">${this.escapeHTML(customerName)}</div>
                    <div class="booking-col booking-boat">${this.escapeHTML(boatLabel)}</div>
                    <div class="booking-col booking-arrival-time">${this.escapeHTML(formattedArrivalTime)}</div>
                    <div class="booking-col booking-duration">${this.escapeHTML(durationLabel)}</div>
                    <div class="booking-col booking-price">${this.escapeHTML(priceLabel)}</div>
                    <div class="btn-action-group">
                        <strong>${this.escapeHTML(statusLabel)}</strong>
                        <span class="expand-arrow">▼</span>
                    </div>
                    <div class="booking-details-expand">
                        <div class="details-grid">
                            <div><strong>Datum:</strong> ${this.escapeHTML(dateDisplay)}</div>
                            <div><strong>Prijs:</strong> ${this.escapeHTML(priceLabel)}</div>
                            <div><strong>Betaalmethode:</strong> ${this.escapeHTML(paymentMethod)}</div>
                            <div><strong>Email:</strong> ${this.escapeHTML(booking.customerEmail || booking.customer_email || '-')}</div>
                            <div><strong>Telefoon:</strong> ${this.escapeHTML(booking.customerPhone || booking.customer_phone || '-')}</div>
                            <div><strong>Woonplaats:</strong> ${this.escapeHTML(cityOfOrigin)}</div>
                            <div style="grid-column: 1 / -1;"><strong>Notities:</strong> ${this.escapeHTML(notes)}</div>
                        </div>
                    </div>
                `;

                item.addEventListener('click', () => {
                    item.classList.toggle('expanded');
                });
                
                return item;
            }

            getHistoryBoatLabel(booking) {
                let boatLabel = booking.boatType ? this.getBoatDisplayName(booking.boatType) : '-';
                const boatMeta = this.boats.find(b => b.id === booking.boatType);
                if (booking.engineOption === 'with' && boatMeta && boatMeta.pricingWithEngine && Array.isArray(boatMeta.pricingWithEngine) && boatMeta.pricingWithEngine.length > 0) {
                    boatLabel += ' + motor';
                }
                if (booking._expandedTotal > 1) boatLabel += ` (${booking._expandedIndex}/${booking._expandedTotal})`;
                return boatLabel;
            }

            getBookingDays(booking) {
                const storedDays = parseInt(booking.numberOfDays || booking.duration || booking.number_of_days || booking.days, 10);
                if (storedDays > 0) return storedDays;
                if (booking.date && booking.endDate) {
                    const start = new Date(booking.date);
                    const end = new Date(booking.endDate);
                    if (!Number.isNaN(start.getTime()) && !Number.isNaN(end.getTime())) {
                        return Math.max(1, Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1);
                    }
                }
                return 1;
            }

            formatMoney(amount) {
                const value = Number(amount);
                return Number.isFinite(value) ? `€${value.toFixed(2)}` : '-';
            }

            formatDateEuropean(dateString) {
                if (!dateString) return '-';
                try {
                    const date = new Date(dateString);
                    if (Number.isNaN(date.getTime())) return dateString;
                    return date.toLocaleDateString('nl-NL', { day: '2-digit', month: '2-digit', year: 'numeric' });
                } catch (e) {
                    return dateString;
                }
            }

            formatDateRange(booking) {
                const start = this.formatDateEuropean(booking.date);
                const end = booking.endDate && booking.endDate !== booking.date ? this.formatDateEuropean(booking.endDate) : '';
                return end ? `${start} t/m ${end}` : start;
            }

            formatPaymentMethodLabel(method) {
                const labels = {
                    ideal: 'iDEAL',
                    bancontact: 'Bancontact',
                    creditcard: 'Creditcard',
                    paypal: 'PayPal',
                    applepay: 'Apple Pay',
                    googlepay: 'Google Pay',
                    pay_on_arrival: 'Betalen bij aankomst'
                };
                return labels[method] || method || '-';
            }

            formatHistoryStatus(booking) {
                const status = booking.status || 'manual';
                const incompleteOnlineStatuses = ['open', 'pending', 'not-confirmed'];
                const isPayOnArrival = (booking.paymentMethod || '') === 'pay_on_arrival';

                if (status === 'manual' && booking.source === 'receptie') return 'Receptie';
                if (status === 'manual') return 'Handmatig';
                if (isPayOnArrival && (incompleteOnlineStatuses.includes(status) || status === 'confirmed')) return 'Betalen bij aankomst';
                if (['paid', 'confirmed-paid', 'success'].includes(status)) return 'Betaald';
                if (status === 'picked_up') return 'Afgehaald';
                if (['canceled', 'cancelled', 'payment-rejected', 'failed', 'expired', 'rejected'].includes(status) || incompleteOnlineStatuses.includes(status)) return 'Geannuleerd';
                return status;
            }

            getHistoryStatusClass(booking) {
                const status = booking.status || 'manual';
                const incompleteOnlineStatuses = ['open', 'pending', 'not-confirmed'];
                const isPayOnArrival = (booking.paymentMethod || '') === 'pay_on_arrival';

                if (isPayOnArrival && (incompleteOnlineStatuses.includes(status) || status === 'confirmed')) return 'status-pay-on-arrival';
                if (['paid', 'confirmed-paid', 'success'].includes(status)) return 'status-paid';
                if (status === 'picked_up') return 'status-picked_up';
                if (['canceled', 'cancelled', 'payment-rejected', 'failed', 'expired', 'rejected'].includes(status) || incompleteOnlineStatuses.includes(status)) return 'status-canceled';
                return 'status-manual';
            }
            
            escapeHTML(text) {
                if (text == null) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        }
        
        // Global functions
        function applyFilters() {
            bookingHistorySystem.applyFilters();
        }
        
        function clearFilters() {
            document.getElementById('yearFilter').value = '';
            document.getElementById('monthFilter').value = '';
            document.getElementById('dayFilter').value = '';
            document.getElementById('boatFilter').value = '';
            document.getElementById('daysFilter').value = '';
            
            if(document.getElementById('nameFilter')) document.getElementById('nameFilter').value = '';
            if(document.getElementById('emailFilter')) document.getElementById('emailFilter').value = '';

            // Update day filter to show all days (1-31) when filters are cleared
            bookingHistorySystem.updateDayFilter();
            bookingHistorySystem.applyFilters();
        }
        
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
        
        // Initialize the booking history system
        const bookingHistorySystem = new BookingHistorySystem();
    </script>
</body>
</html>

