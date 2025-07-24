# Security Deployment Guide

## 🔒 Pre-Deployment Security Checklist

### 1. **Environment Configuration**

#### Required Environment Variables
Create a `.env` file based on `.env.example`:

```bash
# Generate a secure secret key
python3 -c "import uuid; print(uuid.uuid4().hex)"

# Set environment variables
FLASK_ENV=production
DEBUG=False
SECRET_KEY=generated-secret-key-from-above
PERPLEXITY_API_KEY=your-perplexity-api-key
ALLOWED_ORIGINS=https://yourdomain.com,https://www.yourdomain.com
```

#### Validate Configuration
```bash
# Check that no default/example values remain
grep -r "your-" .env
grep -r "example" .env
```

### 2. **Dependency Security**

#### Update Dependencies
```bash
# Install pinned versions
pip install -r requirements.txt

# Check for security vulnerabilities
pip install safety
safety check

# Update package.json dependencies if needed
npm audit
npm audit fix
```

#### Verify Versions
Ensure all dependencies in `requirements.txt` are pinned to specific versions.

### 3. **Code Security Verification**

#### Remove Development Code
- ✅ All `console.log` statements removed
- ✅ Debug mode disabled (`DEBUG=False`)
- ✅ No hardcoded API keys or secrets
- ✅ No test endpoints in production

#### Validate Fixes Applied
```bash
# Check for remaining console.log statements
grep -r "console.log" js/
grep -r "console.log" sw.js

# Check for hardcoded secrets
grep -r "api_key.*=" . --exclude-dir=node_modules
grep -r "secret.*=" . --exclude-dir=node_modules
```

## 🚀 Deployment Steps

### 1. **Server Configuration**

#### HTTPS Setup (Required)
```bash
# Install SSL certificate
certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Or configure manual SSL
# Place certificate files in secure location
# Update environment variables
SSL_CERT_PATH=/path/to/ssl/cert.pem
SSL_KEY_PATH=/path/to/ssl/private.key
```

#### Nginx Configuration Example
```nginx
server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/private.key;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' fonts.googleapis.com; font-src 'self' fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';" always;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    location / {
        root /var/www/nijenhuis;
        index index.html;
        try_files $uri $uri/ =404;
    }
    
    location /api/ {
        proxy_pass http://127.0.0.1:5000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # Rate limiting
        limit_req zone=api burst=20 nodelay;
    }
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

#### Rate Limiting Setup
```nginx
# Add to nginx.conf http block
http {
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/m;
    limit_req_zone $binary_remote_addr zone=general:10m rate=1r/s;
}
```

### 2. **Backend Deployment**

#### Using Gunicorn (Recommended)
```bash
# Install gunicorn
pip install gunicorn

# Create systemd service
sudo nano /etc/systemd/system/nijenhuis-api.service
```

```ini
[Unit]
Description=Nijenhuis API Service
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/nijenhuis
Environment=PATH=/var/www/nijenhuis/venv/bin
EnvironmentFile=/var/www/nijenhuis/.env
ExecStart=/var/www/nijenhuis/venv/bin/gunicorn --workers 3 --bind 127.0.0.1:5000 perplexity_backend:app
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
# Enable and start service
sudo systemctl enable nijenhuis-api
sudo systemctl start nijenhuis-api
sudo systemctl status nijenhuis-api
```

#### Using Docker (Alternative)
```dockerfile
FROM python:3.11-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 5000

