<?php
/**
 * Article Page - Nijenhuis Botenverhuur Blog
 * Renders a single blog article from Markdown, with multilingual support.
 */
require_once __DIR__ . '/../components/data_access.php';
loadEnvSafe(__DIR__ . '/../.env');
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/../lib/Parsedown.php';
require_once __DIR__ . '/../lib/blog-helpers.php';

$basePath = getBasePath();
$slug = $_GET['slug'] ?? '';
$lang = $_GET['lang'] ?? 'nl';
$allowedLangs = ['nl', 'en', 'de'];

if (!in_array($lang, $allowedLangs)) {
    $lang = 'nl';
}

if ($slug !== '' && preg_match('/^[a-z0-9\-]+$/', (string) $slug)) {
    $redirectsFile = nijenhuis_data_path('blog-redirects.json');
    if (is_file($redirectsFile)) {
        $redirects = json_decode(file_get_contents($redirectsFile), true);
        if (is_array($redirects) && isset($redirects[$slug])) {
            $newSlug = $redirects[$slug];
            // Prevent circular redirects: only redirect if target doesn't redirect back
            if ($newSlug !== $slug && (!isset($redirects[$newSlug]) || $redirects[$newSlug] !== $slug)) {
                $prefix = ($lang !== 'nl') ? '/' . $lang : '';
                header('Location: ' . $prefix . '/blog/' . $newSlug, true, 301);
                exit;
            }
        }
    }
}

$articleMeta = blogFindArticleBySlug($slug);

// Fallback: requested lang, then nl, then en, then de (first available)
$meta = null;
$contentLang = $lang;
if ($articleMeta) {
    $resolved = blogResolveArticleTranslation($articleMeta, $lang);
    if ($resolved) {
        $meta = $resolved['meta'];
        $contentLang = $resolved['lang'];
    }
}

// Allow draft preview for authenticated blog portal editors
$isDraft = ($articleMeta['published'] ?? true) === false;
$isBlogEditor = false;
if ($isDraft) {
    require_once __DIR__ . '/../admin/session-config.php';
    $isBlogEditor = !empty($_SESSION['blog_authenticated']);
}

if (!$articleMeta || !$meta || ($isDraft && !$isBlogEditor)) {
    http_response_code(404);
    $backHref = blogPublicHasPublishedArticles()
        ? (($lang !== 'nl') ? '/' . $lang : '') . '/blog'
        : (($lang !== 'nl') ? '/' . $lang . '/' : '/');
    $backLabel = blogPublicHasPublishedArticles() ? 'Back to Blog' : 'Home';
    echo '<!DOCTYPE html><html><head><title>404 - Not Found</title></head><body><h1>404 - Article Not Found</h1><p><a href="' . htmlspecialchars($backHref) . '">' . htmlspecialchars($backLabel) . '</a></p></body></html>';
    exit;
}

$articleDir = __DIR__ . '/../content/articles/' . $slug;
$mdFile = $articleDir . '/' . $contentLang . '.md';
if (!file_exists($mdFile)) {
    http_response_code(404);
    $backHref = blogPublicHasPublishedArticles()
        ? (($lang !== 'nl') ? '/' . $lang : '') . '/blog'
        : (($lang !== 'nl') ? '/' . $lang . '/' : '/');
    $backLabel = blogPublicHasPublishedArticles() ? 'Back to Blog' : 'Home';
    echo '<!DOCTYPE html><html><head><title>404 - Not Found</title></head><body><h1>404 - Article Not Found</h1><p><a href="' . htmlspecialchars($backHref) . '">' . htmlspecialchars($backLabel) . '</a></p></body></html>';
    exit;
}

$rawContent = file_get_contents($mdFile);
$frontmatter = [];
$body = $rawContent;

