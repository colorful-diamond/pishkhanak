# Service ID 14 - Coming Cheque Inquiry Project Analysis

## Service Overview
**Service ID**: 14  
**Title**: استعلام وضعیت چک در راه (Coming Cheque Inquiry)  
**Slug**: `coming-cheque-inquiry`  
**Price**: 10,000 IRT  
**Status**: Active (is_active: true)  
**Parent Service**: None (parent_id: null)  
**Category**: Banking Services  

## Database Structure Analysis

### Services Table Schema
```sql
-- Key fields for Service ID 14
id: 14
title: "استعلام وضعیت چک در راه"
slug: "coming-cheque-inquiry"
parent_id: null
price: 10000
is_active: true
category_id: 1 (Banking Services)
```

### Service Relationships
- **Parent Service**: coming-cheque-inquiry (ID: 14) 
- **Child Services**: None found
- **Related Services**: 
  - cheque-inquiry (ID: 10) - Main cheque inquiry service with 25 bank variations
  - cheque-color (ID: 11) - Check color inquiry with 25 bank variations
  - iban-check (ID: 5) - IBAN validation service

## Controller Architecture

### ServiceControllerFactory Mapping
```php
// Controller mapping discrepancy identified:
'coming-check-inquiry' => ComingCheckInquiryController::class, // Factory mapping
// vs actual database slug: 'coming-cheque-inquiry'
```

### Controller Implementation
**File**: `/app/Http/Controllers/Services/ComingCheckInquiryController.php`
- Extends `BaseFinnotechController`
- API Endpoint: `'coming-check-inquiry'`
- Required Fields: `['mobile', 'national_code']`
- HTTP Method: `GET`
- SMS Required: `false`
- Comprehensive data processing with alerts and recommendations

## View Structure Analysis

### Current Implementation
**Directory**: `/resources/views/front/services/custom/coming-check-inquiry/`
- `upper.blade.php` - Form interface (8 lines)
  - Uses national-code-field and mobile-field partials
  - Submit text: "استعلام وضعیت چک در راه"

### Missing Content Components
Based on successful service patterns (e.g., account-iban service), the following files are needed:
- `content.blade.php` - Main enterprise content (12,000+ words)
- `comprehensive-faqs.blade.php` - Advanced FAQ system (50+ FAQs)
- `analytics-tracking.blade.php` - Comprehensive analytics
- `performance-assets.blade.php` - Performance optimization
- `persian-seo-optimization.blade.php` - SEO & structured data
- `partials/` directory with reusable components

## Reference Design Patterns

### Gold Standard: Cheque-Inquiry Service
**File**: `/resources/views/front/services/custom/cheque-inquiry/comprehensive-faqs.blade.php`
- **53 comprehensive FAQs** with real-time search
- **9 categories**: General, Inquiry, Sayad System, Colors, Returned, Costs, Technical, Security, Legal, Additional
- **Advanced UI**: Purple-blue gradient header, category filtering, animated interactions
- **Mobile-responsive design** with RTL support

### FAQ System Requirements
```blade
{{-- Standard header structure --}}
<div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8 mb-8">
    <h2 class="text-3xl font-bold text-dark-sky-700 mb-4">
        مرجع کامل سوالات متداول
    </h2>
    <p>بیش از <strong>۵۰ سوال و پاسخ تخصصی</strong> درباره [SERVICE_DESCRIPTION]</p>
</div>
```

## Service Categories & Banking Coverage

### Banking Services Overview
- **Total Services**: 365 services
- **Bank Coverage**: 25 major Iranian banks per service
- **Architecture**: Parent services with bank-specific child services

### Major Banks Supported
- بانک ملی (Melli Bank)
- بانک ملت (Mellat Bank) 
- بانک صادرات (Saderat Bank)
- بانک سپه (Sepah Bank)
- بانک پارسیان (Parsian Bank)
- بانک پاسارگاد (Pasargad Bank)
- بانک سامان (Saman Bank)
- [+18 more banks]

## Technical Implementation Notes

### API Integration
- **Provider**: Finnotech
- **Endpoint**: `coming-check-inquiry`
- **Authentication**: Required
- **Data Format**: JSON responses with comprehensive check status information

