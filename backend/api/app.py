import os
from flask import Flask
from flask_cors import CORS

def create_app() -> Flask:
    app = Flask(__name__)
    # Security headers via after_request
    @app.after_request
    def add_security_headers(resp):
        resp.headers['X-Content-Type-Options'] = 'nosniff'
        resp.headers['X-Frame-Options'] = 'DENY'
        resp.headers['Referrer-Policy'] = 'no-referrer'
        resp.headers['Permissions-Policy'] = 'geolocation=(), microphone=(), camera=()'
        return resp

    allowed_origins = [
        'https://nijenhuis-botenverhuur.com',
        'https://www.nijenhuis-botenverhuur.com',
    ]
    if os.environ.get('APP_ENV', 'production').lower() == 'development':
        allowed_origins.extend([
            'http://localhost:8888',
            'http://127.0.0.1:8888',
            'http://localhost:8080',
            'http://127.0.0.1:8080',
        ])
    CORS(app, resources={r"/api/*": {"origins": allowed_origins}})

    # Blueprints (stubs)
    from .routes.health import bp as health_bp
    app.register_blueprint(health_bp, url_prefix='/api')

    return app

app = create_app()

if __name__ == '__main__':
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
    
    app.run(host='0.0.0.0', port=int(os.environ.get('PORT', 8080)), debug=debug_mode)

