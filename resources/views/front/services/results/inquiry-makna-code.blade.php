@extends('front.layouts.app')

@section('title', 'نتیجه استعلام کد مکنا')

@section('content')
<div class="min-h-screen/2 bg-sky-50 py-8" dir="rtl">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">استعلام کد مکنا</h1>
            <p class="text-gray-600">گزارش جامع اعتبار بانکی و امتیاز اعتباری</p>
        </div>

        @if(isset($data['status']) && $data['status'] === 'success')
        @php $result = $data['data']; @endphp

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-wrap gap-4 justify-center">
                <button onclick="copyResults()" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    کپی اطلاعات
                </button>
                <button onclick="shareResults()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                    </svg>
                    اشتراک‌گذاری
                </button>
                <button onclick="printResults()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    چاپ گزارش
                </button>
                <button onclick="exportToExcel()" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    دانلود Excel
                </button>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                اطلاعات متقاضی
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">کد ملی</div>
                    <div class="font-semibold">{{ $result['national_code'] ?? '-' }}</div>
                </div>
            </div>
        </div>

        <!-- MAKNA Information -->
        @if(isset($result['makna_info']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                اطلاعات کد مکنا
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                    <div class="text-sm text-indigo-600 mb-1">کد مکنا</div>
                    <div class="text-xl font-bold text-indigo-900">{{ $result['makna_info']['makna_code'] ?: 'نامشخص' }}</div>
                </div>
                <div class="bg-{{ $result['makna_info']['is_active'] ? 'green' : 'red' }}-50 rounded-lg p-4 border border-{{ $result['makna_info']['is_active'] ? 'green' : 'red' }}-200">
                    <div class="text-sm text-{{ $result['makna_info']['is_active'] ? 'green' : 'red' }}-600 mb-1">وضعیت</div>
                    <div class="text-xl font-bold text-{{ $result['makna_info']['is_active'] ? 'green' : 'red' }}-900">{{ $result['makna_info']['status_persian'] ?: 'نامشخص' }}</div>
                </div>
                <div class="bg-sky-50 rounded-lg p-4 border border-gray-200">
                    <div class="text-sm text-gray-600 mb-1">مرجع صادرکننده</div>
                    <div class="text-sm font-bold text-gray-900">{{ $result['makna_info']['issuing_authority'] ?: 'مرکز تشخیص و گزارش تقلبات بانکی' }}</div>
                </div>
                @if($result['makna_info']['issue_date'])
                <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                    <div class="text-sm text-sky-600 mb-1">تاریخ صدور</div>
                    <div class="font-semibold text-sky-900">{{ $result['makna_info']['issue_date'] }}</div>
                </div>
                @endif
                @if($result['makna_info']['last_update'])
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="text-sm text-purple-600 mb-1">آخرین بروزرسانی</div>
                    <div class="font-semibold text-purple-900">{{ $result['makna_info']['last_update'] }}</div>
                </div>
                @endif
                @if($result['makna_info']['validity_period'])
                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                    <div class="text-sm text-yellow-600 mb-1">مدت اعتبار</div>
                    <div class="font-semibold text-yellow-900">{{ $result['makna_info']['validity_period'] }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Credit Status -->
        @if(isset($result['credit_status']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 00-2 2v6a2 2 0 00-2 2z"></path>
                </svg>
                وضعیت اعتباری
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="bg-{{ $result['credit_status']['score_category']['color'] ?? 'gray' }}-50 rounded-lg p-4 border border-{{ $result['credit_status']['score_category']['color'] ?? 'gray' }}-200">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-{{ $result['credit_status']['score_category']['color'] ?? 'gray' }}-600">امتیاز اعتباری</span>
                            <span class="text-2xl font-bold text-{{ $result['credit_status']['score_category']['color'] ?? 'gray' }}-900">
                                {{ $result['credit_status']['score'] ?? 0 }}/{{ $result['credit_status']['max_score'] ?? 850 }}
                            </span>
                        </div>
                        <div class="w-full bg-{{ $result['credit_status']['score_category']['color'] ?? 'gray' }}-200 rounded-full h-3">
                            <div class="bg-{{ $result['credit_status']['score_category']['color'] ?? 'gray' }}-600 h-3 rounded-full transition-all duration-300" 
                                style="width: {{ min(100, (($result['credit_status']['score'] ?? 0) / ($result['credit_status']['max_score'] ?? 850)) * 100) }}%"></div>
                        </div>
                        <div class="text-sm text-{{ $result['credit_status']['score_category']['color'] ?? 'gray' }}-700 mt-1">
                            رتبه: {{ $result['credit_status']['score_category']['text'] ?? 'نامشخص' }}
                        </div>
                    </div>

                    <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <div class="text-sm text-sky-600 mb-1">رتبه‌بندی اعتباری</div>
                        <div class="text-xl font-bold text-sky-900">{{ $result['credit_status']['credit_rating_persian'] ?: 'نامشخص' }}</div>
                        @if($result['credit_status']['last_assessment_date'])
                        <div class="text-xs text-sky-700 mt-1">آخرین ارزیابی: {{ $result['credit_status']['last_assessment_date'] }}</div>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    @if(isset($result['credit_status']['positive_factors']) && count($result['credit_status']['positive_factors']) > 0)
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <h3 class="font-semibold text-green-900 mb-2">عوامل مثبت</h3>
                        <ul class="space-y-1 text-sm text-green-800">
                            @foreach($result['credit_status']['positive_factors'] as $factor)
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $factor }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($result['credit_status']['negative_factors']) && count($result['credit_status']['negative_factors']) > 0)
                    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                        <h3 class="font-semibold text-red-900 mb-2">عوامل منفی</h3>
                        <ul class="space-y-1 text-sm text-red-800">
                            @foreach($result['credit_status']['negative_factors'] as $factor)
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                {{ $factor }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Financial Summary -->
        @if(isset($result['financial_summary']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                خلاصه مالی
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                    <div class="text-sm text-red-600 mb-1">کل بدهی</div>
                    <div class="text-xl font-bold text-red-900">{{ $result['financial_summary']['formatted_total_debt'] ?? '0 ریال' }}</div>
                </div>
                <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                    <div class="text-sm text-sky-600 mb-1">وام‌های فعال</div>
                    <div class="text-2xl font-bold text-sky-900">{{ $result['financial_summary']['active_loans'] ?? 0 }}</div>
                </div>
                <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                    <div class="text-sm text-orange-600 mb-1">پرداخت‌های معوق</div>
                    <div class="text-xl font-bold text-orange-900">{{ $result['financial_summary']['formatted_overdue_payments'] ?? '0 ریال' }}</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="text-sm text-purple-600 mb-1">چک‌های برگشتی</div>
                    <div class="text-2xl font-bold text-purple-900">{{ $result['financial_summary']['bounced_checks'] ?? 0 }}</div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">میزان استفاده از اعتبار</div>
                    <div class="text-lg font-bold text-gray-900">{{ $result['financial_summary']['credit_utilization'] ?? 0 }}%</div>
                    <div class="w-full bg-sky-200 rounded-full h-2 mt-2">
                        <div class="bg-gray-600 h-2 rounded-full transition-all duration-300" style="width: {{ min(100, $result['financial_summary']['credit_utilization'] ?? 0) }}%"></div>
                    </div>
                </div>
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">امتیاز تاریخچه پرداخت</div>
                    <div class="text-lg font-bold text-gray-900">{{ $result['financial_summary']['payment_history_score'] ?? 0 }}/100</div>
                </div>
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">سابقه بانکی</div>
                    <div class="text-lg font-bold text-gray-900">{{ $result['financial_summary']['banking_relationship_years'] ?? 0 }} سال</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Risk Assessment -->
        @if(isset($result['risk_assessment']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                ارزیابی ریسک
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="bg-{{ 
                        $result['risk_assessment']['risk_level'] === 'LOW' ? 'green' : 
                        ($result['risk_assessment']['risk_level'] === 'HIGH' ? 'red' : 'yellow') 
                    }}-50 rounded-lg p-4 border border-{{ 
                        $result['risk_assessment']['risk_level'] === 'LOW' ? 'green' : 
                        ($result['risk_assessment']['risk_level'] === 'HIGH' ? 'red' : 'yellow') 
                    }}-200">
                        <div class="text-sm text-{{ 
                            $result['risk_assessment']['risk_level'] === 'LOW' ? 'green' : 
                            ($result['risk_assessment']['risk_level'] === 'HIGH' ? 'red' : 'yellow') 
                        }}-600 mb-1">سطح ریسک</div>
                        <div class="text-xl font-bold text-{{ 
                            $result['risk_assessment']['risk_level'] === 'LOW' ? 'green' : 
                            ($result['risk_assessment']['risk_level'] === 'HIGH' ? 'red' : 'yellow') 
                        }}-900">{{ $result['risk_assessment']['risk_level_persian'] ?: 'نامشخص' }}</div>
                        @if($result['risk_assessment']['probability_of_default'])
                        <div class="text-xs text-{{ 
                            $result['risk_assessment']['risk_level'] === 'LOW' ? 'green' : 
                            ($result['risk_assessment']['risk_level'] === 'HIGH' ? 'red' : 'yellow') 
                        }}-700 mt-1">احتمال نکول: {{ $result['risk_assessment']['probability_of_default'] }}%</div>
                        @endif
                    </div>

                    @if($result['risk_assessment']['recommended_credit_limit'])
                    <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <div class="text-sm text-sky-600 mb-1">حد اعتبار پیشنهادی</div>
                        <div class="text-xl font-bold text-sky-900">{{ $result['risk_assessment']['formatted_recommended_credit_limit'] }}</div>
                    </div>
                    @endif
                </div>

                <div class="space-y-4">
                    @if(isset($result['risk_assessment']['risk_factors']) && count($result['risk_assessment']['risk_factors']) > 0)
                    <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                        <h3 class="font-semibold text-orange-900 mb-2">عوامل ریسک</h3>
                        <ul class="space-y-1 text-sm text-orange-800">
                            @foreach($result['risk_assessment']['risk_factors'] as $factor)
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-orange-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                {{ $factor }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($result['risk_assessment']['mitigation_suggestions']) && count($result['risk_assessment']['mitigation_suggestions']) > 0)
                    <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <h3 class="font-semibold text-sky-900 mb-2">پیشنهادات کاهش ریسک</h3>
                        <ul class="space-y-1 text-sm text-sky-800">
                            @foreach($result['risk_assessment']['mitigation_suggestions'] as $suggestion)
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-sky-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $suggestion }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Usage Information -->
        @if(isset($result['usage_info']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                راهنمای استفاده
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="bg-sky-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">امکانات قابل استفاده</h3>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 {{ $result['usage_info']['can_use_for_banking'] ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $result['usage_info']['can_use_for_banking'] ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path>
                                </svg>
                                <span class="text-sm {{ $result['usage_info']['can_use_for_banking'] ? 'text-green-800' : 'text-red-800' }}">خدمات بانکی عمومی</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 {{ $result['usage_info']['can_use_for_credit'] ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $result['usage_info']['can_use_for_credit'] ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path>
                                </svg>
                                <span class="text-sm {{ $result['usage_info']['can_use_for_credit'] ? 'text-green-800' : 'text-red-800' }}">دریافت اعتبار</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 {{ $result['usage_info']['can_use_for_loans'] ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $result['usage_info']['can_use_for_loans'] ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path>
                                </svg>
                                <span class="text-sm {{ $result['usage_info']['can_use_for_loans'] ? 'text-green-800' : 'text-red-800' }}">درخواست وام</span>
                            </div>
                        </div>
                    </div>

                    @if($result['usage_info']['renewal_required'])
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span class="font-medium text-yellow-800">نیاز به تجدید</span>
                        </div>
                        <p class="text-sm text-yellow-700">کد مکنا شما نیاز به تجدید دارد</p>
                    </div>
                    @endif
                </div>

                <div class="space-y-4">
                    <div class="bg-sky-50 rounded-lg p-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">مدت اعتبار:</span>
                                <span class="font-medium">{{ $result['usage_info']['validity_period'] ?: '6 ماه' }}</span>
                            </div>
                            @if($result['usage_info']['next_review_date'])
                            <div class="flex justify-between">
                                <span class="text-gray-600">تاریخ بازبینی بعدی:</span>
                                <span class="font-medium">{{ $result['usage_info']['next_review_date'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if(isset($result['usage_info']['usage_restrictions']) && count($result['usage_info']['usage_restrictions']) > 0)
                    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                        <h3 class="font-semibold text-red-900 mb-2">محدودیت‌ها</h3>
                        <ul class="space-y-1 text-sm text-red-800">
                            @foreach($result['usage_info']['usage_restrictions'] as $restriction)
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                {{ $restriction }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Recommendations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                توصیه‌ها و راهنمایی
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                    <h3 class="font-semibold text-sky-900 mb-2">توصیه‌های بهبود</h3>
                    <ul class="space-y-2 text-sm text-sky-800">
                        @if(isset($result['recommendations']) && count($result['recommendations']) > 0)
                            @foreach(array_slice($result['recommendations'], 0, 4) as $recommendation)
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-sky-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $recommendation }}
                            </li>
                            @endforeach
                        @else
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-sky-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                وضعیت مالی شما مناسب است
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <h3 class="font-semibold text-green-900 mb-2">نکات مفید</h3>
                    <ul class="space-y-2 text-sm text-green-800">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            کد مکنا را دوره‌ای بروزرسانی کنید
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            از انتشار اطلاعات شخصی خودداری کنید
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            رکورد مثبت پرداخت حفظ کنید
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @else
        <!-- Error State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">خطا در دریافت اطلاعات</h3>
            <p class="text-gray-600 mb-4">{{ $data['message'] ?? 'متأسفانه امکان دریافت کد مکنا وجود ندارد.' }}</p>
            <a href="{{ route('services.show', 'inquiry-makna-code') }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                تلاش مجدد
            </a>
        </div>
        @endif

        <!-- Back Button -->
        <div class="text-center mt-8">
            <a href="{{ route('services.show', 'inquiry-makna-code') }}" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                بازگشت به صفحه استعلام
            </a>
        </div>
    </div>
</div>

<script>
// Copy functionality
function copyResults() {
    const data = @json($data['data'] ?? []);
    let text = 'نتایج استعلام کد مکنا\n';
    text += '============================\n\n';
    
    if (data.national_code) {
        text += `کد ملی: ${data.national_code}\n`;
    }
    
    if (data.makna_info) {
        text += '\nاطلاعات کد مکنا:\n';
        text += `کد مکنا: ${data.makna_info.makna_code || 'نامشخص'}\n`;
        text += `وضعیت: ${data.makna_info.status_persian || 'نامشخص'}\n`;
        text += `تاریخ صدور: ${data.makna_info.issue_date || '-'}\n`;
    }
    
    if (data.credit_status) {
        text += '\nوضعیت اعتباری:\n';
        text += `امتیاز: ${data.credit_status.score || 0}/${data.credit_status.max_score || 850}\n`;
        text += `رتبه: ${data.credit_status.score_category ? data.credit_status.score_category.text : 'نامشخص'}\n`;
        text += `رتبه‌بندی: ${data.credit_status.credit_rating_persian || 'نامشخص'}\n`;
    }
    
    if (data.financial_summary) {
        text += '\nخلاصه مالی:\n';
        text += `کل بدهی: ${data.financial_summary.formatted_total_debt || '0 ریال'}\n`;
        text += `وام‌های فعال: ${data.financial_summary.active_loans || 0}\n`;
        text += `پرداخت‌های معوق: ${data.financial_summary.formatted_overdue_payments || '0 ریال'}\n`;
    }
    
    navigator.clipboard.writeText(text).then(() => {
        showToast('اطلاعات با موفقیت کپی شد', 'success');
    }).catch(() => {
        showToast('خطا در کپی اطلاعات', 'error');
    });
}

// Share functionality
async function shareResults() {
    const data = @json($data['data'] ?? []);
    const text = `استعلام کد مکنا - کد ملی: ${data.national_code || 'نامشخص'}`;
    
    if (navigator.share) {
        try {
            await navigator.share({
                title: 'نتایج استعلام کد مکنا',
                text: text,
                url: window.location.href
            });
        } catch (err) {
            console.log('Error sharing:', err);
        }
    } else {
        // Fallback
        const url = window.location.href;
        navigator.clipboard.writeText(`${text}\n${url}`).then(() => {
            showToast('لینک کپی شد', 'success');
        });
    }
}

// Print functionality
function printResults() {
    window.print();
}

// Export to Excel (placeholder)
function exportToExcel() {
    showToast('قابلیت دانلود Excel به زودی اضافه خواهد شد', 'info');
}

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-sky-500 text-white',
        warning: 'bg-yellow-500 text-black'
    };
    
    toast.className += ` ${colors[type] || colors.info}`;
    toast.innerHTML = `
        <div class="flex items-center gap-2">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }, 5000);
}
</script>

<style>
@media print {
    body { print-color-adjust: exact; }
    .no-print { display: none !important; }
    .bg-sky-50 { background-color: white !important; }
    .shadow-sm { box-shadow: none !important; }
    .border { border: 1px solid #d1d5db !important; }
}
</style>
@endsection 