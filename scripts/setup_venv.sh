#!/bin/bash
# Setup Virtual Environment for Nijenhuis Chatbot
# Creates and configures a Python virtual environment

set -e  # Exit on error

echo "============================================================"
echo "Setting up Python Virtual Environment"
echo "============================================================"

# Check if venv already exists
if [ -d "venv" ]; then
    echo "⚠️  Virtual environment already exists at ./venv"
    read -p "Do you want to recreate it? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "Removing existing virtual environment..."
        rm -rf venv
    else
        echo "Using existing virtual environment."
        echo ""
        echo "To activate it, run:"
        echo "  source venv/bin/activate"
        exit 0
    fi
fi

# Check Python version
echo "Checking Python version..."
python3 --version

# Create virtual environment
echo ""
echo "Creating virtual environment..."
python3 -m venv venv

# Activate virtual environment
echo "Activating virtual environment..."
source venv/bin/activate

# Upgrade pip
echo ""
echo "Upgrading pip..."
pip install --upgrade pip

# Install basic requirements
echo ""
echo "Installing basic requirements..."
pip install -r requirements.txt



echo ""
echo "============================================================"
echo "✅ Virtual environment setup complete!"
echo "============================================================"
echo ""
echo "To activate the virtual environment, run:"
echo "  source venv/bin/activate"
echo ""
echo "To deactivate when done, run:"
echo "  deactivate"
echo ""
echo "To verify installation, run:"
echo "  python3 -m backend.api.app"
echo ""

