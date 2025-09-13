@props(['comment', 'isReply' => false])

<div class="comment-item {{ $isReply ? 'mr-12 p-4 bg-gray-50' : 'p-6 bg-white border border-gray-200' }} rounded-2xl">
    <!-- Comment Header -->
    <div class="flex items-start justify-between mb-3">
        <div class="flex items-center gap-3">
            <!-- Avatar -->
            <div class="w-10 h-10 bg-sky-100 rounded-full flex items-center justify-center">
                <span class="text-sky-600 font-semibold">
                    {{ mb_substr($comment->author_display_name, 0, 1) }}
                </span>
            </div>
            
            <div>
                <div class="flex items-center gap-2">
                    <h4 class="font-semibold text-gray-900">{{ $comment->author_display_name }}</h4>
                    @if($comment->is_admin_reply)
                        <span class="px-2 py-1 bg-sky-100 text-sky-700 text-xs rounded-full">مدیر</span>
                    @endif
                    @if($comment->is_featured)
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">برگزیده</span>
                    @endif
                </div>
                <div class="text-sm text-gray-500">{{ $comment->formatted_date }}</div>
            </div>
        </div>

        <!-- Rating Stars (only for parent comments) -->
        @if($comment->is_parent && $comment->rating)
        <div class="flex items-center">
            @for($i = 1; $i <= 5; $i++)
                @if($i <= $comment->rating)
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                    </svg>
                @else
                    <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                    </svg>
                @endif
            @endfor
        </div>
        @endif
    </div>

    <!-- Comment Content -->
    <div class="text-gray-700 mb-4 leading-relaxed">
        {{ $comment->content }}
    </div>

    <!-- Comment Actions -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Helpful/Unhelpful Votes -->
            <div class="flex items-center gap-2">
                <button class="vote-btn flex items-center gap-1 text-sm text-gray-600 hover:text-sky-600 transition-colors"
                        data-comment-id="{{ $comment->id }}" 
                        data-vote-type="helpful">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                    </svg>
                    <span class="helpful-count">{{ $comment->helpful_count }}</span>
                </button>
                
                <button class="vote-btn flex items-center gap-1 text-sm text-gray-600 hover:text-red-600 transition-colors"
                        data-comment-id="{{ $comment->id }}" 
                        data-vote-type="unhelpful">
                    <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                    </svg>
                    <span class="unhelpful-count">{{ $comment->unhelpful_count }}</span>
                </button>
            </div>

            <!-- Reply Button -->
            @if(!$isReply)
            <button class="reply-btn text-sm text-gray-600 hover:text-sky-600 transition-colors"
                    data-comment-id="{{ $comment->id }}">
                پاسخ دادن
            </button>
            @endif
        </div>

        <!-- Report Button -->
        <button class="text-sm text-gray-400 hover:text-red-600 transition-colors"
                onclick="reportComment({{ $comment->id }})">
            گزارش
        </button>
    </div>

    <!-- Reply Form (Hidden by default) -->
    @if(!$isReply)
    <div id="reply-form-{{ $comment->id }}" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
        <form class="reply-form space-y-3" data-parent-id="{{ $comment->id }}" data-service-id="{{ $comment->service_id }}">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            
            @guest
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input type="text" name="author_name" placeholder="نام شما" required
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                <input type="email" name="author_email" placeholder="ایمیل شما" required
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
            </div>
            @endguest
            
            <textarea name="content" rows="3" placeholder="پاسخ شما..." required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent"></textarea>
            
            <div class="flex justify-end gap-2">
                <button type="button" class="cancel-reply px-4 py-2 text-gray-600 hover:text-gray-800">
                    انصراف
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                    ارسال پاسخ
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Nested Replies -->
    @if($comment->approvedReplies->count() > 0)
    <div class="mt-4 space-y-4">
        @foreach($comment->approvedReplies as $reply)
            @include('components.comment-item', ['comment' => $reply, 'isReply' => true])
        @endforeach
    </div>
    @endif
</div>

@once
@push('scripts')
<script>
function reportComment(commentId) {
    const reason = prompt('لطفاً دلیل گزارش را بنویسید:');
    if (reason && reason.trim()) {
        fetch(`/services/comments/${commentId}/report`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            }
        });
    }
}

// Handle reply forms
document.addEventListener('DOMContentLoaded', function() {
    // Cancel reply button
    document.querySelectorAll('.cancel-reply').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.reply-form').parentElement.classList.add('hidden');
        });
    });

    // Submit reply form
    document.querySelectorAll('.reply-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const serviceId = this.dataset.serviceId;
            
            try {
                const response = await fetch(`/services/${serviceId}/comments`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    this.reset();
                    this.parentElement.classList.add('hidden');
                } else {
                    if (data.errors) {
                        let errorMsg = '';
                        for (let field in data.errors) {
                            errorMsg += data.errors[field].join('\n') + '\n';
                        }
                        alert(errorMsg);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('خطایی در ارسال پاسخ رخ داده است');
            }
        });
    });
});
</script>
@endpush
@endonce