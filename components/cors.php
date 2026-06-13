<?php
/**
 * ========================================================================
 * CORS CONFIGURATION - Nijenhuis Botenverhuur
 * ========================================================================
 * Centralized CORS configuration to prevent inconsistencies
 */

/**
 * Get allowed origins based on environment
 * 
 * @return array List of allowed origins
 */
function getAllowedOrigins() {
    $appEnv = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'production');
    
    $origins = [
        'https://nijenhuis-botenverhuur.com',
        'https://www.nijenhuis-botenverhuur.com',
    ];
    
    // Only add localhost in development
    if ($appEnv !== 'production') {
        $origins = array_merge($origins, [
            'http://localhost:3000',
            'http://localhost:8080',
            'http://localhost:8888',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:8080',
            'http://127.0.0.1:8888',
        ]);
    }
    
    // Add chatbot origins from environment variable
    $chatbotOriginsEnv = getenv('CHATBOT_ALLOWED_ORIGINS') ?: ($_ENV['CHATBOT_ALLOWED_ORIGINS'] ?? '');
    if (!empty($chatbotOriginsEnv)) {
        $chatbotOrigins = array_map('trim', explode(',', $chatbotOriginsEnv));
        $origins = array_merge($origins, $chatbotOrigins);
    }

    // Add VYBR!S platform origins (dedicated variable for booking integration)
    $vybrisOriginsEnv = getenv('VYBRIS_ALLOWED_ORIGINS') ?: ($_ENV['VYBRIS_ALLOWED_ORIGINS'] ?? '');
    if (!empty($vybrisOriginsEnv)) {
        $vybrisOrigins = array_map('trim', explode(',', $vybrisOriginsEnv));
        $origins = array_merge($origins, $vybrisOrigins);
    }

    return array_unique($origins);
}

/**
 * Set CORS headers for allowed origin
 * 
 * @param string $origin Origin header value
 * @param array $allowedOrigins List of allowed origins (optional, uses getAllowedOrigins() if not provided)
 * @param bool $allowCredentials Whether to allow credentials
 * @return bool True if headers were set, false if origin not allowed
 */
function setCorsHeadersSafe($origin, $allowedOrigins = null, $allowCredentials = false) {
    if ($allowedOrigins === null) {
        $allowedOrigins = getAllowedOrigins();
    }
    
    if (empty($origin)) {
        return false;
    }
    
    // Remove trailing slash for comparison
    $cleanOrigin = rtrim($origin, '/');
    
    if (!in_array($cleanOrigin, $allowedOrigins, true)) {
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
 * Handle CORS preflight requests
 * 
 * @return bool True if preflight was handled, false otherwise
 */
function handleCorsPreflight() {
    if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
        return false;
    }
    
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowedOrigins = getAllowedOrigins();
    
    if (setCorsHeadersSafe($origin, $allowedOrigins, true)) {
        http_response_code(200);
        exit;
    }
    
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Origin not allowed']);
    exit;
}

?>
