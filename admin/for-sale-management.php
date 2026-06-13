<?php
// Start session and verify admin authentication
require_once 'admin-auth.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Te koop beheer - Nijenhuis Botenverhuur</title>
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <script src="../frontend/src/js/core/shared.js"></script>
    <script src="../js/image-compress.js"></script>
    <link rel="stylesheet" href="admin-consolidated.css">
    <link rel="icon" type="image/svg+xml" href="../frontend/Images/logo-white.svg">
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="admin-nav">
        <div class="admin-nav-container">
            <img src="../frontend/Images/logo-white.svg" alt="Nijenhuis" class="admin-nav-logo">
            <div class="admin-nav-links">
                <a href="admin-static.php" class="admin-nav-link">Dashboard</a>
                <a href="boat-management.php" class="admin-nav-link">Bootbeheer</a>
                <a href="booking-management.php" class="admin-nav-link">Reserveringsbeheer</a>
                <a href="booking-history.php" class="admin-nav-link">Boekingsgeschiedenis</a>
                <a href="for-sale-management.php" class="admin-nav-link active">Te koop</a>
            </div>
            <button class="admin-nav-logout" onclick="logout()">Uitloggen</button>
        </div>
    </nav>

    <!-- Admin Header Section -->
    <div class="admin-header">
        <div class="admin-container">
            <h1 class="admin-title">Te koop beheer</h1>
            <p class="admin-subtitle">Nijenhuis Botenverhuur - Beheer boten en caravans te koop</p>
        </div>
    </div>
    
    <div class="boat-management">
        <div class="management-container">
            <!-- Navigation Links -->
            <div class="nav-links-section">
                <div class="nav-links">
                    <button type="button" class="nav-link">Overzicht</button>
                    <span class="nav-separator">|</span>
                    <button type="button" class="nav-link" onclick="showAddItemModal()">Nieuw item toevoegen.</button>
                    <span class="nav-separator">|</span>
                    <button type="button" class="nav-link" id="migrateBtn" onclick="runImageMigration()" title="Converteert opgeslagen base64-afbeeldingen naar serverbestanden (eenmalig uitvoeren)">Afbeeldingen migreren</button>
                </div>
            </div>
            
            <!-- Items Grid -->
            <div class="boat-grid" id="forSaleGrid">
                <!-- Items will be populated here -->
            </div>
            
            <!-- Empty State -->
            <div id="emptyState" class="empty-state" style="display: none;">
                <p>Geen items te koop. Klik op "Nieuw item toevoegen" om te beginnen.</p>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div id="addItemModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
            <div class="modal-header">
                <h2 id="modalTitle">Nieuw item toevoegen</h2>
                <span class="close" onclick="closeAddItemModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addItemForm">
                    <input type="hidden" id="editItemId" value="">
                    <div class="edit-form-container" style="display: block;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-xl);">
                            <!-- Left Column - Images -->
                            <div class="image-section">
                                <div class="image-upload-group">
                                    <label class="image-upload-label">Hoofdafbeelding *</label>
                                    <img id="mainImagePreview" src="../frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg" alt="Main" class="image-display">
                                    <div class="file-input-wrapper">
                                        <label for="mainImageInput" class="file-input-label">Bestand kiezen</label>
                                        <input type="file" id="mainImageInput" class="file-input" accept="image/*">
                                        <span class="file-name" id="mainImageName">Geen bestand gekozen</span>
                                    </div>
                                </div>
                                
                                <div class="image-upload-group">
                                    <label class="image-upload-label">Extra afbeeldingen</label>
                                    <div id="additionalImagesPreview" class="additional-images-grid"></div>
                                    <div class="file-input-wrapper">
                                        <label for="additionalImagesInput" class="file-input-label">Bestanden kiezen</label>
                                        <input type="file" id="additionalImagesInput" class="file-input" accept="image/*" multiple>
                                        <span class="file-name" id="additionalImagesName">Geen bestanden gekozen</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column - Form Fields -->
                            <div class="form-section">
                                <div class="form-group">
                                    <label for="itemName" class="form-label">Naam *</label>
                                    <input type="text" id="itemName" name="name" class="form-input" required placeholder="bijv. Chalet De Belterwijde">
                                </div>
                                
                                <div class="form-group">
                                    <label for="itemCategory" class="form-label">Categorie *</label>
                                    <select id="itemCategory" name="category" class="form-input" required>
                                        <option value="chalet">Chalet</option>
                                        <option value="stacaravan">Stacaravan</option>
                                        <option value="boot">Boot</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="itemPrice" class="form-label">Vraagprijs (€) *</label>
                                    <input type="number" id="itemPrice" name="price" class="form-input" required min="0" step="1" placeholder="bijv. 25000">
                                </div>
                                
                                <div class="form-group">
                                    <label for="itemDescription" class="form-label">Omschrijving *</label>
                                    <textarea id="itemDescription" name="description" class="form-textarea" rows="5" required placeholder="Beschrijf het item, inclusief kenmerken, staat, afmetingen, etc."></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="itemFeatures" class="form-label">Kenmerken (één per regel)</label>
                                    <textarea id="itemFeatures" name="features" class="form-textarea" rows="4" placeholder="bijv.
