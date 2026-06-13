#!/usr/bin/env php
<?php
/**
 * CLI one-time migration: split bookings with quantity > 1 into separate records (one per boat).
 * Run from project root: php admin/run-migrate-bookings-quantity.php
 * Or from admin: php run-migrate-bookings-quantity.php
 */
if (php_sapi_name() !== 'cli') {
    die('Run this script from the command line only.');
}

require_once __DIR__ . '/../components/data_access.php';
loadEnvSafe(__DIR__ . '/../.env');
$bookingsFile = nijenhuis_data_path('bookings.json');
$bookingsArchiveFile = nijenhuis_data_path('bookings_archive.json');

function loadJson($path) {
    if (!file_exists($path)) return [];
    $raw = @file_get_contents($path);
    if ($raw === false) return [];
    $data = @json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function saveJson($path, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return $json !== false && file_put_contents($path, $json) !== false;
}

$splitActive = 0;
$splitArchive = 0;

// Active bookings
$bookings = loadJson($bookingsFile);
$newBookings = [];
foreach ($bookings as $b) {
    $qty = isset($b['quantity']) ? max(1, (int) $b['quantity']) : 1;
    if ($qty <= 1) {
        $newBookings[] = $b;
        continue;
    }
    $splitActive++;
    $amountEach = isset($b['amount']) ? round((float) $b['amount'] / $qty, 2) : 0;
    $baseId = $b['id'] ?? ('mig_' . uniqid());
    for ($i = 0; $i < $qty; $i++) {
        $entry = $b;
        $entry['quantity'] = 1;
        $entry['amount'] = $amountEach;
        $entry['id'] = $i === 0 ? $baseId : ($baseId . '_' . ($i + 1));
        $newBookings[] = $entry;
    }
}
if ($splitActive > 0) {
    if (!saveJson($bookingsFile, $newBookings)) {
        fwrite(STDERR, "Failed to write $bookingsFile\n");
        exit(1);
    }
    echo "Active: split $splitActive booking(s) in " . basename($bookingsFile) . "\n";
} else {
    echo "Active: no bookings with quantity > 1 in " . basename($bookingsFile) . "\n";
}

// Archive
$archive = loadJson($bookingsArchiveFile);
$newArchive = [];
foreach ($archive as $b) {
    $qty = isset($b['quantity']) ? max(1, (int) $b['quantity']) : 1;
    if ($qty <= 1) {
        $newArchive[] = $b;
        continue;
    }
    $splitArchive++;
    $amountEach = isset($b['amount']) ? round((float) $b['amount'] / $qty, 2) : 0;
    $baseId = $b['id'] ?? ('mig_arch_' . uniqid());
    for ($i = 0; $i < $qty; $i++) {
        $entry = $b;
        $entry['quantity'] = 1;
        $entry['amount'] = $amountEach;
        $entry['id'] = $i === 0 ? $baseId : ($baseId . '_' . ($i + 1));
        $newArchive[] = $entry;
    }
}
if ($splitArchive > 0) {
    if (!saveJson($bookingsArchiveFile, $newArchive)) {
        fwrite(STDERR, "Failed to write $bookingsArchiveFile\n");
        exit(1);
    }
    echo "Archive: split $splitArchive booking(s) in " . basename($bookingsArchiveFile) . "\n";
} else {
    echo "Archive: no bookings with quantity > 1 in " . basename($bookingsArchiveFile) . "\n";
}

echo "Migration complete: one record per boat.\n";
