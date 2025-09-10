// Load admin/admin.js directly without ES6 modules
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
    
    // Load admin/admin.js
    loadScript('../frontend/src/js/admin/admin.js');
})();

