# Simple Chatbot Integration Documentation

## Overview

The Nijenhuis website now features a lightweight, multilingual chatbot system that provides intelligent customer support without external AI dependencies. The system includes:

- **Simple Chatbot**: A rule-based chatbot with language detection and website content analysis
- **Multilingual Support**: Automatic language detection and responses in 6 languages
- **FAQ Integration**: Built-in FAQ system for common questions
- **Website Content Analysis**: Intelligent responses based on website content

## Features

### 🚀 Core Features
- **Multilingual Support**: Detects and responds in 3 languages (Dutch, English, German)
- **Language Detection**: Automatically detects user language and responds appropriately
- **Contextual Responses**: Provides specific answers based on the exact question asked
- **Real-time Typing Indicators**: Shows when the bot is processing
- **Call Button Integration**: Direct phone call buttons for urgent inquiries
- **Conversation History**: Maintains chat context
- **No External Dependencies**: Works completely offline without API costs

### 🎯 Business Intelligence
- **Website Content Analysis**: Analyzes website content for better responses
- **FAQ Integration**: Built-in FAQ system for common questions
- **Contact Information**: Automatic contact details in responses
- **Pricing Information**: Real-time pricing for boat rentals
- **Contextual Understanding**: Recognizes specific boat types, sizes, and requirements
- **Detailed Responses**: Provides comprehensive information including capacity, features, and recommendations

## Technical Architecture

### Backend API (`chatbot_backend.py`)
```
/api/chat              - Main chat endpoint
/api/health            - Health check endpoint
/api/languages         - Supported languages
/api/website/analyze   - Website content analysis
/api/config            - Chatbot configuration
```

### Frontend Integration (`js/simple-chatbot.js`)
- Lightweight chat widget
- Language detection indicators
- Typing animations
- Call button integration
- Conversation management

### Simple Chatbot (`simple_chatbot_demo.py`)
- Language detection using common word patterns
- Website content analysis
- FAQ extraction and matching
- Contact information parsing

## Setup Instructions

### 1. Install Dependencies
```bash
pip install -r requirements.txt
```

### 2. Start the Backend Server
```bash
python3 chatbot_backend.py
```

### 3. Test the Integration
```bash
python3 test_chat_integration.py
```

## Usage

### Basic Usage
The chatbot automatically handles all interactions. Users can:
- Type messages in any supported language
- Get instant responses based on website content
- Use call buttons for direct contact
- See language detection indicators

### Programmatic Control
```javascript
// Open chat
window.simpleChatbot.openChat();

// Close chat
window.simpleChatbot.closeChat();

// Clear history
window.simpleChatbot.clearHistory();

// Send message programmatically
window.simpleChatbot.sendMessageProgrammatically('Hello');
```

## API Endpoints

### Health Check
```http
GET /api/health
```
Returns system status and available features.

### Main Chat
```http
POST /api/chat
Content-Type: application/json

{
  "message": "Wat kost de Tender 720?"
}
```

**Enhanced Response Example:**
```json
{
  "detected_language": "nl",
  "response": "De Tender 720 kost €230 per dag en is geschikt voor maximaal 12 personen. Deze elektrische boot is ideaal voor grotere groepen en heeft een comfortabele uitrusting.",
  "response_type": "pricing",
  "success": true
}
```

### Languages
```http
GET /api/languages
```
Returns supported languages and default language.

### Website Analysis
```http
GET /api/website/analyze
```
Returns analysis of website content including keywords and FAQs.

### Configuration
```http
GET /api/config
```
Returns chatbot configuration and features.

## Response Format

### Chat Response
```json
{
  "response": "We zijn dagelijks open van 09:00 tot 18:00 van 1 april tot 1 november.",
  "detected_language": "nl",
  "response_type": "contact",
  "success": true
}
```

### Health Check Response
```json
{
  "status": "healthy",
  "service": "Nijenhuis Chatbot API",
  "version": "2.0.0",
  "features": {
    "simple_chatbot": true,
    "multilingual_support": true,
    "website_analysis": true,
    "faq_integration": true
  },
  "supported_languages": ["nl", "en", "de"]
}
```

## Customization

### Updating Website Content
Edit the `NIJENHUIS_WEBSITE_CONTENT` variable in `chatbot_backend.py` to include:
- Company information
- Services and pricing
- FAQ sections
- Contact details

### Adding New Languages
1. Update `language_patterns` in `SimpleLanguageDetector`
2. Add responses in `language_responses` in `SimpleChatbot`
3. Update frontend language names in `showLanguageInfo()`

### Styling Customization
The chat widget styles are in `styles.css`:
- `.language-info-bubble` - Language detection messages
- `.chat-call-button` - Call button styling
- `.typing-dots` - Typing animation

## Troubleshooting

### Common Issues

1. **Chatbot not loading**
   - Check if `simple_chatbot_demo.py` is in the same directory
   - Verify Python dependencies are installed

2. **Language detection issues**
   - Ensure language patterns are properly configured
   - Test with clear language indicators

3. **Frontend not connecting**
   - Verify backend server is running on port 5000
   - Check browser console for CORS errors

4. **Responses not relevant**
   - Update the `NIJENHUIS_WEBSITE_CONTENT` with current information
   - Add more FAQ entries for better coverage

### Debug Mode
Enable debug information by setting:
```javascript
localStorage.setItem('chatDebug', 'true');
```

## Performance Benefits

- **Zero API Costs**: No external API calls required
- **Fast Responses**: Instant responses for all queries
- **Offline Capability**: Works without internet connection
- **Scalable**: No rate limits or usage quotas
- **Privacy**: All processing happens locally

## Security Notes

- No external API dependencies
- CORS is enabled for development (restrict for production)
- Input validation is implemented on all endpoints
- No sensitive data is transmitted to external services

## File Structure

```
├── chatbot_backend.py          # Main backend server
├── simple_chatbot_demo.py      # Chatbot logic
├── js/simple-chatbot.js        # Frontend integration
├── test_chat_integration.py    # Test script
├── CHAT_INTEGRATION.md         # This documentation
└── requirements.txt            # Python dependencies
```

## Future Enhancements

- [ ] Add conversation memory across sessions
- [ ] Implement sentiment analysis
- [ ] Add image recognition for boat identification
- [ ] Integrate with booking system
- [ ] Add voice chat capabilities
- [ ] Implement chat analytics and reporting
- [ ] Add more sophisticated language detection
- [ ] Expand FAQ database

## Support

For technical support or questions about the chatbot integration, please contact the development team or refer to the test script for troubleshooting.

## Migration from Perplexity AI

This system completely replaces the previous Perplexity AI integration. Benefits of the migration:

- **Cost Reduction**: No API costs
- **Reliability**: No dependency on external services
- **Speed**: Instant responses
- **Privacy**: No data sent to external services
- **Simplicity**: Single system to maintain

All Perplexity AI references have been removed from the codebase. 