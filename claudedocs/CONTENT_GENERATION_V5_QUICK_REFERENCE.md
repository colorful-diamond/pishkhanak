# 🚀 Enterprise Content Generation v5 - Quick Reference

## 🚨 CRITICAL: CHUNK LOADING FOR REFERENCE FILES
**ALWAYS load content files in 500-line chunks:**
```bash
Read(file, limit=500, offset=0)    # First chunk
Read(file, limit=500, offset=500)  # Second chunk
Read(file, limit=500, offset=1000) # Third chunk
# Continue until complete...
```

## ⚡ IMMEDIATE CHECKLIST FOR EVERY GENERATION

### 🔴 CRITICAL MUST-HAVES (Non-Negotiable)
1. ✅ **Table of Contents** - فهرست مطالب section at top
2. ✅ **50+ Internal Links** - Count them!
3. ✅ **10-15 Keyword Sections** - Each 150-200 words, BEFORE FAQs
4. ✅ **Standardized FAQs** - Copy exact structure from credit-score-rating
5. ✅ **Clean Design** - bg-[color]-50 only, NO dark gradients
6. ✅ **HTML Tags** - Use `<strong>`, `<em>`, `<ul>`, `<li>` everywhere

## 📋 CONTENT STRUCTURE ORDER (Follow Exactly)
```
1. Table of Contents (فهرست مطالب)
2. Hero Section with Stats Grid
3. Main Content Sections (5-8)
4. Keyword-Specific Sections (10-15) ← BEFORE FAQs!
5. FAQ Section (credit-score-rating pattern)
6. Related Services Grid
```

## 🎨 STYLE CHEAT SHEET

### ✅ CORRECT Styling:
```blade
<!-- Backgrounds -->
bg-blue-50, bg-green-50, bg-purple-50

<!-- Gradients (subtle only) -->
from-blue-50 to-indigo-50

<!-- Padding -->
p-6 (sections), p-4 (cards)

<!-- Width -->
max-w-4xl (content), max-w-6xl (container)
```

### ❌ NEVER Use:
```blade
<!-- Too colorful -->
from-red-500 via-yellow-500 to-green-500

<!-- Too much padding -->
p-12, p-10, m-10

<!-- Too narrow -->
max-w-md, max-w-sm
```

## 🔗 INTERNAL LINKING FORMULA
- **First paragraph**: 3-5 links
- **Each section**: 2-3 links
- **Lists**: 1 link per item
- **Keyword sections**: 2-3 links each
- **FAQs**: 1-2 links in answers
- **Total**: MINIMUM 50 links

## 📝 KEYWORD SECTION TEMPLATE
```blade
<section class="mt-12 mb-12">
    <h2 id="keyword-[number]" class="text-2xl font-bold text-dark-sky-700 mb-6">
        [KEYWORD] - [Descriptive Title]
    </h2>
    
    <div class="bg-gradient-to-br from-[color]-50 to-[color]-50 rounded-2xl p-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            <strong>[Keyword]</strong> [150-200 words content].
            Include <em>emphasis</em> and <a href="/services/[slug]">links</a>.
        </p>
    </div>
</section>
```

## ❓ FAQ STRUCTURE (Copy Exactly)
```blade
<section class="bg-white rounded-3xl shadow-lg p-8" dir="rtl">
    <div class="max-w-6xl mx-auto">
        <!-- Header with search -->
        <!-- Category buttons -->
        <!-- FAQ items with gradient backgrounds -->
    </div>
</section>
```

## 🚨 COMMON MISTAKES TO AVOID
1. ❌ Forgetting Table of Contents
2. ❌ Less than 50 links
3. ❌ Missing keyword sections
4. ❌ Dark/bright gradients
5. ❌ p-12 padding
6. ❌ Plain text without HTML tags
7. ❌ FAQ section different from credit-score-rating

## ✅ FINAL VALIDATION
Before finishing, count:
- [ ] Table of Contents sections: All linked?
- [ ] Internal links: 50+ present?
- [ ] Keyword sections: 10-15 created?
- [ ] FAQs: 60+ questions?
- [ ] HTML tags: Used throughout?
- [ ] Styling: Clean and minimal?

## 🎯 COMMAND TO USE
```bash
/sc:enterprise-autonomous-v5 --service-id:[ID] \
    keywords:"[keywords]" \
    --reference-design="credit-score-rating" \
    --pattern-strict \
    --internal-links=50+ \
    --keyword-sections=auto \
    --comprehensive-faqs=60+ \
    words:12000
```

## 📚 REFERENCE FILES
- Pattern Guide: `/claudedocs/ENTERPRISE_CONTENT_GENERATION_PATTERNS.md`
- Reference Content: `/resources/views/front/services/custom/credit-score-rating/content.blade.php`
- Reference FAQ: `/resources/views/front/services/custom/credit-score-rating/faq-section.blade.php`

**REMEMBER**: Follow patterns EXACTLY - No creativity allowed!