# Finnotech Service Mapping - Complete Analysis

This document maps all 44 services in our database to the appropriate Finnotech API endpoints based on the provided documentation.

## Service Categories and Mapping

### 1. Vehicle & Car Services (14 services)

#### 1.1 Traffic Violations & Offenses
- **car-violation-inquiry** → `/billing/v2/clients/{clientId}/drivingOffense` (Scope: `billing:driving-offense-inquiry:get`)
  - Parameters: mobile, nationalID, plateNumber (9-digit encoded)
  - Response: Array of violations with details, amounts, billId, paymentId
  - SMS Required: No
  
- **motor-violation-inquiry** → `/billing/v2/clients/{clientId}/ridingOffense` (Scope: `billing:riding-offense-inquiry:get`)
  - Parameters: mobile, nationalID, plateNumber
  - Response: Amount, BillID, PaymentID, ComplaintStatus
  - SMS Required: No

- **traffic-violation-image** → `/billing/v2/clients/{clientId}/trafficOffenseImage` (Scope: `billing:traffic-offense-image:get`)
  - Parameters: offenseId
  - Response: PlateImageUrl, VehicleImageUrl
  - SMS Required: No

#### 1.2 Vehicle Information & Ownership
- **vehicle-ownership-inquiry** → `/vehicle/v2/clients/{clientId}/matchingVehicleOwnership` (Scope: `vehicle:matching-vehicle-ownership:get`)
  - Parameters: nationalId, plateNumber (9-digit)
  - Response: isValid, vehicleType, vehicleTip, vehicleModel
  - SMS Required: No

- **active-plates-list** → `/vehicle/v2/clients/{clientId}/activePlateNumbers` (Scope: `vehicle:active-plate-numbers:get`)
  - Parameters: nationalId, mobile
  - Response: Array of plate numbers with status
  - SMS Required: No

- **plate-history-inquiry** → `/vehicle/v2/clients/{clientId}/plateNumberHistory` (Scope: `vehicle:plate-number-history:get`)
  - Parameters: nationalId, plateNumber (9-digit)
  - Response: plateHistory array, plateStatus, tracePlate
  - SMS Required: No

- **car-information-and-insurance-discounts** → Need to check if this exists in Finnotech (not found in provided docs)

#### 1.3 License & Driver Related
- **negative-license-score** → `/billing/v2/clients/{clientId}/negativeScore` (Scope: `billing:cc-negative-score:get`)
  - Parameters: licenseNumber, nationalID, mobile
  - Response: LicenseNumber, NegativeScore, OffenseCount, Rule
  - SMS Required: No

- **driving-license-status** → `/kyc/v2/clients/{clientId}/licenseCheckInquiry` (Scope: `kyc:cc-license-check-inquiry:post`)
  - Parameters: nationalID, mobile (POST request)
  - Response: licenses array with detailed info
  - SMS Required: No

- **driver-risk-inquiry** → Likely `/kyc/v2/clients/{clientId}/driversRiskInquiry` (Scope: `kyc:drivers-risk-inquiry:get`)
  - Parameters: nationalID, mobile, licenseNumber
  - Response: Risk assessment data
  - SMS Required: No

#### 1.4 Traffic & Toll Services
- **traffic-vehicle-inquiry** → `/vehicle/v2/clients/{clientId}/trafficTollInquiry` (Scope: `vehicle:traffic-toll-inquiry:get`)
  - Parameters: plateNumber, mobile, nationalId
  - Response: bills array with amounts and dates
  - SMS Required: No

- **toll-road-inquiry** → `/ecity/v2/clients/{clientId}/freewayTollInquiry` (Scope: `ecity:freeway-toll-inquiry:get`)
  - Parameters: plateNumber (9-digit)
  - Response: bills array with toll information
  - SMS Required: No

#### 1.5 Insurance Related
- **third-party-insurance-history** → `/kyc/v2/clients/{clientId}/thirdPartyInsuranceInquiry` (Scope: `kyc:third-party-insurance-inquiry:get`)
  - Parameters: nationalID, mobile, plateNumber
  - Response: Insurance history and claims
  - SMS Required: No

### 2. Banking & Financial Services (8 services)

#### 2.1 Card & IBAN Conversion (Already Implemented)
- **card-iban** → `/facility/v2/clients/{clientId}/cardToIban` (Scope: `facility:card-to-iban:get`)
- **card-account** → `/facility/v2/clients/{clientId}/cardToDeposit` (Scope: `facility:card-to-deposit:get`)
- **account-iban** → `/facility/v2/clients/{clientId}/depositToIban` (Scope: `facility:deposit-to-iban:get`)
- **iban-account** → `/facility/v2/clients/{clientId}/ibanToDeposit` (Scope: `facility:iban-to-deposit:get`)
- **iban-check** → `/facility/v2/clients/{clientId}/ibanInquiry` (Scope: `facility:iban-inquiry:get`)

#### 2.2 Credit & Loan Services
- **credit-score-rating** → `/credit/v2/clients/{clientId}/macnaInquiry` (Scope: `credit:macna-inquiry:get`)
  - Parameters: nationalID
  - Response: Credit score and rating information
  - SMS Required: Yes (after login with sufficient balance)

- **loan-inquiry** → `/credit/v2/clients/{clientId}/facilityInquiry` (Scope: `credit:sms-facility-inquiry:get`)
  - Parameters: nationalID
  - Response: Loan and facility information
  - SMS Required: Yes

- **loan-guarantee-inquiry** → `/credit/v2/clients/{clientId}/guaranteeDetails` (Scope: `credit:guarantee-details:get`)
  - Parameters: nationalID
  - Response: Guarantee and collateral information
  - SMS Required: Yes

