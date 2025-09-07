# Complete Service Implementation Plan

## Overview

This document outlines the complete implementation plan for all 44 services in the database, mapped to Finnotech APIs and structured for optimal user experience.

## Implementation Status

### ✅ Completed (10 services)
- **card-iban** → CardIbanController (✅ Implemented)
- **card-account** → CardAccountController (✅ Implemented) 
- **iban-account** → IbanAccountController (✅ Implemented)
- **account-iban** → IbanAccountController (✅ Implemented)
- **iban-check** → IbanValidatorController (✅ Implemented)

### 🏗️ In Progress (1 service)
- **car-violation-inquiry** → CarViolationInquiryController (🔧 Example Implementation Created)

### 📋 To Be Implemented (33 services)

## Phase 1: Vehicle Services (High Priority) - 13 services

### 1.1 Traffic Violations (3 services)
1. **motor-violation-inquiry** → MotorViolationInquiryController
   - API: `/billing/v2/clients/{clientId}/ridingOffense`
   - Scope: `billing:riding-offense-inquiry:get`
   - Parameters: `mobile`, `national_id`, `plate_number`
   - SMS: ❌ No
   - Price: 2,500 تومان

2. **traffic-violation-image** → TrafficViolationImageController
   - API: `/billing/v2/clients/{clientId}/trafficOffenseImage`
   - Scope: `billing:traffic-offense-image:get`
   - Parameters: `offense_id`
   - SMS: ❌ No
   - Price: 1,500 تومان

### 1.2 Vehicle Information (4 services)
3. **vehicle-ownership-inquiry** → VehicleOwnershipInquiryController
   - API: `/vehicle/v2/clients/{clientId}/matchingVehicleOwnership`
   - Scope: `vehicle:matching-vehicle-ownership:get`
   - Parameters: `national_id`, `plate_number`
   - SMS: ❌ No
   - Price: 2,000 تومان

4. **active-plates-list** → ActivePlatesListController
   - API: `/vehicle/v2/clients/{clientId}/activePlateNumbers`
   - Scope: `vehicle:active-plate-numbers:get`
   - Parameters: `national_id`, `mobile`
   - SMS: ❌ No
   - Price: 2,500 تومان

5. **plate-history-inquiry** → PlateHistoryInquiryController
   - API: `/vehicle/v2/clients/{clientId}/plateNumberHistory`
   - Scope: `vehicle:plate-number-history:get`
   - Parameters: `national_id`, `plate_number`
   - SMS: ❌ No
   - Price: 3,000 تومان

6. **car-information-and-insurance-discounts** → CarInformationAndInsuranceDiscountsController
   - API: 🔍 Need to research (not in provided docs)
   - SMS: ❌ No
   - Price: 3,000 تومان

### 1.3 License & Driver (3 services)
7. **negative-license-score** → NegativeLicenseScoreController
   - API: `/billing/v2/clients/{clientId}/negativeScore`
   - Scope: `billing:cc-negative-score:get`
   - Parameters: `license_number`, `national_id`, `mobile`
   - SMS: ❌ No
   - Price: 2,000 تومان

8. **driving-license-status** → DrivingLicenseStatusController
   - API: `/kyc/v2/clients/{clientId}/licenseCheckInquiry`
   - Scope: `kyc:cc-license-check-inquiry:post`
   - Parameters: `national_id`, `mobile`
   - HTTP Method: POST
   - SMS: ❌ No
   - Price: 2,500 تومان

9. **driver-risk-inquiry** → DriverRiskInquiryController
   - API: `/kyc/v2/clients/{clientId}/driversRiskInquiry`
   - Scope: `kyc:drivers-risk-inquiry:get`
   - Parameters: `national_id`, `mobile`, `license_number`
   - SMS: ❌ No
   - Price: 3,000 تومان

