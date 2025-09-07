<div>
    <label for="card_number" class="block text-sm font-medium text-dark-sky-500 mb-1">شماره 16 رقمی کارت</label>
    <div class="relative">
        <div id="bank_logo_container" class="absolute left-3 top-1/2 transform -translate-y-1/2 hidden">
            <img id="bank_logo_inline" src="" alt="" class="w-6 h-6">
        </div>
        <input type="tel" id="card_number" name="card_number" placeholder="**** **** **** ****"
               class="w-full font-bold dir-ltr p-3 bg-sky-100 rounded-lg border border-sky-300 text-dark-sky-600 placeholder-dark-sky-300 focus:ring-2 focus:ring-primary-normal focus:border-primary-normal text-center transition-all duration-300"
               dir="ltr" data-validate="required|card_number" value="{{ old('card_number') }}" maxlength="19">
    </div>
    <div id="card_number-error" class="text-red-500 text-sm mt-1 hidden"></div>
</div>

<script>
// Iranian Banks Data (from bank-data.js)
const banks = [
    { "key": "ansar", "name": "بانک انصار", "en_name": "ansar", "id": "063", "logo": "/assets/images/banks/ansar.svg", "card_prefixes": ["627381"], "color": "#c8393b" },
    { "key": "ayandeh", "name": "بانک آینده", "en_name": "ayandeh", "id": "062", "logo": "/assets/images/banks/ayandeh.svg", "card_prefixes": ["636214"], "color": "#492631" },
    { "key": "blu", "name": "بلوبانک", "en_name": "blu", "id": "056", "logo": "/assets/images/banks/blu.svg", "card_prefixes": ["62198619"], "color": "#27A2F0" },
    { "key": "dey", "name": "بانک دی", "en_name": "dey", "id": "066", "logo": "/assets/images/banks/day.svg", "card_prefixes": ["502938"], "color": "#008a9f" },
    { "key": "eghtesad_novin", "name": "بانک اقتصاد نوین", "en_name": "eghtesad_novin", "id": "055", "logo": "/assets/images/banks/eghtesad.svg", "card_prefixes": ["627412"], "color": "#5c2e91" },
    { "key": "gardeshgari", "name": "بانک گردشگری", "en_name": "gardeshgari", "id": "064", "logo": "/assets/images/banks/gardeshgari.svg", "card_prefixes": ["505416"], "color": "#af0a0f" },
    { "key": "ghavvamin", "name": "بانک قوامین", "en_name": "ghavvamin", "id": "052", "logo": "/assets/images/banks/ghavvamin.svg", "card_prefixes": ["639599"], "color": "#0e8a42" },
    { "key": "karafarin", "name": "بانک کارآفرین", "en_name": "karafarin", "id": "053", "logo": "/assets/images/banks/karafarin.svg", "card_prefixes": ["627488", "502910"], "color": "#168474" },
    { "key": "keshavarzi", "name": "بانک کشاورزی", "en_name": "keshavarzi", "id": "016", "logo": "/assets/images/banks/keshavarzi.svg", "card_prefixes": ["603770", "639217"], "color": "#112c09" },
    { "key": "maskan", "name": "بانک مسکن", "en_name": "maskan", "id": "014", "logo": "/assets/images/banks/maskan.svg", "card_prefixes": ["628023"], "color": "#ff0100" },
    { "key": "mehr_e_eghtesad", "name": "بانک مهر اقتصاد", "en_name": "mehr_e_eghtesad", "id": "079", "logo": "/assets/images/banks/mehreghtesad.svg", "card_prefixes": ["639370"], "color": "#00a653" },
    { "key": "mehriran", "name": "بانک قرض الحسنه مهر ایران", "en_name": "mehr_e_iranian", "id": "060", "logo": "/assets/images/banks/mehriran.svg", "card_prefixes": ["606373"], "color": "#00a653" },
    { "key": "melli", "name": "بانک ملی ایران", "en_name": "meli", "id": "017", "logo": "/assets/images/banks/melli.svg", "card_prefixes": ["603799"], "color": "#202f5b" },
    { "key": "mellat", "name": "بانک ملت", "en_name": "mellat", "id": "012", "logo": "/assets/images/banks/mellat.svg", "card_prefixes": ["610433", "991975"], "color": "#d12236" },
    { "key": "melal", "name": "موسسه اعتباری ملل", "en_name": "melal", "id": "075", "logo": "/assets/images/banks/melal.svg", "card_prefixes": ["606256"], "color": "#37389a" },
    { "key": "parsian", "name": "بانک پارسیان", "en_name": "parsian", "id": "054", "logo": "/assets/images/banks/parsian.svg", "card_prefixes": ["622106", "639194", "627884"], "color": "#a10f1f" },
    { "key": "pasargad", "name": "بانک پاسارگاد", "en_name": "pasargad", "id": "057", "logo": "/assets/images/banks/pasargad.svg", "card_prefixes": ["502229", "639347"], "color": "#ffc110" },
    { "key": "post", "name": "پست بانک ایران", "en_name": "post_bank", "id": "021", "logo": "/assets/images/banks/post.svg", "card_prefixes": ["627760"], "color": "#008840" },
    { "key": "refahkargaran", "name": "بانک رفاه کارگران", "en_name": "refah", "id": "013", "logo": "/assets/images/banks/refahkargaran.svg", "card_prefixes": ["589463"], "color": "#1e7a00" },
    { "key": "sanatmadan", "name": "بانک صنعت و معدن", "en_name": "sanat_va_maadan", "id": "011", "logo": "/assets/images/banks/sanatmadan.svg", "card_prefixes": ["627961"], "color": "#0f317e" },
    { "key": "saderat", "name": "بانک صادرات", "en_name": "saderat", "id": "019", "logo": "/assets/images/banks/saderat.svg", "card_prefixes": ["603769"], "color": "#29166f" },
    { "key": "saman", "name": "بانک سامان", "en_name": "saman", "id": "056", "logo": "/assets/images/banks/saman.svg", "card_prefixes": ["621986"], "color": "#00aae8" },
    { "key": "sarmaye", "name": "بانک سرمایه", "en_name": "sarmayeh", "id": "058", "logo": "/assets/images/banks/sarmaye.svg", "card_prefixes": ["639607"], "color": "#a7a7a7" },
    { "key": "sepah", "name": "بانک سپه", "en_name": "sepah", "id": "015", "logo": "/assets/images/banks/sepah.svg", "card_prefixes": ["589210"], "color": "#0093dd" },
    { "key": "shahr", "name": "بانک شهر", "en_name": "shahr", "id": "061", "logo": "/assets/images/banks/shahr.svg", "card_prefixes": ["502806", "504706"], "color": "#d00" },
    { "key": "sina", "name": "بانک سینا", "en_name": "sina", "id": "059", "logo": "/assets/images/banks/sina.svg", "card_prefixes": ["639346"], "color": "#16469c" },
    { "key": "hekmat", "name": "بانک حکمت ایرانیان", "en_name": "hekmat", "id": "065", "logo": "/assets/images/banks/hekmat.svg", "card_prefixes": ["636949"], "color": "#0057a1" },
    { "key": "tosesaderat", "name": "بانک توسعه صادرات", "en_name": "tosee_saderat", "id": "020", "logo": "/assets/images/banks/tosesaderat.svg", "card_prefixes": ["627648", "207177"], "color": "#066e16" },
    { "key": "tosetaavon", "name": "بانک توسعه تعاون", "en_name": "tosee_taavon", "id": "022", "logo": "/assets/images/banks/tosetaavon.svg", "card_prefixes": ["502908"], "color": "#0b8a93" },
    { "key": "tejarat", "name": "بانک تجارت", "en_name": "tejarat", "id": "018", "logo": "/assets/images/banks/tejarat.svg", "card_prefixes": ["627353", "585983"], "color": "#1f0d8a" },
    { "key": "iranzamin", "name": "بانک ایران زمین", "en_name": "iranzamin", "id": "069", "logo": "/assets/images/banks/iranzamin.svg", "card_prefixes": ["505785"], "color": "#490fa2" },
    { "key": "khavarmianeh", "name": "بانک خاورمیانه", "en_name": "khavarmianeh", "id": "080", "logo": "/assets/images/banks/khavarmianeh.svg", "card_prefixes": ["585947"], "color": "#f7941e" },
    { "key": "resalat", "name": "بانک قرض‌ الحسنه رسالت", "en_name": "resalat", "id": "070", "logo": "/assets/images/banks/resalat.svg", "card_prefixes": ["504172"], "color": "#0092cf" }
];

