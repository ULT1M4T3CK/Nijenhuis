<?php
// ========================================================================
// BOOKING HANDLER - INTEGRATES WEBSITE BOOKINGS WITH ADMIN SYSTEM
// ========================================================================

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Configuration
$bookingsFile = 'bookings.json';
$adminCredentials = [
    'username' => 'admin',
    'password' => 'nijenhuis2024'
];

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

// Function to validate booking data
function validateBooking($data) {
    $required = ['date', 'boatType', 'customerName', 'customerEmail', 'customerPhone'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return false;
        }
    }
    
    // Validate email
    if (!filter_var($data['customerEmail'], FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Validate date
    $date = DateTime::createFromFormat('Y-m-d', $data['date']);
    if (!$date || $date->format('Y-m-d') !== $data['date']) {
        return false;
    }
    
    return true;
}

// Function to send email notification
function sendBookingNotification($booking) {
    $to = 'info@nijenhuis-botenverhuur.nl';
    $subject = 'New Booking Request - Nijenhuis Botenverhuur';
    
    $message = "A new booking has been submitted:\n\n";
    $message .= "Date: " . $booking['date'] . "\n";
    $message .= "Boat Type: " . $booking['boatType'] . "\n";
    $message .= "Customer: " . $booking['customerName'] . "\n";
    $message .= "Email: " . $booking['customerEmail'] . "\n";
    $message .= "Phone: " . $booking['customerPhone'] . "\n";
    if (!empty($booking['notes'])) {
        $message .= "Notes: " . $booking['notes'] . "\n";
    }
    $message .= "\nStatus: Not Confirmed\n";
    $message .= "Booking ID: " . $booking['id'] . "\n";
    
    $headers = 'From: noreply@nijenhuis-botenverhuur.nl' . "\r\n" .
               'Reply-To: ' . $booking['customerEmail'] . "\r\n" .
               'X-Mailer: PHP/' . phpversion();
    
    return mail($to, $subject, $message, $headers);
}

// Handle different request types
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Debug logging
        error_log('Booking handler received POST request');
        error_log('Raw input: ' . file_get_contents('php://input'));
        error_log('Decoded input: ' . print_r($input, true));
        
        // Check if this is a login request
        if (isset($input['action']) && $input['action'] === 'login') {
            if ($input['username'] === $adminCredentials['username'] && 
                $input['password'] === $adminCredentials['password']) {
                echo json_encode(['success' => true, 'message' => 'Login successful']);
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
            }
            exit;
        }
        
        // Check if this is a booking submission from the main website
        if (isset($input['formType']) && $input['formType'] === 'booking') {
            error_log('Processing booking submission');
            $bookingData = [
                'date' => $input['date'] ?? '',
                'boatType' => $input['boatType'] ?? '',
                'customerName' => $input['customerName'] ?? '',
                'customerEmail' => $input['customerEmail'] ?? '',
                'customerPhone' => $input['customerPhone'] ?? '',
                'notes' => $input['notes'] ?? ''
            ];
            
            if (!validateBooking($bookingData)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid booking data']);
                exit;
            }
            
            // Create new booking
            $newBooking = [
                'id' => generateId(),
                'date' => $bookingData['date'],
                'boatType' => $bookingData['boatType'],
                'customerName' => $bookingData['customerName'],
                'customerEmail' => $bookingData['customerEmail'],
                'customerPhone' => $bookingData['customerPhone'],
                'notes' => $bookingData['notes'],
                'status' => 'not-confirmed',
                'createdAt' => date('c'),
                'updatedAt' => date('c')
            ];
            
            // Load existing bookings
            $bookings = loadBookings($bookingsFile);
            $bookings[] = $newBooking;
            
            // Save bookings
            if (saveBookings($bookingsFile, $bookings)) {
                // Send email notification
                sendBookingNotification($newBooking);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Booking submitted successfully',
                    'bookingId' => $newBooking['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save booking']);
            }
            exit;
        }
        
        // Check if this is an admin action
        if (isset($input['action'])) {
            switch ($input['action']) {
                case 'getBookings':
                    $bookings = loadBookings($bookingsFile);
                    echo json_encode(['success' => true, 'bookings' => $bookings]);
                    break;
                    
                case 'createBooking':
                    if (empty($input['bookingData'])) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Booking data required']);
                        exit;
                    }
                    
                    $bookingData = $input['bookingData'];
                    if (!validateBooking($bookingData)) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Invalid booking data']);
                        exit;
                    }
                    
                    // Create new booking
                    $newBooking = [
                        'id' => generateId(),
                        'date' => $bookingData['date'],
                        'boatType' => $bookingData['boatType'],
                        'customerName' => $bookingData['customerName'],
                        'customerEmail' => $bookingData['customerEmail'],
                        'customerPhone' => $bookingData['customerPhone'],
                        'notes' => $bookingData['notes'] ?? '',
                        'status' => $bookingData['status'] ?? 'not-confirmed',
                        'createdAt' => date('c'),
                        'updatedAt' => date('c')
                    ];
                    
                    // Load existing bookings
                    $bookings = loadBookings($bookingsFile);
                    $bookings[] = $newBooking;
                    
                    // Save bookings
                    if (saveBookings($bookingsFile, $bookings)) {
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Booking created successfully',
                            'bookingId' => $newBooking['id']
                        ]);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Failed to create booking']);
                    }
                    break;
                    
                case 'updateBooking':
                    if (empty($input['bookingId'])) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Booking ID required']);
                        exit;
                    }
                    
                    $bookings = loadBookings($bookingsFile);
                    $bookingIndex = -1;
                    
                    foreach ($bookings as $index => $booking) {
                        if ($booking['id'] === $input['bookingId']) {
                            $bookingIndex = $index;
                            break;
                        }
                    }
                    
                    if ($bookingIndex === -1) {
                        http_response_code(404);
                        echo json_encode(['success' => false, 'message' => 'Booking not found']);
                        exit;
                    }
                    
                    // Update booking
                    $bookings[$bookingIndex] = array_merge($bookings[$bookingIndex], $input['bookingData']);
                    $bookings[$bookingIndex]['updatedAt'] = date('c');
                    
                    if (saveBookings($bookingsFile, $bookings)) {
                        echo json_encode(['success' => true, 'message' => 'Booking updated successfully']);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
                    }
                    break;
                    
                case 'deleteBooking':
                    if (empty($input['bookingId'])) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Booking ID required']);
                        exit;
                    }
                    
                    $bookings = loadBookings($bookingsFile);
                    $bookings = array_filter($bookings, function($booking) use ($input) {
                        return $booking['id'] !== $input['bookingId'];
                    });
                    
                    if (saveBookings($bookingsFile, array_values($bookings))) {
                        echo json_encode(['success' => true, 'message' => 'Booking deleted successfully']);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Failed to delete booking']);
                    }
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid action']);
            }
            exit;
        }
        
        // Default response for POST
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        break;
        
    case 'GET':
        // Return bookings for admin interface
        $bookings = loadBookings($bookingsFile);
        echo json_encode(['success' => true, 'bookings' => $bookings]);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?> 