/**
 * AI Content Generation Progress Polling System
 * Based on the Redis polling pattern used in credit score rating
 */

class AiContentProgressPoller {
    constructor(sessionHash, options = {}) {
        this.sessionHash = sessionHash;
        this.pollInterval = options.pollInterval || 3000; // 3 seconds
        this.maxPollingDuration = options.maxPollingDuration || 20 * 60 * 1000; // 20 minutes
        this.intervalId = null;
        this.timeoutId = null;
        this.pollingStartTime = new Date();
        this.isPolling = false;
        
        // Callbacks
        this.onProgress = options.onProgress || this.defaultProgressHandler.bind(this);
        this.onComplete = options.onComplete || this.defaultCompleteHandler.bind(this);
        this.onError = options.onError || this.defaultErrorHandler.bind(this);
        
        console.log('🚀 [AI-POLLER] Initialized for session:', sessionHash);
    }

    /**
     * Start polling for progress updates
     */
    startPolling() {
        if (this.isPolling) {
            console.log('⚠️ [AI-POLLER] Already polling, skipping start');
            return;
        }

        console.log('🚀 [AI-POLLER] Starting progress polling...');
        this.isPolling = true;
        this.pollingStartTime = new Date();
        
        // Clear any existing intervals
        this.stopPolling();
        
        // Start polling
        this.intervalId = setInterval(() => {
            this.fetchProgress();
        }, this.pollInterval);
        
        // Set timeout to stop polling after max duration
        this.timeoutId = setTimeout(() => {
            console.log('⏰ [AI-POLLER] Max polling duration reached, stopping...');
            this.stopPolling();
            this.onError({
                message: 'زمان انتظار به پایان رسید. لطفاً صفحه را تازه‌سازی کنید.',
                code: 'POLLING_TIMEOUT'
            });
        }, this.maxPollingDuration);
        
        // Initial fetch
        this.fetchProgress();
    }

