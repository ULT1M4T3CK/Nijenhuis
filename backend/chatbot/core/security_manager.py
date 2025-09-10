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
from datetime import datetime, timedelta
from typing import Dict, Any, Optional, Tuple
from collections import defaultdict, deque
import secrets
import jwt
from functools import wraps

class SecurityManager:
    """Comprehensive security management for chatbot API"""
    
    def __init__(self):
        self.api_keys = self._load_api_keys()
        self.rate_limits = defaultdict(lambda: deque())
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
    
    def _load_api_keys(self) -> Dict[str, Dict[str, Any]]:
        """Load API keys from secure storage"""
        keys_file = os.path.join(os.path.dirname(__file__), '..', '..', '..', 'config', 'api_keys.json')
        
        if os.path.exists(keys_file):
            try:
                with open(keys_file, 'r') as f:
                    return json.load(f)
            except Exception as e:
                print(f"Warning: Could not load API keys: {e}")
        
        # Generate default API key for development
        default_key = self._generate_api_key()
        default_keys = {
            default_key: {
                'name': 'default_development_key',
                'permissions': ['chat', 'health', 'config'],
                'created_at': datetime.now().isoformat(),
                'last_used': None,
                'usage_count': 0,
                'rate_limit_override': None
            }
        }
        
        # Save default key
        self._save_api_keys(default_keys)
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
    
    def _generate_api_key(self) -> str:
        """Generate a secure API key"""
        return secrets.token_urlsafe(32)
    
    def _get_or_create_jwt_secret(self) -> str:
        """Get or create JWT secret"""
        secret_file = os.path.join(os.path.dirname(__file__), '..', '..', '..', 'config', 'jwt_secret.txt')
        
        if os.path.exists(secret_file):
            try:
                with open(secret_file, 'r') as f:
                    return f.read().strip()
            except Exception as e:
                print(f"Warning: Could not load JWT secret: {e}")
        
        # Generate new secret
        secret = secrets.token_urlsafe(64)
        os.makedirs(os.path.dirname(secret_file), exist_ok=True)
        
        try:
            with open(secret_file, 'w') as f:
                f.write(secret)
            os.chmod(secret_file, 0o600)  # Restrict permissions
        except Exception as e:
            print(f"Error saving JWT secret: {e}")
        
        return secret
    
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
            payload = jwt.decode(token, self.jwt_secret, algorithms=['HS256'], options={"verify_exp": True})
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
    
    def check_rate_limit(self, identifier: str, api_key: str = None) -> Tuple[bool, str]:
        """Check rate limiting for requests"""
        current_time = time.time()
        
        # Get rate limit for this API key
        rate_limit = self.max_requests_per_minute
        if api_key and api_key in self.api_keys:
            key_info = self.api_keys[api_key]
            if key_info.get('rate_limit_override'):
                rate_limit = key_info['rate_limit_override']
        
        # Clean old entries
        while self.rate_limits[identifier] and self.rate_limits[identifier][0] < current_time - 60:
            self.rate_limits[identifier].popleft()
        
        # Check if limit exceeded
        if len(self.rate_limits[identifier]) >= rate_limit:
            self._log_security_event('rate_limit_exceeded', {
                'identifier': identifier,
                'limit': rate_limit,
                'api_key': api_key[:10] + '...' if api_key else 'anonymous'
            })
            return False, f"Rate limit exceeded. Max {rate_limit} requests per minute."
        
        # Add current request
        self.rate_limits[identifier].append(current_time)
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
