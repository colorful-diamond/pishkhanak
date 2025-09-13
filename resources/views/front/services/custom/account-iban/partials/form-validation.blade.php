{{-- Account to IBAN Form Validation Component --}}
{{-- Usage: @include('front.services.custom.account-iban.partials.form-validation') --}}

<div class="form-validation-container">
    {{-- Account Number Input with Real-time Validation --}}
    <div class="mb-6">
        <label for="accountNumber" class="block text-sm font-bold text-gray-900 mb-3">
            شماره حساب
            <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input type="text" 
                   id="accountNumber"
                   name="account_number"
                   class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right text-lg font-mono"
                   placeholder="شماره حساب خود را وارد کنید..."
                   maxlength="20"
                   autocomplete="off"
                   required>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            {{-- Validation Status Indicator --}}
            <div id="validationStatus" class="absolute inset-y-0 left-0 pl-3 flex items-center hidden">
                <div class="validation-spinner hidden">
                    <svg class="animate-spin h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div class="validation-success hidden">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="validation-error hidden">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        {{-- Character Counter --}}
        <div class="flex justify-between items-center mt-2">
            <div id="charCounter" class="text-sm text-gray-500">
                <span id="charCount">0</span> / 20 کاراکتر
            </div>
            <div id="accountFormat" class="text-xs text-blue-600 hidden">
                فرمت: ✓ فقط عدد
            </div>
        </div>

        {{-- Validation Messages --}}
        <div id="validationMessages" class="mt-2 space-y-2">
            <div id="errorMessage" class="hidden text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg p-3">
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span id="errorText"></span>
                </div>
            </div>
            <div id="successMessage" class="hidden text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg p-3">
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-green-500 mt-0.5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span id="successText"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Bank Selection (Auto-detected) --}}
    <div id="bankSelectionContainer" class="mb-6 hidden">
        <label class="block text-sm font-bold text-gray-900 mb-3">
            بانک شناسایی شده
        </label>
        <div id="detectedBank" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center ml-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="flex-grow">
                    <h4 id="bankName" class="font-bold text-blue-800">--</h4>
                    <p id="bankCode" class="text-sm text-blue-600">کد بانک: --</p>
                </div>
                <div class="text-green-500">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accountInput = document.getElementById('accountNumber');
    const charCount = document.getElementById('charCount');
    const validationStatus = document.getElementById('validationStatus');
    const errorMessage = document.getElementById('errorMessage');
    const successMessage = document.getElementById('successMessage');
    const errorText = document.getElementById('errorText');
    const successText = document.getElementById('successText');
    const accountFormat = document.getElementById('accountFormat');
    const bankContainer = document.getElementById('bankSelectionContainer');
    const bankName = document.getElementById('bankName');
    const bankCode = document.getElementById('bankCode');

    // Bank codes mapping
    const bankCodes = {
        '010': 'بانک مرکزی جمهوری اسلامی ایران',
        '011': 'بانک صنعت و معدن',
        '012': 'بانک کشاورزی',
        '013': 'بانک ملت',
        '014': 'بانک مسکن',
        '015': 'بانک توسعه تعاون',
        '016': 'بانک اقتصاد نوین',
        '017': 'بانک پارسیان',
        '018': 'بانک پاسارگاد',
        '019': 'بانک سرمایه',
        '020': 'بانک تجارت',
        '021': 'بانک ملی ایران',
        '022': 'بانک صادرات ایران',
        '051': 'بانک موسسه اعتباری کوثر',
        '053': 'بانک کارآفرین',
        '054': 'بانک پارسیان',
        '055': 'بانک اقتصاد نوین',
        '056': 'بانک سامان',
        '057': 'بانک پاسارگاد',
        '058': 'بانک سرمایه',
        '059': 'بانک سینا',
        '060': 'بانک قوامین',
        '061': 'بانک انصار',
        '062': 'بانک مهر اقتصاد',
        '063': 'بانک مهر ایران',
        '069': 'بانک دی',
        '070': 'بانک رسالت',
        '073': 'بانک کشور',
        '075': 'بانک مهر اقتصاد',
        '078': 'بانک میدل ایست',
        '079': 'بانک مرکزی ایران',
        '080': 'بانک شهر',
        '090': 'بانک قوامین',
        '095': 'بانک ایران زمین',
        '627760': 'پست بانک ایران',
        '627412': 'بانک اقتصاد نوین',
        '627381': 'بانک انصار',
        '627593': 'بانک ایران زمین'
    };

    function validateAccountNumber(value) {
        // Remove all non-digit characters
        const cleaned = value.replace(/\D/g, '');
        
        // Check basic format
        if (cleaned.length === 0) {
            return { valid: false, message: '' };
        }
        
        if (cleaned.length < 8) {
            return { valid: false, message: 'شماره حساب باید حداقل 8 رقم باشد' };
        }
        
        if (cleaned.length > 20) {
            return { valid: false, message: 'شماره حساب نمی‌تواند بیش از 20 رقم باشد' };
        }
        
        // Check for bank code pattern (first 3 digits)
        const bankCodeCandidate = cleaned.substring(0, 3);
        if (bankCodes[bankCodeCandidate]) {
            return { 
                valid: true, 
                message: 'شماره حساب معتبر است', 
                bank: bankCodes[bankCodeCandidate],
                code: bankCodeCandidate
            };
        }
        
        // Check for longer bank codes (like Post Bank)
        const longBankCode = cleaned.substring(0, 6);
        if (bankCodes[longBankCode]) {
            return { 
                valid: true, 
                message: 'شماره حساب معتبر است', 
                bank: bankCodes[longBankCode],
                code: longBankCode
            };
        }
        
        return { valid: true, message: 'فرمت شماره حساب صحیح است' };
    }

    function showValidationState(state, message, bankInfo = null) {
        // Hide all states first
        document.querySelectorAll('.validation-spinner, .validation-success, .validation-error').forEach(el => {
            el.classList.add('hidden');
        });
        errorMessage.classList.add('hidden');
        successMessage.classList.add('hidden');
        bankContainer.classList.add('hidden');
        
        validationStatus.classList.remove('hidden');
        
        if (state === 'loading') {
            document.querySelector('.validation-spinner').classList.remove('hidden');
        } else if (state === 'success') {
            document.querySelector('.validation-success').classList.remove('hidden');
            successMessage.classList.remove('hidden');
            successText.textContent = message;
            
            if (bankInfo) {
                bankContainer.classList.remove('hidden');
                bankName.textContent = bankInfo.bank;
                bankCode.textContent = `کد بانک: ${bankInfo.code}`;
            }
        } else if (state === 'error') {
            document.querySelector('.validation-error').classList.remove('hidden');
            errorMessage.classList.remove('hidden');
            errorText.textContent = message;
        }
    }

    accountInput.addEventListener('input', function() {
        const value = this.value;
        const cleaned = value.replace(/\D/g, '');
        
        // Update character count
        charCount.textContent = cleaned.length;
        
        // Show format indicator
        if (cleaned.length > 0) {
            accountFormat.classList.remove('hidden');
        } else {
            accountFormat.classList.add('hidden');
        }
        
        // Validate in real-time
        if (cleaned.length === 0) {
            validationStatus.classList.add('hidden');
            errorMessage.classList.add('hidden');
            successMessage.classList.add('hidden');
            bankContainer.classList.add('hidden');
            return;
        }
        
        // Show loading state
        showValidationState('loading');
        
        // Simulate API delay
        setTimeout(() => {
            const validation = validateAccountNumber(cleaned);
            
            if (validation.valid) {
                showValidationState('success', validation.message, validation.bank ? {
                    bank: validation.bank,
                    code: validation.code
                } : null);
            } else {
                showValidationState('error', validation.message);
            }
        }, 500);
        
        // Update input value to cleaned version
        this.value = cleaned;
    });

    // Prevent non-numeric input
    accountInput.addEventListener('keypress', function(e) {
        if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Escape', 'Enter'].includes(e.key)) {
            e.preventDefault();
        }
    });
});
</script>

<style>
/* Enhanced input styling */
#accountNumber {
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
}

#accountNumber:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.validation-spinner svg {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Animation for bank detection */
#bankSelectionContainer {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>