<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ThirdPartyInsuranceService
{
    /**
     * Format the raw Finnotech API response for display
     *
     * @param array $rawResponse
     * @return array
     */
    public function formatInsuranceData(array $rawResponse): array
    {
        try {
            // Handle no data found case
            if (isset($rawResponse['result']) && is_string($rawResponse['result']) && $rawResponse['result'] == 'PERSIAN_TEXT_c0454533') {
                return [
                    'track_id' => $rawResponse['trackId'] ?? '',
                    'response_code' => $rawResponse['responseCode'] ?? '',
                    'raw_result' => ['message' => 'PERSIAN_TEXT_c0454533']
                ];
            }

            $result = $rawResponse['result'] ?? [];
            
            // Format vehicle information
            $vehicleInfo = $this->formatVehicleInfo($result);
            
            // Format current policy information
            $currentPolicy = $this->formatCurrentPolicy($result);
            
            // Format coverage details
            $coverageDetails = $this->formatCoverageDetails($result);
            
            // Format discount information
            $discountInfo = $this->formatDiscountInfo($result);
            
            // Format claims statistics
            $claimsStats = $this->formatClaimsStats($result);
            
            // Format insurance company information
            $insuranceCompany = $this->formatInsuranceCompany($result);
            
            return [
                'track_id' => $rawResponse['trackId'] ?? '',
                'response_code' => $rawResponse['responseCode'] ?? '',
                'vehicle_info' => $vehicleInfo,
                'current_policy' => $currentPolicy,
                'coverage_details' => $coverageDetails,
                'discount_info' => $discountInfo,
                'claims_stats' => $claimsStats,
                'insurance_company' => $insuranceCompany,
                'raw_result' => $result
            ];
            
        } catch (\Exception $e) {
            Log::error('Error formatting third party insurance data', [
                'error' => $e->getMessage(),
                'raw_response' => $rawResponse
            ]);
            
            return [
                'track_id' => $rawResponse['trackId'] ?? '',
                'response_code' => $rawResponse['responseCode'] ?? '',
                'raw_result' => ['message' => 'PERSIAN_TEXT_7a33ac45']
            ];
        }
    }
    
    /**
     * Format vehicle information
     */
    private function formatVehicleInfo(array $result): array
    {
        // Format plate number
        $formattedPlate = $this->formatPlateNumber($result);
        
        return [
            'plate_parts' => [
                'part1' => $result['plk1'] ?? '',
                'letter' => $this->getPlateLetterName($result['plk2'] ?? ''),
                'part2' => $result['plk3'] ?? '',
                'serial' => $result['plkSrl'] ?? ''
            ],
            'formatted_plate' => $formattedPlate,
            'vehicle_system' => $result['systemField'] ?? $result['mapVehicleSystemName'] ?? '',
            'vehicle_type' => $result['typeField'] ?? $result['mapTypNam'] ?? '',
            'vehicle_usage' => $result['usageField'] ?? $result['mapUsageName'] ?? '',
            'model_year' => $result['modelField'] ?? $result['modelCii'] ?? '',
            'main_color' => $result['mainColorField'] ?? '',
            'second_color' => $result['secondColorField'] ?? '',
            'capacity' => $result['capacityField'] ?? '',
            'engine_number' => $result['engineNumberField'] ?? $result['mtrNum'] ?? '',
            'chassis_number' => $result['chassisNumberField'] ?? $result['shsNum'] ?? '',
            'vin_number' => $result['vinNumberField'] ?? $result['vin'] ?? '',
            'cylinder_count' => $result['cylinderNumberField'] ?? $result['cylCnt'] ?? '',
            'axel_count' => $result['axelNumberField'] ?? '',
            'wheel_count' => $result['wheelNumberField'] ?? '',
            'install_date' => $result['installDateField'] ?? ''
        ];
    }
    
    /**
     * Format current policy information
     */
    private function formatCurrentPolicy(array $result): array
    {
        $endDate = $result['endDate'] ?? null;
        $daysRemaining = 0;
        $isActive = false;
        
        if ($endDate) {
            try {
                $endDateTime = \Carbon\Carbon::createFromFormat('Y/m/d', $endDate);
                $daysRemaining = max(0, $endDateTime->diffInDays(now(), false));
                $isActive = $daysRemaining > 0;
            } catch (\Exception $e) {
                Log::warning('Error parsing end date', ['end_date' => $endDate]);
            }
        }
        
        return [
            'policy_number' => $result['prntPlcyCmpDocNo'] ?? '',
            'third_policy_code' => $result['thirdPolicyCode'] ?? '',
            'issue_date' => $result['issueDate'] ?? '',
            'start_date' => $result['startDate'] ?? '',
            'end_date' => $endDate,
            'days_remaining' => abs($daysRemaining),
            'is_active' => $isActive,
            'status_type_code' => $result['statusTypeCode'] ?? 0
        ];
    }
    
    /**
     * Format coverage details
     */
    private function formatCoverageDetails(array $result): array
    {
        return [
            'person_coverage' => [
                'amount' => $result['personCvrCptl'] ?? $result['prsnCvrCptl'] ?? 0,
                'formatted' => $this->formatCurrency($result['personCvrCptl'] ?? $result['prsnCvrCptl'] ?? 0) . 'PERSIAN_TEXT_56f734e6',
                'toman' => ($result['personCvrCptl'] ?? $result['prsnCvrCptl'] ?? 0) / 10,
                'formatted_toman' => $this->formatCurrency(($result['personCvrCptl'] ?? $result['prsnCvrCptl'] ?? 0) / 10) . 'PERSIAN_TEXT_f6ac3483'
            ],
            'life_coverage' => [
                'amount' => $result['lifeCvrCptl'] ?? $result['lfCvrCptl'] ?? 0,
                'formatted' => $this->formatCurrency($result['lifeCvrCptl'] ?? $result['lfCvrCptl'] ?? 0) . 'PERSIAN_TEXT_56f734e6',
                'toman' => ($result['lifeCvrCptl'] ?? $result['lfCvrCptl'] ?? 0) / 10,
                'formatted_toman' => $this->formatCurrency(($result['lifeCvrCptl'] ?? $result['lfCvrCptl'] ?? 0) / 10) . 'PERSIAN_TEXT_f6ac3483'
            ],
            'financial_coverage' => [
                'amount' => $result['financialCvrCptl'] ?? $result['fnCvrCptl'] ?? 0,
                'formatted' => $this->formatCurrency($result['financialCvrCptl'] ?? $result['fnCvrCptl'] ?? 0) . 'PERSIAN_TEXT_56f734e6',
                'toman' => ($result['financialCvrCptl'] ?? $result['fnCvrCptl'] ?? 0) / 10,
                'formatted_toman' => $this->formatCurrency(($result['financialCvrCptl'] ?? $result['fnCvrCptl'] ?? 0) / 10) . 'PERSIAN_TEXT_f6ac3483'
            ]
        ];
    }
    
    /**
     * Format discount information
     */
    private function formatDiscountInfo(array $result): array
    {
        return [
            'person_discount' => [
                'years_without_claim' => $result['disPrsnYrNum'] ?? 0,
                'percentage' => $result['disPrsnYrPrcnt'] ?? 0,
                'discount_percentage' => $result['discountPersonPercent'] ?? 0
            ],
            'financial_discount' => [
                'years_without_claim' => $result['disFnYrNum'] ?? 0,
                'percentage' => $result['disFnYrPrcnt'] ?? 0
            ],
            'life_discount' => [
                'years_without_claim' => $result['disLfYrNum'] ?? 0,
                'percentage' => $result['disLfYrPrcnt'] ?? 0
            ],
            'third_party_discount' => [
                'percentage' => $result['discountThirdPercent'] ?? 0
            ]
        ];
    }
    
    /**
     * Format claims statistics
     */
    private function formatClaimsStats(array $result): array
    {
        return [
            'total_loss_count' => $result['cuntLossAmont'] ?? 0,
            'policy_health_loss' => $result['policyHealthLossCount'] ?? 0,
            'policy_financial_loss' => $result['policyFinancialLossCount'] ?? 0,
            'policy_person_loss' => $result['policyPersonLossCount'] ?? 0
        ];
    }
    
    /**
     * Format insurance company information
     */
    private function formatInsuranceCompany(array $result): array
    {
        return [
            'company_name' => $result['companyName'] ?? '',
            'company_code' => $result['companyCode'] ?? '',
            'last_company_document' => $result['lastCompanyDocumentNumber'] ?? '',
            'endorse_text' => $result['endorseText'] ?? '',
            'endorse_date' => $result['endorseDate'] ?? '',
            'print_endorse_document' => $result['printEndorsCompanyDocumentNumber'] ?? ''
        ];
    }
    
    /**
     * Format plate number for display
     */
    private function formatPlateNumber(array $result): string
    {
        $part1 = str_pad($result['plk1'] ?? '', 2, '0', STR_PAD_LEFT);
        $letter = $this->getPlateLetterName($result['plk2'] ?? '');
        $part2 = str_pad($result['plk3'] ?? '', 3, '0', STR_PAD_LEFT);
        $serial = str_pad($result['plkSrl'] ?? '', 2, '0', STR_PAD_LEFT);
        
        return "{$part1} {$letter} {$part2} - {$serial}"ad7ce003"track_id" => "e1465dfe-608e-4e91-8ebc-59d8cbb58479",
            "response_code" => "FN-KCFH-20003200000",
            "vehicle_info" => [
                "plate_parts" => [
                    "part1" => "91",
                    "letter" => "af6395b3",
                    "part2" => "159",
                    "serial" => "50"
                ],
                "formatted_plate" => "deed5f6a",
                "vehicle_system" => "cf7c05bd",
                "vehicle_type" => "e23bcaff",
                "vehicle_usage" => "9cd0aef1",
                "model_year" => "1401",
                "main_color" => "6064f0d3",
                "second_color" => "6064f0d3",
                "capacity" => "b0269cfc",
                "engine_number" => "M15/9842809",
                "chassis_number" => "NAS750400N5767587",
                "vin_number" => "NAS750400N5767587",
                "cylinder_count" => "4",
                "install_date" => "1401/12/21"
            ],
            "current_policy" => [
                "policy_number" => "1001/1401/000115/1395232",
                "third_policy_code" => "10485205765",
                "issue_date" => "1401/12/26",
                "start_date" => "1401/12/24",
                "end_date" => "1402/12/24",
                "days_remaining" => 45,
                "is_active" => true,
                "status_type_code" => 1
            ],
            "coverage_details" => [
                "person_coverage" => [
                    "amount" => 6000000000,
                    "formatted" => "d5117b85",
                    "toman" => 600000000,
                    "formatted_toman" => "3579e338"
                ],
                "life_coverage" => [
                    "amount" => 8000000000,
                    "formatted" => "d044a62c",
                    "toman" => 800000000,
                    "formatted_toman" => "1dec5ec4"
                ],
                "financial_coverage" => [
                    "amount" => 200000000,
                    "formatted" => "95336dcb",
                    "toman" => 20000000,
                    "formatted_toman" => "64086476"
                ]
            ],
            "discount_info" => [
                "person_discount" => [
                    "years_without_claim" => 2,
                    "percentage" => 10,
                    "discount_percentage" => 10
                ],
                "financial_discount" => [
                    "years_without_claim" => 3,
                    "percentage" => 15
                ],
                "life_discount" => [
                    "years_without_claim" => 2,
                    "percentage" => 10
                ],
                "third_party_discount" => [
                    "percentage" => 20
                ]
            ],
            "claims_stats" => [
                "total_loss_count" => 1,
                "policy_health_loss" => 0,
                "policy_financial_loss" => 1,
                "policy_person_loss" => 0
            ],
            "insurance_company" => [
                "company_name" => "9d68012f",
                "company_code" => "21",
                "last_company_document" => null,
                "endorse_text" => null,
                "endorse_date" => null,
                "print_endorse_document" => "1cd29355"
            ],
            "raw_result" => []
        ];
    }
} 