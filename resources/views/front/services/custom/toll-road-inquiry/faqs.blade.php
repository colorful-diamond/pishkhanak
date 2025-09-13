{{-- Comprehensive Searchable FAQ Section for Highway Toll Inquiry Service --}}
{{-- سوالات متداول جامع و قابل جستجو برای خدمات استعلام عوارض آزادراهی --}}

<!-- Enhanced FAQ Section with Search and Categories -->
<section class="mt-12 mb-12" id="comprehensive-faqs">
    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8 mb-8">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-dark-sky-700 mb-4 flex items-center justify-center gap-3">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                مرجع کامل سوالات متداول عوارض آزادراهی
            </h2>
            <p class="text-gray-700 text-lg leading-relaxed">
                بیش از <strong>۶۵ سوال و پاسخ تخصصی</strong> درباره استعلام عوارض آزادراهی، سامانه آنی رو، و پرداخت الکترونیکی
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
                    placeholder="جستجو در سوالات متداول عوارض آزادراهی..." 
                    class="w-full pl-3 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Category Filter -->
            <div class="flex flex-wrap gap-2">
                <button class="faq-category-btn active px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium transition-colors" data-category="all">
                    همه موضوعات (۶۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="inquiry">
                    استعلام عوارض (۸)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="aniro">
                    آنی رو (۷)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="payment">
                    پرداخت (۷)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="electronic">
                    عوارض الکترونیکی (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="penalties">
                    جریمه‌ها (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="license-plate">
                    پلاک (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="rates">
                    نرخ‌ها (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="technical">
                    مسائل فنی (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="aggregate">
                    عوارض تجمیعی (۴)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="freeway">
                    بزرگراهی (۴)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="non-stop">
                    پرداخت بدون توقف (۴)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="security">
                    امنیت (۳)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="additional">
                    موارد خاص (۵)
                </button>
            </div>
        </div>

        <!-- Search Results Counter -->
        <div id="faq-results" class="mt-4 text-sm text-gray-600 hidden">
            <span id="results-count">0</span> نتیجه یافت شد
        </div>
    </div>

    <!-- No Results Message -->
    <div id="no-results" class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center hidden">
        <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">نتیجه‌ای یافت نشد</h3>
        <p class="text-yellow-700 mb-4">متأسفانه برای جستجوی شما نتیجه‌ای پیدا نکردیم. لطفاً عبارت دیگری امتحان کنید.</p>
        <button onclick="clearSearch()" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
            پاک کردن جستجو
        </button>
    </div>

    <!-- FAQ Categories Container -->
    <div id="faq-container" class="space-y-8">

        <!-- Category 1: استعلام عوارض آزادراهی -->
        <div class="faq-category" data-category="inquiry">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    استعلام عوارض آزادراهی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="inquiry" data-keywords="استعلام عوارض آزادراهی چیست">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">استعلام عوارض آزادراهی چیست و چگونه انجام می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>استعلام عوارض آزادراهی فرآیند بررسی میزان بدهی خودرو به <strong>سامانه آنی رو</strong> و آزادراه‌های کشور است. برای این کار:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>وارد سایت یا اپلیکیشن معتبر شوید</li>
                            <li>شماره پلاک خودرو را وارد کنید</li>
                            <li>فهرست کامل عوارض نمایش داده می‌شود</li>
                            <li>امکان پرداخت فوری وجود دارد</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="شماره پلاک استعلام نحوه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">شماره پلاک را باید چگونه وارد کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        شماره پلاک را <strong>دقیقاً مطابق پلاک خودرو</strong> وارد کنید. مثلاً برای پلاک ۱۲ ج ۳۴۵ ایران ۲۳، عدد ۱۲ - حرف ج - عدد ۳۴۵ - کد شهر ۲۳ را به ترتیب وارد نمایید. دقت کنید که حروف فارسی باشند.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="رایگان استعلام هزینه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا استعلام عوارض آزادراهی رایگان است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، استعلام عوارض کاملاً رایگان است.</strong> شما هیچ هزینه‌ای برای بررسی میزان بدهی خود پرداخت نمی‌کنید و تنها در صورت تمایل به پرداخت، مبلغ عوارض پرداخت می‌شود.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="چندین خودرو همزمان استعلام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم چندین خودرو را همزمان استعلام کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، امکان استعلام همزمان چندین خودرو وجود دارد.</strong> این ویژگی برای مالکان ناوگان، شرکت‌های حمل و نقل، و افرادی که چند خودرو دارند بسیار مفید است. می‌توانید تا ۱۰ خودرو را همزمان بررسی کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="زمان استعلام سرعت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">استعلام عوارض چقدر زمان می‌برد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        استعلام عوارض <strong>کمتر از ۵ ثانیه</strong> زمان می‌برد. سیستم به صورت آنی تمامی پایگاه‌های اطلاعاتی را بررسی کرده و فهرست کاملی از بدهی‌های شما را ارائه می‌دهد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="کد ملی نیاز احراز هویت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا برای استعلام به کد ملی نیاز دارم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، برای استعلام عوارض تنها شماره پلاک کافی است.</strong> نیازی به وارد کردن کد ملی، شماره شناسنامه، یا سایر اطلاعات شخصی نیست. این امر باعث سادگی و سرعت فرآیند می‌شود.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="دقیق اطلاعات به‌روز">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اطلاعات استعلام شده تا چه حد دقیق است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        اطلاعات <strong>۱۰۰٪ دقیق و به‌روز</strong> هستند چراکه مستقیماً از منابع رسمی سامانه آنی رو، سمیع، و سایر منابع معتبر دریافت می‌شوند. تأخیر بین ثبت تردد و نمایش در سیستم حداکثر ۳۰ دقیقه است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="تاریخچه ترددات جزئیات">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا تاریخچه ترددات قابل مشاهده است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، تاریخچه کامل ترددات نمایش داده می‌شود.</strong> شامل تاریخ و ساعت دقیق عبور، نام آزادراه، مبلغ عوارض، و وضعیت پرداخت. این اطلاعات برای ۱۲ ماه گذشته قابل دسترس هستند.
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 2: سامانه آنی رو -->
        <div class="faq-category" data-category="aniro">
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    سامانه آنی رو
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="aniro" data-keywords="آنی رو چیست تعریف">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه آنی رو چیست و چگونه کار می‌کند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>سامانه آنی رو <strong>سیستم الکترونیکی وصول عوارض</strong> آزادراه‌های کشور است که توسط وزارت راه راه‌اندازی شده:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>تشخیص خودکار پلاک با دوربین</li>
                            <li>ثبت تردد بدون نیاز به توقف</li>
                            <li>محاسبه خودکار مبلغ عوارض</li>
                            <li>ارسال پیامک اطلاع‌رسانی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="aniro" data-keywords="آزادراه پوشش شده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه آزادراه‌هایی تحت پوشش آنی رو هستند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در حال حاضر <strong>۲۵ آزادراه</strong> تحت پوشش آنی رو قرار دارند: تهران-ساوه، تهران-قم، کرج-قزوین، قزوین-زنجان، تهران-پردیس، بندرعباس-بندر شهید رجایی، ارومیه-تبریز، همت-کرج و ۱۷ آزادراه دیگر.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="aniro" data-keywords="پیامک دریافت اطلاع‌رسانی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چرا پیامک آنی رو دریافت نمی‌کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        علل احتمالی عدم دریافت پیامک: <strong>عدم ثبت شماره موبایل</strong> در سامانه، پر بودن حافظه گوشی، فیلتر پیامک‌های تبلیغاتی، یا مشکل شبکه موبایل. برای حل مشکل به سایت aaniro.ir مراجعه کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="aniro" data-keywords="اپلیکیشن دانلود">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اپلیکیشن آنی رو از کجا دانلود کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        اپلیکیشن رسمی آنی رو از <strong>مایکت، کافه بازار، و گوگل پلی</strong> قابل دانلود است. همچنین می‌توانید از لینک مستقیم در سایت aaniro.ir استفاده کنید. از دانلود اپلیکیشن از منابع غیررسمی خودداری کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="aniro" data-keywords="ثبت نام عضویت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا نیاز به ثبت نام در آنی رو دارم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای <strong>استعلام عوارض نیازی به ثبت نام نیست</strong>، اما برای استفاده از خدمات پیشرفته مانند پرداخت خودکار، شارژ کیف پول، و دریافت گزارش‌های تفصیلی، ثبت نام الزامی است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="aniro" data-keywords="پشتیبانی تماس">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">شماره پشتیبانی آنی رو چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        شماره پشتیبانی آنی رو <strong>۱۵۳۱</strong> است. همچنین می‌توانید از طریق سایت aaniro.ir اقدام به ثبت تیکت کنید یا به آدرس support@aaniro.ir ایمیل ارسال نمایید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="aniro" data-keywords="اعتراض شکایت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه به عوارض ثبت شده اعتراض کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای اعتراض می‌توانید از <strong>سه روش</strong> استفاده کنید: پیامک به شماره ۱۰۰۰۰۱۵۳، تماس با شماره ۱۵۳۱، یا ثبت تیکت در سایت aaniro.ir. اعتراضات معمولاً ظرف ۴۸ ساعت بررسی می‌شوند.
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 3: پرداخت عوارض -->
        <div class="faq-category" data-category="payment">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    پرداخت عوارض آزادراه
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="payment" data-keywords="روش پرداخت آنلاین">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه روش‌هایی برای پرداخت عوارض وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>روش‌های مختلف پرداخت عوارض عبارتند از:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>آنلاین:</strong> سایت‌ها و اپلیکیشن‌های معتبر</li>
                            <li><strong>کد دستوری:</strong> *137*3*3#</li>
                            <li><strong>خودپردازها:</strong> ATM بانک‌ها</li>
                            <li><strong>بانک‌داری الکترونیک:</strong> همراه بانک، آپ</li>
                            <li><strong>درگاه‌های پرداخت:</strong> شاپرک</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="payment" data-keywords="کارت بانکی پذیرش">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کدام کارت‌های بانکی پذیرش می‌شوند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>تمامی کارت‌های عضو شتاب</strong> از جمله کارت‌های ملی، ملت، پارسیان، پاسارگاد، صادرات، تجارت، کشاورزی، و سایر بانک‌های معتبر پذیرش می‌شوند. کارت باید فعال و دارای موجودی کافی باشد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="payment" data-keywords="رسید پرداخت دیجیتال">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا پس از پرداخت رسید دریافت می‌کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، بلافاصله پس از پرداخت موفق</strong> رسید دیجیتال شامل شماره پیگیری، مبلغ پرداختی، تاریخ و ساعت تراکنش از طریق پیامک یا ایمیل ارسال می‌شود. این رسید معتبر و قابل ارائه به مراجع رسمی است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="payment" data-keywords="ناموفق عدم موفقیت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">در صورت ناموفق بودن پرداخت چه کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در صورت ناموفق بودن پرداخت: <strong>موجودی کارت را بررسی کنید</strong>، اتصال اینترنت را چک کنید، مجدداً تلاش کنید. اگر مبلغ از حساب کسر شده اما پرداخت ثبت نشده، ظرف ۲۴ تا ۷۲ ساعت مبلغ برگشت می‌خورد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="payment" data-keywords="قسطی پرداخت اعتباری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا امکان پرداخت قسطی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در حال حاضر <strong>پرداخت قسطی عوارض در اولویت برنامه‌ریزی</strong> قرار دارد و به زودی این امکان اضافه خواهد شد. فعلاً باید کل مبلغ بدهی را یکجا پرداخت کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="payment" data-keywords="پرداخت دسته‌ای یکجا">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم چندین عوارض را یکجا پرداخت کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، امکان پرداخت دسته‌ای وجود دارد.</strong> می‌توانید تمامی عوارض یک خودرو یا چندین خودرو را انتخاب کرده و با یک تراکنش پرداخت کنید. این ویژگی باعث صرفه‌جویی در وقت و هزینه تراکنش می‌شود.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="payment" data-keywords="برگشت مبلغ استرداد">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا امکان برگشت مبلغ پرداختی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در موارد خاص نظیر <strong>پرداخت اشتباهی، خطای سیستم، یا پذیرش اعتراض</strong> امکان برگشت مبلغ وجود دارد. برای این منظور باید درخواست رسمی ثبت کرده و مدارک لازم را ارائه دهید. فرآیند معمولاً ۷ تا ۱۰ روز کاری طول می‌کشد.
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 4: عوارض الکترونیکی -->
        <div class="faq-category" data-category="electronic">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    عوارض الکترونیکی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="electronic" data-keywords="الکترونیکی تعریف مزایا">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">عوارض الکترونیکی چیست و چه مزایایی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>عوارض الکترونیکی سیستم هوشمند وصول عوارض بدون توقف است. مزایای آن:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>عدم نیاز به توقف:</strong> سفر بدون وقفه</li>
                            <li><strong>صرفه‌جویی زمان:</strong> کاهش ۴۰ دقیقه‌ای زمان سفر</li>
                            <li><strong>کاهش مصرف سوخت:</strong> تا ۳۰ درصد</li>
                            <li><strong>کاهش آلودگی هوا:</strong> کمتر کردن انتشار گازها</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="electronic" data-keywords="تشخیص پلاک دقت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سیستم تشخیص پلاک تا چه حد دقیق است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        سیستم تشخیص پلاک آنی رو با دقت <strong>بیش از ۹۸ درصد</strong> عمل می‌کند. این سیستم قابلیت تشخیص پلاک در سرعت‌های بالا (تا ۱۸۰ کیلومتر بر ساعت) را دارد و در شرایط مختلف آب و هوایی عملکرد مناسبی دارد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="electronic" data-keywords="پلاک کثیف خراب">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر پلاک خودرو کثیف یا خراب باشد چه می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در صورت عدم تشخیص پلاک، <strong>دوربین‌های پشتیبان و سیستم‌های جانبی</strong> فعال می‌شوند. اگر باز هم پلاک تشخیص داده نشود، عکس خودرو ثبت شده و به صورت دستی بررسی می‌شود. توصیه می‌شود پلاک خودرو را همیشه تمیز نگه دارید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="electronic" data-keywords="سرعت حداکثر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">حداکثر سرعت مجاز برای تشخیص پلاک چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        سیستم آنی رو قابلیت تشخیص پلاک تا سرعت <strong>۱۸۰ کیلومتر بر ساعت</strong> را دارد. البته باید سرعت مجاز آزادراه را رعایت کرده و از سرعت غیرمجاز خودداری کنید. سرعت بهینه برای تشخیص ۸۰-۱۲۰ کیلومتر است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="electronic" data-keywords="شب تاریکی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا سیستم در شب و تاریکی کار می‌کند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، سیستم ۲۴ ساعته فعال است.</strong> دوربین‌های آنی رو مجهز به تکنولوژی مادون قرمز و نورافکن‌های قوی هستند که امکان تشخیص دقیق پلاک در شب، باران، مه، و سایر شرایط نامساعد جوی را فراهم می‌کنند.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="electronic" data-keywords="موتور سیکلت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا موتورسیکلت‌ها نیز شناسایی می‌شوند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، موتورسیکلت‌ها نیز تشخیص داده می‌شوند</strong> اما نرخ عوارض آنها متفاوت از خودروهای سواری است. سیستم قابلیت تشخیص انواع مختلف وسایل نقلیه از جمله موتور، سواری، کامیون، اتوبوس و تریلر را دارد.
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 5: جریمه‌ها و مجازات‌ها -->
        <div class="faq-category" data-category="penalties">
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.316 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    جریمه عدم پرداخت عوارض
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="penalties" data-keywords="مهلت پرداخت ۱۵ روز">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مهلت پرداخت عوارض چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مهلت پرداخت عوارض آزادراهی <strong>۱۵ روز کامل</strong> از زمان عبور از آزادراه است. این مهلت از لحظه ثبت تردد در سیستم محاسبه می‌شود، نه از زمان ارسال پیامک. توصیه می‌شود پرداخت را سریع‌تر انجام دهید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="penalties" data-keywords="جریمه مبلغ مقدار">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مبلغ جریمه عدم پرداخت چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>جریمه عدم پرداخت بر اساس نوع خودرو متفاوت است:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>خودروی سواری، ون، وانت:</strong> ۸۰ هزار تومان</li>
                            <li><strong>اتوبوس، کامیون دو محور:</strong> ۱۰۰ هزار تومان</li>
                            <li><strong>کامیون سه محور، تریلر:</strong> ۱۸۰ هزار تومان</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="penalties" data-keywords="تصاعدی ماهانه ۳.۵ درصد">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا جریمه با گذشت زمان افزایش می‌یابد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، از آبان ۱۴۰۲ سیستم جریمه تصاعدی اجرا شده است.</strong> در صورت عدم پرداخت جریمه، ماهانه ۳.۵ درصد به مبلغ اصلی عوارض (نه جریمه) اضافه می‌شود. این فرآیند تا پرداخت کامل ادامه می‌یابد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="penalties" data-keywords="بیمه شخص ثالث">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا عدم پرداخت عوارض بر بیمه تأثیر می‌گذارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، طبق بودجه ۱۴۰۲</strong> خرید یا تمدید بیمه شخص ثالث منوط به پرداخت تمامی بدهی‌های عوارضی است. در صورت وجود بدهی عوارض، امکان خرید یا تمدید بیمه وجود نخواهد داشت.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="penalties" data-keywords="مراجع قضایی اجرا">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا پرونده عوارض به مراجع قضایی ارجاع می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در صورت عدم پرداخت طولانی‌مدت و تجمع بدهی، پرونده به <strong>اجرای احکام مدنی</strong> ارجاع می‌شود. این امر می‌تواند منجر به مسدود شدن حساب‌های بانکی، توقیف اموال، یا منع خروج از کشور شود.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="penalties" data-keywords="اعتراض ناصحیح ثبت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه به جریمه ناصحیح اعتراض کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای اعتراض می‌توانید از <strong>سه روش</strong> استفاده کنید: پیامک به ۱۰۰۰۰۱۵۳، تماس با ۱۵۳۱، یا ثبت تیکت در aaniro.ir. مدارک مثبت مانند رسید پرداخت، عکس خودرو در مکان دیگر، یا گواهی عدم تردد را همراه داشته باشید.
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 6: استعلام با پلاک -->
        <div class="faq-category" data-category="license-plate">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m0 0a2 2 0 01-2-2m2 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6a2 2 0 012-2h6a2 2 0 012 2z"></path>
                    </svg>
                    استعلام عوارض با پلاک
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="license-plate" data-keywords="نحوه ورود پلاک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه شماره پلاک را صحیح وارد کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>نحوه صحیح وارد کردن پلاک:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>فرمت:</strong> دو رقم اول - حرف - سه رقم - کد شهر</li>
                            <li><strong>مثال:</strong> ۱۲ ج ۳۴۵ ایران ۲۳</li>
                            <li><strong>حروف:</strong> حتماً فارسی وارد کنید</li>
                            <li><strong>دقت:</strong> فاصله‌ها و ترتیب را رعایت کنید</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="license-plate" data-keywords="پلاک موقت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا پلاک‌های موقت شناسایی می‌شوند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، پلاک‌های موقت نیز شناسایی می‌شوند</strong> اما ممکن است با تأخیر بیشتری در سیستم ثبت شوند. برای استعلام پلاک موقت، کد کامل روی برگه موقت را همراه با شماره شهر وارد کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="license-plate" data-keywords="پلاک شخصی خاص">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">پلاک‌های شخصی‌سازی شده چگونه وارد شوند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای پلاک‌های شخصی‌سازی شده (مانند پلاک‌های آیین یا اسامی) <strong>دقیقاً همان ترتیب و شکل روی پلاک</strong> را وارد کنید. اگر دارای کاراکترهای خاص است، از کیبورد فارسی استفاده کرده و فاصله‌ها را رعایت کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="license-plate" data-keywords="خطا عدم تطابق">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">پیغام خطای "عدم تطابق پلاک" چه معنی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        این خطا نشان‌دهنده <strong>عدم وجود پلاک در پایگاه داده</strong> یا اشتباه در وارد کردن آن است. ابتدا صحت پلاک وارد شده را بررسی کنید، سپس اگر باز هم خطا داشت، احتمالاً خودرو تردد نکرده یا هنوز در سیستم ثبت نشده است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="license-plate" data-keywords="چندین خودرو ناوگان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">برای ناوگان چندین خودرو چه راهکاری وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای مدیریت ناوگان <strong>حساب سازمانی</strong> ایجاد کنید که امکانات ویژه‌ای دارد: استعلام دسته‌ای تا ۱۰۰ خودرو، گزارش‌گیری تفصیلی، پنل مدیریت، تنظیم هشدارهای خودکار، و پرداخت متمرکز. برای راه‌اندازی با پشتیبانی تماس بگیرید.
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 7: نرخ‌ها و تعرفه‌ها -->
        <div class="faq-category" data-category="rates">
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    نرخ‌های عوارض آزادراهی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="rates" data-keywords="نرخ عوارض ۱۴۰۳">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">نرخ عوارض آزادراهی در سال ۱۴۰۳ چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>نرخ‌های عوارض در سال ۱۴۰۳ (با افزایش ۲۰-۲۵٪ نسبت به سال قبل):</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>خودروی سواری:</strong> ۴۵ تا ۴۹۵ هزار ریال</li>
                            <li><strong>ون و مینی‌بوس:</strong> ۶۵ تا ۶۷۰ هزار ریال</li>
                            <li><strong>کامیون دو محور:</strong> ۱۳۰ تا ۸۱۵ هزار ریال</li>
                            <li><strong>تریلر:</strong> ۲۱۵ هزار تا ۱,۲۷۰ هزار ریال</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="rates" data-keywords="محاسبه عوارض فاکتورها">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">عوارض بر اساس چه فاکتورهایی محاسبه می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>فاکتورهای تأثیرگذار در محاسبه عوارض:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>نوع خودرو:</strong> سواری، کامیون، اتوبوس</li>
                            <li><strong>وزن خودرو:</strong> تعداد محورها</li>
                            <li><strong>مسافت:</strong> کیلومتر طی شده</li>
                            <li><strong>آزادراه:</strong> نرخ هر آزادراه متفاوت</li>
                            <li><strong>زمان:</strong> برخی آزادراه‌ها تخفیف شبانه</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="rates" data-keywords="گران‌ترین آزادراه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">گران‌ترین و ارزان‌ترین آزادراه‌ها کدامند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>گران‌ترین:</strong> آزادراه تهران-شمال (۴۹۵ هزار ریال برای سواری) به دلیل طولانی بودن مسیر و هزینه بالای ساخت. <strong>ارزان‌ترین:</strong> آزادراه‌های کوتاه مانند کاشان-نطنز (۴۵ هزار ریال) به دلیل مسافت کم.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="rates" data-keywords="تخفیف شبانه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا در ساعات مختلف نرخ عوارض متفاوت است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در حال حاضر در اکثر آزادراه‌ها نرخ یکسان است، اما <strong>برخی آزادراه‌ها تخفیف شبانه</strong> (ساعت ۲۲ تا ۶ صبح) ارائه می‌دهند. این تخفیف معمولاً ۱۰ تا ۲۰ درصد است و برای کاهش ترافیک روزانه اعمال می‌شود.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="rates" data-keywords="افزایش سالانه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا نرخ‌ها سالانه افزایش می‌یابند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، نرخ‌های عوارض معمولاً سالانه بازبینی می‌شوند.</strong> این افزایش بر اساس نرخ تورم، هزینه‌های نگهداری، و سیاست‌های اقتصادی تعیین می‌شود. در ۳ سال اخیر میانگین افزایش سالانه ۲۰ تا ۳۰ درصد بوده است.
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 8: مسائل فنی -->
        <div class="faq-category" data-category="technical">
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    مسائل فنی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="technical" data-keywords="سایت کند بطئ">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چرا سایت استعلام عوارض کند است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        علل کندی سایت: <strong>ترافیک بالا در ساعات پیک</strong> (۱۰ تا ۱۴ و ۱۸ تا ۲۱)، مشکل اتصال اینترنت شما، یا تعمیرات سرور. برای بهتر کارکردن از مرورگرهای به‌روز استفاده کنید و Cache را پاک کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="خطای اتصال">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">پیغام "خطا در برقراری اتصال" چه معنی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        این خطا معمولاً به دلیل <strong>قطع موقت اینترنت</strong> یا مشکل سرور است. کارهای پیشنهادی: بررسی اتصال اینترنت، تلاش مجدد پس از چند دقیقه، استفاده از شبکه متفاوت (مثلاً موبایل به جای وای‌فای).
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="مرورگر پشتیبانی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">از چه مرورگرهایی پشتیبانی می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>مرورگرهای پشتیبانی شده:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>کروم:</strong> نسخه ۸۰ به بعد</li>
                            <li><strong>فایرفاکس:</strong> نسخه ۷۵ به بعد</li>
                            <li><strong>سافاری:</strong> نسخه ۱۳ به بعد</li>
                            <li><strong>اج:</strong> نسخه ۸۰ به بعد</li>
                        </ul>
                        <p class="mt-3">از Internet Explorer پشتیبانی نمی‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="موبایل اپلیکیشن">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا اپلیکیشن موبایل دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، اپلیکیشن پیشخوانک</strong> برای اندروید در دسترس است و نسخه iOS نیز به زودی منتشر خواهد شد. همچنین می‌توانید از سایت بهینه شده موبایل (PWA) استفاده کنید که تمام امکانات اپلیکیشن را دارد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="امنیت اطلاعات">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">امنیت اطلاعات چگونه تضمین می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>اقدامات امنیتی اتخاذ شده:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>SSL 256 بیتی:</strong> رمزنگاری تمامی اطلاعات</li>
                            <li><strong>عدم ذخیره:</strong> اطلاعات پرداخت ذخیره نمی‌شود</li>
                            <li><strong>احراز دو مرحله‌ای:</strong> OTP برای تراکنش‌ها</li>
                            <li><strong>نظارت ۲۴/۷:</strong> کنترل مستمر امنیت</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 9: عوارض تجمیعی -->
        <div class="faq-category" data-category="aggregate">
            <div class="bg-gradient-to-r from-pink-600 to-pink-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    عوارض تجمیعی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="aggregate" data-keywords="تجمیعی تعریف مفهوم">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">عوارض تجمیعی چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>عوارض تجمیعی یعنی <strong>استعلام و پرداخت یکجای تمامی انواع عوارض</strong> یک خودرو شامل:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>عوارض آزادراهی</li>
                            <li>عوارض شهرداری</li>
                            <li>عوارض طرح ترافیک</li>
                            <li>عوارض زوج و فرد</li>
                            <li>عوارض پارکینگ</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="aggregate" data-keywords="مزایای تجمیعی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مزایای استعلام تجمیعی چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>مزایای عوارض تجمیعی:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>صرفه‌جویی زمان:</strong> یک بار استعلام، همه چیز</li>
                            <li><strong>کاهش هزینه:</strong> کمتر کردن کارمزد تراکنش</li>
                            <li><strong>مدیریت آسان:</strong> کنترل متمرکز بدهی‌ها</li>
                            <li><strong>جلوگیری فراموشی:</strong> دیدن کامل بدهی‌ها</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="aggregate" data-keywords="انتخابی پرداخت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم برخی عوارض را انتخابی پرداخت کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، امکان پرداخت انتخابی وجود دارد.</strong> می‌توانید از فهرست کامل عوارض، موارد مورد نظر خود را انتخاب کرده و فقط آن‌ها را پرداخت کنید. این ویژگی برای مدیریت بهتر بودجه مفید است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="aggregate" data-keywords="گزارش کامل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا گزارش کامل عوارض ارائه می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transformation group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، گزارش تفصیلی شامل:</strong> تاریخچه ترددات، مبلغ هر عوارض، وضعیت پرداخت، تاریخ سررسید، و نمودار مصرف ماهانه ارائه می‌شود. گزارش به فرمت PDF و Excel قابل دریافت است.
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 10: بزرگراهی -->
        <div class="faq-category" data-category="freeway">
            <div class="bg-gradient-to-r from-cyan-600 to-cyan-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    استعلام عوارض بزرگراهی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="freeway" data-keywords="بزرگراهی تفاوت آزادراهی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">تفاوت عوارض بزرگراهی با آزادراهی چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>آزادراه:</strong> مسیرهای برون‌شهری و بین استانی با سرعت بالا</p>
                        <p><strong>بزرگراه:</strong> مسیرهای درون‌شهری و برخی برون‌شهری با چندین خروجی</p>
                        <p>هر دو در سامانه یکپارچه مدیریت می‌شوند اما نرخ‌های متفاوتی دارند.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="freeway" data-keywords="شهری بزرگراه داخلی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کدام بزرگراه‌های شهری عوارض دارند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در حال حاضر بزرگراه‌های <strong>همت-کرج، آزادگان، یادگار امام</strong> در تهران و <strong>بزرگراه شهید چمران</strong> در اصفهان دارای سیستم عوارض الکترونیکی هستند. سایر بزرگراه‌های شهری به تدریج به این سیستم اضافه می‌شوند.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="freeway" data-keywords="نرخ بزرگراهی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">نرخ عوارض بزرگراه‌ها چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        نرخ عوارض بزرگراه‌ها معمولاً <strong>پائین‌تر از آزادراه‌ها</strong> است: خودروی سواری ۲۰ تا ۵۰ هزار ریال، کامیون ۴۰ تا ۸۰ هزار ریال. نرخ‌ها بر اساس طول مسیر و شلوغی ترافیک تعیین می‌شوند.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="freeway" data-keywords="آینده توسعه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا عوارض بزرگراهی توسعه خواهد یافت؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، برنامه‌ای برای گسترش عوارض الکترونیکی</strong> به تمام بزرگراه‌های شهرهای بزرگ وجود دارد. هدف کاهش ترافیک، مدیریت هوشمند تردد، و تأمین بودجه توسعه حمل و نقل عمومی است.
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 11: پرداخت بدون توقف -->
        <div class="faq-category" data-category="non-stop">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    پرداخت بدون توقف عوارض
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="non-stop" data-keywords="بدون توقف نحوه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه پرداخت بدون توقف فعال کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>برای فعال‌سازی پرداخت بدون توقف:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>در اپلیکیشن آنی رو ثبت نام کنید</li>
                            <li><strong>کیف پول خود را شارژ کنید</strong></li>
                            <li>پلاک خودرو را ثبت کنید</li>
                            <li>پرداخت خودکار را فعال کنید</li>
                            <li>حد اعتبار مناسب تنظیم کنید</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="non-stop" data-keywords="کیف پول شارژ">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه کیف پول را شارژ کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>روش‌های شارژ کیف پول:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>کارت بانکی:</strong> آنلاین از طریق درگاه</li>
                            <li><strong>اپلیکیشن بانکی:</strong> همراه بانک، آپ</li>
                            <li><strong>ATM:</strong> خودپردازهای بانک‌ها</li>
                            <li><strong>فروشگاه:</strong> نمایندگی‌های مجاز</li>
                        </ul>
                        <p class="mt-3">حداقل شارژ ۱۰۰ هزار تومان است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="non-stop" data-keywords="اعتبار تمام شده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر موجودی کیف پول کافی نباشد چه می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در صورت کمبود موجودی، <strong>عوارض در قالب بدهی ثبت می‌شود</strong> و باید ظرف ۱۵ روز آن را پرداخت کنید. سیستم پیامک هشدار ارسال می‌کند تا کیف پول را شارژ کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="non-stop" data-keywords="مزایا صرفه‌جویی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مزایای پرداخت بدون توقف چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>مزایای اصلی:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>صرفه‌جویی زمان:</strong> بدون توقف در صف</li>
                            <li><strong>کاهش مصرف سوخت:</strong> عدم ترمزگیری</li>
                            <li><strong>آسایش بیشتر:</strong> سفر روان و بدون استرس</li>
                            <li><strong>محیط زیست:</strong> کاهش آلودگی هوا</li>
                            <li><strong>عدم جریمه:</strong> پرداخت خودکار</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 12: امنیت -->
        <div class="faq-category" data-category="security">
            <div class="bg-gradient-to-r from-amber-600 to-amber-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    امنیت اطلاعات
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="security" data-keywords="امنیت پرداخت SSL">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">امنیت پرداخت‌ها چگونه تضمین می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>تضمین‌های امنیتی:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>SSL 256 بیتی:</strong> رمزنگاری کامل اطلاعات</li>
                            <li><strong>PCI DSS:</strong> استاندارد بین‌المللی امنیت</li>
                            <li><strong>OTP:</strong> رمز یکبار مصرف برای تأیید</li>
                            <li><strong>عدم ذخیره:</strong> اطلاعات کارت ذخیره نمی‌شود</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="کلاهبرداری تشخیص">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه سایت‌های جعلی را تشخیص دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>نشانه‌های سایت معتبر:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>آدرس HTTPS:</strong> قفل سبز در مرورگر</li>
                            <li><strong>دامنه رسمی:</strong> بررسی دامنه صحیح</li>
                            <li><strong>مجوزهای رسمی:</strong> وجود مجوز بانک مرکزی</li>
                            <li><strong>طراحی حرفه‌ای:</strong> ظاهر متناسب و بدون خطا</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="حریم خصوصی داده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اطلاعات شخصی من امن است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، حریم خصوصی شما محفوظ است.</strong> ما تنها شماره پلاک برای استعلام استفاده می‌کنیم و هیچ اطلاعات شخصی دیگری ذخیره نمی‌کنیم. سیاست حفظ حریم خصوصی ما شفاف و قابل دسترس است.
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 13: موارد خاص -->
        <div class="faq-category" data-category="additional">
            <div class="bg-gradient-to-r from-violet-600 to-violet-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    موارد خاص و متفرقه
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="additional" data-keywords="خودروی کرایه‌ای">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر خودرو کرایه‌ای داشتم عوارض با کیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        عوارض خودروی کرایه‌ای <strong>با مالک اصلی خودرو</strong> ثبت می‌شود. شرکت‌های اجاره خودرو معمولاً این موضوع را در قرارداد ذکر کرده و هزینه را از مشتری دریافت می‌کنند. قبل از اجاره شرایط را بررسی کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="تاکسی آنلاین اسنپ">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">عوارض سفرهای تاکسی آنلاین چه می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        عوارض سفرهای تاکسی آنلاین (اسنپ، تپ‌سی) <strong>بر عهده راننده</strong> است. شرکت‌های تاکسی آنلاین معمولاً سیستم پرداخت خودکار دارند یا هزینه را از راننده کسر می‌کنند. مسافر نگرانی از بابت عوارض ندارد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="خودروی فروخته شده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">خودرو را فروخته‌ام اما هنوز عوارض برایم می‌آید؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        این مشکل به دلیل <strong>عدم انتقال سند</strong> است. تا زمان انتقال رسمی سند، عوارض با مالک قبلی ثبت می‌شود. با مراجعه به راهور و انجام انتقال سند، این مشکل حل خواهد شد. عوارض قبلی باید توسط شما پرداخت شود.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="خودروی دولتی سازمانی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">خودروهای دولتی و سازمانی نیز عوارض پرداخت می‌کنند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، تمامی خودروها</strong> شامل دولتی، سازمانی، نیروهای مسلح و حتی خودروهای دیپلماتیک نیز مشمول پرداخت عوارض هستند. تنها معافیت‌های محدودی برای موارد خاص اورژانس و امدادی وجود دارد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="آینده توسعه طرح">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا عوارض الکترونیکی به سایر شهرها توسعه می‌یابد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، برنامه جامعی برای توسعه</strong> عوارض الکترونیکی به تمام شهرهای بالای ۵۰۰ هزار نفر و آزادراه‌های باقی‌مانده وجود دارد. هدف ایجاد شبکه یکپارچه ملی و مدیریت هوشمند ترافیک در سراسر کشور است.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for FAQ functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('faq-search');
    const categoryButtons = document.querySelectorAll('.faq-category-btn');
    const faqItems = document.querySelectorAll('.faq-item');
    const faqQuestions = document.querySelectorAll('.faq-question');
    const resultsCounter = document.getElementById('faq-results');
    const resultsCount = document.getElementById('results-count');
    const noResults = document.getElementById('no-results');
    const faqContainer = document.getElementById('faq-container');
    
    let currentCategory = 'all';

    // FAQ Question Toggle
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const answer = this.nextElementSibling;
            const chevron = this.querySelector('.faq-chevron');
            
            if (answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                answer.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        });
    });

    // Category Filter
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            categoryButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });
            
            // Add active class to clicked button
            this.classList.add('active', 'bg-blue-600', 'text-white');
            this.classList.remove('bg-gray-100', 'text-gray-700');
            
            currentCategory = this.dataset.category;
            performSearch();
        });
    });

    // Search functionality
    searchInput.addEventListener('input', performSearch);

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        faqItems.forEach(item => {
            const category = item.dataset.category;
            const keywords = item.dataset.keywords.toLowerCase();
            const questionText = item.querySelector('.faq-question h4').textContent.toLowerCase();
            const answerText = item.querySelector('.faq-answer').textContent.toLowerCase();

            // Check category filter
            const categoryMatch = currentCategory === 'all' || category === currentCategory;

            // Check search term
            const searchMatch = searchTerm === '' || 
                keywords.includes(searchTerm) || 
                questionText.includes(searchTerm) || 
                answerText.includes(searchTerm);

            if (categoryMatch && searchMatch) {
                item.style.display = 'block';
                visibleCount++;
                
                // Highlight search terms
                if (searchTerm !== '') {
                    highlightText(item.querySelector('.faq-question h4'), searchTerm);
                }
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide categories based on visible items
        document.querySelectorAll('.faq-category').forEach(category => {
            const visibleItems = category.querySelectorAll('.faq-item[style*="block"], .faq-item:not([style*="none"])');
            if (visibleItems.length === 0 && (searchTerm !== '' || currentCategory !== 'all')) {
                category.style.display = 'none';
            } else {
                category.style.display = 'block';
            }
        });

        // Update results counter
        if (searchTerm !== '') {
            resultsCounter.classList.remove('hidden');
            resultsCount.textContent = visibleCount;
        } else {
            resultsCounter.classList.add('hidden');
        }

        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
            faqContainer.classList.add('hidden');
        } else {
            noResults.classList.add('hidden');
            faqContainer.classList.remove('hidden');
        }
    }

    function highlightText(element, searchTerm) {
        const text = element.textContent;
        const regex = new RegExp(`(${searchTerm})`, 'gi');
        element.innerHTML = text.replace(regex, '<mark class="bg-yellow-200">$1</mark>');
    }

    // Global clear search function
    window.clearSearch = function() {
        searchInput.value = '';
        currentCategory = 'all';
        
        // Reset category buttons
        categoryButtons.forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-700');
        });
        document.querySelector('[data-category="all"]').classList.add('active', 'bg-blue-600', 'text-white');
        document.querySelector('[data-category="all"]').classList.remove('bg-gray-100', 'text-gray-700');
        
        performSearch();
    };
});
</script>

<style>
.faq-question:hover h4 {
    color: #2563eb;
}

.faq-chevron {
    transition: transform 0.3s ease;
}

.faq-item[style*="none"] {
    display: none !important;
}

mark {
    padding: 2px 4px;
    border-radius: 3px;
}

@media (max-width: 640px) {
    .faq-category-btn {
        font-size: 12px;
        padding: 8px 12px;
    }
    
    .grid.grid-cols-1.md\\:grid-cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>