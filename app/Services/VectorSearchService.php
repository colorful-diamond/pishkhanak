<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Post;
use App\Models\ServiceCategory;
use App\Models\ServiceEmbedding;
use App\Models\PostEmbedding;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Exception;

class VectorSearchService
{
    protected GeminiService $geminiService;
    
    // Cache settings
    private const CACHE_TTL_EMBEDDINGS = 86400; // 24 hours
    private const CACHE_TTL_SEARCH_RESULTS = 3600; // 1 hour
    
    // Similarity thresholds
    private const SIMILARITY_THRESHOLD = 0.6;
    private const HIGH_SIMILARITY_THRESHOLD = 0.8;
    
    // Vector dimensions (Gemini embedding size)
    private const VECTOR_DIMENSIONS = 768;
    
    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }
    
    /**
     * Perform semantic search using vector embeddings
     */
    public function semanticSearch(string $query, array $options = []): array
    {
        try {
            $limit = $options['limit'] ?? 10;
            $types = $options['types'] ?? ['services', 'posts'];
            $minSimilarity = $options['min_similarity'] ?? self::SIMILARITY_THRESHOLD;
            
            // Get query embedding
            $queryEmbedding = $this->getTextEmbedding($query);
            
            if (!$queryEmbedding) {
                return $this->getFallbackSearch($query, $options);
            }
            
            $allResults = collect();
            
            // Search in services
            if (in_array('services', $types)) {
                $serviceResults = $this->searchServices($queryEmbedding, $minSimilarity);
                $allResults = $allResults->merge($serviceResults);
            }
            
            // Search in blog posts
            if (in_array('posts', $types)) {
                $postResults = $this->searchPosts($queryEmbedding, $minSimilarity);
                $allResults = $allResults->merge($postResults);
            }
            
            // Sort by similarity and return top results
            $results = $allResults
                ->sortByDesc('similarity')
                ->take($limit)
                ->values()
                ->toArray();
            
            return [
                'results' => $results,
                'query' => $query,
                'method' => 'vector_search',
                'total_found' => count($results),
                'min_similarity' => $minSimilarity
            ];
            
        } catch (Exception $e) {
            Log::error('Vector search failed: ' . $e->getMessage(), ['query' => $query]);
            return $this->getFallbackSearch($query, $options);
        }
    }
    
    /**
     * Search services using PostgreSQL vector similarity
     */
    protected function searchServices(array $queryEmbedding, float $minSimilarity): Collection
    {
        try {
            // Use PostgreSQL vector search for fast similarity matching
            $similarServices = ServiceEmbedding::findSimilar($queryEmbedding, $minSimilarity, 20);
            
            $results = collect();
            
            foreach ($similarServices as $result) {
                $results->push([
                    'type' => 'service',
                    'id' => $result->service_id,
                    'title' => $result->title,
                    'description' => $result->description,
                    'url' => route('app.services.show', $result->slug),
                    'thumbnail' => $result->thumbnail_url,
                    'category' => $result->category_title,
                    'similarity' => round($result->similarity, 4),
                    'confidence' => $this->getSimilarityConfidence($result->similarity),
                    'keywords' => [], // Could be added to metadata
                    'is_popular' => (bool) $result->is_popular,
                    'match_quality' => $this->getMatchQuality($result->similarity),
                    'search_method' => 'postgresql_vector'
                ]);
            }
            
            return $results;
            
        } catch (Exception $e) {
            Log::error('PostgreSQL vector search failed for services: ' . $e->getMessage());
            
            // Fallback to traditional search if vector search fails
            return $this->searchServicesTraditional($queryEmbedding, $minSimilarity);
        }
    }
    
    /**
     * Search posts using PostgreSQL vector similarity
     */
    protected function searchPosts(array $queryEmbedding, float $minSimilarity): Collection
    {
        try {
            // Use PostgreSQL vector search for fast similarity matching
            $similarPosts = PostEmbedding::findSimilar($queryEmbedding, $minSimilarity, 10);
            
            $results = collect();
            
            foreach ($similarPosts as $result) {
                $results->push([
                    'type' => 'post',
                    'id' => $result->post_id,
                    'title' => $result->title,
                    'description' => $result->excerpt ?? $this->generateExcerpt($result->content ?? ''),
                    'url' => route('app.blog.show', $result->slug),
                    'thumbnail' => $result->featured_image_url,
                    'category' => $result->category_title,
                    'similarity' => round($result->similarity, 4),
                    'confidence' => $this->getSimilarityConfidence($result->similarity),
                    'published_at' => $result->published_at,
                    'match_quality' => $this->getMatchQuality($result->similarity),
                    'search_method' => 'postgresql_vector'
                ]);
            }
            
            return $results;
            
        } catch (Exception $e) {
            Log::error('PostgreSQL vector search failed for posts: ' . $e->getMessage());
            
            // Fallback to traditional search if vector search fails
            return $this->searchPostsTraditional($queryEmbedding, $minSimilarity);
        }
    }
    
    /**
     * Fallback traditional search for services when vector search fails
     */
    protected function searchServicesTraditional(array $queryEmbedding, float $minSimilarity): Collection
    {
        $results = collect();
        
        $services = Service::where('is_active', true)->get();
        
        foreach ($services as $service) {
            $serviceText = $this->prepareServiceText($service);
            $serviceEmbedding = $this->getTextEmbedding($serviceText, true);
            
            if ($serviceEmbedding) {
                $similarity = $this->calculateCosineSimilarity($queryEmbedding, $serviceEmbedding);
                
                if ($similarity >= $minSimilarity) {
                    $results->push([
                        'type' => 'service',
                        'id' => $service->id,
                        'title' => $service->title,
                        'description' => $service->description,
                        'url' => $service->getUrl(),
                        'thumbnail' => $service->thumbnail_url,
                        'category' => $service->category?->title,
                        'similarity' => round($similarity, 4),
                        'confidence' => $this->getSimilarityConfidence($similarity),
                        'keywords' => $service->keywords ? explode(',', $service->keywords) : [],
                        'is_popular' => $service->is_popular ?? false,
                        'match_quality' => $this->getMatchQuality($similarity),
                        'search_method' => 'fallback_memory'
                    ]);
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Fallback traditional search for posts when vector search fails
     */
    protected function searchPostsTraditional(array $queryEmbedding, float $minSimilarity): Collection
    {
        $results = collect();
        
        $posts = Post::where('is_published', true)->get();
        
        foreach ($posts as $post) {
            $postText = $this->preparePostText($post);
            $postEmbedding = $this->getTextEmbedding($postText, true);
            
            if ($postEmbedding) {
                $similarity = $this->calculateCosineSimilarity($queryEmbedding, $postEmbedding);
                
                if ($similarity >= $minSimilarity) {
                    $results->push([
                        'type' => 'post',
                        'id' => $post->id,
                        'title' => $post->title,
                        'description' => $post->excerpt ?? $this->generateExcerpt($post->content),
                        'url' => route('app.blog.show', $post),
                        'thumbnail' => $post->featured_image_url,
                        'category' => $post->category?->title,
                        'similarity' => round($similarity, 4),
                        'confidence' => $this->getSimilarityConfidence($similarity),
                        'published_at' => $post->published_at?->toISOString(),
                        'match_quality' => $this->getMatchQuality($similarity),
                        'search_method' => 'fallback_memory'
                    ]);
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Get text embedding using Gemini API
     */
    protected function getTextEmbedding(string $text, bool $useCache = true): ?array
    {
        if (empty(trim($text))) {
            return null;
        }
        
        $cacheKey = 'embedding:' . md5($text);
        
        if ($useCache) {
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return $cached;
            }
        }
        
        try {
            // Use Gemini's embedding endpoint
            $response = $this->geminiService->generateEmbedding($text);
            
            if ($response && is_array($response)) {
                if ($useCache) {
                    Cache::put($cacheKey, $response, self::CACHE_TTL_EMBEDDINGS);
                }
                return $response;
            }
            
            return null;
            
        } catch (Exception $e) {
            Log::error('Failed to generate embedding: ' . $e->getMessage(), ['text' => substr($text, 0, 100)]);
            return null;
        }
    }
    
    /**
     * Calculate cosine similarity between two vectors
     */
    protected function calculateCosineSimilarity(array $vector1, array $vector2): float
    {
        if (count($vector1) !== count($vector2)) {
            return 0.0;
        }
        
        $dotProduct = 0.0;
        $magnitude1 = 0.0;
        $magnitude2 = 0.0;
        
        for ($i = 0; $i < count($vector1); $i++) {
            $dotProduct += $vector1[$i] * $vector2[$i];
            $magnitude1 += $vector1[$i] * $vector1[$i];
            $magnitude2 += $vector2[$i] * $vector2[$i];
        }
        
        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);
        
        if ($magnitude1 == 0.0 || $magnitude2 == 0.0) {
            return 0.0;
        }
        
        return $dotProduct / ($magnitude1 * $magnitude2);
    }
    
    /**
     * Prepare service text for embedding
     */
    protected function prepareServiceText(Service $service): string
    {
        $text = $service->title . ' ';
        $text .= $service->description . ' ';
        
        if ($service->keywords) {
            $text .= $service->keywords . ' ';
        }
        
        if ($service->category) {
            $text .= $service->category->title . ' ';
        }
        
        // Add common search terms based on service type
        $text .= $this->getServiceSearchTerms($service);
        
        return trim($text);
    }
    
    /**
     * Prepare post text for embedding
     */
    protected function preparePostText(Post $post): string
    {
        $text = $post->title . ' ';
        
        if ($post->excerpt) {
            $text .= $post->excerpt . ' ';
        } else {
            $text .= $this->generateExcerpt($post->content) . ' ';
        }
        
        if ($post->category) {
            $text .= $post->category->title . ' ';
        }
        
        // Add tags if available
        if (method_exists($post, 'tags')) {
            $tags = $post->tags()->pluck('name')->join(' ');
            $text .= $tags . ' ';
        }
        
        return trim($text);
    }
    
    /**
     * Get additional search terms for services
     */
    protected function getServiceSearchTerms(Service $service): string
    {
        $terms = [];
        
        // Add Persian synonyms and related terms based on service slug or title
        $slug = $service->slug;
        $title = mb_strtolower($service->title);
        
        if (strpos($slug, 'iban') !== false || strpos($title, 'شبا') !== false) {
            $terms[] = 'شماره شبا محاسبه تبدیل حساب بانکی';
        }
        
        if (strpos($slug, 'check') !== false || strpos($title, 'چک') !== false) {
            $terms[] = 'چک صیادی استعلام وضعیت بانک چک برگشتی';
        }
        
        if (strpos($slug, 'traffic') !== false || strpos($title, 'خلافی') !== false) {
            $terms[] = 'خلافی خودرو ترافیک پلاک جریمه رانندگی';
        }
        
        if (strpos($slug, 'card') !== false || strpos($title, 'کارت') !== false) {
            $terms[] = 'کارت بانکی اعتبارسنجی شماره کارت';
        }
        
        if (strpos($slug, 'postal') !== false || strpos($title, 'پستی') !== false) {
            $terms[] = 'کدپستی آدرس پست محله';
        }
        
        return implode(' ', $terms);
    }
    
    /**
     * Generate excerpt from content
     */
    protected function generateExcerpt(string $content, int $length = 200): string
    {
        $text = strip_tags($content);
        $text = preg_replace('/\s+/', ' ', $text);
        
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        
        return mb_substr($text, 0, $length) . '...';
    }
    
    /**
     * Get confidence level based on similarity score
     */
    protected function getSimilarityConfidence(float $similarity): string
    {
        if ($similarity >= self::HIGH_SIMILARITY_THRESHOLD) {
            return 'high';
        } elseif ($similarity >= 0.7) {
            return 'medium';
        } else {
            return 'low';
        }
    }
    
    /**
     * Get match quality description
     */
    protected function getMatchQuality(float $similarity): string
    {
        if ($similarity >= 0.9) {
            return 'excellent';
        } elseif ($similarity >= 0.8) {
            return 'very_good';
        } elseif ($similarity >= 0.7) {
            return 'good';
        } elseif ($similarity >= 0.6) {
            return 'fair';
        } else {
            return 'poor';
        }
    }
    
    /**
     * Fallback search when vector search fails
     */
    protected function getFallbackSearch(string $query, array $options = []): array
    {
        $limit = $options['limit'] ?? 10;
        $results = collect();
        
        // Simple keyword-based search
        $services = Service::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('keywords', 'LIKE', "%{$query}%");
            })
            ->limit($limit)
            ->get();
            
        foreach ($services as $service) {
            $results->push([
                'type' => 'service',
                'id' => $service->id,
                'title' => $service->title,
                'description' => $service->description,
                'url' => $service->getUrl(),
                'thumbnail' => $service->thumbnail_url,
                'category' => $service->category?->title,
                'similarity' => 0.5, // Default similarity for fallback
                'confidence' => 'medium',
                'match_quality' => 'keyword_match'
            ]);
        }
        
        return [
            'results' => $results->toArray(),
            'query' => $query,
            'method' => 'fallback_search',
            'total_found' => count($results)
        ];
    }
    
    /**
     * Rebuild embeddings for all services and posts in PostgreSQL
     */
    public function rebuildEmbeddings(): array
    {
        $stats = [
            'services_processed' => 0,
            'posts_processed' => 0,
            'services_updated' => 0,
            'posts_updated' => 0,
            'errors' => 0
        ];
        
        try {
            // Process services
            $services = Service::where('is_active', true)->get();
            foreach ($services as $service) {
                try {
                    $result = $this->createOrUpdateServiceEmbedding($service);
                    $stats['services_processed']++;
                    if ($result) {
                        $stats['services_updated']++;
                    }
                } catch (Exception $e) {
                    $stats['errors']++;
                    Log::error('Failed to create embedding for service: ' . $service->id, ['error' => $e->getMessage()]);
                }
            }
            
            // Process posts
            $posts = Post::where('is_published', true)->get();
            foreach ($posts as $post) {
                try {
                    $result = $this->createOrUpdatePostEmbedding($post);
                    $stats['posts_processed']++;
                    if ($result) {
                        $stats['posts_updated']++;
                    }
                } catch (Exception $e) {
                    $stats['errors']++;
                    Log::error('Failed to create embedding for post: ' . $post->id, ['error' => $e->getMessage()]);
                }
            }
            
        } catch (Exception $e) {
            Log::error('Embedding rebuild failed: ' . $e->getMessage());
            $stats['errors']++;
        }
        
        return $stats;
    }
    
    /**
     * Create or update embedding for a service
     */
    public function createOrUpdateServiceEmbedding(Service $service): bool
    {
        try {
            $text = $this->prepareServiceText($service);
            $textHash = hash('sha256', $text);
            
            // Check if embedding already exists
            $existingEmbedding = ServiceEmbedding::where('service_id', $service->id)
                ->where('text_hash', $textHash)
                ->first();
            
            if ($existingEmbedding) {
                Log::info('Embedding already exists for service: ' . $service->id);
                return false;
            }
            
            // Generate new embedding
            $embedding = $this->getTextEmbedding($text, false);
            
            if (!$embedding) {
                throw new Exception('Failed to generate embedding');
            }
            
            // Delete old embeddings for this service
            ServiceEmbedding::deleteForService($service->id);
            
            // Create new embedding
            ServiceEmbedding::getOrCreateForService($service, $embedding, $textHash);
            
            Log::info('Created embedding for service: ' . $service->id);
            return true;
            
        } catch (Exception $e) {
            Log::error('Failed to create service embedding: ' . $e->getMessage(), ['service_id' => $service->id]);
            return false;
        }
    }
    
    /**
     * Create or update embedding for a post
     */
    public function createOrUpdatePostEmbedding(Post $post): bool
    {
        try {
            $text = $this->preparePostText($post);
            $textHash = hash('sha256', $text);
            
            // Check if embedding already exists
            $existingEmbedding = PostEmbedding::where('post_id', $post->id)
                ->where('text_hash', $textHash)
                ->first();
            
            if ($existingEmbedding) {
                Log::info('Embedding already exists for post: ' . $post->id);
                return false;
            }
            
            // Generate new embedding
            $embedding = $this->getTextEmbedding($text, false);
            
            if (!$embedding) {
                throw new Exception('Failed to generate embedding');
            }
            
            // Delete old embeddings for this post
            PostEmbedding::deleteForPost($post->id);
            
            // Create new embedding
            PostEmbedding::getOrCreateForPost($post, $embedding, $textHash);
            
            Log::info('Created embedding for post: ' . $post->id);
            return true;
            
        } catch (Exception $e) {
            Log::error('Failed to create post embedding: ' . $e->getMessage(), ['post_id' => $post->id]);
            return false;
        }
    }
    
    /**
     * Find similar services to a given service
     */
    public function findSimilarServices(int $serviceId, int $limit = 5): array
    {
        try {
            $service = Service::findOrFail($serviceId);
            $serviceText = $this->prepareServiceText($service);
            $serviceEmbedding = $this->getTextEmbedding($serviceText);
            
            if (!$serviceEmbedding) {
                return [];
            }
            
            $similarServices = $this->searchServices($serviceEmbedding, 0.5);
            
            // Remove the original service from results
            $similarServices = $similarServices->filter(function ($item) use ($serviceId) {
                return $item['id'] !== $serviceId;
            });
            
            return $similarServices->take($limit)->values()->toArray();
            
        } catch (Exception $e) {
            Log::error('Failed to find similar services: ' . $e->getMessage(), ['service_id' => $serviceId]);
            return [];
        }
    }
    
    /**
     * Get search analytics for vector search
     */
    public function getSearchAnalytics(int $days = 7): array
    {
        // This would be implemented based on your analytics requirements
        return [
            'total_vector_searches' => 0,
            'average_similarity' => 0.0,
            'most_similar_matches' => [],
            'performance_metrics' => [
                'embedding_cache_hits' => 0,
                'embedding_generation_time' => 0,
                'search_time' => 0
            ]
        ];
    }
} 