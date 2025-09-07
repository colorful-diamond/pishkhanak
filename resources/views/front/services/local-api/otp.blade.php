@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                تایید کد پیامکی
            </h1>
            <p class="text-gray-600">
                کد تایید 5 رقمی ارسال شده را وارد کنید
            </p>
        </div>

        <!-- OTP Form -->
        <div class="bg-white rounded-lg shadow-lg p-6 relative" id="otp-container">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="mr-3">
                            <h3 class="text-sm font-medium text-red-800">خطا در تایید کد</h3>
                            <div class="mt-2 text-sm text-red-700">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Loading Overlay -->
            <div id="loading-overlay" class="hidden absolute inset-0 bg-white bg-opacity-95 flex items-center justify-center z-20 rounded-lg">
                <div class="text-center">
                    <svg class="animate-spin mx-auto h-12 w-12 text-sky-600 mb-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-900">در حال تایید کد...</p>
                    <p class="text-sm text-gray-500 mt-2">لطفاً صبر کنید</p>
                </div>
            </div>

            <form id="otp-form" onsubmit="return false;">
                @csrf
                <input type="hidden" name="hash" value="{{ $localRequest['hash'] }}">
                
                <!-- Hidden fields for original request data -->
                @if(isset($localRequest['request_data']['mobile']))
                    <input type="hidden" name="mobile" value="{{ $localRequest['request_data']['mobile'] }}">
                @endif
                @if(isset($localRequest['request_data']['national_code']))
                    <input type="hidden" name="national_code" value="{{ $localRequest['request_data']['national_code'] }}">
                @endif

                <!-- OTP Message -->
                <div class="text-center mb-6">
                    <p class="text-gray-700">
                        {{ $otpData['message'] ?? 'کد تایید به شماره موبایل شما ارسال شد' }}
                    </p>
                    @if(isset($otpData['expiry']))
                        <p class="text-sm text-gray-500 mt-2">
                            اعتبار کد: {{ $otpData['expiry'] }} دقیقه
                        </p>
                    @endif
                    
                    <!-- Success/Error Messages -->
                    <div id="message-container" class="mt-4 hidden">
                        <div id="message-content" class="p-3 rounded-lg text-sm"></div>
                    </div>
                </div>

                <!-- OTP Input Fields -->
                <div class="mb-6">
                    <div class="flex gap-2 justify-center flex-row-reverse">
                        <input type="text" 
                               maxlength="1" 
                               class="w-12 h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:ring-sky-500 focus:border-sky-500" 
                               id="otp-1" 
                               name="otp_1"
                               autocomplete="off"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               style="direction: ltr !important;">
                        <input type="text" 
                               maxlength="1" 
                               class="w-12 h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:ring-sky-500 focus:border-sky-500" 
                               id="otp-2" 
                               name="otp_2"
                               autocomplete="off"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               style="direction: ltr !important;">
                        <input type="text" 
                               maxlength="1" 
                               class="w-12 h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:ring-sky-500 focus:border-sky-500" 
                               id="otp-3" 
                               name="otp_3"
                               autocomplete="off"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               style="direction: ltr !important;">
                        <input type="text" 
                               maxlength="1" 
                               class="w-12 h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:ring-sky-500 focus:border-sky-500" 
                               id="otp-4" 
                               name="otp_4"
                               autocomplete="off"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               style="direction: ltr !important;">
                        <input type="text" 
                               maxlength="1" 
                               class="w-12 h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:ring-sky-500 focus:border-sky-500" 
                               id="otp-5" 
                               name="otp_5"
                               autocomplete="off"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               style="direction: ltr !important;">
                    </div>
                    <input type="hidden" name="otp" id="otp-combined">
                </div>

                <!-- Submit Button -->
                <button type="button" 
                        onclick="submitOtpAjax()"
                        class="w-full bg-sky-600 text-white py-3 px-4 rounded-lg hover:bg-sky-700 focus:ring-4 focus:ring-sky-200 font-medium transition-colors duration-200"
                        id="submit-btn"
                        disabled>
                    <span id="submit-text">تایید کد</span>
                    <span id="submit-spinner" class="hidden">
                        <svg class="animate-spin inline w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        در حال بررسی...
                    </span>
                </button>
            </form>

            <!-- Resend Section -->
            <div class="mt-6 text-center border-t pt-6">
                <p class="text-gray-600 mb-3">کد را دریافت نکردید؟</p>
                <button onclick="resendOtp()" 
                        class="text-sky-600 hover:text-sky-800 font-medium" 
                        id="resend-btn">
                    ارسال مجدد کد
                </button>
                <div id="resend-timer" class="text-sm text-gray-500 mt-2 hidden">
                                                ارسال مجدد در <span id="timer-seconds">120</span> ثانیه
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-6 bg-yellow-50 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="mr-3">
                    <h3 class="text-sm font-medium text-yellow-800">نکات مهم</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>کد تایید معمولاً ظرف 1 الی ۲ دقیقه ارسال می‌شود</li>
                            <li>کد تایید دارای اعتبار محدود ۲ دقیقه ایست </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let resendTimer = 120;
