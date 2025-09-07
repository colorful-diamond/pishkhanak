<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TokenHealthController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\AiSearchController;
use App\Http\Controllers\FinnotechSmsAuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::post('/services/jibit', [JibitServiceController::class, 'handle'])
//      ->middleware('throttle:15,1'); // 15 requests per minute

// AI Search API Routes
Route::prefix('ai-search')->name('ai-search.')->group(function () {
    // Text search
    Route::post('/text', [AiSearchController::class, 'searchText'])
         ->middleware('ai-search-throttle:text');
    
    // Voice search
    Route::post('/voice', [AiSearchController::class, 'searchVoice'])
         ->middleware('ai-search-throttle:voice');
    
    // Image search
    Route::post('/image', [AiSearchController::class, 'searchImage'])
         ->middleware('ai-search-throttle:image');
    
    // Conversational search
    Route::post('/conversational', [AiSearchController::class, 'searchConversational'])
         ->middleware(['ai-search-throttle:conversational', 'file-upload-security']);
    
    // Utility endpoints
    Route::get('/suggestions', [AiSearchController::class, 'getSuggestions'])
         ->middleware('throttle:60,1'); // 60 requests per minute for suggestions
    
    Route::get('/auto-typing-examples', [AiSearchController::class, 'getAutoTypingExamples'])
         ->middleware('throttle:30,1'); // 30 requests per minute
    
    // Conversation management
    Route::get('/conversation/history', [AiSearchController::class, 'getConversationHistory'])
         ->middleware('throttle:30,1');
    
    Route::delete('/conversation/history', [AiSearchController::class, 'clearConversationHistory'])
         ->middleware('throttle:10,1');
    
    // File access
    Route::get('/file-access/{token}', [AiSearchController::class, 'serveFile'])
         ->middleware('throttle:60,1');
    
    // Analytics (authenticated users only)
    Route::get('/analytics', [AiSearchController::class, 'getSearchAnalytics'])
         ->middleware(['auth:sanctum', 'throttle:10,1']);
});

// Token health monitoring endpoints
Route::prefix('tokens')->group(function () {
    Route::get('/health', [TokenHealthController::class, 'health'])
         ->middleware('throttle:30,1'); // 30 requests per minute
    
    Route::post('/refresh', [TokenHealthController::class, 'refresh'])
         ->middleware('throttle:5,1'); // 5 requests per minute
    
    Route::get('/needs-refresh', [TokenHealthController::class, 'needsRefresh'])
         ->middleware('throttle:30,1'); // 30 requests per minute
});

// Payment Gateway API Routes
Route::prefix('payments')->group(function () {
    // Public routes
    Route::get('gateways', [PaymentController::class, 'getGateways']);
    
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('create', [PaymentController::class, 'createPayment']);
        Route::post('{transactionUuid}/verify', [PaymentController::class, 'verifyPayment']);
        Route::get('{transactionUuid}', [PaymentController::class, 'getTransaction']);
        Route::get('user/transactions', [PaymentController::class, 'getUserTransactions']);
        Route::post('{transactionUuid}/refund', [PaymentController::class, 'refundPayment']);
    });
});

// Finnotech SMS Authorization API Routes
Route::prefix('finnotech/sms-auth')->name('finnotech.sms-auth.')->group(function () {
    // Token management routes
    Route::post('/token/check', [FinnotechSmsAuthController::class, 'checkTokenStatus'])
         ->middleware('throttle:60,1'); // 60 requests per minute
    
    Route::post('/token/revoke', [FinnotechSmsAuthController::class, 'revokeToken'])
         ->middleware('throttle:10,1'); // 10 requests per minute
});

