# 📋 Complete Service Implementation TODO List

> **Design Requirements**: Follow existing style - clean, card-based, no gradients, consistent with postal-code and IBAN result pages

## 🏦 **1. BANKING & FINANCIAL SERVICES**

### ✅ **Already Complete**
- ✅ CardIbanController (Card to IBAN conversion)
- ✅ CardAccountController (Card to Account conversion) 
- ✅ IbanAccountController (IBAN to Account conversion)
- ✅ IbanValidatorController (IBAN validation)
- ✅ PostalCodeInquiryController (Postal code lookup)

### 🔄 **Needs Enhancement**

#### **IbanCheckController** 
- **API Endpoint**: `/facility/v2/clients/{clientId}/ibanInquiry`
- **Fields Required**: `iban`
- **TODO**:
  - [ ] Create custom form view: `resources/views/front/services/custom/iban-check/upper.blade.php`
  - [ ] Create result page: `resources/views/front/services/custom/iban-check/result.blade.php`
  - [ ] Update `formatResponseData()` to handle:
    - `bank_name` - نام بانک
    - `bank_code` - کد بانک  
    - `account_number` - شماره حساب
    - `account_type` - نوع حساب
    - `is_valid` - وضعیت اعتبار
    - `owner_name` - نام صاحب حساب (if available)
  - [ ] Style result cards with bank logo integration
  - [ ] Add copy-to-clipboard for all fields
  - [ ] Implement proper error handling for invalid IBANs

---

## 🚗 **2. VEHICLE & TRANSPORTATION SERVICES**

### **CarViolationInquiryController** ⚠️ **HIGH PRIORITY**
- **API Endpoint**: `/billing/v2/clients/{clientId}/drivingOffense` 
- **Fields Required**: `mobile`, `national_code`, `plate_number`
- **API Response Structure**:
```json
{
  "Bills": [{
    "id": "string",
    "type": "نوع تخلف", 
    "description": "شرح",
    "code": "کد تخلف",
    "price": 600000,
    "city": "شهر",
    "location": "محل تخلف",
    "date": "تاریخ شمسی",
    "serial": "پلاک کدشده",
    "license": "شماره پلاک",
    "billId": 12345678,
    "paymentId": 123456789,
    "normalizedDate": "تاریخ میلادی",
    "isPayable": true,
    "hasImage": false
  }],
  "TotalAmount": 600000
}
```
- **TODO**:
  - [ ] Create plate number input with proper formatting (9-digit converter)
  - [ ] Design violation list cards with:
    - [ ] Violation type badge with color coding
    - [ ] Amount with prominent display
    - [ ] Location with map icon
    - [ ] Date with calendar icon
    - [ ] Payment status indicator
    - [ ] Photo availability badge
  - [ ] Add total violations summary card
  - [ ] Implement payment ID copy functionality
  - [ ] Add filter/search within violations
  - [ ] Create print-friendly violation report

### **MotorViolationInquiryController** 
- **API Endpoint**: `/billing/v2/clients/{clientId}/ridingOffense`
- **Fields Required**: `mobile`, `national_code`, `plate_number` (8-digit)
- **TODO**:
  - [ ] Similar to car violations but for motorcycles
  - [ ] Handle 8-digit plate format
  - [ ] Design motorcycle-specific violation cards

### **ActivePlatesListController** ⭐ **USER FAVORITE**
- **API Endpoint**: `/vehicle/v2/clients/{clientId}/activePlateNumbers`
- **Fields Required**: `mobile`, `national_code`
- **API Response Structure**:
```json
{
  "plateNumbers": [{
    "nationalId": "کد ملی",
    "plateNumber": "ایران ۹۹ - ۲۸۳ ن ۸۰", 
    "revoked": false,
    "revokedDateTime": "تاریخ فک",
    "revokedDescription": "توضیحات فک",
    "serialNumber": "شماره سریال"
  }]
}
```
- **TODO**:
  - [ ] Design beautiful plate cards with:
    - [ ] Visual plate representation (Iran flag colors)
    - [ ] Active/Revoked status with proper colors
    - [ ] Vehicle type detection and icons
    - [ ] Serial number display
  - [ ] Add statistics summary:
    - [ ] Total plates count
    - [ ] Active vs revoked breakdown
    - [ ] Vehicle type distribution
  - [ ] Implement plate search/filter
  - [ ] Add export to PDF functionality

### **VehicleOwnershipInquiryController**
- **API Endpoint**: `/vehicle/v2/clients/{clientId}/matchingVehicleOwnership`
- **Fields Required**: `national_code`, `plate_number`
- **API Response Structure**:
```json
{
  "isValid": true,
  "vehicleType": "سایپا",
  "vehicleTip": "SAINA S MT", 
  "vehicleModel": "1401"
}
```
- **TODO**:
  - [ ] Create ownership verification result card
  - [ ] Display vehicle details with proper styling
  - [ ] Add verification status badge
  - [ ] Include vehicle image if available

