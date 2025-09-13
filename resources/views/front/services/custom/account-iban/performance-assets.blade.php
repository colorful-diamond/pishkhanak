{{-- Performance Assets Optimization for Account to IBAN Service --}}
{{-- Usage: @include('front.services.custom.account-iban.performance-assets') --}}

{{-- Critical CSS - Inline for First Paint Optimization --}}
<style>
/* Critical Above-the-fold Styles */
body{font-family:Tahoma,Arial,sans-serif;margin:0;padding:0;background-color:#f8fafc;direction:rtl}
.hero-section{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:2rem 1rem;text-align:center;min-height:60vh;display:flex;align-items:center;justify-content:center}
.hero-title{font-size:2.5rem;font-weight:700;margin-bottom:1rem}
.hero-subtitle{font-size:1.25rem;opacity:0.9;margin-bottom:2rem}
.form-container{background:#fff;border-radius:1rem;padding:2rem;box-shadow:0 10px 25px rgba(0,0,0,0.1);max-width:600px;margin:0 auto}
.btn-primary{background:#3b82f6;color:#fff;padding:0.75rem 2rem;border-radius:0.5rem;border:none;font-weight:600;cursor:pointer;transition:all 0.2s}
.btn-primary:hover{background:#2563eb;transform:translateY(-1px)}
.loading{display:none}

/* Persian Font Loading Optimization */
@font-face{font-family:'IRANSans';font-display:swap;src:url('/fonts/IRANSans-Light.woff2') format('woff2');font-weight:300}
@font-face{font-family:'IRANSans';font-display:swap;src:url('/fonts/IRANSans-Regular.woff2') format('woff2');font-weight:400}
@font-face{font-family:'IRANSans';font-display:swap;src:url('/fonts/IRANSans-Bold.woff2') format('woff2');font-weight:700}
</style>

{{-- Resource Preloading for Performance --}}
<link rel="preload" href="/fonts/IRANSans-Regular.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/fonts/IRANSans-Bold.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/css/tailwind-rtl.css" as="style">
<link rel="preload" href="/js/account-iban-converter.js" as="script">

{{-- DNS Prefetch for External Resources --}}
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="//api.pishkhanak.com">

{{-- Preconnect for Critical External Resources --}}
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://api.pishkhanak.com">

{{-- Optimized CSS Loading --}}
<link rel="stylesheet" href="/css/tailwind-rtl.css" media="print" onload="this.media='all'; this.onload=null;">
<noscript><link rel="stylesheet" href="/css/tailwind-rtl.css"></noscript>

<script>
// Performance Assets Manager
window.PishkhanakPerformance = {
    // Configuration
    config: {
        lazyLoadThreshold: 100,
        imagePlaceholderQuality: 20,
        fontLoadTimeout: 3000,
        analyticsDelay: 2000
    },
    
    // Persian Font Loading Optimization
    fontLoader: {
        loadedFonts: new Set(),
        
        init: function() {
            this.loadCriticalFonts();
            this.setupFontLoadingFallback();
            this.monitorFontLoading();
        },
        
        loadCriticalFonts: function() {
            // Load critical Persian fonts with fallback
            const criticalFonts = ['IRANSans-Regular', 'IRANSans-Bold'];
            
            criticalFonts.forEach(fontName => {
                if ('fonts' in document) {
                    const fontFace = new FontFace(
                        'IRANSans', 
                        `url(/fonts/${fontName}.woff2) format('woff2')`,
                        { display: 'swap' }
                    );
                    
                    fontFace.load().then(font => {
                        document.fonts.add(font);
                        this.loadedFonts.add(fontName);
                        console.log(`✓ Persian font loaded: ${fontName}`);
                    }).catch(error => {
                        console.warn(`✗ Font loading failed: ${fontName}`, error);
                        this.useFallbackFont();
                    });
                } else {
                    this.useFallbackFont();
                }
            });
        },
        
        setupFontLoadingFallback: function() {
            setTimeout(() => {
                if (this.loadedFonts.size === 0) {
                    console.warn('⚠ Persian fonts loading timeout, using fallback');
                    this.useFallbackFont();
                }
            }, this.parent.config.fontLoadTimeout);
        },
        
        useFallbackFont: function() {
            document.body.style.fontFamily = 'Tahoma, Arial, sans-serif';
        },
        
        monitorFontLoading: function() {
            if ('fonts' in document) {
                document.fonts.ready.then(() => {
                    console.log('✓ All Persian fonts loaded successfully');
                    this.parent.metrics.recordFontLoadTime();
                });
            }
        }
    },
    
    // Lazy Loading Manager
    lazyLoader: {
        observer: null,
        
        init: function() {
            this.setupIntersectionObserver();
            this.setupImageLazyLoading();
            this.setupContentLazyLoading();
        },
        
        setupIntersectionObserver: function() {
            if ('IntersectionObserver' in window) {
                this.observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.loadElement(entry.target);
                            this.observer.unobserve(entry.target);
                        }
                    });
                }, {
                    rootMargin: `${this.parent.config.lazyLoadThreshold}px`
                });
            } else {
                console.warn('IntersectionObserver not supported, loading all content immediately');
                this.loadAllContent();
            }
        },
        
        setupImageLazyLoading: function() {
            // Native lazy loading support
            const images = document.querySelectorAll('img[data-src]');
            images.forEach(img => {
                if (this.observer) {
                    this.observer.observe(img);
                } else {
                    this.loadImage(img);
                }
            });
        },
        
        setupContentLazyLoading: function() {
            const lazyContent = document.querySelectorAll('.lazy-content');
            lazyContent.forEach(content => {
                if (this.observer) {
                    this.observer.observe(content);
                } else {
                    this.loadContent(content);
                }
            });
        },
        
        loadElement: function(element) {
            if (element.tagName === 'IMG') {
                this.loadImage(element);
            } else {
                this.loadContent(element);
            }
        },
        
        loadImage: function(img) {
            const src = img.dataset.src;
            if (src) {
                // Create loading placeholder
                this.showImagePlaceholder(img);
                
                const image = new Image();
                image.onload = () => {
                    img.src = src;
                    img.classList.add('loaded');
                    this.hideImagePlaceholder(img);
                };
                image.onerror = () => {
                    img.classList.add('error');
                    this.showImageError(img);
                };
                image.src = src;
            }
        },
        
        loadContent: function(content) {
            content.classList.add('loading');
            
            // Simulate content loading (replace with actual async content loading)
            setTimeout(() => {
                content.classList.remove('loading');
                content.classList.add('loaded');
            }, 300);
        },
        
        showImagePlaceholder: function(img) {
            img.style.background = 'linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%)';
            img.style.backgroundSize = '200% 100%';
            img.style.animation = 'loading-shimmer 1.5s infinite';
        },
        
        hideImagePlaceholder: function(img) {
            img.style.background = 'none';
            img.style.animation = 'none';
        },
        
        showImageError: function(img) {
            img.alt = 'خطا در بارگذاری تصویر';
            img.style.background = '#f3f4f6';
            img.style.border = '2px dashed #d1d5db';
        },
        
        loadAllContent: function() {
            // Fallback for browsers without IntersectionObserver
            const images = document.querySelectorAll('img[data-src]');
            const content = document.querySelectorAll('.lazy-content');
            
            images.forEach(img => this.loadImage(img));
            content.forEach(c => this.loadContent(c));
        }
    },
    
    // Performance Metrics and Monitoring
    metrics: {
        startTime: performance.now(),
        metrics: {},
        
        init: function() {
            this.recordPageLoadMetrics();
            this.setupPerformanceObserver();
            this.monitorUserInteraction();
        },
        
        recordPageLoadMetrics: function() {
            window.addEventListener('load', () => {
                // Core Web Vitals
                this.recordLCP(); // Largest Contentful Paint
                this.recordFID(); // First Input Delay  
                this.recordCLS(); // Cumulative Layout Shift
                
                // Additional metrics
                this.recordTTFB(); // Time to First Byte
                this.recordFCP();  // First Contentful Paint
                
                // Persian-specific metrics
                this.recordPersianFontLoadTime();
                this.recordRTLLayoutTime();
                
                console.log('📊 Performance metrics recorded:', this.metrics);
            });
        },
        
        recordLCP: function() {
            if ('PerformanceObserver' in window) {
                new PerformanceObserver((entryList) => {
                    const entries = entryList.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    this.metrics.lcp = lastEntry.startTime;
                }).observe({ entryTypes: ['largest-contentful-paint'] });
            }
        },
        
        recordFID: function() {
            if ('PerformanceObserver' in window) {
                new PerformanceObserver((entryList) => {
                    const entries = entryList.getEntries();
                    entries.forEach(entry => {
                        this.metrics.fid = entry.processingStart - entry.startTime;
                    });
                }).observe({ entryTypes: ['first-input'] });
            }
        },
        
        recordCLS: function() {
            let clsScore = 0;
            if ('PerformanceObserver' in window) {
                new PerformanceObserver((entryList) => {
                    const entries = entryList.getEntries();
                    entries.forEach(entry => {
                        if (!entry.hadRecentInput) {
                            clsScore += entry.value;
                        }
                    });
                    this.metrics.cls = clsScore;
                }).observe({ entryTypes: ['layout-shift'] });
            }
        },
        
        recordTTFB: function() {
            const navigationEntry = performance.getEntriesByType('navigation')[0];
            if (navigationEntry) {
                this.metrics.ttfb = navigationEntry.responseStart - navigationEntry.requestStart;
            }
        },
        
        recordFCP: function() {
            if ('PerformanceObserver' in window) {
                new PerformanceObserver((entryList) => {
                    const entries = entryList.getEntries();
                    entries.forEach(entry => {
                        if (entry.name === 'first-contentful-paint') {
                            this.metrics.fcp = entry.startTime;
                        }
                    });
                }).observe({ entryTypes: ['paint'] });
            }
        },
        
        recordPersianFontLoadTime: function() {
            if ('fonts' in document) {
                const fontLoadStart = performance.now();
                document.fonts.ready.then(() => {
                    this.metrics.persianFontLoadTime = performance.now() - fontLoadStart;
                });
            }
        },
        
        recordRTLLayoutTime: function() {
            const layoutStart = performance.now();
            // Measure RTL layout calculation time
            requestAnimationFrame(() => {
                this.metrics.rtlLayoutTime = performance.now() - layoutStart;
            });
        },
        
        recordFontLoadTime: function() {
            this.metrics.totalFontLoadTime = performance.now() - this.parent.startTime;
        },
        
        setupPerformanceObserver: function() {
            if ('PerformanceObserver' in window) {
                try {
                    const observer = new PerformanceObserver((list) => {
                        list.getEntries().forEach((entry) => {
                            // Log long tasks (>50ms)
                            if (entry.duration > 50) {
                                console.warn(`⚠ Long task detected: ${entry.duration.toFixed(2)}ms`);
                            }
                        });
                    });
                    observer.observe({ entryTypes: ['longtask'] });
                } catch (e) {
                    console.warn('Performance Observer not fully supported');
                }
            }
        },
        
        monitorUserInteraction: function() {
            let interactionStart;
            
            ['click', 'touchstart', 'keydown'].forEach(eventType => {
                document.addEventListener(eventType, (e) => {
                    interactionStart = performance.now();
                }, { passive: true });
            });
            
            ['click', 'touchend', 'keyup'].forEach(eventType => {
                document.addEventListener(eventType, (e) => {
                    if (interactionStart) {
                        const interactionTime = performance.now() - interactionStart;
                        if (interactionTime > 100) {
                            console.warn(`⚠ Slow interaction: ${interactionTime.toFixed(2)}ms`);
                        }
                    }
                }, { passive: true });
            });
        },
        
        getMetricsReport: function() {
            return {
                ...this.metrics,
                totalPageLoadTime: performance.now() - this.parent.startTime,
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent,
                language: 'fa-IR',
                service: 'account-to-iban-converter'
            };
        }
    },
    
    // Resource Loading Optimization
    resourceLoader: {
        init: function() {
            this.optimizeScriptLoading();
            this.setupServiceWorker();
            this.enableResourceHints();
        },
        
        optimizeScriptLoading: function() {
            // Load non-critical scripts asynchronously
            const scripts = [
                '/js/analytics.js',
                '/js/social-share.js',
                '/js/advanced-features.js'
            ];
            
            scripts.forEach(scriptSrc => {
                this.loadScriptAsync(scriptSrc);
            });
        },
        
        loadScriptAsync: function(src) {
            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        },
        
        setupServiceWorker: function() {
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw-account-iban.js')
                        .then(registration => {
                            console.log('✓ Service Worker registered:', registration);
                        })
                        .catch(error => {
                            console.warn('✗ Service Worker registration failed:', error);
                        });
                });
            }
        },
        
        enableResourceHints: function() {
            // Prefetch likely next pages
            const prefetchUrls = ['/services', '/faq', '/contact'];
            
            prefetchUrls.forEach(url => {
                const link = document.createElement('link');
                link.rel = 'prefetch';
                link.href = url;
                document.head.appendChild(link);
            });
        }
    },
    
    // Persian-specific Optimizations
    persianOptimizations: {
        init: function() {
            this.optimizeRTLLayout();
            this.optimizePersianTextRendering();
            this.setupPersianInputOptimization();
        },
        
        optimizeRTLLayout: function() {
            // Prevent layout shifts during RTL rendering
            document.documentElement.setAttribute('dir', 'rtl');
            document.documentElement.setAttribute('lang', 'fa');
            
            // Optimize RTL text direction
            const textElements = document.querySelectorAll('input, textarea, [contenteditable]');
            textElements.forEach(element => {
                element.setAttribute('dir', 'auto');
            });
        },
        
        optimizePersianTextRendering: function() {
            // Enable proper Persian text shaping
            const style = document.createElement('style');
            style.textContent = `
                * {
                    text-rendering: optimizeLegibility;
                    font-feature-settings: 'kern' 1, 'liga' 1;
                }
                
                [lang="fa"], [dir="rtl"] {
                    font-feature-settings: 'kern' 1, 'liga' 1, 'calt' 1;
                }
            `;
            document.head.appendChild(style);
        },
        
        setupPersianInputOptimization: function() {
            // Optimize Persian input fields
            const inputs = document.querySelectorAll('input[type="text"], textarea');
            inputs.forEach(input => {
                // Set Persian keyboard
                input.setAttribute('lang', 'fa');
                
                // Optimize for Persian text input
                input.addEventListener('input', this.optimizePersianInput.bind(this));
            });
        },
        
        optimizePersianInput: function(event) {
            const input = event.target;
            const value = input.value;
            
            // Convert Persian numbers to English for form processing
            if (input.dataset.convertNumbers === 'true') {
                const englishNumbers = value.replace(/[۰-۹]/g, (match) => {
                    return String.fromCharCode(match.charCodeAt(0) - '۰'.charCodeAt(0) + '0'.charCodeAt(0));
                });
                input.value = englishNumbers;
            }
        }
    },
    
    // Main initialization
    init: function() {
        console.log('🚀 Initializing Pishkhanak Performance Manager');
        
        // Initialize all modules
        this.fontLoader.init();
        this.lazyLoader.init();
        this.metrics.init();
        this.resourceLoader.init();
        this.persianOptimizations.init();
        
        // Setup performance monitoring
        this.setupPerformanceMonitoring();
        
        console.log('✅ Performance Manager initialized successfully');
    },
    
    setupPerformanceMonitoring: function() {
        // Monitor and report performance issues
        window.addEventListener('load', () => {
            setTimeout(() => {
                const report = this.metrics.getMetricsReport();
                
                // Log performance report
                console.log('📈 Performance Report:', report);
                
                // Send to analytics (if enabled)
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'performance_metrics', {
                        event_category: 'Web Performance',
                        custom_map: {
                            metric_lcp: report.lcp,
                            metric_fid: report.fid,
                            metric_cls: report.cls,
                            metric_ttfb: report.ttfb,
                            metric_fcp: report.fcp
                        }
                    });
                }
            }, this.config.analyticsDelay);
        });
        
        // Monitor errors
        window.addEventListener('error', (event) => {
            console.error('❌ Performance Error:', event.error);
        });
        
        window.addEventListener('unhandledrejection', (event) => {
            console.error('❌ Promise Rejection:', event.reason);
        });
    }
};

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        PishkhanakPerformance.init();
    });
} else {
    PishkhanakPerformance.init();
}
</script>

