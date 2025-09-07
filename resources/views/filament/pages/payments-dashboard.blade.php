<x-filament-panels::page>
    @php
        $data = $this->getWidgetData();
    @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">مدیریت پرداخت‌ها و تراکنش‌ها</h1>
                <p class="text-gray-600 mt-1">نظارت و مدیریت کلیه تراکنش‌های مالی سیستم</p>
            </div>
            <div class="flex space-x-3 space-x-reverse">
                <x-filament::button tag="a" href="{{ \App\Filament\Resources\GatewayTransactionResource::getUrl('index') }}" size="sm">
                    <x-heroicon-o-eye class="w-4 h-4 mr-2" />
                    مشاهده همه تراکنش‌ها
                </x-filament::button>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-green-700">پرداخت‌های امروز</h3>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($data['payments_today']) }}</p>
                    <p class="text-sm text-green-600">تراکنش انجام شده</p>
                </div>
                <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-credit-card class="w-6 h-6 text-green-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-sky-50 to-sky-100 rounded-xl p-6 border border-sky-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-sky-700">درآمد امروز</h3>
                    <p class="text-2xl font-bold text-sky-900">{{ number_format($data['revenue_today']) }}</p>
                    <p class="text-sm text-sky-600">تومان</p>
                </div>
                <div class="w-12 h-12 bg-sky-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-banknotes class="w-6 h-6 text-sky-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-purple-700">نرخ موفقیت</h3>
                    <p class="text-2xl font-bold text-purple-900">{{ $data['success_rate'] }}%</p>
                    <p class="text-sm text-purple-600">از کل تراکنش‌ها</p>
                </div>
                <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-chart-bar-square class="w-6 h-6 text-purple-700" />
                </div>
            </div>
        </div>
    </div>

    <!-- Header Widgets Section -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">آمار کلی</h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach($this->getHeaderWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div>
    </div>

    <!-- Charts Section -->
    <div class="space-y-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">نمودارهای تحلیلی</h2>
            <div class="space-y-6">
                @foreach($this->getWidgets() as $widget)
                    <div class="bg-sky-50 rounded-lg p-4" style="min-height: 350px;">
                        @livewire($widget)
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">عملیات سریع</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ \App\Filament\Resources\GatewayTransactionResource::getUrl('index') }}" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-sky-50 transition-colors text-center">
                    <x-heroicon-o-list-bullet class="w-6 h-6 text-gray-600 mx-auto mb-2" />
                    <span class="text-sm font-medium text-gray-900">لیست تراکنش‌ها</span>
                </a>
                
                <a href="{{ \App\Filament\Resources\PaymentGatewayResource::getUrl('index') }}" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-sky-50 transition-colors text-center">
                    <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-gray-600 mx-auto mb-2" />
                    <span class="text-sm font-medium text-gray-900">تنظیمات درگاه</span>
                </a>
                
                <a href="{{ \App\Filament\Resources\GatewayTransactionResource::getUrl('create') }}" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-sky-50 transition-colors text-center">
                    <x-heroicon-o-plus class="w-6 h-6 text-gray-600 mx-auto mb-2" />
                    <span class="text-sm font-medium text-gray-900">تراکنش جدید</span>
                </a>
                
                <a href="#" onclick="window.print()" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-sky-50 transition-colors text-center">
                    <x-heroicon-o-printer class="w-6 h-6 text-gray-600 mx-auto mb-2" />
                    <span class="text-sm font-medium text-gray-900">چاپ گزارش</span>
                </a>
            </div>
        </div>
    </div>

    <style>
        /* Enhanced chart container styling */
        .bg-sky-50 {
            background-color: #f9fafb;
        }
        
        /* Ensure charts have proper height */
        [wire\\:id] canvas {
            max-height: 300px !important;
            height: 300px !important;
        }
        
        /* Improved card hover effects */
        .hover\\:bg-sky-50:hover {
            background-color: #f9fafb;
        }
        
        /* Better grid responsiveness */
        @media (max-width: 768px) {
            .grid-cols-2.md\\:grid-cols-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</x-filament-panels::page> 