# AI Chat System Improvements v2 - Service Selection & Related Services Fix

## Issues Addressed

### 1. **Main Service Not Being Selected**
**Problem**: When user sends "تبدیل شماره کارت به شبا", the main service wasn't being shown as the selected service.

**Solution**: 
- Enhanced service matching with keyword-based identification
- Added specific service selection rules in the AI prompt
- Improved `selected_service` vs `suggested_services` distinction

### 2. **Related Services Showing Slugs Instead of Titles**
**Problem**: Related services section was displaying service slugs instead of proper titles.

**Solution**:
- Updated `handleServiceInquiry()` method to properly load services from database
- Added proper title display logic
- Fixed service filtering to exclude main service from related services

### 3. **Services Not Loaded Dynamically from Database**
**Problem**: Services were hardcoded in prompts instead of being loaded dynamically with categories.

**Solution**:
- Completely rewrote `getAllServices()` method to load from database with categories
- Added category-based service organization like the home page
- Implemented service keyword generation for better matching

## Technical Implementation

### 1. Enhanced Service Loading (`getAllServices()`)

```php
protected function getAllServices(): array
{
    return Cache::remember('ai_chat_services_with_categories', self::CACHE_TTL_SERVICES, function() {
        // Get categories with their services (like home page)
        $categories = \App\Models\ServiceCategory::with(['services' => function($query) {
            $query->where('status', 'active')
                  ->whereNull('parent_id')
                  ->orderBy('views', 'desc');
        }])
        ->active()
        ->ordered()
        ->get();
        
        // Transform into AI-friendly format with keywords
    });
}
```

### 2. Service Keyword Generation (`generateServiceKeywords()`)

Added intelligent keyword matching for:
- **Card to SHEBA**: 'کارت به شبا', 'تبدیل کارت', 'شماره کارت', etc.
- **Account to SHEBA**: 'حساب به شبا', 'تبدیل حساب', etc.
- **Traffic Violations**: 'خلافی خودرو', 'جریمه رانندگی', etc.
- **National ID**: 'کارت ملی', 'استعلام ملی', etc.

### 3. Enhanced AI Prompt (63 Conditions)

#### Service Selection Rules:
1. For 'کارت به شبا' → Select card-to-sheba service as main service
2. For 'حساب به شبا' → Select account-to-sheba service as main service  
3. Main service goes in `selected_service`, related services in `suggested_services`
4. Keyword matching is crucial for proper service identification

#### Response Format:
```json
{
  "intent": "service_inquiry|service_request|general_question|general_conversation|file_analysis",
  "confidence": 0.95,
  "response": "HTML formatted response",
  "selected_service": "main-service-slug",
  "suggested_services": ["related-service-1", "related-service-2"],
  "requires_data": ["field1", "field2"],
  "has_required_data": false,
  "data_validation_status": "validation_details"
}
```

### 4. Improved Service Inquiry Handler (`handleServiceInquiry()`)

```php
protected function handleServiceInquiry(array $analysis, string $message): array
{
    // Get main selected service
    $selectedService = Service::where('slug', $analysis['selected_service'])->first();
    
    // Get suggested services with proper titles
    $suggestedServices = Service::whereIn('slug', $analysis['suggested_services'])
        ->where('status', 'active')
        ->get();
    
    // Display main service details
    if ($selectedService) {
        $response = "<p><strong>{$selectedService->title}</strong></p>";
        // Add service details, required fields, pricing, etc.
    }
    
    // Display related services (exclude main service)
    $filteredSuggested = $suggestedServices->filter(function($service) use ($selectedService) {
        return !$selectedService || $service->id !== $selectedService->id;
    });
    
    // Show proper titles instead of slugs
    foreach ($filteredSuggested as $service) {
        $priceText = $service->price > 0 ? " (" . number_format($service->price) . " تومان)" : " (رایگان)";
        $response .= "<li><a href=\"{$service->getUrl()}\" target=\"_blank\">{$service->title}</a>{$priceText}</li>";
    }
}
```

## Before vs After Comparison