### 1.4 Traffic & Toll (2 services)
10. **traffic-vehicle-inquiry** → TrafficVehicleInquiryController
    - API: `/vehicle/v2/clients/{clientId}/trafficTollInquiry`
    - Scope: `vehicle:traffic-toll-inquiry:get`
    - Parameters: `plate_number`, `mobile`, `national_id`
    - SMS: ❌ No
    - Price: 2,000 تومان

11. **toll-road-inquiry** → TollRoadInquiryController
    - API: `/ecity/v2/clients/{clientId}/freewayTollInquiry`
    - Scope: `ecity:freeway-toll-inquiry:get`
    - Parameters: `plate_number`
    - SMS: ❌ No
    - Price: 2,000 تومان

### 1.5 Insurance (1 service)
12. **third-party-insurance-history** → ThirdPartyInsuranceHistoryController
    - API: `/kyc/v2/clients/{clientId}/thirdPartyInsuranceInquiry`
    - Scope: `kyc:third-party-insurance-inquiry:get`
    - Parameters: `national_id`, `mobile`, `plate_number`
    - SMS: ❌ No
    - Price: 3,000 تومان

## Phase 2: Credit & Banking Services (Medium Priority) - 7 services

### 2.1 Credit Services (SMS Required)
13. **credit-score-rating** → CreditScoreRatingController
    - API: `/credit/v2/clients/{clientId}/macnaInquiry`
    - Scope: `credit:macna-inquiry:get`
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 5,000 تومان

14. **loan-inquiry** → LoanInquiryController
    - API: `/credit/v2/clients/{clientId}/facilityInquiry`
    - Scope: `credit:sms-facility-inquiry:get`
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 4,000 تومان

15. **loan-guarantee-inquiry** → LoanGuaranteeInquiryController
    - API: `/credit/v2/clients/{clientId}/guaranteeDetails`
    - Scope: `credit:guarantee-details:get`
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 4,000 تومان

### 2.2 Check Services (SMS Required)
16. **check-color** → CheckColorController
    - API: `/credit/v2/clients/{clientId}/chequeColorInquiry`
    - Scope: `credit:cheque-color-inquiry:get`
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,500 تومان

17. **cheque-inquiry** → ChequeInquiryController
    - API: `/credit/v2/clients/{clientId}/backCheques`
    - Scope: `credit:sms-back-cheques:get`
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 4,000 تومان

18. **coming-check-inquiry** → ComingChequeInquiryController
    - API: 🔍 Need to research (may be same as above)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,500 تومان

### 2.3 Special Services
19. **inquiry-makna-code** → InquiryMaknaCodeController
    - API: `/credit/v2/clients/{clientId}/macnaInquiry`
    - Scope: `credit:macna-inquiry:get`
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,500 تومان

## Phase 3: KYC & Government Services (Lower Priority) - 16 services

### 3.1 Identity & Life Status
20. **liveness-inquiry** → LivenessInquiryController
    - API: `/kyc/v2/clients/{clientId}/deathStatusInquiry`
    - Scope: `kyc:death-status-inquiry:get`
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,000 تومان

21. **expats-inquiries** → ExpatsInquiriesController
    - API: `/kyc/v2/clients/{clientId}/foreignerIdInquiry`
    - Scope: `kyc:foreigner-id-inquiry:get`
    - Parameters: `foreigner_id`
    - SMS: ✅ Yes
    - Price: 3,500 تومان

### 3.2 Military & Government
22. **military-service-status** → MilitaryServiceStatusController
    - API: `/kyc/v2/clients/{clientId}/militaryInquiry`
    - Scope: `kyc:military-inquiry:get`
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,000 تومان

23. **passport-status-inquiry** → PassportStatusInquiryController
    - API: `/kyc/v2/clients/{clientId}/passportInquiry`
    - Scope: `kyc:passport-inquiry:post`
    - Parameters: `national_id`, `passport_number`
    - HTTP Method: POST
    - SMS: ✅ Yes
    - Price: 3,500 تومان

