{{-- Conversational AI Search Component --}}

        <div class="max-w-3xl mx-auto">
            <!-- Chat Messages -->
            <div class="ai-chat-messages space-y-4 mb-6 max-h-96 overflow-y-auto">
                <div class="flex items-start space-y-3">
                    <div class="w-8 h-8 bg-sky-500 z-50 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="flex-1 bg-sky-100 -mr-4 ml-3 border border-sky-200 rounded-2xl rounded-tr-sm p-4">
                        <p class="text-sky-900">سلام! چطور می‌تونم کمکتون کنم؟ می‌تونید از من هر سوالی در مورد خدمات پیشخوانک بپرسید.</p>
                    </div>
                </div>
            </div>
            
            <!-- Input Area -->
            <div class="relative space-y-3">
                <!-- File Upload Area -->
                <div id="ai-file-upload-area" class="hidden border-2 border-dashed border-sky-300 rounded-2xl p-6 text-center bg-sky-50">
                    <input type="file" id="ai-file-input" multiple accept="image/*,.pdf,.txt,.doc,.docx" class="hidden">
                    <div class="ai-file-drop-zone cursor-pointer">
                        <svg class="w-10 h-10 mx-auto text-sky-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sky-700 font-medium">فایل‌های خود را اینجا بکشید یا کلیک کنید</p>
                        <p class="text-xs text-sky-500 mt-1">تصاویر، PDF، متن - حداکثر 20MB هر فایل</p>
                    </div>
                </div>
                
                <!-- Selected Files Display -->
                <div id="ai-selected-files" class="hidden space-y-2"></div>
                
                <!-- Chat Input -->
                <div class="flex items-center bg-white rounded-2xl p-4 border border-sky-100">
                    <input type="text" 
                           class="flex-1 bg-transparent border-none focus:outline-none text-sky-900 placeholder-dark-sky-500"
                           placeholder="پیام خود را اینجا بنویسید..."
                           id="ai-chat-input"
                           autocomplete="off">
                    <div class="flex items-center gap-2">
                        <button id="ai-attach-btn" class="ai-attach-btn w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center hover:bg-yellow-500 transition-colors" title="ضمیمه فایل">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        </button>
                        <button id="ai-voice-btn" class="w-8 h-8 bg-green-400 rounded-full flex items-center justify-center hover:bg-green-500 transition-colors" title="ضبط صدا">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                            </svg>
                        </button>
                        <button class="ai-send-btn w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center hover:bg-sky-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            

            
            <!-- Search Results Section -->
            <div class="ai-search-results mt-6 hidden">
                <div class="bg-gradient-to-r from-sky-50 to-sky-50 rounded-2xl p-4 border border-sky-200">
                    <h4 class="font-semibold text-sky-900 mb-3">خدمات پیشنهادی:</h4>
                    <div class="space-y-2" id="suggested-services"></div>
                </div>
            </div>
        </div>

{{-- Loading State --}}
<div class="ai-loading hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center">
                <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
            </div>
            <div>
                <h3 class="font-semibold text-sky-900">در حال پردازش...</h3>
                <p class="text-sm text-sky-600">AI در حال تحلیل درخواست شما</p>
            </div>
        </div>
    </div>
</div>

{{-- Error Notification --}}
<div class="ai-error-notification hidden fixed top-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50 max-w-sm">
    <p class="text-sm"></p>
</div>

@push('head')
<style>
/* Custom scrollbar for chat messages */
.ai-chat-messages::-webkit-scrollbar {
    width: 6px;
}

.ai-chat-messages::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.ai-chat-messages::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.ai-chat-messages::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush

{{-- JavaScript functionality is now handled by enhanced-ai-chat.js --}} 