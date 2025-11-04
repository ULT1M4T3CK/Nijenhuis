// Simple Chatbot Integration for Nijenhuis Website
// Lightweight, multilingual chatbot with website content analysis

class SimpleChatbot {
    constructor() {
        this.chatButton = document.getElementById('chatButton');
        this.chatWindow = document.getElementById('chatWindow');
        this.chatClose = document.getElementById('chatClose');
        this.chatInput = document.getElementById('chatInput');
        this.chatSend = document.getElementById('chatSend');
        this.chatMessages = document.getElementById('chatMessages');
        
        // Chat configuration
        this.isTyping = false;
        this.conversationHistory = [];
        
        // Debug logging
        console.log('[Chat] Elements found:', {
            chatButton: !!this.chatButton,
            chatWindow: !!this.chatWindow,
            chatClose: !!this.chatClose,
            chatInput: !!this.chatInput,
            chatSend: !!this.chatSend,
            chatMessages: !!this.chatMessages
        });
        
        this.init();
        this.injectBasicStyles();
    }

    init() {
        if (!this.chatButton || !this.chatWindow) {
            console.warn('[Chat] Missing chat elements:', {
                chatButton: !!this.chatButton,
                chatWindow: !!this.chatWindow
            });
            return;
        }

        // Check for all required elements
        if (!this.chatInput || !this.chatSend || !this.chatMessages) {
            console.error('[Chat] Missing required chat elements:', {
                chatInput: !!this.chatInput,
                chatSend: !!this.chatSend,
                chatMessages: !!this.chatMessages
            });
            return;
        }

        // Toggle chat window and add welcome message
        this.chatButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            this.chatWindow.classList.toggle('active');
            const isActive = this.chatWindow.classList.contains('active');
            this.chatWindow.style.display = isActive ? 'flex' : 'none';
            
            if (isActive) {
                if (this.chatInput) {
                    this.chatInput.focus();
                }
                // Add welcome message if this is the first time opening
                if (this.chatMessages) {
                    console.log('[Chat] Chat opened, message count:', this.chatMessages.children.length);
                    if (this.chatMessages.children.length === 0) {
                        console.log('[Chat] Adding welcome message');
                        setTimeout(() => {
                            this.addMessage('Hallo! Hoe kan ik u helpen met botenverhuur?', 'bot');
                        }, 500);
                    }
                } else {
                    console.error('[Chat] Cannot add welcome message: chatMessages not found');
                }
            }
        });

        // Close chat window
        if (this.chatClose) {
            this.chatClose.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.chatWindow.classList.remove('active');
                this.chatWindow.style.display = 'none';
            });
        }

        // Send message on Enter key
        this.chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                console.log('[Chat] Enter key pressed');
                this.sendMessage();
            }
        });

        // Send message on button click
        this.chatSend.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('[Chat] Send button clicked');
            this.sendMessage();
        });

        // Add language detection indicator
        this.addLanguageIndicator();
    }

    injectBasicStyles() {
        // Minimal styling to ensure the chat UI renders correctly without external widget CSS
        const style = document.createElement('style');
        style.setAttribute('data-chat-style', 'basic');
        style.textContent = `
          .chat-widget { position: fixed; bottom: 20px; right: 20px; z-index: 10000; }
          .chat-button { 
            width: 56px; height: 56px; border-radius: 50%; border: none; cursor: pointer;
            background: var(--primary-color, #667eea); color: #fff; font-size: 22px;
            box-shadow: 0 10px 20px rgba(0,0,0,.15);
            display: inline-flex; align-items: center; justify-content: center;
          }
          .chat-window { 
            display: none; width: 320px; max-width: calc(100vw - 40px);
            background: #fff; border-radius: 12px; overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,.2); margin-top: 10px;
          }
          .chat-window.active { display: flex; flex-direction: column; height: 420px; }
          .chat-header { background: linear-gradient(135deg,#667eea,#764ba2); color:#fff; padding: 10px 12px; display:flex; align-items:center; justify-content:space-between; }
          .chat-close { background: transparent; border: 0; color: #fff; font-size: 18px; cursor: pointer; }
          .chat-messages { flex: 1; padding: 12px; overflow-y: auto; background: #f8f9fa; }
          .chat-message { margin-bottom: 10px; }
          .chat-message .message-bubble { background: #fff; border: 1px solid #e5e7eb; padding: 8px 10px; border-radius: 10px; display: inline-block; max-width: 85%; }
          .chat-message.user .message-bubble { background: #667eea; color: #fff; border-color: transparent; }
          .chat-message .message-time { font-size: 11px; color: #6b7280; margin-top: 4px; }
          .chat-input { display:flex; gap:8px; padding: 10px; border-top: 1px solid #e5e7eb; }
          #chatInput { flex:1; border: 1px solid #d1d5db; border-radius: 20px; padding: 8px 12px; }
          .chat-send { width: 36px; height:36px; border-radius: 50%; border: none; background: #667eea; color:#fff; cursor: pointer; display:inline-flex; align-items:center; justify-content:center; }
        `;
        if (!document.querySelector('style[data-chat-style="basic"]')) {
            document.head.appendChild(style);
        }
    }

    async sendMessage() {
        if (!this.chatInput || !this.chatMessages) {
            console.error('[Chat] Cannot send message: chatInput or chatMessages not found');
            return;
        }

        const message = this.chatInput.value.trim();
        if (!message || this.isTyping) return;

        // Add user message
        this.addMessage(message, 'user');
        this.chatInput.value = '';

        // Add to conversation history
        this.conversationHistory.push({ role: 'user', content: message });

        // Show typing indicator
        const typingIndicator = this.addTypingIndicator();

        try {
            // Call simple chatbot API
            const response = await fetch('http://localhost:5001/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Chatbot error');
            }

            // Remove typing indicator
            this.removeTypingIndicator(typingIndicator);
            
            // Add bot response
            this.addMessage(data.response, 'bot');
            
            // Add to conversation history
            this.conversationHistory.push({ role: 'assistant', content: data.response });
            
            // Show language info if detected and not Dutch
            if (data.detected_language && data.detected_language !== 'nl') {
                this.showLanguageInfo(data.detected_language);
            }

        } catch (error) {
            console.error('Error in chat:', error);
            
            // Remove typing indicator
            this.removeTypingIndicator(typingIndicator);
            
            // Fallback response
            this.addMessage('Technische storing. Bel direct: 0522 281 528', 'bot');
        }
    }

    addMessage(text, sender) {
        if (!this.chatMessages) {
            console.error('[Chat] Cannot add message: chatMessages not found');
            return;
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}`;
        
        const time = new Date().toLocaleTimeString('nl-NL', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        // Check if the message contains a phone number and add call button
        let processedText = text;
        let hasCallButton = false;
        
        if (sender === 'bot' && text.includes('0522 281 528')) {
            processedText = text.replace('0522 281 528', '0522 281 528');
            hasCallButton = true;
        }
        
        // Fix bold formatting by replacing ** with <strong> tags
        processedText = processedText.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        let messageContent = `<div class="message-bubble">${processedText}</div>`;
        
        // Add call button below the text if needed
        if (hasCallButton) {
            messageContent += `<div class="chat-call-button-container"><a href="tel:0522281528" class="chat-call-button">📞 Bel Nu</a></div>`;
        }
        
        messageDiv.innerHTML = `
            ${messageContent}
            <div class="message-time">${time}</div>
        `;
        
        this.chatMessages.appendChild(messageDiv);
        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
    }

    addTypingIndicator() {
        if (!this.chatMessages) {
            console.error('[Chat] Cannot add typing indicator: chatMessages not found');
            this.isTyping = false;
            return null;
        }

        this.isTyping = true;
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message bot typing-indicator';
        typingDiv.innerHTML = `
            <div class="message-bubble">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        `;
        
        this.chatMessages.appendChild(typingDiv);
        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
        return typingDiv;
    }

    removeTypingIndicator(typingIndicator) {
        this.isTyping = false;
        if (typingIndicator && typingIndicator.parentNode) {
            typingIndicator.parentNode.removeChild(typingIndicator);
        }
    }

    showLanguageInfo(language) {
        const languageNames = {
            'en': 'Engels',
            'de': 'Duits'
        };
        
        const languageName = languageNames[language] || language;
        
        const infoDiv = document.createElement('div');
        infoDiv.className = 'chat-message bot language-info';
        infoDiv.innerHTML = `
            <div class="message-bubble language-info-bubble">
                <i class="fas fa-language"></i> Taal gedetecteerd: ${languageName}
            </div>
        `;
        
        this.chatMessages.appendChild(infoDiv);
        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
        
        // Remove language info after 5 seconds
        setTimeout(() => {
            if (infoDiv.parentNode) {
                infoDiv.remove();
            }
        }, 5000);
    }

    addLanguageIndicator() {
        // Add language indicator to chat header if it exists
        const chatHeader = this.chatWindow ? this.chatWindow.querySelector('.chat-header') : null;
        if (chatHeader) {
            const languageIndicator = document.createElement('div');
            languageIndicator.className = 'language-indicator';
            languageIndicator.innerHTML = `
                <span class="language-icon">🌍</span>
                <span class="language-text">Nederlands</span>
            `;
            languageIndicator.style.cssText = `
                position: absolute;
                right: 60px;
                top: 50%;
                transform: translateY(-50%);
                font-size: 12px;
                color: #fff;
                opacity: 0.8;
            `;
            chatHeader.appendChild(languageIndicator);
        }
    }

    // Public methods for external control
    clearHistory() {
        this.conversationHistory = [];
        this.chatMessages.innerHTML = '';
        this.addMessage('Hallo! Hoe kan ik u helpen met botenverhuur?', 'bot');
    }

    // Public method to open chat
    openChat() {
        if (!this.chatWindow.classList.contains('active')) {
            this.chatButton.click();
        }
    }

    // Public method to close chat
    closeChat() {
        if (this.chatWindow.classList.contains('active')) {
            this.chatClose.click();
        }
    }

    // Public method to send a message programmatically
    sendMessageProgrammatically(message) {
        const chatInput = document.getElementById('chat-input');
        if (chatInput) {
            chatInput.value = message;
            this.sendMessage();
        }
    }
}

// Initialize chat when DOM is loaded
function initializeChatbot() {
    if (!window.simpleChatbot) {
        window.simpleChatbot = new SimpleChatbot();
        
        // Add some helpful console messages
        console.log('🤖 Simple Chatbot initialized!');
        console.log('💡 Use window.simpleChatbot to interact with the chatbot programmatically');
        console.log('📝 Example: window.simpleChatbot.openChat()');
    }
}

// Check if DOM is already loaded
if (document.readyState === 'loading') {
    // DOM hasn't loaded yet, wait for it
    document.addEventListener('DOMContentLoaded', initializeChatbot);
} else {
    // DOM already loaded, initialize immediately
    initializeChatbot();
}

// Add keyboard shortcut to open chat (Ctrl/Cmd + Shift + C)
function setupKeyboardShortcut() {
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            if (window.simpleChatbot) {
                window.simpleChatbot.openChat();
            }
        }
    });
}

// Setup keyboard shortcut when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupKeyboardShortcut);
} else {
    setupKeyboardShortcut();
} 
