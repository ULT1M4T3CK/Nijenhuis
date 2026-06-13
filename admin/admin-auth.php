<?php
// Standardized Admin Authentication Check
// Include this file at the top of every admin page

// 1. Initialize session with standard config
require_once __DIR__ . '/session-config.php';

// 2. Verify authentication
// Check if session variable exists and is explicitly true (strict check)
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    // Clear any partial session data
    unset($_SESSION['admin_authenticated']);
    unset($_SESSION['admin_user']);
    unset($_SESSION['admin_login_time']);
    
    // Not authenticated - redirect to login
    header('Location: /pages/admin-login.php');
    exit;
}
// User is authenticated and session is valid
?>
