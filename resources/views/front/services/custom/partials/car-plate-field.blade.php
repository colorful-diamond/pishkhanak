<!-- Car Plate Field Component -->
<div class="mb-6" id="car-plate-field">
    <label class="block text-sm font-medium text-dark-sky-500 mb-3">شماره پلاک خودرو</label>
    <div class="flex items-center flex-row-reverse justify-center gap-2 p-4 bg-white border-2 border-dark-sky-200 rounded-xl shadow-sm">
        <!-- Iranian Plate SVG -->
        <div class="flex-shrink-0 bg-dark-blue-600 p-2">
            <img src="{{ asset('assets/images/ir-plate.svg') }}" alt="پلاک ایران" class="w-4 h-7">
        </div>
        
        <!-- First 2 digits -->
        <div class="flex-shrink-0">
            <input type="tel" 
                   name="plate_part1" 
                   id="plate_part1"
                   maxlength="2" 
                   placeholder="12"
                   value="{{ old('plate_part1') }}"
                   class="w-12 h-12 text-center text-lg font-bold border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-normal focus:border-primary-normal ltr"
                   dir="ltr"
                   data-validate="required|digits:2">
        </div>
        
        <!-- Letter Selection Button -->
        <div class="flex-shrink-0 relative">
            <button type="button" 
                    id="plate_letter_btn"
                    class="w-16 h-12 text-center text-lg font-bold border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-normal focus:border-primary-normal bg-white cursor-pointer hover:bg-gray-50 transition-colors flex items-center justify-center"
                    onclick="openLetterModal()">
                <span id="selected_letter" class="{{ old('plate_letter') ? 'text-gray-900' : 'text-gray-400' }}">
                    {{ old('plate_letter') ?: 'انتخاب' }}
                </span>
            </button>
            <!-- Hidden input to store the selected value -->
            <input type="hidden" name="plate_letter" id="plate_letter" value="{{ old('plate_letter') }}" data-validate="required">
        </div>
        
        <!-- Middle 3 digits -->
        <div class="flex-shrink-0">
            <input type="tel" 
                   name="plate_part2" 
                   id="plate_part2"
                   maxlength="3" 
                   placeholder="123"
                   value="{{ old('plate_part2') }}"
                   class="w-16 h-12 text-center text-lg font-bold border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-normal focus:border-primary-normal ltr"
                   dir="ltr"
                   data-validate="required|digits:3">
        </div>
        
        <!-- Last 2 digits -->
        <div class="flex-shrink-0">
            <input type="tel" 
                   name="plate_part3" 
                   id="plate_part3"
                   maxlength="2" 
                   placeholder="34"
                   value="{{ old('plate_part3') }}"
                   class="w-12 h-12 text-center text-lg font-bold border border-gray-300 rounded-md focus:ring-2 focus:ring-primary-normal focus:border-primary-normal ltr"
                   dir="ltr"
                   data-validate="required|digits:2">
        </div>
    </div>
    
    <!-- Plate errors -->
    <div id="plate_part1-error" class="text-red-500 text-sm mt-1 hidden"></div>
    <div id="plate_letter-error" class="text-red-500 text-sm mt-1 hidden"></div>
    <div id="plate_part2-error" class="text-red-500 text-sm mt-1 hidden"></div>
    <div id="plate_part3-error" class="text-red-500 text-sm mt-1 hidden"></div>
</div>

<!-- Letter Selection Modal -->
<div id="letter_modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-96 overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">انتخاب حرف پلاک</h3>
            <button type="button" onclick="closeLetterModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-4 max-h-80 overflow-y-auto">
            <div class="grid grid-cols-6 gap-3" id="letter_grid">
                <!-- Persian Letters from specified list -->
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('الف')">الف</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ب')">ب</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ت')">ت</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ج')">ج</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('د')">د</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('س')">س</button>
                
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ص')">ص</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ط')">ط</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ع')">ع</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ق')">ق</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ل')">ل</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('م')">م</button>
                
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ن')">ن</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('و')">و</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ه')">ه</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ی')">ی</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ر')">ر</button>
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('ک')">ک</button>
                
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold" onclick="selectLetter('گ')">گ</button>
                
                <!-- Special Plates -->
                <button type="button" class="letter-btn w-12 h-12 border border-gray-300 rounded-md hover:bg-primary-normal hover:text-white transition-all duration-200 text-lg font-bold flex items-center justify-center" onclick="selectLetter('معلولین')" title="پلاک معلولین">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.4 11.2l-4.1.2 2.3-2.6c.2-.3.3-.8.2-1.3-.1-.3-.2-.6-.5-.8l-5.4-3.2c-.4-.3-1-.2-1.4.1L6.8 6.1c-.5.5-.6 1.2-.1 1.7.4.5 1.2.5 1.7.1l2-1.8 1.9 1.1-4.2 4.3c-.1.1-.1.2-.2.2-.5.2-1 .4-1.4.7L8 13.9c.5-.2 1-.4 1.5-.4 1.9 0 3.5 1.6 3.5 3.5 0 .6-.1 1.1-.4 1.5l1.5 1.5c.6-.9.9-1.9.9-3 0-1.2-.4-2.4-1.1-3.3l3.3-.3-.2 4.8c-.1.7.4 1.2 1.1 1.3h.1c.6 0 1.1-.5 1.2-1.1l.2-5.9c0-.3-.1-.7-.3-.9-.3-.3-.6-.4-.9-.4zM18 5.5c.5 0 1-.2 1.4-.6.4-.4.6-.9.6-1.4s-.2-1-.6-1.4c-.4-.4-.9-.6-1.4-.6s-1 .2-1.4.6c-.4.4-.6.9-.6 1.4s.2 1 .6 1.4c.4.4.9.6 1.4.6zm-5.5 16.1c-.9.6-1.9.9-3 .9C6.5 22.5 4 20 4 17c0-1.1.3-2.1.9-3l1.5 1.5c-.2.5-.4 1-.4 1.5 0 1.9 1.6 3.5 3.5 3.5.6 0 1.1-.1 1.5-.4l1.5 1.5z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if car plate field exists on this page
    if (!document.getElementById('car-plate-field')) {
        return;
    }
    
    // Initialize old values on page load (for validation errors)
    initializeOldValues();
    
    // Auto-focus to next field when typing plate number
    const plateInputs = ['plate_part1', 'plate_part2', 'plate_part3'];
    const plateLetterInput = document.getElementById('plate_letter');
    
    // Auto advance for numeric inputs
    plateInputs.forEach((inputId, index) => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function(e) {
                const value = e.target.value;
                const maxLength = parseInt(e.target.getAttribute('maxlength'));
                
                // Only allow numeric input
                e.target.value = value.replace(/[^0-9]/g, '');
                
                // Auto-advance to next field
                if (e.target.value.length === maxLength) {
                    if (index === 0) {
                        document.getElementById('plate_letter_btn').focus();
                    } else if (index === 1) {
                        document.getElementById('plate_part3').focus();
                    }
                }
            });
            
            // Handle backspace to go to previous field
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && e.target.value === '') {
                    if (index === 1) {
                        document.getElementById('plate_letter_btn').focus();
                    } else if (index === 2) {
                        document.getElementById('plate_part2').focus();
                    }
                }
            });
        }
    });
    
    // Close modal when clicking outside
    const letterModal = document.getElementById('letter_modal');
    if (letterModal) {
        letterModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeLetterModal();
            }
        });
    }
    
    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLetterModal();
        }
    });
});

