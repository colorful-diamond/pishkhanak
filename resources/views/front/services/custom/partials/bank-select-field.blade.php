@php
    // Get banks from the view data or use Bank model
    $banks = $banks ?? \App\Models\Bank::getAll();

    // Convert array data to objects for consistent access
    if (is_array($banks) && !empty($banks) && is_array($banks[0])) {
        $banks = collect($banks)->map(function ($bank) {
            return (object) $bank;
        });
    }
@endphp

<style>
/* Match card field style */
#bank_search {
    font-weight: bold;
    direction: ltr;
    padding: 0.75rem;
    background-color: #e0f2fe;
    border-radius: 0.5rem;
    border: 1px solid #7dd3fc;
    color: #0f172a;
    text-align: center;
    transition: all 0.3s;
    width: 100%;
    outline: none;
}
#bank_search:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 2px #38bdf8;
    background-color: #f0f9ff;
}
#selected_bank_logo img {
    border-radius: 4px;
    object-fit: contain;
}
.bank-option {
    transition: all 0.2s ease;
    border-radius: 0.5rem;
    margin: 0.25rem 0;
    border: 1px solid transparent;
}
.bank-option:hover {
    background-color: #f0f9ff;
}
.bank-option.selected {
    background-color: #e0f2fe;
    border-color: #0ea5e9;
}
#bank_dropdown {
    scrollbar-width: thin;
    scrollbar-color: #bae6fd #f0f9ff;
}
#bank_dropdown::-webkit-scrollbar {
    width: 6px;
}
#bank_dropdown::-webkit-scrollbar-track {
    background: #f0f9ff;
    border-radius: 3px;
}
#bank_dropdown::-webkit-scrollbar-thumb {
    background: #bae6fd;
    border-radius: 3px;
}
#bank_dropdown::-webkit-scrollbar-thumb:hover {
    background: #7dd3fc;
}
</style>