let resendInterval;
let isSubmitting = false;

document.addEventListener('DOMContentLoaded', function() {
    setupOtpInputs();
    startResendTimer();
});

// AJAX OTP submission function - Compatible with bot communication flow
// 
// Bot Communication Flow:
// 1. AJAX submits OTP → Laravel endpoint
// 2. Laravel dispatches job → Node.js bot 
// 3. Bot processes OTP → Updates Redis with result
// 4. Laravel redirects to progress page (success) or returns error (validation)
// 5. Progress page polls Redis for bot's response
//
async function submitOtpAjax() {
    if (isSubmitting) return;
    
    // Get OTP value
    const otpValue = document.getElementById('otp-combined').value;
    if (otpValue.length !== 5) {
        showInlineMessage('لطفاً کد 5 رقمی را کامل وارد کنید', 'error');
        return;
    }
    
    // Get form data
    const formData = new FormData(document.getElementById('otp-form'));
    formData.set('otp', otpValue);
    
    try {
        isSubmitting = true;
        showLoadingState();
        
        // Submit OTP to Laravel endpoint (which dispatches job to bot)
        const response = await fetch(`{{ route('services.progress.verify-otp', ['service' => $service->slug, 'hash' => $localRequest['hash']]) }}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            redirect: 'manual' // Don't follow redirects automatically
        });
        
        if (response.status === 0 || response.type === 'opaqueredirect') {
            // This means Laravel redirected us to progress page (success case)
            showSuccessMessage('کد تایید ارسال شد. در حال پردازش...');
            
            // Wait a moment then redirect to progress page
            setTimeout(() => {
                window.location.href = `{{ route('services.progress', ['service' => $service->slug, 'hash' => $localRequest['hash']]) }}`;
            }, 1500);
            
        } else if (response.ok) {
            // This shouldn't happen in normal flow, but handle just in case
            showSuccessMessage('کد تایید ارسال شد. در حال انتقال...');
            setTimeout(() => {
                window.location.href = `{{ route('services.progress', ['service' => $service->slug, 'hash' => $localRequest['hash']]) }}`;
            }, 1500);
            
        } else {
            // Error response from Laravel validation
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                const data = await response.json();
                hideLoadingState();
                showInlineMessage(data.message || 'خطا در تایید کد', 'error');
                isSubmitting = false;
                clearOtpInputs();
            } else {
                // HTML error response (likely validation errors)
                hideLoadingState();
                showInlineMessage('خطا در تایید کد. لطفاً مجدداً تلاش کنید', 'error');
                isSubmitting = false;
                clearOtpInputs();
            }
        }
        
    } catch (error) {
        console.error('Error submitting OTP:', error);
        hideLoadingState();
        showInlineMessage('خطا در ارسال کد. لطفاً مجدداً تلاش کنید', 'error');
        isSubmitting = false;
        clearOtpInputs();
    }
}

function showLoadingState() {
    document.getElementById('loading-overlay').classList.remove('hidden');
    document.getElementById('otp-container').classList.add('blur-sm');
}

function hideLoadingState() {
    document.getElementById('loading-overlay').classList.add('hidden');
    document.getElementById('otp-container').classList.remove('blur-sm');
}

function clearOtpInputs() {
    const inputs = document.querySelectorAll('[name^="otp_"]');
    inputs.forEach(input => {
        input.value = '';
    });
    document.getElementById('otp-combined').value = '';
    document.getElementById('submit-btn').disabled = true;
    document.getElementById('otp-1').focus();
}

function showSuccessMessage(message) {
    showInlineMessage(message, 'success');
}

// Show inline message function - Global scope
function showInlineMessage(message, type = 'info') {
    const messageContainer = document.getElementById('message-container');
    const messageContent = document.getElementById('message-content');
    
    if (messageContainer && messageContent) {
        messageContent.textContent = message;
        messageContainer.classList.remove('hidden');
        
        // Set appropriate styling based on type
        messageContent.className = 'p-3 rounded-lg text-sm';
        if (type === 'error') {
            messageContent.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
        } else if (type === 'success') {
            messageContent.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
        } else {
            messageContent.classList.add('bg-sky-50', 'text-sky-700', 'border', 'border-sky-200');
        }
        
        // Auto-hide success/info messages after 3 seconds
        if (type !== 'error') {
            setTimeout(() => {
                messageContainer.classList.add('hidden');
            }, 3000);
        }
    }
}

function setupOtpInputs() {
    const inputs = document.querySelectorAll('[name^="otp_"]');
    const submitBtn = document.getElementById('submit-btn');
    const combinedInput = document.getElementById('otp-combined');

    inputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            // Only allow digits
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Move to next input if current is filled
            if (this.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            
            updateCombinedOtp();
        });

        input.addEventListener('keydown', function(e) {
            // Move to previous input on backspace if current is empty
            if (e.key === 'Backspace' && this.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
            
            // Submit on Enter key if OTP is complete
            if (e.key === 'Enter') {
                const currentOtp = document.getElementById('otp-combined').value;
                if (currentOtp.length === 5 && !submitBtn.disabled) {
                    submitOtpAjax();
                }
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text');
            const digits = pastedData.replace(/[^0-9]/g, '').slice(0, 5);
            
            // Fill inputs with pasted digits
            for (let i = 0; i < digits.length && i < inputs.length; i++) {
                inputs[i].value = digits[i];
            }
            
            updateCombinedOtp();
            
            // Focus the next empty input or the last one
            const nextEmpty = Array.from(inputs).find(input => input.value === '');
            if (nextEmpty) {
                nextEmpty.focus();
            } else {
                inputs[inputs.length - 1].focus();
            }
        });
    });

    function updateCombinedOtp() {
        const otp = Array.from(inputs).map(input => input.value).join('');
        combinedInput.value = otp;
        
        // Enable submit button when all 5 digits are entered
        if (otp.length === 5) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
}

function startResendTimer() {
    const resendBtn = document.getElementById('resend-btn');
    const resendTimerDiv = document.getElementById('resend-timer');
    const timerSeconds = document.getElementById('timer-seconds');

    resendBtn.style.display = 'none';
    resendTimerDiv.classList.remove('hidden');

    resendInterval = setInterval(() => {
        resendTimer--;
        timerSeconds.textContent = resendTimer;

        if (resendTimer <= 0) {
            clearInterval(resendInterval);
            resendBtn.style.display = 'inline';
            resendTimerDiv.classList.add('hidden');
            resendTimer = 120;
        }
    }, 1000);
}

async function resendOtp() {
    try {
        const response = await fetch(`/api/local-requests/{{ $localRequest['hash'] }}/resend-otp`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            // Show success message
            showInlineMessage('کد تایید مجدد ارسال شد', 'success');
            startResendTimer();
        } else {
            showInlineMessage(data.message || 'خطا در ارسال مجدد کد', 'error');
            
            // Show the form again on error
            hideLoadingState();
        }
    } catch (error) {
        console.error('Error resending OTP:', error);
        showInlineMessage('خطا در ارسال مجدد کد', 'error');
        
        // Show the form again on error
        hideLoadingState();
    }
}

// Auto-focus first input
document.getElementById('otp-1').focus();
</script>
@endpush
@endsection 