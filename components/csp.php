<?php
/**
 * Content-Security-Policy — single source of truth for PHP pages.
 */
function nijenhuis_csp_directives(): array {
    return [
        "default-src 'self'",
        "base-uri 'self'",
        "form-action 'self' https://*.mollie.com",
        "frame-ancestors 'self'",
        "object-src 'none'",
        "img-src 'self' data: blob: https:",
        "font-src 'self' data: https://fonts.gstatic.com",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
        "script-src 'self' 'unsafe-inline' https://www.googletagmanager.com https://www.google-analytics.com https://js.mollie.com",
        "script-src-elem 'self' 'unsafe-inline' https://www.googletagmanager.com https://www.google-analytics.com https://js.mollie.com",
        "connect-src 'self' https://www.google-analytics.com https://api.mollie.com https://js.mollie.com",
        "frame-src 'self' https://*.mollie.com https://www.google.com https://maps.googleapis.com",
        "worker-src 'self' blob:",
        "manifest-src 'self'",
    ];
}

function nijenhuis_send_csp_header(): void {
    if (headers_sent()) {
        return;
    }
    header('Content-Security-Policy: ' . implode('; ', nijenhuis_csp_directives()));
}

function nijenhuis_csp_header_string(): string {
    return implode('; ', nijenhuis_csp_directives());
}
