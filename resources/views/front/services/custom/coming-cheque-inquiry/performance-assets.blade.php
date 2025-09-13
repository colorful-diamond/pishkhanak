{{-- Performance-Optimized Assets for Coming Check Inquiry Service --}}

{{-- Critical CSS Inlined for Above-the-Fold Content --}}
<style>
/* Critical CSS for Persian RTL Layout and Performance */
.critical-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 60vh;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.critical-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-opacity=".1"/><stop offset="100%" stop-opacity="0"/></radialGradient></defs><rect width="100%" height="100%" fill="url(%23a)"/></svg>');
    opacity: 0.1;
}

.critical-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
    position: relative;
    z-index: 1;
}

.critical-title {
    font-size: 3rem;
    font-weight: 800;
    color: white;
    text-align: center;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    direction: rtl;
}

.critical-subtitle {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.9);
    text-align: center;
    margin-bottom: 2rem;
    direction: rtl;
}

.critical-form {
    background: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    margin: 0 auto;
    direction: rtl;
}

.critical-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: border-color 0.2s ease;
    direction: rtl;
    text-align: right;
}

.critical-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.critical-button {
    width: 100%;
    padding: 0.875rem 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 0.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.critical-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

/* Loading Spinner */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Persian Font Loading */
@font-face {
    font-family: 'IRANSans';
    src: url('/assets/fonts/IRANSans-Bold.woff2') format('woff2');
    font-weight: 700;
    font-display: swap;
}

@font-face {
    font-family: 'IRANSans';
    src: url('/assets/fonts/IRANSans-Medium.woff2') format('woff2');
    font-weight: 500;
    font-display: swap;
}

@font-face {
    font-family: 'IRANSans';
    src: url('/assets/fonts/IRANSans-Regular.woff2') format('woff2');
    font-weight: 400;
    font-display: swap;
}

body {
    font-family: 'IRANSans', 'Tahoma', 'Arial', sans-serif;
    direction: rtl;
    text-align: right;
}

/* Mobile-first responsive design */
@media (max-width: 768px) {
    .critical-title {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .critical-subtitle {
        font-size: 1rem;
    }
    
    .critical-form {
        padding: 1.5rem;
        margin: 0 1rem;
    }
    
    .critical-container {
        padding: 0 1rem;
    }
}

/* High-contrast mode support */
@media (prefers-contrast: high) {
    .critical-input {
        border-color: #000;
    }
    
    .critical-button {
        background: #000;
        border: 2px solid #fff;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}
</style>

{{-- Preload Critical Resources --}}
<link rel="preload" href="/assets/fonts/IRANSans-Regular.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/assets/fonts/IRANSans-Bold.woff2" as="font" type="font/woff2" crossorigin>

{{-- DNS Prefetch for External Resources --}}
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//www.google-analytics.com">
<link rel="dns-prefetch" href="//connect.facebook.net">
<link rel="dns-prefetch" href="//static.hotjar.com">

{{-- Preconnect to Critical Third-party Origins --}}
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://api.finnotech.ir" crossorigin>

{{-- Resource Hints for Performance --}}
<link rel="prefetch" href="{{ asset('js/check-inquiry-advanced.js') }}">
<link rel="prefetch" href="{{ asset('css/components.css') }}">

{{-- Progressive Enhancement JavaScript --}}
<script>
// Feature Detection and Progressive Enhancement
(function() {
    'use strict';
    
    // Check for essential features
    const hasModernFeatures = (
        'querySelector' in document &&
        'addEventListener' in window &&
        'fetch' in window &&
        'Promise' in window
    );
    
    if (!hasModernFeatures) {
        // Load polyfills for older browsers
        const polyfillScript = document.createElement('script');
        polyfillScript.src = 'https://polyfill.io/v3/polyfill.min.js?features=fetch,Promise,querySelector';
        polyfillScript.async = true;
        document.head.appendChild(polyfillScript);
    }
    
    // Performance Observer for monitoring
    if ('PerformanceObserver' in window) {
        const observer = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                if (entry.entryType === 'navigation') {
                    // Track navigation timing
                    if (window.gtag) {
                        gtag('event', 'page_load_time', {
                            event_category: 'Performance',
                            event_label: 'Coming Check Inquiry',
                            value: Math.round(entry.loadEventEnd - entry.loadEventStart)
                        });
                    }
                }
                
                if (entry.entryType === 'resource') {
                    // Track slow resource loading
                    if (entry.duration > 1000) {
                        console.warn('Slow resource:', entry.name, entry.duration + 'ms');
                    }
                }
            }
        });
        
        observer.observe({entryTypes: ['navigation', 'resource']});
    }
    
    // Lazy loading intersection observer
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });
        
        // Apply to images once DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        });
    }
    
    // Service Worker Registration for Caching
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('SW registered: ', registration);
                })
                .catch(registrationError => {
                    console.log('SW registration failed: ', registrationError);
                });
        });
    }
    
    // Critical Path CSS Loader
    function loadCSS(href) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        link.media = 'print';
        link.onload = function() {
            this.media = 'all';
        };
        
        // Fallback for browsers that don't support onload on link
        setTimeout(() => {
            link.media = 'all';
        }, 100);
        
        document.head.appendChild(link);
    }
    
    // Load non-critical CSS after page load
    window.addEventListener('load', () => {
        loadCSS('{{ asset("css/components.css") }}');
        loadCSS('{{ asset("css/animations.css") }}');
        loadCSS('{{ asset("css/responsive.css") }}');
    });
    
    // Optimize font loading
    if (document.fonts) {
        document.fonts.ready.then(() => {
            document.body.classList.add('fonts-loaded');
        });
    }
    
})();

