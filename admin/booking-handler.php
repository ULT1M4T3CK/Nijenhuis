<?php
// Load Site Config
require_once __DIR__ . '/../components/config.php';
ini_set('display_errors', 0); // Don't display errors, but log them
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php-debug.log');

// Start output buffering to catch any errors/warnings
if (!ob_get_level()) {
    ob_start();
}

header('Content-Type: application/json');
// Comprehensive security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()');
// Only advertise HSTS over HTTPS so local/HTTP development doesn't get stuck
// on a forced-HTTPS policy in browsers. Trust X-Forwarded-Proto when behind a
// reverse proxy (Plesk/nginx).
$__isHttps = (
    (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
);
if ($__isHttps) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://js.mollie.com; style-src \'self\' \'unsafe-inline\'; img-src \'self\' data: https:; connect-src \'self\' https://api.mollie.com; frame-ancestors \'none\'; base-uri \'self\'; form-action \'self\';');
header('Cross-Origin-Resource-Policy: same-origin');

// Local includes - Shared Components
require_once __DIR__ . '/../components/data_access.php';
require_once __DIR__ . '/../components/pricing_engine.php';
require_once __DIR__ . '/../components/security.php';
require_once __DIR__ . '/../components/microsoft_graph_mail.php';
require_once __DIR__ . '/../components/booking_confirmation_email.php';

// Secure session cookies
require_once __DIR__ . '/session-config.php';

// Load .env file safely
loadEnvSafe(__DIR__ . '/../.env');

// Configuration (JSON files under data/ — not web-accessible)
$bookingsFile = nijenhuis_data_path('bookings.json');
$bookingsArchiveFile = nijenhuis_data_path('bookings_archive.json');
$boatsFile = nijenhuis_data_path('boats.json');
$forSaleFile = nijenhuis_data_path('for-sale.json');

// Admin credentials from environment variables
$envAdminUser = getenv('ADMIN_USERNAME') ?: ($_ENV['ADMIN_USERNAME'] ?? '');
// SECURITY: Only hashed passwords are supported (plain text support removed)
$envAdminPassHash = getenv('ADMIN_PASSWORD_HASH') ?: ($_ENV['ADMIN_PASSWORD_HASH'] ?? '');

// Employee credentials from environment variables
$envEmployeeUser = getenv('EMPLOYEE_USERNAME') ?: ($_ENV['EMPLOYEE_USERNAME'] ?? '');
// SECURITY: Only hashed passwords are supported (plain text support removed)
$envEmployeePassHash = getenv('EMPLOYEE_PASSWORD_HASH') ?: ($_ENV['EMPLOYEE_PASSWORD_HASH'] ?? '');

// Validate that admin credentials are configured (require hashed passwords)
if (empty($envAdminUser) || empty($envAdminPassHash)) {
    error_log("CRITICAL: ADMIN_USERNAME or ADMIN_PASSWORD_HASH not configured. Plain text passwords are disabled for security.");
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Admin credentials not configured. Please set ADMIN_USERNAME and ADMIN_PASSWORD_HASH environment variables.']);
    exit;
}

// Helper: constant-time compare
function hashEqualsSafe($a, $b) { return hash_equals((string)$a, (string)$b); }

// API Key validation for chatbot integration
function validateApiKey($providedKey, $systemType = 'booking') {
    $bookingApiKey = getenv('BOOKING_API_KEY') ?: ($_ENV['BOOKING_API_KEY'] ?? '');
    $inventoryApiKey = getenv('INVENTORY_API_KEY') ?: ($_ENV['INVENTORY_API_KEY'] ?? '');
    
    if ($systemType === 'booking' && !empty($bookingApiKey)) {
        return hashEqualsSafe($providedKey, $bookingApiKey);
    }
    if ($systemType === 'inventory' && !empty($inventoryApiKey)) {
        return hashEqualsSafe($providedKey, $inventoryApiKey);
    }
    return false;
}

// Get API key from request headers or body
function getApiKeyFromRequest($input = []) {
    // Check Authorization header first (Bearer token)
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['HTTP_X_API_KEY'] ?? '';
    if (preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
        return $matches[1];
    }
    // Check X-API-Key header
    if (!empty($_SERVER['HTTP_X_API_KEY'])) {
        return $_SERVER['HTTP_X_API_KEY'];
    }
    // Check request body
    if (!empty($input['apiKey'])) {
        return $input['apiKey'];
    }
    return '';
}

// Load centralized CORS configuration
require_once __DIR__ . '/../components/cors.php';

// Handle CORS preflight requests using centralized function
handleCorsPreflight();

// Enhanced CORS and origin security check using centralized function
function isSameOrigin() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    
    // Use centralized CORS configuration
    $allowedOrigins = getAllowedOrigins();
    
    // Check Origin header first
    if ($origin) {
        if (setCorsHeadersSafe($origin, $allowedOrigins, true)) {
            return true;
        }
        return false; // Origin not in whitelist
    }
    
    // Fallback to same-host check if no Origin header
    $currentScheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $currentHost = $currentScheme . '://' . $host;
    
    if ($referer) {
        $parsed = parse_url($referer);
        if (!isset($parsed['host'])) return false;
        $refererOrigin = $parsed['scheme'] . '://' . $parsed['host'];
        return $refererOrigin === $currentHost;
    }
    
    return true; // Allow same-origin navigation without headers (for direct access)
}

/** Human-readable message for PHP $_FILES['...']['error'] codes (see https://www.php.net/manual/en/features.file-upload.errors.php). */
function describePhpUploadError(int $code): string {
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'Het bestand is groter dan de toegestane uploadlimiet van de server (upload_max_filesize). Probeer een kleinere afbeelding of vraag de beheerder PHP-limieten te verhogen.';
        case UPLOAD_ERR_FORM_SIZE:
            return 'Het bestand overschrijdt het maximum in het formulier.';
        case UPLOAD_ERR_PARTIAL:
            return 'De upload is onderbroken; probeer opnieuw.';
        case UPLOAD_ERR_NO_FILE:
            return 'Er is geen bestand meegestuurd.';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Tijdelijke map ontbreekt op de server (upload configuratie).';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Schrijven naar schijf mislukt op de server.';
        case UPLOAD_ERR_EXTENSION:
            return 'Upload geblokkeerd door een PHP-extensie op de server.';
        default:
            return 'Upload mislukt (foutcode ' . $code . ').';
    }
}

