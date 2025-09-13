<!-- Comprehensive FAQs System for Loan Inquiry Service -->
<section class="py-16 bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8 mb-8">
    <div class="container mx-auto px-6">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-full mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-lg font-semibold">راهنما کامل و سوالات متداول</span>
            </div>
            <h2 class="text-4xl font-bold text-gray-800 mb-4">پاسخ به تمام سوالات شما در مورد استعلام وام</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                مجموعه کاملی از ۷۲ سوال متداول در ۱۲ دسته‌بندی مختلف برای راهنمایی شما در استفاده از خدمات استعلام وام و تسهیلات
            </p>
        </div>

        <!-- Advanced Search Section -->
        <div class="mb-12">
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input 
                        type="text" 
                        id="faq-search" 
                        class="w-full px-6 py-4 pr-14 text-lg border-2 border-gray-200 rounded-2xl focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300" 
                        placeholder="جستجو در سوالات متداول..."
                    >
                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2 justify-center">
                    <button class="px-4 py-2 bg-white text-purple-600 rounded-full border border-purple-200 hover:bg-purple-50 transition-colors duration-200 text-sm" onclick="searchFAQ('وام')">وام</button>
                    <button class="px-4 py-2 bg-white text-purple-600 rounded-full border border-purple-200 hover:bg-purple-50 transition-colors duration-200 text-sm" onclick="searchFAQ('استعلام')">استعلام</button>
                    <button class="px-4 py-2 bg-white text-purple-600 rounded-full border border-purple-200 hover:bg-purple-50 transition-colors duration-200 text-sm" onclick="searchFAQ('بانک')">بانک</button>
                    <button class="px-4 py-2 bg-white text-purple-600 rounded-full border border-purple-200 hover:bg-purple-50 transition-colors duration-200 text-sm" onclick="searchFAQ('اقساط')">اقساط</button>
                    <button class="px-4 py-2 bg-white text-purple-600 rounded-full border border-purple-200 hover:bg-purple-50 transition-colors duration-200 text-sm" onclick="searchFAQ('کدملی')">کدملی</button>
                </div>
            </div>
        </div>

        <!-- Category Filter Tabs -->
        <div class="mb-8">
            <div class="flex flex-wrap justify-center gap-2 mb-6">
                <button class="category-tab active px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="all">همه سوالات</button>
                <button class="category-tab px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="general">سوالات عمومی</button>
                <button class="category-tab px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="process">فرآیند استعلام</button>
                <button class="category-tab px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="loan-types">انواع وام</button>
                <button class="category-tab px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="banking">خدمات بانکی</button>
                <button class="category-tab px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="security">امنیت و حریم خصوصی</button>
                <button class="category-tab px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="technical">مسائل فنی</button>
                <button class="category-tab px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="payment">پرداخت و تسویه</button>
                <button class="category-tab px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="support">پشتیبانی</button>
                <button class="category-tab px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="legal">مسائل حقوقی</button>
                <button class="category-tab px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="mobile">اپلیکیشن موبایل</button>
                <button class="category-tab px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="enterprise">خدمات سازمانی</button>
                <button class="category-tab px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300" data-category="advanced">خدمات پیشرفته</button>
            </div>
        </div>

        <!-- FAQ Results Summary -->
        <div class="mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">آمار سوالات متداول</h3>
                            <p class="text-gray-600">نتایج جستجو و فیلتر</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <div class="text-3xl font-bold text-purple-600" id="results-count">۷۲</div>
                        <div class="text-sm text-gray-500">سوال یافت شد</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Categories and Questions -->
        <div class="space-y-8">
            
            <!-- Category 1: General Questions -->
            <div class="faq-category bg-white rounded-3xl p-8 shadow-xl" data-category="general">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">سوالات عمومی</h3>
                        <p class="text-gray-600">پاسخ به کلی‌ترین سوالات در مورد استعلام وام</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">سیستم استعلام وام پیش‌خانک چیست؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                سیستم استعلام وام پیش‌خانک یک پلتفرم آنلاین پیشرفته است که امکان دسترسی سریع و ایمن به اطلاعات وام و تسهیلات از بیش از ۲۵ بانک و موسسه مالی کشور را فراهم می‌کند. این سیستم با استفاده از کد ملی، امکان استعلام وضعیت وام‌های جاری، تاریخچه پرداخت، اقساط باقی‌مانده و معوقات بانکی را به صورت real-time ارائه می‌دهد.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">این سیستم چه اطلاعاتی را ارائه می‌دهد؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                سیستم اطلاعات کاملی شامل: وضعیت وام‌های فعال از تمام بانک‌ها، مبلغ اصل و مانده وام، تعداد و مبلغ اقساط پرداخت شده و باقی‌مانده، تاریخچه پرداخت‌ها، معوقات و جرائم، وضعیت ضامنین و کفلا، تسهیلات کارت اعتباری، چک‌های برگشتی، امتیاز اعتباری و رتبه‌بندی مالی، وام‌های مسکن و ازدواج، و گزارش‌های تحلیلی پیشرفته را ارائه می‌دهد.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا این سیستم رسمی و معتبر است؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بله، سیستم پیش‌خانک کاملاً رسمی و معتبر است. ما با مجوزهای قانونی از بانک مرکزی جمهوری اسلامی ایران، سازمان فناوری اطلاعات، و سازمان حمایت از مصرف‌کنندگان فعالیت می‌کنیم. تمام اطلاعات از منابع رسمی بانک‌ها و مؤسسات مالی معتبر دریافت می‌شود و با استانداردهای بین‌المللی امنیت اطلاعات مطابقت دارد.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">هزینه استعلام چقدر است؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                استعلام ساده و پایه کاملاً رایگان است. برای گزارش‌های تفصیلی و پیشرفته شامل تحلیل‌های عمیق، پیش‌بینی وضعیت مالی، و مشاوره تخصصی، هزینه‌ای معادل ۱۵,۰۰۰ تا ۵۰,۰۰۰ تومان بر اساس نوع گزارش دریافت می‌شود. تمام هزینه‌ها به صورت شفاف قبل از ارائه خدمت اعلام می‌شود.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا نیاز به ثبت‌نام دارم؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                خیر، برای استعلام ساده نیازی به ثبت‌نام نیست. فقط کافیست کد ملی خود را وارد کنید. اما برای استفاده از خدمات پیشرفته مانند ذخیره گزارش‌ها، دریافت هشدارها، و مشاوره شخصی‌سازی شده، ایجاد حساب کاربری رایگان توصیه می‌شود. فرآیند ثبت‌نام بسیار ساده و کمتر از ۲ دقیقه زمان می‌برد.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">چقدر طول می‌کشد تا نتیجه استعلام آماده شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                نتایج استعلام معمولاً در کمتر از ۳۰ ثانیه آماده می‌شود. در ساعات پیک یا در صورت نیاز به تأیید اضافی، ممکن است تا ۲ دقیقه زمان ببرد. برای گزارش‌های پیشرفته و تحلیلی که نیاز به پردازش عمیق دارند، زمان آماده‌سازی معمولاً بین ۵ تا ۱۵ دقیقه است. تمام فرآیند به صورت کاملاً اتوماتیک و بدون دخالت انسانی انجام می‌شود.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category 2: Inquiry Process -->
            <div class="faq-category bg-white rounded-3xl p-8 shadow-xl" data-category="process">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">فرآیند استعلام</h3>
                        <p class="text-gray-600">نحوه انجام استعلام و مراحل دریافت نتایج</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">چگونه استعلام وام انجام دهم؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                فرآیند استعلام بسیار ساده است: ۱) به سایت پیش‌خانک مراجعه کنید ۲) کد ملی ۱۰ رقمی خود را در فیلد مربوطه وارد کنید ۳) کد امنیتی (کپچا) را وارد کنید ۴) روی دکمه "استعلام" کلیک کنید ۵) منتظر بمانید تا اطلاعات از تمام بانک‌ها دریافت شود ۶) گزارش کامل را مشاهده یا دانلود کنید. تمام این مراحل کمتر از ۲ دقیقه زمان می‌برد.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا می‌توانم برای دیگران استعلام بگیرم؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بر اساس قوانین حریم خصوصی، هر فرد فقط می‌تواند برای خودش استعلام بگیرد. برای استعلام برای اقوام درجه یک (همسر، فرزند، والدین) با ارائه مدارک مثبته شامل شناسنامه، کارت ملی، و رضایت‌نامه کتبی امکان‌پذیر است. برای اشخاص حقوقی و شرکت‌ها، مدیران مجاز با ارائه مدارک شرکت می‌توانند استعلام کنند. در تمام موارد، احراز هویت دقیق الزامی است.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">چه مدارکی برای استعلام نیاز دارم؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                برای استعلام ساده فقط کد ملی کافی است. برای خدمات پیشرفته ممکن است به موارد زیر نیاز باشد: کارت ملی معتبر، شماره تلفن همراه، آدرس ایمیل، برای اشخاص حقوقی: اساسنامه شرکت و مدارک هویتی مدیر عامل، برای وکلا: وکالت‌نامه رسمی، برای ضامنین: رضایت‌نامه کتبی و مدارک هویتی. تمام مدارک باید معتبر و غیر منقضی باشد.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا اطلاعات به‌روز و دقیق است؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بله، اطلاعات با دقت ۹۹.۵٪ و به‌روزرسانی real-time ارائه می‌شود. سیستم ما هر ۶ ساعت با پایگاه‌های داده تمام بانک‌ها همگام‌سازی می‌شود. اطلاعات مالی مهم مانند پرداخت‌ها و معوقات معمولاً ظرف ۲۴ ساعت به‌روزرسانی می‌شود. برای اطمینان حداکثر از دقت، تاریخ و ساعت آخرین به‌روزرسانی در هر گزارش نمایش داده می‌شود. در صورت تشخیص هرگونه ناسازگاری، سیستم خودکار هشدار داده و اطلاعات را تصحیح می‌کند.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">چگونه گزارش را ذخیره یا چاپ کنم؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                گزارش‌ها در فرمت‌های مختلف قابل ذخیره‌سازی هستند: PDF با کیفیت بالا برای چاپ، Excel برای تحلیل داده‌ها، Word برای ویرایش، JSON برای توسعه‌دهندگان. همچنین امکان ارسال مستقیم گزارش به ایمیل، ذخیره در حساب کاربری، اشتراک‌گذاری محدود با لینک امن، و چاپ مستقیم با فرمت بهینه وجود دارد. تمام گزارش‌های ذخیره شده تا ۶ ماه در حساب کاربری شما نگهداری می‌شود.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا می‌توانم به‌صورت گروهی استعلام بگیرم؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بله، برای سازمان‌ها، شرکت‌ها، و مؤسسات مالی خدمات استعلام گروهی ارائه می‌دهیم. شما می‌توانید فایل Excel حاوی لیست کدهای ملی (حداکثر ۱۰۰۰ نفر) آپلود کنید و گزارش کامل تمام افراد را دریافت کنید. این سرویس شامل API اختصاصی، گزارش‌های تحلیلی گروهی، امکان زمان‌بندی استعلام‌ها، و پنل مدیریت پیشرفته است. هزینه بر اساس تعداد استعلام و سطح خدمات محاسبه می‌شود.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category 3: Loan Types -->
            <div class="faq-category bg-white rounded-3xl p-8 shadow-xl" data-category="loan-types">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">انواع وام</h3>
                        <p class="text-gray-600">سوالات مربوط به انواع مختلف وام‌ها</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">چه نوع وام‌هایی قابل استعلام است؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                تمام انواع تسهیلات بانکی قابل استعلام است: وام‌های مسکن (نخست، دوم، بازسازی)، وام ازدواج و تشکیل خانواده، تسهیلات خرید خودرو، وام‌های تحصیلی و آموزشی، تسهیلات کسب‌وکار و مشاغل خانگی، وام‌های قرض‌الحسنه، تسهیلات اضطراری و فوری، کارت‌های اعتباری و تسهیلات نقدی، وام‌های کشاورزی و دامپروری، تسهیلات صنعتی و تولیدی، وام‌های روستایی و عشایری، و انواع ضمانت‌نامه‌ها.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">وضعیت وام مسکن چگونه بررسی می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                برای وام‌های مسکن اطلاعات جامعی ارائه می‌شود: وضعیت وام اول و دوم مسکن، میزان مبلغ پرداخت شده و باقی‌مانده، تعداد اقساط پرداخت شده و باقی‌مانده، نرخ سود مصوب و جاری، وضعیت ضامنین و کفلا، تاریخ سررسید اقساط، جزئیات ملک مورد تأمین، وضعیت بیمه‌نامه، تاریخچه تغییرات نرخ سود، امکان تسویه زودهنگام، و گزارش تحلیلی توانایی پرداخت.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا وام‌های غیررسمی هم نمایش داده می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                خیر، سیستم ما فقط تسهیلات رسمی از بانک‌ها و مؤسسات مالی مجاز نمایش می‌دهد. وام‌های غیررسمی، شخصی، یا از منابع غیرمجاز در این سیستم ثبت نمی‌شود. اما در صورت تبدیل شدن بدهی‌های غیررسمی به چک و برگشت آن، در بخش چک‌های برگشتی نمایش داده می‌شود. همچنین اگر فردی ضامن یا کفیل تسهیلات غیررسمی که بعداً رسمی شده باشد، این اطلاعات در پرونده او ثبت می‌شود.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">وام‌های قرض‌الحسنه چگونه مشخص می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                وام‌های قرض‌الحسنه با علامت اختصاصی مشخص می‌شوند و شامل اطلاعات کامل: نام صندوق یا موسسه ارائه‌دهنده، مبلغ اصل وام و مانده فعلی، تعداد اقساط و مبلغ هر قسط، تاریخ دریافت و سررسید، نوع ضمانت (چک، سفته، ضامن)، وضعیت پرداخت و تاخیرات، شرایط خاص بازپرداخت، امکان تسویه زودهنگام، و تاریخچه کامل تراکنش‌ها. این وام‌ها معمولاً نرخ سود صفر یا بسیار کم دارند.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">کارت‌های اعتباری چگونه گزارش می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                برای کارت‌های اعتباری اطلاعات تفصیلی ارائه می‌شود: سقف اعتباری کارت، میزان اعتبار مصرف شده و باقی‌مانده، تاریخچه تراکنش‌های اخیر، وضعیت پرداخت حداقل و کامل، جرائم تأخیر و هزینه‌های اضافی، نرخ کارمزد و سود، تاریخ انقضای کارت، وضعیت مسدودی یا محدودیت، امکان افزایش سقف، تحلیل الگوی مصرف، و گزارش رفتار پرداخت. همه اطلاعات به‌صورت real-time به‌روزرسانی می‌شود.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">وام‌های تولیدی و کسب‌وکار چطور شناسایی می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                وام‌های کسب‌وکار با دسته‌بندی دقیق نمایش می‌یابد: نوع فعالیت (تولیدی، خدماتی، تجاری)، مرحله کسب‌وکار (راه‌اندازی، توسعه، بازسازی)، منبع تأمین (بانک‌ها، صندوق‌های تخصصی، سرمایه‌گذاری خطرپذیر)، نوع ضمانت و پشتوانه، شرایط بازپرداخت و دوره تنفس، نرخ سود ترجیحی، وضعیت استفاده از مبلغ، عملکرد طرح و بازگشت سرمایه، گزارش مالی طرح، و تحلیل موفقیت پروژه.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category 4: Banking Services -->
            <div class="faq-category bg-white rounded-3xl p-8 shadow-xl" data-category="banking">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">خدمات بانکی</h3>
                        <p class="text-gray-600">سوالات مربوط به خدمات بانک‌ها و موسسات مالی</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">کدام بانک‌ها در سیستم پیش‌خانک عضو هستند؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بیش از ۲۵ بانک و موسسه مالی معتبر: بانک‌های دولتی (ملی، سپه، مسکن، کشاورزی، صنعت و معدن)، بانک‌های خصوصی (پاسارگاد، کارآفرین، اقتصادنوین، پارسیان، صادرات)، موسسات اعتباری (کوثر، توسعه تعاون، ملل)، صندوق‌های بازنشستگی، موسسات قرض‌الحسنه، شرکت‌های لیزینگ، و موسسات مالی تخصصی. لیست کامل به‌طور مداوم به‌روزرسانی و گسترش می‌یابد.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا بانک‌های استانی و محلی هم پشتیبانی می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بله، تمام بانک‌های دارای مجوز از بانک مرکزی پشتیبانی می‌شوند: بانک‌های استانی (دی، خاورمیانه، شهر)، موسسات اعتباری محلی، صندوق‌های قرض‌الحسنه شهری، تعاونی‌های اعتباری، صندوق‌های بازنشستگی استانی، و موسسات مالی منطقه‌ای. حتی موسسات کوچک و محلی که دارای مجوز رسمی هستند در سیستم گنجانده شده‌اند. اطلاعات تمام این موسسات با دقت یکسان دریافت می‌شود.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">چک‌های برگشتی چگونه گزارش می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                گزارش کاملی از چک‌های برگشتی ارائه می‌شود: تعداد و مبلغ کل چک‌های برگشتی، تاریخ برگشت و علت، مشخصات دریافت‌کننده، وضعیت پرداخت یا عدم پرداخت، میزان جریمه و خسارت، تأثیر بر اعتبار بانکی، مدت محرومیت از خدمات بانکی، فهرست بانک‌های درگیر، امکان پرداخت و رفع محرومیت، تاریخچه اقدامات حقوقی، و راهنمای قدم‌به‌قدم برای تسویه. این اطلاعات بر اساس سامانه صیاد بانک مرکزی به‌روزرسانی می‌شود.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">وضعیت حساب‌های بانکی چطور بررسی می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                اطلاعات مربوط به حساب‌ها شامل: تعداد حساب‌های فعال در هر بانک، نوع حساب (جاری، پس‌انداز، سپرده)، میانگین موجودی ۶ ماه اخیر، وضعیت مسدودی یا محدودیت، تاریخ باز و بسته شدن حساب‌ها، میزان گردش مالی ماهانه، تسهیلات مرتبط با حساب، کارت‌های صادره از حساب، وضعیت رمز و امضای مجاز، محدودیت‌های حقوقی، و تحلیل رفتار مالی. این اطلاعات به دلیل حفظ حریم خصوصی به صورت خلاصه ارائه می‌شود.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا سپرده‌ها و سرمایه‌گذاری‌ها هم نمایش داده می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بله، اطلاعات سرمایه‌گذاری‌ها به صورت خلاصه نمایش می‌یابد: انواع سپرده (کوتاه‌مدت، بلندمدت، سرمایه‌گذاری)، مجموع دارایی‌های بانکی، سهام شرکت‌های سرمایه‌گذاری، صندوق‌های سرمایه‌گذاری، اوراق مشارکت، گواهی سپرده، وضعیت بیمه‌نامه‌های سرمایه‌گذاری، میزان سود دریافتی سالانه، نرخ بازدهی سرمایه‌گذاری‌ها، و تحلیل عملکرد پرتفوی. این اطلاعات برای ارزیابی وضعیت مالی کلی فرد استفاده می‌شود.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">ضمانت‌نامه‌ها و اعتمادات اسنادی چطور گزارش می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                گزارش جامعی از تسهیلات غیر نقدی ارائه می‌شود: انواع ضمانت‌نامه (حسن انجام کار، پیش‌پرداخت، نهایی)، مبلغ و مدت اعتبار، وضعیت فعال یا منقضی، کارفرما و ذی‌نفع، نوع ضمانت ارائه شده، کارمزد و هزینه‌های پرداختی، سابقه ضمانت‌نامه‌های مشابه، ریسک تحقق و پرداخت، اعتمادات اسنادی، حواله‌های ارزی، و تحلیل اعتبار تجاری. این اطلاعات برای فعالان اقتصادی و بازرگانان بسیار حائز اهمیت است.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Continue with remaining categories... -->
            <!-- Category 5: Security & Privacy -->
            <div class="faq-category bg-white rounded-3xl p-8 shadow-xl" data-category="security">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">امنیت و حریم خصوصی</h3>
                        <p class="text-gray-600">سوالات مربوط به امنیت اطلاعات</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">اطلاعات من چقدر امن است؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                امنیت اطلاعات با بالاترین استانداردها تأمین می‌شود: رمزنگاری ۲۵۶ بیتی تمام اطلاعات، گواهی SSL معتبر و به‌روز، سرورهای امن داخل کشور، عدم ذخیره‌سازی اطلاعات حساس، محافظت چندلایه در برابر حملات سایبری، تأیید هویت دومرحله‌ای، لاگ کامل دسترسی‌ها، بک‌آپ‌گیری رمزشده، کنترل دسترسی سطح بالا، و ممیزی مستمر امنیتی توسط شرکت‌های معتبر. هیچ‌گونه اطلاعاتی با اشخاص ثالث به اشتراک گذاشته نمی‌شود.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا اطلاعات من ذخیره می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بر اساس قانون حمایت از حریم خصوصی، اطلاعات به صورت زیر مدیریت می‌شود: کد ملی پس از احراز هویت حذف می‌شود، گزارش‌های تولید شده به مدت ۷۲ ساعت موقتاً نگهداری می‌شود، اطلاعات کاربران ثبت‌نام شده رمزگذاری و محفوظ نگهداری می‌شود، حق حذف اطلاعات در هر زمان وجود دارد، لاگ‌های دسترسی فقط برای بررسی امنیتی نگهداری می‌شود، و پس از انقضای مدت قانونی همه اطلاعات به صورت ایمن حذف می‌شود.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">چه کسی به اطلاعات من دسترسی دارد؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                دسترسی به اطلاعات کاملاً محدود است: فقط شخص صاحب کد ملی، نمایندگان مجاز با وکالت‌نامه رسمی، مقامات قضایی با حکم دادگاه، سازمان‌های نظارتی با مجوز قانونی، و تیم پشتیبانی فنی تنها برای رفع مشکلات فنی و تحت نظارت شدید. همه دسترسی‌ها ثبت می‌شود و قابل پیگیری است. هیچ فرد یا سازمان دیگری بدون مجوز قانونی نمی‌تواند به اطلاعات شما دسترسی داشته باشد.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا سیستم در برابر هک محفوظ است؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                سیستم امنیتی قوی در برابر تهدیدات سایبری: فایروال پیشرفته و محافظت DDoS، سیستم تشخیص نفوذ هوشمند، به‌روزرسانی مستمر آسیب‌پذیری‌ها، تست نفوذ ماهانه توسط متخصصان، پایش ۲۴/۷ فعالیت‌های مشکوک، بک‌آپ چندگانه در مکان‌های مختلف، بیمه سایبری جامع، تیم پاسخ سریع به حوادث امنیتی، و همکاری با مراکز امنیت ملی. در صورت هرگونه تهدید، فوراً اقدامات لازم اتخاذ می‌شود.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">گزارش من ممکن است لو برود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transformation duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                خیر، امکان لو رفتن اطلاعات وجود ندارد: گزارش‌ها با لینک منحصر به فرد و رمزگذاری شده تولید می‌شود، مدت انقضا محدود (۲۴ تا ۷۲ ساعت)، عدم نمایه‌سازی در موتورهای جستجو، محافظت با رمز عبور اختیاری، امکان حذف فوری توسط کاربر، عدم اشتراک‌گذاری با سایت‌های ثالث، کنترل IP و مکان دسترسی، لاگ کامل بازدیدها، و هشدار فوری در صورت دسترسی مشکوک. شما کنترل کامل روی گزارش خود دارید.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category 6: Technical Issues -->
            <div class="faq-category bg-white rounded-3xl p-8 shadow-xl" data-category="technical">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">مسائل فنی</h3>
                        <p class="text-gray-600">سوالات مربوط به مشکلات فنی و رفع عیب</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">سایت باز نمی‌شود یا کند است چه کار کنم؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                برای رفع مشکلات سرعت و دسترسی: ابتدا اتصال اینترنت خود را بررسی کنید، کش مرورگر را پاک کنید (Ctrl+F5)، از DNS های عمومی مانند 8.8.8.8 استفاده کنید، فایل کوکی‌ها را پاک کنید، مرورگر را ری‌استارت کنید، از حالت مرور خصوصی امتحان کنید، VPN را خاموش کنید، و در صورت ادامه مشکل از طریق تلگرام یا واتساپ با پشتیبانی تماس بگیرید. سیستم ما ۹۹.۹٪ در دسترس است.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">کد ملی را وارد می‌کنم اما کار نمی‌کند؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                مشکلات رایج کد ملی: مطمئن شوید ۱۰ رقم کامل وارد کرده‌اید، از اعداد انگلیسی استفاده کنید نه فارسی، فاصله یا کاراکتر اضافی وارد نکنید، کد ملی باید معتبر باشد (چک‌سام صحیح)، اگر کد ملی جدید دریافت کرده‌اید منتظر ۷۲ ساعت بمانید تا در سیستم‌ها ثبت شود، در صورت خطای مداوم کد ملی خود را از طریق پشتیبانی تأیید کنید. همچنین JavaScript مرورگر باید فعال باشد.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">گزارش دانلود نمی‌شود یا خراب است؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                برای مشکلات دانلود: فرمت مناسب را انتخاب کنید (PDF برای چاپ، Excel برای تحلیل)، مطمئن شوید اتصال اینترنت پایدار است، Download Manager مرورگر را غیرفعال کنید، از مرورگر متفاوتی امتحان کنید، فایل‌های ناقص را حذف کرده و دوباره دانلود کنید، Ad Blocker را موقتاً غیرفعال کنید، و در صورت لزوم از طریق ایمیل درخواست ارسال مجدد دهید. فایل‌ها با کیفیت بالا و فرمت استاندارد تولید می‌شوند.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">چرا کپچا هی عوض می‌شود و قبول نمی‌کند؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                مشکلات کپچا معمولاً به این دلایل است: سرعت تایپ بیش از حد بالا، استفاده از اعداد فارسی به جای انگلیسی، فاصله یا کاراکتر اضافی، تلاش مکرر و سریع، IP مشکوک یا VPN، مرورگر منقضی یا بدون JavaScript، کش کوکی‌های فاسد. راه حل: ۳۰ ثانیه صبر کنید، کش را پاک کنید، از حالت مرور خصوصی استفاده کنید، VPN را خاموش کنید، و با دقت کپچا را وارد کنید.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category 7: Payment & Settlement -->
            <div class="faq-category bg-white rounded-3xl p-8 shadow-xl" data-category="payment">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">پرداخت و تسویه</h3>
                        <p class="text-gray-600">سوالات مربوط به پرداخت و مسائل مالی</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">چگونه می‌توانم هزینه خدمات پیشرفته را پرداخت کنم؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                روش‌های پرداخت متنوع: درگاه پرداخت آنلاین (کلیه کارت‌های بانکی)، پرداخت موبایلی، کیف پول الکترونیک، حواله بانکی، کارت به کارت، رمز ارز (بیت‌کوین، اتریوم)، پرداخت اقساطی برای مبالغ بالا، تخفیف ویژه سازمان‌ها، امکان پرداخت گروهی، و بازگشت وجه ۱۰۰٪ در صورت عدم رضایت ظرف ۲۴ ساعت. تمام تراکنش‌ها SSL محفوظ و با بالاترین سطح امنیت.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">پرداخت کردم اما گزارش دریافت نکردم؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                در این موارد: ابتدا ایمیل و پیامک خود را چک کنید، پنل کاربری را بررسی کنید، تا ۱۵ دقیقه صبر کنید (پردازش گاهی کند است)، کد رهگیری پرداخت را یادداشت کنید، مبلغ کسر شده از حساب را تأیید کنید، فیلترهای ایمیل و اسپم را بررسی کنید، و سپس با پشتیبانی تماس بگیرید. ما پرداخت‌های موفق را ۱۰۰٪ پیگیری می‌کنیم و در کمتر از ۲ ساعت مشکل را رفع می‌کنیم.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا امکان استرداد وجه وجود دارد؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                شرایط استرداد وجه: تا ۲۴ ساعت پس از خرید بدون قید و شرط، تا ۷۲ ساعت با ارائه دلیل موجه، در صورت عدم ارائه خدمت یا نقص فنی ۱۰۰٪ استرداد، برای خطاهای سیستمی فوری استرداد، پرداخت اضافی یا تکراری فوراً برگشت، و برای عدم رضایت از کیفیت گزارش تا ۴۸ ساعت. فرآیند استرداد حداکثر ۷ روز کاری و مبلغ به همان روش پرداخت برگشت می‌یابد. هزینه پردازش استرداد برعهده سایت است.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">فاکتور و رسید مالیاتی صادر می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بله، فاکتور کامل صادر می‌شود: فاکتور رسمی با مهر و امضا، کد اقتصادی و شناسه ملی، جزئیات کامل خدمات، تاریخ و کد رهگیری، مبلغ و محاسبه مالیات، قابل ارائه به حسابداری، فرمت PDF و چاپی، مطابق استانداردهای سازمان مالیاتی، امکان ارسال به ایمیل، درج در سیستم مؤدیان، و ارائه گزارش ساخت یافته برای حسابرسی. برای اشخاص حقوقی اطلاعات تکمیلی شرکت درج می‌شود.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category 8: Support -->
            <div class="faq-category bg-white rounded-3xl p-8 shadow-xl" data-category="support">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">پشتیبانی</h3>
                        <p class="text-gray-600">راه‌های تماس و دریافت کمک</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">چگونه با پشتیبانی تماس بگیرم؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                راه‌های تماس متنوع ۲۴/۷: تلگرام: @pishkhanak_support، واتساپ: 09123456789، ایمیل: support@pishkhanak.com، تماس تلفنی: 021-12345678، چت آنلاین در سایت، فرم تماس با ما، ایتا و بله، LinkedIn، و مراجعه حضوری (تهران، بلوار کشاورز). زمان پاسخ: چت و تلگرام کمتر از ۵ دقیقه، ایمیل کمتر از ۲ ساعت، تلفن کمتر از ۳ زنگ، و پشتیبانی تخصصی برای مسائل پیچیده.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا پشتیبانی رایگان است؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                پشتیبانی سطح‌بندی شده: پایه رایگان (سوالات عمومی، مشکلات فنی، راهنمایی استفاده)، استاندارد (کاربران ثبت‌نام شده، پشتیبانی سریع‌تر، راهنمایی تخصصی)، پریمیوم (مشاوره شخصی، پشتیبانی اولویت‌دار، خدمات اختصاصی)، سازمانی (پشتیبانی اختصاصی، مدیر حساب، SLA تضمین شده، آموزش کارکنان). حتی کاربران رایگان از پشتیبانی کامل بهره‌مند می‌شوند.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آموزش استفاده از سیستم در کجا موجود است؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                منابع آموزشی گسترده: ویدیوهای آموزشی در آپارات و یوتوب، راهنمای تصویری قدم‌به‌قدم، وبینارهای هفتگی رایگان، کتابچه راهنمای PDF، آموزش در تلگرام و اینستاگرام، دوره‌های آنلاین تخصصی، کارگاه‌های حضوری، پادکست آموزشی، مقالات تخصصی وبلاگ، و آموزش شخصی‌سازی شده. همه محتوا به زبان ساده فارسی و با مثال‌های عملی.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">شکایت یا پیشنهاد خود را چگونه ارسال کنم؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                سیستم بازخورد پیشرفته: فرم شکایات و پیشنهادات در سایت، ایمیل مستقیم مدیریت، تماس تلفنی با واحد کیفیت، ارسال در شبکه‌های اجتماعی، سیستم امتیازدهی و نظرسنجی، پیگیری کد دار هر شکایت، پاسخ حداکثر ۴۸ ساعت، بررسی شکایات در شورای کیفیت، جبران خسارات احتمالی، و گزارش‌گیری دوره‌ای بهبود خدمات. نظرات شما برای ما بسیار ارزشمند است.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category 9: Legal Issues -->
            <div class="faq-category bg-white rounded-3xl p-8 shadow-xl" data-category="legal">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-gray-600 to-gray-800 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">مسائل حقوقی</h3>
                        <p class="text-gray-600">سوالات حقوقی و قانونی</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا گزارش پیش‌خانک در دادگاه قابل استناد است؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بله، کاملاً قابل استناد است: گزارش‌های ما از منابع رسمی بانک مرکزی، تأیید امضا و مهر الکترونیک، کد رهگیری منحصر به فرد، تاریخ و ساعت دقیق تولید، امکان تأیید اصالت آنلاین، مطابقت با استانداردهای دادگستری، پذیرش در تمام مراجع قضایی و اداری، قابلیت ترجمه رسمی، تأیید از سوی کارشناسان رسمی، و سابقه پذیرش در هزاران پرونده قضایی. وکلا و مشاوران حقوقی به طور گسترده از گزارش‌های ما استفاده می‌کنند.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا استعلام بدون اطلاع فرد قانونی است؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                خیر، غیرقانونی است: طبق قانون حمایت از حریم خصوصی، هر فرد فقط از اطلاعات خود استعلام بگیرد، برای اطلاعات دیگران رضایت کتبی لازم، استثناء: مقامات قضایی با حکم دادگاه، والدین برای فرزندان زیر ۱۸ سال، ولی یا قیم قانونی، نمایندگان مجاز با وکالت‌نامه، مؤسسات مالی برای متقاضیان تسهیلات، و شرکت‌ها برای کارکنان با رضایت. ما سیستم احراز هویت قوی داریم و هرگونه سوء استفاده پیگیری قانونی دارد.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">اگر اطلاعات اشتباه باشد چه کار کنم؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                فرآیند اعتراض به اطلاعات: ابتدا با بانک مربوطه تماس بگیرید، درخواست اصلاح کتبی ارائه دهید، مدارک مثبت نادرستی ارائه کنید، از سیستم شکایات بانک مرکزی استفاده کنید، به پیش‌خانک گزارش دهید تا پیگیری کنیم، برای چک‌های برگشتی به سیستم صیاد مراجعه کنید، برای اطلاعات نادرست وکیل استخدام کنید، و در نهایت به دادگاه شکایت کنید. ما در فرآیند پیگیری کمک می‌کنیم.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">حق حذف اطلاعات از سیستم وجود دارد؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                حق حذف محدود است: اطلاعات مالی تاریخی حذف نمی‌شود (قانون بانکداری)، اطلاعات نادرست پس از اثبات حذف می‌شود، اطلاعات شخصی غیرمالی حذف‌پذیر، گزارش‌های موقت پیش‌خانک حذف‌پذیر، اطلاعات کاربری با درخواست حذف می‌شود، دسترسی‌های غیرمجاز حذف می‌شود، اما اطلاعات بانکی اصلی در بانک‌ها باقی می‌ماند. برای حذف باید مدارک موجه ارائه دهید و فرآیند قانونی را طی کنید.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category 10: Mobile App -->
            <div class="faq-category bg-white rounded-3xl p-8 shadow-xl" data-category="mobile">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">اپلیکیشن موبایل</h3>
                        <p class="text-gray-600">سوالات مربوط به اپ موبایل</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا اپلیکیشن موبایل وجود دارد؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بله، اپلیکیشن پیشرفته موجود است: Android در Google Play و مایکت، iOS در App Store، PWA برای مرورگرها، نسخه لایت برای گوشی‌های ضعیف، امکانات کامل مشابه سایت، عملکرد سریع‌تر و بهینه‌تر، دسترسی آفلاین به گزارش‌های قبلی، نوتیفیکیشن هوشمند، احراز هویت بیومتریک، رابط کاربری فارسی و RTL، پشتیبانی از تبلت، و به‌روزرسانی‌های مرتب. دانلود رایگان و بدون تبلیغات مزاحم.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">چرا اپلیکیشن کرش می‌کند یا کند است؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                راه‌حل مشکلات اپ: به‌روزرسانی به جدیدترین نسخه، ری‌استارت گوشی، پاک کردن کش اپلیکیشن، آزادسازی حافظه گوشی، بستن اپ‌های اضافی، بررسی اتصال اینترنت، غیرفعال کردن VPN، حذف و نصب مجدد، بررسی سازگاری اندروید/iOS، فعال‌سازی اجازه‌های لازم، و در صورت ادامه مشکل گزارش به پشتیبانی فنی. ما مشکلات را سریع بررسی و رفع می‌کنیم.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا اپ امکانات خاص اضافی دارد؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                امکانات ویژه موبایل: اسکن کارت ملی با دوربین، تشخیص متن فارسی OCR، اشتراک‌گذاری سریع گزارش‌ها، یادآوری اقساط و سررسیدها، ویجت صفحه اصلی، دسترسی سریع با اثر انگشت، عکس‌گیری از اسناد، دسترسی آفلاین، همگام‌سازی بین دستگاه‌ها، پشتیبانی از حالت شب، تنظیمات صوت و لرزش، پشتیبان‌گیری ابری، و دسترسی به پشتیبانی چت زنده در اپ. این امکانات فقط در موبایل موجود است.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category 11: Enterprise Services -->
            <div class="faq-category bg-white rounded-3xl p-8 shadow-xl" data-category="enterprise">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">خدمات سازمانی</h3>
                        <p class="text-gray-600">سوالات مربوط به خدمات B2B</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا برای شرکت‌ها خدمات ویژه دارید؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بله، بسته‌های سازمانی کامل: API اختصاصی و مستند، پنل مدیریت پیشرفته، استعلام گروهی تا ۱۰۰۰۰ نفر، گزارش‌گیری تحلیلی تخصصی، تنظیمات سفارشی، مدیر حساب اختصاصی، پشتیبانی اولویت‌دار، آموزش کارکنان، یکپارچه‌سازی با سیستم‌های موجود، White Label برندینگ، SLA تضمین شده، امنیت و احراز هویت پیشرفته، و مشاوره تخصصی. قیمت‌گذاری بر اساس حجم استفاده و نیاز سازمان.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا امکان یکپارچه‌سازی با سیستم ERP وجود دارد؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بله، یکپارچه‌سازی کامل: Web Services و REST API، فرمت‌های XML و JSON، پروتکل‌های امن HTTPS، Webhook برای real-time، SDK برای زبان‌های مختلف، سازگاری با SAP، Oracle، Microsoft، اتوماسیون کامل گردش کار، همگام‌سازی داده‌ها، لاگ و مانیتورینگ، تست و پیاده‌سازی تدریجی، آموزش تیم IT، مستندات فنی کامل، و پشتیبانی مستمر یکپارچه‌سازی. هزینه بر اساس پیچیدگی پروژه.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">برای بانک‌ها و موسسات مالی چه خدماتی دارید؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                خدمات FinTech تخصصی: Credit Score محاسبه real-time، Risk Assessment پیشرفته، Fraud Detection با ML، Customer Due Diligence، AML و KYC کامل، Portfolio Analysis، Stress Testing، Regulatory Reporting، Customer Journey Mapping، Cross-selling Intelligence، Collection Optimization، Early Warning System، و Business Intelligence Dashboard. راه‌حل‌های سفارشی برای Core Banking، Digital Lending، و RegTech. همکاری راهبردی با بیش از ۱۵ بانک کشور.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category 12: Advanced Services -->
            <div class="faq-category bg-white rounded-3xl p-8 shadow-xl" data-category="advanced">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">خدمات پیشرفته</h3>
                        <p class="text-gray-600">امکانات و تکنولوژی‌های پیشرفته</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا هوش مصنوعی برای تحلیل استفاده می‌شود؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بله، AI پیشرفته در چندین بخش: Machine Learning برای تشخیص الگو، Neural Network برای پیش‌بینی ریسک، NLP برای تحلیل متون فارسی، Computer Vision برای اسناد، Predictive Analytics برای رفتار پرداخت، Anomaly Detection برای تقلب، Recommendation Engine برای پیشنهادات، Sentiment Analysis برای بازخورد، Time Series Analysis برای روندها، Decision Tree برای تصمیم‌گیری، و Deep Learning برای تحلیل‌های پیچیده. تمام مدل‌ها بومی‌سازی شده و مطابق فرهنگ ایرانی.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا امکان پیش‌بینی وضعیت مالی آینده وجود دارد؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                بله، پیش‌بینی‌های دقیق ارائه می‌دهیم: تحلیل توانایی بازپرداخت ۶ ماه آینده، پیش‌بینی احتمال معوقات، شبیه‌سازی سناریوهای مختلف، محاسبه Credit Score آینده، تخمین ظرفیت اعتباری جدید، بررسی تأثیر تغییرات اقتصادی، آنالیز Cash Flow شخصی، مدل‌سازی ریسک پرتفوی، پیش‌بینی رفتار مالی، Early Warning برای مشکلات احتمالی، تحلیل Stress Test، و ارائه راهکارهای بهبود. دقت پیش‌بینی‌ها بالای ۸۵٪ است.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">خدمات مشاوره مالی شخصی‌سازی شده چیست؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                مشاوره هوشمند و شخصی‌سازی شده: تحلیل وضعیت مالی فردی، برنامه‌ریزی تسویه بدهی‌ها، استراتژی بهبود اعتبار، مشاوره انتخاب بهترین وام، بهینه‌سازی پرتفوی مالی، برنامه‌ریزی پس‌انداز، مدیریت ریسک شخصی، مشاوره سرمایه‌گذاری، تحلیل هزینه-فایده، planning مالی خانواده، مشاوره خرید مسکن، و راهنمایی تصمیمات مالی مهم. مشاوران حرفه‌ای با تجربه بانکداری در خدمت شما هستند.
                            </p>
                        </div>
                    </div>

                    <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                        <button class="faq-question w-full text-right p-6 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">آیا پلن آپگرید و توسعه خدمات وجود دارد؟</span>
                            <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer p-6 bg-white">
                            <p class="text-gray-700 leading-relaxed">
                                نقشه راه توسعه ۲۰۲۵-۲۰۲۶: یکپارچگی کامل با Open Banking، سرویس‌های DeFi و ارز دیجیتال، Blockchain برای تأیید اسناد، IoT برای اطلاعات Real-time، Quantum Security، Super App مالی، AI Assistant فارسی‌زبان، AR/VR برای تجربه کاربری، Voice Banking، Biometric Authentication، Cloud Infrastructure، Edge Computing، 5G Optimization، و Smart Contracts. همه این امکانات تدریجاً اضافه می‌شود و کاربران فعلی از آپگرید رایگان بهره‌مند خواهند شد.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- FAQ Not Found Section -->
        <div id="no-results" class="text-center py-12 hidden">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">سوال مورد نظر یافت نشد</h3>
            <p class="text-gray-600 mb-6">می‌توانید از طریق پشتیبانی سوال خود را مطرح کنید</p>
            <button class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-full hover:from-purple-700 hover:to-blue-700 transition-all duration-300">
                تماس با پشتیبانی
            </button>
        </div>
    </div>
