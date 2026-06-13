#!/usr/bin/env python3
"""
Security Manager for Nijenhuis Chatbot
Handles authentication, rate limiting, and security monitoring
"""

import hashlib
import hmac
import time
import json
import os
import tempfile
from datetime import datetime, timedelta
from typing import Dict, Any, Optional, Tuple
from collections import defaultdict, deque
import secrets
import jwt
from functools import wraps

try:
    import fcntl  # POSIX only
except ImportError:  # pragma: no cover - Windows fallback
    fcntl = None

class SecurityManager:
    """Comprehensive security management for chatbot API"""
    
    def __init__(self):
        self.api_keys = self._load_api_keys()
        self.rate_limits = defaultdict(lambda: deque())  # Per-minute tracking
        self.rate_limits_hour = defaultdict(lambda: deque())  # Per-hour tracking
        self.failed_attempts = defaultdict(int)
        self.blocked_ips = set()
        self.security_log = []
        
        # Security configuration
        self.max_requests_per_minute = 60
        self.max_requests_per_hour = 1000
        self.max_failed_attempts = 5
        self.block_duration_minutes = 15
        self.jwt_secret = self._get_or_create_jwt_secret()
        self.jwt_expiry_hours = 24
        
        # Connection monitoring
        self.connection_health = {
            'last_heartbeat': time.time(),
            'active_connections': 0,
            'total_requests': 0,
            'failed_requests': 0,
            'uptime_start': time.time()
        }
    
        # Start periodic cleanup for rate limits (every 5 minutes)
        self._start_cleanup_thread()
    
    def _load_api_keys(self) -> Dict[str, Dict[str, Any]]:
        """Load API keys from secure storage.

        Historically this bootstrapped a default key with the ``config``
        permission whenever ``api_keys.json`` was missing. That meant a fresh
        deploy -- or any restart where the file was not yet provisioned --
        briefly exposed admin-level endpoints through a freshly-minted, hard-
        to-audit secret. We now fail closed: if no key file exists and no
        explicit ``CHATBOT_ALLOW_AUTOBOOTSTRAP=1`` override is set, we raise
        on startup so the operator must provision keys deliberately.
        """
        keys_file = os.path.join(os.path.dirname(__file__), '..', '..', '..', 'config', 'api_keys.json')

        if os.path.exists(keys_file):
            try:
                with open(keys_file, 'r') as f:
                    data = json.load(f)
                if not isinstance(data, dict) or not data:
                    raise RuntimeError('api_keys.json is empty or not a JSON object')
                return data
            except Exception as e:
                raise RuntimeError(f"Failed to load API keys from {keys_file}: {e}") from e

        if os.environ.get('CHATBOT_ALLOW_AUTOBOOTSTRAP') != '1':
            raise RuntimeError(
                'api_keys.json is missing and CHATBOT_ALLOW_AUTOBOOTSTRAP is not set. '
                'Provision the key file before starting the chatbot API.'
            )

        # Dev-only bootstrap. Never grants "config" permission; operators must
        # add that explicitly to api_keys.json once they have reviewed it.
        default_key = self._generate_api_key()
        default_keys = {
            default_key: {
                'name': 'default_development_key',
                'permissions': ['chat', 'health'],
                'created_at': datetime.now().isoformat(),
                'last_used': None,
                'usage_count': 0,
                'rate_limit_override': None
            }
        }
        self._save_api_keys(default_keys)
        print(
            'WARNING: Generated a development-only API key in '
            f'{keys_file}. Rotate or replace this before exposing the service.'
        )
        return default_keys
    
    def _save_api_keys(self, keys: Dict[str, Dict[str, Any]]):
        """Save API keys to secure storage"""
        keys_file = os.path.join(os.path.dirname(__file__), '..', '..', '..', 'config', 'api_keys.json')
        os.makedirs(os.path.dirname(keys_file), exist_ok=True)
        
        try:
            with open(keys_file, 'w') as f:
                json.dump(keys, f, indent=2)
        except Exception as e:
            print(f"Error saving API keys: {e}")
    
    def _start_cleanup_thread(self):
        """Start background thread for periodic cleanup of stale rate limit entries"""
        import threading
        
        def cleanup_loop():
            while True:
                try:
                    time.sleep(300)  # Clean every 5 minutes
                    self._cleanup_stale_rate_limits()
                except Exception as e:
                    print(f"Error in cleanup thread: {e}")
        
        cleanup_thread = threading.Thread(target=cleanup_loop, daemon=True)
        cleanup_thread.start()
    
    def _cleanup_stale_rate_limits(self):
        """Remove stale rate limit entries to prevent memory leaks"""
        current_time = time.time()
        stale_minute_keys = []
        stale_hour_keys = []
        
        # Find stale per-minute entries (older than 2 minutes)
        for key, timestamps in list(self.rate_limits.items()):
            if not timestamps or (current_time - timestamps[-1]) > 120:
                stale_minute_keys.append(key)
        
        # Find stale per-hour entries (older than 2 hours)
        for key, timestamps in list(self.rate_limits_hour.items()):
            if not timestamps or (current_time - timestamps[-1]) > 7200:
                stale_hour_keys.append(key)
        
        # Remove stale entries
        for key in stale_minute_keys:
            del self.rate_limits[key]
        
        for key in stale_hour_keys:
            del self.rate_limits_hour[key]
        
        # Also clean up failed attempts for entries that haven't failed recently
        stale_failed = [k for k, v in self.failed_attempts.items() if v == 0]
        for key in stale_failed:
            del self.failed_attempts[key]
        
        if stale_minute_keys or stale_hour_keys:
            print(f"🧹 Cleaned up {len(stale_minute_keys)} minute + {len(stale_hour_keys)} hour rate limit entries")
    
    def _generate_api_key(self) -> str:
        """Generate a secure API key"""
        return secrets.token_urlsafe(32)
    
    def _get_or_create_jwt_secret(self) -> str:
        """Get JWT secret from environment variable"""
        jwt_secret = os.environ.get('JWT_SECRET')
        
        if jwt_secret:
            if len(jwt_secret) < 64:
                print(f"Warning: JWT_SECRET should be at least 64 characters for security. Current length: {len(jwt_secret)}")
            return jwt_secret
        
        # Fallback: try to read from file (for backward compatibility during migration)
        secret_file = os.path.join(os.path.dirname(__file__), '..', '..', '..', 'config', 'jwt_secret.txt')
        if os.path.exists(secret_file):
            try:
                with open(secret_file, 'r') as f:
                    secret = f.read().strip()
                    print("Warning: Using JWT secret from file. Please migrate to JWT_SECRET environment variable.")
                    return secret
            except Exception as e:
                print(f"Warning: Could not load JWT secret from file: {e}")
        
        # Last resort: generate new secret (development only)
        print("ERROR: JWT_SECRET environment variable not set. Generating temporary secret for development.")
        print("WARNING: This is insecure for production. Please set JWT_SECRET environment variable.")
        return secrets.token_urlsafe(64)
    
    def generate_jwt_token(self, api_key: str, permissions: list) -> str:
        """Generate JWT token for authenticated requests"""
        now = datetime.now()
        payload = {
            'api_key': api_key,
            'permissions': permissions,
            'iat': int(now.timestamp()),
            'exp': int((now + timedelta(hours=self.jwt_expiry_hours)).timestamp())
        }
        
        return jwt.encode(payload, self.jwt_secret, algorithm='HS256')
    
    def verify_jwt_token(self, token: str) -> Optional[Dict[str, Any]]:
        """Verify JWT token and return payload"""
        try:
            # Note: verify_aud=False because we do audience validation separately in server.py
            # to allow flexible origin matching (e.g., 'http://localhost:5000' in 'http://localhost:5000/page')
            payload = jwt.decode(
                token, 
                self.jwt_secret, 
                algorithms=['HS256'], 
                options={"verify_exp": True, "verify_aud": False}
            )
            return payload
        except jwt.ExpiredSignatureError:
            self._log_security_event('jwt_expired', {'token': token[:20] + '...'})
            return None
        except jwt.InvalidTokenError as e:
            self._log_security_event('jwt_invalid', {'token': token[:20] + '...', 'error': str(e)})
            return None
    
    def authenticate_request(self, api_key: str, required_permission: str = 'chat') -> Tuple[bool, str]:
        """Authenticate API request"""
        if not api_key:
            return False, "API key required"
        
        # Check if API key exists
        if api_key not in self.api_keys:
            self._log_security_event('invalid_api_key', {'api_key': api_key[:10] + '...'})
            return False, "Invalid API key"
        
        key_info = self.api_keys[api_key]
        
        # Check permissions
        if required_permission not in key_info.get('permissions', []):
            self._log_security_event('insufficient_permissions', {
                'api_key': api_key[:10] + '...',
                'required': required_permission,
                'available': key_info.get('permissions', [])
            })
            return False, f"Insufficient permissions. Required: {required_permission}"
        
        # Update usage statistics
        key_info['last_used'] = datetime.now().isoformat()
        key_info['usage_count'] = key_info.get('usage_count', 0) + 1
        self._save_api_keys(self.api_keys)
        
        return True, "Authentication successful"
    
    def _shared_rate_limit_check(self, identifier: str, max_per_minute: int) -> bool:
        """File-backed per-minute rate limit that is consistent across
        gunicorn workers. Returns True if the caller is under the limit
        and records this hit. Falls back to "allow" on filesystem errors
        so a broken tmp dir doesn't take the service down.
        """
        if fcntl is None:
            return True
        bucket = os.path.join(tempfile.gettempdir(),
                              'nijenhuis_chatbot_rl_' + hashlib.sha1(identifier.encode()).hexdigest())
        try:
            fd = os.open(bucket, os.O_RDWR | os.O_CREAT, 0o600)
        except OSError:
            return True
        try:
            fcntl.flock(fd, fcntl.LOCK_EX)
            try:
                raw = os.read(fd, 4096).decode('utf-8') or '{}'
                data = json.loads(raw) if raw.strip() else {}
            except Exception:
                data = {}
            now = time.time()
            window_start = data.get('window_start', 0)
            count = data.get('count', 0)
            if now - window_start >= 60:
                window_start = now
                count = 0
            if count >= max_per_minute:
                return False
            data = {'window_start': window_start, 'count': count + 1}
            os.lseek(fd, 0, os.SEEK_SET)
            os.ftruncate(fd, 0)
            os.write(fd, json.dumps(data).encode('utf-8'))
            return True
        finally:
            try:
                fcntl.flock(fd, fcntl.LOCK_UN)
            finally:
                os.close(fd)

    def check_rate_limit(self, identifier: str, api_key: str = None) -> Tuple[bool, str]:
        """Check rate limiting for requests (both per-minute and per-hour)"""
        current_time = time.time()
        
        # Get rate limits for this API key
        minute_limit = self.max_requests_per_minute
        hour_limit = self.max_requests_per_hour
        
        if api_key and api_key in self.api_keys:
            key_info = self.api_keys[api_key]
            if key_info.get('rate_limit_override'):
                # Override applies to per-minute limit
                minute_limit = key_info['rate_limit_override']
                # Hourly limit is typically 10x the minute limit, but cap at configured max
                hour_limit = min(key_info['rate_limit_override'] * 10, self.max_requests_per_hour)

        # Cross-worker per-minute gate. The in-memory counters below still
        # enforce per-hour / per-worker behavior, but this ensures that a
        # burst of requests spread across gunicorn workers is still counted.
        if not self._shared_rate_limit_check(identifier, minute_limit):
            self._log_security_event('rate_limit_exceeded', {
                'identifier': identifier,
                'limit': minute_limit,
                'window': 'minute_shared',
                'api_key': api_key[:10] + '...' if api_key else 'anonymous'
            })
            return False, f"Rate limit exceeded. Max {minute_limit} requests per minute."
        
        # Clean old entries from per-minute tracking (older than 60 seconds)
        while self.rate_limits[identifier] and self.rate_limits[identifier][0] < current_time - 60:
            self.rate_limits[identifier].popleft()
        
        # Clean old entries from per-hour tracking (older than 3600 seconds)
        while self.rate_limits_hour[identifier] and self.rate_limits_hour[identifier][0] < current_time - 3600:
            self.rate_limits_hour[identifier].popleft()
        
        # Check per-minute limit
        if len(self.rate_limits[identifier]) >= minute_limit:
            self._log_security_event('rate_limit_exceeded', {
                'identifier': identifier,
                'limit': minute_limit,
                'window': 'minute',
                'api_key': api_key[:10] + '...' if api_key else 'anonymous'
            })
            return False, f"Rate limit exceeded. Max {minute_limit} requests per minute."
        
        # Check per-hour limit
        if len(self.rate_limits_hour[identifier]) >= hour_limit:
            self._log_security_event('rate_limit_exceeded', {
                'identifier': identifier,
                'limit': hour_limit,
                'window': 'hour',
                'api_key': api_key[:10] + '...' if api_key else 'anonymous'
            })
            return False, f"Rate limit exceeded. Max {hour_limit} requests per hour."
        
        # Add current request to both tracking windows
        self.rate_limits[identifier].append(current_time)
        self.rate_limits_hour[identifier].append(current_time)
        
        return True, "Rate limit check passed"
    
    def check_ip_blocking(self, ip_address: str) -> Tuple[bool, str]:
        """Check if IP address is blocked"""
        if ip_address in self.blocked_ips:
            return False, "IP address is blocked"
        return True, "IP address allowed"
    
    def handle_failed_attempt(self, identifier: str, ip_address: str):
        """Handle failed authentication attempt"""
        self.failed_attempts[identifier] += 1
        
        if self.failed_attempts[identifier] >= self.max_failed_attempts:
            self.blocked_ips.add(ip_address)
            self._log_security_event('ip_blocked', {
                'ip': ip_address,
                'identifier': identifier,
                'failed_attempts': self.failed_attempts[identifier]
            })
            
            # Schedule unblocking
            def unblock_ip():
                time.sleep(self.block_duration_minutes * 60)
                self.blocked_ips.discard(ip_address)
                self.failed_attempts[identifier] = 0
                self._log_security_event('ip_unblocked', {'ip': ip_address})
            
            import threading
            threading.Thread(target=unblock_ip, daemon=True).start()
    
    def _log_security_event(self, event_type: str, details: Dict[str, Any]):
        """Log security events"""
        event = {
            'timestamp': datetime.now().isoformat(),
            'type': event_type,
            'details': details
        }
        
        self.security_log.append(event)
        
        # Keep only last 1000 events
        if len(self.security_log) > 1000:
            self.security_log = self.security_log[-1000:]
        
        # Log to console in development
        print(f"🔒 Security Event: {event_type} - {details}")
    
    def get_security_stats(self) -> Dict[str, Any]:
        """Get security statistics"""
        current_time = time.time()
        uptime = current_time - self.connection_health['uptime_start']
        
        return {
            'uptime_seconds': uptime,
            'uptime_human': str(timedelta(seconds=int(uptime))),
            'active_connections': self.connection_health['active_connections'],
            'total_requests': self.connection_health['total_requests'],
            'failed_requests': self.connection_health['failed_requests'],
            'success_rate': (
                (self.connection_health['total_requests'] - self.connection_health['failed_requests']) 
                / max(self.connection_health['total_requests'], 1) * 100
            ),
            'blocked_ips_count': len(self.blocked_ips),
            'active_rate_limits': len(self.rate_limits),
            'recent_security_events': self.security_log[-10:],
            'api_keys_count': len(self.api_keys)
        }
    
    def update_connection_health(self, success: bool = True):
        """Update connection health metrics"""
        self.connection_health['last_heartbeat'] = time.time()
        self.connection_health['total_requests'] += 1
        
        if not success:
            self.connection_health['failed_requests'] += 1
    
    def create_api_key(self, name: str, permissions: list, rate_limit_override: int = None) -> str:
        """Create a new API key"""
        api_key = self._generate_api_key()
        
        self.api_keys[api_key] = {
            'name': name,
            'permissions': permissions,
            'created_at': datetime.now().isoformat(),
            'last_used': None,
            'usage_count': 0,
            'rate_limit_override': rate_limit_override
        }
        
        self._save_api_keys(self.api_keys)
        self._log_security_event('api_key_created', {
            'name': name,
            'permissions': permissions,
            'key_preview': api_key[:10] + '...'
        })
        
        return api_key
    
    def revoke_api_key(self, api_key: str) -> bool:
        """Revoke an API key"""
        if api_key in self.api_keys:
            key_info = self.api_keys[api_key]
            del self.api_keys[api_key]
            self._save_api_keys(self.api_keys)
            
            self._log_security_event('api_key_revoked', {
                'name': key_info.get('name', 'unknown'),
                'key_preview': api_key[:10] + '...'
            })
            return True
        return False

def require_auth(permission: str = 'chat'):
    """Decorator for requiring authentication"""
    def decorator(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            # This would be used in Flask routes
            # Implementation depends on Flask context
            return func(*args, **kwargs)
        return wrapper
    return decorator

def require_rate_limit(identifier_func=None):
    """Decorator for rate limiting"""
    def decorator(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            # This would be used in Flask routes
            # Implementation depends on Flask context
            return func(*args, **kwargs)
        return wrapper
    return decorator

# Global security manager instance
security_manager = SecurityManager()

def get_security_manager() -> SecurityManager:
    """Get the global security manager instance"""
    return security_manager
