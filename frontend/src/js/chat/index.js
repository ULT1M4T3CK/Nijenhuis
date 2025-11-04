// Load simple-chatbot.js directly without ES6 modules
(function() {
    'use strict';
    
    // Function to load script dynamically
    function loadScript(src, callback) {
        const script = document.createElement('script');
        script.src = src;
        script.onload = callback || function() {};
        script.onerror = function() {
            console.error('[Chat] Failed to load script:', src);
            console.error('[Chat] Make sure the chatbot server is running on http://localhost:5001');
            console.error('[Chat] To start the server, run: python3 backend/chatbot/api/server.py');
        };
        document.head.appendChild(script);
    }
    
    // Load simple-chatbot.js with cache busting to ensure latest version
    // The script will handle its own initialization (including late-loading scenarios)
    const scriptSrc = '../frontend/src/js/chat/simple-chatbot.js?v=' + Date.now();
    loadScript(scriptSrc);
})();

// widget disabled for now; using simple-chatbot markup and logic only

