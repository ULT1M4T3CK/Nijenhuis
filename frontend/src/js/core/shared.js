// Shared JavaScript functionality for Nijenhuis website

// SECURITY: One-time cleanup of legacy PII cached in localStorage. Older
// versions of this site stored full customer bookings (name, email, phone,
// address, notes) under `nijenhuis_bookings`. That data is now served from
// the backend only; remove any leftover entries from previous sessions so
// an attacker exploiting a future XSS can't read historical PII.
(function () {
    try {
        if (localStorage.getItem('nijenhuis_bookings') !== null) {
            localStorage.removeItem('nijenhuis_bookings');
        }
    } catch (e) { /* storage unavailable */ }
})();

// Mobile Menu Functionality
function setMobileMenuOpen(isOpen) {
    const navMenu = document.querySelector('.nav-menu');
    const mobileToggle = document.getElementById('mobileMenuToggle');
    if (!navMenu) {
        return;
    }
    navMenu.classList.toggle('active', isOpen);
    document.body.classList.toggle('nav-menu-open', isOpen);
    if (mobileToggle) {
        mobileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }
    updateMobileMenuIcon();
}

function setupMobileMenu() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.querySelector('.nav-menu');

    if (!mobileToggle || !navMenu) {
        console.log('Mobile menu elements not found:', { mobileToggle: !!mobileToggle, navMenu: !!navMenu });
        return;
    }

    console.log('Setting up mobile menu...');

    // Handle both click and touch events for better mobile support
    const toggleMenu = (e) => {
        console.log('Toggle menu called', e.type);
        e.preventDefault();
        e.stopPropagation();
        setMobileMenuOpen(!navMenu.classList.contains('active'));
        console.log('Menu toggled, active:', navMenu.classList.contains('active'));
    };

    // Remove any existing event listeners by cloning the element
    const newMobileToggle = mobileToggle.cloneNode(true);
    mobileToggle.parentNode.replaceChild(newMobileToggle, mobileToggle);

    // Add event listeners to the new element
    newMobileToggle.addEventListener('click', toggleMenu);
    newMobileToggle.addEventListener('touchend', toggleMenu);

    // Also add touchstart to prevent any issues
    newMobileToggle.addEventListener('touchstart', (e) => {
        e.preventDefault();
    });

    const closeMenuIfOutside = (e) => {
        if (!newMobileToggle.contains(e.target) && !navMenu.contains(e.target)) {
            setMobileMenuOpen(false);
        }
    };

    // Close menu when clicking outside
    document.addEventListener('click', closeMenuIfOutside);

    // Close menu when touching outside (for mobile)
    document.addEventListener('touchend', closeMenuIfOutside);

    console.log('Mobile menu setup complete');
}

function updateMobileMenuIcon() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.querySelector('.nav-menu');

    if (!mobileToggle || !navMenu) return;

    // Find the existing SVG element
    const existingSvg = mobileToggle.querySelector('svg');

    if (navMenu.classList.contains('active')) {
        // Change to close icon (X)
        if (existingSvg) {
            while (existingSvg.firstChild) existingSvg.removeChild(existingSvg.firstChild);
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', 'M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z');
            existingSvg.appendChild(path);
        }
    } else {
        // Change to hamburger icon
        if (existingSvg) {
            while (existingSvg.firstChild) existingSvg.removeChild(existingSvg.firstChild);
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', 'M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z');
            existingSvg.appendChild(path);
        }
    }
}

// Nav "More" Dropdown
function setupNavMoreDropdown() {
    const trigger = document.getElementById('navMoreTrigger');
    const dropdown = document.getElementById('navMoreDropdown');
    const wrapper = document.querySelector('.nav-dropdown-wrapper');

    if (!trigger || !dropdown || !wrapper) return;

    trigger.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const isOpen = wrapper.classList.contains('active');
        wrapper.classList.toggle('active');
        trigger.setAttribute('aria-expanded', !isOpen);
    });

    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
            wrapper.classList.remove('active');
            trigger.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            wrapper.classList.remove('active');
            trigger.setAttribute('aria-expanded', 'false');
        }
    });
}

