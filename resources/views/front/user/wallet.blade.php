@extends('front.layouts.app')

@section('content')
<div class="">
    <div class="container mx-auto px-4 py-3">
        <!-- Header - more compact -->
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-gray-900 mb-1">کیف پول</h1>
                    <p class="text-gray-600 text-xs">مدیریت موجودی و تراکنش‌های مالی</p>
                </div>
            </div>
        </div>

        <!-- Include Sidebar Component -->

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            @include('front.user.partials.sidebar')
            
            <!-- Main Content -->
            <div class="lg:col-span-3 col-span-4">
                <!-- Wallet Balance -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-semibold text-gray-900">موجودی کیف پول</h2>
                        <div class="text-right">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">موجودی کیف پول</p>
                                <p class="text-lg font-semibold text-gray-900">{{ number_format($wallet->balance ?? 0) }} تومان</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-green-50 rounded-xl p-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center ml-3">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">کل واریز</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ number_format($totalDeposits ?? 0) }} تومان</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-sky-50 rounded-xl p-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-sky-100 rounded-xl flex items-center justify-center ml-3">
                                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">کل تراکنش‌ها</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $totalTransactions ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charge Wallet Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">شارژ کیف پول</h3>
                    
                    @if(session('charge_success'))
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl">
                            <div class="flex">
                                <svg class="w-4 h-4 text-green-400 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-green-800">شارژ با موفقیت انجام شد</h4>
                                    <p class="text-sm text-green-700">{{ session('charge_success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('app.user.wallet.charge') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- Preset Amounts -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">مبلغ شارژ</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                                <button type="button" 
                                        class="preset-amount p-3 border border-gray-300 rounded-xl text-center hover:border-sky-500 hover:bg-sky-50 transition-colors"
                                        data-amount="100000">
                                    <div class="text-base font-semibold text-gray-900">۱۰۰,۰۰۰</div>
                                    <div class="text-xs text-gray-500">تومان</div>
                                </button>
                                <button type="button" 
                                        class="preset-amount p-3 border border-gray-300 rounded-xl text-center hover:border-sky-500 hover:bg-sky-50 transition-colors"
                                        data-amount="200000">
                                    <div class="text-base font-semibold text-gray-900">۲۰۰,۰۰۰</div>
                                    <div class="text-xs text-gray-500">تومان</div>
                                </button>
                                <button type="button" 
                                        class="preset-amount p-3 border border-gray-300 rounded-xl text-center hover:border-sky-500 hover:bg-sky-50 transition-colors"
                                        data-amount="500000">
                                    <div class="text-base font-semibold text-gray-900">۵۰۰,۰۰۰</div>
                                    <div class="text-xs text-gray-500">تومان</div>
                                </button>
                            </div>
                        </div>

                        <!-- Custom Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">مبلغ سفارشی (تومان)</label>
                            <input type="number" 
                                   id="amount" 
                                   name="amount" 
                                   value="100000"
                                   min="10000" 
                                   max="50000000"
                                   step="1000"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-left"
                                   placeholder="مبلغ را وارد کنید">
                            @error('amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Gateway Selection -->
                        <div id="gatewaySection" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-3">انتخاب درگاه پرداخت</label>
                            
                            <!-- Loading State -->
                            <div id="gatewayLoading" class="flex items-center justify-center p-4 border border-gray-200 rounded-xl">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-sky-500"></div>
                                <span class="mr-3 text-gray-600 text-sm">در حال بارگیری درگاه‌های پرداخت...</span>
                            </div>
                            
                            <!-- Gateway Selection -->
                            <div id="gatewaySelection" class="hidden space-y-3">
                                <!-- Gateway options will be populated by JavaScript -->
                            </div>
                            
                            <!-- Error State -->
                            <div id="gatewayError" class="hidden p-3 bg-red-50 border border-red-200 rounded-xl">
                                <div class="flex">
                                    <svg class="w-4 h-4 text-red-400 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-red-800">خطا در بارگیری درگاه‌های پرداخت</h4>
                                        <p class="text-sm text-red-700 mt-1" id="gatewayErrorMessage">لطفاً صفحه را مجدداً بارگیری کنید.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden input for selected gateway -->
                        <input type="hidden" id="gateway_id" name="gateway_id" value="">

                        <!-- Cost Summary -->
                        <div id="costSummary" class="hidden bg-sky-50 rounded-xl p-3" style="display: none !important;">
                            <h4 class="font-medium text-gray-900 mb-3 text-sm">خلاصه پرداخت</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">مبلغ شارژ:</span>
                                    <span class="text-gray-900" id="chargeAmount">۰ تومان</span>
                                </div>
                                <div class="flex justify-between" id="gatewayFeeRow" style="display: none;">
                                    <span class="text-gray-600">کارمزد درگاه:</span>
                                    <span class="text-gray-900" id="gatewayFee">۰ تومان</span>
                                </div>
                                <div class="border-t border-gray-200 pt-2 flex justify-between font-medium">
                                    <span class="text-gray-900">مبلغ نهایی:</span>
                                    <span class="text-sky-600 font-semibold" id="totalAmount">۰ تومان</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" 
                                id="submitButton"
                                disabled
                                class="w-full px-4 py-2 text-sm bg-gray-400 text-white rounded-xl transition-colors disabled:cursor-not-allowed">
                            شارژ کیف پول
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-gray-900">آخرین تراکنش‌ها</h3>
                        <a href="{{ route('app.user.history') }}" 
                           class="text-sky-600 hover:text-sky-700 text-sm font-medium transition-colors px-3 py-1 rounded-lg bg-sky-50 hover:bg-sky-100">
                            مشاهده همه
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse($latestTransactions ?? [] as $transaction)
                            <div class="flex items-center justify-between p-3 bg-sky-50 rounded-xl">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ $transaction->getStatusColor() }}">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($transaction->type === 'deposit')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            @elseif($transaction->type === 'withdraw')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            @endif
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $transaction->getTypeText() }}</p>
                                        <p class="text-xs text-gray-500">{{ \Verta::instance($transaction->created_at)->format('Y/m/d H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold {{ $transaction->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount) }} تومان
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-xs text-gray-500">هنوز تراکنشی انجام نداده‌اید</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const presetButtons = document.querySelectorAll('.preset-amount');
    const amountInput = document.getElementById('amount');
    const gatewayIdInput = document.getElementById('gateway_id');
    const submitButton = document.getElementById('submitButton');
    const chargeForm = document.querySelector('form[action="{{ route('app.user.wallet.charge') }}"]');
    
    // Gateway loading elements
    const gatewayLoading = document.getElementById('gatewayLoading');
    const gatewaySelection = document.getElementById('gatewaySelection');
    const gatewayError = document.getElementById('gatewayError');
    const gatewayErrorMessage = document.getElementById('gatewayErrorMessage');
    
    // Cost summary elements
    const costSummary = document.getElementById('costSummary');
    const chargeAmount = document.getElementById('chargeAmount');
    const gatewayFee = document.getElementById('gatewayFee');
    const gatewayFeeRow = document.getElementById('gatewayFeeRow');
    const totalAmount = document.getElementById('totalAmount');
    
    let availableGateways = [];
    let selectedGateway = null;
    
    // Set default amount (100,000)
    amountInput.value = '100000';
    
    // Handle preset amount clicks
    presetButtons.forEach(button => {
        button.addEventListener('click', function() {
            const amount = this.getAttribute('data-amount');
            
            // Update input value
            amountInput.value = amount;
            
            // Update button styles
            presetButtons.forEach(btn => {
                btn.classList.remove('border-sky-500', 'bg-sky-50');
                btn.classList.add('border-gray-300');
            });
            
            this.classList.remove('border-gray-300');
            this.classList.add('border-sky-500', 'bg-sky-50');
            
            // Reload gateways for new amount
            loadPaymentGateways();
        });
    });
    
    // Handle custom amount input
    amountInput.addEventListener('input', function() {
        // Remove active state from preset buttons when user types custom amount
        presetButtons.forEach(btn => {
            btn.classList.remove('border-sky-500', 'bg-sky-50');
            btn.classList.add('border-gray-300');
        });
        
        // Reload gateways for new amount
        debounce(loadPaymentGateways, 500)();
    });
    
    // Debounce function to avoid too many API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Load payment gateways from API
    async function loadPaymentGateways() {
        const amount = parseInt(amountInput.value) || 100000;
        
        if (amount < 1000) {
            showGatewayError('مبلغ باید حداقل ۱,۰۰۰ تومان باشد.');
            return;
        }
        
        try {
            showLoading();
            
            const response = await fetch(`/payment/gateways?amount=${amount}&currency=IRT`);
            const data = await response.json();
            
            if (data.success && data.gateways && data.gateways.length > 0) {
                availableGateways = data.gateways;
                renderPaymentGateways(data.gateways);
                updateCostSummary();
            } else {
                showGatewayError('هیچ درگاه پرداخت فعالی برای این مبلغ یافت نشد.');
            }
        } catch (error) {
            console.error('خطا در بارگیری درگاه‌های پرداخت:', error);
            showGatewayError('خطا در بارگیری درگاه‌های پرداخت. لطفاً مجدداً تلاش کنید.');
        }
    }
    
    // Show loading state
    function showLoading() {
        gatewayLoading.classList.remove('hidden');
        gatewaySelection.classList.add('hidden');
        gatewayError.classList.add('hidden');
        costSummary.classList.add('hidden');
        updateSubmitButton(false);
    }
    
    // Show gateway error
    function showGatewayError(message) {
        gatewayLoading.classList.add('hidden');
        gatewaySelection.classList.add('hidden');
        gatewayError.classList.remove('hidden');
        costSummary.classList.add('hidden');
        gatewayErrorMessage.textContent = message;
        updateSubmitButton(false);
    }
    
    // Render payment gateways
    function renderPaymentGateways(gateways) {
        // Since gateway selection is hidden, just auto-select the first gateway
        // and update the form state without rendering the UI
        
        // Select first gateway by default
        if (gateways.length > 0) {
            selectedGateway = gateways[0];
            gatewayIdInput.value = gateways[0].id;
            updateCostSummary();
            updateSubmitButton(true);
        }
        
        gatewayLoading.classList.add('hidden');
        // gatewaySelection.classList.remove('hidden'); // Keep hidden since we don't want to show it
        gatewayError.classList.add('hidden');
    }
    
    // Update cost summary
    function updateCostSummary() {
        if (!selectedGateway) return;
        
        const amount = parseInt(amountInput.value) || 0;
        const fee = selectedGateway.fee || 0;
        const total = amount + fee;
        
        chargeAmount.textContent = formatAmount(amount) + ' تومان';
        gatewayFee.textContent = formatAmount(fee) + ' تومان';
        totalAmount.textContent = formatAmount(total) + ' تومان';
        
        // Show/hide fee row
        if (fee > 0) {
            gatewayFeeRow.style.display = 'flex';
        } else {
            gatewayFeeRow.style.display = 'none';
        }
        
        // Keep cost summary hidden since we don't want to show it
        // costSummary.classList.remove('hidden');
    }
    
    // Update submit button state
    function updateSubmitButton(enabled) {
        if (enabled && selectedGateway && parseInt(amountInput.value) >= 1000) {
            submitButton.disabled = false;
            submitButton.classList.remove('bg-gray-400');
            submitButton.classList.add('bg-sky-600', 'hover:bg-sky-700');
        } else {
            submitButton.disabled = true;
            submitButton.classList.remove('bg-sky-600', 'hover:bg-sky-700');
            submitButton.classList.add('bg-gray-400');
        }
    }
    
    // Format amount with Persian digits and thousand separators
    function formatAmount(amount) {
        return amount.toLocaleString('fa-IR');
    }
    
    // Form validation before submit
    if (chargeForm) {
        chargeForm.addEventListener('submit', function(e) {
            const gatewayId = gatewayIdInput.value;
            const amount = parseInt(amountInput.value);
            
            if (!gatewayId) {
                e.preventDefault();
                alert('لطفاً درگاه پرداخت را انتخاب کنید.');
                return false;
            }
            
            if (!amount || amount < 1000) {
                e.preventDefault();
                alert('مبلغ باید حداقل ۱,۰۰۰ تومان باشد.');
                return false;
            }
            
            if (amount > 50000000) {
                e.preventDefault();
                alert('مبلغ نمی‌تواند بیشتر از ۵۰,۰۰۰,۰۰۰ تومان باشد.');
                return false;
            }
        });
    }
    
    // Initial load
    loadPaymentGateways();
});
</script>
@endsection