# Enterprise Content Generation Patterns - Complete Guide
Based on credit-score-rating reference implementation

## 🚨 CRITICAL: CHUNK LOADING REQUIREMENT

### MUST READ FILES IN CHUNKS (500 lines at a time)
```bash
# NEVER try to read entire file at once
# ALWAYS use chunk loading:
Read(file_path, limit=500, offset=0)    # Lines 1-500
Read(file_path, limit=500, offset=500)  # Lines 501-1000
Read(file_path, limit=500, offset=1000) # Lines 1001-1500
# Continue until complete...
```

## 🎯 CRITICAL REQUIREMENTS CHECKLIST

### ✅ Must-Have Components
1. **Table of Contents (فهرست مطالب)** - MANDATORY for ALL content
2. **Internal Linking** - Minimum 50 internal links throughout content
3. **Minimal UX/UI** - Clean, responsive, mobile-first design
4. **Consistent Styling** - Unified design across all sections
5. **Keyword Sections** - 10-15 dedicated keyword sections (150-200 words each)
6. **Comprehensive FAQs** - MINIMUM 50 questions (MINIMUM 100+ words per answer)
7. **Rich HTML Tags** - Proper use of em, strong, ul, li, dl, dt, dd
8. **SEO Schema** - Structured data for Google indexing

## 📋 1. TABLE OF CONTENTS PATTERN (فهرست مطالب)

### EXACT PATTERN TO FOLLOW:
```blade
<!-- Table of Contents -->
<section class="bg-white rounded-2xl border border-gray-200 p-6 mb-8 mt-8">
    <h2 class="text-xl font-bold text-dark-sky-700 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
        فهرست مطالب - دسترسی سریع به بخش‌های مختلف
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <!-- Each link with specific color dot and hover effect -->
        <a href="#section-id" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-all duration-200 p-2 rounded hover:bg-blue-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-[color]-500 rounded-full"></span>
            <span class="text-sm">[Section Title]</span>
        </a>
        <!-- More links... -->
    </div>
    
    <div class="mt-4 p-3 bg-sky-50 rounded-lg border border-sky-200">
        <p class="text-sm text-sky-700 flex items-start gap-2">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>برای دسترسی سریع به هر بخش، روی عنوان مورد نظر کلیک کنید. این صفحه شامل راهنمای کاملی از [service description] است.</span>
        </p>
    </div>
</section>
```

### Color Rotation for Dots:
- Use different colors for visual distinction: blue, green, purple, orange, red, teal, pink, yellow, cyan, emerald, rose
- Each link gets a unique color to improve visual navigation

## 🔗 2. INTERNAL LINKING STRATEGY (MUST VALIDATE WITH ELOQUENT)

### ⚠️ CRITICAL: ALL LINKS MUST BE VALIDATED - NO 404 ERRORS!

### Eloquent Validation for Internal Links:
```php
// BEFORE adding any internal link, validate it exists:
$validServices = \App\Models\Service::where('status', 'active')
    ->pluck('slug', 'title')
    ->toArray();

// Example validation:
// loan-inquiry ✅ (exists in database)
// fake-service ❌ (would cause 404)
```

### Minimum Requirements:
- **50+ internal links** distributed throughout content
- **ALL links must be validated with Eloquent** - NO made-up links
- Links to REAL services that exist in database
- Natural integration within content flow

### Link Pattern Examples with Validation:
```blade
<!-- Within paragraphs -->
<p class="text-gray-700 leading-relaxed">
    <strong>رتبه اعتباری</strong> یک <em>امتیاز عددی</em> است که... 
    علاوه بر رتبه اعتباری، می‌توانید <a href="/services/cheque-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">وضعیت چک برگشتی</a>، 
    <a href="/services/loan-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">وام‌های دریافتی</a> و 
    <a href="/services/transaction-check" class="text-blue-600 hover:text-blue-800 underline transition-colors">اعتبار معاملاتی</a> خود را نیز بررسی کنید.
</p>

<!-- Within lists -->
<ul class="list-disc mr-6 mb-4 text-gray-700">
    <li><strong>سابقه بازپرداخت تسهیلات</strong> - بررسی با <a href="/services/loan-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">استعلام وام و تسهیلات</a></li>
    <li><em>میزان و تعداد وام‌های دریافتی</em> - قابل مشاهده در <a href="/services/loan-guarantee-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">استعلام ضمانت وام</a></li>
</ul>
```

