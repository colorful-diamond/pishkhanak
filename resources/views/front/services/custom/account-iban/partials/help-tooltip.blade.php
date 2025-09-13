{{-- Help Tooltip Component --}}
{{-- Usage: @include('front.services.custom.account-iban.partials.help-tooltip', ['content' => 'tooltip text', 'position' => 'top|bottom|left|right', 'trigger' => 'hover|click']) --}}

@php
    $content = $content ?? 'راهنمایی موجود نیست';
    $position = $position ?? 'top';
    $trigger = $trigger ?? 'hover';
    $id = 'tooltip_' . uniqid();
@endphp

<div class="help-tooltip-container relative inline-block">
    {{-- Trigger Element --}}
    <div class="help-tooltip-trigger cursor-help inline-flex items-center justify-center w-5 h-5 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-full transition-colors duration-200"
         data-tooltip-id="{{ $id }}"
         data-tooltip-position="{{ $position }}"
         data-tooltip-trigger="{{ $trigger }}">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
        </svg>
    </div>

    {{-- Tooltip Content --}}
    <div id="{{ $id }}" class="help-tooltip-content absolute z-50 hidden opacity-0 transform transition-all duration-200 ease-in-out
                @if($position === 'top') bottom-full left-1/2 -translate-x-1/2 -translate-y-2 @endif
                @if($position === 'bottom') top-full left-1/2 -translate-x-1/2 translate-y-2 @endif
                @if($position === 'left') right-full top-1/2 -translate-y-1/2 -translate-x-2 @endif
                @if($position === 'right') left-full top-1/2 -translate-y-1/2 translate-x-2 @endif">
        
        <div class="bg-gray-900 text-white text-sm rounded-lg px-3 py-2 max-w-xs shadow-lg">
            {{ $content }}
            
            {{-- Arrow --}}
            <div class="absolute
                     @if($position === 'top') top-full left-1/2 -translate-x-1/2 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900 @endif
                     @if($position === 'bottom') bottom-full left-1/2 -translate-x-1/2 border-l-4 border-r-4 border-b-4 border-transparent border-b-gray-900 @endif
                     @if($position === 'left') left-full top-1/2 -translate-y-1/2 border-t-4 border-b-4 border-l-4 border-transparent border-l-gray-900 @endif
                     @if($position === 'right') right-full top-1/2 -translate-y-1/2 border-t-4 border-b-4 border-r-4 border-transparent border-r-gray-900 @endif">
            </div>
        </div>
    </div>
</div>

