import os
import logging
from functools import wraps
from flask import Flask, request, jsonify
from flask_cors import CORS
from flask_limiter import Limiter
from flask_limiter.util import get_remote_address
from cerberus import Validator
import requests

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)

# Secure CORS configuration - only allow specific origins
ALLOWED_ORIGINS = os.environ.get('ALLOWED_ORIGINS', 'https://nijenhuis-botenverhuur.com').split(',')
CORS(app, origins=ALLOWED_ORIGINS, methods=['POST'], allow_headers=['Content-Type', 'X-API-Key'])

# Rate limiting configuration
limiter = Limiter(
    app,
    key_func=get_remote_address,
    default_limits=["100 per hour", "20 per minute"]
)

PERPLEXITY_API_URL = 'https://api.perplexity.ai/chat/completions'
PERPLEXITY_API_KEY = os.environ.get('PERPLEXITY_API_KEY')

# Input validation schema
REQUEST_SCHEMA = {
    'model': {
        'type': 'string', 
        'required': True, 
        'allowed': ['sonar', 'sonar-small', 'sonar-medium']
    },
    'messages': {
        'type': 'list', 
        'required': True,
        'schema': {
            'type': 'dict',
            'schema': {
                'role': {'type': 'string', 'allowed': ['system', 'user', 'assistant']},
                'content': {'type': 'string', 'maxlength': 2000}
            }
        },
        'maxlength': 10
    },
    'max_tokens': {'type': 'integer', 'min': 1, 'max': 500},
    'temperature': {'type': 'float', 'min': 0.0, 'max': 1.0}
}

def require_api_key(f):
    """Decorator to require API key authentication"""
    @wraps(f)
    def decorated(*args, **kwargs):
        api_key = request.headers.get('X-API-Key')
        expected_key = os.environ.get('API_KEY')
        
        if not expected_key:
            # If no API key is configured, skip authentication for development
            return f(*args, **kwargs)
            
        if not api_key or api_key != expected_key:
            logger.warning(f"Invalid API key attempt from {get_remote_address()}")
            return jsonify({'error': 'Invalid or missing API key'}), 401
        return f(*args, **kwargs)
    return decorated

@app.route('/api/perplexity', methods=['POST'])
@limiter.limit("10 per minute")
@require_api_key
def proxy_perplexity():
    """Secure proxy endpoint for Perplexity AI API"""
    
    # Validate API key is configured
    if not PERPLEXITY_API_KEY:
        logger.error("PERPLEXITY_API_KEY not configured")
        return jsonify({'error': 'Service temporarily unavailable'}), 503
    
    # Get and validate request data
    try:
        data = request.get_json(force=True)
    except Exception:
        return jsonify({'error': 'Invalid JSON format'}), 400
    
    if not data:
        return jsonify({'error': 'No data provided'}), 400
    
    # Validate input using Cerberus
    validator = Validator(REQUEST_SCHEMA)
    if not validator.validate(data):
        logger.warning(f"Invalid input from {get_remote_address()}: {validator.errors}")
        return jsonify({
            'error': 'Invalid input format',
            'details': validator.errors
        }), 400
    
    # Additional security: sanitize content
    for message in data.get('messages', []):
        if 'content' in message:
            # Remove potentially dangerous characters/patterns
            content = message['content']
            # Basic sanitization - remove script tags and other dangerous patterns
            dangerous_patterns = ['<script', '</script', 'javascript:', 'data:', 'vbscript:']
            for pattern in dangerous_patterns:
                content = content.replace(pattern.lower(), '').replace(pattern.upper(), '')
            message['content'] = content[:2000]  # Enforce length limit
    
    try:
        # Make request to Perplexity API with timeout
        response = requests.post(
            PERPLEXITY_API_URL,
            json=data,
            headers={
                'Authorization': f'Bearer {PERPLEXITY_API_KEY}',
                'Content-Type': 'application/json'
            },
            timeout=30  # 30 second timeout
        )
        
        response.raise_for_status()
        
        # Log successful request (without sensitive data)
        logger.info(f"Successful API request from {get_remote_address()}")
        
        return jsonify(response.json())
        
    except requests.exceptions.Timeout:
        logger.error("Perplexity API timeout")
        return jsonify({'error': 'Service timeout. Please try again.'}), 504
        
    except requests.exceptions.HTTPError as e:
        status_code = e.response.status_code if e.response else 500
        logger.error(f"Perplexity API HTTP error: {status_code}")
        
        if status_code == 401:
            return jsonify({'error': 'Authentication failed'}), 502
        elif status_code == 429:
            return jsonify({'error': 'Rate limit exceeded. Please try again later.'}), 429
        else:
            return jsonify({'error': 'External service error'}), 502
            
    except requests.exceptions.RequestException as e:
        logger.error(f"Perplexity API request error: {str(e)}")
        return jsonify({'error': 'Service temporarily unavailable'}), 503
        
    except Exception as e:
        logger.error(f"Unexpected error: {str(e)}")
        return jsonify({'error': 'Internal server error'}), 500

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    import time
    return jsonify({'status': 'healthy', 'timestamp': int(time.time())}), 200

@app.errorhandler(429)
def ratelimit_handler(e):
    """Handle rate limit exceeded"""
    logger.warning(f"Rate limit exceeded for {get_remote_address()}")
    return jsonify({
        'error': 'Rate limit exceeded',
        'message': 'Too many requests. Please try again later.'
    }), 429

@app.errorhandler(404)
def not_found(e):
    """Handle 404 errors"""
    return jsonify({'error': 'Endpoint not found'}), 404

@app.errorhandler(500)
def internal_error(e):
    """Handle internal server errors"""
    logger.error(f"Internal server error: {str(e)}")
    return jsonify({'error': 'Internal server error'}), 500

if __name__ == '__main__':
    import time
    
    # Security check
    if not os.environ.get('PERPLEXITY_API_KEY'):
        logger.warning("PERPLEXITY_API_KEY not set - API will not function properly")
    
    # Run with secure defaults
    app.run(
        host='0.0.0.0', 
        port=int(os.environ.get('PORT', 5000)),
        debug=False  # Never enable debug in production
    ) 