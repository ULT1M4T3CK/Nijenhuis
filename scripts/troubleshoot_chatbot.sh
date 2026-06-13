#!/bin/bash
# ========================================================================
# Chatbot Troubleshooting Script
# Diagnoses and fixes common chatbot issues
# ========================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

cd "$PROJECT_ROOT"

usage() {
    echo "Usage: $0 {diagnose|fix|help}"
    echo ""
    echo "Commands:"
    echo "  diagnose  - Run diagnostics without making changes"
    echo "  fix       - Attempt to fix common issues automatically"
    echo "  help      - Show this help message"
    exit 1
}

# ========================================================================
# DIAGNOSE COMMAND
# ========================================================================
do_diagnose() {
    echo "============================================================"
    echo -e "${BLUE}Chatbot Diagnostics${NC}"
    echo "============================================================"
    echo ""
    
    ISSUES_FOUND=0
    
    # Check 1: Virtual Environment
    echo "1. Checking Virtual Environment..."
    if [ -d "venv" ]; then
        echo -e "   ${GREEN}✅ Virtual environment exists${NC}"
    else
        echo -e "   ${RED}❌ Virtual environment not found${NC}"
        ISSUES_FOUND=$((ISSUES_FOUND + 1))
    fi
    
    # Check 2: API Server Status
    echo ""
    echo "2. Checking API Server Status..."
    if curl -s http://localhost:5001/api/health > /dev/null 2>&1; then
        echo -e "   ${GREEN}✅ API server is running${NC}"
        curl -s http://localhost:5001/api/health | python3 -m json.tool 2>/dev/null | head -8 | sed 's/^/   /'
    else
        echo -e "   ${RED}❌ API server is NOT running${NC}"
        ISSUES_FOUND=$((ISSUES_FOUND + 1))
    fi
    
    # Check 3: API Key Configuration
    echo ""
    echo "3. Checking API Key Configuration..."
    if [ -f "config/api_keys.json" ]; then
        echo -e "   ${GREEN}✅ API keys file exists${NC}"
        KEY_COUNT=$(python3 -c "import json; f=open('config/api_keys.json'); print(len(json.load(f)))" 2>/dev/null || echo "0")
        echo "   Keys configured: $KEY_COUNT"
    else
        echo -e "   ${RED}❌ API keys file missing${NC}"
        ISSUES_FOUND=$((ISSUES_FOUND + 1))
    fi
    
    if [ -f "frontend/public/js/chatbot-config.js" ]; then
        echo -e "   ${GREEN}✅ Frontend config file exists${NC}"
    else
        echo -e "   ${YELLOW}⚠️  Frontend config file missing${NC}"
    fi
    
    # Check 4: Test API Connection
    echo ""
    echo "4. Testing API Connection..."
    API_KEY=$(grep CHATBOT_API_KEY frontend/public/js/chatbot-config.js 2>/dev/null | cut -d"'" -f2 | head -1 || true)
    if [ -n "$API_KEY" ]; then
        RESPONSE=$(curl -s -X POST http://localhost:5001/api/chat \
            -H "Content-Type: application/json" \
            -H "X-API-Key: $API_KEY" \
            -d '{"message":"test"}' 2>&1 || echo "CONNECTION_REFUSED")
        
        if echo "$RESPONSE" | grep -q "response"; then
            echo -e "   ${GREEN}✅ API connection works${NC}"
        elif echo "$RESPONSE" | grep -q "Invalid API key"; then
            echo -e "   ${RED}❌ Invalid API key${NC}"
            ISSUES_FOUND=$((ISSUES_FOUND + 1))
        elif echo "$RESPONSE" | grep -q "CONNECTION_REFUSED"; then
            echo -e "   ${RED}❌ Cannot connect - server not running${NC}"
            ISSUES_FOUND=$((ISSUES_FOUND + 1))
        else
            echo -e "   ${YELLOW}⚠️  Unexpected response${NC}"
        fi
    else
        echo -e "   ${YELLOW}⚠️  Cannot test - no API key found${NC}"
    fi
    
    # Check 5: Port Usage
    echo ""
    echo "5. Checking Port 5001..."
    if lsof -i :5001 > /dev/null 2>&1; then
        echo -e "   ${GREEN}✅ Port 5001 is in use${NC}"
        lsof -i :5001 2>/dev/null | head -2 | sed 's/^/   /' || true
    else
        echo -e "   ${YELLOW}⚠️  Port 5001 is not in use${NC}"
    fi
    
    # Check 6: Log File
    echo ""
    echo "6. Checking Logs..."
    if [ -f "logs/chatbot_server.log" ]; then
        echo -e "   ${GREEN}✅ Log file exists${NC}"
        ERROR_COUNT=$(grep -c "ERROR\|Exception\|Traceback" logs/chatbot_server.log 2>/dev/null || echo "0")
        echo "   Recent errors: $ERROR_COUNT"
        if [ "$ERROR_COUNT" -gt 0 ]; then
            echo ""
            echo "   Last error:"
            grep -A2 "ERROR\|Exception" logs/chatbot_server.log 2>/dev/null | tail -5 | sed 's/^/   /'
        fi
    else
        echo -e "   ${YELLOW}⚠️  No log file found${NC}"
    fi
    
    # Check 7: Dependencies
    echo ""
    echo "7. Checking Dependencies..."
    if [ -d "venv" ]; then
        source venv/bin/activate 2>/dev/null || true
        if python3 -c "import flask, openai" 2>/dev/null; then
            echo -e "   ${GREEN}✅ Core dependencies installed${NC}"
        else
            echo -e "   ${RED}❌ Missing dependencies${NC}"
            ISSUES_FOUND=$((ISSUES_FOUND + 1))
        fi
    fi
    
    # Summary
    echo ""
    echo "============================================================"
    echo -e "${BLUE}Diagnostics Summary${NC}"
    echo "============================================================"
    if [ $ISSUES_FOUND -eq 0 ]; then
        echo -e "${GREEN}✅ No issues found!${NC}"
    else
        echo -e "${RED}❌ Found $ISSUES_FOUND issue(s)${NC}"
        echo ""
        echo "Run '$0 fix' to attempt automatic fixes"
    fi
    echo ""
}

