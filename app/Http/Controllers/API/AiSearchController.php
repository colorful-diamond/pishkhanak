<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AiSearchService;
use App\Services\AiChatService;
use App\Services\FileUploadHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Exception;

class AiSearchController extends Controller
{
    protected AiSearchService $aiSearchService;
    protected AiChatService $aiChatService;
    protected FileUploadHandler $fileHandler;
    
    public function __construct(
        AiSearchService $aiSearchService
    ) {
        $this->aiSearchService = $aiSearchService;
        $this->aiChatService = app(AiChatService::class);
        $this->fileHandler = app(FileUploadHandler::class);
    }
    
    /**
     * Handle text-based search requests
     */
    public function searchText(Request $request): JsonResponse
    {
        return $this->handleSearch($request, AiSearchService::SEARCH_TYPE_TEXT);
    }
    
    /**
     * Handle voice-based search requests
     */
    public function searchVoice(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'audio_data' => 'required|string',
            'format' => 'sometimes|in:wav,mp3,ogg',
            'session_id' => 'sometimes|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'داده‌های صوتی نامعتبر است.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // For now, treat audio_data as transcribed text
        // In future, implement actual speech-to-text conversion
        $transcription = $request->input('audio_data');
        
        $modifiedRequest = $request->duplicate();
        $modifiedRequest->merge(['query' => $transcription]);
        
        return $this->handleSearch($modifiedRequest, AiSearchService::SEARCH_TYPE_VOICE);
    }
    
    /**
     * Handle image-based search requests
     */
    public function searchImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:5120', // 5MB max
            'session_id' => 'sometimes|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'تصویر نامعتبر است.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Extract text from image using OCR (placeholder implementation)
            $extractedText = $this->extractTextFromImage($request->file('image'));
            
            $modifiedRequest = $request->duplicate();
            $modifiedRequest->merge(['query' => $extractedText]);
            
