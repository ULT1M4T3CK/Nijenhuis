/**
 * Centralized Error Handling System
 * Provides secure error handling that doesn't leak sensitive information
 */

class ErrorHandler {
    constructor() {
        this.errorLog = [];
        this.maxLogEntries = 100;
        this.setupGlobalErrorHandling();
    }
    
    /**
     * Setup global error handling
     */
    setupGlobalErrorHandling() {
        // Handle uncaught JavaScript errors
        window.addEventListener('error', (event) => {
            this.logError('JavaScript Error', {
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno
            });
        });
        
        // Handle unhandled promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            this.logError('Unhandled Promise Rejection', {
                reason: event.reason?.message || 'Unknown reason'
            });
        });
    }
    
    /**
     * Log an error securely (no sensitive data exposure)
     * @param {string} type - Error type
     * @param {Object} details - Error details (will be sanitized)
     */
    logError(type, details = {}) {
        const timestamp = new Date().toISOString();
        const errorEntry = {
            timestamp,
            type,
            details: this.sanitizeErrorDetails(details),
            userAgent: navigator.userAgent,
            url: window.location.href
        };
        
        this.errorLog.push(errorEntry);
        
        // Keep log size manageable
        if (this.errorLog.length > this.maxLogEntries) {
            this.errorLog = this.errorLog.slice(-this.maxLogEntries);
        }
        
        // In development, log to console
        if (this.isDevelopment()) {
            console.error(`[${timestamp}] ${type}:`, details);
        }
        
        // In production, could send to error reporting service
        // this.sendToErrorService(errorEntry);
    }
    
    /**
     * Sanitize error details to remove sensitive information
     * @param {Object} details - Error details
     * @returns {Object} - Sanitized details
     */
    sanitizeErrorDetails(details) {
        const sanitized = {};
        const sensitiveKeys = ['password', 'token', 'key', 'secret', 'auth', 'session'];
        
        for (const [key, value] of Object.entries(details)) {
            if (sensitiveKeys.some(sensitive => key.toLowerCase().includes(sensitive))) {
                sanitized[key] = '[REDACTED]';
            } else if (typeof value === 'string' && value.length > 500) {
                sanitized[key] = value.substring(0, 500) + '... [TRUNCATED]';
            } else {
                sanitized[key] = value;
            }
        }
        
        return sanitized;
    }
    
    /**
     * Handle API errors safely
     * @param {Response} response - Fetch response
     * @param {string} context - Context where error occurred
     * @returns {string} - User-friendly error message
     */
    async handleApiError(response, context = 'API call') {
        let userMessage = 'Er is een technische fout opgetreden. Probeer het later opnieuw.';
        
        try {
            const errorData = await response.json();
            
            // Log the technical error
            this.logError('API Error', {
                status: response.status,
                statusText: response.statusText,
                context: context,
                response: errorData
            });
            
            // Return user-friendly message based on status
            switch (response.status) {
                case 400:
                    userMessage = 'Ongeldige gegevens ingevoerd. Controleer uw invoer.';
                    break;
                case 401:
                    userMessage = 'U bent niet geautoriseerd. Log opnieuw in.';
                    break;
                case 403:
                    userMessage = 'U heeft geen toegang tot deze functie.';
                    break;
                case 404:
                    userMessage = 'De gevraagde pagina werd niet gevonden.';
                    break;
                case 429:
                    userMessage = 'Te veel verzoeken. Wacht even en probeer opnieuw.';
                    break;
                case 500:
                case 502:
                case 503:
                case 504:
                    userMessage = 'Server tijdelijk niet beschikbaar. Probeer het later opnieuw.';
                    break;
                default:
                    // Use error message from server if it's safe
                    if (errorData.message && this.isSafeMessage(errorData.message)) {
                        userMessage = errorData.message;
                    }
            }
            
        } catch (parseError) {
            // If we can't parse the error response
            this.logError('Error Response Parse Failed', {
                status: response.status,
                statusText: response.statusText,
                context: context,
                parseError: parseError.message
            });
        }
        
        return userMessage;
    }
    
    /**
     * Check if an error message is safe to show to users
     * @param {string} message - Error message
     * @returns {boolean} - True if safe
     */
    isSafeMessage(message) {
        const dangerousPatterns = [
            /sql/i, /database/i, /mysql/i, /postgresql/i,
            /stack trace/i, /exception/i, /internal error/i,
            /file not found/i, /permission denied/i,
            /path/i, /directory/i, /server error/i
        ];
        
        return !dangerousPatterns.some(pattern => pattern.test(message));
    }
    
    /**
     * Handle network errors
     * @param {Error} error - Network error
     * @param {string} context - Context where error occurred
     * @returns {string} - User-friendly error message
     */
    handleNetworkError(error, context = 'Network request') {
        this.logError('Network Error', {
            message: error.message,
            name: error.name,
            context: context
        });
        
        if (error.name === 'AbortError') {
            return 'Verzoek geannuleerd.';
        } else if (!navigator.onLine) {
            return 'Geen internetverbinding. Controleer uw verbinding.';
        } else {
            return 'Verbindingsfout. Controleer uw internetverbinding en probeer opnieuw.';
        }
    }
    
    /**
     * Handle validation errors
     * @param {Array} errors - Array of validation errors
     * @returns {string} - Formatted error message
     */
    handleValidationErrors(errors) {
        if (!Array.isArray(errors) || errors.length === 0) {
            return 'Ongeldige gegevens ingevoerd.';
        }
        
        // Log validation errors (they're usually safe)
        this.logError('Validation Error', { errors });
        
        // Return first few errors to avoid overwhelming the user
        const maxErrors = 3;
        const displayErrors = errors.slice(0, maxErrors);
        let message = displayErrors.join(', ');
        
        if (errors.length > maxErrors) {
            message += ` en ${errors.length - maxErrors} andere fout(en)`;
        }
        
        return message;
    }
    
    /**
     * Check if we're in development mode
     * @returns {boolean} - True if development
     */
    isDevelopment() {
        return window.location.hostname === 'localhost' ||
               window.location.hostname === '127.0.0.1' ||
               window.location.hostname === '';
    }
    
    /**
     * Get error log for debugging (development only)
     * @returns {Array} - Error log entries
     */
    getErrorLog() {
        if (!this.isDevelopment()) {
            return [];
        }
        return [...this.errorLog];
    }
    
    /**
     * Clear error log
     */
    clearErrorLog() {
        this.errorLog = [];
    }
    
    /**
     * Show user-friendly error message in UI
     * @param {string} message - Error message
     * @param {string} elementId - Element ID to show error in
     */
    showError(message, elementId = null) {
        if (elementId) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
                return;
            }
        }
        
        // Fallback: show in alert (not ideal for production)
        if (this.isDevelopment()) {
            alert(`Fout: ${message}`);
        }
    }
    
    /**
     * Hide error message in UI
     * @param {string} elementId - Element ID to hide error in
     */
    hideError(elementId) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }
}

// Create global instance
window.ErrorHandler = new ErrorHandler();

// Export for module environments
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ErrorHandler;
}

