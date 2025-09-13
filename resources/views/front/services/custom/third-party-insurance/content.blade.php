@extends('front.layouts.app')
@section('title', 'بیمه شخص ثالث آنلاین - محاسبه قیمت و خرید فوری')
@section('description', 'محاسبه و خرید بیمه شخص ثالث آنلاین با بهترین قیمت. مقایسه تعرفه بیمه ایران، آسیا و البرز. دریافت فوری بیمه نامه.')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl" dir="rtl">

<!-- SEO Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "بیمه شخص ثالث آنلاین",
    "description": "محاسبه و خرید بیمه شخص ثالث آنلاین با بهترین قیمت و مقایسه نرخ شرکت‌های بیمه",
    "provider": {
        "@type": "Organization",
        "name": "پیشخوانک",
        "url": "https://pishkhanak.com"
    },
    "serviceType": "بیمه خودرو",
    "areaServed": {
        "@type": "Country",
        "name": "Iran"
    },
    "availableChannel": {
        "@type": "ServiceChannel",
        "serviceUrl": "https://pishkhanak.com/services/third-party-insurance",
        "availableLanguage": "fa-IR"
    }
}
</script>

<!-- Table of Contents -->
<section class="bg-white rounded-2xl border border-gray-200 p-6 mb-8 mt-8">
    <h2 class="text-xl font-bold text-dark-sky-700 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
        فهرست مطالب - دسترسی سریع به بخش‌های مختلف
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <a href="#hero-section" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-all duration-200 p-2 rounded hover:bg-blue-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
            <span class="text-sm">معرفی بیمه شخص ثالث</span>
        </a>
        <a href="#insurance-companies" class="flex items-center gap-2 text-green-600 hover:text-green-800 transition-all duration-200 p-2 rounded hover:bg-green-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
            <span class="text-sm">شرکت‌های بیمه معتبر</span>
        </a>
        <a href="#third-party-insurance-main" class="flex items-center gap-2 text-purple-600 hover:text-purple-800 transition-all duration-200 p-2 rounded hover:bg-purple-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
            <span class="text-sm">بیمه شخص ثالث</span>
        </a>
        <a href="#car-insurance-price" class="flex items-center gap-2 text-orange-600 hover:text-orange-800 transition-all duration-200 p-2 rounded hover:bg-orange-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
            <span class="text-sm">قیمت بیمه ماشین</span>
        </a>
        <a href="#car-insurance-calculation" class="flex items-center gap-2 text-red-600 hover:text-red-800 transition-all duration-200 p-2 rounded hover:bg-red-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
            <span class="text-sm">محاسبه بیمه خودرو</span>
        </a>
        <a href="#comprehensive-insurance" class="flex items-center gap-2 text-teal-600 hover:text-teal-800 transition-all duration-200 p-2 rounded hover:bg-teal-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
            <span class="text-sm">بیمه بدنه</span>
        </a>
        <a href="#insurance-discount" class="flex items-center gap-2 text-pink-600 hover:text-pink-800 transition-all duration-200 p-2 rounded hover:bg-pink-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-pink-500 rounded-full"></span>
            <span class="text-sm">تخفیف بیمه</span>
        </a>
        <a href="#iran-insurance" class="flex items-center gap-2 text-yellow-600 hover:text-yellow-800 transition-all duration-200 p-2 rounded hover:bg-yellow-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
            <span class="text-sm">بیمه ایران</span>
        </a>
        <a href="#asia-insurance" class="flex items-center gap-2 text-cyan-600 hover:text-cyan-800 transition-all duration-200 p-2 rounded hover:bg-cyan-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-cyan-500 rounded-full"></span>
            <span class="text-sm">بیمه آسیا</span>
        </a>
        <a href="#alborz-insurance" class="flex items-center gap-2 text-emerald-600 hover:text-emerald-800 transition-all duration-200 p-2 rounded hover:bg-emerald-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
            <span class="text-sm">بیمه البرز</span>
        </a>
        <a href="#insurance-inquiry" class="flex items-center gap-2 text-rose-600 hover:text-rose-800 transition-all duration-200 p-2 rounded hover:bg-rose-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-rose-500 rounded-full"></span>
            <span class="text-sm">استعلام بیمه</span>
        </a>
        <a href="#motorcycle-insurance" class="flex items-center gap-2 text-indigo-600 hover:text-indigo-800 transition-all duration-200 p-2 rounded hover:bg-indigo-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
            <span class="text-sm">بیمه موتور سیکلت</span>
        </a>
        <a href="#faqs" class="flex items-center gap-2 text-gray-600 hover:text-gray-800 transition-all duration-200 p-2 rounded hover:bg-gray-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
            <span class="text-sm">سوالات متداول</span>
        </a>
        <a href="#related-services" class="flex items-center gap-2 text-violet-600 hover:text-violet-800 transition-all duration-200 p-2 rounded hover:bg-violet-50 hover:shadow-sm">
            <span class="w-2 h-2 bg-violet-500 rounded-full"></span>
            <span class="text-sm">خدمات مرتبط</span>
        </a>
    </div>
    
    <div class="mt-4 p-3 bg-sky-50 rounded-lg border border-sky-200">
        <p class="text-sm text-sky-700 flex items-start gap-2">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>برای دسترسی سریع به هر بخش، روی عنوان مورد نظر کلیک کنید. این صفحه شامل راهنمای کاملی از محاسبه و خرید بیمه شخص ثالث آنلاین است.</span>
        </p>
    </div>
</section>

