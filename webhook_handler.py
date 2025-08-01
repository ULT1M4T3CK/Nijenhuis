#!/usr/bin/env python3
"""
Mollie Webhook Handler for Nijenhuis Botenverhuur
This script handles Mollie payment webhooks and updates booking statuses
"""

import json
import os
import sys
from http.server import HTTPServer, BaseHTTPRequestHandler
from urllib.parse import parse_qs, urlparse
import requests
from datetime import datetime

class MollieWebhookHandler(BaseHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        self.mollie_api_key = 'test_sHQfqTngBbCpEfMyMCPGH92gnm8P7m'
        self.mollie_base_url = 'https://api.mollie.com/v2'
        super().__init__(*args, **kwargs)
    
    def do_POST(self):
        """Handle POST requests from Mollie webhooks"""
        try:
            # Get content length
            content_length = int(self.headers.get('Content-Length', 0))
            post_data = self.rfile.read(content_length)
            
            # Parse the webhook data
            webhook_data = json.loads(post_data.decode('utf-8'))
            
            print(f"[{datetime.now()}] Webhook received: {webhook_data}")
            
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
                else:
                    self.send_error(500, "Failed to get payment status")
            else:
                self.send_error(400, "No payment ID in webhook")
                
        except Exception as e:
            print(f"[{datetime.now()}] Webhook error: {str(e)}")
            self.send_error(500, f"Webhook processing error: {str(e)}")
    
    def do_GET(self):
        """Handle GET requests (health check)"""
        self.send_response(200)
        self.send_header('Content-type', 'text/plain')
        self.end_headers()
        self.wfile.write(b"Mollie Webhook Handler is running")
    
    def get_payment_status(self, payment_id):
        """Get payment status from Mollie API"""
        try:
            headers = {
                'Authorization': f'Bearer {self.mollie_api_key}',
                'Content-Type': 'application/json'
            }
            
            response = requests.get(
                f'{self.mollie_base_url}/payments/{payment_id}',
                headers=headers
            )
            
            if response.status_code == 200:
                payment_data = response.json()
                return payment_data.get('status')
            else:
                print(f"Failed to get payment status: {response.status_code}")
                return None
                
        except Exception as e:
            print(f"Error getting payment status: {str(e)}")
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
            
            print(f"[{datetime.now()}] Payment {payment_id}: {payment_status} -> {new_booking_status}")
            
            # In a real implementation, this would update a database
            # For now, we'll just log the status change
            log_entry = {
                'timestamp': datetime.now().isoformat(),
                'payment_id': payment_id,
                'payment_status': payment_status,
                'booking_status': new_booking_status
            }
            
            # Write to log file
            with open('payment_webhooks.log', 'a') as f:
                f.write(json.dumps(log_entry) + '\n')
            
            # For local development, you could also update localStorage
            # by writing to a file that the frontend can read
            self.update_local_storage(payment_id, new_booking_status, payment_status)
            
        except Exception as e:
            print(f"Error updating booking status: {str(e)}")
    
    def update_local_storage(self, payment_id, booking_status, payment_status):
        """Update localStorage for local development"""
        try:
            # Read current bookings from a JSON file
            bookings_file = 'local_bookings.json'
            
            if os.path.exists(bookings_file):
                with open(bookings_file, 'r') as f:
                    bookings = json.load(f)
            else:
                bookings = []
            
            # Find booking with this payment ID and update status
            for booking in bookings:
                if booking.get('paymentId') == payment_id:
                    booking['status'] = booking_status
                    booking['paymentStatus'] = payment_status
                    booking['updatedAt'] = datetime.now().isoformat()
                    print(f"Updated booking {booking.get('id')} status to {booking_status}")
                    break
            
            # Write updated bookings back to file
            with open(bookings_file, 'w') as f:
                json.dump(bookings, f, indent=2)
                
        except Exception as e:
            print(f"Error updating local storage: {str(e)}")
    
    def log_message(self, format, *args):
        """Override to suppress default logging"""
        pass

def run_webhook_server(port=8080):
    """Run the webhook server"""
    server_address = ('', port)
    httpd = HTTPServer(server_address, MollieWebhookHandler)
    print(f"[{datetime.now()}] Starting Mollie webhook server on port {port}")
    print(f"[{datetime.now()}] Webhook URL: http://localhost:{port}/webhook/mollie")
    print(f"[{datetime.now()}] Health check: http://localhost:{port}/")
    print(f"[{datetime.now()}] Press Ctrl+C to stop the server")
    
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print(f"\n[{datetime.now()}] Shutting down webhook server...")
        httpd.shutdown()

if __name__ == '__main__':
    port = int(sys.argv[1]) if len(sys.argv) > 1 else 8080
    run_webhook_server(port) 