// AI Content Generation Progress API Routes
Route::prefix('ai-content-progress')->name('ai-content-progress.')->group(function () {
    // Get AI content generation status
    Route::get('/{sessionHash}/status', function($sessionHash) {
        $progressService = app(\App\Services\AiContentProgressService::class);
        $status = $progressService->getProgress($sessionHash);
        
        if (!$status) {
            \Illuminate\Support\Facades\Log::debug('AI Progress API: Session not found', ['hash' => $sessionHash]);
            return response()->json(['error' => 'جلسه یافت نشد'], 404);
        }
        
        return response()->json($status);
    });

    // Cancel AI content generation
    Route::post('/{sessionHash}/cancel', function($sessionHash) {
        $progressService = app(\App\Services\AiContentProgressService::class);
        $success = $progressService->markAsFailed($sessionHash, 'تولید محتوا توسط کاربر لغو شد');
        
        if ($success) {
            return response()->json(['success' => true, 'message' => 'تولید محتوا لغو شد']);
        }
        
        return response()->json(['success' => false, 'message' => 'خطا در لغو درخواست'], 500);
    });

    // Restart AI content generation
    Route::post('/{sessionHash}/restart', function($sessionHash) {
        // Implementation for restarting generation with same parameters
        return response()->json(['success' => true, 'message' => 'قابلیت راه‌اندازی مجدد به زودی اضافه خواهد شد']);
    });
});

