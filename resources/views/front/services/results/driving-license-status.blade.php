@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-sky-50 to-indigo-50 rounded-xl p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">نتیجه استعلام وضعیت گواهینامه</h1>
                <p class="text-gray-600">وضعیت گواهینامه‌های رانندگی</p>
            </div>
            <div class="text-right">
                @php
                    $hasLicenses = $data['data']['has_licenses'] ?? false;
                    $licenseCount = $data['data']['license_count'] ?? 0;
                    $statusColor = $hasLicenses ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    $statusText = $hasLicenses ? "دارای $licenseCount گواهینامه" : 'فاقد گواهینامه';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        @if($hasLicenses)
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
            <!-- Summary Statistics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    خلاصه گواهینامه‌ها
                </h2>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="text-center bg-sky-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-sky-600 mb-1">{{ $data['data']['license_count'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">کل گواهینامه‌ها</div>
                    </div>
                    <div class="text-center bg-green-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600 mb-1">{{ $data['data']['statistics']['valid_count'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">معتبر</div>
                    </div>
                    <div class="text-center bg-purple-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-purple-600 mb-1">{{ count($data['data']['statistics']['type_breakdown'] ?? []) }}</div>
                        <div class="text-sm text-gray-600">انواع مختلف</div>
                    </div>
                </div>
                
                @if(!empty($data['data']['summary']))
                <div class="mt-4 p-4 bg-sky-50 rounded-lg">
                    <p class="text-sky-800 text-center">{{ $data['data']['summary'] }}</p>
                </div>
                @endif
            </div>

            <!-- Licenses List -->
            @if(!empty($data['data']['licenses']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-6 0h6zm6-6a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M15 14a3.001 3.001 0 00-6 0h6z"></path>
                    </svg>
                    جزئیات گواهینامه‌ها
                </h2>
                
                <div class="space-y-4">
                    @foreach($data['data']['licenses'] as $license)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-1">{{ $license['full_name'] ?? 'نام نامشخص' }}</h3>
                                <p class="text-gray-600 text-sm">گواهینامه {{ $license['license_type'] ?? 'نامشخص' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($license['status_color'] === 'green') bg-green-100 text-green-800
                                    @elseif($license['status_color'] === 'yellow') bg-yellow-100 text-yellow-800
                                    @elseif($license['status_color'] === 'red') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $license['license_status'] ?? 'نامشخص' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="bg-gray-50 rounded p-3">
                                <label class="block font-medium text-gray-600 mb-1">شماره گواهینامه</label>
                                <p class="text-gray-800 font-mono">{{ $license['license_number'] ?? 'نامشخص' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-3">
                                <label class="block font-medium text-gray-600 mb-1">نوع گواهینامه</label>
                                <p class="text-gray-800">{{ $license['license_type'] ?? 'نامشخص' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-3">
                                <label class="block font-medium text-gray-600 mb-1">تاریخ صدور</label>
                                <p class="text-gray-800">{{ $license['issue_date_persian'] ?: ($license['issue_date'] ?? 'نامشخص') }}</p>
                            </div>
                        </div>
                        
                        @if(!empty($license['validity_period']))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <label class="block font-medium text-gray-600 mb-1">دوره اعتبار</label>
                                    <p class="text-gray-800">{{ $license['formatted_validity'] ?? $license['validity_period'] }}</p>
                                </div>
                                <div>
                                    <label class="block font-medium text-gray-600 mb-1">کد ملی</label>
                                    <p class="text-gray-800 font-mono">{{ $license['national_code'] ?? 'نامشخص' }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- License Types Breakdown -->
            @if(!empty($data['data']['statistics']['type_breakdown']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    تفکیک انواع گواهینامه
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($data['data']['statistics']['type_breakdown'] as $type => $count)
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-purple-600 mb-1">{{ $count }}</div>
                        <div class="text-sm text-gray-600">{{ $type }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- License Status Info -->
            @if(!empty($data['data']['license_status']['description']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    وضعیت استعلام
                </h2>
                
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-green-800 font-medium">{{ $data['data']['license_status']['description'] }}</p>
                            <p class="text-green-600 text-sm">کد: {{ $data['data']['license_status']['code'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
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
                    
                    <a href="{{ route('services.show', 'driving-license-status') }}" 
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
                    @if($hasLicenses)
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        گواهینامه‌های معتبر خود را همراه داشته باشید
                    </li>
                    @else
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        برای دریافت گواهینامه به آموزشگاه رانندگی مراجعه کنید
                    </li>
                    @endif
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        گواهینامه منقضی شده قابل استفاده نیست
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        برای تمدید گواهینامه قبل از انقضا اقدام کنید
                    </li>
                </ul>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">آمار سریع</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاریخ استعلام:</span>
                        <span class="font-medium">{{ $data['data']['processed_date'] ?? now()->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">کد پیگیری:</span>
                        <span class="font-medium font-mono text-xs">{{ $data['data']['track_id'] ?? 'نامشخص' }}</span>
                    </div>
                    @if(!empty($data['data']['response_code']))
                    <div class="flex justify-between">
                        <span class="text-gray-600">کد پاسخ:</span>
                        <span class="font-medium font-mono text-xs">{{ $data['data']['response_code'] }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyReport() {
    const licenseData = @json($data['data']);
    let text = 'گزارش وضعیت گواهینامه\n';
    text += '='.repeat(40) + '\n\n';
    text += `تاریخ استعلام: ${licenseData.processed_date || new Date().toLocaleDateString('fa-IR')}\n`;
    text += `تعداد گواهینامه‌ها: ${licenseData.license_count || 0}\n\n`;
    
    if (licenseData.licenses && licenseData.licenses.length > 0) {
        licenseData.licenses.forEach((license, index) => {
            text += `گواهینامه ${index + 1}:\n`;
            text += `نام: ${license.full_name || 'نامشخص'}\n`;
            text += `شماره گواهینامه: ${license.license_number || 'نامشخص'}\n`;
            text += `نوع: ${license.license_type || 'نامشخص'}\n`;
            text += `وضعیت: ${license.license_status || 'نامشخص'}\n`;
            if (license.issue_date_persian || license.issue_date) {
                text += `تاریخ صدور: ${license.issue_date_persian || license.issue_date}\n`;
            }
            if (license.formatted_validity || license.validity_period) {
                text += `دوره اعتبار: ${license.formatted_validity || license.validity_period}\n`;
            }
            text += '\n';
        });
    } else {
        text += 'هیچ گواهینامه‌ای یافت نشد.\n\n';
    }
    
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