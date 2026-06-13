<?php
/**
 * Blog Portal Authentication Check
 * Include at top of protected pages - redirects to login if not authenticated
 */
require_once __DIR__ . '/../admin/session-config.php';

if (!isset($_SESSION['blog_authenticated']) || $_SESSION['blog_authenticated'] !== true) {
    unset($_SESSION['blog_authenticated'], $_SESSION['blog_login_time']);
    header('Location: /blog-portal/login');
    exit;
}
