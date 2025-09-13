# Pishkhanak Brand Consistency Analysis

## Service Structure Analysis
- **Service ID 15**: Corresponds to SHEBA/IBAN conversion services
- **Target Directory**: `resources/views/front/services/custom/account-iban/` (to be created)
- **Keywords Focus**: تبدیل شماره حساب به شبا (Account Number to SHEBA conversion)

## Brand Design Patterns

### FAQ System Pattern (from cheque-inquiry)
```html
<!-- Core Structure -->
<section class="mt-12 mb-12" id="comprehensive-faqs">
    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8 mb-8">
        <!-- Header with icon and title -->
    </div>
    
    <!-- Search and Filter System -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-8 shadow-sm">
        <!-- Search input with right-aligned Persian text -->
        <!-- Category filter buttons -->
    </div>
    
    <!-- FAQ Items -->
    <div class="faq-item p-6" data-category="[category]" data-keywords="[keywords]">
        <button class="faq-question w-full text-right flex items-start justify-between group">
            <!-- Persian question text -->
        </button>
        <div class="faq-answer hidden mt-4">
            <!-- Persian answer with proper styling -->
        </div>
    </div>
</section>
```

### Content Structure Pattern (from credit-score-rating)
```html
<!-- Table of Contents -->
<section class="bg-white rounded-2xl border border-gray-200 p-6 mb-8 mt-8">
    <h2 class="text-xl font-bold text-dark-sky-700 mb-4 flex items-center gap-2">
        <!-- Icon + Title -->
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <!-- Quick links to sections -->
    </div>
</section>

<!-- Hero Section -->
<section class="bg-gradient-to-l from-sky-50 via-blue-50 to-indigo-50 rounded-3xl p-8 relative overflow-hidden mt-12 mb-12">
    <!-- Background pattern and content -->
</section>
```

## Color Scheme Standards
- **Primary Gradients**: sky-50, blue-50, indigo-50, purple-50
- **Text Colors**: dark-sky-700, gray-700, gray-800
- **Interactive**: blue-600, sky-600, hover transitions
- **Border Radius**: rounded-2xl, rounded-3xl consistently used

## Persian Language Standards
- **Direction**: RTL text handling
- **Typography**: Font-semibold for headings, leading-relaxed for content
- **Cultural Context**: Financial services terminology in Persian
- **Formatting**: Persian numbers and proper spacing

## Technical Requirements
- **FAQ Count**: 60+ minimum for comprehensive coverage
- **Search System**: Real-time search with category filtering
- **Responsive**: Grid layouts that adapt to screen sizes
- **Accessibility**: Proper contrast ratios and semantic HTML

## Service Patterns Identified
- Existing SHEBA services: account-iban, card-iban, iban-account, sheba-inquiry
- Consistent upper.blade.php form structures
- Preview.blade.php for results display
- Comprehensive FAQ systems for all major services

## Next Phase Requirements
- Strict keyword-only research (no semantic expansion)
- 12,000+ word content generation
- Advanced FAQ system with 60+ questions
- Persian cultural and financial compliance