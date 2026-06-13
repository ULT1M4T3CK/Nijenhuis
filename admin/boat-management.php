<?php
// Start session and verify admin authentication
require_once 'admin-auth.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootbeheer - Nijenhuis Botenverhuur</title>
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <link rel="stylesheet" href="admin-consolidated.css">
    <link rel="icon" type="image/svg+xml" href="../frontend/Images/logo-white.svg">
    <!-- Boat Data Service (Single Source of Truth) -->
    <script src="../js/boat-data-service.js"></script>
    <script src="../js/image-compress.js"></script>
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="admin-nav">
        <div class="admin-nav-container">
            <img src="../frontend/Images/logo-white.svg" alt="Nijenhuis" class="admin-nav-logo">
            <div class="admin-nav-links">
                <a href="admin-static.php" class="admin-nav-link">Dashboard</a>
                <a href="boat-management.php" class="admin-nav-link active">Bootbeheer</a>
                <a href="booking-management.php" class="admin-nav-link">Reserveringsbeheer</a>
                <a href="booking-history.php" class="admin-nav-link">Boekingsgeschiedenis</a>
                <a href="for-sale-management.php" class="admin-nav-link">Te koop</a>
                </div>
            <button class="admin-nav-logout" onclick="logout()">Uitloggen</button>
                </div>
    </nav>

    <!-- Admin Header Section -->
    <div class="admin-header">
        <div class="admin-container">
            <h1 class="admin-title">Bootbeheer</h1>
            <p class="admin-subtitle">Nijenhuis Botenverhuur - Beheer boten en beschikbaarheid</p>
        </div>
    </div>
    
    <div class="boat-management">
        <div class="management-container">
            <!-- Navigation Links -->
            <div class="nav-links-section">
                <div class="nav-links">
                    <a href="#" class="nav-link">Overzicht</a>
                    <span class="nav-separator">|</span>
                    <a href="#" class="nav-link" onclick="showAddBoatModal(); return false;">Nieuw item toevoegen.</a>
                    <span class="nav-separator">|</span>
                    <a href="#" class="nav-link">Volgorde aanpassen</a>
                </div>
            </div>
            
            <!-- Boat Grid -->
            <div class="boat-grid" id="boatGrid">
                <!-- Boats will be populated here -->
            </div>
        </div>
    </div>

    <!-- Add Boat Modal -->
    <div id="addBoatModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
            <div class="modal-header">
                <h2>Nieuwe boot toevoegen</h2>
                <span class="close" onclick="closeAddBoatModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addBoatForm">
                    <div class="edit-form-container" style="display: block;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-xl);">
                            <!-- Left Column - Images -->
                            <div class="image-section">
                                <div class="image-upload-group">
                                    <label class="image-upload-label">Header Afbeelding</label>
                                    <img id="addHeaderImagePreview" src="../frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg" alt="Header" class="image-display">
                                    <div class="file-input-wrapper">
                                        <label for="addHeaderImageInput" class="file-input-label">Bestand kiezen</label>
                                        <input type="file" id="addHeaderImageInput" class="file-input" accept="image/*">
                                        <span class="file-name" id="addHeaderImageName">Geen bestand gekozen</span>
                                    </div>
                                </div>
                                
                                <div class="image-upload-group">
                                    <label class="image-upload-label">Afbeelding</label>
                                    <img id="addMainImagePreview" src="../frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg" alt="Main" class="image-display">
                                    <div class="file-input-wrapper">
                                        <label for="addMainImageInput" class="file-input-label">Bestand kiezen</label>
                                        <input type="file" id="addMainImageInput" class="file-input" accept="image/*">
                                        <span class="file-name" id="addMainImageName">Geen bestand gekozen</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column - Form Fields -->
                            <div class="form-section">
                                <div class="form-group">
                                    <label for="addBoatName" class="form-label">Naam *</label>
                                    <input type="text" id="addBoatName" name="name" class="form-input" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="addBoatCategory" class="form-label">Categorie *</label>
                                    <select id="addBoatCategory" name="category" class="form-input" required>
                                        <option value="electric">Elektrische boten</option>
                                        <option value="sailing">Zeilboten</option>
                                        <option value="canoe">Kano's & Kajaks</option>
                                        <option value="sup">SUP boards</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="addBoatDeposit" class="form-label">Borg</label>
                                    <input type="number" id="addBoatDeposit" name="deposit" class="form-input" value="0">
                                </div>
                                
                                <div class="form-group">
                                    <label for="addBoatDescription" class="form-label">Omschrijving *</label>
                                    <textarea id="addBoatDescription" name="description" class="form-textarea" rows="4" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="addAvailableDaysSelect" class="form-label">Beschikbare huurdagen selecteren</label>
                                    <select id="addAvailableDaysSelect" name="availableDays" class="form-input">
                                        <option value="1">1 dag</option>
                                        <option value="2">2 dagen</option>
                                        <option value="3">3 dagen</option>
                                        <option value="4">4 dagen</option>
                                        <option value="5">5 dagen</option>
                                        <option value="6">6 dagen</option>
                                        <option value="7" selected>7 dagen</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Meerdaagse prijzen</label>
                                    <div id="addPricingGrid" class="pricing-grid">
                                        <!-- Dynamic pricing inputs will be generated here -->
                                    </div>
                                </div>
                                
                                <div class="form-group" id="addEnginePricingSection" style="display: none;">
                                    <label class="form-label">Meerdaagse prijzen (met motor)</label>
                                    <div id="addEnginePricingGrid" class="pricing-grid">
                                        <!-- Dynamic pricing inputs for engine will be generated here -->
                                    </div>
                                </div>
                                
                                <div class="info-grid">
                                    <div class="form-group">
                                        <label for="addPassengerCount" class="form-label">Aantal passagiers</label>
                                        <input type="text" id="addPassengerCount" name="passengerCount" class="form-input" placeholder="bijv. 1 persoon">
                                    </div>
                                    <div class="form-group">
                                        <label for="addBoatCount" class="form-label">Aantal boten *</label>
                                        <input type="number" id="addBoatCount" name="boatCount" class="form-input" value="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="addOrderId" class="form-label">Order_id</label>
                                        <input type="number" id="addOrderId" name="orderId" class="form-input" value="99">
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="button" class="btn btn-secondary" onclick="closeAddBoatModal()">Annuleren</button>
                                    <button type="submit" class="btn btn-primary">Boot toevoegen</button>
                                </div>
                            </div>
                        </div>
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

        async function parseJsonResponse(response) {
            const text = await response.text();
            if (!text || !text.trim()) {
                return null;
            }
            try {
                return JSON.parse(text);
            } catch (parseError) {
                console.warn('Invalid JSON response:', text.substring(0, 200));
                return null;
            }
        }

        function extractBoatsArray(data) {
            if (!data || !data.success) {
                return [];
            }
            if (Array.isArray(data.boats)) {
                return data.boats;
            }
            if (Array.isArray(data.data)) {
                return data.data;
            }
            return [];
        }

        class BoatManagementSystem {
            constructor() {
                this.boatsStorageKey = 'nijenhuis_boats';
                // Use an admin-only cache key so it doesn't get overwritten by public pages
                this.bookingsStorageKey = 'nijenhuis_admin_bookings';
                this.boats = [];
                this.bookings = [];
                this.init();
            }
            
            detectServerEndpoint() {
                return detectAdminEndpoint();
            }

            async fetchBoatsFromServer() {
                const endpoint = this.detectServerEndpoint();

                try {
                    const endpointUrl = new URL(endpoint, window.location.href);
                    const sameOriginHttp = (window.location.protocol === 'http:' || window.location.protocol === 'https:') &&
                        (endpointUrl.origin === window.location.origin);

                    // Public GET — reliable read path (no CSRF required)
                    const getController = new AbortController();
                    const getTimeoutId = setTimeout(() => getController.abort(), 5000);
                    const getRes = await fetch(`${endpoint}?action=boats`, {
                        method: 'GET',
                        credentials: sameOriginHttp ? 'include' : 'omit',
                        headers: { 'Accept': 'application/json' },
                        signal: getController.signal
                    });
                    clearTimeout(getTimeoutId);

                    if (getRes.ok) {
                        const getData = await parseJsonResponse(getRes);
                        const getBoats = extractBoatsArray(getData);
                        if (getBoats.length > 0) {
                            return getBoats;
                        }
                    }

                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 5000);

                    const csrf = await getCsrfToken();
                    const headers = {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    };
                    if (csrf) {
                        headers['X-CSRF-Token'] = csrf;
                    }

                    const res = await fetch(endpoint, {
                        method: 'POST',
                        headers,
                        credentials: sameOriginHttp ? 'include' : 'omit',
                        body: JSON.stringify({ action: 'getBoats', csrfToken: csrf }),
                        signal: controller.signal
                    });
                    clearTimeout(timeoutId);

                    if (res.ok) {
                        const data = await parseJsonResponse(res);
                        const boats = extractBoatsArray(data);
                        if (boats.length > 0) {
                            return boats;
                        }
                    } else {
                        console.warn('Failed to fetch boats via POST:', res.status, res.statusText);
                    }
                } catch (e) {
                    if (e.name !== 'AbortError') {
                        console.error('Server fetch failed:', e);
                    }
                }
                return [];
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
                
                // Load from server first, then fallback to localStorage/defaults
                try {
                    const serverBoats = await this.fetchBoatsFromServer();
                    if (serverBoats && serverBoats.length > 0) {
                        this.boats = serverBoats;
                        localStorage.setItem(this.boatsStorageKey, JSON.stringify(this.boats));
                        console.log('Loaded', this.boats.length, 'boats from server');
                    } else {
                        // Fallback to localStorage or defaults
                        this.loadData();
                        if (!localStorage.getItem(this.boatsStorageKey) || this.boats.length === 0) {
                            console.log('No boats found, initializing with defaults...');
                            this.boats = this.getDefaultBoats();
                            await this.saveData();
                        }
                    }
                } catch (error) {
                    console.error('Error loading boats from server:', error);
                    // Fallback to localStorage or defaults
                    this.loadData();
                    if (!localStorage.getItem(this.boatsStorageKey) || this.boats.length === 0) {
                        console.log('No boats found, initializing with defaults...');
                        this.boats = this.getDefaultBoats();
                        await this.saveData();
                    }
                }
                
                this.setupEventListeners();
                this.renderBoats();
                this.testImageLoading();
            }
            
            testImageLoading() {
                console.log('Testing image loading...');
                this.boats.forEach(boat => {
                    const img = new Image();
                    img.onload = () => console.log(`✓ Image loaded: ${boat.image}`);
                    img.onerror = () => console.log(`✗ Image failed: ${boat.image}`);
                    img.src = boat.image;
                });
            }
            
            loadData() {
                // Load boats data
                const storedBoats = localStorage.getItem(this.boatsStorageKey);
                this.boats = storedBoats ? JSON.parse(storedBoats) : this.getDefaultBoats();
                
                // Load bookings data
                const storedBookings = localStorage.getItem(this.bookingsStorageKey);
                this.bookings = storedBookings ? JSON.parse(storedBookings) : [];
            }
            
            // Use centralized boat data from BoatDataService (../js/boat-data-service.js)
            getDefaultBoats() {
                return window.BoatDataService ? window.BoatDataService.getDefaultBoats() : [];
            }
            
            setupEventListeners() {
                // Setup calendar event listeners
                const calendarInput = document.getElementById('boatCalendar');
                if (calendarInput) {
                    calendarInput.addEventListener('change', () => {
                        this.filterBoatsByDate();
                    });
                }
            }
            
            async saveData() {
                try {
                    await this.pushBoatsToServer();

                    localStorage.setItem(this.boatsStorageKey, JSON.stringify(this.boats));
                    localStorage.setItem(this.bookingsStorageKey, JSON.stringify(this.bookings));
                    
                    window.dispatchEvent(new CustomEvent('boatsUpdated', { detail: this.boats }));
                    window.dispatchEvent(new CustomEvent('boatsStorageUpdated'));
                    
                    const currentValue = localStorage.getItem(this.boatsStorageKey);
                    localStorage.removeItem(this.boatsStorageKey);
                    localStorage.setItem(this.boatsStorageKey, currentValue);
                    
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('boatsUpdated', { detail: this.boats }));
                        window.dispatchEvent(new CustomEvent('boatsStorageUpdated'));
                    }, 50);
                    
                    console.log('Boats saved successfully. Total boats:', this.boats.length);
                    return true;
                } catch (e) {
                    console.error('Failed to save boats:', e);
                    try {
                        const serverBoats = await this.fetchBoatsFromServer();
                        if (serverBoats && serverBoats.length > 0) {
                            this.boats = serverBoats;
                            localStorage.setItem(this.boatsStorageKey, JSON.stringify(this.boats));
                        }
                    } catch (reloadError) {
                        console.error('Failed to reload boats from server after save error:', reloadError);
                    }
                    this.renderBoats();
                    this.renderRealTimeBoats();
                    this.showNotification(
                        'Opslaan mislukt — wijzigingen zijn niet opgeslagen. ' + (e.message || 'Probeer opnieuw in te loggen.'),
                        'error'
                    );
                    return false;
                }
            }
            
            async pushBoatsToServer() {
                try {
                    // Only use Python handler for file:// protocol (opening HTML files directly)
                    // When served via any web server (including localhost PHP), use PHP handler
                    const isFileProtocol = window.location.protocol === 'file:' || window.location.hostname === '';
                    const endpoint = isFileProtocol
                        ? 'http://localhost:8000/admin/booking-handler.py'
                        : `${window.location.origin}/admin/booking-handler.php`;
                    
                    // CSRF is required for authenticated admin actions (PHP handler)
                    const csrf = await getCsrfToken();
                    const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
                    if (csrf) {
                        headers['X-CSRF-Token'] = csrf;
                    }
                    
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers,
                        credentials: 'include',
                        body: JSON.stringify({ action: 'saveBoats', boats: this.boats, csrfToken: csrf })
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        if (result.success) {
                            console.log(`✅ Successfully saved ${this.boats.length} boats to server`);
                            this.showNotification('Boten succesvol opgeslagen naar server', 'success');
                            return;
                        }
                        throw new Error(result.message || 'Server returned error');
                    }

                    let errorMessage = `HTTP ${response.status}`;
                    try {
                        const result = await response.json();
                        errorMessage = result.message || errorMessage;
                    } catch (parseError) {
                        // ignore non-JSON error bodies
                    }
                    throw new Error(errorMessage);
                } catch (e) {
                    console.warn('Failed to push boats to server:', e);
                    throw e;
                }
            }
            
            renderBoats() {
                const boatGrid = document.getElementById('boatGrid');
                if (!boatGrid) return;
                
                boatGrid.innerHTML = '';
                
                // Sort boats by category and orderId
                const categoryOrder = ['electric', 'sailing', 'canoe', 'sup'];
                const sortedBoats = [...this.boats].sort((a, b) => {
                    const categoryA = categoryOrder.indexOf(a.category || 'other');
                    const categoryB = categoryOrder.indexOf(b.category || 'other');
                    
                    if (categoryA !== categoryB) {
                        return categoryA - categoryB;
                    }
                    
                    return (a.orderId || 999) - (b.orderId || 999);
                });
                
                let currentCategory = null;
                
                sortedBoats.forEach(boat => {
                    // Add category header if category changed
                    if (boat.category && boat.category !== currentCategory) {
                        currentCategory = boat.category;
                        const categoryHeader = document.createElement('div');
                        categoryHeader.className = 'boat-category-header';
                        categoryHeader.style.cssText = 'grid-column: 1 / -1; font-size: var(--font-size-lg); font-weight: 600; color: var(--secondary-color); padding: var(--spacing-lg) 0 var(--spacing-md) 0; border-bottom: 2px solid var(--light-gray); margin-top: var(--spacing-lg);';
                        
                        const categoryNames = {
                            electric: '🚤 Elektrische boten',
                            sailing: '⛵ Zeilboten',
                            canoe: '🛶 Kano\'s & Kajaks',
                            sup: '🏄 SUP boards'
                        };
                        
                        categoryHeader.textContent = categoryNames[currentCategory] || currentCategory;
                        
                        if (currentCategory === 'electric') {
                            categoryHeader.style.marginTop = '0';
                        }
                        
                        boatGrid.appendChild(categoryHeader);
                    }
                    // Escape user input to prevent XSS
                    const safeImage = this.escapeHTML(boat.image || '');
                    const safeName = this.escapeHTML(boat.name || '');
                    const safeId = this.escapeHTML(boat.id || '');
                    
                    const boatCard = document.createElement('div');
                    boatCard.className = 'boat-card';
                    
                    // Image
                    const img = document.createElement('img');
                    img.src = safeImage;
                    img.alt = safeName;
                    img.className = 'boat-card-image';
                    img.onerror = function() {
                        this.onerror = null;
                        this.src = '../frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg';
                        console.log('Image failed to load:', safeImage);
                    };
                    
                    // Content
                    const contentDiv = document.createElement('div');
                    contentDiv.className = 'boat-card-content';
                    
                    const titleDiv = document.createElement('div');
                    titleDiv.className = 'boat-card-title';
                    titleDiv.textContent = safeName;
                    
                    // Actions
                    const actionsDiv = document.createElement('div');
                    actionsDiv.className = 'boat-card-actions';
                    
                    const fotoLink = document.createElement('a');
                    fotoLink.href = '#';
                    fotoLink.className = 'boat-action-link';
                    fotoLink.textContent = "foto's";
                    fotoLink.onclick = (e) => {
                        e.preventDefault();
                        showBoatPhotos(safeId);
                    };
                    
                    const editLink = document.createElement('a');
                    editLink.href = `boat-edit.php?id=${safeId}`;
                    editLink.className = 'boat-action-link';
                    editLink.textContent = 'bewerk';
                    
                    const deleteLink = document.createElement('a');
                    deleteLink.href = '#';
                    deleteLink.className = 'boat-action-link';
                    deleteLink.textContent = 'verwijder';
                    deleteLink.onclick = (e) => {
                        e.preventDefault();
                        if (confirm(`Weet je zeker dat je "${safeName}" wilt verwijderen?`)) {
                            this.deleteBoat(safeId);
                        }
                    };
                    
                    actionsDiv.appendChild(fotoLink);
                    actionsDiv.appendChild(editLink);
                    actionsDiv.appendChild(deleteLink);
                    
                    contentDiv.appendChild(titleDiv);
                    contentDiv.appendChild(actionsDiv);
                    
                    boatCard.appendChild(img);
                    boatCard.appendChild(contentDiv);
                    
                    boatGrid.appendChild(boatCard);
                });
            }
            
            async deleteBoat(boatId) {
                this.boats = this.boats.filter(b => b.id !== boatId);
                const saved = await this.saveData();
                if (saved) {
                    this.renderBoats();
                }
            }
            
            renderRealTimeBoats() {
                const realtimeBoats = document.getElementById('realtimeBoats');
                if (!realtimeBoats) {
                    console.error('Real-time boats container not found');
                    return;
                }
                
                realtimeBoats.innerHTML = '';
                
                if (this.boats.length === 0) {
                    const emptyMsg = document.createElement('p');
                    emptyMsg.style.cssText = 'text-align: center; color: var(--text-secondary); padding: var(--spacing-lg);';
                    emptyMsg.textContent = 'No boats available. Please add boats to inventory.';
                    realtimeBoats.appendChild(emptyMsg);
                    return;
                }
                
                this.boats.forEach(boat => {
                    const isAvailable = boat.available > 0;
                    const boatCard = document.createElement('div');
                    boatCard.className = `realtime-boat-card ${isAvailable ? 'available' : 'unavailable'}`;
                    
                    // Escape user input
                    const safeName = this.escapeHTML(boat.name || '');
                    
                    const nameDiv = document.createElement('div');
                    nameDiv.className = 'boat-name';
                    nameDiv.textContent = safeName;
                    
                    const availDiv = document.createElement('div');
                    availDiv.className = `boat-availability ${isAvailable ? 'available' : 'unavailable'}`;
                    availDiv.textContent = boat.available;
                    
                    const labelDiv = document.createElement('div');
                    labelDiv.className = 'boat-label';
                    labelDiv.textContent = 'Available';
                    
                    const totalDiv = document.createElement('div');
                    totalDiv.className = 'boat-total';
                    totalDiv.textContent = `/ ${boat.total} total`;
                    
                    boatCard.appendChild(nameDiv);
                    boatCard.appendChild(availDiv);
                    boatCard.appendChild(labelDiv);
                    boatCard.appendChild(totalDiv);
                    
                    realtimeBoats.appendChild(boatCard);
                });
                
                // Update last update time
                const lastUpdate = document.getElementById('lastUpdate');
                if (lastUpdate) {
                    lastUpdate.textContent = `Laatst bijgewerkt: ${new Date().toLocaleTimeString('nl-NL')}`;
                }
            }
            
            // Helper function to escape HTML and prevent XSS
            escapeHTML(text) {
                if (text == null) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            // Show notification to user
            showNotification(message, type = 'info') {
                // Remove existing notifications
                const existingNotification = document.querySelector('.admin-notification');
                if (existingNotification) {
                    existingNotification.remove();
                }
                
                const notification = document.createElement('div');
                notification.className = `admin-notification notification-${type}`;
                notification.textContent = message;
                notification.style.cssText = `
                    position: fixed;
                    top: 80px;
                    right: 20px;
                    padding: 1rem 1.5rem;
                    border-radius: 8px;
                    color: white;
                    font-weight: 500;
                    z-index: 9999;
                    animation: slideIn 0.3s ease-out;
                    max-width: 400px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                `;
                
                // Set color based on type
                const colors = {
                    success: '#28a745',
                    warning: '#ffc107',
                    error: '#dc3545',
                    info: '#17a2b8'
                };
                notification.style.backgroundColor = colors[type] || colors.info;
                if (type === 'warning') {
                    notification.style.color = '#333';
                }
                
                document.body.appendChild(notification);
                
                // Auto-remove after 5 seconds
                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease-in';
                    setTimeout(() => notification.remove(), 300);
                }, 5000);
            }
            
            renderBookings() {
                const bookingsList = document.getElementById('bookingsList');
                bookingsList.innerHTML = '';
                
                // One entry per boat: expand any booking with quantity > 1 (existing reservations)
                const expanded = [];
                this.bookings.forEach(booking => {
                    const qty = parseInt(booking.quantity) || 1;
                    if (qty <= 1) {
                        expanded.push(booking);
                    } else {
                        for (let i = 0; i < qty; i++) {
                            const entry = Object.assign({}, booking);
                            entry._expandedIndex = i + 1;
                            entry._expandedTotal = qty;
                            expanded.push(entry);
                        }
                    }
                });
                
                // Sort bookings by date (newest first)
                const sortedBookings = expanded.sort((a, b) => new Date(b.date) - new Date(a.date));
                
                sortedBookings.forEach(booking => {
                    const bookingItem = document.createElement('div');
                    bookingItem.className = `booking-item ${this.escapeHTML(booking.status)}`;
                    
                    let boatLabel = this.getBoatName(booking.boatType);
                    const boatMeta = this.boats.find(b => b.id === booking.boatType);
                    if (booking.engineOption === 'with' && boatMeta && boatMeta.pricingWithEngine && Array.isArray(boatMeta.pricingWithEngine) && boatMeta.pricingWithEngine.length > 0) {
                        boatLabel += ' + motor';
                    }
                    if (booking._expandedTotal > 1) boatLabel += ` (${booking._expandedIndex}/${booking._expandedTotal})`;
                    
                    // Escape all user input to prevent XSS
                    const safeStatus = this.escapeHTML(booking.status);
                    const safeCustomerName = this.escapeHTML(booking.customerName || '');
                    const safeDate = this.escapeHTML(booking.date || '');
                    const safeBoatName = this.escapeHTML(boatLabel);
                    const safeEmail = this.escapeHTML(booking.customerEmail || '');
                    const safePhone = this.escapeHTML(booking.customerPhone || '');
                    const safeNotes = booking.notes ? this.escapeHTML(booking.notes) : '';
                    const safeId = this.escapeHTML(booking.id || '');
                    
                    // Use DOM manipulation instead of innerHTML for safer rendering
                    const statusDiv = document.createElement('div');
                    statusDiv.className = `booking-status status-${safeStatus}`;
                    statusDiv.textContent = safeStatus;
                    
                    const infoDiv = document.createElement('div');
                    infoDiv.className = 'booking-info';
                    
                    const nameH4 = document.createElement('h4');
                    nameH4.textContent = safeCustomerName;
                    
                    const dateP = document.createElement('p');
                    dateP.innerHTML = `<strong>Date:</strong> ${safeDate}`;
                    
                    const boatP = document.createElement('p');
                    boatP.innerHTML = `<strong>Boat:</strong> ${safeBoatName}`;
                    
                    const contactP = document.createElement('p');
                    contactP.innerHTML = `<strong>Contact:</strong> ${safeEmail} | ${safePhone}`;
                    
                    infoDiv.appendChild(nameH4);
                    infoDiv.appendChild(dateP);
                    infoDiv.appendChild(boatP);
                    infoDiv.appendChild(contactP);
                    
                    if (safeNotes) {
                        const notesP = document.createElement('p');
                        notesP.innerHTML = `<strong>Notes:</strong> ${safeNotes}`;
                        infoDiv.appendChild(notesP);
                    }
                    
                    const statusSelect = document.createElement('select');
                    statusSelect.className = 'status-select';
                    statusSelect.id = `status-${safeId}`;
                    statusSelect.addEventListener('change', (e) => {
                        boatSystem.updateBookingStatus(safeId, e.target.value);
                    });
                    
                    const options = [
                        { value: 'manual', label: 'Manual' },
                        { value: 'success', label: 'Success' },
                        { value: 'rejected', label: 'Rejected' },
                        { value: 'picked-up', label: 'Picked Up' }
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
                    saveBtn.textContent = 'Save';
                    saveBtn.addEventListener('click', () => {
                        boatSystem.saveBookingStatus(safeId);
                    });
                    
                    bookingItem.appendChild(statusDiv);
                    bookingItem.appendChild(infoDiv);
                    bookingItem.appendChild(statusSelect);
                    bookingItem.appendChild(saveBtn);
                    
                    bookingsList.appendChild(bookingItem);
                });
            }
            
            getBoatName(boatId) {
                const boat = this.boats.find(b => b.id === boatId);
                return boat ? boat.name : boatId;
            }
            
            async updateBoatAvailability(boatId) {
                const input = document.getElementById(`available-${boatId}`);
                const newAvailable = parseInt(input.value);
                const boat = this.boats.find(b => b.id === boatId);
                
                if (boat && newAvailable >= 0) {
                    const previousTotal = boat.total;
                    const previousAvailable = boat.available;
                    if (newAvailable > boat.total) {
                        boat.total = newAvailable;
                    }
                    boat.available = newAvailable;
                    const saved = await this.saveData();
                    if (!saved) {
                        boat.total = previousTotal;
                        boat.available = previousAvailable;
                        input.value = previousAvailable;
                        return;
                    }
                    this.renderBoats();
                    this.renderRealTimeBoats();
                    this.filterBoatsByDate();
                    
                    // Show success feedback
                    const button = input.nextElementSibling;
                    const originalText = button.textContent;
                    button.textContent = 'Updated!';
                    button.style.background = '#28a745';
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.style.background = '';
                    }, 2000);
                } else {
                    alert('Invalid availability number. Please enter a number greater than or equal to 0.');
                    input.value = boat.available;
                }
            }
            
            filterBoatsByDate() {
                const selectedDate = document.getElementById('boatCalendar').value;
                if (!selectedDate) {
                    this.renderRealTimeBoats();
                    return;
                }
                
                // Get bookings for selected date
                const bookingsForDate = this.bookings.filter(booking => 
                    booking.date === selectedDate && booking.status === 'success'
                );
                
                // Calculate availability for selected date
                const boatsWithAvailability = this.boats.map(boat => {
                    const bookingsForBoat = bookingsForDate.filter(booking => 
                        booking.boatType === boat.id
                    );
                    const bookedCount = bookingsForBoat.length;
                    const availableForDate = Math.max(0, boat.available - bookedCount);
                    
                    return {
                        ...boat,
                        availableForDate,
                        bookedForDate: bookedCount
                    };
                });
                
                this.renderRealTimeBoatsForDate(boatsWithAvailability, selectedDate);
            }
            
            renderRealTimeBoatsForDate(boatsWithAvailability, selectedDate) {
                const realtimeBoats = document.getElementById('realtimeBoats');
                realtimeBoats.innerHTML = '';
                
                boatsWithAvailability.forEach(boat => {
                    const isAvailable = boat.availableForDate > 0;
                    const boatCard = document.createElement('div');
                    boatCard.className = `realtime-boat-card ${isAvailable ? 'available' : 'unavailable'}`;
                    
                    // Escape user input
                    const safeName = this.escapeHTML(boat.name || '');
                    
                    const nameDiv = document.createElement('div');
                    nameDiv.className = 'boat-name';
                    nameDiv.textContent = safeName;
                    
                    const availDiv = document.createElement('div');
                    availDiv.className = `boat-availability ${isAvailable ? 'available' : 'unavailable'}`;
                    availDiv.textContent = boat.availableForDate;
                    
                    const labelDiv = document.createElement('div');
                    labelDiv.className = 'boat-label';
                    labelDiv.textContent = 'Available';
                    
                    const bookedDiv = document.createElement('div');
                    bookedDiv.className = 'boat-booked';
                    bookedDiv.textContent = `${boat.bookedForDate} booked`;
                    
                    boatCard.appendChild(nameDiv);
                    boatCard.appendChild(availDiv);
                    boatCard.appendChild(labelDiv);
                    boatCard.appendChild(bookedDiv);
                    
                    realtimeBoats.appendChild(boatCard);
                });
                
                // Update last update time
                const lastUpdate = document.getElementById('lastUpdate');
                if (lastUpdate) {
                    lastUpdate.textContent = `Beschikbaarheid voor ${new Date(selectedDate).toLocaleDateString('nl-NL')}`;
                }
            }
            
            async handleBoatRental(boatType, date) {
                const boat = this.boats.find(b => b.id === boatType);
                if (boat && boat.available > 0) {
                    const previousAvailable = boat.available;
                    boat.available--;
                    const saved = await this.saveData();
                    if (!saved) {
                        boat.available = previousAvailable;
                        return;
                    }
                    this.renderRealTimeBoats();
                    this.renderBoats();
                    this.filterBoatsByDate();
                    console.log(`Boat ${boat.name} rented. Available: ${boat.available}/${boat.total}`);
                }
            }
            
            startRealTimeUpdates() {
                this.renderRealTimeBoats();
                
                // Listen for storage changes from other tabs/pages
                window.addEventListener('storage', (e) => {
                    if (e.key === this.boatsStorageKey || e.key === this.bookingsStorageKey) {
                        this.loadData();
                        this.renderRealTimeBoats();
                        this.renderBoats();
                        this.filterBoatsByDate(); // Update calendar view if date is selected
                    }
                });
                
                // Also listen for custom events
                window.addEventListener('bookingStatusChanged', () => {
                    this.loadData();
                    this.renderRealTimeBoats();
                    this.renderBoats();
                    this.filterBoatsByDate();
                });
                
                // Update every 30 seconds
                setInterval(() => {
                    this.loadData(); // Reload data to get latest changes
                    this.renderRealTimeBoats();
                }, 30000);
            }
            
        }
        
        // Global functions
        async function logout() {
            const csrf = await getCsrfToken();
            
            try {
                const isLocal = window.location.protocol === 'file:' || 
                               window.location.hostname === 'localhost' || 
                               window.location.hostname === '127.0.0.1' ||
                               window.location.hostname === '';
                const endpoint = isLocal 
                    ? 'http://localhost:8000/admin/booking-handler.py'
                    : `${window.location.origin}/admin/booking-handler.php`;
                
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
            
            sessionStorage.removeItem('adminSessionToken');
            localStorage.removeItem('adminAuthenticated');
            localStorage.removeItem('adminUser');
            sessionStorage.removeItem('adminLoginTime');
            sessionStorage.removeItem('csrfToken');
            window.location.href = '../pages/admin-login.php';
        }
        
        function showBoatPhotos(boatId) {
            const boat = boatSystem.boats.find(b => b.id === boatId);
            if (!boat) {
                alert('Boot niet gevonden.');
                return;
            }
            
            // Create photo management modal
            const modal = document.createElement('div');
            modal.id = 'photoModal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
            `;
            
            modal.innerHTML = `
                <div style="background: white; border-radius: 8px; padding: 2rem; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2 style="margin: 0; color: var(--primary-color);">Foto's beheren: ${boatSystem.escapeHTML(boat.name)}</h2>
                        <button onclick="closePhotoModal()" style="background: none; border: none; font-size: 2rem; cursor: pointer; color: var(--text-secondary);">&times;</button>
                    </div>
                    
                    <!-- Current Photos -->
                    <div id="currentPhotos" style="margin-bottom: 2rem;">
                        <h3 style="margin-bottom: 1rem;">Huidige foto's</h3>
                        <div id="photosGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;"></div>
                    </div>
                    
                    <!-- Upload New Photo -->
                    <div style="border-top: 2px solid var(--light-gray); padding-top: 1.5rem;">
                        <h3 style="margin-bottom: 0.5rem;">Nieuwe foto's uploaden</h3>
                        <p style="margin: 0 0 1rem; font-size: 0.9rem; color: var(--text-secondary);">Selecteer meerdere bestanden tegelijk met Ctrl/Cmd-klik, of sleep ze naar het vak hieronder.</p>
                        <div id="photoDropZone" style="border: 2px dashed var(--light-gray); border-radius: 8px; padding: 1.5rem; text-align: center; cursor: pointer; transition: border-color 0.2s, background 0.2s; margin-bottom: 1rem;"
                             ondragover="event.preventDefault(); this.style.borderColor='var(--primary-color)'; this.style.background='rgba(0,113,187,0.05)';"
                             ondragleave="this.style.borderColor='var(--light-gray)'; this.style.background='transparent';"
                             ondrop="event.preventDefault(); this.style.borderColor='var(--light-gray)'; this.style.background='transparent'; document.getElementById('photoUpload').files = event.dataTransfer.files; uploadBoatPhotos('${boatSystem.escapeHTML(boatId)}');"
                             onclick="document.getElementById('photoUpload').click();">
                            <p style="margin: 0 0 0.5rem; font-weight: 600; color: var(--text-secondary);">Sleep foto's hierheen</p>
                            <p style="margin: 0; font-size: 0.85rem; color: #999;">of klik om bestanden te kiezen</p>
                            <input type="file" id="photoUpload" accept="image/*" multiple style="display: none;" onchange="uploadBoatPhotos('${boatSystem.escapeHTML(boatId)}')">
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            loadBoatPhotos(boatId);
        }
        
        function closePhotoModal() {
            const modal = document.getElementById('photoModal');
            if (modal) {
                modal.remove();
            }
        }
        
        function loadBoatPhotos(boatId) {
            const boat = boatSystem.boats.find(b => b.id === boatId);
            if (!boat) return;
            
            const photosGrid = document.getElementById('photosGrid');
            if (!photosGrid) return;
            
            photosGrid.innerHTML = '';
            
            // Get photos from boat data (stored in localStorage)
            const boatPhotos = boat.photos || [];
            
            if (boatPhotos.length === 0) {
                photosGrid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: var(--text-secondary);">Geen foto\'s beschikbaar.</p>';
                return;
            }
            
            boatPhotos.forEach((photo, index) => {
                const photoDiv = document.createElement('div');
                photoDiv.style.cssText = 'position: relative; border: 1px solid var(--light-gray); border-radius: 4px; overflow: hidden;';
                
                const img = document.createElement('img');
                img.src = photo.url || photo;
                img.style.cssText = 'width: 100%; height: 150px; object-fit: cover; display: block;';
                img.onerror = function() {
                    this.src = '../frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg';
                };
                
                const deleteBtn = document.createElement('button');
                deleteBtn.textContent = '×';
                deleteBtn.style.cssText = `
                    position: absolute;
                    top: 5px;
                    right: 5px;
                    background: var(--error-color);
                    color: white;
                    border: none;
                    width: 30px;
                    height: 30px;
                    border-radius: 50%;
                    cursor: pointer;
                    font-size: 1.2rem;
                    font-weight: bold;
                `;
                deleteBtn.onclick = () => deleteBoatPhoto(boatId, index);
                
                photoDiv.appendChild(img);
                photoDiv.appendChild(deleteBtn);
                photosGrid.appendChild(photoDiv);
            });
        }
        
        async function uploadBoatPhotos(boatId) {
            const input = document.getElementById('photoUpload');
            if (!input || !input.files || input.files.length === 0) {
                alert('Selecteer eerst een of meerdere foto\'s.');
                return;
            }
            
            const boat = boatSystem.boats.find(b => b.id === boatId);
            if (!boat) return;
            
            const files = Array.from(input.files).filter(f => f.type.startsWith('image/'));
            const photos = boat.photos || [];
            input.value = '';
            
            for (const file of files) {
                try {
                    const dataUrl = typeof compressToDataURL === 'function'
                        ? await compressToDataURL(file)
                        : await new Promise((resolve, reject) => {
                            const reader = new FileReader();
                            reader.onload = () => resolve(reader.result);
                            reader.onerror = () => reject(reader.error);
                            reader.readAsDataURL(file);
                        });
                    photos.push({
                        url: dataUrl,
                        name: file.name,
                        uploadedAt: new Date().toISOString()
                    });
                } catch (err) {
                    console.error('Photo compress/read failed', err);
                }
            }
            
            boat.photos = photos;
            const saved = await boatSystem.saveData();
            if (saved) {
                loadBoatPhotos(boatId);
            }
        }
        
        async function deleteBoatPhoto(boatId, photoIndex) {
            if (!confirm('Weet je zeker dat je deze foto wilt verwijderen?')) {
                return;
            }
            
            const boat = boatSystem.boats.find(b => b.id === boatId);
            if (!boat) return;
            
            const photos = boat.photos || [];
            photos.splice(photoIndex, 1);
            boat.photos = photos;
            
            const saved = await boatSystem.saveData();
            if (saved) {
                loadBoatPhotos(boatId);
            }
        }
        
        function showAddBoatModal() {
            const modal = document.getElementById('addBoatModal');
            if (modal) {
                modal.style.display = 'block';
                // Reset form
                document.getElementById('addBoatForm').reset();
                document.getElementById('addHeaderImagePreview').src = '../frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg';
                document.getElementById('addMainImagePreview').src = '../frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg';
                document.getElementById('addHeaderImageName').textContent = 'Geen bestand gekozen';
                document.getElementById('addMainImageName').textContent = 'Geen bestand gekozen';
                addHeaderImageData = null;
                addMainImageData = null;
                
                // Reset day selection to 7 days
                const addDaysSelect = document.getElementById('addAvailableDaysSelect');
                if (addDaysSelect) {
                    addDaysSelect.value = '7';
                }
                
                // Hide engine pricing section initially
                document.getElementById('addEnginePricingSection').style.display = 'none';
                
                // Regenerate pricing inputs
                updatePricingInputs();
            }
        }
        
        function closeAddBoatModal() {
            const modal = document.getElementById('addBoatModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
        
        let addHeaderImageData = null;
        let addMainImageData = null;
        
        // Function to generate dynamic pricing inputs
        function generatePricingInputs(containerId, prefix, selectedDays) {
            const container = document.getElementById(containerId);
            if (!container) return;
            
            container.innerHTML = '';
            
            selectedDays.forEach(day => {
                const pricingItem = document.createElement('div');
                pricingItem.className = 'pricing-item';
                
                const label = document.createElement('label');
                label.className = 'pricing-label';
                label.textContent = `Prijs ${day} ${day === 1 ? 'dag' : 'dagen'}`;
                label.setAttribute('for', `${prefix}Price${day}Days`);
                
                const input = document.createElement('input');
                input.type = 'number';
                input.id = `${prefix}Price${day}Days`;
                input.name = `price${day}Days`;
                input.className = 'pricing-input';
                input.value = '0';
                input.min = '0';
                input.step = '0.01';
                
                pricingItem.appendChild(label);
                pricingItem.appendChild(input);
                container.appendChild(pricingItem);
            });
        }
        
        /** Slug from add-boat name (must match submit-time id for engine fields) */
        function addBoatSlugFromName(name) {
            return (name || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        }
        /** Only Zeilboot (id sailboat-4-5) has optional outboard pricing */
        function addFormEnginePricingEligible() {
            const category = document.getElementById('addBoatCategory')?.value;
            return category === 'sailing' && addBoatSlugFromName(document.getElementById('addBoatName')?.value) === 'sailboat-4-5';
        }

        // Function to update pricing inputs based on selected days
        function updatePricingInputs() {
            const addDaysSelect = document.getElementById('addAvailableDaysSelect');
            const maxDays = parseInt(addDaysSelect.value) || 1;
            const selectedDays = Array.from({ length: maxDays }, (_, i) => i + 1);
            
            generatePricingInputs('addPricingGrid', 'add', selectedDays);
            
            if (addFormEnginePricingEligible()) {
                generatePricingInputs('addEnginePricingGrid', 'addEngine', selectedDays);
            }
        }
        
        // Image preview handlers for add modal
        document.addEventListener('DOMContentLoaded', function() {
            const addHeaderInput = document.getElementById('addHeaderImageInput');
            const addMainInput = document.getElementById('addMainImageInput');
            
            if (addHeaderInput) {
                addHeaderInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        (async function () {
                            try {
                                const dataUrl = typeof compressToDataURL === 'function'
                                    ? await compressToDataURL(file)
                                    : await new Promise((resolve, reject) => {
                                        const reader = new FileReader();
                                        reader.onload = () => resolve(reader.result);
                                        reader.onerror = () => reject(reader.error);
                                        reader.readAsDataURL(file);
                                    });
                                addHeaderImageData = dataUrl;
                                document.getElementById('addHeaderImagePreview').src = addHeaderImageData;
                                document.getElementById('addHeaderImageName').textContent = file.name;
                            } catch (err) {
                                console.error(err);
                            }
                        })();
                    }
                });
            }
            
            if (addMainInput) {
                addMainInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        (async function () {
                            try {
                                const dataUrl = typeof compressToDataURL === 'function'
                                    ? await compressToDataURL(file)
                                    : await new Promise((resolve, reject) => {
                                        const reader = new FileReader();
                                        reader.onload = () => resolve(reader.result);
                                        reader.onerror = () => reject(reader.error);
                                        reader.readAsDataURL(file);
                                    });
                                addMainImageData = dataUrl;
                                document.getElementById('addMainImagePreview').src = addMainImageData;
                                document.getElementById('addMainImageName').textContent = file.name;
                            } catch (err) {
                                console.error(err);
                            }
                        })();
                    }
                });
            }
            
            // Day selection change handlers
            const addDaysSelect = document.getElementById('addAvailableDaysSelect');
            if (addDaysSelect) {
                addDaysSelect.addEventListener('change', updatePricingInputs);
            }
            
            // Category / name: engine block only when slug would be sailboat-4-5
            const categorySelect = document.getElementById('addBoatCategory');
            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    const engineSection = document.getElementById('addEnginePricingSection');
                    if (addFormEnginePricingEligible()) {
                        engineSection.style.display = 'block';
                        updatePricingInputs();
                    } else {
                        engineSection.style.display = 'none';
                        updatePricingInputs();
                    }
                });
            }
            
            const boatNameInput = document.getElementById('addBoatName');
            if (boatNameInput) {
                boatNameInput.addEventListener('input', function() {
                    const engineSection = document.getElementById('addEnginePricingSection');
                    if (addFormEnginePricingEligible()) {
                        engineSection.style.display = 'block';
                        updatePricingInputs();
                    } else {
                        engineSection.style.display = 'none';
                        updatePricingInputs();
                    }
                });
            }
            
            // Initialize pricing inputs on page load
            updatePricingInputs();
            
            // Add boat form submission
            const addBoatForm = document.getElementById('addBoatForm');
            if (addBoatForm) {
                addBoatForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const boatName = document.getElementById('addBoatName').value;
                    if (!boatName) {
                        alert('Naam is verplicht.');
                        return;
                    }
                    
                    // Generate a unique ID from the boat name
                    const boatId = boatName.toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-|-$/g, '');
                    
                    // Check if ID already exists
                    let uniqueId = boatId;
                    let counter = 1;
                    while (boatSystem.boats.some(b => b.id === uniqueId)) {
                        uniqueId = `${boatId}-${counter}`;
                        counter++;
                    }
                    
                    // Get selected available days - generate array from 1 to selected value
                    const addDaysSelect = document.getElementById('addAvailableDaysSelect');
                    const maxDays = parseInt(addDaysSelect.value) || 7;
                    const selectedDays = Array.from({ length: maxDays }, (_, i) => i + 1);
                    
                    if (selectedDays.length === 0) {
                        alert('Selecteer minimaal één huurdag.');
                        return;
                    }
                    
                    // Build pricing object (keys are 0-indexed: 0=1 day, 1=2 days, etc.)
                    const pricing = {};
                    selectedDays.forEach(day => {
                        const input = document.getElementById(`addPrice${day}Days`);
                        if (input) {
                            pricing[day - 1] = parseFloat(input.value) || 0;
                        }
                    });
                    const pricePerDayInput = document.getElementById('addPrice1Days');
                    const pricePerDay = pricePerDayInput ? (parseFloat(pricePerDayInput.value) || 0) : 0;
                    pricing[0] = 0;
                    
                    const category = document.getElementById('addBoatCategory').value || 'electric';
                    let pricingWithEngine = null;
                    if (uniqueId === 'sailboat-4-5') {
                        pricingWithEngine = {};
                        selectedDays.forEach(day => {
                            const input = document.getElementById(`addEnginePrice${day}Days`);
                            if (input) {
                                pricingWithEngine[day - 1] = parseFloat(input.value) || 0;
                            }
                        });
                    }
                    
                    const newBoat = {
                        id: uniqueId,
                        name: boatName,
                        category: category,
                        deposit: parseFloat(document.getElementById('addBoatDeposit').value) || 0,
                        pricePerDay: pricePerDay,
                        description: document.getElementById('addBoatDescription').value || '',
                        passengerCount: document.getElementById('addPassengerCount').value || '',
                        total: Math.max(0, parseInt(document.getElementById('addBoatCount').value) || 0),
                        available: Math.max(0, parseInt(document.getElementById('addBoatCount').value) || 0),
                        orderId: parseInt(document.getElementById('addOrderId').value) || 99,
                        image: addMainImageData || '../frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg',
                        headerImage: addHeaderImageData || '../frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg',
                        availableDays: selectedDays,
                        pricing: pricing,
                        pricingWithEngine: pricingWithEngine,
                        photos: []
                    };
                    
                    boatSystem.boats.push(newBoat);
                    const saved = await boatSystem.saveData();
                    if (saved) {
                        boatSystem.renderBoats();
                        alert('Boot succesvol toegevoegd!');
                        closeAddBoatModal();
                    } else {
                        boatSystem.boats.pop();
                    }
                });
            }
            
            // Close modal when clicking outside
            window.onclick = function(event) {
                const modal = document.getElementById('addBoatModal');
                if (event.target === modal) {
                    closeAddBoatModal();
                }
            }
        });
        
        // Global function to handle boat rentals from other pages
        function handleBoatRental(boatType, date) {
            if (typeof boatSystem !== 'undefined') {
                boatSystem.handleBoatRental(boatType, date);
            }
        }
        
        // Initialize the boat management system
        const boatSystem = new BoatManagementSystem();
        
        /**
         * ========================================================================
         * BOAT DATA SERVICE - SINGLE SOURCE OF TRUTH
         * ========================================================================
         * 
         * This service provides centralized access to boat data across the entire website.
         * The authoritative source is boat-management.php (this file).
         * 
         * All boat data changes made here automatically propagate to:
         * - All pages where boats are referenced
         * - The booking system (availability, pricing, metadata)
         * - Boat detail modals and UI components
         * 
         * USAGE (from other pages):
         *   const boats = await BoatDataService.getAllBoats();
         *   const boat = await BoatDataService.getBoatById('classic-tender-720');
         *   BoatDataService.subscribe((boats) => { console.log('Updated:', boats); });
         * 
         * ========================================================================
         */
        class BoatDataService {
            constructor() {
                this.storageKey = 'nijenhuis_boats';
                this.cache = null;
                this.cacheTime = 0;
                this.cacheDuration = 2000;
                this.subscribers = [];
                this.init();
            }

            init() {
                window.addEventListener('storage', (e) => {
                    if (e.key === this.storageKey) {
                        this.cache = null;
                        this.notifySubscribers();
                    }
                });

                window.addEventListener('boatsUpdated', () => {
                    this.cache = null;
                    this.notifySubscribers();
                });

                window.addEventListener('boatsStorageUpdated', () => {
                    this.cache = null;
                    this.notifySubscribers();
                });
            }

            async loadBoats(forceRefresh = false) {
                const now = Date.now();
                if (!forceRefresh && this.cache && (now - this.cacheTime) < this.cacheDuration) {
                    return this.cache;
                }

                // Prefer localStorage for an instant fallback, then always reconcile with server
                // (matches public BoatDataService — avoids admins seeing stale tariffs indefinitely).
                let boats = [];
                try {
                    const stored = localStorage.getItem(this.storageKey);
                    if (stored) {
                        const parsed = JSON.parse(stored);
                        if (Array.isArray(parsed) && parsed.length > 0) {
                            boats = parsed;
                            this.cache = boats;
                            this.cacheTime = now;
                        }
                    }
                } catch (e) {
                    console.error('Error loading boats:', e);
                }

                try {
                    const serverBoats = await this.fetchBoatsFromServer();
                    if (serverBoats.length > 0) {
                        boats = serverBoats;
                        localStorage.setItem(this.storageKey, JSON.stringify(boats));
                        this.cache = boats;
                        this.cacheTime = now;
                    }
                } catch (e) {
                    console.warn('Server fetch failed:', e);
                }

                if (!boats.length) {
                    boats = boatSystem.getDefaultBoats();
                    localStorage.setItem(this.storageKey, JSON.stringify(boats));
                }

                this.cache = boats;
                this.cacheTime = now;
                return boats;
            }

            async fetchBoatsFromServer() {
                if (typeof boatSystem !== 'undefined' && typeof boatSystem.fetchBoatsFromServer === 'function') {
                    return boatSystem.fetchBoatsFromServer();
                }
                return [];
            }

            async getAllBoats(forceRefresh = false) {
                return await this.loadBoats(forceRefresh);
            }

            async getBoatById(boatId) {
                const boats = await this.loadBoats();
                return boats.find(b => b.id === boatId) || null;
            }

            async getBoatsByCategory(category) {
                const boats = await this.loadBoats();
                return boats.filter(b => b.category === category);
            }

            async getBoatDisplayName(boatId, engineOption = null) {
                const boat = await this.getBoatById(boatId);
                if (!boat) return boatId;
                let name = boat.name;
                if (boatId === 'sailboat-4-5' && engineOption) {
                    name += engineOption === 'with' ? ' (met motor)' : ' (zonder motor)';
                }
                return name;
            }

            async getBoatPrice(boatId, days = 1) {
                const boat = await this.getBoatById(boatId);
                if (!boat) return 0;
                const pricing = boat.pricing;
                const pricePerDay = Number(boat.pricePerDay || 0);

                // Match cart/booking frontend: tier index 0 is only for packages 2–7 days; 1-day uses pricePerDay
                // (many boats intentionally set pricing[0] = 0).
                if (days === 1) {
                    return pricePerDay;
                }
                if (pricing != null && typeof pricing === 'object') {
                    const idx = days - 1;
                    if (pricing[idx] !== undefined && pricing[idx] !== null) {
                        return Number(pricing[idx]);
                    }
                }
                return pricePerDay * days;
            }

            subscribe(callback) {
                if (typeof callback !== 'function') {
                    throw new Error('Callback must be a function');
                }
                this.subscribers.push(callback);
                this.loadBoats().then(boats => callback(boats));
                return () => {
                    const index = this.subscribers.indexOf(callback);
                    if (index > -1) this.subscribers.splice(index, 1);
                };
            }

            async notifySubscribers() {
                const boats = await this.loadBoats(true);
                this.subscribers.forEach(callback => {
                    try {
                        callback(boats);
                    } catch (e) {
                        console.error('Error in subscriber:', e);
                    }
                });
            }
        }

        // Create global BoatDataService instance
        window.BoatDataService = new BoatDataService();
    </script>
</body>
</html> 