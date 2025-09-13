@props(['service'])

<div class="bg-white p-6 md:p-8 rounded-3xl shadow-lg" id="comments-section" dir="rtl">
    <!-- Comments Header with Rating Summary -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-dark-sky-600 mb-4">نظرات و امتیازات</h2>
        
        @if($service->total_ratings > 0)
        <div class="flex items-center gap-6 p-4 bg-sky-50 rounded-2xl">
            <div class="text-center">
                <div class="text-3xl font-bold text-sky-900">{{ number_format($service->average_rating, 1) }}</div>
                <div class="flex items-center justify-center my-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($service->average_rating))
                            <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @elseif($i - 0.5 <= $service->average_rating)
                            <svg class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20">
                                <defs>
                                    <linearGradient id="half-star">
                                        <stop offset="50%" stop-color="currentColor"/>
                                        <stop offset="50%" stop-color="#e5e7eb"/>
                                    </linearGradient>
                                </defs>
                                <path fill="url(#half-star)" d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @endif
                    @endfor
                </div>
                <div class="text-sm text-gray-600">{{ $service->total_ratings }} نظر</div>
            </div>
            
            <!-- Rating Distribution -->
            <div class="flex-1">
                @php
                    $ratingDistribution = [];
                    for($i = 5; $i >= 1; $i--) {
                        $count = $service->serviceComments()
                            ->where('status', 'approved')
                            ->where('rating', $i)
                            ->count();
                        $percentage = $service->total_ratings > 0 ? ($count / $service->total_ratings) * 100 : 0;
                        $ratingDistribution[$i] = ['count' => $count, 'percentage' => $percentage];
                    }
                @endphp
                
                @foreach($ratingDistribution as $rating => $data)
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm text-gray-600 w-3">{{ $rating }}</span>
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                    </svg>
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
                    </div>
                    <span class="text-sm text-gray-600 w-8 text-left">{{ $data['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Add Comment Form -->
    <div class="mb-8 p-6 bg-gray-50 rounded-2xl">
        <h3 class="text-lg font-semibold text-dark-sky-600 mb-4">نظر خود را بنویسید</h3>
        
        <form id="comment-form" data-service-id="{{ $service->id }}" class="space-y-4">
            @csrf
            
            @guest
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="author_name" class="block text-sm font-medium text-gray-700 mb-1">نام *</label>
                    <input type="text" id="author_name" name="author_name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                </div>
                <div>
                    <label for="author_email" class="block text-sm font-medium text-gray-700 mb-1">ایمیل *</label>
                    <input type="email" id="author_email" name="author_email" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                </div>
                <div>
                    <label for="author_phone" class="block text-sm font-medium text-gray-700 mb-1">تلفن</label>
                    <input type="tel" id="author_phone" name="author_phone"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                </div>
            </div>
            @endguest

            <!-- Rating Stars -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">امتیاز شما</label>
                <div class="flex items-center gap-2" id="rating-stars">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="star-rating" data-rating="{{ $i }}">
                        <svg class="w-8 h-8 text-gray-300 hover:text-yellow-400 transition-colors fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                    </button>
                    @endfor
                    <input type="hidden" name="rating" id="rating-input" value="0">
                </div>
            </div>

            <!-- Comment Text -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">نظر شما *</label>
                <textarea id="content" name="content" rows="4" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                          placeholder="نظر خود را در مورد این خدمت بنویسید..."></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <button type="submit" 
                        class="px-6 py-3 bg-sky-600 text-white font-medium rounded-lg hover:bg-sky-700 transition-colors">
                    ارسال نظر
                </button>
                <span class="text-sm text-gray-600">نظر شما پس از بررسی منتشر خواهد شد</span>
            </div>
        </form>
    </div>

    <!-- Comments List -->
    <div id="comments-list" class="space-y-6">
        @php
            $comments = $service->approvedComments()->paginate(10);
        @endphp
        
        @forelse($comments as $comment)
            @include('components.comment-item', ['comment' => $comment])
        @empty
            <div class="text-center py-8 text-gray-500">
                هنوز نظری ثبت نشده است. اولین نفری باشید که نظر می‌دهید!
            </div>
        @endforelse

        @if($comments->hasPages())
        <div class="mt-6">
            {{ $comments->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Rating stars interaction
    const stars = document.querySelectorAll('.star-rating');
    const ratingInput = document.getElementById('rating-input');
    
    stars.forEach((star, index) => {
        star.addEventListener('click', function(e) {
            e.preventDefault();
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            
            // Update star colors
            stars.forEach((s, i) => {
                if (i < rating) {
                    s.querySelector('svg').classList.remove('text-gray-300');
                    s.querySelector('svg').classList.add('text-yellow-400');
                } else {
                    s.querySelector('svg').classList.remove('text-yellow-400');
                    s.querySelector('svg').classList.add('text-gray-300');
                }
            });
        });
    });

    // Comment form submission
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', async function(e) {
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
                    // Show success message
                    alert(data.message);
                    // Reset form
                    this.reset();
                    // Reset stars
                    stars.forEach(s => {
                        s.querySelector('svg').classList.remove('text-yellow-400');
                        s.querySelector('svg').classList.add('text-gray-300');
                    });
                    ratingInput.value = 0;
                } else {
                    // Show error messages
                    if (data.errors) {
                        let errorMsg = '';
                        for (let field in data.errors) {
                            errorMsg += data.errors[field].join('\n') + '\n';
                        }
                        alert(errorMsg);
                    } else {
                        alert(data.message || 'خطایی رخ داده است');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('خطایی در ارسال نظر رخ داده است');
            }
        });
    }

    // Vote buttons
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.vote-btn')) {
            const btn = e.target.closest('.vote-btn');
            const commentId = btn.dataset.commentId;
            const voteType = btn.dataset.voteType;

            try {
                const response = await fetch(`/services/comments/${commentId}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ vote_type: voteType })
                });

                const data = await response.json();

                if (data.success) {
                    // Update vote counts
                    const helpfulCount = btn.parentElement.querySelector('.helpful-count');
                    const unhelpfulCount = btn.parentElement.querySelector('.unhelpful-count');
                    
                    if (helpfulCount) helpfulCount.textContent = data.helpful_count;
                    if (unhelpfulCount) unhelpfulCount.textContent = data.unhelpful_count;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Reply button
        if (e.target.closest('.reply-btn')) {
            const btn = e.target.closest('.reply-btn');
            const commentId = btn.dataset.commentId;
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            if (replyForm) {
                replyForm.classList.toggle('hidden');
            }
        }
    });
});
</script>
@endpush