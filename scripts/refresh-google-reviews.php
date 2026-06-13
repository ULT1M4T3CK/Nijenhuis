<?php
/**
 * Fetch Google Places reviews and cache to data/google-reviews.json
 *
 * Run nightly via cron:
 *   0 3 * * * php /path/to/scripts/refresh-google-reviews.php
 *
 * Requires .env keys: GOOGLE_PLACES_API_KEY, GOOGLE_PLACE_ID
 *
 * Output is consumed by components/schema-localbusiness.php to emit
 * AggregateRating + Review JSON-LD (invisible to users, improves AI search trust).
 */

$root = dirname(__DIR__);

require_once $root . '/components/data_access.php';
if (file_exists($root . '/.env')) {
    loadEnvSafe($root . '/.env');
}

$apiKey = getenv('GOOGLE_PLACES_API_KEY') ?: ($_ENV['GOOGLE_PLACES_API_KEY'] ?? '');
$placeId = getenv('GOOGLE_PLACE_ID') ?: ($_ENV['GOOGLE_PLACE_ID'] ?? '');
$outFile = $root . '/data/google-reviews.json';

if ($apiKey === '' || $placeId === '') {
    fwrite(STDERR, "Missing GOOGLE_PLACES_API_KEY or GOOGLE_PLACE_ID in .env\n");
    exit(1);
}

$url = 'https://maps.googleapis.com/maps/api/place/details/json?'
     . http_build_query([
         'place_id' => $placeId,
         'fields'   => 'rating,user_ratings_total,reviews',
         'key'      => $apiKey,
         'language'  => 'nl',
     ]);

if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_USERAGENT      => 'NijenhuisReviewFetcher/1.0',
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $raw = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);
    if ($raw === false || $httpCode < 200 || $httpCode >= 400) {
        fwrite(STDERR, "Failed to fetch Google Places API (HTTP $httpCode): $curlErr\n");
        exit(1);
    }
} else {
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 30,
            'user_agent' => 'NijenhuisReviewFetcher/1.0',
        ],
    ]);
    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) {
        fwrite(STDERR, "Failed to fetch Google Places API\n");
        exit(1);
    }
}

$data = json_decode($raw, true);
if (empty($data['result'])) {
    fwrite(STDERR, "No result in API response: " . substr($raw, 0, 500) . "\n");
    exit(1);
}

$result = $data['result'];
$rating = $result['rating'] ?? null;
$total = $result['user_ratings_total'] ?? null;
$reviews = [];

foreach (($result['reviews'] ?? []) as $r) {
    $reviews[] = [
        'author_name' => $r['author_name'] ?? '',
        'rating'      => $r['rating'] ?? 5,
        'text'        => $r['text'] ?? '',
        'date'        => !empty($r['time']) ? date('Y-m-d', $r['time']) : '',
        'language'    => $r['language'] ?? 'nl',
    ];
}

usort($reviews, function ($a, $b) {
    return ($b['rating'] ?? 0) <=> ($a['rating'] ?? 0);
});

$output = [
    'fetchedAt' => date('c'),
    'aggregateRating' => null,
    'reviews' => $reviews,
];

if ($rating !== null && $total !== null) {
    $output['aggregateRating'] = [
        'ratingValue' => (string) $rating,
        'reviewCount' => (string) $total,
    ];
}

$json = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
if (file_put_contents($outFile, $json) === false) {
    fwrite(STDERR, "Failed to write $outFile\n");
    exit(1);
}

echo "Saved " . count($reviews) . " reviews (rating: $rating, total: $total) to $outFile\n";
