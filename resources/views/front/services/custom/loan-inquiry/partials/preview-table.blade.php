<!-- Custom Loan Inquiry Service Table Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <!-- Service Header with Icon -->
    <div class="flex items-center mb-6">
        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center ml-3">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $service->title }}</h2>
        </div>
    </div>
    
    <!-- User's Info Display Section -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-6 mb-6">
        
        @php
            // Get combined data from both input and preview
            $nationalCode = $requestDetails['national_code'] ?? '1234567890';
            $mobile = $requestDetails['mobile'] ?? '09123456789';
            
            // Get preview data or use defaults
            $previewData = $previewData ?? [];
            $loansCount = $previewData['loans_count'] ?? 2;
            $totalAmount = $previewData['total_amount'] ?? '150,000,000';
            $status = $previewData['status'] ?? 'سالم';
        @endphp
        
        <!-- User Information -->
        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">کد ملی:</span>
                    <span class="text-sm dir-ltr font-bold text-gray-900 font-mono">{{ substr($nationalCode, 0, 3) }}***{{ substr($nationalCode, -3) }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">شماره موبایل:</span>
                    <span class="text-sm dir-ltr font-bold text-gray-900 font-mono">{{ substr($mobile, 0, 4) }}***{{ substr($mobile, -4) }}</span>
                </div>
            </div>
        </div>

        <!-- Preview Results -->
        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm">
            <h4 class="font-semibold text-gray-900 mb-3">پیش‌نمایش نتایج</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">تعداد تسهیلات:</span>
                    <span class="text-sm font-bold text-blue-600">**</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">مجموع مبلغ:</span>
                    <span class="text-sm font-bold text-green-600">*** ریال</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">وضعیت کلی:</span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        **
                    </span>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Service Information -->
    <div class="border-t border-gray-200 pt-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <div class="text-sm font-medium text-gray-900">کارمزد استعلام</div>
                </div>
            </div>
            <div class="text-left">
                <div class="text-lg font-bold text-blue-600">{{ number_format($service->price) }} تومان</div>
            </div>
        </div>
    </div>
    
</div>