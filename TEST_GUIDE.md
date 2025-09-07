# 🧪 Complete Testing Guide for Background Processing System

This guide will help you verify that the Redis-only background processing system is working perfectly and that credit-score-rating is properly integrated.

## 🚀 Quick Test Commands

### 1. Run Complete System Test
```bash
php artisan test:background-system
```
This command tests **everything**:
- ✅ Redis connection
- ✅ Service controller integration  
- ✅ LocalRequestService methods
- ✅ Local API server
- ✅ Queue system
- ✅ Bot integration
- ✅ Complete flow

### 2. Test Specific Service
```bash
php artisan test:background-system --service=credit-score-rating
```

### 3. Debug Active Requests
```bash
# List all active requests
php artisan debug:local-request --list

# Debug specific request
php artisan debug:local-request req_ABC123... 

# Show raw Redis data
php artisan debug:local-request req_ABC123... --redis
```

## 📋 Manual Testing Checklist

### ✅ 1. Redis Connection Test
```bash
# Test Redis is running
redis-cli ping
# Should return: PONG

# Test basic operations
redis-cli set test_key test_value
redis-cli get test_key
redis-cli del test_key
```

### ✅ 2. Service Controller Test
```bash
# Check service exists in database
php artisan tinker
>>> App\Models\Service::where('slug', 'credit-score-rating')->first()

# Check controller is registered
>>> $service = App\Models\Service::where('slug', 'credit-score-rating')->first();
>>> $controller = App\Http\Controllers\Services\ServiceControllerFactory::getController($service);
>>> get_class($controller)
// Should return: App\Http\Controllers\Services\CreditScoreRatingBackgroundController
```

### ✅ 3. LocalRequestService Test
```bash
php artisan tinker
>>> $service = app(App\Services\LocalRequestService::class);
>>> $request = $service->createRequest('test', 1, ['mobile' => '09123456789'], 123, 'test-session');
>>> $request['hash'] // Should start with 'req_'
>>> $retrieved = $service->getRequest($request['hash']);
>>> $retrieved['hash'] === $request['hash'] // Should be true
```

### ✅ 4. Local API Server Test
```bash
# Check server is running
curl http://127.0.0.1:9999

# Check services endpoint
curl http://127.0.0.1:9999/api/services
# Should include "credit-score-rating"

# Test credit score service (will fail without OTP, but should connect)
curl -X POST http://127.0.0.1:9999/api/services/credit-score-rating \
  -H "Content-Type: application/json" \
  -d '{"mobile": "09123456789", "nationalCode": "1234567890", "requestHash": "req_TEST123"}'
```

### ✅ 5. Queue System Test
```bash
# Check queue configuration
php artisan config:show queue.default
# Should be: redis

# Start queue worker (in separate terminal)
php artisan queue:work --timeout=300

# Test job dispatch
php artisan tinker
>>> App\Jobs\ProcessLocalRequestJob::dispatch('req_TEST123');
```

### ✅ 6. Redis Bot Integration Test
```bash
# Check redis-updater.js exists
ls -la bots/inquiry-provider/services/redis-updater.js

# Check credit-score-rating integration
grep -n "redisUpdater" bots/inquiry-provider/services/credit-score-rating/index.js
grep -n "requestHash" bots/inquiry-provider/services/credit-score-rating/index.js
```

## 🎯 Full End-to-End Test

### Step 1: Start Required Services
```bash
# Terminal 1: Start Redis
redis-server

# Terminal 2: Start Local API Server  
cd bots/inquiry-provider
npm start

# Terminal 3: Start Queue Worker
php artisan queue:work --timeout=300

# Terminal 4: Run tests
```

### Step 2: Test Complete Flow
```bash
# Run full system test
php artisan test:background-system --service=credit-score-rating
```

### Step 3: Manual Browser Test
1. **Navigate** to: `/services/credit-score-rating`
2. **Fill form** with test data:
   - Mobile: `09123456789`
   - National Code: `1234567890`
3. **Submit form** → Should redirect to progress page
4. **Watch progress** → Should update every 5 seconds
5. **Check console** → No JavaScript errors

### Step 4: Debug Test Request
```bash
# While progress page is open, find the request hash from URL
# Example: /services/credit-score-rating/progress/req_ABC123...

# Debug the request
php artisan debug:local-request req_ABC123... --redis
```

## 🔍 Verification Points

### ✅ Redis Storage Verification
```bash
# Check Redis keys
redis-cli keys "local_request:*"

# Check specific request
redis-cli get "local_request:req_ABC123..."

# Check tracking list
redis-cli lrange local_requests_list 0 -1

# Monitor real-time updates
redis-cli monitor
```

