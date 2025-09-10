# Nijenhuis Chatbot Security Guide

## Overview

This document provides comprehensive information about the security measures implemented in the Nijenhuis Chatbot system. The system has been enhanced with enterprise-grade security features to ensure the connection between the MLA (Machine Learning Assistant) and chatbot is active, strong, and secure.

## Security Architecture

### 1. Authentication & Authorization

#### API Key Management
- **Secure API Key Generation**: Uses cryptographically secure random token generation
- **Key Storage**: API keys are stored securely with proper permissions
- **Key Rotation**: Support for automatic key rotation and expiration
- **Permission-Based Access**: Granular permissions (chat, config, admin)

#### JWT Token Support
- **Token-Based Authentication**: JWT tokens for stateless authentication
- **Configurable Expiry**: Tokens expire after 24 hours by default
- **Secure Signing**: Uses HMAC-SHA256 for token signing
- **Token Validation**: Comprehensive token validation and error handling

### 2. Rate Limiting & Throttling

#### Request Rate Limiting
- **Per-Client Limits**: 60 requests per minute, 1000 per hour by default
- **Burst Protection**: Prevents sudden spikes in requests
- **Custom Limits**: API keys can have custom rate limits
- **Graceful Degradation**: Returns appropriate error messages when limits exceeded

#### IP-Based Protection
- **Failed Attempt Tracking**: Monitors failed authentication attempts
- **Automatic IP Blocking**: Blocks IPs after 5 failed attempts
- **Temporary Blocks**: 15-minute block duration with auto-unblock
- **Whitelist Support**: Trusted IPs can be whitelisted

### 3. Connection Monitoring & Health Checks

#### Real-Time Monitoring
- **Health Check Endpoints**: Regular health checks every 30 seconds
- **Connection Status Tracking**: Monitors connection quality and response times
- **Automatic Reconnection**: Attempts reconnection when connections fail
- **Fallback Mechanisms**: Provides offline responses when chatbot is unavailable

#### Performance Metrics
- **Response Time Monitoring**: Tracks API response times
- **Success Rate Tracking**: Monitors request success rates
- **Uptime Monitoring**: Tracks system uptime and availability
- **Error Rate Monitoring**: Monitors and alerts on error rates

### 4. Data Security

#### Input Validation & Sanitization
- **Message Length Limits**: Maximum 1000 characters per message
- **XSS Protection**: HTML escaping and input sanitization
- **SQL Injection Prevention**: Parameterized queries and input validation
- **Content Filtering**: Filters potentially malicious content

#### Secure Data Transmission
- **HTTPS Support**: Encrypted data transmission (when configured)
- **Secure Headers**: Security headers for additional protection
- **Request ID Tracking**: Unique request IDs for audit trails
- **Client Version Tracking**: Tracks client versions for compatibility

### 5. Logging & Auditing

#### Security Event Logging
- **Authentication Events**: Logs all authentication attempts
- **Rate Limiting Events**: Logs rate limit violations
- **IP Blocking Events**: Logs IP blocking and unblocking
- **Error Tracking**: Comprehensive error logging and tracking

#### Audit Trail
- **Request Logging**: Logs all API requests with timestamps
- **Response Logging**: Logs response times and status codes
- **Security Events**: Logs all security-related events
- **Performance Metrics**: Logs performance and health metrics

## Configuration

### Security Configuration File

The security configuration is managed through `backend/chatbot/config/security_config.json`:

```json
{
  "security": {
    "authentication": {
      "enabled": true,
      "api_key_required": true,
      "jwt_enabled": true
    },
    "rate_limiting": {
      "enabled": true,
      "default_requests_per_minute": 60
    },
    "ip_blocking": {
      "enabled": true,
      "max_failed_attempts": 5
    }
  }
}
```

### Environment Variables

Set the following environment variables for production:

```bash
FLASK_SECRET_KEY=your-secure-secret-key
CHATBOT_API_KEY=your-production-api-key
SECURITY_LEVEL=production
```

## API Usage

### Authentication

All API requests require authentication via API key:

```bash
curl -X POST http://localhost:5001/api/chat \
  -H "Content-Type: application/json" \
  -H "X-API-Key: your-api-key" \
  -d '{"message": "Hello"}'
```

### Rate Limiting

When rate limits are exceeded, the API returns:

```json
{
  "error": "Rate limit exceeded. Max 60 requests per minute.",
  "success": false
}
```

### Connection Status

Check connection status:

```bash
curl -H "X-API-Key: your-api-key" \
  http://localhost:5001/api/connection/status
```

## Frontend Integration

### Secure Client Usage

```javascript
// Initialize secure client
const client = new SecureChatbotClient({
    apiEndpoint: 'http://localhost:5001/api/chat',
    apiKey: 'your-api-key'
});

// Send message with automatic retry and fallback
client.sendMessage('Hello').then(response => {
    console.log('Response:', response);
}).catch(error => {
    console.error('Error:', error);
});
```

