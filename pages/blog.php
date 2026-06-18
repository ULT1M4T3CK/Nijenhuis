<?php
/**
 * Blog Index Page - Nijenhuis Botenverhuur
 * Lists all blog articles with multilingual support.
 */
require_once __DIR__ . '/../components/data_access.php';
loadEnvSafe(__DIR__ . '/../.env');
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/../lib/blog-helpers.php';

$basePath = getBasePath();
$lang = $_GET['lang'] ?? 'nl';
$allowedLangs = ['nl', 'en', 'de'];

if (!in_array($lang, $allowedLangs)) {
    $lang = 'nl';
}

$articles = getPublishedBlogArticlesSorted(blogLoadRawArticlesFromData());
if (count($articles) === 0) {
    $home = ($lang !== 'nl') ? '/' . $lang . '/' : '/';
    header('Location: ' . $home, true, 302);
    exit;
}

$perPage = 10;
$totalArticles = count($articles);
$totalPages = max(1, (int) ceil($totalArticles / $perPage));
$currentPage = max(1, min($totalPages, (int) ($_GET['page'] ?? 1)));
$offset = ($currentPage - 1) * $perPage;
$articlesPage = array_slice($articles, $offset, $perPage);

$langPrefix = ($lang !== 'nl') ? '/' . $lang : '';
$blogUrl = SITE_URL . $langPrefix . '/blog';
$canonicalUrl = $currentPage > 1 ? $blogUrl . '?page=' . $currentPage : $blogUrl;

$pageTitle = 'Blog';
$pageTitleFull = 'Blog - ' . SITE_NAME;
$pageDescription = $lang === 'nl' ? 'Lees onze artikelen over bootverhuur in Giethoorn, de Weerribben en alles wat met varen te maken heeft.' : ($lang === 'en' ? 'Read our articles about boat rental in Giethoorn, the Weerribben and everything related to sailing.' : 'Lesen Sie unsere Artikel über Bootsverleih in Giethoorn, die Weerribben und alles, was mit Segeln zu tun hat.');
$pageKeywords = 'blog, bootverhuur, giethoorn, weerribben, boot huren';

$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Blog', 'url' => $langPrefix ? $langPrefix . '/blog' : '/blog']
];

$hreflangLinks = [
    ['lang' => 'x-default', 'href' => SITE_URL . '/blog'],
    ['lang' => 'nl', 'href' => SITE_URL . '/blog'],
    ['lang' => 'en', 'href' => SITE_URL . '/en/blog'],
    ['lang' => 'de', 'href' => SITE_URL . '/de/blog']
];

$additionalStyles = ['/frontend/css/pages/blog.css'];
$headerTitle = 'Blog';
$headerTitleI18n = 'nav_blog';
$headerDescription = $pageDescription;
$headerDescriptionI18n = '';

$langLabels = ['nl' => 'Nederlands', 'en' => 'English', 'de' => 'Deutsch'];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang === 'nl' ? 'nl' : ($lang === 'en' ? 'en' : 'de'); ?>">
<?php include __DIR__ . '/../components/head.php'; ?>

