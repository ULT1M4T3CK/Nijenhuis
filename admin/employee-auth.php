<?php
/**
 * Employee Auth Check
 * Include this file at the top of employee-only pages
 */

// 1. Initialize session with standard config
require_once __DIR__ . '/session-config.php';

// 2. Verify authentication (employees use admin auth)
if (empty($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    header('Location: /login');
    exit;
}
// User is authenticated and session is valid
?>
