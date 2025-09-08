# 🚨 QUICK CREDENTIAL ROTATION GUIDE

## Step 1: Run Emergency Fix (30 seconds)
```bash
./EMERGENCY_FIX.sh
```

## Step 2: Rotate Each Service (10 minutes total)

### 🔐 Google OAuth (2 min)
1. Open: https://console.cloud.google.com/apis/credentials
2. Find your OAuth 2.0 Client
3. Click trash icon to delete
4. Click "+ CREATE CREDENTIALS" → "OAuth client ID"
5. Copy new Client ID and Secret

### 🤖 OpenAI (2 min)
1. Open: https://platform.openai.com/api-keys
2. Click "Revoke" on ALL keys
3. Click "+ Create new secret key"
4. Set usage limits!
5. Copy new key

### 🌐 OpenRouter (1 min)
1. Open: https://openrouter.ai/keys
2. Delete existing key
3. Create new key
4. Copy it

### 💬 Telegram Bot (2 min)
1. Open Telegram
2. Message @BotFather
3. Send: /mybots
4. Select your bot
5. Click "API Token" → "Revoke"
6. Get new token

### 💳 Payment Gateways (3 min)
**Jibit**: Login → API Settings → Regenerate all keys
**Finnotech**: Login → Developer → New credentials

## Step 3: Update .env (1 minute)
```bash
nano .env
# Add your new credentials
# Save and exit
```

## Step 4: Verify (30 seconds)
```bash
# Test your app
php artisan config:cache
curl https://pishkhanak.com
```

## ⏱️ Total Time: ~12 minutes to full security