if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $rawContent, $fm)) {
    $body = $fm[2];
    preg_match_all('/^([a-z]+):\s*["\']?([^"\'\n]+)["\']?\s*$/m', $fm[1], $m, PREG_SET_ORDER);
    foreach ($m as $match) {
        $frontmatter[$match[1]] = trim($match[2], '"\'');
    }
}

$baseTitle = $meta['title'] ?? ($frontmatter['title'] ?? $slug);
$pageTitle = trim($meta['metaTitle'] ?? '') !== '' ? trim($meta['metaTitle']) : $baseTitle;
$pageDescription = $meta['description'] ?? ($frontmatter['description'] ?? '');
$pageKeywords = $meta['keywords'] ?? ($frontmatter['keywords'] ?? '');
$pageTitleFull = $pageTitle;

$parsedown = new Parsedown();
$parsedown->setSafeMode(true);
$html = $parsedown->text($body);
$html = normalizeBlogArticleHtmlImages($html);
$html = normalizeBlogArticleHtmlTables($html);
$html = normalizeBlogArticleHtmlLists($html);

// Hero uses H1; demote first heading in article body to H2
$html = preg_replace('/<h1(\s[^>]*)?>/i', '<h2$1>', $html, 1);
$html = preg_replace('/<\/h1>/i', '</h2>', $html, 1);

// Post-process: external links -> target="_blank" rel="noopener noreferrer"
$siteHost = parse_url(SITE_URL, PHP_URL_HOST);
$html = preg_replace_callback(
    '/<a\s+([^>]*href=["\'])(https?:\/\/[^"\']+)(["\'][^>]*)>/i',
    function ($m) use ($siteHost) {
        $hrefHost = parse_url($m[2], PHP_URL_HOST);
        $ext = ($hrefHost && $hrefHost !== $siteHost);
        $extra = $ext ? ' target="_blank" rel="noopener noreferrer"' : '';
        return '<a ' . $m[1] . $m[2] . $m[3] . $extra . '>';
    },
    $html
);

// Prefix internal blog links with lang when en/de
$langPrefix = ($contentLang !== 'nl') ? '/' . $contentLang : '';
$html = preg_replace_callback(
    '/<a\s+([^>]*href=["\'])(\/blog\/[a-z0-9\-]+)(["\'][^>]*)>/i',
    function ($m) use ($langPrefix) {
        $newHref = $langPrefix . $m[2];
        return '<a ' . $m[1] . $newHref . $m[3] . '>';
    },
    $html
);

// Add IDs to headings and extract table of contents
$tableOfContents = extractTableOfContents($html);

// Key takeaways: explicit list from CMS, else parsed from markdown
$keyTakeawaysRaw = trim($meta['keyTakeaways'] ?? '');
if ($keyTakeawaysRaw !== '') {
    $keyTakeaways = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $keyTakeawaysRaw)), function ($line) {
        return $line !== '';
    }));
} else {
    $keyTakeaways = extractKeyTakeaways($body);
}

$langPrefixUrl = ($contentLang !== 'nl') ? '/' . $contentLang : '';
$articleUrl = SITE_URL . $langPrefixUrl . '/blog/' . $slug;
$canonicalUrl = $articleUrl;
$cOverride = trim($articleMeta['canonicalUrl'] ?? '');
if ($cOverride !== '') {
    $canonicalUrl = (strpos($cOverride, 'http') === 0) ? $cOverride : rtrim(SITE_URL, '/') . '/' . ltrim($cOverride, '/');
}

$socialImageUrl = '';
$mi = trim($meta['metaImage'] ?? '');
$mi = normalizeBlogPublicImageUrl($mi);
if ($mi !== '') {
    $socialImageUrl = (strpos($mi, 'http') === 0) ? $mi : rtrim(SITE_URL, '/') . '/' . ltrim($mi, '/');
} elseif (!empty($articleMeta['featuredImage'])) {
    $fi = normalizeBlogPublicImageUrl($articleMeta['featuredImage']);
    $socialImageUrl = ($fi !== '' && strpos($fi, 'http') === 0) ? $fi : ($fi !== '' ? rtrim(SITE_URL, '/') . '/' . ltrim($fi, '/') : '');
}