### **PlateHistoryInquiryController** 
- **API Endpoint**: `/vehicle/v2/clients/{clientId}/plateNumberHistory`
- **TODO**:
  - [ ] Design timeline view for plate history
  - [ ] Show vehicle changes over time
  - [ ] Add interactive timeline with dates

### **TrafficVehicleInquiryController**
- **API Endpoint**: `/vehicle/v2/clients/{clientId}/trafficTollInquiry` 
- **TODO**:
  - [ ] Design toll charges list
  - [ ] Show payment status for each toll
  - [ ] Add total amount summary

### **TollRoadInquiryController**
- **API Endpoint**: `/ecity/v2/clients/{clientId}/freewayTollInquiry`
- **TODO**:
  - [ ] Create freeway toll history
  - [ ] Show toll locations and amounts
  - [ ] Add payment tracking

### **ThirdPartyInsuranceHistoryController**
- **API Endpoint**: `/kyc/v2/clients/{clientId}/thirdPartyInsuranceInquiry`
- **TODO**:
  - [ ] Display insurance history
  - [ ] Show coverage periods
  - [ ] Add insurance company details

### **TrafficViolationImageController**
- **API Endpoint**: `/billing/v2/clients/{clientId}/trafficOffenseImage`
- **Fields Required**: `offense_id`
- **API Response Structure**:
```json
{
  "OffenseId": "47691362",
  "PlateImageUrl": "https://...",
  "VehicleImageUrl": "https://..."
}
```
- **TODO**:
  - [ ] Create image gallery view
  - [ ] Implement image lightbox
  - [ ] Add image download functionality
  - [ ] Show both plate and vehicle images

### **DrivingLicenseStatusController** 
- **API Endpoint**: `/kyc/v2/clients/{clientId}/licenseCheckInquiry`
- **TODO**:
  - [ ] Display license validity status
  - [ ] Show license type and categories
  - [ ] Add renewal information

### **NegativeLicenseScoreController**
- **API Endpoint**: `/billing/v2/clients/{clientId}/negativeScore` 
- **Fields Required**: `license_number`, `national_code`, `mobile`
- **API Response Structure**:
```json
{
  "LicenseNumber": "9607173353",
  "NegativeScore": "0", 
  "OffenseCount": null,
  "Rule": "-"
}
```
- **TODO**:
  - [ ] Create score display with visual indicator
  - [ ] Show offense count and penalties
  - [ ] Add score interpretation guide

---

## 💳 **3. CREDIT & LOAN SERVICES** (SMS Required)

### **LoanInquiryController** ✅ **FIXED**
- **Status**: Field naming fixed, needs result page enhancement
- **TODO**:
  - [ ] Design comprehensive loan facility results:
    - [ ] Total facility amount with large display
    - [ ] Debt breakdown by categories  
    - [ ] Payment status indicators
    - [ ] Facility history timeline
    - [ ] Bank-wise facility distribution

### **LoanGuaranteeInquiryController**
- **API Endpoint**: `/credit/v2/clients/{clientId}/users/{user}/sms/guarantyInquiry`
- **TODO**:
  - [ ] Design guarantee relationship display
  - [ ] Show borrower information cards
  - [ ] Add guarantee amounts and statuses
  - [ ] Create guarantee risk assessment

### **CheckColorInquiryController** 
- **API Endpoint**: `/credit/v2/clients/{clientId}/chequeColorInquiry`
- **TODO**:
  - [ ] Create check status with color indicators
  - [ ] Show check history and patterns
  - [ ] Add risk level visualization

### **ComingChequeInquiryController**
- **TODO**:
  - [ ] Design upcoming checks timeline
  - [ ] Show payment due dates
  - [ ] Add reminder functionality

### **CreditScoreInquiryController**
- **API Endpoint**: `/credit/v2/clients/{clientId}/macnaInquiry`
- **TODO**:
  - [ ] Design credit score dashboard
  - [ ] Add score interpretation
  - [ ] Show improvement suggestions

### **InquiryMaknaCodeController**
- **TODO**:
  - [ ] Display MAKNA code information
  - [ ] Show credit status details
  - [ ] Add related services suggestions

---

## 🆔 **4. KYC & IDENTITY SERVICES**

### **MilitaryServiceStatusController**
- **API Endpoint**: Military service inquiry
- **TODO**:
  - [ ] Show service status with appropriate badges
  - [ ] Display exemption information if applicable
  - [ ] Add service completion details

### **ExpatsInquiriesController** 
- **API Endpoint**: Foreign nationals inquiry
- **TODO**:
  - [ ] Display residency information
  - [ ] Show visa and permit details
  - [ ] Add document status

### **LifeStatusInquiryController** 
- **TODO**:
  - [ ] Display life status verification
  - [ ] Show official confirmation
  - [ ] Add related information

### **DriverRiskInquiryController**
- **TODO**:
  - [ ] Create risk assessment dashboard
  - [ ] Show driving history analysis
  - [ ] Add safety recommendations

