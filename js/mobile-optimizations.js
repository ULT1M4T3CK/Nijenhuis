// Mobile Optimizations for Nijenhuis Website
// Enhanced mobile experience, performance, and touch interactions

(function() {
    'use strict';

    // Mobile Device Detection
    const isMobile = () => {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
               window.innerWidth <= 768;
    };

    // Touch Optimization
    function optimizeTouchInteractions() {
        // Improve touch targets
        const buttons = document.querySelectorAll('button, .btn, a[href]');
        buttons.forEach(button => {
            const rect = button.getBoundingClientRect();
            if (rect.width < 44 || rect.height < 44) {
                button.style.minWidth = '44px';
                button.style.minHeight = '44px';
            }
        });

        // Add touch feedback
        document.addEventListener('touchstart', function(e) {
            if (e.target.matches('.btn, button, .service-card, .boat-card')) {
                e.target.style.transform = 'scale(0.98)';
            }
        });

        document.addEventListener('touchend', function(e) {
            if (e.target.matches('.btn, button, .service-card, .boat-card')) {
                setTimeout(() => {
                    e.target.style.transform = '';
                }, 150);
            }
        });
    }

    // Lazy Loading Enhancement
    function enhancedLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            observer.unobserve(img);
                        }
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    // Viewport Height Fix for Mobile Browsers
    function fixMobileViewportHeight() {
        const setVH = () => {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        };

        setVH();
        window.addEventListener('resize', setVH);
        window.addEventListener('orientationchange', () => {
            setTimeout(setVH, 100);
        });
    }

    // Enhanced Form Experience
    function enhanceMobileForms() {
        const inputs = document.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            // Prevent zoom on iOS
            if (input.type === 'text' || input.type === 'email' || input.type === 'tel') {
                input.style.fontSize = '16px';
            }

            // Add better mobile keyboard types
            if (input.type === 'tel' || input.name.includes('phone')) {
                input.setAttribute('inputmode', 'tel');
            }
            if (input.type === 'email' || input.name.includes('email')) {
                input.setAttribute('inputmode', 'email');
            }
        });

        // Enhanced date picker for mobile
        const dateInputs = document.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            if (isMobile()) {
                input.style.appearance = 'none';
                input.style.webkitAppearance = 'none';
            }
        });
    }

    // Performance Optimizations
    function optimizePerformance() {
        // Debounced scroll handler
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            if (scrollTimeout) clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                // Scroll-dependent operations
                const scrolled = window.pageYOffset;
                const navbar = document.querySelector('.main-nav');
                if (navbar) {
                    if (scrolled > 100) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                }
            }, 10);
        }, { passive: true });

        // Optimize images for mobile
        if (isMobile()) {
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                // Add loading="lazy" if not already present
                if (!img.hasAttribute('loading')) {
                    img.setAttribute('loading', 'lazy');
                }
                
                // Add decoding="async" for better performance
                img.setAttribute('decoding', 'async');
            });
        }
    }

    // Enhanced Chat Widget for Mobile
    function optimizeChatWidget() {
        const chatWidget = document.querySelector('.chat-widget');
        const chatWindow = document.querySelector('.chat-window');
        
        if (chatWidget && isMobile()) {
            // Make chat widget more accessible on mobile
            chatWidget.style.bottom = '20px';
            chatWidget.style.right = '20px';
            
            if (chatWindow) {
                chatWindow.style.width = 'calc(100vw - 40px)';
                chatWindow.style.height = '70vh';
                chatWindow.style.maxHeight = '500px';
                chatWindow.style.right = '20px';
                chatWindow.style.left = '20px';
            }
        }
    }

    // Swipe Gestures for Cards
    function addSwipeGestures() {
        if (!isMobile()) return;

        const cardContainers = document.querySelectorAll('.boats-grid, .services-grid');
        
        cardContainers.forEach(container => {
            let startX, startY, distX, distY;
            
            container.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
            }, { passive: true });
            
            container.addEventListener('touchmove', (e) => {
                if (!startX || !startY) return;
                
                distX = e.touches[0].clientX - startX;
                distY = e.touches[0].clientY - startY;
                
                // Horizontal swipe detection
                if (Math.abs(distX) > Math.abs(distY) && Math.abs(distX) > 50) {
                    e.preventDefault();
                }
            });
            
            container.addEventListener('touchend', () => {
                startX = null;
                startY = null;
                distX = 0;
                distY = 0;
            }, { passive: true });
        });
    }

    // Initialize all optimizations
    function init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }

        console.log('Initializing mobile optimizations...');

        optimizeTouchInteractions();
        enhancedLazyLoading();
        fixMobileViewportHeight();
        enhanceMobileForms();
        optimizePerformance();
        optimizeChatWidget();
        addSwipeGestures();

        // Add mobile-specific CSS class
        if (isMobile()) {
            document.body.classList.add('is-mobile');
        }

        console.log('Mobile optimizations initialized');
    }

    // Initialize
    init();

})();