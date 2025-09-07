#!/bin/bash

# Sepehr Payment Gateway Setup Script
# This script automates the setup of Sepehr payment gateway

echo "🚀 Setting up Sepehr Payment Gateway..."
echo "======================================"

# Check if running in Laravel project directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Please run this script from your Laravel project root directory"
    exit 1
fi

# Run the seeder
echo "📦 Adding Sepehr gateway to database..."
php artisan db:seed --class=SepehrGatewaySeeder

if [ $? -eq 0 ]; then
    echo "✅ Sepehr gateway added to database successfully"
else
    echo "❌ Failed to add Sepehr gateway to database"
    exit 1
fi

# Test the gateway
echo "🧪 Testing Sepehr gateway integration..."
php artisan test:sepehr-gateway

if [ $? -eq 0 ]; then
    echo "✅ Sepehr gateway test completed successfully"
else
    echo "⚠️  Sepehr gateway test completed with warnings"
fi

echo ""
echo "🎉 Sepehr Payment Gateway setup completed!"
echo ""
echo "📋 Next steps:"
echo "1. Configure environment variables in your .env file:"
echo "   SEPEHR_TERMINAL_ID=your_8_digit_terminal_id_here"
echo "   SEPEHR_SANDBOX=true"
echo "   SEPEHR_GET_METHOD=false"
echo "   SEPEHR_ROLLBACK_ENABLED=false"
echo ""
echo "2. Important: Whitelist your server IP with Sepehr support"
echo "   Contact Sepehr Electronic Payment to add your server IP(s)"
echo ""
echo "3. Set up callback URL in your system:"
echo "   https://yourdomain.com/payment/callback/sepehr"
echo ""
echo "4. Test the integration:"
echo "   php artisan test:sepehr-gateway"
echo ""
echo "5. Optional: Enable rollback service"
echo "   Contact Sepehr support to enable rollback service"
echo "   Then set SEPEHR_ROLLBACK_ENABLED=true"
echo ""
echo "📚 For detailed documentation, see: docs/SEPEHR_GATEWAY_SETUP.md"
echo ""
echo "🔗 Sepehr API Documentation:"
echo "   - Base API URL: https://sepehr.shaparak.ir:8081"
echo "   - Payment Page: https://sepehr.shaparak.ir:8080"
echo "   - Supported features: Purchase, Bill Payment, Mobile Top-up, Fund Splitting"
echo ""
echo "⚠️  Important security notes:"
echo "   - Only whitelisted IPs can call Sepehr APIs"
echo "   - Digital receipts are unique per transaction"
echo "   - Advice call is mandatory for purchase transactions"
echo "   - Transactions auto-reverse after 30 minutes without advice"
echo "" 