#!/bin/bash
# =======================================================================
# Environment Setup Script
# Generates secure secrets and creates .env file from env.example
# =======================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
ENV_EXAMPLE="$PROJECT_ROOT/env.example"
ENV_FILE="$PROJECT_ROOT/.env"

echo "🔒 Setting up environment variables..."
echo ""

# Check if .env already exists
if [ -f "$ENV_FILE" ]; then
    echo "⚠️  .env file already exists!"
    read -p "Do you want to overwrite it? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "Aborted."
        exit 1
    fi
    echo ""
fi

# Copy env.example to .env
if [ ! -f "$ENV_EXAMPLE" ]; then
    echo "❌ Error: env.example not found at $ENV_EXAMPLE"
    exit 1
fi

cp "$ENV_EXAMPLE" "$ENV_FILE"
echo "✅ Created .env file from env.example"
echo ""

# Generate secure secrets
echo "🔐 Generating secure secrets..."

# Generate JWT Secret (64+ characters)
JWT_SECRET=$(openssl rand -base64 64 | tr -d '\n')
sed -i.bak "s|JWT_SECRET=.*|JWT_SECRET=$JWT_SECRET|" "$ENV_FILE"

# Generate Flask Secret Key (32+ characters)
FLASK_SECRET=$(openssl rand -base64 32 | tr -d '\n')
sed -i.bak "s|FLASK_SECRET_KEY=.*|FLASK_SECRET_KEY=$FLASK_SECRET|" "$ENV_FILE"

# Clean up backup file
rm -f "$ENV_FILE.bak"

echo "✅ Generated JWT_SECRET"
echo "✅ Generated FLASK_SECRET_KEY"
echo ""

# Set secure permissions
chmod 600 "$ENV_FILE"
echo "✅ Set secure permissions (600) on .env file"
echo ""

# Prompt for required values
echo "📝 Please fill in the following required values:"
echo ""

# Mollie API Key
read -p "Mollie API Key (test_... or live_...): " MOLLIE_KEY
if [ ! -z "$MOLLIE_KEY" ]; then
    sed -i.bak "s|MOLLIE_API_KEY=.*|MOLLIE_API_KEY=$MOLLIE_KEY|" "$ENV_FILE"
fi

# Mollie Webhook Secret
read -p "Mollie Webhook Secret (press Enter to skip): " MOLLIE_WEBHOOK
if [ ! -z "$MOLLIE_WEBHOOK" ]; then
    sed -i.bak "s|MOLLIE_WEBHOOK_SECRET=.*|MOLLIE_WEBHOOK_SECRET=$MOLLIE_WEBHOOK|" "$ENV_FILE"
fi

# Admin Username
read -p "Admin Username: " ADMIN_USER
if [ ! -z "$ADMIN_USER" ]; then
    sed -i.bak "s|ADMIN_USERNAME=.*|ADMIN_USERNAME=$ADMIN_USER|" "$ENV_FILE"
fi

# Admin Password
read -sp "Admin Password: " ADMIN_PASS
echo ""
if [ ! -z "$ADMIN_PASS" ]; then
    sed -i.bak "s|ADMIN_PASSWORD=.*|ADMIN_PASSWORD=$ADMIN_PASS|" "$ENV_FILE"
fi

# Clean up backup file
rm -f "$ENV_FILE.bak"

echo ""
echo "✅ Environment setup complete!"
echo ""
echo "📋 Next steps:"
echo "1. Review and edit .env file if needed:"
echo "   nano $ENV_FILE"
echo ""
echo "2. For Python backend, load environment variables:"
echo "   source scripts/load-env.sh"
echo "   # or use: python scripts/load_env.py"
echo ""
echo "3. For frontend (Vite), environment variables are automatically loaded from .env"
echo ""
echo "4. Start your services:"
echo "   # Backend: python -m backend.api.app"
echo "   # Frontend: cd frontend && npm run dev"
echo ""
echo "⚠️  IMPORTANT: Never commit .env to version control!"

