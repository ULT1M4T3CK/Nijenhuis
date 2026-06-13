#!/usr/bin/env python3
"""
Secure Chatbot API Server for Nijenhuis Website
Enhanced Flask API with comprehensive security, authentication, and monitoring
"""

from flask import Flask, request, jsonify, g
from flask_cors import CORS
import json
import logging
import os
import sys
import time
import hashlib
import hmac
import re
import html
from functools import wraps
from datetime import datetime, timedelta
import jwt

logger = logging.getLogger('nijenhuis.chatbot.api')
if not logger.handlers:
    logging.basicConfig(level=logging.INFO)

# Add the current directory to the path to import the chatbot
current_dir = os.path.dirname(os.path.abspath(__file__))
project_root = os.path.join(current_dir, '..', '..', '..')
sys.path.append(project_root)
sys.path.append(current_dir)

try:
    # Use consolidated chatbot (backward compatible)
    from backend.chatbot.core.chatbot import Chatbot as EnhancedChatbot
    from backend.chatbot.core.security_manager import get_security_manager
    from backend.chatbot.core.connection_monitor import get_connection_monitor, start_connection_monitoring
except ImportError as e:
    print(f"Error: Required modules not found. {e}")
    print("Please ensure all required modules are available.")
    sys.exit(1)

app = Flask(__name__)

# Security configuration
flask_secret_key = os.environ.get('FLASK_SECRET_KEY')
if not flask_secret_key:
    print("ERROR: FLASK_SECRET_KEY environment variable not set.")
    print("WARNING: Generating temporary secret for development. This is insecure for production.")
    print("Please set FLASK_SECRET_KEY environment variable.")
    flask_secret_key = os.urandom(32).hex()
app.config['SECRET_KEY'] = flask_secret_key
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16MB max request size

# Trust proxy headers (for nginx reverse proxy)
# This allows Flask to correctly detect HTTPS and client IPs when behind nginx
app.config['PREFERRED_URL_SCHEME'] = 'https'
if os.environ.get('TRUST_PROXY', 'false').lower() in ('true', '1', 'yes'):
    from werkzeug.middleware.proxy_fix import ProxyFix
    app.wsgi_app = ProxyFix(
        app.wsgi_app,
        x_for=1,          # Trust X-Forwarded-For header
        x_proto=1,        # Trust X-Forwarded-Proto header
        x_host=1,         # Trust X-Forwarded-Host header
        x_port=1,         # Trust X-Forwarded-Port header
        x_prefix=1        # Trust X-Forwarded-Prefix header
    )

# Emoji removal function - strips all emojis from chatbot responses
def remove_emojis(text: str) -> str:
    """Remove all emojis and emoji-like characters from text"""
    if not text:
        return text
    
    # Comprehensive emoji pattern covering all Unicode emoji ranges
    emoji_pattern = re.compile(
        "["
        "\U0001F600-\U0001F64F"  # Emoticons
        "\U0001F300-\U0001F5FF"  # Symbols & pictographs
        "\U0001F680-\U0001F6FF"  # Transport & map symbols
        "\U0001F1E0-\U0001F1FF"  # Flags
        "\U00002702-\U000027B0"  # Dingbats
        "\U000024C2-\U0001F251"  # Enclosed characters
        "\U0001F900-\U0001F9FF"  # Supplemental Symbols and Pictographs
        "\U0001FA00-\U0001FA6F"  # Chess Symbols
        "\U0001FA70-\U0001FAFF"  # Symbols and Pictographs Extended-A
        "\U00002600-\U000026FF"  # Miscellaneous Symbols
        "\U00002700-\U000027BF"  # Dingbats
        "\U0000FE00-\U0000FE0F"  # Variation Selectors
        "\U0001F000-\U0001F02F"  # Mahjong Tiles
        "\U0001F0A0-\U0001F0FF"  # Playing Cards
        "]+", 
        flags=re.UNICODE
    )
    
    # Remove emojis and clean up any double spaces left behind
    cleaned = emoji_pattern.sub('', text)
    cleaned = re.sub(r'\s+', ' ', cleaned).strip()
    
    return cleaned

