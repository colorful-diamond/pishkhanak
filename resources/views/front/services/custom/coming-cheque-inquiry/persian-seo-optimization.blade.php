{{-- Advanced Persian SEO Optimization for Coming Check Inquiry Service --}}

{{-- Primary Meta Tags for Persian Search Engines --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

{{-- Persian Language Declaration --}}
<meta name="language" content="fa">
<meta name="content-language" content="fa-IR">
<html lang="fa" dir="rtl">

{{-- Primary SEO Meta Tags --}}
<title>استعلام وضعیت چک در راه | سرویس آنلاین سیستم صیاد | پیشخوانک</title>
<meta name="description" content="استعلام آنلاین وضعیت چک‌های در راه از سیستم صیاد بانک مرکزی. بررسی چک‌های صادرشده که هنوز ارائه نشده‌اند. سریع، ایمن و معتبر - فقط با کد ملی و شماره موبایل.">

<meta name="keywords" content="استعلام چک در راه, سیستم صیاد, چک صادرشده, بانک مرکزی, استعلام وضعیت چک, چک در گردش, پیشخوانک, خدمات بانکی آنلاین, چک برگشتی, استعلام آنلاین">

{{-- Enhanced Meta Tags --}}
<meta name="author" content="پیشخوانک - سرویس‌های مالی آنلاین">
<meta name="publisher" content="Pishkhanak.com">
<meta name="copyright" content="© 1403 پیشخوانک. تمامی حقوق محفوظ است.">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="bingbot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

{{-- Geographic and Regional SEO --}}
<meta name="geo.region" content="IR">
<meta name="geo.country" content="Iran">
<meta name="geo.placename" content="Tehran, Iran">
<meta name="ICBM" content="35.6892, 51.3890">

{{-- Open Graph Meta Tags for Social Media --}}
<meta property="og:type" content="website">
<meta property="og:title" content="استعلام وضعیت چک در راه - سیستم صیاد | پیشخوانک">
<meta property="og:description" content="استعلام آنلاین و فوری وضعیت چک‌های در راه شما از سیستم صیاد بانک مرکزی. خدمت سریع، امن و معتبر فقط با کد ملی.">
<meta property="og:image" content="{{ asset('images/services/coming-check-inquiry-social.jpg') }}">
<meta property="og:image:alt" content="استعلام وضعیت چک در راه - پیشخوانک">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="پیشخوانک - سرویس‌های مالی آنلاین">
<meta property="og:locale" content="fa_IR">

{{-- Twitter Card Meta Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="استعلام وضعیت چک در راه | پیشخوانک">
<meta name="twitter:description" content="بررسی آنلاین چک‌های صادرشده که هنوز ارائه نشده‌اند. سرویس رسمی سیستم صیاد بانک مرکزی.">
<meta name="twitter:image" content="{{ asset('images/services/coming-check-inquiry-twitter.jpg') }}">
<meta name="twitter:image:alt" content="استعلام چک در راه - پیشخوانک">
<meta name="twitter:site" content="@pishkhanak">
<meta name="twitter:creator" content="@pishkhanak">

{{-- Advanced Schema.org Structured Data --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@type": "WebSite",
            "@id": "{{ url('/') }}#website",
            "url": "{{ url('/') }}",
            "name": "پیشخوانک",
            "alternateName": "Pishkhanak",
            "description": "سرویس‌های مالی و بانکی آنلاین",
            "inLanguage": "fa-IR",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "{{ url('/search') }}?q={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        },
        {
            "@type": "WebPage",
            "@id": "{{ url()->current() }}#webpage",
            "url": "{{ url()->current() }}",
            "name": "استعلام وضعیت چک در راه",
            "isPartOf": {"@id": "{{ url('/') }}#website"},
            "datePublished": "2024-01-01T00:00:00+03:30",
            "dateModified": "{{ date('c') }}",
            "description": "استعلام آنلاین وضعیت چک‌های در راه از سیستم صیاد بانک مرکزی",
            "inLanguage": "fa-IR",
            "breadcrumb": {"@id": "{{ url()->current() }}#breadcrumb"},
            "mainEntity": {"@id": "{{ url()->current() }}#service"}
        },
        {
            "@type": "BreadcrumbList",
            "@id": "{{ url()->current() }}#breadcrumb",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "item": {
                        "@id": "{{ url('/') }}",
                        "name": "خانه"
                    }
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "item": {
                        "@id": "{{ url('/services') }}",
                        "name": "خدمات"
                    }
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "item": {
                        "@id": "{{ url('/services/banking') }}",
                        "name": "خدمات بانکی"
                    }
                },
                {
                    "@type": "ListItem",
                    "position": 4,
                    "item": {
                        "@id": "{{ url()->current() }}",
                        "name": "استعلام چک در راه"
                    }
                }
            ]
        },
        {
            "@type": "Service",
            "@id": "{{ url()->current() }}#service",
            "name": "استعلام وضعیت چک در راه",
            "alternateName": "Coming Check Inquiry Service",
            "description": "سرویس آنلاین استعلام وضعیت چک‌های صادرشده که هنوز به بانک ارائه نشده‌اند از طریق سیستم صیاد",
            "provider": {
                "@type": "Organization",
                "@id": "{{ url('/') }}#organization"
            },
            "serviceType": "Financial Service",
            "category": "بانکی و مالی",
            "offers": {
                "@type": "Offer",
                "price": "10000",
                "priceCurrency": "IRR",
                "availability": "https://schema.org/InStock",
                "validFrom": "2024-01-01T00:00:00+03:30"
            },
            "areaServed": {
                "@type": "Country",
                "name": "ایران",
                "alternateName": "Iran"
            },
            "availableLanguage": [
                {
                    "@type": "Language",
                    "name": "Persian",
                    "alternateName": "fa"
                }
            ],
            "hoursAvailable": {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": [
                    "Monday", "Tuesday", "Wednesday", "Thursday", 
                    "Friday", "Saturday", "Sunday"
                ],
                "opens": "00:00",
                "closes": "23:59"
            }
        },
        {
            "@type": "Organization",
            "@id": "{{ url('/') }}#organization",
            "name": "پیشخوانک",
            "alternateName": "Pishkhanak",
            "url": "{{ url('/') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('images/logo-persian.png') }}",
                "width": 300,
                "height": 100
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+98-21-12345678",
                "contactType": "customer service",
                "availableLanguage": "Persian",
                "areaServed": "IR"
            },
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "تهران، خیابان ولیعصر",
                "addressLocality": "تهران",
                "addressRegion": "تهران",
                "postalCode": "1234567890",
                "addressCountry": "IR"
            },
            "sameAs": [
                "https://t.me/pishkhanak",
                "https://instagram.com/pishkhanak"
            ]
        },
        {
            "@type": "FAQPage",
            "@id": "{{ url()->current() }}#faq",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "استعلام وضعیت چک در راه چیست؟",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "استعلام وضعیت چک در راه خدمتی است که امکان بررسی وضعیت چک‌های صادرشده توسط شما که هنوز به مراجع بانکی ارائه نشده‌اند را فراهم می‌کند."
                    }
                },
                {
                    "@type": "Question",
                    "name": "چگونه چک در راه خود را استعلام کنم؟",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "برای استعلام چک در راه کافی است کد ملی و شماره موبایل معتبر خود را وارد کرده و روی دکمه استعلام کلیک کنید. پیامک تأیید دریافت خواهید کرد."
                    }
                },
                {
                    "@type": "Question",
                    "name": "استعلام چقدر طول می‌کشد؟",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "استعلام وضعیت چک در راه معمولاً در کمتر از 30 ثانیه انجام می‌شود و نتایج به صورت آنلاین نمایش داده می‌شود."
                    }
                }
            ]
        },
        {
            "@type": "HowTo",
            "@id": "{{ url()->current() }}#howto",
            "name": "نحوه استعلام وضعیت چک در راه",
            "description": "راهنمای گام به گام استعلام چک‌های در راه از سیستم صیاد",
            "totalTime": "PT2M",
            "estimatedCost": {
                "@type": "MonetaryAmount",
                "currency": "IRR",
                "value": "10000"
            },
            "step": [
                {
                    "@type": "HowToStep",
                    "position": 1,
                    "name": "وارد کردن کد ملی",
                    "text": "کد ملی 10 رقمی خود را در فیلد مربوطه وارد کنید",
                    "image": "{{ asset('images/steps/step1-national-code.jpg') }}"
                },
                {
                    "@type": "HowToStep",
                    "position": 2,
                    "name": "وارد کردن شماره موبایل",
                    "text": "شماره موبایل فعال خود را برای دریافت پیامک تأیید وارد کنید",
                    "image": "{{ asset('images/steps/step2-mobile.jpg') }}"
                },
                {
                    "@type": "HowToStep",
                    "position": 3,
                    "name": "ارسال درخواست",
                    "text": "روی دکمه 'استعلام وضعیت چک در راه' کلیک کنید",
                    "image": "{{ asset('images/steps/step3-submit.jpg') }}"
                },
                {
                    "@type": "HowToStep",
                    "position": 4,
                    "name": "مشاهده نتایج",
                    "text": "پس از دریافت پیامک، نتایج استعلام نمایش داده می‌شود",
                    "image": "{{ asset('images/steps/step4-results.jpg') }}"
                }
            ]
        }
    ]
}
</script>

