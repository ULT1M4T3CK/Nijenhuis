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
        
        this.init();
    }

    init() {
        if (!this.chatButton || !this.chatWindow) return;

        // Toggle chat window and add welcome message
        this.chatButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            this.chatWindow.classList.toggle('active');
            
            if (this.chatWindow.classList.contains('active')) {
                this.chatInput.focus();
                // Add welcome message if this is the first time opening
                if (this.chatMessages.children.length === 0) {
                    setTimeout(() => {
                        this.addMessage('Hallo! Hoe kan ik u helpen met botenverhuur?', 'bot');
                    }, 500);
                }
            }
        });

        // Close chat window
        this.chatClose.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.chatWindow.classList.remove('active');
        });

        // Send message on Enter key
        this.chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Send message on button click
        this.chatSend.addEventListener('click', () => this.sendMessage());

        // Add language detection indicator
        this.addLanguageIndicator();
    }

    async sendMessage() {
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
document.addEventListener('DOMContentLoaded', function() {
    window.simpleChatbot = new SimpleChatbot();
    
    // Add some helpful console messages
    console.log('🤖 Simple Chatbot initialized!');
    console.log('💡 Use window.simpleChatbot to interact with the chatbot programmatically');
    console.log('📝 Example: window.simpleChatbot.openChat()');
});

// Add keyboard shortcut to open chat (Ctrl/Cmd + Shift + C)
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            if (window.simpleChatbot) {
                window.simpleChatbot.openChat();
            }
        }
    });
}); 
