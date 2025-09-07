// Services.js - General form functionality for services

// --- GENERAL SERVICE FORM FUNCTIONALITY ---
document.addEventListener('DOMContentLoaded', () => {
    
    // Initialize Customer Type Toggle (for forms with personal/corporate selection)
    initializeCustomerTypeToggle();
    
    // Initialize form validation for all service forms
    initializeFormValidation();
    
    // Initialize loading overlay functionality
    initializeLoadingOverlay();
    
});

// --- LOADING OVERLAY FUNCTIONALITY ---
function initializeLoadingOverlay() {
    console.log('🔧 Initializing loading overlay...');
    
    // Get the loading overlay element
    const loadingOverlay = document.getElementById('serviceLoadingOverlay');
    
    if (!loadingOverlay) {
        console.warn('⚠️ Service loading overlay not found');
        return;
    }
    
    // Add double-click prevention to all forms
    document.querySelectorAll('form').forEach(form => {
        let isSubmitting = false;
        
        form.addEventListener('submit', function(e) {
            // Prevent double submission
            if (isSubmitting) {
                console.log('🚫 Preventing double submission');
                e.preventDefault();
                return false;
            }
            
            // Only show loading if form validation passes
            const isValid = validateForm(form);
            
            if (isValid) {
                console.log('🔄 Showing loading overlay...');
                isSubmitting = true;
                showLoadingOverlay();
                
                // Disable all submit buttons
                const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                submitButtons.forEach(btn => {
                    btn.disabled = true;
                    if (btn.tagName === 'BUTTON') {
                        btn.innerHTML = '<span class="inline-block animate-spin">⏳</span> در حال ارسال...';
                    }
                });
                
                // Allow the form to submit normally
                return true;
            } else {
                // Prevent submission if validation fails
                e.preventDefault();
                return false;
            }
        });
    });
    
    // Also add click prevention to all submit buttons
    document.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(btn => {
        let clickCount = 0;
        btn.addEventListener('click', function(e) {
            clickCount++;
            if (clickCount > 1) {
                console.log('🚫 Preventing multiple clicks');
                e.preventDefault();
                return false;
            }
            
            // Reset click count after 3 seconds
            setTimeout(() => {
                clickCount = 0;
            }, 3000);
        });
    });
}

function showLoadingOverlay() {
    const loadingOverlay = document.getElementById('serviceLoadingOverlay');
    
    if (loadingOverlay) {
        // Show the overlay
        loadingOverlay.classList.add('show');
        
        // Add blur effect to the main form content
        const formContainers = document.querySelectorAll('.text-right, form');
        formContainers.forEach(container => {
            if (container.id !== 'serviceLoadingOverlay') {
                container.classList.add('form-blur');
            }
        });
        

        
        console.log('✅ Loading overlay shown successfully');
    }
}

function hideLoadingOverlay() {
    const loadingOverlay = document.getElementById('serviceLoadingOverlay');
    
    if (loadingOverlay) {
        // Hide the overlay
        loadingOverlay.classList.remove('show');
        
        // Remove blur effect from the main form content
        const formContainers = document.querySelectorAll('.text-right, form');
        formContainers.forEach(container => {
            container.classList.remove('form-blur');
        });
        
        console.log('✅ Loading overlay hidden successfully');
    }
}

// --- CUSTOMER TYPE TOGGLE (for services like credit score) ---
function initializeCustomerTypeToggle() {
    const personalBtn = document.querySelector('#personal-btn');
    const corporateBtn = document.querySelector('#corporate-btn');
    
    if (!personalBtn || !corporateBtn) return;
    
    console.log('🔧 Initializing customer type toggle...');
    
    personalBtn.addEventListener('click', (e) => {
        e.preventDefault();
        setCustomerType('personal');
    });
    
    corporateBtn.addEventListener('click', (e) => {
        e.preventDefault();
        setCustomerType('corporate');
    });
}

