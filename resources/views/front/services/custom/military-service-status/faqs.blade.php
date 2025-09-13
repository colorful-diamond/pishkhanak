{{-- Comprehensive Searchable FAQ Section for Military Service Status Inquiry --}}
{{-- سوالات متداول جامع و قابل جستجو برای خدمات استعلام وضعیت نظام وظیفه --}}

<!-- Enhanced FAQ Section with Search and Categories -->
<section class="mt-12 mb-12" id="comprehensive-faqs">
    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8 mb-8">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-dark-sky-700 mb-4 flex items-center justify-center gap-3">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                مرجع کامل سوالات متداول نظام وظیفه
            </h2>
            <p class="text-gray-700 text-lg leading-relaxed">
                بیش از <strong>۶۷ سوال و پاسخ تخصصی</strong> درباره استعلام وضعیت نظام وظیفه، سامانه سخا، و خدمات پیشخوانک
            </p>
        </div>
    </div>

    <!-- FAQ Search and Filter System -->
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
                    id="faq-search" 
                    placeholder="جستجو هوشمند در ۶۷ سوال: کلیدواژه، موضوع یا عبارت..." 
                    class="w-full pl-3 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-right"
                    autocomplete="off"
                    spellcheck="false"
                >
                <!-- Search Shortcuts -->
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 flex items-center gap-2 text-xs text-gray-400">
                    <kbd class="px-2 py-1 bg-gray-100 rounded text-gray-600">Ctrl</kbd>
                    <span>+</span>
                    <kbd class="px-2 py-1 bg-gray-100 rounded text-gray-600">K</kbd>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="flex flex-wrap gap-2">
                <button class="faq-category-btn active px-4 py-2 rounded-lg bg-purple-600 text-white text-sm font-medium transition-colors" data-category="all">
                    همه موضوعات (۶۷)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="general">
                    کلیات (۹)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="sakha">
                    سامانه سخا (۸)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="inquiry">
                    روش‌های استعلام (۸)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="absence">
                    غیبت و جرائم (۷)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="deployment">
                    اعزام (۸)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="additional">
                    اضافه خدمت (۷)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="sms">
                    پیامک (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="technical">
                    فنی (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="legal">
                    قانونی (۴)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="special">
                    موارد خاص (۴)
                </button>
            </div>
        </div>

        <!-- Advanced Search Results Counter -->
        <div id="faq-results" class="mt-4 text-sm text-gray-600 hidden">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><strong id="results-count">0</strong> نتیجه یافت شد در <span id="search-time">0</span> ثانیه</span>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <button id="clear-search" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        پاک کردن جستجو
                    </button>
                    <button id="highlight-toggle" class="px-3 py-1 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg transition-colors">
                        برجسته‌سازی
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Categories Container -->
    <div id="faq-container" class="space-y-8">

        <!-- Category 1: کلیات نظام وظیفه (General Military Service) -->
        <div class="faq-category" data-category="general">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    کلیات نظام وظیفه
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="general" data-keywords="نظام وظیفه چیست تعریف خدمت سربازی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">نظام وظیفه چیست و چه کسانی مشمول آن هستند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>نظام وظیفه یکی از وظایف مقدس و ملی شهروندان ایرانی است که بر اساس قوانین جمهوری اسلامی ایران، کلیه مردان ایرانی با رسیدن به سن ۱۸ سالگی مشمول آن می‌شوند. این خدمت شامل آموزش‌های نظامی و دفاعی برای حفظ امنیت و دفاع از میهن است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="سن مشمولیت ۱۸ سال شروع خدمت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">از چه سنی مشمول خدمت سربازی می‌شوم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>با رسیدن به سن ۱۸ سالگی، کلیه مردان ایرانی مشمول خدمت سربازی می‌شوند و باید اقدامات لازم برای انجام این وظیفه ملی را آغاز کنند. در صورت ادامه تحصیل، امکان دریافت معافیت تحصیلی وجود دارد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="مدت خدمت چقدر زمان دوره سربازی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مدت خدمت سربازی چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>مدت خدمت سربازی معمولاً ۲۴ ماه (دو سال) است. این مدت برای برخی مشاغل خاص یا شرایط ویژه ممکن است متفاوت باشد. همچنین افرادی که تحصیلات عالیه دارند یا در مناطق محروم خدمت کنند ممکن است از کاهش مدت خدمت بهره‌مند شوند.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="معافیت تحصیلی شرایط تحصیل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">معافیت تحصیلی چیست و چه شرایطی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>معافیت تحصیلی امکان تأخیر در انجام خدمت سربازی برای ادامه تحصیل است. این معافیت برای مقاطع کارشناسی، کارشناسی ارشد و دکتری قابل دریافت بوده و نیازمند تمدید سالانه است. دانشجویان باید با مدارک معتبر از دانشگاه، معافیت خود را تمدید کنند.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="کارت پایان خدمت مدرک سربازی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کارت پایان خدمت چه اهمیتی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>کارت پایان خدمت مدرک رسمی اتمام خدمت سربازی است که برای استخدام، دریافت گذرنامه، ازدواج، و بسیاری از امور اداری ضروری محسوب می‌شود. این کارت باید همیشه نزد فرد باشد و در صورت مفقودی باید سریعاً اقدام به دریافت کارت جایگزین شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="محل خدمت انتخاب تعیین مکان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توان محل خدمت سربازی را انتخاب کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>انتخاب محل خدمت معمولاً براساس نیازهای نظامی و شرایط فردی تعیین می‌شود. در برخی موارد خاص مثل داشتن والدین مسن، بیماری خاص، یا شرایط ویژه خانوادگی، امکان درخواست خدمت در نزدیکی محل سکونت وجود دارد که پس از بررسی تصمیم‌گیری می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="مرخصی سربازی تعطیلات استراحت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">در طول خدمت سربازی چه مقدار مرخصی داریم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>سربازان معمولاً هر ماه حق ۲ روز مرخصی عادی دارند که می‌تواند انباشته شود. همچنین مرخصی‌های استثنایی برای موارد اضطراری مثل فوت بستگان، بیماری، یا موارد خاص خانوادگی قابل دریافت است. تعطیلات رسمی و مذهبی نیز جزء مرخصی محاسبه نمی‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="حقوق مزد سرباز پرداخت درآمد">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا در طول خدمت سربازی حقوق دریافت می‌کنیم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>سربازان ماهانه مبلغی به عنوان حقوق دریافت می‌کنند که مبلغ آن هر ساله تعیین و اعلام می‌شود. این مبلغ شامل حقوق پایه، مزایا و کمک‌هزینه‌های مختلف است. همچنین امکانات رفاهی مثل غذا، اسکان و درمان رایگان ارائه می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="بیمه درمان پزشکی سرباز">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">پوشش بیمه و درمان در دوران خدمت چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>سربازان در دوران خدمت تحت پوشش بیمه درمانی قرار دارند و خدمات پزشکی رایگان دریافت می‌کنند. درمان‌های اورژانسی، معاینات عمومی، داروهای ضروری و در صورت نیاز، مراجعه به متخصص فراهم است. خانواده سرباز نیز تا حدودی تحت پوشش قرار می‌گیرد.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 2: سامانه سخا (SAKHA System) -->
        <div class="faq-category" data-category="sakha">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                    سامانه سخا (sakha.epolice.ir)
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="sakha" data-keywords="سامانه سخا چیست تعریف sakha.epolice.ir">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه سخا چیست و چه خدماتی ارائه می‌دهد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>سامانه سخا (sakha.epolice.ir) پلتفرم رسمی خدمات الکترونیک انتظامی است که توسط نیروی انتظامی ایران راه‌اندازی شده. این سامانه خدمات متنوعی شامل استعلام وضعیت نظام وظیفه، درخواست مجوز خروج از کشور، معافیت تحصیلی، و تعویض کارت پایان خدمت را ارائه می‌دهد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sakha" data-keywords="ثبت نام سامانه سخا کد سخا دریافت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه در سامانه سخا ثبت‌نام کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>برای ثبت‌نام در سامانه سخا ابتدا باید به یکی از دفاتر پلیس +۱۰ مراجعه کرده و پس از تحویل مدارک، کد ۵ رقمی سخا را دریافت کنید. سپس با کد ملی (نام کاربری) و کد سخا (کلمه عبور) می‌توانید وارد سامانه شوید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sakha" data-keywords="ورود سامانه سخا مشکل لاگین">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">نمی‌توانم وارد سامانه سخا شوم، چه کار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>ابتدا مطمئن شوید که کد ملی (۱۰ رقم) و کد سخا (۵ رقم) را صحیح وارد کرده‌اید. سپس مرورگر خود را تغییر دهید یا حافظه موقت را پاک کنید. اگر همچنان مشکل دارید، به نزدیکترین دفتر پلیس +۱۰ مراجعه کرده و کد سخا خود را بازیابی کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sakha" data-keywords="فراموشی کد سخا بازیابی رمز">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کد سخا خود را فراموش کرده‌ام، چطور بازیابی کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>برای بازیابی کد سخا باید با مدارک شناسایی (کارت ملی و شناسنامه) به نزدیکترین دفتر پلیس +۱۰ مراجعه کنید. کارشناسان پس از احراز هویت، کد سخا جدید را برای شما صادر می‌کنند. این فرآیند معمولاً در همان روز انجام می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sakha" data-keywords="امنیت سامانه سخا حفاظت کد">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از امنیت حساب کاربری خود در سامانه سخا مطمئن شوم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>هرگز کد سخا خود را با دیگران به اشتراک نگذارید، همیشه از آدرس رسمی sakha.epolice.ir استفاده کنید، پس از استفاده از سیستم خارج شوید، و از کامپیوترهای عمومی برای ورود به حساب خود خودداری کنید. در صورت مشاهده فعالیت مشکوک، فوراً به دفتر پلیس +۱۰ اطلاع دهید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sakha" data-keywords="ساعات کاری سامانه سخا دسترسی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه سخا در چه ساعاتی در دسترس است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>سامانه سخا ۲۴ ساعته و در تمامی روزهای هفته در دسترس است. شما می‌توانید در هر زمانی از شبانه‌روز وارد سیستم شده و از خدمات آن استفاده کنید. البته در ساعات اوج ترافیک ممکن است سرعت سیستم کمتر باشد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sakha" data-keywords="درخواست خروج کشور مجوز سفر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از طریق سامانه سخا درخواست مجوز خروج کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>پس از ورود به سامانه سخا، بخش "خدمات نظام وظیفه" را انتخاب کرده و گزینه "درخواست مجوز خروج از کشور" را کلیک کنید. فرم مربوطه را تکمیل کرده و مدارک لازم را بارگذاری کنید. پس از تأیید، کد رهگیری دریافت خواهید کرد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sakha" data-keywords="بروزرسانی اطلاعات سامانه سخا تغییر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چقدر طول می‌کشد تا اطلاعات در سامانه سخا بروز شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>معمولاً بروزرسانی اطلاعات در سامانه سخا بین ۲۴ تا ۴۸ ساعت پس از انجام تغییرات در دفاتر پلیس +۱۰ انجام می‌شود. در برخی موارد خاص این زمان ممکن است تا ۷۲ ساعت طول بکشد. اگر پس از این مدت تغییری مشاهده نکردید، با دفتر مربوطه تماس بگیرید.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 3: روش‌های استعلام (Inquiry Methods) -->
        <div class="faq-category" data-category="inquiry">
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    روش‌های استعلام وضعیت
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="inquiry" data-keywords="روش استعلام وضعیت چطور چگونه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه روش‌هایی برای استعلام وضعیت نظام وظیفه وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>سه روش اصلی برای استعلام وضعیت نظام وظیفه وجود دارد: ۱) آنلاین از طریق سامانه سخا (sakha.epolice.ir) ۲) پیامک به شماره ۱۱۰۲۰۶۰۱۰ ۳) مراجعه حضوری به دفاتر پلیس +۱۰. روش آنلاین سریع‌ترین و راحت‌ترین گزینه محسوب می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="کد ملی استعلام بدون کد سخا">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توان فقط با کد ملی استعلام گرفت؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>برای استعلام کامل از سامانه سخا نیاز به کد ملی و کد سخا دارید. اما برای استعلام‌های ساده می‌توانید از سرویس پیامکی استفاده کنید که در برخی مواقع امکان استعلام تنها با کد ملی وجود دارد. برای اطلاعات دقیق‌تر، داشتن کد سخا ضروری است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="اطلاعات نمایش استعلام شامل چه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">استعلام وضعیت چه اطلاعاتی را نشان می‌دهد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>استعلام وضعیت اطلاعاتی شامل تاریخ اعزام، محل خدمت، نوع وضعیت (مشمول، معاف، در حال خدمت)، معافیت‌های فعال، تاریخ انقضای معافیت‌ها، میزان کسری یا اضافه خدمت، و سایر جزئیات مربوط به پرونده نظام وظیفه ارائه می‌دهد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="سرعت استعلام چقدر طول زمان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">استعلام آنلاین چقدر زمان می‌برد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>استعلام آنلاین از طریق سامانه سخا معمولاً کمتر از یک دقیقه طول می‌کشد. پس از ورود موفق به سیستم، اطلاعات به صورت فوری نمایش داده می‌شود. استعلام از طریق پیامک نیز معمولاً ظرف چند دقیقه پاسخ داده می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="هزینه استعلام رایگان پولی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا استعلام وضعیت نظام وظیفه هزینه‌ای دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>استعلام وضعیت از طریق سامانه سخا کاملاً رایگان است. استعلام از طریق پیامک تنها هزینه ارسال پیامک عادی را دارد که معمولاً مبلغ ناچیزی است. مراجعه حضوری نیز برای خود استعلام هزینه‌ای نداشته ولی ممکن است هزینه ایاب و ذهاب داشته باشد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="خارج کشور استعلام از خارج">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توان از خارج از کشور استعلام گرفت؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transformation group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>بله، سامانه سخا از تمامی نقاط جهان قابل دسترس است و می‌توانید از طریق اینترنت استعلام بگیرید. البته سرعت دسترسی بسته به کیفیت اینترنت و موقعیت جغرافیایی شما متفاوت خواهد بود. سرویس پیامکی نیز در صورت داشتن خط تلفن ایرانی قابل استفاده است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="دقت اطلاعات صحت استعلام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">تا چه حد می‌توان به اطلاعات استعلام اعتماد کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>اطلاعات سامانه سخا مستقیماً از بانک اطلاعاتی سازمان نظام وظیفه دریافت می‌شود و دارای اعتبار رسمی است. این اطلاعات برای استفاده در ادارات و سازمان‌ها قابل قبول است. البته همیشه امکان بروزرسانی با تأخیر وجود دارد، لذا در موارد مهم توصیه می‌شود با دفتر پلیس +۱۰ نیز تماس بگیرید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="پرینت چاپ گواهی استعلام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توان نتیجه استعلام را چاپ کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>بله، سامانه سخا امکان چاپ گزارش استعلام را فراهم کرده است. این گزارش شامل مهر و امضای الکترونیکی بوده و برای ارائه به ادارات مختلف قابل استفاده است. همچنین می‌توانید از صفحه نمایش اسکرین‌شات بگیرید، اما برای استفاده رسمی حتماً از گزینه چاپ سامانه استفاده کنید.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 4: غیبت و جرائم (Absence and Penalties) -->
        <div class="faq-category" data-category="absence">
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    غیبت سربازی و جرائم
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="absence" data-keywords="غیبت سربازی چیست تعریف">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">غیبت سربازی چیست و چه عواقبی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>غیبت سربازی به وضعیتی گفته می‌شود که فرد در زمان و مکان مشخص شده برای شروع خدمت حاضر نشود. عواقب آن شامل جرائم مالی، اضافه خدمت، محدودیت در دریافت گذرنامه، منع خروج از کشور، و مشکل در استخدام دولتی است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="absence" data-keywords="جریمه غیبت مبلغ هزینه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مبلغ جریمه غیبت سربازی چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>مبلغ جریمه غیبت بر اساس مدت غیبت و مدرک تحصیلی محاسبه می‌شود. برای مثال افراد دارای دیپلم مبلغ پایه، کارشناسی ۱.۵ برابر، کارشناسی ارشد ۲ برابر و دکتری ۲.۵ برابر مبلغ پایه پرداخت می‌کنند. مبلغ دقیق هر ساله تعیین و اعلام می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="absence" data-keywords="رفع غیبت حل مشکل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه می‌توان مشکل غیبت سربازی را حل کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>برای حل مشکل غیبت باید هرچه سریعتر به نزدیکترین دفتر پلیس +۱۰ مراجعه کرده، علت غیبت را توضیح دهید، جرائم مربوطه را پرداخت کنید و تاریخ جدید اعزام را دریافت کنید. تأخیر در اقدام، عواقب بیشتری به همراه خواهد داشت.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="absence" data-keywords="منع خروج کشور غیبت سربازی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا غیبت سربازی باعث منع خروج از کشور می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>بله، یکی از مهم‌ترین عواقب غیبت سربازی منع خروج از کشور است. افراد دارای غیبت سربازی نمی‌توانند گذرنامه دریافت کرده یا از کشور خارج شوند تا زمانی که وضعیت نظام وظیفه خود را تعیین تکلیف کنند.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="absence" data-keywords="استخدام دولتی غیبت محدودیت شغلی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">غیبت سربازی چه تأثیری روی استخدام دولتی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>افراد دارای غیبت سربازی نمی‌توانند در ادارات و سازمان‌های دولتی استخدام شوند. همچنین در بسیاری از شرکت‌های خصوصی و بانک‌ها نیز این موضوع مانع استخدام محسوب می‌شود. لذا حل مشکل غیبت برای فعالیت‌های شغلی ضروری است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="absence" data-keywords="تسهیلات بانکی غیبت وام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا غیبت سربازی مانع دریافت تسهیلات بانکی است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>بله، اکثر بانک‌ها برای پرداخت وام و تسهیلات، کارت پایان خدمت یا معافیت دائم را مطالبه می‌کنند. افراد دارای غیبت سربازی معمولاً نمی‌توانند وام مسکن، خودرو یا سایر تسهیلات بانکی دریافت کنند تا وضعیت نظام وظیفه خود را مشخص کنند.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="absence" data-keywords="عذر قبول غیبت دلیل موجه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا برای غیبت سربازی عذر قبولی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>در برخی موارد خاص مثل بیماری جدی، شرایط اضطراری خانوادگی، یا عدم اطلاع از تاریخ اعزام به دلیل تغییر آدرس، ممکن است کمیسیون‌های مربوطه غیبت را قابل عذر تلقی کنند. اما این موارد نیاز به بررسی دقیق و ارائه مدارک معتبر دارد.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 5: اعزام به خدمت (Deployment Process) -->
        <div class="faq-category" data-category="deployment">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    اعزام به خدمت سربازی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="deployment" data-keywords="مراحل اعزام چطور چگونه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مراحل اعزام به خدمت سربازی چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>مراحل اعزام شامل: ۱) آماده‌سازی مدارک ۲) مراجعه به دفتر پلیس +۱۰ و تحویل مدارک ۳) دریافت کد سخا ۴) صدور برگه سبز اعزام ظرف ۴۸ ساعت ۵) دریافت برگه سفید ۳-۷ روز قبل از اعزام ۶) حضور در واحد نظامی در تاریخ مقرر.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="deployment" data-keywords="مدارک اعزام چه لازم">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه مداركی برای اعزام به خدمت لازم است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>مدارک لازم عبارتند از: عکس ۴×۳ سیاه و سفید، اصل و کپی کارت ملی، تمام صفحات شناسنامه، فرم تکمیل شده وضعیت مشمول، فرم معاینه اولیه پزشکی، آخرین مدرک تحصیلی. در برخی موارد ممکن است مدارک اضافی نیز مطالبه شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="deployment" data-keywords="زمان اعزام تاریخ کی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اعزام‌ها در چه تاریخ‌هایی انجام می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>اعزام‌ها معمولاً در تاریخ ۱۸ هر ماه انجام می‌شود. برای مشمولان دارای مدرک دیپلم، فاصله زمانی بین ارسال دفترچه تا اعزام حدود ۲ ماه است. این برنامه ممکن است در شرایط خاص یا ایام تعطیل دچار تغییر شود که از طریق سامانه سخا اطلاع‌رسانی می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="deployment" data-keywords="برگه سبز اعزام دریافت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">برگه سبز اعزام چیست و چه زمانی دریافت می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>برگه سبز اعزام حاوی اطلاعات اولیه مشمول و مهم‌تر از همه تاریخ اعزام است. این برگه ظرف ۴۸ ساعت پس از تکمیل نهایی ثبت مدارک در دفتر پلیس +۱۰ صادر می‌شود. این برگه برای پیگیری وضعیت و برنامه‌ریزی فردی ضروری است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="deployment" data-keywords="برگه سفید محل آموزش">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">برگه سفید چه اطلاعاتی دارد و کی صادر می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>برگه سفید حاوی مشخصات کامل محل آموزش، آدرس دقیق واحد نظامی، راهنمای حمل و نقل و اطلاعات تماس واحد است. این برگه ۳ تا ۷ روز قبل از تاریخ اعزام از طریق سامانه سخا یا دفاتر پلیس +۱۰ قابل مشاهده و دریافت است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="deployment" data-keywords="تأخیر اعزام عدم حضور">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر در روز اعزام حضور پیدا نکنم چه اتفاقی می‌افتد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>عدم حضور در روز اعزام منجر به ثبت غیبت سربازی خواهد شد که عواقب جدی دارد. باید هرچه سریعتر به دفتر پلیس +۱۰ مراجعه کرده، دلیل غیبت را توضیح داده، جرائم احتمالی را پرداخت کرده و تاریخ جدید اعزام را دریافت کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="deployment" data-keywords="وسایل همراه ممنوع مجاز">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه وسایلی می‌توان همراه به واحد نظامی برد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>وسایل ضروری شامل لباس‌های شخصی، لوازم بهداشتی، دارو (در صورت نیاز)، کتاب، دفترچه و خودکار، تلفن همراه (طبق مقررات واحد)، مقداری پول نقد، و مدارک شناسایی. وسایل ممنوع شامل سلاح سرد، مواد محترقه، مشروبات الکلی و مواد مخدر است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="deployment" data-keywords="تغییر تاریخ اعزام تعویق">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توان تاریخ اعزام را تغییر داد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>تغییر تاریخ اعزام تنها در شرایط خاص و با دلایل موجه امکان‌پذیر است. موارد مثل بیماری جدی، شرایط اضطراری خانوادگی، یا ادامه تحصیل ممکن است مورد بررسی قرار گیرد. برای این کار باید به دفتر پلیس +۱۰ مراجعه کرده و مدارک لازم را ارائه دهید.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 6: اضافه خدمت (Additional Service) -->
        <div class="faq-category" data-category="additional">
            <div class="bg-gradient-to-r from-rose-600 to-rose-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    اضافه خدمت سربازی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="additional" data-keywords="اضافه خدمت چیست تعریف">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اضافه خدمت سربازی چیست و چه علل دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-rose-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>اضافه خدمت جریمه‌ای است که به دلیل غیبت از خدمت، ارتکاب تخلفات انضباطی، یا سایر مسائل قانونی اعمال می‌شود. این مدت اضافی که معمولاً بین ۹۰ تا ۱۸۰ روز است، باید پس از اتمام خدمت اصلی انجام شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="مدت اضافه خدمت چقدر زمان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مدت اضافه خدمت چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-rose-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>مدت اضافه خدمت بسته به علت آن متفاوت است: برای غیبت تا ۳۰ روز: ۹۰ روز اضافه خدمت، برای غیبت ۳۰ تا ۹۰ روز: ۱۲۰ روز اضافه خدمت، برای غیبت بیش از ۹۰ روز: ۱۸۰ روز اضافه خدمت. تخلفات انضباطی نیز بسته به شدت بین ۶۰ تا ۱۸۰ روز متغیر است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="استعلام اضافه خدمت چطور">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه می‌توان از میزان اضافه خدمت استعلام گرفت؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-rose-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>برای استعلام اضافه خدمت می‌توانید از سه روش استفاده کنید: ۱) ارسال علامت سوال (?) به شماره ۱۱۰۲۰۶۰۱۰ و پیگیری با کد ملی و سخا ۲) ورود به سامانه سخا و مراجعه به بخش خدمات نظام وظیفه ۳) مراجعه حضوری به دفتر پلیس +۱۰.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="کاهش اضافه خدمت لغو تجدیدنظر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توان اضافه خدمت را کاهش یا لغو کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-rose-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>در برخی موارد خاص امکان کاهش یا لغو اضافه خدمت وجود دارد. شرایط ویژه خانوادگی، مسائل پزشکی جدی، یا ارائه دلایل موجه برای غیبت ممکن است مورد بررسی کمیسیون‌های مربوطه قرار گیرد. برای این کار باید درخواست کتبی همراه مدارک معتبر ارائه دهید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="انجام اضافه خدمت زمان کجا">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اضافه خدمت کجا و چه زمانی انجام می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-rose-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>اضافه خدمت معمولاً پس از اتمام دوره اصلی خدمت یا در ادامه همان واحد انجام می‌شود. زمان انجام و محل اضافه خدمت توسط مراجع نظامی تعیین می‌گردد. این اطلاعات از طریق احضاریه یا سامانه سخا به اطلاع مشمولان رسانده می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="تأثیر اضافه خدمت کارت پایان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اضافه خدمت چه تأثیری روی کارت پایان خدمت دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-rose-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>تا زمانی که اضافه خدمت انجام نشود، کارت پایان خدمت صادر نخواهد شد. این موضوع مانع دریافت گذرنامه، استخدام، و انجام بسیاری از امور اداری خواهد بود. لذا انجام اضافه خدمت برای تکمیل پرونده نظام وظیفه ضروری است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="جایگزین اضافه خدمت پول نقدی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توان به جای اضافه خدمت جریمه نقدی پرداخت کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-rose-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>در حال حاضر امکان جایگزینی اضافه خدمت با جریمه نقدی وجود ندارد و باید به صورت فیزیکی انجام شود. البته قوانین در این زمینه ممکن است تغییر کند که از طریق کانال‌های رسمی اطلاع‌رسانی خواهد شد.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 7: پیامک (SMS Services) -->
        <div class="faq-category" data-category="sms">
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    سرویس پیامک ۱۱۰۲۰۶۰۱۰
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="sms" data-keywords="پیامک ۱۱۰۲۰۶۰۱۰ چطور استفاده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از سرویس پیامک ۱۱۰۲۰۶۰۱۰ استفاده کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>ابتدا علامت سوال (?) را به شماره ۱۱۰۲۰۶۰۱۰ ارسال کنید تا منوی خدمات را دریافت کنید. سپس برای استعلام وضعیت: "۱" + کد سخا، برای پیگیری پرونده: "۲" + کد سخا + کد ملی، برای تعویض کارت: "۳" + کد سخا + کد ملی ارسال کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sms" data-keywords="هزینه پیامک رایگان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سرویس پیامک نظام وظیفه چه هزینه‌ای دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>سرویس پیامکی نظام وظیفه تنها هزینه ارسال پیامک عادی را دارد که مطابق تعرفه اپراتور شما محاسبه می‌شود. معمولاً این هزینه بسیار ناچیز بوده و برای دریافت پاسخ هزینه اضافی دریافت نمی‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sms" data-keywords="دریافت پیامک تأخیر زمان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چقدر طول می‌کشد تا پاسخ پیامک را دریافت کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>معمولاً پاسخ پیامک ظرف ۱ تا ۵ دقیقه دریافت می‌شود. در ساعات اوج ترافیک یا مشکلات شبکه ممکن است این زمان تا ۱۵ دقیقه طول بکشد. اگر پس از ۳۰ دقیقه پاسخی دریافت نکردید، مجدداً تلاش کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sms" data-keywords="مشکل پیامک عدم دریافت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">پیامک جواب نمی‌دهد، چه کار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>ابتدا شارژ سیم‌کارت و فضای صندوق پیام خود را بررسی کنید. سپس از سیم‌کارت دیگری تست کنید. اگر همچنان مشکل دارید، چند ساعت صبر کرده و مجدداً تلاش کنید. در نهایت می‌توانید از سامانه سخا یا مراجعه حضوری استفاده کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sms" data-keywords="فرمت پیامک نحوه ارسال">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">فرمت صحیح ارسال پیامک چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>برای دریافت منو: "?" - برای استعلام وضعیت: "1 کد_سخا" - برای پیگیری پرونده: "2 کد_سخا کد_ملی" - برای تعویض کارت: "3 کد_سخا کد_ملی". مثال: "1 12345" یا "2 12345 1234567890". بین اعداد یک فاصله قرار دهید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sms" data-keywords="اپراتور پیامک همه سیمکارت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا سرویس پیامک با تمام اپراتورها کار می‌کند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>بله، سرویس پیامک ۱۱۰۲۰۶۰۱۰ با تمامی اپراتورهای موبایل ایران (همراه اول، ایرانسل، رایتل) کار می‌کند. تنها شرط داشتن شارژ کافی برای ارسال پیامک است. سرعت دریافت پاسخ ممکن است بین اپراتورها کمی متفاوت باشد.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 8: مسائل فنی (Technical Issues) -->
        <div class="faq-category" data-category="technical">
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    مسائل فنی و رفع عیب
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="technical" data-keywords="مرورگر بهترین مناسب سامانه سخا">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کدام مرورگر برای سامانه سخا مناسب‌تر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>سامانه سخا با اکثر مرورگرهای مدرن سازگار است. گوگل کروم، فایرفاکس، اج، و سافاری همگی مناسب هستند. توصیه می‌شود از آخرین نسخه مرورگر استفاده کرده و جاوا اسکریپت فعال باشد. در صورت مشکل، حافظه موقت مرورگر را پاک کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="پاک کردن حافظه موقت cache">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه حافظه موقت مرورگر را پاک کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>در کروم: Ctrl+Shift+Del یا تنظیمات > حریم خصوصی > پاک کردن داده‌ها. در فایرفاکس: Ctrl+Shift+Del یا تنظیمات > حریم خصوصی > پاک کردن تاریخچه. در اج: Ctrl+Shift+Del یا تنظیمات > حریم خصوصی > انتخاب آنچه پاک شود. حتماً کوکی‌ها و فایل‌های موقت را انتخاب کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="موبایل تبلت گوشی سامانه سخا">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توان از موبایل و تبلت به سامانه سخا دسترسی داشت؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>بله، سامانه سخا برای استفاده روی دستگاه‌های موبایل و تبلت بهینه‌سازی شده است. می‌توانید از مرورگر گوشی یا تبلت خود استفاده کنید. توصیه می‌شود از مرورگرهای بروز مثل کروم موبایل، سافاری، یا فایرفاکس موبایل استفاده کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="خطا صفحه پیدا نشد 404">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">خطای "صفحه پیدا نشد" می‌گیرم، چه کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>ابتدا آدرس sakha.epolice.ir را دقیق وارد کنید. سپس اتصال اینترنت خود را بررسی کنید. اگر مشکل همچنان ادامه دارد، چند دقیقه صبر کرده و مجدداً تلاش کنید. ممکن است سامانه موقتاً در حال تعمیر یا بروزرسانی باشد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="کند بودن سامانه سخا سرعت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه سخا خیلی کند کار می‌کند، چرا؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>کندی سامانه ممکن است به دلیل ترافیک بالا، مشکل اینترنت شما، یا تعمیر موقت سیستم باشد. در ساعات اوج (۹-۱۲ صبح و ۱۴-۱۷) ترافیک بیشتر است. سعی کنید در ساعات مختلف تلاش کرده یا از اتصال سریعتری استفاده کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="امنیت سامانه سخا SSL HTTPS">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از امنیت اتصال به سامانه سخا مطمئن شوم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>همیشه در نوار آدرس مرورگر بررسی کنید که آدرس https://sakha.epolice.ir شروع شود و آیکون قفل نمایش داده شود. از شبکه‌های وای‌فای عمومی برای ورود به حساب خود استفاده نکنید. همیشه پس از کار از سیستم خارج شوید.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 9: مسائل حقوقی (Legal and Regulatory) -->
        <div class="faq-category" data-category="legal">
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                    مسائل حقوقی و قانونی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="legal" data-keywords="قانون نظام وظیفه مبنای قانونی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مبنای قانونی نظام وظیفه در ایران چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>نظام وظیفه بر اساس قانون اساسی جمهوری اسلامی ایران و قوانین مصوب مجلس شورای اسلامی تعریف شده است. اصل ۱۴۳ قانون اساسی و قانون خدمت وظیفه عمومی مصوب ۱۳۵۸ از جمله مهم‌ترین مستندات قانونی هستند.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="حقوق سرباز قانونی شکایت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">در صورت نقض حقوق در دوران سربازی چه کار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>ابتدا موضوع را با فرماندهان واحد در میان بگذارید. در صورت عدم رسیدگی، می‌توانید به بازرسی نیروهای مسلح، دادستانی نظامی، یا سازمان بازرسی کل کشور شکایت کنید. همچنین می‌توانید از طریق تماس با خط ارتباطی ۱۹۰ اعتراض خود را ثبت کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="معافیت قانونی کامل دائم">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه کسانی از معافیت دائم برخوردارند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>معافیت دائم شامل افراد دارای نقص عضو یا بیماری صعب‌العلاج، تنها نان‌آور خانواده با شرایط خاص، افراد دارای والدین معلول یا مسن بدون سرپرست، و برخی مشاغل خاص مثل روحانیان و اعضای شورای نگهبان است. تشخیص آن توسط کمیسیون‌های مربوطه انجام می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="تبعیت چندگانه خارجی دو ملیت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">وضعیت نظام وظیفه افراد دارای تابعیت مضاعف چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>ایرانیان دارای تابعیت مضاعف که قبل از سن ۱۸ سالگی از کشور خارج شده و در خارج اقامت دارند، در صورتی که خدمت نظامی کشور محل اقامت را انجام داده باشند، ممکن است از معافیت برخوردار شوند. این موضوع نیازمند بررسی موردی و ارائه مدارک معتبر است.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 10: موارد خاص (Special Cases) -->
        <div class="faq-category" data-category="special">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    موارد خاص و استثناء
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="special" data-keywords="خدمت بدل نقدی جایگزین">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا امکان خدمت بدل یا پرداخت نقدی به جای سربازی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-teal-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>در حال حاضر قانون خدمت بدل یا پرداخت نقدی به جای انجام خدمت سربازی در ایران وجود ندارد. تمامی مشمولان باید خدمت سربازی خود را به صورت فیزیکی انجام دهند. تنها معافیت‌های قانونی می‌توانند فرد را از انجام این وظیفه بر کنار کنند.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="حین خدمت ازدواج عروسی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توان در حین خدمت سربازی ازدواج کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-teal-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>بله، سربازان می‌توانند با کسب مجوز از فرماندهی واحد خود ازدواج کنند. معمولاً مرخصی ویژه‌ای برای مراسم عروسی در نظر گرفته می‌شود. ازدواج در دوران خدمت هیچ تأثیری روی مدت یا شرایط خدمت نخواهد داشت.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="نقل مکان تغییر آدرس">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر در حین فرآیند نقل مکان کنم چه کار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-teal-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>فوراً تغییر آدرس را به دفتر پلیس +۱۰ اطلاع دهید. همچنین در سامانه سخا نیز آدرس جدید را بروزرسانی کنید. این کار برای دریافت احضاریه‌ها و اطلاعیه‌های مهم ضروری است. عدم اطلاع‌رسانی ممکن است منجر به غیبت غیرعمدی شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="تغییر نام خانوادگی سربازی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر نام خانوادگی ام تغییر کرد چه اقدامی باید انجام دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-teal-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer mt-4 text-gray-700 text-base leading-relaxed hidden">
                        <p>با مدارک جدید شناسایی (کارت ملی و شناسنامه با نام جدید) به دفتر پلیس +۱۰ مراجعه کرده و درخواست تصحیح اطلاعات پرونده را بدهید. این فرآیند ممکن است چند روز طول بکشد. مهم است که این کار را قبل از اعزام انجام دهید.</p>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Search Results Message -->
    <div id="no-results" class="hidden text-center py-8">
        <div class="text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m3-3h-3"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-1">نتیجه‌ای یافت نشد</h3>
            <p class="text-gray-500">لطفاً کلیدواژه دیگری امتحان کنید</p>
        </div>
    </div>

