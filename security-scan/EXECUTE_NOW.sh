#!/bin/bash
# IMMEDIATE SECURITY ACTIONS - Execute these commands NOW

echo "🔒 Starting Emergency Credential Rotation..."
echo ""

# Step 1: Backup current .env
echo "1. Backing up compromised .env..."
cp .env .env.compromised.$(date +%Y%m%d_%H%M%S)

# Step 2: Apply secure environment
echo "2. Applying secure environment template..."
cp .env.generated .env

# Step 3: Set secure permissions
echo "3. Securing file permissions..."
chmod 600 .env
chmod 600 .env.compromised.*

# Step 4: Show what needs manual action
echo ""
echo "═══════════════════════════════════════════════════════════════"
echo "              MANUAL ACTIONS REQUIRED NOW"
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo "1. Change Database Password:"
echo "   sudo -u postgres psql -c \"ALTER USER postgres PASSWORD '7c66f979c8575a913ae7'\""
echo ""
echo "2. Configure Redis Password:"
echo "   ./security-scan/configure-redis-security.sh"
echo ""
echo "3. Rotate These API Keys Immediately:"
echo "   - Google OAuth: https://console.cloud.google.com"
echo "   - OpenAI: https://platform.openai.com/api-keys"
echo "   - OpenRouter: https://openrouter.ai/keys"
echo "   - Jibit: Login to dashboard"
echo "   - Finnotech: Login to portal"
echo "   - Telegram: Message @BotFather"
echo ""
echo "4. Update .env with new API keys:"
echo "   nano .env"
echo ""
echo "5. Clear caches after updating:"
echo "   php artisan config:cache && php artisan cache:clear"
echo ""
echo "⚠️  Your credentials are EXPOSED until you complete these steps!"