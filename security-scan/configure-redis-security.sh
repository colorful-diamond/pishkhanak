#!/bin/bash

# Redis Security Configuration Script
# This script helps configure Redis with proper authentication

echo "═══════════════════════════════════════════════════════════"
echo "          Redis Security Configuration Helper"
echo "═══════════════════════════════════════════════════════════"
echo ""

# Function to generate a strong password
generate_password() {
    openssl rand -base64 32 | tr -d "=+/" | cut -c1-25
}

echo "This script will help you secure your Redis installation."
echo ""
echo "STEP 1: Generate a strong Redis password"
echo "----------------------------------------"
REDIS_PASSWORD=$(generate_password)
echo "Generated password: $REDIS_PASSWORD"
echo ""
echo "STEP 2: Update your .env file"
echo "------------------------------"
echo "Add or update the following line in your .env file:"
echo ""
echo "REDIS_PASSWORD=$REDIS_PASSWORD"
echo ""
echo "STEP 3: Configure Redis server"
echo "-------------------------------"
echo "Update your Redis configuration file (usually /etc/redis/redis.conf):"
echo ""
echo "1. Find and uncomment the line: # requirepass foobared"
echo "2. Change it to: requirepass $REDIS_PASSWORD"
echo ""
echo "STEP 4: Restart Redis"
echo "---------------------"
echo "Run one of these commands based on your system:"
echo "  sudo systemctl restart redis"
echo "  sudo service redis-server restart"
echo ""
echo "STEP 5: Test the connection"
echo "---------------------------"
echo "After restarting, test with:"
echo "  redis-cli -a $REDIS_PASSWORD ping"
echo ""
echo "Expected response: PONG"
echo ""
echo "STEP 6: Clear Laravel cache"
echo "---------------------------"
echo "After updating .env, run:"
echo "  php artisan config:clear"
echo "  php artisan cache:clear"
echo ""
echo "═══════════════════════════════════════════════════════════"
echo "IMPORTANT SECURITY NOTES:"
echo "═══════════════════════════════════════════════════════════"
echo "1. Never commit the password to version control"
echo "2. Use different passwords for different environments"
echo "3. Consider using Redis ACL for more granular access control"
echo "4. Enable Redis persistence if not already enabled"
echo "5. Bind Redis to localhost only (bind 127.0.0.1)"
echo "6. Disable dangerous commands (rename-command FLUSHDB \"\")"
echo "═══════════════════════════════════════════════════════════"