@extends('front.layouts.app')

@section('title', 'احراز هویت پیامکی - ' . $service->title)

@section('content')
<div class="min-h-screen/2 py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <!-- Header -->
        <div class="text-center mb-4 hidden md:block">
            <div class="mx-auto h-12 w-12 bg-sky-600 rounded-full flex items-center justify-center mb-4">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">احراز هویت پیامکی</h1>
            <p class="text-gray-600">{{ $service->title }}</p>
        </div>

        <!-- SMS Status Card -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
            <div class="flex items-center">
                <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-gray-900">پیامک ارسال شد</p>
                    <p class="text-sm text-gray-500">کد تایید به شماره {{ $mobile ?? 'نامشخص' }} ارسال شد</p>
                </div>
            </div>
            @if($message && $message != 'کد تایید به شماره موبایل شما ارسال شد')
                <div class="bg-sky-50 mt-4 border border-sky-200 rounded-lg p-3">
                    <p class="text-sm text-sky-800">{{ $message }}</p>
                </div>
            @endif
        </div>

        <!-- OTP Form -->
        <form action="{{ route('services.sms-verification.submit', ['service' => $service->slug, 'hash' => $hash ?? 'default']) }}" method="POST" class="bg-white shadow-lg rounded-xl p-6" id="otpForm">
            @csrf
            <input type="hidden" name="national_id" value="{{ $national_id ?? '' }}">
            <input type="hidden" name="mobile" value="{{ $mobile ?? '' }}">
            <input type="hidden" name="scope" value="{{ $scope ?? session('sms_auth_data.scope', '') }}">

            <div class="mb-6">

                <!-- OTP Digits -->
                <div class="flex justify-center gap-3 my-4 flex-wrap flex-row-reverse sm:flex-nowrap">
                    <input type="tel" 
                           class="otp-digit w-10 h-10 sm:w-14 sm:h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300 @error('otp_code') border-red-500 @enderror" 
                           maxlength="1" 
                           data-index="0">
                    <input type="tel" 
                           class="otp-digit w-10 h-10 sm:w-14 sm:h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300 @error('otp_code') border-red-500 @enderror" 
                           maxlength="1" 
                           data-index="1">
                    <input type="tel" 
                           class="otp-digit w-10 h-10 sm:w-14 sm:h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300 @error('otp_code') border-red-500 @enderror" 
                           maxlength="1" 
                           data-index="2">
                    <input type="tel" 
                           class="otp-digit w-10 h-10 sm:w-14 sm:h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300 @error('otp_code') border-red-500 @enderror" 
                           maxlength="1" 
                           data-index="3">
                    <input type="tel" 
                           class="otp-digit w-10 h-10 sm:w-14 sm:h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300 @error('otp_code') border-red-500 @enderror" 
                           maxlength="1" 
                           data-index="4">
                </div>

                <!-- Hidden input for actual OTP value -->
                <input type="hidden" name="otp_code" id="otpValue">
                
                <p class="text-center text-gray-500 text-sm mb-2">کد ۵ رقمی ارسال شده را وارد کنید</p>
                @error('otp_code')
                    <p class="text-center text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- User Info Update Section -->
            <div class="mb-6 bg-sky-50 rounded-lg p-4" id="editSection" style="display: none;">
                <h3 class="text-sm font-medium text-gray-700 mb-3">ویرایش اطلاعات</h3>
                <div class="space-y-3">
                    <div>
                        <label for="edit_national_id" class="block text-xs text-gray-600 mb-1">کد ملی</label>
                        <input type="text" 
                               id="edit_national_id" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                               value="{{ $national_id ?? '' }}"
                               placeholder="کد ملی 10 رقمی">
                    </div>
                    <div>
                        <label for="edit_mobile" class="block text-xs text-gray-600 mb-1">شماره موبایل</label>
                        <input type="text" 
                               id="edit_mobile" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                               value="{{ $mobile ?? '' }}"
                               placeholder="شماره موبایل 11 رقمی">
                    </div>
                    <div class="flex space-x-2 space-x-reverse">
                        <button type="button" 
                                class="flex-1 bg-sky-600 text-white px-3 py-2 rounded-md text-sm hover:bg-sky-700"
                                onclick="updateUserInfo()">
                            ذخیره و ارسال مجدد
                        </button>
                        <button type="button" 
                                class="flex-1 bg-sky-300 text-gray-700 px-3 py-2 rounded-md text-sm hover:bg-gray-400"
                                onclick="cancelEdit()">
                            انصراف
                        </button>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <button type="submit" 
                        class="w-full bg-sky-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-sky-700 focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition-colors"
                        id="verifyBtn">
                    تایید کد و ادامه
                </button>
                
                <button type="button" 
                        class="w-full bg-sky-100 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-sky-200 transition-colors"
                        onclick="showEditSection()">
                    <svg class="inline h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                   تغییر موبایل یا کدملی
                </button>
            </div>

            <!-- Timer and Resend -->
            <div class="mt-6 text-center">
                <div id="timerSection">
                    <p class="text-sm text-gray-600 mb-2">
                        زمان باقی‌مانده: <span id="timer" class="font-mono font-bold text-sky-600">{{ sprintf('%02d:%02d', floor($remainingTime / 60), $remainingTime % 60) }}</span>
                    </p>
                    <p class="text-xs text-gray-500">در صورت دریافت نکردن پیامک، پس از اتمام زمان می‌توانید مجدداً درخواست کنید</p>
                </div>
                
                <button type="button" 
                        id="resendBtn"
                        class="text-sky-600 hover:text-sky-800 text-sm font-medium hidden"
                        onclick="resendSms()">
                    ارسال مجدد پیامک
                </button>
            </div>
        </form>

        <!-- Help Section -->
        <div class="mt-6 bg-white shadow-lg rounded-xl p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-2">راهنما</h3>
            <ul class="text-xs text-gray-600 space-y-1">
                <li>• کد تایید به شماره {{ $mobile ?? 'موبایل شما' }} ارسال شده است</li>
                <li>• کد تایید معمولاً ظرف 1-2 دقیقه دریافت می‌شود</li>
                <li>• در صورت عدم دریافت، پس از اتمام زمان می‌توانید مجدداً درخواست دهید</li>
                <li>• اگر شماره موبایل یا کد ملی اشتباه است، آن را ویرایش کنید</li>
            </ul>
        </div>
    </div>