$headRobots = !empty($articleMeta['noindex']) ? 'noindex, nofollow' : '';

$blogPath = $langPrefixUrl ? $langPrefixUrl . '/blog' : '/blog';
$articlePath = $blogPath . '/' . $slug;
$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Blog', 'url' => $blogPath],
    ['name' => $baseTitle, 'url' => $articlePath]
];

$hreflangLinks = [];
$translations = $articleMeta['translations'] ?? [];
$defaultHref = SITE_URL . '/blog/' . $slug;
foreach ($translations as $tlang => $tdata) {
    $tprefix = ($tlang !== 'nl') ? '/' . $tlang : '';
    $hreflangLinks[] = [
        'lang' => $tlang === 'nl' ? 'nl' : ($tlang === 'en' ? 'en' : 'de'),
        'href' => SITE_URL . $tprefix . '/blog/' . $slug
    ];
    if ($tlang === 'nl') {
        $defaultHref = SITE_URL . '/blog/' . $slug;
    }
}
array_unshift($hreflangLinks, ['lang' => 'x-default', 'href' => $defaultHref]);

$additionalStyles = ['/frontend/css/pages/blog.css'];

$blogStylesFile = nijenhuis_data_path('blog-styles.json');
$blogStyles = file_exists($blogStylesFile) ? (json_decode(file_get_contents($blogStylesFile), true) ?: []) : [];
$articleStyleOverrides = $articleMeta['styleOverrides'] ?? [];
$blogStyles = array_merge($blogStyles ?: [], is_array($articleStyleOverrides) ? $articleStyleOverrides : []);
$headerTitle = $baseTitle;
$headerTitleI18n = '';
$headerDescription = $pageDescription;
$headerDescriptionI18n = '';

$sortedBlogArticles = getPublishedBlogArticlesSorted(blogLoadRawArticlesFromData());
$relatedArticles = getRelatedBlogArticles($sortedBlogArticles, $slug, 3);
$mostReadArticles = getMostReadBlogArticles($sortedBlogArticles, 5);
$readingMinutes = estimateReadingTime($html);

$labels = [
    'nl' => [
        'toc' => 'Inhoudsopgave',
        'takeaways' => 'Belangrijkste inzichten',
        'related' => 'Gerelateerde Artikelen',
        'mostRead' => 'Meest Gelezen',
        'cta' => 'Reserveer direct een boot',
        'author' => 'Nijenhuis Team',
        'readPrefix' => 'min leestijd',
        'categoryFallback' => 'Blog',
    ],
    'en' => [
        'toc' => 'Table of contents',
        'takeaways' => 'Key takeaways',
        'related' => 'Related articles',
        'mostRead' => 'Most read',
        'cta' => 'Book a boat now',
        'author' => 'Nijenhuis Team',
        'readPrefix' => 'min read',
        'categoryFallback' => 'Blog',
    ],
    'de' => [
        'toc' => 'Inhaltsverzeichnis',
        'takeaways' => 'Wichtigste Erkenntnisse',
        'related' => 'Verwandte Artikel',
        'mostRead' => 'Meist gelesen',
        'cta' => 'Boot direkt reservieren',
        'author' => 'Nijenhuis Team',
        'readPrefix' => 'Min. Lesezeit',
        'categoryFallback' => 'Blog',
    ],
];
$L = $labels[$contentLang] ?? $labels['nl'];
$articleCategory = !empty($meta['category']) ? $meta['category'] : $L['categoryFallback'];
$bookingCtaHref = ($contentLang !== 'nl') ? '/' . $contentLang . '/botenverhuur' : '/botenverhuur';

