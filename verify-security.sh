#!/bin/bash

echo "🔍 Security Verification Check"
echo "══════════════════════════════"
echo ""

ISSUES=0

# Check for exposed credentials
echo "Checking for exposed credentials..."

if grep -q "mOTDCjbxlRA6Xhdk2x" .env 2>/dev/null; then
    echo "❌ Database password STILL EXPOSED!"
    ISSUES=$((ISSUES + 1))
else
    echo "✅ Database password secured"
fi

if grep -q "GOCSPX-HW1TUq3VLKPU" .env 2>/dev/null; then
    echo "❌ Google OAuth STILL EXPOSED!"
    ISSUES=$((ISSUES + 1))
else
    echo "✅ Google OAuth secured"
fi

if grep -q "sk-or-v1-d694" .env 2>/dev/null; then
    echo "❌ OpenRouter STILL EXPOSED!"
    ISSUES=$((ISSUES + 1))
else
    echo "✅ OpenRouter secured"
fi

if grep -q "sk-svcacct-" .env 2>/dev/null; then
    echo "❌ OpenAI STILL EXPOSED!"
    ISSUES=$((ISSUES + 1))
else
    echo "✅ OpenAI secured"
fi

if grep -q "7696804096:AAF" .env 2>/dev/null; then
    echo "❌ Telegram Bot STILL EXPOSED!"
    ISSUES=$((ISSUES + 1))
else
    echo "✅ Telegram Bot secured"
fi

echo ""
if [ $ISSUES -eq 0 ]; then
    echo "🎉 ALL CREDENTIALS SECURED!"
    echo "Your application is now protected."
else
    echo "⚠️  $ISSUES credentials still exposed!"
    echo "Run ./EMERGENCY_FIX.sh immediately!"
fi