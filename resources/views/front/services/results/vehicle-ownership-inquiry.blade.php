@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">نتیجه استعلام مالکیت خودرو</h1>
                <p class="text-gray-600">پلاک {{ $data['data']['plate_info']['formatted_plate'] ?? $data['data']['plate_number'] ?? 'نامشخص' }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    تأیید مالکیت
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Plate Representation -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">پلاک خودرو</h2>
                <div class="flex justify-center mb-4">
                    <div class="bg-white border-4 border-gray-800 rounded-lg p-4 shadow-lg" style="font-family: 'Courier New', monospace;">
                        <!-- Iran Flag Colors -->
                        <div class="flex justify-center mb-2">
                            <div class="w-3 h-2 bg-green-500"></div>
                            <div class="w-3 h-2 bg-white border-t border-b border-gray-300"></div>
                            <div class="w-3 h-2 bg-red-500"></div>
                        </div>
                        
                        <!-- Plate Number -->
                        <div class="text-2xl font-bold text-gray-900 mb-2">
                            {{ $data['data']['plate_info']['formatted_plate'] ?? $data['data']['plate_number'] ?? 'نامشخص' }}
                        </div>
                        
                        <!-- Iran Text -->
                        <div class="text-sm text-gray-600">ایران {{ $data['data']['plate_info']['region_code'] ?? '' }}</div>
                    </div>
                </div>
                
                @if(!empty($data['data']['plate_info']['region_name']))
                    <div class="text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-sky-100 text-sky-800">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $data['data']['plate_info']['region_name'] }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Vehicle Information -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    
                    @if(!empty($data['data']['vehicle_info']['fuel_type']))
                        <div class="bg-sky-50 rounded-lg p-4">
                            <div class="text-gray-600 text-sm mb-1">نوع سوخت</div>
                            <div class="font-semibold text-gray-900">{{ $data['data']['vehicle_info']['fuel_type'] }}</div>
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

            <!-- Ownership Information -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    اطلاعات مالکیت
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(!empty($data['data']['ownership_info']['owner_name']))
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-green-700 text-sm mb-1">نام مالک</div>
                            <div class="font-bold text-green-900 text-lg">{{ $data['data']['ownership_info']['owner_name'] }}</div>
                        </div>
                    @endif
                    
                    @if(!empty($data['data']['ownership_info']['ownership_type']))
                        <div class="bg-sky-50 border border-sky-200 rounded-lg p-4">
                            <div class="text-sky-700 text-sm mb-1">نوع مالکیت</div>
                            <div class="font-semibold text-sky-900">{{ $data['data']['ownership_info']['ownership_type'] }}</div>
                        </div>
                    @endif
                    
                    @if(!empty($data['data']['ownership_info']['ownership_date']))
                        <div class="bg-sky-50 rounded-lg p-4">
                            <div class="text-gray-600 text-sm mb-1">تاریخ مالکیت</div>
                            <div class="font-semibold text-gray-900">{{ $data['data']['ownership_info']['ownership_date'] }}</div>
                        </div>
                    @endif
                    
                    @if(!empty($data['data']['ownership_info']['registration_date']))
                        <div class="bg-sky-50 rounded-lg p-4">
                            <div class="text-gray-600 text-sm mb-1">تاریخ ثبت</div>
                            <div class="font-semibold text-gray-900">{{ $data['data']['ownership_info']['registration_date'] }}</div>
                        </div>
                    @endif
                    
                    @if(!empty($data['data']['ownership_info']['registration_location']))
                        <div class="bg-sky-50 rounded-lg p-4 md:col-span-2">
                            <div class="text-gray-600 text-sm mb-1">محل ثبت</div>
                            <div class="font-semibold text-gray-900">{{ $data['data']['ownership_info']['registration_location'] }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status Information -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    وضعیت خودرو
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Vehicle Status -->
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full flex items-center justify-center
                            @if(($data['data']['status_info']['vehicle_status'] ?? '') === 'فعال') bg-green-100
                            @elseif(($data['data']['status_info']['vehicle_status'] ?? '') === 'غیرفعال') bg-red-100
                            @else bg-sky-100 @endif">
                            <svg class="w-8 h-8 
                                @if(($data['data']['status_info']['vehicle_status'] ?? '') === 'فعال') text-green-600
                                @elseif(($data['data']['status_info']['vehicle_status'] ?? '') === 'غیرفعال') text-red-600
                                @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="text-sm text-gray-600 mb-1">وضعیت خودرو</div>
                        <div class="font-semibold 
                            @if(($data['data']['status_info']['vehicle_status'] ?? '') === 'فعال') text-green-700
                            @elseif(($data['data']['status_info']['vehicle_status'] ?? '') === 'غیرفعال') text-red-700
                            @else text-gray-700 @endif">
                            {{ $data['data']['status_info']['vehicle_status'] ?? 'نامشخص' }}
                        </div>
                    </div>
                    
                    <!-- Insurance Status -->
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full flex items-center justify-center
                            @if(($data['data']['status_info']['insurance_status'] ?? '') === 'فعال') bg-green-100
                            @elseif(($data['data']['status_info']['insurance_status'] ?? '') === 'منقضی') bg-red-100
                            @else bg-sky-100 @endif">
                            <svg class="w-8 h-8 
                                @if(($data['data']['status_info']['insurance_status'] ?? '') === 'فعال') text-green-600
                                @elseif(($data['data']['status_info']['insurance_status'] ?? '') === 'منقضی') text-red-600
                                @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div class="text-sm text-gray-600 mb-1">بیمه شخص ثالث</div>
                        <div class="font-semibold 
                            @if(($data['data']['status_info']['insurance_status'] ?? '') === 'فعال') text-green-700
                            @elseif(($data['data']['status_info']['insurance_status'] ?? '') === 'منقضی') text-red-700
                            @else text-gray-700 @endif">
                            {{ $data['data']['status_info']['insurance_status'] ?? 'نامشخص' }}
                        </div>
                    </div>
                    
                    <!-- Technical Visit Status -->
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full flex items-center justify-center
                            @if(($data['data']['status_info']['technical_visit_status'] ?? '') === 'معتبر') bg-green-100
                            @elseif(($data['data']['status_info']['technical_visit_status'] ?? '') === 'منقضی') bg-red-100
                            @else bg-sky-100 @endif">
                            <svg class="w-8 h-8 
                                @if(($data['data']['status_info']['technical_visit_status'] ?? '') === 'معتبر') text-green-600
                                @elseif(($data['data']['status_info']['technical_visit_status'] ?? '') === 'منقضی') text-red-600
                                @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="text-sm text-gray-600 mb-1">معاینه فنی</div>
                        <div class="font-semibold 
                            @if(($data['data']['status_info']['technical_visit_status'] ?? '') === 'معتبر') text-green-700
                            @elseif(($data['data']['status_info']['technical_visit_status'] ?? '') === 'منقضی') text-red-700
                            @else text-gray-700 @endif">
                            {{ $data['data']['status_info']['technical_visit_status'] ?? 'نامشخص' }}
                        </div>
                    </div>
                </div>
                
                <!-- Technical Visit Dates -->
                @if(!empty($data['data']['status_info']['last_technical_visit']) || !empty($data['data']['status_info']['next_technical_visit']))
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if(!empty($data['data']['status_info']['last_technical_visit']))
                                <div class="bg-sky-50 rounded-lg p-4">
                                    <div class="text-gray-600 text-sm mb-1">آخرین معاینه فنی</div>
                                    <div class="font-semibold text-gray-900">{{ $data['data']['status_info']['last_technical_visit'] }}</div>
                                </div>
                            @endif
                            
                            @if(!empty($data['data']['status_info']['next_technical_visit']))
                                <div class="bg-sky-50 border border-sky-200 rounded-lg p-4">
                                    <div class="text-sky-700 text-sm mb-1">موعد معاینه بعدی</div>
                                    <div class="font-semibold text-sky-900">{{ $data['data']['status_info']['next_technical_visit'] }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
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
                        کپی اطلاعات
                    </button>
                    <button onclick="shareReport()" class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        اشتراک‌گذاری
                    </button>
                </div>
            </div>

            <!-- Quick Summary -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">خلاصه اطلاعات</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاریخ استعلام:</span>
                        <span class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">زمان استعلام:</span>
                        <span class="font-medium">{{ \Hekmatinasser\Verta\Verta::now()->format('H:i') }}</span>
                    </div>
                    @if(!empty($data['data']['vehicle_info']['brand']))
                        <div class="flex justify-between">
                            <span class="text-gray-600">خودرو:</span>
                            <span class="font-medium">{{ $data['data']['vehicle_info']['brand'] }} {{ $data['data']['vehicle_info']['model'] ?? '' }}</span>
                        </div>
                    @endif
                    @if(!empty($data['data']['ownership_info']['owner_name']))
                        <div class="flex justify-between">
                            <span class="text-gray-600">مالک:</span>
                            <span class="font-medium">{{ $data['data']['ownership_info']['owner_name'] }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">نکات مهم</h3>
                <ul class="text-sm text-yellow-700 space-y-2">
                    <li>• اطلاعات ارائه شده بر اساس اطلاعات رسمی است</li>
                    <li>• در صورت تغییر مالکیت، انتقال سند را انجام دهید</li>
                    <li>• بیمه و معاینه فنی را به موقع تمدید کنید</li>
                    <li>• این گزارش صرفاً جنبه اطلاع‌رسانی دارد</li>
                </ul>
            </div>

            <!-- Status Legend -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">راهنمای وضعیت‌ها</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full ml-2"></div>
                        <span>فعال / معتبر</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full ml-2"></div>
                        <span>غیرفعال / منقضی</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-sky-500 rounded-full ml-2"></div>
                        <span>نامشخص</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const vehicleData = @json($data['data'] ?? []);

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('کپی شد!', 'success');
    }).catch(() => {
        showToast('خطا در کپی کردن', 'error');
    });
}

