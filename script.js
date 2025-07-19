/**
 * Modern JavaScript for Nijenhuis Boat Rental Website
 * ES6+ features, modules, performance optimizations
 */

// Constants
const CONFIG = {
    API_BASE_URL: 'https://api.nijenhuis-botenverhuur.com',
    CACHE_NAME: 'nijenhuis-cache-v1',
    CACHE_DURATION: 24 * 60 * 60 * 1000, // 24 hours
    ANIMATION_DURATION: 300,
    BREAKPOINTS: {
        mobile: 768,
        tablet: 1024,
        desktop: 1200
    }
};

// Utility Functions
const utils = {
    /**
     * Debounce function to limit function calls
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Throttle function to limit function calls
     */
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    /**
     * Check if element is in viewport
     */
    isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    },

    /**
     * Smooth scroll to element
     */
    scrollToElement(element, offset = 0) {
        const elementPosition = element.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - offset;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    },

    /**
     * Format date to local string
     */
    formatDate(date) {
        return new Intl.DateTimeFormat('en-GB', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).format(date);
    },

    /**
     * Format currency
     */
    formatCurrency(amount, currency = 'EUR') {
        return new Intl.NumberFormat('en-GB', {
            style: 'currency',
            currency: currency
        }).format(amount);
    },

    /**
     * Generate unique ID
     */
    generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    },

    /**
     * Local storage wrapper
     */
    storage: {
        get(key) {
            try {
                const item = localStorage.getItem(key);
                return item ? JSON.parse(item) : null;
            } catch (error) {
                console.error('Error reading from localStorage:', error);
                return null;
            }
        },

        set(key, value) {
            try {
                localStorage.setItem(key, JSON.stringify(value));
                return true;
            } catch (error) {
                console.error('Error writing to localStorage:', error);
                return false;
            }
        },

        remove(key) {
            try {
                localStorage.removeItem(key);
                return true;
            } catch (error) {
                console.error('Error removing from localStorage:', error);
                return false;
            }
        }
    }
};

// API Service
class ApiService {
    constructor() {
        this.baseURL = CONFIG.API_BASE_URL;
        this.cache = new Map();
    }

    /**
     * Make API request with caching
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const cacheKey = `${options.method || 'GET'}_${url}`;

        // Check cache first
        if (options.method === 'GET' && this.cache.has(cacheKey)) {
            const cached = this.cache.get(cacheKey);
            if (Date.now() - cached.timestamp < CONFIG.CACHE_DURATION) {
                return cached.data;
            }
        }

        try {
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    ...options.headers
                },
                ...options
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            // Cache GET requests
            if (options.method === 'GET' || !options.method) {
                this.cache.set(cacheKey, {
                    data,
                    timestamp: Date.now()
                });
            }

            return data;
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    }

    /**
     * Get boat availability
     */
    async getAvailability(date, boatType = null) {
        const params = new URLSearchParams({ date });
        if (boatType) params.append('boatType', boatType);
        
        return this.request(`/availability?${params}`);
    }

    /**
     * Get all boats
     */
    async getBoats() {
        return this.request('/boats');
    }

    /**
     * Submit booking
     */
    async submitBooking(bookingData) {
        return this.request('/bookings', {
            method: 'POST',
            body: JSON.stringify(bookingData)
        });
    }
}

// UI Components
class UIComponents {
    constructor() {
        this.notifications = [];
    }

    /**
     * Create notification
     */
    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span>${message}</span>
                <button class="notification-close" aria-label="Close notification">&times;</button>
            </div>
        `;

        // Add to page
        document.body.appendChild(notification);

        // Store reference
        this.notifications.push(notification);

        // Auto remove
        const autoRemove = setTimeout(() => {
            this.removeNotification(notification);
        }, duration);

        // Close button
        notification.querySelector('.notification-close').addEventListener('click', () => {
            clearTimeout(autoRemove);
            this.removeNotification(notification);
        });

        // Trigger animation
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        });

        return notification;
    }

    /**
     * Remove notification
     */
    removeNotification(notification) {
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.notifications = this.notifications.filter(n => n !== notification);
        }, 300);
    }

    /**
     * Create loading spinner
     */
    createSpinner(container) {
        const spinner = document.createElement('div');
        spinner.className = 'loading-spinner';
        spinner.innerHTML = `
            <div class="spinner"></div>
            <p>Loading...</p>
        `;
        
        container.appendChild(spinner);
        return spinner;
    }

    /**
     * Remove loading spinner
     */
    removeSpinner(spinner) {
        if (spinner && spinner.parentNode) {
            spinner.parentNode.removeChild(spinner);
        }
    }

    /**
     * Create modal
     */
    createModal(content, options = {}) {
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-backdrop"></div>
            <div class="modal-content">
                <button class="modal-close" aria-label="Close modal">&times;</button>
                ${content}
            </div>
        `;

