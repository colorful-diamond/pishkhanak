@extends('front.layouts.app')

@section('title', 'نتیجه استعلام ضمانت وام')

@section('content')
<div class="min-h-screen/2 bg-sky-50 py-8" dir="rtl">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">استعلام ضمانت وام</h1>
            <p class="text-gray-600">گزارش جامع ضمانت‌های ارائه شده و وضعیت ریسک</p>
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
                اطلاعات ضامن
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">کد ملی</div>
                    <div class="font-semibold">{{ $result['national_code'] ?? '-' }}</div>
                </div>
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

        <!-- Summary Dashboard -->
        @if(isset($result['summary']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 00-2 2v6a2 2 0 00-2 2z"></path>
                </svg>
                خلاصه ضمانت‌ها
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                    <div class="text-sm text-sky-600 mb-1">ضمانت‌های فعال</div>
                    <div class="text-2xl font-bold text-sky-900">{{ $result['summary']['total_active_guarantees'] ?? 0 }}</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="text-sm text-green-600 mb-1">کل مبلغ ضمانت</div>
                    <div class="text-xl font-bold text-green-900">{{ $result['summary']['formatted_total_guarantee_amount'] ?? '0 ریال' }}</div>
                </div>
                <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                    <div class="text-sm text-orange-600 mb-1">مبلغ معوق</div>
                    <div class="text-xl font-bold text-orange-900">{{ $result['summary']['formatted_total_overdue_amount'] ?? '0 ریال' }}</div>
                </div>
                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                    <div class="text-sm text-red-600 mb-1">ضمانت‌های پرخطر</div>
                    <div class="text-2xl font-bold text-red-900">{{ $result['summary']['high_risk_guarantees'] ?? 0 }}</div>
                </div>
            </div>

            @if(isset($result['summary']['max_guarantee_capacity']))
            <div class="mt-4 bg-sky-50 rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">ظرفیت ضمانت</span>
                    <span class="text-sm font-medium">{{ round($result['summary']['guarantee_utilization'] ?? 0) }}%</span>
                </div>
                <div class="w-full bg-sky-200 rounded-full h-2">
                    <div class="bg-sky-500 h-2 rounded-full transition-all duration-300" style="width: {{ min(100, $result['summary']['guarantee_utilization'] ?? 0) }}%"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">حداکثر ظرفیت: {{ $result['summary']['formatted_max_capacity'] }}</div>
            </div>
            @endif
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
                    <div class="bg-sky-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">امتیاز ریسک کلی</span>
                            <span class="text-xl font-bold {{ ($result['risk_assessment']['overall_risk_score'] ?? 0) > 70 ? 'text-red-600' : (($result['risk_assessment']['overall_risk_score'] ?? 0) > 40 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ $result['risk_assessment']['overall_risk_score'] ?? 0 }}/100
                            </span>
                        </div>
                        <div class="w-full bg-sky-200 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-300 {{ ($result['risk_assessment']['overall_risk_score'] ?? 0) > 70 ? 'bg-red-500' : (($result['risk_assessment']['overall_risk_score'] ?? 0) > 40 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                style="width: {{ min(100, $result['risk_assessment']['overall_risk_score'] ?? 0) }}%"></div>
                        </div>
                        <div class="text-sm text-gray-600 mt-1">
                            دسته‌بندی: {{ $result['risk_assessment']['risk_category_persian'] ?? '-' }}
                        </div>
                    </div>

                    <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                        <div class="text-sm text-orange-600 mb-1">زیان احتمالی</div>
                        <div class="text-xl font-bold text-orange-900">{{ $result['risk_assessment']['formatted_potential_loss'] ?? '0 ریال' }}</div>
                        <div class="text-xs text-orange-700 mt-1">احتمال نکول: {{ $result['risk_assessment']['probability_of_default'] ?? 0 }}%</div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                        <div class="text-sm text-purple-600 mb-1">میزان مخاطره‌آمیزی</div>
                        <div class="text-xl font-bold text-purple-900">{{ $result['risk_assessment']['formatted_credit_exposure'] ?? '0 ریال' }}</div>
                    </div>

                    <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <div class="text-sm text-sky-600 mb-1">امتیاز تنوع‌بخشی</div>
                        <div class="text-xl font-bold text-sky-900">{{ $result['risk_assessment']['diversification_score'] ?? 0 }}/100</div>
                        <div class="w-full bg-sky-200 rounded-full h-2 mt-2">
                            <div class="bg-sky-600 h-2 rounded-full transition-all duration-300" style="width: {{ min(100, $result['risk_assessment']['diversification_score'] ?? 0) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Active Guarantees -->
        @if(isset($result['active_guarantees']) && count($result['active_guarantees']) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                ضمانت‌های فعال ({{ count($result['active_guarantees']) }} مورد)
            </h2>

            <!-- Filter Options -->
            <div class="mb-4 flex flex-wrap gap-2">
                <button onclick="filterGuarantees('all')" class="guarantee-filter active bg-sky-600 text-white px-4 py-2 rounded-lg text-sm">همه</button>
                <button onclick="filterGuarantees('current')" class="guarantee-filter bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm">جاری</button>
                <button onclick="filterGuarantees('overdue')" class="guarantee-filter bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm">معوق</button>
                <button onclick="filterGuarantees('high-risk')" class="guarantee-filter bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm">پرخطر</button>
            </div>

            <div class="space-y-4" id="guarantee-list">
                @foreach($result['active_guarantees'] as $index => $guarantee)
                <div class="guarantee-item border border-gray-200 rounded-lg p-6 hover:shadow-md transition-all duration-200 
                    {{ $guarantee['is_at_risk'] ? 'border-red-300 bg-red-50' : 'bg-sky-50' }}"
                    data-status="{{ $guarantee['loan_status'] ?? 'current' }}"
                    data-risk="{{ $guarantee['risk_level'] ?? 'low' }}"
                    data-overdue="{{ ($guarantee['overdue_amount'] ?? 0) > 0 ? 'true' : 'false' }}">
                    
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $guarantee['bank_name'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $guarantee['guarantee_type_persian'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">متقاضی: {{ $guarantee['borrower_name'] ?: 'نامشخص' }}</p>
                        </div>
                        <div class="text-left space-y-1">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium
                                {{ $guarantee['loan_status'] === 'CURRENT' ? 'bg-green-100 text-green-800' : 
                                   ($guarantee['loan_status'] === 'OVERDUE' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $guarantee['status_persian'] }}
                            </span>
                            <div>
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                    {{ $guarantee['risk_level'] === 'LOW' ? 'bg-green-100 text-green-700' : 
                                       ($guarantee['risk_level'] === 'HIGH' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    ریسک {{ $guarantee['risk_level_persian'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">مبلغ ضمانت</div>
                            <div class="font-semibold">{{ $guarantee['formatted_guarantee_amount'] }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">مبلغ وام</div>
                            <div class="font-semibold">{{ $guarantee['formatted_loan_amount'] }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">مانده بدهی</div>
                            <div class="font-semibold">{{ $guarantee['formatted_remaining_balance'] }}</div>
                        </div>
                        @if($guarantee['overdue_amount'] > 0)
                        <div>
                            <div class="text-sm text-red-600 mb-1">مبلغ معوق</div>
                            <div class="font-semibold text-red-700">{{ $guarantee['formatted_overdue_amount'] }}</div>
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">تاریخ شروع:</span>
                            <span class="font-medium">{{ $guarantee['start_date'] ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">تاریخ پایان:</span>
                            <span class="font-medium">{{ $guarantee['end_date'] ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">قسط ماهانه:</span>
                            <span class="font-medium">{{ $guarantee['formatted_monthly_payment'] }}</span>
                        </div>
                    </div>

                    @if($guarantee['is_at_risk'])
                    <div class="mt-4 p-3 bg-white rounded-lg border border-gray-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span class="font-medium text-red-700">هشدار ریسک</span>
                        </div>
                        <div class="text-sm text-gray-700">
                            @if($guarantee['overdue_amount'] > 0)
                                <p>• این ضمانت دارای مبلغ معوق است</p>
                            @endif
                            @if($guarantee['risk_level'] === 'HIGH')
                                <p>• ریسک این ضمانت بالا ارزیابی شده است</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Guarantee History -->
        @if(isset($result['guarantee_history']) && count($result['guarantee_history']) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                تاریخچه ضمانت‌ها ({{ count($result['guarantee_history']) }} مورد)
            </h2>
            
            <div class="space-y-4">
                @foreach($result['guarantee_history'] as $guarantee)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-all duration-200 bg-sky-50">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $guarantee['bank_name'] }}</div>
                            <div class="text-sm text-gray-600">متقاضی: {{ $guarantee['borrower_name'] ?: 'نامشخص' }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $guarantee['start_date'] }} تا {{ $guarantee['end_date'] }}
                            </div>
                        </div>
                        <div class="text-left">
                            <div class="font-bold text-gray-900">{{ $guarantee['formatted_guarantee_amount'] }}</div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                {{ $guarantee['was_claimed'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} mt-1">
                                {{ $guarantee['status_persian'] }}
                            </span>
                            @if($guarantee['was_claimed'])
                            <div class="text-xs text-red-600 mt-1">
                                مطالبه شده: {{ $guarantee['formatted_claimed_amount'] }}
                            </div>
                            @endif
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
                    <h3 class="font-semibold text-sky-900 mb-2">مدیریت ریسک</h3>
                    <ul class="space-y-2 text-sm text-sky-800">
                        @if(isset($result['recommendations']) && count($result['recommendations']) > 0)
                            @foreach(array_slice($result['recommendations'], 0, 3) as $recommendation)
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
                                وضعیت ضمانت‌های شما مناسب است
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
                            وضعیت وام‌ها را دوره‌ای بررسی کنید
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            از ضمانت‌های بیش از حد اجتناب کنید
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ریسک‌ها را در چند بانک توزیع کنید
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
            <p class="text-gray-600 mb-4">{{ $data['message'] ?? 'متأسفانه امکان دریافت اطلاعات ضمانت وجود ندارد.' }}</p>
            <a href="{{ route('services.show', 'loan-guarantee-inquiry') }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                تلاش مجدد
            </a>
        </div>
        @endif

        <!-- Back Button -->
        <div class="text-center mt-8">
            <a href="{{ route('services.show', 'loan-guarantee-inquiry') }}" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
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
    let text = 'نتایج استعلام ضمانت وام\n';
    text += '================================\n\n';
    
    if (data.national_code) {
        text += `کد ملی: ${data.national_code}\n`;
    }
    
    if (data.summary) {
        text += '\nخلاصه ضمانت‌ها:\n';
        text += `ضمانت‌های فعال: ${data.summary.total_active_guarantees || 0}\n`;
        text += `کل مبلغ ضمانت: ${data.summary.formatted_total_guarantee_amount || '0 ریال'}\n`;
        text += `مبلغ معوق: ${data.summary.formatted_total_overdue_amount || '0 ریال'}\n`;
        text += `ضمانت‌های پرخطر: ${data.summary.high_risk_guarantees || 0}\n`;
    }
    
    if (data.active_guarantees && data.active_guarantees.length > 0) {
        text += '\nضمانت‌های فعال:\n';
        data.active_guarantees.forEach((guarantee, index) => {
            text += `${index + 1}. ${guarantee.bank_name}\n`;
            text += `   نوع: ${guarantee.guarantee_type_persian}\n`;
            text += `   مبلغ ضمانت: ${guarantee.formatted_guarantee_amount}\n`;
            text += `   وضعیت: ${guarantee.status_persian}\n`;
            text += `   ریسک: ${guarantee.risk_level_persian}\n\n`;
        });
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
    const text = `استعلام ضمانت وام - کد ملی: ${data.national_code || 'نامشخص'}`;
    
    if (navigator.share) {
        try {
            await navigator.share({
                title: 'نتایج استعلام ضمانت وام',
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

// Filter guarantees
function filterGuarantees(type) {
    const items = document.querySelectorAll('.guarantee-item');
    const buttons = document.querySelectorAll('.guarantee-filter');
    
    // Update button states
    buttons.forEach(btn => {
        btn.classList.remove('active', 'bg-sky-600', 'text-white');
        btn.classList.add('bg-sky-200', 'text-gray-700');
    });
    
    event.target.classList.remove('bg-sky-200', 'text-gray-700');
    event.target.classList.add('active', 'bg-sky-600', 'text-white');
    
    // Filter items
    items.forEach(item => {
        const status = item.dataset.status;
        const risk = item.dataset.risk;
        const isOverdue = item.dataset.overdue === 'true';
        
        let show = false;
        
        switch(type) {
            case 'all':
                show = true;
                break;
            case 'current':
                show = status === 'CURRENT';
                break;
            case 'overdue':
                show = isOverdue;
                break;
            case 'high-risk':
                show = risk === 'HIGH';
                break;
        }
        
        item.style.display = show ? 'block' : 'none';
    });
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