<!-- Hero Section -->
<section id="hero-section" class="bg-gradient-to-l from-sky-50 via-blue-50 to-indigo-50 rounded-3xl p-8 relative overflow-hidden mt-12 mb-12">
    <div class="absolute top-0 left-0 w-full h-full opacity-5">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" preserveAspectRatio="xMidYMid slice">
            <pattern id="insurance-pattern" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                <circle cx="50" cy="50" r="5" fill="currentColor"/>
            </pattern>
            <rect width="100%" height="100%" fill="url(#insurance-pattern)"/>
        </svg>
    </div>
    
    <div class="max-w-4xl mx-auto relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-sky-600 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-dark-sky-700">بیمه شخص ثالث آنلاین - محاسبه قیمت و خرید فوری</h1>
        </div>
        
        <p class="text-gray-700 leading-relaxed mb-6 text-lg">
            <strong>بیمه شخص ثالث</strong> یکی از <em>ضروری‌ترین بیمه‌های خودرو</em> است که طبق قانون، تمامی رانندگان ایرانی موظف به داشتن آن هستند. 
            از طریق سامانه پیشخوانک می‌توانید <a href="/services/third-party-insurance-history" class="text-blue-600 hover:text-blue-800 underline transition-colors">سابقه بیمه شخص ثالث</a> خود را بررسی کرده و
            همچنین <a href="/services/vehicle-insurance-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">وضعیت بیمه خودرو</a> را استعلام نمایید.
            علاوه بر این، امکان <a href="/services/vehicle-technical-diagnosis" class="text-blue-600 hover:text-blue-800 underline transition-colors">تشخیص فنی خودرو</a> و
            <a href="/services/negative-license-score" class="text-blue-600 hover:text-blue-800 underline transition-colors">امتیاز منفی گواهینامه</a> نیز در دسترس شما قرار دارد.
        </p>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-sky-600">۲۴/۷</div>
                <div class="text-sm text-gray-600 mt-1">پشتیبانی آنلاین</div>
            </div>
            <div class="bg-white rounded-2xl p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-emerald-600">۱۵</div>
                <div class="text-sm text-gray-600 mt-1">شرکت بیمه</div>
            </div>
            <div class="bg-white rounded-2xl p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-orange-600">۵ دقیقه</div>
                <div class="text-sm text-gray-600 mt-1">صدور فوری</div>
            </div>
            <div class="bg-white rounded-2xl p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-purple-600">۱۰۰٪</div>
                <div class="text-sm text-gray-600 mt-1">تضمین کیفیت</div>
            </div>
        </div>
    </div>
</section>

<!-- Insurance Companies Section -->
<section id="insurance-companies" class="mt-12 mb-12">
    <h2 class="text-2xl font-bold text-dark-sky-700 mb-6">شرکت‌های بیمه معتبر و قابل اعتماد</h2>
    
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            در سامانه پیشخوانک امکان <strong>مقایسه قیمت بیمه شخص ثالث</strong> از تمامی <em>شرکت‌های بیمه معتبر کشور</em> فراهم است.
            شما می‌توانید با استفاده از <a href="/services/insurance-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">سرویس استعلام بیمه</a>
            وضعیت بیمه فعلی خود را بررسی و سپس از میان گزینه‌های مختلف، بهترین انتخاب را داشته باشید.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200">
                <h4 class="font-bold text-blue-800 mb-2">بیمه‌های دولتی</h4>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• بیمه ایران</li>
                    <li>• بیمه آسیا</li>
                    <li>• بیمه البرز</li>
                </ul>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 border border-green-200">
                <h4 class="font-bold text-green-800 mb-2">بیمه‌های خصوصی</h4>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• بیمه پارسیان</li>
                    <li>• بیمه دانا</li>
                    <li>• بیمه کوثر</li>
                </ul>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-200">
                <h4 class="font-bold text-purple-800 mb-2">بیمه‌های تخصصی</h4>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• بیمه رازی</li>
                    <li>• بیمه نوین</li>
                    <li>• بیمه سامان</li>
                </ul>
            </div>
        </div>

        <p class="text-gray-700 leading-relaxed">
            همچنین امکان بررسی <a href="/services/traffic-violation-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">تخلفات رانندگی</a>،
            <a href="/services/toll-road-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">عوارض آزادراهی</a> و
            <a href="/services/traffic-plan-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">وضعیت طرح ترافیک</a>
            نیز برای کاربران فراهم شده است.
        </p>
    </div>
</section>

<!-- Keyword Section 1: بیمه شخص ثالث -->
<section id="third-party-insurance-main" class="mt-12 mb-12">
    <h2 class="text-2xl font-bold text-dark-sky-700 mb-6">بیمه شخص ثالث - مهمترین پوشش حقوقی خودرو</h2>
    
    <div class="bg-gradient-to-br from-blue-50 to-sky-50 rounded-2xl p-6 mb-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            <strong>بیمه شخص ثالث</strong> به عنوان <em>اجباری‌ترین نوع بیمه خودرو</em> در ایران، پوشش کاملی برای جبران خسارات وارده به اشخاص ثالث ارائه می‌دهد.
            این بیمه شامل پوشش <strong>خسارات جانی و مالی</strong> ناشی از تصادفات رانندگی است و قانوناً تمامی خودروها باید دارای این بیمه باشند.
            با استفاده از سامانه پیشخوانک می‌توانید <a href="/services/vehicle-registration-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">وضعیت سند خودرو</a>
            و <a href="/services/vehicle-owner-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">اطلاعات مالک خودرو</a> را نیز بررسی کنید.
            همچنین امکان <a href="/services/vehicle-theft-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">استعلام سرقتی بودن خودرو</a>
            و <a href="/services/vehicle-mortgage-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">وثیقه و رهن خودرو</a> در دسترس است.
        </p>
        
        <ul class="list-disc mr-6 text-gray-700 mb-4">
            <li><strong>پوشش خسارات جانی</strong> - تا ۲ میلیارد تومان برای هر نفر</li>
            <li><em>پوشش خسارات مالی</em> - شامل تعمیر و جایگزینی اموال آسیب دیده</li>
            <li><strong>پوشش دائمی</strong> - در تمام ساعات شبانه روز و نقاط کشور</li>
            <li><em>قابلیت انتقال</em> - امکان انتقال بیمه به خودروی جدید</li>
        </ul>
        
        <dl class="mr-6 mb-4 text-gray-700">
            <dt class="font-bold mb-2">مزایای بیمه شخص ثالث:</dt>
            <dd class="mb-4 mr-4">حفاظت کامل در برابر مطالبات حقوقی، پوشش هزینه‌های پزشکی و درمانی، جبران خسارات مالی طرف مقابل</dd>
        </dl>
    </div>
</section>

