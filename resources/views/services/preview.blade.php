@extends('front.layouts.app')

@push('styles')
@vite(['resources/css/service-content.css'])
@endpush

@section('content')
<div class="container mx-auto px-4 py-4">
    <!-- Error Messages (Top of Page) -->
    @if(session('error'))
        <div class="mb-6 animate-slide-down">
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="mr-3 flex-1">
                        <h3 class="text-sm font-semibold text-red-800">خطا در پردازش پرداخت</h3>
                        <p class="mt-1 text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="mr-auto flex-shrink-0 text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Messages (Top of Page) -->
    @if(session('success'))
        <div class="mb-6 animate-slide-down">
            <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="mr-3 flex-1">
                        <h3 class="text-sm font-semibold text-green-800">عملیات موفق</h3>
                        <p class="mt-1 text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="mr-auto flex-shrink-0 text-green-400 hover:text-green-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Main Content - Table Section -->
        <div class="lg:col-span-2">
            @include('services.partials.preview-table', [
                'service' => $service,
                'requestDetails' => $requestDetails ?? [],
                'previewData' => $previewData ?? [],
            ])
        </div>

        <!-- Sidebar - Form Section -->
        <div class="lg:col-span-1">
            @include('services.partials.preview-form', [
                'service' => $service,
                'gateways' => $gateways ?? collect(),
                'requestHash' => $requestHash ?? null,
                'sessionKey' => $sessionKey ?? null,
                'user' => $user ?? null,
                'shortfall' => $shortfall ?? null,
                'suggestedAmount' => $suggestedAmount ?? null,
            ])
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-down {
    animation: slideDown 0.3s ease-out;
}
</style>
@endpush
@endsection

 