### Before (Problematic):
```
User: "تبدیل شماره کارت به شبا"

Response:
"تبدیل شماره کارت به شماره شبا
اطلاعات مورد نیاز:
- شماره کارت

خدمات مرتبط:
- card-to-account  <-- SLUG SHOWN
- sheba-check      <-- SLUG SHOWN
- account-to-sheba <-- SLUG SHOWN
- card-to-sheba    <-- MAIN SERVICE IN RELATED!"
```

### After (Fixed):
```
User: "تبدیل شماره کارت به شبا"

Response:
"تبدیل شماره کارت به شماره شبا    <-- MAIN SERVICE SELECTED

اطلاعات مورد نیاز:
- شماره کارت (16 رقم)

هزینه: 2,500 تومان

برای استفاده از این سرویس، لطفاً اطلاعات مورد نیاز را ارسال کنید یا روی لینک زیر کلیک کنید:
[رفتن به صفحه سرویس]

خدمات مرتبط:
- تبدیل شماره کارت به شماره حساب (2,500 تومان)    <-- PROPER TITLES
- استعلام و بررسی شماره شبا (2,500 تومان)         <-- PROPER TITLES  
- تبدیل شماره حساب به شبا (2,500 تومان)          <-- PROPER TITLES"
```

## Enhanced Test Framework

### Updated Test Cases:
1. **"کارت به شبا"** → service_inquiry + main service selected
2. **"تبدیل شماره کارت به شبا"** → service_inquiry + main service selected
3. **"خلافی خودرو"** → service_inquiry + traffic service selected
4. **"حساب به شبا"** → service_inquiry + account-to-sheba selected

### Test Route: `/test-ai-chat`
- Enhanced with service selection verification
- Added main service vs suggested services analysis
- Visual feedback for proper service identification

## Key Improvements Summary

### ✅ **Service Selection Fixed**
- Main service now properly identified and displayed
- "تبدیل شماره کارت به شبا" correctly selects card-to-sheba service
- Keyword-based matching ensures accurate service selection

### ✅ **Related Services Display Fixed**
- Shows proper service titles instead of slugs
- Excludes main service from related services list
- Includes pricing information for each service

### ✅ **Dynamic Service Loading**
- Services loaded from database with categories like home page
- Cached for performance (1-hour TTL)
- Keyword generation for better matching

### ✅ **Enhanced AI Logic**
- 63 specific conditions for proper classification
- Clear distinction between main and related services
- Improved confidence scoring and validation

## Database Structure Used

### Services Table:
- `id`, `title`, `short_title`, `slug`, `summary`, `description`
- `price`, `status`, `category_id`, `parent_id`

### Service Categories Table:
- `id`, `name`, `slug`, `is_active`, `display_order`

### Relationships:
- Service belongs to ServiceCategory
- Service can have parent Service (for sub-services)

## Performance Considerations

1. **Caching**: Service data cached for 1 hour
2. **Query Optimization**: Uses eager loading with specific conditions
3. **Keyword Generation**: Pre-computed and cached
4. **Memory Efficient**: Only loads active services

## Testing and Verification

### Manual Testing:
1. Visit `/test-ai-chat` route
2. Check "تبدیل شماره کارت به شبا" test case
3. Verify main service is selected correctly
4. Confirm related services show proper titles

### Expected Results:
- Main service: `selected_service` contains card-to-sheba slug
- Related services: `suggested_services` contains related service slugs
- Response shows proper titles, not slugs
- Pricing information displayed correctly

## Future Enhancements

1. **Machine Learning**: Improve service matching based on user interactions
2. **Fuzzy Matching**: Handle typos and variations in service names
3. **User Preferences**: Remember frequently used services
4. **Analytics**: Track service selection accuracy
5. **Multi-language**: Support English service names

## Conclusion

The AI chat system now:
- ✅ Correctly identifies main services based on user input
- ✅ Displays proper service titles instead of slugs in related services
- ✅ Loads services dynamically from database with categories
- ✅ Provides comprehensive service information with pricing
- ✅ Maintains performance through intelligent caching
- ✅ Offers robust testing framework for ongoing validation

The specific issue with "تبدیل شماره کارت به شبا" not showing as the main service has been completely resolved through enhanced keyword matching and service selection logic. 