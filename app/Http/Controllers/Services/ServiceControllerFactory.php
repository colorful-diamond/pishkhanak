<?php

namespace App\Http\Controllers\Services;

use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ServiceControllerFactory
{
    /**
     * Service slug to controller mapping
     *
     * @var array
     */
    private static $serviceMapping = [
        // Banking Services (Already implemented)
        'card-iban' => CardIbanController::class,
        'card-account' => CardAccountController::class,
        'iban-account' => IbanAccountController::class,
        'account-iban' => AccountIbanController::class,
        'sheba-account' => IbanAccountController::class,
        'card-to-sheba' => CardIbanController::class,
        'card-to-account' => CardAccountController::class,
        'account-to-iban' => AccountIbanController::class,
        'iban-validator' => IbanValidatorController::class,
        'sheba-validator' => IbanValidatorController::class,

        // Vehicle Services (New Finnotech Controllers)
        'car-violation-inquiry' => CarViolationInquiryController::class,
        'motor-violation-inquiry' => MotorViolationInquiryController::class,
        'traffic-violation-image' => TrafficViolationImageController::class,
        'vehicle-ownership-inquiry' => VehicleOwnershipInquiryController::class,
        'vehicle-info-inquiry' => VehicleInfoInquiryController::class,
        'active-plates-list' => ActivePlatesListController::class,
        'plate-history-inquiry' => PlateHistoryInquiryController::class,
        'negative-license-score' => NegativeLicenseScoreController::class,
        'driving-license-status' => DrivingLicenseStatusController::class,
        'driver-risk-inquiry' => DriverRiskInquiryController::class,
        'traffic-vehicle-inquiry' => TrafficVehicleInquiryController::class,
        'toll-road-inquiry' => TollRoadInquiryController::class,
        'third-party-insurance-history' => ThirdPartyInsuranceHistoryController::class,

        // Credit & Loan Services (Fixed mappings)
        'credit-score-rating' => CreditScoreRatingBackgroundController::class,
        'loan-inquiry' => LoanInquiryController::class,
        'loan-guarantee-inquiry' => LoanGuaranteeInquiryController::class,
        'cheque-color' => ChequeColorInquiryController::class,
        'cheque-color-inquiry' => ChequeColorInquiryController::class,
        'cheque-inquiry' => ChequeInquiryController::class, // Main cheque inquiry service
        'returned-cheques' => BackChequesInquiryController::class,
        'back-cheques' => BackChequesInquiryController::class,
        'coming-cheque-inquiry' => ComingChequeInquiryController::class,
        'inquiry-makna-code' => InquiryMaknaCodeController::class,
        'macna-inquiry' => InquiryMaknaCodeController::class,
        'guaranty-inquiry' => GuarantyInquiryController::class,
        'guarantee-inquiry' => GuarantyInquiryController::class,
        
        // New Credit & KYC Services (New controllers to be created)
        'facility-inquiry' => FacilityInquiryController::class,
        'sayad-id-by-serial' => SayadIdBySerialController::class,
        'cheque-serial-inquiry' => SayadIdBySerialController::class,
        'guarantee-details' => GuaranteeDetailsController::class,
        'guarantee-collaterals' => GuaranteeCollateralsController::class,
        'transaction-credit-report' => TransactionCreditReportController::class,
        'transaction-credit-inquiry' => TransactionCreditReportController::class,

        // KYC Services (SMS Required)
        'liveness-inquiry' => LivenessInquiryController::class,
        'expats-inquiries' => ExpatsInquiriesController::class,
        'military-service-status' => MilitaryServiceStatusController::class,
        'passport-status-inquiry' => PassportStatusInquiryController::class,
        'inquiry-exit-ban' => InquiryExitBanController::class,

        // Utility Services (Jibit Provider)
        'postal-code-inquiry' => PostalCodeInquiryController::class,
        'postal-code' => PostalCodeInquiryController::class,

        // Additional Services (May need alternative APIs)
        'financial-judgment-inquiry' => FinancialJudgmentInquiryController::class,
        'subsidy-payment-inquiry' => SubsidyPaymentInquiryController::class,
        'subsidy-ranking' => SubsidyRankingController::class,
        'justice-stock-value-inquiry' => JusticeStockValueInquiryController::class,
        'social-security-insurance-inquiry' => SocialSecurityInsuranceInquiryController::class,
        'social-security-pension-order-inquiry' => SocialSecurityPensionOrderInquiryController::class,
        'civil-retiree-pension-payslip' => CivilRetireePensionPayslipController::class,
        'teachers-retiree-payslip' => TeachersRetireePensionPayslipController::class,
        'smart-id-card-status' => SmartIdCardStatusController::class,
        'rental-agreement-inquiry' => RentalAgreementInquiryController::class,
        'social-fund-insurance-copy' => SocialFundInsuranceCopyController::class,
        'active-sim-card-inquiry' => ActiveSimCardInquiryController::class,
        'electronic-voucher-inquiry' => ElectronicVoucherInquiryController::class,
        'car-information-and-insurance-discounts' => CarInformationAndInsuranceDiscountsController::class,
        'shahab-number' => ShahabNumberController::class,

        // Aliases for backward compatibility
        'iban-check' => IbanCheckController::class,
    ];

    /**
     * Controller instance cache to prevent duplicate instantiations during single request
     *
     * @var array
     */
    private static $controllerCache = [];

    /**
     * Get the appropriate service controller for a given service
     *
     * @param Service $service
     * @return BaseServiceController|null
     */
    public static function getController(Service $service): ?BaseServiceController
    {
        // For sub-services, use parent service slug for controller lookup
        $slug = $service->parent_id ? $service->parent->slug : $service->slug;
        
        // Create cache key based on service ID to handle sub-services correctly
        $cacheKey = 'service_' . $service->id . '_' . $slug;
        
        // Return cached instance if available
        if (isset(self::$controllerCache[$cacheKey])) {
            return self::$controllerCache[$cacheKey];
        }
        
        // Log when using parent slug for sub-services
        if ($service->parent_id) {
            Log::info('Using parent service slug for sub-service controller lookup', [
                'sub_service_slug' => $service->slug,
                'parent_service_slug' => $service->parent->slug,
                'using_slug' => $slug
            ]);
        }
        
        // Check direct mapping first
        if (isset(self::$serviceMapping[$slug])) {
            $controllerClass = self::$serviceMapping[$slug];
            
            // Check if controller class exists
            if (class_exists($controllerClass)) {
                $controller = app($controllerClass);
                // Cache the instance
                self::$controllerCache[$cacheKey] = $controller;
                return $controller;
            } else {
                Log::warning("Service controller class not found: {$controllerClass} for service: {$slug}");
                return null;
            }
        }

        // Try to find controller by converting slug to controller name
        $controllerClass = self::generateControllerClassName($slug);
        
        if (class_exists($controllerClass)) {
            $controller = app($controllerClass);
            // Cache the instance
            self::$controllerCache[$cacheKey] = $controller;
            return $controller;
        }

        // Log the missing controller for debugging
        Log::warning("No service controller found for service slug: {$slug}");
        
        return null;
    }

    /**
     * Clear controller cache (useful for testing or long-running processes)
     *
     * @return void
     */
    public static function clearCache(): void
    {
        self::$controllerCache = [];
    }

    /**
     * Generate controller class name from service slug
     *
     * @param string $slug
     * @return string
     */
    private static function generateControllerClassName(string $slug): string
    {
        // Convert kebab-case to PascalCase
        $className = Str::studly(str_replace('-', '_', $slug));
        
        // Add Controller suffix
        return "App\\Http\\Controllers\\Services\\{$className}Controller";
    }

    /**
     * Check if a service has a dedicated controller
     *
     * @param Service $service
     * @return bool
     */
    public static function hasController(Service $service): bool
    {
        return self::getController($service) !== null;
    }

    /**
     * Get all available service controllers
     *
     * @return array
     */
    public static function getAvailableControllers(): array
    {
        return array_keys(self::$serviceMapping);
    }

    /**
     * Get controllers by category
     *
     * @return array
     */
    public static function getControllersByCategory(): array
    {
        return [
            'banking' => [
                'card-iban',
                'card-account', 
                'iban-account',
                'account-iban',
                'iban-validator',
                'iban-check',
            ],
            'vehicle' => [
                'car-violation-inquiry',
                'motor-violation-inquiry',
                'traffic-violation-image',
                'vehicle-ownership-inquiry',
                'active-plates-list',
                'plate-history-inquiry',
                'negative-license-score',
                'driving-license-status',
                'driver-risk-inquiry',
                'traffic-vehicle-inquiry',
                'toll-road-inquiry',
                'third-party-insurance-history',
                'car-information-and-insurance-discounts',
            ],
            'credit' => [
                'credit-score-rating',
                'loan-inquiry',
                'loan-guarantee-inquiry',
                'check-color',
                'cheque-inquiry',
                'coming-check-inquiry',
                'inquiry-makna-code',
            ],
            'kyc' => [
                'liveness-inquiry',
                'expats-inquiries',
                'military-service-status',
                'passport-status-inquiry',
                'inquiry-exit-ban',
            ],
            'government' => [
                'financial-judgment-inquiry',
                'subsidy-payment-inquiry',
                'subsidy-ranking',
                'justice-stock-value-inquiry',
                'social-security-insurance-inquiry',
                'social-security-pension-order-inquiry',
                'civil-retiree-pension-payslip',
                'teachers-retiree-payslip',
                'smart-id-card-status',
                'rental-agreement-inquiry',
                'social-fund-insurance-copy',
                'active-sim-card-inquiry',
                'electronic-voucher-inquiry',
                'shahab-number',
            ],
        ];
    }

    /**
     * Get implemented controllers
     *
     * @return array
     */
    public static function getImplementedControllers(): array
    {
        $implemented = [];
        
        foreach (self::$serviceMapping as $slug => $controllerClass) {
            if (class_exists($controllerClass)) {
                $implemented[] = $slug;
            }
        }
        
        return $implemented;
    }

    /**
     * Get unimplemented controllers
     *
     * @return array
     */
    public static function getUnimplementedControllers(): array
    {
        $unimplemented = [];
        
        foreach (self::$serviceMapping as $slug => $controllerClass) {
            if (!class_exists($controllerClass)) {
                $unimplemented[] = $slug;
            }
        }
        
        return $unimplemented;
    }

    /**
     * Register a new service controller mapping
     *
     * @param string $slug
     * @param string $controllerClass
     * @return void
     */
    public static function registerController(string $slug, string $controllerClass): void
    {
        self::$serviceMapping[$slug] = $controllerClass;
    }
} 