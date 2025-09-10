// Load simple-chatbot.js directly without ES6 modules
(function() {
    'use strict';
    
    // Function to load script dynamically
    function loadScript(src, callback) {
        const script = document.createElement('script');
        script.src = src;
        script.onload = callback || function() {};
        script.onerror = function() {
            console.warn('Failed to load script:', src);
        };
        document.head.appendChild(script);
    }
    
    // Load simple-chatbot.js
    loadScript('../frontend/src/js/chat/simple-chatbot.js');
})();

// widget disabled for now; using simple-chatbot markup and logic only

