/**
 * Secure Chatbot Widget for Nijenhuis Website
 * Enhanced widget with security, connection monitoring, and fallback mechanisms
 */

class SecureChatbotWidget {
    constructor(config = {}) {
        this.config = {
            apiEndpoint: config.apiEndpoint || 'http://localhost:5001/api/chat',
            apiKey: config.apiKey || 'default_development_key',
            showConnectionStatus: config.showConnectionStatus !== false,
            showSecurityIndicator: config.showSecurityIndicator !== false,
            autoReconnect: config.autoReconnect !== false,
            ...config
        };
        
        // Widget state
        this.isOpen = false;
        this.isTyping = false;
        this.messageHistory = [];
        this.connectionStatus = 'unknown';
        
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
        this.bindEvents();
        this.setupClientListeners();
        this.addSecurityIndicators();
    }
    
    createWidget() {
        // Create widget HTML
        const widgetHTML = `
            <div class="secure-chat-widget" id="secure-chat-widget">
                <button class="chat-toggle" id="secure-chat-toggle" aria-label="Open secure chat">
                    <i class="fas fa-comments"></i>
                    <span class="connection-indicator" id="connection-indicator"></span>
                </button>
                
                <div class="chat-window" id="secure-chat-window">
                    <div class="chat-header">
                        <div class="header-info">
                            <h3>Nijenhuis Support</h3>
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
                                <div class="message-content">
                                    <strong>Hallo! 👋</strong><br>
                                    Ik ben de Nijenhuis chatbot. Hoe kan ik u helpen met botenverhuur?
                                </div>
                                <div class="message-time">${new Date().toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' })}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chat-input-container">
                        <div class="input-wrapper">
                            <input type="text" id="secure-chat-input" placeholder="Type your message..." maxlength="1000" disabled>
                            <button class="send-button" id="secure-chat-send" disabled>
                                <i class="fas fa-paper-plane"></i>
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
        
        this.injectStyles();
    }
    
    injectStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .secure-chat-widget {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 10000;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }
            
            .chat-toggle {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                border: none;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                font-size: 24px;
                cursor: pointer;
                box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
                transition: all 0.3s ease;
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .chat-toggle:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
            }
            
            .connection-indicator {
                position: absolute;
                top: -2px;
                right: -2px;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                border: 2px solid white;
                background: #ff4444;
                transition: background-color 0.3s ease;
            }
            
            .connection-indicator.connected {
                background: #44ff44;
            }
            
            .connection-indicator.degraded {
                background: #ffaa44;
            }
            
            .chat-window {
                position: absolute;
                bottom: 80px;
                right: 0;
                width: 380px;
                max-width: calc(100vw - 40px);
                height: 500px;
                background: white;
                border-radius: 16px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
                display: none;
                flex-direction: column;
                overflow: hidden;
            }
            
            .chat-window.active {
                display: flex;
            }
            
            .chat-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 16px 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            
            .header-info {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            
            .header-info h3 {
                margin: 0;
                font-size: 18px;
                font-weight: 600;
            }
            
            .security-badge {
                display: flex;
                align-items: center;
                gap: 4px;
                background: rgba(255, 255, 255, 0.2);
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 500;
            }
            
            .chat-close {
                background: none;
                border: none;
                color: white;
                font-size: 24px;
                cursor: pointer;
                padding: 4px;
                border-radius: 4px;
                transition: background-color 0.2s ease;
            }
            
            .chat-close:hover {
                background: rgba(255, 255, 255, 0.2);
            }
            
            .connection-status {
                padding: 8px 16px;
                background: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
                font-size: 14px;
            }
            
            .status-indicator {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .status-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #ff4444;
                animation: pulse 2s infinite;
            }
            
            .status-dot.connected {
                background: #44ff44;
            }
            
            .status-dot.degraded {
                background: #ffaa44;
            }
            
            @keyframes pulse {
                0% { opacity: 1; }
                50% { opacity: 0.5; }
                100% { opacity: 1; }
            }
            
            .chat-messages {
                flex: 1;
                padding: 16px;
                overflow-y: auto;
                background: #f8f9fa;
            }
            
            .message {
                margin-bottom: 16px;
                display: flex;
                flex-direction: column;
            }
            
            .message.user-message {
                align-items: flex-end;
            }
            
            .message.bot-message {
                align-items: flex-start;
            }
            
            .message-content {
                max-width: 80%;
                padding: 12px 16px;
                border-radius: 18px;
                font-size: 14px;
                line-height: 1.4;
                word-wrap: break-word;
            }
            
            .user-message .message-content {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            
            .bot-message .message-content {
                background: white;
                color: #333;
                border: 1px solid #e9ecef;
            }
            
            .message-time {
                font-size: 11px;
                color: #6c757d;
                margin-top: 4px;
                padding: 0 8px;
            }
            
            .typing-indicator {
                display: flex;
                align-items: center;
                gap: 4px;
                padding: 12px 16px;
                background: white;
                border-radius: 18px;
                border: 1px solid #e9ecef;
            }
            
            .typing-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #6c757d;
                animation: typing 1.4s infinite ease-in-out;
            }
            
            .typing-dot:nth-child(2) {
                animation-delay: 0.2s;
            }
            
            .typing-dot:nth-child(3) {
                animation-delay: 0.4s;
            }
            
            @keyframes typing {
                0%, 60%, 100% {
                    transform: translateY(0);
                }
                30% {
                    transform: translateY(-10px);
                }
            }
            
            .chat-input-container {
                padding: 16px;
                background: white;
                border-top: 1px solid #e9ecef;
            }
            
            .input-wrapper {
                display: flex;
                gap: 8px;
                align-items: center;
            }
            
            #secure-chat-input {
                flex: 1;
                border: 2px solid #e9ecef;
                border-radius: 24px;
                padding: 12px 16px;
                font-size: 14px;
                outline: none;
                transition: border-color 0.2s ease;
            }
            
            #secure-chat-input:focus {
                border-color: #667eea;
            }
            
            #secure-chat-input:disabled {
                background: #f8f9fa;
                color: #6c757d;
            }
            
            .send-button {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                border: none;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
            }
            
            .send-button:hover:not(:disabled) {
                transform: scale(1.05);
            }
            
            .send-button:disabled {
                background: #6c757d;
                cursor: not-allowed;
                transform: none;
            }
            
            .input-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 8px;
                font-size: 12px;
                color: #6c757d;
            }
            
            .security-info {
                display: flex;
                align-items: center;
                gap: 4px;
            }
            
            .char-count {
                font-weight: 500;
            }
            
            .char-count.warning {
                color: #ff6b6b;
            }
            
            .char-count.error {
                color: #ff4444;
            }
            
            /* Mobile responsiveness */
            @media (max-width: 480px) {
                .secure-chat-widget {
                    bottom: 10px;
                    right: 10px;
                }
                
                .chat-window {
                    width: calc(100vw - 20px);
                    height: calc(100vh - 100px);
                    bottom: 80px;
                    right: 0;
                }
            }
        `;
        
        document.head.appendChild(style);
    }
    
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
        const isEnabled = data.isConnected;
        this.elements.input.disabled = !isEnabled;
        this.elements.send.disabled = !isEnabled;
        
