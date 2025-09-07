<!-- Service Details Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
    <h2 class="text-base font-semibold text-gray-900 mb-3">{{ $service->title }}</h2>
    
    <div class="space-y-2">
        <!-- Request Details -->
        @if(isset($requestDetails) && is_array($requestDetails))
            @foreach($requestDetails as $key => $value)
                @if($key !== 'card_number_clean' && !empty($value))
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">
                            @switch($key)
                                @case('card_number')
                                    شماره کارت
                                    @break
                                @case('account_number')
                                    شماره حساب
                                    @break
                                @case('iban')
                                    شماره شبا
                                    @break
                                @case('national_code')
                                    کد ملی
                                    @break
                                @case('mobile')
                                    شماره موبایل
                                    @break
                                @case('postal_code')
                                    {{ __('service_fields.postal_code') }}
                                    @break
                                @default
                                    {{ $key }}
                            @endswitch
                        </span>
                        <span class="text-sm font-medium text-gray-900 font-mono">{{ $value }}</span>
                    </div>
                @endif
            @endforeach
        @endif

        <!-- API Response Data -->
        @if(isset($previewData))
            @if(isset($previewData['owner_name']) && !empty($previewData['owner_name']))
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">نام صاحب حساب</span>
                    <span class="text-sm font-medium text-gray-900">{{ $previewData['owner_name'] }}</span>
                </div>
            @endif

            @if(isset($previewData['bank_name']) && !empty($previewData['bank_name']))
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">بانک</span>
                    <div class="flex items-center gap-2">
                        @if(isset($previewData['bank_logo']) && !empty($previewData['bank_logo']))
                            <img src="{{ $previewData['bank_logo'] }}" 
                                 alt="{{ $previewData['bank_name'] }}" 
                                 class="w-5 h-5 object-contain">
                        @endif
                        <span class="text-sm font-medium text-gray-900">{{ $previewData['bank_name'] }}</span>
                    </div>
                </div>
            @endif

            @if(isset($previewData['iban']) && !empty($previewData['iban']))
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">شماره شبا</span>
                    <span class="text-sm font-medium text-gray-900 font-mono">{{ $previewData['iban'] }}</span>
                </div>
            @endif

            @if(isset($previewData['account_number']) && !empty($previewData['account_number']))
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">شماره حساب</span>
                    <span class="text-sm font-medium text-gray-900 font-mono">{{ $previewData['account_number'] }}</span>
                </div>
            @endif
        @endif

        <!-- Service Price -->
        <div class="flex items-center justify-between py-2">
            <span class="text-sm text-gray-600">هزینه سرویس</span>
            <span class="text-base font-bold text-sky-600">{{ number_format($service->price) }} تومان</span>
        </div>
    </div>
</div> 