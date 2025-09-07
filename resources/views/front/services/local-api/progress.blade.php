@extends('front.layouts.app')

@section('title', 'پیشرفت سرویس - ' . $service->title)

@push('styles')
<style>
.otp-input {
    width: 50px;
    height: 50px;
    text-align: center;
    font-size: 1.5rem;
    font-weight: bold;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    margin: 0 5px;
    transition: all 0.3s ease;
    /* Prevent browser extension interference */
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: textfield;
    background-image: none !important;
    background-color: white !important;
    color: inherit !important;
    font-family: inherit !important;
    border-style: solid !important;
    /* Disable autocomplete features */
    -webkit-autocomplete: off;
    -webkit-contacts-auto-fill: disabled;
    -webkit-credentials-auto-fill: disabled;
}

.otp-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

/* Prevent browser extension styling */
.otp-input::-webkit-outer-spin-button,
.otp-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Prevent autocomplete dropdown */
.otp-input::-webkit-calendar-picker-indicator {
    display: none;
}

/* Force consistent appearance across browsers */
.otp-input:-webkit-autofill,
.otp-input:-webkit-autofill:hover,
.otp-input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0 1000px white inset !important;
    -webkit-text-fill-color: #1f2937 !important;
    transition: background-color 5000s ease-in-out 0s;
}

/* Ultra aggressive protection against interference */
.otp-input {
    position: relative !important;
    z-index: 9999 !important;
    pointer-events: auto !important;
    user-select: text !important;
    -webkit-user-select: text !important;
    -moz-user-select: text !important;
    isolation: isolate !important;
}

/* Prevent any overlay or pseudo-element interference */
.otp-input::before,
.otp-input::after {
    display: none !important;
    content: none !important;
}

/* Force focus styles to work */
.otp-input:focus {
    z-index: 10000 !important;
    position: relative !important;
}

.progress-step.active {
    color: #3b82f6 !important;
}

.progress-step.completed {
    color: #10b981 !important;
}

.progress-step.failed {
    color: #ef4444 !important;
}

.otp-container {
    transition: all 0.5s ease-in-out;
    max-height: 0;
    overflow: hidden;
    opacity: 0;
}

.otp-container.show {
    max-height: 500px;
    opacity: 1;
    margin-top: 1rem;
}

.progress-indicator {
    transition: all 0.3s ease;
}

.bounce-animation {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

/* Steps Section Styles */
#steps-container {
    max-height: 200px;
    opacity: 1;
}

#steps-container.collapsed {
    max-height: 0;
    opacity: 0;
}

.step-item {
    transition: all 0.3s ease;
}

.step-item:hover {
    transform: scale(1.05);
}

/* Mobile base optimizations */
.mobile-optimized {
    position: relative;
}

.mobile-container {
    width: 100%;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    /* Container adjustments */
    .container {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    /* Mobile container full width */
    .mobile-container {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Progress card mobile adjustments */
    .mobile-optimized {
        border-radius: 1rem !important;
        margin: 0 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    }
    
    /* Progress header mobile */
    .bg-gradient-to-r.from-sky-500.to-sky-600 {
        padding: 1rem !important;
    }
    
    .bg-gradient-to-r.from-sky-500.to-sky-600 h2 {
        font-size: 1rem !important;
        line-height: 1.5 !important;
    }
    
    .bg-gradient-to-r.from-sky-500.to-sky-600 .text-2xl {
        font-size: 1.5rem !important;
        line-height: 2rem !important;
    }
    
    .bg-gradient-to-r.from-sky-500.to-sky-600 .text-sm {
        font-size: 0.75rem !important;
        line-height: 1rem !important;
    }
    
    /* Progress content mobile */
    .p-6 {
        padding: 1rem !important;
    }
    
    /* Status container mobile */
    #status-container {
        margin-bottom: 1rem !important;
        padding: 1rem !important;
    }
    
    #status-container .progress-indicator svg {
        width: 1.5rem !important;
        height: 1.5rem !important;
        margin-left: 0.5rem !important;
    }
    
    #current-step-description {
        font-size: 0.875rem !important;
        margin-right: 0.5rem !important;
    }
    
    /* Error message mobile */
    #error-message-display {
        margin-top: 1rem !important;
        padding: 1rem !important;
    }
    
    #error-message-display h3 {
        font-size: 1rem !important;
        margin-bottom: 0.5rem !important;
    }
    
    #error-message-display p {
        font-size: 0.875rem !important;
    }
    
    /* Steps section mobile */
    #steps-section {
        padding-top: 1rem !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    #steps-section h3 {
        font-size: 0.875rem !important;
    }
    
    #steps-toggle {
        font-size: 0.75rem !important;
    }
    
    #steps-container {
        max-height: 80px !important;
    }
    
    .step-item {
        min-width: 0;
        flex-shrink: 1;
    }
    
    /* Mobile steps layout improvements */
    .md\\:hidden .flex.items-center.space-x-2 {
        padding: 0.5rem !important;
    }
    
    .md\\:hidden .w-6.h-6 {
        width: 1.25rem !important;
        height: 1.25rem !important;
    }
    
    .md\\:hidden .w-2.h-2 {
        width: 0.375rem !important;
        height: 0.375rem !important;
    }
    
    .md\\:hidden .w-3.h-3 {
        width: 0.5rem !important;
        height: 0.5rem !important;
    }
    
    /* Timer section mobile */
    #timer-section {
        padding-top: 0.75rem !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    #timer-section span {
        font-size: 0.75rem !important;
    }
    
    #remaining-time {
        font-size: 1rem !important;
    }
    
    /* OTP section mobile improvements */
    .otp-container.show {
        margin-top: 1rem !important;
        padding: 1rem !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border-radius: 0.75rem;
        background: white;
    }
    
    /* OTP inputs mobile */
    .otp-input {
        width: 40px !important;
        height: 40px !important;
        font-size: 1.25rem !important;
        margin: 0 3px !important;
        border-radius: 6px !important;
    }
    
    /* OTP header mobile */
    .otp-container .inline-flex.w-16.h-16 {
        width: 3rem !important;
        height: 3rem !important;
    }
    
    .otp-container .w-8.h-8 {
        width: 1.5rem !important;
        height: 1.5rem !important;
    }
    
    .otp-container h3 {
        font-size: 1rem !important;
        margin-bottom: 0.5rem !important;
    }
    
    .otp-container p {
        font-size: 0.875rem !important;
    }
    
    /* OTP buttons mobile */
    .otp-container button {
        padding: 0.75rem 1.5rem !important;
        font-size: 0.875rem !important;
    }
    
    /* Action buttons mobile */
    .error-actions button,
    .success-actions button {
        padding: 0.625rem 1rem !important;
        font-size: 0.75rem !important;
        margin: 0.25rem !important;
    }
    
    .error-actions .space-x-2,
    .success-actions .space-x-2 {
        gap: 0.5rem !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
    }
    
    /* Action descriptions mobile */
    .error-actions .bg-yellow-50,
    .success-actions .bg-sky-50 {
        padding: 0.75rem !important;
        margin-bottom: 0.75rem !important;
    }
    
    .error-actions .bg-yellow-50 p,
    .success-actions p {
        font-size: 0.75rem !important;
        line-height: 1.25 !important;
    }
    
    /* Info box mobile */
    .bg-sky-50.rounded-lg.p-4 {
        padding: 0.75rem !important;
        margin-top: 1rem !important;
        margin-left: 0.5rem !important;
        margin-right: 0.5rem !important;
    }
    
    .bg-sky-50.rounded-lg.p-4 p {
        font-size: 0.75rem !important;
    }
    
    .bg-sky-50.rounded-lg.p-4 svg {
        width: 0.875rem !important;
        height: 0.875rem !important;
    }
    
    /* Progress bar mobile */
    .bg-sky-400.bg-opacity-30.rounded-full {
        height: 0.375rem !important;
    }
    
    .bg-white.rounded-full.h-2 {
        height: 0.375rem !important;
    }
}