// Chat Widget Functionality
function setupChatWidget() {
    const chatButton = document.getElementById('chatButton');
    const chatWindow = document.getElementById('chatWindow');
    const chatClose = document.getElementById('chatClose');
    const chatInput = document.getElementById('chatInput');
    const chatSend = document.getElementById('chatSend');
    const chatMessages = document.getElementById('chatMessages');
    const chatHeader = chatWindow ? chatWindow.querySelector('.chat-header') : null;

    if (!chatButton || !chatWindow) return;

    // Remove any dark mode toggle in chat header
    if (chatHeader) {
        const dmBtn = chatHeader.querySelector('.dark-mode-toggle');
        if (dmBtn) dmBtn.remove();
    }

    // Update chat close button to a modern icon (build safely)
    if (chatHeader && chatClose) {
        while (chatClose.firstChild) chatClose.removeChild(chatClose.firstChild);
        const svgNS = 'http://www.w3.org/2000/svg';
        const svg = document.createElementNS(svgNS, 'svg');
        svg.setAttribute('width', '28');
        svg.setAttribute('height', '28');
        svg.setAttribute('viewBox', '0 0 24 24');
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', 'currentColor');
        svg.setAttribute('stroke-width', '2');
        svg.setAttribute('stroke-linecap', 'round');
        svg.setAttribute('stroke-linejoin', 'round');
        const circle = document.createElementNS(svgNS, 'circle');
        circle.setAttribute('cx', '12');
        circle.setAttribute('cy', '12');
        circle.setAttribute('r', '11');
        circle.setAttribute('stroke', '#fff');
        circle.setAttribute('fill', 'rgba(0,0,0,0.12)');
        const l1 = document.createElementNS(svgNS, 'line');
        l1.setAttribute('x1', '15'); l1.setAttribute('y1', '9'); l1.setAttribute('x2', '9'); l1.setAttribute('y2', '15'); l1.setAttribute('stroke', '#fff');
        const l2 = document.createElementNS(svgNS, 'line');
        l2.setAttribute('x1', '9'); l2.setAttribute('y1', '9'); l2.setAttribute('x2', '15'); l2.setAttribute('y2', '15'); l2.setAttribute('stroke', '#fff');
        svg.appendChild(circle); svg.appendChild(l1); svg.appendChild(l2);
        chatClose.appendChild(svg);
    }

    // On load, apply saved mode
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
    } else {
        document.body.classList.remove('dark-mode');
    }

    function getDarkModeIcon() {
        return document.body.classList.contains('dark-mode')
            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M6.995 12c0 2.761 2.246 5.004 5.005 5.004 2.76 0 5-2.243 5-5.004 0-2.76-2.24-5-5-5-2.759 0-5.005 2.24-5.005 5zm13.705-2.705c-.391-.391-1.023-.391-1.414 0-.391.391-.391 1.023 0 1.414.391.391 1.023.391 1.414 0 .391-.391.391-1.023 0-1.414zm-15.41 0c-.391.391-.391 1.023 0 1.414.391.391 1.023.391 1.414 0 .391-.391.391-1.023 0-1.414-.391-.391-1.023-.391-1.414 0zm7.705-7.295c-.552 0-1 .447-1 1v2c0 .553.448 1 1 1s1-.447 1-1v-2c0-.553-.448-1-1-1zm0 16c-.552 0-1 .447-1 1v2c0 .553.448 1 1 1s1-.447 1-1v-2c0-.553-.448-1-1-1zm9-7h-2c-.553 0-1 .447-1 1s.447 1 1 1h2c.553 0 1-.447 1-1s-.447-1-1-1zm-16 0h-2c-.553 0-1 .447-1 1s.447 1 1 1h2c.553 0 1-.447 1-1s-.447-1-1-1zm12.364 7.364c-.391-.391-1.023-.391-1.414 0-.391.391-.391 1.023 0 1.414.391.391 1.023.391 1.414 0 .391-.391.391-1.023 0-1.414zm-10.728 0c-.391.391-.391 1.023 0 1.414.391.391 1.023.391 1.414 0 .391-.391.391-1.023 0-1.414-.391-.391-1.023-.391-1.414 0z"/></svg>'
            : '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3.19V1h-1v2.19C6.16 3.72 2.72 7.16 2.19 12H1v1h2.19c.53 4.84 4.01 8.28 8.81 8.81V23h1v-2.19c4.84-.53 8.28-4.01 8.81-8.81H23v-1h-2.19c-.53-4.84-4.01-8.28-8.81-8.81zM12 21c-4.97 0-9-4.03-9-9s4.03-9 9-9 9 4.03 9 9-4.03 9-9 9z"/></svg>';
    }

    // Toggle chat window
    chatButton.addEventListener('click', () => {
        chatWindow.classList.add('active');
        chatInput.focus();
    });

    // Close chat window
    chatClose?.addEventListener('click', () => {
        chatWindow.classList.remove('active');
    });

    // Send message
    function sendMessage() {
        const message = chatInput.value.trim();
        if (message) {
            addMessage(message, 'user');
            chatInput.value = '';

            // Simulate bot response
            setTimeout(() => {
                addMessage('Bedankt voor uw bericht. We nemen zo snel mogelijk contact met u op.', 'bot');
            }, 1000);
        }
    }

    chatSend?.addEventListener('click', sendMessage);
    chatInput?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}`;
        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';
        const textEl = document.createElement('div');
        textEl.className = 'message-text';
        textEl.textContent = text;
        const timeEl = document.createElement('div');
        timeEl.className = 'message-time';
        timeEl.textContent = new Date().toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' });
        bubble.appendChild(textEl);
        bubble.appendChild(timeEl);
        messageDiv.appendChild(bubble);
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Add welcome message
    if (chatMessages && chatMessages.children.length === 0) {
        addMessage('Hallo! Hoe kunnen we u helpen?', 'bot');
    }
}

// Notification System
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    const content = document.createElement('div');
    content.className = 'notification-content';
    const msg = document.createElement('div');
    msg.className = 'notification-message';
    msg.textContent = message;
    const closeBtn = document.createElement('button');
    closeBtn.className = 'notification-close';
    closeBtn.setAttribute('aria-label', 'Close notification');
    closeBtn.textContent = '×';
    content.appendChild(msg);
    content.appendChild(closeBtn);
    notification.appendChild(content);
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
    closeBtn.addEventListener('click', () => notification.remove());
}

// Form Handling
function setupForms() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            // Skip booking form - it handles its own state
            if (form.id === 'bookingForm') {
                return;
            }
            
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                // Store original text if not already stored
                if (!submitBtn.dataset.originalText) {
                    submitBtn.dataset.originalText = submitBtn.textContent || submitBtn.innerText;
                }
                submitBtn.disabled = true;
                submitBtn.textContent = 'Versturen...';
            }
        });
    });
}

// --- Removed language switching and translation system code ---

// Service Worker Registration
function registerServiceWorker() {
    if ('serviceWorker' in navigator) {
        // Register service worker after page loads
        window.addEventListener('load', () => {
            // Always use absolute path to avoid path resolution issues
            const swPath = '/frontend/public/sw.js';
            const swUrl = new URL(swPath, window.location.origin).href;
            
            navigator.serviceWorker.register(swUrl, {
                scope: '/' // Register for entire site
            })
                .then((registration) => {
                    console.log('✅ Service Worker registered:', registration.scope);
                    
                    // Check for updates periodically (every hour)
                    setInterval(() => {
                        registration.update();
                    }, 60 * 60 * 1000);
                    
                    // Handle service worker updates
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        if (newWorker) {
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed') {
                                    if (navigator.serviceWorker.controller) {
                                        // New service worker available, force reload
                                        console.log('🔄 New service worker available. Reloading page...');
                                        window.location.reload();
                                    } else {
                                        // First time installation
                                        console.log('✅ Service Worker installed for the first time');
                                    }
                                }
                            });
                        }
                    });
                    
                    // Listen for controller changes (when new SW takes control)
                    navigator.serviceWorker.addEventListener('controllerchange', () => {
                        console.log('🔄 Service Worker controller changed, reloading...');
                        window.location.reload();
                    });
                })
                .catch((error) => {
                    console.warn('Service Worker registration failed:', error);
                    // Don't show error to user - service worker is optional
                });
        });
    } else {
        console.log('Service Worker not supported in this browser');
    }
}

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, setting up mobile menu...');
    setupMobileMenu();
    setupNavMoreDropdown();
    setupChatWidget();
    setupForms();
    registerServiceWorker(); // Register service worker

    // Test mobile menu functionality
    setTimeout(() => {
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const navMenu = document.querySelector('.nav-menu');
        console.log('Mobile menu test:', {
            mobileToggle: mobileToggle,
            navMenu: navMenu,
            mobileToggleVisible: mobileToggle ? window.getComputedStyle(mobileToggle).display : 'not found',
            navMenuVisible: navMenu ? window.getComputedStyle(navMenu).display : 'not found'
        });
    }, 1000);
});

// Export functions for use in other scripts
window.NijenhuisShared = {
    setupMobileMenu,
    setupNavMoreDropdown,
    setupChatWidget,
    showNotification,
    setupForms
};

// Centralized Application Configuration
window.AppConfig = {
    /**
     * Detects the appropriate API endpoint based on the current environment.
     * Handles transitions between local development (PHP or Python) and production.
     * 
     * @param {string} scriptName - The name of the PHP script (e.g., 'booking-handler.php')
     * @returns {string} The full URL to the endpoint
     */
    detectServerEndpoint: function (scriptName = 'booking-handler.php') {
        const isLocalPy = window.location.hostname === 'localhost' && window.location.port === '8000';
        const isFileProtocol = window.location.protocol === 'file:';

        if (isLocalPy || isFileProtocol) {
            // Fallback for Python local dev server or direct file access
            return 'http://localhost:8000/admin/' + scriptName.replace('.php', '.py');
        }

        // Standard PHP environment
        return window.location.origin + '/admin/' + scriptName;
    }
}; 