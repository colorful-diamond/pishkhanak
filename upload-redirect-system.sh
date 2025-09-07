#!/bin/bash

# Upload Redirect System Files to Server
echo "🚀 UPLOADING REDIRECT SYSTEM TO SERVER"
echo "======================================"

SERVER="pishkhanak@109.206.254.170"
PASSWORD="UYzHsGYgMN7tnOdUPuOg"
REMOTE_DIR="/home/pishkhanak/htdocs/pishkhanak.com"

echo "📁 Uploading core files..."

# Upload Model
echo "  → Uploading Redirect Model..."
sshpass -p "$PASSWORD" scp -P 22 app/Models/Redirect.php $SERVER:$REMOTE_DIR/app/Models/

# Upload Migration
echo "  → Uploading Migration..."
sshpass -p "$PASSWORD" scp -P 22 database/migrations/2025_01_27_000000_create_redirects_table.php $SERVER:$REMOTE_DIR/database/migrations/

# Upload Seeder
echo "  → Uploading Seeder..."
sshpass -p "$PASSWORD" scp -P 22 database/seeders/RedirectSeeder.php $SERVER:$REMOTE_DIR/database/seeders/

# Upload Middleware
echo "  → Uploading Middleware..."
sshpass -p "$PASSWORD" scp -P 22 app/Http/Middleware/RedirectMiddleware.php $SERVER:$REMOTE_DIR/app/Http/Middleware/

# Upload Console Commands
echo "  → Uploading Console Commands..."
sshpass -p "$PASSWORD" scp -P 22 app/Console/Commands/RedirectCacheWarm.php $SERVER:$REMOTE_DIR/app/Console/Commands/
sshpass -p "$PASSWORD" scp -P 22 app/Console/Commands/RedirectCacheClear.php $SERVER:$REMOTE_DIR/app/Console/Commands/
sshpass -p "$PASSWORD" scp -P 22 app/Console/Commands/RedirectCacheStats.php $SERVER:$REMOTE_DIR/app/Console/Commands/
sshpass -p "$PASSWORD" scp -P 22 app/Console/Commands/RedirectScheduledCacheWarm.php $SERVER:$REMOTE_DIR/app/Console/Commands/

# Upload Widget (optional)
echo "  → Uploading Cache Stats Widget..."
sshpass -p "$PASSWORD" scp -P 22 app/Filament/Widgets/RedirectCacheStatsWidget.php $SERVER:$REMOTE_DIR/app/Filament/Widgets/

# Upload updated config files
echo "  → Uploading updated bootstrap/app.php..."
sshpass -p "$PASSWORD" scp -P 22 bootstrap/app.php $SERVER:$REMOTE_DIR/bootstrap/

echo "  → Uploading updated DatabaseSeeder.php..."
sshpass -p "$PASSWORD" scp -P 22 database/seeders/DatabaseSeeder.php $SERVER:$REMOTE_DIR/database/seeders/

echo ""
echo "✅ All files uploaded successfully!"
echo ""
echo "🔧 Now run the activation commands on the server..."
echo "   Use: bash activate-redirect-system.sh"