@if(isset($banks) && count($banks) > 0)
<div class="mt-8">
    <div class="text-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">بانک‌های پشتیبانی شده</h3>
        <p class="text-sm text-gray-600">برای دسترسی آسان‌تر، روی هر بانک کلیک کنید</p>
    </div>
    
    <div class="relative">
        <!-- Bank Slider Container -->
        <div class="bank-slider-container overflow-hidden" style="direction: ltr;">
            <div class="bank-slider flex gap-4 transition-transform duration-300 ease-in-out" id="bankSlider" style="direction: ltr;">
                @foreach($banks as $bank)
                @php
                    $bankSlug = strtolower(str_replace(' ', '-', $bank['en_name']));
                    $bankSlug = preg_replace('/[^a-z0-9\-]/', '', $bankSlug);
                    
                    // Get current service slug from request or passed variable
                    $currentServiceSlug = $serviceSlug ?? request()->route('slug1') ?? 'card-iban';
                    
                    // Build the service-specific bank URL
                    $bankUrl = route('services.show', [
                        'slug1' => $currentServiceSlug,
                        'slug2' => $bankSlug
                    ]);
                @endphp
                <a href="{{ $bankUrl }}" 
                   class="bank-item flex-shrink-0 w-24 h-16 bg-white rounded-lg border border-gray-200 flex items-center justify-center p-2 hover:shadow-md transition-all duration-300 hover:scale-105 hover:border-primary-normal" 
                   style="min-width: 96px; direction: ltr;"
                   title="استعلام {{ $bank['name'] }}">
                    @if($bank['logo'])
                        <img src="{{ $bank['logo'] }}" 
                             alt="{{ $bank['name'] }}" 
                             class="max-w-full max-h-full object-contain"
                             style="max-width: 80px; max-height: 40px;"
                             loading="lazy"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <!-- Fallback for failed image loading -->
                        <div class="text-center" style="display: none;">
                            <div class="w-8 h-8 rounded-full mx-auto mb-1 bg-primary-normal"></div>
                            <div class="text-xs text-gray-600 font-medium truncate">{{ $bank['name'] }}</div>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="w-8 h-8 rounded-full mx-auto mb-1 bg-primary-normal"></div>
                            <div class="text-xs text-gray-600 font-medium truncate">{{ $bank['name'] }}</div>
                        </div>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
        
        <!-- Navigation Arrows -->
        <button class="bank-slider-prev absolute left-0 top-1/2 transform -translate-y-1/2 bg-white border border-gray-300 rounded-full p-2 shadow-md hover:bg-sky-50 transition-colors duration-200 z-10" 
                onclick="slideBanks('prev')"
                style="display: none;">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <button class="bank-slider-next absolute right-0 top-1/2 transform -translate-y-1/2 bg-white border border-gray-300 rounded-full p-2 shadow-md hover:bg-sky-50 transition-colors duration-200 z-10" 
                onclick="slideBanks('next')">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('bankSlider');
    const prevBtn = document.querySelector('.bank-slider-prev');
    const nextBtn = document.querySelector('.bank-slider-next');
    const container = slider?.parentElement;
    
    if (!slider || !container) return;
    
    let currentPosition = 0;
    let isDragging = false;
    let startPos = 0;
    let startTransform = 0;
    
    const slideWidth = 120; // 96px (bank item width) + 24px (gap)
    
    function updateSliderParams() {
        const containerWidth = container.offsetWidth;
        const totalWidth = slider.children.length * slideWidth;
        const maxPosition = Math.max(0, totalWidth - containerWidth);
        return { containerWidth, totalWidth, maxPosition };
    }
    
    function updateNavigationButtons() {
        const { maxPosition } = updateSliderParams();
        if (prevBtn) prevBtn.style.display = currentPosition <= 0 ? 'none' : 'block';
        if (nextBtn) nextBtn.style.display = currentPosition >= maxPosition ? 'none' : 'block';
    }
    
    function setTransform(position) {
        slider.style.transform = `translateX(-${position}px)`;
    }
    
    window.slideBanks = function(direction) {
        const { maxPosition } = updateSliderParams();
        const step = slideWidth * 2; // Move 2 banks at a time
        
        if (direction === 'prev') {
            currentPosition = Math.max(0, currentPosition - step);
        } else {
            currentPosition = Math.min(maxPosition, currentPosition + step);
        }
        
        setTransform(currentPosition);
        updateNavigationButtons();
    };
    
    // Touch/Swipe functionality for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    let isTouching = false;
    
    // Touch events
    container.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        isTouching = true;
        isDragging = false;
        startPos = touchStartX;
        startTransform = currentPosition;
        
        // Stop auto-slide while touching
        clearInterval(autoSlideInterval);
    }, { passive: true });
    
    container.addEventListener('touchmove', function(e) {
        if (!isTouching) return;
        
        touchEndX = e.touches[0].clientX;
        const diff = touchStartX - touchEndX;
        const { maxPosition } = updateSliderParams();
        
        // Calculate new position based on touch movement
        let newPosition = startTransform + diff;
        newPosition = Math.max(0, Math.min(maxPosition, newPosition));
        
        setTransform(newPosition);
        isDragging = Math.abs(diff) > 10; // Consider it dragging if moved more than 10px
    }, { passive: true });
    
    container.addEventListener('touchend', function(e) {
        if (!isTouching) return;
        isTouching = false;
        
        const { maxPosition } = updateSliderParams();
        const diff = touchStartX - touchEndX;
        
        // If it was a swipe (not just a tap)
        if (isDragging) {
            // Snap to nearest bank position
            currentPosition = Math.round(currentPosition / slideWidth) * slideWidth;
            currentPosition = Math.max(0, Math.min(maxPosition, currentPosition));
            setTransform(currentPosition);
            updateNavigationButtons();
            
            // Prevent click events on bank items if we were dragging
            setTimeout(() => {
                isDragging = false;
            }, 50);
        }
        
        // Restart auto-slide
        startAutoSlide();
    }, { passive: true });
    
    // Prevent clicks on bank items when dragging
    container.addEventListener('click', function(e) {
        if (isDragging) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
    
    // Mouse events for desktop
    container.addEventListener('mousedown', function(e) {
        isDragging = false;
        startPos = e.clientX;
        startTransform = currentPosition;
        container.style.cursor = 'grabbing';
        clearInterval(autoSlideInterval);
    });
    
    container.addEventListener('mousemove', function(e) {
        if (startPos === 0) return;
        
        const diff = startPos - e.clientX;
        const { maxPosition } = updateSliderParams();
        
        let newPosition = startTransform + diff;
        newPosition = Math.max(0, Math.min(maxPosition, newPosition));
        
        setTransform(newPosition);
        isDragging = Math.abs(diff) > 10;
    });
    
    container.addEventListener('mouseup', function(e) {
        if (startPos === 0) return;
        
        const { maxPosition } = updateSliderParams();
        startPos = 0;
        container.style.cursor = 'grab';
        
        if (isDragging) {
            currentPosition = Math.round(currentPosition / slideWidth) * slideWidth;
            currentPosition = Math.max(0, Math.min(maxPosition, currentPosition));
            setTransform(currentPosition);
            updateNavigationButtons();
            
            setTimeout(() => {
                isDragging = false;
            }, 50);
        }
        
        startAutoSlide();
    });
    
    container.addEventListener('mouseleave', function() {
        startPos = 0;
        container.style.cursor = 'grab';
        startAutoSlide();
    });
    
    // Auto-slide functionality
    let autoSlideInterval;
    
    function startAutoSlide() {
        clearInterval(autoSlideInterval);
        autoSlideInterval = setInterval(() => {
            const { maxPosition } = updateSliderParams();
            if (currentPosition >= maxPosition) {
                currentPosition = 0;
            } else {
                currentPosition += slideWidth;
            }
            setTransform(currentPosition);
            updateNavigationButtons();
        }, 4000);
    }
    
    // Pause auto-slide on hover (desktop only)
    if (window.innerWidth > 768) {
        container.addEventListener('mouseenter', () => {
            clearInterval(autoSlideInterval);
        });
        
        container.addEventListener('mouseleave', () => {
            if (!isDragging && startPos === 0) {
                startAutoSlide();
            }
        });
    }
    
    // Initial setup
    updateNavigationButtons();
    startAutoSlide();
    
    // Handle window resize
    window.addEventListener('resize', () => {
        const { maxPosition } = updateSliderParams();
        if (currentPosition > maxPosition) {
            currentPosition = maxPosition;
            setTransform(currentPosition);
        }
        updateNavigationButtons();
    });
    
    // Debug info
    console.log('Bank slider initialized:', {
        totalBanks: slider.children.length,
        ...updateSliderParams()
    });
});
</script>

<style>
.bank-slider-container {
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
    cursor: grab;
    user-select: none;
}

.bank-slider-container::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

.bank-slider-container:active {
    cursor: grabbing;
}

.bank-item {
    transition: all 0.3s ease;
    text-decoration: none;
}

.bank-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    text-decoration: none;
}

.bank-item img {
    transition: opacity 0.3s ease;
}

@media (max-width: 768px) {
    .bank-item {
        min-width: 80px !important;
    }
    
    .bank-slider-prev,
    .bank-slider-next {
        display: none !important;
    }
    
    .bank-slider-container {
        touch-action: pan-x;
    }
}

/* Loading state for images */
.bank-item img[src=""] {
    display: none;
}

/* Improve touch targets on mobile */
@media (max-width: 768px) {
    .bank-item {
        min-height: 64px;
        min-width: 88px !important;
    }
}
</style>
@endif 