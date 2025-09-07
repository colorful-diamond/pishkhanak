document.addEventListener('DOMContentLoaded', function() {
    const refundButton = document.getElementById('refundButton');
    const increaseButton = document.getElementById('increaseButton');
    const chargeWalletForm = document.getElementById('chargeWalletForm');
    const selectedAmount = document.getElementById('selectedAmount');
    const amountDropdown = document.getElementById('amountDropdown');
    const paymentButton = document.getElementById('paymentButton');
    const transactionTable = document.getElementById('transactionTable');
    const dropdownControl = document.querySelector('.controls');
    const selectedAmountContainer = document.getElementById('selectedAmountContainer');

    refundButton.addEventListener('click', function() {
        // Show refund form modal or redirect to refund page
        const amount = prompt('مبلغ درخواستی عودت (تومان):');
        if (amount && !isNaN(amount) && amount >= 1000) {
            const bankAccount = prompt('شماره حساب بانکی:');
            if (bankAccount) {
                const description = prompt('توضیحات (اختیاری):') || 'درخواست عودت وجه';
                
                // Create and submit refund form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/user/wallet/refund';
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                                document.querySelector('input[name="_token"]')?.value;
                
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="amount" value="${amount}">
                    <input type="hidden" name="bank_account" value="${bankAccount}">
                    <input type="hidden" name="description" value="${description}">
                `;
                
                document.body.appendChild(form);
                form.submit();
            }
        } else if (amount !== null) {
            alert('مبلغ وارد شده معتبر نیست. حداقل مبلغ 1,000 تومان است.');
        }
    });

    increaseButton.addEventListener('click', function() {
        chargeWalletForm.classList.toggle('hidden');
        if (!chargeWalletForm.classList.contains('hidden')) {
            chargeWalletForm.classList.add('animate-fadeIn');
            transactionTable.classList.add('animate-fadeOut');
            setTimeout(() => {
                transactionTable.classList.add('hidden');
                transactionTable.classList.remove('animate-fadeOut');
            }, 300);
        } else {
            chargeWalletForm.classList.remove('animate-fadeIn');
            transactionTable.classList.remove('hidden');
            transactionTable.classList.add('animate-fadeIn');
        }
    });

    // Custom select box functionality
    selectedAmountContainer.addEventListener('click', function() {
        toggleDropdown();
    });

    function toggleDropdown() {
        if (amountDropdown.classList.contains('hidden')) {
            openDropdown();
        } else {
            closeDropdown();
        }
    }

    function openDropdown() {
        amountDropdown.classList.remove('hidden');
        amountDropdown.style.maxHeight = '0px';
        amountDropdown.style.opacity = '0';
        dropdownControl.classList.add('rotate-180');
        
        setTimeout(() => {
            amountDropdown.style.maxHeight = amountDropdown.scrollHeight + 'px';
            amountDropdown.style.opacity = '1';
        }, 10);
    }

    function closeDropdown() {
        amountDropdown.style.maxHeight = '0px';
        amountDropdown.style.opacity = '0';
        dropdownControl.classList.remove('rotate-180');
        
        setTimeout(() => {
            amountDropdown.classList.add('hidden');
        }, 300);
    }

    amountDropdown.addEventListener('click', function(e) {
        const menuItem = e.target.closest('.menu-item');
        if (menuItem) {
            const amount = menuItem.dataset.amount;
            selectedAmount.textContent = formatAmount(amount) + ' تومان';
            
            // Update hidden input value
            const amountInput = document.getElementById('amountInput');
            if (amountInput) {
                amountInput.value = amount;
            }
            
            // Remove active class from all menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('bg-blue-50', 'hover:bg-sky-100');
                item.classList.add('bg-sky-50', 'hover:bg-sky-50');
            });

            // Add active class to selected menu item
            menuItem.classList.remove('bg-sky-50', 'hover:bg-sky-50');
            menuItem.classList.add('bg-blue-50', 'hover:bg-sky-100');
            
            closeDropdown();
        }
    });

    // Load payment gateways dynamically
    loadPaymentGateways();

    // Form validation before submit
    const chargeForm = document.getElementById('chargeWalletForm');
    if (chargeForm) {
        chargeForm.addEventListener('submit', function(e) {
            const gatewayId = document.getElementById('gateway_id');
            const amount = chargeForm.querySelector('input[name="amount"]');
            
            if (!gatewayId.value) {
                e.preventDefault();
                alert('لطفاً روش پرداخت را انتخاب کنید.');
                return false;
            }
            
            if (!amount.value || amount.value < 1000) {
                e.preventDefault();
                alert('مبلغ باید حداقل 1,000 تومان باشد.');
                return false;
            }
        });
    }

    // Load payment gateways from API
    async function loadPaymentGateways() {
        try {
            const amount = document.getElementById('amountInput').value || 100000;
            console.log('Loading gateways for amount:', amount);
            
            const response = await fetch(`/payment/gateways?amount=${amount}&currency=IRR`);
            console.log('Gateway response status:', response.status);
            
            const data = await response.json();
            console.log('Gateway response data:', data);
            
            if (data.success && data.gateways.length > 0) {
                renderPaymentGateways(data.gateways);
            } else {
                console.log('No gateways found or error in response');
                showGatewayError('هیچ درگاه پرداخت فعالی یافت نشد.');
            }
        } catch (error) {
            console.error('خطا در بارگیری درگاه‌های پرداخت:', error);
            showGatewayError('خطا در بارگیری درگاه‌های پرداخت.');
        }
    }

    // Render payment gateways
    function renderPaymentGateways(gateways) {
        const gatewaySelection = document.getElementById('gateway-selection');
        
        let html = '<div class="gateway-options space-y-3">';
        
        gateways.forEach((gateway, index) => {
            const isChecked = gateway.is_default || index === 0 ? 'checked' : '';
            const feeText = gateway.fee > 0 ? `(کارمزد: ${formatAmount(gateway.fee)} ریال)` : '(بدون کارمزد)';
            
            html += `
                <div class="gateway-option">
                    <input type="radio" 
                           id="gateway_${gateway.id}" 
                           name="gateway_selection" 
                           value="${gateway.id}"
                           class="peer hidden"
                           ${isChecked}
                           onchange="selectGateway(${gateway.id}, '${gateway.name}', ${gateway.fee})">
                    <label for="gateway_${gateway.id}" 
                           class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all hover:border-sky-300 peer-checked:border-sky-500 peer-checked:bg-sky-50">
                        <div class="flex items-center gap-3">
                            ${gateway.logo_url ? `<img src="${gateway.logo_url}" alt="${gateway.name}" class="w-8 h-8 rounded">` : ''}
                            <div>
                                <div class="font-medium text-gray-900">${gateway.name}</div>
                                <div class="text-sm text-gray-600">${feeText}</div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">
                            مجموع: ${formatAmount(gateway.total_amount)} ریال
                        </div>
                    </label>
                </div>
            `;
        });
        
        html += '</div>';
        gatewaySelection.innerHTML = html;
        
        // Set default gateway
        const defaultGateway = gateways.find(g => g.is_default) || gateways[0];
        selectGateway(defaultGateway.id, defaultGateway.name, defaultGateway.fee);
    }

    // Show gateway error message
    function showGatewayError(message) {
        const gatewaySelection = document.getElementById('gateway-selection');
        gatewaySelection.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-red-800 text-sm font-medium">${message}</span>
                </div>
                <p class="text-red-600 text-sm mt-2">لطفاً با پشتیبانی تماس بگیرید یا بعداً تلاش کنید.</p>
            </div>
        `;
    }

    // Select gateway
    function selectGateway(gatewayId, gatewayName, fee) {
        document.getElementById('gateway_id').value = gatewayId;
        
        // Update payment button text
        const paymentButton = document.getElementById('paymentButton');
        if (paymentButton) {
            paymentButton.innerHTML = `
                <div class="value text-right text-white text-lg font-medium font-['IRANSansWebFaNum'] capitalize leading-normal">
                    پرداخت با ${gatewayName}
                </div>
                <svg class="w-6 h-6 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            `;
        }
    }

    // Update gateways when amount changes
    document.getElementById('amountInput').addEventListener('change', function() {
        loadPaymentGateways();
    });

    // Make functions global for onclick handlers
    window.selectGateway = selectGateway;
    window.loadPaymentGateways = loadPaymentGateways;

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#selectedAmountContainer') && !e.target.closest('#amountDropdown')) {
            closeDropdown();
        }
    });

    function formatAmount(amount) {
        return new Intl.NumberFormat('fa-IR').format(amount);
    }

    // Add animation for wallet amount
    const walletAmount = document.querySelector('.amount');
    walletAmount.classList.add('animate-pulse');
});
