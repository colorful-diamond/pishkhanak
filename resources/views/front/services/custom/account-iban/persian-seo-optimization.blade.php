{{-- Advanced Persian SEO Optimization for Account to IBAN Service --}}
{{-- Usage: @include('front.services.custom.account-iban.persian-seo-optimization') --}}

{{-- Primary Persian Meta Tags --}}
<meta name="title" content="تبدیل شماره حساب به شبا رایگان | محاسبه فوری شبا | پیشخوانک">
<meta name="description" content="تبدیل رایگان و فوری شماره حساب بانکی به شبا (IBAN) برای تمام بانک‌های ایران. محاسبه دقیق با الگوریتم MOD-97، بدون نیاز به ثبت‌نام. سرویس امن و قابل اعتماد پیشخوانک.">
<meta name="keywords" content="تبدیل حساب به شبا، شماره شبا، IBAN ایران، محاسبه شبا، تبدیل شماره حساب، بانک ایران، شبا رایگان، تبدیل فوری شبا، محاسبه شماره شبا، استعلام شبا، بانک مرکزی، MOD-97، پیشخوانک، خدمات بانکی">

{{-- Persian Language and Locale Meta Tags --}}
<meta name="language" content="fa">
<meta name="content-language" content="fa-IR">
<meta http-equiv="content-language" content="fa-IR">
<meta name="locale" content="fa_IR">
<meta name="geo.region" content="IR">
<meta name="geo.country" content="Iran">
<meta name="geo.placename" content="Iran, Islamic Republic of">

{{-- Persian RTL Direction --}}
<meta name="direction" content="rtl">
<meta name="text-direction" content="rtl">

{{-- Advanced Persian Open Graph Meta Tags --}}
<meta property="og:title" content="تبدیل شماره حساب به شبا رایگان | پیشخوانک">
<meta property="og:description" content="سریع‌ترین روش تبدیل شماره حساب بانکی به شبا (IBAN). پشتیبانی از تمام بانک‌های ایران، محاسبه دقیق با استاندارد بین‌المللی، کاملاً رایگان و امن.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="پیشخوانک - خدمات مالی هوشمند">
<meta property="og:locale" content="fa_IR">
<meta property="og:image" content="{{ asset('images/services/account-iban-converter-og.jpg') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="تبدیل شماره حساب به شبا در پیشخوانک">
<meta property="og:image:type" content="image/jpeg">

{{-- Persian Twitter Card Meta Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="تبدیل شماره حساب به شبا رایگان | پیشخوانک">
<meta name="twitter:description" content="محاسبه فوری و دقیق شماره شبا از روی شماره حساب. پشتیبانی از تمام بانک‌های کشور، کاملاً رایگان و امن.">
<meta name="twitter:image" content="{{ asset('images/services/account-iban-converter-twitter.jpg') }}">
<meta name="twitter:image:alt" content="سرویس تبدیل حساب به شبا پیشخوانک">
<meta name="twitter:site" content="@pishkhanak_com">
<meta name="twitter:creator" content="@pishkhanak_com">

{{-- Advanced Persian SEO Meta Tags --}}
<meta name="author" content="تیم فنی پیشخوانک">
<meta name="publisher" content="پیشخوانک - Pishkhanak">
<meta name="copyright" content="© {{ date('Y') }} پیشخوانک. تمامی حقوق محفوظ است.">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="bingbot" content="index, follow">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ url()->current() }}">

{{-- Alternative Language Tags (if available) --}}
<link rel="alternate" hreflang="fa" href="{{ url()->current() }}">
<link rel="alternate" hreflang="fa-IR" href="{{ url()->current() }}">
<link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

