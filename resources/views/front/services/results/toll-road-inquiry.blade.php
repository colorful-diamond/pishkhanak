@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">گزارش استفاده از جاده‌های آزادراه</h1>
                <p class="text-gray-600">
                    پلاک {{ $data['data']['formatted_plate'] ?? $data['data']['plate_number'] ?? 'نامشخص' }} |
                    از {{ $data['data']['period']['from_date'] ?? '' }} تا {{ $data['data']['period']['to_date'] ?? '' }}
                </p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    استعلام موفق
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Passages -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-3xl font-bold text-sky-600 mb-2">{{ $data['data']['summary']['total_passages'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">کل تردد</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Total Amount -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-2xl font-bold text-green-600 mb-2">{{ $data['data']['summary']['total_amount_formatted'] ?? number_format(intval(($data['data']['summary']['total_amount'] ?? 0) / 10)) . ' تومان' }}</div>
                    <div class="text-sm text-gray-600">مجموع عوارض</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>

                <!-- Unique Roads -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2">{{ $data['data']['summary']['unique_roads'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">جاده‌های مختلف</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Payment Status -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    @php
                        $unpaidCount = $data['data']['summary']['unpaid_count'] ?? 0;
                        $statusColor = $unpaidCount > 0 ? 'text-red-600' : 'text-green-600';
                        $statusText = $unpaidCount > 0 ? "{$unpaidCount} معوق" : 'پرداخت شده';
                    @endphp
                    <div class="text-3xl font-bold {{ $statusColor }} mb-2">{{ $data['data']['summary']['paid_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">پرداخت موفق</div>
                    @if($unpaidCount > 0)
                        <div class="text-xs {{ $statusColor }} mt-1 font-medium">{{ $statusText }}</div>
                    @endif
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto {{ $unpaidCount > 0 ? 'text-red-500' : 'text-green-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($unpaidCount > 0)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @endif
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Most Used Roads -->
            @if(!empty($data['data']['roads_summary']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        جاده‌های پرتردد
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach(array_slice($data['data']['roads_summary'], 0, 6) as $road)
                            <div class="bg-sky-50 rounded-lg p-5 border hover:shadow-sm transition-shadow">
                                <h3 class="font-semibold text-gray-900 mb-3">{{ $road['road_name'] ?? 'نامشخص' }}</h3>
                                
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">تعداد تردد:</span>
                                        <span class="font-medium text-sky-600">{{ $road['total_passages'] ?? 0 }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">مجموع عوارض:</span>
                                        <span class="font-medium text-green-600">{{ number_format(($road['total_amount'] ?? 0) / 10) }} تومان</span>
                                    </div>
                                    @if(!empty($road['last_usage']))
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">آخرین استفاده:</span>
                                            <span class="font-medium text-gray-900">{{ $road['last_usage'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Toll Records -->
            @if(!empty($data['data']['toll_records']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            جزئیات تردد
                        </h2>
                        
                        <!-- Filter Options -->
                        <div class="flex items-center gap-2">
                            <select id="statusFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-1" onchange="filterRecords()">
                                <option value="all">همه وضعیت‌ها</option>
                                <option value="PAID">پرداخت شده</option>
                                <option value="UNPAID">پرداخت نشده</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-4" id="recordsContainer">
                        @foreach($data['data']['toll_records'] as $record)
                            <div class="toll-record border border-gray-200 rounded-lg p-5 hover:shadow-sm transition-shadow" 
                                 data-status="{{ $record['status'] ?? '' }}">
                                
                                <!-- Record Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 mb-1">{{ $record['road_name'] ?? 'جاده نامشخص' }}</h3>
                                        <div class="text-sm text-gray-600">
                                            {{ $record['entrance_station'] ?? 'ورودی نامشخص' }} ← {{ $record['exit_station'] ?? 'خروجی نامشخص' }}
                                        </div>
                                        @if(!empty($record['distance']))
                                            <div class="text-xs text-gray-500 mt-1">مسافت: {{ number_format($record['distance']) }} کیلومتر</div>
                                        @endif
                                    </div>
                                    <div class="text-left">
                                        <div class="text-lg font-bold text-gray-900 mb-1">{{ number_format(($record['amount'] ?? 0) / 10) }} تومان</div>
                                        @php
                                            $statusColors = [
                                                'PAID' => 'bg-green-100 text-green-800',
                                                'UNPAID' => 'bg-red-100 text-red-800',
                                                'PENDING' => 'bg-yellow-100 text-yellow-800',
                                                'CANCELLED' => 'bg-sky-100 text-gray-800',
                                            ];
                                            $statusClass = $statusColors[$record['status'] ?? 'UNKNOWN'] ?? 'bg-sky-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $record['status_persian'] ?? 'نامشخص' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Record Details -->
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                    @if(!empty($record['date']) || !empty($record['time']))
                                        <div class="bg-sky-50 rounded p-3">
                                            <div class="text-gray-600 mb-1 flex items-center">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                تاریخ و زمان
                                            </div>
                                            <div class="font-medium text-gray-900">{{ $record['date'] ?? '' }}</div>
                                            @if(!empty($record['time']))
                                                <div class="text-xs text-gray-600 mt-1">
                                                    {{ $record['time'] }}
                                                    @if($record['is_rush_hour'] ?? false)
                                                        <span class="text-orange-600">(ساعت شلوغی)</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if(!empty($record['vehicle_type']))
                                        <div class="bg-sky-50 rounded p-3">
                                            <div class="text-gray-600 mb-1 flex items-center">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                نوع وسیله
                                            </div>
                                            <div class="font-medium text-gray-900">{{ $record['vehicle_type'] }}</div>
                                        </div>
                                    @endif
                                    
                                    @if(!empty($record['payment_date']))
                                        <div class="bg-green-50 border border-green-200 rounded p-3">
                                            <div class="text-green-700 mb-1 flex items-center">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                تاریخ پرداخت
                                            </div>
                                            <div class="font-medium text-green-900">{{ $record['payment_date'] }}</div>
                                        </div>
                                    @endif
                                    
                                    @if(!empty($record['reference_number']))
                                        <div class="bg-sky-50 border border-sky-200 rounded p-3">
                                            <div class="text-sky-700 mb-1 flex items-center">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                                </svg>
                                                شماره مرجع
                                            </div>
                                            <div class="font-medium text-sky-900 font-mono cursor-pointer" 
                                                 onclick="copyToClipboard('{{ $record['reference_number'] }}')"
                                                 title="کلیک کنید تا کپی شود">
                                                {{ $record['reference_number'] }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Unpaid Payment Section -->
            @if(($data['data']['payment_info']['total_unpaid'] ?? 0) > 0)
                <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                    <h2 class="text-xl font-semibold text-red-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        عوارض پرداخت نشده
                    </h2>
                    
                    <div class="mb-4">
                        <div class="text-3xl font-bold text-red-700 mb-2">{{ number_format(($data['data']['payment_info']['total_unpaid'] ?? 0) / 10) }} تومان</div>
                        <div class="text-red-600">مجموع عوارض معوق</div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button onclick="payAllUnpaid()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            پرداخت همه عوارض
                        </button>
                        <button onclick="viewPaymentMethods()" class="bg-white border border-red-300 text-red-700 hover:bg-red-50 px-6 py-2 rounded-lg font-medium transition-colors">
                            روش‌های پرداخت
                        </button>
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
                    <button onclick="copyAllRecords()" class="w-full bg-sky-100 hover:bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        کپی گزارش
                    </button>
                    <button onclick="exportToExcel()" class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        دانلود Excel
                    </button>
                </div>
            </div>

            <!-- Usage Statistics -->
            @if(!empty($data['data']['statistics']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">آمار استفاده</h3>
                    <div class="space-y-3 text-sm">
                        @if(!empty($data['data']['statistics']['daily_average']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">میانگین روزانه:</span>
                                <span class="font-medium">{{ $data['data']['statistics']['daily_average'] }} بار</span>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['statistics']['most_used_time']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">ساعت پرتردد:</span>
                                <span class="font-medium">{{ $data['data']['statistics']['most_used_time'] }}</span>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['statistics']['formatted_average_amount']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">میانگین عوارض:</span>
                                <span class="font-medium">{{ number_format(($data['data']['statistics']['average_amount'] ?? 0) / 10) }} تومان</span>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['statistics']['total_distance']))
                            <div class="flex justify-between">
                                <span class="text-gray-600">کل مسافت:</span>
                                <span class="font-medium">{{ number_format($data['data']['statistics']['total_distance']) }} کیلومتر</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Plate Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">پلاک مورد نظر</h3>
                <div class="bg-white border-4 border-gray-800 rounded-lg p-3 shadow-sm mb-4" style="font-family: 'Courier New', monospace;">
                    <!-- Iran Flag Colors -->
                    <div class="flex justify-center mb-1">
                        <div class="w-2 h-1 bg-green-500"></div>
                        <div class="w-2 h-1 bg-white border-t border-b border-gray-300"></div>
                        <div class="w-2 h-1 bg-red-500"></div>
                    </div>
                    
                    <!-- Plate Number -->
                    <div class="text-lg font-bold text-gray-900 mb-1">
                        {{ $data['data']['formatted_plate'] ?? $data['data']['plate_number'] ?? 'نامشخص' }}
                    </div>
                    
                    <!-- Iran Text -->
                    <div class="text-xs text-gray-600">ایران</div>
                </div>
                
                <div class="text-sm text-gray-600">
                    {{ $data['data']['summary']['total_passages'] ?? 0 }} تردد در بازه زمانی
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-sky-50 border border-sky-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-sky-800 mb-3">نکات مهم</h3>
                <ul class="text-sm text-sky-700 space-y-2">
                    <li>• عوارض معوق را در اسرع وقت پرداخت کنید</li>
                    <li>• در ساعات شلوغی عوارض بیشتر است</li>
                    <li>• رسید پرداخت را نگهداری کنید</li>
                    <li>• برای سفرهای مکرر کارت عوارض تهیه کنید</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
const tollData = @json($data['data'] ?? []);

function filterRecords() {
    const statusFilter = document.getElementById('statusFilter').value;
    const tollRecords = document.querySelectorAll('.toll-record');

    tollRecords.forEach(record => {
        const status = record.getAttribute('data-status');
        
        if (statusFilter === 'all' || status === statusFilter) {
            record.style.display = 'block';
        } else {
            record.style.display = 'none';
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

function copyAllRecords() {
    let text = 'گزارش آزادراه\n';
    text += '='.repeat(40) + '\n\n';
    text += `پلاک: ${tollData.plate_number || 'نامشخص'}\n`;
    text += `تاریخ گزارش: {{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d H:i') }}\n`;
    text += `بازه زمانی: ${tollData.period?.from_date || ''} تا ${tollData.period?.to_date || ''}\n\n`;
    
    text += `خلاصه:\n`;
    text += `کل تردد: ${tollData.summary?.total_passages || 0}\n`;
    text += `مجموع عوارض: ${tollData.summary ? (tollData.summary.total_amount / 10).toLocaleString() : '0'} تومان\n`;
    text += `جاده‌های مختلف: ${tollData.summary?.unique_roads || 0}\n\n`;
    
    if (tollData.toll_records && tollData.toll_records.length > 0) {
        text += 'جزئیات تردد:\n';
        tollData.toll_records.forEach((record, index) => {
            text += `${index + 1}. ${record.road_name || 'نامشخص'}\n`;
            text += `   تاریخ: ${record.date || ''} ${record.time || ''}\n`;
            text += `   مسیر: ${record.entrance_station || ''} ← ${record.exit_station || ''}\n`;
            text += `   مبلغ: ${record.amount ? (record.amount / 10).toLocaleString() : '0'} تومان\n`;
            text += `   وضعیت: ${record.status_persian || ''}\n\n`;
        });
    }
    
    text += '\nتولید شده در پیشخوانک (pishkhanak.com)';
    
    copyToClipboard(text);
}

function payAllUnpaid() {
    const unpaidAmount = {{ ($data['data']['payment_info']['total_unpaid'] ?? 0) / 10 }};
    if (unpaidAmount > 0) {
        showToast(`در حال هدایت به درگاه پرداخت ${unpaidAmount.toLocaleString()} تومان...`, 'info');
        // Payment gateway integration would go here
    }
}

function viewPaymentMethods() {
    const methods = @json($data['data']['payment_info']['payment_methods'] ?? []);
    alert('روش‌های پرداخت موجود:\n• ' + methods.join('\n• '));
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
    .border { border: 1px solid #e5e7eb !important; }
    
    .toll-record {
        break-inside: avoid;
        margin-bottom: 1rem;
    }
}
</style>
@endsection 