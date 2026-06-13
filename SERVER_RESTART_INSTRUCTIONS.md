# Server Restart Instructions

## ✅ Server Status
The PHP development server should now be running with all security fixes applied.

## Quick Restart Commands

### Option 1: Using the provided script (Recommended)
```bash
cd /home/andre/Desktop/Projects/Nijenhuis
./scripts/start-server.sh
```

### Option 2: Manual restart
```bash
# Stop existing server
pkill -f "php -S localhost:8888"

# Start new server
cd /home/andre/Desktop/Projects/Nijenhuis
php -S localhost:8888 -t .
```

### Option 3: Background server (for development)
```bash
# Stop existing
pkill -f "php -S localhost:8888"

# Start in background
cd /home/andre/Desktop/Projects/Nijenhuis
nohup php -S localhost:8888 -t . > /tmp/php-server.log 2>&1 &
```

## Verify Server is Working

Test the endpoint:
```bash
curl http://localhost:8888/admin/booking-handler.php -X POST \
  -H "Content-Type: application/json" \
  -d '{"action":"checkAuth"}'
```

Expected response: `{"success":false,"message":"Unauthorized"}` (this is correct - means server is working)

## Check Server Logs

```bash
# If running in background, check log:
tail -f /tmp/php-server.log

# Or check PHP error log:
tail -f /tmp/php-debug.log
```

## Current Configuration

- **Server:** `http://localhost:8888`
- **CORS Origin:** `https://ultimaitech.com`
- **Password Hashing:** Enabled (Argon2ID)
- **Security Fixes:** All applied

## Troubleshooting

If you still get 500 errors:

1. **Check if server is running:**
   ```bash
   lsof -i :8888
   ```

2. **Check PHP syntax:**
   ```bash
   php -l admin/booking-handler.php
   php -l admin/api.php
   ```

3. **Check environment variables are loaded:**
   ```bash
   php -r "require 'components/data_access.php'; loadEnvSafe('.env'); echo getenv('ADMIN_USERNAME');"
   ```

4. **Restart server** (see commands above)

## Notes

- The server needs to be restarted after code changes to load new code
- Environment variables are loaded from `.env` file on each request
- All security fixes are now active
