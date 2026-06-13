/**
 * Secure Chatbot Widget for Nijenhuis Website
 * Enhanced widget with security, connection monitoring, and fallback mechanisms
 */

class SecureChatbotWidget {
    constructor(config = {}) {
        // Get API key from multiple sources: window config, Vite env, or config parameter
        let apiKey = config.apiKey;
        
        // Check window.CHATBOT_API_KEY (for static HTML pages)
        if (!apiKey && typeof window !== 'undefined' && window.CHATBOT_API_KEY) {
            apiKey = window.CHATBOT_API_KEY;
        }
        
        // Check Vite environment variable exposed on window (for Vite builds with injection)
        if (!apiKey && typeof window !== 'undefined' && window.VITE_CHATBOT_API_KEY) {
            apiKey = window.VITE_CHATBOT_API_KEY;
        }
        
        if (!apiKey) {
            console.warn('Chatbot API key not configured. Proceeding without API key (token-based auth expected).');
        }
        
        // Get API endpoint from multiple sources
        let apiEndpoint = config.apiEndpoint;
        if (!apiEndpoint && typeof window !== 'undefined' && window.CHATBOT_API_ENDPOINT) {
            apiEndpoint = window.CHATBOT_API_ENDPOINT;
        }
        if (!apiEndpoint && typeof window !== 'undefined' && window.VITE_CHATBOT_API_ENDPOINT) {
            apiEndpoint = window.VITE_CHATBOT_API_ENDPOINT;
        }
        if (!apiEndpoint) {
            apiEndpoint = 'http://localhost:5001/api/chat';
        }
        
        this.config = {
            apiEndpoint: apiEndpoint,
            apiKey: apiKey,
            showConnectionStatus: config.showConnectionStatus !== false,
            showSecurityIndicator: config.showSecurityIndicator !== false,
            autoReconnect: config.autoReconnect !== false,
            ...config
        };
        
        // Widget state
        this.isOpen = false;
        this.isTyping = false;
        this.messageHistory = [];
        this.conversationHistory = [];  // Full conversation context for token prediction
        this.maxConversationHistory = 50;  // Maximum number of messages to keep in history
        this.sessionId = this.generateSessionId();  // Unique session ID for context tracking
        this.connectionStatus = 'unknown';
        this.userLanguage = this.detectUserLanguage();  // Detect user's preferred language
        
        // Listen for language changes from the language switcher
        window.addEventListener('languageChanged', (e) => {
            if (e.detail && e.detail.language) {
                this.userLanguage = e.detail.language;
                // Store in sessionStorage for chatbot-specific use
                sessionStorage.setItem('chatbot_language', this.userLanguage);
                // Update welcome message
                this.setWelcomeMessage();
                // Update input placeholder if elements exist
                if (this.elements && this.elements.input) {
                    const placeholders = {
                        'nl': 'Typ uw bericht...',
                        'en': 'Type your message...',
                        'de': 'Geben Sie Ihre Nachricht ein...'
                    };
                    if (!this.elements.input.disabled) {
                        this.elements.input.placeholder = placeholders[this.userLanguage] || placeholders['nl'];
                    }
                }
            }
        });
        
        // Initialize secure client
        this.client = new SecureChatbotClient({
            apiEndpoint: this.config.apiEndpoint,
            apiKey: this.config.apiKey,
            autoReconnect: this.config.autoReconnect
        });
        
        // Bind methods
        this.toggleChat = this.toggleChat.bind(this);
        this.sendMessage = this.sendMessage.bind(this);
        this.handleConnectionChange = this.handleConnectionChange.bind(this);
        this.handleMessage = this.handleMessage.bind(this);
        this.handleError = this.handleError.bind(this);
        
        this.init();
    }
    
    init() {
        this.createWidget();
        this.setWelcomeMessage();
        this.bindEvents();
        this.setupClientListeners();
        this.addSecurityIndicators();
        
        // Enable input initially if we have internet connection
        // The connection status will be updated after health check
        // Use setTimeout to ensure elements are fully created
        setTimeout(() => {
            if (this.elements && this.elements.input && navigator.onLine !== false) {
                this.elements.input.disabled = false;
                this.elements.send.disabled = false;
                const placeholders = {
                    'nl': 'Typ uw bericht...',
                    'en': 'Type your message...',
                    'de': 'Geben Sie Ihre Nachricht ein...'
                };
                this.elements.input.placeholder = placeholders[this.userLanguage] || placeholders['nl'];
            }
        }, 100);
    }
    
