#!/bin/bash
# ============================================================================
# Nijenhuis Chatbot Quick Health Check Script
# Run this to quickly verify all components are working correctly
# ============================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo "============================================================"
echo -e "${BLUE}Nijenhuis Chatbot Health Check${NC}"
echo "============================================================"
echo ""

CHECKS_PASSED=0
CHECKS_FAILED=0

# Helper function for check results
check_result() {
    if [ $1 -eq 0 ]; then
        echo -e "   ${GREEN}✅ $2${NC}"
        CHECKS_PASSED=$((CHECKS_PASSED + 1))
    else
        echo -e "   ${RED}❌ $2${NC}"
        CHECKS_FAILED=$((CHECKS_FAILED + 1))
    fi
}

# 1. Internet Connectivity
echo "1. Internet Connectivity"
if ping -c 1 -W 3 8.8.8.8 >/dev/null 2>&1; then
    check_result 0 "Internet: Connected (Google DNS reachable)"
elif ping -c 1 -W 3 1.1.1.1 >/dev/null 2>&1; then
    check_result 0 "Internet: Connected (Cloudflare DNS reachable)"
else
    check_result 1 "Internet: Disconnected"
fi
echo ""

# 2. systemd Service Status
echo "2. systemd Service Status"
if systemctl is-active --quiet nijenhuis-chatbot.service 2>/dev/null; then
    UPTIME=$(systemctl show nijenhuis-chatbot.service --property=ActiveEnterTimestamp --value 2>/dev/null || echo "unknown")
    check_result 0 "Service: Running (since $UPTIME)"
elif systemctl is-enabled --quiet nijenhuis-chatbot.service 2>/dev/null; then
    check_result 1 "Service: Stopped but enabled"
else
    # Check if running as Python process directly
    if pgrep -f "python.*server.py" > /dev/null; then
        PID=$(pgrep -f "python.*server.py" | head -1)
        check_result 0 "Service: Running as Python process (PID: $PID)"
    else
        check_result 1 "Service: Not running"
    fi
fi
echo ""

# 3. Port 5001 Status
echo "3. Port 5001"
if command -v nc &> /dev/null; then
    if nc -z localhost 5001 2>/dev/null; then
        check_result 0 "Port: Listening on 5001"
    else
        check_result 1 "Port: Not listening on 5001"
    fi
elif command -v lsof &> /dev/null; then
    if lsof -i :5001 >/dev/null 2>&1; then
        check_result 0 "Port: Listening on 5001"
    else
        check_result 1 "Port: Not listening on 5001"
    fi
else
    echo -e "   ${YELLOW}⚠️ Cannot check port (nc/lsof not available)${NC}"
fi
echo ""

