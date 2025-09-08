#!/bin/bash

# Comprehensive Credential Rotation Guide
# This script guides you through rotating all exposed credentials

echo "═══════════════════════════════════════════════════════════════"
echo "             CRITICAL SECURITY: Credential Rotation"
echo "═══════════════════════════════════════════════════════════════"
echo ""

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${RED}⚠️  CRITICAL: Your credentials have been exposed and must be rotated immediately!${NC}"
echo ""

echo -e "${YELLOW}Step 1: Laravel Application Key${NC}"
echo "New key generated: base64:u1mYXZ2cazpWEqJFgLa+sls9szTXDawLy3/envbBWTk="
echo "Update your .env file:"
echo "  APP_KEY=base64:u1mYXZ2cazpWEqJFgLa+sls9szTXDawLy3/envbBWTk="
echo ""

echo -e "${YELLOW}Step 2: Database Password (PostgreSQL)${NC}"
echo "1. Generate new password:"
NEW_DB_PASS=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-20)
echo "   Suggested: $NEW_DB_PASS"
echo "2. Update PostgreSQL:"
echo "   sudo -u postgres psql"
echo "   ALTER USER pishkhanak PASSWORD '$NEW_DB_PASS';"
echo "3. Update .env:"
echo "   DB_PASSWORD=$NEW_DB_PASS"
echo ""

echo -e "${YELLOW}Step 3: Redis Password${NC}"
echo "Run: ./security-scan/configure-redis-security.sh"
echo ""

echo -e "${YELLOW}Step 4: Payment Gateway Credentials${NC}"
echo -e "${BLUE}Jibit:${NC}"
echo "  1. Login to: https://developer.jibit.ir/"
echo "  2. Navigate to API Keys section"
echo "  3. Regenerate API Key and Secret Key"
echo "  4. Update .env:"
echo "     JIBIT_API_KEY=<new_key>"
echo "     JIBIT_SECRET_KEY=<new_secret>"
echo ""

echo -e "${BLUE}Finnotech:${NC}"
echo "  1. Login to: https://apibeta.finnotech.ir/"
echo "  2. Go to Application Settings"
echo "  3. Generate new Client ID and Secret"
echo "  4. Update .env:"
echo "     FINNOTECH_CLIENT_ID=<new_id>"
echo "     FINNOTECH_CLIENT_SECRET=<new_secret>"
echo ""

echo -e "${YELLOW}Step 5: AI Service API Keys${NC}"
echo -e "${BLUE}OpenAI:${NC}"
echo "  1. Visit: https://platform.openai.com/api-keys"
echo "  2. Delete old key and create new one"
echo "  3. Update .env: OPENAI_API_KEY=<new_key>"
echo ""

echo -e "${BLUE}Google Gemini:${NC}"
echo "  1. Visit: https://makersuite.google.com/app/apikey"
echo "  2. Create new API key"
echo "  3. Update .env: GEMINI_API_KEY=<new_key>"
echo ""

echo -e "${BLUE}OpenRouter:${NC}"
echo "  1. Visit: https://openrouter.ai/keys"
echo "  2. Regenerate API key"
echo "  3. Update .env: OPENROUTER_API_KEY=<new_key>"
echo ""

echo -e "${YELLOW}Step 6: Google OAuth Credentials${NC}"
echo "  1. Visit: https://console.cloud.google.com/apis/credentials"
echo "  2. Select your OAuth 2.0 Client"
echo "  3. Generate new Client Secret"
echo "  4. Update .env:"
echo "     GOOGLE_CLIENT_ID=<keep_same_or_regenerate>"
echo "     GOOGLE_CLIENT_SECRET=<new_secret>"
echo ""

echo -e "${YELLOW}Step 7: Telegram Bot Token${NC}"
echo "  1. Message @BotFather on Telegram"
echo "  2. Send: /revoke"
echo "  3. Select your bot"
echo "  4. Send: /token to get new token"
echo "  5. Update .env: TELEGRAM_BOT_TOKEN=<new_token>"
echo ""

echo "═══════════════════════════════════════════════════════════════"
echo -e "${RED}AFTER ROTATING ALL CREDENTIALS:${NC}"
echo "═══════════════════════════════════════════════════════════════"
echo "1. Clear all caches:"
echo "   php artisan config:clear"
echo "   php artisan cache:clear"
echo ""
echo "2. Test each service to ensure it still works"
echo ""
echo "3. Update any external services that use these credentials"
echo ""
echo "4. Monitor logs for authentication failures:"
echo "   tail -f storage/logs/security.log"
echo ""
echo "5. Consider implementing a secrets management system"
echo "═══════════════════════════════════════════════════════════════"