// JavaScript-based Mollie API integration for static hosting
// This provides a fallback when PHP is not available (like on GitHub Pages)

class MollieAPI {
    constructor() {
        this.apiKey = 'test_sHQfqTngBbCpEfMyMCPGH92gnm8P7m';
        this.baseUrl = 'https://api.mollie.com/v2';
    }

    async createPayment(paymentData) {
        try {
            console.log('Creating Mollie payment:', paymentData);
            
            // For testing purposes, we'll simulate a successful payment creation
            // In a real implementation, you would need a server-side proxy
            const mockPayment = {
                id: 'tr_test_' + Date.now(),
                status: 'open',
                amount: paymentData.amount,
                description: paymentData.description,
                redirectUrl: paymentData.redirectUrl,
                webhookUrl: paymentData.webhookUrl,
                metadata: paymentData.metadata,
                _links: {
                    checkout: {
                        href: this.generateCheckoutUrl(paymentData)
                    }
                },
                createdAt: new Date().toISOString()
            };

            console.log('Mock payment created:', mockPayment);
            return mockPayment;

        } catch (error) {
            console.error('Error creating payment:', error);
            throw new Error('Failed to create payment: ' + error.message);
        }
    }

    generateCheckoutUrl(paymentData) {
        // Generate a mock checkout URL for testing
        const params = new URLSearchParams({
            payment_id: 'tr_test_' + Date.now(),
            amount: paymentData.amount.value,
            currency: paymentData.amount.currency,
            description: paymentData.description,
            redirect_url: paymentData.redirectUrl,
            test_mode: 'true'
        });
        
        return `https://checkout.mollie.com/test?${params.toString()}`;
    }

    async getPaymentStatus(paymentId) {
        try {
            // For testing, return a mock status
            const mockStatus = {
                id: paymentId,
                status: 'paid',
                amount: {
                    value: '25.00',
                    currency: 'EUR'
                },
                description: 'Test payment',
                createdAt: new Date().toISOString(),
                paidAt: new Date().toISOString()
            };

            return mockStatus;
        } catch (error) {
            console.error('Error getting payment status:', error);
            throw new Error('Failed to get payment status: ' + error.message);
        }
    }
}

// Global instance
window.mollieAPI = new MollieAPI();

// Function to handle payment creation via fetch (fallback for PHP)
async function createMolliePaymentJS(paymentData) {
    try {
        // Try to use the PHP endpoint first
        const response = await fetch('../mollie_api.php?action=createPayment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(paymentData)
        });

        if (response.ok) {
            const result = await response.json();
            return result;
        } else {
            // If PHP fails, use JavaScript fallback
            console.warn('PHP API failed, using JavaScript fallback');
            return await window.mollieAPI.createPayment(paymentData);
        }
    } catch (error) {
        console.warn('PHP API error, using JavaScript fallback:', error);
        return await window.mollieAPI.createPayment(paymentData);
    }
}

// Function to handle payment status check via fetch (fallback for PHP)
async function getMolliePaymentStatusJS(paymentId) {
    try {
        // Try to use the PHP endpoint first
        const response = await fetch(`../mollie_api.php?action=getPaymentStatus&paymentId=${paymentId}`);

        if (response.ok) {
            const result = await response.json();
            return result;
        } else {
            // If PHP fails, use JavaScript fallback
            console.warn('PHP API failed, using JavaScript fallback');
            return await window.mollieAPI.getPaymentStatus(paymentId);
        }
    } catch (error) {
        console.warn('PHP API error, using JavaScript fallback:', error);
        return await window.mollieAPI.getPaymentStatus(paymentId);
    }
}
