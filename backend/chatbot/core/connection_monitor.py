#!/usr/bin/env python3
"""
Connection Monitor for Nijenhuis Chatbot
Monitors connection health, implements automatic reconnection, and provides fallback mechanisms
"""

import time
import threading
import json
import os
import requests
from datetime import datetime, timedelta
from typing import Dict, Any, Optional, Callable, List
from dataclasses import dataclass
from enum import Enum
import logging

class ConnectionStatus(Enum):
    """Connection status enumeration"""
    HEALTHY = "healthy"
    DEGRADED = "degraded"
    UNHEALTHY = "unhealthy"
    OFFLINE = "offline"
    RECONNECTING = "reconnecting"

@dataclass
class ConnectionMetrics:
    """Connection metrics data class"""
    status: ConnectionStatus
    response_time_ms: float
    success_rate: float
    last_successful_request: Optional[datetime]
    last_failed_request: Optional[datetime]
    consecutive_failures: int
    total_requests: int
    total_failures: int
    uptime_seconds: float

class ConnectionMonitor:
    """Monitors and manages chatbot connections"""
    
    def __init__(self, health_check_interval: int = 30, max_retries: int = 3):
        self.health_check_interval = health_check_interval
        self.max_retries = max_retries
        
        # Connection state
        self.status = ConnectionStatus.HEALTHY
        self.metrics = ConnectionMetrics(
            status=ConnectionStatus.HEALTHY,
            response_time_ms=0.0,
            success_rate=100.0,
            last_successful_request=None,
            last_failed_request=None,
            consecutive_failures=0,
            total_requests=0,
            total_failures=0,
            uptime_seconds=0.0
        )
        
        # Connection monitoring
        self.connection_health = {
            'last_heartbeat': time.time(),
            'active_connections': 0,
            'total_requests': 0,
            'failed_requests': 0,
            'uptime_start': time.time()
        }
        
        # Monitoring configuration
        self.health_check_endpoints = [
            '/api/health',
            '/api/chat'
        ]
        self.base_url = 'http://localhost:5001'
        self.timeout_seconds = 10
        
        # Callbacks for status changes
        self.status_change_callbacks: List[Callable] = []
        self.health_check_callbacks: List[Callable] = []
        
        # Monitoring thread
        self.monitoring_thread = None
        self.is_monitoring = False
        self.start_time = time.time()
        
        # Fallback mechanisms
        self.fallback_responses = {
            'nl': {
                'greeting': 'Hallo! Ik ben tijdelijk niet beschikbaar. Bel direct: 0522 281 528',
                'error': 'Technische storing. Bel direct: 0522 281 528',
                'offline': 'Onze chatbot is tijdelijk offline. Bel direct: 0522 281 528'
            },
            'en': {
                'greeting': 'Hello! I am temporarily unavailable. Call directly: 0522 281 528',
                'error': 'Technical issue. Call directly: 0522 281 528',
                'offline': 'Our chatbot is temporarily offline. Call directly: 0522 281 528'
            },
            'de': {
                'greeting': 'Hallo! Ich bin vorübergehend nicht verfügbar. Rufen Sie direkt an: 0522 281 528',
                'error': 'Technisches Problem. Rufen Sie direkt an: 0522 281 528',
                'offline': 'Unser Chatbot ist vorübergehend offline. Rufen Sie direkt an: 0522 281 528'
            }
        }
        
        # Logging
        self.logger = logging.getLogger(__name__)
        self.setup_logging()
    
    def setup_logging(self):
        """Setup logging for connection monitoring"""
        log_file = os.path.join(os.path.dirname(__file__), '..', '..', '..', 'logs', 'connection_monitor.log')
        os.makedirs(os.path.dirname(log_file), exist_ok=True)
        
        handler = logging.FileHandler(log_file)
        formatter = logging.Formatter('%(asctime)s - %(name)s - %(levelname)s - %(message)s')
        handler.setFormatter(formatter)
        
        self.logger.addHandler(handler)
        self.logger.setLevel(logging.INFO)
    
    def start_monitoring(self):
        """Start connection monitoring"""
        if self.is_monitoring:
            return
        
        self.is_monitoring = True
        self.monitoring_thread = threading.Thread(target=self._monitoring_loop, daemon=True)
        self.monitoring_thread.start()
        
        self.logger.info("Connection monitoring started")
    
    def stop_monitoring(self):
        """Stop connection monitoring"""
        self.is_monitoring = False
        if self.monitoring_thread:
            self.monitoring_thread.join(timeout=5)
        
        self.logger.info("Connection monitoring stopped")
    
    def _monitoring_loop(self):
        """Main monitoring loop"""
        while self.is_monitoring:
            try:
                self._perform_health_check()
                time.sleep(self.health_check_interval)
            except Exception as e:
                self.logger.error(f"Error in monitoring loop: {e}")
                time.sleep(self.health_check_interval)
    
    def _perform_health_check(self):
        """Perform health check on chatbot endpoints"""
        start_time = time.time()
        success = False
        
        for endpoint in self.health_check_endpoints:
            try:
                url = f"{self.base_url}{endpoint}"
                response = requests.get(url, timeout=self.timeout_seconds)
                
                if response.status_code == 200:
                    success = True
                    break
                    
            except requests.exceptions.RequestException as e:
                self.logger.warning(f"Health check failed for {endpoint}: {e}")
                continue
        
        response_time = (time.time() - start_time) * 1000  # Convert to milliseconds
        self._update_metrics(success, response_time)
        
        # Notify callbacks
        for callback in self.health_check_callbacks:
            try:
                callback(self.metrics)
            except Exception as e:
                self.logger.error(f"Error in health check callback: {e}")
    
    def _update_metrics(self, success: bool, response_time_ms: float):
        """Update connection metrics"""
        self.metrics.total_requests += 1
        self.metrics.response_time_ms = response_time_ms
        self.metrics.uptime_seconds = time.time() - self.start_time
        
        if success:
            self.metrics.consecutive_failures = 0
            self.metrics.last_successful_request = datetime.now()
            
            # Update status based on response time
            if response_time_ms < 1000:  # Less than 1 second
                new_status = ConnectionStatus.HEALTHY
            elif response_time_ms < 3000:  # Less than 3 seconds
                new_status = ConnectionStatus.DEGRADED
            else:
                new_status = ConnectionStatus.UNHEALTHY
        else:
            self.metrics.total_failures += 1
            self.metrics.consecutive_failures += 1
            self.metrics.last_failed_request = datetime.now()
            
            # Update status based on consecutive failures
            if self.metrics.consecutive_failures >= 3:
                new_status = ConnectionStatus.OFFLINE
            elif self.metrics.consecutive_failures >= 2:
                new_status = ConnectionStatus.UNHEALTHY
            else:
                new_status = ConnectionStatus.DEGRADED
        
        # Calculate success rate
        self.metrics.success_rate = (
            (self.metrics.total_requests - self.metrics.total_failures) 
            / self.metrics.total_requests * 100
        )
        
        # Check for status change
        if new_status != self.status:
            old_status = self.status
            self.status = new_status
            self.metrics.status = new_status
            
            self.logger.info(f"Connection status changed: {old_status.value} -> {new_status.value}")
            
            # Notify status change callbacks
            for callback in self.status_change_callbacks:
                try:
                    callback(old_status, new_status, self.metrics)
                except Exception as e:
                    self.logger.error(f"Error in status change callback: {e}")
    
    def update_connection_health(self, success: bool = True):
        """Update connection health metrics"""
        self.connection_health['last_heartbeat'] = time.time()
        self.connection_health['total_requests'] += 1
        
        if not success:
            self.connection_health['failed_requests'] += 1
            self.metrics.consecutive_failures += 1
            self.metrics.total_failures += 1
            self.metrics.last_failed_request = datetime.now()
        else:
            self.metrics.consecutive_failures = 0
            self.metrics.last_successful_request = datetime.now()
        
        # Update metrics
        self.metrics.total_requests = self.connection_health['total_requests']
        self.metrics.total_failures = self.connection_health['failed_requests']
        self.metrics.uptime_seconds = time.time() - self.start_time
        
        # Calculate success rate
        self.metrics.success_rate = (
            (self.metrics.total_requests - self.metrics.total_failures) 
            / max(self.metrics.total_requests, 1) * 100
        )
        
        # Update status based on consecutive failures
        if self.metrics.consecutive_failures >= 3:
            new_status = ConnectionStatus.OFFLINE
        elif self.metrics.consecutive_failures >= 2:
            new_status = ConnectionStatus.UNHEALTHY
        elif self.metrics.consecutive_failures >= 1:
            new_status = ConnectionStatus.DEGRADED
        else:
            new_status = ConnectionStatus.HEALTHY
        
        # Check for status change
        if new_status != self.status:
            old_status = self.status
            self.status = new_status
            self.metrics.status = new_status
            
            self.logger.info(f"Connection status changed: {old_status.value} -> {new_status.value}")
            
            # Notify status change callbacks
            for callback in self.status_change_callbacks:
                try:
                    callback(old_status, new_status, self.metrics)
                except Exception as e:
                    self.logger.error(f"Error in status change callback: {e}")
    
    def add_status_change_callback(self, callback: Callable):
        """Add callback for status changes"""
        self.status_change_callbacks.append(callback)
    
    def add_health_check_callback(self, callback: Callable):
        """Add callback for health checks"""
        self.health_check_callbacks.append(callback)
    
    def get_connection_status(self) -> Dict[str, Any]:
        """Get current connection status"""
        return {
            'status': self.status.value,
            'metrics': {
                'response_time_ms': self.metrics.response_time_ms,
                'success_rate': self.metrics.success_rate,
                'consecutive_failures': self.metrics.consecutive_failures,
                'total_requests': self.metrics.total_requests,
                'total_failures': self.metrics.total_failures,
                'uptime_seconds': self.metrics.uptime_seconds,
                'uptime_human': str(timedelta(seconds=int(self.metrics.uptime_seconds))),
                'last_successful_request': (
                    self.metrics.last_successful_request.isoformat() 
                    if self.metrics.last_successful_request else None
                ),
                'last_failed_request': (
                    self.metrics.last_failed_request.isoformat() 
                    if self.metrics.last_failed_request else None
                )
            },
            'is_healthy': self.status in [ConnectionStatus.HEALTHY, ConnectionStatus.DEGRADED],
            'needs_attention': self.status in [ConnectionStatus.UNHEALTHY, ConnectionStatus.OFFLINE]
        }
    
    def get_fallback_response(self, language: str = 'nl', response_type: str = 'error') -> str:
        """Get fallback response when chatbot is unavailable"""
        return self.fallback_responses.get(language, self.fallback_responses['nl']).get(
            response_type, 
            self.fallback_responses['nl']['error']
        )
    
    def attempt_reconnection(self) -> bool:
        """Attempt to reconnect to chatbot"""
        self.logger.info("Attempting reconnection...")
        self.status = ConnectionStatus.RECONNECTING
        self.metrics.status = ConnectionStatus.RECONNECTING
        
        for attempt in range(self.max_retries):
            try:
                self._perform_health_check()
                if self.status in [ConnectionStatus.HEALTHY, ConnectionStatus.DEGRADED]:
                    self.logger.info(f"Reconnection successful after {attempt + 1} attempts")
                    return True
            except Exception as e:
                self.logger.warning(f"Reconnection attempt {attempt + 1} failed: {e}")
            
            if attempt < self.max_retries - 1:
                time.sleep(2 ** attempt)  # Exponential backoff
        
        self.logger.error("Reconnection failed after all attempts")
        self.status = ConnectionStatus.OFFLINE
        self.metrics.status = ConnectionStatus.OFFLINE
        return False
    
    def is_connection_healthy(self) -> bool:
        """Check if connection is healthy"""
        return self.status in [ConnectionStatus.HEALTHY, ConnectionStatus.DEGRADED]
    
    def get_health_summary(self) -> str:
        """Get human-readable health summary"""
        if self.status == ConnectionStatus.HEALTHY:
            return f"✅ Healthy (Response time: {self.metrics.response_time_ms:.0f}ms)"
        elif self.status == ConnectionStatus.DEGRADED:
            return f"⚠️ Degraded (Response time: {self.metrics.response_time_ms:.0f}ms)"
        elif self.status == ConnectionStatus.UNHEALTHY:
            return f"🔴 Unhealthy ({self.metrics.consecutive_failures} consecutive failures)"
        elif self.status == ConnectionStatus.OFFLINE:
            return f"❌ Offline ({self.metrics.consecutive_failures} consecutive failures)"
        else:
            return f"🔄 {self.status.value.title()}"

# Global connection monitor instance
connection_monitor = ConnectionMonitor()

def get_connection_monitor() -> ConnectionMonitor:
    """Get the global connection monitor instance"""
    return connection_monitor

def start_connection_monitoring():
    """Start global connection monitoring"""
    connection_monitor.start_monitoring()

def stop_connection_monitoring():
    """Stop global connection monitoring"""
    connection_monitor.stop_monitoring()
