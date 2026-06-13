#!/bin/bash
# ========================================================================
# START LOCAL DEVELOPMENT SERVER
# Provides both PHP and Python booking handlers for local development
# ========================================================================

echo "🚀 Starting Nijenhuis Local Development Server"
echo "=============================================="
echo ""

# Check if Python server is already running
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null ; then
    echo "⚠️  Port 8000 is already in use"
    echo "   The Python booking server may already be running"
    echo ""
else
    echo "🐍 Starting Python booking server on port 8000..."
    cd "$(dirname "$0")/.."
    python3 admin/booking-handler.py 8000 > /dev/null 2>&1 &
    PYTHON_PID=$!
    echo "   ✓ Python server started (PID: $PYTHON_PID)"
    echo ""
fi

# Check if PHP is available
if command -v php >/dev/null 2>&1; then
    if lsof -Pi :8888 -sTCP:LISTEN -t >/dev/null ; then
        echo "⚠️  Port 8888 is already in use"
        echo "   PHP server may already be running"
    else
        echo "🐘 Starting PHP built-in server on port 8888 with router..."
        cd "$(dirname "$0")/.."
        php -S localhost:8888 router.php > /dev/null 2>&1 &
        PHP_PID=$!
        echo "   ✓ PHP server started (PID: $PHP_PID)"
        echo ""
        echo "📝 Access website via: http://localhost:8888/ (or http://localhost:8888/pages/index.php)"
        echo "📝 Access admin via: http://localhost:8888/admin-login (or http://localhost:8888/pages/admin-login.php)"
    fi
else
    echo "⚠️  PHP not found - skipping PHP server"
    echo "   Install PHP to use PHP booking handler locally"
    echo ""
fi

echo "✅ Development servers started!"
echo ""
echo "📋 Available endpoints:"
echo "   - Python booking API: http://localhost:8000/admin/booking-handler.py"
if command -v php >/dev/null 2>&1; then
    echo "   - PHP booking API:   http://localhost:8888/admin/booking-handler.php"
    echo "   - Website:           http://localhost:8888/ (clean URLs supported)"
    echo "   - Admin login:       http://localhost:8888/admin-login"
    echo "   - Navigation links:  /botenverhuur, /te-koop, /camping, etc."
fi
echo ""
echo "🛑 To stop servers, press Ctrl+C or run:"
echo "   pkill -f 'booking-handler.py'"
if command -v php >/dev/null 2>&1; then
    echo "   pkill -f 'php -S localhost:8888'"
fi
echo ""

# Wait for user interrupt
trap "echo ''; echo '🛑 Stopping servers...'; kill $PYTHON_PID 2>/dev/null; kill $PHP_PID 2>/dev/null; exit" INT TERM
wait






