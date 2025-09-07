@extends('front.layouts.app')

@push('styles')
@vite(['resources/css/service-content.css'])
@endpush

@section('seo')
    <title>{{ $service->meta_title ?? $service->title }}</title>
    <meta name="description" content="{{ $service->meta_description ?? Str::limit(strip_tags($service->summary), 155) }}">
    <meta name="keywords" content="{{ is_array($service->meta_keywords) ? implode(', ', $service->meta_keywords) : $service->meta_keywords }}">
@endsection

@section('content')
<div class="pb-10 pt-4" dir="rtl">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Main Service Card -->
        <div class="flex flex-col md:flex-row items-start gap-12">
            <!-- Form Section (Upper Custom View) -->
            <div class="w-full md:w-1/2">
                @php
                    // Hierarchical view loading for sub-services
                    $upperView = null;
                    
                    if ($service->parent_id && $service->parent) {
                        // For sub-services: try sub-service specific view first
                        $subUpperView = 'front.services.custom.' . $service->parent->slug . '.' . $service->slug . '.upper';
                        if (View::exists($subUpperView)) {
                            $upperView = $subUpperView;
                        } else {
                            // Fallback to parent's upper view
                            $parentUpperView = 'front.services.custom.' . $service->parent->slug . '.upper';
                            if (View::exists($parentUpperView)) {
                                $upperView = $parentUpperView;
                            }
                        }
                    } else {
                        // For parent services: use their own upper view
                        $parentUpperView = 'front.services.custom.' . $service->slug . '.upper';
                        if (View::exists($parentUpperView)) {
                            $upperView = $parentUpperView;
                        }
                    }
                @endphp
                
                @if($upperView)
                    @include($upperView)
                @else
                    <div class="text-right">
                        <h2 class="text-xl font-bold text-dark-sky-600">{{ $service->title }}</h2>
                        <p class="mt-4 text-gray-500">فرم اختصاصی برای این سرویس به زودی ارائه می‌شود.</p>
                    </div>
                @endif
            </div>
            <!-- Image Section -->
            <div class="w-full md:w-1/2 pb-4">
                @if($service->hasMedia('thumbnail'))
                    <img src="{{ $service->getFirstMediaUrl('thumbnail') }}" alt="{{ $service->title }}" class="w-full h-full rounded-2xl bg-sky-100">
                @else
                    <div class="w-full h-full bg-sky-100 rounded-2xl flex items-center justify-center">
                        <p class="text-gray-400">بدون تصویر</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Dynamic Content Section -->
        @if($service->content)
            @if(str_contains($service->content, 'service-content'))
                {!! $service->content !!}
            @else
                <div class="service-content">
                    {!! $service->content !!}
                </div>
            @endif
        @endif

        
        <!-- Lower Custom Section -->
        @php
            // Hierarchical view loading for lower section
            $lowerView = null;
            
            if ($service->parent_id && $service->parent) {
                // For sub-services: try sub-service specific lower view first
                $subLowerView = 'front.services.custom.' . $service->parent->slug . '.' . $service->slug . '.lower';
                if (View::exists($subLowerView)) {
                    $lowerView = $subLowerView;
                } else {
                    // Fallback to parent's lower view
                    $parentLowerView = 'front.services.custom.' . $service->parent->slug . '.lower';
                    if (View::exists($parentLowerView)) {
                        $lowerView = $parentLowerView;
                    }
                }
            } else {
                // For parent services: use their own lower view
                $parentLowerView = 'front.services.custom.' . $service->slug . '.lower';
                if (View::exists($parentLowerView)) {
                    $lowerView = $parentLowerView;
                }
            }
        @endphp
        
        @if($lowerView)
                @include($lowerView)
        @endif

        <!-- FAQs Section (New Design) -->
        @if($service->faqs && count($service->faqs) > 0)
            <div class="bg-white p-6 md:p-8 rounded-3xl shadow-lg">
                <h2 class="text-xl font-bold text-dark-sky-600 text-right mb-6">پرسش‌های متداول</h2>
                <div class="space-y-4">
                    @foreach($service->faqs as $faq)
                        <details class="group rounded-2xl border border-solid border-gray-200 transition-all duration-300 [&[open]]:bg-sky-50 [&[open]]:border-sky-200 hover:bg-sky-50">
                            <summary class="flex justify-between items-center p-4 w-full text-lg font-semibold text-right text-sky-900 list-none cursor-pointer">
                                {{ $faq['question'] }}
                                <div class="text-primary-normal transition-transform duration-300 group-open:rotate-180">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </summary>
                            <div class="px-4 pb-4 text-dark-sky-500 prose max-w-none text-right">
                                {!! $faq['answer'] !!}
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        @endif
        
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/services.js'])
@if(isset($service) && $service->faqs && count($service->faqs) > 0)
<script type="application/ld+json">
@php
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($service->faqs)->map(function ($faq) {
            return [
                '@type' => 'Question',
                'name' => strip_tags($faq['question']),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($faq['answer']),
                ],
            ];
        })->all(),
    ];
    echo json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
@endphp
</script>
@endif
@endpush