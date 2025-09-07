<?php

namespace App\Services\Finnotech;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Exceptions\FinnotechException;

class KYC
{
    private const BASE_URL = 'https://api.finnotech.ir';
    private const SANDBOX_URL = 'https://sandboxapi.finnotech.ir';
    private const MAX_RETRIES = 2;
    private const REDIS_FAILED_REQUESTS_KEY = 'finnotech:kyc:failed_requests';

    private $token;
    private $clientId;
    private $isSandbox;

    public function __construct(Token $token, string $clientId, bool $isSandbox = false)
    {
        $this->token = $token;
        $this->clientId = $clientId;
        $this->isSandbox = $isSandbox;
    }

    /**
     * Get the base URL for API requests.
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        return $this->isSandbox ? self::SANDBOX_URL : self::BASE_URL;
    }

    /**
     * Make an HTTP request with retry logic and error handling.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @param string $tokenType
     * @return array
     * @throws FinnotechException
     */
    private function makeRequest(string $method, string $endpoint, array $data = [], array $headers = [], string $tokenType = 'client_credentials'): array
    {
        $url = $this->getBaseUrl() . $endpoint;
        
        // Get the appropriate token based on the token type
        $token = $this->getToken($tokenType);
        
        $headers = array_merge([
            'Authorization' => "Bearer {$token}",
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
    }

    /**
     * Get the appropriate token based on the token type.
     *
     * @param string $tokenType
     * @return string
     * @throws FinnotechException
     */
    private function getToken(string $tokenType): string
    {
        switch ($tokenType) {
            case 'client_credentials':
                return $this->token->getClientCredentialToken()['access_token'];
            case 'sms_auth':
                // Implement SMS authentication flow
                // This might involve sending an SMS to the user and getting an OTP
                throw new FinnotechException("SMS authentication not implemented");
            default:
                throw new FinnotechException("Invalid token type: {$tokenType}");
        }
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
        Log::error('Finnotech KYC API request failed', $failedRequest);
    }

    /**
     * Check the status of a digital signature request.
     *
     * @param string $trackId
     * @param string $registrationId
     * @return array
     * @throws FinnotechException
     */
    public function checkSignStatus(string $trackId, string $registrationId): array
    {
        $endpoint = "/sign/v2/clients/{$this->clientId}/statusInquiry";
        $queryParams = http_build_query([
            'trackId' => $trackId,
            'registrationId' => $registrationId,
        ]);

        return $this->makeRequest('get', "{$endpoint}?{$queryParams}");
    }

    /**
     * Verify deposit owner information.
     *
     * @param string $trackId
     * @param string $deposit
     * @param string $bank
     * @param string $nationalCode
     * @return array
     * @throws FinnotechException
     */
    public function verifyDepositOwner(string $trackId, string $deposit, string $bank, string $nationalCode): array
    {
        $endpoint = "/facility/v2/clients/{$this->clientId}/depositOwnerVerification";
        $queryParams = http_build_query([
            'trackId' => $trackId,
            'deposit' => $deposit,
            'bank' => $bank,
            'nationalCode' => $nationalCode,
        ]);

        return $this->makeRequest('get', "{$endpoint}?{$queryParams}");
    }

    /**
     * Verify SMS Shahkar.
     *
     * @param string $trackId
     * @param string $otpTrackId
     * @param string $otp
     * @return array
     * @throws FinnotechException
     */
    public function verifySmsShahkar(string $trackId, string $otpTrackId, string $otp): array
    {
        $endpoint = "/facility/v2/clients/{$this->clientId}/shahkar/smsVerify";
        $queryParams = http_build_query([
            'trackId' => $trackId,
            'otpTrackId' => $otpTrackId,
            'otp' => $otp,
        ]);

        return $this->makeRequest('get', "{$endpoint}?{$queryParams}");
    }

    /**
     * Send SMS Shahkar verification.
     *
     * @param string $trackId
     * @param string $mobile
     * @param string $nationalCode
     * @param string|null $version
     * @return array
     * @throws FinnotechException
     */
    public function sendSmsShahkar(string $trackId, string $mobile, string $nationalCode, ?string $version = null): array
    {
        $endpoint = "/facility/v2/clients/{$this->clientId}/shahkar/smsSend";
        $queryParams = http_build_query(array_filter([
            'trackId' => $trackId,
            'mobile' => $mobile,
            'nationalCode' => $nationalCode,
            'version' => $version,
        ]));

        return $this->makeRequest('get', "{$endpoint}?{$queryParams}");
    }

    /**
     * Match legal ID with members.
     *
     * @param string $trackId
     * @param string $nationalId
     * @param string $legalId
     * @return array
     * @throws FinnotechException
     */
    public function matchLegalIdWithMembers(string $trackId, string $nationalId, string $legalId): array
    {
        $endpoint = "/kyb/v2/clients/{$this->clientId}/matchLegalIdWithMembers";
        $queryParams = http_build_query([
            'trackId' => $trackId,
            'nationalId' => $nationalId,
            'legalId' => $legalId,
        ]);

        return $this->makeRequest('get', "{$endpoint}?{$queryParams}");
    }

    /**
     * Compare live video with national card image and check liveness and speech text.
     *
     * @param string $trackId
     * @param string $videoPath
     * @param string $nationalCode
     * @param string $birthDate
     * @param string $serialNumber
     * @param string|null $speechText
     * @return array
     * @throws FinnotechException
     */
    public function compareLiveVideoWithNationalCard(
        string $trackId,
        string $videoPath,
        string $nationalCode,
        string $birthDate,
        string $serialNumber,
        ?string $speechText = null
    ): array {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/compareLiveVideoWithNationalCard";
        
        $data = [
            'video' => fopen($videoPath, 'r'),
            'nationalCode' => $nationalCode,
            'birthDate' => $birthDate,
            'serialNumber' => $serialNumber,
        ];

        if ($speechText) {
            $data['speechText'] = $speechText;
        }

        return $this->makeRequest('post', "{$endpoint}?trackId={$trackId}", $data, [], 'client_credentials');
    }

    /**
     * Compare live image with national card image.
     *
     * @param string $trackId
     * @param string $imagePath
     * @param string $nationalCode
     * @param string $birthDate
     * @param string $serialNumber
     * @param float|null $threshold
     * @return array
     * @throws FinnotechException
     */
    public function compareLiveImageWithNationalCard(
        string $trackId,
        string $imagePath,
        string $nationalCode,
        string $birthDate,
        string $serialNumber,
        ?float $threshold = null
    ): array {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/compareLiveImageWithNationalCard";
        
        $data = [
            'liveImage' => fopen($imagePath, 'r'),
            'nationalCode' => $nationalCode,
            'birthDate' => $birthDate,
            'serialNumber' => $serialNumber,
        ];

        if ($threshold !== null) {
            $data['threshold'] = $threshold;
        }

        return $this->makeRequest('post', "{$endpoint}?trackId={$trackId}", $data, [], 'client_credentials');
    }

    /**
     * Compare two images.
     *
     * @param string $trackId
     * @param string $liveImagePath
     * @param string $personImagePath
     * @param float|null $threshold
     * @return array
     * @throws FinnotechException
     */
    public function compareTwoImages(
        string $trackId,
        string $liveImagePath,
        string $personImagePath,
        ?float $threshold = null
    ): array {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/compareTwoImages";
        
        $data = [
            'liveImage' => fopen($liveImagePath, 'r'),
            'personImage' => fopen($personImagePath, 'r'),
        ];

        if ($threshold !== null) {
            $data['threshold'] = $threshold;
        }

        return $this->makeRequest('post', "{$endpoint}?trackId={$trackId}", $data, [], 'client_credentials');
    }

    /**
     * Compare video with national card image.
     *
     * @param string $trackId
     * @param string $videoPath
     * @param string $nationalCode
     * @param string $birthDate
     * @param string $serialNumber
     * @return array
     * @throws FinnotechException
     */
    public function compareVideoWithNationalCardImage(
        string $trackId,
        string $videoPath,
        string $nationalCode,
        string $birthDate,
        string $serialNumber
    ): array {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/compareVideoWithNationalCardImage";
        
        $data = [
            'video' => fopen($videoPath, 'r'),
            'nationalCode' => $nationalCode,
            'birthDate' => $birthDate,
            'serialNumber' => $serialNumber,
        ];

        return $this->makeRequest('post', "{$endpoint}?trackId={$trackId}", $data, [], 'client_credentials');
    }

    /**
     * Inquire death status.
     *
     * @param string $trackId
     * @param string $nationalCode
     * @param string $birthDate
     * @return array
     * @throws FinnotechException
     */
    public function inquireDeathStatus(string $trackId, string $nationalCode, string $birthDate): array
    {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/deathStatusInquiry";
        $queryParams = http_build_query([
            'trackId' => $trackId,
            'nationalCode' => $nationalCode,
            'birthDate' => $birthDate,
        ]);

        return $this->makeRequest('get', "{$endpoint}?{$queryParams}");
    }

    /**
     * Inquire foreigner ID.
     *
     * @param string $trackId
     * @param string $foreignerId
     * @param string $birthDate
     * @return array
     * @throws FinnotechException
     */
    public function inquireForeignerId(string $trackId, string $foreignerId, string $birthDate): array
    {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/foreignerIdInquiry";
        $queryParams = http_build_query([
            'trackId' => $trackId,
            'foreignerId' => $foreignerId,
            'birthDate' => $birthDate,
        ]);

        return $this->makeRequest('get', "{$endpoint}?{$queryParams}");
    }

    /**
     * Verify IBAN owner and birthdate.
     *
     * @param string $trackId
     * @param string $birthDate
     * @param string $nid
     * @param string $iban
     * @return array
     * @throws FinnotechException
     */
    public function verifyIbanOwnerBirthdate(string $trackId, string $birthDate, string $nid, string $iban): array
    {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/ibanOwnerBirthdateVerification";
        $queryParams = http_build_query([
            'trackId' => $trackId,
            'birthDate' => $birthDate,
            'nid' => $nid,
            'iban' => $iban,
        ]);

        return $this->makeRequest('get', "{$endpoint}?{$queryParams}");
    }

    /**
     * Verify IBAN owner.
     *
     * @param string $trackId
     * @param string $birthDate
     * @param string $nid
     * @param string $iban
     * @return array
     * @throws FinnotechException
     */
    public function verifyIbanOwner(string $trackId, string $birthDate, string $nid, string $iban): array
    {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/ibanOwnerVerification";
        $queryParams = http_build_query([
            'trackId' => $trackId,
            'birthDate' => $birthDate,
            'nid' => $nid,
            'iban' => $iban,
        ]);

        return $this->makeRequest('get', "{$endpoint}?{$queryParams}");
    }

    /**
     * Verify national ID via SMS.
     *
     * @param string $trackId
     * @param string $nid
     * @param string $birthDate
     * @param array $params
     * @return array
     * @throws FinnotechException
     */
    public function verifyNationalIdViaSms(string $trackId, string $nid, string $birthDate, array $params): array
    {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/users/{$nid}/sms/nidVerification";
        $queryParams = http_build_query(array_merge([
            'trackId' => $trackId,
            'birthDate' => $birthDate,
        ], $params));

        return $this->makeRequest('get', "{$endpoint}?{$queryParams}", [], [], 'sms_auth');
    }

    /**
     * Inquire military status.
     *
     * @param string $trackId
     * @param string $nationalCode
     * @return array
     * @throws FinnotechException
     */
    public function inquireMilitaryStatus(string $trackId, string $nationalCode): array
    {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/militaryInquiry";
        $queryParams = http_build_query([
            'trackId' => $trackId,
            'nationalCode' => $nationalCode,
        ]);

        return $this->makeRequest('get', "{$endpoint}?{$queryParams}");
    }

    /**
     * Inquire passport status.
     *
     * @param string $trackId
     * @param string $nationalCode
     * @param string $mobile
     * @return array
     * @throws FinnotechException
     */
    public function inquirePassportStatus(string $trackId, string $nationalCode, string $mobile): array
    {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/passportInquiry";
        
        $data = [
            'nationalCode' => $nationalCode,
            'mobile' => $mobile,
        ];

        return $this->makeRequest('post', "{$endpoint}?trackId={$trackId}", $data);
    }

    /**
     * Perform OCR verification on national card.
     *
     * @param string $trackId
     * @param string $imagePath
     * @param string $type
     * @return array
     * @throws FinnotechException
     */
    public function performOcrVerification(string $trackId, string $imagePath, string $type): array
    {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/ocr";
        
        $data = [
            'cardImage' => fopen($imagePath, 'r'),
            'type' => $type,
        ];

        return $this->makeRequest('post', "{$endpoint}?trackId={$trackId}", $data);
    }

    /**
     * Verify signature.
     *
     * @param string $trackId
     * @param string $signaturePath
     * @param float|null $threshold
     * @return array
     * @throws FinnotechException
     */
    public function verifySignature(string $trackId, string $signaturePath, ?float $threshold = null): array
    {
        $endpoint = "/kyc/v2/clients/{$this->clientId}/signatureVerification";
        
        $data = [
            'signature' => fopen($signaturePath, 'r'),
        ];

        if ($threshold !== null) {
            $data['threshold'] = $threshold;
        }

        return $this->makeRequest('post', "{$endpoint}?trackId={$trackId}", $data);
    }

    /**
     * Get signed document.
     *
     * @param string $trackId
     * @param string $signingTrackId
     * @return array
     * @throws FinnotechException
     */
    public function getSignedDocument(string $trackId, string $signingTrackId): array
    {
        $endpoint = "/sign/v2/clients/{$this->clientId}/signedDocument";
        $queryParams = http_build_query([
            'trackId' => $trackId,
            'signingTrackId' => $signingTrackId,
        ]);

        return $this->makeRequest('get', "{$endpoint}?{$queryParams}");
    }

    /**
     * Request document signing.
     *
     * @param string $trackId
     * @param array $data
     * @return array
     * @throws FinnotechException
     */
    public function requestDocumentSigning(string $trackId, array $data): array
    {
        $endpoint = "/sign/v2/clients/{$this->clientId}/documentSignRequest";
        return $this->makeRequest('post', "{$endpoint}?trackId={$trackId}", $data);
    }
}