<div>
    <label for="bank_id" class="block text-sm font-medium text-dark-sky-500 mb-1">بانک</label>
    <div class="relative">
        <div class="relative">
            <input type="text"
                   id="bank_search"
                   class="font-bold dir-ltr p-3 bg-sky-100 rounded-lg border border-sky-300 text-dark-sky-600 placeholder-dark-sky-300 focus:ring-2 focus:ring-primary-normal focus:border-primary-normal text-center transition-all duration-300 w-full"
                   placeholder="جستجو در بانک‌ها..."
                   autocomplete="off"
                   dir="ltr"
            >
            <!-- Search Icon -->
            <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <!-- Selected Bank Logo -->
            <div id="selected_bank_logo" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                <img id="bank_logo_img" src="" alt="" class="w-6 h-6">
            </div>
        </div>
        <!-- Hidden Select for Form Submission -->
        <select id="bank_id" name="bank_id" class="hidden" data-validate="required">
            <option value="">انتخاب بانک</option>
            @foreach($banks as $bank)
                <option value="{{ $bank->id }}"
                        data-name="{{ $bank->name }}"
                        data-logo="{{ \Illuminate\Support\Str::startsWith($bank->logo, 'http') ? $bank->logo : asset('assets/images/banks/' . $bank->logo) }}"
                        data-color="{{ $bank->color }}"
                        {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                    {{ $bank->name }}
                </option>
            @endforeach
        </select>
        <!-- Dropdown -->
        <div id="bank_dropdown" class="absolute z-50 w-full mt-1 bg-white border border-sky-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
            @foreach($banks as $bank)
                <div class="bank-option cursor-pointer px-3 py-2 flex items-center space-x-3 space-x-reverse"
                     data-bank-id="{{ $bank->id }}"
                     data-bank-name="{{ $bank->name }}"
                     data-bank-logo="{{ \Illuminate\Support\Str::startsWith($bank->logo, 'http') ? $bank->logo : asset('assets/images/banks/' . $bank->logo) }}"
                     data-bank-color="{{ $bank->color }}">
                                        <!-- Bank Logo -->
                    <div class="flex-shrink-0">
                        <img src="{{ \Illuminate\Support\Str::startsWith($bank->logo, 'http') ? $bank->logo : asset('assets/images/banks/' . $bank->logo) }}"
                             alt="{{ $bank->name }}"
                             class="w-8 h-8 rounded"
                             onerror="this.style.display='none'; console.error('Failed to load logo: {{ $bank->logo }}');">
                    </div>
                    <!-- Bank Name -->
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ $bank->name }}</div>
                        @if($bank->en_name)
                            <div class="text-xs text-gray-500">{{ $bank->en_name }}</div>
                        @endif
                    </div>
                    <!-- Selection Indicator -->
                    <div class="flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div id="bank_id-error" class="text-red-500 text-sm mt-1 hidden"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bankSearch = document.getElementById('bank_search');
    const bankDropdown = document.getElementById('bank_dropdown');
    const bankSelect = document.getElementById('bank_id');
    const selectedBankLogo = document.getElementById('selected_bank_logo');
    const bankLogoImg = document.getElementById('bank_logo_img');
    const bankOptions = document.querySelectorAll('.bank-option');

    // Show dropdown on focus
    bankSearch.addEventListener('focus', function() {
        bankDropdown.classList.remove('hidden');
        filterBanks();
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!bankSearch.contains(e.target) && !bankDropdown.contains(e.target)) {
            bankDropdown.classList.add('hidden');
        }
    });

    // Filter banks based on search
    bankSearch.addEventListener('input', filterBanks);

    // Handle bank selection
    bankOptions.forEach(option => {
        option.addEventListener('click', function() {
            const bankId = this.dataset.bankId;
            const bankName = this.dataset.bankName;
            const bankLogo = this.dataset.bankLogo;
            const bankColor = this.dataset.bankColor;

            // Update search input
            bankSearch.value = bankName;

            // Update hidden select
            bankSelect.value = bankId;
            
            // Debug bank selection
            console.log('🔍 Bank selected:', {
                bankId: bankId,
                bankName: bankName,
                selectValue: bankSelect.value,
                selectElement: bankSelect
            });

            // Show selected bank logo
            bankLogoImg.src = bankLogo;
            bankLogoImg.alt = bankName;
            bankLogoImg.onerror = function() { 
                this.style.display = 'none'; 
                console.error('Failed to load selected logo:', bankLogo); 
            };
            selectedBankLogo.classList.remove('hidden');

            // Change background color to a very light version of the bank color (like card field)
            bankSearch.style.backgroundColor = getLightColor(bankColor);
            bankSearch.style.borderColor = bankColor;

            // Hide dropdown
            bankDropdown.classList.add('hidden');

            // Remove selected class from all, add to this
            bankOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');

            // Trigger change event
            bankSelect.dispatchEvent(new Event('change'));
            
            // Final debug
            console.log('🔍 Final bank selection state:', {
                hiddenSelectValue: bankSelect.value,
                visibleSearchValue: bankSearch.value
            });
        });
    });

    // Filter banks function
    function filterBanks() {
        const searchTerm = bankSearch.value.toLowerCase();
        bankOptions.forEach(option => {
            const bankName = option.dataset.bankName.toLowerCase();
            const bankEnName = option.querySelector('.text-xs')?.textContent.toLowerCase() || '';
            if (bankName.includes(searchTerm) || bankEnName.includes(searchTerm)) {
                option.style.display = 'flex';
            } else {
                option.style.display = 'none';
            }
        });
    }

    // Convert hex color to very light version (95% white, 5% color)
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

    // Initialize with selected value if exists
    const selectedOption = bankSelect.querySelector('option[selected]');
    if (selectedOption) {
        const bankName = selectedOption.dataset.name;
        const bankLogo = selectedOption.dataset.logo;
        const bankColor = selectedOption.dataset.color;
        bankSearch.value = bankName;
        bankLogoImg.src = bankLogo;
        bankLogoImg.alt = bankName;
        bankLogoImg.onerror = function() { 
            this.style.display = 'none'; 
            console.error('Failed to load initial logo:', bankLogo); 
        };
        selectedBankLogo.classList.remove('hidden');
        bankSearch.style.backgroundColor = getLightColor(bankColor);
        bankSearch.style.borderColor = bankColor;
        // Mark selected in dropdown
        bankOptions.forEach(opt => {
            if (opt.dataset.bankId === selectedOption.value) {
                opt.classList.add('selected');
            } else {
                opt.classList.remove('selected');
            }
        });
    }
    
    // Debug form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const formData = new FormData(form);
            const formObj = {};
            formData.forEach((value, key) => {
                formObj[key] = value;
            });
            
            console.log('🔍 Form being submitted with data:', formObj);
            console.log('🔍 Bank select value before submit:', bankSelect.value);
            
            // If bank_id is empty, prevent submission and alert
            if (!bankSelect.value || bankSelect.value === '') {
                console.error('❌ Bank not selected! Form submission prevented.');
                alert('لطفاً ابتدا بانک را انتخاب کنید.');
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>