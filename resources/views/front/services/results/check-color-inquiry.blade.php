@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">نتیجه استعلام وضعیت چک</h1>
                <p class="text-gray-600">شماره چک {{ $data['data']['check_info']['check_number'] ?? 'نامشخص' }}</p>
            </div>
            <div class="text-right">
                @php
                    $color = $data['data']['check_status']['color'] ?? '';
                    $statusClass = match($color) {
                        'WHITE' => 'bg-green-100 text-green-800',
                        'GRAY' => 'bg-yellow-100 text-yellow-800',
                        'RED' => 'bg-red-100 text-red-800',
                        'BLACK' => 'bg-gray-900 text-white',
                        default => 'bg-sky-100 text-gray-800'
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                        @if($color === 'WHITE')
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        @elseif($color === 'RED' || $color === 'BLACK')
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        @else
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        @endif
                    </svg>
                    {{ $data['data']['check_status']['color_persian'] ?? 'نامشخص' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Critical Alerts -->
    @if(!empty($data['data']['warnings']))
        <div class="mb-6 space-y-3">
            @foreach($data['data']['warnings'] as $warning)
                <div class="p-4 rounded-lg border 
                    @if(str_contains($warning, 'جعلی')) bg-red-50 border-red-200 text-red-800
                    @elseif(str_contains($warning, 'برگشتی')) bg-orange-50 border-orange-200 text-orange-800
                    @else bg-yellow-50 border-yellow-200 text-yellow-800 @endif">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-semibold">{{ $warning }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Check Status Visualization -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">وضعیت چک</h2>
                
                <!-- Visual Check Color Indicator -->
                <div class="flex justify-center mb-6">
                    @php
                        $colorClass = match($color) {
                            'WHITE' => 'bg-white border-4 border-green-500',
                            'GRAY' => 'bg-sky-300 border-4 border-gray-500',
                            'RED' => 'bg-red-500 border-4 border-red-700',
                            'BLACK' => 'bg-gray-900 border-4 border-black',
                            default => 'bg-sky-100 border-4 border-gray-400'
                        };
                    @endphp
                    <div class="w-32 h-20 {{ $colorClass }} rounded-lg shadow-lg flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-sm font-bold 
                                @if($color === 'WHITE') text-gray-800
                                @elseif($color === 'BLACK') text-white
                                @else text-white @endif">
                                چک
                            </div>
                            <div class="text-xs 
                                @if($color === 'WHITE') text-gray-600
                                @elseif($color === 'BLACK') text-gray-300
                                @else text-white @endif">
                                {{ $color ?? 'نامشخص' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Description -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold {{ $color === 'WHITE' ? 'text-green-700' : ($color === 'RED' || $color === 'BLACK' ? 'text-red-700' : 'text-yellow-700') }}">
                        {{ $data['data']['check_status']['color_persian'] ?? 'وضعیت نامشخص' }}
                    </h3>
                    @if(!empty($data['data']['check_status']['status_description']))
                        <p class="text-gray-600 mt-2">{{ $data['data']['check_status']['status_description'] }}</p>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center gap-4">
                    @if($data['data']['check_status']['can_cash'] ?? false)
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            قابل نقد
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            غیرقابل نقد
                        </span>
                    @endif
                </div>
            </div>

            <!-- Check Information -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h-10zm10 0V2M7 4V2m10 2v16a2 2 0 01-2 2H9a2 2 0 01-2-2V4h10z"></path>
                    </svg>
                    مشخصات چک
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        @if(!empty($data['data']['check_info']['check_number']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="text-gray-600 text-sm mb-1">شماره چک</div>
                                <div class="font-bold text-gray-900 text-lg font-mono">{{ $data['data']['check_info']['check_number'] }}</div>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['check_details']['amount']))
                            <div class="bg-sky-50 border border-sky-200 rounded-lg p-4">
                                <div class="text-sky-700 text-sm mb-1">مبلغ چک</div>
                                <div class="font-bold text-sky-900 text-xl">{{ number_format(($data['data']['check_details']['amount'] ?? 0) / 10) }} تومان</div>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['check_details']['account_owner']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="text-gray-600 text-sm mb-1">نام صاحب حساب</div>
                                <div class="font-semibold text-gray-900">{{ $data['data']['check_details']['account_owner'] }}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Bank Information -->
                    <div class="space-y-4">
                        @if(!empty($data['data']['check_info']['bank_name']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="text-gray-600 text-sm mb-1">نام بانک</div>
                                <div class="font-semibold text-gray-900">{{ $data['data']['check_info']['bank_name'] }}</div>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['check_info']['branch_name']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="text-gray-600 text-sm mb-1">نام شعبه</div>
                                <div class="font-semibold text-gray-900">{{ $data['data']['check_info']['branch_name'] }}</div>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['check_details']['account_number']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="text-gray-600 text-sm mb-1">شماره حساب</div>
                                <div class="font-semibold text-gray-900 font-mono cursor-pointer" 
                                     onclick="copyToClipboard('{{ $data['data']['check_details']['account_number'] }}')"
                                     title="کلیک کنید تا کپی شود">
                                    {{ $data['data']['check_details']['account_number'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Dates Information -->
                @if(!empty($data['data']['check_details']['issue_date']) || !empty($data['data']['check_details']['due_date']))
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if(!empty($data['data']['check_details']['issue_date']))
                                <div class="bg-sky-50 rounded-lg p-4">
                                    <div class="text-gray-600 text-sm mb-1">تاریخ صدور</div>
                                    <div class="font-semibold text-gray-900">{{ $data['data']['check_details']['issue_date'] }}</div>
                                </div>
                            @endif
                            
                            @if(!empty($data['data']['check_details']['due_date']))
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="text-yellow-700 text-sm mb-1">تاریخ سررسید</div>
                                    <div class="font-semibold text-yellow-900">{{ $data['data']['check_details']['due_date'] }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Bounce Information (if check is RED) -->
            @if(!empty($data['data']['bounce_info']))
                <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                    <h2 class="text-xl font-semibold text-red-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        اطلاعات برگشت چک
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if(!empty($data['data']['bounce_info']['bounce_date']))
                            <div class="bg-white border border-red-200 rounded-lg p-4">
                                <div class="text-red-700 text-sm mb-1">تاریخ برگشت</div>
                                <div class="font-semibold text-red-900">{{ $data['data']['bounce_info']['bounce_date'] }}</div>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['bounce_info']['bounce_reason']))
                            <div class="bg-white border border-red-200 rounded-lg p-4">
                                <div class="text-red-700 text-sm mb-1">علت برگشت</div>
                                <div class="font-semibold text-red-900">{{ $data['data']['bounce_info']['bounce_reason'] }}</div>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['bounce_info']['penalty_amount']))
                            <div class="bg-white border border-red-200 rounded-lg p-4">
                                <div class="text-red-700 text-sm mb-1">مبلغ جریمه</div>
                                <div class="font-bold text-red-900 text-lg">{{ number_format(($data['data']['bounce_info']['penalty_amount'] ?? 0) / 10) }} تومان</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Color Guide -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM7 3H5v12a2 2 0 002 2 2 2 0 002-2V3z"></path>
                    </svg>
                    راهنمای رنگ‌بندی چک‌ها
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                        <div class="w-8 h-5 bg-white border-2 border-green-500 rounded mx-auto mb-2"></div>
                        <div class="font-semibold text-green-800">سفید</div>
                        <div class="text-xs text-green-600 mt-1">معتبر و قابل نقد</div>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                        <div class="w-8 h-5 bg-sky-300 border-2 border-yellow-500 rounded mx-auto mb-2"></div>
                        <div class="font-semibold text-yellow-800">خاکستری</div>
                        <div class="text-xs text-yellow-600 mt-1">نیاز به بررسی</div>
                    </div>
                    
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                        <div class="w-8 h-5 bg-red-500 border-2 border-red-700 rounded mx-auto mb-2"></div>
                        <div class="font-semibold text-red-800">قرمز</div>
                        <div class="text-xs text-red-600 mt-1">برگشت خورده</div>
                    </div>
                    
                    <div class="bg-sky-50 border border-gray-300 rounded-lg p-4 text-center">
                        <div class="w-8 h-5 bg-gray-900 border-2 border-black rounded mx-auto mb-2"></div>
                        <div class="font-semibold text-gray-800">سیاه</div>
                        <div class="text-xs text-gray-600 mt-1">جعلی</div>
                    </div>
                </div>
            </div>
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
                    <button onclick="copyCheckInfo()" class="w-full bg-sky-100 hover:bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        کپی اطلاعات
                    </button>
                    <button onclick="shareReport()" class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        اشتراک‌گذاری
                    </button>
                </div>
            </div>

            <!-- Recommendations -->
            @if(!empty($data['data']['recommendations']))
                <div class="bg-sky-50 border border-sky-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-sky-800 mb-3">توصیه‌ها</h3>
                    <ul class="text-sm text-sky-700 space-y-2">
                        @foreach($data['data']['recommendations'] as $recommendation)
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-sky-600 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $recommendation }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Check Summary -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">خلاصه چک</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاریخ استعلام:</span>
                        <span class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">زمان استعلام:</span>
                        <span class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('H:i') }}</span>
                    </div>
                    @if(!empty($data['data']['check_details']['amount']))
                        <div class="flex justify-between">
                            <span class="text-gray-600">مبلغ:</span>
                            <span class="font-medium">{{ number_format(($data['data']['check_details']['amount'] ?? 0) / 10) }} تومان</span>
                        </div>
                    @endif
                    @if(!empty($data['data']['check_info']['bank_name']))
                        <div class="flex justify-between">
                            <span class="text-gray-600">بانک:</span>
                            <span class="font-medium">{{ $data['data']['check_info']['bank_name'] }}</span>
                        </div>
                    @endif
                    <div class="pt-3 border-t border-gray-200">
                        <div class="text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $data['data']['check_status']['color_persian'] ?? 'نامشخص' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">نکات مهم</h3>
                <ul class="text-sm text-yellow-700 space-y-2">
                    <li>• اطلاعات بر اساس سیستم صیاد است</li>
                    <li>• در صورت شک، با بانک تماس بگیرید</li>
                    <li>• چک‌های جعلی را گزارش دهید</li>
                    <li>• سررسید چک را بررسی کنید</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
const checkData = @json($data['data'] ?? []);

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('کپی شد!', 'success');
    }).catch(() => {
        showToast('خطا در کپی کردن', 'error');
    });
}

function copyCheckInfo() {
    let text = 'گزارش استعلام چک\n';
    text += '='.repeat(40) + '\n\n';
    text += `شماره چک: ${checkData.check_info?.check_number || 'نامشخص'}\n`;
    text += `تاریخ گزارش: {{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d H:i') }}\n\n`;
    
    // Status
    text += `وضعیت: ${checkData.check_status?.color_persian || 'نامشخص'}\n`;
    text += `توضیحات: ${checkData.check_status?.status_description || 'ندارد'}\n`;
    text += `قابل نقد: ${checkData.check_status?.can_cash ? 'بله' : 'خیر'}\n\n`;
    
    // Check Details
    if (checkData.check_details) {
        text += 'مشخصات چک:\n';
        if (checkData.check_details.amount) text += `مبلغ: ${(checkData.check_details.amount / 10).toLocaleString()} تومان\n`;
        if (checkData.check_details.account_owner) text += `صاحب حساب: ${checkData.check_details.account_owner}\n`;
        if (checkData.check_details.issue_date) text += `تاریخ صدور: ${checkData.check_details.issue_date}\n`;
        if (checkData.check_details.due_date) text += `سررسید: ${checkData.check_details.due_date}\n`;
        text += '\n';
    }
    
    // Bank Info
    if (checkData.check_info) {
        text += 'اطلاعات بانک:\n';
        if (checkData.check_info.bank_name) text += `بانک: ${checkData.check_info.bank_name}\n`;
        if (checkData.check_info.branch_name) text += `شعبه: ${checkData.check_info.branch_name}\n`;
        text += '\n';
    }
    
    // Bounce Info
    if (checkData.bounce_info) {
        text += 'اطلاعات برگشت:\n';
        if (checkData.bounce_info.bounce_date) text += `تاریخ برگشت: ${checkData.bounce_info.bounce_date}\n`;
        if (checkData.bounce_info.bounce_reason) text += `علت برگشت: ${checkData.bounce_info.bounce_reason}\n`;
        if (checkData.bounce_info.penalty_amount) text += `جریمه: ${(checkData.bounce_info.penalty_amount / 10).toLocaleString()} تومان\n`;
        text += '\n';
    }
    
    text += 'تولید شده در پیشخوانک (pishkhanak.com)';
    
    copyToClipboard(text);
}

function shareReport() {
    if (navigator.share) {
        navigator.share({
            title: 'گزارش استعلام چک',
            text: 'نتیجه استعلام چک من از پیشخوانک',
            url: window.location.href
        });
    } else {
        copyToClipboard(window.location.href);
        showToast('لینک کپی شد!', 'success');
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
    .bg-green-50 { background: #f0fdf4 !important; }
    .bg-red-50 { background: #fef2f2 !important; }
    .bg-yellow-50 { background: #fefce8 !important; }
    .border { border: 1px solid #e5e7eb !important; }
}
</style>
@endsection 