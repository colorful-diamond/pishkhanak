<?php

namespace App\Services\Finnotech;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\SmsVerificationService;

class SmsAuthorizationService
{
    private string $clientId;
    private string $clientSecret;
    private bool $isSandbox;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.finnotech.client_id');
        $this->clientSecret = config('services.finnotech.client_secret');
        $this->isSandbox = config('services.finnotech.sandbox', false);
        $this->baseUrl = $this->isSandbox ? 'https://sandboxapi.finnotech.ir' : 'https://api.finnotech.ir';
    }

    /**
     * Step 1: Request SMS authorization (triggers SMS sending)
     */
    public function requestSmsAuthorization(string $scope, string $mobile, string $nationalId): array
    {
        try {
            // Prevent duplicate SMS requests within 30 seconds
            $duplicateKey = "sms_duplicate:" . md5($scope . '_' . $nationalId . '_' . $mobile);
            
            if (Redis::exists($duplicateKey)) {
                Log::warning('Duplicate SMS authorization request blocked', [
                    'scope' => $scope,
                    'mobile' => $mobile,
                    'national_id' => $nationalId,
                    'blocked_for' => 'duplicate_prevention'
                ]);
                
                return [
                    'success' => true,
                    'track_id' => 'duplicate_blocked',
                    'sms_sent' => false,
                    'message' => 'کد تایید قبلاً ارسال شده است'
                ];
            }

            $authHeader = base64_encode($this->clientId . ':' . $this->clientSecret);
            // dd(array(
            //     'client_id' => $this->clientId,
            //     'client_secret' => $this->clientSecret,
            //     'scope' => $scope,
            //     'auth_header' => $authHeader
            // ));
            $url = $this->baseUrl . '/dev/v2/oauth2/authorize?' . http_build_query([
                'client_id' => $this->clientId,
                'response_type' => 'code',
                'redirect_uri' => config('app.url') . '/api/auth/',
                'scope' => $scope,
                'mobile' => $mobile,
                'auth_type' => 'SMS',
            ]);
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authHeader,
                'Content-Type' => 'application/json'
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['status']) && $data['status'] === 'DONE') {
                    $trackId = $data['result']['trackId'] ?? null;
                    
                    // Store the track ID for OTP verification
                    $this->storeTrackId($scope, $nationalId, $mobile, $trackId);
                    
                    // Set duplicate prevention lock for 30 seconds
                    Redis::setex($duplicateKey, 30, time());
                    
                    Log::info('SMS authorization request successful', [
                        'scope' => $scope,
                        'mobile' => $mobile,
                        'national_id' => $nationalId,
                        'track_id' => $trackId,
                        'sms_sent' => $data['result']['smsSent'] ?? false
                    ]);

                    return [
                        'success' => true,
                        'track_id' => $trackId,
                        'sms_sent' => $data['result']['smsSent'] ?? false,
                        'message' => 'کد تایید به شماره موبایل شما ارسال شد'
                    ];
                }
            }

            Log::error('SMS authorization request failed', [
                'status_code' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک احراز هویت'
            ];

        } catch (\Exception $e) {
            Log::error('SMS authorization request exception', [
                'error' => $e->getMessage(),
                'scope' => $scope,
                'mobile' => $mobile
            ]);

            return [
                'success' => false,
                'message' => 'خطا در ارتباط با سرویس احراز هویت'
            ];
        }
    }

    /**
     * Step 2: Verify OTP and get authorization code
     */
    public function verifyOtp(string $scope, string $mobile, string $nationalId, string $otp): array
    {
        try {
            $trackId = $this->getTrackId($scope, $nationalId, $mobile);
            
            if (!$trackId) {
                return [
                    'success' => false,
                    'message' => 'جلسه احراز هویت یافت نشد. لطفاً مجدداً تلاش کنید'
                ];
            }

            $authHeader = base64_encode($this->clientId . ':' . $this->clientSecret);
            
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authHeader,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/dev/v2/oauth2/verify/sms', [
                'mobile' => $mobile,
                'otp' => $otp,
                'nid' => $nationalId,
                'trackId' => $trackId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['status']) && $data['status'] === 'DONE') {
                    $authCode = $data['result']['code'] ?? null;
                    
                    if ($authCode) {
                        // Step 3: Exchange authorization code for token
                        return $this->exchangeCodeForToken($authCode, $scope, $nationalId, $mobile);
                    }
                }
            }

            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? 'کد تایید نادرست است';

            Log::warning('OTP verification failed', [
                'scope' => $scope,
                'mobile' => $mobile,
                'national_id' => $nationalId,
                'error' => $errorMessage
            ]);

            return [
                'success' => false,
                'message' => $errorMessage
            ];

        } catch (\Exception $e) {
            Log::error('OTP verification exception', [
                'error' => $e->getMessage(),
                'scope' => $scope,
                'mobile' => $mobile
            ]);

            return [
                'success' => false,
                'message' => 'خطا در تایید کد'
            ];
        }
    }

    /**
     * Step 3: Exchange authorization code for access token
     */
    private function exchangeCodeForToken(string $authCode, string $scope, string $nationalId, string $mobile): array
    {
        try {
            $authHeader = base64_encode($this->clientId . ':' . $this->clientSecret);
            
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authHeader,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/dev/v2/oauth2/token', [
                'grant_type' => 'authorization_code',
                'code' => $authCode,
                'auth_type' => 'SMS',
                'redirect_uri' => config('app.url') . '/api/auth/'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['status']) && $data['status'] === 'DONE') {
                    $tokenData = $data['result'];
                    $accessToken = $tokenData['value'];
                    $lifeTime = (int)($tokenData['lifeTime'] ?? 3600000); // milliseconds
                    $expiresIn = intval($lifeTime / 1000); // convert to seconds
                    
                    // Store the token
                    $this->storeToken($scope, $nationalId, $mobile, $accessToken, $expiresIn, $tokenData);
                    
                    // Clean up track ID
                    $this->cleanupTrackId($scope, $nationalId, $mobile);
                    
                    Log::info('SMS token obtained successfully', [
                        'scope' => $scope,
                        'mobile' => $mobile,
                        'national_id' => $nationalId,
                        'expires_in_seconds' => $expiresIn
                    ]);

                    return [
                        'success' => true,
                        'access_token' => $accessToken,
                        'expires_in' => $expiresIn,
                        'token_data' => $tokenData,
                        'message' => 'احراز هویت با موفقیت انجام شد'
                    ];
                }
            }

            Log::error('Token exchange failed', [
                'status_code' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در دریافت توکن احراز هویت'
            ];

        } catch (\Exception $e) {
            Log::error('Token exchange exception', [
                'error' => $e->getMessage(),
                'scope' => $scope,
                'mobile' => $mobile
            ]);

            return [
                'success' => false,
                'message' => 'خطا در دریافت توکن'
            ];
        }
    }

    /**
     * Get existing token from storage
     */
    public function getToken(string $scope, string $nationalId, string $mobile): ?array
    {
        try {
            $key = $this->generateTokenKey($scope, $nationalId, $mobile);
            $tokenJson = Redis::get($key);
            
            if ($tokenJson) {
                $tokenData = json_decode($tokenJson, true);
                
                // Check if token is expired
                if (isset($tokenData['expires_at'])) {
                    $expiresAt = Carbon::parse($tokenData['expires_at']);
                    if ($expiresAt->isPast()) {
                        // Token expired, remove it
                        Redis::del($key);
                        return null;
                    }
                }
                
                return $tokenData;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting SMS token', [
                'scope' => $scope,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Store token in Redis
     */
    private function storeToken(string $scope, string $nationalId, string $mobile, string $accessToken, int $expiresIn, array $fullTokenData): void
    {
        try {
            $key = $this->generateTokenKey($scope, $nationalId, $mobile);
            $expiresAt = now()->addSeconds($expiresIn);
            
            $tokenData = [
                'access_token' => $accessToken,
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'expires_in' => $expiresIn,
                'expires_at' => $expiresAt->toIso8601String(),
                'created_at' => now()->toIso8601String(),
                'full_token_data' => $fullTokenData
            ];
            
            Redis::setex($key, $expiresIn, json_encode($tokenData));
            
        } catch (\Exception $e) {
            Log::error('Error storing SMS token', [
                'scope' => $scope,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store track ID for OTP verification
     */
    private function storeTrackId(string $scope, string $nationalId, string $mobile, string $trackId): void
    {
        try {
            $key = $this->generateTrackIdKey($scope, $nationalId, $mobile);
            Redis::setex($key, 300, $trackId); // 5 minutes expiry
        } catch (\Exception $e) {
            Log::error('Error storing track ID', [
                'scope' => $scope,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get track ID for OTP verification
     */
    private function getTrackId(string $scope, string $nationalId, string $mobile): ?string
    {
        try {
            $key = $this->generateTrackIdKey($scope, $nationalId, $mobile);
            return Redis::get($key);
        } catch (\Exception $e) {
            Log::error('Error getting track ID', [
                'scope' => $scope,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Clean up track ID after use
     */
    private function cleanupTrackId(string $scope, string $nationalId, string $mobile): void
    {
        try {
            $key = $this->generateTrackIdKey($scope, $nationalId, $mobile);
            Redis::del($key);
        } catch (\Exception $e) {
            Log::error('Error cleaning up track ID', [
                'scope' => $scope,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clean up expired token
     */
    public function cleanupExpiredToken(string $scope, string $nationalId, string $mobile): bool
    {
        try {
            $key = $this->generateTokenKey($scope, $nationalId, $mobile);
            $deleted = Redis::del($key);
            
            Log::info('Cleaned up expired SMS token', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'deleted' => $deleted > 0
            ]);

            return $deleted > 0;
        } catch (\Exception $e) {
            Log::error('Failed to cleanup expired token', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Revoke token for security purposes (e.g., after successful service call)
     * 
     * This method immediately invalidates an SMS authorization token by deleting it from Redis.
     * It's typically called after successfully retrieving sensitive data (like loan information)
     * to ensure the token cannot be misused if compromised.
     * 
     * Security benefits:
     * - Prevents token reuse after data retrieval
     * - Reduces window of vulnerability
     * - Implements principle of least privilege
     * 
     * @param string $scope The scope of the token (e.g., 'credit:sms-facility-inquiry:get')
     * @param string $nationalId User's national ID
     * @param string $mobile User's mobile number  
     * @param string $reason Reason for revocation (for logging purposes)
     * @return bool True if token was found and deleted, false otherwise
     */
    public function revokeToken(string $scope, string $nationalId, string $mobile, string $reason = 'security'): bool
    {
        try {
            $key = $this->generateTokenKey($scope, $nationalId, $mobile);
            $deleted = Redis::del($key);
            
            // Also clean up track ID if exists
            $trackIdKey = $this->generateTrackIdKey($scope, $nationalId, $mobile);
            Redis::del($trackIdKey);
            
            Log::info('Revoked SMS token for security', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'reason' => $reason,
                'deleted' => $deleted > 0
            ]);

            return $deleted > 0;
        } catch (\Exception $e) {
            Log::error('Failed to revoke SMS token', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'reason' => $reason,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate Redis key for token storage
     */
    private function generateTokenKey(string $scope, string $nationalId, string $mobile): string
    {
        return "finnotech:sms_token:" . md5($scope . '_' . $nationalId . '_' . $mobile);
    }

    /**
     * Generate Redis key for track ID storage
     */
    private function generateTrackIdKey(string $scope, string $nationalId, string $mobile): string
    {
        return "finnotech:sms_track:" . md5($scope . '_' . $nationalId . '_' . $mobile);
    }

    /**
     * Generate authorization URL for SMS verification flow
     */
    public function generateAuthorizationUrl(string $scope, string $mobile, string $nationalId, array $serviceData = [], ?int $serviceId = null): string
    {
        try {
            // Start SMS authorization request
            $authResult = $this->requestSmsAuthorization($scope, $mobile, $nationalId);
            
            if (!$authResult['success']) {
                throw new \Exception('Failed to request SMS authorization: ' . ($authResult['message'] ?? 'Unknown error'));
            }

            // Create verification request using SmsVerificationService
            $smsVerificationService = app(SmsVerificationService::class);
            $verificationRequest = $smsVerificationService->createVerificationRequest(
                $serviceData['service_slug'] ?? 'unknown',
                $serviceId ?? 0,
                $serviceData,
                $scope,
                $mobile,
                $nationalId,
                $authResult['track_id'] ?? null,
                Auth::user()?->id,
                session()->getId()
            );

            // Generate SMS verification URL
            $verificationUrl = route('services.sms-verification', [
                'service' => $serviceData['service_slug'] ?? 'unknown',
                'hash' => $verificationRequest['hash']
            ]);

            Log::info('SMS authorization URL generated', [
                'scope' => $scope,
                'mobile' => $mobile,
                'national_id' => $nationalId,
                'hash' => $verificationRequest['hash'],
                'url' => $verificationUrl
            ]);

            return $verificationUrl;

        } catch (\Exception $e) {
            Log::error('Failed to generate SMS authorization URL', [
                'scope' => $scope,
                'mobile' => $mobile,
                'national_id' => $nationalId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Clean up expired tokens (for maintenance)
     */
    public function cleanupExpiredTokens(): int
    {
        try {
            $pattern = "finnotech:sms_token:*";
            $keys = Redis::keys($pattern);
            $removedCount = 0;

            foreach ($keys as $key) {
                $tokenJson = Redis::get($key);
                if ($tokenJson) {
                    $tokenData = json_decode($tokenJson, true);
                    if (isset($tokenData['expires_at'])) {
                        $expiresAt = Carbon::parse($tokenData['expires_at']);
                        if ($expiresAt->isPast()) {
                            Redis::del($key);
                            $removedCount++;
                        }
                    }
                }
            }

            Log::info('Cleaned up expired SMS tokens', [
                'removed_count' => $removedCount,
                'total_checked' => count($keys)
            ]);

            return $removedCount;
        } catch (\Exception $e) {
            Log::error('Error cleaning up expired tokens', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Make an authorized API call using stored SMS token
     */
    public function makeAuthorizedApiCall(
        string $scope,
        string $nationalId,
        string $mobile,
        array $postParams = [],
        array $queryParams = []
    ): array {
        try {
            // Get stored token
            $tokenData = $this->getToken($scope, $nationalId, $mobile);
            
            if (!$tokenData || !isset($tokenData['access_token'])) {
                throw new \App\Exceptions\FinnotechException('No valid SMS auth token found for this user and scope');
            }

            $accessToken = $tokenData['access_token'];
            
            // Build the full API endpoint URL
            $endpoint = $this->buildApiEndpoint($scope, $nationalId, $queryParams);
            $fullUrl = $this->baseUrl . $endpoint;
            
            Log::info('Making authorized SMS API call', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'endpoint' => $endpoint,
                'has_post_params' => !empty($postParams),
                'has_query_params' => !empty($queryParams)
            ]);

            // Make the API call with the token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->timeout(30);

            // Add query parameters if provided
            if (!empty($queryParams)) {
                $response = $response->withOptions(['query' => $queryParams]);
            }

            // Make GET or POST request based on post params
            if (!empty($postParams)) {
                $httpResponse = $response->post($fullUrl, $postParams);
            } else {
                $httpResponse = $response->get($fullUrl);
            }

            if ($httpResponse->successful()) {
                $data = $httpResponse->json();
                
                Log::info('SMS authorized API call successful', [
                    'scope' => $scope,
                    'national_id' => $nationalId,
                    'status' => $data['status'] ?? 'unknown',
                    'response' => $data
                ]);

                // 🔒 REVOKE SMS TOKEN FOR SECURITY (after successful API call)
                try {
                    $tokenRevoked = $this->revokeToken($scope, $nationalId, $mobile, 'post_api_call_security');
                    
                    Log::info('🔒 SMS token revoked for security after API call', [
                        'scope' => $scope,
                        'national_id' => $nationalId,
                        'mobile' => $mobile,
                        'revoked' => $tokenRevoked
                    ]);
                } catch (\Exception $e) {
                    // Don't fail the main request if token revocation fails
                    Log::warning('⚠️ Failed to revoke SMS token after API call', [
                        'scope' => $scope,
                        'national_id' => $nationalId,
                        'mobile' => $mobile,
                        'error' => $e->getMessage()
                    ]);
                }

                return $data;
            } else {
                $errorData = $httpResponse->json();
                $errorMessage = $errorData['error']['message'] ?? 'API call failed';
                
                Log::error('SMS authorized API call failed', [
                    'scope' => $scope,
                    'national_id' => $nationalId,
                    'status_code' => $httpResponse->status(),
                    'error' => $errorMessage,
                    'response' => $errorData
                ]);

                throw new \App\Exceptions\FinnotechException($errorMessage);
            }

        } catch (\App\Exceptions\FinnotechException $e) {
            // Re-throw FinnotechException as-is
            throw $e;
        } catch (\Exception $e) {
            Log::error('SMS authorized API call exception', [
                'scope' => $scope,
                'national_id' => $nationalId,
                'mobile' => $mobile,
                'error' => $e->getMessage()
            ]);

            throw new \App\Exceptions\FinnotechException('Error making authorized API call: ' . $e->getMessage());
        }
    }

    /**
     * Build API endpoint based on scope and parameters
     */
    private function buildApiEndpoint(string $scope, string $nationalId, array $queryParams = []): string
    {
        // Map scopes to their endpoints
        $endpointMap = [
            'credit:sms-facility-inquiry:get' => "/credit/v2/clients/{$this->clientId}/users/{$nationalId}/sms/facilityInquiry",
            'credit:sms-back-cheques:get' => "/credit/v2/clients/{$this->clientId}/users/{$nationalId}/sms/backCheques",
            'credit:sms-guaranty-inquiry:get' => "/credit/v2/clients/{$this->clientId}/users/{$nationalId}/sms/guarantyInquiry",
            'credit:sms-sayady-cheque-inquiry:get' => "/credit/v2/clients/{$this->clientId}/users/{$nationalId}/sms/sayadChequeInquiry",
            'oak:sms-kyc-liveness-inquiry:get' => "/oak/v2/clients/{$this->clientId}/users/{$nationalId}/sms/livenessInquiry",
            'oak:sms-kyc-expats-inquiry:get' => "/oak/v2/clients/{$this->clientId}/users/{$nationalId}/sms/expatsInquiry",
            'oak:sms-kyc-military-service-inquiry:get' => "/oak/v2/clients/{$this->clientId}/users/{$nationalId}/sms/militaryServiceInquiry",
            // Add more mappings as needed
        ];

        if (isset($endpointMap[$scope])) {
            return $endpointMap[$scope];
        }

        // Fallback: try to construct from scope
        // Format: service:sms-resource:method -> /service/v2/clients/{clientId}/users/{nationalId}/sms/resource
        $parts = explode(':', $scope);
        if (count($parts) >= 3) {
            $service = $parts[0];
            $resource = str_replace('sms-', '', $parts[1]);
            $resourceCamelCase = str_replace('-', '', ucwords($resource, '-'));
            $resourceCamelCase = lcfirst($resourceCamelCase);
            
            return "/{$service}/v2/clients/{$this->clientId}/users/{$nationalId}/sms/{$resourceCamelCase}";
        }

        throw new \App\Exceptions\FinnotechException("Unknown scope endpoint: {$scope}");
    }
} 