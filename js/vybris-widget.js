(function () {
    const scriptTag = document.currentScript;
    const chatbotId = scriptTag.getAttribute('data-id');
    const apiUrl = scriptTag.getAttribute('data-url') || '/vybris/api';

    if (!chatbotId) {
        console.error('VYBR!S Widget: No chatbot ID provided.');
        return;
    }

    console.log(`VYBR!S Widget initialized for chatbot: ${chatbotId}`);

    // === SESSION MANAGEMENT ===
    let isOpen = false; // Moved to top scope
    const SESSION_TIMEOUT = 30 * 60 * 1000; // 30 minutes in milliseconds
    const STORAGE_KEY_PREFIX = `vybris_chat_${chatbotId}_`;

    // Get or create session ID
    function getSessionId() {
        const storageKey = STORAGE_KEY_PREFIX + 'session';
        let session = null;

        try {
            const stored = localStorage.getItem(storageKey);
            if (stored) {
                session = JSON.parse(stored);
                const now = Date.now();

                // Check if session has expired (30 minutes of inactivity)
                if (session.lastActivity && (now - session.lastActivity) < SESSION_TIMEOUT) {
                    // Session is still valid, update last activity
                    session.lastActivity = now;
                    localStorage.setItem(storageKey, JSON.stringify(session));
                    return session.id;
                }
            }
        } catch (e) {
            console.warn('Failed to retrieve session:', e);
        }

        // Create new session
        const newSessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        session = {
            id: newSessionId,
            startedAt: Date.now(),
            lastActivity: Date.now()
        };

        try {
            localStorage.setItem(storageKey, JSON.stringify(session));
        } catch (e) {
            console.warn('Failed to store session:', e);
        }

        return newSessionId;
    }

    // Update session activity timestamp
    function updateSessionActivity() {
        const storageKey = STORAGE_KEY_PREFIX + 'session';
        try {
            const stored = localStorage.getItem(storageKey);
            if (stored) {
                const session = JSON.parse(stored);
                session.lastActivity = Date.now();
                localStorage.setItem(storageKey, JSON.stringify(session));
            }
        } catch (e) {
            console.warn('Failed to update session activity:', e);
        }
    }

    // Chat history management
    function saveChatHistory(messages) {
        const storageKey = STORAGE_KEY_PREFIX + 'history';
        try {
            localStorage.setItem(storageKey, JSON.stringify({
                messages: messages,
                timestamp: Date.now()
            }));
        } catch (e) {
            console.warn('Failed to save chat history:', e);
        }
    }

    function loadChatHistory() {
        const storageKey = STORAGE_KEY_PREFIX + 'history';
        try {
            const stored = localStorage.getItem(storageKey);
            if (stored) {
                const history = JSON.parse(stored);
                const now = Date.now();

                // Only restore history if it's from an active session (within timeout)
                if (history.timestamp && (now - history.timestamp) < SESSION_TIMEOUT) {
                    return history.messages || [];
                }
            }
        } catch (e) {
            console.warn('Failed to load chat history:', e);
        }
        return [];
    }

    function clearChatHistory() {
        const storageKey = STORAGE_KEY_PREFIX + 'history';
        try {
            localStorage.removeItem(storageKey);
        } catch (e) {
            console.warn('Failed to clear chat history:', e);
        }
    }

    // Initialize session
    const sessionId = getSessionId();
    console.log(`Session ID: ${sessionId}`);

    // Chatbot configuration - will be loaded from API
    let chatbotConfig = null;
    let chatbotName = 'Chat Support';

    // Create floating button
    const button = document.createElement('div');
    button.id = 'vybris-chat-button';
    button.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #d148e6, #3b48ae);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 2147483647;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: transform 0.2s;
    `;
    button.innerHTML = '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
    document.body.appendChild(button);

    // Create Chat Window
    const chatWindow = document.createElement('div');
    chatWindow.id = 'vybris-chat-window';
    chatWindow.style.cssText = `
        position: fixed;
        bottom: 100px;
        right: 20px;
        width: 350px;
        max-width: calc(100vw - 40px);
        height: 550px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 5px 40px rgba(0,0,0,0.16);
        z-index: 2147483647;
        display: none;
        flex-direction: column;
        overflow: hidden;
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.3s, transform 0.3s;
    `;

    // Responsive sizing logic
    // We update explicit dimensions on resize for smooth transitions and robustness
    chatWindow.style.maxHeight = 'calc(100vh - 120px)';

    const updateChatWindowSize = () => {
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        // Calculate maximum available height (viewport - 120px for margins)
        // We limit the max height to 650px on desktop and 600px on mobile
        const maxAllowedHeight = viewportWidth >= 768 ? 650 : 600;
        // Ensure strictly non-negative
        const targetHeight = Math.max(200, Math.min(maxAllowedHeight, viewportHeight - 120));

        if (viewportWidth >= 768) {
            // Desktop/Tablet
            chatWindow.style.width = '380px';
            chatWindow.style.height = targetHeight + 'px';
        } else {
            // Mobile
            chatWindow.style.width = Math.max(280, viewportWidth - 40) + 'px';
            chatWindow.style.height = targetHeight + 'px';
        }
    };

    // Initial sizing
    updateChatWindowSize();

    // Dynamic resizing
    window.addEventListener('resize', updateChatWindowSize);

    // Header container
    const header = document.createElement('div');
    header.id = 'vybris-chat-header';
    header.style.cssText = `
        padding: 16px;
        background: #00477e;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 10;
        flex-shrink: 0;
    `;

    // Header Content (Avatar + Text)
    const headerContent = document.createElement('div');
    headerContent.style.cssText = `display: flex; align-items: center; gap: 12px;`;

    // Avatar
    const avatarContainer = document.createElement('div');
    avatarContainer.id = 'vybris-avatar-container';
    avatarContainer.innerHTML = '<div style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-blue-600"><path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 6C13.66 6 15 7.34 15 9C15 10.66 13.66 12 12 12C10.34 12 9 10.66 9 9C9 7.34 10.34 6 12 6ZM12 20C9.33 20 7 18.67 7 16.5C7 14.33 9.33 13 12 13C14.67 13 17 14.33 17 16.5C17 18.67 14.67 20 12 20Z" fill="#00477e"/></svg></div>';
    headerContent.appendChild(avatarContainer);

    // Text (Name + Secure)
    const headerText = document.createElement('div');
    headerText.style.cssText = `display: flex; flex-direction: column;`;

    const nameEl = document.createElement('span');
    nameEl.id = 'vybris-bot-name';
    nameEl.innerText = chatbotName;
    nameEl.style.cssText = `font-weight: 700; font-size: 16px; line-height: 1.2;`;
    headerText.appendChild(nameEl);

    const secureEl = document.createElement('span');
    secureEl.innerHTML = 'Secure <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" style="display:inline-block; vertical-align:text-top; margin-left: 2px;"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>';
    secureEl.style.cssText = `font-size: 11px; opacity: 0.8; display: flex; align-items: center;`;
    headerText.appendChild(secureEl);

    headerContent.appendChild(headerText);
    header.appendChild(headerContent);

    // Close Button
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
    closeBtn.style.cssText = `
        background: transparent;
        border: none;
        color: white;
        cursor: pointer;
        padding: 4px;
        opacity: 0.8;
        transition: opacity 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    closeBtn.onmouseover = () => closeBtn.style.opacity = '1';
    closeBtn.onmouseout = () => closeBtn.style.opacity = '0.8';
    header.appendChild(closeBtn);

    chatWindow.appendChild(header);

    // Messages Area
    const messages = document.createElement('div');
    messages.id = 'vybris-chat-messages';
    messages.style.cssText = `
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 12px;
    `;
    chatWindow.appendChild(messages);

    // Input Area
    const inputWrapper = document.createElement('div');
    inputWrapper.style.cssText = `
        padding: 16px 20px 0 20px;
        background: white;
        border-top: 1px solid #eee;
        display: flex;
        flex-direction: column;
        gap: 8px;
    `;

    const inputRow = document.createElement('div');
    inputRow.style.cssText = `
        display: flex;
        gap: 10px;
        align-items: center;
        background: white;
        position: relative;
    `;

    const input = document.createElement('input');
    input.id = 'vybris-chat-input';
    input.placeholder = 'Type a message...';
    input.style.cssText = `
        flex: 1;
        padding: 12px 16px;
        border: 1px solid #e1e4e8;
        border-radius: 24px;
        outline: none;
        font-size: 14px;
        background: #f8f9fa;
        transition: border-color 0.2s;
    `;
    input.onfocus = () => input.style.borderColor = '#00477e';
    input.onblur = () => input.style.borderColor = '#e1e4e8';

    const sendBtn = document.createElement('button');
    sendBtn.id = 'vybris-chat-send';
    sendBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>';
    sendBtn.style.cssText = `
        width: 40px;
        height: 40px;
        background: #00477e;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    `;
    sendBtn.onmouseover = () => sendBtn.style.transform = 'scale(1.05)';
    sendBtn.onmouseout = () => sendBtn.style.transform = 'scale(1)';

    inputRow.appendChild(input);
    inputRow.appendChild(sendBtn);
    inputWrapper.appendChild(inputRow);
    chatWindow.appendChild(inputWrapper);

    // Footer
    const footer = document.createElement('div');
    footer.style.cssText = `
        padding: 8px;
        text-align: center;
        font-size: 11px;
        color: #999;
        background: white;
        font-weight: 500;
        letter-spacing: 0.3px;
    `;
    footer.innerHTML = 'End-to-end secure';
    chatWindow.appendChild(footer);

    document.body.appendChild(chatWindow);

    // Fetch chatbot configuration
    async function fetchChatbotConfig() {
        try {
            const response = await fetch(`${apiUrl}/v1/chatbots/public/${chatbotId}`);
            if (response.ok) {
                const data = await response.json();
                chatbotConfig = data.config;
                chatbotName = data.name;

                // Update Name
                const nameEl = document.getElementById('vybris-bot-name');
                if (nameEl) nameEl.innerText = chatbotName;

                // Update Avatar
                if (chatbotConfig?.customStyles?.avatarUrl) {
                    const avatarContainer = document.getElementById('vybris-avatar-container');
                    if (avatarContainer) {
                        const avatarUrl = chatbotConfig.customStyles.avatarUrl;
                        avatarContainer.innerHTML = `<img src="${apiUrl}${avatarUrl}?t=${Date.now()}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.3);">`;
                    }
                }

                // Apply custom styles if available
                if (chatbotConfig && chatbotConfig.customStyles) {
                    const styles = chatbotConfig.customStyles;
                    // Default to Nijenhuis Blue if not set
                    const primaryColor = styles.primaryColor || '#00477e';
                    const secondaryColor = styles.secondaryColor || '#003366';
                    const gradientAngle = styles.gradientAngle || 135;
                    const colorType = styles.colorType || 'solid'; // Default to solid for consistency with new design

                    // Calculate background based on color type
                    let bgStyle;
                    if (colorType === 'solid') {
                        bgStyle = primaryColor;
                    } else {
                        bgStyle = `linear-gradient(${gradientAngle}deg, ${primaryColor}, ${secondaryColor})`;
                    }

                    // Apply colors to button and header
                    button.style.background = bgStyle;
                    const headerEl = document.getElementById('vybris-chat-header');
                    if (headerEl) headerEl.style.background = bgStyle;

                    const sendBtnEl = document.getElementById('vybris-chat-send');
                    if (sendBtnEl) sendBtnEl.style.background = primaryColor;

                    const inputEl = document.getElementById('vybris-chat-input');
                    if (inputEl) {
                        inputEl.onfocus = () => inputEl.style.borderColor = primaryColor;
                    }
                }

                console.log('VYBR!S Widget: Loaded config for', chatbotName);
            } else {
                console.warn('VYBR!S Widget: Could not load chatbot config');
            }
        } catch (e) {
            console.error('VYBR!S Widget: Error loading config', e);
        }
    }

    // Load config on init
    fetchChatbotConfig();

    // Toggle logic
    // isOpen is now defined at top scope
    let welcomeShown = false;
    let chatHistory = [];

    button.onclick = async () => {
        isOpen = !isOpen;

        if (isOpen) {
            // Show the chat window with animation
            chatWindow.style.display = 'flex';
            // Trigger reflow to ensure transition works
            chatWindow.offsetHeight;
            chatWindow.style.opacity = '1';
            chatWindow.style.transform = 'translateY(0)';
        } else {
            // Hide with animation
            chatWindow.style.opacity = '0';
            chatWindow.style.transform = 'translateY(20px)';
            setTimeout(() => {
                chatWindow.style.display = 'none';
            }, 300);
        }

        button.style.transform = isOpen ? 'rotate(15deg)' : 'none';

        // Restore chat history on first open
        if (isOpen && !welcomeShown) {
            // Ensure config is loaded
            if (!chatbotConfig) {
                await fetchChatbotConfig();
            }

            // Try to restore previous conversation
            const savedHistory = loadChatHistory();
            if (savedHistory && savedHistory.length > 0) {
                // Restore previous messages
                savedHistory.forEach(msg => {
                    addMessage(msg.text, msg.sender, false); // false = don't save to history again
                });
                welcomeShown = true;
                chatHistory = savedHistory;
            } else {
                // Show welcome message for new conversation
                const welcomeMessage = chatbotConfig?.welcomeMessage || 'Hello! How can I assist you today?';
                addMessage(welcomeMessage, 'bot');
                welcomeShown = true;
            }
        }
    };

    // Close Button Logic
    if (typeof closeBtn !== 'undefined' && closeBtn) {
        closeBtn.onclick = (e) => {
            e.stopPropagation();
            isOpen = false;
            chatWindow.style.opacity = '0';
            chatWindow.style.transform = 'translateY(20px)';
            setTimeout(() => {
                chatWindow.style.display = 'none';
            }, 300);
            button.style.transform = 'none';
        };
    }

    // Clear Chat History Button
    const clearChatBtn = document.createElement('button');
    clearChatBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>';
    clearChatBtn.style.cssText = `
        background: transparent;
        border: none;
        color: white;
        cursor: pointer;
        padding: 4px 8px;
        opacity: 0.8;
        transition: opacity 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 8px;
    `;
    clearChatBtn.title = 'Nieuwe conversatie starten';
    clearChatBtn.onmouseover = () => clearChatBtn.style.opacity = '1';
    clearChatBtn.onmouseout = () => clearChatBtn.style.opacity = '0.8';
    clearChatBtn.onclick = (e) => {
        e.stopPropagation();
        if (confirm('Weet u zeker dat u deze conversatie wilt wissen? Dit kan niet ongedaan worden gemaakt.')) {
            clearChatHistory();
            chatHistory = [];
            messages.innerHTML = '';
            welcomeShown = false;
            
            // Show welcome message again
            if (chatbotConfig) {
                const welcomeMessage = chatbotConfig.welcomeMessage || 'Hello! How can I assist you today?';
                addMessage(welcomeMessage, 'bot');
                welcomeShown = true;
            }
        }
    };
    headerContent.appendChild(clearChatBtn);

    // Message Logic
    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        // Update session activity
        updateSessionActivity();

        // Add user message
        addMessage(text, 'user');
        input.value = '';

        // Show typing indicator
        const typingDiv = document.createElement('div');
        typingDiv.id = 'vybris-typing';
        typingDiv.innerText = '...';
        typingDiv.style.cssText = `
            max-width: 80%;
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 14px;
            align-self: flex-start;
            background: #e9ecef;
            color: #666;
            animation: blink 1s infinite;
        `;
        messages.appendChild(typingDiv);

        // API Call with session ID
        try {
            const response = await fetch(`${apiUrl}/nlp/process`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    text: text,
                    chatbot_id: chatbotId,
                    session_id: sessionId  // Include session ID
                })
            });
            const data = await response.json();

            // Remove typing indicator
            const typing = document.getElementById('vybris-typing');
            if (typing) typing.remove();

            // Check if response exists and is valid
            if (data && data.response) {
                addMessage(data.response, 'bot');
            } else {
                // Handle error response or missing response field
                const errorMsg = data?.detail || data?.error || "Sorry, I couldn't process that request.";
                addMessage(errorMsg, 'bot');
                console.error('API returned error or invalid response:', data);
            }
        } catch (e) {
            console.error('Widget error:', e);
            // Remove typing indicator
            const typing = document.getElementById('vybris-typing');
            if (typing) typing.remove();

            addMessage("Sorry, connection failed.", 'bot');
        }

        // Update session activity after response
        updateSessionActivity();
    }

    // Simple markdown to HTML converter
    function convertMarkdown(text) {
        // Escape HTML first for safety
        let html = text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // Convert markdown syntax to HTML
        // Bold: **text** or __text__
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/__(.+?)__/g, '<strong>$1</strong>');

        // Italic: *text* or _text_
        html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
        html = html.replace(/_(.+?)_/g, '<em>$1</em>');

        // Bullet points: lines starting with - or • or *
        html = html.replace(/^[\-\•\*]\s+(.+)$/gm, '<li>$1</li>');

        // Wrap consecutive <li> items in <ul>
        html = html.replace(/(<li>.+<\/li>\n?)+/g, '<ul style="margin: 4px 0; padding-left: 20px; list-style: disc;">$&</ul>');

        // Line breaks
        html = html.replace(/\n/g, '<br>');

        return html;
    }

    function addMessage(text, sender, saveToHistory = true) {
        const div = document.createElement('div');

        // For bot messages, convert markdown to HTML
        if (sender === 'bot') {
            div.innerHTML = convertMarkdown(text);
        } else {
            div.innerText = text;
        }

        div.style.cssText = `
            max-width: 80%;
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 14px;
            align-self: ${sender === 'user' ? 'flex-end' : 'flex-start'};
            background: ${sender === 'user' ? '#e3e8ff' : '#e9ecef'};
            color: black;
            line-height: 1.5;
        `;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;

        // Save to chat history
        if (saveToHistory) {
            chatHistory.push({ text, sender, timestamp: Date.now() });
            saveChatHistory(chatHistory);
        }
    }

    sendBtn.onclick = sendMessage;
    input.onkeypress = (e) => {
        if (e.key === 'Enter') sendMessage();
    };

})();
