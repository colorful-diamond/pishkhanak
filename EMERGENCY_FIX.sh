#!/bin/bash
# EMERGENCY CREDENTIAL PROTECTION - RUN THIS NOW!

echo "🚨 EMERGENCY CREDENTIAL PROTECTION STARTING..."
echo ""

# Step 1: Immediately disable the exposed .env
echo "Step 1: Disabling exposed credentials..."
mv .env .env.COMPROMISED.$(date +%Y%m%d_%H%M%S)

# Step 2: Create minimal safe .env
echo "Step 2: Creating minimal safe environment..."
cat > .env << 'EOF'
APP_NAME="Pishkhanak"
APP_ENV=production
APP_KEY=base64:rogbf+7KrfhhZY/KVx+n7XM12W4ddc5yUBR+zS1+aPk=
APP_DEBUG=false
APP_URL=https://pishkhanak.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pishkhanak
DB_USERNAME=postgres
DB_PASSWORD=CHANGE_ME_IMMEDIATELY

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# ALL API KEYS REMOVED FOR SECURITY
# Add new keys after rotation
EOF

chmod 600 .env

echo ""
echo "✅ Exposed .env has been disabled!"
echo "✅ Minimal safe .env created!"
echo ""
echo "═══════════════════════════════════════════════════════"
echo "         NEXT: ROTATE ALL CREDENTIALS NOW"
echo "═══════════════════════════════════════════════════════"
echo ""
echo "1. DATABASE PASSWORD:"
echo "   sudo -u postgres psql -c \"ALTER USER postgres PASSWORD 'new_secure_password_here'\""
echo "   Then update DB_PASSWORD in .env"
echo ""
echo "2. GOOGLE OAUTH:"
echo "   → https://console.cloud.google.com/apis/credentials"
echo "   → Delete compromised credentials"
echo "   → Create new OAuth 2.0 Client ID"
echo ""
echo "3. OPENAI:"
echo "   → https://platform.openai.com/api-keys"
echo "   → Delete ALL existing keys"
echo "   → Create new key with spending limit"
echo ""
echo "4. OTHER SERVICES:"
echo "   → OpenRouter: https://openrouter.ai/keys"
echo "   → Telegram: Message @BotFather → /revoke"
echo "   → Jibit & Finnotech: Login to dashboards"
echo ""
echo "5. After rotating, add new keys to .env"
echo ""
echo "⚠️  Your app may be down until you add the new credentials!"
echo "⚠️  But this is better than having exposed credentials!"