        document.body.appendChild(modal);

        // Close handlers
        const closeModal = () => {
            modal.classList.add('modal-closing');
            setTimeout(() => {
                if (modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            }, 300);
        };

        modal.querySelector('.modal-close').addEventListener('click', closeModal);
        modal.querySelector('.modal-backdrop').addEventListener('click', closeModal);

        // ESC key
        const handleEsc = (e) => {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', handleEsc);
            }
        };
        document.addEventListener('keydown', handleEsc);

        // Show modal
        requestAnimationFrame(() => {
            modal.classList.add('modal-show');
        });

        return modal;
    }
}

// Main Application Class
class NijenhuisWebsite {
    constructor() {
        this.api = new ApiService();
        this.ui = new UIComponents();
        this.currentBreakpoint = this.getBreakpoint();
        this.isMobileMenuOpen = false;
        
        this.init();
    }

    /**
     * Initialize the application
     */
    init() {
        this.setupEventListeners();
        this.setupMobileMenu();
        this.setupBookingForm();
        this.setupLazyLoading();
        this.setupIntersectionObserver();
        this.setupServiceWorker();
        this.loadBoats();
        this.setupAnalytics();
        
        // Performance monitoring
        this.setupPerformanceMonitoring();
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Window events
        window.addEventListener('resize', utils.debounce(() => {
            this.handleResize();
        }, 250));

        window.addEventListener('scroll', utils.throttle(() => {
            this.handleScroll();
        }, 100));

        // Navigation events
        document.addEventListener('click', (e) => {
            // Handle smooth scrolling for anchor links
            if (e.target.matches('a[href^="#"]')) {
                e.preventDefault();
                const target = document.querySelector(e.target.getAttribute('href'));
                if (target) {
                    utils.scrollToElement(target, 80);
                }
            }
        });

        // Form events
        document.addEventListener('submit', (e) => {
            if (e.target.matches('form')) {
                this.handleFormSubmit(e);
            }
        });
    }

    /**
     * Handle window resize
     */
    handleResize() {
        const newBreakpoint = this.getBreakpoint();
        if (newBreakpoint !== this.currentBreakpoint) {
            this.currentBreakpoint = newBreakpoint;
            this.handleBreakpointChange();
        }
    }

    /**
     * Get current breakpoint
     */
    getBreakpoint() {
        const width = window.innerWidth;
        if (width < CONFIG.BREAKPOINTS.mobile) return 'mobile';
        if (width < CONFIG.BREAKPOINTS.tablet) return 'tablet';
        if (width < CONFIG.BREAKPOINTS.desktop) return 'desktop';
        return 'large';
    }

    /**
     * Handle breakpoint change
     */
    handleBreakpointChange() {
        // Close mobile menu when switching to desktop
        if (this.currentBreakpoint !== 'mobile' && this.isMobileMenuOpen) {
            this.closeMobileMenu();
        }
    }

    /**
     * Handle scroll events
     */
    handleScroll() {
        const scrollTop = window.pageYOffset;
        const nav = document.querySelector('.main-nav');
        
        // Add/remove scrolled class for nav styling
        if (scrollTop > 100) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }

