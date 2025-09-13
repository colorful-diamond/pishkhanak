{{-- Advanced Form Validation Component for Coming Check Inquiry --}}
<div class="form-validation-container">
    
    {{-- National Code Validation Component --}}
    <div class="validation-field" data-field="national_code">
        <div class="validation-messages hidden">
            <div class="validation-message error" data-type="required">
                <span class="icon">❌</span>
                <span class="message">وارد کردن کد ملی الزامی است</span>
            </div>
            <div class="validation-message error" data-type="length">
                <span class="icon">⚠️</span>
                <span class="message">کد ملی باید دقیقاً ۱۰ رقم باشد</span>
            </div>
            <div class="validation-message error" data-type="numeric">
                <span class="icon">🔢</span>
                <span class="message">کد ملی باید فقط شامل اعداد باشد</span>
            </div>
            <div class="validation-message error" data-type="checksum">
                <span class="icon">❗</span>
                <span class="message">کد ملی وارد شده معتبر نیست</span>
            </div>
            <div class="validation-message success" data-type="valid">
                <span class="icon">✅</span>
                <span class="message">کد ملی معتبر است</span>
            </div>
        </div>
    </div>

    {{-- Mobile Number Validation Component --}}
    <div class="validation-field" data-field="mobile">
        <div class="validation-messages hidden">
            <div class="validation-message error" data-type="required">
                <span class="icon">❌</span>
                <span class="message">وارد کردن شماره موبایل الزامی است</span>
            </div>
            <div class="validation-message error" data-type="format">
                <span class="icon">📱</span>
                <span class="message">شماره موبایل باید با ۰۹ شروع شود</span>
            </div>
            <div class="validation-message error" data-type="length">
                <span class="icon">⚠️</span>
                <span class="message">شماره موبایل باید ۱۱ رقم باشد</span>
            </div>
            <div class="validation-message error" data-type="numeric">
                <span class="icon">🔢</span>
                <span class="message">شماره موبایل باید فقط شامل اعداد باشد</span>
            </div>
            <div class="validation-message success" data-type="valid">
                <span class="icon">✅</span>
                <span class="message">شماره موبایل معتبر است</span>
            </div>
        </div>
    </div>

    {{-- General Form Validation Messages --}}
    <div class="form-level-messages">
        <div class="validation-message info" data-type="loading">
            <span class="icon loading-spinner"></span>
            <span class="message">در حال بررسی اطلاعات...</span>
        </div>
        <div class="validation-message success" data-type="all_valid">
            <span class="icon">🎉</span>
            <span class="message">تمام فیلدها معتبر هستند. آماده ارسال درخواست</span>
        </div>
        <div class="validation-message error" data-type="server_error">
            <span class="icon">🚨</span>
            <span class="message">خطا در ارتباط با سرور. لطفاً دوباره تلاش کنید</span>
        </div>
    </div>
</div>

{{-- Advanced JavaScript Validation --}}
<script>
class PersianFormValidator {
    constructor() {
        this.validators = {
            national_code: {
                required: (value) => value.trim() !== '',
                length: (value) => value.length === 10,
                numeric: (value) => /^\d{10}$/.test(value),
                checksum: (value) => this.validateNationalCodeChecksum(value)
            },
            mobile: {
                required: (value) => value.trim() !== '',
                format: (value) => /^09\d{9}$/.test(value),
                length: (value) => value.length === 11,
                numeric: (value) => /^\d{11}$/.test(value)
            }
        };

        this.fieldStates = {};
        this.initializeValidation();
    }

    initializeValidation() {
        document.addEventListener('DOMContentLoaded', () => {
            this.attachEventListeners();
            this.initializeFieldStates();
        });
    }

