/**
 * Browser Stealth Utilities
 * Comprehensive stealth configuration for browser automation
 */

/**
 * HTTP Proxy configuration for your server IPs via local proxies
 */
const SERVER_HTTP_PROXIES = [
    { server: 'http://127.0.0.1:8001', expectedIP: '109.206.254.170' },
    { server: 'http://127.0.0.1:8002', expectedIP: '109.206.254.171' },
    { server: 'http://127.0.0.1:8003', expectedIP: '109.206.254.172' },
    { server: 'http://127.0.0.1:8004', expectedIP: '109.206.254.173' }
];

/**
 * Get random HTTP proxy from your server pool
 */
export function getRandomHttpProxy() {
    return SERVER_HTTP_PROXIES[Math.floor(Math.random() * SERVER_HTTP_PROXIES.length)];
}

/**
 * Get all available HTTP proxies
 */
export function getAllHttpProxies() {
    return SERVER_HTTP_PROXIES;
}


/**
 * Generate random user agent from popular browsers
 */
export function getRandomUserAgent() {
    const userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ];
    return userAgents[Math.floor(Math.random() * userAgents.length)];
}

/**
 * Generate random viewport from common screen resolutions
 */
export function getRandomViewport() {
    const viewports = [
        { width: 1920, height: 1080 },
        { width: 1366, height: 768 },
        { width: 1536, height: 864 },
        { width: 1440, height: 900 },
        { width: 1280, height: 720 },
        { width: 1600, height: 900 },
        { width: 1024, height: 768 }
    ];
    return viewports[Math.floor(Math.random() * viewports.length)];
}

/**
 * Get comprehensive stealth browser launch arguments
 */
export function getStealthLaunchArgs(userAgent = null) {
    const args = [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-web-security',
        '--disable-features=VizDisplayCompositor',
        '--disable-background-timer-throttling',
        '--disable-backgrounding-occluded-windows',
        '--disable-renderer-backgrounding',
        '--disable-field-trial-config',
        '--disable-ipc-flooding-protection',
        '--enable-features=NetworkService,NetworkServiceInProcess',
        '--disable-extensions',
        '--disable-plugins',
        '--disable-default-apps',
        '--disable-sync',
        '--disable-translate',
        '--hide-scrollbars',
        '--mute-audio',
        '--no-first-run',
        '--disable-client-side-phishing-detection',
        '--disable-component-update',
        '--disable-dev-shm-usage',
        '--no-default-browser-check',
        '--autoplay-policy=user-gesture-required',
        '--disable-background-networking',
        '--disable-device-discovery-notifications',
        '--disable-gpu-sandbox',
        '--disable-features=TranslateUI',
        '--disable-hang-monitor',
        '--disable-prompt-on-repost',
        '--disable-domain-reliability',
        '--disable-features=AudioServiceOutOfProcess',
        '--disable-blink-features=AutomationControlled',
        '--exclude-switches=enable-automation',
        '--disable-logging',
        '--disable-login-animations',
        '--disable-notifications',
        '--disable-permissions-api',
        '--disable-session-crashed-bubble',
        '--disable-infobars'
    ];

    if (userAgent) {
        args.push(`--user-agent=${userAgent}`);
    }

    return args;
}

/**
 * Get stealth context configuration with optional HTTP proxy
 */
export function getStealthContextConfig(userAgent = null, viewport = null, useProxy = false) {
    const config = {
        userAgent: userAgent || getRandomUserAgent(),
        viewport: viewport || getRandomViewport(),
        locale: 'fa-IR',
        timezoneId: 'Asia/Tehran',
        permissions: [],
        geolocation: { latitude: 35.6892, longitude: 51.3890 }, // Tehran coordinates
        colorScheme: 'light',
        reducedMotion: 'no-preference',
        forcedColors: 'none',
        extraHTTPHeaders: {
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language': 'fa-IR,fa;q=0.9,en-US;q=0.8,en;q=0.7',
            'Accept-Encoding': 'gzip, deflate, br',
            'Cache-Control': 'max-age=0',
            'Sec-Fetch-Dest': 'document',
            'Sec-Fetch-Mode': 'navigate',
            'Sec-Fetch-Site': 'none',
            'Sec-Fetch-User': '?1',
            'Upgrade-Insecure-Requests': '1'
        }
    };

    if (useProxy) {
        const proxy = getRandomHttpProxy();
        config.proxy = { server: proxy.server };
        console.log('🌐 [STEALTH] Using HTTP proxy:', proxy.server);
        console.log('🎯 [STEALTH] Expected public IP:', proxy.expectedIP);
        console.log('🔄 [STEALTH] HTTP proxy rotation enabled');
    } else {
        console.log('🌐 [STEALTH] Using direct connection (no proxy)');
        console.log('🎭 [STEALTH] Enhanced stealth mode enabled');
    }

    return config;
}

