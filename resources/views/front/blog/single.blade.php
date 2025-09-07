@extends('front.layouts.app')

@section('content')
<main class="flex flex-col self-center w-full rounded-[32px] max-md:max-w-full max-sm:px-2.5">
    <div class="box-border flex relative flex-col shrink-0 mx-auto w-full max-w-screen-lg">
        <div class="page-content w-full p-6 md:p-10 bg-white rounded-3xl flex-col justify-start items-start gap-6 inline-flex">
            @if($post)
            <div class="article w-96 flex-col justify-center items-end gap-6 inline-flex">
                <div class="articles-label-and-details self-stretch h-24 flex-col justify-start items-end gap-3 flex">
                    <div class="text-center text-zinc-800 text-2xl font-medium font-['IRANSansWebFaNum'] leading-loose">{{ $post->title }}</div>
                    <div class="chips h-6 px-4 py-0.5 bg-yellow-500 rounded-full justify-center items-center gap-2.5 inline-flex">
                        <div class="label text-center text-white text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">{{ $post->category->name }}</div>
                    </div>
                    <div class="details self-stretch px-3 justify-end items-center gap-3 inline-flex">
                        <div class="reading-time justify-start items-center gap-1 flex">
                            <div class="title text-right text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">زمان تقریبی مطالعه {{ $post->reading_time }} دقیقه</div>
                        </div>
                        <div class="comments justify-start items-center gap-1 flex">
                            <div class="title text-right text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">{{ $post->comments_count }}</div>
                        </div>
                        <div class="date justify-end items-center gap-1 flex">
                            <div class="title text-right text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">
                                <span class="text-xs text-gray-500">{{ \Verta::instance($post->created_at)->format('Y/m/d') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <img class="photo w-96 h-80 relative rounded-xl" src="{{ $post->getFirstMediaUrl('default') }}" alt="{{ $post->title }}" />
                <div class="article-text self-stretch h-40 flex-col justify-start items-end gap-4 flex">
                    <div class="self-stretch text-justify text-neutral-500 text-base font-normal font-['IRANSansWebFaNum'] capitalize leading-normal">{{ $post->content }}</div>
                </div>
                @if($post->table_of_contents)
                <div class="table-of-contents self-stretch h-60 p-4 bg-neutral-100 rounded-2xl flex-col justify-center items-end gap-4 flex">
                    <div class="text-zinc-800 text-lg font-medium font-['IRANSansWebFaNum'] leading-normal">فهرست مطالب</div>
                    <div class="w-96 text-justify">{!! $post->table_of_contents !!}</div>
                </div>
                @endif
            </div>

            @if($post->related_services)
            <div class="services self-stretch h-96 p-4 rounded-xl border border-zinc-100 flex-col justify-start items-end gap-6 flex">
                <div class="label justify-end items-center gap-1 inline-flex">
                    <div class="text-center text-zinc-800 text-base font-bold font-['IRANSansWebFaNum'] capitalize leading-normal">خدمات مرتبط</div>
                </div>
                <div class="services self-stretch h-96 flex-col justify-start items-end gap-3 flex">
                    @foreach($post->relatedServices as $service)
                    <div class="cards self-stretch p-4 bg-white rounded-xl border border-neutral-200 justify-end items-center gap-3 inline-flex">
                        <div class="grow shrink basis-0 text-right text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">{{ $service->name }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @endif

            @if($latestArticles)
            <div class="latest-articles self-stretch h-96 p-4 rounded-xl border border-zinc-100 flex-col justify-start items-end gap-6 flex">
                <div class="label justify-end items-center gap-1 inline-flex">
                    <div class="text-center text-zinc-800 text-base font-bold font-['IRANSansWebFaNum'] capitalize leading-normal">جدیدترین مقالات</div>
                </div>
                <div class="articles-cards flex-col justify-start items-end gap-4 flex">
                    @foreach($latestArticles as $article)
                    <div class="post w-64 justify-end items-start gap-3 inline-flex">
                        <div class="content grow shrink basis-0 self-stretch flex-col justify-center items-end gap-2 inline-flex">
                            <div class="active-navigation-item self-stretch text-right text-sky-400 text-xs font-medium font-['IRANSansWebFaNum'] leading-none">{{ $article->title }}</div>
                            <div class="publish-time justify-end items-center gap-1 inline-flex">
                                <div class="title text-right text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] leading-none">{{ \Verta::instance($article->created_at)->format('Y/m/d') }}</div>
                            </div>
                        </div>
                        <div class="photo p-2.5 opacity-80 bg-neutral-100 rounded-lg border border-zinc-100">
                            <img src="{{ $article->getFirstMediaUrl('default') }}" alt="{{ $article->title }}" class="w-full h-full object-cover rounded-lg">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($categories)
            <div class="categories self-stretch h-80 p-4 rounded-xl border border-zinc-100 flex-col justify-start items-end gap-6 flex">
                <div class="label justify-end items-center gap-1 inline-flex">
                    <div class="text-center text-zinc-800 text-base font-bold font-['IRANSansWebFaNum'] capitalize leading-normal">دسته‌بندی‌های مقالات</div>
                </div>
                <div class="categories self-stretch h-56 flex-col justify-start items-end gap-2 flex">
                    @foreach($categories as $category)
                    <div class="category self-stretch h-10 px-2 rounded-lg justify-between items-center inline-flex">
                        <div class="text-center text-zinc-800 text-base font-medium font-['IRANSansWebFaNum'] leading-normal">{{ $category->name }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($faqs)
            <div class="mom-question w-96 p-6 bg-white rounded-3xl justify-end items-center gap-6 inline-flex">
                <div class="faq grow shrink basis-0 flex-col justify-start items-end gap-4 inline-flex">
                    <div class="text-center text-zinc-800 text-xl font-bold font-['IRANSansWebFaNum'] leading-loose">پرسش‌های متداول</div>
                    <div class="faq self-stretch h-96 flex-col justify-start items-end gap-4 flex">
                        @foreach($faqs as $faq)
                        <div class="question self-stretch h-16 px-6 rounded-2xl border border-neutral-200 justify-end items-center gap-6 inline-flex">
                            <div class="faq grow shrink basis-0 h-6 justify-end items-center gap-2 flex">
                                <div class="grow shrink basis-0 text-right text-zinc-800 text-base font-medium font-['IRANSansWebFaNum'] leading-normal">{{ $faq->question }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if($comments)
            <div class="comments h-96 pb-6 bg-white rounded-3xl flex-col justify-start items-start flex">
                <div class="title-and-button self-stretch h-24 px-6 py-2.5 bg-white rounded-3xl justify-between items-center inline-flex">
                    <div class="button pl-4 pr-3 py-3 rounded-lg border border-sky-400 justify-center items-center gap-2 flex">
                        <div class="value text-right text-sky-400 text-lg font-medium font-['IRANSansWebFaNum'] leading-normal">ثبت دیدگاه</div>
                    </div>
                    <div class="text-zinc-800 text-xl font-bold font-['IRANSansWebFaNum'] leading-loose">دیدگاه کاربران</div>
                </div>
                <div class="comments h-96 px-6 flex-col justify-start items-end gap-6 flex">
                    <div class="w-14 text-neutral-500 text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">{{ $comments->count() }} دیدگاه</div>
                    @foreach($comments as $comment)
                    <div class="comment w-96 h-24 flex-col justify-start items-end gap-4 flex">
                        <div class="name-time-comment-text flex-col justify-between items-end flex">
                            <div class="user-name justify-end items-center gap-2 inline-flex">
                                <div class="text-right text-zinc-400 text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">{{ \Verta::instance($comment->created_at)->format('Y/m/d') }}</div>
                                <div class="text-zinc-800 text-sm font-bold font-['IRANSansWebFaNum'] leading-normal">{{ $comment->user->name }}</div>
                            </div>
                            <div class="text-neutral-500 text-sm font-normal font-['IRANSansWebFaNum'] leading-normal">{{ $comment->content }}</div>
                        </div>
                        <div class="like-and-respons justify-end items-center gap-6 inline-flex">
                            <div class="text-zinc-800 text-sm font-normal font-['IRANSansWebFaNum'] leading-normal">پاسخ</div>
                            <div class="like justify-end items-center gap-1 flex">
                                <div class="outline-like-heart w-6 h-6 relative rounded"></div>
                                <div class="text-zinc-800 text-sm font-normal font-['IRANSansWebFaNum'] leading-normal">{{ $comment->likes_count }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($relatedArticles)
            <div class="related-articles self-stretch h-96 p-6 bg-white rounded-3xl flex-col justify-center items-end gap-6 flex">
                <div class="text-zinc-800 text-xl font-bold font-['IRANSansWebFaNum'] leading-loose">مقالات مشابه</div>
                <div class="cards self-stretch h-72 justify-end items-center gap-6 inline-flex">
                    @foreach($relatedArticles as $relatedArticle)
                    <div class="card grow shrink basis-0 pb-3 bg-white rounded-lg flex-col justify-start items-end gap-3 inline-flex">
                        <div class="photo-details self-stretch h-44 flex-col justify-start items-end gap-2 flex">
                            <div class="photo self-stretch h-36 p-2 bg-zinc-100 rounded-lg flex-col justify-end items-start gap-2.5 flex">
                                <div class="chips px-2 py-0.5 bg-yellow-500 rounded-full justify-center items-center gap-2.5 inline-flex">
                                    <div class="label text-center text-white text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">{{ $relatedArticle->category->name }}</div>
                                </div>
                            </div>
                            <div class="details self-stretch h-4 px-4 justify-between items-center inline-flex">
                                <div class="comments justify-start items-center gap-1 flex">
                                    <div class="title text-right text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">{{ $relatedArticle->comments_count }}</div>
                                </div>
                                <div class="publish-time justify-end items-center gap-1 flex">
                                    <div class="title text-right text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">{{ \Verta::instance($relatedArticle->created_at)->format('Y/m/d') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="content self-stretch h-20 px-4 flex-col justify-start items-end gap-3 flex">
                            <div class="content self-stretch text-right text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">{{ $relatedArticle->title }}</div>
                            <a href="{{ route('app.blog.show', $relatedArticle) }}" class="button pl-1 pr-2 py-1 bg-sky-400 rounded-lg justify-center items-center gap-1 inline-flex">
                                <div class="value text-right text-white text-xs font-medium font-['IRANSansWebFaNum'] leading-none">بیشتر بخوانید</div>
                                <div class="arrow-left w-4 h-4 justify-center items-center flex">
                                    <div class="arrow-left w-4 h-4 relative"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <script src="{{ asset('resources/js/blog/single.js') }}"></script>
@endpush