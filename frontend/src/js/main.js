// Load scripts directly without ES6 modules to avoid CORS issues with file:// protocol
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
    
    // Load scripts in order
    loadScript('../frontend/src/js/core/shared.js', function() {
        loadScript('../frontend/src/js/booking/booking-system.js', function() {
            loadScript('../frontend/src/js/core/translation.js', function() {
                loadScript('../frontend/src/js/booking/mollie-payment.js', function() {
                    loadScript('../frontend/src/js/chat/index.js');
                });
            });
        });
    });
})();