        if (!isEnabled) {
            this.elements.input.placeholder = 'Chatbot is offline. Please call 0522 281 528';
        } else {
            this.elements.input.placeholder = 'Type your message...';
        }
    }
    
    handleMessage(data) {
        this.hideTypingIndicator();
        this.addMessage(data.response, 'bot', data);
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
            this.elements.toggle.innerHTML = '<i class="fas fa-times"></i>';
        } else {
            this.elements.window.classList.remove('active');
            this.elements.toggle.innerHTML = '<i class="fas fa-comments"></i>';
        }
    }
    
    async sendMessage() {
        const message = this.elements.input.value.trim();
        if (!message || this.isTyping) return;
        
        // Add user message
        this.addMessage(message, 'user');
        this.elements.input.value = '';
        this.elements.charCount.textContent = '0/1000';
        this.elements.charCount.className = 'char-count';
        
        // Show typing indicator
        this.showTypingIndicator();
        
        try {
            await this.client.sendMessage(message);
        } catch (error) {
            console.error('Failed to send message:', error);
            this.hideTypingIndicator();
            this.addMessage('Failed to send message. Please try again.', 'bot');
        }
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
        
        messageDiv.innerHTML = `
            <div class="message-content">${this.escapeHtml(messageContent)}</div>
            <div class="message-time">${time}</div>
        `;
        
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
        typingDiv.innerHTML = `
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
        
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
    
    console.log('🔒 Secure Chatbot Widget initialized!');
    console.log('💡 Use window.secureChatbotWidget to interact with the widget');
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SecureChatbotWidget;
}
