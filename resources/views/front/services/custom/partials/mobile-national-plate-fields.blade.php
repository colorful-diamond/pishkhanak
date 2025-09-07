@php
    $isGuest = !Auth::check();
    $hasPendingService = Session::has('guest_service_data') || Session::has('pending_service_data');
    $showFullForm = !$isGuest || $hasPendingService;
    $serviceData = Session::get('guest_service_data', Session::get('pending_service_data', []));
@endphp

{{-- Always show plate field first --}}
@include('front.services.custom.partials.car-plate-field')

@if($showFullForm)
    {{-- Show additional fields only for logged users or users with pending service data --}}
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
            @if(isset($serviceData['plate_part1']))
                const platePart1 = document.getElementById('plate_part1');
                if (platePart1) platePart1.value = '{{ $serviceData['plate_part1'] }}';
            @endif
            
            @if(isset($serviceData['plate_letter']))
                const plateLetter = document.getElementById('plate_letter');
                if (plateLetter) plateLetter.value = '{{ $serviceData['plate_letter'] }}';
            @endif
            
            @if(isset($serviceData['plate_part2']))
                const platePart2 = document.getElementById('plate_part2');
                if (platePart2) platePart2.value = '{{ $serviceData['plate_part2'] }}';
            @endif
            
            @if(isset($serviceData['plate_part3']))
                const platePart3 = document.getElementById('plate_part3');
                if (platePart3) platePart3.value = '{{ $serviceData['plate_part3'] }}';
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