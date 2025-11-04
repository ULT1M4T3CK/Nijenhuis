// Load simple-chatbot.js directly without ES6 modules
(function() {
    'use strict';
    
    // Function to load script dynamically
    function loadScript(src, callback) {
        console.log('[Chat] Loading script:', src);
        const script = document.createElement('script');
        script.src = src;
        script.onload = function() {
            console.log('[Chat] Script loaded successfully:', src);
            if (callback) callback();
        };
        script.onerror = function() {
            console.error('[Chat] Failed to load script:', src);
        };
        document.head.appendChild(script);
    }
    
    // Load simple-chatbot.js
    loadScript('../frontend/src/js/chat/simple-chatbot.js', function() {
        console.log('[Chat] simple-chatbot.js loaded, initialization should happen automatically');
    });
})();

// widget disabled for now; using simple-chatbot markup and logic only

