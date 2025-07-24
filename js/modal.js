// Modal-related JavaScript extracted from index.html

/**
 * Safely creates a text element with the given content
 * @param {string} tagName - The HTML tag name
 * @param {string} textContent - The text content
 * @param {string} className - Optional CSS class name
 * @returns {HTMLElement}
 */
function createSafeElement(tagName, textContent = '', className = '') {
    const element = document.createElement(tagName);
    if (textContent) {
        element.textContent = textContent;
    }
    if (className) {
        element.className = className;
    }
    return element;
}

/**
 * Safely creates an image element with validation
 * @param {string} src - Image source URL
 * @param {string} alt - Alt text
 * @param {string} className - CSS class name
 * @returns {HTMLElement}
 */
function createSafeImage(src, alt, className = '') {
    const img = document.createElement('img');
    
    // Basic URL validation - only allow relative paths and https URLs
    if (src && (src.startsWith('/') || src.startsWith('./') || src.startsWith('https://'))) {
        img.src = src;
    } else {
        img.src = '/Images/placeholder.jpg'; // Fallback image
    }
    
    img.alt = alt || 'Boat image';
    if (className) {
        img.className = className;
    }
    
    return img;
}

/**
 * Creates a safe list from an array of strings
 * @param {Array<string>} items - Array of list items
 * @param {string} className - CSS class for the list
 * @returns {HTMLElement}
 */
function createSafeList(items, className = '') {
    const ul = document.createElement('ul');
    if (className) {
        ul.className = className;
    }
    
    if (Array.isArray(items)) {
        items.forEach(item => {
            if (typeof item === 'string' && item.trim()) {
                const li = createSafeElement('li', item.trim());
                
                // Add check mark icon safely
                const checkIcon = document.createElement('span');
                checkIcon.textContent = '✓';
                checkIcon.style.color = 'var(--primary-color)';
                checkIcon.style.fontWeight = 'bold';
                checkIcon.style.marginRight = '10px';
                
                li.insertBefore(checkIcon, li.firstChild);
                ul.appendChild(li);
            }
        });
    }
    
    return ul;
}

/**
 * Creates specifications grid safely
 * @param {Object} specifications - Object with key-value pairs
 * @returns {HTMLElement}
 */
function createSpecificationsGrid(specifications) {
    const grid = createSafeElement('div', '', 'specifications-grid');
    
    if (specifications && typeof specifications === 'object') {
        Object.entries(specifications).forEach(([key, value]) => {
            if (typeof key === 'string' && typeof value === 'string') {
                const specItem = createSafeElement('div', '', 'spec-item');
                
                const keyElement = createSafeElement('strong', key + ':');
                const valueElement = createSafeElement('span', ' ' + value);
                
                specItem.appendChild(keyElement);
                specItem.appendChild(valueElement);
                grid.appendChild(specItem);
            }
        });
    }
    
    return grid;
}

