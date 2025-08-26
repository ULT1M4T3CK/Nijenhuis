#!/usr/bin/env python3
"""
Simple Chatbot API Server for Nijenhuis Website
Lightweight Flask API with integrated chatbot functionality
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import json
import os
import sys

# Add the current directory to the path to import the chatbot
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

try:
    from backend.chatbot.core.simple_chatbot import SimpleChatbot
    from backend.chatbot.core.enhanced_chatbot import EnhancedChatbot
except ImportError as e:
    print(f"Error: Required modules not found. {e}")
    print("Please ensure simple_chatbot_demo.py and enhanced_chatbot.py are in the current directory.")
    sys.exit(1)

app = Flask(__name__)
# Restrict CORS to known origins
allowed_origins = [
    'https://nijenhuis-botenverhuur.com',
    'http://nijenhuis-botenverhuur.com',
    'http://85.215.195.147',
    'http://localhost:3000',
    'http://localhost:5000',
    'http://localhost:5173',
    'http://127.0.0.1:3000',
    'http://127.0.0.1:5000',
]
CORS(app, resources={r"/api/*": {"origins": allowed_origins}})

# Ensure relative imports work when running directly
import sys, os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

# Initialize enhanced chatbot (falls back to simple chatbot if training data not available)
try:
    chatbot = EnhancedChatbot()
    print("✅ Enhanced chatbot loaded with training data support")
except Exception as e:
    print(f"⚠️  Enhanced chatbot failed to load: {e}")
    print("🔄 Falling back to simple chatbot")
    chatbot = SimpleChatbot()

# Import boat translation function
from backend.chatbot.core.boat_translations import translate_boat_names

# Nijenhuis website content for the chatbot
NIJENHUIS_WEBSITE_CONTENT = """
Nijenhuis Botenverhuur - Veneweg 199, 7946 LP Wanneperveen

Welkom bij Nijenhuis Botenverhuur in het prachtige Weerribben-Wieden gebied.

Onze diensten:
- Botenverhuur (elektrische boten, zeilboten, kano's, kajaks, sup boards)
- Vakantiehuis verhuur
- Camping met permanente plaatsen
- Jachthaven met ligplaatsen
- Vaarkaart en route-informatie

Openingstijden: Dagelijks 09:00-18:00 (1 april - 1 november)
Telefoon: 0522 281 528

Prijzen (dagprijzen):
- Tender 720: €230 (10-12 personen)
- Tender 570: €200 (8 personen)
- Electrosloep 10: €200 (10 personen)
- Electrosloep 8: €175 (8 personen)
- Zeilboot: €70-85 (4-5 personen)
- Kano/Kajak: €25 (2 personen)
- Sup Board: €35 (1 persoon)

FAQ:
Q: Hoe kan ik reserveren?
A: U kunt reserveren via telefoon (0522 281 528) of door langs te komen.

Q: Wat zijn de openingstijden?
A: We zijn dagelijks open van 09:00 tot 18:00 van 1 april tot 1 november.

Q: Heeft u elektrische boten?
A: Ja, we hebben verschillende elektrische boten beschikbaar voor verhuur.

Q: Kan ik een kano huren?
A: Ja, we verhuren kano's en kajaks voor €25 per dag.

Q: Wat zijn de prijzen van jullie boten?
A: Onze boten kosten tussen €25 en €230 per dag, afhankelijk van het type en aantal personen.

Q: Hebben jullie een camping?
A: Ja, we hebben een camping met permanente plaatsen beschikbaar.

Q: Bieden jullie jachthaven diensten?
A: Ja, we hebben ligplaatsen beschikbaar in onze jachthaven.

Contact informatie:
Email: info@nijenhuis-botenverhuur.nl
Telefoon: 0522 281 528
Adres: Veneweg 199, 7946 LP Wanneperveen
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
        
        # Translate boat names in the response based on detected language
        translated_response = translate_boat_names(result['response'], result['detected_language'])
        
        response_data = {
            'response': translated_response,
            'response_type': result['response_type'],
            'success': True
        }
        
        # Add training information if available (but don't expose language detection)
        if result.get('training_improved'):
            response_data['training_improved'] = True
        
        return jsonify(response_data)
        
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
        'version': '2.0.0',
        'features': {
            'simple_chatbot': True,
            'multilingual_support': True,
            'website_analysis': True,
            'faq_integration': True
        },
        'supported_languages': ['nl', 'en', 'de']
    })

