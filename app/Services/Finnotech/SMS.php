<?php

namespace App\Services\Finnotech;

use App\Services\Finnotech\FinnotechService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class SMS
 * 
 * Handles Finnotech SMS Services including sending SMS, SMS tokens,
 * and SMS-based authentication.
 */
class SMS
{
    /**
     * @var FinnotechService
     */
    protected $finnotechService;

    /**
     * SMS constructor.
     *
     * @param FinnotechService $finnotechService
     */
    public function __construct(FinnotechService $finnotechService)
    {
        $this->finnotechService = $finnotechService;
    }

    /**
     * Send an SMS using the Finnotech API.
     *
     * @param string $message
     * @param string $to
     * @return object|null
     */
    public function sendSMS(string $message, string $to): ?object
    {
        try {
            $endpoint = "/facility/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/finnotext";
            
            return $this->finnotechService->makeApiRequest(
                $endpoint,
                [
                    'from' => config('services.finnotech.sms_sender'),
                    'to' => [$to],
                    'message' => [$message]
                ],
                'POST'
            );
        } catch (Exception $e) {
            Log::error('Error sending SMS: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send SMS token for authentication.
     *
     * @param string $mobile
     * @param string $scope
     * @return object|null
     */
    public function sendSMSToken(string $mobile, string $scope): ?object
    {
        try {
            $url = "/dev/" . $this->finnotechService->getApiVersion() . "/oauth2/authorize?client_id=" . $this->finnotechService->getClientId() . "&response_type=code&redirect_uri=" . $this->finnotechService->getRedirectUri() . "&scope={$scope}&mobile={$mobile}&auth_type=SMS";
            return $this->finnotechService->makeApiRequest($url, [], 'GET', false);
        } catch (Exception $e) {
            Log::error('Error sending SMS token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify SMS token.
     *
     * @param string $mobile
     * @param string $nationalId
     * @param string $otp
     * @return object|null
     */
    public function verifySMSToken(string $mobile, string $nationalId, string $otp): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/dev/" . $this->finnotechService->getApiVersion() . "/oauth2/verify/sms",
                [
                    'mobile' => $mobile,
                    'otp' => $otp,
                    'nid' => $nationalId
                ],
                'POST',
                false
            );
        } catch (Exception $e) {
            Log::error('Error verifying SMS token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get SMS token.
     *
     * @param string $code
     * @return object|null
     */
    public function getSMSToken(string $code): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/dev/" . $this->finnotechService->getApiVersion() . "/oauth2/token",
                [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'auth_type' => 'SMS',
                    'redirect_uri' => $this->finnotechService->getRedirectUri()
                ],
                'POST',
                false
            );
        } catch (Exception $e) {
            Log::error('Error getting SMS token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send SMS token for rate-related operations.
     *
     * @param string $nationalId
     * @param string $mobile
     * @return object|null
     */
    public function sendRateSMSToken(string $nationalId, string $mobile): ?object
    {
        try {
            $data = strlen($nationalId) > 10
                ? ['legalPersonNationalCode' => $nationalId, 'mobileNumber' => $mobile]
                : ['realPersonNationalCode' => $nationalId, 'mobileNumber' => $mobile];

            return $this->finnotechService->makeApiRequest(
                "/credit/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/scoreReport/send",
                $data,
                'POST'
            );
        } catch (Exception $e) {
            Log::error('Error sending rate SMS token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify SMS token for rate-related operations.
     *
     * @param string $hash
     * @param string $otp
     * @return object|null
     */
    public function verifyRateSMSToken(string $hash, string $otp): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/credit/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/scoreReport/verifyAndCreate",
                [
                    'hashData' => $hash,
                    'otpCode' => $otp
                ],
                'POST'
            );
        } catch (Exception $e) {
            Log::error('Error verifying rate SMS token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send multiple SMS messages in batch.
     *
     * @param array $messages Array of message objects with message and to fields
     * @return array
     */
    public function sendBatchSMS(array $messages): array
    {
        $results = [];
        
        foreach ($messages as $message) {
            if (!isset($message['message'], $message['to'])) {
                $results[] = [
                    'success' => false,
                    'error' => 'Missing required fields: message or to'
                ];
                continue;
            }

            $result = $this->sendSMS($message['message'], $message['to']);
            $results[] = [
                'success' => $result !== null,
                'data' => $result
            ];
        }

        return $results;
    }

    /**
     * Format phone number for SMS sending.
     *
     * @param string $phoneNumber
     * @return string
     */
    public function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-digit characters
        $cleaned = preg_replace('/\D/', '', $phoneNumber);
        
        // Add country code if not present
        if (substr($cleaned, 0, 2) !== '98') {
            if (substr($cleaned, 0, 1) === '0') {
                $cleaned = '98' . substr($cleaned, 1);
            } else {
                $cleaned = '98' . $cleaned;
            }
        }
        
        return $cleaned;
    }

    /**
     * Validate phone number format.
     *
     * @param string $phoneNumber
     * @return bool
     */
    public function validatePhoneNumber(string $phoneNumber): bool
    {
        $formatted = $this->formatPhoneNumber($phoneNumber);
        return preg_match('/^98\d{10}$/', $formatted);
    }

    /**
     * Generate a simple OTP code.
     *
     * @param int $length
     * @return string
     */
    public function generateOTP(int $length = 6): string
    {
        return str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}
