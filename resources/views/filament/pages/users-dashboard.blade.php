<x-filament-panels::page>
    @php
        $data = $this->getWidgetData();
    @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تحلیل و مدیریت کاربران</h1>
                <p class="text-gray-600 mt-1">آمار و گزارش‌های جامع کاربران سیستم</p>
            </div>
            <div class="flex space-x-3 space-x-reverse">
                <x-filament::button tag="a" href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}" size="sm">
                    <x-heroicon-o-eye class="w-4 h-4 mr-2" />
                    مشاهده همه کاربران
                </x-filament::button>
            </div>
        </div>
    </div>

    <!-- User Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-r from-sky-50 to-sky-100 rounded-xl p-6 border border-sky-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-sky-700">کل کاربران</h3>
                    <p class="text-2xl font-bold text-sky-900">{{ number_format($data['total_users']) }}</p>
                    <p class="text-sm text-sky-600">ثبت‌نام شده</p>
                </div>
                <div class="w-12 h-12 bg-sky-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-users class="w-6 h-6 text-sky-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-green-700">کاربران فعال</h3>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($data['active_users']) }}</p>
                    <p class="text-sm text-green-600">تایید شده</p>
                </div>
                <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-user-plus class="w-6 h-6 text-green-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-purple-700">عضویت امروز</h3>
                    <p class="text-2xl font-bold text-purple-900">{{ number_format($data['new_users_today']) }}</p>
                    <p class="text-sm text-purple-600">کاربر جدید</p>
                </div>
                <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-sparkles class="w-6 h-6 text-purple-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-orange-700">نرخ رشد</h3>
                    <p class="text-2xl font-bold text-orange-900">
                        @if($data['growth_rate'] >= 0) +@endif{{ number_format($data['growth_rate'], 1) }}%
                    </p>
                    <p class="text-sm text-orange-600">ماه جاری</p>
                </div>
                <div class="w-12 h-12 bg-orange-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-chart-bar-square class="w-6 h-6 text-orange-700" />
                </div>
            </div>
        </div>
    </div>

    <!-- User Engagement Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">آمار عضویت</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">امروز</span>
                    <span class="text-sm font-bold text-purple-600">{{ number_format($data['new_users_today']) }} نفر</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">این هفته</span>
                    <span class="text-sm font-bold text-sky-600">{{ number_format($data['new_users_week']) }} نفر</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">این ماه</span>
                    <span class="text-sm font-bold text-green-600">{{ number_format($data['new_users_month']) }} نفر</span>
                </div>
                <div class="pt-2 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">نرخ رشد ماهانه</span>
                        <span class="text-sm font-bold {{ $data['growth_rate'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            @if($data['growth_rate'] >= 0) +@endif{{ number_format($data['growth_rate'], 1) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">فعالیت کاربران</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">کاربران با تراکنش</span>
                    <span class="text-sm font-bold text-green-600">{{ number_format($data['users_with_transactions']) }} نفر</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">کاربران با کیف‌پول</span>
                    <span class="text-sm font-bold text-sky-600">{{ number_format($data['users_with_wallets']) }} نفر</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">نرخ تایید ایمیل</span>
                    <span class="text-sm font-bold text-purple-600">
                        {{ $data['total_users'] > 0 ? round(($data['active_users'] / $data['total_users']) * 100) : 0 }}%
                    </span>
                </div>
                <div class="pt-2 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">نرخ مشارکت</span>
                        <span class="text-sm font-bold text-orange-600">
                            {{ $data['total_users'] > 0 ? round(($data['users_with_transactions'] / $data['total_users']) * 100) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">تحلیل وضعیت</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">فعال</span>
                    </div>
                    <span class="text-sm font-bold text-green-700">{{ number_format($data['active_users']) }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">در انتظار تایید</span>
                    </div>
                    <span class="text-sm font-bold text-yellow-700">{{ number_format($data['total_users'] - $data['active_users']) }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-sky-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-sky-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">دارای کیف‌پول</span>
                    </div>
                    <span class="text-sm font-bold text-sky-700">{{ number_format($data['users_with_wallets']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">عملیات مدیریت کاربران</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-sky-50 hover:border-sky-300 transition-all text-center group">
                    <x-heroicon-o-eye class="w-8 h-8 text-sky-600 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                    <span class="text-sm font-medium text-gray-900">مشاهده کاربران</span>
                    <p class="text-xs text-gray-500 mt-1">لیست کامل کاربران</p>
                </a>
                
                <a href="{{ \App\Filament\Resources\UserResource::getUrl('create') }}" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 transition-all text-center group">
                    <x-heroicon-o-user-plus class="w-8 h-8 text-green-600 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                    <span class="text-sm font-medium text-gray-900">افزودن کاربر</span>
                    <p class="text-xs text-gray-500 mt-1">ایجاد حساب جدید</p>
                </a>
                
                <a href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-yellow-50 hover:border-yellow-300 transition-all text-center group">
                    <x-heroicon-o-clock class="w-8 h-8 text-yellow-600 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                    <span class="text-sm font-medium text-gray-900">در انتظار تایید</span>
                    <p class="text-xs text-gray-500 mt-1">{{ number_format($data['total_users'] - $data['active_users']) }} کاربر</p>
                </a>
                
                <a href="#" onclick="window.print()" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-300 transition-all text-center group">
                    <x-heroicon-o-document-text class="w-8 h-8 text-purple-600 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                    <span class="text-sm font-medium text-gray-900">گزارش کاربران</span>
                    <p class="text-xs text-gray-500 mt-1">تولید گزارش آماری</p>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">بینش‌های کاربری</h3>
            <div class="space-y-4">
                <div class="p-4 bg-gradient-to-r from-sky-50 to-sky-100 rounded-lg border border-sky-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-sky-800">میانگین کاربران جدید روزانه</h4>
                            <p class="text-lg font-bold text-sky-900">
                                {{ $data['new_users_month'] > 0 ? round($data['new_users_month'] / 30, 1) : 0 }} نفر
                            </p>
                        </div>
                        <x-heroicon-o-calendar class="w-8 h-8 text-sky-600" />
                    </div>
                </div>
                
                <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-green-800">نرخ تبدیل به کاربر فعال</h4>
                            <p class="text-lg font-bold text-green-900">
                                {{ $data['total_users'] > 0 ? round(($data['users_with_transactions'] / $data['total_users']) * 100) : 0 }}%
                            </p>
                        </div>
                        <x-heroicon-o-chart-bar class="w-8 h-8 text-green-600" />
                    </div>
                </div>
                
                <div class="p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-purple-800">کاربران با کیف‌پول فعال</h4>
                            <p class="text-lg font-bold text-purple-900">
                                {{ $data['total_users'] > 0 ? round(($data['users_with_wallets'] / $data['total_users']) * 100) : 0 }}%
                            </p>
                        </div>
                        <x-heroicon-o-wallet class="w-8 h-8 text-purple-600" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Growth Alert -->
    @if($data['growth_rate'] < 0)
        <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
            <div class="flex items-center">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600 mr-3" />
                <div>
                    <h3 class="text-sm font-medium text-red-800">توجه: کاهش نرخ رشد کاربران</h3>
                    <p class="text-sm text-red-700 mt-1">
                        نرخ رشد کاربران {{ number_format(abs($data['growth_rate']), 1) }}% کاهش یافته است. لطفاً استراتژی‌های جذب کاربر را بررسی کنید.
                    </p>
                </div>
                <div class="mr-auto">
                    <x-filament::button tag="a" href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}" size="sm" color="warning">
                        بررسی کاربران
                    </x-filament::button>
                </div>
            </div>
        </div>
    @elseif($data['growth_rate'] > 50)
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
            <div class="flex items-center">
                <x-heroicon-o-sparkles class="w-6 h-6 text-green-600 mr-3" />
                <div>
                    <h3 class="text-sm font-medium text-green-800">عالی: رشد قابل توجه کاربران</h3>
                    <p class="text-sm text-green-700 mt-1">
                        نرخ رشد کاربران {{ number_format($data['growth_rate'], 1) }}% افزایش یافته است. عملکرد بسیار خوبی دارید!
                    </p>
                </div>
            </div>
        </div>
    @endif

    <style>
        /* Enhanced transition effects */
        .group:hover .group-hover\:scale-110 {
            transform: scale(1.1);
        }
        
        .transition-all {
            transition: all 0.2s ease-in-out;
        }
        
        /* Enhanced hover states */
        .hover\:bg-sky-50:hover {
            background-color: #eff6ff;
        }
        
        .hover\:bg-green-50:hover {
            background-color: #f0fdf4;
        }
        
        .hover\:bg-yellow-50:hover {
            background-color: #fefce8;
        }
        
        .hover\:bg-purple-50:hover {
            background-color: #faf5ff;
        }
        
        /* Gradient cards styling */
        .bg-gradient-to-r {
            background-image: linear-gradient(to right, var(--tw-gradient-stops));
        }
    </style>
</x-filament-panels::page> 