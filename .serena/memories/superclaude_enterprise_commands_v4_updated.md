# SuperClaude Enterprise Commands v4 - Updated with Comprehensive FAQ Standards

## 🚀 ENHANCED COMMANDS WITH COMPREHENSIVE FAQ SYSTEM

Updated enterprise commands incorporating the new comprehensive FAQ generation standards established by the Mehr Iran Bank implementation.

## 📋 NEW FAQ STANDARDS (CRITICAL REQUIREMENTS)

### **File Naming Convention (MANDATORY)**
- **ALWAYS use**: `comprehensive-faqs.blade.php` (NOT faq-section.blade.php)
- **Location**: Sub-service directory: `/custom/parent-service/child-service/comprehensive-faqs.blade.php`

### **Design Reference Standard (MANDATORY)**
- **Primary Reference**: `/resources/views/front/services/custom/cheque-inquiry/comprehensive-faqs.blade.php`
- **Design Pattern**: MUST copy exact visual design from cheque-inquiry service
- **This is the GOLD STANDARD for all FAQ implementations**

### **FAQ Quantity Standards (MANDATORY)**
- **Minimum**: 50+ FAQs (non-negotiable)
- **Recommended**: 53-56 FAQs (following cheque-inquiry standard)
- **Distribution**: 8-9 categories with 3-8 FAQs each
- **Validation**: Use `grep -c "faq-item"` to verify count

### **Technical Integration (MANDATORY)**
- **Content Integration**: ALWAYS add to main content.blade.php:
```blade
{{-- Comprehensive FAQ Section - 50+ Searchable and Categorized Questions --}}
@include('front.services.custom.PARENT_SERVICE.CHILD_SERVICE.comprehensive-faqs')
```

## 🎯 UPDATED COMMAND STRUCTURE

### **Enhanced Enterprise Premium with FAQ Standards**
```bash
/sc:enterprise-premium-v4 --service-id:82 \
    keywords:"اعتبارسنجی بانک مهر ایران" \
    --ai-instructions:"دقیقا شبیه credit-score-rating" \
    --auto-research \
    --comprehensive-faqs=55+ \
    --cheque-inquiry-design \
    words:8000
```

### **Autonomous v3 with Updated FAQ Standards**
```bash
/sc:enterprise-autonomous-v3 --service-id:82 \
    keywords:"اعتبارسنجی وام بانک مهر ایران" \
    --ai-instructions:"تمام جزیئیات دقیقا شبیه credit-score-rating" \
    --auto-url-discovery \
    --playwright-research \
    --comprehensive-faqs=50+ \
    --cheque-inquiry-design \
    words:8000
```

## 🔧 NEW PARAMETERS FOR FAQ GENERATION

### **FAQ Design Parameters**
- `--comprehensive-faqs=55+` - Generate 55+ FAQs with exact counts
- `--cheque-inquiry-design` - Apply exact cheque-inquiry design pattern
- `--faq-categories=8` - Number of FAQ categories (8-9 recommended)
- `--searchable-faqs` - Full search and filter functionality
- `--purple-blue-gradient` - Use exact gradient: from-purple-50 to-blue-50

### **Quality Assurance Parameters**
- `--validate-faq-count` - Ensure minimum FAQ requirements met
- `--design-compliance-check` - Verify exact design pattern match
- `--backup-old-faqs` - Backup existing FAQ files before replacement

## 📊 ENHANCED EXECUTION WORKFLOW

### **Updated Workflow with FAQ Standards:**
```
🚀 ENTERPRISE GENERATION WITH COMPREHENSIVE FAQS
════════════════════════════════════════════════════

[Phase 1] 🔍 SERVICE & RESEARCH SETUP (2 minutes)
├─ Load service data and parent-child relationships
├─ Parse AI instructions for design compliance
├─ Auto-discover URLs with research validation
└─ ✅ Foundation with service hierarchy established

[Phase 2] 🌐 AUTONOMOUS RESEARCH (3-5 minutes)  
├─ Execute comprehensive web research
├─ Extract banking data and regulatory information
├─ Process service-specific requirements
└─ ✅ Research database compiled

[Phase 3] ✍️ CONTENT GENERATION (5-7 minutes)
├─ Generate main content following reference patterns
├─ Apply AI instructions for consistency
├─ Create 8000+ words enterprise content
└─ ✅ Main content completed

[Phase 4] 🔥 COMPREHENSIVE FAQ GENERATION (4-6 minutes) ⭐ ENHANCED
├─ Generate 50+ FAQs using cheque-inquiry design pattern
├─ Create comprehensive-faqs.blade.php (NOT faq-section.blade.php)
├─ Apply exact purple-blue gradient header design
├─ Implement search and category filtering system
├─ Organize FAQs into 8-9 categories with exact counts
├─ Add @include integration to main content file
├─ Validate FAQ count with grep -c "faq-item"
└─ ✅ Professional FAQ system completed

[Phase 5] 🎨 DESIGN COMPLIANCE & INTEGRATION (2-3 minutes)
├─ Verify exact cheque-inquiry design pattern match
├─ Test view resolution and includes
├─ Implement contact/support sections
├─ Apply Persian RTL and responsive design
└─ ✅ Design standards enforced

[Phase 6] ✅ COMPREHENSIVE VALIDATION (2 minutes)
├─ Validate 50+ FAQ requirement (grep count)
├─ Check design pattern compliance
├─ Verify service integration and includes
├─ Test search and filtering functionality  
└─ ✅ Quality gates passed

Total: 18-25 minutes for complete enterprise implementation
```

