@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">لیست پلاک‌های فعال</h1>
                <p class="text-gray-600">کد ملی {{ $data['data']['national_code'] ?? 'نامشخص' }}</p>
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
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Plates -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-3xl font-bold text-sky-600 mb-2">{{ $data['data']['total_plates'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">کل پلاک‌ها</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Cars -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ $data['data']['summary']['cars'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">خودرو</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Motorcycles -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div class="text-3xl font-bold text-orange-600 mb-2">{{ $data['data']['summary']['motorcycles'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">موتورسیکلت</div>
                    <div class="mt-2">
                        <svg class="w-8 h-8 mx-auto text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Insurance Status -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    @php
                        $activeInsurance = $data['data']['summary']['active_insurance'] ?? 0;
                        $expiredInsurance = $data['data']['summary']['expired_insurance'] ?? 0;
                        $statusColor = $expiredInsurance > 0 ? 'text-red-600' : 'text-green-600';
                        $statusText = $expiredInsurance > 0 ? 'نیاز به تمدید' : 'بیمه فعال';
                    @endphp
                    <div class="text-3xl font-bold {{ $statusColor }} mb-2">{{ $activeInsurance }}</div>
                    <div class="text-sm text-gray-600">بیمه فعال</div>
                    <div class="mt-2 text-xs {{ $statusColor }} font-medium">{{ $statusText }}</div>
                </div>
            </div>

            <!-- Plates List -->
            @if(!empty($data['data']['plates']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            جزئیات پلاک‌ها
                        </h2>
                        
                        <!-- Filter Options -->
                        <div class="flex items-center gap-2">
                            <select id="vehicleTypeFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-1" onchange="filterPlates()">
                                <option value="all">همه وسایل</option>
                                <option value="خودرو">خودرو</option>
                                <option value="موتورسیکلت">موتورسیکلت</option>
                            </select>
                            <select id="statusFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-1" onchange="filterPlates()">
                                <option value="all">همه وضعیت‌ها</option>
                                <option value="فعال">فعال</option>
                                <option value="غیرفعال">غیرفعال</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="platesContainer">
                        @foreach($data['data']['plates'] as $index => $plate)
                            <div class="plate-card border border-gray-200 rounded-lg p-5 hover:shadow-sm transition-shadow" 
                                 data-vehicle-type="{{ $plate['vehicle_type'] ?? 'خودرو' }}" 
                                 data-status="{{ $plate['status'] ?? 'فعال' }}">
                                
                                <!-- Plate Visual Representation -->
                                <div class="flex items-center justify-center mb-4">
                                    <div class="bg-white border-2 border-gray-800 rounded-lg p-3 shadow-sm min-w-0 flex-shrink-0" style="font-family: 'Courier New', monospace;">
                                        <div class="text-center">
                                            <!-- Iran Flag Colors -->
                                            <div class="flex justify-center mb-1">
                                                <div class="w-2 h-1 bg-green-500"></div>
                                                <div class="w-2 h-1 bg-white border-t border-b border-gray-300"></div>
                                                <div class="w-2 h-1 bg-red-500"></div>
                                            </div>
                                            
                                            <!-- Plate Number -->
                                            <div class="text-lg font-bold text-gray-900 mb-1">
                                                {{ $plate['formatted_plate'] ?? $plate['plate_number'] ?? 'نامشخص' }}
                                            </div>
                                            
                                            <!-- Iran Text -->
                                            <div class="text-xs text-gray-600">ایران</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Plate Details -->
                                <div class="space-y-3">
                                    <!-- Vehicle Type & Status -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            @if(($plate['vehicle_type'] ?? 'خودرو') === 'موتورسیکلت')
                                                <svg class="w-5 h-5 ml-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            @endif
                                            <span class="font-medium text-gray-900">{{ $plate['vehicle_type'] ?? 'خودرو' }}</span>
                                        </div>
                                        
                                        @if(($plate['status'] ?? 'فعال') === 'فعال')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                فعال
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                غیرفعال
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Vehicle Information Grid -->
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        @if(!empty($plate['brand']))
                                            <div class="bg-sky-50 rounded p-2">
                                                <div class="text-gray-600 text-xs">برند</div>
                                                <div class="font-medium text-gray-900">{{ $plate['brand'] }}</div>
                                            </div>
                                        @endif
                                        
                                        @if(!empty($plate['model']))
                                            <div class="bg-sky-50 rounded p-2">
                                                <div class="text-gray-600 text-xs">مدل</div>
                                                <div class="font-medium text-gray-900">{{ $plate['model'] }}</div>
                                            </div>
                                        @endif
                                        
                                        @if(!empty($plate['production_year']))
                                            <div class="bg-sky-50 rounded p-2">
                                                <div class="text-gray-600 text-xs">سال تولید</div>
                                                <div class="font-medium text-gray-900">{{ $plate['production_year'] }}</div>
                                            </div>
                                        @endif
                                        
                                        @if(!empty($plate['color']))
                                            <div class="bg-sky-50 rounded p-2">
                                                <div class="text-gray-600 text-xs">رنگ</div>
                                                <div class="font-medium text-gray-900">{{ $plate['color'] }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Status Information -->
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <!-- Insurance Status -->
                                        <div class="bg-sky-50 rounded p-2">
                                            <div class="text-gray-600 text-xs mb-1">بیمه شخص ثالث</div>
                                            @if(($plate['insurance_status'] ?? 'نامشخص') === 'فعال')
                                                <div class="flex items-center text-green-600">
                                                    <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-xs font-medium">فعال</span>
                                                </div>
                                            @elseif(($plate['insurance_status'] ?? 'نامشخص') === 'منقضی')
                                                <div class="flex items-center text-red-600">
                                                    <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-xs font-medium">منقضی</span>
                                                </div>
                                            @else
                                                <div class="flex items-center text-gray-600">
                                                    <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-xs font-medium">نامشخص</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Technical Visit -->
                                        <div class="bg-sky-50 rounded p-2">
                                            <div class="text-gray-600 text-xs mb-1">معاینه فنی</div>
                                            @if(($plate['technical_visit_status'] ?? 'نامشخص') === 'معتبر')
                                                <div class="flex items-center text-green-600">
                                                    <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-xs font-medium">معتبر</span>
                                                </div>
                                            @elseif(($plate['technical_visit_status'] ?? 'نامشخص') === 'منقضی')
                                                <div class="flex items-center text-red-600">
                                                    <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-xs font-medium">منقضی</span>
                                                </div>
                                            @else
                                                <div class="flex items-center text-gray-600">
                                                    <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-xs font-medium">نامشخص</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Additional Information -->
                                    @if(!empty($plate['registration_date']) || !empty($plate['region_name']) || !empty($plate['ownership_type']))
                                        <div class="pt-3 border-t border-gray-200">
                                            <div class="grid grid-cols-1 gap-2 text-xs text-gray-600">
                                                @if(!empty($plate['registration_date']))
                                                    <div class="flex justify-between">
                                                        <span>تاریخ ثبت:</span>
                                                        <span>{{ $plate['registration_date'] }}</span>
                                                    </div>
                                                @endif
                                                @if(!empty($plate['region_name']))
                                                    <div class="flex justify-between">
                                                        <span>منطقه:</span>
                                                        <span>{{ $plate['region_name'] }}</span>
                                                    </div>
                                                @endif
                                                @if(!empty($plate['ownership_type']))
                                                    <div class="flex justify-between">
                                                        <span>نوع مالکیت:</span>
                                                        <span>{{ $plate['ownership_type'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Action Button -->
                                    <div class="pt-3">
                                        <button onclick="copyPlateInfo({{ $index }})" class="w-full bg-sky-100 hover:bg-sky-200 text-gray-700 px-3 py-2 rounded text-sm font-medium transition-colors">
                                            کپی اطلاعات پلاک
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- No Plates -->
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">پلاک فعالی یافت نشد</h3>
                    <p class="text-gray-600">هیچ پلاک فعالی برای این کد ملی ثبت نشده است.</p>
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
                    <button onclick="copyAllPlates()" class="w-full bg-sky-100 hover:bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        کپی تمام اطلاعات
                    </button>
                    <button onclick="downloadPDF()" class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        دانلود PDF
                    </button>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">آمار سریع</h3>
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
                        <span class="text-gray-600">کد ملی:</span>
                        <span class="font-medium font-mono">{{ $data['data']['national_code'] ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Indicators -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">وضعیت‌ها</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full ml-2"></div>
                            <span class="text-gray-600">فعال</span>
                        </div>
                        <span class="font-medium">{{ collect($data['data']['plates'] ?? [])->where('status', 'فعال')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full ml-2"></div>
                            <span class="text-gray-600">غیرفعال</span>
                        </div>
                        <span class="font-medium">{{ collect($data['data']['plates'] ?? [])->where('status', '!=', 'فعال')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-400 rounded-full ml-2"></div>
                            <span class="text-gray-600">بیمه فعال</span>
                        </div>
                        <span class="font-medium">{{ collect($data['data']['plates'] ?? [])->where('insurance_status', 'فعال')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-400 rounded-full ml-2"></div>
                            <span class="text-gray-600">بیمه منقضی</span>
                        </div>
                        <span class="font-medium">{{ collect($data['data']['plates'] ?? [])->where('insurance_status', 'منقضی')->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-sky-50 border border-sky-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-sky-800 mb-3">نکات مهم</h3>
                <ul class="text-sm text-sky-700 space-y-2">
                    <li>• بیمه شخص ثالث را به موقع تمدید کنید</li>
                    <li>• معاینه فنی خودرو را در زمان مقرر انجام دهید</li>
                    <li>• در صورت فروش خودرو، انتقال سند را انجام دهید</li>
                    <li>• از پلاک‌های غیرمجاز استفاده نکنید</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
const platesData = @json($data['data']['plates'] ?? []);

function filterPlates() {
    const vehicleTypeFilter = document.getElementById('vehicleTypeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const plateCards = document.querySelectorAll('.plate-card');

    plateCards.forEach(card => {
        const vehicleType = card.getAttribute('data-vehicle-type');
        const status = card.getAttribute('data-status');
        
        const vehicleMatch = vehicleTypeFilter === 'all' || vehicleType === vehicleTypeFilter;
        const statusMatch = statusFilter === 'all' || status === statusFilter;
        
        if (vehicleMatch && statusMatch) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function copyPlateInfo(index) {
    const plate = platesData[index];
    if (!plate) return;
    
    let text = `اطلاعات پلاک\n`;
    text += `شماره پلاک: ${plate.plate_number || 'نامشخص'}\n`;
    text += `نوع وسیله: ${plate.vehicle_type || 'نامشخص'}\n`;
    if (plate.brand) text += `برند: ${plate.brand}\n`;
    if (plate.model) text += `مدل: ${plate.model}\n`;
    if (plate.production_year) text += `سال تولید: ${plate.production_year}\n`;
    if (plate.color) text += `رنگ: ${plate.color}\n`;
    text += `وضعیت: ${plate.status || 'نامشخص'}\n`;
    text += `بیمه: ${plate.insurance_status || 'نامشخص'}\n`;
    text += `معاینه فنی: ${plate.technical_visit_status || 'نامشخص'}\n`;
    
    copyToClipboard(text);
}

function copyAllPlates() {
    let text = 'گزارش پلاک‌های فعال\n';
    text += '='.repeat(40) + '\n\n';
    text += `کد ملی: {{ $data['data']['national_code'] ?? 'نامشخص' }}\n`;
    text += `تاریخ استعلام: {{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d H:i') }}\n\n`;
    text += `تعداد کل پلاک‌ها: {{ $data['data']['total_plates'] ?? 0 }}\n`;
    text += `خودرو: {{ $data['data']['summary']['cars'] ?? 0 }}\n`;
    text += `موتورسیکلت: {{ $data['data']['summary']['motorcycles'] ?? 0 }}\n\n`;
    
    if (platesData.length > 0) {
        platesData.forEach((plate, index) => {
            text += `پلاک ${index + 1}:\n`;
            text += `شماره: ${plate.plate_number || 'نامشخص'}\n`;
            text += `نوع: ${plate.vehicle_type || 'نامشخص'}\n`;
            if (plate.brand) text += `برند: ${plate.brand}\n`;
            if (plate.model) text += `مدل: ${plate.model}\n`;
            text += `وضعیت: ${plate.status || 'نامشخص'}\n`;
            text += '\n';
        });
    } else {
        text += 'هیچ پلاک فعالی یافت نشد.\n';
    }
    
    text += '\nتولید شده در پیشخوانک (pishkhanak.com)';
    copyToClipboard(text);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('کپی شد!', 'success');
    }).catch(() => {
        showToast('خطا در کپی کردن', 'error');
    });
}

function downloadPDF() {
    // PDF download functionality can be implemented here
    showToast('قابلیت دانلود PDF به زودی اضافه خواهد شد', 'info');
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
    .border { border: 1px solid #e5e7eb !important; }
    
    .plate-card {
        break-inside: avoid;
        margin-bottom: 1rem;
    }
}
</style>
@endsection 