<?php
/**
 * Home Page - Nijenhuis Botenverhuur
 */
require_once __DIR__ . '/../components/config.php';
require_once __DIR__ . '/../components/faq_price_helper.php';
require_once __DIR__ . '/../lib/boat-seo-content.php';
require_once __DIR__ . '/../lib/responsive-image.php';

/**
 * First paragraph / plain text for meta (boat landing pages).
 */
function index_seo_plain_first_paragraph($text) {
    $text = trim(preg_replace('/\s+/u', ' ', str_replace("\n", ' ', (string) $text)));
    if ($text === '') {
        return '';
    }
    $parts = preg_split('/\n\s*\n/u', $text);
    $first = $parts[0] ?? $text;
    return trim(preg_replace('/\s+/u', ' ', $first));
}

/**
 * Unique meta description per boat (reduces duplicate-content signals vs identical homepage HTML).
 */
function index_seo_boat_meta_description(array $boat) {
    $name = $boat['name'] ?? 'Boot';
    $desc = index_seo_plain_first_paragraph($boat['description'] ?? '');
    $base = $name . ' huren bij Giethoorn & Weerribben (Wanneperveen). ';
    $max = 158;
    $strlen = function_exists('mb_strlen') ? 'mb_strlen' : 'strlen';
    $substr = function_exists('mb_substr') ? 'mb_substr' : 'substr';
    $room = $max - $strlen($base);
    if ($room < 40) {
        $room = 40;
    }
    $rest = $desc;
    if ($strlen($rest) > $room) {
        $rest = $substr($rest, 0, max(0, $room - 1)) . '…';
    }
    return $base . $rest;
}

function index_seo_boat_og_image_url(array $boat) {
    $rel = $boat['headerImage'] ?? $boat['image'] ?? '';
    $rel = preg_replace('#^\.\./#', '', str_replace('\\', '/', $rel));
    $rel = ltrim($rel, '/');
    if ($rel === '') {
        return SITE_URL . assetPath('frontend/Images/banner-img.jpg');
    }
    return SITE_URL . assetPath($rel);
}

$basePath = getBasePath();
$pageTitle = 'Home';
$pageDescription = 'Botenverhuur in de Weerribben-Wieden vanuit Wanneperveen ✔️ Sloepen, fluisterboten, kano\'s & SUP ✔️ Rustig vertrekpunt bij Giethoorn ✔️ Vanaf €20/dag.';
$includeBoatData = true;

// Canonical & hreflang: homepage uses /; boat detail URLs use their path (e.g. /canoe-3).
// Query strings (e.g. ?lang=en) are excluded from canonical; each hreflang entry must include a self-reference for that locale.
$reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$reqPath = $reqPath === '' ? '/' : rawurldecode($reqPath);
if ($reqPath === '/' || $reqPath === '') {
    $canonicalUrl = SITE_URL . '/';
} else {
    $canonicalUrl = SITE_URL . rtrim($reqPath, '/');
}
$langQuerySep = (strpos($canonicalUrl, '?') === false) ? '?' : '&';

$pathSegment = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$boatLanding = null;
if ($pathSegment !== '') {
    foreach (faq_load_boats() as $b) {
        if (($b['id'] ?? '') === $pathSegment) {
            $boatLanding = $b;
            break;
        }
    }
}

$seoTitle = 'Botenverhuur Weerribben-Wieden | Nijenhuis Botenverhuur';
$seoDesc = $pageDescription;
$ogTitle = SITE_NAME . ' - Weerribben-Wieden & Wanneperveen';
$ogDesc = 'Botenverhuur in de Weerribben-Wieden vanuit Wanneperveen: fluisterboot, sloep, kano of SUP huren bij Giethoorn zonder drukte bij vertrek.';
$ogImageUrl = SITE_URL . assetPath('frontend/Images/banner-img.jpg');
$preloadImage = assetPath('frontend/Images/Boats/zeilboot-4-5.jpg');
$boatLandingBodyHtml = '';
$productLdJson = null;

