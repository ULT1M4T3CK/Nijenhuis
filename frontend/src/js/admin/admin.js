// ========================================================================
// ADMIN DASHBOARD JAVASCRIPT
// Handles authentication, calendar, and booking management
// ========================================================================

class AdminDashboard {
    constructor() {
        this.currentDate = new Date();
        this.bookings = [];
        this.currentUser = null;
        this.isAuthenticated = false;
        
        this.init();
    }
    
    async init() {
        this.setupEventListeners();
        this.checkAuthentication();
        await this.loadBookingsData();
    }
    
    async loadBookingsData() {
        this.bookings = await this.loadBookings();
    }
    
    setupEventListeners() {
        // Login form
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLogin(e));
        }
        
        // Logout button
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => this.handleLogout());
        }
        
        // Calendar navigation
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');
        if (prevMonthBtn) prevMonthBtn.addEventListener('click', () => this.navigateMonth(-1));
        if (nextMonthBtn) nextMonthBtn.addEventListener('click', () => this.navigateMonth(1));
        
        // Filters
        const boatFilter = document.getElementById('boatFilter');
        const statusFilter = document.getElementById('statusFilter');
        if (boatFilter) boatFilter.addEventListener('change', () => this.renderCalendar());
        if (statusFilter) statusFilter.addEventListener('change', () => this.renderCalendar());
        
        // Add booking button
        const addBookingBtn = document.getElementById('addBookingBtn');
        if (addBookingBtn) {
            addBookingBtn.addEventListener('click', () => this.openBookingModal());
        }
        
        // Modal events
        const closeModal = document.getElementById('closeModal');
        const cancelBooking = document.getElementById('cancelBooking');
        const bookingForm = document.getElementById('bookingForm');
        
        if (closeModal) closeModal.addEventListener('click', () => this.closeModal());
        if (cancelBooking) cancelBooking.addEventListener('click', () => this.closeModal());
        if (bookingForm) bookingForm.addEventListener('submit', (e) => this.handleBookingSubmit(e));
        
        // Close modal on outside click
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) this.closeModal();
            });
        }
    }
    
    async checkAuthentication() {
        try {
            const response = await fetch('booking-handler.php?action=session', { method: 'GET', headers: { 'Content-Type': 'application/json' } });
            if (response.ok) {
                const data = await response.json();
                if (data.authenticated) {
                    this.isAuthenticated = true;
                    localStorage.setItem('csrfToken', data.csrfToken || '');
                    this.currentUser = { username: localStorage.getItem('adminUser') || 'admin', loginTime: new Date().toISOString() };
                    this.showDashboard();
                    return;
                }
            }
        } catch (e) {}
        this.showLogin();
    }
    
    async handleLogin(e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        try {
            const response = await fetch('booking-handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'login', username, password })
            });
            const result = await response.json();
            if (response.ok && result.success) {
                this.currentUser = { username, loginTime: new Date().toISOString() };
                localStorage.setItem('adminUser', JSON.stringify(this.currentUser));
                if (result.csrfToken) localStorage.setItem('csrfToken', result.csrfToken);
                this.isAuthenticated = true;
                await this.showDashboard();
            } else {
                this.showLoginError(result.message || 'Invalid username or password');
            }
        } catch (err) {
            this.showLoginError('Login failed');
        }
    }
    
    async handleLogout() {
        try {
            const csrf = localStorage.getItem('csrfToken') || '';
            await fetch('booking-handler.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf }, body: JSON.stringify({ action: 'logout', csrfToken: csrf }) });
        } catch (e) {}
        localStorage.removeItem('adminUser');
        localStorage.removeItem('csrfToken');
        this.currentUser = null;
        this.isAuthenticated = false;
        this.showLogin();
    }
    
    showLoginError(message) {
        const errorDiv = document.getElementById('loginError');
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            setTimeout(() => {
                errorDiv.classList.add('hidden');
            }, 3000);
        }
    }
    
    showLogin() {
        const loginScreen = document.getElementById('loginScreen');
        const adminDashboard = document.getElementById('adminDashboard');
        
        if (loginScreen) loginScreen.classList.remove('hidden');
        if (adminDashboard) adminDashboard.classList.add('hidden');
    }
    
    async showDashboard() {
        const loginScreen = document.getElementById('loginScreen');
        const adminDashboard = document.getElementById('adminDashboard');
        
        if (loginScreen) loginScreen.classList.add('hidden');
        if (adminDashboard) adminDashboard.classList.remove('hidden');
        
        // Reload bookings before rendering
        this.bookings = await this.loadBookings();
        
        this.renderCalendar();
        this.renderBookingList();
        this.updateStats();
    }
    
    navigateMonth(direction) {
        this.currentDate.setMonth(this.currentDate.getMonth() + direction);
        this.renderCalendar();
    }
    
    renderCalendar() {
        const calendarGrid = document.getElementById('calendarGrid');
        const currentMonthElement = document.getElementById('currentMonth');
        
        if (!calendarGrid) return;
        
        // Update month display
        if (currentMonthElement) {
            currentMonthElement.textContent = this.currentDate.toLocaleDateString('en-US', {
                month: 'long',
                year: 'numeric'
            });
        }
        
        // Clear calendar
        calendarGrid.innerHTML = '';
        
        // Add day headers
        const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        daysOfWeek.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'calendar-day-header';
            dayHeader.textContent = day;
            calendarGrid.appendChild(dayHeader);
        });
        
        // Get first day of month and number of days
        const firstDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
        const lastDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());
        
        // Generate calendar days
        for (let i = 0; i < 42; i++) {
            const currentDate = new Date(startDate);
            currentDate.setDate(startDate.getDate() + i);
            
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            
            // Check if day is from other month
            if (currentDate.getMonth() !== this.currentDate.getMonth()) {
                dayElement.classList.add('other-month');
            }
            
            // Add day number
            const dayNumber = document.createElement('div');
            dayNumber.className = 'calendar-day-number';
            dayNumber.textContent = currentDate.getDate();
            dayElement.appendChild(dayNumber);
            
            // Add bookings for this day
            const dayBookings = this.getBookingsForDate(currentDate);
            if (dayBookings.length > 0) {
                const bookingsContainer = document.createElement('div');
                bookingsContainer.className = 'calendar-bookings';
                
                dayBookings.forEach(booking => {
                    const bookingIndicator = document.createElement('div');
                    bookingIndicator.className = `booking-indicator booking-status-${booking.status}`;
                    bookingIndicator.textContent = `${booking.customerName} - ${this.getBoatDisplayName(booking.boatType)}`;
                    bookingIndicator.title = `${booking.customerName} - ${this.getBoatDisplayName(booking.boatType)} (${booking.status})`;
                    bookingIndicator.addEventListener('click', () => this.openBookingModal(booking));
                    bookingsContainer.appendChild(bookingIndicator);
                });
                
                dayElement.appendChild(bookingsContainer);
            }
            
            // Add click event to create new booking
            dayElement.addEventListener('click', (e) => {
                if (!e.target.classList.contains('booking-indicator')) {
                    this.openBookingModal(null, currentDate);
                }
            });
            
            calendarGrid.appendChild(dayElement);
        }
    }
    
    getBookingsForDate(date) {
        const boatFilter = document.getElementById('boatFilter')?.value;
        const statusFilter = document.getElementById('statusFilter')?.value;
        
        return this.bookings.filter(booking => {
            const bookingDate = new Date(booking.date);
            const isSameDate = bookingDate.toDateString() === date.toDateString();
            
            const matchesBoat = !boatFilter || booking.boatType === boatFilter;
            const matchesStatus = !statusFilter || booking.status === statusFilter;
            
            return isSameDate && matchesBoat && matchesStatus;
        });
    }
    
    getBoatDisplayName(boatType) {
        const boatNames = {
            'classic-tender-720': 'Tender 720',
            'electrosloop-10': 'Electrosloep 10',
            'classic-tender-570': 'Tender 570',
            'electrosloop-8': 'Electrosloep 8',
            'sailboat-4-5': 'Zeilboot 4/5',
            'sailpunter-3-4': 'Zeilpunter 3/4',
            'electroboat-5': 'Electrosloep 5',
            'canoe-3': 'Canoe 3',
            'kayak-2': 'Kayak 2',
            'kayak-1': 'Kayak 1',
            'sup-board': 'SUP Board'
        };
        return boatNames[boatType] || boatType;
    }
    
    renderBookingList() {
        const bookingList = document.getElementById('bookingList');
        if (!bookingList) return;
        
        bookingList.innerHTML = '';
        
        // Sort bookings by date (newest first)
        const sortedBookings = [...this.bookings].sort((a, b) => new Date(b.date) - new Date(a.date));
        
        sortedBookings.forEach(booking => {
            const escapeHTML = (value) => typeof value === 'string' ? value.replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])) : value;
            const bookingElement = document.createElement('div');
            bookingElement.className = 'booking-item';
            bookingElement.addEventListener('click', () => this.openBookingModal(booking));
            
            const date = new Date(booking.date);
            const formattedDate = date.toLocaleDateString('en-US', {
                weekday: 'short',
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
            
            const safeName = escapeHTML(booking.customerName || '');
            const safeEmail = escapeHTML(booking.customerEmail || '');
            const safePhone = escapeHTML(booking.customerPhone || '');
            const safeNotes = booking.notes ? escapeHTML(booking.notes) : '';

            bookingElement.innerHTML = `
                <div class="booking-item-header">
                    <div class="booking-customer">${safeName}</div>
                    <div class="booking-status booking-status-${booking.status}">${this.formatStatus(booking.status)}</div>
                </div>
                <div class="booking-details">
                    <div class="booking-date">${formattedDate}</div>
                    <div class="booking-boat">${this.getBoatDisplayName(booking.boatType)}</div>
                    <div>${safeEmail} | ${safePhone}</div>
                    ${safeNotes ? `<div style=\"margin-top: 8px; font-style: italic;\">${safeNotes}</div>` : ''}
                </div>
            `;
            
            bookingList.appendChild(bookingElement);
        });
    }
    
    formatStatus(status) {
        const statusMap = {
            'not-confirmed': 'Not Confirmed',
            'confirmed-not-paid': 'Confirmed, Not Paid',
            'confirmed-paid': 'Confirmed & Paid',
            'payment-rejected': 'Payment Rejected'
        };
        return statusMap[status] || status;
    }
    
    updateStats() {
        const totalBookings = document.getElementById('totalBookings');
        const pendingBookings = document.getElementById('pendingBookings');
        
        if (totalBookings) {
            totalBookings.textContent = this.bookings.length;
        }
        
        if (pendingBookings) {
            const pending = this.bookings.filter(b => b.status === 'not-confirmed').length;
            pendingBookings.textContent = pending;
        }
    }
    
    openBookingModal(booking = null, defaultDate = null) {
        const modal = document.getElementById('bookingModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('bookingForm');
        
        if (!modal || !modalTitle || !form) return;
        
        // Set modal title
        modalTitle.textContent = booking ? 'Edit Booking' : 'Add New Booking';
        
        // Reset form
        form.reset();
        
        if (booking) {
            // Edit existing booking
            document.getElementById('modalDate').value = booking.date;
            document.getElementById('modalBoatType').value = booking.boatType;
            document.getElementById('modalCustomerName').value = booking.customerName;
            document.getElementById('modalCustomerEmail').value = booking.customerEmail;
            document.getElementById('modalCustomerPhone').value = booking.customerPhone;
            document.getElementById('modalStatus').value = booking.status;
            document.getElementById('modalNotes').value = booking.notes || '';
            
            // Store booking ID for update
            form.dataset.bookingId = booking.id;
        } else {
            // New booking
            if (defaultDate) {
                document.getElementById('modalDate').value = defaultDate.toISOString().split('T')[0];
            } else {
                document.getElementById('modalDate').value = new Date().toISOString().split('T')[0];
            }
            delete form.dataset.bookingId;
        }
        
        modal.classList.add('active');
    }
    
    closeModal() {
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.classList.remove('active');
        }
    }
    
    async handleBookingSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const bookingId = form.dataset.bookingId;
        
        const bookingData = {
            date: document.getElementById('modalDate').value,
            boatType: document.getElementById('modalBoatType').value,
            customerName: document.getElementById('modalCustomerName').value,
            customerEmail: document.getElementById('modalCustomerEmail').value,
            customerPhone: document.getElementById('modalCustomerPhone').value,
            status: document.getElementById('modalStatus').value,
            notes: document.getElementById('modalNotes').value
        };
        
        if (bookingId) {
            // Update existing booking
            await this.updateBooking(bookingId, bookingData);
        } else {
            // Create new booking
            await this.createBooking(bookingData);
        }
        
        this.closeModal();
    }
    
    async createBooking(bookingData) {
        try {
            const csrf = localStorage.getItem('csrfToken') || '';
            const response = await fetch('booking-handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrf
                },
                body: JSON.stringify({
                    action: 'createBooking',
                    bookingData: bookingData,
                    csrfToken: csrf
                })
            });
            const result = await response.json();
            if (response.ok && result.success) {
                this.bookings = await this.loadBookings();
                this.renderCalendar();
                this.renderBookingList();
                this.updateStats();
                this.showNotification('Booking created successfully', 'success');
            } else {
                this.showNotification(result.message || 'Failed to create booking', 'error');
            }
        } catch (error) {
            console.error('Error creating booking:', error);
            this.showNotification('Failed to create booking', 'error');
        }
    }
    
    async updateBooking(bookingId, bookingData) {
        try {
            const csrf = localStorage.getItem('csrfToken') || '';
            const response = await fetch('booking-handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrf
                },
                body: JSON.stringify({
                    action: 'updateBooking',
                    bookingId: bookingId,
                    bookingData: bookingData,
                    csrfToken: csrf
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Reload bookings from server
                this.bookings = await this.loadBookings();
                this.renderCalendar();
                this.renderBookingList();
                this.updateStats();
                
                this.showNotification('Booking updated successfully', 'success');
            } else {
                this.showNotification(result.message || 'Failed to update booking', 'error');
            }
        } catch (error) {
            console.error('Error updating booking:', error);
            this.showNotification('Failed to update booking', 'error');
        }
    }
    
    async deleteBooking(bookingId) {
        if (confirm('Are you sure you want to delete this booking?')) {
            try {
                const csrf = localStorage.getItem('csrfToken') || '';
                const response = await fetch('booking-handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrf
                    },
                    body: JSON.stringify({
                        action: 'deleteBooking',
                        bookingId: bookingId,
                        csrfToken: csrf
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Reload bookings from server
                    this.bookings = await this.loadBookings();
                    this.renderCalendar();
                    this.renderBookingList();
                    this.updateStats();
                    
                    this.showNotification('Booking deleted successfully', 'success');
                } else {
                    this.showNotification(result.message || 'Failed to delete booking', 'error');
                }
            } catch (error) {
                console.error('Error deleting booking:', error);
                this.showNotification('Failed to delete booking', 'error');
            }
        }
    }
    
    generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }
    
    async loadBookings() {
        try {
            try {
                const csrf = localStorage.getItem('csrfToken') || '';
                const response = await fetch('booking-handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
                    body: JSON.stringify({ action: 'getBookings' })
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.csrfToken) localStorage.setItem('csrfToken', data.csrfToken);
                    return data.bookings || [];
                }
            } catch (error) {
                console.log('Failed to load bookings:', error);
            }
        } catch (error) {
            console.error('Error loading bookings:', error);
        }
        
        // Return empty array if there's an error
        return [];
    }
    
    async saveBookings() {
        // This method is no longer needed as we're using the API
        // Bookings are saved directly to the server via API calls
    }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-message">${message}</div>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        }, 100);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
        
        // Close button functionality
        const closeBtn = notification.querySelector('.notification-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            });
        }
    }
}

// Initialize the admin dashboard when the page loads
document.addEventListener('DOMContentLoaded', () => {
    new AdminDashboard().init();
}); 