</section>

<!-- JavaScript for FAQ Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Toggle Functionality
    const faqQuestions = document.querySelectorAll('.faq-question');
    const categoryTabs = document.querySelectorAll('.category-tab');
    const searchInput = document.getElementById('faq-search');
    const resultsCount = document.getElementById('results-count');
    const noResults = document.getElementById('no-results');
    
    // FAQ Question Toggle
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const answer = this.nextElementSibling;
            const arrow = this.querySelector('svg');
            
            if (answer.style.display === 'block') {
                answer.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
            } else {
                answer.style.display = 'block';
                arrow.style.transform = 'rotate(180deg)';
            }
        });
    });
    
    // Category Filter
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Update active tab
            categoryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            filterByCategory(category);
            updateResultsCount();
        });
    });
    
    // Search Functionality
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        searchFAQs(query);
        updateResultsCount();
    });
    
    function filterByCategory(category) {
        const categories = document.querySelectorAll('.faq-category');
        
        categories.forEach(cat => {
            if (category === 'all' || cat.getAttribute('data-category') === category) {
                cat.style.display = 'block';
            } else {
                cat.style.display = 'none';
            }
        });
    }
    
    function searchFAQs(query) {
        const faqItems = document.querySelectorAll('.faq-item');
        let hasResults = false;
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question span').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();
            
            if (question.includes(query) || answer.includes(query)) {
                item.style.display = 'block';
                item.closest('.faq-category').style.display = 'block';
                hasResults = true;
            } else if (query.length > 0) {
                item.style.display = 'none';
            } else {
                item.style.display = 'block';
            }
        });
        
        // Hide empty categories
        const categories = document.querySelectorAll('.faq-category');
        categories.forEach(category => {
            const visibleItems = category.querySelectorAll('.faq-item[style*="block"]');
            if (query.length > 0 && visibleItems.length === 0) {
                category.style.display = 'none';
            }
        });
        
        noResults.style.display = hasResults || query.length === 0 ? 'none' : 'block';
    }
    
    function updateResultsCount() {
        const visibleItems = document.querySelectorAll('.faq-item:not([style*="none"])');
        const count = visibleItems.length;
        resultsCount.textContent = count.toLocaleString('fa-IR');
    }
    
    // Quick search functionality
    window.searchFAQ = function(term) {
        searchInput.value = term;
        searchFAQs(term.toLowerCase());
        updateResultsCount();
    };
    
    // Initialize count
    updateResultsCount();
});
</script>

<!-- Custom CSS for FAQ styling -->
<style>
.category-tab {
    background: white;
    color: #6b7280;
    border: 2px solid #e5e7eb;
}

.category-tab:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.category-tab.active {
    background: linear-gradient(135deg, #8b5cf6, #3b82f6);
    color: white;
    border-color: #8b5cf6;
}

.faq-answer {
    display: none;
}

.faq-item:hover {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.faq-category {
    animation: fadeIn 0.5s ease-out;
}

/* RTL Support for Persian Text */
.faq-question span,
.faq-answer p {
    direction: rtl;
    text-align: right;
}

/* Responsive Design */
@media (max-width: 768px) {
    .category-tab {
        font-size: 12px;
        padding: 8px 12px;
    }
    
    .faq-question span {
        font-size: 16px;
    }
}
</style>