/* Extra small mobile (phones in portrait) */
@media (max-width: 480px) {
    .container {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
    
    .bg-white.rounded-2xl.shadow-lg.border {
        margin: 0 0.25rem !important;
        border-radius: 0.75rem !important;
    }
    
    .bg-gradient-to-r.from-sky-500.to-sky-600 {
        padding: 0.75rem !important;
    }
    
    .p-6 {
        padding: 0.75rem !important;
    }
    
    /* OTP inputs extra small */
    .otp-input {
        width: 35px !important;
        height: 35px !important;
        font-size: 1.125rem !important;
        margin: 0 2px !important;
    }
    
    /* Steps even more compact */
    #steps-container {
        max-height: 60px !important;
    }
    
    .md\\:hidden .w-6.h-6 {
        width: 1rem !important;
        height: 1rem !important;
    }
    
    /* Action buttons stack vertically on very small screens */
    .error-actions .space-x-2,
    .success-actions .space-x-2 {
        flex-direction: column !important;
        gap: 0.5rem !important;
    }
    
    .error-actions button,
    .success-actions button {
        width: 100% !important;
        padding: 0.75rem !important;
        font-size: 0.875rem !important;
    }
}
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto mobile-container">
        <!-- Desktop Header -->
        <div class="text-center mb-8 hidden md:block">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $service->title }}</h1>
            <p class="text-gray-600">پیشرفت پردازش درخواست شما</p>
        </div>
        
        <!-- Mobile Header -->
        <div class="text-center mb-4 md:hidden">
            <h1 class="text-lg font-bold text-gray-800 mb-1">{{ $service->title }}</h1>
            <p class="text-sm text-gray-600">پیشرفت درخواست</p>
        </div>

        <!-- Progress Card -->
        <div class="bg-white rounded-2xl shadow-lg border overflow-hidden mobile-optimized">
            <!-- Progress Header -->
            <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-6 py-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">وضعیت پردازش</h2>
                        <p class="text-sky-100 text-sm mt-1" id="status-message">{{ $localRequest['current_message'] ?? 'در حال آماده‌سازی...' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold" id="progress-percentage">{{ $localRequest['progress'] ?? 0 }}%</div>
                        <div class="text-sky-100 text-sm">پیشرفت</div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="bg-sky-400 bg-opacity-30 rounded-full h-2">
                        <div class="bg-white rounded-full h-2 transition-all duration-500 ease-out" 
                         id="progress-bar"
                             style="width: {{ $localRequest['progress'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Progress Content -->
            <div class="p-6">
                <!-- Status Container -->
                <div id="status-container" class="mb-6 p-6 text-sky-600 rounded-lg">
                    <div class="flex items-center justify-center">
                        <div class="progress-indicator">
                            <svg class="animate-spin w-8 h-8 text-sky-600 ml-2" id="progress-spinner" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <br>
                    <p id="current-step-description" class="text-sm mr-2">در حال شروع پردازش...</p>
                </div>
                    
                    <!-- Error Message Display (Initially Hidden) -->
                    <div id="error-message-display" class="hidden mt-4 text-center">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-red-700 mb-2">خطا در پردازش درخواست</h3>
                            <p id="error-message-text" class="text-red-600 text-sm leading-relaxed"></p>
                        </div>
                        
                    </div>
            </div>

            <!-- Process Steps - Collapsible -->
            <div class="border-t pt-4" id="steps-section">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-gray-800">مراحل پردازش</h3>
                    <button id="steps-toggle" class="flex items-center text-sm text-gray-600 hover:text-gray-800 transition-colors" onclick="toggleSteps()">
                        <span id="steps-toggle-text">بستن</span>
                        <svg id="steps-toggle-icon" class="w-4 h-4 mr-1 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Horizontal Steps Container -->
                <div id="steps-container" class="transition-all duration-300 ease-in-out overflow-hidden">
                    <!-- Desktop: Horizontal Layout -->
                    <div class="hidden md:flex items-center justify-between mb-4 bg-sky-50 rounded-lg p-3">
                        <div class="flex items-center space-x-4 space-x-reverse w-full">
                            <!-- Step 1 -->
                            <div class="flex flex-col items-center step-item" id="step-initializing">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mb-1">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-700 text-center leading-tight">شروع</span>
                                <div class="w-full h-0.5 bg-sky-300 mt-2 hidden step-connector"></div>
                            </div>

                            <!-- Connector -->
                            <div class="flex-1 h-0.5 bg-sky-300 mx-2"></div>

                            <!-- Step 2 -->
                            <div class="flex flex-col items-center step-item" id="step-authentication">
                                <div class="w-8 h-8 rounded-full bg-sky-100 flex items-center justify-center mb-1">
                                    <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                                </div>
                                <span class="text-xs text-gray-500 text-center leading-tight">ارسال درخواست</span>
                            </div>

                            <!-- Connector -->
                            <div class="flex-1 h-0.5 bg-sky-300 mx-2"></div>

                            <!-- Step 3 -->
                            <div class="flex flex-col items-center step-item" id="step-waiting_otp">
                                <div class="w-8 h-8 rounded-full bg-sky-100 flex items-center justify-center mb-1">
                                    <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                                </div>
                                <span class="text-xs text-gray-500 text-center leading-tight">کد تایید</span>
                            </div>

                            <!-- Connector -->
                            <div class="flex-1 h-0.5 bg-sky-300 mx-2"></div>

                            <!-- Step 4 -->
                            <div class="flex flex-col items-center step-item" id="step-completed">
                                <div class="w-8 h-8 rounded-full bg-sky-100 flex items-center justify-center mb-1">
                                    <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                                </div>
                                <span class="text-xs text-gray-500 text-center leading-tight">اتمام</span>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile: Compact Horizontal Layout -->
                    <div class="md:hidden flex items-center justify-between mb-4 bg-sky-50 rounded-lg p-2">
                        <div class="flex items-center space-x-2 space-x-reverse w-full">
                            <!-- Mobile Step Icons Only -->
                            <div class="flex flex-col items-center" id="mobile-step-initializing">
                                <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 h-0.5 bg-sky-300"></div>
                            <div class="flex flex-col items-center" id="mobile-step-authentication">
                                <div class="w-6 h-6 rounded-full bg-sky-100 flex items-center justify-center">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                </div>
                            </div>
                            <div class="flex-1 h-0.5 bg-sky-300"></div>
                            <div class="flex flex-col items-center" id="mobile-step-waiting_otp">
                                <div class="w-6 h-6 rounded-full bg-sky-100 flex items-center justify-center">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                </div>
                            </div>
                            <div class="flex-1 h-0.5 bg-sky-300"></div>
                            <div class="flex flex-col items-center" id="mobile-step-completed">
                                <div class="w-6 h-6 rounded-full bg-sky-100 flex items-center justify-center">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Timer Section -->
                <div class="border-t pt-3" id="timer-section">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span class="text-xs md:text-sm">زمان تخمینی باقی‌مانده:</span>
                        <div class="flex items-center space-x-1 space-x-reverse">
                            <span class="font-mono font-bold text-base md:text-lg" id="remaining-time">{{ $localRequest['estimated_remaining_time'] ?? 300 }}</span>
                            <span class="text-xs md:text-sm">ثانیه</span>
                        </div>
                    </div>
                </div>

                <!-- OTP Input Section (Initially Hidden) -->
                <div class="otp-container" id="otp-container">
                    <div class="border-t pt-6">
                        <div class="text-center mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-sky-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">کد تایید ارسال شد</h3>
                            <p class="text-gray-600 text-sm">
                                کد تایید ۵ رقمی به شماره موبایل شما ارسال شده است.<br>
                                لطفاً آن را در کادرهای زیر وارد کنید.
                            </p>
                        </div>

                        <!-- OTP Form -->
                        <form id="otp-form" class="space-y-6">
                            @csrf
                            <input type="hidden" name="hash" value="{{ $localRequest['hash'] }}">
                            
                            <!-- OTP Input Fields -->
                            <div class="flex justify-center space-x-2 space-x-reverse flex-row-reverse" dir="ltr" style="isolation: isolate; position: relative; z-index: 10000;">
                                <input type="tel" 
                                       class="otp-input w-12 h-12 text-center text-2xl font-bold border-2 border-gray-300 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300" 
                                       maxlength="1" 
                                       data-index="0" 
                                       autocomplete="one-time-code"
                                       autocorrect="off"
                                       autocapitalize="none"
                                       spellcheck="false"
                                       inputmode="numeric"
                                       pattern="[0-9]"
                                       name="otp_0">
                                <input type="tel" 
                                       class="otp-input w-12 h-12 text-center text-2xl font-bold border-2 border-gray-300 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300" 
                                       maxlength="1" 
                                       data-index="1" 
                                       autocomplete="off"
                                       autocorrect="off"
                                       autocapitalize="none"
                                       spellcheck="false"
                                       inputmode="numeric"
                                       pattern="[0-9]"
                                       name="otp_1">
                                <input type="tel" 
                                       class="otp-input w-12 h-12 text-center text-2xl font-bold border-2 border-gray-300 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300" 
                                       maxlength="1" 
                                       data-index="2" 
                                       autocomplete="off"
                                       autocorrect="off"
                                       autocapitalize="none"
                                       spellcheck="false"
                                       inputmode="numeric"
                                       pattern="[0-9]"
                                       name="otp_2">
                                <input type="tel" 
                                       class="otp-input w-12 h-12 text-center text-2xl font-bold border-2 border-gray-300 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300" 
                                       maxlength="1" 
                                       data-index="3" 
                                       autocomplete="off"
                                       autocorrect="off"
                                       autocapitalize="none"
                                       spellcheck="false"
                                       inputmode="numeric"
                                       pattern="[0-9]"
                                       name="otp_3">
                                <input type="tel" 
                                       class="otp-input w-12 h-12 text-center text-2xl font-bold border-2 border-gray-300 rounded-xl focus:border-sky-500 focus:ring-4 focus:ring-sky-100 transition-all duration-200 bg-white hover:border-sky-300" 
                                       maxlength="1" 
                                       data-index="4" 
                                       autocomplete="off"
                                       autocorrect="off"
                                       autocapitalize="none"
                                       spellcheck="false"
                                       inputmode="numeric"
                                       pattern="[0-9]"
                                       name="otp_4">
                            </div>

                            <input type="hidden" id="otp-combined" name="otp">

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" 
                                        id="otp-submit-btn"
                                        class="bg-sky-600 hover:bg-sky-700 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span id="submit-text">تایید کد</span>
                                    <svg class="animate-spin w-5 h-5 text-white ml-2 hidden" id="submit-spinner" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                            </div>

                            <!-- Error Display -->
                            <div id="otp-error" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-center text-sm"></div>
                        </form>

                        <!-- Resend OTP -->
                        <div class="text-center mt-4">
                            <button id="resend-otp-btn" 
                                    class="text-sky-600 hover:text-sky-700 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                <span id="resend-text">ارسال مجدد کد</span>
                                <span id="resend-timer" class="text-gray-500">(120)</span>
                        </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-sky-50 rounded-lg p-4 text-center mt-6">
            <p class="text-sky-800 text-sm">
                <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                این صفحه به صورت خودکار به‌روزرسانی می‌شود. نیازی به تازه‌سازی صفحه نیست.
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global variables
let requestHash = '{{ $localRequest['hash'] }}';
let serviceSlug = '{{ $service->slug }}';
let updateInterval;
let startTime = new Date();
let estimatedDuration = {{ $localRequest['estimated_remaining_time'] ?? 300 }};
let timerInterval;
let isOtpVisible = {{ $showOtpInput ? 'true' : 'false' }};
let hasOtpSubscriber = {{ $showOtpInput ? 'true' : 'false' }};
let resendTimer = 120;
let resendInterval;
let otpSubmitted = false;
let stepsCollapsed = false;

// Enhanced polling timeout settings
let pollingStartTime = new Date();
let maxPollingDuration = 10 * 60 * 1000; // 10 minutes instead of 90 seconds
let pollingTimeoutId;

// Start real-time updates
document.addEventListener('DOMContentLoaded', function() {
    // Initialize timer with actual remaining time from display
    const currentRemainingTime = parseInt(document.getElementById('remaining-time').textContent) || 0;
    estimatedDuration = currentRemainingTime;
    startTime = new Date();
    
    // Initialize steps container
    const stepsContainer = document.getElementById('steps-container');
    if (stepsContainer) {
        stepsContainer.style.maxHeight = '200px';
        stepsContainer.style.opacity = '1';
    }
    
    // Check initial state and show OTP if needed
    checkInitialStateAndShowOtp();
    
    // Add a delayed retry check for OTP in case the first check missed it
    setTimeout(() => {
        if (!isOtpVisible) {
            console.log('🔄 [DELAYED-CHECK] Performing delayed OTP check...');
            checkInitialStateAndShowOtp();
        }
    }, 2000);
    
    startProgressUpdates();
    startTimer();
});

/**
 * Check initial state and show OTP if needed
 */
async function checkInitialStateAndShowOtp() {
    console.log('🚀 [INITIAL-CHECK] Starting initial state check...', {
        isOtpVisible,
        requestHash,
        serviceSlug
    });
    
    // Setup OTP inputs if OTP is already visible from PHP
    if (isOtpVisible) {
        console.log('✅ [INITIAL-CHECK] OTP already visible from PHP, setting up...');
        
        // Ensure the OTP container has the 'show' class
        const otpContainer = document.getElementById('otp-container');
        if (otpContainer && !otpContainer.classList.contains('show')) {
            console.log('🔧 [INITIAL-CHECK] Adding show class to OTP container');
            otpContainer.classList.add('show');
        }
        
        // Reset submission state
        otpSubmitted = false;
        
        // Setup OTP functionality
        setupOtpSection();
        
        // If OTP is already visible, collapse steps for better focus
        autoCollapseStepsForOtp();
        
        // Focus first input after setup
        setTimeout(() => {
            const firstInput = document.querySelector('.otp-input');
            if (firstInput) {
                console.log('🎯 [INITIAL-CHECK] Focusing first OTP input');
                firstInput.focus();
            }
        }, 500);
        
        return;
    }
    
    console.log('🔍 [INITIAL-CHECK] OTP not visible from PHP, checking API state...');
    
    // If OTP is not visible, check current state from API
    try {
        const apiUrl = `/api/local-requests/${requestHash}/status`;
        console.log('📡 [INITIAL-CHECK] Making API call to:', apiUrl);
        
        const response = await fetch(apiUrl);
        console.log('📡 [INITIAL-CHECK] API response status:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`API call failed: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('🔍 [INITIAL-CHECK] API response data:', {
            step: data.step,
            requires_otp: data.requires_otp,
            status: data.status,
            is_completed: data.is_completed,
            is_failed: data.is_failed,
            current_message: data.current_message
        });
        
        // Check multiple conditions for OTP requirement
        const shouldShowOtp = data.requires_otp === true || 
                             data.step === 'waiting_otp' || 
                             (data.current_message && data.current_message.includes('کد تایید'));
        
        console.log('🎯 [INITIAL-CHECK] Should show OTP?', shouldShowOtp, {
            'data.requires_otp': data.requires_otp,
            'data.step': data.step,
            'message_contains_otp': data.current_message && data.current_message.includes('کد تایید')
        });
        
        if (shouldShowOtp) {
            console.log('✅ [INITIAL-CHECK] Showing OTP section on page load');
            showOtpSection();
        } else {
            console.log('❌ [INITIAL-CHECK] OTP not required based on current state');
        }
        
        // Update UI with initial state
        updateUI(data);
        
    } catch (error) {
        console.error('❌ [INITIAL-CHECK] Error checking initial state:', error);
        
        // Fallback: try to detect OTP need from page content
        console.log('🔄 [INITIAL-CHECK] Trying fallback detection...');
        const statusMessage = document.getElementById('status-message')?.textContent || '';
        const stepDescription = document.getElementById('current-step-description')?.textContent || '';
        
        if (statusMessage.includes('کد تایید') || stepDescription.includes('کد تایید') || 
            statusMessage.includes('در انتظار دریافت کد تایید')) {
            console.log('✅ [INITIAL-CHECK] Fallback detection: Found OTP keywords, showing OTP section');
            showOtpSection();
        } else {
            console.log('❌ [INITIAL-CHECK] Fallback detection: No OTP keywords found');
        }
    }
}

/**
 * Start progress updates via API polling with extended timeout
 */
function startProgressUpdates() {
    console.log('🚀 [POLLING] Starting polling with 10-minute timeout');
    
    // Reset polling start time
    pollingStartTime = new Date();
    
    // Clear any existing intervals and timeouts
    if (updateInterval) clearInterval(updateInterval);
    if (pollingTimeoutId) clearTimeout(pollingTimeoutId);
    
    // Start polling every 3 seconds
    updateInterval = setInterval(fetchStatus, 3000);
    
    // Set extended timeout (10 minutes)
    pollingTimeoutId = setTimeout(() => {
        console.log('⏰ [POLLING] 10-minute timeout reached, stopping polling');
        clearInterval(updateInterval);
        
        // Show timeout message to user
        const statusMessage = document.getElementById('status-message');
        if (statusMessage) {
            statusMessage.textContent = 'زمان انتظار به پایان رسید. لطفاً صفحه را تازه‌سازی کنید.';
        }
    }, maxPollingDuration);
    
    fetchStatus(); // Initial fetch
}

/**
 * Start countdown timer
 */
function startTimer() {
    if (timerInterval) {
        clearInterval(timerInterval);
    }
    timerInterval = setInterval(updateTimer, 1000);
}

/**
 * Update countdown timer
 */
function updateTimer() {
    const elapsed = Math.floor((new Date() - startTime) / 1000);
    const remaining = Math.max(0, estimatedDuration - elapsed);
    
    document.getElementById('remaining-time').textContent = remaining;
    
    if (remaining === 0) {
        clearInterval(timerInterval);
    }
}

/**
 * Fetch status from API
 */
async function fetchStatus() {
    try {
        const response = await fetch(`/api/local-requests/${requestHash}/status`);
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();

        console.log('🔍 [DEBUG] Full API response:', JSON.stringify(data, null, 2));
        updateUI(data);
        
        // Handle state changes
        console.log('🔍 [DEBUG] Status update:', {
            status: data.status,
            requires_otp: data.requires_otp,
            isOtpVisible: isOtpVisible,
            step: data.step,
            progress: data.progress,
            is_failed: data.is_failed,
            is_completed: data.is_completed
        });
        
        // Check for OTP error first - this takes priority and should allow retry
        if (data.step === 'otp_error' && data.requires_otp === true && data.error_data?.allowRetry === true) {
            // OTP error occurred but retry is allowed - show OTP section again for retry
            console.log('🔄 [OTP-ERROR] Detected OTP error with retry allowed - showing retry form');
            console.log('🔍 [OTP-ERROR] Details:', {
                step: data.step,
                progress: data.progress,
                message: data.current_message,
                allowRetry: data.error_data?.allowRetry,
                retryType: data.error_data?.retryType,
                isOtpVisible: isOtpVisible,
                otpSubmitted: otpSubmitted
            });
            
            // DON'T stop polling! We need to continue monitoring for retry attempts
            console.log('✅ [OTP-ERROR] Keeping polling active for retry monitoring');
            
            // Show retry form
            const retryMessage = data.error_data?.retryMessage || data.current_message || 'کد تایید اشتباه است. لطفاً مجدداً تلاش کنید.';
            showOtpSectionForRetry(retryMessage);
            
        } else if (data.requires_otp && !isOtpVisible) {
            // OTP is now required - show OTP section
            console.log('🎯 [OTP-REQUIRED] Showing OTP section because requires_otp=true');
            showOtpSection();
        } else if (data.is_completed || (data.is_failed && !data.requires_otp)) {
            // Process completed or truly failed (not retryable OTP errors)
            console.log('🏁 [STATUS-UPDATE] Process finished - stopping polling:', {
                is_completed: data.is_completed,
                is_failed: data.is_failed,
                requires_otp: data.requires_otp,
                step: data.step
            });
            
            clearInterval(updateInterval);
            clearInterval(timerInterval);
            
            if (data.is_failed) {
                handleFailedState(data);
            } else if (data.is_completed) {
                handleCompletedState(data);
            }
        } else if (data.is_failed && data.requires_otp) {
            // Failed but retryable OTP error - keep polling but update UI
            console.log('⚠️ [STATUS-UPDATE] Failed state but OTP retry allowed - continuing polling');
            updateUI(data);
        }
        
    } catch (error) {
        console.error('Error fetching status:', error);
        // Continue trying, don't stop on network errors
    }
}

/**
 * Update UI with new status data
 */
function updateUI(data) {
    // Update progress
    updateProgress(data.progress || 0);
    
    // Update status message (but don't show error messages in blue header for failed states)
    if (data.is_failed) {
        updateStatusMessage('درخواست با خطا مواجه شد');
    } else {
        updateStatusMessage(data.current_message || 'در حال پردازش...');
    }
    
    // Update step indicators
    const errorMessage = data.is_failed ? (data.error_data?.message || data.current_message) : null;
    updateStepIndicators(data.step || 'initializing', data.is_failed, errorMessage);
    
    // Update status container (error details will be shown here for failed states)
    updateStatusContainer(data);
}

/**
 * Update progress bar and percentage
 */
function updateProgress(progress) {
    document.getElementById('progress-percentage').textContent = progress + '%';
    document.getElementById('progress-bar').style.width = progress + '%';
}

/**
 * Update status message (but don't show error messages here - they go in status-container)
 */
function updateStatusMessage(message) {
    document.getElementById('status-message').textContent = message;
}

/**
 * Update status container based on state
 */
function updateStatusContainer(data) {
    const container = document.getElementById('status-container');
    const spinner = document.getElementById('progress-spinner');
    const timerSection = document.getElementById('timer-section');
    const errorMessageDisplay = document.getElementById('error-message-display');
    const errorMessageText = document.getElementById('error-message-text');
    
    // Reset classes
    container.classList.remove('bg-red-50', 'border-2', 'border-red-200', 'bg-green-50', 'border-2', 'border-green-200', 'bg-yellow-50', 'border-2', 'border-yellow-200');
    
    // Hide error message by default
    if (errorMessageDisplay) {
        errorMessageDisplay.classList.add('hidden');
    }
    
    if (data.is_failed) {
        // Hide timer for failed state
        if (timerSection) {
            timerSection.style.display = 'none';
        }
        
        // Check error type for different styling
        const errorMessage = data.error_data?.message || data.current_message || 'خطای نامشخصی رخ داده است';
        const isOtpTimeoutError = errorMessage.includes('زمان انتظار کد تایید به پایان رسید');
        
        if (isOtpTimeoutError) {
            // OTP timeout error - yellow with alert icon
            container.classList.add('bg-yellow-50', 'border-2', 'border-yellow-200');
            spinner.classList.add('text-yellow-600');
            
            // Replace spinner with alert icon
            spinner.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
            `;
        } else {
            // Other errors - red with X icon
            container.classList.add('bg-red-50', 'border-2', 'border-red-200');
            spinner.classList.add('text-red-600');
            
            // Replace spinner with error icon
            spinner.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            `;
        }
        
        spinner.setAttribute('fill', 'none');
        spinner.setAttribute('viewBox', '0 0 24 24');
        spinner.setAttribute('stroke', 'currentColor');
        spinner.classList.remove('animate-spin');
        
        // Show error message prominently in status container
        if (errorMessageDisplay && errorMessageText) {
            errorMessageText.textContent = errorMessage;
            errorMessageDisplay.classList.remove('hidden');
        }
        
    } else if (data.is_completed) {
        // Hide timer for completed state
        if (timerSection) {
            timerSection.style.display = 'none';
        }
        
        // Success state - green
        container.classList.add('bg-green-50', 'border-2', 'border-green-200');
        spinner.classList.add('text-green-600');
        
        // Replace spinner with checkmark
        spinner.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        `;
        spinner.setAttribute('fill', 'none');
        spinner.setAttribute('viewBox', '0 0 24 24');
        spinner.setAttribute('stroke', 'currentColor');
        spinner.classList.remove('animate-spin');
        
    } else if (data.requires_otp) {
        // OTP required state - yellow/blue
        container.classList.add('bg-yellow-50', 'border-2', 'border-yellow-200');
        spinner.classList.add('text-yellow-600');
        
        // Replace spinner with key icon
        spinner.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
        `;
        spinner.setAttribute('fill', 'none');
        spinner.setAttribute('viewBox', '0 0 24 24');
        spinner.setAttribute('stroke', 'currentColor');
        spinner.classList.remove('animate-spin');
    }
}

/**
 * Toggle steps section collapse/expand
 */
function toggleSteps() {
    const container = document.getElementById('steps-container');
    const toggleText = document.getElementById('steps-toggle-text');
    const toggleIcon = document.getElementById('steps-toggle-icon');
    
    stepsCollapsed = !stepsCollapsed;
    
    if (stepsCollapsed) {
        container.style.maxHeight = '0px';
        container.style.opacity = '0';
        toggleText.textContent = 'نمایش';
        toggleIcon.style.transform = 'rotate(180deg)';
    } else {
        container.style.maxHeight = '200px';
        container.style.opacity = '1';
        toggleText.textContent = 'بستن';
        toggleIcon.style.transform = 'rotate(0deg)';
    }
}

/**
 * Auto-collapse steps when OTP is shown
 */
function autoCollapseStepsForOtp() {
    if (!stepsCollapsed) {
        toggleSteps();
    }
}

/**
 * Update step indicators for both desktop and mobile
 */
function updateStepIndicators(currentStep, isError = false, errorMessage = null) {
    const steps = [
        'initializing', 
        'authentication', 
        'waiting_otp',
        'otp_error',
        'completed'
    ];
    
    // Update current step description
    updateCurrentStepDescription(currentStep, isError, errorMessage);
    
    steps.forEach(step => {
        // Update desktop version
        const desktopElement = document.getElementById(`step-${step}`);
        if (desktopElement) {
            updateStepElement(desktopElement, step, currentStep, steps, isError, 'desktop', errorMessage);
        }
        
        // Update mobile version
        const mobileElement = document.getElementById(`mobile-step-${step}`);
        if (mobileElement) {
            updateStepElement(mobileElement, step, currentStep, steps, isError, 'mobile', errorMessage);
        }
    });
}

/**
 * Update individual step element
 */
function updateStepElement(element, step, currentStep, steps, isError, variant = 'desktop', errorMessage = null) {
    const iconContainer = element.querySelector('div');
    const text = element.querySelector('span');
    
    // Determine sizes based on variant
    const iconSize = variant === 'mobile' ? 'w-6 h-6' : 'w-8 h-8';
    const innerIconSize = variant === 'mobile' ? 'w-3 h-3' : 'w-4 h-4';
    const dotSize = variant === 'mobile' ? 'w-2 h-2' : 'w-3 h-3';
    
    if (step === currentStep) {
        // Special handling for otp_error step - always show as warning
        if (step === 'otp_error') {
            iconContainer.className = `${iconSize} rounded-full bg-yellow-100 flex items-center justify-center ${variant === 'desktop' ? 'mb-1' : ''}`;
            iconContainer.innerHTML = `<svg class="${innerIconSize} text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>`;
            if (text) text.className = 'text-xs text-yellow-700 text-center leading-tight font-medium';
        } else if (isError) {
            // Check if this is OTP timeout error
            const isOtpTimeoutError = errorMessage && errorMessage.includes('زمان انتظار کد تایید به پایان رسید');
            
            if (isOtpTimeoutError) {
                // OTP timeout - yellow with alert icon
                iconContainer.className = `${iconSize} rounded-full bg-yellow-100 flex items-center justify-center ${variant === 'desktop' ? 'mb-1' : ''}`;
                iconContainer.innerHTML = `<svg class="${innerIconSize} text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>`;
                if (text) text.className = 'text-xs text-yellow-700 text-center leading-tight font-medium';
            } else {
                // Other errors - red with X icon
                iconContainer.className = `${iconSize} rounded-full bg-red-100 flex items-center justify-center ${variant === 'desktop' ? 'mb-1' : ''}`;
                iconContainer.innerHTML = `<svg class="${innerIconSize} text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>`;
                if (text) text.className = 'text-xs text-red-700 text-center leading-tight font-medium';
            }
        } else {
            // Current step - blue with pulsing icon
            iconContainer.className = `${iconSize} rounded-full bg-sky-100 flex items-center justify-center ${variant === 'desktop' ? 'mb-1' : ''}`;
            iconContainer.innerHTML = `<div class="${dotSize} bg-sky-600 rounded-full animate-pulse"></div>`;
            if (text) text.className = 'text-xs text-sky-700 text-center leading-tight font-medium';
        }
    } else if (steps.indexOf(step) < steps.indexOf(currentStep)) {
        // Completed step - green with checkmark
        iconContainer.className = `${iconSize} rounded-full bg-green-100 flex items-center justify-center ${variant === 'desktop' ? 'mb-1' : ''}`;
        iconContainer.innerHTML = `<svg class="${innerIconSize} text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>`;
        if (text) text.className = 'text-xs text-green-700 text-center leading-tight';
    } else {
        // Future step - gray
        iconContainer.className = `${iconSize} rounded-full bg-sky-100 flex items-center justify-center ${variant === 'desktop' ? 'mb-1' : ''}`;
        iconContainer.innerHTML = `<div class="${dotSize} bg-gray-400 rounded-full"></div>`;
        if (text) text.className = 'text-xs text-gray-500 text-center leading-tight';
    }
}

/**
 * Update current step description
 */
function updateCurrentStepDescription(currentStep, isError = false, errorMessage = null) {
    const descriptionElement = document.getElementById('current-step-description');
    if (!descriptionElement) return;
    
    // Define step descriptions first
    const stepDescriptions = {
        'initializing': 'شروع پردازش درخواست...',
        'authentication': 'ارسال درخواست به درگاه دولت هوشمند...',
        'waiting_otp': 'در انتظار دریافت کد تایید...',
        'otp_error': 'کد تایید اشتباه است - لطفاً مجدداً تلاش کنید',
        'completed': 'پردازش با موفقیت تکمیل شد'
    };
    
    // Special handling for otp_error step - show as warning, not error
    if (currentStep === 'otp_error') {
        descriptionElement.textContent = stepDescriptions[currentStep] || 'کد تایید اشتباه است - لطفاً مجدداً تلاش کنید';
        descriptionElement.className = 'text-sm text-yellow-600 font-medium';
        return;
    }
    
    if (isError) {
        // Check if this is OTP timeout error
        const isOtpTimeoutError = errorMessage && errorMessage.includes('زمان انتظار کد تایید به پایان رسید');
        
        if (isOtpTimeoutError) {
            descriptionElement.textContent = 'زمان انتظار کد تایید به پایان رسید';
            descriptionElement.className = 'text-sm text-yellow-600 font-medium';
        } else {
            descriptionElement.textContent = 'درخواست با خطا مواجه شد';
            descriptionElement.className = 'text-sm text-red-600 font-medium';
        }
        return;
    }
    
    descriptionElement.textContent = stepDescriptions[currentStep] || 'در حال پردازش...';
    descriptionElement.className = 'text-sm text-gray-600';
}

/**
 * Show OTP section with animation
 */
function showOtpSection() {
    console.log('🎯 [SHOW-OTP] Attempting to show OTP section...', {
        isOtpVisible,
        containerExists: !!document.getElementById('otp-container')
    });
    
    isOtpVisible = true;
    const otpContainer = document.getElementById('otp-container');
    
    if (!otpContainer) {
        console.error('❌ [SHOW-OTP] OTP container not found in DOM!');
        return;
    }
    
    // Check if already visible
    if (otpContainer.classList.contains('show')) {
        console.log('ℹ️ [SHOW-OTP] OTP section already visible');
        return;
    }
    
    console.log('✅ [SHOW-OTP] Adding show class to OTP container');
    otpContainer.classList.add('show');
    
    // Auto-collapse steps to give more focus to OTP
    autoCollapseStepsForOtp();
    
    // Setup OTP functionality
    setupOtpSection();
    
    // Focus first input
    setTimeout(() => {
        const firstInput = document.querySelector('.otp-input');
        if (firstInput) {
            console.log('🎯 [SHOW-OTP] Focusing first OTP input');
            firstInput.focus();
        } else {
            console.warn('⚠️ [SHOW-OTP] First OTP input not found for focus');
        }
    }, 500);
    
    console.log('✅ [SHOW-OTP] OTP section setup complete');
}

/**
 * Show OTP section for retry with error message
 */
function showOtpSectionForRetry(errorMessage) {
    console.log('🔄 [SHOW-OTP-RETRY] Showing OTP section for retry...', {
        errorMessage,
        isOtpVisible,
        otpSubmitted,
        containerExists: !!document.getElementById('otp-container')
    });
    
    // CRITICAL: Reset OTP submission state to allow retry
    otpSubmitted = false;
    
    // Show the OTP section if not already visible
    if (!isOtpVisible) {
        console.log('🔄 [SHOW-OTP-RETRY] OTP section was hidden, showing it...');
        showOtpSection();
    } else {
        console.log('ℹ️ [SHOW-OTP-RETRY] OTP section already visible, just resetting form...');
        // Ensure OTP container is visible even if marked as visible
        const otpContainer = document.getElementById('otp-container');
        if (otpContainer && !otpContainer.classList.contains('show')) {
            otpContainer.classList.add('show');
        }
    }
    
    // Clear previous OTP inputs aggressively
    const inputs = document.querySelectorAll('.otp-input');
    console.log('🔄 [SHOW-OTP-RETRY] Clearing', inputs.length, 'OTP inputs');
    inputs.forEach(input => {
        input.value = '';
        input.disabled = false;
        input.readOnly = false;
        input.style.opacity = '1';
        input.style.pointerEvents = 'auto';
        input.style.backgroundColor = 'white';
    });
    
    // Clear combined OTP
    const combinedInput = document.getElementById('otp-combined');
    if (combinedInput) {
        combinedInput.value = '';
    }
    updateCombinedOtp();
    
    // Show error message with retry context
    console.log('🔄 [SHOW-OTP-RETRY] Showing error message:', errorMessage);
    showOtpError(errorMessage);
    
    // Restore loading state (remove any processing overlay)
    console.log('🔄 [SHOW-OTP-RETRY] Restoring form state...');
    showOtpLoading(false);
    
    // Enable submit button
    const submitBtn = document.getElementById('otp-submit-btn');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.querySelector('#submit-text').textContent = 'تایید کد';
        submitBtn.querySelector('#submit-spinner').classList.add('hidden');
    }
    
    // Re-setup OTP functionality to ensure event listeners work
    console.log('🔄 [SHOW-OTP-RETRY] Re-initializing OTP system...');
    setupOtpInputs();
    setupOtpForm();
    
    // Focus first input with multiple attempts
    setTimeout(() => {
        const firstInput = document.querySelector('.otp-input');
        if (firstInput) {
            console.log('🎯 [SHOW-OTP-RETRY] Focusing first OTP input for retry');
            firstInput.focus();
        } else {
            console.warn('⚠️ [SHOW-OTP-RETRY] Could not find first OTP input to focus');
        }
    }, 500);
    
    console.log('✅ [SHOW-OTP-RETRY] OTP section ready for retry');
}