<!-- Keyword Section 2: قیمت بیمه ماشین -->
<section id="car-insurance-price" class="mt-12 mb-12">
    <h2 class="text-2xl font-bold text-dark-sky-700 mb-6">قیمت بیمه ماشین - راهنمای تعرفه‌های ۱۴۰۳</h2>
    
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 mb-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            <strong>قیمت بیمه ماشین</strong> بر اساس <em>عوامل مختلفی</em> نظیر نوع خودرو، سال ساخت، سابقه بیمه و منطقه جغرافیایی محاسبه می‌شود.
            در سال ۱۴۰۳ تعرفه‌های جدید توسط بیمه مرکزی ابلاغ شده و شرکت‌های مختلف نرخ‌های متفاوتی ارائه می‌دهند.
            برای بررسی دقیق‌تر می‌توانید <a href="/services/vehicle-valuation-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">ارزش روز خودرو</a>
            و <a href="/services/vehicle-technical-diagnosis" class="text-blue-600 hover:text-blue-800 underline transition-colors">معاینه فنی خودرو</a> را استعلام کنید.
            همچنین <a href="/services/vehicle-fuel-card-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">وضعیت کارت سوخت</a>
            نیز در تعیین قیمت نهایی تأثیرگذار است.
        </p>
        
        <div class="bg-white rounded-xl p-4 border border-green-200 mb-4">
            <h4 class="font-bold text-green-800 mb-3">عوامل تأثیرگذار بر قیمت:</h4>
            <ul class="list-decimal mr-6 text-gray-700">
                <li><strong>نوع و مدل خودرو</strong> - خودروهای لوکس قیمت بالاتر</li>
                <li><em>سال ساخت</em> - خودروهای جدیدتر حق بیمه بیشتر</li>
                <li><strong>سابقه خسارت</strong> - تأثیر مستقیم بر نرخ نهایی</li>
                <li><em>منطقه جغرافیایی</em> - شهرهای پرتردد نرخ بالاتر</li>
                <li><strong>تخفیف عدم خسارت</strong> - تا ۶۰ درصد تخفیف</li>
            </ul>
        </div>

        <p class="text-gray-700 leading-relaxed">
            برای محاسبه دقیق قیمت، <a href="/services/insurance-calculator" class="text-blue-600 hover:text-blue-800 underline transition-colors">ماشین حساب بیمه</a>
            را استفاده کرده و با <a href="/services/insurance-comparison" class="text-blue-600 hover:text-blue-800 underline transition-colors">مقایسه قیمت بیمه</a>
            بهترین انتخاب را داشته باشید.
        </p>
    </div>
</section>

<!-- Keyword Section 3: محاسبه بیمه خودرو -->
<section id="car-insurance-calculation" class="mt-12 mb-12">
    <h2 class="text-2xl font-bold text-dark-sky-700 mb-6">محاسبه بیمه خودرو - فرمول‌ها و ضرایب کاربردی</h2>
    
    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 mb-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            <strong>محاسبه بیمه خودرو</strong> بر اساس <em>فرمول‌های مصوب بیمه مرکزی</em> و ضرایب مختلف انجام می‌شود. این محاسبه شامل ارزش خودرو، 
            نرخ پایه بیمه، ضریب منطقه‌ای و تخفیفات قابل اعمال است. برای اطلاع از وضعیت دقیق خودرو می‌توانید 
            <a href="/services/vehicle-identification" class="text-blue-600 hover:text-blue-800 underline transition-colors">شناسایی خودرو با پلاک</a>
            و <a href="/services/vehicle-specs-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">مشخصات فنی خودرو</a> را بررسی کنید.
            همچنین <a href="/services/vehicle-accident-history" class="text-blue-600 hover:text-blue-800 underline transition-colors">سابقه تصادفات</a>
            نیز در محاسبه نهایی اهمیت دارد.
        </p>
        
        <div class="relative max-w-4xl mx-auto mb-4">
            <div class="absolute right-6 top-0 bottom-0 w-1 bg-gradient-to-b from-purple-400 to-pink-400 rounded-full opacity-30"></div>
            
            <div class="space-y-6">
                <div class="flex gap-6 relative">
                    <div class="flex-shrink-0 relative z-10">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-full flex items-center justify-center font-bold text-sm shadow-lg ring-4 ring-purple-100">
                            ۱
                        </div>
                    </div>
                    <div class="flex-1 bg-white rounded-xl p-5 border border-purple-200">
                        <h5 class="font-bold text-purple-800 mb-2">تعیین ارزش خودرو</h5>
                        <p class="text-gray-700 text-sm">بر اساس قیمت روز و سال ساخت</p>
                    </div>
                </div>
                
                <div class="flex gap-6 relative">
                    <div class="flex-shrink-0 relative z-10">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 text-white rounded-full flex items-center justify-center font-bold text-sm shadow-lg ring-4 ring-pink-100">
                            ۲
                        </div>
                    </div>
                    <div class="flex-1 bg-white rounded-xl p-5 border border-pink-200">
                        <h5 class="font-bold text-pink-800 mb-2">اعمال ضرایب</h5>
                        <p class="text-gray-700 text-sm">ضریب منطقه، نوع کاربری و مدل خودرو</p>
                    </div>
                </div>
                
                <div class="flex gap-6 relative">
                    <div class="flex-shrink-0 relative z-10">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 text-white rounded-full flex items-center justify-center font-bold text-sm shadow-lg ring-4 ring-purple-100">
                            ۳
                        </div>
                    </div>
                    <div class="flex-1 bg-white rounded-xl p-5 border border-purple-200">
                        <h5 class="font-bold text-purple-800 mb-2">محاسبه تخفیفات</h5>
                        <p class="text-gray-700 text-sm">تخفیف عدم خسارت و سایر تخفیفات</p>
                    </div>
                </div>
            </div>
        </div>

        <dl class="mr-6 text-gray-700">
            <dt class="font-bold mb-2">فرمول محاسبه:</dt>
            <dd class="mb-4 mr-4">حق بیمه = (ارزش خودرو × نرخ پایه × ضرایب) - تخفیفات</dd>
        </dl>
    </div>
</section>