# Security headers middleware
@app.after_request
def set_security_headers(response):
    """Add security headers to all responses"""
    # Content Security Policy - restrict resource loading to prevent XSS
    is_production = os.environ.get('FLASK_ENV', '').lower() == 'production'
    connect_src_dev = "connect-src 'self' https://nijenhuis-botenverhuur.com http://localhost:*; "
    connect_src_prod = "connect-src 'self' https://nijenhuis-botenverhuur.com; "
    connect_src = connect_src_prod if is_production else connect_src_dev
    csp_policy = (
        "default-src 'self'; "
        "script-src 'self' 'unsafe-inline'; "
        "style-src 'self' 'unsafe-inline'; "
        "img-src 'self' data: https:; "
        "font-src 'self' data:; " +
        connect_src +
        "frame-ancestors 'none'; "
        "base-uri 'self'; "
        "form-action 'self'; "
        "object-src 'none'; "
        "media-src 'none'; "
        "worker-src 'none'; "
        "manifest-src 'self';"
    )
    response.headers['Content-Security-Policy'] = csp_policy
    
    # Other security headers
    response.headers['X-Content-Type-Options'] = 'nosniff'
    response.headers['X-Frame-Options'] = 'DENY'
    response.headers['X-XSS-Protection'] = '1; mode=block'
    response.headers['Referrer-Policy'] = 'strict-origin-when-cross-origin'
    response.headers['Permissions-Policy'] = 'geolocation=(), microphone=(), camera=(), payment=()'
    
    # Additional security headers
    response.headers['X-Permitted-Cross-Domain-Policies'] = 'none'
    response.headers['Cross-Origin-Embedder-Policy'] = 'require-corp'
    response.headers['Cross-Origin-Opener-Policy'] = 'same-origin'
    response.headers['Cross-Origin-Resource-Policy'] = 'same-origin'
    
    # HSTS (only if HTTPS is confirmed)
    if request.is_secure or request.headers.get('X-Forwarded-Proto') == 'https':
        response.headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains; preload'
    
    return response

# Restrict CORS to known origins with enhanced security
# Prioritize HTTPS origins in production
allowed_origins = [
    'https://nijenhuis-botenverhuur.com',
    'https://www.nijenhuis-botenverhuur.com',
    'http://nijenhuis-botenverhuur.com',  # Fallback for initial setup
    'http://www.nijenhuis-botenverhuur.com',
    'http://85.215.195.147',
    'http://localhost',  # Apache on port 80
    'http://127.0.0.1',  # Apache on port 80
    'http://localhost:8888',  # PHP server (frontend pages)
    'http://localhost:5000',
    'http://localhost:5001',
    'http://localhost:8000',
    'http://127.0.0.1:8888',  # PHP server (frontend pages)
    'http://127.0.0.1:5000',
    'http://127.0.0.1:5001',
    'http://127.0.0.1:8000',
    # Note: 'null' origin removed for security - file:// protocol should use localhost server
]

CORS(app, resources={
    r"/api/*": {
        "origins": allowed_origins,
        "methods": ["GET", "POST", "OPTIONS"],
        "allow_headers": [
            "Content-Type", 
            "Authorization", 
            "X-API-Key",
            "X-Request-ID",
            "X-Client-Version"
        ],
        "supports_credentials": True
    }
})

# Initialize security and monitoring
security_manager = get_security_manager()
connection_monitor = get_connection_monitor()