---

## 🎨 **5. DESIGN SYSTEM REQUIREMENTS**

### **Universal Result Page Components**
- [ ] **Header Section**: Service title, success badge, date/time, price
- [ ] **Input Summary**: Yellow-highlighted input data with copy functionality
- [ ] **Main Results**: Clean cards with proper field labels and values
- [ ] **Action Buttons**: Copy all, print, share (Telegram, link)
- [ ] **Footer**: Pishkhanak branding and timestamp

### **Color System** (NO GRADIENTS)
- [ ] **Primary**: Blue tones for headers and main actions
- [ ] **Success**: Green for successful operations and positive statuses
- [ ] **Warning**: Yellow/Orange for attention items  
- [ ] **Error**: Red for problems and negative statuses
- [ ] **Gray**: Neutral backgrounds and borders
- [ ] **Status Colors**: 
  - Green: Active, Valid, Paid
  - Red: Inactive, Invalid, Unpaid
  - Yellow: Pending, Warning
  - Blue: Information, Details

### **Card Designs**
- [ ] **Summary Cards**: Key metrics with large numbers
- [ ] **Detail Cards**: Structured information with labels
- [ ] **List Cards**: Multiple items with consistent formatting
- [ ] **Status Cards**: Clear indicators with appropriate colors
- [ ] **Action Cards**: Interactive elements with hover effects

### **Typography System**
- [ ] **Headers**: Bold, clear hierarchy (text-xl, text-2xl)
- [ ] **Labels**: Medium weight, consistent spacing
- [ ] **Values**: Mono font for IDs/numbers, proper formatting
- [ ] **Status Text**: Appropriate color coding

### **Responsive Design**
- [ ] **Mobile**: Single column, touch-friendly buttons
- [ ] **Tablet**: Optimized card layouts
- [ ] **Desktop**: Multi-column grids where appropriate
- [ ] **Print**: Clean, business-appropriate formatting

---

## 🔧 **6. TECHNICAL REQUIREMENTS**

### **Form Validation**
- [ ] Real-time validation for all input fields
- [ ] Proper Persian/English number handling
- [ ] IBAN/Card number formatting
- [ ] Plate number format validation
- [ ] National code validation

### **API Integration**
- [ ] Comprehensive error handling for all APIs
- [ ] Proper timeout handling (20 seconds as specified)
- [ ] Rate limiting and retry logic
- [ ] Response caching where appropriate
- [ ] Logging for debugging and monitoring

### **Performance**
- [ ] Image optimization for service thumbnails
- [ ] Lazy loading for result cards
- [ ] Efficient data processing
- [ ] Minimal API calls

### **Security**
- [ ] Input sanitization
- [ ] CSRF protection
- [ ] Secure session handling
- [ ] API key protection

---

## 📊 **7. PRIORITY LEVELS**

### **🔥 HIGH PRIORITY** (User Favorites)
1. **CarViolationInquiryController** - Most used service
2. **ActivePlatesListController** - High user engagement  
3. **LoanInquiryController** - Critical financial service
4. **MotorViolationInquiryController** - Popular service

### **⚡ MEDIUM PRIORITY**
5. **VehicleOwnershipInquiryController**
6. **CheckColorInquiryController** 
7. **CreditScoreInquiryController**
8. **PlateHistoryInquiryController**

### **📋 LOW PRIORITY**  
9. **Remaining KYC services**
10. **Specialized inquiry services**
11. **Image-based services**

---

## ✅ **8. COMPLETION CRITERIA**

For each service to be considered "complete":

- [ ] **Form**: Custom upper.blade.php with proper validation
- [ ] **API**: Full integration with error handling  
- [ ] **Result Page**: Beautiful, responsive result display
- [ ] **Field Labels**: Persian translations for all fields
- [ ] **Copy Functionality**: All important data copyable
- [ ] **Print Support**: Clean print layout
- [ ] **Mobile Responsive**: Works perfectly on all devices
- [ ] **Error Handling**: Graceful handling of all error cases
- [ ] **Loading States**: Proper loading indicators
- [ ] **Success Feedback**: Clear success confirmations

---

## 🚀 **9. IMPLEMENTATION APPROACH**

### **Phase 1: High Priority Services** (Week 1-2)
Focus on car violations, active plates, and loan inquiry

### **Phase 2: Vehicle Services** (Week 3-4)  
Complete remaining vehicle and transportation services

### **Phase 3: Credit Services** (Week 5)
Implement all credit and loan related services

### **Phase 4: KYC Services** (Week 6)
Complete identity and verification services

### **Phase 5: Polish & Testing** (Week 7)
Final testing, bug fixes, and performance optimization

---

**Total Services to Complete: 25+ services**
**Estimated Timeline: 6-7 weeks for full completion**
**Design Standard: Clean, professional, no gradients, consistent with existing postal-code and IBAN pages** 