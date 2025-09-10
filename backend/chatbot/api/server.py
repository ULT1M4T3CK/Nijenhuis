#!/usr/bin/env python3
"""
Secure Chatbot API Server for Nijenhuis Website
Enhanced Flask API with comprehensive security, authentication, and monitoring
"""

from flask import Flask, request, jsonify, g
from flask_cors import CORS
import json
import os
import sys
import time
import hashlib
import hmac
from functools import wraps
from datetime import datetime

# Add the current directory to the path to import the chatbot
current_dir = os.path.dirname(os.path.abspath(__file__))
project_root = os.path.join(current_dir, '..', '..', '..')
sys.path.append(project_root)
sys.path.append(current_dir)

try:
    from backend.chatbot.core.enhanced_chatbot import EnhancedChatbot
    from backend.chatbot.core.security_manager import get_security_manager
    from backend.chatbot.core.connection_monitor import get_connection_monitor, start_connection_monitoring
except ImportError as e:
    print(f"Error: Required modules not found. {e}")
    print("Please ensure all required modules are available.")
    sys.exit(1)

app = Flask(__name__)

# Security configuration
app.config['SECRET_KEY'] = os.environ.get('FLASK_SECRET_KEY', 'dev-secret-key-change-in-production')
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16MB max request size

# Restrict CORS to known origins with enhanced security
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

CORS(app, resources={
    r"/api/*": {
        "origins": allowed_origins,
        "methods": ["GET", "POST", "OPTIONS"],
        "allow_headers": ["Content-Type", "Authorization", "X-API-Key"],
        "supports_credentials": True
    }
})

# Initialize security and monitoring
security_manager = get_security_manager()
connection_monitor = get_connection_monitor()

# Ensure relative imports work when running directly
import sys, os
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

# Initialize enhanced chatbot
try:
    chatbot = EnhancedChatbot()
    print("✅ Enhanced chatbot loaded with training data support")
except Exception as e:
    print(f"❌ Enhanced chatbot failed to load: {e}")
    print("Please check the training data and dependencies.")
    sys.exit(1)

# Import boat translation function
from backend.chatbot.core.boat_translations import translate_boat_names

# Security decorators
def require_api_key(permission='chat'):
    """Decorator to require API key authentication"""
    def decorator(f):
        @wraps(f)
        def decorated_function(*args, **kwargs):
            # Get API key from headers
            api_key = request.headers.get('X-API-Key') or request.headers.get('Authorization', '').replace('Bearer ', '')
            
            # Authenticate request
            is_authenticated, message = security_manager.authenticate_request(api_key, permission)
            if not is_authenticated:
                return jsonify({'error': message, 'success': False}), 401
            
            # Check rate limiting
            client_ip = request.environ.get('HTTP_X_FORWARDED_FOR', request.remote_addr)
            identifier = f"{client_ip}:{api_key[:10]}"
            
            is_allowed, rate_message = security_manager.check_rate_limit(identifier, api_key)
            if not is_allowed:
                return jsonify({'error': rate_message, 'success': False}), 429
            
            # Check IP blocking
            is_ip_allowed, ip_message = security_manager.check_ip_blocking(client_ip)
            if not is_ip_allowed:
                return jsonify({'error': ip_message, 'success': False}), 403
            
            # Store authentication info in g for use in route
            g.api_key = api_key
            g.client_ip = client_ip
            g.identifier = identifier
            
            return f(*args, **kwargs)
        return decorated_function
    return decorator

def require_connection_health():
    """Decorator to check connection health before processing"""
    def decorator(f):
        @wraps(f)
        def decorated_function(*args, **kwargs):
            if not connection_monitor.is_connection_healthy():
                # Try to reconnect
                if not connection_monitor.attempt_reconnection():
                    # Return fallback response
                    return jsonify({
                        'response': connection_monitor.get_fallback_response('nl', 'offline'),
                        'response_type': 'fallback',
                        'success': True,
                        'connection_status': 'offline'
                    })
            
            return f(*args, **kwargs)
        return decorated_function
    return decorator

def log_request():
    """Decorator to log API requests"""
    def decorator(f):
        @wraps(f)
        def decorated_function(*args, **kwargs):
            start_time = time.time()
            
            # Log request
            request_data = {
                'endpoint': request.endpoint,
                'method': request.method,
                'ip': getattr(g, 'client_ip', request.remote_addr),
                'user_agent': request.headers.get('User-Agent', ''),
                'timestamp': datetime.now().isoformat()
            }
            
            try:
                result = f(*args, **kwargs)
                response_time = time.time() - start_time
                
                # Update connection health
                connection_monitor.update_connection_health(success=True)
                
                # Log successful request
                print(f"✅ {request.method} {request.endpoint} - {response_time:.3f}s")
                
                return result
                
            except Exception as e:
                response_time = time.time() - start_time
                
                # Update connection health
                connection_monitor.update_connection_health(success=False)
                
                # Log failed request
                print(f"❌ {request.method} {request.endpoint} - {response_time:.3f}s - Error: {str(e)}")
                
                # Return error response
                return jsonify({
                    'error': 'Internal server error',
                    'success': False,
                    'fallback_response': connection_monitor.get_fallback_response('nl', 'error')
                }), 500
                
        return decorated_function
    return decorator

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
@require_api_key('chat')
@require_connection_health()
@log_request()
def chat_api():
    """Main chat API endpoint with enhanced security"""
    try:
        data = request.get_json()
        user_message = data.get('message', '')
        
        if not user_message:
            return jsonify({'error': 'No message provided'}), 400
        
        # Validate message length and content
        if len(user_message) > 1000:
            return jsonify({'error': 'Message too long. Maximum 1000 characters.'}), 400
        
        # Sanitize input (basic XSS prevention)
        user_message = user_message.strip()
        
        # Process the message with chatbot
        result = chatbot.process_query(user_message, NIJENHUIS_WEBSITE_CONTENT)
        
        # Translate boat names in the response based on detected language
        translated_response = translate_boat_names(result['response'], result['detected_language'])
        
        response_data = {
            'response': translated_response,
            'response_type': result['response_type'],
            'success': True,
            'timestamp': datetime.now().isoformat(),
            'connection_status': connection_monitor.get_connection_status()['status']
        }
        
        # Add training information if available (but don't expose language detection)
        if result.get('training_improved'):
            response_data['training_improved'] = True
        
        # Add neural network improvements if available
        if result.get('neural_improved'):
            response_data['neural_improved'] = True
            response_data['neural_confidence'] = result.get('neural_confidence', 0)
        
        return jsonify(response_data)
        
    except Exception as e:
        print(f"Error in chat API: {str(e)}")
        return jsonify({
            'error': 'An error occurred while processing your message',
            'success': False,
            'fallback_response': connection_monitor.get_fallback_response('nl', 'error')
        }), 500