@app.route('/api/languages', methods=['GET'])
def get_languages():
    """Get supported languages"""
    return jsonify({
        'languages': {
            'nl': 'Nederlands',
            'en': 'English',
            'de': 'German'
        },
        'default': 'nl'
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
        'version': '2.0.0',
        'features': [
            'multilingual_support',
            'website_analysis',
            'real_time_chat',
            'responsive_design',
            'faq_integration',
            'contact_information',
            'unsupervised_learning',
            'boat_translations'
        ],
        'supported_languages': ['nl', 'en', 'de'],
        'max_message_length': 500
    })

@app.route('/api/learning/stats', methods=['GET'])
def get_learning_stats():
    """Get unsupervised learning statistics"""
    try:
        from backend.chatbot.core.unsupervised_learning import UnsupervisedLearning
        learning = UnsupervisedLearning()
        stats = learning.get_statistics()
        return jsonify(stats)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/learning/improvements', methods=['GET'])
def get_learning_improvements():
    """Get suggested improvements from unsupervised learning"""
    try:
        from backend.chatbot.core.unsupervised_learning import UnsupervisedLearning
        learning = UnsupervisedLearning()
        improvements = learning.get_suggested_improvements()
        return jsonify({'improvements': improvements})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/neural-network/info', methods=['GET'])
def get_neural_network_info():
    """Get neural network information and architecture"""
    try:
        from backend.chatbot.core.neural_network import NeuralNetwork, ChatbotNeuralNetwork
        
        # Create a sample network to show architecture
        sample_nn = NeuralNetwork([100, 64, 32, 5])
        sample_chatbot_nn = ChatbotNeuralNetwork(input_size=100, hidden_sizes=[64, 32], output_size=5)
        
        info = {
            'architecture': {
                'input_size': 100,
                'hidden_layers': [64, 32],
                'output_size': 5,
                'total_parameters': sum(w.size + b.size for w, b in zip(sample_nn.weights, sample_nn.biases))
            },
            'activation_functions': {
                'hidden_layers': 'ReLU',
                'output_layer': 'Softmax'
            },
            'features': {
                'text_preprocessing': True,
                'response_type_classification': True,
                'confidence_scoring': True,
                'multilingual_support': True
            },
            'training': {
                'learning_rate': 0.001,
                'batch_size': 32,
                'epochs': 50
            }
        }
        
        return jsonify(info)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    print("🤖 Starting Nijenhuis Chatbot API Server...")
    print("📍 API will be available at: http://localhost:5001")
    print("🔗 Main endpoint: http://localhost:5001/api/chat")
    print("🏥 Health check: http://localhost:5001/api/health")
    print("🌍 Languages: http://localhost:5001/api/languages")
    print("📊 Website analysis: http://localhost:5001/api/website/analyze")
    print("⚙️  Config: http://localhost:5001/api/config")
    print("🧠 Learning stats: http://localhost:5001/api/learning/stats")
    print("🚀 Learning improvements: http://localhost:5001/api/learning/improvements")
    print("🧠 Neural network info: http://localhost:5001/api/neural-network/info")
    print("\n✅ Simple chatbot loaded successfully")
    print("🌍 Multilingual support enabled (3 languages: Dutch, English, German)")
    print("📝 FAQ integration active")
    print("📞 Contact information integrated")
    print("\n🚀 Press Ctrl+C to stop the server")
    
    app.run(debug=True, host='0.0.0.0', port=5001) 