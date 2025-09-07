@extends('front.layouts.app')

@section('title', 'نتیجه درخواست - ' . $service->title)

@section('content')
<div class="min-h-screen/2 bg-gradient-to-br from-green-50 via-sky-50 to-purple-50 py-8 px-4">
    <div class="max-w-2xl mx-auto">
        
        <!-- Success Header Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-green-100 overflow-hidden mb-6">
            <!-- Header Section -->


            <!-- Progress Steps -->
            <div class="p-6 bg-green-50/50">
                <div class="flex items-center justify-center space-x-4 space-x-reverse">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="mr-2 text-sm text-gray-600">ورود اطلاعات</span>
                    </div>
                    
                    <div class="w-16 h-0.5 bg-green-500"></div>
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="mr-2 text-sm text-gray-600">تایید کد</span>
                    </div>
                    
                    <div class="w-16 h-0.5 bg-green-500"></div>
                    
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center animate-pulse">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="mr-2 text-sm text-green-700 font-medium">تکمیل</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 mb-6">
            
            <!-- SMS Notification Section -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
                    <svg class="w-10 h-10 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                
                <h2 class="text-2xl font-bold text-gray-900 mb-4">نتیجه به شماره شما ارسال می‌شود</h2>
                <p class="text-gray-600 text-lg leading-relaxed mb-6">
                    {{ $resultData['message'] ?? 'درخواست شما با موفقیت ثبت شد. نتیجه استعلام در حداکثر ۱۵ دقیقه به شماره موبایل شما ارسال خواهد شد.' }}
                </p>
            </div>

            <!-- Request Details -->
            <div class="bg-gradient-to-r from-sky-50 to-purple-50 rounded-2xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 ml-2 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    جزئیات درخواست
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl p-4 border border-sky-100">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">نوع استعلام:</span>
                            <span class="font-semibold text-gray-800">{{ $resultData['data']['inquiry_type'] ?? 'استعلام امتیاز اعتباری' }}</span>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl p-4 border border-sky-100">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">وضعیت:</span>
                            <span class="font-semibold text-sky-600">{{ $resultData['data']['status'] ?? 'در حال پردازش' }}</span>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl p-4 border border-sky-100">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">زمان تقریبی دریافت:</span>
                            <span class="font-semibold text-green-600">{{ $resultData['data']['estimated_delivery'] ?? '۱۵ دقیقه' }}</span>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl p-4 border border-sky-100">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">شماره موبایل:</span>
                            <span class="font-semibold text-gray-800 font-mono">{{ $result->input_data['mobile'] ?? 'شماره موبایل شما' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timer Section -->
            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-6 mb-8 text-center">
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-yellow-600 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-yellow-800 font-semibold">زمان تقریبی دریافت نتیجه</span>
                </div>
                <div class="text-3xl font-bold text-yellow-700 mb-2">حداکثر ۱۵ دقیقه</div>
                <p class="text-yellow-600 text-sm">نتیجه استعلام از طریق پیامک به شماره موبایل شما ارسال خواهد شد</p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <!-- Follow-up Button (Future Functionality) -->
                <button type="button" 
                        id="followUpBtn"
                        class="w-full bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-bold py-4 px-6 rounded-2xl hover:from-purple-600 hover:to-indigo-700 focus:ring-4 focus:ring-purple-200 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    پیگیری درخواست
                    <span class="text-purple-200 text-sm mr-2">(بزودی)</span>
                </button>

                <!-- Return to Services -->
                <a href="{{ route('services.show', $service->slug) }}" 
                   class="block w-full text-center bg-sky-100 text-gray-700 font-medium py-3 px-6 rounded-2xl hover:bg-sky-200 transition-all duration-200">
                    <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    درخواست جدید
                </a>

                <!-- Return Home -->
                <a href="{{ route('app.page.home') }}" 
                   class="block w-full text-center bg-sky-100 text-sky-700 font-medium py-3 px-6 rounded-2xl hover:bg-sky-200 transition-all duration-200">
                    <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    بازگشت به صفحه اصلی
                </a>
            </div>
        </div>

        <!-- Information Section -->
        <div class="bg-sky-50 border border-sky-200 rounded-2xl p-6">
            <h3 class="text-sky-800 font-semibold mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                اطلاعات مهم
            </h3>
            <ul class="text-sky-700 text-sm space-y-3">
                <li class="flex items-start">
                    <span class="w-2 h-2 bg-sky-400 rounded-full mt-2 ml-2 flex-shrink-0"></span>
                    نتیجه استعلام به صورت خودکار از سامانه دریافت و به شماره موبایل شما ارسال می‌شود
                </li>
                <li class="flex items-start">
                    <span class="w-2 h-2 bg-sky-400 rounded-full mt-2 ml-2 flex-shrink-0"></span>
                    در صورت عدم دریافت پیامک تا ۱۵ دقیقه، لطفاً مجدداً درخواست ارسال کنید
                </li>
                <li class="flex items-start">
                    <span class="w-2 h-2 bg-sky-400 rounded-full mt-2 ml-2 flex-shrink-0"></span>
                    پیامک حاوی اطلاعات محرمانه است، آن را در اختیار اشخاص غیر قرار ندهید
                </li>
                <li class="flex items-start">
                    <span class="w-2 h-2 bg-sky-400 rounded-full mt-2 ml-2 flex-shrink-0"></span>
                    در صورت نیاز به پشتیبانی، با شماره تماس سایت تماس بگیرید
                </li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const followUpBtn = document.getElementById('followUpBtn');
    
    // Follow-up button functionality (placeholder for future implementation)
    followUpBtn.addEventListener('click', function() {
        // Show coming soon message
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        Toast.fire({
            icon: 'info',
            title: 'قابلیت پیگیری درخواست بزودی اضافه خواهد شد'
        });
    });
    
    // Auto-refresh disabled to prevent duplicate requests
    // Users can manually check status if needed
});
</script>
@endpush

@push('styles')
<style>
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    .gradient-text {
        background: linear-gradient(45deg, #3B82F6, #8B5CF6, #EC4899);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    .shimmer {
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
    }
</style>
@endpush
@endsection 