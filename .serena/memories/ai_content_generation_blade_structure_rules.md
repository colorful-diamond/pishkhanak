# AI Content Generation - Blade Template Structure Rules

## Critical Issue: Third-Party Insurance Form Display Bug (Sep 2025)

### Problem Description
AI-generated content files were created with incorrect Laravel Blade template structure, causing forms to disappear from service pages.

### Root Cause
The AI content generation system created `content.blade.php` files with full page template structure instead of includable content sections:

**❌ WRONG Structure (caused form to disappear):**
```blade
@extends('front.layouts.app')
@push('styles')
<!-- styles -->
@endpush
@section('content')
<!-- content -->
@endsection
```

**✅ CORRECT Structure (allows form to display):**
```blade
<style>
<!-- styles -->
</style>
<!-- content directly without sections -->
```

### Why This Breaks
Laravel's hierarchical view loading in `single.blade.php` tries to `@include()` content files. When content files have `@extends` and `@section`, they conflict with the parent template structure, causing the form section (upper.blade.php) to not render.

### Content File Types and Rules

#### 1. Upper Files (`upper.blade.php`)
- **Purpose**: Form sections
- **Structure**: Must use `@extends('front.services.custom.upper-base')`
- **Location**: `resources/views/front/services/custom/{service-slug}/upper.blade.php`
- **Contains**: Form fields, submit buttons, validation

#### 2. Content Files (`content.blade.php`) 
- **Purpose**: Main content sections (included by single.blade.php)
- **Structure**: ❌ NO @extends, ❌ NO @section, ❌ NO @push
- **Location**: `resources/views/front/services/custom/{service-slug}/content.blade.php`
- **Contains**: Article content, styling in `<style>` tags, JavaScript in `<script>` tags

#### 3. Lower Files (`lower.blade.php`)
- **Purpose**: Additional content sections
- **Structure**: ❌ NO @extends, ❌ NO @section, ❌ NO @push
- **Location**: `resources/views/front/services/custom/{service-slug}/lower.blade.php`

### Mandatory Content File Structure
```blade
{{-- Service Content: {Service Name} --}}
<style>
/* Custom styles */
</style>

<div class="container mx-auto px-4 py-8">
    <!-- Content sections -->
</div>

<script>
// JavaScript if needed
</script>
```

### Affected Services Recovery
- Third-party insurance history: Fixed by removing @extends/@section structure
- All future AI-generated content must follow includable structure

### Prevention Checklist
1. ✅ Content files must NOT use @extends
2. ✅ Content files must NOT use @section/@endsection  
3. ✅ Content files must NOT use @push/@endpush
4. ✅ Content files must be directly includable HTML/Blade
5. ✅ Test form display after content generation
6. ✅ Clear Laravel caches after content updates

### Testing Protocol
After generating content files:
1. Navigate to service URL
2. Verify form displays at top
3. Verify content displays below form
4. Test form submission functionality