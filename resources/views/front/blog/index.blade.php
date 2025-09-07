@extends('front.layouts.app')

@section('content')
<main class="flex flex-col self-center w-full rounded-[32px] max-md:max-w-full max-sm:px-2.5 transition-all duration-300">
    <div class="box-border flex relative flex-col shrink-0 mx-auto w-full max-w-screen-lg transition-all duration-300">
        <div class="page-content w-full p-6 md:p-10 bg-white rounded-3xl flex-col justify-start items-start gap-6 inline-flex transition-all duration-300">
            <div class="banner w-full h-auto md:h-56 px-4 md:px-10 py-6 md:py-2.5 bg-neutral-100 rounded-xl flex flex-col md:flex-row justify-between items-center gap-6 md:gap-12 transition-all duration-300 hover:bg-neutral-200">
                <div class="content flex-col justify-start items-end gap-4 inline-flex transition-all duration-300">
                    <div class="title flex-col justify-center items-end gap-3 flex transition-all duration-300">
                        <h1 class="text-right text-zinc-800 text-2xl md:text-3xl font-medium font-['IRANSansWebFaNum'] leading-tight transition-all duration-300 hover:text-zinc-900">در حوزه استعلامات<br/>بروزترین اخبار و مقالات</h1>
                    </div>
                    <p class="text-right text-neutral-500 text-base md:text-lg font-normal font-['IRANSansWebFaNum'] leading-normal transition-all duration-300 hover:text-neutral-600">است. گرافیک طراحان با استفاده از<br/>نامفهوم سادگی تولید با متن ساختگی ایپسوم لورم</p>
                </div>
                <div class="photo w-full md:w-1/2 h-40 bg-white rounded-xl overflow-hidden transition-all duration-300 transform hover:scale-105">
                    <img src="{{ asset('path/to/your/image.jpg') }}" alt="Blog Banner" class="w-full h-full object-cover transition-all duration-300">
                </div>
            </div>

            <form action="{{ route('app.blog.index') }}" method="GET" class="search-categories self-stretch justify-end items-start gap-2.5 flex flex-col md:flex-row transition-all duration-300">
                <div class="search w-full md:w-96 h-12 bg-white rounded-full flex-col justify-start items-end inline-flex transition-all duration-300 hover:shadow-md">
                    <div class="text-input self-stretch grow shrink basis-0 flex-col justify-start items-end gap-2 flex transition-all duration-300">
                        <div class="input-container self-stretch grow shrink basis-0 px-3 py-2 bg-white rounded-full border border-zinc-100 justify-end items-center gap-2 inline-flex transition-all duration-300 focus-within:border-sky-400">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="جستجوی مقالات" class="w-full bg-transparent text-right text-zinc-800 text-base font-normal font-['IRANSansWebFaNum'] leading-normal focus:outline-none transition-all duration-300">
                            <button type="submit" aria-label="Search" class="transition-all duration-300 hover:text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="dropdown grow shrink basis-0 flex-col justify-start items-end gap-2 inline-flex w-full md:w-auto transition-all duration-300">
                    <div class="input-container self-stretch h-12 px-3 py-1 bg-white rounded-full border border-zinc-100 justify-end items-center gap-2 inline-flex transition-all duration-300 focus-within:border-sky-400">
                        <select name="sort" class="w-full bg-transparent text-right text-zinc-800 text-base font-normal font-['IRANSansWebFaNum'] leading-normal focus:outline-none transition-all duration-300">
                            <option value="" disabled selected>نحوه ترتیب</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>جدیدترین مقالات</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>محبوب‌ترین مقالات</option>
                        </select>
                    </div>
                </div>
                <div class="categories grow shrink basis-0 flex-col justify-start items-end gap-2 inline-flex w-full md:w-auto transition-all duration-300">
                    <div class="input-container self-stretch h-12 px-3 py-1 bg-white rounded-full border border-zinc-100 justify-end items-center gap-2 inline-flex transition-all duration-300 focus-within:border-sky-400">
                        <select name="category" class="w-full bg-transparent text-right text-zinc-800 text-base font-normal font-['IRANSansWebFaNum'] leading-normal focus:outline-none transition-all duration-300">
                            <option value="" selected>همه دسته بندی ها</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            <div id="blogPosts" class="content self-stretch flex-col justify-start items-start gap-6 flex transition-all duration-300">
                @foreach($posts as $post)
                <div class="card w-full md:w-56 pb-3 bg-white rounded-lg flex-col justify-start items-end gap-3 inline-flex mb-6 md:mb-0 shadow-sm hover:shadow-md transition-shadow duration-300 transform hover:-translate-y-1 hover:scale-105">
                
                    <div class="photo-details self-stretch h-44 flex-col justify-start items-end gap-2 flex">
                        <div class="photo self-stretch h-36 p-2 bg-zinc-100 rounded-lg flex-col justify-end items-start gap-2.5 flex">
                            @if ($post->hasMedia('default'))
                                <img src="{{ $post->getFirstMediaUrl('default') }}" alt="{{ $post->title }}" class="w-full h-full object-cover rounded-lg transition-all duration-300 transform hover:scale-105">
                            @endif
                            <div class="chips px-2 py-0.5 bg-yellow-500 rounded-full justify-center items-center gap-2.5 inline-flex">
                                <div class="label text-center text-white text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">{{ $post->category->name }}</div>
                            </div>
                        </div>
                        <div class="details self-stretch h-4 pt-3 pb-1 justify-between items-center inline-flex">
                            <div class="comments justify-start items-center gap-1 flex">
                                @svg('heroicon-o-chat-bubble-left-right'  , 'w-4 h-4 text-neutral-500')
                                <div class="title text-right text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">{{ $post->comments_count }}</div>
                            </div>
                            <div class="publish-time justify-end items-center gap-1 flex">
                                @svg('heroicon-o-clock'  , 'w-4 h-4 text-neutral-500')
                                <div class="title text-right text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">
                                    <span class="text-xs text-gray-500">{{ \Verta::instance($post->created_at)->format('Y/m/d') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content self-stretch h-20 px-4 flex-col justify-start items-end gap-3 flex">
                        <div class="content self-stretch text-right text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">{{ $post->title }}</div>
                        <a href="{{ route('app.blog.show', $post) }}" class="button pl-1 pr-2 py-1 bg-sky-400 rounded-lg justify-center items-center gap-1 inline-flex hover:bg-sky-500 transition-colors duration-300 transform hover:scale-105">

                            <span class="value text-right text-white text-xs font-medium font-['IRANSansWebFaNum'] leading-none">ادامه مقاله</span>
                            @svg('heroicon-o-arrow-left'  , 'w-4 h-4 text-white')
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="load-more self-stretch h-20 flex-col justify-center items-center gap-6 flex">
                <div class="divider w-full h-px origin-top-left rotate-180 border border-zinc-100"></div>

            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.querySelector('select[name="category"]');
        categorySelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
    </script>
@endpush