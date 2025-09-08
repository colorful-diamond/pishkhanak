#!/bin/bash

# Security Scan Status Checker
# This script provides a quick overview of the security scan findings

SCAN_DIR="$(dirname "$0")"
STATE_FILE="$SCAN_DIR/state.json"

if [ ! -f "$STATE_FILE" ]; then
    echo "❌ No security scan found. Run security scan first."
    exit 1
fi

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔒 SECURITY SCAN STATUS"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Parse JSON using simple grep/awk (works without jq)
TOTAL=$(grep '"total_vulnerabilities"' "$STATE_FILE" | awk -F': ' '{print $2}' | tr -d ',')
CRITICAL=$(grep '"critical"' "$STATE_FILE" | head -1 | awk -F': ' '{print $2}' | tr -d ',')
HIGH=$(grep '"high"' "$STATE_FILE" | head -1 | awk -F': ' '{print $2}' | tr -d ',')
MEDIUM=$(grep '"medium"' "$STATE_FILE" | head -1 | awk -F': ' '{print $2}' | tr -d ',')
FIXED=$(grep '"total_fixed"' "$STATE_FILE" | awk -F': ' '{print $2}' | tr -d ',')

echo ""
echo "📊 Vulnerability Summary:"
echo "  Total Found: $TOTAL"
echo "  🔴 Critical: $CRITICAL"
echo "  🟡 High: $HIGH"  
echo "  🟢 Medium: $MEDIUM"
echo ""
echo "✅ Fixed: $FIXED / $TOTAL"
echo ""

if [ "$CRITICAL" -gt "0" ]; then
    echo "⚠️  WARNING: Critical vulnerabilities require immediate action!"
    echo ""
    echo "🚨 CRITICAL ISSUES:"
    echo "  • Exposed database password"
    echo "  • API keys visible in .env"
    echo "  • Payment gateway credentials exposed"
    echo "  • Telegram bot token exposed"
    echo ""
    echo "📋 Next Steps:"
    echo "  1. Rotate ALL exposed credentials immediately"
    echo "  2. Review security-scan/plan.md for full details"
    echo "  3. Do NOT deploy until critical issues are fixed"
else
    echo "✅ No critical vulnerabilities remaining"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📄 Full report: security-scan/plan.md"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"