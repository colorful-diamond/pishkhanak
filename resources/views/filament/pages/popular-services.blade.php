<x-filament-panels::page>
    @php
        $services = $this->getPopularServices();
        $stats = $this->getServiceStats();
        $categories = $this->getCategories();
    @endphp

    <!-- Page Header with Filters -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">محبوب‌ترین سرویس‌ها</h1>
                <p class="text-gray-600 mt-1">تحلیل کامل استفاده از سرویس‌ها بر اساس داده‌های واقعی</p>
            </div>
            
            <!-- Filters -->
            <div class="flex flex-wrap gap-3">
                <!-- Period Filter -->
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">دوره زمانی</label>
                    <select wire:model.live="period" 
                            class="rounded-lg border-gray-300 text-sm focus:border-primary-500">
                        <option value="7">7 روز گذشته</option>
                        <option value="30">30 روز گذشته</option>
                        <option value="90">3 ماه گذشته</option>
                        <option value="365">یک سال گذشته</option>
                        <option value="all">همه زمان‌ها</option>
                    </select>
                </div>
                
                <!-- Category Filter -->
                <div>
                    <label class="text-sm text-gray-600 mb-1 block">دسته‌بندی</label>
                    <select wire:model.live="category" 
                            class="rounded-lg border-gray-300 text-sm focus:border-primary-500">
                        <option value="all">همه دسته‌ها</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-blue-700">کل استفاده‌ها</h3>
                    <p class="text-2xl font-bold text-blue-900">{{ number_format($stats['total_usage']) }}</p>
                    <p class="text-xs text-blue-600 mt-1">
                        درخواست: {{ number_format($stats['total_requests']) }} | 
                        نتیجه: {{ number_format($stats['total_results']) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-chart-bar class="w-6 h-6 text-blue-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-green-700">کل درآمد</h3>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($stats['total_revenue']) }}</p>
                    <p class="text-xs text-green-600 mt-1">تومان</p>
                </div>
                <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-banknotes class="w-6 h-6 text-green-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-purple-700">سرویس‌های فعال</h3>
                    <p class="text-2xl font-bold text-purple-900">{{ $stats['services_with_activity'] }}</p>
                    <p class="text-xs text-purple-600 mt-1">از {{ $stats['total_services'] }} سرویس</p>
                </div>
                <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-purple-700" />
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl p-6 border border-orange-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-orange-700">میانگین استفاده</h3>
                    <p class="text-2xl font-bold text-orange-900">{{ number_format($stats['average_per_service']) }}</p>
                    <p class="text-xs text-orange-600 mt-1">به ازای هر سرویس</p>
                </div>
                <div class="w-12 h-12 bg-orange-200 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-calculator class="w-6 h-6 text-orange-700" />
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Services Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">رتبه‌بندی سرویس‌ها</h2>
            <p class="text-sm text-gray-600 mt-1">20 سرویس پراستفاده بر اساس مجموع درخواست‌ها و نتایج</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رتبه</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">سرویس</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">کل استفاده</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">روش ثبت</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">نرخ موفقیت</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">درآمد کل</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">درآمد میانگین</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">نظرات</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($services as $index => $service)
                        <tr class="{{ $index < 3 ? 'bg-yellow-50' : '' }} hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($index < 3)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                        {{ $index === 0 ? 'bg-yellow-400 text-white' : '' }}
                                        {{ $index === 1 ? 'bg-gray-400 text-white' : '' }}
                                        {{ $index === 2 ? 'bg-orange-400 text-white' : '' }}">
                                        {{ $index + 1 }}
                                    </span>
                                @else
                                    <span class="text-gray-600">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $service->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $service->slug }}</div>
                                    @if($service->category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 mt-1">
                                            {{ $service->category->name }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($service->is_maintenance)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                        <x-heroicon-o-wrench class="w-3 h-3 mr-1" />
                                        در حال تعمیر
                                    </span>
                                @elseif($service->status === 'active')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                        فعال
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        غیرفعال
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($service->total_usage) }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($service->request_count > 0)
                                        <span>درخواست: {{ number_format($service->request_count) }}</span>
                                    @endif
                                    @if($service->result_count > 0)
                                        @if($service->request_count > 0) | @endif
                                        <span>نتیجه: {{ number_format($service->result_count) }}</span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($service->tracking_method === 'service_requests')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700">
                                        ServiceRequests
                                    </span>
                                @elseif($service->tracking_method === 'service_results')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">
                                        ServiceResults
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-700">
                                        Both
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $service->success_rate }}%</div>
                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $service->success_rate >= 80 ? 'bg-green-500' : ($service->success_rate >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                             style="width: {{ $service->success_rate }}%"></div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-medium text-gray-900">{{ number_format($service->total_revenue) }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($service->gateway_revenue > 0)
                                        درگاه: {{ number_format($service->gateway_revenue) }}
                                    @endif
                                    @if($service->wallet_revenue > 0)
                                        @if($service->gateway_revenue > 0) | @endif
                                        کیف‌پول: {{ number_format($service->wallet_revenue) }}
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-medium text-gray-900">{{ number_format($service->average_revenue) }}</div>
                                <div class="text-xs text-gray-500">تومان</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    <x-heroicon-o-chat-bubble-left class="w-3 h-3 mr-1" />
                                    0
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                    <a href="{{ route('services.show', $service->slug) }}" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-800" 
                                       title="مشاهده سرویس">
                                        <x-heroicon-o-eye class="w-4 h-4" />
                                    </a>
                                    <a href="{{ \App\Filament\Resources\ServiceResource::getUrl('edit', ['record' => $service]) }}" 
                                       class="text-gray-600 hover:text-gray-800" 
                                       title="ویرایش">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    
                    @if($services->isEmpty())
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                <x-heroicon-o-inbox class="w-12 h-12 mx-auto text-gray-400 mb-3" />
                                <p>هیچ سرویسی در این دوره زمانی استفاده نشده است</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">توزیع درآمد</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">درآمد از درگاه پرداخت</span>
                    <span class="text-sm font-medium text-gray-900">{{ number_format($stats['gateway_revenue']) }} تومان</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">درآمد از کیف‌پول</span>
                    <span class="text-sm font-medium text-gray-900">{{ number_format($stats['wallet_revenue']) }} تومان</span>
                </div>
                <div class="pt-3 border-t">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700">مجموع</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($stats['total_revenue']) }} تومان</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Notes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">توضیحات</h3>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex items-start">
                    <x-heroicon-o-information-circle class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" />
                    <p>سرویس‌هایی که از <span class="font-medium">service_requests</span> استفاده می‌کنند (مانند credit-score-rating) در جدول جداگانه ثبت می‌شوند.</p>
                </div>
                <div class="flex items-start">
                    <x-heroicon-o-information-circle class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" />
                    <p>سرویس‌هایی که از <span class="font-medium">service_results</span> استفاده می‌کنند (مانند loan-inquiry) در جدول نتایج ثبت می‌شوند.</p>
                </div>
                <div class="flex items-start">
                    <x-heroicon-o-information-circle class="w-4 h-4 text-purple-500 mt-0.5 mr-2 flex-shrink-0" />
                    <p>برخی سرویس‌ها ممکن است از هر دو روش استفاده کنند.</p>
                </div>
                <div class="flex items-start">
                    <x-heroicon-o-star class="w-4 h-4 text-yellow-500 mt-0.5 mr-2 flex-shrink-0" />
                    <p>سه سرویس اول با رنگ زرد مشخص شده‌اند.</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>