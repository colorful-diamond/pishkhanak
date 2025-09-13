# Varnish Cache Activation Commands for Claude Code (Root User)

## Prerequisites Check
Run these commands first to verify the environment:

```bash
# Check if Varnish is installed and running
systemctl status varnish --no-pager

# Check current Varnish configuration
ls -la /etc/varnish/default.vcl

# Verify Redis is running (needed for sessions)
systemctl status redis --no-pager
```

## Step 1: Backup Current Configuration

```bash
# Create backup directory
mkdir -p /root/varnish-backups

# Backup current Varnish config
cp /etc/varnish/default.vcl /root/varnish-backups/default.vcl.backup-$(date +%Y%m%d_%H%M%S)

# Backup Laravel .env file
cp /home/pishkhanak/htdocs/pishkhanak.com/.env /root/varnish-backups/env.backup-$(date +%Y%m%d_%H%M%S)

# List backups to confirm
ls -la /root/varnish-backups/
```

## Step 2: Apply New Varnish Configuration

```bash
# Copy the optimized Varnish configuration
cp /home/pishkhanak/htdocs/pishkhanak.com/varnish-laravel-optimized.vcl /etc/varnish/default.vcl

# Verify the configuration syntax
varnishd -C -f /etc/varnish/default.vcl > /dev/null 2>&1 && echo "✓ Configuration is valid" || echo "✗ Configuration has errors"

# If configuration is valid, reload Varnish
systemctl reload varnish

# Check Varnish status after reload
systemctl status varnish --no-pager | head -20
```

## Step 3: Update Laravel Environment Configuration

```bash
# Navigate to Laravel directory
cd /home/pishkhanak/htdocs/pishkhanak.com

# Update .env file with Varnish settings
cat >> .env << 'EOF'

# Varnish Cache Settings (Added by activation script)
VARNISH_ENABLED=true
VARNISH_HOST=127.0.0.1
VARNISH_PORT=6081
VARNISH_ADMIN_PORT=6082
VARNISH_DEBUG=false

# Cache TTL Settings (in seconds)
VARNISH_TTL_HOMEPAGE=300
VARNISH_TTL_STATIC=86400
VARNISH_TTL_BLOG=900
VARNISH_TTL_SERVICES=3600
VARNISH_TTL_CATEGORIES=3600
VARNISH_TTL_API_PUBLIC=300
VARNISH_TTL_ASSETS=2592000
VARNISH_GRACE_PERIOD=21600
VARNISH_VARY_BY_USER_AGENT=true
VARNISH_ESI_ENABLED=true
EOF

# Update session driver to Redis (better for Varnish)
sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=redis/' .env

# Verify the changes
grep -E "VARNISH_|SESSION_DRIVER" .env
```

## Step 4: Configure Laravel Application

```bash
# Clear all Laravel caches
cd /home/pishkhanak/htdocs/pishkhanak.com
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Register the middleware (update Kernel.php)
# Check if middleware is already registered
grep -q "VarnishCache" app/Http/Kernel.php || {
    echo "Adding VarnishCache middleware to Kernel.php..."
    # This would need manual editing or use sed for specific insertion
}

# Optimize Laravel for production
php artisan config:cache
php artisan route:cache
```

## Step 5: Configure Nginx for Varnish (CloudPanel)

```bash
# Find the Nginx configuration file for pishkhanak.com
NGINX_CONF=$(find /etc/nginx -name "*pishkhanak*" 2>/dev/null | head -1)

if [ -n "$NGINX_CONF" ]; then
    echo "Found Nginx config: $NGINX_CONF"
    
    # Backup Nginx configuration
    cp "$NGINX_CONF" "/root/varnish-backups/nginx-$(basename $NGINX_CONF).backup-$(date +%Y%m%d_%H%M%S)"
    
    # Check if Varnish proxy is configured
    grep -q "proxy_pass.*6081" "$NGINX_CONF" && echo "✓ Varnish proxy already configured" || echo "⚠ Varnish proxy needs to be configured in CloudPanel"
else
    echo "⚠ Could not find Nginx configuration automatically"
    echo "You need to enable Varnish in CloudPanel web interface"
fi

# Test Nginx configuration
nginx -t
```

