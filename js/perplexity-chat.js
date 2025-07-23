// Perplexity AI Chat Integration
// Shared chat functionality for all pages

class PerplexityChat {
    constructor() {
        this.API_KEY = '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';
        this.MODEL = 'sonar';
        this.chatButton = document.getElementById('chatButton');
        this.chatWindow = document.getElementById('chatWindow');
        this.chatClose = document.getElementById('chatClose');
        this.chatInput = document.getElementById('chatInput');
        this.chatSend = document.getElementById('chatSend');
        this.chatMessages = document.getElementById('chatMessages');
        
        this.init();
    }

    init() {
        if (!this.chatButton || !this.chatWindow) return;

        // Toggle chat window
        this.chatButton.addEventListener('click', () => {
            this.chatWindow.classList.toggle('active');
            if (this.chatWindow.classList.contains('active')) {
                this.chatInput.focus();
            }
        });

        // Close chat window
        this.chatClose.addEventListener('click', () => {
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

        // Add welcome message when chat is opened
        this.chatButton.addEventListener('click', () => {
            if (this.chatWindow.classList.contains('active') && this.chatMessages.children.length === 0) {
                setTimeout(() => {
                    this.addMessage('Hallo! Hoe kan ik u helpen met botenverhuur?', 'bot');
                }, 500);
            }
        });
    }

    async sendMessage() {
        const message = this.chatInput.value.trim();
        if (!message) return;

        // Add user message
        this.addMessage(message, 'user');
        this.chatInput.value = '';

        // Show typing indicator
        const typingIndicator = this.addTypingIndicator();

        try {
            // Call Perplexity AI API
            const response = await fetch('https://api.perplexity.ai/chat/completions', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.API_KEY}`,
                    'Content-Type': 'application/json'
                },
                                    body: JSON.stringify({
                        model: this.MODEL,
                        messages: [
                            {
                                role: 'system',
                                content: `Je bent een korte en behulpzame assistent voor Nijenhuis Botenverhuur. 

BELANGRIJKE REGELS:
- Geef altijd KORTE antwoorden (max 2-3 zinnen)
- Gebruik duidelijke, korte zinnen
- Verwijs naar de website voor meer informatie
- Wees vriendelijk maar direct

BEDRIJFSINFORMATIE:
📍 Veneweg 199, 7946 LP Wanneperveen
📞 0522 281 528
⏰ Dagelijks 09:00-18:00 (1 april - 1 november)

DIENSTEN:
🚤 Botenverhuur (elektrische, zeilboten, kano's)
🏠 Vakantiehuis
🏕️ Camping
⚓ Jachthaven
🗺️ Vaarkaart

PRIJZEN (dagprijzen):
- Tender 720: €230 (10-12 pers)
- Tender 570: €200 (8 pers)
- Electrosloep 10: €200 (10 pers)
- Electrosloep 8: €175 (8 pers)
- Zeilboot: €70-85 (4-5 pers)
- Kano/Kajak: €25 (2 pers)
- Sup Board: €35 (1 pers)

Antwoord in het Nederlands, kort en behulpzaam.`
                            },
                            {
                                role: 'user',
                                content: message
                            }
                        ],
                        max_tokens: 150,
                        temperature: 0.3
                    })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            const aiResponse = data.choices[0].message.content;

            // Remove typing indicator and add AI response
            this.removeTypingIndicator(typingIndicator);
            this.addMessage(aiResponse, 'bot');

        } catch (error) {
            console.error('Error calling Perplexity AI:', error);
            
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
        if (typingIndicator && typingIndicator.parentNode) {
            typingIndicator.parentNode.removeChild(typingIndicator);
        }
    }
}

// Initialize chat when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new PerplexityChat();
}); 