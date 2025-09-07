<?php

namespace App\Services\Finnotech;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Exceptions\FinnotechException;

class Token
{
    private const BASE_URL = 'https://api.finnotech.ir';
    private const SANDBOX_URL = 'https://sandboxapi.finnotech.ir';
    private const MAX_RETRIES = 2;
    private const REDIS_FAILED_REQUESTS_KEY = 'finnotech:failed_requests';

    private $clientId;
    private $clientSecret;
    private $isSandbox;

    public function __construct(string $clientId, string $clientSecret, bool $isSandbox = false)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->isSandbox = $isSandbox;
    }

    /**
     * Get the base URL for API requests.
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->isSandbox ? self::SANDBOX_URL : self::BASE_URL;
    }

    /**
     * Generate the Authorization header for API requests.
     *
     * @return string
     */
    private function getAuthorizationHeader(): string
    {
        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");
        return "Basic {$credentials}";
    }

    /**
     * Make an HTTP request with retry logic and error handling.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return array
     * @throws FinnotechException
     */
    private function makeRequest(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $url = $this->getBaseUrl() . $endpoint;
        $headers = array_merge([
            'Authorization' => $this->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ], $headers);

        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $response = Http::withHeaders($headers)->$method($url, $data);
                
                if ($response->successful()) {
                    return $response->json();
                }

                if ($attempt === self::MAX_RETRIES) {
                    $this->logFailedRequest($method, $endpoint, $data, $response->json());
                    throw new FinnotechException("API request failed: " . $response->body());
                }
            } catch (\Exception $e) {
                if ($attempt === self::MAX_RETRIES) {
                    $this->logFailedRequest($method, $endpoint, $data, ['error' => $e->getMessage()]);
                    throw new FinnotechException("API request failed: " . $e->getMessage());
                }
            }

            // Wait before retrying (exponential backoff)
            sleep(2 ** ($attempt - 1));
        }

        // This should never be reached due to the exception throwing above,
        // but included for completeness
        throw new FinnotechException("Max retries reached for API request");
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
     * Get an authorization code token for SMS authentication.
     *
     * @param string $code
     * @param string $redirectUri
     * @return array
     * @throws FinnotechException
     */
    public function getAuthorizationCodeSmsToken(string $code, string $redirectUri): array
    {
        $data = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'auth_type' => 'SMS',
            'redirect_uri' => $redirectUri,
        ];

        return $this->makeRequest('post', '/dev/v2/oauth2/token', $data);
    }

    /**
     * Get an authorization code token for legal entities.
     *
     * @param string $code
     * @param string $redirectUri
     * @return array
     * @throws FinnotechException
     */
    public function getAuthorizationCodeLegalToken(string $code, string $redirectUri): array
    {
        $data = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'auth_type' => 'LEGAL',
            'redirect_uri' => $redirectUri,
        ];

        return $this->makeRequest('post', '/dev/v2/oauth2/token', $data);
    }

    /**
     * Get a client credential token.
     *
     * @return array
     * @throws FinnotechException
     */
    public function getClientCredentialToken(): array
    {
        $data = [
            'grant_type' => 'client_credentials',
        ];

        return $this->makeRequest('post', '/dev/v2/oauth2/token', $data);
    }

    /**
     * Refresh an existing token.
     *
     * @param string $refreshToken
     * @param string $tokenType
     * @param string|null $bank
     * @param string|null $authType
     * @return array
     * @throws FinnotechException
     */
    public function refreshToken(string $refreshToken, string $tokenType, ?string $bank = null, ?string $authType = null): array
    {
        $data = [
            'grant_type' => 'refresh_token',
            'token_type' => $tokenType,
            'refresh_token' => $refreshToken,
        ];

        if ($bank) {
            $data['bank'] = $bank;
        }

        if ($authType) {
            $data['auth_type'] = $authType;
        }

        return $this->makeRequest('post', '/dev/v2/oauth2/token', $data);
    }

    /**
     * Revoke an existing token.
     *
     * @param string $token
     * @param string $tokenType
     * @param string|null $bank
     * @param string|null $authType
     * @return array
     * @throws FinnotechException
     */
    public function revokeToken(string $token, string $tokenType, ?string $bank = null, ?string $authType = null): array
    {
        $data = [
            'token' => $token,
            'token_type' => $tokenType,
        ];

        if ($bank) {
            $data['bank'] = $bank;
        }

        if ($authType) {
            $data['auth_type'] = $authType;
        }

        return $this->makeRequest('delete', "/dev/v2/clients/{$this->clientId}/token", $data);
    }

    /**
     * Get a list of tokens for the client.
     *
     * @param string|null $bank
     * @return array
     * @throws FinnotechException
     */
    public function getTokens(?string $bank = null): array
    {
        $endpoint = "/dev/v2/clients/{$this->clientId}/tokens";
        
        if ($bank) {
            $endpoint .= "?bank={$bank}";
        }

        return $this->makeRequest('get', $endpoint);
    }

    /**
     * Get wages (transaction fees) for the client.
     *
     * @param string|null $fromDate
     * @param string|null $toDate
     * @param string|null $status
     * @param string|null $trackId
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     * @throws FinnotechException
     */
    public function getWages(
        ?string $fromDate = null,
        ?string $toDate = null,
        ?string $status = null,
        ?string $trackId = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $queryParams = array_filter([
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'status' => $status,
            'trackId' => $trackId,
            'limit' => $limit,
            'offset' => $offset,
        ]);

        $endpoint = "/dev/v2/clients/{$this->clientId}/wages?" . http_build_query($queryParams);

        return $this->makeRequest('get', $endpoint);
    }

    /**
     * Generate the authorization URL for the user to grant access.
     *
     * @param string $redirectUri
     * @param array $scopes
     * @param string $bank
     * @param int|null $limit
     * @param int|null $count
     * @param string|null $state
     * @return string
     */
    public function getAuthorizationUrl(
        string $redirectUri,
        array $scopes,
        string $bank,
        ?int $limit = null,
        ?int $count = null,
        ?string $state = null
    ): string {
        $queryParams = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'scope' => implode(',', $scopes),
            'bank' => $bank,
        ];

        if ($limit !== null) {
            $queryParams['limit'] = $limit;
        }

        if ($count !== null) {
            $queryParams['count'] = $count;
        }

        if ($state !== null) {
            $queryParams['state'] = $state;
        }

        return $this->getBaseUrl() . '/dev/v2/oauth2/authorize?' . http_build_query($queryParams);
    }

    /**
     * Exchange an authorization code for an access token.
     *
     * @param string $code
     * @param string $redirectUri
     * @param string $bank
     * @return array
     * @throws FinnotechException
     */
    public function exchangeAuthorizationCode(string $code, string $redirectUri, string $bank): array
    {
        $data = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'bank' => $bank,
            'redirect_uri' => $redirectUri,
        ];

        return $this->makeRequest('post', '/dev/v2/oauth2/token', $data);
    }

    /**
     * Set SMS authorization token.
     * This method should be called after successful SMS authorization.
     *
     * @param string $accessToken
     * @param int $expiresIn
     * @return bool
     */
    public function setAuthorizationSmsToken(string $accessToken, int $expiresIn = 3600): bool
    {
        try {
            // Store the SMS token in Redis with expiration
            $tokenData = [
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => $expiresIn,
                'expires_at' => time() + $expiresIn,
                'created_at' => time()
            ];
            
            // Store in Redis with automatic expiration
            $redisKey = "finnotech:sms_token:{$this->clientId}:" . time();
            \Illuminate\Support\Facades\Redis::setex($redisKey, $expiresIn, json_encode($tokenData));
            
            Log::info('SMS token stored in Redis', [
                'client_id' => $this->clientId,
                'expires_in' => $expiresIn,
                'redis_key' => $redisKey
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to store SMS token in Redis', [
                'client_id' => $this->clientId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Set current SMS token for immediate use.
     * This is used when we have an SMS token from middleware.
     *
     * @param string $smsToken
     * @return void
     */
    public function setCurrentSmsToken(string $smsToken): void
    {
        $this->currentSmsToken = $smsToken;
    }

    /**
     * @var string|null Current SMS token for this request
     */
    private $currentSmsToken;

    /**
     * Get SMS authorization token for authenticated API calls.
     * This method should return the stored SMS token for the authenticated user.
     *
     * @return string
     * @throws FinnotechException
     */
    public function getAuthorizationSmsToken(): string
    {
        // First check if we have a current SMS token set
        if ($this->currentSmsToken) {
            return $this->currentSmsToken;
        }

        // This method needs to be integrated with your SMS authorization flow
        // For now, we need to implement proper SMS token retrieval
        
        // Check if we have an SMS token stored in Redis or session
        $smsToken = $this->getSmsTokenFromStorage();
        
        if (!$smsToken) {
            throw new FinnotechException("No SMS authorization token found. Please complete SMS authorization first.");
        }
        
        return $smsToken;
    }

    /**
     * Get SMS token from storage (Redis/Session).
     * This is a placeholder implementation that should be integrated with your SMS auth system.
     *
     * @return string|null
     */
    private function getSmsTokenFromStorage(): ?string
    {
        try {
            // Get SMS token from Redis using a pattern based on client ID
            $redisKey = "finnotech:sms_token:{$this->clientId}:*";
            $keys = \Illuminate\Support\Facades\Redis::keys($redisKey);
            
            if (empty($keys)) {
                return null;
            }

            // Get the most recent token (if multiple exist)
            $tokenData = null;
            $latestTime = 0;

            foreach ($keys as $key) {
                $tokenJson = \Illuminate\Support\Facades\Redis::get($key);
                if ($tokenJson) {
                    $data = json_decode($tokenJson, true);
                    if ($data && isset($data['created_at'])) {
                        $createdAt = is_numeric($data['created_at']) ? $data['created_at'] : strtotime($data['created_at']);
                        if ($createdAt > $latestTime) {
                            $latestTime = $createdAt;
                            $tokenData = $data;
                        }
                    }
                }
            }

            if ($tokenData) {
                // Check if token is expired
                if (isset($tokenData['expires_at'])) {
                    $expiresAt = is_numeric($tokenData['expires_at']) 
                        ? $tokenData['expires_at'] 
                        : strtotime($tokenData['expires_at']);
                    
                    if ($expiresAt < time()) {
                        // Token is expired, don't return it
                        return null;
                    }
                }

                return $tokenData['access_token'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get SMS token from Redis storage', [
                'client_id' => $this->clientId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}