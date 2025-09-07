@extends('front.layouts.app')

@section('title', 'نتیجه استعلام اطلاعات خودرو و تخفیفات بیمه')

@section('content')
<div class="min-h-screen/2 bg-gradient-to-br from-blue-50 to-purple-50 py-8" dir="rtl">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16h-1.586a1 1 0 00-.707.293L9 18v-2.707A1 1 0 008.293 15H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">اطلاعات خودرو و تخفیفات بیمه</h1>
            <p class="text-gray-600">گزارش جامع مشخصات خودرو، وضعیت بیمه و تخفیفات</p>
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
                <a href="{{ route('services.show', 'car-information-and-insurance-discounts') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Insurance Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $isInsuranceActive = $result['insurance_info']['is_active'] ?? false;
                    $statusColor = $isInsuranceActive ? 'text-green-600' : 'text-red-600';
                    $statusBg = $isInsuranceActive ? 'bg-green-100' : 'bg-red-100';
                @endphp
                <div class="text-center">
                    <div class="w-16 h-16 {{ $statusBg }} rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 {{ $statusColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isInsuranceActive ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">وضعیت بیمه</h3>
                    <p class="text-sm {{ $statusColor }} font-medium">
                        {{ $isInsuranceActive ? 'فعال' : 'منقضی' }}
                    </p>
                </div>
            </div>

            <!-- Insurance Score -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $score = $result['analysis']['insurance_score'] ?? 50;
                    $scoreColor = $score >= 80 ? 'text-green-600' : ($score >= 60 ? 'text-yellow-600' : 'text-red-600');
                    $scoreBg = $score >= 80 ? 'bg-green-100' : ($score >= 60 ? 'bg-yellow-100' : 'bg-red-100');
                @endphp
                <div class="text-center">
                    <div class="w-16 h-16 {{ $scoreBg }} rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 {{ $scoreColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">امتیاز بیمه</h3>
                    <p class="text-sm {{ $scoreColor }} font-medium">{{ $score }} از 100</p>
                </div>
            </div>

            <!-- Total Discount -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $totalDiscount = $result['discounts']['total_discount']['percentage'] ?? 0;
                    $discountColor = $totalDiscount >= 30 ? 'text-green-600' : ($totalDiscount >= 15 ? 'text-yellow-600' : 'text-gray-600');
                    $discountBg = $totalDiscount >= 30 ? 'bg-green-100' : ($totalDiscount >= 15 ? 'bg-yellow-100' : 'bg-gray-100');
                @endphp
                <div class="text-center">
                    <div class="w-16 h-16 {{ $discountBg }} rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 {{ $discountColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">مجموع تخفیف</h3>
                    <p class="text-sm {{ $discountColor }} font-medium">{{ $totalDiscount }}%</p>
                </div>
            </div>

            <!-- Claims History -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $claimFreeYears = $result['claims_history']['claim_free_years'] ?? 0;
                    $claimsColor = $claimFreeYears >= 5 ? 'text-green-600' : ($claimFreeYears >= 2 ? 'text-yellow-600' : 'text-red-600');
                    $claimsBg = $claimFreeYears >= 5 ? 'bg-green-100' : ($claimFreeYears >= 2 ? 'bg-yellow-100' : 'bg-red-100');
                @endphp
                <div class="text-center">
                    <div class="w-16 h-16 {{ $claimsBg }} rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 {{ $claimsColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">سال‌های بدون خسارت</h3>
                    <p class="text-sm {{ $claimsColor }} font-medium">{{ $claimFreeYears }} سال</p>
                </div>
            </div>
        </div>

        <!-- Vehicle Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16h-1.586a1 1 0 00-.707.293L9 18v-2.707A1 1 0 008.293 15H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2z"></path>
                </svg>
                مشخصات خودرو
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="text-sm text-blue-600 mb-1">شماره پلاک</div>
                    <div class="font-semibold text-blue-900">{{ $result['formatted_plate'] ?? $result['plate_number'] }}</div>
                </div>

                @if($result['vehicle_info']['vehicle_type'])
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="text-sm text-purple-600 mb-1">نوع خودرو</div>
                    <div class="font-semibold text-purple-900">{{ $result['vehicle_info']['vehicle_type'] }}</div>
                </div>
                @endif

                @if($result['vehicle_info']['vehicle_model'])
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="text-sm text-green-600 mb-1">مدل خودرو</div>
                    <div class="font-semibold text-green-900">{{ $result['vehicle_info']['vehicle_model'] }}</div>
                </div>
                @endif

                @if($result['vehicle_info']['production_year'])
                <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                    <div class="text-sm text-amber-600 mb-1">سال تولید</div>
                    <div class="font-semibold text-amber-900">{{ $result['vehicle_info']['production_year'] }}</div>
                </div>
                @endif

                @if($result['vehicle_info']['fuel_type'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">نوع سوخت</div>
                    <div class="font-semibold text-gray-900">{{ $result['vehicle_info']['fuel_type'] }}</div>
                </div>
                @endif

                @if($result['vehicle_info']['color'])
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">رنگ</div>
                    <div class="font-semibold text-gray-900">{{ $result['vehicle_info']['color'] }}</div>
                </div>
                @endif

                @if($result['vehicle_info']['engine_capacity'])
                <div class="bg-indigo-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">حجم موتور</div>
                    <div class="font-semibold text-gray-900">{{ $result['vehicle_info']['engine_capacity'] }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Insurance Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                اطلاعات بیمه
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($result['insurance_info']['insurance_company'])
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="text-sm text-blue-600 mb-1">شرکت بیمه</div>
                    <div class="font-semibold text-blue-900">{{ $result['insurance_info']['insurance_company'] }}</div>
                </div>
                @endif

                @if($result['insurance_info']['policy_number'])
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="text-sm text-purple-600 mb-1">شماره بیمه‌نامه</div>
                    <div class="font-semibold text-purple-900">{{ $result['insurance_info']['policy_number'] }}</div>
                </div>
                @endif

                @if($result['insurance_info']['coverage_type'])
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="text-sm text-green-600 mb-1">نوع پوشش</div>
                    <div class="font-semibold text-green-900">{{ $result['insurance_info']['coverage_type'] }}</div>
                </div>
                @endif

                @if($result['insurance_info']['start_date'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">تاریخ شروع</div>
                    <div class="font-semibold text-gray-900">{{ $result['insurance_info']['start_date'] }}</div>
                </div>
                @endif

                @if($result['insurance_info']['end_date'])
                <div class="bg-{{ $isInsuranceActive ? 'green' : 'red' }}-50 rounded-lg p-4 border border-{{ $isInsuranceActive ? 'green' : 'red' }}-200">
                    <div class="text-sm text-{{ $isInsuranceActive ? 'green' : 'red' }}-600 mb-1">تاریخ انقضا</div>
                    <div class="font-semibold text-{{ $isInsuranceActive ? 'green' : 'red' }}-900">{{ $result['insurance_info']['end_date'] }}</div>
                </div>
                @endif

                @if($result['insurance_info']['premium_amount'])
                <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                    <div class="text-sm text-amber-600 mb-1">مبلغ حق بیمه</div>
                    <div class="font-semibold text-amber-900">{{ number_format($result['insurance_info']['premium_amount']) }} تومان</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Discounts Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                تخفیفات بیمه
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- No Claim Discount -->
                @if($result['discounts']['no_claim_discount']['percentage'] > 0)
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
                    <div class="text-sm text-green-600 mb-1">تخفیف عدم خسارت</div>
                    <div class="font-semibold text-green-900">{{ $result['discounts']['no_claim_discount']['percentage'] }}%</div>
                    <div class="text-xs text-green-700 mt-1">{{ $result['discounts']['no_claim_discount']['description'] }}</div>
                </div>
                @endif

                <!-- Loyalty Discount -->
                @if($result['discounts']['loyalty_discount']['percentage'] > 0)
                <div class="bg-gradient-to-br from-blue-50 to-sky-50 rounded-lg p-4 border border-blue-200">
                    <div class="text-sm text-blue-600 mb-1">تخفیف وفاداری</div>
                    <div class="font-semibold text-blue-900">{{ $result['discounts']['loyalty_discount']['percentage'] }}%</div>
                    <div class="text-xs text-blue-700 mt-1">{{ $result['discounts']['loyalty_discount']['years_with_company'] }} سال با شرکت</div>
                </div>
                @endif

                <!-- Safety Features Discount -->
                @if($result['discounts']['safety_features_discount']['percentage'] > 0)
                <div class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-lg p-4 border border-purple-200">
                    <div class="text-sm text-purple-600 mb-1">تخفیف تجهیزات ایمنی</div>
                    <div class="font-semibold text-purple-900">{{ $result['discounts']['safety_features_discount']['percentage'] }}%</div>
                    <div class="text-xs text-purple-700 mt-1">{{ count($result['discounts']['safety_features_discount']['features']) }} تجهیز</div>
                </div>
                @endif

                <!-- Young Driver Discount -->
                @if($result['discounts']['young_driver_discount']['eligible'])
                <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-lg p-4 border border-amber-200">
                    <div class="text-sm text-amber-600 mb-1">تخفیف راننده جوان</div>
                    <div class="font-semibold text-amber-900">{{ $result['discounts']['young_driver_discount']['percentage'] }}%</div>
                    <div class="text-xs text-amber-700 mt-1">واجد شرایط</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Premium Calculation -->
        @if(isset($result['premium_calculation']) && $result['premium_calculation']['base_premium'])
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                محاسبه حق بیمه
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">حق بیمه پایه</div>
                    <div class="font-semibold text-gray-900">{{ number_format($result['premium_calculation']['base_premium']) }} تومان</div>
                </div>

                @if($result['premium_calculation']['total_discounts'])
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="text-sm text-green-600 mb-1">مجموع تخفیفات</div>
                    <div class="font-semibold text-green-900">{{ number_format($result['premium_calculation']['total_discounts']) }} تومان</div>
                </div>
                @endif

                @if($result['premium_calculation']['total_surcharges'])
                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                    <div class="text-sm text-red-600 mb-1">اضافات</div>
                    <div class="font-semibold text-red-900">{{ number_format($result['premium_calculation']['total_surcharges']) }} تومان</div>
                </div>
                @endif

                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="text-sm text-blue-600 mb-1">حق بیمه نهایی</div>
                    <div class="font-semibold text-blue-900">{{ number_format($result['premium_calculation']['final_premium']) }} تومان</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Analysis Summary -->
        @if(isset($result['analysis']['summary']))
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                خلاصه تحلیل
            </h3>
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900 mb-2">{{ $result['analysis']['summary'] }}</p>
                @if(isset($result['analysis']['discount_potential']))
                <p class="text-gray-600">
                    تخفیف فعلی: {{ $result['analysis']['discount_potential']['current_discount'] }}% - 
                    پتانسیل اضافی: {{ $result['analysis']['discount_potential']['potential_additional'] }}%
                </p>
                @endif
            </div>
        </div>
        @endif

        <!-- Recommendations -->
        @if(isset($result['analysis']['recommendations']) && !empty($result['analysis']['recommendations']))
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                توصیه‌ها و راهنمایی‌ها
            </h3>
            <div class="space-y-3">
                @foreach($result['analysis']['recommendations'] as $recommendation)
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 ml-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <p class="text-gray-600 mb-6">{{ $data['message'] ?? 'متأسفانه امکان دریافت اطلاعات خودرو و تخفیفات بیمه وجود ندارد.' }}</p>
            <div class="flex gap-3 justify-center">
                <a href="{{ route('services.show', 'car-information-and-insurance-discounts') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
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