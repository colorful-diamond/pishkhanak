<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تست فوتر {{ $footerNumber }} - پیشخوانک</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Iran Sans', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>
<body class="bg-sky-50">
    <!-- Test Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto max-w-screen-lg px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="پیشخوانک" class="w-12 h-12 rounded-lg">
                    <div>
                        <h1 class="text-xl font-bold text-sky-900">تست فوتر {{ $footerNumber }}</h1>
                        <p class="text-sm text-gray-600">پیش‌نمایش طراحی فوتر شماره {{ $footerNumber }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 10; $i++)
                        <a href="{{ route('app.test.footer' . $i) }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                  {{ $footerNumber == $i ? 'bg-sky-500 text-white' : 'bg-sky-200 text-gray-700 hover:bg-sky-300' }}">
                            {{ $i }}
                        </a>
                    @endfor
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="min-h-screen/2/2">
        <div class="container mx-auto max-w-screen-lg px-4 py-8">
            <!-- Sample Content -->
            <div class="bg-white rounded-lg shadow-sm p-8 mb-8">
                <h2 class="text-2xl font-bold text-sky-900 mb-4">محتوای نمونه صفحه</h2>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    این صفحه برای تست و بررسی طراحی‌های مختلف فوتر ایجاد شده است. 
                    شما می‌توانید با استفاده از دکمه‌های بالای صفحه بین طراحی‌های مختلف فوتر جابه‌جا شوید.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-sky-50 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-sky-900 mb-3">ویژگی‌های فوتر {{ $footerNumber }}</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            @switch($footerNumber)
                                @case(1)
                                    <li>• طراحی کلاسیک افقی</li>
                                    <li>• لوگو و اطلاعات شرکت در سمت چپ</li>
                                    <li>• ستون‌های خدمات منظم</li>
                                    <li>• اطلاعات تماس در سمت راست</li>
                                    @break
                                @case(2)
                                    <li>• طراحی متمرکز</li>
                                    <li>• لوگو در بالا</li>
                                    <li>• خدمات در قالب شبکه</li>
                                    <li>• اطلاعات تماس در پایین</li>
                                    @break
                                @case(3)
                                    <li>• طراحی مینیمال تک ردیفه</li>
                                    <li>• فقط لینک‌های ضروری</li>
                                    <li>• پس‌زمینه تیره</li>
                                    <li>• مناسب برای صفحات ساده</li>
                                    @break
                                @case(4)
                                    <li>• طراحی مبتنی بر کارت</li>
                                    <li>• هر بخش در کارت جداگانه</li>
                                    <li>• سایه و حاشیه برای کارت‌ها</li>
                                    <li>• ظاهر مدرن و جذاب</li>
                                    @break
                                @case(5)
                                    <li>• طراحی عمودی موبایل محور</li>
                                    <li>• بهینه‌سازی برای موبایل</li>
                                    <li>• چیدمان عمودی</li>
                                    <li>• پاسخگو و انعطاف‌پذیر</li>
                                    @break
                                @case(6)
                                    <li>• طراحی دو ستونه</li>
                                    <li>• خدمات در سمت چپ</li>
                                    <li>• اطلاعات شرکت در سمت راست</li>
                                    <li>• تعادل بصری مناسب</li>
                                    @break
                                @case(7)
                                    <li>• تمرکز بر خبرنامه</li>
                                    <li>• فرم عضویت در خبرنامه</li>
                                    <li>• طراحی تعاملی</li>
                                    <li>• مناسب برای بازاریابی</li>
                                    @break
                                @case(8)
                                    <li>• طراحی شبکه‌ای مدرن</li>
                                    <li>• بخش لوگو بزرگ</li>
                                    <li>• دسته‌بندی منظم خدمات</li>
                                    <li>• ظاهر حرفه‌ای</li>
                                    @break
                                @case(9)
                                    <li>• طراحی آکاردئونی</li>
                                    <li>• بخش‌های قابل جمع شدن</li>
                                    <li>• بهینه برای موبایل</li>
                                    <li>• صرفه‌جویی در فضا</li>
                                    @break
                                @case(10)
                                    <li>• طراحی فشرده تقسیم شده</li>
                                    <li>• تمرکز بر شبکه‌های اجتماعی</li>
                                    <li>• اطلاعات ضروری</li>
                                    <li>• ساده و کاربردی</li>
                                    @break
                            @endswitch
                        </ul>
                    </div>
                    
                    <div class="bg-sky-50 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-3">راهنمای تست</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li>• اندازه صفحه را تغییر دهید</li>
                            <li>• حالت موبایل را بررسی کنید</li>
                            <li>• روی لینک‌ها کلیک کنید</li>
                            <li>• طراحی را با سایر فوترها مقایسه کنید</li>
                            <li>• سرعت بارگذاری را بررسی کنید</li>
                            <li>• قابلیت خوانایی متن را ارزیابی کنید</li>
                        </ul>
                    </div>
                </div>

                <!-- Sample Services -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-sky-50 rounded-lg p-4 text-center">
                        <div class="w-12 h-12 bg-sky-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-sky-900 mb-2">خدمات بانکی</h4>
                        <p class="text-sm text-gray-600">محاسبه شبا، اعتبارسنجی و استعلام چک</p>
                    </div>
                    
                    <div class="bg-sky-50 rounded-lg p-4 text-center">
                        <div class="w-12 h-12 bg-sky-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-sky-900 mb-2">خودرو و موتور</h4>
                        <p class="text-sm text-gray-600">خلافی، بیمه و استعلام خودرو</p>
                    </div>
                    
                    <div class="bg-sky-50 rounded-lg p-4 text-center">
                        <div class="w-12 h-12 bg-sky-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-sky-900 mb-2">سایر خدمات</h4>
                        <p class="text-sm text-gray-600">کدپستی، ملی و نظام وظیفه</p>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-center gap-4">
                    @if($footerNumber > 1)
                        <a href="{{ route('app.test.footer' . ($footerNumber - 1)) }}" 
                           class="bg-sky-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            ← فوتر قبلی
                        </a>
                    @endif
                    
                    @if($footerNumber < 10)
                        <a href="{{ route('app.test.footer' . ($footerNumber + 1)) }}" 
                           class="bg-sky-500 hover:bg-sky-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            فوتر بعدی →
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Display Selected Footer -->
    @include('front.partials.footer' . $footerNumber)

    <!-- JavaScript for interactions -->
    <script>
        // Add some interactivity for testing
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Footer {{ $footerNumber }} loaded successfully');
            
            // Test accordion functionality for footer 9
            @if($footerNumber == 9)
                const details = document.querySelectorAll('details');
                details.forEach(detail => {
                    detail.addEventListener('toggle', function() {
                        if (this.open) {
                            console.log('Accordion opened:', this.querySelector('summary').textContent);
                        }
                    });
                });
            @endif
            
            // Test newsletter form for footer 7
            @if($footerNumber == 7)
                const newsletterForm = document.querySelector('footer form');
                if (newsletterForm) {
                    newsletterForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        alert('فرم خبرنامه ارسال شد! (این فقط برای تست است)');
                    });
                }
            @endif
        });
    </script>
</body>
</html> 