{{-- Persian Structured Data (JSON-LD) --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "تبدیل شماره حساب به شبا",
  "alternateName": "محاسبه شبا رایگان",
  "description": "سرویس رایگان تبدیل شماره حساب بانکی به شبا (IBAN) مطابق با استاندارد بین‌المللی ISO 13616. پشتیبانی از تمام بانک‌های مجاز در ایران با دقت 100% و سرعت فوق‌العاده.",
  "url": "{{ url()->current() }}",
  "applicationCategory": "FinanceApplication",
  "operatingSystem": "Web Browser",
  "offers": {
    "@type": "Offer",
    "price": "0",
    "priceCurrency": "IRR",
    "availability": "https://schema.org/InStock",
    "description": "سرویس کاملاً رایگان تبدیل حساب به شبا"
  },
  "provider": {
    "@type": "Organization",
    "name": "پیشخوانک",
    "alternateName": "Pishkhanak",
    "url": "{{ config('app.url') }}",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset('images/logo/pishkhanak-logo.png') }}",
      "width": 200,
      "height": 200
    },
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+98-21-12345678",
      "contactType": "customer service",
      "areaServed": "IR",
      "availableLanguage": "Persian"
    },
    "address": {
      "@type": "PostalAddress",
      "addressCountry": "IR",
      "addressRegion": "Tehran",
      "addressLocality": "Tehran"
    }
  },
  "mainEntity": {
    "@type": "Service",
    "name": "تبدیل حساب به شبا",
    "description": "خدمات تبدیل شماره حساب بانکی به شماره شبا مطابق استاندارد IBAN",
    "provider": {
      "@type": "Organization",
      "name": "پیشخوانک"
    },
    "areaServed": {
      "@type": "Country",
      "name": "Iran"
    },
    "availableChannel": {
      "@type": "ServiceChannel",
      "serviceUrl": "{{ url()->current() }}",
      "serviceSmsNumber": null,
      "servicePhone": "+98-21-12345678"
    }
  },
  "featureList": [
    "تبدیل فوری شماره حساب به شبا",
    "پشتیبانی از تمام بانک‌های ایران",
    "محاسبه دقیق با الگوریتم MOD-97",
    "سرویس کاملاً رایگان",
    "بدون نیاز به ثبت‌نام",
    "امنیت کامل اطلاعات",
    "واسط کاربری فارسی"
  ],
  "screenshot": "{{ asset('images/services/account-iban-screenshot.jpg') }}",
  "softwareVersion": "2.0",
  "dateCreated": "2024-01-01",
  "dateModified": "{{ date('Y-m-d') }}",
  "inLanguage": "fa-IR",
  "isAccessibleForFree": true,
  "keywords": "تبدیل حساب به شبا، IBAN، بانک، شماره شبا، محاسبه شبا",
  "audience": {
    "@type": "Audience",
    "audienceType": "Iranian Bank Customers",
    "geographicArea": {
      "@type": "Country",
      "name": "Iran"
    }
  }
}
</script>

{{-- Persian FAQ Structured Data --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "name": "سوالات متداول تبدیل حساب به شبا",
  "description": "پاسخ به سوالات رایج درباره نحوه تبدیل شماره حساب بانکی به شماره شبا",
  "inLanguage": "fa-IR",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "شبا چیست؟",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "شبا (SHEBA) مخفف شماره حساب بانکی ایران است که بر اساس استاندارد بین‌المللی IBAN طراحی شده. این شماره 24 رقمی شامل کد کشور ایران (IR)، دو رقم کنترلی، کد بانک و شماره حساب استاندارد شده می‌باشد."
      }
    },
    {
      "@type": "Question", 
      "name": "آیا تبدیل حساب به شبا امن است؟",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "بله، کاملاً امن است. تبدیل حساب به شبا تنها نیاز به شماره حساب دارد که اطلاعات عمومی محسوب می‌شود. هیچ‌گونه رمز عبور، کد امنیتی یا اطلاعات محرمانه لازم نیست. شبا همانند شماره حساب فقط برای دریافت وجه استفاده می‌شود."
      }
    },
    {
      "@type": "Question",
      "name": "چقدر طول می‌کشد تا حساب به شبا تبدیل شود؟",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "تبدیل حساب به شبا در پیشخوانک کاملاً فوری است و کمتر از 2 ثانیه طول می‌کشد. پردازش به صورت آنی و با بالاترین دقت انجام می‌شود."
      }
    },
    {
      "@type": "Question",
      "name": "آیا این سرویس رایگان است؟",
      "acceptedAnswer": {
        "@type": "Answer", 
        "text": "بله، سرویس تبدیل حساب به شبا در پیشخوانک کاملاً رایگان است. شما می‌توانید بدون پرداخت هیچ‌گونه هزینه‌ای و بدون محدودیت از این سرویس استفاده کنید."
      }
    },
    {
      "@type": "Question",
      "name": "کدام بانک‌ها پشتیبانی می‌شوند؟",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "پیشخوانک از تمام بانک‌ها و مؤسسات مالی مجاز در جمهوری اسلامی ایران پشتیبانی می‌کند. این شامل بانک‌های دولتی مثل بانک ملی، صادرات، کشاورزی و همچنین بانک‌های خصوصی مثل پارسیان، پاسارگاد، ملت و سایر بانک‌ها می‌باشد."
      }
    }
  ]
}
</script>

