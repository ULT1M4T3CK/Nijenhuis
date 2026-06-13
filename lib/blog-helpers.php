<?php
/**
 * Blog helper functions - date formatting, reading time, etc.
 */

if (!function_exists('nijenhuis_data_path')) {
    require_once __DIR__ . '/../components/data_paths.php';
}

/**
 * Fix image URLs saved from local dev (localhost) so they resolve on production.
 *
 * @param string|null $url Featured image or img src from CMS
 * @return string
 */
function normalizeBlogPublicImageUrl($url) {
    if ($url === null || $url === '') {
        return '';
    }
    $url = trim((string) $url);
    if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?(/.*)?$#i', $url, $m)) {
        return isset($m[3]) && $m[3] !== '' ? $m[3] : '/';
    }
    return $url;
}

/**
 * Rewrite localhost absolute URLs in <img src="…"> to site-relative paths.
 *
 * @param string $html Article HTML
 * @return string
 */
function normalizeBlogArticleHtmlImages($html) {
    if ($html === '' || strpos($html, '<img') === false) {
        return $html;
    }
    return preg_replace_callback(
        '/\ssrc\s*=\s*(["\'])(https?:\/\/(?:localhost|127\.0\.0\.1)(?::\d+)?)\/?([^"\']*)\1/i',
        function ($m) {
            $path = isset($m[3]) ? $m[3] : '';
            $rel = ($path !== '' && $path[0] === '/') ? $path : '/' . ltrim($path, '/');
            return ' src=' . $m[1] . $rel . $m[1];
        },
        $html
    );
}

/**
 * Wrap markdown tables for blog pricing-style layout.
 *
 * @param string $html Article HTML
 * @return string
 */
function normalizeBlogArticleHtmlTables($html) {
    if ($html === '' || stripos($html, '<table') === false) {
        return $html;
    }
    $html = preg_replace('/<table(\s[^>]*)?>/i', '<div class="blog-table-wrap"><table class="blog-pricing-table"$1>', $html);
    return preg_replace('/<\/table>/i', '</table></div>', $html);
}

/**
 * Add anchor-list class to markdown bullet lists in blog articles.
 *
 * @param string $html Article HTML
 * @return string
 */
function normalizeBlogArticleHtmlLists($html) {
    if ($html === '' || stripos($html, '<ul') === false) {
        return $html;
    }
    return preg_replace_callback('/<ul(\s[^>]*)?>/i', function ($m) {
        $attrs = $m[1] ?? '';
        if (stripos($attrs, 'anchor-list') !== false) {
            return $m[0];
        }
        if (preg_match('/class=(["\'])([^"\']*)\1/i', $attrs, $cm)) {
            $quote = $cm[1];
            $newClass = trim($cm[2] . ' anchor-list');
            return preg_replace(
                '/class=(["\'])[^"\']*\1/i',
                'class=' . $quote . $newClass . $quote,
                $m[0],
                1
            );
        }
        return '<ul class="anchor-list"' . $attrs . '>';
    }, $html);
}

/**
 * Format a date for display in the blog (localized for nl/en/de).
 *
 * @param string $date ISO date (Y-m-d)
 * @param string $lang Language code: nl, en, de
 * @return string Formatted date
 */
function formatBlogDate($date, $lang = 'nl') {
    $ts = strtotime($date);
    if (!$ts) {
        return '';
    }
    $localeMap = [
        'nl' => 'nl_NL',
        'en' => 'en_GB',
        'de' => 'de_DE',
    ];
    $locale = $localeMap[$lang] ?? 'nl_NL';
    if (class_exists('IntlDateFormatter')) {
        $fmt = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        return $fmt->format($ts);
    }
    // Fallback: simple month names per language
    $months = [
        'nl' => ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
        'en' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        'de' => ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
    ];
    $m = $months[$lang] ?? $months['nl'];
    $day = (int) date('j', $ts);
    $month = $m[(int) date('n', $ts) - 1];
    $year = date('Y', $ts);
    if ($lang === 'de') {
        return $day . '. ' . $month . ' ' . $year;
    }
    return $day . ' ' . $month . ' ' . $year;
}

