@extends('front.layouts.app')

@section('title', 'نتیجه استعلام رنگ چک')

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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">استعلام وضعیت اعتباری چک</h1>
            <p class="text-gray-600 text-sm sm:text-base">گزارش وضعیت اعتباری بر اساس رنگ‌بندی چک</p>
        </div>

        @if(isset($data['status']) && $data['status'] === 'success')
        @php
            $result = $data;
            $userInfo = $result['user_info'] ?? [];
            $chequeColor = $result['cheque_color'] ?? [];
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

            <!-- Customer Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    اطلاعات استعلام
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-sky-50 rounded-lg p-4">
                        <div class="text-sm text-sky-600 mb-1">کد ملی / شناسه ملی</div>
                        <div class="font-semibold text-gray-900">{{ $userInfo['national_id'] ?? '-' }}</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <div class="text-sm text-yellow-600 mb-1">کد پیگیری</div>
                        <div class="font-semibold text-gray-900">{{ $userInfo['track_id'] ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Main Result Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-6 text-center">نتیجه وضعیت اعتباری</h2>
                <div class="flex flex-col md:flex-row items-center justify-center gap-6 md:gap-10">
                    <div class="flex-shrink-0">
                        <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg flex items-center justify-center text-5xl {{ $chequeColor['color_class'] ?? 'bg-sky-100' }}">
                            {{ $chequeColor['icon'] ?? '❓' }}
                        </div>
                    </div>
                    <div class="text-center md:text-right">
                        <div class="text-sm text-gray-500 mb-1">وضعیت شما</div>
                        <h3 class="text-3xl font-bold {{ $chequeColor['text_color_class'] ?? 'text-gray-900' }} mb-2">
                            رنگ {{ $chequeColor['name'] ?? 'نامشخص' }}
                        </h3>
                        <p class="text-gray-600 mb-4">{{ $chequeColor['risk_level_name'] ?? 'سطح ریسک نامشخص' }}</p>
                        <div class="bg-sky-50 rounded-lg p-4">
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $chequeColor['description'] ?? 'توضیحات موجود نیست.' }}</p>
                            @if(!empty($chequeColor['recommendation']))
                            <p class="text-sm text-sky-700 mt-3 pt-3 border-t border-gray-200">{{ $chequeColor['recommendation'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Color Guide -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 text-center">راهنمای رنگ‌بندی</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <div class="text-center p-3 rounded-lg bg-sky-50 border"><div class="w-6 h-6 rounded-full mx-auto mb-2 bg-sky-200"></div><span class="text-sm font-medium">سفید</span><p class="text-xs text-gray-500 mt-1">بدون برگشتی</p></div>
                    <div class="text-center p-3 rounded-lg bg-yellow-50 border"><div class="w-6 h-6 rounded-full mx-auto mb-2 bg-yellow-300"></div><span class="text-sm font-medium">زرد</span><p class="text-xs text-gray-500 mt-1">۱ چک / تا ۵م</p></div>
                    <div class="text-center p-3 rounded-lg bg-orange-50 border"><div class="w-6 h-6 rounded-full mx-auto mb-2 bg-orange-400"></div><span class="text-sm font-medium">نارنجی</span><p class="text-xs text-gray-500 mt-1">۲-۴ چک / تا ۲۰م</p></div>
                    <div class="text-center p-3 rounded-lg bg-amber-50 border"><div class="w-6 h-6 rounded-full mx-auto mb-2 bg-amber-500"></div><span class="text-sm font-medium">قهوه‌ای</span><p class="text-xs text-gray-500 mt-1">۵-۱۰ چک / تا ۵۰م</p></div>
                    <div class="text-center p-3 rounded-lg bg-red-50 border"><div class="w-6 h-6 rounded-full mx-auto mb-2 bg-red-500"></div><span class="text-sm font-medium">قرمز</span><p class="text-xs text-gray-500 mt-1">+۱۰ چک / +۵۰م</p></div>
                </div>
            </div>
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
            <a href="{{ route('services.show', 'cheque-color-inquiry') }}" class="inline-flex items-center gap-2 bg-zinc-600 hover:bg-zinc-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg transition-colors duration-200 no-print">
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
    
    let text = 'نتایج استعلام وضعیت اعتباری چک\n';
    text += '================================\n\n';
    
    if (data.user_info) {
        text += `کد ملی / شناسه ملی: ${data.user_info.national_id || '-'}\n`;
        text += `کد پیگیری: ${data.user_info.track_id || '-'}\n`;
    }
    
    if (data.cheque_color) {
        text += '\nوضعیت اعتباری:\n';
        text += `رنگ: ${data.cheque_color.name}\n`;
        text += `سطح ریسک: ${data.cheque_color.risk_level_name}\n`;
        text += `توضیحات: ${data.cheque_color.description}\n`;
        if (data.cheque_color.recommendation) {
            text += `توصیه: ${data.cheque_color.recommendation}\n`;
        }
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
    if (!data || data.status !== 'success') {
        showToast('اطلاعاتی برای اشتراک‌گذاری وجود ندارد', 'warning');
        return;
    }
    
    const text = `نتیجه استعلام وضعیت اعتباری چک: رنگ ${data.cheque_color.name} (${data.cheque_color.risk_level_name})`;
    
    if (navigator.share) {
        try {
            await navigator.share({
                title: 'نتیجه استعلام وضعیت اعتباری چک',
                text: text,
                url: window.location.href
            });
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
        const nationalId = data.user_info ? (data.user_info.national_id || 'unknown') : 'unknown';
        const fileName = `وضعیت-اعتباری-چک-${nationalId}-${new Date().toISOString().split('T')[0]}.pdf`;
        
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