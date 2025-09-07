#!/usr/bin/env python3
"""
Gunicorn Configuration for Persian Digit Captcha API
===================================================

Production-ready gunicorn configuration for the captcha solver API.
Optimized for AI/ML workloads with appropriate memory and timeout settings.
"""

import multiprocessing
import os

# Server Socket
bind = "127.0.0.1:9090"  # Localhost only for security
backlog = 2048

# Worker Processes
workers = min(4, (multiprocessing.cpu_count() * 2) + 1)  # Conservative for ML workload
worker_class = "sync"  # Synchronous workers for ML processing
worker_connections = 1000
max_requests = 1000  # Restart workers after 1000 requests (prevents memory leaks)
max_requests_jitter = 50  # Add randomness to prevent thundering herd
timeout = 120  # 2 minutes timeout for ML processing
keepalive = 2

# Memory Management
preload_app = True  # Preload app to share model memory across workers
worker_tmp_dir = "/dev/shm"  # Use shared memory for temporary files (if available)

# Security
limit_request_line = 4094
limit_request_fields = 100
limit_request_field_size = 8190

# Application
wsgi_app = "captcha_api_production:app"
pythonpath = "."

# Logging
accesslog = "logs/gunicorn_access.log"
errorlog = "logs/gunicorn_error.log"
loglevel = "info"
access_log_format = '%(h)s %(l)s %(u)s %(t)s "%(r)s" %(s)s %(b)s "%(f)s" "%(a)s" %(D)s'

# Process Naming
proc_name = "captcha_api"

# Daemon Mode
daemon = False
pidfile = "logs/gunicorn.pid"
user = None  # Run as current user
group = None
umask = 0

# SSL (if needed in future)
# keyfile = None
# certfile = None

# Development vs Production
raw_env = [
    'FLASK_ENV=production',
    'LOG_LEVEL=INFO',
    'COLOR_TOLERANCE=30',
    'USE_PATTERN_SHADOW_DETECTION=true',
    'SIMPLE_MODE=false'
]

# Hooks
def on_starting(server):
    """Called just before the master process is initialized."""
    server.log.info("🚀 Starting Persian Digit Captcha API with Gunicorn")
    
    # Create logs directory if it doesn't exist
    os.makedirs("logs", exist_ok=True)

def on_reload(server):
    """Called to recycle workers during a reload via SIGHUP."""
    server.log.info("🔄 Reloading workers...")

def when_ready(server):
    """Called just after the server is started."""
    server.log.info("✅ Gunicorn server ready!")
    server.log.info(f"🌐 Listening on: http://{bind}")
    server.log.info(f"👥 Workers: {workers}")
    server.log.info(f"🔒 Security: Localhost only")
    server.log.info(f"⏱️  Timeout: {timeout}s")
    server.log.info(f"💾 Max requests per worker: {max_requests}")

def worker_int(worker):
    """Called just after a worker exited on SIGINT or SIGQUIT."""
    worker.log.info("🔄 Worker received INT or QUIT signal")

def pre_fork(server, worker):
    """Called just before a worker is forked."""
    server.log.info(f"👷 Forking worker {worker.pid}")

def post_fork(server, worker):
    """Called just after a worker has been forked."""
    server.log.info(f"✅ Worker {worker.pid} spawned")

def post_worker_init(worker):
    """Called just after a worker has initialized the application."""
    worker.log.info(f"🤖 Worker {worker.pid} initialized - loading ML model...")

def worker_abort(worker):
    """Called when a worker received the SIGABRT signal."""
    worker.log.info(f"⚠️  Worker {worker.pid} aborted")

def pre_exec(server):
    """Called just before a new master process is forked."""
    server.log.info("🔄 Pre-exec hook")

def pre_request(worker, req):
    """Called just before a worker processes the request."""
    worker.log.debug(f"📨 Processing request: {req.method} {req.path}")

def post_request(worker, req, environ, resp):
    """Called after a worker processes the request."""
    worker.log.debug(f"✅ Request completed: {resp.status}")

def child_exit(server, worker):
    """Called just after a worker has been exited, in the master process."""
    server.log.info(f"👋 Worker {worker.pid} exited")

def worker_exit(server, worker):
    """Called just after a worker has been exited, in the worker process."""
    server.log.info(f"🚪 Worker {worker.pid} exit")

def nworkers_changed(server, new_value, old_value):
    """Called just after num_workers has been changed."""
    server.log.info(f"👥 Workers changed from {old_value} to {new_value}")

def on_exit(server):
    """Called just before exiting."""
    server.log.info("👋 Shutting down Persian Digit Captcha API")