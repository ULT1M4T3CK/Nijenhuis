<?php
// ========================================================================
// TEST BOOKING SYSTEM
// Simple test to debug booking issues
// ========================================================================

header('Content-Type: application/json');

// Test configuration
$bookingsFile = 'bookings.json';

// Function to load bookings
function loadBookings($file) {
    if (file_exists($file)) {
        $data = file_get_contents($file);
        return json_decode($data, true) ?: [];
    }
    return [];
}

// Function to save bookings
function saveBookings($file, $bookings) {
    return file_put_contents($file, json_encode($bookings, JSON_PRETTY_PRINT));
}

// Function to generate unique ID
function generateId() {
    return uniqid() . '_' . time();
}

// Test data
$testBooking = [
    'id' => generateId(),
    'date' => '2024-01-20',
    'boatType' => 'classic-tender-720',
    'customerName' => 'Test Customer',
    'customerEmail' => 'test@example.com',
    'customerPhone' => '+31 6 12345678',
    'notes' => 'Test booking',
    'status' => 'not-confirmed',
    'createdAt' => date('c'),
    'updatedAt' => date('c')
];

// Test file operations
$bookings = loadBookings($bookingsFile);
$bookings[] = $testBooking;

$result = [
    'file_exists' => file_exists($bookingsFile),
    'file_writable' => is_writable($bookingsFile) || is_writable(dirname($bookingsFile)),
    'current_bookings_count' => count($bookings),
    'test_booking' => $testBooking,
    'save_result' => saveBookings($bookingsFile, $bookings),
    'php_version' => phpversion(),
    'error_log' => error_get_last()
];

echo json_encode($result, JSON_PRETTY_PRINT);
?> 