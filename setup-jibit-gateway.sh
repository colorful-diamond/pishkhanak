#!/bin/bash

# Jibit Payment Gateway Setup Script
# This script automates the setup of Jibit payment gateway

echo "🚀 Setting up Jibit Payment Gateway..."
echo "======================================"

# Check if running in Laravel project directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Please run this script from your Laravel project root directory"
    exit 1
fi

# Run the seeder
echo "📦 Adding Jibit gateway to database..."
php artisan db:seed --class=JibitGatewaySeeder

if [ $? -eq 0 ]; then
    echo "✅ Jibit gateway added to database successfully"
else
    echo "❌ Failed to add Jibit gateway to database"
    exit 1
fi

# Test the gateway
echo "🧪 Testing Jibit gateway integration..."
php artisan test:jibit-gateway

if [ $? -eq 0 ]; then
    echo "✅ Jibit gateway test completed successfully"
else
    echo "⚠️  Jibit gateway test completed with warnings"
fi

echo ""
echo "🎉 Jibit Payment Gateway setup completed!"
echo ""
echo "📋 Next steps:"
echo "1. Configure environment variables in your .env file:"
echo "   JIBIT_ACCESS_TOKEN=your_access_token_here"
echo "   JIBIT_WEBHOOK_SECRET=your_webhook_secret_here"
echo "   JIBIT_SANDBOX=true"
echo ""
echo "2. Set up webhook URL in Jibit dashboard:"
echo "   https://yourdomain.com/payment/callback/jibit"
echo ""
echo "3. Test the integration:"
echo "   php artisan test:jibit-gateway"
echo ""
echo "📚 For detailed documentation, see: docs/JIBIT_GATEWAY_SETUP.md"
echo "" 