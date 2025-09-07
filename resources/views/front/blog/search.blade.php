@extends('front.layouts.app')

@section('content')
<main class="flex flex-col self-center w-full rounded-[32px] max-md:max-w-full max-sm:px-2.5 transition-all duration-300">
    <div class="box-border flex relative flex-col shrink-0 mx-auto w-full max-w-screen-xl transition-all duration-300">
        {{-- Search Header --}}
        <div class="search-header w-full mb-8 px-6 py-12 bg-gradient-to-br from-sky-50 to-indigo-50 rounded-3xl">
            <nav class="breadcrumb mb-6">
                <ol class="flex items-center gap-2 text-sm text-neutral-600">
                    <li><a href="{{ route('app.page.home') }}" class="hover:text-sky-500 transition-colors duration-300">خانه</a></li>
                    <li><span class="text-neutral-400">/</span></li>
                    <li><a href="{{ route('app.blog.index') }}" class="hover:text-sky-500 transition-colors duration-300">وبلاگ</a></li>
                    <li><span class="text-neutral-400">/</span></li>
                    <li class="text-zinc-800 font-medium">جستجو</li>
                </ol>
            </nav>
            
            <div class="flex items-center gap-3 mb-4">
                <svg class="w-8 h-8 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h1 class="text-2xl md:text-3xl font-bold text-zinc-800 font-['IRANSansWebFaNum']">
                    نتایج جستجو برای: <span class="text-sky-500">"{{ $searchTerm }}"</span>
                </h1>
            </div>
            
            <p class="text-lg text-neutral-600 font-['IRANSansWebFaNum'] mb-6">
                {{ $posts->total() }} نتیجه یافت شد
            </p>

            {{-- Search Form --}}
            <form action="{{ route('app.blog.search') }}" method="GET" class="search-form max-w-2xl">
                <div class="relative">
                    <input type="text" name="q" value="{{ $searchTerm }}" 
                           placeholder="جستجوی مجدد..." 
                           class="w-full px-4 py-3 pr-12 bg-white rounded-xl border border-neutral-200 text-right text-zinc-800 text-base font-normal font-['IRANSansWebFaNum'] leading-normal focus:outline-none focus:border-sky-400 transition-all duration-300 shadow-sm">
                    <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-sky-400 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Main Content Area --}}
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Search Results --}}
            <div class="flex-1">
                @if($posts->count() > 0)
                    {{-- Results Info --}}
                    <div class="results-info mb-6 p-4 bg-white rounded-xl border border-neutral-200">
                        <p class="text-neutral-600 font-['IRANSansWebFaNum']">
                            نمایش {{ $posts->firstItem() }} تا {{ $posts->lastItem() }} از {{ $posts->total() }} نتیجه
                        </p>
                    </div>

                    <div class="posts-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($posts as $post)
                        <article class="post-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 group">
                            <div class="thumbnail relative h-48 bg-gradient-to-br from-sky-100 to-indigo-100 overflow-hidden">
                                @if ($post->hasMedia('thumbnail'))
                                    <img src="{{ $post->getFirstMediaUrl('thumbnail') }}" 
                                         alt="{{ $post->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-20 h-20 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="absolute top-3 right-3">
                                    <span class="px-3 py-1 bg-yellow-500 text-white text-xs font-medium font-['IRANSansWebFaNum'] rounded-full">
                                        {{ $post->category->name }}
                                    </span>
                                </div>
                            </div>
                            <div class="content p-6 flex flex-col gap-4">
                                <h2 class="title text-zinc-800 text-lg font-bold font-['IRANSansWebFaNum'] leading-normal line-clamp-2 group-hover:text-sky-500 transition-colors duration-300">
                                    @php
                                        $highlightedTitle = str_ireplace(
                                            $searchTerm, 
                                            '<mark class="bg-yellow-200 text-zinc-800">' . $searchTerm . '</mark>', 
                                            $post->title
                                        );
                                    @endphp
                                    {!! $highlightedTitle !!}
                                </h2>
                                @if($post->summary)
                                <p class="summary text-neutral-600 text-sm font-normal font-['IRANSansWebFaNum'] leading-normal line-clamp-3">
                                    @php
                                        $highlightedSummary = str_ireplace(
                                            $searchTerm, 
                                            '<mark class="bg-yellow-200 text-zinc-800">' . $searchTerm . '</mark>', 
                                            $post->summary
                                        );
                                    @endphp
                                    {!! $highlightedSummary !!}
                                </p>
                                @endif
                                <div class="meta flex justify-between items-center text-neutral-500 text-xs">
                                    <div class="flex items-center gap-3">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            {{ number_format($post->views) }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            {{ $post->comments_count ?? 0 }}
                                        </span>
                                    </div>
                                    <span class="date">{{ \Verta::instance($post->published_at)->format('Y/m/d') }}</span>
                                </div>
                                <a href="{{ route('app.blog.show', $post->slug) }}" 
                                   class="read-more inline-flex items-center gap-2 text-sky-500 hover:text-sky-600 transition-colors duration-300 font-medium font-['IRANSansWebFaNum'] group">
                                    مطالعه مقاله
                                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </a>
                            </div>
                        </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="pagination mt-12">
                        {{ $posts->appends(['q' => $searchTerm])->links('vendor.pagination.tailwind') }}
                    </div>
                @else
                    <div class="no-results bg-white rounded-3xl p-12 text-center">
                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 12h.01M12 12h-.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-800 mb-2 font-['IRANSansWebFaNum']">هیچ نتیجه‌ای یافت نشد</h3>
                        <p class="text-gray-600 font-['IRANSansWebFaNum'] mb-6">متاسفانه مقاله‌ای با عبارت "{{ $searchTerm }}" یافت نشد.</p>
                        
                        {{-- Search Suggestions --}}
                        <div class="suggestions mt-8 text-right">
                            <h4 class="text-base font-medium text-gray-700 mb-3 font-['IRANSansWebFaNum']">پیشنهادات:</h4>
                            <ul class="list-disc list-inside text-gray-600 font-['IRANSansWebFaNum'] space-y-2 text-sm">
                                <li>از نوشتار صحیح کلمات اطمینان حاصل کنید</li>
                                <li>از کلمات کلیدی متفاوت استفاده کنید</li>
                                <li>از کلمات عمومی‌تر استفاده کنید</li>
                                <li>تعداد کلمات جستجو را کاهش دهید</li>
                            </ul>
                        </div>
                        
                        <a href="{{ route('app.blog.index') }}" class="inline-flex items-center gap-2 mt-8 text-sky-500 hover:text-sky-600 transition-colors duration-300 font-medium font-['IRANSansWebFaNum']">
                            مشاهده همه مقالات
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            @include('front.blog.partials.sidebar')
        </div>
    </div>
</main>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    mark {
        background-color: #fef3c7;
        color: #27272a;
        padding: 0 2px;
        border-radius: 2px;
    }
</style>
@endpush