### Strategic Link Placement:
1. First paragraph: 3-5 links to main related services
2. Each major section: 2-3 contextual links
3. Lists and enumerations: 1 link per item where relevant
4. Conclusion/summary: 5-7 links to action pages

## 🎨 3. MINIMAL & CONSISTENT UI/UX DESIGN

### CRITICAL STYLE RULES:

#### ❌ AVOID (Bad Patterns):
```blade
<!-- TOO COLORFUL - DON'T USE -->
<div class="bg-gradient-to-r from-red-500 via-yellow-500 to-green-500 p-8">

<!-- TOO MUCH SPACING - DON'T USE -->
<div class="p-12 m-10 space-y-8">

<!-- TOO NARROW - DON'T USE -->
<div class="max-w-md mx-auto">
```

#### ✅ USE (Good Patterns):
```blade
<!-- SUBTLE GRADIENTS -->
<div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6">

<!-- MODERATE SPACING -->
<div class="p-6 mb-6 space-y-4">

<!-- PROPER WIDTH -->
<div class="max-w-4xl mx-auto"> <!-- for main content -->
<div class="max-w-6xl mx-auto"> <!-- for container -->
```

### Color Palette Rules:
- **Primary backgrounds**: Use 50-level colors (blue-50, green-50, etc.)
- **Borders**: Use 200-level colors (border-blue-200)
- **Text**: Use 700-900 levels for headers, 600-700 for body
- **Accents**: Use 500-600 levels sparingly

### Spacing Guidelines:
- **Padding**: p-6 for sections, p-4 for cards
- **Margins**: mb-6 between sections, mb-4 between elements
- **Gap**: gap-4 for grids, gap-3 for smaller elements

## 📝 4. CONTENT STRUCTURE ORDER (CRITICAL)

### ⚠️ PROPER CONTENT ORDER:
1. **Table of Contents** (فهرست مطالب)
2. **Main Hero Section** (معرفی اصلی سرویس)
3. **PRIMARY CONTENT SECTIONS** (محتوای اصلی - 5-8 sections)
   - What is the service (این سرویس چیست)
   - How it works (نحوه کار)
   - Benefits (مزایا)
   - Requirements (مدارک مورد نیاز)
   - Process steps (مراحل انجام)
   - Important points (نکات مهم)
4. **KEYWORD SECTIONS** (بخش‌های کلیدواژه - EXTRAS - 10-15 sections)
5. **Related Services** (خدمات مرتبط)
6. **FAQs** (سوالات متداول - 50+ questions)

### ❌ WRONG ORDER:
- Starting with keyword sections
- Mixing keyword sections with main content
- Treating keyword sections as primary content

## 📝 4.1 MAIN CONTENT SECTION PATTERNS

### Main Hero Section:
```blade
<section class="bg-gradient-to-l from-sky-50 via-blue-50 to-indigo-50 rounded-3xl p-8 relative overflow-hidden mt-12 mb-12">
    <div class="absolute top-0 left-0 w-full h-full opacity-5">
        <!-- Subtle pattern background -->
    </div>
    
    <div class="max-w-4xl mx-auto relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-sky-600 rounded-full flex items-center justify-center">
                <svg><!-- Icon --></svg>
            </div>
            <h1 class="text-3xl font-bold text-dark-sky-700">[Main Title]</h1>
        </div>
        
        <p class="text-gray-700 leading-relaxed mb-6 text-lg">
            <!-- Rich content with internal links and semantic HTML -->
        </p>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Stat cards -->
        </div>
    </div>
</section>
```

### Standard Content Section:
```blade
<section class="mt-12 mb-12">
    <h2 id="section-id" class="text-2xl font-bold text-dark-sky-700 mb-6">[Section Title]</h2>
    
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            <!-- Content with proper HTML tags -->
        </p>
        
        <!-- Use various content structures -->
        <ul class="list-disc mr-6 mb-4 text-gray-700">
            <li><strong>Bold point</strong> - explanation</li>
            <li><em>Emphasized point</em> - details</li>
        </ul>
        
        <dl class="mr-6 mb-4 text-gray-700">
            <dt class="font-bold mb-2">Definition Term:</dt>
            <dd class="mb-4 mr-4">Definition description...</dd>
        </dl>
    </div>
</section>
```

