# Nijenhuis Deployment Configuration

This directory contains all deployment configurations for the Nijenhuis Chatbot service.

## Directory Structure

```
deploy/
├── systemd/               # systemd service files
│   ├── nijenhuis-chatbot.service    # Chatbot API (port 5001)
│   ├── nijenhuis-backend.service    # Backend API (port 8080)
│   ├── nijenhuis-admin.service      # Admin interface
│   └── nijenhuis-frontend.service   # Frontend server
├── networkmanager/        # NetworkManager dispatcher scripts
│   └── 99-nijenhuis-chatbot         # Auto-restart on network changes
├── logrotate/             # Log rotation configuration
│   └── nijenhuis                    # Rotate logs daily, keep 14 days
└── nginx/                 # Nginx configuration
    └── site.conf                    # Reverse proxy config
```

## Installation

Run the installation script with sudo:

```bash
sudo bash scripts/install-systemd-services.sh
```

This will:
1. Install all systemd service files to `/etc/systemd/system/`
2. Install NetworkManager dispatcher to `/etc/NetworkManager/dispatcher.d/`
3. Install logrotate configuration to `/etc/logrotate.d/`
4. Enable and start all services
5. Install Gunicorn if not already installed

## Service Management

### Check Status
```bash
# All services
sudo systemctl status nijenhuis-*.service

# Specific service
sudo systemctl status nijenhuis-chatbot.service
```

### View Logs
```bash
# Follow logs in real-time
sudo journalctl -u nijenhuis-chatbot.service -f

# Last 100 lines
sudo journalctl -u nijenhuis-chatbot.service -n 100

# Application logs
tail -f /home/andre/Desktop/Projects/Nijenhuis/logs/chatbot_server.log
```

### Restart Services
```bash
# Restart chatbot
sudo systemctl restart nijenhuis-chatbot.service

# Reload (graceful restart)
sudo systemctl reload nijenhuis-chatbot.service
```

### Stop Services
```bash
sudo systemctl stop nijenhuis-chatbot.service
```

## Health Check

Quick health check script:
```bash
bash scripts/check_chatbot_health.sh
```

## Auto-Restart Features

### On System Boot
- Services start automatically via systemd `WantedBy=multi-user.target`

### On Service Crash
- systemd `Restart=always` with 5-second delay
- Up to 10 restarts within 10 minutes before giving up

### On Network Changes
- NetworkManager dispatcher script detects network up/down events
- Verifies actual internet connectivity (pings 8.8.8.8)
- Restarts chatbot service when internet is restored

### Watchdog
- systemd WatchdogSec=60s monitors for hung processes
- Gunicorn sends WATCHDOG=1 on each request
- Process is killed and restarted if watchdog times out

## Configuration Files

### Environment Variables (.env)
Required environment variables:
- `FLASK_SECRET_KEY` - Flask session encryption key (min 32 chars)
- `JWT_SECRET` - JWT token signing key (min 64 chars)

Optional:
- `APP_ENV` - Set to `production` for production mode
- `APP_DEBUG` - Set to `false` in production
- `LOG_LEVEL` - Logging level (INFO, WARNING, ERROR)

### Gunicorn Configuration
Edit `gunicorn.conf.py` in project root to adjust:
- Number of workers
- Timeouts
- Logging paths
- Worker settings

## Troubleshooting

### Service won't start
1. Check logs: `sudo journalctl -u nijenhuis-chatbot.service -n 50`
2. Verify .env file exists and has required variables
3. Run diagnostics: `bash scripts/troubleshoot_chatbot.sh diagnose`

### High memory usage
1. Check with: `bash scripts/check_chatbot_health.sh`
2. Restart service: `sudo systemctl restart nijenhuis-chatbot.service`
3. Adjust MemoryMax in service file if needed

### Network issues
1. Check internet: `ping 8.8.8.8`
2. Verify NetworkManager: `systemctl status NetworkManager`
3. Check dispatcher logs: `sudo journalctl -t nijenhuis-chatbot-network`

## AWS EC2: PHP image upload limits

Admin “te koop” image uploads use `multipart/form-data`. PHP-FPM’s default `upload_max_filesize` is often 2M, which triggers `UPLOAD_ERR_INI_SIZE` before application checks run.

- **Drop-in:** [`deploy/aws/99-nijenhuis-uploads.ini`](aws/99-nijenhuis-uploads.ini) sets `upload_max_filesize=12M` and `post_max_size=16M`.
- **Install:** `scripts/deploy_aws.sh` copies it to `/etc/php.d/` and restarts `php-fpm`. Verify on the server: `php -i | grep -E 'upload_max_filesize|post_max_size'`.

## Security Notes

- Services run as user `andre` (not root)
- `NoNewPrivileges=true` prevents privilege escalation
- `ProtectSystem=strict` makes system read-only
- Only specific paths are writable (logs, config)
- Rate limiting and IP blocking enabled in application

