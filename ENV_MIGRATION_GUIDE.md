# 🔐 Environment Variables Migration Guide

## Overview

After the security fixes, your `.env` file needs to be updated to use **password hashes** instead of plain text passwords. This guide will help you migrate safely.

## ⚠️ Important Security Changes

1. **Plain text passwords are NO LONGER SUPPORTED**
2. **Password hashes are REQUIRED** (`ADMIN_PASSWORD_HASH`, `EMPLOYEE_PASSWORD_HASH`)
3. **Webhook secret is REQUIRED** (`MOLLIE_WEBHOOK_SECRET`)
4. **CORS origins must be configured** (`CHATBOT_ALLOWED_ORIGINS`)

## 📋 Migration Steps

### Step 1: Generate Password Hashes

Run the migration script to generate password hashes:

```bash
php scripts/migrate-passwords.php
```

This will prompt you for:
- Admin username
- Admin password
- Employee username (optional)
- Employee password (optional)

The script will output the hashed values you need to add to your `.env` file.

### Step 2: Update Your .env File

Open your `.env` file and make the following changes:

#### Remove Old Password Lines:
```bash
# REMOVE THESE LINES:
ADMIN_PASSWORD=your_plain_text_password
EMPLOYEE_PASSWORD=your_plain_text_password
```

#### Add New Password Hash Lines:
```bash
# ADD THESE LINES (use values from migration script):
ADMIN_PASSWORD_HASH=$2y$10$generated_hash_here
EMPLOYEE_PASSWORD_HASH=$2y$10$generated_hash_here
```

#### Ensure Required Variables Are Set:

```bash
# Required for webhook security
MOLLIE_WEBHOOK_SECRET=your_webhook_secret_from_mollie

# Required for chatbot API security
CHATBOT_ALLOWED_ORIGINS=https://your-chatbot-domain.com,https://api.your-provider.com
BOOKING_API_KEY=your_secure_api_key_here
INVENTORY_API_KEY=your_secure_api_key_here

# Set environment
APP_ENV=production
```

### Step 3: Verify Configuration

Check that your `.env` file has:

✅ `ADMIN_USERNAME`  
✅ `ADMIN_PASSWORD_HASH` (NOT `ADMIN_PASSWORD`)  
✅ `EMPLOYEE_USERNAME` (if using)  
✅ `EMPLOYEE_PASSWORD_HASH` (if using, NOT `EMPLOYEE_PASSWORD`)  
✅ `MOLLIE_WEBHOOK_SECRET`  
✅ `CHATBOT_ALLOWED_ORIGINS` (if using chatbot API)  
✅ `APP_ENV=production`  

### Step 4: Test Login

1. Restart your web server
2. Test admin login
3. Test employee login (if configured)
4. Verify webhook processing works

### Step 5: Remove Plain Text Passwords

Once everything works, **remove any remaining plain text password lines** from `.env`:

```bash
# DELETE THESE IF STILL PRESENT:
ADMIN_PASSWORD=...
EMPLOYEE_PASSWORD=...
```

## 🔧 Quick Migration Script

If you prefer an automated approach, you can use:

```bash
php scripts/migrate-passwords-auto.php
```

This script will:
- Read existing passwords from `.env`
- Generate hashes automatically
- Update `.env` file with hashes
- Add missing security configurations

**Note:** This script requires your current `.env` to have `ADMIN_PASSWORD` set.

## 📝 Example .env Configuration

```bash
# ============================================
# SECURITY CONFIGURATION
# ============================================

# Application Environment
APP_ENV=production
APP_DEBUG=false

# Admin Authentication (HASHED PASSWORDS ONLY)
ADMIN_USERNAME=admin
ADMIN_PASSWORD_HASH=$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy

# Employee Authentication (HASHED PASSWORDS ONLY)
EMPLOYEE_USERNAME=employee
EMPLOYEE_PASSWORD_HASH=$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy

# ============================================
# MOLLIE PAYMENT INTEGRATION
# ============================================
MOLLIE_API_KEY=live_your_mollie_api_key
MOLLIE_WEBHOOK_SECRET=your_webhook_secret_from_mollie

# ============================================
# API SECURITY
# ============================================
CHATBOT_ALLOWED_ORIGINS=https://your-chatbot-domain.com
BOOKING_API_KEY=your_secure_booking_api_key
INVENTORY_API_KEY=your_secure_inventory_api_key

# ============================================
# OTHER CONFIGURATION
# ============================================
# ... rest of your configuration
```

## ⚠️ Troubleshooting

### Error: "Admin credentials not configured"

**Cause:** Missing `ADMIN_PASSWORD_HASH` in `.env`

**Solution:**
1. Run `php scripts/migrate-passwords.php`
2. Add the generated `ADMIN_PASSWORD_HASH` to `.env`
3. Restart web server

### Error: "Webhook configuration error"

**Cause:** Missing `MOLLIE_WEBHOOK_SECRET` in production

**Solution:**
1. Get webhook secret from Mollie dashboard
2. Add `MOLLIE_WEBHOOK_SECRET=...` to `.env`
3. Ensure `APP_ENV=production` is set

### Error: "Origin not allowed"

**Cause:** CORS origin not configured

**Solution:**
1. Add `CHATBOT_ALLOWED_ORIGINS=https://your-domain.com` to `.env`
2. Restart web server

## 🔒 Security Best Practices

1. **Never commit `.env` files** to version control
2. **Use strong passwords** before hashing
3. **Rotate API keys** regularly
4. **Use different passwords** for admin and employee accounts
5. **Keep `.env` file permissions** restricted (chmod 600)
6. **Backup `.env` securely** before making changes

## 📞 Need Help?

If you encounter issues:
1. Check the error logs
2. Verify all required variables are set
3. Ensure password hashes are correctly formatted (start with `$2y$` or `$argon2`)
4. Test with the migration scripts

---

**Last Updated:** January 2025  
**Security Status:** ✅ Password hashing enforced
