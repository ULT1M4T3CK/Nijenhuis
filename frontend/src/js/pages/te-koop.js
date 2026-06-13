/**
 * Te Koop Page - Modal functionality and grid rendering for items for sale
 * Nijenhuis Botenverhuur
 */

(function() {
    'use strict';

    const DEFAULT_IMAGE = '/frontend/Images/Boats/zeilboot/zeilboot-4-5.jpg';

    // Category labels for display
    const categoryLabels = {
        'chalet': 'Chalet',
        'stacaravan': 'Stacaravan',
        'boot': 'Boot'
    };

    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Format price in Dutch format
    function formatPrice(price) {
        return new Intl.NumberFormat('nl-NL').format(price);
    }

    // Slideshow state
    let currentSlideIndex = 0;
    let isTransitioning = false;
    let slideInterval = null;

    // Set main image in modal gallery (for backwards compatibility)
    function setMainImage(src) {
        const mainImage = document.getElementById('modalMainImage');
        if (mainImage) {
            mainImage.src = src;
        }
    }

    // Initialize slideshow functionality
    function initSlideshow() {
        const slideshowMain = document.querySelector('.modal-slideshow-main');
        if (!slideshowMain) return;

        const slides = slideshowMain.querySelectorAll('.slide');
        const thumbnails = document.querySelectorAll('.modal-slideshow-thumbnail');
        const totalSlides = slides.length;

        if (totalSlides === 0) return;

        // Reset state
        currentSlideIndex = 0;
        isTransitioning = false;

        // Initialize first slide
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            if (i === 0) slide.classList.add('active');
        });
        thumbnails.forEach((thumb, i) => {
            thumb.classList.remove('active');
            if (i === 0) thumb.classList.add('active');
        });

        // Clear any existing interval
        if (slideInterval) {
            clearInterval(slideInterval);
        }

        // Auto-advance slideshow every 5 seconds
        if (totalSlides > 1) {
            slideInterval = setInterval(() => {
                if (!isTransitioning) {
                    changeSlide(1);
                }
            }, 5000);
        }
    }

    // Show specific slide
    function showSlide(index) {
        if (isTransitioning) return;
        isTransitioning = true;

        const slides = document.querySelectorAll('.modal-slideshow-main .slide');
        const thumbnails = document.querySelectorAll('.modal-slideshow-thumbnail');
        const totalSlides = slides.length;

        if (totalSlides === 0) {
            isTransitioning = false;
            return;
        }

        // Ensure index is within bounds
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;

        // Remove active class from all slides and thumbnails
        slides.forEach(slide => slide.classList.remove('active'));
        thumbnails.forEach(thumb => thumb.classList.remove('active'));

        // Add active class to new slide and thumbnail
        if (slides[index]) {
            slides[index].classList.add('active');
            if (thumbnails[index]) thumbnails[index].classList.add('active');
            currentSlideIndex = index;
        }

        // Reset transition lock after animation completes
        setTimeout(() => {
            isTransitioning = false;
        }, 600);
    }

    // Change slide by direction
    function changeSlide(direction) {
        const slides = document.querySelectorAll('.modal-slideshow-main .slide');
        const totalSlides = slides.length;
        if (totalSlides === 0) return;

        currentSlideIndex += direction;
        if (currentSlideIndex >= totalSlides) currentSlideIndex = 0;
        else if (currentSlideIndex < 0) currentSlideIndex = totalSlides - 1;
        showSlide(currentSlideIndex);
    }

    // Go to specific slide
    function goToSlide(index) {
        showSlide(index);
    }

    // Open fullscreen slideshow
    function openFullscreen() {
        const slideshowMain = document.querySelector('.modal-slideshow-main');
        if (!slideshowMain) return;

        const slides = slideshowMain.querySelectorAll('.slide');
        const allImages = Array.from(slides).map(slide => ({
            src: slide.querySelector('img')?.src || slide.src,
            alt: slide.querySelector('img')?.alt || slide.alt || ''
        }));

        if (allImages.length === 0) return;

        // Create fullscreen overlay
        const fullscreenOverlay = document.createElement('div');
        fullscreenOverlay.className = 'fullscreen-slideshow-overlay';
        fullscreenOverlay.id = 'fullscreenSlideshow';
        
        let fullscreenHtml = '<button class="fullscreen-close" onclick="window.teKoopModal.closeFullscreen()" aria-label="Sluiten">&times;</button>';
        fullscreenHtml += '<button class="fullscreen-btn prev" onclick="window.teKoopModal.fullscreenChangeSlide(-1)" aria-label="Vorige">';
        fullscreenHtml += '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>';
        fullscreenHtml += '</button>';
        fullscreenHtml += '<button class="fullscreen-btn next" onclick="window.teKoopModal.fullscreenChangeSlide(1)" aria-label="Volgende">';
        fullscreenHtml += '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>';
        fullscreenHtml += '</button>';
        
        fullscreenHtml += '<div class="fullscreen-slideshow-main">';
        allImages.forEach((img, i) => {
            fullscreenHtml += `<img src="${escapeHtml(img.src)}" alt="${escapeHtml(img.alt)}" class="fullscreen-slide${i === currentSlideIndex ? ' active' : ''}" loading="lazy">`;
        });
        fullscreenHtml += '</div>';

        if (allImages.length > 1) {
            fullscreenHtml += '<div class="fullscreen-thumbnails">';
            allImages.forEach((img, i) => {
                fullscreenHtml += `<div class="fullscreen-thumbnail${i === currentSlideIndex ? ' active' : ''}" onclick="window.teKoopModal.fullscreenGoToSlide(${i})">`;
                fullscreenHtml += `<img src="${escapeHtml(img.src)}" alt="Thumbnail ${i + 1}">`;
                fullscreenHtml += '</div>';
            });
            fullscreenHtml += '</div>';
        }

        fullscreenOverlay.innerHTML = fullscreenHtml;
        document.body.appendChild(fullscreenOverlay);
        document.body.style.overflow = 'hidden';

        // Initialize fullscreen slideshow
        window.teKoopModal.fullscreenCurrentIndex = currentSlideIndex;

        // Add keyboard event listener for fullscreen
        const fullscreenKeyHandler = function(e) {
            if (e.key === 'Escape') {
                closeFullscreen();
                document.removeEventListener('keydown', fullscreenKeyHandler);
            } else if (e.key === 'ArrowLeft') {
                fullscreenChangeSlide(-1);
            } else if (e.key === 'ArrowRight') {
                fullscreenChangeSlide(1);
            }
        };
        document.addEventListener('keydown', fullscreenKeyHandler);
        
        // Store handler for cleanup
        fullscreenOverlay._keyHandler = fullscreenKeyHandler;
    }

    // Close fullscreen slideshow
    function closeFullscreen() {
        const fullscreenOverlay = document.getElementById('fullscreenSlideshow');
        if (fullscreenOverlay) {
            // Remove keyboard event listener if it exists
            if (fullscreenOverlay._keyHandler) {
                document.removeEventListener('keydown', fullscreenOverlay._keyHandler);
            }
            fullscreenOverlay.remove();
            document.body.style.overflow = '';
        }
    }

    // Change slide in fullscreen
    function fullscreenChangeSlide(direction) {
        const fullscreenMain = document.querySelector('.fullscreen-slideshow-main');
        if (!fullscreenMain) return;

        const slides = fullscreenMain.querySelectorAll('.fullscreen-slide');
        const thumbnails = document.querySelectorAll('.fullscreen-thumbnail');
        const totalSlides = slides.length;

        if (totalSlides === 0) return;

        window.teKoopModal.fullscreenCurrentIndex += direction;
        if (window.teKoopModal.fullscreenCurrentIndex >= totalSlides) {
            window.teKoopModal.fullscreenCurrentIndex = 0;
        } else if (window.teKoopModal.fullscreenCurrentIndex < 0) {
            window.teKoopModal.fullscreenCurrentIndex = totalSlides - 1;
        }

        // Update slides
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === window.teKoopModal.fullscreenCurrentIndex);
        });
        thumbnails.forEach((thumb, i) => {
            thumb.classList.toggle('active', i === window.teKoopModal.fullscreenCurrentIndex);
        });
    }

    // Go to specific slide in fullscreen
    function fullscreenGoToSlide(index) {
        const fullscreenMain = document.querySelector('.fullscreen-slideshow-main');
        if (!fullscreenMain) return;

        const slides = fullscreenMain.querySelectorAll('.fullscreen-slide');
        const thumbnails = document.querySelectorAll('.fullscreen-thumbnail');
        const totalSlides = slides.length;

        if (index < 0 || index >= totalSlides) return;

        window.teKoopModal.fullscreenCurrentIndex = index;

        // Update slides
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        thumbnails.forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
    }

    // Close the item details modal
    function closeItemDetails() {
        const modal = document.getElementById('itemDetailsModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
        // Clear slideshow interval
        if (slideInterval) {
            clearInterval(slideInterval);
            slideInterval = null;
        }
        // Close fullscreen if open
        closeFullscreen();
    }

    // Show item details in modal
    function showItemDetails(itemId) {
        console.log('Opening modal for item:', itemId);
        
        // Get items from global variable set by PHP
        const items = window.forSaleItems || [];
        const item = items.find(i => i.id === itemId);
        
        if (!item) {
            console.error('Item not found:', itemId);
            console.log('Available item IDs:', items.map(i => i.id));
            return;
        }

        // Get contact info from global config set by PHP
        const contactPhone = window.forSaleConfig?.phone || '0522 281 528';
        const contactEmail = window.forSaleConfig?.email || '';

        // Build features HTML
        let featuresHtml = '';
        if (item.features && item.features.length > 0) {
            featuresHtml = '<ul class="modal-features">' + 
                item.features.map(f => '<li>✓ ' + escapeHtml(f) + '</li>').join('') + 
                '</ul>';
        }

        // Build slideshow HTML
        const allImages = [item.mainImage, ...(item.additionalImages || [])].filter(Boolean);
        const hasMultipleImages = allImages.length > 1;
        
        let slideshowHtml = '<div class="modal-slideshow">';
        slideshowHtml += '<div class="modal-slideshow-main" onclick="if(event.target.classList.contains(\'slide\') || event.target.tagName === \'IMG\') window.teKoopModal.openFullscreen()" style="cursor: pointer;">';
        allImages.forEach((img, i) => {
            slideshowHtml += '<img src="' + escapeHtml(img) + '" alt="' + escapeHtml(item.name) + ' - Afbeelding ' + (i + 1) + '" class="slide' + (i === 0 ? ' active' : '') + '" onerror="this.style.display=\'none\'" loading="lazy">';
        });
        slideshowHtml += '</div>';

        // Add navigation buttons if multiple images
        if (hasMultipleImages) {
            slideshowHtml += '<button class="modal-slideshow-btn prev" onclick="event.stopPropagation(); window.teKoopModal.changeSlide(-1)" aria-label="Vorige">';
            slideshowHtml += '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>';
            slideshowHtml += '</button>';
            slideshowHtml += '<button class="modal-slideshow-btn next" onclick="event.stopPropagation(); window.teKoopModal.changeSlide(1)" aria-label="Volgende">';
            slideshowHtml += '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>';
            slideshowHtml += '</button>';
        }

        // Add fullscreen button
        slideshowHtml += '<button class="modal-fullscreen-btn" onclick="event.stopPropagation(); window.teKoopModal.openFullscreen()" aria-label="Volledig scherm" title="Volledig scherm">';
        slideshowHtml += '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>';
        slideshowHtml += '</button>';

        // Add thumbnails if multiple images
        if (hasMultipleImages) {
            slideshowHtml += '<div class="modal-slideshow-thumbnails">';
            allImages.forEach((img, i) => {
                slideshowHtml += '<div class="modal-slideshow-thumbnail' + (i === 0 ? ' active' : '') + '" onclick="window.teKoopModal.goToSlide(' + i + ')">';
                slideshowHtml += '<img src="' + escapeHtml(img) + '" alt="Thumbnail ' + (i + 1) + '" onerror="this.style.display=\'none\'">';
                slideshowHtml += '</div>';
            });
            slideshowHtml += '</div>';
        }
        slideshowHtml += '</div>';

        // Build modal content
        let content = '<div class="modal-header">';
        content += '<span class="modal-category">' + (categoryLabels[item.category] || item.category) + '</span>';
        content += '<h2 class="modal-title">' + escapeHtml(item.name) + '</h2>';
        content += '<div class="modal-price">€' + formatPrice(item.price) + '</div>';
        content += '</div>';

        content += '<div class="modal-body">';
        content += '<div class="modal-image-section">';
        content += slideshowHtml;
        content += '</div>';

        content += '<div class="modal-info-section">';

        if (item.year || item.size) {
            content += '<div class="modal-meta">';
            if (item.year) content += '<span class="meta-item">📅 <strong>Bouwjaar:</strong> ' + escapeHtml(item.year) + '</span>';
            if (item.size) content += '<span class="meta-item">📏 <strong>Afmetingen:</strong> ' + escapeHtml(item.size) + '</span>';
            content += '</div>';
        }

        content += '<div class="modal-description">';
        content += '<h4>Omschrijving</h4>';
        content += '<p>' + escapeHtml(item.description || '').replace(/\n/g, '<br>') + '</p>';
        content += '</div>';

        if (featuresHtml) {
            content += '<div class="modal-features-section">';
            content += '<h4>Kenmerken</h4>';
            content += featuresHtml;
            content += '</div>';
        }

        content += '<div class="modal-contact">';
        content += '<h4>Interesse?</h4>';
        content += '<p>Neem contact met ons op voor meer informatie of een bezichtiging:</p>';
        content += '<p><strong>📞 Telefoon:</strong> ' + escapeHtml(contactPhone) + '</p>';
        if (contactEmail) {
            content += '<p><strong>✉️ E-mail:</strong> ' + escapeHtml(contactEmail) + '</p>';
        }
        content += '</div>';

        content += '</div></div>';

        // Update modal and display
        const contentEl = document.getElementById('itemDetailsContent');
        const modal = document.getElementById('itemDetailsModal');
        
        if (contentEl && modal) {
            contentEl.innerHTML = content;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Initialize slideshow after content is inserted
            setTimeout(() => {
                initSlideshow();
            }, 50);
        }
    }

    // Initialize modal event listeners
    function initModal() {
        const modal = document.getElementById('itemDetailsModal');
        
        if (modal) {
            // Close modal on outside click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeItemDetails();
                }
            });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeItemDetails();
            }
        });
    }

    // Expose functions globally for onclick handlers
    window.teKoopModal = {
        show: showItemDetails,
        close: closeItemDetails,
        setMainImage: setMainImage,
        changeSlide: changeSlide,
        goToSlide: goToSlide,
        openFullscreen: openFullscreen,
        closeFullscreen: closeFullscreen,
        fullscreenChangeSlide: fullscreenChangeSlide,
        fullscreenGoToSlide: fullscreenGoToSlide,
        fullscreenCurrentIndex: 0
    };

    // Also expose as direct window functions for backwards compatibility
    window.showItemDetails = showItemDetails;
    window.closeItemDetails = closeItemDetails;
    window.setMainImage = setMainImage;

    /**
     * Render for-sale items in the grid (no inline JSON - fetches via ForSaleDataService)
     */
    async function renderForSaleItems(forceRefresh = false) {
        let items = [];
        if (window.ForSaleDataService) {
            items = await window.ForSaleDataService.getAllItems(forceRefresh);
            const featuredItems = await window.ForSaleDataService.getFeaturedItems();
            items.sort((a, b) => {
                const aFeatured = featuredItems.some(f => f.id === a.id);
                const bFeatured = featuredItems.some(f => f.id === b.id);
                if (aFeatured && !bFeatured) return -1;
                if (!aFeatured && bFeatured) return 1;
                return new Date(b.createdAt ?? 0) - new Date(a.createdAt ?? 0);
            });
        } else {
            try {
                const stored = localStorage.getItem('nijenhuis_for_sale');
                if (stored) items = JSON.parse(stored);
            } catch (e) {}
            items.sort((a, b) => {
                if ((a.featured ?? false) && !(b.featured ?? false)) return -1;
                if (!(a.featured ?? false) && (b.featured ?? false)) return 1;
                return new Date(b.createdAt ?? 0) - new Date(a.createdAt ?? 0);
            });
        }
        window.forSaleItems = items;

        const grid = document.querySelector('.for-sale-grid');
        const emptyState = document.querySelector('.no-boats-message');
        if (!grid) return;

        grid.innerHTML = '';
        if (items.length === 0) {
            if (emptyState) emptyState.style.display = 'block';
            return;
        }
        if (emptyState) emptyState.style.display = 'none';

        items.forEach(item => {
            const card = document.createElement('div');
            card.className = 'for-sale-card' + ((item.featured ?? false) ? ' featured' : '');
            const imgSrc = item.mainImage || DEFAULT_IMAGE;
            let html = '';
            if (item.featured ?? false) html += '<div class="featured-badge">★ Uitgelicht</div>';
            html += '<div class="for-sale-image-container">';
            html += '<img src="' + escapeHtml(imgSrc) + '" alt="' + escapeHtml(item.name || 'Item') + '" class="for-sale-image" onerror="this.src=\'' + DEFAULT_IMAGE + '\'">';
            html += '</div><div class="for-sale-content">';
            html += '<h3 class="for-sale-title">' + escapeHtml(item.name || 'Onbekend') + '</h3>';
            html += '<div class="for-sale-price">€' + formatPrice(item.price || 0) + '</div>';
            const desc = item.description || '';
            const firstLine = desc.split('\n')[0] || desc;
            const shortDesc = firstLine.length > 80 ? firstLine.substring(0, 80) + '...' : firstLine;
            html += '<p class="for-sale-description-short">' + escapeHtml(shortDesc) + '</p>';
            html += '<button class="for-sale-contact-btn" onclick="showItemDetails(\'' + escapeHtml(item.id || '') + '\')">Meer informatie</button>';
            html += '</div>';
            card.innerHTML = html;
            grid.appendChild(card);
        });
    }

    // Initialize when DOM is ready
    function init() {
        initModal();
        renderForSaleItems();
        if (window.ForSaleDataService) {
            window.ForSaleDataService.subscribe(() => renderForSaleItems(true));
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

