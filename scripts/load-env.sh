#!/bin/bash
# =======================================================================
# Load Environment Variables Script
# Sources .env file and exports all variables for current shell session
# =======================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
ENV_FILE="$PROJECT_ROOT/.env"

if [ ! -f "$ENV_FILE" ]; then
    echo "❌ Error: .env file not found at $ENV_FILE"
    echo "Run scripts/setup-env.sh first to create it."
    exit 1
fi

# Export all variables from .env file
# This handles comments and empty lines properly
set -a
source "$ENV_FILE"
set +a

echo "✅ Environment variables loaded from .env"
echo ""
echo "Available variables:"
echo "  - MOLLIE_API_KEY: ${MOLLIE_API_KEY:+***set***}"
echo "  - ADMIN_USERNAME: ${ADMIN_USERNAME:+***set***}"
echo "  - JWT_SECRET: ${JWT_SECRET:+***set***}"
echo "  - FLASK_SECRET_KEY: ${FLASK_SECRET_KEY:+***set***}"
echo ""

