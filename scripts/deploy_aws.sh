#!/bin/bash

# ============================================================
# AWS Deployment Script for Nijenhuis Botenverhuur
# Target: Amazon Linux EC2 Instance
# PHP Website with Python Backend
# ============================================================

set -e

# Configuration
# EC2: nijenhuis-deploy | i-05209c73b5e3680a8 | Amazon Linux 2023
AWS_IP="51.20.126.15"
AWS_USER="ec2-user"
PEM_KEY="/home/andre/Documents/Instances/PEM/ultimAItech.pem"
REMOTE_DIR="/home/ec2-user/nijenhuis"
LOCAL_DIR="/home/andre/Desktop/Projects/nijenhuis"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${GREEN}[INFO]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }
log_step() { echo -e "${BLUE}[STEP]${NC} $1"; }

SSH_CMD="ssh -i $PEM_KEY -o StrictHostKeyChecking=no $AWS_USER@$AWS_IP"
SCP_CMD="scp -i $PEM_KEY -o StrictHostKeyChecking=no"

# Verify PEM key exists
if [ ! -f "$PEM_KEY" ]; then
    log_error "PEM key not found: $PEM_KEY"
    exit 1
fi

# Ensure proper permissions on PEM key
chmod 400 "$PEM_KEY"

echo "=============================================="
echo "  Deploying Nijenhuis to AWS EC2"
echo "  IP: $AWS_IP"
echo "=============================================="

# Step 0: Minify CSS/JS for production (fixes unminified asset audit)
log_step "0/6 Minifying CSS and JS..."
(cd "$LOCAL_DIR" && npm run minify 2>/dev/null) || log_warn "Minify skipped (run: npm install && npm run minify)"

# Step 1: Sync files to AWS
# Note: admin/boats.json and data/boats.json are excluded so live fleet counts
# edited in admin are not overwritten on deploy (same as bookings/for-sale).
# Blog CMS is included (blog-portal/); configure auth on the server (.env / blog password).
log_step "1/6 Syncing files to AWS..."
rsync -avz --progress \
    -e "ssh -i $PEM_KEY -o StrictHostKeyChecking=no" \
    --exclude 'venv' \
    --exclude 'node_modules' \
    --exclude '__pycache__' \
    --exclude '.git' \
    --exclude 'logs/*.log' \
    --exclude '*.pyc' \
    --exclude 'admin/bookings.json' \
    --exclude 'admin/bookings_archive.json' \
    --exclude 'admin/admin_sessions.json' \
    --exclude 'admin/for-sale.json' \
    --exclude 'admin/boats.json' \
    --exclude 'data/bookings.json' \
    --exclude 'data/boats.json' \
    --exclude 'data/bookings_archive.json' \
    --exclude 'data/for-sale.json' \
    --exclude 'data/articles.json' \
    --exclude 'data/blog-styles.json' \
    --exclude 'data/blog-redirects.json' \
    --exclude '.env' \
    "$LOCAL_DIR/" "$AWS_USER@$AWS_IP:$REMOTE_DIR/"

# Step 2: Install system dependencies
log_step "2/6 Installing system dependencies on AWS..."
$SSH_CMD << 'REMOTE_DEPS'
set -e

# Update system
sudo dnf update -y

# Install PHP and PHP-FPM with required extensions
sudo dnf install -y php php-fpm php-cli php-common php-json php-mbstring php-xml php-curl php-opcache

# Install Python 3 and pip
sudo dnf install -y python3 python3-pip python3-devel gcc

# Install nginx
sudo dnf install -y nginx

# Configure PHP-FPM for ec2-user
sudo sed -i 's/^user = .*/user = ec2-user/' /etc/php-fpm.d/www.conf
sudo sed -i 's/^group = .*/group = ec2-user/' /etc/php-fpm.d/www.conf
sudo sed -i 's/^listen = .*/listen = \/run\/php-fpm\/www.sock/' /etc/php-fpm.d/www.conf
sudo sed -i 's/^;listen.owner = .*/listen.owner = nginx/' /etc/php-fpm.d/www.conf
sudo sed -i 's/^;listen.group = .*/listen.group = nginx/' /etc/php-fpm.d/www.conf
sudo sed -i 's/^;listen.mode = .*/listen.mode = 0660/' /etc/php-fpm.d/www.conf