## 🔍 5. KEYWORD-SPECIFIC SECTIONS (EXTRA CONTENT - NOT MAIN)

### ⚠️ CRITICAL: These are ADDITIONAL sections AFTER main content!
These keyword sections are EXTRAS that come AFTER your main service content sections.

### MANDATORY Structure for Each Keyword Section:
Each keyword from the command must have its own dedicated section with:
- **150-200 words minimum**
- **Unique content** focusing on that specific keyword
- **Internal links** to related services (validated with Eloquent)
- **Proper HTML semantic tags**
- **Multimedia Elements** (SVG icons, infographics, visual aids)

### Pattern for Keyword Sections with Multimedia:
```blade
<!-- Place these AFTER main content sections but BEFORE the FAQ section -->
<section class="mt-12 mb-12">
    <h2 id="keyword-section-1" class="text-2xl font-bold text-dark-sky-700 mb-6">[Keyword 1] - [Descriptive Title]</h2>
    
    <div class="bg-gradient-to-br from-[color1]-50 to-[color2]-50 rounded-2xl p-6 mb-6">
        <!-- Multimedia Element (REQUIRED) -->
        <div class="flex flex-col md:flex-row gap-6 items-start">
            <div class="flex-1">
                <p class="text-gray-700 leading-relaxed mb-4">
                    <strong>[Keyword variation]</strong> [150-200 words of unique content specifically about this keyword].
                    Include <em>emphasis</em> and <a href="/services/related">internal links</a> naturally.
                </p>
                
                <ul class="list-disc mr-6 text-gray-700">
                    <li>Point related to keyword</li>
                    <li>Another relevant point</li>
                </ul>
            </div>
            
            <!-- SVG Infographic (REQUIRED) -->
            <div class="flex-shrink-0 w-full md:w-64">
                <svg class="w-full h-auto" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Add relevant SVG infographic here -->
                    <!-- Can be chart, diagram, icon, or visual representation -->
                </svg>
            </div>
        </div>
    </div>
</section>
```

### Example Keywords Implementation:
If keywords are: "استعلام طرح ترافیک, جریمه طرح ترافیک, عوارض طرح ترافیک"

Create sections like:
1. "استعلام طرح ترافیک - راهنمای جامع بررسی وضعیت"
2. "جریمه طرح ترافیک - نحوه محاسبه و پرداخت آنلاین"
3. "عوارض طرح ترافیک - تعرفه‌ها و روش‌های پرداخت"

## ❓ 6. COMPREHENSIVE FAQ PATTERN (50+ QUESTIONS MANDATORY)

### ⚠️ CRITICAL FAQ REQUIREMENTS:
- **MINIMUM 50 questions** (not 10, not 20, not 30 - FIFTY or more!)
- **MINIMUM 100 words per answer** (detailed, comprehensive answers)
- **Categories**: General (عمومی), Technical (فنی), Financial (مالی), Process (فرآیند), Legal (قانونی)
- **Rich HTML formatting** in answers (lists, emphasis, links)
- **Internal links** in answers to related services

### EXACT Structure from credit-score-rating:
```blade
<section class="bg-white rounded-3xl shadow-lg p-8" dir="rtl">
    <div class="max-w-6xl mx-auto">
        <!-- FAQ Header -->
        <div class="text-center mb-8">
            <h2 id="faqs" class="text-3xl font-bold text-dark-sky-700 mb-4">سوالات متداول [Service Name]</h2>
            <p class="text-gray-600 text-lg">
                <strong>پیشخوانک</strong> [service description]
            </p>
            
            <!-- Search Box -->
            <div class="relative max-w-2xl mx-auto mt-6">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="faq-search" 
                       class="block w-full pr-10 pl-4 py-3 border border-gray-300 rounded-full leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-sky-500 focus:border-transparent text-right" 
                       placeholder="جستجو در سوالات متداول...">
            </div>
        </div>

        <!-- FAQ Categories -->
        <div class="flex flex-wrap justify-center gap-2 mb-8">
            <button class="faq-category-btn active bg-sky-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-sky-700 transition-colors" data-category="all">همه سوالات</button>
            <!-- More category buttons -->
        </div>

        <!-- FAQ Items -->
        <div class="space-y-4" id="faq-container">
            <div class="faq-item bg-gradient-to-l from-blue-50 to-sky-50 rounded-2xl border border-blue-200" data-category="basic">
                <button class="faq-toggle w-full text-right p-6 flex justify-between items-center focus:outline-none">
                    <span class="text-lg font-semibold text-blue-900">[Question]</span>
                    <svg class="faq-icon w-6 h-6 text-blue-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-6 text-gray-700 leading-relaxed">
                    <p>[Answer with proper HTML tags]</p>
                </div>
            </div>
        </div>
    </div>
</section>
```

