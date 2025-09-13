<!-- Custom Credit Score Rating Service Table Section -->
<div class="rounded-xl md:bg-white md:rounded-xl md:shadow-sm md:border md:border-gray-200 md:p-6 bg-transparent shadow-none p-0">
    <!-- Service Header with Icon -->

    @php
        // For credit-score-rating service, always use sky blue theme
        $bankSpecific = false;
        $bankInfo = null;
        $bankColor = null;
        
        // Force sky blue for main credit score rating service
        if ($service->slug === 'credit-score-rating') {
            $bankColor = '#0ea5e9'; // sky-500 color
            $bankSpecific = false;
        } elseif ($service->parent_id && $service->parent) {
            // This is a sub-service, use the service slug as bank slug
            $bankSlug = $service->slug;
            $bankSpecific = true;
            
            try {
                $bankService = app(\App\Services\BankService::class);
                $bankInfo = $bankService->getBankBySlug($bankSlug);
                if ($bankInfo && isset($bankInfo['color'])) {
                    $bankColor = $bankInfo['color'];
                }
            } catch (\Exception $e) {
                // Bank not found, disable bank-specific display
                $bankSpecific = false;
                $bankInfo = null;
            }
        }
        
        // Function to convert hex to RGB
        $hexToRgb = function($hex) {
            $hex = ltrim($hex, '#');
            return [
                'r' => hexdec(substr($hex, 0, 2)),
                'g' => hexdec(substr($hex, 2, 2)),
                'b' => hexdec(substr($hex, 4, 2))
            ];
        };
        
        // Get RGB values if bank color exists
        $rgb = $bankColor ? $hexToRgb($bankColor) : null;
    @endphp
    <div class="flex items-center mb-6">
        @if($bankSpecific && $bankInfo)
            <img src="{{ $bankInfo['logo'] }}" alt="{{ $bankInfo['fa_name'] }}" class="w-12 h-12 rounded-lg ml-3">
        @else
            <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center ml-3"
                 @if($bankColor)
                 style="background: linear-gradient(135deg, {{ $bankColor }} 0%, {{ $bankColor }}CC 100%);"
                 @else
                 class="bg-gradient-to-br from-sky-400 to-sky-500"
                 @endif>
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        @endif
        <div>
            @if($bankSpecific && $bankInfo)
                <h2 class="text-xl font-bold text-gray-900">سامانه اعتبارسنجی {{ $bankInfo['fa_name'] }}</h2>
            @else
                <h2 class="text-xl font-bold text-gray-900">سامانه اعتبارسنجی بانکی</h2>
            @endif
        </div>
    </div>
    
    <!-- User's Info Display Section -->
    <div class="border-2 rounded-xl p-6 mb-6"
         @if($bankColor && $rgb)
         style="background: linear-gradient(135deg, rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.05) 0%, rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.1) 100%); 
                border-color: rgba({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}, 0.3);"
         @else
         class="bg-gradient-to-r from-sky-50 to-blue-50 border-sky-200"
         @endif>
        
        @php
            // Get combined data from both input and preview
            $nationalCode = $requestDetails['national_code'] ?? '1234567890';
            $mobile = $requestDetails['mobile'] ?? '09123456789';
            
            // Get Iranian banking credit preview data or use defaults
            $previewData = $previewData ?? [];
            
            // Iranian banking credit system data (0-900 scale)
            $creditInfo = $previewData['credit_info'] ?? [];
            $creditScore = $creditInfo['credit_score'] ?? 785;
            $maxScore = $creditInfo['max_score'] ?? 900;
            $rating = $creditInfo['rating'] ?? 'عالی';
            $ratingGrade = $creditInfo['rating_grade'] ?? 'A';
            $percentage = $creditInfo['percentage'] ?? 87;
            $status = $creditInfo['status'] ?? 'قابل دریافت تسهیلات';
            
            // Banking status information
            $bankingStatus = $previewData['banking_status'] ?? [];
            $creditFactors = $previewData['credit_factors'] ?? [];
            $availableFacilities = $previewData['available_facilities'] ?? [];
            $recommendations = $previewData['recommendations'] ?? [];
        @endphp
        
        <!-- User Information -->
        <div class="rounded-lg">
        <h4 class="font-semibold text-gray-900 mb-3">اطلاعات درخواست دهنده</h4>
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
    </div>
    
    {{-- Credit report preview hidden as requested --}}

    <!-- Service Information -->
    <div class="border-t border-gray-200 pt-4 mt-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <div class="text-sm font-medium text-gray-900">کارمزد درخواست اعتبارسنجی</div>
                </div>
            </div>
            <div class="text-left">
                <div class="text-lg font-bold" @if($bankColor) style="color: {{ $bankColor }};" @else class="text-sky-600" @endif>
                    {{ number_format($service->price) }} تومان
                </div>
            </div>
        </div>
    </div>
    
</div>