document.addEventListener('DOMContentLoaded', function() {
    const cardInput = document.getElementById('card_number');
    const errorDiv = document.getElementById('card_number-error');
    const bankInfoDiv = document.getElementById('card_bank_info');
    const bankLogo = document.getElementById('bank_logo');
    const bankName = document.getElementById('bank_name');
    const bankLogoContainer = document.getElementById('bank_logo_container');
    const bankLogoInline = document.getElementById('bank_logo_inline');
    
    if (cardInput) {
        // Convert hex color to very light version (95% white)
        function getLightColor(hexColor) {
            // Remove # if present
            hexColor = hexColor.replace('#', '');
            
            // Parse RGB values
            const r = parseInt(hexColor.substr(0, 2), 16);
            const g = parseInt(hexColor.substr(2, 2), 16);
            const b = parseInt(hexColor.substr(4, 2), 16);
            
            // Mix with white (95% white, 5% original color)
            const lightR = Math.round(r * 0.05 + 255 * 0.95);
            const lightG = Math.round(g * 0.05 + 255 * 0.95);
            const lightB = Math.round(b * 0.05 + 255 * 0.95);
            
            // Convert back to hex
            return `#${lightR.toString(16).padStart(2, '0')}${lightG.toString(16).padStart(2, '0')}${lightB.toString(16).padStart(2, '0')}`;
        }
        
        // Luhn algorithm for card validation
        function luhnCheck(cardNumber) {
            if (!cardNumber || cardNumber.length !== 16) return false;
            
            let sum = 0;
            let isEven = false;
            
            // Loop through values starting from the rightmost side
            for (let i = cardNumber.length - 1; i >= 0; i--) {
                let digit = parseInt(cardNumber.charAt(i));
                
                if (isEven) {
                    digit *= 2;
                    if (digit > 9) {
                        digit -= 9;
                    }
                }
                
                sum += digit;
                isEven = !isEven;
            }
            
            return (sum % 10) === 0;
        }
        
        // Identify bank by card prefix
        function identifyBank(cardNumber) {
            if (!cardNumber || cardNumber.length < 6) return null;
            
            const cleanNumber = cardNumber.replace(/\D/g, '');
            
            for (const bank of banks) {
                for (const prefix of bank.card_prefixes) {
                    if (cleanNumber.startsWith(prefix)) {
                        return bank;
                    }
                }
            }
            
            return null;
        }
        
        // Validate card number
        function validateCard(cardNumber) {
            const cleanNumber = cardNumber.replace(/\D/g, '');
            
            // Check length
            if (cleanNumber.length !== 16) {
                return {
                    isValid: false,
                    message: 'شماره کارت باید 16 رقم باشد.',
                    bank: null
                };
            }
            
            // Check if all characters are digits
            if (!/^\d{16}$/.test(cleanNumber)) {
                return {
                    isValid: false,
                    message: 'شماره کارت باید فقط شامل اعداد باشد.',
                    bank: null
                };
            }
            
            // Check Luhn algorithm
            if (!luhnCheck(cleanNumber)) {
                return {
                    isValid: false,
                    message: 'شماره کارت نامعتبر است.',
                    bank: null
                };
            }
            
            // Identify bank
            const bank = identifyBank(cleanNumber);
            
            if (!bank) {
                return {
                    isValid: false,
                    message: 'بانک صادرکننده کارت شناسایی نشد.',
                    bank: null
                };
            }
            
            return {
                isValid: true,
                message: 'شماره کارت معتبر است.',
                bank: bank
            };
        }
        
        // Show/hide error message
        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
            bankInfoDiv.classList.add('hidden');
            bankLogoContainer.classList.add('hidden');
            cardInput.classList.add('border-red-500');
            cardInput.classList.remove('border-green-500');
            cardInput.style.backgroundColor = '#f0f9ff'; // Reset to default light blue
            cardInput.style.paddingLeft = '12px'; // Reset padding
        }
        
        function hideError() {
            errorDiv.classList.add('hidden');
            cardInput.classList.remove('border-red-500');
        }
        
        // Show bank information
        function showBankInfo(bank) {
            // Update inline bank logo
            bankLogoInline.src = bank.logo;
            bankLogoInline.alt = bank.name;
            bankLogoContainer.classList.remove('hidden');
            
            // Update background color to very light bank color
            const lightColor = getLightColor(bank.color);
            cardInput.style.backgroundColor = lightColor;
            
            // Add left padding to make room for logo
            cardInput.style.paddingLeft = '48px';
            
            // Update external bank info
            bankLogo.src = bank.logo;
            bankLogo.alt = bank.name;
            bankName.textContent = bank.name;
            bankInfoDiv.classList.remove('hidden');
            cardInput.classList.add('border-green-500');
            cardInput.classList.remove('border-red-500');
        }

        // Show bank information (early detection version)
        function showBankInfoEarly(bank) {
            // Update inline bank logo
            bankLogoInline.src = bank.logo;
            bankLogoInline.alt = bank.name;
            bankLogoContainer.classList.remove('hidden');
            
            // Update background color to very light bank color
            const lightColor = getLightColor(bank.color);
            cardInput.style.backgroundColor = lightColor;
            
            // Add left padding to make room for logo
            cardInput.style.paddingLeft = '48px';
            
            // Update external bank info
            bankLogo.src = bank.logo;
            bankLogo.alt = bank.name;
            bankName.textContent = bank.name;
            bankInfoDiv.classList.remove('hidden');
            
            // Don't add green border yet (wait for full validation)
            cardInput.classList.remove('border-red-500');
        }
        
        // Reset field styling
        function resetFieldStyling() {
            bankLogoContainer.classList.add('hidden');
            cardInput.style.backgroundColor = '#f0f9ff'; // Default light blue
            cardInput.style.paddingLeft = '12px'; // Default padding
            bankInfoDiv.classList.add('hidden');
            cardInput.classList.remove('border-green-500', 'border-red-500');
        }
        
        // Format card number with spaces every 4 digits
        function formatCardNumber(value) {
            // Remove all non-digit characters
            const digits = value.replace(/\D/g, '');
            
            // Add spaces every 4 digits
            const formatted = digits.replace(/(\d{4})(?=\d)/g, '$1 ');
            
            return formatted;
        }
        
        // Handle input event for real-time formatting and validation
        cardInput.addEventListener('input', function(e) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const oldLength = oldValue.length;
            
            // Format the value
            const formattedValue = formatCardNumber(e.target.value);
            e.target.value = formattedValue;
            
            // Calculate new cursor position
            let newCursorPosition = cursorPosition;
            const newLength = formattedValue.length;
            
            // Adjust cursor position based on added/removed spaces
            if (newLength > oldLength) {
                // A space was added, move cursor forward
                if (cursorPosition % 5 === 0 && cursorPosition > 0) {
                    newCursorPosition = cursorPosition + 1;
                }
            } else if (newLength < oldLength) {
                // A character was removed, adjust cursor if needed
                if (cursorPosition > 0 && oldValue[cursorPosition - 1] === ' ') {
                    newCursorPosition = cursorPosition - 1;
                }
            }
            
            // Set cursor position
            e.target.setSelectionRange(newCursorPosition, newCursorPosition);
            
            // Validate card number
            const cleanNumber = formattedValue.replace(/\D/g, '');
            
            if (cleanNumber.length === 16) {
                // Full validation for complete card number
                const validation = validateCard(cleanNumber);
                
                if (validation.isValid) {
                    hideError();
                    showBankInfo(validation.bank);
                } else {
                    showError(validation.message);
                }
            } else if (cleanNumber.length >= 6) {
                // Early bank detection for partial card numbers
                hideError();
                
                // Try to identify bank from partial number
                const bank = identifyBank(cleanNumber);
                
                if (bank) {
                    showBankInfoEarly(bank);
                } else {
                    resetFieldStyling();
                }
            } else if (cleanNumber.length > 0) {
                hideError();
                resetFieldStyling();
            }
        });
        
        // Handle keydown for backspace and delete
        cardInput.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' || e.key === 'Delete') {
                const cursorPosition = e.target.selectionStart;
                const value = e.target.value;
                
                // If cursor is at a space, move it back one position
                if (e.key === 'Backspace' && cursorPosition > 0 && value[cursorPosition - 1] === ' ') {
                    e.preventDefault();
                    e.target.setSelectionRange(cursorPosition - 1, cursorPosition - 1);
                }
            }
        });
        
        // Handle paste event
        cardInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pastedText.replace(/\D/g, '');
            
            // Limit to 16 digits
            const limitedDigits = digits.substring(0, 16);
            const formattedValue = formatCardNumber(limitedDigits);
            
            // Set the formatted value
            this.value = formattedValue;
            
            // Trigger input event for validation
            this.dispatchEvent(new Event('input'));
        });
        
        // Handle blur event for final validation
        cardInput.addEventListener('blur', function() {
            const cleanNumber = this.value.replace(/\D/g, '');
            
            if (cleanNumber.length > 0 && cleanNumber.length < 16) {
                showError('شماره کارت ناقص است.');
            }
        });
        
        // Format existing value on page load
        if (cardInput.value) {
            cardInput.value = formatCardNumber(cardInput.value);
            // Trigger validation for existing value
            cardInput.dispatchEvent(new Event('input'));
        }
    }
});
</script> 