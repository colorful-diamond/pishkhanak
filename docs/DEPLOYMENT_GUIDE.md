# Deployment Guide

## Overview

This guide covers the complete deployment process for the Pishkhanak platform, including server setup, environment configuration, database migration, and production optimization.

## Prerequisites

### Server Requirements

#### Minimum Specifications
- **CPU**: 2 cores
- **RAM**: 4GB
- **Storage**: 50GB SSD
- **Bandwidth**: 1TB/month

#### Recommended Specifications
- **CPU**: 4+ cores
- **RAM**: 8GB+
- **Storage**: 100GB+ SSD
- **Bandwidth**: 5TB/month

#### Software Requirements
- **OS**: Ubuntu 20.04+ or CentOS 8+
- **PHP**: 8.1, 8.2, 8.3, or 8.4
- **Database**: PostgreSQL 13+ (recommended) or MySQL 8.0+
- **Web Server**: Nginx (recommended) or Apache
- **Process Manager**: Supervisor
- **Cache/Queue**: Redis 6.0+
- **Node.js**: 18+ (for asset compilation)

## Server Setup

### 1. Initial Server Configuration

#### Update System
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget git unzip supervisor
```

#### Install PHP 8.2
```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-pgsql php8.2-zip php8.2-gd php8.2-mbstring \
    php8.2-curl php8.2-xml php8.2-bcmath php8.2-json php8.2-redis \
    php8.2-intl php8.2-soap php8.2-imagick
```

#### Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

#### Install Node.js
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Database Setup

#### PostgreSQL (Recommended)
```bash
sudo apt install -y postgresql postgresql-contrib
sudo -u postgres createuser --interactive pishkhanak
sudo -u postgres createdb pishkhanak_production
sudo -u postgres psql -c "ALTER USER pishkhanak PASSWORD 'secure_password_here';"
```

#### MySQL Alternative
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
sudo mysql -e "CREATE DATABASE pishkhanak_production;"
sudo mysql -e "CREATE USER 'pishkhanak'@'localhost' IDENTIFIED BY 'secure_password_here';"
sudo mysql -e "GRANT ALL PRIVILEGES ON pishkhanak_production.* TO 'pishkhanak'@'localhost';"
```

### 3. Redis Setup
```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Configure Redis for production
sudo sed -i 's/# maxmemory <bytes>/maxmemory 1gb/' /etc/redis/redis.conf
sudo sed -i 's/# maxmemory-policy noeviction/maxmemory-policy allkeys-lru/' /etc/redis/redis.conf
sudo systemctl restart redis-server
```

