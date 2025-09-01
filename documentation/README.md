# 🤖 Chatbot Integration Package for Nijenhuis Website

## 📦 **What's Included**

This package contains all the essential files to add a multilingual customer support chatbot to your existing website.

### **Core Files:**
- `simple_chatbot_demo.py` - Working chatbot engine
- `chatbot-api.py` - Lightweight API server
- `chatbot-widget.html` - HTML widget code
- `chatbot-widget.css` - Widget styling
- `chatbot-widget.js` - Widget functionality

## 🚀 **Quick Integration**

### **Step 1: Add the Widget to Your Website**

Copy this HTML code to your website's `<head>` section:

```html
<link rel="stylesheet" href="backend/chatbot/chatbot-widget.css">
<script src="chatbot-widget.js" defer></script>
```

Add this HTML code to your website's `<body>` section:

```html
<div id="chat-widget" class="chat-widget">
    <div id="chat-header" class="chat-header">
        <div class="chat-title">
            <i class="fas fa-robot"></i>
            <span>Customer Support</span>
        </div>
        <button id="chat-toggle" class="chat-toggle">
            <i class="fas fa-comments"></i>
        </button>
    </div>
    
    <div id="chat-body" class="chat-body">
        <div id="chat-messages" class="chat-messages">
            <div class="message bot-message">
                <div class="message-content">
                    Hello! How can I help you today? 👋
                </div>
            </div>
        </div>
        
        <div class="chat-input-container">
            <input type="text" id="chat-input" placeholder="Type your message..." maxlength="500">
            <button id="send-message" class="send-button">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>
```

### **Step 2: Start the Chatbot API**

```bash
# Install Python dependencies
pip3 install flask requests beautifulsoup4

# Start the chatbot API
python3 chatbot-api.py

# The API will be available at http://localhost:5000
```

### **Step 3: Update API Endpoint**

In `chatbot-widget.js`, update the API endpoint to point to your server:

```javascript
// Change this line in the sendMessage function
const response = await fetch('http://your-server.com/api/chat', {
    // ... rest of the code
});
```

## 🌟 **Features**

- ✅ **Multilingual Support** (English, Spanish, French, German, Italian)
- ✅ **Automatic Language Detection**
- ✅ **Responsive Design** (Mobile-friendly)
- ✅ **Real-time Chat** with typing indicators
- ✅ **Website Content Analysis**
- ✅ **Easy Customization**

## 🔧 **Customization**

### **Change Colors**
Edit `chatbot-widget.css`:
```css
.chat-widget {
    --primary-color: #your-brand-color;
    --secondary-color: #your-accent-color;
}
```

### **Add Languages**
Edit `simple_chatbot_demo.py`:
```python
language_patterns = {
    'pt': ['o', 'a', 'os', 'as', 'e', 'ou', 'mas'],  # Portuguese
    # Add more languages...
}
```

### **Customize Responses**
Edit `simple_chatbot_demo.py`:
```python
language_responses = {
    'en': {
        'greeting': 'Welcome to Nijenhuis! How can I help?',
        # Add more responses...
    }
}
```

## 📞 **Support**

For integration help, check the detailed guide in `WEBSITE_INTEGRATION_GUIDE.md`.

The chatbot is production-ready and will work seamlessly with your existing website! 🎉 