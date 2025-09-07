@extends('front.layouts.app')

@section('content')
<div class="bg-sky-50 min-h-screen/2/2">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">پرداخت موفق</h1>
                <p class="text-gray-600">برای دریافت نتیجه سرویس، لطفاً شماره موبایل خود را وارد کنید</p>
            </div>
        </div>

        <div class="max-w-md mx-auto">
            <!-- Payment Success Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">اطلاعات پرداخت</h3>
                </div>
                
                @if($service)
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">سرویس:</span>
                        <span class="text-gray-900 font-medium">{{ $service->title }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">مبلغ پرداخت:</span>
                        <span class="text-gray-900 font-medium">{{ number_format($amount) }} تومان</span>
                    </div>
                </div>
                @endif
                
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex items-center justify-center text-sm text-green-600">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        پرداخت با موفقیت انجام شد
                    </div>
                </div>
            </div>

            <!-- Mobile Number Input & Phone Verification Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Step 1: Mobile Number Input -->
                <div id="mobile-input-step" class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">شماره موبایل خود را وارد کنید</h3>
                    
                    <form id="mobile-form" class="space-y-4">
                        <div>
                            <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">
                                شماره موبایل *
                            </label>
                            <input type="tel" 
                                   id="mobile" 
                                   name="mobile" 
                                   placeholder="09123456789"
                                   class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-left"
                                   pattern="09[0-9]{9}"
                                   maxlength="11"
                                   required>
                            <p class="mt-1 text-xs text-gray-500">شماره موبایل خود را با 09 شروع کنید</p>
                        </div>
                        
                        <button type="submit" 
                                id="send-otp-btn"
                                class="w-full px-4 py-3 bg-sky-600 text-white font-medium rounded-lg hover:bg-sky-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            ارسال کد تایید
                        </button>
                    </form>
                </div>

                <!-- Step 2: OTP Verification (Hidden initially) -->
                <div id="otp-verification-step" class="p-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">تایید شماره موبایل</h3>
                    <p class="text-sm text-gray-600">کد تایید به شماره <span id="display-mobile"></span> ارسال شد</p>
                </div>

                <!-- OTP Form -->
                <div id="otp-form-step" class="p-6 hidden">
                    <form id="otpForm" class="space-y-6">
                        @csrf
                        
                        <!-- OTP Input -->
                        <div id="otpInputSection" style="display: none;">
                            <label for="otp_code" class="block text-sm font-medium text-gray-700 mb-2">کد تایید</label>
                            <div class="relative">
                                <input type="text" 
                                       id="otp_code" 
                                       name="otp_code" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-normal focus:border-primary-normal text-center text-lg font-mono tracking-widest"
                                       placeholder="۱۲۳۴۵۶"
                                       maxlength="6"
                                       pattern="[0-9]{6}">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">کد ۶ رقمی ارسال شده را وارد کنید</p>
                            
                            <!-- Timer -->
                            <div id="timer" class="mt-2 text-sm text-gray-600 text-center" style="display: none;">
                                <span>ارسال مجدد کد در </span>
                                <span id="timerCount" class="font-medium text-primary-normal">120</span>
                                <span> ثانیه</span>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div id="errorMessage" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-400 mt-0.5 ml-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-red-700" id="errorText"></p>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="space-y-3">
                            <button type="button" 
                                    id="sendOtpBtn" 
                                    class="w-full bg-primary-normal text-white py-3 px-4 rounded-lg font-medium hover:bg-primary-dark transition-colors duration-200">
                                ارسال کد تایید
                            </button>
                            
                            <button type="submit" 
                                    id="verifyOtpBtn" 
                                    class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    style="display: none;"
                                    disabled>
                                تایید و ادامه
                            </button>
                            
                            <button type="button" 
                                    id="resendOtpBtn" 
                                    class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-gray-700 transition-colors duration-200"
                                    style="display: none;">
                                ارسال مجدد کد
                            </button>
                        </div>
                    </form>

                    <!-- Info -->
                    <div class="mt-6 p-4 bg-sky-50 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-sky-400 mt-0.5 ml-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-sky-800 mb-1">مراحل بعدی</h4>
                                <ul class="text-sm text-sky-700 space-y-1">
                                    <li>1. کد تایید دریافتی را وارد کنید</li>
                                    <li>2. به صفحه ورود منتقل خواهید شد</li>
                                    <li>3. با همین شماره وارد شوید</li>
                                    <li>4. سرویس شما به طور خودکار پردازش می‌شود</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileForm = document.getElementById('mobile-form');
    const mobileInput = document.getElementById('mobile');
    const sendOtpBtn = document.getElementById('send-otp-btn');
    const mobileInputStep = document.getElementById('mobile-input-step');
    const otpVerificationStep = document.getElementById('otp-verification-step');
    const otpFormStep = document.getElementById('otp-form-step');
    const displayMobile = document.getElementById('display-mobile');
    
    const otpForm = document.getElementById('otpForm');
    const otpInput = document.getElementById('otp_code');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const otpInputSection = document.getElementById('otpInputSection');
    const timer = document.getElementById('timer');
    const timerCount = document.getElementById('timerCount');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    
    let timerInterval;
    let currentMobile = '';
    
    // Mobile form submission
    mobileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const mobile = mobileInput.value.trim();
        
        if (!validateMobile(mobile)) {
            showError('شماره موبایل وارد شده نامعتبر است');
            return;
        }
        
        currentMobile = mobile;
        sendOtp(mobile);
    });
    
    // Mobile input formatting
    mobileInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        e.target.value = value;
    });
    
    // OTP form submission
    if (otpForm) {
        otpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            verifyOtp();
        });
    }
    
    // OTP input formatting and validation
    if (otpInput) {
        otpInput.addEventListener('input', function(e) {
            // Convert Persian/Arabic digits to English
            let value = e.target.value.replace(/[۰-۹]/g, (match) => '۰۱۲۳۴۵۶۷۸۹'.indexOf(match));
            value = value.replace(/[٠-٩]/g, (match) => '٠١٢٣٤٥٦٧٨٩'.indexOf(match));
            value = value.replace(/\D/g, '');
            
            if (value.length > 6) {
                value = value.substring(0, 6);
            }
            
            e.target.value = value;
            
            // Enable verify button when 6 digits entered
        if (e.target.value.length === 6) {
            verifyOtpBtn.disabled = false;
        } else {
            verifyOtpBtn.disabled = true;
        }
    });
    
    // Auto-submit when 6 digits entered
    otpInput.addEventListener('keyup', function(e) {
        if (e.target.value.length === 6) {
            setTimeout(() => {
                verifyOtp();
            }, 500);
        }
    });
    
    // Form submission
    otpForm.addEventListener('submit', function(e) {
        e.preventDefault();
        verifyOtp();
    });
    
    async function sendOtp() {
        try {
            showLoading(sendOtpBtn, 'در حال ارسال...');
            hideError();
            
            const response = await fetch('{{ route("guest.payment.send.verification") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Show OTP input section
                otpInputSection.style.display = 'block';
                sendOtpBtn.style.display = 'none';
                verifyOtpBtn.style.display = 'block';
                
                // Focus on OTP input
                otpInput.focus();
                
                // Start timer
                startTimer(120);
                
                showNotification('کد تایید ارسال شد', 'success');
            } else {
                showError(data.message);
            }
        } catch (error) {
            showError('خطا در ارسال کد تایید');
        } finally {
            hideLoading(sendOtpBtn, 'ارسال کد تایید');
        }
    }
    
    async function verifyOtp() {
        const otpCode = otpInput.value;
        
        if (otpCode.length !== 6) {
            showError('لطفاً کد ۶ رقمی را کامل وارد کنید');
            return;
        }
        
        try {
            showLoading(verifyOtpBtn, 'در حال تایید...');
            hideError();
            
            const response = await fetch('{{ route("guest.payment.verify.otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    otp_code: otpCode
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                showError(data.message);
                // Clear OTP input on error
                otpInput.value = '';
                otpInput.focus();
            }
        } catch (error) {
            showError('خطا در تایید کد');
        } finally {
            hideLoading(verifyOtpBtn, 'تایید و ادامه');
        }
    }
    
    function startTimer(seconds) {
        let timeLeft = seconds;
        timer.style.display = 'block';
        resendOtpBtn.style.display = 'none';
        
        timerInterval = setInterval(() => {
            timerCount.textContent = timeLeft;
            timeLeft--;
            
            if (timeLeft < 0) {
                clearInterval(timerInterval);
                timer.style.display = 'none';
                resendOtpBtn.style.display = 'block';
            }
        }, 1000);
    }
    
    function showError(message) {
        errorText.textContent = message;
        errorMessage.classList.remove('hidden');
    }
    
    function hideError() {
        errorMessage.classList.add('hidden');
    }
    
    function showLoading(button, text) {
        button.disabled = true;
        button.innerHTML = '<div class="flex items-center justify-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>' + text + '</div>';
    }
    
    function hideLoading(button, text) {
        button.disabled = false;
        button.textContent = text;
    }
    
    function showNotification(message, type) {
        // Simple notification - you can replace with your existing notification system
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white shadow-lg`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endsection 