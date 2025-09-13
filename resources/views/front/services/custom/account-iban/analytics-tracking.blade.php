{{-- Advanced Analytics Tracking for Account to IBAN Service --}}
{{-- Usage: @include('front.services.custom.account-iban.analytics-tracking') --}}

{{-- Google Analytics 4 Configuration --}}
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.tracking_id', 'GA_MEASUREMENT_ID') }}"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}

// Initialize Google Analytics with Persian localization
gtag('js', new Date());
gtag('config', '{{ config("services.google_analytics.tracking_id", "GA_MEASUREMENT_ID") }}', {
    page_title: 'تبدیل حساب به شبا | پیشخوانک',
    page_location: window.location.href,
    content_group1: 'خدمات بانکی',
    content_group2: 'تبدیل شبا',
    content_group3: 'سرویس رایگان',
    language: 'fa',
    country: 'IR',
    currency: 'IRR',
    custom_map: {
        'custom_parameter_1': 'service_category',
        'custom_parameter_2': 'conversion_type',
        'custom_parameter_3': 'user_language'
    }
});

// SHEBA Conversion Analytics Manager
window.PishkhanakAnalytics = {
    // Configuration
    config: {
        trackingEnabled: true,
        debugMode: false,
        sessionTimeout: 1800000, // 30 minutes
        conversionGoalId: 'sheba_conversion',
        privacyMode: true
    },
    
    // Session Management
    session: {
        sessionId: null,
        startTime: Date.now(),
        pageViews: 0,
        interactions: 0,
        conversions: 0,
        
        init: function() {
            this.sessionId = this.generateSessionId();
            this.trackSessionStart();
            this.setupSessionTracking();
        },
        
        generateSessionId: function() {
            return 'pishkhanak_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        },
        
        trackSessionStart: function() {
            this.parent.trackEvent('session_start', {
                session_id: this.sessionId,
                service: 'account_to_iban',
                language: 'fa-IR',
                page_title: document.title,
                referrer: document.referrer || 'direct'
            });
        },
        
        setupSessionTracking: function() {
            // Track page visibility changes
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden') {
                    this.parent.trackEvent('session_pause', {
                        session_id: this.sessionId,
                        duration: Date.now() - this.startTime
                    });
                } else {
                    this.parent.trackEvent('session_resume', {
                        session_id: this.sessionId
                    });
                }
            });
            
            // Track session end
            window.addEventListener('beforeunload', () => {
                this.trackSessionEnd();
            });
        },
        
        trackSessionEnd: function() {
            const sessionDuration = Date.now() - this.startTime;
            this.parent.trackEvent('session_end', {
                session_id: this.sessionId,
                duration: sessionDuration,
                page_views: this.pageViews,
                interactions: this.interactions,
                conversions: this.conversions
            });
        }
    },
    
    // SHEBA Conversion Tracking
    conversionTracking: {
        conversionSteps: [
            'page_view',
            'form_start',
            'account_input',
            'bank_detection',
            'conversion_request',
            'conversion_success',
            'result_interaction'
        ],
        
        currentStep: 0,
        conversionStartTime: null,
        
        init: function() {
            this.setupConversionTracking();
            this.trackPageView();
        },
        
        setupConversionTracking: function() {
            // Track form interactions
            this.setupFormTracking();
            this.setupErrorTracking();
            this.setupSuccessTracking();
        },
        
        trackPageView: function() {
            this.parent.trackEvent('page_view', {
                page_title: 'تبدیل حساب به شبا',
                page_location: window.location.href,
                service_category: 'banking',
                conversion_funnel_step: 1,
                step_name: 'page_view'
            });
            
            this.currentStep = 1;
            this.parent.session.pageViews++;
        },
        
        setupFormTracking: function() {
            // Track form start
            document.addEventListener('click', (e) => {
                if (e.target.matches('#accountNumber')) {
                    this.trackFormStart();
                }
            });
            
            // Track input interactions
            document.addEventListener('input', (e) => {
                if (e.target.matches('#accountNumber')) {
                    this.trackAccountInput(e);
                }
            });
            
            // Track bank detection
            document.addEventListener('DOMContentLoaded', () => {
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.target.id === 'bankSelectionContainer' && 
                            !mutation.target.classList.contains('hidden')) {
                            this.trackBankDetection();
                        }
                    });
                });
                
                const bankContainer = document.getElementById('bankSelectionContainer');
                if (bankContainer) {
                    observer.observe(bankContainer, { attributes: true, attributeFilter: ['class'] });
                }
            });
            
            // Track conversion request
            document.addEventListener('click', (e) => {
                if (e.target.matches('.btn-convert, #convertButton')) {
                    this.trackConversionRequest();
                }
            });
        },
        
        trackFormStart: function() {
            this.conversionStartTime = Date.now();
            
            this.parent.trackEvent('form_start', {
                conversion_funnel_step: 2,
                step_name: 'form_start',
                session_id: this.parent.session.sessionId
            });
            
            // Enhanced conversion tracking
            gtag('event', 'begin_checkout', {
                currency: 'IRR',
                value: 0,
                items: [{
                    item_id: 'sheba_conversion',
                    item_name: 'تبدیل حساب به شبا',
                    item_category: 'خدمات بانکی',
                    quantity: 1,
                    price: 0
                }]
            });
            
            this.currentStep = 2;
        },
        
        trackAccountInput: function(event) {
            const inputValue = event.target.value;
            const inputLength = inputValue.length;
            
            // Track meaningful input (not every keystroke)
            if (inputLength > 0 && inputLength % 3 === 0) {
                this.parent.trackEvent('account_input_progress', {
                    input_length: inputLength,
                    conversion_funnel_step: 3,
                    step_name: 'account_input',
                    session_id: this.parent.session.sessionId
                });
            }
            
            // Track when user reaches minimum valid length
            if (inputLength >= 8 && this.currentStep < 3) {
                this.parent.trackEvent('account_input_valid', {
                    conversion_funnel_step: 3,
                    step_name: 'account_input_complete',
                    input_length: inputLength
                });
                this.currentStep = 3;
            }
            
            this.parent.session.interactions++;
        },
        
        trackBankDetection: function() {
            const bankName = document.getElementById('bankName')?.textContent || 'نامشخص';
            const bankCode = document.getElementById('bankCode')?.textContent || '';
            
            this.parent.trackEvent('bank_detection', {
                conversion_funnel_step: 4,
                step_name: 'bank_detection',
                bank_name: bankName,
                bank_code: bankCode,
                session_id: this.parent.session.sessionId
            });
            
            this.currentStep = 4;
        },
        
        trackConversionRequest: function() {
            this.parent.trackEvent('conversion_request', {
                conversion_funnel_step: 5,
                step_name: 'conversion_request',
                session_id: this.parent.session.sessionId
            });
            
            this.currentStep = 5;
        },
        
        trackConversionSuccess: function(shebaNumber, accountNumber, bankInfo) {
            const conversionDuration = this.conversionStartTime ? 
                Date.now() - this.conversionStartTime : 0;
            
            // Main conversion event
            this.parent.trackEvent('conversion_success', {
                conversion_funnel_step: 6,
                step_name: 'conversion_success',
                conversion_duration: conversionDuration,
                bank_name: bankInfo?.name || 'نامشخص',
                bank_code: bankInfo?.code || '',
                session_id: this.parent.session.sessionId,
                sheba_length: shebaNumber?.length || 0,
                account_length: accountNumber?.length || 0
            });
            
            // Enhanced e-commerce tracking
            gtag('event', 'purchase', {
                transaction_id: 'sheba_' + Date.now(),
                currency: 'IRR',
                value: 0,
                items: [{
                    item_id: 'sheba_conversion_success',
                    item_name: 'تبدیل موفق حساب به شبا',
                    item_category: 'خدمات بانکی',
                    quantity: 1,
                    price: 0
                }]
            });
            
            // Google Analytics conversion goal
            gtag('event', 'conversion', {
                send_to: `{{ config("services.google_analytics.tracking_id") }}/{{ config("services.google_analytics.conversion_id", "CONVERSION_ID") }}`
            });
            
            this.currentStep = 6;
            this.parent.session.conversions++;
            
            // Track user satisfaction (delayed)
            setTimeout(() => {
                this.trackUserSatisfaction();
            }, 5000);
        },
        
        trackUserSatisfaction: function() {
            // Simple satisfaction tracking based on user behavior
            const timeOnResults = Date.now() - (this.conversionStartTime || 0);
            const satisfaction = timeOnResults > 10000 ? 'high' : 
                               timeOnResults > 5000 ? 'medium' : 'low';
            
            this.parent.trackEvent('user_satisfaction', {
                satisfaction_level: satisfaction,
                time_on_results: timeOnResults,
                session_id: this.parent.session.sessionId
            });
        },
        
        setupErrorTracking: function() {
            // Track conversion errors
            document.addEventListener('error', (e) => {
                this.parent.trackEvent('conversion_error', {
                    error_type: 'javascript_error',
                    error_message: e.message || 'نامشخص',
                    error_source: e.filename || 'نامشخص',
                    conversion_funnel_step: this.currentStep,
                    session_id: this.parent.session.sessionId
                });
            });
            
            // Track validation errors
            window.addEventListener('validationError', (e) => {
                this.parent.trackEvent('validation_error', {
                    error_type: 'validation_error',
                    error_message: e.detail?.message || 'خطای اعتبارسنجی',
                    conversion_funnel_step: this.currentStep,
                    session_id: this.parent.session.sessionId
                });
            });
        },
        
        setupSuccessTracking: function() {
            // Track result interactions
            document.addEventListener('click', (e) => {
                if (e.target.matches('#copySheba, #downloadBtn, #shareBtn')) {
                    this.trackResultInteraction(e.target.id);
                }
            });
            
            // Listen for conversion success event
            window.addEventListener('shebaConversionSuccess', (e) => {
                this.trackConversionSuccess(
                    e.detail?.sheba,
                    e.detail?.account,
                    e.detail?.bankInfo
                );
            });
        },
        
        trackResultInteraction: function(actionType) {
            this.parent.trackEvent('result_interaction', {
                action_type: actionType,
                conversion_funnel_step: 7,
                step_name: 'result_interaction',
                session_id: this.parent.session.sessionId
            });
            
            this.currentStep = 7;
            this.parent.session.interactions++;
        }
    },
    
    // User Behavior Analytics
    behaviorTracking: {
        scrollDepth: 0,
        maxScrollDepth: 0,
        timeOnPage: Date.now(),
        
        init: function() {
            this.setupScrollTracking();
            this.setupTimeTracking();
            this.setupInteractionTracking();
        },
        
        setupScrollTracking: function() {
            let ticking = false;
            
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        const scrollTop = window.pageYOffset;
                        const docHeight = document.body.scrollHeight - window.innerHeight;
                        const scrollPercent = Math.round((scrollTop / docHeight) * 100);
                        
                        this.scrollDepth = scrollPercent;
                        if (scrollPercent > this.maxScrollDepth) {
                            this.maxScrollDepth = scrollPercent;
                            
                            // Track milestone scroll depths
                            if (scrollPercent >= 25 && scrollPercent < 50) {
                                this.parent.trackEvent('scroll_depth', { depth: 25 });
                            } else if (scrollPercent >= 50 && scrollPercent < 75) {
                                this.parent.trackEvent('scroll_depth', { depth: 50 });
                            } else if (scrollPercent >= 75) {
                                this.parent.trackEvent('scroll_depth', { depth: 75 });
                            }
                        }
                        
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });
        },
        
        setupTimeTracking: function() {
            // Track time milestones
            const milestones = [10, 30, 60, 120, 300]; // seconds
            
            milestones.forEach(seconds => {
                setTimeout(() => {
                    if (!document.hidden) {
                        this.parent.trackEvent('time_on_page', {
                            time_seconds: seconds,
                            session_id: this.parent.session.sessionId
                        });
                    }
                }, seconds * 1000);
            });
        },
        
        setupInteractionTracking: function() {
            // Track clicks on important elements
            document.addEventListener('click', (e) => {
                const target = e.target;
                let elementInfo = '';
                
                if (target.matches('a')) {
                    elementInfo = `link:${target.href}`;
                } else if (target.matches('button')) {
                    elementInfo = `button:${target.textContent?.trim()}`;
                } else if (target.matches('.faq-question')) {
                    elementInfo = 'faq_question';
                } else if (target.matches('.help-tooltip-trigger')) {
                    elementInfo = 'help_tooltip';
                }
                
                if (elementInfo) {
                    this.parent.trackEvent('element_interaction', {
                        element_type: elementInfo,
                        session_id: this.parent.session.sessionId
                    });
                }
            }, { passive: true });
        }
    },
    
    // Persian Banking Metrics
    bankingMetrics: {
        supportedBanks: [
            'بانک ملی ایران', 'بانک صادرات', 'بانک کشاورزی', 
            'بانک ملت', 'بانک پارسیان', 'بانک پاسارگاد'
        ],
        
        init: function() {
            this.trackBankingEnvironment();
        },
        
        trackBankingEnvironment: function() {
            this.parent.trackEvent('banking_environment', {
                supported_banks_count: this.supportedBanks.length,
                service_language: 'persian',
                compliance_standard: 'ISO_13616',
                algorithm: 'MOD_97',
                country_code: 'IR'
            });
        },
        
        trackBankPreference: function(bankCode, bankName) {
            this.parent.trackEvent('bank_preference', {
                bank_code: bankCode,
                bank_name: bankName,
                is_supported: this.supportedBanks.includes(bankName),
                session_id: this.parent.session.sessionId
            });
        }
    },
    
    // Performance Integration
    performanceTracking: {
        init: function() {
            this.trackPerformanceMetrics();
        },
        
        trackPerformanceMetrics: function() {
            // Integration with performance manager
            window.addEventListener('load', () => {
                setTimeout(() => {
                    if (window.PishkhanakPerformance?.metrics?.getMetricsReport) {
                        const metrics = window.PishkhanakPerformance.metrics.getMetricsReport();
                        
                        this.parent.trackEvent('performance_metrics', {
                            lcp: metrics.lcp,
                            fid: metrics.fid,
                            cls: metrics.cls,
                            ttfb: metrics.ttfb,
                            fcp: metrics.fcp,
                            persian_font_load_time: metrics.persianFontLoadTime,
                            total_load_time: metrics.totalPageLoadTime
                        });
                    }
                }, 3000);
            });
        }
    },
    
    // Privacy-compliant tracking
    privacy: {
        consentGiven: false,
        
        init: function() {
            this.checkConsent();
            this.setupConsentBanner();
        },
        
        checkConsent: function() {
            const consent = localStorage.getItem('pishkhanak_analytics_consent');
            this.consentGiven = consent === 'true';
            
            if (!this.consentGiven) {
                this.parent.config.trackingEnabled = false;
            }
        },
        
        setupConsentBanner: function() {
            if (!this.consentGiven) {
                // Show privacy-compliant consent banner
                this.showConsentBanner();
            }
        },
        
        giveConsent: function() {
            this.consentGiven = true;
            this.parent.config.trackingEnabled = true;
            localStorage.setItem('pishkhanak_analytics_consent', 'true');
            
            // Initialize tracking after consent
            this.parent.init();
        },
        
        revokeConsent: function() {
            this.consentGiven = false;
            this.parent.config.trackingEnabled = false;
            localStorage.removeItem('pishkhanak_analytics_consent');
            
            // Clear existing tracking data
            gtag('consent', 'update', {
                'analytics_storage': 'denied'
            });
        },
        
        showConsentBanner: function() {
            // Simple consent banner (can be styled)
            const banner = document.createElement('div');
            banner.innerHTML = `
                <div style="position: fixed; bottom: 0; left: 0; right: 0; background: #1f2937; color: white; padding: 1rem; z-index: 1000; text-align: center;">
                    <p style="margin: 0 0 1rem 0;">برای بهبود خدمات، از کوکی‌ها و ابزارهای تحلیلی استفاده می‌کنیم.</p>
                    <button onclick="window.PishkhanakAnalytics.privacy.giveConsent(); this.parentElement.remove();" 
                            style="background: #3b82f6; color: white; border: none; padding: 0.5rem 1rem; border-radius: 0.25rem; margin: 0 0.5rem;">
                        موافقم
                    </button>
                    <button onclick="this.parentElement.remove();" 
                            style="background: #6b7280; color: white; border: none; padding: 0.5rem 1rem; border-radius: 0.25rem;">
                        رد کردن
                    </button>
                </div>
            `;
            document.body.appendChild(banner);
        }
    },
    
    // Main tracking function
    trackEvent: function(eventName, parameters = {}) {
        if (!this.config.trackingEnabled) {
            return;
        }
        
        // Add common parameters
        const enrichedParams = {
            ...parameters,
            timestamp: new Date().toISOString(),
            user_language: 'fa-IR',
            service_name: 'account_to_iban_converter',
            page_path: window.location.pathname
        };
        
        // Send to Google Analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, enrichedParams);
        }
        
        // Debug logging
        if (this.config.debugMode) {
            console.log('📊 Analytics Event:', eventName, enrichedParams);
        }
        
        // Send to custom analytics endpoint (if configured)
        if (window.CUSTOM_ANALYTICS_ENDPOINT) {
            this.sendToCustomEndpoint(eventName, enrichedParams);
        }
    },
    
    sendToCustomEndpoint: function(eventName, parameters) {
        fetch(window.CUSTOM_ANALYTICS_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                event: eventName,
                parameters: parameters
            })
        }).catch(error => {
            console.warn('Custom analytics endpoint error:', error);
        });
    },
    
    // Main initialization
    init: function() {
        if (!this.config.trackingEnabled) {
            console.log('📊 Analytics tracking disabled (privacy mode)');
            return;
        }
        
        console.log('📊 Initializing Pishkhanak Analytics');
        
        // Initialize all tracking modules
        this.session.init();
        this.conversionTracking.init();
        this.behaviorTracking.init();
        this.bankingMetrics.init();
        this.performanceTracking.init();
        
        console.log('✅ Analytics tracking initialized');
    }
};

