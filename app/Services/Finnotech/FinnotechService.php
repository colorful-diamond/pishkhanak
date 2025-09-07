<?php

namespace App\Services\Finnotech;

use App\Models\Token;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;
use App\Services\FinnotechTokenMapper;

/**
 * Class FinnotechService
 * 
 * This service handles interactions with the Finnotech API.
 */
class FinnotechService
{
    /**
     * API Endpoints
     */
    private const API_BASE_URL = 'https://api.finnotech.ir';
    private const API_VERSION = 'v2';
    private const CLIENT_ID = 'pishkhanak';
    private const REDIRECT_URI = 'https://pishkhanak.com/api/auth';
    private const REDIS_FAILED_REQUESTS_KEY = 'finnotech:failed_requests';
    private const MAX_RETRIES = 2;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var \App\Services\TokenService
     */
    private $tokenService;

    /**
     * FinnotechService constructor.
     * Initializes the service with the access token and refresh token.
     */
    public function __construct()
    {
        $this->tokenService = app(\App\Services\TokenService::class);
        
        // Get tokens from TokenService (default to original fino token as fallback)
        $this->token = $this->tokenService->getAccessToken(Token::PROVIDER_FINNOTECH, Token::NAME_FINNOTECH);
        $this->refreshToken = $this->tokenService->getRefreshToken(Token::PROVIDER_FINNOTECH, Token::NAME_FINNOTECH);
    }

    /**
     * Refresh the access token using the refresh token.
     *
     * @return bool
     */
    public function refreshToken(): bool
    {
        $success = $this->tokenService->refreshFinnotechToken();
        
        if ($success) {
            // Update local tokens
            $this->token = $this->tokenService->getAccessToken(Token::PROVIDER_FINNOTECH);
            $this->refreshToken = $this->tokenService->getRefreshToken(Token::PROVIDER_FINNOTECH);
        }
        
        return $success;
    }

    /**
     * Make an API request to the Finnotech API.
     *
     * @param string $endpoint
     * @param array $params
     * @param string $method
     * @param bool $useToken
     * @param string|null $customToken
     * @return object|null
     */
    public function makeApiRequest(string $endpoint, array $params = [], string $method = 'GET', bool $useToken = true, ?string $customToken = null): ?object
    {
        
        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            // Use trackId from params if provided, otherwise generate one
            if ( $attempt <=1){
                $trackId = $params['trackId'] ?? $this->generateTrackId();
            }else{
                $trackId = $this->generateTrackId();
            }
            try {
                $url = self::API_BASE_URL . $endpoint . '?trackId=' . $trackId;
            
                // Prepare headers
                $headers = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ];

                if ($useToken) {
                    if ($customToken) {
                        $headers['Authorization'] = 'Bearer ' . $customToken;
                    } else {
                        // Determine the appropriate token based on endpoint
                        $tokenName = FinnotechTokenMapper::getTokenNameForEndpoint($endpoint);
                        $token = $this->tokenService->getAccessToken(Token::PROVIDER_FINNOTECH, $tokenName);
                        
                        // Fallback to default token if category token not available
                        if (!$token) {
                            $token = $this->token;
                            Log::warning("Using fallback token for endpoint: {$endpoint}. Category token '{$tokenName}' not available.");
                        }
                        
                        $headers['Authorization'] = 'Bearer ' . $token;
                        
                        // Log which token is being used for debugging
                        Log::debug("Using token '{$tokenName}' for endpoint: {$endpoint}");
                    }
                } elseif ($customToken) {
                    $headers['Authorization'] = 'Bearer ' . $customToken;
                } else {
                    $headers['Authorization'] = 'Basic ZXN0ZWxhbTpRb1pKUlE1VTVQVXNDb1Vac3BDdw==';
                }

                // Create HTTP client with timeout and headers
                $httpClient = Http::timeout(100)
                    ->withHeaders($headers);

                // Log request for debugging
                Log::debug('Finnotech API Request', [
                    'url' => $url,
                    'method' => $method,
                    'params' => $params,
                    'track_id' => $trackId,
                    'headers' => $headers,
                    'token_category' => $useToken && !$customToken ? FinnotechTokenMapper::getTokenNameForEndpoint($endpoint) : 'custom/none'
                ]);

                if ($method === 'POST') {
                    $response = $httpClient->post($url, $params);
                } else {
                    if (!empty($params)) {
                        $response = $httpClient->get($url, $params);
                    } else {
                        $response = $httpClient->get($url);
                    }
                }

                Log::debug('Finnotech API Response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                if ($response->successful()) {
                    $responseData = $response->object();
                    // For SMS and other services that may return empty successful responses,
                    // return immediately on HTTP success to prevent retries
                    return $responseData ?: new \stdClass();
                }

                if ($attempt === self::MAX_RETRIES) {
                    $this->logFailedRequest($method, $endpoint, $params, [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    return null;
                }
            } catch (Exception $e) {
                if ($attempt === self::MAX_RETRIES) {
                    $this->logFailedRequest($method, $endpoint, $params, ['error' => $e->getMessage()]);
                    Log::error("Error making API request to $endpoint: " . $e->getMessage());
                    return null;
                }
            }

            // Wait before retrying (exponential backoff)
            sleep(2 ** ($attempt - 1));
        }

        return null;
    }

    /**
     * Log failed API requests to Redis and application log.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @param array $response
     */
    private function logFailedRequest(string $method, string $endpoint, array $data, array $response): void
    {
        $failedRequest = [
            'method' => $method,
            'endpoint' => $endpoint,
            'data' => $data,
            'response' => $response,
            'timestamp' => now()->toIso8601String(),
        ];

        Redis::lpush(self::REDIS_FAILED_REQUESTS_KEY, json_encode($failedRequest));
        Log::error('Finnotech API request failed', $failedRequest);
    }

    /**
     * Generate a unique track ID.
     *
     * @return string
     */
    public function generateTrackId(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Get the current access token.
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token ?? '';
    }

    /**
     * Get the current refresh token.
     *
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken ?? '';
    }

    /**
     * Set the access token.
     *
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * Set the refresh token.
     *
     * @param string $refreshToken
     */
    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * Get API constants.
     */
    public function getClientId(): string
    {
        return self::CLIENT_ID;
    }

    public function getRedirectUri(): string
    {
        return self::REDIRECT_URI;
    }

    public function getApiVersion(): string
    {
        return self::API_VERSION;
    }

    /**
     * Get the appropriate token for a specific endpoint
     *
     * @param string $endpoint
     * @return string|null
     */
    public function getTokenForEndpoint(string $endpoint): ?string
    {
        $tokenName = FinnotechTokenMapper::getTokenNameForEndpoint($endpoint);
        $token = $this->tokenService->getAccessToken(Token::PROVIDER_FINNOTECH, $tokenName);
        
        // Fallback to default token if category token not available
        if (!$token) {
            $token = $this->token;
            Log::warning("Category token '{$tokenName}' not available for endpoint: {$endpoint}. Using fallback token.");
        }
        
        return $token;
    }

    /**
     * Ensure valid token for a specific endpoint
     *
     * @param string $endpoint
     * @return bool
     */
    public function ensureValidTokenForEndpoint(string $endpoint): bool
    {
        $tokenName = FinnotechTokenMapper::getTokenNameForEndpoint($endpoint);
        return $this->tokenService->ensureValidToken(Token::PROVIDER_FINNOTECH, $tokenName);
    }
} 