// Immediate DOM optimization
document.addEventListener('DOMContentLoaded', function() {
    // Optimize form performance
    const form = document.querySelector('form[data-service="coming-check-inquiry"]');
    if (form) {
        // Debounced validation
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            let validationTimeout;
            input.addEventListener('input', function() {
                clearTimeout(validationTimeout);
                validationTimeout = setTimeout(() => {
                    validateField(this);
                }, 300);
            });
        });
        
        // Optimized form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            // Show loading state
            submitButton.innerHTML = '<span class="loading-spinner"></span> در حال پردازش...';
            submitButton.disabled = true;
            
            // Simulate API call with proper error handling
            submitForm(form).finally(() => {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        });
    }
    
    // Optimize scroll performance
    let ticking = false;
    function updateScrollPosition() {
        // Efficient scroll handling
        const scrollY = window.pageYOffset;
        document.body.style.setProperty('--scroll-y', scrollY + 'px');
        ticking = false;
    }
    
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(updateScrollPosition);
            ticking = true;
        }
    });
});

// Field validation with performance optimization
function validateField(field) {
    const fieldName = field.name;
    const value = field.value.trim();
    
    // Use DocumentFragment for DOM manipulation
    const fragment = document.createDocumentFragment();
    
    switch (fieldName) {
        case 'national_code':
            const isValidNationalCode = value.length === 10 && /^\d{10}$/.test(value);
            updateFieldStatus(field, isValidNationalCode, 'کد ملی باید ۱۰ رقم باشد');
            break;
            
        case 'mobile':
            const isValidMobile = /^09\d{9}$/.test(value);
            updateFieldStatus(field, isValidMobile, 'شماره موبایل باید با ۰۹ شروع شود');
            break;
    }
}

function updateFieldStatus(field, isValid, message) {
    // Batch DOM updates
    requestAnimationFrame(() => {
        field.classList.toggle('valid', isValid);
        field.classList.toggle('invalid', !isValid);
        
        // Update ARIA attributes for accessibility
        field.setAttribute('aria-invalid', !isValid);
        
        let errorElement = field.parentNode.querySelector('.error-message');
        if (!isValid && message) {
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'error-message text-red-500 text-sm mt-1';
                errorElement.setAttribute('role', 'alert');
                field.parentNode.appendChild(errorElement);
            }
            errorElement.textContent = message;
        } else if (errorElement) {
            errorElement.remove();
        }
    });
}

// Optimized form submission
async function submitForm(form) {
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        // Handle success
        if (result.success) {
            showResults(result.data);
        } else {
            showError(result.message || 'خطا در پردازش درخواست');
        }
        
        // Track performance
        if (window.gtag) {
            gtag('event', 'form_response_time', {
                event_category: 'Performance',
                value: Date.now() - window.formSubmitTime
            });
        }
        
    } catch (error) {
        console.error('Form submission error:', error);
        showError('خطا در ارتباط با سرور. لطفاً دوباره تلاش کنید.');
        
        // Track errors
        if (window.gtag) {
            gtag('event', 'exception', {
                description: error.message,
                fatal: false
            });
        }
    }
}

function showResults(data) {
    const resultsContainer = document.getElementById('results-container');
    if (resultsContainer) {
        // Use template for better performance
        const template = document.getElementById('results-template');
        if (template) {
            const clone = template.content.cloneNode(true);
            // Populate clone with data
            resultsContainer.innerHTML = '';
            resultsContainer.appendChild(clone);
        }
    }
}

function showError(message) {
    // Create error notification with accessibility support
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-error';
    errorDiv.setAttribute('role', 'alert');
    errorDiv.setAttribute('aria-live', 'assertive');
    errorDiv.textContent = message;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(errorDiv, container.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }
}

