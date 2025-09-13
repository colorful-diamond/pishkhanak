@extends('front.services.custom.upper-base')

@section('form_action')
    {{ route('front.services.vehicle-inquiry.process') }}
@endsection

@section('form_fields')
    <div class="grid grid-cols-1 gap-4">
        <!-- Vehicle Plate Input -->
        <div class="space-y-2">
            <label for="license_plate" class="block text-sm font-medium text-gray-700">
                شماره پلاک خودرو <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input 
                    type="text" 
                    id="license_plate" 
                    name="license_plate" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-normal focus:border-transparent text-right"
                    placeholder="مثال: ۱۲ ج ۳۴۵ ایران ۱۶"
                    required
                    pattern="[۰-۹0-9]{2}\s*[آ-ی]{1}\s*[۰-۹0-9]{3}\s*(ایران|iran)\s*[۰-۹0-9]{2}"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                شماره پلاک را به صورت کامل وارد کنید. مثال: ۱۲ ج ۳۴۵ ایران ۱۶
            </p>
        </div>

        <!-- National Code Input -->
        <div class="space-y-2">
            <label for="national_code" class="block text-sm font-medium text-gray-700">
                کد ملی مالک خودرو <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input 
                    type="text" 
                    id="national_code" 
                    name="national_code" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-normal focus:border-transparent text-right"
                    placeholder="کد ملی ۱۰ رقمی"
                    required
                    pattern="[۰-۹0-9]{10}"
                    maxlength="10"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                کد ملی باید ۱۰ رقم باشد و با کد ملی مالک خودرو در سامانه راهور مطابقت داشته باشد
            </p>
        </div>

        <!-- Mobile Number Input -->
        <div class="space-y-2">
            <label for="mobile" class="block text-sm font-medium text-gray-700">
                شماره موبایل <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input 
                    type="tel" 
                    id="mobile" 
                    name="mobile" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-normal focus:border-transparent text-right"
                    placeholder="۰۹۱۲۳۴۵۶۷۸۹"
                    required
                    pattern="0[۰-۹0-9]{10}"
                    maxlength="11"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                شماره موبایل برای دریافت کد تایید و اطلاع‌رسانی نتایج استفاده می‌شود
            </p>
        </div>
    </div>
@endsection

@section('submit_text')
    استعلام طرح ترافیک
@endsection

