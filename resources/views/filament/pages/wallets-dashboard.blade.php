<x-filament-panels::page>
    @php
        $data = $this->getWidgetData();
    @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">مدیریت کیف‌پول‌ها و موجودی</h1>
                <p class="text-gray-600 mt-1">نظارت و کنترل موجودی کیف‌پول‌های کاربران</p>
            </div>
            <div class="flex space-x-3 space-x-reverse">
                <x-filament::button tag="a" href="{{ \App\Filament\Resources\WalletResource::getUrl('index') }}" size="sm">
                    <x-heroicon-o-eye class="w-4 h-4 mr-2" />
                    مشاهده همه کیف‌پول‌ها
                </x-filament::button>
            </div>
        </div>
    </div>

    <!-- Financial Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl p-6 border border-emerald-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-emerald-700">کل موجودی</h3>
                    <p class="text-lg font-bold text-emerald-900">{{ number_format($data['total_balance']) }}</p>
                    <p class="text-sm text-emerald-600">تومان</p>
                </div>
                <div class="w-12 h-12 bg-emerald-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-banknotes class="w-6 h-6 text-emerald-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-sky-50 to-sky-100 rounded-xl p-6 border border-sky-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-sky-700">کیف‌پول‌های فعال</h3>
                    <p class="text-2xl font-bold text-sky-900">{{ number_format($data['active_wallets']) }}</p>
                    <p class="text-sm text-sky-600">دارای موجودی</p>
                </div>
                <div class="w-12 h-12 bg-sky-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-wallet class="w-6 h-6 text-sky-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-purple-700">تراکنش‌های امروز</h3>
                    <p class="text-2xl font-bold text-purple-900">{{ number_format($data['transactions_today']) }}</p>
                    <p class="text-sm text-purple-600">تراکنش انجام شده</p>
                </div>
                <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-arrow-path class="w-6 h-6 text-purple-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-orange-700">تراکنش‌های معلق</h3>
                    <p class="text-2xl font-bold text-orange-900">{{ number_format($data['pending_transactions']) }}</p>
                    <p class="text-sm text-orange-600">در انتظار تایید</p>
                </div>
                <div class="w-12 h-12 bg-orange-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-clock class="w-6 h-6 text-orange-700" />
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet Statistics Widgets -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">آمار تفصیلی کیف‌پول‌ها</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($this->getHeaderWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Wallet Management Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">عملیات کیف‌پول</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 transition-all text-center group">
                    <x-heroicon-o-plus-circle class="w-8 h-8 text-green-600 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                    <span class="text-sm font-medium text-gray-900">شارژ کیف‌پول</span>
                    <p class="text-xs text-gray-500 mt-1">افزایش موجودی کاربر</p>
                </a>
                
                <a href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-red-50 hover:border-red-300 transition-all text-center group">
                    <x-heroicon-o-minus-circle class="w-8 h-8 text-red-600 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                    <span class="text-sm font-medium text-gray-900">کسر موجودی</span>
                    <p class="text-xs text-gray-500 mt-1">کاهش موجودی کاربر</p>
                </a>
                
                <a href="{{ \App\Filament\Resources\WalletTransactionResource::getUrl('index') }}" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-sky-50 hover:border-sky-300 transition-all text-center group">
                    <x-heroicon-o-list-bullet class="w-8 h-8 text-sky-600 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                    <span class="text-sm font-medium text-gray-900">تاریخچه تراکنش</span>
                    <p class="text-xs text-gray-500 mt-1">مشاهده کل تراکنش‌ها</p>
                </a>
                
                <a href="#" onclick="window.print()" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-300 transition-all text-center group">
                    <x-heroicon-o-document-text class="w-8 h-8 text-purple-600 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                    <span class="text-sm font-medium text-gray-900">گزارش مالی</span>
                    <p class="text-xs text-gray-500 mt-1">تولید گزارش جامع</p>
                </a>
            </div>
        </div>

        <!-- Financial Insights -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">تحلیل مالی</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-sky-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">میانگین موجودی کیف‌پول</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">
                        {{ $data['active_wallets'] > 0 ? number_format($data['total_balance'] / $data['active_wallets']) : 0 }} تومان
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-sky-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-sky-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">درصد کیف‌پول‌های فعال</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">
                        {{ $data['active_wallets'] > 0 ? round(($data['active_wallets'] / max(1, $data['active_wallets'])) * 100) : 0 }}%
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-sky-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">تراکنش‌های معلق</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ $data['pending_transactions'] }} مورد</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Section for Pending Transactions -->
    @if($data['pending_transactions'] > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
            <div class="flex items-center">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-yellow-600 mr-3" />
                <div>
                    <h3 class="text-sm font-medium text-yellow-800">توجه: تراکنش‌های معلق موجود است</h3>
                    <p class="text-sm text-yellow-700 mt-1">
                        {{ $data['pending_transactions'] }} تراکنش در انتظار تایید است. لطفاً آنها را بررسی کنید.
                    </p>
                </div>
                <div class="mr-auto">
                    <x-filament::button tag="a" href="{{ \App\Filament\Resources\WalletTransactionResource::getUrl('index') }}" size="sm" color="warning">
                        بررسی تراکنش‌ها
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif

    <style>
        /* Enhanced hover effects */
        .group:hover .group-hover\:scale-110 {
            transform: scale(1.1);
        }
        
        /* Better transition effects */
        .transition-all {
            transition: all 0.2s ease-in-out;
        }
        
        /* Improved card styling */
        .hover\:bg-green-50:hover {
            background-color: #f0fdf4;
        }
        
        .hover\:bg-red-50:hover {
            background-color: #fef2f2;
        }
        
        .hover\:bg-sky-50:hover {
            background-color: #eff6ff;
        }
        
        .hover\:bg-purple-50:hover {
            background-color: #faf5ff;
        }
    </style>
</x-filament-panels::page> 