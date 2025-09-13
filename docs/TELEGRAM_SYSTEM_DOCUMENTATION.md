# Pishkhanak Telegram Bot & Admin Panel System Documentation

## Table of Contents
1. [System Architecture Overview](#system-architecture-overview)
2. [Database Schema and Models](#database-schema-and-models)
3. [Authentication & Security](#authentication--security)
4. [Command Reference](#command-reference)
5. [API Endpoints](#api-endpoints)
6. [Configuration Guide](#configuration-guide)
7. [Deployment Instructions](#deployment-instructions)
8. [Troubleshooting Guide](#troubleshooting-guide)

---

## System Architecture Overview

The Pishkhanak Telegram system is built on Laravel 11 with PostgreSQL and implements a comprehensive admin panel for managing a financial services platform with Persian language support.

### Core Components

#### 1. Telegram Bot Controller
- **File**: `app/Http/Controllers/TelegramBotController.php`
- **Purpose**: Main webhook handler for incoming Telegram updates
- **Features**:
  - Webhook signature verification
  - Update routing to appropriate handlers
  - Administrative functions (set/remove webhook, test bot)

#### 2. Command Handlers Architecture
```
app/Services/Telegram/Handlers/
├── AbstractCommandHandler.php     # Base handler class
├── AdminCommandHandler.php        # Admin panel commands
├── GeneralCommandHandler.php      # Public bot commands
├── TicketCommandHandler.php       # Support ticket system
├── MessageHandler.php             # Message processing
├── CallbackQueryHandler.php       # Inline keyboard callbacks
└── InlineQueryHandler.php         # Inline query responses
```

#### 3. Core Services
```
app/Services/Telegram/Core/
├── AdminAuthService.php           # Admin authentication & sessions
├── AuditLogger.php               # Comprehensive audit logging
├── UpdateContext.php             # Request context wrapper
└── PersianTextProcessor.php      # Persian language handling
```

#### 4. Security Middleware
- **TelegramWebhookAuth**: HMAC-SHA256 signature verification
- **TelegramRateLimit**: Intelligent rate limiting by IP/user/command type
- **SecurityHeaders**: HTTP security headers

### Request Flow
1. **Webhook Receives Update** → TelegramWebhookAuth → TelegramRateLimit
2. **Route to Controller** → TelegramBotController::webhook()
3. **Create UpdateContext** → Extract user, chat, command details
4. **Route to Handler** → Match command to appropriate handler
5. **Execute Command** → Process with authentication/authorization
6. **Audit Logging** → Record all actions with Persian context
7. **Response** → Send formatted Persian response to user

---

## Database Schema and Models

### Admin System Tables

#### telegram_admins
```sql
CREATE TABLE telegram_admins (
    id BIGSERIAL PRIMARY KEY,
    telegram_user_id VARCHAR(20) UNIQUE NOT NULL,
    username VARCHAR(255),
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255),
    role ENUM('super_admin', 'admin', 'moderator', 'support', 'read_only') DEFAULT 'support',
    permissions JSON DEFAULT '[]',
    is_active BOOLEAN DEFAULT true,
    last_login_at TIMESTAMP,
    failed_login_attempts INTEGER DEFAULT 0,
    locked_until TIMESTAMP,
    created_by VARCHAR(20),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### telegram_admin_sessions
- Secure session management with expiration
- IP and user agent hashing for privacy
- Automatic cleanup of expired sessions

#### telegram_audit_logs
- Complete action logging with before/after values
- Resource tracking (user, ticket, post, etc.)
- IP and user agent logging for security

#### telegram_security_events
- Security event monitoring
- Severity levels: info, warning, error, critical
- Failed login attempts, permission violations

### Ticketing System Tables

#### telegram_tickets
```sql
CREATE TABLE telegram_tickets (
    id BIGSERIAL PRIMARY KEY,
    user_id VARCHAR(20) NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    status ENUM('open', 'waiting_admin', 'waiting_user', 'closed') DEFAULT 'open',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    assigned_to VARCHAR(20),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### telegram_ticket_messages
- Message history for each ticket
- Admin/user message differentiation
- Timestamp-based ordering

### Content Management Tables

#### telegram_posts
- Content creation and scheduling
- Multi-status workflow (draft, scheduled, published, archived)
- Admin authorship tracking

#### ai_content_templates
- Reusable AI prompt templates
- Category organization
- Usage statistics tracking

### Model Features

#### TelegramAdmin Model
- **Role-based Access Control**: 5-level hierarchy with numeric comparison
- **Permission System**: Explicit permissions array + role-based defaults
- **Security Features**: Account locking, failed attempt tracking
- **Persian Display**: Localized role names and display formatting

```php
// Permission levels (higher = more access)
const PERMISSION_LEVELS = [
    'read_only' => 1,
    'support' => 2, 
    'moderator' => 3,
    'admin' => 4,
    'super_admin' => 5
];

// Usage examples
$admin->canAccessCommand('user_ban');      // Check command access
$admin->hasPermission('delete_posts');     // Check explicit permission
$admin->isLocked();                        // Check if account is locked
$admin->display_name;                      // Get formatted Persian name
```

---

## Authentication & Security

### Multi-Layer Security Architecture

#### 1. Webhook Authentication
- **HMAC Signature Verification**: Uses `TELEGRAM_WEBHOOK_SECRET`
- **Request Structure Validation**: Validates Telegram update format
- **Content-Type Enforcement**: Requires `application/json`

```php
// Middleware: TelegramWebhookAuth
private function verifyWebhookSignature(Request $request): bool
{
    $secretToken = env('TELEGRAM_WEBHOOK_SECRET');
    $receivedToken = $request->header('X-Telegram-Bot-Api-Secret-Token');
    return hash_equals($secretToken, $receivedToken);
}
```

#### 2. Admin Authentication System
- **Session-Based Authentication**: Secure token generation with expiration
- **Role-Based Authorization**: 5-level permission hierarchy
- **Account Security**: Failed attempt tracking, automatic lockouts
- **Privacy Protection**: IP and User-Agent hashing

```php
// AdminAuthService usage
$authResult = $authService->authenticate($telegramUserId, [
    'ip' => $context->getMetadata('ip'),
    'user_agent' => $context->getMetadata('user_agent')
]);

if ($authResult->isSuccess()) {
    $admin = $authResult->getAdmin();
    $session = $authResult->getSession();
}
```

#### 3. Rate Limiting System
Intelligent rate limiting with different limits for different operations:

- **Webhook Requests**: 30/minute per IP
- **Admin Commands**: 10/minute per user
- **User Commands**: 20/minute per user  
- **Persian Text Processing**: 15/minute per user (CPU intensive)

#### 4. Audit Logging
Comprehensive logging of all admin actions:

```php
// Every admin action is logged
$this->auditLogger->logAdminAction(
    $admin->id,
    'user_ban',           // Action type
    'user',               // Resource type
    $userId,              // Resource ID
    [                     // Additional context
        'reason' => $reason,
        'ip_hash' => $ipHash
    ]
);
```

### Security Event Types
- `auth_attempt_invalid_user` - Unknown user login attempt
- `auth_attempt_inactive_user` - Inactive account access
- `auth_attempt_locked_user` - Locked account access
- `permission_denied` - Insufficient permissions
- `unauthorized_access` - No authentication

---

## Command Reference

### Public User Commands

#### Support System
- `/start` - Welcome message and main menu
- `/help` - Help and command list
- `/ticket` - Create new support ticket
- `/tickets` - View user's tickets
- `/status` - Check service status

#### Service Commands  
- `/services` - Available financial services
- `/credit` - Credit score inquiry
- `/violations` - Traffic violation check
- `/wallet` - Wallet balance and operations

### Admin Commands

#### Authentication
- `/admin`, `/login` - Admin panel login
- Automatic session management with 1-hour expiration
- Role verification for all subsequent commands

#### Dashboard & Analytics
- `/dashboard`, `/panel`, `/menu` - Main admin dashboard
- `/stats` - Comprehensive system statistics
- Real-time metrics and recent activity

#### User Management (Moderator+)
- `/users` - User management interface
- User search, ban/unban operations
- Account status modifications

#### Financial Management (Admin+)
- `/wallets` - Wallet management
- Balance adjustments with full audit trail
- Transaction monitoring

#### Ticket System (Support+)
- `/tickets` - Admin ticket management
- Ticket assignment and status updates
- Response templates and escalation

#### Content Management (Support+)
- `/posts` - Content creation and management
- Scheduling and publishing workflow
- AI-assisted content generation

#### System Administration (Super Admin)
- `/config`, `/settings` - System configuration
- `/security` - Security monitoring and alerts
- `/tokens` - API token management

### Command Permission Matrix

| Command Category | read_only | support | moderator | admin | super_admin |
|-----------------|-----------|---------|-----------|--------|-------------|
| Dashboard View  | ✅        | ✅      | ✅        | ✅     | ✅          |
| User View       | ❌        | ✅      | ✅        | ✅     | ✅          |
| User Ban/Unban  | ❌        | ❌      | ✅        | ✅     | ✅          |
| Wallet Adjust   | ❌        | ❌      | ❌        | ✅     | ✅          |
| System Config   | ❌        | ❌      | ❌        | ✅     | ✅          |
| Admin Creation  | ❌        | ❌      | ❌        | ❌     | ✅          |

---

## API Endpoints

### Webhook Endpoints

#### Main Bot Webhook
```
POST /api/telegram/webhook
Content-Type: application/json
X-Telegram-Bot-Api-Secret-Token: {WEBHOOK_SECRET}

Middleware:
- telegram.webhook.auth (signature verification)
- telegram.rate.limit:webhook (30 req/min per IP)
```

### Management Endpoints (Authenticated)

#### Webhook Management
```
POST /telegram/set-webhook       - Set webhook URL
POST /telegram/remove-webhook    - Remove webhook
GET  /telegram/webhook-info      - Get webhook status
GET  /telegram/test             - Test bot connection
POST /telegram/test-notification - Send test message
```

#### Admin Management (Super Admin Only)
```
GET    /telegram/admin/users           - List admin users
POST   /telegram/admin/users           - Add admin user
DELETE /telegram/admin/users/{userId}  - Remove admin user
GET    /telegram/admin/security-logs   - View security logs
POST   /telegram/admin/clear-rate-limit - Clear rate limits
GET    /telegram/admin/stats           - Bot statistics
```

#### Public Information (Rate Limited)
```
GET /telegram/public/info    - Bot information
GET /telegram/public/status  - Service status
```

### API Response Formats

#### Success Response
```json
{
    "ok": true,
    "result": {
        "message": "عملیات با موفقیت انجام شد",
        "data": { /* response data */ }
    }
}
```

#### Error Response
```json
{
    "ok": false,
    "error": {
        "code": "UNAUTHORIZED",
        "message": "دسترسی مجاز نیست",
        "details": "شما مجاز به دسترسی به این بخش نیستید"
    }
}
```

---

## Configuration Guide

### Environment Variables

#### Core Telegram Settings
```bash
# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=123456789:ABCdefGhiJKlmnoPQRstuvWXyz
TELEGRAM_WEBHOOK_SECRET=your-secure-webhook-secret
TELEGRAM_WEBHOOK_URL=https://yourdomain.com/api/telegram/webhook

# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_DATABASE=pishkhanak
DB_USERNAME=ali_master
DB_PASSWORD=your-secure-password
```

### Telegram Admin Configuration

#### File: `config/telegram-admin.php`

```php
// Permission levels (higher = more access)
'permission_levels' => [
    'read_only' => 1,
    'support' => 2,
    'moderator' => 3,
    'admin' => 4,
    'super_admin' => 5,
],

// Command permissions
'command_permissions' => [
    'dashboard' => 'read_only',
    'stats' => 'read_only',
    'users' => 'support',
    'user_ban' => 'moderator',
    'wallet_balance_adjust' => 'admin',
    'admin_create' => 'super_admin',
],

// Rate limiting
'rate_limits' => [
    'admin_commands' => '60:1',      # 60 per minute
    'sensitive_operations' => '10:1', # 10 per minute
    'broadcasts' => '5:60',          # 5 per hour
],

// Security settings
'security' => [
    'session_duration' => 3600,      # 1 hour
    'max_failed_attempts' => 5,
    'lockout_duration' => 1800,      # 30 minutes
],
```

### Persian Language Configuration

```php
'localization' => [
    'number_format' => [
        'currency_symbol' => 'تومان',
        'currency_position' => 'after',
    ],
    'date_format' => [
        'short' => 'Y/m/d',
        'datetime' => 'Y/m/d H:i',
    ],
    'messages' => [
        'success_prefix' => '✅',
        'error_prefix' => '❌',
        'warning_prefix' => '⚠️',
    ],
],
```

### Middleware Registration

#### File: `bootstrap/app.php`
```php
$middleware->alias([
    'telegram.webhook.auth' => TelegramWebhookAuth::class,
    'telegram.rate.limit' => TelegramRateLimit::class,
]);
```

---

## Deployment Instructions

### Production Environment Setup

#### 1. Server Requirements
- **PHP**: 8.3+
- **Database**: PostgreSQL 13+
- **Cache**: Redis (recommended)
- **Web Server**: Nginx with HTTPS
- **SSL**: Required for Telegram webhooks

#### 2. Installation Steps

```bash
# Clone repository
git clone https://github.com/your-repo/pishkhanak.git
cd pishkhanak

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm install --production
npm run build

# Set up environment
cp .env.example .env
# Edit .env with production values

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed initial admin user
php artisan db:seed --class=TelegramAdminSeeder

# Set up storage links
php artisan storage:link

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 3. Telegram Bot Setup

```bash
# Set webhook URL
php artisan telegram:set-webhook

# Test webhook
php artisan telegram:test-webhook

# Verify bot status
php artisan telegram:get-me
```

#### 4. Database Backup Strategy

```bash
# Create backup script
#!/bin/bash
BACKUP_DIR="/backups/pishkhanak"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
pg_dump -h 127.0.0.1 -U ali_master -d pishkhanak > "$BACKUP_DIR/backup_$TIMESTAMP.sql"

# Schedule with cron (daily at 2 AM)
0 2 * * * /path/to/backup-script.sh
```

#### 5. Monitoring Setup

```bash
# Laravel Horizon for queue monitoring (if using)
php artisan horizon:install
php artisan horizon:publish

# Set up log monitoring
tail -f storage/logs/laravel.log | grep -i telegram

# Set up health checks
curl -f https://yourdomain.com/up || exit 1
```

### Nginx Configuration

```nginx
server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    
    ssl_certificate /path/to/certificate.pem;
    ssl_certificate_key /path/to/private-key.pem;
    
    root /path/to/pishkhanak/public;
    index index.php;
    
    # Telegram webhook endpoint
    location /api/telegram/webhook {
        try_files $uri $uri/ /index.php?$query_string;
        
        # Security headers
        add_header X-Frame-Options DENY;
        add_header X-Content-Type-Options nosniff;
        
        # Rate limiting
        limit_req zone=api burst=10 nodelay;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Troubleshooting Guide

### Common Issues

#### 1. Webhook Authentication Failures

**Symptoms**:
- 403 Unauthorized responses
- Log entries: "Invalid signature"

**Solutions**:
```bash
# Check webhook secret
grep TELEGRAM_WEBHOOK_SECRET .env

# Verify Telegram bot token
php artisan telegram:get-me

# Test webhook manually
curl -X POST https://api.telegram.org/bot{TOKEN}/setWebhook \
  -H "Content-Type: application/json" \
  -d '{"url":"https://yourdomain.com/api/telegram/webhook","secret_token":"your-secret"}'
```

#### 2. Database Connection Issues

**Symptoms**:
- "Connection refused" errors
- Admin login failures

**Solutions**:
```bash
# Test database connection
pg_isready -h 127.0.0.1 -p 5432 -U ali_master

# Check migration status
php artisan migrate:status

# Run missing migrations
php artisan migrate
```

#### 3. Rate Limiting Problems

**Symptoms**:
- 429 Too Many Requests
- Bot becomes unresponsive

**Solutions**:
```bash
# Check rate limit status
php artisan tinker
>>> use Illuminate\Support\Facades\RateLimiter;
>>> RateLimiter::attempts('telegram_rate_limit:webhook:ip:1.2.3.4');

# Clear rate limits
php artisan cache:clear
# Or specifically:
>>> RateLimiter::clear('telegram_rate_limit:webhook:ip:1.2.3.4');
```

#### 4. Admin Session Issues

**Symptoms**:
- Automatic logouts
- "Session expired" messages

**Solutions**:
```bash
# Check session configuration
grep SESSION_ .env

# Clear expired sessions
php artisan telegram:cleanup-sessions

# Check admin account status
php artisan tinker
>>> $admin = App\Models\TelegramAdmin::where('telegram_user_id', 'USER_ID')->first();
>>> $admin->isLocked();
>>> $admin->unlockAccount();
```

#### 5. Persian Text Display Issues

**Symptoms**:
- Garbled Persian text
- RTL formatting problems

**Solutions**:
```bash
# Check database collation
psql -h 127.0.0.1 -U ali_master -d pishkhanak
=> \l+ pishkhanak

# Should show UTF8 encoding

# Test Persian text processing
php artisan tinker
>>> $processor = app(App\Services\Telegram\Core\PersianTextProcessor::class);
>>> $processor->formatPersianText('تست متن فارسی');
```

### Debugging Commands

#### System Health Check
```bash
# Check all system components
php artisan telegram:health-check

# Sample output:
# ✅ Database: Connected
# ✅ Telegram API: Accessible  
# ✅ Webhook: Active
# ❌ Redis: Connection failed
```

#### Log Analysis
```bash
# Monitor real-time logs
tail -f storage/logs/laravel.log | grep -E "(telegram|admin|auth)"

# Check specific error patterns
grep -n "TELEGRAM_ERROR" storage/logs/laravel-$(date +%Y-%m-%d).log

# Audit log analysis
php artisan telegram:audit-report --days=7
```

#### Performance Monitoring
```bash
# Check admin session count
php artisan tinker
>>> App\Models\TelegramAdminSession::where('expires_at', '>', now())->count();

# Monitor webhook response times
grep "webhook response" storage/logs/laravel.log | tail -20

# Database query analysis
php artisan telescope:clear  # If using Telescope
```

### Emergency Procedures

#### 1. Bot Disabled by Rate Limiting
```bash
# Emergency rate limit reset
php artisan telegram:emergency-reset

# Alternative manual method
php artisan tinker
>>> use Illuminate\Support\Facades\Cache;
>>> Cache::tags(['rate-limiting'])->flush();
```

#### 2. Compromised Admin Account
```bash
# Immediately lock admin account
php artisan telegram:lock-admin --user-id=TELEGRAM_USER_ID

# Revoke all sessions
php artisan telegram:revoke-sessions --user-id=TELEGRAM_USER_ID

# Review security events
php artisan telegram:security-report --admin-id=ADMIN_ID --days=1
```

#### 3. Database Corruption
```bash
# Emergency backup
pg_dump -h 127.0.0.1 -U ali_master -d pishkhanak > emergency_backup_$(date +%s).sql

# Check table integrity
php artisan telegram:check-integrity

# Restore from backup if needed
psql -h 127.0.0.1 -U ali_master -d pishkhanak < backup_file.sql
```

### Support Contacts

- **System Administrator**: Contact details for database/server issues
- **Telegram Bot API**: https://core.telegram.org/bots/api
- **Laravel Documentation**: https://laravel.com/docs/11.x
- **PostgreSQL Support**: Community forums and documentation

---

## Appendix

### File Structure Reference
```
app/
├── Http/
│   ├── Controllers/
│   │   └── TelegramBotController.php
│   └── Middleware/
│       ├── TelegramWebhookAuth.php
│       └── TelegramRateLimit.php
├── Models/
│   ├── TelegramAdmin.php
│   ├── TelegramAdminSession.php
│   ├── TelegramAuditLog.php
│   └── TelegramSecurityEvent.php
└── Services/
    └── Telegram/
        ├── Core/
        │   ├── AdminAuthService.php
        │   ├── AuditLogger.php
        │   └── UpdateContext.php
        └── Handlers/
            ├── AdminCommandHandler.php
            ├── GeneralCommandHandler.php
            └── TicketCommandHandler.php

config/
└── telegram-admin.php

database/
└── migrations/
    ├── 2025_09_08_160000_create_telegram_admin_system_tables.php
    └── 2025_09_08_150848_create_telegram_tickets_table.php

routes/
└── telegram.php
```

### Security Checklist

- [ ] TELEGRAM_WEBHOOK_SECRET configured and secure
- [ ] Database credentials properly secured
- [ ] HTTPS enabled for all webhook endpoints
- [ ] Rate limiting configured appropriately
- [ ] Admin accounts use strong authentication
- [ ] Audit logging enabled for all sensitive operations
- [ ] Regular security event monitoring
- [ ] Database backups automated
- [ ] Persian text handling tested

### Version History

- **v1.0** - Initial admin panel implementation
- **v1.1** - Enhanced security features and Persian support
- **v1.2** - Comprehensive audit logging system
- **Current** - Full production-ready implementation

---

*This documentation covers the complete Pishkhanak Telegram Bot and Admin Panel system. For additional technical details, refer to the inline code documentation and Laravel framework documentation.*