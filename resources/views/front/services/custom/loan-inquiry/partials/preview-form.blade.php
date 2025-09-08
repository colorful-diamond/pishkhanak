<!-- Custom Loan Inquiry Payment Form -->
<div class="lg:sticky lg:top-4">
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 border-2 border-blue-200 rounded-xl p-6 shadow-sm">
        
        @php
            // For sub-services, the service slug itself is the bank slug
            $bankSpecific = false;
            $bankInfo = null;
            
            if ($service->parent_id && $service->parent) {
                // This is a sub-service, use the service slug as bank slug
                $bankSlug = $service->slug;
                $bankSpecific = true;
                
                try {
                    $bankService = app(\App\Services\BankService::class);
                    $bankInfo = $bankService->getBankBySlug($bankSlug);
                } catch (\Exception $e) {
                    // Bank not found, disable bank-specific display
                    $bankSpecific = false;
                    $bankInfo = null;
                }
            }
        @endphp

        <!-- Service-specific header -->
        <div class="text-center">
            @if($bankSpecific && $bankInfo)
                <div class="flex items-center justify-center mb-4">
                    @if($bankInfo['logo'])
                        <img src="{{ $bankInfo['logo'] }}" alt="{{ $bankInfo['fa_name'] }}" class="w-12 h-12 rounded-lg shadow-md ml-3">
                    @endif
                    <div>
                        <h3 class="text-lg font-bold text-blue-900">{{ $bankInfo['fa_name'] }}</h3>
                        <p class="text-sm text-blue-700">استعلام وام و تسهیلات</p>
                    </div>
                </div>
            @endif
            <p class="text-sm text-blue-700">برای دریافت اطلاعات تسهیلات، کیف پول خود را شارژ کنید</p>
        </div>
        
        <!-- Check if gateways are available -->
        @if($gateways && $gateways->isNotEmpty())
            <!-- Custom Wallet Charge Form -->
            <form action="{{ route('app.user.wallet.charge', [], false) }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Hidden fields for service continuation -->
                <input type="hidden" name="continue_service" value="{{ $service->slug }}">
                @if(isset($requestHash))
                    <input type="hidden" name="service_request_hash" value="{{ $requestHash }}">
                @endif
                <input type="hidden" name="service_session_key" value="{{ $sessionKey }}">
                
                <!-- Amount Selection -->
                <div>
                    <div class="relative">
                        <select name="amount" 
                                id="amount" 
                                class="w-full px-4 py-4 pr-10 border-2 border-blue-300 rounded-lg bg-white text-gray-900 font-medium appearance-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                            <option value="100000" @if($service->price != 20000) selected @endif>۱۰۰,۰۰۰ تومان</option>
                            <option value="200000" @if($service->price == 20000) selected @endif>۲۰۰,۰۰۰ تومان</option>
                            <option value="500000">۵۰۰,۰۰۰ تومان</option>
                            <option value="1000000">۱,۰۰۰,۰۰۰ تومان</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 mx-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gateway Selection -->
                <input type="hidden" name="gateway_id" value="{{ $gateways->first()->id }}">

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    مشاهده تسهیلات
                </button>
            </form>
        @else
            <!-- No gateways available -->
            <div class="text-center py-6">
                <div class="w-12 h-12 bg-blue-100 rounded-full mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-blue-600 mb-2">درگاه پرداخت مناسبی یافت نشد</p>
                <p class="text-xs text-gray-500">لطفاً با پشتیبانی تماس بگیرید</p>
            </div>
        @endif

        <!-- Service Benefits -->
        <div class="mt-6 pt-4 border-t border-blue-200">
            <h4 class="text-sm font-semibold text-blue-800 mb-3">اطلاعات قابل دریافت</h4>
            <div class="space-y-2 text-xs text-blue-700">
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    لیست کامل تسهیلات و وام‌ها
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    مبلغ و نوع هر تسهیلات
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    تاریخ دریافت و وضعیت پرداخت
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    نام بانک و شعبه ارائه‌دهنده
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    آمار کامل سابقه تسهیلات
                </div>
            </div>
        </div>

        <!-- Advantages Section -->
        <div class="mt-6 pt-4 border-t border-blue-200">
            <h4 class="text-sm font-semibold text-blue-800 mb-3">مزایای کیف پول پیشخوانک</h4>
            <div class="space-y-2 text-xs text-blue-700">
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    دسترسی به بیش از ۴۰ سرویس مختلف
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    پرداخت سریع بدون ورود مجدد اطلاعات
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    مدیریت آسان تراکنش‌ها و تاریخچه
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Custom styles for the loan inquiry service form */
select#amount {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%232563eb'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 0.75rem center;
    background-size: 1.25rem;
    padding-left: 2.5rem;
}

select#amount:hover {
    border-color: #2563eb;
    background-color: #eff6ff;
}

select#amount:focus {
    background-color: white;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Remove default arrow in Firefox */
select#amount {
    -moz-appearance: none;
}

/* Remove default arrow in IE */
select#amount::-ms-expand {
    display: none;
}

/* Gradient animation for button */
button[type="submit"]:hover {
    background-size: 200% 200%;
    animation: gradient-shift 0.3s ease;
}

@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    100% { background-position: 100% 50%; }
}
</style>
@endpush