<!-- Keyword Section 4: بیمه بدنه -->
<section id="comprehensive-insurance" class="mt-12 mb-12">
    <h2 class="text-2xl font-bold text-dark-sky-700 mb-6">بیمه بدنه - حفاظت کامل از خودروی شما</h2>
    
    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-6 mb-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            <strong>بیمه بدنه</strong> یا بیمه جامع خودرو، <em>پوشش کاملی</em> برای خسارات وارده به خود خودروی بیمه‌گذار ارائه می‌دهد. 
            این نوع بیمه علاوه بر پوشش‌های بیمه شخص ثالث، شامل تعمیر و جایگزینی قطعات آسیب دیده خودرو نیز می‌شود.
            برای تصمیم‌گیری بهتر می‌توانید <a href="/services/vehicle-damage-assessment" class="text-blue-600 hover:text-blue-800 underline transition-colors">ارزیابی خسارت خودرو</a>
            و <a href="/services/vehicle-repair-cost" class="text-blue-600 hover:text-blue-800 underline transition-colors">هزینه تعمیرات</a> را بررسی کنید.
            همچنین <a href="/services/vehicle-parts-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">قیمت قطعات یدکی</a>
            نیز در انتخاب میزان پوشش تأثیرگذار است.
        </p>
        
        <ul class="list-disc mr-6 text-gray-700 mb-4">
            <li><strong>پوشش تصادفات</strong> - شامل برخورد، واژگونی و سقوط</li>
            <li><em>پوشش سرقت کامل</em> - جبران کامل ارزش خودرو</li>
            <li><strong>پوشش آتش‌سوزی</strong> - خسارات ناشی از آتش و انفجار</li>
            <li><em>پوشش بلایای طبیعی</em> - سیل، زلزله و طوفان</li>
            <li><strong>پوشش شیشه‌ها</strong> - تعویض شیشه‌های آسیب دیده</li>
            <li><em>خدمات جانبی</em> - امداد جاده‌ای و خدمات فوری</li>
        </ul>
        
        <div class="bg-white rounded-xl p-4 border border-orange-200">
            <h4 class="font-bold text-orange-800 mb-2">انواع بیمه بدنه:</h4>
            <p class="text-gray-700 text-sm">
                بیمه بدنه شامل <strong>پوشش کامل</strong>، <em>پوشش محدود</em> و <strong>پوشش اقتصادی</strong> است که هر یک 
                میزان پوشش و فرانشیز متفاوتی دارند.
            </p>
        </div>
    </div>
</section>

<!-- Keyword Section 5: تخفیف بیمه -->
<section id="insurance-discount" class="mt-12 mb-12">
    <h2 class="text-2xl font-bold text-dark-sky-700 mb-6">تخفیف بیمه - روش‌های قانونی کاهش حق بیمه</h2>
    
    <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-2xl p-6 mb-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            <strong>تخفیف بیمه</strong> یکی از مهم‌ترین عوامل در کاهش هزینه‌های سالانه خودرو است. <em>تخفیف عدم خسارت</em> به عنوان 
            اصلی‌ترین نوع تخفیف، تا ۶۰ درصد از حق بیمه را کاهش می‌دهد. برای بررسی وضعیت تخفیف خود می‌توانید 
            <a href="/services/insurance-discount-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">استعلام تخفیف بیمه</a>
            و <a href="/services/insurance-record-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">سابقه بیمه</a> را بررسی کنید.
            همچنین <a href="/services/driver-record-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">سابقه رانندگی</a>
            نیز در میزان تخفیف دریافتی نقش دارد.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white rounded-xl p-4 border border-teal-200">
                <h5 class="font-bold text-teal-800 mb-3">تخفیفات اصلی:</h5>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li><strong>۱ سال عدم خسارت:</strong> ۱۰٪ تخفیف</li>
                    <li><em>۲ سال عدم خسارت:</em> ۲۰٪ تخفیف</li>
                    <li><strong>۳ سال عدم خسارت:</strong> ۳۰٪ تخفیف</li>
                    <li><em>۴ سال عدم خسارت:</em> ۴۰٪ تخفیف</li>
                    <li><strong>۵+ سال عدم خسارت:</strong> ۶۰٪ تخفیف</li>
                </ul>
            </div>
            <div class="bg-white rounded-xl p-4 border border-cyan-200">
                <h5 class="font-bold text-cyan-800 mb-3">تخفیفات ویژه:</h5>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li><strong>تخفیف گروهی:</strong> کارکنان ادارات</li>
                    <li><em>تخفیف خانوادگی:</em> بیمه چند خودرو</li>
                    <li><strong>تخفیف سنی:</strong> رانندگان با تجربه</li>
                    <li><em>تخفیف تحصیلی:</em> مدارک تحصیلی</li>
                </ul>
            </div>
        </div>

        <dl class="mr-6 text-gray-700">
            <dt class="font-bold mb-2">نکات مهم تخفیف بیمه:</dt>
            <dd class="mb-4 mr-4">تخفیف عدم خسارت قابل انتقال به شرکت‌های مختلف بیمه است و در صورت بروز خسارت، 
            بخشی از تخفیف از دست می‌رود.</dd>
        </dl>
    </div>
</section>

<!-- Keyword Section 6: بیمه ایران -->
<section id="iran-insurance" class="mt-12 mb-12">
    <h2 class="text-2xl font-bold text-dark-sky-700 mb-6">بیمه ایران - قدیمی‌ترین و معتبرترین شرکت بیمه</h2>
    
    <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl p-6 mb-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            <strong>بیمه ایران</strong> به عنوان <em>اولین و بزرگ‌ترین شرکت بیمه کشور</em>، بیش از ۸۰ سال سابقه فعالیت در صنعت بیمه دارد.
            این شرکت با شبکه گسترده نمایندگی‌ها در سراسر کشور، خدمات متنوع بیمه‌ای ارائه می‌دهد. برای اطلاع از جزئیات بیشتر می‌توانید
            <a href="/services/iran-insurance-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">استعلام بیمه ایران</a>
            و <a href="/services/iran-insurance-branches" class="text-blue-600 hover:text-blue-800 underline transition-colors">شعب بیمه ایران</a> را بررسی کنید.
            همچنین <a href="/services/iran-insurance-claims" class="text-blue-600 hover:text-blue-800 underline transition-colors">وضعیت خسارت</a>
            و <a href="/services/iran-insurance-policies" class="text-blue-600 hover:text-blue-800 underline transition-colors">بیمه‌نامه‌های فعال</a> نیز قابل پیگیری است.
        </p>
        
        <ul class="list-disc mr-6 text-gray-700 mb-4">
            <li><strong>سابقه ۸۰ ساله</strong> - تجربه طولانی در صنعت بیمه</li>
            <li><em>پوشش سراسری</em> - نمایندگی در تمام شهرهای کشور</li>
            <li><strong>تنوع محصولات</strong> - انواع بیمه‌های فردی و تجاری</li>
            <li><em>پشتیبانی ۲۴ ساعته</em> - خدمات اورژانسی و امدادی</li>
            <li><strong>نرخ‌های رقابتی</strong> - قیمت‌گذاری منصفانه</li>
        </ul>
        
        <div class="bg-white rounded-xl p-4 border border-yellow-200">
            <h4 class="font-bold text-yellow-800 mb-2">مزایای انتخاب بیمه ایران:</h4>
            <p class="text-gray-700 text-sm">
                <strong>اعتبار بالا</strong> در نزد مراجع قضایی، <em>سرعت در تسویه خسارت</em>، 
                امکان پرداخت اقساطی حق بیمه و ارائه خدمات مشاوره‌ای تخصصی.
            </p>
        </div>
    </div>
