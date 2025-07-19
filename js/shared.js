// Shared JavaScript functionality for Nijenhuis website

// Language Switcher Functionality
function setupLanguageSwitcher() {
    const languageSwitcher = document.querySelector('.language-switcher');
    const currentLang = document.querySelector('.current-lang');
    const langDropdown = document.querySelector('.lang-dropdown');
    const langOptions = document.querySelectorAll('.lang-option');

    if (!languageSwitcher) return;

    // Toggle dropdown on current language click
    currentLang?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        languageSwitcher.classList.toggle('active');
    });

    // Handle language option clicks
    langOptions.forEach(option => {
        option.addEventListener('click', (e) => {
            e.preventDefault();
            const lang = option.getAttribute('data-lang');
            if (lang) {
                // Update current language display
                const flagSpan = option.querySelector('.flag-circle').innerHTML;
                currentLang.innerHTML = `<span class="flag-circle">${flagSpan}</span> ${lang.toUpperCase()}`;
                
                // Update active state
                langOptions.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                
                // Close dropdown
                languageSwitcher.classList.remove('active');
                
                // Store language preference
                localStorage.setItem('preferred-language', lang);
                
                // Call translation function if it exists
                if (typeof switchLanguage === 'function') {
                    switchLanguage(lang);
                }
            }
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!languageSwitcher.contains(e.target)) {
            languageSwitcher.classList.remove('active');
        }
    });
}

// Mobile Menu Functionality
function setupMobileMenu() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.querySelector('.nav-menu');

    if (!mobileToggle || !navMenu) return;

    mobileToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        updateMobileMenuIcon();
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!mobileToggle.contains(e.target) && !navMenu.contains(e.target)) {
            navMenu.classList.remove('active');
            updateMobileMenuIcon();
        }
    });
}

function updateMobileMenuIcon() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.querySelector('.nav-menu');

    if (!mobileToggle || !navMenu) return;

    if (navMenu.classList.contains('active')) {
        mobileToggle.innerHTML = `<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
        </svg>`;
    } else {
        mobileToggle.innerHTML = `<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
        </svg>`;
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

    if (!chatButton || !chatWindow) return;

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

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    setupLanguageSwitcher();
    setupMobileMenu();
    setupChatWidget();
    setupForms();
    
    // Load preferred language
    const preferredLang = localStorage.getItem('preferred-language');
    if (preferredLang) {
        const langOption = document.querySelector(`[data-lang="${preferredLang}"]`);
        if (langOption) {
            langOption.click();
        }
    }
});

// Export functions for use in other scripts
window.NijenhuisShared = {
    setupLanguageSwitcher,
    setupMobileMenu,
    setupChatWidget,
    showNotification,
    setupForms
}; 