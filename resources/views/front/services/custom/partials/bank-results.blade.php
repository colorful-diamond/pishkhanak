@if (session('success') && session($dataKey))
    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <h3 class="text-lg font-semibold text-green-800 mb-3">{{ $title ?? 'نتیجه' }}</h3>
        <div class="grid grid-cols-1 gap-3 text-sm">
            @if(session($dataKey . '.bank_name'))
                <div class="flex justify-between">
                    <span class="text-gray-600">نام بانک:</span>
                    <span class="font-medium text-gray-900">{{ session($dataKey . '.bank_name') }}</span>
                </div>
            @endif
            @if(session($dataKey . '.iban'))
                <div class="flex justify-between">
                    <span class="text-gray-600">شماره شبا:</span>
                    <span class="font-medium text-gray-900 font-mono" dir="ltr">{{ session($dataKey . '.iban') }}</span>
                </div>
            @endif
            @if(session($dataKey . '.account_number'))
                <div class="flex justify-between">
                    <span class="text-gray-600">شماره حساب:</span>
                    <span class="font-medium text-gray-900 font-mono" dir="ltr">{{ session($dataKey . '.account_number') }}</span>
                </div>
            @endif
            @if(session($dataKey . '.status'))
                <div class="flex justify-between">
                    <span class="text-gray-600">وضعیت:</span>
                    <span class="font-medium text-gray-900">{{ session($dataKey . '.status') }}</span>
                </div>
            @endif
        </div>
    </div>
@endif 