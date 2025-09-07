@extends('front.layouts.app')

@section('title', 'نتیجه استعلام اتباع خارجی')

@section('content')
<div class="min-h-screen/2 bg-sky-50 py-8" dir="rtl">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">استعلام اتباع خارجی</h1>
            <p class="text-gray-600">گزارش جامع وضعیت اقامت و خدمات قابل دریافت</p>
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

        <!-- Alerts -->
        @if(isset($result['alerts']) && count($result['alerts']) > 0)
        <div class="mb-6 space-y-4">
            @foreach($result['alerts'] as $alert)
            <div class="rounded-lg p-4 border {{ $alert['type'] === 'danger' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-yellow-50 border-yellow-200 text-yellow-800' }}">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <div class="font-semibold">{{ $alert['title'] }}</div>
                        <div class="text-sm">{{ $alert['message'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Personal Information -->
        @if(isset($result['personal_info']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                اطلاعات شخصی
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">کد ملی</div>
                    <div class="font-semibold">{{ $result['national_code'] ?? '-' }}</div>
                </div>
                @if($result['personal_info']['full_name'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">نام و نام خانوادگی</div>
                    <div class="font-semibold">{{ $result['personal_info']['full_name'] }}</div>
                </div>
                @endif
                @if($result['personal_info']['father_name'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">نام پدر</div>
                    <div class="font-semibold">{{ $result['personal_info']['father_name'] }}</div>
                </div>
                @endif
                @if($result['personal_info']['nationality_persian'])
                <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                    <div class="text-sm text-sky-600 mb-1">ملیت</div>
                    <div class="font-semibold text-sky-900">{{ $result['personal_info']['nationality_persian'] }}</div>
                </div>
                @endif
                @if($result['personal_info']['birth_date'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">تاریخ تولد</div>
                    <div class="font-semibold">{{ $result['personal_info']['birth_date'] }}</div>
                </div>
                @endif
                @if($result['personal_info']['birth_place'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">محل تولد</div>
                    <div class="font-semibold">{{ $result['personal_info']['birth_place'] }}</div>
                </div>
                @endif
            </div>

            <!-- Passport Information -->
            @if($result['personal_info']['passport_number'])
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">اطلاعات گذرنامه</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                        <div class="text-sm text-purple-600 mb-1">شماره گذرنامه</div>
                        <div class="font-semibold text-purple-900">{{ $result['personal_info']['passport_number'] }}</div>
                    </div>
                    @if($result['personal_info']['passport_issue_date'])
                    <div class="bg-sky-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600 mb-1">تاریخ صدور</div>
                        <div class="font-semibold">{{ $result['personal_info']['passport_issue_date'] }}</div>
                    </div>
                    @endif
                    @if($result['personal_info']['passport_expiry_date'])
                    <div class="bg-{{ $result['personal_info']['is_passport_valid'] ? 'green' : 'red' }}-50 rounded-lg p-4 border border-{{ $result['personal_info']['is_passport_valid'] ? 'green' : 'red' }}-200">
                        <div class="text-sm text-{{ $result['personal_info']['is_passport_valid'] ? 'green' : 'red' }}-600 mb-1">تاریخ انقضا</div>
                        <div class="font-semibold text-{{ $result['personal_info']['is_passport_valid'] ? 'green' : 'red' }}-900">{{ $result['personal_info']['passport_expiry_date'] }}</div>
                        <div class="text-xs text-{{ $result['personal_info']['is_passport_valid'] ? 'green' : 'red' }}-700 mt-1">
                            {{ $result['personal_info']['is_passport_valid'] ? 'معتبر' : 'منقضی' }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Residency Information -->
        @if(isset($result['residency_info']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                وضعیت اقامت
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-200">
                    <div class="text-sm text-emerald-600 mb-1">نوع اقامت</div>
                    <div class="font-semibold text-emerald-900">{{ $result['residency_info']['permit_type_persian'] ?? 'نامشخص' }}</div>
                </div>
                <div class="bg-{{ $result['residency_info']['is_permit_valid'] ? 'green' : 'red' }}-50 rounded-lg p-4 border border-{{ $result['residency_info']['is_permit_valid'] ? 'green' : 'red' }}-200">
                    <div class="text-sm text-{{ $result['residency_info']['is_permit_valid'] ? 'green' : 'red' }}-600 mb-1">وضعیت</div>
                    <div class="font-semibold text-{{ $result['residency_info']['is_permit_valid'] ? 'green' : 'red' }}-900">{{ $result['residency_info']['is_permit_valid'] ? 'معتبر' : 'منقضی' }}</div>
                </div>
                @if($result['residency_info']['expiry_date'])
                <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                    <div class="text-sm text-orange-600 mb-1">تاریخ انقضا</div>
                    <div class="font-semibold text-orange-900">{{ $result['residency_info']['expiry_date'] }}</div>
                    @if($result['residency_info']['days_to_expiry'] !== null)
                    <div class="text-xs text-orange-700 mt-1">{{ $result['residency_info']['days_to_expiry'] }} روز باقی‌مانده</div>
                    @endif
                </div>
                @endif
                @if($result['residency_info']['has_work_permit'])
                <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                    <div class="text-sm text-sky-600 mb-1">مجوز کار</div>
                    <div class="font-semibold text-sky-900">{{ $result['residency_info']['has_work_permit'] ? 'دارد' : 'ندارد' }}</div>
                </div>
                @endif
            </div>

            @if($result['residency_info']['entry_date'] || $result['residency_info']['current_address'])
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($result['residency_info']['entry_date'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">تاریخ ورود</div>
                    <div class="font-semibold">{{ $result['residency_info']['entry_date'] }}</div>
                </div>
                @endif
                @if($result['residency_info']['current_address'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">آدرس فعلی</div>
                    <div class="font-semibold">{{ $result['residency_info']['current_address'] }}</div>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endif

        <!-- Legal Status -->
        @if(isset($result['legal_status']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                وضعیت حقوقی
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="bg-{{ $result['legal_status']['is_legal'] ? 'green' : 'red' }}-50 rounded-lg p-4 border border-{{ $result['legal_status']['is_legal'] ? 'green' : 'red' }}-200">
                        <div class="text-sm text-{{ $result['legal_status']['is_legal'] ? 'green' : 'red' }}-600 mb-1">وضعیت قانونی</div>
                        <div class="font-semibold text-{{ $result['legal_status']['is_legal'] ? 'green' : 'red' }}-900">{{ $result['legal_status']['status_persian'] ?: 'نامشخص' }}</div>
                    </div>

                    @if($result['legal_status']['has_violations'])
                    <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                        <div class="text-sm text-orange-600 mb-1">تخلفات</div>
                        <div class="font-semibold text-orange-900">{{ $result['legal_status']['has_violations'] ? 'دارای تخلف' : 'بدون تخلف' }}</div>
                    </div>
                    @endif
                </div>

                <div class="space-y-4">
                    @if(isset($result['legal_status']['violations']) && count($result['legal_status']['violations']) > 0)
                    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                        <h3 class="font-semibold text-red-900 mb-2">تخلفات ثبت شده</h3>
                        <ul class="space-y-1 text-sm text-red-800">
                            @foreach($result['legal_status']['violations'] as $violation)
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                {{ $violation }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($result['legal_status']['restrictions']) && count($result['legal_status']['restrictions']) > 0)
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                        <h3 class="font-semibold text-yellow-900 mb-2">محدودیت‌ها</h3>
                        <ul class="space-y-1 text-sm text-yellow-800">
                            @foreach($result['legal_status']['restrictions'] as $restriction)
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                {{ $restriction }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>

            @if($result['legal_status']['legal_notes'])
            <div class="mt-4 bg-sky-50 rounded-lg p-4">
                <div class="text-sm text-gray-600 mb-1">یادداشت‌های حقوقی</div>
                <div class="text-sm">{{ $result['legal_status']['legal_notes'] }}</div>
            </div>
            @endif
        </div>
        @endif

        <!-- Services Eligibility -->
        @if(isset($result['services_eligibility']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                خدمات قابل دریافت
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                $services = [
                    'can_open_bank_account' => 'افتتاح حساب بانکی',
                    'can_get_driver_license' => 'دریافت گواهینامه رانندگی',
                    'can_register_business' => 'ثبت کسب‌وکار',
                    'can_buy_property' => 'خرید ملک',
                    'can_work' => 'فعالیت شغلی',
                    'can_study' => 'تحصیل'
                ];
                @endphp

                @foreach($services as $key => $service)
                @php $canUse = $result['services_eligibility'][$key] ?? false; @endphp
                <div class="bg-{{ $canUse ? 'green' : 'red' }}-50 rounded-lg p-4 border border-{{ $canUse ? 'green' : 'red' }}-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-{{ $canUse ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $canUse ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path>
                        </svg>
                        <span class="font-semibold text-{{ $canUse ? 'green' : 'red' }}-900">{{ $service }}</span>
                    </div>
                    <div class="text-xs text-{{ $canUse ? 'green' : 'red' }}-700 mt-1">{{ $canUse ? 'امکان‌پذیر' : 'غیرممکن' }}</div>
                </div>
                @endforeach
            </div>

            @if(isset($result['services_eligibility']['required_documents']) && count($result['services_eligibility']['required_documents']) > 0)
            <div class="mt-6 bg-sky-50 rounded-lg p-4 border border-sky-200">
                <h3 class="font-semibold text-sky-900 mb-2">مدارک مورد نیاز برای تمدید</h3>
                <ul class="space-y-1 text-sm text-sky-800">
                    @foreach($result['services_eligibility']['required_documents'] as $document)
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-sky-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ $document }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        @endif

        <!-- Travel History -->
        @if(isset($result['travel_history']) && count($result['travel_history']['recent_travels']) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                تاریخچه سفر
            </h2>

            <!-- Travel Summary -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                    <div class="text-sm text-indigo-600 mb-1">کل سفرها</div>
                    <div class="text-2xl font-bold text-indigo-900">{{ $result['travel_history']['total_travels'] ?? 0 }}</div>
                </div>
                <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                    <div class="text-sm text-sky-600 mb-1">آخرین سفر</div>
                    <div class="font-semibold text-sky-900">{{ $result['travel_history']['last_travel_date'] ?: 'نامشخص' }}</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="text-sm text-green-600 mb-1">مقصد محبوب</div>
                    <div class="font-semibold text-green-900">{{ $result['travel_history']['most_visited_country'] ?: 'نامشخص' }}</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="text-sm text-purple-600 mb-1">کل ایام</div>
                    <div class="text-2xl font-bold text-purple-900">{{ $result['travel_history']['total_days_abroad'] ?? 0 }}</div>
                </div>
            </div>

            <!-- Recent Travels -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">سفرهای اخیر</h3>
                @foreach(array_slice($result['travel_history']['recent_travels'], 0, 5) as $travel)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-all duration-200 bg-sky-50">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $travel['destination_country'] ?: 'نامشخص' }}</div>
                            <div class="text-sm text-gray-600">{{ $travel['purpose'] ?: 'هدف نامشخص' }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $travel['departure_date'] }} - {{ $travel['return_date'] ?: 'در حال سفر' }}
                            </div>
                        </div>
                        <div class="text-left">
                            @if($travel['duration_days'])
                            <div class="text-sm font-medium text-gray-900">{{ $travel['duration_days'] }} روز</div>
                            @endif
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                {{ $travel['return_date'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $travel['return_date'] ? 'بازگشته' : 'در سفر' }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
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
                    <h3 class="font-semibold text-sky-900 mb-2">اقدامات ضروری</h3>
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
                                وضعیت اقامت شما مناسب است
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
                            همواره اسناد شناسایی خود را به‌روز نگه دارید
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            از قوانین و مقررات ایران اطلاع داشته باشید
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            برای تمدید اقامت زودتر اقدام کنید
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
            <p class="text-gray-600 mb-4">{{ $data['message'] ?? 'متأسفانه امکان دریافت اطلاعات اتباع خارجی وجود ندارد.' }}</p>
            <a href="{{ route('services.show', 'expats-inquiries') }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                تلاش مجدد
            </a>
        </div>
        @endif

        <!-- Back Button -->
        <div class="text-center mt-8">
            <a href="{{ route('services.show', 'expats-inquiries') }}" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
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
    let text = 'نتایج استعلام اتباع خارجی\n';
    text += '=================================\n\n';
    
    if (data.national_code) {
        text += `کد ملی: ${data.national_code}\n`;
    }
    
    if (data.personal_info) {
        text += '\nاطلاعات شخصی:\n';
        text += `نام: ${data.personal_info.full_name || 'نامشخص'}\n`;
        text += `ملیت: ${data.personal_info.nationality_persian || 'نامشخص'}\n`;
        if (data.personal_info.passport_number) {
            text += `گذرنامه: ${data.personal_info.passport_number}\n`;
        }
    }
    
    if (data.residency_info) {
        text += '\nوضعیت اقامت:\n';
        text += `نوع اقامت: ${data.residency_info.permit_type_persian || 'نامشخص'}\n`;
        text += `وضعیت: ${data.residency_info.is_permit_valid ? 'معتبر' : 'منقضی'}\n`;
        if (data.residency_info.expiry_date) {
            text += `تاریخ انقضا: ${data.residency_info.expiry_date}\n`;
        }
    }
    
    if (data.legal_status) {
        text += '\nوضعیت حقوقی:\n';
        text += `وضعیت: ${data.legal_status.status_persian || 'نامشخص'}\n`;
        text += `تخلفات: ${data.legal_status.has_violations ? 'دارد' : 'ندارد'}\n`;
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
    const text = `استعلام اتباع خارجی - کد ملی: ${data.national_code || 'نامشخص'}`;
    
    if (navigator.share) {
        try {
            await navigator.share({
                title: 'نتایج استعلام اتباع خارجی',
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