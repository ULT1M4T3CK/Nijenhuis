<?php
/**
 * Blog Portal API - Authentication and CRUD for blog articles
 * Separate from admin/employee - uses BLOG_USERNAME and BLOG_PASSWORD_HASH
 */
require_once __DIR__ . '/portal-headers.php';
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/../components/security.php';
require_once __DIR__ . '/../components/data_access.php';
require_once __DIR__ . '/../lib/blog-helpers.php';
require_once __DIR__ . '/../admin/session-config.php';

loadEnvSafe(__DIR__ . '/../.env');

$blogUser = getenv('BLOG_USERNAME') ?: ($_ENV['BLOG_USERNAME'] ?? '');
$blogPassHash = getenv('BLOG_PASSWORD_HASH') ?: ($_ENV['BLOG_PASSWORD_HASH'] ?? '');

$articlesFile = nijenhuis_data_path('articles.json');
$articlesDir = __DIR__ . '/../content/articles';
$blogStylesFile = nijenhuis_data_path('blog-styles.json');
$blogImagesDir = __DIR__ . '/../frontend/Images/blog';

function jsonResponse($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Handle multipart image upload before JSON parsing
$contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'multipart/form-data') !== false) {
    $multipartAction = $_POST['action'] ?? '';
    if ($multipartAction === 'uploadBlogImage') {
        require_once __DIR__ . '/../components/data_access.php';
        require_once __DIR__ . '/../admin/session-config.php';
        loadEnvSafe(__DIR__ . '/../.env');
        requireBlogAuth();
        requireBlogCsrfMultipart();
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errCode = $_FILES['image']['error'] ?? -1;
            http_response_code(400);
            jsonResponse(['success' => false, 'message' => 'Upload error: ' . $errCode]);
        }
        $file = $_FILES['image'];
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            jsonResponse(['success' => false, 'message' => 'Bestand te groot. Max 5MB.']);
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            jsonResponse(['success' => false, 'message' => 'Server kan bestandstype niet bepalen.']);
        }
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!is_string($mime) || $mime === '') {
            jsonResponse(['success' => false, 'message' => 'Ongeldig afbeeldingsbestand.']);
        }
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($mime, $allowedMimes, true)) {
            jsonResponse(['success' => false, 'message' => 'Alleen JPEG, PNG, WebP en GIF toegestaan.']);
        }
        $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $ext = $extMap[$mime];
        $uploadDir = realpath(__DIR__ . '/../frontend/Images/blog');
        if (!$uploadDir) {
            $uploadDir = __DIR__ . '/../frontend/Images/blog';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $uploadDir = realpath($uploadDir);
        }
        if (!$uploadDir || !is_dir($uploadDir)) {
            jsonResponse(['success' => false, 'message' => 'Upload map niet gevonden.']);
        }
        $uploadDir .= '/';
        $filename = 'blog_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $destPath = $uploadDir . $filename;
        if (!is_uploaded_file($file['tmp_name']) || !move_uploaded_file($file['tmp_name'], $destPath)) {
            jsonResponse(['success' => false, 'message' => 'Opslaan mislukt.']);
        }
        jsonResponse(['success' => true, 'url' => '/frontend/Images/blog/' . $filename]);
    }
}

function requireBlogAuth() {
    global $blogUser, $blogPassHash;
    if (empty($blogUser) || empty($blogPassHash)) {
        jsonResponse(['success' => false, 'message' => 'Blog credentials not configured. Set BLOG_USERNAME and BLOG_PASSWORD_HASH in .env']);
    }
    if (!isset($_SESSION['blog_authenticated']) || $_SESSION['blog_authenticated'] !== true) {
        http_response_code(401);
        jsonResponse(['success' => false, 'message' => 'Unauthorized', 'authenticated' => false]);
    }
}

/**
 * CSRF for JSON POST bodies (after requireBlogAuth).
 */
function requireBlogCsrfJson(array $input) {
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrfToken'] ?? '');
    $session = $_SESSION['blog_csrf_token'] ?? '';
    if ($session === '' || $token === '' || !hash_equals($session, (string)$token)) {
        http_response_code(403);
        jsonResponse(['success' => false, 'message' => 'Invalid CSRF token']);
    }
}

/**
 * CSRF for multipart (token in POST field or header).
 */
