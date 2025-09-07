@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">تاریخچه مالکیت پلاک</h1>
                <p class="text-gray-600">پلاک {{ $data['data']['formatted_plate'] ?? $data['data']['plate_number'] ?? 'نامشخص' }}</p>
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

    <!-- Alerts Section -->
    @if(!empty($data['data']['alerts']))
        <div class="mb-6 space-y-3">
            @foreach($data['data']['alerts'] as $alert)
                <div class="p-4 rounded-lg border 
                    @if($alert['type'] === 'warning') bg-yellow-50 border-yellow-200 text-yellow-800
                    @elseif($alert['type'] === 'danger') bg-red-50 border-red-200 text-red-800
                    @else bg-sky-50 border-sky-200 text-sky-800 @endif">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">{{ $alert['message'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Owners -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-3xl font-bold text-sky-600 mb-2">{{ $data['data']['total_owners'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">تعداد مالکان</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Total Transfers -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-3xl font-bold text-orange-600 mb-2">{{ $data['data']['statistics']['total_transfers'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">تعداد انتقالات</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                </div>

                <!-- Average Ownership -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-lg font-bold text-green-600 mb-2">{{ $data['data']['statistics']['average_ownership']['formatted'] ?? '0 روز' }}</div>
                    <div class="text-sm text-gray-600">متوسط مالکیت</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Longest Ownership -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-lg font-bold text-purple-600 mb-2">{{ $data['data']['statistics']['longest_ownership']['formatted'] ?? '0 روز' }}</div>
                    <div class="text-sm text-gray-600">طولانی‌ترین مالکیت</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Current Owner Information -->
            @if(!empty($data['data']['current_owner']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        مالک فعلی
                    </h2>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @if(!empty($data['data']['current_owner']['owner_name']))
                                <div>
                                    <div class="text-green-700 text-sm mb-1">نام مالک</div>
                                    <div class="font-bold text-green-900 text-lg">{{ $data['data']['current_owner']['owner_name'] }}</div>
                                </div>
                            @endif
                            
                            @if(!empty($data['data']['current_owner']['ownership_date']))
                                <div>
                                    <div class="text-green-700 text-sm mb-1">تاریخ مالکیت</div>
                                    <div class="font-semibold text-green-900">{{ $data['data']['current_owner']['ownership_date'] }}</div>
                                </div>
                            @endif
                            
                            @if(!empty($data['data']['current_owner']['ownership_type']))
                                <div>
                                    <div class="text-green-700 text-sm mb-1">نوع مالکیت</div>
                                    <div class="font-semibold text-green-900">{{ $data['data']['current_owner']['ownership_type'] }}</div>
                                </div>
                            @endif
                            
                            @if(!empty($data['data']['current_owner']['registration_location']))
                                <div>
                                    <div class="text-green-700 text-sm mb-1">محل ثبت</div>
                                    <div class="font-semibold text-green-900">{{ $data['data']['current_owner']['registration_location'] }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Ownership Timeline -->
            @if(!empty($data['data']['ownership_history']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        تاریخچه مالکیت
                    </h2>
                    
                    <div class="space-y-6">
                        @foreach($data['data']['ownership_history'] as $index => $record)
                            <div class="relative flex items-start">
                                <!-- Timeline Line -->
                                @if(!$loop->last)
                                    <div class="absolute right-4 top-8 w-0.5 h-20 bg-sky-300"></div>
                                @endif
                                
                                <!-- Timeline Dot -->
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                    @if($record['is_current']) bg-green-500
                                    @else bg-sky-500 @endif text-white text-sm font-bold">
                                    {{ $record['sequence'] }}
                                </div>
                                
                                <!-- Content -->
                                <div class="mr-6 flex-1 min-w-0">
                                    <div class="bg-sky-50 rounded-lg p-5 border 
                                        @if($record['is_current']) border-green-200 bg-green-50
                                        @else border-gray-200 @endif">
                                        
                                        <!-- Owner Header -->
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex-1">
                                                <h3 class="font-bold text-gray-900 text-lg mb-1">
                                                    {{ $record['owner_name'] ?? 'نام نامشخص' }}
                                                    @if($record['is_current'])
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                                            مالک فعلی
                                                        </span>
                                                    @endif
                                                </h3>
                                                <div class="text-sm text-gray-600">
                                                    {{ $record['ownership_type'] ?? 'نوع مالکیت نامشخص' }}
                                                </div>
                                            </div>
                                            <div class="text-left">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $record['ownership_duration']['formatted'] ?? '0 روز' }}
                                                </div>
                                                <div class="text-xs text-gray-500">مدت مالکیت</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Details Grid -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                            @if(!empty($record['ownership_date']))
                                                <div>
                                                    <div class="text-gray-600 mb-1 flex items-center">
                                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        تاریخ شروع
                                                    </div>
                                                    <div class="font-medium text-gray-900">{{ $record['ownership_date'] }}</div>
                                                </div>
                                            @endif
                                            
                                            @if(!empty($record['transfer_date']))
                                                <div>
                                                    <div class="text-gray-600 mb-1 flex items-center">
                                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                        </svg>
                                                        تاریخ انتقال
                                                    </div>
                                                    <div class="font-medium text-gray-900">{{ $record['transfer_date'] }}</div>
                                                </div>
                                            @endif
                                            
                                            @if(!empty($record['registration_location']))
                                                <div>
                                                    <div class="text-gray-600 mb-1 flex items-center">
                                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                        محل ثبت
                                                    </div>
                                                    <div class="font-medium text-gray-900">{{ $record['registration_location'] }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @if(!empty($record['transfer_reason']))
                                            <div class="mt-4 pt-4 border-t border-gray-200">
                                                <div class="text-gray-600 text-xs mb-1">دلیل انتقال</div>
                                                <div class="text-sm text-gray-900">{{ $record['transfer_reason'] }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Vehicle Information -->
            @if(!empty($data['data']['vehicle_info']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        مشخصات خودرو
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if(!empty($data['data']['vehicle_info']['brand']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="text-gray-600 text-sm mb-1">برند و مدل</div>
                                <div class="font-semibold text-gray-900">
                                    {{ $data['data']['vehicle_info']['brand'] }}
                                    @if(!empty($data['data']['vehicle_info']['model']))
                                        {{ $data['data']['vehicle_info']['model'] }}
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['vehicle_info']['production_year']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="text-gray-600 text-sm mb-1">سال تولید</div>
                                <div class="font-semibold text-gray-900">{{ $data['data']['vehicle_info']['production_year'] }}</div>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['vehicle_info']['color']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="text-gray-600 text-sm mb-1">رنگ</div>
                                <div class="font-semibold text-gray-900">{{ $data['data']['vehicle_info']['color'] }}</div>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['vehicle_info']['engine_number']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="text-gray-600 text-sm mb-1">شماره موتور</div>
                                <div class="font-semibold text-gray-900 font-mono cursor-pointer" 
                                     onclick="copyToClipboard('{{ $data['data']['vehicle_info']['engine_number'] }}')"
                                     title="کلیک کنید تا کپی شود">
                                    {{ $data['data']['vehicle_info']['engine_number'] }}
                                </div>
                            </div>
                        @endif
                        
                        @if(!empty($data['data']['vehicle_info']['chassis_number']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="text-gray-600 text-sm mb-1">شماره شاسی</div>
                                <div class="font-semibold text-gray-900 font-mono cursor-pointer"
                                     onclick="copyToClipboard('{{ $data['data']['vehicle_info']['chassis_number'] }}')"
                                     title="کلیک کنید تا کپی شود">
                                    {{ $data['data']['vehicle_info']['chassis_number'] }}
                                </div>
                            </div>
                        @endif
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
                    <button onclick="copyAllInfo()" class="w-full bg-sky-100 hover:bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        کپی تاریخچه
                    </button>
                    <button onclick="exportTimeline()" class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        صادرات PDF
                    </button>
                </div>
            </div>

            <!-- Ownership Stats -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">آمار مالکیت</h3>
                <div class="space-y-3 text-sm">
                    @if(!empty($data['data']['statistics']['longest_ownership']['owner']))
                        <div>
                            <div class="text-gray-600 mb-1">طولانی‌ترین مالک:</div>
                            <div class="font-medium text-gray-900">{{ $data['data']['statistics']['longest_ownership']['owner'] }}</div>
                            <div class="text-xs text-gray-500">{{ $data['data']['statistics']['longest_ownership']['formatted'] }}</div>
                        </div>
                    @endif
                    
                    @if(!empty($data['data']['statistics']['shortest_ownership']['owner']))
                        <div>
                            <div class="text-gray-600 mb-1">کوتاه‌ترین مالک:</div>
                            <div class="font-medium text-gray-900">{{ $data['data']['statistics']['shortest_ownership']['owner'] }}</div>
                            <div class="text-xs text-gray-500">{{ $data['data']['statistics']['shortest_ownership']['formatted'] }}</div>
                        </div>
                    @endif
                    
                    <div class="pt-3 border-t border-gray-200">
                        <div class="text-gray-600 mb-1">تاریخ استعلام:</div>
                        <div class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            </div>

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
                    {{ $data['data']['total_owners'] ?? 0 }} مالک از ابتدا تاکنون
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-sky-50 border border-sky-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-sky-800 mb-3">نکات مهم</h3>
                <ul class="text-sm text-sky-700 space-y-2">
                    <li>• تعداد مالکان زیاد ممکن است نشانه مشکل باشد</li>
                    <li>• انتقالات مکرر اخیر قابل بررسی است</li>
                    <li>• اطلاعات بر اساس رکوردهای رسمی است</li>
                    <li>• در صورت خرید، سوابق را بررسی کنید</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
const historyData = @json($data['data'] ?? []);

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('کپی شد!', 'success');
    }).catch(() => {
        showToast('خطا در کپی کردن', 'error');
    });
}

function copyAllInfo() {
    let text = 'تاریخچه مالکیت پلاک\n';
    text += '='.repeat(40) + '\n\n';
    text += `پلاک: ${historyData.plate_number || 'نامشخص'}\n`;
    text += `تاریخ گزارش: {{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d H:i') }}\n\n`;
    
    text += `خلاصه آمار:\n`;
    text += `تعداد مالکان: ${historyData.total_owners || 0}\n`;
    text += `تعداد انتقالات: ${historyData.statistics?.total_transfers || 0}\n`;
    text += `متوسط مالکیت: ${historyData.statistics?.average_ownership?.formatted || '0 روز'}\n\n`;
    
    if (historyData.ownership_history && historyData.ownership_history.length > 0) {
        text += 'تاریخچه مالکیت:\n';
        historyData.ownership_history.forEach((record, index) => {
            text += `${record.sequence}. ${record.owner_name || 'نامشخص'}\n`;
            text += `   مدت مالکیت: ${record.ownership_duration?.formatted || '0 روز'}\n`;
            if (record.ownership_date) text += `   از: ${record.ownership_date}\n`;
            if (record.transfer_date) text += `   تا: ${record.transfer_date}\n`;
            if (record.is_current) text += `   (مالک فعلی)\n`;
            text += '\n';
        });
    }
    
    text += '\nتولید شده در پیشخوانک (pishkhanak.com)';
    
    copyToClipboard(text);
}

function exportTimeline() {
    // PDF export functionality can be implemented here
    showToast('قابلیت صادرات PDF به زودی اضافه خواهد شد', 'info');
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
    .border { border: 1px solid #e5e7eb !important; }
    
    .timeline-item {
        break-inside: avoid;
        margin-bottom: 1rem;
    }
}
</style>
@endsection 