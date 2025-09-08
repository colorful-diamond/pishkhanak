#!/bin/bash

# Security Monitoring Script
# Run this regularly to check security status

echo "═══════════════════════════════════════════════════════════"
echo "             Security Status Monitor"
echo "═══════════════════════════════════════════════════════════"
echo ""

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check .env file permissions
echo "Checking file permissions..."
ENV_PERMS=$(stat -c %a .env 2>/dev/null)
if [ "$ENV_PERMS" == "600" ]; then
    echo -e "${GREEN}✓${NC} .env file properly secured (600)"
else
    echo -e "${RED}✗${NC} .env file has insecure permissions: $ENV_PERMS (should be 600)"
fi

# Check if debug mode is disabled
echo ""
echo "Checking debug mode..."
if grep -q "APP_DEBUG=false" .env; then
    echo -e "${GREEN}✓${NC} Debug mode disabled"
else
    echo -e "${RED}✗${NC} Debug mode may be enabled!"
fi

# Check if production environment
if grep -q "APP_ENV=production" .env; then
    echo -e "${GREEN}✓${NC} Production environment set"
else
    echo -e "${YELLOW}⚠${NC} Not in production environment"
fi

# Check Redis connectivity
echo ""
echo "Checking Redis security..."
if redis-cli ping 2>&1 | grep -q "NOAUTH"; then
    echo -e "${GREEN}✓${NC} Redis requires authentication"
elif redis-cli ping 2>&1 | grep -q "PONG"; then
    echo -e "${RED}✗${NC} Redis accessible without password!"
else
    echo -e "${YELLOW}⚠${NC} Redis may be down or misconfigured"
fi

# Check for recent security events
echo ""
echo "Checking security logs..."
if [ -f storage/logs/security.log ]; then
    RECENT_EVENTS=$(tail -100 storage/logs/security.log | grep -c "CRITICAL\|WARNING" 2>/dev/null || echo "0")
    if [ "$RECENT_EVENTS" -gt 0 ]; then
        echo -e "${YELLOW}⚠${NC} Found $RECENT_EVENTS warning/critical events in recent logs"
        echo "  Recent critical events:"
        tail -100 storage/logs/security.log | grep "CRITICAL" | tail -5
    else
        echo -e "${GREEN}✓${NC} No critical security events in recent logs"
    fi
else
    echo -e "${YELLOW}⚠${NC} Security log file not found"
fi

# Check for failed login attempts
echo ""
echo "Checking authentication failures..."
if [ -f storage/logs/laravel.log ]; then
    FAILED_LOGINS=$(tail -500 storage/logs/laravel.log | grep -c "authentication failed\|login failed\|401\|429" 2>/dev/null || echo "0")
    if [ "$FAILED_LOGINS" -gt 10 ]; then
        echo -e "${RED}✗${NC} High number of failed login attempts: $FAILED_LOGINS"
    elif [ "$FAILED_LOGINS" -gt 0 ]; then
        echo -e "${YELLOW}⚠${NC} Some failed login attempts: $FAILED_LOGINS"
    else
        echo -e "${GREEN}✓${NC} No recent authentication failures"
    fi
fi

# Check storage directory permissions
echo ""
echo "Checking storage permissions..."
STORAGE_PERMS=$(find storage -type d -perm -o+w 2>/dev/null | wc -l)
if [ "$STORAGE_PERMS" -gt 0 ]; then
    echo -e "${RED}✗${NC} Found $STORAGE_PERMS world-writable directories in storage/"
else
    echo -e "${GREEN}✓${NC} Storage directory permissions look secure"
fi

# Check for exposed keys in code
echo ""
echo "Checking for exposed credentials..."
EXPOSED_KEYS=$(grep -r "sk-\|AIza\|key.*=.*['\"]" app/ --include="*.php" 2>/dev/null | grep -v "env(" | wc -l)
if [ "$EXPOSED_KEYS" -gt 0 ]; then
    echo -e "${RED}✗${NC} Found $EXPOSED_KEYS potential hardcoded credentials in app/"
else
    echo -e "${GREEN}✓${NC} No hardcoded credentials found in application code"
fi

# Summary
echo ""
echo "═══════════════════════════════════════════════════════════"
echo "Security Scan Complete"
echo ""
echo "Run full security audit with: ./security-scan/state.json"
echo "Fix permissions with: sudo ./security-scan/fix-permissions.sh"
echo "Rotate credentials with: ./security-scan/rotate-all-credentials.sh"
echo "═══════════════════════════════════════════════════════════"