# 4. Health Endpoint
echo "4. Health Endpoint"
HEALTH_RESPONSE=$(curl -sf http://localhost:5001/api/health 2>/dev/null)
if [ $? -eq 0 ]; then
    STATUS=$(echo "$HEALTH_RESPONSE" | grep -o '"status":"[^"]*"' | cut -d'"' -f4 2>/dev/null || echo "unknown")
    VERSION=$(echo "$HEALTH_RESPONSE" | grep -o '"version":"[^"]*"' | cut -d'"' -f4 2>/dev/null || echo "unknown")
    check_result 0 "API: $STATUS (v$VERSION)"
else
    check_result 1 "API: Not responding"
fi
echo ""

# 5. Token Endpoint
echo "5. Token Endpoint"
TOKEN_RESPONSE=$(curl -sf http://localhost:5001/api/token 2>/dev/null)
if [ $? -eq 0 ]; then
    TOKEN=$(echo "$TOKEN_RESPONSE" | grep -o '"token":"[^"]*"' | head -c 50 2>/dev/null)
    if [ -n "$TOKEN" ]; then
        check_result 0 "Token: Endpoint working"
    else
        check_result 1 "Token: No token in response"
    fi
else
    check_result 1 "Token: Endpoint not responding"
fi
echo ""

# 6. Chat Endpoint Test
echo "6. Chat Endpoint Test"
# First get a token
TOKEN=$(curl -sf http://localhost:5001/api/token 2>/dev/null | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
if [ -n "$TOKEN" ]; then
    CHAT_RESPONSE=$(curl -sf -X POST http://localhost:5001/api/chat \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer $TOKEN" \
        -d '{"message":"test"}' 2>/dev/null)
    
    if [ $? -eq 0 ]; then
        if echo "$CHAT_RESPONSE" | grep -q '"success":true'; then
            RESPONSE_TYPE=$(echo "$CHAT_RESPONSE" | grep -o '"response_type":"[^"]*"' | cut -d'"' -f4)
            check_result 0 "Chat: Working (type: $RESPONSE_TYPE)"
        else
            check_result 1 "Chat: Response not successful"
        fi
    else
        check_result 1 "Chat: Request failed"
    fi
else
    check_result 1 "Chat: Could not get token for test"
fi
echo ""

# 7. Memory Usage
echo "7. Memory Usage"
PID=$(pgrep -f "server.py\|gunicorn.*chatbot" | head -1)
if [ -n "$PID" ]; then
    MEM_KB=$(ps -o rss= -p $PID 2>/dev/null | tr -d ' ')
    if [ -n "$MEM_KB" ]; then
        MEM_MB=$((MEM_KB / 1024))
        if [ $MEM_MB -lt 512 ]; then
            check_result 0 "Memory: ${MEM_MB}MB (healthy)"
        elif [ $MEM_MB -lt 768 ]; then
            echo -e "   ${YELLOW}⚠️ Memory: ${MEM_MB}MB (elevated)${NC}"
            CHECKS_PASSED=$((CHECKS_PASSED + 1))
        else
            check_result 1 "Memory: ${MEM_MB}MB (high - may need restart)"
        fi
    else
        echo -e "   ${YELLOW}⚠️ Could not read memory usage${NC}"
    fi
else
    echo -e "   ${YELLOW}⚠️ No process found to check${NC}"
fi
echo ""

# 8. Log Files
echo "8. Log Files"
if [ -f "$PROJECT_ROOT/logs/chatbot_server.log" ]; then
    LOG_SIZE=$(du -h "$PROJECT_ROOT/logs/chatbot_server.log" 2>/dev/null | cut -f1)
    LAST_MOD=$(stat -c %y "$PROJECT_ROOT/logs/chatbot_server.log" 2>/dev/null | cut -d'.' -f1)
    ERROR_COUNT=$(grep -c "ERROR\|Exception" "$PROJECT_ROOT/logs/chatbot_server.log" 2>/dev/null | tr -d '\n' || echo "0")
    ERROR_COUNT=${ERROR_COUNT:-0}
    
    if [ "$ERROR_COUNT" -lt 10 ] 2>/dev/null; then
        check_result 0 "Logs: OK (size: $LOG_SIZE, errors: $ERROR_COUNT)"
    else
        echo -e "   ${YELLOW}⚠️ Logs: $ERROR_COUNT errors found (size: $LOG_SIZE)${NC}"
        CHECKS_PASSED=$((CHECKS_PASSED + 1))
    fi
else
    echo -e "   ${YELLOW}⚠️ Log file not found${NC}"
fi
echo ""

# 9. Virtual Environment
echo "9. Virtual Environment"
if [ -d "$PROJECT_ROOT/venv" ]; then
    if [ -f "$PROJECT_ROOT/venv/bin/python3" ]; then
        PYTHON_VERSION=$("$PROJECT_ROOT/venv/bin/python3" --version 2>/dev/null || echo "unknown")
        check_result 0 "Venv: OK ($PYTHON_VERSION)"
    else
        check_result 1 "Venv: Directory exists but Python not found"
    fi
else
    check_result 1 "Venv: Not found"
fi
echo ""

# 10. Environment Configuration
echo "10. Environment Configuration"
if [ -f "$PROJECT_ROOT/.env" ]; then
    # Check for critical environment variables
    MISSING_VARS=""
    for var in FLASK_SECRET_KEY JWT_SECRET; do
        if ! grep -q "^${var}=" "$PROJECT_ROOT/.env" 2>/dev/null; then
            MISSING_VARS="$MISSING_VARS $var"
        fi
    done
    
    if [ -z "$MISSING_VARS" ]; then
        check_result 0 "Environment: .env configured"
    else
        echo -e "   ${YELLOW}⚠️ Environment: Missing$MISSING_VARS${NC}"
        CHECKS_PASSED=$((CHECKS_PASSED + 1))
    fi
else
    check_result 1 "Environment: .env file not found"
fi
echo ""

# Summary
echo "============================================================"
echo -e "${BLUE}Summary${NC}"
echo "============================================================"
TOTAL=$((CHECKS_PASSED + CHECKS_FAILED))

if [ $CHECKS_FAILED -eq 0 ]; then
    echo -e "${GREEN}✅ All $TOTAL checks passed!${NC}"
    echo ""
    echo "The chatbot service is healthy and ready to serve requests."
elif [ $CHECKS_FAILED -le 2 ]; then
    echo -e "${YELLOW}⚠️ $CHECKS_PASSED/$TOTAL checks passed ($CHECKS_FAILED issues)${NC}"
    echo ""
    echo "Some non-critical issues detected. Service may still be operational."
else
    echo -e "${RED}❌ $CHECKS_PASSED/$TOTAL checks passed ($CHECKS_FAILED failures)${NC}"
    echo ""
    echo "Critical issues detected. Please investigate:"
    echo "  • Run: bash $SCRIPT_DIR/troubleshoot_chatbot.sh diagnose"
    echo "  • View logs: tail -50 $PROJECT_ROOT/logs/chatbot_server.log"
    echo "  • Start service: bash $SCRIPT_DIR/manage_chatbot_server.sh start"
fi
echo ""

exit $CHECKS_FAILED

