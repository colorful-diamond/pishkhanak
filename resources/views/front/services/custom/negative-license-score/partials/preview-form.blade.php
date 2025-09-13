<!-- Negative License Score Service Payment Form -->
<div class="lg:sticky lg:top-4">
    <div class="border-2 rounded-xl p-6 shadow-sm bg-gradient-to-br from-red-50 to-red-100 border-red-200">
        <!-- Service Title with Icon -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">استعلام نمره منفی گواهینامه</h3>
            <p class="text-sm text-gray-600">بررسی وضعیت امتیاز منفی رانندگی شما</p>
        </div>

        <!-- Payment Information -->
        <div class="space-y-4 mb-6">
            <div class="bg-white rounded-lg p-4 border border-red-200">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-medium text-gray-700">هزینه سرویس</span>
                    <span class="text-lg font-bold text-red-600">
                        {{ number_format($service->price ?? 16179) }} تومان
                    </span>
                </div>
                
                @if(isset($shortfall) && $shortfall > 0)
                    <!-- Show wallet balance if insufficient -->
                    <div class="border-t pt-3">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">موجودی کیف پول</span>
                            <span class="text-gray-800">{{ number_format(($user->balance ?? 0)) }} تومان</span>
                        </div>
                        <div class="flex justify-between text-sm font-medium">
                            <span class="text-red-600">کمبود موجودی</span>
                            <span class="text-red-600">{{ number_format($shortfall) }} تومان</span>
                        </div>
                    </div>

                    <!-- Suggested Amount for Top-up -->
                    @if(isset($suggestedAmount))
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-700 mb-2">مبلغ پیشنهادی برای شارژ کیف پول:</p>
                            <p class="text-lg font-bold text-blue-800">{{ number_format($suggestedAmount) }} تومان</p>
                        </div>
                    @endif
                @else
                    <!-- Show wallet balance if sufficient -->
                    @if(isset($user) && $user->balance >= ($service->price ?? 16179))
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">موجودی کیف پول</span>
                                <span class="text-green-600 font-medium">{{ number_format($user->balance) }} تومان</span>
                            </div>
                            <div class="mt-2 text-xs text-green-600">
                                ✓ موجودی کافی برای استعلام
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Payment Method Selection -->
        @if(isset($shortfall) && $shortfall > 0)
            <!-- Need to top up wallet first -->
            <form action="{{ route('payments.create') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="service_type" value="wallet_topup">
                <input type="hidden" name="amount" value="{{ $suggestedAmount ?? $shortfall }}">
                <input type="hidden" name="return_to_service" value="{{ $service->slug }}">
                @if(isset($requestHash))
                    <input type="hidden" name="service_request_hash" value="{{ $requestHash }}">
                @endif
                @if(isset($sessionKey))
                    <input type="hidden" name="session_key" value="{{ $sessionKey }}">
                @endif

                <!-- Gateway Selection -->
                @if(isset($gateways) && $gateways->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">انتخاب درگاه پرداخت</label>
                        <div class="space-y-2">
                            @foreach($gateways as $gateway)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="gateway_id" value="{{ $gateway->id }}" class="ml-3" required>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $gateway->name }}</div>
                                        @if($gateway->description)
                                            <div class="text-sm text-gray-600">{{ $gateway->description }}</div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                        شارژ کیف پول
                    </span>
                </button>
            </form>
        @else
            <!-- Proceed with service -->
            <form action="{{ route('services.submit') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                @if(isset($requestHash))
                    <input type="hidden" name="request_hash" value="{{ $requestHash }}">
                @endif
                @if(isset($sessionKey))
                    <input type="hidden" name="session_key" value="{{ $sessionKey }}">
                @endif

                <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        استعلام نمره منفی
                    </span>
                </button>
            </form>
        @endif

        <!-- Service Features -->
        <div class="mt-6 pt-6 border-t border-red-200">
            <h4 class="text-sm font-semibold text-gray-800 mb-3">ویژگی‌های سرویس</h4>
            <div class="space-y-2 text-xs text-gray-600">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>استعلام آنلاین و آنی</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>اطلاعات به‌روز از راهور</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>بررسی وضعیت تعلیق</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>راهنمای کامل قوانین</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>پشتیبانی ۲۴ ساعته</span>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="mt-6 pt-4 border-t border-red-200 text-center">
            <p class="text-xs text-gray-500 mb-2">سوال یا مشکلی دارید؟</p>
            <a href="#" class="text-xs text-red-600 hover:text-red-700 font-medium">
                تماس با پشتیبانی
            </a>
        </div>
    </div>
</div>