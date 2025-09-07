/**
 * Enhanced AI Chat System
 * Supports file uploads, image display, conversation context, and advanced features
 */

class EnhancedAiChat {
    constructor() {
        console.log('EnhancedAiChat: Constructor called');
        
        this.config = {
            apiBase: '/api/ai-search',
            maxFiles: 5,
            maxFileSize: 20 * 1024 * 1024, // 20MB
            allowedTypes: {
                image: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                document: ['application/pdf', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
            },
            sessionId: this.generateSessionId()
        };
        
        this.state = {
            isProcessing: false,
            selectedFiles: [],
            conversationHistory: [],
            isRecording: false,
            mediaRecorder: null
        };
        
        this.elements = {};
        this.init();
    }
    
    init() {
        console.log('EnhancedAiChat: Initializing...');
        this.initializeElements();
        this.setupEventListeners();
        this.setupFileHandling();
        this.loadConversationHistory();
        this.updateInitialMessage();
        console.log('EnhancedAiChat: Initialization complete');
    }
    
    initializeElements() {
        console.log('EnhancedAiChat: Initializing DOM elements...');
        
        this.elements = {
            chatMessages: document.querySelector('.ai-chat-messages'),
            chatInput: document.getElementById('ai-chat-input'),
            sendBtn: document.querySelector('.ai-send-btn'),
            attachBtn: document.getElementById('ai-attach-btn'),
            voiceBtn: document.getElementById('ai-voice-btn'),
            fileInput: document.getElementById('ai-file-input'),
            fileUploadArea: document.getElementById('ai-file-upload-area'),
            selectedFiles: document.getElementById('ai-selected-files')
        };
        
        // Critical debugging for chat input element
        console.log('EnhancedAiChat: Chat input element details:', {
            element: this.elements.chatInput,
            exists: !!this.elements.chatInput,
            id: this.elements.chatInput?.id,
            className: this.elements.chatInput?.className,
            style: this.elements.chatInput?.style.cssText,
            parentElement: this.elements.chatInput?.parentElement,
            isVisible: this.elements.chatInput ? this.isElementVisible(this.elements.chatInput) : false
        });
        
        // If chat input not found, keep trying with detailed logging
        if (!this.elements.chatInput) {
            console.error('EnhancedAiChat: CRITICAL - Chat input element not found initially!');
            this.retryFindingChatInput();
        }
        
        console.log('EnhancedAiChat: DOM elements initialized', {
            chatMessages: !!this.elements.chatMessages,
            chatInput: !!this.elements.chatInput,
            sendBtn: !!this.elements.sendBtn,
            attachBtn: !!this.elements.attachBtn,
            voiceBtn: !!this.elements.voiceBtn,
            fileInput: !!this.elements.fileInput,
            fileUploadArea: !!this.elements.fileUploadArea,
            selectedFiles: !!this.elements.selectedFiles
        });
    }
    
    retryFindingChatInput() {
        let attempts = 0;
        const maxAttempts = 10;
        
        const retry = () => {
            attempts++;
            console.log(`EnhancedAiChat: Retry attempt ${attempts} to find chat input`);
            
            // Try multiple selectors
            this.elements.chatInput = document.getElementById('ai-chat-input') ||
                                     document.querySelector('#ai-chat-input') ||
                                     document.querySelector('input[id="ai-chat-input"]') ||
                                     document.querySelector('.ai-chat-input');
            
            if (this.elements.chatInput) {
                console.log('EnhancedAiChat: Chat input found on retry!', {
                    attempt: attempts,
                    element: this.elements.chatInput,
                    id: this.elements.chatInput.id,
                    className: this.elements.chatInput.className
                });
                return;
            }
            
            if (attempts < maxAttempts) {
                setTimeout(retry, 200);
            } else {
                console.error('EnhancedAiChat: Failed to find chat input after maximum attempts');
            }
        };
        
        setTimeout(retry, 100);
    }
    
    isElementVisible(element) {
        if (!element) return false;
        
        const style = window.getComputedStyle(element);
        const rect = element.getBoundingClientRect();
        
        return style.display !== 'none' &&
               style.visibility !== 'hidden' &&
               style.opacity !== '0' &&
               rect.width > 0 &&
               rect.height > 0;
    }
    
    setupEventListeners() {
        console.log('EnhancedAiChat: Setting up event listeners...');
        
        // Chat input events
        this.elements.chatInput?.addEventListener('input', (e) => {
            console.log('EnhancedAiChat: Chat input changed');
        });
        
        this.elements.chatInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        // Send button
        this.elements.sendBtn?.addEventListener('click', () => {
            console.log('EnhancedAiChat: Send button clicked');
            this.sendMessage();
        });
        
        // File attach button
        this.elements.attachBtn?.addEventListener('click', () => {
            console.log('EnhancedAiChat: Attach button clicked');
            this.toggleFileUpload();
        });
        
        // Voice button
        this.elements.voiceBtn?.addEventListener('click', () => {
            console.log('EnhancedAiChat: Voice button clicked');
            this.toggleVoiceRecording();
        });
        
        console.log('EnhancedAiChat: Event listeners setup complete');
    }
    
    setupFileHandling() {
        console.log('EnhancedAiChat: Setting up file handling...');
        
        if (!this.elements.fileInput) {
            console.warn('EnhancedAiChat: File input element not found');
            return;
        }
        
        // File input change
        this.elements.fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            console.log('EnhancedAiChat: File input changed', { fileCount: files.length });
            this.handleFileSelection(files);
        });
        
