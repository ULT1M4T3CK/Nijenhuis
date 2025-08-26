#!/usr/bin/env python3
# ========================================================================
# START BOOKING SERVER - LOCAL DEVELOPMENT
# ========================================================================

import os
import sys
import subprocess
import webbrowser
import time
from pathlib import Path

def main():
    print("🚀 Starting Nijenhuis Booking System - Local Development")
    print("=" * 60)
    
    # Ensure we run from repo root even if executed from scripts/dev
    repo_root = Path(__file__).resolve().parents[2]
    os.chdir(repo_root)
    if not os.path.exists('admin/booking-handler.py'):
        print("❌ Error: admin/booking-handler.py not found in repo root")
        sys.exit(1)
    
    # Set up the server
    port = 8000
    server_script = 'admin/booking-handler.py'
    
    print(f"📁 Project directory: {os.getcwd()}")
    print(f"🌐 Server will run on: http://localhost:{port}")
    print(f"🔧 Admin area: http://localhost:{port}/admin/")
    print(f"📝 Booking API: http://localhost:{port}/admin/booking-handler.py")
    print()
    
    # Make the server script executable
    os.chmod(server_script, 0o755)
    
    try:
        print("🔄 Starting booking server...")
        print("   Press Ctrl+C to stop the server")
        print("-" * 60)
        
        # Start the server
        process = subprocess.Popen([sys.executable, server_script, str(port)])
        
        # Wait a moment for server to start
        time.sleep(2)
        
        # Open browser to admin area
        print("🌐 Opening admin area in browser...")
        webbrowser.open(f'http://localhost:{port}/admin/')
        
        print("✅ Booking server is running!")
        print("📋 You can now:")
        print("   • Test the booking form on the main website")
        print("   • Access the admin area at http://localhost:8000/admin/")
        print("   • View bookings in admin/bookings.json")
        
        # Wait for the process to finish
        process.wait()
        
    except KeyboardInterrupt:
        print("\n🛑 Stopping server...")
        if 'process' in locals():
            process.terminate()
        print("✅ Server stopped")
    except Exception as e:
        print(f"❌ Error starting server: {e}")
        sys.exit(1)

if __name__ == '__main__':
    main() 