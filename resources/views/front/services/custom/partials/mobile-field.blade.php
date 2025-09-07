<div>
    <label for="mobile" class="block text-sm font-medium text-dark-sky-500 mb-1">شماره موبایل</label>
    <div class="relative">
        <input type="tel" id="mobile" name="mobile" placeholder="09123456789"
               class="w-full font-bold dir-ltr p-3 bg-sky-100 rounded-lg border border-sky-300 text-dark-sky-600 placeholder-dark-sky-300 focus:ring-2 focus:ring-primary-normal focus:border-primary-normal text-center transition-all duration-300"
               dir="ltr" data-validate="required|iranian_mobile" maxlength="11" value="{{ old('mobile') }}">
    </div>
    <div id="mobile-error" class="text-red-500 text-sm mt-1 hidden"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileInput = document.getElementById('mobile');
    const errorDiv = document.getElementById('mobile-error');

    if (mobileInput) {
        // Validate Iranian mobile number
        function validateMobile(mobile) {
            // Remove all non-digit characters
            const clean = mobile.replace(/\D/g, '');
            // Iranian mobile: 09xxxxxxxxx (11 digits, starts with 09)
            if (clean.length !== 11) {
                return {
                    isValid: false,
                    message: 'شماره موبایل باید ۱۱ رقم باشد.'
                };
            }
            if (!/^09\d{9}$/.test(clean)) {
                return {
                    isValid: false,
                    message: 'شماره موبایل معتبر نیست.'
                };
            }
            return {
                isValid: true,
                message: ''
            };
        }

        // Show/hide error message
        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            mobileInput.classList.add('border-red-500');
            mobileInput.classList.remove('border-green-500');
            mobileInput.style.backgroundColor = '#f0f9ff'; // Reset to default light blue
        }

        function hideError() {
            errorDiv.classList.add('hidden');
            mobileInput.classList.remove('border-red-500');
        }

        function showValid() {
            mobileInput.classList.add('border-green-500');
            mobileInput.classList.remove('border-red-500');
            mobileInput.style.backgroundColor = '#e6f9f0'; // Very light green
        }

        function resetFieldStyling() {
            mobileInput.style.backgroundColor = '#f0f9ff'; // Default light blue
            mobileInput.classList.remove('border-green-500', 'border-red-500');
        }

        // Format mobile number (no formatting, just digits)
        function formatMobile(value) {
            return value.replace(/\D/g, '').substring(0, 11);
        }

        // Handle input event for real-time validation
        mobileInput.addEventListener('input', function(e) {
            const formatted = formatMobile(e.target.value);
            e.target.value = formatted;

            if (formatted.length === 11) {
                const validation = validateMobile(formatted);
                if (validation.isValid) {
                    hideError();
                    showValid();
                } else {
                    showError(validation.message);
                    resetFieldStyling();
                }
            } else if (formatted.length > 0) {
                hideError();
                resetFieldStyling();
            } else {
                hideError();
                resetFieldStyling();
            }
        });

        // Handle paste event
        mobileInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pastedText.replace(/\D/g, '').substring(0, 11);
            this.value = digits;
            this.dispatchEvent(new Event('input'));
        });

        // Handle blur event for final validation
        mobileInput.addEventListener('blur', function() {
            const clean = this.value.replace(/\D/g, '');
            if (clean.length > 0 && clean.length < 11) {
                showError('شماره موبایل ناقص است.');
                resetFieldStyling();
            }
        });

        // Format existing value on page load
        if (mobileInput.value) {
            mobileInput.value = formatMobile(mobileInput.value);
            mobileInput.dispatchEvent(new Event('input'));
        }
    }
});
</script>