</div>

<script>
// Get server-calculated remaining time instead of hardcoded value
let timerSeconds = {{ $remainingTime ?? 60 }}; // Use server time or fallback to 60 seconds
let timerInterval;

function startTimer() {
    timerInterval = setInterval(function() {
        timerSeconds--;
        
        const minutes = Math.floor(timerSeconds / 60);
        const seconds = timerSeconds % 60;
        
        const timerEl = document.getElementById('timer');
        if (timerEl) {
            timerEl.textContent = 
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }
        
        if (timerSeconds <= 0) {
            clearInterval(timerInterval);
            const timerSection = document.getElementById('timerSection');
            const resendBtn = document.getElementById('resendBtn');
            
            if (timerSection) timerSection.style.display = 'none';
            if (resendBtn) resendBtn.classList.remove('hidden');
        }
    }, 1000);
}

function showEditSection() {
    const editSection = document.getElementById('editSection');
    if (editSection) {
        editSection.style.display = 'block';
    }
}

function cancelEdit() {
    const editSection = document.getElementById('editSection');
    if (editSection) {
        editSection.style.display = 'none';
    }
}

function updateUserInfo() {
    const newNationalIdEl = document.getElementById('edit_national_id');
    const newMobileEl = document.getElementById('edit_mobile');
    
    if (!newNationalIdEl || !newMobileEl) {
        console.error('Edit form elements not found');
        return;
    }
    
    const newNationalId = newNationalIdEl.value;
    const newMobile = newMobileEl.value;
    
    // Basic validation
    if (newNationalId.length !== 10 || !/^\d{10}$/.test(newNationalId)) {
        alert('کد ملی باید 10 رقم باشد');
        return;
    }
    
    if (!/^09\d{9}$/.test(newMobile)) {
        alert('شماره موبایل نامعتبر است');
        return;
    }
    
    // Update hidden form fields
    const nationalIdInput = document.querySelector('input[name="national_id"]');
    const mobileInput = document.querySelector('input[name="mobile"]');
    
    if (nationalIdInput) nationalIdInput.value = newNationalId;
    if (mobileInput) mobileInput.value = newMobile;
    
    // Hide edit section
    cancelEdit();
    
    // Redirect back to start SMS process with new info
    const baseUrl = window.location.pathname.replace(/\/+$/, '');
    window.location.href = baseUrl + '?' + 
        'national_code=' + encodeURIComponent(newNationalId) + 
        '&mobile=' + encodeURIComponent(newMobile);
}