function requireBlogCsrfMultipart() {
    $token = $_POST['csrfToken'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    $session = $_SESSION['blog_csrf_token'] ?? '';
    if ($session === '' || $token === '' || !hash_equals($session, (string)$token)) {
        http_response_code(403);
        jsonResponse(['success' => false, 'message' => 'Invalid CSRF token']);
    }
}

function sanitizeSlug($slug) {
    $slug = preg_replace('/[^a-z0-9\-]/', '-', strtolower($slug));
    return trim(preg_replace('/-+/', '-', $slug), '-');
}

$input = [];
$raw = file_get_contents('php://input');
if ($raw) {
    $input = json_decode($raw, true) ?: [];
}
$action = $_GET['action'] ?? $input['action'] ?? '';

switch ($action) {
    case 'blogLogin':
        if (empty($blogUser) || empty($blogPassHash)) {
            jsonResponse(['success' => false, 'message' => 'Blog credentials not configured.']);
        }
        // Rate-limit login attempts per IP (5 per 15 minutes) to blunt
        // credential stuffing against the blog portal.
        $__loginIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $__loginIp = explode(',', $__loginIp)[0];
        if (!checkRateLimitAtomic('blog_login_' . $__loginIp, 5, 900, false)) {
            http_response_code(429);
            jsonResponse(['success' => false, 'message' => 'Te veel inlogpogingen. Probeer later opnieuw.']);
        }
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';
        if (!$username || !$password) {
            jsonResponse(['success' => false, 'message' => 'Voer gebruikersnaam en wachtwoord in.']);
        }
        if (!hash_equals($blogUser, $username) || !verifyPassword($password, $blogPassHash)) {
            // Constant-ish delay to reduce observable timing difference.
            usleep(250000);
            jsonResponse(['success' => false, 'message' => 'Ongeldige gebruikersnaam of wachtwoord.']);
        }
        $_SESSION['blog_authenticated'] = true;
        $_SESSION['blog_login_time'] = time();
        $_SESSION['blog_csrf_token'] = bin2hex(random_bytes(32));
        jsonResponse(['success' => true, 'csrfToken' => $_SESSION['blog_csrf_token']]);

    case 'blogSession':
        $auth = isset($_SESSION['blog_authenticated']) && $_SESSION['blog_authenticated'] === true;
        $csrf = ($auth && !empty($_SESSION['blog_csrf_token'])) ? $_SESSION['blog_csrf_token'] : '';
        if ($auth && $csrf === '') {
            $_SESSION['blog_csrf_token'] = bin2hex(random_bytes(32));
            $csrf = $_SESSION['blog_csrf_token'];
        }
        jsonResponse(['success' => true, 'authenticated' => $auth, 'csrfToken' => $csrf]);

    case 'blogLogout':
        // CSRF-protect logout so a cross-site request can't nuisance-log a
        // user out. Only enforce when an authenticated session exists.
        if (!empty($_SESSION['blog_authenticated'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrfToken'] ?? '');
            $sessionToken = $_SESSION['blog_csrf_token'] ?? '';
            if (empty($sessionToken) || empty($token) || !hash_equals($sessionToken, (string)$token)) {
                http_response_code(403);
                jsonResponse(['success' => false, 'message' => 'Invalid CSRF token']);
            }
        }
        unset($_SESSION['blog_authenticated'], $_SESSION['blog_login_time'], $_SESSION['blog_csrf_token']);
        jsonResponse(['success' => true]);

    case 'listArticles':
        requireBlogAuth();
        blogImportPortalBlogDrafts();
        $articles = blogLoadRawArticlesFromData();
        jsonResponse(['success' => true, 'articles' => $articles]);

    case 'importBlogDrafts':
        requireBlogAuth();
        requireBlogCsrfJson($input);
        $result = blogImportPortalBlogDrafts();
        jsonResponse([
            'success' => true,
            'imported' => $result['imported'],
            'skipped' => $result['skipped'],
            'articles' => blogLoadRawArticlesFromData(),
        ]);

    case 'getArticle':
        requireBlogAuth();
        $slug = sanitizeSlug($input['slug'] ?? '');
        if (!$slug) {
            jsonResponse(['success' => false, 'message' => 'Slug ontbreekt.']);
        }
        $article = blogFindArticleBySlug($slug);
        if (!$article) {
            jsonResponse(['success' => false, 'message' => 'Artikel niet gevonden.']);
        }
        $article = blogPortalNormalizeArticleMeta($article);
        $article = blogPortalAttachArticleBodies($article);
        jsonResponse(['success' => true, 'article' => $article]);

    case 'saveArticle':
        requireBlogAuth();
        requireBlogCsrfJson($input);
        $data = $input['article'] ?? [];
        $slug = sanitizeSlug($data['slug'] ?? '');
        if (!$slug) {
            jsonResponse(['success' => false, 'message' => 'Slug is verplicht.']);
        }
        $translations = $data['translations'] ?? [];

        $articles = loadJsonSafe($articlesFile);
        if (!is_array($articles)) {
            $articles = [];
        }
        $lookupSlug = $slug;
        if (!empty($data['originalSlug'])) {
            $lookupSlug = sanitizeSlug($data['originalSlug']);
        }
        $foundIndex = null;
        $oldSlug = null;
        foreach ($articles as $i => $a) {
            if (isset($a['slug']) && $a['slug'] === $lookupSlug) {
                $foundIndex = $i;
                $oldSlug = $a['slug'];
                break;
            }
        }

        $featuredImage = trim($data['featuredImage'] ?? '');
        $published = isset($data['published']) ? (bool) $data['published'] : true;
        $noindex = !empty($data['noindex']);
        $canonicalUrlArticle = trim($data['canonicalUrl'] ?? '');
        $publishAtRaw = trim($data['publishAt'] ?? '');
        $date = trim($data['date'] ?? date('Y-m-d'));
        if ($publishAtRaw !== '') {
            $parsed = strtotime($publishAtRaw);
            if ($parsed !== false) {
                $date = date('Y-m-d', $parsed);
            }
        }
        $allowedStyleKeys = ['articleLinkColor', 'articleH1Size', 'articleH2Size', 'articleH3Size', 'articleBodyLineHeight', 'articleMaxWidth', 'articleTextColor', 'articleBlockquoteBg'];
        $styleOverrides = [];
        foreach ($allowedStyleKeys as $k) {
            if (isset($data['styleOverrides'][$k]) && $data['styleOverrides'][$k] !== '') {
                $styleOverrides[$k] = $data['styleOverrides'][$k];
            }
        }
        $entry = [
            'slug' => $slug,
            'featuredImage' => $featuredImage ?: null,
            'published' => $published,
            'noindex' => $noindex,
            'canonicalUrl' => $canonicalUrlArticle !== '' ? $canonicalUrlArticle : null,
            'publishAt' => $publishAtRaw !== '' ? $publishAtRaw : null,
            'styleOverrides' => $styleOverrides ?: null,
            'translations' => []
        ];
        $articleDir = $articlesDir . '/' . $slug;
        $oldArticleDir = $oldSlug ? $articlesDir . '/' . $oldSlug : null;

        if ($oldSlug && $oldSlug !== $slug && $slug) {
            if (!is_dir($oldArticleDir)) {
                jsonResponse(['success' => false, 'message' => 'Oud artikel map niet gevonden.']);
            }
            if (is_dir($articleDir) && $articleDir !== $oldArticleDir) {
                jsonResponse(['success' => false, 'message' => 'Doel-slug bestaat al.']);
            }
            if (!rename($oldArticleDir, $articleDir)) {
                jsonResponse(['success' => false, 'message' => 'Kon map niet hernoemen.']);
            }
            $redirectsFile = nijenhuis_data_path('blog-redirects.json');
            $redirects = file_exists($redirectsFile) ? (json_decode(file_get_contents($redirectsFile), true) ?: []) : [];
            $redirects[$oldSlug] = $slug;
            if (!saveJsonSafe($redirectsFile, $redirects)) {
                jsonResponse(['success' => false, 'message' => 'Kon redirects niet opslaan.']);
            }
        } elseif (!is_dir($articleDir)) {
            if (!mkdir($articleDir, 0755, true)) {
                jsonResponse(['success' => false, 'message' => 'Kon map niet aanmaken.']);
            }
        }
        foreach (['nl', 'en', 'de'] as $lang) {
            $t = $translations[$lang] ?? [];
            $title = trim($t['title'] ?? '');
            $description = trim($t['description'] ?? '');
            $keywords = trim($t['keywords'] ?? '');
            $excerpt = trim($t['excerpt'] ?? '');
            $keyTakeaways = trim($t['keyTakeaways'] ?? '');
            $metaTitle = trim($t['metaTitle'] ?? '');
            $metaImage = trim($t['metaImage'] ?? '');
            $body = $t['body'] ?? '';
            if (!$title && !$body) {
                continue;
            }
            $trans = [
                'title' => $title,
                'description' => $description,
                'date' => $date,
                'keywords' => $keywords
            ];
            if ($excerpt !== '') {
                $trans['excerpt'] = $excerpt;
            }
            if ($keyTakeaways !== '') {
                $trans['keyTakeaways'] = $keyTakeaways;
            }
            if ($metaTitle !== '') {
                $trans['metaTitle'] = $metaTitle;
            }
            if ($metaImage !== '') {
                $trans['metaImage'] = $metaImage;
            }
            $entry['translations'][$lang] = $trans;
            $frontmatter = "---\ntitle: \"" . str_replace('"', '\\"', $title) . "\"\ndescription: \"" . str_replace('"', '\\"', $description) . "\"\ndate: \"" . $date . "\"\nslug: \"" . $slug . "\"\nkeywords: \"" . str_replace('"', '\\"', $keywords) . "\"\n---\n\n";
            $content = $frontmatter . $body;
            $mdPath = $articleDir . '/' . $lang . '.md';
            if (file_put_contents($mdPath, $content) === false) {
                jsonResponse(['success' => false, 'message' => 'Kon bestand niet schrijven: ' . $lang . '.md']);
            }
        }
        if ($foundIndex !== null) {
            $articles[$foundIndex] = $entry;
        } else {
            $articles[] = $entry;
        }
        usort($articles, function ($a, $b) {
            $dA = $a['translations']['nl']['date'] ?? $a['translations']['en']['date'] ?? '';
            $dB = $b['translations']['nl']['date'] ?? $b['translations']['en']['date'] ?? '';
            return strcmp($dB, $dA);
        });
        if (!saveJsonSafe($articlesFile, $articles)) {
            jsonResponse(['success' => false, 'message' => 'Kon articles.json niet opslaan.']);
        }
        jsonResponse(['success' => true, 'slug' => $slug]);

    case 'deleteArticle':
        requireBlogAuth();
        requireBlogCsrfJson($input);
        $slug = sanitizeSlug($input['slug'] ?? '');
        if (!$slug) {
            jsonResponse(['success' => false, 'message' => 'Slug ontbreekt.']);
        }
        $articleDir = $articlesDir . '/' . $slug;
        if (is_dir($articleDir)) {
            $files = glob($articleDir . '/*');
            foreach ($files as $f) {
                if (is_file($f)) {
                    unlink($f);
                }
            }
            rmdir($articleDir);
        }
        $articles = loadJsonSafe($articlesFile);
        $articles = array_values(array_filter($articles ?: [], function ($a) use ($slug) {
            return ($a['slug'] ?? '') !== $slug;
        }));
        saveJsonSafe($articlesFile, $articles);
        jsonResponse(['success' => true]);

    case 'getBlogStyles':
        requireBlogAuth();
        $stylesFile = nijenhuis_data_path('blog-styles.json');
        $styles = [];
        if (file_exists($stylesFile)) {
            $styles = json_decode(file_get_contents($stylesFile), true) ?: [];
        }
        if (empty($styles)) {
            $styles = [
                'articleLinkColor' => '#0071BB',
                'articleH1Size' => '1.75rem',
                'articleH2Size' => '1.35rem',
                'articleH3Size' => '1.15rem',
                'articleBodyLineHeight' => '1.7',
                'articleMaxWidth' => '800px',
                'articleTextColor' => '#333333',
                'articleBlockquoteBg' => '#f8f9fa'
            ];
        }
        jsonResponse(['success' => true, 'styles' => $styles]);

    case 'saveBlogStyles':
        requireBlogAuth();
        requireBlogCsrfJson($input);
        $styles = $input['styles'] ?? [];
        $stylesFile = nijenhuis_data_path('blog-styles.json');
        $allowed = ['articleLinkColor', 'articleH1Size', 'articleH2Size', 'articleH3Size', 'articleBodyLineHeight', 'articleMaxWidth', 'articleTextColor', 'articleBlockquoteBg'];
        $out = [];
        foreach ($allowed as $k) {
            if (isset($styles[$k])) {
                $out[$k] = $styles[$k];
            }
        }
        if (empty($out)) {
            jsonResponse(['success' => false, 'message' => 'Geen geldige stijlen.']);
        }
        if (!saveJsonSafe($stylesFile, $out)) {
            jsonResponse(['success' => false, 'message' => 'Kon stijlen niet opslaan.']);
        }
        jsonResponse(['success' => true]);

    default:
        http_response_code(400);
        jsonResponse(['success' => false, 'message' => 'Ongeldige actie.']);
}