{{-- Loading Shimmer Animation for Lazy Loading --}}
<style>
@keyframes loading-shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.loading-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading-shimmer 1.5s infinite;
}

/* Lazy Loading States */
.lazy-content {
    transition: opacity 0.3s ease;
}

.lazy-content.loading {
    opacity: 0.7;
}

.lazy-content.loaded {
    opacity: 1;
}

img[data-src] {
    transition: opacity 0.3s ease;
    opacity: 0.8;
}

img.loaded {
    opacity: 1;
}

img.error {
    opacity: 0.5;
    filter: grayscale(1);
}

/* Persian Font Loading States */
.font-loading {
    font-family: Tahoma, Arial, sans-serif;
}

.font-loaded {
    font-family: 'IRANSans', Tahoma, Arial, sans-serif;
}

/* Performance Optimization CSS */
.hero-section {
    contain: layout style paint;
}

.form-container {
    contain: layout style;
}

/* Critical Resource Preload Styles */
.critical-content {
    will-change: transform;
}

/* Persian RTL Optimization */
[dir="rtl"] {
    text-align: right;
}

[dir="rtl"] input {
    text-align: right;
}

/* Performance-optimized animations */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* GPU acceleration for smooth scrolling */
.smooth-scroll {
    transform: translateZ(0);
    backface-visibility: hidden;
}
</style>

