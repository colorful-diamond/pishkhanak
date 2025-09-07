@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-sky-50 to-indigo-50 rounded-xl p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">نتیجه استعلام خلافی خودرو</h1>
                <p class="text-gray-600">اطلاعات کامل خلافی‌های ثبت شده برای پلاک شما</p>
            </div>
            <div class="text-right">
                @if($data['data']['has_violations'])
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    دارای خلافی
                </span>
                @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    بدون خلافی
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Summary Statistics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    خلاصه خلافی‌ها
                </h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center bg-sky-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-sky-600 mb-1">{{ $data['data']['violation_count'] }}</div>
                        <div class="text-sm text-gray-600">کل خلافی‌ها</div>
                    </div>
                    <div class="text-center bg-red-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-red-600 mb-1">{{ $data['data']['statistics']['payable_count'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">قابل پرداخت</div>
                    </div>
                    <div class="text-center bg-green-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600 mb-1">{{ $data['data']['statistics']['with_image_count'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">دارای تصویر</div>
                    </div>
                    <div class="text-center bg-orange-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-orange-600 mb-1">{{ $data['data']['total_amount_formatted'] }}</div>
                        <div class="text-sm text-gray-600">مبلغ کل</div>
                    </div>
                </div>
                
                @if(!empty($data['data']['summary']))
                <div class="mt-4 p-4 bg-sky-50 rounded-lg">
                    <p class="text-sky-800 text-center">{{ $data['data']['summary'] }}</p>
                </div>
                @endif
            </div>

            <!-- Violations List -->
            @if(!empty($data['data']['violations']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    جزئیات خلافی‌ها
                </h2>
                
                <div class="space-y-4">
                    @foreach($data['data']['violations'] as $violation)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-1">{{ $violation['type'] }}</h3>
                                <p class="text-gray-600 text-sm">{{ $violation['description'] }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($violation['status_color'] === 'red') bg-red-100 text-red-800
                                    @elseif($violation['status_color'] === 'orange') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $violation['status_text'] }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="bg-gray-50 rounded p-3">
                                <label class="block font-medium text-gray-600 mb-1">مبلغ</label>
                                <p class="text-gray-800 font-bold">{{ $violation['price_formatted'] }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-3">
                                <label class="block font-medium text-gray-600 mb-1">تاریخ</label>
                                <p class="text-gray-800">{{ $violation['date_persian'] ?: $violation['date'] }}</p>
                            </div>
                            <div class="bg-gray-50 rounded p-3">
                                <label class="block font-medium text-gray-600 mb-1">مکان</label>
                                <p class="text-gray-800">{{ $violation['location'] ?: 'نامشخص' }}</p>
                            </div>
                        </div>
                        
                        @if($violation['is_payable'] && !empty($violation['bill_id']))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <label class="block font-medium text-gray-600 mb-1">شناسه قبض</label>
                                    <p class="text-gray-800 font-mono">{{ $violation['bill_id'] }}</p>
                                </div>
                                <div>
                                    <label class="block font-medium text-gray-600 mb-1">شناسه پرداخت</label>
                                    <p class="text-gray-800 font-mono">{{ $violation['payment_id'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Severity Breakdown -->
            @if(isset($data['data']['statistics']['severity_breakdown']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    تفکیک شدت خلافی‌ها
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-600 mb-1">{{ $data['data']['statistics']['severity_breakdown']['minor'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">خلافی‌های جزئی</div>
                        <div class="text-xs text-gray-500 mt-1">زیر ۵۰ هزار تومان</div>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-orange-600 mb-1">{{ $data['data']['statistics']['severity_breakdown']['moderate'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">خلافی‌های متوسط</div>
                        <div class="text-xs text-gray-500 mt-1">۵۰ تا ۱۰۰ هزار تومان</div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-red-600 mb-1">{{ $data['data']['statistics']['severity_breakdown']['severe'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">خلافی‌های شدید</div>
                        <div class="text-xs text-gray-500 mt-1">بالای ۱۰۰ هزار تومان</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">عملیات سریع</h3>
                
                <div class="space-y-3">
                    @if($data['data']['has_violations'])
                    <button onclick="copyPaymentInfo()" 
                            class="w-full flex items-center justify-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        کپی اطلاعات پرداخت
                    </button>
                    @endif
                    
                    <button onclick="printReport()" 
                            class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        چاپ گزارش
                    </button>
                    
                    <a href="{{ route('services.show', 'car-violation-inquiry') }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        استعلام جدید
                    </a>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-sky-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-sky-800 mb-3">نکات مهم</h3>
                <ul class="space-y-2 text-sky-700 text-sm">
                    @if($data['data']['has_violations'])
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        پرداخت زودهنگام خلافی‌ها از جریمه تأخیر جلوگیری می‌کند
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        در صورت عدم اعتراف به خلافی می‌توانید اعتراض کنید
                    </li>
                    @else
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        عالی! خودرو شما هیچ خلافی ندارد
                    </li>
                    @endif
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        به رعایت قوانین راهنمایی و رانندگی ادامه دهید
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function copyPaymentInfo() {
    const paymentInfo = @json($data['data']['violations']);
    let text = 'اطلاعات پرداخت خلافی‌ها:\n\n';
    
    paymentInfo.forEach((violation, index) => {
        if (violation.is_payable && violation.bill_id) {
            text += `خلافی ${index + 1}:\n`;
            text += `نوع: ${violation.type}\n`;
            text += `مبلغ: ${violation.price_formatted}\n`;
            text += `شناسه قبض: ${violation.bill_id}\n`;
            text += `شناسه پرداخت: ${violation.payment_id}\n\n`;
        }
    });
    
    copyToClipboard(text);
}

function printReport() {
    window.print();
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('اطلاعات با موفقیت کپی شد!', 'success');
        });
    } else {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showNotification('اطلاعات با موفقیت کپی شد!', 'success');
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