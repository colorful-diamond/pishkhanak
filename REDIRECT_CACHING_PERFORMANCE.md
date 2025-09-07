# Redirect Caching Performance Guide

## Overview
The enhanced redirect caching system eliminates database queries for redirect lookups, providing near-instant redirects with comprehensive cache management.

## Cache Architecture

### 1. Per-URL Caching
```
Cache Key: redirect.url.{md5(clean_url)}
Duration: 2 hours (redirects) / 30 minutes (no redirects)
Purpose: Instant lookup for specific URLs
```

### 2. Active Redirects Cache
```
Cache Key: redirects.active.all
Duration: 1 hour
Purpose: Master list of all active redirects
```

### 3. Negative Caching
- URLs with no redirects are cached as 'NO_REDIRECT'
- Prevents repeated database queries for non-existent redirects
- Shorter cache duration (30 minutes) for flexibility

## Performance Metrics

### Before Caching
- Database query on every redirect check
- ~5-20ms per redirect lookup
- Increased database load

### After Caching
- Zero database queries for cached redirects
- ~0.1-1ms per redirect lookup
- 95%+ reduction in database load

## Cache Management

### Automatic Cache Warming
```bash
# Warm all redirects (recommended after deployment)
php artisan redirects:cache-warm

# Warm only popular redirects (for daily maintenance)
php artisan redirects:cache-warm --popular

# Scheduled warming (add to scheduler)
php artisan redirects:scheduled-cache-warm
```

### Cache Monitoring
```bash
# View detailed cache statistics
php artisan redirects:cache-stats

# Output example:
# Total redirects: 25
# Active redirects: 22
# Inactive redirects: 3
# Active redirects cached: Yes
# URL cache keys: 47
# Cache Driver: redis
```

### Cache Invalidation
- **Automatic**: When redirects are created/updated/deleted
- **Manual**: Admin panel buttons or CLI commands
- **Selective**: Only relevant caches are cleared

## Admin Panel Features

### Cache Dashboard
Access via **مدیریت محتوا > مدیریت تغییر مسیر**:

1. **آمار کش** - Real-time cache statistics
2. **گرم کردن کش** - Warm all redirect caches
3. **پاک کردن کل کش** - Emergency cache clear
4. **پاک کردن کش** (per redirect) - Selective cache clearing

### Cache Widget
The `RedirectCacheStatsWidget` shows:
- Active redirects count
- Total clicks/hits
- Cache status (active/inactive)
- Cache efficiency percentage

## Best Practices

### 1. Cache Warming Schedule
```php
// In routes/console.php or App\Console\Kernel
Schedule::command('redirects:scheduled-cache-warm')->hourly();
```

### 2. Deployment Strategy
```bash
# After deployment
php artisan migrate
php artisan redirects:cache-warm
php artisan config:cache
```

### 3. Monitoring
- Check cache stats weekly
- Monitor cache efficiency (aim for >80%)
- Track redirect lookup times in logs

### 4. Cache Driver Optimization
**Recommended**: Redis or Memcached
```env
CACHE_DRIVER=redis
# or
CACHE_DRIVER=memcached
```

**Avoid**: File or database cache for production

## Performance Tuning

### Redis Configuration
```redis
# Optimize Redis for caching
maxmemory-policy allkeys-lru
maxmemory 256mb
```

### Cache TTL Tuning
```php
// In Redirect model - adjust as needed
$cacheDuration = $foundRedirect ? 7200 : 1800; // 2h:30m
```

### Memory Usage
- Each redirect uses ~1-2KB of cache memory
- 1000 redirects ≈ 1-2MB cache usage
- Monitor with `redis-cli info memory`

## Troubleshooting

### High Cache Miss Rate
1. Check if cache is being cleared too frequently
2. Verify cache driver is working
3. Increase cache TTL if needed

### Memory Issues
1. Monitor cache key count
2. Implement cache key rotation
3. Consider using cache tags (Redis)

### Stale Redirects
1. Check cache invalidation on model events
2. Manual cache clear if needed
3. Verify model events are firing

## Monitoring Commands

```bash
# Check cache performance
php artisan redirects:cache-stats

# Test specific URL caching
php artisan redirects:cache-warm --urls="/test-url"

# Monitor Redis (if using Redis)
redis-cli monitor

# Check cache keys
redis-cli keys "*redirect*"
```

## Cache Statistics Interpretation

### Good Performance Indicators
- Cache efficiency >80%
- Active redirects cached: Yes
- Lookup time <1ms (in logs)
- Cache hit rate >90%

### Warning Signs
- Cache efficiency <50%
- High cache key count without traffic
- Lookup time >5ms
- Frequent cache misses

## Integration with Monitoring

### Laravel Telescope
Redirect lookups appear in Telescope cache queries

### Log Analysis
```bash
# Monitor redirect performance
tail -f storage/logs/laravel.log | grep "Redirect executed"
```

### Custom Metrics
```php
// Track cache performance
\Log::info('Redirect cache performance', [
    'lookup_time_ms' => $lookupTime,
    'cache_hit' => $cached !== null,
    'url' => $currentPath
]);
```