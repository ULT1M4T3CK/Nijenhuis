<?php
/**
 * Mollie Webhook Handler for Plesk/Strato Hosting
 * Place this file in your web root directory
 * Access via: https://yourdomain.com/webhooks/mollie/webhook_handler_plesk.php
 */

// Production-safe error handling
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', 0);

// Set content type and security headers
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: no-referrer');
$__isHttps = (
    (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
);
if ($__isHttps) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}
header('Cross-Origin-Resource-Policy: same-origin');

// Load shared components
require_once __DIR__ . '/../../../components/data_access.php';
require_once __DIR__ . '/../../../components/booking_confirmation_email.php';

// Log function
function logWebhook($message) {
    $logFile = __DIR__ . '/webhook_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Load .env file safely
loadEnvSafe(__DIR__ . '/../../../.env');

// Configuration
$mollieApiKey = getenv('MOLLIE_API_KEY') ?: '';
$mollieWebhookSecret = getenv('MOLLIE_WEBHOOK_SECRET') ?: '';
$appEnv = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'production');
$bookingsFile = nijenhuis_data_path('bookings.json');
$logFile = __DIR__ . '/webhook_log.txt';

// Validate Microsoft Graph env vars (log to webhook log for local debugging)
$requiredGraphVars = [
    'MS_GRAPH_TENANT_ID',
    'MS_GRAPH_CLIENT_ID',
    'MS_GRAPH_CLIENT_SECRET',
    'MS_GRAPH_MAILBOX'
];
foreach ($requiredGraphVars as $varName) {
    $value = getenv($varName) ?: ($_ENV[$varName] ?? '');
    if (empty($value)) {
        logWebhook("Warning: Missing env var $varName (Graph email may fail)");
    }
}