</section>

<style>
    .faq-chevron {
        transition: transform 0.3s ease;
    }
    
    .faq-question.active .faq-chevron {
        transform: rotate(180deg);
    }
    
    .faq-answer {
        transition: all 0.3s ease;
        max-height: 0;
        overflow: hidden;
    }
    
    .faq-answer.show {
        max-height: 1000px;
        padding-top: 1rem;
    }
    
    .faq-category-btn.active {
        background-color: #8b5cf6;
        color: white;
    }
    
    .faq-category-btn.active:hover {
        background-color: #7c3aed;
    }
    
    /* Advanced Search Enhancements */
    .search-highlight {
        background-color: #fef3c7;
        color: #92400e;
        padding: 0 0.2em;
        border-radius: 0.25rem;
        font-weight: 600;
        box-shadow: 0 0 0 2px #f59e0b20;
    }
    
    .faq-item.search-match {
        background-color: #fef3c750;
        border-left: 4px solid #f59e0b;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    #faq-search:focus {
        box-shadow: 0 0 0 3px #8b5cf620;
    }
    
    .search-loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
    /* Keyboard Navigation */
    .faq-item.keyboard-focused {
        outline: 2px solid #8b5cf6;
        outline-offset: 2px;
    }
    
    /* Persian Text Optimization */
    .faq-question h4, .faq-answer p {
        text-align: right;
        direction: rtl;
        line-height: 1.8;
        font-feature-settings: "kern" 1;
    }
    
    /* Quick Access Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .faq-item {
        animation: fadeInUp 0.3s ease-out;
        transition: all 0.3s ease;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced FAQ Search functionality with Persian text support
    const searchInput = document.getElementById('faq-search');
    const faqItems = document.querySelectorAll('.faq-item');
    const faqCategories = document.querySelectorAll('.faq-category');
    const categoryButtons = document.querySelectorAll('.faq-category-btn');
    const resultsCounter = document.getElementById('results-count');
    const resultsDiv = document.getElementById('faq-results');
    const noResultsDiv = document.getElementById('no-results');
    const clearSearchBtn = document.getElementById('clear-search');
    const highlightToggle = document.getElementById('highlight-toggle');
    const searchTimeSpan = document.getElementById('search-time');
    
    let currentCategory = 'all';
    let highlightEnabled = true;
    let searchTimeout;
    let keyboardFocusIndex = -1;
    
    // Persian text normalization for better search
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
            const question = item.querySelector('.faq-question h4');
            const answer = item.querySelector('.faq-answer p');
            
            if (question) question.innerHTML = question.textContent;
            if (answer) answer.innerHTML = answer.textContent;
            
            item.classList.remove('search-match');
        });
    }
    
    // Enhanced search algorithm with fuzzy matching
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
    
    // FAQ Toggle functionality with keyboard support
    faqItems.forEach((item, index) => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        
        question.addEventListener('click', () => toggleFAQ(item, question, answer));
        
        // Keyboard accessibility
        question.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleFAQ(item, question, answer);
            }
        });
        
        // Add tabindex for keyboard navigation
        question.setAttribute('tabindex', '0');
        question.setAttribute('role', 'button');
        question.setAttribute('aria-expanded', 'false');
    });
    
    function toggleFAQ(item, question, answer) {
        const isActive = question.classList.contains('active');
        
        // Close all other answers
        faqItems.forEach(otherItem => {
            const otherQuestion = otherItem.querySelector('.faq-question');
            const otherAnswer = otherItem.querySelector('.faq-answer');
            otherQuestion.classList.remove('active');
            otherAnswer.classList.remove('show');
            otherQuestion.setAttribute('aria-expanded', 'false');
        });
        
        // Toggle current answer
        if (!isActive) {
            question.classList.add('active');
            answer.classList.add('show');
            question.setAttribute('aria-expanded', 'true');
            
            // Smooth scroll to the opened FAQ
            setTimeout(() => {
                item.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }
    }
    
    // Category filter functionality
    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            currentCategory = button.dataset.category;
            filterFAQs();
        });
    });
    
    // Enhanced search with debouncing
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterFAQs();
        }, 200); // 200ms debounce
    });
    
    // Main filtering function with performance timing
    function filterFAQs() {
        const startTime = performance.now();
        const searchTerm = searchInput.value.trim();
        let visibleCount = 0;
        
        // Remove previous highlights
        removeHighlights();
        
        faqCategories.forEach(category => {
            const categoryItems = category.querySelectorAll('.faq-item');
            let categoryHasVisible = false;
            
            categoryItems.forEach(item => {
                const questionElement = item.querySelector('.faq-question h4');
                const answerElement = item.querySelector('.faq-answer p');
                const question = questionElement ? questionElement.textContent : '';
                const answer = answerElement ? answerElement.textContent : '';
                const keywords = item.dataset.keywords || '';
                const itemCategory = item.dataset.category;
                
                // Advanced search matching
                const matchesSearch = !searchTerm || 
                                    advancedSearch(question, searchTerm) || 
                                    advancedSearch(answer, searchTerm) || 
                                    advancedSearch(keywords, searchTerm);
                
                const matchesCategory = currentCategory === 'all' || itemCategory === currentCategory;
                const isVisible = matchesSearch && matchesCategory;
                
                if (isVisible) {
                    item.style.display = 'block';
                    categoryHasVisible = true;
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
            
            // Show/hide category based on visible items
            if (categoryHasVisible && (currentCategory === 'all' || category.dataset.category === currentCategory)) {
                category.style.display = 'block';
            } else {
                category.style.display = 'none';
            }
        });
        
        // Calculate search time
        const endTime = performance.now();
        const searchTime = ((endTime - startTime) / 1000).toFixed(3);
        searchTimeSpan.textContent = searchTime;
        
        // Update results counter
        if (searchTerm) {
            resultsDiv.classList.remove('hidden');
            resultsCounter.textContent = visibleCount;
        } else {
            resultsDiv.classList.add('hidden');
        }
        
        // Show no results message
        if (visibleCount === 0 && (searchTerm || currentCategory !== 'all')) {
            noResultsDiv.classList.remove('hidden');
        } else {
            noResultsDiv.classList.add('hidden');
        }
    }
    
    // Clear search functionality
    clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        currentCategory = 'all';
        
        // Reset active category button
        categoryButtons.forEach(btn => btn.classList.remove('active'));
        categoryButtons[0].classList.add('active');
        
        filterFAQs();
        searchInput.focus();
    });
    
    // Toggle highlighting
    highlightToggle.addEventListener('click', () => {
        highlightEnabled = !highlightEnabled;
        highlightToggle.textContent = highlightEnabled ? 'برجسته‌سازی' : 'بدون برجسته‌سازی';
        highlightToggle.classList.toggle('bg-purple-100');
        highlightToggle.classList.toggle('bg-gray-100');
        filterFAQs();
    });
    
    // Keyboard shortcuts
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
                clearSearchBtn.click();
            }
        }
    });
    
    // Initialize with all categories visible
    filterFAQs();
});
</script>