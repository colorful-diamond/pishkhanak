@if (session('success') && session('result'))
    <div class="mt-6 p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">نتیجه تبدیل</h3>
        
        <div class="space-y-3">
            @foreach (session('result') as $key => $value)
                @if (!in_array($key, ['conversion_date']))
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                        <span class="text-sm font-medium text-gray-600">
                            @switch($key)
                                @case('card_number')
                                    شماره کارت:
                                    @break
                                @case('iban')
                                    شماره شبا:
                                    @break
                                @case('account_number')
                                    شماره حساب:
                                    @break
                                @case('bank_name')
                                    نام بانک:
                                    @break
                                @case('account_type')
                                    نوع حساب:
                                    @break
                                @case('branch_code')
                                    کد شعبه:
                                    @break
                                @default
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}:
                            @endswitch
                        </span>
                        <span class="text-sm text-gray-900 font-mono">{{ $value }}</span>
                    </div>
                @endif
            @endforeach
            
            @if (isset(session('result')['conversion_date']))
                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                    <span class="text-sm font-medium text-gray-600">تاریخ تبدیل:</span>
                    <span class="text-sm text-gray-900">{{ session('result')['conversion_date'] }}</span>
                </div>
            @endif
        </div>
        
        <!-- Copy Button -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <button 
                type="button" 
                onclick="copyResults()"
                class="w-full px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700 transition-colors"
            >
                کپی نتیجه
            </button>
        </div>
    </div>

    <script>
        function copyResults() {
            const results = @json(session('result'));
            let textToCopy = '';
            
            // Copy only the main result (IBAN, account number, etc.)
            if (results.iban) {
                textToCopy = results.iban;
            } else if (results.account_number) {
                textToCopy = results.account_number;
            } else if (results.card_number) {
                textToCopy = results.card_number;
            } else if (results.is_valid !== undefined) {
                textToCopy = results.is_valid ? 'معتبر' : 'نامعتبر';
            } else {
                // Fallback: copy the first available result
                for (const [key, value] of Object.entries(results)) {
                    if (!['conversion_date', 'processed_at', 'result_id'].includes(key) && value) {
                        textToCopy = value;
                        break;
                    }
                }
            }
            
            navigator.clipboard.writeText(textToCopy).then(() => {
                // Show success message
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'کپی شد!';
                button.classList.add('bg-green-600');
                button.classList.remove('bg-sky-600', 'hover:bg-sky-700');
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-sky-600', 'hover:bg-sky-700');
                }, 2000);
            });
        }
        
        function getLabel(key) {
            const labels = {
                'card_number': 'شماره کارت',
                'iban': 'شماره شبا',
                'account_number': 'شماره حساب',
                'bank_name': 'نام بانک',
                'account_type': 'نوع حساب',
                'branch_code': 'کد شعبه'
            };
            
            return labels[key] || key;
        }
    </script>
@endif 