function copyAllInfo() {
    let text = 'گزارش مالکیت خودرو\n';
    text += '='.repeat(40) + '\n\n';
    text += `پلاک: ${vehicleData.plate_number || 'نامشخص'}\n`;
    text += `تاریخ گزارش: {{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d H:i') }}\n\n`;
    
    // Vehicle Info
    if (vehicleData.vehicle_info) {
        text += 'مشخصات خودرو:\n';
        if (vehicleData.vehicle_info.brand) text += `برند: ${vehicleData.vehicle_info.brand} ${vehicleData.vehicle_info.model || ''}\n`;
        if (vehicleData.vehicle_info.production_year) text += `سال تولید: ${vehicleData.vehicle_info.production_year}\n`;
        if (vehicleData.vehicle_info.color) text += `رنگ: ${vehicleData.vehicle_info.color}\n`;
        if (vehicleData.vehicle_info.fuel_type) text += `نوع سوخت: ${vehicleData.vehicle_info.fuel_type}\n`;
        if (vehicleData.vehicle_info.engine_number) text += `شماره موتور: ${vehicleData.vehicle_info.engine_number}\n`;
        if (vehicleData.vehicle_info.chassis_number) text += `شماره شاسی: ${vehicleData.vehicle_info.chassis_number}\n`;
        text += '\n';
    }
    
    // Ownership Info
    if (vehicleData.ownership_info) {
        text += 'اطلاعات مالکیت:\n';
        if (vehicleData.ownership_info.owner_name) text += `نام مالک: ${vehicleData.ownership_info.owner_name}\n`;
        if (vehicleData.ownership_info.ownership_type) text += `نوع مالکیت: ${vehicleData.ownership_info.ownership_type}\n`;
        if (vehicleData.ownership_info.ownership_date) text += `تاریخ مالکیت: ${vehicleData.ownership_info.ownership_date}\n`;
        if (vehicleData.ownership_info.registration_location) text += `محل ثبت: ${vehicleData.ownership_info.registration_location}\n`;
        text += '\n';
    }
    
    // Status Info
    if (vehicleData.status_info) {
        text += 'وضعیت:\n';
        if (vehicleData.status_info.vehicle_status) text += `وضعیت خودرو: ${vehicleData.status_info.vehicle_status}\n`;
        if (vehicleData.status_info.insurance_status) text += `بیمه شخص ثالث: ${vehicleData.status_info.insurance_status}\n`;
        if (vehicleData.status_info.technical_visit_status) text += `معاینه فنی: ${vehicleData.status_info.technical_visit_status}\n`;
        text += '\n';
    }
    
    text += 'تولید شده در پیشخوانک (pishkhanak.com)';
    
    copyToClipboard(text);
}

function shareReport() {
    if (navigator.share) {
        navigator.share({
            title: 'گزارش مالکیت خودرو',
            text: 'گزارش مالکیت خودرو من از پیشخوانک',
            url: window.location.href
        });
    } else {
        copyToClipboard(window.location.href);
        showToast('لینک کپی شد!', 'success');
    }
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
    .bg-yellow-50 { background: #fefce8 !important; }
    .border { border: 1px solid #e5e7eb !important; }
}
</style>
@endsection 