/**
 * Estimate reading time in minutes from HTML or plain text.
 *
 * @param string $content HTML or text content
 * @param int $wordsPerMinute Average reading speed
 * @return int Minutes
 */
function estimateReadingTime($content, $wordsPerMinute = 200) {
    $text = strip_tags($content);
    $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    $count = count($words);
    $minutes = max(1, (int) ceil($count / $wordsPerMinute));
    return $minutes;
}

/**
 * Slugify a string for use as HTML ID.
 */
function slugifyHeading($text) {
    $text = strip_tags($text);
    $text = trim($text);
    $text = preg_replace('/[^\p{L}\p{N}\s\-]/u', '', $text);
    $text = preg_replace('/\s+/', '-', $text);
    $text = strtolower($text);
    return trim($text, '-') ?: 'section';
}

/**
 * Extract table of contents from HTML headings (h2, h3).
 * Adds id attributes to headings and returns TOC structure.
 *
 * @param string $html Article HTML (modified in place via reference)
 * @return array List of [level, text, id]
 */
function extractTableOfContents(&$html) {
    $toc = [];
    $counter = [];
    $html = preg_replace_callback(
        '/<h([2-3])([^>]*)>([^<]+)<\/h\1>/i',
        function ($m) use (&$toc, &$counter) {
            $level = (int) $m[1];
            $text = trim($m[3]);
            $baseId = slugifyHeading($text);
            if (isset($counter[$baseId])) {
                $counter[$baseId]++;
                $id = $baseId . '-' . $counter[$baseId];
            } else {
                $counter[$baseId] = 0;
                $id = $baseId;
            }
            $toc[] = ['level' => $level, 'text' => $text, 'id' => $id];
            return '<h' . $level . ' id="' . $id . '"' . $m[2] . '>' . $m[3] . '</h' . $level . '>';
        },
        $html
    );
    return $toc;
}

/**
 * Format one key-takeaway line for HTML output: escape text and allow **bold** (Markdown-style).
 *
 * @param string $line
 * @return string Safe HTML fragment (inline only, no block tags)
 */
function formatBlogTakeawayLineHtml($line) {
    $line = (string) $line;
    $out = '';
    $rest = $line;
    while (preg_match('/^(.*?)\*\*(.+?)\*\*/s', $rest, $m)) {
        $out .= htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8');
        $out .= '<strong>' . htmlspecialchars($m[2], ENT_QUOTES, 'UTF-8') . '</strong>';
        $rest = substr($rest, strlen($m[0]));
    }
    $out .= htmlspecialchars($rest, ENT_QUOTES, 'UTF-8');
    return $out;
}

/**
 * Extract key takeaways from Markdown automatically.
 * Looks for: (1) list under heading containing "tip","takeaway","samenvatting","key","belangrijk","wichtig"
 * (2) first bullet list in document (max 7 items).
 *
 * @param string $markdown Raw markdown body
 * @return array List of takeaway strings (plain text)
 */
