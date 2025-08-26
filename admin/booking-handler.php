<?php
// ========================================================================
// BOOKING HANDLER - INTEGRATES WEBSITE BOOKINGS WITH ADMIN SYSTEM
// Security-hardened: session auth, CSRF protection, origin checks
// ========================================================================

header('Content-Type: application/json');
// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// Secure session cookies
if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 60 * 60 * 24,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Configuration
$bookingsFile = 'bookings.json';

// Admin credentials from environment (recommended)
$envAdminUser = getenv('ADMIN_USERNAME') ?: '';
$envAdminPass = getenv('ADMIN_PASSWORD') ?: '';

// Helper: constant-time compare
function hashEqualsSafe($a, $b) { return hash_equals((string)$a, (string)$b); }

// Helper: origin/referrer same-origin check
function isSameOrigin() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin) {
        $parsed = parse_url($origin);
        if (!isset($parsed['host'])) return false;
        return strtolower($parsed['host']) === strtolower(parse_url((isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:'http').'://'.$host, PHP_URL_HOST));
    }
    // Fallback to Referer
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ($ref) {
        $parsed = parse_url($ref);
        if (!isset($parsed['host'])) return false;
        return strtolower($parsed['host']) === strtolower(parse_url('http://'.$host, PHP_URL_HOST));
    }
    return true; // allow same-origin navigation without headers
}

// CSRF: validate for state-changing admin actions
function requireCsrf($input) {
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrfToken'] ?? '');
    if (empty($_SESSION['csrf_token']) || empty($token) || !hashEqualsSafe($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
}

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

// Helpers for auth
function requireAdmin() {
    if (empty($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

// Handle different request types
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $inputRaw = file_get_contents('php://input');
        $input = json_decode($inputRaw, true);
        if (!is_array($input)) { $input = []; }
        
        // LOGIN
        if (($input['action'] ?? '') === 'login') {
            if (!$input['username'] || !$input['password']) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Username and password required']);
                exit;
            }
            if ($envAdminUser === '' || $envAdminPass === '') {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Server credentials not configured']);
                exit;
            }
            if (hashEqualsSafe($input['username'], $envAdminUser) && hashEqualsSafe($input['password'], $envAdminPass)) {
                $_SESSION['admin_authenticated'] = true;
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                echo json_encode(['success' => true, 'message' => 'Login successful', 'csrfToken' => $_SESSION['csrf_token']]);
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
            }
            exit;
        }
        
        // LOGOUT
        if (($input['action'] ?? '') === 'logout') {
            requireAdmin();
            requireCsrf($input);
            session_unset();
            session_destroy();
            echo json_encode(['success' => true]);
            exit;
        }
        
        // Check if this is a booking submission from the main website
        if (isset($input['formType']) && $input['formType'] === 'booking') {
            if (!isSameOrigin()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Cross-site requests are not allowed']);
                exit;
            }
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
        
        // Public availability check without exposing bookings
        if (($input['action'] ?? '') === 'checkAvailability') {
            $date = $input['date'] ?? '';
            $boatType = $input['boatType'] ?? '';
            if (!$date || !$boatType) {
                http_response_code(400);
                echo json_encode(['success' => false, 'available' => false, 'message' => 'Missing parameters']);
                exit;
            }
            $bookings = loadBookings($bookingsFile);
            $conflict = false;
            foreach ($bookings as $b) {
                if (($b['date'] ?? null) === $date && ($b['boatType'] ?? null) === $boatType && ($b['status'] ?? '') !== 'payment-rejected') {
                    $conflict = true; break;
                }
            }
            echo json_encode(['success' => true, 'available' => !$conflict]);
            exit;
        }

        // Admin actions
        if (isset($input['action'])) {
            requireAdmin();
            // Require CSRF for state-changing actions
            $stateChanging = in_array($input['action'], ['createBooking','updateBooking','deleteBooking'], true);
            if ($stateChanging) {
                requireCsrf($input);
            }
            switch ($input['action']) {
                case 'getBookings':
                    $bookings = loadBookings($bookingsFile);
                    echo json_encode(['success' => true, 'bookings' => $bookings, 'csrfToken' => $_SESSION['csrf_token']]);
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
        // Session status endpoint
        if (isset($_GET['action']) && $_GET['action'] === 'session') {
            $auth = !empty($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
            if ($auth && empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            echo json_encode(['success' => true, 'authenticated' => $auth, 'csrfToken' => $auth ? $_SESSION['csrf_token'] : null]);
            break;
        }
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?> 