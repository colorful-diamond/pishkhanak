@extends('front.layouts.app')

@section('content')
<div class="min-h-screen/2/2 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- Animated 503 Number -->
        <div class="relative">
            <div class="text-9xl font-black text-purple-400 animate-float opacity-20 absolute inset-0 flex items-center justify-center">
                503
            </div>
            <div class="relative z-10">
                <!-- Main SVG Illustration - Maintenance -->
                <svg class="mx-auto h-64 w-64 text-purple-500" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <!-- Background Circle -->
                    <circle cx="100" cy="100" r="90" fill="none" stroke="currentColor" stroke-width="2" opacity="0.1"/>
                    
                    <!-- Floating Tools -->
                    <g class="animate-pulse">
                        <circle cx="60" cy="60" r="4" fill="currentColor" opacity="0.6"/>
                        <circle cx="140" cy="80" r="3" fill="currentColor" opacity="0.4"/>
                        <circle cx="80" cy="140" r="5" fill="currentColor" opacity="0.5"/>
                    </g>
                    
                    <!-- Main Icon - Tools and Gears -->
                    <g transform="translate(100, 100)">
                        <!-- Large Gear -->
                        <g transform="translate(-25, -25)">
                            <circle cx="0" cy="0" r="20" fill="none" stroke="currentColor" stroke-width="3"/>
                            <circle cx="0" cy="0" r="8" fill="currentColor" opacity="0.3"/>
                            <!-- Gear Teeth -->
                            <rect x="-2" y="-25" width="4" height="8" fill="currentColor" opacity="0.6"/>
                            <rect x="-2" y="17" width="4" height="8" fill="currentColor" opacity="0.6"/>
                            <rect x="-25" y="-2" width="8" height="4" fill="currentColor" opacity="0.6"/>
                            <rect x="17" y="-2" width="8" height="4" fill="currentColor" opacity="0.6"/>
                            <rect x="-18" y="-18" width="6" height="4" fill="currentColor" opacity="0.6"/>
                            <rect x="12" y="-18" width="6" height="4" fill="currentColor" opacity="0.6"/>
                            <rect x="-18" y="14" width="6" height="4" fill="currentColor" opacity="0.6"/>
                            <rect x="12" y="14" width="6" height="4" fill="currentColor" opacity="0.6"/>
                        </g>
                        
                        <!-- Small Gear -->
                        <g transform="translate(25, 25)">
                            <circle cx="0" cy="0" r="15" fill="none" stroke="currentColor" stroke-width="2"/>
                            <circle cx="0" cy="0" r="6" fill="currentColor" opacity="0.3"/>
                            <!-- Gear Teeth -->
                            <rect x="-1.5" y="-18" width="3" height="6" fill="currentColor" opacity="0.6"/>
                            <rect x="-1.5" y="12" width="3" height="6" fill="currentColor" opacity="0.6"/>
                            <rect x="-18" y="-1.5" width="6" height="3" fill="currentColor" opacity="0.6"/>
                            <rect x="12" y="-1.5" width="6" height="3" fill="currentColor" opacity="0.6"/>
                        </g>
                        
                        <!-- Wrench -->
                        <g transform="translate(0, 0)">
                            <path d="M -15 -15 L -5 -5 L 5 5 L 15 15" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                            <path d="M -15 -15 L -20 -10 L -15 -5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                        </g>
                        
                        <!-- Screwdriver -->
                        <g transform="translate(-10, 10)">
                            <line x1="0" y1="0" x2="0" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M -3 0 L 3 0 L 0 -5 Z" fill="currentColor" opacity="0.8"/>
                        </g>
                        
                        <!-- Progress Indicator -->
                        <g transform="translate(0, -50)">
                            <circle cx="0" cy="0" r="12" fill="none" stroke="currentColor" stroke-width="2" opacity="0.3"/>
                            <circle cx="0" cy="0" r="12" fill="none" stroke="currentColor" stroke-width="2" 
                                    stroke-dasharray="37.7" stroke-dashoffset="18.85" 
                                    transform="rotate(-90)" class="animate-spin">
                                <animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" dur="2s" repeatCount="indefinite"/>
                            </circle>
                        </g>
                    </g>
                </svg>
            </div>
        </div>

        <!-- Maintenance Message -->
        <div class="space-y-4">
            <h1 class="text-3xl font-bold text-purple-900">
                سایت در حال بروزرسانی!
            </h1>
            <p class="text-lg text-gray-600 leading-relaxed">
                در حال حاضر سایت در حال بروزرسانی و بهبود است. لطفاً کمی صبر کنید و دوباره تلاش کنید.
            </p>
        </div>

        <!-- Progress Bar -->
        <div class="mt-8">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-purple-900">پیشرفت بروزرسانی</span>
                <span class="text-sm text-purple-600">75%</span>
            </div>
            <div class="w-full bg-purple-200 rounded-full h-2">
                <div class="bg-purple-600 h-2 rounded-full transition-all duration-1000 ease-out" style="width: 75%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">تخمین زمان باقی‌مانده: ۱۵ دقیقه</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center pt-8">
            <button onclick="window.location.reload()" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-purple-500 hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300 transform hover:scale-105">
                <x-tabler-refresh class="w-5 h-5 ml-2" />
                بررسی مجدد
            </button>
                            <a href="mailto:{{ \App\Helpers\SettingsHelper::getSupportEmail() }}" 
                   class="inline-flex items-center px-6 py-3 border border-purple-300 text-base font-medium rounded-lg text-purple-700 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300">
                <x-tabler-mail class="w-5 h-5 ml-2" />
                اطلاع‌رسانی
            </a>
        </div>

        <!-- Maintenance Info -->
        <div class="mt-8 p-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl border border-purple-100">
            <h3 class="text-lg font-semibold text-purple-900 mb-3">چه اتفاقی در حال رخ دادن است؟</h3>
            <div class="space-y-3 text-right">
                <div class="flex items-start gap-3">
                    <x-tabler-settings class="w-5 h-5 text-purple-500 mt-0.5 flex-shrink-0" />
                    <span class="text-sm text-gray-700">بروزرسانی سیستم‌های امنیتی</span>
                </div>
                <div class="flex items-start gap-3">
                    <x-tabler-database class="w-5 h-5 text-purple-500 mt-0.5 flex-shrink-0" />
                    <span class="text-sm text-gray-700">بهینه‌سازی پایگاه داده</span>
                </div>
                <div class="flex items-start gap-3">
                    <x-tabler-rocket class="w-5 h-5 text-purple-500 mt-0.5 flex-shrink-0" />
                    <span class="text-sm text-gray-700">افزودن قابلیت‌های جدید</span>
                </div>
            </div>
        </div>

        <!-- Alternative Contact -->
        <div class="mt-8 p-6 bg-gradient-to-r from-sky-50 to-sky-50 rounded-2xl border border-sky-100">
            <h3 class="text-lg font-semibold text-sky-900 mb-3">نیاز فوری دارید؟</h3>
            <p class="text-sm text-gray-600 mb-4">
                در صورت نیاز فوری، می‌توانید از طریق کانال‌های زیر با ما در ارتباط باشید.
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="tel:+989123456789" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-sky-300 text-sm font-medium rounded-lg text-sky-700 bg-white hover:bg-sky-50 transition-colors duration-300">
                    <x-tabler-phone class="w-4 h-4 ml-2" />
                    تماس تلفنی
                </a>
                <a href="{{ \App\Helpers\SettingsHelper::getTelegramUrl() }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-sky-300 text-sm font-medium rounded-lg text-sky-700 bg-white hover:bg-sky-50 transition-colors duration-300">
                    <x-tabler-brand-telegram class="w-4 h-4 ml-2" />
                    تلگرام
                </a>
            </div>
        </div>

        <!-- Status Updates -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-sky-900 mb-4">آخرین بروزرسانی‌ها</h3>
            <div class="space-y-3 text-right">
                <div class="p-3 bg-white border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start">
                        <span class="text-xs text-gray-500">{{ \Verta::instance(now()->subMinutes(5))->format('H:i') }}</span>
                        <span class="text-sm text-gray-800">بروزرسانی سیستم‌های امنیتی تکمیل شد</span>
                    </div>
                </div>
                <div class="p-3 bg-white border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start">
                        <span class="text-xs text-gray-500">{{ \Verta::instance(now()->subMinutes(10))->format('H:i') }}</span>
                        <span class="text-sm text-gray-800">بهینه‌سازی پایگاه داده در حال انجام</span>
                    </div>
                </div>
                <div class="p-3 bg-white border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start">
                        <span class="text-xs text-gray-500">{{ \Verta::instance(now()->subMinutes(15))->format('H:i') }}</span>
                        <span class="text-sm text-gray-800">شروع فرآیند بروزرسانی</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto Refresh Notice -->
        <div class="mt-8 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-100">
            <div class="flex items-center gap-3">
                <x-tabler-clock class="w-6 h-6 text-green-600" />
                <div class="text-right">
                    <h4 class="text-sm font-semibold text-green-900">بروزرسانی خودکار</h4>
                    <p class="text-xs text-green-700">صفحه هر ۳۰ ثانیه به‌طور خودکار بروزرسانی می‌شود</p>
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

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 2s linear infinite;
}
</style>

<script>
// Auto refresh every 30 seconds
setInterval(function() {
    window.location.reload();
}, 30000);
</script>
@endsection 