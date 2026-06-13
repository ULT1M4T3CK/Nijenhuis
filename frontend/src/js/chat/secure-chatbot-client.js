
/**
 * Secure Chatbot Client for Nijenhuis Website
 * Handles authentication, connection monitoring, and secure communication
 */

class SecureChatbotClient {
    constructor(config = {}) {
        // Token-based auth (no public API keys)
        this.token = null;
        
        // Get API endpoints from multiple sources
        let apiEndpoint = config.apiEndpoint;
        if (!apiEndpoint && typeof window !== 'undefined' && window.CHATBOT_API_ENDPOINT) {
            apiEndpoint = window.CHATBOT_API_ENDPOINT;
        }
        // Check Vite environment variable (for Vite builds)
        // Note: import.meta is only available in ES modules, not in regular script tags
        // For Vite builds, use window.VITE_CHATBOT_API_ENDPOINT instead
        if (!apiEndpoint && typeof window !== 'undefined' && window.VITE_CHATBOT_API_ENDPOINT) {
            apiEndpoint = window.VITE_CHATBOT_API_ENDPOINT;
        }
        if (!apiEndpoint) {
            apiEndpoint = 'http://localhost:5001/api/chat';
        }
        
        let healthEndpoint = config.healthEndpoint;
        if (!healthEndpoint && typeof window !== 'undefined' && window.CHATBOT_HEALTH_ENDPOINT) {
            healthEndpoint = window.CHATBOT_HEALTH_ENDPOINT;
        }
        // Check Vite environment variable (for Vite builds)
        // Note: import.meta is only available in ES modules, not in regular script tags
        // For Vite builds, use window.VITE_CHATBOT_HEALTH_ENDPOINT instead
        if (!healthEndpoint && typeof window !== 'undefined' && window.VITE_CHATBOT_HEALTH_ENDPOINT) {
            healthEndpoint = window.VITE_CHATBOT_HEALTH_ENDPOINT;
        }
        if (!healthEndpoint) {
            healthEndpoint = 'http://localhost:5001/api/health';
        }
        
        // Auto-detect development mode (localhost) and enable silent health checks
        const isLocalhost = healthEndpoint && (
            healthEndpoint.includes('localhost') || 
            healthEndpoint.includes('127.0.0.1') ||
            healthEndpoint.startsWith('http://localhost') ||
            healthEndpoint.startsWith('http://127.0.0.1')
        );
        
        this.config = {
            apiEndpoint: apiEndpoint,
            healthEndpoint: healthEndpoint,
            reconnectAttempts: config.reconnectAttempts || 3,
            reconnectDelay: config.reconnectDelay || 2000,
            requestTimeout: config.requestTimeout || 30000,  // 30 seconds for ML model responses
            enableHealthChecks: config.enableHealthChecks !== false, // Default to true
            // Auto-enable silent health checks for localhost (development mode)
            silentHealthChecks: config.silentHealthChecks !== undefined 
                ? config.silentHealthChecks 
                : isLocalhost, // Default to true for localhost, false for production
            ...config
        };
        
        // Connection state
        // Start optimistic: if browser reports online, assume connected until proven otherwise
        this.isConnected = navigator.onLine !== false; // Default to true if online, false only if explicitly offline
        this.connectionStatus = navigator.onLine ? 'healthy' : 'unknown';
        this.lastHealthCheck = null;
        this.reconnectAttempts = 0;
        this.healthCheckInterval = null;
        
        // Request queue for offline mode
        this.requestQueue = [];
        this.isProcessingQueue = false;
        
        // Event listeners
        this.eventListeners = {
            connectionChange: [],
            error: [],
            message: []
        };
        
        // Initialize
        this.init();
    }
    
    init() {
        // Acquire token early (non-blocking)
        this.refreshToken().catch(() => {
            // Token retrieval can fail if server is down; client will retry on first request
        });
        this.startHealthMonitoring();
        this.setupErrorHandling();
    }
    
