# Justice Shares Service Analysis - Service ID 436

## Service Database Information
- **ID**: 436
- **Slug**: `justice-stock-value-inquiry`
- **Title**: `ارزش سهام عدالت` (Justice Shares Value)
- **Description**: `سرویس ارزش سهام عدالت در پیشخوانک` (Justice Shares Value Service in Pishkhonak)
- **Parent ID**: NULL (top-level service)
- **Category ID**: 11
- **Active**: true

## Current Implementation Status
- **Existing Files**: Only `upper.blade.php` (basic form template)
- **Directory**: `resources/views/front/services/custom/justice-stock-value-inquiry/`
- **Form Fields**: national-code-field, mobile-field
- **Missing**: content.blade.php, comprehensive-faqs.blade.php

## Target Keywords (Persian)
استعلام سهام عدالت، استعلام سهام با کد ملی، مشاهده سهام عدالت، ارزش سهام عدالت، قیمت سهام عدالت امروز، فروش سهام عدالت، سود سهام عدالت، سامانه سهام عدالت، پرتفوی سهام عدالت، آزادسازی سهام عدالت

## Reference Design Pattern (from cheque-inquiry)
- **Header**: `bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8`
- **Title Structure**: Icon + Persian title + description
- **FAQ Target**: 60+ FAQs (reference had 53)
- **Categories**: Multiple categories with counts
- **Search System**: Advanced search with filtering
- **Content Length**: 12,000+ words

## Enterprise Requirements
- Advanced search functionality
- Multi-level categorization (10+ categories)
- Cultural appropriateness for Iranian financial services
- SEO optimization with Persian keywords
- RTL text handling
- Professional financial terminology

## Next Steps
1. Parse Persian keywords for strict research
2. Execute auto-URL discovery for each keyword
3. Perform smart web extraction
4. Create comprehensive content.blade.php (12,000+ words)
5. Generate faqs.blade.php (60+ FAQs with advanced features)