@extends('front.services.custom.upper-base')

@php
    // Check if this is a bank-specific service
    $currentSlug2 = request()->route('slug2');
    $bankSpecific = !empty($currentSlug2);
    
    // Get bank information if bank-specific
    $bankInfo = null;
    if ($bankSpecific) {
        $bankService = app(\App\Services\BankService::class);
        $bankInfo = $bankService->getBankBySlug($currentSlug2);
    }
@endphp

@section('submit_text')
    @if($bankSpecific && $bankInfo)
        استعلام وام {{ $bankInfo['fa_name'] }}
    @else
        استعلام وام و تسهیلات
    @endif
@endsection

@section('form_action')
    @php
        // Get current route parameters to preserve bank-specific routing
        $currentSlug1 = request()->route('slug1');
        $currentSlug2 = request()->route('slug2');
        
        // Build the correct form action based on current URL
        if ($currentSlug2) {
            // Bank-specific service URL: /services/loan-inquiry/melli
            echo route('services.submit', ['slug1' => $currentSlug1, 'slug2' => $currentSlug2]);
        } else {
            // Main service URL: /services/loan-inquiry
            echo route('services.submit', ['slug1' => $currentSlug1 ?? 'loan-inquiry']);
        }
    @endphp
@endsection

@section('form_fields')
    {{-- Bank Information Section --}}
    <!-- @if($bankSpecific && $bankInfo)
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-6 mb-6">
            <div class="flex items-center justify-center mb-4">
                <div class="flex items-center space-x-4 space-x-reverse">
                    @if($bankInfo['logo'])
                        <img src="{{ $bankInfo['logo'] }}" alt="{{ $bankInfo['fa_name'] }}" class="w-16 h-16 rounded-lg shadow-md">
                    @endif
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900">{{ $bankInfo['fa_name'] }}</h3>
                        <p class="text-sm text-gray-600">استعلام وام و تسهیلات</p>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <p class="text-sm text-blue-800 font-medium">اطلاعات تسهیلات شما در {{ $bankInfo['fa_name'] }} نمایش داده می‌شود</p>
            </div>
        </div>
    @endif -->

    @include('front.services.custom.partials.national-code-field')
    @include('front.services.custom.partials.mobile-field')
    
@endsection

@section('bank_slider_section')
    @if(isset($banks) && count($banks) > 0)
        @include('front.components.bank-slider', ['banks' => $banks, 'serviceSlug' => 'loan-inquiry'])
    @endif
@endsection 