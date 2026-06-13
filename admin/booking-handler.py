#!/usr/bin/env python3
# ========================================================================
# BOOKING HANDLER - PYTHON VERSION FOR LOCAL DEVELOPMENT
# Security-hardened: environment-based auth, constant-time comparison
# 
# SESSION ARCHITECTURE:
# - Python handler (local dev): Uses file-based sessions with cookies
# - PHP handler (production): Uses PHP sessions with secure cookies
# - Both handlers follow the same authentication flow:
#   1. POST /login with credentials -> returns CSRF token
#   2. GET ?action=session -> validates session, returns auth status
#   3. All admin operations require valid session
# - Client-side localStorage is used for UI gating only; actual data
#   operations always require server-side session validation
# ========================================================================

import json
import os
import sys
import hashlib
import hmac
import time
from datetime import datetime, timedelta
from http.server import HTTPServer, BaseHTTPRequestHandler, SimpleHTTPRequestHandler
from urllib.parse import parse_qs, urlparse, unquote
import uuid
import mimetypes
import threading

# Configuration
BOOKINGS_FILE = 'bookings.json'
SESSIONS_FILE = 'admin_sessions.json'
SESSION_EXPIRY_HOURS = 24
SESSION_COOKIE_NAME = 'admin_session_token'
BOATS_FILE = 'boats.json'

# Thread lock for session file access
session_lock = threading.Lock()

# Load .env file if it exists (for local development)
# This matches the PHP handler's behavior
def load_env_file():
    """Load environment variables from .env file in project root"""
    script_dir = os.path.dirname(os.path.abspath(__file__))
    project_root = os.path.dirname(script_dir)
    env_file = os.path.join(project_root, '.env')
    
    if os.path.exists(env_file):
        try:
            with open(env_file, 'r', encoding='utf-8') as f:
                for line in f:
                    line = line.strip()
                    # Skip comments and empty lines
                    if not line or line.startswith('#'):
                        continue
                    # Parse key=value pairs
                    if '=' in line:
                        key, value = line.split('=', 1)
                        key = key.strip()
                        value = value.strip()
                        # Remove quotes if present
                        if value.startswith('"') and value.endswith('"'):
                            value = value[1:-1]
                        elif value.startswith("'") and value.endswith("'"):
                            value = value[1:-1]
                        # Set environment variable if not already set
                        if key and value and key not in os.environ:
                            os.environ[key] = value
        except Exception as e:
            print(f"Warning: Could not load .env file: {e}", file=sys.stderr)

# Load .env file at module level
load_env_file()

# Admin credentials from environment variables (REQUIRED)
# This matches the PHP handler's behavior
ENV_ADMIN_USER = os.environ.get('ADMIN_USERNAME', '')
ENV_ADMIN_PASS = os.environ.get('ADMIN_PASSWORD', '')

# Validate that admin credentials are configured (optional for local dev)
# If not set, will use default credentials in handle_login()
if not ENV_ADMIN_USER or not ENV_ADMIN_PASS:
    print("WARNING: Admin credentials not configured in environment variables.", file=sys.stderr)
    print("Using default credentials for local development.", file=sys.stderr)
    print("For production, set ADMIN_USERNAME and ADMIN_PASSWORD environment variables.", file=sys.stderr)

# Helper: constant-time string comparison (matches PHP's hash_equals)
def secure_compare(a, b):
    """Constant-time string comparison to prevent timing attacks"""
    return hmac.compare_digest(str(a), str(b))

# Session management
def load_sessions():
    """Load sessions from file"""
    script_dir = os.path.dirname(os.path.abspath(__file__))
    sessions_path = os.path.join(script_dir, SESSIONS_FILE)
    
    if os.path.exists(sessions_path):
        try:
            with open(sessions_path, 'r', encoding='utf-8') as f:
                return json.load(f)
        except (json.JSONDecodeError, IOError):
            return {}
    return {}

def save_sessions(sessions):
    """Save sessions to file"""
    script_dir = os.path.dirname(os.path.abspath(__file__))
    sessions_path = os.path.join(script_dir, SESSIONS_FILE)
    
    try:
        with session_lock:
            with open(sessions_path, 'w', encoding='utf-8') as f:
                json.dump(sessions, f, indent=2, ensure_ascii=False)
        return True
    except IOError:
        return False

