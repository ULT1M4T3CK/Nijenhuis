<?php
/**
 * Mollie Webhook Handler for Plesk/Strato Hosting
 * Place this file in your web root directory
 * Access via: https://yourdomain.com/webhooks/mollie/webhook_handler_plesk.php
 */

// Production-safe error handling
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', 0);

// Set content type
header('Content-Type: application/json');

// Log function
function logWebhook($message) {
    $logFile = __DIR__ . '/webhook_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Configuration
$mollieApiKey = getenv('MOLLIE_API_KEY') ?: '';
$bookingsFile = __DIR__ . '/local_bookings.json';
$logFile = __DIR__ . '/webhook_log.txt';

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Health check endpoint
    echo json_encode([
        'status' => 'success',
        'message' => 'Mollie Webhook Handler is running',
        'timestamp' => date('Y-m-d H:i:s'),
        'server' => $_SERVER['SERVER_NAME'] ?? 'unknown'
    ]);
    exit;
}

if ($method === 'POST') {
    try {
        // Get POST data
        $input = file_get_contents('php://input');
        $webhookData = json_decode($input, true);
        
        if (!$webhookData) {
            throw new Exception('Invalid JSON data received');
        }
        
        logWebhook("Webhook received: " . json_encode($webhookData));
        
        // Extract payment ID
        $paymentId = $webhookData['id'] ?? null;
        
        if (!$paymentId) {
            throw new Exception('No payment ID in webhook data');
        }
        
        // Get payment status from Mollie API
        $paymentStatus = getPaymentStatus($paymentId);
        
        if ($paymentStatus) {
            // Update booking status
            updateBookingStatus($paymentId, $paymentStatus);
            
            // Send success response
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'Payment processed successfully',
                'payment_id' => $paymentId,
                'payment_status' => $paymentStatus
            ]);
            
            logWebhook("Payment $paymentId processed successfully: $paymentStatus");
        } else {
            throw new Exception('Failed to get payment status from Mollie');
        }
        
    } catch (Exception $e) {
        logWebhook("Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}

/**
 * Get payment status from Mollie API
 */
function getPaymentStatus($paymentId) {
    global $mollieApiKey;
    
    $url = "https://api.mollie.com/v2/payments/$paymentId";
    $headers = [
        'Authorization: Bearer ' . $mollieApiKey,
        'Content-Type: application/json'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        $paymentData = json_decode($response, true);
        return $paymentData['status'] ?? null;
    }
    
    logWebhook("Failed to get payment status. HTTP Code: $httpCode, Response: $response");
    return null;
}

/**
 * Update booking status based on payment status
 */
function updateBookingStatus($paymentId, $paymentStatus) {
    global $bookingsFile;
    
    // Map Mollie status to booking status
    $statusMapping = [
        'paid' => 'confirmed-paid',
        'failed' => 'payment-rejected',
        'expired' => 'payment-rejected',
        'canceled' => 'payment-rejected',
        'pending' => 'confirmed-not-paid'
    ];
    
    $newBookingStatus = $statusMapping[$paymentStatus] ?? 'not-confirmed';
    
    logWebhook("Payment $paymentId: $paymentStatus -> $newBookingStatus");
    
    // Load existing bookings
    $bookings = [];
    if (file_exists($bookingsFile)) {
        $bookingsData = file_get_contents($bookingsFile);
        $bookings = json_decode($bookingsData, true) ?: [];
    }
    
    // Find and update booking with this payment ID
    $updated = false;
    foreach ($bookings as &$booking) {
        if (isset($booking['paymentId']) && $booking['paymentId'] === $paymentId) {
            $booking['status'] = $newBookingStatus;
            $booking['paymentStatus'] = $paymentStatus;
            $booking['updatedAt'] = date('c');
            $updated = true;
            logWebhook("Updated booking " . $booking['id'] . " status to $newBookingStatus");
            break;
        }
    }
    
    if ($updated) {
        // Save updated bookings
        file_put_contents($bookingsFile, json_encode($bookings, JSON_PRETTY_PRINT));
        logWebhook("Bookings file updated successfully");
    } else {
        logWebhook("No booking found with payment ID: $paymentId");
    }
}

/**
 * Test function - can be called manually for testing
 */
function testWebhook() {
    $testData = [
        'id' => 'tr_test123',
        'status' => 'paid',
        'amount' => [
            'currency' => 'EUR',
            'value' => '85.00'
        ]
    ];
    
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $GLOBALS['test_mode'] = true;
    
    // Simulate POST request
    file_put_contents('php://input', json_encode($testData));
    
    // Re-run the script
    include __FILE__;
}
?> 