# Input sanitization functions
def sanitize_text(text: str, max_length: int = 1000) -> str:
    """
    Sanitize user input text to prevent XSS and injection attacks
    
    Args:
        text: Input text to sanitize
        max_length: Maximum allowed length
        
    Returns:
        Sanitized text
    """
    if not isinstance(text, str):
        return ''
    
    # Strip whitespace
    text = text.strip()
    
    # Enforce length limit
    if len(text) > max_length:
        text = text[:max_length]
    
    # Remove control characters (except newlines and tabs)
    text = re.sub(r'[\x00-\x08\x0B-\x0C\x0E-\x1F\x7F]', '', text)
    
    # HTML escape to prevent XSS
    text = html.escape(text)
    
    # Validate UTF-8 encoding
    try:
        text.encode('utf-8').decode('utf-8')
    except UnicodeDecodeError:
        # If encoding fails, return empty string
        return ''
    
    return text

def sanitize_conversation_history(history: list, max_messages: int = 50, max_message_length: int = 1000) -> list:
    """
    Sanitize conversation history array
    
    Args:
        history: List of message dictionaries
        max_messages: Maximum number of messages to keep
        max_message_length: Maximum length per message
        
    Returns:
        Sanitized conversation history
    """
    if not isinstance(history, list):
        return []
    
    # Limit number of messages
    if len(history) > max_messages:
        history = history[-max_messages:]  # Keep most recent messages
    
    sanitized = []
    for msg in history:
        if not isinstance(msg, dict):
            continue
        
        role = msg.get('role', '')
        content = msg.get('content', '')
        
        # Validate role
        if role not in ['user', 'assistant']:
            continue
        
        # Sanitize content
        sanitized_content = sanitize_text(str(content), max_message_length)
        
        if sanitized_content:  # Only add non-empty messages
            sanitized.append({
                'role': role,
                'content': sanitized_content
            })
    
    return sanitized

# Note: sys and os are already imported at the top of the file

# Initialize enhanced chatbot - optimized for fast responses
try:
    import time as init_time_module
    chatbot_init_start = init_time_module.time()
    
    # PERFORMANCE: Initialize with advanced_nlp=False for sub-2s responses
    chatbot = EnhancedChatbot(use_advanced_nlp=False)
    
    chatbot_init_time = init_time_module.time() - chatbot_init_start
    print(f"✅ Enhanced chatbot loaded in {chatbot_init_time:.2f}s (fast mode)")
except Exception as e:
    print(f"❌ Enhanced chatbot failed to load: {e}")
    print("Please check the training data and dependencies.")
    sys.exit(1)

# Import boat translation function
from backend.chatbot.core.boat_translations import translate_boat_names

