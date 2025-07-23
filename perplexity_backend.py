import os
from flask import Flask, request, jsonify
from flask_cors import CORS
import requests

app = Flask(__name__)
CORS(app)  # Enable CORS for all domains (adjust as needed)

PERPLEXITY_API_URL = 'https://api.perplexity.ai/chat/completions'  # Actual endpoint
PERPLEXITY_API_KEY = os.environ.get('PERPLEXITY_API_KEY')

@app.route('/api/perplexity', methods=['POST'])
def proxy_perplexity():
    data = request.get_json()
    if not data:
        return jsonify({'error': 'No data provided'}), 400
    try:
        response = requests.post(
            PERPLEXITY_API_URL,
            json=data,
            headers={
                'Authorization': f'Bearer {PERPLEXITY_API_KEY}',
                'Content-Type': 'application/json'
            }
        )
        response.raise_for_status()
        return jsonify(response.json())
    except requests.RequestException as e:
        return jsonify({'error': 'Error calling Perplexity API', 'details': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000) 