CMD ["gunicorn", "--workers", "3", "--bind", "0.0.0.0:5000", "perplexity_backend:app"]
```

### 3. **Frontend Deployment**

#### Static Files
```bash
# Copy files to web root
sudo cp -r * /var/www/nijenhuis/
sudo chown -R www-data:www-data /var/www/nijenhuis/
sudo chmod -R 644 /var/www/nijenhuis/
sudo chmod -R 755 /var/www/nijenhuis/*/
```

#### Service Worker Registration
Ensure service worker is properly registered and cached files are updated.

## 🔍 Post-Deployment Security Checks

### 1. **SSL/TLS Verification**
```bash
# Test SSL configuration
curl -I https://yourdomain.com
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com
```

Use online tools:
- [SSL Labs Test](https://www.ssllabs.com/ssltest/)
- [Security Headers](https://securityheaders.com/)

### 2. **API Security Testing**
```bash
# Test rate limiting
for i in {1..15}; do curl -X POST https://yourdomain.com/api/perplexity; done

# Test CORS
curl -H "Origin: https://malicious-site.com" https://yourdomain.com/api/perplexity

# Test input validation
curl -X POST https://yourdomain.com/api/perplexity \
  -H "Content-Type: application/json" \
  -d '{"invalid": "data"}'
```

### 3. **Security Headers Verification**
```bash
curl -I https://yourdomain.com
```

Expected headers:
- `Content-Security-Policy`
- `X-Frame-Options: SAMEORIGIN`
- `X-Content-Type-Options: nosniff`
- `X-XSS-Protection: 1; mode=block`
- `Strict-Transport-Security`

### 4. **Vulnerability Scanning**

#### OWASP ZAP Scan
```bash
# Install OWASP ZAP
# Run automated scan
zap-cli start
zap-cli spider https://yourdomain.com
zap-cli active-scan https://yourdomain.com
zap-cli report -o security-report.html -f html
```

#### Nmap Security Scan
```bash
nmap -sV --script vuln yourdomain.com
```

## 📊 Monitoring and Alerting

### 1. **Log Monitoring**
```bash
# Monitor application logs
tail -f /var/log/nijenhuis/app.log

# Monitor nginx access logs
tail -f /var/log/nginx/access.log

# Monitor for security events
grep "401\|403\|429" /var/log/nginx/access.log
```

### 2. **Security Monitoring**

#### Failed Authentication Attempts
```bash
# Monitor rate limit violations
grep "limiting requests" /var/log/nginx/error.log

# Monitor suspicious API calls
grep "Invalid input" /var/log/nijenhuis/app.log
```

#### Set up Alerts
```bash
# Example logwatch configuration
# Add to /etc/logwatch/conf/services/nijenhuis.conf
LogFile = /var/log/nijenhuis/app.log
*RemoveHeaders
*OnlyService = nijenhuis
*Print = 1
```

### 3. **Automated Security Checks**

#### Daily Security Scan Script
```bash
#!/bin/bash
# security-check.sh

# Check for failed logins
FAILED_LOGINS=$(grep "Invalid API key" /var/log/nijenhuis/app.log | wc -l)

if [ $FAILED_LOGINS -gt 100 ]; then
    echo "High number of failed API attempts: $FAILED_LOGINS" | mail -s "Security Alert" admin@yourdomain.com
fi

# Check SSL certificate expiry
DAYS_UNTIL_EXPIRY=$(openssl x509 -in /path/to/ssl/cert.pem -noout -dates | grep "notAfter" | cut -d= -f2 | xargs -I {} date -d {} +%s | xargs -I {} echo $(( ({} - $(date +%s)) / 86400 )))

if [ $DAYS_UNTIL_EXPIRY -lt 30 ]; then
    echo "SSL certificate expires in $DAYS_UNTIL_EXPIRY days" | mail -s "SSL Expiry Warning" admin@yourdomain.com
fi
```

## 🔄 Maintenance Tasks

### Weekly Security Tasks
- [ ] Review access logs for suspicious activity
- [ ] Check for dependency updates
- [ ] Verify backup integrity
- [ ] Review rate limit logs

### Monthly Security Tasks
- [ ] Update dependencies (after testing)
- [ ] Review and rotate API keys
- [ ] Security scan with OWASP ZAP
- [ ] Review CSP violations (if logging enabled)

### Quarterly Security Tasks
- [ ] Full security audit
- [ ] Penetration testing
- [ ] Review and update security policies
- [ ] SSL certificate renewal (if needed)

## 🚨 Incident Response

### Security Incident Checklist
1. **Immediate Response**
   - [ ] Isolate affected systems
   - [ ] Change all API keys and secrets
   - [ ] Review access logs
   - [ ] Document the incident

2. **Investigation**
   - [ ] Determine attack vector
   - [ ] Assess data impact
   - [ ] Check for data exfiltration
   - [ ] Identify vulnerabilities exploited

3. **Recovery**
   - [ ] Apply security patches
   - [ ] Restore from clean backups if needed
   - [ ] Update security measures
   - [ ] Monitor for continued threats

4. **Post-Incident**
   - [ ] Conduct lessons learned session
   - [ ] Update security procedures
   - [ ] Improve monitoring and detection
   - [ ] User communication if required

## 📞 Emergency Contacts

- **System Administrator**: [contact info]
- **Security Team**: [contact info]
- **DNS Provider**: [contact info]
- **Hosting Provider**: [contact info]
- **SSL Certificate Provider**: [contact info]

---

## 🔗 Additional Resources

- [OWASP Security Guidelines](https://owasp.org/www-project-web-security-testing-guide/)
- [Mozilla Security Guidelines](https://infosec.mozilla.org/guidelines/web_security)
- [Flask Security Documentation](https://flask.palletsprojects.com/en/2.3.x/security/)
- [Nginx Security Guide](https://nginx.org/en/docs/http/ngx_http_secure_link_module.html)