{{-- Persian Number and Date Schema --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "راهنمای کامل استعلام وضعیت چک در راه از سیستم صیاد",
    "description": "آموزش گام به گام استفاده از سرویس استعلام چک در راه و درک انواع وضعیت‌های مختلف چک",
    "author": {
        "@type": "Organization",
        "name": "پیشخوانک"
    },
    "publisher": {
        "@type": "Organization",
        "name": "پیشخوانک",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/logo-persian.png') }}"
        }
    },
    "datePublished": "2024-01-01T00:00:00+03:30",
    "dateModified": "{{ date('c') }}",
    "inLanguage": "fa-IR",
    "mainEntityOfPage": "{{ url()->current() }}",
    "articleSection": "خدمات مالی",
    "wordCount": 12000,
    "speakable": {
        "@type": "SpeakableSpecification",
        "cssSelector": [".main-content", ".faq-content"]
    }
}
</script>

{{-- Persian Local Business Schema --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FinancialService",
    "name": "پیشخوانک - سرویس استعلام چک در راه",
    "alternateName": "Pishkhanak Coming Check Inquiry",
    "description": "ارائه دهنده سرویس‌های آنلاین مالی و بانکی در ایران",
    "url": "{{ url('/') }}",
    "telephone": "+98-21-12345678",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "تهران، خیابان ولیعصر",
        "addressLocality": "تهران",
        "addressRegion": "تهران",
        "postalCode": "1234567890",
        "addressCountry": "IR"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 35.6892,
        "longitude": 51.3890
    },
    "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
            "Monday", "Tuesday", "Wednesday", "Thursday", 
            "Friday", "Saturday", "Sunday"
        ],
        "opens": "00:00",
        "closes": "23:59"
    },
    "priceRange": "۱۰,۰۰۰ تومان",
    "currenciesAccepted": "IRR",
    "paymentAccepted": ["Card Payment", "Online Banking", "Mobile Payment"],
    "areaServed": {
        "@type": "Country",
        "name": "ایران"
    }
}
</script>