### Connection Monitoring

```javascript
// Listen for connection changes
client.on('connectionChange', (data) => {
    console.log('Connection status:', data.status);
    if (!data.isConnected) {
        // Handle offline state
    }
});
```

## Security Best Practices

### 1. API Key Management
- **Rotate Keys Regularly**: Change API keys every 30-90 days
- **Use Environment Variables**: Store keys in environment variables, not code
- **Limit Permissions**: Use minimal required permissions for each key
- **Monitor Usage**: Regularly review API key usage and access patterns

### 2. Network Security
- **Use HTTPS**: Always use HTTPS in production environments
- **Firewall Configuration**: Configure firewalls to restrict access
- **VPN Access**: Use VPN for administrative access
- **Regular Updates**: Keep all dependencies and systems updated

### 3. Monitoring & Alerting
- **Set Up Alerts**: Configure alerts for security events
- **Monitor Logs**: Regularly review security logs
- **Performance Monitoring**: Monitor system performance and response times
- **Backup Verification**: Regularly test backup and recovery procedures

### 4. Development Security
- **Code Reviews**: Conduct security-focused code reviews
- **Dependency Scanning**: Regularly scan for vulnerable dependencies
- **Testing**: Include security testing in your test suite
- **Documentation**: Keep security documentation up to date

## Troubleshooting

### Common Issues

#### Authentication Failures
```bash
# Check API key validity
curl -H "X-API-Key: your-key" http://localhost:5001/api/health

# Verify key permissions
curl -H "X-API-Key: your-key" http://localhost:5001/api/security/status
```

#### Rate Limiting Issues
```bash
# Check current rate limit status
curl -H "X-API-Key: your-key" http://localhost:5001/api/security/status

# Wait for rate limit reset or use different API key
```

#### Connection Issues
```bash
# Check connection status
curl -H "X-API-Key: your-key" http://localhost:5001/api/connection/status

# Force reconnection
curl -X POST -H "X-API-Key: your-key" \
  http://localhost:5001/api/connection/reconnect
```

### Log Analysis

#### Security Events
```bash
# View security logs
tail -f logs/security.log | grep "Security Event"

# Check authentication failures
grep "invalid_api_key" logs/security.log

# Monitor rate limiting
grep "rate_limit_exceeded" logs/security.log
```

#### Performance Issues
```bash
# Check response times
grep "response_time" logs/connection_monitor.log

# Monitor error rates
grep "Error" logs/security.log | wc -l
```

## Security Testing

### Running Security Tests

```bash
# Run comprehensive security tests
cd backend/chatbot
python -m pytest tests/test_security_integration.py -v

# Run specific test categories
python -m pytest tests/test_security_integration.py::TestSecurityManager -v
```

### Manual Security Testing

#### Authentication Testing
```bash
# Test without API key
curl -X POST http://localhost:5001/api/chat \
  -d '{"message": "test"}'

# Test with invalid API key
curl -X POST http://localhost:5001/api/chat \
  -H "X-API-Key: invalid-key" \
  -d '{"message": "test"}'
```

#### Rate Limiting Testing
```bash
# Test rate limiting
for i in {1..70}; do
  curl -H "X-API-Key: your-key" \
    http://localhost:5001/api/health
done
```

## Compliance & Standards

### GDPR Compliance
- **Data Minimization**: Only collect necessary data
- **Data Retention**: Automatic data cleanup after 90 days
- **User Rights**: Support for data access and deletion requests
- **Privacy by Design**: Security measures built into the system

### Security Standards
- **OWASP Guidelines**: Follows OWASP security best practices
- **Industry Standards**: Implements industry-standard security measures
- **Regular Audits**: Supports regular security audits and assessments

## Support & Maintenance

### Regular Maintenance Tasks
1. **Update Dependencies**: Monthly dependency updates
2. **Review Logs**: Weekly security log review
3. **Key Rotation**: Quarterly API key rotation
4. **Security Testing**: Monthly security testing
5. **Backup Verification**: Weekly backup testing

### Emergency Procedures
1. **Security Incident Response**: Immediate isolation and investigation
2. **Key Revocation**: Emergency API key revocation procedures
3. **System Shutdown**: Emergency system shutdown procedures
4. **Communication Plan**: Incident communication procedures

## Conclusion

The Nijenhuis Chatbot system now implements comprehensive security measures to ensure the connection between the MLA and chatbot is:

- **Active**: Real-time monitoring and health checks ensure continuous availability
- **Strong**: Robust authentication, rate limiting, and input validation provide strong protection
- **Secure**: End-to-end security measures protect data and prevent unauthorized access

For additional support or security concerns, please contact the development team or refer to the system logs for detailed information.
