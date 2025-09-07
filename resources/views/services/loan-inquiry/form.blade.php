@extends('front.layouts.app')

@section('title', $title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Service Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $title }}</h1>
                <p class="text-gray-600">{{ $description }}</p>
            </div>

            <!-- Security Notice -->
            <div class="bg-sky-50 border border-sky-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-sky-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-sky-800">احراز هویت پیامکی مورد نیاز</h3>
                        <p class="text-sm text-sky-700 mt-1">
                            برای دسترسی به اطلاعات تسهیلات، نیاز به احراز هویت از طریق پیامک است. پس از وارد کردن اطلاعات، به صفحه احراز هویت هدایت خواهید شد.
                        </p>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form -->
            @php
                // Get current route parameters to preserve bank-specific routing
                $currentSlug1 = request()->route('slug1');
                $currentSlug2 = request()->route('slug2');
                
                // Build the correct form action based on current URL
                if ($currentSlug2) {
                    // Bank-specific service URL: /services/loan-inquiry/melli
                    $formAction = route('services.submit', ['slug1' => $currentSlug1, 'slug2' => $currentSlug2]);
                } else {
                    // Main service URL: /services/loan-inquiry
                    $formAction = route('services.submit', ['slug1' => $currentSlug1 ?? 'loan-inquiry']);
                }
            @endphp
            <form method="POST" action="{{ $formAction }}" id="loanInquiryForm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- National Code -->
                    <div>
                        <label for="national_code" class="block text-sm font-medium text-gray-700 mb-2">
                            کد ملی <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="national_code" 
                            name="national_code" 
                            maxlength="10" 
                            pattern="[0-9]{10}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500" 
                            placeholder="کد ملی 10 رقمی"
                            value="{{ old('national_code') }}"
                            required
                            autocomplete="off"
                        >
                        <p class="text-sm text-gray-500 mt-1">کد ملی 10 رقمی خود را وارد کنید</p>
                    </div>

                    <!-- Mobile -->
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">
                            شماره موبایل <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="tel" 
                            id="mobile" 
                            name="mobile" 
                            maxlength="11" 
                            pattern="09[0-9]{9}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500" 
                            placeholder="09xxxxxxxxx"
                            value="{{ old('mobile') }}"
                            required
                            autocomplete="tel"
                        >
                        <p class="text-sm text-gray-500 mt-1">شماره موبایلی که برای احراز هویت استفاده می‌شود</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8">
                    <button 
                        type="submit" 
                        class="w-full bg-sky-600 hover:bg-sky-700 text-white font-medium py-4 px-6 rounded-lg transition duration-200 flex items-center justify-center text-lg"
                        id="submitBtn"
                    >
                        <span id="submitText">شروع استعلام تسهیلات</span>
                        <svg id="loadingIcon" class="animate-spin -mr-1 ml-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                    <p class="text-sm text-gray-500 text-center mt-3">
                        با کلیک بر روی دکمه، شما را به صفحه احراز هویت پیامکی هدایت می‌کنیم
                    </p>
                </div>
            </form>
        </div>

        <!-- Information Panel -->
        <div class="bg-sky-50 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">اطلاعات مهم</h3>
            <div class="space-y-3">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-700 ml-3">اطلاعات تسهیلات تمامی بانک‌ها نمایش داده می‌شود</p>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-700 ml-3">احراز هویت از طریق سرویس‌های رسمی فین‌تک انجام می‌شود</p>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-700 ml-3">اطلاعات شما با بالاترین سطح امنیت محافظت می‌شود</p>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-sky-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-700 ml-3">پردازش معمولاً بین 10 تا 30 ثانیه زمان می‌برد</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loanInquiryForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingIcon = document.getElementById('loadingIcon');
    const nationalCodeInput = document.getElementById('national_code');
    const mobileInput = document.getElementById('mobile');

    // Format national code input (numbers only)
    nationalCodeInput.addEventListener('input', function(e) {
        const value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        e.target.value = value;
    });

    // Format mobile input (numbers only)
    mobileInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        
        // Ensure it starts with 09
        if (value.length > 0 && !value.startsWith('09')) {
            if (value.startsWith('9')) {
                value = '0' + value;
            } else if (!value.startsWith('0')) {
                value = '09' + value;
            }
        }
        
        e.target.value = value;
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        // Validate national code
        const nationalCode = nationalCodeInput.value;
        if (nationalCode.length !== 10) {
            e.preventDefault();
            alert('کد ملی باید 10 رقم باشد');
            nationalCodeInput.focus();
            return;
        }

        // Validate mobile
        const mobile = mobileInput.value;
        if (!mobile.match(/^09[0-9]{9}$/)) {
            e.preventDefault();
            alert('شماره موبایل نامعتبر است');
            mobileInput.focus();
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitText.textContent = 'در حال پردازش...';
        loadingIcon.classList.remove('hidden');
    });

    // Auto-focus on first input
    nationalCodeInput.focus();
});
</script>
@endsection 