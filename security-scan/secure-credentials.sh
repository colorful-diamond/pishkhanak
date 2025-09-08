#!/bin/bash

# Secure Credentials Management Script
# This script helps rotate and secure all exposed credentials

set -e

echo "╔═══════════════════════════════════════════════════════════════╗"
echo "║          SECURE CREDENTIALS MANAGEMENT SYSTEM                 ║"
echo "╚═══════════════════════════════════════════════════════════════╝"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root (not recommended)
if [ "$EUID" -eq 0 ]; then 
   echo -e "${RED}❌ Please do not run this script as root${NC}"
   exit 1
fi

# Create backup directory
BACKUP_DIR="./security-scan/backups/$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

echo -e "${YELLOW}📁 Creating backup of current .env file...${NC}"
cp .env "$BACKUP_DIR/.env.backup"
echo -e "${GREEN}✅ Backup created at: $BACKUP_DIR/.env.backup${NC}"

# Function to generate secure random strings
generate_secure_key() {
    local length=${1:-32}
    openssl rand -base64 "$length" | tr -d "=+/" | cut -c1-"$length"
}

# Function to generate Laravel APP_KEY
generate_laravel_key() {
    echo "base64:$(openssl rand -base64 32)"
}

echo ""
echo -e "${YELLOW}🔑 Generating new secure keys...${NC}"

# Generate new keys
NEW_APP_KEY=$(generate_laravel_key)
NEW_ENC_KEY=$(generate_laravel_key)
NEW_DB_PASSWORD=$(generate_secure_key 20)
NEW_REDIS_PASSWORD=$(generate_secure_key 24)

# Create secure .env template
cat > .env.secure.template << 'EOF'
# SECURITY WARNING: This is a template file with placeholders
# Replace all CHANGE_ME values with actual credentials
# Store actual credentials in a secure secrets management system

APP_NAME="Pishkhanak"
APP_ENV=production
APP_KEY=CHANGE_ME_APP_KEY
APP_DEBUG=false
APP_URL=https://pishkhanak.com

# Logging Configuration
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pishkhanak
DB_USERNAME=postgres
DB_PASSWORD=CHANGE_ME_DB_PASSWORD

# Cache & Session Configuration
BROADCAST_DRIVER=reverb
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Redis Configuration (with password)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=CHANGE_ME_REDIS_PASSWORD
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# OAuth Credentials (Store in secrets manager)
GOOGLE_CLIENT_ID=CHANGE_ME_GOOGLE_CLIENT_ID
GOOGLE_CLIENT_SECRET=CHANGE_ME_GOOGLE_CLIENT_SECRET
GOOGLE_REDIRECT_URI=https://pishkhanak.com/auth/google/callback

# Payment Gateway Credentials (Store in secrets manager)
JIBIT_API_KEY=CHANGE_ME_JIBIT_API_KEY
JIBIT_SECRET_KEY=CHANGE_ME_JIBIT_SECRET_KEY
JIBIT_PPG_API_KEY=CHANGE_ME_JIBIT_PPG_API_KEY
JIBIT_PPG_API_SECRET=CHANGE_ME_JIBIT_PPG_API_SECRET
JIBIT_PPG_WEBHOOK_SECRET=CHANGE_ME_JIBIT_PPG_WEBHOOK_SECRET

# Finnotech Credentials (Store in secrets manager)
FINNOTECH_CLIENT_ID=CHANGE_ME_FINNOTECH_CLIENT_ID
FINNOTECH_CLIENT_SECRET=CHANGE_ME_FINNOTECH_CLIENT_SECRET

# AI Service Keys (Store in secrets manager)
OPENROUTER_API_KEY=CHANGE_ME_OPENROUTER_API_KEY
OPENAI_API_KEY=CHANGE_ME_OPENAI_API_KEY
GEMINI_API_KEY=CHANGE_ME_GEMINI_API_KEY

# Telegram Bot (Store in secrets manager)
TELEGRAM_BOT_TOKEN=CHANGE_ME_TELEGRAM_BOT_TOKEN

# Encryption Key (Generate with: php artisan key:generate)
ENC_KEY=CHANGE_ME_ENC_KEY

