{{-- Advanced Analytics Tracking for Coming Check Inquiry Service --}}
{{-- Google Analytics 4 Enhanced E-commerce and Event Tracking --}}

{{-- Google Tag Manager --}}
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

// Enhanced Configuration for Persian Financial Services
gtag('config', 'GA_MEASUREMENT_ID', {
    // Persian Language Support
    language: 'fa',
    country: 'IR',
    currency: 'IRR',
    
    // Enhanced E-commerce Configuration
    enhanced_ecommerce: true,
    
    // Service-specific Configuration
    custom_map: {
        'custom_parameter_1': 'service_type',
        'custom_parameter_2': 'inquiry_method',
        'custom_parameter_3': 'check_status'
    },
    
    // Privacy and GDPR Compliance
    anonymize_ip: true,
    allow_ad_personalization_signals: false,
    allow_google_signals: false
});

// Service Page View Tracking
gtag('event', 'page_view', {
    page_title: 'استعلام وضعیت چک در راه - پیشخوانک',
    page_location: window.location.href,
    content_group1: 'Financial Services',
    content_group2: 'Check Inquiry',
    content_group3: 'Coming Check Status',
    content_group4: 'Persian Language',
    content_group5: 'Mobile Optimized'
});
</script>

{{-- Advanced Event Tracking for Check Inquiry Service --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Form Interaction Tracking
    const checkInquiryForm = document.querySelector('form[data-service="coming-check-inquiry"]');
    const nationalCodeInput = document.querySelector('input[name="national_code"]');
    const mobileInput = document.querySelector('input[name="mobile"]');
    const submitButton = document.querySelector('button[type="submit"]');
    
    // Form Start Event
    if (nationalCodeInput) {
        nationalCodeInput.addEventListener('focus', function() {
            gtag('event', 'form_start', {
                event_category: 'Check Inquiry',
                event_label: 'Coming Check Form',
                service_type: 'coming_check_inquiry',
                step: 'national_code_focus',
                language: 'persian'
            });
        });
    }
    
    // Input Validation Events
    if (nationalCodeInput) {
        nationalCodeInput.addEventListener('blur', function() {
            const isValid = this.value.length === 10 && /^\d+$/.test(this.value);
            gtag('event', 'input_validation', {
                event_category: 'Form Validation',
                event_label: 'National Code',
                validation_result: isValid ? 'valid' : 'invalid',
                field_type: 'national_code'
            });
        });
    }
    
    if (mobileInput) {
        mobileInput.addEventListener('blur', function() {
            const isValid = /^09\d{9}$/.test(this.value);
            gtag('event', 'input_validation', {
                event_category: 'Form Validation',
                event_label: 'Mobile Number',
                validation_result: isValid ? 'valid' : 'invalid',
                field_type: 'mobile_number'
            });
        });
    }
    
    // Form Submission Tracking
    if (checkInquiryForm) {
        checkInquiryForm.addEventListener('submit', function(e) {
            gtag('event', 'form_submit', {
                event_category: 'Check Inquiry',
                event_label: 'Coming Check Inquiry Request',
                service_type: 'coming_check_inquiry',
                form_method: 'POST',
                price: 10000,
                currency: 'IRR'
            });
            
            // Enhanced E-commerce Purchase Event
            gtag('event', 'purchase', {
                transaction_id: 'coming_check_' + Date.now(),
                value: 100, // Price in Toman (10,000 IRR = 100 Toman)
                currency: 'IRR',
                items: [{
                    item_id: 'coming_check_inquiry_14',
                    item_name: 'استعلام وضعیت چک در راه',
                    item_category: 'Financial Services',
                    item_category2: 'Check Services',
                    item_category3: 'Inquiry Services',
                    quantity: 1,
                    price: 100
                }]
            });
        });
    }
    
    // Content Interaction Tracking
    
    // FAQ Interaction Tracking
    const faqToggles = document.querySelectorAll('.faq-toggle');
    faqToggles.forEach((toggle, index) => {
        toggle.addEventListener('click', function() {
            const faqTitle = this.querySelector('span').textContent.trim();
            const category = this.closest('.faq-item').dataset.category;
            
            gtag('event', 'faq_interaction', {
                event_category: 'FAQ Engagement',
                event_label: faqTitle.substring(0, 100),
                faq_category: category,
                faq_position: index + 1,
                interaction_type: 'toggle_open'
            });
        });
    });
    
    // FAQ Search Tracking
    const faqSearch = document.getElementById('faq-search');
    if (faqSearch) {
        let searchTimeout;
        faqSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.trim().length > 2) {
                    gtag('event', 'faq_search', {
                        event_category: 'FAQ Search',
                        event_label: this.value.trim().substring(0, 50),
                        search_term: this.value.trim(),
                        search_length: this.value.trim().length
                    });
                }
            }, 1000);
        });
    }
    
    // Category Filter Tracking
    const categoryFilters = document.querySelectorAll('.category-filter');
    categoryFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            const category = this.dataset.category;
            const categoryText = this.textContent.trim();
            
            gtag('event', 'category_filter', {
                event_category: 'FAQ Navigation',
                event_label: categoryText,
                selected_category: category,
                filter_type: 'faq_category'
            });
        });
    });
    
    // Scroll Depth Tracking
    let maxScrollDepth = 0;
    let scrollCheckpoints = [10, 25, 50, 75, 90, 100];
    let triggeredCheckpoints = [];
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset;
        const docHeight = document.body.scrollHeight - window.innerHeight;
        const scrollPercent = Math.round((scrollTop / docHeight) * 100);
        
        if (scrollPercent > maxScrollDepth) {
            maxScrollDepth = scrollPercent;
        }
        
        scrollCheckpoints.forEach(checkpoint => {
            if (scrollPercent >= checkpoint && !triggeredCheckpoints.includes(checkpoint)) {
                triggeredCheckpoints.push(checkpoint);
                
                gtag('event', 'scroll_depth', {
                    event_category: 'User Engagement',
                    event_label: 'Coming Check Inquiry Page',
                    scroll_depth: checkpoint,
                    page_type: 'service_page'
                });
            }
        });
    });
    
    // Time on Page Tracking
    let timeOnPage = 0;
    const timeCheckpoints = [30, 60, 120, 300, 600]; // seconds
    let timeTriggered = [];
    
    setInterval(function() {
        timeOnPage += 5;
        
        timeCheckpoints.forEach(checkpoint => {
            if (timeOnPage >= checkpoint && !timeTriggered.includes(checkpoint)) {
                timeTriggered.push(checkpoint);
                
                gtag('event', 'time_on_page', {
                    event_category: 'User Engagement',
                    event_label: 'Coming Check Inquiry',
                    time_threshold: checkpoint,
                    engagement_level: checkpoint >= 300 ? 'high' : checkpoint >= 60 ? 'medium' : 'low'
                });
            }
        });
    }, 5000);
    
    // Error Tracking
    window.addEventListener('error', function(e) {
        gtag('event', 'exception', {
            description: 'JavaScript Error: ' + e.message,
            fatal: false,
            error_source: e.filename,
            error_line: e.lineno
        });
    });
    
    // Performance Tracking
    window.addEventListener('load', function() {
        setTimeout(function() {
            const perfData = performance.getEntriesByType('navigation')[0];
            
            if (perfData) {
                gtag('event', 'page_timing', {
                    event_category: 'Performance',
                    dom_load_time: Math.round(perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart),
                    page_load_time: Math.round(perfData.loadEventEnd - perfData.loadEventStart),
                    dns_time: Math.round(perfData.domainLookupEnd - perfData.domainLookupStart),
                    server_response_time: Math.round(perfData.responseEnd - perfData.requestStart)
                });
            }
        }, 1000);
    });
    
    // Mobile Usage Tracking
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const deviceType = isMobile ? 'mobile' : 'desktop';
    
    gtag('event', 'device_detection', {
        event_category: 'Device Info',
        device_type: deviceType,
        user_agent: navigator.userAgent.substring(0, 100),
        screen_resolution: screen.width + 'x' + screen.height,
        viewport_size: window.innerWidth + 'x' + window.innerHeight
    });
    
    // Language and Locale Tracking
    gtag('event', 'locale_detection', {
        event_category: 'Localization',
        browser_language: navigator.language || navigator.userLanguage,
        page_language: document.documentElement.lang || 'fa',
        rtl_support: document.dir === 'rtl' || getComputedStyle(document.body).direction === 'rtl',
        persian_content: true
    });
    
    // Exit Intent Tracking (Desktop)
    if (!isMobile) {
        document.addEventListener('mouseleave', function(e) {
            if (e.clientY <= 0) {
                gtag('event', 'exit_intent', {
                    event_category: 'User Behavior',
                    event_label: 'Coming Check Inquiry Page',
                    time_on_page: timeOnPage,
                    scroll_depth: maxScrollDepth
                });
            }
        });
    }
});