/**
 * Setup OTP section functionality
 */
function setupOtpSection() {
    setupOtpInputs();
    setupOtpForm();
    startResendTimer();
}

/**
 * Setup OTP input handling - ULTRA ROBUST VERSION
 */
function setupOtpInputs() {
    console.log('🔧 [SETUP-OTP-INPUTS] Starting ULTRA ROBUST setup...');
    
    // Store reference to prevent conflicts
    window.otpManager = {
        inputs: [],
        initialized: false,
        processing: false
    };
    
    // First, remove ALL existing event listeners aggressively
    const existingInputs = document.querySelectorAll('.otp-input');
    existingInputs.forEach((input, index) => {
        // Create completely fresh input element
        const newInput = document.createElement('input');
        
        // Copy all attributes
        for (let attr of input.attributes) {
            newInput.setAttribute(attr.name, attr.value);
        }
        
        // Copy classes
        newInput.className = input.className;
        newInput.value = '';
        
        // Replace in DOM
        input.parentNode.replaceChild(newInput, input);
        
        console.log(`🔄 [SETUP-OTP-INPUTS] Replaced input ${index} with fresh element`);
    });
    
    // Wait a moment for DOM to settle
    setTimeout(() => {
        const inputs = document.querySelectorAll('.otp-input');
        console.log('🔧 [SETUP-OTP-INPUTS] Found fresh inputs:', inputs.length);
        
        inputs.forEach((input, index) => {
            if (!input) {
                console.error('❌ [SETUP-OTP-INPUTS] Input not found for index', index);
                return;
            }
            
            // Store reference
            window.otpManager.inputs[index] = input;
            
            console.log(`🔧 [SETUP-OTP-INPUTS] Setting up ULTRA ROBUST input ${index}`);
            
            // Clear and prepare input
            input.value = '';
            input.disabled = false;
            input.readOnly = false;
            
            // AGGRESSIVE input handler with multiple fallbacks
            const robustInputHandler = function(e) {
                // Prevent browser extension interference
                e.stopImmediatePropagation();
                
                // Prevent processing if already processing
                if (window.otpManager.processing) {
                    console.log(`⚠️ [OTP-INPUT-${index}] Already processing, skipping...`);
                    return;
                }
                
                window.otpManager.processing = true;
                
                console.log(`📝 [ROBUST-INPUT-${index}] Event: ${e.type}, value: "${e.target.value}"`);
                
                // Multiple cleanup attempts
                let value = e.target.value || '';
                value = value.replace(/[^0-9]/g, '');
                
                if (value.length > 1) {
                    value = value.slice(-1);
                }
                
                // Force set value multiple ways
                e.target.value = value;
                e.target.setAttribute('value', value);
                
                console.log(`📝 [ROBUST-INPUT-${index}] Final value: "${value}"`);
                
                // Update combined OTP
                updateCombinedOtp();
                
                // Auto advance with multiple fallback methods
                if (value && index < 4) {
                    console.log(`➡️ [ROBUST-INPUT-${index}] Auto-advancing to field ${index + 1}`);
                    
                    const advanceToNext = () => {
                        const nextInput = document.querySelector(`input[data-index="${index + 1}"]`) || 
                                         window.otpManager.inputs[index + 1];
                        
                        if (nextInput) {
                            // Multiple focus methods for maximum compatibility
                            nextInput.focus();
                            nextInput.click();
                            nextInput.select();
                            
                            // Force focus with setTimeout as backup
                            setTimeout(() => {
                                if (document.activeElement !== nextInput) {
                                    nextInput.focus();
                                    console.log(`🔄 [ROBUST-INPUT-${index}] Backup focus triggered`);
                                }
                            }, 10);
                            
                            console.log(`✅ [ROBUST-INPUT-${index}] Advanced to field ${index + 1}`);
                        } else {
                            console.error(`❌ [ROBUST-INPUT-${index}] Next input not found`);
                        }
                    };
                    
                    // Try immediate advance
                    requestAnimationFrame(advanceToNext);
                    
                    // Backup advance
                    setTimeout(advanceToNext, 50);
                }
                
                // Reset processing flag
                setTimeout(() => {
                    window.otpManager.processing = false;
                }, 100);
            };
            
            // Add multiple event listeners with high priority
            input.addEventListener('input', robustInputHandler, { passive: false, capture: true });
            input.addEventListener('change', robustInputHandler, { passive: false, capture: true });
            input.addEventListener('keyup', robustInputHandler, { passive: false, capture: true });
            
            // Direct property assignment as fallback
            input.oninput = robustInputHandler;
            
            // Enhanced keydown handler
            input.addEventListener('keydown', function(e) {
                console.log(`⌨️ [ROBUST-INPUT-${index}] Keydown: "${e.key}"`);
                
                // Aggressive event stopping for special keys
                if (['Backspace', 'ArrowLeft', 'ArrowRight', 'Delete'].includes(e.key)) {
                    e.stopImmediatePropagation();
                }
                
                if (e.key === 'Backspace') {
                    if (!e.target.value && index > 0) {
                        const prevInput = document.querySelector(`input[data-index="${index - 1}"]`) || 
                                         window.otpManager.inputs[index - 1];
                        if (prevInput) {
                            prevInput.focus();
                            prevInput.value = '';
                            updateCombinedOtp();
                        }
                    }
                }
                
                // Number key direct handling
                if (/^[0-9]$/.test(e.key)) {
                    e.preventDefault();
                    e.target.value = e.key;
                    robustInputHandler(e);
                }
                
                // Arrow key navigation
                if (e.key === 'ArrowRight' && index < 4) {
                    const nextInput = window.otpManager.inputs[index + 1];
                    if (nextInput) nextInput.focus();
                }
                
                if (e.key === 'ArrowLeft' && index > 0) {
                    const prevInput = window.otpManager.inputs[index - 1];
                    if (prevInput) prevInput.focus();
                }
            }, { passive: false, capture: true });
            
            // Enhanced paste handler
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                
                const pastedData = e.clipboardData.getData('text');
                const digits = pastedData.replace(/[^0-9]/g, '').slice(0, 5);
                
                if (digits.length > 0) {
                    console.log('📋 [ROBUST-PASTE] Distributing digits:', digits);
                    
                    for (let i = 0; i < Math.min(digits.length, 5); i++) {
                        const targetInput = window.otpManager.inputs[i];
                        if (targetInput) {
                            targetInput.value = digits[i];
                        }
                    }
                    
                    updateCombinedOtp();
                    
                    const nextEmptyIndex = Math.min(digits.length, 4);
                    if (window.otpManager.inputs[nextEmptyIndex]) {
                        window.otpManager.inputs[nextEmptyIndex].focus();
                    }
                }
            }, { passive: false, capture: true });
            
            console.log(`✅ [SETUP-OTP-INPUTS] ULTRA ROBUST input ${index} setup complete`);
        });
        
        // Focus first input with multiple attempts
        const focusFirst = () => {
            const firstInput = window.otpManager.inputs[0];
            if (firstInput) {
                firstInput.focus();
                firstInput.click();
                console.log('🎯 [SETUP-OTP-INPUTS] Focused first input');
            }
        };
        
        requestAnimationFrame(focusFirst);
        setTimeout(focusFirst, 100);
        
        window.otpManager.initialized = true;
        console.log('✅ [SETUP-OTP-INPUTS] ULTRA ROBUST setup complete');
        
        // Advanced testing function
        window.testOTPRobust = function() {
            console.log('🧪 [ROBUST-TEST] Testing OTP system...');
            
            // Test each input
            window.otpManager.inputs.forEach((input, index) => {
                if (input) {
                    console.log(`🧪 [ROBUST-TEST] Testing input ${index}`);
                    input.value = (index + 1).toString();
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
            
            setTimeout(() => {
                const combined = document.getElementById('otp-combined');
                console.log('🧪 [ROBUST-TEST] Combined result:', combined ? combined.value : 'not found');
            }, 100);
        };
        
        // Auto-recovery system
        setInterval(() => {
            if (window.otpManager.initialized) {
                const inputs = document.querySelectorAll('.otp-input');
                if (inputs.length !== window.otpManager.inputs.length) {
                    console.log('🔄 [AUTO-RECOVERY] Detected input count mismatch, re-initializing...');
                    setupOtpInputs();
                }
            }
        }, 5000);
        
        // SUPER DIAGNOSTIC FUNCTION
        window.diagnosOTP = function() {
            console.log('🔍 [DIAGNOSTIC] Starting comprehensive OTP diagnosis...');
            
            // Check DOM state
            const inputs = document.querySelectorAll('.otp-input');
            console.log('🔍 [DIAGNOSTIC] Found inputs in DOM:', inputs.length);
            
            inputs.forEach((input, index) => {
                console.log(`🔍 [DIAGNOSTIC] Input ${index}:`, {
                    element: input,
                    value: input.value,
                    disabled: input.disabled,
                    readOnly: input.readOnly,
                    focused: document.activeElement === input,
                    style: input.style.cssText,
                    computed: window.getComputedStyle(input).getPropertyValue('pointer-events'),
                    zIndex: window.getComputedStyle(input).getPropertyValue('z-index'),
                    position: window.getComputedStyle(input).getPropertyValue('position')
                });
            });
            
            // Check otpManager state
            console.log('🔍 [DIAGNOSTIC] OTP Manager:', window.otpManager);
            
            // Check for event listener interference
            const firstInput = inputs[0];
            if (firstInput) {
                console.log('🔍 [DIAGNOSTIC] Testing event simulation on first input...');
                
                // Simulate typing '1'
                firstInput.focus();
                firstInput.value = '1';
                
                // Dispatch multiple event types
                firstInput.dispatchEvent(new Event('input', { bubbles: true }));
                firstInput.dispatchEvent(new Event('change', { bubbles: true }));
                firstInput.dispatchEvent(new KeyboardEvent('keyup', { key: '1', bubbles: true }));
                
                setTimeout(() => {
                    console.log('🔍 [DIAGNOSTIC] After simulation - Active element:', document.activeElement);
                    console.log('🔍 [DIAGNOSTIC] After simulation - Values:', 
                        Array.from(inputs).map(inp => inp.value));
                }, 100);
            }
            
            // Check for global conflicts
            console.log('🔍 [DIAGNOSTIC] Checking for global interference...');
            console.log('🔍 [DIAGNOSTIC] Window event listeners:', 
                Object.keys(window).filter(key => key.includes('event') || key.includes('listener')));
            
            // Check document event listeners
            const proto = EventTarget.prototype;
            const addListener = proto.addEventListener;
            let listenerCount = 0;
            
            proto.addEventListener = function(type, listener, options) {
                if (type === 'input' || type === 'keydown' || type === 'focus') {
                    listenerCount++;
                    console.log(`🔍 [DIAGNOSTIC] Found ${type} listener #${listenerCount} on:`, this);
                }
                return addListener.call(this, type, listener, options);
            };
            
            console.log('🔍 [DIAGNOSTIC] Diagnosis complete!');
        };
        
    }, 100);
}

/**
 * Update combined OTP value
 */
function updateCombinedOtp() {
    const inputs = document.querySelectorAll('.otp-input');
    console.log('🔄 [UPDATE-COMBINED] Found', inputs.length, 'inputs');
    
    // Get values in correct order based on data-index
    const values = [];
    for (let i = 0; i < 5; i++) {
        const input = document.querySelector(`input[data-index="${i}"]`);
        if (input) {
            values[i] = input.value || '';
            console.log(`🔄 [UPDATE-COMBINED] Input ${i}:`, input.value);
        }
    }
    
    const combined = values.join('');
    document.getElementById('otp-combined').value = combined;
    
    console.log('🔄 [UPDATE-COMBINED] Combined value:', combined, 'length:', combined.length);
}

/**
 * Setup OTP form submission
 */
function setupOtpForm() {
    const form = document.getElementById('otp-form');
    
    // Remove existing event listeners to prevent duplicates
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);
    
    // Add fresh event listener
    document.getElementById('otp-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        console.log('📝 [OTP-FORM] Form submitted, otpSubmitted state:', otpSubmitted);
        
        if (otpSubmitted) {
            console.log('⚠️ [OTP-FORM] Already submitted, ignoring');
            return; // Prevent double submission
        }
        
        await submitOtp();
    });
    
    // Also setup resend button
    const resendBtn = document.getElementById('resend-otp-btn');
    if (resendBtn) {
        // Remove existing event listeners
        const newResendBtn = resendBtn.cloneNode(true);
        resendBtn.parentNode.replaceChild(newResendBtn, resendBtn);
        
        // Add fresh event listener
        document.getElementById('resend-otp-btn').addEventListener('click', resendOtp);
    }
}

