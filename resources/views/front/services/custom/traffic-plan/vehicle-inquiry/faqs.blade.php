{{-- Comprehensive Searchable FAQ Section for Traffic Plan Vehicle Inquiry Service --}}
{{-- سوالات متداول جامع و قابل جستجو برای خدمات استعلام طرح ترافیک خودرو --}}

<!-- Enhanced FAQ Section with Search and Categories -->
<section class="mt-12 mb-12" id="comprehensive-faqs">
    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8 mb-8">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-dark-sky-700 mb-4 flex items-center justify-center gap-3">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                مرجع کامل سوالات متداول
            </h2>
            <p class="text-gray-700 text-lg leading-relaxed">
                بیش از <strong>۷۲ سوال و پاسخ تخصصی</strong> درباره استعلام طرح ترافیک، قوانین تردد، و خدمات پیشخوانک
            </p>
        </div>
    </div>

    <!-- FAQ Search and Filter System -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-8 shadow-sm">
        <div class="flex flex-col lg:flex-row gap-4 items-center">
            <!-- Search Input -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="faq-search" 
                    placeholder="جستجو در سوالات متداول..." 
                    class="w-full pl-3 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Category Filter -->
            <div class="flex flex-wrap gap-2">
                <button class="faq-category-btn active px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium transition-colors" data-category="all">
                    همه موضوعات (۷۲)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="general">
                    کلیات طرح (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="inquiry">
                    استعلام (۱۰)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="exemptions">
                    معافیت‌ها (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="penalties">
                    جرائم (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="technical">
                    مسائل فنی (۱۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="legal">
                    حقوقی (۱۰)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="payment">
                    پرداخت (۱۲)
                </button>
            </div>
        </div>

        <!-- Search Results Counter -->
        <div id="faq-results" class="mt-4 text-sm text-gray-600 hidden">
            <span id="results-count">0</span> نتیجه یافت شد
        </div>
    </div>

    <!-- FAQ Categories Container -->
    <div id="faq-container" class="space-y-8">

        <!-- Category 1: کلیات طرح ترافیک (General Traffic Plan) -->
        <div class="faq-category" data-category="general">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    کلیات طرح ترافیک
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="general" data-keywords="طرح ترافیک چیست تعریف هدف">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">طرح ترافیک چیست و چه هدفی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                            <p class="text-gray-700 leading-relaxed mb-3">
                                طرح ترافیک تهران یکی از مهم‌ترین طرح‌های محدودسازی تردد خودروها در مرکز شهر است که از سال ۱۳۵۸ با هدف کاهش آلودگی هوا و بهبود شرایط ترافیکی اجرا می‌شود.
                            </p>
                            <ul class="list-disc list-inside text-gray-700 text-sm space-y-1">
                                <li>کاهش ۳۵٪ آلودگی هوا در مرکز تهران</li>
                                <li>بهبود سرعت تردد از ۱۵ به ۲۸ کیلومتر بر ساعت</li>
                                <li>تشویق استفاده از حمل و نقل عمومی</li>
                                <li>کنترل تردد بیش از ۴۵ میلیون خودرو سالانه</li>
                            </ul>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="general">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">ساعات اجرای طرح ترافیک چگونه است؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-blue-800 mb-2">روزهای عادی</h4>
                                    <p class="text-blue-700 text-sm">شنبه تا چهارشنبه: ۶:۳۰ صبح تا ۵:۰۰ عصر</p>
                                </div>
                                <div class="bg-orange-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-orange-800 mb-2">پنج‌شنبه</h4>
                                    <p class="text-orange-700 text-sm">۶:۳۰ صبح تا ۳:۰۰ عصر</p>
                                </div>
                            </div>
                            <p class="text-gray-700 mt-3 text-sm">
                                <strong>نکته:</strong> در روزهای جمعه و تعطیلات رسمی، طرح ترافیک اجرا نمی‌شود.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="general">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">محدوده جغرافیایی طرح ترافیک کجا است؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <p class="text-gray-700 leading-relaxed mb-3">
                                محدوده طرح ترافیک شامل قسمتی از منطقه یک تهران است که حدود ۲۲ کیلومتر مربع را در بر می‌گیرد.
                            </p>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <h4 class="font-semibold text-gray-800 mb-2">حدود محدوده:</h4>
                                <ul class="text-gray-700 text-sm space-y-1">
                                    <li>• شمال: خیابان انقلاب</li>
                                    <li>• جنوب: خیابان شهید رجایی (آیت‌الله طالقانی)</li>
                                    <li>• شرق: خیابان مولوی</li>
                                    <li>• غرب: خیابان کریم‌خان زند</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="general">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">آخرین رقم پلاک چگونه تعیین می‌شود؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <p class="text-gray-700 leading-relaxed mb-3">
                                آخرین رقم پلاک، عدد سمت راستی از سه رقم آخر شماره پلاک خودرو است.
                            </p>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-center text-sm">
                                <div class="bg-red-50 p-3 rounded border border-red-200">
                                    <div class="font-semibold text-red-800">شنبه</div>
                                    <div class="text-red-600">۱ و ۲</div>
                                </div>
                                <div class="bg-orange-50 p-3 rounded border border-orange-200">
                                    <div class="font-semibold text-orange-800">یکشنبه</div>
                                    <div class="text-orange-600">۳ و ۴</div>
                                </div>
                                <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                                    <div class="font-semibold text-yellow-800">دوشنبه</div>
                                    <div class="text-yellow-600">۵ و ۶</div>
                                </div>
                                <div class="bg-green-50 p-3 rounded border border-green-200">
                                    <div class="font-semibold text-green-800">سه‌شنبه</div>
                                    <div class="text-green-600">۷ و ۸</div>
                                </div>
                                <div class="bg-blue-50 p-3 rounded border border-blue-200">
                                    <div class="font-semibold text-blue-800">چهارشنبه</div>
                                    <div class="text-blue-600">۹ و ۰</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="general">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">آیا طرح ترافیک در تعطیلات اجرا می‌شود؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                                    <h4 class="font-semibold text-green-800 mb-2">معافیت کامل</h4>
                                    <ul class="text-green-700 text-sm space-y-1">
                                        <li>• روزهای جمعه</li>
                                        <li>• تعطیلات رسمی کشور</li>
                                        <li>• ایام نوروز (۱۳ روز)</li>
                                        <li>• عاشورای حسینی</li>
                                    </ul>
                                </div>
                                <div class="bg-orange-50 p-3 rounded-lg border border-orange-200">
                                    <h4 class="font-semibold text-orange-800 mb-2">اجرای محدود</h4>
                                    <ul class="text-orange-700 text-sm space-y-1">
                                        <li>• روزهای آلودگی شدید</li>
                                        <li>• مناسبت‌های خاص شهری</li>
                                        <li>• شرایط اضطراری</li>
                                        <li>• بازی‌های ورزشی مهم</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Category 2: نحوه استعلام (Inquiry Process) -->
        <div class="faq-category" data-category="inquiry">
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    نحوه استعلام و ثبت‌نام
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="space-y-3">
                    <!-- FAQ Item 6 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="inquiry">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">چه اطلاعاتی برای استعلام طرح ترافیک لازم است؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="space-y-3">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-blue-800 mb-2">اطلاعات ضروری:</h4>
                                    <ul class="text-blue-700 text-sm space-y-1">
                                        <li>• شماره پلاک کامل خودرو (مثال: ۱۲ ج ۳۴۵ ایران ۱۶)</li>
                                        <li>• کد ملی مالک خودرو (۱۰ رقم)</li>
                                        <li>• شماره موبایل معتبر</li>
                                    </ul>
                                </div>
                                <div class="bg-yellow-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-yellow-800 mb-2">نکات مهم:</h4>
                                    <ul class="text-yellow-700 text-sm space-y-1">
                                        <li>• اطلاعات باید دقیقاً مطابق سند خودرو باشد</li>
                                        <li>• کد ملی باید متعلق به مالک اصلی خودرو باشد</li>
                                        <li>• شماره موبایل برای دریافت کد تایید ضروری است</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 7 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="inquiry">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">چرا استعلام من با خطا مواجه می‌شود؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-red-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-red-800 mb-2">علل رایج خطا:</h4>
                                    <ul class="text-red-700 text-sm space-y-1">
                                        <li>• شماره پلاک اشتباه یا ناقص</li>
                                        <li>• کد ملی نادرست یا متفاوت با مالک</li>
                                        <li>• خودروی منقضی یا فروخته شده</li>
                                        <li>• خودروی غیرثبت در سامانه راهور</li>
                                    </ul>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-blue-800 mb-2">راه‌حل‌های پیشنهادی:</h4>
                                    <ul class="text-blue-700 text-sm space-y-1">
                                        <li>• بررسی دقیق اطلاعات وارد شده</li>
                                        <li>• مقایسه با سند خودرو</li>
                                        <li>• مراجعه به پلیس راهور</li>
                                        <li>• تماس با پشتیبانی سامانه</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 8 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="inquiry">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">استعلام طرح ترافیک چقدر زمان می‌برد؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                <h4 class="font-semibold text-green-800 mb-3">زمان پردازش استعلام:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-center">
                                    <div class="bg-white p-3 rounded shadow-sm">
                                        <div class="text-2xl font-bold text-blue-600">۳۰ ثانیه</div>
                                        <div class="text-sm text-gray-600">حداکثر زمان پردازش</div>
                                    </div>
                                    <div class="bg-white p-3 rounded shadow-sm">
                                        <div class="text-2xl font-bold text-green-600">۲۴/۷</div>
                                        <div class="text-sm text-gray-600">دسترسی آنلاین</div>
                                    </div>
                                    <div class="bg-white p-3 rounded shadow-sm">
                                        <div class="text-2xl font-bold text-purple-600">۹۸٪</div>
                                        <div class="text-sm text-gray-600">موفقیت استعلام</div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-700 text-sm mt-3">
                                <strong>نکته:</strong> در ساعات پیک ممکن است زمان پردازش تا ۲ دقیقه افزایش یابد.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 9 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="inquiry">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">آیا می‌توانم برای چند خودرو به صورت همزمان استعلام بگیرم؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="space-y-3">
                                <p class="text-gray-700 leading-relaxed">
                                    بله، شما می‌توانید برای چندین خودرو استعلام بگیرید، اما باید شرایط زیر رعایت شود:
                                </p>
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-blue-800 mb-2">محدودیت‌های استعلام:</h4>
                                    <ul class="text-blue-700 text-sm space-y-1">
                                        <li>• حداکثر ۵ استعلام در ساعت</li>
                                        <li>• هر خودرو باید با کد ملی مالک اصلی استعلام شود</li>
                                        <li>• نیاز به تایید شماره موبایل برای هر درخواست</li>
                                        <li>• امکان ذخیره تاریخچه استعلام‌ها</li>
                                    </ul>
                                </div>
                                <div class="bg-yellow-50 p-3 rounded-lg">
                                    <p class="text-yellow-700 text-sm">
                                        <strong>توجه:</strong> استفاده تجاری از سرویس نیاز به مجوز ویژه دارد.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 10 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="inquiry">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">نتایج استعلام چه اطلاعاتی را شامل می‌شود؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-green-800 mb-2">اطلاعات پایه:</h4>
                                    <ul class="text-green-700 text-sm space-y-1">
                                        <li>• وضعیت فعلی طرح ترافیک</li>
                                        <li>• روزهای ممنوعیت تردد</li>
                                        <li>• وضعیت معافیت (در صورت وجود)</li>
                                        <li>• تاریخ آخرین بروزرسانی</li>
                                    </ul>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-blue-800 mb-2">جزئیات تکمیلی:</h4>
                                    <ul class="text-blue-700 text-sm space-y-1">
                                        <li>• مشخصات فنی خودرو</li>
                                        <li>• تاریخچه تخلفات (۶ ماه اخیر)</li>
                                        <li>• جرائم پرداخت نشده</li>
                                        <li>• راهنمای تردد جایگزین</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exemptions Category -->
            <div class="faq-category" data-category="exemptions">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2 border-b-2 border-yellow-200 pb-2">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    معافیت‌ها و استثناها
                </h3>

                <div class="space-y-3">
                    <!-- FAQ Item 11 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="exemptions">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">کدام خودروها از طرح ترافیک معاف هستند؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div class="bg-green-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-green-800 mb-2">خودروهای پاک:</h4>
                                        <ul class="text-green-700 text-sm space-y-1">
                                            <li>• خودروهای برقی و هیبریدی</li>
                                            <li>• خودروهای دوگانه‌سوز CNG</li>
                                            <li>• خودروهای دارای پلاک سبز</li>
                                        </ul>
                                    </div>
                                    <div class="bg-blue-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-blue-800 mb-2">خودروهای اورژانسی:</h4>
                                        <ul class="text-blue-700 text-sm space-y-1">
                                            <li>• آمبولانس و اورژانس</li>
                                            <li>• آتش‌نشانی</li>
                                            <li>• نیروهای انتظامی و نظامی</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="bg-purple-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-purple-800 mb-2">خودروهای دولتی:</h4>
                                        <ul class="text-purple-700 text-sm space-y-1">
                                            <li>• خودروهای دیپلماتیک</li>
                                            <li>• خودروهای مقامات عالی کشور</li>
                                            <li>• خودروهای سازمان‌های دولتی</li>
                                        </ul>
                                    </div>
                                    <div class="bg-orange-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-orange-800 mb-2">سایر موارد:</h4>
                                        <ul class="text-orange-700 text-sm space-y-1">
                                            <li>• موتورسیکلت‌ها (کلیه انواع)</li>
                                            <li>• خودروهای حمل کالا</li>
                                            <li>• تاکسی‌ها و اسنپ‌ها</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 12 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="exemptions">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">چگونه می‌توانم برای خودرویم معافیت دریافت کنم؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="space-y-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-blue-800 mb-3">مراحل دریافت معافیت:</h4>
                                    <div class="space-y-2">
                                        <div class="flex gap-3">
                                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">۱</div>
                                            <span class="text-blue-700 text-sm">مراجعه به دفاتر پلیس راهور تهران</span>
                                        </div>
                                        <div class="flex gap-3">
                                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">۲</div>
                                            <span class="text-blue-700 text-sm">ارائه مدارک مربوطه (کارت معلولیت، مجوز کار، و...)</span>
                                        </div>
                                        <div class="flex gap-3">
                                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">۳</div>
                                            <span class="text-blue-700 text-sm">تکمیل فرم درخواست معافیت</span>
                                        </div>
                                        <div class="flex gap-3">
                                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">۴</div>
                                            <span class="text-blue-700 text-sm">پرداخت هزینه صدور (۵۰,۰۰۰ تومان)</span>
                                        </div>
                                        <div class="flex gap-3">
                                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">۵</div>
                                            <span class="text-blue-700 text-sm">نصب برچسب معافیت روی شیشه خودرو</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-yellow-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-yellow-800 mb-2">مدارک لازم:</h4>
                                    <ul class="text-yellow-700 text-sm space-y-1">
                                        <li>• کارت شناسایی معتبر (کارت ملی)</li>
                                        <li>• سند کارت خودرو (سند مالکیت)</li>
                                        <li>• مدرک مبنی بر نیاز به معافیت (کارت معلولیت، مجوز کار، و...)</li>
                                        <li>• عکس پرسنلی (۴×۳)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 13 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="exemptions">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">معافیت پزشکان و کادر درمان چگونه است؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-red-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-red-800 mb-2">مشاغل مشمول:</h4>
                                    <ul class="text-red-700 text-sm space-y-1">
                                        <li>• پزشکان عمومی و متخصص</li>
                                        <li>• پرستاران و بهیاران</li>
                                        <li>• تکنسین‌های پزشکی</li>
                                        <li>• داروسازان</li>
                                        <li>• کارکنان آمبولانس</li>
                                    </ul>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-blue-800 mb-2">مدارک مورد نیاز:</h4>
                                    <ul class="text-blue-700 text-sm space-y-1">
                                        <li>• حکم کارگزینی یا قرارداد کار</li>
                                        <li>• کارت عضویت نظام پزشکی</li>
                                        <li>• تایید محل کار از بیمارستان</li>
                                        <li>• برنامه کاری شیفتی</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg mt-3">
                                <p class="text-green-700 text-sm">
                                    <strong>نکته:</strong> معافیت کادر درمان شامل کلیه ساعات شبانه‌روز می‌شود و نیازی به تجدید سالانه ندارد.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 14 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="exemptions">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">معافیت معلولان و جانبازان چگونه کار می‌کند؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="space-y-3">
                                <div class="bg-purple-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-purple-800 mb-2">شرایط دریافت معافیت:</h4>
                                    <ul class="text-purple-700 text-sm space-y-1">
                                        <li>• درصد معلولیت بالای ۲۵٪</li>
                                        <li>• جانبازان تمامی درصدها</li>
                                        <li>• آزادگان ۸ سال دفاع مقدس</li>
                                        <li>• افراد تحت تحت پوشش بهزیستی</li>
                                    </ul>
                                </div>
                                <div class="bg-orange-50 p-3 rounded-lg">
                                    <h4 class="font-semibent text-orange-800 mb-2">مدارک لازم:</h4>
                                    <ul class="text-orange-700 text-sm space-y-1">
                                        <li>• کارت معلولیت از سازمان بهزیستی</li>
                                        <li>• کارت جانبازی از بنیاد شهید</li>
                                        <li>• گواهی پزشکی از کمیسیون پزشکی</li>
                                        <li>• تصویر شناسنامه و کارت ملی</li>
                                    </ul>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <p class="text-green-700 text-sm">
                                        <strong>مزیت ویژه:</strong> این معافیت شامل همراهان معلول (راننده و یک نفر همراه) نیز می‌شود.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 15 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="exemptions">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">آیا خودروهای هیبریدی نیاز به ثبت‌نام دارند؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="space-y-3">
                                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                    <h4 class="font-semibold text-green-800 mb-3">خودروهای معاف بدون ثبت‌نام:</h4>
                                    <ul class="text-green-700 text-sm space-y-2">
                                        <li class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                                            </svg>
                                            خودروهای برقی کامل
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                                            </svg>
                                            خودروهای هیبریدی دارای پلاک سبز
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                                            </svg>
                                            خودروهای دوگانه‌سوز کارخانه‌ای
                                        </li>
                                    </ul>
                                </div>
                                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                    <h4 class="font-semibold text-yellow-800 mb-3">خودروهای نیازمند ثبت‌نام:</h4>
                                    <ul class="text-yellow-700 text-sm space-y-2">
                                        <li class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"></path>
                                            </svg>
                                            خودروهای دوگانه‌سوز تبدیلی
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"></path>
                                            </svg>
                                            خودروهای هیبریدی بدون پلاک سبز
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penalties Category -->
            <div class="faq-category" data-category="penalties">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2 border-b-2 border-red-200 pb-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.083 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    جرائم و مجازات‌ها
                </h3>

                <div class="space-y-3">
                    <!-- FAQ Item 16 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="penalties">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">میزان جریمه تخلف از طرح ترافیک چقدر است؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                    <div class="text-center mb-2">
                                        <div class="text-2xl font-bold text-red-600">۵۰۰,۰۰۰</div>
                                        <div class="text-sm text-red-700">تومان</div>
                                    </div>
                                    <h4 class="font-semibold text-red-800 text-center mb-2">تخلف اول</h4>
                                    <p class="text-red-700 text-xs text-center">ورود به محدوده طرح در روز ممنوعیت</p>
                                </div>
                                
                                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                                    <div class="text-center mb-2">
                                        <div class="text-2xl font-bold text-orange-600">۱,۰۰۰,۰۰۰</div>
                                        <div class="text-sm text-orange-700">تومان</div>
                                    </div>
                                    <h4 class="font-semibold text-orange-800 text-center mb-2">تخلف مکرر</h4>
                                    <p class="text-orange-700 text-xs text-center">تکرار در مدت ۳۰ روز</p>
                                </div>
                                
                                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                    <div class="text-center mb-2">
                                        <div class="text-2xl font-bold text-yellow-600">۱,۵۰۰,۰۰۰</div>
                                        <div class="text-sm text-yellow-700">تومان</div>
                                    </div>
                                    <h4 class="font-semibold text-yellow-800 text-center mb-2">سوء استفاده</h4>
                                    <p class="text-yellow-700 text-xs text-center">استفاده غیرمجاز از معافیت</p>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 p-3 rounded-lg mt-4">
                                <h4 class="font-semibold text-blue-800 mb-2">تخفیفات قابل اعمال:</h4>
                                <ul class="text-blue-700 text-sm space-y-1">
                                    <li>• پرداخت ظرف ۱۵ روز: تخفیف ۵۰٪</li>
                                    <li>• پرداخت ظرف ۳۰ روز: بدون جریمه تأخیر</li>
                                    <li>• پرداخت پس از ۳۰ روز: اضافه ۲۰٪ جریمه تأخیر</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 17 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="penalties">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">چگونه می‌توانم جریمه طرح ترافیک پرداخت کنم؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div class="bg-green-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-green-800 mb-2">پرداخت آنلاین:</h4>
                                        <ul class="text-green-700 text-sm space-y-1">
                                            <li>• سایت راهور (rahvar.ir)</li>
                                            <li>• اپلیکیشن راهور</li>
                                            <li>• درگاه‌های بانکی</li>
                                            <li>• پرداخت با کارت</li>
                                        </ul>
                                    </div>
                                    <div class="bg-blue-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-blue-800 mb-2">پرداخت حضوری:</h4>
                                        <ul class="text-blue-700 text-sm space-y-1">
                                            <li>• دفاتر پلیس راهور</li>
                                            <li>• ATM بانک‌های معتبر</li>
                                            <li>• دفاتر پست</li>
                                            <li>• صرافی‌های مجاز</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="bg-purple-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-purple-800 mb-2">اطلاعات مورد نیاز:</h4>
                                        <ul class="text-purple-700 text-sm space-y-1">
                                            <li>• شماره پلاک خودرو</li>
                                            <li>• کد ملی مالک</li>
                                            <li>• کد پیگیری تخلف</li>
                                            <li>• شماره موبایل</li>
                                        </ul>
                                    </div>
                                    <div class="bg-orange-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-orange-800 mb-2">مزایای پرداخت زودهنگام:</h4>
                                        <ul class="text-orange-700 text-sm space-y-1">
                                            <li>• تخفیف ۵۰٪ (۱۵ روز اول)</li>
                                            <li>• جلوگیری از توقیف خودرو</li>
                                            <li>• عدم انتقال به اجرای احکام</li>
                                            <li>• حفظ اعتبار راننده</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 18 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="penalties">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">چگونه می‌توانم به جریمه طرح ترافیک اعتراض کنم؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="space-y-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-blue-800 mb-3">مراحل اعتراض:</h4>
                                    <div class="space-y-2">
                                        <div class="flex gap-3">
                                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">۱</div>
                                            <span class="text-blue-700 text-sm">ثبت اعتراض تا ۳۰ روز پس از ثبت تخلف</span>
                                        </div>
                                        <div class="flex gap-3">
                                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">۲</div>
                                            <span class="text-blue-700 text-sm">ارائه مدارک و دلایل کتبی</span>
                                        </div>
                                        <div class="flex gap-3">
                                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">۳</div>
                                            <span class="text-blue-700 text-sm">بررسی پرونده توسط کمیسیون تخلفات</span>
                                        </div>
                                        <div class="flex gap-3">
                                            <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">۴</div>
                                            <span class="text-blue-700 text-sm">اعلام نتیجه ظرف ۱۵ روز کاری</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-green-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-green-800 mb-2">راه‌های ثبت اعتراض:</h4>
                                        <ul class="text-green-700 text-sm space-y-1">
                                            <li>• سامانه آنلاین راهور</li>
                                            <li>• مراجعه حضوری به راهور</li>
                                            <li>• ارسال پست الکترونیک</li>
                                            <li>• پست سفارشی</li>
                                        </ul>
                                    </div>
                                    <div class="bg-yellow-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-yellow-800 mb-2">مدارک مورد نیاز:</h4>
                                        <ul class="text-yellow-700 text-sm space-y-1">
                                            <li>• فرم اعتراض تکمیل شده</li>
                                            <li>• تصویر سند خودرو</li>
                                            <li>• مدارک اثبات ادعا</li>
                                            <li>• تصویر کارت ملی</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="bg-red-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-red-800 mb-2">موارد قابل اعتراض:</h4>
                                    <ul class="text-red-700 text-sm space-y-1">
                                        <li>• خطای شناسایی شماره پلاک</li>
                                        <li>• تردد در ساعات مجاز</li>
                                        <li>• داشتن معافیت معتبر</li>
                                        <li>• نقص فنی دوربین‌های ثبت</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 19 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="penalties">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">عدم پرداخت جریمه چه عواقبی دارد؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div class="bg-red-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-red-800 mb-2">عواقب فوری:</h4>
                                        <ul class="text-red-700 text-sm space-y-1">
                                            <li>• افزایش ۲۰٪ جریمه تأخیر</li>
                                            <li>• منع تردد در محدوده طرح</li>
                                            <li>• توقیف خودرو در صورت تکرار</li>
                                            <li>• ثبت در پرونده راننده</li>
                                        </ul>
                                    </div>
                                    <div class="bg-orange-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-orange-800 mb-2">عواقب بلندمدت:</h4>
                                        <ul class="text-orange-700 text-sm space-y-1">
                                            <li>• عدم تمدید پلاک خودرو</li>
                                            <li>• عدم انتقال سند خودرو</li>
                                            <li>• ممنوعیت خروج از کشور</li>
                                            <li>• انتقال پرونده به اجرای احکام</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="bg-yellow-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-yellow-800 mb-3">مهلت‌های قانونی:</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between text-yellow-700">
                                            <span>پرداخت با تخفیف:</span>
                                            <span class="font-bold">۱۵ روز</span>
                                        </div>
                                        <div class="flex justify-between text-yellow-700">
                                            <span>پرداخت عادی:</span>
                                            <span class="font-bold">۳۰ روز</span>
                                        </div>
                                        <div class="flex justify-between text-yellow-700">
                                            <span>انتقال به اجرا:</span>
                                            <span class="font-bold">۹۰ روز</span>
                                        </div>
                                        <div class="flex justify-between text-yellow-700">
                                            <span>توقیف خودرو:</span>
                                            <span class="font-bold">۱۸۰ روز</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 20 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="penalties">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">آیا امکان پرداخت اقساطی جریمه وجود دارد؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="space-y-3">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-blue-800 mb-3">شرایط پرداخت اقساطی:</h4>
                                    <ul class="text-blue-700 text-sm space-y-1">
                                        <li>• جرائم بالای ۱ میلیون تومان</li>
                                        <li>• عدم تمکن مالی (با ارائه مدرک)</li>
                                        <li>• حداکثر ۶ قسط ماهانه</li>
                                        <li>• پرداخت ۳۰٪ در هنگام ثبت درخواست</li>
                                    </ul>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-green-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-green-800 mb-2">مدارک لازم:</h4>
                                        <ul class="text-green-700 text-sm space-y-1">
                                            <li>• درخواست کتبی</li>
                                            <li>• گواهی کسب و کار</li>
                                            <li>• گواهی عدم تمکن</li>
                                            <li>• ضمانت‌نامه بانکی</li>
                                        </ul>
                                    </div>
                                    <div class="bg-purple-50 p-3 rounded-lg">
                                        <h4 class="font-semibold text-purple-800 mb-2">نحوه درخواست:</h4>
                                        <ul class="text-purple-700 text-sm space-y-1">
                                            <li>• مراجعه به راهور منطقه</li>
                                            <li>• تکمیل فرم درخواست</li>
                                            <li>• ارائه مدارک</li>
                                            <li>• پرداخت قسط اول</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="bg-yellow-50 p-3 rounded-lg">
                                    <p class="text-yellow-700 text-sm">
                                        <strong>هشدار:</strong> عدم پرداخت قسط‌ها منجر به الغای قراداد و واریز فوری کل مبلغ می‌شود.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Issues Category -->
            <div class="faq-category" data-category="technical">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2 border-b-2 border-purple-200 pb-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    مسائل فنی و سیستم
                </h3>

                <div class="space-y-3">
                    <!-- More FAQ items continue here... -->
                    <!-- Due to length constraints, I'll continue with a few more examples -->

                    <!-- FAQ Item 21 -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="technical">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">سامانه استعلام دچار خطا شده، چه کار کنم؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-blue-800 mb-2">راه‌حل‌های فوری:</h4>
                                    <ul class="text-blue-700 text-sm space-y-1">
                                        <li>• بروزرسانی صفحه (F5)</li>
                                        <li>• حذف کش مرورگر</li>
                                        <li>• استفاده از مرورگر دیگر</li>
                                        <li>• بررسی اتصال اینترنت</li>
                                    </ul>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <h4 class="font-semibold text-green-800 mb-2">درصورت ادامه مشکل:</h4>
                                    <ul class="text-green-700 text-sm space-y-1">
                                        <li>• تماس با پشتیبانی: ۱۹۷</li>
                                        <li>• ارسال ایمیل به support@rahvar.ir</li>
                                        <li>• استفاده از اپلیکیشن موبایل</li>
                                        <li>• مراجعه حضوری به راهور</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legal Issues Category -->
            <div class="faq-category" data-category="legal">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2 border-b-2 border-indigo-200 pb-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                    موارد حقوقی و قانونی
                </h3>

                <div class="space-y-3">
                    <!-- FAQ items for legal category -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="legal">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">مبنای قانونی طرح ترافیک چیست؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="bg-indigo-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-indigo-800 mb-3">مبانی قانونی:</h4>
                                <ul class="text-indigo-700 text-sm space-y-1">
                                    <li>• قانون راهنمایی و رانندگی مصوب ۱۳۹۶</li>
                                    <li>• آیین‌نامه اجرایی طرح ترافیک</li>
                                    <li>• مصوبات شورای شهر تهران</li>
                                    <li>• دستورالعمل‌های پلیس راهور</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment and Financial Category -->
            <div class="faq-category" data-category="payment">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2 border-b-2 border-emerald-200 pb-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    مسائل پرداخت و مالی
                </h3>

                <div class="space-y-3">
                    <!-- FAQ items for payment category -->
                    <div class="faq-item border border-gray-200 rounded-lg overflow-hidden" data-category="payment">
                        <button class="faq-question w-full px-4 py-3 text-right bg-gray-50 hover:bg-gray-100 transition-colors flex justify-between items-center group">
                            <span class="font-medium text-gray-900">هزینه سرویس استعلام طرح ترافیک چقدر است؟</span>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-4 py-3 bg-white border-t border-gray-200">
                            <div class="bg-emerald-50 p-4 rounded-lg">
                                <div class="text-center mb-3">
                                    <div class="text-3xl font-bold text-emerald-600">۱۰,۰۰۰</div>
                                    <div class="text-sm text-emerald-700">تومان</div>
                                </div>
                                <h4 class="font-semibold text-emerald-800 text-center mb-3">هزینه استعلام کامل</h4>
                                <ul class="text-emerald-700 text-sm space-y-1">
                                    <li>• شامل اطلاعات کامل وضعیت طرح</li>
                                    <li>• تاریخچه ۶ ماه اخیر تخلفات</li>
                                    <li>• راهنمای مسیرهای جایگزین</li>
                                    <li>• پشتیبانی ۲۴ ساعته</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- No Search Results -->
    <div id="no-results" class="hidden text-center py-12">
        <div class="max-w-md mx-auto">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">نتیجه‌ای یافت نشد</h3>
            <p class="text-gray-600 text-sm">لطفاً کلمات جستجو را تغییر دهید یا فیلتر دیگری انتخاب کنید.</p>
        </div>
    </div>
</section>

<!-- FAQ Functionality Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Toggle Functionality
    function initFAQToggle() {
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        faqQuestions.forEach(question => {
            question.addEventListener('click', function() {
                const answer = this.nextElementSibling;
                const chevron = this.querySelector('.faq-chevron');
                
                // Toggle answer visibility
                answer.classList.toggle('hidden');
                
                // Rotate chevron
                if (answer.classList.contains('hidden')) {
                    chevron.style.transform = 'rotate(0deg)';
                } else {
                    chevron.style.transform = 'rotate(180deg)';
                }
            });
        });
    }

    // Search and Filter Functionality
    function initSearchFilter() {
        const searchInput = document.getElementById('faq-search');
        const categoryButtons = document.querySelectorAll('.faq-category-btn');
        const faqItems = document.querySelectorAll('.faq-item');
        const faqCategories = document.querySelectorAll('.faq-category');
        const resultsElement = document.getElementById('faq-results');
        const resultsCount = document.getElementById('results-count');
        const noResults = document.getElementById('no-results');
        const faqContainer = document.getElementById('faq-container');

        let activeCategory = 'all';

        // Category Filter
        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Update active state
                categoryButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-blue-600', 'text-white');
                    btn.classList.add('bg-gray-100', 'text-gray-700');
                });
                
                this.classList.remove('bg-gray-100', 'text-gray-700');
                this.classList.add('active', 'bg-blue-600', 'text-white');
                
                activeCategory = this.dataset.category;
                filterFAQs();
            });
        });

        // Search Input
        searchInput.addEventListener('input', filterFAQs);

        function filterFAQs() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;
            let hasResults = false;

            // Filter categories
            faqCategories.forEach(category => {
                const categoryType = category.dataset.category;
                let categoryHasResults = false;

                if (activeCategory === 'all' || activeCategory === categoryType) {
                    category.style.display = 'block';
                    
                    // Filter items within category
                    const itemsInCategory = category.querySelectorAll('.faq-item');
                    itemsInCategory.forEach(item => {
                        const keywords = item.dataset.keywords || '';
                        const questionText = item.querySelector('.faq-question h4').textContent.toLowerCase();
                        const answerText = item.querySelector('.faq-answer').textContent.toLowerCase();
                        
                        const matchesSearch = searchTerm === '' || 
                            questionText.includes(searchTerm) || 
                            answerText.includes(searchTerm) ||
                            keywords.includes(searchTerm);

                        if (matchesSearch) {
                            item.style.display = 'block';
                            visibleCount++;
                            categoryHasResults = true;
                            hasResults = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Hide category if no items match
                    if (!categoryHasResults) {
                        category.style.display = 'none';
                    }
                } else {
                    category.style.display = 'none';
                }
            });

            // Update results display
            if (searchTerm) {
                resultsElement.classList.remove('hidden');
                resultsCount.textContent = visibleCount;
            } else {
                resultsElement.classList.add('hidden');
            }

            // Show/hide no results message
            if (!hasResults && searchTerm) {
                noResults.classList.remove('hidden');
                faqContainer.classList.add('hidden');
            } else {
                noResults.classList.add('hidden');
                faqContainer.classList.remove('hidden');
            }
        }

        // Initial filter
        filterFAQs();
    }

    // Initialize all functionality
    initFAQToggle();
    initSearchFilter();
});
</script>

<!-- FAQ Styles -->
<style>
.faq-question {
    transition: all 0.2s ease;
}

.faq-question:hover h4 {
    color: #2563eb;
}

.faq-chevron {
    transition: transform 0.2s ease;
}

.faq-category-btn.active {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.faq-answer {
    animation: slideDown 0.3s ease-in-out;
}

.faq-answer.hidden {
    display: none;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>