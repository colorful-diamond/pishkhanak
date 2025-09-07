<div class="sidebar w-full lg:w-96 flex-col justify-start items-start gap-6 flex">
    {{-- Search Widget --}}
    <div class="widget search-widget w-full p-6 bg-white rounded-2xl border border-zinc-100 flex-col justify-start items-end gap-4 flex hover:shadow-md transition-all duration-300">
        <div class="text-zinc-800 text-lg font-bold font-['IRANSansWebFaNum'] leading-normal">جستجو در وبلاگ</div>
        <form action="{{ route('app.blog.search') }}" method="GET" class="w-full">
            <div class="search-box self-stretch h-12 bg-white rounded-full flex-col justify-start items-end inline-flex">
                <div class="input-container self-stretch grow shrink basis-0 px-3 py-2 bg-neutral-50 rounded-full border border-zinc-100 justify-end items-center gap-2 inline-flex transition-all duration-300 hover:bg-neutral-100 focus-within:border-sky-400">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="جستجو..." 
                           class="w-full bg-transparent text-right text-zinc-800 text-base font-normal font-['IRANSansWebFaNum'] leading-normal focus:outline-none transition-all duration-300">
                    <button type="submit" aria-label="Search" class="transition-all duration-300 hover:text-sky-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Categories Widget --}}
    <div class="widget categories-widget w-full p-6 bg-white rounded-2xl border border-zinc-100 flex-col justify-start items-end gap-4 flex hover:shadow-md transition-all duration-300">
        <div class="text-zinc-800 text-lg font-bold font-['IRANSansWebFaNum'] leading-normal">دسته‌بندی‌ها</div>
        <div class="categories-list self-stretch flex-col justify-start items-end gap-2 flex">
            @foreach($categories as $category)
            <a href="{{ route('app.blog.category', $category->slug) }}" 
               class="category-item self-stretch px-4 py-3 rounded-lg hover:bg-neutral-50 transition-all duration-300 flex justify-between items-center group">
                <span class="text-sky-400 text-sm font-medium font-['IRANSansWebFaNum'] leading-none group-hover:text-sky-500 transition-colors duration-300">({{ $category->posts_count }})</span>
                <span class="text-zinc-800 text-base font-medium font-['IRANSansWebFaNum'] leading-normal group-hover:text-sky-500 transition-colors duration-300">{{ $category->name }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Popular Posts Widget --}}
    <div class="widget popular-posts-widget w-full p-6 bg-white rounded-2xl border border-zinc-100 flex-col justify-start items-end gap-4 flex hover:shadow-md transition-all duration-300">
        <div class="text-zinc-800 text-lg font-bold font-['IRANSansWebFaNum'] leading-normal">محبوب‌ترین مقالات</div>
        <div class="posts-list self-stretch flex-col justify-start items-end gap-4 flex">
            @foreach($popularPosts as $popularPost)
            <a href="{{ route('app.blog.show', $popularPost->slug) }}" 
               class="post-item w-full justify-end items-start gap-3 inline-flex hover:bg-neutral-50 p-2 rounded-lg transition-all duration-300 group">
                <div class="content grow shrink basis-0 self-stretch flex-col justify-center items-end gap-2 inline-flex">
                    <div class="text-right text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-tight group-hover:text-sky-500 transition-colors duration-300">{{ $popularPost->title }}</div>
                    <div class="details justify-end items-center gap-3 inline-flex">
                        <div class="views justify-end items-center gap-1 flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span class="text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] leading-none">{{ number_format($popularPost->views) }}</span>
                        </div>
                        <div class="date text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] leading-none">{{ \Verta::instance($popularPost->created_at)->format('Y/m/d') }}</div>
                    </div>
                </div>
                @if($popularPost->hasMedia('thumbnail'))
                <div class="thumbnail w-16 h-16 bg-neutral-100 rounded-lg overflow-hidden transform group-hover:scale-105 transition-transform duration-300">
                    <img src="{{ $popularPost->getFirstMediaUrl('thumbnail') }}" alt="{{ $popularPost->title }}" class="w-full h-full object-cover">
                </div>
                @else
                <div class="thumbnail w-16 h-16 bg-gradient-to-br from-sky-100 to-indigo-100 rounded-lg"></div>
                @endif
            </a>
            @endforeach
        </div>
    </div>

    {{-- Recent Posts Widget --}}
    <div class="widget recent-posts-widget w-full p-6 bg-white rounded-2xl border border-zinc-100 flex-col justify-start items-end gap-4 flex hover:shadow-md transition-all duration-300">
        <div class="text-zinc-800 text-lg font-bold font-['IRANSansWebFaNum'] leading-normal">جدیدترین مقالات</div>
        <div class="posts-list self-stretch flex-col justify-start items-end gap-4 flex">
            @foreach($recentPosts as $recentPost)
            <a href="{{ route('app.blog.show', $recentPost->slug) }}" 
               class="post-item w-full justify-end items-start gap-3 inline-flex hover:bg-neutral-50 p-2 rounded-lg transition-all duration-300 group">
                <div class="content grow shrink basis-0 self-stretch flex-col justify-center items-end gap-2 inline-flex">
                    <div class="text-right text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-tight group-hover:text-sky-500 transition-colors duration-300">{{ $recentPost->title }}</div>
                    <div class="date text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] leading-none">{{ \Verta::instance($recentPost->created_at)->format('Y/m/d') }}</div>
                </div>
                @if($recentPost->hasMedia('thumbnail'))
                <div class="thumbnail w-16 h-16 bg-neutral-100 rounded-lg overflow-hidden transform group-hover:scale-105 transition-transform duration-300">
                    <img src="{{ $recentPost->getFirstMediaUrl('thumbnail') }}" alt="{{ $recentPost->title }}" class="w-full h-full object-cover">
                </div>
                @else
                <div class="thumbnail w-16 h-16 bg-gradient-to-br from-sky-100 to-indigo-100 rounded-lg"></div>
                @endif
            </a>
            @endforeach
        </div>
    </div>

    {{-- Tags Cloud Widget --}}
    <div class="widget tags-widget w-full p-6 bg-white rounded-2xl border border-zinc-100 flex-col justify-start items-end gap-4 flex hover:shadow-md transition-all duration-300">
        <div class="text-zinc-800 text-lg font-bold font-['IRANSansWebFaNum'] leading-normal">برچسب‌های محبوب</div>
        <div class="tags-cloud self-stretch flex-wrap justify-end items-center gap-2 flex">
            @foreach($tags as $tag)
            <a href="{{ route('app.blog.tag', $tag->slug) }}" 
               class="tag-item px-3 py-1.5 bg-neutral-100 rounded-full hover:bg-sky-100 transition-all duration-300 group">
                <span class="text-zinc-700 text-sm font-normal font-['IRANSansWebFaNum'] leading-none group-hover:text-sky-600 transition-colors duration-300">{{ $tag->name }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Newsletter Widget --}}
    <div class="widget newsletter-widget w-full p-6 bg-gradient-to-br from-sky-50 to-indigo-50 rounded-2xl border border-sky-100 flex-col justify-start items-end gap-4 flex hover:shadow-md transition-all duration-300">
        <div class="text-zinc-800 text-lg font-bold font-['IRANSansWebFaNum'] leading-normal">عضویت در خبرنامه</div>
        <p class="text-neutral-600 text-sm font-normal font-['IRANSansWebFaNum'] leading-normal text-right">از آخرین مقالات و اخبار ما باخبر شوید</p>
        <form class="w-full flex-col justify-start items-end gap-3 flex">
            <input type="email" placeholder="ایمیل خود را وارد کنید" 
                   class="w-full px-4 py-3 bg-white rounded-lg border border-zinc-200 text-right text-zinc-800 text-sm font-normal font-['IRANSansWebFaNum'] leading-normal focus:outline-none focus:border-sky-400 transition-all duration-300">
            <button type="submit" 
                    class="w-full px-4 py-3 bg-sky-400 rounded-lg hover:bg-sky-500 transition-all duration-300 transform hover:scale-105">
                <span class="text-white text-sm font-medium font-['IRANSansWebFaNum'] leading-none">عضویت در خبرنامه</span>
            </button>
        </form>
    </div>
</div>