@section('additional_content')
    <!-- Service Information Section -->
    <div class="mt-8 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">درباره سرویس استعلام طرح ترافیک</h3>
                <p class="text-gray-700 text-sm leading-relaxed mb-4">
                    این سرویس به شما امکان استعلام وضعیت طرح ترافیک خودرو را در شهر تهران فراهم می‌کند. با وارد کردن شماره پلاک و کد ملی، می‌توانید از آخرین وضعیت خودرو خود در سامانه طرح ترافیک مطلع شوید.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">اطلاعات آنلاین و به‌روز</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">سرعت بالا در پردازش</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">امنیت کامل اطلاعات</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">پشتیبانی ۲۴ ساعته</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comprehensive Guide Section -->
    <div class="mt-8 space-y-6">
        <!-- Main Title -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                راهنمای کامل استعلام طرح ترافیک خودرو در تهران
            </h1>
            <p class="text-lg text-gray-600 leading-relaxed max-w-4xl mx-auto">
                طرح ترافیک تهران یکی از مهم‌ترین طرح‌های کنترل آلودگی هوا و کاهش ترافیک شهری است که برای کلیه خودروها در محدوده‌های مشخص اعمال می‌شود. در ادامه راهنمای جامعی از نحوه استعلام و کلیه اطلاعات مرتبط با طرح ترافیک ارائه می‌دهیم.
            </p>
        </div>

        <!-- Traffic Plan Overview -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    کلیات طرح ترافیک تهران
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="prose prose-sm max-w-none">
                    <p class="text-gray-700 leading-relaxed mb-4">
                        طرح ترافیک تهران از سال ۱۳۵۸ با هدف کاهش آلودگی هوا و بهبود وضعیت ترافیک شهری اجرا شده است. این طرح شامل محدودیت‌هایی برای تردد خودروها در منطقه مرکزی تهران می‌باشد که به صورت روزانه و با توجه به شماره پلاک خودروها اعمال می‌شود.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
                        <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                            <h4 class="font-semibold text-amber-800 mb-2">محدوده زمانی اجرا</h4>
                            <p class="text-amber-700 text-sm">شنبه تا چهارشنبه: ۶:۳۰ تا ۱۷:۰۰</p>
                            <p class="text-amber-700 text-sm">پنج‌شنبه: ۶:۳۰ تا ۱۵:۰۰</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <h4 class="font-semibold text-red-800 mb-2">محدوده جغرافیایی</h4>
                            <p class="text-red-700 text-sm">منطقه محدود ترافیک مرکز تهران</p>
                            <p class="text-red-700 text-sm">شامل ۲۲ کیلومتر مربع از مرکز شهر</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 mb-3">اهداف اصلی طرح ترافیک</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700">
                        <li>کاهش آلودگی هوا و بهبود کیفیت زندگی شهروندان</li>
                        <li>کاهش حجم ترافیک در مرکز شهر و افزایش سرعت جابجایی</li>
                        <li>تشویق استفاده از حمل و نقل عمومی</li>
                        <li>کنترل و مدیریت تردد خودروهای شخصی</li>
                        <li>حفاظت از محیط زیست و کاهش انتشار آلاینده‌ها</li>
                    </ul>

                    <h3 class="text-lg font-semibold text-gray-900 mb-3 mt-6">روش‌های اجرای طرح</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 leading-relaxed mb-3">
                            طرح ترافیک بر اساس آخرین رقم شماره پلاک خودروها اجرا می‌شود. هر روز خودروهایی که آخرین رقم پلاک‌شان با رقم تعیین شده همخوانی دارد، مجاز به تردد در محدوده طرح نیستند.
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-center text-sm">
                            <div class="bg-white p-2 rounded border">
                                <div class="font-semibold text-gray-800">شنبه</div>
                                <div class="text-red-600">۱ و ۲</div>
                            </div>
                            <div class="bg-white p-2 rounded border">
                                <div class="font-semibold text-gray-800">یکشنبه</div>
                                <div class="text-red-600">۳ و ۴</div>
                            </div>
                            <div class="bg-white p-2 rounded border">
                                <div class="font-semibold text-gray-800">دوشنبه</div>
                                <div class="text-red-600">۵ و ۶</div>
                            </div>
                            <div class="bg-white p-2 rounded border">
                                <div class="font-semibold text-gray-800">سه‌شنبه</div>
                                <div class="text-red-600">۷ و ۸</div>
                            </div>
                            <div class="bg-white p-2 rounded border">
                                <div class="font-semibold text-gray-800">چهارشنبه</div>
                                <div class="text-red-600">۹ و ۰</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inquiry Process Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    نحوه استعلام طرح ترافیک
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <p class="text-gray-700 leading-relaxed">
                    برای استعلام وضعیت طرح ترافیک خودرو، شما نیاز به ارائه اطلاعات دقیق خودرو دارید. روند استعلام به شرح زیر است:
                </p>

                <div class="space-y-4">
                    <div class="flex gap-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">۱</div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-blue-800 mb-1">ورود شماره پلاک</h4>
                            <p class="text-blue-700 text-sm">شماره پلاک خودرو را به صورت کامل وارد کنید. مثال: ۱۲ ج ۳۴۵ ایران ۱۶</p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">۲</div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-green-800 mb-1">ورود کد ملی مالک</h4>
                            <p class="text-green-700 text-sm">کد ملی ۱۰ رقمی مالک خودرو که در سند خودرو درج شده است</p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <div class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-bold">۳</div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-purple-800 mb-1">تایید شماره موبایل</h4>
                            <p class="text-purple-700 text-sm">شماره موبایل برای دریافت کد تایید و اطلاع‌رسانی</p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-4 bg-orange-50 rounded-lg border border-orange-200">
                        <div class="flex-shrink-0 w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center text-sm font-bold">۴</div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-orange-800 mb-1">دریافت نتایج</h4>
                            <p class="text-orange-700 text-sm">اطلاعات کامل وضعیت طرح ترافیک خودرو شما نمایش داده می‌شود</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="font-semibold text-gray-800 mb-2">نکات مهم استعلام:</h4>
                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                        <li>اطلاعات وارد شده باید دقیقاً مطابق با مدارک خودرو باشد</li>
                        <li>کد ملی وارد شده باید متعلق به مالک اصلی خودرو باشد</li>
                        <li>در صورت عدم تطابق اطلاعات، امکان استعلام وجود ندارد</li>
                        <li>نتایج استعلام بر اساس آخرین اطلاعات سامانه راهور است</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Exemptions Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    معافیت‌ها و استثناهای طرح ترافیک
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <p class="text-gray-700 leading-relaxed">
                    برخی خودروها و افراد از محدودیت‌های طرح ترافیک معاف هستند. در ادامه فهرست کاملی از این معافیت‌ها ارائه می‌دهیم:
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Vehicle-based Exemptions -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b-2 border-blue-200 pb-2">معافیت‌های خودرویی</h3>
                        
                        <div class="space-y-3">
                            <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                                <h4 class="font-medium text-green-800 mb-1">خودروهای هیبریدی</h4>
                                <p class="text-green-700 text-sm">کلیه خودروهای هیبریدی دارای پلاک سبز</p>
                            </div>
                            
                            <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                <h4 class="font-medium text-blue-800 mb-1">خودروهای برقی</h4>
                                <p class="text-blue-700 text-sm">تمام خودروهای برقی و پلاگین هیبرید</p>
                            </div>
                            
                            <div class="bg-purple-50 p-3 rounded-lg border border-purple-200">
                                <h4 class="font-medium text-purple-800 mb-1">خودروهای دوگانه‌سوز</h4>
                                <p class="text-purple-700 text-sm">خودروهای دارای سیستم دوگانه‌سوز CNG</p>
                            </div>
                            
                            <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                                <h4 class="font-medium text-yellow-800 mb-1">موتورسیکلت‌ها</h4>
                                <p class="text-yellow-700 text-sm">کلیه موتورسیکلت‌ها از طرح ترافیک معاف هستند</p>
                            </div>
                        </div>
                    </div>

                    <!-- Person-based Exemptions -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b-2 border-orange-200 pb-2">معافیت‌های شخصی</h3>
                        
                        <div class="space-y-3">
                            <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                <h4 class="font-medium text-red-800 mb-1">خودروهای اورژانسی</h4>
                                <p class="text-red-700 text-sm">آمبولانس، آتش‌نشانی، پلیس و نیروهای نظامی</p>
                            </div>
                            
                            <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-200">
                                <h4 class="font-medium text-indigo-800 mb-1">معلولان و جانبازان</h4>
                                <p class="text-indigo-700 text-sm">افراد دارای کارت معلولیت بالای ۲۵ درصد</p>
                            </div>
                            
                            <div class="bg-pink-50 p-3 rounded-lg border border-pink-200">
                                <h4 class="font-medium text-pink-800 mb-1">کارکنان خدمات عمومی</h4>
                                <p class="text-pink-700 text-sm">پزشکان، پرستاران و کارکنان بیمارستان‌ها</p>
                            </div>
                            
                            <div class="bg-teal-50 p-3 rounded-lg border border-teal-200">
                                <h4 class="font-medium text-teal-800 mb-1">خبرنگاران</h4>
                                <p class="text-teal-700 text-sm">خبرنگاران دارای مجوز معتبر</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                    <h4 class="font-semibold text-amber-800 mb-2">نحوه دریافت معافیت:</h4>
                    <ul class="list-decimal list-inside space-y-1 text-sm text-amber-700">
                        <li>مراجعه به دفاتر پلیس راهور تهران</li>
                        <li>ارائه مدارک مربوطه (کارت معلولیت، کارت پزشکی، مجوز کار و...)</li>
                        <li>تکمیل فرم درخواست معافیت</li>
                        <li>پرداخت هزینه صدور کارت معافیت</li>
                        <li>نصب برچسب معافیت روی شیشه خودرو</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Penalties Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.083 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    جرائم و مجازات‌های طرح ترافیک
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <p class="text-gray-700 leading-relaxed">
                    تخلف از طرح ترافیک به صورت خودکار توسط دوربین‌های شهری ثبت شده و جریمه متناسب با آن صادر می‌شود. در ادامه انواع تخلفات و مجازات‌های مربوطه آورده شده است:
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Primary Violations -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-red-900 bg-red-100 px-3 py-2 rounded">تخلفات اصلی</h3>
                        
                        <div class="space-y-3">
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-medium text-red-800">ورود به محدوده طرح</h4>
                                    <span class="text-red-600 font-bold text-sm bg-red-100 px-2 py-1 rounded">۵۰۰,۰۰۰ تومان</span>
                                </div>
                                <p class="text-red-700 text-sm">تردد خودرو در روز ممنوعیت پلاک در محدوده طرح ترافیک</p>
                            </div>
                            
                            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-medium text-orange-800">تخلف مکرر</h4>
                                    <span class="text-orange-600 font-bold text-sm bg-orange-100 px-2 py-1 rounded">۱,۰۰۰,۰۰۰ تومان</span>
                                </div>
                                <p class="text-orange-700 text-sm">تکرار تخلف در مدت ۳۰ روز (دو برابر جریمه اول)</p>
                            </div>
                            
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-medium text-yellow-800">استفاده غیرمجاز از معافیت</h4>
                                    <span class="text-yellow-600 font-bold text-sm bg-yellow-100 px-2 py-1 rounded">۱,۵۰۰,۰۰۰ تومان</span>
                                </div>
                                <p class="text-yellow-700 text-sm">استفاده از کارت یا برچسب معافیت جعلی یا منقضی</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-blue-900 bg-blue-100 px-3 py-2 rounded">اطلاعات مهم</h3>
                        
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="font-medium text-blue-800 mb-2">مهلت پرداخت جریمه</h4>
                            <ul class="text-blue-700 text-sm space-y-1">
                                <li>• ۳۰ روز پس از ثبت تخلف</li>
                                <li>• امکان پرداخت اقساطی در موارد خاص</li>
                                <li>• تخفیف ۵۰٪ در صورت پرداخت ظرف ۱۵ روز</li>
                            </ul>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h4 class="font-medium text-green-800 mb-2">راه‌های پرداخت</h4>
                            <ul class="text-green-700 text-sm space-y-1">
                                <li>• درگاه‌های پرداخت اینترنتی</li>
                                <li>• ATM بانک‌های معتبر</li>
                                <li>• دفاتر پست</li>
                                <li>• دفاتر پلیس راهور</li>
                            </ul>
                        </div>
                        
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <h4 class="font-medium text-purple-800 mb-2">عواقب عدم پرداخت</h4>
                            <ul class="text-purple-700 text-sm space-y-1">
                                <li>• توقیف خودرو تا پرداخت جریمه</li>
                                <li>• منع تردد در محدوده طرح</li>
                                <li>• امکان عدم تمدید پلاک</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-100 p-4 rounded-lg border-r-4 border-gray-500">
                    <h4 class="font-semibold text-gray-800 mb-2">راه‌های اعتراض به جریمه:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
                        <div>
                            <strong>مراجعه حضوری:</strong>
                            <p>دفاتر پلیس راهور تهران</p>
                        </div>
                        <div>
                            <strong>ارسال مدارک:</strong>
                            <p>پست الکترونیک یا پست معمولی</p>
                        </div>
                        <div>
                            <strong>سامانه اینترنتی:</strong>
                            <p>درگاه اعتراض آنلاین راهور</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technology and Systems Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.781 0-2.674-2.153-1.414-3.414l5-5A2 2 0 009 8.172V5L8 4z"></path>
                    </svg>
                    فناوری و سامانه‌های طرح ترافیک
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <p class="text-gray-700 leading-relaxed">
                    طرح ترافیک تهران از پیشرفته‌ترین فناوری‌های نظارتی و کنترلی برای اجرای دقیق و عادلانه استفاده می‌کند. این سامانه‌ها قادر به شناسایی خودکار و ثبت تخلفات به صورت ۲۴ ساعته هستند.
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Monitoring Systems -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-indigo-900 bg-indigo-100 px-3 py-2 rounded">سامانه‌های نظارتی</h3>
                        
                        <div class="space-y-3">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <h4 class="font-medium text-blue-800 mb-2">دوربین‌های ANPR</h4>
                                <p class="text-blue-700 text-sm mb-2">سیستم تشخیص خودکار شماره پلاک</p>
                                <ul class="text-blue-600 text-xs space-y-1">
                                    <li>• دقت بالای ۹۸٪ در شناسایی پلاک</li>
                                    <li>• عملکرد ۲۴ ساعته در تمام شرایط جوی</li>
                                    <li>• پردازش آنلاین و آفلاین</li>
                                </ul>
                            </div>
                            
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <h4 class="font-medium text-green-800 mb-2">سنسورهای مغناطیسی</h4>
                                <p class="text-green-700 text-sm mb-2">تشخیص حضور خودرو در محدوده</p>
                                <ul class="text-green-600 text-xs space-y-1">
                                    <li>• نصب زیر آسفالت معابر اصلی</li>
                                    <li>• تشخیص نوع و سرعت خودرو</li>
                                    <li>• ارتباط با مرکز کنترل ترافیک</li>
                                </ul>
                            </div>
                            
                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                <h4 class="font-medium text-purple-800 mb-2">سامانه GPS</h4>
                                <p class="text-purple-700 text-sm mb-2">ردیابی مسیر و مکان خودروها</p>
                                <ul class="text-purple-600 text-xs space-y-1">
                                    <li>• اختیاری برای خودروهای مجاز</li>
                                    <li>• کنترل دقیق زمان ورود و خروج</li>
                                    <li>• گزارش‌گیری تحلیلی</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Data Processing -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-purple-900 bg-purple-100 px-3 py-2 rounded">پردازش اطلاعات</h3>
                        
                        <div class="space-y-3">
                            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                                <h4 class="font-medium text-orange-800 mb-2">مرکز داده مرکزی</h4>
                                <p class="text-orange-700 text-sm mb-2">پردازش روزانه میلیون‌ها رکورد</p>
                                <ul class="text-orange-600 text-xs space-y-1">
                                    <li>• ظرفیت بالا و قابلیت اطمینان</li>
                                    <li>• پردازش بی‌درنگ اطلاعات</li>
                                    <li>• پشتیبان‌گیری چندگانه</li>
                                </ul>
                            </div>
                            
                            <div class="bg-teal-50 p-4 rounded-lg border border-teal-200">
                                <h4 class="font-medium text-teal-800 mb-2">هوش مصنوعی</h4>
                                <p class="text-teal-700 text-sm mb-2">تحلیل الگوهای ترافیکی</p>
                                <ul class="text-teal-600 text-xs space-y-1">
                                    <li>• پیش‌بینی حجم ترافیک</li>
                                    <li>• شناسایی تخلفات مشکوک</li>
                                    <li>• بهینه‌سازی زمان‌بندی</li>
                                </ul>
                            </div>
                            
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                <h4 class="font-medium text-red-800 mb-2">سامانه امنیتی</h4>
                                <p class="text-red-700 text-sm mb-2">حفاظت از اطلاعات شخصی</p>
                                <ul class="text-red-600 text-xs space-y-1">
                                    <li>• رمزنگاری پیشرفته</li>
                                    <li>• کنترل دسترسی چندلایه</li>
                                    <li>• مطابقت با استانداردهای بین‌المللی</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-5 rounded-lg border">
                    <h4 class="font-semibold text-gray-800 mb-3">آمار عملکرد سامانه در سال ۱۴۰۲:</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="bg-white p-3 rounded shadow-sm">
                            <div class="text-2xl font-bold text-blue-600">۴۵M</div>
                            <div class="text-sm text-gray-600">خودروی ثبت شده</div>
                        </div>
                        <div class="bg-white p-3 rounded shadow-sm">
                            <div class="text-2xl font-bold text-green-600">۹۸.۵٪</div>
                            <div class="text-sm text-gray-600">دقت شناسایی</div>
                        </div>
                        <div class="bg-white p-3 rounded shadow-sm">
                            <div class="text-2xl font-bold text-purple-600">۲.۳M</div>
                            <div class="text-sm text-gray-600">تخلف ثبت شده</div>
                        </div>
                        <div class="bg-white p-3 rounded shadow-sm">
                            <div class="text-2xl font-bold text-orange-600">۲۴/۷</div>
                            <div class="text-sm text-gray-600">عملکرد سامانه</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Benefits and Impact Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"></path>
                    </svg>
                    فواید و تأثیرات طرح ترافیک
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <p class="text-gray-700 leading-relaxed">
                    طرح ترافیک تهران در طول چندین دهه اجرا، تأثیرات مثبت قابل توجهی بر زندگی شهروندان و کیفیت محیط زیست داشته است. این طرح موفق به کاهش چشمگیر آلودگی هوا و بهبود شرایط ترافیکی شده است.
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Environmental Benefits -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-emerald-900 bg-emerald-100 px-3 py-2 rounded flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            فواید زیست محیطی
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <h4 class="font-medium text-green-800 mb-2">کاهش آلودگی هوا</h4>
                                <div class="space-y-2 text-green-700 text-sm">
                                    <div class="flex justify-between">
                                        <span>کاهش CO₂:</span>
                                        <span class="font-semibold">۳۵٪</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>کاهش NOx:</span>
                                        <span class="font-semibold">۴۲٪</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>کاهش PM2.5:</span>
                                        <span class="font-semibold">۲۸٪</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <h4 class="font-medium text-blue-800 mb-2">بهبود کیفیت هوا</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• کاهش روزهای آلوده از ۲۸۰ به ۱۸۵ روز در سال</li>
                                    <li>• بهبود ۴۰٪ کیفیت هوا در مرکز شهر</li>
                                    <li>• کاهش بیماری‌های تنفسی در کودکان</li>
                                </ul>
                            </div>
                            
                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                <h4 class="font-medium text-purple-800 mb-2">حفاظت از منابع طبیعی</h4>
                                <ul class="text-purple-700 text-sm space-y-1">
                                    <li>• صرفه‌جویی ۱۲ میلیون لیتر سوخت در سال</li>
                                    <li>• کاهش مصرف منابع فسیلی</li>
                                    <li>• افزایش استفاده از حمل و نقل پاک</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Social and Economic Benefits -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-teal-900 bg-teal-100 px-3 py-2 rounded flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            فواید اجتماعی و اقتصادی
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                                <h4 class="font-medium text-orange-800 mb-2">بهبود ترافیک</h4>
                                <div class="space-y-2 text-orange-700 text-sm">
                                    <div class="flex justify-between">
                                        <span>کاهش تراکم:</span>
                                        <span class="font-semibold">۴۵٪</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>افزایش سرعت:</span>
                                        <span class="font-semibold">۳۰٪</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>کاهش زمان سفر:</span>
                                        <span class="font-semibold">۲۵ دقیقه</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                                <h4 class="font-medium text-indigo-800 mb-2">توسعه حمل و نقل عمومی</h4>
                                <ul class="text-indigo-700 text-sm space-y-1">
                                    <li>• افزایش ۱۵۰٪ استفاده از مترو</li>
                                    <li>• توسعه ۸۰ کیلومتر خط اتوبوس BRT</li>
                                    <li>• ایجاد ۵۰۰ ایستگاه جدید</li>
                                </ul>
                            </div>
                            
                            <div class="bg-pink-50 p-4 rounded-lg border border-pink-200">
                                <h4 class="font-medium text-pink-800 mb-2">فواید اقتصادی</h4>
                                <ul class="text-pink-700 text-sm space-y-1">
                                    <li>• صرفه‌جویی ۲۰۰ میلیارد تومان سوخت سالانه</li>
                                    <li>• کاهش ۳۰٪ هزینه‌های درمانی</li>
                                    <li>• افزایش بهره‌وری کاری شهروندان</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-6 rounded-lg border border-emerald-200">
                    <h4 class="font-semibold text-emerald-800 mb-4 text-center">مقایسه وضعیت قبل و بعد از اجرای طرح ترافیک</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b-2 border-emerald-200">
                                    <th class="text-right py-2 font-semibold text-emerald-800">شاخص</th>
                                    <th class="text-center py-2 font-semibold text-red-600">قبل از طرح (۱۳۵۷)</th>
                                    <th class="text-center py-2 font-semibold text-green-600">بعد از طرح (۱۴۰۲)</th>
                                    <th class="text-center py-2 font-semibold text-blue-600">بهبود</th>
                                </tr>
                            </thead>
                            <tbody class="space-y-2">
                                <tr class="border-b border-emerald-100">
                                    <td class="py-2 text-gray-800">میانگین سرعت (کیلومتر/ساعت)</td>
                                    <td class="text-center py-2 text-red-700">۱۵</td>
                                    <td class="text-center py-2 text-green-700">۲۸</td>
                                    <td class="text-center py-2 text-blue-700">+۸۷٪</td>
                                </tr>
                                <tr class="border-b border-emerald-100">
                                    <td class="py-2 text-gray-800">زمان سفر (دقیقه/کیلومتر)</td>
                                    <td class="text-center py-2 text-red-700">۶.۵</td>
                                    <td class="text-center py-2 text-green-700">۳.۲</td>
                                    <td class="text-center py-2 text-blue-700">-۵۱٪</td>
                                </tr>
                                <tr class="border-b border-emerald-100">
                                    <td class="py-2 text-gray-800">مصرف سوخت (لیتر/۱۰۰کیلومتر)</td>
                                    <td class="text-center py-2 text-red-700">۱۴.۲</td>
                                    <td class="text-center py-2 text-green-700">۹.۸</td>
                                    <td class="text-center py-2 text-blue-700">-۳۱٪</td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-800">کیفیت هوا (AQI میانگین سالانه)</td>
                                    <td class="text-center py-2 text-red-700">۱۸۵</td>
                                    <td class="text-center py-2 text-green-700">۱۲۵</td>
                                    <td class="text-center py-2 text-blue-700">-۳۲٪</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Future Developments Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-violet-600 to-indigo-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    آینده و توسعه طرح ترافیک
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <p class="text-gray-700 leading-relaxed">
                    شهرداری تهران برنامه‌های جامعی برای توسعه و بهبود طرح ترافیک در سال‌های آینده دارد. این برنامه‌ها شامل استفاده از فناوری‌های نوین، گسترش محدوده‌های پوشش و بهبود خدمات شهری است.
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Technological Advancements -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-violet-900 bg-violet-100 px-3 py-2 rounded flex items-center gap-2">
                            <svg class="w-5 h-5 text-violet-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            پیشرفت‌های فناورانه
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <h4 class="font-medium text-blue-800 mb-2">هوش مصنوعی پیشرفته</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• تحلیل رفتاری ترافیک با یادگیری ماشین</li>
                                    <li>• پیش‌بینی و پیشگیری از ترافیک</li>
                                    <li>• بهینه‌سازی مسیرهای جایگزین</li>
                                    <li>• تشخیص خودکار حوادث و اعمال اورژانس</li>
                                </ul>
                            </div>
                            
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <h4 class="font-medium text-green-800 mb-2">IoT و سنسورهای هوشمند</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• سنسورهای کیفیت هوای بی‌درنگ</li>
                                    <li>• ردیابی جریان ترافیک با دقت بالا</li>
                                    <li>• اتصال به خودروهای خودران</li>
                                    <li>• یکپارچگی با زیرساخت‌های هوشمند شهری</li>
                                </ul>
                            </div>
                            
                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                <h4 class="font-medium text-purple-800 mb-2">بلاک‌چین و امنیت</h4>
                                <ul class="text-purple-700 text-sm space-y-1">
                                    <li>• ثبت غیرقابل تغییر تراکنش‌ها</li>
                                    <li>• حفاظت از داده‌های شخصی شهروندان</li>
                                    <li>• شفافیت کامل در پردازش جرائم</li>
                                    <li>• احراز هویت چندلایه</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Policy and Infrastructure -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-indigo-900 bg-indigo-100 px-3 py-2 rounded flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            سیاست‌گذاری و زیرساخت
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                                <h4 class="font-medium text-orange-800 mb-2">گسترش محدوده‌ها</h4>
                                <ul class="text-orange-700 text-sm space-y-1">
                                    <li>• اضافه شدن مناطق ۲ و ۳ به طرح</li>
                                    <li>• ایجاد مناطق کم آلایندگی (LEZ)</li>
                                    <li>• طرح ترافیک شبانه در نقاط خاص</li>
                                    <li>• هماهنگی با شهرهای اطراف</li>
                                </ul>
                            </div>
                            
                            <div class="bg-teal-50 p-4 rounded-lg border border-teal-200">
                                <h4 class="font-medium text-teal-800 mb-2">حمل و نقل پایدار</h4>
                                <ul class="text-teal-700 text-sm space-y-1">
                                    <li>• توسعه شبکه مترو به ۱۰ خط</li>
                                    <li>• افزایش ۵۰٪ ظرفیت اتوبوسرانی</li>
                                    <li>• ایجاد ۲۰۰ ایستگاه دوچرخه</li>
                                    <li>• تشویق استفاده از خودروهای برقی</li>
                                </ul>
                            </div>
                            
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                <h4 class="font-medium text-red-800 mb-2">مشارکت شهروندی</h4>
                                <ul class="text-red-700 text-sm space-y-1">
                                    <li>• اپلیکیشن همراه طرح ترافیک</li>
                                    <li>• سیستم پیشنهاد و انتقاد آنلاین</li>
                                    <li>• برنامه‌های تشویقی برای استفاده از حمل و نقل عمومی</li>
                                    <li>• آموزش و فرهنگ‌سازی ترافیک</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-violet-50 to-indigo-50 p-6 rounded-lg border border-violet-200">
                    <h4 class="font-semibold text-violet-800 mb-4 text-center">برنامه زمانبندی توسعه‌های آتی</h4>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4 p-3 bg-white rounded-lg shadow-sm">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">۱۴۰۳</div>
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-800">راه‌اندازی سامانه پیش‌بینی ترافیک</h5>
                                <p class="text-sm text-gray-600">استفاده از هوش مصنوعی برای پیش‌بینی وضعیت ترافیک</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 p-3 bg-white rounded-lg shadow-sm">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold">۱۴۰۴</div>
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-800">گسترش به مناطق جدید</h5>
                                <p class="text-sm text-gray-600">اضافه شدن بخش‌هایی از مناطق ۲، ۳ و ۴ به طرح</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 p-3 bg-white rounded-lg shadow-sm">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-bold">۱۴۰۵</div>
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-800">یکپارچگی کامل حمل و نقل</h5>
                                <p class="text-sm text-gray-600">اتصال تمام سیستم‌های حمل و نقل در یک پلتفرم واحد</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action Section -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-700 rounded-xl p-8 text-center text-white">
            <div class="max-w-2xl mx-auto space-y-4">
                <h2 class="text-2xl font-bold">همین الان وضعیت خودرو خود را بررسی کنید</h2>
                <p class="text-blue-100 leading-relaxed">
                    با استفاده از سرویس آنلاین استعلام طرح ترافیک، می‌توانید در کمترین زمان از وضعیت خودرو خود مطلع شوید و برای سفرهای آینده برنامه‌ریزی کنید.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center items-center pt-4">
                    <a href="#" class="inline-flex items-center gap-2 bg-white text-blue-600 px-6 py-3 rounded-lg font-medium hover:bg-blue-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        شروع استعلام
                    </a>
                    <a href="#" class="inline-flex items-center gap-2 text-white border border-white px-6 py-3 rounded-lg font-medium hover:bg-white hover:text-blue-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        سوالات متداول
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection