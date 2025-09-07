@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">گزارش جامع خودرو</h1>
                <p class="text-gray-600">پلاک {{ $data['data']['formatted_plate'] ?? $data['data']['plate_number'] ?? 'نامشخص' }}</p>
            </div>
            <div class="text-right">
                @php
                    $isActive = $data['data']['status_info']['is_active'] ?? false;
                    $isBlocked = $data['data']['status_info']['is_blocked'] ?? false;
                    $statusClass = $isBlocked ? 'bg-red-100 text-red-800' : ($isActive ? 'bg-green-100 text-green-800' : 'bg-sky-100 text-gray-800');
                    $statusText = $isBlocked ? 'مسدود' : ($isActive ? 'فعال' : 'نامشخص');
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                        @if($isBlocked)
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        @elseif($isActive)
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        @else
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        @endif
                    </svg>
                    {{ $statusText }}
                </span>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    @if(!empty($data['data']['alerts']))
        <div class="mb-6 space-y-3">
            @foreach($data['data']['alerts'] as $alert)
                <div class="p-4 rounded-lg border 
                    @if($alert['type'] === 'danger') bg-red-50 border-red-200 text-red-800
                    @elseif($alert['type'] === 'warning') bg-yellow-50 border-yellow-200 text-yellow-800
                    @elseif($alert['type'] === 'info') bg-sky-50 border-sky-200 text-sky-800
                    @else bg-sky-50 border-gray-200 text-gray-800 @endif">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 ml-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            @if($alert['type'] === 'danger')
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            @elseif($alert['type'] === 'warning')
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            @else
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            @endif
                        </svg>
                        <div>
                            <div class="font-semibold">{{ $alert['title'] ?? 'هشدار' }}</div>
                            <div class="text-sm mt-1">{{ $alert['message'] ?? '' }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Status Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Vehicle Status -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    @php
                        $statusColor = $isBlocked ? 'text-red-600' : ($isActive ? 'text-green-600' : 'text-gray-600');
                        $statusBg = $isBlocked ? 'bg-red-100' : ($isActive ? 'bg-green-100' : 'bg-sky-100');
                    @endphp
                    <div class="w-16 h-16 {{ $statusBg }} rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 {{ $statusColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="text-sm text-gray-600 mb-1">وضعیت خودرو</div>
                    <div class="font-semibold {{ $statusColor }}">{{ $data['data']['status_info']['status_description'] ?? $statusText }}</div>
                </div>

                <!-- Insurance Status -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    @php
                        $isInsuranceValid = $data['data']['insurance_info']['is_insurance_valid'] ?? false;
                        $insuranceColor = $isInsuranceValid ? 'text-green-600' : 'text-red-600';
                        $insuranceBg = $isInsuranceValid ? 'bg-green-100' : 'bg-red-100';
                        $insuranceText = $isInsuranceValid ? 'معتبر' : 'منقضی';
                    @endphp
                    <div class="w-16 h-16 {{ $insuranceBg }} rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 {{ $insuranceColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="text-sm text-gray-600 mb-1">بیمه شخص ثالث</div>
                    <div class="font-semibold {{ $insuranceColor }}">{{ $insuranceText }}</div>
                    @if(!empty($data['data']['insurance_info']['days_to_expiry']))
                        <div class="text-xs text-gray-500 mt-1">{{ $data['data']['insurance_info']['days_to_expiry'] }} روز مانده</div>
                    @endif
                </div>

                <!-- Technical Inspection -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    @php
                        $isInspectionValid = $data['data']['inspection_info']['is_inspection_valid'] ?? false;
                        $inspectionColor = $isInspectionValid ? 'text-green-600' : 'text-red-600';
                        $inspectionBg = $isInspectionValid ? 'bg-green-100' : 'bg-red-100';
                        $inspectionText = $isInspectionValid ? 'معتبر' : 'منقضی';
                    @endphp
                    <div class="w-16 h-16 {{ $inspectionBg }} rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 {{ $inspectionColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="text-sm text-gray-600 mb-1">معاینه فنی</div>
                    <div class="font-semibold {{ $inspectionColor }}">{{ $inspectionText }}</div>
                    @if(!empty($data['data']['inspection_info']['days_to_inspection']))
                        <div class="text-xs text-gray-500 mt-1">{{ $data['data']['inspection_info']['days_to_inspection'] }} روز مانده</div>
                    @endif
                </div>

                <!-- Financial Status -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    @php
                        $hasDebt = $data['data']['financial_info']['has_debt'] ?? false;
                        $debtColor = $hasDebt ? 'text-red-600' : 'text-green-600';
                        $debtBg = $hasDebt ? 'bg-red-100' : 'bg-green-100';
                        $debtText = $hasDebt ? 'دارای بدهی' : 'بدون بدهی';
                    @endphp
                    <div class="w-16 h-16 {{ $debtBg }} rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 {{ $debtColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="text-sm text-gray-600 mb-1">وضعیت مالی</div>
                    <div class="font-semibold {{ $debtColor }}">{{ $debtText }}</div>
                    @if($hasDebt)
                        <div class="text-xs text-gray-500 mt-1">{{ $data['data']['financial_info']['total_amount_formatted'] ?? number_format(intval(($data['data']['financial_info']['total_debt'] ?? 0) / 10)) . ' تومان' }}</div>
                    @endif
                </div>
            </div>

            <!-- Vehicle Information -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    مشخصات فنی خودرو
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @if(!empty($data['data']['vehicle_info']['brand']))
                        <div class="bg-sky-50 rounded-lg p-4">
                            <div class="text-gray-600 text-sm mb-1">برند و مدل</div>
                            <div class="font-semibold text-gray-900">
                                {{ $data['data']['vehicle_info']['brand'] }}
                                @if(!empty($data['data']['vehicle_info']['model']))
                                    {{ $data['data']['vehicle_info']['model'] }}
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_info']['production_year']))
                        <div class="bg-sky-50 rounded-lg p-4">
                            <div class="text-gray-600 text-sm mb-1">سال تولید</div>
                            <div class="font-semibold text-gray-900">{{ $data['data']['vehicle_info']['production_year'] }}</div>
                        </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_info']['color']))
                        <div class="bg-sky-50 rounded-lg p-4">
                            <div class="text-gray-600 text-sm mb-1">رنگ</div>
                            <div class="font-semibold text-gray-900">{{ $data['data']['vehicle_info']['color'] }}</div>
                        </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_info']['fuel_type']))
                        <div class="bg-sky-50 rounded-lg p-4">
                            <div class="text-gray-600 text-sm mb-1">نوع سوخت</div>
                            <div class="font-semibold text-gray-900">{{ $data['data']['vehicle_info']['fuel_type'] }}</div>
                        </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_info']['engine_power']))
                        <div class="bg-sky-50 rounded-lg p-4">
                            <div class="text-gray-600 text-sm mb-1">قدرت موتور</div>
                            <div class="font-semibold text-gray-900">{{ $data['data']['vehicle_info']['engine_power'] }}</div>
                        </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_info']['cylinder_capacity']))
                        <div class="bg-sky-50 rounded-lg p-4">
                            <div class="text-gray-600 text-sm mb-1">حجم موتور</div>
                            <div class="font-semibold text-gray-900">{{ $data['data']['vehicle_info']['cylinder_capacity'] }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Financial Details -->
            @if($hasDebt)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        جزئیات بدهی‌ها
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-red-600 mb-1">{{ $data['data']['financial_info']['total_amount_formatted'] ?? number_format(intval(($data['data']['financial_info']['total_debt'] ?? 0) / 10)) }}</div>
                            <div class="text-sm text-red-700">مجموع بدهی</div>
                        </div>
                        
                        @if(($data['data']['financial_info']['traffic_fines'] ?? 0) > 0)
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-orange-600 mb-1">{{ number_format(intval(($data['data']['financial_info']['traffic_fines'] ?? 0) / 10)) }}</div>
                                <div class="text-sm text-orange-700">جرائم رانندگی</div>
                            </div>
                        @endif
                        
                        @if(($data['data']['financial_info']['annual_tax'] ?? 0) > 0)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-yellow-600 mb-1">{{ number_format(intval(($data['data']['financial_info']['annual_tax'] ?? 0) / 10)) }}</div>
                                <div class="text-sm text-yellow-700">عوارض سالانه</div>
                            </div>
                        @endif
                        
                        @if(($data['data']['financial_info']['other_fees'] ?? 0) > 0)
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-purple-600 mb-1">{{ number_format(intval(($data['data']['financial_info']['other_fees'] ?? 0) / 10)) }}</div>
                                <div class="text-sm text-purple-700">سایر هزینه‌ها</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Registration & Insurance Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Registration Info -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        اطلاعات ثبت
                    </h3>
                    
                    <div class="space-y-3 text-sm">
                        @if(!empty($data['data']['registration_info']['registration_date']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">تاریخ ثبت:</span>
                                <span class="font-medium">{{ $data['data']['registration_info']['registration_date'] }}</span>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['registration_info']['registration_location']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">محل ثبت:</span>
                                <span class="font-medium">{{ $data['data']['registration_info']['registration_location'] }}</span>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['registration_info']['usage_type']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">نوع کاربری:</span>
                                <span class="font-medium">{{ $data['data']['registration_info']['usage_type'] }}</span>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['registration_info']['plate_type']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">نوع پلاک:</span>
                                <span class="font-medium">{{ $data['data']['registration_info']['plate_type'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Insurance Info -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        اطلاعات بیمه
                    </h3>
                    
                    <div class="space-y-3 text-sm">
                        @if(!empty($data['data']['insurance_info']['insurance_company']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">شرکت بیمه:</span>
                                <span class="font-medium">{{ $data['data']['insurance_info']['insurance_company'] }}</span>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['insurance_info']['start_date']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">شروع بیمه:</span>
                                <span class="font-medium">{{ $data['data']['insurance_info']['start_date'] }}</span>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['insurance_info']['end_date']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">پایان بیمه:</span>
                                <span class="font-medium">{{ $data['data']['insurance_info']['end_date'] }}</span>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['insurance_info']['coverage_type']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">نوع پوشش:</span>
                                <span class="font-medium">{{ $data['data']['insurance_info']['coverage_type'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">عملیات</h3>
                <div class="space-y-3">
                    <button onclick="window.print()" class="w-full bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        چاپ گزارش
                    </button>
                    <button onclick="copyAllInfo()" class="w-full bg-sky-100 hover:bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        کپی اطلاعات
                    </button>
                    <button onclick="shareReport()" class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        اشتراک‌گذاری
                    </button>
                </div>
            </div>

            <!-- Recommendations -->
            @if(!empty($data['data']['recommendations']))
                <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-green-800 mb-3">توصیه‌ها</h3>
                    <ul class="text-sm text-green-700 space-y-2">
                        @foreach($data['data']['recommendations'] as $recommendation)
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-green-600 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $recommendation }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Quick Summary -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">خلاصه اطلاعات</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاریخ استعلام:</span>
                        <span class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">زمان استعلام:</span>
                        <span class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('H:i') }}</span>
                    </div>
                    @if(!empty($data['data']['vehicle_info']['brand']))
                        <div class="flex justify-between">
                            <span class="text-gray-600">خودرو:</span>
                            <span class="font-medium">{{ $data['data']['vehicle_info']['brand'] }} {{ $data['data']['vehicle_info']['model'] ?? '' }}</span>
                        </div>
                    @endif
                    @if(!empty($data['data']['vehicle_info']['production_year']))
                        <div class="flex justify-between">
                            <span class="text-gray-600">سال تولید:</span>
                            <span class="font-medium">{{ $data['data']['vehicle_info']['production_year'] }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Plate Visualization -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">شماره پلاک</h3>
                <div class="bg-white border-4 border-gray-800 rounded-lg p-3 shadow-sm mb-4" style="font-family: 'Courier New', monospace;">
                    <!-- Iran Flag Colors -->
                    <div class="flex justify-center mb-1">
                        <div class="w-2 h-1 bg-green-500"></div>
                        <div class="w-2 h-1 bg-white border-t border-b border-gray-300"></div>
                        <div class="w-2 h-1 bg-red-500"></div>
                    </div>
                    
                    <!-- Plate Number -->
                    <div class="text-lg font-bold text-gray-900 mb-1">
                        {{ $data['data']['formatted_plate'] ?? $data['data']['plate_number'] ?? 'نامشخص' }}
                    </div>
                    
                    <!-- Iran Text -->
                    <div class="text-xs text-gray-600">ایران</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const vehicleData = @json($data['data'] ?? []);

function copyAllInfo() {
    let text = 'گزارش جامع خودرو\n';
    text += '='.repeat(40) + '\n\n';
    text += `پلاک: ${vehicleData.plate_number || 'نامشخص'}\n`;
    text += `تاریخ گزارش: {{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d H:i') }}\n\n`;
    
    // Vehicle Info
    if (vehicleData.vehicle_info) {
        text += 'مشخصات خودرو:\n';
        if (vehicleData.vehicle_info.brand) text += `برند: ${vehicleData.vehicle_info.brand} ${vehicleData.vehicle_info.model || ''}\n`;
        if (vehicleData.vehicle_info.production_year) text += `سال تولید: ${vehicleData.vehicle_info.production_year}\n`;
        if (vehicleData.vehicle_info.color) text += `رنگ: ${vehicleData.vehicle_info.color}\n`;
        if (vehicleData.vehicle_info.fuel_type) text += `نوع سوخت: ${vehicleData.vehicle_info.fuel_type}\n`;
        text += '\n';
    }
    
    // Status Info
    if (vehicleData.status_info) {
        text += 'وضعیت:\n';
        text += `وضعیت خودرو: ${vehicleData.status_info.status_description || 'نامشخص'}\n`;
        text += `فعال: ${vehicleData.status_info.is_active ? 'بله' : 'خیر'}\n`;
        if (vehicleData.status_info.is_blocked) text += `مسدود: بله - ${vehicleData.status_info.block_reason || ''}\n`;
        text += '\n';
    }
    
    // Insurance
    if (vehicleData.insurance_info) {
        text += 'بیمه:\n';
        text += `وضعیت: ${vehicleData.insurance_info.is_insurance_valid ? 'معتبر' : 'منقضی'}\n`;
        if (vehicleData.insurance_info.insurance_company) text += `شرکت بیمه: ${vehicleData.insurance_info.insurance_company}\n`;
        if (vehicleData.insurance_info.end_date) text += `تاریخ انقضا: ${vehicleData.insurance_info.end_date}\n`;
        text += '\n';
    }
    
    // Financial
    if (vehicleData.financial_info && vehicleData.financial_info.has_debt) {
        text += 'اطلاعات مالی:\n';
        text += `مجموع بدهی: ${Math.floor(vehicleData.financial_info.total_debt / 10).toLocaleString()} تومان\n`;
        if (vehicleData.financial_info.traffic_fines > 0) text += `جرائم: ${Math.floor(vehicleData.financial_info.traffic_fines / 10).toLocaleString()} تومان\n`;
        text += '\n';
    }
    
    text += 'تولید شده در پیشخوانک (pishkhanak.com)';
    
    copyToClipboard(text);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('کپی شد!', 'success');
    }).catch(() => {
        showToast('خطا در کپی کردن', 'error');
    });
}

function shareReport() {
    if (navigator.share) {
        navigator.share({
            title: 'گزارش جامع خودرو',
            text: 'گزارش خودرو من از پیشخوانک',
            url: window.location.href
        });
    } else {
        copyToClipboard(window.location.href);
        showToast('لینک کپی شد!', 'success');
    }
}

function showToast(message, type) {
    const toast = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-sky-500'
    };
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 ${colors[type] || 'bg-sky-500'}`;
    toast.textContent = message;
    setTimeout(() => toast.remove(), 3000);
    document.body.appendChild(toast);
}
</script>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .bg-sky-50 { background: #f9fafb !important; }
    .bg-sky-50 { background: #eff6ff !important; }
    .bg-green-50 { background: #f0fdf4 !important; }
    .bg-red-50 { background: #fef2f2 !important; }
    .border { border: 1px solid #e5e7eb !important; }
}
</style>
@endsection 