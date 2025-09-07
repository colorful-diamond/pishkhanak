<!-- Custom Car Violation Inquiry Service Table Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <!-- Service Header with Icon -->
    <div class="flex items-center mb-6">
        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center ml-3">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $service->title }}</h2>
        </div>
    </div>
    
    <!-- User's Plate Display Section -->
    <div class="bg-gradient-to-r from-red-50 to-orange-50 border-2 border-red-200 rounded-xl p-6 mb-6">
        
        <div class="flex justify-center">
            @php
                // Extract plate information from request details
                $platePart1 = $requestDetails['plate_part1'] ?? '۱۲';
                $plateLetter = $requestDetails['plate_letter'] ?? 'الف';
                $platePart2 = $requestDetails['plate_part2'] ?? '۳۴۵';
                $platePart3 = $requestDetails['plate_part3'] ?? '۵۶';
            @endphp
            
            <!-- Enhanced Car Plate Display -->
            <div class="relative">
                <!-- Plate Shadow/Depth Effect -->
                <!-- Main Plate Container -->
                <div class="relative bg-white border-2 border-gray-700 rounded-xl p-2 shadow-xl">
                    
                    <!-- Plate Numbers -->
                    <div class="flex items-center pl-10 relative flex-row-reverse justify-center gap-3 bg-gradient-to-b from-gray-50 to-gray-100 rounded-lg p-3 border-2 border-gray-300">

                        <div class="absolute rounded-tl-lg rounded-bl-lg top-0 left-0 bottom-0 flex-shrink-0 bg-dark-blue-600 p-2">
                            <img src="https://pishkhanak.com/assets/images/ir-plate.svg" alt="پلاک ایران" class="w-4 h-7" data-pagespeed-url-hash="2507114506" onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
                        </div>
                        <!-- Part 1 -->
                        <div class="bg-white border-2 border-gray-400 rounded-md w-12 h-12 flex items-center justify-center shadow-sm">
                            <span class="text-xl font-bold text-gray-800">{{ $platePart1 }}</span>
                        </div>

                        <!-- Letter -->
                        <div class="bg-gradient-to-b from-yellow-300 to-yellow-400 border-2 border-yellow-600 rounded-md w-12 h-12 flex items-center justify-center shadow-sm">
                            <span class="text-xl font-bold text-gray-800">{{ $plateLetter }}</span>
                        </div>

                        <!-- Part 2 (Serial) -->
                        <div class="bg-white border-2 border-gray-400 rounded-md w-16 h-12 flex items-center justify-center shadow-sm">
                            <span class="text-xl font-bold text-gray-800">{{ $platePart2 }}</span>
                        </div>
                    
                        <!-- Part 3 (City Code) -->
                        <div class="bg-white border-2 border-gray-400 rounded-md w-12 h-12 flex items-center justify-center shadow-sm">
                            <span class="text-xl font-bold text-gray-800">{{ $platePart3 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
            // Get combined data from both input and preview
            $nationalCode = $requestDetails['national_code'] ?? '1234567890';
            $mobile = $requestDetails['mobile'] ?? null;
            
            // Get preview data or use defaults
            $vehicleStatus = $previewData['vehicle_status'] ?? 'فعال';
            $violationStatus = $previewData['violation_status'] ?? null;
            $carData = $previewData['carData'] ?? null;
            
            // Check if data comes from cache (passed through previewData)
            $fromCache = $previewData['from_cache'] ?? false;
            $cachedAt = $previewData['cached_at'] ?? null;
        @endphp
        
        <!-- Vehicle & Owner Information -->
        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">کد ملی مالک:</span>
                    <span class="text-sm dir-ltr font-bold text-gray-900 font-mono">{{ substr($nationalCode, 0, 3) }}***{{ substr($nationalCode, -3) }}</span>
                </div>

                @if($violationStatus === 'success' && $carData)
                    @php
                        $vehicleBrand = $previewData['vehicle_brand'] ?? 'نامشخص';
                        $vehicleModel = $previewData['vehicle_model'] ?? '';
                        $constructionYear = $previewData['construction_year'] ?? 'نامشخص';
                        
                        // Show vehicle type only if brand is not "نامشخص"
                        $showVehicleType = $vehicleBrand !== 'نامشخص';
                        
                        // Show construction year only if it's not "نامشخص"
                        $showConstructionYear = $constructionYear !== 'نامشخص';
                    @endphp

                    @if($showVehicleType)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">نوع خودرو:</span>
                        <span class="text-sm dir-ltr font-bold text-gray-900 font-mono">{{ $vehicleBrand }} {{ $vehicleModel }}</span>
                    </div>
                    @endif

                    @if($showConstructionYear)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">سال ساخت:</span>
                        <span class="text-sm dir-ltr font-bold text-gray-900 font-mono">{{ $constructionYear }}</span>
                    </div>
                    @endif
                @endif
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
                <div class="text-lg font-bold text-red-600">{{ number_format($service->price) }} تومان</div>
            </div>
        </div>
    </div>
    
</div>