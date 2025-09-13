<!-- Custom Credit Score Rating Payment Form -->
<div class="lg:sticky lg:top-4">
    @php
        // For credit-score-rating service, always use sky blue theme
        $bankSpecific = false;
        $bankInfo = null;
        $bankColor = null;
        
        // Force sky blue for main credit score rating service
        if ($service->slug === 'credit-score-rating') {
            $bankColor = '#0ea5e9'; // sky-500 color
            $bankSpecific = false;
        } elseif ($service->parent_id && $service->parent) {
            // This is a sub-service, use the service slug as bank slug
            $bankSlug = $service->slug;
            $bankSpecific = true;
            
            try {
                $bankService = app(\App\Services\BankService::class);
                $bankInfo = $bankService->getBankBySlug($bankSlug);
                if ($bankInfo && isset($bankInfo['color'])) {
                    $bankColor = $bankInfo['color'];
                }
            } catch (\Exception $e) {
                // Bank not found, disable bank-specific display
                $bankSpecific = false;
                $bankInfo = null;
            }
        }
        
        // Function to convert hex to RGB
        $hexToRgb = function($hex) {
            $hex = ltrim($hex, '#');
            return [
                'r' => hexdec(substr($hex, 0, 2)),
                'g' => hexdec(substr($hex, 2, 2)),
                'b' => hexdec(substr($hex, 4, 2))
            ];
        };
        
        // Get RGB values if bank color exists
        $rgb = $bankColor ? $hexToRgb($bankColor) : null;
    @endphp
    
    <div class="border-2 rounded-xl p-6 shadow-sm"
         @if($bankColor && $rgb)
         style="background: linear-gradient(135deg, rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.05) 0%, rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.1) 100%); 
                border-color: rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.3);"
         @else
         class="bg-gradient-to-br from-sky-50 to-blue-100 border-sky-200"
         @endif>
        
        <!-- Service-specific header -->
        <div class="text-center">
            <p class="text-sm" @if($bankColor && $rgb) style="color: rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.8);" @else class="text-sky-700" @endif>
                جهت مشاهده وضعیت اعتباری خود می بایست کیف پول اعتبارسنجی خود را فعال کنید
            </p>
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
                                id="amount-{{ $service->id }}" 
                                class="w-full px-4 py-4 pr-10 border-2 rounded-lg bg-white text-gray-900 font-medium appearance-none focus:ring-2 cursor-pointer transition-all duration-200"
                                @if($bankColor && $rgb) 
                                style="border-color: rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.4);" 
                                onmouseover="this.style.borderColor='{{ $bankColor }}'; this.style.backgroundColor='rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.02)';" 
                                onmouseout="this.style.borderColor='rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.4)'; this.style.backgroundColor='white';" 
                                onfocus="this.style.borderColor='{{ $bankColor }}'; this.style.boxShadow='0 0 0 3px rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.1)';" 
                                onblur="this.style.borderColor='rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.4)'; this.style.boxShadow='none';"
                                @else 
                                class="border-sky-300 focus:ring-sky-500 focus:border-sky-500 hover:border-sky-400 hover:bg-sky-50"
                                @endif>
                            <option value="100000" @if($service->price != 20000) selected @endif>۱۰۰,۰۰۰ تومان</option>
                            <option value="200000" @if($service->price == 20000) selected @endif>۲۰۰,۰۰۰ تومان</option>
                            <option value="500000">۵۰۰,۰۰۰ تومان</option>
                            <option value="1000000">۱,۰۰۰,۰۰۰ تومان</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 mx-3" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-400" @endif fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        class="w-full px-6 py-4 text-white font-bold rounded-xl transition-all duration-200 flex items-center justify-center shadow-lg transform"
                        @if($bankColor)
                        style="background-color: {{ $bankColor }}; border-color: {{ $bankColor }};" 
                        onmouseover="this.style.backgroundColor='{{ $bankColor }}E6'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 25px rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.3)';" 
                        onmouseout="this.style.backgroundColor='{{ $bankColor }}'; this.style.transform='translateY(0px)'; this.style.boxShadow='0 10px 15px rgba(0, 0, 0, 0.1)';"
                        @else
                        class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 hover:shadow-xl hover:-translate-y-1"
                        @endif>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    مشاهده وضعیت اعتباری
                </button>
            </form>
        @else
            <!-- No gateways available -->
            <div class="text-center py-6">
                <div class="w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center"
                     @if($bankColor && $rgb)
                     style="background-color: rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.1);"
                     @else
                     class="bg-sky-100"
                     @endif>
                    <svg class="w-6 h-6" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-600" @endif fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm mb-2" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-600" @endif>
                    درگاه پرداخت مناسبی یافت نشد
                </p>
                <p class="text-xs text-gray-500">لطفاً با پشتیبانی تماس بگیرید</p>
            </div>
        @endif

        <!-- Service Benefits -->
        <div class="mt-6 pt-4 border-t" @if($bankColor && $rgb) style="border-color: rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.2);" @else class="border-sky-200" @endif>
            <h4 class="text-sm font-semibold mb-3" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-800" @endif>
                اطلاعات قابل دریافت
            </h4>
            <div class="space-y-2 text-xs" @if($bankColor && $rgb) style="color: rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.8);" @else class="text-sky-700" @endif>
                <div class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-500" @endif fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    امتیاز اعتباری دقیق و جامع
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-500" @endif fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    تحلیل عوامل مؤثر بر امتیاز
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-500" @endif fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    سابقه پرداخت و استفاده از اعتبار
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-500" @endif fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    توصیه‌های بهبود امتیاز
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-500" @endif fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    مقایسه با متوسط کشوری
                </div>
            </div>
        </div>

        <!-- Advantages Section -->
        <div class="mt-6 pt-4 border-t" @if($bankColor && $rgb) style="border-color: rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.2);" @else class="border-sky-200" @endif>
            <h4 class="text-sm font-semibold mb-3" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-800" @endif>
                مزایای کیف پول پیشخوانک
            </h4>
            <div class="space-y-2 text-xs" @if($bankColor && $rgb) style="color: rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.8);" @else class="text-sky-700" @endif>
                <div class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-500" @endif fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    دسترسی به بیش از ۴۰ سرویس مختلف
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-500" @endif fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    پرداخت سریع بدون ورود مجدد اطلاعات
                </div>
                <div class="flex items-start">
                    <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-500" @endif fill="currentColor" viewBox="0 0 20 20">
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
/* Dynamic styles for select dropdown based on bank color */
@if($bankColor)
select#amount-{{ $service->id }} {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='{{ urlencode($bankColor) }}'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 0.75rem center;
    background-size: 1.25rem;
    padding-left: 2.5rem;
}
@else
/* Default sky blue styles */
select#amount-{{ $service->id }} {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%237c3aed'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 0.75rem center;
    background-size: 1.25rem;
    padding-left: 2.5rem;
}

select#amount-{{ $service->id }}:hover {
    border-color: #7c3aed;
    background-color: #faf5ff;
}

select#amount-{{ $service->id }}:focus {
    background-color: white;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}
@endif

/* Remove default arrow in Firefox */
select#amount-{{ $service->id }} {
    -moz-appearance: none;
}

/* Remove default arrow in IE */
select#amount-{{ $service->id }}::-ms-expand {
    display: none;
}
</style>
@endpush