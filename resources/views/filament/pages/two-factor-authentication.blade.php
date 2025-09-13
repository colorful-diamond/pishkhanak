<x-filament-panels::page>
    <div class="space-y-6">
        @if(!auth()->user()->two_factor_enabled)
            <!-- Two-Factor Disabled State -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <div class="mr-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">احراز هویت دو مرحله‌ای غیرفعال است</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">برای افزایش امنیت حساب خود، احراز هویت دو مرحله‌ای را فعال کنید.</p>
                    </div>
                </div>
            </div>
        @elseif(!auth()->user()->two_factor_confirmed_at)
            <!-- Two-Factor Setup State -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.239"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">تنظیم احراز هویت دو مرحله‌ای</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        کد QR زیر را با اپلیکیشن احراز هویت خود (مانند Google Authenticator، Authy) اسکن کنید و سپس کد تایید را وارد نمایید.
                    </p>
                    
                    @php
                        $qrCode = null;
                        try {
                            $qrCode = $this->getQrCodeSvg();
                        } catch (\Exception $e) {
                            // Handle QR code generation error silently
                        }
                    @endphp
                    
                    @if($qrCode)
                        <div class="flex justify-center mb-6">
                            <div class="bg-white p-4 rounded-lg shadow border">
                                {!! $qrCode !!}
                            </div>
                        </div>
                    @else
                        <div class="flex justify-center mb-6">
                            <div class="bg-gray-100 p-8 rounded-lg">
                                <p class="text-gray-600 text-center">قادر به تولید کد QR نیستیم. لطفاً صفحه را رفرش کنید.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Confirmation Form -->
                    <div class="max-w-sm mx-auto">
                        {{ $this->form }}
                    </div>
                </div>
            </div>
        @else
            <!-- Two-Factor Enabled State -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <div class="mr-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">احراز هویت دو مرحله‌ای فعال است</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">حساب شما با احراز هویت دو مرحله‌ای محافظت می‌شود.</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">تایید شده در: {{ auth()->user()->two_factor_confirmed_at?->format('Y/m/d H:i') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($showRecoveryCodes) && $showRecoveryCodes && isset($recoveryCodes) && !empty($recoveryCodes))
            <!-- Recovery Codes -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="mr-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">کدهای بازیابی</h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p class="mb-3">این کدها را در مکان امنی ذخیره کنید. در صورت از دست دادن دستگاه احراز هویت می‌توانید از آنها استفاده کنید:</p>
                            <div class="bg-white dark:bg-gray-700 rounded p-4 grid grid-cols-2 gap-2 text-xs font-mono">
                                @foreach($recoveryCodes as $code)
                                    <div class="text-gray-900 dark:text-gray-100">{{ $code }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>