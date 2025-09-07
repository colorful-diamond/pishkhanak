@php
    use App\Services\FooterManagerService;
    $footerData = FooterManagerService::getFooterData();
@endphp

<!-- Dynamic Footer -->
<footer dir="rtl" class="bg-sky-50 mt-8 w-full mb-14 md:mb-0">
    <div class="container mx-auto max-w-screen-lg px-4 w-full">
        <!-- Desktop Footer -->
        <section class="hidden md:block py-16">
            <!-- Logo and Company Info - Centered -->
            <div class="text-center mb-12">
                <img src="{{ asset('assets/images/logo.png') }}" alt="{{ $footerData['company_name'] }}" class="w-32 mx-auto mb-6 rounded-lg">
                <p class="text-gray-600 max-w-2xl mx-auto mb-6 leading-relaxed">
                    {{ $footerData['description'] }}
                </p>
                <div class="flex justify-center gap-4">
                    <a href="#" class="bg-white rounded-lg p-3 hover:bg-sky-50 transition-colors shadow-sm">
                        <img src="{{ asset('assets/images/enamad.svg') }}" alt="اینماد" class="w-14 h-14 object-contain">
                    </a>
                    <a href="#" class="bg-white rounded-lg p-3 hover:bg-sky-50 transition-colors shadow-sm">
                        <img src="{{ asset('assets/images/ecunion.svg') }}" alt="اتحادیه" class="w-14 h-14 object-contain">
                    </a>
                </div>
            </div>

            <!-- Services Grid -->
            @if(!empty($footerData['random_services']))
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
                    @foreach($footerData['random_services'] as $serviceGroup)
                        <div class="bg-white rounded-lg p-6 shadow-sm">
                            <h3 class="text-lg font-bold text-sky-900 mb-4">
                                {{ $serviceGroup['title'] }}
                            </h3>
                            @if(!empty($serviceGroup['services']))
                                <ul class="space-y-2 text-sm">
                                    @foreach($serviceGroup['services'] as $service)
                                        <li>
                                            <a href="{{ $service->getUrl() }}" 
                                               class="text-gray-600 hover:text-sky-600 transition-colors">
                                                {{ $service->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Contact Section -->
            <div class="bg-white rounded-lg p-8 shadow-sm text-center">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @if($footerData['phone'] || $footerData['mobile'])
                        <div>
                            <h4 class="font-bold text-sky-900 mb-2">تماس با ما</h4>
                            @if($footerData['phone'])
                                <p class="text-gray-600 text-sm">{{ $footerData['phone'] }}</p>
                            @endif
                            @if($footerData['mobile'])
                                <p class="text-gray-600 text-sm">{{ $footerData['mobile'] }}</p>
                            @endif
                        </div>
                    @endif
                    
                    @if($footerData['address'])
                        <div>
                            <h4 class="font-bold text-sky-900 mb-2">آدرس</h4>
                            <p class="text-gray-600 text-sm">{{ $footerData['address'] }}</p>
                        </div>
                    @endif
                    
                    <div>
                        <h4 class="font-bold text-sky-900 mb-2">دسترسی سریع</h4>
                        <div class="flex justify-center gap-4 text-sm">
                            @foreach(FooterManagerService::getSiteLinks('footer') as $link)
                                <a href="{{ $link->url }}" 
                                   class="text-gray-600 hover:text-sky-600 transition-colors"
                                   {!! \App\Services\FooterManagerService::renderLinkAttributes($link) !!}>
                                    {{ $link->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-200 mt-8 pt-6 text-center">
                @if($footerData['legal_links']->count() > 0)
                    <div class="flex justify-center gap-6 text-sm mb-4">
                        @foreach($footerData['legal_links'] as $key => $value)
                            <a href="{{ $value }}" class="text-gray-600 hover:text-sky-600 transition-colors">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                            </a>
                        @endforeach
                    </div>
                @endif
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} {{ $footerData['company_name'] }} - کلیه حقوق محفوظ است
                </p>
            </div>
        </section>

        <!-- Mobile Footer -->
        <section class="md:hidden py-8">
            <!-- Mobile Logo -->
            <div class="text-center mb-6">
                <img src="{{ asset('assets/images/logo.png') }}" alt="{{ $footerData['company_name'] }}" class="w-24 mx-auto mb-4 rounded-lg">
                <h2 class="text-lg font-bold text-sky-900 mb-2">{{ $footerData['company_name'] }}</h2>
                <p class="text-sm text-gray-600 mb-4">خدمات استعلامی آنلاین</p>
                <div class="flex justify-center gap-3">
                    <a href="#" class="bg-white rounded-lg p-2 hover:bg-sky-50 transition-colors shadow-sm">
                        <img src="{{ asset('assets/images/enamad.svg') }}" alt="اینماد" class="w-12 h-12 object-contain">
                    </a>
                    <a href="#" class="bg-white rounded-lg p-2 hover:bg-sky-50 transition-colors shadow-sm">
                        <img src="{{ asset('assets/images/ecunion.svg') }}" alt="اتحادیه" class="w-12 h-12 object-contain">
                    </a>
                </div>
            </div>

            <!-- Mobile Services Grid -->
            @if(!empty($footerData['random_services']))
                <div class="grid grid-cols-2 gap-4 mb-6">
                    @foreach($footerData['random_services'] as $serviceGroup)
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <h3 class="font-bold text-sky-900 mb-3 text-sm">
                                {{ $serviceGroup['title'] }}
                            </h3>
                            @if(!empty($serviceGroup['services']))
                                <ul class="space-y-2 text-xs">
                                    @foreach(collect($serviceGroup['services'])->take(4) as $service)
                                        <li>
                                            <a href="{{ $service->getUrl() }}" 
                                               class="text-gray-600 hover:text-sky-600 transition-colors">
                                                {{ $service->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Mobile Contact -->
            <div class="bg-white rounded-lg p-4 text-center">
                @if($footerData['phone'])
                    <div class="text-sm text-gray-600 mb-2">
                        تلفن: {{ $footerData['phone'] }}
                    </div>
                @endif
                @if($footerData['address'])
                    <div class="text-sm text-gray-600 mb-4">
                        {{ $footerData['address'] }}
                    </div>
                @endif
                @if($footerData['legal_links']->count() > 0)
                    <div class="flex justify-center gap-4 text-xs mb-3">
                        @foreach($footerData['legal_links']->take(2) as $key => $value)
                            <a href="{{ $value }}" class="text-gray-600">
                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                            </a>
                            @if(!$loop->last)
                                <span class="text-gray-400">|</span>
                            @endif
                        @endforeach
                    </div>
                @endif
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} {{ $footerData['company_name'] }}
                </p>
            </div>
        </section>
    </div>
</footer> 