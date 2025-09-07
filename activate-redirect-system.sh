#!/bin/bash

# Activate Redirect System on Server
echo "🔧 ACTIVATING REDIRECT SYSTEM ON SERVER"
echo "======================================="

# Check if we're on the correct server
if [[ $HOSTNAME != "makna" ]]; then
    echo "❌ This script must be run ON THE SERVER (makna)"
    echo "💡 Upload this file and run it via SSH"
    exit 1
fi

# Navigate to project directory
cd /home/pishkhanak/htdocs/pishkhanak.com

echo "📍 Current directory: $(pwd)"
echo ""

echo "🔄 Step 1: Re-enabling RedirectResource..."
if [ -f "app/Filament/Resources/RedirectResource.php.disabled" ]; then
    mv app/Filament/Resources/RedirectResource.php.disabled app/Filament/Resources/RedirectResource.php
    echo "✅ RedirectResource.php enabled"
else
    echo "⚠️  RedirectResource.php already enabled or not found"
fi

if [ -d "app/Filament/Resources/RedirectResource.disabled" ]; then
    mv app/Filament/Resources/RedirectResource.disabled app/Filament/Resources/RedirectResource
    echo "✅ RedirectResource directory enabled"
else
    echo "⚠️  RedirectResource directory already enabled or not found"
fi

echo ""
echo "🗄️  Step 2: Running migration..."
php artisan migrate --force
echo ""

echo "🌱 Step 3: Running seeder..."
php artisan db:seed --class=RedirectSeeder
echo ""

echo "🔥 Step 4: Warming redirect cache..."
php artisan redirects:cache-warm
echo ""

echo "🧹 Step 5: Clearing application caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
echo ""

echo "📊 Step 6: Checking system status..."
echo "  → Testing redirect commands..."
php artisan redirects:cache-stats
echo ""

echo "🧪 Step 7: Testing routes..."
if php artisan route:list | grep -q "redirects"; then
    echo "✅ Redirect routes registered successfully"
else
    echo "⚠️  Redirect routes not found - check for errors"
fi

echo ""
echo "🎯 ACTIVATION COMPLETE!"
echo "======================"
echo ""
echo "✅ Redirect Management System is now ACTIVE!"
echo ""
echo "📋 What you can do now:"
echo "  • Visit: https://pishkhanak.com/access"
echo "  • Navigate to: مدیریت محتوا > مدیریت تغییر مسیر"
echo "  • Manage redirects through admin panel"
echo ""
echo "🧪 Test your existing redirects:"
echo "  • /services/credit-scoring → /services/credit-score-rating"
echo "  • /services/card-to-sheba → /services/card-iban"
echo "  • /services/traffic-fines → /services/car-violation-inquiry"
echo ""
echo "📊 Monitor performance:"
echo "  • php artisan redirects:cache-stats"
echo "  • tail -f storage/logs/laravel.log"
echo ""
echo "🎉 Enjoy your new redirect management system!"