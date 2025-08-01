// ========================================================================
// MOLLIE PAYMENT INTEGRATION
// ========================================================================

class MolliePaymentSystem {
    constructor() {
        this.apiKey = 'test_sHQfqTngBbCpEfMyMCPGH92gnm8P7m'; // Test API key
        this.baseUrl = 'https://api.mollie.com/v2';
        
        // Set webhook URL based on environment
        if (window.location.hostname === '85.215.195.147' || window.location.hostname.includes('85.215.195.147')) {
            this.webhookUrl = 'http://85.215.195.147/webhook/mollie';
        } else {
            this.webhookUrl = window.location.origin + '/webhook/mollie';
        }
        
        this.init();
    }
    
    init() {
        console.log('Mollie payment system initialized');
        this.setupWebhookListener();
    }
    
    // Create a new payment
    async createPayment(bookingData) {
        try {
            // Check if we're in local development mode
            const isLocalDevelopment = window.location.hostname === 'localhost' || 
                                     window.location.hostname === '127.0.0.1' ||
                                     window.location.protocol === 'file:';
            
            // Check if we're on the production server
            const isProductionServer = window.location.hostname === '85.215.195.147' ||
                                     window.location.hostname.includes('85.215.195.147');
            
            if (isLocalDevelopment) {
                console.log('Local development mode detected - simulating payment creation');
                return this.createSimulatedPayment(bookingData);
            }
            
            if (isProductionServer) {
                console.log('Production server detected - using real Mollie API');
                // Use production API key when available
                // this.apiKey = 'live_YOUR_PRODUCTION_API_KEY';
            }
            
            const paymentData = {
                amount: {
                    currency: 'EUR',
                    value: this.calculateBookingPrice(bookingData.boatType, bookingData.engineOption)
                },
                description: `Reservering ${bookingData.boatType} - ${bookingData.date}`,
                redirectUrl: this.getRedirectUrl(bookingData.id),
                webhookUrl: this.webhookUrl,
                metadata: {
                    booking_id: bookingData.id,
                    customer_email: bookingData.customerEmail,
                    boat_type: bookingData.boatType,
                    booking_date: bookingData.date
                }
            };
            
            const response = await fetch(`${this.baseUrl}/payments`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.apiKey}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(paymentData)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const payment = await response.json();
            console.log('Payment created:', payment);
            
            // Store payment ID with booking
            this.updateBookingWithPaymentId(bookingData.id, payment.id);
            
            return payment;
            
        } catch (error) {
            console.error('Error creating payment:', error);
            throw error;
        }
    }
    
    // Create a simulated payment for local development
    createSimulatedPayment(bookingData) {
        const paymentId = 'tr_sim_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        // Create proper URLs for local development
        const baseUrl = this.getBaseUrl();
        const redirectUrl = this.getRedirectUrl(bookingData.id);
        const checkoutUrl = `${baseUrl}pages/payment-simulation.html?payment_id=${paymentId}&booking_id=${bookingData.id}`;
        
        const simulatedPayment = {
            id: paymentId,
            status: 'pending',
            amount: {
                currency: 'EUR',
                value: this.calculateBookingPrice(bookingData.boatType, bookingData.engineOption)
            },
            description: `Reservering ${bookingData.boatType} - ${bookingData.date}`,
            redirectUrl: redirectUrl,
            links: {
                checkout: {
                    href: checkoutUrl
                }
            },
            metadata: {
                booking_id: bookingData.id,
                customer_email: bookingData.customerEmail,
                boat_type: bookingData.boatType,
                booking_date: bookingData.date
            }
        };
        
        console.log('Simulated payment created:', simulatedPayment);
        console.log('Checkout URL:', checkoutUrl);
        
        // Store payment ID with booking
        this.updateBookingWithPaymentId(bookingData.id, paymentId);
        
        return simulatedPayment;
    }
    
    // Get redirect URL for payment success
    getRedirectUrl(bookingId) {
        if (window.location.hostname === '85.215.195.147' || window.location.hostname.includes('85.215.195.147')) {
            return `http://85.215.195.147/pages/payment-success.html?booking_id=${bookingId}`;
        } else {
            return `${window.location.origin}/pages/payment-success.html?booking_id=${bookingId}`;
        }
    }
    
    // Get base URL for local development
    getBaseUrl() {
        // Check if we're running from file:// protocol (local development)
        if (window.location.protocol === 'file:') {
            // For file:// protocol, we need to construct the path differently
            const currentPath = window.location.pathname;
            const pathParts = currentPath.split('/');
            
            // Remove the filename and get the directory path
            pathParts.pop(); // Remove current file
            if (pathParts[pathParts.length - 1] === 'pages') {
                pathParts.pop(); // Remove 'pages' directory
            }
            
            // Construct the base URL
            const basePath = pathParts.join('/') + '/';
            return `file://${basePath}`;
        } else {
            // For http/https, use origin
            return window.location.origin + '/';
        }
    }
    
    // Calculate booking price based on boat type and engine option
    calculateBookingPrice(boatType, engineOption) {
        const prices = {
            'classic-tender-720': '120.00',
            'classic-tender-570': '100.00',
            'electrosloop-10': '95.00',
            'electrosloop-8': '85.00',
            'electroboat-5': '75.00',
            'sailboat-4-5': engineOption === 'with' ? '85.00' : '70.00',
            'sailpunter-3-4': '60.00',
            'canoe-3': '35.00',
            'kayak-2': '25.00',
            'kayak-1': '20.00',
            'sup-board': '25.00'
        };
        
        return prices[boatType] || '50.00';
    }
    
    // Update booking with payment ID
    updateBookingWithPaymentId(bookingId, paymentId) {
        try {
            const bookings = JSON.parse(localStorage.getItem('nijenhuis_bookings') || '[]');
            const bookingIndex = bookings.findIndex(b => b.id === bookingId);
            
            if (bookingIndex !== -1) {
                bookings[bookingIndex].paymentId = paymentId;
                bookings[bookingIndex].updatedAt = new Date().toISOString();
                localStorage.setItem('nijenhuis_bookings', JSON.stringify(bookings));
                console.log('Booking updated with payment ID:', paymentId);
            }
        } catch (error) {
            console.error('Error updating booking with payment ID:', error);
        }
    }
    
    // Get payment status
    async getPaymentStatus(paymentId) {
        try {
            const response = await fetch(`${this.baseUrl}/payments/${paymentId}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${this.apiKey}`,
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const payment = await response.json();
            return payment.status;
            
        } catch (error) {
            console.error('Error getting payment status:', error);
            throw error;
        }
    }
    
    // Handle payment status update
    handlePaymentStatusUpdate(paymentId, status) {
        try {
            const bookings = JSON.parse(localStorage.getItem('nijenhuis_bookings') || '[]');
            const bookingIndex = bookings.findIndex(b => b.paymentId === paymentId);
            
            if (bookingIndex !== -1) {
                const booking = bookings[bookingIndex];
                let newStatus;
                
                switch (status) {
                    case 'paid':
                        newStatus = 'confirmed-paid';
                        break;
                    case 'failed':
                    case 'expired':
                    case 'canceled':
                        newStatus = 'payment-rejected';
                        break;
                    case 'pending':
                        newStatus = 'confirmed-not-paid';
                        break;
                    default:
                        newStatus = booking.status; // Keep current status
                }
                
                // Update booking status
                bookings[bookingIndex].status = newStatus;
                bookings[bookingIndex].paymentStatus = status;
                bookings[bookingIndex].updatedAt = new Date().toISOString();
                
                localStorage.setItem('nijenhuis_bookings', JSON.stringify(bookings));
                
                // Dispatch event for admin system to update
                window.dispatchEvent(new CustomEvent('paymentStatusUpdated', {
                    detail: { bookingId: booking.id, newStatus, paymentStatus: status }
                }));
                
                console.log(`Payment status updated for booking ${booking.id}: ${status} -> ${newStatus}`);
                
                return booking;
            }
            
        } catch (error) {
            console.error('Error handling payment status update:', error);
        }
    }
    
    // Setup webhook listener for local development
    setupWebhookListener() {
        // For local development, we'll simulate webhook calls
        // In production, this would be handled by a server endpoint
        window.addEventListener('mollieWebhook', (event) => {
            const { paymentId, status } = event.detail;
            this.handlePaymentStatusUpdate(paymentId, status);
        });
    }
    
    // Simulate webhook for testing (local development)
    simulateWebhook(paymentId, status) {
        window.dispatchEvent(new CustomEvent('mollieWebhook', {
            detail: { paymentId, status }
        }));
    }
    
    // Redirect to Mollie payment page
    redirectToPayment(payment) {
        if (payment.links && payment.links.checkout) {
            console.log('Redirecting to payment page:', payment.links.checkout.href);
            console.log('Current location:', window.location.href);
            console.log('Protocol:', window.location.protocol);
            
            // Add a small delay to ensure console logs are visible
            setTimeout(() => {
                window.location.href = payment.links.checkout.href;
            }, 100);
        } else {
            console.error('No checkout URL found in payment response');
            throw new Error('No checkout URL found in payment response');
        }
    }
    
    // Check if payment is completed
    isPaymentCompleted(status) {
        return status === 'paid';
    }
    
    // Check if payment failed
    isPaymentFailed(status) {
        return ['failed', 'expired', 'canceled'].includes(status);
    }
}

// Initialize Mollie payment system
const molliePayment = new MolliePaymentSystem();

// Export for use in other files
window.molliePayment = molliePayment; 