// Webhook signature verification function
function verifyWebhookSignature($input, $signature, $secret) {
    if (empty($secret)) {
        return false;
    }
    if (empty($signature)) {
        return false;
    }
    $expectedSignature = hash_hmac('sha256', $input, $secret);
    return hash_equals('sha256=' . $expectedSignature, $signature);
}

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'] ?? null;
if (!$method) {
    // Some server contexts (e.g. CLI/test) may not set REQUEST_METHOD
    logWebhook('Warning: REQUEST_METHOD missing; assuming POST');
    $method = 'POST';
}

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action === 'simulatePaid' && $appEnv !== 'production') {
        $paymentId = trim($_GET['paymentId'] ?? '');
        $bookingId = trim($_GET['bookingId'] ?? '');

        if (empty($paymentId) && !empty($bookingId)) {
            $bookings = loadJsonSafe($bookingsFile);
            foreach ($bookings as $b) {
                if (($b['id'] ?? '') === $bookingId) {
                    $paymentId = $b['paymentId'] ?? '';
                    break;
                }
            }
        }

        if (empty($paymentId)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'paymentId or bookingId is required']);
            exit;
        }

        logWebhook("Local simulatePaid triggered for paymentId: $paymentId");
        updateBookingStatus($paymentId, 'paid');
        echo json_encode(['status' => 'success', 'message' => 'Simulated paid webhook', 'paymentId' => $paymentId]);
        exit;
    }

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
        
        // SECURITY: When MOLLIE_WEBHOOK_SECRET is set, require valid X-Mollie-Signature.
        // When unset, webhooks are still processed (Mollie confirms payment via API) — set the secret when you can.
        $signature = $_SERVER['HTTP_X_MOLLIE_SIGNATURE'] ?? '';
        $hasRealSecret = !empty($mollieWebhookSecret) &&
                         $mollieWebhookSecret !== 'your_webhook_secret_here' &&
                         strlen($mollieWebhookSecret) > 20;

        if ($hasRealSecret) {
            if (empty($signature)) {
                logWebhook('Webhook rejected: Missing X-Mollie-Signature header (secret is configured)');
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Missing signature']);
                exit;
            }
            if (!verifyWebhookSignature($input, $signature, $mollieWebhookSecret)) {
                logWebhook('Webhook signature verification failed');
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
                exit;
            }
            logWebhook('Webhook signature verified successfully');
        } else {
            $envNote = $appEnv === 'production' ? 'production' : 'non-production';
            logWebhook("Warning: Webhook processed without signature verification ($envNote; set MOLLIE_WEBHOOK_SECRET when ready)");
        }
        
        // CRITICAL: Mollie sends form-urlencoded data (id=tr_xxx), not JSON!
        // Try form-urlencoded first (POST data), then raw parse, then JSON fallback
        $paymentId = $_POST['id'] ?? null;
        
        if (!$paymentId) {
            // Try parsing raw input as form-urlencoded
            parse_str($input, $formData);
            $paymentId = $formData['id'] ?? null;
        }
        
        if (!$paymentId) {
            // Last resort: try JSON (for manual testing)
            $webhookData = json_decode($input, true);
            $paymentId = $webhookData['id'] ?? null;
        }
        
        logWebhook("Webhook received - Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set') . ", Payment ID: " . ($paymentId ?? 'NONE'));
        
        if (!$paymentId) {
            throw new Exception('No payment ID in webhook data');
        }
        
        $paymentPayload = getPaymentPayload($paymentId);
        $paymentStatus = $paymentPayload['status'] ?? null;
        
        if ($paymentStatus) {
            updateBookingStatus($paymentId, $paymentStatus, $paymentPayload);
            
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
        // SECURITY: Log detailed error but don't expose to client
        logWebhook("Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Webhook processing failed. Please contact support.'
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
 * Get full payment object from Mollie API
 *
 * @return array<string, mixed>|null
 */
function getPaymentPayload($paymentId) {
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
        return is_array($paymentData) ? $paymentData : null;
    }
    
    logWebhook("Failed to get payment. HTTP Code: $httpCode, Response: $response");
    return null;
}

/**
 * Update booking status based on payment status (all rows sharing this Mollie payment id).
 *
 * @param array<string, mixed>|null $paymentPayload Full Mollie payment JSON when available
 */
function updateBookingStatus($paymentId, $paymentStatus, $paymentPayload = null) {
    global $bookingsFile;
    
    $metadata = is_array($paymentPayload) && isset($paymentPayload['metadata']) && is_array($paymentPayload['metadata'])
        ? $paymentPayload['metadata']
        : [];
    $isPoaReservation = ($metadata['paymentKind'] ?? '') === 'pay_on_arrival_reservation'
        || ($metadata['payOnArrival'] ?? '') === '1';
    
    $statusMapping = [
        'paid' => 'paid',
        'failed' => 'canceled',
        'expired' => 'canceled',
        'canceled' => 'canceled',
        'pending' => 'pending',
        'open' => 'pending'
    ];
    
    $newBookingStatus = $statusMapping[$paymentStatus] ?? 'canceled';
    
    logWebhook("Payment $paymentId: $paymentStatus -> $newBookingStatus (poaReservation=" . ($isPoaReservation ? '1' : '0') . ")");
    
    $bookings = loadJsonSafe($bookingsFile);
    $updated = false;
    $emailSentForCart = false;
    
    foreach ($bookings as &$booking) {
        if (!isset($booking['paymentId']) || $booking['paymentId'] !== $paymentId) {
            continue;
        }
        $emailAlreadySent = $booking['confirmationEmailSent'] ?? false;
        $mapped = $newBookingStatus;
        $isPoaReservationRow = $isPoaReservation || !empty($booking['payOnArrivalReservation']);
        if ($mapped === 'paid' && $isPoaReservationRow
            && (($booking['paymentMethod'] ?? '') === (defined('CHECKOUT_PAY_ON_ARRIVAL_METHOD') ? CHECKOUT_PAY_ON_ARRIVAL_METHOD : 'pay_on_arrival')
                || !empty($booking['payOnArrivalReservation']))) {
            $mapped = 'confirmed';
        }
        $booking['status'] = $mapped;
        $booking['paymentStatus'] = $paymentStatus;
        $booking['updatedAt'] = date('c');
        $updated = true;
        logWebhook("Updated booking " . ($booking['id'] ?? '') . " status to $mapped");
        
        $sendMail = ($mapped === 'paid' || $mapped === 'confirmed') && !$emailAlreadySent && !$emailSentForCart;
        if ($sendMail) {
            $emailSent = sendBookingConfirmationEmail($booking, true);
            $booking['confirmationEmailSent'] = $emailSent;
            if ($emailSent) {
                $booking['confirmationEmailSentAt'] = date('c');
                logWebhook("Confirmation email sent to " . ($booking['customerEmail'] ?? '') . " via Microsoft Graph");
            } else {
                logWebhook("Failed to send confirmation email for booking " . ($booking['id'] ?? ''));
            }
            $emailSentForCart = true;
        }
    }
    unset($booking);
    
    if ($updated) {
        saveJsonSafe($bookingsFile, $bookings);
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