## 🏷️ 7. HTML SEMANTIC TAGS USAGE

### MANDATORY Tags Throughout Content:

#### Text Emphasis:
```blade
<strong>Important terms</strong> <!-- For key concepts -->
<em>Emphasized phrases</em> <!-- For secondary emphasis -->
```

#### Lists:
```blade
<!-- Unordered lists -->
<ul class="list-disc mr-6 mb-4 text-gray-700">
    <li>Item with <strong>bold</strong> and <em>italic</em></li>
</ul>

<!-- Ordered lists -->
<ol class="list-decimal mr-6 mb-4 text-gray-700">
    <li>First step</li>
    <li>Second step</li>
</ol>

<!-- Definition lists -->
<dl class="mr-6 mb-4 text-gray-700">
    <dt class="font-bold mb-2">Term:</dt>
    <dd class="mb-4 mr-4">Definition or explanation</dd>
</dl>
```

#### Paragraphs with Rich Content:
```blade
<p class="text-gray-700 leading-relaxed mb-4">
    <strong>Key concept</strong> explanation with <em>emphasis</em> and 
    <a href="/link" class="text-blue-600 hover:text-blue-800 underline transition-colors">internal link</a>.
</p>
```

## 🎯 8. SEO SCHEMA IMPLEMENTATION

### Service Schema Structure:
```blade
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "[Service Name]",
    "description": "[Service Description]",
    "provider": {
        "@type": "Organization",
        "name": "پیشخوانک",
        "url": "https://pishkhanak.com"
    },
    "serviceType": "[Service Type]",
    "areaServed": {
        "@type": "Country",
        "name": "Iran"
    },
    "availableChannel": {
        "@type": "ServiceChannel",
        "serviceUrl": "[Service URL]",
        "servicePhone": "[Support Phone]",
        "availableLanguage": "fa-IR"
    }
}
</script>
```

## 📊 9. VISUAL ELEMENTS PATTERNS

### Info Graphics:
```blade
<!-- Process Flow -->
<div class="relative max-w-4xl mx-auto">
    <div class="absolute right-6 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-400 to-emerald-400 rounded-full opacity-30"></div>
    
    <div class="space-y-8">
        <!-- Step -->
        <div class="flex gap-6 relative">
            <div class="flex-shrink-0 relative z-10">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm shadow-lg ring-4 ring-blue-100">
                    ۱
                </div>
            </div>
            <div class="flex-1 bg-white rounded-xl p-5 border border-blue-200">
                <!-- Step content -->
            </div>
        </div>
    </div>
</div>
```

### Stats Grid:
```blade
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-2xl p-4 text-center shadow-sm">
        <div class="text-3xl font-bold text-sky-600">[Number]</div>
        <div class="text-sm text-gray-600 mt-1">[Label]</div>
    </div>
</div>
```

## 🔄 10. RELATED SERVICES SECTION (MUST USE ELOQUENT)

### ⚠️ CRITICAL: FETCH REAL SERVICES FROM DATABASE
```php
// MUST use Eloquent to fetch real services - NO MADE UP DATA!
$relatedServices = \App\Models\Service::where('status', 'active')
    ->where('parent_id', $service->parent_id) // Same parent category
    ->where('id', '!=', $service->id) // Exclude current service
    ->limit(6)
    ->get();

// For parent services, get children
if ($service->children()->exists()) {
    $relatedServices = $service->children()->where('status', 'active')->limit(6)->get();
}
```

