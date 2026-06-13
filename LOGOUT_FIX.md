# Logout Functionality Fix

## Issues Fixed

### 1. ✅ Missing Logout Handler
**Problem:** The backend didn't handle the `logout` action, so logout requests were ignored.

**Fix:** Added logout handler in `admin/booking-handler.php` that:
- Verifies CSRF token (if authenticated)
- Clears all session data
- Deletes session cookie
- Destroys the session completely

### 2. ✅ Improved Session Validation
**Problem:** Session check might have been too permissive.

**Fix:** Enhanced `admin-auth.php` to:
- Use strict type checking (`!== true` instead of loose check)
- Explicitly unset session variables on failed auth
- Clear partial session data

## How to Use

### Normal Logout
Click the "Uitloggen" (Logout) button in the admin navigation. This will:
1. Send logout request to server
2. Clear server-side session
3. Clear browser localStorage
4. Redirect to login page

### Emergency Session Clear
If logout button doesn't work, access:
```
http://localhost:8888/admin/clear-session.php
```

This will:
- Clear all session data
- Delete session cookie
- Clean up old session files
- Redirect to login page

## Testing

1. **Test Normal Logout:**
   - Login to admin area
   - Click "Uitloggen" button
   - Should redirect to login page
   - Try accessing admin page directly - should redirect to login

2. **Test Session Persistence:**
   - Login
   - Close browser tab (don't logout)
   - Reopen admin URL
   - Should still be logged in (session valid for 24 hours)

3. **Test Session Expiry:**
   - Login
   - Wait 24+ hours (or manually expire session)
   - Should redirect to login with expired message

## Manual Session Cleanup

If sessions are stuck, you can manually clear them:

```bash
# Find and clear session files (older than 1 hour)
find /var/lib/php/sessions -name "sess_*" -mmin +60 -delete

# Or clear all session files (WARNING: logs out everyone)
find /var/lib/php/sessions -name "sess_*" -delete
```

## Code Changes

### Added Logout Handler
Location: `admin/booking-handler.php` (after employeeLogin handler)

```php
// LOGOUT
if (($input['action'] ?? '') === 'logout') {
    // Verify CSRF token if authenticated
    // Clear session completely
    // Destroy session cookie
    // Return success
}
```

### Enhanced Session Check
Location: `admin/admin-auth.php`

- Changed from `empty()` check to explicit `!== true` check
- Added session cleanup on failed auth

## Troubleshooting

### Still Auto-Logged In?

1. **Clear browser cookies:**
   - Open browser dev tools (F12)
   - Application/Storage tab
   - Clear cookies for localhost:8888

2. **Use emergency clear:**
   - Visit `http://localhost:8888/admin/clear-session.php`

3. **Restart PHP server:**
   ```bash
   pkill -f "php -S localhost:8888"
   php -S localhost:8888 -t .
   ```

4. **Check session files:**
   ```bash
   ls -la /var/lib/php/sessions/sess_*
   ```

### Logout Button Not Working?

1. Check browser console for errors (F12)
2. Verify CSRF token is being sent
3. Check network tab to see if logout request succeeds
4. Try emergency clear script

## Security Notes

- Logout properly destroys server-side session
- Session cookie is deleted
- CSRF protection on logout (if authenticated)
- Session files are cleaned up
