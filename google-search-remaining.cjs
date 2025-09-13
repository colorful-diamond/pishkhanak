const { chromium } = require('playwright');
const fs = require('fs');

// Configuration
const PROXY_PORTS = [8001, 8002, 8003, 8004, 8005, 8006, 8007, 8008, 8009, 8010, 8011, 8012];
const DELAY_MIN = 3000;
const DELAY_MAX = 7000;
const MAX_RETRIES = 3;
const USER_AGENTS = [
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
];

// Remaining 25 services to search
const REMAINING_SERVICES = [
    // Vehicle Services (15)
    { title: 'دریافت تصویر تخلفات رانندگی', slug: 'traffic-violation-image', category: 'Vehicle' },
    { title: 'لیست پلاک‌های فعال با کدملی', slug: 'active-plates-list', category: 'Vehicle' },
    { title: 'استعلام عوارض آزادراهی با شماره پلاک', slug: 'toll-road-inquiry', category: 'Vehicle' },
    { title: 'استعلام تطبیق مالکیت و نوع خودرو', slug: 'vehicle-ownership-inquiry', category: 'Vehicle' },
    { title: 'استعلام خلافی موتور', slug: 'motor-violation-inquiry', category: 'Vehicle' },
    { title: 'استعلام ریسک رانندگان با کدملی', slug: 'driver-risk-inquiry', category: 'Vehicle' },
    { title: 'تاریخچه پلاک با کدملی و شماره پلاک', slug: 'plate-history-inquiry', category: 'Vehicle' },
    { title: 'استعلام اطلاعات خودرو و تخفیفات بیمه', slug: 'car-information-and-insurance-discounts', category: 'Vehicle' },
    { title: 'جریمه طرح ترافیک', slug: 'shahrdarikhodro', category: 'Vehicle' },
    { title: 'کارت و سند خودرو', slug: 'cardocuments', category: 'Vehicle' },
    { title: 'مالیات انتقال خودرو', slug: 'cartax', category: 'Vehicle' },
    { title: 'مالیات انتقال موتور', slug: 'motortransfertax', category: 'Vehicle' },
    { title: 'معاینه فنی', slug: 'technicalexaminationcertificate', category: 'Vehicle' },
    { title: 'تاریخچه پلاک', slug: 'car-plate-history', category: 'Vehicle' },
    { title: 'سوابق خودرو', slug: 'carauthenticity', category: 'Vehicle' },
    
    // Insurance Services (3)
    { title: 'استعلام سوابق بیمه تامین اجتماعی', slug: 'social-security-insurance-inquiry', category: 'Insurance' },
    { title: 'اعتبار دفترچه بیمه', slug: 'insurancecard', category: 'Insurance' },
    { title: 'بیمه‌نامه‌', slug: 'insurancepolicy', category: 'Insurance' },
    
    // Social Services (3)
    { title: 'حکم تامین اجتماعی', slug: 'hokmmostamari', category: 'Social' },
    { title: 'حکم بازنشستگان کشوری', slug: 'hokmbazneshasteghi', category: 'Social' },
    { title: 'کالابرگ الکترونیکی', slug: 'electronic-subsidy', category: 'Social' },
    
    // KYC Services (4)
    { title: 'معافیت تحصیلی', slug: 'study-exemption', category: 'KYC' },
    { title: 'اجاره نامه ملک', slug: 'rentalcontractinquiry', category: 'KYC' },
    { title: 'سیم‌کارت‌های به‌نام', slug: 'my-simcart', category: 'KYC' },
    { title: 'دریافت آدرس با کد‌پستی', slug: 'postal-address-lookup', category: 'KYC' }
];

class RemainingSearchBot {
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
        
        // Check for different slug
        const serviceMatch = results.find(r => 
            r.url.includes('/services/') && 
            !r.url.includes(service.slug)
        );
        
        if (serviceMatch) {
            const urlParts = serviceMatch.url.split('/services/');
            const actualSlug = urlParts[1] ? urlParts[1].split('/')[0] : '';
            
            return {
                indexed: true,
                exactMatch: false,
                slugMismatch: true,
                currentSlug: service.slug,
                actualSlug: actualSlug,
                url: serviceMatch.url,
                text: serviceMatch.text
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
        console.log(`🚀 Starting Google search for REMAINING ${REMAINING_SERVICES.length} services`);
        console.log(`🔄 Using ${PROXY_PORTS.length} rotating proxies`);
        
        const startTime = Date.now();
        
        for (let i = 0; i < REMAINING_SERVICES.length; i++) {
            const service = REMAINING_SERVICES[i];
            console.log(`\n📈 Progress: ${i + 1}/${REMAINING_SERVICES.length} (${Math.round((i + 1) / REMAINING_SERVICES.length * 100)}%)`);
            
            await this.searchService(service);
            
            // Sleep between requests (except for last one)
            if (i < REMAINING_SERVICES.length - 1) {
                await this.sleep();
            }
        }
        
        const endTime = Date.now();
        const duration = Math.round((endTime - startTime) / 1000);
        
        console.log(`\n🎉 REMAINING search completed in ${duration} seconds!`);
        
        // Save results
        await this.saveResults();
        
        // Generate summary
        this.generateSummary();
    }

    async saveResults() {
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
        const resultsFile = `google-search-remaining-${timestamp}.json`;
        
        const data = {
            timestamp: new Date().toISOString(),
            totalServices: REMAINING_SERVICES.length,
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
        console.log('🎯 REMAINING SERVICES SEARCH SUMMARY');
        console.log('='.repeat(60));
        
        const indexed = this.results.filter(r => r.analysis.indexed);
        const exactMatches = this.results.filter(r => r.analysis.exactMatch);
        const slugMismatches = this.results.filter(r => r.analysis.slugMismatch);
        
        console.log(`📊 Total Services Searched: ${REMAINING_SERVICES.length}`);
        console.log(`✅ Services Found Indexed: ${indexed.length}`);
        console.log(`🎯 Exact Slug Matches: ${exactMatches.length}`);
        console.log(`🔄 Slug Mismatches: ${slugMismatches.length}`);
        console.log(`❌ Errors: ${this.errors.length}`);
        
        if (indexed.length > 0) {
            console.log('\n📋 NEWLY FOUND INDEXED SERVICES:');
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
            console.log('\n🔧 ADDITIONAL SLUG UPDATES NEEDED:');
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
    const bot = new RemainingSearchBot();
    await bot.runSearch();
}

if (require.main === module) {
    main().catch(console.error);
}

module.exports = RemainingSearchBot;