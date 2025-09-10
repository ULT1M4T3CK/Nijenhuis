/**
 * Secure Chatbot Client for Nijenhuis Website
 * Handles authentication, connection monitoring, and secure communication
 */

class SecureChatbotClient {
    constructor(config = {}) {
        this.config = {
            apiEndpoint: config.apiEndpoint || 'http://localhost:5001/api/chat',
            healthEndpoint: config.healthEndpoint || 'http://localhost:5001/api/health',
            apiKey: config.apiKey || this._getStoredApiKey(),
            reconnectAttempts: config.reconnectAttempts || 3,
            reconnectDelay: config.reconnectDelay || 2000,
            requestTimeout: config.requestTimeout || 10000,
            ...config
        };
        
        // Connection state
        this.isConnected = false;
        this.connectionStatus = 'unknown';
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
        this.startHealthMonitoring();
        this.loadStoredApiKey();
        this.setupErrorHandling();
    }
    
    _getStoredApiKey() {
        // In production, this should be securely stored
        // For development, we'll use a default key
        return localStorage.getItem('nijenhuis_api_key') || 'default_development_key';
    }
    
    loadStoredApiKey() {
        const storedKey = localStorage.getItem('nijenhuis_api_key');
        if (storedKey) {
            this.config.apiKey = storedKey;
        }
    }
    
    setApiKey(apiKey) {
        this.config.apiKey = apiKey;
        localStorage.setItem('nijenhuis_api_key', apiKey);
        this.emit('connectionChange', { status: 'authenticated' });
    }
    
    startHealthMonitoring() {
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
        try {
            const response = await fetch(this.config.healthEndpoint, {
                method: 'GET',
                timeout: this.config.requestTimeout,
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.lastHealthCheck = new Date();
                
                const wasConnected = this.isConnected;
                this.isConnected = data.status === 'healthy' || data.status === 'degraded';
                this.connectionStatus = data.status;
                
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
            console.warn('Health check failed:', error);
            this.handleConnectionError();
            return null;
        }
    }
    
    handleConnectionError() {
        const wasConnected = this.isConnected;
        this.isConnected = false;
        this.connectionStatus = 'offline';
        
        if (wasConnected) {
            this.emit('connectionChange', {
                status: 'offline',
                isConnected: false
            });
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
        
        // If offline, queue the request
        if (!this.isConnected) {
            this.requestQueue.push(requestData);
            return this.getOfflineResponse();
        }
        
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
                'X-API-Key': this.config.apiKey,
                'X-Client-Version': '3.0.0',
                'X-Request-ID': this.generateRequestId(),
                ...options.headers
            },
            ...options
        };
        
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.config.requestTimeout);
        
        try {
            const response = await fetch(url, {
                ...requestOptions,
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
            if (!response.ok) {
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
        
        // Detect language from browser or default to Dutch
        const language = navigator.language.startsWith('en') ? 'en' : 
                        navigator.language.startsWith('de') ? 'de' : 'nl';
        
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
            this.checkHealth();
        });
        
        window.addEventListener('offline', () => {
            console.log('Network connection lost');
            this.isConnected = false;
            this.connectionStatus = 'offline';
            this.emit('connectionChange', {
                status: 'offline',
                isConnected: false
            });
        });
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
