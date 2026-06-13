<?php
/**
 * Serves individual page markdown files for AI crawlers.
 * Accessible at: /<page-slug>.md  (e.g. /giethoorn.md)
 *
 * The router passes the requested slug via $_GET['page'].
 */

$allowedPages = [
    'index',
    'botenverhuur',
    'vakantiehuis',
    'camping',
    'te-koop',
    'vaarkaart',
    'contact',
    'veelgestelde-vragen',
    'giethoorn',
    'weerribben',
    'belt-schutsloot',
    'wanneperveen',
];

$page = $_GET['page'] ?? '';

// Sanitise: only allow known slugs (no path traversal possible)
if (!in_array($page, $allowedPages, true)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "404 – Markdown page not found.\n";
    exit;
}

$filePath = __DIR__ . '/../markdown/' . $page . '.md';

if (!file_exists($filePath)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "404 – Markdown file not found.\n";
    exit;
}

header('Content-Type: text/markdown; charset=utf-8');
header('Cache-Control: public, max-age=86400');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($filePath)));
readfile($filePath);
