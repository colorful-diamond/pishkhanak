<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Finnotech API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Finnotech financial services API integration
    |
    */

    'base_url' => env('FINNOTECH_BASE_URL', 'https://api.finnotech.ir'),
    'sandbox_url' => env('FINNOTECH_SANDBOX_URL', 'https://sandboxapi.finnotech.ir'),
    'client_id' => env('FINNOTECH_CLIENT_ID'),
    'client_secret' => env('FINNOTECH_CLIENT_SECRET'),
    'sandbox' => env('FINNOTECH_SANDBOX', false), // Changed from true to false - use production API by default
    'timeout' => env('FINNOTECH_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Service Scopes
    |--------------------------------------------------------------------------
    |
    | Finnotech API scopes for different services
    |
    */
    'scopes' => [
        // Vehicle Services
        'car_violation_inquiry' => 'billing:driving-offense-inquiry:get',
        'motor_violation_inquiry' => 'billing:riding-offense-inquiry:get',
        'traffic_violation_image' => 'billing:traffic-offense-image:get',
        'vehicle_ownership_inquiry' => 'vehicle:matching-vehicle-ownership:get',
        'active_plates_list' => 'vehicle:active-plate-numbers:get',
        'plate_history_inquiry' => 'vehicle:plate-number-history:get',
        'negative_license_score' => 'billing:cc-negative-score:get',
        'driving_license_status' => 'kyc:cc-license-check-inquiry:post',
        'driver_risk_inquiry' => 'kyc:drivers-risk-inquiry:get',
        'traffic_vehicle_inquiry' => 'vehicle:traffic-toll-inquiry:get',
        'toll_road_inquiry' => 'ecity:freeway-toll-inquiry:get',
        'third_party_insurance_history' => 'kyc:third-party-insurance-inquiry:get',

        // Banking & Financial Services (Client Credential)
        'card_iban' => 'facility:card-to-iban:get',
        'card_account' => 'facility:card-to-deposit:get', 
        'account_iban' => 'facility:deposit-to-iban:get',
        'iban_account' => 'facility:iban-to-deposit:get',
        'iban_check' => 'facility:iban-inquiry:get',

        // Credit & Loan Services (Client Credential)
        'credit_score_rating' => 'credit:macna-inquiry:get',
        'check_color' => 'credit:cheque-color-inquiry:get',
        'inquiry_makna_code' => 'credit:macna-inquiry:get',
        'cheque_book_inquiry' => 'credit:chequebook-inquiry:get',
        'guarantee_details' => 'credit:guarantee-details:get',
        'guarantee_collaterals' => 'credit:guarantee-collaterals:get',
        'sayad_serial_inquiry' => 'credit:sayad-serial-inquiry:get',
        'sayad_id_by_serial' => 'credit:sayad-id-by-serial:get',
        'sayad_issuer_inquiry' => 'credit:cc-sayad-issuer-inquiry-cheque:post',

        // Credit & Loan Services (SMS Authentication Required) 
        'loan_inquiry' => 'credit:sms-facility-inquiry:get',
        'loan_guarantee_inquiry' => 'credit:sms-guaranty-inquiry:get',
        'cheque_inquiery' => 'credit:sms-back-cheques:get',
        'cheque_color_sms' => 'credit:sms-cheque-color-inquiry:get',
        'sayad_inquiry_sms' => 'credit:sayadi-cheque-inquiry-sms:get',
        'sayad_issuer_inquiry_sms' => 'credit:sms-sayad-issuer-inquiry-cheque:post',
        'transaction_credit_report' => 'kyc:transaction-credit-inquiry-request:get',

        // Sayad Cheque Services (Authorization Code Required)
        'sayad_issue_cheque' => 'credit:ac-sayad-issue-cheque:post',
        'sayad_transfer_cheque' => 'credit:ac-sayad-transfer-cheque:post',

        // Sayad Cheque Services (SMS Authorization Required)
        'sayad_transfer_cheque_legal' => 'credit:sms-sayad-transfer-cheque:post', 
        'sayad_accept_cheque_sms' => 'credit:sayadi-cheque-accept-sms:post',
        'sayad_cancel_cheque_sms' => 'credit:sayadi-cheque-cancel-sms:post',

        // KYC Services
        'liveness_inquiry' => 'kyc:death-status-inquiry:get',
        'expats_inquiries' => 'kyc:foreigner-id-inquiry:get',
        'military_service_status' => 'kyc:cc-military-inquiry:get',
        'passport_status_inquiry' => 'kyc:passport-inquiry:get',

        // Insurance Services
        'social_security_insurance_inquiry' => 'kyc:social-security-insurance:get',
        'car_information_and_insurance_discounts' => 'vehicle:car-info-insurance-discounts:get',

        // Bill Services
        'bill_inquiry' => 'billing:billing-inquiry:get',
        'bill_inquiry_detail' => 'billing:detail-billing-inquiry:get',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    |
    | Finnotech API endpoints for different services
    |
    */
    'endpoints' => [
        // Vehicle Services
        'car_violation_inquiry' => '/billing/v2/clients/{clientId}/drivingOffense',
        'motor_violation_inquiry' => '/billing/v2/clients/{clientId}/ridingOffense',
        'traffic_violation_image' => '/billing/v2/clients/{clientId}/trafficOffenseImage',
        'vehicle_ownership_inquiry' => '/vehicle/v2/clients/{clientId}/matchingVehicleOwnership',
        'active_plates_list' => '/vehicle/v2/clients/{clientId}/activePlateNumbers',
        'plate_history_inquiry' => '/vehicle/v2/clients/{clientId}/plateNumberHistory',
        'negative_license_score' => '/billing/v2/clients/{clientId}/negativeScore',
        'driving_license_status' => '/kyc/v2/clients/{clientId}/licenseCheckInquiry',
        'driver_risk_inquiry' => '/kyc/v2/clients/{clientId}/driversRiskInquiry',
        'traffic_vehicle_inquiry' => '/vehicle/v2/clients/{clientId}/trafficTollInquiry',
        'toll_road_inquiry' => '/ecity/v2/clients/{clientId}/freewayTollInquiry',
        'third_party_insurance_history' => '/kyc/v2/clients/{clientId}/thirdPartyInsuranceInquiry',

        // Banking & Financial Services
        'card_iban' => '/facility/v2/clients/{clientId}/cardToIban',
        'card_account' => '/facility/v2/clients/{clientId}/cardToDeposit',
        'account_iban' => '/facility/v2/clients/{clientId}/depositToIban',
        'iban_account' => '/facility/v2/clients/{clientId}/ibanToDeposit',
        'iban_check' => '/facility/v2/clients/{clientId}/ibanInquiry',

        // Credit & Loan Services (Client Credential)
        'credit_score_rating' => '/credit/v2/clients/{clientId}/macnaInquiry',
        'check_color' => '/credit/v2/clients/{clientId}/chequeColorInquiry',
        'inquiry_makna_code' => '/credit/v2/clients/{clientId}/macnaInquiry',
        'cheque_book_inquiry' => '/credit/v2/clients/{clientId}/chequebookInquiry',
        'guarantee_details' => '/credit/v2/clients/{clientId}/guaranteeDetails',
        'guarantee_collaterals' => '/credit/v2/clients/{clientId}/guaranteeCollaterals',
        'sayad_serial_inquiry' => '/credit/v2/clients/{clientId}/sayadSerialInquiry',
        'sayad_id_by_serial' => '/credit/v2/clients/{clientId}/sayadIdBySerial',
        'sayad_issuer_inquiry' => '/credit/v2/clients/{clientId}/users/{user}/sayadIssuerInquiryCheque',

        // Credit & Loan Services (SMS Authentication Required)
        'loan_inquiry' => '/credit/v2/clients/{clientId}/users/{user}/sms/facilityInquiry',
        'loan_guarantee_inquiry' => '/credit/v2/clients/{clientId}/users/{user}/sms/guarantyInquiry',
        'cheque_inquiery' => '/credit/v2/clients/{clientId}/users/{user}/sms/backCheques',
        'cheque_color_sms' => '/credit/v2/clients/{clientId}/sms/chequeColorInquiry',
        'sayad_inquiry_sms' => '/credit/v2/clients/{clientId}/users/{user}/sms/sayadiChequeInquiry',
        'sayad_issuer_inquiry_sms' => '/credit/v2/clients/{clientId}/users/{user}/sms/sayadIssuerInquiryCheque',
        'transaction_credit_report' => '/kyc/v2/clients/{clientId}/transactionCreditInquiryRequest',

        // Sayad Cheque Services (Authorization Code Required)
        'sayad_issue_cheque' => '/credit/v2/clients/{clientId}/ac/sayadIssueCheque',
        'sayad_transfer_cheque' => '/credit/v2/clients/{clientId}/ac/sayadTransferCheque',

        // Sayad Cheque Services (SMS Authorization Required)
        'sayad_transfer_cheque_legal' => '/clients/{clientId}/sms/sayadTransferCheque',
        'sayad_accept_cheque_sms' => '/credit/v2/clients/{clientId}/users/{user}/sms/sayadiChequeAccept',
        'sayad_cancel_cheque_sms' => '/credit/v2/clients/{clientId}/users/{user}/sms/sayadiChequeCancel',

        // KYC Services
        'liveness_inquiry' => '/kyc/v2/clients/{clientId}/deathStatusInquiry',
        'expats_inquiries' => '/kyc/v2/clients/{clientId}/foreignerIdInquiry',
        'military_service_status' => '/kyc/v2/clients/{clientId}/militaryInquiry',
        'passport_status_inquiry' => '/kyc/v2/clients/{clientId}/passportInquiry',

        // Insurance Services
        'social_security_insurance_inquiry' => '/kyc/v2/clients/{clientId}/socialSecurityInsuranceInquiry',
        'car_information_and_insurance_discounts' => '/vehicle/v2/clients/{clientId}/carInfoInsuranceDiscounts',

        // Bill Services
        'bill_inquiry' => '/billing/v2/clients/{clientId}/billingInquiry',
        'bill_inquiry_detail' => '/billing/v2/clients/{clientId}/detailBillingInquiry',

        // System
        'wages' => '/dev/v2/clients/{clientId}/wages',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Types
    |--------------------------------------------------------------------------
    |
    | Define which authentication method each service requires
    |
    */
    'auth_types' => [
        // Client Credential Services
        'client_credential' => [
            'car_violation_inquiry',
            'motor_violation_inquiry', 
            'traffic_violation_image',
            'vehicle_ownership_inquiry',
            'active_plates_list',
            'plate_history_inquiry',
            'negative_license_score',
            'traffic_vehicle_inquiry',
            'toll_road_inquiry',
            'third_party_insurance_history',
            'card_iban',
            'card_account', 
            'account_iban',
            'iban_account',
            'iban_check',
            'credit_score_rating',
            'check_color',
            'inquiry_makna_code',
            'cheque_book_inquiry',
            'guarantee_details',
            'guarantee_collaterals',
            'sayad_serial_inquiry',
            'sayad_id_by_serial',
            'sayad_issuer_inquiry',
            'liveness_inquiry',
            'expats_inquiries',
            'military_service_status',
            'passport_status_inquiry',
            'social_security_insurance_inquiry',
            'car_information_and_insurance_discounts',
            'bill_inquiry',
            'bill_inquiry_detail',
        ],

        // SMS Authorization Services
        'sms_authorization' => [
            'loan_inquiry',
            'loan_guarantee_inquiry', 
            'cheque_inquiery',
            'cheque_color_sms',
            'sayad_inquiry_sms',
            'sayad_issuer_inquiry_sms',
            'sayad_transfer_cheque_legal',
            'sayad_accept_cheque_sms',
            'sayad_cancel_cheque_sms',
        ],

        // Authorization Code Services (User Consent Required)
        'authorization_code' => [
            'driving_license_status',
            'driver_risk_inquiry',
            'sayad_issue_cheque',
            'sayad_transfer_cheque',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Pricing (in Toman)
    |--------------------------------------------------------------------------
    |
    | Default pricing for services - can be overridden in database
    |
    */
    'pricing' => [
        // Vehicle Services (Medium pricing)
        'car_violation_inquiry' => 3000,
        'motor_violation_inquiry' => 2500,
        'traffic_violation_image' => 1500,
        'vehicle_ownership_inquiry' => 2000,
        'active_plates_list' => 2500,
        'plate_history_inquiry' => 3000,
        'negative_license_score' => 2000,
        'driving_license_status' => 3500, // Authorization Code required
        'driver_risk_inquiry' => 3500, // Authorization Code required
        'traffic_vehicle_inquiry' => 2000,
        'toll_road_inquiry' => 2000,
        'third_party_insurance_history' => 10000,

        // Banking & Financial Services (Lower pricing for basic services)
        'card_iban' => 1500,
        'card_account' => 1500,
        'account_iban' => 1500,
        'iban_account' => 1500,
        'iban_check' => 2000,

        // Credit & Loan Services - Client Credential (Medium pricing)
        'credit_score_rating' => 3500,
        'check_color' => 2500,
        'inquiry_makna_code' => 3500,
        'cheque_book_inquiry' => 3000,
        'guarantee_details' => 3500,
        'guarantee_collaterals' => 3500,
        'sayad_serial_inquiry' => 2500,
        'sayad_id_by_serial' => 2500,
        'sayad_issuer_inquiry' => 4000,

        // Credit & Loan Services - SMS Authentication (Higher pricing due to SMS requirement)
        'loan_inquiry' => 5000,
        'loan_guarantee_inquiry' => 5000,
        'cheque_inquiery' => 4500,
        'cheque_color_sms' => 3500,
        'sayad_inquiry_sms' => 4500,
        'sayad_issuer_inquiry_sms' => 5000,
        'transaction_credit_report' => 6000,

        // Sayad Cheque Services - Authorization Code (Higher pricing due to user consent flow)
        'sayad_issue_cheque' => 6000,
        'sayad_transfer_cheque' => 6000,

        // Sayad Cheque Services - SMS Authorization (Highest pricing due to complex SMS flow)
        'sayad_transfer_cheque_legal' => 6500,
        'sayad_accept_cheque_sms' => 5500,
        'sayad_cancel_cheque_sms' => 5500,

        // KYC Services (Medium to high pricing)
        'liveness_inquiry' => 3000,
        'expats_inquiries' => 3500,
        'military_service_status' => 3000,
        'passport_status_inquiry' => 3500,

        // Insurance Services (Medium pricing)
        'social_security_insurance_inquiry' => 3000,
        'car_information_and_insurance_discounts' => 3500,

        // Bill Services (Lower pricing for basic inquiry)
        'bill_inquiry' => 2000,
        'bill_inquiry_detail' => 2500,
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Required Services
    |--------------------------------------------------------------------------
    |
    | Services that require SMS verification and higher user balance
    |
    */
    'sms_required_services' => [
        'loan_inquiry',
        'loan_guarantee_inquiry', 
        'cheque_inquiery',
        'cheque_color_sms',
        'sayad_inquiry_sms',
        'sayad_issuer_inquiry_sms',
        'sayad_transfer_cheque_legal',
        'sayad_accept_cheque_sms',
        'sayad_cancel_cheque_sms',
        'driving_license_status',
        'driver_risk_inquiry',
        'sayad_issue_cheque',
        'sayad_transfer_cheque',
        'social_security_insurance_inquiry',
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Methods
    |--------------------------------------------------------------------------
    |
    | HTTP methods for different services
    |
    */
    'http_methods' => [
        'driving_license_status' => 'POST',
        'passport_status_inquiry' => 'POST',
        // All others default to GET
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | API retry settings
    |
    */
    'retry' => [
        'max_attempts' => 3,
        'delay_ms' => 1000,
        'backoff_multiplier' => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Mapping
    |--------------------------------------------------------------------------
    |
    | Common response field mappings for different result types
    |
    */
    'response_mapping' => [
        'vehicle_violations' => [
            'bills' => 'Bills',
            'total_amount' => 'TotalAmount',
            'violation_count' => 'count(Bills)',
        ],
        'vehicle_info' => [
            'is_valid' => 'isValid',
            'vehicle_type' => 'vehicleType',
            'vehicle_tip' => 'vehicleTip',
            'vehicle_model' => 'vehicleModel',
        ],
        'plate_history' => [
            'history' => 'plateHistory',
            'status' => 'plateStatus',
            'trace_plate' => 'tracePlate',
        ],
        'license_score' => [
            'license_number' => 'LicenseNumber',
            'negative_score' => 'NegativeScore',
            'offense_count' => 'OffenseCount',
            'rule' => 'Rule',
        ],
    ],
]; 