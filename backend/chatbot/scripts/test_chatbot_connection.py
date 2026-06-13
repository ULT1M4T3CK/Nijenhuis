#!/usr/bin/env python3
"""
Test Chatbot Connection
Tests the chatbot API connection from frontend perspective
"""

import sys
import requests
from pathlib import Path

# Add project root to path
project_root = Path(__file__).parent.parent.parent.parent
sys.path.insert(0, str(project_root))

def get_api_key():
    """Get API key from config file or security manager"""
    # Try to read from config file
    config_file = project_root / 'frontend' / 'public' / 'js' / 'chatbot-config.js'
    if config_file.exists():
        with open(config_file, 'r') as f:
            content = f.read()
            # Extract API key
            for line in content.split('\n'):
                if 'CHATBOT_API_KEY' in line and '=' in line:
                    api_key = line.split("'")[1] if "'" in line else line.split('"')[1]
                    return api_key
    
    # Fallback to security manager
    try:
        from backend.chatbot.core.security_manager import get_security_manager
        sm = get_security_manager()
        if sm.api_keys:
            return list(sm.api_keys.keys())[0]
    except:
        pass
    
    return None

def test_connection():
    """Test chatbot API connection"""
    print("=" * 60)
    print("Testing Chatbot Connection")
    print("=" * 60)
    
    api_key = get_api_key()
    if not api_key:
        print("❌ API key not found. Run get_api_key.py first.")
        return 1
    
    api_endpoint = 'http://localhost:5001/api/chat'
    
    print(f"\n🔑 API Key: {api_key[:20]}...")
    print(f"📍 Endpoint: {api_endpoint}")
    
    # Test health endpoint (no auth required)
    print("\n1. Testing health endpoint...")
    try:
        response = requests.get('http://localhost:5001/api/health', timeout=5)
        if response.status_code == 200:
            print("   ✅ Health check passed")
            data = response.json()
            print(f"   Status: {data.get('status', 'unknown')}")
        else:
            print(f"   ❌ Health check failed: {response.status_code}")
            return 1
    except requests.exceptions.ConnectionError:
        print("   ❌ Cannot connect to API server")
        print("   Make sure the chatbot API server is running:")
        print("   python3 backend/chatbot/api/server.py")
        return 1
    except Exception as e:
        print(f"   ❌ Error: {e}")
        return 1
    
    # Test chat endpoint (with auth)
    print("\n2. Testing chat endpoint...")
    test_messages = [
        "Hallo",
        "Wat kost de Tender 720?",
        "What are your opening hours?"
    ]
    
    for message in test_messages:
        try:
            response = requests.post(
                api_endpoint,
                json={'message': message},
                headers={
                    'Content-Type': 'application/json',
                    'X-API-Key': api_key
                },
                timeout=10
            )
            
            if response.status_code == 200:
                data = response.json()
                print(f"   ✅ '{message}' → {data.get('response', '')[:50]}...")
            elif response.status_code == 401:
                print(f"   ❌ Authentication failed for '{message}'")
                print(f"   Response: {response.text}")
                return 1
            else:
                print(f"   ❌ Error {response.status_code} for '{message}'")
                print(f"   Response: {response.text}")
                return 1
        except Exception as e:
            print(f"   ❌ Error testing '{message}': {e}")
            return 1
    
    print("\n" + "=" * 60)
    print("✅ All connection tests passed!")
    print("=" * 60)
    print("\nThe chatbot should now work on your website.")
    print("Make sure chatbot-config.js is loaded in your HTML pages.")
    
    return 0

if __name__ == "__main__":
    sys.exit(test_connection())

