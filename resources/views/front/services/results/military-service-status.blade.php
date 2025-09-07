@extends('front.layouts.app')

@section('title', 'نتیجه استعلام وضعیت نظام وظیفه')

@section('content')
<div class="min-h-screen/2 bg-sky-50 py-8" dir="rtl">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">استعلام وضعیت نظام وظیفه</h1>
            <p class="text-gray-600">گزارش جامع وضعیت خدمت سربازی و معافیت‌ها</p>
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

        <!-- Personal Information -->
        @if(isset($result['personal_info']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                اطلاعات شخصی
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                @if($result['personal_info']['birth_date'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">تاریخ تولد</div>
                    <div class="font-semibold">{{ $result['personal_info']['birth_date'] }}</div>
                </div>
                @endif
            </div>
            @if($result['personal_info']['birth_place'])
            <div class="mt-4 bg-sky-50 rounded-lg p-4">
                <div class="text-sm text-gray-600 mb-1">محل تولد</div>
                <div class="font-semibold">{{ $result['personal_info']['birth_place'] }}</div>
            </div>
            @endif
        </div>
        @endif

        <!-- Military Status -->
        @if(isset($result['military_status']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                وضعیت نظام وظیفه
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-{{ $result['military_status']['is_completed'] ? 'green' : ($result['military_status']['is_exempt'] ? 'blue' : 'yellow') }}-50 rounded-lg p-4 border border-{{ $result['military_status']['is_completed'] ? 'green' : ($result['military_status']['is_exempt'] ? 'blue' : 'yellow') }}-200">
                    <div class="text-sm text-{{ $result['military_status']['is_completed'] ? 'green' : ($result['military_status']['is_exempt'] ? 'blue' : 'yellow') }}-600 mb-1">وضعیت کلی</div>
                    <div class="text-xl font-bold text-{{ $result['military_status']['is_completed'] ? 'green' : ($result['military_status']['is_exempt'] ? 'blue' : 'yellow') }}-900">{{ $result['military_status']['status_title'] ?: 'نامشخص' }}</div>
                </div>
                @if($result['military_status']['status_code'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">کد وضعیت</div>
                    <div class="font-semibold">{{ $result['military_status']['status_code'] }}</div>
                </div>
                @endif
                @if($result['military_status']['status_date'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">تاریخ تعیین وضعیت</div>
                    <div class="font-semibold">{{ $result['military_status']['status_date'] }}</div>
                </div>
                @endif
            </div>
            @if($result['military_status']['status_description'])
            <div class="mt-4 bg-sky-50 rounded-lg p-4 border border-sky-200">
                <div class="text-sm text-sky-600 mb-1">توضیحات</div>
                <div class="text-sm text-sky-800">{{ $result['military_status']['status_description'] }}</div>
            </div>
            @endif
        </div>
        @endif

        <!-- Service Details (if completed service) -->
        @if(isset($result['service_details']) && ($result['military_status']['is_completed'] ?? false))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                جزئیات خدمت سربازی
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @if($result['service_details']['unit_name'])
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="text-sm text-green-600 mb-1">واحد خدمتی</div>
                    <div class="font-semibold text-green-900">{{ $result['service_details']['unit_name'] }}</div>
                </div>
                @endif
                @if($result['service_details']['service_location'])
                <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                    <div class="text-sm text-sky-600 mb-1">محل خدمت</div>
                    <div class="font-semibold text-sky-900">{{ $result['service_details']['service_location'] }}</div>
                </div>
                @endif
                @if($result['service_details']['rank'])
                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="text-sm text-purple-600 mb-1">درجه نظامی</div>
                    <div class="font-semibold text-purple-900">{{ $result['service_details']['rank'] }}</div>
                </div>
                @endif
                @if($result['service_details']['start_date'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">تاریخ شروع خدمت</div>
                    <div class="font-semibold">{{ $result['service_details']['start_date'] }}</div>
                </div>
                @endif
                @if($result['service_details']['end_date'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">تاریخ پایان خدمت</div>
                    <div class="font-semibold">{{ $result['service_details']['end_date'] }}</div>
                </div>
                @endif
                @if($result['service_details']['service_duration'])
                <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                    <div class="text-sm text-orange-600 mb-1">مدت خدمت</div>
                    <div class="font-semibold text-orange-900">{{ $result['service_details']['service_duration'] }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Exemption Information (if exempt) -->
        @if(isset($result['exemption_info']) && ($result['military_status']['is_exempt'] ?? false))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                اطلاعات معافیت
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($result['exemption_info']['exemption_type'])
                <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                    <div class="text-sm text-sky-600 mb-1">نوع معافیت</div>
                    <div class="font-semibold text-sky-900">{{ $result['exemption_info']['exemption_type'] }}</div>
                </div>
                @endif
                @if($result['exemption_info']['exemption_date'])
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">تاریخ معافیت</div>
                    <div class="font-semibold">{{ $result['exemption_info']['exemption_date'] }}</div>
                </div>
                @endif
            </div>
            @if($result['exemption_info']['exemption_reason'])
            <div class="mt-4 bg-sky-50 rounded-lg p-4 border border-sky-200">
                <div class="text-sm text-sky-600 mb-1">دلیل معافیت</div>
                <div class="text-sm text-sky-800">{{ $result['exemption_info']['exemption_reason'] }}</div>
            </div>
            @endif
            @if($result['exemption_info']['exemption_document'])
            <div class="mt-4 bg-purple-50 rounded-lg p-4 border border-purple-200">
                <div class="text-sm text-purple-600 mb-1">شماره سند معافیت</div>
                <div class="font-semibold text-purple-900">{{ $result['exemption_info']['exemption_document'] }}</div>
            </div>
            @endif
        </div>
        @endif

        <!-- Additional Information -->
        @if(isset($result['additional_info']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                اطلاعات تکمیلی
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-{{ $result['additional_info']['has_military_card'] ? 'green' : 'red' }}-50 rounded-lg p-4 border border-{{ $result['additional_info']['has_military_card'] ? 'green' : 'red' }}-200">
                    <div class="text-sm text-{{ $result['additional_info']['has_military_card'] ? 'green' : 'red' }}-600 mb-1">کارت پایان خدمت</div>
                    <div class="font-semibold text-{{ $result['additional_info']['has_military_card'] ? 'green' : 'red' }}-900">{{ $result['additional_info']['has_military_card'] ? 'دارد' : 'ندارد' }}</div>
                </div>
                @if($result['additional_info']['military_card_number'])
                <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                    <div class="text-sm text-sky-600 mb-1">شماره کارت</div>
                    <div class="font-semibold text-sky-900">{{ $result['additional_info']['military_card_number'] }}</div>
                </div>
                @endif
                @if($result['additional_info']['deferment_count'] > 0)
                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                    <div class="text-sm text-yellow-600 mb-1">تعداد معافیت‌های موقت</div>
                    <div class="text-2xl font-bold text-yellow-900">{{ $result['additional_info']['deferment_count'] }}</div>
                </div>
                @endif
                @if($result['additional_info']['last_deferment_date'])
                <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                    <div class="text-sm text-orange-600 mb-1">آخرین معافیت موقت</div>
                    <div class="font-semibold text-orange-900">{{ $result['additional_info']['last_deferment_date'] }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Next Actions -->
        @if(isset($result['next_actions']) && count($result['next_actions']) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                اقدامات بعدی
            </h2>
            <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                <ul class="space-y-2 text-sm text-orange-800">
                    @foreach($result['next_actions'] as $action)
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-orange-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        {{ $action }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- Required Documents -->
        @if(isset($result['documents_needed']) && count($result['documents_needed']) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                مدارک مورد نیاز
            </h2>
            <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                <ul class="space-y-2 text-sm text-indigo-800">
                    @foreach($result['documents_needed'] as $document)
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-indigo-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ $document }}
                    </li>
                    @endforeach
                </ul>
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
                    <h3 class="font-semibold text-sky-900 mb-2">نکات مهم</h3>
                    <ul class="space-y-2 text-sm text-sky-800">
                        @if($result['military_status']['is_completed'])
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-sky-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            کارت پایان خدمت را در مکان امن نگهداری کنید
                        </li>
                        @endif
                        @if($result['military_status']['is_exempt'])
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-sky-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            مدارک معافیت را محفوظ نگهداری کنید
                        </li>
                        @endif
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-sky-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            در صورت نیاز از نیروهای مسلح مشورت بگیرید
                        </li>
                    </ul>
                </div>
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <h3 class="font-semibold text-green-900 mb-2">اطلاعات مفید</h3>
                    <ul class="space-y-2 text-sm text-green-800">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            برای دریافت کارت ملی کد وضعیت الزامی است
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            برای سفر خارج کشور کد وضعیت بررسی می‌شود
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            اطلاعات در بانک‌ها و ادارات بررسی می‌شود
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
            <p class="text-gray-600 mb-4">{{ $data['message'] ?? 'متأسفانه امکان دریافت وضعیت نظام وظیفه وجود ندارد.' }}</p>
            <a href="{{ route('services.show', 'military-service-status') }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                تلاش مجدد
            </a>
        </div>
        @endif

        <!-- Back Button -->
        <div class="text-center mt-8">
            <a href="{{ route('services.show', 'military-service-status') }}" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
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
    let text = 'نتایج استعلام وضعیت نظام وظیفه\n';
    text += '======================================\n\n';
    
    if (data.national_code) {
        text += `کد ملی: ${data.national_code}\n`;
    }
    
    if (data.personal_info) {
        text += '\nاطلاعات شخصی:\n';
        text += `نام: ${data.personal_info.full_name || 'نامشخص'}\n`;
        text += `نام پدر: ${data.personal_info.father_name || 'نامشخص'}\n`;
        text += `تاریخ تولد: ${data.personal_info.birth_date || 'نامشخص'}\n`;
    }
    
    if (data.military_status) {
        text += '\nوضعیت نظام وظیفه:\n';
        text += `وضعیت: ${data.military_status.status_title || 'نامشخص'}\n`;
        text += `کد وضعیت: ${data.military_status.status_code || 'نامشخص'}\n`;
        text += `توضیحات: ${data.military_status.status_description || 'نامشخص'}\n`;
    }
    
    if (data.service_details && data.military_status.is_completed) {
        text += '\nجزئیات خدمت:\n';
        text += `واحد: ${data.service_details.unit_name || 'نامشخص'}\n`;
        text += `محل خدمت: ${data.service_details.service_location || 'نامشخص'}\n`;
        text += `مدت خدمت: ${data.service_details.service_duration || 'نامشخص'}\n`;
    }
    
    if (data.exemption_info && data.military_status.is_exempt) {
        text += '\nمعافیت:\n';
        text += `نوع معافیت: ${data.exemption_info.exemption_type || 'نامشخص'}\n`;
        text += `دلیل: ${data.exemption_info.exemption_reason || 'نامشخص'}\n`;
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
    const text = `استعلام وضعیت نظام وظیفه - کد ملی: ${data.national_code || 'نامشخص'}`;
    
    if (navigator.share) {
        try {
            await navigator.share({
                title: 'نتایج استعلام وضعیت نظام وظیفه',
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