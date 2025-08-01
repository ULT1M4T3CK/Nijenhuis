# Plesk Deployment Guide for Strato Hosting

## 🎯 Overview
This guide is specifically for deploying the Nijenhuis Botenverhuur website on Strato hosting using Plesk control panel.

## 📋 Prerequisites
- ✅ Strato hosting account with Plesk access
- ✅ Plesk installer downloaded and installed
- ✅ Domain or subdomain configured
- ✅ FTP access credentials

## 🚀 Step-by-Step Deployment

### **Step 1: Access Plesk Control Panel**

1. **Install Plesk** (if not already done):
   - Run `plesk-installer.exe`
   - Follow installation wizard

2. **Access Plesk**:
   - Open browser and go to: `https://85.215.195.147:8443`
   - Or use the URL provided by Strato in your hosting confirmation email

3. **Login** with your Strato hosting credentials

### **Step 2: Configure Domain**

1. **Go to:** `Domains` in Plesk
2. **Add Domain** (if not already configured):
   - **Domain name:** Your domain or use the IP
   - **Document root:** `/httpdocs` (default)
   - **PHP version:** 7.4 or higher
   - **Enable:** Apache web server

### **Step 3: Upload Website Files**

#### **Option A: Using Plesk File Manager**
1. **Go to:** `Files` → `File Manager`
2. **Navigate to:** `/httpdocs`
3. **Upload all files:**
   ```
   pages/
   js/
   admin/
   Images/
   *.html
   *.css
   *.js
   *.json
   *.xml
   *.txt
   webhook_handler_plesk.php
   ```

#### **Option B: Using FTP (Recommended)**
1. **Get FTP credentials:**
   - Go to: `Domains` → `Your Domain` → `FTP Access`
   - Create new FTP account or note existing credentials

2. **Use FTP client** (FileZilla, WinSCP, etc.):
   ```
   Host: 85.215.195.147
   Username: [Your FTP username]
   Password: [Your FTP password]
   Port: 21
   ```

3. **Upload files** to `/httpdocs` directory

### **Step 4: Configure PHP Settings**

1. **Go to:** `Domains` → `Your Domain` → `PHP Settings`
2. **Configure:**
   - **PHP version:** 7.4 or higher
   - **Memory limit:** 256M or higher
   - **Max execution time:** 300 seconds
   - **Enable:** cURL extension

### **Step 5: Test Webhook Handler**

1. **Test health check:**
   ```
   https://yourdomain.com/webhook_handler_plesk.php
   ```
   Should return: `{"status":"success","message":"Mollie Webhook Handler is running"}`

2. **Check file permissions:**
   - `webhook_handler_plesk.php` should be readable (644)
   - `local_bookings.json` should be writable (666)
   - `webhook_log.txt` should be writable (666)

### **Step 6: Configure Mollie Webhook**

1. **In Mollie Dashboard:**
   - Go to: `Settings` → `Webhooks`
   - **Add webhook URL:** `https://yourdomain.com/webhook_handler_plesk.php`
   - **Select events:** Payment status changes

2. **Test webhook:**
   - Use Mollie's webhook testing tool
   - Or manually test with curl:
   ```bash
   curl -X POST https://yourdomain.com/webhook_handler_plesk.php \
     -H "Content-Type: application/json" \
     -d '{"id":"tr_test123","status":"paid"}'
   ```

## 🔧 Configuration Files

### **File Structure on Server:**
```
/httpdocs/
├── index.html                    # Main website
├── pages/                        # Website pages
│   ├── botenverhuur.html
│   ├── contact.html
│   ├── payment-success.html
│   └── ...
├── js/                          # JavaScript files
│   ├── mollie-payment.js
│   ├── booking-system-simple.js
│   └── ...
├── admin/                       # Admin area
│   ├── admin-login.html
│   ├── admin-simple.html
│   └── boat-management.html
├── Images/                      # Images and assets
├── webhook_handler_plesk.php    # Webhook handler
├── local_bookings.json          # Booking storage (auto-created)
└── webhook_log.txt              # Webhook logs (auto-created)
```