/**
 * Setup comprehensive stealth configuration for a page
 */
export async function setupStealthMode(page) {
    // Remove automation indicators and override navigator properties
    await page.addInitScript(() => {
        // Remove webdriver property
        Object.defineProperty(navigator, 'webdriver', {
            get: () => undefined,
        });

        // Remove automation indicators
        delete window.cdc_adoQpoasnfa76pfcZLmcfl_Array;
        delete window.cdc_adoQpoasnfa76pfcZLmcfl_Promise;
        delete window.cdc_adoQpoasnfa76pfcZLmcfl_Symbol;

        // Override plugins to look more realistic
        Object.defineProperty(navigator, 'plugins', {
            get: () => [
                {
                    0: {
                        type: "application/x-google-chrome-pdf",
                        suffixes: "pdf",
                        description: "Portable Document Format",
                        enabledPlugin: Plugin
                    },
                    description: "Portable Document Format",
                    filename: "internal-pdf-viewer",
                    length: 1,
                    name: "Chrome PDF Plugin"
                },
                {
                    0: {
                        type: "application/pdf",
                        suffixes: "pdf",
                        description: "",
                        enabledPlugin: Plugin
                    },
                    description: "",
                    filename: "mhjfbmdgcfjbbpaeojofohoefgiehjai",
                    length: 1,
                    name: "Chrome PDF Viewer"
                }
            ],
        });

        // Override languages
        Object.defineProperty(navigator, 'languages', {
            get: () => ['fa-IR', 'fa', 'en-US', 'en'],
        });

        // Override platform
        Object.defineProperty(navigator, 'platform', {
            get: () => 'Win32',
        });

        // Override hardware properties
        Object.defineProperty(navigator, 'hardwareConcurrency', {
            get: () => 4,
        });

        Object.defineProperty(navigator, 'deviceMemory', {
            get: () => 8,
        });

        // Override permissions API
        const originalQuery = window.navigator.permissions.query;
        window.navigator.permissions.query = (parameters) => (
            parameters.name === 'notifications' ?
                Promise.resolve({ state: Notification.permission }) :
                originalQuery(parameters)
        );

        // Override canvas fingerprinting
        const originalGetContext = HTMLCanvasElement.prototype.getContext;
        HTMLCanvasElement.prototype.getContext = function(type) {
            if (type === '2d') {
                const context = originalGetContext.apply(this, arguments);
                const originalFillText = context.fillText;
                context.fillText = function() {
                    // Add slight randomization to text rendering
                    arguments[1] += Math.random() * 0.1;
                    arguments[2] += Math.random() * 0.1;
                    return originalFillText.apply(this, arguments);
                };
                return context;
            }
            return originalGetContext.apply(this, arguments);
        };

        // Override WebGL fingerprinting
        const originalGetParameter = WebGLRenderingContext.prototype.getParameter;
        WebGLRenderingContext.prototype.getParameter = function(parameter) {
            if (parameter === 37445) {
                return 'Intel Inc.';
            }
            if (parameter === 37446) {
                return 'Intel(R) Iris(R) Pro Graphics 6200';
            }
            return originalGetParameter.apply(this, arguments);
        };

        // Override screen properties
        Object.defineProperty(screen, 'width', { get: () => 1920 });
        Object.defineProperty(screen, 'height', { get: () => 1080 });
        Object.defineProperty(screen, 'availWidth', { get: () => 1920 });
        Object.defineProperty(screen, 'availHeight', { get: () => 1040 });
        Object.defineProperty(screen, 'colorDepth', { get: () => 24 });
        Object.defineProperty(screen, 'pixelDepth', { get: () => 24 });

        // Override Date to prevent timezone detection inconsistencies
        const originalDate = Date;
        Date = function(...args) {
            if (args.length === 0) {
                return new originalDate();
            }
            return new originalDate(...args);
        };
        Date.prototype = originalDate.prototype;
        Date.now = originalDate.now;
        Date.parse = originalDate.parse;
        Date.UTC = originalDate.UTC;

        // Override chrome runtime
        if (!window.chrome) {
            window.chrome = {};
        }
        
        if (!window.chrome.runtime) {
            window.chrome.runtime = {
                onConnect: undefined,
                onMessage: undefined
            };
        }

        // Override Notification API
        const originalNotification = window.Notification;
        Object.defineProperty(window, 'Notification', {
            get: () => originalNotification,
            set: () => {}
        });
    });

    // Set realistic page properties
    await page.setExtraHTTPHeaders({
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language': 'fa-IR,fa;q=0.9,en-US;q=0.8,en;q=0.7',
        'Accept-Encoding': 'gzip, deflate, br',
        'Cache-Control': 'max-age=0',
        'Sec-Fetch-Dest': 'document',
        'Sec-Fetch-Mode': 'navigate',
        'Sec-Fetch-Site': 'none',
        'Sec-Fetch-User': '?1',
        'Upgrade-Insecure-Requests': '1'
    });
}

