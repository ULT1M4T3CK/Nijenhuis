// Shared JavaScript functionality for Nijenhuis website

// Logger utility for production
const Logger = {
    enabled: false, // Set to true only in development
    log: function(message, data = null) {
        if (this.enabled && typeof console !== 'undefined') {
            console.log(message, data);
        }
    },
    warn: function(message, data = null) {
        if (this.enabled && typeof console !== 'undefined') {
            console.warn(message, data);
        }
    },
    error: function(message, data = null) {
        if (typeof console !== 'undefined') {
            console.error(message, data);
        }
    }
};

// Event controller for cleanup
class EventController {
    constructor() {
        this.controllers = new Map();
    }
    
    create(name) {
        if (this.controllers.has(name)) {
            this.controllers.get(name).abort();
        }
        const controller = new AbortController();
        this.controllers.set(name, controller);
        return controller;
    }
    
    cleanup(name) {
        if (this.controllers.has(name)) {
            this.controllers.get(name).abort();
            this.controllers.delete(name);
        }
    }
    
    cleanupAll() {
        this.controllers.forEach(controller => controller.abort());
        this.controllers.clear();
    }
}

const eventController = new EventController();

// Mobile Menu Functionality
function setupMobileMenu() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.querySelector('.nav-menu');

    if (!mobileToggle || !navMenu) {
        Logger.warn('Mobile menu elements not found', { 
            mobileToggle: !!mobileToggle, 
            navMenu: !!navMenu 
        });
        return;
    }

    Logger.log('Setting up mobile menu...');

    // Create event controller for mobile menu
    const controller = eventController.create('mobileMenu');

    // Handle both click and touch events for better mobile support
    const toggleMenu = (e) => {
        Logger.log('Toggle menu called', e.type);
        e.preventDefault();
        e.stopPropagation();
        
        if (!navMenu || !mobileToggle) return;
        
        const isActive = navMenu.classList.contains('active');
        navMenu.classList.toggle('active');
        mobileToggle.setAttribute('aria-expanded', !isActive);
        updateMobileMenuIcon();
        Logger.log('Menu toggled, active:', !isActive);
    };

    // Add event listeners with abort signal for cleanup
    mobileToggle.addEventListener('click', toggleMenu, { signal: controller.signal });
    mobileToggle.addEventListener('touchend', toggleMenu, { signal: controller.signal });
    
    // Prevent touch issues
    mobileToggle.addEventListener('touchstart', (e) => {
        e.preventDefault();
    }, { signal: controller.signal });

    // Close menu when clicking outside - with proper cleanup
    const closeOnOutsideClick = (e) => {
        if (!mobileToggle || !navMenu) return;
        
        if (!mobileToggle.contains(e.target) && !navMenu.contains(e.target)) {
            navMenu.classList.remove('active');
            mobileToggle.setAttribute('aria-expanded', 'false');
            updateMobileMenuIcon();
        }
    };

    document.addEventListener('click', closeOnOutsideClick, { signal: controller.signal });
    document.addEventListener('touchend', closeOnOutsideClick, { signal: controller.signal });

    Logger.log('Mobile menu setup complete');
}

