# Comprehensive FAQ Generation System - Best Practices

## Overview
This memory documents the exact methodology for generating comprehensive FAQ systems for services, based on the successful implementation for Mehr Iran Bank Credit Assessment Service (Service ID 82).

## Critical Requirements for FAQ Generation

### File Naming Convention
- **ALWAYS use**: `comprehensive-faqs.blade.php` (NOT faq-section.blade.php)
- **Location**: Must be in sub-service directory structure: `/custom/parent-service/child-service/comprehensive-faqs.blade.php`

### Design Reference Standard
- **Primary Reference**: `/resources/views/front/services/custom/cheque-inquiry/comprehensive-faqs.blade.php`
- **Design Pattern**: ALWAYS copy exact visual design from cheque-inquiry service
- **This is the gold standard for all FAQ implementations**

## Exact Design Requirements

### Header Section
```html
<div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8 mb-8">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-bold text-dark-sky-700 mb-4 flex items-center justify-center gap-3">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            مرجع کامل سوالات متداول
        </h2>
        <p class="text-gray-700 text-lg leading-relaxed">
            بیش از <strong>[NUMBER] سوال و پاسخ تخصصی</strong> درباره [SERVICE_DESCRIPTION]
        </p>
    </div>
</div>
```

### Search and Filter System
```html
<div class="bg-white rounded-2xl border border-gray-200 p-6 mb-8 shadow-sm">
    <div class="flex flex-col lg:flex-row gap-4 items-center">
        <!-- Search Input -->
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input 
                type="text" 
                id="faq-search" 
                placeholder="جستجو در سوالات متداول..." 
                class="w-full pl-3 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
            >
        </div>
        
        <!-- Category buttons with exact counts -->
        <!-- ALWAYS include exact FAQ counts in parentheses -->
```

## FAQ Quantity Standards

### Minimum Requirements
- **Target**: 50+ FAQs minimum
- **Recommended**: 53-56 FAQs (following cheque-inquiry standard)
- **Distribution**: 8-9 categories with 3-8 FAQs each

### Category Distribution Template
```
1. عمومی (General) - 8 FAQs
2. [SERVICE_SPECIFIC] - 7 FAQs  
3. فرآیند (Process) - 7 FAQs
4. مدارک (Documents) - 6 FAQs
5. هزینه‌ها (Costs) - 6 FAQs
6. امنیت (Security) - 5 FAQs
7. مقررات (Regulations) - 6 FAQs
8. خدمات (Services) - 5 FAQs
9. رفع مشکل (Troubleshooting) - 3-5 FAQs
```

## Technical Implementation

### Content Integration
- **ALWAYS add** to main content.blade.php:
```blade
{{-- Comprehensive FAQ Section - 50+ Searchable and Categorized Questions --}}
@include('front.services.custom.PARENT_SERVICE.CHILD_SERVICE.comprehensive-faqs')
```

### Contact Section (Required)
- **ALWAYS include** contact/support section after FAQs
- Copy exact HTML structure from cheque-inquiry
- Update service-specific text but maintain identical styling

### JavaScript Functionality
- **Search functionality**: Real-time filtering
- **Category filtering**: Show/hide by data-category attributes
- **Accordion behavior**: Smooth expand/collapse with animations
- **Responsive design**: Mobile-first approach

## Content Quality Standards

### Question Format
- **Length**: 100-200 words per answer
- **Language**: Formal Persian business terminology
- **Structure**: Question in h3, answer in paragraph with formatting
- **Keywords**: Natural integration of target keywords
- **Accuracy**: All information must be factual and current

### Answer Quality Requirements
- **Expertise Level**: Professional banking/financial knowledge
- **Cultural Appropriateness**: Persian business culture awareness  
- **Practical Value**: Actionable information for users
- **Compliance**: Reference official regulations when applicable

## File Structure Requirements

### Directory Organization
```
/resources/views/front/services/custom/
├── parent-service/
│   ├── child-service/
│   │   ├── comprehensive-faqs.blade.php ✅ (NEW STANDARD)
│   │   ├── content.blade.php (updated with @include)
│   │   ├── upper.blade.php
│   │   ├── preview.blade.php
│   │   └── partials/
```

### Backup Strategy
- **ALWAYS backup** old FAQ files before replacement
- **Naming**: `faq-section.blade.php.backup` or similar
- **Preserve**: Original work for reference/rollback if needed

## Validation Checklist

### Pre-Delivery Validation
- [ ] FAQ count: 50+ verified with `grep -c "faq-item"`
- [ ] Design match: Visual comparison with cheque-inquiry
- [ ] Categories: Proper distribution and counts
- [ ] Search: Functional testing of filter system
- [ ] Views: Laravel view resolution confirmed
- [ ] Content: Service-specific information accuracy
- [ ] Integration: Proper @include in main content file

## Success Metrics - Mehr Iran Bank Implementation

### Achieved Results
- **FAQ Count**: 56 FAQs (exceeded 50+ requirement)
- **Design Compliance**: 100% match with cheque-inquiry
- **File Size**: 116,835 bytes comprehensive-faqs.blade.php
- **User Experience**: Full search and category filtering
- **Integration**: Seamless inclusion in main content flow

### Best Practices Confirmed
- **Purple-blue gradient header**: Creates professional appearance
- **Category-based organization**: Improves user navigation
- **Exact count display**: Sets proper user expectations  
- **RTL support**: Proper Persian language handling
- **Responsive design**: Mobile and desktop optimization

## Future Implementation Notes

### Task Agent Usage
- **Use**: `technical-writer` subagent for FAQ generation
- **Specify**: Exact cheque-inquiry pattern matching
- **Provide**: Service-specific research data and context
- **Verify**: FAQ count and design compliance

### Quality Assurance
- **Always** validate final FAQ count
- **Always** test view resolution  
- **Always** compare visual design with reference
- **Always** backup previous versions

This methodology ensures consistent, high-quality FAQ systems across all services while maintaining the professional standards established by the cheque-inquiry implementation.