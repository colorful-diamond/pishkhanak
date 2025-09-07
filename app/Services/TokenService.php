<?php

namespace App\Services;

use App\Models\Token;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Exception;

class TokenService
{
    /**
     * Jibit API configuration
     */
    private const JIBIT_BASE_URL = 'https://napi.jibit.ir/ide';
    private const JIBIT_VERSION = 'v1';

    /**
     * Finnotech API configuration
     */
    private const FINNOTECH_BASE_URL = 'https://api.finnotech.ir';
    private const FINNOTECH_VERSION = 'v2';

    /**
     * Cache TTL for tokens in seconds (5 minutes)
     */
    private const CACHE_TTL = 300;

    /**
     * Get active access token for a provider with Redis caching
     *
     * @param string $provider 'jibit' or 'finnotech'
     * @param string|null $tokenName Specific token name for category-specific tokens
     * @return string|null
     */
    public function getAccessToken(string $provider, ?string $tokenName = null): ?string
    {
        // If a specific token name is provided, use it instead of provider-based lookup
        if ($tokenName) {
            $cacheKey = "access_token:{$tokenName}";
            
            return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($tokenName) {
                $token = Token::getByName($tokenName);
                
                if (!$token) {
                    return null;
                }

                $accessToken = $token->getActiveAccessToken();
                
                // If token is expired or needs refresh, try to refresh it
                if (!$accessToken || $token->needsRefresh()) {
                    if ($this->refreshTokenByName($tokenName)) {
                        $token->refresh();
                        return $token->getActiveAccessToken();
                    }
                }

                return $accessToken;
            });
        }

        // Original provider-based logic
        $cacheKey = "access_token:{$provider}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($provider) {
            $token = Token::getByProvider($provider);
            
            if (!$token) {
                return null;
            }

            $accessToken = $token->getActiveAccessToken();
            
            // If token is expired or needs refresh, try to refresh it
            if (!$accessToken || $token->needsRefresh()) {
                if ($this->refreshToken($provider)) {
                    $token->refresh();
                    return $token->getActiveAccessToken();
                }
            }