### Response Data Structure
```php
[
    'status' => 'success',
    'data' => [
        'national_code' => 'user_input',
        'issued_checks' => [...], // Processed check details
        'summary' => [...], // Aggregated statistics
        'account_info' => [...], // Account details
        'alerts' => [...], // Warning messages
        'recommendations' => [...], // Actionable suggestions
    ]
]
```

### Check Status Translations
```php
'PENDING' => 'در انتظار تصویب',
'PAID' => 'پرداخت شده',
'BOUNCED' => 'برگشت خورده',
'CANCELLED' => 'لغو شده',
'STOPPED' => 'توقف پرداخت',
'EXPIRED' => 'منقضی شده'
```

## Content Generation Requirements

### Enterprise Content Specifications
- **Word Count**: 6,000+ words minimum
- **Language**: Formal Persian business terminology
- **Keywords**: Natural integration of financial/banking keywords
- **Sections**: Hero, features, process, troubleshooting, compliance, business integration
- **Cultural Context**: Persian business culture awareness

### FAQ System Specifications  
- **Quantity**: 50+ FAQs minimum (following cheque-inquiry standard of 53)
- **Categories**: 8-9 distinct categories
- **Search**: Real-time filtering functionality
- **Design**: Exact visual match to cheque-inquiry reference
- **File Name**: `comprehensive-faqs.blade.php` (NOT faq-section.blade.php)

### SEO & Analytics Requirements
- **Meta Tags**: Comprehensive Persian SEO optimization
- **Structured Data**: JSON-LD schema markup
- **Analytics**: Google Analytics 4 integration
- **Performance**: Optimized asset loading
- **RTL Support**: Right-to-left text handling

## File Organization Standards

### Directory Structure
```
/resources/views/front/services/custom/coming-check-inquiry/
├── content.blade.php (NEEDED)
├── comprehensive-faqs.blade.php (NEEDED)
├── analytics-tracking.blade.php (NEEDED)
├── performance-assets.blade.php (NEEDED)
├── persian-seo-optimization.blade.php (NEEDED)
├── upper.blade.php (EXISTS)
└── partials/ (NEEDED)
    ├── form-validation.blade.php
    ├── results-display.blade.php
    ├── security-notice.blade.php
    ├── loading-spinner.blade.php
    └── help-tooltip.blade.php
```

## Integration Points

### Service Factory
- Update mapping from `coming-check-inquiry` to `coming-cheque-inquiry` for consistency
- Ensure proper controller resolution

### Database Relationships
- No child services currently defined
- Potential for bank-specific variations (following cheque-inquiry pattern)

### Payment Integration
- Price: 10,000 IRT
- Integration with Jibit, Sepehr, Finnotech gateways
- Guest and authenticated user workflows

## Quality Assurance Standards

### Content Validation
- [ ] 50+ FAQs with proper categorization
- [ ] 6,000+ words enterprise content
- [ ] Visual design match to cheque-inquiry reference
- [ ] Persian language cultural appropriateness
- [ ] SEO optimization completion
- [ ] Mobile responsiveness testing

### Technical Validation
- [ ] Laravel view resolution confirmed
- [ ] Controller mapping consistency
- [ ] Database service record accuracy
- [ ] API endpoint functionality
- [ ] Performance optimization implemented

## Development Workflow

### Content Generation Process
1. **Research Phase**: Web research for coming cheque inquiry patterns
2. **Content Creation**: Enterprise content with Persian financial expertise
3. **FAQ Development**: 50+ comprehensive FAQs using cheque-inquiry template
4. **SEO Implementation**: Meta tags, schema markup, analytics
5. **Performance Optimization**: Asset optimization, lazy loading
6. **Quality Validation**: Content review, technical testing

### Success Metrics
- **Content Quality**: 6,000+ words, culturally appropriate Persian content
- **FAQ Completeness**: 50+ FAQs with full search functionality  
- **Performance**: < 3s page load time
- **SEO Score**: > 85 lighthouse score
- **User Experience**: Mobile-first responsive design

This analysis provides complete context for Service ID 14 (coming-cheque-inquiry) content generation and system integration within the Pishkhanak financial services platform.