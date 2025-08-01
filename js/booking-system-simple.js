// ========================================================================
// SIMPLE BOOKING SYSTEM - NO SERVER REQUIRED (LOCAL STORAGE)
// ========================================================================

class SimpleBookingSystem {
    constructor() {
        this.currentBooking = null;
        this.storageKey = 'nijenhuis_bookings';
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        console.log('Simple booking system initialized - using local storage');
    }
    
    setupEventListeners() {
        // Main booking form
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.addEventListener('submit', (e) => this.handleBookingFormSubmit(e));
        }
        
        // Modal close buttons
        const closeModal = document.getElementById('closeBookingModal');
        if (closeModal) {
            closeModal.addEventListener('click', () => this.closeModal());
        }
        
        // Modal backdrop click
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) this.closeModal();
            });
        }
        
        // Booking details form
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
        const engineOption = formData.get('engineOption');
        
        if (!date || !boatType) {
            alert('Please select both date and boat type');
            return;
        }
        
        // Store current booking info
        this.currentBooking = { date, boatType, engineOption };
        
        // Show modal and check availability
        this.showModal();
        this.checkAvailability();
    }
    
    checkAvailability() {
        this.showLoadingState();
        
        // Simulate availability check
        setTimeout(() => {
            const isAvailable = this.isBoatAvailable(this.currentBooking.date, this.currentBooking.boatType);
            
            if (isAvailable) {
                this.showBookingForm();
            } else {
                this.showUnavailableMessage();
            }
        }, 1000);
    }
    
    isBoatAvailable(date, boatType) {
        const bookings = this.loadBookings();
        const boats = this.loadBoats();
        
        // Find the boat in the boat management system
        const boat = boats.find(b => b.id === boatType);
        if (!boat) {
            console.error('Boat not found in boat management system:', boatType);
            return false;
        }
        
        // Check if there are any boats available
        if (boat.available <= 0) {
            return false;
        }
        
        // Check if there's already a booking for this boat on this date
        const conflictingBookings = bookings.filter(booking => 
            booking.date === date && 
            booking.boatType === boatType &&
            booking.status !== 'payment-rejected' &&
            booking.status !== 'boat-picked-up'
        );
        
        // Check if we have enough boats available for this date
        return conflictingBookings.length < boat.available;
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
        if (bookingDetailsForm) {
            bookingDetailsForm.classList.remove('hidden');
            this.updateBookingSummary();
        }
    }
    
    showUnavailableMessage() {
        const availabilityCheck = document.getElementById('availabilityCheck');
        const bookingError = document.getElementById('bookingError');
        
        if (availabilityCheck) availabilityCheck.classList.add('hidden');
        if (bookingError) {
            bookingError.classList.remove('hidden');
            bookingError.innerHTML = `
                <div class="error-message">
                    <h3>❌ Boat Not Available</h3>
                    <p>The ${this.getBoatDisplayName(this.currentBooking.boatType, this.currentBooking.engineOption)} is not available on ${this.formatDate(this.currentBooking.date)}.</p>
                    <p>Please try a different date or boat type.</p>
                    <button onclick="window.bookingSystem.closeModal()" class="btn">Close</button>
                </div>
            `;
        }
    }
    
    updateBookingSummary() {
        const summaryElement = document.getElementById('bookingSummary');
        if (summaryElement) {
            summaryElement.innerHTML = `
                <div class="booking-summary">
                    <h3>Booking Summary</h3>
                    <p><strong>Date:</strong> ${this.formatDate(this.currentBooking.date)}</p>
                    <p><strong>Boat:</strong> ${this.getBoatDisplayName(this.currentBooking.boatType, this.currentBooking.engineOption)}</p>
                </div>
            `;
        }
    }
    
    getBoatDisplayName(boatType, engineOption) {
        let boatName = '';
        const boatNames = {
            'classic-tender-720': 'Classic tender 720 (10/12 pers)',
            'classic-tender-570': 'Classic tender 570 (8 pers)',
            'electrosloop-10': 'Electrosloep voor 10 pers',
            'electrosloop-8': 'Electrosloep voor 8 pers',
            'electroboat-5': 'Electrosloep voor 5 pers',
            'sailboat-4-5': 'Zeilboot',
            'sailpunter-3-4': 'Zeilpunter 3/4 pers',
            'canoe-3': 'Canadese kano 3 pers',
            'kayak-2': 'Kajak 2 pers',
            'kayak-1': 'Kajak 1 pers',
            'sup-board': 'SUP board 1 pers'
        };
        
        boatName = boatNames[boatType] || boatType;
        
        // Add engine option for sailboats
        if (boatType === 'sailboat-4-5' && engineOption) {
            if (engineOption === 'with') {
                boatName += ' (met motor)';
            } else {
                boatName += ' (zonder motor)';
            }
        }
        
        return boatName;
    }
    
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('nl-NL', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    handleBookingDetailsSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const customerName = formData.get('customerName');
        const customerEmail = formData.get('customerEmail');
        const customerPhone = formData.get('customerPhone');
        const notes = formData.get('notes');
        
        // Validate required fields
        if (!customerName || !customerEmail || !customerPhone) {
            alert('Please fill in all required fields');
            return;
        }
        
        // Create booking object
        const bookingData = {
            id: this.generateId(),
            date: this.currentBooking.date,
            boatType: this.currentBooking.boatType,
            engineOption: this.currentBooking.engineOption || null,
            customerName,
            customerEmail,
            customerPhone,
            notes: notes || '',
            status: 'not-confirmed',
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };
        
        // Save booking
        this.saveBooking(bookingData);
        
        // Show success message
        this.showSuccessMessage();
    }
    
    saveBooking(bookingData) {
        try {
            const bookings = this.loadBookings();
            bookings.push(bookingData);
            localStorage.setItem(this.storageKey, JSON.stringify(bookings));
            
            // Update boat availability
            this.updateBoatAvailability(bookingData.boatType, -1);
            
            // Refresh stock display in admin if available
            if (typeof refreshBoatStockDisplay === 'function') {
                refreshBoatStockDisplay();
            }
            
            console.log('Booking saved successfully:', bookingData);
            console.log('Total bookings in storage:', bookings.length);
            
            return true;
        } catch (error) {
            console.error('Error saving booking:', error);
            return false;
        }
    }
    
    loadBookings() {
        try {
            const stored = localStorage.getItem(this.storageKey);
            return stored ? JSON.parse(stored) : [];
        } catch (error) {
            console.error('Error loading bookings:', error);
            return [];
        }
    }
    
    loadBoats() {
        try {
            const stored = localStorage.getItem('nijenhuis_boats');
            if (stored) {
                return JSON.parse(stored);
            }
            
            // Return default boats if none stored
            return [
                { id: 'classic-tender-720', available: 2 },
                { id: 'classic-tender-570', available: 2 },
                { id: 'electrosloop-10', available: 1 },
                { id: 'electrosloop-8', available: 2 },
                { id: 'electroboat-5', available: 2 },
                { id: 'sailboat-4-5', available: 2 },
                { id: 'sailboat-4-5-engine', available: 1 },
                { id: 'sailpunter-3-4', available: 1 },
                { id: 'canoe-3', available: 3 },
                { id: 'kayak-2', available: 2 },
                { id: 'kayak-1', available: 2 },
                { id: 'sup-board', available: 2 }
            ];
        } catch (error) {
            console.error('Error loading boats:', error);
            return [];
        }
    }
    
    updateBoatAvailability(boatType, change) {
        try {
            const boats = this.loadBoats();
            const boatIndex = boats.findIndex(b => b.id === boatType);
            
            if (boatIndex !== -1) {
                boats[boatIndex].available = Math.max(0, boats[boatIndex].available + change);
                localStorage.setItem('nijenhuis_boats', JSON.stringify(boats));
                console.log(`Updated ${boatType} availability: ${boats[boatIndex].available}`);
            }
        } catch (error) {
            console.error('Error updating boat availability:', error);
        }
    }
    
    generateId() {
        return 'booking_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    showSuccessMessage() {
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        const bookingSuccess = document.getElementById('bookingSuccess');
        
        if (bookingDetailsForm) bookingDetailsForm.classList.add('hidden');
        if (bookingSuccess) {
            bookingSuccess.classList.remove('hidden');
            bookingSuccess.innerHTML = `
                <div class="success-message">
                    <h3>✅ Booking Submitted Successfully!</h3>
                    <p>Your booking for the ${this.getBoatDisplayName(this.currentBooking.boatType, this.currentBooking.engineOption)} on ${this.formatDate(this.currentBooking.date)} has been submitted.</p>
                    <p>We will contact you soon to confirm your reservation.</p>
                    <div class="success-actions">
                        <button onclick="window.bookingSystem.closeModal()" class="btn">Close</button>
                        <button onclick="window.bookingSystem.retryBooking()" class="btn btn-outline">Make Another Booking</button>
                    </div>
                </div>
            `;
        }
    }
    
    retryBooking() {
        this.resetForms();
        this.resetModalContent();
        this.closeModal();
        
        // Reset the main form
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.reset();
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
            this.resetForms();
            this.resetModalContent();
        }
    }
    
    resetForms() {
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        if (bookingDetailsForm) {
            bookingDetailsForm.reset();
        }
    }
    
    resetModalContent() {
        const availabilityCheck = document.getElementById('availabilityCheck');
        const bookingDetailsForm = document.getElementById('bookingDetailsForm');
        const bookingSuccess = document.getElementById('bookingSuccess');
        const bookingError = document.getElementById('bookingError');
        
        if (availabilityCheck) {
            availabilityCheck.classList.remove('hidden');
            availabilityCheck.innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Beschikbaarheid controleren...</p>
                </div>
            `;
        }
        if (bookingDetailsForm) bookingDetailsForm.classList.add('hidden');
        if (bookingSuccess) bookingSuccess.classList.add('hidden');
        if (bookingError) bookingError.classList.add('hidden');
    }
}

// Initialize the booking system when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.bookingSystem = new SimpleBookingSystem();
}); 