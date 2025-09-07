#!/bin/bash

# SEP (Saman Electronic Payment) Gateway Setup Script
# This script automates the setup of SEP payment gateway

echo "🚀 Setting up SEP (Saman Electronic Payment) Gateway..."
echo "====================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions for colored output
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Check if running in Laravel project directory
if [ ! -f "artisan" ]; then
    print_error "Please run this script from your Laravel project root directory"
    exit 1
fi

print_info "Detected Laravel project directory"

# Check if composer.json exists and has required dependencies
if [ ! -f "composer.json" ]; then
    print_error "composer.json not found. This doesn't appear to be a valid Laravel project."
    exit 1
fi

# Check for required Laravel version
LARAVEL_VERSION=$(php artisan --version 2>/dev/null | grep -o '[0-9]\+\.[0-9]\+' | head -1)
if [ -z "$LARAVEL_VERSION" ]; then
    print_warning "Could not detect Laravel version. Proceeding anyway..."
else
    print_info "Detected Laravel version: $LARAVEL_VERSION"
fi

echo ""
print_info "Starting SEP Gateway setup process..."
echo ""

# Step 1: Check environment file
print_info "📝 Checking environment configuration..."

if [ ! -f ".env" ]; then
    print_error ".env file not found. Please create .env file first."
    exit 1
fi

# Check if SEP variables already exist
if grep -q "SEP_TERMINAL_ID" .env; then
    print_warning "SEP environment variables already exist in .env file"
else
    print_info "Adding SEP environment variables to .env file..."
    
    # Add SEP configuration to .env
    cat >> .env << 'EOL'

# =====================================================
# SEP (Saman Electronic Payment) Gateway Configuration
# =====================================================

# Terminal ID - 8-digit unique identifier provided by SEP
# Example: 12345678
# Required: YES
# Note: Contact SEP to obtain your terminal ID
SEP_TERMINAL_ID=

# Sandbox Mode - Enable/disable test environment
# Values: true (sandbox), false (production)
# Default: true
# Required: YES
SEP_SANDBOX=true

# Token Expiry - Token validity period in minutes
# Range: 20-3600 minutes (20 minutes to 60 hours)
# Default: 20 minutes
# Required: NO
SEP_TOKEN_EXPIRY=20

# Refund Service - Enable/disable refund functionality
# Values: true (enabled), false (disabled)
# Default: false
# Required: NO
# Note: Contact SEP to activate refund service on your account
SEP_REFUND_ENABLED=false
EOL
    
    print_success "SEP environment variables added to .env file"
fi

# Step 2: Clear config cache
print_info "🔄 Clearing configuration cache..."
php artisan config:clear > /dev/null 2>&1
if [ $? -eq 0 ]; then
    print_success "Configuration cache cleared"
else
    print_warning "Could not clear configuration cache"
fi

# Step 3: Run the seeder
print_info "📦 Adding SEP gateway to database..."
php artisan db:seed --class=SepGatewaySeeder

if [ $? -eq 0 ]; then
    print_success "SEP gateway added to database successfully"
else
    print_error "Failed to add SEP gateway to database"
    exit 1
fi

# Step 4: Test the gateway
print_info "🧪 Testing SEP gateway integration..."
php artisan test:sep-gateway --skip-api

if [ $? -eq 0 ]; then
    print_success "SEP gateway test completed successfully"
else
    print_warning "SEP gateway test completed with warnings (this is normal without proper configuration)"
fi

# Step 5: Cache configuration
print_info "⚡ Caching configuration..."
php artisan config:cache > /dev/null 2>&1
if [ $? -eq 0 ]; then
    print_success "Configuration cached successfully"
else
    print_warning "Could not cache configuration"
fi

echo ""
print_success "🎉 SEP Payment Gateway setup completed!"
echo ""

# Display configuration information
echo "📋 Configuration Summary:"
echo "========================"
echo ""
print_info "Gateway Name: سامان الکترونیک (SEP)"
print_info "Gateway Slug: sep"
print_info "API Version: v4.1"
print_info "Supported Currency: IRT (Iranian Toman/Rial)"
print_info "Default Sandbox Mode: Enabled"
echo ""

# Display next steps
echo "📝 Next Steps:"
echo "=============="
echo ""
echo "1. 🔧 Configure Terminal ID:"
echo "   Edit your .env file and set SEP_TERMINAL_ID to your 8-digit terminal ID"
echo "   Example: SEP_TERMINAL_ID=12345678"
echo ""