        // Parallax effect for hero section
        const hero = document.querySelector('.hero');
        if (hero) {
            const scrolled = scrollTop * 0.5;
            hero.style.transform = `translateY(${scrolled}px)`;
        }
    }

    /**
     * Setup mobile menu
     */
    setupMobileMenu() {
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const navMenu = document.getElementById('navMenu');

        if (!mobileToggle || !navMenu) return;

        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleMobileMenu();
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileToggle.contains(e.target) && !navMenu.contains(e.target)) {
                this.closeMobileMenu();
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isMobileMenuOpen) {
                this.closeMobileMenu();
            }
        });
    }

    /**
     * Toggle mobile menu
     */
    toggleMobileMenu() {
        if (this.isMobileMenuOpen) {
            this.closeMobileMenu();
        } else {
            this.openMobileMenu();
        }
    }

    /**
     * Open mobile menu
     */
    openMobileMenu() {
        const navMenu = document.getElementById('navMenu');
        const mobileToggle = document.getElementById('mobileMenuToggle');
        
        navMenu.classList.add('active');
        this.isMobileMenuOpen = true;
        
        // Update icon
        mobileToggle.innerHTML = `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
        `;

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    /**
     * Close mobile menu
     */
    closeMobileMenu() {
        const navMenu = document.getElementById('navMenu');
        const mobileToggle = document.getElementById('mobileMenuToggle');
        
        navMenu.classList.remove('active');
        this.isMobileMenuOpen = false;
        
        // Update icon
        mobileToggle.innerHTML = `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
            </svg>
        `;

        // Restore body scroll
        document.body.style.overflow = '';
    }

    /**
     * Setup booking form
     */
    setupBookingForm() {
        const form = document.getElementById('bookingForm');
        const dateInput = document.getElementById('date');
        const boatSelect = document.getElementById('boatType');

        if (!form || !dateInput || !boatSelect) return;

        // Set default date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateInput.value = utils.formatDate(tomorrow).split('/').reverse().join('-');

        // Real-time availability check
        dateInput.addEventListener('change', utils.debounce(async () => {
            await this.checkAvailability(dateInput.value);
        }, 500));

        // Form submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleBooking(form);
        });
    }

    /**
     * Handle booking form submission
     */
    async handleBooking(form) {
        const formData = new FormData(form);
        const date = formData.get('date');
        const boatType = formData.get('boatType');

        if (!date || !boatType) {
            this.ui.showNotification('Vul alle verplichte velden in.', 'error');
            return;
        }

        try {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Controleren...';
            submitBtn.disabled = true;

            // Check availability
            const availability = await this.api.getAvailability(date, boatType);
            
            if (availability.available) {
                this.ui.showNotification('Boat is available! Redirecting to booking page...', 'success');
                
                // Store booking data
                utils.storage.set('pendingBooking', {
                    date,
                    boatType,
                    timestamp: Date.now()
                });

                // Redirect to booking page
                setTimeout(() => {
                    window.location.href = `/en/boat-hire/${boatType}/?date=${date}`;
                }, 1500);
            } else {
                this.ui.showNotification('Sorry, this boat is not available on the selected date.', 'error');
            }

        } catch (error) {
            console.error('Booking error:', error);
            this.ui.showNotification('An error occurred. Please try again.', 'error');
        } finally {
            // Reset button
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    }

    /**
     * Check availability for a date
     */
    async checkAvailability(date, boatType = null) {
        try {
            const availability = await this.api.getAvailability(date, boatType);
            this.updateBoatAvailability(availability);
            return availability;
        } catch (error) {
            console.error('Availability check failed:', error);
            return null;
        }
    }

    /**
     * Update boat availability display
     */
    updateBoatAvailability(availability) {
        const boatCards = document.querySelectorAll('.boat-card');
        
        boatCards.forEach(card => {
            const boatId = card.dataset.boatId;
            const statusElement = card.querySelector('.boat-status');
            const bookButton = card.querySelector('.btn');
            
            if (availability.boats && availability.boats[boatId]) {
                const boat = availability.boats[boatId];
                
                if (boat.available) {
                    statusElement.textContent = 'Available';
                    statusElement.className = 'boat-status available';
                    bookButton.disabled = false;
                    bookButton.textContent = 'Book Now';
                } else {
                    statusElement.textContent = 'Occupied';
                    statusElement.className = 'boat-status occupied';
                    bookButton.disabled = true;
                    bookButton.textContent = 'Not Available';
                }
            }
        });
    }

    /**
     * Load boats data
     */
    async loadBoats() {
        const boatsGrid = document.getElementById('boatsGrid');
        if (!boatsGrid) return;

        try {
            // Show loading state
            const spinner = this.ui.createSpinner(boatsGrid);

            // Load boats data
            const boats = await this.api.getBoats();
            
            // Remove spinner
            this.ui.removeSpinner(spinner);

            // Render boats
            this.renderBoats(boats);

        } catch (error) {
            console.error('Failed to load boats:', error);
            boatsGrid.innerHTML = `
                <div class="error-message">
                    <p>Sorry, we konden de boten niet laden. Probeer het later opnieuw.</p>
                    <button class="btn" onclick="location.reload()">Opnieuw Proberen</button>
                </div>
            `;
        }
    }

    /**
     * Render boats in the grid
     */
    renderBoats(boats) {
        const boatsGrid = document.getElementById('boatsGrid');
        
        boatsGrid.innerHTML = boats.map(boat => `
            <div class="boat-card" data-boat-id="${boat.id}">
                <div class="boat-image">
                    <img src="${boat.image}" alt="${boat.name}" loading="lazy">
                    <div class="boat-status ${boat.available ? 'available' : 'occupied'}">
                        ${boat.available ? 'Available' : 'Occupied'}
                    </div>
                </div>
                <div class="boat-info">
                    <h3>${boat.name}</h3>
                    <p class="boat-capacity">${boat.capacity}</p>
                    <p class="boat-price">${utils.formatCurrency(boat.price)}</p>
                    <a href="/en/boat-hire/${boat.id}/" class="btn" ${!boat.available ? 'disabled' : ''}>
                        ${boat.available ? 'Book Now' : 'Not Available'}
                    </a>
                </div>
            </div>
        `).join('');
    }

    /**
     * Setup lazy loading for images
     */
    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Setup intersection observer for animations
     */
    setupIntersectionObserver() {
        if ('IntersectionObserver' in window) {
            const animationObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Observe elements for animation
            document.querySelectorAll('.boat-card, .service-card, .intro-content').forEach(el => {
                animationObserver.observe(el);
            });
        }
    }

    /**
     * Setup service worker
     */
    async setupServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                console.log('Service Worker registered successfully:', registration);
                
                // Handle updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.ui.showNotification('Nieuwe versie beschikbaar! Vernieuw om bij te werken.', 'info');
                        }
                    });
                });
            } catch (error) {
                console.error('Service Worker registration failed:', error);
            }
        }
    }

    /**
     * Setup analytics
     */
    setupAnalytics() {
        // Google Analytics 4
        if (typeof gtag !== 'undefined') {
            gtag('config', 'G-XXXXXXXXXX'); // Replace with your GA4 ID
        }

        // Custom analytics events
        this.trackEvents();
    }

    /**
     * Track custom events
     */
    trackEvents() {
        // Track form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.matches('#bookingForm')) {
                this.trackEvent('booking_form_submit', {
                    date: e.target.querySelector('#date').value,
                    boat_type: e.target.querySelector('#boatType').value
                });
            }
        });

        // Track boat clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.boat-card')) {
                const boatCard = e.target.closest('.boat-card');
                const boatId = boatCard.dataset.boatId;
                this.trackEvent('boat_click', { boat_id: boatId });
            }
        });

        // Track navigation
        document.addEventListener('click', (e) => {
            if (e.target.matches('nav a')) {
                this.trackEvent('navigation_click', {
                    link: e.target.href,
                    text: e.target.textContent
                });
            }
        });
    }

    /**
     * Track custom event
     */
    trackEvent(eventName, parameters = {}) {
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, parameters);
        }
        
        // Also send to custom analytics endpoint
        fetch('/api/analytics', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                event: eventName,
                parameters,
                timestamp: Date.now(),
                userAgent: navigator.userAgent,
                url: window.location.href
            })
        }).catch(error => {
            console.error('Analytics error:', error);
        });
    }

    /**
     * Setup performance monitoring
     */
    setupPerformanceMonitoring() {
        // Monitor Core Web Vitals
        if ('PerformanceObserver' in window) {
            // LCP (Largest Contentful Paint)
            const lcpObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const lastEntry = entries[entries.length - 1];
                this.trackEvent('lcp', { value: lastEntry.startTime });
            });
            lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] });

            // FID (First Input Delay)
            const fidObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach(entry => {
                    this.trackEvent('fid', { value: entry.processingStart - entry.startTime });
                });
            });
            fidObserver.observe({ entryTypes: ['first-input'] });

            // CLS (Cumulative Layout Shift)
            const clsObserver = new PerformanceObserver((list) => {
                let clsValue = 0;
                const entries = list.getEntries();
                entries.forEach(entry => {
                    if (!entry.hadRecentInput) {
                        clsValue += entry.value;
                    }
                });
                this.trackEvent('cls', { value: clsValue });
            });
            clsObserver.observe({ entryTypes: ['layout-shift'] });
        }

        // Monitor page load time
        window.addEventListener('load', () => {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            this.trackEvent('page_load_time', { value: loadTime });
        });
    }

    /**
     * Handle form submission
     */
    handleFormSubmit(e) {
        // Add form validation and submission handling here
        const form = e.target;
        const formData = new FormData(form);
        
        // Track form submission
        this.trackEvent('form_submit', {
            form_id: form.id,
            form_action: form.action
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize the application
    window.nijenhuisApp = new NijenhuisWebsite();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { NijenhuisWebsite, ApiService, UIComponents, utils };
} 