// Privacy-first initialization
document.addEventListener('DOMContentLoaded', () => {
    // Initialize privacy system first
    PishkhanakAnalytics.privacy.init();
    
    // Initialize analytics if consent is given
    if (PishkhanakAnalytics.privacy.consentGiven) {
        PishkhanakAnalytics.init();
    }
});

// Global analytics functions for external use
window.trackShebaConversion = function(shebaNumber, accountNumber, bankInfo) {
    if (window.PishkhanakAnalytics) {
        PishkhanakAnalytics.conversionTracking.trackConversionSuccess(
            shebaNumber, accountNumber, bankInfo
        );
    }
};

window.trackAnalyticsEvent = function(eventName, parameters) {
    if (window.PishkhanakAnalytics) {
        PishkhanakAnalytics.trackEvent(eventName, parameters);
    }
};
</script>

{{-- Hotjar Integration (Optional) --}}
@if(config('services.hotjar.site_id'))
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:{{ config('services.hotjar.site_id') }},hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>
@endif

{{-- Persian Content Analytics Schema --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "url": "{{ config('app.url') }}",
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "{{ config('app.url') }}/search?q={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  },
  "about": {
    "@type": "Service",
    "name": "تبدیل حساب به شبا",
    "description": "سرویس رایگان تبدیل شماره حساب بانکی به شبا"
  }
}
</script>