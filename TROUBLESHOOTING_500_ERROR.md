# Troubleshooting 500 Internal Server Error

## Issue
Getting 500 error from `/admin/booking-handler.php`

## Root Cause Analysis

The error is likely caused by one of these issues:

### 1. ✅ Fixed: Missing Variable Definitions
**Status:** FIXED
- Variables `$envAdminPassHash` and `$envAdminPassPlain` were referenced but not defined
- **Fix Applied:** Added proper variable definitions in `admin/booking-handler.php`

### 2. ✅ Fixed: CORS Configuration
**Status:** FIXED  
**Chatbot Origin:** `https://ultimaitech.com`
- Updated `.env` with correct CORS origin
- **Current Setting:** `CHATBOT_ALLOWED_ORIGINS=https://ultimaitech.com`

### 3. ⚠️ Potential Issue: Web Server Cache
**Status:** NEEDS VERIFICATION
- PHP may have cached the old code
- Environment variables may not be reloaded

## Solutions

### Solution 1: Restart PHP/Web Server
```bash
# If using PHP built-in server, restart it
# If using Apache/Nginx, restart the service:
sudo systemctl restart apache2
# or
sudo systemctl restart nginx
sudo systemctl restart php-fpm
```

### Solution 2: Clear PHP OpCache (if enabled)
```bash
# Restart PHP-FPM to clear opcache
sudo systemctl restart php8.1-fpm  # Adjust version as needed
```

### Solution 3: Verify Environment Variables Are Loaded
Check if the web server can see the environment variables:

Create a test file `test-env.php`:
```php
<?php
require_once 'components/data_access.php';
loadEnvSafe(__DIR__ . '/.env');

header('Content-Type: application/json');
echo json_encode([
    'ADMIN_USERNAME' => getenv('ADMIN_USERNAME') ?: 'NOT SET',
    'ADMIN_PASSWORD_HASH' => getenv('ADMIN_PASSWORD_HASH') ? 'SET (' . substr(getenv('ADMIN_PASSWORD_HASH'), 0, 20) . '...)' : 'NOT SET',
    'ADMIN_PASSWORD' => getenv('ADMIN_PASSWORD') ? 'SET (legacy)' : 'NOT SET',
    'CHATBOT_ALLOWED_ORIGINS' => getenv('CHATBOT_ALLOWED_ORIGINS') ?: 'NOT SET'
]);
```

Access via browser: `http://localhost:8888/test-env.php`

### Solution 4: Check PHP Error Logs
```bash
# Check PHP error log
tail -f /var/log/php/error.log
# or
tail -f /tmp/php-debug.log
# or check Apache error log
tail -f /var/log/apache2/error.log
```

### Solution 5: Enable Error Display (Temporary)
Add to the top of `admin/booking-handler.php` (REMOVE AFTER DEBUGGING):
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
```

## Verification Steps

1. ✅ Code syntax is valid (tested with `php -l`)
2. ✅ Security functions load correctly
3. ✅ Environment variables are readable
4. ✅ Password verification works
5. ⚠️ Web server needs restart to load new code

## Current Configuration

- **CORS Origin:** `https://ultimaitech.com`
- **Password Hash:** Argon2ID (secure)
- **Legacy Support:** Enabled (both hash and plain text supported)

## Next Steps

1. **Restart your web server** (most likely fix)
2. **Check error logs** for specific error message
3. **Test the endpoint** after restart
4. **Remove test files** after debugging

## Expected Behavior After Fix

- Login should work with existing passwords (backward compatible)
- API endpoints should respond correctly
- CORS should allow requests from `https://ultimaitech.com`
- No 500 errors
