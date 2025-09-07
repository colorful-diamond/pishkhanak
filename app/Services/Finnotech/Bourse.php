<?php

namespace App\Services\Finnotech;

use App\Exceptions\FinnotechException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class Bourse
{
    private const MAX_RETRIES = 2;
    private const REDIS_FAILED_REQUESTS_KEY = 'finnotech:bourse:failed_requests';

    private Token $tokenService;
    private string $baseUrl;

    public function __construct(Token $tokenService)
    {
        $this->tokenService = $tokenService;
        $this->baseUrl = $tokenService->getBaseUrl();
    }

    /**
     * Get balanced trades by ID.
     *
     * @param string $token
     * @param string $id
     * @return array
     * @throws FinnotechException
     */
    public function getBalancedTradesById(string $token, string $id): array
    {
        $endpoint = "/bourse/v2/trades/balanced/{$id}";
        return $this->makeRequest('GET', $endpoint, [], ['Authorization' => "Bearer {$token}"]);
    }

    /**
     * Get daily trades.
     *
     * @param string $token
     * @param string $date
     * @return array
     * @throws FinnotechException
     */
    public function getDailyTrades(string $token, string $date): array
    {
        $endpoint = "/bourse/v2/trades/daily/{$date}";
        return $this->makeRequest('GET', $endpoint, [], ['Authorization' => "Bearer {$token}"]);
    }

    /**
     * Get balanced trades.
     *
     * @param string $token
     * @param string $date
     * @return array
     * @throws FinnotechException
     */
    public function getBalancedTrades(string $token, string $date): array
    {
        $endpoint = "/bourse/v2/trades/balanced/{$date}";
        return $this->makeRequest('GET', $endpoint, [], ['Authorization' => "Bearer {$token}"]);
    }

    /**
     * Get company assessment.
     *
     * @param string $token
     * @param string $symbol
     * @return array
     * @throws FinnotechException
     */
    public function getCompanyAssessment(string $token, string $symbol): array
    {
        $endpoint = "/bourse/v2/companies/assessment/{$symbol}";
        return $this->makeRequest('GET', $endpoint, [], ['Authorization' => "Bearer {$token}"]);
    }

    /**
     * Get company inquiry by ID.
     *
     * @param string $token
     * @param string $id
     * @return array
     * @throws FinnotechException
     */
    public function getCompanyInquiry(string $token, string $id): array
    {
        $endpoint = "/bourse/v2/companies/{$id}/inquiry";
        return $this->makeRequest('GET', $endpoint, [], ['Authorization' => "Bearer {$token}"]);
    }

    /**
     * Get real-time trades.
     *
     * @param string $token
     * @param array $symbols
     * @return array
     * @throws FinnotechException
     */
    public function getRealTimeTrades(string $token, array $symbols): array
    {
        $endpoint = "/bourse/v2/trades/realtime";
        $queryParams = http_build_query(['symbols' => implode(',', $symbols)]);
        $endpoint .= "?{$queryParams}";
        return $this->makeRequest('GET', $endpoint, [], ['Authorization' => "Bearer {$token}"]);
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
        $url = $this->baseUrl . $endpoint;
        $headers = array_merge([
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
        Log::error('Finnotech Bourse API request failed', $failedRequest);
    }

    /**
     * Get an SMS authentication token.
     *
     * @param string $code
     * @param string $redirectUri
     * @return array
     * @throws FinnotechException
     */
    public function getSmsAuthToken(string $code, string $redirectUri): array
    {
        return $this->tokenService->getAuthorizationCodeSmsToken($code, $redirectUri);
    }

    /**
     * Generate the authorization URL for SMS authentication.
     *
     * @param string $redirectUri
     * @param array $scopes
     * @param string $bank
     * @param int|null $limit
     * @param int|null $count
     * @param string|null $state
     * @return string
     */
    public function getSmsAuthorizationUrl(
        string $redirectUri,
        array $scopes,
        string $bank,
        ?int $limit = null,
        ?int $count = null,
        ?string $state = null
    ): string {
        return $this->tokenService->getAuthorizationUrl($redirectUri, $scopes, $bank, $limit, $count, $state);
    }

    /**
     * Get a client credential token.
     *
     * @return array
     * @throws FinnotechException
     */
    public function getClientCredentialToken(): array
    {
        return $this->tokenService->getClientCredentialToken();
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
        return $this->tokenService->refreshToken($refreshToken, $tokenType, $bank, $authType);
    }
}