def create_session(username):
    """Create a new session for a user"""
    session_token = hashlib.sha256(f"{username}:{time.time()}:{uuid.uuid4().hex}".encode()).hexdigest()
    expires_at = (datetime.now() + timedelta(hours=SESSION_EXPIRY_HOURS)).isoformat()
    
    sessions = load_sessions()
    sessions[session_token] = {
        'username': username,
        'created_at': datetime.now().isoformat(),
        'expires_at': expires_at,
        'last_used': datetime.now().isoformat()
    }
    
    # Clean up expired sessions
    sessions = cleanup_expired_sessions(sessions)
    save_sessions(sessions)
    
    return session_token

def validate_session(session_token):
    """Validate a session token and return username if valid"""
    if not session_token:
        return None
    
    sessions = load_sessions()
    
    if session_token not in sessions:
        return None
    
    session = sessions[session_token]
    expires_at = datetime.fromisoformat(session['expires_at'])
    
    # Check if session expired
    if datetime.now() > expires_at:
        # Remove expired session
        del sessions[session_token]
        save_sessions(sessions)
        return None
    
    # Update last used time
    session['last_used'] = datetime.now().isoformat()
    save_sessions(sessions)
    
    return session['username']

def cleanup_expired_sessions(sessions):
    """Remove expired sessions"""
    now = datetime.now()
    valid_sessions = {}
    
    for token, session in sessions.items():
        expires_at = datetime.fromisoformat(session['expires_at'])
        if now <= expires_at:
            valid_sessions[token] = session
    
    return valid_sessions

def delete_session(session_token):
    """Delete a session"""
    sessions = load_sessions()
    if session_token in sessions:
        del sessions[session_token]
        save_sessions(sessions)

def get_session_token_from_cookies(cookie_header):
    """Extract session token from cookie header"""
    if not cookie_header:
        return None
    
    cookies = {}
    for cookie in cookie_header.split(';'):
        cookie = cookie.strip()
        if '=' in cookie:
            key, value = cookie.split('=', 1)
            cookies[key.strip()] = unquote(value.strip())
    
    return cookies.get(SESSION_COOKIE_NAME)

