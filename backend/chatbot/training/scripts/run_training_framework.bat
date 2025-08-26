@echo off
echo 🤖 Nijenhuis Chatbot Training Framework Launcher
echo ==================================================
echo.

REM Check if Python is available
python --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Python is not installed or not in PATH
    echo Please install Python 3.7+ and try again
    pause
    exit /b 1
)

echo ✅ Python found
echo.

REM Run the training framework
echo 🚀 Starting Training Framework...
python run_training_framework.py

echo.
echo 👋 Training Framework closed
pause 