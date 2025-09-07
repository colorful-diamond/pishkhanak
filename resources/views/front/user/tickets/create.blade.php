@extends('front.layouts.app')

@section('content')
<div class="">
    <div class="container mx-auto px-4 py-6">
        <!-- Header - more compact -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-xl font-bold text-gray-900 mb-1">ایجاد درخواست جدید</h1>
                    <p class="text-gray-600 text-sm md:text-xs">فرم ایجاد درخواست پشتیبانی جدید</p>
                </div>
                <a href="{{ route('app.user.tickets.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    بازگشت
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Include Sidebar Component -->

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            @include('front.user.partials.sidebar')
            
            <!-- Main Content -->
            <div class="lg:col-span-3 col-span-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">اطلاعات درخواست</h2>

                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-400 mt-0.5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-red-800 mb-1">خطاهای موجود:</h3>
                                    <ul class="text-sm text-red-700 space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>• {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('app.user.tickets.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">موضوع درخواست *</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors"
                                   placeholder="موضوع درخواست خود را وارد کنید">
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">دسته‌بندی *</label>
                            <select id="category" name="category" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                                <option value="">دسته‌بندی را انتخاب کنید</option>
                                <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>فنی</option>
                                <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>مالی</option>
                                <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>عمومی</option>
                                <option value="bug_report" {{ old('category') === 'bug_report' ? 'selected' : '' }}>گزارش خطا</option>
                                <option value="feature_request" {{ old('category') === 'feature_request' ? 'selected' : '' }}>درخواست ویژگی</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">توضیحات *</label>
                            <textarea id="description" name="description" rows="6" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors resize-none"
                                      placeholder="جزئیات مشکل یا درخواست خود را به طور کامل توضیح دهید...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Attachments -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">فایل‌های پیوست (اختیاری)</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-sky-400 transition-colors">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-sm text-gray-600 mb-2">فایل‌های خود را اینجا بکشید یا کلیک کنید</p>
                                <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.zip,.rar"
                                       class="hidden" id="file-input">
                                <label for="file-input" 
                                       class="inline-flex items-center px-4 py-2 bg-sky-100 text-sky-700 rounded-lg hover:bg-sky-200 transition-colors cursor-pointer">
                                    انتخاب فایل
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </label>
                            </div>
                            <div id="file-list" class="mt-4 space-y-2"></div>
                            <p class="mt-2 text-xs text-gray-500">حداکثر 5 فایل، هر فایل حداکثر 10 مگابایت</p>
                        </div>

                        <!-- Tips -->
                        <div class="bg-sky-50 border border-sky-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-sky-400 mt-0.5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-sky-800 mb-1">نکات مهم:</h4>
                                    <ul class="text-sm text-sky-700 space-y-1">
                                        <li>• موضوع درخواست را به طور واضح و مختصر بنویسید</li>
                                        <li>• جزئیات کامل مشکل را در توضیحات وارد کنید</li>
                                        <li>• در صورت نیاز، تصاویر یا فایل‌های مربوطه را پیوست کنید</li>
                                        <li>• اولویت مناسب را انتخاب کنید تا سریع‌تر پاسخ داده شود</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3 space-x-reverse">
                            <a href="{{ route('app.user.tickets.index') }}" 
                               class="px-6 py-3 bg-sky-200 text-gray-700 rounded-lg hover:bg-sky-300 transition-colors">
                                انصراف
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                                ایجاد درخواست
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-input');
    const fileList = document.getElementById('file-list');
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