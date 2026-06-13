#!/usr/bin/env python3
"""
Production Mollie Webhook Handler for Nijenhuis Botenverhuur
Server: 85.215.195.147
This script handles Mollie payment webhooks and updates booking statuses
"""

import json
import os
import sys
import hmac
import hashlib
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

def _nijenhuis_root():
    return os.environ.get('NIJENHUIS_ROOT', '/var/www/nijenhuis').rstrip('/')


class ProductionMollieWebhookHandler(BaseHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        self.mollie_api_key = os.environ.get('MOLLIE_API_KEY', '')  # Set via environment
        self.mollie_base_url = 'https://api.mollie.com/v2'
        self._root = _nijenhuis_root()
        self.database_path = os.path.join(self._root, 'bookings.db')
        super().__init__(*args, **kwargs)

    @staticmethod
    def _verify_mollie_signature(raw_body: str, signature_header: str, secret: str) -> bool:
        digest = hmac.new(
            secret.encode('utf-8'),
            raw_body.encode('utf-8'),
            hashlib.sha256,
        ).hexdigest()
        expected = 'sha256=' + digest
        return hmac.compare_digest(expected, signature_header)

    def do_POST(self):
        """Handle POST requests from Mollie webhooks"""
        try:
            content_length = int(self.headers.get('Content-Length', 0))
            post_data = self.rfile.read(content_length)
            raw_body = post_data.decode('utf-8')
            app_env = os.environ.get('APP_ENV', 'production').lower()
            webhook_secret = os.environ.get('MOLLIE_WEBHOOK_SECRET', '').strip()
            has_real_secret = (
                webhook_secret
                and webhook_secret != 'your_webhook_secret_here'
                and len(webhook_secret) > 20
            )
            signature = (self.headers.get('X-Mollie-Signature') or '').strip()

            if has_real_secret:
                if not signature or not self._verify_mollie_signature(raw_body, signature, webhook_secret):
                    logging.error('Invalid or missing Mollie webhook signature')
                    self.send_error(401, 'Invalid signature')
                    return
            else:
                logging.warning(
                    'Webhook processed without signature verification (%s; set MOLLIE_WEBHOOK_SECRET when ready)',
                    app_env,
                )

            # Parse the webhook data (Mollie may send JSON or form-urlencoded)
            try:
                webhook_data = json.loads(raw_body)
            except json.JSONDecodeError:
                from urllib.parse import parse_qs
                q = parse_qs(raw_body)
                pid = (q.get('id') or [''])[0]
                webhook_data = {'id': pid} if pid else {}

            logging.info(f"Webhook received from {self.client_address[0]}: id={webhook_data.get('id')}")
            
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
        self.wfile.write(b'Mollie Webhook Handler is running\n')
    
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
                'paid': 'paid',
                'failed': 'payment-rejected',
                'expired': 'payment-rejected',
                'canceled': 'payment-rejected',
                'pending': 'confirmed-not-paid'
            }
            
            new_booking_status = status_mapping.get(payment_status, 'not-confirmed')
            
            logging.info(f"Payment {payment_id}: {payment_status} -> {new_booking_status}")
            
            # Get booking data for email sending
            booking = self.get_booking_by_payment_id(payment_id)
            
            # Update database
            self.update_database(payment_id, new_booking_status, payment_status)
            
            # Also update localStorage file for frontend access
            old_status = booking.get('status') if booking else None
            self.update_local_storage(payment_id, new_booking_status, payment_status)
            
            # Send confirmation email if status changed to paid
            if new_booking_status == 'paid' and old_status != 'paid' and booking:
                self.send_confirmation_email(booking)
            
        except Exception as e:
            logging.error(f"Error updating booking status: {str(e)}")
    
    def get_booking_by_payment_id(self, payment_id):
        """Get booking data by payment ID"""
        try:
            bookings_file = os.path.join(self._root, 'data', 'bookings.json')
            # Legacy path (migration)
            legacy_admin = os.path.join(self._root, 'admin', 'bookings.json')
            for path in (bookings_file, legacy_admin):
                if os.path.exists(path):
                    with open(path, 'r') as f:
                        bookings = json.load(f)
                        for booking in bookings:
                            if booking.get('paymentId') == payment_id:
                                return booking

            storage_file = os.path.join(self._root, 'local_bookings.json')
            if os.path.exists(storage_file):
                with open(storage_file, 'r') as f:
                    bookings = json.load(f)
                    for booking in bookings:
                        if booking.get('paymentId') == payment_id:
                            return booking
            
            return None
        except Exception as e:
            logging.error(f"Error getting booking by payment ID: {str(e)}")
            return None
    
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
            paths = [
                os.path.join(self._root, 'data', 'bookings.json'),
                os.path.join(self._root, 'admin', 'bookings.json'),
                os.path.join(self._root, 'local_bookings.json'),
            ]
            for file_path in paths:
                if not os.path.exists(file_path):
                    continue
                
                # Read existing bookings
                with open(file_path, 'r') as f:
                    bookings = json.load(f)
                
                # Find booking with this payment ID and update status
                updated = False
                for booking in bookings:
                    if booking.get('paymentId') == payment_id:
                        booking['status'] = booking_status
                        booking['paymentStatus'] = payment_status
                        booking['updatedAt'] = datetime.now().isoformat()
                        logging.info(f"Updated booking {booking.get('id')} status to {booking_status} in {file_path}")
                        updated = True
                        break
                
                # Write updated bookings back to file
                if updated:
                    with open(file_path, 'w') as f:
                        json.dump(bookings, f, indent=2)
                
        except Exception as e:
            logging.error(f"Local storage update error: {str(e)}")
    
    def send_confirmation_email(self, booking):
        """Send confirmation email after successful payment"""
        try:
            import smtplib
            from email.mime.text import MIMEText
            from email.mime.multipart import MIMEMultipart
            from email.header import Header
            
            customer_email = booking.get('customerEmail')
            if not customer_email:
                logging.warning(f"No email address for booking {booking.get('id')}")
                return
            
            # Get boat name
            boats_file = os.path.join(self._root, 'data', 'boats.json')
            legacy_boats = os.path.join(self._root, 'admin', 'boats.json')
            boat_name = booking.get('boatType', 'Unknown')
            boat_found = False
            for bf in (boats_file, legacy_boats):
                if not os.path.exists(bf):
                    continue
                with open(bf, 'r') as f:
                    boats = json.load(f)
                    for boat in boats:
                        if boat.get('id') == booking.get('boatType'):
                            boat_name = boat.get('name', boat_name)
                            boat_found = True
                            break
                if boat_found:
                    break
            
            # Format dates (Dutch format)
            from datetime import datetime
            start_date = booking.get('date', '')
            end_date = booking.get('endDate', start_date)
            
            try:
                start_date_obj = datetime.strptime(start_date, '%Y-%m-%d')
                end_date_obj = datetime.strptime(end_date, '%Y-%m-%d') if end_date else start_date_obj
                start_date_str = start_date_obj.strftime('%d-%m-%Y')
                end_date_str = end_date_obj.strftime('%d-%m-%Y') if end_date != start_date else start_date_str
                date_str = start_date_str if start_date_str == end_date_str else f"{start_date_str} tot {end_date_str}"
                # English format
                date_str_en = start_date_str if start_date_str == end_date_str else f"{start_date_str} to {end_date_str}"
            except:
                date_str = start_date
                date_str_en = start_date
            
            # Email content
            subject = "Boeking Bevestigd - Nijenhuis Botenverhuur"
            
            # Escape HTML for customer name and notes
            customer_name = booking.get('customerName', '').replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;').replace('"', '&quot;').replace("'", '&#039;')
            notes = (booking.get('notes') or '-').replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;').replace('"', '&quot;').replace("'", '&#039;')
            
            html_body = f"""
            <html>
            <head>
                <style>
                    body {{ font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }}
                    .container {{ max-width: 600px; margin: 0 auto; background: #fff; }}
                    .header {{ background: #1a365d; padding: 30px; text-align: center; }}
                    .content {{ padding: 30px; }}
                    h2 {{ color: #1a365d; margin-top: 0; }}
                    table {{ width: 100%; border-collapse: collapse; margin: 20px 0; }}
                    table td {{ padding: 12px; border-bottom: 1px solid #eee; }}
                    table td:first-child {{ font-weight: bold; width: 40%; color: #555; }}
                    .footer {{ background: #f8f9fa; padding: 20px; font-size: 12px; color: #666; text-align: center; border-top: 1px solid #eee; }}
                    .warning {{ color: #e53e3e; font-weight: bold; }}
                    .divider {{ border-top: 2px solid #1a365d; margin: 40px 0; }}
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1 style='color: white; margin: 10px 0 0; font-size: 24px;'>Boeking Bevestigd</h1>
                    </div>
                    <div class='content'>
                        <!-- Dutch Section -->
                        <p>Beste {customer_name},</p>
                        <p>Bedankt voor uw boeking bij Nijenhuis Botenverhuur! Uw betaling is succesvol ontvangen en uw reservering is definitief.</p>
                        
                        <div style='background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0;'>
                            <strong>Reservering ID:</strong> {booking.get('id', '')}
                        </div>
                        
                        <table>
                            <tr>
                                <td>Boot</td>
                                <td>{boat_name}</td>
                            </tr>
                            <tr>
                                <td>Datum</td>
                                <td>{date_str}</td>
                            </tr>
                            <tr>
                                <td>Aantal Dagen</td>
                                <td>{booking.get('numberOfDays', 1)}</td>
                            </tr>
                            <tr>
                                <td>Totaalprijs</td>
                                <td>€{booking.get('amount', 0):.2f}</td>
                            </tr>
                            <tr>
                                <td>Opmerkingen</td>
                                <td>{notes}</td>
                            </tr>
                        </table>
                        
                        <p>We kijken ernaar uit u te verwelkomen op onze locatie:</p>
                        <p><strong>Veneweg 199<br>7946 LP Wanneperveen</strong></p>
                        
                        <!-- Divider -->
                        <div class='divider'></div>
                        
                        <!-- English Section -->
                        <p>Dear {customer_name},</p>
                        <p>Thank you for your booking with Nijenhuis Botenverhuur! Your payment has been successfully received and your reservation is confirmed.</p>
                        
                        <div style='background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0;'>
                            <strong>Booking ID:</strong> {booking.get('id', '')}
                        </div>
                        
                        <table>
                            <tr>
                                <td>Boat</td>
                                <td>{boat_name}</td>
                            </tr>
                            <tr>
                                <td>Date</td>
                                <td>{date_str_en}</td>
                            </tr>
                            <tr>
                                <td>Number of Days</td>
                                <td>{booking.get('numberOfDays', 1)}</td>
                            </tr>
                            <tr>
                                <td>Total Price</td>
                                <td>€{booking.get('amount', 0):.2f}</td>
                            </tr>
                            <tr>
                                <td>Notes</td>
                                <td>{notes}</td>
                            </tr>
                        </table>
                        
                        <p>We look forward to welcoming you at our location:</p>
                        <p><strong>Veneweg 199<br>7946 LP Wanneperveen</strong></p>
                    </div>
                    <div class='footer'>
                        <p class='warning'>Let op: Een annuleringsvergoeding van 10% van het totaalbedrag is van toepassing.</p>
                        <p class='warning' style='margin-top: 10px;'>Note: A cancellation fee of 10% of the total amount applies.</p>
                        <p>&copy; {datetime.now().year} Nijenhuis Botenverhuur. Alle rechten voorbehouden.</p>
                        <p>&copy; {datetime.now().year} Nijenhuis Botenverhuur. All rights reserved.</p>
                        <p>Tel: +31 522 281 528</p>
                    </div>
                </div>
            </body>
            </html>
            """
            
            # SMTP only (no unsafe subprocess / PHP embedding)
            smtp_host = os.environ.get('SMTP_HOST', '')
            smtp_port = int(os.environ.get('SMTP_PORT', '587'))
            smtp_user = os.environ.get('SMTP_USER', '')
            smtp_pass = os.environ.get('SMTP_PASS', '')
            smtp_from = os.environ.get('SMTP_FROM', 'reserveringen@nijenhuis-botenverhuur.com')
            
            if smtp_host and smtp_user and smtp_pass:
                # Send via SMTP
                msg = MIMEMultipart('alternative')
                msg['Subject'] = subject
                msg['From'] = smtp_from
                msg['To'] = customer_email
                msg['Reply-To'] = 'info@nijenhuis-botenverhuur.com'
                
                part = MIMEText(html_body, 'html', 'utf-8')
                msg.attach(part)
                
                try:
                    server = smtplib.SMTP(smtp_host, smtp_port)
                    server.starttls()
                    server.login(smtp_user, smtp_pass)
                    server.send_message(msg)
                    server.quit()
                    logging.info(f"Confirmation email sent via SMTP to {customer_email}")
                    return
                except Exception as e:
                    logging.error(
                        f'Confirmation email failed via SMTP for {customer_email}: {e}'
                    )
            else:
                logging.error(
                    'Confirmation email not sent: configure SMTP_HOST, SMTP_USER, SMTP_PASS '
                    f'(customer {customer_email})'
                )
                
        except Exception as e:
            logging.error(f"Exception sending confirmation email: {str(e)}")
    
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