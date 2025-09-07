// Ensure the script runs after DOM is fully loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeModal);
} else {
    initializeModal();
}

function initializeModal() {
    // No need for modal elements since we're on a dedicated page
    // Just initialize the form elements directly
    console.log('Initializing login page...');
    
    // Step elements
    const mobileStep = document.getElementById('mobileStep');
    const passwordStep = document.getElementById('passwordStep');
    const otpStep = document.getElementById('otpStep');
    const loadingState = document.getElementById('loadingState');
    const termsSection = document.getElementById('termsSection');
    
    // Form inputs
    const phoneNumberInput = document.getElementById('phoneNumber');
    const passwordInput = document.getElementById('password');
    const otpCodeInput = document.getElementById('otpCode');
    const rememberCheckbox = document.getElementById('remember');
    
    // Buttons - moved before null checks
    const mobileSubmitBtn = document.getElementById('mobileSubmitBtn');
    const passwordSubmitBtn = document.getElementById('passwordSubmitBtn');
    const otpSubmitBtn = document.getElementById('otpSubmitBtn');
    const useSmsBtn = document.getElementById('useSmsBtn');
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const changePhoneBtn = document.getElementById('changePhoneBtn');
    const backToMobileBtn = document.getElementById('backToMobileBtn');
    const showRulesBtn = document.getElementById('showRulesBtn');
    
    // Display elements
    const displayMobileNumber = document.getElementById('displayMobileNumber');
    const phoneNumberDisplay = document.getElementById('phoneNumberDisplay');
    const countdown = document.getElementById('countdown');
    const rulesContent = document.getElementById('rulesContent');
    
    // Check if essential elements exist
    if (!phoneNumberInput) {
        console.error('Phone number input not found!');
        return;
    }
    
    if (!mobileSubmitBtn) {
        console.error('Mobile submit button not found!');
        return;
    }
    
    if (!mobileStep || !passwordStep || !otpStep || !loadingState) {
        console.error('One or more step elements not found!');
        return;
    }
    
    console.log('✅ All essential elements found, initializing login page...');
    
    // State variables
    let currentMobile = '';
    let countdownInterval = null;
    let currentStep = 'mobile';
    let authType = 'login'; // 'login' or 'register'
    let pendingRequests = new Map(); // Debounce map for API requests
    
    // ========== PAGE INITIALIZATION ==========
    
    // Validate CSRF token on page load
    validateCsrfToken();
    
    // Initialize the page in mobile step
    resetToMobileStep();

    function resetForms() {
        phoneNumberInput.value = '';
        passwordInput.value = '';
        otpCodeInput.value = '';
        rememberCheckbox.checked = false;
        currentMobile = '';
        currentStep = 'mobile';
        authType = 'login';
        hideAllErrors();
    }

    // ========== STEP NAVIGATION ==========
    
    function showStep(step) {
        // Hide all steps
        mobileStep.classList.add('hidden');
        passwordStep.classList.add('hidden');
        otpStep.classList.add('hidden');
        loadingState.classList.add('hidden');
        
        // Show/hide terms section based on step
        if (step === 'mobile') {
            termsSection.classList.remove('hidden');
        } else {
            termsSection.classList.add('hidden');
        }
        
        // Show the requested step
        switch(step) {
            case 'mobile':
                mobileStep.classList.remove('hidden');
                // Reset mobile button state
                setButtonLoading(mobileSubmitBtn, false);
                mobileSubmitBtn.innerHTML = '<span>ادامه</span><x-tabler-arrow-left class="w-5 h-5" />';
                break;
            case 'password':
                passwordStep.classList.remove('hidden');
                // Reset password button state
                setButtonLoading(passwordSubmitBtn, false);
                passwordSubmitBtn.innerHTML = '<span>ورود با رمز عبور</span><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 512 512"><g><g><path d="M334.974,0c-95.419,0-173.049,77.63-173.049,173.049c0,21.213,3.769,41.827,11.211,61.403L7.672,399.928c-2.365,2.366-3.694,5.573-3.694,8.917v90.544c0,6.965,5.646,12.611,12.611,12.611h74.616c3.341,0,6.545-1.325,8.91-3.686l25.145-25.107c2.37-2.366,3.701-5.577,3.701-8.925v-30.876h30.837c6.965,0,12.611-5.646,12.611-12.611v-12.36h12.361c6.964,0,12.611-5.646,12.611-12.611v-27.136h27.136c3.344,0,6.551-1.329,8.917-3.694l40.121-40.121c19.579,7.449,40.196,11.223,61.417,11.223c95.419,0,173.049-77.63,173.049-173.049C508.022,77.63,430.393,0,334.974,0z M334.974,320.874c-20.642,0-40.606-4.169-59.339-12.393c-4.844-2.126-10.299-0.956-13.871,2.525c-0.039,0.037-0.077,0.067-0.115,0.106l-42.354,42.354h-34.523c-6.965,0-12.611,5.646-12.611,12.611v27.136H159.8c-6.964,0-12.611,5.646-12.611,12.611v12.36h-30.838c-6.964,0-12.611,5.646-12.611,12.611v38.257l-17.753,17.725H29.202v-17.821l154.141-154.14c4.433-4.433,4.433-11.619,0-16.051s-11.617-4.434-16.053,0L29.202,436.854V414.07l167.696-167.708c0.038-0.038,0.067-0.073,0.102-0.11c3.482-3.569,4.656-9.024,2.53-13.872c-8.216-18.732-12.38-38.695-12.38-59.33c0-81.512,66.315-147.827,147.827-147.827S482.802,91.537,482.802,173.05C482.8,254.56,416.484,320.874,334.974,320.874z"></path></g></g><g><g><path d="M387.638,73.144c-26.047,0-47.237,21.19-47.237,47.237s21.19,47.237,47.237,47.237s47.237-21.19,47.237-47.237S413.686,73.144,387.638,73.144z M387.638,142.396c-12.139,0-22.015-9.876-22.015-22.015s9.876-22.015,22.015-22.015s22.015,9.876,22.015,22.015S399.777,142.396,387.638,142.396z"></path></g></g></svg>';
                break;
            case 'otp':
                otpStep.classList.remove('hidden');
                // Reset OTP button state
                setButtonLoading(otpSubmitBtn, false);
                otpSubmitBtn.innerHTML = '<span>تأیید و ورود</span><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                break;
            case 'loading':
                loadingState.classList.remove('hidden');
                break;
        }
        
        currentStep = step;
        console.log(`🔄 [DEBUG] Switched to step: ${step}`);
    }
    
    function resetToMobileStep() {
        showStep('mobile');
        phoneNumberInput.focus();
    }

    // ========== UTILITY FUNCTIONS ==========
    
    function validateCsrfToken() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                         document.querySelector('input[name="csrf_token"]')?.value ||
                         document.querySelector('input[name="_token"]')?.value;
        
        if (!csrfToken) {
            console.error('🔒 [ERROR] CSRF token not found during page initialization!');
            showNotification('خطای امنیتی: لطفاً صفحه را تازه‌سازی کنید', 'error');
            return false;
        }
        
        console.log('🔒 [DEBUG] CSRF token validated successfully on page load');
        return true;
    }
    
    function convertToPersianNumbers(input) {
        const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return input.replace(/[0-9]/g, function(w) {
            return persianNumbers[+w];
        });
    }

    function convertToEnglishNumbers(input) {
        const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return input.replace(/[۰-۹]/g, function(w) {
            return persianNumbers.indexOf(w).toString();
        });
    }

    function validatePhoneNumber(phoneNumber) {
        const englishNumber = convertToEnglishNumbers(phoneNumber);
        return /^09\d{9}$/.test(englishNumber);
    }
    
    function formatPhoneNumber(phoneNumber) {
        const cleaned = convertToEnglishNumbers(phoneNumber).replace(/\D/g, '');
        return cleaned;
    }

    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            const span = errorElement.querySelector('span');
            if (span) span.textContent = message;
            errorElement.classList.remove('hidden');
        }
    }

    function hideError(elementId) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.classList.add('hidden');
        }
    }
    
    function hideAllErrors() {
        ['phoneNumberError', 'passwordError', 'otpError'].forEach(hideError);
    }

    function setButtonLoading(button, loading, text = '') {
        if (loading) {
            button.disabled = true;
            button.innerHTML = `
                <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                ${text}
            `;
        } else {
            button.disabled = false;
        }
    }

    // ========== API FUNCTIONS ==========
    
    // Function to refresh CSRF token if needed
    async function refreshCsrfToken() {
        try {
            const response = await fetch('/csrf-token', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                // Update the meta tag with the new token
                let metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', data.csrf_token);
                    console.log('🔒 [DEBUG] CSRF token refreshed successfully');
                    return data.csrf_token;
                }
            }
        } catch (error) {
            console.error('🔒 [ERROR] Failed to refresh CSRF token:', error);
        }
        return null;
    }
    
    async function makeRequest(url, data) {
        // Create a unique key for this request
        const requestKey = url + JSON.stringify(data);
        console.log(`🌐 [DEBUG] makeRequest called:`, { url, data, requestKey, timestamp: new Date().toISOString() });
        
        // If the same request is already pending, return the existing promise
        if (pendingRequests.has(requestKey)) {
            console.log(`⚠️ [DEBUG] Duplicate request prevented:`, requestKey);
            return pendingRequests.get(requestKey);
        }
        
        // Enhanced CSRF token retrieval with better error handling
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                         document.querySelector('input[name="csrf_token"]')?.value ||
                         document.querySelector('input[name="_token"]')?.value;
        
        if (!csrfToken) {
            console.error('🔒 [ERROR] CSRF token not found in any of the expected locations!');
            console.error('Available meta tags:', Array.from(document.querySelectorAll('meta')).map(m => ({ name: m.name, content: m.content?.substring(0, 20) + '...' })));
            showNotification('خطای امنیتی: توکن CSRF یافت نشد', 'error');
            throw new Error('CSRF token not found');
        }
        
        console.log(`🔒 [DEBUG] CSRF token found:`, csrfToken.substring(0, 20) + '...');
        
        // Create the request promise
        const requestPromise = fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        }).then(async (response) => {
            console.log(`📡 [DEBUG] Response received:`, { url, status: response.status, statusText: response.statusText });
            
            // Check if response is JSON first
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Response is not JSON. Content-Type:', contentType);
                console.error('Response text:', text.substring(0, 200));
                throw new Error('Server returned HTML instead of JSON. Check server logs.');
            }
            
            // Try to parse JSON regardless of status
            let jsonData;
            try {
                jsonData = await response.json();
            } catch (error) {
                const text = await response.text();
                console.error('JSON parse error:', error);
                console.error('Response text:', text);
                throw new Error('Invalid JSON response from server');
            }
            
            // If response is not ok, but we have JSON, throw with the parsed data
            if (!response.ok) {
                const error = new Error(jsonData.message || `HTTP ${response.status}`);
                error.status = response.status;
                error.data = jsonData;
                throw error;
            }

            return jsonData;
        }).catch(async (error) => {
            // Handle CSRF token mismatch error (419)
            if (error.status === 419) {
                console.log('🔒 [DEBUG] CSRF token mismatch detected, attempting to refresh...');
                
                // Try to refresh the CSRF token
                const newToken = await refreshCsrfToken();
                if (newToken) {
                    console.log('🔒 [DEBUG] Retrying request with new CSRF token...');
                    
                    // Remove from pending requests to allow retry
                    pendingRequests.delete(requestKey);
                    
                    // Retry the request with the new token
                    return makeRequest(url, data);
                } else {
                    console.error('🔒 [ERROR] Failed to refresh CSRF token, cannot retry');
                    showNotification('خطای امنیتی: لطفاً صفحه را تازه‌سازی کنید', 'error');
                }
            }
            
            // Re-throw the error for other cases
            throw error;
        }).finally(() => {
            // Remove from pending requests after completion
            pendingRequests.delete(requestKey);
        });
        
        // Store the promise to prevent duplicates
        pendingRequests.set(requestKey, requestPromise);
        
        return requestPromise;
    }

    async function checkMobileLogin(mobile) {
        showStep('loading');
        
        try {
            const data = await makeRequest('/user/login', { 
                mobile: mobile,
                step: 'check_mobile'
            });
            
            // Handle successful login with redirect
            if (data.success && data.action === 'redirect') {
                let message = 'با موفقیت وارد شدید';
                if (data.guest_payment_processed && data.guest_payment_message) {
                    message = data.guest_payment_message;
                }
                showNotification(message, 'success');
                setTimeout(() => window.location.href = data.redirect, 1500);
                return;
            }
            
            // Handle all action types (regardless of success value)
            if (data.action) {
                switch(data.action) {
                    case 'password_required':
                        displayMobileNumber.textContent = convertToPersianNumbers(mobile);
                        showStep('password');
                        passwordInput.focus();
                        break;
                        
                    case 'sms_required':
                        authType = 'login';
                        await sendOTP(mobile);
                        break;
                        
                    case 'register':
                        authType = 'register';
                        await sendOTP(mobile);
                        break;
                        
                    default:
                        showStep('mobile');
                        showNotification(data.message || 'خطای غیرمنتظره', 'error');
                }
            } else {
                // No action provided, show error
                showStep('mobile');
                showNotification(data.message || 'خطای غیرمنتظره', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showStep('mobile');
            
            // Handle validation errors (422)
            if (error.status === 422 && error.data) {
                // Show validation error in the mobile input field
                if (error.data.errors && error.data.errors.mobile) {
                    showError('phoneNumberError', error.data.errors.mobile[0]);
                } else {
                    showError('phoneNumberError', error.data.message);
                }
                showNotification(error.message, 'error');
            } else {
                // Show generic error
                showNotification('خطای ارتباط با سرور', 'error');
            }
        }
    }

    async function loginWithPassword(mobile, password, remember) {
        setButtonLoading(passwordSubmitBtn, true, 'در حال ورود...');
        
        try {
            const data = await makeRequest('/user/login', { 
                mobile: mobile, 
                password: password, 
                remember: remember,
                step: 'password_login'
            });
            
            if (data.success) {
                showNotification('با موفقیت وارد شدید', 'success');
                setTimeout(() => window.location.href = data.redirect, 1000);
            } else {
                showError('passwordError', data.message);
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            
            // Handle validation errors (422)
            if (error.status === 422 && error.data) {
                if (error.data.errors && error.data.errors.password) {
                    showError('passwordError', error.data.errors.password[0]);
                } else if (error.data.errors && error.data.errors.mobile) {
                    showError('passwordError', error.data.errors.mobile[0]);
                } else {
                    showError('passwordError', error.data.message);
                }
                showNotification(error.message, 'error');
            } else {
                showNotification('خطای ارتباط با سرور', 'error');
            }
        }
        
        setButtonLoading(passwordSubmitBtn, false);
        passwordSubmitBtn.innerHTML = '<span class="text-center text-white text-base font-medium capitalize leading-normal">ورود با رمز عبور</span>';
    }

    async function sendOTP(mobile) {
        // Prevent multiple simultaneous calls
        if (mobileSubmitBtn.disabled || otpSubmitBtn.disabled) {
            return;
        }

        showStep('loading');

        // Disable relevant buttons during API call
        setButtonLoading(mobileSubmitBtn, true, 'در حال ارسال...');
        if (resendOtpBtn) {
            resendOtpBtn.disabled = true;
        }

        try {
            const data = await makeRequest('/user/send-otp', {
                mobile, 
                type: authType 
            });

            if (data.success) {
                phoneNumberDisplay.textContent = convertToPersianNumbers(mobile);
                showStep('otp');
                startCountdown(180); // 3 minutes
                otpCodeInput.focus();
                showNotification(data.message, 'success');
            } else {
                showStep('mobile');
                showNotification(data.message, 'error');
                
                if (data.wait_time) {
                    setTimeout(() => {
                        mobileSubmitBtn.disabled = false;
                    }, data.wait_time * 1000);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showStep('mobile');
            
            // Handle validation errors (422)
            if (error.status === 422 && error.data) {
                if (error.data.errors && error.data.errors.mobile) {
                    showError('phoneNumberError', error.data.errors.mobile[0]);
                } else {
                    showError('phoneNumberError', error.data.message);
                }
                showNotification(error.message, 'error');
            } else {
                showNotification('خطای ارتباط با سرور', 'error');
            }
        } finally {
            // Re-enable buttons
            setButtonLoading(mobileSubmitBtn, false);
            if (resendOtpBtn) {
                resendOtpBtn.disabled = false;
            }
        }
    }

    async function verifyOTP(mobile, code) {
        setButtonLoading(otpSubmitBtn, true, 'در حال تأیید...');
        
        try {
            const data = await makeRequest('/user/verify-otp', { 
                mobile, 
                code, 
                type: authType 
            });
            
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => window.location.href = data.redirect, 1000);
            } else {
                showError('otpError', data.message);
                showNotification(data.message, 'error');
                
                if (data.code === 'MAX_ATTEMPTS_EXCEEDED') {
                    otpSubmitBtn.disabled = true;
                }
            }
        } catch (error) {
            console.error('Error:', error);
            
            // Handle validation errors (422)
            if (error.status === 422 && error.data) {
                if (error.data.errors && error.data.errors.code) {
                    showError('otpError', error.data.errors.code[0]);
                } else if (error.data.errors && error.data.errors.mobile) {
                    showError('otpError', error.data.errors.mobile[0]);
                } else {
                    showError('otpError', error.data.message);
                }
                showNotification(error.message, 'error');
            } else {
                showNotification('خطای ارتباط با سرور', 'error');
            }
        }
        
        setButtonLoading(otpSubmitBtn, false);
        otpSubmitBtn.innerHTML = '<span class="text-center text-white text-base font-medium capitalize leading-normal">تأیید و ورود</span>';
    }

    // ========== COUNTDOWN TIMER ==========
    
    function startCountdown(seconds) {
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
        
        resendOtpBtn.disabled = true;
        
        countdownInterval = setInterval(() => {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            countdown.textContent = `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            
            if (seconds <= 0) {
                clearInterval(countdownInterval);
                countdown.textContent = '00:00';
                resendOtpBtn.disabled = false;
                resendOtpBtn.textContent = 'ارسال مجدد کد';
            }
            
            seconds--;
        }, 1000);
    }

    // ========== EVENT LISTENERS ==========

    // Phone number input formatting
    phoneNumberInput.addEventListener('input', (e) => {
        let value = e.target.value.replace(/[^0-9۰-۹]/g, '');
        e.target.value = convertToPersianNumbers(value.slice(0, 11));
        hideError('phoneNumberError');
    });

    // Password input
    passwordInput.addEventListener('input', () => {
        hideError('passwordError');
    });

    // OTP input formatting
    otpCodeInput.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, 5);
        hideError('otpError');
    });

    // Mobile step submission
    mobileSubmitBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        console.log(`🎯 [DEBUG] Mobile submit button clicked`);
        
        // Prevent multiple clicks
        if (mobileSubmitBtn.disabled) {
            console.warn(`⚠️ [DEBUG] Mobile submit button is disabled, ignoring click`);
            return;
        }

        const mobile = formatPhoneNumber(phoneNumberInput.value.trim());
        console.log(`📱 [DEBUG] Original input: "${phoneNumberInput.value.trim()}", Formatted: "${mobile}"`);

        if (!validatePhoneNumber(mobile)) {
            console.error(`❌ [DEBUG] Phone number validation failed for: "${mobile}"`);
            showError('phoneNumberError', 'لطفا شماره موبایل معتبر وارد کنید.');
            return;
        }

        console.log(`✅ [DEBUG] Phone number validation passed for: "${mobile}"`);
        currentMobile = mobile;
        console.log(`🚀 [DEBUG] Calling checkMobileLogin with: "${mobile}"`);
        await checkMobileLogin(mobile);
    });

    // Password step submission
    passwordSubmitBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        // Prevent multiple clicks
        if (passwordSubmitBtn.disabled) {
            return;
        }
        
        const password = passwordInput.value.trim();
        
        if (!password) {
            showError('passwordError', 'لطفا رمز عبور را وارد کنید.');
            return;
        }
        
        await loginWithPassword(currentMobile, password, rememberCheckbox.checked);
    });

    // Use SMS button
    useSmsBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        // Prevent multiple clicks
        if (useSmsBtn.disabled) {
            return;
        }
        
        authType = 'login';
        await sendOTP(currentMobile);
    });

    // OTP step submission
    otpSubmitBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        // Prevent multiple clicks
        if (otpSubmitBtn.disabled) {
            return;
        }
        
        const otp = otpCodeInput.value.trim();
        
        if (otp.length !== 5) {
            showError('otpError', 'کد تأیید باید 5 رقم باشد.');
            return;
        }
        
        await verifyOTP(currentMobile, otp);
    });

    // Resend OTP
    resendOtpBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        console.log(`🔄 [DEBUG] Resend OTP button clicked`);

        if (resendOtpBtn.disabled) {
            console.warn(`⚠️ [DEBUG] Resend button is disabled, ignoring click`);
            return;
        }

        await sendOTP(currentMobile);
    });

    // Navigation buttons
    changePhoneBtn.addEventListener('click', resetToMobileStep);
    backToMobileBtn.addEventListener('click', resetToMobileStep);

    // Terms and conditions toggle
    showRulesBtn.addEventListener('click', (e) => {
        e.preventDefault();
        rulesContent.classList.toggle('hidden');
    });

    // Enter key support
    phoneNumberInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') mobileSubmitBtn.click();
    });

    passwordInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') passwordSubmitBtn.click();
    });

    otpCodeInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') otpSubmitBtn.click();
    });

    // ========== NOTIFICATION FUNCTION ==========
    
    function showNotification(message, type) {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification-toast');
        existingNotifications.forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification-toast flex items-center flex-row gap-2 fixed z-50 bottom-4 left-1/2 transform -translate-x-1/2 p-4 rounded-md text-white shadow-md transition-opacity duration-300 ${type === 'error' ? 'bg-red-500' : 'bg-green-500'}`;

        const icon = document.createElement('span');
        icon.className = 'mr-2';

        if (type === 'error') {
            icon.innerHTML = `
                <svg class="w-6 h-6" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 6C12.5523 6 13 6.44772 13 7V13C13 13.5523 12.5523 14 12 14C11.4477 14 11 13.5523 11 13V7C11 6.44772 11.4477 6 12 6Z" fill="currentColor"></path>
                    <path d="M12 16C11.4477 16 11 16.4477 11 17C11 17.5523 11.4477 18 12 18C12.5523 18 13 17.5523 13 17C13 16.4477 12.5523 16 12 16Z" fill="currentColor"></path>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2ZM4 12C4 16.4183 7.58172 20 12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12Z" fill="currentColor"></path>
                </svg>
            `;
        } else {
            icon.innerHTML = `
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" fill="currentColor">
                    <path fill="currentColor" d="M512 64a448 448 0 1 1 0 896 448 448 0 0 1 0-896m-55.808 536.384-99.52-99.584a38.4 38.4 0 1 0-54.336 54.336l126.72 126.72a38.27 38.27 0 0 0 54.336 0l262.4-262.464a38.4 38.4 0 1 0-54.272-54.336z"></path>
                </svg>
            `;
        }

        const messageSpan = document.createElement('span');
        messageSpan.textContent = message;

        notification.appendChild(icon);
        notification.appendChild(messageSpan);
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
}