{{-- Enhanced Tooltip with Rich Content --}}
@if(isset($rich) && $rich)
<div class="help-tooltip-rich-container relative inline-block">
    <div class="help-tooltip-trigger-rich cursor-help inline-flex items-center justify-center w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-full shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105"
         data-rich-tooltip-id="{{ $id }}_rich"
         data-tooltip-position="{{ $position }}"
         data-tooltip-trigger="{{ $trigger }}">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
        </svg>
    </div>

    <div id="{{ $id }}_rich" class="help-tooltip-rich-content absolute z-50 hidden opacity-0 transform transition-all duration-300 ease-in-out
                @if($position === 'top') bottom-full left-1/2 -translate-x-1/2 -translate-y-3 @endif
                @if($position === 'bottom') top-full left-1/2 -translate-x-1/2 translate-y-3 @endif
                @if($position === 'left') right-full top-1/2 -translate-y-1/2 -translate-x-3 @endif
                @if($position === 'right') left-full top-1/2 -translate-y-1/2 translate-x-3 @endif">
        
        <div class="bg-white border border-gray-200 rounded-xl shadow-xl max-w-sm overflow-hidden">
            {{-- Header --}}
            @if(isset($title))
            <div class="bg-gradient-to-r from-blue-500 to-purple-500 px-4 py-2">
                <h4 class="text-white font-bold text-sm">{{ $title }}</h4>
            </div>
            @endif
            
            {{-- Content --}}
            <div class="p-4">
                <div class="text-gray-700 text-sm leading-relaxed">
                    {{ $content }}
                </div>
                
                @if(isset($example))
                <div class="mt-3 p-3 bg-gray-50 rounded-lg border">
                    <div class="text-xs font-semibold text-gray-600 mb-1">مثال:</div>
                    <div class="text-sm font-mono text-gray-800">{{ $example }}</div>
                </div>
                @endif
                
                @if(isset($link))
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <a href="{{ $link['url'] ?? '#' }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                        {{ $link['text'] ?? 'اطلاعات بیشتر' }}
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
                @endif
            </div>
            
            {{-- Arrow --}}
            <div class="absolute
                     @if($position === 'top') top-full left-1/2 -translate-x-1/2 border-l-8 border-r-8 border-t-8 border-transparent border-t-white @endif
                     @if($position === 'bottom') bottom-full left-1/2 -translate-x-1/2 border-l-8 border-r-8 border-b-8 border-transparent border-b-white @endif
                     @if($position === 'left') left-full top-1/2 -translate-y-1/2 border-t-8 border-b-8 border-l-8 border-transparent border-l-white @endif
                     @if($position === 'right') right-full top-1/2 -translate-y-1/2 border-t-8 border-b-8 border-r-8 border-transparent border-r-white @endif">
            </div>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all tooltips
    initializeTooltips();
    
    function initializeTooltips() {
        // Regular tooltips
        document.querySelectorAll('.help-tooltip-trigger').forEach(trigger => {
            const tooltipId = trigger.dataset.tooltipId;
            const position = trigger.dataset.tooltipPosition || 'top';
            const triggerType = trigger.dataset.tooltipTrigger || 'hover';
            const tooltip = document.getElementById(tooltipId);
            
            if (!tooltip) return;
            
            if (triggerType === 'click') {
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleTooltip(tooltip);
                });
                
                // Close on outside click
                document.addEventListener('click', () => {
                    hideTooltip(tooltip);
                });
            } else {
                // Hover behavior
                trigger.addEventListener('mouseenter', () => showTooltip(tooltip));
                trigger.addEventListener('mouseleave', () => hideTooltip(tooltip));
                
                // Keep tooltip visible when hovering over it
                tooltip.addEventListener('mouseenter', () => showTooltip(tooltip));
                tooltip.addEventListener('mouseleave', () => hideTooltip(tooltip));
            }
        });
        
        // Rich tooltips
        document.querySelectorAll('.help-tooltip-trigger-rich').forEach(trigger => {
            const tooltipId = trigger.dataset.richTooltipId;
            const position = trigger.dataset.tooltipPosition || 'top';
            const triggerType = trigger.dataset.tooltipTrigger || 'hover';
            const tooltip = document.getElementById(tooltipId);
            
            if (!tooltip) return;
            
            if (triggerType === 'click') {
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleTooltip(tooltip);
                });
                
                document.addEventListener('click', () => {
                    hideTooltip(tooltip);
                });
            } else {
                trigger.addEventListener('mouseenter', () => showTooltip(tooltip));
                trigger.addEventListener('mouseleave', () => {
                    setTimeout(() => {
                        if (!tooltip.matches(':hover')) {
                            hideTooltip(tooltip);
                        }
                    }, 100);
                });
                
                tooltip.addEventListener('mouseleave', () => hideTooltip(tooltip));
            }
        });
    }
    
    function showTooltip(tooltip) {
        // Hide all other tooltips first
        document.querySelectorAll('.help-tooltip-content, .help-tooltip-rich-content').forEach(t => {
            if (t !== tooltip) hideTooltip(t);
        });
        
        tooltip.classList.remove('hidden');
        setTimeout(() => {
            tooltip.classList.remove('opacity-0');
            tooltip.classList.add('opacity-100');
        }, 10);
    }
    
    function hideTooltip(tooltip) {
        tooltip.classList.remove('opacity-100');
        tooltip.classList.add('opacity-0');
        setTimeout(() => {
            tooltip.classList.add('hidden');
        }, 200);
    }
    
    function toggleTooltip(tooltip) {
        if (tooltip.classList.contains('hidden')) {
            showTooltip(tooltip);
        } else {
            hideTooltip(tooltip);
        }
    }
});

// Global tooltip utilities
window.PishkhanakTooltip = {
    show: function(tooltipId) {
        const tooltip = document.getElementById(tooltipId);
        if (tooltip) showTooltip(tooltip);
    },
    
    hide: function(tooltipId) {
        const tooltip = document.getElementById(tooltipId);
        if (tooltip) hideTooltip(tooltip);
    },
    
    hideAll: function() {
        document.querySelectorAll('.help-tooltip-content, .help-tooltip-rich-content').forEach(hideTooltip);
    }
};
</script>

<style>
/* Tooltip Animation Styles */
.help-tooltip-content,
.help-tooltip-rich-content {
    pointer-events: none;
}

.help-tooltip-content.opacity-100,
.help-tooltip-rich-content.opacity-100 {
    pointer-events: auto;
}

/* Enhanced Hover Effects */
.help-tooltip-trigger:hover {
    transform: scale(1.1);
}

.help-tooltip-trigger-rich:hover {
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.4);
}

/* Smooth Transitions */
.help-tooltip-content,
.help-tooltip-rich-content {
    transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
}

/* Responsive Adjustments */
@media (max-width: 640px) {
    .help-tooltip-rich-content .max-w-sm {
        max-width: calc(100vw - 2rem);
    }
}

/* RTL Support */
[dir="rtl"] .help-tooltip-content,
[dir="rtl"] .help-tooltip-rich-content {
    text-align: right;
}

/* Accessibility */
.help-tooltip-trigger:focus,
.help-tooltip-trigger-rich:focus {
    outline: 2px solid #3B82F6;
    outline-offset: 2px;
}

/* Dark Mode Support (if needed) */
@media (prefers-color-scheme: dark) {
    .help-tooltip-content .bg-gray-900 {
        background-color: #1F2937;
    }
    
    .help-tooltip-rich-content .bg-white {
        background-color: #374151;
        color: #F9FAFB;
    }
}
</style>