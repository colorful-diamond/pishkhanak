@extends('front.layouts.app')

@section('title', 'احراز هویت پیامکی - ' . $service->title)

@section('content')
<div class="min-h-screen/2 bg-gradient-to-br from-sky-50 via-sky-50 to-purple-50 py-8 px-4">
    <div class="max-w-lg mx-auto">
        
        <!-- Header Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-sky-100 overflow-hidden mb-6">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-sky-500 to-sky-600 p-8 text-center relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="1"/>
                            </pattern>
                        </defs>
                        <rect width="100" height="100" fill="url(#grid)" />
                    </svg>
                </div>
                
                <!-- Icon -->
                <div class="relative z-10 w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                
                <h1 class="relative z-10 text-2xl font-bold text-white mb-2">احراز هویت پیامکی</h1>
                <p class="relative z-10 text-sky-100 text-sm">{{ $service->title }}</p>
            </div>

            <!-- Progress Steps -->
            <div class="p-6 bg-sky-50/50">
                <div class="flex items-center justify-center space-x-4 space-x-reverse">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="mr-2 text-sm text-gray-600">ورود اطلاعات</span>
                    </div>
                    
                    <div class="w-16 h-0.5 bg-green-500"></div>
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center animate-pulse">
                            <span class="text-white text-sm font-bold">2</span>
                        </div>
                        <span class="mr-2 text-sm text-sky-700 font-medium">تایید کد</span>
                    </div>
                    
                    <div class="w-16 h-0.5 bg-sky-200"></div>
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-sky-200 rounded-full flex items-center justify-center">
                            <span class="text-gray-500 text-sm">3</span>
                        </div>
                        <span class="mr-2 text-sm text-gray-400">نتیجه</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- OTP Form Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-sky-100 p-8">
            
            <!-- SMS Status -->
            <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-6 mb-8 text-center">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-green-800 font-semibold mb-2">پیامک ارسال شد!</p>
                <p class="text-green-700 text-sm mb-3">کد تایید ۵ رقمی به شماره زیر ارسال شد:</p>
                <p class="text-green-900 font-bold text-lg tracking-wider font-mono">{{ $mobile }}</p>
                <p class="text-green-600 text-xs mt-2">کد ملی: {{ $national_code }}</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-4 mb-6">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-red-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-red-800 font-semibold text-sm">خطا در تایید کد</span>
                    </div>
                    @foreach ($errors->all() as $error)
                        <p class="text-red-700 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- OTP Form -->
            <form action="{{ route('services.progress.verify-otp', ['service' => $service->slug, 'hash' => $hash]) }}" method="POST" id="otpForm">
                @csrf
                <input type="hidden" name="hash" value="{{ $hash }}">
                <input type="hidden" name="mobile" value="{{ $mobile }}">
                <input type="hidden" name="national_code" value="{{ $national_code }}">

                <!-- OTP Input -->
                <div class="mb-8">
                    <label class="block text-gray-700 font-semibold mb-4 text-center">
                        <svg class="w-5 h-5 inline ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        کد تایید ۵ رقمی
                    </label>

                    <!-- OTP Digits -->
                    <div class="flex justify-center space-x-3 space-x-reverse mb-4">
                        <input type="text" 
                               class="otp-digit w-14 h-14 text-center text-2xl font-bold border-2 border-sky-200 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300" 
                               maxlength="1" 
                               data-index="0">
                        <input type="text" 
                               class="otp-digit w-14 h-14 text-center text-2xl font-bold border-2 border-sky-200 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300" 
                               maxlength="1" 
                               data-index="1">
                        <input type="text" 
                               class="otp-digit w-14 h-14 text-center text-2xl font-bold border-2 border-sky-200 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300" 
                               maxlength="1" 
                               data-index="2">
                        <input type="text" 
                               class="otp-digit w-14 h-14 text-center text-2xl font-bold border-2 border-sky-200 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300" 
                               maxlength="1" 
                               data-index="3">
                        <input type="text" 
                               class="otp-digit w-14 h-14 text-center text-2xl font-bold border-2 border-sky-200 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300" 
                               maxlength="1" 
                               data-index="4">
                    </div>

                    <!-- Hidden input for actual OTP value -->
                    <input type="hidden" name="otp" id="otpValue">
                    
                    <p class="text-center text-gray-500 text-sm">کد ۵ رقمی ارسال شده را وارد کنید</p>
                </div>

                <!-- Timer Section -->
                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-4 mb-6 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-yellow-600 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-yellow-800 font-semibold text-sm">زمان باقی‌مانده</span>
                    </div>
                    <div id="timer" class="text-2xl font-bold text-yellow-700" data-expiry="{{ $expiry }}">05:00</div>
                    <p class="text-yellow-600 text-xs mt-1">در صورت عدم دریافت کد، از دکمه ارسال مجدد استفاده کنید</p>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        id="verifyBtn"
                        class="w-full bg-gradient-to-r from-sky-500 to-sky-600 text-white font-bold py-4 px-6 rounded-2xl hover:from-sky-600 hover:to-sky-700 focus:ring-4 focus:ring-sky-200 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none mb-4">
                    <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    تایید کد
                </button>

                <!-- Back Button -->
                <a href="{{ route('services.show', $service->slug) }}" 
                   class="block w-full text-center bg-sky-100 text-gray-700 font-medium py-3 px-6 rounded-2xl hover:bg-sky-200 transition-all duration-200">
                    <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    بازگشت به فرم
                </a>
            </form>
        </div>

        <!-- Help Section -->
        <div class="bg-sky-50 border border-sky-200 rounded-2xl p-6 mt-6">
            <h3 class="text-sky-800 font-semibold mb-3 flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                راهنمای استفاده
            </h3>
            <ul class="text-sky-700 text-sm space-y-2">
                <li class="flex items-start">
                    <span class="w-2 h-2 bg-sky-400 rounded-full mt-2 ml-2 flex-shrink-0"></span>
                    کد تایید ممکن است تا ۲ دقیقه طول بکشد
                </li>
                <li class="flex items-start">
                    <span class="w-2 h-2 bg-sky-400 rounded-full mt-2 ml-2 flex-shrink-0"></span>
                    اگر کد را دریافت نکردید، پس از انقضای زمان مجدداً تلاش کنید
                </li>
                <li class="flex items-start">
                    <span class="w-2 h-2 bg-sky-400 rounded-full mt-2 ml-2 flex-shrink-0"></span>
                    در صورت بروز مشکل، با پشتیبانی تماس بگیرید
                </li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpDigits = document.querySelectorAll('.otp-digit');
    const otpValue = document.getElementById('otpValue');
    const verifyBtn = document.getElementById('verifyBtn');
    const timerElement = document.getElementById('timer');
    const expiry = parseInt(timerElement.dataset.expiry);
    
    // OTP input handling
    otpDigits.forEach((digit, index) => {
        digit.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }
            
            // Move to next input
            if (value && index < otpDigits.length - 1) {
                otpDigits[index + 1].focus();
            }
            
            updateOtpValue();
        });
        
        digit.addEventListener('keydown', function(e) {
            // Move to previous input on backspace
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                otpDigits[index - 1].focus();
            }
        });
        
        digit.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text');
            const numbers = pastedData.replace(/\D/g, '').slice(0, 5);
            
            numbers.split('').forEach((num, i) => {
                if (otpDigits[i]) {
                    otpDigits[i].value = num;
                }
            });
            
            updateOtpValue();
            
            // Focus last filled input or next empty
            const lastIndex = Math.min(numbers.length - 1, 4);
            otpDigits[lastIndex].focus();
        });
    });
    
    function updateOtpValue() {
        const otp = Array.from(otpDigits).map(digit => digit.value).join('');
        otpValue.value = otp;
        
        // Enable/disable submit button
        verifyBtn.disabled = otp.length !== 5;
        
        if (otp.length === 5) {
            verifyBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            verifyBtn.classList.add('shadow-lg');
        } else {
            verifyBtn.classList.add('opacity-50', 'cursor-not-allowed');
            verifyBtn.classList.remove('shadow-lg');
        }
    }
    
    // Timer countdown
    function updateTimer() {
        const now = Math.floor(Date.now() / 1000);
        const remaining = expiry - now;
        
        if (remaining <= 0) {
            timerElement.textContent = 'منقضی شده';
            timerElement.className = 'text-2xl font-bold text-red-600';
            return;
        }
        
        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        setTimeout(updateTimer, 1000);
    }
    
    updateTimer();
    
    // Auto-focus first input
    otpDigits[0].focus();
    
    // Form submission with loading state
    document.getElementById('otpForm').addEventListener('submit', function() {
        verifyBtn.innerHTML = `
            <svg class="w-5 h-5 inline ml-2 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            در حال تایید...
        `;
        verifyBtn.disabled = true;
    });
});
</script>
@endpush

@push('styles')
<style>
    .otp-digit:focus {
        transform: scale(1.05);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    
    .otp-digit[data-filled="true"] {
        background-color: rgb(239 246 255);
        border-color: rgb(59 130 246);
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .error-shake {
        animation: shake 0.5s ease-in-out;
    }
</style>
@endpush
@endsection 