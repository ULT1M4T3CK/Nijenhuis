<?php
/**
 * ========================================================================
 * SITE CONFIGURATION - Nijenhuis Botenverhuur
 * ========================================================================
 * Centralized configuration for all site-wide settings
 */

// Prevent direct access
if (!defined('NIJENHUIS_SITE')) {
    define('NIJENHUIS_SITE', true);
}

// Site Information
define('SITE_URL', 'https://nijenhuis-botenverhuur.com');
define('INDEXNOW_API_KEY', 'b4d8e2f1a0c34567890abcdef1234567');
// GA4 Measurement ID for analytics (set to enable; create custom dimension "Language" as dimension1)
define('GA4_MEASUREMENT_ID', ''); // e.g. 'G-XXXXXXXXXX'
define('SITE_NAME', 'Nijenhuis Botenverhuur');
define('SITE_TAGLINE', 'Botenverhuur & Camping');
define('SITE_DESCRIPTION', 'Botenverhuur in het prachtige natuurgebied Weerribben bij Giethoorn.');

/** Administrative fee on online booking payments (% of rental subtotal). Set to 0 to disable. */
define('BOOKING_ADMIN_FEE_PERCENT', 0.0);

/**
 * Pay-on-arrival: non-refundable reservation = BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT of rental
 * (plus BOOKING_ADMIN_FEE_PERCENT on that slice only when the admin rate is above 0). Remainder due on arrival.
 */
define('BOOKING_PAY_ON_ARRIVAL_RESERVATION_FEE_PERCENT', 10.0);

/** Payment method id: settle at the harbour (not via Mollie). */
define('CHECKOUT_PAY_ON_ARRIVAL_METHOD', 'pay_on_arrival');

/**
 * Mollie hosted checkout payment methods (activate each in the Mollie dashboard).
 * @return list<string>
 */
function getMollieCheckoutMethods(): array {
    return ['ideal', 'bancontact', 'applepay', 'googlepay'];
}

/** Wallet / one-click methods shown as branded buttons above the radio list. */
define('CHECKOUT_WALLET_METHODS', ['applepay', 'googlepay']);

/**
 * All selectable methods on checkout / booking (Mollie + pay on arrival).
 * @return list<string>
 */
function getCheckoutPaymentMethods(): array {
    return array_merge(getMollieCheckoutMethods(), [CHECKOUT_PAY_ON_ARRIVAL_METHOD]);
}

// Contact Information
define('SITE_PHONE', '0522 281 528');
define('SITE_PHONE_LINK', '+31522281528');
define('SITE_ADDRESS', 'Veneweg 199');
define('SITE_POSTAL', '7946 LP Wanneperveen');
define('SITE_COUNTRY', 'Nederland');
define('SITE_KVK', '6769 7097');
define('SITE_BTW', 'NL857 1361 48 B01');

// Geo Coordinates (Veneweg 199, Wanneperveen)
define('SITE_LAT', 52.697269);
define('SITE_LONG', 6.077958);

// Opening Hours
define('SITE_HOURS', '9:00 - 18:00');
define('SITE_SEASON_START', '1 april');
define('SITE_SEASON_END', '31 oktober');

// Season Date Parts (for logic)
// Season Date Parts (for logic)
define('SEASON_START_MONTH', 4);
define('SEASON_START_DAY', 1);
define('SEASON_END_MONTH', 10);
define('SEASON_END_DAY', 31);

// Booking Window (Bookings open on this date for the upcoming season)
define('BOOKING_OPEN_MONTH', 1);
define('BOOKING_OPEN_DAY', 1);

// Paths (relative to site root)
define('PATH_STYLES', '/frontend/css/styles.css?v=3');
define('PATH_LOGO', '/frontend/Images/logo-white.svg');
define('PATH_JS_SECURITY', '/frontend/src/js/core/security.js');
define('PATH_JS_SHARED', '/frontend/src/js/core/shared.js');
define('PATH_JS_BOOKING', '/frontend/src/js/booking/booking-system.js');
define('PATH_JS_TRANSLATION', '/frontend/src/js/core/translation-core.js?v=1');
define('PATH_JS_PAYMENT', '/frontend/src/js/booking/mollie-payment.js');
define('PATH_JS_TE_KOOP', '/frontend/src/js/pages/te-koop.js');
define('PATH_JS_CART', '/frontend/src/js/booking/cart.js');
define('PATH_BOAT_DATA', '/js/boat-data-service.js');

// Main navigation items (Blog in "More" is appended below after isDevelopment + articles check)
$NAV_ITEMS = [
    ['href' => '/botenverhuur', 'i18n' => 'nav_boats', 'label' => 'Botenverhuur'],
    ['href' => '/vakantiehuis', 'i18n' => 'nav_house', 'label' => 'Vakantiehuis'],
    ['href' => '/te-koop', 'i18n' => 'nav_forsale', 'label' => 'Te Koop'],
    ['href' => '/camping', 'i18n' => 'nav_camping', 'label' => 'Camping'],
];