24. **inquiry-exit-ban** → InquiryExitBanController
    - API: 🔍 Need to research (likely related to passport)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,500 تومان

### 3.3 Financial Government Services (May need alternative APIs)
25. **subsidy-payment-inquiry** → SubsidyPaymentInquiryController
    - API: 🔍 Not found in Finnotech (may be government API)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,000 تومان

26. **subsidy-ranking** → SubsidyRankingController
    - API: 🔍 Not found in Finnotech (may be government API)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,000 تومان

27. **justice-stock-value-inquiry** → JusticeStockValueInquiryController
    - API: 🔍 Not found in Finnotech (may be government API)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,500 تومان

### 3.4 Social Security & Pension
28. **social-security-insurance-inquiry** → SocialSecurityInsuranceInquiryController
    - API: 🔍 Not found in Finnotech (may be government API)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,000 تومان

29. **social-security-pension-order-inquiry** → SocialSecurityPensionOrderInquiryController
    - API: 🔍 Not found in Finnotech (may be government API)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,500 تومان

30. **civil-retiree-pension-payslip** → CivilRetireePensionPayslipController
    - API: 🔍 Not found in Finnotech (may be government API)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,000 تومان

31. **teachers-retiree-payslip** → TeachersRetireePensionPayslipController
    - API: 🔍 Not found in Finnotech (may be government API)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 3,000 تومان

### 3.5 Document & Card Services
32. **smart-id-card-status** → SmartIdCardStatusController
    - API: 🔍 Not found in Finnotech (may be government API)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 2,500 تومان

33. **rental-agreement-inquiry** → RentalAgreementInquiryController
    - API: 🔍 Not found in Finnotech (may be government API)
    - Parameters: `national_id`, `contract_id`
    - SMS: ✅ Yes
    - Price: 3,000 تومان

34. **social-fund-insurance-copy** → SocialFundInsuranceCopyController
    - API: 🔍 Not found in Finnotech (may be government API)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 2,500 تومان

### 3.6 Communication & Utility
35. **active-sim-card-inquiry** → ActiveSimCardInquiryController
    - API: 🔍 Not found in Finnotech (may be telecom API)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 2,000 تومان

36. **electronic-voucher-inquiry** → ElectronicVoucherInquiryController
    - API: 🔍 Not found in Finnotech (may be government API)
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 2,500 تومان

37. **shahab-number** → ShahabNumberController
    - API: 🔍 Need to find in facility services
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 2,000 تومان

### 3.7 Financial & Legal
38. **financial-judgment-inquiry** → FinancialJudgmentInquiryController
    - API: 🔍 Possibly related to credit services
    - Parameters: `national_id`
    - SMS: ✅ Yes
    - Price: 4,000 تومان

## Implementation Checklist for Each Service

### 1. Controller Implementation
- [ ] Create controller class extending `BaseFinnotechController`
- [ ] Configure service settings (`configureService()`)
- [ ] Implement `prepareApiParameters()`
- [ ] Implement `formatResponseData()`
- [ ] Implement `formatResultForDisplay()`
- [ ] Set appropriate `getResultType()`

### 2. Form Creation
- [ ] Create input form blade template
- [ ] Add proper validation rules
- [ ] Include real-time JavaScript validation
- [ ] Add helper modals/guides where needed
- [ ] Show service cost and wallet balance

### 3. Result Template
- [ ] Create specialized result template
- [ ] Format data for optimal display
- [ ] Add export/print functionality
- [ ] Include appropriate action buttons
- [ ] Handle empty/error states

### 4. SMS Integration (for SMS-required services)
- [ ] Implement SMS verification flow
- [ ] Create OTP confirmation page
- [ ] Handle SMS failures gracefully
- [ ] Add resend functionality

