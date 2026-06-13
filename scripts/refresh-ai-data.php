<?php
/**
 * Nightly AI-data refresh orchestrator
 *
 * Cron (recommended):
 *   0 3 * * * php /path/to/scripts/refresh-ai-data.php >> /path/to/logs/ai-refresh.log 2>&1
 *
 * Tasks:
 *  1. Refresh Google Reviews → data/google-reviews.json
 *  2. Update sitemap.xml <lastmod> timestamps for pages whose markdown changed
 *  3. Regenerate blog discovery blocks in sitemap.xml and llms*.txt
 */

$root = dirname(__DIR__);
require_once $root . '/lib/blog-helpers.php';
$now = date('Y-m-d\TH:i:sP');
echo "=== AI data refresh: $now ===\n";

// ---------------------------------------------------------------------------
// 1. Google Reviews
// ---------------------------------------------------------------------------
$reviewScript = __DIR__ . '/refresh-google-reviews.php';
if (file_exists($reviewScript)) {
    echo "[1/3] Refreshing Google Reviews...\n";

    $apiKey = getenv('GOOGLE_PLACES_API_KEY') ?: '';
    if ($apiKey === '' && file_exists($root . '/.env')) {
        require_once $root . '/components/data_access.php';
        loadEnvSafe($root . '/.env');
        $apiKey = getenv('GOOGLE_PLACES_API_KEY') ?: ($_ENV['GOOGLE_PLACES_API_KEY'] ?? '');
    }

    if ($apiKey !== '') {
        $output = [];
        $code = 0;
        exec('php ' . escapeshellarg($reviewScript) . ' 2>&1', $output, $code);
        echo implode("\n", $output) . "\n";
        if ($code !== 0) {
            echo "  WARNING: review fetch exited with code $code\n";
        }
    } else {
        echo "  SKIPPED: GOOGLE_PLACES_API_KEY not set in .env\n";
    }
} else {
    echo "[1/3] SKIPPED: $reviewScript not found\n";
}

// ---------------------------------------------------------------------------
// 2. Update sitemap.xml lastmod for changed pages
// ---------------------------------------------------------------------------
echo "[2/3] Updating sitemap.xml lastmod timestamps...\n";

$sitemapFile = $root . '/sitemap.xml';
if (!file_exists($sitemapFile)) {
    echo "  SKIPPED: sitemap.xml not found\n";
    exit(0);
}

$markdownDir = $root . '/markdown/';
$sitemap = file_get_contents($sitemapFile);
$changed = false;

$slugs = [
    'index' => '/',
    'botenverhuur' => '/botenverhuur',
    'vakantiehuis' => '/vakantiehuis',
    'camping' => '/camping',
    'te-koop' => '/te-koop',
    'vaarkaart' => '/vaarkaart',
    'contact' => '/contact',
    'veelgestelde-vragen' => '/veelgestelde-vragen',
    'giethoorn' => '/giethoorn',
    'belt-schutsloot' => '/belt-schutsloot',
    'wanneperveen' => '/wanneperveen',
];

foreach ($slugs as $slug => $urlPath) {
    $mdFile = $markdownDir . $slug . '.md';
    if (!file_exists($mdFile)) {
        continue;
    }
    $newDate = date('Y-m-d', filemtime($mdFile));
    $locUrl = 'https://nijenhuis-botenverhuur.com' . $urlPath;
    $escapedLoc = preg_quote($locUrl, '#');

    $pattern = '#(<loc>' . $escapedLoc . '</loc>.*?<lastmod>)(\d{4}-\d{2}-\d{2})(</lastmod>)#s';
    $sitemap = preg_replace_callback($pattern, function ($m) use ($newDate, &$changed) {
        if ($m[2] !== $newDate) {
            $changed = true;
        }
        return $m[1] . $newDate . $m[3];
    }, $sitemap);
}

if ($changed) {
    file_put_contents($sitemapFile, $sitemap);
    echo "  Sitemap updated with new lastmod dates.\n";
} else {
    echo "  Sitemap unchanged.\n";
}

// ---------------------------------------------------------------------------
// 3. Regenerate blog discovery blocks (sitemap + llms index files)
// ---------------------------------------------------------------------------
echo "[3/3] Regenerating blog SEO/AI discovery blocks...\n";

$articles = blogLoadRawArticlesFromData();
$blogSitemapBlock = blogBuildSitemapBlogEntries($articles);

$sitemapStart = '<!-- Blog Pages AUTO-GENERATED START -->';
$sitemapEnd = '<!-- Blog Pages AUTO-GENERATED END -->';
$sitemap = file_get_contents($sitemapFile);
$sitemapNew = blogReplaceBetweenSentinels($sitemap, $sitemapStart, $sitemapEnd, $blogSitemapBlock);
if ($sitemapNew !== $sitemap) {
    file_put_contents($sitemapFile, $sitemapNew);
    echo "  Sitemap blog block regenerated (" . count(getPublishedBlogArticlesSorted($articles)) . " articles).\n";
} else {
    echo "  WARNING: sitemap blog sentinels not found; skipped blog block update.\n";
}

$llmsStart = '<!-- LLMS-BLOG-AUTO-GENERATED START -->';
$llmsEnd = '<!-- LLMS-BLOG-AUTO-GENERATED END -->';
$llmsFiles = [
    $root . '/llms.txt' => 'nl',
    $root . '/llms-en.txt' => 'en',
    $root . '/llms-de.txt' => 'de',
    $root . '/llms-nijenhuis.txt' => 'nl',
];

foreach ($llmsFiles as $llmsFile => $lang) {
    if (!file_exists($llmsFile)) {
        echo "  SKIPPED: " . basename($llmsFile) . " not found\n";
        continue;
    }
    $llmsContent = file_get_contents($llmsFile);
    $blogSection = blogBuildLlmsBlogSection($articles, $lang);
    $llmsNew = blogReplaceBetweenSentinels($llmsContent, $llmsStart, $llmsEnd, $blogSection);
    if ($llmsNew !== $llmsContent) {
        file_put_contents($llmsFile, $llmsNew);
        echo "  Updated " . basename($llmsFile) . " blog section.\n";
    } else {
        echo "  WARNING: " . basename($llmsFile) . " blog sentinels not found; skipped.\n";
    }
}

echo "=== Done ===\n";
