@extends('front.layouts.app')

@section('content')
<div class="min-h-screen/2/2 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- Animated 500 Number -->
        <div class="relative">
            <div class="text-9xl font-black text-red-400 animate-float opacity-20 absolute inset-0 flex items-center justify-center">
                500
            </div>
            <div class="relative z-10">
                <!-- Main SVG Illustration - Server Error -->
                <svg class="mx-auto h-64 w-64 text-red-500" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <!-- Background Circle -->
                    <circle cx="100" cy="100" r="90" fill="none" stroke="currentColor" stroke-width="2" opacity="0.1"/>
                    
                    <!-- Floating Error Elements -->
                    <g class="animate-pulse">
                        <circle cx="60" cy="60" r="4" fill="currentColor" opacity="0.6"/>
                        <circle cx="140" cy="80" r="3" fill="currentColor" opacity="0.4"/>
                        <circle cx="80" cy="140" r="5" fill="currentColor" opacity="0.5"/>
                    </g>
                    
                    <!-- Server Icon -->
                    <g transform="translate(100, 100)">
                        <!-- Server Tower -->
                        <rect x="-30" y="-40" width="60" height="80" rx="4" fill="none" stroke="currentColor" stroke-width="3"/>
                        
                        <!-- Server Racks -->
                        <rect x="-25" y="-35" width="50" height="8" rx="2" fill="currentColor" opacity="0.3"/>
                        <rect x="-25" y="-20" width="50" height="8" rx="2" fill="currentColor" opacity="0.3"/>
                        <rect x="-25" y="-5" width="50" height="8" rx="2" fill="currentColor" opacity="0.3"/>
                        <rect x="-25" y="10" width="50" height="8" rx="2" fill="currentColor" opacity="0.3"/>
                        <rect x="-25" y="25" width="50" height="8" rx="2" fill="currentColor" opacity="0.3"/>
                        
                        <!-- Error Indicators -->
                        <circle cx="-15" cy="-15" r="2" fill="red" class="animate-pulse"/>
                        <circle cx="0" cy="-15" r="2" fill="red" class="animate-pulse"/>
                        <circle cx="15" cy="-15" r="2" fill="red" class="animate-pulse"/>
                        
                        <!-- Warning Triangle -->
                        <g transform="translate(0, -60)">
                            <path d="M -15 15 L 0 -15 L 15 15 Z" fill="none" stroke="currentColor" stroke-width="2"/>
                            <line x1="0" y1="-5" x2="0" y2="5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="0" cy="8" r="1" fill="currentColor"/>
                        </g>
                        
                        <!-- Lightning Bolt -->
                        <g transform="translate(25, -20)">
                            <path d="M -5 -10 L 0 0 L -3 0 L 5 10 L 0 0 L 3 0 Z" fill="currentColor" opacity="0.8"/>
                        </g>
                    </g>
                </svg>
            </div>
        </div>

        <!-- Error Message -->
        <div class="space-y-4">
            <h1 class="text-3xl font-bold text-red-900">
                خطای سرور!
            </h1>
            <p class="text-lg text-gray-600 leading-relaxed">
                متأسفانه مشکلی در سرور رخ داده است. تیم فنی ما در حال بررسی و رفع مشکل است.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center pt-8">
            <button onclick="window.location.reload()" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300 transform hover:scale-105">
                <x-tabler-refresh class="w-5 h-5 ml-2" />
                تلاش مجدد
            </button>
            <a href="{{ route('app.page.home') }}" 
               class="inline-flex items-center px-6 py-3 border border-red-300 text-base font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300">
                <x-tabler-home class="w-5 h-5 ml-2" />
                بازگشت به صفحه اصلی
            </a>
        </div>

        <!-- Status Information -->
        <div class="mt-8 p-6 bg-gradient-to-r from-red-50 to-pink-50 rounded-2xl border border-red-100">
            <h3 class="text-lg font-semibold text-red-900 mb-3">وضعیت سیستم</h3>
            <div class="space-y-3 text-right">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">وضعیت سرور:</span>
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                        <span class="w-2 h-2 bg-red-400 rounded-full ml-1 animate-pulse"></span>
                        در حال بررسی
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">زمان تخمینی:</span>
                    <span class="text-sm text-gray-800">۵-۱۰ دقیقه</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">آخرین بروزرسانی:</span>
                    <span class="text-sm text-gray-800">{{ \Verta::instance(now())->format('H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="mt-8 p-6 bg-gradient-to-r from-sky-50 to-sky-50 rounded-2xl border border-sky-100">
            <h3 class="text-lg font-semibold text-sky-900 mb-3">نیاز به کمک دارید؟</h3>
            <p class="text-sm text-gray-600 mb-4">
                اگر این مشکل همچنان ادامه دارد، با تیم پشتیبانی ما تماس بگیرید.
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('app.page.contact') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-sky-300 text-sm font-medium rounded-lg text-sky-700 bg-white hover:bg-sky-50 transition-colors duration-300">
                    <x-tabler-message-circle class="w-4 h-4 ml-2" />
                    تماس با پشتیبانی
                </a>
                <a href="mailto:{{ \App\Helpers\SettingsHelper::getSupportEmail() }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-sky-300 text-sm font-medium rounded-lg text-sky-700 bg-white hover:bg-sky-50 transition-colors duration-300">
                    <x-tabler-mail class="w-4 h-4 ml-2" />
                    ارسال ایمیل
                </a>
            </div>
        </div>

        <!-- Alternative Services -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-sky-900 mb-4">خدمات جایگزین</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="tel:+989123456789" class="p-3 bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors duration-300 text-sm text-sky-700">
                    <x-tabler-phone class="w-4 h-4 inline ml-1" />
                    تماس تلفنی
                </a>
                <a href="{{ \App\Helpers\SettingsHelper::getTelegramUrl() }}" class="p-3 bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors duration-300 text-sm text-sky-700">
                    <x-tabler-message class="w-4 h-4 inline ml-1" />
                    چت آنلاین
                </a>
                <a href="{{ route('app.blog.index') }}" class="p-3 bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors duration-300 text-sm text-sky-700">
                    <x-tabler-help class="w-4 h-4 inline ml-1" />
                    راهنمای کاربری
                </a>
                <a href="{{ route('app.blog.index') }}" class="p-3 bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors duration-300 text-sm text-sky-700">
                    <x-tabler-book class="w-4 h-4 inline ml-1" />
                    سوالات متداول
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
@endsection 