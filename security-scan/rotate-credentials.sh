#!/bin/bash

# Credential Rotation Helper Script
# This script assists with rotating exposed credentials

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔐 CREDENTIAL ROTATION ASSISTANT"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo -e "${RED}⚠️  CRITICAL: Your credentials have been exposed!${NC}"
echo "This script will guide you through rotating them."
echo ""

# Check if .env exists
if [ ! -f ".env" ]; then
    echo -e "${RED}❌ .env file not found!${NC}"
    exit 1
fi

# Backup current .env
BACKUP_FILE=".env.backup.$(date +%Y%m%d_%H%M%S)"
cp .env "$BACKUP_FILE"
echo -e "${GREEN}✅ Created backup: $BACKUP_FILE${NC}"
echo ""

# Function to generate strong password
generate_password() {
    openssl rand -base64 32 | tr -d "=+/" | cut -c1-25
}

# Function to update .env value
update_env() {
    local key=$1
    local value=$2
    
    if grep -q "^${key}=" .env; then
        # Use different sed syntax for macOS vs Linux
        if [[ "$OSTYPE" == "darwin"* ]]; then
            sed -i '' "s|^${key}=.*|${key}=${value}|" .env
        else
            sed -i "s|^${key}=.*|${key}=${value}|" .env
        fi
        echo -e "${GREEN}✅ Updated ${key}${NC}"
    else
        echo "${key}=${value}" >> .env
        echo -e "${GREEN}✅ Added ${key}${NC}"
    fi
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📋 CREDENTIAL ROTATION CHECKLIST"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# 1. Database Password
echo -e "${YELLOW}1. DATABASE PASSWORD${NC}"
echo "   Generate new password? (y/n): "
read -r rotate_db
if [ "$rotate_db" = "y" ]; then
    NEW_DB_PASS=$(generate_password)
    echo "   New password: $NEW_DB_PASS"
    echo ""
    echo "   ⚠️  UPDATE PostgreSQL NOW:"
    echo "   sudo -u postgres psql"
    echo "   ALTER USER ali_master WITH PASSWORD '$NEW_DB_PASS';"
    echo "   \\q"
    echo ""
    echo "   Password updated in PostgreSQL? (y/n): "
    read -r db_updated
    if [ "$db_updated" = "y" ]; then
        update_env "DB_PASSWORD" "\"$NEW_DB_PASS\""
    fi
fi
echo ""

# 2. Redis Password
echo -e "${YELLOW}2. REDIS PASSWORD${NC}"
echo "   Generate new password? (y/n): "
read -r rotate_redis
if [ "$rotate_redis" = "y" ]; then
    NEW_REDIS_PASS=$(generate_password)
    echo "   New password: $NEW_REDIS_PASS"
    echo ""
    echo "   ⚠️  UPDATE Redis NOW:"
    echo "   sudo nano /etc/redis/redis.conf"
    echo "   Add: requirepass $NEW_REDIS_PASS"
    echo "   sudo systemctl restart redis"
    echo ""
    echo "   Password updated in Redis? (y/n): "
    read -r redis_updated
    if [ "$redis_updated" = "y" ]; then
        update_env "REDIS_PASSWORD" "\"$NEW_REDIS_PASS\""
    fi
fi
echo ""

# 3. Encryption Keys
echo -e "${YELLOW}3. ENCRYPTION KEYS${NC}"
echo "   Generate new APP_KEY? (y/n): "
read -r rotate_app_key
if [ "$rotate_app_key" = "y" ]; then
    php artisan key:generate --show | while read -r NEW_KEY; do
        update_env "APP_KEY" "$NEW_KEY"
    done
fi

echo "   Generate new ENC_KEY? (y/n): "
read -r rotate_enc_key
if [ "$rotate_enc_key" = "y" ]; then
    NEW_ENC_KEY="base64:$(openssl rand -base64 32)"
    update_env "ENC_KEY" "$NEW_ENC_KEY"
fi
echo ""

# 4. API Keys Status
echo -e "${YELLOW}4. EXTERNAL API KEYS${NC}"
echo ""
echo "   ⚠️  MANUALLY ROTATE these in their respective portals:"
echo ""
echo "   [ ] Jibit API Keys"
echo "       - Contact Jibit support"
echo ""
echo "   [ ] Finnotech Client Secret"  
echo "       - Portal: https://apibeta.finnotech.ir"
echo ""
echo "   [ ] Google OAuth Credentials"
echo "       - Portal: https://console.cloud.google.com"
echo ""
echo "   [ ] OpenRouter API Key"
echo "       - Portal: https://openrouter.ai/keys"
echo ""
echo "   [ ] OpenAI API Key"
echo "       - Portal: https://platform.openai.com/api-keys"
echo ""
echo "   [ ] Gemini API Keys (all 3)"
echo "       - Portal: https://makersuite.google.com/app/apikey"
echo ""
echo "   [ ] Telegram Bot Token"
echo "       - Use @BotFather on Telegram"
echo "       - Commands: /revoke then /token"
echo ""

# 5. Clear caches
echo -e "${YELLOW}5. CLEARING CACHES${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Caches cleared${NC}"
echo ""

# 6. Test application
echo -e "${YELLOW}6. TESTING${NC}"
echo "   Test database connection..."
php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null && echo -e "${GREEN}✅ Database OK${NC}" || echo -e "${RED}❌ Database connection failed${NC}"

echo "   Test Redis connection..."
php artisan tinker --execute="Redis::ping();" 2>/dev/null && echo -e "${GREEN}✅ Redis OK${NC}" || echo -e "${RED}❌ Redis connection failed${NC}"
echo ""

# 7. Summary
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 ROTATION SUMMARY"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "✅ Backup created: $BACKUP_FILE"
echo ""
echo "⚠️  NEXT STEPS:"
echo "1. Rotate all external API keys manually"
echo "2. Update any deployment scripts with new credentials"
echo "3. Notify team members of credential changes"
echo "4. Monitor logs for authentication failures"
echo "5. Delete old backup files after confirming everything works"
echo ""
echo -e "${RED}🚨 IMPORTANT: Test your application thoroughly!${NC}"
echo ""

# Create rotation log
ROTATION_LOG="security-scan/credential-rotation.log"
echo "[$(date)] Credential rotation performed" >> "$ROTATION_LOG"
echo "Backup: $BACKUP_FILE" >> "$ROTATION_LOG"
echo "---" >> "$ROTATION_LOG"