</section>

<!-- Keyword Section 7: بیمه آسیا -->
<section id="asia-insurance" class="mt-12 mb-12">
    <h2 class="text-2xl font-bold text-dark-sky-700 mb-6">بیمه آسیا - نوآوری در خدمات بیمه‌ای</h2>
    
    <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl p-6 mb-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            <strong>بیمه آسیا</strong> به عنوان یکی از <em>پیشگامان نوآوری در صنعت بیمه</em>، خدمات مدرن و کیفیت بالا ارائه می‌دهد.
            این شرکت با بهره‌گیری از فناوری‌های روز، فرآیندهای بیمه‌گری را ساده‌تر و سریع‌تر کرده است. برای دسترسی به خدمات می‌توانید
            <a href="/services/asia-insurance-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">استعلام بیمه آسیا</a>
            و <a href="/services/asia-insurance-online" class="text-blue-600 hover:text-blue-800 underline transition-colors">خدمات آنلاین آسیا</a> را استفاده کنید.
            همچنین <a href="/services/asia-insurance-mobile" class="text-blue-600 hover:text-blue-800 underline transition-colors">اپلیکیشن موبایل</a>
            و <a href="/services/asia-insurance-support" class="text-blue-600 hover:text-blue-800 underline transition-colors">پشتیبانی تلفنی</a> در دسترس است.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-white rounded-xl p-4 border border-cyan-200 text-center">
                <div class="w-12 h-12 bg-cyan-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h5 class="font-bold text-cyan-800 mb-1">خدمات دیجیتال</h5>
                <p class="text-xs text-gray-600">اپلیکیشن و سامانه آنلاین</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-blue-200 text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h5 class="font-bold text-blue-800 mb-1">تسویه سریع</h5>
                <p class="text-xs text-gray-600">پردازش فوری خسارات</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-sky-200 text-center">
                <div class="w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h5 class="font-bold text-sky-800 mb-1">ضمانت کیفیت</h5>
                <p class="text-xs text-gray-600">استانداردهای بین‌المللی</p>
            </div>
        </div>

        <dl class="mr-6 text-gray-700">
            <dt class="font-bold mb-2">نوآوری‌های بیمه آسیا:</dt>
            <dd class="mb-4 mr-4">استفاده از هوش مصنوعی در ارزیابی خسارت، سامانه آنلاین جامع، 
            اپلیکیشن موبایل پیشرفته و خدمات مشتری دیجیتال.</dd>
        </dl>
    </div>
</section>

<!-- Keyword Section 8: بیمه البرز -->
<section id="alborz-insurance" class="mt-12 mb-12">
    <h2 class="text-2xl font-bold text-dark-sky-700 mb-6">بیمه البرز - تخصص در بیمه‌های خودرو</h2>
    
    <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl p-6 mb-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            <strong>بیمه البرز</strong> با <em>تخصص ویژه در حوزه بیمه‌های خودرو</em>، یکی از انتخاب‌های مناسب برای رانندگان ایرانی محسوب می‌شود.
            این شرکت با ارائه نرخ‌های رقابتی و خدمات تخصصی، جایگاه مناسبی در بازار بیمه کسب کرده است. 
            برای کسب اطلاعات بیشتر می‌توانید <a href="/services/alborz-insurance-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">استعلام بیمه البرز</a>
            و <a href="/services/alborz-insurance-rates" class="text-blue-600 hover:text-blue-800 underline transition-colors">تعرفه‌های البرز</a> را بررسی کنید.
            همچنین <a href="/services/alborz-insurance-coverage" class="text-blue-600 hover:text-blue-800 underline transition-colors">پوشش‌های ویژه</a>
            و <a href="/services/alborz-insurance-services" class="text-blue-600 hover:text-blue-800 underline transition-colors">خدمات جانبی</a> در دسترس است.
        </p>
        
        <ul class="list-disc mr-6 text-gray-700 mb-4">
            <li><strong>تخصص در خودرو</strong> - سال‌ها تجربه در بیمه‌های اتومبیل</li>
            <li><em>نرخ‌های مناسب</em> - قیمت‌گذاری رقابتی و منطقی</li>
            <li><strong>خدمات سریع</strong> - پردازش فوری درخواست‌ها</li>
            <li><em>پوشش جامع</em> - انواع بیمه‌های مکمل</li>
            <li><strong>شبکه خدماتی</strong> - نمایندگی‌ها در شهرهای مهم</li>
        </ul>
        
        <div class="bg-white rounded-xl p-4 border border-emerald-200">
            <h4 class="font-bold text-emerald-800 mb-2">ویژگی‌های منحصر به فرد:</h4>
            <p class="text-gray-700 text-sm">
                <strong>بیمه البرز</strong> با ارائه <em>بسته‌های ویژه خودرو</em> و خدمات مشاوره‌ای رایگان، 
                انتخاب مناسبی برای رانندگانی است که به دنبال کیفیت و قیمت مناسب هستند.
            </p>
        </div>
    </div>
</section>