#### 2.3 Check Services
- **check-color** → `/credit/v2/clients/{clientId}/chequeColorInquiry` (Scope: `credit:cheque-color-inquiry:get`)
  - Parameters: nationalID
  - Response: Check status and color information
  - SMS Required: Yes

- **cheque-inquiry** → `/credit/v2/clients/{clientId}/backCheques` (Scope: `credit:sms-back-cheques:get`)
  - Parameters: nationalID
  - Response: Returned checks information
  - SMS Required: Yes

- **coming-check-inquiry** → May be part of same API or separate endpoint
  - SMS Required: Yes

#### 2.4 Special Banking Services
- **inquiry-makna-code** → `/credit/v2/clients/{clientId}/macnaInquiry` (Scope: `credit:macna-inquiry:get`)
  - Parameters: nationalID
  - Response: MACNA code and related information
  - SMS Required: Yes

- **shahab-number** → Need to find appropriate endpoint in facility services

### 3. Government & KYC Services (12 services)

#### 3.1 Identity & Life Status
- **liveness-inquiry** → `/kyc/v2/clients/{clientId}/deathStatusInquiry` (Scope: `kyc:death-status-inquiry:get`)
  - Parameters: nationalID
  - Response: Life/death status
  - SMS Required: Yes

- **expats-inquiries** → `/kyc/v2/clients/{clientId}/foreignerIdInquiry` (Scope: `kyc:foreigner-id-inquiry:get`)
  - Parameters: foreignerID
  - Response: Foreigner status and information
  - SMS Required: Yes

#### 3.2 Military & Government Services
- **military-service-status** → `/kyc/v2/clients/{clientId}/militaryInquiry` (Scope: `kyc:military-inquiry:get`)
  - Parameters: nationalID
  - Response: Military service status
  - SMS Required: Yes

- **passport-status-inquiry** → `/kyc/v2/clients/{clientId}/passportInquiry` (Scope: `kyc:passport-inquiry:post`)
  - Parameters: nationalID, passportNumber
  - Response: Passport status and validity
  - SMS Required: Yes

- **inquiry-exit-ban** → Likely related to passport or travel restrictions
  - SMS Required: Yes

#### 3.3 Financial Government Services
- **subsidy-payment-inquiry** → Not found in provided Finnotech docs (may be government API)
- **subsidy-ranking** → Not found in provided Finnotech docs
- **justice-stock-value-inquiry** → Not found in provided Finnotech docs

#### 3.4 Social Security & Pension
- **social-security-insurance-inquiry** → Not found in provided Finnotech docs
- **social-security-pension-order-inquiry** → Not found in provided Finnotech docs
- **civil-retiree-pension-payslip** → Not found in provided Finnotech docs
- **teachers-retiree-payslip** → Not found in provided Finnotech docs

#### 3.5 Document & Card Services
- **smart-id-card-status** → Not found in provided Finnotech docs
- **rental-agreement-inquiry** → Not found in provided Finnotech docs
- **social-fund-insurance-copy** → Not found in provided Finnotech docs

### 4. Communication & Utility Services (4 services)

- **active-sim-card-inquiry** → Not found in provided Finnotech docs
- **electronic-voucher-inquiry** → Not found in provided Finnotech docs

### 5. Financial & Legal Services (6 services)

- **financial-judgment-inquiry** → Possibly related to credit services
  - SMS Required: Yes

## API Integration Requirements

### 1. Token Management
All services require Client Credential tokens:
```
Authorization: Bearer {Token}
```

### 2. Common Parameters
- **trackId**: Optional 40-character string for tracking
- **clientId**: Required in all URLs
- **nationalID**: Most common parameter (10 digits)
- **mobile**: Required for many services (11 digits starting with 09)

### 3. SMS Verification Services
Services requiring SMS verification (only after login with sufficient balance):
- All credit-related services
- All KYC services
- All financial inquiry services
- All government services

### 4. Response Format
Standard response format:
```json
{
  "trackId": "unique-tracking-id",
  "result": {
    // Service-specific data
  },
  "status": "DONE|FAILED|PENDING",
  "error": {
    "code": "ERROR_CODE",
    "message": "Error description"
  }
}
```

## Implementation Priority

### Phase 1: Vehicle Services (High Priority)
1. car-violation-inquiry
2. negative-license-score
3. active-plates-list
4. traffic-vehicle-inquiry
5. toll-road-inquiry

### Phase 2: Credit & Banking Services (Medium Priority)
1. credit-score-rating
2. check-color
3. cheque-inquiry
4. loan-inquiry

### Phase 3: KYC & Government Services (Lower Priority)
1. liveness-inquiry
2. military-service-status
3. passport-status-inquiry
4. expats-inquiries

### Phase 4: Missing API Research (Research Required)
Services not found in provided Finnotech documentation need research or alternative APIs.

## Controller Implementation Structure

Each service controller should implement:

1. **Input Validation**
   - Specific validation rules for each parameter type
   - Persian error messages
   - Rate limiting

2. **API Integration**
   - Token management
   - HTTP client configuration
   - Error handling and retries

3. **SMS Verification** (when required)
   - Check user authentication
   - Check wallet balance
   - Send OTP via SMS
   - Verify OTP before API call

4. **Result Processing**
   - Parse API response
   - Format data for display
   - Handle hidden fields
   - Store in database

5. **Response Handling**
   - Success: Redirect to result page
   - Error: Return with error messages
   - Timeout: Graceful degradation

## Next Steps

1. Implement Phase 1 services first
2. Create base classes for common functionality
3. Set up API configuration management
4. Implement SMS verification system
5. Create dynamic result page templates
6. Test with sandbox environment first 