    detectUserLanguage() {
        // First check the main language switcher's stored preference (localStorage)
        const mainLang = localStorage.getItem('selected-language');
        if (mainLang && ['nl', 'en', 'de'].includes(mainLang)) {
            // Store in sessionStorage for chatbot-specific use
            sessionStorage.setItem('chatbot_language', mainLang);
            return mainLang;
        }
        
        // Fallback to chatbot-specific language preference
        const storedLang = sessionStorage.getItem('chatbot_language');
        if (storedLang && ['nl', 'en', 'de'].includes(storedLang)) {
            return storedLang;
        }
        
        // Detect from browser language
        const browserLang = navigator.language || navigator.userLanguage || 'nl';
        let detectedLang = 'nl'; // Default to Dutch
        
        if (browserLang.toLowerCase().startsWith('en')) {
            detectedLang = 'en';
        } else if (browserLang.toLowerCase().startsWith('de')) {
            detectedLang = 'de';
        }
        
        // Store detected language
        sessionStorage.setItem('chatbot_language', detectedLang);
        return detectedLang;
    }
    
    setWelcomeMessage() {
        // Set welcome message based on detected language
        const welcomeMessages = {
            'nl': { greeting: 'Hallo! 👋', text: 'Ik ben AlBot. Hoe kan ik u helpen met botenverhuur?' },
            'en': { greeting: 'Hello! 👋', text: 'I\'m AlBot. How can I help you with boat rental?' },
            'de': { greeting: 'Hallo! 👋', text: 'Ich bin AlBot. Wie kann ich Ihnen bei der Bootsvermietung helfen?' }
        };
        
        const welcome = welcomeMessages[this.userLanguage] || welcomeMessages['nl'];
        
        // Set welcome message after widget is created (using safe DOM manipulation)
        // Use requestAnimationFrame for better timing
        const updateWelcome = () => {
            const welcomeElement = document.getElementById('welcome-message-content');
            if (welcomeElement) {
                // Clear existing content
                welcomeElement.textContent = '';
                
                // Create greeting element
                const greetingEl = document.createElement('strong');
                greetingEl.textContent = welcome.greeting;
                welcomeElement.appendChild(greetingEl);
                
                // Add line break
                welcomeElement.appendChild(document.createElement('br'));
                
                // Add main text
                const textEl = document.createTextNode(welcome.text);
                welcomeElement.appendChild(textEl);
            } else {
                // If element not found, try again after a short delay
                setTimeout(updateWelcome, 100);
            }
        };
        
        // Try immediately, then with delay if needed
        if (document.getElementById('welcome-message-content')) {
            updateWelcome();
        } else {
            setTimeout(updateWelcome, 100);
        }
    }
    
