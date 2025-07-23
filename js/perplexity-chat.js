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

        // Language detection (simple)
        function detectLanguage(text) {
            // Very basic: look for common words
            const nlWords = ['de', 'het', 'een', 'en', 'ik', 'je', 'jij', 'u', 'zijn', 'niet', 'wel'];
            const deWords = ['und', 'ist', 'ein', 'ich', 'du', 'sie', 'nicht', 'ja', 'nein'];
            const enWords = ['the', 'and', 'is', 'are', 'you', 'your', 'not', 'yes', 'no'];
            const lower = text.toLowerCase();
            let nl = 0, de = 0, en = 0;
            nlWords.forEach(w => { if (lower.includes(' ' + w + ' ')) nl++; });
            deWords.forEach(w => { if (lower.includes(' ' + w + ' ')) de++; });
            enWords.forEach(w => { if (lower.includes(' ' + w + ' ')) en++; });
            if (nl >= de && nl >= en && nl > 0) return 'nl';
            if (de > nl && de >= en) return 'de';
            if (en > nl && en > de) return 'en';
            // fallback to browser language
            const browserLang = navigator.language || navigator.userLanguage;
            if (browserLang.startsWith('nl')) return 'nl';
            if (browserLang.startsWith('de')) return 'de';
            return 'en';
        }

        const lang = detectLanguage(message);

        // System prompts for each language
        const systemPrompts = {
            nl: `Je bent een korte en behulpzame assistent voor Nijenhuis Botenverhuur.\n\nBELANGRIJKE REGELS:\n- Geef altijd KORTE antwoorden (max 2-3 zinnen)\n- Gebruik duidelijke, korte zinnen\n- Verwijs naar de website voor meer informatie\n- Wees vriendelijk maar direct\n\nBEDRIJFSINFORMATIE:\n📍 Veneweg 199, 7946 LP Wanneperveen\n📞 0522 281 528\n⏰ Dagelijks 09:00-18:00 (1 april - 1 november)\n\nDIENSTEN:\n🚤 Botenverhuur (elektrische, zeilboten, kano's)\n🏠 Vakantiehuis\n🏕️ Camping\n⚓ Jachthaven\n🗺️ Vaarkaart\n\nPRIJZEN (dagprijzen):\n- Tender 720: €230 (10-12 pers)\n- Tender 570: €200 (8 pers)\n- Electrosloep 10: €200 (10 pers)\n- Electrosloep 8: €175 (8 pers)\n- Zeilboot: €70-85 (4-5 pers)\n- Kano/Kajak: €25 (2 pers)\n- Sup Board: €35 (1 pers)\n\nAntwoord in het Nederlands, kort en behulpzaam.`,
            en: `You are a brief and helpful assistant for Nijenhuis Botenverhuur.\n\nIMPORTANT RULES:\n- Always give SHORT answers (max 2-3 sentences)\n- Use clear, short sentences\n- Refer to the website for more information\n- Be friendly but direct\n\nCOMPANY INFO:\n📍 Veneweg 199, 7946 LP Wanneperveen\n📞 0522 281 528\n⏰ Daily 09:00-18:00 (April 1 - November 1)\n\nSERVICES:\n🚤 Boat rental (electric, sailboats, canoes)\n🏠 Holiday house\n🏕️ Camping\n⚓ Marina\n🗺️ Navigation map\n\nPRICES (per day):\n- Tender 720: €230 (10-12 pers)\n- Tender 570: €200 (8 pers)\n- Electrosloep 10: €200 (10 pers)\n- Electrosloep 8: €175 (8 pers)\n- Sailboat: €70-85 (4-5 pers)\n- Canoe/Kayak: €25 (2 pers)\n- Sup Board: €35 (1 pers)\n\nAnswer in English, briefly and helpfully.`,
            de: `Du bist ein kurzer und hilfreicher Assistent für Nijenhuis Botenverhuur.\n\nWICHTIGE REGELN:\n- Gib immer KURZE Antworten (max. 2-3 Sätze)\n- Verwende klare, kurze Sätze\n- Verweise für weitere Informationen auf die Website\n- Sei freundlich, aber direkt\n\nFIRMENINFORMATIONEN:\n📍 Veneweg 199, 7946 LP Wanneperveen\n📞 0522 281 528\n⏰ Täglich 09:00-18:00 (1. April - 1. November)\n\nDIENSTLEISTUNGEN:\n🚤 Bootsverleih (elektrisch, Segelboote, Kanus)\n🏠 Ferienhaus\n🏕️ Camping\n⚓ Jachthafen\n🗺️ Fahrkarte\n\nPREISE (pro Tag):\n- Tender 720: €230 (10-12 Pers.)\n- Tender 570: €200 (8 Pers.)\n- Electrosloep 10: €200 (10 Pers.)\n- Electrosloep 8: €175 (8 Pers.)\n- Segelboot: €70-85 (4-5 Pers.)\n- Kanu/Kajak: €25 (2 Pers.)\n- Sup Board: €35 (1 Pers.)\n\nAntworte auf Deutsch, kurz und hilfsbereit.`
        };

        const systemPrompt = systemPrompts[lang] || systemPrompts['en'];

        try {
            // Call backend API instead of Perplexity directly
            const response = await fetch('/api/perplexity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    model: this.MODEL,
                    messages: [
                        {
                            role: 'system',
                            content: systemPrompt
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
            const aiResponse = data.choices && data.choices[0] && data.choices[0].message && data.choices[0].message.content
                ? data.choices[0].message.content
                : (lang === 'nl' ? 'Technische storing. Bel direct: 0522 281 528' : (lang === 'de' ? 'Technischer Fehler. Rufen Sie direkt an: 0522 281 528' : 'Technical error. Call directly: 0522 281 528'));

            // Remove typing indicator and add AI response
            this.removeTypingIndicator(typingIndicator);
            this.addMessage(aiResponse, 'bot');

        } catch (error) {
            console.error('Error calling backend API:', error);
            this.removeTypingIndicator(typingIndicator);
            this.addMessage(lang === 'nl' ? 'Technische storing. Bel direct: 0522 281 528' : (lang === 'de' ? 'Technischer Fehler. Rufen Sie direkt an: 0522 281 528' : 'Technical error. Call directly: 0522 281 528'), 'bot');
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
