#!/usr/bin/env python3
"""
Security Integration Tests for Nijenhuis Chatbot
Comprehensive tests for authentication, rate limiting, and connection security
"""

import unittest
import requests
import time
import json
import os
import sys
from unittest.mock import patch, MagicMock

# Add the project root to the path
sys.path.append(os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__)))))

from backend.chatbot.core.security_manager import SecurityManager
from backend.chatbot.core.connection_monitor import ConnectionMonitor

class TestSecurityManager(unittest.TestCase):
    """Test security manager functionality"""
    
    def setUp(self):
        self.security_manager = SecurityManager()
        self.test_api_key = "test_api_key_12345"
    
    def test_api_key_creation(self):
        """Test API key creation"""
        api_key = self.security_manager.create_api_key(
            "test_key", 
            ["chat", "config"], 
            rate_limit_override=100
        )
        
        self.assertIsNotNone(api_key)
        self.assertIn(api_key, self.security_manager.api_keys)
        
        key_info = self.security_manager.api_keys[api_key]
        self.assertEqual(key_info['name'], "test_key")
        self.assertEqual(key_info['permissions'], ["chat", "config"])
        self.assertEqual(key_info['rate_limit_override'], 100)
    
    def test_authentication_success(self):
        """Test successful authentication"""
        # Create a test API key
        api_key = self.security_manager.create_api_key("test", ["chat"])
        
        is_authenticated, message = self.security_manager.authenticate_request(api_key, "chat")
        
        self.assertTrue(is_authenticated)
        self.assertEqual(message, "Authentication successful")
    
    def test_authentication_failure(self):
        """Test authentication failure"""
        is_authenticated, message = self.security_manager.authenticate_request("invalid_key", "chat")
        
        self.assertFalse(is_authenticated)
        self.assertEqual(message, "Invalid API key")
    
    def test_permission_check(self):
        """Test permission checking"""
        api_key = self.security_manager.create_api_key("test", ["chat"])
        
        # Test with correct permission
        is_authenticated, message = self.security_manager.authenticate_request(api_key, "chat")
        self.assertTrue(is_authenticated)
        
        # Test with incorrect permission
        is_authenticated, message = self.security_manager.authenticate_request(api_key, "admin")
        self.assertFalse(is_authenticated)
        self.assertIn("Insufficient permissions", message)
    
    def test_rate_limiting(self):
        """Test rate limiting functionality"""
        identifier = "test_client"
        
        # Test normal requests with default limit
        for i in range(10):
            is_allowed, message = self.security_manager.check_rate_limit(identifier)
            self.assertTrue(is_allowed)
        
        # Test rate limit exceeded with custom key
        api_key = self.security_manager.create_api_key("rate_test", ["chat"], rate_limit_override=3)
        
        # Clear any existing rate limit data for this identifier
        if identifier in self.security_manager.rate_limits:
            self.security_manager.rate_limits[identifier].clear()
        
        # Test with the custom rate limit
        for i in range(4):
            is_allowed, message = self.security_manager.check_rate_limit(identifier, api_key)
            if i < 3:
                self.assertTrue(is_allowed, f"Request {i+1} should be allowed")
            else:
                self.assertFalse(is_allowed, f"Request {i+1} should be blocked")
                self.assertIn("Rate limit exceeded", message)
    
    def test_ip_blocking(self):
        """Test IP blocking functionality"""
        ip_address = "192.168.1.100"
        
        # Initially should be allowed
        is_allowed, message = self.security_manager.check_ip_blocking(ip_address)
        self.assertTrue(is_allowed)
        
        # Simulate failed attempts
        for i in range(6):  # Exceed max_failed_attempts (5)
            self.security_manager.handle_failed_attempt("test_user", ip_address)
        
        # Should now be blocked
        is_allowed, message = self.security_manager.check_ip_blocking(ip_address)
        self.assertFalse(is_allowed)
        self.assertEqual(message, "IP address is blocked")
    
    def test_jwt_token_generation(self):
        """Test JWT token generation and verification"""
        api_key = "test_jwt_key"
        permissions = ["chat", "config"]
        
        # Generate token
        token = self.security_manager.generate_jwt_token(api_key, permissions)
        self.assertIsNotNone(token)
        
        # Verify token
        payload = self.security_manager.verify_jwt_token(token)
        self.assertIsNotNone(payload)
        self.assertEqual(payload['api_key'], api_key)
        self.assertEqual(payload['permissions'], permissions)
    
    def test_jwt_token_expiry(self):
        """Test JWT token expiry handling"""
        # Create a token with very short expiry
        with patch.object(self.security_manager, 'jwt_expiry_hours', 0.0001):  # ~0.36 seconds
            token = self.security_manager.generate_jwt_token("test", ["chat"])
            
            # Should be valid initially
            payload = self.security_manager.verify_jwt_token(token)
            self.assertIsNotNone(payload)
            
            # Wait for expiry (use a longer sleep to ensure expiry)
            time.sleep(2)
            
            # Should be expired now
            payload = self.security_manager.verify_jwt_token(token)
            self.assertIsNone(payload)