<!-- Keyword Section 9: استعلام بیمه -->
<section id="insurance-inquiry" class="mt-12 mb-12">
    <h2 class="text-2xl font-bold text-dark-sky-700 mb-6">استعلام بیمه - بررسی وضعیت و سابقه بیمه</h2>
    
    <div class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl p-6 mb-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            <strong>استعلام بیمه</strong> امکان <em>بررسی وضعیت فعلی و سابقه بیمه</em> خودرو را فراهم می‌کند. این سرویس شامل 
            اطلاعات بیمه‌نامه فعال، تاریخ انقضا، میزان تخفیف عدم خسارت و سابقه خسارات است.
            برای استعلام کامل می‌توانید <a href="/services/insurance-status-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">وضعیت بیمه</a>،
            <a href="/services/insurance-expiry-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">تاریخ انقضای بیمه</a>
            و <a href="/services/insurance-history-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">تاریخچه بیمه</a> را بررسی کنید.
            همچنین <a href="/services/insurance-claims-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">سابقه خسارات</a>
            نیز قابل استعلام است.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white rounded-xl p-4 border border-rose-200">
                <h5 class="font-bold text-rose-800 mb-3">اطلاعات قابل استعلام:</h5>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• <strong>وضعیت بیمه‌نامه فعال</strong></li>
                    <li>• <em>تاریخ شروع و انقضا</em></li>
                    <li>• <strong>نوع پوشش بیمه‌ای</strong></li>
                    <li>• <em>شرکت بیمه‌گر</em></li>
                    <li>• <strong>میزان تخفیف عدم خسارت</strong></li>
                </ul>
            </div>
            <div class="bg-white rounded-xl p-4 border border-pink-200">
                <h5 class="font-bold text-pink-800 mb-3">سوابق قابل بررسی:</h5>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• <strong>سابقه خسارات</strong></li>
                    <li>• <em>تعداد سال‌های عدم خسارت</em></li>
                    <li>• <strong>تغییرات شرکت بیمه</strong></li>
                    <li>• <em>نوع بیمه‌های قبلی</em></li>
                    <li>• <strong>وضعیت پرداخت حق بیمه</strong></li>
                </ul>
            </div>
        </div>

        <dl class="mr-6 text-gray-700">
            <dt class="font-bold mb-2">کاربردهای استعلام بیمه:</dt>
            <dd class="mb-4 mr-4">قبل از خرید خودروی دست دوم، تمدید بیمه، تغییر شرکت بیمه 
            و دریافت تخفیف عدم خسارت از استعلام بیمه استفاده کنید.</dd>
        </dl>
    </div>
</section>

