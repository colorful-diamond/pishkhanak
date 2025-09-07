@if($links->count() > 0)
    <div class="{{ $cssClass }}">
        @foreach($links as $link)
            <a href="{{ $link->url }}" 
               class="text-gray-600 hover:text-sky-600 transition-colors"
               {!! \App\Services\FooterManagerService::renderLinkAttributes($link) !!}>
                @if($link->icon)
                    <i class="{{ $link->icon }} ml-1"></i>
                @endif
                {{ $link->title }}
            </a>
        @endforeach
    </div>
@endif 