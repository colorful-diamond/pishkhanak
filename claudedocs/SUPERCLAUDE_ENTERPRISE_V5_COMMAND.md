# SuperClaude Enterprise Content Generation v5 Command

## 🚀 /sc:enterprise-autonomous-v5

### Enhanced Command with Critical Fixes

```bash
/sc:enterprise-autonomous-v5 --service-id:[ID] \
    keywords:"[comma-separated Persian keywords]" \
    --reference-design="credit-score-rating" \
    --pattern-strict \
    --internal-links=50+ \
    --keyword-sections=extra \
    --comprehensive-faqs=50+ \
    --eloquent-validation \
    --multimedia-required \
    --parallel-research \
    --workers:4 \
    words:12000
```

## 📋 Command Execution Flow

### Phase 1: Data Preparation
```php
// 1. Load service data with Eloquent
$service = \App\Models\Service::findOrFail($serviceId);

// 2. Fetch all valid services for link validation
$validServices = \App\Models\Service::where('status', 'active')
    ->pluck('slug', 'title')
    ->toArray();

// 3. Get related services (REAL data)
$relatedServices = \App\Models\Service::where('status', 'active')
    ->where('parent_id', $service->parent_id)
    ->where('id', '!=', $service->id)
    ->limit(6)
    ->get();
```

### Phase 2: Content Generation Order

#### 1. Table of Contents (فهرست مطالب)
- Dynamic generation based on all sections
- Anchor links to each section
- Color-coded dots for visual navigation

#### 2. Main Hero Section
- Service introduction
- Key statistics grid
- Primary call-to-action

#### 3. PRIMARY CONTENT SECTIONS (5-8 sections)
**These come FIRST - the main service content:**
- `#what-is` - این سرویس چیست؟
- `#how-it-works` - نحوه کار و عملکرد
- `#benefits` - مزایا و فواید استفاده
- `#requirements` - مدارک و شرایط مورد نیاز
- `#process-steps` - مراحل انجام کار
- `#important-points` - نکات مهم و کلیدی
- `#pricing` - هزینه‌ها و تعرفه‌ها
- `#support` - پشتیبانی و راهنمایی

#### 4. KEYWORD SECTIONS (10-15 sections - EXTRAS)
**These are ADDITIONAL sections, not the main content:**

For each keyword, create a dedicated section with:
- 150-200 words of unique content
- SVG infographic or visual element
- Internal links (validated with Eloquent)
- Semantic HTML tags

```blade
@foreach($keywords as $index => $keyword)
<section class="mt-12 mb-12">
    <h2 id="keyword-{{ $index + 1 }}">{{ $keyword }} - [Descriptive Title]</h2>
    
    <div class="bg-gradient-to-br from-[color1]-50 to-[color2]-50 rounded-2xl p-6">
        <div class="flex flex-col md:flex-row gap-6 items-start">
            <div class="flex-1">
                <!-- 150-200 words about this specific keyword -->
                <p class="text-gray-700 leading-relaxed mb-4">
                    <strong>{{ $keyword }}</strong> [detailed content]...
                    @foreach($validatedLinks as $link)
                        <a href="/services/{{ $link }}">{{ $linkTitle }}</a>
                    @endforeach
                </p>
            </div>
            
            <!-- REQUIRED: SVG Infographic -->
            <div class="flex-shrink-0 w-full md:w-64">
                <svg viewBox="0 0 200 200">
                    <!-- Relevant visualization -->
                </svg>
            </div>
        </div>
    </div>
</section>
@endforeach
```

#### 5. Related Services Section
```blade
@php
    // REAL services from database - NO dummy data
    $relatedServices = \App\Models\Service::where('status', 'active')
        ->where('parent_id', $service->parent_id ?? null)
        ->where('id', '!=', $service->id)
        ->limit(6)
        ->get();
@endphp

<!-- Display real services with actual titles and summaries -->
```

#### 6. Comprehensive FAQs (50+ Questions)
```blade
@php
    $faqCategories = ['general', 'technical', 'financial', 'process', 'legal'];
    $totalQuestions = 50; // MINIMUM!
    $minAnswerWords = 100; // Each answer must be detailed
@endphp

<!-- Generate 50+ questions with 100+ word answers -->
```