function setCustomerType(type) {
    console.log('🔧 Setting customer type to:', type);
    
    const nationalCodeField = document.querySelector('#national-code-field');
    const companyIdField = document.querySelector('#company-id-field');
    const customerTypeInput = document.querySelector('#customer_type');
    
    // Update button styles
    document.querySelectorAll('.customer-type-btn').forEach(btn => {
        btn.classList.remove('active', 'border-primary-normal', 'bg-primary-normal', 'text-white', 'border-primary-dark');
        btn.classList.add('border-primary-200', 'bg-primary-50', 'text-dark-blue-500');
    });

    const activeBtn = document.querySelector(`#${type}-btn`);
    if (activeBtn) {
        activeBtn.classList.add('active', 'border-primary-normal', 'bg-primary-normal', 'text-white');
        activeBtn.classList.remove('border-primary-200', 'bg-primary-50', 'text-dark-blue-500');
    }

    // Update form fields
    if (type === 'personal') {
        if (nationalCodeField) nationalCodeField.classList.remove('hidden');
        if (companyIdField) companyIdField.classList.add('hidden');
        const companyIdInput = document.querySelector('#company_id');
        const nationalCodeInput = document.querySelector('#national_code');
        if (companyIdInput) companyIdInput.removeAttribute('data-validate');
        if (nationalCodeInput) nationalCodeInput.setAttribute('data-validate', 'required|iranian_national_code');
    } else {
        if (nationalCodeField) nationalCodeField.classList.add('hidden');
        if (companyIdField) companyIdField.classList.remove('hidden');
        const companyIdInput = document.querySelector('#company_id');
        const nationalCodeInput = document.querySelector('#national_code');
        if (nationalCodeInput) nationalCodeInput.removeAttribute('data-validate');
        if (companyIdInput) companyIdInput.setAttribute('data-validate', 'required|iranian_company_id');
    }

    if (customerTypeInput) customerTypeInput.value = type;
    
    console.log('✅ Customer type set successfully');
}

// --- GENERAL FORM VALIDATION ---
function initializeFormValidation() {
    // Apply to all forms with validation
    document.querySelectorAll('form').forEach(form => {
        // Live validation on input
        form.querySelectorAll('input[data-validate]').forEach(input => {
            input.addEventListener('input', (e) => {
                validateField(e.target);
            });
        });
        
        // Clean card number before submission and basic validation (loading overlay handles submit)
        form.addEventListener('submit', (e) => {
            // Clean card number before submission
            const cardInput = form.querySelector('input[name="card_number"]');
            if (cardInput && cardInput.value) {
                // Create a hidden input with cleaned value
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'card_number_clean';
                hiddenInput.value = cardInput.value.replace(/\s/g, '');
                form.appendChild(hiddenInput);
                
                // Also clean the original input for submission
                const originalValue = cardInput.value;
                cardInput.value = cardInput.value.replace(/\s/g, '');
                
                // Restore the formatted value after a short delay
                setTimeout(() => {
                    cardInput.value = originalValue;
                    if (hiddenInput.parentNode) {
                        hiddenInput.parentNode.removeChild(hiddenInput);
                    }
                }, 100);
            }
            
            // Note: Form submission validation is now handled by the loading overlay function
        });
    });
}

// --- VALIDATION LOGIC ---
function validateField(input) {
    const rules = parseValidationRules(input.dataset.validate);
    let value = faToEn(input.value.trim());
    let error = null;

    // Special handling for card_number field - preserve formatting during validation
    if (input.name === 'card_number') {
        // Store the formatted value for display
        const formattedValue = value;
        // Remove spaces for validation
        value = value.replace(/\s/g, '');
        
        if (rules.numeric) value = value.replace(/[^0-9]/g, '');
        // Don't update input.value here to preserve formatting
    } else {
        if (rules.numeric) value = value.replace(/[^0-9]/g, '');
        input.value = value; // Update input with sanitized value
    }

    if (rules.required && value === '') {
        error = 'این فیلد الزامی است.';
    } else if (rules.numeric && !/^\d+$/.test(value)) {
        error = 'فقط اعداد مجاز هستند.';
    } else if (rules.length && value.length !== rules.length) {
        error = `طول این فیلد باید ${rules.length} رقم باشد.`;
    } else if (rules.minLength && value.length < rules.minLength) {
        error = `حداقل طول مجاز ${rules.minLength} رقم است.`;
    } else if (rules.maxLength && value.length > rules.maxLength) {
        error = `حداکثر طول مجاز ${rules.maxLength} رقم است.`;
    } else if (rules.regex && !rules.regex.test(input.value)) { // Use original value for regex like IBAN
        error = staticRules[input.name]?.message || 'فرمت ورودی نامعتبر است.';
    } else if (rules.iranian_mobile) {
        // Only validate Iranian mobile when field is complete (11 digits)
        if (value.length === 11 && !validateIranianMobile(value)) {
            error = 'شماره موبایل باید با 09 شروع شود و 11 رقم باشد.';
        } else if (value.length > 0 && value.length < 11) {
            // No error shown during typing, just clear any existing errors
            error = null;
        }
    } else if (rules.iranian_national_code) {
        // Only validate Iranian national code when field is complete (10 digits)
        if (value.length === 10 && !validateIranianNationalCode(value)) {
            error = 'کد ملی وارد شده معتبر نیست.';
        } else if (value.length > 0 && value.length < 10) {
            // No error shown during typing, just clear any existing errors
            error = null;
        }
    } else if (rules.iranian_company_id) {
        // Only validate Iranian company ID when field is complete (11 digits)
        if (value.length === 11 && !validateIranianCompanyId(value)) {
            error = 'شناسه ملی شرکت باید 11 رقم باشد.';
        } else if (value.length > 0 && value.length < 11) {
            // No error shown during typing, just clear any existing errors
            error = null;
        }
    }

    displayFieldError(input, error);
    return !error;
}

