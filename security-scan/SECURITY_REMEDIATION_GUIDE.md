# Security Remediation Guide - Pishkhanak Platform

## 🚨 IMMEDIATE ACTIONS REQUIRED

### 1. Rotate All Exposed Credentials

**CRITICAL**: Your credentials have been exposed and must be rotated immediately.

#### Database Password Rotation
```bash
# 1. Generate new strong password (minimum 20 characters)
openssl rand -base64 32

# 2. Update PostgreSQL user password
sudo -u postgres psql
ALTER USER ali_master WITH PASSWORD 'NEW_STRONG_PASSWORD_HERE';
\q

# 3. Update .env file
DB_PASSWORD="NEW_STRONG_PASSWORD_HERE"

# 4. Restart application
php artisan config:clear
php artisan cache:clear
```

#### API Key Rotation Checklist

| Service | Action Required | Portal Link |
|---------|----------------|-------------|
| **Jibit** | Regenerate API keys and secrets | Contact Jibit support |
| **Finnotech** | Request new client secret | https://apibeta.finnotech.ir |
| **Google OAuth** | Create new OAuth credentials | https://console.cloud.google.com |
| **OpenRouter** | Generate new API key | https://openrouter.ai/keys |
| **OpenAI** | Create new service account key | https://platform.openai.com/api-keys |
| **Gemini** | Generate new API keys (all 3) | https://makersuite.google.com/app/apikey |
| **Telegram Bot** | Revoke and create new bot token | https://t.me/BotFather |

### 2. Configure Redis Security

#### Set Redis Password
```bash
# 1. Generate strong Redis password
openssl rand -base64 32

# 2. Update Redis configuration
sudo nano /etc/redis/redis.conf

# Add or update:
requirepass YOUR_STRONG_REDIS_PASSWORD_HERE

# 3. Restart Redis
sudo systemctl restart redis

# 4. Update .env file
REDIS_PASSWORD="YOUR_STRONG_REDIS_PASSWORD_HERE"

# 5. Test connection
redis-cli -a YOUR_STRONG_REDIS_PASSWORD_HERE ping
```

#### Enable Redis ACL (Recommended)
```bash
# Create ACL user for Laravel application
redis-cli -a YOUR_REDIS_PASSWORD
ACL SETUSER laravel_app on >app_password_here ~* &* +@all
ACL SAVE
quit

# Update .env to use ACL user
REDIS_USERNAME=laravel_app
REDIS_PASSWORD="app_password_here"
```

### 3. Generate New Encryption Key

```bash
# 1. Generate new application key
php artisan key:generate

# 2. Generate new encryption key
echo "ENC_KEY=base64:$(openssl rand -base64 32)" >> .env

# 3. If you have encrypted data, rotate it:
php artisan tinker
>>> use Illuminate\Support\Facades\Crypt;
>>> // Decrypt with old key, encrypt with new key
```

## 📋 Security Hardening Checklist

### Environment Configuration
- [ ] `.env` file is NOT in version control
- [ ] All passwords are strong (20+ characters)
- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production` in production
- [ ] Redis password is set
- [ ] Database uses SSL connection
- [ ] Session driver changed to Redis

### Application Security
- [ ] CSRF protection enabled on all forms
- [ ] Rate limiting configured
- [ ] Security headers implemented
- [ ] Input validation on all endpoints
- [ ] SQL injection fixes applied
- [ ] Command injection fixes applied

### Monitoring & Logging
- [ ] Failed login attempts logged
- [ ] API usage monitored
- [ ] Suspicious activity alerts configured
- [ ] Regular security audits scheduled

## 🔐 Best Practices for Credential Management

### 1. Use Environment-Specific Files
```bash
# Development
.env.local

# Staging
.env.staging

# Production
.env.production
```

### 2. Implement Secrets Management

#### Option A: Laravel Encrypted Env
```bash
# Encrypt production env
php artisan env:encrypt --env=production

# Deploy only the encrypted file
# Decrypt on server with key
php artisan env:decrypt --env=production --key=YOUR_ENCRYPTION_KEY
```

#### Option B: HashiCorp Vault Integration
```php
// config/services.php
'vault' => [
    'address' => env('VAULT_ADDR'),
    'token' => env('VAULT_TOKEN'),
    'path' => env('VAULT_PATH', 'secret/data/pishkhanak'),
];
```

#### Option C: AWS Secrets Manager
```bash
# Store secrets in AWS
aws secretsmanager create-secret \
    --name pishkhanak/production \
    --secret-string file://.env.production

# Retrieve in application
composer require aws/aws-sdk-php
```

### 3. Implement Key Rotation Policy

```php
// app/Console/Commands/RotateApiKeys.php
class RotateApiKeys extends Command
{
    protected $signature = 'security:rotate-keys';
    
    public function handle()
    {
        // Automated key rotation logic
        // Alert admins for manual rotations needed
    }
}

// Schedule monthly rotation
$schedule->command('security:rotate-keys')->monthly();
```

## 🛡️ Security Headers Configuration

Add to your web server configuration:

### Nginx
```nginx
# /etc/nginx/sites-available/pishkhanak.com
add_header X-Frame-Options "DENY" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
```

### Laravel Middleware
```php
// app/Http/Middleware/SecurityHeaders.php
class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        
        return $response;
    }
}
```

## 🔍 Monitoring Commands

### Check Security Status
```bash
# Run security audit
composer audit

# Check for exposed credentials
grep -r "sk-" . --exclude-dir=vendor --exclude-dir=node_modules
grep -r "AIza" . --exclude-dir=vendor --exclude-dir=node_modules

# Monitor failed login attempts
tail -f storage/logs/laravel.log | grep "failed"

# Check Redis security
redis-cli INFO security
```

### Regular Security Tasks
```bash
# Daily
php artisan security:check-api-usage
php artisan security:monitor-failed-logins

# Weekly
composer audit
npm audit

# Monthly
php artisan security:rotate-keys
php artisan security:audit-permissions
```

## 📊 Security Audit Log

Track all security-related changes:

```php
// Create audit log table
php artisan make:migration create_security_audit_logs_table

// Log security events
SecurityAudit::log('credential_rotation', [
    'service' => 'jibit',
    'rotated_by' => Auth::id(),
    'ip_address' => request()->ip(),
]);
```

## 🚀 Deployment Security Checklist

Before deploying to production:

1. [ ] All credentials rotated
2. [ ] .env file secured and encrypted
3. [ ] Debug mode disabled
4. [ ] HTTPS enforced
5. [ ] Rate limiting configured
6. [ ] Security headers set
7. [ ] Monitoring enabled
8. [ ] Backups configured
9. [ ] Incident response plan ready
10. [ ] Team notified of changes

## 📞 Emergency Contacts

- **Database Admin**: [Contact info]
- **Security Team Lead**: [Contact info]
- **DevOps On-Call**: [Contact info]
- **Payment Gateway Support**: [Contact info]

## 📝 Notes

- Keep this document updated with any security changes
- Review monthly for compliance
- Conduct quarterly security audits
- Train team on security best practices

---
**Last Updated**: 2025-09-07
**Next Review**: 2025-10-07