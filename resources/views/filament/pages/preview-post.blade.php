<x-filament-panels::page>
    <div class="prose dark:prose-invert max-w-none">
        <div class="space-y-4">
            {{-- Post Header --}}
            <div class="space-y-2">
                <h1 class="text-3xl font-bold">{{ $post->title }}</h1>
                
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <span>{{ $post->author?->name }}</span>
                    <span>•</span>
                    <span>{{ $post->published_at ? \Verta::instance($post->published_at)->format('Y/m/d') : 'Draft' }}</span>
                    @if($post->category)
                        <span>•</span>
                        <span>{{ $post->category->name }}</span>
                    @endif
                </div>

                @if($post->tags->count())
                    <div class="flex flex-wrap gap-2">
                        @foreach($post->tags as $tag)
                            <span class="px-2 py-1 text-xs bg-primary-50 text-dark-sky-500 rounded-full">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Featured Image --}}
            @if($post->getFirstMediaUrl('thumbnail'))
                <img 
                    src="{{ $post->getFirstMediaUrl('thumbnail') }}" 
                    alt="{{ $post->title }}"
                    class="w-full rounded-lg shadow-lg"
                >
            @endif

            {{-- Content --}}
            <div class="mt-8">
                {!! $post->content !!}
            </div>

            {{-- Meta Information --}}
            <div class="mt-8 p-4 bg-sky-50 dark:bg-gray-800 rounded-lg">
                <h3 class="text-lg font-semibold mb-2">Meta Information</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="font-medium">Status</dt>
                        <dd>{{ ucfirst($post->status) }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">Views</dt>
                        <dd>{{ $post->views }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">Likes</dt>
                        <dd>{{ $post->likes }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium">Shares</dt>
                        <dd>{{ $post->shares }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-filament-panels::page>
