# Military Service Status Service - Project Analysis

## Service Details (ID: 36)
- **Title**: استعلام وضعیت نظام وظیفه (Military Service Status Inquiry)
- **Slug**: military-service-status  
- **Category**: شناسایی و احراز هویت (KYC/Identity Verification) - ID: 12
- **Parent**: null (main service, not sub-service)
- **Status**: active
- **Directory**: `/resources/views/front/services/custom/military-service-status/`

## Existing Structure
- ✅ Directory exists: `military-service-status/`
- ✅ Has `upper.blade.php` (form section)
- ❌ Missing `content.blade.php` (main content)
- ❌ Missing `faqs.blade.php` (comprehensive FAQ system)

## Reference Design Pattern (from cheque-inquiry)
### FAQ System Features:
- **Multi-category system** with color-coded buttons
- **Real-time search** with Persian text support  
- **Results counter** and category filtering
- **Accordion-style expandable** FAQ items
- **Advanced visual design**: Purple/blue gradients, white content areas
- **Structure**: 50+ FAQs across 8+ categories

### File Patterns in Other Services:
- `content.blade.php` - Main comprehensive content
- `comprehensive-faqs.blade.php` or `faqs.blade.php` - FAQ system
- `upper.blade.php` - Form and upper section (already exists)
- Some services have `partials/` directories for reusable components

## Implementation Requirements:
1. **Content File**: Create `content.blade.php` with 10,000+ words of military service content
2. **FAQ File**: Create `faqs.blade.php` with 60+ FAQs following the established design pattern
3. **No modifications** to existing `upper.blade.php` file
4. **Follow exact visual design** from reference services like cheque-inquiry

## Content Focus Areas:
- Military service status inquiry procedures
- SAKHA system usage (sakha.epolice.ir)
- Different types of military service status
- Required documents and procedures
- Common issues and troubleshooting
- Legal framework and regulations