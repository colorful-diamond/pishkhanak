@extends('front.layouts.app')

@section('content')
<div class="min-h-screen/2/2 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- Animated Maintenance Icon -->
        <div class="relative">
            <div class="text-9xl font-black text-orange-400 animate-float opacity-20 absolute inset-0 flex items-center justify-center">
                <i class="bi bi-tools"></i>
            </div>
            <div class="relative z-10">
                <!-- Main SVG Illustration - Maintenance -->
                <svg class="mx-auto h-64 w-64 text-orange-500" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <!-- Background Circle -->
                    <circle cx="100" cy="100" r="90" fill="none" stroke="currentColor" stroke-width="2" opacity="0.1"/>
                    
                    <!-- Floating Elements -->
                    <g class="animate-pulse">
                        <circle cx="60" cy="60" r="4" fill="currentColor" opacity="0.6"/>
                        <circle cx="140" cy="80" r="3" fill="currentColor" opacity="0.4"/>
                        <circle cx="80" cy="140" r="5" fill="currentColor" opacity="0.5"/>
                    </g>
                    
                    <!-- Main Icon - Wrench and Gear -->
                    <g transform="translate(100, 100)">
                        <!-- Gear -->
                        <g transform="translate(-25, -25)" class="animate-spin-slow">
                            <circle cx="0" cy="0" r="20" fill="none" stroke="currentColor" stroke-width="3"/>
                            <circle cx="0" cy="0" r="8" fill="currentColor" opacity="0.3"/>
                            <!-- Gear Teeth -->
                            <rect x="-2" y="-25" width="4" height="8" fill="currentColor" opacity="0.6"/>
                            <rect x="-2" y="17" width="4" height="8" fill="currentColor" opacity="0.6"/>
                            <rect x="-25" y="-2" width="8" height="4" fill="currentColor" opacity="0.6"/>
                            <rect x="17" y="-2" width="8" height="4" fill="currentColor" opacity="0.6"/>
                        </g>
                        
                        <!-- Wrench -->
                        <g transform="translate(15, 15) rotate(45)">
                            <rect x="-2" y="0" width="4" height="30" fill="currentColor" opacity="0.7"/>
                            <circle cx="0" cy="35" r="8" fill="none" stroke="currentColor" stroke-width="2"/>
                            <rect x="-8" y="-8" width="16" height="12" rx="2" fill="currentColor" opacity="0.6"/>
                        </g>
                    </g>
                </svg>
            </div>
        </div>

        <!-- Error Title -->
        <div class="space-y-2">
            <h1 class="text-5xl font-extrabold text-gray-900 dark:text-white">
                سرویس در حال تعمیر
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400">
                {{ $service->title ?? 'این سرویس' }} موقتاً در دسترس نمی‌باشد
            </p>
        </div>

        <!-- Custom Error Message -->
        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-6 text-center">
            <div class="flex justify-center mb-4">
                <svg class="h-12 w-12 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                {{ $message }}
            </p>
            
            @if($ends_at)
            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                <i class="bi bi-clock ml-1"></i>
                زمان تخمینی پایان تعمیرات: 
                <span class="font-semibold">{{ \Carbon\Carbon::parse($ends_at)->diffForHumans() }}</span>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                بازگشت
            </a>
            <a href="/" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                صفحه اصلی
            </a>
        </div>

        <!-- Additional Information -->
        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                اگر سوالی دارید، می‌توانید با پشتیبانی تماس بگیرید
            </p>
            <div class="mt-2">
                <a href="/contact" class="text-orange-600 hover:text-orange-500 font-medium">
                    تماس با پشتیبانی
                    <i class="bi bi-arrow-left mr-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Add custom animation for slow rotation -->
<style>
@keyframes spin-slow {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin-slow {
    animation: spin-slow 8s linear infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}
</style>
@endsection