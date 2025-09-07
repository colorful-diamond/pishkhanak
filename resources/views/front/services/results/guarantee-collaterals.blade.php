@extends('front.layouts.app')

@section('title', 'نتیجه استعلام وثایق ضمانت‌نامه')

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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">استعلام وثایق ضمانت‌نامه</h1>
            <p class="text-gray-600 text-sm sm:text-base">لیست وثایق و تضمینات برای ضمانت‌نامه مورد نظر</p>
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
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        <span class="hidden sm:inline">کپی اطلاعات</span><span class="sm:hidden">کپی</span>
                    </button>
                    <button onclick="shareResults()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path></svg>
                        <span class="hidden sm:inline">اشتراک‌گذاری</span><span class="sm:hidden">اشتراک</span>
                    </button>
                    <button onclick="printResults()" class="inline-flex items-center gap-2 bg-zinc-600 hover:bg-zinc-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span class="hidden sm:inline">چاپ گزارش</span><span class="sm:hidden">چاپ</span>
                    </button>
                    <button onclick="downloadPDF()" class="inline-flex items-center gap-2 bg-yellow-600 hover:bg-yellow-700 text-white px-3 sm:px-4 py-2 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="hidden sm:inline">دانلود PDF</span><span class="sm:hidden">PDF</span>
                    </button>
                </div>
            </div>

            <!-- Input Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                     <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    اطلاعات استعلام
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-sky-50 rounded-lg p-4">
                        <div class="text-sm text-sky-600 mb-1">کد ضمانت‌نامه</div>
                        <div class="font-semibold text-gray-900">{{ $result['input_info']['guarantee_id'] ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            @if(isset($result['summary']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 00-2 2v6a2 2 0 00-2 2z"></path></svg>
                    خلاصه وثایق
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    <div class="bg-sky-50 rounded-lg p-4 border border-sky-200">
                        <div class="text-sm text-sky-600 mb-1">تعداد کل وثایق</div>
                        <div class="text-lg sm:text-xl font-bold text-sky-900">{{ $result['summary']['total_collaterals'] ?? '۰' }}</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                        <div class="text-sm text-yellow-600 mb-1">کل مبلغ ارزیابی شده</div>
                        <div class="text-lg sm:text-xl font-bold text-yellow-900">{{ $result['summary']['total_evaluated_amount']['formatted'] ?? '۰ تومان' }}</div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                        <div class="text-sm text-red-600 mb-1">کل مبلغ بدهی</div>
                        <div class="text-lg sm:text-xl font-bold text-red-900">{{ $result['summary']['total_debt_amount']['formatted'] ?? '۰ تومان' }}</div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Collaterals List -->
            @if(isset($result['collaterals']) && count($result['collaterals']) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    فهرست وثایق ({{ count($result['collaterals']) }} مورد)
                </h2>
                <div class="space-y-3 sm:space-y-4">
                    @foreach($result['collaterals'] as $index => $collateral)
                    <div class="border border-gray-200 rounded-lg p-4 sm:p-6 bg-sky-50 hover:shadow-md transition-all duration-200">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4 gap-3">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900">وثیقه {{ $index + 1 }}</h3>
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-sky-100 text-sky-800">
                                {{ $collateral['collateral_type_description'] ?? 'نامشخص' }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                            <div><div class="text-sm text-gray-600 mb-1">شناسه وثیقه</div><div class="font-semibold text-sm sm:text-base">{{ $collateral['collateral_id'] ?? '-' }}</div></div>
                            <div><div class="text-sm text-gray-600 mb-1">مبلغ ارزیابی شده</div><div class="font-semibold text-sm sm:text-base">{{ $collateral['evaluated_amount_formatted'] ?? '-' }}</div></div>
                            <div><div class="text-sm text-gray-600 mb-1">مبلغ بدهی</div><div class="font-semibold text-sm sm:text-base">{{ $collateral['debt_amount_formatted'] ?? '-' }}</div></div>
                            <div><div class="text-sm text-gray-600 mb-1">نرخ بهره</div><div class="font-semibold text-sm sm:text-base">{{ $collateral['interest_rate_formatted'] ?? '-' }}</div></div>
                            <div><div class="text-sm text-gray-600 mb-1">تاریخ دریافت</div><div class="font-semibold text-sm sm:text-base">{{ $collateral['receive_date_formatted'] ?? '-' }}</div></div>
                            <div><div class="text-sm text-gray-600 mb-1">تاریخ صدور</div><div class="font-semibold text-sm sm:text-base">{{ $collateral['issue_date_formatted'] ?? '-' }}</div></div>
                            @if(isset($collateral['assign_date']))
                            <div><div class="text-sm text-gray-600 mb-1">تاریخ واگذاری</div><div class="font-semibold text-sm sm:text-base">{{ $collateral['assign_date_formatted'] ?? '-' }}</div></div>
                            @endif
                            <div><div class="text-sm text-gray-600 mb-1">کد نوع وثیقه</div><div class="font-semibold text-sm sm:text-base">{{ $collateral['collateral_type_code'] ?? '-' }}</div></div>
                            <div><div class="text-sm text-gray-600 mb-1">کد نوع وثیقه (بانک مرکزی)</div><div class="font-semibold text-sm sm:text-base">{{ $collateral['central_bank_collateral_type_code'] ?? '-' }}</div></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        @elseif(isset($data['status']) && $data['status'] === 'no_results')
        <!-- No Collaterals Found -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">وثیقه‌ای یافت نشد</h2>
            <p class="text-gray-600 mb-6">{{ $data['message'] ?? 'برای این ضمانت‌نامه هیچ وثیقه‌ای ثبت نشده است.' }}</p>
            
            @if(!empty($data['data']['input_info']['guarantee_id']))
            <div class="bg-sky-50 rounded-lg p-4 max-w-md mx-auto">
                <div class="text-sm text-sky-600 mb-1">کد ضمانت‌نامه استعلام شده</div>
                <div class="font-semibold">{{ $data['data']['input_info']['guarantee_id'] }}</div>
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
            <p class="text-gray-600">متأسفانه نتوانستیم اطلاعات را به درستی نمایش دهیم. لطفاً مجدداً تلاش کنید.</p>
        </div>
        @endif

        <!-- Back Button -->
        <div class="text-center mt-6 sm:mt-8">
            <a href="{{ route('services.show', 'guarantee-collaterals') }}" class="inline-flex items-center gap-2 bg-zinc-600 hover:bg-zinc-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                بازگشت به صفحه استعلام
            </a>
        </div>
    </div>
</div>

<script>
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
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => { toast.classList.remove('translate-x-full'); }, 100);
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => { if (toast.parentElement) { toast.remove(); } }, 300);
    }, 5000);
}

// Copy functionality
function copyResults() {
    const data = @json($result ?? null);
    if (!data || !data.collaterals || data.collaterals.length === 0) {
        showToast('اطلاعاتی برای کپی وجود ندارد', 'warning');
        return;
    }

    let text = 'نتیجه استعلام وثایق ضمانت‌نامه\n';
    text += '================================\n\n';
    
    if (data.input_info) {
        text += `کد ضمانت‌نامه: ${data.input_info.guarantee_id || '-'}\n\n`;
    }
    
    if (data.summary) {
        text += 'خلاصه وثایق:\n';
        text += `تعداد کل: ${data.summary.total_collaterals || '۰'}\n`;
        text += `کل مبلغ ارزیابی شده: ${data.summary.total_evaluated_amount.formatted || '-'}\n`;
        text += `کل مبلغ بدهی: ${data.summary.total_debt_amount.formatted || '-'}\n\n`;
    }

    text += 'لیست وثایق:\n';
    data.collaterals.forEach((c, index) => {
        text += `${index + 1}. ${c.collateral_type_description || 'وثیقه'}\n`;
        text += `   شناسه: ${c.collateral_id || '-'}\n`;
        text += `   مبلغ ارزیابی: ${c.evaluated_amount_formatted || '-'}\n`;
        text += `   مبلغ بدهی: ${c.debt_amount_formatted || '-'}\n`;
        text += `   تاریخ دریافت: ${c.receive_date_formatted || '-'}\n\n`;
    });

    navigator.clipboard.writeText(text).then(() => {
        showToast('اطلاعات با موفقیت کپی شد', 'success');
    }).catch(() => {
        showToast('خطا در کپی اطلاعات', 'error');
    });
}

// Share functionality
async function shareResults() {
    const data = @json($result['input_info'] ?? null);
    const guaranteeId = data ? data.guarantee_id : 'نامشخص';
    const text = `نتیجه استعلام وثایق ضمانت‌نامه - کد: ${guaranteeId}`;
    
    if (navigator.share) {
        try {
            await navigator.share({
                title: 'وثایق ضمانت‌نامه',
                text: text,
                url: window.location.href
            });
        } catch (err) {
            console.log('Error sharing:', err);
        }
    } else {
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
        
        element.style.backgroundColor = 'white';
        element.style.color = 'black';
        
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
        
        const data = @json($result['input_info'] ?? null);
        const guaranteeId = data ? data.guarantee_id : 'unknown';
        const fileName = `وثایق-ضمانت‌نامه-${guaranteeId}-${new Date().toISOString().split('T')[0]}.pdf`;
        
        pdf.save(fileName);
        showToast('PDF با موفقیت دانلود شد', 'success');
        
        element.style.backgroundColor = '';
        element.style.color = '';
        
    } catch (error) {
        console.error('Error generating PDF:', error);
        showToast('خطا در تولید PDF', 'error');
    }
}
</script>
@endsection
