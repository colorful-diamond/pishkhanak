@extends('front.layouts.app')

@section('title', 'پرداخت آنلاین')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-sky-600 to-sky-700 px-6 py-4">
                <h1 class="text-xl font-bold text-white">پرداخت آنلاین</h1>
                <p class="text-sky-100 text-sm mt-1">انتخاب مبلغ و درگاه پرداخت</p>
            </div>

            <!-- Form -->
            <form action="{{ route('payments.create') }}" method="POST" id="payment-form" class="p-6 space-y-6">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="mr-3">
                                <h3 class="text-sm font-medium text-red-800">خطا در اطلاعات ورودی</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc space-y-1 pr-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-dark-sky-500 mb-2">
                        مبلغ (تومان) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="amount" 
                           name="amount" 
                           value="{{ old('amount', $amount) }}"
                           min="100" 
                           step="100"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors"
                           placeholder="مثال: 10000"
                           required>
                    <p class="text-sm text-gray-500 mt-1">حداقل مبلغ: ۱۰۰ تومان</p>
                </div>

                <!-- Currency -->
                <div>
                    <label for="currency" class="block text-sm font-medium text-dark-sky-500 mb-2">
                        واحد پول <span class="text-red-500">*</span>
                    </label>
                    <select id="currency" 
                            name="currency"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors"
                            required>
                        <option value="IRT" {{ old('currency', 'IRT') === 'IRT' ? 'selected' : '' }}>تومان ایران (IRT)</option>
                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>دلار آمریکا (USD)</option>
                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>یورو (EUR)</option>
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-dark-sky-500 mb-2">
                        توضیحات
                    </label>
                    <input type="text" 
                           id="description" 
                           name="description" 
                           value="{{ old('description', $description) }}"
                           maxlength="255"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors"
                           placeholder="توضیحات اختیاری">
                </div>

                <!-- Gateway Selection -->
                <div>
                    <label class="block text-sm font-medium text-dark-sky-500 mb-4">
                        انتخاب درگاه پرداخت <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($gateways as $gateway)
                            <div class="relative">
                                <input type="radio" 
                                       id="gateway_{{ $gateway->id }}" 
                                       name="gateway_id" 
                                       value="{{ $gateway->id }}"
                                       class="peer hidden"
                                       data-fee-percentage="{{ $gateway->fee_percentage }}"
                                       data-fee-fixed="{{ $gateway->fee_fixed }}"
                                       {{ old('gateway_id') == $gateway->id ? 'checked' : '' }}
                                       required>
                                <label for="gateway_{{ $gateway->id }}" 
                                       class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all hover:border-sky-300 peer-checked:border-sky-500 peer-checked:bg-sky-50">
                                    <div class="flex-shrink-0">
                                        @if($gateway->logo_url)
                                            <img src="{{ $gateway->logo_url }}" 
                                                 alt="{{ $gateway->name }}" 
                                                 class="w-12 h-12 object-contain rounded">
                                        @else
                                            <div class="w-12 h-12 bg-sky-200 rounded flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mr-4 flex-1">
                                        <h3 class="font-medium text-dark-sky-600">{{ $gateway->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $gateway->description }}</p>
                                        <div class="mt-1 text-xs text-gray-400">
                                            کارمزد: {{ $gateway->fee_percentage }}% + {{ number_format($gateway->fee_fixed) }} تومان
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="w-4 h-4 border-2 border-gray-300 rounded-full peer-checked:border-sky-500 peer-checked:bg-sky-500 transition-colors"></div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Fee Calculation Display -->
                <div id="fee-display" class="hidden bg-sky-50 rounded-lg p-4">
                    <h3 class="font-medium text-dark-sky-600 mb-3">محاسبه هزینه‌ها</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-dark-sky-500">مبلغ اصلی:</span>
                            <span id="original-amount" class="font-medium">۰ تومان</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-dark-sky-500">کارمزد درگاه:</span>
                            <span id="gateway-fee" class="font-medium">۰ تومان</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between">
                            <span class="text-dark-sky-600 font-semibold">مبلغ نهایی:</span>
                            <span id="total-amount" class="font-bold text-sky-600">۰ تومان</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-sky-600 hover:bg-sky-700 text-white font-medium py-3 px-6 rounded-lg transition-colors focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            id="submit-button">
                        ادامه پرداخت
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Notice -->
        <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="mr-3">
                    <h3 class="text-sm font-medium text-green-800">پرداخت امن</h3>
                    <p class="text-sm text-green-700 mt-1">
                        تمامی تراکنش‌ها از طریق درگاه‌های معتبر و با رمزنگاری SSL انجام می‌شود.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const gatewayInputs = document.querySelectorAll('input[name="gateway_id"]');
    const feeDisplay = document.getElementById('fee-display');
    const originalAmountSpan = document.getElementById('original-amount');
    const gatewayFeeSpan = document.getElementById('gateway-fee');
    const totalAmountSpan = document.getElementById('total-amount');

    function calculateFee() {
        const amount = parseInt(amountInput.value);
        const selectedGateway = document.querySelector('input[name="gateway_id"]:checked');

        if (!amount || !selectedGateway) {
            feeDisplay.classList.add('hidden');
            return;
        }

        const feePercentage = parseFloat(selectedGateway.dataset.feePercentage);
        const feeFixed = parseInt(selectedGateway.dataset.feeFixed);
        
        const fee = Math.round((amount * feePercentage / 100) + feeFixed);
        const total = amount + fee;

        originalAmountSpan.textContent = amount.toLocaleString('fa-IR') + ' تومان';
        gatewayFeeSpan.textContent = fee.toLocaleString('fa-IR') + ' تومان';
        totalAmountSpan.textContent = total.toLocaleString('fa-IR') + ' تومان';

        feeDisplay.classList.remove('hidden');
    }

    amountInput.addEventListener('input', calculateFee);
    gatewayInputs.forEach(input => {
        input.addEventListener('change', calculateFee);
    });

    // Initial calculation if values are set
    calculateFee();
});
</script>
@endsection 