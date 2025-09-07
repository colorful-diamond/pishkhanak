{{-- Car Plate Display Component (Read-Only) --}}
{{-- 
Usage: @include('front.services.custom.partials.car-plate-display', [
    'plate_part1' => '12',
    'plate_letter' => 'الف', 
    'plate_part2' => '345',
    'plate_part3' => '56',
    'size' => 'normal' // or 'small', 'large'
])
--}}

@php
    $size = $size ?? 'normal';
    $sizeClasses = [
        'small' => [
            'container' => 'p-2',
            'svg_container' => 'p-1',
            'svg' => 'w-2 h-4',
            'part' => 'w-8 h-6 text-xs',
            'letter' => 'w-10 h-6 text-xs'
        ],
        'normal' => [
            'container' => 'p-3',
            'svg_container' => 'p-2',
            'svg' => 'w-3 h-5',
            'part' => 'w-10 h-8 text-sm',
            'letter' => 'w-12 h-8 text-sm'
        ],
        'large' => [
            'container' => 'p-4',
            'svg_container' => 'p-3',
            'svg' => 'w-4 h-7',
            'part' => 'w-12 h-10 text-base',
            'letter' => 'w-14 h-10 text-base'
        ]
    ];
    $classes = $sizeClasses[$size];
@endphp

<div class="inline-flex items-center flex-row-reverse gap-2 {{ $classes['container'] }} bg-gray-50 border-2 border-gray-200 rounded-xl shadow-sm">
    <!-- Iranian Plate SVG -->
    <div class="flex-shrink-0 bg-dark-blue-600 {{ $classes['svg_container'] }}">
        <img src="{{ asset('assets/images/ir-plate.svg') }}" alt="پلاک ایران" class="{{ $classes['svg'] }}">
    </div>
    
    <!-- Plate Part 1 (First 2 digits) -->
    <div class="flex-shrink-0">
        <div class="{{ $classes['part'] }} text-center font-bold border border-gray-400 rounded bg-white flex items-center justify-center">
            {{ $plate_part1 ?? '۱۲' }}
        </div>
    </div>
    
    <!-- Letter -->
    <div class="flex-shrink-0">
        <div class="{{ $classes['letter'] }} text-center font-bold border border-gray-400 rounded bg-white flex items-center justify-center">
            {{ $plate_letter ?? 'الف' }}
        </div>
    </div>
    
    <!-- Plate Part 2 (3 digits) -->
    <div class="flex-shrink-0">
        <div class="{{ $classes['letter'] }} text-center font-bold border border-gray-400 rounded bg-white flex items-center justify-center">
            {{ $plate_part2 ?? '۳۴۵' }}
        </div>
    </div>
    
    <!-- Plate Part 3 (Last 2 digits) -->
    <div class="flex-shrink-0">
        <div class="{{ $classes['part'] }} text-center font-bold border border-gray-400 rounded bg-white flex items-center justify-center">
            {{ $plate_part3 ?? '۵۶' }}
        </div>
    </div>
</div> 