<!-- Keyword Section 10: بیمه موتور سیکلت -->
<section id="motorcycle-insurance" class="mt-12 mb-12">
    <h2 class="text-2xl font-bold text-dark-sky-700 mb-6">بیمه موتور سیکلت - حفاظت قانونی از موتورسواران</h2>
    
    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 mb-6">
        <p class="text-gray-700 leading-relaxed mb-4">
            <strong>بیمه موتور سیکلت</strong> مانند بیمه خودرو، <em>اجباری و ضروری</em> برای تمامی موتورسواران ایرانی است. 
            این بیمه پوشش خسارات وارده به اشخاص ثالث در اثر تصادفات موتور سیکلت را بر عهده می‌گیرد.
            برای کسب اطلاعات بیشتر می‌توانید <a href="/services/motorcycle-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">استعلام موتور سیکلت</a>،
            <a href="/services/motorcycle-registration" class="text-blue-600 hover:text-blue-800 underline transition-colors">وضعیت ثبت موتور</a>
            و <a href="/services/motorcycle-violations" class="text-blue-600 hover:text-blue-800 underline transition-colors">تخلفات موتور سیکلت</a> را بررسی کنید.
            همچنین <a href="/services/motorcycle-license-inquiry" class="text-blue-600 hover:text-blue-800 underline transition-colors">گواهینامه موتور سیکلت</a>
            نیز قابل استعلام است.
        </p>
        
        <ul class="list-disc mr-6 text-gray-700 mb-4">
            <li><strong>اجباری بودن</strong> - طبق قانون راهنمایی و رانندگی</li>
            <li><em>پوشش جانی</em> - تا ۵۰۰ میلیون تومان برای هر نفر</li>
            <li><strong>پوشش مالی</strong> - جبران خسارات مالی طرف مقابل</li>
            <li><em>نرخ‌های مناسب</em> - قیمت پایین‌تر نسبت به خودرو</li>
            <li><strong>صدور آنلاین</strong> - امکان خرید اینترنتی</li>
        </ul>
        
        <div class="bg-white rounded-xl p-4 border border-indigo-200">
            <h4 class="font-bold text-indigo-800 mb-2">ویژگی‌های بیمه موتور:</h4>
            <p class="text-gray-700 text-sm">
                <strong>بیمه موتور سیکلت</strong> علاوه بر <em>پوشش‌های اجباری</em>، 
                امکان اضافه کردن پوشش‌های اختیاری مانند بیمه حوادث شخصی و بیمه سرقت را نیز دارد.
            </p>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faqs" class="bg-white rounded-3xl shadow-lg p-8" dir="rtl">
    <div class="max-w-6xl mx-auto">
        <!-- FAQ Header -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-dark-sky-700 mb-4">سوالات متداول بیمه شخص ثالث</h2>
            <p class="text-gray-600 text-lg">
                <strong>پیشخوانک</strong> پاسخ سوالات رایج شما درباره بیمه شخص ثالث، قیمت‌گذاری و خرید آنلاین
            </p>
            
            <!-- Search Box -->
            <div class="relative max-w-2xl mx-auto mt-6">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="faq-search" 
                       class="block w-full pr-10 pl-4 py-3 border border-gray-300 rounded-full leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-sky-500 focus:border-transparent text-right" 
                       placeholder="جستجو در سوالات متداول...">
            </div>
        </div>

        <!-- FAQ Categories -->
        <div class="flex flex-wrap justify-center gap-2 mb-8">
            <button class="faq-category-btn active bg-sky-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-sky-700 transition-colors" data-category="all">همه سوالات</button>
            <button class="faq-category-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-300 transition-colors" data-category="basic">مفاهیم پایه</button>
            <button class="faq-category-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-300 transition-colors" data-category="calculation">محاسبه قیمت</button>
            <button class="faq-category-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-300 transition-colors" data-category="companies">شرکت‌های بیمه</button>
            <button class="faq-category-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-300 transition-colors" data-category="purchase">خرید و تمدید</button>
        </div>

        <!-- FAQ Items -->
        <div class="space-y-4" id="faq-container">
            <!-- FAQ 1 -->
            <div class="faq-item bg-gradient-to-l from-blue-50 to-sky-50 rounded-2xl border border-blue-200" data-category="basic">
                <button class="faq-toggle w-full text-right p-6 flex justify-between items-center focus:outline-none">
                    <span class="text-lg font-semibold text-blue-900">بیمه شخص ثالث چیست و چرا اجباری است؟</span>
                    <svg class="faq-icon w-6 h-6 text-blue-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-6 text-gray-700 leading-relaxed">
                    <p class="mb-3">
                        <strong>بیمه شخص ثالث</strong> نوعی بیمه مسئولیت مدنی است که طبق قانون، تمامی خودروها باید دارای آن باشند. 
                        این بیمه <em>خسارات وارده به اشخاص ثالث</em> در اثر تصادفات را جبران می‌کند.
                    </p>
                    <p>
                        دلیل اجباری بودن این بیمه، <strong>حمایت از حقوق شهروندان</strong> و تضمین جبران خسارات احتمالی است.
                        برای کسب اطلاعات بیشتر می‌توانید <a href="/services/legal-vehicle-requirements" class="text-blue-600 hover:text-blue-800 underline">الزامات قانونی خودرو</a> را مطالعه کنید.
                    </p>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="faq-item bg-gradient-to-l from-green-50 to-emerald-50 rounded-2xl border border-green-200" data-category="calculation">
                <button class="faq-toggle w-full text-right p-6 flex justify-between items-center focus:outline-none">
                    <span class="text-lg font-semibold text-green-900">قیمت بیمه شخص ثالث چگونه محاسبه می‌شود؟</span>
                    <svg class="faq-icon w-6 h-6 text-green-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-6 text-gray-700 leading-relaxed">
                    <p class="mb-3">
                        قیمت بیمه شخص ثالث بر اساس <strong>عوامل مختلفی</strong> محاسبه می‌شود:
                    </p>
                    <ul class="list-disc mr-6 mb-3">
                        <li><em>نوع و مدل خودرو</em> - تأثیر بر ضریب پایه</li>
                        <li><strong>سال ساخت</strong> - خودروهای جدیدتر قیمت بالاتر</li>
                        <li><em>منطقه جغرافیایی</em> - ضریب شهرهای مختلف</li>
                        <li><strong>تخفیف عدم خسارت</strong> - تا ۶۰ درصد کاهش</li>
                    </ul>
                    <p>
                        برای محاسبه دقیق از <a href="/services/insurance-calculator" class="text-blue-600 hover:text-blue-800 underline">ماشین حساب بیمه</a> استفاده کنید.
                    </p>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="faq-item bg-gradient-to-l from-purple-50 to-pink-50 rounded-2xl border border-purple-200" data-category="companies">
                <button class="faq-toggle w-full text-right p-6 flex justify-between items-center focus:outline-none">
                    <span class="text-lg font-semibold text-purple-900">کدام شرکت بیمه بهترین قیمت را ارائه می‌دهد؟</span>
                    <svg class="faq-icon w-6 h-6 text-purple-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-6 text-gray-700 leading-relaxed">
                    <p class="mb-3">
                        <strong>قیمت بیمه</strong> بستگی به شرایط خودرو و راننده دارد. <em>شرکت‌های مختلف</em> ممکن است برای خودروهای متفاوت، 
                        قیمت‌های متفاوتی ارائه دهند:
                    </p>
                    <ul class="list-disc mr-6 mb-3">
                        <li><strong>بیمه ایران</strong> - معمولاً قیمت متعادل</li>
                        <li><em>بیمه آسیا</em> - نرخ‌های رقابتی</li>
                        <li><strong>بیمه البرز</strong> - تخصص در خودرو</li>
                    </ul>
                    <p>
                        بهترین روش، <a href="/services/insurance-comparison" class="text-blue-600 hover:text-blue-800 underline">مقایسه قیمت همه شرکت‌ها</a> است.
                    </p>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="faq-item bg-gradient-to-l from-orange-50 to-red-50 rounded-2xl border border-orange-200" data-category="purchase">
                <button class="faq-toggle w-full text-right p-6 flex justify-between items-center focus:outline-none">
                    <span class="text-lg font-semibold text-orange-900">چگونه می‌توانم بیمه شخص ثالث آنلاین خریداری کنم؟</span>
                    <svg class="faq-icon w-6 h-6 text-orange-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-6 text-gray-700 leading-relaxed">
                    <p class="mb-3">
                        خرید <strong>بیمه شخص ثالث آنلاین</strong> در چند مرحله ساده انجام می‌شود:
                    </p>
                    <ol class="list-decimal mr-6 mb-3">
                        <li><em>وارد کردن اطلاعات خودرو</em> - شماره پلاک و شناسه</li>
                        <li><strong>انتخاب نوع پوشش</strong> - شخص ثالث یا جامع</li>
                        <li><em>مقایسه قیمت‌ها</em> - انتخاب بهترین گزینه</li>
                        <li><strong>پرداخت آنلاین</strong> - از طریق درگاه امن</li>
                        <li><em>دریافت بیمه‌نامه</em> - ارسال فوری به ایمیل</li>
                    </ol>
                    <p>
                        برای شروع فرآیند خرید به <a href="/services/insurance-purchase" class="text-blue-600 hover:text-blue-800 underline">صفحه خرید بیمه</a> مراجعه کنید.
                    </p>
                </div>
            </div>

            <!-- FAQ 5 -->
            <div class="faq-item bg-gradient-to-l from-teal-50 to-cyan-50 rounded-2xl border border-teal-200" data-category="basic">
                <button class="faq-toggle w-full text-right p-6 flex justify-between items-center focus:outline-none">
                    <span class="text-lg font-semibold text-teal-900">تفاوت بیمه شخص ثالث و بیمه بدنه چیست؟</span>
                    <svg class="faq-icon w-6 h-6 text-teal-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-6 text-gray-700 leading-relaxed">
                    <p class="mb-3">
                        <strong>بیمه شخص ثالث</strong> فقط <em>خسارات وارده به طرف مقابل</em> را پوشش می‌دهد، 
                        در حالی که <strong>بیمه بدنه</strong> خسارات خود خودروی شما را نیز شامل می‌شود.
                    </p>
                    <ul class="list-disc mr-6 mb-3">
                        <li><em>بیمه شخص ثالث:</em> اجباری، پوشش طرف مقابل</li>
                        <li><strong>بیمه بدنه:</strong> اختیاری، پوشش خودروی شما</li>
                    </ul>
                    <p>
                        برای انتخاب بهتر، <a href="/services/insurance-types-comparison" class="text-blue-600 hover:text-blue-800 underline">مقایسه انواع بیمه</a> را مطالعه کنید.
                    </p>
                </div>
            </div>

            <!-- FAQ 6 -->
            <div class="faq-item bg-gradient-to-l from-yellow-50 to-amber-50 rounded-2xl border border-yellow-200" data-category="calculation">
                <button class="faq-toggle w-full text-right p-6 flex justify-between items-center focus:outline-none">
                    <span class="text-lg font-semibold text-yellow-900">تخفیف عدم خسارت چگونه محاسبه می‌شود؟</span>
                    <svg class="faq-icon w-6 h-6 text-yellow-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden px-6 pb-6 text-gray-700 leading-relaxed">
                    <p class="mb-3">
                        <strong>تخفیف عدم خسارت</strong> بر اساس تعداد سال‌های <em>بدون گزارش خسارت</em> محاسبه می‌شود:
                    </p>
                    <div class="bg-white rounded-xl p-4 border border-yellow-200 mb-3">
                        <ul class="list-none text-sm">
                            <li><strong>۱ سال:</strong> ۱۰٪ تخفیف</li>
                            <li><strong>۲ سال:</strong> ۲۰٪ تخفیف</li>
                            <li><strong>۳ سال:</strong> ۳۰٪ تخفیف</li>
                            <li><strong>۴ سال:</strong> ۴۰٪ تخفیف</li>
                            <li><strong>۵+ سال:</strong> ۶۰٪ تخفیف</li>
                        </ul>
                    </div>
                    <p>
                        برای بررسی میزان تخفیف خود از <a href="/services/insurance-discount-inquiry" class="text-blue-600 hover:text-blue-800 underline">استعلام تخفیف بیمه</a> استفاده کنید.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Services Section -->