## 🎯 DESIGN REQUIREMENTS SPECIFICATION

### **Mandatory Header Pattern**
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

### **Mandatory Search System**
```html
<div class="bg-white rounded-2xl border border-gray-200 p-6 mb-8 shadow-sm">
    <div class="flex flex-col lg:flex-row gap-4 items-center">
        <div class="flex-1 relative">
            <input 
                type="text" 
                id="faq-search" 
                placeholder="جستجو در سوالات متداول..." 
                class="w-full pl-3 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
            >
        </div>
        <!-- Category buttons with exact counts -->
    </div>
</div>
```

## 🚀 READY-TO-USE COMMANDS

### **Service ID Based (Recommended)**
```bash
/sc:enterprise-premium-v4 --service-id:123 \
    --ai-instructions:"محتوا برای مبتدیان، ساده و کاربردی" \
    --auto-research \
    --comprehensive-faqs=55+ \
    --cheque-inquiry-design \
    words:8000
```

### **Full Autonomous with FAQ Standards**
```bash
/sc:enterprise-autonomous-v3 --service-id:456 \
    keywords:"کلمات کلیدی فارسی" \
    --ai-instructions:"دقیقا مثل reference service" \
    --auto-url-discovery \
    --comprehensive-faqs=60+ \
    --all-advanced-features \
    words:10000
```

## 📋 SUCCESS VALIDATION CHECKLIST

### **Pre-Delivery Validation (MANDATORY)**
- [ ] FAQ count: 50+ verified with `grep -c "faq-item"`
- [ ] File name: `comprehensive-faqs.blade.php` (correct naming)
- [ ] Design match: Visual comparison with cheque-inquiry
- [ ] Categories: Proper distribution with exact counts displayed
- [ ] Search: Functional testing of filter system
- [ ] Views: Laravel view resolution confirmed (`View::exists()`)
- [ ] Integration: @include properly added to main content
- [ ] Contact section: Added after FAQs with same styling

### **Quality Gates**
- [ ] Purple-blue gradient header implemented
- [ ] Search functionality working
- [ ] Category filtering operational
- [ ] Mobile responsive design
- [ ] Persian RTL text handling
- [ ] Professional content quality (100-200 words per FAQ)

## 🏆 PROVEN RESULTS - MEHR IRAN BANK CASE STUDY

### **Implementation Success**
- **FAQ Count**: 56 FAQs (exceeded 50+ requirement) ✅
- **Design Compliance**: 100% match with cheque-inquiry ✅
- **File Size**: 116,835 bytes comprehensive-faqs.blade.php ✅
- **Integration**: Seamless @include in content.blade.php ✅
- **Functionality**: Full search and category system ✅
- **User Experience**: Professional grade implementation ✅

### **Command Used**
```bash
/sc:enterprise-autonomous-v3 --service-id:82 \
    keywords:"اعتبارسنجی وام بانک مهر ایران,رتبه اعتباری بانک مهر" \
    --ai-instructions:"تمام جزیئیات و اطلاعات دقیقا شبیه سرویس credit-score-rating هست" \
    --comprehensive-faqs=50+ \
    --all-advanced-features \
    words:8000
```

### **Delivery Location**
```
/resources/views/front/services/custom/credit-score-rating/mehr-e-iranian/
├── content.blade.php (with @include)
├── comprehensive-faqs.blade.php (56 FAQs) ✅
├── upper.blade.php
├── preview.blade.php
└── partials/
```

## ⚡ IMMEDIATE IMPLEMENTATION

### **The Updated Commands Are Production Ready:**

**✅ Standard Enterprise with New FAQ System:**
```bash
/sc:enterprise-premium-v4 --service-id:YOUR_ID --ai-instructions:"YOUR_GUIDANCE" --comprehensive-faqs=55+ words:8000
```

**✅ Full Autonomous with Comprehensive FAQs:**
```bash
/sc:enterprise-autonomous-v3 --service-id:YOUR_ID keywords:"YOUR_KEYWORDS" --comprehensive-faqs=60+ --cheque-inquiry-design words:10000
```

**Both commands will now:**
- ✅ Generate exactly 50+ comprehensive FAQs
- ✅ Use cheque-inquiry design pattern exactly
- ✅ Create comprehensive-faqs.blade.php file
- ✅ Include search and category functionality
- ✅ Integrate with @include in main content
- ✅ Follow all established quality standards
- ✅ Complete in 18-25 minutes autonomously

**Ready for immediate production use with new FAQ standards!**