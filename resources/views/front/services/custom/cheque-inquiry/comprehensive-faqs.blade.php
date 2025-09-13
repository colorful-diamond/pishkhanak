{{-- Comprehensive Searchable FAQ Section for Cheque Inquiry Service --}}
{{-- سوالات متداول جامع و قابل جستجو برای خدمات استعلام چک صیادی --}}

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
                بیش از <strong>۵۰ سوال و پاسخ تخصصی</strong> درباره استعلام چک صیادی، سامانه صیاد، و خدمات پیشخوانک
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
                    همه موضوعات (۵۳)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="inquiry">
                    استعلام چک (۸)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="sayad">
                    سامانه صیاد (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="colors">
                    رنگ‌بندی (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="returned">
                    چک برگشتی (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="costs">
                    هزینه‌ها (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="technical">
                    مسائل فنی (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="security">
                    امنیت (۳)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="legal">
                    حقوقی (۳)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="additional">
                    موارد خاص (۱۵)
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

        <!-- Category 1: استعلام چک صیادی (Cheque Inquiry) -->
        <div class="faq-category" data-category="inquiry">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    استعلام چک صیادی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="inquiry" data-keywords="استعلام چک صیادی چیست تعریف">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">استعلام چک صیادی چیست و چه اطلاعاتی ارائه می‌دهد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>استعلام چک صیادی فرآیند بررسی وضعیت و اعتبار چک از طریق <strong>سامانه رسمی صیاد بانک مرکزی</strong> است. این سامانه اطلاعات کاملی شامل:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>وضعیت رنگی صاحب چک (سفید، زرد، نارنجی، قهوه‌ای، قرمز)</li>
                            <li>تعداد و مبلغ کل چک‌های برگشتی</li>
                            <li>تاریخ آخرین چک برگشتی</li>
                            <li>نام و نام خانوادگی صاحب چک</li>
                            <li>وضعیت رفع سوءاثر</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="کد 16 رقمی چک پیدا کردن">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کد ۱۶ رقمی چک را از کجا پیدا کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        کد ۱۶ رقمی چک معمولاً در <strong>پشت چک</strong> یا در <strong>قسمت پایین چک</strong> قرار دارد. این کد با حروف SAYAD شروع نمی‌شود و صرفاً شامل ۱۶ رقم است. اگر چک قدیمی است (قبل از سال ۱۳۹۶) ممکن است این کد وجود نداشته باشد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="چک قدیمی استعلام 1396">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا چک‌های قدیمی (قبل از سال ۱۳۹۶) قابل استعلام هستند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، چک‌های صادر شده قبل از سال ۱۳۹۶ قابل استعلام آنلاین نیستند.</strong> سامانه صیاد از سال ۱۳۹۶ راه‌اندازی شده و تنها چک‌هایی که پس از این تاریخ صادر شده‌اند، کد ۱۶ رقمی دارند و در سامانه ثبت هستند.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="چندین چک همزمان دسته‌ای">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم چندین چک را همزمان استعلام کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، پیشخوانک امکان استعلام دسته‌ای چندین چک را فراهم کرده است.</strong> شما می‌توانید کدهای ۱۶ رقمی چندین چک را همزمان وارد کرده و نتایج را یکجا دریافت کنید. این ویژگی برای فروشندگان و کسب‌وکارها بسیار مفید است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="زمان استعلام سرعت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">استعلام چک چقدر زمان می‌برد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        استعلام چک در پیشخوانک <strong>کمتر از ۱۰ ثانیه</strong> انجام می‌شود. با دسترسی مستقیم به سامانه صیاد بانک مرکزی، اطلاعات به‌روز و دقیق را فوراً دریافت خواهید کرد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="کد ملی استعلام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم با کد ملی استعلام چک کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، امکان استعلام کلی وضعیت چک با کد ملی وجود دارد.</strong> این روش به شما کمک می‌کند تا وضعیت کلی اعتبار چکی یک شخص را بدون داشتن کد چک مشخص بررسی کنید. اما برای اطلاعات دقیق هر چک، نیاز به کد ۱۶ رقمی است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="چک جعلی تشخیص">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه چک جعلی را تشخیص دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        چک‌های جعلی معمولاً این مشخصات را ندارند:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>کد ۱۶ رقمی معتبر</strong> که در سامانه صیاد ثبت باشد</li>
                            <li>کیفیت چاپ مناسب و عدم وجود لکه یا تغییر</li>
                            <li>مطابقت اطلاعات چک با نتایج استعلام</li>
                            <li>وجود ویژگی‌های امنیتی بانک صادرکننده</li>
                        </ul>
                        <p class="mt-3"><em>همیشه قبل از پذیرش چک، از طریق پیشخوانک استعلام کنید.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="چک تاریخ‌دار آینده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا چک‌های تاریخ‌دار (آینده) قابل استعلام هستند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، چک‌های تاریخ‌دار نیز قابل استعلام هستند.</strong> حتی اگر تاریخ سررسید چک به آینده باشد، می‌توانید وضعیت اعتباری صاحب چک و تاریخچه چک‌های قبلی او را مشاهده کنید. این کار برای ارزیابی ریسک بسیار مهم است.
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 2: سامانه صیاد (SAYAD System) -->
        <div class="faq-category" data-category="sayad">
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                    سامانه صیاد بانک مرکزی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="sayad" data-keywords="صیاد چیست تاریخچه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه صیاد چیست و چه زمانی راه‌اندازی شد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>سامانه صیاد (SAYAD)</strong> سامانه ثبت اطلاعات چک بانک مرکزی است که در <strong>سال ۱۳۹۶ راه‌اندازی شد.</strong> هدف این سامانه کاهش چک‌های بلامحل و افزایش شفافیت در سیستم پرداخت کشور است. تمام چک‌های صادر شده از این تاریخ در این سامانه ثبت می‌شوند.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sayad" data-keywords="صیاد دسترسی مستقیم">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم مستقیماً از سامانه صیاد استعلام کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        سامانه رسمی صیاد برای عموم مردم دسترسی مستقیم ندارد و تنها از طریق <strong>پلتفرم‌های مجاز</strong> مثل پیشخوانک قابل دسترسی است. این کار برای <em>حفظ امنیت اطلاعات</em> و <strong>جلوگیری از سوءاستفاده</strong> انجام می‌شود.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sayad" data-keywords="صیاد بروزرسانی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اطلاعات سامانه صیاد چقدر به‌روز است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        اطلاعات سامانه صیاد <strong>۲۴ ساعته و لحظه‌ای</strong> بروزرسانی می‌شود. به محض برگشت یا رفع سوءاثر چک، این اطلاعات در سامانه ثبت و از طریق پیشخوانک قابل مشاهده است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sayad" data-keywords="رفع سوءاثر صیاد">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رفع سوءاثر در سامانه صیاد چگونه انجام می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای رفع سوءاثر باید:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li>مبلغ چک برگشتی را به همراه جریمه پرداخت کنید</li>
                            <li>رسید پرداخت را به بانک مربوطه ارائه دهید</li>
                            <li>بانک وضعیت رفع سوءاثر را در صیاد ثبت کند</li>
                            <li>تغییر وضعیت طی ۲۴-۴۸ ساعت در استعلام نمایش داده شود</li>
                        </ol>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sayad" data-keywords="صیاد امنیت حریم خصوصی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">امنیت اطلاعات در سامانه صیاد چگونه تضمین می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        سامانه صیاد از <strong>بالاترین استانداردهای امنیتی</strong> برخوردار است:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>رمزنگاری اطلاعات با استانداردهای بین‌المللی</li>
                            <li>دسترسی محدود از طریق پلتفرم‌های مجاز</li>
                            <li>نظارت مستمر بانک مرکزی</li>
                            <li>عدم ذخیره‌سازی اطلاعات در سرورهای خارجی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sayad" data-keywords="بانک‌ها صیاد اتصال">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا همه بانک‌ها به سامانه صیاد متصل هستند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، اتصال به سامانه صیاد برای همه بانک‌ها و موسسات اعتباری الزامی است.</strong> این شامل بانک‌های دولتی، خصوصی، و موسسات اعتباری می‌شود. هیچ مؤسسه‌ای حق صدور چک بدون ثبت در صیاد را ندارد.
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 3: رنگ‌بندی چک (Color Coding) -->
        <div class="faq-category" data-category="colors">
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                    </svg>
                    سیستم رنگ‌بندی چک
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="colors" data-keywords="رنگ سفید چک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رنگ سفید در چک به چه معناست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>رنگ سفید یعنی صاحب چک هیچ چک برگشتی ندارد</strong> و از اعتبار کامل برخوردار است. این بهترین وضعیت برای پذیرش چک محسوب می‌شود و ریسک آن تقریباً صفر است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="colors" data-keywords="رنگ زرد چک معنی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رنگ زرد چه معنایی دارد و آیا ریسکی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        رنگ زرد نشان‌دهنده وجود <strong>یک چک برگشتی</strong> یا <strong>بدهی تا ۵ میلیون تومان</strong> است. این وضعیت ریسک کمی دارد اما نیاز به دقت بیشتر در پذیرش چک است. بهتر است با صاحب چک درباره علت چک برگشتی صحبت کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="colors" data-keywords="رنگ نارنجی قهوه‌ای">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">تفاوت رنگ نارنجی و قهوه‌ای چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                                <strong class="text-orange-800">رنگ نارنجی:</strong>
                                <ul class="list-disc mr-6 mt-2 text-sm">
                                    <li>۲ تا ۴ چک برگشتی</li>
                                    <li>یا تا ۲۰ میلیون تومان بدهی</li>
                                    <li>ریسک متوسط</li>
                                </ul>
                            </div>
                            <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                                <strong class="text-amber-800">رنگ قهوه‌ای:</strong>
                                <ul class="list-disc mr-6 mt-2 text-sm">
                                    <li>۵ تا ۱۰ چک برگشتی</li>
                                    <li>یا تا ۵۰ میلیون تومان بدهی</li>
                                    <li>ریسک بالا</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="colors" data-keywords="رنگ قرمز خطرناک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رنگ قرمز چقدر خطرناک است و باید چه کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <strong class="text-red-800">رنگ قرمز نشان‌دهنده خطر بالا:</strong>
                            <ul class="list-disc mr-6 mt-3 space-y-1">
                                <li>بیش از ۱۰ چک برگشتی</li>
                                <li>یا بدهی بیش از ۵۰ میلیون تومان</li>
                                <li><strong>توصیه: چک را نپذیرید</strong></li>
                                <li>در صورت اضطرار، ضمانت مطمئن دریافت کنید</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="colors" data-keywords="تغییر رنگ زمان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رنگ چک چقدر طول می‌کشد تا تغییر کند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        تغییر رنگ چک بستگی به نوع تغییر دارد:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>چک برگشتی جدید:</strong> فوری تا ۲۴ ساعت</li>
                            <li><strong>رفع سوءاثر:</strong> ۲۴ تا ۴۸ ساعت پس از پرداخت</li>
                            <li><strong>بهبود طبیعی:</strong> بر اساس گذشت زمان و عدم چک جدید</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="colors" data-keywords="رنگ محاسبه الگوریتم">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رنگ چک بر اساس چه فاکتورهایی محاسبه می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        الگوریتم رنگ‌بندی این فاکتورها را در نظر می‌گیرد:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>تعداد چک‌های برگشتی</strong></li>
                            <li><strong>مبلغ کل بدهی</strong></li>
                            <li><strong>تاریخ آخرین چک برگشتی</strong></li>
                            <li><strong>وضعیت رفع سوءاثر</strong></li>
                            <li><em>فاکتورهای ریسک دیگر (محرمانه)</em></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 4: چک برگشتی (Returned Cheques) -->
        <div class="faq-category" data-category="returned">
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    چک‌های برگشتی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="returned" data-keywords="چک برگشتی علت دلایل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">دلایل اصلی برگشت چک چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        دلایل اصلی برگشت چک عبارتند از:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>عدم کفایت موجودی</strong> (رایج‌ترین دلیل)</li>
                            <li><em>مغایرت امضا</em></li>
                            <li><strong>انقضای تاریخ چک</strong> (بیش از ۶ ماه)</li>
                            <li><em>مسدود بودن حساب</em></li>
                            <li><strong>خرابی یا پاره شدن چک</strong></li>
                            <li><em>عدم تطابق اطلاعات با سامانه صیاد</em></li>
                        </ol>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="returned" data-keywords="چک برگشتی مدت زمان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چک برگشتی چقدر در سامانه باقی می‌ماند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        اطلاعات چک برگشتی <strong>۶ سال در سامانه صیاد باقی می‌ماند</strong>، مگر اینکه سوءاثر آن رفع شود. این مدت از تاریخ برگشت چک محاسبه می‌شود و تا زمان رفع سوءاثر یا انقضای ۶ سال ادامه دارد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="returned" data-keywords="جریمه چک برگشتی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">جریمه چک برگشتی چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        جریمه چک برگشتی شامل:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>اصل مبلغ چک</strong></li>
                            <li><strong>جریمه دیرکرد:</strong> حداقل ۶٪ و حداکثر ۱۸٪ سالانه</li>
                            <li><em>هزینه‌های اداری بانک</em></li>
                            <li><strong>خسارت تأخیر</strong> (در صورت توافق)</li>
                        </ul>
                        <p class="mt-3 text-sm text-gray-600">میزان دقیق جریمه بستگی به بانک و مدت زمان تأخیر دارد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="returned" data-keywords="چک برگشتی پیگیری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه چک برگشتی خود را پیگیری کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای پیگیری چک برگشتی:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li>با کد ملی خود از <strong>پیشخوانک استعلام کنید</strong></li>
                            <li>به بانک صادرکننده چک مراجعه کنید</li>
                            <li>مبلغ و جریمه را محاسبه و پرداخت کنید</li>
                            <li>رسید پرداخت را نگهداری کنید</li>
                            <li>پس از ۴۸ ساعت مجدداً استعلام کنید</li>
                        </ol>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="returned" data-keywords="چک برگشتی تأثیر اعتبار">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چک برگشتی چه تأثیری بر اعتبار بانکی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        چک برگشتی تأثیرات منفی بر اعتبار دارد:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>کاهش امتیاز اعتباری</strong> در بانک‌ها</li>
                            <li><em>محدودیت در دریافت تسهیلات</em></li>
                            <li><strong>افزایش ضمانت‌های مورد نیاز</strong></li>
                            <li><em>مشکل در باز کردن حساب جدید</em></li>
                            <li><strong>کاهش اعتماد در معاملات تجاری</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="returned" data-keywords="چک برگشتی جلوگیری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از برگشت چک جلوگیری کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        روش‌های جلوگیری از برگشت چک:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>همیشه موجودی کافی نگه دارید</strong></li>
                            <li>از تاریخ انقضای چک آگاه باشید</li>
                            <li>اطلاعات چک را در صیاد ثبت کنید</li>
                            <li>امضای خود را یکسان نگه دارید</li>
                            <li>از چک‌های معتبر و سالم استفاده کنید</li>
                            <li>وضعیت حساب خود را مرتب بررسی کنید</li>
                        </ol>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 5: هزینه‌ها و پرداخت (Costs and Payment) -->
        <div class="faq-category" data-category="costs">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    هزینه‌ها و پرداخت
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="costs" data-keywords="هزینه استعلام چک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">هزینه استعلام چک در پیشخوانک چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        هزینه استعلام چک در پیشخوانک <strong>بسیار مقرون‌به‌صرفه</strong> و با نرخ‌های رقابتی ارائه می‌شود. برای اطلاع از تعرفه‌های به‌روز، از قسمت قیمت‌گذاری سایت یا تماس با پشتیبانی استفاده کنید. هزینه بسیار کمی نسبت به ریسک دریافت چک برگشتی دارد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="روش‌های پرداخت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">روش‌های پرداخت در پیشخوانک چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        روش‌های پرداخت موجود:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>کارت‌های بانکی</strong> (شتاب)</li>
                            <li><strong>کیف پول الکترونیکی</strong></li>
                            <li><em>انتقال بانکی</em></li>
                            <li><strong>درگاه‌های امن پرداخت</strong></li>
                        </ul>
                        <p class="mt-3 text-sm">همه تراکنش‌ها امن و رمزنگاری شده هستند.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="تخفیف حجمی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا برای استعلام حجمی تخفیف وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، پیشخوانک تخفیف‌های ویژه برای استعلام حجمی دارد:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>استعلام بیش از ۱۰ چک: تخفیف ۱۰٪</li>
                            <li>استعلام بیش از ۵۰ چک: تخفیف ۲۰٪</li>
                            <li>پکیج‌های سازمانی: تخفیف تا ۳۰٪</li>
                        </ul>
                        <p class="mt-3 text-sm">برای کسب‌وکارها و فروشندگان پکیج‌های ویژه موجود است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="فاکتور مالیات">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا فاکتور رسمی ارائه می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، برای همه خریدها فاکتور رسمی همراه با مالیات ارائه می‌شود.</strong> این فاکتور قابلیت ثبت در سیستم‌های حسابداری و کسر از درآمد مشاغل را دارد. فاکتور بلافاصله پس از پرداخت ارسال می‌شود.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="بازگشت پول">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">در صورت مشکل، آیا پول بازگردانده می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در موارد زیر پول بازگردانده می‌شود:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>خرابی سیستم</strong> و عدم ارائه خدمت</li>
                            <li><em>خطای فنی</em> در نتایج استعلام</li>
                            <li><strong>عدم دسترسی</strong> به سامانه صیاد</li>
                        </ul>
                        <p class="mt-3 text-sm">برای بازگشت وجه با پشتیبانی تماس بگیرید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="اشتراک ماهانه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا طرح اشتراک ماهانه وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، برای کاربران پرتکرار طرح‌های اشتراک ماهانه و سالانه موجود است:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>طرح پایه: ۱۰۰ استعلام ماهانه</li>
                            <li>طرح حرفه‌ای: ۵۰۰ استعلام ماهانه</li>
                            <li>طرح سازمانی: استعلام نامحدود</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 6: مسائل فنی (Technical Issues) -->
        <div class="faq-category" data-category="technical">
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    مسائل فنی و رفع مشکل
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="technical" data-keywords="کد غلط خطای فنی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر کد چک را اشتباه وارد کنم چه می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        اگر کد چک اشتباه باشد:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>پیام خطا نمایش داده می‌شود</strong></li>
                            <li>هزینه‌ای کسر نمی‌شود</li>
                            <li>می‌توانید مجدداً کد صحیح را وارد کنید</li>
                            <li>سیستم فرمت کد را بررسی می‌کند</li>
                        </ul>
                        <p class="mt-3 text-sm text-gray-600">کد باید دقیقاً ۱۶ رقم باشد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="اتصال قطع اینترنت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر در حین استعلام اتصال قطع شود چه کار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در صورت قطعی اتصال:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li>اتصال اینترنت خود را بررسی کنید</li>
                            <li>صفحه را رفرش کنید</li>
                            <li>اگر پرداخت انجام شده، از قسمت تاریخچه نتیجه را مشاهده کنید</li>
                            <li>در صورت مشکل با پشتیبانی تماس بگیرید</li>
                        </ol>
                        <p class="mt-3 text-sm"><strong>توجه:</strong> تراکنش‌ها محافظت می‌شوند و اطلاعات گم نمی‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="مرورگر سازگاری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کدام مرورگرها پشتیبانی می‌شوند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مرورگرهای پشتیبانی شده:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>Google Chrome</strong> (نسخه ۸۰ به بالا)</li>
                            <li><strong>Mozilla Firefox</strong> (نسخه ۷۵ به بالا)</li>
                            <li><strong>Safari</strong> (نسخه ۱۳ به بالا)</li>
                            <li><strong>Microsoft Edge</strong> (نسخه ۸۵ به بالا)</li>
                        </ul>
                        <p class="mt-3 text-sm">برای بهترین عملکرد از آخرین نسخه مرورگر استفاده کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="موبایل اپلیکیشن">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا اپلیکیشن موبایل وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>در حال حاضر پیشخوانک دارای وب‌سایت کاملاً ریسپانسیو است</strong> که روی همه دستگاه‌های موبایل به خوبی کار می‌کند. اپلیکیشن موبایل نیز در حال توسعه بوده و به‌زودی منتشر خواهد شد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="API توسعه‌دهندگان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا API برای توسعه‌دهندگان موجود است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، پیشخوانک API کاملی برای توسعه‌دهندگان ارائه می‌دهد:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>RESTful API با استانداردهای جهانی</li>
                            <li>احراز هویت امن با API Key</li>
                            <li>مستندات کامل و نمونه کد</li>
                            <li>پشتیبانی JSON و XML</li>
                            <li>محدودیت نرخ مناسب</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="نتیجه ذخیره پرینت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم نتایج استعلام را ذخیره یا پرینت کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، امکانات مختلفی برای ذخیره نتایج فراهم است:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>دانلود PDF</strong> با واترمارک رسمی</li>
                            <li><em>پرینت مستقیم</em> با فرمت بهینه</li>
                            <li><strong>ارسال ایمیل</strong> نتایج</li>
                            <li><em>ذخیره در تاریخچه حساب کاربری</em></li>
                        </ul>
                        <p class="mt-3 text-sm">نتایج تا ۳ ماه در حساب شما محفوظ می‌ماند.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Additional Categories and Questions to reach 50+ FAQs -->
        
        <!-- Category 7: امنیت و حریم خصوصی (Security and Privacy) -->
        <div class="faq-category" data-category="security">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    امنیت و حریم خصوصی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="security" data-keywords="امنیت اطلاعات حفاظت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اطلاعات شخصی من در پیشخوانک امن است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، پیشخوانک از بالاترین استانداردهای امنیتی استفاده می‌کند:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>رمزنگاری SSL/TLS برای انتقال داده</li>
                            <li>عدم ذخیره‌سازی اطلاعات حساس</li>
                            <li>دسترسی محدود به اطلاعات کاربران</li>
                            <li>حذف خودکار داده‌های موقت</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="کلاهبرداری جعل هشدار">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از کلاهبرداری در استعلام چک محافظت کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        توصیه‌های امنیتی:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li>تنها از سایت‌های مجاز مثل پیشخوانک استفاده کنید</li>
                            <li>هیچ‌گاه اطلاعات بانکی خود را در سایت‌های مشکوک وارد نکنید</li>
                            <li>از سایت‌هایی که ادعای "استعلام رایگان" دارند، اجتناب کنید</li>
                            <li>همیشه HTTPS بودن سایت را بررسی کنید</li>
                        </ol>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="IP ثبت لاگ">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا IP و اطلاعات مرورگر من ثبت می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        تنها اطلاعات ضروری برای امنیت سیستم ثبت می‌شود:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>IP address برای جلوگیری از سوءاستفاده</li>
                            <li>زمان درخواست برای امنیت</li>
                            <li>نوع مرورگر برای بهینه‌سازی</li>
                        </ul>
                        <p class="mt-3 text-sm">این اطلاعات پس از ۳۰ روز حذف می‌شود.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 8: مسائل حقوقی (Legal Issues) -->
        <div class="faq-category" data-category="legal">
            <div class="bg-gradient-to-r from-amber-600 to-amber-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                    مسائل حقوقی و قانونی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="legal" data-keywords="حقوقی قانون مجازات">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چک برگشتی چه پیامدهای حقوقی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        پیامدهای حقوقی چک برگشتی:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>مسئولیت مدنی</strong> پرداخت اصل مبلغ و خسارت</li>
                            <li><strong>محدودیت‌های بانکی</strong> و اعتباری</li>
                            <li>امکان <strong>شکایت کیفری</strong> در صورت عدم پرداخت</li>
                            <li>درج در <strong>لیست بدهکاران بانکی</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="شکایت چک برگشتی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از چک برگشتی شکایت کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مراحل شکایت از چک برگشتی:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li>تقاضای وصول از بانک محل پرداخت</li>
                            <li>دریافت برگه عدم پرداخت</li>
                            <li>مراجعه به دادسرای کیفری</li>
                            <li>تنظیم دادخواست با کمک وکیل</li>
                            <li>پیگیری پرونده قضایی</li>
                        </ol>
                        <p class="mt-3 text-sm"><em>مشاوره با وکیل متخصص ضروری است.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="مدت قانونی اعتراض">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مدت قانونی اعتراض به چک برگشتی چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مهلت‌های قانونی مهم:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>۶ ماه</strong> برای ارائه چک به بانک</li>
                            <li><strong>۲ سال</strong> برای شکایت کیفری</li>
                            <li><strong>۳ سال</strong> برای دعوای مدنی</li>
                            <li><strong>۱۰ سال</strong> مدت انقضای اسناد تجاری</li>
                        </ul>
                        <p class="mt-3 text-sm text-amber-700">⚠️ مهلت‌ها از تاریخ سررسید چک محاسبه می‌شود.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Additional Single FAQs across categories to reach 50+ total -->
        <div class="faq-category mt-8" data-category="additional">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    سوالات تکمیلی و موارد خاص
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <!-- Additional 15 FAQs to reach 50+ total -->
                <div class="faq-item p-6" data-category="additional" data-keywords="چک مسافرتی خارجی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا چک‌های خارجی قابل استعلام هستند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، سامانه صیاد تنها چک‌های صادر شده توسط بانک‌های ایرانی را پوشش می‌دهد.</strong> چک‌های مسافرتی و ارزی خارجی در این سامانه ثبت نیستند.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="چک شرکت حقوقی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چک‌های اشخاص حقوقی چگونه استعلام می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای چک‌های شرکت‌ها و اشخاص حقوقی باید <strong>شناسه ملی شرکت</strong> یا <strong>کد ۱۶ رقمی چک</strong> استفاده شود. روند کاملاً مشابه اشخاص حقیقی است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="چک پست مکاتبه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا چک‌های پستی قابل استعلام هستند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، چک‌های ارسالی از طریق پست</strong> نیز در صورت داشتن کد ۱۶ رقمی و ثبت در صیاد، قابل استعلام هستند. مهم این است که چک معتبر باشد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="چک تضمینی بانکی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چک تضمینی بانکی نیاز به استعلام دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        چک‌های تضمینی بانکی <strong>ریسک کمتری دارند</strong> اما همچنان توصیه می‌شود استعلام کنید تا از معتبر بودن و ثبت صحیح در سامانه صیاد اطمینان حاصل کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="حساب مسدود تعلیق">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چک از حساب مسدود شده چگونه شناسایی می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در استعلام چک از حساب مسدود، پیام <strong>"حساب غیرفعال"</strong> یا <strong>"مسدود"</strong> نمایش داده می‌شود. این چک‌ها معمولاً برگشت می‌خورند.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="چک آینده‌نگار محدودیت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semبold text-gray-800 text-lg">چک‌های آینده‌نگار چه محدودیتی دارند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        چک‌های آینده‌نگار (بیش از ۶ ماه آینده) <strong>محدودیت قانونی دارند</strong> و معمولاً بانک‌ها آن‌ها را نمی‌پذیرند. بهتر است از تاریخ‌های نزدیک‌تر استفاده کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="چک موهوم جعلی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه چک موهوم از چک واقعی تشخیص داده می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        علائم چک موهوم:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>عدم وجود کد ۱۶ رقمی معتبر</li>
                            <li>خطا در استعلام از سامانه صیاد</li>
                            <li>کیفیت چاپ نامناسب</li>
                            <li>عدم تطابق اطلاعات با واقعیت</li>
                        </ul>
                        <p class="mt-3"><strong>همیشه قبل از پذیرش چک، استعلام کنید.</strong></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="بلاک چین دیجیتال">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا چک‌های دیجیتال در آینده جایگزین می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        بانک مرکزی در حال بررسی <strong>چک‌های دیجیتال</strong> و استفاده از فناوری‌های نوین است. در آینده احتمالاً سیستم‌های امن‌تر و سریع‌تری جایگزین چک‌های کاغذی می‌شود.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="تاریخ منقضی انقضا">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چک منقضی شده قابل وصول است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        چک‌هایی که <strong>بیش از ۶ ماه از تاریخ صدور</strong> آن‌ها گذشته باشد، منقضی شده و بانک‌ها معمولاً آن‌ها را نمی‌پذیرند. باید چک جدید صادر شود.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="ضمانت وثیقه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا ضمانت اضافی برای چک‌های رنگی لازم است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای چک‌های <strong>نارنجی، قهوه‌ای و قرمز</strong> توصیه می‌شود ضمانت اضافی مثل:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>چک ضمانت</li>
                            <li>سفته</li>
                            <li>سند رهنی</li>
                            <li>ضامن معتبر</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="صفر کردن سن چک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا امکان صفر کردن سابقه چک برگشتی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، امکان حذف کامل سابقه وجود ندارد.</strong> تنها راه بهبود وضعیت، رفع سوءاثر همه چک‌ها و گذشت زمان (حداکثر ۶ سال) است. هیچ روش غیرقانونی برای پاک کردن سابقه وجود ندارد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="تعویض چک جدید">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه زمانی نیاز به تعویض چک با چک جدید است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        موارد نیاز به چک جدید:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>انقضای ۶ ماهه چک</li>
                            <li>پاره یا خراب شدن چک</li>
                            <li>اشتباه در مبلغ یا تاریخ</li>
                            <li>مغایرت امضا</li>
                            <li>تغییر شرایط قرارداد</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="همراه بانک موبایل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا از طریق همراه بانک می‌توان چک استعلام کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>برخی بانک‌ها امکان استعلام محدود</strong> در همراه بانک دارند، اما برای استعلام کامل و دقیق، استفاده از پلتفرم‌های تخصصی مثل پیشخوانک توصیه می‌شود.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="additional" data-keywords="پیش‌نویس صدور">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا پیش‌نویس چک قابل استعلام است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، تنها چک‌های نهایی و تکمیل شده</strong> که اطلاعات آن‌ها در سامانه صیاد ثبت شده باشد، قابل استعلام هستند. پیش‌نویس‌ها هنوز در سامانه ثبت نیستند.
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- No Results Message -->
    <div id="no-results" class="hidden text-center py-12">
        <div class="max-w-md mx-auto">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">نتیجه‌ای یافت نشد</h3>
            <p class="text-gray-500 mb-4">کلمه کلیدی دیگری را امتحان کنید یا از فیلترهای موضوعی استفاده کنید.</p>
            <button onclick="clearSearch()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                پاک کردن جستجو
            </button>
        </div>
    </div>

</section>

<!-- FAQ Functionality Script -->
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

    // FAQ Accordion functionality
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

    // Search functionality
    searchInput.addEventListener('input', function() {
        performSearch();
    });

    // Category filter functionality
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            categoryButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });
            this.classList.add('active', 'bg-blue-600', 'text-white');
            this.classList.remove('bg-gray-100', 'text-gray-700');

            currentCategory = this.dataset.category;
            performSearch();
        });
    });

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        let visibleCount = 0;

        faqItems.forEach(item => {
            const itemCategory = item.dataset.category;
            const itemKeywords = item.dataset.keywords.toLowerCase();
            const questionText = item.querySelector('.faq-question h4').textContent.toLowerCase();
            const answerText = item.querySelector('.faq-answer').textContent.toLowerCase();

            // Check category filter
            const categoryMatch = currentCategory === 'all' || itemCategory === currentCategory;
            
            // Check search term
            const searchMatch = searchTerm === '' || 
                itemKeywords.includes(searchTerm) || 
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