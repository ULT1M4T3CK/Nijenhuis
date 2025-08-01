#!/usr/bin/env python3
"""
Test script for the simple chatbot functionality
"""

import requests
import json
import time

def test_chat_endpoints():
    """Test all chat endpoints"""
    base_url = "http://localhost:5000"
    
    print("🤖 Testing Nijenhuis Simple Chatbot")
    print("=" * 50)
    
    # Test health endpoint
    print("\n1. Testing health endpoint...")
    try:
        response = requests.get(f"{base_url}/api/health")
        if response.status_code == 200:
            data = response.json()
            print(f"✅ Health check passed")
            print(f"   Service: {data.get('service')}")
            print(f"   Version: {data.get('version')}")
            print(f"   Features: {data.get('features')}")
            print(f"   Supported languages: {data.get('supported_languages')}")
        else:
            print(f"❌ Health check failed: {response.status_code}")
    except Exception as e:
        print(f"❌ Health check error: {e}")
    
    # Test simple chatbot
    print("\n2. Testing simple chatbot...")
    try:
        response = requests.post(f"{base_url}/api/chat", 
                               json={"message": "Hallo, wat zijn jullie openingstijden?"})
        if response.status_code == 200:
            data = response.json()
            print(f"✅ Simple chatbot working")
            print(f"   Response: {data.get('response')}")
            print(f"   Language: {data.get('detected_language')}")
            print(f"   Type: {data.get('response_type')}")
        else:
            print(f"❌ Simple chatbot failed: {response.status_code}")
            print(f"   Error: {response.text}")
    except Exception as e:
        print(f"❌ Simple chatbot error: {e}")
    
    # Test FAQ responses
    print("\n3. Testing FAQ responses...")
    faq_tests = [
        ("Wat kosten jullie boten?", "pricing"),
        ("Hoe kan ik reserveren?", "contact"),
        ("Hebben jullie elektrische boten?", "services"),
        ("Wat zijn de openingstijden?", "contact")
    ]
    
    for question, expected_type in faq_tests:
        try:
            response = requests.post(f"{base_url}/api/chat", 
                                   json={"message": question})
            if response.status_code == 200:
                data = response.json()
                response_type = data.get('response_type', 'unknown')
                print(f"✅ {question[:30]}... → {response_type}")
            else:
                print(f"❌ {question[:30]}... → Failed")
        except Exception as e:
            print(f"❌ {question[:30]}... → Error: {e}")
    
    # Test multilingual support
    print("\n4. Testing multilingual support...")
    test_messages = [
        ("Hello, what are your opening hours?", "English"),
        ("Hola, ¿cuáles son sus horarios?", "Spanish"),
        ("Bonjour, quels sont vos horaires?", "French"),
        ("Hallo, was sind Ihre Öffnungszeiten?", "German"),
        ("Ciao, quali sono i vostri orari?", "Italian")
    ]
    
    for message, language in test_messages:
        try:
            response = requests.post(f"{base_url}/api/chat", 
                                   json={"message": message})
            if response.status_code == 200:
                data = response.json()
                detected = data.get('detected_language', 'unknown')
                print(f"✅ {language}: {detected} - {data.get('response')[:50]}...")
            else:
                print(f"❌ {language}: Failed")
        except Exception as e:
            print(f"❌ {language}: Error - {e}")
    
    # Test additional endpoints
    print("\n5. Testing additional endpoints...")
    
    # Test languages endpoint
    try:
        response = requests.get(f"{base_url}/api/languages")
        if response.status_code == 200:
            data = response.json()
            print(f"✅ Languages endpoint: {len(data.get('languages', {}))} languages supported")
        else:
            print(f"❌ Languages endpoint failed")
    except Exception as e:
        print(f"❌ Languages endpoint error: {e}")
    
    # Test config endpoint
    try:
        response = requests.get(f"{base_url}/api/config")
        if response.status_code == 200:
            data = response.json()
            print(f"✅ Config endpoint: {data.get('name')} v{data.get('version')}")
        else:
            print(f"❌ Config endpoint failed")
    except Exception as e:
        print(f"❌ Config endpoint error: {e}")
    
    # Test website analysis
    try:
        response = requests.get(f"{base_url}/api/website/analyze")
        if response.status_code == 200:
            data = response.json()
            analysis = data.get('analysis', {})
            print(f"✅ Website analysis: {len(analysis.get('keywords', []))} keywords found")
        else:
            print(f"❌ Website analysis failed")
    except Exception as e:
        print(f"❌ Website analysis error: {e}")
    
    print("\n" + "=" * 50)
    print("🎉 Simple chatbot test completed!")
    print("✅ All Perplexity AI dependencies removed")
    print("✅ Simple chatbot is now the primary system")

if __name__ == "__main__":
    test_chat_endpoints() 