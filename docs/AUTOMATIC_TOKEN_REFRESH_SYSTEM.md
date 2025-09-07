# 🔄 **Automatic Token Refresh System**

## 📋 **Overview**

This system provides automated token refresh functionality for API providers (Jibit and Finnotech) that runs every 12 hours with comprehensive logging, monitoring, and failure handling.

## 🎯 **Features**

### ✅ **Core Functionality**
- **🕐 Automatic Refresh**: Runs every 12 hours via Laravel scheduler
- **📊 Advanced Logging**: Comprehensive tracking of all refresh attempts
- **🔍 Real-time Monitoring**: Filament admin panel with live updates
- **🚨 Failure Notifications**: Email alerts for failed refresh attempts
- **🔄 Backup System**: Fallback refresh job when automatic system fails
- **⚡ Queue Support**: Background processing with retry logic
- **📈 Health Monitoring**: Success rate tracking and statistics

### 🏗️ **Architecture Components**

1. **AutomaticTokenRefreshJob** - Main refresh job
2. **TokenRefreshLog** - Database tracking model
3. **TokenRefreshLogResource** - Filament admin interface
4. **TokenRefreshFailedNotification** - Email notifications
5. **AutoRefreshTokensCommand** - Manual trigger command

## 📋 **System Requirements**

### Prerequisites
- Laravel 11.x
- Filament 3.x
- Queue system (database/redis)
- Laravel scheduler (cron)

### Dependencies
- Existing TokenService
- Token model with Jibit/Finnotech providers
- Email configuration for notifications

## 🚀 **Installation & Setup**

### 1. **Run Migrations**
```bash
php artisan migrate
```

### 2. **Configure Environment**
Add to your `.env` file:
```env
# Token Refresh Notifications
TOKEN_REFRESH_NOTIFICATIONS_ENABLED=true
ADMIN_EMAIL=admin@pishkhanak.com

# Queue Configuration
QUEUE_CONNECTION=database
```

### 3. **Set Up Laravel Scheduler**
Add to your crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. **Start Queue Worker**
```bash
php artisan queue:work --queue=tokens
```

## 📖 **Usage Guide**

### **Automatic Operation**

The system runs automatically every 12 hours. No manual intervention required.

**Schedule**: Every 12 hours starting from deployment
**Queue**: `tokens`
**Timeout**: 10 minutes per job
**Retries**: 3 attempts with 60-second backoff

### **Manual Triggers**

#### **Command Line**
```bash
# Refresh all providers
php artisan tokens:auto-refresh

# Refresh specific provider
php artisan tokens:auto-refresh --provider=jibit

# Force refresh (even if not needed)
php artisan tokens:auto-refresh --force

# Run synchronously (for testing)
php artisan tokens:auto-refresh --sync
```

#### **Access Panel**
1. Navigate to `/access/token-refresh-logs`
2. Click "Trigger Refresh" button
3. Choose provider and options
4. Monitor results in real-time

## 📊 **Monitoring & Analytics**

### **Access Panel Dashboard**
- **URL**: `/access/token-refresh-logs`
- **Features**:
  - Real-time refresh status
  - Success/failure statistics
  - Duration tracking
  - Error details and codes
  - Retry functionality
  - Auto-refresh every 30 seconds

### **Log Categories**
- **✅ Success**: Token refreshed successfully
- **❌ Failed**: Refresh attempt failed
- **⏭️ Skipped**: Token didn't need refresh yet

### **Trigger Types**
- **🤖 Automatic**: Scheduled 12-hour refresh
- **👤 Manual**: Triggered via admin panel
- **💪 Forced**: Manual refresh ignoring expiry

## 🚨 **Error Handling & Notifications**

### **Failure Scenarios**
1. **API Provider Down**: Network/service issues
2. **Invalid Credentials**: API key/secret problems
3. **Token Expired**: Refresh token expired
4. **System Errors**: Database/queue issues

### **Notification System**
- **Email Alerts**: Sent to admin on failures
- **Panel Badges**: Visual indicators in access panel
- **Detailed Logs**: Complete error traces and metadata

### **Backup System**
- **Legacy Job**: Runs twice daily as backup
- **Conditional**: Only if automatic refresh failed
- **Redundancy**: Ensures tokens never expire

## 📈 **Performance & Optimization**

