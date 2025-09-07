@extends('front.layouts.app')

@section('title', 'نتیجه استعلام سوابق بیمه شخص ثالث')

@section('head')
<script src="{{ asset('js/jspdf.min.js') }}"></script>
<script src="{{ asset('js/html2canvas.min.js') }}"></script>
<style>
@media print {
    body { print-color-adjust: exact; }
    .no-print { display: none !important; }
    .bg-sky-50 { background-color: white !important; }
    .shadow-sm { box-shadow: none !important; }
    .border { border: 1px solid #e2e8f0 !important; }
    .rounded-xl { border-radius: 8px !important; }
}

.pdf-content {
    background: white;
    color: black;
}

.status-active {
    background: #dcfce7;
    border: 2px solid #22c55e;
}

.status-warning {
    background: #fef3c7;
    border: 2px solid #f59e0b;
}

.status-expired {
    background: #fee2e2;
    border: 2px solid #ef4444;
}

.primary-card {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 2px solid #0ea5e9;
}

.accent-dot {
    background: #f59e0b;
    width: 6px;
    height: 6px;
    border-radius: 50%;
}
</style>
@endsection

@section('content')
<div class="min-h-screen/2 bg-sky-50 py-4 sm:py-6 lg:py-8" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @php
            $result = $data['data'] ?? [];
            $result = $result['data'] ?? [];
            $vehicleInfo = $result['vehicle_info'] ?? [];
            $currentPolicy = $result['current_policy'] ?? [];
            $coverageDetails = $result['coverage_details'] ?? [];
            $discountInfo = $result['discount_info'] ?? [];
            $claimsStats = $result['claims_stats'] ?? [];
            $companyInfo = $result['insurance_company'] ?? [];
            $rawResult = $result['raw_result'] ?? [];
            $endDate = $currentPolicy['end_date'];
            if(!empty($endDate)){
                $endDate = \Hekmatinasser\Verta\Verta::parse($endDate);
                $daysRemaining = $endDate->diffDays(\Hekmatinasser\Verta\Verta::now());
            }else{
                $daysRemaining = 0;
            }
            $isActive = $daysRemaining > 0 ? true : false;
            
            // Determine status
            if (!$isActive || $daysRemaining < 0) {
                $status = ['status' => 'expired', 'text' => 'منقضی شده', 'color' => 'red', 'icon' => 'expired'];
            } elseif ($daysRemaining <= 30) {
                $status = ['status' => 'expiring_soon', 'text' => 'نزدیک به انقضا', 'color' => 'yellow', 'icon' => 'warning'];
            } else {
                $status = ['status' => 'active', 'text' => 'فعال', 'color' => 'green', 'icon' => 'active'];
            }
        @endphp

        <!-- Header -->
        <div class="text-center mb-6 sm:mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-sky-100 rounded-full mb-4 border-2 border-sky-300">
                <svg class="w-8 h-8 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">استعلام سوابق بیمه شخص ثالث</h1>
            <p class="text-gray-600 text-sm sm:text-base">گزارش جامع وضعیت بیمه نامه و سوابق خسارت</p>
        </div>

        @if(isset($data['status']) && $data['status'] === 'success')
        
        <!-- Content for PDF -->
        <div id="pdf-content" class="pdf-content">
            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6 no-print">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-3">
                    <div class="accent-dot"></div>
                    عملیات
                </h2>
                <div class="flex flex-wrap gap-2 sm:gap-3">
                    <button onclick="copyResults()" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">کپی اطلاعات</span>
                        <span class="sm:hidden">کپی</span>
                    </button>
                    <button onclick="shareResults()" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        <span class="hidden sm:inline">اشتراک‌گذاری</span>
                        <span class="sm:hidden">اشتراک</span>
                    </button>
                    <button onclick="printResults()" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span class="hidden sm:inline">چاپ گزارش</span>
                        <span class="sm:hidden">چاپ</span>
                    </button>
                    <button onclick="downloadPDF()" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">دانلود PDF</span>
                        <span class="sm:hidden">PDF</span>
                    </button>
                </div>
            </div>

            <!-- Insurance Status Alert -->
            @if($status['color'] === 'red' || $status['color'] === 'yellow')
            <div class="mb-6 p-4 sm:p-6 rounded-xl border-2 
                @if($status['color'] === 'red') bg-red-50 border-red-200 text-red-800
                @else bg-yellow-50 border-yellow-200 text-yellow-800 @endif no-print">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        @if($status['color'] === 'red')
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        @else
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-lg mb-2">
                            @if($status['color'] === 'red')
                                🚨 بیمه شما منقضی شده است!
                            @else
                                ⚠️ بیمه شما به زودی منقضی می‌شود
                            @endif
                        </div>
                        <div class="text-sm mb-3">
                            @if($status['color'] === 'red')
                                رانندگی بدون بیمه شخص ثالث جرم است و با جریمه سنگین همراه است. فوراً بیمه نامه جدید تهیه کنید.
                            @else
                                {{ $daysRemaining }} روز تا انقضا باقی مانده است. هر چه زودتر برای تمدید اقدام کنید.
                            @endif
                        </div>
                        <a href="{{ route('services.show', ['slug1' => 'third-party-insurance-history']) }}" class="inline-flex items-center gap-2 bg-white text-{{ $status['color'] === 'red' ? 'red' : 'yellow' }}-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            استعلام مجدد
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Insurance Policy Summary Card -->
            @if(!empty($currentPolicy) || !empty($companyInfo))
            <div class="primary-card rounded-xl p-6 sm:p-8 mb-6 {{ $status['color'] === 'green' ? 'status-active' : ($status['color'] === 'yellow' ? 'status-warning' : 'status-expired') }}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm border-2 border-sky-300">
                                @if($status['color'] === 'green')
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                @elseif($status['color'] === 'yellow')
                                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                @else
                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">وضعیت بیمه شخص ثالث</h2>
                                <p class="text-gray-700 mt-1">{{ $status['text'] }} - {{ $vehicleInfo['formatted_plate'] ?? 'نامشخص' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(!empty($companyInfo['company_name']))
                            <div class="bg-white bg-opacity-90 rounded-lg p-4 border border-sky-200">
                                <div class="text-sm text-sky-700 mb-1 font-medium">شرکت بیمه‌گر</div>
                                <div class="font-bold text-gray-900 text-lg">{{ $companyInfo['company_name'] }}</div>
                            </div>
                            @endif
                            
                            @if($daysRemaining >= 0)
                            <div class="bg-white bg-opacity-90 rounded-lg p-4 border border-sky-200">
                                <div class="text-sm text-sky-700 mb-1 font-medium">روزهای باقی‌مانده</div>
                                <div class="font-bold text-{{ $status['color'] === 'green' ? 'green' : ($status['color'] === 'yellow' ? 'yellow' : 'red') }}-700 text-lg">
                                    {{ $daysRemaining }} روز
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Insurance Company Logo -->
                    @if(!empty($companyInfo['company_name']))
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 bg-white rounded-xl shadow-sm flex items-center justify-center p-3 border-2 border-sky-200">
                            @php
                                $logoMapping = [
                                    'بیمه ایران' => 'iran.svg',
                                    'بیمه آسیا' => 'asia.svg',
                                    'بیمه پارسیان' => 'parsian.svg',
                                    'بیمه پاسارگاد' => 'pasargad.svg',
                                    'بیمه ملت' => 'mellat.svg',
                                    'بیمه دانا' => 'dana.svg',
                                    'بیمه سامان' => 'saman.svg',
                                    'بیمه البرز' => 'alborz.svg',
                                    'بیمه دی' => 'dey.svg',
                                    'بیمه کوثر' => 'kosar.svg',
                                    'بیمه رازی' => 'razi.svg',
                                    'بیمه آرمان' => 'arman.svg',
                                    'بیمه کارآفرین' => 'karafarin.svg',
                                    'بیمه م.ا' => 'ma.svg',
                                    'بیمه ما' => 'ma.svg',
                                    'بیمه میهن' => 'mihan.svg',
                                    'بیمه نوین' => 'novin.svg',
                                    'بیمه سرمد' => 'sarmad.svg',
                                    'بیمه سینا' => 'sina.svg',
                                    'بیمه تعاون' => 'taavon.svg',
                                    'بیمه تجارت نو' => 'tejarat-e-no.svg',
                                    'بیمه معلم' => 'moallem.svg',
                                ];
                                $logoFile = $logoMapping[$companyInfo['company_name']] ?? null;
                            @endphp
                            
                            @if($logoFile && file_exists(public_path("assets/images/insurances/{$logoFile}")))
                                <img src="{{ asset("assets/images/insurances/{$logoFile}") }}" 
                                     alt="{{ $companyInfo['company_name'] }}" 
                                     class="w-full h-full object-contain">
                            @else
                                <div class="text-center">
                                    <svg class="w-8 h-8 text-sky-600 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    <div class="text-xs text-gray-600 font-medium">{{ substr($companyInfo['company_name'], 0, 10) }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Vehicle Information -->
            @if(!empty($vehicleInfo))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    مشخصات خودرو
                </h2>

                <!-- Vehicle Plate Display -->
                @if(!empty($vehicleInfo['plate_parts']))
                <div class="flex justify-center mb-6">
                    <div class="bg-blue-600 text-white p-3 rounded-lg shadow-lg">
                        <div class="flex items-center flex-row-reverse gap-2 font-bold text-lg">
                            <span class="bg-white text-blue-600 px-2 py-1 rounded">{{ $vehicleInfo['plate_parts']['part1'] }}</span>
                            <span class="bg-yellow-400 text-blue-600 px-2 py-1 rounded font-bold">{{ $vehicleInfo['plate_parts']['letter'] }}</span>
                            <span class="bg-white text-blue-600 px-2 py-1 rounded">{{ $vehicleInfo['plate_parts']['part2'] }}</span>
                            <span class="bg-white text-blue-600 px-2 py-1 rounded text-sm">{{ $vehicleInfo['plate_parts']['serial'] }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if(!empty($vehicleInfo['vehicle_system']))
                    <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <div class="text-sm text-sky-700 font-medium">سازنده</div>
                        </div>
                        <div class="font-bold text-gray-900">{{ $vehicleInfo['vehicle_system'] }}</div>
                    </div>
                    @endif

                    @if(!empty($vehicleInfo['vehicle_type']))
                    <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414A1 1 0 0016 10v6a1 1 0 01-1 1h-1m-1-1a1 1 0 01-1 1H9m4-1a1 1 0 01-1 1H9"></path>
                            </svg>
                            <div class="text-sm text-sky-700 font-medium">مدل</div>
                        </div>
                        <div class="font-bold text-gray-900">{{ $vehicleInfo['vehicle_type'] }}</div>
                    </div>
                    @endif

                    @if(!empty($vehicleInfo['model_year']))
                    <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div class="text-sm text-sky-700 font-medium">سال ساخت</div>
                        </div>
                        <div class="font-bold text-gray-900">{{ $vehicleInfo['model_year'] }}</div>
                    </div>
                    @endif

                    @if(!empty($vehicleInfo['main_color']))
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                            <div class="text-sm text-gray-600 font-medium">رنگ</div>
                        </div>
                        <div class="font-bold text-gray-900">{{ $vehicleInfo['main_color'] }}</div>
                    </div>
                    @endif

                    @if(!empty($vehicleInfo['vehicle_usage']))
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V6"></path>
                            </svg>
                            <div class="text-sm text-gray-600 font-medium">کاربری</div>
                        </div>
                        <div class="font-bold text-gray-900">{{ $vehicleInfo['vehicle_usage'] }}</div>
                    </div>
                    @endif

                    @if(!empty($vehicleInfo['capacity']))
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <div class="text-sm text-gray-600 font-medium">ظرفیت</div>
                        </div>
                        <div class="font-bold text-gray-900">{{ $vehicleInfo['capacity'] }}</div>
                    </div>
                    @endif
                </div>

                <!-- Technical Details -->
                @if(!empty($vehicleInfo['engine_number']) || !empty($vehicleInfo['chassis_number']) || !empty($vehicleInfo['vin_number']))
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        مشخصات فنی
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        @if(!empty($vehicleInfo['engine_number']))
                        <div class="flex justify-between bg-gray-50 rounded-lg p-3">
                            <span class="text-gray-600 font-medium">شماره موتور:</span>
                            <span class="font-bold font-mono text-gray-900">{{ $vehicleInfo['engine_number'] }}</span>
                        </div>
                        @endif
                        
                        @if(!empty($vehicleInfo['chassis_number']))
                        <div class="flex justify-between bg-gray-50 rounded-lg p-3">
                            <span class="text-gray-600 font-medium">شماره شاسی:</span>
                            <span class="font-bold font-mono text-gray-900">{{ $vehicleInfo['chassis_number'] }}</span>
                        </div>
                        @endif
                        
                        @if(!empty($vehicleInfo['vin_number']))
                        <div class="flex justify-between bg-gray-50 rounded-lg p-3">
                            <span class="text-gray-600 font-medium">شماره VIN:</span>
                            <span class="font-bold font-mono text-gray-900">{{ $vehicleInfo['vin_number'] }}</span>
                        </div>
                        @endif
                        
                        @if(!empty($vehicleInfo['cylinder_count']))
                        <div class="flex justify-between bg-gray-50 rounded-lg p-3">
                            <span class="text-gray-600 font-medium">تعداد سیلندر:</span>
                            <span class="font-bold text-gray-900">{{ $vehicleInfo['cylinder_count'] }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Current Policy Details -->
            @if(!empty($currentPolicy))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    جزئیات بیمه نامه
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    @if(!empty($currentPolicy['policy_number']))
                    <div class="bg-amber-50 rounded-lg p-4 border-2 border-amber-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div class="text-sm text-amber-700 font-medium">شماره بیمه نامه</div>
                        </div>
                        <div class="font-bold text-amber-900 font-mono cursor-pointer" 
                             onclick="copyToClipboard('{{ $currentPolicy['policy_number'] }}')"
                             title="کلیک کنید تا کپی شود">
                            {{ $currentPolicy['policy_number'] }}
                        </div>
                    </div>
                    @endif

                    @if(!empty($currentPolicy['third_policy_code']))
                    <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            <div class="text-sm text-sky-700 font-medium">کد بیمه نامه</div>
                        </div>
                        <div class="font-bold text-sky-900 font-mono">{{ $currentPolicy['third_policy_code'] }}</div>
                    </div>
                    @endif

                    @if(!empty($companyInfo['company_code']))
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <div class="text-sm text-gray-600 font-medium">کد شرکت</div>
                        </div>
                        <div class="font-bold text-gray-900">{{ $companyInfo['company_code'] }}</div>
                    </div>
                    @endif
                </div>

                <!-- Policy Dates -->
                @if(!empty($currentPolicy['start_date']) || !empty($currentPolicy['end_date']))
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm bg-gray-50 rounded-lg p-4 border border-gray-200">
                    @if(!empty($currentPolicy['issue_date']))
                    <div class="text-center">
                        <div class="text-gray-600 mb-1 font-medium">تاریخ صدور</div>
                        <div class="font-bold text-gray-900">{{ $currentPolicy['issue_date'] }}</div>
                    </div>
                    @endif
                    
                    @if(!empty($currentPolicy['start_date']))
                    <div class="text-center">
                        <div class="text-gray-600 mb-1 font-medium">شروع بیمه</div>
                        <div class="font-bold text-sky-700">{{ $currentPolicy['start_date'] }}</div>
                    </div>
                    @endif
                    
                    @if(!empty($currentPolicy['end_date']))
                    <div class="text-center">
                        <div class="text-gray-600 mb-1 font-medium">پایان بیمه</div>
                        <div class="font-bold {{ $status['color'] === 'red' ? 'text-red-600' : ($status['color'] === 'yellow' ? 'text-yellow-600' : 'text-sky-600') }}">
                            {{ $currentPolicy['end_date'] }}
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endif

            <!-- Coverage Details -->
            @if(!empty($coverageDetails))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    پوشش بیمه
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @if(!empty($coverageDetails['person_coverage']))
                    <div class="bg-sky-50 border border-sky-200 rounded-lg p-6 text-center">
                        <div class="w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="text-sky-700 text-sm font-medium mb-2">حوادث راننده</div>
                        <div class="font-bold text-sky-900 text-xl mb-1">{{ $coverageDetails['person_coverage']['formatted_toman'] ?? 'نامشخص' }}</div>
                        <div class="text-sky-600 text-xs">{{ number_format(($coverageDetails['person_coverage']['amount'] ?? 0) / 10) }} تومان</div>
                    </div>
                    @endif

                    @if(!empty($coverageDetails['life_coverage']))
                    <div class="bg-amber-50 border-2 border-amber-200 rounded-lg p-6 text-center">
                        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <div class="text-amber-700 text-sm font-medium mb-2">خسارت جانی</div>
                        <div class="font-bold text-amber-900 text-xl mb-1">{{ $coverageDetails['life_coverage']['formatted_toman'] ?? 'نامشخص' }}</div>
                        <div class="text-amber-600 text-xs">{{ number_format(($coverageDetails['life_coverage']['amount'] ?? 0) / 10) }} تومان</div>
                    </div>
                    @endif

                    @if(!empty($coverageDetails['financial_coverage']))
                    <div class="bg-sky-50 border border-sky-200 rounded-lg p-6 text-center">
                        <div class="w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="text-sky-700 text-sm font-medium mb-2">خسارت مالی</div>
                        <div class="font-bold text-sky-900 text-xl mb-1">{{ $coverageDetails['financial_coverage']['formatted_toman'] ?? 'نامشخص' }}</div>
                        <div class="text-sky-600 text-xs">{{ number_format(($coverageDetails['financial_coverage']['amount'] ?? 0) / 10) }} تومان</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Discount Information -->
            @if(!empty($discountInfo))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    وضعیت تخفیف
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @if(!empty($discountInfo['person_discount']))
                    <div class="bg-sky-50 border border-sky-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h3 class="font-bold text-sky-800">حوادث راننده</h3>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-sky-600 font-medium">سال‌های بدون خسارت:</span>
                                <span class="font-bold text-sky-900">{{ $discountInfo['person_discount']['years_without_claim'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sky-600 font-medium">درصد تخفیف:</span>
                                <span class="font-bold text-sky-900">{{ $discountInfo['person_discount']['percentage'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!empty($discountInfo['financial_discount']))
                    <div class="bg-sky-50 border border-sky-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <h3 class="font-bold text-sky-800">خسارت مالی</h3>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-sky-600 font-medium">سال‌های بدون خسارت:</span>
                                <span class="font-bold text-sky-900">{{ $discountInfo['financial_discount']['years_without_claim'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sky-600 font-medium">درصد تخفیف:</span>
                                <span class="font-bold text-sky-900">{{ $discountInfo['financial_discount']['percentage'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!empty($discountInfo['life_discount']))
                    <div class="bg-amber-50 border-2 border-amber-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <h3 class="font-bold text-amber-800">خسارت جانی</h3>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-amber-600 font-medium">سال‌های بدون خسارت:</span>
                                <span class="font-bold text-amber-900">{{ $discountInfo['life_discount']['years_without_claim'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-amber-600 font-medium">درصد تخفیف:</span>
                                <span class="font-bold text-amber-900">{{ $discountInfo['life_discount']['percentage'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Claims Statistics -->
            @if(!empty($claimsStats))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    آمار خسارات
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="text-center bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-gray-700 mb-1">{{ $claimsStats['total_loss_count'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600 font-medium">کل خسارات</div>
                    </div>

                    <div class="text-center bg-amber-50 rounded-lg p-4 border-2 border-amber-200">
                        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-amber-700 mb-1">{{ $claimsStats['policy_health_loss'] ?? 0 }}</div>
                        <div class="text-sm text-amber-600 font-medium">خسارت جانی</div>
                    </div>

                    <div class="text-center bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <div class="w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-sky-600 mb-1">{{ $claimsStats['policy_financial_loss'] ?? 0 }}</div>
                        <div class="text-sm text-sky-700 font-medium">خسارت مالی</div>
                    </div>

                    <div class="text-center bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <div class="w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-sky-600 mb-1">{{ $claimsStats['policy_person_loss'] ?? 0 }}</div>
                        <div class="text-sm text-sky-700 font-medium">خسارت راننده</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Track ID -->
            @if(!empty($result['track_id']))
            <div class="bg-gray-50 rounded-lg p-4 text-center text-sm text-gray-600 border border-gray-200">
                <span class="font-medium">کد پیگیری:</span>
                <span class="font-mono bg-white px-2 py-1 rounded border ml-2">{{ $result['track_id'] }}</span>
            </div>
            @endif

        </div>

        @else
        <!-- Error State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">خطا در دریافت اطلاعات</h2>
            <p class="text-gray-600 mb-6">{{ $rawResult['message'] ?? 'اطلاعات یافت نشد.' }}</p>
            <a href="{{ route('services.show', ['slug1' => 'third-party-insurance-history']) }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                تلاش مجدد
            </a>
        </div>
        @endif
    </div>
</div>

<script>
function copyResults() {
    const element = document.getElementById('pdf-content');
    const text = element.innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('اطلاعات کپی شد');
    });
}

function shareResults() {
    if (navigator.share) {
        navigator.share({
            title: 'نتیجه استعلام بیمه شخص ثالث',
            text: 'گزارش بیمه شخص ثالث',
            url: window.location.href
        });
    } else {
        copyToClipboard(window.location.href);
        alert('لینک کپی شد');
    }
}

function printResults() {
    window.print();
}

function downloadPDF() {
    const element = document.getElementById('pdf-content');
    const opt = {
        margin: 1,
        filename: 'third-party-insurance-report.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    
    html2canvas(element).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF();
        const imgWidth = 210;
        const pageHeight = 295;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;
        
        let position = 0;
        
        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
        
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }
        
        pdf.save('third-party-insurance-report.pdf');
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        console.log('Copied to clipboard');
    });
}
</script>
@endsection 