// Web Vitals measurement
function measureWebVitals() {
    // Core Web Vitals
    new PerformanceObserver((entryList) => {
        for (const entry of entryList.getEntries()) {
            if (entry.name === 'first-contentful-paint') {
                console.log('FCP:', entry.startTime);
            }
        }
    }).observe({entryTypes: ['paint']});
    
    // Largest Contentful Paint
    new PerformanceObserver((entryList) => {
        const entries = entryList.getEntries();
        const lastEntry = entries[entries.length - 1];
        console.log('LCP:', lastEntry.startTime);
    }).observe({entryTypes: ['largest-contentful-paint']});
    
    // First Input Delay (interaction readiness)
    new PerformanceObserver((entryList) => {
        for (const entry of entryList.getEntries()) {
            const delay = entry.processingStart - entry.startTime;
            console.log('FID:', delay);
        }
    }).observe({entryTypes: ['first-input']});
}

// Initialize performance measurement
if ('PerformanceObserver' in window) {
    measureWebVitals();
}
</script>

{{-- Optimized CSS Loading with Media Queries --}}
<noscript>
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</noscript>

{{-- Async JavaScript Loading --}}
<script>
// Async script loader
function loadScript(src, callback) {
    const script = document.createElement('script');
    script.async = true;
    script.src = src;
    script.onload = callback || function() {};
    script.onerror = function() {
        console.error('Failed to load script:', src);
    };
    document.head.appendChild(script);
}

// Load non-critical scripts after page load
window.addEventListener('load', function() {
    // Load FAQ search functionality
    loadScript('{{ asset("js/faq-search.js") }}');
    
    // Load form validation enhancements
    loadScript('{{ asset("js/form-validation.js") }}');
    
    // Load animation library
    loadScript('{{ asset("js/animations.js") }}');
    
    // Load accessibility enhancements
    loadScript('{{ asset("js/accessibility.js") }}');
});

// Preload hover interactions
document.addEventListener('mouseover', function(e) {
    if (e.target.matches('a[href^="/"], button')) {
        // Preload link destination or prepare interaction
        const href = e.target.href;
        if (href && !e.target.dataset.preloaded) {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = href;
            document.head.appendChild(link);
            e.target.dataset.preloaded = 'true';
        }
    }
});
</script>

{{-- Image Optimization and Lazy Loading --}}
<script>
// Optimized image loading
function optimizeImages() {
    const images = document.querySelectorAll('img');
    
    images.forEach(img => {
        // Add lazy loading attribute
        if (!img.hasAttribute('loading')) {
            img.loading = 'lazy';
        }
        
        // Optimize for different screen sizes
        if (!img.srcset && img.dataset.srcset) {
            img.srcset = img.dataset.srcset;
        }
        
        // Add error handling
        img.addEventListener('error', function() {
            this.src = '/images/placeholder.jpg';
            this.alt = 'تصویر در دسترس نیست';
        });
    });
}

document.addEventListener('DOMContentLoaded', optimizeImages);

// Critical resource hints injection
const criticalResources = [
    { rel: 'prefetch', href: '{{ asset("css/tailwind.min.css") }}' },
    { rel: 'prefetch', href: '{{ asset("js/alpine.min.js") }}' },
    { rel: 'preload', href: '{{ asset("fonts/IRANSans-Medium.woff2") }}', as: 'font', type: 'font/woff2' }
];

criticalResources.forEach(resource => {
    const link = document.createElement('link');
    Object.keys(resource).forEach(key => {
        link[key] = resource[key];
    });
    if (resource.type) link.type = resource.type;
    if (resource.crossorigin !== undefined) link.crossOrigin = resource.crossorigin;
    document.head.appendChild(link);
});
</script>

{{-- Service Worker for Caching --}}
<script>
// Service Worker for offline capabilities and caching
if ('serviceWorker' in navigator && 'PushManager' in window) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/service-worker.js', {
            scope: '/services/'
        }).then(function(registration) {
            console.log('ServiceWorker registration successful');
            
            // Update on page refresh
            registration.addEventListener('updatefound', () => {
                const newWorker = registration.installing;
                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        // New update available
                        showUpdateNotification();
                    }
                });
            });
            
        }).catch(function(err) {
            console.log('ServiceWorker registration failed: ', err);
        });
    });
}

function showUpdateNotification() {
    const notification = document.createElement('div');
    notification.className = 'update-notification';
    notification.innerHTML = `
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
            <div class="flex items-center">
                <div class="ml-3">
                    <p class="text-sm">نسخه جدید در دسترس است. برای بهره‌مندی از آخرین بهبودها صفحه را بازآوری کنید.</p>
                    <button onclick="window.location.reload()" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded text-sm">
                        بازآوری صفحه
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertBefore(notification, document.body.firstChild);
}
</script>