function extractKeyTakeaways($markdown) {
    $takeaways = [];
    $lines = preg_split('/\r\n|\r|\n/', $markdown);

    $takeawayHeadings = [
        'tip', 'takeaway', 'samenvatting', 'summary', 'key', 'belangrijk', 'wichtig',
        'punten', 'points', 'highlights', 'samenvatting', 'zusammenfassung'
    ];

    $inTakeawayList = false;
    $lastHeading = '';
    $foundExplicit = false;

    foreach ($lines as $line) {
        if (preg_match('/^#{1,6}\s+(.+)$/', $line, $hm)) {
            $heading = strtolower(trim($hm[1]));
            $inTakeawayList = false;
            foreach ($takeawayHeadings as $kw) {
                if (strpos($heading, $kw) !== false) {
                    $lastHeading = $heading;
                    $inTakeawayList = true;
                    break;
                }
            }
            continue;
        }
        if (preg_match('/^\*{1,2}(.+)\*{0,2}\s*$/', $line, $bm) && trim($bm[1]) !== '') {
            $boldText = strtolower(trim($bm[1]));
            foreach ($takeawayHeadings as $kw) {
                if (strpos($boldText, $kw) !== false) {
                    $inTakeawayList = true;
                    break;
                }
            }
            continue;
        }

        if (preg_match('/^[\-\*]\s+(.+)$/', $line, $lm)) {
            $item = trim($lm[1]);
            $item = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $item);
            if ($inTakeawayList) {
                $takeaways[] = $item;
                $foundExplicit = true;
            }
        } elseif (trim($line) !== '') {
            if ($inTakeawayList) {
                $inTakeawayList = false;
            }
        }
    }

    if (!empty($takeaways)) {
        return array_slice($takeaways, 0, 7);
    }

    $inList = false;
    foreach ($lines as $line) {
        if (preg_match('/^[\-\*]\s+(.+)$/', $line, $lm)) {
            if (!$inList) {
                $inList = true;
                $takeaways = [];
            }
            $item = trim($lm[1]);
            $item = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $item);
            $takeaways[] = $item;
        } elseif (trim($line) !== '' && !preg_match('/^>\s/', $line)) {
            if ($inList && count($takeaways) > 0) {
                break;
            }
            $inList = false;
        }
    }

    return array_slice($takeaways ?: [], 0, 7);
}

/**
 * Published articles sorted by date (newest first). Uses nl/en/de date from translations.
 *
 * @param array $articles Raw articles from articles.json
 * @return array
 */
function getPublishedBlogArticlesSorted(array $articles) {
    $published = array_values(array_filter($articles, function ($a) {
        return ($a['published'] ?? true) === true;
    }));
    usort($published, function ($a, $b) {
        $dateA = $a['translations']['nl']['date'] ?? $a['translations']['en']['date'] ?? $a['translations']['de']['date'] ?? '';
        $dateB = $b['translations']['nl']['date'] ?? $b['translations']['en']['date'] ?? $b['translations']['de']['date'] ?? '';
        return strcmp($dateB, $dateA);
    });
    return $published;
}

/**
 * Parse YAML-style frontmatter from a blog markdown file.
 *
 * @return array<string, string>
 */
function blogParseMarkdownFrontmatter(string $mdPath): array {
    if (!is_file($mdPath)) {
        return [];
    }
    $raw = file_get_contents($mdPath);
    if ($raw === false || !preg_match('/^---\s*\n(.*?)\n---\s*\n/s', $raw, $fm)) {
        return [];
    }
    $frontmatter = [];
    preg_match_all('/^([a-zA-Z]+):\s*["\']?([^"\'\n]+)["\']?\s*$/m', $fm[1], $m, PREG_SET_ORDER);
    foreach ($m as $match) {
        $frontmatter[$match[1]] = trim($match[2], '"\'');
    }
    return $frontmatter;
}

/**
 * Merge repo-tracked article seeds (content/articles.json + markdown folders) into runtime list.
 * data/articles.json wins on slug conflicts; seeds fill gaps for local/git-only metadata.
 *
 * @param array<int, array<string, mixed>> $articles
 * @return array<int, array<string, mixed>>
 */
