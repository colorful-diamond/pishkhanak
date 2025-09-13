{{-- Comprehensive Searchable FAQ Section for Credit Score Rating Service --}}
{{-- سوالات متداول جامع و قابل جستجو برای خدمات اعتبارسنجی بانک مهر ایران --}}

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
                بیش از <strong>۵۳ سوال و پاسخ تخصصی</strong> درباره اعتبارسنجی، وام قرض‌الحسنه، و خدمات بانک مهر ایران
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
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="general">
                    عمومی (۸)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="credit-assessment">
                    اعتبارسنجی (۷)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="loan-process">
                    فرآیند وام (۷)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="documents">
                    مدارک (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="costs">
                    هزینه‌ها (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="security">
                    امنیت (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="regulations">
                    مقررات (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="services">
                    خدمات (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="troubleshooting">
                    رفع مشکل (۳)
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

        <!-- Category 1: عمومی (General) -->
        <div class="faq-category" data-category="general">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    سوالات عمومی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="general" data-keywords="بانک مهر ایران معرفی تاریخچه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">بانک مهر ایران چیست و چه جایگاهی در سیستم بانکی کشور دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>بانک مهر ایران یکی از <strong>بانک‌های خصوصی معتبر کشور</strong> و چهارمین بانک بزرگ از نظر حجم تراکنش‌ها است که حدود <strong>۹٪ از تراکنش‌های شاپرک</strong> را پردازش می‌کند.</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>تأسیس در سال ۱۳۸۰ با مجوز رسمی بانک مرکزی</li>
                            <li>دارای شبکه گسترده شعب در سراسر کشور</li>
                            <li>متخصص در ارائه خدمات مالی و اعتباری</li>
                            <li>پیشرو در ارائه خدمات دیجیتال و نوین بانکداری</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="قرض الحسنه وام بدون سود">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">وام قرض‌الحسنه چیست و چه تفاوتی با وام‌های معمولی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>وام قرض‌الحسنه نوعی تسهیلات <strong>بدون سود و منطبق با اصول بانکداری اسلامی</strong> است که تنها کارمزد ناچیزی (۴٪) بابت خدمات اداری دریافت می‌کند.</p>
                        <div class="mt-3">
                            <strong>ویژگی‌های قرض‌الحسنه:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>عدم دریافت سود (ربا)</li>
                                <li>کارمزد ثابت ۴٪ کل مبلغ</li>
                                <li>مبلغ حداکثر ۵۰۰ میلیون تومان</li>
                                <li>مدت بازپرداخت تا ۶۰ ماه</li>
                                <li>صرفاً برای اشخاص حقیقی</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="اعتبارسنجی تعریف چیست">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اعتبارسنجی چیست و چرا برای دریافت وام ضروری است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        اعتبارسنجی فرآیند <strong>ارزیابی توانایی مالی و سابقه اعتباری</strong> متقاضیان برای تعیین میزان ریسک اعطای تسهیلات است:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>بررسی سابقه پرداخت تسهیلات قبلی</li>
                            <li>تحلیل درآمد و هزینه‌های متقاضی</li>
                            <li>ارزیابی وضعیت شغلی و اقتصادی</li>
                            <li>بررسی تاریخچه چک‌های برگشتی</li>
                            <li>تعیین رتبه اعتباری از A تا E</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="رتبه اعتباری A B C D E">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سیستم رتبه‌بندی اعتباری A تا E چگونه کار می‌کند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        سیستم رتبه‌بندی اعتباری شامل <strong>۵ درجه از A (بهترین) تا E (ضعیف‌ترین)</strong> است:
                        <div class="mt-3">
                            <ul class="space-y-2">
                                <li><strong>رتبه A:</strong> عالی - بدون مشکل اعتباری</li>
                                <li><strong>رتبه B:</strong> خوب - مشکلات جزئی</li>
                                <li><strong>رتبه C:</strong> متوسط - حد مجاز برای وام</li>
                                <li><strong>رتبه D:</strong> ضعیف - نیاز به ضمانت بیشتر</li>
                                <li><strong>رتبه E:</strong> غیرقابل قبول - عدم واجد شرایط</li>
                            </ul>
                        </div>
                        <p class="mt-3"><em>برای دریافت وام قرض‌الحسنه، حداقل رتبه C لازم است.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="زمان پردازش ساعت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چقدر زمان برای بررسی و تأیید وام نیاز است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        زمان پردازش وام در بانک مهر ایران <strong>بسیار سریع و کارآمد</strong> است:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>متقاضیان واجد شرایط:</strong> ۱ تا ۲ ساعت</li>
                            <li><strong>متقاضیان نیازمند بررسی بیشتر:</strong> ۲۴ تا ۴۸ ساعت</li>
                            <li><strong>موارد پیچیده:</strong> تا ۷۲ ساعت</li>
                            <li>بررسی در روزهای کاری بانک</li>
                            <li>اطلاع‌رسانی فوری از طریق پیامک</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="سقف مبلغ حداکثر 500 میلیون">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">حداکثر مبلغ وام قرض‌الحسنه چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>حداکثر مبلغ وام قرض‌الحسنه <strong>۵۰۰ میلیون تومان</strong> است، اما مبلغ نهایی بر اساس عوامل زیر تعیین می‌شود:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>رتبه اعتباری متقاضی</li>
                            <li>میزان درآمد ماهانه</li>
                            <li>تعهدات مالی فعلی</li>
                            <li>نوع ضمانت ارائه شده</li>
                            <li>سابقه همکاری با بانک</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="بازپرداخت مدت اقساط 60 ماه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مدت بازپرداخت وام چقدر است و آیا قابل تغییر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مدت بازپرداخت وام <strong>حداکثر ۶۰ ماه (۵ سال)</strong> است با انعطاف‌پذیری زیر:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>حداقل مدت: ۶ ماه</li>
                            <li>حداکثر مدت: ۶۰ ماه</li>
                            <li>امکان پرداخت زودهنگام بدون جریمه</li>
                            <li>قابلیت تجدیدنظر در صورت تغییر شرایط</li>
                            <li>اقساط ماهانه ثابت</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="حقوقی حقیقی شخص">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا اشخاص حقوقی می‌توانند از این وام استفاده کنند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، وام قرض‌الحسنه صرفاً برای اشخاص حقیقی</strong> (افراد واقعی) ارائه می‌شود. اشخاص حقوقی باید از محصولات تسهیلاتی خاص کسب‌وکار استفاده کنند که دارای شرایط و کارمزد متفاوت هستند.
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 2: اعتبارسنجی (Credit Assessment) -->
        <div class="faq-category" data-category="credit-assessment">
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    اعتبارسنجی و رتبه‌بندی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="credit-assessment" data-keywords="نحوه محاسبه رتبه اعتباری فاکتورها">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رتبه اعتباری بر اساس چه فاکتورهایی محاسبه می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        محاسبه رتبه اعتباری بر اساس <strong>الگوریتم پیچیده‌ای از فاکتورهای متعدد</strong> انجام می‌شود:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>سابقه پرداخت:</strong> ۳۵٪ - تاریخچه پرداخت وام‌ها و تسهیلات</li>
                            <li><strong>میزان بدهی:</strong> ۳۰٪ - نسبت بدهی به درآمد</li>
                            <li><strong>سابقه اعتباری:</strong> ۱۵٪ - طول مدت تاریخچه مالی</li>
                            <li><strong>انواع اعتبار:</strong> ۱۰٪ - تنوع محصولات مالی</li>
                            <li><strong>اعتبارات جدید:</strong> ۱۰٪ - درخواست‌های اخیر</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="credit-assessment" data-keywords="بهبود رتبه اعتباری راه‌های">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه می‌توانم رتبه اعتباری خود را بهبود دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>بهبود رتبه اعتباری فرآیندی <strong>تدریجی اما قابل دستیابی</strong> است:</p>
                        <div class="mt-3">
                            <strong>اقدامات فوری:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>پرداخت کلیه بدهی‌های معوق</li>
                                <li>تسویه چک‌های برگشتی</li>
                                <li>کاهش نسبت بدهی به درآمد</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <strong>اقدامات بلندمدت:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>پرداخت منظم اقساط</li>
                                <li>حفظ تعادل در حساب‌ها</li>
                                <li>استفاده معقول از تسهیلات</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="credit-assessment" data-keywords="مدت زمان بروزرسانی رتبه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رتبه اعتباری چند وقت یک‌بار بروزرسانی می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        رتبه اعتباری <strong>به‌صورت ماهانه بروزرسانی</strong> می‌شود:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>بروزرسانی خودکار در پایان هر ماه</li>
                            <li>اعمال فوری تغییرات مهم (چک برگشتی)</li>
                            <li>امکان درخواست بررسی فوری</li>
                            <li>اطلاع‌رسانی تغییرات از طریق پیامک</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="credit-assessment" data-keywords="چک برگشتی تأثیر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چک برگشتی چه تأثیری بر رتبه اعتباری دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        چک برگشتی یکی از <strong>مخرب‌ترین عوامل برای رتبه اعتباری</strong> محسوب می‌شود:
                        <div class="mt-3">
                            <strong>تأثیرات فوری:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>کاهش شدید رتبه اعتباری</li>
                                <li>ممنوعیت صدور چک جدید</li>
                                <li>رد خودکار درخواست وام</li>
                                <li>ثبت در فهرست بدحساب‌ها</li>
                            </ul>
                        </div>
                        <p class="mt-3"><em>برای رفع سوءاثر، باید مبلغ چک به همراه جریمه پرداخت شود.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="credit-assessment" data-keywords="گزارش اعتباری دریافت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه می‌توانم گزارش اعتباری خود را دریافت کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        دریافت گزارش اعتباری از <strong>چندین مسیر رسمی</strong> امکان‌پذیر است:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>همراه بانک مهر ایران</strong> - رایگان ماهانه</li>
                            <li><strong>درگاه اینترنتی بانک</strong> - ۲۴ ساعته</li>
                            <li><strong>مراجعه حضوری</strong> - شعب بانک</li>
                            <li><strong>تماس تلفنی</strong> - مرکز تماس بانک</li>
                            <li><strong>خدمات پیشخوانک</strong> - سریع و آنلاین</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="credit-assessment" data-keywords="تفاوت رتبه‌های اعتباری مقایسه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">تفاوت عملی بین رتبه‌های اعتباری مختلف چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        هر رتبه اعتباری <strong>مزایا و محدودیت‌های خاصی</strong> دارد:
                        <div class="space-y-3 mt-3">
                            <div><strong>رتبه A:</strong> تا ۵۰۰ میلیون، بدون ضامن، کارمزد ۴٪</div>
                            <div><strong>رتبه B:</strong> تا ۴۰۰ میلیون، ضامن اختیاری، کارمزد ۴٪</div>
                            <div><strong>رتبه C:</strong> تا ۳۰۰ میلیون، ضامن ضروری، کارمزد ۴٪</div>
                            <div><strong>رتبه D:</strong> رد درخواست یا شرایط خاص</div>
                            <div><strong>رتبه E:</strong> غیرواجد شرایط</div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="credit-assessment" data-keywords="اعتراض رتبه اعتباری تجدیدنظر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر با رتبه اعتباری‌ام موافق نیستم چه کار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>امکان <strong>اعتراض و درخواست تجدیدنظر</strong> وجود دارد:</p>
                        <div class="mt-3">
                            <strong>مراحل اعتراض:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>ارائه مدارک جدید یا تکمیلی</li>
                                <li>تماس با واحد اعتبارسنجی بانک</li>
                                <li>درخواست کتبی برای بررسی مجدد</li>
                                <li>ارائه توضیحات برای موارد منفی</li>
                                <li>بررسی توسط کمیته تخصصی</li>
                            </ul>
                        </div>
                        <p class="mt-3"><em>پاسخ تجدیدنظر ظرف ۷۲ ساعت اعلام می‌شود.</em></p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 3: فرآیند وام (Loan Process) -->
        <div class="faq-category" data-category="loan-process">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    فرآیند دریافت وام
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="loan-process" data-keywords="مراحل درخواست وام گام به گام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مراحل درخواست وام قرض‌الحسنه چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        فرآیند درخواست وام در <strong>۶ مرحله ساده</strong> انجام می‌شود:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>ثبت درخواست:</strong> تکمیل فرم آنلاین یا حضوری</li>
                            <li><strong>ارائه مدارک:</strong> بارگذاری اسناد مورد نیاز</li>
                            <li><strong>اعتبارسنجی:</strong> بررسی رتبه و سابقه اعتباری</li>
                            <li><strong>تأیید نهایی:</strong> بررسی کمیته تسهیلات</li>
                            <li><strong>عقد قرارداد:</strong> امضای اسناد و ضمانت</li>
                            <li><strong>واریز وجه:</strong> انتقال مبلغ به حساب</li>
                        </ol>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="loan-process" data-keywords="شرایط واجد شرایط بودن">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه شرایطی برای واجد شرایط بودن لازم است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>شرایط اصلی واجد شرایط بودن:</strong></p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>داشتن سن ۱۸ تا ۶۵ سال</li>
                            <li>تابعیت ایرانی یا اتباع مقیم</li>
                            <li>رتبه اعتباری حداقل C</li>
                            <li>درآمد ثابت حداقل ۳ میلیون تومان</li>
                            <li>عدم بدهی معوق به بانک‌ها</li>
                            <li>ارائه ضمانت معتبر</li>
                            <li>عدم سابقه چک برگشتی در ۶ ماه اخیر</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="loan-process" data-keywords="درآمد حداقل 3 میلیون">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">حداقل درآمد برای دریافت وام چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        حداقل درآمد مورد نیاز <strong>۳ میلیون تومان در ماه</strong> است، اما مبلغ وام بر اساس ضریب بازپرداخت محاسبه می‌شود:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>درآمد ۳-۵ میلیون:</strong> تا ۱۰۰ میلیون وام</li>
                            <li><strong>درآمد ۵-۱۰ میلیون:</strong> تا ۲۵۰ میلیون وام</li>
                            <li><strong>درآمد بالای ۱۰ میلیون:</strong> تا ۵۰۰ میلیون وام</li>
                            <li>نسبت قسط به درآمد حداکثر ۳۰٪</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="loan-process" data-keywords="ضمانت انواع وثیقه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه انواع ضمانت‌هایی قابل قبول است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>انواع ضمانت‌های قابل قبول:</strong></p>
                        <div class="mt-3">
                            <strong>ضمانت‌های اصلی:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>سند ملک (آپارتمان، زمین، ویلا)</li>
                                <li>سپرده نقدی (۲۰٪ مبلغ وام)</li>
                                <li>ضامن معتبر با رتبه A یا B</li>
                                <li>سند خودرو (کمتر از ۵ سال)</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <strong>ضمانت‌های تکمیلی:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>اوراق مشارکت</li>
                                <li>سهام شرکت‌های پذیرفته شده</li>
                                <li>ضمانت کارفرما</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="loan-process" data-keywords="رد درخواست دلایل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">در چه مواردی درخواست وام رد می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>دلایل اصلی رد درخواست:</strong></p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>رتبه اعتباری پایین‌تر از C</li>
                            <li>داشتن چک برگشتی فعال</li>
                            <li>درآمد کمتر از حد مجاز</li>
                            <li>نسبت بالای بدهی به درآمد</li>
                            <li>عدم ارائه ضمانت کافی</li>
                            <li>نقص در مدارک ارائه شده</li>
                            <li>سابقه بد پرداخت تسهیلات</li>
                        </ul>
                        <p class="mt-3"><em>در صورت رد، امکان درخواست مجدد پس از رفع نواقص وجود دارد.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="loan-process" data-keywords="واریز مبلغ زمان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">پس از تأیید، وام چه زمانی واریز می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مبلغ وام پس از تأیید نهایی <strong>حداکثر ۲۴ ساعت</strong> به حساب واریز می‌شود:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>روزهای کاری:</strong> همان روز تا ساعت ۱۶</li>
                            <li><strong>بعد از ساعت ۱۶:</strong> روز کاری بعد</li>
                            <li><strong>تعطیلات:</strong> اول روز کاری</li>
                            <li>اطلاع‌رسانی فوری پس از واریز</li>
                            <li>امکان پیگیری آنلاین وضعیت</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="loan-process" data-keywords="لغو درخواست انصراف">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا امکان لغو درخواست وام پس از تأیید وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>بله، امکان لغو تا قبل از واریز وجود دارد:</strong></p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>قبل از واریز:</strong> لغو بدون هزینه</li>
                            <li><strong>بعد از واریز:</strong> بازگشت کامل مبلغ ظرف ۷۲ ساعت</li>
                            <li>کسر هزینه اداری در صورت انصراف بعد از واریز</li>
                            <li>لغو ضمانت‌ها پس از بازگشت مبلغ</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 4: مدارک (Documents) -->
        <div class="faq-category" data-category="documents">
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    مدارک مورد نیاز
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="documents" data-keywords="فهرست مدارک لازم لیست">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">فهرست کامل مدارک مورد نیاز چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>مدارک اصلی (الزامی):</strong></p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>تصویر کارت ملی (پشت و رو)</li>
                            <li>تصویر شناسنامه (صفحات اول تا سوم)</li>
                            <li>گواهی درآمد یا حکم کارگزینی</li>
                            <li>فیش حقوقی ۳ ماه اخیر</li>
                            <li>گردش حساب ۶ ماه اخیر</li>
                            <li>تصویر دفترچه چک (در صورت وجود)</li>
                        </ul>
                        <div class="mt-3">
                            <strong>مدارک ضمانت:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>سند ملک یا رهنی</li>
                                <li>مدارک ضامن (مشابه متقاضی)</li>
                                <li>قولنامه خرید (در صورت لزوم)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="documents" data-keywords="گواهی درآمد کجا بگیرم">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">گواهی درآمد را از کجا و چگونه دریافت کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        دریافت گواهی درآمد بسته به <strong>نوع شغل</strong> متفاوت است:
                        <div class="mt-3">
                            <strong>کارمندان:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>واحد منابع انسانی سازمان</li>
                                <li>حکم کارگزینی + فیش حقوقی</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <strong>آزاد کاران:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>اظهارنامه مالیاتی</li>
                                <li>گردش حساب بانکی</li>
                                <li>گواهی اتاق اصناف/بازرگانی</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <strong>بازنشستگان:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>حکم بازنشستگی</li>
                                <li>فیش مستمری</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="documents" data-keywords="اسکن مدارک کیفیت عکس">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مدارک باید اسکن باشند یا عکس موبایل کافی است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>هر دو روش قابل قبول است</strong> با رعایت این نکات:</p>
                        <div class="mt-3">
                            <strong>الزامات کیفیت:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>وضوح بالا و خوانایی کامل متن</li>
                                <li>نور مناسب و بدون سایه</li>
                                <li>عدم کج بودن سند</li>
                                <li>فرمت JPG یا PDF</li>
                                <li>حجم حداکثر ۵ مگابایت</li>
                            </ul>
                        </div>
                        <p class="mt-3"><em>پیشنهاد: استفاده از اپلیکیشن اسکنر موبایل برای کیفیت بهتر</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="documents" data-keywords="مدت اعتبار مدارک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مدارک چه مدت اعتبار دارند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مدت اعتبار مدارک <strong>بسته به نوع سند</strong> متفاوت است:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>کارت ملی/شناسنامه:</strong> بدون محدودیت زمانی</li>
                            <li><strong>گواهی درآمد:</strong> حداکثر ۳ ماه</li>
                            <li><strong>فیش حقوقی:</strong> حداکثر ۱ ماه</li>
                            <li><strong>گردش حساب:</strong> حداکثر ۱۵ روز</li>
                            <li><strong>سند ملک:</strong> بدون محدودیت زمانی</li>
                            <li><strong>اظهارنامه مالیاتی:</strong> سال جاری</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="documents" data-keywords="تکمیلی نقص مدارک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر در مدارک نقص باشد چه اتفاقی می‌افتد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در صورت نقص در مدارک، <strong>فرصت رفع نواقص</strong> داده می‌شود:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>اطلاع‌رسانی فوری نواقص از طریق پیامک</li>
                            <li>مهلت ۷۲ ساعت برای ارائه مدارک کامل</li>
                            <li>راهنمایی تلفنی برای رفع نواقص</li>
                            <li>عدم لغو درخواست تا پایان مهلت</li>
                            <li>امکان ارائه مدارک جایگزین</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="documents" data-keywords="مدارک ضامن لازم">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">ضامن چه مداركی باید ارائه دهد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>ضامن باید <strong>مدارک مشابه متقاضی اصلی</strong> ارائه دهد:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>تصویر کارت ملی و شناسنامه</li>
                            <li>گواهی درآمد یا حکم کارگزینی</li>
                            <li>فیش حقوقی ۳ ماه اخیر</li>
                            <li>گردش حساب ۶ ماه اخیر</li>
                            <li>تعهدنامه ضمانت (فرم بانک)</li>
                            <li>مدارک اضافی در صورت لزوم</li>
                        </ul>
                        <p class="mt-3"><em>ضامن نیز باید رتبه اعتباری مناسب (حداقل C) داشته باشد.</em></p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 5: هزینه‌ها (Costs) -->
        <div class="faq-category" data-category="costs">
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    هزینه‌ها و کارمزدها
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="costs" data-keywords="کارمزد 4 درصد هزینه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کارمزد وام قرض‌الحسنه چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        کارمزد وام قرض‌الحسنه <strong>۴٪ از کل مبلغ دریافتی</strong> است:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>کارمزد ثابت بدون تغییر در طول دوره</li>
                            <li>پرداخت همراه با اولین قسط</li>
                            <li>عدم وجود سود یا ربا</li>
                            <li>شفاف و بدون هزینه پنهان</li>
                        </ul>
                        <div class="mt-3">
                            <strong>مثال محاسبه:</strong>
                            <p>وام ۱۰۰ میلیون تومان = کارمزد ۴ میلیون تومان</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="هزینه درخواست 100000 تومان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">هزینه درخواست و بررسی چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>هزینه درخواست و بررسی <strong>۱۰۰,۰۰۰ تومان</strong> است که شامل:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>بررسی مدارک و اعتبارسنجی</li>
                            <li>گزارش اعتباری تفصیلی</li>
                            <li>هزینه کمیته تسهیلات</li>
                            <li>خدمات مشاوره و راهنمایی</li>
                        </ul>
                        <p class="mt-3"><strong>نکته:</strong> این هزینه صرف در صورت تأیید نهایی وام از آن کسر می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="هزینه پنهان اضافی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا هزینه‌های پنهان یا اضافی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>هیچ هزینه پنهانی وجود ندارد.</strong> تمام هزینه‌ها شفاف و از قبل اعلام شده:</p>
                        <div class="mt-3">
                            <strong>هزینه‌های شفاف:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>کارمزد ۴٪ - یکبار در ابتدا</li>
                                <li>هزینه درخواست ۱۰۰ هزار تومان</li>
                                <li>هزینه بیمه (اختیاری)</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <strong>هزینه‌های اضافی محتمل:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>جریمه تأخیر در پرداخت قسط</li>
                                <li>هزینه تبدیل چک (در صورت درخواست)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="جریمه تأخیر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">جریمه تأخیر در پرداخت قسط چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        جریمه تأخیر بر اساس <strong>مدت و مبلغ تأخیر</strong> محاسبه می‌شود:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>۱-۷ روز تأخیر:</strong> بدون جریمه</li>
                            <li><strong>۸-۳۰ روز:</strong> ۰.۱٪ در روز</li>
                            <li><strong>۳۱-۹۰ روز:</strong> ۰.۱۵٪ در روز</li>
                            <li><strong>بیش از ۹۰ روز:</strong> ۰.۲٪ در روز</li>
                        </ul>
                        <p class="mt-3"><em>توصیه: برای جلوگیری از جریمه، از خدمات اتوماتیک کسر از حساب استفاده کنید.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="پرداخت زودهنگام جریمه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا برای پرداخت زودهنگام جریمه دریافت می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>نه، هیچ جریمه‌ای برای پرداخت زودهنگام وجود ندارد.</strong> بلکه مزایای زیر دارد:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>امکان تسویه کامل بدون جریمه</li>
                            <li>تخفیف در کارمزد باقیمانده</li>
                            <li>آزادسازی زودهنگام ضمانت‌ها</li>
                            <li>بهبود رتبه اعتباری</li>
                            <li>امکان درخواست وام جدید با شرایط بهتر</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="مقایسه هزینه بانک‌های دیگر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">هزینه‌های بانک مهر ایران نسبت به سایر بانک‌ها چطور است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        بانک مهر ایران <strong>رقابتی‌ترین نرخ‌ها را ارائه می‌دهد:</strong>
                        <div class="mt-3">
                            <strong>مقایسه کارمزد:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>بانک مهر ایران: ۴٪</li>
                                <li>متوسط سایر بانک‌ها: ۶-۸٪</li>
                                <li>وام‌های ربوی: ۲۰-۲۴٪ سالانه</li>
                            </ul>
                        </div>
                        <p class="mt-3">علاوه بر نرخ پایین، <strong>سرعت و کیفیت خدمات</strong> نیز بالاتر است.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 6: امنیت (Security) -->
        <div class="faq-category" data-category="security">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    امنیت و حریم خصوصی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="security" data-keywords="امنیت اطلاعات شخصی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">امنیت اطلاعات شخصی من چگونه تضمین می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        بانک مهر ایران از <strong>بالاترین استانداردهای امنیتی</strong> استفاده می‌کند:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>رمزنگاری SSL 256-bit</strong> برای انتقال داده‌ها</li>
                            <li>ذخیره اطلاعات در سرورهای امن داخل کشور</li>
                            <li>احراز هویت چندمرحله‌ای</li>
                            <li>نظارت ۲۴ساعته بر سیستم‌ها</li>
                            <li>پشتیبان‌گیری منظم و ایمن</li>
                            <li>تأیید استانداردهای بانک مرکزی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="اشتراک اطلاعات سومین شخص">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا اطلاعات من با اشخاص ثالث به اشتراک گذاشته می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>اطلاعات شما محرمانه است</strong> و تنها در موارد زیر استفاده می‌شود:</p>
                        <div class="mt-3">
                            <strong>موارد مجاز قانونی:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>استعلام از بانک مرکزی (الزامی)</li>
                                <li>گزارش به مراجع قانونی در صورت درخواست</li>
                                <li>خدمات بیمه (با اجازه مشتری)</li>
                            </ul>
                        </div>
                        <p class="mt-3"><em>هیچگاه اطلاعات برای مقاصد تبلیغاتی یا تجاری به غیر واگذار نمی‌شود.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="تأیید هویت احراز">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">فرآیند تأیید هویت چگونه انجام می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        تأیید هویت در <strong>چندین مرحله امن</strong> انجام می‌شود:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>مرحله ۱:</strong> بررسی اصالت مدارک هویتی</li>
                            <li><strong>مرحله ۲:</strong> تطبیق با پایگاه ثبت احوال</li>
                            <li><strong>مرحله ۳:</strong> تماس تلفنی تأییدی</li>
                            <li><strong>مرحله ۴:</strong> پیامک کد فعال‌سازی</li>
                            <li><strong>مرحله ۵:</strong> بازدید حضوری (در صورت لزوم)</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="کلاهبرداری مراقبت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از کلاهبرداری‌ها محافظت کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>نکات مهم امنیتی:</strong></p>
                        <div class="mt-3">
                            <strong>هرگز این کارها را نکنید:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>ارائه کلمه عبور به هیچ کس</li>
                                <li>کلیک بر لینک‌های مشکوک</li>
                                <li>ارسال عکس مدارک از طریق شبکه‌های اجتماعی</li>
                                <li>پذیرش تماس‌های مشکوک</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <strong>روش‌های امن:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>ورود مستقیم از سایت رسمی</li>
                                <li>تماس با شماره‌های رسمی بانک</li>
                                <li>استفاده از اپلیکیشن رسمی</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="گزارش مشکل امنیتی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر مشکل امنیتی مشاهده کردم به کجا گزارش دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>راه‌های گزارش فوری مشکلات امنیتی:</strong></p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>خط ویژه امنیت:</strong> ۰۲۱-۲۱۴۷۴۷۴۷</li>
                            <li><strong>ایمیل امنیت:</strong> security@mehriran-bank.ir</li>
                            <li><strong>تلگرام پشتیبانی:</strong> @mehriran_support</li>
                            <li><strong>پیامک:</strong> ۱۰۰۰۴۴۱۰۱۰</li>
                            <li><strong>حضوری:</strong> نزدیک‌ترین شعبه</li>
                        </ul>
                        <p class="mt-3"><em>پاسخ‌گویی ۲۴ ساعته در تمام روزهای هفته</em></p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 7: مقررات (Regulations) -->
        <div class="faq-category" data-category="regulations">
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    قوانین و مقررات
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="regulations" data-keywords="قوانین بانک مرکزی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">وام قرض‌الحسنه تابع چه قوانینی است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>وام قرض‌الحسنه تابع <strong>قوانین بانک مرکزی و شرع</strong> است:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>قانون عملیات بانکی بدون ربا</strong> - مصوب ۱۳۶۲</li>
                            <li><strong>دستورالعمل‌های بانک مرکزی</strong> - به‌روزرسانی ۱۴۰۲</li>
                            <li><strong>اصول شریعت اسلامی</strong> - نظارت شورای نگهبان</li>
                            <li><strong>قانون حمایت از حقوق مصرف‌کنندگان</strong></li>
                            <li><strong>قانون مبارزه با پولشویی</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="regulations" data-keywords="حقوق مشتری قانونی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">حقوق قانونی من به عنوان مشتری چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>حقوق قانونی مشتریان:</strong></p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>دریافت اطلاعات شفاف و کامل درباره شرایط</li>
                            <li>محرمانگی کامل اطلاعات شخصی</li>
                            <li>حق اعتراض و شکایت</li>
                            <li>دسترسی به گزارش اعتباری شخصی</li>
                            <li>انصراف از قرارداد قبل از واریز</li>
                            <li>پرداخت زودهنگام بدون جریمه</li>
                            <li>دریافت خدمات بانکی یکسان</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="regulations" data-keywords="شکایت اعتراض">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه می‌توانم شکایت یا اعتراض کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>مراحل طرح شکایت:</strong></p>
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>مرحله ۱:</strong> تماس با پشتیبانی بانک</li>
                            <li><strong>مرحله ۲:</strong> ارسال شکایت کتبی</li>
                            <li><strong>مرحله ۳:</strong> پیگیری از طریق کد رهگیری</li>
                            <li><strong>مرحله ۴:</strong> شکایت به بانک مرکزی</li>
                            <li><strong>مرحله ۵:</strong> مراجعه به مراجع قضایی</li>
                        </ol>
                        <div class="mt-3">
                            <strong>مهلت پاسخ:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>پشتیبانی: فوری تا ۲۴ ساعت</li>
                                <li>شکایت رسمی: ۷ روز کاری</li>
                                <li>بانک مرکزی: ۳۰ روز</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="regulations" data-keywords="مسئولیت بانک تعهدات">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">تعهدات و مسئولیت‌های بانک چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>تعهدات قانونی بانک:</strong></p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>ارائه خدمات طبق شرایط اعلام شده</li>
                            <li>حفظ محرمانگی اطلاعات مشتریان</li>
                            <li>پاسخگویی سریع به درخواست‌ها</li>
                            <li>اعمال نرخ‌های مصوب بانک مرکزی</li>
                            <li>گزارش‌دهی دقیق به مراجع نظارتی</li>
                            <li>جبران خسارت در صورت اشتباه بانک</li>
                            <li>ارائه راهنمایی و مشاوره</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="regulations" data-keywords="قرارداد شرایط">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">شرایط قرارداد وام شامل چه مواردی است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>بندهای اصلی قرارداد:</strong></p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>مبلغ، مدت و نحوه بازپرداخت</li>
                            <li>کارمزد و هزینه‌های جانبی</li>
                            <li>نوع و میزان ضمانت‌ها</li>
                            <li>حقوق و تکالیف طرفین</li>
                            <li>شرایط نقض قرارداد</li>
                            <li>نحوه حل اختلافات</li>
                            <li>قوانین حاکم و صلاحیت دادگاه</li>
                        </ul>
                        <p class="mt-3"><em>توصیه: قبل از امضا، قرارداد را به دقت مطالعه کنید.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="regulations" data-keywords="نقض قرارداد عواقب">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">عواقب نقض قرارداد وام چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>عواقب قانونی نقض قرارداد:</strong></p>
                        <div class="mt-3">
                            <strong>عواقب مالی:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>اعمال جریمه تأخیر</li>
                                <li>سررسید کل بدهی</li>
                                <li>اجرای ضمانت‌ها</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <strong>عواقب اعتباری:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>کاهش شدید رتبه اعتباری</li>
                                <li>ثبت در فهرست بدحساب‌ها</li>
                                <li>منع دریافت تسهیلات جدید</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <strong>عواقب قانونی:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>پرونده قضایی</li>
                                <li>اجرای احکام</li>
                                <li>منع خروج از کشور</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 8: خدمات (Services) -->
        <div class="faq-category" data-category="services">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    خدمات جانبی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="services" data-keywords="مشاوره رایگان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا خدمات مشاوره رایگان ارائه می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>بله، خدمات مشاوره کاملاً رایگان</strong> ارائه می‌شود:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>مشاوره اعتباری:</strong> بهبود رتبه و وضعیت</li>
                            <li><strong>مشاوره مالی:</strong> برنامه‌ریزی بازپرداخت</li>
                            <li><strong>مشاوره حقوقی:</strong> توضیح قوانین و مقررات</li>
                            <li><strong>راهنمایی فرآیند:</strong> تکمیل مدارک و درخواست</li>
                            <li><strong>پشتیبانی ۲۴ ساعته:</strong> در تمام مراحل</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="services" data-keywords="بیمه وام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا امکان بیمه کردن وام وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>بله، بیمه وام به صورت اختیاری</strong> قابل خریداری است:</p>
                        <div class="mt-3">
                            <strong>انواع پوشش بیمه:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li><strong>بیمه عمر:</strong> تسویه وام در صورت فوت</li>
                                <li><strong>بیمه ناتوانی:</strong> توقف اقساط در صورت ناتوانی</li>
                                <li><strong>بیمه بیکاری:</strong> تعویق اقساط در صورت بیکاری</li>
                                <li><strong>بیمه ترکیبی:</strong> پوشش جامع</li>
                            </ul>
                        </div>
                        <p class="mt-3">حق بیمه: <strong>۰.۵ تا ۱.۵٪ مبلغ وام</strong> بسته به نوع پوشش</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="services" data-keywords="همراه بانک اپلیکیشن">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه خدمات آنلاینی برای پیگیری وام وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>خدمات آنلاین جامع</strong> برای مدیریت وام:</p>
                        <div class="mt-3">
                            <strong>همراه بانک مهر ایران:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>مشاهده جزئیات وام و اقساط</li>
                                <li>پرداخت قسط آنلاین</li>
                                <li>دریافت گزارش وضعیت</li>
                                <li>تنظیم یادآوری پرداخت</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <strong>درگاه اینترنتی:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>درخواست آنلاین وام جدید</li>
                                <li>بارگذاری مدارک</li>
                                <li>پیگیری وضعیت درخواست</li>
                                <li>دریافت قرارداد الکترونیکی</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="services" data-keywords="تسویه زودهنگام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">خدمات تسویه زودهنگام چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>تسویه زودهنگام با مزایای ویژه:</strong></p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>بدون جریمه:</strong> هیچ کسری اضافی</li>
                            <li><strong>تخفیف کارمزد:</strong> کاهش متناسب با زمان باقیمانده</li>
                            <li><strong>آزادسازی ضمانت:</strong> فوری پس از تسویه</li>
                            <li><strong>گواهی تسویه:</strong> صدور در همان روز</li>
                            <li><strong>بهبود رتبه:</strong> تأثیر مثبت بر اعتبار</li>
                        </ul>
                        <p class="mt-3"><em>محاسبه دقیق مبلغ تسویه از طریق همراه بانک یا شعب</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="services" data-keywords="تبدیل ارز">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا امکان دریافت وام با ارز خارجی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>خیر، وام قرض‌الحسنه تنها به ریال</strong> ارائه می‌شود طبق مقررات بانک مرکزی. اما خدمات جانبی شامل:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>تبدیل ارز با نرخ ترجیحی</li>
                            <li>حواله ارزی برای پرداخت خارجی</li>
                            <li>مشاوره سرمایه‌گذاری ارزی</li>
                            <li>خدمات بازرگانی و صادراتی</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 9: رفع مشکل (Troubleshooting) -->
        <div class="faq-category" data-category="troubleshooting">
            <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    رفع مشکلات رایج
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="troubleshooting" data-keywords="رد درخواست دلیل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">درخواستم رد شده، چه کار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>در صورت رد درخواست، <strong>مراحل زیر را دنبال کنید:</strong></p>
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>دریافت علت رد:</strong> تماس با پشتیبانی برای دریافت دلیل دقیق</li>
                            <li><strong>رفع نواقص:</strong> اقدام برای رفع مشکلات اعلام شده</li>
                            <li><strong>بهبود رتبه:</strong> اگر رتبه اعتباری مشکل است</li>
                            <li><strong>تکمیل مدارک:</strong> ارائه مدارک کامل‌تر یا بهتر</li>
                            <li><strong>درخواست مجدد:</strong> پس از رفع نواقص</li>
                        </ol>
                        <p class="mt-3"><em>فاصله حداقل ۳۰ روز بین درخواست‌های مکرر لازم است.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="troubleshooting" data-keywords="مشکل پرداخت قسط">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر نتوانم قسط پرداخت کنم چه اتفاقی می‌افتد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>فوراً با بانک تماس بگیرید</strong> برای بررسی گزینه‌های زیر:</p>
                        <div class="mt-3">
                            <strong>راه‌حل‌های ممکن:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li><strong>تعویق پرداخت:</strong> تا ۳ ماه با توجیه مناسب</li>
                                <li><strong>تنظیم مجدد:</strong> کاهش مبلغ قسط، افزایش مدت</li>
                                <li><strong>پرداخت جزئی:</strong> بخشی از قسط در مواقع اضطرار</li>
                                <li><strong>تغییر تاریخ:</strong> انطباق با زمان دریافت حقوق</li>
                            </ul>
                        </div>
                        <p class="mt-3"><strong>مهم:</strong> عدم تماس و رها کردن وضعیت باعث مشکلات جدی می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="troubleshooting" data-keywords="فراموشی کلمه عبور">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کلمه عبور همراه بانک را فراموش کرده‌ام، چه کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>بازیابی کلمه عبور به سه روش:</strong></p>
                        <div class="mt-3">
                            <strong>روش ۱ - خودکار:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>کلیک "فراموشی کلمه عبور" در اپلیکیشن</li>
                                <li>وارد کردن کد ملی و شماره موبایل</li>
                                <li>دریافت کد تأیید از طریق پیامک</li>
                                <li>تنظیم کلمه عبور جدید</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <strong>روش ۲ - تلفنی:</strong>
                            <ul class="list-disc mr-6 mt-2 space-y-1">
                                <li>تماس با ۰۲۱-۲۱۴۷۴۷۴۷</li>
                                <li>احراز هویت تلفنی</li>
                                <li>بازنشانی فوری</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <strong>روش ۳ - حضوری:</strong>
                            <p>مراجعه با کارت ملی به نزدیک‌ترین شعبه</p>
                        </div>
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
    
    .grid.grid-cols-1.md\:grid-cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>