@app.route('/api/health', methods=['GET'])
@log_request()
def health_check():
    """Enhanced health check endpoint with connection monitoring"""
    connection_status = connection_monitor.get_connection_status()
    security_stats = security_manager.get_security_stats()
    
    return jsonify({
        'status': 'healthy' if connection_status['is_healthy'] else 'degraded',
        'service': 'Nijenhuis Chatbot API',
        'version': '3.0.0',
        'timestamp': datetime.now().isoformat(),
        'connection': connection_status,
        'security': {
            'uptime': security_stats['uptime_human'],
            'success_rate': f"{security_stats['success_rate']:.1f}%",
            'active_connections': security_stats['active_connections'],
            'blocked_ips': security_stats['blocked_ips_count']
        },
        'features': {
            'simple_chatbot': True,
            'enhanced_chatbot': True,
            'neural_network': True,
            'multilingual_support': True,
            'website_analysis': True,
            'faq_integration': True,
            'security_monitoring': True,
            'connection_monitoring': True,
            'rate_limiting': True,
            'authentication': True
        },
        'supported_languages': ['nl', 'en', 'de']
    })

@app.route('/api/security/status', methods=['GET'])
@require_api_key('config')
@log_request()
def security_status():
    """Get security status and statistics"""
    return jsonify(security_manager.get_security_stats())

@app.route('/api/security/create-key', methods=['POST'])
@require_api_key('config')
@log_request()
def create_api_key():
    """Create a new API key"""
    try:
        data = request.get_json()
        name = data.get('name', 'New API Key')
        permissions = data.get('permissions', ['chat'])
        rate_limit_override = data.get('rate_limit_override')
        
        api_key = security_manager.create_api_key(name, permissions, rate_limit_override)
        
        return jsonify({
            'success': True,
            'api_key': api_key,
            'name': name,
            'permissions': permissions,
            'message': 'API key created successfully'
        })
        
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500

@app.route('/api/connection/status', methods=['GET'])
@require_api_key('config')
@log_request()
def connection_status():
    """Get connection status and metrics"""
    return jsonify(connection_monitor.get_connection_status())

@app.route('/api/connection/reconnect', methods=['POST'])
@require_api_key('config')
@log_request()
def force_reconnect():
    """Force reconnection attempt"""
    success = connection_monitor.attempt_reconnection()
    
    return jsonify({
        'success': success,
        'status': connection_monitor.get_connection_status()['status'],
        'message': 'Reconnection successful' if success else 'Reconnection failed'
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
    print("🤖 Starting Nijenhuis Secure Chatbot API Server...")
    print("=" * 60)
    print("📍 API will be available at: http://localhost:5001")
    print("🔗 Main endpoint: http://localhost:5001/api/chat")
    print("🏥 Health check: http://localhost:5001/api/health")
    print("🔒 Security status: http://localhost:5001/api/security/status")
    print("📊 Connection status: http://localhost:5001/api/connection/status")
    print("🌍 Languages: http://localhost:5001/api/languages")
    print("📊 Website analysis: http://localhost:5001/api/website/analyze")
    print("⚙️  Config: http://localhost:5001/api/config")
    print("🧠 Learning stats: http://localhost:5001/api/learning/stats")
    print("🚀 Learning improvements: http://localhost:5001/api/learning/improvements")
    print("🧠 Neural network info: http://localhost:5001/api/neural-network/info")
    print("\n🔐 Security Features:")
    print("   ✅ API Key Authentication")
    print("   ✅ Rate Limiting")
    print("   ✅ IP Blocking")
    print("   ✅ Connection Monitoring")
    print("   ✅ Request Logging")
    print("   ✅ Fallback Mechanisms")
    print("\n✅ Enhanced chatbot loaded successfully")
    print("🌍 Multilingual support enabled (3 languages: Dutch, English, German)")
    print("📝 FAQ integration active")
    print("📞 Contact information integrated")
    print("🧠 Neural network active")
    print("🔒 Security monitoring active")
    print("📊 Connection monitoring active")
    
    # Start connection monitoring
    start_connection_monitoring()
    print("🔄 Connection monitoring started")
    
    print("\n🚀 Press Ctrl+C to stop the server")
    print("=" * 60)
    
    try:
        app.run(debug=True, host='0.0.0.0', port=5001)
    except KeyboardInterrupt:
        print("\n👋 Shutting down server...")
        connection_monitor.stop_monitoring()
        print("✅ Server stopped gracefully") 