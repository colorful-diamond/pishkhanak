@extends('front.layouts.auth')

@section('title', 'ورود | ثبت‌نام - پیشخوانک')

@section('content')
<div class="min-h-screen/2/2 flex items-center justify-center py-4 px-4 sm:px-6 lg:px-8 bg-sky-50">
    <div class="relative max-w-md w-full">
        <!-- Guest Payment Success Alert -->
        @if(session('guest_payment_success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400 mt-0.5 ml-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-green-800 mb-1">پرداخت با موفقیت انجام شد!</h4>
                    <p class="text-sm text-green-700">لطفاً با شماره موبایل خود وارد شوید تا درخواست شما پردازش شود.</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400 mt-0.5 ml-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 mt-0.5 ml-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Login Card -->
        <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- CSRF Token (hidden input as backup) -->
            <input type="hidden" name="csrf_token" value="{{ csrf_token() }}">
            
            <!-- Mobile Step -->
            <div id="mobileStep" class="step-content">
                <div class="text-center mb-2">
                    <a href="/" class="inline-block mb-2 group">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="پیشخوانک" class="h-12 mx-auto filter">
                    </a>
                    <p class="text-dark-sky-500 text-sm leading-relaxed">برای ورود یا ثبت‌نام، شماره موبایل خود را وارد نمایید</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="phoneNumber" class="block text-sm font-semibold text-gray-800 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span>شماره همراه</span>
                            </span>
                        </label>
                        <div class="relative">
                            <input type="tel" id="phoneNumber" name="phoneNumber" 
                                   class="w-full px-4 py-3 bg-white rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500 transition-colors placeholder-gray-400 text-gray-700" 
                                   placeholder="09111234567" dir="ltr">
                        </div>
                        <div id="phoneNumberError" class="hidden text-red-500 text-sm flex items-center mt-2 bg-red-50 p-3 rounded-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>لطفا شماره موبایل معتبر وارد کنید.</span>
                        </div>
                    </div>

                    <button id="mobileSubmitBtn" 
                            class="w-full px-6 py-4 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-lg shadow-sm hover:shadow-md transition-colors flex items-center justify-center gap-2">
                        <span>ادامه</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Password Step -->
            <div id="passwordStep" class="step-content hidden">
                <!-- Header Section with User Info -->
                <div class="text-center mb-6 bg-sky-50 p-4 rounded-lg border border-gray-200">
                    <div class="w-12 h-12 bg-sky-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-sky-700 font-medium">کاربر گرامی، خوش آمدید!</p>
                    <p class="font-bold text-sky-900" id="displayMobileNumber"></p>
                    <p class="text-xs text-sky-600">انتخاب روش ورود</p>
                </div>
                
                <!-- Password Input Section -->
                <div class="space-y-4 mb-6">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-800 mb-2">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span>رمز عبور</span>
                            </span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" 
                                   class="w-full px-4 py-3 pr-12 bg-white rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500 transition-colors placeholder-gray-400"
                                   placeholder="رمز عبور خود را وارد کنید">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                        </div>
                        <div id="passwordError" class="hidden text-red-500 text-sm flex items-center mt-2 bg-red-50 p-3 rounded-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>رمز عبور اشتباه است</span>
                        </div>
                    </div>
                    
                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <span class="text-sm text-dark-sky-500 ml-2">مرا به خاطر بسپار</span>
                            <input type="checkbox" id="remember" name="remember" 
                                   class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500 focus:ring-2">
                        </label>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button id="passwordSubmitBtn" 
                            class="w-full px-6 py-4 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-lg shadow-sm hover:shadow-md transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>ورود با رمز عبور</span>
                    </button>
                    
                    <div class="relative py-2">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">یا</span>
                        </div>
                    </div>
                    
                    <button id="useSmsBtn" 
                            class="w-full px-6 py-3 bg-white border border-gray-300 text-gray-700 hover:bg-sky-50 font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>ورود با کد تأیید پیامکی</span>
                    </button>
                </div>
                
                <!-- Back Button -->
                <div class="mt-4 text-center">
                    <button id="backToMobileBtn" 
                            class="text-gray-500 hover:text-gray-700 text-sm font-medium transition-colors flex items-center justify-center gap-1 mx-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span>تغییر شماره موبایل</span>
                    </button>
                </div>
            </div>

            <!-- OTP Step -->
            <div id="otpStep" class="step-content hidden">
                <!-- Header Section with SMS Icon -->
                <div class="text-center mb-6 bg-sky-50 p-4 rounded-lg border border-gray-200">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-green-700 font-medium">کد تأیید ارسال شد</p>
                    <p class="font-bold text-green-900" id="phoneNumberDisplay"></p>
                    <p class="text-xs text-green-600">لطفاً کد ۵ رقمی را وارد کنید</p>
                </div>
                
                <!-- Timer Section -->
                <div class="bg-sky-50 p-4 rounded-lg border border-gray-200 mb-6">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-sky-500 rounded-full animate-pulse"></div>
                            <span class="text-sm text-sky-700 font-medium">زمان باقی‌مانده</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span id="countdown" class="font-bold text-sky-700">03:00</span>
                        </div>
                    </div>
                </div>
                
                <!-- OTP Input Section -->
                <div class="space-y-4 mb-6">
                    <div>
                        <label for="otpCode" class="block text-sm font-semibold text-gray-800 mb-2 text-center">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                <span>کد تأیید</span>
                            </span>
                        </label>
                        <div class="relative">
                            <input type="text" id="otpCode" name="otpCode" 
                                   class="w-full px-4 py-3 bg-white rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500 transition-colors text-center text-xl tracking-[0.4em] font-bold placeholder-gray-300"
                                   placeholder="●●●●●" maxlength="5" dir="ltr">
                        </div>
                        <div id="otpError" class="hidden text-red-500 text-sm flex items-center justify-center mt-2 bg-red-50 p-3 rounded-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>کد تأیید نامعتبر است</span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button id="otpSubmitBtn" 
                            class="w-full px-6 py-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm hover:shadow-md transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>تأیید و ورود</span>
                    </button>
                    
                    <div class="flex items-center justify-center">
                        <button id="resendOtpBtn" 
                                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-sky-50 font-medium rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>ارسال مجدد</span>
                        </button>
                    </div>
                </div>
                
                <!-- Back Button -->
                <div class="mt-4 text-center">
                    <button id="changePhoneBtn" 
                            class="text-gray-500 hover:text-gray-700 text-sm font-medium transition-colors flex items-center justify-center gap-1 mx-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span>تغییر شماره موبایل</span>
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="step-content hidden text-center py-8">
                <div class="flex flex-col items-center space-y-4">
                    <div class="relative">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-sky-200 border-t-sky-600"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-2 h-2 bg-sky-600 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sky-700 font-medium">در حال پردازش...</p>
                        <p class="text-sm text-gray-500">لطفاً صبر کنید</p>
                    </div>
                </div>
            </div>

            <!-- Terms Section -->
            <div id="termsSection" class="text-center text-sm bg-sky-50 p-4 rounded-lg mt-6 border border-gray-200">
                <span class="text-gray-700">با ورود به پیشخوانک، </span>
                <button id="showRulesBtn" class="text-sky-600 hover:text-sky-700 font-medium transition-colors underline decoration-dotted">قوانین و مقررات</button>
                <span class="text-gray-700"> و شرایط استفاده از آن را می‌پذیرم.</span>
            </div>

            <!-- Rules Content -->
            <div id="rulesContent" class="hidden mt-4 p-4 bg-sky-50 rounded-lg border border-gray-200">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <span class="text-gray-700 font-medium">قوانین و مقررات پیشخوانک</span>
                </div>
                <div class="text-gray-700 text-sm leading-relaxed max-h-40 overflow-y-auto">
                    با ورود به پیشخوانک، شما موافقت می‌کنید که از تمامی خدمات ما بر اساس قوانین و مقررات تعریف شده استفاده نمایید. لطفاً قبل از استفاده از خدمات، قوانین را به دقت مطالعه فرمایید.
                    <br><br>
                    ۱. کاربر متعهد است از اطلاعات صحیح و معتبر استفاده کند.
                    <br>
                    ۲. هرگونه سوء استفاده از سرویس پیگرد قانونی دارد.
                    <br>
                    ۳. حریم خصوصی کاربران محفوظ و محرمانه نگه داشته می‌شود.
                </div>
            </div>
        </div>


    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/login.js'])
@endpush 