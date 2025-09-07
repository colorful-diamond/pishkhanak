@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-sky-900 mb-4">طرح‌های مختلف جستجو</h1>
            <p class="text-lg text-sky-700">10 طرح متنوع برای جستجوی هوشمند با رنگ‌بندی آسمانی و زرد</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-sky-800 mb-6">ویژگی‌های طرح‌ها</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-sky-900">طراحی ریسپانسیو</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7"/>
                        </svg>
                    </div>
                    <span class="text-sky-900">جستجوی سریع</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        </svg>
                    </div>
                    <span class="text-sky-900">پشتیبانی صوتی</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <span class="text-sky-900">پیشنهادات هوشمند</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-sky-900">موبایل فرست</span>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v18a1 1 0 01-1 1H4a1 1 0 01-1-1V1a1 1 0 011-1h2a1 1 0 011 1v3m0 0h8"/>
                        </svg>
                    </div>
                    <span class="text-sky-900">اکشن‌های سریع</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-semibold text-sky-800 mb-6">لیست طرح‌ها</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border border-sky-200 rounded-xl p-4 hover:shadow-md transition-shadow duration-300">
                    <h3 class="font-semibold text-sky-900 mb-2">طرح 1: مینیمال و تمیز</h3>
                    <p class="text-sm text-sky-600 mb-3">طراحی ساده و تمیز با فوکوس روی تجربه کاربری</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-sky-100 text-sky-800 px-2 py-1 rounded-full">ساده</span>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">محبوب</span>
                    </div>
                </div>
                
                <div class="border border-sky-200 rounded-xl p-4 hover:shadow-md transition-shadow duration-300">
                    <h3 class="font-semibold text-sky-900 mb-2">طرح 2: کارتی با آیکون‌ها</h3>
                    <p class="text-sm text-sky-600 mb-3">طراحی کارتی با آیکون‌های رنگی و پیشنهادات شبکه‌ای</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-sky-100 text-sky-800 px-2 py-1 rounded-full">کارتی</span>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">زیبا</span>
                    </div>
                </div>
                
                <div class="border border-sky-200 rounded-xl p-4 hover:shadow-md transition-shadow duration-300">
                    <h3 class="font-semibold text-sky-900 mb-2">طرح 3: دکمه تقسیم شده</h3>
                    <p class="text-sm text-sky-600 mb-3">ورودی و دکمه جستجو در یک بلوک با پیشنهادات تگی</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-sky-100 text-sky-800 px-2 py-1 rounded-full">کلاسیک</span>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">کاربردی</span>
                    </div>
                </div>
                
                <div class="border border-sky-200 rounded-xl p-4 hover:shadow-md transition-shadow duration-300">
                    <h3 class="font-semibold text-sky-900 mb-2">طرح 4: برچسب شناور</h3>
                    <p class="text-sm text-sky-600 mb-3">برچسب شناور با انیمیشن و پیشنهادات توضیحی</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-sky-100 text-sky-800 px-2 py-1 rounded-full">انیمیشن</span>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">مدرن</span>
                    </div>
                </div>
                
                <div class="border border-sky-200 rounded-xl p-4 hover:shadow-md transition-shadow duration-300">
                    <h3 class="font-semibold text-sky-900 mb-2">طرح 5: جستجوی تب‌دار</h3>
                    <p class="text-sm text-sky-600 mb-3">جستجو با تب‌های دسته‌بندی شده</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-sky-100 text-sky-800 px-2 py-1 rounded-full">تب‌دار</span>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">سازمان‌یافته</span>
                    </div>
                </div>
                
                <div class="border border-sky-200 rounded-xl p-4 hover:shadow-md transition-shadow duration-300">
                    <h3 class="font-semibold text-sky-900 mb-2">طرح 6: جستجوی صوتی</h3>
                    <p class="text-sm text-sky-600 mb-3">پشتیبانی از جستجوی صوتی با انیمیشن</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-sky-100 text-sky-800 px-2 py-1 rounded-full">صوتی</span>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">پیشرفته</span>
                    </div>
                </div>
                
                <div class="border border-sky-200 rounded-xl p-4 hover:shadow-md transition-shadow duration-300">
                    <h3 class="font-semibold text-sky-900 mb-2">طرح 7: چند مرحله‌ای</h3>
                    <p class="text-sm text-sky-600 mb-3">جستجوی مرحله‌ای با راهنمای بصری</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-sky-100 text-sky-800 px-2 py-1 rounded-full">مرحله‌ای</span>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">راهنما</span>
                    </div>
                </div>
                
                <div class="border border-sky-200 rounded-xl p-4 hover:shadow-md transition-shadow duration-300">
                    <h3 class="font-semibold text-sky-900 mb-2">طرح 8: اکشن‌های سریع</h3>
                    <p class="text-sm text-sky-600 mb-3">دکمه‌های اکشن سریع برای دسترسی فوری</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-sky-100 text-sky-800 px-2 py-1 rounded-full">سریع</span>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">کاربردی</span>
                    </div>
                </div>
                
                <div class="border border-sky-200 rounded-xl p-4 hover:shadow-md transition-shadow duration-300">
                    <h3 class="font-semibold text-sky-900 mb-2">طرح 9: پیشنهادات هوشمند</h3>
                    <p class="text-sm text-sky-600 mb-3">پیشنهادات هوشمند با برچسب‌های توضیحی</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-sky-100 text-sky-800 px-2 py-1 rounded-full">هوشمند</span>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">تعاملی</span>
                    </div>
                </div>
                
                <div class="border border-sky-200 rounded-xl p-4 hover:shadow-md transition-shadow duration-300">
                    <h3 class="font-semibold text-sky-900 mb-2">طرح 10: موبایل محور</h3>
                    <p class="text-sm text-sky-600 mb-3">طراحی فشرده و بهینه برای موبایل</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-sky-100 text-sky-800 px-2 py-1 rounded-full">موبایل</span>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">فشرده</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('test.search.designs') }}" 
               class="inline-flex items-center px-6 py-3 bg-sky-500 text-white font-semibold rounded-xl hover:bg-sky-600 transition-colors duration-300">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                مشاهده همه طرح‌ها
            </a>
        </div>
    </div>
</div>
@endsection 