// Custom Dimensions for Persian Financial Services
gtag('config', 'GA_MEASUREMENT_ID', {
    custom_map: {
        'dimension1': 'service_category',
        'dimension2': 'language',
        'dimension3': 'user_type',
        'dimension4': 'inquiry_type',
        'dimension5': 'device_category'
    }
});

// Set Custom Dimensions
gtag('event', 'page_view', {
    service_category: 'Financial Services',
    language: 'Persian',
    user_type: 'guest',
    inquiry_type: 'coming_check',
    device_category: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? 'mobile' : 'desktop'
});
</script>

{{-- Facebook Pixel for Persian Market --}}
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');

fbq('init', 'FACEBOOK_PIXEL_ID');
fbq('track', 'PageView', {
    content_name: 'استعلام وضعیت چک در راه',
    content_category: 'Financial Services',
    content_ids: ['coming_check_inquiry_14'],
    value: 10000,
    currency: 'IRR'
});

// Track form submissions
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[data-service="coming-check-inquiry"]');
    if (form) {
        form.addEventListener('submit', function() {
            fbq('track', 'Purchase', {
                value: 10000,
                currency: 'IRR',
                content_name: 'استعلام وضعیت چک در راه',
                content_category: 'Check Inquiry Services',
                content_ids: ['coming_check_inquiry_14']
            });
        });
    }
});
</script>

