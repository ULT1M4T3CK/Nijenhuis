/**
 * Security utilities for input sanitization and validation
 * These functions help prevent XSS, injection attacks, and other security issues
 */

class SecurityUtils {
    /**
     * Escape HTML characters to prevent XSS attacks
     * @param {string} text - Text to escape
     * @returns {string} - Escaped text
     */
    static escapeHtml(text) {
        if (typeof text !== 'string') {
            return '';
        }
        
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Sanitize text input by removing dangerous characters
     * @param {string} input - Input to sanitize
     * @returns {string} - Sanitized input
     */
    static sanitizeText(input) {
        if (typeof input !== 'string') {
            return '';
        }
        
        // Remove potential script tags and dangerous characters
        return input
            .replace(/<script[^>]*>.*?<\/script>/gi, '')
            .replace(/<iframe[^>]*>.*?<\/iframe>/gi, '')
            .replace(/javascript:/gi, '')
            .replace(/on\w+\s*=/gi, '')
            .replace(/data:text\/html/gi, '')
            .trim();
    }
    
    /**
     * Validate email format
     * @param {string} email - Email to validate
     * @returns {boolean} - True if valid email
     */
    static isValidEmail(email) {
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailRegex.test(email);
    }
    
    /**
     * Validate phone number (Dutch format)
     * @param {string} phone - Phone number to validate
     * @returns {boolean} - True if valid phone
     */
    static isValidPhone(phone) {
        // Allow various Dutch phone formats
        const phoneRegex = /^(\+31|0031|0)[6-9]\d{8}$|^(\+31|0031|0)[2-5]\d{8}$/;
        const cleaned = phone.replace(/[\s\-\(\)]/g, '');
        return phoneRegex.test(cleaned);
    }
    
    /**
     * Validate date format (YYYY-MM-DD)
     * @param {string} date - Date to validate
     * @returns {boolean} - True if valid date
     */
    static isValidDate(date) {
        const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
        if (!dateRegex.test(date)) {
            return false;
        }
        
        const dateObj = new Date(date);
        return dateObj instanceof Date && !isNaN(dateObj);
    }
    
    /**
     * Sanitize and validate booking data
     * @param {Object} bookingData - Booking data to validate
     * @returns {Object} - Validation result with sanitized data
     */
    static validateBookingData(bookingData) {
        const result = {
            isValid: true,
            errors: [],
            sanitizedData: {}
        };
        
        // Validate and sanitize customer name
        if (!bookingData.customerName || bookingData.customerName.trim().length < 2) {
            result.isValid = false;
            result.errors.push('Naam moet minimaal 2 karakters bevatten');
        } else {
            result.sanitizedData.customerName = this.sanitizeText(bookingData.customerName);
        }
        
        // Validate email
        if (!bookingData.customerEmail || !this.isValidEmail(bookingData.customerEmail)) {
            result.isValid = false;
            result.errors.push('Ongeldig emailadres');
        } else {
            result.sanitizedData.customerEmail = bookingData.customerEmail.toLowerCase().trim();
        }
        
        // Validate phone
        if (!bookingData.customerPhone || !this.isValidPhone(bookingData.customerPhone)) {
            result.isValid = false;
            result.errors.push('Ongeldig telefoonnummer');
        } else {
            result.sanitizedData.customerPhone = bookingData.customerPhone.replace(/[\s\-\(\)]/g, '');
        }
        
        // Validate date
        if (!bookingData.date || !this.isValidDate(bookingData.date)) {
            result.isValid = false;
            result.errors.push('Ongeldige datum');
        } else {
            result.sanitizedData.date = bookingData.date;
        }
        
        // Validate boat type
        const validBoatTypes = [
            'classic-tender-720', 'electrosloop-10', 'classic-tender-570',
            'electrosloop-8', 'sailboat-4-5', 'sailpunter-3-4',
            'electroboat-5', 'canoe-3', 'kayak-2', 'kayak-1', 'sup-board'
        ];
        
        if (!bookingData.boatType || !validBoatTypes.includes(bookingData.boatType)) {
            result.isValid = false;
            result.errors.push('Ongeldig boottype');
        } else {
            result.sanitizedData.boatType = bookingData.boatType;
        }
        
        // Sanitize notes (optional)
        if (bookingData.notes) {
            result.sanitizedData.notes = this.sanitizeText(bookingData.notes);
        }
        
        return result;
    }
    
    /**
     * Create a CSRF token for forms
     * @returns {string} - CSRF token
     */
    static generateCSRFToken() {
        const array = new Uint8Array(32);
        crypto.getRandomValues(array);
        return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
    }
    
    /**
     * Validate CSRF token using constant-time comparison to prevent timing attacks
     * @param {string} token - Token to validate
     * @param {string} sessionToken - Session token to compare against
     * @returns {boolean} - True if valid
     */
    static validateCSRFToken(token, sessionToken) {
        if (!token || !sessionToken) {
            return false;
        }
        
        // Constant-time comparison to prevent timing attacks
        if (token.length !== sessionToken.length) {
            return false;
        }
        
        let result = 0;
        for (let i = 0; i < token.length; i++) {
            result |= token.charCodeAt(i) ^ sessionToken.charCodeAt(i);
        }
        return result === 0;
    }
    
    /**
     * Rate limiting helper
     * @param {string} key - Rate limiting key
     * @param {number} maxRequests - Maximum requests allowed
     * @param {number} windowMs - Time window in milliseconds
     * @returns {boolean} - True if request is allowed
     */
    static checkRateLimit(key, maxRequests = 10, windowMs = 60000) {
        const now = Date.now();
        const storageKey = `rateLimit_${key}`;
        
        try {
            const stored = localStorage.getItem(storageKey);
            const data = stored ? JSON.parse(stored) : { requests: [], firstRequest: now };
            
            // Remove old requests outside the window
            data.requests = data.requests.filter(time => now - time < windowMs);
            
            // Check if limit exceeded
            if (data.requests.length >= maxRequests) {
                return false;
            }
            
            // Add current request
            data.requests.push(now);
            localStorage.setItem(storageKey, JSON.stringify(data));
            
            return true;
        } catch (error) {
            // If localStorage fails, allow the request
            console.warn('Rate limiting storage failed:', error);
            return true;
        }
    }
    
    /**
     * Safely create DOM elements with text content
     * @param {string} tagName - HTML tag name
     * @param {string} textContent - Text content (will be escaped)
     * @param {string} className - CSS class name
     * @returns {HTMLElement} - Created element
     */
    static createSafeElement(tagName, textContent = '', className = '') {
        const element = document.createElement(tagName);
        if (textContent) {
            element.textContent = textContent; // This automatically escapes
        }
        if (className) {
            element.className = className;
        }
        return element;
    }
    
    /**
     * Remove all children from a DOM element safely
     * @param {HTMLElement} element - Element to clear
     */
    static clearElement(element) {
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }
    }
    