function validateForm(form) {
    let isFormValid = true;
    form.querySelectorAll('input[data-validate]').forEach(input => {
        if (!validateField(input)) {
            isFormValid = false;
        }
    });
    return isFormValid;
}

function parseValidationRules(rulesStr) {
    const rules = {};
    if (!rulesStr) return rules;
    rulesStr.split('|').forEach(part => {
        const [key, val] = part.split(':');
        rules[key] = val || true;
    });
    return rules;
}

function displayFieldError(input, error) {
    const errorElement = document.querySelector(`#${input.name}-error`);
    if (!errorElement) return;
    if (error) {
        input.classList.add('border-red-500');
        errorElement.textContent = error;
        errorElement.classList.remove('hidden');
    } else {
        input.classList.remove('border-red-500');
        errorElement.textContent = '';
        errorElement.classList.add('hidden');
    }
}



// --- UTILITY FUNCTIONS ---
function faToEn(str) {
    const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    
    let result = str;
    for (let i = 0; i < 10; i++) {
        result = result.replace(new RegExp(persianNumbers[i], 'g'), i.toString());
        result = result.replace(new RegExp(arabicNumbers[i], 'g'), i.toString());
    }
    return result;
}

// --- VALIDATION HELPER FUNCTIONS ---
function validateIranianMobile(mobile) {
    // Remove any non-digit characters
    const cleaned = mobile.replace(/\D/g, '');
    
    // Check if it's 11 digits and starts with 09
    if (cleaned.length !== 11 || !cleaned.startsWith('09')) {
        return false;
    }
    
    // Check if the third digit is valid (Iranian mobile operators)
    const thirdDigit = cleaned.charAt(2);
    const validThirdDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
    return validThirdDigits.includes(thirdDigit);
}

function validateIranianNationalCode(nationalCode) {
    // Remove any non-digit characters
    const cleaned = nationalCode.replace(/\D/g, '');
    
    // Check if it's exactly 10 digits
    if (cleaned.length !== 10) {
        return false;
    }
    
    // Check for obviously invalid codes (all same digits)
    if (/^(\d)\1{9}$/.test(cleaned)) {
        return false;
    }
    
    // Calculate checksum using Iranian national code algorithm
    let sum = 0;
    for (let i = 0; i < 9; i++) {
        sum += parseInt(cleaned.charAt(i)) * (10 - i);
    }
    
    const remainder = sum % 11;
    const checkDigit = parseInt(cleaned.charAt(9));
    
    if (remainder < 2) {
        return checkDigit === remainder;
    } else {
        return checkDigit === 11 - remainder;
    }
}

function validateIranianCompanyId(companyId) {
    // Remove any non-digit characters
    const cleaned = companyId.replace(/\D/g, '');
    
    // Check if it's exactly 11 digits
    if (cleaned.length !== 11) {
        return false;
    }
    
    // Check for obviously invalid codes (all same digits)
    if (/^(\d)\1{10}$/.test(cleaned)) {
        return false;
    }
    
    // Calculate checksum using Iranian company ID algorithm
    let sum = 0;
    const weights = [29, 27, 23, 19, 17, 13, 11, 7, 5, 3];
    
    for (let i = 0; i < 10; i++) {
        sum += parseInt(cleaned.charAt(i)) * weights[i];
    }
    
    const remainder = sum % 11;
    const checkDigit = parseInt(cleaned.charAt(10));
    
    if (remainder < 2) {
        return checkDigit === remainder;
    } else {
        return checkDigit === 11 - remainder;
    }
} 