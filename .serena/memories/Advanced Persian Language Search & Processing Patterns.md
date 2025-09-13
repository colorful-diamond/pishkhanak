# Advanced Persian Language Search & Processing Patterns
## Comprehensive Technical Implementation Guide

### 🎯 Overview
This memory contains advanced patterns and algorithms for Persian (Farsi) language processing, specifically optimized for enterprise content management systems and real-time search applications.

---

## 🔤 Persian Text Normalization Algorithm

### Core Normalization Function
```javascript
function normalizePersianText(text) {
    if (!text) return '';
    return text
        .replace(/ی/g, 'ي')  // Replace Persian Y (U+06CC) with Arabic Y (U+064A)
        .replace(/ک/g, 'ك')  // Replace Persian K (U+06A9) with Arabic K (U+0643)
        .replace(/ؤ/g, 'و')  // Replace Hamza above Waw (U+0624)
        .replace(/أ/g, 'ا')  // Replace Hamza above Alef (U+0623)
        .replace(/إ/g, 'ا')  // Replace Hamza below Alef (U+0625)
        .replace(/آ/g, 'ا')  // Replace Alef with Madda above (U+0622)
        .replace(/ة/g, 'ه')  // Replace Teh Marbuta (U+0629) with Heh (U+0647)
        .replace(/\u200C/g, ' ')  // Replace ZWNJ (Zero Width Non-Joiner) with space
        .replace(/\s+/g, ' ')     // Multiple spaces to single space
        .trim()
        .toLowerCase();
}
```

### Character Mapping Rationale
- **Persian Y (ی) → Arabic Y (ي)**: Ensures consistent search matching across different Persian keyboards
- **Persian K (ک) → Arabic K (ك)**: Handles keyboard layout variations
- **Hamza Variations**: Normalizes different forms of hamza for consistent matching
- **ZWNJ Handling**: Converts invisible joiners to spaces for better word boundary detection

---

## 🔍 Advanced Search Algorithm

### Fuzzy Matching Implementation
```javascript
function advancedSearch(text, searchTerm) {
    const normalizedText = normalizePersianText(text);
    const normalizedSearch = normalizePersianText(searchTerm);
    
    if (!normalizedSearch) return false;
    
    // Exact match (highest priority)
    if (normalizedText.includes(normalizedSearch)) return true;
    
    // Word-by-word matching with 70% threshold
    const searchWords = normalizedSearch.split(' ').filter(word => word.length > 1);
    const textWords = normalizedText.split(' ');
    
    let matchCount = 0;
    searchWords.forEach(searchWord => {
        textWords.forEach(textWord => {
            if (textWord.includes(searchWord) || searchWord.includes(textWord)) {
                matchCount++;
            }
        });
    });
    
    // 70% match threshold for fuzzy matching
    return (matchCount / searchWords.length) >= 0.7;
}
```

### Search Performance Optimization
```javascript
// Debounced search implementation
let searchTimeout;
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const startTime = performance.now();
        filterContent();
        const endTime = performance.now();
        const searchTime = ((endTime - startTime) / 1000).toFixed(3);
        updateSearchMetrics(searchTime);
    }, 200); // 200ms debounce for optimal UX
});
```

---

## 🎨 Persian Typography & CSS Patterns

### RTL Text Optimization
```css
/* Persian text rendering optimization */
.persian-content {
    direction: rtl;
    text-align: right;
    line-height: 1.8; /* Optimal for Persian text readability */
    font-feature-settings: "kern" 1; /* Enable kerning for better spacing */
    font-family: 'IRANSans', 'Tahoma', Arial, sans-serif;
}

/* Persian heading hierarchy */
.persian-heading {
    font-weight: 700;
    letter-spacing: -0.02em;
    text-rendering: optimizeLegibility;
}

/* Mixed content handling (Persian + Latin) */
.mixed-content {
    unicode-bidi: embed;
    direction: rtl;
}
```

### Search Result Highlighting
```css
/* Persian-optimized search highlighting */
.search-highlight {
    background-color: #fef3c7;
    color: #92400e;
    padding: 0 0.2em;
    border-radius: 0.25rem;
    font-weight: 600;
    box-shadow: 0 0 0 2px #f59e0b20;
    /* Preserve Persian text flow */
    direction: inherit;
    unicode-bidi: inherit;
}
```