    async refreshToken() {
        const tokenEndpoint = this.config.apiEndpoint.replace('/api/chat', '/api/token');
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.config.requestTimeout);
        try {
            const resp = await fetch(tokenEndpoint, {
                method: 'GET',
                signal: controller.signal,
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            clearTimeout(timeoutId);
            if (!resp.ok) {
                throw new Error(`Token request failed: ${resp.status}`);
            }
            const data = await resp.json();
            if (data && data.token) {
                this.token = data.token;
                return true;
        }
            throw new Error('Token missing in response');
        } catch (e) {
            clearTimeout(timeoutId);
            console.warn('Failed to refresh token:', e);
            this.token = null;
            return false;
        }
    }
    
    startHealthMonitoring() {
        // Skip health monitoring if disabled
        if (!this.config.enableHealthChecks) {
            return;
        }
        
        // Check health every 30 seconds
        this.healthCheckInterval = setInterval(() => {
            this.checkHealth();
        }, 30000);
        
        // Initial health check
        this.checkHealth();
    }
    
    stopHealthMonitoring() {
        if (this.healthCheckInterval) {
            clearInterval(this.healthCheckInterval);
            this.healthCheckInterval = null;
        }
    }
    
    async checkHealth() {
        // If browser reports offline, verify actual internet connection first
        if (!navigator.onLine) {
            await this.verifyInternetConnection();
            // If still no internet after verification, don't proceed with health check
            if (!navigator.onLine) {
                return null;
            }
        }
        
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.config.requestTimeout);
        
        try {
            const response = await fetch(this.config.healthEndpoint, {
                method: 'GET',
                signal: controller.signal,
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            clearTimeout(timeoutId);
            
            if (response.ok) {
                const data = await response.json();
                this.lastHealthCheck = new Date();
                
                const wasConnected = this.isConnected;
                this.isConnected = data.status === 'healthy' || data.status === 'degraded';
                this.connectionStatus = data.status;
                
                // Reset reconnect attempts on successful health check
                if (this.isConnected) {
                    this.reconnectAttempts = 0;
                }
                
                if (wasConnected !== this.isConnected) {
                    this.emit('connectionChange', {
                        status: this.connectionStatus,
                        isConnected: this.isConnected,
                        data: data
                    });
                }
                
                // Process queued requests if we're back online
                if (this.isConnected && this.requestQueue.length > 0) {
                    this.processRequestQueue();
                }
                
                return data;
            } else {
                throw new Error(`Health check failed: ${response.status}`);
            }
        } catch (error) {
            clearTimeout(timeoutId);
            
            // Suppress console warnings for connection refused errors (expected when server is down)
            // Fetch throws TypeError with "Failed to fetch" for network errors like ERR_CONNECTION_REFUSED
            const isConnectionRefused = (
                error.name === 'TypeError' && 
                (error.message && error.message.includes('Failed to fetch'))
            ) || (
                error.message && (
                    error.message.includes('ERR_CONNECTION_REFUSED') ||
                    error.message.includes('NetworkError') ||
                    error.message.includes('Network request failed')
                )
            );
            
            // Only log errors that aren't expected connection failures
            if (error.name === 'AbortError') {
                // Timeout errors are less common, so log them if not in silent mode
                if (!this.config.silentHealthChecks) {
                    console.warn('Health check timeout:', error);
                }
            } else if (!isConnectionRefused && !this.config.silentHealthChecks) {
                // Only log unexpected errors (not connection refused)
                console.warn('Health check failed:', error);
            }
            // Connection refused errors are silently handled - server is just offline
            
            // Only handle as connection error if we have internet
            // If no internet, verifyInternetConnection will handle it
            if (navigator.onLine) {
                this.handleConnectionError();
            } else {
                await this.verifyInternetConnection();
            }
            return null;
        }
    }
    
    handleConnectionError() {
        // Only mark as offline if we truly have no internet connection
        // Check navigator.onLine first, and if online, keep trying
        if (!navigator.onLine) {
            // Browser reports offline - verify with actual network check
            this.verifyInternetConnection();
            return;
        }
        
        // We have internet, but chatbot server might be down
        // Don't immediately mark as offline - allow retries first
        const wasConnected = this.isConnected;
        
        // Only mark as offline after multiple consecutive failures
        // This prevents false offline status during temporary network hiccups
        if (this.reconnectAttempts >= this.config.reconnectAttempts) {
            this.isConnected = false;
            this.connectionStatus = 'offline';
            
            if (wasConnected) {
                this.emit('connectionChange', {
                    status: 'offline',
                    isConnected: false
                });
            }
        } else {
            // Still attempting reconnection - mark as degraded/unhealthy but not offline
            this.connectionStatus = 'unhealthy';
            if (wasConnected) {
                this.emit('connectionChange', {
                    status: 'unhealthy',
                    isConnected: false
                });
            }
        }
        
        // Attempt reconnection
        if (this.reconnectAttempts < this.config.reconnectAttempts) {
            this.reconnectAttempts++;
            setTimeout(() => {
                this.checkHealth();
            }, this.config.reconnectDelay * this.reconnectAttempts);
        }
    }
    
    async sendMessage(message, options = {}) {
        if (!message || typeof message !== 'string') {
            throw new Error('Message must be a non-empty string');
        }
        
        // Validate message length
        if (message.length > 1000) {
            throw new Error('Message too long. Maximum 1000 characters.');
        }
        
        const requestData = {
            message: message.trim(),
            timestamp: new Date().toISOString(),
            ...options
        };
        
        // Only queue if we truly have no internet connection
        // If we have internet but chatbot is down, still try to send (it will retry)
        if (!this.isConnected && !navigator.onLine) {
            // Verify internet connection before giving up
            await this.verifyInternetConnection();
            
            // If still no internet after verification, queue and return offline response
            if (!navigator.onLine) {
                this.requestQueue.push(requestData);
                return this.getOfflineResponse();
            }
        }
        
        // If we have internet connection, try to send even if chatbot appears offline
        // This allows the chatbot to recover automatically when server comes back online
        
        try {
            const response = await this.makeSecureRequest('/api/chat', {
                method: 'POST',
                body: JSON.stringify(requestData)
            });
            
            if (response.success) {
                this.reconnectAttempts = 0; // Reset on successful request
                this.emit('message', response);
                return response;
            } else {
                throw new Error(response.error || 'Unknown error');
            }
        } catch (error) {
            console.error('Message send failed:', error);
            
            // Queue request for retry
            this.requestQueue.push(requestData);
            
            this.emit('error', {
                type: 'message_send_failed',
                error: error.message,
                message: message
            });
            
            throw error;
        }
    }
    
    async makeSecureRequest(endpoint, options = {}) {
        const url = this.config.apiEndpoint.replace('/api/chat', endpoint);
        
        const requestOptions = {
            timeout: this.config.requestTimeout,
            headers: {
                'Content-Type': 'application/json',
                // Prefer Bearer token
                ...(this.token ? { 'Authorization': `Bearer ${this.token}` } : {}),
                'X-Client-Version': '3.0.0',
                'X-Request-ID': this.generateRequestId(),
                ...options.headers
            },
            ...options
        };
        
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.config.requestTimeout);
        
        try {
            // Ensure token is present before making authenticated request
            if (!requestOptions.headers.Authorization) {
                await this.refreshToken();
                requestOptions.headers = {
                    ...requestOptions.headers,
                    ...(this.token ? { 'Authorization': `Bearer ${this.token}` } : {})
                };
            }
            
            const response = await fetch(url, {
                ...requestOptions,
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
            if (!response.ok) {
                // If unauthorized, try to refresh token once and retry
                if (response.status === 401) {
                    const refreshed = await this.refreshToken();
                    if (refreshed) {
                        const retry = await fetch(url, {
                            ...requestOptions,
                            headers: {
                                ...requestOptions.headers,
                                'Authorization': `Bearer ${this.token}`
                            },
                            signal: controller.signal
                        });
                        if (!retry.ok) {
                            throw new Error(`Request failed after refresh: ${retry.status} ${retry.statusText}`);
                        }
                        return await retry.json();
                    }
                }
                if (response.status === 401) {
                    throw new Error('Authentication failed. Please check your API key.');
                } else if (response.status === 429) {
                    throw new Error('Rate limit exceeded. Please wait before sending another message.');
                } else if (response.status === 403) {
                    throw new Error('Access forbidden. Your IP may be blocked.');
                } else {
                    throw new Error(`Request failed: ${response.status} ${response.statusText}`);
                }
            }
            
            const data = await response.json();
            return data;
            
        } catch (error) {
            clearTimeout(timeoutId);
            
            if (error.name === 'AbortError') {
                throw new Error('Request timeout. Please check your connection.');
            }
            
            throw error;
        }
    }
    
    generateRequestId() {
        return 'req_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    async processRequestQueue() {
        if (this.isProcessingQueue || this.requestQueue.length === 0) {
            return;
        }
        
        this.isProcessingQueue = true;
        
        while (this.requestQueue.length > 0 && this.isConnected) {
            const requestData = this.requestQueue.shift();
            
            try {
                const response = await this.makeSecureRequest('/api/chat', {
                    method: 'POST',
                    body: JSON.stringify(requestData)
                });
                
                this.emit('message', response);
            } catch (error) {
                console.error('Queued request failed:', error);
                // Re-queue the request if it fails
                this.requestQueue.unshift(requestData);
                break;
            }
        }
        
        this.isProcessingQueue = false;
    }
    
    getOfflineResponse() {
        const responses = {
            'nl': 'Onze chatbot is tijdelijk offline. Bel direct: 0522 281 528',
            'en': 'Our chatbot is temporarily offline. Call directly: 0522 281 528',
            'de': 'Unser Chatbot ist vorübergehend offline. Rufen Sie direkt an: 0522 281 528'
        };
        
        // Detect language from browser or stored preference
        let language = 'nl';  // Default to Dutch
        
        // Check for stored language preference
        const storedLang = sessionStorage.getItem('chatbot_language');
        if (storedLang && ['nl', 'en', 'de'].includes(storedLang)) {
            language = storedLang;
        } else {
            // Detect from browser language
            const browserLang = navigator.language || navigator.userLanguage || 'nl';
            if (browserLang.toLowerCase().startsWith('en')) {
                language = 'en';
            } else if (browserLang.toLowerCase().startsWith('de')) {
                language = 'de';
            }
        }
        
        return {
            response: responses[language],
            response_type: 'offline',
            success: true,
            offline: true,
            timestamp: new Date().toISOString()
        };
    }
    
    setupErrorHandling() {
        // Handle network errors
        window.addEventListener('online', () => {
            console.log('Network connection restored');
            // Immediately check health when connection is restored
            this.checkHealth();
        });
        
        window.addEventListener('offline', () => {
            console.log('Browser reports offline - verifying with actual network check');
            // Don't immediately mark as offline - verify with actual network request
            // The browser's offline event can be unreliable
            this.verifyInternetConnection();
        });
    }
    
    async verifyInternetConnection() {
        // Verify actual internet connectivity, not just browser's offline status
        // Try to fetch a small resource to confirm internet access
        const testUrls = [
            this.config.healthEndpoint, // Try chatbot health endpoint first
            'https://www.google.com/favicon.ico?t=' + Date.now(), // Add timestamp to prevent caching
            'https://www.cloudflare.com/favicon.ico?t=' + Date.now()
        ];
        
        let hasInternet = false;
        
        for (const url of testUrls) {
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 3000);
                
                // Try to fetch the resource
                // Use HEAD method to minimize data transfer
                try {
                    const response = await fetch(url, {
                        method: 'HEAD',
                        signal: controller.signal,
                        cache: 'no-cache',
                        credentials: 'omit'
                    });
                    clearTimeout(timeoutId);
                    // If we got a response, we have internet
                    hasInternet = true;
                    break;
                } catch (corsError) {
                    // If CORS fails, try with no-cors mode as fallback
                    // This will work even if we can't read the response
                    try {
                        await fetch(url, {
                            method: 'HEAD',
                            mode: 'no-cors',
                            signal: controller.signal,
                            cache: 'no-cache'
                        });
                        clearTimeout(timeoutId);
                        // If no-cors fetch succeeds (no exception), we have internet
                        hasInternet = true;
                        break;
                    } catch (noCorsError) {
                        clearTimeout(timeoutId);
                        // Both attempts failed, continue to next URL
                        continue;
                    }
                }
            } catch (error) {
                // Continue to next URL
                continue;
            }
        }
        
        // Only mark as offline if we truly have no internet
        if (!hasInternet && !navigator.onLine) {
            const wasConnected = this.isConnected;
            this.isConnected = false;
            this.connectionStatus = 'offline';
            
            if (wasConnected) {
                this.emit('connectionChange', {
                    status: 'offline',
                    isConnected: false
                });
            }
        } else if (hasInternet || navigator.onLine) {
            // We have internet, check chatbot health
            // Reset reconnect attempts since we have internet
            this.reconnectAttempts = 0;
            this.checkHealth();
        }
    }
    
    // Event system
    on(event, callback) {
        if (this.eventListeners[event]) {
            this.eventListeners[event].push(callback);
        }
    }
    
    off(event, callback) {
        if (this.eventListeners[event]) {
            const index = this.eventListeners[event].indexOf(callback);
            if (index > -1) {
                this.eventListeners[event].splice(index, 1);
            }
        }
    }
    
    emit(event, data) {
        if (this.eventListeners[event]) {
            this.eventListeners[event].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error('Error in event callback:', error);
                }
            });
        }
    }
    
    // Public methods
    getConnectionStatus() {
        return {
            isConnected: this.isConnected,
            status: this.connectionStatus,
            lastHealthCheck: this.lastHealthCheck,
            queuedRequests: this.requestQueue.length
        };
    }
    
    async forceReconnect() {
        this.reconnectAttempts = 0;
        return await this.checkHealth();
    }
    
    clearQueue() {
        this.requestQueue = [];
    }
    
    destroy() {
        this.stopHealthMonitoring();
        this.clearQueue();
        this.eventListeners = {
            connectionChange: [],
            error: [],
            message: []
        };
    }
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SecureChatbotClient;
}

// Global instance for easy access
window.SecureChatbotClient = SecureChatbotClient;
