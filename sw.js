/**
 * Service Worker for Nijenhuis Boat Rental Website
 * Provides offline functionality, caching, and performance improvements
 */

const CACHE_NAME = 'nijenhuis-cache-v2';
const STATIC_CACHE = 'nijenhuis-static-v2';
const DYNAMIC_CACHE = 'nijenhuis-dynamic-v2';

// Files to cache immediately
const STATIC_FILES = [
    '/',
    '/index.html',
    '/styles.css',
    '/script.js',
    '/Images/logo-white.svg',
    '/Images/banner-img.jpg',
    '/flags/nl.svg',
    '/flags/gb.svg',
    '/flags/de.svg',
    'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap'
];

// API endpoints to cache
const API_CACHE = [
    '/api/boats',
    '/api/availability'
];

// Install event - cache static files
self.addEventListener('install', (event) => {
    // Production-ready service worker - logging removed
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => {
                return cache.addAll(STATIC_FILES);
            })
            .then(() => {
                return self.skipWaiting();
            })
            .catch((error) => {
                // Only log errors in production
                console.error('Error caching static files:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    // Production-ready service worker - logging removed
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                return self.clients.claim();
            })
    );
});

// Fetch event - handle requests
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Handle different types of requests
    if (url.pathname === '/' || url.pathname.endsWith('.html')) {
        // HTML pages - network first, fallback to cache
        event.respondWith(handleHTMLRequest(request));
    } else if (url.pathname.endsWith('.css') || url.pathname.endsWith('.js')) {
        // Static assets - cache first, fallback to network
        event.respondWith(handleStaticRequest(request));
    } else if (url.pathname.startsWith('/images/')) {
        // Images - cache first, fallback to network
        event.respondWith(handleImageRequest(request));
    } else if (url.pathname.startsWith('/api/')) {
        // API requests - network first, fallback to cache
        event.respondWith(handleAPIRequest(request));
    } else if (url.origin === 'https://fonts.googleapis.com') {
        // Google Fonts - cache first
        event.respondWith(handleFontRequest(request));
    } else {
        // Default - network first
        event.respondWith(handleDefaultRequest(request));
    }
});

/**
 * Handle HTML requests - network first, fallback to cache
 */
async function handleHTMLRequest(request) {
    try {
        // Try network first
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Cache the response
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
            return networkResponse;
        }
    } catch (error) {
                    // Network failed, fallback to cache
    }

    // Fallback to cache
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }

    // Fallback to offline page
    return caches.match('/offline.html');
}

/**
 * Handle static asset requests - cache first, fallback to network
 */
async function handleStaticRequest(request) {
    // Check cache first
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }

    try {
        // Try network
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Cache the response
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, networkResponse.clone());
            return networkResponse;
        }
            } catch (error) {
            // Network failed, fallback to cache
        }

    // Return a default response for CSS/JS
    if (request.url.endsWith('.css')) {
        return new Response('/* Offline */', {
            headers: { 'Content-Type': 'text/css' }
        });
    }
    
    if (request.url.endsWith('.js')) {
        return new Response('// Offline', {
            headers: { 'Content-Type': 'application/javascript' }
        });
    }
}

/**
 * Handle image requests - cache first, fallback to network
 */
async function handleImageRequest(request) {
    // Check cache first
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }

    try {
        // Try network
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Cache the response
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
            return networkResponse;
        }
            } catch (error) {
            // Network failed, fallback to cache
        }

    // Return a placeholder image
    return caches.match('/images/placeholder.jpg');
}

/**
 * Handle API requests - network first, fallback to cache
 */
async function handleAPIRequest(request) {
    try {
        // Try network first
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Cache the response with expiration
            const cache = await caches.open(DYNAMIC_CACHE);
            const responseToCache = networkResponse.clone();
            
            // Add cache headers
            const headers = new Headers(responseToCache.headers);
            headers.set('sw-cached', Date.now().toString());
            
            const cachedResponse = new Response(await responseToCache.arrayBuffer(), {
                status: responseToCache.status,
                statusText: responseToCache.statusText,
                headers: headers
            });
            
            cache.put(request, cachedResponse);
            return networkResponse;
        }
            } catch (error) {
            // Network failed, fallback to cache
        }

    // Fallback to cache
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        // Check if cache is still valid (less than 1 hour old)
        const cacheTime = parseInt(cachedResponse.headers.get('sw-cached') || '0');
        const now = Date.now();
        
        if (now - cacheTime < 60 * 60 * 1000) { // 1 hour
            return cachedResponse;
        }
    }

    // Return error response
    return new Response(JSON.stringify({
        error: 'Service unavailable',
        message: 'Please check your connection and try again'
    }), {
        status: 503,
        headers: { 'Content-Type': 'application/json' }
    });
}

/**
 * Handle font requests - cache first
 */
async function handleFontRequest(request) {
    // Check cache first
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }

    try {
        // Try network
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Cache the response
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, networkResponse.clone());
            return networkResponse;
        }
            } catch (error) {
            // Network failed, fallback to cache
        }

    // Return empty response for fonts
    return new Response('', {
        headers: { 'Content-Type': 'text/css' }
    });
}

/**
 * Handle default requests - network first
 */
async function handleDefaultRequest(request) {
    try {
        return await fetch(request);
    } catch (error) {
        // Network failed for default request
        
        // Try cache as fallback
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return error response
        return new Response('Network error', { status: 503 });
    }
}

/**
 * Background sync for offline actions
 */
self.addEventListener('sync', (event) => {
    // Background sync triggered
    
    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

/**
 * Perform background sync
 */
async function doBackgroundSync() {
    try {
        // Get pending actions from IndexedDB
        const pendingActions = await getPendingActions();
        
        for (const action of pendingActions) {
            try {
                // Retry the action
                await retryAction(action);
                
                // Remove from pending actions
                await removePendingAction(action.id);
            } catch (error) {
                console.error('Background sync failed for action:', action, error);
            }
        }
    } catch (error) {
        console.error('Background sync error:', error);
    }
}

/**
 * Push notification handling
 */
self.addEventListener('push', (event) => {
    // Push notification received
    
    const options = {
        body: event.data ? event.data.text() : 'New notification from Nijenhuis',
        icon: '/Images/logo-white.svg',
        badge: '/Images/logo-white.svg',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'View Details',
                icon: '/Images/logo-white.svg'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/Images/logo-white.svg'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('Nijenhuis Boat Rental', options)
    );
});

/**
 * Notification click handling
 */
self.addEventListener('notificationclick', (event) => {
    // Notification clicked
    
    event.notification.close();

    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/en/')
        );
    }
});

/**
 * Message handling from main thread
 */
self.addEventListener('message', (event) => {
    // Message received in service worker
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'CACHE_URLS') {
        event.waitUntil(
            caches.open(DYNAMIC_CACHE)
                .then((cache) => {
                    return cache.addAll(event.data.urls);
                })
        );
    }
});

/**
 * Get pending actions from IndexedDB
 */
async function getPendingActions() {
    // This would typically use IndexedDB
    // For now, return empty array
    return [];
}

/**
 * Retry a failed action
 */
async function retryAction(action) {
    // Implement retry logic based on action type
    switch (action.type) {
        case 'booking':
            return await retryBooking(action.data);
        case 'availability_check':
            return await retryAvailabilityCheck(action.data);
        default:
            throw new Error(`Unknown action type: ${action.type}`);
    }
}

/**
 * Retry booking action
 */
async function retryBooking(bookingData) {
    const response = await fetch('/api/bookings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(bookingData)
    });
    
    if (!response.ok) {
        throw new Error('Booking retry failed');
    }
    
    return response.json();
}

/**
 * Retry availability check
 */
async function retryAvailabilityCheck(checkData) {
    const response = await fetch(`/api/availability?${new URLSearchParams(checkData)}`);
    
    if (!response.ok) {
        throw new Error('Availability check retry failed');
    }
    
    return response.json();
}

/**
 * Remove pending action from IndexedDB
 */
async function removePendingAction(actionId) {
    // This would typically use IndexedDB
            // Removing pending action
}

/**
 * Cache management utilities
 */
const cacheManager = {
    /**
     * Clear all caches
     */
    async clearAll() {
        const cacheNames = await caches.keys();
        return Promise.all(
            cacheNames.map(cacheName => caches.delete(cacheName))
        );
    },

    /**
     * Get cache size
     */
    async getCacheSize() {
        const cacheNames = await caches.keys();
        let totalSize = 0;
        
        for (const cacheName of cacheNames) {
            const cache = await caches.open(cacheName);
            const keys = await cache.keys();
            
            for (const request of keys) {
                const response = await cache.match(request);
                if (response) {
                    const blob = await response.blob();
                    totalSize += blob.size;
                }
            }
        }
        
        return totalSize;
    },

    /**
     * Clean old cache entries
     */
    async cleanOldEntries() {
        const cache = await caches.open(DYNAMIC_CACHE);
        const keys = await cache.keys();
        
        for (const request of keys) {
            const response = await cache.match(request);
            if (response) {
                const cacheTime = parseInt(response.headers.get('sw-cached') || '0');
                const now = Date.now();
                
                // Remove entries older than 24 hours
                if (now - cacheTime > 24 * 60 * 60 * 1000) {
                    await cache.delete(request);
                }
            }
        }
    }
};

// Periodic cache cleanup
setInterval(() => {
    cacheManager.cleanOldEntries();
}, 60 * 60 * 1000); // Every hour 