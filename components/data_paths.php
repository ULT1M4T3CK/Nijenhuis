<?php
/**
 * Central paths for operational data stored OUTSIDE the web-exposed JSON surface.
 * All JSON files live under data/; nginx/Apache deny direct *.json access.
 */

if (!defined('NIJENHUIS_ROOT')) {
    define('NIJENHUIS_ROOT', dirname(__DIR__));
}

/**
 * @return string Absolute path to the data directory (no trailing slash)
 */
function nijenhuis_data_dir(): string {
    $override = getenv('DATA_DIR') ?: ($_ENV['DATA_DIR'] ?? '');
    $override = is_string($override) ? trim($override) : '';
    if ($override !== '' && is_dir($override)) {
        return rtrim($override, '/');
    }
    return NIJENHUIS_ROOT . '/data';
}

/**
 * @param string $filename Basename only (e.g. bookings.json)
 */
function nijenhuis_data_path(string $filename): string {
    return nijenhuis_data_dir() . '/' . ltrim($filename, '/');
}

/**
 * One-time copy from legacy locations (admin/*.json, content/*.json) when data/ is empty.
 * Call after loadEnvSafe() so DATA_DIR is available.
 */
function nijenhuis_migrate_legacy_data_files(): void {
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $dataDir = nijenhuis_data_dir();
    if (!is_dir($dataDir)) {
        @mkdir($dataDir, 0750, true);
    }

    $pairs = [
        NIJENHUIS_ROOT . '/admin/bookings.json'           => nijenhuis_data_path('bookings.json'),
        NIJENHUIS_ROOT . '/admin/bookings_archive.json'   => nijenhuis_data_path('bookings_archive.json'),
        NIJENHUIS_ROOT . '/admin/boats.json'              => nijenhuis_data_path('boats.json'),
        NIJENHUIS_ROOT . '/admin/for-sale.json'           => nijenhuis_data_path('for-sale.json'),
        NIJENHUIS_ROOT . '/content/articles.json'         => nijenhuis_data_path('articles.json'),
        NIJENHUIS_ROOT . '/content/blog-styles.json'      => nijenhuis_data_path('blog-styles.json'),
        NIJENHUIS_ROOT . '/content/blog-redirects.json'   => nijenhuis_data_path('blog-redirects.json'),
    ];

    foreach ($pairs as $legacy => $target) {
        if (file_exists($target) || !file_exists($legacy)) {
            continue;
        }
        if (@copy($legacy, $target)) {
            error_log('nijenhuis: migrated legacy data file to ' . $target);
        }
    }
}
