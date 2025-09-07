<div>
    <label for="license_number" class="block text-sm font-medium text-dark-sky-500 mb-1">شماره گواهینامه</label>
    <div class="relative">
        <input type="tel" id="license_number" name="license_number" placeholder="1234567890"
               value="{{ old('license_number') }}"
               class="w-full font-bold dir-ltr p-3 bg-sky-100 rounded-lg border border-sky-300 text-dark-sky-600 placeholder-dark-sky-300 focus:ring-2 focus:ring-primary-normal focus:border-primary-normal text-center transition-all duration-300"
               dir="ltr" data-validate="required|digits:10" maxlength="10">
    </div>
    <div id="license_number-error" class="text-red-500 text-sm mt-1 hidden"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const licenseNumberInput = document.getElementById('license_number');
    const errorDiv = document.getElementById('license_number-error');

    if (licenseNumberInput) {
        // Validate license number
        function validateLicenseNumber(licenseNumber) {
            // Remove all non-digit characters
            const clean = licenseNumber.replace(/\D/g, '');
            
            // Check if it's exactly 10 digits
            if (clean.length !== 10) {
                return {
                    isValid: false,
                    message: 'شماره گواهینامه باید ۱۰ رقم باشد.'
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
            licenseNumberInput.classList.add('border-red-500');
            licenseNumberInput.classList.remove('border-green-500');
            licenseNumberInput.style.backgroundColor = '#f0f9ff'; // Reset to default light blue
        }

        function hideError() {
            errorDiv.classList.add('hidden');
            licenseNumberInput.classList.remove('border-red-500');
        }

        function showValid() {
            licenseNumberInput.classList.add('border-green-500');
            licenseNumberInput.classList.remove('border-red-500');
            licenseNumberInput.style.backgroundColor = '#e6f9f0'; // Very light green
        }

        function resetFieldStyling() {
            licenseNumberInput.style.backgroundColor = '#f0f9ff'; // Default light blue
            licenseNumberInput.classList.remove('border-green-500', 'border-red-500');
        }

        // Format license number (only digits)
        function formatLicenseNumber(value) {
            return value.replace(/\D/g, '').substring(0, 10);
        }

        // Handle input event for real-time validation
        licenseNumberInput.addEventListener('input', function(e) {
            const formatted = formatLicenseNumber(e.target.value);
            e.target.value = formatted;

            if (formatted.length === 10) {
                const validation = validateLicenseNumber(formatted);
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
        licenseNumberInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pastedText.replace(/\D/g, '').substring(0, 10);
            this.value = digits;
            this.dispatchEvent(new Event('input'));
        });

        // Handle blur event for final validation
        licenseNumberInput.addEventListener('blur', function() {
            const clean = this.value.replace(/\D/g, '');
            if (clean.length > 0 && clean.length < 10) {
                showError('شماره گواهینامه ناقص است.');
                resetFieldStyling();
            }
        });

        // Format existing value on page load
        if (licenseNumberInput.value) {
            licenseNumberInput.value = formatLicenseNumber(licenseNumberInput.value);
            licenseNumberInput.dispatchEvent(new Event('input'));
        }
    }
});
</script> 