{{-- Hotjar Heat Mapping and User Behavior Analysis --}}
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:HOTJAR_ID,hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    
    // Persian language and RTL support for Hotjar
    hj('trigger', 'persian_financial_service');
</script>

{{-- Microsoft Clarity for User Session Analysis --}}
<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "CLARITY_ID");
    
    // Track Persian language usage
    clarity("set", "language", "persian");
    clarity("set", "service_type", "check_inquiry");
</script>

{{-- Custom Performance Monitoring --}}
<script>
// Performance monitoring for Persian financial services
window.addEventListener('load', function() {
    // Web Vitals Measurement
    function measureWebVitals() {
        // First Contentful Paint
        const fcpEntry = performance.getEntriesByName('first-contentful-paint')[0];
        if (fcpEntry) {
            gtag('event', 'web_vitals', {
                event_category: 'Performance',
                metric_name: 'FCP',
                metric_value: Math.round(fcpEntry.startTime),
                page_type: 'service_page'
            });
        }
        
        // Largest Contentful Paint
        if ('PerformanceObserver' in window) {
            const po = new PerformanceObserver((entryList) => {
                const entries = entryList.getEntries();
                const lastEntry = entries[entries.length - 1];
                
                gtag('event', 'web_vitals', {
                    event_category: 'Performance',
                    metric_name: 'LCP',
                    metric_value: Math.round(lastEntry.startTime),
                    page_type: 'service_page'
                });
            });
            
            po.observe({entryTypes: ['largest-contentful-paint']});
        }
        
        // Cumulative Layout Shift
        let clsValue = 0;
        if ('PerformanceObserver' in window) {
            const po = new PerformanceObserver((entryList) => {
                for (const entry of entryList.getEntries()) {
                    if (!entry.hadRecentInput) {
                        clsValue += entry.value;
                    }
                }
                
                gtag('event', 'web_vitals', {
                    event_category: 'Performance',
                    metric_name: 'CLS',
                    metric_value: Math.round(clsValue * 1000) / 1000,
                    page_type: 'service_page'
                });
            });
            
            po.observe({entryTypes: ['layout-shift']});
        }
    }
    
    setTimeout(measureWebVitals, 1000);
});

// API Response Time Monitoring
function trackAPICall(endpoint, startTime, success) {
    const duration = Date.now() - startTime;
    
    gtag('event', 'api_performance', {
        event_category: 'API Performance',
        event_label: endpoint,
        duration: duration,
        success: success,
        service_type: 'coming_check_inquiry'
    });
}

// Form Abandonment Tracking
let formStarted = false;
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            if (!formStarted) {
                formStarted = true;
                
                // Track form abandonment after 30 seconds
                setTimeout(function() {
                    if (!document.querySelector('form').dataset.submitted) {
                        gtag('event', 'form_abandonment', {
                            event_category: 'Form Behavior',
                            event_label: 'Coming Check Inquiry Form',
                            abandonment_point: document.activeElement.name || 'unknown',
                            time_to_abandon: 30
                        });
                    }
                }, 30000);
            }
        });
    });
});
</script>

{{-- A/B Testing Framework --}}
<script>
// Simple A/B testing for Persian UI elements
function initABTesting() {
    const userId = localStorage.getItem('user_id') || 'user_' + Math.random().toString(36).substr(2, 9);
    localStorage.setItem('user_id', userId);
    
    // Test variations
    const tests = {
        'cta_color': {
            'control': 'bg-blue-600',
            'variant': 'bg-green-600'
        },
        'form_layout': {
            'control': 'vertical',
            'variant': 'horizontal'
        }
    };
    
    Object.keys(tests).forEach(testName => {
        const hash = btoa(userId + testName).slice(0, 8);
        const variant = parseInt(hash, 16) % 2 === 0 ? 'control' : 'variant';
        
        gtag('event', 'ab_test_assignment', {
            event_category: 'A/B Testing',
            test_name: testName,
            variant: variant,
            user_id: userId
        });
        
        document.body.dataset[testName] = variant;
    });
}

document.addEventListener('DOMContentLoaded', initABTesting);
</script>