/**
 * Submit OTP via AJAX
 */
async function submitOtp() {
    console.log('📝 [SUBMIT-OTP] Starting OTP submission, current state:', {
        otpSubmitted: otpSubmitted,
        isOtpVisible: isOtpVisible
    });
    
    if (otpSubmitted) {
        console.log('⚠️ [SUBMIT-OTP] Already submitted, aborting');
        return;
    }
    
    const otpValue = document.getElementById('otp-combined').value;
    console.log('📝 [SUBMIT-OTP] OTP value length:', otpValue.length);
    
    if (otpValue.length !== 5) {
        showOtpError('لطفاً کد ۵ رقمی را کامل وارد کنید');
        return;
    }
    
    try {
        otpSubmitted = true;
        console.log('📝 [SUBMIT-OTP] Set otpSubmitted = true');
        showOtpLoading(true);
        hideOtpError();
        
        // Prepare form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('hash', requestHash);
        formData.append('otp', otpValue);
        
        console.log('📝 [SUBMIT-OTP] Sending request to:', `/services/${serviceSlug}/progress/${requestHash}`);
        
        // Submit OTP to Laravel (which publishes to Redis channel)
        const response = await fetch(`/services/${serviceSlug}/progress/${requestHash}`, {
            method: 'POST',
            body: formData,
            redirect: 'manual'
        });
        
        console.log('📝 [SUBMIT-OTP] Response status:', response.status, 'type:', response.type);
        
        if (response.status === 0 || response.type === 'opaqueredirect') {
            // Laravel redirected us (success case)
            console.log('✅ [SUBMIT-OTP] Success - redirected');
            showOtpSuccess('کد تایید ارسال شد. در حال پردازش...');
            
            // Hide OTP section and continue with progress updates
            hideOtpSection();
            
        } else if (response.ok) {
            // Should not happen normally
            showOtpSuccess('کد تایید ارسال شد. در حال پردازش...');
            hideOtpSection();
            
        } else {
            // Handle error response
            const result = await response.json().catch(() => ({}));
            console.log('❌ [SUBMIT-OTP] Error response:', result.message || 'خطا در ارسال کد تایید');
            
            // Show error but don't hide OTP section - let the polling handle state changes
            showOtpError(result.message || 'خطا در ارسال کد تایید');
            
            otpSubmitted = false;
            showOtpLoading(false);
        }
        
    } catch (error) {
        console.error('❌ [SUBMIT-OTP] Network error:', error);
        
        // Show error but don't hide OTP section
        showOtpError('خطا در ارتباط با سرور');
        
        otpSubmitted = false;
        showOtpLoading(false);
    }
}

