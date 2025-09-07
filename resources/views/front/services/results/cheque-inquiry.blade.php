@extends('front.layouts.app')

@section('title', 'نتیجه استعلام چک های برگشتی')

@section('head')
    <script src="{{ asset('js/jspdf.min.js') }}"></script>
    <script src="{{ asset('js/html2canvas.min.js') }}"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; }
            .bg-gradient-to-br { background: white !important; }
        }
        .pdf-content {
            background: white;
            color: black;
        }
    </style>
@endsection

@section('content')
<div class="min-h-screen/2 py-6 sm:py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-lg border border-sky-100 p-6 sm:p-8 mb-6 no-print">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-sky-800 mb-2">
                        نتیجه استعلام چک های برگشتی
                    </h1>
                    <p class="text-sky-600 text-sm sm:text-base">
                        اطلاعات تمامی چک های برگشتی شما از تمام بانک ها
                    </p>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <button onclick="downloadPDF()" class="flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        دانلود PDF
                    </button>
                    <button onclick="copyResults()" class="flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        کپی
                    </button>
                    <button onclick="shareResults()" class="flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        اشتراک
                    </button>
                    <button onclick="window.print()" class="flex items-center justify-center gap-2 bg-zinc-500 hover:bg-zinc-600 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        چاپ
                    </button>
                </div>
            </div>
        </div>

        <!-- PDF Content Container -->
        <div id="pdf-content" class="pdf-content">
            @php
                function formatCurrency($amount) {
                    return number_format($amount) . ' تومان';
                }
                
                function formatPersianDate($date) {
                    if (empty($date) || strlen($date) !== 8) {
                        return $date;
                    }
                    $year = substr($date, 0, 4);
                    $month = substr($date, 4, 2);
                    $day = substr($date, 6, 2);
                    return "{$year}/{$month}/{$day}";
                }

                $userInfo = $data['user_info'] ?? [];
                $cheques = $data['cheques'] ?? [];
                $summary = $data['summary'] ?? [];
                $hasValidData = in_array($data['status'], ['success', 'no_cheques']);
            @endphp

            @if($hasValidData)
                <!-- User Information Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-sky-100 p-6 sm:p-8 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-sky-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold text-sky-800 mb-1">اطلاعات کاربر</h2>
                            <p class="text-sky-600 text-sm sm:text-base">مشخصات صاحب چک های برگشتی</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        @if($userInfo['name'])
                        <div class="bg-sky-50 rounded-xl p-4">
                            <div class="text-sm text-sky-600 mb-1">نام و نام خانوادگی</div>
                            <div class="font-semibold text-sky-800 text-lg">{{ $userInfo['name'] ?? 'نامشخص' }}</div>
                        </div>
                        @endif
                        <div class="bg-amber-50 rounded-xl p-4">
                            <div class="text-sm text-amber-600 mb-1">کد ملی</div>
                            <div class="font-mono font-semibold text-amber-800 text-lg">{{ $userInfo['national_id'] ?? 'نامشخص' }}</div>
                        </div>
                        @if($userInfo['legal_id'])
                        <div class="bg-zinc-50 rounded-xl p-4">
                            <div class="text-sm text-zinc-600 mb-1">شناسه حقوقی</div>
                            <div class="font-mono font-semibold text-zinc-800 text-lg">{{ $userInfo['legal_id'] }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-lg border border-red-100 p-4 sm:p-6">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <div class="text-2xl sm:text-3xl font-bold text-red-700">{{ $summary['total_cheques'] ?? 0 }}</div>
                                <div class="text-red-600 text-sm font-medium">تعداد چک برگشتی</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg border border-orange-100 p-4 sm:p-6">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <div class="text-lg sm:text-xl font-bold text-orange-700">{{ $summary['total_amount']['formatted'] ?? '0 تومان' }}</div>
                                <div class="text-orange-600 text-sm font-medium">مجموع مبلغ</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg border border-sky-100 p-4 sm:p-6">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-sky-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <div class="text-2xl sm:text-3xl font-bold text-sky-700">{{ $summary['banks_count'] ?? 0 }}</div>
                                <div class="text-sky-600 text-sm font-medium">تعداد بانک ها</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(count($cheques) > 0)
                    <!-- Filter Section -->
                    <div class="bg-white rounded-xl shadow-lg border border-sky-100 p-4 sm:p-6 mb-6 no-print">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                <span class="font-medium text-sky-800">فیلترها:</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button onclick="filterCheques('all')" class="filter-btn active px-3 py-1 rounded-full text-sm font-medium transition-all duration-200 bg-sky-100 text-sky-700 border border-sky-200">
                                    همه ({{ count($cheques) }})
                                </button>
                                <button onclick="filterCheques('high-amount')" class="filter-btn px-3 py-1 rounded-full text-sm font-medium transition-all duration-200 bg-sky-100 text-gray-600 border border-gray-200 hover:bg-red-100 hover:text-red-700">
                                    مبلغ بالا (>50م)
                                </button>
                                <button onclick="filterCheques('recent')" class="filter-btn px-3 py-1 rounded-full text-sm font-medium transition-all duration-200 bg-sky-100 text-gray-600 border border-gray-200 hover:bg-orange-100 hover:text-orange-700">
                                    اخیر (سال جاری)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cheques List -->
                    <div class="space-y-4 sm:space-y-6">
                        @foreach($cheques as $index => $cheque)
                        <div class="cheque-item bg-white rounded-2xl shadow-lg border border-red-100 p-4 sm:p-6 transition-all duration-300 hover:shadow-xl" 
                             data-amount="{{ $cheque['amount'] }}" 
                             data-year="{{ substr($cheque['raw_back_date'], 0, 4) }}">
                            
                            <div class="flex flex-col lg:flex-row lg:items-center gap-4 sm:gap-6">
                                <!-- Bank Logo -->
                                @if(!empty($cheque['bank_logo']))
                                <div class="flex-shrink-0">
                                    <img src="{{ $cheque['bank_logo'] }}" alt="{{ $cheque['bank_name'] }}" 
                                         class="w-12 h-12 sm:w-16 sm:h-16 rounded-xl object-contain bg-white border border-gray-200 p-2">
                                </div>
                                @endif

                                <!-- Main Info -->
                                <div class="flex-1 space-y-3 sm:space-y-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <h3 class="text-lg sm:text-xl font-bold text-red-800">
                                            چک شماره {{ $cheque['cheque_number'] }}
                                        </h3>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                برگشتی
                                            </span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                                        <div class="bg-red-50 rounded-lg p-3">
                                            <div class="text-xs text-red-600 mb-1">مبلغ چک</div>
                                            <div class="font-bold text-red-800 text-sm sm:text-base">{{ $cheque['formatted_amount'] }}</div>
                                        </div>
                                        <div class="bg-orange-50 rounded-lg p-3">
                                            <div class="text-xs text-orange-600 mb-1">تاریخ برگشت</div>
                                            <div class="font-mono font-medium text-orange-800 text-sm">{{ $cheque['back_date'] }}</div>
                                        </div>
                                        <div class="bg-sky-50 rounded-lg p-3">
                                            <div class="text-xs text-sky-600 mb-1">بانک</div>
                                            <div class="font-medium text-sky-800 text-sm">{{ $cheque['bank_name'] }}</div>
                                        </div>
                                        <div class="bg-sky-50 rounded-lg p-3">
                                            <div class="text-xs text-gray-600 mb-1">شماره حساب</div>
                                            <div class="font-mono font-medium text-gray-800 text-sm">{{ $cheque['account_number'] }}</div>
                                        </div>
                                    </div>

                                    <!-- Additional Details -->
                                    <div class="border-t border-gray-100 pt-3">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">شعبه:</span>
                                                <span class="font-medium text-gray-800">{{ $cheque['branch_description'] }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">کد شعبه:</span>
                                                <span class="font-mono text-gray-800">{{ $cheque['branch_code'] }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">تاریخ وصول:</span>
                                                <span class="font-mono text-gray-800">{{ $cheque['date'] }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">شناسه چک:</span>
                                                <span class="font-mono text-gray-800">{{ $cheque['cheque_id'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <!-- No Cheques Found -->
                    <div class="bg-white rounded-2xl shadow-lg border border-emerald-100 p-8 sm:p-12 text-center">
                        <div class="w-16 h-16 sm:w-24 sm:h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                            <svg class="w-8 h-8 sm:w-12 sm:h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-emerald-800 mb-2">تبریک! 🎉</h3>
                        <p class="text-emerald-600 text-sm sm:text-base mb-4">شما هیچ چک برگشتی ندارید</p>
                        <p class="text-gray-600 text-sm">سابقه مالی شما کاملاً پاک است و چک برگشتی در تمام بانک ها وجود ندارد.</p>
                    </div>
                @endif
            @else
                <!-- Error State -->
                <div class="bg-white rounded-2xl shadow-lg border border-red-100 p-8 sm:p-12 text-center">
                    <div class="w-16 h-16 sm:w-24 sm:h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                        <svg class="w-8 h-8 sm:w-12 sm:h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-red-800 mb-2">خطا در دریافت اطلاعات</h3>
                    <p class="text-red-600 text-sm sm:text-base mb-4">متأسفانه قادر به دریافت اطلاعات چک های برگشتی نیستیم</p>
                    <p class="text-gray-600 text-sm">لطفاً مجدداً تلاش کنید یا با پشتیبانی تماس بگیرید.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span id="toast-message">عملیات با موفقیت انجام شد</span>
    </div>
</div>

<script>
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    
    const colors = {
        success: 'bg-emerald-500',
        error: 'bg-red-500',
        info: 'bg-sky-500'
    };
    
    toast.className = `fixed top-4 right-4 text-white px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 z-50 ${colors[type]}`;
    toastMessage.textContent = message;
    
    toast.style.transform = 'translateX(0)';
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
    }, 3000);
}

function copyResults() {
    const userInfo = @json($userInfo ?? []);
    const summary = @json($summary ?? []);
    const cheques = @json($cheques ?? []);
    
    let text = '📋 نتیجه استعلام چک های برگشتی\n\n';
    text += `👤 نام: ${userInfo.name || 'نامشخص'}\n`;
    text += `🆔 کد ملی: ${userInfo.national_id || 'نامشخص'}\n\n`;
    text += `📊 خلاصه:\n`;
    text += `• تعداد چک برگشتی: ${summary.total_cheques || 0}\n`;
    text += `• مجموع مبلغ: ${summary.formatted_total_amount || '0 تومان'}\n`;
    text += `• تعداد بانک ها: ${summary.banks_count || 0}\n\n`;
    
    if (cheques && cheques.length > 0) {
        text += '📋 جزئیات چک ها:\n';
        cheques.forEach((cheque, index) => {
            text += `${index + 1}. چک ${cheque.cheque_number}\n`;
            text += `   • مبلغ: ${cheque.formatted_amount}\n`;
            text += `   • تاریخ برگشت: ${cheque.back_date}\n`;
            text += `   • بانک: ${cheque.bank_name}\n\n`;
        });
    } else {
        text += '✅ هیچ چک برگشتی یافت نشد\n';
    }
    
    navigator.clipboard.writeText(text).then(() => {
        showToast('اطلاعات کپی شد', 'success');
    }).catch(() => {
        showToast('خطا در کپی کردن', 'error');
    });
}

function shareResults() {
    const userInfo = @json($userInfo ?? []);
    const summary = @json($summary ?? []);
    
    const text = `استعلام چک های برگشتی ${userInfo.name || 'نامشخص'}: ${summary.total_cheques || 0} چک برگشتی با مجموع ${summary.formatted_total_amount || '0 تومان'}`;
    
    if (navigator.share) {
        navigator.share({
            title: 'نتیجه استعلام چک های برگشتی',
            text: text,
            url: window.location.href
        });
    } else {
        copyResults();
        showToast('اطلاعات کپی شد', 'info');
    }
}

function filterCheques(filter) {
    const items = document.querySelectorAll('.cheque-item');
    const buttons = document.querySelectorAll('.filter-btn');
    
    // Update active button
    buttons.forEach(btn => btn.classList.remove('active', 'bg-sky-100', 'text-sky-700'));
    event.target.classList.add('active', 'bg-sky-100', 'text-sky-700');
    
    items.forEach(item => {
        let show = true;
        
        if (filter === 'high-amount') {
            const amount = parseInt(item.dataset.amount);
            show = amount > 50000000; // 50 million tomans
        } else if (filter === 'recent') {
            const year = parseInt(item.dataset.year);
            const currentYear = 1403; // Current Persian year
            show = year >= currentYear;
        }
        
        if (show) {
            item.style.display = 'block';
            item.style.opacity = '1';
        } else {
            item.style.display = 'none';
            item.style.opacity = '0';
        }
    });
}

function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const element = document.getElementById('pdf-content');
    
    showToast('در حال تولید PDF...', 'info');
    
    html2canvas(element, {
        scale: 2,
        useCORS: true,
        allowTaint: true
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');
        
        const imgWidth = 190;
        const pageHeight = 295;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;
        let position = 10;
        
        pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
        
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight + 10;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }
        
        const userInfo = @json($userInfo ?? []);
        const fileName = `back-cheques-inquiry-${userInfo.national_id || 'unknown'}-${Date.now()}.pdf`;
        pdf.save(fileName);
        
        showToast('PDF با موفقیت دانلود شد', 'success');
    }).catch(error => {
        console.error('Error generating PDF:', error);
        showToast('خطا در تولید PDF', 'error');
    });
}
</script>
@endsection 