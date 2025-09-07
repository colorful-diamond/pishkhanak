@extends('front.layouts.app')

@section('content')
<div class="min-h-screen/2/2 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- Animated 404 Number -->
        <div class="relative">
            <div class="text-9xl font-black text-sky-400 animate-float opacity-20 absolute inset-0 flex items-center justify-center">
                404
            </div>
            <div class="relative z-10">
                <!-- Main SVG Illustration -->
                <svg class="mx-auto h-64 w-64 text-sky-500" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <!-- Background Circle -->
                    <circle cx="100" cy="100" r="90" fill="none" stroke="currentColor" stroke-width="2" opacity="0.1"/>
                    
                    <!-- Floating Elements -->
                    <g class="animate-pulse">
                        <circle cx="60" cy="60" r="4" fill="currentColor" opacity="0.6"/>
                        <circle cx="140" cy="80" r="3" fill="currentColor" opacity="0.4"/>
                        <circle cx="80" cy="140" r="5" fill="currentColor" opacity="0.5"/>
                    </g>
                    
                    <!-- Main Icon - Broken Link/Page -->
                    <g transform="translate(100, 100)">
                        <!-- Page Icon -->
                        <rect x="-25" y="-35" width="50" height="70" rx="4" fill="none" stroke="currentColor" stroke-width="3"/>
                        <line x1="-15" y1="-25" x2="15" y2="-25" stroke="currentColor" stroke-width="2"/>
                        <line x1="-15" y1="-15" x2="10" y2="-15" stroke="currentColor" stroke-width="2"/>
                        <line x1="-15" y1="-5" x2="5" y2="-5" stroke="currentColor" stroke-width="2"/>
                        
                        <!-- Broken Corner -->
                        <path d="M 25 -35 L 35 -25 L 25 -15" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                        
                        <!-- Search Magnifying Glass -->
                        <circle cx="15" cy="15" r="8" fill="none" stroke="currentColor" stroke-width="2"/>
                        <line x1="20" y1="20" x2="25" y2="25" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        
                        <!-- Question Mark -->
                        <g transform="translate(-15, 25)">
                            <circle cx="0" cy="0" r="6" fill="none" stroke="currentColor" stroke-width="2"/>
                            <circle cx="0" cy="8" r="1" fill="currentColor"/>
                        </g>
                    </g>
                </svg>
            </div>
        </div>

        <!-- Error Message -->
        <div class="space-y-4">
            <h1 class="text-3xl font-bold text-sky-900">
                صفحه مورد نظر یافت نشد!
            </h1>
            <p class="text-lg text-gray-600 leading-relaxed">
                متأسفانه صفحه‌ای که به دنبال آن هستید وجود ندارد یا به آدرس دیگری منتقل شده است.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center pt-8">
            <a href="{{ route('app.page.home') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-sky-500 hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all duration-300 transform hover:scale-105">
                <x-tabler-home class="w-5 h-5 ml-2" />
                بازگشت به صفحه اصلی
            </a>
            <a href="{{ route('app.page.contact') }}" 
               class="inline-flex items-center px-6 py-3 border border-sky-300 text-base font-medium rounded-lg text-sky-700 bg-white hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all duration-300">
                <x-tabler-message-circle class="w-5 h-5 ml-2" />
                تماس با پشتیبانی
            </a>
        </div>

        <!-- Search Suggestion -->
        <div class="mt-8 p-6 bg-gradient-to-r from-sky-50 to-sky-50 rounded-2xl border border-sky-100">
            <h3 class="text-lg font-semibold text-sky-900 mb-3">جستجو کنید</h3>
            <form action="{{ route('app.blog.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" placeholder="جستجو در خدمات و مقالات..." 
                       class="flex-1 px-4 py-2 border border-sky-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent text-right">
                <button type="submit" 
                        class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors duration-300">
                    <x-tabler-search class="w-5 h-5" />
                </button>
            </form>
        </div>

        <!-- Popular Services -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-sky-900 mb-4">خدمات محبوب</h3>
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