echo "2. 🌐 IP Whitelisting (CRITICAL):"
echo "   Contact Saman Electronic Payment support to whitelist your server IP addresses:"
echo "   - Development IP: $(curl -s ipinfo.io/ip 2>/dev/null || echo 'Unable to detect')"
echo "   - Production IPs: (Contact your hosting provider)"
echo ""

echo "3. 📞 Contact SEP Support:"
echo "   - Request terminal ID if you don't have one"
echo "   - Whitelist your server IP addresses"
echo "   - Enable refund service if needed"
echo ""

echo "4. 🧪 Test Configuration:"
echo "   Run: php artisan test:sep-gateway"
echo "   This will validate your configuration and test API connectivity"
echo ""

echo "5. 💳 Test Payment Flow:"
echo "   - Set SEP_TERMINAL_ID in .env"
echo "   - Create a test transaction"
echo "   - Complete payment flow in sandbox mode"
echo ""

echo "6. 🚀 Production Deployment:"
echo "   - Set SEP_SANDBOX=false"
echo "   - Use production terminal ID"
echo "   - Ensure production IPs are whitelisted"
echo ""

# Display important warnings
echo "⚠️  Important Notes:"
echo "==================="
echo ""
print_warning "Without proper terminal ID and IP whitelisting, payments will fail"
print_warning "Always test in sandbox mode before going to production"
print_warning "SEP requires IP whitelisting for security - this is mandatory"
print_warning "Keep your terminal ID secure and never commit it to version control"
echo ""

# Display helpful resources
echo "📚 Resources:"
echo "============"
echo ""
echo "• Documentation: docs/SEP_GATEWAY_SETUP.md"
echo "• Environment Config: SEP_ENVIRONMENT_CONFIG.md" 
echo "• Admin Panel: /access/payment-gateways"
echo "• Test Command: php artisan test:sep-gateway"
echo "• SEP Documentation: https://sep.shaparak.ir"
echo "• SEP Merchant Portal: https://report.sep.ir"
echo ""

# Display configuration validation
echo "🔍 Configuration Validation:"
echo "==========================="
echo ""

# Check if terminal ID is set
TERMINAL_ID=$(grep "SEP_TERMINAL_ID=" .env | cut -d '=' -f2)
if [ -z "$TERMINAL_ID" ] || [ "$TERMINAL_ID" = "" ]; then
    print_warning "SEP_TERMINAL_ID is not configured"
    echo "   → Set this to your 8-digit terminal ID from SEP"
else
    if [[ $TERMINAL_ID =~ ^[0-9]{8}$ ]]; then
        print_success "SEP_TERMINAL_ID format is valid (8 digits)"
    else
        print_warning "SEP_TERMINAL_ID format may be invalid (should be 8 digits)"
    fi
fi

# Check sandbox mode
SANDBOX=$(grep "SEP_SANDBOX=" .env | cut -d '=' -f2)
if [ "$SANDBOX" = "true" ]; then
    print_info "Sandbox mode is enabled (good for testing)"
elif [ "$SANDBOX" = "false" ]; then
    print_warning "Production mode is enabled (ensure you're ready for live payments)"
else
    print_warning "SEP_SANDBOX value should be 'true' or 'false'"
fi

# Check token expiry
TOKEN_EXPIRY=$(grep "SEP_TOKEN_EXPIRY=" .env | cut -d '=' -f2)
if [ ! -z "$TOKEN_EXPIRY" ] && [ "$TOKEN_EXPIRY" != "" ]; then
    if [ "$TOKEN_EXPIRY" -ge 20 ] && [ "$TOKEN_EXPIRY" -le 3600 ]; then
        print_info "Token expiry is set to $TOKEN_EXPIRY minutes (valid range)"
    else
        print_warning "Token expiry should be between 20 and 3600 minutes"
    fi
fi

echo ""

# Final status
if [ -z "$TERMINAL_ID" ] || [ "$TERMINAL_ID" = "" ]; then
    print_warning "⚠️  SEP Gateway is installed but requires configuration"
    echo "   Configure SEP_TERMINAL_ID and contact SEP for IP whitelisting"
else
    print_success "✅ SEP Gateway is ready for testing!"
    echo "   Run 'php artisan test:sep-gateway' to validate your setup"
fi

echo ""
print_info "Setup completed at $(date)"
print_info "For support, check the documentation or run: php artisan test:sep-gateway --verbose"

# Make script executable reminder
echo ""
print_info "💡 Tip: Make this script executable with: chmod +x setup-sep-gateway.sh" 