# ========================================================================
# FIX COMMAND
# ========================================================================
do_fix() {
    echo "============================================================"
    echo -e "${BLUE}Fixing Chatbot Issues${NC}"
    echo "============================================================"
    echo ""
    
    # Step 1: Check virtual environment
    if [ ! -d "venv" ]; then
        echo -e "${RED}❌ Virtual environment not found!${NC}"
        echo "   Run: bash setup_venv.sh first"
        exit 1
    fi
    
    source venv/bin/activate
    
    # Step 2: Stop existing servers
    echo "1. Stopping existing servers..."
    pkill -f "python.*server.py" 2>/dev/null || echo "   No servers running"
    sleep 2
    
    # Step 3: Ensure API key exists
    echo ""
    echo "2. Ensuring API key exists..."
    if [ -f "backend/chatbot/scripts/get_api_key.py" ]; then
        python3 backend/chatbot/scripts/get_api_key.py 2>/dev/null || true
    fi
    
    # Step 4: Get API key and create config
    echo ""
    echo "3. Updating configuration..."
    API_KEY=$(python3 -c "
import sys
sys.path.insert(0, '.')
try:
    from backend.chatbot.core.security_manager import get_security_manager
    sm = get_security_manager()
    if sm.api_keys:
        print(list(sm.api_keys.keys())[0])
except:
    pass
" 2>/dev/null || true)
    
    if [ -n "$API_KEY" ]; then
        echo -e "   ${GREEN}✅ API Key: ${API_KEY:0:20}...${NC}"
        
        # Create config file
        CONFIG_FILE="frontend/public/js/chatbot-config.js"
        mkdir -p "$(dirname "$CONFIG_FILE")"
        cat > "$CONFIG_FILE" << EOF
// Chatbot API Configuration
// Auto-generated by troubleshoot_chatbot.sh

window.CHATBOT_API_KEY = '$API_KEY';
window.CHATBOT_API_ENDPOINT = 'http://localhost:5001/api/chat';
EOF
        echo -e "   ${GREEN}✅ Config file updated${NC}"
    else
        echo -e "   ${YELLOW}⚠️  Could not retrieve API key${NC}"
    fi
    
    # Step 5: Start server
    echo ""
    echo "4. Starting chatbot server..."
    mkdir -p logs
    nohup python3 backend/chatbot/api/server.py > logs/chatbot_server.log 2>&1 &
    SERVER_PID=$!
    
    # Wait for server to start
    echo "   Waiting for server..."
    sleep 5
    
    # Step 6: Test connection
    echo ""
    echo "5. Testing connection..."
    if curl -s http://localhost:5001/api/health > /dev/null 2>&1; then
        echo -e "   ${GREEN}✅ Server is running${NC}"
    else
        echo -e "   ${YELLOW}⚠️  Server may not be ready. Check logs${NC}"
    fi
    
    # Test API key if available
    if [ -n "$API_KEY" ]; then
        TEST_RESPONSE=$(curl -s -X POST http://localhost:5001/api/chat \
            -H "Content-Type: application/json" \
            -H "X-API-Key: $API_KEY" \
            -d '{"message":"test"}' 2>&1 || echo "")
        
        if echo "$TEST_RESPONSE" | grep -q "response"; then
            echo -e "   ${GREEN}✅ API key works${NC}"
        elif echo "$TEST_RESPONSE" | grep -q "Invalid API key"; then
            echo -e "   ${YELLOW}⚠️  API key rejected, restarting server...${NC}"
            pkill -f "python.*server.py" 2>/dev/null || true
            sleep 2
            nohup python3 backend/chatbot/api/server.py > logs/chatbot_server.log 2>&1 &
            SERVER_PID=$!
            sleep 3
        fi
    fi
    
    echo ""
    echo "============================================================"
    echo -e "${GREEN}✅ Fix Complete!${NC}"
    echo "============================================================"
    echo ""
    echo "Server PID: $SERVER_PID"
    echo "Log file: logs/chatbot_server.log"
    echo ""
    echo "Next steps:"
    echo "  1. Open website and test chatbot"
    echo "  2. Check browser console for errors (F12)"
    echo "  3. Run '$0 diagnose' to verify"
    echo ""
}

# ========================================================================
# MAIN
# ========================================================================
case "${1}" in
    diagnose|diag|check)
        do_diagnose
        ;;
    fix|repair)
        do_fix
        ;;
    help|--help|-h)
        usage
        ;;
    *)
        if [ -z "${1}" ]; then
            do_diagnose
        else
            echo -e "${RED}❌ Unknown command: ${1}${NC}"
            echo ""
            usage
        fi
        ;;
esac

