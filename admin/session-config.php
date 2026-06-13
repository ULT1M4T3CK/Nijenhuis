<?php
// Centralized session configuration
// This file ensures consistent session settings across all admin pages and handlers

// Prevent multiple session starts
if (session_status() === PHP_SESSION_NONE) {
    // Session security settings
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    
    // Set cookie parameters before starting session
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    // Start the session
    session_start();
}
