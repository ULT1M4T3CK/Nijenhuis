# 🤖 Nijenhuis Chatbot Training Framework

A modern, interactive training framework for improving the Nijenhuis chatbot responses through manual testing and correction.

## 🚀 Features

### 📝 **Interactive Testing**
- Real-time chatbot testing with instant responses
- Support for all 3 languages (Dutch, English, German)
- Visual display of detected language and response type

### ✏️ **Response Correction**
- Manual correction of chatbot responses
- Optional correction field for each test
- Automatic saving of improvements

### 📊 **Training Statistics**
- Real-time accuracy tracking
- Total tests and improved responses counter
- Training progress visualization

### 📚 **Training History**
- Complete history of all training sessions
- Searchable training data
- Export functionality for backup

### 🎯 **Smart Improvements**
- Enhanced chatbot that learns from corrections
- Similarity matching for related questions
- Automatic response improvement

## 🛠️ Setup Instructions

### 1. **Prerequisites**
```bash
# Install required dependencies
pip install requests flask flask-cors
```

### 2. **Start the Chatbot Server**
```bash
# Start the backend server
python3 chatbot_backend.py
```

### 3. **Launch Training Framework**
```bash
# Start the training framework
python3 start_training.py
```

## 📖 Usage Guide

### **Testing the Chatbot**

1. **Enter Test Question**: Type your question in the input field
2. **Test Response**: Click "🚀 Test Chatbot" or press Enter
3. **Review Response**: View the chatbot's response and metadata
4. **Provide Correction**: Enter an improved response if needed
5. **Save or Skip**: Choose to save the correction or skip

### **Training Workflow**

```
Test Question → Chatbot Response → Review → Correct → Save → Learn
```

### **Example Training Session**

1. **Input**: "Wat kost de Tender 720?"
2. **Original Response**: "De Tender 720 kost €230 per dag en is geschikt voor maximaal 12 personen."
3. **Correction**: "De Tender 720 kost €230 per dag en is geschikt voor maximaal 12 personen. Deze elektrische boot is ideaal voor grotere groepen en heeft een comfortabele uitrusting met overkapping."
4. **Save**: The correction is saved and will be used for similar questions

## 📁 File Structure

```
Nijenhuis/
├── training_framework.py      # Main training framework
├── enhanced_chatbot.py        # Enhanced chatbot with training data
├── start_training.py          # Training framework launcher
├── training_data.json         # Training data storage
├── chatbot_backend.py         # Updated backend with enhanced chatbot
└── TRAINING_FRAMEWORK.md      # This documentation
```

## 🔧 Technical Details

### **Training Data Format**
```json
{
  "training_sessions": [
    {
      "question": "Wat kost de Tender 720?",
      "original_response": "De Tender 720 kost €230 per dag...",
      "corrected_response": "De Tender 720 kost €230 per dag...",
      "detected_language": "nl",
      "response_type": "pricing",
      "timestamp": "2025-07-30T10:30:00",
      "status": "Corrected"
    }
  ],
  "improved_responses": {
    "wat kost de tender 720?": {
      "original": "De Tender 720 kost €230 per dag...",
      "corrected": "De Tender 720 kost €230 per dag...",
      "language": "nl",
      "response_type": "pricing",
      "timestamp": "2025-07-30T10:30:00"
    }
  },
  "statistics": {
    "total_tests": 25,
    "improved_responses": 18,
    "accuracy_score": 72.0
  }
}
```

### **Enhanced Chatbot Features**

1. **Similarity Matching**: Uses Jaccard similarity to find related questions
2. **Training Data Integration**: Automatically uses corrected responses
3. **Fallback Mechanism**: Falls back to original chatbot if no training data
4. **Real-time Learning**: Updates responses based on training data

### **API Integration**

The enhanced chatbot integrates with the existing API:

```http
POST /api/chat
Content-Type: application/json

{
  "message": "Wat kost de Tender 720?"
}
```

