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
        loadScript('frontend/src/js/core/shared.js', function() {
            loadScript('frontend/src/js/booking/booking-system.js', function() {
                loadScript('frontend/src/js/core/translation.js', function() {
                    loadScript('frontend/src/js/booking/mollie-payment.js', function() {
                        // Note: chat/index.js removed - chat widget is loaded via external script in footer
                        // Load dev tools in development mode
                        if (window.location.hostname === 'localhost' || 
                            window.location.hostname === '127.0.0.1' || 
                            window.location.hostname === '' ||
                            window.location.search.includes('dev=true')) {
                            loadScript('scripts/dev-tools.js');
                        }
                    });
                });
            });
        });
})();