            return $this->handleSearch($modifiedRequest, AiSearchService::SEARCH_TYPE_IMAGE);
            
        } catch (Exception $e) {
            Log::error('Image processing failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در پردازش تصویر. لطفاً دوباره تلاش کنید.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Handle conversational search requests with enhanced AI chat
     */
    public function searchConversational(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:2000',
            'session_id' => 'sometimes|string|max:255',
            'files' => 'sometimes|array|max:5',
            'files.*' => 'file|max:20480', // 20MB max per file
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'داده‌های ورودی نامعتبر است.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $sessionId = $request->input('session_id') ?: 'session_' . Str::random(16);
            $files = $request->file('files', []);
            
            // Process uploaded files
            $processedFiles = [];
            foreach ($files as $file) {
                $result = $this->fileHandler->processUpload($file);
                if ($result['success']) {
                    $processedFiles[] = $result['file_info'];
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'خطا در پردازش فایل: ' . $result['error']
                    ], 400);
                }
            }
            
            // Use enhanced AI chat service
            $response = $this->aiChatService->chat($request->input('query'), [
                'session_id' => $sessionId,
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'files' => $processedFiles
            ]);
            
            if (!$response['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $response['response'],
                    'error_code' => $response['error_code'] ?? 'CHAT_ERROR'
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'ai_response' => $response['response'],
                    'intent' => $response['intent'],
                    'confidence' => $response['confidence'],
                    'detected_service' => $response['detected_service'],
                    'service_confidence' => $response['service_confidence'],
                    'extracted_data' => $response['extracted_data'],
                    'suggested_services' => $response['suggested_services'],
                    'conversation_id' => $response['conversation_id'],
                    'conversation_stats' => $response['conversation_stats'],
                    'requires_input' => $response['requires_input'],
                    'next_field' => $response['next_field'],
                    'service_url' => $response['service_url'],
                    'timestamp' => $response['timestamp'],
                    'results' => [] // Empty results array for compatibility
                ],
                'meta' => [
                    'request_id' => Str::uuid(),
                    'processing_time' => round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2),
                    'cached' => false
                ]
            ]);
            
        } catch (Exception $e) {
            Log::error('Conversational search failed: ' . $e->getMessage(), [
                'query' => $request->input('query'),
                'session_id' => $request->input('session_id'),
                'user_id' => Auth::id(),
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'متاسفانه خطایی رخ داده است. لطفاً دوباره تلاش کنید.'
            ], 500);
        }
    }
    
    /**
     * Serve uploaded files securely
     */
    public function serveFile(Request $request, string $token)
    {
        $fileInfo = $this->fileHandler->getFileByToken($token);
        
        if (!$fileInfo) {
            return response()->json([
                'success' => false,
                'message' => 'فایل یافت نشد یا منقضی شده است.'
            ], 404);
        }
        
        return response()->file($fileInfo['full_path'], [
            'Content-Type' => $fileInfo['mime_type'],
            'Content-Length' => $fileInfo['size']
        ]);
    }
    
    /**
     * Main search handler method
     */
    protected function handleSearch(Request $request, string $searchType): JsonResponse
    {
        try {
            // Validate common parameters
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|max:1000',
                'session_id' => 'sometimes|string|max:255',
                'force_refresh' => 'sometimes|boolean'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'درخواست نامعتبر است.',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Check rate limiting
            if (!$this->checkRateLimit($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً کمی صبر کنید.',
                    'code' => 'RATE_LIMITED',
                    'retry_after' => $this->getRateLimitRetryAfter($request)
                ], 429);
            }
            
            // Prepare search options
            $options = [
                'type' => $searchType,
                'user_id' => Auth::id(),
                'session_id' => $request->input('session_id', Str::uuid()),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'force_refresh' => $request->boolean('force_refresh', false)
            ];
            
            // Perform search
            $result = $this->aiSearchService->search($request->input('query'), $options);
            
            // Track rate limiting
            $this->incrementRateLimit($request);
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'meta' => [
                    'search_type' => $searchType,
                    'session_id' => $options['session_id'],
                    'cached' => $result['cached'] ?? false,
                    'timestamp' => now()->toISOString()
                ]
            ]);
            
        } catch (Exception $e) {
            Log::error('Search request failed: ' . $e->getMessage(), [
                'query' => $request->input('query'),
                'type' => $searchType,
                'user_id' => Auth::id(),
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در انجام جستجو. لطفاً دوباره تلاش کنید.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    
    /**
     * Get search suggestions
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        try {
            $query = $request->input('query', '');
            $suggestions = $this->aiSearchService->getSearchSuggestions($query);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'suggestions' => $suggestions,
                    'query' => $query
                ]
            ]);
            
        } catch (Exception $e) {
            Log::error('Failed to get suggestions: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت پیشنهادات.',
                'data' => [
                    'suggestions' => [],
                    'query' => $request->input('query', '')
                ]
            ]);
        }
    }
    
    /**
     * Get auto-typing examples
     */
    public function getAutoTypingExamples(): JsonResponse
    {
        try {
            $examples = $this->aiSearchService->getAutoTypingExamples();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'examples' => $examples
                ]
            ]);
            
        } catch (Exception $e) {
            Log::error('Failed to get auto-typing examples: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت نمونه‌ها.',
                'data' => [
                    'examples' => [
                        'استعلام خلافی خودرو...',
                        'محاسبه شماره شبا...',
                        'استعلام وضعیت چک...'
                    ]
                ]
            ]);
        }
    }
    
    /**
     * Get conversation history
     */
    public function getConversationHistory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'شناسه جلسه نامعتبر است.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // This would be implemented based on your conversation storage strategy
            $history = []; // Placeholder
            
            return response()->json([
                'success' => true,
                'data' => [
                    'history' => $history,
                    'session_id' => $request->input('session_id')
                ]
            ]);
            
        } catch (Exception $e) {
            Log::error('Failed to get conversation history: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت تاریخچه مکالمه.'
            ]);
        }
    }
    
    /**
     * Clear conversation history
     */
    public function clearConversationHistory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'شناسه جلسه نامعتبر است.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Clear conversation context from cache
            $sessionId = $request->input('session_id');
            $contextKey = "ai_search:conversation:{$sessionId}";
            cache()->forget($contextKey);
            
            return response()->json([
                'success' => true,
                'message' => 'تاریخچه مکالمه پاک شد.',
                'data' => [
                    'session_id' => $sessionId
                ]
            ]);
            
        } catch (Exception $e) {
            Log::error('Failed to clear conversation history: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در پاک کردن تاریخچه مکالمه.'
            ]);
        }
    }
    
    /**
     * Get search analytics (for authenticated users)
     */
    public function getSearchAnalytics(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'برای دسترسی به آمار جستجو باید وارد شوید.'
            ], 401);
        }
        
        try {
            // Placeholder for search analytics
            $analytics = [
                'total_searches' => 0,
                'popular_queries' => [],
                'recent_searches' => [],
                'success_rate' => 0.95
            ];
            
            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);
            
        } catch (Exception $e) {
            Log::error('Failed to get search analytics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت آمار جستجو.'
            ]);
        }
    }
    
    /**
     * Extract text from uploaded image (placeholder implementation)
     */
    protected function extractTextFromImage($imageFile): string
    {
        // Placeholder implementation
        // In production, integrate with OCR service like Google Vision API or Tesseract
        
        // For now, return a sample extracted text
        return "استعلام خلافی خودرو";
    }
    
    /**
     * Check rate limiting for current user/IP
     */
    protected function checkRateLimit(Request $request): bool
    {
        $key = $this->getRateLimitKey($request);
        $maxAttempts = Auth::check() ? 100 : 50; // Higher limit for authenticated users
        $perMinutes = 60; // 1 hour window
        
        return !RateLimiter::tooManyAttempts($key, $maxAttempts);
    }
    
    /**
     * Increment rate limit counter
     */
    protected function incrementRateLimit(Request $request): void
    {
        $key = $this->getRateLimitKey($request);
        $perMinutes = 60; // 1 hour window
        
        RateLimiter::hit($key, $perMinutes * 60);
    }
    
    /**
     * Get rate limit retry after seconds
     */
    protected function getRateLimitRetryAfter(Request $request): int
    {
        $key = $this->getRateLimitKey($request);
        return RateLimiter::availableIn($key);
    }
    
    /**
     * Generate rate limit key for user/IP
     */
    protected function getRateLimitKey(Request $request): string
    {
        $userId = Auth::id();
        $ip = $request->ip();
        
        return $userId ? "ai_search_user_{$userId}" : "ai_search_ip_{$ip}";
    }
} 