@extends('front.layouts.app')

@section('content')
<div class="">
    <div class="container mx-auto px-4 py-3">
        <!-- Header - more compact -->
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-gray-900 mb-1">سوابق تراکنش</h1>
                    <p class="text-gray-600 text-xs">مشاهده تاریخچه کامل تراکنش‌های مالی</p>
                </div>
            </div>
        </div>

        <!-- Include Sidebar Component -->

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            @include('front.user.partials.sidebar')
            
            <!-- Main Content -->
            <div class="lg:col-span-3 col-span-4">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">موجودی فعلی</p>
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($wallet->balance ?? 0) }} تومان</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-sky-100 rounded-xl flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">کل تراکنش‌ها</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $transactions->total() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-100 rounded-xl flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">تراکنش‌های موفق</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $successfulTransactions ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">تراکنش‌های ناموفق</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $failedTransactions ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
                    <h3 class="text-base font-medium text-gray-900 mb-4">فیلترهای جستجو</h3>
                    <form method="GET" class="space-y-4" id="historyFiltersForm">
                        <!-- Mobile-friendly filters layout -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">نوع تراکنش</label>
                                <select id="type" name="type" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                    <option value="">همه</option>
                                    <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>واریز</option>
                                    <option value="withdraw" {{ request('type') === 'withdraw' ? 'selected' : '' }}>برداشت</option>
                                    <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>پرداخت</option>
                                    <option value="refund" {{ request('type') === 'refund' ? 'selected' : '' }}>بازگشت</option>
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">وضعیت</label>
                                <select id="status" name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                    <option value="">همه</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>در انتظار</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>موفق</option>
                                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>ناموفق</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                                </select>
                            </div>

                            <div>
                                <label for="per_page" class="block text-sm font-medium text-gray-700 mb-2">تعداد در صفحه</label>
                                <select id="per_page" name="per_page" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                        </div>

                        <!-- Date filters in separate row for mobile -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">از تاریخ</label>
                                <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}" placeholder="تاریخ شروع (مثال: 1403-03-15)"
                                       class="persian-date-picker w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            </div>

                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">تا تاریخ</label>
                                <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}" placeholder="تاریخ پایان (مثال: 1403-03-20)"
                                       class="persian-date-picker w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="flex flex-col md:flex-row gap-3 justify-end">
                            <button type="submit" class="w-full md:w-auto px-4 py-2 text-sm bg-sky-600 text-white rounded-xl hover:bg-sky-700 transition-colors">
                                اعمال فیلتر
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
                                </svg>
                            </button>
                            <a href="{{ route('app.user.history') }}" class="w-full md:w-auto px-4 py-2 text-sm bg-sky-300 text-gray-700 rounded-xl hover:bg-gray-400 transition-colors text-center">
                                پاک کردن
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Transactions List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-base font-medium text-gray-900">تراکنش‌های شما ({{ $transactions->total() }})</h2>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                            <div class="p-4 hover:bg-sky-50 transition-colors">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
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
                                            <h3 class="text-sm font-medium text-gray-900 mb-1">{{ $transaction->getTypeText() }}</h3>
                                            <div class="flex flex-col md:flex-row md:items-center md:space-x-3 md:space-x-reverse text-xs text-gray-500 space-y-1 md:space-y-0">
                                                <span>شماره: {{ $transaction->transaction_id }}</span>
                                                <span>تاریخ: {{ \Verta::instance($transaction->created_at)->format('Y/m/d H:i') }}</span>
                                                @if($transaction->gateway)
                                                    <span>درگاه: {{ $transaction->gateway->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-row md:flex-col md:text-right items-center md:items-end space-x-3 md:space-x-0 space-x-reverse">
                                        <div class="text-left md:text-right">
                                            <p class="text-sm font-semibold {{ $transaction->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount) }} تومان
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $transaction->getStatusBadgeClass() }}">
                                            {{ $transaction->getStatusText() }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if($transaction->description)
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <p class="text-xs text-gray-600">{{ $transaction->description }}</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900 mb-2">تراکنشی یافت نشد</h3>
                                <p class="text-xs text-gray-500 mb-4">هنوز تراکنشی انجام نداده‌اید یا تراکنش‌های شما با فیلترهای انتخاب شده مطابقت ندارد.</p>
                                <a href="{{ route('app.user.wallet') }}" 
                                   class="inline-flex items-center px-4 py-2 text-sm bg-sky-600 text-white rounded-xl hover:bg-sky-700 transition-colors">
                                    شارژ کیف پول
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </a>
                            </div>
                        @endforelse
                    </div>

                    @if($transactions->hasPages())
                        <div class="px-4 py-3 border-t border-gray-200">
                            {{ $transactions->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when per_page changes
    const perPageSelect = document.getElementById('per_page');
    const filtersForm = document.getElementById('historyFiltersForm');
    
    if (perPageSelect && filtersForm) {
        perPageSelect.addEventListener('change', function() {
            filtersForm.submit();
        });
    }

    // Persian Date Picker Implementation
    function initPersianDatePicker() {
        const persianDateInputs = document.querySelectorAll('.persian-date-picker');
        
        persianDateInputs.forEach(function(input) {
            // Create a simple Persian date picker
            input.addEventListener('click', function() {
                showPersianDatePicker(this);
            });
            
            // Format validation on blur
            input.addEventListener('blur', function() {
                const value = this.value.trim();
                if (value && !isValidPersianDate(value)) {
                    alert('لطفاً تاریخ را به صورت صحیح وارد کنید (مثال: 1403-03-15)');
                    this.focus();
                }
            });
        });
    }

    function isValidPersianDate(dateStr) {
        // Basic validation for YYYY-MM-DD format
        const regex = /^\d{4}-\d{2}-\d{2}$/;
        if (!regex.test(dateStr)) return false;
        
        const parts = dateStr.split('-');
        const year = parseInt(parts[0]);
        const month = parseInt(parts[1]);
        const day = parseInt(parts[2]);
        
        // Basic Persian calendar validation
        return year >= 1300 && year <= 1500 && 
               month >= 1 && month <= 12 && 
               day >= 1 && day <= 31;
    }

    function showPersianDatePicker(input) {
        // Create a simple modal for date selection
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
                <h3 class="text-lg font-medium mb-4">انتخاب تاریخ</h3>
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">سال</label>
                        <select id="year-select" class="w-full px-2 py-1 border rounded text-sm">
                            ${generateYearOptions()}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">ماه</label>
                        <select id="month-select" class="w-full px-2 py-1 border rounded text-sm">
                            ${generateMonthOptions()}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">روز</label>
                        <select id="day-select" class="w-full px-2 py-1 border rounded text-sm">
                            ${generateDayOptions()}
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 justify-end">
                    <button id="cancel-date" class="px-4 py-2 bg-sky-300 text-gray-700 rounded text-sm hover:bg-gray-400">لغو</button>
                    <button id="confirm-date" class="px-4 py-2 bg-sky-600 text-white rounded text-sm hover:bg-sky-700">تایید</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Set current values if input has a date
        const currentValue = input.value;
        if (currentValue && isValidPersianDate(currentValue)) {
            const parts = currentValue.split('-');
            modal.querySelector('#year-select').value = parts[0];
            modal.querySelector('#month-select').value = parts[1];
            modal.querySelector('#day-select').value = parts[2];
        }

        // Event handlers
        modal.querySelector('#cancel-date').addEventListener('click', function() {
            document.body.removeChild(modal);
        });

        modal.querySelector('#confirm-date').addEventListener('click', function() {
            const year = modal.querySelector('#year-select').value;
            const month = modal.querySelector('#month-select').value.padStart(2, '0');
            const day = modal.querySelector('#day-select').value.padStart(2, '0');
            
            if (year && month && day) {
                input.value = `${year}-${month}-${day}`;
            }
            document.body.removeChild(modal);
        });

        // Close on background click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    }

    function generateYearOptions() {
        const currentYear = 1403; // Current Persian year (adjust as needed)
        let options = '<option value="">سال</option>';
        for (let year = currentYear - 5; year <= currentYear + 1; year++) {
            options += `<option value="${year}">${year}</option>`;
        }
        return options;
    }

    function generateMonthOptions() {
        const months = [
            { value: '01', name: 'فروردین' },
            { value: '02', name: 'اردیبهشت' },
            { value: '03', name: 'خرداد' },
            { value: '04', name: 'تیر' },
            { value: '05', name: 'مرداد' },
            { value: '06', name: 'شهریور' },
            { value: '07', name: 'مهر' },
            { value: '08', name: 'آبان' },
            { value: '09', name: 'آذر' },
            { value: '10', name: 'دی' },
            { value: '11', name: 'بهمن' },
            { value: '12', name: 'اسفند' }
        ];
        
        let options = '<option value="">ماه</option>';
        months.forEach(month => {
            options += `<option value="${month.value}">${month.name}</option>`;
        });
        return options;
    }

    function generateDayOptions() {
        let options = '<option value="">روز</option>';
        for (let day = 1; day <= 31; day++) {
            const dayStr = day.toString().padStart(2, '0');
            options += `<option value="${dayStr}">${day}</option>`;
        }
        return options;
    }

    // Initialize Persian date picker
    initPersianDatePicker();
});
</script>
@endpush

@endsection