<?php
/**
 * Router for PHP Built-in Server
 * Handles clean URL routing for local development
 * 
 * This router maps clean URLs (like /botenverhuur) to actual PHP files
 * (like /pages/botenverhuur.php) to match production nginx configuration
 */

// Block direct access to JSON files (match production nginx)
$parsedUrlEarly = parse_url($_SERVER['REQUEST_URI'] ?? '/');
if (preg_match('/\.json$/i', $parsedUrlEarly['path'] ?? '')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Forbidden';
    return true;
}

require_once __DIR__ . '/components/data_access.php';
if (file_exists(__DIR__ . '/.env')) {
    loadEnvSafe(__DIR__ . '/.env');
}

// Get the requested URI
$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];

// Remove leading/trailing slashes
$path = trim($path, '/');

// If path is empty, serve index page
if (empty($path)) {
    $indexFile = __DIR__ . '/pages/index.php';
    if (file_exists($indexFile)) {
        $_SERVER['SCRIPT_NAME'] = '/pages/index.php';
        require $indexFile;
        return true;
    }
}

// If it's already a file that exists, serve it directly (blog API is required from router below for reliable POST/uploads)
$filePath = __DIR__ . ($parsedUrl['path'] ?? '');
$blogPortalApiPath = isset($parsedUrl['path']) && preg_match('#/blog-portal/api\\.php$#', (string) $parsedUrl['path']) === 1;
if (file_exists($filePath) && is_file($filePath) && !$blogPortalApiPath) {
    return false; // Let PHP server handle it
}

// Serve humans.txt
if ($path === 'humans.txt') {
    $humansFile = __DIR__ . '/humans.txt';
    if (file_exists($humansFile)) {
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: public, max-age=86400');
        readfile($humansFile);
        exit;
    }
}

// Serve /.well-known/security.txt
if ($path === '.well-known/security.txt') {
    $secFile = __DIR__ . '/.well-known/security.txt';
    if (file_exists($secFile)) {
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: public, max-age=86400');
        readfile($secFile);
        exit;
    }
}

// Serve llms.txt (AI crawler index) — with per-language variants
if (preg_match('/^(?:(en|de)\/)?llms\.txt$/', $path, $llmsMatch)) {
    $llmsLang = $llmsMatch[1] ?? 'nl';
    $llmsFile = ($llmsLang === 'nl')
        ? __DIR__ . '/llms.txt'
        : __DIR__ . '/llms-' . $llmsLang . '.txt';
    if (file_exists($llmsFile)) {
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: public, max-age=86400');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($llmsFile)));
        readfile($llmsFile);
        exit;
    }
}

// Serve brand-specific llms file for AI crawlers.
if ($path === 'llms-nijenhuis.txt') {
    $llmsFile = __DIR__ . '/llms-nijenhuis.txt';
    if (file_exists($llmsFile)) {
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: public, max-age=86400');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($llmsFile)));
        readfile($llmsFile);
        exit;
    }
}

// Serve llms-full.txt (full content for AI crawlers) — with per-language variants
if (preg_match('/^(?:(en|de)\/)?llms-full\.txt$/', $path, $llmsFullMatch)) {
    $_GET['lang'] = $llmsFullMatch[1] ?? 'nl';
    require __DIR__ . '/pages/llms-full.php';
    exit;
}

// Serve per-page markdown files (e.g. /giethoorn.md)
if (preg_match('/^([a-z0-9][a-z0-9\-]*)\.md$/', $path, $matches)) {
    $_GET['page'] = $matches[1];
    require __DIR__ . '/pages/serve-markdown.php';
    exit;
}

// Blog: en/blog, de/blog, en/blog/slug, de/blog/slug
if (preg_match('/^(en|de)\/blog(?:\/([a-z0-9\-]+))?$/u', $path, $m)) {
    $reqSlug = $m[2] ?? '';
    if ($reqSlug) {
        $redirectsFile = nijenhuis_data_path('blog-redirects.json');
        if (file_exists($redirectsFile)) {
            $redirects = json_decode(file_get_contents($redirectsFile), true);
            if (isset($redirects[$reqSlug])) {
                $target = $redirects[$reqSlug];
                if ($target !== $reqSlug && (!isset($redirects[$target]) || $redirects[$target] !== $reqSlug)) {
                    header('Location: /' . $m[1] . '/blog/' . $target, true, 301);
                    exit;
                }
            }
        }
    }
    $_GET['lang'] = $m[1];
    $_GET['slug'] = $reqSlug;
    require __DIR__ . '/pages/' . (!empty($reqSlug) ? 'article.php' : 'blog.php');
    return true;
}