# Security decorators
def require_api_key(permission='chat'):
    """Decorator to require authentication via JWT Bearer token or legacy API key"""
    def decorator(f):
        @wraps(f)
        def decorated_function(*args, **kwargs):
            auth_header = request.headers.get('Authorization', '')
            bearer_token = None
            if auth_header.startswith('Bearer '):
                bearer_token = auth_header.replace('Bearer ', '').strip()
            
            client_ip = request.environ.get('HTTP_X_FORWARDED_FOR', request.remote_addr)
            
            # Prefer JWT Bearer token when present
            if bearer_token:
                payload = security_manager.verify_jwt_token(bearer_token)
                if not payload:
                    return jsonify({'error': 'Invalid or expired token', 'success': False}), 401
                # Check permission
                perms = payload.get('permissions', [])
                if permission not in perms:
                    return jsonify({'error': f'Insufficient permissions. Required: {permission}', 'success': False}), 403
                
                # Audience must match the request Origin exactly. The previous
                # substring check let a token bound to "https://example.com"
                # be replayed against "https://example.com.attacker.tld" or
                # any Referer that merely contained the audience string.
                req_origin = request.headers.get('Origin') or ''
                token_aud = payload.get('aud')
                token_ip = payload.get('ip')
                if token_aud and token_aud != 'public':
                    if not req_origin or req_origin.rstrip('/') != str(token_aud).rstrip('/'):
                        return jsonify({'error': 'Token audience mismatch', 'success': False}), 401
                if token_ip and token_ip != client_ip:
                    return jsonify({'error': 'Token IP mismatch', 'success': False}), 401
                
                # Rate limit by IP + token subject
                identifier = f"{client_ip}:token:{payload.get('sub','')}"
                is_allowed, rate_message = security_manager.check_rate_limit(identifier)
                if not is_allowed:
                    return jsonify({'error': rate_message, 'success': False}), 429
                
                g.client_ip = client_ip
                g.identifier = identifier
                g.auth = {'type': 'token', 'sub': payload.get('sub')}
                return f(*args, **kwargs)
            
            # Fallback to legacy API key header
            api_key = request.headers.get('X-API-Key')
            if not api_key:
                return jsonify({'error': 'Authentication required', 'success': False}), 401
            
            is_authenticated, message = security_manager.authenticate_request(api_key, permission)
            if not is_authenticated:
                return jsonify({'error': message, 'success': False}), 401
            
            identifier = f"{client_ip}:{api_key[:10]}"
            is_allowed, rate_message = security_manager.check_rate_limit(identifier, api_key)
            if not is_allowed:
                return jsonify({'error': rate_message, 'success': False}), 429
            
            # Check IP blocking
            is_ip_allowed, ip_message = security_manager.check_ip_blocking(client_ip)
            if not is_ip_allowed:
                return jsonify({'error': ip_message, 'success': False}), 403
            
            g.api_key = api_key
            g.client_ip = client_ip
            g.identifier = identifier
            g.auth = {'type': 'api_key'}
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

