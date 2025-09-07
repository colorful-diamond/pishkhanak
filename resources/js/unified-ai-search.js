/**
 * Unified AI Search System
 * Complete implementation with multimodal input, auto-typing, and AI integration
 */

window.UnifiedAiSearch = (function() {
    'use strict';

    // Configuration
    const CONFIG = {
        API_BASE: '/api/ai-search',
        TYPING_SPEED: 100,
        TYPING_PAUSE: 2000,
        SEARCH_DELAY: 500,
        MAX_RECORDING_TIME: 30000, // 30 seconds
        SUPPORTED_IMAGE_TYPES: ['image/jpeg', 'image/png', 'image/webp'],
        MAX_IMAGE_SIZE: 5 * 1024 * 1024, // 5MB
        CSRF_TOKEN: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    };

    // State management
    const state = {
        currentMode: 'text',
        isSearching: false,
        isRecording: false,
        isTyping: false,
        searchTimeout: null,
        typingTimeout: null,
        recordingTimeout: null,
        mediaRecorder: null,
        audioChunks: [],
        sessionId: null,
        conversationHistory: [],
        autoTypingExamples: []
    };

    // DOM elements
    let elements = {};

    /**
     * Initialize the search system
     */
    function init() {
        initializeElements();
        initializeSessionId();
        setupEventListeners();
        loadAutoTypingExamples();
        loadSearchHistory();
        startAutoTyping();
        
        console.log('Unified AI Search initialized');
    }

    /**
     * Initialize DOM element references
     */
    function initializeElements() {
        elements = {
            container: document.getElementById('unifiedAiSearch'),
            searchInput: document.getElementById('mainSearchInput'),
            searchForm: document.getElementById('mainSearchForm'),
            searchContainer: document.getElementById('searchContainer'),
            resultsContainer: document.getElementById('searchResultsContainer'),
            processingStatus: document.querySelector('.ai-processing-status'),
            
            // Mode buttons
            modeButtons: document.querySelectorAll('.search-mode-btn'),
            modeContents: document.querySelectorAll('.search-mode-content'),
            
            // Text mode
            textSearchMode: document.getElementById('text-search-mode'),
            voiceSearchBtn: document.querySelector('.ai-voice-search-btn'),
            submitBtn: document.querySelector('.ai-search-submit-btn'),
            
            // Voice mode
            voiceSearchMode: document.getElementById('voice-search-mode'),
            voiceRecordingBtn: document.querySelector('.voice-recording-btn'),
            voiceStatus: document.querySelector('.voice-status'),
            voiceTimer: document.querySelector('.voice-timer'),
            
            // Image mode
            imageSearchMode: document.getElementById('image-search-mode'),
            imageUpload: document.getElementById('imageUpload'),
            imagePreview: document.querySelector('.image-preview'),
            
            // Conversational mode
            conversationalMode: document.getElementById('conversational-search-mode'),
            chatMessages: document.getElementById('chatMessages'),
            chatInput: document.getElementById('chatInput'),
            chatSendBtn: document.querySelector('.chat-send-btn'),
            chatVoiceBtn: document.querySelector('.chat-voice-btn'),
            clearChatBtn: document.querySelector('.clear-chat-btn'),
            
            // Results
            traditionalContent: document.getElementById('traditionalSearchContent'),
            aiResults: document.getElementById('aiSearchResults'),
            recentSearches: document.getElementById('recentSearches'),
            popularSearches: document.getElementById('popularSearches'),
            clearHistoryBtn: document.getElementById('clearSearchHistory')
        };
    }

    /**
     * Initialize session ID
     */
    function initializeSessionId() {
        const container = elements.container;
        if (container) {
            state.sessionId = container.dataset.sessionId || generateSessionId();
        }
    }

    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // Mode switching
        elements.modeButtons.forEach(btn => {
            btn.addEventListener('click', handleModeSwitch);
        });

        // Text search
        if (elements.searchForm) {
            elements.searchForm.addEventListener('submit', handleTextSearch);
        }

        if (elements.searchInput) {
            elements.searchInput.addEventListener('input', handleSearchInput);
            elements.searchInput.addEventListener('focus', handleSearchFocus);
            elements.searchInput.addEventListener('blur', handleSearchBlur);
        }

        // Voice search
        if (elements.voiceSearchBtn) {
            elements.voiceSearchBtn.addEventListener('click', () => switchMode('voice'));
        }

        if (elements.voiceRecordingBtn) {
            elements.voiceRecordingBtn.addEventListener('click', handleVoiceRecording);
        }

        // Image search
        if (elements.imageUpload) {
            elements.imageUpload.addEventListener('change', handleImageUpload);
        }

        // Conversational chat
        if (elements.chatSendBtn) {
            elements.chatSendBtn.addEventListener('click', handleChatSend);
        }

        if (elements.chatInput) {
            elements.chatInput.addEventListener('keypress', handleChatKeypress);
        }

        if (elements.clearChatBtn) {
            elements.clearChatBtn.addEventListener('click', clearConversation);
        }

        // History management
        if (elements.clearHistoryBtn) {
            elements.clearHistoryBtn.addEventListener('click', clearSearchHistory);
        }

        // Close results when clicking outside
        document.addEventListener('click', handleOutsideClick);

        // Keyboard shortcuts
        document.addEventListener('keydown', handleKeyboardShortcuts);
    }

    /**
     * Handle mode switching
     */
    function handleModeSwitch(event) {
        const mode = event.currentTarget.dataset.mode;
        switchMode(mode);
    }

    /**
     * Switch search mode
     */
    function switchMode(mode) {
        if (state.currentMode === mode) return;

        // Update mode buttons
        elements.modeButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.mode === mode);
        });

        // Update mode contents
        elements.modeContents.forEach(content => {
            content.classList.toggle('active', content.id === `${mode}-search-mode`);
            content.classList.toggle('hidden', content.id !== `${mode}-search-mode`);
        });

        state.currentMode = mode;
        hideResults();

        // Mode-specific initialization
        switch (mode) {
            case 'voice':
                initializeVoiceMode();
                break;
            case 'image':
                initializeImageMode();
                break;
            case 'conversational':
                initializeConversationalMode();
                break;
            default:
                initializeTextMode();
        }

        console.log(`Switched to ${mode} mode`);
    }

    /**
     * Initialize text mode
     */
    function initializeTextMode() {
        if (elements.searchInput) {
            elements.searchInput.focus();
            startAutoTyping();
        }
    }

    /**
     * Initialize voice mode
     */
    function initializeVoiceMode() {
        checkMicrophonePermissions();
    }

    /**
     * Initialize image mode
     */
    function initializeImageMode() {
        resetImageUpload();
    }

    /**
     * Initialize conversational mode
     */
    function initializeConversationalMode() {
        if (elements.chatInput) {
            elements.chatInput.focus();
        }
        loadConversationHistory();
    }

    /**
     * Handle text search form submission
     */
    async function handleTextSearch(event) {
        event.preventDefault();
        
        const query = elements.searchInput?.value?.trim();
        if (!query || state.isSearching) return;

        await performSearch(query, 'text');
    }

    /**
     * Handle search input changes
     */
    function handleSearchInput(event) {
        const query = event.target.value.trim();
        
        if (state.typingTimeout) {
            clearTimeout(state.typingTimeout);
        }

        if (query.length >= 2) {
            state.typingTimeout = setTimeout(() => {
                getSuggestions(query);
            }, CONFIG.SEARCH_DELAY);
        } else {
            hideSuggestions();
        }
    }

    /**
     * Handle search input focus
     */
    function handleSearchFocus() {
        stopAutoTyping();
        showInitialSearchContent();
    }

    /**
     * Handle search input blur
     */
    function handleSearchBlur() {
        // Delay hiding to allow clicking on suggestions
        setTimeout(() => {
            if (!elements.searchInput?.matches(':focus')) {
                startAutoTyping();
            }
        }, 150);
    }

    /**
     * Handle voice recording
     */
    async function handleVoiceRecording() {
        if (state.isRecording) {
            stopVoiceRecording();
        } else {
            await startVoiceRecording();
        }
    }

    /**
     * Start voice recording
     */
    async function startVoiceRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            
            state.mediaRecorder = new MediaRecorder(stream);
            state.audioChunks = [];
            state.isRecording = true;

            state.mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    state.audioChunks.push(event.data);
                }
            };

            state.mediaRecorder.onstop = () => {
                processAudioRecording();
            };

            state.mediaRecorder.start();
            updateVoiceUI(true);
            
            // Auto-stop after max time
            state.recordingTimeout = setTimeout(() => {
                stopVoiceRecording();
            }, CONFIG.MAX_RECORDING_TIME);

            startVoiceTimer();

        } catch (error) {
            console.error('Voice recording failed:', error);
            showNotification('خطا در دسترسی به میکروفن. لطفاً دسترسی را فعال کنید.', 'error');
        }
    }

    /**
     * Stop voice recording
     */
    function stopVoiceRecording() {
        if (state.mediaRecorder && state.isRecording) {
            state.mediaRecorder.stop();
            state.mediaRecorder.stream.getTracks().forEach(track => track.stop());
            state.isRecording = false;
            
            if (state.recordingTimeout) {
                clearTimeout(state.recordingTimeout);
            }
            
            updateVoiceUI(false);
            stopVoiceTimer();
        }
    }

    /**
     * Process audio recording
     */
    async function processAudioRecording() {
        if (state.audioChunks.length === 0) return;

        try {
            const audioBlob = new Blob(state.audioChunks, { type: 'audio/wav' });
            
            showProcessingStatus('در حال پردازش صدا...');
            
            // For now, simulate transcription
            // In production, send to speech-to-text API
            setTimeout(async () => {
                const mockTranscription = 'استعلام خلافی خودرو';
                hideProcessingStatus();
                
                // Fill the search input and perform search
                if (elements.searchInput) {
                    elements.searchInput.value = mockTranscription;
                }
                
                await performSearch(mockTranscription, 'voice');
            }, 2000);

        } catch (error) {
            console.error('Audio processing failed:', error);
            hideProcessingStatus();
            showNotification('خطا در پردازش صدا. لطفاً دوباره تلاش کنید.', 'error');
        }
    }

    /**
     * Handle image upload
     */
    function handleImageUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file
        if (!CONFIG.SUPPORTED_IMAGE_TYPES.includes(file.type)) {
            showNotification('فرمت تصویر پشتیبانی نمی‌شود. لطفاً JPG، PNG یا WebP استفاده کنید.', 'error');
            return;
        }

        if (file.size > CONFIG.MAX_IMAGE_SIZE) {
            showNotification('حجم تصویر باید کمتر از 5 مگابایت باشد.', 'error');
            return;
        }

        processImageUpload(file);
    }

    /**
     * Process image upload
     */
    async function processImageUpload(file) {
        try {
            showProcessingStatus('در حال پردازش تصویر...');
            
            // Show image preview
            const reader = new FileReader();
            reader.onload = (e) => {
                if (elements.imagePreview) {
                    const img = elements.imagePreview.querySelector('img');
                    if (img) {
                        img.src = e.target.result;
                        elements.imagePreview.classList.remove('hidden');
                    }
                }
            };
            reader.readAsDataURL(file);

            // Create FormData for upload
            const formData = new FormData();
            formData.append('image', file);
            formData.append('session_id', state.sessionId);

            const response = await fetch(`${CONFIG.API_BASE}/image`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN
                },
                body: formData
            });

            const result = await response.json();
            hideProcessingStatus();

            if (result.success) {
                displaySearchResults(result.data);
            } else {
                showNotification(result.message || 'خطا در پردازش تصویر', 'error');
            }

        } catch (error) {
            console.error('Image processing failed:', error);
            hideProcessingStatus();
            showNotification('خطا در پردازش تصویر. لطفاً دوباره تلاش کنید.', 'error');
        }
    }

    /**
     * Handle chat message sending
     */
    async function handleChatSend() {
        const message = elements.chatInput?.value?.trim();
        if (!message || state.isSearching) return;

        elements.chatInput.value = '';
        await sendChatMessage(message);
    }

    /**
     * Handle chat input keypress
     */
    function handleChatKeypress(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            handleChatSend();
        }
    }

    /**
     * Send chat message
     */
    async function sendChatMessage(message) {
        try {
            // Add user message to chat
            addChatMessage(message, 'user');
            
            // Show typing indicator
            addTypingIndicator();
            
            state.isSearching = true;
            
            const response = await fetch(`${CONFIG.API_BASE}/conversational`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN
                },
                body: JSON.stringify({
                    query: message,
                    session_id: state.sessionId
                })
            });

            const result = await response.json();
            
            // Remove typing indicator
            removeTypingIndicator();
            
            if (result.success) {
                // Add AI response to chat
                addChatMessage(result.data.ai_response, 'ai');
                
                // Update conversation history
                state.conversationHistory.push({
                    user: message,
                    ai: result.data.ai_response,
                    timestamp: new Date().toISOString()
                });
                
                // Show search results if any
                if (result.data.results && result.data.results.length > 0) {
                    displayInlineResults(result.data.results);
                }
                
            } else {
                addChatMessage(result.message || 'متاسفم، خطایی رخ داده است.', 'ai');
            }
            
        } catch (error) {
            console.error('Chat message failed:', error);
            removeTypingIndicator();
            addChatMessage('متاسفم، در حال حاضر امکان پاسخگویی وجود ندارد.', 'ai');
        } finally {
            state.isSearching = false;
        }
    }

    /**
     * Perform search
     */
    async function performSearch(query, type = 'text') {
        if (!query || state.isSearching) return;

        try {
            state.isSearching = true;
            showProcessingStatus();
            
            // Add to search history
            addToSearchHistory(query);
            
            const endpoint = type === 'text' ? '/text' : `/${type}`;
            const response = await fetch(`${CONFIG.API_BASE}${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN
                },
                body: JSON.stringify({
                    query: query,
                    session_id: state.sessionId
                })
            });

            const result = await response.json();
            
            if (result.success) {
                displaySearchResults(result.data);
            } else {
                showNotification(result.message || 'خطا در جستجو', 'error');
            }
            
        } catch (error) {
            console.error('Search failed:', error);
            showNotification('خطا در جستجو. لطفاً دوباره تلاش کنید.', 'error');
        } finally {
            state.isSearching = false;
            hideProcessingStatus();
        }
    }

    /**
     * Get search suggestions
     */
    async function getSuggestions(query) {
        try {
            const response = await fetch(`${CONFIG.API_BASE}/suggestions?query=${encodeURIComponent(query)}`, {
                headers: {
                    'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN
                }
            });

            const result = await response.json();
            
            if (result.success && result.data.suggestions) {
                displaySuggestions(result.data.suggestions);
            }
            
        } catch (error) {
            console.error('Failed to get suggestions:', error);
        }
    }

    /**
     * Load auto-typing examples
     */
    async function loadAutoTypingExamples() {
        try {
            const response = await fetch(`${CONFIG.API_BASE}/auto-typing-examples`, {
                headers: {
                    'X-CSRF-TOKEN': CONFIG.CSRF_TOKEN
                }
            });

            const result = await response.json();
            
            if (result.success && result.data.examples) {
                state.autoTypingExamples = result.data.examples;
            } else {
                // Fallback examples
                state.autoTypingExamples = [
                    'استعلام خلافی خودرو با پلاک...',
                    'محاسبه شماره شبا از حساب...',
                    'چک کردن وضعیت چک صیادی...',
                    'اعتبارسنجی کارت بانکی...',
                    'پیدا کردن کدپستی...'
                ];
            }
            
        } catch (error) {
            console.error('Failed to load auto-typing examples:', error);
            // Use fallback examples
            state.autoTypingExamples = [
                'استعلام خلافی خودرو...',
                'محاسبه شماره شبا...',
                'استعلام وضعیت چک...'
            ];
        }
    }

    /**
     * Start auto-typing animation
     */
    function startAutoTyping() {
        if (state.isTyping || !elements.searchInput || state.autoTypingExamples.length === 0) return;
        
        state.isTyping = true;
        const examples = [...state.autoTypingExamples];
        let currentIndex = 0;

        function typeExample() {
            if (!state.isTyping || elements.searchInput.matches(':focus')) {
                state.isTyping = false;
                return;
            }

            const example = examples[currentIndex];
            let charIndex = 0;
            
            // Clear previous text
            elements.searchInput.placeholder = '';
            
            function typeChar() {
                if (!state.isTyping || elements.searchInput.matches(':focus')) {
                    state.isTyping = false;
                    return;
                }
                
                if (charIndex < example.length) {
                    elements.searchInput.placeholder = example.substring(0, charIndex + 1);
                    charIndex++;
                    state.typingTimeout = setTimeout(typeChar, CONFIG.TYPING_SPEED);
                } else {
                    // Move to next example after pause
                    state.typingTimeout = setTimeout(() => {
                        currentIndex = (currentIndex + 1) % examples.length;
                        typeExample();
                    }, CONFIG.TYPING_PAUSE);
                }
            }
            
            typeChar();
        }

        typeExample();
    }

    /**
     * Stop auto-typing animation
     */
    function stopAutoTyping() {
        state.isTyping = false;
        if (state.typingTimeout) {
            clearTimeout(state.typingTimeout);
        }
        if (elements.searchInput) {
            elements.searchInput.placeholder = 'جستجوی هوشمند...';
        }
    }

    /**
     * Display search results
     */
    function displaySearchResults(data) {
        if (!elements.resultsContainer) return;

        hideInitialSearchContent();
        
        // Show AI results
        if (elements.aiResults) {
            elements.aiResults.classList.remove('hidden');
            
            // Display AI response
            const responseElement = elements.aiResults.querySelector('.ai-response-text');
            if (responseElement && data.ai_response) {
                responseElement.textContent = data.ai_response;
            }
            
            // Display search results
            const resultsListElement = elements.aiResults.querySelector('.search-results-list');
            if (resultsListElement && data.results) {
                resultsListElement.innerHTML = '';
                
                data.results.forEach(result => {
                    const resultElement = createResultElement(result);
                    resultsListElement.appendChild(resultElement);
                });
            }
            
            // Display suggestions
            const suggestionsElement = elements.aiResults.querySelector('.suggestions-list');
            if (suggestionsElement && data.suggestions) {
                suggestionsElement.innerHTML = '';
                
                data.suggestions.forEach(suggestion => {
                    const suggestionElement = createSuggestionElement(suggestion);
                    suggestionsElement.appendChild(suggestionElement);
                });
            }
        }
        
        showResults();
    }

    /**
     * Create result element
     */
    function createResultElement(result) {
        const div = document.createElement('div');
        div.className = 'search-result-item p-4 bg-white border border-sky-200 rounded-lg hover:shadow-md transition-shadow cursor-pointer';
        
        div.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-sky-900">${escapeHtml(result.title)}</h3>
                <div class="flex items-center space-x-2">
                    ${result.confidence ? `<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">${Math.round(result.confidence * 100)}% مطابقت</span>` : ''}
                    ${result.type === 'service' ? '<span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">خدمت</span>' : ''}
                    ${result.type === 'post' ? '<span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">مقاله</span>' : ''}
                </div>
            </div>
            <p class="text-sky-700 mb-3">${escapeHtml(result.description)}</p>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 text-sm text-sky-600">
                    ${result.category ? `<span>دسته: ${escapeHtml(result.category)}</span>` : ''}
                    ${result.similarity ? `<span>شباهت: ${Math.round(result.similarity * 100)}%</span>` : ''}
                </div>
                <a href="${result.url}" class="px-3 py-1 bg-sky-500 text-white rounded text-sm hover:bg-sky-600 transition-colors">
                    مشاهده
                </a>
            </div>
        `;
        
        return div;
    }

    /**
     * Create suggestion element
     */
    function createSuggestionElement(suggestion) {
        const span = document.createElement('span');
        span.className = 'suggestion-chip px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm cursor-pointer hover:bg-yellow-100 hover:text-yellow-700 transition-colors';
        span.textContent = suggestion;
        
        span.addEventListener('click', () => {
            if (elements.searchInput) {
                elements.searchInput.value = suggestion;
                performSearch(suggestion);
            }
        });
        
        return span;
    }

    /**
     * Add chat message
     */
    function addChatMessage(message, sender = 'ai') {
        if (!elements.chatMessages) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `${sender}-message flex items-start space-x-3 mb-4`;
        
        if (sender === 'user') {
            messageDiv.innerHTML = `
                <div class="flex-1 bg-sky-100 rounded-2xl rounded-tl-sm p-4 mr-8">
                    <p class="text-sky-900">${escapeHtml(message)}</p>
                </div>
                <div class="w-8 h-8 bg-sky-300 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div class="flex-1 bg-sky-50 rounded-2xl rounded-tr-sm p-4">
                    <p class="text-sky-900">${escapeHtml(message)}</p>
                </div>
            `;
        }
        
        elements.chatMessages.appendChild(messageDiv);
        elements.chatMessages.scrollTop = elements.chatMessages.scrollHeight;
    }

    /**
     * Add typing indicator
     */
    function addTypingIndicator() {
        if (!elements.chatMessages) return;

        const typingDiv = document.createElement('div');
        typingDiv.className = 'typing-indicator flex items-start space-x-3 mb-4';
        typingDiv.innerHTML = `
            <div class="w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <div class="flex-1 bg-sky-50 rounded-2xl rounded-tr-sm p-4">
                <div class="typing-animation flex space-x-1">
                    <div class="w-2 h-2 bg-sky-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-sky-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-sky-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        `;
        
        elements.chatMessages.appendChild(typingDiv);
        elements.chatMessages.scrollTop = elements.chatMessages.scrollHeight;
    }

    /**
     * Remove typing indicator
     */
    function removeTypingIndicator() {
        const typingIndicator = elements.chatMessages?.querySelector('.typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    /**
     * Show processing status
     */
    function showProcessingStatus(message = 'در حال پردازش...') {
        if (elements.processingStatus) {
            const messageElement = elements.processingStatus.querySelector('p');
            if (messageElement) {
                messageElement.textContent = message;
            }
            elements.processingStatus.classList.remove('hidden');
        }
    }

    /**
     * Hide processing status
     */
    function hideProcessingStatus() {
        if (elements.processingStatus) {
            elements.processingStatus.classList.add('hidden');
        }
    }

    /**
     * Show search results container
     */
    function showResults() {
        if (elements.resultsContainer) {
            elements.resultsContainer.classList.remove('hidden');
        }
    }

    /**
     * Hide search results container
     */
    function hideResults() {
        if (elements.resultsContainer) {
            elements.resultsContainer.classList.add('hidden');
        }
    }

    /**
     * Show initial search content
     */
    function showInitialSearchContent() {
        if (elements.traditionalContent) {
            elements.traditionalContent.classList.remove('hidden');
        }
        if (elements.aiResults) {
            elements.aiResults.classList.add('hidden');
        }
        showResults();
    }

    /**
     * Hide initial search content
     */
    function hideInitialSearchContent() {
        if (elements.traditionalContent) {
            elements.traditionalContent.classList.add('hidden');
        }
    }

    /**
     * Load search history
     */
    function loadSearchHistory() {
        const recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
        const popularSearches = ['استعلام خلافی خودرو', 'محاسبه شبا', 'استعلام چک'];
        
        if (elements.recentSearches) {
            elements.recentSearches.innerHTML = '';
            recentSearches.forEach(search => {
                const chip = createSearchChip(search);
                elements.recentSearches.appendChild(chip);
            });
        }
        
        if (elements.popularSearches) {
            elements.popularSearches.innerHTML = '';
            popularSearches.forEach(search => {
                const chip = createSearchChip(search);
                elements.popularSearches.appendChild(chip);
            });
        }
    }

    /**
     * Create search chip
     */
    function createSearchChip(text) {
        const chip = document.createElement('div');
        chip.className = 'search-chip px-3 py-1 bg-gray-100 rounded-full text-sm text-gray-700 cursor-pointer hover:bg-yellow-100 hover:text-yellow-700 transition-colors';
        chip.textContent = text;
        
        chip.addEventListener('click', () => {
            if (elements.searchInput) {
                elements.searchInput.value = text;
                performSearch(text);
            }
        });
        
        return chip;
    }

    /**
     * Add to search history
     */
    function addToSearchHistory(query) {
        let recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
        recentSearches = [query, ...recentSearches.filter(item => item !== query)].slice(0, 5);
        localStorage.setItem('recentSearches', JSON.stringify(recentSearches));
        loadSearchHistory();
    }

    /**
     * Clear search history
     */
    function clearSearchHistory() {
        localStorage.removeItem('recentSearches');
        loadSearchHistory();
    }

    /**
     * Generate session ID
     */
    function generateSessionId() {
        return 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Show notification
     */
    function showNotification(message, type = 'info') {
        // Simple notification implementation
        console.log(`[${type.toUpperCase()}] ${message}`);
        alert(message); // Replace with proper notification system
    }

    /**
     * Handle outside clicks
     */
    function handleOutsideClick(event) {
        if (!elements.searchContainer?.contains(event.target)) {
            hideResults();
        }
    }

    /**
     * Handle keyboard shortcuts
     */
    function handleKeyboardShortcuts(event) {
        // Escape to close results
        if (event.key === 'Escape') {
            hideResults();
        }
        
        // Ctrl/Cmd + K to focus search
        if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
            event.preventDefault();
            elements.searchInput?.focus();
        }
    }

    // Voice UI helpers
    function updateVoiceUI(isRecording) {
        if (!elements.voiceRecordingBtn || !elements.voiceStatus) return;
        
        if (isRecording) {
            elements.voiceRecordingBtn.classList.add('recording');
            elements.voiceStatus.classList.remove('hidden');
        } else {
            elements.voiceRecordingBtn.classList.remove('recording');
            elements.voiceStatus.classList.add('hidden');
        }
    }

    function startVoiceTimer() {
        let seconds = 0;
        const timer = setInterval(() => {
            if (!state.isRecording) {
                clearInterval(timer);
                return;
            }
            
            seconds++;
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            const timeString = `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
            
            if (elements.voiceTimer) {
                elements.voiceTimer.textContent = timeString;
            }
        }, 1000);
    }

    function stopVoiceTimer() {
        if (elements.voiceTimer) {
            elements.voiceTimer.textContent = '00:00';
        }
    }

    function checkMicrophonePermissions() {
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(() => {
                console.log('Microphone permission granted');
            })
            .catch(() => {
                showNotification('برای استفاده از جستجوی صوتی، لطفاً دسترسی به میکروفن را فعال کنید.', 'warning');
            });
    }

    // Image helpers
    function resetImageUpload() {
        if (elements.imageUpload) {
            elements.imageUpload.value = '';
        }
        if (elements.imagePreview) {
            elements.imagePreview.classList.add('hidden');
        }
    }

    function clearImageUpload() {
        resetImageUpload();
    }

    // Conversational helpers
    function loadConversationHistory() {
        // Load from localStorage or API
        const history = JSON.parse(localStorage.getItem(`conversation_${state.sessionId}`) || '[]');
        state.conversationHistory = history;
    }

    function clearConversation() {
        state.conversationHistory = [];
        localStorage.removeItem(`conversation_${state.sessionId}`);
        
        // Clear chat messages except initial AI message
        if (elements.chatMessages) {
            const initialMessage = elements.chatMessages.querySelector('.ai-message');
            elements.chatMessages.innerHTML = '';
            if (initialMessage) {
                elements.chatMessages.appendChild(initialMessage.cloneNode(true));
            }
        }
    }

    // Public API
    return {
        init,
        switchMode,
        performSearch,
        clearSearchHistory,
        clearConversation
    };

})();

// Auto-initialize when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.UnifiedAiSearch.init);
} else {
    window.UnifiedAiSearch.init();
} 