### **Important Files:**
- **`webhook_handler_plesk.php`** - Handles Mollie payment notifications
- **`local_bookings.json`** - Stores booking data (created automatically)
- **`webhook_log.txt`** - Logs webhook events (created automatically)

## 🔐 Security Configuration

### **1. File Permissions**
```bash
# Set proper permissions via Plesk File Manager or FTP
webhook_handler_plesk.php: 644 (readable)
local_bookings.json: 666 (readable/writable)
webhook_log.txt: 666 (readable/writable)
```

### **2. SSL Certificate**
1. **In Plesk:** `Domains` → `Your Domain` → `SSL/TLS Certificates`
2. **Install certificate** (Let's Encrypt or purchased)
3. **Force HTTPS** for secure connections

### **3. Update API Keys**
1. **Edit:** `webhook_handler_plesk.php`
2. **Replace:** `test_sHQfqTngBbCpEfMyMCPGH92gnm8P7m` with your live Mollie API key
3. **Edit:** `js/mollie-payment.js` and update the API key there too

## 📊 Monitoring & Logs

### **1. Webhook Logs**
- **File:** `/httpdocs/webhook_log.txt`
- **View via:** Plesk File Manager or FTP
- **Format:** `[timestamp] message`

### **2. Apache Logs**
- **Access logs:** Available in Plesk under `Logs`
- **Error logs:** Check for PHP errors and webhook issues

### **3. Test Webhook Functionality**
```bash
# Test health check
curl https://yourdomain.com/webhook_handler_plesk.php

# Test webhook processing
curl -X POST https://yourdomain.com/webhook_handler_plesk.php \
  -H "Content-Type: application/json" \
  -d '{"id":"tr_test123","status":"paid"}'
```

## 🔄 Maintenance

### **1. Update Website**
1. **Upload new files** via FTP or Plesk File Manager
2. **No service restart needed** (unlike VPS deployment)
3. **Test functionality** after updates

### **2. Backup**
1. **In Plesk:** `Tools & Settings` → `Backup Manager`
2. **Create backup** of your domain
3. **Download backup** for local storage

### **3. Monitor Logs**
- **Check webhook logs** regularly for errors
- **Monitor booking data** in `local_bookings.json`
- **Review Apache logs** for any issues

## 🚨 Troubleshooting

### **1. Webhook Not Working**
- **Check file permissions** on `webhook_handler_plesk.php`
- **Verify PHP cURL extension** is enabled
- **Check webhook logs** in `webhook_log.txt`
- **Test webhook endpoint** manually

### **2. Website Not Loading**
- **Check domain configuration** in Plesk
- **Verify file upload** to `/httpdocs`
- **Check Apache logs** in Plesk
- **Test PHP functionality**

### **3. Payment Issues**
- **Verify Mollie API key** is correct
- **Check webhook URL** in Mollie dashboard
- **Test payment flow** end-to-end
- **Review webhook logs** for errors

## 📋 Deployment Checklist

- [ ] Plesk access configured
- [ ] Domain/subdomain set up
- [ ] All website files uploaded to `/httpdocs`
- [ ] `webhook_handler_plesk.php` uploaded and accessible
- [ ] PHP settings configured (7.4+, cURL enabled)
- [ ] File permissions set correctly
- [ ] Webhook handler tested and working
- [ ] Mollie webhook URL configured
- [ ] SSL certificate installed (recommended)
- [ ] API keys updated to production
- [ ] End-to-end payment flow tested
- [ ] Admin area accessible and functional

## 🔗 Important URLs

- **Website:** `https://yourdomain.com`
- **Admin Login:** `https://yourdomain.com/admin/admin-login.html`
- **Webhook Health Check:** `https://yourdomain.com/webhook_handler_plesk.php`
- **Mollie Webhook URL:** `https://yourdomain.com/webhook_handler_plesk.php`

## 📞 Support

For Plesk/Strato specific issues:
1. **Check Plesk documentation** and help resources
2. **Contact Strato support** for hosting issues
3. **Review webhook logs** for application errors
4. **Test webhook functionality** manually

---

**Last Updated:** January 2024
**Hosting:** Strato with Plesk
**Server:** 85.215.195.147 