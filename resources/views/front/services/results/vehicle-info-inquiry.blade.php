@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-sky-50 to-indigo-50 rounded-xl p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">نتیجه استعلام اطلاعات خودرو</h1>
                <p class="text-gray-600">اطلاعات کامل و تخفیفات مربوط به خودرو</p>
            </div>
            <div class="text-right">
                @php
                    $isInsuranceActive = $data['data']['insurance_info']['is_active'] ?? false;
                    $statusColor = $isInsuranceActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    $statusText = $isInsuranceActive ? 'بیمه فعال' : 'بیمه منقضی';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        @if($isInsuranceActive)
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
            <!-- Vehicle Specifications -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    مشخصات فنی خودرو
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if(!empty($data['data']['vehicle_specs']['axel_no']))
                    <div class="bg-sky-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">تعداد محور</label>
                        <p class="text-gray-800">{{ $data['data']['vehicle_specs']['axel_no'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_specs']['wheel_no']))
                    <div class="bg-sky-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">تعداد چرخ</label>
                        <p class="text-gray-800">{{ $data['data']['vehicle_specs']['wheel_no'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_specs']['capacity']))
                    <div class="bg-sky-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">ظرفیت</label>
                        <p class="text-gray-800">{{ $data['data']['vehicle_specs']['capacity'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_specs']['cylinder_no']))
                    <div class="bg-sky-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">تعداد سیلندر</label>
                        <p class="text-gray-800">{{ $data['data']['vehicle_specs']['cylinder_no'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_specs']['main_color']))
                    <div class="bg-sky-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">رنگ اصلی</label>
                        <p class="text-gray-800">{{ $data['data']['vehicle_specs']['main_color'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_specs']['second_color']))
                    <div class="bg-sky-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">رنگ دوم</label>
                        <p class="text-gray-800">{{ $data['data']['vehicle_specs']['second_color'] }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Vehicle Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    جزئیات خودرو
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(!empty($data['data']['vehicle_details']['system_name_by_naja']))
                    <div class="bg-purple-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">سیستم (ناجا)</label>
                        <p class="text-gray-800">{{ $data['data']['vehicle_details']['system_name_by_naja'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_details']['tip_by_naja']))
                    <div class="bg-purple-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">تیپ (ناجا)</label>
                        <p class="text-gray-800">{{ $data['data']['vehicle_details']['tip_by_naja'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_details']['model_by_naja']))
                    <div class="bg-purple-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">مدل</label>
                        <p class="text-gray-800">{{ $data['data']['vehicle_details']['model_by_naja'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_details']['system_by_insurance_company']))
                    <div class="bg-purple-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">سیستم (بیمه)</label>
                        <p class="text-gray-800">{{ $data['data']['vehicle_details']['system_by_insurance_company'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_details']['tip_by_central_insurance']))
                    <div class="bg-purple-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">تیپ (بیمه مرکزی)</label>
                        <p class="text-gray-800">{{ $data['data']['vehicle_details']['tip_by_central_insurance'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['vehicle_details']['vehicle_type_name_by_insurance_company']))
                    <div class="bg-purple-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">نوع خودرو</label>
                        <p class="text-gray-800">{{ $data['data']['vehicle_details']['vehicle_type_name_by_insurance_company'] }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Insurance Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    اطلاعات بیمه
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(!empty($data['data']['insurance_info']['insurance_company_title']))
                    <div class="bg-green-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">شرکت بیمه</label>
                        <p class="text-gray-800">{{ $data['data']['insurance_info']['insurance_company_title'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['insurance_info']['insurance_print_number']))
                    <div class="bg-green-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">شماره چاپی بیمه نامه</label>
                        <p class="text-gray-800 font-mono">{{ $data['data']['insurance_info']['insurance_print_number'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['insurance_info']['insurance_unique_code']))
                    <div class="bg-green-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">کد یکتای بیمه نامه</label>
                        <p class="text-gray-800 font-mono">{{ $data['data']['insurance_info']['insurance_unique_code'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['insurance_info']['begin_date']))
                    <div class="bg-green-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">تاریخ شروع</label>
                        <p class="text-gray-800">{{ $data['data']['insurance_info']['begin_date'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['insurance_info']['end_date']))
                    <div class="bg-green-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">تاریخ پایان</label>
                        <p class="text-gray-800">{{ $data['data']['insurance_info']['end_date'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['insurance_info']['insurance_duration']))
                    <div class="bg-green-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">مدت بیمه</label>
                        <p class="text-gray-800">{{ $data['data']['insurance_info']['insurance_duration'] }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Discount Information -->
            @php
                $lifeDiscount = (int)($data['data']['discount_info']['discount_life_year_number'] ?? 0);
                $personDiscount = (int)($data['data']['discount_info']['discount_person_year_number'] ?? 0);
                $financialDiscount = (int)($data['data']['discount_info']['discount_financial_year_number'] ?? 0);
                $hasDiscounts = $lifeDiscount > 0 || $personDiscount > 0 || $financialDiscount > 0;
            @endphp
            
            @if($hasDiscounts)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    تخفیفات بیمه
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($lifeDiscount > 0)
                    <div class="bg-orange-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-orange-600 mb-1">{{ $lifeDiscount }}</div>
                        <div class="text-sm text-gray-600">سال تخفیف جانی</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $data['data']['discount_info']['discount_life_year_percent'] ?? '0' }}%</div>
                    </div>
                    @endif
                    
                    @if($personDiscount > 0)
                    <div class="bg-orange-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-orange-600 mb-1">{{ $personDiscount }}</div>
                        <div class="text-sm text-gray-600">سال تخفیف راننده</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $data['data']['discount_info']['discount_person_year_percent'] ?? '0' }}%</div>
                    </div>
                    @endif
                    
                    @if($financialDiscount > 0)
                    <div class="bg-orange-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-orange-600 mb-1">{{ $financialDiscount }}</div>
                        <div class="text-sm text-gray-600">سال تخفیف مالی</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $data['data']['discount_info']['discount_financial_year_percent'] ?? '0' }}%</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Identification -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-6 0h6zm6-6a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M15 14a3.001 3.001 0 00-6 0h6z"></path>
                    </svg>
                    شناسایی خودرو
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(!empty($data['data']['identification']['chassis_number']))
                    <div class="bg-indigo-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">شماره شاسی</label>
                        <p class="text-gray-800 font-mono">{{ $data['data']['identification']['chassis_number'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['identification']['engine_number']))
                    <div class="bg-indigo-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">شماره موتور</label>
                        <p class="text-gray-800 font-mono">{{ $data['data']['identification']['engine_number'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['identification']['vin']))
                    <div class="bg-indigo-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">شماره VIN</label>
                        <p class="text-gray-800 font-mono">{{ $data['data']['identification']['vin'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['identification']['plate_install_date']))
                    <div class="bg-indigo-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">تاریخ نصب پلاک</label>
                        <p class="text-gray-800">{{ $data['data']['identification']['plate_install_date'] }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Usage Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    اطلاعات کاربری
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(!empty($data['data']['usage_info']['usage_name_by_naja']))
                    <div class="bg-teal-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">کاربری (ناجا)</label>
                        <p class="text-gray-800">{{ $data['data']['usage_info']['usage_name_by_naja'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['usage_info']['usage_name_by_insurance_company']))
                    <div class="bg-teal-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">کاربری (بیمه)</label>
                        <p class="text-gray-800">{{ $data['data']['usage_info']['usage_name_by_insurance_company'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['usage_info']['sub_usage']))
                    <div class="bg-teal-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">کاربری دوم</label>
                        <p class="text-gray-800">{{ $data['data']['usage_info']['sub_usage'] }}</p>
                    </div>
                    @endif
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
                    
                    <a href="{{ route('services.show', 'vehicle-info-inquiry') }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        استعلام جدید
                    </a>
                </div>
            </div>

            <!-- Summary -->
            @if(!empty($data['data']['summary']))
            <div class="bg-sky-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-sky-800 mb-3">خلاصه</h3>
                <p class="text-sky-700 text-sm">{{ $data['data']['summary'] }}</p>
            </div>
            @endif

            <!-- Recommendations -->
            @if(!empty($data['data']['recommendations']))
            <div class="bg-orange-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-orange-800 mb-3">توصیه‌ها</h3>
                <ul class="space-y-2 text-orange-700 text-sm">
                    @foreach($data['data']['recommendations'] as $recommendation)
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $recommendation }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

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
    const vehicleData = @json($data['data']);
    let text = 'گزارش اطلاعات خودرو\n';
    text += '='.repeat(50) + '\n\n';
    text += `تاریخ استعلام: ${vehicleData.processed_date || new Date().toLocaleDateString('fa-IR')}\n\n`;
    
    // Vehicle Specifications
    if (vehicleData.vehicle_specs) {
        text += 'مشخصات فنی:\n';
        if (vehicleData.vehicle_specs.axel_no) text += `تعداد محور: ${vehicleData.vehicle_specs.axel_no}\n`;
        if (vehicleData.vehicle_specs.wheel_no) text += `تعداد چرخ: ${vehicleData.vehicle_specs.wheel_no}\n`;
        if (vehicleData.vehicle_specs.capacity) text += `ظرفیت: ${vehicleData.vehicle_specs.capacity}\n`;
        if (vehicleData.vehicle_specs.cylinder_no) text += `تعداد سیلندر: ${vehicleData.vehicle_specs.cylinder_no}\n`;
        if (vehicleData.vehicle_specs.main_color) text += `رنگ اصلی: ${vehicleData.vehicle_specs.main_color}\n`;
        if (vehicleData.vehicle_specs.second_color) text += `رنگ دوم: ${vehicleData.vehicle_specs.second_color}\n`;
        text += '\n';
    }
    
    // Vehicle Details
    if (vehicleData.vehicle_details) {
        text += 'جزئیات خودرو:\n';
        if (vehicleData.vehicle_details.system_name_by_naja) text += `سیستم: ${vehicleData.vehicle_details.system_name_by_naja}\n`;
        if (vehicleData.vehicle_details.tip_by_naja) text += `تیپ: ${vehicleData.vehicle_details.tip_by_naja}\n`;
        if (vehicleData.vehicle_details.model_by_naja) text += `مدل: ${vehicleData.vehicle_details.model_by_naja}\n`;
        text += '\n';
    }
    
    // Insurance Information
    if (vehicleData.insurance_info) {
        text += 'اطلاعات بیمه:\n';
        if (vehicleData.insurance_info.insurance_company_title) text += `شرکت بیمه: ${vehicleData.insurance_info.insurance_company_title}\n`;
        if (vehicleData.insurance_info.begin_date) text += `تاریخ شروع: ${vehicleData.insurance_info.begin_date}\n`;
        if (vehicleData.insurance_info.end_date) text += `تاریخ پایان: ${vehicleData.insurance_info.end_date}\n`;
        if (vehicleData.insurance_info.insurance_duration) text += `مدت بیمه: ${vehicleData.insurance_info.insurance_duration}\n`;
        text += `وضعیت بیمه: ${vehicleData.insurance_info.is_active ? 'فعال' : 'منقضی'}\n\n`;
    }
    
    // Identification
    if (vehicleData.identification) {
        text += 'شناسایی:\n';
        if (vehicleData.identification.chassis_number) text += `شماره شاسی: ${vehicleData.identification.chassis_number}\n`;
        if (vehicleData.identification.engine_number) text += `شماره موتور: ${vehicleData.identification.engine_number}\n`;
        if (vehicleData.identification.vin) text += `شماره VIN: ${vehicleData.identification.vin}\n`;
        text += '\n';
    }
    
    if (vehicleData.summary) text += `خلاصه: ${vehicleData.summary}\n\n`;
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