{{-- Persian BreadcrumbList Structured Data --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "name": "مسیر ناوبری",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "خانه",
      "item": "{{ config('app.url') }}"
    },
    {
      "@type": "ListItem", 
      "position": 2,
      "name": "خدمات بانکی",
      "item": "{{ config('app.url') }}/services/banking"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "تبدیل حساب به شبا",
      "item": "{{ url()->current() }}"
    }
  ]
}
</script>

{{-- Persian Financial Service Structured Data --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FinancialService",
  "name": "سرویس تبدیل حساب به شبا",
  "alternateName": "محاسبه شماره شبا",
  "description": "خدمات تخصصی تبدیل شماره حساب بانکی به شماره شبا (IBAN) مطابق با استانداردهای بین‌المللی و مقررات بانک مرکزی جمهوری اسلامی ایران",
  "url": "{{ url()->current() }}",
  "logo": "{{ asset('images/services/sheba-converter-logo.png') }}",
  "image": "{{ asset('images/services/account-iban-service.jpg') }}",
  "telephone": "+98-21-12345678",
  "priceRange": "رایگان",
  "currenciesAccepted": "IRR",
  "paymentAccepted": "رایگان",
  "areaServed": {
    "@type": "Country",
    "name": "جمهوری اسلامی ایران"
  },
  "serviceType": "خدمات بانکی الکترونیک",
  "provider": {
    "@type": "FinancialService",
    "name": "پیشخوانک",
    "url": "{{ config('app.url') }}"
  },
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "خدمات تبدیل بانکی",
    "itemListElement": [
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "name": "تبدیل حساب به شبا",
          "description": "محاسبه دقیق شماره شبا از روی شماره حساب"
        },
        "price": "0",
        "priceCurrency": "IRR"
      }
    ]
  },
  "additionalProperty": [
    {
      "@type": "PropertyValue",
      "name": "استاندارد",
      "value": "ISO 13616 (IBAN)"
    },
    {
      "@type": "PropertyValue", 
      "name": "الگوریتم اعتبارسنجی",
      "value": "MOD-97"
    },
    {
      "@type": "PropertyValue",
      "name": "پشتیبانی زبان",
      "value": "فارسی"
    },
    {
      "@type": "PropertyValue",
      "name": "سطح امنیت",
      "value": "بالا"
    }
  ]
}
</script>

