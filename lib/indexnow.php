<?php
/**
 * IndexNow - Push URL updates to search engines.
 * Call indexnow_submit(string|array $urls) after content changes.
 */
function indexnow_submit($urls): void {
    if (!defined('INDEXNOW_API_KEY') || INDEXNOW_API_KEY === '') return;
    $urls = is_array($urls) ? $urls : [$urls];
    $payload = json_encode([
        'host' => parse_url(SITE_URL, PHP_URL_HOST),
        'key' => INDEXNOW_API_KEY,
        'urlList' => $urls,
    ]);
    $ch = curl_init('https://api.indexnow.org/indexnow');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
    ]);
    curl_exec($ch);
    curl_close($ch);
}