    /**
     * Stop polling
     */
    stopPolling() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
        
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
            this.timeoutId = null;
        }
        
        this.isPolling = false;
        console.log('🛑 [AI-POLLER] Polling stopped');
    }

    /**
     * Fetch current progress from API
     */
    async fetchProgress() {
        try {
            const response = await fetch(`/api/ai-content-progress/${this.sessionHash}/status`);
            
            if (!response.ok) {
                if (response.status === 404) {
                    console.log('⚠️ [AI-POLLER] Session not found');
                    this.onError({
                        message: 'جلسه تولید محتوا یافت نشد',
                        code: 'SESSION_NOT_FOUND'
                    });
                    return;
                }
                throw new Error(`API Error: ${response.status} ${response.statusText}`);
            }
            
            const data = await response.json();
            
            console.log('📊 [AI-POLLER] Progress update:', {
                step: data.step,
                progress: data.progress,
                status: data.status,
                message: data.message
            });
            
            // Handle different states
            if (data.is_completed) {
                console.log('✅ [AI-POLLER] Generation completed!');
                this.stopPolling();
                this.onComplete(data);
                
            } else if (data.is_failed) {
                console.log('❌ [AI-POLLER] Generation failed!');
                this.stopPolling();
                this.onError({
                    message: data.error_data?.message || 'تولید محتوا با خطا مواجه شد',
                    code: 'GENERATION_FAILED',
                    data: data
                });
                
            } else {
                // Still in progress
                this.onProgress(data);
            }
            
        } catch (error) {
            console.error('❌ [AI-POLLER] Error fetching progress:', error);
            // Don't stop polling on network errors, just log them
        }
    }

    /**
     * Default progress handler
     */
    defaultProgressHandler(data) {
        // Update progress bar
        this.updateProgressBar(data.progress || 0);
        
        // Update status message
        this.updateStatusMessage(data.message || 'در حال پردازش...');
        
        // Update step indicator
        this.updateStepIndicator(data.step || 'unknown');
        
        // Update step-specific data
        this.updateStepData(data);
    }

    /**
     * Default completion handler
     */
    defaultCompleteHandler(data) {
        console.log('🎉 [AI-POLLER] Generation completed successfully!');
        
        // Update progress to 100%
        this.updateProgressBar(100);
        this.updateStatusMessage('تولید محتوا با موفقیت کامل شد!');
        this.updateStepIndicator('completed');
        
        // Show completion actions
        this.showCompletionActions(data);
    }

    /**
     * Default error handler
     */
    defaultErrorHandler(errorData) {
        console.log('💥 [AI-POLLER] Generation failed:', errorData);
        
        // Update UI for error state
        this.updateStatusMessage(errorData.message || 'خطایی رخ داده است');
        this.showErrorActions(errorData);
    }

    /**
     * Update progress bar
     */
    updateProgressBar(progress) {
        const progressBar = document.getElementById('ai-progress-bar');
        const progressText = document.getElementById('ai-progress-text');
        
        if (progressBar) {
            progressBar.style.width = progress + '%';
        }
        
        if (progressText) {
            progressText.textContent = progress + '%';
        }
    }

    /**
     * Update status message
     */
    updateStatusMessage(message) {
        const statusElement = document.getElementById('ai-status-message');
        if (statusElement) {
            statusElement.textContent = message;
        }
    }

    /**
     * Update step indicator
     */
    updateStepIndicator(currentStep) {
        const steps = [
            'initialization',
            'heading_generation', 
            'section_generation',
            'image_generation',
            'summary_generation',
            'meta_generation',
            'faq_generation',
            'completed'
        ];
        
        steps.forEach((step, index) => {
            const stepElement = document.getElementById(`ai-step-${step}`);
            if (!stepElement) return;
            
            const isCurrentStep = step === currentStep;
            const isCompleted = steps.indexOf(step) < steps.indexOf(currentStep);
            
            if (isCompleted) {
                this.markStepAsCompleted(stepElement);
            } else if (isCurrentStep) {
                this.markStepAsActive(stepElement);
            } else {
                this.markStepAsPending(stepElement);
            }
        });
    }

    /**
     * Mark step as completed
     */
    markStepAsCompleted(stepElement) {
        const icon = stepElement.querySelector('.step-icon');
        const text = stepElement.querySelector('.step-text');
        
        if (icon) {
            icon.className = 'step-icon w-8 h-8 rounded-full bg-green-100 flex items-center justify-center';
            icon.innerHTML = `
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            `;
        }
        
        if (text) {
            text.className = 'step-text text-xs text-green-700 text-center';
        }
    }

    /**
     * Mark step as active
     */
    markStepAsActive(stepElement) {
        const icon = stepElement.querySelector('.step-icon');
        const text = stepElement.querySelector('.step-text');
        
        if (icon) {
            icon.className = 'step-icon w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center';
            icon.innerHTML = `<div class="w-3 h-3 bg-blue-600 rounded-full animate-pulse"></div>`;
        }
        
        if (text) {
            text.className = 'step-text text-xs text-blue-700 text-center font-medium';
        }
    }

    /**
     * Mark step as pending
     */
    markStepAsPending(stepElement) {
        const icon = stepElement.querySelector('.step-icon');
        const text = stepElement.querySelector('.step-text');
        
        if (icon) {
            icon.className = 'step-icon w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center';
            icon.innerHTML = `<div class="w-3 h-3 bg-gray-400 rounded-full"></div>`;
        }
        
        if (text) {
            text.className = 'step-text text-xs text-gray-500 text-center';
        }
    }

    /**
     * Update step-specific data
     */
    updateStepData(data) {
        // Update headings count
        if (data.headings_count) {
            const headingsCount = document.getElementById('ai-headings-count');
            if (headingsCount) {
                headingsCount.textContent = data.headings_count;
            }
        }
        
        // Update sections progress
        if (data.completed_sections !== undefined && data.total_sections !== undefined) {
            const sectionsProgress = document.getElementById('ai-sections-progress');
            if (sectionsProgress) {
                sectionsProgress.textContent = `${data.completed_sections}/${data.total_sections}`;
            }
        }
        
        // Update images progress
        if (data.completed_images !== undefined && data.total_images !== undefined) {
            const imagesProgress = document.getElementById('ai-images-progress');
            if (imagesProgress) {
                imagesProgress.textContent = `${data.completed_images}/${data.total_images}`;
            }
        }
    }

    /**
     * Show completion actions
     */
    showCompletionActions(data) {
        const actionsContainer = document.getElementById('ai-completion-actions');
        if (!actionsContainer) return;
        
        actionsContainer.innerHTML = `
            <div class="text-center space-y-4 p-6 bg-green-50 rounded-lg border border-green-200">
                <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mx-auto">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-green-800">تولید محتوا کامل شد!</h3>
                <p class="text-green-700 text-sm">محتوای شما با موفقیت تولید شد و آماده استفاده است.</p>
                <div class="space-x-2 space-x-reverse">
                    <button onclick="viewGeneratedContent()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        مشاهده محتوا
                    </button>
                    <button onclick="copyContent()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        کپی محتوا
                    </button>
                </div>
            </div>
        `;
        
        actionsContainer.classList.remove('hidden');
    }

    /**
     * Show error actions
     */
    showErrorActions(errorData) {
        const actionsContainer = document.getElementById('ai-error-actions');
        if (!actionsContainer) return;
        
        actionsContainer.innerHTML = `
            <div class="text-center space-y-4 p-6 bg-red-50 rounded-lg border border-red-200">
                <div class="flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mx-auto">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-red-800">خطا در تولید محتوا</h3>
                <p class="text-red-700 text-sm">${errorData.message}</p>
                <div class="space-x-2 space-x-reverse">
                    <button onclick="retryGeneration()" 
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        تلاش مجدد
                    </button>
                    <button onclick="cancelGeneration()" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        انصراف
                    </button>
                </div>
            </div>
        `;
        
        actionsContainer.classList.remove('hidden');
    }

    /**
     * Cancel generation
     */
    async cancelGeneration() {
        try {
            const response = await fetch(`/api/ai-content-progress/${this.sessionHash}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.stopPolling();
                this.updateStatusMessage('تولید محتوا لغو شد');
                window.location.reload();
            }
            
        } catch (error) {
            console.error('❌ [AI-POLLER] Error canceling generation:', error);
        }
    }
}

// Global variables
let aiProgressPoller = null;
let sessionHash = null;

/**
 * Initialize AI content progress polling
 */
function initAiContentPolling(hash) {
    sessionHash = hash;
    
    console.log('🚀 [AI-CONTENT-POLLING] Initializing for session:', hash);
    
    aiProgressPoller = new AiContentProgressPoller(hash, {
        onProgress: function(data) {
            updateAiProgress(data);
        },
        onComplete: function(data) {
            handleAiComplete(data);
        },
        onError: function(errorData) {
            handleAiError(errorData);
        }
    });
    
    aiProgressPoller.startPolling();
}

/**
 * Update AI progress UI
 */
function updateAiProgress(data) {
    // Update progress bar
    const progressBar = document.getElementById('ai-progress-bar');
    const progressText = document.getElementById('ai-progress-percentage');
    
    if (progressBar) {
        progressBar.style.width = (data.progress || 0) + '%';
    }
    
    if (progressText) {
        progressText.textContent = (data.progress || 0) + '%';
    }
    
    // Update status message
    const statusMessage = document.getElementById('ai-status-message');
    if (statusMessage) {
        statusMessage.textContent = data.message || 'در حال پردازش...';
    }
    
    // Update step indicators
    updateAiStepIndicators(data.step);
    
    // Update detailed progress
    updateDetailedProgress(data);
}

/**
 * Update step indicators
 */
function updateAiStepIndicators(currentStep) {
    const stepMap = {
        'initialization': 1,
        'heading_generation': 2,
        'headings_completed': 2,
        'section_generation': 3,
        'section_progress': 3,
        'sections_completed': 3,
        'image_generation': 4,
        'image_progress': 4,
        'images_completed': 4,
        'summary_generation': 5,
        'summary_completed': 5,
        'meta_generation': 6,
        'meta_completed': 6,
        'faq_generation': 7,
        'completed': 7
    };
    
    const currentStepNumber = stepMap[currentStep] || 1;
    
    // Update all step indicators
    for (let i = 1; i <= 7; i++) {
        const stepElement = document.getElementById(`ai-step-${i}`);
        if (!stepElement) continue;
        
        const icon = stepElement.querySelector('.step-icon');
        const text = stepElement.querySelector('.step-text');
        
        if (i < currentStepNumber) {
            // Completed step
            if (icon) {
                icon.className = 'step-icon w-8 h-8 rounded-full bg-green-100 flex items-center justify-center';
                icon.innerHTML = `
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                `;
            }
            if (text) text.className = 'step-text text-xs text-green-700';
            
        } else if (i === currentStepNumber) {
            // Current step
            if (icon) {
                icon.className = 'step-icon w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center';
                icon.innerHTML = `<div class="w-3 h-3 bg-blue-600 rounded-full animate-pulse"></div>`;
            }
            if (text) text.className = 'step-text text-xs text-blue-700 font-medium';
            
        } else {
            // Future step
            if (icon) {
                icon.className = 'step-icon w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center';
                icon.innerHTML = `<div class="w-3 h-3 bg-gray-400 rounded-full"></div>`;
            }
            if (text) text.className = 'step-text text-xs text-gray-500';
        }
    }
}

/**
 * Update detailed progress information
 */
function updateDetailedProgress(data) {
    // Update headings info
    if (data.headings_count) {
        const headingsInfo = document.getElementById('ai-headings-info');
        if (headingsInfo) {
            headingsInfo.textContent = `${data.headings_count} سرفصل`;
        }
    }
    
    // Update sections progress
    if (data.total_sections) {
        const sectionsInfo = document.getElementById('ai-sections-info');
        if (sectionsInfo) {
            const completed = data.completed_sections || 0;
            sectionsInfo.textContent = `${completed}/${data.total_sections} بخش`;
        }
    }
    
    // Update images progress
    if (data.total_images) {
        const imagesInfo = document.getElementById('ai-images-info');
        if (imagesInfo) {
            const completed = data.completed_images || 0;
            imagesInfo.textContent = `${completed}/${data.total_images} تصویر`;
        }
    }
}

/**
 * Handle completion
 */
function handleAiComplete(data) {
    console.log('🎉 [AI-CONTENT] Generation completed!', data);
    
    // Show success state
    const container = document.getElementById('ai-progress-container');
    if (container) {
        container.classList.add('border-green-200');
        container.classList.remove('border-blue-200');
    }
    
    // Enable completion actions
    const completionSection = document.getElementById('ai-completion-section');
    if (completionSection) {
        completionSection.classList.remove('hidden');
    }
}

/**
 * Handle errors
 */
function handleAiError(errorData) {
    console.log('💥 [AI-CONTENT] Generation failed!', errorData);
    
    // Show error state
    const container = document.getElementById('ai-progress-container');
    if (container) {
        container.classList.add('border-red-200');
        container.classList.remove('border-blue-200');
    }
    
    // Show error section
    const errorSection = document.getElementById('ai-error-section');
    if (errorSection) {
        errorSection.classList.remove('hidden');
        
        const errorMessage = errorSection.querySelector('#ai-error-message');
        if (errorMessage) {
            errorMessage.textContent = errorData.message;
        }
    }
}

/**
 * Retry generation (reload page)
 */
function retryGeneration() {
    if (confirm('آیا می‌خواهید تولید محتوا را از ابتدا شروع کنید؟')) {
        window.location.reload();
    }
}

/**
 * Cancel generation
 */
function cancelGeneration() {
    if (aiProgressPoller) {
        aiProgressPoller.cancelGeneration();
    }
}

/**
 * View generated content
 */
function viewGeneratedContent() {
    // This will be handled by Livewire method
    if (window.Livewire && window.Livewire.find) {
        const component = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
        if (component) {
            component.call('copyContent');
        }
    }
}

/**
 * Copy content to clipboard
 */
function copyContent() {
    // This will be handled by Livewire method
    if (window.Livewire && window.Livewire.find) {
        const component = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
        if (component) {
            component.call('copyContent');
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Get session hash from Livewire component
    const sessionHashElement = document.getElementById('ai-session-hash');
    if (sessionHashElement) {
        const hash = sessionHashElement.textContent.trim();
        if (hash) {
            initAiContentPolling(hash);
        } else {
            console.error('❌ [AI-CONTENT-POLLING] No session hash found');
        }
    } else {
        console.error('❌ [AI-CONTENT-POLLING] Session hash element not found');
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (aiProgressPoller) {
        aiProgressPoller.stopPolling();
    }
});
