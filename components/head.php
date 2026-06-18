<?php
/**
 * HEAD Component - Common <head> section for all pages
 * 
 * Required variables:
 * - $pageTitle: Page title (will be appended to site name)
 * - $pageDescription: Meta description for the page
 * 
 * Optional variables:
 * - $pageTitleFull: When set, used as the complete page title (no site name appended)
 * - $pageKeywords: Meta keywords
 * - $additionalStyles: Array of additional CSS files to include
 * - $additionalScripts: Array of additional JS files to include in head
 * - $basePath: Base path for assets (defaults to calculated value)
 * - $canonicalUrl: Full canonical URL (defaults to current page)
 */

// Default base path (for page links; assets use assetPath() for clean URLs)
$basePath = $basePath ?? getBasePath();
$headRobots = $headRobots ?? '';
$socialImageUrl = $socialImageUrl ?? '';
$ogType = $ogType ?? 'website';
$ogLocale = $ogLocale ?? 'nl_NL';
$headTitle = !empty($pageTitleFull) ? $pageTitleFull : $pageTitle . ' - ' . SITE_NAME;
$headSocialImageUrl = !empty($socialImageUrl) ? $socialImageUrl : (SITE_URL . assetPath('frontend/Images/banner-img.jpg'));

// ---------------------------------------------------------------------------
// Content Security Policy (see components/csp.php)
// ---------------------------------------------------------------------------
require_once __DIR__ . '/csp.php';
// Emit HTTP Link header pointing at the .md alternate for AI crawlers
if (!headers_sent()) {
    $_mdAlternateUrl = ($canonicalUrl ?? $currentUrl ?? '');
    if ($_mdAlternateUrl !== '' && !preg_match('/\.(md|txt|json|php)$/', $_mdAlternateUrl)) {
        $_mdPath = parse_url($_mdAlternateUrl, PHP_URL_PATH);
        $_mdPath = rtrim($_mdPath, '/');
        if ($_mdPath === '' || $_mdPath === '/') {
            $_mdPath = '/index';
        }
        header('Link: <' . SITE_URL . $_mdPath . '.md>; rel="alternate"; type="text/markdown"', false);
    }
}

if (!headers_sent()) {
    nijenhuis_send_csp_header();
}

// Generate canonical URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$rawPath = strtok($_SERVER['REQUEST_URI'], '?');
$cleanPath = preg_replace('#^/pages/([a-z0-9_-]+)\.php$#i', '/$1', $rawPath);
$currentUrl = SITE_URL . $cleanPath;
$canonicalUrl = $canonicalUrl ?? $currentUrl;

