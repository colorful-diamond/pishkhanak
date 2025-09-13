@extends('front.layouts.app')

@push('styles')
@vite(['resources/css/service-content.css'])
@endpush

@section('content')
<div class="container mx-auto px-4 py-4">
    <!-- Error Messages (Top of Page) -->
    @if(session('error'))
        <div class="mb-6 animate-slide-down">
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="mr-3 flex-1">
                        <h3 class="text-sm font-semibold text-red-800">خطا در پردازش</h3>
                        <p class="mt-1 text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="mr-auto flex-shrink-0 text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Messages -->
    @if(session('success'))
        <div class="mb-6 animate-slide-down">
            <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="mr-3 flex-1">
                        <h3 class="text-sm font-semibold text-green-800">موفقیت‌آمیز</h3>
                        <p class="mt-1 text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="mr-auto flex-shrink-0 text-green-400 hover:text-green-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Preview Header -->
    <div class="bg-gradient-to-l from-sky-50 via-blue-50 to-indigo-50 rounded-3xl p-8 mb-8">
        <div class="max-w-4xl mx-auto text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-12 h-12 bg-sky-600 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-dark-sky-700">رتبه اعتباری بانک مهر ایران - پیشخوانک</h1>
            </div>
            
            <p class="text-gray-700 leading-relaxed text-lg">
                استعلام اعتبارسنجی بانک مهر ایران جهت دریافت تسهیلات قرض الحسنه. 
                بررسی رتبه اعتباری A تا E و شرایط واجد شرایط بودن برای دریافت وام تا 500 میلیون تومان.
            </p>
        </div>
    </div>

    <!-- Results Display Area -->
    <div id="results-section" class="space-y-6">
        @if(isset($results))
            <!-- Display results here -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">نتایج اعتبارسنجی بانک مهر ایران</h2>
                <!-- Results content would go here -->
                <div class="text-center py-8">
                    <div class="text-6xl mb-4">📊</div>
                    <p class="text-gray-600">نتایج اعتبارسنجی در حال پردازش...</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Related Services -->
    <div class="mt-12">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">خدمات مرتبط</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{ route('services.show', 'credit-score-rating') }}" class="group block">
                <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 p-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">اعتبارسنجی کلی بانکی</h4>
                        <p class="text-sm text-gray-600">بررسی رتبه اعتباری از تمام بانک‌های کشور</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Add any specific JavaScript for Mehr Iran Bank service here
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any special functionality for this service
    console.log('Mehr Iran Bank Credit Assessment Preview loaded');
});
</script>
@endpush
@endsection