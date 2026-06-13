<?php
/**
 * Serves raw article markdown at /blog/slug.md and /en/blog/slug.md (production nginx entry point).
 */
require_once __DIR__ . '/../components/data_access.php';
if (file_exists(__DIR__ . '/../.env')) {
    loadEnvSafe(__DIR__ . '/../.env');
}
require_once __DIR__ . '/../components/config.php';

$slug = $_GET['slug'] ?? '';
$lang = $_GET['lang'] ?? 'nl';
$allowed = ['nl', 'en', 'de'];

if ($slug === '' || !preg_match('/^[a-z0-9\-]+$/', (string) $slug)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Not found';
    exit;
}
if (!in_array($lang, $allowed, true)) {
    $lang = 'nl';
}

$articleDir = __DIR__ . '/../content/articles/' . $slug;
$mdFile = $articleDir . '/' . $lang . '.md';
if (!file_exists($mdFile) && file_exists($articleDir . '/nl.md')) {
    $mdFile = $articleDir . '/nl.md';
}
if (!file_exists($mdFile)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Not found';
    exit;
}

header('Content-Type: text/markdown; charset=utf-8');
header('Cache-Control: public, max-age=86400');
readfile($mdFile);
