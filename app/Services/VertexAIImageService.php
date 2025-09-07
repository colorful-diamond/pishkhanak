<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VertexAIImageService
{
    protected $projectId;
    protected $location;
    protected $apiEndpoint;
    protected $accessToken;
    protected $proxyUrl;
    protected $proxyEnabled;
    
    public function __construct()
    {
        $this->projectId = env('GOOGLE_CLOUD_PROJECT_ID', 'your-project-id');
        $this->location = env('VERTEX_AI_LOCATION', 'us-central1');
        $this->apiEndpoint = "https://{$this->location}-aiplatform.googleapis.com";
        
        // Proxy configuration
        $this->proxyUrl = env('VERTEX_AI_PROXY_URL', 'socks5://127.0.0.1:1080');
        $this->proxyEnabled = env('VERTEX_AI_PROXY_ENABLED', true);
    }
    
    /**
     * Get access token for Vertex AI
     * This uses Application Default Credentials or service account
     */
    protected function getAccessToken()
    {
        try {
            // Try to use Google Application Default Credentials
            $credentials = $this->getCredentials();
            
            if ($credentials) {
                // Use service account to get access token
                $tokenUrl = 'https://oauth2.googleapis.com/token';
                
                $response = Http::asForm()->post($tokenUrl, [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $this->createJWT($credentials)
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['access_token'] ?? null;
                }
            }
            
            // Fallback to API key if available
            return env('GOOGLE_API_KEY');
            
        } catch (\Exception $e) {
            Log::error('Failed to get Vertex AI access token', [
                'error' => $e->getMessage()
            ]);
            
            // Return API key as fallback
            return env('GOOGLE_API_KEY');
        }
    }
    
    /**
     * Get credentials from service account file
     */
    protected function getCredentials()
    {
        $credentialsPath = env('GOOGLE_APPLICATION_CREDENTIALS');
        
        if (!$credentialsPath) {
            // Try default locations
            $possiblePaths = [
                storage_path('app/vertex-ai-credentials.json'),
                storage_path('app/gcp-credentials.json'),
                storage_path('app/google-cloud-credentials.json'),
                base_path('vertex-ai-credentials.json'),
                base_path('gcp-credentials.json')
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $credentialsPath = $path;
                    break;
                }
            }
        }
        
        if ($credentialsPath && file_exists($credentialsPath)) {
            return json_decode(file_get_contents($credentialsPath), true);
        }
        
        return null;
    }
    
    /**
     * Create JWT for service account authentication
     */
    protected function createJWT($credentials)
    {
        if (!isset($credentials['private_key']) || !isset($credentials['client_email'])) {
            throw new \Exception('Invalid service account credentials');
        }
        
        $now = time();
        
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];
        
        $payload = [
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ];
        
        $encodedHeader = $this->base64UrlEncode(json_encode($header));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload));
        
        $signature = '';
        openssl_sign(
            $encodedHeader . '.' . $encodedPayload,
            $signature,
            $credentials['private_key'],
            OPENSSL_ALGO_SHA256
        );
        
        $encodedSignature = $this->base64UrlEncode($signature);
        
        return $encodedHeader . '.' . $encodedPayload . '.' . $encodedSignature;
    }
    
    /**
     * Base64 URL encode
     */
    protected function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Make HTTP request with SOCKS5 proxy support using cURL
     */
    protected function makeProxyRequest(string $url, array $data, array $headers = [])
    {
        $ch = curl_init();
        
        // Basic cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        
        // Set headers
        $curlHeaders = ['Content-Type: application/json'];
        foreach ($headers as $key => $value) {
            $curlHeaders[] = "{$key}: {$value}";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
        
        // Configure SOCKS5 proxy if enabled
        if ($this->proxyEnabled && $this->proxyUrl) {
            // Parse proxy URL
            $proxyParts = parse_url($this->proxyUrl);
            
            if ($proxyParts['scheme'] === 'socks5' || $proxyParts['scheme'] === 'socks5h') {
                // Set SOCKS5 proxy
                curl_setopt($ch, CURLOPT_PROXY, $proxyParts['host'] . ':' . $proxyParts['port']);
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                
                // Use socks5h to resolve DNS through proxy
                if ($proxyParts['scheme'] === 'socks5h') {
                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
                }
                
                // Set proxy authentication if provided
                if (isset($proxyParts['user']) && isset($proxyParts['pass'])) {
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyParts['user'] . ':' . $proxyParts['pass']);
                }
                
                // Enable tunneling for HTTPS through proxy
                curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
                
                Log::info('Using SOCKS5 proxy for Vertex AI request', [
                    'proxy' => $proxyParts['host'] . ':' . $proxyParts['port'],
                    'url' => $url
                ]);
            }
        }
        
        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }
        
        return [
            'body' => $response,
            'status' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }
    
    /**
     * Generate image using Imagen 4 on Vertex AI
     */
    public function generateImage(string $prompt, array $settings = [])
    {
        try {
            // Get access token
            $token = $this->getAccessToken();
            
            if (!$token) {
                throw new \Exception('Unable to authenticate with Vertex AI');
            }
            
            // Use model from settings or environment variable
            $model = $settings['model'] ?? env('IMAGEN_MODEL', 'imagen-4.0-ultra-generate-001');
            $endpoint = "{$this->apiEndpoint}/v1/projects/{$this->projectId}/locations/{$this->location}/publishers/google/models/{$model}:predict";
            
            // Prepare the request according to Imagen 4 specifications
            $requestData = [
                'instances' => [
                    [
                        'prompt' => $prompt
                    ]
                ],
                'parameters' => [
                    'sampleCount' => $settings['count'] ?? 1,
                    'aspectRatio' => $settings['aspectRatio'] ?? '1:1',
                    'mode' => 'generate',  // For Imagen 4
                    'personGeneration' => $settings['personGeneration'] ?? 'allow_all',
                    'safetyFilterLevel' => $settings['safetyFilterLevel'] ?? 'block_some',
                    'addWatermark' => $settings['addWatermark'] ?? false,
                    'seed' => $settings['seed'] ?? random_int(1, 1000000),
                    'language' => $settings['language'] ?? 'auto',
                    // Imagen 4 specific improvements
                    'guidanceScale' => $settings['guidanceScale'] ?? 7.5,  // Controls prompt adherence
                    'outputOptions' => [
                        'mimeType' => 'image/png',
                        'compressionQuality' => 95
                    ]
                ]
            ];
            
            // Add negative prompt if provided
            if (isset($settings['negativePrompt'])) {
                $requestData['instances'][0]['negativePrompt'] = $settings['negativePrompt'];
            }
            
            Log::info('Calling Vertex AI Imagen 4', [
                'model' => $model,
                'endpoint' => $endpoint,
                'prompt' => $prompt,
                'settings' => $settings
            ]);
            
            // Make the API request with proxy support
            $headers = [];
            
            // Use Bearer token if it looks like an access token, otherwise use API key
            if (strlen($token) > 100) {
                $headers['Authorization'] = 'Bearer ' . $token;
            } else {
                // Use API key in URL parameter
                $endpoint .= '?key=' . $token;
            }
            
            // Use cURL with SOCKS5 proxy support
            try {
                $response = $this->makeProxyRequest($endpoint, $requestData, $headers);
                $responseData = $response['data'];
                $statusCode = $response['status'];
                
                if ($statusCode !== 200) {
                    Log::error('Vertex AI Imagen 4 API error', [
                        'status' => $statusCode,
                        'error' => $responseData,
                        'endpoint' => $endpoint
                    ]);
                    
                    // Try with direct API key approach
                    return $this->generateImageWithAPIKey($prompt, $settings);
                }
            } catch (\Exception $e) {
                Log::error('Proxy request failed', [
                    'error' => $e->getMessage(),
                    'endpoint' => $endpoint
                ]);
                
                // Fallback to regular HTTP request without proxy
                $response = Http::withHeaders(array_merge(['Content-Type' => 'application/json'], $headers))
                    ->timeout(120)
                    ->post($endpoint, $requestData);
                
                if (!$response->successful()) {
                    return $this->generateImageWithAPIKey($prompt, $settings);
                }
                
                $responseData = $response->json();
            }
            
            // Process the predictions
            if (isset($responseData['predictions']) && count($responseData['predictions']) > 0) {
                $images = [];
                $localPaths = [];
                
                foreach ($responseData['predictions'] as $prediction) {
                    if (isset($prediction['bytesBase64Encoded'])) {
                        $imageData = $this->saveGeneratedImage($prediction['bytesBase64Encoded']);
                        if ($imageData) {
                            $images[] = $imageData;
                            $localPaths[] = $imageData;
                        }
                    }
                }
                
                if (count($images) > 0) {
                    return [
                        'success' => true,
                        'images' => $images,
                        'local_paths' => $localPaths,
                        'metadata' => [
                            'model' => $settings['model'] ?? env('IMAGEN_MODEL', 'imagen-4.0-ultra-generate-001'),
                            'count' => count($images),
                            'prompt' => $prompt
                        ]
                    ];
                }
            }
            
            Log::warning('No predictions in Vertex AI response', [
                'response' => $responseData
            ]);
            
            return [
                'success' => false,
                'error' => 'No images generated',
                'details' => $responseData ?? []
            ];
            
        } catch (\Exception $e) {
            Log::error('Vertex AI Imagen 4 generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Try alternative approach
            $result = $this->generateImageWithAPIKey($prompt, $settings);
            if (!empty($result)) {
                return $result;
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'details' => []
            ];
        }
    }
    
    /**
     * Alternative method using API key directly
     */
    protected function generateImageWithAPIKey(string $prompt, array $settings = [])
    {
        try {
            $apiKey = env('GOOGLE_API_KEY');
            
            if (!$apiKey) {
                throw new \Exception('No Google API key configured');
            }
            
            // Try Google AI Studio endpoint for Imagen 4
            $model = $settings['model'] ?? env('IMAGEN_MODEL', 'imagen-4.0-ultra-generate-001');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateImage";
            
            $requestData = [
                'prompt' => [
                    'text' => $prompt
                ],
                'images' => [
                    'sampleCount' => $settings['count'] ?? 1,
                    'aspectRatio' => $settings['aspectRatio'] ?? '1:1',
                    'safetyFilterLevel' => $settings['safetyFilterLevel'] ?? 'block_some',
                    'personGeneration' => $settings['personGeneration'] ?? 'allow_all',
                    'language' => $settings['language'] ?? 'auto'
                ]
            ];
            
            Log::info('Trying Imagen with API key', [
                'url' => $url,
                'prompt' => $prompt
            ]);
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $apiKey
            ])->timeout(120)->post($url, $requestData);
            
            if (!$response->successful()) {
                Log::error('Imagen API key method failed', [
                    'status' => $response->status(),
                    'error' => $response->json()
                ]);
                return [];
            }
            
            $responseData = $response->json();
            
            if (isset($responseData['images'])) {
                $images = [];
                $localPaths = [];
                
                foreach ($responseData['images'] as $image) {
                    if (isset($image['image']['imageBytes'])) {
                        $imageData = $this->saveGeneratedImage($image['image']['imageBytes']);
                        if ($imageData) {
                            $images[] = $imageData;
                            $localPaths[] = $imageData;
                        }
                    }
                }
                
                if (count($images) > 0) {
                    return [
                        'success' => true,
                        'images' => $images,
                        'local_paths' => $localPaths,
                        'metadata' => [
                            'model' => $model,
                            'count' => count($images),
                            'prompt' => $prompt,
                            'method' => 'api_key'
                        ]
                    ];
                }
            }
            
            return [
                'success' => false,
                'error' => 'No images generated with API key',
                'details' => $responseData ?? []
            ];
            
        } catch (\Exception $e) {
            Log::error('Imagen API key generation failed', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'details' => []
            ];
        }
    }
    
    /**
     * Save generated image from base64 data
     */
    protected function saveGeneratedImage(string $base64Data)
    {
        try {
            $imageContent = base64_decode($base64Data);
            
            if (!$imageContent) {
                Log::error('Failed to decode base64 image data');
                return null;
            }
            
            $filename = 'imagen_' . Str::random(10) . '.png';
            $path = 'ai_images/generated/' . $filename;
            
            // Ensure directory exists
            Storage::disk('public')->makeDirectory('ai_images/generated');
            
            // Save image
            Storage::disk('public')->put($path, $imageContent);
            
            Log::info('Imagen image saved successfully', [
                'path' => $path,
                'size' => strlen($imageContent)
            ]);
            
            return [
                'path' => $path,
                'url' => asset('storage/' . $path),
                'filename' => $filename,
                'source' => 'vertex-ai-imagen-4'
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to save generated image', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}