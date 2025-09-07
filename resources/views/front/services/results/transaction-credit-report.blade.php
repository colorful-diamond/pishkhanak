@extends('front.layouts.app')

@section('title', 'نتیجه گزارش اعتبار تراکنش')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-sky-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">گزارش اعتبار تراکنش</h1>
                        <p class="text-gray-600">تحلیل و گزارش وضعیت اعتباری بر اساس تراکنش‌ها</p>
                    </div>
                </div>
                <div class="text-left">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        موفق
                    </span>
                </div>
            </div>
        </div>

        <!-- Input Information -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">اطلاعات ورودی</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">کد ملی</div>
                    <div class="font-medium text-gray-900 font-mono">{{ $data['input_info']['national_code'] ?? 'نامشخص' }}</div>
                </div>
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">شناسه پیگیری</div>
                    <div class="font-medium text-gray-900">{{ $data['input_info']['track_id'] ?? 'نامشخص' }}</div>
                </div>
            </div>
        </div>

        <!-- Report Analysis -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">تحلیل گزارش</h2>
            
            @if(isset($data['report_analysis']))
            <div class="space-y-6">
                <!-- Overall Status -->
                <div class="bg-sky-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-sky-900 mb-4">وضعیت کلی</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-sky-600 mb-1">وضعیت اعتبار</div>
                            <div class="font-bold text-sky-900 text-lg">{{ $data['report_analysis']['credit_status'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-sky-600 mb-1">امتیاز اعتباری</div>
                            <div class="font-bold text-sky-900 text-lg">{{ $data['report_analysis']['credit_score'] ?? 'نامشخص' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Key Metrics -->
                <div class="bg-sky-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">شاخص‌های کلیدی</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">تعداد تراکنش‌ها</div>
                            <div class="font-bold text-gray-900 text-lg">{{ $data['report_analysis']['total_transactions'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">حجم کل تراکنش‌ها</div>
                            <div class="font-bold text-gray-900">{{ $data['report_analysis']['total_volume_formatted'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">میانگین تراکنش</div>
                            <div class="font-bold text-gray-900">{{ $data['report_analysis']['average_transaction_formatted'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">تعداد حساب‌ها</div>
                            <div class="font-bold text-gray-900">{{ $data['report_analysis']['account_count'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">تعداد بانک‌ها</div>
                            <div class="font-bold text-gray-900">{{ $data['report_analysis']['bank_count'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">دوره زمانی</div>
                            <div class="font-bold text-gray-900">{{ $data['report_analysis']['period'] ?? 'نامشخص' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Risk Analysis -->
                @if(isset($data['report_analysis']['risk_factors']))
                <div class="bg-red-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-red-900 mb-4">عوامل ریسک</h3>
                    <div class="space-y-3">
                        @foreach($data['report_analysis']['risk_factors'] as $factor)
                        <div class="bg-white rounded-lg p-4 border-l-4 border-red-500">
                            <div class="font-medium text-red-900">{{ $factor['title'] ?? 'نامشخص' }}</div>
                            <div class="text-sm text-red-700 mt-1">{{ $factor['description'] ?? 'نامشخص' }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Positive Indicators -->
                @if(isset($data['report_analysis']['positive_indicators']))
                <div class="bg-green-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-900 mb-4">شاخص‌های مثبت</h3>
                    <div class="space-y-3">
                        @foreach($data['report_analysis']['positive_indicators'] as $indicator)
                        <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
                            <div class="font-medium text-green-900">{{ $indicator['title'] ?? 'نامشخص' }}</div>
                            <div class="text-sm text-green-700 mt-1">{{ $indicator['description'] ?? 'نامشخص' }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Recommendations -->
                @if(isset($data['report_analysis']['recommendations']))
                <div class="bg-yellow-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-4">توصیه‌ها</h3>
                    <div class="space-y-3">
                        @foreach($data['report_analysis']['recommendations'] as $recommendation)
                        <div class="bg-white rounded-lg p-4 border-l-4 border-yellow-500">
                            <div class="font-medium text-yellow-900">{{ $recommendation['title'] ?? 'نامشخص' }}</div>
                            <div class="text-sm text-yellow-700 mt-1">{{ $recommendation['description'] ?? 'نامشخص' }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Detailed Analysis -->
                @if(isset($data['report_analysis']['detailed_analysis']))
                <div class="bg-sky-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">تحلیل تفصیلی</h3>
                    <div class="prose max-w-none">
                        <div class="bg-white rounded-lg p-6">
                            <div class="text-gray-900 leading-relaxed">
                                {!! nl2br(e($data['report_analysis']['detailed_analysis'])) !!}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">گزارشی یافت نشد</h3>
                <p class="text-gray-600">برای کد ملی وارد شده، گزارش اعتباری یافت نشد.</p>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('front.services.show', $service->slug) }}" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-medium py-3 px-6 rounded-lg text-center transition duration-200">
                    استعلام جدید
                </a>
                <button onclick="window.print()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200">
                    چاپ نتیجه
                </button>
                <a href="{{ route('front.dashboard') }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg text-center transition duration-200">
                    بازگشت به داشبورد
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style media="print">
    @media print {
        .container { max-width: none; }
        .shadow-lg { box-shadow: none; }
        .bg-white { background: white !important; }
        .text-gray-900 { color: black !important; }
        .text-gray-600 { color: #666 !important; }
        .bg-sky-50 { background: #f0f8ff !important; }
        .bg-green-50 { background: #f0fff4 !important; }
        .bg-red-50 { background: #fef2f2 !important; }
        .bg-yellow-50 { background: #fffbeb !important; }
        .bg-sky-50 { background: #f9f9f9 !important; }
        .bg-green-100 { background: #f0fff4 !important; }
        .bg-sky-100 { background: #ebf8ff !important; }
        .text-sky-600 { color: #2563eb !important; }
        .text-sky-900 { color: #1e3a8a !important; }
        .text-green-600 { color: #059669 !important; }
        .text-green-900 { color: #14532d !important; }
        .text-green-800 { color: #166534 !important; }
        .text-red-900 { color: #7f1d1d !important; }
        .text-red-700 { color: #b91c1c !important; }
        .text-yellow-900 { color: #92400e !important; }
        .text-yellow-700 { color: #a16207 !important; }
        .text-yellow-600 { color: #d97706 !important; }
        .rounded-lg { border-radius: 0.5rem; }
        .rounded-full { border-radius: 9999px; }
        .border-l-4 { border-left-width: 4px; }
        .border-red-500 { border-color: #ef4444; }
        .border-green-500 { border-color: #10b981; }
        .border-yellow-500 { border-color: #f59e0b; }
        .font-bold { font-weight: bold; }
        .font-medium { font-weight: 500; }
        .font-mono { font-family: monospace; }
        .text-sm { font-size: 0.875rem; }
        .text-lg { font-size: 1.125rem; }
        .text-2xl { font-size: 1.5rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-6 { margin-top: 1.5rem; }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .w-12 { width: 3rem; }
        .h-12 { height: 3rem; }
        .w-6 { width: 1.5rem; }
        .h-6 { height: 1.5rem; }
        .w-8 { width: 2rem; }
        .h-8 { height: 2rem; }
        .w-16 { width: 4rem; }
        .h-16 { height: 4rem; }
        .w-4 { width: 1rem; }
        .h-4 { height: 1rem; }
        .space-y-3 > * + * { margin-top: 0.75rem; }
        .space-y-4 > * + * { margin-top: 1rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .justify-between { justify-content: space-between; }
        .text-center { text-align: center; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .space-x-4 > * + * { margin-right: 1rem; }
        .space-x-reverse > * + * { margin-right: 0; margin-left: 1rem; }
        .inline-flex { display: inline-flex; }
        .mr-1 { margin-right: 0.25rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .leading-relaxed { line-height: 1.625; }
        .max-w-none { max-width: none; }
        .prose { color: #374151; }
        .hidden { display: none; }
    }
</style>
@endsection 