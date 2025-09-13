# Template Consistency System - Comprehensive Implementation

## Overview
The Pishkhanak platform uses a standardized template system based on credit-score-rating service as the master template. All services must follow identical structural and visual patterns to ensure brand consistency.

## Master Template Reference
**File**: `/home/pishkhanak/htdocs/pishkhanak.com/resources/views/front/services/custom/credit-score-rating/content.blade.php`

## Template Standards

### Required Structure Elements
1. **Table of Contents Section**:
   ```blade
   <section class="bg-white rounded-2xl border border-gray-200 p-6 mb-8 mt-8">
   ```

2. **Hero Section with Standard Gradient**:
   ```blade
   <section class="bg-gradient-to-l from-sky-50 via-blue-50 to-indigo-50 rounded-3xl p-8 relative overflow-hidden mt-12 mb-12">
   ```

3. **Standardized Section Headers**:
   ```blade
   <h2 id="section-id" class="text-2xl font-bold text-dark-sky-700 mb-6">
   ```

4. **FAQ System Integration**:
   ```blade
   @include('front.services.custom.SERVICE-NAME.faq-section')
   ```

### Template Consistency Validation Checklist

#### ✅ Completed Services
- **credit-score-rating**: Master template (reference standard)
- **loan-inquiry**: Template consistency applied (September 2025)
  - ✅ Content structure recreated
  - ✅ FAQ system created with 25+ FAQs
  - ✅ Standard gradient and header structure
  - ✅ Table of contents with 15+ navigation links

#### 🔄 Implementation Process
1. **Analysis Phase**: Compare current service with master template
2. **Identification Phase**: Document gaps and inconsistencies
3. **Recreation Phase**: Rewrite content.blade.php using template structure
4. **FAQ Creation**: Create faq-section.blade.php with interactive features
5. **Integration Phase**: Add FAQ include to main content file
6. **Validation Phase**: Ensure perfect structural alignment

## Critical Design Patterns

### Color Schemes
- **Standard Gradient**: `bg-gradient-to-l from-sky-50 via-blue-50 to-indigo-50`
- **Text Colors**: `text-dark-sky-700` for headers
- **Avoid**: `bg-gradient-to-br from-slate-50` or `text-slate-800`

### Typography Hierarchy
- **Main Headers**: `text-2xl font-bold text-dark-sky-700 mb-6`
- **Section IDs**: All sections must have proper `id` attributes
- **Navigation**: Table of contents with proper anchor links

### Component Structure
- **Stats Grid**: 4-column layout with service-specific statistics
- **Process Steps**: Gradient flow visualization
- **Related Services**: Grid layout with service cards
- **FAQ System**: Interactive search and category filtering

## FAQ System Standards

### Required Features
- **Search Functionality**: Real-time search across all FAQs
- **Category Filtering**: Multiple categories with button filters
- **Interactive Design**: Collapsible FAQ items with smooth animations
- **Persian RTL Support**: Proper right-to-left layout
- **Responsive Design**: Mobile-first approach

### FAQ File Structure
```
service-name/
├── content.blade.php (main content with @include)
├── faq-section.blade.php (interactive FAQ system)
├── comprehensive-faqs.blade.php (if exists)
├── preview.blade.php
├── upper.blade.php
└── partials/
```

## Service-Specific Adaptations

### loan-inquiry Specific Elements
- **Stats**: 16 نوع وام, 25 بانک عضو, CBI بانک مرکزی, 24/7 خدمات پیشخوانک
- **Content Focus**: Loan and facility inquiry, 16 types of loans, 25 banks
- **FAQ Categories**: basic, process, loans, banks, technical
- **Bank Directory**: Complete list of 25 participating banks

## Quality Assurance Process

### Validation Steps
1. **Structural Check**: Verify all required sections exist
2. **Visual Check**: Ensure gradient and color consistency
3. **Navigation Check**: Test all anchor links and table of contents
4. **FAQ Check**: Verify interactive functionality works
5. **Mobile Check**: Test responsive design across devices

### Tools for Validation
- **Grep Patterns**: Search for standard header patterns
- **File Comparison**: Compare with master template structure
- **Visual Review**: Check gradient and color schemes
- **Functional Testing**: Test FAQ search and filtering

## Memory for Future Implementations

### Next Services to Update
Priority list of services needing template consistency:
1. card-iban (may already be updated)
2. cheque-inquiry
3. third-party-insurance-history
4. Other custom services in the platform

### Automation Opportunities
- Create command for automatic template consistency checking
- Develop validation scripts for new service creation
- Implement automated FAQ generation based on service type

## Implementation Notes
- Always read master template first before making changes
- Use parallel tool operations for efficiency
- Document all changes in project memory
- Test all interactive elements after implementation
- Ensure Persian language and RTL layout correctness

This system ensures all Pishkhanak services maintain consistent professional appearance and user experience while adapting content to specific service requirements.