/**
 * Add random mouse movements to mimic human behavior
 */
export async function addRandomMouseMovements(page) {
    try {
        // Generate random mouse movements
        const movements = Math.floor(Math.random() * 3) + 2; // 2-4 movements
        
        for (let i = 0; i < movements; i++) {
            const x = Math.floor(Math.random() * 800) + 100;
            const y = Math.floor(Math.random() * 600) + 100;
            
            await page.mouse.move(x, y, { steps: Math.floor(Math.random() * 5) + 5 });
            await page.waitForTimeout(100 + Math.random() * 300);
        }
    } catch (error) {
        // Ignore mouse movement errors silently
        console.log('🐭 Mouse movement simulation completed');
    }
}

/**
 * Human-like typing with realistic delays
 */
export async function humanType(page, selector, text, options = {}) {
    const baseDelay = options.delay || 50;
    const variance = options.variance || 50;
    
    // Click on the element first
    await page.click(selector);
    await page.waitForTimeout(100 + Math.random() * 200);
    
    // Clear existing content if specified
    if (options.clear) {
        await page.fill(selector, '');
        await page.waitForTimeout(50 + Math.random() * 100);
    }
    
    // Type with realistic delays
    await page.type(selector, text, { 
        delay: baseDelay + Math.random() * variance 
    });
}

/**
 * Human-like form submission
 */
export async function humanSubmit(page, submitSelector) {
    // Add some random mouse movements before submitting
    await addRandomMouseMovements(page);
    await page.waitForTimeout(200 + Math.random() * 500);
    
    // Hover over submit button first
    await page.hover(submitSelector);
    await page.waitForTimeout(100 + Math.random() * 200);
    
    // Click the submit button
    await page.click(submitSelector);
}

/**
 * Random delay with human-like timing
 */
export async function humanDelay(page, minMs = 500, maxMs = 2000) {
    const delay = minMs + Math.random() * (maxMs - minMs);
    await page.waitForTimeout(delay);
}

/**
 * Generate random scroll behavior
 */
export async function randomScroll(page) {
    try {
        const scrollCount = Math.floor(Math.random() * 3) + 1; // 1-3 scrolls
        
        for (let i = 0; i < scrollCount; i++) {
            const scrollY = Math.floor(Math.random() * 300) + 100;
            await page.evaluate((y) => window.scrollBy(0, y), scrollY);
            await page.waitForTimeout(200 + Math.random() * 500);
        }
    } catch (error) {
        // Ignore scroll errors
    }
}