#!/usr/bin/env python3
"""
Startup script for Nijenhuis Simple Chatbot
"""

import subprocess
import sys
import os

def main():
    print("🤖 Starting Nijenhuis Simple Chatbot...")
    print("=" * 50)
    
    # Check if required files exist
    required_files = [
        'chatbot_backend.py',
        'simple_chatbot_demo.py'
    ]
    
    for file in required_files:
        if not os.path.exists(file):
            print(f"❌ Error: {file} not found!")
            print("Please ensure all required files are in the current directory.")
            sys.exit(1)
    
    print("✅ All required files found")
    
    # Check Python dependencies
    try:
        import flask
        import flask_cors
        print("✅ Python dependencies available")
    except ImportError as e:
        print(f"❌ Missing dependency: {e}")
        print("Please run: pip3 install -r requirements.txt")
        sys.exit(1)
    
    print("\n🚀 Starting chatbot server...")
    print("📍 Server will be available at: http://localhost:5000")
    print("🔗 Main endpoint: http://localhost:5000/api/chat")
    print("🏥 Health check: http://localhost:5000/api/health")
    print("\n💡 To test the chatbot, run: python3 test_chat_integration.py")
    print("🛑 Press Ctrl+C to stop the server")
    print("=" * 50)
    
    try:
        # Start the chatbot server
        subprocess.run([sys.executable, 'chatbot_backend.py'])
    except KeyboardInterrupt:
        print("\n👋 Chatbot server stopped")
    except Exception as e:
        print(f"❌ Error starting server: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main() 