function showBoatInfo(boatId) {
    // Validate input
    if (!boatId || typeof boatId !== 'string') {
        if (window.NijenhuisShared && window.NijenhuisShared.Logger) {
            window.NijenhuisShared.Logger.warn('Invalid boat ID provided to showBoatInfo');
        }
        return;
    }

    const boatData = window.boatData || {};
    const boat = boatData[boatId];
    
    if (!boat) {
        if (window.NijenhuisShared && window.NijenhuisShared.showNotification) {
            window.NijenhuisShared.showNotification('Boot informatie niet gevonden', 'error');
        }
        return;
    }

    // Validate boat data structure
    if (!boat.name || !boat.price || !boat.capacity) {
        if (window.NijenhuisShared && window.NijenhuisShared.Logger) {
            window.NijenhuisShared.Logger.warn('Incomplete boat data for:', boatId);
        }
        return;
    }

    // Create modal container
    const modal = createSafeElement('div', '', 'boat-modal');
    modal.id = 'boatModal';

    // Create modal content
    const modalContent = createSafeElement('div', '', 'boat-modal-content');

    // Create close button
    const closeButton = createSafeElement('span', '×', 'boat-modal-close');
    closeButton.setAttribute('aria-label', 'Sluit modal');
    closeButton.setAttribute('role', 'button');
    closeButton.setAttribute('tabindex', '0');
    closeButton.onclick = closeBoatModal;

    // Create header
    const header = createSafeElement('div', '', 'boat-modal-header');
    
    // Add image
    const image = createSafeImage(boat.image, boat.name, 'boat-modal-image');
    
    // Add title section
    const titleSection = createSafeElement('div', '', 'boat-modal-title');
    const title = createSafeElement('h2', boat.name);
    const price = createSafeElement('p', boat.price, 'boat-modal-price');
    const capacity = createSafeElement('p', boat.capacity, 'boat-modal-capacity');
    
    titleSection.appendChild(title);
    titleSection.appendChild(price);
    titleSection.appendChild(capacity);
    
    header.appendChild(image);
    header.appendChild(titleSection);

    // Create body
    const body = createSafeElement('div', '', 'boat-modal-body');

    // Description section
    if (boat.description) {
        const descSection = createSafeElement('div', '', 'boat-modal-section');
        const descTitle = createSafeElement('h3', 'Beschrijving');
        const descText = createSafeElement('p', boat.description);
        
        descSection.appendChild(descTitle);
        descSection.appendChild(descText);
        body.appendChild(descSection);
    }

    // Features section
    if (boat.features && Array.isArray(boat.features) && boat.features.length > 0) {
        const featuresSection = createSafeElement('div', '', 'boat-modal-section');
        const featuresTitle = createSafeElement('h3', 'Kenmerken');
        const featuresList = createSafeList(boat.features);
        
        featuresSection.appendChild(featuresTitle);
        featuresSection.appendChild(featuresList);
        body.appendChild(featuresSection);
    }

    // Specifications section
    if (boat.specifications && typeof boat.specifications === 'object') {
        const specsSection = createSafeElement('div', '', 'boat-modal-section');
        const specsTitle = createSafeElement('h3', 'Specificaties');
        const specsGrid = createSpecificationsGrid(boat.specifications);
        
        specsSection.appendChild(specsTitle);
        specsSection.appendChild(specsGrid);
        body.appendChild(specsSection);
    }

    // Create footer
    const footer = createSafeElement('div', '', 'boat-modal-footer');
    
    const closeBtn = createSafeElement('button', 'Sluiten', 'btn');
    closeBtn.onclick = closeBoatModal;
    
    const bookBtn = createSafeElement('button', 'Nu Boeken', 'btn btn-primary');
    bookBtn.onclick = () => bookBoat(boatId);
    
    footer.appendChild(closeBtn);
    footer.appendChild(bookBtn);

    // Assemble modal
    modalContent.appendChild(closeButton);
    modalContent.appendChild(header);
    modalContent.appendChild(body);
    modalContent.appendChild(footer);
    modal.appendChild(modalContent);

    // Add modal styles if not already present
    addModalStyles();

    // Add modal to page
    if (document.body) {
        document.body.appendChild(modal);
        
        // Show modal with accessibility support
        modal.style.display = 'flex';
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-labelledby', 'boat-modal-title');
        
        // Focus management
        closeButton.focus();
        
        // Keyboard support
        modal.addEventListener('keydown', handleModalKeydown);
    }
}

function handleModalKeydown(event) {
    if (event.key === 'Escape') {
        closeBoatModal();
    }
    
    // Trap focus within modal
    if (event.key === 'Tab') {
        const modal = document.getElementById('boatModal');
        if (!modal) return;
        
        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        if (event.shiftKey) {
            if (document.activeElement === firstElement) {
                lastElement.focus();
                event.preventDefault();
            }
        } else {
            if (document.activeElement === lastElement) {
                firstElement.focus();
                event.preventDefault();
            }
        }
    }
}

