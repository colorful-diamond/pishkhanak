<!-- Custom Car Violation Inquiry Payment Form -->
<div class="lg:sticky lg:top-4">
    <div class="bg-gradient-to-br from-red-50 to-orange-100 border-2 border-red-200 rounded-xl p-6 shadow-sm">
        <!-- Service-specific header -->
        <div class="text-center">
            <p class="text-sm text-red-700">برای دریافت خلافی‌های خودرو، کیف پول خود را شارژ کنید</p>
        </div>
        
        <!-- Check if gateways are available -->
        @if($gateways && $gateways->isNotEmpty())
            <!-- Custom Wallet Charge Form -->
            <form action="{{ route('app.user.wallet.charge') }}" method="POST" class="space-y-4">
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
                                class="w-full px-4 py-4 pr-10 border-2 border-red-300 rounded-lg bg-white text-gray-900 font-medium appearance-none focus:ring-2 focus:ring-red-500 focus:border-red-500 cursor-pointer">
                            <option value="100000">۱۰۰,۰۰۰ تومان</option>
                            <option value="200000">۲۰۰,۰۰۰ تومان</option>
                            <option value="500000">۵۰۰,۰۰۰ تومان</option>
                            <option value="1000000">۱,۰۰۰,۰۰۰ تومان</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 mx-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        class="w-full px-6 py-4 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    مشاهده خلافی‌های خودرو
                </button>
            </form>
        @else
            <!-- No gateways available -->
            <div class="text-center py-6">
                <div class="w-12 h-12 bg-red-100 rounded-full mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-red-600 mb-2">درگاه پرداخت مناسبی یافت نشد</p>
                <p class="text-xs text-gray-500">لطفاً با پشتیبانی تماس بگیرید</p>
            </div>
        @endif

        <!-- Service Benefits -->
        <div class="mt-6 pt-4 border-t border-red-200">
            <h4 class="text-sm font-semibold text-red-800 mb-3">اطلاعات قابل دریافت</h4>
            <div class="space-y-2 text-xs text-red-700">
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    لیست کامل خلافی‌های ثبت شده
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    مبلغ و نوع هر خلافی
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    تاریخ و مکان وقوع خلافی
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    وضعیت قابل پرداخت بودن
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    شناسه‌های قبض و پرداخت
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    آمار کامل تفکیک شده خلافی‌ها
                </div>
            </div>
        </div>

        <!-- Advantages Section -->
        <div class="mt-6 pt-4 border-t border-red-200">
            <h4 class="text-sm font-semibold text-red-800 mb-3">مزایای کیف پول پیشخوانک</h4>
            <div class="space-y-2 text-xs text-red-700">
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    دسترسی به بیش از ۴۰ سرویس مختلف
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    پرداخت سریع بدون ورود مجدد اطلاعات
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    مدیریت آسان تراکنش‌ها و تاریخچه
                </div>
            </div>
        </div>

        <!-- Security Badge -->
        <div class="mt-4 pt-4 border-t border-red-200">
            <div class="flex items-center justify-center gap-2 text-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-xs font-medium">اتصال مستقیم به پلیس راهور</span>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Custom styles for the car violation service form */
select#amount {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23dc2626'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 0.75rem center;
    background-size: 1.25rem;
    padding-left: 2.5rem;
}

select#amount:hover {
    border-color: #dc2626;
    background-color: #fef2f2;
}

select#amount:focus {
    background-color: white;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
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