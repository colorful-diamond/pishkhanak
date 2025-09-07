@extends('front.layouts.app')

@section('content')
<section class="flex justify-center p-4 md:p-10 w-full animate-fadeIn">
    <div class="w-full max-w-screen-lg bg-gradient-to-br from-sky-50 to-white rounded-[32px] shadow-lg border border-sky-200 overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-sky-500 to-sky-600 p-8 md:p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3 font-['IRANSans']">شرایط و قوانین استفاده</h1>
            <p class="text-sky-100 text-lg">قوانین و مقررات استفاده از خدمات پیشخوانک</p>
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
                        <p class="text-sm text-yellow-700">این شرایط و قوانین مطابق با قوانین جمهوری اسلامی ایران و قانون تجارت الکترونیکی تنظیم شده است.</p>
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
                        <h2 class="text-2xl font-bold text-sky-900">تعاریف و مقدمات</h2>
                    </div>
                    <div class="space-y-4 text-dark-sky-500 leading-relaxed">
                        <p>
                            پیشخوانک به عنوان یک پلتفرم آنلاین ارائه خدمات مالی و بانکی، متعهد به ارائه خدمات با کیفیت و مطابق با قوانین جمهوری اسلامی ایران می‌باشد. این شرایط و قوانین، چارچوب استفاده از تمامی خدمات ما را تعیین می‌کند.
                        </p>
                        <p>
                            با ثبت‌نام و استفاده از خدمات پیشخوانک، شما پذیرش کامل این شرایط و قوانین را اعلام می‌کنید. در صورت عدم موافقت با هر بخش از این شرایط، لطفاً از استفاده از خدمات ما خودداری نمایید.
                        </p>
                    </div>
                </div>

                <!-- Services Overview -->
                <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex-shrink-0 w-10 h-10 bg-sky-100 rounded-full flex items-center justify-center">
                            <span class="text-sky-600 font-bold">۲</span>
                        </div>
                        <h2 class="text-2xl font-bold text-sky-900">خدمات ارائه شده</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Banking Services -->
                        <div class="bg-green-50 rounded-xl p-6 border border-green-200">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-green-800">خدمات بانکی</h3>
                            </div>
                            <ul class="space-y-2 text-sm text-green-700">
                                <li>• کیف پول الکترونیکی</li>
                                <li>• انتقال وجه و پرداخت</li>
                                <li>• استعلامات بانکی</li>
                                <li>• احراز هویت دیجیتال</li>
                            </ul>
                        </div>

                        <!-- Government Services -->
                        <div class="bg-sky-50 rounded-xl p-6 border border-sky-200">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-sky-800">خدمات دولتی</h3>
                            </div>
                            <ul class="space-y-2 text-sm text-sky-700">
                                <li>• استعلامات ادارات</li>
                                <li>• پرداخت عوارض</li>
                                <li>• خدمات مالیاتی</li>
                                <li>• استعلام کدپستی</li>
                            </ul>
                        </div>

                        <!-- Automotive Services -->
                        <div class="bg-red-50 rounded-xl p-6 border border-red-200">
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-red-800">خدمات خودرو</h3>
                            </div>
                            <ul class="space-y-2 text-sm text-red-700">
                                <li>• استعلام خلافی</li>
                                <li>• وضعیت پلاک</li>
                                <li>• بیمه شخص ثالث</li>
                                <li>• سوابق رانندگی</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- User Responsibilities -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- User Obligations -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-bold text-sm">۳</span>
                            </div>
                            <h3 class="text-lg font-bold text-sky-900">تعهدات کاربر</h3>
                        </div>
                        <ul class="space-y-2 text-sm text-dark-sky-500">
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-sky-400 rounded-full"></span>
                                ارائه اطلاعات صحیح و کامل
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-sky-400 rounded-full"></span>
                                حفظ اطلاعات حساب کاربری
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-sky-400 rounded-full"></span>
                                عدم استفاده نامشروع از خدمات
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-sky-400 rounded-full"></span>
                                رعایت قوانین جمهوری اسلامی ایران
                            </li>
                        </ul>
                    </div>

                    <!-- Platform Obligations -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 font-bold text-sm">۴</span>
                            </div>
                            <h3 class="text-lg font-bold text-sky-900">تعهدات پیشخوانک</h3>
                        </div>
                        <ul class="space-y-2 text-sm text-dark-sky-500">
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                ارائه خدمات با کیفیت
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                حفظ امنیت اطلاعات
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                پشتیبانی مناسب
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                انطباق با قوانین
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Important Notice -->
                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-8 text-white">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-3">نکات مهم</h3>
                            <ul class="space-y-2 text-sm text-red-100">
                                <li>• تمامی تراکنش‌ها مطابق با قوانین بانک مرکزی انجام می‌شود</li>
                                <li>• سوء استفاده از خدمات منجر به مسدودی حساب کاربری خواهد شد</li>
                                <li>• پیشخوانک مسئولیتی در قبال خسارات ناشی از سوء استفاده کاربران ندارد</li>
                                <li>• تغییرات قوانین از طریق اعلان در سایت به اطلاع می‌رسد</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="bg-gradient-to-r from-sky-500 to-sky-600 rounded-2xl p-8 text-white text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-4">پشتیبانی و راهنمایی</h2>
                    <p class="text-sky-100 mb-6">برای هرگونه سؤال یا درخواست کمک در رابطه با شرایط استفاده، با ما در تماس باشید</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="bg-white bg-opacity-10 rounded-lg p-4">
                            <div class="font-semibold mb-2">ایمیل پشتیبانی</div>
                            <div>{{ \App\Helpers\SettingsHelper::getSupportEmail() }}</div>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-lg p-4">
                            <div class="font-semibold mb-2">تلفن پشتیبانی</div>
                            <div>{{ \App\Helpers\SettingsHelper::getPhone() }}</div>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-lg p-4">
                            <div class="font-semibold mb-2">ساعات پاسخگویی</div>
                            <div>{{ \App\Helpers\SettingsHelper::getWorkingHours() }}</div>
                        </div>
                    </div>
                </div>

                <!-- Legal Footer -->
                <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm">
                    <h2 class="text-xl font-bold text-sky-900 mb-6 text-center">مراجع قانونی</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                        <div class="p-4">
                            <div class="w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <div class="font-medium text-sm">بانک مرکزی ایران</div>
                            <div class="text-xs text-gray-500">نظارت بر خدمات مالی</div>
                        </div>
                        <div class="p-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="font-medium text-sm">وزارت ارتباطات</div>
                            <div class="text-xs text-gray-500">نظارت بر خدمات دیجیتال</div>
                        </div>
                        <div class="p-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l6-2m-6 2l-6-2"/>
                                </svg>
                            </div>
                            <div class="font-medium text-sm">مراجع قضایی</div>
                            <div class="text-xs text-gray-500">حل اختلافات قانونی</div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection 