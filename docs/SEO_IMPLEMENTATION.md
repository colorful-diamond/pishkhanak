# SEO Implementation Guide for Pishkhanak

This document outlines the comprehensive SEO implementation across the Pishkhanak website using the `artesaos/seotools` package.

## Overview

The SEO implementation covers:
- ✅ **Homepage** - Complete with website schema
- ✅ **Static Pages** - About, Contact, Privacy, Terms
- ✅ **Service Pages** - Individual services with structured data
- ✅ **Service Categories** - Category listing pages
- ✅ **Service Results** - Result pages with proper meta
- ✅ **Blog** - Blog index and individual posts
- ✅ **Meta Tags** - Title, description, keywords, canonical
- ✅ **Open Graph** - Facebook sharing optimization
- ✅ **Twitter Cards** - Twitter sharing optimization
- ✅ **JSON-LD** - Structured data for search engines
- ✅ **Breadcrumbs** - Navigation structure schema

## Files Modified

### Core Files
- `app/Traits/SeoTrait.php` - Main SEO functionality trait
- `app/Helpers/SeoHelper.php` - Additional SEO utilities
- `config/seotools.php` - Updated default configurations

### Controllers Updated
- `app/Http/Controllers/Web/PageController.php` - All static pages
- `app/Http/Controllers/Web/ServiceController.php` - Service-related pages
- `app/Http/Controllers/Web/BlogController.php` - Blog pages

### Layout File
- `resources/views/front/layouts/app.blade.php` - Already includes `{!! SEO::generate(true) !!}`

## Implementation Details

### 1. SeoTrait Usage

All controllers now use the `SeoTrait` which provides:

```php
// Basic SEO setup
$this->setSeo([
    'title' => 'Page Title',
    'description' => 'Page description',
    'keywords' => ['keyword1', 'keyword2'],
    'breadcrumbs' => [/* breadcrumb array */]
]);

// Service-specific SEO
$this->setServiceSeo($service, $parent);

// Blog-specific SEO
$this->setBlogSeo($post);

// Category-specific SEO
$this->setCategorySeo($category);
```

### 2. Structured Data (JSON-LD)

Each page type includes appropriate structured data:

- **Website**: Organization, search action
- **Services**: Service schema with provider information
- **Blog Posts**: Article schema with author and dates
- **Categories**: Collection page schema
- **Results**: Article schema for result pages

### 3. Page-Specific SEO

#### Homepage (`showHome`)
- Website schema with search functionality
- Comprehensive keywords covering all services
- Organization information

#### Service Pages (`show`)
- Service-specific title and description
- Category breadcrumbs
- Service schema with provider details
- Dynamic keywords based on service content

#### Blog Pages
- **Index**: Blog schema with pagination
- **Posts**: Article schema with author, dates, and content

#### Static Pages
- Custom titles and descriptions for each page
- Proper breadcrumb navigation
- Relevant keywords for each page type

### 4. Meta Tags Included

For each page:
- **Title**: Dynamic with site branding
- **Description**: Page-specific descriptions
- **Keywords**: Relevant keywords array
- **Canonical**: Current URL
- **Robots**: Index, follow
- **Author**: Pishkhanak
- **Language**: Persian (fa)
- **Viewport**: Mobile-optimized

### 5. Open Graph Tags

- **Title**: Page-specific titles
- **Description**: Optimized for social sharing
- **URL**: Canonical URL
- **Type**: website/article based on content
- **Site Name**: پیشخوانک
- **Images**: Default logo or page-specific images
- **Locale**: fa_IR with en_US alternate

### 6. Twitter Cards

- **Card Type**: summary_large_image
- **Site**: @estelam_net
- **Title**: Page-specific titles
- **Description**: Optimized descriptions
- **Images**: Page-specific or default logo

## Configuration

### Default SEO Settings (`config/seotools.php`)

```php
'defaults' => [
    'title' => 'پیشخوانک - استعلام هر آنچه که می خواهید!',
    'description' => 'پیشخوانک، مرجع جامع خدمات استعلام آنلاین...',
    'separator' => ' | ',
    'keywords' => ['استعلام آنلاین', 'کارت به شبا', '...'],
    'canonical' => 'current',
    'robots' => 'index, follow'
]
```

## Testing

### Routes Covered

1. **Static Pages**
   - `/` (Homepage)
   - `/about` (About Us)
   - `/contact` (Contact)
   - `/privacy-policy` (Privacy Policy)
   - `/terms-conditions` (Terms & Conditions)

2. **Service Pages**
   - `/services/category/{slug}` (Category pages)
   - `/services/{service}` (Individual services)
   - `/services/{parent}/{child}` (Child services)
   - `/services/result/{id}` (Result pages)

3. **Blog Pages**
   - `/blog` (Blog index)
   - `/blog/{post}` (Individual posts)

### Verification

To verify SEO implementation:

1. **View Source**: Check `<head>` section for meta tags
2. **Google Structured Data Tool**: Test JSON-LD schemas
3. **Facebook Debugger**: Verify Open Graph tags
4. **Twitter Card Validator**: Test Twitter sharing
5. **Lighthouse SEO**: Run Google Lighthouse audit

## Usage Examples

### Adding SEO to a New Controller

```php
use App\Traits\SeoTrait;

class NewController extends Controller
{
    use SeoTrait;
    
    public function show()
    {
        $this->setSeo([
            'title' => 'Custom Page Title',
            'description' => 'Page description here',
            'keywords' => ['keyword1', 'keyword2'],
            'breadcrumbs' => [
                ['name' => 'خانه', 'url' => route('app.page.home')],
                ['name' => 'Current Page']
            ]
        ]);
        
        return view('page.template');
    }
}
```

### Custom Structured Data

```php
use App\Helpers\SeoHelper;

// Add website schema
SeoHelper::addWebsiteSchema();

// Add mobile meta tags
SeoHelper::setMobileMeta();

// Generate keywords from content
$keywords = SeoHelper::generateKeywords($content, ['custom', 'keywords']);
```

## Maintenance

### Regular Updates
- Monitor Google Search Console for crawl errors
- Update meta descriptions based on performance
- Add new keywords for new services
- Keep structured data up to date

### Performance
- All SEO operations are efficient and cached where possible
- Minimal impact on page load times
- Structured data validates without errors

## Support

For questions or issues with SEO implementation:
1. Check the trait methods in `app/Traits/SeoTrait.php`
2. Review helper functions in `app/Helpers/SeoHelper.php`
3. Verify configuration in `config/seotools.php`
4. Test with online SEO tools

---

**Last Updated**: December 2024  
**Version**: 1.0.0  
**Package**: artesaos/seotools v1.3 