// Auto-generate hreflang for main site when not set (blog/article set it explicitly)
if (empty($hreflangLinks)) {
  $path = strtok($_SERVER['REQUEST_URI'], '?');
  $baseUrl = defined('SITE_URL') ? SITE_URL : 'https://nijenhuis-botenverhuur.com';
  $sep = (strpos($path, '?') === false) ? '?' : '&';
  $hreflangLinks = [
    ['lang' => 'x-default', 'href' => $baseUrl . $path],
    ['lang' => 'nl', 'href' => $baseUrl . $path],
    ['lang' => 'en', 'href' => $baseUrl . $path . $sep . 'lang=en'],
    ['lang' => 'de', 'href' => $baseUrl . $path . $sep . 'lang=de'],
  ];
}
?>
<head>
    <?php include __DIR__ . '/gtag.php'; ?>
    <meta charset="UTF-8">
    <script>
    (function(){var i=new Image();i.onload=function(){if(i.width>0)document.documentElement.classList.add('webp')};i.onerror=function(){document.documentElement.classList.add('no-webp')};i.src='data:image/webp;base64,UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoBAAEAAQAcJaQAA3AA/v3AgAA='})();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0071BB">
    <title><?php echo htmlspecialchars($headTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <?php if (!empty($headRobots)): ?>
    <meta name="robots" content="<?php echo htmlspecialchars($headRobots); ?>">
    <?php else: ?>
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <?php endif; ?>
    <?php if (!empty($pageKeywords)): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords); ?>">
    <?php endif; ?>

    <!-- Geo meta for location-aware AI and search engines -->
    <meta name="geo.region" content="NL-OV">
    <meta name="geo.placename" content="Wanneperveen">
    <meta name="geo.position" content="<?php echo SITE_LAT; ?>;<?php echo SITE_LONG; ?>">
    <meta name="ICBM" content="<?php echo SITE_LAT; ?>, <?php echo SITE_LONG; ?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">

    <?php
    // Markdown alternate for AI crawlers
    $_headMdPath = parse_url($canonicalUrl, PHP_URL_PATH);
    $_headMdPath = rtrim($_headMdPath ?: '', '/');
    if ($_headMdPath === '' || $_headMdPath === '/') {
        $_headMdPath = '/index';
    }
    if (!preg_match('/\.(md|txt|json|php)$/', $_headMdPath)):
    ?>
    <link rel="alternate" type="text/markdown" href="<?php echo htmlspecialchars(SITE_URL . $_headMdPath . '.md'); ?>">
    <?php endif; ?>
    
    <?php if (!empty($hreflangLinks)): ?>
    <?php foreach ($hreflangLinks as $hl): ?>
    <link rel="alternate" hreflang="<?php echo htmlspecialchars($hl['lang']); ?>" href="<?php echo htmlspecialchars($hl['href']); ?>">
    <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Resource hints for speed optimization -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://www.google.com">
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://ultimaitech.com">
    <link rel="dns-prefetch" href="https://api.mollie.com">
    <link rel="dns-prefetch" href="https://js.mollie.com">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($headTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:type" content="<?php echo htmlspecialchars($ogType); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($headSocialImageUrl); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:site_name" content="<?php echo SITE_NAME; ?>">
    <meta property="og:locale" content="<?php echo htmlspecialchars($ogLocale); ?>">
    <meta property="og:locale:alternate" content="en_US">
    <meta property="og:locale:alternate" content="de_DE">
    <?php if ($ogType === 'article' && !empty($articlePublishedTime)): ?>
    <?php
    $ogPublishedIso = preg_match('/^\d{4}-\d{2}-\d{2}$/', $articlePublishedTime)
        ? $articlePublishedTime . 'T08:00:00+02:00'
        : $articlePublishedTime;
    $ogModifiedIso = preg_match('/^\d{4}-\d{2}-\d{2}$/', $articleModifiedTime ?? '')
        ? ($articleModifiedTime ?? $articlePublishedTime) . 'T08:00:00+02:00'
        : ($articleModifiedTime ?? $ogPublishedIso);
    ?>
    <meta property="article:published_time" content="<?php echo htmlspecialchars($ogPublishedIso); ?>">
    <meta property="article:modified_time" content="<?php echo htmlspecialchars($ogModifiedIso); ?>">
    <?php endif; ?>

    <!-- Twitter/X Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($headTitle); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($headSocialImageUrl); ?>">
    <meta name="twitter:image:alt" content="<?php echo htmlspecialchars($pageDescription); ?>">
    
    <!-- Favicon — same as homepage (logo-white.svg) -->
    <link rel="icon" type="image/svg+xml" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="apple-touch-icon" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="manifest" href="/frontend/public/manifest.json">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo assetPath(ltrim(PATH_STYLES, '/')); ?>" media="all">
    
    <?php if (!empty($additionalStyles)): ?>
    <?php foreach ($additionalStyles as $style): ?>
    <link rel="stylesheet" href="<?php echo assetPath(ltrim($style, '/')); ?>" media="all">
    <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Critical JavaScript (security/config only - other scripts loaded in footer) -->
    <script src="<?php echo assetPath(ltrim(PATH_JS_SECURITY, '/')); ?>" defer></script>
    <script src="<?php echo assetPath(ltrim(PATH_JS_SHARED, '/')); ?>" defer></script>

    <!-- Schema.org Structured Data - LocalBusiness / BoatRental -->
    <?php include __DIR__ . '/schema-localbusiness.php'; ?>
</head>

