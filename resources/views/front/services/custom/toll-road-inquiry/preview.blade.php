{{-- Toll Road Inquiry Preview Page --}}
{{-- صفحه پیش‌نمایش استعلام عوارض آزادراهی --}}

@extends('front.services.preview-base')

@section('page_title', 'پیش‌نمایش استعلام عوارض آزادراهی')
@section('service_name', 'استعلام عوارض آزادراهی')
@section('service_description', 'استعلام و پرداخت آنلاین عوارض آزادراهی با امنیت و سرعت بالا از سامانه رسمی آزادراه‌های کشور')

@section('preview_content')
<!-- Service Introduction -->
<div class="bg-gradient-to-br from-sky-50 to-blue-50 rounded-2xl p-8 mb-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-sky-600 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0M15 17a2 2 0 104 0"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-dark-sky-700">استعلام عوارض آزادراهی پیشخوانک</h1>
                <p class="text-gray-600 mt-2 text-lg">سریع، آسان، و معتبر - مستقیم از سامانه رسمی آزادراه‌های کشور</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-gray-900">اطلاعات دقیق</p>
                    <p class="text-sm text-gray-600">تاریخ، ساعت و مبلغ هر عبور</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-sky-600 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-gray-900">امنیت بالا</p>
                    <p class="text-sm text-gray-600">اتصال مستقیم و رمزگذاری شده</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-gray-900">پرداخت فوری</p>
                    <p class="text-sm text-gray-600">پرداخت آنلاین مستقیم</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Section -->
        <div class="lg:col-span-2">
            @include('front.services.custom.toll-road-inquiry.partials.preview-form')
            @include('front.services.custom.toll-road-inquiry.partials.preview-table')
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-dark-sky-700 mb-4">آمار سامانه آنی رو</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">کل آزادراه‌ها</span>
                        <span class="font-bold text-sky-600">۲۷ آزادراه</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">استان‌های تحت پوشش</span>
                        <span class="font-bold text-sky-600">۳۱ استان</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">کل کیلومتر</span>
                        <span class="font-bold text-sky-600">۲,۵۰۰ کیلومتر</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">تعداد ایستگاه</span>
                        <span class="font-bold text-sky-600">۴۵۰ ایستگاه</span>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-amber-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    نکات مهم
                </h3>
                <ul class="space-y-2 text-sm text-amber-700">
                    <li class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 bg-amber-500 rounded-full mt-2 flex-shrink-0"></div>
                        <span>عدم پرداخت عوارض منجر به جریمه می‌شود</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 bg-amber-500 rounded-full mt-2 flex-shrink-0"></div>
                        <span>برای عبور از آزادراه، پرداخت عوارض الزامی است</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 bg-amber-500 rounded-full mt-2 flex-shrink-0"></div>
                        <span>اطلاعات از سامانه رسمی آزادراه‌ها دریافت می‌شود</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 bg-amber-500 rounded-full mt-2 flex-shrink-0"></div>
                        <span>پس از پرداخت، تأیید به صورت آنی انجام می‌شود</span>
                    </li>
                </ul>
            </div>

            <!-- Payment Methods -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-dark-sky-700 mb-4">روش‌های پرداخت</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg">
                        <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">کارت بانکی</p>
                            <p class="text-xs text-gray-500">تمامی بانک‌های ایرانی</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">کیف پول الکترونیک</p>
                            <p class="text-xs text-gray-500">پی پال، یکتاپی و...</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg">
                        <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">اینترنت بانک</p>
                            <p class="text-xs text-gray-500">درگاه‌های معتبر بانکی</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Information -->
<div class="mt-12 bg-gray-50 rounded-2xl p-8">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-dark-sky-700 mb-6 text-center">درباره سامانه استعلام عوارض آزادراهی</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">قابلیت‌های سامانه</h3>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-sky-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        استعلام دقیق عوارض تمامی آزادراه‌های کشور
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-sky-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        نمایش تاریخ و ساعت دقیق هر عبور
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-sky-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        پرداخت آنلاین و فوری عوارض
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-sky-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        دریافت رسید و تأیید پرداخت
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-3">مزایای استفاده</h3>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-green-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        صرفه‌جویی در زمان و هزینه
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-green-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        دسترسی ۲۴ ساعته و ۷ روز هفته
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-green-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        جلوگیری از جریمه و پنالتی
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-green-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        امنیت و اعتماد کامل
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection