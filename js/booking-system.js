// ========================================================================
// BOOKING SYSTEM JAVASCRIPT
// Handles booking form submission and modal interactions
// ========================================================================

class BookingSystem {
    constructor() {
        this.currentBooking = null;
        // Use absolute path to ensure it works from any page
        this.adminEndpoint = '/admin/booking-handler.php';
        this.init();
    }
    
    init() {
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        // Booking form submission
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.addEventListener('submit', (e) => this.handleBookingFormSubmit(e));
        }
        
        // Modal close buttons
        const closeModal = document.getElementById('closeBookingModal');
        const cancelBooking = document.getElementById('cancelBooking');
        const closeSuccessModal = document.getElementById('closeSuccessModal');
        const retryBooking = document.getElementById('retryBooking');
        
        if (closeModal) closeModal.addEventListener('click', () => this.closeModal());
        if (cancelBooking) cancelBooking.addEventListener('click', () => this.closeModal());
        if (closeSuccessModal) closeSuccessModal.addEventListener('click', () => this.closeModal());
        if (retryBooking) retryBooking.addEventListener('click', () => this.retryBooking());
        
        // Close modal on outside click
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) this.closeModal();
            });
        }
        
        // Booking details form submission
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        if (bookingDetailsForm) {
            bookingDetailsForm.addEventListener('submit', (e) => this.handleBookingDetailsSubmit(e));
        }
    }
    
    handleBookingFormSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const date = formData.get('date');
        const boatType = formData.get('boatType');
        
        if (!date || !boatType) {
            this.showError('Vul alle verplichte velden in.');
            return;
        }
        
        // Store current booking data
        this.currentBooking = {
            date: date,
            boatType: boatType
        };
        
        // Show modal and check availability
        this.showModal();
        this.checkAvailability(date, boatType);
    }
    
    async checkAvailability(date, boatType) {
        try {
            // Show loading state
            this.showLoadingState();
            
            // Check if boat is available on the selected date
            const isAvailable = await this.isBoatAvailable(date, boatType);
            
            if (isAvailable) {
                this.showBookingForm();
            } else {
                this.showUnavailableMessage(date, boatType);
            }
        } catch (error) {
            console.error('Error checking availability:', error);
            this.showError('Er is een fout opgetreden bij het controleren van de beschikbaarheid.');
        }
    }
    
    async isBoatAvailable(date, boatType) {
        try {
            // For now, we'll simulate availability check
            // In a real implementation, this would query the admin system
            const response = await fetch(this.adminEndpoint, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                const bookings = data.bookings || [];
                
                // Check if there's already a booking for this boat on this date
                const conflictingBooking = bookings.find(booking => 
                    booking.date === date && 
                    booking.boatType === boatType &&
                    booking.status !== 'payment-rejected'
                );
                
                return !conflictingBooking;
            }
            
            // If we can't reach the admin system, assume available
            return true;
        } catch (error) {
            console.error('Error checking availability:', error);
            // If there's an error, assume available to not block bookings
            return true;
        }
    }
    
    showLoadingState() {
        const availabilityCheck = document.getElementById('availabilityCheck');
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        const bookingSuccess = document.getElementById('bookingSuccess');
        const bookingError = document.getElementById('bookingError');
        
        if (availabilityCheck) availabilityCheck.classList.remove('hidden');
        if (bookingDetailsForm) bookingDetailsForm.classList.add('hidden');
        if (bookingSuccess) bookingSuccess.classList.add('hidden');
        if (bookingError) bookingError.classList.add('hidden');
    }
    
    showBookingForm() {
        const availabilityCheck = document.getElementById('availabilityCheck');
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        
        if (availabilityCheck) availabilityCheck.classList.add('hidden');
        if (bookingDetailsForm) bookingDetailsForm.classList.remove('hidden');
        
        // Update summary
        this.updateBookingSummary();
    }
    
    showUnavailableMessage(date, boatType) {
        const availabilityCheck = document.getElementById('availabilityCheck');
        const boatName = this.getBoatDisplayName(boatType);
        const formattedDate = new Date(date).toLocaleDateString('nl-NL', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        if (availabilityCheck) {
            availabilityCheck.innerHTML = `
                <div class="unavailable-message">
                    <div class="unavailable-icon">❌</div>
                    <h3>Niet beschikbaar</h3>
                    <p>De ${boatName} is helaas niet beschikbaar op ${formattedDate}.</p>
                    <p>Probeer een andere datum of een ander type boot.</p>
                    <button class="btn btn-outline" onclick="this.closeModal()">Sluiten</button>
                </div>
            `;
        }
    }
    
    updateBookingSummary() {
        if (!this.currentBooking) return;
        
        const summaryDate = document.getElementById('summaryDate');
        const summaryBoat = document.getElementById('summaryBoat');
        
        if (summaryDate) {
            const date = new Date(this.currentBooking.date);
            summaryDate.textContent = date.toLocaleDateString('nl-NL', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        
        if (summaryBoat) {
            summaryBoat.textContent = this.getBoatDisplayName(this.currentBooking.boatType);
        }
    }
    
    getBoatDisplayName(boatType) {
        const boatNames = {
            'classic-tender-720': 'Classic tender 720 (10/12 pers)',
            'electrosloop-10': 'Electrosloep voor 10 pers',
            'classic-tender-570': 'Classic tender 570 (8 pers)',
            'electrosloop-8': 'Electrosloep voor 8 pers',
            'sailboat-4-5': 'Zeilboot 4/5 pers',
            'sailpunter-3-4': 'Zeilpunter 3/4 pers',
            'electroboat-5': 'Electrosloep voor 5 pers',
            'canoe-3': 'Canadese kano 3 pers',
            'kayak-2': 'Kajak 2 pers',
            'kayak-1': 'Kajak 1 pers',
            'sup-board': 'SUP board 1 pers'
        };
        return boatNames[boatType] || boatType;
    }
    
    async handleBookingDetailsSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const bookingData = {
            date: this.currentBooking.date,
            boatType: this.currentBooking.boatType,
            customerName: formData.get('customerName'),
            customerEmail: formData.get('customerEmail'),
            customerPhone: formData.get('customerPhone'),
            notes: formData.get('customerNotes') || '',
            formType: 'booking'
        };
        
        // Validate required fields
        if (!bookingData.customerName || !bookingData.customerEmail || !bookingData.customerPhone) {
            this.showError('Vul alle verplichte velden in.');
            return;
        }
        
        try {
            // Show loading state
            this.showLoadingState();
            
            console.log('Submitting booking data:', bookingData);
            
            // Try multiple endpoint paths
            const endpoints = [
                this.adminEndpoint,
                '../admin/booking-handler.php',
                './admin/booking-handler.php'
            ];
            
            let response = null;
            let lastError = null;
            
            for (const endpoint of endpoints) {
                try {
                    console.log('Trying endpoint:', endpoint);
                    response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(bookingData)
                    });
                    
                    if (response.ok) {
                        break; // Success, exit the loop
                    }
                } catch (error) {
                    console.log('Failed to connect to:', endpoint, error);
                    lastError = error;
                }
            }
            
            if (!response || !response.ok) {
                throw new Error(`HTTP error! status: ${response?.status || 'No response'} - ${lastError?.message || 'Connection failed'}`);
            }
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            const result = await response.json();
            console.log('Response result:', result);
            
            if (result.success) {
                this.showSuccessMessage(result.bookingId);
            } else {
                this.showError(result.message || 'Er is een fout opgetreden bij het verwerken van uw reservering.');
            }
        } catch (error) {
            console.error('Error submitting booking:', error);
            this.showError(`Er is een fout opgetreden bij het verwerken van uw reservering. (${error.message})`);
        }
    }
    
    showSuccessMessage(bookingId) {
        const availabilityCheck = document.getElementById('availabilityCheck');
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        const bookingSuccess = document.getElementById('bookingSuccess');
        const bookingError = document.getElementById('bookingError');
        
        if (availabilityCheck) availabilityCheck.classList.add('hidden');
        if (bookingDetailsForm) bookingDetailsForm.classList.add('hidden');
        if (bookingError) bookingError.classList.add('hidden');
        if (bookingSuccess) bookingSuccess.classList.remove('hidden');
        
        // Set booking ID
        const bookingIdElement = document.getElementById('bookingId');
        if (bookingIdElement) {
            bookingIdElement.textContent = bookingId;
        }
        
        // Reset form
        this.resetForms();
    }
    
    showError(message) {
        const availabilityCheck = document.getElementById('availabilityCheck');
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        const bookingSuccess = document.getElementById('bookingSuccess');
        const bookingError = document.getElementById('bookingError');
        const errorMessage = document.getElementById('errorMessage');
        
        if (availabilityCheck) availabilityCheck.classList.add('hidden');
        if (bookingDetailsForm) bookingDetailsForm.classList.add('hidden');
        if (bookingSuccess) bookingSuccess.classList.add('hidden');
        if (bookingError) bookingError.classList.remove('hidden');
        
        if (errorMessage) {
            errorMessage.textContent = message;
        }
    }
    
    retryBooking() {
        if (this.currentBooking) {
            this.checkAvailability(this.currentBooking.date, this.currentBooking.boatType);
        }
    }
    
    showModal() {
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    
    closeModal() {
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // Reset everything
        this.resetForms();
        this.currentBooking = null;
        
        // Reset modal content
        this.resetModalContent();
    }
    
    resetForms() {
        const bookingForm = document.getElementById('bookingForm');
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        
        if (bookingForm) bookingForm.reset();
        if (bookingDetailsForm) bookingDetailsForm.reset();
    }
    
    resetModalContent() {
        const availabilityCheck = document.getElementById('availabilityCheck');
        if (availabilityCheck) {
            availabilityCheck.innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Beschikbaarheid controleren...</p>
                </div>
            `;
        }
    }
}

// Initialize the booking system when the page loads
document.addEventListener('DOMContentLoaded', () => {
    new BookingSystem();
}); 