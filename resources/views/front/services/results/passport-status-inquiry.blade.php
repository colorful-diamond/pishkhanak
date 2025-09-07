@extends('front.layouts.app')

@section('title', 'نتیجه استعلام وضعیت گذرنامه')

@section('content')
<div class="min-h-screen/2 bg-gradient-to-br from-purple-50 to-blue-50 py-8" dir="rtl">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">استعلام وضعیت گذرنامه</h1>
            <p class="text-gray-600">گزارش جامع وضعیت گذرنامه و اطلاعات مربوطه</p>
        </div>

        @if(isset($data['status']) && $data['status'] === 'success')
        @php $result = $data['data']; @endphp

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-wrap gap-3 justify-center">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    چاپ گزارش
                </button>
                <a href="{{ route('services.show', 'passport-status-inquiry') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    استعلام مجدد
                </a>
                <a href="{{ route('app.page.home') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    صفحه اصلی
                </a>
            </div>
        </div>

        <!-- Status Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Passport Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $hasPassport = $result['passport_info']['has_passport'] ?? false;
                    $statusColor = $hasPassport ? 'text-green-600' : 'text-gray-600';
                    $statusBg = $hasPassport ? 'bg-green-100' : 'bg-gray-100';
                    $statusIcon = $hasPassport ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12';
                @endphp
                <div class="text-center">
                    <div class="w-16 h-16 {{ $statusBg }} rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 {{ $statusColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcon }}"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">وضعیت گذرنامه</h3>
                    <p class="text-sm {{ $statusColor }} font-medium">
                        {{ $hasPassport ? 'دارای گذرنامه' : 'عدم وجود گذرنامه' }}
                    </p>
                </div>
            </div>

            <!-- Request Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $hasRequest = $result['passport_info']['passport_request'] ?? false;
                    $requestColor = $hasRequest ? 'text-blue-600' : 'text-gray-600';
                    $requestBg = $hasRequest ? 'bg-blue-100' : 'bg-gray-100';
                @endphp
                <div class="text-center">
                    <div class="w-16 h-16 {{ $requestBg }} rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 {{ $requestColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">وضعیت درخواست</h3>
                    <p class="text-sm {{ $requestColor }} font-medium">
                        {{ $hasRequest ? 'دارای درخواست' : 'عدم درخواست' }}
                    </p>
                </div>
            </div>

            <!-- Passport Validity -->
            @if($hasPassport)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $isValid = $result['analysis']['is_valid'] ?? false;
                    $validityColor = $isValid ? 'text-green-600' : 'text-red-600';
                    $validityBg = $isValid ? 'bg-green-100' : 'bg-red-100';
                @endphp
                <div class="text-center">
                    <div class="w-16 h-16 {{ $validityBg }} rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 {{ $validityColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">اعتبار گذرنامه</h3>
                    <p class="text-sm {{ $validityColor }} font-medium">
                        {{ $isValid ? 'معتبر' : 'نامعتبر' }}
                    </p>
                </div>
            </div>
            @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">اعتبار گذرنامه</h3>
                    <p class="text-sm text-gray-600 font-medium">نامشخص</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Personal Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                اطلاعات هویتی
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">کد ملی</div>
                    <div class="font-semibold text-gray-900">{{ $result['national_code'] ?? 'نامشخص' }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">شماره موبایل</div>
                    <div class="font-semibold text-gray-900">{{ $result['mobile'] ?? 'نامشخص' }}</div>
                </div>
            </div>
        </div>

        @if($hasPassport)
        <!-- Passport Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                اطلاعات گذرنامه
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($result['passport_info']['passport_number'])
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="text-sm text-blue-600 mb-1">شماره گذرنامه</div>
                    <div class="font-semibold text-blue-900">{{ $result['passport_info']['passport_number'] }}</div>
                </div>
                @endif
                
                @if($result['passport_info']['passport_serial'])
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="text-sm text-purple-600 mb-1">سریال گذرنامه</div>
                    <div class="font-semibold text-purple-900">{{ $result['passport_info']['passport_serial'] }}</div>
                </div>
                @endif

                @if($result['passport_info']['passport_status'])
                <div class="bg-{{ $result['analysis']['is_valid'] ? 'green' : 'red' }}-50 rounded-lg p-4 border border-{{ $result['analysis']['is_valid'] ? 'green' : 'red' }}-200">
                    <div class="text-sm text-{{ $result['analysis']['is_valid'] ? 'green' : 'red' }}-600 mb-1">وضعیت گذرنامه</div>
                    <div class="font-semibold text-{{ $result['analysis']['is_valid'] ? 'green' : 'red' }}-900">{{ $result['passport_info']['passport_status'] }}</div>
                </div>
                @endif

                @if($result['passport_info']['issue_date'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">تاریخ صدور</div>
                    <div class="font-semibold">{{ $result['passport_info']['issue_date'] }}</div>
                </div>
                @endif

                @if($result['passport_info']['validity_date'])
                <div class="bg-{{ $result['analysis']['is_expired'] ? 'red' : 'green' }}-50 rounded-lg p-4 border border-{{ $result['analysis']['is_expired'] ? 'red' : 'green' }}-200">
                    <div class="text-sm text-{{ $result['analysis']['is_expired'] ? 'red' : 'green' }}-600 mb-1">تاریخ انقضا</div>
                    <div class="font-semibold text-{{ $result['analysis']['is_expired'] ? 'red' : 'green' }}-900">{{ $result['passport_info']['validity_date'] }}</div>
                    @if($result['analysis']['days_until_expiry'] !== null)
                    <div class="text-xs text-{{ $result['analysis']['is_expired'] ? 'red' : 'green' }}-700 mt-1">
                        @if($result['analysis']['is_expired'])
                            {{ abs($result['analysis']['days_until_expiry']) }} روز از انقضا گذشته
                        @else
                            {{ $result['analysis']['days_until_expiry'] }} روز تا انقضا
                        @endif
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($hasRequest)
        <!-- Request Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                اطلاعات درخواست گذرنامه
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($result['passport_info']['request_description'])
                <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                    <div class="text-sm text-amber-600 mb-1">توضیحات درخواست</div>
                    <div class="font-semibold text-amber-900">{{ $result['passport_info']['request_description'] }}</div>
                </div>
                @endif

                @if($result['passport_info']['request_date'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">تاریخ درخواست</div>
                    <div class="font-semibold">{{ $result['passport_info']['request_date'] }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Recommendations -->
        @if(isset($result['analysis']['recommendations']) && !empty($result['analysis']['recommendations']))
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                توصیه‌ها و راهنمایی‌ها
            </h3>
            <div class="space-y-3">
                @foreach($result['analysis']['recommendations'] as $recommendation)
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 ml-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-800">{{ $recommendation }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Technical Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                اطلاعات فنی
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-gray-600 mb-1">کد پیگیری</div>
                    <div class="font-mono text-gray-900">{{ $result['request_info']['track_id'] ?? 'نامشخص' }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-gray-600 mb-1">کد پاسخ</div>
                    <div class="font-mono text-gray-900">{{ $result['request_info']['response_code'] ?? 'نامشخص' }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-gray-600 mb-1">زمان استعلام</div>
                    <div class="font-mono text-gray-900">{{ $result['request_info']['processed_at'] ?? 'نامشخص' }}</div>
                </div>
            </div>
        </div>

        @else
        <!-- Error State -->
        <div class="bg-white rounded-xl shadow-sm border border-red-200 p-8 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">خطا در دریافت اطلاعات</h3>
            <p class="text-gray-600 mb-6">{{ $data['message'] ?? 'متأسفانه امکان دریافت اطلاعات گذرنامه وجود ندارد.' }}</p>
            <div class="flex gap-3 justify-center">
                <a href="{{ route('services.show', 'passport-status-inquiry') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    تلاش مجدد
                </a>
                <a href="{{ route('app.page.home') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    صفحه اصلی
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .bg-gradient-to-br { background: white !important; }
    .shadow-sm, .border { box-shadow: none !important; border: 1px solid #ddd !important; }
}
</style>
@endsection