// Local Request API Routes
Route::prefix('local-requests')->name('local-requests.')->group(function () {
    // Get request status
    Route::get('/{hash}/status', function($hash) {
        $localRequestService = app(\App\Services\LocalRequestService::class);
        $status = $localRequestService->getRequestStatus($hash);
        
        if (!$status) {
            \Illuminate\Support\Facades\Log::debug('Status API: Request not found', ['hash' => $hash]);
            return response()->json(['error' => 'درخواست یافت نشد'], 404);
        }
        
        // \Illuminate\Support\Facades\Log::debug('Status API response', [
        //     'hash' => $hash,
        //     'status' => $status['status'] ?? 'unknown',
        //     'requires_otp' => $status['requires_otp'] ?? false,
        //     'step' => $status['step'] ?? 'unknown',
        //     'progress' => $status['progress'] ?? 0
        // ]);
        
        return response()->json($status);
    })->where(['hash' => 'req_[A-Z0-9]{16}'])
      ->middleware('throttle:60,1'); // 60 requests per minute

    // Cancel request
    Route::post('/{hash}/cancel', function($hash) {
        $localRequestService = app(\App\Services\LocalRequestService::class);
        $success = $localRequestService->cancelRequest($hash);
        
        return response()->json([
            'success' => $success,
            'message' => $success ? 'درخواست لغو شد' : 'امکان لغو درخواست وجود ندارد'
        ]);
    })->where(['hash' => 'req_[A-Z0-9]{16}'])
      ->middleware('throttle:30,1'); // 30 requests per minute

    // Resend OTP for local API services
    Route::post('/{hash}/resend-otp', function($hash) {
        try {
            $localRequestService = app(\App\Services\LocalRequestService::class);
            $localRequest = $localRequestService->getRequest($hash);
            
            if (!$localRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'درخواست یافت نشد'
                ], 404);
            }
            
            // Check if request requires OTP or has OTP error
            if ($localRequest['status'] !== 'otp_required' && $localRequest['step'] !== 'otp_error') {
                return response()->json([
                    'success' => false,
                    'message' => 'درخواست در مرحله ارسال مجدد کد نیست'
                ], 400);
            }
            
            // Check rate limiting (120 seconds = 2 minutes)
            $lastResendKey = "resend_otp:{$hash}";
            $lastResend = \Illuminate\Support\Facades\Cache::get($lastResendKey);
            
            if ($lastResend && now()->diffInSeconds($lastResend) < 120) {
                $remainingTime = 120 - now()->diffInSeconds($lastResend);
                return response()->json([
                    'success' => false,
                    'message' => "لطفاً {$remainingTime} ثانیه صبر کنید"
                ], 429);
            }
            
            // Call local API to resend SMS
            $result = \Illuminate\Support\Facades\Http::timeout(30)
                ->post(config('services.local_api.url') . '/api/services/' . $localRequest['service_slug'], [
                    'resendSms' => true,
                    'hash' => $localRequest['otp_data']['hash'] ?? null,
                    'mobile' => $localRequest['request_data']['mobile'] ?? null,
                    'national_code' => $localRequest['request_data']['national_code'] ?? null,
                    'requestHash' => $hash
                ]);
            
            if ($result->successful()) {
                $responseData = $result->json();
                
                if ($responseData['status'] === 'success') {
                    // Set rate limiting cache
                    \Illuminate\Support\Facades\Cache::put($lastResendKey, now(), 120);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'کد تایید مجدد ارسال شد'
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در ارسال مجدد کد'
            ], 500);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in resend OTP', [
                'hash' => $hash,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در ارسال مجدد کد'
            ], 500);
        }
    })->where(['hash' => 'req_[A-Z0-9]{16}'])
      ->middleware('throttle:5,1'); // 5 requests per minute
    
    // Resend SMS for progress page
    Route::post('/{hash}/resend-sms', function($hash) {
        try {
            $localRequestService = app(\App\Services\LocalRequestService::class);
            $localRequest = $localRequestService->getRequest($hash);
            
            if (!$localRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'درخواست یافت نشد'
                ], 404);
            }
            
            // Check if request is in SMS sending step
            if ($localRequest['step'] !== 'sending_otp') {
                return response()->json([
                    'success' => false,
                    'message' => 'درخواست در مرحله ارسال پیامک نیست'
                ], 400);
            }
            
            // Check rate limiting (120 seconds = 2 minutes)
            $lastResendKey = "resend_sms:{$hash}";
            $lastResend = \Illuminate\Support\Facades\Cache::get($lastResendKey);
            
            if ($lastResend && now()->diffInSeconds($lastResend) < 120) {
                $remainingTime = 120 - now()->diffInSeconds($lastResend);
                return response()->json([
                    'success' => false,
                    'message' => "لطفاً {$remainingTime} ثانیه صبر کنید"
                ], 429);
            }
            
            // Call local API to resend SMS
            $result = \Illuminate\Support\Facades\Http::timeout(30)
                ->post(config('services.local_api.url') . '/api/services/' . $localRequest['service_slug'], [
                    'resendSms' => true,
                    'hash' => $localRequest['otp_data']['hash'] ?? null,
                    'requestHash' => $hash
                ]);
            
            if ($result->successful()) {
                $responseData = $result->json();
                
                if ($responseData['status'] === 'success') {
                    // Set rate limiting cache
                    \Illuminate\Support\Facades\Cache::put($lastResendKey, now(), 120);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'پیامک مجدد ارسال شد'
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در ارسال مجدد پیامک'
            ], 500);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in resend SMS', [
                'hash' => $hash,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در ارسال مجدد پیامک'
            ], 500);
        }
    })->where(['hash' => 'req_[A-Z0-9]{16}'])
      ->middleware('throttle:5,1'); // 5 requests per minute
    
    // Restart request with original data
    Route::post('/{hash}/restart', function($hash) {
        try {
            $localRequestService = app(\App\Services\LocalRequestService::class);
            $localRequest = $localRequestService->getRequest($hash);
            
            if (!$localRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'درخواست یافت نشد'
                ], 404);
            }
            
            // Get original request data
            $originalData = $localRequest['request_data'] ?? [];
            $serviceSlug = $localRequest['service_slug'] ?? '';
            $serviceId = $localRequest['service_id'] ?? null;
            $userId = $localRequest['user_id'] ?? null;
            $sessionId = $localRequest['session_id'] ?? null;
            $estimatedDuration = $localRequest['estimated_duration'] ?? 300;
            
            if (empty($originalData) || empty($serviceSlug)) {
                return response()->json([
                    'success' => false,
                    'message' => 'اطلاعات درخواست ناقص است'
                ], 400);
            }
            
            // Cancel the old request
            $localRequestService->cancelRequest($hash);
            
            // Create a new request with the same data
            $newRequest = $localRequestService->createRequest(
                serviceSlug: $serviceSlug,
                serviceId: $serviceId,
                requestData: $originalData,
                userId: $userId,
                sessionId: $sessionId,
                estimatedDuration: $estimatedDuration
            );
            
            // Dispatch new background job
            \App\Jobs\ProcessLocalRequestJob::dispatch($newRequest['hash']);
            
            \Illuminate\Support\Facades\Log::info('Request restarted successfully', [
                'old_hash' => $hash,
                'new_hash' => $newRequest['hash'],
                'service_slug' => $serviceSlug
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'درخواست مجدد با موفقیت شروع شد',
                'new_hash' => $newRequest['hash'],
                'progress_url' => "/services/{$serviceSlug}/progress/{$newRequest['hash']}"
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error restarting request', [
                'hash' => $hash,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در راه‌اندازی مجدد درخواست'
            ], 500);
        }
    })->where(['hash' => 'req_[A-Z0-9]{16}'])
      ->middleware('throttle:3,1'); // 3 requests per minute
});