// Blog: blog, blog/slug
if ($path === 'blog' || preg_match('/^blog\/([a-z0-9\-]+)$/u', $path, $m)) {
    $reqSlug = isset($m[1]) ? $m[1] : '';
    if ($reqSlug) {
        $redirectsFile = nijenhuis_data_path('blog-redirects.json');
        if (file_exists($redirectsFile)) {
            $redirects = json_decode(file_get_contents($redirectsFile), true);
            if (isset($redirects[$reqSlug])) {
                $target = $redirects[$reqSlug];
                if ($target !== $reqSlug && (!isset($redirects[$target]) || $redirects[$target] !== $reqSlug)) {
                    header('Location: /blog/' . $target, true, 301);
                    exit;
                }
            }
        }
    }
    $_GET['lang'] = 'nl';
    $_GET['slug'] = $reqSlug;
    require __DIR__ . '/pages/' . ($reqSlug ? 'article.php' : 'blog.php');
    return true;
}

// Serve blog article markdown for AI crawlers (e.g. /blog/slug.md or /en/blog/slug.md)
if (preg_match('/^(?:(en|de)\/)?blog\/([a-z0-9\-]+)\.md$/u', $path, $m)) {
    $lang = $m[1] ?? 'nl';
    $slug = $m[2];
    $articleDir = __DIR__ . '/content/articles/' . $slug;
    $mdFile = $articleDir . '/' . $lang . '.md';
    if (!file_exists($mdFile) && file_exists($articleDir . '/nl.md')) {
        $mdFile = $articleDir . '/nl.md';
    }
    if (file_exists($mdFile)) {
        header('Content-Type: text/markdown; charset=utf-8');
        header('Cache-Control: public, max-age=86400');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($mdFile)));
        readfile($mdFile);
        exit;
    }
}

// Blog portal (separate login from admin/employee)
if (preg_match('/^blog-portal\/?(.*)$/', $path, $m)) {
    require_once __DIR__ . '/blog-portal/portal-headers.php';
    $sub = $m[1] ?? '';
    if ($sub === 'api.php') {
        require __DIR__ . '/blog-portal/api.php';
        return true;
    }
    if ($sub === '' || $sub === 'login') {
        require __DIR__ . '/blog-portal/login.php';
        return true;
    }
    if ($sub === 'dashboard') {
        require __DIR__ . '/blog-portal/dashboard.php';
        return true;
    }
    if ($sub === 'logout') {
        require __DIR__ . '/blog-portal/logout.php';
        return true;
    }
    if ($sub === 'style') {
        require __DIR__ . '/blog-portal/style-editor.php';
        return true;
    }
    if ($sub === 'article/new') {
        $_GET['slug'] = 'new';
        require __DIR__ . '/blog-portal/article-edit.php';
        return true;
    }
    if (preg_match('/^article\/edit\/([a-z0-9\-]+)$/', $sub, $sm)) {
        $_GET['slug'] = $sm[1];
        require __DIR__ . '/blog-portal/article-edit.php';
        return true;
    }
}

// Map clean URLs to actual PHP files
$routeMap = [
    'botenverhuur' => 'pages/botenverhuur.php',
    'vakantiehuis' => 'pages/vakantiehuis.php',
    'te-koop' => 'pages/te-koop.php',
    'camping' => 'pages/camping.php',
    'vaarkaart' => 'pages/vaarkaart.php',
    'contact' => 'pages/contact.php',
    'booking' => 'pages/booking.php',
    'checkout' => 'pages/checkout.php',
    'giethoorn' => 'pages/giethoorn.php',
    'weerribben' => 'pages/weerribben.php',
    'belt-schutsloot' => 'pages/belt-schutsloot.php',
    'wanneperveen' => 'pages/wanneperveen.php',
    'veelgestelde-vragen' => 'pages/veelgestelde-vragen.php',
    'tarieven' => 'pages/tarieven.php',
    'admin-login' => 'pages/admin-login.php',
    'login' => 'pages/employee-login.php',
    'employee-login' => 'pages/employee-login.php',
    'employee-portal' => 'pages/employee-portal.php',
    'payment-success' => 'pages/payment-success.php',
    'payment-failure' => 'pages/payment-failure.php',
    'offline' => 'pages/offline.php',
];