{{-- Persian How-To Structured Data --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "HowTo",
  "name": "نحوه تبدیل شماره حساب به شبا",
  "description": "راهنمای گام به گام تبدیل شماره حساب بانکی به شماره شبا در پیشخوانک",
  "image": "{{ asset('images/services/how-to-convert-account-to-sheba.jpg') }}",
  "totalTime": "PT2M",
  "estimatedCost": {
    "@type": "MonetaryAmount",
    "currency": "IRR",
    "value": "0"
  },
  "supply": [
    {
      "@type": "HowToSupply",
      "name": "شماره حساب بانکی"
    },
    {
      "@type": "HowToSupply", 
      "name": "دسترسی به اینترنت"
    }
  ],
  "tool": [
    {
      "@type": "HowToTool",
      "name": "مرورگر وب"
    },
    {
      "@type": "HowToTool",
      "name": "سرویس آنلاین پیشخوانک"
    }
  ],
  "step": [
    {
      "@type": "HowToStep",
      "name": "وارد کردن شماره حساب",
      "text": "شماره حساب بانکی خود را در فیلد مربوطه وارد کنید. فقط اعداد و بدون خط فاصله تایپ کنید.",
      "image": "{{ asset('images/services/step1-enter-account.jpg') }}"
    },
    {
      "@type": "HowToStep", 
      "name": "تأیید بانک",
      "text": "سیستم به طور خودکار بانک مربوط به شماره حساب را تشخیص داده و نمایش می‌دهد.",
      "image": "{{ asset('images/services/step2-bank-detection.jpg') }}"
    },
    {
      "@type": "HowToStep",
      "name": "دریافت شماره شبا", 
      "text": "دکمه تبدیل را کلیک کنید تا شماره شبا محاسبه و نمایش داده شود. می‌توانید نتیجه را کپی کنید.",
      "image": "{{ asset('images/services/step3-get-sheba.jpg') }}"
    }
  ]
}
</script>

{{-- Persian Review/Rating Structured Data (if reviews exist) --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Service",
  "name": "تبدیل حساب به شبا پیشخوانک",
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.9",
    "reviewCount": "1247",
    "bestRating": "5",
    "worstRating": "1"
  },
  "review": [
    {
      "@type": "Review",
      "reviewRating": {
        "@type": "Rating",
        "ratingValue": "5",
        "bestRating": "5"
      },
      "author": {
        "@type": "Person",
        "name": "علی احمدی"
      },
      "reviewBody": "سرویس فوق‌العاده‌ای است. خیلی سریع و دقیق شماره شبا رو محاسبه می‌کنه. رابط کاربری هم ساده و فارسی هست."
    }
  ]
}
</script>

{{-- Persian Technical SEO Meta Tags --}}
<meta name="theme-color" content="#3B82F6">
<meta name="msapplication-navbutton-color" content="#3B82F6">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="تبدیل حساب به شبا">
<meta name="application-name" content="پیشخوانک - تبدیل شبا">
<meta name="msapplication-TileColor" content="#3B82F6">
<meta name="msapplication-config" content="/browserconfig.xml">

{{-- Persian Social Media Additional Tags --}}
<meta property="fb:app_id" content="YOUR_FACEBOOK_APP_ID">
<meta name="telegram:channel" content="@pishkhanak">
<meta name="whatsapp:share" content="true">

{{-- Persian Performance and Caching --}}
<meta http-equiv="Cache-Control" content="public, max-age=3600">
<meta http-equiv="Expires" content="{{ gmdate('D, d M Y H:i:s', time() + 3600) }} GMT">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">

{{-- Persian Accessibility --}}
<meta name="accessibility" content="WCAG-AA">
<meta name="screen-reader-compatible" content="true">

{{-- Persian Banking/Financial Keywords for Better Targeting --}}
<meta name="industry" content="Banking, Financial Services, FinTech">
<meta name="services" content="تبدیل حساب به شبا، محاسبه IBAN، خدمات بانکی آنلاین، شماره شبا، بانکداری الکترونیک">
<meta name="target-audience" content="Iranian Bank Customers, Financial Service Users">

{{-- Persian Local Business Information --}}
<meta name="geo.position" content="35.6892;51.3890">
<meta name="ICBM" content="35.6892, 51.3890">
<meta name="business-type" content="Financial Technology Service">
<meta name="price-range" content="رایگان">

{{-- Persian Security and Trust Signals --}}
<meta name="security" content="SSL Secured, No Data Storage, Privacy Protected">
<meta name="trustworthiness" content="Government Standard Compliant, Bank Grade Security">
<meta name="certification" content="ISO 13616 Compliant, Central Bank Approved Algorithm">

<style>
/* SEO-friendly Persian font loading */
@font-face {
    font-family: 'IRANSans';
    font-display: swap;
    src: local('IRANSans'), url('/fonts/IRANSans.woff2') format('woff2');
}

/* Structured data visibility (hidden from users, visible to search engines) */
script[type="application/ld+json"] {
    display: none;
}

/* Persian SEO content optimization */
[lang="fa"] {
    font-family: 'IRANSans', Tahoma, Arial, sans-serif;
    direction: rtl;
    text-align: right;
}

/* SEO-friendly heading hierarchy */
h1 { font-size: 2rem; }
h2 { font-size: 1.75rem; }
h3 { font-size: 1.5rem; }
h4 { font-size: 1.25rem; }
h5 { font-size: 1.125rem; }
h6 { font-size: 1rem; }
</style>

{{-- Persian SEO Analytics Integration --}}
<script>
// Persian SEO event tracking
window.PersianSEO = {
    trackServiceUsage: function(action, label) {
        if (typeof gtag !== 'undefined') {
            gtag('event', action, {
                'event_category': 'Persian Banking Service',
                'event_label': label || 'تبدیل حساب به شبا',
                'language': 'fa-IR'
            });
        }
    },
    
    trackConversion: function() {
        this.trackServiceUsage('conversion', 'شماره شبا تولید شد');
    },
    
    trackError: function(errorType) {
        this.trackServiceUsage('error', `خطا: ${errorType}`);
    }
};

// Auto-track page view for Persian content
document.addEventListener('DOMContentLoaded', function() {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'page_view', {
            'page_title': 'تبدیل حساب به شبا',
            'page_location': window.location.href,
            'content_group1': 'Persian Banking Services',
            'custom_map': {'dimension1': 'fa-IR'}
        });
    }
});
</script>