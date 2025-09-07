<?php

namespace App\Services\Jibit;

use App\Services\Jibit\JibitService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class Token
 * 
 * Handles Jibit authentication token operations.
 */
class Token
{
    /**
     * @var JibitService
     */
    protected $jibitService;

    /**
     * Token constructor.
     *
     * @param JibitService $jibitService
     */
    public function __construct(JibitService $jibitService)
    {
        $this->jibitService = $jibitService;
    }

    /**
     * Generate a new access token.
     * Each token is valid for 24 hours.
     *
     * @return object|null
     */
    public function generate(): ?object
    {
        try {
            return $this->jibitService->generateToken();
        } catch (Exception $e) {
            Log::error('Error generating Jibit token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Refresh an expired access token using a refresh token.
     * Returns a new token valid for 24 hours and new refresh token valid for 48 hours.
     *
     * @return object|null
     */
    public function refresh(): ?object
    {
        try {
            return $this->jibitService->refreshToken();
        } catch (Exception $e) {
            Log::error('Error refreshing Jibit token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the current access token.
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->jibitService->getToken();
    }

    /**
     * Get the current refresh token.
     *
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->jibitService->getRefreshToken();
    }

    /**
     * Check if the current token is valid by making a test request.
     *
     * @return bool
     */
    public function isTokenValid(): bool
    {
        try {
            // Test token validity by making a simple API call
            $response = $this->jibitService->makeApiRequest('/v1/balances');
            return $response !== null && !isset($response->error);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Ensure we have a valid token, refresh if necessary.
     *
     * @return bool
     */
    public function ensureValidToken(): bool
    {
        if ($this->isTokenValid()) {
            return true;
        }

        // Try to refresh the token
        $refreshResult = $this->refresh();
        if ($refreshResult && isset($refreshResult->accessToken)) {
            return true;
        }

        // If refresh fails, try to generate a new token
        $generateResult = $this->generate();
        return $generateResult && isset($generateResult->accessToken);
    }

    // I will add Token related methods here once I have the API documentation.
} 