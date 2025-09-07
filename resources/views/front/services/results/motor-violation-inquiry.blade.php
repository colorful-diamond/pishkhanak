@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">نتیجه استعلام تخلفات موتورسیکلت</h1>
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
                <!-- Total Amount -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-3xl font-bold text-red-600 mb-2">{{ $data['violation_data']['amount_formatted'] ?? number_format(intval(($data['violation_data']['Amount'] ?? 0) / 10)) . ' تومان' }}</div>
                    <div class="text-sm text-gray-600">مجموع جریمه</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>

                <!-- Bill Status -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    @php
                        $billId = $data['violation_data']['BillID'] ?? '';
                        $hasViolation = !empty($billId) && $billId !== '0';
                        $statusColor = $hasViolation ? 'text-orange-600' : 'text-green-600';
                        $statusText = $hasViolation ? 'دارای تخلف' : 'بدون تخلف';
                    @endphp
                    <div class="text-3xl font-bold {{ $statusColor }} mb-2">{{ $hasViolation ? '1' : '0' }}</div>
                    <div class="text-sm text-gray-600">وضعیت</div>
                    <div class="mt-2 text-xs {{ $statusColor }} font-medium">{{ $statusText }}</div>
                </div>

                <!-- Complaint Status -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    @php
                        $complaintCode = $data['violation_data']['ComplaintCode'] ?? '0';
                        $complaintStatus = $data['violation_data']['ComplaintStatus'] ?? 'شکایت ندارد';
                        $hasComplaint = $complaintCode !== '0';
                        $complaintColor = $hasComplaint ? 'text-sky-600' : 'text-gray-600';
                    @endphp
                    <div class="text-3xl font-bold {{ $complaintColor }} mb-2">{{ $complaintCode }}</div>
                    <div class="text-sm text-gray-600">شکایت</div>
                    <div class="mt-2 text-xs {{ $complaintColor }} font-medium">{{ $complaintStatus }}</div>
                </div>
            </div>

            <!-- Violation Details -->
            @if($hasViolation)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        جزئیات تخلف موتورسیکلت
                    </h2>
                    
                    <div class="border border-gray-200 rounded-lg p-6">
                        <!-- Violation Header -->
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">تخلف موتورسیکلت</h3>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 ml-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">پلاک {{ $data['violation_data']['PlateNumber'] ?? $data['input_data']['plate_number'] ?? 'نامشخص' }}</span>
                                </div>
                            </div>
                            <div class="text-left">
                                <div class="text-2xl font-bold text-red-600 mb-2">{{ $data['violation_data']['amount_formatted'] ?? number_format(intval(($data['violation_data']['Amount'] ?? 0) / 10)) . ' تومان' }}</div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-2">
                                    قابل پرداخت
                                </span>
                            </div>
                        </div>

                        <!-- Payment Information Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Bill Information -->
                            <div class="bg-sky-50 border border-sky-200 rounded-lg p-4">
                                <h4 class="font-medium text-sky-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    اطلاعات قبض
                                </h4>
                                <div class="space-y-3">
                                    <div>
                                        <div class="text-sky-700 text-sm mb-1">شناسه قبض</div>
                                        <div class="font-mono text-sky-900 text-lg cursor-pointer bg-sky-100 p-2 rounded border" 
                                             onclick="copyToClipboard('{{ $data['violation_data']['BillID'] ?? '' }}')" 
                                             title="کلیک کنید تا کپی شود">
                                            {{ $data['violation_data']['BillID'] ?? 'نامشخص' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sky-700 text-sm mb-1">شناسه پرداخت</div>
                                        <div class="font-mono text-sky-900 text-lg cursor-pointer bg-sky-100 p-2 rounded border" 
                                             onclick="copyToClipboard('{{ $data['violation_data']['PaymentID'] ?? '' }}')" 
                                             title="کلیک کنید تا کپی شود">
                                            {{ $data['violation_data']['PaymentID'] ?? 'نامشخص' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Complaint Information -->
                            <div class="bg-sky-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    اطلاعات شکایت
                                </h4>
                                <div class="space-y-3">
                                    <div>
                                        <div class="text-gray-700 text-sm mb-1">کد شکایت</div>
                                        <div class="font-mono text-gray-900 text-lg">{{ $data['violation_data']['ComplaintCode'] ?? '0' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-700 text-sm mb-1">وضعیت شکایت</div>
                                        <div class="text-gray-900">{{ $data['violation_data']['ComplaintStatus'] ?? 'شکایت ندارد' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Instructions -->
                        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 ml-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <div class="font-medium text-yellow-800 mb-1">نحوه پرداخت جریمه</div>
                                    <div class="text-sm text-yellow-700">
                                        برای پرداخت جریمه موتورسیکلت، از شناسه قبض و شناسه پرداخت در درگاه‌های پرداخت معتبر استفاده کنید.
                                        می‌توانید از طریق اپلیکیشن‌های بانکی، کیوسک‌ها یا دفاتر پست نسبت به پرداخت اقدام کنید.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <button onclick="copyPaymentInfo()" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                کپی اطلاعات پرداخت
                            </button>
                            <button onclick="window.print()" class="flex-1 bg-sky-100 hover:bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                چاپ قبض
                            </button>
                            <button onclick="sharePaymentInfo()" class="flex-1 bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                اشتراک‌گذاری
                            </button>
                        </div>
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
                    <p class="text-gray-600">هیچ تخلف ثبت شده‌ای برای این موتورسیکلت موجود نیست.</p>
                    <div class="mt-6">
                        <div class="inline-flex items-center px-4 py-2 bg-green-50 border border-green-200 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span class="text-green-800 font-medium">رانندگی ایمن ادامه دهید!</span>
                        </div>
                    </div>
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
                    <button onclick="copyAllInfo()" class="w-full bg-sky-100 hover:bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        کپی تمام اطلاعات
                    </button>
                    <button onclick="shareReport()" class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        اشتراک‌گذاری
                    </button>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">اطلاعات استعلام</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاریخ استعلام:</span>
                        <span class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">زمان استعلام:</span>
                        <span class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">پلاک:</span>
                        <span class="font-medium font-mono">{{ $data['input_data']['plate_number'] ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">نوع وسیله:</span>
                        <span class="font-medium">موتورسیکلت</span>
                    </div>
                </div>
            </div>

            <!-- Motorcycle Safety Tips -->
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-orange-800 mb-3">نکات ایمنی موتورسیکلت</h3>
                <ul class="text-sm text-orange-700 space-y-2">
                    <li>• همیشه کلاه ایمنی استفاده کنید</li>
                    <li>• از سرعت مطمئن رانندگی کنید</li>
                    <li>• قوانین راهنمایی را رعایت کنید</li>
                    <li>• بیمه شخص ثالث را تمدید کنید</li>
                    <li>• معاینه فنی را انجام دهید</li>
                </ul>
            </div>

            <!-- Payment Methods -->
            @if($hasViolation)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">روش‌های پرداخت</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-sky-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span>اپلیکیشن‌های بانکی</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span>کیوسک‌های بانکی</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-orange-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>دفاتر پست</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-purple-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                            </svg>
                            <span>درگاه‌های اینترنتی</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
const violationData = @json($data['violation_data'] ?? []);
const inputData = @json($data['input_data'] ?? []);

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('کپی شد!', 'success');
    }).catch(() => {
        showToast('خطا در کپی کردن', 'error');
    });
}

function copyPaymentInfo() {
    const billId = violationData.BillID || '';
    const paymentId = violationData.PaymentID || '';
    const amount = violationData.Amount || 0;
    
    let text = 'اطلاعات پرداخت جریمه موتورسیکلت\n';
    text += '='.repeat(40) + '\n\n';
    text += `پلاک: ${inputData.plate_number || 'نامشخص'}\n`;
    text += `مبلغ جریمه: ${Math.floor(amount / 10).toLocaleString()} تومان\n`;
    text += `شناسه قبض: ${billId}\n`;
    text += `شناسه پرداخت: ${paymentId}\n\n`;
    text += 'تولید شده در پیشخوانک (pishkhanak.com)';
    
    copyToClipboard(text);
}

function copyAllInfo() {
    let text = 'گزارش تخلفات موتورسیکلت\n';
    text += '='.repeat(40) + '\n\n';
    text += `پلاک: ${inputData.plate_number || 'نامشخص'}\n`;
    text += `کد ملی: ${inputData.national_code || 'نامشخص'}\n`;
    text += `تاریخ استعلام: {{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d H:i') }}\n\n`;
    
    if (violationData.BillID && violationData.BillID !== '0') {
        text += 'اطلاعات تخلف:\n';
        text += `مبلغ جریمه: ${Math.floor((violationData.Amount || 0) / 10).toLocaleString()} تومان\n`;
        text += `شناسه قبض: ${violationData.BillID}\n`;
        text += `شناسه پرداخت: ${violationData.PaymentID}\n`;
        text += `وضعیت شکایت: ${violationData.ComplaintStatus || 'شکایت ندارد'}\n`;
    } else {
        text += 'نتیجه: هیچ تخلفی یافت نشد.\n';
    }
    
    text += '\nتولید شده در پیشخوانک (pishkhanak.com)';
    
    copyToClipboard(text);
}

function sharePaymentInfo() {
    if (navigator.share) {
        navigator.share({
            title: 'اطلاعات پرداخت جریمه موتورسیکلت',
            text: 'اطلاعات پرداخت جریمه من از پیشخوانک',
            url: window.location.href
        });
    } else {
        copyToClipboard(window.location.href);
        showToast('لینک کپی شد!', 'success');
    }
}

function shareReport() {
    if (navigator.share) {
        navigator.share({
            title: 'گزارش تخلفات موتورسیکلت',
            text: 'گزارش تخلفات موتورسیکلت من از پیشخوانک',
            url: window.location.href
        });
    } else {
        copyAllInfo();
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
    .bg-orange-50 { background: #fff7ed !important; }
    .bg-yellow-50 { background: #fefce8 !important; }
    .border { border: 1px solid #e5e7eb !important; }
}
</style>
@endsection 