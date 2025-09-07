@extends('front.layouts.app')

@section('title', 'نتیجه استعلام بیمه تامین اجتماعی')

@section('content')
<div class="min-h-screen/2 bg-gradient-to-br from-green-50 to-blue-50 py-8" dir="rtl">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">استعلام بیمه تامین اجتماعی</h1>
            <p class="text-gray-600">گزارش جامع سوابق بیمه و مستمری تامین اجتماعی</p>
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
                <a href="{{ route('services.show', 'social-security-insurance-inquiry') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    استعلام مجدد
                </a>
                <a href="{{ route('app.page.home') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    صفحه اصلی
                </a>
            </div>
        </div>

        <!-- Status Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Insurance Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $isInsured = $result['insurance_info']['is_insured'] ?? false;
                    $statusColor = $isInsured ? 'text-green-600' : 'text-red-600';
                    $statusBg = $isInsured ? 'bg-green-100' : 'bg-red-100';
                @endphp
                <div class="text-center">
                    <div class="w-16 h-16 {{ $statusBg }} rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 {{ $statusColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isInsured ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">وضعیت بیمه</h3>
                    <p class="text-sm {{ $statusColor }} font-medium">
                        {{ $isInsured ? 'بیمه شده' : 'بیمه نشده' }}
                    </p>
                </div>
            </div>

            <!-- Contribution Years -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $years = $result['analysis']['years_of_contribution'] ?? 0;
                    $yearsColor = $years >= 30 ? 'text-green-600' : ($years >= 15 ? 'text-yellow-600' : 'text-red-600');
                    $yearsBg = $years >= 30 ? 'bg-green-100' : ($years >= 15 ? 'bg-yellow-100' : 'bg-red-100');
                @endphp
                <div class="text-center">
                    <div class="w-16 h-16 {{ $yearsBg }} rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 {{ $yearsColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">سابقه بیمه</h3>
                    <p class="text-sm {{ $yearsColor }} font-medium">{{ $years }} سال</p>
                </div>
            </div>

            <!-- Current Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $currentStatus = $result['insurance_info']['current_status'] ?? '';
                    $isActive = $currentStatus === 'active';
                    $currentColor = $isActive ? 'text-green-600' : 'text-gray-600';
                    $currentBg = $isActive ? 'bg-green-100' : 'bg-gray-100';
                @endphp
                <div class="text-center">
                    <div class="w-16 h-16 {{ $currentBg }} rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 {{ $currentColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">وضعیت فعلی</h3>
                    <p class="text-sm {{ $currentColor }} font-medium">
                        {{ $result['insurance_info']['status_description'] }}
                    </p>
                </div>
            </div>

            <!-- Pension Eligibility -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $isPensionEligible = $result['analysis']['is_pension_eligible'] ?? false;
                    $pensionColor = $isPensionEligible ? 'text-green-600' : 'text-yellow-600';
                    $pensionBg = $isPensionEligible ? 'bg-green-100' : 'bg-yellow-100';
                @endphp
                <div class="text-center">
                    <div class="w-16 h-16 {{ $pensionBg }} rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 {{ $pensionColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">واجد شرایط بازنشستگی</h3>
                    <p class="text-sm {{ $pensionColor }} font-medium">
                        {{ $isPensionEligible ? 'بله' : 'خیر' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                اطلاعات شخصی
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">کد ملی</div>
                    <div class="font-semibold text-gray-900">{{ $result['national_code'] ?? 'نامشخص' }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">نام و نام خانوادگی</div>
                    <div class="font-semibold text-gray-900">{{ $result['personal_info']['full_name'] ?? 'نامشخص' }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">نام پدر</div>
                    <div class="font-semibold text-gray-900">{{ $result['personal_info']['father_name'] ?? 'نامشخص' }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">تاریخ تولد</div>
                    <div class="font-semibold text-gray-900">{{ $result['personal_info']['birth_date'] ?? 'نامشخص' }}</div>
                </div>
            </div>
        </div>

        @if($isInsured)
        <!-- Insurance Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                جزئیات بیمه
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($result['insurance_info']['insurance_number'])
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="text-sm text-blue-600 mb-1">شماره بیمه</div>
                    <div class="font-semibold text-blue-900">{{ $result['insurance_info']['insurance_number'] }}</div>
                </div>
                @endif

                @if($result['insurance_info']['membership_date'])
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="text-sm text-green-600 mb-1">تاریخ عضویت</div>
                    <div class="font-semibold text-green-900">{{ $result['insurance_info']['membership_date'] }}</div>
                </div>
                @endif

                @if($result['insurance_info']['last_contribution_date'])
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="text-sm text-purple-600 mb-1">آخرین پرداخت</div>
                    <div class="font-semibold text-purple-900">{{ $result['insurance_info']['last_contribution_date'] }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Contribution History -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                سوابق پرداخت حق بیمه
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                    <div class="text-sm text-amber-600 mb-1">مجموع سال‌ها</div>
                    <div class="font-semibold text-amber-900">{{ $result['contribution_history']['total_years'] ?? 0 }} سال</div>
                </div>

                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="text-sm text-blue-600 mb-1">مجموع ماه‌ها</div>
                    <div class="font-semibold text-blue-900">{{ $result['contribution_history']['total_months'] ?? 0 }} ماه</div>
                </div>

                @if($result['contribution_history']['last_employer'])
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">آخرین کارفرما</div>
                    <div class="font-semibold text-gray-900">{{ $result['contribution_history']['last_employer'] }}</div>
                </div>
                @endif

                @if($result['contribution_history']['employment_status'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">وضعیت اشتغال</div>
                    <div class="font-semibold text-gray-900">{{ $result['contribution_history']['employment_status'] }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Pension Information -->
        @if($isPensionEligible && isset($result['pension_info']['pension_amount']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                اطلاعات مستمری
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($result['pension_info']['pension_amount'])
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="text-sm text-green-600 mb-1">مبلغ مستمری</div>
                    <div class="font-semibold text-green-900">{{ number_format($result['pension_info']['pension_amount']) }} تومان</div>
                </div>
                @endif

                @if($result['pension_info']['retirement_date'])
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="text-sm text-blue-600 mb-1">تاریخ بازنشستگی</div>
                    <div class="font-semibold text-blue-900">{{ $result['pension_info']['retirement_date'] }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif
        @endif

        <!-- Recommendations -->
        @if(isset($result['analysis']['recommendations']) && !empty($result['analysis']['recommendations']))
        <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-xl border border-blue-200 p-6 mb-6">
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
            <p class="text-gray-600 mb-6">{{ $data['message'] ?? 'متأسفانه امکان دریافت اطلاعات بیمه تامین اجتماعی وجود ندارد.' }}</p>
            <div class="flex gap-3 justify-center">
                <a href="{{ route('services.show', 'social-security-insurance-inquiry') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
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