/**
 * Hide OTP section after successful submission
 */
function hideOtpSection() {
    const otpContainer = document.getElementById('otp-container');
    otpContainer.classList.remove('show');
    isOtpVisible = false;
}

/**
 * Show OTP loading state
 */
function showOtpLoading(loading) {
    const submitBtn = document.getElementById('otp-submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitSpinner = document.getElementById('submit-spinner');
    const otpInputs = document.querySelectorAll('.otp-input');
    const resendBtn = document.getElementById('resend-otp-btn');
    const otpForm = document.getElementById('otp-form');
    const otpContainer = document.getElementById('otp-container');
    
    if (loading) {
        // Disable and blur form elements
        submitBtn.disabled = true;
        submitText.textContent = 'لطفاً صبر کنید...';
        submitSpinner.classList.remove('hidden');
        
        // Blur and disable OTP inputs
        otpInputs.forEach(input => {
            input.disabled = true;
            input.style.opacity = '0.5';
            input.style.pointerEvents = 'none';
        });
        
        // Disable resend button
        if (resendBtn) {
            resendBtn.disabled = true;
            resendBtn.style.opacity = '0.5';
            resendBtn.style.pointerEvents = 'none';
        }
        
        // Add processing overlay effect
        if (otpContainer) {
            otpContainer.style.position = 'relative';
            if (!document.getElementById('otp-processing-overlay')) {
                const overlay = document.createElement('div');
                overlay.id = 'otp-processing-overlay';
                overlay.className = 'absolute left-0 right-0 top-0 bottom-0 inset-0 bg-white bg-opacity-50 flex items-center justify-center z-10 rounded-lg';
                overlay.innerHTML = `
                    <div class="text-center">
                        <svg class="animate-spin w-8 h-8 text-sky-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm text-gray-600 font-medium">در حال پردازش کد تایید...</p>
                    </div>
                `;
                otpContainer.appendChild(overlay);
            }
        }
    } else {
        // Restore form elements
        submitBtn.disabled = false;
        submitText.textContent = 'تایید کد';
        submitSpinner.classList.add('hidden');
        
        // Restore OTP inputs
        otpInputs.forEach(input => {
            input.disabled = false;
            input.style.opacity = '1';
            input.style.pointerEvents = 'auto';
        });
        
        // Restore resend button
        if (resendBtn) {
            resendBtn.style.opacity = '1';
            resendBtn.style.pointerEvents = 'auto';
            // Don't enable resend button if timer is still running
        }
        
        // Remove processing overlay
        const overlay = document.getElementById('otp-processing-overlay');
        if (overlay) {
            overlay.remove();
        }
    }
}

/**
 * Show OTP error message
 */
function showOtpError(message) {
    const errorDiv = document.getElementById('otp-error');
    errorDiv.textContent = message;
    errorDiv.classList.remove('hidden');
}

/**
 * Hide entire OTP section with smooth animation (for errors)
 */
function hideOtpSectionOnError() {
    const otpContainer = document.getElementById('otp-container');
    if (otpContainer && otpContainer.classList.contains('show')) {
        // Add fade out animation
        otpContainer.style.transition = 'all 0.5s ease-out';
        otpContainer.style.opacity = '0';
        otpContainer.style.transform = 'translateY(-20px)';
        
        setTimeout(() => {
            otpContainer.classList.remove('show');
            otpContainer.style.opacity = '';
            otpContainer.style.transform = '';
            otpContainer.style.transition = '';
            isOtpVisible = false;
        }, 500);
    }
}

/**
 * Hide OTP error message
 */
function hideOtpError() {
    const errorDiv = document.getElementById('otp-error');
    errorDiv.classList.add('hidden');
}

/**
 * Show OTP success message
 */
function showOtpSuccess(message) {
    updateStatusMessage(message);
}

/**
 * Start resend timer
 */
function startResendTimer() {
    const resendBtn = document.getElementById('resend-otp-btn');
    const resendText = document.getElementById('resend-text');
    const resendTimerSpan = document.getElementById('resend-timer');
    
    resendTimer = 120;
    resendBtn.disabled = true;
    
    resendInterval = setInterval(() => {
        resendTimer--;
        resendTimerSpan.textContent = `(${resendTimer})`;
        
        if (resendTimer <= 0) {
            clearInterval(resendInterval);
            resendBtn.disabled = false;
            resendTimerSpan.textContent = '';
        }
    }, 1000);
    
    // Setup resend functionality
    resendBtn.addEventListener('click', resendOtp);
}

/**
 * Resend OTP
 */
async function resendOtp() {
    try {
        const response = await fetch(`/api/local-requests/${requestHash}/resend-otp`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showOtpSuccess('کد تایید مجدد ارسال شد');
            startResendTimer();
        } else {
            showOtpError(result.message || 'خطا در ارسال مجدد کد');
        }
        
    } catch (error) {
        showOtpError('خطا در ارسال مجدد کد');
    }
}

/**
 * Handle failed state
 */
function handleFailedState(data) {
    console.log('🚨 [FAILED-STATE] Request failed, hiding OTP section if visible');
    
    // Hide OTP section if it's currently visible
    if (isOtpVisible) {
        hideOtpSectionOnError();
    }
    
    // Show error actions after a delay
    setTimeout(() => {
        addErrorActionButtons();
    }, 3000);
}

/**
 * Handle completed state
 */
function handleCompletedState(data) {
    console.log('✅ [COMPLETED-STATE] Request completed successfully, staying on progress page');
    
    // Hide OTP section if it's still visible
    if (isOtpVisible) {
        hideOtpSection();
    }
    
    // Show success actions after a delay
    setTimeout(() => {
        addSuccessActionButtons(data);
    }, 2000);
}

/**
 * Add success action buttons after completion
 */
function addSuccessActionButtons(data) {
    const statusContainer = document.getElementById('status-container');
    
    if (statusContainer && !statusContainer.querySelector('.success-actions')) {
        const actionsDiv = document.createElement('div');
        actionsDiv.className = 'success-actions mt-6 text-center space-y-3';
        
        // Determine result URL
        let resultUrl = `/services/${serviceSlug}/result/${requestHash}`;
        if (data.result_data && data.result_data.result_url) {
            resultUrl = data.result_data.result_url;
        }
        
        actionsDiv.innerHTML = `
            <p class="text-md font-semibold text-gray-500">
                درخواست شما در صف پردازش درگاه دولتی قرار گرفته است، تا ۱۵ دقیقه دیگر نتیجه برای شما پیامک خواهد شد
            </p>
            <div class="pt-2 text-xs text-gray-500">
                نتیجه گزارش را بعد از ۱۵ دقیقه دریافت نکردید؟
            </div>
            <div class="pt-2 space-x-2 space-x-reverse">
                <button onclick="requestNewSms()" 
                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                     ارسال مجدد پیامک نتیجه
                </button>
                <button onclick="window.location.href='/services/${serviceSlug}'" 
                        class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                     درخواست جدید
                </button>
            </div>
        `;
        statusContainer.appendChild(actionsDiv);
    }
}

/**
 * Request new SMS (for cases where user wants to restart the process)
 */
function requestNewSms() {
    if (confirm('آیا می‌خواهید درخواست جدیدی برای دریافت کد تایید ارسال کنید؟')) {
        // Show loading
        const actionsDiv = document.querySelector('.success-actions');
        if (actionsDiv) {
            actionsDiv.innerHTML = `
                <div class="text-center">
                    <svg class="animate-spin w-8 h-8 text-sky-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm text-sky-600 font-medium">در حال ارسال درخواست جدید...</p>
                </div>
            `;
        }
        
        // Redirect to service page for new request
        setTimeout(() => {
            window.location.href = `/services/${serviceSlug}`;
        }, 1500);
    }
}

/**
 * Retry the entire request from beginning (with 3-minute cooldown)
 */
function retryRequest() {
    const currentTime = new Date();
    const elapsedTime = Math.floor((currentTime - startTime) / 1000); // in seconds
    const requiredWaitTime = 180; // 3 minutes in seconds
    const remainingTime = requiredWaitTime - elapsedTime;
    
    console.log('🔄 [RETRY-REQUEST] Time check:', {
        elapsedTime,
        requiredWaitTime,
        remainingTime,
        canRetry: remainingTime <= 0
    });
    
    if (remainingTime > 0) {
        // Show wait message with countdown
        showRetryWaitMessage(remainingTime);
        return;
    }
    
    // Confirm retry
    if (confirm('آیا می‌خواهید درخواست را از ابتدا تکرار کنید؟\n\nاین عمل درخواست فعلی را لغو کرده و درخواست جدیدی ایجاد می‌کند.')) {
        // Show loading state
        showRetryLoading();
        
        // Create new request automatically
        createNewRequestAndRedirect();
    }
}

/**
 * Create a new request with the same data and redirect to its progress page
 */
async function createNewRequestAndRedirect() {
    try {
        console.log('🔄 [RETRY-REQUEST] Restarting request with original data...');
        
        // Use the existing restart endpoint that duplicates the current request
        const restartResponse = await fetch(`/api/local-requests/${requestHash}/restart`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        if (!restartResponse.ok) {
            throw new Error(`Restart API failed: ${restartResponse.status} ${restartResponse.statusText}`);
        }
        
        const restartResult = await restartResponse.json();
        console.log('✅ [RETRY-REQUEST] Restart response:', restartResult);
        
        if (!restartResult.success) {
            throw new Error(restartResult.message || 'Restart failed');
        }
        
        // Check if we got a new hash
        const newHash = restartResult.new_hash;
        if (newHash) {
            console.log('🔄 [RETRY-REQUEST] Redirecting to new progress page:', newHash);
            window.location.href = `/services/${serviceSlug}/progress/${newHash}`;
        } else {
            throw new Error('No new hash received from restart API');
        }
        
    } catch (error) {
        console.error('❌ [RETRY-REQUEST] Error with restart API:', error);
        showRetryErrorAndRedirect();
    }
}

/**
 * Show error and redirect to service page
 */
function showRetryErrorAndRedirect() {
    console.log('🔄 [RETRY-REQUEST] Restart failed, redirecting to service page');
    
    // Show error message to user
    const retryButtons = document.querySelectorAll('button[onclick="retryRequest()"]');
    retryButtons.forEach(button => {
        button.innerHTML = 'خطا - انتقال به صفحه سرویس...';
        button.disabled = true;
        button.className = button.className.replace('bg-sky-600', 'bg-red-500');
    });
    
    // Redirect after delay
    setTimeout(() => {
        window.location.href = `/services/${serviceSlug}`;
    }, 2000);
}

/**
 * Show retry wait message with countdown
 */
function showRetryWaitMessage(remainingSeconds) {
    // Find the retry button and replace it with countdown
    const retryButtons = document.querySelectorAll('button[onclick="retryRequest()"]');
    
    retryButtons.forEach(button => {
        const originalHTML = button.innerHTML;
        const originalClasses = button.className;
        
        // Disable button and show countdown
        button.disabled = true;
        button.className = originalClasses.replace('bg-sky-600 hover:bg-sky-700', 'bg-gray-400 cursor-not-allowed');
        
        // Start countdown
        let timeLeft = remainingSeconds;
        const countdownInterval = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            const timeString = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            button.innerHTML = `
                <div class="flex items-center justify-center space-x-1 space-x-reverse">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>صبر کنید ${timeString}</span>
                </div>
            `;
            
            timeLeft--;
            
            if (timeLeft < 0) {
                clearInterval(countdownInterval);
                
                // Restore button
                button.disabled = false;
                button.className = originalClasses;
                button.innerHTML = originalHTML;
                
                // Add a small pulse animation to indicate it's available
                button.classList.add('animate-pulse');
                setTimeout(() => {
                    button.classList.remove('animate-pulse');
                }, 2000);
            }
        }, 1000);
    });
}

