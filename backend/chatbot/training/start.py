#!/usr/bin/env python3
"""
Training Framework Launcher for Nijenhuis Chatbot
Simple script to start the training framework
"""

import sys
import os

def check_dependencies():
    """Check if required dependencies are available"""
    try:
        import tkinter
        import requests
        print("✅ All dependencies are available")
        return True
    except ImportError as e:
        print(f"❌ Missing dependency: {e}")
        print("Please install required packages:")
        print("pip install requests")
        return False

def check_chatbot_server():
    """Check if the chatbot server is running"""
    try:
        import requests
        response = requests.get("http://localhost:5001/api/health", timeout=5)
        if response.status_code == 200:
            print("✅ Chatbot server is running")
            return True
        else:
            print("⚠️  Chatbot server responded with error")
            return False
    except requests.exceptions.RequestException:
        print("❌ Chatbot server is not running")
        print("Please start the chatbot server first:")
        print("python3 backend/chatbot/api/server.py")
        return False

def main():
    """Main function"""
    print("🤖 Nijenhuis Chatbot Training Framework Launcher")
    print("=" * 50)
    
    # Check dependencies
    if not check_dependencies():
        sys.exit(1)
    
    # Check if chatbot server is running
    if not check_chatbot_server():
        print("\n💡 You can still use the training framework to collect corrections,")
        print("   but you won't be able to test the chatbot until the server is running.")
        response = input("\nDo you want to continue anyway? (y/n): ")
        if response.lower() != 'y':
            sys.exit(1)
    
    # Import and start training framework
    try:
        from framework import ChatbotTrainingFramework
        print("\n🚀 Starting Training Framework...")
        app = ChatbotTrainingFramework()
        app.run()
    except ImportError as e:
        print(f"❌ Could not import training framework: {e}")
        print("Please ensure training_framework.py is in the current directory.")
        sys.exit(1)
    except Exception as e:
        print(f"❌ Error starting training framework: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main() 