if ($boatLanding) {
    $seoTitle = ($boatLanding['name'] ?? 'Boot') . ' huren | ' . SITE_NAME;
    $seoDesc = index_seo_boat_meta_description($boatLanding);
    $ogTitle = $seoTitle;
    $ogDesc = $seoDesc;
    $ogImageUrl = index_seo_boat_og_image_url($boatLanding);
    $pi = $boatLanding['headerImage'] ?? $boatLanding['image'] ?? '';
    $pi = preg_replace('#^\.\./#', '', str_replace('\\', '/', $pi));
    $pi = ltrim($pi, '/');
    if ($pi !== '') {
        $preloadImage = assetPath($pi);
    }
    $paras = preg_split('/\n\s*\n/u', trim($boatLanding['description'] ?? ''));
    foreach ($paras as $p) {
        $p = trim($p);
        if ($p === '') {
            continue;
        }
        $boatLandingBodyHtml .= '<p>' . htmlspecialchars($p, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    $boatLandingSeoHtml = boat_build_seo_html($boatLanding);
    $priceDay = isset($boatLanding['pricePerDay']) ? (float) $boatLanding['pricePerDay'] : 0;
    $boatCategory = $boatLanding['category'] ?? '';
    $isElectric = in_array($boatCategory, ['electric'], true);
    $isSailing = in_array($boatCategory, ['sailing'], true);

    $productLdJson = [
        '@context' => 'https://schema.org',
        '@type' => ['Product', 'Vehicle'],
        'name' => $boatLanding['name'] ?? '',
        'description' => index_seo_plain_first_paragraph($boatLanding['description'] ?? ''),
        'image' => [$ogImageUrl],
        'sku' => $boatLanding['id'] ?? '',
        'brand' => ['@type' => 'Brand', 'name' => SITE_NAME],
        'vehicleSeatingCapacity' => $boatLanding['passengerCount'] ?? '',
        'fuelType' => $isElectric ? 'Electric' : ($isSailing ? 'Wind' : 'Human Power'),
        'offers' => [
            '@type' => 'Offer',
            'url' => $canonicalUrl,
            'priceCurrency' => 'EUR',
            'price' => $priceDay > 0 ? (string) $priceDay : '0',
            'availability' => 'https://schema.org/InStock',
            'priceValidUntil' => date('Y') . '-12-31',
            'seller' => ['@type' => 'Organization', 'name' => SITE_NAME],
        ],
    ];
    if ($isElectric) {
        $productLdJson['vehicleEngine'] = [
            '@type' => 'EngineSpecification',
            'fuelType' => 'Electric',
            'name' => 'Elektrische fluistermotor',
        ];
    }
}

// Emit HTTP Link header for AI crawlers (markdown alternate)
require_once __DIR__ . '/../components/csp.php';
if (!headers_sent()) {
    nijenhuis_send_csp_header();
}
if (!headers_sent()) {
    $_indexMdUrl = $canonicalUrl;
    if ($_indexMdUrl !== '' && !preg_match('/\.(md|txt|json|php)$/', $_indexMdUrl)) {
        $_indexMdPath = parse_url($_indexMdUrl, PHP_URL_PATH);
        $_indexMdPath = rtrim($_indexMdPath, '/');
        if ($_indexMdPath === '' || $_indexMdPath === '/') {
            $_indexMdPath = '/index';
        }
        header('Link: <' . SITE_URL . $_indexMdPath . '.md>; rel="alternate"; type="text/markdown"', false);
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <?php include __DIR__ . '/../components/gtag.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0071BB">
    <link rel="manifest" href="<?php echo assetPath('frontend/public/manifest.json'); ?>">
    <title><?php echo htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($seoDesc, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($boatLanding ? 'boot huren, ' . ($boatLanding['name'] ?? '') . ', ' . ($boatLanding['id'] ?? '') . ', botenverhuur, Giethoorn, Weerribben, Wanneperveen, sloep, kano' : 'botenverhuur weerribben, bootje huren weerribben, boot huren weerribben-wieden, fluisterboot weerribben, sloep huren overijssel, bootje huren wanneperveen, boot huren bij giethoorn, kano, kajak', ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="author" content="Nijenhuis Boat Rental">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="geo.region" content="NL-OV">
    <meta name="geo.placename" content="Wanneperveen">
    <meta name="geo.position" content="<?php echo SITE_LAT; ?>;<?php echo SITE_LONG; ?>">
    <meta name="ICBM" content="<?php echo SITE_LAT; ?>, <?php echo SITE_LONG; ?>">
    
    <!-- Canonical URL (path-only; no ?lang= on canonical) -->
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <?php
    $_homeMdPath = parse_url($canonicalUrl, PHP_URL_PATH);
    $_homeMdPath = rtrim($_homeMdPath ?: '', '/');
    if ($_homeMdPath === '' || $_homeMdPath === '/') {
        $_homeMdPath = '/index';
    }
    if (!preg_match('/\.(md|txt|json|php)$/', $_homeMdPath)):
    ?>
    <link rel="alternate" type="text/markdown" href="<?php echo htmlspecialchars(SITE_URL . $_homeMdPath . '.md'); ?>">
    <?php endif; ?>
    
    <!-- Hreflang: self-referencing nl + alternates; must match canonical path on boat URLs -->
    <link rel="alternate" hreflang="x-default" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <link rel="alternate" hreflang="nl" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <link rel="alternate" hreflang="en" href="<?php echo htmlspecialchars($canonicalUrl . $langQuerySep . 'lang=en'); ?>">
    <link rel="alternate" hreflang="de" href="<?php echo htmlspecialchars($canonicalUrl . $langQuerySep . 'lang=de'); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($ogTitle, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($ogDesc, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImageUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="<?php echo htmlspecialchars($ogDesc, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:site_name" content="<?php echo SITE_NAME; ?>">
    <meta property="og:locale" content="nl_NL">
    <meta property="og:locale:alternate" content="en_US">
    <meta property="og:locale:alternate" content="de_DE">

    <!-- Twitter/X Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($ogTitle, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($ogDesc, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImageUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:image:alt" content="<?php echo htmlspecialchars($ogDesc, ENT_QUOTES, 'UTF-8'); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    <link rel="apple-touch-icon" href="<?php echo assetPath('frontend/Images/logo-white.svg'); ?>">
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="<?php echo htmlspecialchars($preloadImage, ENT_QUOTES, 'UTF-8'); ?>" as="image" fetchpriority="high">
    <link rel="preload" href="<?php echo assetPath('frontend/css/styles.css'); ?>" as="style">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/pages/home.css'); ?>" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="<?php echo assetPath('js/booking-system.css'); ?>" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="<?php echo assetPath('frontend/css/pages/boats.css'); ?>" media="print" onload="this.media='all'">

    <!-- Schema.org Structured Data - LocalBusiness (shared) -->
    <?php include __DIR__ . '/../components/schema-localbusiness.php'; ?>
    <?php if (!empty($productLdJson)): ?>
    <script type="application/ld+json" id="schema-boat-product">
    <?php echo json_encode($productLdJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>

    </script>
    <?php endif; ?>
    
    <!-- JavaScript -->
    <script>
        // Inject PHP Configuration into JS
        window.SiteConfig = {
            seasonStart: { 
                month: <?php echo SEASON_START_MONTH; ?>, 
                day: <?php echo SEASON_START_DAY; ?> 
            },
            seasonEnd: { 
                month: <?php echo SEASON_END_MONTH; ?>, 
                day: <?php echo SEASON_END_DAY; ?> 
            },
            serverPort: <?php echo isDevelopment() ? 8000 : 80; ?>
        };
    </script>
    <!-- Critical scripts only - other scripts loaded in footer with defer -->
    <script defer src="<?php echo assetPath('frontend/src/js/core/security.js'); ?>"></script>
    <script defer src="<?php echo assetPath('frontend/src/js/core/shared.js'); ?>"></script>

</head>
<body data-page="home"<?php echo $boatLanding ? ' data-boat-id="' . htmlspecialchars($boatLanding['id'] ?? '', ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
    <?php include __DIR__ . '/../components/topbar.php'; ?>
    <?php include __DIR__ . '/../components/nav.php'; ?>

    <?php if ($boatLanding): ?>
    <section class="boat-landing-hero" aria-label="Bootinformatie" style="background: #f1f5f9; padding: 2rem 0; border-bottom: 1px solid #e2e8f0;">
        <div class="container" style="max-width: 920px;">
            <h1 style="font-size: clamp(1.5rem, 4vw, 2rem); margin: 0 0 1rem; color: var(--primary-color, #003366); line-height: 1.25;">
                <?php echo htmlspecialchars($boatLanding['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> huren — Giethoorn &amp; Weerribben
            </h1>
            <p style="margin: 0 0 1.25rem; color: var(--text-secondary, #475569); font-size: 1rem;">
                Capaciteit: <strong><?php echo htmlspecialchars($boatLanding['passengerCount'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
                · vanaf <strong>€<?php echo htmlspecialchars((string) (int) ($boatLanding['pricePerDay'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></strong> per dag
                · <?php echo SITE_NAME; ?>, Wanneperveen
            </p>
            <?php
            $boatImg = $boatLanding['headerImage'] ?? $boatLanding['image'] ?? '';
            $boatImg = preg_replace('#^\.\./#', '', str_replace('\\', '/', $boatImg));
            $boatImg = ltrim($boatImg, '/');
            if ($boatImg !== '') {
                echo responsiveImage($boatImg, ($boatLanding['name'] ?? 'Boot') . ' huren bij Nijenhuis Botenverhuur', '(max-width: 768px) 100vw, 920px', ['loading' => 'eager', 'fetchpriority' => 'high', 'style' => 'width:100%;max-height:420px;object-fit:cover;border-radius:12px;margin-bottom:1.25rem;']);
            }
            ?>
            <div class="boat-landing-seo-body content-prose" style="color: #334155; line-height: 1.7; font-size: 1rem;">
                <?php echo $boatLandingSeoHtml; ?>
            </div>
            <p style="margin: 1.25rem 0 0;">
                <a href="#booking" class="btn" style="display: inline-block; margin-right: 0.75rem;">Direct boeken</a>
                <a href="/botenverhuur" class="btn btn-outline" style="display: inline-block;">Alle boten</a>
            </p>
        </div>
    </section>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-layout">
                <!-- Booking Form -->
                <div class="booking-form-modern" id="booking">
                    <div class="form-header">
                        <p class="form-subtitle" data-i18n="hero_book_h2">Direct boeken</p>
                        <p data-i18n="hero_book_p">Reserveer eenvoudig je boot voor een dag op het water</p>
                    </div>
                    <form id="bookingForm" autocomplete="off">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="boatType" data-i18n="hero_book_boat_type">Boot type</label>
                                <span class="input-icon" aria-hidden="true">⛵</span>
                                <select id="boatType" name="boatType" required aria-required="true">
                                    <option value="" data-i18n="hero_book_boat_type_select">Selecteer een boot</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="dateRange" data-i18n="hero_book_date">Datum</label>
                                <span class="input-icon" aria-hidden="true">📅</span>
                                <input type="text" id="dateRange" name="dateRange" readonly required aria-required="true" placeholder="Selecteer datums">
                                <input type="hidden" id="date" name="date">
                                <input type="hidden" id="rentalEndDate" name="rentalEndDate">
                                <div id="calendarContainer" class="calendar-container" style="display: none;"></div>
                                <div id="priceDisplay" style="display: none; margin-top: 1rem; padding: 1rem; background: #f8fafc; border-radius: 0.75rem; text-align: center;">
                                    <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Totaalprijs</div>
                                    <div id="priceAmount" style="font-size: 1.8rem; font-weight: 700; color: var(--primary-color);">€0.00</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row" id="engineOptionRow" style="display: none;">
                            <div class="form-group">
                                <label for="engineOption">Motor optie</label>
                                <span class="input-icon" aria-hidden="true">⚙️</span>
                                <select id="engineOption" name="engineOption">
                                    <option value="without">Zonder motor (€70)</option>
                                    <option value="with">Met motor (€85)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row" id="quantityRow" style="display: none;">
                            <div class="form-group">
                                <label for="boatQuantity">Aantal boten</label>
                                <span class="input-icon" aria-hidden="true">🚤</span>
                                <select id="boatQuantity" name="boatQuantity">
                                    <option value="1">1</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn" data-i18n="hero_book_btn">Boek nu</button>
                            <button type="button" id="addToCartBtn" class="btn" data-i18n="btn_add_to_cart" style="text-align:center;">🛒 Toevoegen aan winkelwagen</button>
                        </div>
                    </form>
                    

                </div>
                
                <div class="hero-content">
                    <?php if ($boatLanding): ?>
                    <p style="font-size: 1.15rem; font-weight: 600; margin: 0 0 0.5rem;"><?php echo htmlspecialchars($boatLanding['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p data-i18n="hero_h1_p">Reserveer direct — geen vaarbewijs nodig. Fluisterstil varen in de Weerribben.</p>
                    <?php else: ?>
                    <h1 data-i18n="hero_h1">Botenverhuur Weerribben-Wieden vanuit Wanneperveen</h1>
                    <p data-i18n="hero_h1_p">Huur een fluisterboot, luxe sloep, zeilboot, kano of SUP in Nationaal Park Weerribben-Wieden. Rustig vertrekken bij Wanneperveen, gratis parkeren en varen richting Giethoorn zonder toeristische startdrukte.</p>
                    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
                        <a href="/giethoorn" style="color: white; text-decoration: underline; font-weight: 500; opacity: 0.95;">
                            Ontdek Giethoorn →
                        </a>
                        <span style="color: white; opacity: 0.7;">|</span>
                        <a href="/weerribben" style="color: white; text-decoration: underline; font-weight: 500; opacity: 0.95;">
                            Varen in de Weerribben →
                        </a>
                        <span style="color: white; opacity: 0.7;">|</span>
                        <a href="/belt-schutsloot" style="color: white; text-decoration: underline; font-weight: 500; opacity: 0.95;">
                            Ontdek Belt-schutsloot →
                        </a>
                        <span style="color: white; opacity: 0.7;">|</span>
                        <a href="/wanneperveen" style="color: white; text-decoration: underline; font-weight: 500; opacity: 0.95;">
                            Ontdek Wanneperveen →
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main>
        <?php if (!$boatLanding): ?>
        <!-- Introduction Section -->
        <section class="intro-section">
            <div class="container">
                <div class="intro-grid">
                    <div class="intro-content">
                        <h2 data-i18n="intro_h2">Ontsnap aan de dagelijkse sleur met dé botenverhuur van de Weerribben</h2>
                        <p data-i18n="intro_h2_p">In onze drukke wereld snak iedereen naar rust. Laat files, stress en dagelijkse routine achter je – ontdek de Nationaal Park Weerribben-Wieden vanaf het water bij Nijenhuis Botenverhuur in Wanneperveen, dé bootverhuur van de Weerribben.</p>
                        <p data-i18n="intro_h2_p2">Verhuur boot voor quality time met familie of vrienden. Onze fluisterboten en electrosloepen glijden stil door smalste slootjes, weg van de massa. Creëer onvergetelijke momenten – perfect om even helemaal weg te zijn.</p>
                        <div class="deposit-notice" data-i18n="deposit_notice_cash"><strong>Let op:</strong> De borg dient contant te worden betaald bij aankomst.</div>
                    </div>
                    <div class="intro-features">
                        <h3 data-i18n="intro_h3">Waarom kiezen voor Nijenhuis?</h3>
                        <ul class="features-list">
                            <li data-i18n="intro_h3_li1">📍 Gelegen in het hart van het natuurgebied Weerribben</li>
                            <li data-i18n="intro_h3_li2">🚤 Breed assortiment boten voor alle voorkeuren</li>
                            <li data-i18n="intro_h3_li3">🌿 Milieuvriendelijke elektrische boten beschikbaar</li>
                            <li data-i18n="intro_h3_li4">👨‍👩‍👧‍👦 Perfect voor families en groepen</li>
                            <li data-i18n="intro_h3_li5">💰 Concurrentiële prijzen voor alle budgetten</li>
                            <li data-i18n="intro_h3_li6">📞 Persoonlijke service en ondersteuning</li>
                        </ul>
                        <div class="cta-box">
                            <p data-i18n="intro_cta_p"><strong>Voor meer informatie, bel <?php echo SITE_PHONE; ?></strong></p>
                            <p class="payment-note" data-i18n="intro_cta_p2">Contant en pin betalingen geaccepteerd</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Boat Fleet Section -->
        <section class="boat-fleet-section" id="boten" style="padding: 4rem 0; background: #f8fafc;">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="fleet_h2">Onze boten</h2>
                    <p data-i18n="fleet_p">Kies uit ons ruime aanbod van elektrische sloepen, zeilboten en kano's</p>
                </div>
                
                <!-- Note about hourly rental and direct booking -->
                <div style="max-width: 800px; margin: 0 auto 2rem; padding: 1rem 1.5rem; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 8px; text-align: center;">
                    <p style="margin: 0; color: #856404; font-size: 0.95rem; line-height: 1.6;" data-i18n="fleet_hourly_note">
                        ℹ️ Let op: Voor alle boten is het ook mogelijk om per uur te huren in plaats van per dag. 
                        Uurverhuur kan alleen direct ter plaatse bij de bootverhuur worden geboekt, niet online of telefonisch. 
                        Kom langs bij onze verhuurlocatie voor beschikbaarheid en directe boeking.
                    </p>
                </div>
                
                <!-- Boats Grid - Populated by JS -->
                <div id="boatFleetGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    <!-- Loading state -->
                    <div style="grid-column: 1/-1; text-align: center; padding: 2rem;">
                        <p>Boten laden...</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="services-section">
            <div class="container">
                <h2 data-i18n="services_h2">Onze diensten</h2>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon">🚤</div>
                        <h3 data-i18n="services_h3_1">Botenverhuur</h3>
                        <p data-i18n="services_p_1">Fluisterboten, electrosloepen, kano's, kajaks en SUP boards voor alle leeftijden. Bootjes verhuur.</p>
                        <div style="text-align:center;"><a href="/botenverhuur" class="btn btn-outline" data-i18n="services_btn_1">Bekijk Botenverhuur</a></div>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">🏠</div>
                        <h3 data-i18n="services_h3_2">Vakantiehuis</h3>
                        <p data-i18n="services_p_2">Comfortabele vakantie accommodatie perfect voor families en groepen.</p>
                        <div style="text-align:center;"><a href="/vakantiehuis" class="btn btn-outline" data-i18n="services_btn_2">Bekijk Vakantiehuis</a></div>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">🏕️</div>
                        <h3 data-i18n="services_h3_3">Camping</h3>
                        <p data-i18n="services_p_3">Prachtige kampeerplaatsen in het natuurgebied met moderne faciliteiten.</p>
                        <div style="text-align:center;"><a href="/camping" class="btn btn-outline" data-i18n="services_btn_3">Bekijk Camping</a></div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Discover Destinations Section -->
        <section class="content-section" style="background: #fff; padding: 4rem 0;">
            <div class="container">
                <div class="section-title" style="text-align: center; margin-bottom: 3rem;">
                    <h2 style="font-size: 2rem; margin-bottom: 1rem; color: var(--primary-color, #003366);">Ontdek de mooiste bestemmingen</h2>
                    <p style="font-size: 1.1rem; color: var(--text-secondary, #555);">Vaar door de Weerribben, naar Giethoorn of naar het rustige Belt-schutsloot</p>
                </div>
                
                <div class="destinations-grid-three">
                    <div class="destination-card-inner" style="background: linear-gradient(135deg, #eef8f1 0%, #d9f0df 100%); padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; border: 2px solid #2f8f46;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🌿</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--primary-color, #003366);">Weerribben-Wieden</h3>
                        <p style="line-height: 1.8; color: #666; margin-bottom: 1.5rem;">
                            Huur een boot voor het grootste laagveenmoeras van Noordwest-Europa. Vaar door rietlanden, stille slootjes en open meren vanaf ons rustige startpunt in Wanneperveen.
                        </p>
                        <a href="/weerribben"
                           style="display: inline-block; background: #2f8f46; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: transform 0.2s;">
                            Ontdek de Weerribben →
                        </a>
                    </div>

                    <div class="destination-card-inner" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🏘️</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--primary-color, #003366);">Giethoorn</h3>
                        <p style="line-height: 1.8; color: #666; margin-bottom: 1.5rem;">
                            Ontdek het beroemde "Venetië van het Noorden" met zijn idyllische grachten, meer dan 180 bruggetjes 
                            en karakteristieke rietgedekte boerderijen. Perfect voor een onvergetelijke vaartocht.
                        </p>
                        <a href="/giethoorn" 
                           style="display: inline-block; background: var(--primary-color, #003366); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: transform 0.2s;">
                            Ontdek Giethoorn →
                        </a>
                    </div>
                    
                    <div class="destination-card-inner" style="background: linear-gradient(135deg, #fff5e6 0%, #ffe6cc 100%); padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; border: 2px solid #ffa500;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">💎</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--primary-color, #003366);">Belt-schutsloot</h3>
                        <p style="line-height: 1.8; color: #666; margin-bottom: 1.5rem;">
                            Ontdek dit verborgen parel: dezelfde idyllische charme als Giethoorn, maar rustiger en minder toeristisch. 
                            Perfect voor wie op zoek is naar authenticiteit en rust.
                        </p>
                        <a href="/belt-schutsloot" 
                           style="display: inline-block; background: #ffa500; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: transform 0.2s;">
                            Ontdek Belt-schutsloot →
                        </a>
                    </div>

                    <div class="destination-card-inner" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; border: 2px solid #4caf50;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🌿</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--primary-color, #003366);">Wanneperveen</h3>
                        <p style="line-height: 1.8; color: #666; margin-bottom: 1.5rem;">
                            Jouw startpunt voor rustig varen. Gratis parkeren, geen vaarbewijs nodig en directe toegang tot de 
                            mooiste vaarwegen van de Weerribben.
                        </p>
                        <a href="/wanneperveen" 
                           style="display: inline-block; background: #4caf50; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: transform 0.2s;">
                            Ontdek Wanneperveen →
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Entity Definition Block - AI Optimized -->
        <section class="about-section" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 4rem 0;">
            <div class="container">
                <div class="section-title">
                    <h2 data-i18n="home_about_title">Over Nijenhuis Botenverhuur</h2>
                    <p data-i18n="home_about_tagline">Al meer dan 50 jaar jouw vertrouwde partner voor watersport in de Weerribben</p>
                </div>
                
                <div class="about-content" style="max-width: 1000px; margin: 0 auto;">
                    <div style="text-align: center; margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.8; color: var(--text-secondary);">
                        <p>
                            <strong>Nijenhuis Botenverhuur</strong> is een familiebedrijf voor bootjes verhuur en bootje huren, met seizoenscamping, 
                            gelegen aan de <strong>Veneweg 199 in Wanneperveen</strong>, direct aan 
                            <strong>Nationaal Park Weerribben-Wieden</strong> – het grootste laagveenmoeras van Noordwest-Europa. 
                            Vanuit deze rustige locatie huur je een boot voor de Weerribben, Giethoorn, Belt-schutsloot en de Beulakerwijde.
                        </p>
                    </div>
                    
                    <div class="facts-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
                        <div class="fact-card" style="background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: transform 0.3s ease; display: flex; flex-direction: column; align-items: center; text-align: center;">
                            <div style="width: 60px; height: 60px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1rem;">📍</div>
                            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color); font-size: 1.25rem;" data-i18n="about_location_title">Locatie</h3>
                            <p style="margin: 0; color: var(--text-secondary);" data-i18n="about_location_desc">
                                Wanneperveen, Overijssel<br>
                                <span class="fact-sub">
                                    10 km van <a href="/giethoorn" style="color: var(--primary-color, #003366); text-decoration: underline; font-weight: 500;">Giethoorn</a>
                                </span>
                            </p>
                        </div>
                        
                        <div class="fact-card" style="background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: transform 0.3s ease; display: flex; flex-direction: column; align-items: center; text-align: center;">
                            <div style="width: 60px; height: 60px; background: rgba(16, 185, 129, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1rem;">📅</div>
                            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color); font-size: 1.25rem;" data-i18n="about_season_title">Seizoen</h3>
                            <p style="margin: 0; color: var(--text-secondary);" data-i18n="about_season_desc">1 april – 31 oktober<br><span class="fact-sub">Dagelijks 09:00-18:00</span></p>
                        </div>
                        
                        <div class="fact-card" style="background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: transform 0.3s ease; display: flex; flex-direction: column; align-items: center; text-align: center;">
                            <div style="width: 60px; height: 60px; background: rgba(245, 158, 11, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1rem;">🚤</div>
                            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color); font-size: 1.25rem;" data-i18n="about_fleet_title">Boten</h3>
                            <p style="margin: 0; color: var(--text-secondary);" data-i18n="about_fleet_desc">25+ vaartuigen<br><span class="fact-sub">1 tot 12 personen</span></p>
                        </div>
                        
                        <div class="fact-card" style="background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: transform 0.3s ease; display: flex; flex-direction: column; align-items: center; text-align: center;">
                            <div style="width: 60px; height: 60px; background: rgba(99, 102, 241, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1rem;">💳</div>
                            <h3 style="margin: 0 0 0.5rem; color: var(--primary-color); font-size: 1.25rem;" data-i18n="about_prices_title">Prijzen</h3>
                            <p style="margin: 0; color: var(--text-secondary);" data-i18n="about_prices_desc">Vanaf €20/dag<br><span class="fact-sub">Geen vaarbewijs nodig</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive Boat Finder (Moved to botenverhuur.php) -->

        <!-- Testimonials Section -->
        <section class="testimonials-section" style="display: none; background: var(--secondary-color); padding: 4rem 0; color: white;">
            <div class="container">
                <div class="section-title" style="color: white;">
                    <h2 style="color: white;">Wat onze gasten zeggen</h2>
                    <p style="color: rgba(255,255,255,0.8);">Ervaringen van bezoekers aan Nijenhuis Botenverhuur</p>
                </div>
                
                <div class="testimonials-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                    
                    <div class="testimonial-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.2);">
                        <div style="display: flex; gap: 0.25rem; margin-bottom: 1rem;">
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                        </div>
                        <p style="font-style: italic; line-height: 1.7; margin-bottom: 1rem;">"Fantastische dag gehad met het hele gezin! De electrosloep was makkelijk te besturen en we konden heerlijk picknicken op het water. Aanrader!"</p>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">F</div>
                            <div>
                                <strong>Familie de Vries</strong>
                                <p style="margin: 0; font-size: 0.85rem; opacity: 0.8;">Amsterdam</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.2);">
                        <div style="display: flex; gap: 0.25rem; margin-bottom: 1rem;">
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                        </div>
                        <p style="font-style: italic; line-height: 1.7; margin-bottom: 1rem;">"Al jaren komen we hier met de caravan. De rust, de natuur en de vriendelijke service maken het elke keer weer speciaal. Echt een verborgen parel!"</p>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">J</div>
                            <div>
                                <strong>Jan & Marieke</strong>
                                <p style="margin: 0; font-size: 0.85rem; opacity: 0.8;">Seizoengasten camping</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 1.5rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.2);">
                        <div style="display: flex; gap: 0.25rem; margin-bottom: 1rem;">
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                            <span style="color: #fbbf24;">★</span>
                        </div>
                        <p style="font-style: italic; line-height: 1.7; margin-bottom: 1rem;">"Met de kajak door de kleine slootjes, vogelgeluiden overal... Dit is het echte Nederland! Mooier dan Giethoorn zelf, veel rustiger hier."</p>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; background: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">S</div>
                            <div>
                                <strong>Stefan</strong>
                                <p style="margin: 0; font-size: 0.85rem; opacity: 0.8;">Duitsland</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php endif; ?>

        <!-- Map Section -->
        <section class="map-section">
            <div class="container">
                <h2 data-i18n="map_h2">Vind ons</h2>
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d77373.91668916645!2d6.077958504433576!3d52.69726901355547!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c871953a3891e5%3A0x1a70802adc308067!2sVeneweg+199%2C+7946+LP+Wanneperveen!5e0!3m2!1sen!2snl!4v1552921192864" 
                        width="100%" 
                        height="400" 
                        style="border:0; border-radius: var(--border-radius);" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Locatie Nijenhuis Botenverhuur - Veneweg 199, Wanneperveen">
                    </iframe>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../components/footer.php'; ?>


    <!-- Boat Info Modal -->
    <div id="boatInfoModal" class="boat-info-modal">
        <div class="boat-info-content">
            <button class="boat-info-close" id="boatInfoClose" aria-label="Sluiten">&times;</button>
            <div id="boatInfoBody"></div>
        </div>
    </div>

    <!-- Availability Calendar Modal -->
    <div id="availabilityCalendarModal" class="availability-calendar-modal">
        <div class="calendar-modal-content">
            <button class="calendar-modal-close" id="calendarModalClose" aria-label="Sluiten">&times;</button>
            <div class="calendar-header">
                <h2 id="calendarBoatName">Boot beschikbaarheid</h2>
                <p class="calendar-legend">
                    <span class="legend-item"><span class="legend-dot available"></span> Beschikbaar</span>
                    <span class="legend-item"><span class="legend-dot unavailable"></span> Niet beschikbaar</span>
                    <span class="legend-item"><span class="legend-dot selected"></span> Geselecteerd</span>
                </p>
            </div>
            
            <div class="calendar-navigation">
                <button class="calendar-nav-btn" id="prevMonth">❮</button>
                <h3 id="currentMonthYear"></h3>
                <button class="calendar-nav-btn" id="nextMonth">❯</button>
            </div>
            
            <div class="calendar-grid" id="calendarGrid">
                <!-- Calendar will be generated here -->
            </div>
            
            <div class="calendar-selection-info" id="selectionInfo" style="display:none; margin-top: 15px; padding: 10px; background: #e8f5e9; border-radius: 8px; text-align: center;">
                <p style="margin: 0; font-weight: 600;">Geselecteerde periode:</p>
                <p id="selectionRangeText" style="margin: 5px 0;">-</p>
                <p id="selectionPriceText" style="margin: 5px 0; font-size: 1.1em; color: var(--primary-color);">Totaal: €0</p>
            </div>
            <div id="selectionError" style="display:none; margin-top: 10px; color: #c62828; text-align: center; font-size: 0.9em;">
                De geselecteerde periode bevat niet-beschikbare dagen.
            </div>

            <div id="boatOptions" class="calendar-options" style="margin-top: 10px; text-align: center;"></div>

            <div class="calendar-actions">
                <button class="btn btn-primary disabled" id="addToCartBtn" onclick="addCurrentBoatToCart()">🛒 Toevoegen aan winkelwagen</button>
            </div>
        </div>
    </div>

    <!-- New Modular Scripts -->
    <script defer src="<?php echo assetPath('frontend/src/js/hooks/useBoatData.js'); ?>"></script>
    <script defer src="<?php echo assetPath('frontend/src/js/hooks/useBookingAvailability.js'); ?>"></script>
    <script defer src="<?php echo assetPath('frontend/src/js/pages/home.js'); ?>"></script>
</body>
</html>
