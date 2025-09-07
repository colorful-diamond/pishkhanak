#!/bin/bash

# Redirect System Status Checker
# Run this on the server to check deployment status

echo "🔍 REDIRECT SYSTEM STATUS CHECK"
echo "================================="

# Check if we're on the server
if [[ $HOSTNAME == "makna" ]]; then
    echo "✅ Running on server: $HOSTNAME"
    cd /home/pishkhanak/htdocs/pishkhanak.com
else
    echo "❌ Not running on server (hostname: $HOSTNAME)"
    echo "💡 Run this script on the makna server"
    exit 1
fi

echo ""
echo "📁 FILE STATUS:"
echo "---------------"

# Check Model
if [ -f "app/Models/Redirect.php" ]; then
    echo "✅ Redirect Model: Found"
else
    echo "❌ Redirect Model: Missing"
fi

# Check Migration
if ls database/migrations/*create_redirects_table.php 1> /dev/null 2>&1; then
    echo "✅ Redirect Migration: Found"
else
    echo "❌ Redirect Migration: Missing"
fi

# Check Seeder
if [ -f "database/seeders/RedirectSeeder.php" ]; then
    echo "✅ Redirect Seeder: Found"
else
    echo "❌ Redirect Seeder: Missing"
fi

# Check Middleware
if [ -f "app/Http/Middleware/RedirectMiddleware.php" ]; then
    echo "✅ Redirect Middleware: Found"
else
    echo "❌ Redirect Middleware: Missing"
fi

# Check Resource (should be disabled currently)
if [ -f "app/Filament/Resources/RedirectResource.php" ]; then
    echo "⚠️  RedirectResource: ACTIVE (may cause errors)"
elif [ -f "app/Filament/Resources/RedirectResource.php.disabled" ]; then
    echo "✅ RedirectResource: Safely disabled"
else
    echo "❌ RedirectResource: Not found"
fi

# Check Resource Directory
if [ -d "app/Filament/Resources/RedirectResource" ]; then
    echo "⚠️  RedirectResource Directory: ACTIVE (may cause errors)"
elif [ -d "app/Filament/Resources/RedirectResource.disabled" ]; then
    echo "✅ RedirectResource Directory: Safely disabled"
else
    echo "❌ RedirectResource Directory: Not found"
fi

echo ""
echo "🗄️  DATABASE STATUS:"
echo "--------------------"

# Check if table exists
if php artisan tinker --execute="echo Schema::hasTable('redirects') ? 'EXISTS' : 'MISSING';" 2>/dev/null | grep -q "EXISTS"; then
    echo "✅ Redirects Table: Exists"
    RECORD_COUNT=$(php artisan tinker --execute="echo App\\Models\\Redirect::count();" 2>/dev/null | tail -1)
    echo "📊 Records Count: $RECORD_COUNT"
else
    echo "❌ Redirects Table: Missing"
fi

echo ""
echo "🔧 COMMANDS STATUS:"
echo "-------------------"

# Check console commands
for cmd in "RedirectCacheWarm" "RedirectCacheClear" "RedirectCacheStats" "RedirectScheduledCacheWarm"; do
    if [ -f "app/Console/Commands/$cmd.php" ]; then
        echo "✅ $cmd: Found"
    else
        echo "❌ $cmd: Missing"
    fi
done

echo ""
echo "📋 DEPLOYMENT RECOMMENDATION:"
echo "----------------------------"

# Count missing files
MISSING_COUNT=0

[ ! -f "app/Models/Redirect.php" ] && ((MISSING_COUNT++))
[ ! -f "database/seeders/RedirectSeeder.php" ] && ((MISSING_COUNT++))
[ ! -f "app/Http/Middleware/RedirectMiddleware.php" ] && ((MISSING_COUNT++))
[ ! -d "database/migrations" ] || [ -z "$(ls database/migrations/*create_redirects_table.php 2>/dev/null)" ] && ((MISSING_COUNT++))

if [ $MISSING_COUNT -eq 0 ]; then
    if [ -f "app/Filament/Resources/RedirectResource.php.disabled" ]; then
        echo "🚀 READY TO ACTIVATE!"
        echo "   Run: mv app/Filament/Resources/RedirectResource.php.disabled app/Filament/Resources/RedirectResource.php"
        echo "   Run: mv app/Filament/Resources/RedirectResource.disabled app/Filament/Resources/RedirectResource"
        echo "   Run: php artisan migrate --force"
        echo "   Run: php artisan db:seed --class=RedirectSeeder"
    else
        echo "✅ FULLY DEPLOYED!"
    fi
elif [ $MISSING_COUNT -lt 3 ]; then
    echo "⚠️  PARTIALLY DEPLOYED - Upload missing files"
else
    echo "❌ NOT DEPLOYED - Upload all system files"
fi

echo ""
echo "🔄 CURRENT ERROR STATUS:"
echo "-----------------------"

# Check recent logs for redirect errors
if tail -20 storage/logs/laravel.log 2>/dev/null | grep -q "RedirectResource"; then
    echo "❌ Recent redirect errors found in logs"
else
    echo "✅ No recent redirect errors in logs"
fi

echo ""
echo "📞 QUICK COMMANDS:"
echo "-----------------"
echo "• Check logs: tail -f storage/logs/laravel.log"
echo "• Clear cache: php artisan cache:clear && php artisan config:clear"
echo "• Check routes: php artisan route:list | grep redirect"
echo "• Test migration: php artisan migrate:status"

echo ""
echo "✨ Check complete!"