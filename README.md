# Nijenhuis Botenverhuur – Monorepo

A structured repository for the Nijenhuis website, admin, payments, chatbot, and infrastructure.

## Directory layout

- frontend/
  - public/            Static assets served as-is (PWA files, images, flags, CSS)
  - src/
    - pages/           HTML pages (built by Vite)
    - js/
      - core/          Shared utilities (translation, modal, shared UI)
      - booking/       Booking and payments (Mollie client proxy)
      - chat/          Chat widget and helpers
      - admin/         Admin UI entry
- backend/
  - api/               Flask API scaffold (security headers, CORS)
  - chatbot/
    - api/             Chatbot API servers (server.py, legacy, proxies)
    - core/            Chatbot engines and helpers
    - training/        Training UI and scripts (framework.py, data/)
    - tests/           Chatbot-related tests
  - webhooks/
    - mollie/          Mollie webhook handlers (Python + PHP for Plesk)
- infra/
  - nginx/             Example Nginx config to serve frontend and proxy APIs
- documentation/       All project documentation and deployment guides
- scripts/
  - dev/               Developer utilities (local booking server launcher)

## Getting started (development)

Frontend (Vite):
1) cd frontend
2) npm install
3) npm run dev
Open http://localhost:5173/src/pages/index.html

Backend API (Flask scaffold):
- python backend/api/app.py (example app factory for future endpoints)

Chatbot API:
- python backend/chatbot/api/server.py
- Health: http://localhost:5001/api/health
- Chat:   http://localhost:5001/api/chat

Training framework (GUI):
- python backend/chatbot/training/start.py

Webhooks (local):
- Python service: python backend/webhooks/mollie/webhook_handler_production.py 8080
- Endpoint (proxied by web server): http://localhost:8080/webhook/mollie

Plesk (PHP) handler path in production:
- https://yourdomain.com/webhooks/mollie/webhook_handler_plesk.php

## Payments (Mollie)
- Client uses /mollie_api.php proxy to avoid exposing API keys in the browser.
- Set MOLLIE_API_KEY in environment for both Python and PHP handlers.

## Environment variables
- MOLLIE_API_KEY: Mollie secret key
- ADMIN_USERNAME / ADMIN_PASSWORD: Admin login (used in PHP admin handler)

## Security hardening (highlights)
- Secrets removed from client; server-side proxy for Mollie
- PHP admin handler: session auth, CSRF, same-origin checks, security headers
- Service worker only caches safe public API endpoints
- DOM XSS mitigations across pages and shared components
- CORS restricted to known origins for backend services

## Build and deploy
- Frontend: cd frontend && npm run build → output in frontend/dist
- Nginx example: infra/nginx/site.conf
- Deploy script (example, VPS): deploy_to_server.sh

## Documentation
See documentation/ for deployment guides and integration details:
- documentation/MOLLIE_INTEGRATION.md
- documentation/PRODUCTION_DEPLOYMENT.md
- documentation/PLESK_DEPLOYMENT.md
- documentation/TRAINING_FRAMEWORK_GUIDE.md
- documentation/BOOKING_SYSTEM_GUIDE.md

## License
Proprietary. All rights reserved.