/**
 * Show retry loading state
 */
function showRetryLoading() {
    // Find retry buttons and show loading
    const retryButtons = document.querySelectorAll('button[onclick="retryRequest()"]');
    
    retryButtons.forEach(button => {
        button.disabled = true;
        button.innerHTML = `
            <div class="flex items-center justify-center space-x-1 space-x-reverse">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>در حال تکرار درخواست...</span>
            </div>
        `;
    });
    
    // Also show loading in action containers
    const actionsContainers = document.querySelectorAll('.error-actions, .success-actions');
    actionsContainers.forEach(container => {
        if (container.querySelector('button[onclick="retryRequest()"]')) {
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'retry-loading text-center mt-4 p-4 bg-sky-50 rounded-lg';
            loadingDiv.innerHTML = `
                <svg class="animate-spin w-6 h-6 text-sky-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm text-sky-600 font-medium">در حال آماده‌سازی درخواست جدید...</p>
                <p class="text-xs text-gray-500 mt-1">به صفحه سرویس منتقل می‌شوید</p>
            `;
            container.appendChild(loadingDiv);
        }
    });
}

/**
 * Add error action buttons to the error message display area
 */
function addErrorActionButtons() {
    const errorMessageDisplay = document.getElementById('error-message-display');
    
    if (errorMessageDisplay && !errorMessageDisplay.querySelector('.error-actions')) {
        const errorText = document.getElementById('error-message-text')?.textContent || '';
        const isOtpTimeoutError = errorText.includes('زمان انتظار کد تایید به پایان رسید');
        
        const actionsDiv = document.createElement('div');
        actionsDiv.className = 'error-actions mt-6 text-center space-y-3';
        
        if (isOtpTimeoutError) {
            // Special handling for OTP timeout errors
            actionsDiv.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <p class="text-yellow-800 text-sm mb-2">
                        <strong>علت خطا:</strong> کد تایید در زمان مناسب وارد نشد یا دریافت نشد.
                    </p>
                    <p class="text-yellow-700 text-xs">
                        برای حل این مشکل می‌توانید درخواست جدیدی ارسال کنید یا کمی صبر کرده و مجدداً تلاش کنید.
                    </p>
                </div>
                <div class="space-x-2 space-x-reverse">
                    <button onclick="retryRequest()" 
                            class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                         🔄 تکرار درخواست
                    </button>
                    <button onclick="window.location.href='/services/${serviceSlug}'" 
                            class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                         ✨ درخواست جدید
                    </button>
                </div>
                <div class="pt-2">
                    <button onclick="window.location.href='/'" 
                            class="bg-sky-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-xs font-medium transition-colors">
                         صفحه اصلی
                    </button>
                </div>
            `;
        } else {
            // General error handling
            actionsDiv.innerHTML = `
                <div class="space-x-2 space-x-reverse">
                    <button onclick="retryRequest()" 
                            class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                         تکرار درخواست
                    </button>
                    <button onclick="window.location.href='/services/${serviceSlug}'" 
                            class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                         درخواست جدید
                    </button>
                    <button onclick="window.location.href='/'" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                         صفحه اصلی
                    </button>
                </div>
            `;
        }
        
        errorMessageDisplay.appendChild(actionsDiv);
    }
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (updateInterval) {
        clearInterval(updateInterval);
    }
    if (timerInterval) {
        clearInterval(timerInterval);
    }
    if (resendInterval) {
        clearInterval(resendInterval);
    }
    if (pollingTimeoutId) {
        clearTimeout(pollingTimeoutId);
    }
});
</script>
@endpush
@endsection 