<x-filament-panels::page>
    @php
        $data = $this->getViewData();
        $stats = $data['summary_stats'];
        $actions = $data['quick_actions'];
        $health = $data['system_health'];
        $activities = $data['recent_activities'];
    @endphp

    <!-- Summary Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Users Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">کاربران</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['users']['total']) }}</p>
                    <p class="text-xs text-green-600">+{{ $stats['users']['new_today'] }} امروز</p>
                </div>
                <div class="w-12 h-12 bg-sky-100 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-users class="w-6 h-6 text-sky-600" />
                </div>
            </div>
        </div>

        <!-- Payments Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">پرداخت‌ها</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['payments']['total_today']) }}</p>
                    <p class="text-xs text-green-600">نرخ موفقیت: {{ $stats['payments']['success_rate'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-credit-card class="w-6 h-6 text-green-600" />
                </div>
            </div>
        </div>

        <!-- Tickets Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">تیکت‌ها</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['tickets']['open']) }}</p>
                    <p class="text-xs text-red-600">{{ $stats['tickets']['urgent'] }} فوری</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-ticket class="w-6 h-6 text-yellow-600" />
                </div>
            </div>
        </div>

        <!-- Wallets Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">کیف‌پول‌ها</h3>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['wallets']['total_balance']) }} ت</p>
                    <p class="text-xs text-sky-600">{{ $stats['wallets']['active_wallets'] }} فعال</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-wallet class="w-6 h-6 text-purple-600" />
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($actions as $action)
            <a href="{{ $action['url'] }}" class="group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-{{ $action['color'] }}-100 rounded-lg flex items-center justify-center group-hover:bg-{{ $action['color'] }}-200 transition-colors">
                        <x-dynamic-component :component="$action['icon']" class="w-6 h-6 text-{{ $action['color'] }}-600" />
                    </div>
                    <x-heroicon-o-arrow-top-right-on-square class="w-5 h-5 text-gray-400 group-hover:text-{{ $action['color'] }}-600 transition-colors" />
                </div>
                <h3 class="font-medium text-gray-900 mb-2">{{ $action['title'] }}</h3>
                <p class="text-sm text-gray-600">{{ $action['description'] }}</p>
            </a>
        @endforeach
    </div>

    <!-- System Health & Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- System Health -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">وضعیت سیستم</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">درگاه‌های پرداخت</span>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full 
                            @if($health['payments'] === 'good') bg-green-500
                            @elseif($health['payments'] === 'warning') bg-yellow-500
                            @else bg-red-500 @endif
                            mr-2"></div>
                        <span class="text-sm text-gray-600">
                            @if($health['payments'] === 'good') عملکرد عالی
                            @elseif($health['payments'] === 'warning') نیاز به توجه
                            @else مشکل جدی @endif
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">سیستم پشتیبانی</span>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full 
                            @if($health['support'] === 'good') bg-green-500
                            @elseif($health['support'] === 'warning') bg-yellow-500
                            @else bg-red-500 @endif
                            mr-2"></div>
                        <span class="text-sm text-gray-600">
                            @if($health['support'] === 'good') عملکرد عالی
                            @elseif($health['support'] === 'warning') نیاز به توجه
                            @else مشکل جدی @endif
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">سیستم کیف‌پول</span>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full 
                            @if($health['wallets'] === 'good') bg-green-500
                            @elseif($health['wallets'] === 'warning') bg-yellow-500
                            @else bg-red-500 @endif
                            mr-2"></div>
                        <span class="text-sm text-gray-600">
                            @if($health['wallets'] === 'good') عملکرد عالی
                            @elseif($health['wallets'] === 'warning') نیاز به توجه
                            @else مشکل جدی @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">فعالیت‌های اخیر</h3>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse($activities as $activity)
                    <div class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-sky-50">
                        <div class="w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-dynamic-component :component="$activity['icon']" class="w-4 h-4 text-gray-600" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['time']->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-8">فعالیت اخیری وجود ندارد</p>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        .grid {
            display: grid;
        }
        .grid-cols-1 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
        @media (min-width: 768px) {
            .md\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .lg\:grid-cols-4 {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
    </style>
</x-filament-panels::page> 