    createWidget() {
        // Detect logo path from existing logo on page, or use default
        let logoPath = '../frontend/Images/logo-white.svg';
        const existingLogo = document.querySelector('.logo, .footer-logo, img[alt*="Nijenhuis"]');
        if (existingLogo && existingLogo.src) {
            try {
                const logoUrl = new URL(existingLogo.src);
                logoPath = logoUrl.pathname;
            } catch (e) {
                // If URL parsing fails, try to get relative path
                if (existingLogo.src.includes('logo-white.svg')) {
                    const src = existingLogo.src;
                    const pathMatch = src.match(/(\/|\.\.\/).*logo-white\.svg/);
                    if (pathMatch) {
                        logoPath = pathMatch[0].startsWith('/') ? pathMatch[0] : pathMatch[0];
                    }
                }
            }
        }
        
        // Determine AlBot avatar path (same directory as logo)
        let avatarPath = '../frontend/Images/AlBot.png';
        if (logoPath.includes('/Images/')) {
            avatarPath = logoPath.replace('logo-white.svg', 'AlBot.png');
        }
        
        // Create widget HTML
        const widgetHTML = `
            <div class="secure-chat-widget" id="secure-chat-widget">
                <button class="chat-toggle" id="secure-chat-toggle" aria-label="Open secure chat">
                    <img src="${logoPath}" alt="Nijenhuis" class="chat-toggle-logo" />
                    <span class="connection-indicator" id="connection-indicator"></span>
                </button>
                
                <div class="chat-window" id="secure-chat-window">
                    <div class="chat-header">
                        <div class="header-info">
                            <img src="${avatarPath}" alt="AlBot" class="chatbot-avatar" />
                            <h3>AlBot</h3>
                            <div class="security-badge" id="security-badge">
                                <i class="fas fa-shield-alt"></i>
                                <span>Secure</span>
                            </div>
                        </div>
                        <button class="chat-close" id="secure-chat-close" aria-label="Close chat">×</button>
                    </div>
                    
                    <div class="connection-status" id="connection-status" style="display: none;">
                        <div class="status-indicator">
                            <span class="status-dot"></span>
                            <span class="status-text">Connecting...</span>
                        </div>
                    </div>
                    
                    <div class="chat-messages" id="secure-chat-messages">
                        <div class="welcome-message">
                            <div class="message bot-message">
                                <div class="message-content" id="welcome-message-content">
                                    <!-- Welcome message will be set based on detected language -->
                                </div>
                                <div class="message-time">${new Date().toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' })}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chat-input-container">
                        <div class="input-wrapper">
                            <input type="text" id="secure-chat-input" placeholder="Type your message..." maxlength="1000" disabled>
                            <button class="send-button" id="secure-chat-send" disabled>
                                <svg class="send-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" fill="currentColor"/>
                                </svg>
                            </button>
                        </div>
                        <div class="input-footer">
                            <span class="char-count" id="char-count">0/1000</span>
                            <span class="security-info">
                                <i class="fas fa-lock"></i>
                                <span>End-to-end secure</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Add to page
        document.body.insertAdjacentHTML('beforeend', widgetHTML);
        
        // Store references
        this.elements = {
            widget: document.getElementById('secure-chat-widget'),
            toggle: document.getElementById('secure-chat-toggle'),
            window: document.getElementById('secure-chat-window'),
            close: document.getElementById('secure-chat-close'),
            messages: document.getElementById('secure-chat-messages'),
            input: document.getElementById('secure-chat-input'),
            send: document.getElementById('secure-chat-send'),
            connectionStatus: document.getElementById('connection-status'),
            connectionIndicator: document.getElementById('connection-indicator'),
            securityBadge: document.getElementById('security-badge'),
            charCount: document.getElementById('char-count')
        };
        
        // Styles are loaded from global stylesheet
    }
    
    injectStyles() {}
    
    bindEvents() {
        // Toggle chat
        this.elements.toggle.addEventListener('click', this.toggleChat);
        this.elements.close.addEventListener('click', this.toggleChat);
        
        // Send message
        this.elements.send.addEventListener('click', this.sendMessage);
        this.elements.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        // Character count
        this.elements.input.addEventListener('input', (e) => {
            const count = e.target.value.length;
            this.elements.charCount.textContent = `${count}/1000`;
            
            if (count > 800) {
                this.elements.charCount.className = 'char-count error';
            } else if (count > 600) {
                this.elements.charCount.className = 'char-count warning';
            } else {
                this.elements.charCount.className = 'char-count';
            }
        });
        
        // Close on outside click
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.elements.widget.contains(e.target)) {
                this.toggleChat();
            }
        });
    }
    
    setupClientListeners() {
        this.client.on('connectionChange', this.handleConnectionChange);
        this.client.on('message', this.handleMessage);
        this.client.on('error', this.handleError);
    }
    
    handleConnectionChange(data) {
        this.connectionStatus = data.status;
        
        // Update connection indicator
        this.elements.connectionIndicator.className = `connection-indicator ${data.status}`;
        
        // Update status display
        if (this.config.showConnectionStatus) {
            const statusElement = this.elements.connectionStatus;
            const statusText = statusElement.querySelector('.status-text');
            const statusDot = statusElement.querySelector('.status-dot');
            
            if (data.isConnected) {
                statusElement.style.display = 'none';
            } else {
                statusElement.style.display = 'block';
                statusText.textContent = this.getStatusText(data.status);
                statusDot.className = `status-dot ${data.status}`;
            }
        }
        
        // Enable/disable input
        // Only disable if truly offline (no internet), not just if chatbot server is down
        // If we have internet, allow messages (they'll be queued and retried)
        const hasInternet = navigator.onLine !== false;
        const isEnabled = data.isConnected || (hasInternet && data.status !== 'offline');
        
        this.elements.input.disabled = !isEnabled;
        this.elements.send.disabled = !isEnabled;
        
        if (!isEnabled) {
            const offlineMessages = {
                'nl': 'Chatbot is offline. Bel direct: 0522 281 528',
                'en': 'Chatbot is offline. Call directly: 0522 281 528',
                'de': 'Chatbot ist offline. Rufen Sie direkt an: 0522 281 528'
            };
            this.elements.input.placeholder = offlineMessages[this.userLanguage] || offlineMessages['nl'];
        } else {
            const placeholders = {
                'nl': 'Typ uw bericht...',
                'en': 'Type your message...',
                'de': 'Geben Sie Ihre Nachricht ein...'
            };
            this.elements.input.placeholder = placeholders[this.userLanguage] || placeholders['nl'];
        }
    }
    
    async handleMessage(data) {
        // Calculate human-like typing delay based on response length
        // Average typing speed: ~200 characters per minute = ~3.3 chars/second
        // Add base delay of 500ms + variable delay based on length
        const responseLength = data && data.response ? data.response.length : 0;
        const baseDelay = 800; // Base delay in ms (simulates thinking time)
        const typingSpeed = 30; // Characters per second (slightly slower than human average for realism)
        const variableDelay = Math.min(responseLength / typingSpeed * 1000, 4000); // Max 4 seconds
        const randomVariation = Math.random() * 500 - 250; // ±250ms randomization
        const totalDelay = Math.max(baseDelay + variableDelay + randomVariation, 500); // Minimum 500ms
        
        // Keep typing indicator visible during delay
        await this.delay(totalDelay);
        
        // Hide typing indicator and show response
        this.hideTypingIndicator();
        
        // Add assistant response to conversation history if not already added
        if (data && data.response) {
            const assistantMessage = { role: 'assistant', content: data.response };
            // Check if this message is already in history (avoid duplicates)
            const lastMessage = this.conversationHistory[this.conversationHistory.length - 1];
            if (!lastMessage || lastMessage.content !== data.response) {
                this.conversationHistory.push(assistantMessage);
                
                // Limit conversation history size to prevent memory leaks
                if (this.conversationHistory.length > this.maxConversationHistory) {
                    // Remove oldest messages (keep most recent)
                    const removeCount = this.conversationHistory.length - this.maxConversationHistory;
                    this.conversationHistory.splice(0, removeCount);
                }
            }
            
            // Update session ID if server returned a new one
            if (data.session_id) {
                this.sessionId = data.session_id;
            }
        }
        
        this.addMessage(data.response, 'bot', data);
        
        // Scroll to bottom to show new message (if chat is open)
        if (this.isOpen) {
            this.scrollToBottom();
        }
    }
    
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
    
    handleError(data) {
        this.hideTypingIndicator();
        
        let errorMessage = 'Sorry, I encountered an error. Please try again.';
        
        if (data.type === 'message_send_failed') {
            if (data.error.includes('Authentication failed')) {
                errorMessage = 'Authentication error. Please refresh the page.';
            } else if (data.error.includes('Rate limit')) {
                errorMessage = 'Too many requests. Please wait a moment.';
            } else if (data.error.includes('timeout')) {
                errorMessage = 'Connection timeout. Please check your internet connection.';
            }
        }
        
        this.addMessage(errorMessage, 'bot');
    }
    
    getStatusText(status) {
        const statusTexts = {
            'healthy': 'Connected',
            'degraded': 'Slow connection',
            'unhealthy': 'Connection issues',
            'offline': 'Offline',
            'reconnecting': 'Reconnecting...'
        };
        
        return statusTexts[status] || 'Unknown status';
    }
    
    toggleChat() {
        this.isOpen = !this.isOpen;
        
        if (this.isOpen) {
            this.elements.window.classList.add('active');
            this.elements.input.focus();
        } else {
            this.elements.window.classList.remove('active');
        }
    }
    
    async sendMessage() {
        const message = this.elements.input.value.trim();
        if (!message || this.isTyping) return;
        
        // Add user message to conversation history
        const userMessage = { role: 'user', content: message };
        this.conversationHistory.push(userMessage);
        
        // Limit conversation history size to prevent memory leaks
        if (this.conversationHistory.length > this.maxConversationHistory) {
            // Remove oldest messages (keep most recent)
            const removeCount = this.conversationHistory.length - this.maxConversationHistory;
            this.conversationHistory.splice(0, removeCount);
        }
        
        // Add user message to UI
        this.addMessage(message, 'user');
        this.elements.input.value = '';
        this.elements.charCount.textContent = '0/1000';
        this.elements.charCount.className = 'char-count';
        
        // Show typing indicator
        this.showTypingIndicator();
        
        try {
            // Send message with conversation history, session ID, and user language
            // The client will emit a 'message' event which will trigger handleMessage
            // handleMessage will add the delay and typing simulation
            await this.client.sendMessage(message, {
                conversation_history: this.conversationHistory.slice(0, -1),  // Exclude current message
                session_id: this.sessionId,
                use_token_prediction: true,
                language: this.userLanguage  // Pass user's selected language to backend
            });
            // Note: handleMessage is called automatically via the 'message' event listener
        } catch (error) {
            console.error('Failed to send message:', error);
            this.hideTypingIndicator();
            const errorMessages = {
                'nl': 'Bericht verzenden mislukt. Probeer het opnieuw.',
                'en': 'Failed to send message. Please try again.',
                'de': 'Nachricht senden fehlgeschlagen. Bitte versuchen Sie es erneut.'
            };
            this.addMessage(errorMessages[this.userLanguage] || errorMessages['nl'], 'bot');
        }
    }
    
    generateSessionId() {
        // Generate a cryptographically secure session ID using crypto.getRandomValues
        // This prevents session ID prediction and hijacking attacks
        if (typeof crypto !== 'undefined' && crypto.getRandomValues) {
            const array = new Uint32Array(4);
            crypto.getRandomValues(array);
            // Use only cryptographically secure random values, no timestamp
            return 'session_' + Array.from(array, dec => dec.toString(36)).join('');
        } else {
            // Fallback for older browsers - still use crypto if available
            if (typeof crypto !== 'undefined' && crypto.getRandomValues) {
                const array = new Uint8Array(16);
                crypto.getRandomValues(array);
                return 'session_' + Array.from(array, byte => byte.toString(36)).join('');
            }
            // Last resort fallback (should not happen in modern browsers)
            console.warn('Using insecure session ID generation - browser does not support crypto.getRandomValues');
            const array = new Uint32Array(4);
            for (let i = 0; i < array.length; i++) {
                array[i] = Math.floor(Math.random() * 0xFFFFFFFF);
            }
            return 'session_' + Array.from(array, dec => dec.toString(36)).join('');
        }
    }
    
    clearConversationHistory() {
        // Clear conversation history (for new conversation)
        this.conversationHistory = [];
        this.sessionId = this.generateSessionId();
    }
    
    addMessage(content, sender, data = {}) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const time = new Date().toLocaleTimeString('nl-NL', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        let messageContent = content;
        
        // Add special indicators for bot messages
        if (sender === 'bot' && data) {
            if (data.offline) {
                messageContent = `🔴 ${content}`;
            } else if (data.training_improved) {
                messageContent = `✨ ${content}`;
            } else if (data.neural_improved) {
                messageContent = `🧠 ${content}`;
            }
        }
        
        // Safely create message content
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.textContent = messageContent; // Already escaped by escapeHtml, use textContent for safety
        
        const timeDiv = document.createElement('div');
        timeDiv.className = 'message-time';
        timeDiv.textContent = time;
        
        messageDiv.appendChild(contentDiv);
        messageDiv.appendChild(timeDiv);
        
        this.elements.messages.appendChild(messageDiv);
        this.scrollToBottom();
        
        // Store in history
        this.messageHistory.push({
            content,
            sender,
            timestamp: new Date(),
            data
        });
    }
    
    showTypingIndicator() {
        this.isTyping = true;
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot-message typing-indicator-message';
        
        // Create typing indicator container
        const typingIndicator = document.createElement('div');
        typingIndicator.className = 'typing-indicator';
        
        // Add "AlBot is typing..." text
        const typingText = document.createElement('span');
        typingText.className = 'typing-text';
        typingText.textContent = 'AlBot is typing';
        typingIndicator.appendChild(typingText);
        
        // Create dots container
        const dotsContainer = document.createElement('div');
        dotsContainer.className = 'typing-dots-container';
        
        // Add three typing dots
        for (let i = 0; i < 3; i++) {
            const dot = document.createElement('div');
            dot.className = 'typing-dot';
            dotsContainer.appendChild(dot);
        }
        
        typingIndicator.appendChild(dotsContainer);
        typingDiv.appendChild(typingIndicator);
        
        this.elements.messages.appendChild(typingDiv);
        this.scrollToBottom();
    }
    
    hideTypingIndicator() {
        this.isTyping = false;
        const typingIndicator = this.elements.messages.querySelector('.typing-indicator-message');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }
    
    scrollToBottom() {
        this.elements.messages.scrollTop = this.elements.messages.scrollHeight;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    addSecurityIndicators() {
        if (this.config.showSecurityIndicator) {
            // Add security badge to header
            this.elements.securityBadge.style.display = 'flex';
        }
    }
    
    // Public methods
    openChat() {
        if (!this.isOpen) {
            this.toggleChat();
        }
    }
    
    closeChat() {
        if (this.isOpen) {
            this.toggleChat();
        }
    }
    
    setApiKey(apiKey) {
        this.client.setApiKey(apiKey);
    }
    
    getConnectionStatus() {
        return this.client.getConnectionStatus();
    }
    
    destroy() {
        this.client.destroy();
        if (this.elements.widget) {
            this.elements.widget.remove();
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Create global instance
    window.secureChatbotWidget = new SecureChatbotWidget();
    
    // Secure Chatbot Widget initialized
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SecureChatbotWidget;
}
