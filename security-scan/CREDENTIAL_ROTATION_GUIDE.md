# Credential Rotation Guide

## ⚠️ IMMEDIATE ACTIONS REQUIRED

### 1. Database Password Rotation
```bash
# PostgreSQL password change
sudo -u postgres psql
ALTER USER postgres PASSWORD 'NEW_SECURE_PASSWORD';
\q
```

### 2. Redis Password Configuration
```bash
# Run the Redis security script
./security-scan/configure-redis-security.sh
```

### 3. API Key Rotation

#### Google OAuth
1. Go to https://console.cloud.google.com
2. Navigate to APIs & Services > Credentials
3. Delete the exposed credentials
4. Create new OAuth 2.0 Client ID
5. Update authorized redirect URIs

#### Jibit Payment Gateway
1. Log into Jibit dashboard
2. Navigate to API Settings
3. Regenerate all API keys
4. Update webhook secrets

#### Finnotech
1. Access Finnotech developer portal
2. Revoke current client secret
3. Generate new credentials

#### OpenAI / OpenRouter
1. Visit respective dashboards
2. Revoke exposed keys immediately
3. Generate new API keys
4. Set spending limits

#### Gemini AI
1. Go to Google AI Studio
2. Delete exposed API keys
3. Create new restricted keys

#### Telegram Bot
1. Message @BotFather on Telegram
2. Use /revoke command
3. Generate new token with /token

### 4. Laravel Encryption Key
```bash
# Generate new APP_KEY
php artisan key:generate --show

# For ENC_KEY (custom encryption)
php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

### 5. Environment Variables Best Practices

#### Use Laravel Config Caching
```bash
php artisan config:cache
```

#### Implement Secrets Management
Consider using:
- AWS Secrets Manager
- HashiCorp Vault
- Azure Key Vault
- Kubernetes Secrets (if using K8s)

### 6. Security Checklist

- [ ] All API keys rotated
- [ ] Database password changed
- [ ] Redis password set
- [ ] Encryption keys regenerated
- [ ] .env file permissions set to 600
- [ ] Secrets management system implemented
- [ ] Monitoring alerts configured
- [ ] Audit logs reviewed

### 7. Monitoring Setup

```bash
# Monitor for unauthorized access
tail -f storage/logs/security.log

# Check for suspicious database queries
tail -f storage/logs/laravel.log | grep -E "(DROP|DELETE|UPDATE.*users)"
```

### 8. Post-Rotation Verification

```bash
# Test application functionality
php artisan tinker
>>> \DB::connection()->getPdo();
>>> \Cache::get('test');
>>> \Queue::push(new \App\Jobs\TestJob);
```

## 🔒 Long-term Security Improvements

1. **Implement Secret Rotation Policy**
   - Rotate credentials every 90 days
   - Use automated rotation where possible

2. **Enable MFA**
   - All service accounts
   - Admin panels
   - API management portals

3. **Audit Logging**
   - Log all credential usage
   - Alert on unusual patterns

4. **Principle of Least Privilege**
   - Separate credentials per environment
   - Limited scope API keys
   - Service-specific database users

5. **Security Monitoring**
   - Set up intrusion detection
   - Monitor for credential leaks
   - Regular security audits
