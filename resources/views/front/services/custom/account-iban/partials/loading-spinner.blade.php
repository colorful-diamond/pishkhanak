{{-- Loading Spinner Component --}}
{{-- Usage: @include('front.services.custom.account-iban.partials.loading-spinner', ['type' => 'default|small|large', 'message' => 'loading message']) --}}

@php
    $type = $type ?? 'default';
    $message = $message ?? 'در حال پردازش...';
@endphp

@if($type === 'small')
    {{-- Small Loading Spinner --}}
    <div class="loading-spinner-small inline-flex items-center">
        <svg class="animate-spin h-4 w-4 text-blue-500 ml-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm text-blue-600">{{ $message }}</span>
    </div>

@elseif($type === 'large')
    {{-- Large Loading Spinner --}}
    <div class="loading-spinner-large fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-sm mx-4 text-center shadow-2xl">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full mb-4 relative">
                    <svg class="animate-spin h-8 w-8 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{-- Pulse Ring --}}
                    <div class="absolute inset-0 rounded-full border-4 border-blue-200 animate-ping"></div>
                </div>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $message }}</h3>
            <p class="text-sm text-gray-600">لطفاً صبر کنید...</p>
            
            {{-- Progress Steps --}}
            <div class="mt-6 space-y-2">
                <div class="flex items-center text-xs text-gray-500">
                    <div class="w-2 h-2 bg-green-500 rounded-full ml-2"></div>
                    <span>تأیید شماره حساب</span>
                </div>
                <div class="flex items-center text-xs text-gray-500">
                    <div class="w-2 h-2 bg-blue-500 rounded-full ml-2 animate-pulse"></div>
                    <span>محاسبه شبا</span>
                </div>
                <div class="flex items-center text-xs text-gray-400">
                    <div class="w-2 h-2 bg-gray-300 rounded-full ml-2"></div>
                    <span>آماده‌سازی نتیجه</span>
                </div>
            </div>
        </div>
    </div>

@else
    {{-- Default Loading Spinner --}}
    <div class="loading-spinner-default flex flex-col items-center justify-center py-12">
        <div class="relative mb-4">
            {{-- Main Spinner --}}
            <div class="w-16 h-16 border-4 border-blue-200 border-t-4 border-t-blue-500 rounded-full animate-spin"></div>
            
            {{-- Inner Spinner --}}
            <div class="absolute inset-2 w-8 h-8 border-2 border-purple-200 border-b-2 border-b-purple-500 rounded-full animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
            
            {{-- Center Dot --}}
            <div class="absolute inset-6 w-4 h-4 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full animate-pulse"></div>
        </div>
        
        <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ $message }}</h3>
        <p class="text-sm text-gray-500 mb-4">محاسبه شماره شبا در حال انجام است</p>
        
        {{-- Progress Bar --}}
        <div class="w-64 bg-gray-200 rounded-full h-2">
            <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full animate-pulse" style="width: 75%;"></div>
        </div>
        
        {{-- Loading Text Animation --}}
        <div class="mt-4 flex items-center space-x-1">
            <span class="text-blue-500">در حال پردازش</span>
            <div class="flex space-x-1">
                <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce"></div>
                <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
            </div>
        </div>
    </div>
@endif

{{-- Inline Spinner (for buttons) --}}
<div class="loading-spinner-inline hidden">
    <svg class="animate-spin h-4 w-4 text-current" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
</div>

<script>
// Loading Spinner Utilities
window.PishkhanakLoader = {
    // Show loading state
    show: function(type = 'default', message = 'در حال پردازش...') {
        const spinner = document.querySelector(`.loading-spinner-${type}`);
        if (spinner) {
            if (message && type !== 'inline') {
                const messageEl = spinner.querySelector('h3, span');
                if (messageEl) messageEl.textContent = message;
            }
            spinner.classList.remove('hidden');
            
            // Disable page interaction for large spinner
            if (type === 'large') {
                document.body.style.overflow = 'hidden';
            }
        }
    },
    
    // Hide loading state  
    hide: function(type = 'default') {
        const spinner = document.querySelector(`.loading-spinner-${type}`);
        if (spinner) {
            spinner.classList.add('hidden');
            
            // Re-enable page interaction
            if (type === 'large') {
                document.body.style.overflow = '';
            }
        }
    },
    
    // Show inline button loading
    showButtonLoading: function(buttonElement, loadingText = 'در حال پردازش...') {
        if (!buttonElement.originalText) {
            buttonElement.originalText = buttonElement.innerHTML;
        }
        
        buttonElement.disabled = true;
        buttonElement.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-current ml-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            ${loadingText}
        `;
    },
    
    // Hide inline button loading
    hideButtonLoading: function(buttonElement) {
        if (buttonElement.originalText) {
            buttonElement.disabled = false;
            buttonElement.innerHTML = buttonElement.originalText;
        }
    }
};

// Auto-hide loading after delay (safety mechanism)
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide after 30 seconds to prevent hanging
    setTimeout(() => {
        document.querySelectorAll('[class*="loading-spinner"]').forEach(spinner => {
            if (!spinner.classList.contains('hidden')) {
                console.warn('Auto-hiding loading spinner after 30s timeout');
                PishkhanakLoader.hide('default');
                PishkhanakLoader.hide('large');
            }
        });
    }, 30000);
});
</script>

<style>
/* Loading Animation Styles */
.loading-spinner-default,
.loading-spinner-large .bg-white,
.loading-spinner-small {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

/* Custom Bounce Animation for Dots */
@keyframes bounce {
    0%, 80%, 100% {
        transform: scale(0);
    } 40% {
        transform: scale(1);
    }
}

/* Pulse Ring Animation */
@keyframes ping {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

/* Smooth Progress Bar */
.loading-spinner-default .bg-gradient-to-r {
    animation: progressSlide 2s ease-in-out infinite;
}

@keyframes progressSlide {
    0% { width: 0%; }
    50% { width: 75%; }
    100% { width: 100%; }
}

/* Prevent scrolling when large spinner is shown */
body.loading-active {
    overflow: hidden;
}

/* Spinner Color Variations */
.spinner-blue .border-t-blue-500 { border-top-color: #3B82F6; }
.spinner-green .border-t-green-500 { border-top-color: #10B981; }
.spinner-purple .border-t-purple-500 { border-top-color: #8B5CF6; }
.spinner-red .border-t-red-500 { border-top-color: #EF4444; }

/* RTL Support */
[dir="rtl"] .loading-spinner-small svg {
    margin-left: 0;
    margin-right: 0.5rem;
}
</style>