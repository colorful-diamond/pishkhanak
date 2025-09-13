---
description: Autonomous enterprise content generation with comprehensive FAQ system and AI intelligence
argument-hint: --service-id:ID keywords:"..." --ai-instructions:"..." --options
allowed-tools: "*"
---

# Enterprise Autonomous v3 Content Generation

Generate autonomous enterprise-level content with comprehensive FAQ system for: **$ARGUMENTS**

## Execution Instructions:

**MANDATORY BEFORE STARTING:**
- Use `TodoWrite` tool to create **10+ detailed todos** breaking down major phases
- Use `mcp__sequential-thinking__sequentialthinking` for planning and decision-making
- Use Serena MCP tools extensively throughout ALL phases

1. **Service Database Integration** (with Sequential Thinking & Serena):
   - **Sequential Thinking**: Analyze service requirements and database structure
   - **Serena**: Use `find_symbol` and `search_for_pattern` to understand existing service code
   - Parse `--service-id:ID` parameter if provided
   - Load service data from PostgreSQL database
   - Fetch parent-child service relationships using Serena project memory
   - Load existing keywords and meta information
   - **Sequential Thinking**: Plan proper sub-service directory structure

2. **AI Instructions Processing** (with Sequential Thinking & Serena):
   - **Sequential Thinking**: Analyze AI instructions for content requirements
   - **Serena**: Use project memory to understand reference patterns
   - Parse `--ai-instructions:"..."` parameter for context
   - Apply AI guidance throughout content generation
   - Customize tone, style, and target audience based on instructions
   - **Sequential Thinking**: Ensure content matches specified reference patterns

3. **Keyword-Focused URL Discovery & Research** (MANDATORY SCOPE + Thinking & Serena):
   - **Sequential Thinking**: Plan keyword research strategy for each provided keyword
   - **Serena**: Use `write_memory` to store research findings for session persistence
   - **ONLY search provided keywords** - do not expand beyond given terms
   - Use WebSearch tool EXCLUSIVELY for the exact keywords provided
   - Search each keyword individually and systematically
   - Extract ALL authoritative URLs found for each specific keyword
   - **Document every discovered URL** with the keyword that found it
   - **Sequential Thinking**: Validate URLs for relevance to the EXACT keywords only

4. **Keyword-Targeted Web Extraction** (with Sequential Thinking & Serena):
   - **Sequential Thinking**: Analyze and prioritize discovered URLs
   - **Serena**: Store extracted content in project memory for reference
   - Use WebFetch tool on URLs found through keyword searches ONLY
   - Process content that directly relates to provided keywords
   - Extract information relevant to the specific keywords provided
   - **Provide complete list of all extracted URLs** organized by keyword
   - **Sequential Thinking**: Compile research database focused ONLY on keyword-related content

5. **Enterprise Content Generation** (with Sequential Thinking & Serena):
   - **Sequential Thinking**: Plan content architecture and section organization
   - **Serena**: Use `get_symbols_overview` to analyze reference template structure
   - Generate 8,000+ words following reference design patterns
   - Create 12+ comprehensive sections with proper hierarchy
   - Apply exact visual design patterns from credit-score-rating
   - **Sequential Thinking**: Integrate all research findings and AI instructions
   - **Serena**: Store content progress in project memory
   - Use natural Persian keyword integration throughout

6. **Comprehensive FAQ System (MANDATORY)** (with Sequential Thinking & Serena):
   - **Sequential Thinking**: Plan FAQ categorization and content strategy
   - **Serena**: Analyze cheque-inquiry FAQ structure using `search_for_pattern`
   - **File Name**: Create `faqs.blade.php` (simplified, general filename)
   - **Design Pattern**: Copy EXACT design from cheque-inquiry comprehensive-faqs structure
   - **FAQ Count**: Generate 50+ FAQs minimum (target: 53-56 FAQs)
   - **Categories**: Organize into 8-9 categories with exact counts
   - **Sequential Thinking**: Design header with `bg-gradient-to-br from-purple-50 to-blue-50` gradient
   - **Search System**: Implement full search and category filtering
   - **Serena**: Use `write_memory` to document FAQ patterns for future use
   - **Integration**: Add @include to main content.blade.php file

7. **Sub-Service Structure Implementation** (with Sequential Thinking & Serena):
   - **Sequential Thinking**: Analyze optimal directory structure
   - **Serena**: Use `find_symbol` to understand existing service patterns
   - Create proper directory: `/custom/parent-service/child-service/`
   - **Primary Focus**: content.blade.php (main content file)
   - **FAQ File**: faqs.blade.php (separate FAQ file with simpler name)
   - **DO NOT MODIFY**: upper.blade.php, preview.blade.php (existing templates)
   - **Sequential Thinking**: Plan Laravel view resolution strategy
   - **Serena**: Test and validate parent-child service relationships

