# Playwright MCP Web Research Architecture

## Advanced Web Research Integration

### Service Model Extension for Custom Input
```php
// Enhanced Service Configuration
class PremiumServiceContentConfig extends Model
{
    protected $fillable = [
        'service_name',
        'custom_keywords',           // User input: "keyword1,keyword2,keyword3"
        'research_urls',            // User input: "url1,url2,url3"  
        'target_word_count',        // 6000+
        'keyword_sections_count',   // 4 dedicated sections
        'research_depth',           // shallow|medium|deep
        'citation_style',           // apa|mla|custom
        'industry_context',         // banking|insurance|finance
        'language_preference'       // persian|bilingual
    ];
    
    // Web research processing method
    public function executeWebResearch()
    {
        return [
            'playwright_navigation' => $this->research_urls,
            'keyword_analysis' => $this->custom_keywords,
            'content_extraction' => 'deep_analysis',
            'data_processing' => 'persian_optimization',
            'citation_generation' => $this->citation_style
        ];
    }
}
```

## Playwright MCP Research Pipeline

### Web Data Extraction Workflow
```javascript
// Comprehensive Web Research System
const webResearchSystem = {
    
    // Phase 1: Intelligent URL Navigation
    async navigateAndExtract(urls, keywords) {
        const extractedData = [];
        
        for (const url of urls) {
            try {
                // Navigate with error handling
                await page.goto(url, { waitUntil: 'networkidle' });
                
                // Extract comprehensive data
                const pageData = await page.evaluate((keywords) => {
                    return {
                        title: document.title,
                        headings: [...document.querySelectorAll('h1,h2,h3,h4')]
                            .map(h => h.textContent.trim()),
                        
                        // Keyword-relevant content extraction
                        relevantParagraphs: [...document.querySelectorAll('p')]
                            .filter(p => keywords.some(k => 
                                p.textContent.toLowerCase().includes(k.toLowerCase())
                            ))
                            .map(p => p.textContent.trim()),
                        
                        // Statistical data extraction
                        statistics: [...document.querySelectorAll('[class*="stat"], [class*="number"], [class*="percent"]')]
                            .map(el => ({
                                value: el.textContent.trim(),
                                context: el.closest('div')?.textContent.trim()
                            })),
                        
                        // Table data extraction
                        tables: [...document.querySelectorAll('table')]
                            .map(table => ({
                                headers: [...table.querySelectorAll('th')].map(th => th.textContent.trim()),
                                rows: [...table.querySelectorAll('tr')].slice(1).map(tr => 
                                    [...tr.querySelectorAll('td')].map(td => td.textContent.trim())
                                )
                            })),
                        
                        // Link and reference extraction
                        authorityLinks: [...document.querySelectorAll('a[href*="gov"], a[href*="bank"], a[href*="official"]')]
                            .map(link => ({
                                text: link.textContent.trim(),
                                url: link.href
                            })),
                        
                        // Meta information
                        lastUpdated: document.querySelector('[datetime], [class*="date"], [class*="updated"]')?.textContent,
                        author: document.querySelector('[class*="author"], [rel="author"]')?.textContent,
                        domain: window.location.hostname,
                        extractedAt: new Date().toISOString()
                    };
                }, keywords);
                
                // Take screenshot for verification
                await page.screenshot({
                    path: `research_${new Date().getTime()}.png`,
                    fullPage: true
                });
                
                extractedData.push({
                    url,
                    data: pageData,
                    status: 'success'
                });
                
            } catch (error) {
                extractedData.push({
                    url,
                    error: error.message,
                    status: 'failed'
                });
            }
        }
        
        return extractedData;
    },
    
    // Phase 2: Advanced Content Analysis
    async analyzeExtractedContent(extractedData, keywords) {
        return {
            // Keyword relevance scoring
            keywordAnalysis: keywords.map(keyword => ({
                keyword,
                frequency: this.calculateKeywordFrequency(extractedData, keyword),
                contexts: this.findKeywordContexts(extractedData, keyword),
                sentiment: this.analyzeKeywordSentiment(extractedData, keyword)
            })),
            
            // Statistical data compilation
            statistics: this.compileStatistics(extractedData),
            
            // Authority source validation
            sourceCredibility: this.validateSources(extractedData),
            
            // Competitive analysis
            competitorInsights: this.analyzeCompetitors(extractedData),
            
            // Content quality assessment
            contentQuality: this.assessContentQuality(extractedData)
        };
    },
    
    // Phase 3: Persian Content Generation
    async generatePersianContent(analysisResults, keywords) {
        return {
            // 4 keyword sections (500-600 words each)
            keywordSections: keywords.map(keyword => ({
                title: `تحلیل جامع ${keyword} - بررسی بازار و آمار`,
                content: this.generateKeywordSection(
                    keyword, 
                    analysisResults, 
                    600 // target word count
                ),
                citations: this.generateCitations(keyword, analysisResults),
                statistics: this.extractRelevantStats(keyword, analysisResults)
            })),
            
            // Additional research-backed sections
            marketAnalysis: this.generateMarketAnalysis(analysisResults),
            trendAnalysis: this.generateTrendAnalysis(analysisResults),
            expertInsights: this.generateExpertInsights(analysisResults)
        };
    }
};
```

