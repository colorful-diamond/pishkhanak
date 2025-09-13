<!-- Negative License Score Service Information Section -->
<div class="rounded-xl md:bg-white md:rounded-xl md:shadow-sm md:border md:border-gray-200 md:p-6 bg-transparent shadow-none p-0">
    <!-- Service Header with Icon -->
    <div class="flex items-center mb-6">
        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center ml-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-bold text-gray-900">استعلام نمره منفی گواهینامه</h2>
            <p class="text-sm text-gray-600">بررسی وضعیت امتیاز منفی رانندگی</p>
        </div>
    </div>

    <!-- Request Details (if available) -->
    @if(!empty($requestDetails))
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="text-sm font-semibold text-blue-800 mb-3">اطلاعات درخواست شما:</h3>
            <div class="space-y-2 text-sm">
                @if(isset($requestDetails['license_number']))
                    <div class="flex justify-between">
                        <span class="text-blue-700">شماره گواهینامه:</span>
                        <span class="text-blue-900 font-medium">{{ $requestDetails['license_number'] }}</span>
                    </div>
                @endif
                @if(isset($requestDetails['national_code']))
                    <div class="flex justify-between">
                        <span class="text-blue-700">کد ملی:</span>
                        <span class="text-blue-900 font-medium">{{ $requestDetails['national_code'] }}</span>
                    </div>
                @endif
                @if(isset($requestDetails['mobile']))
                    <div class="flex justify-between">
                        <span class="text-blue-700">شماره موبایل:</span>
                        <span class="text-blue-900 font-medium">{{ $requestDetails['mobile'] }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Result Preview (if available) -->
    @if(!empty($previewData))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <h3 class="text-sm font-semibold text-green-800 mb-3">نتیجه استعلام:</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-green-700">نمره منفی فعلی:</span>
                        <span class="text-green-900 font-bold text-lg">
                            {{ $previewData['negative_score'] ?? '0' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-green-700">وضعیت گواهینامه:</span>
                        <span class="text-green-900 font-medium">
                            {{ $previewData['license_status'] ?? 'فعال' }}
                        </span>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-green-700">آخرین تخلف:</span>
                        <span class="text-green-900 font-medium">
                            {{ $previewData['last_violation'] ?? 'ندارد' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-green-700">تاریخ بررسی:</span>
                        <span class="text-green-900 font-medium">
                            {{ now()->format('Y/m/d H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Information Sections -->
    <div class="space-y-6">
        <!-- How the Negative Point System Works -->
        <div class="border border-gray-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 text-red-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                نحوه عملکرد سیستم نمره منفی
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="font-semibold text-yellow-800 mb-1">مرحله اول</div>
                    <div class="text-yellow-700 mb-2">30 نمره منفی</div>
                    <div class="text-yellow-600 text-xs">تعلیق 3 ماهه + 40,000 تومان</div>
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                    <div class="font-semibold text-orange-800 mb-1">مرحله دوم</div>
                    <div class="text-orange-700 mb-2">25 نمره منفی</div>
                    <div class="text-orange-600 text-xs">تعلیق 6 ماهه + 60,000 تومان</div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <div class="font-semibold text-red-800 mb-1">مرحله سوم</div>
                    <div class="text-red-700 mb-2">20 نمره منفی</div>
                    <div class="text-red-600 text-xs">باطل شدن + آزمون مجدد</div>
                </div>
            </div>
        </div>

        <!-- Common Violations with Negative Points -->
        <div class="border border-gray-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 text-red-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                تخلفات شایع دارای نمره منفی
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-700">سرعت غیرمجاز</span>
                    <span class="text-red-600 font-medium">2-5 نمره</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-700">سبقت غیرمجاز</span>
                    <span class="text-red-600 font-medium">3-6 نمره</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-700">استفاده از موبایل</span>
                    <span class="text-red-600 font-medium">2-4 نمره</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-700">عدم رعایت چراغ قرمز</span>
                    <span class="text-red-600 font-medium">4-8 نمره</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-700">رانندگی زیگزاگی</span>
                    <span class="text-red-600 font-medium">3-5 نمره</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-700">مصرف الکل</span>
                    <span class="text-red-600 font-medium">10-20 نمره</span>
                </div>
            </div>
        </div>

        <!-- How to Clear Negative Points -->
        <div class="border border-gray-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                نحوه پاک شدن نمره منفی
            </h3>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-sm text-green-800 space-y-2">
                    <p class="font-medium">پاک‌سازی خودکار:</p>
                    <ul class="list-disc list-inside space-y-1 text-green-700">
                        <li>6 ماه پس از آخرین تخلف دارای نمره منفی</li>
                        <li>عدم ارتکاب تخلف جدید در این مدت</li>
                        <li>حذف کامل تمام نمرات منفی قبلی</li>
                        <li>بازگشت به وضعیت عادی گواهینامه</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Alternative Inquiry Methods -->
        <div class="border border-gray-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 text-blue-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                سایر روش‌های استعلام
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="font-semibold text-blue-800 mb-2">سایت راهور 120</div>
                    <div class="text-blue-700 mb-1">rahvar120.ir</div>
                    <div class="text-blue-600 text-xs">رایگان • نیاز به ثبت‌نام</div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="font-semibold text-blue-800 mb-2">اپلیکیشن پلیس من</div>
                    <div class="text-blue-700 mb-1">Android & iOS</div>
                    <div class="text-blue-600 text-xs">رایگان • رسمی</div>
                </div>
            </div>
        </div>

        <!-- Warning Alert -->
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-500 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="text-sm">
                    <p class="font-semibold text-red-800 mb-1">توجه مهم:</p>
                    <p class="text-red-700">
                        برای جلوگیری از تعلیق یا باطل شدن گواهینامه، به محض دریافت اطلاعیه تخلف، نسبت به پرداخت جریمه و اعتراض در صورت نیاز اقدام کنید.
                        سیستم نمره منفی برای حفظ ایمنی جاده‌ها و کاهش تصادفات طراحی شده است.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>