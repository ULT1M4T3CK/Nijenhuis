#!/usr/bin/env python3
"""
Generate a new chatbot API key using the security manager
Usage: python scripts/generate-api-key.py [name] [permissions]
"""
import sys
import os
from pathlib import Path

# Add project root to path
script_dir = Path(__file__).parent
project_root = script_dir.parent
sys.path.insert(0, str(project_root))

# Load environment variables
try:
    import scripts.load_env
except ImportError:
    # Try to load .env manually
    env_file = project_root / '.env'
    if env_file.exists():
        with open(env_file, 'r') as f:
            for line in f:
                line = line.strip()
                if line and not line.startswith('#') and '=' in line:
                    key, value = line.split('=', 1)
                    os.environ.setdefault(key.strip(), value.strip().strip('"\''))

try:
    from backend.chatbot.core.security_manager import get_security_manager
    
    # Get arguments
    name = sys.argv[1] if len(sys.argv) > 1 else 'default_key'
    permissions_str = sys.argv[2] if len(sys.argv) > 2 else 'chat,health'
    permissions = [p.strip() for p in permissions_str.split(',')]
    
    # Generate API key
    security_manager = get_security_manager()
    api_key = security_manager.create_api_key(name, permissions)
    
    print("✅ API key generated successfully!")
    print(f"\nName: {name}")
    print(f"Permissions: {', '.join(permissions)}")
    print(f"\nAPI Key:")
    print(f"{api_key}")
    print(f"\n⚠️  IMPORTANT: Save this key securely. It won't be shown again.")
    print(f"\nTo use in frontend, add to .env:")
    print(f"VITE_CHATBOT_API_KEY={api_key}")
    
except Exception as e:
    print(f"❌ Error generating API key: {e}")
    import traceback
    traceback.print_exc()
    sys.exit(1)

