<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>احراز هویت پیامکی | {{ config('app.name', 'پیشخوانک') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @font-face {
            font-family: 'IRANSansWebFaNum';
            src: url('/resources/fonts/IRANSansWeb.woff2') format('woff2'),
                 url('/resources/fonts/IRANSansWeb.woff') format('woff');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        :root {
            --sky-50: #f0f9ff;
            --sky-100: #e0f2fe;
            --sky-200: #bae6fd;
            --sky-300: #7dd3fc;
            --sky-400: #38bdf8;
            --sky-500: #0ea5e9;
            --sky-600: #0284c7;
            --sky-700: #0369a1;
            --sky-800: #075985;
            --sky-900: #0c4a6e;
            --yellow-100: #fef3c7;
            --yellow-400: #fbbf24;
            --yellow-500: #f59e0b;
            --yellow-600: #d97706;
        }

        * {
            font-family: 'IRANSansWebFaNum', 'IRANSans', system-ui, sans-serif !important;
        }

        body {
            background: linear-gradient(135deg, var(--sky-50) 0%, #ffffff 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            direction: rtl;
        }

        .otp-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .otp-card {
            background: white;
            border-radius: 32px;
            box-shadow: 0 20px 60px rgba(56, 189, 248, 0.1);
            border: 1px solid var(--sky-100);
            overflow: hidden;
            max-width: 480px;
            width: 100%;
            position: relative;
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .otp-header {
            background: linear-gradient(135deg, var(--sky-400), var(--sky-500));
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .otp-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .otp-icon {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .otp-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .otp-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .otp-content {
            padding: 2.5rem 2rem;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .step {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--sky-200);
            margin: 0 6px;
            transition: all 0.3s ease;
        }

        .step.active {
            background: var(--sky-500);
            transform: scale(1.2);
        }

        .step.completed {
            background: var(--yellow-400);
        }

        .phone-display {
            background: var(--sky-50);
            border: 2px solid var(--sky-100);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        .phone-icon {
            color: var(--sky-500);
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .phone-number {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--sky-700);
            margin-bottom: 0.5rem;
            font-family: 'IRANSansWebFaNum', monospace;
            direction: ltr;
            letter-spacing: 1px;
        }

        .edit-phone-btn {
            background: transparent;
            border: 2px solid var(--yellow-400);
            color: var(--yellow-600);
            padding: 0.5rem 1rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .edit-phone-btn:hover {
            background: var(--yellow-400);
            color: white;
            transform: translateY(-2px);
        }

        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 2rem;
        }

        .otp-input {
            width: 60px;
            height: 60px;
            border: 3px solid var(--sky-200);
            border-radius: 16px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sky-700);
            background: white;
            transition: all 0.3s ease;
            outline: none;
        }

        .otp-input:focus {
            border-color: var(--sky-500);
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
            transform: scale(1.05);
        }

        .otp-input.filled {
            background: var(--sky-50);
            border-color: var(--sky-400);
        }

        .timer-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .timer {
            background: var(--yellow-100);
            color: var(--yellow-600);
            padding: 1rem;
            border-radius: 16px;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .resend-section {
            text-align: center;
            color: var(--sky-600);
            font-size: 0.875rem;
        }

        .resend-btn {
            background: transparent;
            border: none;
            color: var(--sky-500);
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            transition: color 0.3s ease;
        }

        .resend-btn:hover {
            color: var(--sky-700);
        }

        .resend-btn:disabled {
            color: var(--sky-300);
            cursor: not-allowed;
            text-decoration: none;
        }

        .verify-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--sky-500), var(--sky-600));
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 16px;
            font-size: 1.125rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .verify-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(56, 189, 248, 0.3);
        }

        .verify-btn:disabled {
            background: var(--sky-300);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .verify-btn .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 0.5rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .verify-btn.loading .loading-spinner {
            display: inline-block;
        }

        .back-btn {
            background: transparent;
            border: 2px solid var(--sky-200);
            color: var(--sky-600);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .back-btn:hover {
            background: var(--sky-50);
            border-color: var(--sky-300);
        }

        .error-message {
            background: #fef2f2;
            border: 2px solid #fecaca;
            color: #dc2626;
            padding: 1rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 500;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .success-message {
            background: #f0fdf4;
            border: 2px solid #bbf7d0;
            color: #166534;
            padding: 1rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 500;
        }

        .edit-form {
            display: none;
            background: var(--sky-50);
            border: 2px solid var(--sky-100);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .edit-form.active {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            color: var(--sky-700);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--sky-200);
            border-radius: 12px;
            font-size: 1rem;
            background: white;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--sky-500);
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
        }

        .form-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .save-btn {
            flex: 1;
            background: var(--yellow-400);
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .save-btn:hover {
            background: var(--yellow-500);
            transform: translateY(-1px);
        }

        .cancel-btn {
            flex: 1;
            background: transparent;
            border: 2px solid var(--sky-200);
            color: var(--sky-600);
            padding: 0.75rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .cancel-btn:hover {
            background: var(--sky-50);
        }

        /* Mobile Responsive */
        @media (max-width: 640px) {
            .otp-container {
                padding: 0.5rem;
            }

            .otp-card {
                border-radius: 24px;
            }

            .otp-content {
                padding: 2rem 1.5rem;
            }

            .otp-inputs {
                gap: 8px;
            }

            .otp-input {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="otp-container">
        <div class="otp-card">
            <!-- Header -->
            <div class="otp-header">
                <div class="otp-icon">
                    <svg width="40" height="40" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                </div>
                <h1 class="otp-title">احراز هویت پیامکی</h1>
                <p class="otp-subtitle">کد تأیید ارسال شده به شماره موبایل خود را وارد کنید</p>
            </div>

            <!-- Content -->
            <div class="otp-content">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step completed"></div>
                    <div class="step active"></div>
                    <div class="step"></div>
                </div>

                <!-- Phone Display -->
                <div class="phone-display">
                    <div class="phone-icon">📱</div>
                    <div class="phone-number" id="displayPhone">{{ $mobile ?? '09123456789' }}</div>
                    <div style="font-size: 0.875rem; color: var(--sky-600); margin-bottom: 1rem;">
                        کد ملی: <span id="displayNationalId" style="font-family: 'IRANSansWebFaNum', monospace;">{{ $nationalId ?? '1234567890' }}</span>
                    </div>
                    <button type="button" class="edit-phone-btn" onclick="showEditForm()">
                        ✏️ ویرایش اطلاعات
                    </button>
                </div>

                <!-- Edit Form (Hidden by default) -->
                <div class="edit-form" id="editForm">
                    <div class="form-group">
                        <label class="form-label">شماره موبایل جدید</label>
                        <input type="tel" class="form-input" id="newMobile" placeholder="09123456789" maxlength="11" pattern="09[0-9]{9}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">کد ملی جدید</label>
                        <input type="text" class="form-input" id="newNationalId" placeholder="1234567890" maxlength="10" pattern="[0-9]{10}">
                    </div>
                    <div class="form-buttons">
                        <button type="button" class="save-btn" onclick="updateInfo()">ذخیره و ارسال مجدد</button>
                        <button type="button" class="cancel-btn" onclick="hideEditForm()">انصراف</button>
                    </div>
                </div>

                <!-- Error/Success Messages -->
                <div id="errorMessage" class="error-message" style="display: none;"></div>
                <div id="successMessage" class="success-message" style="display: none;"></div>

                <!-- OTP Inputs -->
                <div class="otp-inputs">
                    <input type="text" class="otp-input" maxlength="1" data-index="0">
                    <input type="text" class="otp-input" maxlength="1" data-index="1">
                    <input type="text" class="otp-input" maxlength="1" data-index="2">
                    <input type="text" class="otp-input" maxlength="1" data-index="3">
                    <input type="text" class="otp-input" maxlength="1" data-index="4">
                    <input type="text" class="otp-input" maxlength="1" data-index="5">
                </div>

                <!-- Timer Section -->
                <div class="timer-section">
                    <div class="timer" id="timer">
                        ⏱️ زمان باقی‌مانده: <span id="countdown">02:00</span>
                    </div>
                    
                    <div class="resend-section">
                        <span>کد را دریافت نکردید؟</span>
                        <button type="button" class="resend-btn" id="resendBtn" onclick="resendCode()" disabled>
                            ارسال مجدد کد
                        </button>
                    </div>
                </div>

                <!-- Verify Button -->
                <button type="button" class="verify-btn" id="verifyBtn" onclick="verifyOtp()" disabled>
                    <span id="verifyText">تأیید کد</span>
                    <div class="loading-spinner"></div>
                </button>

                <!-- Back Button -->
                <button type="button" class="back-btn" onclick="goBack()">
                    🔙 بازگشت به صفحه قبل
                </button>
            </div>
        </div>
    </div>

    <script>
        // OTP Management
        let otpInputs = document.querySelectorAll('.otp-input');
        let verifyBtn = document.getElementById('verifyBtn');
        let currentOtp = '';
        let countdownTimer;
        let timeLeft = 120; // 2 minutes

        // Initialize OTP inputs
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');
                
                if (this.value.length === 1) {
                    this.classList.add('filled');
                    // Move to next input
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                } else {
                    this.classList.remove('filled');
                }
                
                updateOtpValue();
            });

            input.addEventListener('keydown', function(e) {
                // Handle backspace
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    otpInputs[index - 1].focus();
                    otpInputs[index - 1].value = '';
                    otpInputs[index - 1].classList.remove('filled');
                    updateOtpValue();
                }
                
                // Handle paste
                if (e.key === 'Paste' || (e.ctrlKey && e.key === 'v')) {
                    setTimeout(() => {
                        let pastedData = this.value;
                        if (pastedData.length >= 6) {
                            for (let i = 0; i < 6; i++) {
                                if (otpInputs[i]) {
                                    otpInputs[i].value = pastedData[i] || '';
                                    if (pastedData[i]) {
                                        otpInputs[i].classList.add('filled');
                                    }
                                }
                            }
                            updateOtpValue();
                        }
                    }, 10);
                }
            });
        });

        function updateOtpValue() {
            currentOtp = '';
            otpInputs.forEach(input => {
                currentOtp += input.value;
            });
            
            verifyBtn.disabled = currentOtp.length !== 6;
        }

        // Countdown timer
        function startCountdown() {
            const countdownElement = document.getElementById('countdown');
            const resendBtn = document.getElementById('resendBtn');
            
            countdownTimer = setInterval(() => {
                timeLeft--;
                
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 0) {
                    clearInterval(countdownTimer);
                    countdownElement.textContent = '00:00';
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'ارسال مجدد کد';
                }
            }, 1000);
        }

        // OTP Verification
        function verifyOtp() {
            if (currentOtp.length !== 6) {
                showError('لطفاً کد 6 رقمی را به طور کامل وارد کنید');
                return;
            }

            const verifyBtn = document.getElementById('verifyBtn');
            const verifyText = document.getElementById('verifyText');
            
            verifyBtn.classList.add('loading');
            verifyBtn.disabled = true;
            verifyText.textContent = 'در حال تأیید...';

            // Simulate OTP verification (replace with actual implementation)
            setTimeout(() => {
                // For demo - you should replace this with actual server call
                if (currentOtp === '123456') {
                    showSuccess('کد تأیید شد! در حال انتقال...');
                    setTimeout(() => {
                        // Redirect to original service
                        window.location.href = '{{ $returnUrl ?? "/" }}';
                    }, 1500);
                } else {
                    showError('کد وارد شده صحیح نیست. لطفاً مجدداً تلاش کنید');
                    clearOtpInputs();
                }
                
                verifyBtn.classList.remove('loading');
                verifyBtn.disabled = false;
                verifyText.textContent = 'تأیید کد';
            }, 2000);
        }

        // Resend Code
        function resendCode() {
            const resendBtn = document.getElementById('resendBtn');
            resendBtn.disabled = true;
            resendBtn.textContent = 'در حال ارسال...';
            
            // Reset timer
            timeLeft = 120;
            startCountdown();
            
            // Simulate sending new code
            setTimeout(() => {
                showSuccess('کد جدید ارسال شد');
                clearOtpInputs();
            }, 1500);
        }

        // Edit Form Functions
        function showEditForm() {
            document.getElementById('editForm').classList.add('active');
            document.getElementById('newMobile').value = document.getElementById('displayPhone').textContent;
            document.getElementById('newNationalId').value = document.getElementById('displayNationalId').textContent;
        }

        function hideEditForm() {
            document.getElementById('editForm').classList.remove('active');
        }

        function updateInfo() {
            const newMobile = document.getElementById('newMobile').value;
            const newNationalId = document.getElementById('newNationalId').value;
            
            // Validate inputs
            if (!newMobile.match(/^09[0-9]{9}$/)) {
                showError('شماره موبایل نامعتبر است');
                return;
            }
            
            if (!newNationalId.match(/^[0-9]{10}$/)) {
                showError('کد ملی نامعتبر است');
                return;
            }
            
            // Update display
            document.getElementById('displayPhone').textContent = newMobile;
            document.getElementById('displayNationalId').textContent = newNationalId;
            hideEditForm();
            
            // Restart the SMS auth process with new info
            showSuccess('اطلاعات به‌روزرسانی شد. کد جدید ارسال می‌شود...');
            
            setTimeout(() => {
                // Here you should call your SMS auth service with new credentials
                // For now, just restart the countdown
                timeLeft = 120;
                startCountdown();
                clearOtpInputs();
            }, 1500);
        }

        // Utility Functions
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 5000);
        }

        function showSuccess(message) {
            const successDiv = document.getElementById('successMessage');
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            setTimeout(() => {
                successDiv.style.display = 'none';
            }, 3000);
        }

        function clearOtpInputs() {
            otpInputs.forEach(input => {
                input.value = '';
                input.classList.remove('filled');
            });
            updateOtpValue();
            otpInputs[0].focus();
        }

        function goBack() {
            if (confirm('آیا مطمئن هستید که می‌خواهید به صفحه قبل بازگردید؟')) {
                window.history.back();
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            startCountdown();
            otpInputs[0].focus();
            
            // Auto-fill demo code for testing (remove in production)
            if (window.location.hostname === 'localhost' || window.location.hostname.includes('test')) {
                setTimeout(() => {
                    otpInputs.forEach((input, index) => {
                        if (index < 6) {
                            input.value = '123456'[index];
                            input.classList.add('filled');
                        }
                    });
                    updateOtpValue();
                }, 1000);
            }
        });
    </script>
</body>
</html> 