#!/usr/bin/env python3
"""
Standalone Training Framework Launcher
Run this script to start the Nijenhuis Chatbot Training Framework outside of Cursor
"""

import os
import sys
import subprocess
import time
import signal
import threading
from pathlib import Path

def check_dependencies():
    """Check if all required dependencies are available"""
    print("🔍 Checking dependencies...")
    
    required_modules = [
        'tkinter',
        'requests',
        'json',
        'threading',
        'datetime'
    ]
    
    missing_modules = []
    
    for module in required_modules:
        try:
            __import__(module)
            print(f"   ✅ {module}")
        except ImportError:
            print(f"   ❌ {module} - Missing")
            missing_modules.append(module)
    
    if missing_modules:
        print(f"\n❌ Missing dependencies: {', '.join(missing_modules)}")
        print("Please install missing dependencies and try again.")
        return False
    
    print("✅ All dependencies are available")
    return True

def check_chatbot_server():
    """Check if the chatbot server is running"""
    print("\n🔍 Checking chatbot server...")
    
    try:
        import requests
        response = requests.get("http://localhost:5001/api/health", timeout=5)
        if response.status_code == 200:
            print("✅ Chatbot server is running")
            return True
        else:
            print("❌ Chatbot server is not responding correctly")
            return False
    except requests.exceptions.RequestException:
        print("❌ Chatbot server is not running")
        return False

def start_chatbot_server():
    """Start the chatbot server in the background"""
    print("\n🚀 Starting chatbot server...")
    
    try:
        # Check if chatbot_backend.py exists
        if not os.path.exists("chatbot_backend.py"):
            print("❌ chatbot_backend.py not found in current directory")
            return False
        
        # Start the server in a subprocess
        server_process = subprocess.Popen(
            [sys.executable, "chatbot_backend.py"],
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            text=True
        )
        
        # Wait a moment for the server to start
        time.sleep(3)
        
        # Check if server started successfully
        if server_process.poll() is None:
            print("✅ Chatbot server started successfully")
            return server_process
        else:
            print("❌ Failed to start chatbot server")
            return False
            
    except Exception as e:
        print(f"❌ Error starting chatbot server: {e}")
        return False

def run_training_framework():
    """Run the training framework"""
    print("\n🚀 Starting Training Framework...")
    
    try:
        # Check if training_framework.py exists
        if not os.path.exists("training_framework.py"):
            print("❌ training_framework.py not found in current directory")
            return False
        
        # Import and run the training framework
        import training_framework
        
        # This will start the GUI
        print("✅ Training Framework started successfully")
        print("📝 The GUI window should now be open")
        print("🔧 You can now train and test the chatbot")
        
        return True
        
    except Exception as e:
        print(f"❌ Error starting training framework: {e}")
        return False

def main():
    """Main launcher function"""
    print("🤖 Nijenhuis Chatbot Training Framework Launcher")
    print("=" * 50)
    
    # Get the current directory
    current_dir = os.getcwd()
    print(f"📍 Working directory: {current_dir}")
    
    # Check if we're in the right directory
    required_files = ["training_framework.py", "chatbot_backend.py"]
    missing_files = []
    
    for file in required_files:
        if not os.path.exists(file):
            missing_files.append(file)
    
    if missing_files:
        print(f"\n❌ Missing required files: {', '.join(missing_files)}")
        print("Please make sure you're in the correct directory containing the chatbot files.")
        return
    
    print("✅ All required files found")
    
    # Check dependencies
    if not check_dependencies():
        return
    
    # Check if chatbot server is running, start if not
    server_process = None
    if not check_chatbot_server():
        server_process = start_chatbot_server()
        if not server_process:
            print("\n❌ Cannot start training framework without chatbot server")
            return
    
    # Run the training framework
    if run_training_framework():
        print("\n🎉 Training Framework is now running!")
        print("\n📋 Instructions:")
        print("   1. Use the input field to test questions")
        print("   2. Review the chatbot's response")
        print("   3. Enter corrected responses if needed")
        print("   4. Click 'Save' to improve the chatbot")
        print("   5. View training history and edit entries")
        print("\n🔄 The framework will automatically learn and improve!")
        
        # Keep the script running
        try:
            while True:
                time.sleep(1)
        except KeyboardInterrupt:
            print("\n\n👋 Shutting down...")
            
            # Clean up server process if we started it
            if server_process:
                print("🛑 Stopping chatbot server...")
                server_process.terminate()
                server_process.wait()
                print("✅ Server stopped")
    else:
        print("\n❌ Failed to start training framework")

if __name__ == "__main__":
    main() 