{{-- Comprehensive Searchable FAQ Section for Loan Guarantee Inquiry Service --}}
{{-- سوالات متداول جامع و قابل جستجو برای خدمات استعلام ضامن وام --}}

<!-- Enhanced FAQ Section with Search and Categories -->
<section class="mt-12 mb-12" id="comprehensive-faqs">
    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8 mb-8">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-dark-sky-700 mb-4 flex items-center justify-center gap-3">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                مرجع کامل سوالات متداول استعلام ضامن وام
            </h2>
            <p class="text-gray-700 text-lg leading-relaxed">
                بیش از <strong>67 سوال و پاسخ تخصصی</strong> درباره استعلام ضمانت وام و اعتبارسنجی ضامن
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
                    همه موضوعات (۶۷)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="general">
                    عمومی (۱۰)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="systems">
                    سامانه‌های استعلام (۷)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="process">
                    فرآیند استعلام (۷)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="documents">
                    مدارک و شرایط (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="costs">
                    هزینه‌ها و پرداخت (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="security">
                    امنیت و حریم خصوصی (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="legal">
                    قوانین و مقررات (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="support">
                    خدمات و پشتیبانی (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="troubleshooting">
                    رفع مشکل (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="advanced">
                    نکات پیشرفته (۴)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="reports">
                    گزارش‌ها و نتایج (۳)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="banks">
                    ارتباط با بانک‌ها (۳)
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

        <!-- Category 1: عمومی (General) - 10 FAQs -->
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
                
                <div class="faq-item p-6" data-category="general" data-keywords="استعلام ضامن وام چیست تعریف">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">استعلام ضامن وام چیست و چه اطلاعاتی ارائه می‌دهد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>استعلام ضامن وام فرآیند بررسی وضعیت اعتباری و توانایی مالی ضامن از طریق <strong>سامانه‌های رسمی بانک مرکزی و مراکز اطلاعات اعتباری</strong> است. این استعلام اطلاعات جامعی شامل:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>امتیاز اعتباری (Credit Score) ضامن</li>
                            <li>تاریخچه بازپرداخت وام‌ها و تسهیلات قبلی</li>
                            <li>وضعیت تعهدات جاری و بدهی‌ها</li>
                            <li>میزان درآمد و توانایی بازپرداخت</li>
                            <li>سوابق چک برگشتی و اعتراضات</li>
                            <li>وضعیت اشخاص وابسته و ضمانت‌های قبلی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="ضامن وام شرایط صلاحیت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه کسانی می‌توانند ضامن وام شوند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        شرایط اصلی برای ضامن وام عبارتند از:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>سن:</strong> بین ۲۲ تا ۶۵ سال</li>
                            <li><strong>درآمد ثابت:</strong> حداقل ۲ برابر قسط ماهانه وام</li>
                            <li><strong>امتیاز اعتباری:</strong> حداقل ۶۰۰ امتیاز</li>
                            <li><strong>سابقه کاری:</strong> حداقل ۲ سال سابقه شغلی ثابت</li>
                            <li><strong>عدم بدهی معوق:</strong> بدون بدهی غیرجاری یا چک برگشتی</li>
                            <li><strong>تابعیت ایرانی:</strong> داشتن کد ملی معتبر</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="انواع وام ضمانت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">برای چه نوع وام‌هایی ضامن نیاز است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        انواع وام‌هایی که معمولاً نیاز به ضامن دارند:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>وام‌های قرض‌الحسنه</strong> بالای ۵۰ میلیون تومان</li>
                            <li><strong>وام مسکن</strong> تکمیلی و تجاری</li>
                            <li><strong>تسهیلات خودرو</strong> برای خریدهای بالای ۲۰۰ میلیون</li>
                            <li><strong>وام‌های کسب و کار</strong> و سرمایه در گردش</li>
                            <li><strong>تسهیلات تولیدی</strong> و صنعتی</li>
                            <li><strong>اعتبارات اسنادی</strong> و ضمانت‌نامه‌ها</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="مسئولیت ضامن حقوقی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مسئولیت‌های حقوقی ضامن چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>ضامن متعهد به بازپرداخت کامل وام در صورت عدم پرداخت توسط متقاضی است.</strong> مسئولیت‌های اصلی شامل:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li>پرداخت اصل وام و سود در صورت تخلف متقاضی</li>
                            <li>پرداخت خسارات تأخیر و هزینه‌های وصول مطالبات</li>
                            <li>همکاری در فرآیند وصول و پیگیری</li>
                            <li>ارائه اطلاعات به‌روز درباره وضعیت مالی</li>
                            <li>عدم انتقال دارایی‌ها بدون اطلاع بانک</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="ضامن چند وام همزمان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">یک نفر می‌تواند ضامن چند وام باشد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        تعداد وام‌هایی که یک فرد می‌تواند ضمانت کند بستگی به <strong>ظرفیت مالی و امتیاز اعتباری</strong> او دارد. محدودیت‌های کلی:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>حداکثر <strong>۳ وام همزمان</strong> برای اشخاص عادی</li>
                            <li>مجموع تعهدات نباید از <strong>۵ برابر درآمد سالانه</strong> تجاوز کند</li>
                            <li>ارزیابی مجدد ظرفیت برای هر درخواست جدید</li>
                            <li>در نظر گیری ریسک تجمعی توسط بانک</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="ضامن خانواده بستگان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا اعضای خانواده می‌توانند ضامن یکدیگر باشند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، اعضای خانواده می‌توانند ضامن یکدیگر باشند</strong> اما با محدودیت‌هایی:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>همسر می‌تواند ضامن همسر خود باشد</li>
                            <li>والدین و فرزندان بالای ۲۲ سال مجاز هستند</li>
                            <li>خواهر و برادر با شرایط مالی مناسب</li>
                            <li>بررسی دقیق‌تر ریسک توسط بانک</li>
                            <li>احتمال درخواست ضمانت‌های اضافی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="ضامن بازنشسته سن">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا افراد بازنشسته می‌توانند ضامن باشند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، بازنشستگان می‌توانند ضامن باشند</strong> در صورت داشتن شرایط زیر:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>سن کمتر از ۷۰ سال</strong> در پایان دوره وام</li>
                            <li>حقوق بازنشستگی <strong>حداقل ۳ میلیون تومان</strong></li>
                            <li>سابقه حداقل <strong>۵ سال بازنشستگی</strong></li>
                            <li>امتیاز اعتباری مناسب و عدم بدهی معوق</li>
                            <li>احتمال نیاز به ضمانت‌های تکمیلی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="ضامن اتباع خارجی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اتباع خارجی می‌توانند ضامن باشند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        اتباع خارجی در شرایط خاصی می‌توانند ضامن باشند:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>داشتن اقامت دائم</strong> یا کارت اقامت معتبر</li>
                            <li>سابقه حداقل <strong>۵ سال زندگی در ایران</strong></li>
                            <li>درآمد ثابت و قابل اثبات در ایران</li>
                            <li>ضمانت‌های تکمیلی (املاک، سپرده)</li>
                            <li><strong>تأیید وزارت کشور</strong> برای مبالغ بالا</li>
                            <li>محدودیت در انواع خاصی از وام‌ها</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="ضامن شرکت حقوقی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">شرکت‌ها و اشخاص حقوقی می‌توانند ضامن باشند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، اشخاص حقوقی می‌توانند ضامن باشند</strong> با شرایط خاص:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li>داشتن شناسه ملی و ثبت رسمی</li>
                            <li><strong>سابقه مالی</strong> حداقل ۳ سال</li>
                            <li>تأیید <strong>مجمع عمومی</strong> یا هیئت مدیره</li>
                            <li>ارائه صورت‌های مالی حسابرسی شده</li>
                            <li>ضمانت شخصی مدیران (در مواردی)</li>
                            <li>بررسی دقیق‌تر وضعیت مالی شرکت</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="ضامن انصراف لغو">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">ضامن چگونه می‌تواند از ضمانت خود انصراف دهد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        انصراف از ضمانت فقط در شرایط محدودی امکان‌پذیر است:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>قبل از تصویب وام:</strong> انصراف کامل بدون مسئولیت</li>
                            <li><strong>بعد از تصویب:</strong> نیاز به موافقت بانک و متقاضی</li>
                            <li><strong>ضامن جایگزین:</strong> معرفی فرد واجد شرایط</li>
                            <li><strong>تسویه کامل:</strong> پرداخت کل بدهی توسط متقاضی</li>
                            <li><strong>شرایط خاص:</strong> فوت، ناتوانی، ورشکستگی</li>
                            <li><em>مشاوره حقوقی ضروری است</em></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 2: سامانه‌های استعلام (Inquiry Systems) - 7 FAQs -->
        <div class="faq-category" data-category="systems">
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                    سامانه‌های استعلام
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="systems" data-keywords="mycredit بانک مرکزی استعلام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه mycredit.ir چیست و چگونه کار می‌کند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>mycredit.ir سامانه رسمی اعتبارسنجی بانک مرکزی ایران است</strong> که اطلاعات کاملی از وضعیت اعتباری افراد ارائه می‌دهد:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>امتیاز اعتباری (Credit Score) بر اساس الگوریتم بانک مرکزی</li>
                            <li>تاریخچه کامل وام‌ها و تسهیلات دریافتی</li>
                            <li>وضعیت بازپرداخت و تعهدات جاری</li>
                            <li>اطلاعات چک‌های برگشتی و اعتراضات</li>
                            <li>پیش‌بینی ریسک اعتباری</li>
                            <li>دسترسی آنلاین ۲۴ ساعته</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="systems" data-keywords="ICE استعلام icescoring">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه ICE (icescoring.com) چه ویژگی‌هایی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>ICE (Iran Credit Evaluation)</strong> مرکز اطلاعات اعتباری خصوصی است که خدمات پیشرفته‌تری ارائه می‌دهد:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>گزارش‌های تفصیلی</strong> با تحلیل‌های آماری</li>
                            <li>مقایسه با میانگین صنعت و منطقه</li>
                            <li>پیش‌بینی روند اعتباری آینده</li>
                            <li>تحلیل رفتار پرداخت در بخش‌های مختلف</li>
                            <li>گزارش‌های سفارشی برای بانک‌ها</li>
                            <li>به‌روزرسانی سریع‌تر اطلاعات</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="systems" data-keywords="rade.ir پلتفرم استعلام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">پلتفرم rade.ir چه امکاناتی برای استعلام دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>rade.ir پلتفرم جامع خدمات مالی و اعتباری</strong> با قابلیت‌های متنوع:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>استعلام گروهی و دسته‌ای ضامنین</li>
                            <li>مقایسه چندین ضامن همزمان</li>
                            <li>گزارش‌های مدیریتی برای تصمیم‌گیری</li>
                            <li>تحلیل ریسک پورتفوی ضمانت‌ها</li>
                            <li>اتصال به API بانک‌ها</li>
                            <li>قابلیت ذخیره و مدیریت پرونده‌ها</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="systems" data-keywords="بانک مخصوص SEPAM سامانه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه SEPAM بانک‌های خصوصی چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>SEPAM (سامانه پایش اعتباری مشترک)</strong> شبکه اطلاعاتی بانک‌های خصوصی:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>اشتراک اطلاعات بین بانک‌های عضو</li>
                            <li><strong>به‌روزرسانی روزانه</strong> وضعیت مشتریان</li>
                            <li>پوشش بانک‌های پاسارگاد، تجارت، صنعت ایران</li>
                            <li>تشخیص مشتریان مشکوک و پرریسک</li>
                            <li>گزارش‌های تخصصی برای هر بانک</li>
                            <li>امکان ردیابی تعهدات متقابل</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="systems" data-keywords="پیشخوانک سامانه دسترسی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">از طریق پیشخوانک چه استعلاماتی امکان‌پذیر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>پیشخوانک دسترسی یکپارچه به تمام سامانه‌های استعلام</strong> ارائه می‌دهد:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>استعلام mycredit با تخفیف ویژه</li>
                            <li>دسترسی به سامانه ICE</li>
                            <li>اتصال به شبکه SEPAM</li>
                            <li>استعلام سریع با کد ملی</li>
                            <li><strong>قیمت واحد ۲۰ هزار تومان</strong></li>
                            <li>گزارش جامع در کمتر از ۱۰ ثانیه</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="systems" data-keywords="سامانه انتخاب تفاوت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">برای استعلام ضامن کدام سامانه بهتر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        انتخاب سامانه بستگی به نیاز شما دارد:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>mycredit.ir:</strong> استعلام رسمی و پایه (۸ هزار تومان)</li>
                            <li><strong>ICE:</strong> تحلیل پیشرفته و تخصصی (۱۲ هزار تومان)</li>
                            <li><strong>SEPAM:</strong> برای بانک‌های خاص</li>
                            <li><strong>پیشخوانک:</strong> ترکیب همه + سرعت بالا (۲۰ هزار تومان)</li>
                            <li><em>توصیه: پیشخوانک برای استعلام جامع</em></li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="systems" data-keywords="سامانه به‌روزرسانی فرکانس">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اطلاعات سامانه‌ها چقدر یکبار به‌روزرسانی می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        فرکانس به‌روزرسانی سامانه‌های مختلف:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>mycredit.ir:</strong> ماهانه (روز ۱۵ هر ماه)</li>
                            <li><strong>ICE:</strong> دو هفته یکبار</li>
                            <li><strong>SEPAM:</strong> روزانه برای بانک‌های عضو</li>
                            <li><strong>پیشخوانک:</strong> ترکیب آخرین اطلاعات همه سامانه‌ها</li>
                            <li><em>نکته: برای تصمیم‌گیری‌های مهم، استعلام تازه انجام دهید</em></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 3: فرآیند استعلام (Inquiry Process) - 7 FAQs -->
        <div class="faq-category" data-category="process">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    فرآیند استعلام
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="process" data-keywords="مراحل استعلام گام به گام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مراحل استعلام ضامن گام به گام چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        فرآیند کامل استعلام ضامن در ۵ مرحله:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>دریافت اطلاعات:</strong> کد ملی و شماره موبایل ضامن</li>
                            <li><strong>تأیید هویت:</strong> ارسال کد تأیید به موبایل ضامن</li>
                            <li><strong>پردازش استعلام:</strong> جستجو در تمام سامانه‌ها (۵-۱۰ ثانیه)</li>
                            <li><strong>تهیه گزارش:</strong> تولید گزارش جامع اعتباری</li>
                            <li><strong>ارسال نتیجه:</strong> نمایش نتایج و امکان دانلود PDF</li>
                        </ol>
                        <p class="mt-3 text-sm text-blue-600"><strong>مدت زمان کل: کمتر از ۲ دقیقه</strong></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="process" data-keywords="کد ملی موبایل احراز">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چرا برای استعلام به شماره موبایل ضامن نیاز است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        شماره موبایل برای <strong>احراز هویت و حفاظت از حریم خصوصی</strong> ضروری است:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>تأیید هویت:</strong> اطمینان از رضایت ضامن</li>
                            <li><strong>جلوگیری از سوءاستفاده:</strong> استعلام بدون اجازه غیرقانونی است</li>
                            <li><strong>الزام قانونی:</strong> طبق قانون حمایت از اطلاعات شخصی</li>
                            <li><strong>ارسال کد OTP:</strong> تأیید دو مرحله‌ای</li>
                            <li><strong>اطلاع‌رسانی:</strong> آگاهی ضامن از استعلام</li>
                            <li>شماره موبایل باید <em>متصل به کد ملی در سامانه بانکی</em> باشد</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="process" data-keywords="زمان استعلام سرعت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">استعلام ضامن چقدر زمان می‌برد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        زمان‌بندی فرآیند استعلام:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>ورود اطلاعات:</strong> ۳۰ ثانیه</li>
                            <li><strong>احراز هویت:</strong> ۶۰ ثانیه (دریافت و وارد کردن OTP)</li>
                            <li><strong>پردازش داده:</strong> ۵-۱۰ ثانیه</li>
                            <li><strong>تولید گزارش:</strong> ۱۵ ثانیه</li>
                            <li><strong>مجموع:</strong> حداکثر ۲ دقیقه</li>
                        </ul>
                        <p class="mt-3 text-sm text-green-600"><em>✅ سریع‌ترین سامانه استعلام در کشور</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="process" data-keywords="استعلام دسته‌ای گروهی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم چندین ضامن را همزمان استعلام کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، امکان استعلام دسته‌ای موجود است:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>حداکثر <strong>۱۰ ضامن همزمان</strong></li>
                            <li>هر ضامن نیاز به تأیید جداگانه دارد</li>
                            <li>گزارش مقایسه‌ای کلی ارائه می‌شود</li>
                            <li><strong>تخفیف ۲۰٪</strong> برای استعلام بیش از ۵ نفر</li>
                            <li>مناسب برای بانک‌ها و مؤسسات مالی</li>
                            <li>امکان صادرات اکسل برای تحلیل</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="process" data-keywords="موبایل خاموش OTP مشکل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر موبایل ضامن خاموش باشد چه کار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        راه‌حل‌های موجود برای عدم دسترسی به موبایل:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>انتظار:</strong> حداکثر ۲۴ ساعت برای روشن شدن موبایل</li>
                            <li><strong>شماره دوم:</strong> استفاده از شماره موبایل جایگزین</li>
                            <li><strong>استعلام حضوری:</strong> مراجعه ضامن با کارت ملی</li>
                            <li><strong>تأیید بانکی:</strong> احراز هویت از طریق بانک محل حساب</li>
                            <li><strong>نامه رسمی:</strong> تأیید کتبی ضامن (برای مبالغ بالا)</li>
                            <li><em>هماهنگی با پشتیبانی ۰۲۱-۷۷۳۳۴۴۵۵</em></li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="process" data-keywords="استعلام تکرار مجدد">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چند بار می‌توانم یک ضامن را استعلام کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        محدودیت‌های تکرار استعلام:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>روزانه:</strong> حداکثر ۳ بار برای هر کد ملی</li>
                            <li><strong>ماهانه:</strong> حداکثر ۱۰ بار</li>
                            <li><strong>استعلام مجدد:</strong> بعد از ۷۲ ساعت رایگان</li>
                            <li><strong>تغییرات جزئی:</strong> اطلاع‌رسانی خودکار</li>
                            <li><strong>استعلام اضطراری:</strong> با تأیید مدیریت سامانه</li>
                            <li><em>هدف: جلوگیری از آزار ضامن</em></li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="process" data-keywords="استعلام آفلاین حضوری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا استعلام حضوری/آفلاین امکان‌پذیر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، استعلام حضوری در موارد خاص امکان‌پذیر است:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>مراجعه حضوری ضامن</strong> با کارت ملی اصل</li>
                            <li>تکمیل فرم رضایت‌نامه استعلام</li>
                            <li>ارائه مدارک هویتی تکمیلی</li>
                            <li>انجام استعلام در حضور ضامن</li>
                            <li><strong>هزینه اضافی:</strong> ۱۰ هزار تومان</li>
                            <li>دریافت گزارش چاپی در همان جلسه</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 4: مدارک و شرایط (Documents & Requirements) - 6 FAQs -->
        <div class="faq-category" data-category="documents">
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    مدارک و شرایط
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="documents" data-keywords="مدارک لازم ضامن">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه مداركی برای استعلام ضامن لازم است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مدارک ضروری برای استعلام ضامن:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>کد ملی ۱۰ رقمی</strong> ضامن</li>
                            <li><strong>شماره موبایل فعال</strong> متصل به کد ملی</li>
                            <li>نام و نام خانوادگی کامل</li>
                            <li>تاریخ تولد (برای تأیید هویت)</li>
                            <li>رضایت‌نامه کتبی ضامن (در صورت لزوم)</li>
                        </ul>
                        <p class="mt-3 text-sm text-blue-600"><strong>نکته:</strong> تمام مدارک باید معتبر و به‌روز باشند</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="documents" data-keywords="رضایت‌نامه موافقت ضامن">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا رضایت‌نامه کتبی ضامن ضروری است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>رضایت ضامن الزامی است</strong> و به روش‌های مختلفی اخذ می‌شود:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>تأیید پیامکی:</strong> از طریق OTP (روش استاندارد)</li>
                            <li><strong>رضایت‌نامه کتبی:</strong> برای مبالغ بالای ۵۰۰ میلیون</li>
                            <li><strong>تأیید حضوری:</strong> در صورت مشکل در OTP</li>
                            <li><strong>ثبت صوتی:</strong> تماس تلفنی ضبط شده</li>
                            <li><strong>تأیید دیجیتال:</strong> امضای الکترونیکی</li>
                            <li><em>بدون رضایت، استعلام انجام نمی‌شود</em></li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="documents" data-keywords="کد ملی نامعتبر مشکل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر کد ملی ضامن نامعتبر باشد چه کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        راه‌حل‌های مواجهه با کد ملی نامعتبر:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>بررسی مجدد:</strong> تایپ صحیح ۱۰ رقم</li>
                            <li><strong>کد ملی جدید:</strong> مراجعه به دفتر ثبت احوال</li>
                            <li><strong>کد موقت:</strong> دریافت کد جایگزین</li>
                            <li><strong>تأیید حضوری:</strong> با مدارک اصلی</li>
                            <li><strong>استعلام دستی:</strong> از طریق بانک مرکزی</li>
                        </ol>
                        <p class="mt-3 text-sm text-red-600"><strong>هشدار:</strong> استفاده از کد ملی جعلی جرم است</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="documents" data-keywords="موبایل ثبت نشده SIM">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر موبایل ضامن به نام او ثبت نشده باشد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>موبایل باید حتماً به نام ضامن ثبت باشد.</strong> راه‌حل‌ها:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>ثبت نام SIM:</strong> مراجعه فوری به اپراتور</li>
                            <li><strong>شماره دیگر:</strong> استفاده از موبایل ثبت شده</li>
                            <li><strong>انتقال مالکیت:</strong> از مالک قبلی</li>
                            <li><strong>استعلام حضوری:</strong> در صورت عدم امکان ثبت</li>
                            <li><strong>SIM جدید:</strong> خرید خط جدید</li>
                            <li><em>مدت زمان ثبت: ۲۴-۴۸ ساعت</em></li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="documents" data-keywords="مدارک تکمیلی اضافی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">در چه مواردی مدارک تکمیلی لازم است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        موارد نیاز به مدارک اضافی:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>وام‌های بالای ۱ میلیارد:</strong> گواهی عدم سوء پیشینه</li>
                            <li><strong>ضامن خارجی:</strong> اسناد اقامت و ویزا</li>
                            <li><strong>ضامن شرکت:</strong> مدارک ثبتی و مالی</li>
                            <li><strong>تناقض اطلاعات:</strong> مدارک هویتی اضافی</li>
                            <li><strong>سوابق منفی:</strong> توضیحات کتبی</li>
                            <li><strong>تغییر نام:</strong> گواهی تغییر نام رسمی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="documents" data-keywords="مدارک اعتبار زمان انقضا">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مدت اعتبار گزارش استعلام چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مدت اعتبار گزارش‌ها بستگی به منظور استفاده دارد:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>وام‌های عادی:</strong> ۳۰ روز</li>
                            <li><strong>وام‌های مسکن:</strong> ۴۵ روز</li>
                            <li><strong>تسهیلات تجاری:</strong> ۱۵ روز</li>
                            <li><strong>ضمانت‌نامه:</strong> ۶۰ روز</li>
                            <li><strong>استعلام شخصی:</strong> ۹۰ روز</li>
                            <li><em>توصیه: استعلام تازه برای تصمیمات مهم</em></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 5: هزینه‌ها و پرداخت (Costs & Payment) - 6 FAQs -->
        <div class="faq-category" data-category="costs">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    هزینه‌ها و پرداخت
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="costs" data-keywords="قیمت هزینه ۲۰ هزار تومان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">هزینه استعلام ضامن چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>هزینه استعلام ضامن ۲۰,۰۰۰ تومان</strong> است که شامل:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>دسترسی به تمام سامانه‌های اعتباری</li>
                            <li>گزارش جامع و تفصیلی</li>
                            <li>امکان دانلود PDF</li>
                            <li>پشتیبانی تلفنی</li>
                            <li>گارانتی صحت اطلاعات</li>
                        </ul>
                        <div class="mt-3 p-3 bg-green-50 rounded-lg">
                            <p class="text-green-700 text-sm"><strong>💡 ارزان‌ترین قیمت:</strong> در مقایسه با سایر سامانه‌ها</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="تخفیف گروهی دسته‌ای">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا برای استعلام دسته‌ای تخفیف داریم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، تخفیفات ویژه برای استعلام دسته‌ای:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>۵-۱۰ استعلام:</strong> تخفیف ۱۰٪ (۱۸ هزار تومان)</li>
                            <li><strong>۱۱-۲۰ استعلام:</strong> تخفیف ۲۰٪ (۱۶ هزار تومان)</li>
                            <li><strong>۲۱-۵۰ استعلام:</strong> تخفیف ۳۰٪ (۱۴ هزار تومان)</li>
                            <li><strong>بالای ۵۰ استعلام:</strong> تخفیف ۴۰٪ (۱۲ هزار تومان)</li>
                            <li><strong>مشتریان ویژه:</strong> قرارداد سازمانی با شرایط خاص</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="روش پرداخت درگاه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه روش‌های پرداختی موجود است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        روش‌های پرداخت متنوع:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>درگاه اینترنتی:</strong> تمام کارت‌های عضو شتاب</li>
                            <li><strong>کیف پول:</strong> اعتبار پیش پرداخت</li>
                            <li><strong>حواله بانکی:</strong> به حساب مشخص</li>
                            <li><strong>پرداخت حضوری:</strong> دفاتر خدمات</li>
                            <li><strong>قرارداد سازمانی:</strong> پرداخت نسیه</li>
                            <li>تمام پرداخت‌ها <em>امن و رمزگذاری شده</em></li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="بازگشت پول کنسلی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر استعلام با مشکل مواجه شود، پول برمی‌گردد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، ضمانت بازگشت پول در موارد زیر:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>خطای سامانه:</strong> عدم ارائه گزارش</li>
                            <li><strong>اطلاعات نادرست:</strong> مشکل فنی</li>
                            <li><strong>عدم رضایت ضامن:</strong> قبل از انجام استعلام</li>
                            <li><strong>کد ملی نامعتبر:</strong> عدم وجود در سامانه</li>
                            <li><strong>مدت بازگشت:</strong> حداکثر ۴۸ ساعت</li>
                            <li><strong>روش بازگشت:</strong> همان روش پرداخت</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="اعتبار کیف پول شارژ">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه کیف پول خود را شارژ کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        روش‌های شارژ کیف پول:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>آنلاین:</strong> درگاه پرداخت اینترنتی</li>
                            <li><strong>کارت به کارت:</strong> انتقال به حساب مشخص</li>
                            <li><strong>حواله بانکی:</strong> از طریق عابر بانک</li>
                            <li><strong>کد دستوری:</strong> *720*کد#</li>
                            <li><strong>اپلیکیشن بانکی:</strong> پرداخت قبض</li>
                        </ol>
                        <p class="mt-3 text-sm text-green-600"><strong>مزیت:</strong> پرداخت سریع‌تر بدون وارد کردن مجدد اطلاعات</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="فاکتور مالیاتی رسید">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا فاکتور رسمی و مالیاتی صادر می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، تمام مدارک مالی رسمی ارائه می‌شود:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>فاکتور الکترونیکی:</strong> فوری پس از پرداخت</li>
                            <li><strong>فاکتور مالیاتی:</strong> با کد اقتصادی و ثبت</li>
                            <li><strong>رسید پرداخت:</strong> تأیید انجام تراکنش</li>
                            <li><strong>گواهی خدمات:</strong> برای امور حسابداری</li>
                            <li><strong>ارسال ایمیلی:</strong> خودکار به آدرس ثبت شده</li>
                            <li><strong>قابل کسر:</strong> از درآمد مشاغل</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 6: امنیت و حریم خصوصی (Security & Privacy) - 5 FAQs -->
        <div class="faq-category" data-category="security">
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    امنیت و حریم خصوصی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="security" data-keywords="امنیت اطلاعات محرمانه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">امنیت اطلاعات ضامن چگونه تضمین می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بالاترین سطح امنیت برای حفاظت از اطلاعات:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>رمزگذاری SSL 256 بیتی</strong> برای انتقال داده</li>
                            <li>ذخیره‌سازی رمزگذاری شده در سرورهای امن</li>
                            <li>دسترسی محدود کارشناسان مجاز</li>
                            <li>حذف خودکار اطلاعات پس از ۳۰ روز</li>
                            <li>لاگ کامل دسترسی‌ها</li>
                            <li><strong>تأیید امنیت از بانک مرکزی</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="حریم خصوصی قانون">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">قوانین حریم خصوصی چگونه رعایت می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>کامل ترین پایبندی به قوانین حریم خصوصی:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>اخذ رضایت صریح قبل از هر استعلام</li>
                            <li>عدم اشتراک اطلاعات با اشخاص ثالث</li>
                            <li><strong>حق حذف اطلاعات</strong> توسط ضامن</li>
                            <li>شفافیت کامل در نحوه استفاده از داده‌ها</li>
                            <li>امکان اعتراض و تصحیح اطلاعات</li>
                            <li>تبعیت از <em>قانون حمایت از حریم خصوصی</em></li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="سوءاستفاده جلوگیری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">از سوءاستفاده از استعلام چگونه جلوگیری می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        تدابیر پیشگیری از سوءاستفاده:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>احراز هویت دو مرحله‌ای</strong> (OTP + اطلاعات شخصی)</li>
                            <li>محدودیت تعداد استعلام روزانه</li>
                            <li>ردیابی و ثبت IP متقاضیان</li>
                            <li>اطلاع‌رسانی فوری به ضامن</li>
                            <li>سیستم هشدار برای فعالیت‌های مشکوک</li>
                            <li><strong>پیگرد قانونی</strong> برای استفاده غیرمجاز</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="اطلاعات حذف پاک کردن">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه اطلاعاتم را از سامانه حذف کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>حق حذف اطلاعات شخصی محفوظ است:</strong>
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>درخواست کتبی:</strong> ارسال به ایمیل پشتیبانی</li>
                            <li><strong>احراز هویت:</strong> تأیید مالکیت اطلاعات</li>
                            <li><strong>بررسی درخواست:</strong> حداکثر ۴۸ ساعت</li>
                            <li><strong>حذف اطلاعات:</strong> از تمام سرورها</li>
                            <li><strong>تأیید حذف:</strong> ارسال گواهی به متقاضی</li>
                        </ol>
                        <p class="mt-3 text-sm text-blue-600"><em>مدت زمان کل: حداکثر ۷۲ ساعت</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="گزارش نگهداری مدت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">گزارش‌های استعلام چقدر نگهداری می‌شوند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مدت نگهداری گزارش‌ها:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>در حساب کاربری:</strong> ۶ ماه قابل دسترسی</li>
                            <li><strong>در سرورهای امن:</strong> ۲ سال برای پشتیبانی</li>
                            <li><strong>بک‌آپ ایمن:</strong> ۵ سال طبق قانون</li>
                            <li><strong>حذف خودکار:</strong> پس از انقضای مدت</li>
                            <li><strong>دسترسی محدود:</strong> تنها کاربر و مقامات قضایی</li>
                            <li><em>امکان دانلود مجدد تا ۶ ماه</em></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 7: قوانین و مقررات (Laws & Regulations) - 6 FAQs -->
        <div class="faq-category" data-category="legal">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                    قوانین و مقررات
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="legal" data-keywords="قانون ضمانت بانکی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">قوانین ضمانت در نظام بانکی ایران چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        اصول قانونی ضمانت در نظام بانکی:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>قانون عملیات بانکی بدون ربا</strong> (۱۳۹۲)</li>
                            <li><strong>آیین‌نامه ضمانت‌ها</strong> بانک مرکزی (۱۳۹۸)</li>
                            <li>الزام <strong>احراز هویت و توانایی ضامن</strong></li>
                            <li>حداکثر سقف ضمانت برای هر فرد</li>
                            <li>ضرورت <strong>اطلاع‌رسانی به ضامن</strong></li>
                            <li>حقوق ضامن در برابر بانک و متقاضی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="استعلام اجباری الزام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا استعلام ضامن قانوناً الزامی است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، استعلام ضامن الزام قانونی است:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>بخشنامه شماره ۱۲۳۴/۱۳۹۹</strong> بانک مرکزی</li>
                            <li>الزام استعلام برای وام‌های بالای ۱۰۰ میلیون</li>
                            <li>بررسی <strong>امتیاز اعتباری حداقل ۶۰۰</strong></li>
                            <li>تأیید عدم بدهی معوق</li>
                            <li>احراز توانایی بازپرداخت</li>
                            <li>مسئولیت بانک در صورت عدم رعایت</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="حقوق ضامن قانونی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">حقوق قانونی ضامن چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>حقوق محفوظ ضامن طبق قانون:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>حق اطلاع:</strong> آگاهی از وضعیت بازپرداخت وام</li>
                            <li><strong>حق اعتراض:</strong> به میزان بدهی و محاسبات</li>
                            <li><strong>حق مراجعه:</strong> به متقاضی پس از پرداخت</li>
                            <li><strong>حق انصراف:</strong> در شرایط خاص قانونی</li>
                            <li><strong>حق دفاع:</strong> در مراجع قضایی</li>
                            <li><strong>حق حریم خصوصی:</strong> محرمانگی اطلاعات</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="تعهدات قانونی ضامن">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">تعهدات قانونی ضامن کدامند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        تعهدات اصلی ضامن:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>پرداخت بدهی:</strong> در صورت تخلف متقاضی</li>
                            <li><strong>اطلاع‌رسانی:</strong> تغییرات وضعیت مالی</li>
                            <li><strong>همکاری:</strong> در فرآیند وصول مطالبات</li>
                            <li><strong>عدم انتقال دارایی:</strong> بدون اطلاع بانک</li>
                            <li><strong>حضور:</strong> در جلسات اعتراض و صلح</li>
                            <li><strong>مسئولیت مدنی و کیفری</strong> در صورت تخلف</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="اعتراض قانونی مراجع">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مراجع قانونی اعتراض به گزارش استعلام کدامند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مراجع رسیدگی به اعتراضات:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>پشتیبانی پیشخوانک:</strong> اولین مرجع</li>
                            <li><strong>مرکز اطلاعات اعتباری:</strong> بررسی فنی</li>
                            <li><strong>بانک مرکزی:</strong> نظارت بر سامانه‌ها</li>
                            <li><strong>شورای عالی حمایت:</strong> حقوق مصرف‌کنندگان</li>
                            <li><strong>مراجع قضایی:</strong> در صورت عدم حل اختلاف</li>
                        </ol>
                        <p class="mt-3 text-sm text-amber-600"><strong>مهلت اعتراض:</strong> ۳۰ روز از تاریخ صدور گزارش</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="جرائم مالی ضمانت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">مجازات‌های قانونی برای تخلف در ضمانت چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مجازات‌های قانونی تخلف:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>ارائه مدارک جعلی:</strong> ۶ ماه تا ۲ سال حبس</li>
                            <li><strong>کلاهبرداری:</strong> ۲ تا ۱۰ سال حبس</li>
                            <li><strong>اخفای اطلاعات:</strong> جزای نقدی تا ۵۰۰ میلیون</li>
                            <li><strong>انتقال غیرقانونی اموال:</strong> مصادره دارایی</li>
                            <li><strong>تخلف از تعهدات:</strong> منع خدمات بانکی</li>
                            <li><strong>ثبت در فهرست سیاه</strong> سیستم بانکی</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 8: خدمات و پشتیبانی (Services & Support) - 5 FAQs -->
        <div class="faq-category" data-category="support">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    خدمات و پشتیبانی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="support" data-keywords="پشتیبانی تلفن ساعات">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">ساعات کاری پشتیبانی چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>پشتیبانی ۲۴ ساعته در روزهای کاری:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>تلفن:</strong> ۰۲۱-۷۷۳۳۴۴۵۵</li>
                            <li><strong>روزهای کاری:</strong> شنبه تا چهارشنبه ۸-۲۰</li>
                            <li><strong>پنج‌شنبه:</strong> ۸-۱۴</li>
                            <li><strong>چت آنلاین:</strong> ۲۴ ساعته</li>
                            <li><strong>ایمیل:</strong> support@pishkhanak.com</li>
                            <li><strong>اورژانس فنی:</strong> ۰۹۱۲-۳۴۵۶۷۸۹</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="support" data-keywords="راهنمایی مشاوره">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چه خدمات مشاورهای ارائه می‌دهید؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        خدمات مشاوره‌ای تخصصی:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>تفسیر گزارش:</strong> توضیح کامل نتایج استعلام</li>
                            <li><strong>مشاوره انتخاب ضامن:</strong> بهترین گزینه‌ها</li>
                            <li><strong>راهنمایی بهبود امتیاز:</strong> روش‌های افزایش Credit Score</li>
                            <li><strong>مشاوره ریسک:</strong> ارزیابی احتمال عدم بازپرداخت</li>
                            <li><strong>راهکارهای جایگزین:</strong> در صورت مشکل</li>
                            <li>مشاوره <strong>رایگان ۱۵ دقیقه</strong> اول</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="support" data-keywords="آموزش وبینار">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا آموزش‌هایی برای استفاده بهتر ارائه می‌دهید؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>برنامه‌های آموزشی متنوع:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>وبینارهای رایگان:</strong> هفتگی سه‌شنبه‌ها</li>
                            <li><strong>آموزش ویدیویی:</strong> کانال یوتیوب پیشخوانک</li>
                            <li><strong>راهنمای کاربری:</strong> PDF قابل دانلود</li>
                            <li><strong>وورک‌شاپ حضوری:</strong> ماهانه در تهران</li>
                            <li><strong>پادکست:</strong> موضوعات اعتباری هفتگی</li>
                            <li><strong>خبرنامه:</strong> نکات و ترفندهای مفید</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="support" data-keywords="API سازمانی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا API برای استفاده سازمانی موجود است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، API کامل برای سازمان‌ها:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>REST API:</strong> اتصال به سیستم‌های موجود</li>
                            <li><strong>Webhook:</strong> اطلاع‌رسانی خودکار</li>
                            <li><strong>مستندات کامل:</strong> راهنمای توسعه‌دهندگان</li>
                            <li><strong>محیط تست:</strong> آزمایش قبل از اجرا</li>
                            <li><strong>پشتیبانی فنی:</strong> تیم توسعه اختصاصی</li>
                            <li><strong>نرخ ویژه:</strong> برای مصرف بالا</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="support" data-keywords="رضایت‌مندی نظر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه می‌توانم نظر و پیشنهاد خود را ارائه دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        راه‌های ارسال نظر و پیشنهاد:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>فرم نظرسنجی:</strong> در انتهای هر استعلام</li>
                            <li><strong>ایمیل:</strong> feedback@pishkhanak.com</li>
                            <li><strong>شبکه‌های اجتماعی:</strong> @pishkhanak</li>
                            <li><strong>نظرات گوگل:</strong> Google My Business</li>
                            <li><strong>تماس مستقیم:</strong> با مدیریت</li>
                            <li><strong>جایزه بهترین پیشنهاد:</strong> ماهانه ۵۰۰ هزار تومان</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 9: رفع مشکل (Troubleshooting) - 5 FAQs -->
        <div class="faq-category" data-category="troubleshooting">
            <div class="bg-gradient-to-r from-rose-600 to-rose-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    رفع مشکل
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="troubleshooting" data-keywords="خطا مشکل فنی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر در حین استعلام خطا دادم چه کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        راه‌حل‌های رفع مشکلات فنی:
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>بروزرسانی صفحه:</strong> F5 یا Ctrl+R</li>
                            <li><strong>پاک کردن کش:</strong> Ctrl+Shift+Delete</li>
                            <li><strong>تغییر مرورگر:</strong> Chrome, Firefox, Edge</li>
                            <li><strong>بررسی اتصال اینترنت:</strong> سرعت و پایداری</li>
                            <li><strong>غیرفعال کردن AdBlock:</strong> موقتاً</li>
                            <li><strong>تماس با پشتیبانی:</strong> اگر مشکل ادامه داشت</li>
                        </ol>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="troubleshooting" data-keywords="OTP نمی‌رسد پیامک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کد OTP ارسال نمی‌شود، چه کار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>راه‌حل‌های عدم دریافت OTP:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>انتظار ۵ دقیقه:</strong> تأخیر شبکه ممکن است</li>
                            <li><strong>بررسی پیامک‌های هرزنامه:</strong> فولدر Spam</li>
                            <li><strong>خاموش/روشن کردن موبایل:</strong> رفرش شبکه</li>
                            <li><strong>تغییر اپراتور:</strong> در صورت امکان</li>
                            <li><strong>درخواست مجدد:</strong> بعد از ۳ دقیقه</li>
                            <li><strong>تماس با اپراتور:</strong> بررسی فیلتر پیامک</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="troubleshooting" data-keywords="گزارش دانلود نمی‌شود">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">نمی‌توانم گزارش را دانلود کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        راه‌حل مشکل دانلود گزارش:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>بررسی فضای دیسک:</strong> حداقل ۱۰ مگ آزاد</li>
                            <li><strong>غیرفعال کردن مسدودکننده پاپ‌آپ</strong></li>
                            <li><strong>تنظیمات دانلود:</strong> عدم مسدودی PDF</li>
                            <li><strong>تست با مرورگر دیگر</strong></li>
                            <li><strong>دانلود از پنل کاربری:</strong> ورود مجدد</li>
                            <li><strong>درخواست ارسال ایمیل:</strong> جایگزین دانلود</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="troubleshooting" data-keywords="پرداخت ناموفق عدم">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">پرداختم انجام شد اما استعلام نشد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>اقدامات لازم برای مشکل پرداخت:</strong>
                        <ol class="list-decimal mr-6 mt-3 space-y-2">
                            <li><strong>یادداشت کد پیگیری:</strong> رسید بانکی</li>
                            <li><strong>انتظار ۱۵ دقیقه:</strong> تأیید خودکار پرداخت</li>
                            <li><strong>تماس فوری با پشتیبانی:</strong> ۰۲۱-۷۷۳۳۴۴۵۵</li>
                            <li><strong>ارسال رسید بانکی:</strong> ایمیل یا واتس‌اپ</li>
                            <li><strong>تکمیل دستی استعلام:</strong> توسط اپراتور</li>
                            <li><strong>بازگشت پول:</strong> در صورت عدم حل مشکل</li>
                        </ol>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="troubleshooting" data-keywords="نتایج عجیب غیرعادی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">نتایج استعلام عجیب یا غیرمنتظره است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        علل و راه‌حل نتایج غیرعادی:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>تشابه نام:</strong> اشتباه هویتی با فرد دیگر</li>
                            <li><strong>خطای ورود کد ملی:</strong> بررسی مجدد اطلاعات</li>
                            <li><strong>اطلاعات قدیمی:</strong> عدم به‌روزرسانی سامانه</li>
                            <li><strong>مشکل فنی:</strong> خطا در دریافت از منبع</li>
                            <li><strong>درخواست بررسی دوباره:</strong> استعلام جدید</li>
                            <li><strong>مراجعه به بانک مرکزی:</strong> تأیید صحت</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 10: نکات پیشرفته (Advanced Tips) - 4 FAQs -->
        <div class="faq-category" data-category="advanced">
            <div class="bg-gradient-to-r from-violet-600 to-violet-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    نکات پیشرفته
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="advanced" data-keywords="امتیاز اعتباری بهبود افزایش">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه امتیاز اعتباری ضامن را بهبود دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>استراتژی‌های بهبود امتیاز اعتباری:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>پرداخت به‌موقع:</strong> تمام تعهدات در سررسید</li>
                            <li><strong>تنوع تسهیلات:</strong> ترکیب وام، کارت اعتباری، چک</li>
                            <li><strong>مدیریت بدهی:</strong> نگه‌داری زیر ۳۰٪ درآمد</li>
                            <li><strong>سابقه بانکی طولانی:</strong> حفظ حساب‌های قدیمی</li>
                            <li><strong>تصحیح اطلاعات نادرست:</strong> اعتراض به خطاها</li>
                            <li><strong>افزایش درآمد:</strong> مستندسازی منابع مالی</li>
                        </ul>
                        <p class="mt-3 text-sm text-green-600"><em>بهبود امتیاز: ۶-۱۲ ماه زمان نیاز</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="advanced" data-keywords="ریسک ارزیابی پیش‌بینی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه ریسک ضامن را دقیق‌تر ارزیابی کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        تکنیک‌های پیشرفته ارزیابی ریسک:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>تحلیل روند:</strong> تغییرات امتیاز در ۱۲ ماه اخیر</li>
                            <li><strong>نسبت بدهی به درآمد:</strong> حداکثر ۴۰٪ قابل قبول</li>
                            <li><strong>تنوع منابع درآمد:</strong> وابستگی به یک منبع خطرناک</li>
                            <li><strong>بررسی صنعت شغل:</strong> ثبات بخش اقتصادی</li>
                            <li><strong>تحلیل فصلی:</strong> نوسانات درآمد</li>
                            <li><strong>شاخص‌های اقتصادی:</strong> تأثیر تورم و ركود</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="advanced" data-keywords="چندین ضامن ترکیب">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">استراتژی انتخاب چندین ضامن چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>اصول انتخاب ضامنین متعدد:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>تکمیل ظرفیت:</strong> ضامن اصلی + تکمیلی</li>
                            <li><strong>توزیع ریسک:</strong> بخش‌های مختلف اقتصادی</li>
                            <li><strong>ترکیب سنی:</strong> جوان + میان‌سال</li>
                            <li><strong>تنوع جغرافیایی:</strong> شهرهای مختلف</li>
                            <li><strong>ترکیب درآمد:</strong> ثابت + متغیر</li>
                            <li><strong>هماهنگی قانونی:</strong> تعریف مسئولیت هر ضامن</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="advanced" data-keywords="آینده نگری پیش‌بینی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه وضعیت آینده ضامن را پیش‌بینی کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        تکنیک‌های پیش‌بینی وضعیت آینده:
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>تحلیل سیکل زندگی:</strong> سن و مرحله شغلی</li>
                            <li><strong>بررسی تعهدات آینده:</strong> وام‌های در جریان</li>
                            <li><strong>پیش‌بینی درآمد:</strong> روند شغلی و ارتقا</li>
                            <li><strong>تحلیل بازار کار:</strong> آینده صنعت شغل</li>
                            <li><strong>بررسی خانوادگی:</strong> تغییرات احتمالی</li>
                            <li><strong>شاخص‌های اقتصاد کلان:</strong> تأثیر بر درآمد</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 11: گزارش‌ها و نتایج (Reports & Results) - 3 FAQs -->
        <div class="faq-category" data-category="reports">
            <div class="bg-gradient-to-r from-amber-600 to-amber-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    گزارش‌ها و نتایج
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="reports" data-keywords="گزارش محتویات تفسیر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">گزارش استعلام شامل چه بخش‌هایی است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>ساختار کامل گزارش استعلام ضامن:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>خلاصه اجرایی:</strong> امتیاز کلی و توصیه</li>
                            <li><strong>اطلاعات هویتی:</strong> تأیید صحت مشخصات</li>
                            <li><strong>تاریخچه اعتباری:</strong> ۵ سال گذشته</li>
                            <li><strong>تعهدات جاری:</strong> وام‌ها و بدهی‌های فعال</li>
                            <li><strong>سوابق چک:</strong> برگشتی و اعتراضات</li>
                            <li><strong>تحلیل ریسک:</strong> احتمال عدم بازپرداخت</li>
                            <li><strong>توصیه‌های عملی:</strong> تصمیم‌گیری نهایی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="reports" data-keywords="امتیاز محدوده تفسیر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">محدوده امتیازات و معنی آن‌ها چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>جدول تفسیر امتیازات اعتباری:</strong>
                        <div class="mt-3 space-y-2">
                            <div class="flex justify-between bg-red-50 p-2 rounded">
                                <span><strong>۳۰۰-۴۹۹:</strong> ریسک بالا</span>
                                <span class="text-red-600">🔴 غیرقابل قبول</span>
                            </div>
                            <div class="flex justify-between bg-orange-50 p-2 rounded">
                                <span><strong>۵۰۰-۵۹۹:</strong> ریسک متوسط</span>
                                <span class="text-orange-600">🟡 نیاز ضمانت اضافی</span>
                            </div>
                            <div class="flex justify-between bg-yellow-50 p-2 rounded">
                                <span><strong>۶۰۰-۶۹۹:</strong> ریسک پایین</span>
                                <span class="text-yellow-600">🟡 قابل قبول</span>
                            </div>
                            <div class="flex justify-between bg-green-50 p-2 rounded">
                                <span><strong>۷۰۰-۸۵۰:</strong> ریسک خیلی پایین</span>
                                <span class="text-green-600">🟢 عالی</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="reports" data-keywords="مقایسه تحلیل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه نتایج چند ضامن را مقایسه کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>معیارهای مقایسه ضامنین:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-2">
                            <li><strong>امتیاز اعتباری:</strong> بالاتر بهتر</li>
                            <li><strong>نسبت بدهی به درآمد:</strong> پایین‌تر بهتر</li>
                            <li><strong>پایداری درآمد:</strong> ثبات شغلی</li>
                            <li><strong>سوابق منفی:</strong> تعداد کمتر مطلوب</li>
                            <li><strong>ظرفیت ضمانت:</strong> توان تحمل ریسک</li>
                            <li><strong>گزارش ترکیبی:</strong> جدول مقایسه خودکار</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 12: ارتباط با بانک‌ها (Bank Communication) - 3 FAQs -->
        <div class="faq-category" data-category="banks">
            <div class="bg-gradient-to-r from-cyan-600 to-cyan-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    ارتباط با بانک‌ها
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="banks" data-keywords="بانک ارائه گزارش">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه گزارش را به بانک ارائه دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>روش‌های ارائه گزارش به بانک:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>فایل PDF:</strong> دانلود و چاپ رسمی</li>
                            <li><strong>کد رهگیری:</strong> ارائه شماره استعلام</li>
                            <li><strong>لینک مستقیم:</strong> دسترسی آنلاین بانک</li>
                            <li><strong>ارسال ایمیل:</strong> مستقیم به کارشناس وام</li>
                            <li><strong>تأیید اصالت:</strong> کد QR روی گزارش</li>
                            <li>گزارش <strong>مهر و امضای رسمی</strong> دارد</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="banks" data-keywords="بانک تأیید پذیرش">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">همه بانک‌ها گزارش پیشخوانک را می‌پذیرند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، گزارش در تمام بانک‌ها قابل پذیرش است:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>بانک‌های دولتی:</strong> ملی، صادرات، کشاورزی</li>
                            <li><strong>بانک‌های خصوصی:</strong> پاسارگاد، تجارت، صنعت ایران</li>
                            <li><strong>مؤسسات اعتباری:</strong> کوثر، ملل، نور</li>
                            <li><strong>صندوق‌های قرض‌الحسنه:</strong> رسالت، مهر ایران</li>
                            <li><strong>تأیید بانک مرکزی:</strong> مجوز رسمی دریافت شده</li>
                            <li><strong>بروزرسانی مداوم:</strong> انطباق با استانداردهای جدید</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="banks" data-keywords="بانک سوالات کارشناس">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر کارشناس بانک سوال داشته باشد چه کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>پشتیبانی کامل در ارتباط با بانک:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>هاتلاین بانک‌ها:</strong> ۰۲۱-۷۷۳۳۴۴۵۵ داخلی ۲</li>
                            <li><strong>توضیح فنی:</strong> تفسیر اصطلاحات تخصصی</li>
                            <li><strong>مدارک تکمیلی:</strong> ارائه جزئیات بیشتر</li>
                            <li><strong>تماس مستقیم:</strong> با کارشناس بانک</li>
                            <li><strong>گزارش تکمیلی:</strong> در صورت نیاز</li>
                            <li><strong>ضمانت پاسخ‌گویی:</strong> ۲۴ ساعت</li>
                        </ul>
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

    <!-- Contact Support Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 mt-12 text-center">
        <div class="max-w-2xl mx-auto">
            <h3 class="text-2xl font-bold text-white mb-4">همچنان سوالی دارید؟</h3>
            <p class="text-blue-100 mb-6 text-lg">
                تیم متخصص پیشخوانک ۲۴ ساعته پاسخگوی سوالات شما در زمینه استعلام ضامن وام است
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="tel:02177334455" class="bg-white text-blue-600 px-6 py-3 rounded-xl font-semibold hover:bg-blue-50 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    تماس: ۰۲۱-۷۷۳۳۴۴۵۵
                </a>
                <a href="mailto:support@pishkhanak.com" class="bg-purple-700 text-white px-6 py-3 rounded-xl font-semibold hover:bg-purple-800 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    support@pishkhanak.com
                </a>
            </div>
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