function blogMergeArticlesFromRepoSeed(array $articles): array {
    $bySlug = [];
    foreach ($articles as $article) {
        if (!empty($article['slug'])) {
            $bySlug[$article['slug']] = $article;
        }
    }

    $seedFile = NIJENHUIS_ROOT . '/content/articles.json';
    if (is_file($seedFile)) {
        $seedRaw = file_get_contents($seedFile);
        $seed = ($seedRaw !== false && $seedRaw !== '') ? json_decode($seedRaw, true) : null;
        if (is_array($seed)) {
            foreach ($seed as $article) {
                $slug = $article['slug'] ?? '';
                if ($slug !== '' && !isset($bySlug[$slug])) {
                    $bySlug[$slug] = $article;
                }
            }
        }
    }

    $articlesDir = NIJENHUIS_ROOT . '/content/articles';
    if (is_dir($articlesDir)) {
        foreach (scandir($articlesDir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $slug = $entry;
            $mdPath = $articlesDir . '/' . $slug . '/nl.md';
            if (!is_dir($articlesDir . '/' . $slug) || !is_file($mdPath) || isset($bySlug[$slug])) {
                continue;
            }
            $fm = blogParseMarkdownFrontmatter($mdPath);
            $title = trim($fm['title'] ?? '');
            if ($title === '') {
                $title = ucwords(str_replace('-', ' ', $slug));
            }
            $bySlug[$slug] = [
                'slug' => $slug,
                'featuredImage' => null,
                'published' => true,
                'noindex' => false,
                'canonicalUrl' => null,
                'publishAt' => null,
                'styleOverrides' => null,
                'translations' => [
                    'nl' => [
                        'title' => $title,
                        'description' => trim($fm['description'] ?? ''),
                        'date' => trim($fm['date'] ?? date('Y-m-d')),
                        'keywords' => trim($fm['keywords'] ?? ''),
                    ],
                ],
            ];
        }
    }

    return array_values($bySlug);
}

/**
 * Raw articles array from data/articles.json (empty if missing).
 * Merges repo seed (content/articles.json) and markdown folders for git-tracked articles.
 *
 * @return array<int, array<string, mixed>>
 */
function blogLoadRawArticlesFromData() {
    $articles = [];
    $articlesFile = nijenhuis_data_path('articles.json');
    if (is_file($articlesFile)) {
        $raw = file_get_contents($articlesFile);
        if ($raw !== false && $raw !== '') {
            $data = json_decode($raw, true);
            if (is_array($data)) {
                $articles = $data;
            }
        }
    }
    return blogMergeArticlesFromRepoSeed($articles);
}

/**
 * Find merged article metadata by slug (articles.json + repo seed + markdown folders).
 *
 * @return array<string, mixed>|null
 */
function blogFindArticleBySlug(string $slug): ?array {
    foreach (blogLoadRawArticlesFromData() as $article) {
        if (($article['slug'] ?? '') === $slug) {
            return $article;
        }
    }
    return null;
}

/**
 * Resolve translation meta for an article, with markdown frontmatter fallback.
 *
 * @return array{meta: array<string, mixed>, lang: string}|null
 */
function blogResolveArticleTranslation(array $articleMeta, string $lang): ?array {
    $tryOrder = array_unique([$lang, 'nl', 'en', 'de']);
    foreach ($tryOrder as $tryLang) {
        if (!isset($articleMeta['translations'][$tryLang])) {
            continue;
        }
        $meta = $articleMeta['translations'][$tryLang];
        if (!is_array($meta)) {
            continue;
        }
        if (trim($meta['title'] ?? '') !== '') {
            return ['meta' => $meta, 'lang' => $tryLang];
        }
    }

    $slug = $articleMeta['slug'] ?? '';
    if ($slug === '') {
        return null;
    }
    $articleDir = NIJENHUIS_ROOT . '/content/articles/' . $slug;
    foreach ($tryOrder as $tryLang) {
        $mdPath = $articleDir . '/' . $tryLang . '.md';
        if (!is_file($mdPath)) {
            continue;
        }
        $fm = blogParseMarkdownFrontmatter($mdPath);
        $title = trim($fm['title'] ?? '');
        if ($title === '') {
            continue;
        }
        return [
            'meta' => [
                'title' => $title,
                'metaTitle' => trim($fm['metaTitle'] ?? '') ?: null,
                'description' => trim($fm['description'] ?? ''),
                'date' => trim($fm['date'] ?? ''),
                'keywords' => trim($fm['keywords'] ?? ''),
            ],
            'lang' => $tryLang,
        ];
    }

    return null;
}

/**
 * True when there is at least one published article (public blog index may be shown).
 */
function blogPublicHasPublishedArticles() {
    return count(getPublishedBlogArticlesSorted(blogLoadRawArticlesFromData())) > 0;
}

/**
 * Other articles for the sidebar (excluding current slug).
 * Uses article.relatedSlugs when set; otherwise newest published articles.
 *
 * @param array $sortedPublished Output of getPublishedBlogArticlesSorted
 * @param string $excludeSlug
 * @param int $limit
 * @return array
 */
function getRelatedBlogArticles(array $sortedPublished, $excludeSlug, $limit = 3) {
    $current = null;
    foreach ($sortedPublished as $a) {
        if (($a['slug'] ?? '') === $excludeSlug) {
            $current = $a;
            break;
        }
    }

    $relatedSlugs = $current['relatedSlugs'] ?? null;
    if (is_array($relatedSlugs) && $relatedSlugs !== []) {
        $bySlug = [];
        foreach ($sortedPublished as $a) {
            $slug = $a['slug'] ?? '';
            if ($slug !== '') {
                $bySlug[$slug] = $a;
            }
        }
        $out = [];
        foreach ($relatedSlugs as $slug) {
            if (!is_string($slug) || $slug === '' || $slug === $excludeSlug) {
                continue;
            }
            if (isset($bySlug[$slug])) {
                $out[] = $bySlug[$slug];
            }
            if (count($out) >= $limit) {
                break;
            }
        }
        if ($out !== []) {
            return $out;
        }
    }

    $out = [];
    foreach ($sortedPublished as $a) {
        if (isset($a['slug']) && $a['slug'] === $excludeSlug) {
            continue;
        }
        $out[] = $a;
        if (count($out) >= $limit) {
            break;
        }
    }
    return $out;
}

/**
 * "Most read" sidebar list: newest published articles (proxy until analytics exist).
 *
 * @param array $sortedPublished
 * @param int $limit
 * @return array
 */
function getMostReadBlogArticles(array $sortedPublished, $limit = 5) {
    return array_slice($sortedPublished, 0, $limit);
}

/**
 * Normalize article metadata for the blog portal editor API.
 *
 * @param array<string, mixed> $article
 * @return array<string, mixed>
 */
function blogPortalNormalizeArticleMeta(array $article): array {
    $article['noindex'] = !empty($article['noindex']);
    $article['publishAt'] = isset($article['publishAt']) ? (string) $article['publishAt'] : '';
    $article['canonicalUrl'] = isset($article['canonicalUrl']) ? (string) $article['canonicalUrl'] : '';
    $article['featuredImage'] = $article['featuredImage'] ?? null;
    $article['published'] = $article['published'] ?? true;
    $article['styleOverrides'] = $article['styleOverrides'] ?? null;
    return $article;
}

/**
 * Attach markdown bodies from content/articles/{slug}/{lang}.md for portal editing.
 *
 * @param array<string, mixed> $article
 * @return array<string, mixed>
 */
function blogPortalAttachArticleBodies(array $article): array {
    $slug = $article['slug'] ?? '';
    if ($slug === '') {
        return $article;
    }
    $articlesDir = NIJENHUIS_ROOT . '/content/articles';
    $translations = $article['translations'] ?? [];
    foreach (['nl', 'en', 'de'] as $lang) {
        if (!isset($translations[$lang])) {
            continue;
        }
        $mdPath = $articlesDir . '/' . $slug . '/' . $lang . '.md';
        if (!is_file($mdPath)) {
            continue;
        }
        $raw = file_get_contents($mdPath);
        if ($raw === false) {
            continue;
        }
        $body = $raw;
        if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $raw, $m)) {
            $body = $m[2];
        }
        $translations[$lang]['body'] = $body;
    }
    $article['translations'] = $translations;
    return $article;
}

