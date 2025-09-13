# Laravel Service View Integration Patterns

## Directory Structure Pattern
```
resources/views/front/services/custom/{service-slug}/
├── content.blade.php      # Main content (10,000+ words)
├── faqs.blade.php         # FAQ system (60+ FAQs)
└── upper.blade.php        # Form interface
```

## View Resolution Pattern
- **Service Model**: `Service::find(24)` → slug: `active-plates-list`
- **URL Route**: `/services/active-plates-list` → `ServiceController@show`
- **View Path**: `front.services.custom.active-plates-list.{view}`
- **Include Pattern**: `@include('front.services.custom.active-plates-list.faqs')`

## Template Integration
```php
// In content.blade.php
{{-- Include FAQ Section --}}
<div class="mt-16">
    @include('front.services.custom.active-plates-list.faqs')
</div>
```

## Blade Template Structure
```php
@extends('front.services.custom.upper-base')

@section('service_title', 'لیست پلاک‌های فعال')
@section('submit_text', 'دریافت لیست پلاک‌ها')

@section('form_fields')
    @include('front.services.custom.partials.personal-info-fields')
@endsection
```

## Service Controller Integration
- **Route Binding**: Service resolved by slug parameter
- **View Resolution**: Laravel automatically resolves view paths
- **Data Passing**: Service model passed to view context
- **Template Compilation**: Blade engine compiles all includes

## Validation Commands
```bash
# Clear view cache
php artisan view:clear

# Test view resolution
php artisan tinker --execute="view('front.services.custom.{slug}.faqs')"

# Syntax validation
php -l /path/to/view.blade.php
```

## Best Practices Identified
1. **Consistent Naming**: Match directory name with service slug
2. **Proper Includes**: Use @include for modular content
3. **View Resolution**: Test Laravel view compilation
4. **Service Model**: Validate service exists and is active
5. **Template Structure**: Follow existing patterns for compatibility