#!/bin/bash
# Start PHP Development Server for Nijenhuis Botenverhuur

PORT=8888
DOCUMENT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"

echo "=========================================="
echo "Starting Nijenhuis Development Server"
echo "=========================================="
echo "Port: $PORT"
echo "Document Root: $DOCUMENT_ROOT"
echo ""

# Kill any existing server on this port
if lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo "Stopping existing server on port $PORT..."
    pkill -f "php -S localhost:$PORT" 2>/dev/null
    sleep 1
fi

# Start the server
echo "Starting PHP server..."
cd "$DOCUMENT_ROOT"
php -S localhost:$PORT -t . &
SERVER_PID=$!

sleep 2

# Check if server started successfully
if ps -p $SERVER_PID > /dev/null; then
    echo "✓ Server started successfully (PID: $SERVER_PID)"
    echo ""
    echo "Server running at: http://localhost:$PORT"
    echo "Press Ctrl+C to stop the server"
    echo ""
    echo "To stop the server: kill $SERVER_PID"
    echo "Or run: pkill -f 'php -S localhost:$PORT'"
    echo ""
    
    # Wait for server
    wait $SERVER_PID
else
    echo "✗ Failed to start server"
    exit 1
fi
