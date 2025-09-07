# SEO Keywords Fix - Handle String and Array Formats

## Issue Fixed
The `meta_keywords` field in services, posts, and categories could be stored as either:
- **Array format**: `['keyword1', 'keyword2', 'keyword3']`
- **String format**: `"keyword1, keyword2, keyword3"`

This caused errors when using `array_merge()` since it expected arrays only.

## Solution Implemented

### 1. Added `normalizeKeywords()` Helper Method
Created a private method in `SeoTrait` that:
- Converts comma-separated strings to arrays
- Trims whitespace from each keyword
- Filters out empty values
- Returns existing arrays unchanged
- Handles null/empty values gracefully

```php
private function normalizeKeywords($keywords)
{
    if (is_string($keywords)) {
        return array_filter(array_map('trim', explode(',', $keywords)));
    }
    
    return is_array($keywords) ? $keywords : [];
}
```

### 2. Updated All SEO Methods
- **setSeo()**: Now handles keywords parameter as string or array
- **setServiceSeo()**: Normalizes `$service->meta_keywords`
- **setBlogSeo()**: Normalizes `$post->meta_keywords`
- **setCategorySeo()**: Normalizes `$category->meta_keywords`

## Examples

### Before (Error-prone)
```php
// This would fail if meta_keywords was a string
$keywords = array_merge($service->meta_keywords, ['new', 'keywords']);
```

### After (Robust)
```php
// This works with both string and array formats
$keywords = array_merge(
    $this->normalizeKeywords($service->meta_keywords),
    ['new', 'keywords']
);
```

## Supported Formats

### String Format (Database)
```
"استعلام, کارت به شبا, بانکی, آنلاین"
```

### Array Format (Code)
```php
['استعلام', 'کارت به شبا', 'بانکی', 'آنلاین']
```

### Result (Always Array)
```php
['استعلام', 'کارت به شبا', 'بانکی', 'آنلاین', 'استعلام', 'پیشخوانک']
```

## Benefits
- **No More Errors**: Handles both string and array formats
- **Flexible Input**: Accepts keywords from database or hardcoded arrays
- **Clean Code**: Single helper method for all normalization
- **Backward Compatible**: Works with existing data formats
- **Automatic Cleanup**: Trims whitespace and removes empty values

## Files Updated
- `app/Traits/SeoTrait.php` - Added normalizeKeywords() method and updated all SEO methods

This fix ensures the SEO system works reliably regardless of how keywords are stored in the database. 