<body class="blog-index-page">
    <?php include __DIR__ . '/../components/gtm-body.php'; ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Blog",
    "name": "Blog - <?php echo SITE_NAME; ?>",
    "description": "<?php echo htmlspecialchars($pageDescription); ?>",
    "url": "<?php echo htmlspecialchars($canonicalUrl); ?>",
    "publisher": {
        "@type": "Organization",
        "name": "<?php echo SITE_NAME; ?>",
        "url": "<?php echo SITE_URL; ?>"
    },
    "blogPost": [
        <?php
        $posts = [];
        foreach ($articlesPage as $a) {
            $t = $a['translations'][$lang] ?? $a['translations']['nl'] ?? [];
            $postUrl = SITE_URL . $langPrefix . '/blog/' . $a['slug'];
            $posts[] = json_encode([
                '@type' => 'BlogPosting',
                'headline' => $t['title'] ?? $a['slug'],
                'datePublished' => $t['date'] ?? '',
                'url' => $postUrl
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        echo implode(",\n        ", $posts);
        ?>
    ]
}
</script>
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>
    <?php include __DIR__ . '/../components/breadcrumb.php'; ?>
    <?php include __DIR__ . '/../components/page-header.php'; ?>

    <main>
        <section class="content-section">
            <div class="container">
                <div class="blog-article-list">
                    <?php foreach ($articlesPage as $a): ?>
                    <?php
                    $meta = $a['translations'][$lang] ?? $a['translations']['nl'] ?? [];
                    $articleUrl = $langPrefix . '/blog/' . $a['slug'];
                    $title = $meta['title'] ?? $a['slug'];
                    $description = isset($meta['excerpt']) && trim((string) $meta['excerpt']) !== ''
                        ? trim($meta['excerpt'])
                        : ($meta['description'] ?? '');
                    $date = $meta['date'] ?? '';
                    $featuredImage = normalizeBlogPublicImageUrl($a['featuredImage'] ?? null);
                    ?>
                    <article class="blog-article-card">
                        <?php if ($featuredImage !== ''): ?>
                        <a href="<?php echo htmlspecialchars($articleUrl); ?>" class="blog-article-card-image">
                            <img src="<?php echo htmlspecialchars($featuredImage); ?>" alt="<?php echo htmlspecialchars($title); ?>" loading="lazy" decoding="async">
                        </a>
                        <?php endif; ?>
                        <h2 class="blog-article-title"><a href="<?php echo htmlspecialchars($articleUrl); ?>"><?php echo htmlspecialchars($title); ?></a></h2>
                        <?php if ($date): ?>
                        <time class="blog-article-date" datetime="<?php echo htmlspecialchars($date); ?>"><?php echo formatBlogDate($date, $lang); ?></time>
                        <?php endif; ?>
                        <p class="blog-article-excerpt"><?php echo htmlspecialchars($description); ?></p>
                        <a href="<?php echo htmlspecialchars($articleUrl); ?>" class="blog-article-link"><?php echo $lang === 'nl' ? 'Lees meer' : ($lang === 'en' ? 'Read more' : 'Weiterlesen'); ?> →</a>
                    </article>
                    <?php endforeach; ?>

                    <?php if (empty($articlesPage)): ?>
                    <p class="blog-empty"><?php echo $lang === 'nl' ? 'Er zijn nog geen artikelen.' : ($lang === 'en' ? 'No articles yet.' : 'Noch keine Artikel.'); ?></p>
                    <?php endif; ?>

                    <?php if ($totalPages > 1): ?>
                    <nav class="blog-pagination" aria-label="Pagination">
                        <ul>
                            <?php if ($currentPage > 1): ?>
                            <li><a href="<?php echo $langPrefix ? $langPrefix . '/blog' : '/blog'; ?>?page=<?php echo $currentPage - 1; ?>"><?php echo $lang === 'nl' ? '← Vorige' : ($lang === 'en' ? '← Previous' : '← Zurück'); ?></a></li>
                            <?php endif; ?>
                            <li class="blog-pagination-info"><?php echo $lang === 'nl' ? 'Pagina ' : ($lang === 'en' ? 'Page ' : 'Seite '); ?><?php echo $currentPage; ?> <?php echo $lang === 'nl' ? 'van' : ($lang === 'en' ? 'of' : 'von'); ?> <?php echo $totalPages; ?></li>
                            <?php if ($currentPage < $totalPages): ?>
                            <li><a href="<?php echo $langPrefix ? $langPrefix . '/blog' : '/blog'; ?>?page=<?php echo $currentPage + 1; ?>"><?php echo $lang === 'nl' ? 'Volgende →' : ($lang === 'en' ? 'Next →' : 'Weiter →'); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
