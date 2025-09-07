# 🚀 Background Processing System for Long-Running Services

A comprehensive background job processing system designed for services that require up to 300 seconds to complete, with real-time progress updates, OTP verification flow, and Redis-based status management.

## 📋 Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Components](#components)
- [Installation](#installation)
- [Usage](#usage)
- [Creating New Services](#creating-new-services)
- [Frontend Integration](#frontend-integration)
- [API Endpoints](#api-endpoints)
- [Redis Integration](#redis-integration)
- [Monitoring & Maintenance](#monitoring--maintenance)

## 🎯 Overview

This system provides:

- **Background Processing**: Long-running services execute as Laravel queue jobs
- **Real-time Updates**: Progress updates every 5 seconds via Redis
- **Progress Visualization**: Beautiful progress bars with step indicators
- **OTP Flow**: Seamless transition from progress → OTP → results
- **Redis Integration**: Fast status updates from JS bots to frontend
- **Auto-cleanup**: Expired requests are automatically removed
- **Error Handling**: Graceful error recovery and user feedback

## 🏗️ Architecture

```
┌─────────────────┐    Submit    ┌─────────────────┐    Dispatch   ┌─────────────────┐
│   User Form     │ ──────────> │ LocalApiController │ ─────────> │ ProcessLocalRequest │
│                 │              │                 │              │     Job         │
└─────────────────┘              └─────────────────┘              └─────────────────┘
         │                                │                               │
         │ Redirect                       │                               │ Calls
         ▼                                ▼                               ▼
┌─────────────────┐              ┌─────────────────┐              ┌─────────────────┐
│ Progress Page   │              │ LocalRequest    │              │ Local API Server│
│                 │              │ Model/DB        │              │ (Node.js)       │
└─────────────────┘              └─────────────────┘              └─────────────────┘
         │                                │                               │
         │ Polls Status                   │ Updates                       │ Updates
         ▼                                ▼                               ▼
┌─────────────────┐              ┌─────────────────┐              ┌─────────────────┐
│ Redis Status    │ ◄────────────│ LocalRequestService │ ◄──────── │ Redis Updater   │
│ & Pub/Sub       │              │                 │              │ (JS Module)     │
└─────────────────┘              └─────────────────┘              └─────────────────┘
```

## 🔧 Components

### Backend Components

1. **LocalRequest Model** - Database storage for request data and status
2. **LocalRequestService** - Redis integration and status management
3. **LocalApiController** - Base controller for background services
4. **ProcessLocalRequestJob** - Laravel queue job for background processing
5. **CreditScoreRatingBackgroundController** - Example implementation

### Frontend Components

1. **Progress Page** - Real-time progress visualization
2. **OTP Page** - SMS verification interface
3. **Result Page** - Final results display
4. **JavaScript** - Auto-updating progress and status polling

### JavaScript/Bot Components

1. **Redis Updater** - Node.js module for updating request status
2. **Service Integration** - Updated service handlers with Redis updates

## 📦 Installation

### 1. Database Migration

```bash
php artisan migrate
```

### 2. Redis Configuration

Ensure Redis is running and configured in your `.env`:

```env
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_DB=0

# Local API Configuration
LOCAL_API_URL=http://127.0.0.1:9999
LOCAL_API_TIMEOUT=180
LOCAL_API_RETRIES=2
```

### 3. Queue Configuration

Configure queue driver in `.env`:

```env
QUEUE_CONNECTION=redis
```

Start queue workers:

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### 4. JavaScript Dependencies

Install Redis client for Node.js bot:

```bash
cd bots/inquiry-provider
npm install ioredis
```

## 🚀 Usage

### For Credit Score Rating (Example)

1. **User submits form** → Redirected to progress page
2. **Background job starts** → Updates progress in real-time
3. **OTP sent** → User redirected to OTP verification
4. **OTP verified** → Final results displayed

### Progress Flow

```
Initial: 0% → Authentication: 30% → Captcha: 50% → OTP Sent: 70% → Verifying: 80% → Complete: 100%
```

## 🆕 Creating New Services

### 1. Create Controller

```php
<?php

namespace App\Http\Controllers\Services;

use App\Rules\IranianMobile;

class YourServiceBackgroundController extends LocalApiController
{
    protected function configureService(): void
    {
        $this->serviceSlug = 'your-service-slug';
        $this->estimatedDuration = 120; // seconds
        
        $this->requiredFields = ['mobile', 'national_code'];
        $this->validationRules = [
            'mobile' => ['required', 'string', new IranianMobile()],
            // Add your validation rules
        ];
        $this->validationMessages = [
            'mobile.required' => 'شماره موبایل الزامی است.',
            // Add your messages
        ];
    }
}
```

### 2. Register in ServiceControllerFactory

```php
private static $serviceMapping = [
    // ... existing services
    'your-service-slug' => YourServiceBackgroundController::class,
];
```

### 3. Create Node.js Service Handler

```javascript
// bots/inquiry-provider/services/your-service/index.js
import redisUpdater from '../redis-updater.js';

export async function handle(data) {
    const { requestHash } = data;
    
    try {
        // Update progress
        await redisUpdater.updateProgress(requestHash, 20, 'authentication', 'ارسال درخواست به درگاه دولت هوشمند...');
        
        // Your service logic here
        
        // Mark as OTP required
        await redisUpdater.markAsOtpRequired(requestHash, {
            hash: 'otp-hash',
            expiry: 300,
            message: 'کد تایید ارسال شد'
        });
        
        return { status: 'success', code: 'SMS_SENT' };
        
    } catch (error) {
        await redisUpdater.markAsFailed(requestHash, error.message);
        throw error;
    }
}
```

## 🌐 Frontend Integration

### Routes

The system automatically creates these routes:

- `/services/{service}/progress/{hash}` - Progress page
- `/services/{service}/otp/{hash}` - OTP verification
- `/services/{service}/result/{hash}` - Results page

### Views

Create custom views for your service:

```
resources/views/front/services/
├── progress.blade.php    # Progress page (universal)
├── otp.blade.php        # OTP page (universal)  
├── result.blade.php     # Results page (universal)
```

## 📡 API Endpoints

### Get Request Status

```http
GET /api/local-requests/{hash}/status
```

Response:
```json
{
    "hash": "req_ABC123...",
    "status": "processing",
    "step": "captcha_solving",
    "progress": 50,
    "current_message": "حل کپچا...",
    "estimated_remaining_time": 90,
    "requires_otp": false,
    "is_completed": false
}
```

### Cancel Request

```http
POST /api/local-requests/{hash}/cancel
```

### Resend OTP

```http
POST /api/local-requests/{hash}/resend-otp
```

## 🔴 Redis Integration

### Data Structure

```javascript
// Redis Key: local_request:req_ABC123...
{
    "hash": "req_ABC123...",
    "service_slug": "credit-score-rating",
    "status": "processing",
    "step": "captcha_solving", 
    "progress": 50,
    "current_message": "حل کپچا...",
    "estimated_remaining_time": 90,
    "otp_data": {...},
    "result_data": {...},
    "updated_at": "2024-01-01T12:00:00Z"
}
```

### Pub/Sub Channels

- Channel: `local_request_updates:{hash}`
- Publishes real-time updates to listening clients

### Redis Updater Methods

```javascript
// Update progress
await redisUpdater.updateProgress(hash, progress, step, message);

// Mark as OTP required
await redisUpdater.markAsOtpRequired(hash, otpData);

// Mark as completed
await redisUpdater.markAsCompleted(hash, resultData);

// Mark as failed
await redisUpdater.markAsFailed(hash, errorMessage, errorData);
```

## 🔧 Monitoring & Maintenance

### Cleanup Expired Requests

```bash
# Manual cleanup
php artisan local-requests:cleanup

# Dry run (preview)
php artisan local-requests:cleanup --dry-run

# Add to cron (every hour)
0 * * * * php artisan local-requests:cleanup
```

### Queue Monitoring

```bash
# Monitor queue
php artisan queue:monitor

# Failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Redis Monitoring

```bash
# Redis CLI
redis-cli monitor

# Check memory usage
redis-cli info memory

# List keys
redis-cli keys "local_request:*"
```

## 🎨 Customization

### Progress Steps

Customize progress steps by editing the step constants in `LocalRequest` model:

```php
const STEP_INITIALIZING = 'initializing';
const STEP_AUTHENTICATION = 'authentication';
const STEP_CAPTCHA_SOLVING = 'captcha_solving';
const STEP_SENDING_OTP = 'sending_otp';
// Add your custom steps
```

### UI Customization

The frontend uses Tailwind CSS classes and can be customized by editing:

- `resources/views/front/services/progress.blade.php`
- `resources/views/front/services/otp.blade.php`
- `resources/views/front/services/result.blade.php`

### Timeout Configuration

Adjust timeouts in various components:

- **Laravel Job**: `ProcessLocalRequestJob::$timeout`
- **Local API**: `config/services.php` → `local_api.timeout`
- **Frontend Polling**: JavaScript `setInterval` timing
- **Redis TTL**: `LocalRequestService::$redisTtl`

## 🚨 Troubleshooting

### Common Issues

1. **Queue not processing**
   ```bash
   php artisan queue:restart
   php artisan queue:work
   ```

2. **Redis connection issues**
   - Check Redis is running: `redis-cli ping`
   - Verify connection config in `.env`

3. **Progress not updating**
   - Check queue workers are running
   - Verify Redis Pub/Sub is working
   - Check browser console for JS errors

4. **OTP not working**
   - Verify `otp_data` is properly set in Redis
   - Check LocalRequest database record
   - Ensure proper hash format

### Debugging

Enable debug mode in bot:

```env
DEBUG_MODE=true
```

Check logs:

```bash
tail -f storage/logs/laravel.log
```

## 🔒 Security Considerations

- ✅ Request hashes are cryptographically secure
- ✅ User session validation for request access
- ✅ Rate limiting on all API endpoints
- ✅ CSRF protection on form submissions
- ✅ Input validation and sanitization
- ✅ Automatic cleanup of expired data

## 📊 Performance

### Optimizations

- **Redis Caching**: Fast status retrieval
- **Database Indexing**: Optimized queries
- **Queue Processing**: Background execution
- **TTL Management**: Automatic cleanup
- **Connection Pooling**: Efficient Redis usage

### Monitoring Metrics

- Request processing time
- Queue job success/failure rates  
- Redis memory usage
- Database query performance
- API response times

---

## 🎉 Success!

Your background processing system is now ready! Users will experience:

- ⚡ **Instant feedback** with real-time progress
- 📱 **Smooth OTP flow** with beautiful UI
- 🎯 **Reliable processing** with automatic retries
- 🔄 **Real-time updates** every 5 seconds
- 💾 **Persistent state** across page refreshes
- 🛡️ **Error recovery** with user-friendly messages

The system handles everything automatically - from background processing to Redis updates to frontend visualization! 