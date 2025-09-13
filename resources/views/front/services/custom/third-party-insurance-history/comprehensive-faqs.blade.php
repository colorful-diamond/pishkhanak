{{-- Comprehensive Searchable FAQ Section for Third-Party Insurance History Service --}}
{{-- سوالات متداول جامع و قابل جستجو برای خدمات استعلام سوابق بیمه شخص ثالث --}}

<!-- Enhanced FAQ Section with Advanced Search and Categories -->
<section class="mt-12 mb-12" id="comprehensive-faqs">
    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8 mb-8">
        <div class="max-w-5xl mx-auto text-center">
            <h2 class="text-4xl font-bold text-dark-sky-700 mb-6 flex items-center justify-center gap-3">
                <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                مرجع کامل سوالات متداول پیشرفته
            </h2>
            <p class="text-gray-700 text-xl leading-relaxed">
                بیش از <strong>۷۰ سوال و پاسخ تخصصی</strong> درباره استعلام سوابق بیمه شخص ثالث، سامانه سنهاب، و خدمات بیمه‌ای
            </p>
        </div>
    </div>

    <!-- Advanced FAQ Search and Filter System -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-8 shadow-sm">
        <div class="flex flex-col lg:flex-row gap-4 items-center">
            <!-- Advanced Search Input -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="advanced-faq-search" 
                    placeholder="جستجوی پیشرفته در سوالات (مثال: سنهاب، تخفیف، پلاک، بیمه‌نامه)..." 
                    class="w-full pl-3 pr-10 py-4 text-lg border-2 border-purple-200 rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-right"
                >
                <div id="search-suggestions" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-lg border border-gray-200 z-10"></div>
            </div>
        </div>

        <!-- Advanced Category Filter Buttons -->
        <div class="flex flex-wrap gap-2 mt-4">
            <button class="faq-category-btn active px-4 py-2 rounded-lg bg-purple-600 text-white text-sm font-medium transition-colors" data-category="all">
                همه موضوعات (۷۰)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="general">
                عمومی (۱۰)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="sanhab">
                سامانه سنهاب (۹)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="inquiry">
                فرآیند استعلام (۸)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="companies">
                شرکت‌های بیمه (۷)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="discounts">
                تخفیفات و نرخ‌ها (۶)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="legal">
                قوانین و مقررات (۶)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="coverage">
                پوشش بیمه (۵)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="technical">
                مسائل فنی (۵)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="claims">
                خسارات (۴)
            </button>
        </div>

        <!-- Advanced Search Results Counter -->
        <div id="faq-results" class="mt-4 text-sm text-gray-600 hidden">
            <span id="results-count">0</span> نتیجه یافت شد
        </div>
    </div>

    <!-- Advanced FAQ Categories Container -->
    <div id="faq-container" class="space-y-8">

        <!-- Category 1: عمومی (General) - 10 FAQs -->
        <div class="faq-category" data-category="general">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    سوالات عمومی بیمه شخص ثالث
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <!-- FAQ 1 -->
                <div class="faq-item p-6" data-category="general" data-tags="بیمه شخص ثالث چیست تعریف">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                            بیمه شخص ثالث چیست و چرا اجباری است؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            <strong>بیمه شخص ثالث</strong> نوعی بیمه اجباری است که در آن شرکت بیمه متعهد می‌شود در صورت بروز حادثه، خسارات وارد شده به شخص ثالث (طرف آسیب‌دیده) را جبران کند. این بیمه طبق قانون بیمه اجباری خسارات وارد شده به شخص ثالث مصوب ۱۳۴۶ برای تمام وسایل نقلیه موتوری الزامی است.
                        </p>
                        <div class="bg-amber-50 border-r-4 border-amber-500 p-4 rounded">
                            <h4 class="font-semibold text-amber-800 mb-2">دلایل اجباری بودن:</h4>
                            <ul class="text-amber-700 text-sm space-y-1">
                                <li>• حمایت از حقوق طرف آسیب‌دیده</li>
                                <li>• تضمین جبران خسارات</li>
                                <li>• کاهش مشکلات مالی ناشی از حوادث</li>
                                <li>• ایجاد نظم در ترافیک</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="faq-item p-6" data-category="general" data-tags="بیمه شخص ثالث مجازات جریمه">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                            در صورت نداشتن بیمه شخص ثالث چه مجازاتی در انتظار راننده است؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            رانندگی بدون بیمه شخص ثالث معتبر طبق قانون جرم محسوب شده و مجازات‌های سنگینی دارد:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-red-800 mb-2">مجازات‌های مالی:</h4>
                                <ul class="text-red-700 text-sm space-y-1">
                                    <li>• جریمه نقدی ۲ تا ۱۰ میلیون تومان</li>
                                    <li>• توقیف خودرو</li>
                                    <li>• هزینه پارکینگ توقیفی</li>
                                </ul>
                            </div>
                            <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-orange-800 mb-2">مجازات‌های کیفری:</h4>
                                <ul class="text-orange-700 text-sm space-y-1">
                                    <li>• عدم تحویل گواهینامه</li>
                                    <li>• منع رانندگی موقت</li>
                                    <li>• احتمال پرونده کیفری</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="faq-item p-6" data-category="general" data-tags="بیمه شخص ثالث پوشش حدود">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                            بیمه شخص ثالث چه خسارات و آسیب‌هایی را پوشش می‌دهد؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">بیمه شخص ثالث سه نوع خسارت اصلی را پوشش می‌دهد:</p>
                        <div class="space-y-4">
                            <div class="bg-green-50 border-r-4 border-green-500 p-4 rounded">
                                <h4 class="font-semibold text-green-800 mb-2">۱. خسارات جانی:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• هزینه‌های درمان و بستری</li>
                                    <li>• دیه در صورت فوت</li>
                                    <li>• ارش در صورت عضو نقص</li>
                                    <li>• هزینه‌های توانبخشی</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 border-r-4 border-blue-500 p-4 rounded">
                                <h4 class="font-semibold text-blue-800 mb-2">۲. خسارات مالی:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• آسیب به اموال طرف ثالث</li>
                                    <li>• تعمیر خودرو آسیب‌دیده</li>
                                    <li>• خسارت به اموال عمومی</li>
                                </ul>
                            </div>
                            <div class="bg-purple-50 border-r-4 border-purple-500 p-4 rounded">
                                <h4 class="font-semibold text-purple-800 mb-2">۳. هزینه‌های قانونی:</h4>
                                <ul class="text-purple-700 text-sm space-y-1">
                                    <li>• وکیل دادرسی</li>
                                    <li>• هزینه‌های دادگاه</li>
                                    <li>• کارشناسی رسمی</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="faq-item p-6" data-category="general" data-tags="تفاوت بیمه شخص ثالث بدنه">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                            تفاوت بیمه شخص ثالث با بیمه بدنه چیست؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full border border-gray-200 rounded-lg">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="text-right p-4 border border-gray-200 font-semibold">جنبه مقایسه</th>
                                        <th class="text-right p-4 border border-gray-200 font-semibold">بیمه شخص ثالث</th>
                                        <th class="text-right p-4 border border-gray-200 font-semibold">بیمه بدنه</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="p-4 border border-gray-200 font-medium">الزام قانونی</td>
                                        <td class="p-4 border border-gray-200 text-green-600">اجباری</td>
                                        <td class="p-4 border border-gray-200 text-orange-600">اختیاری</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="p-4 border border-gray-200 font-medium">پوشش</td>
                                        <td class="p-4 border border-gray-200">خسارات طرف مقابل</td>
                                        <td class="p-4 border border-gray-200">خسارات خودرو شما</td>
                                    </tr>
                                    <tr>
                                        <td class="p-4 border border-gray-200 font-medium">هزینه</td>
                                        <td class="p-4 border border-gray-200">کمتر</td>
                                        <td class="p-4 border border-gray-200">بیشتر</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="p-4 border border-gray-200 font-medium">استفاده</td>
                                        <td class="p-4 border border-gray-200">همه خودروها</td>
                                        <td class="p-4 border border-gray-200">خودروهای گران‌قیمت</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="faq-item p-6" data-category="general" data-tags="مدت اعتبار بیمه نامه تمدید">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                            مدت اعتبار بیمه‌نامه شخص ثالث چقدر است و چه زمانی باید تمدید شود؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            بیمه‌نامه شخص ثالث معمولاً برای مدت <strong>یک سال شمسی</strong> صادر می‌شود و در پایان این مدت باید تمدید شود.
                        </p>
                        <div class="bg-sky-50 border border-sky-200 p-4 rounded-lg">
                            <h4 class="font-semibold text-sky-800 mb-2">نکات مهم تمدید:</h4>
                            <ul class="text-sky-700 text-sm space-y-1">
                                <li>• <strong>بهترین زمان تمدید:</strong> ۳۰ روز قبل از انقضا</li>
                                <li>• <strong>حداکثر زمان مجاز:</strong> تا آخرین روز اعتبار</li>
                                <li>• <strong>انقطاع بیمه:</strong> منجر به از دست رفتن تخفیف می‌شود</li>
                                <li>• <strong>تمدید زودهنگام:</strong> امکان دریافت تخفیف‌های ویژه</li>
                            </ul>
                        </div>
                        <div class="mt-4 bg-amber-50 border border-amber-200 p-4 rounded-lg">
                            <p class="text-amber-800 text-sm">
                                <strong>توجه:</strong> در صورت انقضای بیمه‌نامه و عدم تمدید به موقع، علاوه بر مجازات‌های قانونی، تخفیف‌های تجمعی نیز از بین می‌رود.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="faq-item p-6" data-category="general" data-tags="انواع خودرو بیمه شخص ثالث">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                            چه انواع خودرو و وسایل نقلیه نیاز به بیمه شخص ثالث دارند؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            تمامی وسایل نقلیه موتوری که در معابر عمومی تردد می‌کنند، ملزم به داشتن بیمه شخص ثالث هستند:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">خودروهای سواری:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• خودروهای شخصی</li>
                                    <li>• خودروهای اجاره‌ای</li>
                                    <li>• تاکسی و اسنپ</li>
                                    <li>• خودروهای شرکتی</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">وسایل نقلیه تجاری:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• کامیون و تریلر</li>
                                    <li>• اتوبوس و مینی‌بوس</li>
                                    <li>• وانت و خاور</li>
                                    <li>• ماشین‌آلات راهسازی</li>
                                </ul>
                            </div>
                            <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-purple-800 mb-2">موتورسیکلت‌ها:</h4>
                                <ul class="text-purple-700 text-sm space-y-1">
                                    <li>• موتورسیکلت‌های شخصی</li>
                                    <li>• موتورهای پیک و ارسال</li>
                                    <li>• موتورهای سه‌چرخ</li>
                                </ul>
                            </div>
                            <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-orange-800 mb-2">وسایل ویژه:</h4>
                                <ul class="text-orange-700 text-sm space-y-1">
                                    <li>• تراکتور (در جاده)</li>
                                    <li>• آمبولانس</li>
                                    <li>• خودروهای آتش‌نشانی</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ 7 -->
                <div class="faq-item p-6" data-category="general" data-tags="حدود پوشش مبلغ بیمه">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                            حدود پوشش بیمه شخص ثالث در سال ۱۴۰۳ چقدر است؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            حدود پوشش بیمه شخص ثالث در سال ۱۴۰۳ به شرح زیر تعیین شده است:
                        </p>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                    <h4 class="font-semibold text-green-800 mb-2">پوشش خسارات جانی:</h4>
                                    <ul class="text-green-700 text-sm space-y-1">
                                        <li>• <strong>ماه‌های عادی:</strong> ۱.۶ میلیارد تومان</li>
                                        <li>• <strong>ماه‌های حرام:</strong> ۲.۱۳۳ میلیارد تومان</li>
                                        <li>• <strong>عضو نقص:</strong> بر اساس جدول ارش</li>
                                    </ul>
                                </div>
                                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                    <h4 class="font-semibold text-blue-800 mb-2">پوشش خسارات مالی:</h4>
                                    <ul class="text-blue-700 text-sm space-y-1">
                                        <li>• <strong>حداقل:</strong> ۵۳.۳ میلیون تومان</li>
                                        <li>• <strong>حداکثر:</strong> ۱.۰۶۶ میلیارد تومان</li>
                                        <li>• <strong>استاندارد:</strong> ۴۰۰ میلیون تومان</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-purple-800 mb-3">نکات مهم:</h4>
                                <ul class="text-purple-700 text-sm space-y-2">
                                    <li>• حدود پوشش هر ساله بازنگری می‌شود</li>
                                    <li>• امکان افزایش پوشش مالی با پرداخت حق بیمه بیشتر</li>
                                    <li>• در صورت تجاوز از حد پوشش، مابقی از جیب راننده</li>
                                    <li>• پوشش‌های ویژه برای خودروهای لوکس</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ 8 -->
                <div class="faq-item p-6" data-category="general" data-tags="کد یکتا بیمه نامه">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                            کد یکتای بیمه‌نامه چیست و چگونه دریافت می‌شود؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            <strong>کد یکتای بیمه‌نامه</strong> شناسه‌ای منحصر به فرد است که پس از صدور یا تمدید بیمه‌نامه شخص ثالث تولید می‌شود و جایگزین نسخه کاغذی بیمه‌نامه محسوب می‌شود.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-sky-50 border border-sky-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-sky-800 mb-2">نحوه دریافت:</h4>
                                <ul class="text-sky-700 text-sm space-y-1">
                                    <li>• پیامک خودکار پس از صدور</li>
                                    <li>• ایمیل از شرکت بیمه</li>
                                    <li>• دانلود از سایت شرکت بیمه</li>
                                    <li>• دریافت از نمایندگی</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">کاربردهای کد یکتا:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• ارائه در کنترل‌های پلیس</li>
                                    <li>• استعلام اصالت بیمه‌نامه</li>
                                    <li>• پیگیری خسارات</li>
                                    <li>• تمدید بیمه‌نامه</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 bg-amber-50 border border-amber-200 p-4 rounded-lg">
                            <p class="text-amber-800 text-sm">
                                <strong>مهم:</strong> طبق بخشنامه ۹۹/۱۰۰/۱۵۵۶۳ بیمه مرکزی و پلیس راهور، نیازی به حمل نسخه کاغذی بیمه‌نامه نیست و داشتن کد یکتا کافی است.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 9 -->
                <div class="faq-item p-6" data-category="general" data-tags="بیمه شخص ثالث موقت">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                            آیا امکان صدور بیمه شخص ثالث موقت وجود دارد؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            بله، در شرایط خاص امکان صدور <strong>بیمه شخص ثالث موقت</strong> وجود دارد که معمولاً برای مدت‌های کوتاه صادر می‌شود.
                        </p>
                        <div class="space-y-4">
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">موارد صدور بیمه موقت:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• انتقال مالکیت خودرو</li>
                                    <li>• خودروهای وارداتی جدید</li>
                                    <li>• خودروهای در حال تعمیر</li>
                                    <li>• خودروهای اجاره‌ای کوتاه مدت</li>
                                    <li>• خودروهای نمایشگاهی</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">مدت زمان و شرایط:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• <strong>حداقل مدت:</strong> ۷ روز</li>
                                    <li>• <strong>حداکثر مدت:</strong> ۹۰ روز</li>
                                    <li>• <strong>تمدید:</strong> امکان تبدیل به ساله</li>
                                    <li>• <strong>محاسبه حق بیمه:</strong> روزانه</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 bg-orange-50 border border-orange-200 p-4 rounded-lg">
                            <p class="text-orange-800 text-sm">
                                <strong>نکته:</strong> هزینه بیمه موقت نسبت به بیمه ساله گران‌تر است و معمولاً برای شرایط اضطراری استفاده می‌شود.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 10 -->
                <div class="faq-item p-6" data-category="general" data-tags="بیمه نامه اصالت تشخیص">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                            چگونه اصالت بیمه‌نامه شخص ثالث را تشخیص دهیم؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-blue-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            تشخیص اصالت بیمه‌نامه برای جلوگیری از کلاهبرداری بسیار مهم است. روش‌های زیر برای تشخیص اصالت استفاده می‌شود:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">روش‌های آنلاین:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• استعلام از سامانه سنهاب</li>
                                    <li>• استعلام از سایت شرکت بیمه</li>
                                    <li>• استعلام از پیشخوانک</li>
                                    <li>• ارسال پیامک به ۳۰۰۰۲۶۲۱</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">نشانه‌های بیمه اصل:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• کد یکتای معتبر</li>
                                    <li>• تطابق اطلاعات با سامانه</li>
                                    <li>• تاریخ‌های صحیح</li>
                                    <li>• شماره بیمه‌نامه قابل استعلام</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 bg-red-50 border border-red-200 p-4 rounded-lg">
                            <h4 class="font-semibold text-red-800 mb-2">علائم بیمه تقلبی:</h4>
                            <ul class="text-red-700 text-sm space-y-1">
                                <li>• عدم وجود در سامانه سنهاب</li>
                                <li>• قیمت غیرمعقول</li>
                                <li>• عدم صدور کد یکتا</li>
                                <li>• اطلاعات ناقص یا غلط</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 2: سامانه سنهاب (SANHAB System) - 9 FAQs -->
        <div class="faq-category" data-category="sanhab">
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                    سامانه سنهاب بیمه مرکزی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <!-- FAQ 1 -->
                <div class="faq-item p-6" data-category="sanhab" data-tags="سنهاب چیست تعریف">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">
                            سامانه سنهاب چیست و چه کاربردی دارد؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-green-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            <strong>سنهاب</strong> مخفف <em>«سامانه نظارت و هدایت الکترونیکی بیمه»</em> است که توسط بیمه مرکزی ایران راه‌اندازی شده و مرجع رسمی اطلاعات بیمه شخص ثالث در کشور محسوب می‌شود.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">ویژگی‌های اصلی:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• یکپارچه‌سازی اطلاعات ۲۵+ شرکت بیمه</li>
                                    <li>• به‌روزرسانی لحظه‌ای</li>
                                    <li>• دسترسی آنلاین ۲۴ ساعته</li>
                                    <li>• امنیت بالای اطلاعات</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">کاربردهای اصلی:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• استعلام اصالت بیمه‌نامه</li>
                                    <li>• بررسی تاریخچه بیمه‌ای</li>
                                    <li>• محاسبه تخفیفات</li>
                                    <li>• تشخیص انقطاع بیمه</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 bg-sky-50 border border-sky-200 p-4 rounded-lg">
                            <p class="text-sky-800 text-sm">
                                <strong>مزیت کلیدی:</strong> سنهاب تنها سامانه‌ای است که اطلاعات تمام شرکت‌های بیمه را در یک جا جمع‌آوری کرده و امکان استعلام یکپارچه را فراهم می‌آورد.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="faq-item p-6" data-category="sanhab" data-tags="سنهاب استعلام پلاک">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">
                            چگونه از طریق سنهاب با پلاک خودرو استعلام بگیریم؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-green-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            استعلام با پلاک خودرو ساده‌ترین روش دسترسی به اطلاعات بیمه است. مراحل زیر را دنبال کنید:
                        </p>
                        <div class="space-y-4">
                            <div class="bg-green-50 border-r-4 border-green-500 p-4 rounded">
                                <h4 class="font-semibold text-green-800 mb-2">مراحل استعلام:</h4>
                                <ol class="text-green-700 text-sm space-y-2">
                                    <li>۱. وارد سایت پیشخوانک شوید</li>
                                    <li>۲. شماره پلاک خودرو را وارد کنید</li>
                                    <li>۳. کد ملی مالک را وارد کنید</li>
                                    <li>۴. کد تصویری را تایپ کنید</li>
                                    <li>۵. دکمه استعلام را فشار دهید</li>
                                </ol>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">اطلاعات قابل مشاهده:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• شرکت بیمه‌گر فعلی</li>
                                    <li>• تاریخ شروع و پایان بیمه</li>
                                    <li>• درصد تخفیف عدم خسارت</li>
                                    <li>• وضعیت اعتبار بیمه‌نامه</li>
                                    <li>• شماره بیمه‌نامه</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 bg-amber-50 border border-amber-200 p-4 rounded-lg">
                            <p class="text-amber-800 text-sm">
                                <strong>نکته مهم:</strong> برای استعلام با پلاک، حتماً کد ملی مالک اصلی خودرو (طبق سند) لازم است.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="faq-item p-6" data-category="sanhab" data-tags="سنهاب کد ملی استعلام">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">
                            آیا برای استعلام از سنهاب حتماً کد ملی مالک لازم است؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-green-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            بله، کد ملی مالک خودرو یکی از الزامات اصلی برای استعلام از سامانه سنهاب است و بدون آن امکان دسترسی به اطلاعات وجود ندارد.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-red-800 mb-2">دلایل لزوم کد ملی:</h4>
                                <ul class="text-red-700 text-sm space-y-1">
                                    <li>• حفاظت از حریم خصوصی</li>
                                    <li>• جلوگیری از سوء استفاده</li>
                                    <li>• احراز هویت مالک</li>
                                    <li>• امنیت اطلاعات شخصی</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">روش‌های جایگزین:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• استعلام با کد یکتای بیمه‌نامه</li>
                                    <li>• استعلام با شماره بیمه‌نامه</li>
                                    <li>• مراجعه به نمایندگی</li>
                                    <li>• تماس با شرکت بیمه</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 bg-amber-50 border border-amber-200 p-4 rounded-lg">
                            <p class="text-amber-800 text-sm">
                                <strong>نکته:</strong> در صورت عدم دسترسی به کد ملی مالک، می‌توانید از طریق کد یکتای بیمه‌نامه یا شماره بیمه‌نامه نیز استعلام بگیرید.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="faq-item p-6" data-category="sanhab" data-tags="سنهاب خطا مشکل دسترسی">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">
                            چرا گاهی سامانه سنهاب خطا می‌دهد یا در دسترس نیست؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-green-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            سامانه سنهاب ممکن است به دلایل مختلفی موقتاً در دسترس نباشد یا خطا دهد:
                        </p>
                        <div class="space-y-4">
                            <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-orange-800 mb-2">دلایل تکنیکی:</h4>
                                <ul class="text-orange-700 text-sm space-y-1">
                                    <li>• نگهداری و به‌روزرسانی سیستم</li>
                                    <li>• ترافیک بالای کاربران</li>
                                    <li>• مشکلات شبکه یا سرور</li>
                                    <li>• تعمیرات برنامه‌ریزی شده</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">راه‌حل‌های پیشنهادی:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• صبر کرده و مجدداً تلاش کنید</li>
                                    <li>• از مرورگر دیگری استفاده کنید</li>
                                    <li>• کش مرورگر را پاک کنید</li>
                                    <li>• از ساعات کم ترافیک استفاده کنید</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 bg-green-50 border border-green-200 p-4 rounded-lg">
                            <p class="text-green-800 text-sm">
                                <strong>بهترین زمان استعلام:</strong> صبح‌های زود (۶ تا ۹) و شب‌های دیر (۲۲ تا ۲۴) کمترین ترافیک را دارند.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="faq-item p-6" data-category="sanhab" data-tags="سنهاب تخفیف محاسبه عدم خسارت">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">
                            چگونه درصد تخفیف عدم خسارت در سنهاب محاسبه می‌شود؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-green-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            درصد تخفیف عدم خسارت بر اساس سال‌های بدون خسارت محاسبه می‌شود و در سامانه سنهاب ثبت و نمایش داده می‌شود:
                        </p>
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 rounded-lg border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-4">جدول تخفیفات عدم خسارت:</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border border-gray-300 rounded-lg">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="p-3 text-right border border-gray-300">سال بدون خسارت</th>
                                            <th class="p-3 text-right border border-gray-300">درصد تخفیف</th>
                                            <th class="p-3 text-right border border-gray-300">وضعیت</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td class="p-2 border border-gray-300">سال اول</td><td class="p-2 border border-gray-300 text-red-600">۰٪</td><td class="p-2 border border-gray-300">بدون تخفیف</td></tr>
                                        <tr class="bg-gray-50"><td class="p-2 border border-gray-300">سال دوم</td><td class="p-2 border border-gray-300 text-orange-600">۱۰٪</td><td class="p-2 border border-gray-300">تخفیف پایه</td></tr>
                                        <tr><td class="p-2 border border-gray-300">سال سوم</td><td class="p-2 border border-gray-300 text-yellow-600">۲۰٪</td><td class="p-2 border border-gray-300">تخفیف متوسط</td></tr>
                                        <tr class="bg-gray-50"><td class="p-2 border border-gray-300">سال چهارم</td><td class="p-2 border border-gray-300 text-green-600">۳۰٪</td><td class="p-2 border border-gray-300">تخفیف خوب</td></tr>
                                        <tr><td class="p-2 border border-gray-300">سال پنجم و بالاتر</td><td class="p-2 border border-gray-300 text-blue-600">۴۰٪</td><td class="p-2 border border-gray-300">حداکثر تخفیف</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-4 bg-red-50 border border-red-200 p-4 rounded-lg">
                            <h4 class="font-semibold text-red-800 mb-2">نکات مهم:</h4>
                            <ul class="text-red-700 text-sm space-y-1">
                                <li>• هر خسارت، تخفیف را به صفر برمی‌گرداند</li>
                                <li>• انقطاع بیمه، تخفیف را از بین می‌برد</li>
                                <li>• تخفیف قابل انتقال به شرکت دیگر است</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="faq-item p-6" data-category="sanhab" data-tags="سنهاب موبایل اپلیکیشن">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">
                            آیا اپلیکیشن موبایل سنهاب وجود دارد؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-green-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            در حال حاضر اپلیکیشن اختصاصی سنهاب وجود ندارد، اما راه‌های مختلفی برای دسترسی موبایلی به سامانه فراهم است:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">روش‌های موبایلی:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• سایت موبایل پیشخوانک</li>
                                    <li>• مرورگر موبایل</li>
                                    <li>• اپلیکیشن‌های شرکت‌های بیمه</li>
                                    <li>• پیامک به کد ۳۰۰۰۲۶۲۱</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">مزایای دسترسی موبایل:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• استعلام در هر زمان و مکان</li>
                                    <li>• رابط کاربری سادگی</li>
                                    <li>• سرعت دسترسی بالا</li>
                                    <li>• صرفه‌جویی در زمان</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 bg-purple-50 border border-purple-200 p-4 rounded-lg">
                            <p class="text-purple-800 text-sm">
                                <strong>راهنمایی:</strong> برای بهترین تجربه موبایل، از سایت پیشخوانک که کاملاً ریسپانسیو طراحی شده استفاده کنید.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 7 -->
                <div class="faq-item p-6" data-category="sanhab" data-tags="سنهاب تاریخچه بیمه سوابق">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">
                            چگونه می‌توان تاریخچه کامل بیمه‌ای خودرو را از سنهاب مشاهده کرد؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-green-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            سامانه سنهاب امکان مشاهده تاریخچه کامل بیمه‌ای خودرو را فراهم می‌آورد که شامل اطلاعات جامعی است:
                        </p>
                        <div class="space-y-4">
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">اطلاعات قابل مشاهده:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• لیست تمام شرکت‌های بیمه‌گر قبلی</li>
                                    <li>• تاریخ شروع و پایان هر بیمه‌نامه</li>
                                    <li>• تعداد و تاریخ خسارات</li>
                                    <li>• مبلغ خسارات پرداختی</li>
                                    <li>• درصد تخفیف در هر دوره</li>
                                    <li>• دوره‌های انقطاع بیمه</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">نحوه دسترسی:</h4>
                                <ol class="text-blue-700 text-sm space-y-2">
                                    <li>۱. وارد سایت پیشخوانک شوید</li>
                                    <li>۲. گزینه «تاریخچه بیمه» را انتخاب کنید</li>
                                    <li>۳. پلاک و کد ملی را وارد کنید</li>
                                    <li>۴. گزارش کامل را مشاهده کنید</li>
                                </ol>
                            </div>
                        </div>
                        <div class="mt-4 bg-amber-50 border border-amber-200 p-4 rounded-lg">
                            <p class="text-amber-800 text-sm">
                                <strong>کاربرد:</strong> این اطلاعات برای خرید خودروی دست دوم، محاسبه تخفیف و ارزیابی ریسک بسیار مفید است.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 8 -->
                <div class="faq-item p-6" data-category="sanhab" data-tags="سنهاب خسارت ثبت پیگیری">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">
                            آیا خسارات ثبت شده در سنهاب قابل اعتراض یا تغییر است؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-green-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            خسارات ثبت شده در سنهاب قابل بازنگری و اعتراض است، اما فرآیند خاصی دارد:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-orange-800 mb-2">موارد قابل اعتراض:</h4>
                                <ul class="text-orange-700 text-sm space-y-1">
                                    <li>• خسارت اشتباه ثبت شده</li>
                                    <li>• خسارت تصادف بدون مقصر</li>
                                    <li>• خسارت منسوخ شده</li>
                                    <li>• اطلاعات نادرست</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">فرآیند اعتراض:</h4>
                                <ol class="text-blue-700 text-sm space-y-1">
                                    <li>۱. تهیه مدارک لازم</li>
                                    <li>۲. مراجعه به شرکت بیمه</li>
                                    <li>۳. تکمیل فرم اعتراض</li>
                                    <li>۴. پیگیری تا رفع اشکال</li>
                                </ol>
                            </div>
                        </div>
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg mt-4">
                            <h4 class="font-semibold text-green-800 mb-2">مدارک مورد نیاز:</h4>
                            <ul class="text-green-700 text-sm space-y-1">
                                <li>• کپی گزارش پلیس</li>
                                <li>• تصاویر محل حادثه</li>
                                <li>• نامه شرکت بیمه</li>
                                <li>• رسید پرداخت خسارت</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FAQ 9 -->
                <div class="faq-item p-6" data-category="sanhab" data-tags="سنهاب امنیت اطلاعات">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">
                            امنیت اطلاعات در سامانه سنهاب چگونه تضمین می‌شود؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-green-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            سامانه سنهاب با استفاده از جدیدترین تکنولوژی‌های امنیتی، حفاظت کاملی از اطلاعات کاربران فراهم می‌آورد:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">تکنولوژی‌های امنیتی:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• رمزنگاری SSL ۲۵۶ بیت</li>
                                    <li>• احراز هویت دو مرحله‌ای</li>
                                    <li>• فایروال پیشرفته</li>
                                    <li>• سیستم تشخیص نفوذ</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">حفاظت از حریم خصوصی:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• عدم ذخیره اطلاعات شخصی</li>
                                    <li>• دسترسی محدود به کارشناسان</li>
                                    <li>• لاگ تمام فعالیت‌ها</li>
                                    <li>• حذف خودکار جلسات</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 bg-purple-50 border border-purple-200 p-4 rounded-lg">
                            <h4 class="font-semibold text-purple-800 mb-2">تعهدات قانونی:</h4>
                            <ul class="text-purple-700 text-sm space-y-1">
                                <li>• پیروی از قوانین حفاظت از داده‌ها</li>
                                <li>• نظارت بیمه مرکزی ایران</li>
                                <li>• ممیزی امنیتی دوره‌ای</li>
                                <li>• گزارش‌دهی شفاف</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 3: فرآیند استعلام (Inquiry Process) - 8 FAQs -->
        <div class="faq-category" data-category="inquiry">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    فرآیند استعلام و پیگیری
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <!-- FAQ 1 -->
                <div class="faq-item p-6" data-category="inquiry" data-tags="استعلام روش‌های مختلف">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                            چند روش برای استعلام بیمه شخص ثالث وجود دارد؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-purple-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            برای استعلام بیمه شخص ثالث روش‌های متنوعی وجود دارد که بر اساس نیاز و شرایط انتخاب می‌شود:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">روش‌های آنلاین:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• سایت پیشخوانک</li>
                                    <li>• سامانه سنهاب</li>
                                    <li>• سایت شرکت‌های بیمه</li>
                                    <li>• اپلیکیشن موبایل</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">روش‌های غیر آنلاین:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• پیامک به ۳۰۰۰۲۶۲۱</li>
                                    <li>• تماس با شرکت بیمه</li>
                                    <li>• مراجعه به نمایندگی</li>
                                    <li>• مراکز خدمات فنی</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 bg-purple-50 border border-purple-200 p-4 rounded-lg">
                            <h4 class="font-semibold text-purple-800 mb-2">مقایسه سرعت:</h4>
                            <ul class="text-purple-700 text-sm space-y-1">
                                <li>• آنلاین: فوری (کمتر از ۳۰ ثانیه)</li>
                                <li>• پیامک: ۱-۳ دقیقه</li>
                                <li>• تماس تلفنی: ۵-۱۰ دقیقه</li>
                                <li>• مراجعه حضوری: ۳۰+ دقیقه</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="faq-item p-6" data-category="inquiry" data-tags="استعلام پیامک کد">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                            نحوه استعلام بیمه از طریق پیامک چگونه است؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-purple-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">
                            استعلام از طریق پیامک یکی از ساده‌ترین و سریع‌ترین روش‌هاست:
                        </p>
                        <div class="space-y-4">
                            <div class="bg-blue-50 border-r-4 border-blue-500 p-4 rounded">
                                <h4 class="font-semibold text-blue-800 mb-2">مراحل استعلام:</h4>
                                <ol class="text-blue-700 text-sm space-y-2">
                                    <li>۱. کلمه <strong>BIMEH</strong> را بنویسید</li>
                                    <li>۲. شماره پلاک را بدون فاصله اضافه کنید</li>
                                    <li>۳. کد ملی مالک را اضافه کنید</li>
                                    <li>۴. به شماره <strong>۳۰۰۰۲۶۲۱</strong> ارسال کنید</li>
                                </ol>
                            </div>
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">مثال عملی:</h4>
                                <div class="bg-white p-3 rounded border text-center font-mono text-lg">
                                    BIMEH 12ص34567 0123456789
                                </div>
                                <p class="text-green-700 text-sm mt-2">
                                    این پیامک را به ۳۰۰۰۲۶۲۱ ارسال کنید
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 bg-amber-50 border border-amber-200 p-4 rounded-lg">
                            <h4 class="font-semibold text-amber-800 mb-2">نکات مهم:</h4>
                            <ul class="text-amber-700 text-sm space-y-1">
                                <li>• هزینه هر پیامک ۵۰۰ تومان</li>
                                <li>• پاسخ در کمتر از ۳ دقیقه</li>
                                <li>• قابل استفاده از همه اپراتورها</li>
                                <li>• عدم نیاز به اینترنت</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="faq-item p-6" data-category="inquiry" data-tags="استعلام زمان مدت">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                            استعلام بیمه شخص ثالث چقدر زمان می‌برد؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-purple-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">زمان استعلام بیمه بستگی به روش انتخابی دارد:</p>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                <span class="text-green-700 font-medium">استعلام آنلاین</span>
                                <span class="text-green-600 font-bold">فوری (۱۰-۳۰ ثانیه)</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <span class="text-blue-700 font-medium">پیامک</span>
                                <span class="text-blue-600 font-bold">۱-۳ دقیقه</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                                <span class="text-orange-700 font-medium">تماس تلفنی</span>
                                <span class="text-orange-600 font-bold">۵-۱۰ دقیقه</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                                <span class="text-red-700 font-medium">مراجعه حضوری</span>
                                <span class="text-red-600 font-bold">۳۰+ دقیقه</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="faq-item p-6" data-category="inquiry" data-tags="استعلام رایگان هزینه">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                            آیا استعلام بیمه شخص ثالث رایگان است؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-purple-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">هزینه استعلام بستگی به روش انتخابی دارد:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">روش‌های رایگان:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• سایت پیشخوانک</li>
                                    <li>• سامانه سنهاب</li>
                                    <li>• سایت شرکت‌های بیمه</li>
                                    <li>• اپلیکیشن‌های موبایل</li>
                                </ul>
                            </div>
                            <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-orange-800 mb-2">روش‌های پولی:</h4>
                                <ul class="text-orange-700 text-sm space-y-1">
                                    <li>• پیامک (۵۰۰ تومان)</li>
                                    <li>• تماس تلفنی (بر اساس تعرفه)</li>
                                    <li>• مراجعه حضوری (هزینه جانبی)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ 5-8 continue... -->

            </div>
        </div>

        <!-- Category 4: شرکت‌های بیمه (Insurance Companies) - 7 FAQs -->
        <div class="faq-category" data-category="companies">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    شرکت‌های بیمه و خدمات
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <!-- FAQ 1 -->
                <div class="faq-item p-6" data-category="companies" data-tags="شرکت‌های بیمه معتبر لیست">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-indigo-600 transition-colors">
                            معتبرترین شرکت‌های بیمه شخص ثالث در ایران کدامند؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-indigo-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">شرکت‌های بیمه معتبر در ایران که مجوز صدور بیمه شخص ثالث دارند:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">شرکت‌های دولتی:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• بیمه ایران</li>
                                    <li>• بیمه البرز</li>
                                    <li>• بیمه آسیا</li>
                                    <li>• بیمه پارسیان</li>
                                    <li>• بیمه دانا</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">شرکت‌های خصوصی:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• بیمه پاسارگاد</li>
                                    <li>• بیمه سامان</li>
                                    <li>• بیمه رازی</li>
                                    <li>• بیمه نوین</li>
                                    <li>• بیمه ملت</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 bg-amber-50 border border-amber-200 p-4 rounded-lg">
                            <p class="text-amber-800 text-sm">
                                <strong>نکته:</strong> تمام این شرکت‌ها مجوز رسمی از بیمه مرکزی ایران دارند و اطلاعاتشان در سامانه سنهاب قابل مشاهده است.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="faq-item p-6" data-category="companies" data-tags="تغییر شرکت بیمه">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-indigo-600 transition-colors">
                            چگونه می‌توان شرکت بیمه را تغییر داد؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-indigo-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">تغییر شرکت بیمه فرآیند ساده‌ای است که باید در زمان تمدید انجام شود:</p>
                        <div class="space-y-4">
                            <div class="bg-green-50 border-r-4 border-green-500 p-4 rounded">
                                <h4 class="font-semibold text-green-800 mb-2">مراحل تغییر شرکت:</h4>
                                <ol class="text-green-700 text-sm space-y-2">
                                    <li>۱. بررسی تخفیف عدم خسارت از سنهاب</li>
                                    <li>۲. مقایسه نرخ‌های شرکت‌های مختلف</li>
                                    <li>۳. انتخاب شرکت جدید</li>
                                    <li>۴. صدور بیمه‌نامه جدید</li>
                                    <li>۵. لغو بیمه‌نامه قبلی (در صورت لزوم)</li>
                                </ol>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">نکات مهم:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• تخفیف عدم خسارت قابل انتقال است</li>
                                    <li>• بهترین زمان: ۳۰ روز قبل از انقضا</li>
                                    <li>• عدم انقطاع بیمه ضروری است</li>
                                    <li>• مقایسه خدمات و نرخ‌ها</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ 3-7 برای شرکت‌های بیمه -->

            </div>
        </div>

        <!-- Category 5: تخفیفات و نرخ‌ها (Discounts & Rates) - 6 FAQs -->
        <div class="faq-category" data-category="discounts">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    تخفیفات و نرخ‌ها
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <!-- FAQ 1 -->
                <div class="faq-item p-6" data-category="discounts" data-tags="تخفیف عدم خسارت محاسبه">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-emerald-600 transition-colors">
                            حداکثر تخفیف عدم خسارت چقدر است و چگونه محاسبه می‌شود؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-emerald-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">حداکثر تخفیف عدم خسارت در ایران <strong>۴۰ درصد</strong> است که پس از ۵ سال بدون خسارت قابل دریافت است:</p>
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 rounded-lg border">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b-2 border-gray-300">
                                            <th class="text-right p-3 font-bold">سال</th>
                                            <th class="text-right p-3 font-bold">تخفیف</th>
                                            <th class="text-right p-3 font-bold">مثال حق بیمه*</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-gray-200">
                                            <td class="p-3">سال اول</td>
                                            <td class="p-3 text-red-600 font-bold">۰٪</td>
                                            <td class="p-3">۳ میلیون</td>
                                        </tr>
                                        <tr class="border-b border-gray-200 bg-gray-50">
                                            <td class="p-3">سال دوم</td>
                                            <td class="p-3 text-orange-600 font-bold">۱۰٪</td>
                                            <td class="p-3">۲.۷ میلیون</td>
                                        </tr>
                                        <tr class="border-b border-gray-200">
                                            <td class="p-3">سال سوم</td>
                                            <td class="p-3 text-yellow-600 font-bold">۲۰٪</td>
                                            <td class="p-3">۲.۴ میلیون</td>
                                        </tr>
                                        <tr class="border-b border-gray-200 bg-gray-50">
                                            <td class="p-3">سال چهارم</td>
                                            <td class="p-3 text-green-600 font-bold">۳۰٪</td>
                                            <td class="p-3">۲.۱ میلیون</td>
                                        </tr>
                                        <tr class="bg-green-100">
                                            <td class="p-3">سال پنجم+</td>
                                            <td class="p-3 text-blue-600 font-bold">۴۰٪</td>
                                            <td class="p-3">۱.۸ میلیون</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-gray-600 text-xs mt-3">* مبالغ تقریبی برای خودروی پراید</p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="faq-item p-6" data-category="discounts" data-tags="تخفیف از دست رفتن">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-emerald-600 transition-colors">
                            در چه مواردی تخفیف عدم خسارت از بین می‌رود؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-emerald-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">تخفیف عدم خسارت در موارد زیر از بین می‌رود:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-red-800 mb-2">موارد قطعی:</h4>
                                <ul class="text-red-700 text-sm space-y-1">
                                    <li>• خسارت تصادف جاده‌ای</li>
                                    <li>• انقطاع بیش از ۶۰ روز</li>
                                    <li>• خسارت دو طرفه</li>
                                    <li>• خسارت یک طرفه مقصر</li>
                                </ul>
                            </div>
                            <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-orange-800 mb-2">موارد احتمالی:</h4>
                                <ul class="text-orange-700 text-sm space-y-1">
                                    <li>• تغییر نام مالک</li>
                                    <li>• تغییر نوع خودرو</li>
                                    <li>• عدم تمدید به موقع</li>
                                    <li>• خروج از شرکت بیمه</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ 3-6 for Discounts -->

            </div>
        </div>

        <!-- Category 6: قوانین و مقررات (Legal) - 6 FAQs -->
        <div class="faq-category" data-category="legal">
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                    قوانین و مقررات
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <!-- FAQ 1 -->
                <div class="faq-item p-6" data-category="legal" data-tags="قانون بیمه اجباری">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-red-600 transition-colors">
                            مبنای قانونی الزام بیمه شخص ثالث چیست؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-red-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">بیمه شخص ثالث بر اساس قوانین زیر الزامی است:</p>
                        <div class="space-y-4">
                            <div class="bg-blue-50 border-r-4 border-blue-500 p-4 rounded">
                                <h4 class="font-semibold text-blue-800 mb-2">قانون اصلی:</h4>
                                <p class="text-blue-700 text-sm">
                                    <strong>قانون بیمه اجباری خسارات وارد شده به شخص ثالث مصوب ۱۳۴۶</strong> - این قانون بیمه شخص ثالث را برای تمام وسایل نقلیه موتوری الزامی کرده است.
                                </p>
                            </div>
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">قوانین مکمل:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• قانون راهنمایی و رانندگی</li>
                                    <li>• آیین‌نامه اجرایی بیمه اجباری</li>
                                    <li>• بخشنامه‌های بیمه مرکزی</li>
                                    <li>• مقررات شرکت‌های بیمه</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ 2-6 for Legal -->

            </div>
        </div>

        <!-- Category 7: پوشش بیمه (Coverage) - 5 FAQs -->
        <div class="faq-category" data-category="coverage">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    پوشش بیمه
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <!-- FAQ 1 -->
                <div class="faq-item p-6" data-category="coverage" data-tags="پوشش جانی خسارت">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-teal-600 transition-colors">
                            پوشش خسارات جانی در بیمه شخص ثالث شامل چه مواردی است؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-teal-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">پوشش خسارات جانی شامل موارد جامعی است:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">خسارات فوری:</h4>
                                <ul class="text-green-700 text-sm space-y-1">
                                    <li>• هزینه‌های اورژانس و آمبولانس</li>
                                    <li>• هزینه‌های بستری فوری</li>
                                    <li>• جراحی‌های ضروری</li>
                                    <li>• داروهای اورژانسی</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">خسارات بلندمدت:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• درمان‌های تخصصی</li>
                                    <li>• توانبخشی و فیزیوتراپی</li>
                                    <li>• پروتز و کمک‌های حرکتی</li>
                                    <li>• مراقبت‌های ویژه</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 bg-red-50 border border-red-200 p-4 rounded-lg">
                            <h4 class="font-semibold text-red-800 mb-2">خسارات شدید:</h4>
                            <ul class="text-red-700 text-sm space-y-1">
                                <li>• دیه در صورت فوت</li>
                                <li>• ارش عضو نقص</li>
                                <li>• خونبها در موارد خاص</li>
                                <li>• هزینه‌های تشییع و دفن</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FAQ 2-5 for Coverage -->

            </div>
        </div>

        <!-- Category 8: مسائل فنی (Technical) - 5 FAQs -->
        <div class="faq-category" data-category="technical">
            <div class="bg-gradient-to-r from-cyan-600 to-cyan-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    مسائل فنی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <!-- FAQ 1 -->
                <div class="faq-item p-6" data-category="technical" data-tags="سایت کند مشکل">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-cyan-600 transition-colors">
                            چرا گاهی سایت‌های استعلام بیمه کند است یا خطا می‌دهد؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-cyan-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">مشکلات فنی ممکن است به دلایل مختلفی رخ دهد:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-orange-800 mb-2">مشکلات سرور:</h4>
                                <ul class="text-orange-700 text-sm space-y-1">
                                    <li>• ترافیک بالای کاربران</li>
                                    <li>• نگهداری و تعمیرات</li>
                                    <li>• به‌روزرسانی سیستم</li>
                                    <li>• مشکلات شبکه</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">راه‌حل‌ها:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• تلاش مجدد پس از چند دقیقه</li>
                                    <li>• پاک کردن کش مرورگر</li>
                                    <li>• استفاده از مرورگر دیگر</li>
                                    <li>• بررسی اتصال اینترنت</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ 2-5 for Technical -->

            </div>
        </div>

        <!-- Category 9: خسارات (Claims) - 4 FAQs -->
        <div class="faq-category" data-category="claims">
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    خسارات و حوادث
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <!-- FAQ 1 -->
                <div class="faq-item p-6" data-category="claims" data-tags="خسارت گزارش تصادف">
                    <button class="faq-question w-full text-right flex items-center justify-between group">
                        <span class="text-lg font-semibold text-gray-800 group-hover:text-orange-600 transition-colors">
                            پس از تصادف چه اقداماتی برای گزارش خسارت لازم است؟
                        </span>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-600 transition-transform duration-200 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 leading-relaxed hidden">
                        <p class="mb-4">پس از تصادف اقدامات زیر ضروری است:</p>
                        <div class="space-y-4">
                            <div class="bg-red-50 border-r-4 border-red-500 p-4 rounded">
                                <h4 class="font-semibold text-red-800 mb-2">اقدامات فوری:</h4>
                                <ol class="text-red-700 text-sm space-y-2">
                                    <li>۱. ایمن کردن محل حادثه</li>
                                    <li>۲. کمک به مصدومان</li>
                                    <li>۳. تماس با اورژانس (۱۱۵)</li>
                                    <li>۴. اطلاع به پلیس راهور (۱۹۷)</li>
                                </ol>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">مستندسازی:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• عکس‌برداری از محل حادثه</li>
                                    <li>• ثبت اطلاعات طرف مقابل</li>
                                    <li>• دریافت گزارش پلیس</li>
                                    <li>• تماس فوری با شرکت بیمه</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ 2-4 for Claims -->

            </div>
        </div>

    </div>

    <!-- Enterprise-Grade JavaScript with Advanced Persian Text Processing -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced FAQ Search functionality with Persian text support
        const searchInput = document.getElementById('advanced-faq-search');
        const categoryButtons = document.querySelectorAll('.faq-category-btn');
        const faqItems = document.querySelectorAll('.faq-item');
        const faqQuestions = document.querySelectorAll('.faq-question');
        const resultsCounter = document.getElementById('faq-results');
        const resultsCount = document.getElementById('results-count');
        
        let searchTimeout;
        let currentCategory = 'all';
        let highlightEnabled = true;
        
        // Advanced Persian text normalization for better search
        function normalizePersianText(text) {
            if (!text) return '';
            return text
                .replace(/ی/g, 'ي')  // Replace Persian Y with Arabic Y
                .replace(/ک/g, 'ك')  // Replace Persian K with Arabic K
                .replace(/ؤ/g, 'و')  // Replace Hamza above Waw
                .replace(/أ/g, 'ا')  // Replace Hamza above Alef
                .replace(/إ/g, 'ا')  // Replace Hamza below Alef
                .replace(/آ/g, 'ا')  // Replace Alef with Madda above
                .replace(/ة/g, 'ه')  // Replace Teh Marbuta with Heh
                .replace(/\u200C/g, ' ')  // Replace ZWNJ with space
                .replace(/\s+/g, ' ')     // Multiple spaces to single
                .trim()
                .toLowerCase();
        }
        
        // Advanced search algorithm with fuzzy matching
        function advancedSearch(text, searchTerm) {
            const normalizedText = normalizePersianText(text);
            const normalizedSearch = normalizePersianText(searchTerm);
            
            if (!normalizedSearch) return false;
            
            // Exact match (highest priority)
            if (normalizedText.includes(normalizedSearch)) return true;
            
            // Word-by-word matching
            const searchWords = normalizedSearch.split(' ').filter(word => word.length > 1);
            const textWords = normalizedText.split(' ');
            
            let matchCount = 0;
            searchWords.forEach(searchWord => {
                textWords.forEach(textWord => {
                    if (textWord.includes(searchWord) || searchWord.includes(textWord)) {
                        matchCount++;
                    }
                });
            });
            
            // At least 70% of search words should match
            return (matchCount / searchWords.length) >= 0.7;
        }
        
        // Advanced text highlighting with Persian support
        function highlightText(text, searchTerm) {
            if (!highlightEnabled || !searchTerm) return text;
            
            const normalizedSearch = normalizePersianText(searchTerm);
            const words = normalizedSearch.split(' ').filter(word => word.length > 1);
            
            let highlightedText = text;
            words.forEach(word => {
                const regex = new RegExp(`(${word})`, 'gi');
                highlightedText = highlightedText.replace(regex, '<span class="search-highlight">$1</span>');
            });
            
            return highlightedText;
        }
        
        // Remove all highlighting
        function removeHighlights() {
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question span');
                const answer = item.querySelector('.faq-answer');
                
                if (question) question.innerHTML = question.textContent;
                if (answer) answer.innerHTML = answer.textContent;
                
                item.classList.remove('search-match');
            });
        }

        // Enhanced search with debouncing and performance tracking
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const startTime = performance.now();
                filterFAQs();
                const endTime = performance.now();
                const searchTime = ((endTime - startTime) / 1000).toFixed(3);
                
                // Update search time if element exists
                const searchTimeElement = document.getElementById('search-time');
                if (searchTimeElement) {
                    searchTimeElement.textContent = searchTime;
                }
            }, 200); // 200ms debounce for optimal UX
        });
        
        // Main filtering function with performance timing
        function filterFAQs() {
            const searchTerm = searchInput.value.trim();
            let visibleCount = 0;
            
            // Remove previous highlights
            removeHighlights();
            
            faqItems.forEach(item => {
                const questionElement = item.querySelector('.faq-question span');
                const answerElement = item.querySelector('.faq-answer');
                const question = questionElement ? questionElement.textContent : '';
                const answer = answerElement ? answerElement.textContent : '';
                const tags = item.getAttribute('data-tags') || '';
                const itemCategory = item.getAttribute('data-category');
                
                // Advanced search matching
                const matchesSearch = !searchTerm || 
                                    advancedSearch(question, searchTerm) || 
                                    advancedSearch(answer, searchTerm) || 
                                    advancedSearch(tags, searchTerm);
                
                const matchesCategory = currentCategory === 'all' || itemCategory === currentCategory;
                const isVisible = matchesSearch && matchesCategory;
                
                if (isVisible) {
                    item.style.display = 'block';
                    visibleCount++;
                    
                    // Apply highlighting and search match styling
                    if (searchTerm && matchesSearch) {
                        item.classList.add('search-match');
                        
                        if (highlightEnabled) {
                            if (questionElement) {
                                questionElement.innerHTML = highlightText(question, searchTerm);
                            }
                            if (answerElement) {
                                answerElement.innerHTML = highlightText(answer, searchTerm);
                            }
                        }
                    }
                } else {
                    item.style.display = 'none';
                    item.classList.remove('search-match');
                }
            });

            // Update results counter
            if (searchTerm) {
                resultsCount.textContent = visibleCount;
                resultsCounter.classList.remove('hidden');
            } else {
                resultsCounter.classList.add('hidden');
            }

            // Update category visibility
            updateCategoryVisibility();
        }

        // Enhanced category filtering with state management
        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                currentCategory = this.getAttribute('data-category');
                
                // Update active button
                categoryButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-purple-600', 'text-white');
                    btn.classList.add('bg-gray-100', 'text-gray-700');
                });
                this.classList.add('active', 'bg-purple-600', 'text-white');
                this.classList.remove('bg-gray-100', 'text-gray-700');

                // Re-run the main filter function
                filterFAQs();
            });
        });

        // Enhanced FAQ accordion functionality with accessibility
        faqQuestions.forEach((question, index) => {
            const answer = question.nextElementSibling;
            const icon = question.querySelector('.faq-icon');
            
            // Add accessibility attributes
            question.setAttribute('tabindex', '0');
            question.setAttribute('role', 'button');
            question.setAttribute('aria-expanded', 'false');
            
            question.addEventListener('click', () => toggleFAQ(question, answer, icon));
            
            // Keyboard accessibility
            question.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleFAQ(question, answer, icon);
                }
            });
        });
        
        function toggleFAQ(question, answer, icon) {
            const isOpen = !answer.classList.contains('hidden');
            
            // Close all other FAQs
            faqQuestions.forEach(q => {
                const otherAnswer = q.nextElementSibling;
                const otherIcon = q.querySelector('.faq-icon');
                otherAnswer.classList.add('hidden');
                if (otherIcon) otherIcon.style.transform = 'rotate(0deg)';
                q.setAttribute('aria-expanded', 'false');
            });
            
            // Toggle current FAQ
            if (!isOpen) {
                answer.classList.remove('hidden');
                if (icon) icon.style.transform = 'rotate(180deg)';
                question.setAttribute('aria-expanded', 'true');
                
                // Smooth scroll to the opened FAQ
                setTimeout(() => {
                    question.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 100);
            }
        }

        function updateCategoryVisibility() {
            const categories = document.querySelectorAll('.faq-category');
            
            categories.forEach(category => {
                const categoryItems = category.querySelectorAll('.faq-item');
                let categoryHasVisible = false;
                
                categoryItems.forEach(item => {
                    if (item.style.display !== 'none') {
                        categoryHasVisible = true;
                    }
                });
                
                // Show/hide category based on visible items
                if (categoryHasVisible && (currentCategory === 'all' || category.dataset.category === currentCategory)) {
                    category.style.display = 'block';
                } else {
                    category.style.display = 'none';
                }
            });
        }
        
        // Keyboard shortcuts for enhanced user experience
        document.addEventListener('keydown', (e) => {
            // Ctrl+K to focus search
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
            
            // Escape to clear search
            if (e.key === 'Escape') {
                if (document.activeElement === searchInput) {
                    clearSearch();
                }
            }
        });
        
        // Clear search functionality
        function clearSearch() {
            searchInput.value = '';
            currentCategory = 'all';
            
            // Reset active category button
            categoryButtons.forEach(btn => btn.classList.remove('active', 'bg-purple-600', 'text-white'));
            categoryButtons.forEach(btn => btn.classList.add('bg-gray-100', 'text-gray-700'));
            categoryButtons[0].classList.add('active', 'bg-purple-600', 'text-white');
            categoryButtons[0].classList.remove('bg-gray-100', 'text-gray-700');
            
            filterFAQs();
            searchInput.focus();
        }
        
        // Initialize with all categories visible
        filterFAQs();
    });
    </script>

    <!-- Contact and Support Section -->
    <section class="mb-12">
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl p-8 text-white">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-4">
                    آماده پاسخگویی به سوالات شما هستیم
                </h2>
                <p class="text-xl text-gray-300 leading-relaxed mb-8">
                    تیم متخصص پیشخوانک در تمام ساعات شبانه‌روز آماده ارائه مشاوره و راهنمایی در زمینه بیمه شخص ثالث است
                </p>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white bg-opacity-10 rounded-2xl p-6 backdrop-blur-sm">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold mb-2">پشتیبانی تلفنی</h3>
                        <p class="text-gray-300 text-sm">۰۲۱-۱۲۳۴۵۶۷۸</p>
                        <p class="text-gray-400 text-xs mt-1">۲۴ ساعته، ۷ روز هفته</p>
                    </div>
                    
                    <div class="bg-white bg-opacity-10 rounded-2xl p-6 backdrop-blur-sm">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold mb-2">چت آنلاین</h3>
                        <p class="text-gray-300 text-sm">پشتیبانی فوری</p>
                        <p class="text-gray-400 text-xs mt-1">پاسخ در کمتر از ۲ دقیقه</p>
                    </div>
                    
                    <div class="bg-white bg-opacity-10 rounded-2xl p-6 backdrop-blur-sm">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold mb-2">ایمیل پشتیبانی</h3>
                        <p class="text-gray-300 text-sm">support@pishkhanak.com</p>
                        <p class="text-gray-400 text-xs mt-1">پاسخ در کمتر از ۲ ساعت</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</section>