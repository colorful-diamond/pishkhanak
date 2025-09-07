#!/bin/bash

# NICS24 Provider Test Shell Script
# For Linux/Mac users to easily run the test

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo ""
echo "======================================"
echo "   NICS24 Provider Test Launcher"
echo "======================================"
echo ""

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP is not installed or not in PATH${NC}"
    echo -e "${YELLOW}💡 Please install PHP first${NC}"
    exit 1
fi

echo -e "${GREEN}✅ PHP is available${NC}"
echo ""

show_menu() {
    echo "Select test type:"
    echo "1. Quick connectivity test"
    echo "2. Full test with OTP flow"
    echo "3. Debug mode test"
    echo "4. Custom test (enter parameters)"
    echo "5. Show usage help"
    echo "6. Exit"
    echo ""
}

quick_test() {
    echo ""
    echo -e "${BLUE}🔍 Running Quick Connectivity Test...${NC}"
    echo "===================================="
    php quick-test-nics24.php
}

full_test() {
    echo ""
    echo -e "${BLUE}🚀 Running Full Test with OTP Flow...${NC}"
    echo "===================================="
    echo ""
    
    read -p "Enter mobile number (or press Enter for default 09123456789): " mobile
    read -p "Enter national code (or press Enter for default 1234567890): " national_code
    
    mobile=${mobile:-09123456789}
    national_code=${national_code:-1234567890}
    
    echo ""
    echo "Running test with:"
    echo "Mobile: $mobile"
    echo "National Code: $national_code"
    echo ""
    
    php test-nics24-provider.php --mobile="$mobile" --national_code="$national_code"
}

debug_test() {
    echo ""
    echo -e "${BLUE}🐛 Running Debug Mode Test...${NC}"
    echo "============================="
    php test-nics24-provider.php --debug
}

custom_test() {
    echo ""
    echo -e "${BLUE}⚙️ Custom Test Configuration${NC}"
    echo "============================"
    
    read -p "Mobile number (default: 09123456789): " mobile
    read -p "National code (default: 1234567890): " national_code
    read -p "Provider (default: nics24): " provider
    read -p "Timeout in seconds (default: 300): " timeout
    
    mobile=${mobile:-09123456789}
    national_code=${national_code:-1234567890}
    provider=${provider:-nics24}
    timeout=${timeout:-300}
    
    echo ""
    echo "Running custom test with:"
    echo "Mobile: $mobile"
    echo "National Code: $national_code"
    echo "Provider: $provider"
    echo "Timeout: $timeout seconds"
    echo ""
    
    php test-nics24-provider.php --mobile="$mobile" --national_code="$national_code" --provider="$provider" --timeout="$timeout" --debug
}

show_help() {
    echo ""
    echo -e "${BLUE}📖 NICS24 Test Help${NC}"
    echo "==================="
    echo ""
    echo "Available test scripts:"
    echo ""
    echo "1. quick-test-nics24.php"
    echo "   - Quick connectivity and configuration check"
    echo "   - No OTP required"
    echo "   - Usage: php quick-test-nics24.php"
    echo ""
    echo "2. test-nics24-provider.php"
    echo "   - Full credit score test with OTP flow"
    echo "   - Supports various options"
    echo "   - Usage: php test-nics24-provider.php [options]"
    echo ""
    echo "Available options:"
    echo "  --mobile=09123456789       Mobile number for OTP"
    echo "  --national_code=1234567890 National code"
    echo "  --provider=nics24          Provider name"
    echo "  --timeout=300              Timeout in seconds"
    echo "  --debug                    Enable debug output"
    echo ""
    echo "Prerequisites:"
    echo "  - Node.js server running on port 9999"
    echo "  - Redis server running"
    echo "  - NICS24 credentials configured in config.js"
    echo "  - Captcha API running on localhost:9090"
    echo ""
    echo "Examples:"
    echo "  php test-nics24-provider.php"
    echo "  php test-nics24-provider.php --debug"
    echo "  php test-nics24-provider.php --mobile=09121234567 --debug"
    echo ""
    read -p "Press Enter to continue..."
}

# Main loop
while true; do
    show_menu
    read -p "Enter your choice (1-6): " choice
    
    case $choice in
        1)
            quick_test
            ;;
        2)
            full_test
            ;;
        3)
            debug_test
            ;;
        4)
            custom_test
            ;;
        5)
            show_help
            continue
            ;;
        6)
            echo ""
            echo -e "${GREEN}👋 Goodbye!${NC}"
            exit 0
            ;;
        *)
            echo -e "${RED}Invalid choice. Please try again.${NC}"
            echo ""
            continue
            ;;
    esac
    
    echo ""
    echo "Test completed. Check the log files for detailed information."
    echo ""
    
    read -p "Run another test? (y/n): " restart
    case $restart in
        [Yy]* ) continue;;
        [Nn]* ) break;;
        * ) break;;
    esac
done

echo ""
echo -e "${GREEN}👋 Goodbye!${NC}"