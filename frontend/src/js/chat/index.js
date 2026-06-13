// Load Secure Chatbot Widget
// This replaces the Simple Chatbot with the enhanced Secure Chatbot Widget
(function() {
    'use strict';
    
    // Function to load script dynamically
    function loadScript(src, callback) {
        const script = document.createElement('script');
        script.src = src;
        script.onload = callback || function() {};
        script.onerror = function() {
            console.error('Failed to load script:', src);
        };
        document.head.appendChild(script);
    }
    
    // Load secure chatbot client first (dependency), then widget
    loadScript('../frontend/src/js/chat/secure-chatbot-client.js', function() {
        loadScript('../frontend/src/js/chat/secure-chatbot-widget.js', function() {
            // Initialize secure chatbot widget when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeSecureChatbot);
            } else {
                initializeSecureChatbot();
            }
        });
    });
    
    function initializeSecureChatbot() {
        if (typeof SecureChatbotWidget !== 'undefined') {
            // Get endpoint from window config (set in HTML) or default for dev
            const apiEndpoint = window.CHATBOT_API_ENDPOINT || '/api/chat';
            
            // Initialize the secure chatbot widget
            window.secureChatbot = new SecureChatbotWidget({
                apiEndpoint: apiEndpoint,
                showConnectionStatus: true,
                showSecurityIndicator: true,
                autoReconnect: true
            });
            
            console.log('✅ Secure Chatbot Widget initialized');
        } else {
            console.error('SecureChatbotWidget class not found. Check script loading order.');
        }
    }
})();

