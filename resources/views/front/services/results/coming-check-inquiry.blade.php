@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">چک‌های صادرشده</h1>
                <p class="text-gray-600">کد ملی {{ $data['data']['national_code'] ?? 'نامشخص' }}</p>
            </div>
            <div class="text-right">
                @php
                    $totalChecks = count($data['data']['issued_checks'] ?? []);
                    $overdueFount = collect($data['data']['issued_checks'] ?? [])->where('is_overdue', true)->count();
                    $statusClass = $overdueFount > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                    $statusText = $overdueFount > 0 ? "هشدار: {$overdueFount} چک معوق" : 'وضعیت مناسب';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                        @if($overdueFount > 0)
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        @else
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        @endif
                    </svg>
                    {{ $statusText }}
                </span>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    @if(!empty($data['data']['alerts']))
        <div class="mb-6 space-y-3">
            @foreach($data['data']['alerts'] as $alert)
                <div class="p-4 rounded-lg border 
                    @if($alert['type'] === 'danger') bg-red-50 border-red-200 text-red-800
                    @elseif($alert['type'] === 'warning') bg-yellow-50 border-yellow-200 text-yellow-800
                    @elseif($alert['type'] === 'info') bg-sky-50 border-sky-200 text-sky-800
                    @else bg-sky-50 border-gray-200 text-gray-800 @endif">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            @if($alert['type'] === 'danger')
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            @else
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            @endif
                        </svg>
                        <span class="font-semibold">{{ $alert['message'] ?? 'هشدار' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Summary Statistics -->
            @if(!empty($data['data']['summary']))
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    @php $summary = $data['data']['summary']; @endphp
                    
                    <!-- Total Checks -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                        <div class="text-3xl font-bold text-sky-600 mb-2">{{ $summary['total_checks'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">کل چک‌ها</div>
                        <div class="mt-2">
                            <svg class="w-8 h-8 mx-auto text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Pending Checks -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                        <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $summary['pending_checks'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">در انتظار</div>
                        <div class="mt-2">
                            <svg class="w-8 h-8 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Paid Checks -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                        <div class="text-3xl font-bold text-green-600 mb-2">{{ $summary['paid_checks'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">پرداخت شده</div>
                        <div class="mt-2">
                            <svg class="w-8 h-8 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Total Amount -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                        <div class="text-2xl font-bold text-purple-600 mb-2">{{ number_format(($summary['total_amount'] ?? 0) / 10) }}</div>
                        <div class="text-xs text-gray-500 mb-1">تومان</div>
                        <div class="text-sm text-gray-600">مجموع مبلغ</div>
                        <div class="mt-2">
                            <svg class="w-8 h-8 mx-auto text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Account Information -->
            @if(!empty($data['data']['account_info']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        اطلاعات حساب
                    </h2>
                    
                    @php $accountInfo = $data['data']['account_info']; @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if(!empty($accountInfo['account_number']))
                            <div class="bg-sky-50 border border-sky-200 rounded-lg p-4">
                                <div class="text-sky-700 text-sm mb-1">شماره حساب</div>
                                <div class="font-bold text-sky-900 font-mono cursor-pointer" 
                                     onclick="copyToClipboard('{{ $accountInfo['account_number'] }}')"
                                     title="کلیک کنید تا کپی شود">
                                    {{ $accountInfo['account_number'] }}
                                </div>
                            </div>
                        @endif
                        
                        @if(!empty($accountInfo['bank_name']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="text-gray-600 text-sm mb-1">نام بانک</div>
                                <div class="font-semibold text-gray-900">{{ $accountInfo['bank_name'] }}</div>
                            </div>
                        @endif
                        
                        @if(!empty($accountInfo['branch_name']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="text-gray-600 text-sm mb-1">نام شعبه</div>
                                <div class="font-semibold text-gray-900">{{ $accountInfo['branch_name'] }}</div>
                            </div>
                        @endif
                        
                        @if(!empty($accountInfo['account_holder']))
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="text-green-700 text-sm mb-1">صاحب حساب</div>
                                <div class="font-bold text-green-900">{{ $accountInfo['account_holder'] }}</div>
                            </div>
                        @endif
                        
                        @if(!empty($accountInfo['current_balance']))
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                <div class="text-purple-700 text-sm mb-1">موجودی فعلی</div>
                                <div class="font-bold text-purple-900">{{ number_format(($accountInfo['current_balance'] ?? 0) / 10) }} تومان</div>
                            </div>
                        @endif
                        
                        @if(!empty($accountInfo['check_book_status']))
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="text-yellow-700 text-sm mb-1">وضعیت دسته چک</div>
                                <div class="font-semibold text-yellow-900">{{ $accountInfo['check_book_status'] }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Issued Checks List -->
            @if(!empty($data['data']['issued_checks']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2H9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l2 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2V7l2-2h6z"></path>
                            </svg>
                            لیست چک‌های صادرشده
                        </h2>
                        
                        <!-- Filter Options -->
                        <div class="flex items-center gap-2">
                            <select id="statusFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-1" onchange="filterChecks()">
                                <option value="all">همه وضعیت‌ها</option>
                                <option value="PENDING">در انتظار</option>
                                <option value="PAID">پرداخت شده</option>
                                <option value="BOUNCED">برگشتی</option>
                                <option value="CANCELLED">لغو شده</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-4" id="checksContainer">
                        @foreach($data['data']['issued_checks'] as $check)
                            <div class="check-item border border-gray-200 rounded-lg p-5 hover:shadow-sm transition-shadow" 
                                 data-status="{{ $check['status'] ?? '' }}">
                                
                                <!-- Check Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 mb-1">
                                            چک شماره {{ $check['check_number'] ?? 'نامشخص' }}
                                            @if($check['is_overdue'] ?? false)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                                                    معوق
                                                </span>
                                            @endif
                                        </h3>
                                        @if(!empty($check['payee_name']))
                                            <div class="text-sm text-gray-600">دریافت‌کننده: {{ $check['payee_name'] }}</div>
                                        @endif
                                        @if(!empty($check['issue_date']))
                                            <div class="text-xs text-gray-500 mt-1">تاریخ صدور: {{ $check['issue_date'] }}</div>
                                        @endif
                                    </div>
                                    <div class="text-left">
                                        <div class="text-xl font-bold text-gray-900 mb-2">{{ number_format(($check['amount'] ?? 0) / 10) }} تومان</div>
                                        @php
                                            $statusColors = [
                                                'PENDING' => 'bg-yellow-100 text-yellow-800',
                                                'PAID' => 'bg-green-100 text-green-800',
                                                'BOUNCED' => 'bg-red-100 text-red-800',
                                                'CANCELLED' => 'bg-sky-100 text-gray-800',
                                                'STOPPED' => 'bg-orange-100 text-orange-800',
                                                'EXPIRED' => 'bg-purple-100 text-purple-800',
                                            ];
                                            $statusClass = $statusColors[$check['status'] ?? 'UNKNOWN'] ?? 'bg-sky-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                                            {{ $check['status_persian'] ?? 'نامشخص' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Check Details -->
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                    @if(!empty($check['due_date']))
                                        <div class="bg-sky-50 rounded p-3">
                                            <div class="text-gray-600 mb-1 flex items-center">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                تاریخ سررسید
                                            </div>
                                            <div class="font-medium text-gray-900 
                                                @if($check['is_overdue'] ?? false) text-red-600 font-bold @endif">
                                                {{ $check['due_date'] }}
                                            </div>
                                            @if(!empty($check['days_to_due']))
                                                <div class="text-xs text-gray-500 mt-1">
                                                    @if($check['days_to_due'] < 0)
                                                        {{ abs($check['days_to_due']) }} روز گذشته
                                                    @else
                                                        {{ $check['days_to_due'] }} روز مانده
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if(!empty($check['bank_name']))
                                        <div class="bg-sky-50 rounded p-3">
                                            <div class="text-gray-600 mb-1 flex items-center">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                بانک
                                            </div>
                                            <div class="font-medium text-gray-900">{{ $check['bank_name'] }}</div>
                                            @if(!empty($check['branch_name']))
                                                <div class="text-xs text-gray-500 mt-1">{{ $check['branch_name'] }}</div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if(!empty($check['payment_date']))
                                        <div class="bg-green-50 border border-green-200 rounded p-3">
                                            <div class="text-green-700 mb-1 flex items-center">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                تاریخ پرداخت
                                            </div>
                                            <div class="font-medium text-green-900">{{ $check['payment_date'] }}</div>
                                        </div>
                                    @endif
                                    
                                    @if($check['can_stop_payment'] ?? false)
                                        <div class="bg-orange-50 border border-orange-200 rounded p-3">
                                            <div class="text-orange-700 mb-1 flex items-center">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                عملیات
                                            </div>
                                            <button onclick="stopPayment('{{ $check['check_number'] }}')" 
                                                    class="text-orange-800 hover:text-orange-900 font-medium text-xs">
                                                توقف پرداخت
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Bounce Information -->
                                @if(!empty($check['bounce_reason']))
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <div class="bg-red-50 border border-red-200 rounded p-3">
                                            <div class="text-red-700 text-sm font-medium mb-1">علت برگشت</div>
                                            <div class="text-red-900 text-sm">{{ $check['bounce_reason'] }}</div>
                                            @if(!empty($check['bounce_code']))
                                                <div class="text-red-600 text-xs mt-1">کد: {{ $check['bounce_code'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">عملیات</h3>
                <div class="space-y-3">
                    <button onclick="window.print()" class="w-full bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        چاپ گزارش
                    </button>
                    <button onclick="copyAllChecks()" class="w-full bg-sky-100 hover:bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        کپی لیست
                    </button>
                    <button onclick="exportToExcel()" class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        دانلود Excel
                    </button>
                </div>
            </div>

            <!-- Recommendations -->
            @if(!empty($data['data']['recommendations']))
                <div class="bg-sky-50 border border-sky-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-sky-800 mb-3">توصیه‌ها</h3>
                    <ul class="text-sm text-sky-700 space-y-2">
                        @foreach($data['data']['recommendations'] as $recommendation)
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-sky-600 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $recommendation }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Summary -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">خلاصه</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاریخ استعلام:</span>
                        <span class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">زمان استعلام:</span>
                        <span class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">کل چک‌ها:</span>
                        <span class="font-medium">{{ $totalChecks }}</span>
                    </div>
                    @if($overdueFount > 0)
                        <div class="flex justify-between">
                            <span class="text-red-600">چک‌های معوق:</span>
                            <span class="font-bold text-red-600">{{ $overdueFount }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">نکات مهم</h3>
                <ul class="text-sm text-yellow-700 space-y-2">
                    <li>• چک‌های معوق را سریع پیگیری کنید</li>
                    <li>• در صورت لزوم توقف پرداخت کنید</li>
                    <li>• موجودی حساب را کافی نگه دارید</li>
                    <li>• اطلاعات را به‌روز نگه دارید</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
const checksData = @json($data['data'] ?? []);

function filterChecks() {
    const statusFilter = document.getElementById('statusFilter').value;
    const checkItems = document.querySelectorAll('.check-item');

    checkItems.forEach(item => {
        const status = item.getAttribute('data-status');
        
        if (statusFilter === 'all' || status === statusFilter) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('کپی شد!', 'success');
    }).catch(() => {
        showToast('خطا در کپی کردن', 'error');
    });
}

function copyAllChecks() {
    let text = 'چک‌های صادرشده\n';
    text += '='.repeat(40) + '\n\n';
    text += `کد ملی: ${checksData.national_code || 'نامشخص'}\n`;
    text += `تاریخ گزارش: {{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d H:i') }}\n\n`;
    
    // Account Info
    if (checksData.account_info) {
        text += 'اطلاعات حساب:\n';
        if (checksData.account_info.account_number) text += `شماره حساب: ${checksData.account_info.account_number}\n`;
        if (checksData.account_info.bank_name) text += `بانک: ${checksData.account_info.bank_name}\n`;
        if (checksData.account_info.account_holder) text += `صاحب حساب: ${checksData.account_info.account_holder}\n`;
        text += '\n';
    }
    
    // Summary
    if (checksData.summary) {
        text += 'خلاصه:\n';
        text += `کل چک‌ها: ${checksData.summary.total_checks || 0}\n`;
        text += `در انتظار: ${checksData.summary.pending_checks || 0}\n`;
        text += `پرداخت شده: ${checksData.summary.paid_checks || 0}\n`;
        text += `مجموع مبلغ: ${checksData.summary.total_amount ? (checksData.summary.total_amount / 10).toLocaleString() : '0'} تومان\n\n`;
    }
    
    // Checks List
    if (checksData.issued_checks && checksData.issued_checks.length > 0) {
        text += 'لیست چک‌ها:\n';
        checksData.issued_checks.forEach((check, index) => {
            text += `${index + 1}. چک ${check.check_number || 'نامشخص'}\n`;
            text += `   مبلغ: ${check.amount ? (check.amount / 10).toLocaleString() : '0'} تومان\n`;
            text += `   وضعیت: ${check.status_persian || 'نامشخص'}\n`;
            if (check.due_date) text += `   سررسید: ${check.due_date}\n`;
            if (check.payee_name) text += `   گیرنده: ${check.payee_name}\n`;
            text += '\n';
        });
    }
    
    text += 'تولید شده در پیشخوانک (pishkhanak.com)';
    
    copyToClipboard(text);
}

function stopPayment(checkNumber) {
    if (confirm(`آیا می‌خواهید توقف پرداخت چک شماره ${checkNumber} را درخواست کنید؟`)) {
        showToast('درخواست توقف پرداخت ارسال شد', 'info');
        // Stop payment functionality would be implemented here
    }
}

function exportToExcel() {
    // Excel export functionality can be implemented here
    showToast('قابلیت دانلود Excel به زودی اضافه خواهد شد', 'info');
}

function showToast(message, type) {
    const toast = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-sky-500'
    };
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 ${colors[type] || 'bg-sky-500'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .bg-sky-50 { background: #f9fafb !important; }
    .bg-sky-50 { background: #eff6ff !important; }
    .bg-green-50 { background: #f0fdf4 !important; }
    .bg-red-50 { background: #fef2f2 !important; }
    .bg-yellow-50 { background: #fefce8 !important; }
    .border { border: 1px solid #e5e7eb !important; }
    
    .check-item {
        break-inside: avoid;
        margin-bottom: 1rem;
    }
}
</style>
@endsection 