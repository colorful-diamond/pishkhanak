@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->

        <!-- Result Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Result Header -->
            <div class="bg-green-50 p-3 border-b border-green-200">
                <div class="flex items-center justify-between">
                    <h4 class="text-md font-semibold text-green-800">
                        <svg class="w-6 h-6 ml-1 inline-block" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        استعلام موفق
                    </h4>
                    <span class="text-sm font-semibold text-green-600">
                        {{ verta($localRequest['completed_at'])->format('Y/m/d H:i') }}
                    </span>
                </div>
            </div>

            <!-- Result Content -->
            <div class="p-4">
                @if(isset($resultData['message']))
                    <div class="mb-6">
                        <div class="bg-sky-50 border border-sky-200 rounded-lg p-4">
                            <p class="text-sky-800">{{ $resultData['message'] }}</p>
                        </div>
                    </div>
                @endif

                @if(isset($resultData['data']) && is_array($resultData['data']) && count($resultData['data']) > 0)
                    <!-- Data Display -->
                    <div class="space-y-4">
                        @foreach($resultData['data'] as $key => $value)
                            @if(!is_array($value))
                                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                    <span class="font-medium text-gray-700">
                                        @php
                                            $persianLabels = [
                                                'mobile' => 'شماره موبایل',
                                                'national_code' => 'کد ملی',
                                                'estimated_delivery' => 'زمان تحویل',
                                                'inquiry_type' => 'نوع استعلام',
                                                'status' => 'وضعیت',
                                                'reference_code' => 'کد پیگیری',
                                                'amount' => 'مبلغ',
                                                'date' => 'تاریخ',
                                                'time' => 'زمان'
                                            ];
                                        @endphp
                                        {{ $persianLabels[$key] ?? $key }}:
                                    </span>
                                    <span class="text-gray-900">{{ $value }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if(isset($resultData['code']) && $resultData['code'] === 'CREDIT_SCORE_SMS_SENT' || isset($resultData['completion_type']) && $resultData['completion_type'] === 'sms_sent')
                    <!-- Credit Score Specific Result -->
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            درخواست شما در صف قرار گرفت
                        </h3>
                        <p class="text-gray-600 mb-4">
                            نتیجه استعلام اعتبارسنجی تا 15 دقیقه دیگر به شماره موبایل شما ارسال خواهد شد.
                        </p>
                        
                        @if(isset($resultData['data']['mobile']))
                            <div class="inline-flex items-center px-4 py-2 bg-sky-50 rounded-lg">
                                <svg class="w-5 h-5 text-gray-600 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="text-gray-700 font-medium">
                                    {{ $resultData['data']['mobile'] }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="bg-sky-50 px-4 py-4 flex flex-col sm:flex-row justify-between items-center gap-3">
                <a href="{{ route('services.show', $service->slug) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-sky-50 transition-colors duration-200 w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    استعلام مجدد
                </a>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <a href="{{ route('app.page.home') }}" 
                       class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors duration-200 justify-center">
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        صفحه اصلی
                    </a>
                </div>
            </div>
        </div>

        {{-- <!-- Request Info -->
        <div class="mt-6 bg-sky-50 rounded-lg p-4 text-sm text-gray-600">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="font-medium">شناسه درخواست:</span>
                    <code class="ml-2 text-xs bg-sky-200 px-2 py-1 rounded">{{ $localRequest['hash'] }}</code>
                </div>
                <div>
                    <span class="font-medium">زمان شروع:</span>
                    <span class="ml-2">{{ \Carbon\Carbon::parse($localRequest['started_at'])->format('Y/m/d H:i:s') }}</span>
                </div>
                <div>
                    <span class="font-medium">مدت پردازش:</span>
                    <span class="ml-2">{{ \Carbon\Carbon::parse($localRequest['started_at'])->diffInSeconds(\Carbon\Carbon::parse($localRequest['completed_at'])) }} ثانیه</span>
                </div>
            </div>
        </div> --}}
    </div>
</div>

@push('scripts')
<script>
function printResult() {
    window.print();
}

function shareResult() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $service->title }}',
            text: 'نتیجه استعلام {{ $service->title }}',
            url: window.location.href
        });
    } else {
        // Fallback to copying URL
        navigator.clipboard.writeText(window.location.href).then(() => {
            showMessage('لینک نتیجه کپی شد', 'success');
        }).catch(() => {
            showMessage('خطا در کپی لینک', 'error');
        });
    }
}

function showMessage(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' 
            ? 'bg-green-50 border border-green-200 text-green-800' 
            : 'bg-red-50 border border-red-200 text-red-800'
    }`;
    alertDiv.textContent = message;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}
</script>

@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        print-color-adjust: exact;
    }
}
@endpush
@endsection 