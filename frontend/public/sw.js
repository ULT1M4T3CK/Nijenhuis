/**
 * Service Worker for Nijenhuis Boat Rental Website
 * Provides offline functionality, caching, and performance improvements
 */

// Cache version - INCREMENT THIS when deploying new content to force cache invalidation
// v6: Fix offline fallback to use /offline (offline.html did not exist)
const CACHE_VERSION = 'v7';
const CACHE_NAME = `nijenhuis-cache-${CACHE_VERSION}`;
const STATIC_CACHE = `nijenhuis-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE = `nijenhuis-dynamic-${CACHE_VERSION}`;

// Files to cache immediately (use real paths - site uses PHP, not index.html)
const STATIC_FILES = [
    '/',
    '/offline',
    '/frontend/Images/logo-white.svg',
    '/frontend/Images/banner-img.jpg',
    '/frontend/public/flags/nl.svg',
    '/frontend/public/flags/gb.svg',
    '/frontend/public/flags/de.svg',
    'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap'
];

// API endpoints to cache
const API_CACHE = [
    '/api/boats',
    '/api/availability'
];

// Install event - cache static files
self.addEventListener('install', (event) => {
    console.log('Service Worker installing...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => {
                console.log('Caching static files');
                return cache.addAll(STATIC_FILES);
            })
            .then(() => {
                console.log('Static files cached successfully');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('Error caching static files:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker activating...');
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        // Delete all caches that don't match current version
                        if (!cacheName.includes(CACHE_VERSION)) {
                            console.log('Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker activated, cache version:', CACHE_VERSION);
                // Force immediate activation and claim clients
                return self.clients.claim();
            })
    );
});

// Paths that may contain authenticated/session-sensitive data. These must
// NEVER be intercepted by the service worker, or a cached response could
// leak between users / sessions.
const SENSITIVE_PATH_PREFIXES = [
    '/admin',
    '/booking-management',
    '/blog-portal',
    '/mollie_api.php',
    '/webhook/',
    '/webhooks/',
    '/pages/payment-success',
    '/pages/payment-failure',
    '/pages/admin-login',
    '/pages/employee',
    '/api/security',
    '/api/token',
    '/api/chat',
];

function isSensitivePath(pathname) {
    return SENSITIVE_PATH_PREFIXES.some(prefix => pathname.startsWith(prefix));
}

// Fetch event - handle requests
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET and cross-origin requests we don't explicitly handle.
    if (request.method !== 'GET') {
        return;
    }

    // Never cache or intercept authenticated / payment / admin routes.
    // This prevents cross-session data leakage and stale booking state.
    if (isSensitivePath(url.pathname) || request.headers.get('Authorization')
        || request.headers.has('Cookie') && isSensitivePath(url.pathname)) {
        event.respondWith(fetch(request, { cache: 'no-store' }));
        return;
    }

    // Requests with query strings that identify a specific user or booking
    // (bookingId, cartId, payment_id, token) must bypass the cache too.
    if (url.searchParams.has('bookingId')
        || url.searchParams.has('cartId')
        || url.searchParams.has('payment_id')
        || url.searchParams.has('token')) {
        event.respondWith(fetch(request, { cache: 'no-store' }));
        return;
    }

    // Handle different types of requests
    if (url.pathname === '/' || url.pathname.endsWith('.html') || url.pathname.endsWith('.php')) {
        // HTML/PHP pages - network first, fallback to cache (always get fresh content)
        event.respondWith(handleHTMLRequest(request));
    } else if (url.pathname.endsWith('.css') || url.pathname.endsWith('.js')) {
        // Static assets - network first with cache fallback (ensures fresh content on updates)
        event.respondWith(handleStaticRequest(request));
    } else if (url.pathname.startsWith('/images/')) {
        // Images - cache first, fallback to network
        event.respondWith(handleImageRequest(request));
    } else if (url.pathname.startsWith('/api/')) {
        // Only cache explicitly allowed public API endpoints
        const publicApiAllowlist = ['/api/boats', '/api/availability'];
        if (publicApiAllowlist.includes(url.pathname)) {
            event.respondWith(handleAPIRequest(request));
        } else {
            // Do not cache sensitive endpoints
            event.respondWith(fetch(request, { cache: 'no-store' }));
        }
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
 * Always tries to get fresh content from network
 */
async function handleHTMLRequest(request) {
    try {
        // Try network first with no-cache to ensure fresh content
        const networkResponse = await fetch(request, {
            cache: 'no-cache' // Force revalidation
        });
        
        if (networkResponse.ok) {
            // Cache the fresh response for offline use
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
            return networkResponse;
        }
    } catch (error) {
        console.log('Network failed for HTML, trying cache:', error);
    }

    // Fallback to cache only if network fails
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }

    // Fallback to offline page (/offline -> pages/offline.php)
    return caches.match('/offline') || caches.match('/');
}

/**
 * Handle static asset requests - network first, fallback to cache
 * This ensures users always get the latest CSS/JS files when available
 */
async function handleStaticRequest(request) {
    try {
        // Try network first to get fresh content
        const networkResponse = await fetch(request, {
            cache: 'no-cache' // Force revalidation
        });
        
        if (networkResponse.ok) {
            // Cache the fresh response
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, networkResponse.clone());
            return networkResponse;
        }
    } catch (error) {
        console.log('Network failed for static asset, trying cache:', error);
    }

    // Fallback to cache if network fails
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }

    // Return a default response for CSS/JS if both network and cache fail
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
        console.log('Network failed for image:', error);
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
        console.log('Network failed for API, trying cache:', error);
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
        console.log('Network failed for font:', error);
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
        console.log('Network failed for default request:', error);
        
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
    console.log('Background sync triggered:', event.tag);
    
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
    console.log('Push notification received:', event);
    
    const options = {
        body: event.data ? event.data.text() : 'New notification from Nijenhuis',
        icon: '/frontend/Images/logo-white.svg',
        badge: '/frontend/Images/logo-white.svg',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'View Details',
                icon: '/frontend/Images/logo-white.svg'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/frontend/Images/logo-white.svg'
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
    console.log('Notification clicked:', event);
    
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
    console.log('Message received in service worker:', event.data);
    
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
    console.log('Removing pending action:', actionId);
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