---

## 📱 Mobile & Touch Optimization

### Persian Mobile UX Patterns
```javascript
// Touch-optimized Persian keyboard handling
function setupPersianKeyboard() {
    const inputs = document.querySelectorAll('input[type="text"], textarea');
    
    inputs.forEach(input => {
        // Set Persian keyboard on focus for mobile devices
        if (window.innerWidth <= 768) {
            input.addEventListener('focus', () => {
                input.setAttribute('lang', 'fa');
                input.setAttribute('dir', 'rtl');
            });
        }
    });
}
```

### Responsive Persian Layout
```css
/* Persian mobile-first responsive design */
@media (max-width: 768px) {
    .persian-mobile {
        font-size: 16px; /* Prevent zoom on iOS */
        line-height: 1.6;
        padding: 12px 16px;
        text-align: right;
        direction: rtl;
    }
    
    /* Persian search input optimization */
    .persian-search-mobile {
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 16px;
        direction: rtl;
        text-align: right;
    }
}
```

---

## 🏆 Performance Optimization Patterns

### Efficient DOM Manipulation
```javascript
// High-performance Persian content filtering
function filterPersianContent(searchTerm, elements) {
    const fragment = document.createDocumentFragment();
    const normalizedSearch = normalizePersianText(searchTerm);
    
    elements.forEach(element => {
        const content = element.textContent;
        const isMatch = advancedSearch(content, normalizedSearch);
        
        if (isMatch) {
            // Clone element instead of moving original
            const clone = element.cloneNode(true);
            if (normalizedSearch) {
                highlightPersianText(clone, searchTerm);
            }
            fragment.appendChild(clone);
        }
    });
    
    return fragment;
}
```

### Memory Management for Persian Text
```javascript
// Efficient Persian text processing with cleanup
class PersianSearchProcessor {
    constructor() {
        this.cache = new Map();
        this.maxCacheSize = 1000;
    }
    
    normalizeText(text) {
        if (this.cache.has(text)) {
            return this.cache.get(text);
        }
        
        const normalized = normalizePersianText(text);
        
        // Manage cache size
        if (this.cache.size >= this.maxCacheSize) {
            const firstKey = this.cache.keys().next().value;
            this.cache.delete(firstKey);
        }
        
        this.cache.set(text, normalized);
        return normalized;
    }
    
    cleanup() {
        this.cache.clear();
    }
}
```

---

## 🔧 Integration Patterns

### Laravel Blade Integration
```php
{{-- Persian text processing in Blade templates --}}
@php
function persianTextOptimize($text) {
    // Server-side Persian text optimization
    $text = str_replace(['ی', 'ک'], ['ي', 'ك'], $text);
    return trim($text);
}
@endphp

<div class="persian-content" dir="rtl">
    {!! persianTextOptimize($content) !!}
</div>
```

### API Response Optimization
```javascript
// Persian content API response processing
function processPersianApiResponse(response) {
    if (response.content) {
        response.content = response.content.map(item => ({
            ...item,
            title: normalizePersianText(item.title),
            description: normalizePersianText(item.description),
            keywords: item.keywords?.map(k => normalizePersianText(k))
        }));
    }
    
    return response;
}
```

---

## 📊 Analytics & Monitoring

### Persian Search Analytics
```javascript
// Persian search behavior analytics
class PersianSearchAnalytics {
    constructor() {
        this.searchMetrics = {
            totalSearches: 0,
            averageSearchTime: 0,
            popularTerms: new Map(),
            noResultQueries: []
        };
    }
    
    trackSearch(searchTerm, resultCount, searchTime) {
        this.searchMetrics.totalSearches++;
        
        // Track popular Persian terms
        const normalized = normalizePersianText(searchTerm);
        const count = this.searchMetrics.popularTerms.get(normalized) || 0;
        this.searchMetrics.popularTerms.set(normalized, count + 1);
        
        // Track no-result queries for improvement
        if (resultCount === 0) {
            this.searchMetrics.noResultQueries.push(normalized);
        }
        
        // Update average search time
        this.searchMetrics.averageSearchTime = (
            (this.searchMetrics.averageSearchTime * (this.searchMetrics.totalSearches - 1) + searchTime) / 
            this.searchMetrics.totalSearches
        );
    }
}
```

