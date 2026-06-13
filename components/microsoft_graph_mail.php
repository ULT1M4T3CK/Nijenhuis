<?php
/**
 * Microsoft Graph Mail helper (OAuth 2.0 Client Credentials)
 */

function graph_mail_curl_available(): bool {
    return function_exists('curl_init');
}

function getGraphAccessToken() {
    static $cachedToken = null;
    static $expiresAt = 0;

    if (!empty($cachedToken) && time() < ($expiresAt - 60)) {
        return $cachedToken;
    }

    $tenantId = getenv('MS_GRAPH_TENANT_ID') ?: ($_ENV['MS_GRAPH_TENANT_ID'] ?? '');
    $clientId = getenv('MS_GRAPH_CLIENT_ID') ?: ($_ENV['MS_GRAPH_CLIENT_ID'] ?? '');
    $clientSecret = getenv('MS_GRAPH_CLIENT_SECRET') ?: ($_ENV['MS_GRAPH_CLIENT_SECRET'] ?? '');

    if (empty($tenantId) || empty($clientId) || empty($clientSecret)) {
        error_log('Microsoft Graph OAuth config missing. Check MS_GRAPH_* env vars.');
        return null;
    }

    if (!graph_mail_curl_available()) {
        error_log('Microsoft Graph: PHP curl extension (ext-curl) is not loaded. Install e.g. sudo apt install php-curl (or php8.3-curl) and restart the web server / PHP.');
        return null;
    }

    $tokenUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";
    $postFields = http_build_query([
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'scope' => 'https://graph.microsoft.com/.default',
        'grant_type' => 'client_credentials'
    ]);

    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false || $httpCode < 200 || $httpCode >= 300) {
        error_log('Microsoft Graph token request failed. HTTP ' . $httpCode . ' Error: ' . $curlError);
        if (!empty($response)) {
            error_log('Token response: ' . $response);
        }
        return null;
    }

    $data = json_decode($response, true);
    if (!is_array($data) || empty($data['access_token'])) {
        error_log('Microsoft Graph token response missing access_token.');
        error_log('Token response: ' . $response);
        return null;
    }

    $cachedToken = $data['access_token'];
    $expiresIn = isset($data['expires_in']) ? (int)$data['expires_in'] : 3600;
    $expiresAt = time() + $expiresIn;

    return $cachedToken;
}

/**
 * @param list<array{name: string, content: string, contentType?: string}> $attachments Binary attachments (e.g. PDF)
 */
function sendGraphMail($toRecipients, $subject, $body, $bodyType = 'HTML', $replyTo = null, $cc = [], $bcc = [], $saveToSentItems = true, $mailbox = null, $attachments = [], &$errorOut = null) {
    $errorOut = null;
    $mailbox = $mailbox ?: (getenv('MS_GRAPH_MAILBOX') ?: ($_ENV['MS_GRAPH_MAILBOX'] ?? ''));
    if (empty($mailbox)) {
        error_log('Microsoft Graph mailbox not configured. Set MS_GRAPH_MAILBOX.');
        $errorOut = [
            'error' => 'mailbox_not_configured'
        ];
        return false;
    }

    $accessToken = getGraphAccessToken();
    if (empty($accessToken)) {
        $errorOut = [
            'error' => 'access_token_missing'
        ];
        return false;
    }

    if (!graph_mail_curl_available()) {
        error_log('Microsoft Graph sendMail: curl extension not loaded.');
        $errorOut = ['error' => 'curl_extension_missing'];
        return false;
    }

    $toList = array_map(function($addr) {
        return ['emailAddress' => ['address' => $addr]];
    }, (array)$toRecipients);

    $message = [
        'subject' => $subject,
        'body' => [
            'contentType' => $bodyType,
            'content' => $body
        ],
        'toRecipients' => $toList
    ];

    if (!empty($replyTo)) {
        $message['replyTo'] = [
            ['emailAddress' => ['address' => $replyTo]]
        ];
    }

    if (!empty($cc)) {
        $message['ccRecipients'] = array_map(function($addr) {
            return ['emailAddress' => ['address' => $addr]];
        }, (array)$cc);
    }

    if (!empty($bcc)) {
        $message['bccRecipients'] = array_map(function($addr) {
            return ['emailAddress' => ['address' => $addr]];
        }, (array)$bcc);
    }

    if (!empty($attachments) && is_array($attachments)) {
        $graphAtt = [];
        foreach ($attachments as $att) {
            if (empty($att['name']) || !isset($att['content']) || $att['content'] === '') {
                continue;
            }
            $graphAtt[] = [
                '@odata.type' => '#microsoft.graph.fileAttachment',
                'name' => (string)$att['name'],
                'contentType' => !empty($att['contentType']) ? (string)$att['contentType'] : 'application/octet-stream',
                'contentBytes' => base64_encode((string)$att['content']),
            ];
        }
        if (!empty($graphAtt)) {
            $message['attachments'] = $graphAtt;
        }
    }

    $payload = [
        'message' => $message,
        'saveToSentItems' => (bool)$saveToSentItems
    ];

    $endpoint = 'https://graph.microsoft.com/v1.0/users/' . rawurlencode($mailbox) . '/sendMail';

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false || $httpCode < 200 || $httpCode >= 300) {
        error_log('Microsoft Graph sendMail failed. HTTP ' . $httpCode . ' Error: ' . $curlError);
        if (!empty($response)) {
            error_log('sendMail response: ' . $response);
        }
        $errorOut = [
            'error' => 'sendmail_failed',
            'httpCode' => $httpCode,
            'curlError' => $curlError,
            'response' => $response
        ];
        return false;
    }

    return true;
}
?>
