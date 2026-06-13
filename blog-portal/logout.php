<?php
require_once __DIR__ . '/portal-headers.php';
require_once __DIR__ . '/../admin/session-config.php';

// Require the caller to supply the session-bound CSRF token. This turns the
// former GET-click logout into an authenticated action that can't be
// triggered by an attacker embedding <img src="/blog-portal/logout"> on a
// different site. The sidebar link below passes the token as a query arg.
if (!empty($_SESSION['blog_authenticated'])) {
    $token = $_GET['csrf'] ?? ($_POST['csrf'] ?? '');
    $sessionToken = $_SESSION['blog_csrf_token'] ?? '';
    if (empty($sessionToken) || empty($token) || !hash_equals($sessionToken, (string) $token)) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Invalid CSRF token';
        exit;
    }
}

unset($_SESSION['blog_authenticated'], $_SESSION['blog_login_time'], $_SESSION['blog_csrf_token']);
header('Location: /blog-portal/login');
exit;
