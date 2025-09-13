# Service ID 37 Analysis - Social Security Insurance Inquiry

## Database Information
- **ID**: 37
- **Title**: استعلام سوابق بیمه تامین اجتماعی (Social Security Insurance Records Inquiry)
- **Slug**: social-security-insurance-inquiry
- **Type**: Parent service (parent_id is null)
- **Database**: pishkhane (PostgreSQL)

## Service Structure Analysis
Based on the Service model analysis, this service:
- Uses Laravel service structure with parent-child relationships
- Has media collections (thumbnail, icon, images)
- Supports AI content (content can be numeric referencing AiContent model)
- Has schema, faqs, and related_articles JSON fields
- Supports comments and ratings system

## Directory Structure Planning
Since this is a parent service, the content should be placed in:
`resources/views/front/services/custom/social-security-insurance-inquiry/`

Files to create:
- content.blade.php (main enterprise content)
- faqs.blade.php (60+ comprehensive FAQs)
- Persian cultural and RTL considerations required

## Keywords for Research (14 total):
1. استعلام سوابق بیمه تامین اجتماعی
2. مشاهده سوابق بیمه  
3. کد دستوری سوابق بیمه
4. سامانه سوابق بیمه sabeghe.tamin.ir
5. کد بیمه شده
6. سابقه بیمه من
7. چند سال بیمه دارم
8. مشاهده سوابق کار
9. کنترل بیمه
10. بیمه چقدر شده
11. سوابق کاری
12. تاریخچه بیمه
13. پرداختی های بیمه
14. استعلام با کد ملی

## Research Strategy
- Keyword-only approach (NO semantic expansion)
- Focus on official Iranian social security system sources
- Target sabeghe.tamin.ir and related government portals
- Persian language content with cultural considerations