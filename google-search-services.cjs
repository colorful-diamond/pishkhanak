const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

// Configuration
const PROXY_PORTS = [8001, 8002, 8003, 8004, 8005, 8006, 8007, 8008, 8009, 8010, 8011, 8012];
const DELAY_MIN = 3000; // 3 seconds minimum delay
const DELAY_MAX = 7000; // 7 seconds maximum delay
const MAX_RETRIES = 3;
const USER_AGENTS = [
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
];

// Services to search (the 59 remaining services)
const SERVICES_TO_SEARCH = [
    // Banking Services
    { title: 'استعلام رنگ چک با کد ملی', slug: 'check-color', category: 'Banking' },
    { title: 'استعلام و دریافت شماره شهاب', slug: 'shahab-number', category: 'Banking' },
    { title: 'تبدیل شماره حساب به شبا', slug: 'account-iban', category: 'Banking' },
    { title: 'استعلام اعتبار و محکومیت مالی', slug: 'financial-judgment-inquiry', category: 'Banking' },
    { title: 'استعلام چک در راه', slug: 'coming-check-inquiry', category: 'Banking' },
    { title: 'استعلام ضمانت وام با کدملی', slug: 'loan-guarantee-inquiry', category: 'Banking' },
    { title: 'استعلام و بررسی شماره شبا', slug: 'iban-check', category: 'Banking' },
    { title: 'استعلام چک برگشتی با کدملی و شناسه صیاد', slug: 'cheque-inquiery', category: 'Banking' },
    { title: 'اعتبار معاملاتی افراد', slug: 'transaction-check', category: 'Banking' },
    { title: 'تبدیل شماره کارت به شماره حساب', slug: 'card-account', category: 'Banking' },
    
    // Vehicle Services (top 10 most likely to be indexed)
    { title: 'خلافی خودرو', slug: 'khalafi', category: 'Vehicle' },
    { title: 'خلافی موتورسیکلت', slug: 'khalafimotor', category: 'Vehicle' },
    { title: 'عوارض آزادراهی', slug: 'freeway', category: 'Vehicle' },
    { title: 'نمره منفی گواهینامه', slug: 'nomremanfi', category: 'Vehicle' },
    { title: 'پلاک‌های فعال', slug: 'platenumber', category: 'Vehicle' },
    { title: 'استعلام خلافی خودرو کلی و جزئی', slug: 'car-violation-inquiry', category: 'Vehicle' },
    { title: 'استعلام نمره منفی گواهینامه', slug: 'negative-license-score', category: 'Vehicle' },
    { title: 'استعلام وضعیت گواهینامه با کدملی', slug: 'driving-license-status', category: 'Vehicle' },
    { title: 'عوارض سالیانه خودرو', slug: 'avarezkhodro', category: 'Vehicle' },
    { title: 'گواهینامه رانندگی', slug: 'drivinglicence', category: 'Vehicle' },
    
    // KYC Services (most likely government services)
    { title: 'گذرنامه', slug: 'passport', category: 'KYC' },
    { title: 'استعلام وضعیت گذرنامه', slug: 'passport-status-inquiry', category: 'KYC' },
    { title: 'پیگیری کارت ملی', slug: 'national-id-card', category: 'KYC' },
    { title: 'استعلام اتباع خارجی', slug: 'expats-inquiries', category: 'KYC' },
    { title: 'استعلام وضعیت حیات', slug: 'liveness-inquiry', category: 'KYC' },
    { title: 'استعلام ممنوع الخروجی', slug: 'inquiry-exit-ban', category: 'KYC' },
    { title: 'کارت پایان خدمت', slug: 'military-card', category: 'KYC' },
    { title: 'پیگیری مرسوله پستی', slug: 'posttracking', category: 'KYC' },
    
    // Insurance Services
    { title: 'بیمه شخص ثالث', slug: 'bimesales', category: 'Insurance' },
    { title: 'استعلام سوابق بیمه نامه شخص ثالث', slug: 'third-party-insurance-history', category: 'Insurance' },
    { title: 'سوابق بیمه', slug: 'tamin', category: 'Insurance' },
    
    // Social Services
    { title: 'یارانه', slug: 'yaraneh', category: 'Social' },
    { title: 'فیش تامین اجتماعی', slug: 'fishmostamari', category: 'Social' },
    { title: 'نسخه تامین اجتماعی', slug: 'electronicprescribe', category: 'Social' }
];

class GoogleSearchBot {
    constructor() {
        this.proxyIndex = 0;
        this.results = [];
        this.errors = [];
    }