        // Drag and drop
        const dropZone = document.querySelector('.ai-file-drop-zone');
        if (dropZone) {
            console.log('EnhancedAiChat: Drop zone found, setting up drag and drop');
            
            dropZone.addEventListener('click', () => {
                console.log('EnhancedAiChat: Drop zone clicked');
                this.elements.fileInput?.click();
            });
            
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-blue-500', 'bg-blue-50');
            });
            
            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            });
            
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-blue-500', 'bg-blue-50');
                
                const files = Array.from(e.dataTransfer.files);
                console.log('EnhancedAiChat: Files dropped', { fileCount: files.length });
                this.handleFileSelection(files);
            });
        }
        
        console.log('EnhancedAiChat: File handling setup complete');
    }
    
    generateSessionId() {
        const sessionId = 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
        console.log('EnhancedAiChat: Generated session ID', sessionId);
        return sessionId;
    }
    
    async sendMessage() {
        console.log('EnhancedAiChat: sendMessage called');
        
        const message = this.elements.chatInput?.value.trim();
        console.log('EnhancedAiChat: Message to send', { message, hasFiles: this.state.selectedFiles.length > 0 });
        
        if (!message && this.state.selectedFiles.length === 0) {
            console.log('EnhancedAiChat: No message or files to send');
            return;
        }
        
        // Clear input immediately to prevent double-sending
        if (this.elements.chatInput) {
            this.elements.chatInput.value = '';
        }
        
        return this.processSendMessage(message);
    }
    
    async processSendMessage(message) {
        if (this.state.isProcessing) {
            console.log('EnhancedAiChat: Already processing, aborting');
            return;
        }
        
        this.state.isProcessing = true;
        console.log('EnhancedAiChat: Processing started');
        this.updateSendButton(true);
        this.disableInputField();
        
        try {
            // Add user message to chat
            this.addMessage(message || 'فایل ارسال شده', 'user', this.state.selectedFiles);
            
            // Show typing indicator
            this.addTypingIndicator();
            
            // Prepare form data
            const formData = new FormData();
            formData.append('query', message || 'فایل ارسال شده');
            formData.append('session_id', this.config.sessionId);
            
            // Add files
            this.state.selectedFiles.forEach((fileInfo, index) => {
                if (fileInfo.file) {
                    formData.append(`files[${index}]`, fileInfo.file);
                }
            });
            
            // Send request
            const response = await fetch(`${this.config.apiBase}/conversational`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const result = await response.json();
            console.log('EnhancedAiChat: API response', result);
            
            // Remove typing indicator
            this.removeTypingIndicator();
            
            if (result.success) {
                // Add AI response
                this.addMessage(result.data.ai_response, 'ai');
                
                // Update conversation history
                const historyEntry = {
                    user: message,
                    ai: result.data.ai_response,
                    timestamp: new Date().toISOString(),
                    files: this.state.selectedFiles.map(f => ({
                        name: f.name,
                        type: f.type,
                        url: f.preview
                    }))
                };
                this.state.conversationHistory.push(historyEntry);
                
            } else {
                this.addMessage(result.message || 'متاسفم، خطایی رخ داده است.', 'ai', [], 'error');
            }
            
        } catch (error) {
            console.error('EnhancedAiChat: Chat error', error);
            this.removeTypingIndicator();
            this.addMessage('متاسفم، در حال حاضر امکان پاسخگویی وجود ندارد.', 'ai', [], 'error');
        } finally {
            this.state.isProcessing = false;
            console.log('EnhancedAiChat: Processing complete, re-enabling interface');
            
            this.updateSendButton(false);
            this.enableInputField();
            this.clearSelectedFiles();
            
            // Ensure input field is visible and focused after a delay
            setTimeout(() => {
                console.log('EnhancedAiChat: Final input field check');
                
                if (!this.elements.chatInput) {
                    console.error('EnhancedAiChat: Input field lost after processing! Searching again...');
                    this.retryFindingChatInput();
                } else {
                    console.log('EnhancedAiChat: Input field status:', {
                        exists: !!this.elements.chatInput,
                        visible: this.isElementVisible(this.elements.chatInput),
                        disabled: this.elements.chatInput.disabled,
                        value: this.elements.chatInput.value
                    });
                    
                    // Force focus
                    this.elements.chatInput.focus();
                    console.log('EnhancedAiChat: Input field focused');
                }
            }, 200);
            
            // Monitor input field for next 5 seconds
            this.monitorInputField();
        }
    }
    
    addMessage(content, sender, files = [], type = 'normal') {
        console.log('EnhancedAiChat: Adding message', { sender, type, hasFiles: files.length > 0 });
        
        if (!this.elements.chatMessages) {
            console.error('EnhancedAiChat: Chat messages container not found');
            return;
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'} mb-4`;
        
        const messageContent = document.createElement('div');
        messageContent.className = `max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
            sender === 'user' 
                ? 'bg-blue-500 text-white' 
                : type === 'error' 
                    ? 'bg-red-100 text-red-800' 
                    : 'bg-gray-100 text-gray-800'
        }`;
        
        // Add message content
        messageContent.innerHTML = this.formatMessage(content);
        
        // Add files if any
        if (files && files.length > 0) {
            const filesDiv = this.createFilesDisplay(files);
            messageContent.appendChild(filesDiv);
        }
        
        messageDiv.appendChild(messageContent);
        this.elements.chatMessages.appendChild(messageDiv);
        
        // Scroll to bottom
        this.scrollToBottom();
    }
    
    createFilesDisplay(files) {
        const filesDiv = document.createElement('div');
        filesDiv.className = 'mt-2 space-y-2';
        
        files.forEach(file => {
            const fileDiv = document.createElement('div');
            fileDiv.className = 'flex items-center text-sm bg-white bg-opacity-20 rounded p-2';
            
            if (file.type && file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = file.preview || file.url;
                img.className = 'w-8 h-8 rounded object-cover mr-2';
                fileDiv.appendChild(img);
            } else {
                const icon = document.createElement('div');
                icon.className = 'w-8 h-8 bg-gray-300 rounded mr-2 flex items-center justify-center';
                icon.innerHTML = '📄';
                fileDiv.appendChild(icon);
            }
            
            const fileName = document.createElement('span');
            fileName.textContent = file.name;
            fileDiv.appendChild(fileName);
            
            filesDiv.appendChild(fileDiv);
        });
        
        return filesDiv;
    }
    
    formatMessage(content) {
        if (!content) return '';
        
        // Convert URLs to links
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        content = content.replace(urlRegex, '<a href="$1" target="_blank" class="underline">$1</a>');
        
        // Convert line breaks to HTML
        content = content.replace(/\n/g, '<br>');
        
        return content;
    }
    
    addTypingIndicator() {
        if (document.querySelector('.typing-indicator')) {
            return; // Already exists
        }
        
        const typingDiv = document.createElement('div');
        typingDiv.className = 'flex justify-start mb-4 typing-indicator';
        
        const typingContent = document.createElement('div');
        typingContent.className = 'max-w-xs lg:max-w-md px-4 py-2 rounded-lg bg-gray-100 text-gray-800';
        typingContent.innerHTML = `
            <div class="flex items-center">
                <div class="typing-dots">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
                <span class="mr-2">در حال تایپ...</span>
            </div>
        `;
        
        typingDiv.appendChild(typingContent);
        this.elements.chatMessages.appendChild(typingDiv);
        
        this.scrollToBottom();
    }
    
    removeTypingIndicator() {
        const typingIndicator = document.querySelector('.typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }
    
    handleFileSelection(files) {
        console.log('EnhancedAiChat: Handling file selection', { count: files.length });
        
        for (const file of files) {
            if (this.state.selectedFiles.length >= this.config.maxFiles) {
                this.showNotification(`حداکثر ${this.config.maxFiles} فایل قابل انتخاب است.`, 'warning');
                break;
            }
            
            if (this.validateFile(file)) {
                const fileInfo = {
                    file: file,
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    preview: null
                };
                
                // Create preview for images
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        fileInfo.preview = e.target.result;
                        this.updateFileDisplay();
                    };
                    reader.readAsDataURL(file);
                }
                
                this.state.selectedFiles.push(fileInfo);
            }
        }
        
        this.updateFileDisplay();
        
        // Clear file input
        if (this.elements.fileInput) {
            this.elements.fileInput.value = '';
        }
    }
    
    validateFile(file) {
        // Check file size
        if (file.size > this.config.maxFileSize) {
            this.showNotification(`فایل ${file.name} بزرگتر از حد مجاز است.`, 'error');
            return false;
        }
        
        // Check file type
        const category = this.getFileTypeCategory(file.type);
        if (!category) {
            this.showNotification(`نوع فایل ${file.name} پشتیبانی نمی‌شود.`, 'error');
            return false;
        }
        
        return true;
    }
    
    getFileTypeCategory(mimeType) {
        for (const [category, types] of Object.entries(this.config.allowedTypes)) {
            if (types.includes(mimeType)) {
                return category;
            }
        }
        return null;
    }
    
    updateFileDisplay() {
        if (!this.elements.selectedFiles) return;
        
        this.elements.selectedFiles.innerHTML = '';
        
        this.state.selectedFiles.forEach((file, index) => {
            const fileDiv = document.createElement('div');
            fileDiv.className = 'flex items-center justify-between bg-gray-50 p-2 rounded border';
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'flex items-center';
            
            if (file.preview) {
                const img = document.createElement('img');
                img.src = file.preview;
                img.className = 'w-8 h-8 rounded object-cover mr-2';
                fileInfo.appendChild(img);
            } else {
                const icon = document.createElement('div');
                icon.className = 'w-8 h-8 bg-gray-300 rounded mr-2 flex items-center justify-center';
                icon.innerHTML = '📄';
                fileInfo.appendChild(icon);
            }
            
            const fileName = document.createElement('span');
            fileName.textContent = file.name;
            fileName.className = 'text-sm';
            fileInfo.appendChild(fileName);
            
            const removeBtn = document.createElement('button');
            removeBtn.className = 'text-red-500 hover:text-red-700 ml-2';
            removeBtn.innerHTML = '×';
            removeBtn.addEventListener('click', () => this.removeFile(index));
            
            fileDiv.appendChild(fileInfo);
            fileDiv.appendChild(removeBtn);
            
            this.elements.selectedFiles.appendChild(fileDiv);
        });
        
        // Show/hide file upload area
        if (this.state.selectedFiles.length > 0) {
            this.elements.selectedFiles.parentElement?.classList.remove('hidden');
        } else {
            this.elements.selectedFiles.parentElement?.classList.add('hidden');
        }
    }
    
    removeFile(index) {
        this.state.selectedFiles.splice(index, 1);
        this.updateFileDisplay();
    }
    
    clearSelectedFiles() {
        this.state.selectedFiles = [];
        this.updateFileDisplay();
    }
    
    disableInputField() {
        console.log('EnhancedAiChat: Disabling input field');
        
        if (!this.elements.chatInput) {
            console.error('EnhancedAiChat: Chat input element not found when trying to disable');
            this.retryFindingChatInput();
            return;
        }
        
        console.log('EnhancedAiChat: Chat input state before disable:', {
            element: this.elements.chatInput,
            disabled: this.elements.chatInput.disabled,
            visible: this.isElementVisible(this.elements.chatInput),
            parentVisible: this.isElementVisible(this.elements.chatInput.parentElement),
            style: this.elements.chatInput.style.cssText
        });
        
        this.elements.chatInput.disabled = true;
        this.elements.chatInput.classList.add('opacity-50');
        this.elements.chatInput.placeholder = 'در حال پردازش...';
        
        console.log('EnhancedAiChat: Input field disabled successfully');
    }
    
    enableInputField() {
        console.log('EnhancedAiChat: Enabling input field');
        
        if (!this.elements.chatInput) {
            console.error('EnhancedAiChat: Chat input element not found when trying to enable - searching again');
            this.retryFindingChatInput();
            
            // Wait a bit and try again
            setTimeout(() => {
                if (this.elements.chatInput) {
                    this.enableInputField();
                }
            }, 100);
            return;
        }
        
        console.log('EnhancedAiChat: Chat input state before enable:', {
            element: this.elements.chatInput,
            disabled: this.elements.chatInput.disabled,
            visible: this.isElementVisible(this.elements.chatInput),
            parentVisible: this.isElementVisible(this.elements.chatInput.parentElement),
            style: this.elements.chatInput.style.cssText,
            parentStyle: this.elements.chatInput.parentElement?.style.cssText
        });
        
        // Re-enable the input
        this.elements.chatInput.disabled = false;
        this.elements.chatInput.classList.remove('opacity-50');
        this.elements.chatInput.placeholder = 'پیام خود را بنویسید...';
        
        // Force visibility
        this.elements.chatInput.style.display = 'block';
        this.elements.chatInput.style.visibility = 'visible';
        this.elements.chatInput.style.opacity = '1';
        
        // Check parent containers for visibility
        let parent = this.elements.chatInput.parentElement;
        while (parent && parent !== document.body) {
            if (parent.style.display === 'none' || parent.classList.contains('hidden')) {
                console.warn('EnhancedAiChat: Found hidden parent container:', {
                    element: parent,
                    className: parent.className,
                    style: parent.style.cssText
                });
                
                // Force parent visibility
                parent.style.display = '';
                parent.classList.remove('hidden');
            }
            parent = parent.parentElement;
        }
        
        console.log('EnhancedAiChat: Chat input state after enable:', {
            element: this.elements.chatInput,
            disabled: this.elements.chatInput.disabled,
            visible: this.isElementVisible(this.elements.chatInput),
            parentVisible: this.isElementVisible(this.elements.chatInput.parentElement)
        });
        
        console.log('EnhancedAiChat: Input field enabled successfully');
    }
    
    updateSendButton(isDisabled) {
        if (this.elements.sendBtn) {
            this.elements.sendBtn.disabled = isDisabled;
            if (isDisabled) {
                this.elements.sendBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                this.elements.sendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    }
    
    toggleFileUpload() {
        if (this.elements.fileUploadArea?.classList.contains('hidden')) {
            this.showFileUpload();
        } else {
            this.hideFileUpload();
        }
    }
    
    showFileUpload() {
        this.elements.fileUploadArea?.classList.remove('hidden');
    }
    
    hideFileUpload() {
        this.elements.fileUploadArea?.classList.add('hidden');
    }
    
    toggleVoiceRecording() {
        if (this.state.isRecording) {
            this.stopVoiceRecording();
        } else {
            this.startVoiceRecording();
        }
    }
    
    async startVoiceRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            this.state.mediaRecorder = new MediaRecorder(stream);
            this.state.isRecording = true;
            
            const audioChunks = [];
            this.state.mediaRecorder.ondataavailable = (event) => {
                audioChunks.push(event.data);
            };
            
            this.state.mediaRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                this.handleAudioRecording(audioBlob);
            };
            
            this.state.mediaRecorder.start();
            this.updateVoiceButton(true);
            
        } catch (error) {
            console.error('Voice recording error:', error);
            this.showNotification('خطا در دسترسی به میکروفون', 'error');
        }
    }
    
    stopVoiceRecording() {
        if (this.state.mediaRecorder && this.state.isRecording) {
            this.state.mediaRecorder.stop();
            this.state.isRecording = false;
            this.updateVoiceButton(false);
        }
    }
    
    handleAudioRecording(audioBlob) {
        // Convert audio to file and add to selected files
        const audioFile = new File([audioBlob], `recording_${Date.now()}.wav`, { type: 'audio/wav' });
        this.handleFileSelection([audioFile]);
    }
    
    updateVoiceButton(isRecording) {
        if (this.elements.voiceBtn) {
            if (isRecording) {
                this.elements.voiceBtn.classList.add('bg-red-500', 'animate-pulse');
                this.elements.voiceBtn.innerHTML = '⏹️';
            } else {
                this.elements.voiceBtn.classList.remove('bg-red-500', 'animate-pulse');
                this.elements.voiceBtn.innerHTML = '🎤';
            }
        }
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    scrollToBottom() {
        if (this.elements.chatMessages) {
            this.elements.chatMessages.scrollTop = this.elements.chatMessages.scrollHeight;
        }
    }
    
    loadConversationHistory() {
        try {
            const saved = localStorage.getItem(`ai_chat_history_${this.config.sessionId}`);
            if (saved) {
                this.state.conversationHistory = JSON.parse(saved);
                console.log('EnhancedAiChat: Conversation history loaded');
            }
        } catch (error) {
            console.error('EnhancedAiChat: Failed to load conversation history', error);
        }
    }
    
    saveConversationHistory() {
        try {
            localStorage.setItem(`ai_chat_history_${this.config.sessionId}`, JSON.stringify(this.state.conversationHistory));
        } catch (error) {
            console.error('EnhancedAiChat: Failed to save conversation history', error);
        }
    }
    
    updateInitialMessage() {
        const initialMessage = document.querySelector('.ai-chat-initial-message');
        if (initialMessage) {
            initialMessage.textContent = 'چطور می‌تونم کمکتون کنم؟ می‌تونید متن بنویسید یا فایل ارسال کنید.';
        }
    }
    
    monitorInputField() {
        let checks = 0;
        const maxChecks = 25; // 5 seconds at 200ms intervals
        
        const checkInterval = setInterval(() => {
            checks++;
            
            if (!this.elements.chatInput) {
                console.error(`EnhancedAiChat: Monitor check ${checks} - Input field missing!`);
                this.retryFindingChatInput();
            } else if (!this.isElementVisible(this.elements.chatInput)) {
                console.warn(`EnhancedAiChat: Monitor check ${checks} - Input field not visible!`, {
                    element: this.elements.chatInput,
                    style: this.elements.chatInput.style.cssText,
                    computedStyle: window.getComputedStyle(this.elements.chatInput).display,
                    parentVisible: this.isElementVisible(this.elements.chatInput.parentElement)
                });
                
                // Try to fix visibility
                this.enableInputField();
            } else {
                console.log(`EnhancedAiChat: Monitor check ${checks} - Input field OK`);
            }
            
            if (checks >= maxChecks) {
                clearInterval(checkInterval);
                console.log('EnhancedAiChat: Input field monitoring complete');
            }
        }, 200);
    }
}

// Initialize when DOM is ready - only if chat container exists
document.addEventListener('DOMContentLoaded', () => {
    console.log('EnhancedAiChat: DOM content loaded');
    
    if (document.querySelector('.ai-chat-messages')) {
        console.log('EnhancedAiChat: Chat messages container found, initializing');
        window.enhancedAiChat = new EnhancedAiChat();
        
        // Add global debug function
        window.debugAiChat = () => {
            const chat = window.enhancedAiChat;
            if (!chat) {
                console.log('EnhancedAiChat instance not found');
                return;
            }
            
            console.log('=== AI Chat Debug Info ===');
            console.log('Chat instance:', chat);
            console.log('Elements:', chat.elements);
            console.log('State:', chat.state);
            console.log('Config:', chat.config);
            
            if (chat.elements.chatInput) {
                console.log('Input field details:', {
                    element: chat.elements.chatInput,
                    id: chat.elements.chatInput.id,
                    disabled: chat.elements.chatInput.disabled,
                    visible: chat.isElementVisible(chat.elements.chatInput),
                    style: chat.elements.chatInput.style.cssText,
                    computedStyle: window.getComputedStyle(chat.elements.chatInput),
                    parent: chat.elements.chatInput.parentElement,
                    parentVisible: chat.isElementVisible(chat.elements.chatInput.parentElement)
                });
            } else {
                console.log('Input field: NOT FOUND');
                
                // Try to find it manually
                const foundInput = document.getElementById('ai-chat-input');
                console.log('Manual search result:', foundInput);
            }
            
            console.log('=== End Debug Info ===');
        };
        
        console.log('EnhancedAiChat: Global debug function available as window.debugAiChat()');
        
    } else {
        console.log('EnhancedAiChat: Chat messages container not found, skipping initialization');
    }
}); 