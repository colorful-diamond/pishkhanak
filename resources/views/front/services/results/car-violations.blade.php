@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">نتیجه استعلام تخلفات خودرو</h1>
                <p class="text-gray-600">پلاک {{ $data['input_data']['plate_number'] ?? 'نامشخص' }} | کد ملی {{ $data['input_data']['national_code'] ?? 'نامشخص' }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    استعلام موفق
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Violations -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-3xl font-bold text-sky-600 mb-2">{{ $data['violation_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">تعداد تخلفات</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Total Amount -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-3xl font-bold text-red-600 mb-2">{{ $data['total_amount_formatted'] ?? number_format(intval(($data['total_amount'] ?? 0) / 10)) . ' تومان' }}</div>
                    <div class="text-sm text-gray-600">مجموع جریمه</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>

                <!-- Payable Status -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    @php
                        $payableCount = collect($data['violations'] ?? [])->where('is_payable', true)->count();
                        $statusColor = $payableCount > 0 ? 'text-orange-600' : 'text-green-600';
                        $statusText = $payableCount > 0 ? 'نیاز به پرداخت' : 'بدون جریمه معوق';
                    @endphp
                    <div class="text-3xl font-bold {{ $statusColor }} mb-2">{{ $payableCount }}</div>
                    <div class="text-sm text-gray-600">قابل پرداخت</div>
                    <div class="mt-2 text-xs {{ $statusColor }} font-medium">{{ $statusText }}</div>
                </div>
            </div>

            <!-- Violations List -->
            @if(!empty($data['violations']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        جزئیات تخلفات
                    </h2>
                    
                    <div class="space-y-4">
                        @foreach($data['violations'] as $index => $violation)
                            <div class="border border-gray-200 rounded-lg p-5 hover:shadow-sm transition-shadow">
                                <!-- Violation Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 mb-1">{{ $violation['type'] ?? 'نوع تخلف نامشخص' }}</h3>
                                        <p class="text-sm text-gray-600">{{ $violation['description'] ?? 'توضیحی ارائه نشده' }}</p>
                                    </div>
                                    <div class="text-left flex flex-col items-end">
                                        <div class="text-lg font-bold text-red-600 mb-1">{{ $violation['price_formatted'] ?? number_format(intval(($violation['price'] ?? 0) / 10)) . ' تومان' }}</div>
                                        @if($violation['is_payable'] ?? false)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                قابل پرداخت
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-sky-100 text-gray-800">
                                                پرداخت شده
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Violation Details Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                    <!-- Date & Time -->
                                    <div class="bg-sky-50 rounded-lg p-3">
                                        <div class="text-gray-600 mb-1 flex items-center">
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            تاریخ
                                        </div>
                                        <div class="font-medium text-gray-900">{{ $violation['date'] ?? 'نامشخص' }}</div>
                                    </div>

                                    <!-- Location -->
                                    <div class="bg-sky-50 rounded-lg p-3">
                                        <div class="text-gray-600 mb-1 flex items-center">
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            محل
                                        </div>
                                        <div class="font-medium text-gray-900">{{ $violation['location'] ?? 'نامشخص' }}</div>
                                        @if(!empty($violation['city']))
                                            <div class="text-xs text-gray-500 mt-1">{{ $violation['city'] }}</div>
                                        @endif
                                    </div>

                                    <!-- Violation Code -->
                                    <div class="bg-sky-50 rounded-lg p-3">
                                        <div class="text-gray-600 mb-1 flex items-center">
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                            </svg>
                                            کد تخلف
                                        </div>
                                        <div class="font-medium text-gray-900 font-mono">{{ $violation['code'] ?? '-' }}</div>
                                    </div>

                                    <!-- Features -->
                                    <div class="bg-sky-50 rounded-lg p-3">
                                        <div class="text-gray-600 mb-1">ویژگی‌ها</div>
                                        <div class="space-y-1">
                                            @if($violation['has_image'] ?? false)
                                                <div class="flex items-center text-xs text-sky-600">
                                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    دارای تصویر
                                                </div>
                                            @endif
                                            @if(!empty($violation['policeman_code']))
                                                <div class="text-xs text-gray-600">
                                                    کد پلیس: {{ $violation['policeman_code'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Information -->
                                @if($violation['is_payable'] && (!empty($violation['bill_id']) || !empty($violation['payment_id'])))
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            @if(!empty($violation['bill_id']))
                                                <div class="bg-sky-50 border border-sky-200 rounded-lg p-3">
                                                    <div class="text-sky-700 font-medium mb-1">شناسه قبض</div>
                                                    <div class="font-mono text-sky-900 text-base cursor-pointer" onclick="copyToClipboard('{{ $violation['bill_id'] }}')" title="کلیک کنید تا کپی شود">
                                                        {{ $violation['bill_id'] }}
                                                    </div>
                                                </div>
                                            @endif
                                            @if(!empty($violation['payment_id']))
                                                <div class="bg-sky-50 border border-sky-200 rounded-lg p-3">
                                                    <div class="text-sky-700 font-medium mb-1">شناسه پرداخت</div>
                                                    <div class="font-mono text-sky-900 text-base cursor-pointer" onclick="copyToClipboard('{{ $violation['payment_id'] }}')" title="کلیک کنید تا کپی شود">
                                                        {{ $violation['payment_id'] }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- No Violations -->
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">تخلفی یافت نشد</h3>
                    <p class="text-gray-600">هیچ تخلف ثبت شده‌ای برای این خودرو موجود نیست.</p>
                </div>
            @endif
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
                    <button onclick="copyAllViolations()" class="w-full bg-sky-100 hover:bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        کپی تمام اطلاعات
                    </button>
                    <button onclick="shareReport()" class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        اشتراک‌گذاری
                    </button>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">آمار سریع</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاریخ استعلام:</span>
                        <span class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">زمان استعلام:</span>
                        <span class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('H:i') }}</span>
                    </div>
                    @if(!empty($data['violations']))
                        <div class="flex justify-between">
                            <span class="text-gray-600">آخرین تخلف:</span>
                            <span class="font-medium">{{ collect($data['violations'])->first()['date'] ?? '-' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">نکات مهم</h3>
                <ul class="text-sm text-yellow-700 space-y-2">
                    <li>• جریمه‌های قابل پرداخت را در اسرع وقت تسویه کنید</li>
                    <li>• برای پرداخت از درگاه‌های معتبر استفاده کنید</li>
                    <li>• رسید پرداخت را نگهداری کنید</li>
                    <li>• در صورت اعتراض به مراجع ذیصلاح مراجعه کنید</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show toast notification
        showToast('کپی شد!', 'success');
    }).catch(() => {
        showToast('خطا در کپی کردن', 'error');
    });
}

function copyAllViolations() {
    const violations = @json($data['violations'] ?? []);
    let text = 'گزارش تخلفات خودرو\n';
    text += '='.repeat(40) + '\n\n';
    text += `پلاک: {{ $data['input_data']['plate_number'] ?? 'نامشخص' }}\n`;
    text += `تاریخ استعلام: {{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d H:i') }}\n\n`;
    
    if (violations.length > 0) {
        violations.forEach((violation, index) => {
            text += `تخلف ${index + 1}:\n`;
            text += `نوع: ${violation.type || 'نامشخص'}\n`;
            text += `مبلغ: ${violation.price_formatted || (violation.price ? Math.floor(violation.price / 10).toLocaleString() + ' تومان' : '0 تومان')}\n`;
            text += `تاریخ: ${violation.date || 'نامشخص'}\n`;
            text += `محل: ${violation.location || 'نامشخص'}\n`;
            if (violation.bill_id) text += `شناسه قبض: ${violation.bill_id}\n`;
            if (violation.payment_id) text += `شناسه پرداخت: ${violation.payment_id}\n`;
            text += '\n';
        });
        text += `مجموع جریمه: {{ $data['total_amount_formatted'] ?? number_format(intval(($data['total_amount'] ?? 0) / 10)) . ' تومان' }}\n`;
    } else {
        text += 'هیچ تخلفی یافت نشد.\n';
    }
    
    text += '\nتولید شده در پیشخوانک (pishkhanak.com)';
    
    copyToClipboard(text);
}

function shareReport() {
    if (navigator.share) {
        navigator.share({
            title: 'گزارش تخلفات خودرو',
            text: 'گزارش تخلفات خودرو من از پیشخوانک',
            url: window.location.href
        });
    } else {
        copyToClipboard(window.location.href);
        showToast('لینک کپی شد!', 'success');
    }
}

function showToast(message, type) {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .bg-sky-50 { background: #f9fafb !important; }
    .bg-sky-50 { background: #eff6ff !important; }
    .bg-yellow-50 { background: #fefce8 !important; }
    .border { border: 1px solid #e5e7eb !important; }
}
</style>
@endsection

