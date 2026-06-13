<?php
// Start session and verify admin authentication
require_once 'admin-auth.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bewerk Boot - Nijenhuis Botenverhuur</title>
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
            <img src="../frontend/Images/logo-white.svg" alt="Nijenhuis" class="admin-nav-logo" style="height: 40px; width: auto;">
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
            <h1 class="admin-title">Bewerk Boot</h1>
            <p class="admin-subtitle">Nijenhuis Botenverhuur - Bewerk bootgegevens</p>
        </div>
    </div>
    
    <div class="boat-edit-page">
        <div class="edit-container">
            
            <form id="boatEditForm" class="edit-form-container">
                <!-- Left Column - Images -->
                <div class="image-section">
                    <!-- Header Afbeelding -->
                    <div class="image-upload-group">
                        <label class="image-upload-label">Header Afbeelding</label>
                        <img id="headerImagePreview" src="../frontend/Images/Boats/tender-720/tender-720-10-12.jpg" alt="Header" class="image-display">
                        <div class="file-input-wrapper">
                            <label for="headerImageInput" class="file-input-label">Bestand kiezen</label>
                            <input type="file" id="headerImageInput" class="file-input" accept="image/*">
                            <span class="file-name" id="headerImageName">Geen bestand gekozen</span>
                        </div>
                    </div>
                    
                    <!-- Afbeelding -->
                    <div class="image-upload-group">
                        <label class="image-upload-label">Afbeelding</label>
                        <img id="mainImagePreview" src="../frontend/Images/Boats/tender-720/tender-720-10-12.jpg" alt="Main" class="image-display">
                        <div class="file-input-wrapper">
                            <label for="mainImageInput" class="file-input-label">Bestand kiezen</label>
                            <input type="file" id="mainImageInput" class="file-input" accept="image/*">
                            <span class="file-name" id="mainImageName">Geen bestand gekozen</span>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Form Fields -->
                <div class="form-section">
                    <!-- Naam -->
                    <div class="form-group">
                        <label for="boatName" class="form-label">Naam</label>
                        <input type="text" id="boatName" name="name" class="form-input" value="Classic Tender 720 10/12pers" required>
                    </div>
                    
                    <!-- Category -->
                    <div class="form-group">
                        <label for="boatCategory" class="form-label">Categorie</label>
                        <select id="boatCategory" name="category" class="form-input" required>
                            <option value="electric">Elektrische boten</option>
                            <option value="sailing">Zeilboten</option>
                            <option value="canoe">Kano's & Kajaks</option>
                            <option value="sup">SUP boards</option>
                        </select>
                    </div>
                    
                    <!-- Borg -->
                    <div class="form-group">
                        <label for="boatDeposit" class="form-label">Borg</label>
                        <input type="number" id="boatDeposit" name="deposit" class="form-input" value="100" required>
                    </div>
                    
                    <!-- Omschrijving -->
                    <div class="form-group">
                        <label for="boatDescription" class="form-label">Omschrijving</label>
                        <textarea id="boatDescription" name="description" class="form-textarea" required>De sloep is een boot van Aluminium waar je met 10/12 personen in kunt. De boot is voorzien van kussens en er zit een electro motor in.

Huisdieren zijn aan boord niet toegestaan.

