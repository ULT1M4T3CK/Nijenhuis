#!/usr/bin/env python3
# ========================================================================
# BOOKING HANDLER - PYTHON VERSION FOR LOCAL DEVELOPMENT
# ========================================================================

import json
import os
import sys
from datetime import datetime
from http.server import HTTPServer, BaseHTTPRequestHandler
from urllib.parse import parse_qs, urlparse
import uuid

# Configuration
BOOKINGS_FILE = 'bookings.json'
ADMIN_CREDENTIALS = {
    'username': 'admin',
    'password': 'nijenhuis2024'
}

class BookingHandler(BaseHTTPRequestHandler):
    def do_OPTIONS(self):
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        self.end_headers()
    
    def do_GET(self):
        self.send_response(200)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.end_headers()
        
        bookings = self.load_bookings()
        response = {'success': True, 'bookings': bookings}
        self.wfile.write(json.dumps(response).encode())
    
    def do_POST(self):
        content_length = int(self.headers['Content-Length'])
        post_data = self.rfile.read(content_length)
        
        try:
            input_data = json.loads(post_data.decode('utf-8'))
        except json.JSONDecodeError:
            self.send_error_response(400, 'Invalid JSON data')
            return
        
        # Debug logging
        print(f"Received POST request: {input_data}")
        
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        
        # Handle login request
        if input_data.get('action') == 'login':
            self.handle_login(input_data)
            return
        
        # Handle booking submission from main website
        if input_data.get('formType') == 'booking':
            self.handle_booking_submission(input_data)
            return
        
        # Handle admin actions
        if 'action' in input_data:
            self.handle_admin_action(input_data)
            return
        
        self.send_error_response(400, 'Invalid request')
    
    def handle_login(self, input_data):
        username = input_data.get('username', '')
        password = input_data.get('password', '')
        
        if username == ADMIN_CREDENTIALS['username'] and password == ADMIN_CREDENTIALS['password']:
            response = {'success': True, 'message': 'Login successful'}
        else:
            response = {'success': False, 'message': 'Invalid credentials'}
        
        self.send_response(200)
        self.end_headers()
        self.wfile.write(json.dumps(response).encode())
    
    def handle_booking_submission(self, input_data):
        booking_data = {
            'date': input_data.get('date', ''),
            'boatType': input_data.get('boatType', ''),
            'customerName': input_data.get('customerName', ''),
            'customerEmail': input_data.get('customerEmail', ''),
            'customerPhone': input_data.get('customerPhone', ''),
            'notes': input_data.get('notes', '')
        }
        
        if not self.validate_booking(booking_data):
            self.send_error_response(400, 'Invalid booking data')
            return
        
        # Create new booking
        new_booking = {
            'id': self.generate_id(),
            'date': booking_data['date'],
            'boatType': booking_data['boatType'],
            'customerName': booking_data['customerName'],
            'customerEmail': booking_data['customerEmail'],
            'customerPhone': booking_data['customerPhone'],
            'notes': booking_data['notes'],
            'status': 'not-confirmed',
            'createdAt': datetime.now().isoformat(),
            'updatedAt': datetime.now().isoformat()
        }
        
        # Load existing bookings
        bookings = self.load_bookings()
        bookings.append(new_booking)
        
        # Save bookings
        if self.save_bookings(bookings):
            response = {
                'success': True,
                'message': 'Booking submitted successfully',
                'bookingId': new_booking['id']
            }
            self.send_response(200)
        else:
            response = {'success': False, 'message': 'Failed to save booking'}
            self.send_response(500)
        
        self.end_headers()
        self.wfile.write(json.dumps(response).encode())
    
    def handle_admin_action(self, input_data):
        action = input_data.get('action')
        
        if action == 'getBookings':
            bookings = self.load_bookings()
            response = {'success': True, 'bookings': bookings}
            self.send_response(200)
        
        elif action == 'createBooking':
            booking_data = input_data.get('bookingData', {})
            if not booking_data:
                self.send_error_response(400, 'Booking data required')
                return
            
            if not self.validate_booking(booking_data):
                self.send_error_response(400, 'Invalid booking data')
                return
            
            new_booking = {
                'id': self.generate_id(),
                'date': booking_data['date'],
                'boatType': booking_data['boatType'],
                'customerName': booking_data['customerName'],
                'customerEmail': booking_data['customerEmail'],
                'customerPhone': booking_data['customerPhone'],
                'notes': booking_data.get('notes', ''),
                'status': booking_data.get('status', 'not-confirmed'),
                'createdAt': datetime.now().isoformat(),
                'updatedAt': datetime.now().isoformat()
            }
            
            bookings = self.load_bookings()
            bookings.append(new_booking)
            
            if self.save_bookings(bookings):
                response = {
                    'success': True,
                    'message': 'Booking created successfully',
                    'bookingId': new_booking['id']
                }
                self.send_response(200)
            else:
                response = {'success': False, 'message': 'Failed to create booking'}
                self.send_response(500)
        
        elif action == 'updateBooking':
            booking_id = input_data.get('bookingId')
            if not booking_id:
                self.send_error_response(400, 'Booking ID required')
                return
            
            booking_data = input_data.get('bookingData', {})
            bookings = self.load_bookings()
            
            booking_index = -1
            for i, booking in enumerate(bookings):
                if booking['id'] == booking_id:
                    booking_index = i
                    break
            
            if booking_index == -1:
                self.send_error_response(404, 'Booking not found')
                return
            
            # Update booking
            bookings[booking_index].update(booking_data)
            bookings[booking_index]['updatedAt'] = datetime.now().isoformat()
            
            if self.save_bookings(bookings):
                response = {'success': True, 'message': 'Booking updated successfully'}
                self.send_response(200)
            else:
                response = {'success': False, 'message': 'Failed to update booking'}
                self.send_response(500)
        
        elif action == 'deleteBooking':
            booking_id = input_data.get('bookingId')
            if not booking_id:
                self.send_error_response(400, 'Booking ID required')
                return
            
            bookings = self.load_bookings()
            bookings = [b for b in bookings if b['id'] != booking_id]
            
            if self.save_bookings(bookings):
                response = {'success': True, 'message': 'Booking deleted successfully'}
                self.send_response(200)
            else:
                response = {'success': False, 'message': 'Failed to delete booking'}
                self.send_response(500)
        
        else:
            self.send_error_response(400, 'Invalid action')
            return
        
        self.end_headers()
        self.wfile.write(json.dumps(response).encode())
    
    def validate_booking(self, booking_data):
        required_fields = ['date', 'boatType', 'customerName', 'customerEmail', 'customerPhone']
        for field in required_fields:
            if not booking_data.get(field):
                return False
        
        # Basic email validation
        email = booking_data['customerEmail']
        if '@' not in email or '.' not in email:
            return False
        
        # Basic date validation
        try:
            datetime.strptime(booking_data['date'], '%Y-%m-%d')
        except ValueError:
            return False
        
        return True
    
    def load_bookings(self):
        if os.path.exists(BOOKINGS_FILE):
            try:
                with open(BOOKINGS_FILE, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except (json.JSONDecodeError, IOError):
                return []
        return []
    
    def save_bookings(self, bookings):
        try:
            with open(BOOKINGS_FILE, 'w', encoding='utf-8') as f:
                json.dump(bookings, f, indent=2, ensure_ascii=False)
            return True
        except IOError:
            return False
    
    def generate_id(self):
        return f"{uuid.uuid4().hex}_{int(datetime.now().timestamp())}"
    
    def send_error_response(self, status_code, message):
        response = {'success': False, 'message': message}
        self.send_response(status_code)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.end_headers()
        self.wfile.write(json.dumps(response).encode())

def run_server(port=8000):
    server_address = ('', port)
    httpd = HTTPServer(server_address, BookingHandler)
    print(f"Starting booking server on port {port}")
    print(f"Admin area: http://localhost:{port}/admin/")
    print(f"Test endpoint: http://localhost:{port}/admin/test-booking.py")
    print("Press Ctrl+C to stop the server")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\nShutting down server...")
        httpd.server_close()

if __name__ == '__main__':
    port = 8000
    if len(sys.argv) > 1:
        port = int(sys.argv[1])
    run_server(port) 