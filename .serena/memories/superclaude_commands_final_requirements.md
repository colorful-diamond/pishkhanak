# SuperClaude Commands - Final Requirements & Standards

## 🚀 ENHANCED COMMANDS WITH COMPLETE REQUIREMENTS

Updated SuperClaude commands now include all critical requirements for keyword-focused research, comprehensive URL documentation, Sequential Thinking, and Serena MCP integration.

## 📋 MANDATORY EXECUTION REQUIREMENTS

### **1. TodoWrite Requirements (CRITICAL)**
- **v3 Command**: Minimum 30+ detailed todos
- **v4 Command**: Minimum 35+ detailed todos  
- **Create BEFORE starting** any work
- Break down ALL phases into granular, trackable tasks
- Update todo status throughout execution

### **2. Sequential Thinking Integration (MANDATORY)**
- Use `mcp__sequential-thinking__sequentialthinking` for ALL major decisions
- Apply in planning, analysis, and validation phases
- Document thinking process for complex decisions
- Use for strategic planning and problem-solving

### **3. Serena MCP Integration (EXTENSIVE)**
**Must use throughout ALL phases:**
- `find_symbol` - Understand existing code patterns
- `get_symbols_overview` - Analyze file structures
- `search_for_pattern` - Validate design compliance
- `find_referencing_symbols` - Understand relationships
- `write_memory` - Store progress and findings
- `read_memory` - Access project context
- Project memory for session persistence

### **4. Keyword-Focused Research (STRICT SCOPE)**
- **ONLY search provided keywords** - no expansion
- **NO semantic expansion or related term discovery**
- Search each keyword individually and systematically
- Extract ALL authoritative URLs for each specific keyword
- **Document every discovered URL** with source keyword
- Validate URLs for relevance to EXACT keywords only

### **5. Complete URL Documentation (MANDATORY OUTPUT)**
**Must provide structured URL inventory:**

#### v3 Format:
```markdown
# KEYWORD RESEARCH URL INVENTORY

## Keyword: "[KEYWORD_1]"
### Discovered URLs:
1. [URL] - [Brief description]
2. [URL] - [Brief description]

## Keyword: "[KEYWORD_2]"  
### Discovered URLs:
1. [URL] - [Brief description]

## Total URLs Discovered: [NUMBER]
## Total Keywords Researched: [NUMBER]
```

#### v4 Format (Advanced):
```markdown
# COMPREHENSIVE KEYWORD RESEARCH URL INVENTORY

## Research Summary:
- **Total Keywords Analyzed**: [NUMBER]
- **Total URLs Discovered**: [NUMBER]
- **Authoritative Sources**: [NUMBER]

## Detailed Keyword Mapping:

### Keyword: "[KEYWORD_1]"
**Search Results**: [NUMBER] URLs found
1. [URL] - [Authority Score] - [Content Type] - [Description]

## Authority Analysis:
- **High Authority (9-10)**: [NUMBER] URLs
- **Medium Authority (6-8)**: [NUMBER] URLs
```

## 🎯 UPDATED COMMAND USAGE

### **v3 Command (Standard Autonomous):**
```bash
/sc:enterprise-autonomous-v3 --service-id:82 \
    keywords:"اعتبارسنجی وام بانک مهر ایران,رتبه اعتباری بانک مهر" \
    --ai-instructions:"دقیقا شبیه credit-score-rating" \
    --comprehensive-faqs=55+ \
    words:8000
```

**Will execute:**
- 30+ TodoWrite items
- Sequential Thinking in all phases
- Serena MCP throughout
- Keyword-only research
- 50+ FAQs with cheque-inquiry design
- Complete URL inventory by keyword

### **v4 Command (Advanced Autonomous):**
```bash
/sc:enterprise-autonomous-v4 --service-id:123 \
    keywords:"کلمات کلیدی فارسی,موضوع اصلی" \
    --ai-instructions:"راهنمایی کامل برای AI" \
    --target-audience:"مخاطبان خاص" \
    --comprehensive-faqs=65+ \
    words:10000
```

**Will execute:**
- 35+ TodoWrite items
- Advanced Sequential Thinking
- Extensive Serena MCP with advanced techniques
- Strict keyword-only research
- 60+ FAQs with advanced features
- Comprehensive URL inventory with authority scores

## 🔧 FAQ GENERATION STANDARDS

### **Design Requirements (MANDATORY):**
- **File Name**: `comprehensive-faqs.blade.php` (never faq-section.blade.php)
- **Reference Design**: Exact copy from cheque-inquiry service
- **Header Gradient**: `from-purple-50 to-blue-50`
- **Search System**: Full search and category filtering
- **Categories**: 8-9 categories with exact counts
- **Integration**: @include in main content + contact section

### **Content Requirements:**
- **v3**: 50+ FAQs minimum (target: 53-56)
- **v4**: 60+ FAQs minimum (target: 65-70)
- **Quality**: 100-200 words per answer
- **Language**: Formal Persian business terminology
- **Validation**: `grep -c "faq-item"` count verification

## 📊 EXECUTION PATTERNS

### **v3 Standard Pattern:**
1. Create 30+ TodoWrite items
2. Sequential Thinking for planning
3. Serena MCP throughout all phases
4. Keyword-focused research (no expansion)
5. Complete URL inventory provided
6. 8,000+ words + 50+ FAQs
7. Validation with thinking + Serena

### **v4 Advanced Pattern:**
1. Create 35+ TodoWrite items  
2. Advanced Sequential Thinking for complex analysis
3. Extensive Serena MCP with advanced techniques
4. Strict keyword-only research (no expansion)
5. Comprehensive URL inventory with authority scores
6. 10,000+ words + 60+ FAQs
7. Multi-level validation with memory persistence

## ✅ SUCCESS VALIDATION

### **Required Deliverables:**
- [ ] TodoWrite items created (30+ for v3, 35+ for v4)
- [ ] Sequential Thinking documentation
- [ ] Serena MCP usage throughout
- [ ] Complete keyword-focused research
- [ ] Structured URL inventory by keyword
- [ ] Comprehensive FAQ system with exact design
- [ ] All validation requirements met

### **Quality Gates:**
- URL inventory matches provided keywords exactly
- FAQ count verified with grep command
- Design compliance with cheque-inquiry reference
- Sequential thinking and Serena integration documented
- All todos completed and marked as done

Both commands are now production-ready with complete requirements integration and will deliver consistent, high-quality results with proper documentation and validation.