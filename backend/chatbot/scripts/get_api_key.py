#!/usr/bin/env python3
"""
Get or Create API Key for Chatbot
Retrieves existing API key or creates a new one for frontend use
"""

import sys
import os
from pathlib import Path

# Add project root to path
project_root = Path(__file__).parent.parent.parent.parent
sys.path.insert(0, str(project_root))

def get_or_create_api_key():
    """Get existing API key or create a new one"""
    try:
        from backend.chatbot.core.security_manager import get_security_manager
        
        security_manager = get_security_manager()
        
        # Check if API keys exist
        if security_manager.api_keys:
            # Get the first available key
            api_key = list(security_manager.api_keys.keys())[0]
            key_info = security_manager.api_keys[api_key]
            
            print("=" * 60)
            print("Existing API Key Found")
            print("=" * 60)
            print(f"API Key: {api_key}")
            print(f"Name: {key_info.get('name', 'Unknown')}")
            print(f"Permissions: {', '.join(key_info.get('permissions', []))}")
            print(f"Created: {key_info.get('created_at', 'Unknown')}")
            print(f"Usage Count: {key_info.get('usage_count', 0)}")
            print("\n" + "=" * 60)
            return api_key
        else:
            # Create a new API key
            print("No API keys found. Creating new API key...")
            api_key = security_manager.create_api_key(
                name='frontend_chatbot_key',
                permissions=['chat', 'health'],
                rate_limit_override=None
            )
            
            print("=" * 60)
            print("New API Key Created")
            print("=" * 60)
            print(f"API Key: {api_key}")
            print("\n" + "=" * 60)
            return api_key
            
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return None

def generate_config_script(api_key: str, api_endpoint: str = 'http://localhost:5001/api/chat'):
    """Generate configuration script for HTML pages"""
    config_js = f"""
// Chatbot API Configuration
// This should be added to your HTML pages before the chatbot script

window.CHATBOT_API_KEY = '{api_key}';
window.CHATBOT_API_ENDPOINT = '{api_endpoint}';

// For development only - in production, use environment variables or secure config
"""
    
    config_file = project_root / 'frontend' / 'public' / 'js' / 'chatbot-config.js'
    config_file.parent.mkdir(parents=True, exist_ok=True)
    
    with open(config_file, 'w') as f:
        f.write(config_js.strip())
    
    print(f"\n✅ Configuration file created: {config_file}")
    return config_file

def main():
    """Main function"""
    print("\n🔑 Chatbot API Key Configuration\n")
    
    # Get or create API key
    api_key = get_or_create_api_key()
    
    if not api_key:
        print("\n❌ Failed to get or create API key")
        return 1
    
    # Determine API endpoint
    api_endpoint = os.environ.get('CHATBOT_API_ENDPOINT', 'http://localhost:5001/api/chat')
    
    # Generate config file
    config_file = generate_config_script(api_key, api_endpoint)
    
    print("\n📝 Next Steps:")
    print("=" * 60)
    print("\n1. Add this script tag to your HTML pages (before chatbot script):")
    print(f'   <script src="../frontend/public/js/chatbot-config.js"></script>')
    print("\n2. Or add directly to HTML <head> section:")
    print(f'   <script>')
    print(f'   window.CHATBOT_API_KEY = "{api_key}";')
    print(f'   window.CHATBOT_API_ENDPOINT = "{api_endpoint}";')
    print(f'   </script>')
    print("\n3. For production, use environment variables:")
    print(f'   Set CHATBOT_API_KEY environment variable')
    print(f'   Set CHATBOT_API_ENDPOINT environment variable')
    print("\n" + "=" * 60)
    
    return 0

if __name__ == "__main__":
    sys.exit(main())