// Footer column 1 (Blog added to column 2 below)
$FOOTER_NAV_COL1 = [
    ['href' => '/botenverhuur', 'i18n' => 'nav_boats', 'label' => 'Botenverhuur'],
    ['href' => '/vakantiehuis', 'i18n' => 'nav_house', 'label' => 'Vakantiehuis'],
    ['href' => '/te-koop', 'i18n' => 'nav_forsale', 'label' => 'Te Koop'],
    ['href' => '/camping', 'i18n' => 'nav_camping', 'label' => 'Camping'],
];

/**
 * Get web root-absolute path for static assets (CSS, JS, images).
 * In production, .css and .js paths are rewritten to .min.css / .min.js when minified files exist.
 * Use this for href/src to avoid ../ in URLs and SEO/crawler issues.
 */
function assetPath($path) {
    $path = ltrim(str_replace('\\', '/', $path), '/');
    if (preg_match('/^(.+\.)(css|js)(\?.*)?$/i', $path, $m)) {
        $query = isset($m[3]) ? $m[3] : '';
        $docRoot = isset($_SERVER['DOCUMENT_ROOT'])
            ? rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/')
            : '';
        $assetRelative = $m[1] . $m[2];

        if (!isDevelopment()) {
            $minRelative = $m[1] . 'min.' . $m[2];
            if ($docRoot !== '' && is_file($docRoot . '/' . $minRelative)) {
                $assetRelative = $minRelative;
            }
        }

        if ($docRoot !== '' && is_file($docRoot . '/' . $assetRelative)) {
            $versionSeparator = $query === '' ? '?' : '&';
            $versionParam = $query === '' ? 'v' : 'asset_v';
            $query .= $versionSeparator . $versionParam . '=' . filemtime($docRoot . '/' . $assetRelative);
        }

        $path = $assetRelative . $query;
    }
    return '/' . $path;
}

/**
 * Get the base path for includes based on current file location
 */
function getBasePath() {
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
    $currentDir = dirname($_SERVER['SCRIPT_FILENAME']);
    
    // Calculate relative path from current file to document root
    $relativePath = '';
    $depth = substr_count(str_replace($docRoot, '', $currentDir), DIRECTORY_SEPARATOR);
    
    for ($i = 0; $i < $depth; $i++) {
        $relativePath .= '../';
    }
    
    return rtrim($relativePath, '/');
}

/**
 * Determine if running in development mode
 */
function isDevelopment() {
    $serverName = $_SERVER['SERVER_NAME'] ?? '';
    $httpHost = $_SERVER['HTTP_HOST'] ?? '';
    $serverPort = (string)($_SERVER['SERVER_PORT'] ?? '');

    // Built-in PHP server sometimes reports SERVER_NAME differently (e.g. 0.0.0.0),
    // so also check HTTP_HOST and common local dev ports.
    $hostLooksLocal =
        strpos($serverName, 'localhost') !== false ||
        strpos($serverName, '127.0.0.1') !== false ||
        strpos($httpHost, 'localhost') !== false ||
        strpos($httpHost, '127.0.0.1') !== false;

    return $hostLooksLocal || in_array($serverPort, ['8000', '8888'], true);
}

require_once __DIR__ . '/../lib/blog-helpers.php';
require_once __DIR__ . '/../lib/responsive-image.php';
$BLOG_NAV = ['href' => '/blog', 'i18n' => 'nav_blog', 'label' => 'Blog'];
$blogNavVisible = isDevelopment() || blogPublicHasPublishedArticles();

$MORE_NAV_ITEMS = array_merge(
    [
        ['href' => '/vaarkaart', 'i18n' => 'nav_chart', 'label' => 'Vaarkaart'],
        ['href' => '/weerribben', 'i18n' => 'nav_weerribben', 'label' => 'Weerribben'],
        ['href' => '/veelgestelde-vragen', 'i18n' => 'nav_faq', 'label' => 'Veelgestelde vragen'],
    ],
    $blogNavVisible ? [$BLOG_NAV] : []
);

$FOOTER_NAV_COL2 = array_merge(
    $blogNavVisible ? [$BLOG_NAV] : [],
    [
        ['href' => '/vaarkaart', 'i18n' => 'nav_chart', 'label' => 'Vaarkaart'],
        ['href' => '/weerribben', 'i18n' => 'nav_weerribben', 'label' => 'Weerribben'],
        ['href' => '/veelgestelde-vragen', 'i18n' => 'nav_faq', 'label' => 'Veelgestelde vragen'],
        ['href' => '/contact', 'i18n' => 'nav_contact', 'label' => 'Contact'],
    ]
);
