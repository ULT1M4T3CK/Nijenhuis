// Shared JavaScript functionality for Nijenhuis website

// Enhanced Mobile Menu Functionality
function setupMobileMenu() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.querySelector('.nav-menu');

    if (!mobileToggle || !navMenu) {
        console.log('Mobile menu elements not found:', { mobileToggle: !!mobileToggle, navMenu: !!navMenu });
        return;
    }

    console.log('Setting up enhanced mobile menu...');

    // Enhanced toggle function with improved mobile support
    const toggleMenu = (e) => {
        console.log('Toggle menu called', e.type);
        e.preventDefault();
        e.stopPropagation();
        
        const isActive = navMenu.classList.contains('active');
        navMenu.classList.toggle('active');
        mobileToggle.setAttribute('aria-expanded', !isActive);
        
        // Add haptic feedback on supported devices
        if (navigator.vibrate) {
            navigator.vibrate(50);
        }
        
        // Prevent body scroll when menu is open
        document.body.style.overflow = isActive ? 'auto' : 'hidden';
        
        updateMobileMenuIcon();
        console.log('Menu toggled, active:', !isActive);
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

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!newMobileToggle.contains(e.target) && !navMenu.contains(e.target)) {
            navMenu.classList.remove('active');
            newMobileToggle.setAttribute('aria-expanded', 'false');
            updateMobileMenuIcon();
        }
    });

    // Close menu when touching outside (for mobile)
    document.addEventListener('touchend', (e) => {
        if (!newMobileToggle.contains(e.target) && !navMenu.contains(e.target)) {
            navMenu.classList.remove('active');
            newMobileToggle.setAttribute('aria-expanded', 'false');
            updateMobileMenuIcon();
        }
    });

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
            existingSvg.innerHTML = `<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>`;
        }
    } else {
        // Change to hamburger icon
        if (existingSvg) {
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

    // Remove any dark mode toggle in chat header
    if (chatHeader) {
        const dmBtn = chatHeader.querySelector('.dark-mode-toggle');
        if (dmBtn) dmBtn.remove();
    }

    // Update chat close button to a modern icon
    if (chatHeader && chatClose) {
        chatClose.innerHTML = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="11" stroke="#fff" fill="rgba(0,0,0,0.12)"/><line x1="15" y1="9" x2="9" y2="15" stroke="#fff"/><line x1="9" y1="9" x2="15" y2="15" stroke="#fff"/></svg>';
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
        
        const time = new Date().toLocaleTimeString('nl-NL', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        messageDiv.innerHTML = `
            <div class="message-bubble">
                <div class="message-text">${text}</div>
                <div class="message-time">${time}</div>
            </div>
        `;
        
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
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-message">${message}</div>
            <button class="notification-close" aria-label="Close notification">×</button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);

    // Close button functionality
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn?.addEventListener('click', () => {
        notification.remove();
    });
}

// Form Handling
function setupForms() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Versturen...';
            }
        });
    });
}

// --- Removed language switching and translation system code ---

    // Initialize all functionality when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM loaded, setting up mobile menu...');
        setupMobileMenu();
        setupChatWidget();
        setupForms();
        
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
    setupChatWidget,
    showNotification,
    setupForms
}; 