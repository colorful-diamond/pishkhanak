<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
class GeminiService
{
    protected string $baseUrl;
    protected string $defaultModel;
    protected string $advancedModel;
    public string $apiKey;
    protected array $proxyOptions;

    public function __construct()
    {
        // Try to get API keys from database settings first, then fall back to env
        $randomNum = rand(1, 3);
        $randomKey = \App\Models\Setting::getValue('gemini_api_key_' . $randomNum);
        
        if (!$randomKey) {
            // Try single key from settings
            $randomKey = \App\Models\Setting::getValue('gemini_api_key');
        }
        
        if (!$randomKey) {
            // Fall back to env for backward compatibility
            $randomKey = env('GEMINI_API_KEY' . $randomNum);
            if (!$randomKey) {
                $randomKey = env('GEMINI_API_KEY', '');
            }
        }
        
        $this->apiKey = $randomKey ?: '';
        
        if (!$this->apiKey) {
            // Don't throw exception, just log warning - allow graceful degradation
            Log::warning("GEMINI_API_KEY not configured in settings or .env");
            $this->apiKey = 'placeholder_key'; // Use placeholder to prevent errors
        }
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
        $this->defaultModel = 'gemini-2.5-flash'; // Updated to the new lite model
        $this->advancedModel = 'gemini-2.5-pro';
        $this->proxyOptions = [
            'proxy' => 'socks5://127.0.0.1:1080',
            'verify' => false
        ];
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function generateContent(string $prompt, string $model = null, array $options = [])
    {
        try {
            $modelToUse = $model === true ? $this->advancedModel : ($model ?? $this->defaultModel);
            $modelToUse = str_replace('google/', '', $modelToUse);
            $url = "{$this->baseUrl}/models/{$modelToUse}:generateContent";
            
            $body = $this->buildRequestBody([['parts' => [['text' => $prompt]]]], $options);
            $response = $this->makeRequest('post', $url, $body);
            $responseData = $response->json();
            
            // Check for API errors first
            if (isset($responseData['error'])) {
                Log::error("Gemini API error in generateContent", ['error' => $responseData['error']]);
                throw new \Exception("Gemini API error: " . ($responseData['error']['message'] ?? 'Unknown error'));
            }
            
            // Check if response has expected structure
            if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                // Try alternative structures
                if (isset($responseData['candidates'][0]['finishReason'])) {
                    $finishReason = $responseData['candidates'][0]['finishReason'];
                    Log::warning("Content blocked or filtered in generateContent", ['reason' => $finishReason]);
                    
                    // Handle MAX_TOKENS gracefully
                    if ($finishReason === 'MAX_TOKENS') {
                        // Return partial content if available
                        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                            return $responseData['candidates'][0]['content']['parts'][0]['text'];
                        } else {
                            return 'پاسخ خیلی طولانی شد. لطفاً سوال کوتاه‌تری بپرسید.';
                        }
                    } else {
                        throw new \Exception("Content was blocked by safety filters: " . $finishReason);
                    }
                } else {
                    Log::error("Unexpected Gemini API response structure in generateContent", ['response' => $responseData]);
                    // Return a fallback response instead of throwing exception
                    return 'متاسفانه نتوانستم پاسخ مناسبی تولید کنم. لطفاً دوباره تلاش کنید.';
                }
            }
            
            return $responseData['candidates'][0]['content']['parts'][0]['text'];
        } catch (\Exception $e) {
            Log::error("Error in generateContent: " . $e->getMessage(), ['prompt' => $prompt, 'model' => $model, 'options' => $options]);
            throw $e;
        }
    }

    public function streamGenerateContent(string $prompt, string $model = null, array $options = [])
    {
        try {
            $modelToUse = $model === true ? $this->advancedModel : ($model ?? $this->defaultModel);
            $modelToUse = str_replace('google/', '', $modelToUse);
            $url = "{$this->baseUrl}/models/{$modelToUse}:streamGenerateContent";
            
            $body = $this->buildRequestBody([['parts' => [['text' => $prompt]]]], $options);
            return $this->makeRequest('post', $url, $body, ['stream' => true]);
        } catch (\Exception $e) {
            Log::error("Error in streamGenerateContent: " . $e->getMessage(), ['prompt' => $prompt, 'model' => $model, 'options' => $options]);
            throw $e;
        }
    }

    public function countTokens(string $prompt, string $model = null)
    {
        try {
            $modelToUse = $model ?? $this->defaultModel;
            $modelToUse = str_replace('google/', '', $modelToUse);
            $url = "{$this->baseUrl}/models/{$modelToUse}:countTokens";
            $body = [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ];

            $response = $this->makeRequest('post', $url, $body);
            return $response->json()['totalTokens'];
        } catch (\Exception $e) {
            Log::error("Error in countTokens: " . $e->getMessage(), ['prompt' => $prompt, 'model' => $model]);
            throw $e;
        }
    }

    public function listModels()
    {
        try {
            $url = "{$this->baseUrl}/models";
            $response = $this->makeRequest('get', $url);
            return $response->json()['models'];
        } catch (\Exception $e) {
            Log::error("Error in listModels: " . $e->getMessage());
            throw $e;
        }
    }

    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    protected function makeRequest($method, $url, $data = [], $options = [])
    {
        $apiKey = $this->getApiKey();
        
        // Optimized timeout (reduced from 600s to 30s max)
        $timeout = isset($options['timeout']) ? $options['timeout'] : 30;
        
        $request = Http::withHeaders($this->getHeaders())
            ->timeout($timeout)
            ->connectTimeout(10)
            ->retry(3, 2000, function ($exception) {
                // Retry on timeout and server errors
                return $exception instanceof \Illuminate\Http\Client\ConnectionException ||
                       ($exception instanceof \Illuminate\Http\Client\RequestException && 
                        $exception->response && $exception->response->status() >= 500);
            });
        
        // Add proxy settings if grounding is set in options (conditional based on environment)
        if (count($this->proxyOptions) > 0 && app()->environment('production')) {
            Log::info("Using proxy for Gemini API request");
            $request = $request->withOptions($this->proxyOptions);
        }

        $response = $request->$method($url . '?key=' . $apiKey, $data);

        if (!$response->successful()) {
            $errorDetails = [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
                'url' => $url,
                'data' => $data,
                'options' => $options
            ];
            throw new \Exception("HTTP error! Details: " . json_encode($errorDetails));
        }

        return $response;
    }

    public function chatCompletion(array $messages, string $model = null, array $options = [])
    {
        try {
            $modelToUse = $model === true ? $this->advancedModel : ($model ?? $this->defaultModel);
            $modelToUse = str_replace('google/', '', $modelToUse);
            $url = "{$this->baseUrl}/models/{$modelToUse}:generateContent";

            $body = $this->buildRequestBody($this->formatMessagesForGemini($messages, $model), $options);
            
            // Store whether JSON was requested for later processing
            $jsonRequested = isset($options['json']);
            
            // Handle special options
            if(isset($options['online']) && $options['online']){
                // Use modern Google Search grounding for Gemini 2.5+ models
                if (strpos($modelToUse, 'gemini-2.') === 0 || strpos($modelToUse, 'gemini-3.') === 0) {
                    $body['tools'] = [
                        [
                            'google_search' => new \stdClass()
                        ]
                    ];
                    Log::info("Using Google Search grounding with model: {$modelToUse}");
                } else {
                    // Fallback to legacy grounding for older models
                    $body['tools'] = [
                        [
                            'google_search_retrieval' => [
                                'dynamic_retrieval_config' => [
                                    'mode' => 'MODE_DYNAMIC',
                                    'dynamic_threshold' => 0.3,
                                ]
                            ]
                        ]
                    ];
                    Log::info("Using legacy Google Search grounding with model: {$modelToUse}");
                }
                unset($options['online']);
            }

            if(isset($options['json'])){
                unset($body['json']);
                // Only set JSON response format if no grounding tools are being used
                if (!isset($body['tools'])) {
                    if (!isset($body['generationConfig'])) {
                        $body['generationConfig'] = [];
                    }
                    $body['generationConfig']['responseMimeType'] = 'application/json';
                } else {
                    Log::info("JSON response format disabled due to tool usage (grounding)");
                }
            }

            $response = $this->makeRequest('post', $url, $body);
            $responseData = $response->json();
            
            // Check for API errors first
            if (isset($responseData['error'])) {
                Log::error("Gemini API error", ['error' => $responseData['error']]);
                throw new \Exception("Gemini API error: " . ($responseData['error']['message'] ?? 'Unknown error'));
            }
            
            // Check if response has expected structure
            if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                // Try alternative structures
                if (isset($responseData['candidates'][0]['content']['parts'][0]['functionCall'])) {
                    // Function call response
                    Log::info("Received function call response from Gemini");
                    return json_encode($responseData['candidates'][0]['content']['parts'][0]['functionCall']);
                } elseif (isset($responseData['candidates'][0]['finishReason'])) {
                    // Blocked or filtered content
                    $finishReason = $responseData['candidates'][0]['finishReason'];
                    Log::warning("Content blocked or filtered", ['reason' => $finishReason]);
                    
                    // Handle MAX_TOKENS gracefully
                    if ($finishReason === 'MAX_TOKENS') {
                        // Return partial content if available
                        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                            return $responseData['candidates'][0]['content']['parts'][0]['text'];
                        } else {
                            return 'پاسخ خیلی طولانی شد. لطفاً سوال کوتاه‌تری بپرسید.';
                        }
                    } else {
                        throw new \Exception("Content was blocked by safety filters: " . $finishReason);
                    }
                } else {
                    Log::error("Unexpected Gemini API response structure", ['response' => $responseData]);
                    // Return a fallback response instead of throwing exception
                    return 'متاسفانه نتوانستم پاسخ مناسبی تولید کنم. لطفاً دوباره تلاش کنید.';
                }
            }
            
            $responseText = $responseData['candidates'][0]['content']['parts'][0]['text'];
            
            // If JSON was requested but grounding prevented it, try to extract JSON from text response
            if($jsonRequested && isset($body['tools'])) {
                // Try to extract JSON from the response text
                $jsonText = $this->extractJsonFromText($responseText);
                if ($jsonText) {
                    return $jsonText;
                }
                Log::warning("Could not extract JSON from grounded response, returning raw text");
            }
            
            return $responseText;
        } catch (\Exception $e) {
            Log::error("Error in chatCompletion: " . $e->getMessage(), ['messages' => $messages, 'model' => $model, 'options' => $options]);
            throw $e;
        }
    }

    public function streamChatCompletion(array $messages, string $model = null, array $options = [])
    {
        try {
            $modelToUse = $model === true ? $this->advancedModel : ($model ?? $this->defaultModel);
            $modelToUse = str_replace('google/', '', $modelToUse);
            $url = "{$this->baseUrl}/models/{$modelToUse}:streamGenerateContent";

            $body = $this->buildRequestBody($this->formatMessagesForGemini($messages), $options);
            return $this->makeRequest('post', $url, $body, ['stream' => true]);
        } catch (\Exception $e) {
            Log::error("Error in streamChatCompletion: " . $e->getMessage(), ['messages' => $messages, 'model' => $model, 'options' => $options]);
            throw $e;
        }
    }

    protected function formatMessagesForGemini(array $messages, string $model = null): array
    {
        $res = [];
        
        foreach ($messages as $message) {
            if (isset($message['content']) && isset($message['role'])) {
                $role = $message['role'];
                
                // Convert system messages to user messages for Gemini
                if ($role === 'system') {
                    $role = 'user';
                }
                // Convert assistant messages to model messages for Gemini
                elseif ($role === 'assistant') {
                    $role = 'model';
                }
                
                $res[] = [
                    'role' => $role,
                    'parts' => [['text' => $message['content']]]
                ];
            }
        }

        if ($model === true) {
            // Note: This legacy approach is kept for backward compatibility
            // The modern approach should use the 'online' option in chatCompletion
            $res['tools'] = [
                [
                    'google_search_retrieval' => [
                        'dynamic_retrieval_config' => [
                            'mode' => 'MODE_DYNAMIC',
                            'dynamic_threshold' => 0.7,
                        ]
                    ]
                ]
            ];
        }

        return $res;
    }

    public function chatCompletionWithJsonValidation(array $messages, string $instruction = null, string $model = null, array $options = [] , $try = 0)
    {
        try {
            $options['system_instruction'] = ["parts"=>["text"=>$instruction]];
            $options['generationConfig'] = [
                'responseMimeType' => 'application/json',
            ];
            $response = $this->chatCompletion($messages, $model, $options);

            $jsonResponse = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                preg_match('/\{.*\}/s', $response, $matches);
                if (!empty($matches)) {
                    $jsonResponse = json_decode($matches[0], true);
                }
            }

            if (json_last_error() !== JSON_ERROR_NONE) {
                if($try < 3){
                    Log::error("Failed to obtain valid JSON from the AI response: " . $response);
                    return $this->chatCompletionWithJsonValidation($messages, $instruction , $model, $options, $try + 1);
                }
                throw new \Exception("Failed to obtain valid JSON from the AI response: " . $response);
            }

            return $jsonResponse;
        } catch (\Exception $e) {
            Log::error("Error in chatCompletionWithJsonValidation: " . $e->getMessage(), ['messages' => $messages, 'instruction' => $instruction, 'model' => $model, 'options' => $options, 'try' => $try]);
            throw $e;
        }
    }

    public function getGenerationStats(string $generationId)
    {
        throw new \Exception("Generation stats are not supported for Gemini API.");
    }

    public function generateEmbedding(string $text, string $model = null)
    {
        $modelToUse = $model ?? 'text-embedding-004'; // Gemini's embedding model
        $modelToUse = str_replace('google/', '', $modelToUse);
        $url = "{$this->baseUrl}/models/{$modelToUse}:embedContent";


        $body = [
            'model' => "models/{$modelToUse}",
            'content' => [
                'parts' => [
                    ['text' => $text]
                ]
            ],
        ];

        try {
            $response = $this->makeRequest('post', $url, $body);
            return $response->json()['embedding']['values'];
        } catch (\Exception $e) {
            Log::error("Gemini Embedding API Error: " . $e->getMessage());
            throw $e;
        }
    }

    protected function processGenerationConfig(array $options): array
    {
        // Map of parameter names to Gemini API field names
        $generationConfigParams = [
            'temperature' => 'temperature',
            'max_tokens' => 'maxOutputTokens',
            'top_p' => 'topP',
            'top_k' => 'topK',
            'maxOutputTokens' => 'maxOutputTokens',
            'topP' => 'topP',
            'topK' => 'topK',
            'candidateCount' => 'candidateCount',
            'stopSequences' => 'stopSequences',
        ];

        $generationConfig = [];
        $otherOptions = [];

        // Separate generation config from other options
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $generationConfigParams)) {
                $apiKey = $generationConfigParams[$key];
                $generationConfig[$apiKey] = $value;
            } else {
                $otherOptions[$key] = $value;
            }
        }

        return [$generationConfig, $otherOptions];
    }

    protected function buildRequestBody(array $contents, array $options): array
    {
        [$generationConfig, $otherOptions] = $this->processGenerationConfig($options);

        // Exclude special options that are handled separately
        unset($otherOptions['online']);
        unset($otherOptions['json']);

        $body = [
            'contents' => $contents,
            ...$otherOptions
        ];

        // Add generationConfig if we have any parameters
        if (!empty($generationConfig)) {
            if (isset($body['generationConfig'])) {
                $body['generationConfig'] = array_merge($body['generationConfig'], $generationConfig);
            } else {
                $body['generationConfig'] = $generationConfig;
            }
        }

        return $body;
    }

    /**
     * Extract JSON from text response when grounding prevents JSON format
     */
    protected function extractJsonFromText(string $text): ?string
    {
        // Remove common prefixes/suffixes that might be added
        $text = trim($text);
        
        // Try to find JSON content between ```json and ``` markers
        if (preg_match('/```json\s*(\{.*?\})\s*```/s', $text, $matches)) {
            return $matches[1];
        }
        
        // Try to find JSON content between ``` markers
        if (preg_match('/```\s*(\{.*?\})\s*```/s', $text, $matches)) {
            return $matches[1];
        }
        
        // Try to find JSON object directly
        if (preg_match('/(\{.*?\})/s', $text, $matches)) {
            $potential_json = $matches[1];
            // Validate it's actually JSON
            json_decode($potential_json);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $potential_json;
            }
        }
        
        // If all else fails, try to extract the largest JSON-like structure
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        
        if ($start !== false && $end !== false && $end > $start) {
            $potential_json = substr($text, $start, $end - $start + 1);
            // Validate it's actually JSON
            json_decode($potential_json);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $potential_json;
            }
        }
        
        return null;
    }
}