/**
 * Import new blog drafts from blog-portal/blogs/*.md into data/articles.json + content/articles/.
 * Skips slugs that already exist in data/articles.json (does not overwrite portal edits).
 *
 * @return array{imported: int, skipped: int}
 */
function blogImportPortalBlogDrafts(): array {
    if (!function_exists('nijenhuis_data_path')) {
        require_once __DIR__ . '/../components/data_paths.php';
    }
    if (!function_exists('saveJsonSafe')) {
        require_once __DIR__ . '/../components/data_access.php';
    }

    $blogsDir = NIJENHUIS_ROOT . '/blog-portal/blogs';
    $articlesFile = nijenhuis_data_path('articles.json');
    $articlesDir = NIJENHUIS_ROOT . '/content/articles';
    $imported = 0;
    $skipped = 0;

    if (!is_dir($blogsDir)) {
        return ['imported' => 0, 'skipped' => 0];
    }

    $articles = [];
    if (is_file($articlesFile)) {
        $raw = file_get_contents($articlesFile);
        $decoded = ($raw !== false && $raw !== '') ? json_decode($raw, true) : null;
        if (is_array($decoded)) {
            $articles = $decoded;
        }
    }
    $existingSlugs = [];
    foreach ($articles as $article) {
        $slug = $article['slug'] ?? '';
        if ($slug !== '') {
            $existingSlugs[$slug] = true;
        }
    }

    $featuredImages = [
        '/frontend/Images/Giethoorn/Giethoorn1.png',
        '/frontend/Images/belterwijde.jpg',
        '/frontend/Images/Wanneperveen/beulakerwijde-view.jpg',
        '/frontend/Images/Boats/electroboat-5.jpg',
        '/frontend/Images/Boats/electrosloep-8/electrosloop-8.jpg',
    ];

    foreach (scandir($blogsDir) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..' || !preg_match('/\.md$/i', $entry)) {
            continue;
        }
        $mdSource = $blogsDir . '/' . $entry;
        if (!is_file($mdSource)) {
            continue;
        }
        $fm = blogParseMarkdownFrontmatter($mdSource);
        $slug = trim($fm['slug'] ?? '');
        if ($slug === '') {
            $slug = preg_replace('/^blog-nij-\d+-/', '', pathinfo($entry, PATHINFO_FILENAME));
            $slug = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9\-]/', '-', strtolower($slug))), '-');
        }
        if ($slug === '' || isset($existingSlugs[$slug])) {
            $skipped++;
            continue;
        }

        $raw = file_get_contents($mdSource);
        if ($raw === false) {
            $skipped++;
            continue;
        }
        $body = $raw;
        if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $raw, $m)) {
            $body = $m[2];
        }

        $title = trim($fm['title'] ?? '');
        if ($title === '') {
            $title = ucwords(str_replace('-', ' ', $slug));
        }
        $description = trim($fm['description'] ?? '');
        $date = trim($fm['date'] ?? date('Y-m-d'));
        $keywords = trim($fm['keywords'] ?? '');
        $keyTakeaways = extractKeyTakeaways($body);
        $keyTakeawaysRaw = '';
        if ($keyTakeaways !== []) {
            $keyTakeawaysRaw = implode("\n", array_map(function ($line) {
                return preg_match('/^\*\*/', $line) ? $line : '**Tip:** ' . $line;
            }, $keyTakeaways));
        }

        $articleDir = $articlesDir . '/' . $slug;
        if (!is_dir($articleDir) && !mkdir($articleDir, 0755, true)) {
            $skipped++;
            continue;
        }
        if (file_put_contents($articleDir . '/nl.md', $raw) === false) {
            $skipped++;
            continue;
        }

        $translation = [
            'title' => $title,
            'description' => $description,
            'excerpt' => $description,
            'date' => $date,
            'keywords' => $keywords,
        ];
        if ($keyTakeawaysRaw !== '') {
            $translation['keyTakeaways'] = $keyTakeawaysRaw;
        }

        $articles[] = [
            'slug' => $slug,
            'featuredImage' => $featuredImages[$imported % count($featuredImages)] ?? null,
            'published' => true,
            'noindex' => false,
            'canonicalUrl' => null,
            'publishAt' => $date . 'T12:00',
            'styleOverrides' => null,
            'translations' => ['nl' => $translation],
        ];
        $existingSlugs[$slug] = true;
        $imported++;
    }

    if ($imported > 0) {
        usort($articles, function ($a, $b) {
            $dA = $a['translations']['nl']['date'] ?? $a['translations']['en']['date'] ?? '';
            $dB = $b['translations']['nl']['date'] ?? $b['translations']['en']['date'] ?? '';
            return strcmp($dB, $dA);
        });
        saveJsonSafe($articlesFile, $articles);
    }

    return ['imported' => $imported, 'skipped' => $skipped];
}

