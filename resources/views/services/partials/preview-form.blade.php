<div class="lg:sticky lg:top-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <h3 class="text-base text-center text-sm font-semibold text-gray-900">جهت مشاهده نتیجه سرویس، کیف پول خود را فعال کنید</h3>
        
        <!-- Check if gateways are available -->
        @if($gateways && $gateways->isNotEmpty())
            <!-- Unified Wallet Charge Form -->
            <form action="{{ route('app.user.wallet.charge', [], false) }}" method="POST" class="space-y-3">
                @csrf
                
                <!-- Hidden fields for service continuation -->
                <input type="hidden" name="continue_service" value="{{ $service->slug }}">
                @if(isset($requestHash))
                    <input type="hidden" name="service_request_hash" value="{{ $requestHash }}">
                @endif
                <input type="hidden" name="service_session_key" value="{{ $sessionKey }}">
                
                <!-- Amount Dropdown -->
                <div>
                    <div class="relative">
                        <select name="amount" 
                                id="amount" 
                                class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg bg-white text-gray-900 font-medium appearance-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 cursor-pointer">
                            {{-- <option value="10000">۱۰,۰۰۰ تومان</option> --}}
                            <option value="100000" @if($service->price != 20000) selected @endif>۱۰۰,۰۰۰ تومان</option>
                            <option value="200000" @if($service->price == 20000) selected @endif>۲۰۰,۰۰۰ تومان</option>
                            <option value="500000">۵۰۰,۰۰۰ تومان</option>
                            <option value="1000000">۱,۰۰۰,۰۰۰ تومان</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 mx-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        class="w-full px-4 py-3 bg-sky-600 text-white font-medium rounded-lg hover:bg-sky-700 transition-colors duration-200 flex items-center justify-center">
                    ادامه و مشاهده نتیجه
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

        <!-- Security Note -->
        <div class="mt-3 pt-3 border-t border-gray-200">
            <div class="flex items-start">
                <svg class="w-4 h-4 text-green-500 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <p class="text-xs text-gray-600">
                    شما با استفاده از این کیف پول می توانید بیش از ۴۰ خدمت مختلف را در سایت پیشخوانک استفاده نمایید
                </p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Custom styles for the select dropdown */
select#amount {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 0.75rem center;
    background-size: 1.25rem;
    padding-left: 2.5rem;
}

select#amount:hover {
    border-color: #0284c7;
    background-color: #f0f9ff;
}

select#amount:focus {
    background-color: white;
}

/* Remove default arrow in Firefox */
select#amount {
    -moz-appearance: none;
}

/* Remove default arrow in IE */
select#amount::-ms-expand {
    display: none;
}
</style>
@endpush 