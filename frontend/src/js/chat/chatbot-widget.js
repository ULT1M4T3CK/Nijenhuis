/**
 * Chatbot Widget JavaScript for Nijenhuis Website
 * Standalone widget that can be integrated into any existing website
 */

class NijenhuisChatbot {
    constructor() {
        this.isOpen = false;
        this.isTyping = false;
        this.apiEndpoint = 'http://localhost:5000/api/chat'; // Update this for production
        this.init();
    }

    init() {
        this.bindEvents();
        this.addPulseAnimation();
    }

    bindEvents() {
        // Chat toggle
        const chatToggle = document.getElementById('chat-toggle');
        const chatHeader = document.getElementById('chat-header');

        if (chatToggle) {
            chatToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleChat();
            });
        }

        if (chatHeader) {
            chatHeader.addEventListener('click', () => this.toggleChat());
        }

        // Send message
        const sendButton = document.getElementById('send-message');
        const chatInput = document.getElementById('chat-input');

        if (sendButton) {
            sendButton.addEventListener('click', () => this.sendMessage());
        }

        if (chatInput) {
            chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.sendMessage();
                }
            });

            // Auto-focus input when chat opens
            chatInput.addEventListener('focus', () => {
                if (!this.isOpen) {
                    this.toggleChat();
                }
            });
        }

        // Close chat when clicking outside
        document.addEventListener('click', (e) => {
            const chatWidget = document.getElementById('chat-widget');
            if (chatWidget && !chatWidget.contains(e.target) && this.isOpen) {
                this.toggleChat();
            }
        });
    }

    toggleChat() {
        const chatBody = document.getElementById('chat-body');
        const chatToggle = document.getElementById('chat-toggle');
        const chatWidget = document.getElementById('chat-widget');
        
        this.isOpen = !this.isOpen;
        
        if (this.isOpen) {
            chatBody.style.display = 'flex';
            chatToggle.innerHTML = '<i class="fas fa-times"></i>';
            document.getElementById('chat-input').focus();
            chatWidget.classList.remove('pulse');
        } else {
            chatBody.style.display = 'none';
            chatToggle.innerHTML = '<i class="fas fa-comments"></i>';
        }
    }

    async sendMessage() {
        const chatInput = document.getElementById('chat-input');
        const message = chatInput.value.trim();
        
        if (!message || this.isTyping) return;

        // Clear input
        chatInput.value = '';

        // Add user message to chat
        this.addMessage(message, 'user');

        // Show typing indicator
        this.showTypingIndicator();

        try {
            // Send message to API
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Remove typing indicator
                this.hideTypingIndicator();
                
                // Add bot response
                this.addMessage(data.response, 'bot');
                
                // Show language info if not English
                if (data.detected_language && data.detected_language !== 'en') {
                    this.showLanguageInfo(data.detected_language);
                }
            } else {
                throw new Error(data.error || 'Failed to send message');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.hideTypingIndicator();
            this.addMessage('Sorry, I encountered an error. Please try again.', 'bot');
        }
    }

    addMessage(content, sender) {
        const chatMessages = document.getElementById('chat-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        messageDiv.innerHTML = `
            <div class="message-content">
                ${this.escapeHtml(content)}
            </div>
            <div class="message-time">${time}</div>
        `;
        
        chatMessages.appendChild(messageDiv);
        this.scrollToBottom();
    }

    showTypingIndicator() {
        this.isTyping = true;
        const chatMessages = document.getElementById('chat-messages');
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot-message';
        typingDiv.id = 'typing-indicator';
        
        typingDiv.innerHTML = `
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
        
        chatMessages.appendChild(typingDiv);
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        this.isTyping = false;
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    showLanguageInfo(language) {
        const languageNames = {
            'en': 'English',
            'es': 'Spanish',
            'fr': 'French',
            'de': 'German',
            'it': 'Italian'
        };
        
        const languageName = languageNames[language] || language;
        
        // Create language info element
        const infoDiv = document.createElement('div');
        infoDiv.className = 'message bot-message';
        infoDiv.innerHTML = `
            <div class="message-content language-info" style="font-size: 0.8rem; color: #666; background: #f0f0f0;">
                <i class="fas fa-language"></i> Detected language: ${languageName}
            </div>
        `;
        
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.appendChild(infoDiv);
        this.scrollToBottom();
        
        // Remove language info after 5 seconds
        setTimeout(() => {
            if (infoDiv.parentNode) {
                infoDiv.remove();
            }
        }, 5000);
    }

    scrollToBottom() {
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    addPulseAnimation() {
        // Add pulse animation for first-time visitors
        if (!localStorage.getItem('nijenhuis_chat_visited')) {
            setTimeout(() => {
                const chatWidget = document.getElementById('chat-widget');
                if (chatWidget) {
                    chatWidget.classList.add('pulse');
                }
            }, 3000);
            
            localStorage.setItem('nijenhuis_chat_visited', 'true');
        }
    }

    // Public method to update API endpoint
    setApiEndpoint(endpoint) {
        this.apiEndpoint = endpoint;
    }

    // Public method to send a message programmatically
    sendMessageProgrammatically(message) {
        const chatInput = document.getElementById('chat-input');
        if (chatInput) {
            chatInput.value = message;
            this.sendMessage();
        }
    }

    // Public method to open chat
    openChat() {
        if (!this.isOpen) {
            this.toggleChat();
        }
    }

    // Public method to close chat
    closeChat() {
        if (this.isOpen) {
            this.toggleChat();
        }
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Create global instance
    window.nijenhuisChatbot = new NijenhuisChatbot();
    
    // Add some helpful console messages
    console.log('🤖 Nijenhuis Chatbot initialized!');
    console.log('💡 Use window.nijenhuisChatbot to interact with the chatbot programmatically');
    console.log('📝 Example: window.nijenhuisChatbot.openChat()');
});

// Add some helpful features
document.addEventListener('DOMContentLoaded', () => {
    // Add keyboard shortcut to open chat (Ctrl/Cmd + Shift + C)
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            if (window.nijenhuisChatbot) {
                window.nijenhuisChatbot.openChat();
            }
        }
    });

    // Add some helpful CSS for better integration
    const style = document.createElement('style');
    style.textContent = `
        /* Ensure chatbot appears above other elements */
        .chat-widget {
            z-index: 10000 !important;
        }
        
        /* Smooth transitions */
        .chat-body {
            transition: all 0.3s ease;
        }
        
        /* Better focus styles */
        #chat-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }
    `;
    document.head.appendChild(style);
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NijenhuisChatbot;
} 