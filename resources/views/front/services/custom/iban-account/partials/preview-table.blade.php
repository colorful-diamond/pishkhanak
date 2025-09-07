<!-- Custom IBAN to Account Service Details Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <!-- Service Header with Icon -->
    <div class="flex items-center mb-4">
        <div class="flex-shrink-0 w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center ml-3">
            <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $service->title }}</h2>
            <p class="text-sm text-gray-600">تبدیل شماره شبا به شماره حساب</p>
        </div>
    </div>
    
    <!-- Input Information Section -->
    <div class="bg-sky-50 border border-sky-200 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-semibold text-sky-800 mb-3 flex items-center">
            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            اطلاعات ورودی
        </h3>
        
        <div class="space-y-3">
            @if(isset($requestDetails) && is_array($requestDetails))
                @foreach($requestDetails as $key => $value)
                    @if($key !== 'card_number_clean' && !empty($value))
                        <div class="flex items-center justify-between py-2 px-3 bg-white rounded-lg border border-sky-200">
                            <span class="text-sm font-medium text-sky-700">
                                @switch($key)
                                    @case('iban')
                                        شماره شبا
                                        @break
                                    @case('account_number')
                                        شماره حساب
                                        @break
                                    @case('bank_code')
                                        کد بانک
                                        @break
                                    @case('national_code')
                                        کد ملی
                                        @break
                                    @case('mobile')
                                        شماره موبایل
                                        @break
                                    @default
                                        {{ $key }}
                                @endswitch
                            </span>
                            <span class="text-sm font-bold text-gray-900 font-mono bg-gray-50 px-3 py-1 rounded-md">{{ $value }}</span>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>

    <!-- Preview Result Section -->
    @if(isset($previewData) && !empty($previewData))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <h3 class="text-sm font-semibold text-green-800 mb-3 flex items-center">
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                پیش‌نمایش نتیجه
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(isset($previewData['owner_name']) && !empty($previewData['owner_name']))
                    <div class="bg-white p-3 rounded-lg border border-green-200">
                        <div class="text-xs text-green-600 mb-1">نام صاحب حساب</div>
                        <div class="text-sm font-bold text-green-900">{{ $previewData['owner_name'] }}</div>
                    </div>
                @endif

                @if(isset($previewData['bank_name']) && !empty($previewData['bank_name']))
                    <div class="bg-white p-3 rounded-lg border border-green-200">
                        <div class="text-xs text-green-600 mb-1">نام بانک</div>
                        <div class="flex items-center gap-2">
                            @if(isset($previewData['bank_logo']) && !empty($previewData['bank_logo']))
                                <img src="{{ $previewData['bank_logo'] }}" 
                                     alt="{{ $previewData['bank_name'] }}" 
                                     class="w-5 h-5 object-contain">
                            @endif
                            <span class="text-sm font-bold text-green-900">{{ $previewData['bank_name'] }}</span>
                        </div>
                    </div>
                @endif

                @if(isset($previewData['account_number']) && !empty($previewData['account_number']))
                    <div class="bg-white p-3 rounded-lg border border-green-200">
                        <div class="text-xs text-green-600 mb-1">شماره حساب</div>
                        <div class="text-sm font-bold text-green-900 font-mono">{{ $previewData['account_number'] }}</div>
                    </div>
                @endif

                @if(isset($previewData['iban']) && !empty($previewData['iban']))
                    <div class="bg-white p-3 rounded-lg border border-green-200">
                        <div class="text-xs text-green-600 mb-1">شماره شبا</div>
                        <div class="text-sm font-bold text-green-900 font-mono">{{ $previewData['iban'] }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Service Information -->
    <div class="border-t border-gray-200 pt-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-900">هزینه سرویس</div>
                    <div class="text-xs text-gray-500">قابل پرداخت از کیف پول</div>
                </div>
            </div>
            <div class="text-left">
                <div class="text-lg font-bold text-sky-600">{{ number_format($service->price) }} تومان</div>
                <div class="text-xs text-gray-500">{{ number_format($service->price * 10) }} ریال</div>
            </div>
        </div>
    </div>
    
    <!-- Service Features -->
    <div class="mt-6 pt-4 border-t border-gray-200">
        <h4 class="text-sm font-semibold text-gray-800 mb-3">ویژگی‌های سرویس</h4>
        <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="flex items-center gap-2 text-green-600">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                دریافت آنی نتیجه
            </div>
            <div class="flex items-center gap-2 text-green-600">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                دقت ۱۰۰٪
            </div>
            <div class="flex items-center gap-2 text-green-600">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                امنیت بالا
            </div>
            <div class="flex items-center gap-2 text-green-600">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                پشتیبانی ۲۴/۷
            </div>
        </div>
    </div>
</div> 