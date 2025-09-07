<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Post;
use App\Models\Page;
use App\Models\AiSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Exception;

class AiSearchService
{
    protected GeminiService $geminiService;
    protected ?AiSetting $aiSetting;
    
    // Rate limiting constants
    private const RATE_LIMIT_PER_USER_HOURLY = 50;
    private const RATE_LIMIT_PER_IP_HOURLY = 30;
    private const RATE_LIMIT_PER_USER_DAILY = 200;
    private const RATE_LIMIT_ANONYMOUS_DAILY = 100;
    
    // Cost control constants
    private const MAX_TOKENS_PER_REQUEST = 1000;
    private const MAX_DAILY_TOKEN_USAGE = 50000;
    private const CACHE_TTL_SEARCH = 3600; // 1 hour
    private const CACHE_TTL_CONVERSATIONS = 1800; // 30 minutes
    
    // Search types
    public const SEARCH_TYPE_TEXT = 'text';
    public const SEARCH_TYPE_VOICE = 'voice';
    public const SEARCH_TYPE_IMAGE = 'image';
    public const SEARCH_TYPE_CONVERSATIONAL = 'conversational';
    
    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
        $this->aiSetting = AiSetting::where('is_active', true)->first();
    }
    
    /**
     * Main search method that handles all types of searches
     */
    public function search(string $query, array $options = []): array
    {
        $searchType = $options['type'] ?? self::SEARCH_TYPE_TEXT;
        $userId = $options['user_id'] ?? null;
        $sessionId = $options['session_id'] ?? null;
        $ipAddress = $options['ip_address'] ?? null;
        
        try {
            // Check rate limits
            if (!$this->checkRateLimit($userId, $ipAddress)) {
                throw new Exception('Rate limit exceeded. Please try again later.');
            }
            
            // Check daily token usage
            if (!$this->checkTokenUsage()) {
                throw new Exception('Daily token limit exceeded. Please try again tomorrow.');
            }
            
            // Get cached results if available
            $cacheKey = $this->getCacheKey($query, $searchType, $userId);
            $cachedResult = Cache::get($cacheKey);
            
            if ($cachedResult && !($options['force_refresh'] ?? false)) {
                Log::info('Returning cached search result', ['query' => $query, 'type' => $searchType]);
                return $cachedResult;
            }
            
            // Perform search based on type
            $result = match ($searchType) {
                self::SEARCH_TYPE_TEXT => $this->performTextSearch($query, $options),
                self::SEARCH_TYPE_VOICE => $this->performVoiceSearch($query, $options),
                self::SEARCH_TYPE_IMAGE => $this->performImageSearch($query, $options),
                self::SEARCH_TYPE_CONVERSATIONAL => $this->performConversationalSearch($query, $options),
                default => $this->performTextSearch($query, $options)
            };
            
            // Cache the result
            Cache::put($cacheKey, $result, self::CACHE_TTL_SEARCH);
            
            // Log search for analytics
            $this->logSearch($query, $searchType, $userId, $ipAddress, count($result['results'] ?? []));
            
            return $result;
            
        } catch (Exception $e) {
            Log::error('AI Search error: ' . $e->getMessage(), [
                'query' => $query,
                'type' => $searchType,
                'user_id' => $userId,
                'options' => $options
            ]);
            
            // Return fallback results
            return $this->getFallbackResults($query);
        }
    }
    
    /**
     * Perform text-based search with AI enhancement
     */
    protected function performTextSearch(string $query, array $options = []): array
    {
        // First, perform traditional search
        $traditionalResults = $this->performTraditionalSearch($query);
        
        // Then enhance with AI analysis
        $aiAnalysis = $this->analyzeQueryWithAI($query, $options);
        
        // Combine and rank results
        $combinedResults = $this->combineAndRankResults($traditionalResults, $aiAnalysis);
        
        return [
            'query' => $query,
            'type' => self::SEARCH_TYPE_TEXT,
            'results' => $combinedResults,
            'ai_response' => $aiAnalysis['response'] ?? null,
            'suggestions' => $aiAnalysis['suggestions'] ?? [],
            'intent' => $aiAnalysis['intent'] ?? 'general',
            'confidence' => $aiAnalysis['confidence'] ?? 0.5,
            'cached' => false,
            'timestamp' => now()->toISOString()
        ];
    }
    
    /**
     * Perform voice search (transcription + text search)
     */
    protected function performVoiceSearch(string $audioData, array $options = []): array
    {
        // For now, assume audio is already transcribed
        // In future, integrate with speech-to-text service
        $transcription = $audioData; // Placeholder
        
        return $this->performTextSearch($transcription, array_merge($options, ['original_type' => 'voice']));
    }
    
    /**
     * Perform image search (OCR + analysis)
     */
    protected function performImageSearch(string $imageData, array $options = []): array
    {
        // For now, return placeholder - implement OCR service later
        $extractedText = "استعلام خلافی خودرو"; // Placeholder
        
        return $this->performTextSearch($extractedText, array_merge($options, ['original_type' => 'image']));
    }
    
    /**
     * Perform conversational search with context
     */
    protected function performConversationalSearch(string $query, array $options = []): array
    {
        $sessionId = $options['session_id'] ?? null;
        $context = $this->getConversationContext($sessionId);
        
        // Enhance query with context
        $contextualQuery = $this->enhanceQueryWithContext($query, $context);
        
        // Perform search
        $results = $this->performTextSearch($contextualQuery, $options);
        
        // Update conversation context
        $this->updateConversationContext($sessionId, $query, $results);
        
        return array_merge($results, [
            'type' => self::SEARCH_TYPE_CONVERSATIONAL,
            'context_used' => !empty($context),
            'conversation_id' => $sessionId
        ]);
    }
    
    /**
     * Analyze query with AI to understand intent and enhance results
     */
    protected function analyzeQueryWithAI(string $query, array $options = []): array
    {
        if (!$this->aiSetting) {
            return $this->getFallbackAiAnalysis($query);
        }
        
        $systemPrompt = $this->buildSystemPrompt();
        $userPrompt = $this->buildUserPrompt($query, $options);
        
        // Get conversation context if session_id is provided
        $sessionId = $options['session_id'] ?? null;
        $context = $this->getConversationContext($sessionId);
        
        try {
            // Build messages with conversation history
            $messages = $this->buildConversationMessages(
                $userPrompt, 
                $context, 
                $systemPrompt
            );
            
            $response = $this->geminiService->chatCompletion(
                $messages,
                'google/gemini-2.5-flash',
                [
                    'temperature' => $this->aiSetting->temperature ?? 0.3,
                    'max_tokens' => min(self::MAX_TOKENS_PER_REQUEST, $this->aiSetting->max_tokens ?? 500),
                    'json' => true
                ]
            );
            
            $aiResult = json_decode($response, true);
            
            if (!$aiResult) {
                throw new Exception('Invalid AI response format');
            }
            
            // Update token usage
            $this->updateTokenUsage(strlen($response));
            
            return $aiResult;
            
        } catch (Exception $e) {
            Log::error('AI analysis failed: ' . $e->getMessage(), ['query' => $query]);
            return $this->getFallbackAiAnalysis($query);
        }
    }
    
    /**
     * Perform traditional database search
     */
    protected function performTraditionalSearch(string $query): array
    {
        $results = [];
        
        // Search services
        $services = Service::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('keywords', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();
            
        foreach ($services as $service) {
            $results[] = [
                'type' => 'service',
                'id' => $service->id,
                'title' => $service->title,
                'description' => $service->description,
                'url' => $service->getUrl(),
                'thumbnail' => $service->thumbnail_url,
                'category' => $service->category?->title,
                'score' => $this->calculateRelevanceScore($query, $service->title . ' ' . $service->description),
                'is_popular' => $service->is_popular ?? false
            ];
        }
        
        // Search blog posts
        $posts = Post::where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%")
                  ->orWhere('summary', 'LIKE', "%{$query}%");
            })
            ->limit(5)
            ->get();
            
        foreach ($posts as $post) {
            $results[] = [
                'type' => 'blog',
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->summary ?? Str::limit(strip_tags($post->content), 150),
                'url' => route('app.blog.show', $post),
                'thumbnail' => $post->thumbnail,
                'category' => $post->category?->name,
                'score' => $this->calculateRelevanceScore($query, $post->title . ' ' . $post->content),
                'published_at' => $post->published_at?->toISOString()
            ];
        }
        
        return $results;
    }
    
    /**
     * Combine traditional and AI results with intelligent ranking
     */
    protected function combineAndRankResults(array $traditionalResults, array $aiAnalysis): array
    {
        $results = collect($traditionalResults);
        
        // Apply AI confidence scoring
        if (isset($aiAnalysis['intent']) && isset($aiAnalysis['confidence'])) {
            $intent = $aiAnalysis['intent'];
            $confidence = $aiAnalysis['confidence'];
            
            $results = $results->map(function ($result) use ($intent, $confidence) {
                // Boost score based on AI intent analysis
                if ($intent === 'service_search' && $result['type'] === 'service') {
                    $result['score'] *= 1.5;
                } elseif ($intent === 'information_search' && $result['type'] === 'blog') {
                    $result['score'] *= 1.3;
                }
                
                // Apply confidence factor
                $result['score'] *= (0.5 + ($confidence * 0.5));
                
                return $result;
            });
        }
        
        // Sort by score and return top results
        return $results->sortByDesc('score')->take(10)->values()->toArray();
    }
    
    /**
     * Build system prompt for AI analysis
     */
    protected function buildSystemPrompt(): string
    {
        // Get all active services dynamically with slugs
        $services = Service::where('is_active', true)
            ->select('title', 'slug', 'summary')
            ->get();
            
        $servicesList = $services->map(function($service) {
            return "- {$service->title} (slug: {$service->slug})" . 
                   ($service->summary ? " - {$service->summary}" : "");
        })->implode("\n");
        
        return "شما دستیار هوشمند پیشخوانک هستید که خدمات مختلف بانکی، مالی و اداری ارائه می‌دهد.

خدمات اصلی شامل:
{$servicesList}

وظایف شما:
1. تحلیل دقیق قصد کاربر
2. ارائه پاسخ‌های مفید و غیرتکراری (بدون استفاده از کلمات مثل 'سلام')
3. شناسایی سرویس مناسب و جمع‌آوری اطلاعات مورد نیاز
4. راهنمایی کاربر به خدمات مرتبط
5. پاسخ به سوالات عمومی در حد توان

قوانین مهم:
- مستقیماً به موضوع بپردازید
- از تکرار کلمات تشریفاتی خودداری کنید
- فقط به سوالات مرتبط با خدمات یا سوالات عمومی پاسخ دهید
- از تولید محتوا یا انجام کارهای خارج از حوزه خودداری کنید

پاسخ را به صورت JSON با ساختار زیر ارائه دهید:
{
  \"intent\": \"نوع قصد کاربر\",
  \"confidence\": \"میزان اطمینان از 0 تا 1\",
  \"response\": \"پاسخ مفید و مناسب\",
  \"suggestions\": [\"پیشنهادات برای جستجوی بیشتر\"],
  \"related_services\": [\"خدمات مرتبط\"],
  \"selected_service\": \"slug سرویس انتخاب شده در صورت وجود\"
}";
    }
    
    /**
     * Build user prompt for AI analysis
     */
    protected function buildUserPrompt(string $query, array $options = []): string
    {
        $prompt = "کاربر جستجو کرده: \"{$query}\"";
        
        if (isset($options['original_type'])) {
            $prompt .= "\nنوع جستجوی اصلی: {$options['original_type']}";
        }
        
        if (isset($options['user_context'])) {
            $prompt .= "\nزمینه کاربر: {$options['user_context']}";
        }
        
        return $prompt;
    }
    
    /**
     * Calculate relevance score for search results
     */
    protected function calculateRelevanceScore(string $query, string $content): float
    {
        $queryWords = explode(' ', mb_strtolower($query));
        $contentWords = explode(' ', mb_strtolower($content));
        
        $matches = 0;
        foreach ($queryWords as $word) {
            if (in_array($word, $contentWords)) {
                $matches++;
            }
        }
        
        return count($queryWords) > 0 ? $matches / count($queryWords) : 0;
    }
    
    /**
     * Check rate limits for user/IP
     */
    protected function checkRateLimit(?string $userId, ?string $ipAddress): bool
    {
        $now = now();
        $hourKey = $now->format('Y-m-d-H');
        $dayKey = $now->format('Y-m-d');
        
        if ($userId) {
            // Check user hourly limit
            $userHourlyKey = "ai_search:user:{$userId}:hour:{$hourKey}";
            $userHourlyCount = Cache::get($userHourlyKey, 0);
            
            if ($userHourlyCount >= self::RATE_LIMIT_PER_USER_HOURLY) {
                return false;
            }
            
            // Check user daily limit
            $userDailyKey = "ai_search:user:{$userId}:day:{$dayKey}";
            $userDailyCount = Cache::get($userDailyKey, 0);
            
            if ($userDailyCount >= self::RATE_LIMIT_PER_USER_DAILY) {
                return false;
            }
            
            // Increment counters
            Cache::put($userHourlyKey, $userHourlyCount + 1, 3600);
            Cache::put($userDailyKey, $userDailyCount + 1, 86400);
            
        } else {
            // Check IP-based limits for anonymous users
            $ipHourlyKey = "ai_search:ip:{$ipAddress}:hour:{$hourKey}";
            $ipHourlyCount = Cache::get($ipHourlyKey, 0);
            
            if ($ipHourlyCount >= self::RATE_LIMIT_PER_IP_HOURLY) {
                return false;
            }
            
            $ipDailyKey = "ai_search:ip:{$ipAddress}:day:{$dayKey}";
            $ipDailyCount = Cache::get($ipDailyKey, 0);
            
            if ($ipDailyCount >= self::RATE_LIMIT_ANONYMOUS_DAILY) {
                return false;
            }
            
            // Increment counters
            Cache::put($ipHourlyKey, $ipHourlyCount + 1, 3600);
            Cache::put($ipDailyKey, $ipDailyCount + 1, 86400);
        }
        
        return true;
    }
    
    /**
     * Check daily token usage for cost control
     */
    protected function checkTokenUsage(): bool
    {
        $dayKey = now()->format('Y-m-d');
        $tokenUsageKey = "ai_search:tokens:day:{$dayKey}";
        $dailyUsage = Cache::get($tokenUsageKey, 0);
        
        return $dailyUsage < self::MAX_DAILY_TOKEN_USAGE;
    }
    
    /**
     * Update token usage counter
     */
    protected function updateTokenUsage(int $tokens): void
    {
        $dayKey = now()->format('Y-m-d');
        $tokenUsageKey = "ai_search:tokens:day:{$dayKey}";
        $currentUsage = Cache::get($tokenUsageKey, 0);
        
        Cache::put($tokenUsageKey, $currentUsage + $tokens, 86400);
    }
    
    /**
     * Get conversation context for conversational search
     */
    protected function getConversationContext(?string $sessionId): array
    {
        if (!$sessionId) {
            return [];
        }
        
        $contextKey = "ai_search:conversation:{$sessionId}";
        return Cache::get($contextKey, []);
    }
    
    /**
     * Update conversation context
     */
    protected function updateConversationContext(?string $sessionId, string $query, array $results): void
    {
        if (!$sessionId) {
            return;
        }
        
        $contextKey = "ai_search:conversation:{$sessionId}";
        $context = $this->getConversationContext($sessionId);
        
        $context[] = [
            'query' => $query,
            'timestamp' => now()->toISOString(),
            'results_count' => count($results['results'] ?? []),
            'intent' => $results['intent'] ?? 'unknown'
        ];
        
        // Keep only last 5 interactions
        $context = array_slice($context, -5);
        
        Cache::put($contextKey, $context, self::CACHE_TTL_CONVERSATIONS);
    }
    
    /**
     * Enhance query with conversation context
     */
    protected function enhanceQueryWithContext(string $query, array $context): string
    {
        if (empty($context)) {
            return $query;
        }
        
        // Simple context enhancement - can be improved with AI
        $recentQueries = collect($context)->pluck('query')->join(' ');
        return $query . ' (زمینه: ' . $recentQueries . ')';
    }
    
    /**
     * Get cache key for search results
     */
    protected function getCacheKey(string $query, string $type, ?string $userId): string
    {
        $key = 'ai_search:' . md5($query . $type . ($userId ?? 'anonymous'));
        return $key;
    }
    
    /**
     * Log search for analytics
     */
    protected function logSearch(string $query, string $type, ?string $userId, ?string $ipAddress, int $resultsCount): void
    {
        try {
            DB::table('ai_search_logs')->insert([
                'query' => $query,
                'type' => $type,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'results_count' => $resultsCount,
                            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
            ]);
        } catch (Exception $e) {
            Log::error('Failed to log search: ' . $e->getMessage());
        }
    }
    
    /**
     * Get fallback results when AI fails
     */
    protected function getFallbackResults(string $query): array
    {
        $traditionalResults = $this->performTraditionalSearch($query);
        
        return [
            'query' => $query,
            'type' => 'fallback',
            'results' => $traditionalResults,
            'ai_response' => 'متاسفانه در حال حاضر امکان تحلیل هوشمند وجود ندارد. نتایج جستجوی معمولی نمایش داده شده است.',
            'suggestions' => [
                'لطفاً جستجوی خود را ساده‌تر کنید',
                'از کلمات کلیدی استفاده کنید',
                'بعداً دوباره تلاش کنید'
            ],
            'intent' => 'unknown',
            'confidence' => 0.3,
            'cached' => false,
            'timestamp' => now()->toISOString()
        ];
    }
    
    /**
     * Get fallback AI analysis when AI service fails
     */
    protected function getFallbackAiAnalysis(string $query): array
    {
        // Simple keyword-based analysis
        $intent = 'general';
        $confidence = 0.3;
        $response = 'در حال پردازش درخواست شما...';
        $suggestions = ['جستجوی دقیق‌تر', 'استفاده از کلمات کلیدی'];
        $recommendedServices = [];
        
        // Basic keyword detection
        if (strpos($query, 'خلافی') !== false || strpos($query, 'ترافیک') !== false) {
            $intent = 'traffic_violation';
            $confidence = 0.8;
            $response = 'به نظر می‌رسد به دنبال استعلام خلافی خودرو هستید.';
            $recommendedServices = ['استعلام خلافی خودرو'];
        } elseif (strpos($query, 'شبا') !== false || strpos($query, 'حساب') !== false) {
            $intent = 'iban_service';
            $confidence = 0.8;
            $response = 'خدمات مربوط به شماره شبا و حساب بانکی را پیدا کردم.';
            $recommendedServices = ['محاسبه شبا', 'اعتبارسنجی شبا'];
        } elseif (strpos($query, 'چک') !== false) {
            $intent = 'check_service';
            $confidence = 0.8;
            $response = 'خدمات مربوط به چک را برای شما یافتم.';
            $recommendedServices = ['استعلام وضعیت چک'];
        }
        
        return [
            'intent' => $intent,
            'confidence' => $confidence,
            'response' => $response,
            'suggestions' => $suggestions,
            'recommended_services' => $recommendedServices
        ];
    }
    
    /**
     * Get auto-typing examples for search suggestions
     */
    public function getAutoTypingExamples(): array
    {
        return [
            'استعلام خلافی خودرو با پلاک...',
            'محاسبه شماره شبا از حساب...',
            'چک کردن وضعیت چک صیادی...',
            'اعتبارسنجی کارت بانکی...',
            'پیدا کردن کدپستی...',
            'استعلام مالیات...',
            'بررسی سابقه بانکی...',
            'محاسبه سود بانکی...',
            'استعلام بیمه خودرو...',
            'تبدیل ارز...'
        ];
    }
    
    /**
     * Get search suggestions based on popular queries
     */
    public function getSearchSuggestions(string $query = ''): array
    {
        $suggestions = [
            'استعلام خلافی خودرو',
            'محاسبه شبا',
            'استعلام چک صیادی',
            'اعتبارسنجی کارت',
            'کدپستی',
            'استعلام مالیات',
            'بیمه خودرو',
            'تبدیل ارز'
        ];
        
        if (!empty($query)) {
            // Filter suggestions based on query
            $suggestions = array_filter($suggestions, function ($suggestion) use ($query) {
                return strpos(mb_strtolower($suggestion), mb_strtolower($query)) !== false;
            });
        }
        
        return array_values($suggestions);
    }

    /**
     * Build conversation messages with history for AI analysis
     */
    protected function buildConversationMessages(string $query, array $context, string $systemPrompt = ''): array
    {
        $messages = [];
        
        // Add system prompt
        if (!empty($systemPrompt)) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }
        
        // Add conversation context if available
        if (!empty($context)) {
            foreach ($context as $contextItem) {
                if (isset($contextItem['query'])) {
                    $messages[] = ['role' => 'user', 'content' => $contextItem['query']];
                    
                    // Add a simple AI response context if we have result info
                    if (isset($contextItem['results_count'])) {
                        $messages[] = ['role' => 'assistant', 'content' => "یافتم {$contextItem['results_count']} نتیجه مرتبط."];
                    }
                }
            }
        }
        
        // Add current query
        $messages[] = ['role' => 'user', 'content' => $query];
        
        return $messages;
    }
} 