{{-- Comprehensive Searchable FAQ Section for National Code Inquiry Service --}}
{{-- سوالات متداول جامع و قابل جستجو برای خدمات استعلام کد ملی --}}

<!-- Enhanced FAQ Section with Search and Categories -->
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
                بیش از <strong>۶۵ سوال و پاسخ تخصصی</strong> درباره استعلام کد ملی، احراز هویت، و سامانه‌های رسمی کشور
            </p>
            
            <!-- Advanced Search with Suggestions -->
            <div class="mt-8 max-w-2xl mx-auto">
                <div class="relative">
                    <input 
                        type="text" 
                        id="advanced-faq-search" 
                        placeholder="جستجوی پیشرفته در سوالات..."
                        class="w-full px-6 py-4 text-lg border-2 border-purple-200 rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-right shadow-lg"
                    >
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <div id="search-suggestions" class="hidden mt-2 bg-white rounded-xl shadow-lg border border-gray-200"></div>
            </div>
        </div>
    </div>

    <!-- FAQ Search and Filter System -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-8 shadow-lg">
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

            <!-- Category Filter Buttons -->
            <div class="flex flex-wrap gap-2">
                <button class="faq-category-btn active px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium transition-colors" data-category="all">
                    همه موضوعات (۶۲)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="basics">
                    مبانی کد ملی (۱۱)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="verification">
                    بررسی صحت (۹)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="government">
                    سامانه‌های دولتی (۱۱)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="banking">
                    خدمات بانکی (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="legal">
                    مسائل حقوقی (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="security">
                    امنیت و حریم خصوصی (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="technical">
                    مسائل فنی (۸)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="business">
                    کاربردهای تجاری (۷)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="special">
                    موارد خاص (۱۰)
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

        <!-- Category 1: مبانی کد ملی (National ID Basics) -->
        <div class="faq-category" data-category="basics">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    مبانی و اصول کد ملی ایران
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="basics" data-keywords="کد ملی چیست تعریف">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کد ملی ایران چیست و چه کاربردی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>کد ملی ایران <strong>شناسه یکتای ۱۰ رقمی</strong> است که از سال ۱۳۵۹ برای تمامی اتباع ایرانی صادر می‌شود. این کد توسط سازمان ثبت احوال کشور تحت نظارت وزارت کشور اداره می‌شود و شامل اطلاعات زیر است:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>سه رقم اول: کد محل تولد</li>
                            <li>شش رقم میانی: شماره سریال منحصر به فرد</li>
                            <li>رقم آخر: رقم کنترلی برای تأیید صحت</li>
                        </ul>
                        <p class="mt-3">کاربردهای اصلی شامل احراز هویت در بانک‌ها، دستگاه‌های دولتی، انعقاد قراردادها و دریافت خدمات است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="basics" data-keywords="ساختار کد ملی ۱۰ رقم">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">ساختار ۱۰ رقمی کد ملی چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        کد ملی ایران دارای ساختار منطقی و ریاضی مشخصی است:
                        <div class="bg-gray-50 rounded-lg p-4 mt-3 font-mono text-center text-lg">
                            XXX - XXXXXX - X
                        </div>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>سه رقم اول (XXX):</strong> نشان‌دهنده محل تولد طبق کدهای استاندارد</li>
                            <li><strong>شش رقم میانی (XXXXXX):</strong> شماره سریال فرد در آن منطقه</li>
                            <li><strong>رقم آخر (X):</strong> رقم کنترلی محاسبه شده بر اساس الگوریتم</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="basics" data-keywords="تاریخچه کد ملی 1359">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کد ملی از چه زمانی در ایران راه‌اندازی شد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        سیستم کد ملی ایران <strong>از سال ۱۳۵۹ (۱۹۸۰)</strong> به‌طور رسمی راه‌اندازی شد. این طرح با هدف ایجاد سامانه یکپارچه شناسایی اتباع کشور و رفع مشکلات ناشی از تشابه نام‌ها کلید خورد. قبل از این تاریخ، شناسایی افراد بر اساس شماره شناسنامه انجام می‌شد که دارای محدودیت‌هایی بود.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="basics" data-keywords="الگوریتم محاسبه رقم کنترلی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">الگوریتم محاسبه رقم کنترلی چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        محاسبه رقم کنترلی کد ملی بر اساس فرمول زیر انجام می‌شود:
                        <div class="bg-blue-50 rounded-lg p-4 mt-3">
                            <ol class="list-decimal mr-6 space-y-2 text-sm">
                                <li>هر یک از ۹ رقم اول را در موقعیت خود (۱۰ تا ۲) ضرب کنید</li>
                                <li>حاصل‌ضرب‌ها را جمع کنید</li>
                                <li>مجموع را بر ۱۱ تقسیم کنید</li>
                                <li>اگر باقیمانده < ۲ باشد، همان عدد رقم کنترلی است</li>
                                <li>اگر باقیمانده ≥ ۲ باشد، از ۱۱ کم کنید</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="basics" data-keywords="محل تولد کد شهر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از روی کد ملی، محل تولد را تشخیص دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        سه رقم اول کد ملی نشان‌دهنده محل تولد است. برخی از کدهای رایج:
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-3 text-sm">
                            <div class="bg-gray-100 p-2 rounded">تهران: ۰۰۱-۴۹۹</div>
                            <div class="bg-gray-100 p-2 rounded">اصفهان: ۵۰۰-۵۹۹</div>
                            <div class="bg-gray-100 p-2 rounded">مشهد: ۶۰۰-۶۴۹</div>
                            <div class="bg-gray-100 p-2 rounded">شیراز: ۶۵۰-۶۹۹</div>
                            <div class="bg-gray-100 p-2 rounded">تبریز: ۷۰۰-۷۴۹</div>
                            <div class="bg-gray-100 p-2 rounded">اهواز: ۷۵۰-۷۹۹</div>
                        </div>
                        <p class="mt-3 text-sm text-gray-600">توجه: این کدها بر اساس محل تولد است، نه محل سکونت فعلی.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="basics" data-keywords="کارت ملی صدور">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کارت ملی و کد ملی چه تفاوتی دارند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>کد ملی</strong> همان شماره ۱۰ رقمی یکتای هر شخص است، در حالی که <strong>کارت ملی</strong> مدرک فیزیکی (پلاستیکی) است که این کد روی آن درج شده است.
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>کد ملی:</strong> شماره ۱۰ رقمی ثابت و غیرقابل تغییر</li>
                            <li><strong>کارت ملی:</strong> مدرک شناسایی قابل تعویض و تمدید</li>
                            <li><strong>کارت ملی هوشمند:</strong> نسل جدید با ویژگی‌های امنیتی پیشرفته</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="basics" data-keywords="کد ملی یکتا تکراری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا ممکن است دو نفر کد ملی یکسان داشته باشند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، هیچ‌گاه دو نفر نمی‌توانند کد ملی یکسان داشته باشند.</strong> سیستم طوری طراحی شده که هر کد ملی منحصر به فرد باشد. در صورت بروز چنین مشکلی (که بسیار نادر است)، سازمان ثبت احوال فوراً اقدام به اصلاح و تعیین کد جدید می‌کند. این یکی از اصول اساسی سیستم شناسایی کشور است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="basics" data-keywords="اتباع خارجی کد ملی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اتباع خارجی آیا کد ملی ایرانی دارند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        اتباع خارجی در موارد خاص ممکن است کد ملی ایرانی دریافت کنند:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>متولدین ایران:</strong> اتباع خارجی متولد ایران</li>
                            <li><strong>اقامت دائم:</strong> دارندگان اقامت دائم</li>
                            <li><strong>ازدواج:</strong> همسران ایرانی‌ها در شرایط خاص</li>
                            <li><strong>پناهندگان:</strong> افراد دارای وضعیت قانونی پناهندگی</li>
                        </ul>
                        <p class="mt-3 text-sm">در غیر این موارد، از شماره اقامت یا پاسپورت استفاده می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="basics" data-keywords="کد ملی نوزاد فرزند">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کد ملی نوزادان چگونه صادر می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        فرآیند صدور کد ملی برای نوزادان:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>پس از تولد:</strong> حداکثر ظرف ۱۵ روز پس از تولد</li>
                            <li><strong>مدارک لازم:</strong> گواهی تولد، شناسنامه والدین</li>
                            <li><strong>مراجعه به ثبت احوال:</strong> والدین یا نماینده قانونی</li>
                            <li><strong>صدور فوری:</strong> در صورت ضرورت پزشکی</li>
                            <li><strong>ثبت در سامانه:</strong> اتصال به پایگاه داده ملی</li>
                        </ul>
                        <div class="bg-green-50 p-3 rounded mt-3">
                            <p class="text-green-800 text-sm"><strong>نکته:</strong> کد ملی از بدو تولد تا پایان عمر ثابت باقی می‌ماند.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="basics" data-keywords="کد ملی تاریخچه ایجاد">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سیستم کد ملی در ایران چه زمانی شروع شد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>تاریخچه سیستم کد ملی در ایران:</strong>
                        <div class="space-y-3 mt-3">
                            <div class="bg-blue-50 p-3 rounded">
                                <strong>۱۳۴۲:</strong> آغاز طراحی سیستم شناسایی
                            </div>
                            <div class="bg-blue-50 p-3 rounded">
                                <strong>۱۳۵۵:</strong> تصویب قانون ثبت احوال و کد ملی
                            </div>
                            <div class="bg-blue-50 p-3 rounded">
                                <strong>۱۳۶۰:</strong> شروع صدور کدهای ملی
                            </div>
                            <div class="bg-blue-50 p-3 rounded">
                                <strong>۱۳۷۰:</strong> تکمیل سیستم کامپیوتری
                            </div>
                            <div class="bg-green-50 p-3 rounded">
                                <strong>امروز:</strong> بیش از ۸۵ میلیون کد ملی فعال
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 2: بررسی صحت (Verification) -->
        <div class="faq-category" data-category="verification">
            <div class="bg-gradient-to-r from-green-600 to-emerald-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    بررسی صحت و اعتبارسنجی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="verification" data-keywords="بررسی صحت کد ملی آنلاین">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه صحت کد ملی را آنلاین بررسی کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای بررسی آنلاین صحت کد ملی چند روش وجود دارد:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>سامانه‌های دولتی:</strong> my.gov.ir و sabteahval.ir</li>
                            <li><strong>ابزارهای آنلاین:</strong> بررسی الگوریتم ریاضی</li>
                            <li><strong>سامانه پیشخوانک:</strong> استعلام سریع و دقیق</li>
                            <li><strong>محاسبه دستی:</strong> بر اساس فرمول رسمی</li>
                        </ul>
                        <div class="bg-green-50 p-4 rounded-lg mt-3">
                            <p class="text-green-800 text-sm"><strong>نکته:</strong> بررسی الگوریتم ریاضی فقط صحت ساختار کد را نشان می‌دهد، نه وجود واقعی فرد.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="verification" data-keywords="کد ملی جعلی تشخیص">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه کد ملی جعلی را تشخیص دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای تشخیص کد ملی جعلی این مراحل را دنبال کنید:
                        <div class="space-y-3 mt-3">
                            <div class="bg-red-50 p-3 rounded">
                                <h5 class="font-bold text-red-800">علائم جعلی بودن:</h5>
                                <ul class="text-sm text-red-700 mt-1 space-y-1">
                                    <li>• رقم کنترلی اشتباه</li>
                                    <li>• تکرار اعداد یکسان (مثل ۰۰۰۰۰۰۰۰۰۰)</li>
                                    <li>• شروع با اعداد غیرمجاز</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 p-3 rounded">
                                <h5 class="font-bold text-green-800">روش‌های تأیید:</h5>
                                <ul class="text-sm text-green-700 mt-1 space-y-1">
                                    <li>• استعلام از سامانه‌های رسمی</li>
                                    <li>• بررسی الگوریتم ریاضی</li>
                                    <li>• مطابقت با مدارک هویتی</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="verification" data-keywords="کد ملی نامعتبر خطا">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چرا کد ملی من را نامعتبر نشان می‌دهد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        دلایل مختلفی برای نمایش کد ملی به عنوان نامعتبر وجود دارد:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>خطای تایپی:</strong> اشتباه در وارد کردن اعداد</li>
                            <li><strong>رقم کنترلی نادرست:</strong> عدم تطبیق با فرمول</li>
                            <li><strong>کد جعلی:</strong> استفاده از کد ساختگی</li>
                            <li><strong>مشکل سیستمی:</strong> خطای موقت در سامانه</li>
                            <li><strong>کد قدیمی:</strong> کدهای صادر شده قبل از ۱۳۵۹</li>
                        </ul>
                        <p class="mt-3 text-amber-700 bg-amber-50 p-3 rounded">
                            <strong>توصیه:</strong> در صورت اطمینان از صحت کد، با سازمان ثبت احوال تماس بگیرید.
                        </p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="verification" data-keywords="دستی محاسبه رقم کنترلی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه رقم کنترلی را به‌صورت دستی محاسبه کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مثال عملی برای کد ملی ۰۰۷۳۱۵۱۸۹X:
                        <div class="bg-gray-50 p-4 rounded-lg mt-3 font-mono">
                            <div class="space-y-2 text-sm">
                                <div>۰×۱۰ + ۰×۹ + ۷×۸ + ۳×۷ + ۱×۶ + ۵×۵ + ۱×۴ + ۸×۳ + ۹×۲</div>
                                <div>= ۰ + ۰ + ۵۶ + ۲۱ + ۶ + ۲۵ + ۴ + ۲۴ + ۱۸ = ۱۵۴</div>
                                <div>۱۵۴ ÷ ۱۱ = ۱۴ باقیمانده ۰</div>
                                <div class="text-green-600 font-bold">چون باقیمانده < ۲ است، رقم کنترلی = ۰</div>
                            </div>
                        </div>
                        <p class="mt-3 text-gray-600">پس کد ملی صحیح: ۰۰۷۳۱۵۱۸۹۰</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="verification" data-keywords="ابزار بررسی کد ملی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">بهترین ابزارهای آنلاین برای بررسی کد ملی کدامند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <div class="space-y-4">
                            <div class="border-r-4 border-green-500 pr-4">
                                <h5 class="font-bold text-green-700">سامانه‌های رسمی (بالاترین اعتبار)</h5>
                                <ul class="text-sm mt-2 space-y-1">
                                    <li>• sabteahval.ir - سازمان ثبت احوال</li>
                                    <li>• my.gov.ir - درگاه ملی خدمات</li>
                                    <li>• tax.gov.ir - سازمان امور مالیاتی</li>
                                </ul>
                            </div>
                            <div class="border-r-4 border-blue-500 pr-4">
                                <h5 class="font-bold text-blue-700">ابزارهای تخصصی</h5>
                                <ul class="text-sm mt-2 space-y-1">
                                    <li>• پیشخوانک - استعلام جامع</li>
                                    <li>• ابزارهای بررسی الگوریتم</li>
                                    <li>• سامانه‌های احراز هویت</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="verification" data-keywords="سرعت استعلام کد ملی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">استعلام کد ملی چقدر زمان می‌برد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        سرعت استعلام بر اساس نوع سرویس متفاوت است:
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div class="bg-green-50 p-3 rounded">
                                <h5 class="font-bold text-green-800">بررسی الگوریتم</h5>
                                <p class="text-sm text-green-700">فوری (کمتر از ۱ ثانیه)</p>
                            </div>
                            <div class="bg-blue-50 p-3 rounded">
                                <h5 class="font-bold text-blue-800">استعلام آنلاین</h5>
                                <p class="text-sm text-blue-700">۲-۵ ثانیه</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded">
                                <h5 class="font-bold text-purple-800">سامانه‌های دولتی</h5>
                                <p class="text-sm text-purple-700">۵-۱۵ ثانیه</p>
                            </div>
                            <div class="bg-orange-50 p-3 rounded">
                                <h5 class="font-bold text-orange-800">استعلام حضوری</h5>
                                <p class="text-sm text-orange-700">چند دقیقه تا چند ساعت</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="verification" data-keywords="انبوه بررسی چندین کد ملی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم چندین کد ملی را همزمان بررسی کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، امکان بررسی انبوه کد ملی وجود دارد:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>سامانه‌های تخصصی:</strong> قابلیت آپلود فایل Excel</li>
                            <li><strong>API های تجاری:</strong> برای توسعه‌دهندگان</li>
                            <li><strong>نرم‌افزارهای اختصاصی:</strong> برای سازمان‌ها</li>
                            <li><strong>محدودیت‌ها:</strong> رعایت قوانین حریم خصوصی</li>
                        </ul>
                        <div class="bg-amber-50 p-3 rounded mt-3">
                            <p class="text-amber-800 text-sm"><strong>هشدار:</strong> برای بررسی انبوه، مجوزهای قانونی لازم را کسب کنید.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="verification" data-keywords="اعتبار کد ملی موقت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کد ملی موقت چیست و چه اعتباری دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>کد ملی موقت</strong> برای افرادی صادر می‌شود که در انتظار تکمیل مدارک هستند:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>کاربرد:</strong> دریافت خدمات ضروری در دوران انتظار</li>
                            <li><strong>مدت اعتبار:</strong> حداکثر ۶ ماه</li>
                            <li><strong>قابلیت تمدید:</strong> در شرایط خاص</li>
                            <li><strong>محدودیت‌ها:</strong> برخی خدمات بانکی و دولتی</li>
                            <li><strong>تبدیل به دائم:</strong> پس از تکمیل مدارک</li>
                        </ul>
                        <div class="bg-yellow-50 p-3 rounded mt-3">
                            <p class="text-yellow-800 text-sm"><strong>توجه:</strong> کد موقت باید در اسرع وقت به کد دائم تبدیل شود.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="verification" data-keywords="بررسی تطبیق کد ملی نام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه تطبیق کد ملی با نام و نام خانوادگی را بررسی کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای بررسی تطبیق کد ملی با اطلاعات شخصی:
                        <div class="space-y-3 mt-3">
                            <div class="bg-blue-50 p-3 rounded">
                                <strong>روش‌های رسمی:</strong>
                                <ul class="list-disc mr-4 mt-2">
                                    <li>سامانه my.gov.ir</li>
                                    <li>درگاه ثبت احوال</li>
                                    <li>سامانه بانک‌ها</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 p-3 rounded">
                                <strong>اطلاعات قابل تطبیق:</strong>
                                <ul class="list-disc mr-4 mt-2">
                                    <li>نام و نام خانوادگی</li>
                                    <li>تاریخ تولد</li>
                                    <li>نام پدر</li>
                                    <li>محل تولد</li>
                                </ul>
                            </div>
                        </div>
                        <div class="bg-red-50 p-3 rounded mt-3">
                            <p class="text-red-800 text-sm"><strong>امنیت:</strong> هرگز اطلاعات کد ملی را در سایت‌های غیرمعتبر وارد نکنید.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 3: سامانه‌های دولتی (Government Systems) -->
        <div class="faq-category" data-category="government">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    سامانه‌های دولتی و رسمی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="government" data-keywords="my.gov.ir درگاه ملی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه my.gov.ir چیست و چه خدماتی ارائه می‌دهد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>درگاه ملی خدمات هوشمند</strong> (my.gov.ir) پنجره واحد دسترسی به تمامی خدمات الکترونیک دولت است:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>احراز هویت یکپارچه:</strong> ورود با یک حساب کاربری</li>
                            <li><strong>خدمات ثبت احوال:</strong> گواهی‌ها و مدارک</li>
                            <li><strong>خدمات مالیاتی:</strong> اظهارنامه و پرداخت</li>
                            <li><strong>خدمات قضایی:</strong> استعلام پرونده‌ها</li>
                            <li><strong>خدمات اجتماعی:</strong> بیمه و یارانه</li>
                            <li><strong>خدمات آموزشی:</strong> مدارس و دانشگاه‌ها</li>
                        </ul>
                        <p class="mt-3 text-blue-700 bg-blue-50 p-3 rounded">
                            برای استفاده، کافی است با کد ملی و شماره همراه ثبت‌نام کنید.
                        </p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="government" data-keywords="sabteahval.ir ثبت احوال">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه sabteahval.ir چه امکاناتی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        سازمان ثبت احوال کشور امکانات زیر را فراهم کرده:
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div class="bg-green-50 p-3 rounded">
                                <h5 class="font-bold text-green-800">استعلام اطلاعات</h5>
                                <ul class="text-sm text-green-700 mt-1 space-y-1">
                                    <li>• وضعیت حیات</li>
                                    <li>• تاریخ تولد</li>
                                    <li>• محل تولد</li>
                                    <li>• وضعیت تأهل</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 p-3 rounded">
                                <h5 class="font-bold text-blue-800">صدور مدارک</h5>
                                <ul class="text-sm text-blue-700 mt-1 space-y-1">
                                    <li>• گواهی تولد</li>
                                    <li>• گواهی فوت</li>
                                    <li>• گواهی ازدواج</li>
                                    <li>• گواهی طلاق</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="government" data-keywords="tax.gov.ir مالیات">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه tax.gov.ir برای چه استفاده می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>سامانه اداره کل امور مالیاتی</strong> خدمات زیر را ارائه می‌دهد:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>استعلام کد ملی:</strong> تأیید هویت مؤدیان</li>
                            <li><strong>صدور کد اقتصادی:</strong> برای اشخاص حقوقی</li>
                            <li><strong>اظهارنامه مالیاتی:</strong> تسلیم آنلاین</li>
                            <li><strong>پرداخت مالیات:</strong> آنلاین و حضوری</li>
                            <li><strong>پیگیری پرونده:</strong> وضعیت پرونده‌ها</li>
                        </ul>
                        <p class="mt-3 text-orange-700 bg-orange-50 p-3 rounded">
                            برای دسترسی، نیاز به ثبت‌نام با کد ملی و شناسه اقتصادی دارید.
                        </p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="government" data-keywords="sejam.ir سجام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه sejam.ir چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>سجام (سامانه الکترونیک احراز هویت)</strong> مرکز سپرده گذاری اوراق بهادار:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>احراز هویت الکترونیک:</strong> برای بازار سرمایه</li>
                            <li><strong>افتتاح کد بورسی:</strong> آنلاین و سریع</li>
                            <li><strong>تأیید اطلاعات:</strong> از سامانه ثبت احوال</li>
                            <li><strong>مدیریت پرتفو:</strong> کدهای بورسی</li>
                            <li><strong>گزارش‌گیری:</strong> وضعیت سهام و اوراق</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="government" data-keywords="احراز هویت دولتی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">احراز هویت در سامانه‌های دولتی چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        فرآیند احراز هویت در سامانه‌های دولتی به چند روش انجام می‌شود:
                        <div class="space-y-3 mt-3">
                            <div class="bg-blue-50 p-3 rounded">
                                <h5 class="font-bold text-blue-800">مرحله اول - شناسایی</h5>
                                <p class="text-sm text-blue-700">وارد کردن کد ملی و اطلاعات پایه</p>
                            </div>
                            <div class="bg-green-50 p-3 rounded">
                                <h5 class="font-bold text-green-800">مرحله دوم - تأیید</h5>
                                <p class="text-sm text-green-700">ارسال کد تأیید به شماره همراه</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded">
                                <h5 class="font-bold text-purple-800">مرحله سوم - دسترسی</h5>
                                <p class="text-sm text-purple-700">ایجاد حساب کاربری و دسترسی به خدمات</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="government" data-keywords="کارت ملی هوشمند">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کارت ملی هوشمند چه امکاناتی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>کارت ملی هوشمند</strong> نسل جدید کارت‌های شناسایی با ویژگی‌های پیشرفته:
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <h5 class="font-bold text-gray-800 mb-2">ویژگی‌های امنیتی</h5>
                                <ul class="text-sm space-y-1">
                                    <li>• تراشه الکترونیک</li>
                                    <li>• رمزگذاری اطلاعات</li>
                                    <li>• احراز هویت بیومتریک</li>
                                    <li>• مقاوم در برابر جعل</li>
                                </ul>
                            </div>
                            <div>
                                <h5 class="font-bold text-gray-800 mb-2">کاربردها</h5>
                                <ul class="text-sm space-y-1">
                                    <li>• ورود به سامانه‌های دولتی</li>
                                    <li>• خدمات بانکی</li>
                                    <li>• رأی‌گیری الکترونیک</li>
                                    <li>• تردد امن</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="government" data-keywords="ثنا sana احراز هویت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه ثنا (Sana) چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>سامانه ثنا</strong> سیستم احراز هویت قوه قضائیه است:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>احراز هویت قضایی:</strong> برای دادگاه‌ها و دادسرای‌ها</li>
                            <li><strong>تأیید وکلا:</strong> احراز هویت وکلای دادگستری</li>
                            <li><strong>مدیریت پرونده:</strong> دسترسی آنلاین به پرونده‌ها</li>
                            <li><strong>ابلاغ الکترونیک:</strong> دریافت احضاریه‌ها</li>
                            <li><strong>استعلام قضایی:</strong> سوابق و احکام</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="government" data-keywords="پست ملی ehraz.post.ir">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه احراز نشانی پست ملی چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>سامانه احراز نشانی شرکت ملی پست</strong> برای تأیید آدرس‌ها:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>تأیید آدرس:</strong> بررسی صحت نشانی پستی</li>
                            <li><strong>کد پستی:</strong> تشخیص کد پستی ۱۰ رقمی</li>
                            <li><strong>استاندارسازی آدرس:</strong> تبدیل به فرمت استاندارد</li>
                            <li><strong>خدمات بانکی:</strong> تأیید آدرس برای حساب‌ها</li>
                            <li><strong>تجارت الکترونیک:</strong> تأیید آدرس ارسال</li>
                        </ul>
                        <p class="mt-3 text-gray-600 text-sm">این سامانه برای جلوگیری از کلاهبرداری‌های آدرس طراحی شده است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="government" data-keywords="مشکلات سامانه‌های دولتی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مشکلات رایج در سامانه‌های دولتی چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مشکلات رایج و راه‌حل‌های آن‌ها:
                        <div class="space-y-3 mt-3">
                            <div class="bg-red-50 p-3 rounded">
                                <h5 class="font-bold text-red-800">مشکلات رایج</h5>
                                <ul class="text-sm text-red-700 mt-1 space-y-1">
                                    <li>• عدم دریافت کد تأیید</li>
                                    <li>• بطء سیستم در ساعات پیک</li>
                                    <li>• عدم تطبیق اطلاعات</li>
                                    <li>• خطاهای فنی موقت</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 p-3 rounded">
                                <h5 class="font-bold text-green-800">راه‌حل‌ها</h5>
                                <ul class="text-sm text-green-700 mt-1 space-y-1">
                                    <li>• بررسی شماره همراه</li>
                                    <li>• تلاش در ساعات غیرپیک</li>
                                    <li>• تماس با پشتیبانی</li>
                                    <li>• مراجعه حضوری در صورت نیاز</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="government" data-keywords="سامانه شهروند نسیم آنلاین">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه شهروندان (نسیم) چه خدماتی ارائه می‌دهد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        سامانه نسیم (NASIM) مجموعه جامعی از خدمات دولتی الکترونیک ارائه می‌دهد:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>خدمات ثبت احوال:</strong> استعلام کد ملی و تغییر اطلاعات</li>
                            <li><strong>خدمات قضائی:</strong> پرونده‌های حقوقی و کیفری</li>
                            <li><strong>خدمات مالیاتی:</strong> ارسال اظهارنامه و استعلام</li>
                            <li><strong>بیمه و درمان:</strong> خدمات تأمین اجتماعی</li>
                            <li><strong>خدمات شهری:</strong> پرداخت عوارض و جرائم</li>
                        </ul>
                        <div class="bg-blue-50 p-3 rounded mt-3">
                            <p class="text-blue-800 text-sm"><strong>دسترسی:</strong> my.gov.ir و اپلیکیشن موبایل دولت من</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="government" data-keywords="سامانه رسمی تأیید هویت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کدام سامانه‌ها برای تأیید هویت رسمی هستند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>سامانه‌های رسمی تأیید هویت در ایران:</strong>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div class="bg-green-50 p-3 rounded">
                                <h5 class="font-bold text-green-800">سامانه‌های دولتی</h5>
                                <ul class="text-sm text-green-700 mt-1 space-y-1">
                                    <li>• سامانه ثبت احوال (sabteahval.ir)</li>
                                    <li>• درگاه ملی خدمات الکترونیک (my.gov.ir)</li>
                                    <li>• سامانه تأمین اجتماعی</li>
                                </ul>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded">
                                <h5 class="font-bold text-yellow-800">هشدارهای امنیتی</h5>
                                <ul class="text-sm text-yellow-700 mt-1 space-y-1">
                                    <li>• سایت‌های غیررسمی استفاده نکنید</li>
                                    <li>• https:// و گواهی امنیتی بررسی کنید</li>
                                    <li>• اطلاعات در سایت‌های مشکوک ندهید</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 4: خدمات بانکی (Banking Services) -->
        <div class="faq-category" data-category="banking">
            <div class="bg-gradient-to-r from-green-600 to-teal-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    کاربردهای بانکی و مالی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="banking" data-keywords="KYC احراز هویت بانک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">احراز هویت مشتریان (KYC) در بانک‌ها چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>KYC (Know Your Customer)</strong> فرآیند شناخت و احراز هویت مشتریان در بانک‌ها:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>تأیید هویت:</strong> بررسی کد ملی و مدارک</li>
                            <li><strong>بررسی سوابق:</strong> تاریخچه بانکی و اعتباری</li>
                            <li><strong>ارزیابی ریسک:</strong> تشخیص مشتریان پرخطر</li>
                            <li><strong>مطابقت مقررات:</strong> رعایت قوانین پولشویی</li>
                            <li><strong>به‌روزرسانی مداوم:</strong> نظارت بر فعالیت‌ها</li>
                        </ul>
                        <p class="mt-3 text-blue-700 bg-blue-50 p-3 rounded">
                            کد ملی اصلی‌ترین ابزار KYC در بانک‌های ایران محسوب می‌شود.
                        </p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="banking" data-keywords="افتتاح حساب بانکی کد ملی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">برای افتتاح حساب بانکی چه مداركي لازم است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>مدارک الزامی برای افتتاح حساب:</strong>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div class="bg-blue-50 p-3 rounded">
                                <h5 class="font-bold text-blue-800">اشخاص حقیقی</h5>
                                <ul class="text-sm text-blue-700 mt-1 space-y-1">
                                    <li>• کارت ملی معتبر</li>
                                    <li>• شناسنامه</li>
                                    <li>• عکس شخصی</li>
                                    <li>• فرم درخواست</li>
                                    <li>• نمونه امضا</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 p-3 rounded">
                                <h5 class="font-bold text-green-800">اشخاص حقوقی</h5>
                                <ul class="text-sm text-green-700 mt-1 space-y-1">
                                    <li>• روزنامه رسمی</li>
                                    <li>• اساسنامه</li>
                                    <li>• کد اقتصادی</li>
                                    <li>• معرفی‌نامه مدیران</li>
                                    <li>• آگهی تأسیس</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="banking" data-keywords="شبا IBAN کد ملی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رابطه کد ملی با شماره شبا چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        کد ملی و شماره شبا (IBAN) ارتباط مستقیم دارند:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>یکتا بودن:</strong> هر شماره شبا به یک کد ملی تعلق دارد</li>
                            <li><strong>احراز هویت:</strong> تطبیق نام صاحب حساب</li>
                            <li><strong>انتقال وجه:</strong> تأیید مالکیت قبل از انتقال</li>
                            <li><strong>کنترل تقلب:</strong> جلوگیری از سوءاستفاده</li>
                        </ul>
                        <div class="bg-amber-50 p-3 rounded mt-3">
                            <p class="text-amber-800 text-sm">
                                <strong>نکته:</strong> برای انتقال وجه، بانک‌ها نام صاحب حساب را با کد ملی تطبیق می‌دهند.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="banking" data-keywords="اعتبارسنجی وام تسهیلات">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کد ملی در اعطای وام و تسهیلات چه نقشی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        کد ملی در فرآیند اعطای تسهیلات نقش محوری دارد:
                        <div class="space-y-3 mt-3">
                            <div class="bg-red-50 p-3 rounded">
                                <h5 class="font-bold text-red-800">بررسی سوابق</h5>
                                <ul class="text-sm text-red-700 mt-1 space-y-1">
                                    <li>• تاریخچه بانکی فرد</li>
                                    <li>• وضعیت چک‌های برگشتی</li>
                                    <li>• پرداخت تسهیلات قبلی</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 p-3 rounded">
                                <h5 class="font-bold text-green-800">اعتبارسنجی</h5>
                                <ul class="text-sm text-green-700 mt-1 space-y-1">
                                    <li>• رتبه‌بندی اعتباری</li>
                                    <li>• ظرفیت بازپرداخت</li>
                                    <li>• ضمانت‌های ارائه شده</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="banking" data-keywords="کارت بانکی صدور">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">فرآیند صدور کارت بانکی چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مراحل صدور کارت بانکی:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>درخواست:</strong> تکمیل فرم با کد ملی</li>
                            <li><strong>بررسی سوابق:</strong> کنترل سیاه‌لیست‌ها</li>
                            <li><strong>تأیید هویت:</strong> مطابقت مدارک</li>
                            <li><strong>صدور:</strong> چاپ و آماده‌سازی کارت</li>
                            <li><strong>فعال‌سازی:</strong> تنظیم رمز و محدودیت‌ها</li>
                        </ol>
                        <p class="mt-3 text-purple-700 bg-purple-50 p-3 rounded">
                            معمولاً ۳-۷ روز کاری زمان می‌برد.
                        </p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="banking" data-keywords="مسدود کردن حساب">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">در چه مواردی حساب بانکی مسدود می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        دلایل مسدودی حساب بانکی:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>چک‌های برگشتی:</strong> صدور چک بدون پوشش</li>
                            <li><strong>فعالیت مشکوک:</strong> تراکنش‌های غیرعادی</li>
                            <li><strong>عدم تطبیق KYC:</strong> نقص در مدارک</li>
                            <li><strong>دستور قضایی:</strong> حکم دادگاه</li>
                            <li><strong>عدم پرداخت:</strong> بدهی معوق</li>
                        </ul>
                        <div class="bg-red-50 p-3 rounded mt-3">
                            <p class="text-red-800 text-sm">
                                <strong>راه‌حل:</strong> رفع علت مسدودی و مراجعه به بانک
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 5: مسائل حقوقی (Legal Issues) -->
        <div class="faq-category" data-category="legal">
            <div class="bg-gradient-to-r from-red-600 to-pink-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                    مسائل حقوقی و قانونی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="legal" data-keywords="قانون ثبت احوال">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مبنای قانونی کد ملی در ایران چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مبنای قانونی کد ملی در ایران:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>قانون ثبت احوال مصوب ۱۳۵۵:</strong> اساس قانونی سیستم</li>
                            <li><strong>ماده ۱۲:</strong> الزام داشتن کد ملی یکتا</li>
                            <li><strong>آیین‌نامه اجرایی:</strong> جزئیات صدور و کاربرد</li>
                            <li><strong>بخشنامه‌های تکمیلی:</strong> به‌روزرسانی مقررات</li>
                        </ul>
                        <div class="bg-blue-50 p-3 rounded mt-3">
                            <p class="text-blue-800 text-sm">
                                <strong>مرجع:</strong> سازمان ثبت احوال کشور تحت نظارت وزارت کشور
                            </p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="حریم خصوصی اطلاعات">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">قانون حمایت از اطلاعات شخصی چه حکمی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>قانون حمایت از حریم شخصی افراد در برابر داده‌ها</strong> (مصوب ۱۳۹۹):</p>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>رضایت صریح:</strong> جمع‌آوری با اجازه فرد</li>
                            <li><strong>محدودیت استفاده:</strong> فقط برای اهداف مشخص</li>
                            <li><strong>امنیت داده:</strong> حفاظت در برابر نفوذ</li>
                            <li><strong>حق دسترسی:</strong> بررسی اطلاعات شخصی</li>
                            <li><strong>حق اصلاح:</strong> تصحیح اطلاعات نادرست</li>
                        </ul>
                        <div class="bg-red-50 p-3 rounded mt-3">
                            <p class="text-red-800 text-sm">
                                <strong>مجازات:</strong> جریمه نقدی و انضباطی برای متخلفان
                            </p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="سوءاستفاده کد ملی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سوءاستفاده از کد ملی چه مجازاتی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        انواع سوءاستفاده و مجازات‌های آن:
                        <div class="space-y-3 mt-3">
                            <div class="bg-red-50 p-3 rounded">
                                <h5 class="font-bold text-red-800">انواع سوءاستفاده</h5>
                                <ul class="text-sm text-red-700 mt-1 space-y-1">
                                    <li>• استفاده غیرمجاز در قراردادها</li>
                                    <li>• جعل اطلاعات هویتی</li>
                                    <li>• فروش اطلاعات شخصی</li>
                                    <li>• کلاهبرداری با هویت</li>
                                </ul>
                            </div>
                            <div class="bg-orange-50 p-3 rounded">
                                <h5 class="font-bold text-orange-800">مجازات‌ها</h5>
                                <ul class="text-sm text-orange-700 mt-1 space-y-1">
                                    <li>• جریمه نقدی سنگین</li>
                                    <li>• حبس تا ۶ ماه</li>
                                    <li>• جبران خسارات</li>
                                    <li>• محرومیت از خدمات</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="تغییر کد ملی امکان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا امکان تغییر کد ملی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، کد ملی ثابت و غیرقابل تغییر است.</strong> موارد استثنایی:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>خطای سیستمی:</strong> اصلاح توسط ثبت احوال</li>
                            <li><strong>تکراری شدن:</strong> صدور کد جایگزین (نادر)</li>
                            <li><strong>نقص فنی:</strong> بازنگری در موارد خاص</li>
                        </ul>
                        <div class="bg-amber-50 p-3 rounded mt-3">
                            <p class="text-amber-800 text-sm">
                                <strong>نکته:</strong> حتی در صورت تغییر نام، کد ملی ثابت می‌ماند.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="حقوق شهروندی کد ملی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">حقوق شهروندی در رابطه با کد ملی چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        حقوق اساسی شهروندان:
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div class="bg-green-50 p-3 rounded">
                                <h5 class="font-bold text-green-800">حقوق مثبت</h5>
                                <ul class="text-sm text-green-700 mt-1 space-y-1">
                                    <li>• دریافت کد ملی رایگان</li>
                                    <li>• دسترسی به اطلاعات شخصی</li>
                                    <li>• اصلاح اطلاعات نادرست</li>
                                    <li>• محرمانگی اطلاعات</li>
                                </ul>
                            </div>
                            <div class="bg-blue-50 p-3 rounded">
                                <h5 class="font-bold text-blue-800">حقوق منفی</h5>
                                <ul class="text-sm text-blue-700 mt-1 space-y-1">
                                    <li>• عدم افشای غیرمجاز</li>
                                    <li>• عدم سوءاستفاده</li>
                                    <li>• عدم دسترسی بدون مجوز</li>
                                    <li>• عدم تبعیض</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="شکایت نقض حریم خصوصی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از نقض حریم خصوصی شکایت کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مراحل شکایت از نقض حریم خصوصی:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>جمع‌آوری مدارک:</strong> اسناد و مدارک مثبته</li>
                            <li><strong>شکایت به مرجع ناظر:</strong> حمایت از حقوق مصرف‌کنندگان</li>
                            <li><strong>شکایت قضایی:</strong> دادگاه صالح</li>
                            <li><strong>پیگیری:</strong> مراحل رسیدگی</li>
                        </ol>
                        <div class="bg-blue-50 p-3 rounded mt-3">
                            <p class="text-blue-800 text-sm">
                                <strong>مهلت:</strong> یک سال از زمان اطلاع از نقض
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Additional categories continue with the same pattern... -->
        <!-- For brevity, I'll continue with remaining categories -->

        <!-- Category 6: امنیت و حریم خصوصی (Security and Privacy) -->
        <div class="faq-category" data-category="security">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    امنیت و حریم خصوصی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="security" data-keywords="امنیت کد ملی محافظت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از کد ملی خود محافظت کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        راهکارهای محافظت از کد ملی:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>عدم اشتراک‌گذاری:</strong> در شبکه‌های اجتماعی</li>
                            <li><strong>سایت‌های امن:</strong> بررسی https://</li>
                            <li><strong>مدارک ایمن:</strong> نگهداری در مکان امن</li>
                            <li><strong>کپی محدود:</strong> تهیه کپی فقط در صورت نیاز</li>
                            <li><strong>بررسی دوره‌ای:</strong> کنترل سوءاستفاده</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="کلاهبرداری هویت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">علائم کلاهبرداری با هویت چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        علائم هشداردهنده:
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div class="bg-red-50 p-3 rounded">
                                <h5 class="font-bold text-red-800">علائم مشکوک</h5>
                                <ul class="text-sm text-red-700 mt-1 space-y-1">
                                    <li>• تراکنش‌های ناشناخته</li>
                                    <li>• پیام‌های تأیید غیرمنتظره</li>
                                    <li>• تغییر اطلاعات حساب‌ها</li>
                                    <li>• درخواست‌های مشکوک</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 p-3 rounded">
                                <h5 class="font-bold text-green-800">اقدامات فوری</h5>
                                <ul class="text-sm text-green-700 mt-1 space-y-1">
                                    <li>• تماس با بانک</li>
                                    <li>• تغییر رمزهای عبور</li>
                                    <li>• شکایت قضایی</li>
                                    <li>• مراقبت بیشتر</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="سایت امن تشخیص">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه سایت‌های امن را تشخیص دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        ویژگی‌های سایت‌های امن:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>قفل امنیتی:</strong> نماد HTTPS در مرورگر</li>
                            <li><strong>دامنه رسمی:</strong> آدرس‌های .gov.ir، .ir معتبر</li>
                            <li><strong>گواهی SSL:</strong> تأیید اعتبار سایت</li>
                            <li><strong>طراحی حرفه‌ای:</strong> ظاهر مناسب و بدون خطا</li>
                            <li><strong>اطلاعات تماس:</strong> آدرس و تلفن واقعی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="رمزگذاری اطلاعات">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اطلاعات کد ملی چگونه رمزگذاری می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        روش‌های رمزگذاری:
                        <div class="space-y-3 mt-3">
                            <div class="bg-blue-50 p-3 rounded">
                                <h5 class="font-bold text-blue-800">در انتقال</h5>
                                <p class="text-sm text-blue-700">استفاده از SSL/TLS برای حفاظت در شبکه</p>
                            </div>
                            <div class="bg-green-50 p-3 rounded">
                                <h5 class="font-bold text-green-800">در نگهداری</h5>
                                <p class="text-sm text-green-700">رمزگذاری پایگاه داده‌ها با الگوریتم‌های قوی</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded">
                                <h5 class="font-bold text-purple-800">در پردازش</h5>
                                <p class="text-sm text-purple-700">حفاظت حافظه و محاسبات امن</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="دوعاملی 2FA احراز">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">احراز هویت دوعاملی چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>احراز هویت دوعاملی (2FA)</strong> روشی برای افزایش امنیت:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>عامل اول:</strong> کد ملی + رمز عبور</li>
                            <li><strong>عامل دوم:</strong> کد پیامکی یا بیومتریک</li>
                            <li><strong>مزایا:</strong> امنیت بالاتر در برابر هک</li>
                            <li><strong>کاربرد:</strong> بانک‌ها و سامانه‌های حساس</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 7: مسائل فنی (Technical Issues) -->
        <div class="faq-category" data-category="technical">
            <div class="bg-gradient-to-r from-gray-600 to-slate-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    مسائل فنی و تکنولوژیکی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="technical" data-keywords="API کد ملی توسعه دهندگان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا API رسمی برای اعتبارسنجی کد ملی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>API‌های رسمی اعتبارسنجی کد ملی:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>API ثبت احوال:</strong> سرویس رسمی سازمان ثبت احوال</li>
                            <li><strong>وب‌سرویس بانک‌ها:</strong> برای مؤسسات مالی</li>
                            <li><strong>API درگاه ملی:</strong> خدمات الکترونیک دولتی</li>
                            <li><strong>سرویس پیشخوانک:</strong> راه‌حل سریع و معتبر</li>
                        </ul>
                        <div class="bg-blue-50 p-3 rounded mt-3">
                            <p class="text-blue-800 text-sm">
                                <strong>نکته:</strong> استفاده از API‌های غیررسمی ممنوع و خلاف قانون است.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="خطای سیستم اعتبارسنجی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چرا گاهی سامانه‌ها کد ملی معتبر را نمی‌پذیرند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        علل رد کد ملی معتبر در سامانه‌ها:
                        <div class="space-y-3 mt-3">
                            <div class="bg-yellow-50 p-3 rounded">
                                <h5 class="font-bold text-yellow-800">مسائل فنی</h5>
                                <ul class="text-sm text-yellow-700 mt-1">
                                    <li>• قطعی موقت سرور</li>
                                    <li>• مشکل اتصال شبکه</li>
                                    <li>• به‌روزرسانی پایگاه داده</li>
                                </ul>
                            </div>
                            <div class="bg-red-50 p-3 rounded">
                                <h5 class="font-bold text-red-800">مسائل اطلاعاتی</h5>
                                <ul class="text-sm text-red-700 mt-1">
                                    <li>• عدم تطبیق نام و نام خانوادگی</li>
                                    <li>• اختلاف تاریخ تولد</li>
                                    <li>• تغییرات اخیر اطلاعات</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 8: کاربردهای تجاری (Business Applications) -->
        <div class="faq-category" data-category="business">
            <div class="bg-gradient-to-r from-amber-600 to-orange-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                    </svg>
                    کاربردهای تجاری و کسب‌وکار
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="business" data-keywords="KYC احراز هویت مشتری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">فرآیند KYC در ایران چگونه انجام می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>فرآیند شناخت مشتری (KYC) در ایران:</strong>
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>جمع‌آوری اطلاعات:</strong> کد ملی، نام، نام خانوادگی</li>
                            <li><strong>اعتبارسنجی:</strong> تطبیق با پایگاه ثبت احوال</li>
                            <li><strong>تأیید هویت:</strong> مدارک عکس‌دار</li>
                            <li><strong>بررسی سوابق:</strong> چک لیست‌های منع</li>
                            <li><strong>ثبت و نگهداری:</strong> آرشیو امن اطلاعات</li>
                        </ol>
                        <div class="bg-green-50 p-3 rounded mt-3">
                            <p class="text-green-800 text-sm">
                                <strong>قانون:</strong> الزام KYC برای تمام مؤسسات مالی و پرداخت
                            </p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="business" data-keywords="فروشگاه آنلاین احراز هویت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">فروشگاه‌های آنلاین چگونه هویت مشتریان را تأیید کنند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        راهکارهای تأیید هویت برای فروشگاه‌های آنلاین:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>سامانه پیشخوانک:</strong> اعتبارسنجی سریع کد ملی</li>
                            <li><strong>درگاه‌های پرداخت:</strong> تأیید از طریق بانک‌ها</li>
                            <li><strong>پیامک OTP:</strong> تأیید شماره موبایل</li>
                            <li><strong>آپلود مدارک:</strong> عکس کارت ملی</li>
                            <li><strong>تماس تلفنی:</strong> تأیید اطلاعات</li>
                        </ul>
                        <div class="bg-blue-50 p-3 rounded mt-3">
                            <p class="text-blue-800 text-sm">
                                <strong>مزیت:</strong> کاهش کلاهبرداری و افزایش اعتماد مشتریان
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 9: موارد خاص (Special Cases) - Final Category -->
        <div class="faq-category" data-category="special">
            <div class="bg-gradient-to-r from-orange-600 to-red-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    موارد خاص و استثنائی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="special" data-keywords="کودک نوزاد کد ملی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">نوزادان چه موقع کد ملی دریافت می‌کنند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        فرآیند صدور کد ملی برای نوزادان:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>زمان صدور:</strong> حداکثر ۱۵ روز پس از تولد</li>
                            <li><strong>مکان ثبت:</strong> اداره ثبت احوال محل تولد</li>
                            <li><strong>مدارک لازم:</strong> گواهی تولد بیمارستان</li>
                            <li><strong>حضور والدین:</strong> پدر یا مادر با مدارک</li>
                            <li><strong>انتخاب نام:</strong> همزمان با ثبت کد ملی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="فوت کد ملی ابطال">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">پس از فوت، کد ملی چه وضعیتی پیدا می‌کند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        وضعیت کد ملی پس از فوت:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>عدم ابطال:</strong> کد ملی حذف نمی‌شود</li>
                            <li><strong>تغییر وضعیت:</strong> در سیستم به عنوان «فوت‌شده» ثبت می‌شود</li>
                            <li><strong>کاربردهای قانونی:</strong> برای امور ارثیه و قضایی</li>
                            <li><strong>عدم استفاده مجدد:</strong> کد برای فرد دیگری صادر نمی‌شود</li>
                            <li><strong>نگهداری دائم:</strong> در آرشیو ملی باقی می‌ماند</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="تابعیت مضاعف دوگانه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">افراد دارای تابعیت مضاعف چه وضعیتی دارند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        وضعیت تابعیت مضاعف:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>کد ملی ایرانی:</strong> حفظ می‌شود</li>
                            <li><strong>شناسه خارجی:</strong> جداگانه در کشور دوم</li>
                            <li><strong>تعهدات:</strong> رعایت قوانین هر دو کشور</li>
                            <li><strong>خدمات:</strong> دسترسی به خدمات ایرانی</li>
                            <li><strong>محدودیت‌ها:</strong> در برخی مشاغل حساس</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="سالمندان کد ملی قدیمی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سالمندان بدون کد ملی چه باید کنند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        راه‌حل برای سالمندان فاقد کد ملی:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>مراجعه به ثبت احوال:</strong> با شناسنامه</li>
                            <li><strong>تکمیل پرونده:</strong> اطلاعات شخصی</li>
                            <li><strong>صدور کد:</strong> بر اساس اطلاعات موجود</li>
                            <li><strong>تسهیلات ویژه:</strong> برای افراد سالخورده</li>
                            <li><strong>همراهی خانواده:</strong> در صورت نیاز</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="فراموشی کد ملی بازیابی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کد ملی خود را فراموش کرده‌ام، چه کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        راه‌های بازیابی کد ملی:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>کارت ملی:</strong> بررسی کارت شناسایی</li>
                            <li><strong>مدارک بانکی:</strong> دفترچه چک یا کارت</li>
                            <li><strong>اسناد رسمی:</strong> قراردادها و مدارک</li>
                            <li><strong>ثبت احوال:</strong> مراجعه با شناسنامه</li>
                            <li><strong>سامانه my.gov.ir:</strong> بازیابی آنلاین</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="معلولین کد ملی تسهیلات">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا برای معلولان تسهیلات خاصی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        تسهیلات ویژه معلولان:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>دسترسی آسان:</strong> ادارات مجهز به تسهیلات</li>
                            <li><strong>همراه قانونی:</strong> امکان همراهی سرپرست</li>
                            <li><strong>خدمات در منزل:</strong> برای موارد خاص</li>
                            <li><strong>فرم‌های ویژه:</strong> متناسب با نوع معلولیت</li>
                            <li><strong>اولویت خدمات:</strong> سرعت بیشتر در رسیدگی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="زندانیان کد ملی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">وضعیت کد ملی زندانیان چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        وضعیت کد ملی در دوران زندان:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>حفظ کد ملی:</strong> بدون تغییر باقی می‌ماند</li>
                            <li><strong>محدودیت‌های موقت:</strong> برخی خدمات</li>
                            <li><strong>نماینده قانونی:</strong> برای امور ضروری</li>
                            <li><strong>بازگشت حقوق:</strong> پس از آزادی</li>
                            <li><strong>خدمات اولیه:</strong> درمان و آموزش</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="بحران طبیعی فوریت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">در شرایط بحران طبیعی چه باید کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        خدمات اضطراری کد ملی:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>کارت موقت:</strong> صدور فوری</li>
                            <li><strong>مراکز سیار:</strong> خدمات در مناطق آسیب‌دیده</li>
                            <li><strong>تسهیلات ویژه:</strong> بدون مدارک کامل</li>
                            <li><strong>شناسایی بیومتریک:</strong> اثر انگشت و چهره</li>
                            <li><strong>هماهنگی سازمان‌ها:</strong> خدمات یکپارچه</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Search Results Message -->
    <div id="no-results" class="hidden text-center py-12">
        <div class="max-w-md mx-auto">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">نتیجه‌ای یافت نشد</h3>
            <p class="text-gray-500">لطفاً کلمه کلیدی دیگری امتحان کنید یا از فیلترهای دسته‌بندی استفاده کنید.</p>
        </div>
    </div>

</section>

<!-- Advanced FAQ JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Elements
    const faqSearch = document.getElementById('faq-search');
    const advancedFaqSearch = document.getElementById('advanced-faq-search'); 
    const categoryButtons = document.querySelectorAll('.faq-category-btn');
    const faqItems = document.querySelectorAll('.faq-item');
    const faqQuestions = document.querySelectorAll('.faq-question');
    const resultsCounter = document.getElementById('results-count');
    const resultsDiv = document.getElementById('faq-results');
    const noResultsDiv = document.getElementById('no-results');
    const faqContainer = document.getElementById('faq-container');

    // Initialize FAQ System
    initializeFAQ();

    function initializeFAQ() {
        // Add click handlers to questions
        faqQuestions.forEach(question => {
            question.addEventListener('click', function() {
                const answer = this.nextElementSibling;
                const chevron = this.querySelector('.faq-chevron');
                const isOpen = !answer.classList.contains('hidden');

                if (isOpen) {
                    answer.classList.add('hidden');
                    chevron.style.transform = 'rotate(0deg)';
                } else {
                    answer.classList.remove('hidden');
                    chevron.style.transform = 'rotate(180deg)';
                }
            });
        });

        // Search functionality
        if (faqSearch) {
            faqSearch.addEventListener('input', performSearch);
        }
        if (advancedFaqSearch) {
            advancedFaqSearch.addEventListener('input', performAdvancedSearch);
        }

        // Category filters
        categoryButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const category = this.dataset.category;
                filterByCategory(category);
                
                // Update active button
                categoryButtons.forEach(b => b.classList.remove('active', 'bg-blue-600', 'text-white'));
                categoryButtons.forEach(b => b.classList.add('bg-gray-100', 'text-gray-700'));
                
                this.classList.add('active', 'bg-blue-600', 'text-white');
                this.classList.remove('bg-gray-100', 'text-gray-700');
            });
        });
    }

    function performSearch() {
        const query = faqSearch.value.toLowerCase().trim();
        searchFAQs(query);
    }

    function performAdvancedSearch() {
        const query = advancedFaqSearch.value.toLowerCase().trim();
        searchFAQs(query);
        
        // Sync with main search
        if (faqSearch) {
            faqSearch.value = advancedFaqSearch.value;
        }
    }

    function searchFAQs(query) {
        let visibleCount = 0;
        const categories = document.querySelectorAll('.faq-category');
        
        categories.forEach(category => {
            let categoryHasVisibleItems = false;
            const items = category.querySelectorAll('.faq-item');
            
            items.forEach(item => {
                const questionText = item.querySelector('h4').textContent.toLowerCase();
                const answerText = item.querySelector('.faq-answer').textContent.toLowerCase();
                const keywords = item.dataset.keywords ? item.dataset.keywords.toLowerCase() : '';
                
                const isMatch = query === '' || 
                              questionText.includes(query) || 
                              answerText.includes(query) || 
                              keywords.includes(query);
                
                if (isMatch) {
                    item.style.display = '';
                    categoryHasVisibleItems = true;
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Hide/show category header
            category.style.display = categoryHasVisibleItems ? '' : 'none';
        });
        
        updateResultsCounter(visibleCount, query);
    }

    function filterByCategory(category) {
        const categories = document.querySelectorAll('.faq-category');
        let visibleCount = 0;
        
        categories.forEach(cat => {
            const categoryName = cat.dataset.category;
            
            if (category === 'all' || categoryName === category) {
                cat.style.display = '';
                const items = cat.querySelectorAll('.faq-item');
                visibleCount += items.length;
            } else {
                cat.style.display = 'none';
            }
        });
        
        // Clear search
        if (faqSearch) faqSearch.value = '';
        if (advancedFaqSearch) advancedFaqSearch.value = '';
        
        updateResultsCounter(visibleCount, '');
    }

    function updateResultsCounter(count, query) {
        if (resultsCounter && resultsDiv) {
            resultsCounter.textContent = count;
            
            if (query && query.length > 0) {
                resultsDiv.classList.remove('hidden');
            } else {
                resultsDiv.classList.add('hidden');
            }
        }
        
        // Show/hide no results message
        if (noResultsDiv) {
            if (count === 0 && query && query.length > 0) {
                noResultsDiv.classList.remove('hidden');
                faqContainer.classList.add('hidden');
            } else {
                noResultsDiv.classList.add('hidden');
                faqContainer.classList.remove('hidden');
            }
        }
    }

    // Advanced search suggestions (future enhancement)
    function showSearchSuggestions(query) {
        // Implementation for search suggestions
        const suggestions = [
            'چگونه کد ملی را بررسی کنم؟',
            'سامانه‌های دولتی کدامند؟',
            'احراز هویت در بانک چگونه است؟',
            'امنیت کد ملی چیست؟'
        ];
        
        // Show relevant suggestions based on query
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + / to focus search
        if (e.ctrlKey && e.key === '/') {
            e.preventDefault();
            if (advancedFaqSearch) {
                advancedFaqSearch.focus();
            } else if (faqSearch) {
                faqSearch.focus();
            }
        }
        
        // Escape to clear search
        if (e.key === 'Escape') {
            if (faqSearch) faqSearch.value = '';
            if (advancedFaqSearch) advancedFaqSearch.value = '';
            searchFAQs('');
        }
    });

    // Analytics tracking for FAQ usage
    function trackFAQUsage(question, action) {
        // Implementation for analytics
        console.log(`FAQ ${action}: ${question}`);
    }

    // Add usage tracking to questions
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const questionText = this.querySelector('h4').textContent;
            trackFAQUsage(questionText, 'opened');
        });
    });
});

// Smooth scrolling for FAQ links
function scrollToFAQ(faqId) {
    const element = document.getElementById(faqId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Export functions for external use
window.faqSystem = {
    scrollToFAQ,
    searchFAQs: function(query) {
        const event = new Event('input');
        document.getElementById('faq-search').value = query;
        document.getElementById('faq-search').dispatchEvent(event);
    },
    filterByCategory: function(category) {
        const btn = document.querySelector(`[data-category="${category}"]`);
        if (btn) btn.click();
    }
};
</script>

<style>
.faq-question:hover .faq-chevron {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}

.faq-item:hover {
    background-color: #f9fafb;
    transition: background-color 0.2s ease;
}

.faq-category-btn.active {
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.faq-answer {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Custom scrollbar for FAQ container */
.faq-category {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}

.faq-category::-webkit-scrollbar {
    width: 4px;
}

.faq-category::-webkit-scrollbar-track {
    background: transparent;
}

.faq-category::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 2px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .faq-category-btn {
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
    }
    
    .faq-question h4 {
        font-size: 1rem;
    }
}

/* Print styles */
@media print {
    .faq-category-btn,
    #faq-search,
    #advanced-faq-search {
        display: none;
    }
    
    .faq-answer {
        display: block !important;
    }
}
</style>