8. **Quality Validation (MANDATORY)** (with Sequential Thinking & Serena):
   - **Sequential Thinking**: Systematic validation approach for all components
   - **Serena**: Use `search_for_pattern` to validate code quality
   - Validate FAQ count: Use `grep -c "faq-item"` to ensure 50+ FAQs
   - Verify design compliance: Compare with cheque-inquiry reference
   - Test view resolution: Confirm all includes work properly
   - **Sequential Thinking**: Check mobile responsiveness and Persian RTL handling
   - **Serena**: Store validation results in project memory
   - Validate search and filtering functionality

## Special Parameters:

- `--service-id:ID` - Use database service ID for automatic data loading
- `--ai-instructions:"..."` - Detailed AI guidance for content customization
- `--auto-url-discovery` - Enable autonomous URL discovery
- `--playwright-research` - Use browser automation for research
- `--smart-web-extraction` - Advanced content extraction and analysis
- `--content-analysis-ai` - AI-powered content relevance scoring
- `--search-depth:N` - Research depth (1-5, default: 4)
- `--pages-per-site:N` - Pages to research per site (1-10, default: 6)
- `--comprehensive-faqs=N+` - Generate N+ FAQs with exact cheque-inquiry design
- `--all-advanced-features` - Enable all autonomous features

## FAQ Design Requirements (CRITICAL):

### Header Pattern (MANDATORY):
```html
<div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8 mb-8">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-bold text-dark-sky-700 mb-4 flex items-center justify-center gap-3">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            مرجع کامل سوالات متداول
        </h2>
        <p class="text-gray-700 text-lg leading-relaxed">
            بیش از <strong>[NUMBER] سوال و پاسخ تخصصی</strong> درباره [SERVICE_DESCRIPTION]
        </p>
    </div>
</div>
```

### Content Integration (MANDATORY):
```blade
{{-- Comprehensive FAQ Section - 50+ Searchable and Categorized Questions --}}
@include('front.services.custom.PARENT_SERVICE.CHILD_SERVICE.faqs')

<!-- Contact and Support -->
<section class="mt-12 mb-8">
    <div class="bg-gradient-to-br from-blue-50 to-sky-50 rounded-2xl p-8 text-center">
        <h2 class="text-2xl font-bold text-blue-800 mb-4">نیاز به راهنمایی بیشتر دارید؟</h2>
        <!-- Contact section content -->
    </div>
</section>
```

## Output:

Create complete sub-service structure in:
`resources/views/front/services/custom/[parent-service]/[child-service]/`

Including:
- ✅ **content.blade.php** - Main content with @include for FAQs
- ✅ **faqs.blade.php** - 50+ FAQs with cheque-inquiry design (simplified filename)
- ❌ **DO NOT MODIFY**: upper.blade.php, preview.blade.php (existing templates)
- ✅ **partials/** - Directory for reusable components if needed

## MANDATORY URL Documentation Output:

**Provide complete research URL inventory in structured format:**

```markdown
# KEYWORD RESEARCH URL INVENTORY

## Keyword: "[KEYWORD_1]"
### Discovered URLs:
1. [URL] - [Brief description]
2. [URL] - [Brief description]
3. [URL] - [Brief description]

## Keyword: "[KEYWORD_2]"
### Discovered URLs:
1. [URL] - [Brief description]
2. [URL] - [Brief description]

[Continue for all keywords...]

## Total URLs Discovered: [NUMBER]
## Total Keywords Researched: [NUMBER]
```

## Success Criteria:

- [ ] **TodoWrite**: 10+ detailed todos created and managed throughout execution
- [ ] **Sequential Thinking**: Used in all planning and decision-making phases
- [ ] **Serena MCP**: Extensively used in all phases for project understanding
- [ ] Service ID integration and database loading
- [ ] AI instructions properly applied with thinking validation
- [ ] Keyword-focused research completed with COMPLETE URL inventory provided
- [ ] 8,000+ words enterprise content generated
- [ ] 50+ comprehensive FAQs with exact cheque-inquiry design
- [ ] Proper sub-service directory structure
- [ ] All view resolutions tested and working with Serena validation
- [ ] Search and filtering functionality operational
- [ ] Mobile responsive and Persian RTL optimized
- [ ] All research URLs documented and organized by keyword

**MANDATORY EXECUTION PATTERN:**
1. Create 10+ TodoWrite items BEFORE starting
2. Use Sequential Thinking for ALL major decisions  
3. Use Serena MCP tools throughout ALL phases
4. Provide complete URL inventory organized by keyword
5. Validate using both thinking and Serena tools