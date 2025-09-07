<div>
    <label for="national_code" class="block text-sm font-medium text-dark-sky-500 mb-1">کد ملی</label>
    <div class="relative">
        <input type="tel" id="national_code" name="national_code" placeholder="1234567890"
               value="{{ old('national_code') }}"
               class="w-full font-bold dir-ltr p-3 bg-sky-100 rounded-lg border border-sky-300 text-dark-sky-600 placeholder-dark-sky-300 focus:ring-2 focus:ring-primary-normal focus:border-primary-normal text-center transition-all duration-300"
               dir="ltr" data-validate="required|iranian_national_code" maxlength="10">
    </div>
    <div id="national_code-error" class="text-red-500 text-sm mt-1 hidden"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nationalCodeInput = document.getElementById('national_code');
    const errorDiv = document.getElementById('national_code-error');

    if (nationalCodeInput) {
        // Validate Iranian national code
        function validateNationalCode(nationalCode) {
            // Remove any non-digit characters
            const cleaned = nationalCode.replace(/\D/g, '');
            
            // Check if it's exactly 10 digits
            if (cleaned.length !== 10) {
                return {
                    isValid: false,
                    message: 'کد ملی باید ۱۰ رقم باشد.'
                };
            }
            
            // Check for obviously invalid codes (all same digits)
            if (/^(\d)\1{9}$/.test(cleaned)) {
                return {
                    isValid: false,
                    message: 'کد ملی نمی‌تواند تمام ارقام یکسان باشد.'
                };
            }
            
            // Calculate checksum using Iranian national code algorithm
            let sum = 0;
            for (let i = 0; i < 9; i++) {
                sum += parseInt(cleaned.charAt(i)) * (10 - i);
            }
            
            const remainder = sum % 11;
            const checkDigit = parseInt(cleaned.charAt(9));
            
            let isValid;
            if (remainder < 2) {
                isValid = checkDigit === remainder;
            } else {
                isValid = checkDigit === 11 - remainder;
            }
            
            return {
                isValid: isValid,
                message: isValid ? '' : 'کد ملی وارد شده معتبر نیست.'
            };
        }

        // Show/hide error message
        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            nationalCodeInput.classList.add('border-red-500');
            nationalCodeInput.classList.remove('border-green-500');
            nationalCodeInput.style.backgroundColor = '#f0f9ff'; // Reset to default light blue
        }

        function hideError() {
            errorDiv.classList.add('hidden');
            nationalCodeInput.classList.remove('border-red-500');
        }

        function showValid() {
            nationalCodeInput.classList.add('border-green-500');
            nationalCodeInput.classList.remove('border-red-500');
            nationalCodeInput.style.backgroundColor = '#e6f9f0'; // Very light green
        }

        function resetFieldStyling() {
            nationalCodeInput.style.backgroundColor = '#f0f9ff'; // Default light blue
            nationalCodeInput.classList.remove('border-green-500', 'border-red-500');
        }

        // Format national code (only digits)
        function formatNationalCode(value) {
            return value.replace(/\D/g, '').substring(0, 10);
        }

        // Handle input event for real-time validation
        nationalCodeInput.addEventListener('input', function(e) {
            const formatted = formatNationalCode(e.target.value);
            e.target.value = formatted;

            if (formatted.length === 10) {
                const validation = validateNationalCode(formatted);
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
        nationalCodeInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pastedText.replace(/\D/g, '').substring(0, 10);
            this.value = digits;
            this.dispatchEvent(new Event('input'));
        });

        // Handle blur event for final validation
        nationalCodeInput.addEventListener('blur', function() {
            const clean = this.value.replace(/\D/g, '');
            if (clean.length > 0 && clean.length < 10) {
                showError('کد ملی ناقص است.');
                resetFieldStyling();
            }
        });

        // Format existing value on page load
        if (nationalCodeInput.value) {
            nationalCodeInput.value = formatNationalCode(nationalCodeInput.value);
            nationalCodeInput.dispatchEvent(new Event('input'));
        }
    }
});
</script>