// Check if the path matches a route
if (isset($routeMap[$path])) {
    $targetFile = __DIR__ . '/' . $routeMap[$path];
    if (file_exists($targetFile)) {
        require $targetFile;
        return true;
    }
}

// Check if it's a pages/ path
if (strpos($path, 'pages/') === 0) {
    $filePath = __DIR__ . '/' . $path;
    if (file_exists($filePath) && is_file($filePath)) {
        require $filePath;
        return true;
    }
}

// Check if it's an admin path
if (strpos($path, 'admin/') === 0) {
    $filePath = __DIR__ . '/' . $path;
    if (file_exists($filePath) && is_file($filePath)) {
        require $filePath;
        return true;
    }
}

// If no match, try to serve as static file or return 404
$filePath = __DIR__ . $parsedUrl['path'];
if (file_exists($filePath)) {
    return false; // Let PHP server handle static files
}

// Check if path matches a known boat ID
$boatsFile = nijenhuis_data_path('boats.json');
if (file_exists($boatsFile)) {
    $boatsData = json_decode(file_get_contents($boatsFile), true);
    if (is_array($boatsData)) {
        foreach ($boatsData as $boat) {
            if (isset($boat['id']) && $path === $boat['id']) {
                $_SERVER['SCRIPT_NAME'] = '/pages/index.php';
                require __DIR__ . '/pages/index.php';
                return true;
            }
        }
    }
}

// 404 - Page not found
http_response_code(404);
echo '<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Pagina niet gevonden - 404</title>
    <link rel="stylesheet" href="/frontend/css/styles.css">
    <style>
        .error-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
            padding: 2rem 1rem;
        }
        .error-page__code {
            font-size: 6rem;
            font-weight: 800;
            line-height: 1;
            color: var(--color-primary, #1a5276);
            margin: 0;
        }
        .error-page__heading {
            font-size: 1.75rem;
            margin: 0.5rem 0 1rem;
            color: var(--color-heading, #222);
        }
        .error-page__message {
            font-size: 1.1rem;
            color: var(--color-text-muted, #555);
            max-width: 480px;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }
        .error-page__home {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: var(--color-primary, #1a5276);
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: background 0.2s;
            margin-bottom: 2.5rem;
        }
        .error-page__home:hover {
            background: var(--color-primary-dark, #124163);
        }
        .error-page__links {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }
        .error-page__links a {
            color: var(--color-primary, #1a5276);
            text-decoration: none;
            font-weight: 500;
        }
        .error-page__links a:hover {
            text-decoration: underline;
        }
        .error-page__links-label {
            width: 100%;
            font-size: 0.95rem;
            color: var(--color-text-muted, #555);
            margin-bottom: 0.25rem;
        }
    </style>
</head>
<body>
    <main class="error-page">
        <p class="error-page__code">404</p>
        <h1 class="error-page__heading">Pagina niet gevonden</h1>
        <p class="error-page__message">
            Sorry, de pagina die u zoekt bestaat niet of is verplaatst.
            Controleer de URL of ga terug naar de homepage.
        </p>
        <a href="/" class="error-page__home">Terug naar homepage</a>
        <nav aria-label="Populaire pagina\'s">
            <p class="error-page__links-label">Misschien zoekt u een van deze pagina\'s:</p>
            <ul class="error-page__links">
                <li><a href="/botenverhuur">Botenverhuur</a></li>
                <li><a href="/contact">Contact</a></li>
                <li><a href="/veelgestelde-vragen">Veelgestelde vragen</a></li>
            </ul>
        </nav>
    </main>
</body>
</html>';
return true;
