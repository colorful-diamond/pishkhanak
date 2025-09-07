@extends('front.layouts.app')

@section('title', 'نتیجه استعلام تسهیلات بانکی')

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
}

.filter-btn.active {
    background-color: #e0f2fe !important;
    color: #0c4a6e !important;
}

.pdf-content {
    background: white;
    color: black;
}
</style>
@endsection

@section('content')
<div class="min-h-screen/2 bg-sky-50 py-4 sm:py-6 lg:py-8" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-6 sm:mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-sky-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">استعلام تسهیلات بانکی</h1>
            <p class="text-gray-600 text-sm sm:text-base">گزارش جامع وام‌ها و تسهیلات مالی</p>
        </div>

        @if(isset($data['status']) && $data['status'] === 'success')
        @php $result = $data['data']; @endphp

        <!-- Content for PDF -->
        <div id="pdf-content" class="pdf-content">
            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6 no-print">
                <h2 class="text-lg font-bold text-gray-900 mb-4">عملیات</h2>
                <div class="flex flex-wrap gap-2 sm:gap-3">
                    <button onclick="copyResults()" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">کپی اطلاعات</span>
                        <span class="sm:hidden">کپی</span>
                    </button>
                    <button onclick="shareResults()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        <span class="hidden sm:inline">اشتراک‌گذاری</span>
                        <span class="sm:hidden">اشتراک</span>
                    </button>
                    <button onclick="printResults()" class="inline-flex items-center gap-2 bg-zinc-600 hover:bg-zinc-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span class="hidden sm:inline">چاپ گزارش</span>
                        <span class="sm:hidden">چاپ</span>
                    </button>
                    <button onclick="downloadPDF()" class="inline-flex items-center gap-2 bg-yellow-600 hover:bg-yellow-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">دانلود PDF</span>
                        <span class="sm:hidden">PDF</span>
                    </button>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    اطلاعات مشتری
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-sky-50 rounded-lg p-4">
                        <div class="text-sm text-sky-600 mb-1">کد ملی</div>
                        <div class="font-semibold text-gray-900">{{ $result['national_code'] ?? '-' }}</div>
                    </div>
                    @if(!empty($result['customer_name']))
                    <div class="bg-sky-50 rounded-lg p-4">
                        <div class="text-sm text-sky-600 mb-1">نام و نام خانوادگی</div>
                        <div class="font-semibold text-gray-900">{{ $result['customer_name'] }}</div>
                    </div>
                    @endif
                    @if(!empty($result['legal_id']))
                    <div class="bg-sky-50 rounded-lg p-4">
                        <div class="text-sm text-sky-600 mb-1">شناسه حقوقی</div>
                        <div class="font-semibold text-gray-900">{{ $result['legal_id'] }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Facility Summary -->
            @if(isset($result['facility_summary']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 00-2 2v6a2 2 0 00-2 2z"></path>
                    </svg>
                    خلاصه تسهیلات
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <div class="text-sm text-sky-600 mb-1">کل مبلغ تسهیلات</div>
                        <div class="text-lg sm:text-xl font-bold text-sky-900">{{ $result['facility_summary']['formatted_total_amount'] ?? '0 تومان' }}</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                        <div class="text-sm text-yellow-600 mb-1">کل بدهی فعلی</div>
                        <div class="text-lg sm:text-xl font-bold text-yellow-900">{{ $result['facility_summary']['formatted_debt_total_amount'] ?? '0 تومان' }}</div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                        <div class="text-sm text-red-600 mb-1">مبلغ سررسید گذشته</div>
                        <div class="text-lg sm:text-xl font-bold text-red-900">{{ $result['facility_summary']['formatted_past_expired_total_amount'] ?? '0 تومان' }}</div>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                        <div class="text-sm text-amber-600 mb-1">مبلغ معوق</div>
                        <div class="text-lg sm:text-xl font-bold text-amber-900">{{ $result['facility_summary']['formatted_deferred_total_amount'] ?? '0 تومان' }}</div>
                    </div>
                    <div class="bg-zinc-50 rounded-lg p-4 border border-zinc-200">
                        <div class="text-sm text-zinc-600 mb-1">مبلغ مشکوک الوصول</div>
                        <div class="text-lg sm:text-xl font-bold text-zinc-900">{{ $result['facility_summary']['formatted_suspicious_total_amount'] ?? '0 تومان' }}</div>
                    </div>
                    @if(!empty($result['facility_summary']['dishonored']))
                    <div class="bg-sky-50 rounded-lg p-4 border border-gray-200">
                        <div class="text-sm text-gray-600 mb-1">برگشت چک</div>
                        <div class="text-lg sm:text-xl font-bold text-gray-900">{{ $result['facility_summary']['dishonored'] }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Facility List -->
            @if(isset($result['facility_list']) && count($result['facility_list']) > 0)
            @php
                $facilities = $result['facility_list'];
                $activeCount = collect($facilities)->where('is_active', true)->count();
                $overdueCount = collect($facilities)->where('has_past_due', true)->count();
                $suspiciousCount = collect($facilities)->where('has_suspicious', true)->count();
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    فهرست تسهیلات ({{ count($facilities) }} مورد)
                </h2>

                <!-- Filter Options -->
                <div class="mb-4 sm:mb-6 no-print">
                    <div class="flex flex-wrap gap-2">
                        <button onclick="filterFacilities('all')" class="filter-btn active px-3 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-medium bg-sky-100 text-sky-800 hover:bg-sky-200 transition-colors duration-200">
                            همه ({{ count($facilities) }})
                        </button>
                        <button onclick="filterFacilities('active')" class="filter-btn px-3 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-medium bg-sky-100 text-gray-700 hover:bg-sky-200 transition-colors duration-200">
                            فعال ({{ $activeCount }})
                        </button>
                        <button onclick="filterFacilities('overdue')" class="filter-btn px-3 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-medium bg-sky-100 text-gray-700 hover:bg-sky-200 transition-colors duration-200">
                            سررسید گذشته ({{ $overdueCount }})
                        </button>
                        <button onclick="filterFacilities('suspicious')" class="filter-btn px-3 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-medium bg-sky-100 text-gray-700 hover:bg-sky-200 transition-colors duration-200">
                            مشکوک الوصول ({{ $suspiciousCount }})
                        </button>
                    </div>
                </div>

                <div class="space-y-3 sm:space-y-4" id="facility-list">
                    @foreach($facilities as $facility)
                    <div class="facility-item border border-gray-200 rounded-lg p-4 sm:p-6 hover:shadow-md transition-all duration-200 
                        {{ $facility['has_past_due'] ? 'border-red-300 bg-red-50' : ($facility['has_deferred'] ? 'border-amber-300 bg-amber-50' : ($facility['has_suspicious'] ? 'border-zinc-300 bg-zinc-50' : 'bg-sky-50')) }}"
                        data-status="{{ $facility['is_active'] ? 'active' : 'inactive' }}"
                        data-overdue="{{ $facility['has_past_due'] ? 'true' : 'false' }}"
                        data-suspicious="{{ $facility['has_suspicious'] ? 'true' : 'false' }}">
                        
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4 gap-3">
                            <div class="flex items-center gap-3">
                                @if(!empty($facility['bank_logo']))
                                <div class="flex-shrink-0">
                                    <img src="{{ $facility['bank_logo'] }}" alt="{{ $facility['bank_name'] }}" class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg object-contain bg-white border border-gray-200 p-1">
                                </div>
                                @endif
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900">{{ $facility['bank_name'] }}</h3>
                                    <p class="text-sm text-gray-600">{{ $facility['facility_type_name'] }}</p>
                                    @if(!empty($facility['branch_name']))
                                    <p class="text-xs text-gray-500 mt-1">{{ $facility['branch_name'] }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-left">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium
                                    {{ $facility['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-gray-800' }}">
                                    {{ $facility['facility_status'] }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4">
                            <div>
                                <div class="text-sm text-gray-600 mb-1">مبلغ اصلی</div>
                                <div class="font-semibold text-sm sm:text-base">{{ $facility['formatted_original_amount'] }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">مبلغ بدهی</div>
                                <div class="font-semibold text-sm sm:text-base">{{ $facility['formatted_debt_total_amount'] }}</div>
                            </div>
                            @if($facility['has_past_due'])
                            <div>
                                <div class="text-sm text-red-600 mb-1">سررسید گذشته</div>
                                <div class="font-semibold text-red-700 text-sm sm:text-base">{{ $facility['formatted_past_expired_amount'] }}</div>
                            </div>
                            @endif
                            @if($facility['has_deferred'])
                            <div>
                                <div class="text-sm text-amber-600 mb-1">معوق</div>
                                <div class="font-semibold text-amber-700 text-sm sm:text-base">{{ $facility['formatted_deferred_amount'] }}</div>
                            </div>
                            @endif
                            @if($facility['has_suspicious'])
                            <div>
                                <div class="text-sm text-zinc-600 mb-1">مشکوک الوصول</div>
                                <div class="font-semibold text-zinc-700 text-sm sm:text-base">{{ $facility['formatted_suspicious_amount'] }}</div>
                            </div>
                            @endif
                            @if(intval($facility['benefit_amount']) > 0)
                            <div>
                                <div class="text-sm text-gray-600 mb-1">سود</div>
                                <div class="font-semibold text-sm sm:text-base">{{ $facility['formatted_benefit_amount'] }}</div>
                            </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 text-sm">
                            @if(!empty($facility['request_number']))
                            <div>
                                <span class="text-gray-600">شماره درخواست:</span>
                                <span class="font-medium">{{ $facility['request_number'] }}</span>
                            </div>
                            @endif
                            @if(!empty($facility['set_date']))
                            <div>
                                <span class="text-gray-600">تاریخ تنظیم:</span>
                                <span class="font-medium">{{ $facility['set_date'] }}</span>
                            </div>
                            @endif
                            @if(!empty($facility['end_date']))
                            <div>
                                <span class="text-gray-600">تاریخ سررسید:</span>
                                <span class="font-medium">{{ $facility['end_date'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        @elseif(isset($data['status']) && $data['status'] === 'no_facilities')
        <!-- No Facilities Found -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">هیچ تسهیلاتی یافت نشد</h2>
            <p class="text-gray-600 mb-6">{{ $data['data']['message'] ?? 'برای این کد ملی هیچ تسهیلات فعالی در سیستم بانکی ثبت نشده است.' }}</p>
            
            @if(!empty($data['data']['national_code']))
            <div class="bg-sky-50 rounded-lg p-4 max-w-md mx-auto">
                <div class="text-sm text-sky-600 mb-1">کد ملی استعلام شده</div>
                <div class="font-semibold">{{ $data['data']['national_code'] }}</div>
                @if(!empty($data['data']['customer_name']))
                <div class="text-sm text-sky-600 mb-1 mt-2">نام</div>
                <div class="font-semibold">{{ $data['data']['customer_name'] }}</div>
                @endif
            </div>
            @endif
        </div>

        @else
        <!-- Error State -->
        <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6 sm:p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">خطا در نمایش اطلاعات</h2>
            <p class="text-gray-600">متأسفانه نتوانستیم اطلاعات را به درستی نمایش دهیم. لطفاً مجدداً تلاش کنید.</p>
        </div>
        @endif

        <!-- Back Button -->
        <div class="text-center mt-6 sm:mt-8">
            <a href="{{ route('services.show', 'loan-inquiry') }}" class="inline-flex items-center gap-2 bg-zinc-600 hover:bg-zinc-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg transition-colors duration-200">
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
    let text = 'نتایج استعلام تسهیلات بانکی\n';
    text += '================================\n\n';
    
    if (data.national_code) {
        text += `کد ملی: ${data.national_code}\n`;
    }
    if (data.customer_name) {
        text += `نام: ${data.customer_name}\n`;
    }
    
    if (data.facility_summary) {
        text += '\nخلاصه تسهیلات:\n';
        text += `کل مبلغ تسهیلات: ${data.facility_summary.formatted_total_amount}\n`;
        text += `کل بدهی فعلی: ${data.facility_summary.formatted_debt_total_amount}\n`;
        text += `مبلغ سررسید گذشته: ${data.facility_summary.formatted_past_expired_total_amount}\n`;
        text += `مبلغ معوق: ${data.facility_summary.formatted_deferred_total_amount}\n`;
        text += `مبلغ مشکوک الوصول: ${data.facility_summary.formatted_suspicious_total_amount}\n`;
    }
    
    if (data.facility_list && data.facility_list.length > 0) {
        text += '\nفهرست تسهیلات:\n';
        data.facility_list.forEach((facility, index) => {
            text += `${index + 1}. ${facility.bank_name} - ${facility.facility_type_name}\n`;
            text += `   مبلغ اصلی: ${facility.formatted_original_amount}\n`;
            text += `   بدهی فعلی: ${facility.formatted_debt_total_amount}\n`;
            text += `   وضعیت: ${facility.facility_status}\n`;
            if (facility.set_date) {
                text += `   تاریخ تنظیم: ${facility.set_date}\n`;
            }
            if (facility.end_date) {
                text += `   تاریخ سررسید: ${facility.end_date}\n`;
            }
            text += '\n';
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
    const nationalId = data.national_code ? data.national_code : 'نامشخص';
    const text = `استعلام تسهیلات بانکی - کد ملی: ${nationalId}`;
    
    if (navigator.share) {
        try {
            await navigator.share({
                title: 'نتایج استعلام تسهیلات بانکی',
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

// PDF Download functionality
async function downloadPDF() {
    try {
        showToast('در حال تولید PDF...', 'info');
        
        const { jsPDF } = window.jspdf;
        const element = document.getElementById('pdf-content');
        
        // Temporarily modify styles for better PDF output
        element.style.backgroundColor = 'white';
        element.style.color = 'black';
        
        const canvas = await html2canvas(element, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false
        });
        
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');
        
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
        
        const data = @json($data['data'] ?? []);
        const nationalId = data.national_code ? data.national_code : 'unknown';
        const fileName = `تسهیلات-بانکی-${nationalId}-${new Date().toISOString().split('T')[0]}.pdf`;
        
        pdf.save(fileName);
        showToast('PDF با موفقیت دانلود شد', 'success');
        
        // Restore original styles
        element.style.backgroundColor = '';
        element.style.color = '';
        
    } catch (error) {
        console.error('Error generating PDF:', error);
        showToast('خطا در تولید PDF', 'error');
    }
}

// Filter facilities
function filterFacilities(filter) {
    const facilities = document.querySelectorAll('.facility-item');
    const buttons = document.querySelectorAll('.filter-btn');
    
    // Update active button
    buttons.forEach(btn => btn.classList.remove('active', 'bg-sky-100', 'text-sky-800'));
    event.target.classList.add('active', 'bg-sky-100', 'text-sky-800');
    event.target.classList.remove('bg-sky-100', 'text-gray-700');
    
    facilities.forEach(facility => {
        let show = false;
        
        switch(filter) {
            case 'all':
                show = true;
                break;
            case 'active':
                show = facility.dataset.status === 'active';
                break;
            case 'overdue':
                show = facility.dataset.overdue === 'true';
                break;
            case 'suspicious':
                show = facility.dataset.suspicious === 'true';
                break;
        }
        
        facility.style.display = show ? 'block' : 'none';
    });
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
    
    const colors = {
        success: 'bg-emerald-500 text-white',
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
@endsection 