            return $accessToken;
        });
    }

    /**
     * Get refresh token for a provider or by token name
     *
     * @param string $provider
     * @param string|null $tokenName
     * @return string|null
     */
    public function getRefreshToken(string $provider, ?string $tokenName = null): ?string
    {
        if ($tokenName) {
            $token = Token::getByName($tokenName);
        } else {
            $token = Token::getByProvider($provider);
        }
        
        return $token ? $token->getActiveRefreshToken() : null;
    }

    /**
     * Generate new access token for Jibit
     *
     * @return bool
     */
    public function generateJibitToken(): bool
    {
        try {

            Log::info('Generating Jibit token...');
            $apiKey = config('services.jibit.api_key');
            $secretKey = config('services.jibit.secret_key');

            if (!$apiKey || !$secretKey) {
                Log::error('Jibit API credentials not configured');
                return false;
            }

            $response = $this->makeJibitRequest('/v1/tokens/generate', [
                'apiKey' => $apiKey,
                'secretKey' => $secretKey
            ], 'POST', false);

            Log::info('Jibit token generated successfully', ['response' => $response]);

            if ($response && isset($response->accessToken)) {
                $this->saveJibitToken(
                    $response->accessToken,
                    $response->refreshToken,
                    isset($response->expiresIn) ? now()->addSeconds($response->expiresIn) : now()->addHours(24),
                    isset($response->refreshExpiresIn) ? now()->addSeconds($response->refreshExpiresIn) : now()->addHours(48)
                );

                Log::info('Jibit token generated successfully', ['response' => $response]);

                $this->clearTokenCache(Token::PROVIDER_JIBIT);
                return true;
            }

            Log::error('Failed to generate Jibit token', ['response' => $response]);
            return false;
        } catch (Exception $e) {
            Log::error('Error generating Jibit token: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Refresh access token for a provider
     *
     * @param string $provider
     * @return bool
     */
    public function refreshToken(string $provider): bool
    {
        switch ($provider) {
            case Token::PROVIDER_JIBIT:
                return $this->refreshJibitToken();
            case Token::PROVIDER_FINNOTECH:
                return $this->refreshFinnotechToken();
            default:
                Log::error("Unknown provider: {$provider}");
                return false;
        }
    }

    /**
     * Refresh access token by token name
     *
     * @param string $tokenName
     * @return bool
     */
    public function refreshTokenByName(string $tokenName): bool
    {
        // Check if this is a Finnotech category token
        if (str_starts_with($tokenName, 'fino_')) {
            return $this->refreshFinnotechCategoryToken($tokenName);
        }

        // Handle other token types
        switch ($tokenName) {
            case Token::NAME_JIBIT:
                return $this->refreshJibitToken();
            case Token::NAME_FINNOTECH:
                return $this->refreshFinnotechToken();
            default:
                Log::error("Unknown token name: {$tokenName}");
                return false;
        }
    }

    /**
     * Refresh Jibit token
     *
     * @return bool
     */
    public function refreshJibitToken(): bool
    {
        try {
            $token = Token::getByName(Token::NAME_JIBIT);
            
            // if (!$token || !$token->getActiveRefreshToken()) {
            //     Log::warning('No valid Jibit refresh token found, generating new token');
                return $this->generateJibitToken();
            // }

            $response = $this->makeJibitRequest('/v1/tokens/refresh', [
                'accessToken' => $token->access_token,
                'refreshToken' => $token->refresh_token
            ], 'POST', false);

            Log::info('Jibit token refreshed successfully', ['response' => $response]);

            if ($response && isset($response->accessToken)) {
                $this->saveJibitToken(
                    $response->accessToken,
                    $response->refreshToken,
                    isset($response->expiresIn) ? now()->addSeconds($response->expiresIn) : now()->addHours(24),
                    isset($response->refreshExpiresIn) ? now()->addSeconds($response->refreshExpiresIn) : now()->addHours(48)
                );

                $this->clearTokenCache(Token::PROVIDER_JIBIT);
                Log::info('Jibit token refreshed successfully');
                return true;
            }

            Log::error('Failed to refresh Jibit token', ['response' => $response]);
            
            // If refresh fails, try to generate new token
            return $this->generateJibitToken();
        } catch (Exception $e) {
            Log::error('Error refreshing Jibit token: ' . $e->getMessage());
            return $this->generateJibitToken();
        }
    }

    /**
     * Refresh Finnotech category token
     *
     * @param string $tokenName
     * @return bool
     */
    public function refreshFinnotechCategoryToken(string $tokenName): bool
    {
        try {
            $token = Token::getByName($tokenName);
            
            if (!$token || !$token->getActiveRefreshToken()) {
                Log::warning("No valid Finnotech refresh token found for {$tokenName}, generating new token");
                return $this->generateFinnotechCategoryToken($tokenName);
            }

            // Extract category from token name (e.g., 'fino_inquiry' -> 'inquiry')
            $category = str_replace('fino_', '', $tokenName);
            
            // Determine token type and required parameters based on category
            $tokenType = 'CLIENT-CREDENTIAL'; // Default for most categories
            $authType = null;
            $bank = null;
            
            // For SMS-related tokens, use CODE type with auth_type
            if ($category === 'sms') {
                $tokenType = 'CODE';
                $authType = 'SMS';
            } else {
                // For other categories, determine bank code based on category
                $bank = $this->getBankCodeForCategory($category);
            }

            $requestData = [
                'grant_type' => 'refresh_token',
                'token_type' => $tokenType,
                'refresh_token' => $token->refresh_token,
            ];

            // Add bank code if required
            if ($bank) {
                $requestData['bank'] = $bank;
            }

            // Add auth_type for SMS tokens
            if ($authType) {
                $requestData['auth_type'] = $authType;
            }

            $response = $this->makeFinnotechRequest('/dev/v2/oauth2/token', $requestData, 'POST', false);

            // Handle response according to Finnotech documentation
            if ($response && isset($response['result']) && isset($response['result']['value'])) {
                $result = $response['result'];
                
                $this->saveFinnotechCategoryToken(
                    $tokenName,
                    $result['value'], // access_token is in 'value' field
                    $result['refreshToken'] ?? $token->refresh_token,
                    isset($result['lifeTime']) ? now()->addSeconds($result['lifeTime'] / 1000) : now()->addHours(24),
                    now()->addHours(48)
                );

                $this->clearTokenCacheByName($tokenName);
                Log::info("Finnotech category token {$tokenName} refreshed successfully");
                return true;
            }

            Log::error("Failed to refresh Finnotech category token {$tokenName}", ['response' => $response]);
            
            // If refresh fails, try to generate new token
            return $this->generateFinnotechCategoryToken($tokenName);
        } catch (Exception $e) {
            Log::error("Error refreshing Finnotech category token {$tokenName}: " . $e->getMessage());
            return $this->generateFinnotechCategoryToken($tokenName);
        }
    }

    /**
     * Generate new access token for Finnotech
     *
     * @return bool
     */
    public function generateFinnotechToken(): bool
    {
        try {
            // Finnotech uses client credentials grant
            $response = $this->makeFinnotechRequest('/dev/v2/oauth2/token', [
                'grant_type' => 'client_credentials',
                'token_type' => 'CLIENT-CREDENTIAL'
            ], 'POST', false);

            // Handle response according to Finnotech documentation
            if ($response && isset($response['result']) && isset($response['result']['value'])) {
                $result = $response['result'];
                
                $this->saveFinnotechToken(
                    $result['value'], // access_token is in 'value' field
                    $result['refreshToken'] ?? '',
                    isset($result['lifeTime']) ? now()->addSeconds($result['lifeTime'] / 1000) : now()->addHours(24),
                    now()->addHours(48) // Default refresh expiry
                );

                $this->clearTokenCache(Token::PROVIDER_FINNOTECH);
                return true;
            }

            Log::error('Failed to generate Finnotech token', ['response' => $response]);
            return false;
        } catch (Exception $e) {
            Log::error('Error generating Finnotech token: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate new access token for Finnotech category
     *
     * @param string $tokenName
     * @return bool
     */
    public function generateFinnotechCategoryToken(string $tokenName): bool
    {
        try {
            // Extract category from token name (e.g., 'fino_inquiry' -> 'inquiry')
            $category = str_replace('fino_', '', $tokenName);
            
            // Determine token type and required parameters based on category
            $tokenType = 'CLIENT-CREDENTIAL'; // Default for most categories
            $authType = null;
            $bank = null;
            
            // For SMS-related tokens, use CODE type with auth_type
            if ($category === 'sms') {
                $tokenType = 'CODE';
                $authType = 'SMS';
            } else {
                // For other categories, determine bank code based on category
                $bank = $this->getBankCodeForCategory($category);
            }

            $requestData = [
                'grant_type' => 'client_credentials',
                'token_type' => $tokenType,
            ];

            // Add bank code if required
            if ($bank) {
                $requestData['bank'] = $bank;
            }

            // Add auth_type for SMS tokens
            if ($authType) {
                $requestData['auth_type'] = $authType;
            }

            $response = $this->makeFinnotechRequest('/dev/v2/oauth2/token', $requestData, 'POST', false);

            // Handle response according to Finnotech documentation
            if ($response && isset($response['result']) && isset($response['result']['value'])) {
                $result = $response['result'];
                
                $this->saveFinnotechCategoryToken(
                    $tokenName,
                    $result['value'], // access_token is in 'value' field
                    $result['refreshToken'] ?? '',
                    isset($result['lifeTime']) ? now()->addSeconds($result['lifeTime'] / 1000) : now()->addHours(24),
                    now()->addHours(48) // Default refresh expiry
                );

                $this->clearTokenCacheByName($tokenName);
                Log::info("Generated new Finnotech category token for {$tokenName}");
                return true;
            }

            Log::error("Failed to generate Finnotech category token for {$tokenName}", ['response' => $response]);
            return false;
        } catch (Exception $e) {
            Log::error("Error generating Finnotech category token for {$tokenName}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Refresh Finnotech token (original method)
     *
     * @return bool
     */
    public function refreshFinnotechToken(): bool
    {
        try {
            $token = Token::getByName(Token::NAME_FINNOTECH);
            
            if (!$token || !$token->getActiveRefreshToken()) {
                Log::warning('No valid Finnotech refresh token found, generating new token');
                return $this->generateFinnotechToken();
            }

            // For main Finnotech token, use CLIENT-CREDENTIAL type
            $requestData = [
                'grant_type' => 'refresh_token',
                'token_type' => 'CLIENT-CREDENTIAL',
                'refresh_token' => $token->refresh_token,
            ];

            $response = $this->makeFinnotechRequest('/dev/v2/oauth2/token', $requestData, 'POST', false);

            // Handle response according to Finnotech documentation
            if ($response && isset($response['result']) && isset($response['result']['value'])) {
                $result = $response['result'];
                
                $this->saveFinnotechToken(
                    $result['value'], // access_token is in 'value' field
                    $result['refreshToken'] ?? $token->refresh_token,
                    isset($result['lifeTime']) ? now()->addSeconds($result['lifeTime'] / 1000) : now()->addHours(24),
                    now()->addHours(48)
                );

                $this->clearTokenCache(Token::PROVIDER_FINNOTECH);
                Log::info('Finnotech token refreshed successfully');
                return true;
            }

            Log::error('Failed to refresh Finnotech token', ['response' => $response]);
            
            // If refresh fails, try to generate new token
            return $this->generateFinnotechToken();
        } catch (Exception $e) {
            Log::error('Error refreshing Finnotech token: ' . $e->getMessage());
            return $this->generateFinnotechToken();
        }
    }

    /**
     * Save Jibit token to database
     *
     * @param string $accessToken
     * @param string $refreshToken
     * @param Carbon $expiresAt
     * @param Carbon $refreshExpiresAt
     */
    private function saveJibitToken(string $accessToken, string $refreshToken, Carbon $expiresAt, Carbon $refreshExpiresAt): void
    {
        Token::createOrUpdate(
            Token::PROVIDER_JIBIT,
            Token::NAME_JIBIT,
            $accessToken,
            $refreshToken,
            $expiresAt,
            $refreshExpiresAt,
            [
                'api_version' => self::JIBIT_VERSION,
                'base_url' => self::JIBIT_BASE_URL
            ]
        );
    }

    /**
     * Save Finnotech token to database
     *
     * @param string $accessToken
     * @param string $refreshToken
     * @param Carbon $expiresAt
     * @param Carbon $refreshExpiresAt
     */
    private function saveFinnotechToken(string $accessToken, string $refreshToken, Carbon $expiresAt, Carbon $refreshExpiresAt): void
    {
        Token::createOrUpdate(
            Token::PROVIDER_FINNOTECH,
            Token::NAME_FINNOTECH,
            $accessToken,
            $refreshToken,
            $expiresAt,
            $refreshExpiresAt,
            [
                'api_version' => self::FINNOTECH_VERSION,
                'base_url' => self::FINNOTECH_BASE_URL
            ]
        );
    }

    /**
     * Save Finnotech category token to database
     *
     * @param string $tokenName
     * @param string $accessToken
     * @param string $refreshToken
     * @param Carbon $expiresAt
     * @param Carbon $refreshExpiresAt
     */
    private function saveFinnotechCategoryToken(string $tokenName, string $accessToken, string $refreshToken, Carbon $expiresAt, Carbon $refreshExpiresAt): void
    {
        Token::createOrUpdate(
            Token::PROVIDER_FINNOTECH,
            $tokenName,
            $accessToken,
            $refreshToken,
            $expiresAt,
            $refreshExpiresAt,
            [
                'api_version' => self::FINNOTECH_VERSION,
                'base_url' => self::FINNOTECH_BASE_URL,
                'category' => str_replace('fino_', '', $tokenName)
            ]
        );
    }

    /**
     * Make API request to Jibit
     *
     * @param string $endpoint
     * @param array $data
     * @param string $method
     * @param bool $useAuth
     * @return object|null
     */
    private function makeJibitRequest(string $endpoint, array $data = [], string $method = 'GET', bool $useAuth = true): ?object
    {
        try {
            $url = self::JIBIT_BASE_URL . $endpoint;
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-TRACK-ID' => $this->generateTrackId(),
            ];

            if ($useAuth) {
                $accessToken = $this->getAccessToken(Token::PROVIDER_JIBIT);
                if ($accessToken) {
                    $headers['Authorization'] = "Bearer {$accessToken}";
                }
            }

            $response = Http::timeout(30)
                ->withHeaders($headers);
                // ->withOptions([
                //     'proxy' => 'socks5://127.0.0.1:1090'
                // ]);

            if ($method === 'POST') {
                $response = $response->post($url, $data);
            } else {
                $response = $response->get($url, $data);
            }

            if ($response->successful()) {
                return $response->object();
            }

            Log::error("Jibit API request failed", [
                'url' => $url,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (Exception $e) {
            Log::error("Jibit API request failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Make API request to Finnotech
     *
     * @param string $endpoint
     * @param array $data
     * @param string $method
     * @param bool $useAuth
     * @return array|null
     */
    private function makeFinnotechRequest(string $endpoint, array $data = [], string $method = 'GET', bool $useAuth = true): ?array
    {
        try {
            $url = self::FINNOTECH_BASE_URL . $endpoint;
            
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];
            
            if ($useAuth) {
                $accessToken = $this->getAccessToken(Token::PROVIDER_FINNOTECH);
                if ($accessToken) {
                    $headers['Authorization'] = "Bearer {$accessToken}";
                }
            } else {
                // Use basic auth for token requests
                $clientId = config('services.finnotech.client_id', 'pishkhanak');
                $clientSecret = config('services.finnotech.client_secret', 'EB9Kx6Z5FUiWgiD1N9z9');
                $credentials = base64_encode("{$clientId}:{$clientSecret}");
                $headers['Authorization'] = "Basic {$credentials}";
            }

            $response = Http::withHeaders($headers);
            
            if ($method === 'POST') {
                $response = $response->post($url, $data);
            } else {
                $response = $response->get($url, $data);
            }
            
            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Finnotech API request failed", [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $headers
            ]);
            
            return null;
        } catch (Exception $e) {
            Log::error("Finnotech API request failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Clear token cache for a provider or token name
     *
     * @param string $provider
     * @param string|null $tokenName
     */
    private function clearTokenCache(string $provider, ?string $tokenName = null): void
    {
        if ($tokenName) {
            $this->clearTokenCacheByName($tokenName);
        } else {
            Cache::forget("access_token:{$provider}");
            Cache::forget("token:provider:{$provider}");
            
            // Clear by name as well
            $name = $provider === Token::PROVIDER_JIBIT ? Token::NAME_JIBIT : Token::NAME_FINNOTECH;
            Cache::forget("token:name:{$name}");
        }
    }

    /**
     * Clear token cache by token name
     *
     * @param string $tokenName
     */
    private function clearTokenCacheByName(string $tokenName): void
    {
        Cache::forget("access_token:{$tokenName}");
        Cache::forget("token:name:{$tokenName}");
    }

    /**
     * Ensure valid token for provider or token name
     *
     * @param string $provider
     * @param string|null $tokenName
     * @return bool
     */
    public function ensureValidToken(string $provider, ?string $tokenName = null): bool
    {
        $accessToken = $this->getAccessToken($provider, $tokenName);
        
        if (!$accessToken) {
            // Try to generate/refresh token
            if ($tokenName) {
                return $this->refreshTokenByName($tokenName);
            } else {
                return $this->refreshToken($provider);
            }
        }

        return true;
    }

    /**
     * Get tokens that need refresh
     *
     * @return array
     */
    public function getTokensNeedingRefresh(): array
    {
        return Token::getTokensNeedingRefresh()->toArray();
    }

    /**
     * Refresh all tokens that need refresh
     *
     * @return array
     */
    public function refreshAllTokensNeedingRefresh(): array
    {
        $results = [];
        $tokens = Token::getTokensNeedingRefresh();

        foreach ($tokens as $token) {
            $success = $this->refreshToken($token->provider);
            $results[$token->provider] = $success;
            
            if ($success) {
                Log::info("Successfully refreshed token for provider: {$token->provider}");
            } else {
                Log::error("Failed to refresh token for provider: {$token->provider}");
            }
        }

        return $results;
    }

    /**
     * Deactivate expired tokens
     *
     * @return int Number of tokens deactivated
     */
    public function deactivateExpiredTokens(): int
    {
        $expiredTokens = Token::getExpiredTokens();
        $count = 0;

        foreach ($expiredTokens as $token) {
            $token->deactivate();
            $this->clearTokenCache($token->provider);
            $count++;
            Log::info("Deactivated expired token for provider: {$token->provider}");
        }

        return $count;
    }

    /**
     * Generate unique track ID for API requests
     *
     * @return string
     */
    private function generateTrackId(): string
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
     * Get token health status including category tokens
     *
     * @return array
     */
    public function getTokenHealthStatus(): array
    {
        $providers = [Token::PROVIDER_JIBIT, Token::PROVIDER_FINNOTECH];
        $status = [];

        // Check main providers
        foreach ($providers as $provider) {
            $token = Token::getByProvider($provider);
            
            $status[$provider] = [
                'exists' => (bool)$token,
                'active' => $token ? $token->is_active : false,
                'access_token_expired' => $token ? $token->isAccessTokenExpired() : true,
                'refresh_token_expired' => $token ? $token->isRefreshTokenExpired() : true,
                'needs_refresh' => $token ? $token->needsRefresh() : true,
                'expires_at' => $token ? $token->expires_at?->toISOString() : null,
                'refresh_expires_at' => $token ? $token->refresh_expires_at?->toISOString() : null,
                'last_used_at' => $token ? $token->last_used_at?->toISOString() : null,
            ];
        }

        // Check Finnotech category tokens
        $categoryTokens = [
            Token::NAME_FINNOTECH_INQUIRY,
            Token::NAME_FINNOTECH_CREDIT,
            Token::NAME_FINNOTECH_KYC,
            Token::NAME_FINNOTECH_TOKEN,
            Token::NAME_FINNOTECH_PROMISSORY,
            Token::NAME_FINNOTECH_VEHICLE,
            Token::NAME_FINNOTECH_INSURANCE,
            Token::NAME_FINNOTECH_SMS,
        ];

        foreach ($categoryTokens as $tokenName) {
            $token = Token::getByName($tokenName);
            
            $status[$tokenName] = [
                'exists' => (bool)$token,
                'active' => $token ? $token->is_active : false,
                'access_token_expired' => $token ? $token->isAccessTokenExpired() : true,
                'refresh_token_expired' => $token ? $token->isRefreshTokenExpired() : true,
                'needs_refresh' => $token ? $token->needsRefresh() : true,
                'expires_at' => $token ? $token->expires_at?->toISOString() : null,
                'refresh_expires_at' => $token ? $token->refresh_expires_at?->toISOString() : null,
                'last_used_at' => $token ? $token->last_used_at?->toISOString() : null,
            ];
        }

        return $status;
    }

    /**
     * Generate all missing Finnotech category tokens
     *
     * @return array
     */
    public function generateMissingFinnotechCategoryTokens(): array
    {
        $results = [];
        $categoryTokens = [
            Token::NAME_FINNOTECH_INQUIRY,
            Token::NAME_FINNOTECH_CREDIT,
            Token::NAME_FINNOTECH_KYC,
            Token::NAME_FINNOTECH_TOKEN,
            Token::NAME_FINNOTECH_PROMISSORY,
            Token::NAME_FINNOTECH_VEHICLE,
            Token::NAME_FINNOTECH_INSURANCE,
            Token::NAME_FINNOTECH_SMS,
        ];

        foreach ($categoryTokens as $tokenName) {
            $token = Token::getByName($tokenName);
            
            if (!$token || $token->isAccessTokenExpired()) {
                $success = $this->generateFinnotechCategoryToken($tokenName);
                $results[$tokenName] = $success;
                
                if ($success) {
                    Log::info("Successfully generated token for category: {$tokenName}");
                } else {
                    Log::error("Failed to generate token for category: {$tokenName}");
                }
            } else {
                $results[$tokenName] = true; // Token already exists and is valid
            }
        }

        return $results;
    }

    /**
     * Get bank code for a specific category
     * 
     * @param string $category
     * @return string|null
     */
    private function getBankCodeForCategory(string $category): ?string
    {
        // Map categories to bank codes based on your requirements
        // You may need to adjust this mapping based on your specific needs
        $categoryBankMap = [
            'inquiry' => '062', // آینده
            'credit' => '062',  // آینده
            'kyc' => '062',     // آینده
            'token' => '062',   // آینده
            'promissory' => '062', // آینده
            'vehicle' => '062', // آینده
            'insurance' => '062', // آینده
            // Add more mappings as needed
        ];

        return $categoryBankMap[$category] ?? '062'; // Default to آینده bank
    }
} 