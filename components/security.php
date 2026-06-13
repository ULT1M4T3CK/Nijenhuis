<?php
/**
 * ========================================================================
 * SECURITY UTILITIES - Nijenhuis Botenverhuur
 * ========================================================================
 * Centralized security functions for input sanitization, output escaping,
 * and security-related operations.
 */

/**
 * Sanitize output to prevent XSS attacks
 * Recursively sanitizes arrays and strings
 * 
 * @param mixed $data Data to sanitize (string, array, or object)
 * @return mixed Sanitized data
 */
function sanitizeOutput($data) {
    if (is_array($data)) {
        return array_map('sanitizeOutput', $data);
    } elseif (is_object($data)) {
        $sanitized = new stdClass();
        foreach ($data as $key => $value) {
            $sanitized->$key = sanitizeOutput($value);
        }
        return $sanitized;
    } elseif (is_string($data)) {
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    return $data;
}

/**
 * Sanitize text input (removes HTML tags, normalizes whitespace)
 * Use for user-provided text fields that should not contain HTML
 * 
 * @param string $text Text to sanitize
 * @return string Sanitized text
 */
function sanitizeText($text) {
    if (!is_string($text)) {
        return '';
    }
    // Remove HTML tags
    $text = strip_tags($text);
    // Normalize whitespace
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

/**
 * Sanitize booking data before storage
 * Ensures all user-provided fields are properly sanitized
 * 
 * @param array $bookingData Raw booking data
 * @return array Sanitized booking data
 */
function sanitizeBookingData($bookingData) {
    $sanitized = [];
    
    // Sanitize text fields
    $textFields = ['customerName', 'customerPhone', 'notes', 'boatType', 'arrivalTime', 'cityOfOrigin', 'engineOption'];
    foreach ($textFields as $field) {
        if (isset($bookingData[$field])) {
            $sanitized[$field] = sanitizeText($bookingData[$field]);
        }
    }
    
    // Validate and sanitize email
    if (isset($bookingData['customerEmail'])) {
        $email = filter_var(trim($bookingData['customerEmail']), FILTER_VALIDATE_EMAIL);
        $sanitized['customerEmail'] = $email ? strtolower($email) : '';
    }
    
    // Validate dates (already validated elsewhere, but ensure format)
    $dateFields = ['date', 'endDate'];
    foreach ($dateFields as $field) {
        if (isset($bookingData[$field])) {
            // Ensure YYYY-MM-DD format
            $date = DateTime::createFromFormat('Y-m-d', $bookingData[$field]);
            $sanitized[$field] = $date ? $date->format('Y-m-d') : '';
        }
    }
    
    // Preserve other fields (numeric, status, etc.) but validate types
    $preserveFields = ['id', 'amount', 'numberOfDays', 'status', 'paymentId', 'source', 'createdAt', 'updatedAt', 'quantity'];
    foreach ($preserveFields as $field) {
        if (isset($bookingData[$field])) {
            $sanitized[$field] = $bookingData[$field];
        }
    }
    
    return $sanitized;
}

/**
 * Validate status value against allowed list (prevents injection)
 * 
 * @param string $status Status to validate
 * @return bool True if status is valid
 */
function isValidBookingStatus($status) {
    $allowedStatuses = [
        'pending', 'paid', 'manual', 'canceled', 'cancelled',
        'picked_up', 'returned', 'temporary', 'not-confirmed',
        'open', 'payment-rejected', 'expired', 'failed',
        // Used by Mollie status sync (pay-on-arrival reservation paid) and
        // by several legacy admin/booking management UI paths. Missing these
        // previously caused updateBookingStatus to reject valid transitions.
        'confirmed', 'confirmed-paid', 'success',
    ];
    return in_array($status, $allowedStatuses, true);
}

/**
 * Sanitize boat data before storage
 * 
 * @param array $boat Raw boat data
 * @return array Sanitized boat data
 */
function sanitizeBoatData($boat) {
    if (!is_array($boat)) {
        return [];
    }
    
    return [
        'id' => sanitizeText($boat['id'] ?? ''),
        'name' => sanitizeText($boat['name'] ?? ''),
        'category' => sanitizeText($boat['category'] ?? ''),
        'description' => sanitizeText($boat['description'] ?? ''),
        'passengerCount' => sanitizeText($boat['passengerCount'] ?? ''),
        'deposit' => (float)($boat['deposit'] ?? 0),
        'pricePerDay' => (float)($boat['pricePerDay'] ?? 0),
        'total' => max(0, (int)($boat['total'] ?? 1)),
        'available' => max(0, (int)($boat['available'] ?? 1)),
        'orderId' => (int)($boat['orderId'] ?? 99),
        'image' => sanitizeText($boat['image'] ?? ''),
        'headerImage' => sanitizeText($boat['headerImage'] ?? ''),
        'availableDays' => is_array($boat['availableDays'] ?? null) 
            ? array_map('intval', array_filter($boat['availableDays'], 'is_numeric'))
            : [],
        'pricing' => is_array($boat['pricing'] ?? null) 
            ? array_map('floatval', array_filter($boat['pricing'], 'is_numeric'))
            : [],
        'pricingWithEngine' => is_array($boat['pricingWithEngine'] ?? null) 
            ? array_map('floatval', array_filter($boat['pricingWithEngine'], 'is_numeric'))
            : []
    ];
}

/**
 * Sanitize for-sale item data
 * 
 * @param array $item Raw item data
 * @return array Sanitized item data
 */
function sanitizeForSaleItem($item) {
    if (!is_array($item)) {
        return [];
    }

    $sanitizeMultiline = function($text) {
        if (!is_string($text)) {
            return '';
        }
        $text = strip_tags($text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        return trim($text);
    };

    $name = sanitizeText($item['name'] ?? ($item['title'] ?? ''));

    // Only accept relative URL paths for images - reject base64 data URIs entirely.
    $sanitizeImageUrl = function($val) {
        $url = sanitizeText($val ?? '');
        // Reject data: URIs (base64 images must not be stored in JSON)
        if (strncmp($url, 'data:', 5) === 0) {
            return '';
        }
        return $url;
    };

    $mainImage = $sanitizeImageUrl($item['mainImage'] ?? ($item['image'] ?? ''));

    $additionalImages = [];
    $rawAdditionalImages = $item['additionalImages'] ?? [];
    if (is_array($rawAdditionalImages)) {
        foreach ($rawAdditionalImages as $img) {
            $clean = $sanitizeImageUrl($img);
            if ($clean !== '') {
                $additionalImages[] = $clean;
            }
        }
    }

    $features = [];
    $rawFeatures = $item['features'] ?? [];
    if (is_array($rawFeatures)) {
        foreach ($rawFeatures as $feature) {
            $clean = sanitizeText($feature);
            if ($clean !== '') {
                $features[] = $clean;
            }
        }
    } elseif (is_string($rawFeatures)) {
        foreach (preg_split('/\r\n|\r|\n/', $rawFeatures) as $feature) {
            $clean = sanitizeText($feature);
            if ($clean !== '') {
                $features[] = $clean;
            }
        }
    }

    return [
        'id' => sanitizeText($item['id'] ?? ''),
        'name' => $name,
        'title' => $name,
        'description' => $sanitizeMultiline($item['description'] ?? ''),
        'price' => (float)($item['price'] ?? 0),
        'category' => sanitizeText($item['category'] ?? ''),
        'featured' => filter_var($item['featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'mainImage' => $mainImage,
        'additionalImages' => $additionalImages,
        'features' => $features,
        'year' => sanitizeText((string)($item['year'] ?? '')),
        'size' => sanitizeText($item['size'] ?? ''),
        'createdAt' => sanitizeText($item['createdAt'] ?? ''),
        'updatedAt' => sanitizeText($item['updatedAt'] ?? ''),
        'image' => $mainImage,
        'available' => (bool)($item['available'] ?? true),
        'orderId' => (int)($item['orderId'] ?? 99)
    ];
}

/**
 * Verify password against hash
 * Supports both plain text (legacy) and hashed passwords
 * 
 * @param string $password Plain text password
 * @param string $storedHash Stored password hash (or plain text for migration)
 * @return bool True if password matches
 */
function verifyPassword($password, $storedHash) {
    // Only modern password hashes are accepted (bcrypt / argon2)
    if (preg_match('/^\$2[ay]\$|\$argon2/', $storedHash)) {
        return password_verify($password, $storedHash);
    }
    return false;
}

/**
 * Hash password using Argon2ID (preferred) or bcrypt
 * 
 * @param string $password Plain text password
 * @return string|false Password hash or false on failure
 */
function hashPassword($password) {
    // Try Argon2ID first (PHP 7.2+)
    if (defined('PASSWORD_ARGON2ID')) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 3          // 3 threads
        ]);
    }
    
    // Fallback to bcrypt
    return password_hash($password, PASSWORD_BCRYPT, [
        'cost' => 12
    ]);
}

/**
 * Check CORS origin against whitelist
 * 
 * @param string $origin Origin header value
 * @param array $allowedOrigins List of allowed origins
 * @return bool True if origin is allowed
 */
function isOriginAllowed($origin, $allowedOrigins = []) {
    if (empty($origin)) {
        return false;
    }
    
    // Remove trailing slash for comparison
    $cleanOrigin = rtrim($origin, '/');
    
    return in_array($cleanOrigin, $allowedOrigins, true);
}

/**
 * Set CORS headers for allowed origin
 * 
 * @param string $origin Origin header value
 * @param array $allowedOrigins List of allowed origins
 * @param bool $allowCredentials Whether to allow credentials
 * @return bool True if headers were set, false if origin not allowed
 */
function setCorsHeaders($origin, $allowedOrigins = [], $allowCredentials = false) {
    if (!isOriginAllowed($origin, $allowedOrigins)) {
        return false;
    }
    
    header("Access-Control-Allow-Origin: $origin");
    if ($allowCredentials) {
        header('Access-Control-Allow-Credentials: true');
    }
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-API-Key, Authorization, X-CSRF-Token');
    header('Access-Control-Max-Age: 86400'); // 24 hours
    
    return true;
}

/**
 * Rate limiting check with atomic file locking
 *
 * @param string $identifier Unique identifier (IP address, API key, etc.)
 * @param int    $maxAttempts Maximum attempts allowed
 * @param int    $timeWindow  Time window in seconds
 * @param bool   $failOpen    If true (default), treat FS errors as "allow" so an
 *                            unavailable tmp dir doesn't DoS all traffic. Login
 *                            and other auth-sensitive call sites should pass
 *                            false to fail closed.
 * @return bool True if request is allowed, false if rate limited
 */
function checkRateLimitAtomic($identifier, $maxAttempts = 100, $timeWindow = 60, $failOpen = true) {
    $limitFile = sys_get_temp_dir() . '/rate_limit_' . md5($identifier);
    $fp = @fopen($limitFile, 'c+');

    if (!$fp) {
        return $failOpen;
    }
    
    $allowed = true;
    
    if (flock($fp, LOCK_EX)) {
        $fileSize = filesize($limitFile);
        $data = ['count' => 0, 'last_reset' => time()];
        
        if ($fileSize > 0) {
            $content = fread($fp, $fileSize);
            $decoded = json_decode($content, true);
            if ($decoded && is_array($decoded)) {
                $data = $decoded;
            }
        }
        
        $now = time();
        
        // Reset if time window has passed
        if ($now - $data['last_reset'] > $timeWindow) {
            $data = ['count' => 0, 'last_reset' => $now];
        }
        
        // Check if limit exceeded
        if ($data['count'] >= $maxAttempts) {
            $allowed = false;
        } else {
            $data['count']++;
            $data['last_reset'] = $data['last_reset'] ?? $now;
        }
        
        // Write updated data
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data));
        fflush($fp);
        
        flock($fp, LOCK_UN);
    }
    
    fclose($fp);
    return $allowed;
}

/**
 * Validate JSON input with proper error handling
 * 
 * @param string $jsonString JSON string to validate
 * @return array|null Decoded array or null on failure
 */
function validateJsonInput($jsonString) {
    if (empty($jsonString)) {
        return null;
    }
    
    $decoded = json_decode($jsonString, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }
    
    if (!is_array($decoded)) {
        return null;
    }
    
    return $decoded;
}

?>
