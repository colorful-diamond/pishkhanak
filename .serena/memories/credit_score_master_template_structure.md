# Credit Score Master Template Structure - Reference Design

## Master Template File
**Path**: `/home/pishkhanak/htdocs/pishkhanak.com/resources/views/front/services/custom/credit-score-rating/content.blade.php`
**Status**: Master template - all services must match this structure exactly

## Critical Design Patterns (MANDATORY)

### 1. Standard Gradient Pattern
```blade
<!-- MANDATORY: Hero Section Gradient -->
<section class="bg-gradient-to-l from-sky-50 via-blue-50 to-indigo-50 rounded-3xl p-8 relative overflow-hidden mt-12 mb-12">
```

### 2. Standardized Section Headers
```blade
<!-- MANDATORY: All section headers must follow this pattern -->
<h2 id="section-id" class="text-2xl font-bold text-dark-sky-700 mb-6">
<h2 id="section-id" class="text-2xl font-bold text-dark-sky-700 mb-6 flex items-center gap-3">
```

### 3. Table of Contents Structure
```blade
<!-- MANDATORY: Table of contents section -->
<section class="bg-white rounded-2xl border border-gray-200 p-6 mb-8 mt-8">
    <h2 class="text-xl font-bold text-dark-sky-700 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
        </svg>
        فهرست مطالب
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <!-- Navigation links with colored dots -->
        <a href="#section-id" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-all duration-200 p-2 rounded hover:bg-blue-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
            <span class="text-sm">Section Title</span>
        </a>
    </div>
</section>
```

### 4. Hero Section with Statistics
```blade
<!-- MANDATORY: Hero section structure -->
<section class="bg-gradient-to-l from-sky-50 via-blue-50 to-indigo-50 rounded-3xl p-8 relative overflow-hidden mt-12 mb-12">
    <!-- Background pattern -->
    <div class="absolute top-0 left-0 w-full h-full opacity-5">
        <svg class="w-full h-full">
            <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#94a3b8" stroke-width="1"/>
            </pattern>
            <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
    </div>
    
    <div class="max-w-4xl mx-auto relative z-10">
        <!-- Service icon and title -->
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-sky-600 rounded-full flex items-center justify-center">
                <svg class="w-7 h-7 text-white"><!-- Icon --></svg>
            </div>
            <h1 class="text-3xl font-bold text-dark-sky-700">Service Title</h1>
        </div>
        
        <!-- Service description -->
        <p class="text-gray-700 leading-relaxed mb-6 text-lg">
            Service description with <strong>key terms</strong> and <em>emphasis</em>
        </p>
        
        <!-- 4-column statistics grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-sky-600">Stat 1</div>
                <div class="text-sm text-gray-600 mt-1">Description</div>
            </div>
            <div class="bg-white rounded-2xl p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-emerald-600">Stat 2</div>
                <div class="text-sm text-gray-600 mt-1">Description</div>
            </div>
            <div class="bg-white rounded-2xl p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-purple-600">Stat 3</div>
                <div class="text-sm text-gray-600 mt-1">Description</div>
            </div>
            <div class="bg-white rounded-2xl p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-orange-600">Stat 4</div>
                <div class="text-sm text-gray-600 mt-1">Description</div>
            </div>
        </div>
    </div>
</section>
```

### 5. Standard Section Structure
```blade
<!-- MANDATORY: Section structure pattern -->
<section class="mt-12 mb-12">
    <h2 id="section-id" class="text-2xl font-bold text-dark-sky-700 mb-6">Section Title</h2>
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <!-- Section content -->
    </div>
</section>
```

### 6. FAQ System Integration
```blade
<!-- MANDATORY: FAQ include at end of content -->
@include('front.services.custom.SERVICE-NAME.faq-section')
```

## Section IDs and Navigation (Credit Score Reference)

### Required Section IDs:
- `#why-pishkhanak` - Why Pishkhanak section
- `#what-is-credit-rating` - Service explanation
- `#credit-rating-system` - System details
- `#pishkhanak-system` - How system works
- `#pishkhanak-benefits` - Benefits section
- `#process-steps` - Process steps with gradient flow
- `#national-banks` - Banks integration
- `#free-credit-check` - Free vs paid
- `#iranian-credit-system` - Iranian system specific
- `#view-credit-report` - Report viewing
- `#online-banking-credit` - Online banking
- `#comprehensive-keywords` - Keywords section
- `#related-services` - Related services
- `#faqs` - FAQ section

