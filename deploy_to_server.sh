#!/bin/bash

# Deployment script for Nijenhuis Botenverhuur
# Server: 85.215.195.147

echo "🚀 Deploying Nijenhuis Botenverhuur to server 85.215.195.147"

# Configuration
SERVER_IP="85.215.195.147"
SERVER_USER="root"  # Change this to your server username
WEB_DIR="/var/www/nijenhuis"
WEBHOOK_PORT="8080"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if SSH key is available
if [ ! -f ~/.ssh/id_rsa ]; then
    print_warning "SSH key not found. You may need to enter your password."
fi

print_status "Creating web directory on server..."
ssh $SERVER_USER@$SERVER_IP "mkdir -p $WEB_DIR"

print_status "Uploading website files..."
scp -r frontend/dist/ $SERVER_USER@$SERVER_IP:$WEB_DIR/
scp -r backend/webhooks/mollie/ $SERVER_USER@$SERVER_IP:$WEB_DIR/backend/webhooks/mollie/

print_status "Uploading webhook handler..."
scp backend/webhooks/mollie/webhook_handler_production.py $SERVER_USER@$SERVER_IP:$WEB_DIR/backend/webhooks/mollie/

print_status "Setting up webhook handler on server..."
ssh $SERVER_USER@$SERVER_IP << EOF
    cd $WEB_DIR
    
    # Install Python dependencies
    pip3 install requests
    
    # Make webhook handler executable
    chmod +x backend/webhooks/mollie/webhook_handler_production.py
    
    # Create log directory
    sudo mkdir -p /var/log
    sudo touch /var/log/mollie_webhook.log
    sudo chmod 666 /var/log/mollie_webhook.log
    
    # Create systemd service for webhook handler
    sudo tee /etc/systemd/system/mollie-webhook.service > /dev/null << 'SERVICE'
[Unit]
Description=Mollie Webhook Handler
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=$WEB_DIR
ExecStart=/usr/bin/python3 $WEB_DIR/backend/webhooks/mollie/webhook_handler_production.py $WEBHOOK_PORT
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
SERVICE

    # Reload systemd and enable service
    sudo systemctl daemon-reload
    sudo systemctl enable mollie-webhook
    sudo systemctl start mollie-webhook
    
    # Set proper permissions
    sudo chown -R www-data:www-data $WEB_DIR
    sudo chmod -R 755 $WEB_DIR
EOF

print_status "Setting up web server configuration..."
ssh $SERVER_USER@$SERVER_IP << EOF
    # Install Apache if not already installed
    sudo apt update
    sudo apt install -y apache2
    
    # Create Apache virtual host configuration
    sudo tee /etc/apache2/sites-available/nijenhuis.conf > /dev/null << 'APACHE'
<VirtualHost *:80>
    ServerName $SERVER_IP
    DocumentRoot $WEB_DIR
    
    <Directory $WEB_DIR>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Proxy webhook requests to Python handler
    ProxyPass /webhook http://localhost:$WEBHOOK_PORT/webhook
    ProxyPassReverse /webhook http://localhost:$WEBHOOK_PORT/webhook
    
    ErrorLog \${APACHE_LOG_DIR}/nijenhuis_error.log
    CustomLog \${APACHE_LOG_DIR}/nijenhuis_access.log combined
</VirtualHost>
APACHE

    # Enable required Apache modules
    sudo a2enmod proxy
    sudo a2enmod proxy_http
    sudo a2enmod rewrite
    
    # Enable the site and restart Apache
    sudo a2ensite nijenhuis
    sudo systemctl restart apache2
EOF

print_status "Testing webhook handler..."
ssh $SERVER_USER@$SERVER_IP "curl -s http://localhost:$WEBHOOK_PORT/"

print_status "Checking webhook handler status..."
ssh $SERVER_USER@$SERVER_IP "sudo systemctl status mollie-webhook --no-pager"

print_status "Testing website access..."
curl -s -I "http://$SERVER_IP" | head -1

print_status "🎉 Deployment completed!"
echo ""
echo "📋 Next steps:"
echo "1. Test the website: http://$SERVER_IP"
echo "2. Test the webhook: http://$SERVER_IP:$WEBHOOK_PORT/"
echo "3. Check webhook logs: sudo tail -f /var/log/mollie_webhook.log"
echo "4. Update Mollie webhook URL to: http://$SERVER_IP/webhook/mollie"
echo ""
echo "🔧 Useful commands:"
echo "  - Restart webhook: sudo systemctl restart mollie-webhook"
echo "  - View logs: sudo journalctl -u mollie-webhook -f"
echo "  - Check Apache: sudo systemctl status apache2"
echo ""
echo "⚠️  Remember to:"
echo "  - Replace test API key with live key in production"
echo "  - Set up SSL certificate for HTTPS"
echo "  - Configure firewall rules"
echo "  - Set up regular backups" 