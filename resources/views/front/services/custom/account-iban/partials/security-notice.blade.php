{{-- Security Notice Component --}}
{{-- Usage: @include('front.services.custom.account-iban.partials.security-notice', ['type' => 'default|compact|detailed']) --}}

@php
    $type = $type ?? 'default';
@endphp

@if($type === 'compact')
    {{-- Compact Security Notice --}}
    <div class="security-notice-compact bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="mr-3">
                <p class="text-sm font-semibold text-green-800">
                    🔐 امن و محفوظ - بدون نیاز به رمز عبور یا اطلاعات محرمانه
                </p>
            </div>
        </div>
    </div>

@elseif($type === 'detailed')
    {{-- Detailed Security Notice --}}
    <div class="security-notice-detailed bg-gradient-to-br from-green-50 to-blue-50 border border-green-200 rounded-xl p-8 mb-8">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-500 to-blue-500 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">امنیت و حریم خصوصی شما</h3>
            <p class="text-gray-600">بالاترین استانداردهای امنیتی در تبدیل حساب به شبا</p>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg p-6 border border-green-200">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center ml-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h4 class="font-bold text-green-800">بدون نیاز به رمز</h4>
                </div>
                <p class="text-green-700 text-sm leading-relaxed">
                    تبدیل حساب به شبا تنها نیاز به شماره حساب دارد. هیچ‌گونه رمز عبور، کد امنیتی یا اطلاعات محرمانه لازم نیست.
                </p>
            </div>

            <div class="bg-white rounded-lg p-6 border border-blue-200">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center ml-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h4 class="font-bold text-blue-800">پردازش آنی</h4>
                </div>
                <p class="text-blue-700 text-sm leading-relaxed">
                    تمام محاسبات در مرورگر شما انجام می‌شود. هیچ‌گونه اطلاعاتی ارسال، ذخیره یا نگهداری نمی‌شود.
                </p>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4 mb-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h5 class="font-bold text-green-800 text-sm">SSL محافظت</h5>
                <p class="text-green-600 text-xs">رمزگذاری 256 بیتی</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <h5 class="font-bold text-blue-800 text-sm">بدون ذخیره</h5>
                <p class="text-blue-600 text-xs">صفر ردپای دیجیتال</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h5 class="font-bold text-purple-800 text-sm">تأیید شده</h5>
                <p class="text-purple-600 text-xs">الگوریتم MOD-97</p>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-500 mt-0.5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h5 class="font-bold text-yellow-800 mb-1">نکته مهم</h5>
                    <p class="text-yellow-700 text-sm">
                        شماره حساب اطلاعات عمومی محسوب می‌شود و برای دریافت پول استفاده می‌شود، نه پرداخت. 
                        شبا همانند شماره حساب، فقط برای دریافت وجه کاربرد دارد.
                    </p>
                </div>
            </div>
        </div>
    </div>

@else
    {{-- Default Security Notice --}}
    <div class="security-notice-default bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6 mb-8">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <div class="mr-4 flex-grow">
                <h3 class="text-lg font-bold text-green-800 mb-2">🔐 امنیت کامل اطلاعات شما</h3>
                <div class="space-y-2">
                    <div class="flex items-center text-green-700">
                        <svg class="w-4 h-4 text-green-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">تبدیل حساب به شبا نیازی به رمز یا اطلاعات محرمانه ندارد</span>
                    </div>
                    <div class="flex items-center text-green-700">
                        <svg class="w-4 h-4 text-green-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">پردازش کاملاً محلی - هیچ اطلاعاتی ارسال نمی‌شود</span>
                    </div>
                    <div class="flex items-center text-green-700">
                        <svg class="w-4 h-4 text-green-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">شماره شبا فقط برای دریافت پول استفاده می‌شود</span>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-green-600">
                    <span class="bg-green-100 px-2 py-1 rounded-full ml-2">✓ SSL محافظت شده</span>
                    <span class="bg-green-100 px-2 py-1 rounded-full ml-2">✓ بدون ذخیره‌سازی</span>
                    <span class="bg-green-100 px-2 py-1 rounded-full">✓ تأیید MOD-97</span>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
.security-notice-compact,
.security-notice-default,
.security-notice-detailed {
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>