Met deze boot mag u niet door Giethoorn heen varen.</textarea>
                    </div>

                    <!-- SEO content (boat detail landing page) -->
                    <div class="form-group">
                        <label for="boatSeoContent" class="form-label">SEO-tekst (boot detailpagina)</label>
                        <textarea id="boatSeoContent" name="seoContent" class="form-textarea" rows="8" placeholder="Optioneel: uitgebreide tekst voor de boot detailpagina (300+ woorden). Laat leeg voor automatisch gegenereerde tekst."></textarea>
                        <p class="form-hint" style="font-size: 0.85rem; color: #666; margin-top: 0.35rem;">Wordt getoond op /boot-id pagina's voor zoekmachines en bezoekers. HTML is toegestaan.</p>
                    </div>
                    
                    <!-- Available Days Selection -->
                    <div class="form-group">
                        <label for="availableDaysSelect" class="form-label">Beschikbare huurdagen selecteren</label>
                        <select id="availableDaysSelect" name="availableDays" class="form-input">
                            <option value="1">1 dag</option>
                            <option value="2">2 dagen</option>
                            <option value="3">3 dagen</option>
                            <option value="4">4 dagen</option>
                            <option value="5">5 dagen</option>
                            <option value="6">6 dagen</option>
                            <option value="7">7 dagen</option>
                        </select>
                    </div>
                    
                    <!-- Multi-day Pricing -->
                    <div class="form-group">
                        <label id="pricingLabel" class="form-label">Meerdaagse prijzen</label>
                        <div id="pricingGrid" class="pricing-grid">
                            <!-- Dynamic pricing inputs will be generated here -->
                        </div>
                    </div>
                    
                    <!-- Engine Pricing for Sailboats -->
                    <div class="form-group" id="enginePricingSection" style="display: none;">
                        <label class="form-label">Meerdaagse prijzen (met motor)</label>
                        <div id="enginePricingGrid" class="pricing-grid">
                            <!-- Dynamic pricing inputs for engine will be generated here -->
                        </div>
                    </div>
                    
                    <!-- Bottom Info -->
                    <div class="info-grid">
                        <div class="form-group">
                            <label for="passengerCount" class="form-label">Aantal passagiers</label>
                            <input type="text" id="passengerCount" name="passengerCount" class="form-input" value="10 tot 12 personen">
                        </div>
                        <div class="form-group">
                            <label for="boatCount" class="form-label">Aantal boten</label>
                            <input type="number" id="boatCount" name="boatCount" class="form-input" value="2">
                        </div>
                        <div class="form-group">
                            <label for="orderId" class="form-label">Order_id</label>
                            <input type="number" id="orderId" name="orderId" class="form-input" value="1">
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='boat-management.php'">Annuleren</button>
                        <button type="submit" class="btn btn-primary">Opslaan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const adminSession = { csrfToken: '' };

        function getSessionEndpoint() {
            if (window.location.protocol === 'file:' || window.location.hostname === '') {
                return 'http://localhost:8000/admin/booking-handler.py';
            }
            return `${window.location.origin}/admin/booking-handler.php`;
        }

        async function refreshAdminSession() {
            try {
                const endpoint = getSessionEndpoint();
                const sessionUrl = endpoint + '?action=session';
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

        // Authentication check
        (async function checkAuth() {
            const isAuthenticated = await refreshAdminSession();
            if (!isAuthenticated) {
                window.location.href = '../pages/admin-login.php';
                return;
            }
        })();
        
        // Image preview handlers
        document.getElementById('headerImageInput').addEventListener('change', function(e) {
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
                        headerImageData = dataUrl;
                        document.getElementById('headerImagePreview').src = headerImageData;
                        document.getElementById('headerImageName').textContent = file.name;
                        if (currentBoat) {
                            currentBoat.headerImage = headerImageData;
                        }
                    } catch (err) {
                        console.error(err);
                    }
                })();
            }
        });
        
        document.getElementById('mainImageInput').addEventListener('change', function(e) {
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
                        mainImageData = dataUrl;
                        document.getElementById('mainImagePreview').src = mainImageData;
                        document.getElementById('mainImageName').textContent = file.name;
                        if (currentBoat) {
                            currentBoat.image = mainImageData;
                        }
                    } catch (err) {
                        console.error(err);
                    }
                })();
            }
        });
        
        // Boat data storage key
        const boatsStorageKey = 'nijenhuis_boats';
        
        // Load boat data if ID is provided
        const urlParams = new URLSearchParams(window.location.search);
        const boatId = urlParams.get('id');
        let currentBoat = null;
        
        // Use centralized boat data from BoatDataService (../js/boat-data-service.js)
        function getDefaultBoats() {
            return window.BoatDataService ? window.BoatDataService.getDefaultBoats() : [];
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

        async function fetchBoatsFromServer() {
            const endpoint = getSessionEndpoint();

            try {
                const endpointUrl = new URL(endpoint, window.location.href);
                const sameOriginHttp = (window.location.protocol === 'http:' || window.location.protocol === 'https:') &&
                    (endpointUrl.origin === window.location.origin);

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

                const csrf = await getCsrfToken();
                const headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                };
                if (csrf) {
                    headers['X-CSRF-Token'] = csrf;
                }

                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 5000);

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
                }
            } catch (e) {
                if (e.name !== 'AbortError') {
                    console.warn('Server fetch failed:', e);
                }
            }
            return [];
        }

        function populateBoatForm(boat) {
            currentBoat = boat;

            document.getElementById('boatName').value = currentBoat.name || '';
            document.getElementById('boatCategory').value = currentBoat.category || 'electric';
            document.getElementById('boatDeposit').value = currentBoat.deposit || 0;
            document.getElementById('boatDescription').value = currentBoat.description || '';
            document.getElementById('boatSeoContent').value = currentBoat.seoContent || '';
            document.getElementById('passengerCount').value = currentBoat.passengerCount || '';
            document.getElementById('boatCount').value = currentBoat.total ?? 0;
            document.getElementById('orderId').value = currentBoat.orderId || 1;

            const availableDays = currentBoat.availableDays || [1, 2, 3, 4, 5, 6, 7];
            const maxDay = availableDays.length > 0 ? Math.max(...availableDays) : 7;
            const daysSelect = document.getElementById('availableDaysSelect');
            if (daysSelect) {
                daysSelect.value = maxDay.toString();
            }

            const engineSection = document.getElementById('enginePricingSection');
            if (currentBoat.id === 'sailboat-4-5') {
                engineSection.style.display = 'block';
            } else {
                engineSection.style.display = 'none';
            }

            const pricingLabel = document.getElementById('pricingLabel');
            if (pricingLabel) {
                if (currentBoat.name.toLowerCase().includes('zeilpunter') || currentBoat.id === 'sailpunter-3-4') {
                    pricingLabel.textContent = 'Prijz';
                } else {
                    pricingLabel.textContent = 'Meerdaagse prijzen';
                }
            }

            updatePricingInputs();

            if (currentBoat.headerImage) {
                document.getElementById('headerImagePreview').src = currentBoat.headerImage;
            }
            if (currentBoat.image) {
                document.getElementById('mainImagePreview').src = currentBoat.image;
            }
        }

        async function loadBoatData() {
            if (!boatId) {
                alert('Geen boot ID opgegeven.');
                window.location.href = 'boat-management.php';
                return;
            }

            let boats = [];

            try {
                const serverBoats = await fetchBoatsFromServer();
                if (serverBoats && serverBoats.length > 0) {
                    boats = serverBoats;
                    localStorage.setItem(boatsStorageKey, JSON.stringify(boats));
                }
            } catch (error) {
                console.warn('Could not load boats from server:', error);
            }

            if (boats.length === 0) {
                boats = JSON.parse(localStorage.getItem(boatsStorageKey) || '[]');
            }

            if (boats.length === 0) {
                boats = getDefaultBoats();
                localStorage.setItem(boatsStorageKey, JSON.stringify(boats));
            }

            const boat = boats.find(b => b.id === boatId);

            if (!boat) {
                alert(`Boot met ID "${boatId}" niet gevonden. Beschikbare boten: ${boats.map(b => b.id).join(', ')}`);
                window.location.href = 'boat-management.php';
                return;
            }

            populateBoatForm(boat);
        }
        
        // Form submission - ensure DOM is ready
        function setupFormSubmission() {
            const form = document.getElementById('boatEditForm');
            if (!form) {
                console.error('Form not found!');
                return;
            }
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                console.log('Form submitted, currentBoat:', currentBoat);
                
                if (!currentBoat) {
                    alert('Boot data niet geladen. Laad de pagina opnieuw.');
                    return;
                }
                
                if (!boatId) {
                    alert('Geen boot ID gevonden. Laad de pagina opnieuw.');
                    return;
                }
                
                try {
                    // Get selected available days - generate array from 1 to selected value
                    const daysSelect = document.getElementById('availableDaysSelect');
                    const maxDays = parseInt(daysSelect.value) || 7;
                    const selectedDays = Array.from({ length: maxDays }, (_, i) => i + 1);
                    
                    if (selectedDays.length === 0) {
                        alert('Selecteer minimaal één huurdag.');
                        return;
                    }
            
                    // Build pricing object (keys are 0-indexed: 0=1 day, 1=2 days, etc.)
                    const pricing = {};
                    selectedDays.forEach(day => {
                        const input = document.getElementById(`Price${day}Days`);
                        if (input) {
                            pricing[day - 1] = parseFloat(input.value) || 0;
                        }
                    });
                    const pricePerDayInput = document.getElementById('Price1Days');
                    const parsedPricePerDay = pricePerDayInput ? parseFloat(pricePerDayInput.value) : NaN;
                    const pricePerDay = Number.isFinite(parsedPricePerDay)
                        ? parsedPricePerDay
                        : (parseFloat(currentBoat.pricePerDay) || 0);
                    const originalPricing = currentBoat.pricing || {};
                    const originalFirstTier = originalPricing[0] ?? originalPricing['0'];
                    if (originalFirstTier === 0) {
                        pricing[0] = 0;
                    }
                    
                    // Optional outboard pricing only for Zeilboot (sailboat-4-5)
                    const category = document.getElementById('boatCategory').value || 'electric';
                    let pricingWithEngine = null;
                    if (currentBoat.id === 'sailboat-4-5') {
                        pricingWithEngine = {};
                        selectedDays.forEach(day => {
                            const input = document.getElementById(`enginePrice${day}Days`);
                            if (input) {
                                pricingWithEngine[day - 1] = parseFloat(input.value) || 0;
                            }
                        });
                    }
                    
                    // Collect form data
                    const newTotal = parseInt(document.getElementById('boatCount').value) || 0;
                    const previousAvailable = currentBoat.available !== undefined
                        ? currentBoat.available
                        : (currentBoat.total ?? 0);
                    const syncedAvailable = Math.min(Math.max(0, previousAvailable), newTotal);

                    const formData = {
                        id: currentBoat.id,
                        name: document.getElementById('boatName').value,
                        category: category,
                        deposit: parseFloat(document.getElementById('boatDeposit').value) || 0,
                        pricePerDay: pricePerDay,
                        description: document.getElementById('boatDescription').value,
                        seoContent: document.getElementById('boatSeoContent').value.trim() || undefined,
                        passengerCount: document.getElementById('passengerCount').value,
                        total: newTotal,
                        available: syncedAvailable,
                        orderId: parseInt(document.getElementById('orderId').value) || 1,
                        image: mainImageData || currentBoat.image || document.getElementById('mainImagePreview').src,
                        headerImage: headerImageData || currentBoat.headerImage || document.getElementById('headerImagePreview').src,
                        availableDays: selectedDays,
                        pricing: pricing,
                        pricingWithEngine: pricingWithEngine,
                        photos: currentBoat.photos || []
                    };
                    
                    // Update boat in boats array (server is source of truth — persist locally only after save succeeds)
                    let boats = JSON.parse(localStorage.getItem(boatsStorageKey) || '[]');
                    if (boats.length === 0) {
                        try {
                            const serverBoats = await fetchBoatsFromServer();
                            if (serverBoats.length > 0) {
                                boats = serverBoats;
                            }
                        } catch (e) {
                            console.warn('Could not refresh boats from server before save:', e);
                        }
                    }

                    const boatIndex = boats.findIndex(b => b.id === boatId);
                    
                    if (boatIndex !== -1) {
                        boats[boatIndex] = formData;
                        console.log('✅ Updated boat at index', boatIndex);
                    } else {
                        boats.push(formData);
                        console.log('✅ Added new boat');
                    }
                    
                    // Push to server first — do not update localStorage until confirmed
                    async function pushBoatsToServer() {
                        try {
                            // Only use Python handler for file:// protocol (opening HTML files directly)
                            // When served via any web server (including localhost PHP), use PHP handler
                            const isFileProtocol = window.location.protocol === 'file:' || window.location.hostname === '';
                            const endpoint = isFileProtocol
                                ? 'http://localhost:8000/admin/booking-handler.py'
                                : `${window.location.origin}/admin/booking-handler.php`;
                            
                            console.log('Saving boats to:', endpoint);
                            console.log('Number of boats:', boats.length);
                            
                            // CSRF is required for authenticated admin actions (PHP handler)
                            const csrf = await getCsrfToken();
                            const headers = {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            };
                            if (csrf) {
                                headers['X-CSRF-Token'] = csrf;
                            }
                            
                            const response = await fetch(endpoint, {
                                method: 'POST',
                                headers,
                                credentials: 'include',
                                body: JSON.stringify({ action: 'saveBoats', boats: boats, csrfToken: csrf })
                            });
                            
                            console.log('Response status:', response.status);
                            
                            // Get response text first to see what we're getting
                            const responseText = await response.text();
                            console.log('Response text:', responseText);
                            
                            let result;
                            try {
                                result = JSON.parse(responseText);
                            } catch (e) {
                                console.error('Failed to parse JSON response:', e);
                                console.error('Response was:', responseText);
                                throw new Error('Invalid JSON response from server: ' + responseText.substring(0, 200));
                            }
                            
                            if (response.ok) {
                                if (result.success) {
                                    console.log(`✅ Successfully saved ${boats.length} boats to server`);
                                } else {
                                    console.error('Server returned success=false:', result);
                                    throw new Error(result.message || 'Server returned error');
                                }
                            } else {
                                console.error('Server error response:', result);
                                throw new Error(result.message || `Server error: ${response.status} ${response.statusText}`);
                            }
                        } catch (e) {
                            console.error('Failed to push boats to server:', e);
                            throw e; // Re-throw so the caller can handle it
                        }
                    }
                    
                    await pushBoatsToServer();

                    localStorage.setItem(boatsStorageKey, JSON.stringify(boats));
                    console.log('✅ Saved to localStorage after server confirmed, total boats:', boats.length);
                    
                    // Force a storage event by removing and re-setting (workaround for same-tab updates)
                    const currentValue = localStorage.getItem(boatsStorageKey);
                    localStorage.removeItem(boatsStorageKey);
                    localStorage.setItem(boatsStorageKey, currentValue);
                    
                    // Trigger custom events for same-tab and cross-tab updates
                    window.dispatchEvent(new CustomEvent('boatsUpdated', { detail: boats }));
                    window.dispatchEvent(new CustomEvent('boatsStorageUpdated'));
                    
                    console.log('✅ Boat data saved and events triggered');
                    
                    alert('Bootgegevens opgeslagen! Booking formulieren worden automatisch bijgewerkt.');
                    window.location.href = 'boat-management.php';
                } catch (error) {
                    console.error('Error saving boat:', error);
                    alert('Opslaan mislukt — wijzigingen zijn niet opgeslagen. Fout: ' + error.message);
                }
            });
        }
        
        // Setup form submission when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupFormSubmission);
        } else {
            setupFormSubmission();
        }
        
        // Day selection change handlers
        document.addEventListener('DOMContentLoaded', function() {
            const daysSelect = document.getElementById('availableDaysSelect');
            if (daysSelect) {
                daysSelect.addEventListener('change', updatePricingInputs);
            }
            
            // Category change: engine block only for boot id sailboat-4-5
            const categorySelect = document.getElementById('boatCategory');
            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    const engineSection = document.getElementById('enginePricingSection');
                    const boatId = new URLSearchParams(window.location.search).get('id') || '';
                    if (this.value === 'sailing' && boatId === 'sailboat-4-5') {
                        engineSection.style.display = 'block';
                        updatePricingInputs();
                    } else {
                        engineSection.style.display = 'none';
                        updatePricingInputs();
                    }
                });
            }
        });
        
        // Load data on page load (server-first)
        if (boatId) {
            loadBoatData().catch((error) => {
                console.error('Failed to load boat data:', error);
                alert('Kon bootgegevens niet laden. Probeer de pagina opnieuw.');
            });
        } else {
            alert('Geen boot ID opgegeven.');
            window.location.href = 'boat-management.php';
        }
        
        // Handle image uploads
        let headerImageData = null;
        let mainImageData = null;
        
        // Function to generate dynamic pricing inputs
        function generatePricingInputs(containerId, prefix, selectedDays, existingPrices = {}) {
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
                // Get price from existingPrices (key is day-1 for 0-indexed)
                const priceKey = day - 1;
                input.value = existingPrices[priceKey] !== undefined ? existingPrices[priceKey] : '0';
                input.min = '0';
                input.step = '0.01';
                
                pricingItem.appendChild(label);
                pricingItem.appendChild(input);
                container.appendChild(pricingItem);
            });
        }
        
        // Function to update pricing inputs based on selected days
        function updatePricingInputs() {
            const daysSelect = document.getElementById('availableDaysSelect');
            const maxDays = parseInt(daysSelect.value) || 1;
            const selectedDays = Array.from({ length: maxDays }, (_, i) => i + 1);
            
            // Get existing pricing from currentBoat if available
            const existingPricing = { ...(currentBoat?.pricing || {}) };
            const existingEnginePricing = currentBoat?.pricingWithEngine || {};
            if (currentBoat && currentBoat.pricePerDay !== undefined) {
                existingPricing[0] = currentBoat.pricePerDay;
            }
            
            generatePricingInputs('pricingGrid', '', selectedDays, existingPricing);
            
            const boatIdParam = new URLSearchParams(window.location.search).get('id') || '';
            if (boatIdParam === 'sailboat-4-5') {
                generatePricingInputs('enginePricingGrid', 'engine', selectedDays, existingEnginePricing);
            }
        }
        
        async function logout() {
            const csrf = await getCsrfToken();
            
            try {
                const endpoint = (window.location.protocol === 'file:' || window.location.hostname === '')
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
    </script>
</body>
</html>

