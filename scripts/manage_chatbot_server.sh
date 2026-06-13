#!/bin/bash
# ========================================================================
# Chatbot Server Management Script
# Unified script for starting, stopping, restarting, and checking status
# ========================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

usage() {
    echo "Usage: $0 {start|stop|restart|status|logs|help}"
    echo ""
    echo "Commands:"
    echo "  start    - Start the chatbot server"
    echo "  stop     - Stop the chatbot server"
    echo "  restart  - Restart the chatbot server"
    echo "  status   - Show server status and health"
    echo "  logs     - Show recent log entries (tail -f)"
    echo "  help     - Show this help message"
    echo ""
    echo "Options:"
    echo "  start -f, --foreground  Run server in foreground mode"
    exit 1
}

# ========================================================================
# START COMMAND
# ========================================================================
do_start() {
    cd "$PROJECT_ROOT"
    
    echo "============================================================"
    echo -e "${BLUE}Starting Nijenhuis Chatbot Server${NC}"
    echo "============================================================"
    
    # Check for virtual environment
    if [ ! -d "venv" ]; then
        echo -e "${RED}❌ Virtual environment not found!${NC}"
        echo "   Run: bash scripts/setup_venv.sh first"
        exit 1
    fi
    
    # Activate virtual environment
    echo "1. Activating virtual environment..."
    source venv/bin/activate
    
    # Load environment variables if .env exists
    if [ -f ".env" ]; then
        echo "2. Loading environment variables..."
        set -a
        source .env
        set +a
        echo -e "   ${GREEN}✅ Environment variables loaded${NC}"
    else
        echo -e "2. ${YELLOW}⚠️  No .env file found (using defaults)${NC}"
    fi
    
    # Ensure API key exists
    echo "3. Verifying API key..."
    if [ ! -f "config/api_keys.json" ]; then
        echo -e "   ${YELLOW}⚠️  API keys file not found. Creating...${NC}"
        python3 backend/chatbot/scripts/get_api_key.py 2>/dev/null || true
    fi
    
    # Check for Docker container
    DOCKER_CONTAINER=$(docker ps --format "{{.Names}}" --filter "name=nijenhuis-chatbot" 2>/dev/null || true)
    
    if [ -n "$DOCKER_CONTAINER" ]; then
        echo ""
        echo -e "${YELLOW}⚠️  Docker container '$DOCKER_CONTAINER' is already running on port 5001!${NC}"
        echo "   Use 'bash scripts/manage_docker_chatbot.sh' for Docker management"
        exit 1
    fi
    
    # Check if direct Python server is already running
    if pgrep -f "python.*server.py" > /dev/null; then
        echo ""
        echo -e "${YELLOW}⚠️  Chatbot server is already running!${NC}"
        echo "   PID: $(pgrep -f 'python.*server.py' | head -1)"
        echo "   Use '$0 restart' to restart"
        exit 1
    fi
    
    # Check if port is in use
    if lsof -i :5001 > /dev/null 2>&1; then
        echo ""
        echo -e "${RED}❌ Port 5001 is already in use!${NC}"
        echo "   Free the port first or check what's using it"
        exit 1
    fi
    
    # Create logs directory
    mkdir -p logs
    
    # Start server
    echo "4. Starting chatbot server..."
    
    # Determine if running in foreground or background
    if [ "${1}" = "--foreground" ] || [ "${1}" = "-f" ]; then
        echo "   Running in foreground mode (Ctrl+C to stop)"
        echo "============================================================"
        python3 backend/chatbot/api/server.py
    else
        echo "   Running in background mode"
        nohup python3 backend/chatbot/api/server.py > logs/chatbot_server.log 2>&1 &
        SERVER_PID=$!
        
        # Wait for server to start
        sleep 3
        
        # Check if server is running
        if ps -p $SERVER_PID > /dev/null 2>&1; then
            echo -e "   ${GREEN}✅ Server started (PID: $SERVER_PID)${NC}"
        else
            echo -e "   ${RED}❌ Server failed to start. Check logs/chatbot_server.log${NC}"
            exit 1
        fi
        
        # Test connection
        echo "5. Testing connection..."
        sleep 2
        if curl -s http://localhost:5001/api/health > /dev/null 2>&1; then
            echo -e "   ${GREEN}✅ Server is responding${NC}"
        else
            echo -e "   ${YELLOW}⚠️  Server may not be ready yet${NC}"
        fi
        
        echo ""
        echo "============================================================"
        echo -e "${GREEN}✅ Chatbot server started successfully!${NC}"
        echo "============================================================"
        echo ""
        echo "Server PID: $SERVER_PID"
        echo "Log file: logs/chatbot_server.log"
        echo "Health check: http://localhost:5001/api/health"
        echo ""
        echo "To view logs: $0 logs"
        echo "To stop: $0 stop"
    fi
}