$articlePublishedTime = trim($meta['date'] ?? '');
$articleModifiedTime = '';
if (file_exists($mdFile)) {
    $articleModifiedTime = date('Y-m-d', filemtime($mdFile));
}
if ($articleModifiedTime === '') {
    $articleModifiedTime = $articlePublishedTime;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $contentLang === 'nl' ? 'nl' : ($contentLang === 'en' ? 'en' : 'de'); ?>">
<?php
$hreflangLinks = $hreflangLinks;
$ogType = 'article';
$ogLocale = match ($contentLang) {
    'en' => 'en_US',
    'de' => 'de_DE',
    default => 'nl_NL',
};
include __DIR__ . '/../components/head.php';
?>

<body class="blog-article-page">
<?php
$_articleDateModified = $articleModifiedTime;
?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "<?php echo htmlspecialchars($baseTitle); ?>",
    "description": "<?php echo htmlspecialchars($pageDescription); ?>",
    "datePublished": "<?php echo htmlspecialchars($meta['date'] ?? ''); ?>",
    "dateModified": "<?php echo htmlspecialchars($_articleDateModified ?: ($meta['date'] ?? '')); ?>",
    "url": "<?php echo htmlspecialchars($canonicalUrl); ?>",
    "inLanguage": "<?php echo htmlspecialchars($contentLang); ?>",
    <?php if ($socialImageUrl !== ''): ?>
    "image": "<?php echo htmlspecialchars($socialImageUrl); ?>",
    <?php endif; ?>
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?php echo htmlspecialchars($canonicalUrl); ?>"
    },
    "author": {
        "@type": "Organization",
        "name": "<?php echo SITE_NAME; ?>",
        "url": "<?php echo SITE_URL; ?>"
    },
    "publisher": {
        "@type": "Organization",
        "name": "<?php echo SITE_NAME; ?>",
        "url": "<?php echo SITE_URL; ?>",
        "logo": {
            "@type": "ImageObject",
            "url": "<?php echo SITE_URL; ?>/frontend/Images/logo-white.svg"
        }
    }
}
</script>
<?php
require_once __DIR__ . '/../components/schema-blog-faq-ld.php';
$blogFaqSchema = schema_blog_faq_ld($slug);
if ($blogFaqSchema !== null):
?>
<script type="application/ld+json"><?php echo json_encode($blogFaqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
<?php endif; ?>
<?php if (!empty($blogStyles)): ?>
    <style>
    .blog-article-content { <?php if (!empty($blogStyles['articleMaxWidth'])): ?>max-width: <?php echo htmlspecialchars($blogStyles['articleMaxWidth']); ?>;<?php endif; ?> }
    .blog-article-content a { <?php if (!empty($blogStyles['articleLinkColor'])): ?>color: <?php echo htmlspecialchars($blogStyles['articleLinkColor']); ?>;<?php endif; ?> }
    .blog-article-content h1 { <?php if (!empty($blogStyles['articleH1Size'])): ?>font-size: <?php echo htmlspecialchars($blogStyles['articleH1Size']); ?>;<?php endif; ?> }
    .blog-article-content h2 { <?php if (!empty($blogStyles['articleH2Size'])): ?>font-size: <?php echo htmlspecialchars($blogStyles['articleH2Size']); ?>;<?php endif; ?> }
    .blog-article-content h3 { <?php if (!empty($blogStyles['articleH3Size'])): ?>font-size: <?php echo htmlspecialchars($blogStyles['articleH3Size']); ?>;<?php endif; ?> }
    .blog-article-content p { <?php if (!empty($blogStyles['articleBodyLineHeight'])): ?>line-height: <?php echo htmlspecialchars($blogStyles['articleBodyLineHeight']); ?>;<?php endif; if (!empty($blogStyles['articleTextColor'])): ?> color: <?php echo htmlspecialchars($blogStyles['articleTextColor']); ?>;<?php endif; ?> }
    .blog-article-content blockquote { <?php if (!empty($blogStyles['articleBlockquoteBg'])): ?>background: <?php echo htmlspecialchars($blogStyles['articleBlockquoteBg']); ?>;<?php endif; ?> }
    </style>
<?php endif; ?>
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php $showBreadcrumb = false; include __DIR__ . '/../components/breadcrumb.php'; ?>

    <main class="blog-article-main">
        <?php $featuredImage = normalizeBlogPublicImageUrl($articleMeta['featuredImage'] ?? null); ?>
        <section class="blog-hero" data-purpose="hero-section" aria-label="<?php echo htmlspecialchars($baseTitle); ?>">
            <?php if ($featuredImage !== ''): ?>
            <?php echo responsiveImage(
                ltrim($featuredImage, '/'),
                $baseTitle,
                '100vw',
                ['class' => 'blog-hero-image', 'loading' => 'eager']
            ); ?>
            <?php endif; ?>
            <div class="blog-hero-overlay">
                <div class="blog-hero-inner">
                    <h1 class="blog-hero-title"><?php echo htmlspecialchars($baseTitle); ?></h1>
                    <div class="blog-hero-meta">
                        <?php if (!empty($meta['date'])): ?>
                        <time datetime="<?php echo htmlspecialchars($meta['date']); ?>"><?php echo formatBlogDate($meta['date'], $contentLang); ?></time>
                        <span class="blog-hero-meta-sep" aria-hidden="true">|</span>
                        <?php endif; ?>
                        <span><?php echo htmlspecialchars($articleCategory); ?></span>
                        <span class="blog-hero-meta-sep" aria-hidden="true">|</span>
                        <span><?php echo (int) $readingMinutes . ' ' . htmlspecialchars($L['readPrefix']); ?></span>
                        <span class="blog-hero-meta-sep" aria-hidden="true">|</span>
                        <span><?php echo htmlspecialchars($L['author']); ?></span>
                        <?php if (count($translations) > 1): ?>
                        <span class="blog-hero-meta-sep blog-hero-meta-sep-lang" aria-hidden="true">|</span>
                        <span class="blog-hero-lang">
                            <?php foreach ($translations as $tlang => $tdata): ?>
                            <?php if ($tlang !== $contentLang): ?>
                            <?php $tprefix = ($tlang !== 'nl') ? '/' . $tlang : ''; ?>
                            <a href="<?php echo htmlspecialchars($tprefix . '/blog/' . $slug); ?>" hreflang="<?php echo htmlspecialchars($tlang); ?>"><?php echo $tlang === 'nl' ? 'NL' : ($tlang === 'en' ? 'EN' : 'DE'); ?></a>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="blog-content-area" data-purpose="main-content-area">
            <div class="blog-content-container">
                <article class="blog-main-column" data-purpose="article-content">
                    <?php if (!empty($tableOfContents)): ?>
                    <nav class="blog-toc blog-toc-card" aria-label="<?php echo htmlspecialchars($L['toc']); ?>">
                        <h3 class="blog-toc-heading">
                            <svg class="blog-toc-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            <?php echo htmlspecialchars($L['toc']); ?>
                        </h3>
                        <ul class="blog-toc-list">
                            <?php foreach ($tableOfContents as $t): ?>
                            <li class="blog-toc-item blog-toc-level-<?php echo (int) $t['level']; ?>">
                                <a href="#<?php echo htmlspecialchars($t['id']); ?>">
                                    <span class="blog-toc-bullet" aria-hidden="true"></span>
                                    <?php echo htmlspecialchars($t['text']); ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>

                    <?php if (!empty($keyTakeaways)): ?>
                    <div class="blog-key-takeaways blog-key-takeaways-card" data-purpose="key-takeaways">
                        <h3 class="blog-takeaways-heading">
                            <svg class="blog-takeaways-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0012 18.75c-1.03 0-1.9-.4-2.593-1.003l-.548-.547z"/></svg>
                            <?php echo htmlspecialchars($L['takeaways']); ?>
                        </h3>
                        <ul class="blog-takeaways-grid">
                            <?php foreach ($keyTakeaways as $tk): ?>
                            <li class="blog-takeaways-item">
                                <span class="blog-takeaways-check" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                </span>
                                <span><?php echo formatBlogTakeawayLineHtml($tk); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <div class="blog-article-content content-prose blog-article-prose">
                        <?php echo $html; ?>
                    </div>
                </article>

                <aside class="blog-sidebar" data-purpose="sidebar">
                    <?php if (!empty($relatedArticles)): ?>
                    <div class="blog-sidebar-block" data-purpose="related-articles-widget">
                        <h3 class="blog-sidebar-title"><?php echo htmlspecialchars($L['related']); ?></h3>
                        <div class="blog-related-list">
                            <?php foreach ($relatedArticles as $ra): ?>
                            <?php
                            $rm = $ra['translations'][$contentLang] ?? $ra['translations']['nl'] ?? [];
                            $rTitle = $rm['title'] ?? $ra['slug'];
                            $rDate = $rm['date'] ?? '';
                            $rImg = normalizeBlogPublicImageUrl($ra['featuredImage'] ?? null);
                            $rUrl = $langPrefixUrl . '/blog/' . $ra['slug'];
                            ?>
                            <a class="blog-related-item" href="<?php echo htmlspecialchars($rUrl); ?>">
                                <?php if ($rImg !== ''): ?>
                                <?php echo responsiveImage(
                                    ltrim($rImg, '/'),
                                    $rTitle,
                                    '80px',
                                    ['class' => 'blog-related-thumb', 'width' => '80', 'height' => '60'],
                                    [400]
                                ); ?>
                                <?php else: ?>
                                <span class="blog-related-thumb blog-related-thumb-placeholder" aria-hidden="true"></span>
                                <?php endif; ?>
                                <div class="blog-related-text">
                                    <h4 class="blog-related-heading"><?php echo htmlspecialchars($rTitle); ?></h4>
                                    <?php if ($rDate): ?>
                                    <p class="blog-related-date"><?php echo htmlspecialchars(formatBlogDate($rDate, $contentLang)); ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($mostReadArticles)): ?>
                    <div class="blog-sidebar-block" data-purpose="most-read-widget">
                        <h3 class="blog-sidebar-title"><?php echo htmlspecialchars($L['mostRead']); ?></h3>
                        <ul class="blog-most-read-list">
                            <?php foreach ($mostReadArticles as $ma): ?>
                            <?php
                            $mm = $ma['translations'][$contentLang] ?? $ma['translations']['nl'] ?? [];
                            $mTitle = $mm['title'] ?? $ma['slug'];
                            $mUrl = $langPrefixUrl . '/blog/' . $ma['slug'];
                            ?>
                            <li class="blog-most-read-item">
                                <span class="blog-most-read-bullet" aria-hidden="true">•</span>
                                <a href="<?php echo htmlspecialchars($mUrl); ?>"><?php echo htmlspecialchars($mTitle); ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <div class="blog-sidebar-cta" data-purpose="cta-widget">
                        <a class="blog-cta-button" href="<?php echo htmlspecialchars($bookingCtaHref); ?>"><?php echo htmlspecialchars($L['cta']); ?></a>
                    </div>
                </aside>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>
    <script>
    (function () {
        function revealAnchorList(list) {
            if (!list || list.classList.contains('is-revealed')) return;
            list.classList.add('is-revealed');
        }
        function initBlogAnchorLists() {
            var lists = document.querySelectorAll('.blog-article-prose .anchor-list, .blog-article-content .anchor-list');
            if (!lists.length) return;
            if (!('IntersectionObserver' in window)) {
                lists.forEach(revealAnchorList);
                return;
            }
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        revealAnchorList(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });
            lists.forEach(function (list) { observer.observe(list); });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initBlogAnchorLists);
        } else {
            initBlogAnchorLists();
        }
    })();
    </script>
</body>
</html>