    getRandomProxy() {
        const port = PROXY_PORTS[this.proxyIndex % PROXY_PORTS.length];
        this.proxyIndex++;
        return { server: `127.0.0.1:${port}` };
    }

    getRandomUserAgent() {
        return USER_AGENTS[Math.floor(Math.random() * USER_AGENTS.length)];
    }

    async sleep(min = DELAY_MIN, max = DELAY_MAX) {
        const delay = Math.floor(Math.random() * (max - min + 1)) + min;
        console.log(`⏱️  Sleeping for ${delay}ms...`);
        return new Promise(resolve => setTimeout(resolve, delay));
    }

    async searchService(service, attempt = 1) {
        let browser = null;
        try {
            console.log(`\n🔍 [Attempt ${attempt}] Searching: ${service.title} (${service.category})`);
            console.log(`🌐 Expected URL: https://pishkhanak.com/services/${service.slug}`);
            
            const proxy = this.getRandomProxy();
            console.log(`🔄 Using proxy: ${proxy.server}`);
            
            browser = await chromium.launch({
                headless: true,
                proxy: proxy,
                args: [
                    '--no-sandbox',
                    '--disable-setuid-sandbox',
                    '--disable-dev-shm-usage',
                    '--disable-accelerated-2d-canvas',
                    '--no-first-run',
                    '--disable-gpu'
                ]
            });

            const context = await browser.newContext({
                userAgent: this.getRandomUserAgent()
            });
            const page = await context.newPage();
            
            // Set viewport
            await page.setViewportSize({ width: 1366, height: 768 });
            
            // Search query
            const query = `site:pishkhanak.com "${service.title}"`;
            const searchUrl = `https://www.google.com/search?q=${encodeURIComponent(query)}&hl=en`;
            
            console.log(`🔍 Query: ${query}`);
            
            // Navigate to Google search
            await page.goto(searchUrl, { waitUntil: 'networkidle', timeout: 30000 });
            
            // Wait for results
            await page.waitForTimeout(2000);
            
            // Extract search results
            const results = await page.evaluate(() => {
                const resultLinks = [];
                
                // Try different selectors for Google search results
                const selectors = [
                    'div[data-async-context] a[href*="pishkhanak.com"]',
                    'a[href*="pishkhanak.com"]',
                    '.g a[href*="pishkhanak.com"]',
                    '[data-ved] a[href*="pishkhanak.com"]'
                ];
                
                for (const selector of selectors) {
                    const links = document.querySelectorAll(selector);
                    for (const link of links) {
                        const href = link.href;
                        const text = link.innerText || '';
                        if (href && href.includes('pishkhanak.com') && !href.includes('google.com')) {
                            resultLinks.push({ url: href, text: text.trim() });
                        }
                    }
                    if (resultLinks.length > 0) break;
                }
                
                return resultLinks;
            });
            
            console.log(`✅ Found ${results.length} results`);
            
            // Analyze results
            const analysis = this.analyzeResults(service, results);
            
            this.results.push({
                service,
                results,
                analysis,
                timestamp: new Date().toISOString(),
                attempt
            });
            
            console.log(`📊 Analysis: ${JSON.stringify(analysis, null, 2)}`);
            
            return analysis;
            
        } catch (error) {
            console.error(`❌ Error searching ${service.title}:`, error.message);
            
            this.errors.push({
                service,
                error: error.message,
                attempt,
                timestamp: new Date().toISOString()
            });
            
            if (attempt < MAX_RETRIES) {
                console.log(`🔄 Retrying in ${DELAY_MAX}ms...`);
                await this.sleep(DELAY_MAX, DELAY_MAX * 2);
                return this.searchService(service, attempt + 1);
            }
            
            return { indexed: false, error: error.message };
            
        } finally {
            if (browser) {
                await browser.close();
            }
        }
    }

    analyzeResults(service, results) {
        if (results.length === 0) {
            return { indexed: false, reason: 'No results found' };
        }
        
        const expectedUrl = `https://pishkhanak.com/services/${service.slug}`;
        const exactMatch = results.find(r => r.url === expectedUrl);
        
        if (exactMatch) {
            return { 
                indexed: true, 
                exactMatch: true, 
                url: exactMatch.url, 
                text: exactMatch.text 
            };
        }
        
        // Look for service URLs (not bank-specific)
        const serviceMatch = results.find(r => 
            r.url.includes('/services/') && 
            !r.url.includes('/services/') + service.slug + '/' &&
            r.url === `https://pishkhanak.com/services/${service.slug}`
        );
        
        if (serviceMatch) {
            return {
                indexed: true,
                exactMatch: false,
                url: serviceMatch.url,
                text: serviceMatch.text,
                reason: 'Service URL found but different format'
            };
        }
        
        // Check for different slug
        const differentSlugMatch = results.find(r => 
            r.url.includes('/services/') && 
            !r.url.includes(service.slug)
        );
        
        if (differentSlugMatch) {
            const urlParts = differentSlugMatch.url.split('/services/');
            const actualSlug = urlParts[1] ? urlParts[1].split('/')[0] : '';
            
            return {
                indexed: true,
                exactMatch: false,
                slugMismatch: true,
                currentSlug: service.slug,
                actualSlug: actualSlug,
                url: differentSlugMatch.url,
                text: differentSlugMatch.text
            };
        }
        
        return {
            indexed: true,
            exactMatch: false,
            url: results[0].url,
            text: results[0].text,
            allResults: results,
            reason: 'Found results but no exact service match'
        };
    }