function resendSms() {
    // Reset timer to 1 minute (higher rate)
    timerSeconds = 60;
    const timerSection = document.getElementById('timerSection');
    const resendBtn = document.getElementById('resendBtn');
    
    if (timerSection) timerSection.style.display = 'block';
    if (resendBtn) resendBtn.classList.add('hidden');
    
    startTimer();
    
    // Redirect to trigger new SMS
    const nationalIdInput = document.querySelector('input[name="national_id"]');
    const mobileInput = document.querySelector('input[name="mobile"]');
    
    if (!nationalIdInput || !mobileInput) {
        console.error('Form inputs not found');
        return;
    }
    
    const nationalId = nationalIdInput.value;
    const mobile = mobileInput.value;
    
    const baseUrl = window.location.pathname.replace(/\/+$/, '');
    window.location.href = baseUrl + '?' + 
        'national_code=' + encodeURIComponent(nationalId) + 
        '&mobile=' + encodeURIComponent(mobile);
}

// OTP digit handling
document.addEventListener('DOMContentLoaded', function() {
    const otpDigits = document.querySelectorAll('.otp-digit');
    const otpValue = document.getElementById('otpValue');
    const verifyBtn = document.getElementById('verifyBtn');
    
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
            if (otpDigits[lastIndex]) {
                otpDigits[lastIndex].focus();
            }
        });
    });
    
    function updateOtpValue() {
        const otp = Array.from(otpDigits).map(digit => digit.value).join('');
        otpValue.value = otp;
        
        // Update visual feedback for filled fields
        otpDigits.forEach((digit, index) => {
            if (digit.value) {
                digit.setAttribute('data-filled', 'true');
                digit.style.backgroundColor = 'rgb(239 246 255)';
                digit.style.borderColor = 'rgb(59 130 246)';
            } else {
                digit.removeAttribute('data-filled');
                digit.style.backgroundColor = 'white';
                digit.style.borderColor = 'rgb(209 213 219)';
            }
        });
        
        // Enable/disable submit button
        if (verifyBtn) {
            verifyBtn.disabled = otp.length !== 5;
            
            if (otp.length === 5) {
                verifyBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                verifyBtn.classList.add('shadow-lg');
                verifyBtn.style.opacity = '1';
                verifyBtn.style.cursor = 'pointer';
            } else {
                verifyBtn.classList.add('opacity-50', 'cursor-not-allowed');
                verifyBtn.classList.remove('shadow-lg');
                verifyBtn.style.opacity = '0.5';
                verifyBtn.style.cursor = 'not-allowed';
            }
        }
    }
    
    // Auto-focus first input
    if (otpDigits[0]) {
        otpDigits[0].focus();
    }
    
    // Start the timer
    startTimer();
    
    // Form submission with loading state
    const otpForm = document.getElementById('otpForm');
    if (otpForm) {
        otpForm.addEventListener('submit', function() {
            if (verifyBtn) {
                verifyBtn.innerHTML = `
                    <svg class="w-5 h-5 inline ml-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    در حال تایید...
                `;
                verifyBtn.disabled = true;
            }
        });
    }
});
</script>

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

    /* Initially disable submit button */
    #verifyBtn {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush

@endsection 