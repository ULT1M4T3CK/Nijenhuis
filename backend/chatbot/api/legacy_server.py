#!/usr/bin/env python3
"""
Lightweight Chatbot API Server for Nijenhuis Website
Simple Flask API that can be easily integrated with existing websites
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import json
import os
import sys

# Add the parent directory to the path to import the chatbot
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

try:
    from backend.chatbot.core.simple_chatbot import SimpleChatbot
except ImportError:
    print("Error: simple_chatbot_demo.py not found. Please ensure it's in the parent directory.")
    sys.exit(1)

app = Flask(__name__)
# Restrict CORS to known origins
allowed_origins = [
    'https://nijenhuis-botenverhuur.com',
    'http://nijenhuis-botenverhuur.com',
    'http://85.215.195.147',
    'http://localhost:3000',
    'http://localhost:5000',
    'http://127.0.0.1:3000',
    'http://127.0.0.1:5000',
]
CORS(app, resources={r"/api/*": {"origins": allowed_origins}})

# Initialize chatbot
chatbot = SimpleChatbot()

# Sample website content for Nijenhuis (customize this for your actual content)
NIJENHUIS_WEBSITE_CONTENT = """
Welcome to Nijenhuis

We are a leading company providing innovative solutions and services.

Our Services:
- Consulting Services
- Technology Solutions
- Project Management
- Training Programs

FAQ:
Q: What services do you offer?
A: We offer consulting, technology solutions, project management, and training programs.

Q: How can I contact you?
A: You can reach us via email at info@nijenhuis.com or call us at +31-123-456-789.

Q: Do you work internationally?
A: Yes, we provide services worldwide and have offices in multiple countries.

Q: What industries do you serve?
A: We serve various industries including technology, healthcare, finance, and manufacturing.

Contact Information:
Email: info@nijenhuis.com
Phone: +31-123-456-789
Address: Nijenhuis Headquarters, Netherlands
Website: www.nijenhuis.com

About Us:
Nijenhuis is a trusted partner for businesses seeking innovative solutions. 
We have years of experience in delivering high-quality services to our clients.
"""

@app.route('/api/chat', methods=['POST'])
def chat_api():
    """Main chat API endpoint"""
    try:
        data = request.get_json()
        user_message = data.get('message', '')
        
        if not user_message:
            return jsonify({'error': 'No message provided'}), 400
        
        # Process the message with chatbot
        result = chatbot.process_query(user_message, NIJENHUIS_WEBSITE_CONTENT)
        
        return jsonify({
            'response': result['response'],
            'detected_language': result['detected_language'],
            'response_type': result['response_type'],
            'success': True
        })
        
    except Exception as e:
        print(f"Error in chat API: {str(e)}")
        return jsonify({
            'error': 'An error occurred while processing your message',
            'success': False
        }), 500

@app.route('/api/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'service': 'Nijenhuis Chatbot API',
        'version': '1.0.0'
    })

@app.route('/api/languages', methods=['GET'])
def get_languages():
    """Get supported languages"""
    return jsonify({
        'languages': {
            'en': 'English',
            'es': 'Spanish',
            'fr': 'French',
            'de': 'German',
            'it': 'Italian'
        },
        'default': 'en'
    })

@app.route('/api/website/analyze', methods=['GET'])
def analyze_website():
    """Analyze website content"""
    try:
        analysis = chatbot.website_analyzer.analyze_website_content(NIJENHUIS_WEBSITE_CONTENT)
        return jsonify({
            'analysis': analysis,
            'success': True
        })
    except Exception as e:
        return jsonify({
            'error': str(e),
            'success': False
        }), 500

@app.route('/api/config', methods=['GET'])
def get_config():
    """Get chatbot configuration"""
    return jsonify({
        'name': 'Nijenhuis Customer Support Chatbot',
        'version': '1.0.0',
        'features': [
            'multilingual_support',
            'website_analysis',
            'real_time_chat',
            'responsive_design'
        ],
        'supported_languages': ['en', 'es', 'fr', 'de', 'it'],
        'max_message_length': 500
    })

if __name__ == '__main__':
    print("🤖 Starting Nijenhuis Chatbot API Server...")
    print("📍 API will be available at: http://localhost:5000")
    print("🔗 Main endpoint: http://localhost:5000/api/chat")
    print("🏥 Health check: http://localhost:5000/api/health")
    print("🌍 Languages: http://localhost:5000/api/languages")
    print("\n📝 To integrate with your website:")
    print("1. Update the API endpoint in your widget to: http://localhost:5000/api/chat")
    print("2. Customize the website content in this file for your actual content")
    print("3. Deploy this API to your server for production use")
    print("\n🚀 Press Ctrl+C to stop the server")
    
    app.run(debug=True, host='0.0.0.0', port=5000) 