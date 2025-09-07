<?php

namespace App\Services\Jibit;

use App\Models\Token;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;

/**
 * Class JibitService
 * 
 * This service handles interactions with the Jibit API.
 */
class JibitService
{
    /**
     * API Endpoints
     */
    private const API_BASE_URL = 'https://napi.jibit.ir/ide';
    private const API_VERSION = 'v1';
    private const REDIS_FAILED_REQUESTS_KEY = 'jibit:failed_requests';
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
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var \App\Services\TokenService
     */
    private $tokenService;

    /**
     * JibitService constructor.
     * Initializes the service with the access token and refresh token.
     */
    public function __construct()
    {
        $this->tokenService = app(\App\Services\TokenService::class);
        
        // Get tokens from TokenService
        $this->token = $this->tokenService->getAccessToken(Token::PROVIDER_JIBIT);
        $this->refreshToken = $this->tokenService->getRefreshToken(Token::PROVIDER_JIBIT);
        
        // Get API credentials from config or environment
        $this->apiKey = config('services.jibit.api_key', env('JIBIT_API_KEY'));
        $this->secretKey = config('services.jibit.secret_key', env('JIBIT_SECRET_KEY'));
    }

    /**
     * Generate a new access token.
     *
     * @return object|null
     */
    public function generateToken(): ?object
    {
        $success = $this->tokenService->generateJibitToken();
        
        if ($success) {
            // Update local tokens
            $this->token = $this->tokenService->getAccessToken(Token::PROVIDER_JIBIT);
            $this->refreshToken = $this->tokenService->getRefreshToken(Token::PROVIDER_JIBIT);
            
            return (object) [
                'accessToken' => $this->token,
                'refreshToken' => $this->refreshToken
            ];
        }

        return null;
    }

    /**
     * Refresh the access token using the refresh token.
     *
     * @return object|null
     */
    public function refreshToken(): ?object
    {
        $success = $this->tokenService->refreshJibitToken();
        
        if ($success) {
            // Update local tokens
            $this->token = $this->tokenService->getAccessToken(Token::PROVIDER_JIBIT);
            $this->refreshToken = $this->tokenService->getRefreshToken(Token::PROVIDER_JIBIT);
            
            return (object) [
                'accessToken' => $this->token,
                'refreshToken' => $this->refreshToken
            ];
        }

        return null;
    }

    /**
     * Make an API request to the Jibit API.
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
            try {
                $url = self::API_BASE_URL . $endpoint;
                $trackId = $this->generateTrackId();
                
                // Prepare headers
                $headers = [
                    'X-TRACK-ID' => $trackId,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ];

                if ($useToken) {
                    $headers['Authorization'] = 'Bearer ' . ($customToken ?? $this->token);
                }

                // Create HTTP client with timeout, headers and proxy
                $httpClient = Http::timeout(100)
                    ->withHeaders($headers);
                    // ->withOptions(['proxy' => 'socks5://127.0.0.1:1090']);

                if ($method === 'POST') {
                    $response = $httpClient->post($url, $params);
                } else {
                    if (!empty($params)) {
                        $response = $httpClient->get($url, $params);
                    } else {
                        $response = $httpClient->get($url);
                    }
                }

                if ($response->successful()) {
                    $responseData = $response->object();
                    
                    if ($responseData) {
                        return $responseData;
                    }
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
        Log::error('Jibit API request failed', $failedRequest);
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
} 