2 slaapkamers
Volledig gemeubileerd
Gasaansluiting
Eigen parkeerplaats"></textarea>
                                </div>
                                
                                <div class="info-grid" style="grid-template-columns: 1fr 1fr;">
                                    <div class="form-group">
                                        <label for="itemYear" class="form-label">Bouwjaar</label>
                                        <input type="number" id="itemYear" name="year" class="form-input" placeholder="bijv. 2018" min="1950" max="2099">
                                    </div>
                                    <div class="form-group">
                                        <label for="itemSize" class="form-label">Afmetingen</label>
                                        <input type="text" id="itemSize" name="size" class="form-input" placeholder="bijv. 8m x 3m">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <input type="checkbox" id="itemFeatured" name="featured"> Uitgelicht (toon bovenaan)
                                    </label>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="button" class="btn btn-secondary" onclick="closeAddItemModal()">Annuleren</button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Item toevoegen</button>
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

        async function refreshAdminSession() {
            try {
                const endpoint = window.AppConfig.detectServerEndpoint();
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

        // For-Sale Management System
        class ForSaleManagement {
            constructor() {
                this.items = [];
                this.storageKey = 'nijenhuis_for_sale';
                this.pendingMainImageFile = null;       // File object awaiting upload
                this.pendingAdditionalFiles = [];       // Array of File objects awaiting upload
                this.currentMainImageUrl = null;        // Server URL or existing URL for main image
                this.currentAdditionalUrls = [];        // Server URLs or existing URLs for additional images
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
                
                await this.loadItems();
                this.setupEventListeners();
                this.renderItems();
            }
            
            async loadItems() {
                // Load from server (authoritative source)
                try {
                    const endpoint = window.AppConfig.detectServerEndpoint();
                    const response = await fetch(`${endpoint}?action=getForSaleItems`, {
                        method: 'GET',
                        credentials: 'include'
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success && Array.isArray(data.items)) {
                            this.items = data.items;
                            return;
                        }
                    }
                } catch (e) {
                    console.warn('Failed to load from server:', e);
                }

                // Fallback: read metadata-only cache from localStorage (no image data)
                try {
                    const stored = localStorage.getItem(this.storageKey);
                    if (stored) {
                        this.items = JSON.parse(stored);
                    }
                } catch (e) {
                    this.items = [];
                }
            }
            
            
            // detectServerEndpoint removed - using window.AppConfig
            
            
            setupEventListeners() {
                // Main image preview
                const mainImageInput = document.getElementById('mainImageInput');
                if (mainImageInput) {
                    mainImageInput.addEventListener('change', (e) => this.handleMainImageChange(e));
                }
                
                // Additional images preview
                const additionalImagesInput = document.getElementById('additionalImagesInput');
                if (additionalImagesInput) {
                    additionalImagesInput.addEventListener('change', (e) => this.handleAdditionalImagesChange(e));
                }
                
                // Form submission
                const form = document.getElementById('addItemForm');
                if (form) {
                    form.addEventListener('submit', (e) => this.handleFormSubmit(e));
                }
            }
            
            handleMainImageChange(e) {
                const file = e.target.files[0];
                if (file) {
                    // Revoke previous object URL to free memory
                    const prev = document.getElementById('mainImagePreview').src;
                    if (prev && prev.startsWith('blob:')) URL.revokeObjectURL(prev);

                    this.pendingMainImageFile = file;
                    document.getElementById('mainImagePreview').src = URL.createObjectURL(file);
                    document.getElementById('mainImageName').textContent = file.name;
                }
            }
            
            handleAdditionalImagesChange(e) {
                const files = Array.from(e.target.files);
                const container = document.getElementById('additionalImagesPreview');
                const nameSpan = document.getElementById('additionalImagesName');

                files.forEach(file => {
                    const objectUrl = URL.createObjectURL(file);
                    this.pendingAdditionalFiles.push(file);

                    const imgWrapper = document.createElement('div');
                    imgWrapper.className = 'additional-image-wrapper';
                    imgWrapper.innerHTML = `<img src="${objectUrl}" alt="Preview">`;
                    container.appendChild(imgWrapper);
                });

                const totalPending = this.pendingAdditionalFiles.length;
                const totalExisting = this.currentAdditionalUrls.length;
                const total = totalPending + totalExisting;
                nameSpan.textContent = total > 0 ? `${total} bestand(en)` : 'Geen bestanden gekozen';

                // Clear the input so the same folder can be picked again to add more
                e.target.value = '';
            }
            
            async uploadImage(file) {
                const endpoint = window.AppConfig.detectServerEndpoint();
                const csrf = adminSession.csrfToken || '';
                const toSend = typeof compressImageForUpload === 'function'
                    ? await compressImageForUpload(file)
                    : file;
                const formData = new FormData();
                formData.append('action', 'uploadForSaleImage');
                formData.append('image', toSend);

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'X-CSRF-Token': csrf },
                    credentials: 'include',
                    body: formData
                });

                const _respText = await response.text();
                if (!response.ok) {
                    throw new Error(`Image upload failed (HTTP ${response.status}): ${_respText}`);
                }
                const result = JSON.parse(_respText);

                if (!result.success) {
                    throw new Error(result.message || 'Image upload failed');
                }
                return result.url;
            }

            async handleFormSubmit(e) {
                e.preventDefault();
                
                const editId = document.getElementById('editItemId').value;
                const isEdit = editId !== '';
                
                // Validate required fields
                const itemName = document.getElementById('itemName').value.trim();
                if (!itemName) {
                    alert('Naam is verplicht. Vul alstublieft een naam in.');
                    document.getElementById('itemName').focus();
                    return;
                }

                // Disable submit button to prevent double-submission
                const submitBtn = document.getElementById('submitBtn');
                const originalBtnText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Bezig met uploaden...';

                let mainImage;
                let additionalImages;

                try {
                    // Upload pending main image file if a new one was selected
                    if (this.pendingMainImageFile) {
                        mainImage = await this.uploadImage(this.pendingMainImageFile);
                    } else {
                        // Use existing URL (from current item or default preview)
                        const previewSrc = document.getElementById('mainImagePreview').src;
                        // Strip blob: URLs (shouldn't happen here, but guard anyway)
                        mainImage = previewSrc.startsWith('blob:') ? (this.currentMainImageUrl || '') : previewSrc;
                    }

                    // Upload any pending additional image files
                    const newAdditionalUrls = [];
                    for (const file of this.pendingAdditionalFiles) {
                        const url = await this.uploadImage(file);
                        newAdditionalUrls.push(url);
                    }
                    additionalImages = [...this.currentAdditionalUrls, ...newAdditionalUrls];

                } catch (uploadError) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                    alert('Afbeelding uploaden mislukt: ' + uploadError.message);
                    return;
                }
                
                // Parse features
                const featuresText = document.getElementById('itemFeatures').value;
                const features = featuresText.split('\n').filter(f => f.trim() !== '');
                
                const itemData = {
                    id: isEdit ? editId : this.generateId(),
                    name: itemName,
                    category: document.getElementById('itemCategory').value,
                    price: parseFloat(document.getElementById('itemPrice').value) || 0,
                    description: document.getElementById('itemDescription').value,
                    features: features,
                    year: document.getElementById('itemYear').value || null,
                    size: document.getElementById('itemSize').value || null,
                    featured: document.getElementById('itemFeatured').checked,
                    mainImage: mainImage,
                    additionalImages: additionalImages,
                    createdAt: isEdit ? (this.items.find(i => i.id === editId)?.createdAt || new Date().toISOString()) : new Date().toISOString(),
                    updatedAt: new Date().toISOString()
                };
                
                if (isEdit) {
                    // Update existing item
                    const index = this.items.findIndex(i => i.id === editId);
                    if (index !== -1) {
                        this.items[index] = itemData;
                    }
                } else {
                    // Add new item
                    this.items.push(itemData);
                }
                
                await this.saveItems();
                this.renderItems();
                closeAddItemModal();

                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
                
                alert(isEdit ? 'Item succesvol bijgewerkt!' : 'Item succesvol toegevoegd!');
            }
            
            generateId() {
                return 'item_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            }
            
            async saveItems() {
                // Store only metadata (no image data) in localStorage to avoid quota errors.
                // Images are now stored as server-side files, not base64 strings.
                try {
                    const metaOnly = this.items.map(({ mainImage, additionalImages, ...meta }) => meta);
                    localStorage.setItem(this.storageKey, JSON.stringify(metaOnly));
                } catch (e) {
                    // localStorage quota exceeded or unavailable - safe to ignore, server is source of truth
                    console.warn('localStorage write skipped:', e.message);
                }

                // Trigger custom events to notify other pages of updates (same-tab)
                window.dispatchEvent(new CustomEvent('forSaleItemsUpdated', { detail: this.items }));
                window.dispatchEvent(new CustomEvent('forSaleItemsStorageUpdated'));

                // Force immediate update by dispatching another event after a tiny delay
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('forSaleItemsUpdated', { detail: this.items }));
                    window.dispatchEvent(new CustomEvent('forSaleItemsStorageUpdated'));
                }, 50);
                
                // Save to server (retry once if CSRF expired)
                try {
                    const endpoint = window.AppConfig.detectServerEndpoint();
                    const sendSaveRequest = async () => {
                        if (!adminSession.csrfToken) {
                            await refreshAdminSession();
                        }
                        const csrf = adminSession.csrfToken || '';
                        return await fetch(endpoint, {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-Token': csrf
                            },
                            credentials: 'include',
                            body: JSON.stringify({
                                action: 'saveForSaleItems',
                                items: this.items
                            })
                        });
                    };

                    let response = await sendSaveRequest();
                    if (response.status === 403) {
                        await refreshAdminSession();
                        response = await sendSaveRequest();
                    }

                    if (response.ok) {
                        const result = await response.json();
                        if (result.success) {
                            console.log('✅ Items saved to server');
                        } else {
                            console.warn('Server returned error:', result.message);
                        }
                    } else {
                        console.warn('Failed to save to server: HTTP', response.status);
                        if (response.status === 401) {
                            alert('Je sessie is verlopen. Log opnieuw in om op te slaan.');
                            window.location.href = '../pages/admin-login.php';
                        } else if (response.status === 403) {
                            alert('Opslaan mislukt: sessie-token ongeldig. Ververs de pagina en probeer opnieuw.');
                        } else if (response.status === 413) {
                            alert('Opslaan mislukt: De afbeeldingen zijn samen te groot. Probeer kleinere afbeeldingen te gebruiken.');
                        } else {
                            alert(`Opslaan mislukt: Server fout (HTTP ${response.status}). Controleer de console voor details.`);
                        }
                    }
                } catch (e) {
                    console.warn('Failed to save to server:', e);
                }
            }
            
            renderItems() {
                const grid = document.getElementById('forSaleGrid');
                const emptyState = document.getElementById('emptyState');
                
                if (!grid) return;
                
                grid.innerHTML = '';
                
                if (this.items.length === 0) {
                    emptyState.style.display = 'block';
                    return;
                }
                
                emptyState.style.display = 'none';
                
                // Sort items: featured first, then by date
                const sortedItems = [...this.items].sort((a, b) => {
                    if (a.featured && !b.featured) return -1;
                    if (!a.featured && b.featured) return 1;
                    return new Date(b.createdAt) - new Date(a.createdAt);
                });
                
                sortedItems.forEach(item => {
                    const card = document.createElement('div');
                    // Add 'featured' class if item is featured for highlighting
                    card.className = item.featured ? 'boat-card featured' : 'boat-card';
                    
                    const categoryLabels = {
                        'chalet': 'Chalet',
                        'stacaravan': 'Stacaravan',
                        'boot': 'Boot'
                    };
                    
                    // Ensure name is displayed, fallback to 'Onbekend' if empty
                    const itemName = item.name && item.name.trim() ? item.name : 'Onbekend';
                    
                    card.innerHTML = `
                        <img src="${this.escapeHTML(item.mainImage)}" alt="${this.escapeHTML(itemName)}" class="boat-card-image" onerror="this.src='../frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg'">
                        <div class="boat-card-content">
                            <div class="boat-card-title">
                                ${item.featured ? '<span style="color: var(--warning-color);">★</span> ' : ''}
                                ${this.escapeHTML(itemName)}
                            </div>
                            <div style="color: var(--text-secondary); font-size: var(--font-size-sm); margin-bottom: var(--spacing-sm);">
                                ${categoryLabels[item.category] || item.category} • €${this.formatPrice(item.price)}
                            </div>
                            <div class="boat-card-actions">
                                <a href="#" class="boat-action-link" onclick="forSaleManager.editItem('${item.id}'); return false;">bewerk</a>
                                <a href="#" class="boat-action-link" onclick="forSaleManager.deleteItem('${item.id}'); return false;">verwijder</a>
                            </div>
                        </div>
                    `;
                    
                    grid.appendChild(card);
                });
            }
            
            editItem(itemId) {
                const item = this.items.find(i => i.id === itemId);
                if (!item) return;

                // Reset pending upload state
                this.pendingMainImageFile = null;
                this.pendingAdditionalFiles = [];
                this.currentMainImageUrl = item.mainImage || null;
                this.currentAdditionalUrls = item.additionalImages ? [...item.additionalImages] : [];
                
                // Set form values
                document.getElementById('editItemId').value = item.id;
                document.getElementById('modalTitle').textContent = 'Item bewerken';
                document.getElementById('submitBtn').textContent = 'Wijzigingen opslaan';
                document.getElementById('itemName').value = item.name || '';
                document.getElementById('itemCategory').value = item.category || 'chalet';
                document.getElementById('itemPrice').value = item.price || '';
                document.getElementById('itemDescription').value = item.description || '';
                document.getElementById('itemFeatures').value = (item.features || []).join('\n');
                document.getElementById('itemYear').value = item.year || '';
                document.getElementById('itemSize').value = item.size || '';
                document.getElementById('itemFeatured').checked = item.featured || false;
                document.getElementById('mainImagePreview').src = item.mainImage || '../frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg';
                document.getElementById('mainImageName').textContent = 'Huidige afbeelding';
                
                // Show additional images
                const container = document.getElementById('additionalImagesPreview');
                container.innerHTML = '';
                if (item.additionalImages && item.additionalImages.length > 0) {
                    item.additionalImages.forEach(imgSrc => {
                        const imgWrapper = document.createElement('div');
                        imgWrapper.className = 'additional-image-wrapper';
                        imgWrapper.innerHTML = `<img src="${imgSrc}" alt="Preview">`;
                        container.appendChild(imgWrapper);
                    });
                    document.getElementById('additionalImagesName').textContent = `${item.additionalImages.length} afbeelding(en)`;
                }
                
                // Show modal
                document.getElementById('addItemModal').style.display = 'block';
            }
            
            async deleteItem(itemId) {
                const item = this.items.find(i => i.id === itemId);
                if (!item) return;
                
                if (!confirm(`Weet je zeker dat je "${item.name}" wilt verwijderen?`)) {
                    return;
                }
                
                this.items = this.items.filter(i => i.id !== itemId);
                await this.saveItems();
                this.renderItems();
            }
            
            formatPrice(price) {
                return new Intl.NumberFormat('nl-NL').format(price);
            }
            
            escapeHTML(text) {
                if (text == null) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        }
        
        // Global instance
        let forSaleManager;
        
        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            forSaleManager = new ForSaleManagement();
        });
        
        // Modal functions
        function showAddItemModal() {
            // Reset pending upload state
            if (forSaleManager) {
                forSaleManager.pendingMainImageFile = null;
                forSaleManager.pendingAdditionalFiles = [];
                forSaleManager.currentMainImageUrl = null;
                forSaleManager.currentAdditionalUrls = [];
            }

            // Reset form
            document.getElementById('addItemForm').reset();
            document.getElementById('editItemId').value = '';
            document.getElementById('modalTitle').textContent = 'Nieuw item toevoegen';
            document.getElementById('submitBtn').textContent = 'Item toevoegen';
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('mainImagePreview').src = '../frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg';
            document.getElementById('mainImageName').textContent = 'Geen bestand gekozen';
            document.getElementById('additionalImagesPreview').innerHTML = '';
            document.getElementById('additionalImagesName').textContent = 'Geen bestanden gekozen';
            
            document.getElementById('addItemModal').style.display = 'block';
        }
        
        function closeAddItemModal() {
            document.getElementById('addItemModal').style.display = 'none';
            // Reset pending upload state on close
            if (forSaleManager) {
                forSaleManager.pendingMainImageFile = null;
                forSaleManager.pendingAdditionalFiles = [];
                forSaleManager.currentMainImageUrl = null;
                forSaleManager.currentAdditionalUrls = [];
            }
        }

        async function runImageMigration() {
            const btn = document.getElementById('migrateBtn');
            if (!confirm('Wil je bestaande base64-afbeeldingen converteren naar serverbestanden? Dit hoeft slechts één keer te worden uitgevoerd.')) return;
            btn.disabled = true;
            btn.textContent = 'Bezig...';
            try {
                if (!adminSession.csrfToken) await refreshAdminSession();
                const endpoint = window.AppConfig.detectServerEndpoint();
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': adminSession.csrfToken || '' },
                    credentials: 'include',
                    body: JSON.stringify({ action: 'migrateForSaleImages' })
                });
                const result = await response.json();
                if (result.success) {
                    alert(`Migratie voltooid: ${result.migrated} afbeelding(en) geconverteerd.`);
                    await forSaleManager.loadItems();
                    forSaleManager.renderItems();
                } else {
                    alert('Migratie mislukt: ' + (result.message || 'Onbekende fout'));
                }
            } catch (e) {
                alert('Migratie mislukt: ' + e.message);
            } finally {
                btn.disabled = false;
                btn.textContent = 'Afbeeldingen migreren';
            }
        }
        
        // Close modal on outside click
        window.onclick = function(event) {
            const modal = document.getElementById('addItemModal');
            if (event.target === modal) {
                closeAddItemModal();
            }
        };
        
        // Logout function
        async function logout() {
            if (!adminSession.csrfToken) {
                await refreshAdminSession();
            }
            const csrf = adminSession.csrfToken || '';
            
            try {
                const endpoint = window.AppConfig.detectServerEndpoint();
                
                
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

