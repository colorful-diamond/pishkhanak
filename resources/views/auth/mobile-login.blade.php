@extends('front.layouts.auth')

@section('title', 'ورود با موبایل')

@section('content')
<div class="min-h-screen/2/2 bg-gradient-to-br from-sky-50 via-sky-50 to-purple-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-dark-sky-600 mb-2">ورود با موبایل</h1>
            <p class="text-dark-sky-500">شماره موبایل خود را وارد کنید</p>
        </div>

        <!-- Step 1: Mobile Number Input -->
        <div id="mobile-step" class="step">
            <form id="mobile-form">
                <div class="mb-4">
                    <label for="mobile" class="block text-sm font-medium text-dark-sky-500 mb-2">
                        شماره موبایل
                    </label>
                    <input 
                        type="tel" 
                        id="mobile" 
                        name="mobile" 
                        placeholder="09123456789"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 text-left"
                        required
                        pattern="^09[0-9]{9}$"
                        maxlength="11"
                    >
                    <p class="text-xs text-gray-500 mt-1">شماره موبایل خود را با 09 شروع کنید</p>
                </div>

                <button 
                    type="submit" 
                    id="mobile-submit"
                    class="w-full bg-sky-600 text-white py-2 px-4 rounded-md hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    ادامه
                </button>
            </form>
        </div>

        <!-- Step 2: Password Input (if user has password) -->
        <div id="password-step" class="step hidden">
            <form id="password-form">
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-dark-sky-500 mb-2">
                        رمز عبور
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500"
                        required
                    >
                </div>

                <div class="flex items-center mb-4">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember" 
                        class="h-4 w-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500"
                    >
                    <label for="remember" class="mr-2 block text-sm text-dark-sky-600">
                        مرا به خاطر بسپار
                    </label>
                </div>

                <button 
                    type="submit" 
                    id="password-submit"
                    class="w-full bg-sky-600 text-white py-2 px-4 rounded-md hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:opacity-50 disabled:cursor-not-allowed mb-3"
                >
                    ورود با رمز عبور
                </button>

                <button 
                    type="button" 
                    id="use-sms-btn"
                    class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
                >
                    ورود با کد تأیید
                </button>
            </form>
        </div>

        <!-- Step 3: OTP Input -->
        <div id="otp-step" class="step hidden">
            <div class="mb-4 text-center">
                <p class="text-sm text-dark-sky-500 mb-2">کد تأیید به شماره</p>
                <p class="font-semibold text-dark-sky-600" id="display-mobile"></p>
                <p class="text-sm text-dark-sky-500">ارسال شد</p>
            </div>

            <form id="otp-form">
                <div class="mb-4">
                    <label for="otp" class="block text-sm font-medium text-dark-sky-500 mb-2">
                        کد تأیید
                    </label>
                    <input 
                        type="text" 
                        id="otp" 
                        name="otp" 
                        placeholder="12345"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 text-center text-lg tracking-widest"
                        required
                        pattern="[0-9]{5}"
                        maxlength="5"
                    >
                    <p class="text-xs text-gray-500 mt-1">کد 5 رقمی ارسال شده را وارد کنید</p>
                </div>

                <div class="mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-dark-sky-500">زمان باقی‌مانده:</span>
                        <span id="countdown" class="text-sm font-semibold text-sky-600">03:00</span>
                    </div>
                </div>

                <button 
                    type="submit" 
                    id="otp-submit"
                    class="w-full bg-sky-600 text-white py-2 px-4 rounded-md hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:opacity-50 disabled:cursor-not-allowed mb-3"
                >
                    تأیید کد
                </button>

                <button 
                    type="button" 
                    id="resend-otp"
                    class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled
                >
                    ارسال مجدد کد
                </button>
            </form>
        </div>

        <!-- Loading State -->
        <div id="loading" class="hidden text-center py-4">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
            <p class="mt-2 text-dark-sky-500">در حال پردازش...</p>
        </div>

        <!-- Alert Messages -->
        <div id="alert" class="hidden mt-4 p-4 rounded-md">
            <p id="alert-message"></p>
        </div>

        <!-- Back Button -->
        <div class="mt-6 text-center">
            <button 
                id="back-btn" 
                class="text-sky-600 hover:text-sky-800 text-sm hidden"
                onclick="goBack()"
            >
                ← بازگشت
            </button>
        </div>

        <!-- Footer Links -->
        <div class="mt-6 text-center text-sm">
            <p class="text-dark-sky-500 mb-2">حساب کاربری ندارید؟</p>
            <a href="{{ route('app.auth.register') }}" class="text-sky-600 hover:text-sky-800 font-medium">
                ثبت نام کنید
            </a>
        </div>

        <div class="mt-4 text-center text-sm">
            <a href="{{ route('app.auth.login') }}" class="text-dark-sky-500 hover:text-gray-800">
                ورود با ایمیل و رمز عبور
            </a>
        </div>
        <div class="mt-6 text-center text-xs text-gray-500">
            <a href="{{ route('app.page.privacy') }}" class="hover:text-sky-600 transition-colors">حریم خصوصی</a>
            <span class="mx-2">|</span>
            <a href="{{ route('app.page.terms') }}" class="hover:text-sky-600 transition-colors">قوانین و مقررات</a>
        </div>
    </div>
</div>

{{-- login.js is already included in the main layout, no need to duplicate --}}
@endsection 