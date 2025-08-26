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
        'http://nijenhuis-botenverhuur.com',
        'http://85.215.195.147',
    ]
    CORS(app, resources={r"/api/*": {"origins": allowed_origins}})

    # Blueprints (stubs)
    from .routes.health import bp as health_bp
    app.register_blueprint(health_bp, url_prefix='/api')

    return app

app = create_app()

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=int(os.environ.get('PORT', 8080)))