/**
 * Site base URL for SEO generators (works without loading config.php).
 */
function blogSeoSiteUrl(): string {
    return defined('SITE_URL') ? SITE_URL : 'https://nijenhuis-botenverhuur.com';
}

/**
 * Last-modified date (Y-m-d) for a blog article from markdown files or translation dates.
 */
function blogArticleLastmod(string $slug, array $article = []): string {
    $articleDir = __DIR__ . '/../content/articles/' . $slug;
    $latest = 0;
    foreach (['nl', 'en', 'de'] as $lang) {
        $mdFile = $articleDir . '/' . $lang . '.md';
        if (is_file($mdFile)) {
            $latest = max($latest, filemtime($mdFile));
        }
    }
    if ($latest > 0) {
        return date('Y-m-d', $latest);
    }
    $translations = $article['translations'] ?? [];
    foreach (['nl', 'en', 'de'] as $lang) {
        $date = trim($translations[$lang]['date'] ?? '');
        if ($date !== '') {
            return substr($date, 0, 10);
        }
    }
    return date('Y-m-d');
}

/**
 * Build sitemap XML for blog index + published articles (between AUTO-GENERATED sentinels).
 *
 * @param array<int, array<string, mixed>> $articles
 */
function blogBuildSitemapBlogEntries(array $articles): string {
    $siteUrl = blogSeoSiteUrl();
    $published = getPublishedBlogArticlesSorted($articles);
    $newestLastmod = '2026-01-01';
    foreach ($published as $article) {
        $slug = $article['slug'] ?? '';
        if ($slug === '') {
            continue;
        }
        $lm = blogArticleLastmod($slug, $article);
        if ($lm > $newestLastmod) {
            $newestLastmod = $lm;
        }
    }

    $xml = "    <url>\n";
    $xml .= "        <loc>{$siteUrl}/blog</loc>\n";
    $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"nl\" href=\"{$siteUrl}/blog\"/>\n";
    $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"en\" href=\"{$siteUrl}/en/blog\"/>\n";
    $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"de\" href=\"{$siteUrl}/de/blog\"/>\n";
    $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$siteUrl}/blog\"/>\n";
    $xml .= "        <lastmod>{$newestLastmod}</lastmod>\n";
    $xml .= "        <changefreq>weekly</changefreq>\n";
    $xml .= "        <priority>0.7</priority>\n";
    $xml .= "    </url>\n";

    foreach ($published as $article) {
        $slug = $article['slug'] ?? '';
        if ($slug === '') {
            continue;
        }
        $lastmod = blogArticleLastmod($slug, $article);
        $xml .= "    \n";
        $xml .= "    <url>\n";
        $xml .= "        <loc>{$siteUrl}/blog/{$slug}</loc>\n";
        $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"nl\" href=\"{$siteUrl}/blog/{$slug}\"/>\n";
        $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"en\" href=\"{$siteUrl}/en/blog/{$slug}\"/>\n";
        $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"de\" href=\"{$siteUrl}/de/blog/{$slug}\"/>\n";
        $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$siteUrl}/blog/{$slug}\"/>\n";
        $xml .= "        <lastmod>{$lastmod}</lastmod>\n";
        $xml .= "        <changefreq>monthly</changefreq>\n";
        $xml .= "        <priority>0.75</priority>\n";

        $featured = normalizeBlogPublicImageUrl($article['featuredImage'] ?? '');
        if ($featured !== '') {
            $imageUrl = (strpos($featured, 'http') === 0)
                ? $featured
                : rtrim($siteUrl, '/') . '/' . ltrim($featured, '/');
            $resolved = blogResolveArticleTranslation($article, 'nl');
            $caption = htmlspecialchars(
                $resolved['meta']['title'] ?? $slug,
                ENT_XML1 | ENT_QUOTES,
                'UTF-8'
            );
            $xml .= "        <image:image>\n";
            $xml .= "            <image:loc>" . htmlspecialchars($imageUrl, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</image:loc>\n";
            $xml .= "            <image:caption>{$caption}</image:caption>\n";
            $xml .= "        </image:image>\n";
        }

        $xml .= "    </url>\n";
    }

    return $xml;
}