### Pattern with Real Data:
```blade
@php
    // FETCH REAL SERVICES - NO DUMMY DATA!
    $relatedServices = \App\Models\Service::where('status', 'active')
        ->where('parent_id', $service->parent_id ?? null)
        ->where('id', '!=', $service->id)
        ->limit(6)
        ->get();
        
    if ($relatedServices->isEmpty() && $service->parent) {
        // Get siblings if no same-level services
        $relatedServices = $service->parent->children()
            ->where('status', 'active')
            ->where('id', '!=', $service->id)
            ->limit(6)
            ->get();
    }
@endphp

<section class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl p-8 mt-16" dir="rtl">
    <div class="max-w-6xl mx-auto">
        <h2 id="related-services" class="text-2xl font-bold text-dark-sky-700 mb-6 text-center">خدمات مرتبط</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($relatedServices as $relatedService)
            <a href="/services/{{ $relatedService->slug }}" class="bg-white rounded-xl p-4 border border-blue-200 hover:shadow-lg hover:border-blue-400 transition-all duration-200 group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $loop->index % 2 == 0 ? 'blue' : 'green' }}-100 rounded-full flex items-center justify-center group-hover:bg-{{ $loop->index % 2 == 0 ? 'blue' : 'green' }}-200">
                        <svg><!-- Icon --></svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 group-hover:text-blue-700">{{ $relatedService->title }}</h4>
                        <p class="text-xs text-gray-500">{{ Str::limit($relatedService->summary, 50) }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
```

## ⚠️ CRITICAL VALIDATION CHECKLIST v5

Before generating any content, verify:

1. ✅ **Table of Contents** with proper anchor links (فهرست مطالب)
2. ✅ **50+ internal links** validated with Eloquent (NO 404 errors!)
3. ✅ **Clean, minimal design** (50-level backgrounds, 200-level borders)
4. ✅ **Consistent styling** across all sections
5. ✅ **Main content sections FIRST** (5-8 primary sections)
6. ✅ **10-15 keyword sections as EXTRAS** (with multimedia/SVG)
7. ✅ **50+ FAQs** with 100+ word answers each
8. ✅ **Proper HTML semantic tags** (strong, em, ul, li, dl, dt, dd)
9. ✅ **SEO schema** implementation
10. ✅ **Mobile-responsive** design
11. ✅ **Related services** fetched with Eloquent (real data only)
12. ✅ **All links validated** - no made-up URLs

## 📐 RESPONSIVE DESIGN RULES

### Grid Layouts:
```blade
<!-- Responsive grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

<!-- Never use fixed widths for mobile -->
<!-- Always use responsive classes -->
```

### Text Sizing:
- Headers: `text-3xl` (desktop) → responsive
- Subheaders: `text-2xl` → `text-xl` on mobile
- Body: `text-base` or `text-lg`
- Small text: `text-sm` or `text-xs`

### Padding/Margin for Mobile:
```blade
<!-- Desktop padding → Mobile padding -->
<div class="p-8 md:p-6"> <!-- Less padding on mobile -->
<div class="mb-12 md:mb-8"> <!-- Less margin on mobile -->
```

## 🚨 COMMON MISTAKES TO AVOID

1. ❌ Forgetting Table of Contents
2. ❌ Using gradient backgrounds with multiple colors
3. ❌ Excessive padding/margins (p-12, m-10)
4. ❌ Narrow content containers (max-w-md)
5. ❌ Missing keyword-specific sections
6. ❌ Inconsistent FAQ styling
7. ❌ Plain text without HTML tags
8. ❌ Less than 50 internal links
9. ❌ Missing related services section
10. ❌ Not following exact patterns from credit-score-rating

## 📋 IMPLEMENTATION WORKFLOW

1. **Load parent service data** - Get all related services for internal linking
2. **Create Table of Contents** - List all sections with anchor links
3. **Generate Hero Section** - Main introduction with stats
4. **Add Content Sections** - With proper HTML tags and internal links
5. **Create Keyword Sections** - 10-15 sections, 150-200 words each
6. **Implement FAQs** - Using exact pattern from credit-score-rating
7. **Add Related Services** - Grid of related service links
8. **Validate** - Check all requirements are met

Remember: ALWAYS follow these patterns EXACTLY. No creativity or variations allowed!