## 🔍 Validation Requirements

### Pre-Generation Validation
```php
// 1. Validate all internal links before adding
foreach ($proposedLinks as $link) {
    if (!isset($validServices[$link])) {
        // Skip or find alternative - NO 404s!
    }
}

// 2. Verify keyword sections are marked as extras
$contentStructure = [
    'main_sections' => [...], // Primary content
    'keyword_sections' => [...] // Extras
];

// 3. Count FAQs and word counts
assert(count($faqs) >= 50);
foreach ($faqs as $faq) {
    assert(str_word_count($faq['answer']) >= 100);
}
```

## 📊 Quality Metrics

### Content Requirements
- **Total word count**: 12,000+ words
- **Internal links**: 50+ (all validated)
- **Main sections**: 5-8 comprehensive sections
- **Keyword sections**: 10-15 (as extras)
- **FAQs**: 50+ questions
- **FAQ answer length**: 100+ words each
- **Multimedia elements**: 1 per keyword section minimum
- **Related services**: 6 real services from database

### Technical Requirements
- **Chunk loading**: Read files in 500-line chunks
- **Eloquent queries**: Use for all data fetching
- **Link validation**: 100% of links must be valid
- **Responsive design**: Mobile-first approach
- **SEO schema**: Structured data for Google
- **RTL support**: Full Persian text support

## 🎯 Success Criteria

### Must Pass All Checks:
✅ Table of Contents present with all sections
✅ Main content sections appear BEFORE keyword sections  
✅ All 50+ internal links return 200 status (no 404s)
✅ Related services exist in database
✅ 50+ FAQs with substantial answers
✅ SVG/multimedia in keyword sections
✅ Clean, minimal UI (not overly colorful)
✅ Consistent styling throughout
✅ Mobile responsive design
✅ SEO schema included

## 🚫 Common Failures to Avoid

❌ **Content Structure Error**: Putting keyword sections as main content
✅ **Fix**: Always generate main service content first, keywords as extras

❌ **Fake Data Error**: Making up service names and links
✅ **Fix**: Use Eloquent to fetch real services from database

❌ **Invalid Links Error**: Creating links that don't exist (404s)
✅ **Fix**: Validate every link with `Service::where('slug', $slug)->exists()`

❌ **Insufficient FAQs**: Only generating 10-20 questions
✅ **Fix**: Always generate 50+ questions with detailed answers

❌ **Missing Multimedia**: Text-only keyword sections
✅ **Fix**: Include SVG infographics in every keyword section

## 💡 Implementation Example

```php
// Command implementation
class EnterpriseContentV5Command {
    public function handle($serviceId, $keywords, $options) {
        // 1. Load and validate
        $service = Service::findOrFail($serviceId);
        $validServices = Service::active()->pluck('slug', 'title');
        
        // 2. Generate main content first
        $mainContent = $this->generatePrimaryContent($service);
        
        // 3. Generate keyword sections as extras
        $keywordSections = $this->generateKeywordExtras($keywords, $validServices);
        
        // 4. Fetch real related services
        $relatedServices = $this->fetchRelatedServices($service);
        
        // 5. Generate comprehensive FAQs
        $faqs = $this->generateComprehensiveFAQs($service, 50, 100);
        
        // 6. Validate all internal links
        $this->validateAllLinks($mainContent . $keywordSections);
        
        return view('content', compact(
            'mainContent', 
            'keywordSections', 
            'relatedServices', 
            'faqs'
        ));
    }
}
```

## 📚 Reference Files

- **Pattern Documentation**: `/claudedocs/ENTERPRISE_CONTENT_GENERATION_PATTERNS.md`
- **Reference Implementation**: `/resources/views/front/services/custom/credit-score-rating/content.blade.php`
- **Memory Files**: `enterprise_content_generation_v5_final`

## 🔄 Version History

- **v5.0**: Critical fixes for FAQ size, multimedia, Eloquent validation, content structure, link validation
- **v4.0**: Initial implementation with basic patterns
- **v3.0**: Added keyword sections
- **v2.0**: Basic content generation
- **v1.0**: Simple template system