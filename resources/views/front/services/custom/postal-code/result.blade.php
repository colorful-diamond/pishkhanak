@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-sky-50 to-indigo-50 rounded-xl p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">نتیجه استعلام کد پستی</h1>
                <p class="text-gray-600">اطلاعات کامل کد پستی {{ $data['formatted_postal_code'] ?? $data['postal_code'] }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    موفق
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Address Information -->
            @if(isset($data['address_info']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    اطلاعات آدرس
                </h2>
                
                <div class="space-y-4">
                    <div class="bg-sky-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">آدرس کامل</label>
                        <p class="text-gray-800 leading-relaxed">{{ $data['address_info']['full_address'] ?? 'در دسترس نیست' }}</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-sky-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-600 mb-1">استان</label>
                            <p class="text-gray-800 font-medium">{{ $data['address_info']['province'] ?? 'نامشخص' }}</p>
                        </div>
                        <div class="bg-sky-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-600 mb-1">شهر</label>
                            <p class="text-gray-800 font-medium">{{ $data['address_info']['city'] ?? 'نامشخص' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Postal Code Structure -->
            @if(isset($data['additional_info']['postal_code_structure']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    ساختار کد پستی
                </h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center bg-sky-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-sky-600 mb-1">{{ $data['additional_info']['postal_code_structure']['region'] }}</div>
                        <div class="text-sm text-gray-600">کد منطقه</div>
                    </div>
                    <div class="text-center bg-green-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600 mb-1">{{ $data['additional_info']['postal_code_structure']['zone'] }}</div>
                        <div class="text-sm text-gray-600">کد ناحیه</div>
                    </div>
                    <div class="text-center bg-yellow-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-yellow-600 mb-1">{{ $data['additional_info']['postal_code_structure']['area'] }}</div>
                        <div class="text-sm text-gray-600">کد منطقه</div>
                    </div>
                    <div class="text-center bg-red-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-red-600 mb-1">{{ $data['additional_info']['postal_code_structure']['building'] }}</div>
                        <div class="text-sm text-gray-600">کد ساختمان</div>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <div class="inline-flex items-center space-x-2 space-x-reverse bg-sky-100 rounded-lg px-4 py-2">
                        <span class="text-gray-600">فرمت:</span>
                        <span class="font-mono text-lg font-bold text-gray-800">{{ $data['formatted_postal_code'] ?? $data['postal_code'] }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Validation Status -->
            @if(isset($data['additional_info']['validation']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    وضعیت اعتبارسنجی
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <span class="text-gray-700">کد معتبر</span>
                        <span class="text-green-600">
                            @if($data['additional_info']['validation']['is_valid'])
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-sky-50 rounded-lg">
                        <span class="text-gray-700">فرمت صحیح</span>
                        <span class="text-sky-600">
                            @if($data['additional_info']['validation']['format_check'])
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <span class="text-gray-700">ساختار صحیح</span>
                        <span class="text-purple-600">
                            @if($data['additional_info']['validation']['structure_check'])
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </span>
                    </div>
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
                    <button onclick="copyToClipboard('{{ $data['postal_code'] }}')" 
                            class="w-full flex items-center justify-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        کپی کد پستی
                    </button>
                    
                    <button onclick="copyToClipboard('{{ $data['address_info']['full_address'] ?? '' }}')" 
                            class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        کپی آدرس
                    </button>
                    
                    <a href="{{ route('services.show', 'postal-code') }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        استعلام جدید
                    </a>
                </div>
            </div>


            <!-- Usage Tips -->
            @if(isset($data['additional_info']['usage_tips']))
            <div class="bg-sky-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-sky-800 mb-3">نکات کاربردی</h3>
                <p class="text-sky-700 text-sm leading-relaxed">{{ $data['additional_info']['usage_tips'] }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('متن با موفقیت کپی شد!', 'success');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showNotification('متن با موفقیت کپی شد!', 'success');
        } catch (err) {
            showNotification('خطا در کپی کردن', 'error');
        }
        document.body.removeChild(textArea);
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => notification.classList.add('translate-x-0'), 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 3000);
}
</script>
@endsection 