function updateMobileMenuIcon() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.querySelector('.nav-menu');

    if (!mobileToggle || !navMenu) return;

    // Find the existing SVG element
    const existingSvg = mobileToggle.querySelector('svg');
    
    if (existingSvg) {
        if (navMenu.classList.contains('active')) {
            // Change to close icon (X) - using textContent for security
            existingSvg.innerHTML = `<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>`;
        } else {
            // Change to hamburger icon - using textContent for security
            existingSvg.innerHTML = `<path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>`;
        }
    }
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

    // Create event controller for chat
    const controller = eventController.create('chatWidget');

    // Remove any dark mode toggle in chat header
    if (chatHeader) {
        const dmBtn = chatHeader.querySelector('.dark-mode-toggle');
        if (dmBtn) dmBtn.remove();
    }

    // Update chat close button to a modern icon - using safe static content
    if (chatHeader && chatClose) {
        chatClose.innerHTML = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="11" stroke="#fff" fill="rgba(0,0,0,0.12)"/><line x1="15" y1="9" x2="9" y2="15" stroke="#fff"/><line x1="9" y1="9" x2="15" y2="15" stroke="#fff"/></svg>';
    }

    // Apply saved dark mode
    if (localStorage.getItem('darkMode') === '1') {
        document.body.classList.add('dark-mode');
    } else {
        document.body.classList.remove('dark-mode');
    }

    // Toggle chat window
    const toggleChat = () => {
        if (!chatWindow || !chatInput) return;
        chatWindow.classList.add('active');
        chatInput.focus();
    };

    // Close chat window
    const closeChat = () => {
        if (!chatWindow) return;
        chatWindow.classList.remove('active');
    };

    // Add event listeners with cleanup
    chatButton.addEventListener('click', toggleChat, { signal: controller.signal });
    if (chatClose) {
        chatClose.addEventListener('click', closeChat, { signal: controller.signal });
    }

    // Send message function
    function sendMessage() {
        if (!chatInput || !chatMessages) return;
        
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

    if (chatSend) {
        chatSend.addEventListener('click', sendMessage, { signal: controller.signal });
    }
    
    if (chatInput) {
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        }, { signal: controller.signal });
    }

    function addMessage(text, sender) {
        if (!chatMessages) return;
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}`;
        
        const time = new Date().toLocaleTimeString('nl-NL', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        // Create elements safely without innerHTML
        const messageBubble = document.createElement('div');
        messageBubble.className = 'message-bubble';
        
        const messageText = document.createElement('div');
        messageText.className = 'message-text';
        messageText.textContent = text; // Safe text content
        
        const messageTime = document.createElement('div');
        messageTime.className = 'message-time';
        messageTime.textContent = time;
        
        messageBubble.appendChild(messageText);
        messageBubble.appendChild(messageTime);
        messageDiv.appendChild(messageBubble);
        
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
    if (!message) return;
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    // Create notification content safely
    const notificationContent = document.createElement('div');
    notificationContent.className = 'notification-content';
    
    const notificationMessage = document.createElement('div');
    notificationMessage.className = 'notification-message';
    notificationMessage.textContent = message; // Safe text content
    
    const closeButton = document.createElement('button');
    closeButton.className = 'notification-close';
    closeButton.setAttribute('aria-label', 'Close notification');
    closeButton.textContent = '×';
    
    notificationContent.appendChild(notificationMessage);
    notificationContent.appendChild(closeButton);
    notification.appendChild(notificationContent);

    if (document.body) {
        document.body.appendChild(notification);
    }

    // Auto-remove after 5 seconds
    const removeNotification = () => {
        if (notification.parentNode) {
            notification.remove();
        }
    };

    setTimeout(removeNotification, 5000);

    // Close button functionality
    closeButton.addEventListener('click', removeNotification);
}

// Form Handling
function setupForms() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        if (!form) return;
        
        form.addEventListener('submit', (e) => {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Versturen...';
                
                // Re-enable after 3 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }, 3000);
            }
        });
    });
}

// Cleanup function for page unload
function cleanup() {
    eventController.cleanupAll();
}

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    Logger.log('DOM loaded, setting up components...');
    
    try {
        setupMobileMenu();
        setupChatWidget();
        setupForms();
        
        // Add cleanup on page unload
        window.addEventListener('beforeunload', cleanup);
        
        // Test mobile menu functionality after setup
        setTimeout(() => {
            const mobileToggle = document.getElementById('mobileMenuToggle');
            const navMenu = document.querySelector('.nav-menu');
            Logger.log('Mobile menu test:', {
                mobileToggle: !!mobileToggle,
                navMenu: !!navMenu,
                mobileToggleVisible: mobileToggle ? window.getComputedStyle(mobileToggle).display : 'not found',
                navMenuVisible: navMenu ? window.getComputedStyle(navMenu).display : 'not found'
            });
        }, 1000);
    } catch (error) {
        Logger.error('Error during initialization:', error);
    }
});

// Export functions for use in other scripts
window.NijenhuisShared = {
    setupMobileMenu,
    setupChatWidget,
    showNotification,
    setupForms,
    cleanup,
    Logger
}; 