## Step 6: Verify Varnish is Working

```bash
# Test Varnish directly
curl -I http://127.0.0.1:6081/ 2>/dev/null | grep -E "HTTP|Server|X-Varnish"

# Test if website responds through Varnish
curl -I https://pishkhanak.com/ 2>/dev/null | grep -E "X-Cache|Cache-Control|X-Served-By"

# Check Varnish statistics
varnishstat -1 | grep -E "cache_hit|cache_miss|client_req" | head -10

# Test cache hit ratio
echo "=== Varnish Cache Statistics ==="
varnishstat -1 | awk '/cache_hit|cache_miss/ {print $1": "$2}'

# Monitor real-time (run for 5 seconds then stop)
timeout 5 varnishstat || true
```

## Step 7: Test Critical Functionality

```bash
# Test homepage caching
echo "Testing homepage..."
curl -s -o /dev/null -w "Response time: %{time_total}s\n" https://pishkhanak.com/
curl -s -o /dev/null -w "Response time (cached): %{time_total}s\n" https://pishkhanak.com/

# Test form pages still work (should bypass cache)
echo "Testing login page (should bypass cache)..."
curl -I https://pishkhanak.com/login 2>/dev/null | grep -E "Cache-Control|Set-Cookie"

# Test API endpoint
echo "Testing API..."
curl -I https://pishkhanak.com/api/public/services 2>/dev/null | grep -E "Cache-Control|X-Cache"
```

## Step 8: Setup Monitoring

```bash
# Create a simple monitoring script
cat > /usr/local/bin/varnish-monitor.sh << 'EOF'
#!/bin/bash
echo "=== Varnish Cache Monitor ==="
echo "Date: $(date)"
echo ""
echo "Service Status:"
systemctl is-active varnish && echo "✓ Varnish is running" || echo "✗ Varnish is not running"
echo ""
echo "Cache Statistics:"
varnishstat -1 | grep -E "cache_hit|cache_miss|client_req" | awk '{printf "%-30s %s\n", $1":", $2}'
echo ""
echo "Hit Rate:"
HITS=$(varnishstat -1 | grep "cache_hit" | awk '{print $2}')
MISSES=$(varnishstat -1 | grep "cache_miss" | awk '{print $2}')
if [ "$HITS" -gt 0 ] && [ "$MISSES" -gt 0 ]; then
    RATIO=$(echo "scale=2; $HITS * 100 / ($HITS + $MISSES)" | bc)
    echo "Cache hit ratio: ${RATIO}%"
fi
EOF

chmod +x /usr/local/bin/varnish-monitor.sh

# Run the monitor
/usr/local/bin/varnish-monitor.sh
```

## Step 9: Create Emergency Rollback Script

```bash
# Create rollback script in case of issues
cat > /usr/local/bin/varnish-rollback.sh << 'EOF'
#!/bin/bash
echo "=== Rolling back Varnish configuration ==="

# Restore original Varnish config
LATEST_BACKUP=$(ls -t /root/varnish-backups/default.vcl.backup-* 2>/dev/null | head -1)
if [ -n "$LATEST_BACKUP" ]; then
    cp "$LATEST_BACKUP" /etc/varnish/default.vcl
    systemctl reload varnish
    echo "✓ Varnish configuration restored from $LATEST_BACKUP"
else
    echo "✗ No backup found"
fi

# Restore Laravel .env
LATEST_ENV=$(ls -t /root/varnish-backups/env.backup-* 2>/dev/null | head -1)
if [ -n "$LATEST_ENV" ]; then
    cp "$LATEST_ENV" /home/pishkhanak/htdocs/pishkhanak.com/.env
    cd /home/pishkhanak/htdocs/pishkhanak.com
    php artisan config:clear
    echo "✓ Laravel .env restored from $LATEST_ENV"
else
    echo "✗ No .env backup found"
fi

echo "Rollback complete. You may need to disable Varnish in CloudPanel manually."
EOF

chmod +x /usr/local/bin/varnish-rollback.sh

echo "✓ Rollback script created at /usr/local/bin/varnish-rollback.sh"
```

