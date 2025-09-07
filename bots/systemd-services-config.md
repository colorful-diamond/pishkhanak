# Systemd Service Configuration for Bots

## NICS24 Inquiry Provider Service

Create `/etc/systemd/system/nics24-inquiry.service`:

```ini
[Unit]
Description=NICS24 Inquiry Provider Service
After=network.target redis.service

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/path/to/pishkhanak.com/bots/inquiry-provider

# Environment variables
Environment="NODE_ENV=production"
Environment="REDIS_HOST=127.0.0.1"
Environment="REDIS_PORT=6379"

# File descriptor limits to prevent exhaustion
LimitNOFILE=65535
LimitNPROC=4096

# Memory limits
MemoryLimit=2G
MemoryMax=2G

# Restart policy
Restart=always
RestartSec=10
StartLimitBurst=5
StartLimitInterval=120

# Command
ExecStart=/usr/bin/node /path/to/pishkhanak.com/bots/inquiry-provider/providers/nics24/index.js

# Logging
StandardOutput=journal
StandardError=journal
SyslogIdentifier=nics24-inquiry

[Install]
WantedBy=multi-user.target
```

## Persian Captcha Solver Service

Create `/etc/systemd/system/persian-captcha.service`:

```ini
[Unit]
Description=Persian Digit Captcha Solver API
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/path/to/pishkhanak.com/bots/persian-digits-captcha-solver

# Environment variables
Environment="PYTHONUNBUFFERED=1"
Environment="PORT=9090"
Environment="LOG_LEVEL=INFO"

# File descriptor limits to prevent exhaustion
LimitNOFILE=65535
LimitNPROC=4096

# Memory limits
MemoryLimit=1G
MemoryMax=1G

# Restart policy
Restart=always
RestartSec=10
StartLimitBurst=5
StartLimitInterval=120

# Command using gunicorn for production
ExecStart=/path/to/pishkhanak.com/bots/persian-digits-captcha-solver/cpu/bin/gunicorn \
    -c /path/to/pishkhanak.com/bots/persian-digits-captcha-solver/gunicorn.conf.py \
    captcha_api_production:app

# Logging
StandardOutput=journal
StandardError=journal
SyslogIdentifier=persian-captcha

[Install]
WantedBy=multi-user.target
```

## Installation Instructions

1. Copy service files to systemd directory:
```bash
sudo cp nics24-inquiry.service /etc/systemd/system/
sudo cp persian-captcha.service /etc/systemd/system/
```

2. Reload systemd daemon:
```bash
sudo systemctl daemon-reload
```

3. Enable services to start on boot:
```bash
sudo systemctl enable nics24-inquiry.service
sudo systemctl enable persian-captcha.service
```

4. Start services:
```bash
sudo systemctl start nics24-inquiry.service
sudo systemctl start persian-captcha.service
```

5. Check service status:
```bash
sudo systemctl status nics24-inquiry.service
sudo systemctl status persian-captcha.service
```

## Monitoring Commands

```bash
# View logs
sudo journalctl -u nics24-inquiry.service -f
sudo journalctl -u persian-captcha.service -f

# Check resource usage
sudo systemctl status nics24-inquiry.service
sudo systemctl status persian-captcha.service

# Restart services
sudo systemctl restart nics24-inquiry.service
sudo systemctl restart persian-captcha.service
```

## System-wide File Descriptor Limits

Add to `/etc/security/limits.conf`:
```
* soft nofile 65535
* hard nofile 65535
* soft nproc 4096
* hard nproc 4096
```

Add to `/etc/sysctl.conf`:
```
fs.file-max = 2097152
```

Apply changes:
```bash
sudo sysctl -p
```