### ✅ Progress Updates Verification
```bash
# Watch Redis updates in real-time
redis-cli monitor | grep local_request

# Should see updates like:
# "SETEX" "local_request:req_ABC123..." "1800" "{...progress:30...}"
# "PUBLISH" "local_request_updates:req_ABC123..." "{...progress:50...}"
```

### ✅ Bot Integration Verification
```bash
# Check bot logs for Redis updates
cd bots/inquiry-provider
npm start | grep "REDIS-UPDATER"

# Should see logs like:
# 📊 [REDIS-UPDATER] Updating progress for req_ABC123...
# ✅ [REDIS-UPDATER] Progress updated successfully
```

## 🚨 Troubleshooting

### Redis Connection Issues
```bash
# Check Redis status
sudo systemctl status redis

# Check Redis configuration
redis-cli config get "*"

# Test Redis memory
redis-cli info memory
```

### Queue Not Processing
```bash
# Restart queue workers
php artisan queue:restart
php artisan queue:work --sleep=3 --tries=3

# Check failed jobs
php artisan queue:failed

# Monitor queue
php artisan queue:monitor
```

### Local API Server Issues
```bash
# Check if port 9999 is in use
netstat -tulpn | grep :9999

# Check bot dependencies
cd bots/inquiry-provider
npm list ioredis
```

### Service Not Found
```bash
# Check service exists
php artisan db:seed --class=ServiceSeeder

# Check controller mapping
grep -n "credit-score-rating" app/Http/Controllers/Services/ServiceControllerFactory.php
```

## 📊 Expected Test Results

### ✅ Successful Test Output
```
🧪 Testing Background Processing System
Service: credit-score-rating

1️⃣ Testing Redis Connection...
  ✅ Redis Connection: Basic read/write operations work
  ✅ Redis TTL: TTL functionality works
  ✅ Redis Pub/Sub: Publish functionality works

2️⃣ Testing Service Controller...
  ✅ Service Model: Service 'credit-score-rating' found
  ✅ Service Controller: Controller found: App\Http\Controllers\Services\CreditScoreRatingBackgroundController
  ✅ Controller Type: Uses LocalApiController for background processing

3️⃣ Testing LocalRequestService...
  ✅ Create Request: Request created with hash: req_ABC123...
  ✅ Get Request: Request retrieved successfully
  ✅ Update Progress: Progress updated successfully
  ✅ Mark OTP Required: OTP status set successfully
  ✅ Mark Completed: Request marked as completed
  ✅ Get Status: Status retrieval works correctly

4️⃣ Testing Local API Server...
  ✅ Local API Server: Server is running and responding
  ✅ Services Endpoint: credit-score-rating service found

5️⃣ Testing Queue System...
  ✅ Queue Config: Queue using Redis driver
  ✅ Job Dispatch: ProcessLocalRequestJob dispatched successfully

6️⃣ Testing Redis Updater Bot...
  ✅ Redis Updater File: redis-updater.js exists
  ✅ Redis Import: ioredis dependency found
  ✅ Update Methods: updateProgress method found
  ✅ Credit Score Integration: redisUpdater imported in credit-score-rating
  ✅ Hash Parameter: requestHash parameter handled

7️⃣ Testing Complete Flow...
  ✅ Complete Flow: Background processing initiated with hash: req_DEF456...
  ✅ Flow Verification: Request properly stored in Redis

📊 Test Results Summary
==================================================
✅ Successes: 19
❌ Failures: 0
⚠️  Warnings: 0

🎉 All tests passed! The background processing system is working correctly.
Your credit-score-rating service is properly integrated!
```

## 🎯 Performance Verification

### Response Time Test
```bash
# Test API response times
time curl -s http://127.0.0.1:8000/api/local-requests/req_ABC123.../status
# Should be < 0.1 seconds

# Test Redis operations
redis-cli --latency-history -h 127.0.0.1 -p 6379
```

### Load Test
```bash
# Test multiple concurrent requests
for i in {1..10}; do
  curl -X POST http://127.0.0.1:8000/api/local-requests/test_$i/status &
done
wait
```

## ✅ Final Verification

If all tests pass, your system is **production-ready** with:

1. ✅ **Redis-only storage** - No database load
2. ✅ **Real-time updates** - 1ms response times  
3. ✅ **Background processing** - Non-blocking user experience
4. ✅ **Bot integration** - Progress updates from Node.js
5. ✅ **OTP flow** - Seamless verification process
6. ✅ **Auto-cleanup** - TTL-based expiration
7. ✅ **Error handling** - Graceful failure recovery

Your users will experience **lightning-fast** progress tracking! ⚡ 