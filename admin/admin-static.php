<?php
// Start session and verify admin authentication
require_once 'admin-auth.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Nijenhuis Botenverhuur</title>
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <link rel="stylesheet" href="admin-consolidated.css">
    <link rel="icon" type="image/svg+xml" href="../frontend/Images/logo-white.svg">
    <style>
        .overview-tables-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-lg, 1.5rem);
        }
        @media (max-width: 768px) {
            .overview-tables-grid { grid-template-columns: 1fr; }
        }
        .overview-table-title {
            margin: 0 0 0.75rem;
            font-size: 1rem;
            font-weight: 600;
        }
        .overview-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }
        .overview-table th,
        .overview-table td {
            padding: 0.6rem 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--light-gray, #e9ecef);
        }
        .overview-table th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--text-secondary, #6c757d);
        }
        .overview-table td:last-child,
        .overview-table th:last-child {
            text-align: right;
        }
        .overview-table tbody tr:nth-child(even) {
            background: var(--bg-light, #f8f9fa);
        }
        .overview-table td:last-child {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="admin-nav">
        <div class="admin-nav-container">
            <img src="../frontend/Images/logo-white.svg" alt="Nijenhuis" class="admin-nav-logo" style="height: 40px; width: auto;">
            <div class="admin-nav-links">
                <a href="admin-static.php" class="admin-nav-link active">Dashboard</a>
                <a href="boat-management.php" class="admin-nav-link">Bootbeheer</a>
                <a href="booking-management.php" class="admin-nav-link">Reserveringsbeheer</a>
                <a href="booking-history.php" class="admin-nav-link">Boekingsgeschiedenis</a>
                <a href="for-sale-management.php" class="admin-nav-link">Te koop</a>
            </div>
            <button class="admin-nav-logout" onclick="logout()">Uitloggen</button>
        </div>
    </nav>

    <div class="admin-dashboard">
        <!-- Admin Dashboard -->
        <div id="adminSection" class="hidden">
            <div class="admin-header">
                <div class="admin-container">
                    <h1 class="admin-title">Admin Dashboard</h1>
                    <p class="admin-subtitle">Nijenhuis Botenverhuur - Reserveringsbeheer</p>
                </div>
            </div>

            <div class="admin-container">
                <div class="admin-content">
                    <!-- Statistics -->
                    <div class="admin-stats">
                        <div class="stat-card">
                            <div class="stat-number" id="totalBookings">0</div>
                            <div class="stat-label">Totaal Reserveringen</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="manualBookings">0</div>
                            <div class="stat-label">Handmatig</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="confirmedBookings">0</div>
                            <div class="stat-label">Bevestigd</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="cancelledBookings">0</div>
                            <div class="stat-label">Geannuleerd</div>
                        </div>
                    </div>

                    <!-- Booking Overview by Method -->
                    <div class="admin-section">
                        <div class="section-header">
                            <h2 class="section-title">Boekingsoverzicht per methode</h2>
                        </div>
                        <div class="section-content">
                            <div class="overview-tables-grid">
                                <div class="overview-table-wrap">
                                    <h3 class="overview-table-title">Boekingskanaal</h3>
                                    <table class="overview-table">
                                        <thead>
                                            <tr>
                                                <th>Kanaal</th>
                                                <th>Aantal boten</th>
                                            </tr>
                                        </thead>
                                        <tbody id="channelOverviewBody">
                                            <tr><td colspan="2" style="text-align:center; color: #999;">Laden...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="overview-table-wrap">
                                    <h3 class="overview-table-title">Betaalmethode</h3>
                                    <table class="overview-table">
                                        <thead>
                                            <tr>
                                                <th>Methode</th>
                                                <th>Aantal boten</th>
                                            </tr>
                                        </thead>
                                        <tbody id="paymentOverviewBody">
                                            <tr><td colspan="2" style="text-align:center; color: #999;">Laden...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="admin-section">
                        <div class="section-header">
                            <h2 class="section-title">Snelle Acties</h2>
                        </div>
                        <div class="section-content">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-lg);">
                                <a href="boat-management.php" class="btn-admin btn-edit" style="text-decoration: none; text-align: center; padding: var(--spacing-lg);">
                                    <h3>🚤 Bootbeheer</h3>
                                    <p>Beheer bootvoorraad en beschikbaarheid</p>
                                </a>
                                <a href="booking-management.php" class="btn-admin btn-export" style="text-decoration: none; text-align: center; padding: var(--spacing-lg);">
                                    <h3>📋 Reserveringsbeheer</h3>
                                    <p>Bekijk en beheer alle reserveringen</p>
                                </a>
                            </div>
                        </div>
                    </div>
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
                if (!response.ok) {
                    console.error('Session check failed: HTTP', response.status);
                    return false;
                }
                
                // Read response as text first to handle errors
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

        /**
         * One logical reservation per key (multi-day online bookings are one row; manual batches
         * created in the same save share createdAt and are deduped).
         */
        function bookingReservationKey(b) {
            const start = b.date || '';
            const end = b.endDate || b.date || '';
            const boat = b.boatType || b.boatId || '';
            const email = (b.customerEmail || '').toLowerCase().trim();
            const isManual = b.status === 'manual' || b.source === 'manual';
            if (isManual) {
                return 'manual:' + start + '|' + end + '|' + boat + '|' + email + '|' + (b.createdAt || '');
            }
            if (b.cartId) {
                return 'cart:' + b.cartId + '|' + boat + '|' + start + '|' + end;
            }
            return 'id:' + (b.id || start + '|' + end + '|' + boat);
        }

        function countUniqueReservationKeys(bookings, predicate) {
            const keys = new Set();
            for (let i = 0; i < bookings.length; i++) {
                const b = bookings[i];
                if (predicate && !predicate(b)) continue;
                keys.add(bookingReservationKey(b));
            }
            return keys.size;
        }

        function mergeBookingsByReservationKey(primary, secondary) {
            const merged = new Map();
            [...primary, ...secondary].forEach(booking => {
                merged.set(bookingReservationKey(booking), booking);
            });
            return Array.from(merged.values());
        }

        function isConfirmedDashboardStatus(b) {
            const s = b.status;
            return s === 'paid' || s === 'confirmed' || s === 'confirmed-paid' || s === 'confirmed-not-paid' || s === 'success';
        }

        function isCancelledDashboardStatus(b) {
            const s = b.status;
            return s === 'canceled' || s === 'cancelled' || s === 'payment-rejected';
        }

        function isManualDashboardStatus(b) {
            return b.status === 'manual' || b.source === 'manual';
        }

        /** Online checkout not finalized — do not count as a reservation. */
        function countsTowardTotalBookings(b) {
            const s = b.status;
            return !['not-confirmed', 'open', 'pending', 'canceled', 'cancelled', 'payment-rejected', 'failed', 'expired', 'rejected'].includes(s);
        }

        class StaticAdminSystem {
            constructor() {
                // Use an admin-only cache key so it doesn't get overwritten by public pages
                this.storageKey = 'nijenhuis_admin_bookings';
                this.bookings = [];
                this.init();
            }
            
            init() {
                this.checkAuth();
            }
            
            detectServerEndpoint() {
                return detectAdminEndpoint();
            }
            
            async checkAuth() {
                const isAuthenticated = await refreshAdminSession();
                if (!isAuthenticated) {
                    window.location.href = '../pages/admin-login.php';
                    return;
                }
                this.showAdminSection();
            }
            
            async showAdminSection() {
                document.getElementById('adminSection').classList.remove('hidden');
                await this.loadBookings();
                this.updateStats();
                this.updateMethodOverview();
            }
            
            async loadBookings() {
                try {
                    // Try to load from server first
                    const csrf = await getCsrfToken();
                    const endpoint = this.detectServerEndpoint();
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': csrf
                        },
                        body: JSON.stringify({ action: 'getBookings' })
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.csrfToken) {
                            adminSession.csrfToken = data.csrfToken;
                        }
                        const activeBookings = data.bookings || [];
                        const historyBookings = await this.loadHistoryBookings(endpoint);
                        this.bookings = mergeBookingsByReservationKey(activeBookings, historyBookings);
                        return;
                    }
                } catch (error) {
                    console.log('Server unavailable, loading from localStorage:', error);
                }
                
                // Fallback to localStorage if server unavailable
                try {
                    const stored = localStorage.getItem(this.storageKey);
                    this.bookings = stored ? JSON.parse(stored) : [];
                } catch (error) {
                    console.error('Error loading bookings from localStorage:', error);
                    this.bookings = [];
                }
            }

            async loadHistoryBookings(endpoint) {
                try {
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

                    if (!response.ok) return [];

                    const data = await response.json();
                    if (data.csrfToken) {
                        adminSession.csrfToken = data.csrfToken;
                    }
                    return Array.isArray(data.bookings) ? data.bookings : [];
                } catch (error) {
                    console.log('Unable to load archived bookings for dashboard overview:', error);
                    return [];
                }
            }
            
            updateStats() {
                const all = this.bookings;
                const total = countUniqueReservationKeys(all, countsTowardTotalBookings);
                const manual = countUniqueReservationKeys(all, isManualDashboardStatus);
                const confirmed = countUniqueReservationKeys(all, isConfirmedDashboardStatus);
                const cancelled = countUniqueReservationKeys(all, isCancelledDashboardStatus);

                document.getElementById('totalBookings').textContent = total;
                document.getElementById('manualBookings').textContent = manual;
                document.getElementById('confirmedBookings').textContent = confirmed;
                document.getElementById('cancelledBookings').textContent = cancelled;
            }

            updateMethodOverview() {
                const eligible = this.bookings.filter(countsTowardTotalBookings);

                const channelCounts = {};
                const paymentCounts = {};

                const channelLabels = {
                    online: 'Online',
                    cart: 'Online',
                    manual: 'Handmatig (Via telefoon)',
                    receptie: 'Receptie'
                };

                const paymentLabels = {
                    ideal: 'iDEAL',
                    creditcard: 'Credit / debitcard',
                    bancontact: 'Bancontact',
                    pay_on_arrival: 'Betalen bij aankomst'
                };

                for (const b of eligible) {
                    const qty = parseInt(b.quantity) || 1;

                    const src = (b.source || '').toLowerCase();
                    const channelLabel = channelLabels[src] || 'Overig';
                    channelCounts[channelLabel] = (channelCounts[channelLabel] || 0) + qty;

                    const pm = (b.paymentMethod || '').toLowerCase().trim();
                    const paymentLabel = pm ? (paymentLabels[pm] || pm) : 'Geen (handmatig)';
                    paymentCounts[paymentLabel] = (paymentCounts[paymentLabel] || 0) + qty;
                }

                const channelOrder = ['Online', 'Handmatig (Via telefoon)', 'Receptie', 'Overig'];
                const paymentOrder = ['iDEAL', 'Credit / debitcard', 'Bancontact', 'Betalen bij aankomst', 'Geen (handmatig)'];

                const renderTable = (tbody, counts, order) => {
                    const sorted = Object.keys(counts).sort((a, b) => {
                        const ia = order.indexOf(a);
                        const ib = order.indexOf(b);
                        return (ia === -1 ? 999 : ia) - (ib === -1 ? 999 : ib);
                    });
                    if (sorted.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="2" style="text-align:center; color:#999;">Geen gegevens</td></tr>';
                        return;
                    }
                    tbody.innerHTML = sorted.map(label =>
                        `<tr><td>${label}</td><td>${counts[label]}</td></tr>`
                    ).join('');
                };

                renderTable(document.getElementById('channelOverviewBody'), channelCounts, channelOrder);
                renderTable(document.getElementById('paymentOverviewBody'), paymentCounts, paymentOrder);
            }
            
            // Removed renderBookings - now handled in booking-management.php
            
            // Removed booking management functions - now handled in booking-management.php
        }
        
        // Global functions
        async function logout() {
            // Get token before clearing localStorage
            const csrf = await getCsrfToken();
            
            try {
                // Get endpoint from admin system instance
                const adminSystem = window.adminSystem;
                let endpoint = 'booking-handler.php';
                
                if (adminSystem && typeof adminSystem.detectServerEndpoint === 'function') {
                    endpoint = adminSystem.detectServerEndpoint();
                } else {
                    // Fallback endpoint detection
                    if (window.location.protocol === 'file:' || window.location.hostname === '') {
                        endpoint = 'http://localhost:8000/admin/booking-handler.py';
                    } else {
                        endpoint = `${window.location.origin}/admin/booking-handler.php`;
                    }
                }
                
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
                console.error('Logout server call error (continuing anyway):', error);
            }
            
            // Clear localStorage after server call attempt
            sessionStorage.removeItem('adminSessionToken');
            localStorage.removeItem('adminAuthenticated');
            localStorage.removeItem('adminUser');
            sessionStorage.removeItem('adminLoginTime');
            
            // Always redirect to login page
            window.location.href = '../pages/admin-login.php';
        }
        
        // Initialize the admin system
        const adminSystem = new StaticAdminSystem();
        // Make it globally accessible for logout function
        window.adminSystem = adminSystem;
    </script>
</body>
</html>
