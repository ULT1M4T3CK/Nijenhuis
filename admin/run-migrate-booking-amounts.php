#!/usr/bin/env php
<?php
/**
 * CLI one-time migration: Recalculate booking amounts using 1/7th of weekly price for extra days.
 * Use this after deploying the pricing_engine.php fix to ensure existing bookings reflect correct costs.
 *
 * Run from project root: php admin/run-migrate-booking-amounts.php
 * Or from admin: php run-migrate-booking-amounts.php
 */
if (php_sapi_name() !== 'cli') {
    die('Run this script from the command line only.');
}

// Bootstrap: load pricing engine and data paths
$projectRoot = realpath(__DIR__ . '/..');
require_once $projectRoot . '/components/data_access.php';
loadEnvSafe($projectRoot . '/.env');
require_once $projectRoot . '/components/pricing_engine.php';
$bookingsFile = nijenhuis_data_path('bookings.json');
$bookingsArchiveFile = nijenhuis_data_path('bookings_archive.json');
$boatsFile = nijenhuis_data_path('boats.json');

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

function recalcBookingAmount($b, $boats) {
    $boatType = $b['boatType'] ?? '';
    if (empty($boatType)) return null;

    $startDate = $b['date'] ?? '';
    $endDate = $b['endDate'] ?? $startDate;

    $days = 1;
    if (!empty($startDate) && !empty($endDate)) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $diff = $start->diff($end);
        $days = $diff->days + 1;
    }
    if ($days < 1) $days = 1;

    $useMotor = (($b['engineOption'] ?? 'without') === 'with');

    return calculateBoatPrice($boatType, $days, $boats, $useMotor);
}

$boats = loadJson($boatsFile);
if (empty($boats)) {
    fwrite(STDERR, "No boats found in $boatsFile. Cannot recalculate.\n");
    exit(1);
}

$updatedActive = 0;
$updatedArchive = 0;
$skippedActive = 0;
$skippedArchive = 0;

// Process active bookings
$bookings = loadJson($bookingsFile);
foreach ($bookings as &$b) {
    $newAmount = recalcBookingAmount($b, $boats);
    if ($newAmount === null) {
        $skippedActive++;
        continue;
    }
    $oldAmount = (float)($b['amount'] ?? 0);
    $newAmount = round((float)$newAmount, 2);
    if (abs($oldAmount - $newAmount) > 0.001) {
        $b['amount'] = $newAmount;
        $b['updatedAt'] = date('c');
        $updatedActive++;
    }
}
unset($b);

if ($updatedActive > 0) {
    if (!saveJson($bookingsFile, $bookings)) {
        fwrite(STDERR, "Failed to write $bookingsFile\n");
        exit(1);
    }
    echo "Active: updated $updatedActive booking(s) in " . basename($bookingsFile) . "\n";
} else {
    echo "Active: no amount changes needed in " . basename($bookingsFile) . "\n";
}
if ($skippedActive > 0) {
    echo "Active: skipped $skippedActive booking(s) (no boatType)\n";
}

// Process archive
$archive = loadJson($bookingsArchiveFile);
foreach ($archive as &$b) {
    $newAmount = recalcBookingAmount($b, $boats);
    if ($newAmount === null) {
        $skippedArchive++;
        continue;
    }
    $oldAmount = (float)($b['amount'] ?? 0);
    $newAmount = round((float)$newAmount, 2);
    if (abs($oldAmount - $newAmount) > 0.001) {
        $b['amount'] = $newAmount;
        $b['updatedAt'] = date('c');
        $updatedArchive++;
    }
}
unset($b);

if ($updatedArchive > 0) {
    if (!saveJson($bookingsArchiveFile, $archive)) {
        fwrite(STDERR, "Failed to write $bookingsArchiveFile\n");
        exit(1);
    }
    echo "Archive: updated $updatedArchive booking(s) in " . basename($bookingsArchiveFile) . "\n";
} else {
    echo "Archive: no amount changes needed in " . basename($bookingsArchiveFile) . "\n";
}
if ($skippedArchive > 0) {
    echo "Archive: skipped $skippedArchive booking(s) (no boatType)\n";
}

echo "Migration complete: booking amounts recalculated (1/7th of weekly price per extra day).\n";