### **Efficiency Features**
- **Smart Refresh**: Only refresh when needed (5-minute threshold)
- **Concurrent Processing**: Parallel provider refresh
- **Caching**: Token status caching for 5 minutes
- **Queue Processing**: Background execution
- **Resource Limits**: 10-minute timeout, 3 retries

### **Scalability**
- **Configurable**: All settings in config file
- **Extensible**: Easy to add new providers
- **Monitoring**: Real-time performance metrics
- **Cleanup**: Automatic log cleanup after 30 days

## ⚙️ **Configuration**

### **Config File**: `config/auto-token-refresh.php`

Key settings:
```php
'schedule' => [
    'interval' => 'everyTwelveHours',
    'timeout' => 600,
    'retries' => 3,
],

'notifications' => [
    'enabled' => true,
    'email' => env('ADMIN_EMAIL'),
    'triggers' => [
        'on_failure' => true,
        'on_critical' => true,
    ],
],

'logging' => [
    'cleanup_after_days' => 30,
    'detailed_metadata' => true,
],
```

## 🛠️ **Troubleshooting**

### **Common Issues**

#### **Scheduler Not Running**
```bash
# Check if cron is working
php artisan schedule:list

# Test scheduler manually
php artisan schedule:run
```

#### **Queue Not Processing**
```bash
# Check queue status
php artisan queue:work --queue=tokens --verbose

# Clear failed jobs
php artisan queue:flush
```

#### **Tokens Still Expiring**
1. Check access panel logs for errors
2. Verify API credentials in `.env`
3. Test manual refresh: `php artisan tokens:auto-refresh --sync`
4. Check provider API status

### **Debug Commands**
```bash
# Check token health
php artisan tokens:refresh --provider=jibit

# View recent logs
php artisan log:show --lines=50

# Test notification
php artisan queue:work --queue=tokens --once
```

## 📊 **API & Integration**

### **TokenRefreshLog Model**
```php
// Get latest logs
TokenRefreshLog::recent(24)->get();

// Check success rate
TokenRefreshLog::getSuccessRateForProvider('jibit', 24);

// Get statistics
TokenRefreshLog::getStatistics(24);
```

### **Manual Job Dispatch**
```php
// Dispatch refresh job
AutomaticTokenRefreshJob::dispatch('jibit', true);

// Check job status
Bus::batch([new AutomaticTokenRefreshJob()])->dispatch();
```

## 🔄 **Maintenance**

### **Regular Tasks**
1. **Monitor Success Rate**: Keep above 90%
2. **Check Error Logs**: Review failed attempts weekly
3. **Update Credentials**: Refresh API keys as needed
4. **Clean Old Logs**: Automatic cleanup after 30 days

### **Health Checks**
- **Token Expiry**: Monitor upcoming expirations
- **Queue Status**: Ensure workers are running
- **Notification Delivery**: Test email alerts
- **Performance**: Track refresh duration trends

## 📧 **Support & Alerts**

### **Notification Types**
- **🚨 Critical**: Token expires within 2 hours
- **❌ Failed**: Refresh attempt failed
- **📊 Summary**: Daily statistics report

### **Admin Actions Required**
1. **Failed Notifications**: Check API credentials
2. **Critical Expiry**: Manual intervention needed
3. **System Errors**: Review logs and fix issues

## 🔗 **Related Documentation**
- [Token Management Guide](./TOKEN_MANAGEMENT.md)
- [API Provider Setup](./API_PROVIDERS.md)
- [Queue Configuration](./QUEUE_SETUP.md)
- [Filament Admin Panel](./ADMIN_PANEL.md)

---

## 📞 **Quick Reference**

### **Key URLs**
- **Logs**: `/access/token-refresh-logs`
- **Tokens**: `/access/tokens`
- **Health**: `/access/token-health`

### **Key Commands**
- **Manual Refresh**: `php artisan tokens:auto-refresh`
- **Check Schedule**: `php artisan schedule:list`
- **Queue Worker**: `php artisan queue:work --queue=tokens`

### **Emergency Actions**
1. **Force Refresh**: `php artisan tokens:auto-refresh --force --sync`
2. **Restart Queues**: `php artisan queue:restart`
3. **Clear Cache**: `php artisan cache:clear`

---

**🎯 System Status**: ✅ **ACTIVE** - Automatically refreshing every 12 hours
**📧 Contact**: Support team for issues or questions
**📝 Last Updated**: January 2025 