class TestConnectionMonitor(unittest.TestCase):
    """Test connection monitor functionality"""
    
    def setUp(self):
        self.connection_monitor = ConnectionMonitor(health_check_interval=1)
    
    def test_initial_status(self):
        """Test initial connection status"""
        status = self.connection_monitor.get_connection_status()
        
        self.assertEqual(status['status'], 'healthy')
        self.assertTrue(status['is_healthy'])
        self.assertFalse(status['needs_attention'])
    
    def test_connection_health_update(self):
        """Test connection health updates"""
        # Test successful update
        self.connection_monitor.update_connection_health(success=True)
        status = self.connection_monitor.get_connection_status()
        
        self.assertEqual(status['metrics']['total_requests'], 1)
        self.assertEqual(status['metrics']['total_failures'], 0)
        self.assertEqual(status['metrics']['success_rate'], 100.0)
        
        # Test failed update
        self.connection_monitor.update_connection_health(success=False)
        status = self.connection_monitor.get_connection_status()
        
        self.assertEqual(status['metrics']['total_requests'], 2)
        self.assertEqual(status['metrics']['total_failures'], 1)
        self.assertEqual(status['metrics']['success_rate'], 50.0)
    
    def test_fallback_responses(self):
        """Test fallback response generation"""
        # Test Dutch fallback
        response = self.connection_monitor.get_fallback_response('nl', 'error')
        self.assertIn('Technische storing', response)
        self.assertIn('0522 281 528', response)
        
        # Test English fallback
        response = self.connection_monitor.get_fallback_response('en', 'offline')
        self.assertIn('temporarily offline', response)
        self.assertIn('0522 281 528', response)
        
        # Test German fallback
        response = self.connection_monitor.get_fallback_response('de', 'greeting')
        self.assertIn('nicht verfügbar', response)
        self.assertIn('0522 281 528', response)
    
    def test_connection_status_transitions(self):
        """Test connection status transitions"""
        # Simulate multiple failures to trigger status change
        for i in range(3):
            self.connection_monitor.update_connection_health(success=False)
        
        status = self.connection_monitor.get_connection_status()
        self.assertEqual(status['status'], 'offline')
        self.assertFalse(status['is_healthy'])
        self.assertTrue(status['needs_attention'])

