@extends('front.layouts.app')

@section('content')
<div class="min-h-screen/2/2 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- Animated Error Number -->
        <div class="relative">
            <div class="text-9xl font-black text-gray-400 animate-float opacity-20 absolute inset-0 flex items-center justify-center">
                {{ $exception->getStatusCode() ?? 'خطا' }}
            </div>
            <div class="relative z-10">
                <!-- Main SVG Illustration - Generic Error -->
                <svg class="mx-auto h-64 w-64 text-gray-500" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <!-- Background Circle -->
                    <circle cx="100" cy="100" r="90" fill="none" stroke="currentColor" stroke-width="2" opacity="0.1"/>
                    
                    <!-- Floating Elements -->
                    <g class="animate-pulse">
                        <circle cx="60" cy="60" r="4" fill="currentColor" opacity="0.6"/>
                        <circle cx="140" cy="80" r="3" fill="currentColor" opacity="0.4"/>
                        <circle cx="80" cy="140" r="5" fill="currentColor" opacity="0.5"/>
                    </g>
                    
                    <!-- Main Icon - Exclamation Mark -->
                    <g transform="translate(100, 100)">
                        <!-- Circle Background -->
                        <circle cx="0" cy="0" r="35" fill="none" stroke="currentColor" stroke-width="3"/>
                        
                        <!-- Exclamation Mark -->
                        <line x1="0" y1="-20" x2="0" y2="5" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                        <circle cx="0" cy="15" r="3" fill="currentColor"/>
                        
                        <!-- Decorative Elements -->
                        <g transform="translate(-25, -25)">
                            <line x1="0" y1="0" x2="8" y2="8" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                            <line x1="8" y1="0" x2="0" y2="8" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                        </g>
                        <g transform="translate(17, -25)">
                            <line x1="0" y1="0" x2="8" y2="8" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                            <line x1="8" y1="0" x2="0" y2="8" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                        </g>
                        <g transform="translate(-25, 17)">
                            <line x1="0" y1="0" x2="8" y2="8" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                            <line x1="8" y1="0" x2="0" y2="8" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                        </g>
                        <g transform="translate(17, 17)">
                            <line x1="0" y1="0" x2="8" y2="8" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                            <line x1="8" y1="0" x2="0" y2="8" stroke="currentColor" stroke-width="1" opacity="0.5"/>
                        </g>
                    </g>
                </svg>
            </div>
        </div>

        <!-- Error Message -->
        <div class="space-y-4">
            <h1 class="text-3xl font-bold text-gray-900">
                @switch($exception->getStatusCode())
                    @case(400)
                        درخواست نامعتبر!
                        @break
                    @case(401)
                        احراز هویت ناموفق!
                        @break
                    @case(422)
                        اطلاعات نامعتبر!
                        @break
                    @case(429)
                        درخواست‌های بیش از حد!
                        @break
                    @default
                        خطای غیرمنتظره!
                @endswitch
            </h1>
            <p class="text-lg text-gray-600 leading-relaxed">
                @switch($exception->getStatusCode())
                    @case(400)
                        درخواست ارسالی شما نامعتبر است. لطفاً اطلاعات را بررسی کرده و دوباره تلاش کنید.
                        @break
                    @case(401)
                        برای دسترسی به این صفحه نیاز به ورود به حساب کاربری دارید.
                        @break
                    @case(422)
                        اطلاعات ارسالی شما دارای خطا است. لطفاً فرم را دوباره بررسی کنید.
                        @break
                    @case(429)
                        تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً کمی صبر کنید.
                        @break
                    @default
                        متأسفانه مشکلی رخ داده است. لطفاً دوباره تلاش کنید یا با پشتیبانی تماس بگیرید.
                @endswitch
            </p>
            <p class="text-gray-600 text-sm mb-4">خطای {{ $exception->getStatusCode() ?? 500 }} در تاریخ {{ \Verta::instance(now())->format('Y/m/d H:i') }}</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center pt-8">
            @switch($exception->getStatusCode())
                @case(401)
                    <a href="{{ route('app.auth.login') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-sky-500 hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all duration-300 transform hover:scale-105">
                        <x-tabler-login class="w-5 h-5 ml-2" />
                        ورود به حساب کاربری
                    </a>
                    @break
                @case(429)
                    <button onclick="window.location.reload()" 
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-300 transform hover:scale-105">
                        <x-tabler-refresh class="w-5 h-5 ml-2" />
                        تلاش مجدد
                    </button>
                    @break
                @default
                    <button onclick="window.history.back()" 
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-sky-500 hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all duration-300 transform hover:scale-105">
                        <x-tabler-arrow-left class="w-5 h-5 ml-2" />
                        بازگشت
                    </button>
            @endswitch
            
            <a href="{{ route('app.page.home') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300">
                <x-tabler-home class="w-5 h-5 ml-2" />
                صفحه اصلی
            </a>
        </div>

        <!-- Error Details -->
        <div class="mt-8 p-6 bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">جزئیات خطا</h3>
            <div class="space-y-3 text-right">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">کد خطا:</span>
                    <span class="text-sm font-mono text-gray-800">{{ $exception->getStatusCode() ?? 'نامشخص' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">زمان:</span>
                    <span class="text-sm text-gray-800">{{ \Verta::instance(now())->format('Y/m/d H:i') }}</span>
                </div>
                @if(config('app.debug'))
                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-xs text-red-800 font-mono text-right">
                            {{ $exception->getMessage() }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Help Section -->
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
                <a href="{{ route('app.blog.index') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-sky-300 text-sm font-medium rounded-lg text-sky-700 bg-white hover:bg-sky-50 transition-colors duration-300">
                    <x-tabler-book class="w-4 h-4 ml-2" />
                    راهنمای کاربری
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-sky-900 mb-4">دسترسی سریع</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('services.show', ['slug1' => 'check-inquiry']) }}" class="p-3 bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors duration-300 text-sm text-sky-700">
                    <x-tabler-apps class="w-4 h-4 inline ml-1" />
                    خدمات ما
                </a>
                <a href="{{ route('app.blog.index') }}" class="p-3 bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors duration-300 text-sm text-sky-700">
                    <x-tabler-news class="w-4 h-4 inline ml-1" />
                    مقالات
                </a>
                <a href="{{ route('app.page.about') }}" class="p-3 bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors duration-300 text-sm text-sky-700">
                    <x-tabler-info-circle class="w-4 h-4 inline ml-1" />
                    درباره ما
                </a>
                <a href="{{ route('app.page.contact') }}" class="p-3 bg-white border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors duration-300 text-sm text-sky-700">
                    <x-tabler-phone class="w-4 h-4 inline ml-1" />
                    تماس با ما
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