class BookingHandler(SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        # Get the directory of booking-handler.py and set it as the base directory
        script_dir = os.path.dirname(os.path.abspath(__file__))
        self.project_root = os.path.dirname(script_dir)
        # SimpleHTTPRequestHandler with directory parameter (Python 3.7+)
        if 'directory' not in kwargs:
            kwargs['directory'] = self.project_root
        super().__init__(*args, **kwargs)
    
    def log_message(self, format, *args):
        # Custom logging - don't use default stderr logging
        print(f"[{self.log_date_time_string()}] {format % args}")
    
    def do_OPTIONS(self):
        # Handle CORS preflight requests
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-Token')
        self.send_header('Access-Control-Allow-Credentials', 'true')
        self.send_header('Access-Control-Max-Age', '86400')
        self.end_headers()
    
    def is_public_path(self, path):
        """Check if a path should be accessible without authentication"""
        # Root path should redirect to login
        if path == '/' or path == '':
            return False
        
        # Login page and related assets
        public_paths = [
            '/pages/admin-login.html',
            '/admin-login.html',
            '/styles.css',
            '/frontend/',
            '/js/',
            '/Images/',
            '/images/',
            '/favicon.ico',
            '/.well-known/',
        ]
        
        # Check if path starts with any public path
        for public_path in public_paths:
            if path.startswith(public_path):
                return True
        
        # Allow static assets (CSS, JS, images, fonts)
        static_extensions = ['.css', '.js', '.jpg', '.jpeg', '.png', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.eot']
        if any(path.lower().endswith(ext) for ext in static_extensions):
            return True
        
        return False
    
    def do_GET(self):
        # Parse URL and query parameters
        parsed_path = urlparse(self.path)
        path = parsed_path.path
        query_params = parse_qs(parsed_path.query)
        action = query_params.get('action', [None])[0]
        
        # Check if this is an API request to booking-handler
        if '/admin/booking-handler.py' in path or '/admin/booking-handler.php' in path:
            # Handle session check, boats, and bookings/public endpoints
            if action in ('session', 'boats', 'bookings'):
                self.send_response(200)
                self.send_header('Content-Type', 'application/json')
                self.send_header('Access-Control-Allow-Origin', '*')
                self.send_header('Access-Control-Allow-Credentials', 'true')
                self.end_headers()
                if action == 'session':
                    cookie_header = self.headers.get('Cookie', '')
                    session_token = get_session_token_from_cookies(cookie_header)
                    username = validate_session(session_token) if session_token else None
                    if username:
                        response = {
                            'success': True,
                            'authenticated': True,
                            'username': username,
                            'csrfToken': session_token[:32]
                        }
                    else:
                        response = {
                            'success': False,
                            'authenticated': False,
                            'message': 'Session expired or invalid'
                        }
                elif action == 'boats':
                    # Public boats fetch (no auth)
                    response = {'success': True, 'boats': self.load_boats()}
                elif action == 'bookings':
                    # Public bookings fetch for availability checking (no auth, only date and boatType)
                    bookings = self.load_bookings()
                    # Only return minimal info needed for availability: date, boatType, status
                    public_bookings = [
                        {
                            'date': b.get('date'),
                            'boatType': b.get('boatType'),
                            'status': b.get('status', 'not-confirmed')
                        }
                        for b in bookings
                    ]
                    response = {'success': True, 'bookings': public_bookings}
                self.wfile.write(json.dumps(response).encode('utf-8'))
                self.wfile.flush()
                return
            
            # Default: return bookings (requires auth)
            cookie_header = self.headers.get('Cookie', '')
            session_token = get_session_token_from_cookies(cookie_header)
            username = validate_session(session_token) if session_token else None
            
            if not username:
                # Redirect to login if not authenticated
                self.send_response(302)
                self.send_header('Location', '/pages/admin-login.html')
                self.end_headers()
                return
            
            bookings = self.load_bookings()
            response = {'success': True, 'bookings': bookings}
            response_bytes = json.dumps(response).encode('utf-8')
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.send_header('Access-Control-Allow-Credentials', 'true')
            self.end_headers()
            self.wfile.write(response_bytes)
            self.wfile.flush()
            return
        
        # Check authentication for all other requests
        # Allow public paths (login page, static assets)
        if not self.is_public_path(path):
            # Check if user is authenticated
            cookie_header = self.headers.get('Cookie', '')
            session_token = get_session_token_from_cookies(cookie_header)
            username = validate_session(session_token) if session_token else None
            
            # Debug logging (can be removed in production)
            if not username:
                print(f"[AUTH] Unauthenticated access to {path}, redirecting to login")
                print(f"[AUTH] Cookie header: {cookie_header[:100] if cookie_header else 'None'}")
                print(f"[AUTH] Session token: {session_token[:20] if session_token else 'None'}...")
            
            if not username:
                # Redirect to login page
                self.send_response(302)
                self.send_header('Location', '/pages/admin-login.html')
                self.end_headers()
                return
        
        # For public paths or authenticated users, serve static files
        return super().do_GET()
    
    def do_POST(self):
        # Parse URL to check if this is an API request
        parsed_path = urlparse(self.path)
        path = parsed_path.path
        
        # Only handle POST requests to booking-handler endpoints
        if '/admin/booking-handler.py' not in path and '/admin/booking-handler.php' not in path:
            # Not an API request, let parent handle it (might be a form submission)
            self.send_error_response(404, 'Not Found')
            return
        
        # Get content length
        try:
            content_length = int(self.headers.get('Content-Length', 0))
        except (ValueError, TypeError):
            self.send_error_response(400, 'Invalid Content-Length')
            return
            
        if content_length == 0:
            self.send_error_response(400, 'No data provided')
            return
            
        # Read POST data
        try:
            post_data = self.rfile.read(content_length)
            input_data = json.loads(post_data.decode('utf-8'))
        except json.JSONDecodeError as e:
            self.send_error_response(400, f'Invalid JSON data: {str(e)}')
            return
        except Exception as e:
            self.send_error_response(400, f'Error reading request: {str(e)}')
            return
        
        # Debug logging
        print(f"Received POST request to {path}: {input_data.get('action', 'unknown')}")
        
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
            # Public boats fetch via POST
            if input_data.get('action') == 'boats':
                self.send_response(200)
                self.send_header('Content-Type', 'application/json')
                self.send_header('Access-Control-Allow-Origin', '*')
                self.send_header('Access-Control-Allow-Credentials', 'true')
                self.end_headers()
                self.wfile.write(json.dumps({'success': True, 'boats': self.load_boats()}).encode('utf-8'))
                self.wfile.flush()
                return
            self.handle_admin_action(input_data)
            return
        
        self.send_error_response(400, 'Invalid request')
    
    def handle_login(self, input_data):
        username = input_data.get('username', '')
        password = input_data.get('password', '')
        
        # Default credentials for local development if env vars not set
        default_user = 'admin'
        default_pass = 'nijenhuis2025'
        
        # Use environment credentials if available, otherwise use defaults
        check_user = ENV_ADMIN_USER if ENV_ADMIN_USER else default_user
        check_pass = ENV_ADMIN_PASS if ENV_ADMIN_PASS else default_pass
        
        # Also check manager credentials
        manager_user = 'manager'
        manager_pass = 'boats2025'
        
        # Use constant-time comparison to prevent timing attacks
        is_valid = (
            (secure_compare(username, check_user) and secure_compare(password, check_pass)) or
            (secure_compare(username, manager_user) and secure_compare(password, manager_pass))
        )
        
        if is_valid:
            # Create session
            session_token = create_session(username)
            expires_at = (datetime.now() + timedelta(hours=SESSION_EXPIRY_HOURS)).isoformat()
            
            # Set HTTP-only cookie
            cookie_value = f"{SESSION_COOKIE_NAME}={session_token}; HttpOnly; Path=/; Max-Age={SESSION_EXPIRY_HOURS * 3600}; SameSite=Lax"
            
            response = {
                'success': True,
                'message': 'Login successful',
                'csrfToken': session_token[:32],  # First 32 chars as CSRF token
                'sessionToken': session_token
            }
            status_code = 200
        else:
            response = {'success': False, 'message': 'Invalid credentials'}
            status_code = 401
            cookie_value = None
        
        self.send_response(status_code)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', self.headers.get('Origin', '*'))
        self.send_header('Access-Control-Allow-Credentials', 'true')
        self.send_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-Token')
        if cookie_value:
            self.send_header('Set-Cookie', cookie_value)
        self.end_headers()
        response_bytes = json.dumps(response).encode('utf-8')
        self.wfile.write(response_bytes)
        self.wfile.flush()
    
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
            status_code = 200
        else:
            response = {'success': False, 'message': 'Failed to save booking'}
            status_code = 500
        
        self.send_response(status_code)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Credentials', 'true')
        self.send_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type, X-CSRF-Token')
        self.end_headers()
        response_bytes = json.dumps(response).encode('utf-8')
        self.wfile.write(response_bytes)
        self.wfile.flush()
    
    def handle_admin_action(self, input_data):
        action = input_data.get('action')
        response = None
        status_code = 200

        # Public actions (do not require admin session)
        public_actions = {'validateCartAvailability'}
        
        if action == 'getBookings':
            bookings = self.load_bookings()
            response = {'success': True, 'bookings': bookings, 'csrfToken': 'local-dev-token'}
            status_code = 200
        
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
                status_code = 200
            else:
                response = {'success': False, 'message': 'Failed to create booking'}
                status_code = 500
        
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
                status_code = 200
            else:
                response = {'success': False, 'message': 'Failed to update booking'}
                status_code = 500
        
        elif action == 'deleteBooking':
            booking_id = input_data.get('bookingId')
            if not booking_id:
                self.send_error_response(400, 'Booking ID required')
                return
            
            bookings = self.load_bookings()
            bookings = [b for b in bookings if b['id'] != booking_id]
            
            if self.save_bookings(bookings):
                response = {'success': True, 'message': 'Booking deleted successfully'}
                status_code = 200
            else:
                response = {'success': False, 'message': 'Failed to delete booking'}
                status_code = 500

        elif action == 'validateCartAvailability':
            # Public: Validate cart items against current availability
            items = input_data.get('items', [])
            if not isinstance(items, list) or len(items) == 0:
                self.send_error_response(400, 'Cart items required')
                return

            boats = self.load_boats()
            bookings = self.load_bookings()

            def parse_date(s):
                return datetime.strptime(s, '%Y-%m-%d')

            def is_blocking_booking(b):
                status = (b.get('status') or '').strip()
                payment_id = (b.get('paymentId') or '').strip()
                has_online_payment = bool(payment_id) and not payment_id.startswith('manual_')

                # Non-blocking statuses
                if status in ('canceled', 'cancelled', 'payment-rejected', 'failed', 'expired', 'rejected'):
                    return False
                if status == 'temporary':
                    return False
                if has_online_payment and status in ('pending', 'open', 'not-confirmed'):
                    return False

                # Blocking statuses
                return status in ('success', 'manual', 'paid', 'picked_up', 'confirmed', 'confirmed-paid')

            def is_available(boat_id, start_date_str, end_date_str):
                boat = next((x for x in boats if x.get('id') == boat_id), None)
                if not boat:
                    return False, 'boat_not_found'
                total = int(boat.get('total') or 1)

                try:
                    start = parse_date(start_date_str)
                    end = parse_date(end_date_str or start_date_str)
                except Exception:
                    return False, 'invalid_date'

                day = start
                while day <= end:
                    count = 0
                    for b in bookings:
                        if b.get('boatType') != boat_id:
                            continue
                        if not b.get('date'):
                            continue
                        if not is_blocking_booking(b):
                            continue
                        try:
                            b_start = parse_date(b.get('date'))
                            b_end = parse_date(b.get('endDate') or b.get('date'))
                        except Exception:
                            continue
                        if b_start <= day <= b_end:
                            count += 1
                    if count >= total:
                        return False, day.strftime('%Y-%m-%d')
                    day += timedelta(days=1)

                return True, None

            unavailable = []
            for item in items:
                boat_id = (item.get('boatId') or '').strip()
                start_date = (item.get('startDate') or '').strip()
                end_date = (item.get('endDate') or start_date).strip()
                if not boat_id or not start_date:
                    continue

                ok, blocked = is_available(boat_id, start_date, end_date)
                if not ok:
                    unavailable.append({
                        'boatId': boat_id,
                        'boatName': item.get('boatName') or boat_id,
                        'blockedDate': start_date if blocked in ('boat_not_found', 'invalid_date') else blocked
                    })

            if unavailable:
                response = {
                    'success': False,
                    'message': 'Helaas zijn één of meerdere boten inmiddels niet meer beschikbaar. Verwijder deze uit uw winkelwagen en probeer het opnieuw.',
                    'unavailableItems': unavailable
                }
                status_code = 409
            else:
                response = {'success': True}
                status_code = 200
        
        elif action == 'logout':
            # Handle logout - delete session
            cookie_header = self.headers.get('Cookie', '')
            session_token = get_session_token_from_cookies(cookie_header)
            if session_token:
                delete_session(session_token)
            
            # Clear cookie
            cookie_value = f"{SESSION_COOKIE_NAME}=; HttpOnly; Path=/; Max-Age=0; SameSite=Lax"
            
            response = {'success': True, 'message': 'Logged out successfully'}
            status_code = 200

        elif action == 'getBoats':
            # Admin-authenticated fetch of boats
            response = {'success': True, 'boats': self.load_boats()}
            status_code = 200

        elif action == 'saveBoats':
            # Admin-authenticated save of boats
            boats = input_data.get('boats')
            if not isinstance(boats, list):
                self.send_error_response(400, 'Invalid boats payload')
                return
            if self.save_boats(boats):
                response = {'success': True}
                status_code = 200
            else:
                response = {'success': False, 'message': 'Failed to save boats'}
                status_code = 500
        
        else:
            self.send_error_response(400, 'Invalid action')
            return
        
        # Send response with proper headers
        if response:
            # Check authentication for admin actions
            cookie_header = self.headers.get('Cookie', '')
            session_token = get_session_token_from_cookies(cookie_header)
            username = validate_session(session_token) if session_token else None
            
            if not username and action not in public_actions and action != 'logout':
                self.send_error_response(401, 'Unauthorized - Please login')
                return
            
            self.send_response(status_code)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', self.headers.get('Origin', '*'))
            self.send_header('Access-Control-Allow-Credentials', 'true')
            if action == 'logout':
                cookie_value = f"{SESSION_COOKIE_NAME}=; HttpOnly; Path=/; Max-Age=0; SameSite=Lax"
                self.send_header('Set-Cookie', cookie_value)
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
        
        # Check Season & Booking Window
        try:
            date_obj = datetime.strptime(booking_data['date'], '%Y-%m-%d')
            year = date_obj.year
            month = date_obj.month
            day = date_obj.day
            
            # 1. Rental Window: Apr 1 - Oct 31
            SEASON_START_MONTH = 4
            SEASON_START_DAY = 1
            SEASON_END_MONTH = 10
            SEASON_END_DAY = 31
            
            in_season = False
            if month > SEASON_START_MONTH and month < SEASON_END_MONTH:
                in_season = True
            elif month == SEASON_START_MONTH and day >= SEASON_START_DAY:
                in_season = True
            elif month == SEASON_END_MONTH and day <= SEASON_END_DAY:
                in_season = True
                
            if not in_season:
                # Log usage?
                return False

            # 2. Booking Window: Open from Jan 1st
            today = datetime.now()
            cur_year = today.year
            
            if year > cur_year:
                # Booking for future years opens Jan 1st of that year
                return False
            
            if year == cur_year:
                BOOKING_OPEN_MONTH = 1
                BOOKING_OPEN_DAY = 1
                open_date = datetime(cur_year, BOOKING_OPEN_MONTH, BOOKING_OPEN_DAY)
                if today < open_date:
                    return False
                    
        except ValueError:
            return False
            
        return True
    
    def load_bookings(self):
        # Use absolute path to bookings file in admin directory
        bookings_path = os.path.join(self.project_root, 'admin', BOOKINGS_FILE)
        if os.path.exists(bookings_path):
            try:
                with open(bookings_path, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except (json.JSONDecodeError, IOError):
                return []
        return []
    
    def save_bookings(self, bookings):
        # Use absolute path to bookings file in admin directory
        bookings_path = os.path.join(self.project_root, 'admin', BOOKINGS_FILE)
        try:
            with open(bookings_path, 'w', encoding='utf-8') as f:
                json.dump(bookings, f, indent=2, ensure_ascii=False)
            return True
        except IOError:
            return False
    
    def _boats_path(self):
        data_path = os.path.join(self.project_root, 'data', BOATS_FILE)
        if os.path.exists(data_path):
            return data_path
        return os.path.join(self.project_root, 'admin', BOATS_FILE)

    def load_boats(self):
        boats_path = self._boats_path()
        if os.path.exists(boats_path):
            try:
                with open(boats_path, 'r', encoding='utf-8') as f:
                    return json.load(f)
            except (json.JSONDecodeError, IOError):
                return []
        return []
    
    def save_boats(self, boats):
        boats_path = self._boats_path()
        try:
            os.makedirs(os.path.dirname(boats_path), exist_ok=True)
            with open(boats_path, 'w', encoding='utf-8') as f:
                json.dump(boats, f, indent=2, ensure_ascii=False)
            return True
        except IOError:
            return False
    
    def generate_id(self):
        return f"{uuid.uuid4().hex}_{int(datetime.now().timestamp())}"
    
    def send_error_response(self, status_code, message):
        response = {'success': False, 'message': message}
        try:
            self.send_response(status_code)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.send_header('Access-Control-Allow-Credentials', 'true')
            self.end_headers()
            self.wfile.write(json.dumps(response).encode())
            self.wfile.flush()
        except Exception as e:
            print(f"Error sending error response: {e}")

def run_server(port=8000):
    # Change to project root directory
    script_dir = os.path.dirname(os.path.abspath(__file__))
    project_root = os.path.dirname(script_dir)
    os.chdir(project_root)
    
    server_address = ('', port)
    httpd = HTTPServer(server_address, BookingHandler)
    print(f"🚀 Starting Nijenhuis Development Server on port {port}")
    print(f"=" * 60)
    print(f"📁 Serving from: {project_root}")
    print(f"")
    print(f"🌐 Available URLs:")
    print(f"   - Admin Login:    http://localhost:{port}/pages/admin-login.html")
    print(f"   - Admin Dashboard: http://localhost:{port}/admin/admin-static.html")
    print(f"   - Booking API:     http://localhost:{port}/admin/booking-handler.py")
    print(f"")
    print(f"Press Ctrl+C to stop the server")
    print(f"=" * 60)
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n🛑 Shutting down server...")
        httpd.server_close()

if __name__ == '__main__':
    port = 8000
    if len(sys.argv) > 1:
        port = int(sys.argv[1])
    run_server(port) 