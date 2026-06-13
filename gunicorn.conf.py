# gunicorn.conf.py - Gunicorn Configuration for Nijenhuis Chatbot
# Production-ready WSGI server configuration with systemd integration

import os
import multiprocessing

# Server socket
bind = "0.0.0.0:5001"
backlog = 2048

# Worker processes
# Use 2 workers minimum, scale with CPU cores (max 4 for chatbot)
workers = min(max(multiprocessing.cpu_count(), 2), 4)
worker_class = "gthread"  # Thread-based workers for I/O bound operations
threads = 4
worker_connections = 1000
timeout = 120
keepalive = 5
graceful_timeout = 30

# Process naming
proc_name = "nijenhuis-chatbot"

# Logging
accesslog = "/home/andre/Desktop/Projects/Nijenhuis/logs/chatbot_access.log"
errorlog = "/home/andre/Desktop/Projects/Nijenhuis/logs/chatbot_error.log"
loglevel = os.environ.get("LOG_LEVEL", "info").lower()
access_log_format = '%(h)s %(l)s %(u)s %(t)s "%(r)s" %(s)s %(b)s "%(f)s" "%(a)s" %(L)s'
capture_output = True

# Security
limit_request_line = 4094
limit_request_fields = 100
limit_request_field_size = 8190

# Server mechanics
daemon = False  # systemd manages the process
pidfile = "/tmp/nijenhuis-chatbot.pid"
umask = 0o022
user = None  # Set via systemd
group = None  # Set via systemd
tmp_upload_dir = None

# Preloading
preload_app = True  # Load app before forking workers

# Hooks for systemd watchdog integration
def on_starting(server):
    """Called just before the master process is initialized."""
    server.log.info("Nijenhuis Chatbot starting...")

def when_ready(server):
    """Called just after the server is started."""
    server.log.info("Nijenhuis Chatbot is ready to serve requests")
    # Notify systemd that we're ready
    try:
        import sdnotify
        n = sdnotify.SystemdNotifier()
        n.notify("READY=1")
        n.notify("STATUS=Chatbot API ready")
    except ImportError:
        server.log.warning("sdnotify not available - systemd notification skipped")
    except Exception as e:
        server.log.warning(f"Failed to notify systemd: {e}")

def pre_fork(server, worker):
    """Called just before a worker is forked."""
    pass

def post_fork(server, worker):
    """Called just after a worker has been forked."""
    server.log.info(f"Worker {worker.pid} spawned")

def worker_int(worker):
    """Called when a worker receives SIGINT or SIGQUIT."""
    worker.log.info(f"Worker {worker.pid} interrupted")

def worker_abort(worker):
    """Called when a worker receives SIGABRT."""
    worker.log.error(f"Worker {worker.pid} aborted")

def pre_exec(server):
    """Called just before a new master process is forked."""
    server.log.info("Master process forking...")

def pre_request(worker, req):
    """Called just before a worker processes a request."""
    # Send watchdog ping for each request
    try:
        import sdnotify
        n = sdnotify.SystemdNotifier()
        n.notify("WATCHDOG=1")
    except (ImportError, Exception):
        pass

def post_request(worker, req, environ, resp):
    """Called after a worker processes a request."""
    pass

def child_exit(server, worker):
    """Called when a worker is terminated."""
    server.log.info(f"Worker {worker.pid} exited")

def worker_exit(server, worker):
    """Called just after a worker exits."""
    server.log.info(f"Worker {worker.pid} shutdown complete")

def nworkers_changed(server, new_value, old_value):
    """Called when the number of workers changes."""
    server.log.info(f"Workers changed: {old_value} -> {new_value}")

def on_exit(server):
    """Called just before exiting gunicorn."""
    server.log.info("Nijenhuis Chatbot shutting down...")
    try:
        import sdnotify
        n = sdnotify.SystemdNotifier()
        n.notify("STOPPING=1")
    except (ImportError, Exception):
        pass

