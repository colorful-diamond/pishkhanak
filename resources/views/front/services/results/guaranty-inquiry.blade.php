@extends('front.layouts.app')

@section('title', 'نتیجه استعلام ضمانت ها')

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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l-3-3m0 0l3-3m-3 3h6"></path>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">استعلام تعهدات و ضمانت‌ها</h1>
            <p class="text-gray-600 text-sm sm:text-base">گزارش جامع تعهداتی که شما ضامن آن بوده‌اید</p>
        </div>

        @if(isset($data['status']) && $data['status'] === 'success')
        @php
            $result = $data;
            $guarantorInfo = $result['guarantor_info'] ?? [];
            $guarantees = $result['guarantees'] ?? [];
            $summary = $result['summary'] ?? [];
        @endphp

        <!-- Content for PDF -->
        <div id="pdf-content" class="pdf-content">
            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6 no-print">
                <h2 class="text-lg font-bold text-gray-900 mb-4">عملیات</h2>
                <div class="flex flex-wrap gap-2 sm:gap-3">
                    <button onclick="copyResults()" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        <span class="hidden sm:inline">کپی اطلاعات</span>
                        <span class="sm:hidden">کپی</span>
                    </button>
                    <button onclick="shareResults()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path></svg>
                        <span class="hidden sm:inline">اشتراک‌گذاری</span>
                        <span class="sm:hidden">اشتراک</span>
                    </button>
                    <button onclick="printResults()" class="inline-flex items-center gap-2 bg-zinc-600 hover:bg-zinc-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span class="hidden sm:inline">چاپ گزارش</span>
                        <span class="sm:hidden">چاپ</span>
                    </button>
                    <button onclick="downloadPDF()" class="inline-flex items-center gap-2 bg-yellow-600 hover:bg-yellow-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="hidden sm:inline">دانلود PDF</span>
                        <span class="sm:hidden">PDF</span>
                    </button>
                </div>
            </div>

            <!-- Guarantor Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    اطلاعات ضامن
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-sky-50 rounded-lg p-4">
                        <div class="text-sm text-sky-600 mb-1">کد ملی</div>
                        <div class="font-semibold text-gray-900">{{ $guarantorInfo['national_code'] ?? '-' }}</div>
                    </div>
                    @if(!empty($guarantorInfo['full_name']))
                    <div class="bg-sky-50 rounded-lg p-4">
                        <div class="text-sm text-sky-600 mb-1">نام و نام خانوادگی</div>
                        <div class="font-semibold text-gray-900">{{ $guarantorInfo['full_name'] }}</div>
                    </div>
                    @endif
                    @if(!empty($guarantorInfo['inquiry_result_id']))
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <div class="text-sm text-yellow-600 mb-1">شناسه استعلام</div>
                        <div class="font-semibold text-gray-900">{{ $guarantorInfo['inquiry_result_id'] }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Summary -->
            @if(isset($summary))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 00-2 2v6a2 2 0 00-2 2z"></path></svg>
                    خلاصه تعهدات
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <div class="text-sm text-sky-600 mb-1">تعداد کل ضمانت‌ها</div>
                        <div class="text-lg sm:text-xl font-bold text-sky-900">{{ $summary['total_guarantees'] ?? '0' }}</div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                        <div class="text-sm text-red-600 mb-1">مجموع مبلغ بدهی</div>
                        <div class="text-lg sm:text-xl font-bold text-red-900">{{ $summary['formatted_total_debt_amount'] ?? '0 تومان' }}</div>
                    </div>
                    <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-200">
                        <div class="text-sm text-emerald-600 mb-1">ضمانت‌های فعال</div>
                        <div class="text-lg sm:text-xl font-bold text-emerald-900">{{ $summary['active_guarantees'] ?? '0' }}</div>
                    </div>
                    <div class="bg-sky-50 rounded-lg p-4 border border-gray-200">
                        <div class="text-sm text-gray-600 mb-1">ضمانت‌های تسویه شده</div>
                        <div class="text-lg sm:text-xl font-bold text-gray-900">{{ $summary['settled_guarantees'] ?? '0' }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Guarantees List -->
            @if(isset($guarantees) && count($guarantees) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    فهرست ضمانت‌ها ({{ count($guarantees) }} مورد)
                </h2>

                <div class="space-y-3 sm:space-y-4" id="guarantee-list">
                    @foreach($guarantees as $guarantee)
                    <div class="guarantee-item border rounded-lg p-4 sm:p-6 hover:shadow-md transition-all duration-200 
                        {{ $guarantee['is_active'] ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-sky-50' }}">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4 gap-3">
                            <div class="flex items-center gap-3">
                                @if(!empty($guarantee['bank_logo']))
                                <div class="flex-shrink-0">
                                    <img src="{{ $guarantee['bank_logo'] }}" alt="{{ $guarantee['bank_name'] }}" class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg object-contain bg-white border border-gray-200 p-1">
                                </div>
                                @endif
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900">{{ $guarantee['bank_name'] }}</h3>
                                    <p class="text-sm text-gray-600">وام گیرنده: {{ $guarantee['debtor_full_name'] }}</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium
                                    {{ $guarantee['is_active'] ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $guarantee['status'] }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4">
                            <div><div class="text-sm text-gray-600 mb-1">مبلغ اصلی</div><div class="font-semibold text-sm sm:text-base">{{ $guarantee['formatted_original_amount'] }}</div></div>
                            <div><div class="text-sm text-red-600 mb-1">مانده بدهی</div><div class="font-semibold text-red-700 text-sm sm:text-base">{{ $guarantee['formatted_total_amount'] }}</div></div>
                            <div><div class="text-sm text-gray-600 mb-1">سود</div><div class="font-semibold text-sm sm:text-base">{{ $guarantee['formatted_benefit_amount'] }}</div></div>
                            <div><div class="text-sm text-gray-600 mb-1">درصد ضمانت</div><div class="font-semibold text-sm sm:text-base">{{ $guarantee['guaranty_percent'] }}%</div></div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 text-sm pt-4 border-t border-gray-200">
                            <div><span class="text-gray-600">نوع:</span><span class="font-medium"> {{ $guarantee['request_type_name'] }}</span></div>
                            <div><span class="text-gray-600">تاریخ تنظیم:</span><span class="font-medium"> {{ $guarantee['set_date'] }}</span></div>
                            <div><span class="text-gray-600">تاریخ سررسید:</span><span class="font-medium"> {{ $guarantee['end_date'] }}</span></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        @elseif(isset($data['status']) && $data['status'] === 'no_guarantees')
        <!-- No Guarantees Found -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">تبریک! شما ضامن هیچ تعهدی نیستید.</h2>
            <p class="text-gray-600 mb-6">{{ $data['message'] ?? 'برای این کد ملی هیچ ضمانتی در سیستم بانکی ثبت نشده است.' }}</p>
            
            @if(!empty($data['guarantor_info']['national_code']))
            <div class="bg-sky-50 rounded-lg p-4 max-w-md mx-auto">
                <div class="text-sm text-sky-600 mb-1">کد ملی استعلام شده</div>
                <div class="font-semibold">{{ $data['guarantor_info']['national_code'] }}</div>
            </div>
            @endif
        </div>

        @else
        <!-- Error State -->
        <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6 sm:p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">خطا در نمایش اطلاعات</h2>
            <p class="text-gray-600">{{ $data['message'] ?? 'متأسفانه نتوانستیم اطلاعات را به درستی نمایش دهیم. لطفاً مجدداً تلاش کنید.' }}</p>
        </div>
        @endif

        <!-- Back Button -->
        <div class="text-center mt-6 sm:mt-8">
            <a href="{{ route('services.show', 'guaranty-inquiry') }}" class="inline-flex items-center gap-2 bg-zinc-600 hover:bg-zinc-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg transition-colors duration-200 no-print">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                بازگشت به صفحه استعلام
            </a>
        </div>
    </div>
</div>

<script>
// Copy functionality
function copyResults() {
    const data = @json($data ?? []);
    if (!data || data.status !== 'success') {
        showToast('اطلاعاتی برای کپی وجود ندارد', 'warning');
        return;
    }
    
    let text = 'نتایج استعلام تعهدات و ضمانت‌ها\n';
    text += '================================\n\n';
    
    if (data.guarantor_info) {
        text += `کد ملی ضامن: ${data.guarantor_info.national_code || '-'}\n`;
        text += `نام ضامن: ${data.guarantor_info.full_name || '-'}\n`;
    }
    
    if (data.summary) {
        text += '\nخلاصه تعهدات:\n';
        text += `تعداد کل ضمانت‌ها: ${data.summary.total_guarantees}\n`;
        text += `مجموع مبلغ بدهی: ${data.summary.formatted_total_debt_amount}\n`;
        text += `ضمانت‌های فعال: ${data.summary.active_guarantees}\n`;
    }
    
    if (data.guarantees && data.guarantees.length > 0) {
        text += '\nفهرست ضمانت‌ها:\n';
        data.guarantees.forEach((g, index) => {
            text += `${index + 1}. بانک ${g.bank_name} (وام گیرنده: ${g.debtor_full_name})\n`;
            text += `   مبلغ بدهی: ${g.formatted_total_amount}\n`;
            text += `   وضعیت: ${g.status}\n\n`;
        });
    } else {
         text += '\n✅ شما ضامن هیچ تعهدی نیستید.\n';
    }
    
    navigator.clipboard.writeText(text).then(() => {
        showToast('اطلاعات با موفقیت کپی شد', 'success');
    }).catch(() => {
        showToast('خطا در کپی اطلاعات', 'error');
    });
}

// Share functionality
async function shareResults() {
    const data = @json($data ?? []);
    const guarantorName = data.guarantor_info ? (data.guarantor_info.full_name || 'نامشخص') : 'نامشخص';
    const text = `استعلام تعهدات و ضمانت‌های ${guarantorName}`;
    
    if (navigator.share) {
        try {
            await navigator.share({ title: 'نتایج استعلام ضمانت‌ها', text: text, url: window.location.href });
        } catch (err) { /* Sharing cancelled */ }
    } else {
        const url = window.location.href;
        navigator.clipboard.writeText(`${text}\n${url}`).then(() => {
            showToast('لینک کپی شد، می‌توانید به اشتراک بگذارید', 'success');
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
        
        const canvas = await html2canvas(element, { scale: 2, useCORS: true, allowTaint: true, backgroundColor: '#ffffff', logging: false });
        
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
        
        const data = @json($data ?? []);
        const nationalId = data.guarantor_info ? (data.guarantor_info.national_code || 'unknown') : 'unknown';
        const fileName = `استعلام-ضمانت-${nationalId}-${new Date().toISOString().split('T')[0]}.pdf`;
        
        pdf.save(fileName);
        showToast('PDF با موفقیت دانلود شد', 'success');
        
    } catch (error) {
        console.error('Error generating PDF:', error);
        showToast('خطا در تولید PDF', 'error');
    }
}

function showToast(message, type = 'info') {
    const toastContainer = document.body;
    const toast = document.createElement('div');
    
    const colors = {
        success: 'bg-emerald-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-sky-500 text-white',
        warning: 'bg-yellow-500 text-black'
    };
    
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 transform translate-x-full ${colors[type] || colors.info}`;
    
    toast.innerHTML = `
        <div class="flex items-center justify-between gap-4">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
            </button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(120%)';
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }, 5000);
}
</script>
@endsection 