function addModalStyles() {
    if (document.getElementById('boatModalStyles')) {
        return; // Styles already added
    }
    
    const style = document.createElement('style');
    style.id = 'boatModalStyles';
    style.textContent = `
        .boat-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }
        .boat-modal-content {
            background: white;
            border-radius: 12px;
            max-width: 800px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }
        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .boat-modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #666;
            z-index: 1;
        }
        .boat-modal-close:hover,
        .boat-modal-close:focus { 
            color: #000; 
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
        .boat-modal-header { 
            display: flex; 
            gap: 20px; 
            padding: 30px; 
            border-bottom: 1px solid #eee; 
        }
        .boat-modal-image { 
            width: 200px; 
            height: 150px; 
            object-fit: cover; 
            border-radius: 8px; 
        }
        .boat-modal-title h2 { 
            color: var(--secondary-color); 
            margin-bottom: 10px; 
        }
        .boat-modal-price { 
            font-size: 1.5rem; 
            font-weight: bold; 
            color: var(--primary-color); 
            margin-bottom: 5px; 
        }
        .boat-modal-capacity { 
            color: #666; 
            font-size: 1.1rem; 
        }
        .boat-modal-body { 
            padding: 30px; 
        }
        .boat-modal-section { 
            margin-bottom: 30px; 
        }
        .boat-modal-section h3 { 
            color: var(--secondary-color); 
            margin-bottom: 15px; 
            font-size: 1.3rem; 
        }
        .boat-modal-section ul { 
            list-style: none; 
            padding: 0; 
        }
        .boat-modal-section li { 
            padding: 8px 0; 
            border-bottom: 1px solid #eee; 
        }
        .boat-modal-section li:last-child { 
            border-bottom: none; 
        }
        .specifications-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 15px; 
        }
        .spec-item { 
            padding: 10px; 
            background: #f8f9fa; 
            border-radius: 6px; 
            border-left: 4px solid var(--primary-color); 
        }
        .boat-modal-footer { 
            padding: 20px 30px; 
            border-top: 1px solid #eee; 
            display: flex; 
            gap: 15px; 
            justify-content: flex-end; 
        }
        .btn-primary { 
            background: var(--primary-color); 
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn-primary:hover,
        .btn-primary:focus { 
            background: var(--secondary-color); 
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
        .btn {
            background: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn:hover,
        .btn:focus {
            background: #e9e9e9;
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
        @media (max-width: 768px) {
            .boat-modal-header { 
                flex-direction: column; 
                text-align: center; 
            }
            .boat-modal-image { 
                width: 100%; 
                height: 200px; 
            }
            .specifications-grid { 
                grid-template-columns: 1fr; 
            }
            .boat-modal-footer { 
                flex-direction: column; 
            }
        }
    `;
    
    if (document.head) {
        document.head.appendChild(style);
    }
}

function closeBoatModal() {
    const modal = document.getElementById('boatModal');
    if (modal && modal.parentNode) {
        // Remove keyboard event listener
        modal.removeEventListener('keydown', handleModalKeydown);
        modal.remove();
    }
}

function bookBoat(boatId) {
    // Validate input
    if (!boatId || typeof boatId !== 'string') {
        if (window.NijenhuisShared && window.NijenhuisShared.Logger) {
            window.NijenhuisShared.Logger.warn('Invalid boat ID provided to bookBoat');
        }
        return;
    }

    closeBoatModal();
    
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.scrollIntoView({ behavior: 'smooth' });
        
        const boatTypeSelect = document.getElementById('boatType');
        if (boatTypeSelect) {
            // Sanitize the boat ID before setting
            const sanitizedBoatId = boatId.replace(/[^a-zA-Z0-9\-_]/g, '');
            boatTypeSelect.value = sanitizedBoatId;
            
            // Trigger change event if needed
            const event = new Event('change', { bubbles: true });
            boatTypeSelect.dispatchEvent(event);
        }
    } else {
        // Fallback - show notification if form not found
        if (window.NijenhuisShared && window.NijenhuisShared.showNotification) {
            window.NijenhuisShared.showNotification('Scroll naar beneden om te boeken', 'info');
        }
    }
}

// Close modal when clicking outside - with proper event delegation
document.addEventListener('click', function(event) {
    const modal = document.getElementById('boatModal');
    if (modal && event.target === modal) {
        closeBoatModal();
    }
});

// Make functions available globally for inline event handlers (temporary compatibility)
window.showBoatInfo = showBoatInfo;
window.closeBoatModal = closeBoatModal;
window.bookBoat = bookBoat; 