## Real-Time Research Command Examples

### Financial Services Research
```bash
/sc:create-premium-service تسهیلات-بانکی \
  --keywords="وام مسکن,تسهیلات قرض الحسنه,سود بانکی,ضمانت نامه" \
  --research-urls="https://cbi.ir,https://sei.ir,https://bankrate-iran.com" \
  --6000-words \
  --playwright-deep-research \
  --citations=academic
```

### Insurance Market Analysis
```bash
/sc:create-premium-service بیمه-درمان \
  --keywords="بیمه تکمیلی,پوشش درمانی,حق بیمه,شبکه درمان" \
  --research-urls="https://insurance.ir,https://health-insurance-data.gov,https://medical-network.ir" \
  --6000-words \
  --real-time-stats \
  --competitive-analysis
```

## Advanced Research Features

### 1. **Real-Time Data Integration**
```javascript
// Live data extraction and processing
const realTimeData = {
    currentRates: await extractFromCBI(),
    marketTrends: await analyzeMarketData(),
    competitorPricing: await compareServices(),
    regulatoryUpdates: await fetchRegulations()
};
```

### 2. **Authority Citation System**
```markdown
## Automated Citation Generation
- **Source**: Central Bank of Iran (cbi.ir)
- **Data**: Current interest rates and lending policies
- **Date Accessed**: [Real-time timestamp]
- **Relevance Score**: 95/100
- **Credibility Rating**: Government Authority
```

### 3. **Statistical Data Compilation**
```javascript
// Intelligent statistics processing
const statisticalAnalysis = {
    marketSize: extractMarketData(),
    growthRates: calculateTrends(),
    competitorComparison: benchmarkAnalysis(),
    userBehaviorData: analyzeUserMetrics()
};
```

## Enhanced Workflow Execution

### Complete Research Pipeline (12-15 minutes)
```
[1/15] 🌐 Playwright Web Research Initialization
├─ URL validation and accessibility check
├─ Research strategy optimization
├─ Keyword clustering and prioritization
└─ ✅ Research environment ready

[2/15] 🔍 Deep Web Data Extraction  
├─ Multi-site navigation and content extraction
├─ Statistical data compilation
├─ Authority source validation
├─ Screenshot capture for verification
└─ ✅ Raw data extracted and verified

[3/15] 📊 Advanced Data Analysis
├─ Keyword relevance scoring
├─ Content quality assessment
├─ Competitive intelligence analysis
├─ Market trend identification
└─ ✅ Analysis complete with insights

[4/15] ✍️ 6000+ Word Content Generation
├─ 4 keyword sections with web research
├─ 8 additional comprehensive sections
├─ Statistical integration and citations
├─ Persian language optimization
└─ ✅ Premium content generated

[5/15] 🎨 Professional Design Implementation
├─ Enhanced visual design system
├─ Research-backed infographics
├─ Data visualization charts
├─ Interactive elements integration
└─ ✅ Visual ecosystem complete
```

## Quality Assurance with Web Research

### ✅ **Premium Research Standards**
- **Source Verification**: Multi-source cross-validation
- **Data Accuracy**: Real-time statistics and information
- **Authority Citations**: Government and institutional sources
- **Competitive Analysis**: Market positioning insights
- **Technical Validation**: Industry-standard compliance
- **Persian Excellence**: Advanced linguistic and cultural accuracy

**Result**: 6,000+ word premium content ecosystem with real-time web research integration and authority-backed citations.