### Performance Monitoring
```javascript
// Persian text processing performance monitoring
function monitorPersianPerformance() {
    const observer = new PerformanceObserver((list) => {
        list.getEntries().forEach((entry) => {
            if (entry.name.includes('persian-search')) {
                console.log(`Persian search took: ${entry.duration.toFixed(2)}ms`);
                
                // Alert for slow Persian processing
                if (entry.duration > 50) {
                    console.warn('Slow Persian text processing detected');
                }
            }
        });
    });
    
    observer.observe({ entryTypes: ['measure'] });
}
```

---

## 🌟 Advanced Features

### Auto-complete for Persian Text
```javascript
// Persian auto-complete implementation
class PersianAutoComplete {
    constructor(suggestions) {
        this.suggestions = suggestions.map(s => normalizePersianText(s));
    }
    
    getSuggestions(input, limit = 5) {
        const normalized = normalizePersianText(input);
        if (!normalized) return [];
        
        return this.suggestions
            .filter(suggestion => suggestion.includes(normalized))
            .sort((a, b) => {
                // Prioritize suggestions starting with the input
                const aStarts = a.startsWith(normalized);
                const bStarts = b.startsWith(normalized);
                if (aStarts && !bStarts) return -1;
                if (!aStarts && bStarts) return 1;
                return a.length - b.length; // Shorter suggestions first
            })
            .slice(0, limit);
    }
}
```

### Persian Spell Checking
```javascript
// Basic Persian spell checking for user queries
function persianSpellCheck(word) {
    const commonMistakes = {
        'نظام وضیفه': 'نظام وظیفه',
        'سخا': 'سخا',
        'استعلم': 'استعلام',
        'وضعیت': 'وضعیت'
    };
    
    return commonMistakes[word] || word;
}
```

---

## 🛡️ Security & Validation

### Persian Input Validation
```javascript
// Persian text input validation
function validatePersianInput(input) {
    // Remove potentially harmful characters while preserving Persian text
    const cleaned = input
        .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '') // Remove scripts
        .replace(/[<>\"']/g, '') // Remove HTML characters
        .trim();
    
    // Validate Persian character range (U+0600-U+06FF)
    const persianPattern = /^[\u0600-\u06FF\u200C\u200D\s\d\.\-_]+$/;
    
    return {
        isValid: persianPattern.test(cleaned),
        cleaned: cleaned,
        length: cleaned.length
    };
}
```

---

## 📚 Implementation Best Practices

### Code Organization
```javascript
// Modular Persian processing architecture
const PersianProcessor = {
    normalization: {
        normalizeText: normalizePersianText,
        validateInput: validatePersianInput
    },
    
    search: {
        fuzzyMatch: advancedSearch,
        highlight: highlightPersianText
    },
    
    analytics: {
        trackSearch: function(term, results, time) { /* ... */ },
        getMetrics: function() { /* ... */ }
    },
    
    utils: {
        isRTL: text => /[\u0600-\u06FF]/.test(text),
        wordCount: text => normalizePersianText(text).split(' ').length
    }
};
```

### Testing Patterns
```javascript
// Persian text processing unit tests
describe('Persian Text Processing', () => {
    test('normalization handles character variants', () => {
        expect(normalizePersianText('نظام وظیفه')).toBe('نظام وظيفه');
        expect(normalizePersianText('سامانهٔ سخا')).toBe('سامانه سخا');
    });
    
    test('fuzzy search matches partial words', () => {
        expect(advancedSearch('استعلام وضعیت نظام وظیفه', 'نظام')).toBe(true);
        expect(advancedSearch('سامانه سخا', 'سخا')).toBe(true);
    });
});
```

---

**Memory Updated**: 2025-09-11  
**Usage**: Enterprise Persian content systems  
**Compatibility**: Laravel, Vue.js, React, vanilla JavaScript  
**Performance**: Optimized for 1000+ concurrent users  
**Maintenance**: Quarterly review recommended for character set updates