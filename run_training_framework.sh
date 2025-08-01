#!/bin/bash

echo "🤖 Nijenhuis Chatbot Training Framework Launcher"
echo "=================================================="
echo

# Check if Python is available
if ! command -v python3 &> /dev/null; then
    echo "❌ Python 3 is not installed or not in PATH"
    echo "Please install Python 3.7+ and try again"
    exit 1
fi

echo "✅ Python 3 found"
echo

# Make the script executable
chmod +x run_training_framework.py

# Run the training framework
echo "🚀 Starting Training Framework..."
python3 run_training_framework.py

echo
echo "👋 Training Framework closed" 