/** Sample image for semi/transparent pixels (GD truecolor alpha in high byte). */
function forsale_image_has_alpha($im): bool {
    $w = imagesx($im);
    $h = imagesy($im);
    if ($w < 1 || $h < 1) {
        return false;
    }
    $stepX = $w > 64 ? (int)floor($w / 32) : 1;
    $stepY = $h > 64 ? (int)floor($h / 32) : 1;
    for ($y = 0; $y < $h; $y += $stepY) {
        for ($x = 0; $x < $w; $x += $stepX) {
            $rgba = imagecolorat($im, $x, $y);
            if (($rgba >> 24) & 0x7F) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Resize and re-encode for-sale uploads when GD is available. Skips GIF (animation).
 *
 * @return array{filename: string}|null  null = caller should use move_uploaded_file fallback
 */
function optimize_for_sale_uploaded_image(string $tmpPath, string $uploadDir, string $baseFilename, string $mime, int $maxEdge = 1920, int $jpegQuality = 85): ?array {
    if ($mime === 'image/gif') {
        return null;
    }
    if (!function_exists('imagecreatefromstring') || !function_exists('imagescale')) {
        return null;
    }
    $binary = @file_get_contents($tmpPath);
    if ($binary === false) {
        return null;
    }
    $im = @imagecreatefromstring($binary);
    if ($im === false) {
        return null;
    }
    $w = imagesx($im);
    $h = imagesy($im);
    if ($w < 1 || $h < 1) {
        imagedestroy($im);
        return null;
    }
    $newW = $w;
    $newH = $h;
    if ($w > $maxEdge || $h > $maxEdge) {
        $ratio = min($maxEdge / $w, $maxEdge / $h);
        $newW = max(1, (int)round($w * $ratio));
        $newH = max(1, (int)round($h * $ratio));
    }
    if ($newW !== $w || $newH !== $h) {
        $scaled = imagescale($im, $newW, $newH, IMG_BILINEAR_FIXED);
        imagedestroy($im);
        if ($scaled === false) {
            return null;
        }
        $im = $scaled;
    }

    if (forsale_image_has_alpha($im)) {
        imagealphablending($im, false);
        imagesavealpha($im, true);
        $outName = $baseFilename . '.png';
        $outPath = $uploadDir . $outName;
        if (!imagepng($im, $outPath, 6)) {
            imagedestroy($im);
            return null;
        }
        imagedestroy($im);
        return ['filename' => $outName];
    }

    $outName = $baseFilename . '.jpg';
    $outPath = $uploadDir . $outName;
    if (!imagejpeg($im, $outPath, $jpegQuality)) {
        imagedestroy($im);
        return null;
    }
    imagedestroy($im);
    return ['filename' => $outName];
}

// CSRF: validate for state-changing admin/employee actions
function requireCsrf($input) {
    $isAdmin = !empty($_SESSION['admin_authenticated']);
    $isEmployee = !empty($_SESSION['employee_authenticated']);
    if (!($isAdmin || $isEmployee)) {
        return;
    }
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrfToken'] ?? '');
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    // Fail closed: every authenticated state-changing call must carry a CSRF
    // token that matches the one bound to the session. Login paths that
    // haven't yet issued one should refuse rather than silently allow.
    if (empty($sessionToken) || empty($token) || !hashEqualsSafe($sessionToken, $token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
}

// Replaced load/save logic with wrappers around safe components
function loadBookings($file) {
    // Handle both relative and absolute paths (legacy support)
    $path = (strpos($file, '/') === 0) ? $file : __DIR__ . '/' . $file;
    return loadJsonSafe($path);
}

function saveBookings($file, $bookings) {
    $path = (strpos($file, '/') === 0) ? $file : __DIR__ . '/' . $file;
    return saveJsonSafe($path, $bookings);
}

function loadBoats($file) {
    $path = (strpos($file, '/') === 0) ? $file : __DIR__ . '/' . $file;
    return loadJsonSafe($path);
}

function saveBoats($file, $boats) {
    $path = (strpos($file, '/') === 0) ? $file : __DIR__ . '/' . $file;
    return saveJsonSafe($path, $boats);
}


// Function to load for-sale items
function loadForSaleItems($file) {
    $path = (strpos($file, '/') === 0) ? $file : __DIR__ . '/' . $file;
    return loadJsonSafe($path);
}

// Function to save for-sale items
function saveForSaleItems($file, $items) {
    $path = (strpos($file, '/') === 0) ? $file : __DIR__ . '/' . $file;
    return saveJsonSafe($path, $items);
}

// Backup JSON file with rotation (non-blocking, atomic).
// The source is read with a shared lock so we don't capture a half-written
// state from a concurrent saveJsonSafe(), and the backup is written to a
// temp file then renamed into place so readers never see a torn file.
function backupJsonFile($filePath, $backupDir, $maxBackups = 10) {
    $path = (strpos($filePath, '/') === 0) ? $filePath : __DIR__ . '/' . $filePath;
    if (!file_exists($path)) {
        return false;
    }
    if (!is_dir($backupDir)) {
        @mkdir($backupDir, 0755, true);
    }

    $src = @fopen($path, 'rb');
    if (!$src) {
        return false;
    }
    @flock($src, LOCK_SH);
    $data = @stream_get_contents($src);
    @flock($src, LOCK_UN);
    @fclose($src);
    if ($data === false) {
        return false;
    }

    $timestamp = date('Ymd_His');
    $finalPath = rtrim($backupDir, '/') . "/for-sale-{$timestamp}.json";
    $tmpPath = $finalPath . '.tmp.' . bin2hex(random_bytes(4));
    if (@file_put_contents($tmpPath, $data, LOCK_EX) === false) {
        return false;
    }
    @chmod($tmpPath, 0644);
    if (!@rename($tmpPath, $finalPath)) {
        @unlink($tmpPath);
        return false;
    }

    $files = glob(rtrim($backupDir, '/') . '/for-sale-*.json');
    if ($files !== false && count($files) > $maxBackups) {
        usort($files, function($a, $b) {
            return filemtime($b) <=> filemtime($a);
        });
        foreach (array_slice($files, $maxBackups) as $oldFile) {
            @unlink($oldFile);
        }
    }
    return true;
}

// Function to generate unique ID
function generateId() {
    return uniqid() . '_' . time();
}

// Function to validate booking data
function validateBooking($data) {
    $isReceptie = (isset($data['source']) && $data['source'] === 'receptie');

    if ($isReceptie) {
        $required = ['date', 'boatType'];
    } else {
        $required = ['date', 'boatType', 'customerName', 'customerEmail', 'customerPhone'];
    }

    foreach ($required as $field) {
        if (empty($data[$field])) {
            return false;
        }
    }
    
    if (!$isReceptie && !filter_var($data['customerEmail'], FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Validate date
    $date = DateTime::createFromFormat('Y-m-d', $data['date']);
    if (!$date || $date->format('Y-m-d') !== $data['date']) {
        return false;
    }
    
    return true;
}

// Shared availability functions (resolveBoatType, countBookingsForBoatOnDateRange, checkBoatAvailability)
require_once __DIR__ . '/../components/availability.php';

// Calculate price Wrapper
function calculatePrice($boats, $boatType, $numberOfDays, $useMotor = false) {
    return calculateBoatPrice($boatType, $numberOfDays, $boats, $useMotor);
}

// Function to clean up expired temporary bookings
function cleanupExpiredBookings($bookingsFile) {
    $bookings = loadBookings($bookingsFile);
    if (empty($bookings)) return;
    
    $now = new DateTime();
    $hasChanges = false;
    $activeBookings = [];
    
    foreach ($bookings as $booking) {
        if (($booking['status'] ?? '') === 'temporary' && !empty($booking['expiresAt'])) {
            try {
                $expiresAt = new DateTime($booking['expiresAt']);
                if ($now > $expiresAt) {
                    $hasChanges = true;
                    continue; // Delete it
                }
            } catch (Exception $e) {
                $hasChanges = true;
                continue;
            }
        }
        $activeBookings[] = $booking;
    }
    
    if ($hasChanges) {
        saveBookings($bookingsFile, $activeBookings);
    }
}

// Function to archive past bookings
function archiveBookingKey($booking) {
    if (!empty($booking['id'])) {
        return 'id:' . (string) $booking['id'];
    }

    $start = $booking['date'] ?? '';
    $end = $booking['endDate'] ?? $start;
    $boat = $booking['boatType'] ?? ($booking['boatId'] ?? '');
    $email = strtolower(trim((string) ($booking['customerEmail'] ?? '')));
    $createdAt = $booking['createdAt'] ?? '';

    return 'fallback:' . implode('|', [$start, $end, $boat, $email, $createdAt]);
}

function archivePastBookings($bookingsFile, $archiveFile) {
    $bookings = loadBookings($bookingsFile);
    if (empty($bookings)) return true;

    $today = new DateTime();
    $today->setTime(0, 0, 0); // Start of today

    $activeBookings = [];
    $pastBookings = [];
    $hasChanges = false;

    foreach ($bookings as $booking) {
        $endDateStr = $booking['endDate'] ?? $booking['date'] ?? null;
        if (!$endDateStr) {
            $activeBookings[] = $booking;
            continue;
        }

        try {
            $endDate = new DateTime($endDateStr);
            $endDate->setTime(0, 0, 0);
            if ($endDate < $today) {
                $pastBookings[] = $booking;
                $hasChanges = true;
            } else {
                $activeBookings[] = $booking;
            }
        } catch (Exception $e) {
            $activeBookings[] = $booking;
        }
    }

    if ($hasChanges) {
        $archive = loadBookings($archiveFile);
        $archiveByKey = [];

        foreach ($archive as $archivedBooking) {
            $archiveByKey[archiveBookingKey($archivedBooking)] = $archivedBooking;
        }

        foreach ($pastBookings as $pastBooking) {
            $archiveByKey[archiveBookingKey($pastBooking)] = $pastBooking;
        }

        $mergedArchive = array_values($archiveByKey);
        if (!saveBookings($archiveFile, $mergedArchive)) {
            error_log('archivePastBookings: Failed to save archive; active bookings left unchanged');
            return false;
        }

        if (!saveBookings($bookingsFile, $activeBookings)) {
            error_log('archivePastBookings: Archived past bookings but failed to prune active bookings');
            return false;
        }
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
    
    // SECURITY: Validate and sanitize email to prevent header injection
    $replyToEmail = filter_var($booking['customerEmail'], FILTER_VALIDATE_EMAIL);
    if (!$replyToEmail) {
        $replyToEmail = 'info@nijenhuis-botenverhuur.nl'; // Fallback to default
    }
    
    return sendGraphMail(
        $to,
        $subject,
        $message,
        'Text',
        $replyToEmail,
        [],
        [],
        true
    );
}

// Helpers for auth
function requireAdmin() {
    if (empty($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

// Allow either admin or employee session (for employee portal actions)
function requireAdminOrEmployee() {
    $isAdmin = !empty($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
    $isEmployee = !empty($_SESSION['employee_authenticated']) && $_SESSION['employee_authenticated'] === true;
    
    if (!$isAdmin && !$isEmployee) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

// Rate Limiter implementation with atomic file locking (replaces simple version)
// Uses the atomic version from security.php for thread-safe operations.
//
// NOTE: checkRateLimitAtomic() already increments the counter as part of the
// check, so every login attempt counts as one regardless of outcome. The
// previous implementation of updateRateLimit(false) called
// checkRateLimitAtomic again, which caused every failed login to consume
// two attempts. updateRateLimit is now only responsible for clearing the
// counter on success.
function checkRateLimit($ip) {
    return checkRateLimitAtomic('login_' . $ip, 5, 900, false);
}

function updateRateLimit($ip, $success) {
    if ($success) {
        $limitFile = sys_get_temp_dir() . '/rate_limit_' . md5('login_' . $ip);
        if (file_exists($limitFile)) {
            @unlink($limitFile);
        }
    }
    // Failures do not re-increment: checkRateLimit() already did.
}

// Handle different request types
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Handle multipart file uploads (for-sale image upload) before JSON parsing
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'multipart/form-data') !== false) {
            $multipartAction = $_POST['action'] ?? '';

            if ($multipartAction === 'uploadForSaleImage') {
                requireAdmin();
                requireCsrf($_POST);

                if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                    $errCode = (int)($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE);
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => describePhpUploadError($errCode)]);
                    exit;
                }

                $file = $_FILES['image'];
                $maxSize = 5 * 1024 * 1024; // 5 MB
                if ($file['size'] > $maxSize) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB.']);
                    exit;
                }

                // Validate MIME type using file contents, not client-supplied type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if (!in_array($mime, $allowedMimes, true)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, WebP and GIF are allowed.']);
                    exit;
                }

                $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
                $ext = $extMap[$mime];
                $uploadDir = realpath(__DIR__ . '/../frontend/Images/for-sale') . '/';
                if (!$uploadDir || !is_dir($uploadDir)) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Upload directory not found.']);
                    exit;
                }

                $base = 'forsale_' . time() . '_' . bin2hex(random_bytes(8));
                $optimized = optimize_for_sale_uploaded_image($file['tmp_name'], $uploadDir, $base, $mime);
                if ($optimized !== null) {
                    echo json_encode(['success' => true, 'url' => '/frontend/Images/for-sale/' . $optimized['filename']]);
                    exit;
                }

                $filename = $base . '.' . $ext;
                $destPath = $uploadDir . $filename;

                if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file.']);
                    exit;
                }

                echo json_encode(['success' => true, 'url' => '/frontend/Images/for-sale/' . $filename]);
                exit;
            }

            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown multipart action']);
            exit;
        }

        $inputRaw = file_get_contents('php://input');
        $input = validateJsonInput($inputRaw);
        if ($input === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
            exit;
        }
        
        // LOGIN
        if (($input['action'] ?? '') === 'login') {
            if (!$input['username'] || !$input['password']) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Username and password required']);
                exit;
            }
            
            // Rate limiting
            $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            if (!checkRateLimit($clientIp)) {
                http_response_code(429);
                echo json_encode(['success' => false, 'message' => 'Too many failed login attempts. Please try again in 15 minutes.']);
                exit;
            }
            
            // Validate credentials
            $isValidUser = hashEqualsSafe($input['username'], $envAdminUser);
            
            // SECURITY: Only hashed passwords are supported (plain text support removed)
            $isValidPass = verifyPassword($input['password'], $envAdminPassHash);
            
            if ($isValidUser && $isValidPass) {
                updateRateLimit($clientIp, true);
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                // Clear any employee portal session data to ensure clean admin session
                unset($_SESSION['employee_authenticated']);
                unset($_SESSION['employee_user']);
                unset($_SESSION['employee_login_time']);
                unset($_SESSION['employee_last_activity']);
                
                $_SESSION['admin_authenticated'] = true;
                $_SESSION['admin_user'] = $envAdminUser;
                $_SESSION['admin_login_time'] = time();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Login successful', 
                    'csrfToken' => $_SESSION['csrf_token']
                ]);
            } else {
                updateRateLimit($clientIp, false);
                usleep(500000); // 0.5 sec delay
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
            }
            exit;
        }
        
        // EMPLOYEE LOGIN
        if (($input['action'] ?? '') === 'employeeLogin') {
             // Rate limiting (reuse same mechanism)
             $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
             if (!checkRateLimit($clientIp)) {
                 http_response_code(429);
                 echo json_encode(['success' => false, 'message' => 'Too many failed login attempts. Please try again in 15 minutes.']);
                 exit;
             }
 
             if (!$input['password']) {
                 http_response_code(400);
                 echo json_encode(['success' => false, 'message' => 'Password required']);
                 exit;
             }
 
             // Logic to determine if user/pass is valid (Admin OR Employee)
             // SECURITY: Only hashed passwords are supported (plain text support removed)
             $isEmployee = false;
             if (hashEqualsSafe($input['username'], $envEmployeeUser) && !empty($envEmployeePassHash)) {
                 $isEmployee = verifyPassword($input['password'], $envEmployeePassHash);
             }
             
             // Allow admin to login as employee too
             $isAdmin = false;
             if (hashEqualsSafe($input['username'], $envAdminUser) && !empty($envAdminPassHash)) {
                 $isAdmin = verifyPassword($input['password'], $envAdminPassHash);
             }
 
             if ($isEmployee || $isAdmin) {
                 updateRateLimit($clientIp, true);
                 
                 // Regenerate session ID to prevent session fixation
                 session_regenerate_id(true);
                 
                $_SESSION['employee_authenticated'] = true;
                $_SESSION['employee_user'] = $isEmployee ? 'employee' : 'admin';
                $_SESSION['employee_login_time'] = time();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // For future CSRF-protected employee actions
                 
                 echo json_encode(['success' => true, 'message' => 'Employee login successful', 'csrfToken' => $_SESSION['csrf_token']]);
             } else {
                 updateRateLimit($clientIp, false);
                 usleep(500000);
                 http_response_code(401);
                 echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
             }
             exit;
        }
        
        // LOGOUT
        if (($input['action'] ?? '') === 'logout') {
            // Verify CSRF for any authenticated session (admin OR employee).
            if (!empty($_SESSION['admin_authenticated']) || !empty($_SESSION['employee_authenticated'])) {
                $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrfToken'] ?? '');
                $sessionToken = $_SESSION['csrf_token'] ?? '';
                if (empty($sessionToken) || empty($token) || !hashEqualsSafe($sessionToken, $token)) {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
                    exit;
                }
            }
            
            // Destroy session completely
            $_SESSION = [];
            
            // Delete session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            session_unset();
            session_destroy();
            
            echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
            exit;
        }

        // --- AUTHENTICATED ACTIONS ---
        
        // 1. API Key Auth Actions (Chatbot)
        $apiKey = getApiKeyFromRequest($input);
        
        if (!empty($apiKey) && validateApiKey($apiKey, 'booking')) {
            // BOOKING CREATION (External)
            if (($input['action'] ?? '') === 'createBooking') {
                if (!validateBooking($input)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid booking data']);
                    exit;
                }
                
                // Sanitize input data before processing
                $input = sanitizeBookingData($input);
                
                $bookings = loadBookings($bookingsFile);
                $boats = loadBoats($boatsFile);
                
                // Check Availability using shared logic
                $check = checkBoatAvailability($bookings, $boats, $input['boatType'], $input['date'], $input['endDate'] ?? $input['date']);
                
                if (!$check['available']) {
                    http_response_code(409); // Conflict
                    echo json_encode(['success' => false, 'message' => $check['reason']]);
                    exit;
                }
                
                // Calculate Price
                // Start/End date duration
                $start = new DateTime($input['date']);
                $end = new DateTime($input['endDate'] ?? $input['date']);
                $days = $start->diff($end)->days + 1;
                
                $engineOption = $input['engineOption'] ?? 'without';
                if (($input['boatType'] ?? '') !== 'sailboat-4-5') {
                    $engineOption = 'without';
                }
                $useMotor = ($engineOption === 'with');
                $input['engineOption'] = $engineOption;
                
                $price = calculatePrice($boats, $input['boatType'], $days, $useMotor);
                
                // Get quantity - create separate booking per boat
                $quantity = isset($input['quantity']) ? max(1, intval($input['quantity'])) : 1;
                
                $createdIds = [];
                for ($q = 0; $q < $quantity; $q++) {
                    $newBooking = array_merge([
                        'createdAt' => date('c'),
                        // SEMANTICS: new online bookings start as 'canceled'
                        // (treated as non-blocking by components/availability.php)
                        // until the Mollie webhook flips them to 'paid'. The name
                        // is legacy; think of it as "unpaid -> does not hold the
                        // slot yet". Do NOT rename without also updating the
                        // non-blocking set in availability.php, the admin
                        // canceled grouping, and any clients that read `status`.
                        'status' => 'canceled',
                        'source' => 'online',
                        'amount' => $price, // Price per single boat
                        'numberOfDays' => $days,
                        'quantity' => 1
                    ], $input);
                    
                    // Always generate a unique ID per booking entry
                    $newBooking['id'] = generateId() . '_' . $q;
                    // Override quantity to 1 (each entry = 1 boat)
                    $newBooking['quantity'] = 1;
                    $newBooking['amount'] = $price;
                    
                    unset($newBooking['action']);
                    unset($newBooking['apiKey']);
                    
                    $bookings[] = $newBooking;
                    $createdIds[] = $newBooking['id'];
                    
                    if ($q === 0) {
                        sendBookingNotification($newBooking);
                    }
                }
                
                if (saveBookings($bookingsFile, $bookings)) {
                    echo json_encode(['success' => true, 'message' => 'Booking created', 'bookingId' => $createdIds[0], 'bookingsCreated' => count($createdIds)]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to save booking']);
                }
                exit;
            }
        }
        

        // PUBLIC availability action
        if (($input['action'] ?? '') === 'getPublicBookings') {
            // Per-IP rate limit to blunt scraping / enumeration. Generous cap
            // because legitimate pages poll every 30s per tab.
            $rlIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
            $rlIp = explode(',', $rlIp)[0];
            if (!checkRateLimitAtomic('public_bookings_' . $rlIp, 120, 60)) {
                http_response_code(429);
                echo json_encode(['success' => false, 'message' => 'Too many requests']);
                exit;
            }

            $bookings = loadBookings($bookingsFile);

            // Only expose bookings whose status actually blocks the calendar,
            // and strip internal ids / customer data. The client only needs
            // (boatType, date range, status) to render availability.
            $blockingStatuses = ['success', 'manual', 'paid', 'picked_up', 'confirmed', 'confirmed-paid'];
            $publicBookings = [];
            foreach ($bookings as $b) {
                $status = $b['status'] ?? '';
                if (!in_array($status, $blockingStatuses, true)) {
                    continue;
                }
                $publicBookings[] = [
                    'boatType' => $b['boatType'] ?? '',
                    'date' => $b['date'] ?? '',
                    'endDate' => $b['endDate'] ?? ($b['date'] ?? ''),
                    'status' => $status,
                ];
            }

            echo json_encode(['success' => true, 'bookings' => $publicBookings]);
            exit;
        }

        if (($input['action'] ?? '') === 'checkAvailabilityPublic') {
            $bookings = loadBookings($bookingsFile);
            $boats = loadBoats($boatsFile);
            $startDate = $input['date'] ?? date('Y-m-d');
            $endDate = $input['endDate'] ?? $startDate;
            $boatType = $input['boatType'] ?? null;

            if (!$boatType) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Boat type required']);
                exit;
            }

            $result = checkBoatAvailability($bookings, $boats, $boatType, $startDate, $endDate);
            echo json_encode(['success' => true, 'available' => $result['available'], 'data' => $result]);
            exit;
        }

        if (($input['action'] ?? '') === 'validateCartAvailability') {
            $items = $input['items'] ?? [];
            if (empty($items) || !is_array($items)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Cart items required']);
                exit;
            }

            $bookings = loadBookings($bookingsFile);
            $boats = loadBoats($boatsFile);
            $unavailableItems = [];

            foreach ($items as $item) {
                $boatId = $item['boatId'] ?? '';
                $startDate = $item['startDate'] ?? '';
                $endDate = $item['endDate'] ?? $startDate;
                $quantity = isset($item['quantity']) ? max(1, intval($item['quantity'])) : 1;

                if (empty($boatId) || empty($startDate)) {
                    continue;
                }

                // Find boat to get total count
                $boat = null;
                foreach ($boats as $b) {
                    if ($b['id'] === $boatId) {
                        $boat = $b;
                        break;
                    }
                }

                if (!$boat) {
                    $unavailableItems[] = [
                        'boatId' => $boatId,
                        'boatName' => $item['boatName'] ?? $boatId,
                        'blockedDate' => $startDate,
                        'reason' => 'boat_not_found'
                    ];
                    continue;
                }

                $totalBoats = $boat['total'] ?? 1;

                // Check if requested quantity exceeds total
                if ($quantity > $totalBoats) {
                    $unavailableItems[] = [
                        'boatId' => $boatId,
                        'boatName' => $item['boatName'] ?? $boatId,
                        'blockedDate' => $startDate,
                        'reason' => 'insufficient_quantity',
                        'requested' => $quantity,
                        'available' => $totalBoats
                    ];
                    continue;
                }

                // Check availability for the date range
                $result = checkBoatAvailability($bookings, $boats, $boatId, $startDate, $endDate);
                if (!$result['available']) {
                    $unavailableItems[] = [
                        'boatId' => $boatId,
                        'boatName' => $item['boatName'] ?? $boatId,
                        'blockedDate' => $result['date'] ?? $startDate,
                        'reason' => $result['reason'] ?? 'unavailable'
                    ];
                } else if (isset($result['availableCount']) && $result['availableCount'] < $quantity) {
                    // Check if available count is sufficient for requested quantity
                    $unavailableItems[] = [
                        'boatId' => $boatId,
                        'boatName' => $item['boatName'] ?? $boatId,
                        'blockedDate' => $startDate,
                        'reason' => 'insufficient_availability',
                        'requested' => $quantity,
                        'available' => $result['availableCount']
                    ];
                }
            }

            if (!empty($unavailableItems)) {
                http_response_code(409);
                $boatNames = array_map(function($i) { return $i['boatName']; }, $unavailableItems);
                echo json_encode([
                    'success' => false,
                    'message' => 'Helaas zijn de volgende boot(en) inmiddels niet meer beschikbaar: ' . implode(', ', $boatNames) . '. Verwijder deze uit uw winkelwagen en probeer het opnieuw.',
                    'unavailableItems' => $unavailableItems
                ]);
                exit;
            }

            echo json_encode(['success' => true]);
            exit;
        }

        $action = $input['action'] ?? '';

        // 2a. Admin OR Employee actions (employee portal: createManualBooking, checkAvailability)
        if ($action === 'createManualBooking' || $action === 'checkAvailability') {
            requireAdminOrEmployee();
            requireCsrf($input); // Requires valid X-CSRF-Token when session has csrf_token (admin and employee)
            
            if ($action === 'checkAvailability') {
                $bookings = loadBookings($bookingsFile);
                $boats = loadBoats($boatsFile); // Need boats for capacity checking
                
                $startDate = $input['date'] ?? date('Y-m-d');
                $endDate = $input['endDate'] ?? $startDate;
                $boatType = $input['boatType'] ?? null;
                
                if ($boatType) {
                    $result = checkBoatAvailability($bookings, $boats, $boatType, $startDate, $endDate);
                    echo json_encode(['success' => true, 'data' => $result]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Boat type required']);
                }
                exit;
            }
            
            if ($action === 'createManualBooking') {
                if (!validateBooking($input)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid data']);
                    exit;
                }

                // Capture forceOverride before sanitization strips it. Only
                // admins may bypass availability; employees must go through
                // the normal check. This prevents a low-trust operator from
                // overbooking by setting a flag in the request body.
                $isAdminSession = !empty($_SESSION['admin_authenticated']);
                $forceOverride = $isAdminSession && !empty($input['forceOverride']);
                
                // Sanitize input data
                $input = sanitizeBookingData($input);
                
                $bookings = loadBookings($bookingsFile);
                $boats = loadBoats($boatsFile); // Need boats for availability check

                // Check availability - even for manual bookings!
                $startDate = $input['date'];
                $endDate = $input['endDate'] ?? $startDate;
                
                $s = new DateTime($startDate);
                $e = new DateTime($endDate);
                $days = $s->diff($e)->days + 1;
                
                $quantity = isset($input['quantity']) ? max(1, intval($input['quantity'])) : 1;
                
                if (!$forceOverride) {
                    $check = checkBoatAvailability($bookings, $boats, $input['boatType'], $startDate, $endDate);
                    if (!$check['available']) {
                        http_response_code(409);
                        echo json_encode([
                            'success' => false, 
                            'message' => 'Boat not available: ' . $check['reason'],
                            'availability' => $check
                        ]);
                        exit;
                    }
                    if (isset($check['availableCount']) && $check['availableCount'] < $quantity) {
                        http_response_code(409);
                        echo json_encode([
                            'success' => false,
                            'message' => "Niet genoeg boten beschikbaar. Gevraagd: $quantity, beschikbaar: " . $check['availableCount'],
                            'availability' => $check
                        ]);
                        exit;
                    }
                }
                
                $engineOption = $input['engineOption'] ?? 'without';
                if (($input['boatType'] ?? '') !== 'sailboat-4-5') {
                    $engineOption = 'without';
                }
                $useMotor = ($engineOption === 'with');
                $input['engineOption'] = $engineOption;
                $amount = $input['amount'] ?? 0;
                if ($amount <= 0 && !isset($input['amount'])) {
                    $amount = calculatePrice($boats, $input['boatType'], $days, $useMotor);
                }
                $pricePerBoat = ($quantity > 1 && isset($input['amount'])) ? ($amount / $quantity) : $amount;
                
                $createdBookings = [];
                for ($q = 0; $q < $quantity; $q++) {
                    $newBooking = array_merge([
                        'createdAt' => date('c'),
                        'status' => 'manual',
                        'source' => 'manual',
                        'amount' => $pricePerBoat,
                        'numberOfDays' => $days,
                        'quantity' => 1,
                        'paymentId' => 'manual_' . uniqid()
                    ], $input);
                    $newBooking['id'] = generateId() . '_' . $q;
                    $newBooking['quantity'] = 1;
                    $newBooking['amount'] = $pricePerBoat;
                    unset($newBooking['action'], $newBooking['csrfToken'], $newBooking['forceOverride']);
                    
                    $bookings[] = $newBooking;
                    $createdBookings[] = $newBooking;
                }
                
                if (saveBookings($bookingsFile, $bookings)) {
                    $primary = $createdBookings[0];
                    $emailSent = false;
                    $isReceptie = (isset($input['source']) && $input['source'] === 'receptie');
                    if (!$isReceptie && !empty($primary['customerEmail'])) {
                        $totalAmount = array_sum(array_column($createdBookings, 'amount'));
                        $emailBooking = array_merge($primary, ['amount' => $totalAmount]);
                        if (count($createdBookings) > 1) {
                            $emailBooking['numberOfDays'] = $primary['numberOfDays'];
                        }
                        $emailSent = sendBookingConfirmationEmail($emailBooking, false);
                        if (!$emailSent) {
                            error_log('createManualBooking: confirmation email failed for booking ' . ($primary['id'] ?? '') . ' to ' . ($primary['customerEmail'] ?? ''));
                            error_log('createManualBooking: ensure MS_GRAPH_TENANT_ID, MS_GRAPH_CLIENT_ID, MS_GRAPH_CLIENT_SECRET, MS_GRAPH_MAILBOX are set in .env');
                        }
                    } else {
                        error_log('createManualBooking: skipping confirmation email - no valid customerEmail for booking ' . ($primary['id'] ?? ''));
                    }
                    echo json_encode([
                        'success' => true,
                        'message' => 'Booking created',
                        'bookingId' => $createdBookings[0]['id'],
                        'booking' => $createdBookings[0],
                        'bookingsCreated' => count($createdBookings),
                        'confirmationEmailSent' => $emailSent
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to save booking']);
                }
                exit;
            }
        }

        // 2b. Admin-only actions
        requireAdmin();
        requireCsrf($input);

        // ADMIN: Get bookings (used by admin dashboard pages via POST)
        if ($action === 'getBookings') {
        // Maintenance tasks on load
            cleanupExpiredBookings($bookingsFile);
            archivePastBookings($bookingsFile, $bookingsArchiveFile);

            $bookings = loadBookings($bookingsFile);
            echo json_encode([
                'success' => true,
                'bookings' => $bookings,
                // Allow frontend to keep CSRF token fresh
                'csrfToken' => $_SESSION['csrf_token'] ?? ''
            ]);
            exit;
        }

        // ADMIN: Get archived bookings for booking history
        if ($action === 'getHistory') {
            cleanupExpiredBookings($bookingsFile);
            archivePastBookings($bookingsFile, $bookingsArchiveFile);

            $archive = loadBookings($bookingsArchiveFile);
            echo json_encode([
                'success' => true,
                'bookings' => $archive,
                'csrfToken' => $_SESSION['csrf_token'] ?? ''
            ]);
            exit;
        }

        // ADMIN: Get boats (used by boat-management via POST)
        if ($action === 'getBoats') {
            $boats = loadBoats($boatsFile);
            echo json_encode([
                'success' => true,
                'boats' => $boats,
                'data' => $boats,
                'csrfToken' => $_SESSION['csrf_token'] ?? ''
            ]);
            exit;
        }
        
        // ADMIN: Update existing booking (edit details without changing status or creating new)
        if ($action === 'updateBooking') {
            $bookingId = $input['bookingId'] ?? '';
            $bookingData = $input['bookingData'] ?? [];
            
            if (empty($bookingId) || !is_array($bookingData)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Booking ID and data required']);
                exit;
            }
            
            $bookingId = sanitizeText($bookingId);
            
            $bookings = loadBookings($bookingsFile);
            $boats = loadBoats($boatsFile);
            $found = false;
            
            foreach ($bookings as &$b) {
                if (($b['id'] ?? '') === $bookingId) {
                    $found = true;
                    // Update only allowed fields - NEVER overwrite status
                    $allowedFields = ['date', 'endDate', 'numberOfDays', 'boatType', 'customerName', 'customerEmail', 'customerPhone', 'arrivalTime', 'cityOfOrigin', 'notes'];
                    foreach ($allowedFields as $field) {
                        if (array_key_exists($field, $bookingData)) {
                            if (in_array($field, ['date', 'endDate'], true)) {
                                $date = DateTime::createFromFormat('Y-m-d', $bookingData[$field]);
                                $b[$field] = $date ? $date->format('Y-m-d') : ($b[$field] ?? '');
                            } elseif ($field === 'customerEmail') {
                                $email = filter_var(trim($bookingData[$field] ?? ''), FILTER_VALIDATE_EMAIL);
                                $b[$field] = $email ? strtolower($email) : ($b[$field] ?? '');
                            } else {
                                $b[$field] = sanitizeText($bookingData[$field] ?? '');
                            }
                        }
                    }
                    // Recalculate amount if dates or boat changed
                    $days = 1;
                    if (!empty($b['endDate']) && $b['endDate'] !== ($b['date'] ?? '')) {
                        $s = new DateTime($b['date']);
                        $e = new DateTime($b['endDate']);
                        $days = $s->diff($e)->days + 1;
                    }
                    $b['numberOfDays'] = $days;
                    $updateUseMotor = (($b['engineOption'] ?? 'without') === 'with');
                    $b['amount'] = calculatePrice($boats, $b['boatType'] ?? '', $days, $updateUseMotor);
                    $b['updatedAt'] = date('c');
                    break;
                }
            }
            
            if ($found && saveBookings($bookingsFile, $bookings)) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code($found ? 500 : 404);
                echo json_encode(['success' => false, 'message' => $found ? 'Save failed' : 'Booking not found']);
            }
            exit;
        }
        
        if ($action === 'updateBookingStatus') {
            $bookingId = $input['bookingId'] ?? '';
            $status = $input['status'] ?? '';
            
            if (!$bookingId || !$status) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing ID or status']);
                exit;
            }
            
            // Sanitize booking ID
            $bookingId = sanitizeText($bookingId);
            
            // Validate status is in allowed list (prevent injection)
            if (!isValidBookingStatus($status)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                exit;
            }
            
            $bookings = loadBookings($bookingsFile);
            $found = false;
            foreach ($bookings as &$b) {
                if ($b['id'] === $bookingId) {
                    // BOLA Protection: Admin can update any booking (current behavior)
                    // Future: Add organization/ownership checks if needed
                    $b['status'] = $status;
                    $b['updatedAt'] = date('c');
                    $found = true;
                    break;
                }
            }
            
            if ($found && saveBookings($bookingsFile, $bookings)) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found or save failed']);
            }
            exit;
        }
        
        if ($action === 'deleteBooking') {
            $bookingId = $input['bookingId'] ?? '';
            
            if (empty($bookingId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Booking ID required']);
                exit;
            }
            
            // Sanitize booking ID
            $bookingId = sanitizeText($bookingId);
            
            $bookings = loadBookings($bookingsFile);
            $initialCount = count($bookings);
            
            // BOLA Protection: Admin can delete any booking (current behavior)
            // Future: Add organization/ownership checks if multi-tenant support is needed
            $bookings = array_filter($bookings, function($b) use ($bookingId) {
                return ($b['id'] ?? '') !== $bookingId;
            });
            
            if (count($bookings) < $initialCount) {
                saveBookings($bookingsFile, array_values($bookings));
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
            }
            exit;
        }

        // SAVE BOATS ACTION (Admin Only)
        if ($action === 'saveBoats') {
            $boatsData = $input['boats'] ?? null;
            if (!$boatsData || !is_array($boatsData)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid boats data']);
                exit;
            }
            
            // Validate and sanitize boat data before saving
            $sanitizedBoats = [];
            foreach ($boatsData as $boat) {
                if (!is_array($boat)) continue;
                $sanitizedBoats[] = sanitizeBoatData($boat);
            }
            
            // Ensure at least one boat exists
            if (empty($sanitizedBoats)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No valid boats data provided']);
                exit;
            }

            if (saveBoats($boatsFile, $sanitizedBoats)) {
                echo json_encode(['success' => true, 'message' => 'Boats configuration saved']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save boats configuration']);
            }
            exit;
        }
        
        // SAVE FOR-SALE ITEMS ACTION (Admin Only)
        if ($action === 'saveForSaleItems') {
            $itemsData = $input['items'] ?? null;
            if ($itemsData === null || !is_array($itemsData)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid items data']);
                exit;
            }
            
            // Validate and sanitize for-sale items before saving
            $sanitizedItems = [];
            foreach ($itemsData as $item) {
                if (!is_array($item)) continue;
                $sanitizedItems[] = sanitizeForSaleItem($item);
            }

            // Create a backup before overwriting for-sale.json (best-effort)
            if (!backupJsonFile($forSaleFile, nijenhuis_data_dir() . '/backups', 10)) {
                error_log('backupJsonFile: Failed to create for-sale.json backup');
            }

            if (saveForSaleItems($forSaleFile, $sanitizedItems)) {
                echo json_encode(['success' => true, 'message' => 'For-sale items saved']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save for-sale items']);
            }
            exit;
        }

        // MIGRATE FOR-SALE IMAGES: Convert existing base64 data URIs to server files (one-time)
        if ($action === 'migrateForSaleImages') {
            $items = loadForSaleItems($forSaleFile);
            $uploadDir = realpath(__DIR__ . '/../frontend/Images/for-sale') . '/';
            $migrated = 0;
            $errors = [];

            $saveBase64Image = function($dataUri) use ($uploadDir, &$errors) {
                if (strpos($dataUri, 'data:image/') !== 0) {
                    return $dataUri; // Not base64, return as-is
                }
                if (preg_match('/^data:(image\/[a-z]+);base64,(.+)$/s', $dataUri, $matches)) {
                    $mime = $matches[1];
                    $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
                    $ext = $extMap[$mime] ?? null;
                    if (!$ext) {
                        $errors[] = 'Unsupported image type: ' . $mime;
                        return $dataUri;
                    }
                    $imageData = base64_decode($matches[2], true);
                    if ($imageData === false) {
                        $errors[] = 'Failed to decode base64 image';
                        return $dataUri;
                    }
                    $filename = 'forsale_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                    $destPath = $uploadDir . $filename;
                    if (file_put_contents($destPath, $imageData) === false) {
                        $errors[] = 'Failed to write file: ' . $filename;
                        return $dataUri;
                    }
                    return '/frontend/Images/for-sale/' . $filename;
                }
                return $dataUri;
            };

            foreach ($items as &$item) {
                $changed = false;
                if (isset($item['mainImage']) && strpos($item['mainImage'], 'data:image/') === 0) {
                    $item['mainImage'] = $saveBase64Image($item['mainImage']);
                    $item['image'] = $item['mainImage'];
                    $changed = true;
                    $migrated++;
                }
                if (isset($item['additionalImages']) && is_array($item['additionalImages'])) {
                    foreach ($item['additionalImages'] as &$img) {
                        if (strpos($img, 'data:image/') === 0) {
                            $img = $saveBase64Image($img);
                            $changed = true;
                            $migrated++;
                        }
                    }
                    unset($img);
                }
            }
            unset($item);

            if ($migrated > 0) {
                if (!backupJsonFile($forSaleFile, nijenhuis_data_dir() . '/backups', 10)) {
                    error_log('migrateForSaleImages: Failed to create backup before migration');
                }
                saveForSaleItems($forSaleFile, $items);
            }

            echo json_encode([
                'success' => true,
                'migrated' => $migrated,
                'errors' => $errors,
                'message' => "Migrated {$migrated} image(s) to server files."
            ]);
            exit;
        }
        
        break;
        
    case 'GET':
        // public or authenticated checks?
        // SECURITY: Sanitize GET parameter
        $action = sanitizeText($_GET['action'] ?? '');
        
        // ADMIN AUTH CHECK
        if ($action === 'checkAuth' || $action === 'session') {
            if (!empty($_SESSION['admin_authenticated'])) {
                // Clear any employee portal session data that might have been accidentally set
                if (empty($_SESSION['employee_authenticated'])) {
                    unset($_SESSION['employee_last_activity']);
                }
                
                echo json_encode([
                    'success' => true,
                    'authenticated' => true,
                    'user' => $_SESSION['admin_user'] ?? 'admin',
                    'csrfToken' => $_SESSION['csrf_token'] ?? '',
                    'username' => $_SESSION['admin_user'] ?? 'admin'
                ]);
            } else if (!empty($_SESSION['employee_authenticated'])) {
                echo json_encode([
                    'success' => true,
                    'authenticated' => true,
                    'user' => $_SESSION['employee_user'] ?? 'employee',
                    'csrfToken' => $_SESSION['csrf_token'] ?? ''
                ]);
            } else {
                echo json_encode(['success' => true, 'authenticated' => false]);
            }
            exit;
        }
        

        // PUBLIC boats action (for frontend)
        if ($action === 'boats') {
            $boats = loadBoats($boatsFile);
            echo json_encode(['success' => true, 'boats' => $boats]); // Frontend expects 'boats' key
            exit;
        }
        
        // PUBLIC: Check availability via GET (for frontend quantity dropdowns)
        if ($action === 'checkAvailability') {
            $bookings = loadBookings($bookingsFile);
            $boats = loadBoats($boatsFile);
            
            // Get parameters from GET query string
            $startDate = sanitizeText($_GET['date'] ?? date('Y-m-d'));
            $endDate = sanitizeText($_GET['endDate'] ?? $startDate);
            $boatType = sanitizeText($_GET['boatType'] ?? '');
            
            if ($boatType) {
                $result = checkBoatAvailability($bookings, $boats, $boatType, $startDate, $endDate);
                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Boat type required']);
            }
            exit;
        }

        // PUBLIC: Return only boat IDs that are available for a date range
        if ($action === 'getAvailableBoats') {
            $startDate = sanitizeText($_GET['date'] ?? '');
            $endDate = sanitizeText($_GET['endDate'] ?? $startDate);

            if ($startDate === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Date required']);
                exit;
            }
            if ($endDate === '') {
                $endDate = $startDate;
            }

            $bookings = loadBookings($bookingsFile);
            $boats = loadBoats($boatsFile);
            $availableBoatIds = [];
            $availableBoats = [];

            foreach ($boats as $boat) {
                $boatId = (string) ($boat['id'] ?? '');
                if ($boatId === '') {
                    continue;
                }

                $availability = checkBoatAvailability($bookings, $boats, $boatId, $startDate, $endDate);
                if (!empty($availability['available'])) {
                    $availableBoatIds[] = $boatId;
                    $availableBoats[] = [
                        'id' => $boatId,
                        'availableCount' => (int) ($availability['availableCount'] ?? 0),
                        'totalBoats' => (int) ($availability['totalBoats'] ?? 0),
                    ];
                }
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'date' => $startDate,
                    'endDate' => $endDate,
                    'availableBoatIds' => $availableBoatIds,
                    'availableBoats' => $availableBoats,
                ],
            ]);
            exit;
        }

        // PUBLIC: Get for-sale items (for frontend te-koop page)
        if ($action === 'getForSaleItems') {
            $items = loadForSaleItems($forSaleFile);
            echo json_encode(['success' => true, 'items' => $items]);
            exit;
        }

        // Require Admin for data retrieval
        requireAdmin();
        
        if ($action === 'getBookings') {
            // Maintenance tasks on load
            cleanupExpiredBookings($bookingsFile);
            archivePastBookings($bookingsFile, $bookingsArchiveFile);
            
            $bookings = loadBookings($bookingsFile);
            echo json_encode(['success' => true, 'bookings' => $bookings]);
            exit;
        }
        
        if ($action === 'getBoats') {
            $boats = loadBoats($boatsFile);
            // Return in format expected by frontend (both 'data' and 'boats' keys for compatibility)
            echo json_encode(['success' => true, 'boats' => $boats, 'data' => $boats]);
            exit;
        }
        
        if ($action === 'getArchivedBookings') {
            $archive = [];
            if (file_exists($bookingsArchiveFile)) {
                $archive = loadBookings($bookingsArchiveFile);
            }
            echo json_encode(['success' => true, 'data' => $archive]);
            exit;
        }
        
        // One-time migration: split existing bookings with quantity > 1 into separate records (one per boat)
        if ($action === 'migrate_bookings_quantity') {
            $splitActive = 0;
            $splitArchive = 0;
            
            $bookings = loadBookings($bookingsFile);
            $newBookings = [];
            foreach ($bookings as $b) {
                $qty = isset($b['quantity']) ? max(1, (int) $b['quantity']) : 1;
                if ($qty <= 1) {
                    $newBookings[] = $b;
                    continue;
                }
                $splitActive++;
                $amountEach = isset($b['amount']) ? round((float) $b['amount'] / $qty, 2) : 0;
                $baseId = $b['id'] ?? ('mig_' . uniqid());
                for ($i = 0; $i < $qty; $i++) {
                    $entry = $b;
                    $entry['quantity'] = 1;
                    $entry['amount'] = $amountEach;
                    $entry['id'] = $i === 0 ? $baseId : ($baseId . '_' . ($i + 1));
                    $newBookings[] = $entry;
                }
            }
            if ($splitActive > 0) {
                saveBookings($bookingsFile, $newBookings);
            }
            
            $archive = [];
            if (file_exists($bookingsArchiveFile)) {
                $archive = loadBookings($bookingsArchiveFile);
            }
            $newArchive = [];
            foreach ($archive as $b) {
                $qty = isset($b['quantity']) ? max(1, (int) $b['quantity']) : 1;
                if ($qty <= 1) {
                    $newArchive[] = $b;
                    continue;
                }
                $splitArchive++;
                $amountEach = isset($b['amount']) ? round((float) $b['amount'] / $qty, 2) : 0;
                $baseId = $b['id'] ?? ('mig_arch_' . uniqid());
                for ($i = 0; $i < $qty; $i++) {
                    $entry = $b;
                    $entry['quantity'] = 1;
                    $entry['amount'] = $amountEach;
                    $entry['id'] = $i === 0 ? $baseId : ($baseId . '_' . ($i + 1));
                    $newArchive[] = $entry;
                }
            }
            if ($splitArchive > 0) {
                saveBookings($bookingsArchiveFile, $newArchive);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Migration complete: one record per boat.',
                'splitActive' => $splitActive,
                'splitArchive' => $splitArchive
            ]);
            exit;
        }
        
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>