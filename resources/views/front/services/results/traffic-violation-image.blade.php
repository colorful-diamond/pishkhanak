@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-sky-50 to-indigo-50 rounded-xl p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">نتیجه دریافت تصویر خلافی</h1>
                <p class="text-gray-600">تصویر ثبت شده از خلافی رانندگی</p>
            </div>
            <div class="text-right">
                @php
                    $hasImage = $data['data']['has_image'] ?? false;
                    $statusColor = $hasImage ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    $statusText = $hasImage ? 'تصویر موجود' : 'تصویر موجود نیست';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        @if($hasImage)
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                        @else
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        @endif
                    </svg>
                    {{ $statusText }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Image Display -->
            @if($hasImage)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    تصویر خلافی
                </h2>
                
                <div class="text-center">
                    @if(!empty($data['data']['image_url']))
                        <img src="{{ $data['data']['image_url'] }}" 
                             alt="تصویر خلافی {{ $data['data']['violation_serial'] }}"
                             class="max-w-full h-auto mx-auto rounded-lg shadow-lg border border-gray-300"
                             style="max-height: 500px;"
                             onclick="openImageModal(this.src)">
                        <p class="text-sm text-gray-500 mt-2">برای بزرگ‌نمایی روی تصویر کلیک کنید</p>
                    @elseif(!empty($data['data']['image_base64']))
                        <img src="data:image/jpeg;base64,{{ $data['data']['image_base64'] }}" 
                             alt="تصویر خلافی {{ $data['data']['violation_serial'] }}"
                             class="max-w-full h-auto mx-auto rounded-lg shadow-lg border border-gray-300"
                             style="max-height: 500px;"
                             onclick="openImageModal(this.src)">
                        <p class="text-sm text-gray-500 mt-2">برای بزرگ‌نمایی روی تصویر کلیک کنید</p>
                    @endif
                </div>
            </div>
            @else
            <!-- No Image Available -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">تصویری موجود نیست</h3>
                <p class="text-gray-600">متأسفانه تصویری برای این خلافی در سیستم موجود نمی‌باشد.</p>
            </div>
            @endif

            <!-- Violation Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    جزئیات خلافی
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-purple-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">سریال خلافی</label>
                        <p class="text-gray-800 font-mono">{{ $data['data']['violation_serial'] ?? 'نامشخص' }}</p>
                    </div>
                    
                    @if(!empty($data['data']['violation_info']['code']))
                    <div class="bg-purple-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">کد خلافی</label>
                        <p class="text-gray-800 font-mono">{{ $data['data']['violation_info']['code'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['violation_info']['description']))
                    <div class="bg-purple-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">شرح خلافی</label>
                        <p class="text-gray-800">{{ $data['data']['violation_info']['description'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['violation_info']['formatted_amount']))
                    <div class="bg-purple-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">مبلغ جریمه</label>
                        <p class="text-gray-800 font-bold">{{ $data['data']['violation_info']['formatted_amount'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['violation_info']['date_persian']) || !empty($data['data']['violation_info']['date']))
                    <div class="bg-purple-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">تاریخ خلافی</label>
                        <p class="text-gray-800">{{ $data['data']['violation_info']['date_persian'] ?: $data['data']['violation_info']['date'] }}</p>
                    </div>
                    @endif
                    
                    @if(!empty($data['data']['violation_info']['location']))
                    <div class="bg-purple-50 rounded-lg p-3">
                        <label class="block font-medium text-gray-600 mb-1">محل خلافی</label>
                        <p class="text-gray-800">{{ $data['data']['violation_info']['location'] }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Summary -->
            @if(!empty($data['data']['summary']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    خلاصه
                </h2>
                
                <div class="bg-sky-50 rounded-lg p-4">
                    <p class="text-sky-800">{{ $data['data']['summary'] }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">عملیات سریع</h3>
                
                <div class="space-y-3">
                    @if($hasImage && $data['data']['download_available'])
                    <button onclick="downloadImage()" 
                            class="w-full flex items-center justify-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        دانلود تصویر
                    </button>
                    @endif
                    
                    <button onclick="printReport()" 
                            class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        چاپ گزارش
                    </button>
                    
                    <button onclick="copyReport()" 
                            class="w-full flex items-center justify-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        کپی اطلاعات
                    </button>
                    
                    <a href="{{ route('services.show', 'traffic-violation-image') }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        دریافت تصویر جدید
                    </a>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-orange-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-orange-800 mb-3">نکات مهم</h3>
                <ul class="space-y-2 text-orange-700 text-sm">
                    @if($hasImage)
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        تصویر را برای اعتراض یا پیگیری قانونی نگهداری کنید
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        کیفیت تصویر برای شناسایی خودرو و پلاک کافی است
                    </li>
                    @else
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        عدم وجود تصویر به معنای عدم وجود خلافی نیست
                    </li>
                    @endif
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        برای اعتراض به خلافی، مراحل قانونی را دنبال کنید
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        سریال خلافی را برای مراجعات بعدی نگهداری کنید
                    </li>
                </ul>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">آمار سریع</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاریخ استعلام:</span>
                        <span class="font-medium">{{ $data['data']['processed_date'] ?? now()->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">کد پیگیری:</span>
                        <span class="font-medium font-mono text-xs">{{ $data['data']['track_id'] ?? 'نامشخص' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">وضعیت تصویر:</span>
                        <span class="font-medium {{ $hasImage ? 'text-green-600' : 'text-red-600' }}">
                            {{ $hasImage ? 'موجود' : 'موجود نیست' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="relative max-w-4xl max-h-full p-4">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modalImage" src="" alt="تصویر خلافی" class="max-w-full max-h-full object-contain">
    </div>
</div>

<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function downloadImage() {
    const imageUrl = @json($data['data']['image_url'] ?? '');
    if (imageUrl) {
        const link = document.createElement('a');
        link.href = imageUrl;
        link.download = `violation-image-${@json($data['data']['violation_serial'])}.jpg`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        showNotification('دانلود تصویر آغاز شد', 'success');
    } else {
        showNotification('خطا در دانلود تصویر', 'error');
    }
}

function copyReport() {
    const violationData = @json($data['data']);
    let text = 'گزارش تصویر خلافی\n';
    text += '='.repeat(40) + '\n\n';
    text += `سریال خلافی: ${violationData.violation_serial || 'نامشخص'}\n`;
    
    if (violationData.violation_info) {
        if (violationData.violation_info.code) text += `کد خلافی: ${violationData.violation_info.code}\n`;
        if (violationData.violation_info.description) text += `شرح: ${violationData.violation_info.description}\n`;
        if (violationData.violation_info.formatted_amount) text += `مبلغ: ${violationData.violation_info.formatted_amount}\n`;
        if (violationData.violation_info.date_persian || violationData.violation_info.date) {
            text += `تاریخ: ${violationData.violation_info.date_persian || violationData.violation_info.date}\n`;
        }
        if (violationData.violation_info.location) text += `محل: ${violationData.violation_info.location}\n`;
    }
    
    text += `وضعیت تصویر: ${violationData.has_image ? 'موجود' : 'موجود نیست'}\n`;
    text += `تاریخ استعلام: ${violationData.processed_date || new Date().toLocaleDateString('fa-IR')}\n\n`;
    
    if (violationData.summary) text += `خلاصه: ${violationData.summary}\n\n`;
    text += 'تولید شده در پیشخوانک (pishkhanak.com)';
    
    copyToClipboard(text);
}

function printReport() {
    window.print();
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('اطلاعات با موفقیت کپی شد!', 'success');
        });
    } else {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showNotification('اطلاعات با موفقیت کپی شد!', 'success');
        } catch (err) {
            showNotification('خطا در کپی کردن', 'error');
        }
        document.body.removeChild(textArea);
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => notification.classList.add('translate-x-0'), 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 3000);
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Close modal on background click
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});
</script>
@endsection