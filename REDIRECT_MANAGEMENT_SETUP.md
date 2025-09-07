# Redirect Management System Setup

## Overview
This system provides a complete redirect management solution for your Laravel application with Filament admin interface.

## Installation Steps

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Seed Initial Redirects
```bash
php artisan db:seed --class=RedirectSeeder
```

Or run all seeders:
```bash
php artisan db:seed
```

### 3. Warm the Cache
```bash
php artisan redirects:cache-warm
```

### 4. Clear Application Cache (if needed)
```bash
php artisan cache:clear
php artisan config:clear
```

### 5. Schedule Cache Warming (Optional)
Add to your `routes/console.php` or cron:
```php
// Auto-warm cache every hour
Schedule::command('redirects:scheduled-cache-warm')->hourly();
```

## Features

### Admin Panel Management
- Navigate to **مدیریت محتوا > مدیریت تغییر مسیر** in your Filament admin
- Add, edit, delete redirects
- View redirect statistics (hit count, last used)
- Enable/disable redirects
- Support for exact and wildcard matching

### Cache Management in Admin Panel
- **آمار کش**: View cache statistics
- **گرم کردن کش**: Warm up all redirect caches
- **پاک کردن کل کش**: Clear all redirect caches
- **پاک کردن کش** (per redirect): Clear cache for individual redirects

### Redirect Types
- **301 Permanent Redirect**: Best for SEO, tells search engines the page has moved permanently
- **302 Temporary Redirect**: For temporary moves
- **303, 307, 308**: Other redirect types as needed

### Wildcard Support
- **Exact Match**: `/old-url` only matches exactly `/old-url`
- **Pattern Match**: `/old-path/*` can match `/old-path/anything`

### Enhanced Caching System

### Multi-Level Caching
1. **Per-URL Caching**: Each URL lookup is cached individually (2 hours for redirects, 30 min for no-redirects)
2. **Active Redirects Cache**: All active redirects cached together (1 hour)
3. **Negative Caching**: URLs with no redirects are cached to prevent repeated database queries

### Performance Features
- **Zero Database Queries**: After cache warm-up, no database queries for redirect lookups
- **Smart Cache Invalidation**: Only relevant caches are cleared when redirects change
- **Cache Warming**: Automatic preloading of popular redirects
- **Performance Monitoring**: Redirect lookup time logging

### Cache Management Commands
```bash
# Warm cache for all redirects
php artisan redirects:cache-warm

# Warm only popular redirects
php artisan redirects:cache-warm --popular

# Warm specific URLs
php artisan redirects:cache-warm --urls="/old-url" --urls="/another-url"

# Clear all redirect cache
php artisan redirects:cache-clear

# Clear cache for specific URL
php artisan redirects:cache-clear --url="/specific-url"

# View cache statistics
php artisan redirects:cache-stats
```

## Current Redirects
The following redirects from your .htaccess have been migrated:

1. `/services/credit-scoring` → `/services/credit-score-rating` (301)
2. `/services/card-to-sheba` → `/services/card-iban` (301)  
3. `/services/traffic-fines` → `/services/car-violation-inquiry` (301)

## Usage Examples

### Adding a New Redirect
1. Go to Filament admin
2. Navigate to **مدیریت تغییر مسیر**
3. Click **تغییر مسیر جدید**
4. Fill in:
   - **URL مبدأ**: `/old-page`
   - **URL مقصد**: `/new-page` or `https://external-site.com`
   - **کد وضعیت**: `301` for permanent, `302` for temporary
   - **تطابق دقیق**: Enable for exact URL matching
   - **فعال**: Enable to activate the redirect

### Wildcard Redirects
For pattern matching, disable **تطابق دقیق** and use patterns like:
- `/old-section/*` → `/new-section/*`
- `/blog/*` → `https://blog.example.com/*`

### External Redirects
You can redirect to external URLs:
- `/old-page` → `https://example.com/new-page`

## Monitoring
- View hit counts and last usage dates in the admin
- Filter by popular redirects, unused redirects, etc.
- All redirects are logged for debugging

## Security Notes
- Middleware skips admin panel routes to prevent lockouts
- Only GET requests are redirected (POST/PUT/DELETE ignored)
- Asset files (CSS, JS, images) are automatically skipped

## .htaccess Integration
You can now remove the manual redirects from your `.htaccess` file:
```apache
# These can be removed - now managed via admin panel
# Redirect 301 /services/credit-scoring /services/credit-score-rating
# Redirect 301 /services/card-to-sheba /services/card-iban
# Redirect 301 /services/traffic-fines /services/car-violation-inquiry
```

## Troubleshooting

### Redirects Not Working
1. Check if redirect is active in admin panel
2. Verify middleware is registered in `bootstrap/app.php`
3. Clear application cache: `php artisan cache:clear`
4. Warm redirect cache: `php artisan redirects:cache-warm`

### Performance Issues
- **Slow redirects**: Run `php artisan redirects:cache-warm`
- **High database load**: Check cache stats with `php artisan redirects:cache-stats`
- **Cache not working**: Verify your `CACHE_DRIVER` is set to `redis` or `memcached`

### Cache Issues
- **Cache not clearing**: Check your cache driver configuration
- **Memory usage**: Monitor cache key count with cache stats
- **Stale redirects**: Clear specific URL cache or all cache

### Infinite Loops
- The system prevents basic loops by design
- Be careful with wildcard redirects that might redirect to themselves

## API Access (if needed)
The redirect model is available for programmatic access:

```php
// Find redirect for URL
$redirect = \App\Models\Redirect::findForUrl('/some-url');

// Create redirect programmatically
\App\Models\Redirect::create([
    'from_url' => '/old',
    'to_url' => '/new', 
    'status_code' => 301,
    'is_active' => true
]);
```