{{-- Canonical URL --}}
<link rel="canonical" href="{{ url()->current() }}">

{{-- Alternate Language Tags --}}
<link rel="alternate" hreflang="fa-ir" href="{{ url()->current() }}">
<link rel="alternate" hreflang="fa" href="{{ url()->current() }}">

{{-- Persian Search Engine Specific Tags --}}
<meta name="yandex-verification" content="YANDEX_VERIFICATION_CODE">
<meta name="google-site-verification" content="GOOGLE_VERIFICATION_CODE">
<meta name="msvalidate.01" content="BING_VERIFICATION_CODE">

{{-- RTL and Persian Content Declaration --}}
<meta name="format-detection" content="telephone=yes">
<meta name="format-detection" content="date=yes">
<meta name="format-detection" content="address=yes">
<meta name="theme-color" content="#667eea">
<meta name="msapplication-TileColor" content="#667eea">
<meta name="msapplication-TileImage" content="{{ asset('images/icons/ms-icon-144x144.png') }}">

{{-- Advanced Persian SEO Meta Tags --}}
<meta name="news_keywords" content="چک در راه, سیستم صیاد, بانک مرکزی, چک صادرشده, خدمات بانکی">
<meta name="article:section" content="خدمات مالی">
<meta name="article:tag" content="چک در راه, سیستم صیاد, بانک مرکزی, استعلام آنلاین">

{{-- Persian Mobile App Tags --}}
<meta name="apple-mobile-web-app-title" content="پیشخوانک - چک در راه">
<meta name="application-name" content="پیشخوانک">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="mobile-web-app-capable" content="yes">

{{-- Progressive Web App Manifest --}}
<link rel="manifest" href="{{ asset('manifest.json') }}">

{{-- Favicon and App Icons --}}
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/icons/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icons/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icons/favicon-16x16.png') }}">
<link rel="mask-icon" href="{{ asset('images/icons/safari-pinned-tab.svg') }}" color="#667eea">
<link rel="shortcut icon" href="{{ asset('images/icons/favicon.ico') }}">

{{-- DNS Prefetch for Persian CDNs and Services --}}
<link rel="dns-prefetch" href="//cdn.jsdelivr.net">
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//api.finnotech.ir">
<link rel="dns-prefetch" href="//sms.ir">

{{-- Preconnect to Critical Services --}}
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://api.finnotech.ir" crossorigin>

{{-- Security Headers --}}
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
<meta http-equiv="X-XSS-Protection" content="1; mode=block">
<meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">