# Create PHP-FPM run directory
sudo mkdir -p /run/php-fpm
sudo chown ec2-user:ec2-user /run/php-fpm

# Enable and start services
sudo systemctl enable nginx php-fpm
sudo systemctl start php-fpm

# Fix PHP session permissions for ec2-user
if [ -d /var/lib/php/session ]; then
    sudo chown -R ec2-user:ec2-user /var/lib/php/session
fi
REMOTE_DEPS

# Larger than default upload_max_filesize (~2M) so te koop / admin image uploads work (app allows up to 5M)
log_step "2b/6 Installing PHP upload limits (php.d)..."
$SCP_CMD "$LOCAL_DIR/deploy/aws/99-nijenhuis-uploads.ini" "$AWS_USER@$AWS_IP:/tmp/"
$SSH_CMD << 'REMOTE_PHP_UPLOADS'
set -e
sudo mv /tmp/99-nijenhuis-uploads.ini /etc/php.d/99-nijenhuis-uploads.ini
sudo chmod 644 /etc/php.d/99-nijenhuis-uploads.ini
sudo systemctl restart php-fpm
REMOTE_PHP_UPLOADS

# Step 3: Set up Python virtual environment
log_step "3/6 Setting up Python virtual environment..."
$SSH_CMD << REMOTE_VENV
set -e
cd $REMOTE_DIR
python3 -m venv venv
source venv/bin/activate
pip install --upgrade pip
pip install -r requirements-aws.txt
pip install gunicorn
REMOTE_VENV

# Step 4: Create directories and set permissions
log_step "4/6 Creating directories and setting permissions..."
$SSH_CMD << REMOTE_DIRS
set -e
mkdir -p $REMOTE_DIR/logs
mkdir -p $REMOTE_DIR/config
touch $REMOTE_DIR/logs/backend_server.log
chmod -R 755 $REMOTE_DIR
# Allow nginx to access the files
sudo chown -R ec2-user:ec2-user $REMOTE_DIR
REMOTE_DIRS

# Step 5: Install systemd services and nginx config
log_step "5/6 Installing systemd services and nginx config..."
$SCP_CMD "$LOCAL_DIR/deploy/aws/nijenhuis-backend.service" "$AWS_USER@$AWS_IP:/tmp/"
$SCP_CMD "$LOCAL_DIR/deploy/aws/nginx-aws.conf" "$AWS_USER@$AWS_IP:/tmp/"

$SSH_CMD << 'REMOTE_SERVICES'
set -e

# Install systemd services
sudo mv /tmp/nijenhuis-backend.service /etc/systemd/system/

# Install nginx config
sudo mv /tmp/nginx-aws.conf /etc/nginx/conf.d/nijenhuis.conf
sudo rm -f /etc/nginx/conf.d/default.conf 2>/dev/null || true

# Remove default nginx server block if it exists
sudo rm -f /etc/nginx/sites-enabled/default 2>/dev/null || true

# Test nginx config
sudo nginx -t

# Reload and enable services
sudo systemctl daemon-reload
sudo systemctl enable nijenhuis-backend
sudo systemctl restart nginx php-fpm
sudo systemctl restart nijenhuis-backend
REMOTE_SERVICES

# Step 6: Verify deployment
log_step "6/6 Verifying deployment..."
sleep 5

echo ""
log_info "Checking service status..."
$SSH_CMD "sudo systemctl status nijenhuis-backend --no-pager" || true
$SSH_CMD "sudo systemctl status php-fpm --no-pager" || true

echo ""
log_info "Verifying remote booking.php content..."
$SSH_CMD "grep -A 5 'bookingEndDate' $REMOTE_DIR/pages/booking.php || echo 'bookingEndDate NOT FOUND'"

echo ""
log_info "Testing endpoints..."
curl -s --max-time 10 "http://$AWS_IP/" | head -20 || log_warn "Frontend not responding yet"

echo ""
echo "=============================================="
log_info "🎉 Deployment complete!"
echo ""
echo "Access your website at: http://$AWS_IP"
echo ""
echo "Useful commands:"
echo "  SSH: ssh -i $PEM_KEY $AWS_USER@$AWS_IP"
echo "  Logs: sudo journalctl -u nijenhuis-backend -f"
echo "  Status: sudo systemctl status nijenhuis-backend"
echo "  PHP-FPM: sudo systemctl status php-fpm"
echo "=============================================="
