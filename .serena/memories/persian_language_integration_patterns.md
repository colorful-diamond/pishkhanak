# Persian Language Integration Patterns for Financial Services

## Persian Keywords Successfully Integrated
Original 15 Persian keywords for Iranian license plate inquiry services:

1. **استعلام پلاک فعال** - Active plate inquiry
2. **پلاک فعال با کد ملی** - Active plate with national ID  
3. **فک پلاک** - Plate detachment
4. **پلاک‌های فعال من** - My active plates
5. **تعداد پلاک به نام** - Number of plates registered
6. **استعلام پلاک غیرفعال** - Inactive plate inquiry
7. **پلاک فک شده** - Detached plates
8. **استعلام پلاک با کد ملی** - Plate inquiry with national ID
9. **مشاهده پلاک‌های فعال** - View active plates
10. **وضعیت پلاک** - Plate status
11. **پلاک‌های بنام** - Plates registered to name
12. **تاریخ فک پلاک** - Plate detachment date
13. **محل فک پلاک** - Plate detachment location
14. **سریال پلاک راهور** - Traffic police plate serial
15. **پلاک انتظامی** - Military/police plates

## Integration Statistics
- **Total Keyword Instances**: 170+ occurrences across content and FAQ files
- **Content Distribution**: Each keyword featured in multiple contexts
- **FAQ Keywords**: All keywords integrated into relevant FAQ data-keywords attributes

## RTL (Right-to-Left) Support Patterns
```html
<!-- Text alignment for Persian -->
class="text-right"          # 63 instances
class="text-center"         # For headings and emphasis
class="leading-relaxed"     # Better Persian text readability

<!-- Flex direction for RTL -->
class="flex items-center justify-between"
class="space-x-reverse"     # Proper spacing in RTL context
```

## Persian Typography Best Practices
```html
<!-- Font weights appropriate for Persian -->
class="font-semibold"       # Good for Persian headings
class="font-bold"          # For important emphasis
class="text-lg"            # Readable size for Persian text

<!-- Persian-friendly spacing -->
class="leading-relaxed"     # Line height for Persian readability
class="space-y-4"          # Vertical spacing between Persian paragraphs
```

## Content Structure for Persian Services
1. **Hero Section**: Bilingual service introduction
2. **Keyword Sections**: Dedicated section per Persian keyword
3. **FAQ Categories**: Persian category names with English data attributes
4. **Visual Elements**: Persian-appropriate icons and colors

## Search Integration for Persian
```javascript
// Persian keyword search support
const itemKeywords = item.dataset.keywords.toLowerCase();
const itemText = item.textContent.toLowerCase();

// Supports both Persian and transliterated search terms
const matchesSearch = searchTerm === '' || 
                    itemKeywords.includes(searchTerm) || 
                    itemText.includes(searchTerm);
```

## Persian Financial Services Context
- **Domain**: Iranian government services (راهور - Traffic Police)
- **User Base**: Persian-speaking citizens  
- **Cultural Context**: Formal government service language
- **Legal Terms**: Integration of Iranian legal and administrative terminology

## Color Psychology for Persian Services  
- **Green**: Trust and government services (primary)
- **Blue**: Information and inquiry processes
- **Red**: Important warnings and detachment processes
- **Purple**: Premium features and special services

## Performance in Persian Context
- **Character Encoding**: UTF-8 for proper Persian display
- **Font Support**: Standard web fonts with Persian character support  
- **Text Rendering**: Proper Persian/Arabic script rendering
- **Search Performance**: Efficient Persian text searching

## Reusable Persian Patterns
1. **RTL Layout**: Consistent right-to-left alignment
2. **Persian Headings**: Proper hierarchy with Persian typography
3. **Keyword Integration**: Strategic placement throughout content
4. **Search Support**: Multi-language search functionality
5. **Cultural Appropriateness**: Government service tone and terminology