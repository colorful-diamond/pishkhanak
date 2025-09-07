/**
 * Enhanced AI Chat System
 * Supports file uploads, image display, conversation context, and advanced features
 */

class EnhancedAiChat {
    constructor() {
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
        this.initializeElements();
        this.setupEventListeners();
        this.setupFileHandling();
        this.loadConversationHistory();
        this.updateInitialMessage();
    }
    
    initializeElements() {
        this.elements = {
            chatMessages: document.querySelector('.ai-chat-messages'),
            chatInput: document.getElementById('ai-chat-input'),
            sendBtn: document.querySelector('.ai-send-btn'),
            attachBtn: document.getElementById('ai-attach-btn'),
            voiceBtn: document.getElementById('ai-voice-btn'),
            fileInput: document.getElementById('ai-file-input'),
            fileUploadArea: document.getElementById('ai-file-upload-area'),
            selectedFiles: document.getElementById('ai-selected-files'),
            suggestedQuestions: document.querySelectorAll('.suggested-question')
        };
    }
    
    setupEventListeners() {
        // Chat input events
        this.elements.chatInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        // Send button
        this.elements.sendBtn?.addEventListener('click', () => this.sendMessage());
        
        // File attach button
        this.elements.attachBtn?.addEventListener('click', () => this.toggleFileUpload());
        
        // Voice button
        this.elements.voiceBtn?.addEventListener('click', () => this.toggleVoiceRecording());
        
        // Suggested questions
        this.elements.suggestedQuestions?.forEach(btn => {
            btn.addEventListener('click', () => {
                const question = btn.textContent.trim();
                this.elements.chatInput.value = question;
                this.sendMessage();
            });
        });
    }
    
    setupFileHandling() {
        if (!this.elements.fileInput) return;
        
        // File input change
        this.elements.fileInput.addEventListener('change', (e) => {
            this.handleFileSelection(Array.from(e.target.files));
        });
        
        // Drag and drop
        const dropZone = document.querySelector('.ai-file-drop-zone');
        if (dropZone) {
            dropZone.addEventListener('click', () => this.elements.fileInput.click());
            
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-sky-500', 'bg-sky-100');
            });
            
