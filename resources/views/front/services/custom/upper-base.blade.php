<div class="text-right">
    
    <!-- Service Loading Overlay -->
    <div id="serviceLoadingOverlay" class="service-loading-overlay">
        <div class="loading-backdrop"></div>
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">در حال ارسال درخواست ...</div>
            <div class="loading-subtext">لطفاً منتظر بمانید</div>
        </div>
    </div>
    
    <!-- Service Title -->
    <div class="flex items-center justify-center gap-3 mb-6">
        <h1 class="text-xl font-bold text-gray-900">{{ $service->title }}</h1>
    </div>

    <!-- Service Pricing -->
    @if($service->is_paid && $service->price > 0)
        <div class="mb-6 p-4 bg-sky-50 border border-sky-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="text-right">
                    <div class="text-sm text-gray-600">هزینه سرویس:</div>
                    <div class="text-lg font-bold text-sky-600">{{ number_format($service->price) }} تومان</div>
                </div>
                <div class="text-left">
                    <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @if(!Auth::check())
                <div class="mt-2 text-xs text-gray-500 text-center">
                    برای استفاده از این سرویس ابتدا وارد شوید یا ثبت‌نام کنید
                </div>
            @elseif(Auth::user()->balance < $service->price)
                <div class="mt-2 text-xs text-orange-600 text-center">
                    موجودی کیف‌پول شما کافی نیست. ابتدا کیف‌پول خود را شارژ کنید.
                </div>
            @else
                <div class="mt-2 text-xs text-green-600 text-center">
                    موجودی کیف‌پول شما کافی است
                </div>
            @endif
        </div>
    @endif

    <!-- Form Content -->
    <div class="space-y-6">
        <form method="POST" action="@yield('form_action', url()->current())" class="space-y-4">
            @csrf
            
            @yield('form_fields')

            @php
                $isGuest = !Auth::check();
                $hasPendingService = Session::has('guest_service_data') || Session::has('pending_service_data');
                $showFullForm = !$isGuest || $hasPendingService;
                
                // Check if this is a service that requires user authentication first (no plate preview)
                $authRequiredServices = ['negative-license-score', 'driving-license-status'];
                $currentSlug = request()->route('slug1') ?? '';
                $requiresAuthFirst = in_array($currentSlug, $authRequiredServices);
            @endphp

            {{-- Submit Button Logic --}}
            @if($requiresAuthFirst && $isGuest && !$hasPendingService)
                {{-- For services that require auth first and guest has no pending data, show login button --}}
                <button type="button" onclick="window.location.href='{{ route('login') }}'" class="w-full px-4 py-3 bg-primary-normal text-white font-medium rounded-lg hover:bg-primary-dark transition-colors">
                    ورود / ثبت نام برای ادامه
                </button>
            @else
                {{-- Always show submit button for other cases - guests need it to submit plate and go to preview --}}
                <button type="submit" class="w-full px-4 py-3 bg-primary-normal text-white font-medium rounded-lg hover:bg-primary-dark transition-colors">
                    @if($isGuest && !$hasPendingService)
                        دریافت نتیجه
                    @else
                        @yield('submit_text', 'ارسال')
                    @endif
                </button>
            @endif
        </form>
    </div>

    <!-- Laravel Validation Errors -->
    @if ($errors->any())
        <div class="mt-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Success Message -->
    @if (session('success'))
        <div class="mt-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg text-sm">
            {{ session('message') }}
        </div>
    @endif

    <!-- Results Container -->
    @yield('results_section')

    @yield('additional_content')
    
    <!-- Other Services Section -->
    @yield('other_services_section')
    
    <!-- Bank Slider Section -->
    @yield('bank_slider_section')
</div> 