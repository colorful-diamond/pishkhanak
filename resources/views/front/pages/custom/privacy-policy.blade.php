@extends('front.layouts.app')

@section('content')
<section class="flex justify-center p-4 md:p-10 w-full animate-fadeIn">
    <div class="w-full max-w-screen-lg bg-gradient-to-br from-sky-50 to-white rounded-[32px] shadow-lg border border-sky-200 overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-sky-500 to-sky-600 p-8 md:p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3 font-['IRANSans']">سیاست حفظ حریم خصوصی</h1>
            <p class="text-sky-100 text-lg">حفاظت از اطلاعات شخصی شما، اولویت اصلی ماست</p>
        </div>

        <!-- Content Section -->
        <div class="p-8 md:p-12">
            <!-- Legal Notice -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 mb-8">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-right font-['IRANSans']">
                        <p class="text-sm font-semibold text-yellow-800 mb-1">آخرین بروزرسانی: {{ \Verta::instance(now())->format('Y/m/d') }}</p>
                        <p class="text-sm text-yellow-700">این سیاست حریم خصوصی مطابق با قانون تجارت الکترونیکی ایران و اصل ۲۲ و ۲۵ قانون اساسی جمهوری اسلامی ایران تنظیم شده است.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-12 text-right font-['IRANSans']">

                <!-- Introduction Section -->
                <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex-shrink-0 w-10 h-10 bg-sky-100 rounded-full flex items-center justify-center">
                            <span class="text-sky-600 font-bold">۱</span>
                        </div>
                        <h2 class="text-2xl font-bold text-sky-900">مقدمه</h2>
                    </div>
                    <div class="space-y-4 text-dark-sky-500 leading-relaxed">
                        <p>
                            شرکت پیشخوانک (در ادامه "پیشخوانک"، "ما"، "خدمات ما") متعهد به حفظ حریم خصوصی و امنیت اطلاعات شخصی کاربران خود می‌باشد. این سیاست حریم خصوصی نحوه جمع‌آوری، استفاده، ذخیره‌سازی، و محافظت از اطلاعات شخصی شما هنگام استفاده از وب‌سایت و خدمات ما را شرح می‌دهد.
                        </p>
                        <p>
                            با استفاده از خدمات پیشخوانک، شما با شرایط این سیاست حریم خصوصی موافقت می‌کنید. در صورت عدم موافقت با هر بخش از این سیاست، لطفاً از استفاده از خدمات ما خودداری کنید.
                        </p>
                    </div>
                </div>

                <!-- Data Collection Section -->
                <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex-shrink-0 w-10 h-10 bg-sky-100 rounded-full flex items-center justify-center">
                            <span class="text-sky-600 font-bold">۲</span>
                        </div>
                        <h2 class="text-2xl font-bold text-sky-900">اطلاعات جمع‌آوری شده</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Personal Information -->
                        <div class="bg-green-50 rounded-xl p-6 border border-green-200">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-green-800">اطلاعات شخصی</h3>
                            </div>
                            <ul class="space-y-2 text-sm text-green-700">
                                <li>• اطلاعات هویتی (نام، شماره ملی)</li>
                                <li>• اطلاعات تماس (ایمیل، تلفن)</li>
                                <li>• اطلاعات حساب کاربری</li>
                                <li>• اطلاعات مالی (کارت، شبا)</li>
                            </ul>
                        </div>

                        <!-- Technical Information -->
                        <div class="bg-sky-50 rounded-xl p-6 border border-sky-200">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-sky-800">اطلاعات فنی</h3>
                            </div>
                            <ul class="space-y-2 text-sm text-sky-700">
                                <li>• آدرس IP و مرورگر</li>
                                <li>• اطلاعات دستگاه</li>
                                <li>• کوکی‌ها و جلسات</li>
                                <li>• لاگ‌های دسترسی</li>
                            </ul>
                        </div>

                        <!-- Sensitive Information -->
                        <div class="bg-red-50 rounded-xl p-6 border border-red-200">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-red-800">اطلاعات حساس</h3>
                            </div>
                            <p class="text-sm text-red-700 mb-3">مطابق ماده ۵۸ قانون تجارت الکترونیکی:</p>
                            <ul class="space-y-2 text-sm text-red-700">
                                <li>• اطلاعات مذهبی و عقیدتی</li>
                                <li>• اطلاعات سلامتی</li>
                                <li>• اطلاعات قومی</li>
                                <li>• اطلاعات خانوادگی</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Quick Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Data Collection Methods -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-bold text-sm">۳</span>
                            </div>
                            <h3 class="text-lg font-bold text-sky-900">نحوه جمع‌آوری</h3>
                        </div>
                        <ul class="space-y-2 text-sm text-dark-sky-500">
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-sky-400 rounded-full"></span>
                                ثبت‌نام و احراز هویت
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-sky-400 rounded-full"></span>
                                استفاده از خدمات بانکی
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-sky-400 rounded-full"></span>
                                فرم‌های تماس و پشتیبانی
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-sky-400 rounded-full"></span>
                                شرکای تجاری (فین‌تک، جیبیت)
                            </li>
                        </ul>
                    </div>

                    <!-- Data Usage Purposes -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-bold text-sm">۴</span>
                            </div>
                            <h3 class="text-lg font-bold text-sky-900">اهداف استفاده</h3>
                        </div>
                        <ul class="space-y-2 text-sm text-dark-sky-500">
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                ارائه خدمات مالی و بانکی
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                احراز هویت و امنیت
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                بهبود تجربه کاربری
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                انطباق با قوانین
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="bg-gradient-to-r from-sky-500 to-sky-600 rounded-2xl p-8 text-white text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-4">سؤالی دارید؟</h2>
                    <p class="text-sky-100 mb-6">برای دریافت اطلاعات بیشتر در مورد سیاست حریم خصوصی، با ما در تماس باشید</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="bg-white bg-opacity-10 rounded-lg p-4">
                            <div class="font-semibold mb-2">ایمیل</div>
                            <div>{{ \App\Helpers\SettingsHelper::getEmail() }}</div>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-lg p-4">
                            <div class="font-semibold mb-2">تلفن</div>
                            <div>{{ \App\Helpers\SettingsHelper::getPhone() }}</div>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-lg p-4">
                            <div class="font-semibold mb-2">ساعات کاری</div>
                            <div>{{ \App\Helpers\SettingsHelper::getWorkingHours() }}</div>
                        </div>
                    </div>
                </div>

                <!-- User Rights Summary -->
                <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm">
                    <h2 class="text-xl font-bold text-sky-900 mb-6 text-center">خلاصه حقوق شما</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <div class="font-medium text-sm">حق دسترسی</div>
                        </div>
                        <div class="text-center p-4">
                            <div class="w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div class="font-medium text-sm">حق تصحیح</div>
                        </div>
                        <div class="text-center p-4">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            <div class="font-medium text-sm">حق حذف</div>
                        </div>
                        <div class="text-center p-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="font-medium text-sm">حق دریافت کپی</div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection 