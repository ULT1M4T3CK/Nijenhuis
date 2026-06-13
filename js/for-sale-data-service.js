/**
 * ========================================================================
 * FOR-SALE DATA SERVICE - SINGLE SOURCE OF TRUTH
 * ========================================================================
 * 
 * This service provides centralized access to for-sale items data across the entire website.
 * The authoritative source is the admin/for-sale.json file (via server) or localStorage.
 * 
 * All for-sale data changes made in for-sale-management.php automatically propagate to:
 * - The te-koop.php page
 * - Any other pages that display for-sale items
 * 
 * USAGE (from other pages):
 *   const items = await ForSaleDataService.getAllItems();
 *   const item = await ForSaleDataService.getItemById('item_123');
 *   ForSaleDataService.subscribe((items) => { console.log('Updated:', items); });
 * 
 * ========================================================================
 */
class ForSaleDataServiceClass {
    constructor() {
        this.storageKey = 'nijenhuis_for_sale';
        this.cache = null;
        this.cacheTime = 0;
        this.cacheDuration = 100; // 100ms cache for near-instant updates
        this.subscribers = [];
        this.initialized = false;
    }

    init() {
        if (this.initialized) return;
        this.initialized = true;

        // Listen for storage changes from other tabs
        window.addEventListener('storage', (e) => {
            if (e.key === this.storageKey) {
                this.cache = null;
                this.notifySubscribers();
            }
        });

        // Listen for custom events from for-sale-management.php
        window.addEventListener('forSaleItemsUpdated', () => {
            this.cache = null;
            this.notifySubscribers();
        });

        window.addEventListener('forSaleItemsStorageUpdated', () => {
            this.cache = null;
            this.notifySubscribers();
        });
    }

    /**
     * Detect the correct server endpoint based on the current environment
     */
    detectServerEndpoint() {
        // Only use Python backend when opened directly as file://
        // When served via any web server (including localhost), use PHP
        if (window.location.protocol === 'file:' || window.location.hostname === '') {
            return 'http://localhost:8000/admin/booking-handler.py';
        }
        return `${window.location.origin}/admin/booking-handler.php`;
    }

    /**
     * Load items from server (authoritative) with localStorage metadata as offline fallback.
     * Image data is never written to localStorage to prevent QuotaExceededError.
     */
    async loadItems(forceRefresh = false) {
        const now = Date.now();
        
        // Return cached data if still valid
        if (!forceRefresh && this.cache && (now - this.cacheTime) < this.cacheDuration) {
            return this.cache;
        }

        // Try server first (authoritative source)
        try {
            const serverItems = await this.fetchItemsFromServer();
            if (serverItems && Array.isArray(serverItems)) {
                this.cache = serverItems;
                this.cacheTime = now;
                return serverItems;
            }
        } catch (e) {
            console.warn('Server fetch failed, falling back to localStorage:', e.message);
        }

        // Fallback to localStorage (metadata-only, no image data)
        let items = [];
        try {
            const stored = localStorage.getItem(this.storageKey);
            if (stored) {
                items = JSON.parse(stored);
            }
        } catch (e) {
            console.error('Error reading localStorage fallback:', e);
        }

        this.cache = items;
        this.cacheTime = now;
        return items;
    }

    /**
     * Fetch items from the server
     */
    async fetchItemsFromServer() {
        const endpoint = this.detectServerEndpoint();

        try {
            // Only treat as truly "local" (no credentials) for file:// protocol
            const isFileProtocol = window.location.protocol === 'file:' || window.location.hostname === '';

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 3000); // 3 second timeout

            const res = await fetch(`${endpoint}?action=getForSaleItems`, {
                credentials: isFileProtocol ? 'omit' : 'include',
                mode: 'cors',
                headers: { 'Accept': 'application/json' },
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);

            if (res.ok) {
                // Check if response has content before parsing
                const text = await res.text();
                if (!text || text.trim() === '') {
                    return [];
                }
                
                try {
                    const data = JSON.parse(text);
                    if (data && data.success && Array.isArray(data.items)) {
                        return data.items;
                    }
                } catch (parseError) {
                    console.warn('Failed to parse for-sale items response:', parseError);
                    return [];
                }
            }
        } catch (e) {
            if (e.name !== 'AbortError') {
                console.warn('Server fetch failed:', e.message);
            }
        }
        return [];
    }

    /**
     * Get all items
     */
    async getAllItems(forceRefresh = false) {
        this.init();
        return await this.loadItems(forceRefresh);
    }

    /**
     * Get a specific item by ID
     */
    async getItemById(itemId) {
        const items = await this.loadItems();
        return items.find(i => i.id === itemId) || null;
    }

    /**
     * Get items by category
     */
    async getItemsByCategory(category) {
        const items = await this.loadItems();
        return items.filter(i => i.category === category);
    }

    /**
     * Get featured items
     */
    async getFeaturedItems() {
        const items = await this.loadItems();
        return items.filter(i => i.featured === true);
    }

    /**
     * Subscribe to item data changes
     */
    subscribe(callback) {
        if (typeof callback !== 'function') {
            throw new Error('Callback must be a function');
        }
        
        this.init();
        this.subscribers.push(callback);
        
        // Immediately call with current data
        this.loadItems().then(items => callback(items));
        
        // Return unsubscribe function
        return () => {
            const index = this.subscribers.indexOf(callback);
            if (index > -1) this.subscribers.splice(index, 1);
        };
    }

    /**
     * Notify all subscribers of data changes
     */
    async notifySubscribers() {
        // Force refresh and bypass cache for instant updates
        this.cache = null;
        this.cacheTime = 0;
        const items = await this.loadItems(true);
        this.subscribers.forEach(callback => {
            try {
                callback(items);
            } catch (e) {
                console.error('Error in subscriber:', e);
            }
        });
    }
}

// Create global singleton instance
window.ForSaleDataService = new ForSaleDataServiceClass();

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.ForSaleDataService.init();
    });
} else {
    window.ForSaleDataService.init();
}

// Export for module usage if supported
if (typeof module !== 'undefined' && module.exports) {
    module.exports = window.ForSaleDataService;
}

