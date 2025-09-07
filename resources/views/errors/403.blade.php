@extends('front.layouts.app')

@section('content')
<div class="min-h-screen/2/2 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- Animated 403 Number -->
        <div class="relative">
            <div class="text-9xl font-black text-orange-400 animate-float opacity-20 absolute inset-0 flex items-center justify-center">
                403
            </div>
            <div class="relative z-10">
                <!-- Main SVG Illustration - Access Denied -->
                <svg class="mx-auto h-64 w-64 text-orange-500" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <!-- Background Circle -->
                    <circle cx="100" cy="100" r="90" fill="none" stroke="currentColor" stroke-width="2" opacity="0.1"/>
                    
                    <!-- Floating Security Elements -->
                    <g class="animate-pulse">
                        <circle cx="60" cy="60" r="4" fill="currentColor" opacity="0.6"/>
                        <circle cx="140" cy="80" r="3" fill="currentColor" opacity="0.4"/>
                        <circle cx="80" cy="140" r="5" fill="currentColor" opacity="0.5"/>
                    </g>
                    
                    <!-- Lock and Shield Icon -->
                    <g transform="translate(100, 100)">
                        <!-- Shield Background -->
                        <path d="M -25 -40 L 25 -40 L 35 -20 L 25 0 L -25 0 L -35 -20 Z" fill="none" stroke="currentColor" stroke-width="3"/>
                        
                        <!-- Lock Body -->
                        <rect x="-15" y="-15" width="30" height="25" rx="3" fill="none" stroke="currentColor" stroke-width="3"/>
                        
                        <!-- Lock Shackle -->
                        <path d="M -15 -15 L -15 -25 L 15 -25 L 15 -15" fill="none" stroke="currentColor" stroke-width="3"/>
                        
                        <!-- Keyhole -->
                        <circle cx="0" cy="-5" r="3" fill="currentColor" opacity="0.3"/>
                        <rect x="-1" y="-5" width="2" height="8" fill="currentColor" opacity="0.3"/>
                        
                        <!-- Warning Triangle -->
                        <g transform="translate(0, -50)">
                            <path d="M -12 12 L 0 -12 L 12 12 Z" fill="none" stroke="currentColor" stroke-width="2"/>
                            <line x1="0" y1="-4" x2="0" y2="4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="0" cy="6" r="1" fill="currentColor"/>
                        </g>
                        
                        <!-- Security Lines -->
                        <g transform="translate(-30, -30)">
                            <line x1="0" y1="0" x2="10" y2="10" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                            <line x1="10" y1="0" x2="0" y2="10" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                        </g>
                        <g transform="translate(20, -30)">
                            <line x1="0" y1="0" x2="10" y2="10" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                            <line x1="10" y1="0" x2="0" y2="10" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                        </g>
                    </g>
                </svg>
            </div>
        </div>

        <!-- Error Message -->
        <div class="space-y-4">
            <h1 class="text-3xl font-bold text-orange-900">
                دسترسی غیرمجاز!
            </h1>
            <p class="text-lg text-gray-600 leading-relaxed">
                متأسفانه شما مجوز دسترسی به این صفحه را ندارید. لطفاً وارد حساب کاربری خود شوید.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center pt-8">
            <a href="{{ route('app.auth.login') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-300 transform hover:scale-105">
                <x-tabler-login class="w-5 h-5 ml-2" />
                ورود به حساب کاربری
            </a>
            <a href="{{ route('app.page.home') }}" 
               class="inline-flex items-center px-6 py-3 border border-orange-300 text-base font-medium rounded-lg text-orange-700 bg-white hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-300">
                <x-tabler-home class="w-5 h-5 ml-2" />
                بازگشت به صفحه اصلی
            </a>
        </div>

        <!-- Access Information -->
        <div class="mt-8 p-6 bg-gradient-to-r from-orange-50 to-yellow-50 rounded-2xl border border-orange-100">
            <h3 class="text-lg font-semibold text-orange-900 mb-3">چرا این خطا رخ داد؟</h3>
            <div class="space-y-3 text-right">
                <div class="flex items-start gap-3">
                    <x-tabler-info-circle class="w-5 h-5 text-orange-500 mt-0.5 flex-shrink-0" />
                    <span class="text-sm text-gray-700">شما وارد حساب کاربری خود نشده‌اید</span>
                </div>
                <div class="flex items-start gap-3">
                    <x-tabler-shield-lock class="w-5 h-5 text-orange-500 mt-0.5 flex-shrink-0" />
                    <span class="text-sm text-gray-700">این صفحه نیاز به احراز هویت دارد</span>
                </div>
                <div class="flex items-start gap-3">
                    <x-tabler-user-check class="w-5 h-5 text-orange-500 mt-0.5 flex-shrink-0" />
                    <span class="text-sm text-gray-700">احتمالاً نیاز به سطح دسترسی بالاتری دارید</span>
                </div>
            </div>
        </div>

        <!-- Registration Suggestion -->
        <div class="mt-8 p-6 bg-gradient-to-r from-sky-50 to-sky-50 rounded-2xl border border-sky-100">
            <h3 class="text-lg font-semibold text-sky-900 mb-3">حساب کاربری ندارید؟</h3>
            <p class="text-sm text-gray-600 mb-4">
                با ثبت‌نام در پیشخوانک، به تمام خدمات ما دسترسی داشته باشید.
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('app.auth.login') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-sky-300 text-sm font-medium rounded-lg text-sky-700 bg-white hover:bg-sky-50 transition-colors duration-300">
                    <x-tabler-user-plus class="w-4 h-4 ml-2" />
                    ثبت‌نام رایگان
                </a>
                <a href="{{ route('app.page.contact') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-sky-300 text-sm font-medium rounded-lg text-sky-700 bg-white hover:bg-sky-50 transition-colors duration-300">
                    <x-tabler-help class="w-4 h-4 ml-2" />
                    راهنمای ثبت‌نام
                </a>
            </div>
        </div>

        <!-- Available Services -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-sky-900 mb-4">خدمات عمومی</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('services.show', ['slug1' => 'check-inquiry']) }}" class="p-3 bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors duration-300 text-sm text-sky-700">
                    <x-tabler-credit-card class="w-4 h-4 inline ml-1" />
                    استعلام چک
                </a>
                <a href="{{ route('services.show', ['slug1' => 'traffic-violation']) }}" class="p-3 bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors duration-300 text-sm text-sky-700">
                    <x-tabler-car class="w-4 h-4 inline ml-1" />
                    خلافی خودرو
                </a>
                <a href="{{ route('services.show', ['slug1' => 'bank-inquiry']) }}" class="p-3 bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors duration-300 text-sm text-sky-700">
                    <x-tabler-building-bank class="w-4 h-4 inline ml-1" />
                    استعلام بانکی
                </a>
                <a href="{{ route('services.show', ['slug1' => 'postal-code']) }}" class="p-3 bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors duration-300 text-sm text-sky-700">
                    <x-tabler-file-text class="w-4 h-4 inline ml-1" />
                    استعلام کدپستی
                </a>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="mt-8 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-100">
            <div class="flex items-center gap-3">
                <x-tabler-shield-check class="w-6 h-6 text-green-600" />
                <div class="text-right">
                    <h4 class="text-sm font-semibold text-green-900">امنیت شما مهم است</h4>
                    <p class="text-xs text-green-700">اطلاعات شما با بالاترین استانداردهای امنیتی محافظت می‌شود</p>
                </div>
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