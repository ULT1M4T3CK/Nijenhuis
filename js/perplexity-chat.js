// Perplexity AI Chat Integration
// Shared chat functionality for all pages

class PerplexityChat {
    constructor() {
        this.API_KEY = '';
        this.MODEL = 'sonar';
        this.chatButton = document.getElementById('chatButton');
        this.chatWindow = document.getElementById('chatWindow');
        this.chatClose = document.getElementById('chatClose');
        this.chatInput = document.getElementById('chatInput');
        this.chatSend = document.getElementById('chatSend');
        this.chatMessages = document.getElementById('chatMessages');
        this.isInitialized = false;
        this.requestTimeout = 30000; // 30 seconds
        
        this.init();
    }

    init() {
        if (!this.chatButton || !this.chatWindow) {
            this.logError('Chat elements not found - chat functionality disabled');
            return;
        }

        try {
            this.setupEventListeners();
            this.isInitialized = true;
        } catch (error) {
            this.logError('Failed to initialize chat:', error);
        }
    }

    setupEventListeners() {
        // Toggle chat window
        this.chatButton.addEventListener('click', () => {
            this.toggleChat();
        });

        // Close chat window
        if (this.chatClose) {
            this.chatClose.addEventListener('click', () => {
                this.closeChat();
            });
        }

        // Send message on Enter key
        if (this.chatInput) {
            this.chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });
        }

        // Send message on button click
        if (this.chatSend) {
            this.chatSend.addEventListener('click', () => this.sendMessage());
        }

        // Add welcome message when chat is opened
        this.chatButton.addEventListener('click', () => {
            if (this.chatWindow.classList.contains('active') && 
                this.chatMessages && 
                this.chatMessages.children.length === 0) {
                setTimeout(() => {
                    this.addMessage('Hallo! Hoe kan ik u helpen met botenverhuur?', 'bot');
                }, 500);
            }
        });
    }

    toggleChat() {
        if (!this.chatWindow || !this.isInitialized) return;
        
        this.chatWindow.classList.toggle('active');
        if (this.chatWindow.classList.contains('active') && this.chatInput) {
            this.chatInput.focus();
        }
    }

    closeChat() {
        if (!this.chatWindow) return;
        this.chatWindow.classList.remove('active');
    }

    // Input sanitization
    sanitizeInput(input) {
        if (typeof input !== 'string') return '';
        
        // Remove potentially dangerous content
        const dangerous = [
            /<script[\s\S]*?>[\s\S]*?<\/script>/gi,
            /<iframe[\s\S]*?>[\s\S]*?<\/iframe>/gi,
            /<object[\s\S]*?>[\s\S]*?<\/object>/gi,
            /<embed[\s\S]*?>/gi,
            /javascript:/gi,
            /data:text\/html/gi,
            /vbscript:/gi,
            /on\w+\s*=/gi
        ];
        
        let sanitized = input;
        dangerous.forEach(pattern => {
            sanitized = sanitized.replace(pattern, '');
        });
        
        // Limit length
        return sanitized.substring(0, 1000).trim();
    }

    async sendMessage() {
        if (!this.isInitialized || !this.chatInput || !this.chatMessages) return;
        
        const rawMessage = this.chatInput.value.trim();
        if (!rawMessage) return;

        // Sanitize input
        const message = this.sanitizeInput(rawMessage);
        if (!message) {
            this.showError('Please enter a valid message.');
            return;
        }

        // Add user message
        this.addMessage(message, 'user');
        this.chatInput.value = '';

        // Show typing indicator
        const typingIndicator = this.addTypingIndicator();

        try {
            // Prepare request data
            const requestData = {
                model: this.MODEL,
                messages: [
                    {
                        role: 'system',
                        content: `Je bent een korte en behulpzame assistent voor Nijenhuis Botenverhuur. \n\nBELANGRIJKE REGELS:\n- Geef altijd KORTE antwoorden (max 2-3 zinnen)\n- Gebruik duidelijke, korte zinnen\n- Verwijs naar de website voor meer informatie\n- Wees vriendelijk maar direct\n\nBEDRIJFSINFORMATIE:\n📍 Veneweg 199, 7946 LP Wanneperveen\n📞 0522 281 528\n⏰ Dagelijks 09:00-18:00 (1 april - 1 november)\n\nDIENSTEN:\n🚤 Botenverhuur (elektrische, zeilboten, kano's)\n🏠 Vakantiehuis\n🏕️ Camping\n⚓ Jachthaven\n🗺️ Vaarkaart\n\nPRIJZEN (dagprijzen):\n- Tender 720: €230 (10-12 pers)\n- Tender 570: €200 (8 pers)\n- Electrosloep 10: €200 (10 pers)\n- Electrosloep 8: €175 (8 pers)\n- Zeilboot: €70-85 (4-5 pers)\n- Kano/Kajak: €25 (2 pers)\n- Sup Board: €35 (1 pers)\n\nAntwoord in het Nederlands, kort en behulpzaam.`
                    },
                    {
                        role: 'user',
                        content: message
                    }
                ],
                max_tokens: 150,
                temperature: 0.3
            };

            // Call backend Perplexity proxy with timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), this.requestTimeout);

            const response = await fetch('/api/perplexity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData),
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            
            if (!data.choices || !data.choices[0] || !data.choices[0].message) {
                throw new Error('Invalid response format from API');
            }

            const aiResponse = data.choices[0].message.content;

            // Remove typing indicator and add AI response
            this.removeTypingIndicator(typingIndicator);
            this.addMessage(this.sanitizeInput(aiResponse), 'bot');

        } catch (error) {
            this.logError('Error in chat request:', error);
            
            // Remove typing indicator
            this.removeTypingIndicator(typingIndicator);
            
            // Handle different error types
            if (error.name === 'AbortError') {
                this.addMessage('Verzoek duurt te lang. Probeer opnieuw of bel: 0522 281 528', 'bot');
            } else if (error.message.includes('429')) {
                this.addMessage('Te veel berichten. Wacht even en probeer opnieuw.', 'bot');
            } else if (error.message.includes('401') || error.message.includes('403')) {
                this.addMessage('Service tijdelijk niet beschikbaar. Bel direct: 0522 281 528', 'bot');
            } else if (error.message.includes('500') || error.message.includes('502') || error.message.includes('503')) {
                this.addMessage('Server probleem. Probeer later opnieuw of bel: 0522 281 528', 'bot');
            } else {
                this.addMessage('Technische storing. Bel direct: 0522 281 528', 'bot');
            }
        }
    }

    addMessage(text, sender) {
        if (!this.chatMessages || !text) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}`;
        
        const time = new Date().toLocaleTimeString('nl-NL', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        // Create message bubble safely
        const messageBubble = document.createElement('div');
        messageBubble.className = 'message-bubble';
        
        // Process text for phone number detection and safe rendering
        const processedContent = this.createMessageContent(text, sender);
        messageBubble.appendChild(processedContent);
        
        // Add time
        const timeElement = document.createElement('div');
        timeElement.className = 'message-time';
        timeElement.textContent = time;
        
        messageDiv.appendChild(messageBubble);
        messageDiv.appendChild(timeElement);
        
        this.chatMessages.appendChild(messageDiv);
        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
    }

    createMessageContent(text, sender) {
        const container = document.createElement('div');
        
        // Check for phone number and create call button if needed
        if (sender === 'bot' && text.includes('0522 281 528')) {
            const textPart = document.createElement('div');
            textPart.textContent = text;
            
            const callButtonContainer = document.createElement('div');
            callButtonContainer.className = 'chat-call-button-container';
            
            const callButton = document.createElement('a');
            callButton.href = 'tel:0522281528';
            callButton.className = 'chat-call-button';
            callButton.textContent = '📞 Bel Nu';
            callButton.setAttribute('aria-label', 'Bel Nijenhuis Botenverhuur');
            
            callButtonContainer.appendChild(callButton);
            container.appendChild(textPart);
            container.appendChild(callButtonContainer);
        } else {
            // Handle bold formatting safely
            if (text.includes('**')) {
                this.addFormattedText(container, text);
            } else {
                container.textContent = text;
            }
        }
        
        return container;
    }

    addFormattedText(container, text) {
        const parts = text.split(/(\*\*.*?\*\*)/g);
        
        parts.forEach(part => {
            if (part.startsWith('**') && part.endsWith('**')) {
                const strong = document.createElement('strong');
                strong.textContent = part.slice(2, -2);
                container.appendChild(strong);
            } else if (part) {
                const textNode = document.createTextNode(part);
                container.appendChild(textNode);
            }
        });
    }

    addTypingIndicator() {
        if (!this.chatMessages) return null;

        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message bot typing-indicator';
        
        const messageBubble = document.createElement('div');
        messageBubble.className = 'message-bubble';
        
        const typingDots = document.createElement('div');
        typingDots.className = 'typing-dots';
        
        // Create three dots safely
        for (let i = 0; i < 3; i++) {
            const dot = document.createElement('span');
            typingDots.appendChild(dot);
        }
        
        messageBubble.appendChild(typingDots);
        typingDiv.appendChild(messageBubble);
        
        this.chatMessages.appendChild(typingDiv);
        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
        
        return typingDiv;
    }

    removeTypingIndicator(typingIndicator) {
        if (typingIndicator && typingIndicator.parentNode) {
            typingIndicator.parentNode.removeChild(typingIndicator);
        }
    }

    showError(message) {
        if (window.NijenhuisShared && window.NijenhuisShared.showNotification) {
            window.NijenhuisShared.showNotification(message, 'error');
        }
    }

    logError(message, error = null) {
        if (window.NijenhuisShared && window.NijenhuisShared.Logger) {
            window.NijenhuisShared.Logger.error(message, error);
        } else if (typeof console !== 'undefined') {
            console.error(message, error);
        }
    }
}

// Initialize chat when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    try {
        new PerplexityChat();
    } catch (error) {
        if (typeof console !== 'undefined') {
            console.error('Failed to initialize PerplexityChat:', error);
        }
    }
}); 
