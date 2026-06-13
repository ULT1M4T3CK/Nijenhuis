<?php
/**
 * Emergency Session Clear Script
 * Use this to manually clear all admin sessions if logout isn't working
 * 
 * WARNING: This will log out ALL users
 * Access: http://localhost:8888/admin/clear-session.php
 */

// Start session to access it
session_start();

// Store old session ID for logging
$oldSessionId = session_id();

// Clear all session data
$_SESSION = [];

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_unset();
session_destroy();

// Also try to delete session file if using file-based sessions
$sessionFile = session_save_path() . '/sess_' . $oldSessionId;
if (file_exists($sessionFile)) {
    @unlink($sessionFile);
}

// Clear all session files (if needed)
$sessionPath = session_save_path();
if ($sessionPath && is_dir($sessionPath)) {
    $files = glob($sessionPath . '/sess_*');
    foreach ($files as $file) {
        if (is_file($file)) {
            // Only delete if older than 1 hour (safety measure)
            if (time() - filemtime($file) > 3600) {
                @unlink($file);
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Session cleared successfully',
    'session_id' => $oldSessionId,
    'redirect' => '/pages/admin-login.php'
]);

// Redirect after a short delay
header('Refresh: 2; url=/pages/admin-login.php');

?>