// Open letter selection modal
function openLetterModal() {
    const modal = document.getElementById('letter_modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }
}

// Close letter selection modal
function closeLetterModal() {
    const modal = document.getElementById('letter_modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = ''; // Restore scrolling
    }
}

// Select letter and close modal
function selectLetter(letter) {
    const plateLetterInput = document.getElementById('plate_letter');
    const selectedLetterSpan = document.getElementById('selected_letter');
    
    if (plateLetterInput && selectedLetterSpan) {
        // Update hidden input value
        plateLetterInput.value = letter;
        
        // Update button text
        selectedLetterSpan.textContent = letter;
        selectedLetterSpan.classList.remove('text-gray-400');
        selectedLetterSpan.classList.add('text-gray-900');
        
        // Close modal
        closeLetterModal();
        
        // Auto-advance to next field
        setTimeout(() => {
            const nextField = document.getElementById('plate_part2');
            if (nextField) {
                nextField.focus();
            }
        }, 100);
    }
}

// Helper function to get plate number as string
function getPlateNumber() {
    const part1 = document.getElementById('plate_part1')?.value || '';
    const letter = document.getElementById('plate_letter')?.value || '';
    const part2 = document.getElementById('plate_part2')?.value || '';
    const part3 = document.getElementById('plate_part3')?.value || '';
    
    if (part1 && letter && part2 && part3) {
        return `${part1}${letter}${part2}${part3}`;
    }
    return null;
}

// Helper function to set plate number from string
function setPlateNumber(plateString) {
    if (!plateString || plateString.length < 6) return false;
    
    // Extract parts (assuming format: 12ب12345)
    const part1 = plateString.substring(0, 2);
    const letter = plateString.substring(2, 3);
    const part2 = plateString.substring(3, 6);
    const part3 = plateString.substring(6, 8);
    
    // Set values
    const part1Input = document.getElementById('plate_part1');
    const letterInput = document.getElementById('plate_letter');
    const part2Input = document.getElementById('plate_part2');
    const part3Input = document.getElementById('plate_part3');
    const selectedLetterSpan = document.getElementById('selected_letter');
    
    if (part1Input) part1Input.value = part1;
    if (letterInput) letterInput.value = letter;
    if (part2Input) part2Input.value = part2;
    if (part3Input) part3Input.value = part3;
    
    if (selectedLetterSpan && letter) {
        selectedLetterSpan.textContent = letter;
        selectedLetterSpan.classList.remove('text-gray-400');
        selectedLetterSpan.classList.add('text-gray-900');
    }
    
    return true;
}

// Initialize old values on page load (for validation errors)
function initializeOldValues() {
    const plateLetterInput = document.getElementById('plate_letter');
    const selectedLetterSpan = document.getElementById('selected_letter');
    
    // If there's an old letter value, make sure it's displayed correctly
    if (plateLetterInput && plateLetterInput.value && selectedLetterSpan) {
        selectedLetterSpan.textContent = plateLetterInput.value;
        selectedLetterSpan.classList.remove('text-gray-400');
        selectedLetterSpan.classList.add('text-gray-900');
    }
    
    // Trigger validation styling for filled fields
    const allInputs = ['plate_part1', 'plate_part2', 'plate_part3', 'national_code'];
    allInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input && input.value) {
            // Trigger any validation styling if field has value
            input.dispatchEvent(new Event('input'));
        }
    });
}
</script>
@endpush 