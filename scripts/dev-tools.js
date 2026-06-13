/**
 * Development Tools for Nijenhuis Website
 * Provides utilities for testing and development
 */

(function(window) {
    'use strict';

    /**
     * Development Tools Object
     */
    const DevTools = {
        /**
         * Simulate Mollie webhook for testing payment flows
         * @param {string} paymentId - Payment ID to simulate
         * @param {string} status - Payment status ('paid', 'failed', 'expired', 'canceled', 'pending')
         */
        simulateMollieWebhook: function(paymentId, status) {
            if (!window.molliePayment) {
                console.error('MolliePaymentSystem not available. Ensure mollie-payment.js is loaded.');
                return;
            }

            if (typeof window.molliePayment.simulateWebhook !== 'function') {
                console.error('simulateWebhook method not available in MolliePaymentSystem');
                return;
            }

            const validStatuses = ['paid', 'failed', 'expired', 'canceled', 'pending', 'open'];
            if (!validStatuses.includes(status)) {
                console.error(`Invalid status: ${status}. Valid statuses: ${validStatuses.join(', ')}`);
                return;
            }

            console.log(`🧪 Simulating Mollie webhook: paymentId=${paymentId}, status=${status}`);
            window.molliePayment.simulateWebhook(paymentId, status);
            console.log('✅ Webhook simulation triggered');
        },

        /**
         * Check payment status using JavaScript fallback
         * @param {string} paymentId - Payment ID to check
         */
        checkPaymentStatus: async function(paymentId) {
            if (typeof getMolliePaymentStatusJS === 'function') {
                try {
                    const status = await getMolliePaymentStatusJS(paymentId);
                    console.log(`📊 Payment Status for ${paymentId}:`, status);
                    return status;
                } catch (error) {
                    console.error('Error checking payment status:', error);
                    return null;
                }
            } else {
                console.error('getMolliePaymentStatusJS function not available. Ensure mollie_api.js is loaded.');
                return null;
            }
        },

        /**
         * Test payment completion check
         * @param {string} status - Payment status to test
         */
        testPaymentStatusHelpers: function(status) {
            if (!window.molliePayment) {
                console.error('MolliePaymentSystem not available.');
                return;
            }

            const isCompleted = window.molliePayment.isPaymentCompleted(status);
            const isFailed = window.molliePayment.isPaymentFailed(status);

            console.log(`📊 Payment Status Test: ${status}`);
            console.log(`  ✅ Completed: ${isCompleted}`);
            console.log(`  ❌ Failed: ${isFailed}`);
        },

        /**
         * Clear chat history (for testing)
         */
        clearChatHistory: function() {
            // Access the clearChatHistory function from vybris widget
            // This is a helper for dev tools
            const storageKey = 'vybris_chat_bot_d315cb9c035d_history';
            try {
                localStorage.removeItem(storageKey);
                console.log('✅ Chat history cleared');
            } catch (e) {
                console.error('Error clearing chat history:', e);
            }
        },

        /**
         * Show all available dev tools
         */
        help: function() {
            console.log('%c🔧 Nijenhuis Dev Tools', 'color: #00477e; font-size: 16px; font-weight: bold;');
            console.log('%cAvailable commands:', 'color: #666; font-size: 14px;');
            console.log('  DevTools.simulateMollieWebhook(paymentId, status) - Simulate payment webhook');
            console.log('  DevTools.checkPaymentStatus(paymentId) - Check payment status');
            console.log('  DevTools.testPaymentStatusHelpers(status) - Test payment status helpers');
            console.log('  DevTools.clearChatHistory() - Clear chat history');
            console.log('  DevTools.help() - Show this help');
            console.log('\n%cExample:', 'color: #00477e; font-weight: bold;');
            console.log('  DevTools.simulateMollieWebhook("tr_test123", "paid")');
        }
    };

    // Expose to window in development mode only
    if (window.location.hostname === 'localhost' || 
        window.location.hostname === '127.0.0.1' || 
        window.location.hostname === '' ||
        window.location.search.includes('dev=true')) {
        window.DevTools = DevTools;
        console.log('%c🔧 Dev Tools loaded. Type DevTools.help() for available commands.', 'color: #28a745;');
    }

})(window);
