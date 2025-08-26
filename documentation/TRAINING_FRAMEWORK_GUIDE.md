# 🤖 Nijenhuis Chatbot Training Framework - Standalone Guide

This guide explains how to run the Nijenhuis Chatbot Training Framework as a standalone application on any system.

## 📋 Prerequisites

### Required Software
- **Python 3.7 or higher**
- **pip** (Python package manager)

### Required Python Packages
- `tkinter` (usually included with Python)
- `requests`
- `numpy` (for neural network features)

## 🚀 Quick Start

### Option 1: Using the Launcher Script (Recommended)

#### On macOS/Linux:
```bash
./run_training_framework.sh
```

#### On Windows:
```cmd
run_training_framework.bat
```

#### On any system with Python:
```bash
python3 run_training_framework.py
```

### Option 2: Manual Setup

1. **Install dependencies:**
   ```bash
   pip install requests numpy
   ```

2. **Start the chatbot server:**
   ```bash
   python3 chatbot_backend.py
   ```

3. **In a new terminal, start the training framework:**
   ```bash
   python3 training_framework.py
   ```

## 📁 File Structure

Make sure you have these files in your directory:
```
Nijenhuis/
├── run_training_framework.py      # Main launcher script
├── run_training_framework.sh      # macOS/Linux launcher
├── run_training_framework.bat     # Windows launcher
├── training_framework.py          # Training framework GUI
├── chatbot_backend.py             # Chatbot API server
├── enhanced_chatbot.py            # Enhanced chatbot logic
├── neural_network.py              # Neural network architecture
├── unsupervised_learning.py       # Unsupervised learning system
├── boat_translations.py           # Boat translation utilities
└── training_data.json             # Training data (created automatically)
```

## 🔧 Troubleshooting

### Common Issues

#### 1. "Python not found"
- **Solution:** Install Python 3.7+ from [python.org](https://python.org)
- **macOS:** Use `brew install python3`
- **Linux:** Use your package manager (e.g., `sudo apt install python3`)

#### 2. "Module not found" errors
- **Solution:** Install missing packages:
  ```bash
  pip install requests numpy
  ```

#### 3. "Port 5001 is in use"
- **Solution:** Stop any existing chatbot server:
  ```bash
  # macOS/Linux
  lsof -ti:5001 | xargs kill -9
  
  # Windows
  netstat -ano | findstr :5001
  taskkill /PID <PID> /F
  ```

#### 4. "Tkinter not available"
- **macOS:** Install Python with Tkinter support
- **Linux:** Install tkinter package:
  ```bash
  sudo apt install python3-tk  # Ubuntu/Debian
  sudo yum install tkinter     # CentOS/RHEL
  ```

### System-Specific Instructions

#### macOS
1. Install Python using Homebrew:
   ```bash
   brew install python3
   ```

2. Run the framework:
   ```bash
   ./run_training_framework.sh
   ```

#### Windows
1. Download Python from [python.org](https://python.org)
2. Make sure to check "Add Python to PATH" during installation
3. Run the framework:
   ```cmd
   run_training_framework.bat
   ```

#### Linux (Ubuntu/Debian)
1. Install required packages:
   ```bash
   sudo apt update
   sudo apt install python3 python3-pip python3-tk
   ```

2. Run the framework:
   ```bash
   ./run_training_framework.sh
   ```

## 🎯 Using the Training Framework

### Interface Overview
- **Input Field:** Enter test questions for the chatbot
- **Response Display:** Shows the chatbot's current response
- **Correction Field:** Enter improved responses
- **Save/Skip Buttons:** Save improvements or skip to next question
- **Training History:** View and edit previous training data

### Training Process
1. **Test Questions:** Enter questions in any language (Dutch, German, English)
2. **Review Responses:** Check the chatbot's automatic response
3. **Improve Responses:** Enter better answers if needed
4. **Save Improvements:** Click "Save" to train the chatbot
5. **Monitor Progress:** View training history and statistics

### Advanced Features
- **Neural Network Integration:** Automatic pattern recognition
- **Unsupervised Learning:** Continuous improvement from interactions
- **Multilingual Support:** Works with Dutch, German, and English
- **Boat Translation:** Automatic translation of boat model names

## 📊 Monitoring and Analytics

### Learning Statistics
Access learning statistics via API:
```bash
curl http://localhost:5001/api/learning/stats
```

### Neural Network Information
View neural network architecture:
```bash
curl http://localhost:5001/api/neural-network/info
```

### Training Data
Training data is automatically saved to `training_data.json` and can be:
- Viewed in the training framework
- Edited manually
- Backed up for safekeeping

## 🔄 Continuous Learning

The system automatically:
- **Learns from interactions** using unsupervised learning
- **Improves responses** based on training data
- **Recognizes patterns** using neural networks
- **Translates content** across languages
- **Optimizes performance** over time

## 🛠️ Development and Customization

### Adding New Features
1. Modify `enhanced_chatbot.py` for chatbot logic
2. Update `neural_network.py` for AI improvements
3. Enhance `unsupervised_learning.py` for learning algorithms
4. Customize `training_framework.py` for UI changes

### Extending Training Data
- Add new question-response pairs in the training framework
- Import existing data by editing `training_data.json`
- Use the API to programmatically add training examples

## 📞 Support

If you encounter issues:
1. Check the troubleshooting section above
2. Verify all required files are present
3. Ensure Python and dependencies are properly installed
4. Check that port 5001 is available

## 🎉 Success Indicators

You'll know the framework is working correctly when:
- ✅ The GUI window opens successfully
- ✅ You can test questions and see responses
- ✅ Training data is saved and persists
- ✅ The chatbot improves over time
- ✅ Neural network predictions are available
- ✅ Learning statistics show positive trends

---

**Happy Training! 🚀** 