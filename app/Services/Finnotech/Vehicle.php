<?php

namespace App\Services\Finnotech;

use App\Services\Finnotech\FinnotechService;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class Vehicle
 * 
 * Handles Finnotech Vehicle Services including car violations,
 * motor violations, and driving license minus points.
 */
class Vehicle
{
    /**
     * @var FinnotechService
     */
    protected $finnotechService;

    /**
     * Vehicle constructor.
     *
     * @param FinnotechService $finnotechService
     */
    public function __construct(FinnotechService $finnotechService)
    {
        $this->finnotechService = $finnotechService;
    }

    /**
     * Get car violation information.
     *
     * @param object $violation
     * @return object|null
     */
    public function getCarViolation(object $violation): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/billing/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/drivingOffense",
                [
                    'mobile' => $violation->mobile,
                    'nationalID' => $violation->nid,
                    'plateNumber' => $violation->plate,
                    'trackId' => $this->finnotechService->generateTrackId()
                ]
            );
        } catch (Exception $e) {
            Log::error('Error getting car violation: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get motor violation information.
     *
     * @param object $violation
     * @return object|null
     */
    public function getMotorViolation(object $violation): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/billing/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/ridingOffense",
                [
                    'mobile' => $violation->mobile,
                    'nationalID' => $violation->nid,
                    'plateNumber' => $violation->plate,
                    'trackId' => $this->finnotechService->generateTrackId()
                ]
            );
        } catch (Exception $e) {
            Log::error('Error getting motor violation: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get minus points information.
     *
     * @param object $violation
     * @return object|null
     */
    public function getMinusPoints(object $violation): ?object
    {
        try {
            return $this->finnotechService->makeApiRequest(
                "/billing/" . $this->finnotechService->getApiVersion() . "/clients/" . $this->finnotechService->getClientId() . "/negativeScore",
                [
                    'mobile' => $violation->mobile,
                    'nationalID' => $violation->nid,
                    'licenseNumber' => $violation->license,
                    'trackId' => $this->finnotechService->generateTrackId()
                ]
            );
        } catch (Exception $e) {
            Log::error('Error getting minus points: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get car violation with array input.
     *
     * @param string $mobile
     * @param string $nationalId
     * @param string $plateNumber
     * @return object|null
     */
    public function getCarViolationByParams(string $mobile, string $nationalId, string $plateNumber): ?object
    {
        $violation = (object) [
            'mobile' => $mobile,
            'nid' => $nationalId,
            'plate' => $plateNumber
        ];

        return $this->getCarViolation($violation);
    }

    /**
     * Get motor violation with array input.
     *
     * @param string $mobile
     * @param string $nationalId
     * @param string $plateNumber
     * @return object|null
     */
    public function getMotorViolationByParams(string $mobile, string $nationalId, string $plateNumber): ?object
    {
        $violation = (object) [
            'mobile' => $mobile,
            'nid' => $nationalId,
            'plate' => $plateNumber
        ];

        return $this->getMotorViolation($violation);
    }

    /**
     * Get minus points with array input.
     *
     * @param string $mobile
     * @param string $nationalId
     * @param string $licenseNumber
     * @return object|null
     */
    public function getMinusPointsByParams(string $mobile, string $nationalId, string $licenseNumber): ?object
    {
        $violation = (object) [
            'mobile' => $mobile,
            'nid' => $nationalId,
            'license' => $licenseNumber
        ];

        return $this->getMinusPoints($violation);
    }

    /**
     * Validate mobile number format.
     *
     * @param string $mobile
     * @return bool
     */
    public function validateMobile(string $mobile): bool
    {
        $cleaned = preg_replace('/\D/', '', $mobile);
        
        // Check for Iranian mobile numbers (starting with 09 or 989)
        return preg_match('/^(09\d{9}|989\d{9})$/', $cleaned);
    }

    /**
     * Validate national ID format.
     *
     * @param string $nationalId
     * @return bool
     */
    public function validateNationalId(string $nationalId): bool
    {
        $cleaned = preg_replace('/\D/', '', $nationalId);
        return strlen($cleaned) === 10 && ctype_digit($cleaned);
    }

    /**
     * Validate plate number format.
     *
     * @param string $plateNumber
     * @return bool
     */
    public function validatePlateNumber(string $plateNumber): bool
    {
        // Iranian plate numbers can have various formats
        // This is a basic validation
        return !empty(trim($plateNumber)) && strlen(trim($plateNumber)) >= 4;
    }

    /**
     * Validate license number format.
     *
     * @param string $licenseNumber
     * @return bool
     */
    public function validateLicenseNumber(string $licenseNumber): bool
    {
        // License numbers are typically numeric
        $cleaned = preg_replace('/\D/', '', $licenseNumber);
        return !empty($cleaned) && ctype_digit($cleaned);
    }

    /**
     * Format mobile number.
     *
     * @param string $mobile
     * @return string
     */
    public function formatMobile(string $mobile): string
    {
        $cleaned = preg_replace('/\D/', '', $mobile);
        
        // Convert to international format if needed
        if (substr($cleaned, 0, 2) === '09') {
            return '98' . substr($cleaned, 1);
        }
        
        return $cleaned;
    }

    /**
     * Get comprehensive vehicle report.
     *
     * @param string $mobile
     * @param string $nationalId
     * @param string $plateNumber
     * @param string|null $licenseNumber
     * @return array
     */
    public function getVehicleReport(string $mobile, string $nationalId, string $plateNumber, ?string $licenseNumber = null): array
    {
        $report = [
            'mobile' => $mobile,
            'nationalId' => $nationalId,
            'plateNumber' => $plateNumber,
            'carViolations' => null,
            'motorViolations' => null,
            'minusPoints' => null,
            'errors' => []
        ];

        // Validate inputs
        if (!$this->validateMobile($mobile)) {
            $report['errors'][] = 'Invalid mobile number format';
        }

        if (!$this->validateNationalId($nationalId)) {
            $report['errors'][] = 'Invalid national ID format';
        }

        if (!$this->validatePlateNumber($plateNumber)) {
            $report['errors'][] = 'Invalid plate number format';
        }

        if ($licenseNumber && !$this->validateLicenseNumber($licenseNumber)) {
            $report['errors'][] = 'Invalid license number format';
        }

        // If there are validation errors, return early
        if (!empty($report['errors'])) {
            return $report;
        }

        // Format mobile number
        $formattedMobile = $this->formatMobile($mobile);

        // Get car violations
        try {
            $report['carViolations'] = $this->getCarViolationByParams($formattedMobile, $nationalId, $plateNumber);
        } catch (Exception $e) {
            $report['errors'][] = 'Error getting car violations: ' . $e->getMessage();
        }

        // Get motor violations
        try {
            $report['motorViolations'] = $this->getMotorViolationByParams($formattedMobile, $nationalId, $plateNumber);
        } catch (Exception $e) {
            $report['errors'][] = 'Error getting motor violations: ' . $e->getMessage();
        }

        // Get minus points if license number is provided
        if ($licenseNumber) {
            try {
                $report['minusPoints'] = $this->getMinusPointsByParams($formattedMobile, $nationalId, $licenseNumber);
            } catch (Exception $e) {
                $report['errors'][] = 'Error getting minus points: ' . $e->getMessage();
            }
        }

        return $report;
    }

    /**
     * Get multiple vehicle reports in batch.
     *
     * @param array $vehicles Array of vehicle data
     * @return array
     */
    public function getBatchVehicleReports(array $vehicles): array
    {
        $results = [];
        
        foreach ($vehicles as $vehicle) {
            if (!isset($vehicle['mobile'], $vehicle['nationalId'], $vehicle['plateNumber'])) {
                $results[] = [
                    'success' => false,
                    'error' => 'Missing required fields: mobile, nationalId, or plateNumber'
                ];
                continue;
            }

            $licenseNumber = $vehicle['licenseNumber'] ?? null;
            $result = $this->getVehicleReport(
                $vehicle['mobile'],
                $vehicle['nationalId'],
                $vehicle['plateNumber'],
                $licenseNumber
            );

            $results[] = [
                'success' => empty($result['errors']),
                'data' => $result
            ];
        }

        return $results;
    }
} 