# Security Headers
SECURE_HEADERS_ENABLED=true
FORCE_HTTPS=true

# Rate Limiting
RATE_LIMIT_ENABLED=true
LOGIN_MAX_ATTEMPTS=5
LOGIN_DECAY_MINUTES=15

# Audit Logging
AUDIT_LOG_ENABLED=true
SECURITY_LOG_CHANNEL=security
EOF

echo -e "${GREEN}✅ Secure template created: .env.secure.template${NC}"

# Create secrets management script
cat > ./security-scan/manage-secrets.php << 'EOF'
<?php

/**
 * Secrets Management Helper
 * This script helps manage secrets securely
 */

class SecretsManager {
    private $secretsFile = '.secrets.encrypted';
    private $keyFile = '.key';
    
    public function encrypt($data, $key) {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt(
            json_encode($data),
            'AES-256-CBC',
            $key,
            0,
            $iv
        );
        return base64_encode($iv . $encrypted);
    }
    
    public function decrypt($data, $key) {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        $decrypted = openssl_decrypt(
            $encrypted,
            'AES-256-CBC',
            $key,
            0,
            $iv
        );
        return json_decode($decrypted, true);
    }
    
    public function storeSecrets($secrets) {
        $key = bin2hex(openssl_random_pseudo_bytes(32));
        $encrypted = $this->encrypt($secrets, $key);
        
        file_put_contents($this->secretsFile, $encrypted);
        file_put_contents($this->keyFile, $key);
        
        chmod($this->secretsFile, 0600);
        chmod($this->keyFile, 0600);
        
        echo "Secrets encrypted and stored securely.\n";
        echo "Key file: {$this->keyFile}\n";
        echo "Secrets file: {$this->secretsFile}\n";
        echo "\nIMPORTANT: Store the key file separately from the secrets!\n";
    }
    
    public function loadSecrets() {
        if (!file_exists($this->keyFile) || !file_exists($this->secretsFile)) {
            throw new Exception("Secrets or key file not found");
        }
        
        $key = file_get_contents($this->keyFile);
        $encrypted = file_get_contents($this->secretsFile);
        
        return $this->decrypt($encrypted, $key);
    }
}

// Example usage (commented out for safety)
// $manager = new SecretsManager();
// $secrets = [
//     'db_password' => 'your-secure-password',
//     'api_keys' => [
//         'openai' => 'your-api-key',
//         // ... other keys
//     ]
// ];
// $manager->storeSecrets($secrets);
EOF

# Create credential rotation checklist
cat > ./security-scan/CREDENTIAL_ROTATION_GUIDE.md << 'EOF'
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
EOF

echo -e "${GREEN}✅ Created credential rotation guide${NC}"

# Update file permissions
echo ""
echo -e "${YELLOW}🔒 Securing file permissions...${NC}"
chmod 600 .env
chmod 600 .env.secure.template
chmod 700 ./security-scan/*.sh

echo ""
echo "╔═══════════════════════════════════════════════════════════════╗"
echo "║                    NEXT STEPS REQUIRED                        ║"
echo "╚═══════════════════════════════════════════════════════════════╝"
echo ""
echo "1. IMMEDIATELY rotate all exposed credentials:"
echo "   - Database password"
echo "   - All API keys (Google, Jibit, Finnotech, AI services)"
echo "   - Telegram bot token"
echo ""
echo "2. Use the secure template:"
echo "   cp .env.secure.template .env"
echo "   Then replace CHANGE_ME values with new credentials"
echo ""
echo "3. Generate new encryption keys:"
echo "   php artisan key:generate"
echo "   php -r \"echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;\""
echo ""
echo "4. Configure Redis password:"
echo "   ./security-scan/configure-redis-security.sh"
echo ""
echo "5. Review the credential rotation guide:"
echo "   cat ./security-scan/CREDENTIAL_ROTATION_GUIDE.md"
echo ""
echo -e "${RED}⚠️  WARNING: The exposed credentials are still active!${NC}"
echo -e "${RED}    Rotate them immediately to prevent unauthorized access.${NC}"