    async runSearch() {
        console.log(`🚀 Starting Google search for ${SERVICES_TO_SEARCH.length} services`);
        console.log(`🔄 Using ${PROXY_PORTS.length} rotating proxies`);
        
        const startTime = Date.now();
        
        for (let i = 0; i < SERVICES_TO_SEARCH.length; i++) {
            const service = SERVICES_TO_SEARCH[i];
            console.log(`\n📈 Progress: ${i + 1}/${SERVICES_TO_SEARCH.length} (${Math.round((i + 1) / SERVICES_TO_SEARCH.length * 100)}%)`);
            
            await this.searchService(service);
            
            // Sleep between requests (except for last one)
            if (i < SERVICES_TO_SEARCH.length - 1) {
                await this.sleep();
            }
        }
        
        const endTime = Date.now();
        const duration = Math.round((endTime - startTime) / 1000);
        
        console.log(`\n🎉 Search completed in ${duration} seconds!`);
        
        // Save results
        await this.saveResults();
        
        // Generate summary
        this.generateSummary();
    }

    async saveResults() {
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
        const resultsFile = `google-search-results-${timestamp}.json`;
        
        const data = {
            timestamp: new Date().toISOString(),
            totalServices: SERVICES_TO_SEARCH.length,
            totalResults: this.results.length,
            totalErrors: this.errors.length,
            results: this.results,
            errors: this.errors
        };
        
        fs.writeFileSync(resultsFile, JSON.stringify(data, null, 2));
        console.log(`💾 Results saved to: ${resultsFile}`);
    }

    generateSummary() {
        console.log('\n' + '='.repeat(60));
        console.log('🎯 GOOGLE SEARCH SUMMARY');
        console.log('='.repeat(60));
        
        const indexed = this.results.filter(r => r.analysis.indexed);
        const exactMatches = this.results.filter(r => r.analysis.exactMatch);
        const slugMismatches = this.results.filter(r => r.analysis.slugMismatch);
        
        console.log(`📊 Total Services Searched: ${SERVICES_TO_SEARCH.length}`);
        console.log(`✅ Services Found Indexed: ${indexed.length}`);
        console.log(`🎯 Exact Slug Matches: ${exactMatches.length}`);
        console.log(`🔄 Slug Mismatches: ${slugMismatches.length}`);
        console.log(`❌ Errors: ${this.errors.length}`);
        
        if (indexed.length > 0) {
            console.log('\n📋 INDEXED SERVICES:');
            indexed.forEach((result, i) => {
                console.log(`${i + 1}. ${result.service.title}`);
                console.log(`   URL: ${result.analysis.url}`);
                console.log(`   Match: ${result.analysis.exactMatch ? 'EXACT' : 'DIFFERENT'}`);
                if (result.analysis.slugMismatch) {
                    console.log(`   Current Slug: ${result.analysis.currentSlug}`);
                    console.log(`   Actual Slug: ${result.analysis.actualSlug}`);
                }
                console.log('');
            });
        }
        
        if (slugMismatches.length > 0) {
            console.log('\n🔧 SLUG UPDATES NEEDED:');
            slugMismatches.forEach((result, i) => {
                console.log(`${i + 1}. ${result.service.title}`);
                console.log(`   Current: ${result.analysis.currentSlug}`);
                console.log(`   Should be: ${result.analysis.actualSlug}`);
                console.log('');
            });
        }
        
        console.log('='.repeat(60));
    }
}

// Run the search
async function main() {
    const bot = new GoogleSearchBot();
    await bot.runSearch();
}

// Handle errors
process.on('unhandledRejection', (reason, promise) => {
    console.error('Unhandled Rejection at:', promise, 'reason:', reason);
});

// Run if called directly
if (require.main === module) {
    main().catch(console.error);
}

module.exports = GoogleSearchBot;