# Load website content extractor
try:
    from backend.chatbot.core.website_content_extractor import WebsiteContentExtractor, get_website_content
    # Extract content from actual HTML pages
    print("📄 Extracting content from website pages...")
    extractor = WebsiteContentExtractor()
    NIJENHUIS_WEBSITE_CONTENT = extractor.extract_all_content()
    
    if NIJENHUIS_WEBSITE_CONTENT:
        print(f"✅ Loaded {len(NIJENHUIS_WEBSITE_CONTENT)} characters of website content")
        # Limit content size to avoid memory issues (keep first 50000 chars)
        if len(NIJENHUIS_WEBSITE_CONTENT) > 50000:
            NIJENHUIS_WEBSITE_CONTENT = NIJENHUIS_WEBSITE_CONTENT[:50000]
            print("⚠️ Content truncated to 50000 characters")
    else:
        print("⚠️ No website content extracted, using fallback")
        # Fallback to basic content if extraction fails
        NIJENHUIS_WEBSITE_CONTENT = """
Nijenhuis Botenverhuur - Veneweg 199, 7946 LP Wanneperveen

Welkom bij Nijenhuis Botenverhuur in het prachtige Weerribben-Wieden gebied.

Onze diensten:
- Botenverhuur (elektrische boten, zeilboten, kano's, kajaks, sup boards)
- Vakantiehuis verhuur
- Camping met permanente plaatsen
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

Contact informatie:
Email: info@nijenhuis-botenverhuur.nl
Telefoon: 0522 281 528
Adres: Veneweg 199, 7946 LP Wanneperveen
"""
except Exception as e:
    print(f"⚠️ Error loading website content extractor: {e}")
    print("Using fallback content")
    # Fallback content
    NIJENHUIS_WEBSITE_CONTENT = """
Nijenhuis Botenverhuur - Veneweg 199, 7946 LP Wanneperveen

Welkom bij Nijenhuis Botenverhuur in het prachtige Weerribben-Wieden gebied.

Onze diensten:
- Botenverhuur (elektrische boten, zeilboten, kano's, kajaks, sup boards)
- Vakantiehuis verhuur
- Camping met permanente plaatsen
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
    """Main chat API endpoint with enhanced security and conversation context"""
    try:
        data = request.get_json()
        user_message = data.get('message', '')
        conversation_history = data.get('conversation_history', None)  # List of messages with 'role' and 'content'
        session_id = data.get('session_id', None)  # Optional session ID for context tracking
        use_token_prediction = data.get('use_token_prediction', False)  # Disabled by default (slow on CPU)
        
        if not user_message:
            return jsonify({'error': 'No message provided'}), 400
        
        # Validate message length and content
        if len(user_message) > 1000:
            return jsonify({'error': 'Message too long. Maximum 1000 characters.'}), 400
        
        # Validate and sanitize conversation history if provided
        if conversation_history is not None:
            if not isinstance(conversation_history, list):
                return jsonify({'error': 'conversation_history must be a list'}), 400
            
            # Validate size limit
            if len(conversation_history) > 50:
                return jsonify({'error': 'conversation_history exceeds maximum of 50 messages'}), 400
            
            # Sanitize conversation history
            conversation_history = sanitize_conversation_history(conversation_history)
        
        # Sanitize user message
        user_message = sanitize_text(user_message, max_length=1000)
        
        if not user_message:
            return jsonify({'error': 'Message cannot be empty after sanitization'}), 400
        
        # Validate session ID format to prevent path traversal
        if session_id:
            # Basic validation: check for path traversal characters
            if '..' in session_id or '/' in session_id or '\\' in session_id:
                return jsonify({'error': 'Invalid session ID format'}), 400
            # Additional validation: check length and characters
            if len(session_id) > 128 or not re.match(r'^[a-zA-Z0-9_.-]+$', session_id):
                return jsonify({'error': 'Invalid session ID format'}), 400
        
        # Process the message with chatbot (with full conversation context)
        # PERFORMANCE: Token prediction disabled by default for sub-2s responses
        query_start = time.time()
        result = chatbot.process_query(
            query=user_message,
            website_content=NIJENHUIS_WEBSITE_CONTENT,
            conversation_history=conversation_history,
            session_id=session_id,
            use_token_prediction=use_token_prediction  # False by default
        )
        query_time = time.time() - query_start
        
        # Log response time for monitoring (target: <2s)
        if query_time > 2.0:
            print(f"⚠️ Slow response: {query_time:.2f}s for query: {user_message[:50]}...")
        
        # Translate boat names in the response based on detected language
        translated_response = translate_boat_names(result['response'], result['detected_language'])
        
        # Remove emojis from the response for a clean, professional look
        clean_response = remove_emojis(translated_response)
        
        response_data = {
            'response': clean_response,
            'response_type': result['response_type'],
            'success': True,
            'timestamp': datetime.now().isoformat(),
            'connection_status': connection_monitor.get_connection_status()['status'],
            'processing_time_ms': round(query_time * 1000, 1)  # Response time in milliseconds
        }
        
        # Add session ID if provided (or create new one)
        if session_id:
            response_data['session_id'] = session_id
        elif hasattr(chatbot, 'context_manager') and chatbot.context_manager:
            # Return the session ID that was created/used
            if conversation_history:
                # Get the most recent context
                stats = chatbot.context_manager.get_statistics()
                if stats['total_sessions'] > 0:
                    # Find the session that matches (simplified - in production, track this better)
                    pass
        
        # Add context-aware information
        if result.get('context_aware'):
            response_data['context_aware'] = True
        
        # Add token prediction information
        if result.get('token_prediction_used'):
            response_data['token_prediction_used'] = True
            response_data['confidence'] = result.get('confidence', 0.5)
        
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
            'chatbot': True,
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

@app.route('/api/reload-training', methods=['POST'])
@log_request()
def reload_training_data():
    """
    Reload training data from files without restarting the service.
    Useful after adding new corrections to the training data files.
    """
    try:
        # Reload the knowledge base training data
        if chatbot and hasattr(chatbot, 'knowledge_base') and chatbot.knowledge_base:
            chatbot.knowledge_base.reload_trained_responses()
            count = len(chatbot.knowledge_base.trained_responses)
            return jsonify({
                'success': True,
                'message': f'Training data reloaded successfully',
                'trained_responses': count,
                'timestamp': datetime.now().isoformat()
            })
        else:
            return jsonify({
                'success': False,
                'message': 'Knowledge base not available'
            }), 500
    except Exception as e:
        print(f"Error reloading training data: {str(e)}")
        return jsonify({
            'success': False,
            'message': f'Error reloading training data: {str(e)}'
        }), 500

@app.route('/api/training-stats', methods=['GET'])
@log_request()
def get_training_stats():
    """Get statistics about loaded training data"""
    try:
        stats = {
            'trained_responses': 0,
            'sources': []
        }
        
        if chatbot and hasattr(chatbot, 'knowledge_base') and chatbot.knowledge_base:
            stats['trained_responses'] = len(chatbot.knowledge_base.trained_responses)
            stats['sources'] = ['enhanced_training_data.json']
        
        return jsonify({
            'success': True,
            'stats': stats,
            'timestamp': datetime.now().isoformat()
        })
    except Exception as e:
        return jsonify({
            'success': False,
            'message': str(e)
        }), 500

@app.route('/api/train', methods=['POST'])
@require_api_key('train')
@log_request()
def submit_training_correction():
    """
    Submit a corrected response from external training platform (VYBR1S).
    This endpoint saves corrections to the training data file and reloads the knowledge base.
    
    Expected JSON body:
    {
        "question": "Original user question",
        "original_response": "What the chatbot originally responded",
        "corrected_response": "The corrected/improved response",
        "language": "nl|en|de" (optional, auto-detected if not provided),
        "response_type": "pricing|opening_hours|location|general" (optional)
    }
    """
    try:
        data = request.get_json()
        
        if not data:
            return jsonify({'success': False, 'message': 'No data provided'}), 400
        
        question = data.get('question', '').strip()
        original_response = data.get('original_response', '').strip()
        corrected_response = data.get('corrected_response', '').strip()
        language = data.get('language', 'nl')
        response_type = data.get('response_type', 'general')
        
        if not question or not corrected_response:
            return jsonify({
                'success': False, 
                'message': 'Both "question" and "corrected_response" are required'
            }), 400
        
        # Load existing training data (correct path with 'data' subdirectory)
        training_file = os.path.join(
            os.path.dirname(__file__), '..', 'training', 'data', 'enhanced_training_data.json'
        )
        
        try:
            with open(training_file, 'r', encoding='utf-8') as f:
                training_data = json.load(f)
        except FileNotFoundError:
            training_data = {
                "metadata": {
                    "created": datetime.now().isoformat(),
                    "version": "2.1",
                    "enhanced": True
                },
                "training_sessions": [],
                "training_data": {
                    "improved_responses": {}
                }
            }
        
        # Add the new correction
        new_session = {
            "question": question,
            "original_response": original_response,
            "corrected_response": corrected_response,
            "detected_language": language,
            "response_type": response_type,
            "timestamp": datetime.now().isoformat(),
            "status": "Corrected",
            "source": "VYBR1S"
        }
        
        # Add to training_sessions at root level (this is what knowledge_base reads)
        if 'training_sessions' not in training_data:
            training_data['training_sessions'] = []
        
        training_data['training_sessions'].append(new_session)
        
        # Update metadata
        training_data['metadata'] = training_data.get('metadata', {})
        training_data['metadata']['last_updated'] = datetime.now().isoformat()
        training_data['metadata']['last_source'] = 'VYBR1S'
        
        # Save updated training data
        with open(training_file, 'w', encoding='utf-8') as f:
            json.dump(training_data, f, indent=2, ensure_ascii=False)
        
        # Reload the knowledge base to use the new correction immediately
        if chatbot and hasattr(chatbot, 'knowledge_base') and chatbot.knowledge_base:
            chatbot.knowledge_base.reload_trained_responses()
            trained_count = len(chatbot.knowledge_base.trained_responses)
        else:
            trained_count = 0
        
        return jsonify({
            'success': True,
            'message': 'Correction saved and knowledge base reloaded',
            'question': question,
            'trained_responses_count': trained_count,
            'timestamp': datetime.now().isoformat()
        })
        
    except Exception as e:
        print(f"Error saving training correction: {str(e)}")
        return jsonify({
            'success': False,
            'message': f'Error saving correction: {str(e)}'
        }), 500

@app.route('/api/train/batch', methods=['POST'])
@require_api_key('train')
@log_request()
def submit_training_batch():
    """
    Submit multiple corrections at once from external training platform (VYBR1S).
    
    Expected JSON body:
    {
        "corrections": [
            {
                "question": "...",
                "original_response": "...",
                "corrected_response": "...",
                "language": "nl",
                "response_type": "pricing"
            },
            ...
        ]
    }
    """
    try:
        data = request.get_json()
        
        if not data or 'corrections' not in data:
            return jsonify({'success': False, 'message': 'No corrections provided'}), 400
        
        corrections = data.get('corrections', [])
        if not isinstance(corrections, list) or len(corrections) == 0:
            return jsonify({'success': False, 'message': 'corrections must be a non-empty array'}), 400
        
        # Load existing training data (correct path with 'data' subdirectory)
        training_file = os.path.join(
            os.path.dirname(__file__), '..', 'training', 'data', 'enhanced_training_data.json'
        )
        
        try:
            with open(training_file, 'r', encoding='utf-8') as f:
                training_data = json.load(f)
        except FileNotFoundError:
            training_data = {
                "metadata": {"created": datetime.now().isoformat(), "version": "2.1", "enhanced": True},
                "training_sessions": []
            }
        
        # Ensure training_sessions exists at root level
        if 'training_sessions' not in training_data:
            training_data['training_sessions'] = []
        
        added_count = 0
        for correction in corrections:
            question = correction.get('question', '').strip()
            corrected_response = correction.get('corrected_response', '').strip()
            
            if question and corrected_response:
                new_session = {
                    "question": question,
                    "original_response": correction.get('original_response', ''),
                    "corrected_response": corrected_response,
                    "detected_language": correction.get('language', 'nl'),
                    "response_type": correction.get('response_type', 'general'),
                    "timestamp": datetime.now().isoformat(),
                    "status": "Corrected",
                    "source": "VYBR1S"
                }
                training_data['training_sessions'].append(new_session)
                added_count += 1
        
        # Update metadata
        training_data['metadata'] = training_data.get('metadata', {})
        training_data['metadata']['last_updated'] = datetime.now().isoformat()
        training_data['metadata']['last_source'] = 'VYBR1S'
        training_data['metadata']['batch_import_count'] = added_count
        
        # Save updated training data
        with open(training_file, 'w', encoding='utf-8') as f:
            json.dump(training_data, f, indent=2, ensure_ascii=False)
        
        # Reload the knowledge base
        if chatbot and hasattr(chatbot, 'knowledge_base') and chatbot.knowledge_base:
            chatbot.knowledge_base.reload_trained_responses()
            trained_count = len(chatbot.knowledge_base.trained_responses)
        else:
            trained_count = 0
        
        return jsonify({
            'success': True,
            'message': f'Added {added_count} corrections and reloaded knowledge base',
            'added_count': added_count,
            'trained_responses_count': trained_count,
            'timestamp': datetime.now().isoformat()
        })
        
    except Exception as e:
        print(f"Error saving training batch: {str(e)}")
        return jsonify({
            'success': False,
            'message': f'Error saving batch: {str(e)}'
        }), 500

@app.route('/api/token', methods=['GET'])
@log_request()
def issue_token():
    """
    Issue a short-lived JWT to clients without exposing an API key.
    Ties the token to origin (aud) and client IP when available.
    """
    try:
        now = datetime.utcnow()
        client_ip = request.environ.get('HTTP_X_FORWARDED_FOR', request.remote_addr)
        origin = request.headers.get('Origin') or ''

        # Every token must be bound to a specific, allowed Origin. Without
        # this, a missing Origin was mapped to the synthetic audience
        # "public" and the aud check became a no-op, letting non-browser
        # clients obtain long-lived chat tokens.
        if not origin:
            return jsonify({'error': 'Origin header required', 'success': False}), 400
        if origin not in allowed_origins:
            return jsonify({'error': 'Origin not allowed', 'success': False}), 403

        # Per-IP rate limit on token issuance to stop bulk minting.
        is_allowed, rate_message = security_manager.check_rate_limit(f"token_issue:{client_ip}")
        if not is_allowed:
            return jsonify({'error': rate_message, 'success': False}), 429

        payload = {
            'sub': 'chat_client',
            'permissions': ['chat'],
            'ip': client_ip,
            'aud': origin,
            'iat': int(now.timestamp()),
            'exp': int((now + timedelta(hours=2)).timestamp())  # 2-hour expiry
        }
        token = jwt.encode(payload, security_manager.jwt_secret, algorithm='HS256')
        
        return jsonify({
            'success': True,
            'token': token,
            'expires_in': 2 * 60 * 60
        })
    except Exception as e:
        print(f"Error issuing token: {e}")
        return jsonify({'error': 'Unable to issue token', 'success': False}), 500

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
        logger.exception('create_api_key failed')
        return jsonify({
            'success': False,
            'error': 'Internal server error'
        }), 500

@app.route('/api/cache/clear', methods=['POST'])
@require_api_key('config')
@log_request()
def clear_cache():
    """Clear the knowledge base response cache"""
    try:
        if chatbot and hasattr(chatbot, 'knowledge_base') and chatbot.knowledge_base:
            chatbot.knowledge_base._response_cache = {}
            chatbot.knowledge_base._cache_order = []
            return jsonify({
                'success': True,
                'message': 'Cache cleared successfully'
            })
        return jsonify({
            'success': False,
            'message': 'Knowledge base not available'
        }), 500
    except Exception as e:
        logger.exception('clear_cache failed')
        return jsonify({
            'success': False,
            'error': 'Internal server error'
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
        logger.exception('analyze_website failed')
        return jsonify({
            'error': 'Internal server error',
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
        logger.exception('get_learning_stats failed')
        return jsonify({'error': 'Internal server error'}), 500

@app.route('/api/learning/improvements', methods=['GET'])
def get_learning_improvements():
    """Get suggested improvements from unsupervised learning"""
    try:
        from backend.chatbot.core.unsupervised_learning import UnsupervisedLearning
        learning = UnsupervisedLearning()
        improvements = learning.get_suggested_improvements()
        return jsonify({'improvements': improvements})
    except Exception as e:
        logger.exception('get_learning_improvements failed')
        return jsonify({'error': 'Internal server error'}), 500

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
        logger.exception('get_neural_network_info failed')
        return jsonify({'error': 'Internal server error'}), 500

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
    
    # Determine debug mode from environment variables
    # Debug mode is disabled by default in production
    app_env = os.environ.get('APP_ENV', 'production').lower()
    app_debug = os.environ.get('APP_DEBUG', 'false').lower()
    
    # Enable debug only if explicitly set to development AND debug is enabled
    debug_mode = app_env == 'development' and app_debug in ('true', '1', 'yes')
    
    if debug_mode:
        print("⚠️  DEBUG MODE ENABLED - Not suitable for production!")
    else:
        print("✅ Production mode - Debug disabled")
    
    try:
        app.run(debug=debug_mode, host='0.0.0.0', port=5001)
    except KeyboardInterrupt:
        print("\n👋 Shutting down server...")
        connection_monitor.stop_monitoring()
        print("✅ Server stopped gracefully") 