    /**
     * Sanitize HTML by allowing only safe tags and attributes
     * Uses DOMParser instead of innerHTML to prevent XSS
     * @param {string} html - HTML string to sanitize
     * @param {Object} options - Sanitization options
     * @returns {string} - Sanitized HTML string
     */
    static sanitizeHtml(html, options = {}) {
        if (typeof html !== 'string') {
            return '';
        }
        
        // Allowed tags (default safe tags)
        const allowedTags = options.allowedTags || ['strong', 'em', 'b', 'i', 'u', 'br', 'p', 'a'];
        
        // Allowed attributes
        const allowedAttributes = options.allowedAttributes || {
            'a': ['href'],
            '*': ['class']
        };
        
        // Use DOMParser instead of innerHTML to prevent XSS
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const tempDiv = doc.body;
        
        // Recursively sanitize all elements
        const sanitizeNode = (node) => {
            if (node.nodeType === Node.TEXT_NODE) {
                return node.cloneNode(true);
            }
            
            if (node.nodeType === Node.ELEMENT_NODE) {
                const tagName = node.tagName.toLowerCase();
                
                // If tag is not allowed, return only its text content
                if (!allowedTags.includes(tagName)) {
                    const textNode = document.createTextNode(node.textContent);
                    return textNode;
                }
                
                // Create a safe copy of the element
                const safeElement = document.createElement(tagName);
                
                // Copy allowed attributes
                const attrs = allowedAttributes[tagName] || allowedAttributes['*'] || [];
                for (const attr of attrs) {
                    if (node.hasAttribute(attr)) {
                        const attrValue = node.getAttribute(attr);
                        // Sanitize href to prevent javascript: and data: URLs
                        if (attr === 'href') {
                            if (attrValue && !attrValue.match(/^(javascript|data|vbscript):/i)) {
                                safeElement.setAttribute(attr, attrValue);
                            }
                        } else {
                            safeElement.setAttribute(attr, attrValue);
                        }
                    }
                }
                
                // Recursively sanitize children
                for (const child of Array.from(node.childNodes)) {
                    const safeChild = sanitizeNode(child);
                    safeElement.appendChild(safeChild);
                }
                
                return safeElement;
            }
            
            return null;
        };
        
        // Sanitize all nodes
        const sanitizedNodes = [];
        for (const child of Array.from(tempDiv.childNodes)) {
            const sanitized = sanitizeNode(child);
            if (sanitized) {
                sanitizedNodes.push(sanitized);
            }
        }
        
        // Reconstruct HTML
        const resultDiv = document.createElement('div');
        for (const node of sanitizedNodes) {
            resultDiv.appendChild(node);
        }
        
        return resultDiv.innerHTML;
    }
}

// Export for use in other modules
window.SecurityUtils = SecurityUtils;

// For module environments
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SecurityUtils;
}