# ========================================================================
# STOP COMMAND
# ========================================================================
do_stop() {
    echo "============================================================"
    echo -e "${BLUE}Stopping Nijenhuis Chatbot Server${NC}"
    echo "============================================================"
    
    # Find all running chatbot servers
    PIDS=$(pgrep -f "python.*server.py" || true)
    
    if [ -z "$PIDS" ]; then
        echo -e "${GREEN}✅ No chatbot servers running${NC}"
        return 0
    fi
    
    echo "Found running servers:"
    for PID in $PIDS; do
        echo "  PID $PID"
    done
    
    echo ""
    echo "Stopping servers..."
    
    # Try graceful shutdown first
    for PID in $PIDS; do
        if kill -0 $PID 2>/dev/null; then
            echo "  Sending SIGTERM to PID $PID..."
            kill -TERM $PID 2>/dev/null || true
        fi
    done
    
    # Wait for graceful shutdown
    sleep 3
    
    # Force kill if still running
    REMAINING=$(pgrep -f "python.*server.py" || true)
    if [ -n "$REMAINING" ]; then
        echo "  Some servers still running, forcing shutdown..."
        for PID in $REMAINING; do
            if kill -0 $PID 2>/dev/null; then
                kill -9 $PID 2>/dev/null || true
            fi
        done
        sleep 1
    fi
    
    # Verify all stopped
    if pgrep -f "python.*server.py" > /dev/null; then
        echo -e "${RED}❌ Some servers could not be stopped (may require sudo)${NC}"
        exit 1
    else
        echo -e "${GREEN}✅ All chatbot servers stopped${NC}"
    fi
}

# ========================================================================
# STATUS COMMAND
# ========================================================================
do_status() {
    echo "============================================================"
    echo -e "${BLUE}Nijenhuis Chatbot Server Status${NC}"
    echo "============================================================"
    
    # Check for Docker container
    DOCKER_CONTAINER=$(docker ps --format "{{.Names}}" --filter "name=nijenhuis-chatbot" 2>/dev/null || true)
    
    if [ -n "$DOCKER_CONTAINER" ]; then
        echo -e "🐳 Docker Status: ${GREEN}RUNNING${NC}"
        echo "   Container: $DOCKER_CONTAINER"
        echo ""
    fi
    
    # Check if server is running (direct Python execution)
    PIDS=$(pgrep -f "python.*server.py" || true)
    
    if [ -z "$PIDS" ] && [ -z "$DOCKER_CONTAINER" ]; then
        echo -e "${RED}❌ Server Status: NOT RUNNING${NC}"
        echo ""
        echo "To start: $0 start"
        return 1
    fi
    
    if [ -n "$PIDS" ]; then
        echo -e "✅ Python Server: ${GREEN}RUNNING${NC}"
        echo ""
        echo "Running processes:"
        for PID in $PIDS; do
            MEM=$(ps -o rss= -p $PID 2>/dev/null | awk '{printf "%.1f MB", $1/1024}' || echo "unknown")
            echo "  PID: $PID (Memory: $MEM)"
        done
    fi
    
    # Check health endpoint
    echo ""
    echo "Health Check (http://localhost:5001/api/health):"
    HEALTH_RESPONSE=$(curl -s http://localhost:5001/api/health 2>&1 || echo "ERROR")
    
    if echo "$HEALTH_RESPONSE" | grep -q "healthy\|degraded"; then
        echo -e "  ${GREEN}✅ API responding${NC}"
        
        # Parse and display key info if jq is available
        if command -v jq > /dev/null 2>&1; then
            echo ""
            echo "$HEALTH_RESPONSE" | jq -r '
                "  Service: \(.service // "unknown")",
                "  Version: \(.version // "unknown")",
                "  Status: \(.status // "unknown")"
            ' 2>/dev/null || true
        fi
    else
        echo -e "  ${RED}❌ API not responding${NC}"
    fi
    
    # Show recent log entries
    echo ""
    echo "Recent Log Entries (last 5 lines):"
    if [ -f "$PROJECT_ROOT/logs/chatbot_server.log" ]; then
        tail -n 5 "$PROJECT_ROOT/logs/chatbot_server.log" | sed 's/^/  /'
    else
        echo "  No log file found"
    fi
    
    echo ""
    echo "============================================================"
}

# ========================================================================
# LOGS COMMAND
# ========================================================================
do_logs() {
    if [ -f "$PROJECT_ROOT/logs/chatbot_server.log" ]; then
        echo "Showing logs (Ctrl+C to exit)..."
        echo ""
        tail -f "$PROJECT_ROOT/logs/chatbot_server.log"
    else
        echo -e "${RED}❌ Log file not found: logs/chatbot_server.log${NC}"
        exit 1
    fi
}

# ========================================================================
# MAIN
# ========================================================================
case "${1}" in
    start)
        do_start "${2}"
        ;;
    stop)
        do_stop
        ;;
    restart)
        echo "Restarting chatbot server..."
        do_stop
        sleep 2
        do_start
        ;;
    status)
        do_status
        ;;
    logs)
        do_logs
        ;;
    help|--help|-h)
        usage
        ;;
    *)
        echo -e "${RED}❌ Unknown command: ${1}${NC}"
        echo ""
        usage
        ;;
esac
