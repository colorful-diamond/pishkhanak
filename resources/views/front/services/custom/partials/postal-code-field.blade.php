<div>
    <label for="postal_code" class="block text-sm font-medium text-dark-sky-500 mb-1">کد پستی (10 رقم)</label>
    <div class="relative">
        <input type="tel" id="postal_code" name="postal_code" placeholder="__________"
               class="w-full font-bold dir-ltr p-3 bg-sky-100 rounded-lg border border-sky-300 text-dark-sky-600 placeholder-dark-sky-300 focus:ring-2 focus:ring-primary-normal focus:border-primary-normal text-center transition-all duration-300"
               dir="ltr" data-validate="required|digits:10" value="{{ old('postal_code') }}" maxlength="10">
    </div>
    <div id="postal_code-error" class="text-red-500 text-sm mt-1 hidden"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const postalInput = document.getElementById('postal_code');
    const errorDiv = document.getElementById('postal_code-error');

    if (postalInput) {
        // Only allow digits, max 10
        function formatPostalCode(value) {
            return value.replace(/\D/g, '').substring(0, 10);
        }

        // Validate postal code
        function validatePostalCode(value) {
            const clean = value.replace(/\D/g, '');
            if (clean.length !== 10) {
                return {
                    isValid: false,
                    message: 'کد پستی باید ۱۰ رقم باشد.'
                };
            }
            if (!/^\d{10}$/.test(clean)) {
                return {
                    isValid: false,
                    message: 'کد پستی باید فقط شامل اعداد باشد.'
                };
            }
            return {
                isValid: true,
                message: ''
            };
        }

        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            postalInput.classList.add('border-red-500');
            postalInput.classList.remove('border-green-500');
            postalInput.style.backgroundColor = '#f0f9ff';
        }

        function hideError() {
            errorDiv.classList.add('hidden');
            postalInput.classList.remove('border-red-500');
        }

        function showValid() {
            postalInput.classList.add('border-green-500');
            postalInput.classList.remove('border-red-500');
            postalInput.style.backgroundColor = '#f0f9ff';
        }

        function resetFieldStyling() {
            postalInput.classList.remove('border-green-500', 'border-red-500');
            postalInput.style.backgroundColor = '#f0f9ff';
        }

        postalInput.addEventListener('input', function(e) {
            const oldValue = e.target.value;
            const formatted = formatPostalCode(oldValue);
            e.target.value = formatted;

            if (formatted.length === 10) {
                const validation = validatePostalCode(formatted);
                if (validation.isValid) {
                    hideError();
                    showValid();
                } else {
                    showError(validation.message);
                }
            } else if (formatted.length > 0) {
                hideError();
                resetFieldStyling();
            } else {
                hideError();
                resetFieldStyling();
            }
        });

        postalInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pastedText.replace(/\D/g, '').substring(0, 10);
            this.value = digits;
            this.dispatchEvent(new Event('input'));
        });

        postalInput.addEventListener('blur', function() {
            const clean = this.value.replace(/\D/g, '');
            if (clean.length > 0 && clean.length < 10) {
                showError('کد پستی ناقص است.');
            }
        });

        // Format existing value on page load
        if (postalInput.value) {
            postalInput.value = formatPostalCode(postalInput.value);
            postalInput.dispatchEvent(new Event('input'));
        }
    }
});
</script>