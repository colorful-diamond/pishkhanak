@extends('front.layouts.app')

@section('seo')
    <title>دسته بندی خدمات: {{ $category->name }}</title>
    <meta name="description" content="لیست خدمات ارائه شده در دسته بندی {{ $category->name }} در پیشخوانک.">
@endsection

@section('content')
<div class="bg-sky-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-dark-sky-600">دسته بندی: {{ $category->name }}</h1>
            @if($category->description)
                <p class="mt-4 text-lg text-dark-sky-500 max-w-2xl mx-auto">{{ $category->description }}</p>
            @endif
        </div>

        <!-- Services Grid -->
        @if($services->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($services as $service)
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden flex flex-col text-right">
                        <a href="{{ $service->getUrl() }}">
                            <img src="{{ $service->getfirstMediaUrl('thumbnail', 'thumb') }}" alt="{{ $service->title }}" class="w-full h-48 object-cover">
                        </a>
                        <div class="p-6 flex flex-col flex-grow">
                            <h2 class="text-xl font-bold text-dark-sky-600">
                                <a href="{{ $service->getUrl() }}" class="hover:text-primary-normal">{{ $service->getDisplayTitle() }}</a>
                            </h2>
                            <div class="mt-3 text-dark-sky-500 prose-sm max-w-none flex-grow">
                                {!! Str::limit(strip_tags($service->summary), 120) !!}
                            </div>
                            <div class="mt-6 flex justify-between items-center">
                                <a href="{{ $service->getUrl() }}" class="text-primary-normal font-semibold hover:text-primary-dark">ادامه و مشاهده <i class="fas fa-arrow-left mr-2"></i></a>
                                <span class="text-lg font-bold text-gray-800">{{ $service->price ? number_format($service->price) . ' ت' : 'رایگان' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-12">
                {{ $services->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-3xl shadow-lg">
                <p class="text-xl text-gray-500">در حال حاضر هیچ سرویسی در این دسته بندی وجود ندارد.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
@endpush
