// Load simple-chatbot.js as a regular script (no ES6 modules needed)
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
    
    // Load simple-chatbot.js (regular script, not module)
    loadScript('../frontend/src/js/chat/simple-chatbot.js', function() {
        console.log('[Chat] simple-chatbot.js loaded, initialization should happen automatically');
    });
})();

// widget disabled for now; using simple-chatbot markup and logic only

