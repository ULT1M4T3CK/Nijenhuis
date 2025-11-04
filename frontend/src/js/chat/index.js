// Load simple-chatbot.js as an ES6 module (needed for import.meta.env support)
(function() {
    'use strict';
    
    // Function to load script dynamically
    function loadScript(src, callback, isModule = false) {
        console.log('[Chat] Loading script:', src, isModule ? '(as module)' : '(regular)');
        const script = document.createElement('script');
        script.src = src;
        if (isModule) {
            script.type = 'module';
        }
        script.onload = function() {
            console.log('[Chat] Script loaded successfully:', src);
            if (callback) callback();
        };
        script.onerror = function() {
            console.error('[Chat] Failed to load script:', src);
        };
        document.head.appendChild(script);
    }
    
    // Load simple-chatbot.js as a module (needed for import.meta.env support)
    loadScript('../frontend/src/js/chat/simple-chatbot.js', function() {
        console.log('[Chat] simple-chatbot.js loaded, initialization should happen automatically');
    }, true);
})();

// widget disabled for now; using simple-chatbot markup and logic only