    attachEventListeners() {
        const form = document.querySelector('form[data-service="coming-check-inquiry"]');
        if (!form) return;

        // Real-time validation on input
        Object.keys(this.validators).forEach(fieldName => {
            const input = form.querySelector(`input[name="${fieldName}"]`);
            if (input) {
                input.addEventListener('input', (e) => this.validateField(fieldName, e.target.value));
                input.addEventListener('blur', (e) => this.validateField(fieldName, e.target.value, true));
                input.addEventListener('focus', () => this.clearFieldMessages(fieldName));
            }
        });

        // Form submission validation
        form.addEventListener('submit', (e) => {
            if (!this.validateAllFields()) {
                e.preventDefault();
                this.showFormError();
            }
        });
    }

    initializeFieldStates() {
        Object.keys(this.validators).forEach(fieldName => {
            this.fieldStates[fieldName] = {
                valid: false,
                errors: [],
                touched: false
            };
        });
    }

    validateField(fieldName, value, showMessages = false) {
        const validators = this.validators[fieldName];
        const state = this.fieldStates[fieldName];
        
        state.errors = [];
        state.valid = true;
        state.touched = true;

        // Run all validators for the field
        for (const [validatorName, validatorFunc] of Object.entries(validators)) {
            if (!validatorFunc(value)) {
                state.errors.push(validatorName);
                state.valid = false;
            }
        }

        // Update UI
        if (showMessages || state.touched) {
            this.updateFieldUI(fieldName);
        }

        // Update form state
        this.updateFormState();

        return state.valid;
    }

    validateAllFields() {
        const form = document.querySelector('form[data-service="coming-check-inquiry"]');
        let allValid = true;

        Object.keys(this.validators).forEach(fieldName => {
            const input = form.querySelector(`input[name="${fieldName}"]`);
            if (input) {
                const isValid = this.validateField(fieldName, input.value, true);
                allValid = allValid && isValid;
            }
        });

        return allValid;
    }

    updateFieldUI(fieldName) {
        const state = this.fieldStates[fieldName];
        const fieldContainer = document.querySelector(`[data-field="${fieldName}"]`);
        const input = document.querySelector(`input[name="${fieldName}"]`);
        
        if (!fieldContainer || !input) return;

        // Clear previous messages
        const messagesContainer = fieldContainer.querySelector('.validation-messages');
        messagesContainer.classList.add('hidden');
        messagesContainer.querySelectorAll('.validation-message').forEach(msg => {
            msg.style.display = 'none';
        });

        // Update input styling
        input.classList.remove('valid', 'invalid');
        
        if (state.touched) {
            if (state.valid) {
                input.classList.add('valid');
                this.showMessage(fieldContainer, 'success', 'valid');
            } else {
                input.classList.add('invalid');
                // Show first error
                if (state.errors.length > 0) {
                    this.showMessage(fieldContainer, 'error', state.errors[0]);
                }
            }
        }
    }

    showMessage(fieldContainer, type, messageType) {
        const messagesContainer = fieldContainer.querySelector('.validation-messages');
        const message = messagesContainer.querySelector(`[data-type="${messageType}"]`);
        
        if (message) {
            messagesContainer.classList.remove('hidden');
            message.style.display = 'flex';
            
            // Auto-hide after 5 seconds for success messages
            if (type === 'success') {
                setTimeout(() => {
                    message.style.display = 'none';
                    messagesContainer.classList.add('hidden');
                }, 5000);
            }
        }
    }

    updateFormState() {
        const allValid = Object.values(this.fieldStates).every(state => state.valid);
        const submitButton = document.querySelector('button[type="submit"]');
        
        if (submitButton) {
            submitButton.disabled = !allValid;
            submitButton.classList.toggle('disabled', !allValid);
        }

        // Show form-level success message if all fields are valid
        if (allValid && Object.values(this.fieldStates).every(state => state.touched)) {
            this.showFormMessage('success', 'all_valid');
        }
    }

    showFormMessage(type, messageType) {
        const container = document.querySelector('.form-level-messages');
        const message = container.querySelector(`[data-type="${messageType}"]`);
        
        if (message) {
            // Hide all other form messages
            container.querySelectorAll('.validation-message').forEach(msg => {
                msg.style.display = 'none';
            });
            
            message.style.display = 'flex';
            
            // Auto-hide after 3 seconds
            setTimeout(() => {
                message.style.display = 'none';
            }, 3000);
        }
    }

