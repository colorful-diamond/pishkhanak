@php
    $isGuest = !Auth::check();
    $hasPendingService = Session::has('guest_service_data') || Session::has('pending_service_data');
    $showFullForm = !$isGuest || $hasPendingService;
    $serviceData = Session::get('guest_service_data', Session::get('pending_service_data', []));
@endphp

@if($showFullForm)
    {{-- Show fields only for logged users or users with pending service data --}}
    @include('front.services.custom.partials.license-number-field')

    <div class="mt-4">
        @include('front.services.custom.partials.mobile-field')
    </div>

    <div class="mt-4">
        @include('front.services.custom.partials.national-code-field')
    </div>
@else
    {{-- For guest users, show info message --}}

@endif

{{-- Pre-fill fields if data exists --}}
@if($showFullForm && !empty($serviceData))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pre-fill form fields with stored data
            @if(isset($serviceData['license_number']))
                const licenseNumber = document.getElementById('license_number');
                if (licenseNumber) licenseNumber.value = '{{ $serviceData['license_number'] }}';
            @endif
            
            @if(isset($serviceData['mobile']))
                const mobile = document.getElementById('mobile');
                if (mobile) mobile.value = '{{ $serviceData['mobile'] }}';
            @endif
            
            @if(isset($serviceData['national_code']))
                const nationalCode = document.getElementById('national_code');
                if (nationalCode) nationalCode.value = '{{ $serviceData['national_code'] }}';
            @endif
        });
    </script>
@endif 