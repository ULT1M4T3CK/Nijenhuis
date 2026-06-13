<?php
// Simple test script to debug login issues
header('Content-Type: application/json');

// Log the request method and data
$method = $_SERVER['REQUEST_METHOD'];
$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

$response = [
    'method' => $method,
    'raw_input' => $inputRaw,
    'parsed_input' => $input,
    'server_info' => [
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
        'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
        'HTTP_ORIGIN' => $_SERVER['HTTP_ORIGIN'] ?? 'not set',
        'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? 'not set'
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