### 5. Configuration Updates
- [ ] Update `config/finnotech.php`
- [ ] Add to `ServiceControllerFactory`
- [ ] Update service pricing in database
- [ ] Add service-specific validation rules

### 6. Testing
- [ ] Unit tests for controller
- [ ] Integration tests with API
- [ ] UI/UX testing
- [ ] Error scenario testing
- [ ] Performance testing

## Priority Implementation Order

### Week 1-2: Phase 1 Vehicle Services (13 services)
Focus on high-traffic, revenue-generating services:
1. motor-violation-inquiry
2. vehicle-ownership-inquiry
3. active-plates-list
4. negative-license-score
5. driving-license-status
6. traffic-vehicle-inquiry
7. toll-road-inquiry
8. third-party-insurance-history
9. traffic-violation-image
10. plate-history-inquiry
11. driver-risk-inquiry
12. car-information-and-insurance-discounts

### Week 3-4: Phase 2 Credit Services (7 services)
High-value services with SMS verification:
1. credit-score-rating
2. loan-inquiry
3. check-color
4. cheque-inquiry
5. loan-guarantee-inquiry
6. inquiry-makna-code
7. coming-check-inquiry

### Week 5-6: Phase 3 KYC Services (5 services)
Essential identity verification services:
1. liveness-inquiry
2. military-service-status
3. passport-status-inquiry
4. expats-inquiries
5. inquiry-exit-ban

### Week 7-8: Phase 4 Research & Alternative APIs (13 services)
Services requiring research or alternative API sources:
1. financial-judgment-inquiry
2. subsidy-payment-inquiry
3. subsidy-ranking
4. justice-stock-value-inquiry
5. social-security-insurance-inquiry
6. social-security-pension-order-inquiry
7. civil-retiree-pension-payslip
8. teachers-retiree-payslip
9. smart-id-card-status
10. rental-agreement-inquiry
11. social-fund-insurance-copy
12. active-sim-card-inquiry
13. electronic-voucher-inquiry

## Technical Requirements

### Environment Variables (.env)
```bash
FINNOTECH_CLIENT_ID=your_client_id
FINNOTECH_CLIENT_SECRET=your_client_secret
FINNOTECH_SANDBOX=true
FINNOTECH_BASE_URL=https://api.finnotech.ir
FINNOTECH_SANDBOX_URL=https://sandboxapi.finnotech.ir
FINNOTECH_TIMEOUT=30
```

### Database Updates Required
1. Update service pricing in database
2. Set `is_paid = true` and appropriate `price` values
3. Update service content with proper descriptions
4. Set service categories correctly

### SMS Service Integration
- Implement SMS verification system
- Create OTP confirmation flows
- Handle SMS provider integration
- Add fallback mechanisms

### Result Storage & Caching
- Optimize ServiceResult storage
- Implement result caching strategy
- Add result expiration handling
- Create result archiving system

## Success Metrics

### Technical Metrics
- [ ] All 44 services have working controllers
- [ ] 100% API integration success rate
- [ ] < 5 second average response time
- [ ] 99.9% uptime for service processing

### Business Metrics
- [ ] Increased service usage by 300%
- [ ] Revenue growth from paid services
- [ ] User satisfaction > 4.5/5
- [ ] Reduced support tickets by 50%

### Quality Metrics
- [ ] All services have comprehensive error handling
- [ ] Mobile-responsive design on all forms
- [ ] Accessibility compliance (WCAG 2.1)
- [ ] Security audit passed

## Risk Mitigation

### API Dependencies
- Implement circuit breaker patterns
- Add fallback mechanisms
- Create comprehensive error handling
- Monitor API health continuously

### Performance Risks
- Implement caching strategies
- Add rate limiting
- Monitor response times
- Optimize database queries

### Security Considerations
- Validate all input data
- Implement proper authentication
- Secure API credentials
- Add audit logging

This comprehensive plan ensures systematic implementation of all 44 services with proper structure, testing, and quality assurance. 