### 4. Nginx Configuration
```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

#### Nginx Site Configuration
```nginx
# /etc/nginx/sites-available/pishkhanak.com
server {
    listen 80;
    server_name pishkhanak.com www.pishkhanak.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name pishkhanak.com www.pishkhanak.com;
    
    root /var/www/pishkhanak.com/public;
    index index.php index.html;
    
    # SSL Configuration
    ssl_certificate /etc/ssl/certs/pishkhanak.com.crt;
    ssl_certificate_key /etc/ssl/private/pishkhanak.com.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';" always;
    
    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    # Static Files Caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
    
    # Security
    location ~ /\.ht {
        deny all;
    }
    
    location ~ /\.env {
        deny all;
    }
    
    # Rate Limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
    location /api/ {
        limit_req zone=api burst=20 nodelay;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/pishkhanak.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Application Deployment

### 1. Clone Repository
```bash
sudo mkdir -p /var/www
sudo chown $USER:www-data /var/www
cd /var/www

git clone https://github.com/colorful-diamond/pishkhanak.git pishkhanak.com
cd pishkhanak.com
```

### 2. Install Dependencies
```bash
# PHP dependencies
composer install --optimize-autoloader --no-dev

# Node.js dependencies
npm install --production

# Build assets
npm run build
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### Production Environment Variables
```env
# Application
APP_NAME="Pishkhanak"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pishkhanak.com

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pishkhanak_production
DB_USERNAME=pishkhanak
DB_PASSWORD=your_secure_password

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls

# External APIs
JIBIT_CLIENT_ID=your_jibit_client_id
JIBIT_CLIENT_SECRET=your_jibit_secret
FINNOTECH_CLIENT_ID=your_finnotech_client_id
FINNOTECH_CLIENT_SECRET=your_finnotech_secret

# AI Services
OPENROUTER_API_KEY=your_openrouter_key
GEMINI_API_KEY=your_gemini_key

# SMS Service
SMS_API_KEY=your_sms_api_key

# Telegram Bot
TELEGRAM_BOT_TOKEN=your_telegram_token

# Logging
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=warning
```

### 4. Database Migration and Seeding
```bash
# Run migrations
php artisan migrate --force

# Seed essential data
php artisan db:seed --class=ProductionSeeder

# Create admin user
php artisan make:admin-user

# Set up roles and permissions
php artisan setup:roles-permissions
```

### 5. File Permissions
```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/pishkhanak.com
sudo chmod -R 755 /var/www/pishkhanak.com
sudo chmod -R 775 /var/www/pishkhanak.com/storage
sudo chmod -R 775 /var/www/pishkhanak.com/bootstrap/cache

# Create symbolic link for storage
php artisan storage:link
```

### 6. Optimization for Production
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize

# Create event cache
php artisan event:cache
```

## Queue and Background Processing

### 1. Supervisor Configuration
```bash
sudo nano /etc/supervisor/conf.d/pishkhanak-queue.conf
```

```ini
[program:pishkhanak-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pishkhanak.com/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/pishkhanak.com/storage/logs/queue.log
stopwaitsecs=3600
```

### 2. Laravel Reverb WebSocket Server
```bash
sudo nano /etc/supervisor/conf.d/pishkhanak-reverb.conf
```

```ini
[program:pishkhanak-reverb]
process_name=%(program_name)s
command=php /var/www/pishkhanak.com/artisan reverb:start --host=127.0.0.1 --port=8080
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/pishkhanak.com/storage/logs/reverb.log
```

### 3. Start Supervisor Services
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pishkhanak-queue:*
sudo supervisorctl start pishkhanak-reverb:*
```

## Scheduled Tasks (Cron)

### Setup Cron Job
```bash
sudo crontab -e -u www-data
```

Add the following line:
```cron
* * * * * cd /var/www/pishkhanak.com && php artisan schedule:run >> /dev/null 2>&1
```

## SSL Certificate Setup

### Using Let's Encrypt (Certbot)
```bash
sudo apt install -y certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d pishkhanak.com -d www.pishkhanak.com

# Auto-renewal
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

### Manual SSL Certificate
If using a purchased SSL certificate:
```bash
# Copy certificate files
sudo cp pishkhanak.com.crt /etc/ssl/certs/
sudo cp pishkhanak.com.key /etc/ssl/private/
sudo chmod 600 /etc/ssl/private/pishkhanak.com.key
```

## Monitoring and Logging

### 1. Log Rotation
```bash
sudo nano /etc/logrotate.d/pishkhanak
```

```
/var/www/pishkhanak.com/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        php /var/www/pishkhanak.com/artisan config:clear
    endscript
}
```

### 2. System Monitoring
```bash
# Install monitoring tools
sudo apt install -y htop iotop nethogs

# Monitor processes
htop

# Monitor queue status
php artisan queue:monitor

# Check system status
php artisan system:status
```

### 3. Application Health Checks
Create a health check endpoint:
```bash
php artisan make:command HealthCheck
```

Add to cron for automated monitoring:
```cron
*/5 * * * * cd /var/www/pishkhanak.com && php artisan health:check
```

## Backup Strategy

### 1. Database Backup Script
```bash
#!/bin/bash
# /var/www/pishkhanak.com/scripts/backup-database.sh

BACKUP_DIR="/var/backups/pishkhanak"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DB_NAME="pishkhanak_production"

# Create backup directory
mkdir -p $BACKUP_DIR

# PostgreSQL backup
pg_dump $DB_NAME | gzip > $BACKUP_DIR/database_$TIMESTAMP.sql.gz

# Keep only last 7 days of backups
find $BACKUP_DIR -name "database_*.sql.gz" -mtime +7 -delete

echo "Database backup completed: database_$TIMESTAMP.sql.gz"
```

### 2. File System Backup
```bash
#!/bin/bash
# /var/www/pishkhanak.com/scripts/backup-files.sh

BACKUP_DIR="/var/backups/pishkhanak"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
APP_DIR="/var/www/pishkhanak.com"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup important directories
tar -czf $BACKUP_DIR/storage_$TIMESTAMP.tar.gz $APP_DIR/storage/app
tar -czf $BACKUP_DIR/uploads_$TIMESTAMP.tar.gz $APP_DIR/public/uploads

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Files backup completed"
```

### 3. Automated Backup Cron
```cron
# Database backup every 6 hours
0 */6 * * * /var/www/pishkhanak.com/scripts/backup-database.sh

# File backup daily at 2 AM
0 2 * * * /var/www/pishkhanak.com/scripts/backup-files.sh
```

## Security Hardening

### 1. Firewall Configuration
```bash
# Install UFW
sudo apt install -y ufw

# Default policies
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Allow necessary ports
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS

# Enable firewall
sudo ufw enable
```

### 2. Fail2Ban Setup
```bash
# Install Fail2Ban
sudo apt install -y fail2ban

# Configure Nginx jail
sudo nano /etc/fail2ban/jail.local
```

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 3

[nginx-http-auth]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log

[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/error.log
maxretry = 10

[sshd]
enabled = true
port = 22
logpath = /var/log/auth.log
maxretry = 3
```

### 3. System Updates
```bash
# Enable automatic security updates
sudo apt install -y unattended-upgrades
sudo dpkg-reconfigure -plow unattended-upgrades
```

## Performance Optimization

### 1. PHP-FPM Optimization
```bash
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

```ini
[www]
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500

php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 300
php_admin_value[upload_max_filesize] = 10M
php_admin_value[post_max_size] = 10M
```

### 2. Redis Configuration
```bash
sudo nano /etc/redis/redis.conf
```

```
maxmemory 2gb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

### 3. Database Optimization (PostgreSQL)
```bash
sudo nano /etc/postgresql/13/main/postgresql.conf
```

```
shared_buffers = 1GB
effective_cache_size = 3GB
maintenance_work_mem = 256MB
checkpoint_completion_target = 0.7
wal_buffers = 16MB
default_statistics_target = 100
random_page_cost = 1.1
effective_io_concurrency = 200
```

## Deployment Script

### Automated Deployment
```bash
#!/bin/bash
# /var/www/pishkhanak.com/deploy.sh

set -e

echo "Starting deployment..."

# Enter maintenance mode
php artisan down

# Pull latest changes
git pull origin main

# Install/Update dependencies
composer install --optimize-autoloader --no-dev
npm install --production

# Build assets
npm run build

# Run database migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Restart queue workers
sudo supervisorctl restart pishkhanak-queue:*

# Exit maintenance mode
php artisan up

echo "Deployment completed successfully!"
```

Make it executable:
```bash
chmod +x /var/www/pishkhanak.com/deploy.sh
```

## Zero-Downtime Deployment

### Using Symlinks
```bash
#!/bin/bash
# /var/www/zero-downtime-deploy.sh

REPO_URL="https://github.com/colorful-diamond/pishkhanak.git"
DEPLOY_ROOT="/var/www"
APP_DIR="$DEPLOY_ROOT/pishkhanak.com"
SHARED_DIR="$DEPLOY_ROOT/shared"
RELEASES_DIR="$DEPLOY_ROOT/releases"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
RELEASE_DIR="$RELEASES_DIR/$TIMESTAMP"

# Create directories
mkdir -p $RELEASES_DIR $SHARED_DIR

# Clone latest code
git clone $REPO_URL $RELEASE_DIR
cd $RELEASE_DIR

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install --production
npm run build

# Link shared directories
ln -s $SHARED_DIR/.env $RELEASE_DIR/.env
ln -s $SHARED_DIR/storage $RELEASE_DIR/storage

# Run migrations and cache
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Switch to new release
ln -sfn $RELEASE_DIR $APP_DIR

# Restart services
sudo supervisorctl restart pishkhanak-queue:*
sudo systemctl reload php8.2-fpm

# Cleanup old releases (keep last 5)
cd $RELEASES_DIR && ls -t | tail -n +6 | xargs rm -rf

echo "Zero-downtime deployment completed!"
```

## Troubleshooting

### Common Issues

#### 1. Permission Errors
```bash
sudo chown -R www-data:www-data /var/www/pishkhanak.com
sudo chmod -R 755 /var/www/pishkhanak.com
sudo chmod -R 775 /var/www/pishkhanak.com/storage
sudo chmod -R 775 /var/www/pishkhanak.com/bootstrap/cache
```

#### 2. Queue Not Processing
```bash
# Check supervisor status
sudo supervisorctl status

# Restart queue workers
sudo supervisorctl restart pishkhanak-queue:*

# Check queue status
php artisan queue:monitor
```

#### 3. Database Connection Issues
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check database configuration
php artisan config:show database
```

#### 4. Cache Issues
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Logs and Debugging
```bash
# Application logs
tail -f /var/www/pishkhanak.com/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log

# System logs
journalctl -f -u nginx
journalctl -f -u php8.2-fpm
```

## Maintenance

### Regular Tasks
- **Daily**: Check application logs and system resources
- **Weekly**: Review security updates, backup verification
- **Monthly**: Performance analysis, optimization review
- **Quarterly**: Security audit, dependency updates

### Update Procedure
1. Test updates in staging environment
2. Create backup before updates
3. Put application in maintenance mode
4. Apply updates
5. Run tests
6. Exit maintenance mode
7. Monitor for issues

This deployment guide ensures a secure, scalable, and maintainable production environment for the Pishkhanak platform.