    showFormError() {
        this.showFormMessage('error', 'server_error');
    }

    clearFieldMessages(fieldName) {
        const fieldContainer = document.querySelector(`[data-field="${fieldName}"]`);
        if (fieldContainer) {
            const messagesContainer = fieldContainer.querySelector('.validation-messages');
            messagesContainer.classList.add('hidden');
            messagesContainer.querySelectorAll('.validation-message').forEach(msg => {
                msg.style.display = 'none';
            });
        }
    }

    // Persian National Code Validation Algorithm
    validateNationalCodeChecksum(nationalCode) {
        if (!nationalCode || nationalCode.length !== 10 || !/^\d{10}$/.test(nationalCode)) {
            return false;
        }

        // Check for repeated digits (invalid codes)
        if (/^(\d)\1{9}$/.test(nationalCode)) {
            return false;
        }

        // Calculate checksum
        let sum = 0;
        for (let i = 0; i < 9; i++) {
            sum += parseInt(nationalCode.charAt(i)) * (10 - i);
        }

        const remainder = sum % 11;
        const checkDigit = parseInt(nationalCode.charAt(9));

        if (remainder < 2) {
            return checkDigit === remainder;
        } else {
            return checkDigit === 11 - remainder;
        }
    }

    // Public method to trigger validation
    validate() {
        return this.validateAllFields();
    }

    // Public method to get field state
    getFieldState(fieldName) {
        return this.fieldStates[fieldName];
    }

    // Public method to reset form
    reset() {
        this.initializeFieldStates();
        
        // Clear UI
        Object.keys(this.validators).forEach(fieldName => {
            const input = document.querySelector(`input[name="${fieldName}"]`);
            if (input) {
                input.classList.remove('valid', 'invalid');
                input.value = '';
            }
            this.clearFieldMessages(fieldName);
        });

        // Reset submit button
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('disabled');
        }
    }
}

// Initialize validator
const formValidator = new PersianFormValidator();

// Export for use in other scripts
window.PersianFormValidator = formValidator;
</script>

{{-- CSS Styles for Validation --}}
<style>
.validation-field {
    position: relative;
}

.validation-messages {
    margin-top: 0.5rem;
    transition: all 0.3s ease;
}

.validation-message {
    display: none;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
    direction: rtl;
    text-align: right;
}

.validation-message.error {
    background-color: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.validation-message.success {
    background-color: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}

.validation-message.info {
    background-color: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
}

.validation-message .icon {
    flex-shrink: 0;
    font-size: 1rem;
}

.validation-message .message {
    flex: 1;
}

/* Input States */
input.valid {
    border-color: #16a34a;
    box-shadow: 0 0 0 1px #16a34a;
}

input.invalid {
    border-color: #dc2626;
    box-shadow: 0 0 0 1px #dc2626;
}

/* Loading Spinner */
.loading-spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 2px solid #e5e7eb;
    border-top: 2px solid #2563eb;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Form Level Messages */
.form-level-messages {
    margin-top: 1rem;
}

/* Submit Button States */
button[type="submit"].disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

button[type="submit"]:not(.disabled) {
    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
}

/* RTL Adjustments */
.validation-message {
    text-align: right;
}

/* Accessibility Improvements */
@media (prefers-reduced-motion: reduce) {
    .validation-messages,
    .validation-message {
        transition: none;
    }
    
    .loading-spinner {
        animation: none;
    }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    .validation-message.error {
        background-color: #fff;
        color: #000;
        border: 2px solid #dc2626;
    }
    
    .validation-message.success {
        background-color: #fff;
        color: #000;
        border: 2px solid #16a34a;
    }
    
    input.valid {
        border: 3px solid #16a34a;
    }
    
    input.invalid {
        border: 3px solid #dc2626;
    }
}
</style>