## Step 10: Final Verification and Report

```bash
# Generate activation report
cat > /root/varnish-activation-report.txt << 'EOF'
VARNISH ACTIVATION REPORT
========================
EOF

echo "Date: $(date)" >> /root/varnish-activation-report.txt
echo "" >> /root/varnish-activation-report.txt

# Check all services
echo "Service Status:" >> /root/varnish-activation-report.txt
systemctl is-active varnish >> /root/varnish-activation-report.txt
systemctl is-active nginx >> /root/varnish-activation-report.txt
systemctl is-active redis >> /root/varnish-activation-report.txt

# Check configuration
echo "" >> /root/varnish-activation-report.txt
echo "Configuration Files:" >> /root/varnish-activation-report.txt
ls -la /etc/varnish/default.vcl >> /root/varnish-activation-report.txt
ls -la /home/pishkhanak/htdocs/pishkhanak.com/.env >> /root/varnish-activation-report.txt

# Performance test
echo "" >> /root/varnish-activation-report.txt
echo "Performance Test:" >> /root/varnish-activation-report.txt
curl -s -o /dev/null -w "Homepage response time: %{time_total}s\n" https://pishkhanak.com/ >> /root/varnish-activation-report.txt

# Display report
cat /root/varnish-activation-report.txt

echo ""
echo "==================================="
echo "✓ VARNISH ACTIVATION COMPLETE"
echo "==================================="
echo ""
echo "Next steps:"
echo "1. Log into CloudPanel"
echo "2. Go to Sites → pishkhanak.com → Settings"
echo "3. Enable Varnish Cache (if not already enabled)"
echo "4. Set port to 6081"
echo ""
echo "To monitor: varnish-monitor.sh"
echo "To rollback: varnish-rollback.sh"
echo ""
echo "Report saved to: /root/varnish-activation-report.txt"
```

## Important Notes for CloudPanel

After running these commands, you MUST:

1. **Enable Varnish in CloudPanel Web Interface**:
   - Log into CloudPanel at https://your-server:8443
   - Navigate to Sites → pishkhanak.com
   - Go to Settings tab
   - Find "Varnish Cache" option
   - Toggle it ON
   - Ensure port is set to 6081
   - Save changes

2. **CloudPanel will automatically**:
   - Configure Nginx to proxy through Varnish
   - Handle SSL termination
   - Set proper headers

3. **If you encounter issues**:
   - Run: `/usr/local/bin/varnish-rollback.sh`
   - Disable Varnish in CloudPanel
   - Check logs: `journalctl -u varnish -n 50`

## Testing After CloudPanel Activation

```bash
# Final test after CloudPanel activation
echo "=== Final Varnish Test ==="

# Test cache headers
curl -I https://pishkhanak.com/ 2>/dev/null | grep -E "X-Cache|Cache-Control"

# Test multiple requests (should show cache hits)
for i in {1..3}; do
    echo "Request $i:"
    curl -s -o /dev/null -w "  Response time: %{time_total}s\n" https://pishkhanak.com/
    sleep 1
done

# Check hit rate
/usr/local/bin/varnish-monitor.sh
```

## Troubleshooting Commands

```bash
# If Varnish fails to start
journalctl -u varnish -n 50

# If forms don't work
grep -E "csrf|form" /etc/varnish/default.vcl

# If logged-in users see wrong content
varnishlog -g request -q "ReqHeader:Cookie"

# To clear all cache
varnishadm "ban req.url ~ ."
```

---

**Save this file and provide the path to Claude Code running as root:**
```
/home/pishkhanak/htdocs/pishkhanak.com/VARNISH_ACTIVATION_COMMANDS.md
```

Claude Code can then execute each section sequentially to activate Varnish properly.