# 🚀 **Nijenhuis Chatbot Integration Guide**

## 📋 **Files to Add to Your Nijenhuis Repository**

### **Essential Files (Required):**
1. `simple_chatbot_demo.py` - Core chatbot engine
2. `chatbot-api.py` - Lightweight API server
3. `chatbot-widget.css` - Widget styling
4. `chatbot-widget.js` - Widget functionality
5. `requirements.txt` - Python dependencies

### **Optional Files:**
6. `chatbot-widget.html` - Example integration
7. `README.md` - Integration instructions

## 🔧 **Integration Steps**

### **Step 1: Add Files to Your Repository**

Copy these files to your Nijenhuis repository:

```bash
# Copy the essential files
cp simple_chatbot_demo.py /path/to/your/nijenhuis/repo/
cp chatbot-api.py /path/to/your/nijenhuis/repo/
cp chatbot-widget.css /path/to/your/nijenhuis/repo/
cp chatbot-widget.js /path/to/your/nijenhuis/repo/
cp requirements.txt /path/to/your/nijenhuis/repo/
```

### **Step 2: Add Widget to Your Website**

Add this HTML code to your website's `<head>` section:

```html
<link rel="stylesheet" href="chatbot-widget.css">
<script src="chatbot-widget.js" defer></script>
```

Add this HTML code to your website's `<body>` section (before the closing `</body>` tag):

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
                    Hello! Welcome to Nijenhuis. How can I help you today? 👋
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

### **Step 3: Start the Chatbot API**

```bash
# Install dependencies
pip3 install -r requirements.txt

# Start the API server
python3 chatbot-api.py
```

### **Step 4: Update API Endpoint**

In `chatbot-widget.js`, update the API endpoint:

```javascript
// For local development
this.apiEndpoint = 'http://localhost:5000/api/chat';

// For production (update with your server URL)
// this.apiEndpoint = 'https://your-nijenhuis-server.com/api/chat';
```

## 🎨 **Customization for Nijenhuis**

### **Update Website Content**

Edit `chatbot-api.py` and update the `NIJENHUIS_WEBSITE_CONTENT` variable with your actual website content:

```python
NIJENHUIS_WEBSITE_CONTENT = """
Welcome to Nijenhuis

[Your actual website content here]
- Services
- About Us
- Contact Information
- FAQs
"""
```

### **Customize Branding**

Edit `chatbot-widget.css` to match your brand colors:

```css
.chat-widget {
    --primary-color: #your-nijenhuis-brand-color;
    --secondary-color: #your-nijenhuis-accent-color;
}
```

### **Customize Responses**

Edit `simple_chatbot_demo.py` to add Nijenhuis-specific responses:

```python
language_responses = {
    'en': {
        'greeting': 'Welcome to Nijenhuis! How can I help you today?',
        'contact': 'You can reach Nijenhuis at info@nijenhuis.com or +31-123-456-789.',
        # Add more Nijenhuis-specific responses
    }
}
```

## 🚀 **Deployment Options**

### **Option 1: Local Development**
```bash
python3 chatbot-api.py
# Chatbot will be available at http://localhost:5000
```

### **Option 2: Heroku Deployment**
```bash
# Create Procfile
echo "web: gunicorn chatbot-api:app" > Procfile

# Deploy to Heroku
heroku create nijenhuis-chatbot
git add .
git commit -m "Add chatbot to Nijenhuis"
git push heroku main
```

### **Option 3: Vercel/Netlify (Static + API)**
- Deploy the API to a server (Heroku, DigitalOcean, etc.)
- Deploy your website to Vercel/Netlify
- Update the API endpoint in the widget

### **Option 4: Docker Deployment**
```dockerfile
FROM python:3.9-slim
WORKDIR /app
COPY requirements.txt .
RUN pip install -r requirements.txt
COPY . .
EXPOSE 5000
CMD ["gunicorn", "-w", "4", "-b", "0.0.0.0:5000", "chatbot-api:app"]
```

## 🔧 **Advanced Integration**

### **Add to Your Existing Website**

If you have an existing Nijenhuis website, you can add the chatbot by:

1. **Copy the widget files** to your website directory
2. **Include the CSS and JS** in your HTML
3. **Add the widget HTML** to your pages
4. **Start the API server** separately

### **WordPress Integration**

If your Nijenhuis website uses WordPress:

1. Add this to your theme's `functions.php`:
```php
function add_nijenhuis_chatbot() {
    wp_enqueue_style('chatbot-widget', get_template_directory_uri() . '/chatbot-widget.css');
    wp_enqueue_script('chatbot-widget', get_template_directory_uri() . '/chatbot-widget.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'add_nijenhuis_chatbot');

function add_chatbot_widget_html() {
    echo '<div id="chat-widget" class="chat-widget">...</div>';
}
add_action('wp_footer', 'add_chatbot_widget_html');
```

### **React/Next.js Integration**

If your Nijenhuis website uses React:

1. Copy the CSS to your styles
2. Create a React component from the widget HTML
3. Use the JavaScript functions in your React components

## 📊 **Testing**

### **Test the Integration**

1. **Start the API server**:
   ```bash
   python3 chatbot-api.py
   ```

2. **Open your website** and test the chat widget

3. **Test multilingual support**:
   - English: "Hello, I need help"
   - Spanish: "Hola, necesito ayuda"
   - French: "Bonjour, j'ai besoin d'aide"

4. **Test different queries**:
   - "What services do you offer?"
   - "How can I contact you?"
   - "Tell me about Nijenhuis"

## 🔒 **Security & Production**

### **Production Checklist**

- [ ] Update API endpoint to production URL
- [ ] Add HTTPS support
- [ ] Implement rate limiting
- [ ] Add input validation
- [ ] Set up monitoring
- [ ] Configure CORS properly
- [ ] Add error logging

### **Security Features**

The chatbot includes:
- ✅ Input validation
- ✅ XSS protection
- ✅ CORS support
- ✅ Error handling
- ✅ Rate limiting ready

## 📞 **Support**

### **Troubleshooting**

1. **Chatbot not responding**: Check if API server is running
2. **CORS errors**: Ensure CORS is properly configured
3. **Styling issues**: Check if CSS is loaded correctly
4. **JavaScript errors**: Check browser console for errors

### **Getting Help**

- Check the browser console for errors
- Verify the API server is running
- Test the API endpoints directly
- Review the integration code

## 🎉 **Success!**

Once integrated, your Nijenhuis website will have:

- ✅ **Multilingual customer support** (5 languages)
- ✅ **Real-time chat** with typing indicators
- ✅ **Responsive design** (mobile-friendly)
- ✅ **Website content analysis**
- ✅ **Professional appearance**
- ✅ **Easy customization**

**Your Nijenhuis website now has a professional, multilingual customer support chatbot!** 🚀 