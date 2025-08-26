#!/usr/bin/env python3
"""
Production Mollie Webhook Handler for Nijenhuis Botenverhuur
Server: 85.215.195.147
This script handles Mollie payment webhooks and updates booking statuses
"""

import json
import os
import sys
import logging
from http.server import HTTPServer, BaseHTTPRequestHandler
from urllib.parse import parse_qs, urlparse
import requests
from datetime import datetime
import sqlite3
import threading

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('/var/log/mollie_webhook.log'),
        logging.StreamHandler()
    ]
)

class ProductionMollieWebhookHandler(BaseHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        self.mollie_api_key = os.environ.get('MOLLIE_API_KEY', '')  # Set via environment
        self.mollie_base_url = 'https://api.mollie.com/v2'
        self.server_ip = '85.215.195.147'
        self.database_path = '/var/www/nijenhuis/bookings.db'
        super().__init__(*args, **kwargs)
    
    def do_POST(self):
        """Handle POST requests from Mollie webhooks"""
        try:
            # Get content length
            content_length = int(self.headers.get('Content-Length', 0))
            post_data = self.rfile.read(content_length)
            
            # Parse the webhook data
            webhook_data = json.loads(post_data.decode('utf-8'))
            
            logging.info(f"Webhook received from {self.client_address[0]}: {webhook_data}")
            
            # Extract payment ID from webhook
            payment_id = webhook_data.get('id')
            
            if payment_id:
                # Get payment status from Mollie API
                payment_status = self.get_payment_status(payment_id)
                
                if payment_status:
                    # Update booking status based on payment status
                    self.update_booking_status(payment_id, payment_status)
                    
                    # Send success response
                    self.send_response(200)
                    self.send_header('Content-type', 'application/json')
                    self.end_headers()
                    self.wfile.write(json.dumps({'status': 'success'}).encode())
                    
                    logging.info(f"Payment {payment_id} processed successfully: {payment_status}")
                else:
                    self.send_error(500, "Failed to get payment status")
                    logging.error(f"Failed to get payment status for {payment_id}")
            else:
                self.send_error(400, "No payment ID in webhook")
                logging.error("No payment ID in webhook data")
                
        except Exception as e:
            logging.error(f"Webhook error: {str(e)}")
            self.send_error(500, f"Webhook processing error: {str(e)}")
    
    def do_GET(self):
        """Handle GET requests (health check)"""
        self.send_response(200)
        self.send_header('Content-type', 'text/plain')
        self.end_headers()
        self.wfile.write(f"Mollie Webhook Handler is running on {self.server_ip}".encode())
    
    def get_payment_status(self, payment_id):
        """Get payment status from Mollie API"""
        try:
            headers = {
                'Authorization': f'Bearer {self.mollie_api_key}',
                'Content-Type': 'application/json'
            }
            
            response = requests.get(
                f'{self.mollie_base_url}/payments/{payment_id}',
                headers=headers,
                timeout=10
            )
            
            if response.status_code == 200:
                payment_data = response.json()
                return payment_data.get('status')
            else:
                logging.error(f"Failed to get payment status: {response.status_code}")
                return None
                
        except Exception as e:
            logging.error(f"Error getting payment status: {str(e)}")
            return None
    
    def update_booking_status(self, payment_id, payment_status):
        """Update booking status based on payment status"""
        try:
            # Map Mollie status to booking status
            status_mapping = {
                'paid': 'confirmed-paid',
                'failed': 'payment-rejected',
                'expired': 'payment-rejected',
                'canceled': 'payment-rejected',
                'pending': 'confirmed-not-paid'
            }
            
            new_booking_status = status_mapping.get(payment_status, 'not-confirmed')
            
            logging.info(f"Payment {payment_id}: {payment_status} -> {new_booking_status}")
            
            # Update database
            self.update_database(payment_id, new_booking_status, payment_status)
            
            # Also update localStorage file for frontend access
            self.update_local_storage(payment_id, new_booking_status, payment_status)
            
        except Exception as e:
            logging.error(f"Error updating booking status: {str(e)}")
    
    def update_database(self, payment_id, booking_status, payment_status):
        """Update booking status in database"""
        try:
            conn = sqlite3.connect(self.database_path)
            cursor = conn.cursor()
            
            # Create table if it doesn't exist
            cursor.execute('''
                CREATE TABLE IF NOT EXISTS bookings (
                    id TEXT PRIMARY KEY,
                    payment_id TEXT,
                    status TEXT,
                    payment_status TEXT,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ''')
            
            # Update or insert booking
            cursor.execute('''
                INSERT OR REPLACE INTO bookings (id, payment_id, status, payment_status, updated_at)
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
            ''', (payment_id, payment_id, booking_status, payment_status))
            
            conn.commit()
            conn.close()
            
            logging.info(f"Database updated for payment {payment_id}")
            
        except Exception as e:
            logging.error(f"Database update error: {str(e)}")
    
    def update_local_storage(self, payment_id, booking_status, payment_status):
        """Update localStorage file for frontend access"""
        try:
            storage_file = '/var/www/nijenhuis/local_bookings.json'
            
            # Read existing bookings
            if os.path.exists(storage_file):
                with open(storage_file, 'r') as f:
                    bookings = json.load(f)
            else:
                bookings = []
            
            # Find booking with this payment ID and update status
            for booking in bookings:
                if booking.get('paymentId') == payment_id:
                    booking['status'] = booking_status
                    booking['paymentStatus'] = payment_status
                    booking['updatedAt'] = datetime.now().isoformat()
                    logging.info(f"Updated booking {booking.get('id')} status to {booking_status}")
                    break
            
            # Write updated bookings back to file
            with open(storage_file, 'w') as f:
                json.dump(bookings, f, indent=2)
                
        except Exception as e:
            logging.error(f"Local storage update error: {str(e)}")
    
    def log_message(self, format, *args):
        """Override to suppress default logging"""
        pass

def run_production_webhook_server(port=8080):
    """Run the production webhook server"""
    server_address = ('', port)
    httpd = HTTPServer(server_address, ProductionMollieWebhookHandler)
    
    logging.info(f"Starting production Mollie webhook server on port {port}")
    logging.info(f"Server IP: 85.215.195.147")
    logging.info(f"Webhook URL: http://85.215.195.147:{port}/webhook/mollie")
    logging.info(f"Health check: http://85.215.195.147:{port}/")
    logging.info(f"Press Ctrl+C to stop the server")
    
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        logging.info("Shutting down production webhook server...")
        httpd.shutdown()

if __name__ == '__main__':
    port = int(sys.argv[1]) if len(sys.argv) > 1 else 8080
    run_production_webhook_server(port) 