/**
 * Build markdown blog section for llms*.txt (between AUTO-GENERATED sentinels).
 *
 * @param array<int, array<string, mixed>> $articles
 */
function blogBuildLlmsBlogSection(array $articles, string $lang): string {
    $siteUrl = blogSeoSiteUrl();
    $published = getPublishedBlogArticlesSorted($articles);

    $indexLines = [
        'nl' => [
            '- [Blog (NL)](https://nijenhuis-botenverhuur.com/blog): Artikelen over bootverhuur, Giethoorn en de Weerribben.',
            '- [Blog (EN)](https://nijenhuis-botenverhuur.com/en/blog): Articles about boat rental, Giethoorn and the Weerribben.',
            '- [Blog (DE)](https://nijenhuis-botenverhuur.com/de/blog): Artikel über Bootsverleih, Giethoorn und die Weerribben.',
        ],
        'en' => [
            '- [Blog (NL)](https://nijenhuis-botenverhuur.com/blog): Articles about boat rental, Giethoorn and the Weerribben.',
            '- [Blog (EN)](https://nijenhuis-botenverhuur.com/en/blog): Articles about boat rental, Giethoorn and the Weerribben.',
            '- [Blog (DE)](https://nijenhuis-botenverhuur.com/de/blog): Articles about boat rental, Giethoorn and the Weerribben.',
        ],
        'de' => [
            '- [Blog (NL)](https://nijenhuis-botenverhuur.com/blog): Artikel über Bootsverleih, Giethoorn und die Weerribben.',
            '- [Blog (EN)](https://nijenhuis-botenverhuur.com/en/blog): Artikel über Bootsverleih, Giethoorn und die Weerribben.',
            '- [Blog (DE)](https://nijenhuis-botenverhuur.com/de/blog): Artikel über Bootsverleih, Giethoorn und die Weerribben.',
        ],
    ];
    $lines = $indexLines[$lang] ?? $indexLines['nl'];

    foreach ($published as $article) {
        $slug = $article['slug'] ?? '';
        if ($slug === '') {
            continue;
        }
        $resolved = blogResolveArticleTranslation($article, $lang);
        if (!$resolved) {
            continue;
        }
        $meta = $resolved['meta'];
        $title = trim($meta['title'] ?? $slug);
        $description = trim($meta['description'] ?? $meta['excerpt'] ?? '');
        if ($description === '') {
            $description = 'Blogartikel';
        }
        $mdUrl = "{$siteUrl}/blog/{$slug}.md";
        $lines[] = '- [' . $title . '](' . $mdUrl . '): ' . $description;
    }

    return implode("\n", $lines);
}

/**
 * Replace content between sentinel markers in a file.
 */
function blogReplaceBetweenSentinels(string $content, string $startMarker, string $endMarker, string $replacement): string {
    $pattern = '#(' . preg_quote($startMarker, '#') . ')\s*\n.*?\n\s*(' . preg_quote($endMarker, '#') . ')#s';
    $newBlock = $startMarker . "\n" . $replacement . "\n" . $endMarker;
    if (preg_match($pattern, $content)) {
        return preg_replace($pattern, $newBlock, $content, 1);
    }
    return $content;
}
