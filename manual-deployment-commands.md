# Manual Deployment Commands

## Step 1: Upload Files
```bash
# Upload Model
sshpass -p "UYzHsGYgMN7tnOdUPuOg" scp -P 22 app/Models/Redirect.php pishkhanak@109.206.254.170:/home/pishkhanak/htdocs/pishkhanak.com/app/Models/

# Upload Migration
sshpass -p "UYzHsGYgMN7tnOdUPuOg" scp -P 22 database/migrations/2025_01_27_000000_create_redirects_table.php pishkhanak@109.206.254.170:/home/pishkhanak/htdocs/pishkhanak.com/database/migrations/

# Upload Seeder
sshpass -p "UYzHsGYgMN7tnOdUPuOg" scp -P 22 database/seeders/RedirectSeeder.php pishkhanak@109.206.254.170:/home/pishkhanak/htdocs/pishkhanak.com/database/seeders/

# Upload Middleware
sshpass -p "UYzHsGYgMN7tnOdUPuOg" scp -P 22 app/Http/Middleware/RedirectMiddleware.php pishkhanak@109.206.254.170:/home/pishkhanak/htdocs/pishkhanak.com/app/Http/Middleware/

# Upload Console Commands
sshpass -p "UYzHsGYgMN7tnOdUPuOg" scp -P 22 app/Console/Commands/RedirectCacheWarm.php pishkhanak@109.206.254.170:/home/pishkhanak/htdocs/pishkhanak.com/app/Console/Commands/
sshpass -p "UYzHsGYgMN7tnOdUPuOg" scp -P 22 app/Console/Commands/RedirectCacheClear.php pishkhanak@109.206.254.170:/home/pishkhanak/htdocs/pishkhanak.com/app/Console/Commands/
sshpass -p "UYzHsGYgMN7tnOdUPuOg" scp -P 22 app/Console/Commands/RedirectCacheStats.php pishkhanak@109.206.254.170:/home/pishkhanak/htdocs/pishkhanak.com/app/Console/Commands/
sshpass -p "UYzHsGYgMN7tnOdUPuOg" scp -P 22 app/Console/Commands/RedirectScheduledCacheWarm.php pishkhanak@109.206.254.170:/home/pishkhanak/htdocs/pishkhanak.com/app/Console/Commands/

# Upload Updated Config
sshpass -p "UYzHsGYgMN7tnOdUPuOg" scp -P 22 bootstrap/app.php pishkhanak@109.206.254.170:/home/pishkhanak/htdocs/pishkhanak.com/bootstrap/
sshpass -p "UYzHsGYgMN7tnOdUPuOg" scp -P 22 database/seeders/DatabaseSeeder.php pishkhanak@109.206.254.170:/home/pishkhanak/htdocs/pishkhanak.com/database/seeders/
```

## Step 2: Connect to Server and Activate
```bash
# SSH to server
sshpass -p "UYzHsGYgMN7tnOdUPuOg" ssh -o StrictHostKeyChecking=no -p 22 pishkhanak@109.206.254.170

# Navigate to project
cd /home/pishkhanak/htdocs/pishkhanak.com

# Re-enable RedirectResource
mv app/Filament/Resources/RedirectResource.php.disabled app/Filament/Resources/RedirectResource.php
mv app/Filament/Resources/RedirectResource.disabled app/Filament/Resources/RedirectResource

# Run migration
php artisan migrate --force

# Run seeder (THIS IS WHAT YOU ASKED FOR!)
php artisan db:seed --class=RedirectSeeder

# Warm cache
php artisan redirects:cache-warm

# Clear caches
php artisan cache:clear && php artisan config:clear && php artisan route:clear

# Check status
php artisan redirects:cache-stats
```