{{-- Service Worker Registration --}}
<script>
// Service Worker for Caching Persian Assets
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw-persian-assets.js')
            .then(function(registration) {
                console.log('✓ Persian Assets Service Worker registered');
            })
            .catch(function(error) {
                console.warn('✗ Service Worker registration failed:', error);
            });
    });
}
</script>

{{-- Performance Hints and Prefetch --}}
<script>
// Intelligent Resource Prefetching
window.PishkhanakPrefetch = {
    prefetchedResources: new Set(),
    
    init: function() {
        this.setupIdlePrefetch();
        this.setupUserBehaviorPrefetch();
    },
    
    setupIdlePrefetch: function() {
        if ('requestIdleCallback' in window) {
            requestIdleCallback(() => {
                this.prefetchCriticalResources();
            });
        } else {
            setTimeout(() => {
                this.prefetchCriticalResources();
            }, 2000);
        }
    },
    
    prefetchCriticalResources: function() {
        const resources = [
            '/css/persian-fonts.css',
            '/js/persian-number-converter.js',
            '/images/banks/sprite.svg'
        ];
        
        resources.forEach(resource => {
            if (!this.prefetchedResources.has(resource)) {
                this.prefetchResource(resource);
            }
        });
    },
    
    prefetchResource: function(url) {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = url;
        document.head.appendChild(link);
        
        this.prefetchedResources.add(url);
        console.log(`⚡ Prefetched: ${url}`);
    },
    
    setupUserBehaviorPrefetch: function() {
        // Prefetch resources based on user behavior
        document.addEventListener('mouseenter', (e) => {
            if (e.target.matches('a[href]')) {
                const href = e.target.getAttribute('href');
                if (href && !this.prefetchedResources.has(href)) {
                    this.prefetchResource(href);
                }
            }
        }, { passive: true });
    }
};

// Initialize prefetching
document.addEventListener('DOMContentLoaded', () => {
    PishkhanakPrefetch.init();
});
</script>