@extends('front.layouts.app')

@section('content')

    <div class="container mx-auto px-4 py-6">
        <!-- Header - more compact -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-4 space-x-reverse mb-2">
                        <a href="{{ route('app.user.tickets.index') }}" 
                           class="inline-flex items-center px-3 py-2 bg-sky-200 text-gray-700 rounded-lg hover:bg-sky-300 transition-colors">
                            بازگشت
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </a>
                        <h1 class="text-2xl md:text-xl font-bold text-gray-900">{{ $ticket->subject }}</h1>
                    </div>
                    <div class="flex items-center space-x-6 space-x-reverse text-sm md:text-xs text-gray-600">
                        <span>شماره درخواست: {{ $ticket->ticket_number }}</span>
                        <span>تاریخ ایجاد: {{ \Verta::instance($ticket->created_at)->format('Y/m/d H:i') }}</span>
                        <span>دسته‌بندی: {{ $ticket->getCategoryText() }}</span>
                    </div>
                </div>
                <div class="flex items-center space-x-3 space-x-reverse">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $ticket->getStatusColor() }}">
                        {{ $ticket->getStatusText() }}
                    </span>
                    @if($ticket->isOpen())
                        <form method="POST" action="{{ route('app.user.tickets.close', $ticket) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
                                    onclick="return confirm('آیا مطمئن هستید که می‌خواهید این درخواست را ببندید؟')">
                                بستن درخواست
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Include Sidebar Component -->

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            @include('front.user.partials.sidebar')

            <!-- Main Content -->
            <div class="lg:col-span-3 col-span-4">
                <!-- Messages Thread -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">گفتگو</h2>
                    
                    <div class="space-y-4">
                        @foreach($ticket->publicMessages as $message)
                            <div class="flex space-x-4 space-x-reverse {{ $message->user_id === Auth::id() ? 'flex-row-reverse' : '' }}">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-sky-500 flex items-center justify-center">
                                        <span class="text-white text-sm font-bold">
                                            {{ substr($message->user->name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 {{ $message->user_id === Auth::id() ? 'text-left' : 'text-right' }}">
                                    <div class="bg-sky-50 rounded-lg p-4 {{ $message->user_id === Auth::id() ? 'bg-sky-50' : 'bg-sky-50' }}">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-medium text-gray-900 text-sm">{{ $message->user->name }}</span>
                                            <span class="text-xs text-gray-500">{{ \Verta::instance($message->created_at)->format('Y/m/d H:i') }}</span>
                                        </div>
                                        <div class="text-gray-700 leading-relaxed text-sm">
                                            {!! $message->getFormattedMessage() !!}
                                        </div>
                                        
                                        <!-- Attachments -->
                                        @if($message->attachments && $message->attachments->count() > 0)
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <h5 class="text-xs font-medium text-gray-700 mb-2">فایل‌های پیوست:</h5>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    @foreach($message->attachments as $attachment)
                                                        <a href="{{ $attachment->getDownloadUrl() }}" 
                                                           class="flex items-center space-x-2 space-x-reverse p-2 bg-white rounded-lg border border-gray-200 hover:bg-sky-50 transition-colors">
                                                            <div class="w-6 h-6 bg-sky-100 rounded-lg flex items-center justify-center">
                                                                <svg class="w-3 h-3 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                </svg>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-xs font-medium text-gray-900 truncate">{{ $attachment->original_filename }}</p>
                                                                <p class="text-xs text-gray-500">{{ $attachment->file_size }}</p>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Reply Form -->
                @if($ticket->isOpen())
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-4">ارسال پاسخ</h3>
                        
                        <form method="POST" action="{{ route('app.user.tickets.messages.store', $ticket) }}" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">پیام شما</label>
                                <textarea id="message" name="message" rows="4" required
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors resize-none"
                                          placeholder="پیام خود را بنویسید..."></textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">فایل‌های پیوست (اختیاری)</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-sky-400 transition-colors">
                                    <svg class="w-6 h-6 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-sm text-gray-600 mb-2">فایل‌های خود را اینجا بکشید یا کلیک کنید</p>
                                    <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.zip,.rar"
                                           class="hidden" id="reply-file-input">
                                    <label for="reply-file-input" 
                                           class="inline-flex items-center px-4 py-2 bg-sky-100 text-sky-700 rounded-lg hover:bg-sky-200 transition-colors cursor-pointer">
                                        انتخاب فایل
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </label>
                                </div>
                                <div id="reply-file-list" class="mt-4 space-y-2"></div>
                                @error('attachments.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-3 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                                    <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    ارسال پاسخ
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bg-sky-50 rounded-lg p-6 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">این درخواست بسته شده است</h3>
                        <p class="text-xs text-gray-600">در صورت نیاز به کمک بیشتر، می‌توانید درخواست جدیدی ایجاد کنید.</p>
                        <a href="{{ route('app.user.tickets.create') }}" 
                           class="inline-flex items-center mt-4 px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            ایجاد درخواست جدید
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('reply-file-input');
    const fileList = document.getElementById('reply-file-list');
    const dropZone = document.querySelector('.border-dashed');

    if (fileInput && fileList && dropZone) {
        // File input change
        fileInput.addEventListener('change', function(e) {
            handleFiles(e.target.files);
        });

        // Drag and drop
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('border-sky-400', 'bg-sky-50');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-sky-400', 'bg-sky-50');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-sky-400', 'bg-sky-50');
            handleFiles(e.dataTransfer.files);
        });

        function handleFiles(files) {
            fileList.innerHTML = '';
            
            Array.from(files).forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between p-3 bg-sky-50 rounded-lg';
                
                const fileInfo = document.createElement('div');
                fileInfo.className = 'flex items-center space-x-3 space-x-reverse';
                
                const fileIcon = document.createElement('div');
                fileIcon.className = 'w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center';
                fileIcon.innerHTML = '<svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
                
                const fileName = document.createElement('span');
                fileName.className = 'text-sm font-medium text-gray-900';
                fileName.textContent = file.name;
                
                const fileSize = document.createElement('span');
                fileSize.className = 'text-xs text-gray-500';
                fileSize.textContent = formatFileSize(file.size);
                
                fileInfo.appendChild(fileIcon);
                fileInfo.appendChild(fileName);
                fileInfo.appendChild(fileSize);
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'text-red-500 hover:text-red-700 transition-colors';
                removeBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
                removeBtn.onclick = function() {
                    fileItem.remove();
                };
                
                fileItem.appendChild(fileInfo);
                fileItem.appendChild(removeBtn);
                fileList.appendChild(fileItem);
            });
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }
});
</script>
@endpush
@endsection 