**Enhanced Response:**
```json
{
  "response": "De Tender 720 kost €230 per dag en is geschikt voor maximaal 12 personen. Deze elektrische boot is ideaal voor grotere groepen en heeft een comfortabele uitrusting met overkapping.",
  "detected_language": "nl",
  "response_type": "pricing",
  "success": true,
  "training_improved": true,
  "original_response": "De Tender 720 kost €230 per dag en is geschikt voor maximaal 12 personen."
}
```

## 📈 Training Best Practices

### **Effective Question Testing**

1. **Test Different Languages**: Ensure all 3 languages work correctly
2. **Test Edge Cases**: Try unusual or complex questions
3. **Test Similar Questions**: Use variations of the same question
4. **Test Specific Details**: Ask for specific boat types, prices, etc.

### **Quality Corrections**

1. **Be Specific**: Provide detailed, accurate responses
2. **Include Context**: Add relevant information when helpful
3. **Maintain Consistency**: Use consistent language and tone
4. **Consider User Intent**: Think about what the user really wants to know

### **Training Data Management**

1. **Regular Exports**: Export training data regularly for backup
2. **Review History**: Periodically review training history
3. **Monitor Statistics**: Track accuracy improvements over time
4. **Clean Data**: Remove or correct any poor training examples

## 🔄 Workflow Integration

### **Development Workflow**

1. **Test**: Use the framework to test new chatbot features
2. **Correct**: Provide corrections for inaccurate responses
3. **Train**: Save corrections to improve the chatbot
4. **Deploy**: The enhanced chatbot automatically uses training data
5. **Monitor**: Track improvements through statistics

### **Quality Assurance**

- **Manual Review**: All corrections are manually reviewed
- **Consistency Check**: Ensure responses are consistent across languages
- **Accuracy Tracking**: Monitor accuracy improvements over time
- **Feedback Loop**: Continuous improvement through corrections

## 🚨 Troubleshooting

### **Common Issues**

1. **Chatbot Server Not Running**
   - Start the server: `python3 chatbot_backend.py`
   - Check if port 5001 is available

2. **Training Framework Won't Start**
   - Check dependencies: `pip install requests`
   - Ensure tkinter is available (usually included with Python)

3. **Training Data Not Loading**
   - Check file permissions for `training_data.json`
   - Verify JSON format is valid

4. **Enhanced Chatbot Not Working**
   - Check if `enhanced_chatbot.py` is in the directory
   - Verify `training_data.json` exists and is valid

### **Performance Tips**

1. **Regular Restarts**: Restart the chatbot server after significant training
2. **Data Cleanup**: Periodically clean up old training data
3. **Similarity Threshold**: Adjust similarity threshold in `enhanced_chatbot.py` if needed
4. **Memory Management**: Export and archive old training sessions

## 📊 Monitoring and Analytics

### **Key Metrics**

- **Total Tests**: Number of questions tested
- **Improved Responses**: Number of corrections made
- **Accuracy Score**: Percentage of responses that needed improvement
- **Training Progress**: Improvement over time

### **Export Options**

- **JSON Export**: Full training data export
- **Statistics Report**: Summary of training progress
- **Backup**: Regular backups of training data

## 🔮 Future Enhancements

### **Planned Features**

1. **Bulk Import**: Import training data from external sources
2. **Advanced Analytics**: Detailed performance analytics
3. **A/B Testing**: Compare different response versions
4. **Automated Suggestions**: AI-powered correction suggestions
5. **Multi-user Support**: Collaborative training environment

### **Integration Possibilities**

1. **Web Interface**: Web-based training interface
2. **API Training**: Direct API training without UI
3. **Real-time Learning**: Live learning from user interactions
4. **Advanced ML**: Machine learning-based improvements

## 📞 Support

For issues or questions about the training framework:

1. Check the troubleshooting section above
2. Review the training data format
3. Verify all dependencies are installed
4. Ensure the chatbot server is running

The training framework provides a powerful way to continuously improve the Nijenhuis chatbot through manual testing and correction, ensuring accurate and helpful responses for all users. 