## Visual Design Standards

### Color Scheme:
- **Primary Text**: `text-dark-sky-700`
- **Hero Gradient**: `bg-gradient-to-l from-sky-50 via-blue-50 to-indigo-50`
- **Cards**: `bg-white rounded-2xl border border-gray-200`
- **Stats Colors**: sky-600, emerald-600, purple-600, orange-600

### Typography:
- **Main Headers**: `text-2xl font-bold text-dark-sky-700 mb-6`
- **Section Headers**: Include icons with `flex items-center gap-3`
- **Body Text**: `text-gray-700 leading-relaxed`

### Layout Patterns:
- **Spacing**: `mt-12 mb-12` for sections
- **Cards**: `rounded-2xl p-6` for content containers
- **Grids**: Responsive grid layouts for statistics and services

## Process Steps Visualization

### Gradient Flow Pattern:
```blade
<!-- MANDATORY: Process steps with gradient visualization -->
<div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8">
    <div class="relative max-w-4xl mx-auto">
        <!-- Gradient line connector -->
        <div class="absolute right-6 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-400 via-purple-400 via-green-400 via-orange-400 to-emerald-400 rounded-full opacity-30"></div>
        
        <div class="space-y-8">
            <!-- Individual step -->
            <div class="flex gap-6 relative">
                <div class="flex-shrink-0 relative z-10">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm shadow-lg ring-4 ring-blue-100">1</div>
                </div>
                <div class="flex-1 bg-white rounded-xl p-5 border border-blue-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <!-- Step content -->
                </div>
            </div>
        </div>
    </div>
</div>
```

## Related Services Structure

### Service Grid Pattern:
```blade
<!-- MANDATORY: Related services section -->
<section class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl p-8 mt-16">
    <div class="max-w-6xl mx-auto">
        <h2 id="related-services" class="text-2xl font-bold text-dark-sky-700 mb-6 text-center">خدمات مرتبط</h2>
        
        <!-- Service categories -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a class="bg-white rounded-xl p-4 border border-blue-200 hover:shadow-lg hover:border-blue-400 transition-all duration-200 group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-service-color-100 rounded-full flex items-center justify-center group-hover:bg-service-color-200">
                        <svg class="w-6 h-6 text-service-color-600"><!-- Icon --></svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 group-hover:text-blue-700">Service Name</h4>
                        <p class="text-xs text-gray-500">Service Description</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>
```

## Performance and SEO Patterns

### Meta Structure:
- Proper heading hierarchy (h1 → h2 → h3)
- Semantic HTML structure
- Accessibility attributes
- Persian RTL optimization

### Internal Linking:
- Service cross-references with proper anchor links
- Related services grid with hover effects
- Table of contents with smooth scrolling

## Validation Checklist

When implementing any service using this template:

1. ✅ **Gradient Check**: Hero section uses exact gradient pattern
2. ✅ **Header Check**: All h2 headers use standard classes and have IDs
3. ✅ **Table of Contents**: Complete navigation with colored dots
4. ✅ **Statistics Grid**: 4-column responsive stats in hero
5. ✅ **Process Steps**: Gradient flow visualization
6. ✅ **Related Services**: Grid layout with hover effects
7. ✅ **FAQ Integration**: Include statement at end of file
8. ✅ **Color Consistency**: text-dark-sky-700 for headers
9. ✅ **Spacing**: mt-12 mb-12 for section spacing
10. ✅ **Card Design**: rounded-2xl border border-gray-200 pattern

## Usage in Commands

This structure should be recalled and applied when running:
- `/sc:template-consistency [service-name]`
- `/sc:enterprise-premium [service-name]`
- `/sc:enterprise-basic [service-name]`
- Any service content generation command

## Implementation Notes

- **Persian RTL**: All text properly aligned for right-to-left reading
- **Responsive**: Mobile-first approach with proper breakpoints
- **Performance**: Optimized images and minimal external dependencies
- **Accessibility**: ARIA labels and semantic structure
- **SEO**: Proper heading hierarchy and meta optimization

This template structure serves as the definitive reference for all Pishkhanak service pages, ensuring complete visual and structural consistency across the platform.