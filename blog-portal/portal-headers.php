<?php
/**
 * Blog portal HTTP headers. Include once at the start of each portal script (before output).
 * In development, relax framing so IDE/simple-browser previews and embedded dev tools can load
 * the portal without Chrome leaving frames on chrome-error://chromewebdata/ when a subframe load fails.
 */
if (defined('NIJENHUIS_BLOG_PORTAL_HEADERS_SENT')) {
    return;
}
define('NIJENHUIS_BLOG_PORTAL_HEADERS_SENT', true);

require_once __DIR__ . '/../components/config.php';

// Hard-guard the permissive frame-ancestors against production: only allow
// wide-open framing when BOTH isDevelopment() is true AND APP_ENV is not
// 'production'. This prevents a misconfigured prod box (e.g. SERVER_NAME
// accidentally containing "localhost") from disabling clickjacking
// protection on the portal.
$__appEnv = strtolower((string) (getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'production')));
if ($__appEnv !== 'production' && isDevelopment()) {
    header('Content-Security-Policy: frame-ancestors *');
} else {
    header("Content-Security-Policy: frame-ancestors 'self'");
    header('X-Frame-Options: SAMEORIGIN');
}