            dropZone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-sky-500', 'bg-sky-100');
            });
            
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-sky-500', 'bg-sky-100');
                const files = Array.from(e.dataTransfer.files);
                this.handleFileSelection(files);
            });
        }
    }
    
    generateSessionId() {
        return 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
    }
    
    async sendMessage() {
        const message = this.elements.chatInput?.value.trim();
        if (!message && this.state.selectedFiles.length === 0) return;
        if (this.state.isProcessing) return;
        
        this.state.isProcessing = true;
        this.updateSendButton(true);
        
        try {
            // Add user message to chat
            this.addMessage(message || 'فایل ارسال شده', 'user', this.state.selectedFiles);
            
            // Clear input
            if (this.elements.chatInput) {
                this.elements.chatInput.value = '';
            }
            
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
            
            // Remove typing indicator
            this.removeTypingIndicator();
            
            if (result.success) {
                // Add AI response - ensure HTML content is properly handled
                const aiResponse = result.data.ai_response;
                console.log('AI Response:', aiResponse);
                console.log('Contains HTML:', aiResponse.includes('<') && aiResponse.includes('>'));
                
                // Decode HTML entities and ensure proper HTML rendering
                const decodedResponse = this.decodeHtmlContent(aiResponse);
                this.addMessage(decodedResponse, 'ai');
                
                // Handle service suggestions
                if (result.data.suggested_services && result.data.suggested_services.length > 0) {
                    this.addServiceSuggestions(result.data.suggested_services);
                }
                
                // Handle service form data
                if (result.data.service_form_data && result.data.service_url) {
                    this.addServiceAction(result.data.service_url, result.data.service_form_data);
                }
                
                // Update conversation history
                this.state.conversationHistory.push({
                    user: message,
                    ai: result.data.ai_response,
                    timestamp: new Date().toISOString(),
                    files: this.state.selectedFiles.map(f => ({
                        name: f.name,
                        type: f.type,
                        url: f.preview
                    }))
                });
                
            } else {
                this.addMessage(result.message || 'متاسفم، خطایی رخ داده است.', 'ai', [], 'error');
            }
            
        } catch (error) {
            console.error('Chat error:', error);
            this.removeTypingIndicator();
            this.addMessage('متاسفم، در حال حاضر امکان پاسخگویی وجود ندارد.', 'ai', [], 'error');
        } finally {
            this.state.isProcessing = false;
            this.updateSendButton(false);
            this.clearSelectedFiles();
        }
    }
    
    addMessage(content, sender, files = [], type = 'normal') {
        if (!this.elements.chatMessages) return;
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex items-start space-y-3 ${sender === 'user' ? 'justify-end' : ''}`;
        
        const avatar = this.createAvatar(sender);
        const messageContent = this.createMessageContent(content, sender, files, type);
        
        if (sender === 'user') {
            messageDiv.appendChild(messageContent);
            messageDiv.appendChild(avatar);
        } else {
            messageDiv.appendChild(avatar);
            messageDiv.appendChild(messageContent);
        }
        
        this.elements.chatMessages.appendChild(messageDiv);
        this.scrollToBottom();
    }
    
    createAvatar(sender) {
        const avatar = document.createElement('div');
        avatar.className = `w-8 h-8 rounded-full flex items-center justify-center ${
            sender === 'user' ? 'bg-yellow-500' : 'bg-sky-500'
        }`;
        
        const icon = document.createElement('svg');
        icon.className = 'w-4 h-4 text-white';
        icon.setAttribute('fill', 'none');
        icon.setAttribute('stroke', 'currentColor');
        icon.setAttribute('viewBox', '0 0 24 24');
        
        if (sender === 'user') {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>';
        } else {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>';
        }
        
        avatar.appendChild(icon);
        return avatar;
    }
    
    createMessageContent(content, sender, files = [], type = 'normal') {
        const messageContent = document.createElement('div');
        
        // Handle special message types
        if (type === 'suggestions') {
            messageContent.className = `flex-1 bg-yellow-50 border-yellow-200 -mr-4 ml-3 border rounded-2xl rounded-tr-sm p-4 max-w-md`;
        } else if (type === 'action') {
            messageContent.className = `flex-1 bg-green-50 border-green-200 -mr-4 ml-3 border rounded-2xl rounded-tr-sm p-4 max-w-md`;
        } else {
            messageContent.className = `flex-1 ${sender === 'user' ? 'bg-yellow-100 border-yellow-200 ml-3 -mr-4' : 'bg-sky-100 border-sky-200 -mr-4 ml-3'} border rounded-2xl ${sender === 'user' ? 'rounded-tl-sm' : 'rounded-tr-sm'} p-4 max-w-md`;
        }
        
        // Add error styling if needed
        if (type === 'error') {
            messageContent.className = messageContent.className.replace('bg-sky-100 border-sky-200', 'bg-red-100 border-red-200');
        }
        
        // Add files if any
        if (files && files.length > 0) {
            const filesContainer = this.createFilesDisplay(files);
            messageContent.appendChild(filesContainer);
        }
        
        // Add text content
        if (content) {
            const textContent = document.createElement('div');
            if (type === 'suggestions') {
                textContent.className = 'text-yellow-900';
                textContent.innerHTML = content; // Already formatted HTML for suggestions
            } else if (type === 'action') {
                textContent.className = 'text-green-900';
                textContent.innerHTML = content; // Already formatted HTML for actions
            } else {
                textContent.className = `text-${sender === 'user' ? 'yellow' : type === 'error' ? 'red' : 'sky'}-900`;
                const formattedContent = this.formatMessage(content);
                console.log('Setting innerHTML to:', formattedContent);
                console.log('Content type:', typeof formattedContent);
                console.log('Content includes HTML:', formattedContent.includes('<') && formattedContent.includes('>'));
                textContent.innerHTML = formattedContent;
                
                // Debug: log the actual DOM content after setting innerHTML
                console.log('DOM innerHTML after setting:', textContent.innerHTML);
                console.log('DOM textContent after setting:', textContent.textContent);
            }
            messageContent.appendChild(textContent);
        }
        
        return messageContent;
    }
    
    createFilesDisplay(files) {
        const container = document.createElement('div');
        container.className = 'mb-3 space-y-2';
        
        files.forEach(file => {
            const fileDiv = document.createElement('div');
            fileDiv.className = 'flex items-center space-x-2 p-2 bg-white rounded-lg border';
            
            if (file.type === 'image' && file.preview) {
                const img = document.createElement('img');
                img.src = file.preview;
                img.className = 'w-16 h-16 object-cover rounded';
                img.alt = file.name;
                fileDiv.appendChild(img);
            } else {
                const icon = document.createElement('div');
                icon.className = 'w-8 h-8 bg-gray-200 rounded flex items-center justify-center';
                icon.innerHTML = '<svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
                fileDiv.appendChild(icon);
            }
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'flex-1 min-w-0';
            fileInfo.innerHTML = `
                <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                <p class="text-xs text-gray-500">${this.formatFileSize(file.size)}</p>
            `;
            fileDiv.appendChild(fileInfo);
            
            container.appendChild(fileDiv);
        });
        
        return container;
    }
    
    decodeHtmlContent(content) {
        // Create a temporary element to decode HTML entities
        const tempElement = document.createElement('textarea');
        tempElement.innerHTML = content;
        let decodedContent = tempElement.value;
        
        // Handle JSON escaped characters
        decodedContent = decodedContent.replace(/\\\//g, '/');
        decodedContent = decodedContent.replace(/\\"/g, '"');
        decodedContent = decodedContent.replace(/\\'/g, "'");
        decodedContent = decodedContent.replace(/\\n/g, '\n');
        decodedContent = decodedContent.replace(/\\r/g, '\r');
        decodedContent = decodedContent.replace(/\\t/g, '\t');
        
        console.log('Decoded content:', decodedContent);
        return decodedContent;
    }
    
    formatMessage(content) {
        // Enhanced formatting that handles both HTML and plain text
        console.log('formatMessage input:', content);
        
        // First, check if content contains HTML tags
        if (content.includes('<') && content.includes('>')) {
            console.log('Returning HTML as is');
            // Content already contains HTML tags, return as is for innerHTML
            return content;
        }
        
        // Basic formatting for plain text
        console.log('Formatting as plain text');
        return content
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>');
    }
    
    addTypingIndicator() {
        if (document.querySelector('.typing-indicator')) return;
        
        const indicator = document.createElement('div');
        indicator.className = 'typing-indicator flex items-start space-y-3';
        
        const avatar = this.createAvatar('ai');
        const dots = document.createElement('div');
        dots.className = 'flex-1 bg-sky-100 border-sky-200 border rounded-2xl rounded-tr-sm p-4 -mr-4 ml-3';
        dots.innerHTML = `
            <div class="flex space-x-1">
                <div class="w-2 h-2 bg-sky-400 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-sky-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-sky-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        `;
        
        indicator.appendChild(avatar);
        indicator.appendChild(dots);
        
        this.elements.chatMessages?.appendChild(indicator);
        this.scrollToBottom();
    }
    
    removeTypingIndicator() {
        const indicator = document.querySelector('.typing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }
    
    handleFileSelection(files) {
        if (files.length === 0) return;
        
        // Check file count limit
        if (this.state.selectedFiles.length + files.length > this.config.maxFiles) {
            this.showNotification(`حداکثر ${this.config.maxFiles} فایل مجاز است.`, 'error');
            return;
        }
        
        files.forEach(file => {
            // Validate file
            if (!this.validateFile(file)) return;
            
            const fileInfo = {
                file: file,
                name: file.name,
                size: file.size,
                type: this.getFileTypeCategory(file.type),
                preview: null
            };
            
            // Create preview for images
            if (fileInfo.type === 'image') {
                const reader = new FileReader();
                reader.onload = (e) => {
                    fileInfo.preview = e.target.result;
                    this.updateFileDisplay();
                };
                reader.readAsDataURL(file);
            }
            
            this.state.selectedFiles.push(fileInfo);
        });
        
        this.updateFileDisplay();
        this.elements.fileInput.value = ''; // Reset input
    }
    
    validateFile(file) {
        // Check file size
        if (file.size > this.config.maxFileSize) {
            this.showNotification(`حجم فایل ${file.name} بیش از حد مجاز است.`, 'error');
            return false;
        }
        
        // Check file type
        const allowedTypes = [...this.config.allowedTypes.image, ...this.config.allowedTypes.document];
        if (!allowedTypes.includes(file.type)) {
            this.showNotification(`نوع فایل ${file.name} مجاز نیست.`, 'error');
            return false;
        }
        
        return true;
    }
    
    getFileTypeCategory(mimeType) {
        if (this.config.allowedTypes.image.includes(mimeType)) {
            return 'image';
        } else if (this.config.allowedTypes.document.includes(mimeType)) {
            return 'document';
        }
        return 'unknown';
    }
    
    updateFileDisplay() {
        if (!this.elements.selectedFiles) return;
        
        if (this.state.selectedFiles.length === 0) {
            this.elements.selectedFiles.classList.add('hidden');
            return;
        }
        
        this.elements.selectedFiles.classList.remove('hidden');
        this.elements.selectedFiles.innerHTML = '';
        
        this.state.selectedFiles.forEach((fileInfo, index) => {
            const fileDiv = document.createElement('div');
            fileDiv.className = 'flex items-center justify-between p-3 bg-white border border-sky-200 rounded-lg';
            
            const leftSide = document.createElement('div');
            leftSide.className = 'flex items-center space-x-3';
            
            // File icon or preview
            if (fileInfo.type === 'image' && fileInfo.preview) {
                const img = document.createElement('img');
                img.src = fileInfo.preview;
                img.className = 'w-12 h-12 object-cover rounded';
                leftSide.appendChild(img);
            } else {
                const icon = document.createElement('div');
                icon.className = 'w-12 h-12 bg-sky-100 rounded flex items-center justify-center';
                icon.innerHTML = '<svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
                leftSide.appendChild(icon);
            }
            
            // File info
            const info = document.createElement('div');
            info.innerHTML = `
                <p class="text-sm font-medium text-gray-900">${fileInfo.name}</p>
                <p class="text-xs text-gray-500">${this.formatFileSize(fileInfo.size)}</p>
            `;
            leftSide.appendChild(info);
            
            // Remove button
            const removeBtn = document.createElement('button');
            removeBtn.className = 'text-red-500 hover:text-red-700 p-1';
            removeBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            removeBtn.onclick = () => this.removeFile(index);
            
            fileDiv.appendChild(leftSide);
            fileDiv.appendChild(removeBtn);
            
            this.elements.selectedFiles.appendChild(fileDiv);
        });
    }
    
    removeFile(index) {
        this.state.selectedFiles.splice(index, 1);
        this.updateFileDisplay();
    }
    
    clearSelectedFiles() {
        this.state.selectedFiles = [];
        this.updateFileDisplay();
        this.hideFileUpload();
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
            this.state.audioChunks = [];
            
            this.state.mediaRecorder.ondataavailable = (event) => {
                this.state.audioChunks.push(event.data);
            };
            
            this.state.mediaRecorder.onstop = () => {
                const audioBlob = new Blob(this.state.audioChunks, { type: 'audio/wav' });
                this.handleAudioRecording(audioBlob);
            };
            
            this.state.mediaRecorder.start();
            this.state.isRecording = true;
            this.updateVoiceButton(true);
            
        } catch (error) {
            console.error('Voice recording error:', error);
            this.showNotification('خطا در دسترسی به میکروفون', 'error');
        }
    }
    
    stopVoiceRecording() {
        if (this.state.mediaRecorder && this.state.isRecording) {
            this.state.mediaRecorder.stop();
            this.state.mediaRecorder.stream.getTracks().forEach(track => track.stop());
            this.state.isRecording = false;
            this.updateVoiceButton(false);
        }
    }
    
    handleAudioRecording(audioBlob) {
        // For now, just show a placeholder message
        // In production, this would be sent to speech-to-text service
        this.showNotification('ضبط صدا تکمیل شد. قابلیت تبدیل گفتار به متن به زودی اضافه خواهد شد.', 'info');
    }
    
    updateVoiceButton(isRecording) {
        if (!this.elements.voiceBtn) return;
        
        if (isRecording) {
            this.elements.voiceBtn.classList.remove('bg-green-400', 'hover:bg-green-500');
            this.elements.voiceBtn.classList.add('bg-red-400', 'hover:bg-red-500', 'animate-pulse');
            this.elements.voiceBtn.title = 'توقف ضبط';
        } else {
            this.elements.voiceBtn.classList.remove('bg-red-400', 'hover:bg-red-500', 'animate-pulse');
            this.elements.voiceBtn.classList.add('bg-green-400', 'hover:bg-green-500');
            this.elements.voiceBtn.title = 'ضبط صدا';
        }
    }
    
    updateSendButton(isDisabled) {
        if (!this.elements.sendBtn) return;
        
        if (isDisabled) {
            this.elements.sendBtn.disabled = true;
            this.elements.sendBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            this.elements.sendBtn.disabled = false;
            this.elements.sendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    
    addServiceSuggestions(services) {
        // Create service suggestions as a separate chat message
        const suggestionsContent = this.createServiceSuggestionsContent(services);
        this.addMessage(suggestionsContent, 'ai', [], 'suggestions');
    }
    
    createServiceSuggestionsContent(services) {
        const suggestionsDiv = document.createElement('div');
        suggestionsDiv.className = 'p-3 bg-yellow-50 border border-yellow-200 rounded-lg';
        
        const title = document.createElement('p');
        title.className = 'text-sm font-medium text-yellow-800 mb-3';
        title.innerHTML = '<strong>خدمات پیشنهادی:</strong>';
        suggestionsDiv.appendChild(title);
        
        const servicesList = document.createElement('div');
        servicesList.className = 'space-y-2';
        
        services.forEach(service => {
            const serviceBtn = document.createElement('button');
            serviceBtn.className = 'block w-full text-left px-3 py-2 text-sm text-yellow-700 hover:bg-yellow-100 rounded transition-colors border border-yellow-300 hover:border-yellow-400';
            serviceBtn.textContent = service.title || service;
            serviceBtn.onclick = () => {
                this.elements.chatInput.value = `اطلاعات بیشتر در مورد ${service.title || service}`;
                this.sendMessage();
            };
            servicesList.appendChild(serviceBtn);
        });
        
        suggestionsDiv.appendChild(servicesList);
        return suggestionsDiv.outerHTML;
    }
    
    addServiceAction(serviceUrl, formData) {
        // Create service action as a separate chat message
        const actionContent = this.createServiceActionContent(serviceUrl, formData);
        this.addMessage(actionContent, 'ai', [], 'action');
    }
    
    createServiceActionContent(serviceUrl, formData) {
        const actionDiv = document.createElement('div');
        actionDiv.className = 'p-3 bg-green-50 border border-green-200 rounded-lg';
        
        const title = document.createElement('p');
        title.className = 'text-sm font-medium text-green-800 mb-3';
        title.innerHTML = '<strong>اطلاعات جمع‌آوری شد:</strong>';
        actionDiv.appendChild(title);
        
        const actionBtn = document.createElement('a');
        actionBtn.href = serviceUrl;
        actionBtn.target = '_blank';
        actionBtn.className = 'inline-block px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors';
        actionBtn.textContent = 'ادامه در صفحه سرویس';
        actionDiv.appendChild(actionBtn);
        
        return actionDiv.outerHTML;
    }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'success' ? 'bg-green-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    scrollToBottom() {
        if (this.elements.chatMessages) {
            this.elements.chatMessages.scrollTop = this.elements.chatMessages.scrollHeight;
        }
    }
    
    loadConversationHistory() {
        // Load from localStorage if available
        const saved = localStorage.getItem(`ai_chat_history_${this.config.sessionId}`);
        if (saved) {
            try {
                this.state.conversationHistory = JSON.parse(saved);
            } catch (e) {
                console.error('Failed to load conversation history:', e);
            }
        }
    }
    
    saveConversationHistory() {
        // Save to localStorage
        localStorage.setItem(
            `ai_chat_history_${this.config.sessionId}`,
            JSON.stringify(this.state.conversationHistory.slice(-20)) // Keep last 20 exchanges
        );
    }
    
    updateInitialMessage() {
        // Update the initial AI message to be more dynamic
        const initialMessage = this.elements.chatMessages?.querySelector('.flex-1 p');
        if (initialMessage) {
            initialMessage.textContent = 'چطور می‌تونم کمکتون کنم؟ می‌تونید متن بنویسید یا فایل ارسال کنید.';
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.ai-chat-messages')) {
        window.enhancedAiChat = new EnhancedAiChat();
    }
}); 