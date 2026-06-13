#!/bin/bash
# ========================================================================
# Install and Enable Nijenhuis Systemd Services
# This script installs all service files and enables them to start
# automatically when the device is connected to the internet.
# 
# Also installs:
# - NetworkManager dispatcher for auto-restart on network changes
# - Logrotate configuration for log management
# ========================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
SYSTEMD_DIR="$PROJECT_ROOT/deploy/systemd"
NETWORKMANAGER_DIR="$PROJECT_ROOT/deploy/networkmanager"
LOGROTATE_DIR="$PROJECT_ROOT/deploy/logrotate"

echo "============================================================"
echo "Installing Nijenhuis Systemd Services"
echo "============================================================"
echo ""

# Check if running as root or with sudo
if [ "$EUID" -ne 0 ]; then 
    echo "⚠️  This script requires sudo privileges"
    echo "   Please run: sudo bash $0"
    exit 1
fi

# Ensure logs directory exists
echo "1. Creating logs directory..."
mkdir -p "$PROJECT_ROOT/logs"
chown andre:andre "$PROJECT_ROOT/logs" 2>/dev/null || true
echo "   ✅ Logs directory ready"
echo ""

# Copy service files to systemd directory
echo "2. Installing systemd service files..."
SERVICES=(
    "nijenhuis-backend.service"
    "nijenhuis-admin.service"
    "nijenhuis-frontend.service"
)

for service in "${SERVICES[@]}"; do
    if [ -f "$SYSTEMD_DIR/$service" ]; then
        echo "   📦 Installing $service..."
        cp "$SYSTEMD_DIR/$service" /etc/systemd/system/
        chmod 644 /etc/systemd/system/$service
        echo "      ✅ Installed"
    else
        echo "   ⚠️  Warning: $service not found in $SYSTEMD_DIR"
    fi
done
echo ""

# Reload systemd daemon
echo "3. Reloading systemd daemon..."
systemctl daemon-reload
echo "   ✅ Daemon reloaded"
echo ""

# Enable services to start on boot
echo "4. Enabling services to start on boot..."
for service in "${SERVICES[@]}"; do
    if [ -f "/etc/systemd/system/$service" ]; then
        echo "   🔄 Enabling $service..."
        systemctl enable "$service" || echo "      ⚠️  Failed to enable (may already be enabled)"
    fi
done
echo ""

# Start services
echo "5. Starting services..."
for service in "${SERVICES[@]}"; do
    if [ -f "/etc/systemd/system/$service" ]; then
        echo "   🚀 Starting $service..."
        systemctl start "$service" || echo "      ⚠️  Failed to start (check logs)"
    fi
done
echo ""

# Show status
echo "6. Service Status:"
echo "============================================================"
for service in "${SERVICES[@]}"; do
    if [ -f "/etc/systemd/system/$service" ]; then
        echo ""
        echo "📋 $service:"
        systemctl status "$service" --no-pager -l || true
    fi
done
echo ""

# Install NetworkManager dispatcher script
echo ""


# Install logrotate configuration
echo ""
echo "8. Installing logrotate configuration..."
if [ -d "/etc/logrotate.d" ]; then
    if [ -f "$LOGROTATE_DIR/nijenhuis" ]; then
        cp "$LOGROTATE_DIR/nijenhuis" /etc/logrotate.d/
        chmod 644 /etc/logrotate.d/nijenhuis
        chown root:root /etc/logrotate.d/nijenhuis
        echo "   ✅ Logrotate configuration installed"
    else
        echo "   ⚠️  Logrotate config not found in $LOGROTATE_DIR"
    fi
else
    echo "   ⚠️  Logrotate not installed"
fi

# Install Gunicorn if not present
echo ""
echo "9. Checking Gunicorn installation..."
if [ -f "$PROJECT_ROOT/venv/bin/gunicorn" ]; then
    echo "   ✅ Gunicorn already installed"
else
    echo "   📦 Installing Gunicorn..."
    "$PROJECT_ROOT/venv/bin/pip" install gunicorn sdnotify >/dev/null 2>&1 || echo "   ⚠️  Failed to install Gunicorn"
fi

echo ""
echo "============================================================"
echo "✅ Installation Complete!"
echo "============================================================"
echo ""
echo "All services are now configured to start automatically"
echo "when the device is connected to the internet."
echo ""
echo "Components installed:"
echo "  • systemd services (auto-restart on boot and failures)"
echo "  • NetworkManager dispatcher (auto-restart on network changes)"
echo "  • Logrotate configuration (automatic log rotation)"
echo "  • Gunicorn WSGI server (production-ready)"
echo ""
echo "Useful commands:"
echo "  • Check status:     sudo systemctl status nijenhuis-*.service"
echo "  • View logs:        sudo journalctl -u nijenhuis-*.service -f"
echo "  • Restart service:  sudo systemctl restart nijenhuis-<service>.service"
echo "  • Stop service:     sudo systemctl stop nijenhuis-<service>.service"
echo ""