<section id="related-services" class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl p-8 mt-16" dir="rtl">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-2xl font-bold text-dark-sky-700 mb-6 text-center">خدمات مرتبط</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="/services/third-party-insurance-history" class="bg-white rounded-xl p-4 border border-blue-200 hover:shadow-lg hover:border-blue-400 transition-all duration-200 group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 group-hover:text-blue-700">سابقه بیمه شخص ثالث</h4>
                        <p class="text-xs text-gray-500">بررسی تاریخچه بیمه خودرو</p>
                    </div>
                </div>
            </a>
            
            <a href="/services/vehicle-technical-diagnosis" class="bg-white rounded-xl p-4 border border-green-200 hover:shadow-lg hover:border-green-400 transition-all duration-200 group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 group-hover:text-green-700">معاینه فنی خودرو</h4>
                        <p class="text-xs text-gray-500">وضعیت معاینه فنی</p>
                    </div>
                </div>
            </a>
            
            <a href="/services/traffic-violation-inquiry" class="bg-white rounded-xl p-4 border border-purple-200 hover:shadow-lg hover:border-purple-400 transition-all duration-200 group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center group-hover:bg-purple-200">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.764 0L3.05 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 group-hover:text-purple-700">تخلفات رانندگی</h4>
                        <p class="text-xs text-gray-500">استعلام جریمه خودرو</p>
                    </div>
                </div>
            </a>
            
            <a href="/services/negative-license-score" class="bg-white rounded-xl p-4 border border-orange-200 hover:shadow-lg hover:border-orange-400 transition-all duration-200 group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center group-hover:bg-orange-200">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a2 2 0 10-4 0v5a2 2 0 01-2 2h6m-6-4h4m8 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 group-hover:text-orange-700">امتیاز منفی گواهینامه</h4>
                        <p class="text-xs text-gray-500">بررسی امتیاز رانندگی</p>
                    </div>
                </div>
            </a>
            
            <a href="/services/toll-road-inquiry" class="bg-white rounded-xl p-4 border border-teal-200 hover:shadow-lg hover:border-teal-400 transition-all duration-200 group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center group-hover:bg-teal-200">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 group-hover:text-teal-700">عوارض آزادراهی</h4>
                        <p class="text-xs text-gray-500">استعلام عوارض</p>
                    </div>
                </div>
            </a>
            
            <a href="/services/vehicle-registration-inquiry" class="bg-white rounded-xl p-4 border border-pink-200 hover:shadow-lg hover:border-pink-400 transition-all duration-200 group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center group-hover:bg-pink-200">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 group-hover:text-pink-700">وضعیت سند خودرو</h4>
                        <p class="text-xs text-gray-500">استعلام سند و مالکیت</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="text-center mt-8">
            <a href="/services" class="inline-flex items-center gap-2 bg-sky-600 text-white px-6 py-3 rounded-full font-medium hover:bg-sky-700 transition-colors">
                <span>مشاهده همه خدمات</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
        </div>
    </div>
</section>

</div>

<script>
// FAQ Functionality
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Toggle
    const faqToggles = document.querySelectorAll('.faq-toggle');
    const faqItems = document.querySelectorAll('.faq-item');
    const searchInput = document.getElementById('faq-search');
    const categoryBtns = document.querySelectorAll('.faq-category-btn');
    
    faqToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const icon = this.querySelector('.faq-icon');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        });
    });
    
    // FAQ Search
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-toggle span').textContent.toLowerCase();
                const answer = item.querySelector('.faq-content').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // FAQ Categories
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Update active button
            categoryBtns.forEach(b => {
                b.classList.remove('active', 'bg-sky-600', 'text-white');
                b.classList.add('bg-gray-200', 'text-gray-700');
            });
            
            this.classList.add('active', 'bg-sky-600', 'text-white');
            this.classList.remove('bg-gray-200', 'text-gray-700');
            
            // Filter FAQs
            faqItems.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                
                if (category === 'all' || itemCategory === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

@endsection