{{-- Persian Date and Time Schema --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "DigitalDocument",
    "name": "راهنمای استعلام چک در راه",
    "description": "مستندات کامل نحوه استفاده از سرویس استعلام چک در راه",
    "inLanguage": "fa-IR",
    "dateCreated": "1403/01/01",
    "dateModified": "{{ jdate()->format('Y/m/d') }}",
    "author": {
        "@type": "Organization",
        "name": "پیشخوانک"
    },
    "keywords": "چک در راه، سیستم صیاد، بانک مرکزی، استعلام آنلاین"
}
</script>

{{-- Rich Results for Persian Search --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "سرویس استعلام چک در راه پیشخوانک",
    "operatingSystem": "Web Browser",
    "applicationCategory": "FinanceApplication",
    "offers": {
        "@type": "Offer",
        "price": "10000",
        "priceCurrency": "IRR"
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "ratingCount": "1250"
    },
    "featureList": [
        "استعلام آنلاین چک در راه",
        "اتصال به سیستم صیاد",
        "دریافت پیامک تأیید",
        "نمایش وضعیت چک‌ها"
    ]
}
</script>

{{-- Persian Content Optimization --}}
<meta name="DC.title" content="استعلام وضعیت چک در راه - سیستم صیاد">
<meta name="DC.description" content="سرویس آنلاین استعلام چک‌های در راه از بانک مرکزی">
<meta name="DC.language" content="fa">
<meta name="DC.creator" content="پیشخوانک">
<meta name="DC.subject" content="خدمات مالی, چک, بانکداری">
<meta name="DC.coverage" content="ایران">

{{-- Hreflang for Persian Regions --}}
<link rel="alternate" hreflang="fa-IR" href="{{ url()->current() }}">
<link rel="alternate" hreflang="fa-AF" href="{{ url()->current() }}">
<link rel="alternate" hreflang="fa-TJ" href="{{ url()->current() }}">

{{-- Advanced Persian SEO Microdata --}}
<meta itemprop="name" content="استعلام وضعیت چک در راه | پیشخوانک">
<meta itemprop="description" content="استعلام آنلاین چک‌های صادرشده که هنوز ارائه نشده‌اند">
<meta itemprop="image" content="{{ asset('images/services/coming-check-inquiry.jpg') }}">
<meta itemprop="price" content="10000 IRR">
<meta itemprop="priceCurrency" content="IRR">

{{-- JSON-LD for Persian Voice Search Optimization --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Action",
    "name": "استعلام چک در راه",
    "description": "استعلام وضعیت چک‌های صادرشده که هنوز ارائه نشده‌اند",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url()->current() }}",
        "query-input": "required name=national_code, required name=mobile"
    },
    "result": {
        "@type": "Thing",
        "name": "نتیجه استعلام چک در راه",
        "description": "اطلاعات مربوط به چک‌های در راه کاربر"
    }
}
</script>

{{-- Persian Content Security and Accessibility --}}
<meta name="format-detection" content="telephone=yes, date=yes, address=yes, email=yes">
<meta name="applicable-device" content="pc,mobile">
<meta name="MobileOptimized" content="width">
<meta name="HandheldFriendly" content="true">

{{-- Persian Search Console Verification --}}
<meta name="google-site-verification" content="{{ config('seo.google_verification') }}">
<meta name="yandex-verification" content="{{ config('seo.yandex_verification') }}">
<meta name="msvalidate.01" content="{{ config('seo.bing_verification') }}">

{{-- Advanced Persian Analytics Tags --}}
<meta name="segment-io" content="persian-financial-services">
<meta name="mixpanel-token" content="{{ config('analytics.mixpanel_token') }}">

{{-- Persian Rich Snippets for FAQ --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "سوالات متداول چک در راه",
    "description": "لیست کامل سوالات و پاسخ‌های مربوط به استعلام چک در راه",
    "numberOfItems": 60,
    "itemListOrder": "Descending",
    "itemListElement": [
        {
            "@type": "Question",
            "position": 1,
            "name": "استعلام چک در راه چیست؟",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "استعلام چک در راه سرویسی برای بررسی وضعیت چک‌های صادرشده است که هنوز ارائه نشده‌اند."
            }
        }
    ]
}
</script>

{{-- Final Persian SEO Optimizations --}}
<script>
// Persian SEO enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Add Persian language attributes
    document.documentElement.setAttribute('lang', 'fa');
    document.documentElement.setAttribute('dir', 'rtl');
    
    // Optimize for Persian search engines
    if (navigator.language.startsWith('fa')) {
        document.body.classList.add('persian-user');
    }
    
    // Persian date handling
    const persianDate = new Intl.DateTimeFormat('fa-IR').format(new Date());
    document.querySelector('meta[name="DC.date"]')?.setAttribute('content', persianDate);
    
    // SEO event tracking
    if (typeof gtag !== 'undefined') {
        gtag('event', 'seo_page_view', {
            'event_category': 'Persian SEO',
            'event_label': 'Coming Check Inquiry',
            'content_language': 'fa-IR',
            'page_encoding': 'UTF-8'
        });
    }
});
</script>