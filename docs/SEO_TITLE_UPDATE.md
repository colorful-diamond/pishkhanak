# SEO Title Update - Remove Redundant Branding

## Overview
Removed "| پیشخوانک" from all page titles since the branding is already handled site-wide in the configuration.

## Files Updated

### 1. app/Traits/SeoTrait.php
- **setServiceSeo()**: Removed "| پیشخوانک" from service titles
- **setBlogSeo()**: Removed "| وبلاگ پیشخوانک" from blog post titles  
- **setCategorySeo()**: Removed "| پیشخوانک" from category titles

### 2. app/Http/Controllers/Web/PageController.php
- **showHome()**: Changed "پیشخوانک - استعلام هر آنچه که می خواهید!" → "استعلام هر آنچه که می خواهید!"
- **showAbout()**: Changed "درباره ما | پیشخوانک" → "درباره ما"
- **showContact()**: Changed "تماس با ما | پیشخوانک" → "تماس با ما"
- **showPrivacyPolicy()**: Changed "سیاست حفظ حریم خصوصی | پیشخوانک" → "سیاست حفظ حریم خصوصی"
- **showTermsConditions()**: Changed "شرایط و قوانین استفاده | پیشخوانک" → "شرایط و قوانین استفاده"

### 3. app/Http/Controllers/Web/ServiceController.php
- **showResult()**: Changed "نتیجه X | پیشخوانک" → "نتیجه X"
- Updated description to remove "در پیشخوانک"

### 4. app/Http/Controllers/Web/BlogController.php
- **index()**: Changed "وبلاگ پیشخوانک - آخرین اخبار و مقالات" → "وبلاگ - آخرین اخبار و مقالات"

## Result
- All page titles are now cleaner without redundant branding
- Site-wide branding is handled by the seotools configuration
- Titles focus on page content rather than repeated brand name
- Better SEO optimization with more concise, relevant titles

## Before vs After Examples

### Homepage
- **Before**: "پیشخوانک - استعلام هر آنچه که می خواهید!"
- **After**: "استعلام هر آنچه که می خواهید!"

### Service Page  
- **Before**: "کارت به شبا | پیشخوانک"
- **After**: "کارت به شبا"

### Blog Post
- **Before**: "مقاله نمونه | وبلاگ پیشخوانک" 
- **After**: "مقاله نمونه"

The site-wide branding "پیشخوانک" will still appear due to the configuration in `config/seotools.php` defaults. 