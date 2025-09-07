<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\GeminiService;

class EnhancedContentGeneratorV2
{
    protected $geminiService;
    protected $proxies = [];
    protected $currentProxyIndex = 0;
    
    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
        
        // Initialize proxy list from ports 8001-8012
        for ($i = 8001; $i <= 8012; $i++) {
            $this->proxies[] = "socks5://localhost:{$i}";
        }
    }
    
    /**
     * Get next proxy in rotation
     */
    protected function getNextProxy(): string
    {
        $proxy = $this->proxies[$this->currentProxyIndex];
        $this->currentProxyIndex = ($this->currentProxyIndex + 1) % count($this->proxies);
        return $proxy;
    }
    
    /**
     * Get Google search suggestions for a keyword using cURL
     */
    protected function getGoogleSuggestions(string $keyword): array
    {
        $suggestions = [];
        
        try {
            // Build URL
            $url = "http://suggestqueries.google.com/complete/search?" . http_build_query([
                'client' => 'firefox',
                'q' => $keyword,
                'hl' => 'fa' // Persian language
            ]);
            
            // Use cURL which works better than HTTP client
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            
            // Optional: Use proxy if needed (commented out for now since direct works)
            // $proxy = $this->getNextProxy();
            // curl_setopt($ch, CURLOPT_PROXY, $proxy);
            // curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode == 200 && $result) {
                $data = json_decode($result, true);
                if (isset($data[1]) && is_array($data[1])) {
                    $suggestions = array_slice($data[1], 0, 10); // Get top 10 suggestions
                }
            }
            
            Log::info('Google suggestions fetched', [
                'keyword' => $keyword,
                'suggestions_count' => count($suggestions),
                'suggestions' => array_slice($suggestions, 0, 5) // Log first 5
            ]);
            
        } catch (\Exception $e) {
            Log::warning('Failed to get Google suggestions', [
                'keyword' => $keyword,
                'error' => $e->getMessage()
            ]);
        }
        
        return $suggestions;
    }
    
    /**
     * Enhanced research with Google suggestions and proxy support
     */
    public function researchServiceEnhanced(string $serviceName, string $description = ''): array
    {
        Log::info('Starting enhanced research with Google suggestions', [
            'service' => $serviceName,
            'proxies_available' => count($this->proxies)
        ]);
        
        // Get Google suggestions for more comprehensive keyword coverage
        $baseKeywords = $this->getGoogleSuggestions($serviceName);
        
        // Add service-specific keywords
        $additionalKeywords = [
            "8ca86013",
            "PERSIAN_TEXT_f03c8762",
            "0e42101b",
            "PERSIAN_TEXT_79a70797",
            "2d258b26"
        ];
        
        // Get suggestions for each additional keyword
        foreach ($additionalKeywords as $keyword) {
            $moreSuggestions = $this->getGoogleSuggestions($keyword);
            $baseKeywords = array_merge($baseKeywords, $moreSuggestions);
        }
        
        // Remove duplicates and limit to most relevant
        $baseKeywords = array_unique($baseKeywords);
        $baseKeywords = array_slice($baseKeywords, 0, 20);
        
        Log::info('Keywords collected from Google suggestions', [
            'total_keywords' => count($baseKeywords)
        ]);
        
        // Comprehensive research queries based on Google suggestions + manual queries
        $researchQueries = [
            // Core service understanding
            "PERSIAN_TEXT_607782d4",
            "55834eca",
            "PERSIAN_TEXT_74a3bc82",
            "38988acb",
            "PERSIAN_TEXT_79ccf0a5",
            
            // Detailed aspects
            "f76a86e4",
            "PERSIAN_TEXT_2d93147c",
            "ba851005",
            "PERSIAN_TEXT_9a87a29d",
            "b7d1d31f",
            
            // Applications
            "PERSIAN_TEXT_2f7fb820",
            "3fa11da0",
            "PERSIAN_TEXT_662255c6",
            
            // Process
            "3c1717b5",
            "PERSIAN_TEXT_ab6b6e5f",
            "6cf04387"
        ];
        
        // Add top Google suggestions to research queries
        foreach (array_slice($baseKeywords, 0, 10) as $suggestion) {
            if (!in_array($suggestion, $researchQueries)) {
                $researchQueries[] = $suggestion;
            }
        }
        
        // Initialize research data structure
        $researchData = [
            'service_purpose' => '',
            'detailed_description' => '',
            'required_documents' => [],
            'process_steps' => [],
            'typical_cost' => '',
            'time_required' => '',
            'use_cases' => [],
            'benefits' => [],
            'limitations' => [],
            'important_notes' => [],
            'common_questions' => [],
            'legal_requirements' => [],
            'alternatives' => [],
            'target_audience' => [],
            'success_stories' => [],
            'statistics' => [],
            'google_keywords' => $baseKeywords,
            'raw_responses' => []
        ];
        
        $totalTokens = 0;
        $successfulQueries = 0;
        $failedQueries = 0;
        
        // Process each query WITHOUT proxy (direct Gemini API calls)
        foreach ($researchQueries as $index => $query) {
            try {
                Log::info("Processing query {$index}", [
                    'query' => $query,
                    'query_number' => $index + 1,
                    'total_queries' => count($researchQueries)
                ]);
                
                $messages = [
                    [
                        'role' => 'system',
                        'content' => 'You are a research assistant gathering comprehensive information about Iranian services. Provide detailed, factual information with examples, statistics, and practical details. Always respond in Persian.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "35f2c2c8"
                    ]
                ];
                
                // Call Gemini directly without proxy
                $startTime = microtime(true);
                $response = $this->callGeminiDirect($messages);
                $duration = microtime(true) - $startTime;
                
                if ($response && strlen($response) > 100) {
                    $successfulQueries++;
                    
                    // Parse and categorize response
                    $this->parseAndStoreResponse($query, $response, $researchData);
                    
                    // Store raw response
                    $researchData['raw_responses'][$query] = [
                        'response' => $response,
                        'duration' => $duration,
                        'tokens' => (int)(strlen($response) / 4)
                    ];
                    
                    $totalTokens += (int)(strlen($response) / 4);
                    
                    Log::info("Query completed successfully", [
                        'query_index' => $index,
                        'duration' => $duration,
                        'response_length' => strlen($response),
                        'total_tokens_so_far' => $totalTokens
                    ]);
                    
                    // If we have enough tokens and it's taking too long, we can stop
                    if ($totalTokens >= 4000 && $duration > 30) {
                        Log::info('Sufficient tokens collected, stopping early', [
                            'total_tokens' => $totalTokens
                        ]);
                        break;
                    }
                } else {
                    $failedQueries++;
                    Log::warning("Query returned insufficient response"b925a33a"\n\n";
            $researchData['detailed_description'PERSIAN_TEXT_213c6742'detailed_description'] .= "\n\n" . $response;
        }
        
        // Always try to extract statistics from any response
        preg_match_all('PERSIAN_TEXT_22e47eac', $response, $matches);
        if (!empty($matches[0])) {
            $researchData['statistics'] = array_merge($researchData['statistics'], $matches[0]);
        }
    }
}