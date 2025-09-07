@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-sky-50 to-indigo-50 rounded-xl p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">نتیجه استعلام نمره منفی گواهینامه</h1>
                <p class="text-gray-600">نمره منفی گواهینامه رانندگی شماره {{ $data['data']['license_number'] ?? 'نامشخص' }}</p>
            </div>
            <div class="text-right">
                @php
                    $score = (int)($data['data']['negative_score'] ?? 0);
                    $statusColor = $score == 0 ? 'bg-green-100 text-green-800' : ($score <= 10 ? 'bg-yellow-100 text-yellow-800' : ($score <= 20 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'));
                    $statusText = $data['data']['score_status']['text'] ?? 'نامشخص';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        @if($score == 0)
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        @else
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        @endif
                    </svg>
                    {{ $statusText }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Score Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    خلاصه نمره منفی
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center {{ $score == 0 ? 'bg-green-50' : ($score <= 10 ? 'bg-yellow-50' : ($score <= 20 ? 'bg-orange-50' : 'bg-red-50')) }} rounded-lg p-6">
                        <div class="text-4xl font-bold {{ $score == 0 ? 'text-green-600' : ($score <= 10 ? 'text-yellow-600' : ($score <= 20 ? 'text-orange-600' : 'text-red-600')) }} mb-2">{{ $data['data']['negative_score'] ?? '0' }}</div>
                        <div class="text-sm text-gray-600">امتیاز منفی</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $data['data']['score_status']['description'] ?? '' }}</div>
                    </div>
                    
                    @if(!empty($data['data']['offense_count']) && $data['data']['offense_count'] !== null)
                    <div class="text-center bg-sky-50 rounded-lg p-6">
                        <div class="text-4xl font-bold text-sky-600 mb-2">{{ $data['data']['offense_count'] }}</div>
                        <div class="text-sm text-gray-600">تعداد تخلف</div>
                    </div>
                    @endif
                    
                    <div class="text-center bg-gray-50 rounded-lg p-6">
                        <div class="text-lg font-bold text-gray-600 mb-2">{{ $data['data']['license_number'] ?? 'نامشخص' }}</div>
                        <div class="text-sm text-gray-600">شماره گواهینامه</div>
                    </div>
                </div>
                
                @if(!empty($data['data']['summary']))
                <div class="mt-6 p-4 bg-sky-50 rounded-lg">
                    <p class="text-sky-800 text-center">{{ $data['data']['summary'] }}</p>
                </div>
                @endif
            </div>

            <!-- License Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-6 0h6zm6-6a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M15 14a3.001 3.001 0 00-6 0h6z"></path>
                    </svg>
                    جزئیات گواهینامه
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-sky-50 rounded-lg p-4">
                        <label class="block font-medium text-gray-600 mb-2">شماره گواهینامه</label>
                        <p class="text-gray-800 font-mono text-lg">{{ $data['data']['license_number'] ?? 'نامشخص' }}</p>
                    </div>
                    
                    <div class="bg-sky-50 rounded-lg p-4">
                        <label class="block font-medium text-gray-600 mb-2">نمره منفی</label>
                        <p class="text-gray-800 text-lg">{{ $data['data']['formatted_score'] ?? '0 امتیاز منفی' }}</p>
                    </div>
                    
                    @if(!empty($data['data']['rule']) && $data['data']['rule'] !== '-')
                    <div class="bg-sky-50 rounded-lg p-4">
                        <label class="block font-medium text-gray-600 mb-2">حکم</label>
                        <p class="text-gray-800">{{ $data['data']['rule'] }}</p>
                    </div>
                    @endif
                    
                    <div class="bg-sky-50 rounded-lg p-4">
                        <label class="block font-medium text-gray-600 mb-2">تاریخ استعلام</label>
                        <p class="text-gray-800">{{ $data['data']['processed_date'] ?? now()->format('Y/m/d H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <!-- Score Analysis -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    تحلیل نمره
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-lg font-bold text-green-600 mb-1">0</div>
                        <div class="text-sm text-gray-600">وضعیت مطلوب</div>
                        <div class="text-xs text-gray-500 mt-1">بدون امتیاز منفی</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                        <div class="text-lg font-bold text-yellow-600 mb-1">1-10</div>
                        <div class="text-sm text-gray-600">وضعیت قابل قبول</div>
                        <div class="text-xs text-gray-500 mt-1">در حد مجاز</div>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4 text-center">
                        <div class="text-lg font-bold text-orange-600 mb-1">11-20</div>
                        <div class="text-sm text-gray-600">وضعیت هشدار</div>
                        <div class="text-xs text-gray-500 mt-1">نزدیک به تعلیق</div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 text-center">
                        <div class="text-lg font-bold text-red-600 mb-1">20+</div>
                        <div class="text-sm text-gray-600">وضعیت خطرناک</div>
                        <div class="text-xs text-gray-500 mt-1">احتمال تعلیق</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">عملیات سریع</h3>
                
                <div class="space-y-3">
                    <button onclick="printReport()" 
                            class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        چاپ گزارش
                    </button>
                    
                    <button onclick="copyReport()" 
                            class="w-full flex items-center justify-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        کپی گزارش
                    </button>
                    
                    <a href="{{ route('services.show', 'negative-license-score') }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        استعلام جدید
                    </a>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-sky-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-sky-800 mb-3">نکات مهم</h3>
                <ul class="space-y-2 text-sky-700 text-sm">
                    @if($score == 0)
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        عالی! گواهینامه شما بدون امتیاز منفی است
                    </li>
                    @else
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        از قوانین راهنمایی و رانندگی پیروی کنید
                    </li>
                    @endif
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        امتیاز منفی ۲۰ و بالاتر موجب تعلیق گواهینامه می‌شود
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        در صورت تجمع امتیاز منفی، دوره آموزشی الزامی است
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function copyReport() {
    const licenseData = @json($data['data']);
    let text = 'گزارش نمره منفی گواهینامه\n';
    text += '='.repeat(40) + '\n\n';
    text += `شماره گواهینامه: ${licenseData.license_number || 'نامشخص'}\n`;
    text += `نمره منفی: ${licenseData.negative_score || '0'}\n`;
    if (licenseData.offense_count !== null) text += `تعداد تخلف: ${licenseData.offense_count}\n`;
    if (licenseData.rule && licenseData.rule !== '-') text += `حکم: ${licenseData.rule}\n`;
    text += `وضعیت: ${licenseData.score_status ? licenseData.score_status.text : 'نامشخص'}\n`;
    text += `تاریخ استعلام: ${licenseData.processed_date || new Date().toLocaleDateString('fa-IR')}\n\n`;
    if (licenseData.summary) text += `خلاصه: ${licenseData.summary}\n\n`;
    text += 'تولید شده در پیشخوانک (pishkhanak.com)';
    
    copyToClipboard(text);
}

function printReport() {
    window.print();
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('گزارش با موفقیت کپی شد!', 'success');
        });
    } else {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showNotification('گزارش با موفقیت کپی شد!', 'success');
        } catch (err) {
            showNotification('خطا در کپی کردن', 'error');
        }
        document.body.removeChild(textArea);
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => notification.classList.add('translate-x-0'), 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 3000);
}
</script>
@endsection