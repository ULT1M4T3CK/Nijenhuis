<?php
// Server-side proxy for Mollie API
// Loads API key from environment and prevents exposing it to the client

header('Content-Type: application/json');

// Simple same-origin check to avoid cross-site abuse
function isSameOrigin() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin) {
        $parsed = parse_url($origin);
        if (!isset($parsed['host'])) return false;
        return strtolower($parsed['host']) === strtolower(parse_url((isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:'http').'://'.$host, PHP_URL_HOST));
    }
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ($ref) {
        $parsed = parse_url($ref);
        if (!isset($parsed['host'])) return false;
        return strtolower($parsed['host']) === strtolower(parse_url('http://'.$host, PHP_URL_HOST));
    }
    return true;
}

if (!isSameOrigin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

$mollieApiKey = getenv('MOLLIE_API_KEY') ?: 'test_sHQfqTngBbCpEfMyMCPGH92gnm8P7m';
if ($mollieApiKey === '') {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Mollie API key not configured']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

$baseUrl = 'https://api.mollie.com/v2';

function forwardRequest($url, $method = 'GET', $payload = null, $mollieApiKey = '') {
    $headers = [
        'Authorization: Bearer ' . $mollieApiKey,
        'Content-Type: application/json'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload ? json_encode($payload) : '{}');
    }
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$httpCode, $response];
}

try {
    if ($method === 'POST' && $action === 'createPayment') {
        $inputRaw = file_get_contents('php://input');
        $input = json_decode($inputRaw, true);
        if (!is_array($input)) { $input = []; }
        [$code, $resp] = forwardRequest($baseUrl . '/payments', 'POST', $input, $mollieApiKey);
        http_response_code($code);
        echo $resp ?: json_encode(['success' => false, 'message' => 'Empty response from Mollie']);
        exit;
    }
    
    if ($method === 'GET' && $action === 'getPaymentStatus') {
        $paymentId = $_GET['paymentId'] ?? '';
        if ($paymentId === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'paymentId is required']);
            exit;
        }
        [$code, $resp] = forwardRequest($baseUrl . '/payments/' . rawurlencode($paymentId), 'GET', null, $mollieApiKey);
        http_response_code($code);
        echo $resp ?: json_encode(['success' => false, 'message' => 'Empty response from Mollie']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

?>


