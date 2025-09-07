<x-filament-panels::page>
    @php
        $data = $this->getWidgetData();
    @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">مدیریت تیکت‌ها و پشتیبانی</h1>
                <p class="text-gray-600 mt-1">پاسخگویی و مدیریت درخواست‌های کاربران</p>
            </div>
            <div class="flex space-x-3 space-x-reverse">
                <x-filament::button tag="a" href="{{ \App\Filament\Pages\Tickets::getUrl() }}" size="sm">
                    <x-heroicon-o-eye class="w-4 h-4 mr-2" />
                    مشاهده همه تیکت‌ها
                </x-filament::button>
            </div>
        </div>
    </div>

    <!-- Support Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-gradient-to-r from-sky-50 to-sky-100 rounded-xl p-6 border border-sky-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-sky-700">کل تیکت‌ها</h3>
                    <p class="text-2xl font-bold text-sky-900">{{ number_format($data['total_tickets']) }}</p>
                    <p class="text-sm text-sky-600">در سیستم</p>
                </div>
                <div class="w-12 h-12 bg-sky-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-ticket class="w-6 h-6 text-sky-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-green-700">تیکت‌های باز</h3>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($data['open_tickets']) }}</p>
                    <p class="text-sm text-green-600">نیاز به بررسی</p>
                </div>
                <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-chat-bubble-left class="w-6 h-6 text-green-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-xl p-6 border border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-red-700">تیکت‌های فوری</h3>
                    <p class="text-2xl font-bold text-red-900">{{ number_format($data['urgent_tickets']) }}</p>
                    <p class="text-sm text-red-600">اولویت بالا</p>
                </div>
                <div class="w-12 h-12 bg-red-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-fire class="w-6 h-6 text-red-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-purple-700">تیکت‌های امروز</h3>
                    <p class="text-2xl font-bold text-purple-900">{{ number_format($data['tickets_today']) }}</p>
                    <p class="text-sm text-purple-600">ایجاد شده</p>
                </div>
                <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-plus-circle class="w-6 h-6 text-purple-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-orange-700">زمان پاسخ</h3>
                    <p class="text-2xl font-bold text-orange-900">
                        @if($data['avg_response_time'])
                            {{ number_format($data['avg_response_time'], 1) }}
                        @else
                            --
                        @endif
                    </p>
                    <p class="text-sm text-orange-600">ساعت میانگین</p>
                </div>
                <div class="w-12 h-12 bg-orange-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-clock class="w-6 h-6 text-orange-700" />
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Statistics Widget -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">آمار تفصیلی تیکت‌ها</h2>
        <div class="grid grid-cols-1 gap-6">
            @foreach($this->getHeaderWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div>
    </div>

    <!-- Action Dashboard -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">عملیات سریع</h3>
            <div class="space-y-3">
                <a href="{{ \App\Filament\Pages\Tickets::getUrl() }}" 
                   class="w-full p-4 border border-gray-200 rounded-lg hover:bg-sky-50 hover:border-sky-300 transition-all flex items-center group">
                    <x-heroicon-o-eye class="w-5 h-5 text-sky-600 mr-3 group-hover:scale-110 transition-transform" />
                    <div class="text-right">
                        <span class="block text-sm font-medium text-gray-900">مشاهده تیکت‌های باز</span>
                        <span class="text-xs text-gray-500">{{ $data['open_tickets'] }} تیکت در انتظار پاسخ</span>
                    </div>
                </a>
                
                <a href="{{ \App\Filament\Pages\Tickets::getUrl() }}" 
                   class="w-full p-4 border border-gray-200 rounded-lg hover:bg-red-50 hover:border-red-300 transition-all flex items-center group">
                    <x-heroicon-o-fire class="w-5 h-5 text-red-600 mr-3 group-hover:scale-110 transition-transform" />
                    <div class="text-right">
                        <span class="block text-sm font-medium text-gray-900">تیکت‌های فوری</span>
                        <span class="text-xs text-gray-500">{{ $data['urgent_tickets'] }} تیکت با اولویت بالا</span>
                    </div>
                </a>
                
                <a href="{{ \App\Filament\Resources\TicketCategoryResource::getUrl('index') }}" 
                   class="w-full p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 transition-all flex items-center group">
                    <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-green-600 mr-3 group-hover:scale-110 transition-transform" />
                    <div class="text-right">
                        <span class="block text-sm font-medium text-gray-900">مدیریت دسته‌بندی‌ها</span>
                        <span class="text-xs text-gray-500">تنظیمات سیستم تیکتینگ</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Support Performance -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">عملکرد پشتیبانی</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">نرخ حل مسئله</span>
                    <div class="flex items-center">
                        @php
                            $resolveRate = $data['total_tickets'] > 0 ? round((($data['total_tickets'] - $data['open_tickets']) / $data['total_tickets']) * 100) : 0;
                        @endphp
                        <div class="w-16 bg-sky-200 rounded-full h-2 ml-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $resolveRate }}%"></div>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ $resolveRate }}%</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">میانگین زمان پاسخ</span>
                    <span class="text-sm font-bold text-gray-900">
                        @if($data['avg_response_time'])
                            {{ number_format($data['avg_response_time'], 1) }} ساعت
                        @else
                            نامشخص
                        @endif
                    </span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">تیکت‌های امروز</span>
                    <span class="text-sm font-bold text-gray-900">{{ $data['tickets_today'] }} مورد</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">بار کاری فعلی</span>
                    <span class="text-sm font-bold text-gray-900
                        @if($data['open_tickets'] > 20) text-red-600
                        @elseif($data['open_tickets'] > 10) text-yellow-600
                        @else text-green-600 @endif">
                        @if($data['open_tickets'] > 20) سنگین
                        @elseif($data['open_tickets'] > 10) متوسط
                        @else سبک @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Priority Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">توزیع اولویت</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">فوری</span>
                    </div>
                    <span class="text-sm font-bold text-red-700">{{ $data['urgent_tickets'] }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">بالا</span>
                    </div>
                    <span class="text-sm font-bold text-yellow-700">
                        {{ max(0, floor($data['open_tickets'] * 0.3)) }}
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-sky-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-sky-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">متوسط</span>
                    </div>
                    <span class="text-sm font-bold text-sky-700">
                        {{ max(0, floor($data['open_tickets'] * 0.5)) }}
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-sky-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-sky-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">پایین</span>
                    </div>
                    <span class="text-sm font-bold text-gray-700">
                        {{ max(0, $data['open_tickets'] - $data['urgent_tickets'] - floor($data['open_tickets'] * 0.3) - floor($data['open_tickets'] * 0.5)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Resources -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">منابع پشتیبانی</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ \App\Filament\Resources\TicketStatusResource::getUrl('index') }}" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-sky-50 hover:border-sky-300 transition-all text-center group">
                    <x-heroicon-o-rectangle-stack class="w-8 h-8 text-sky-600 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                    <span class="text-sm font-medium text-gray-900">وضعیت‌های تیکت</span>
                </a>
                
                <a href="{{ \App\Filament\Resources\TicketTemplateResource::getUrl('index') }}" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 transition-all text-center group">
                    <x-heroicon-o-document-duplicate class="w-8 h-8 text-green-600 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                    <span class="text-sm font-medium text-gray-900">قالب‌های پاسخ</span>
                </a>
                
                <!-- Support agents feature removed -->
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 text-center opacity-50">
                    <x-heroicon-o-user-group class="w-8 h-8 text-gray-400 mx-auto mb-2" />
                    <span class="text-sm font-medium text-gray-500">پشتیبانان (غیرفعال)</span>
                </div>
                
                <a href="#" onclick="window.print()" 
                   class="p-4 border border-gray-200 rounded-lg hover:bg-orange-50 hover:border-orange-300 transition-all text-center group">
                    <x-heroicon-o-printer class="w-8 h-8 text-orange-600 mx-auto mb-2 group-hover:scale-110 transition-transform" />
                    <span class="text-sm font-medium text-gray-900">گزارش عملکرد</span>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">خلاصه عملکرد</h3>
            <div class="space-y-4">
                <div class="p-4 bg-gradient-to-r from-sky-50 to-sky-100 rounded-lg border border-sky-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-sky-800">کل تیکت‌های حل شده</h4>
                            <p class="text-lg font-bold text-sky-900">{{ $data['total_tickets'] - $data['open_tickets'] }}</p>
                        </div>
                        <x-heroicon-o-check-circle class="w-8 h-8 text-sky-600" />
                    </div>
                </div>
                
                <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-green-800">نرخ موفقیت</h4>
                            <p class="text-lg font-bold text-green-900">{{ $resolveRate }}%</p>
                        </div>
                        <x-heroicon-o-chart-bar class="w-8 h-8 text-green-600" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Urgent Tickets Alert -->
    @if($data['urgent_tickets'] > 0)
        <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
            <div class="flex items-center">
                <x-heroicon-o-fire class="w-6 h-6 text-red-600 mr-3" />
                <div>
                    <h3 class="text-sm font-medium text-red-800">هشدار: تیکت‌های فوری موجود است</h3>
                    <p class="text-sm text-red-700 mt-1">
                        {{ $data['urgent_tickets'] }} تیکت با اولویت فوری نیاز به رسیدگی فوری دارد.
                    </p>
                </div>
                <div class="mr-auto">
                    <x-filament::button tag="a" href="{{ \App\Filament\Pages\Tickets::getUrl() }}" size="sm" color="danger">
                        رسیدگی فوری
                    </x-filament::button>
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
        
        /* Progress bar animation */
        .bg-green-600 {
            transition: width 0.5s ease-in-out;
        }
        
        /* Enhanced hover states */
        .hover\:bg-sky-50:hover {
            background-color: #eff6ff;
        }
        
        .hover\:bg-red-50:hover {
            background-color: #fef2f2;
        }
        
        .hover\:bg-green-50:hover {
            background-color: #f0fdf4;
        }
    </style>
</x-filament-panels::page> 