class TestSecurityIntegration(unittest.TestCase):
    """Integration tests for security features"""
    
    def setUp(self):
        self.base_url = "http://localhost:5001"
        self.test_api_key = None
    
    def test_health_endpoint_accessibility(self):
        """Test that health endpoint is accessible without authentication"""
        try:
            response = requests.get(f"{self.base_url}/api/health", timeout=5)
            if response.status_code == 404:
                self.skipTest("Server not running or endpoint not found")
            self.assertEqual(response.status_code, 200)
            
            data = response.json()
            self.assertIn('status', data)
            self.assertIn('service', data)
            self.assertIn('connection', data)
            self.assertIn('security', data)
        except requests.exceptions.RequestException:
            self.skipTest("Server not running")
    
    def test_chat_endpoint_authentication_required(self):
        """Test that chat endpoint requires authentication"""
        try:
            response = requests.post(
                f"{self.base_url}/api/chat",
                json={"message": "test"},
                timeout=5
            )
            if response.status_code == 404:
                self.skipTest("Server not running or endpoint not found")
            self.assertEqual(response.status_code, 401)
            
            data = response.json()
            self.assertIn('error', data)
            self.assertIn('API key required', data['error'])
        except requests.exceptions.RequestException:
            self.skipTest("Server not running")
    
    def test_rate_limiting_integration(self):
        """Test rate limiting in integration"""
        # This test would require a running server with a valid API key
        # For now, we'll test the logic without actual HTTP requests
        security_manager = SecurityManager()
        
        # Create a test API key with low rate limit
        api_key = security_manager.create_api_key("rate_test", ["chat"], rate_limit_override=3)
        
        identifier = "test_client"
        
        # Test rate limiting
        for i in range(5):
            is_allowed, message = security_manager.check_rate_limit(identifier, api_key)
            if i < 3:
                self.assertTrue(is_allowed)
            else:
                self.assertFalse(is_allowed)
                self.assertIn("Rate limit exceeded", message)
    
    def test_security_logging(self):
        """Test security event logging"""
        security_manager = SecurityManager()
        
        # Trigger a security event
        security_manager._log_security_event('test_event', {'test': 'data'})
        
        # Check that event was logged
        self.assertEqual(len(security_manager.security_log), 1)
        self.assertEqual(security_manager.security_log[0]['type'], 'test_event')
        self.assertEqual(security_manager.security_log[0]['details']['test'], 'data')

class TestErrorHandling(unittest.TestCase):
    """Test error handling and fallback mechanisms"""
    
    def test_invalid_input_handling(self):
        """Test handling of invalid input"""
        security_manager = SecurityManager()
        
        # Test empty API key
        is_authenticated, message = security_manager.authenticate_request("", "chat")
        self.assertFalse(is_authenticated)
        self.assertEqual(message, "API key required")
        
        # Test None API key
        is_authenticated, message = security_manager.authenticate_request(None, "chat")
        self.assertFalse(is_authenticated)
        self.assertEqual(message, "API key required")
    
    def test_connection_monitor_error_handling(self):
        """Test connection monitor error handling"""
        connection_monitor = ConnectionMonitor()
        
        # Test with invalid health check endpoint
        connection_monitor.base_url = "http://invalid-url-that-does-not-exist"
        
        # Should handle error gracefully
        try:
            connection_monitor._perform_health_check()
        except Exception as e:
            self.fail(f"Connection monitor should handle errors gracefully: {e}")
    
    def test_fallback_mechanisms(self):
        """Test fallback mechanisms"""
        connection_monitor = ConnectionMonitor()
        
        # Test fallback response for unknown language
        response = connection_monitor.get_fallback_response('unknown_lang', 'error')
        self.assertIsNotNone(response)
        self.assertIn('Technische storing', response)  # Should default to Dutch
        
        # Test fallback response for unknown type
        response = connection_monitor.get_fallback_response('nl', 'unknown_type')
        self.assertIsNotNone(response)
        self.assertIn('Technische storing', response)  # Should default to error

def run_security_tests():
    """Run all security tests"""
    print("🔒 Running Security Integration Tests...")
    print("=" * 50)
    
    # Create test suite
    test_suite = unittest.TestSuite()
    
    # Add test cases
    test_classes = [
        TestSecurityManager,
        TestConnectionMonitor,
        TestSecurityIntegration,
        TestErrorHandling
    ]
    
    for test_class in test_classes:
        tests = unittest.TestLoader().loadTestsFromTestCase(test_class)
        test_suite.addTests(tests)
    
    # Run tests
    runner = unittest.TextTestRunner(verbosity=2)
    result = runner.run(test_suite)
    
    # Print summary
    print("\n" + "=" * 50)
    print(f"Tests run: {result.testsRun}")
    print(f"Failures: {len(result.failures)}")
    print(f"Errors: {len(result.errors)}")
    
    if result.failures:
        print("\nFailures:")
        for test, traceback in result.failures:
            print(f"  - {test}: {traceback}")
    
    if result.errors:
        print("\nErrors:")
        for test, traceback in result.errors:
            print(f"  - {test}: {traceback}")
    
    success = len(result.failures) == 0 and len(result.errors) == 0
    print(f"\n{'✅ All tests passed!' if success else '❌ Some tests failed!'}")
    
    return success

if __name__ == "__main__":
    success = run_security_tests()
    sys.exit(0 if success else 1)
