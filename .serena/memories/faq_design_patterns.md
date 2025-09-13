# FAQ Design Patterns for Enterprise Services

## Service Analysis for ID 34 (Liveness Inquiry)
- **Service Name**: استعلام وضعیت حیات (Life Status Inquiry)
- **Slug**: liveness-inquiry  
- **Type**: Standalone service (no sub-services)
- **Directory Structure**: `custom/liveness-inquiry/`

## Reference Design Pattern (cheque-inquiry/comprehensive-faqs.blade.php)

### HTML Structure:
```html
<section class="mt-12 mb-12" id="comprehensive-faqs">
    <!-- Category Filter Buttons -->
    <button class="faq-category-btn active px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium transition-colors" data-category="all">

    <!-- Search Input -->
    <input type="text" id="faq-search" class="search-input">
    
    <!-- FAQ Items -->
    <div class="faq-item p-6" data-category="inquiry" data-keywords="استعلام چک صیادی چیست تعریف">
        <div class="faq-question cursor-pointer flex justify-between items-start">
            <h4>Question Text</h4>
            <div class="faq-chevron">↓</div>
        </div>
        <div class="faq-answer mt-4 hidden">
            <p>Answer Content</p>
        </div>
    </div>
</section>
```

### Key Features:
1. **Category System**: Multiple category buttons with active states
2. **Search Functionality**: Real-time search with keyword matching
3. **Accordion Style**: Expandable FAQ items with chevron icons
4. **Data Attributes**: `data-category` and `data-keywords` for filtering
5. **Results Counter**: Shows number of matching results

### Required Files for Service ID 34:
1. `content.blade.php` - Main enterprise content (12,000+ words)
2. `faqs.blade.php` - Comprehensive FAQ system (60+ FAQs)

### Categories for Life Inquiry FAQs:
1. Basic Inquiry (استعلام پایه)
2. Legal Requirements (الزامات قانونی)  
3. Government Integration (تطابق دولتی)
4. Technical Process (فرآیند فنی)
5. Documentation (مستندات)
6. Costs & Fees (هزینه‌ها)
7. Troubleshooting (رفع مشکل)
8. Security & Privacy (امنیت و حریم خصوصی)
9. Advanced Features (قابلیت‌های پیشرفته)
10. Support & Contact (پشتیبانی و تماس)