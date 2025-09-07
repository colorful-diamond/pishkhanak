<div>
    <label for="account_number" class="block text-sm font-medium text-dark-sky-500 mb-1">شماره حساب</label>
    <div class="relative">
        <div id="account_logo_container" class="absolute left-3 top-1/2 transform -translate-y-1/2 hidden">
            <img id="account_logo_inline" src="" alt="" class="w-6 h-6">
        </div>
        <input type="tel" id="account_number" name="account_number" placeholder="شماره حساب"
               class="w-full font-bold dir-ltr p-3 bg-sky-100 rounded-lg border border-sky-300 text-dark-sky-600 placeholder-dark-sky-300 focus:ring-2 focus:ring-primary-normal focus:border-primary-normal text-center transition-all duration-300"
               dir="ltr" data-validate="required|account_number" value="{{ old('account_number') }}">
    </div>
    <div id="account_number-error" class="text-red-500 text-sm mt-1 hidden"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accountInput = document.getElementById('account_number');
    const errorDiv = document.getElementById('account_number-error');
    const accountLogoContainer = document.getElementById('account_logo_container');
    const accountLogoInline = document.getElementById('account_logo_inline');

    if (accountInput) {
        // Example: You can add your own logic for account number validation and bank detection here.
        // For now, we just style and validate length (e.g., 13-16 digits, adjust as needed).

        // Convert hex color to very light version (95% white)
        function getLightColor(hexColor) {
            hexColor = hexColor.replace('#', '');
            const r = parseInt(hexColor.substr(0, 2), 16);
            const g = parseInt(hexColor.substr(2, 2), 16);
            const b = parseInt(hexColor.substr(4, 2), 16);
            const lightR = Math.round(r * 0.05 + 255 * 0.95);
            const lightG = Math.round(g * 0.05 + 255 * 0.95);
            const lightB = Math.round(b * 0.05 + 255 * 0.95);
            return `#${lightR.toString(16).padStart(2, '0')}${lightG.toString(16).padStart(2, '0')}${lightB.toString(16).padStart(2, '0')}`;
        }

        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            accountLogoContainer.classList.add('hidden');
            accountInput.classList.add('border-red-500');
            accountInput.classList.remove('border-green-500');
            accountInput.style.backgroundColor = '#f0f9ff';
            accountInput.style.paddingLeft = '12px';
        }

        function hideError() {
            errorDiv.classList.add('hidden');
            accountInput.classList.remove('border-red-500');
        }

        function showAccountInfo(/*bank*/) {
            // If you want to show a logo, set src/alt here
            // accountLogoInline.src = bank.logo;
            // accountLogoInline.alt = bank.name;
            // accountLogoContainer.classList.remove('hidden');
            // accountInput.style.backgroundColor = getLightColor(bank.color);
            // accountInput.style.paddingLeft = '48px';
            // accountInput.classList.add('border-green-500');
            // accountInput.classList.remove('border-red-500');
        }

        function resetFieldStyling() {
            accountLogoContainer.classList.add('hidden');
            accountInput.style.backgroundColor = '#f0f9ff';
            accountInput.style.paddingLeft = '12px';
            accountInput.classList.remove('border-green-500', 'border-red-500');
        }

        // Keep account number as digits only (no automatic spacing)
        function formatAccountNumber(value) {
            // Just remove non-digits, no spacing for now
            return value.replace(/\D/g, '');
        }

        accountInput.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const oldLength = oldValue.length;

            const formattedValue = formatAccountNumber(e.target.value);
            e.target.value = formattedValue;

            // Since we're not adding spaces, cursor position remains the same
            e.target.setSelectionRange(cursorPosition, cursorPosition);

            const cleanNumber = formattedValue.replace(/\D/g, '');

            // Accept account numbers between 8 and 20 digits
            if (cleanNumber.length >= 8 && cleanNumber.length <= 20) {
                hideError();
                // showAccountInfo(); // If you want to show info/logo
                accountInput.classList.add('border-green-500');
                accountInput.classList.remove('border-red-500');
            } else if (cleanNumber.length > 0) {
                hideError();
                resetFieldStyling();
            }
        });

        // No special keydown handling needed since we're not using spaces

        accountInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pastedText.replace(/\D/g, '');
            // Limit to 20 digits for account numbers
            const limitedDigits = digits.substring(0, 20);
            const formattedValue = formatAccountNumber(limitedDigits);
            this.value = formattedValue;
            this.dispatchEvent(new Event('input'));
        });

        accountInput.addEventListener('blur', function() {
            const cleanNumber = this.value.replace(/\D/g, '');
            if (cleanNumber.length > 0 && (cleanNumber.length < 8 || cleanNumber.length > 20)) {
                showError('شماره حساب ناقص است.');
            }
        });

        // Format existing value on page load
        if (accountInput.value) {
            accountInput.value = formatAccountNumber(accountInput.value);
            accountInput.dispatchEvent(new Event('input'));
        }
    }
});
</script>