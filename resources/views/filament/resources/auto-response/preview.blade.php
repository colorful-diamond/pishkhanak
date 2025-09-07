<div class="space-y-4 p-4">
    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2">{{ $response->title }}</h3>
        
        <div class="prose dark:prose-invert max-w-none">
            {!! $response->getFormattedResponse([
                'user_name' => 'کاربر گرامی',
                'ticket_number' => 'TKT-2025-000001'
            ]) !!}
        </div>
    </div>

    @if($response->hasLinks())
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
            <h4 class="font-semibold mb-2">لینک‌های مفید:</h4>
            <ul class="space-y-1">
                @foreach($response->links as $link)
                    <li>
                        <a href="{{ $link['url'] }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">
                            {{ $link['title'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($response->hasAttachments())
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
            <h4 class="font-semibold mb-2">فایل‌های پیوست:</h4>
            <ul class="space-y-1">
                @foreach($response->attachments as $attachment)
                    <li class="flex items-center space-x-2 space-x-reverse">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        <span>{{ $attachment['description'] ?? 'فایل پیوست' }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 text-sm">
        <p class="font-semibold">توجه:</p>
        <p>این یک پیش‌نمایش است. متغیرهای {{user_name}} و {{ticket_number}} در پاسخ واقعی با اطلاعات کاربر و تیکت جایگزین خواهند شد.</p>
    </div>
</div>
