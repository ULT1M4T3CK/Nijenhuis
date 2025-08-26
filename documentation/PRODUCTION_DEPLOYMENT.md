# Production Deployment Guide

## Server Configuration
- **Server IP:** 85.215.195.147
- **Web Directory:** `/var/www/nijenhuis`
- **Webhook Port:** 8080
- **Web Server:** Apache2

## 🚀 Quick Deployment

### 1. Run the Deployment Script
```bash
./deploy_to_server.sh
```

This script will:
- Upload all website files to the server
- Set up Apache web server configuration
- Install and configure the webhook handler
- Create systemd service for automatic startup
- Set proper permissions

### 2. Verify Deployment
```bash
# Test website access
curl -I http://85.215.195.147

# Test webhook handler
curl http://85.215.195.147:8080/

# Check webhook service status
ssh root@85.215.195.147 "sudo systemctl status mollie-webhook"
```

## 🔧 Manual Setup (Alternative)

### 1. Server Preparation
```bash
# Connect to server
ssh root@85.215.195.147

# Update system
apt update && apt upgrade -y

# Install required packages
apt install -y apache2 python3 python3-pip git
```

### 2. Upload Website Files
```bash
# Create web directory
mkdir -p /var/www/nijenhuis

# Upload files (from your local machine)
scp -r * root@85.215.195.147:/var/www/nijenhuis/
```

### 3. Configure Apache
```bash
# Create virtual host configuration
cat > /etc/apache2/sites-available/nijenhuis.conf << 'EOF'
<VirtualHost *:80>
    ServerName 85.215.195.147
    DocumentRoot /var/www/nijenhuis
    
    <Directory /var/www/nijenhuis>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Proxy webhook requests to Python handler
    ProxyPass /webhook http://localhost:8080/webhook
    ProxyPassReverse /webhook http://localhost:8080/webhook
    
    ErrorLog ${APACHE_LOG_DIR}/nijenhuis_error.log
    CustomLog ${APACHE_LOG_DIR}/nijenhuis_access.log combined
</VirtualHost>
EOF

# Enable required modules
a2enmod proxy
a2enmod proxy_http
a2enmod rewrite

# Enable site and restart Apache
a2ensite nijenhuis
systemctl restart apache2
```

### 4. Set Up Webhook Handler
```bash
cd /var/www/nijenhuis

# Install Python dependencies
pip3 install requests

# Make webhook handler executable
chmod +x backend/webhooks/mollie/webhook_handler_production.py

# Create systemd service
cat > /etc/systemd/system/mollie-webhook.service << 'EOF'
[Unit]
Description=Mollie Webhook Handler
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/nijenhuis
ExecStart=/usr/bin/python3 /var/www/nijenhuis/backend/webhooks/mollie/webhook_handler_production.py 8080
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

# Enable and start service
systemctl daemon-reload
systemctl enable mollie-webhook
systemctl start mollie-webhook

# Set permissions
chown -R www-data:www-data /var/www/nijenhuis
chmod -R 755 /var/www/nijenhuis
```

## 🔐 Production Security

### 1. SSL Certificate (Recommended)
```bash
# Install Certbot
apt install -y certbot python3-certbot-apache

# Get SSL certificate
certbot --apache -d yourdomain.com

# Auto-renewal
crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 2. Firewall Configuration
```bash
# Install UFW
apt install -y ufw

# Configure firewall
ufw allow ssh
ufw allow 80
ufw allow 443
ufw allow 8080
ufw enable
```

### 3. Update Mollie Configuration
```bash
# Edit webhook handler
nano /var/www/nijenhuis/backend/webhooks/mollie/webhook_handler_production.py

# Replace test API key with live key
# self.mollie_api_key = 'live_YOUR_PRODUCTION_API_KEY'
```

## 📊 Monitoring & Logs

### 1. Webhook Logs
```bash
# View webhook logs
tail -f /var/log/mollie_webhook.log

# View systemd logs
journalctl -u mollie-webhook -f
```

### 2. Apache Logs
```bash
# Access logs
tail -f /var/log/apache2/nijenhuis_access.log

# Error logs
tail -f /var/log/apache2/nijenhuis_error.log
```

### 3. System Monitoring
```bash
# Check service status
systemctl status mollie-webhook
systemctl status apache2

# Check disk space
df -h

# Check memory usage
free -h
```

## 🔄 Maintenance

### 1. Update Website
```bash
# Upload new files
scp -r * root@85.215.195.147:/var/www/nijenhuis/

# Restart webhook handler
systemctl restart mollie-webhook
```

### 2. Backup
```bash
# Create backup script
cat > /root/backup_nijenhuis.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/backup/nijenhuis"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR
tar -czf $BACKUP_DIR/nijenhuis_$DATE.tar.gz /var/www/nijenhuis
cp /var/www/nijenhuis/bookings.db $BACKUP_DIR/bookings_$DATE.db

# Keep only last 7 days of backups
find $BACKUP_DIR -name "nijenhuis_*.tar.gz" -mtime +7 -delete
find $BACKUP_DIR -name "bookings_*.db" -mtime +7 -delete
EOF

chmod +x /root/backup_nijenhuis.sh

# Add to crontab for daily backups
crontab -e
# Add: 0 2 * * * /root/backup_nijenhuis.sh
```

## 🚨 Troubleshooting

### 1. Webhook Not Working
```bash
# Check if service is running
systemctl status mollie-webhook

# Check logs
journalctl -u mollie-webhook -f

# Test webhook endpoint
curl -X POST http://localhost:8080/webhook/mollie \
  -H "Content-Type: application/json" \
  -d '{"id":"test123","status":"paid"}'
```

### 2. Website Not Loading
```bash
# Check Apache status
systemctl status apache2

# Check Apache logs
tail -f /var/log/apache2/error.log

# Test Apache configuration
apache2ctl configtest
```

### 3. Permission Issues
```bash
# Fix permissions
chown -R www-data:www-data /var/www/nijenhuis
chmod -R 755 /var/www/nijenhuis
chmod 666 /var/log/mollie_webhook.log
```

## 📋 Checklist

- [ ] Website files uploaded to `/var/www/nijenhuis`
- [ ] Apache virtual host configured
- [ ] Webhook handler running on port 8080
- [ ] Systemd service enabled and running
- [ ] Firewall configured
- [ ] SSL certificate installed (recommended)
- [ ] Mollie API key updated to production
- [ ] Backup system configured
- [ ] Monitoring set up
- [ ] Test booking flow end-to-end

## 🔗 URLs

- **Website:** http://85.215.195.147
- **Admin Area:** http://85.215.195.147/admin/admin-login.html
- **Webhook Health Check:** http://85.215.195.147:8080/
- **Mollie Webhook URL:** http://85.215.195.147/webhook/mollie

## 📞 Support

For issues with the deployment:
1. Check the logs: `/var/log/mollie_webhook.log`
2. Verify service status: `systemctl status mollie-webhook`
3. Test webhook endpoint manually
4. Check Apache configuration and logs

---

**Last Updated:** January 2024
**Server:** 85.215.195.147 