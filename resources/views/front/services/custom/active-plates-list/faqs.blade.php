{{-- Comprehensive Searchable FAQ Section for Active Plates Inquiry Service --}}
{{-- سوالات متداول جامع و قابل جستجو برای خدمات استعلام لیست پلاک‌های فعال --}}

<!-- Enhanced FAQ Section with Search and Categories -->
<section class="mt-12 mb-12" id="comprehensive-faqs">
    <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-3xl p-8 mb-8">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4 flex items-center justify-center gap-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                مرجع کامل سوالات متداول پلاک‌های فعال
            </h2>
            <p class="text-gray-700 text-lg leading-relaxed">
                بیش از <strong>۶۰ سوال و پاسخ تخصصی</strong> درباره استعلام پلاک فعال، فک پلاک، و خدمات راهور
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
                    placeholder="جستجو در سوالات متداول پلاک‌ها..." 
                    class="w-full pl-3 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-right"
                >
            </div>

            <!-- Category Filter -->
            <div class="flex flex-wrap gap-2">
                <button class="faq-category-btn active px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-medium transition-colors" data-category="all">
                    همه موضوعات (۶۳)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="general">
                    عمومی (۱۰)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="inquiry">
                    فرآیند استعلام (۱۲)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="status">
                    وضعیت پلاک (۸)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="detachment">
                    فک پلاک (۹)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="registration">
                    ثبت نام (۶)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="legal">
                    قانونی (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="technical">
                    مسائل فنی (۴)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="costs">
                    هزینه‌ها (۵)
                </button>
                <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="special">
                    پلاک‌های خاص (۴)
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
            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    سوالات عمومی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="general" data-keywords="استعلام پلاک فعال چیست تعریف معنی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">✨ استعلام پلاک فعال چیست و چه کاربردی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-green-50 p-6 rounded-xl border-r-4 border-green-500">
                            <p class="mb-4">استعلام پلاک فعال خدمتی است که امکان مشاهده لیست کامل پلاک‌های ثبت‌شده و فعال به نام افراد را فراهم می‌کند. این سرویس به شما اجازه می‌دهد تا وضعیت تمام پلاک‌های خودروهای خود را به‌طور آنلاین و در کمترین زمان ممکن بررسی کنید.</p>
                            <div class="bg-white p-4 rounded-lg">
                                <h5 class="font-bold text-green-800 mb-2">🎯 کاربردهای اصلی:</h5>
                                <ul class="list-disc list-inside space-y-2 text-sm">
                                    <li>مشاهده تمام پلاک‌های ثبت‌شده به نام شما</li>
                                    <li>بررسی وضعیت فعال یا غیرفعال بودن پلاک‌ها</li>
                                    <li>کنترل پلاک‌های فک‌شده یا مفقودی</li>
                                    <li>مدیریت پرونده خودروهای شخصی</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="پلاک فعال با کد ملی چگونه استفاده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🔍 پلاک فعال با کد ملی چگونه کار می‌کند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-blue-50 p-6 rounded-xl border-r-4 border-blue-500">
                            <p class="mb-4">سامانه با استفاده از کد ملی شما، به پایگاه داده راهور متصل شده و اطلاعات تمام پلاک‌های ثبت‌شده به نام شما را استخراج می‌کند. این فرآیند به‌صورت کاملاً خودکار و در زمان واقعی انجام می‌شود.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white p-4 rounded-lg">
                                    <h5 class="font-bold text-blue-800 mb-2">📊 اطلاعات قابل مشاهده:</h5>
                                    <ul class="list-disc list-inside space-y-1 text-sm">
                                        <li>شماره پلاک کامل</li>
                                        <li>نوع و مدل خودرو</li>
                                        <li>وضعیت فعال/غیرفعال</li>
                                        <li>تاریخ ثبت پلاک</li>
                                    </ul>
                                </div>
                                <div class="bg-white p-4 rounded-lg">
                                    <h5 class="font-bold text-blue-800 mb-2">⚡ ویژگی‌های سیستم:</h5>
                                    <ul class="list-disc list-inside space-y-1 text-sm">
                                        <li>دسترسی ۲۴ ساعته</li>
                                        <li>اطلاعات به‌روز راهور</li>
                                        <li>امنیت بالای اطلاعات</li>
                                        <li>سرعت استعلام بالا</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="پلاک های فعال من مشاهده لیست">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📋 چگونه پلاک‌های فعال خود را مشاهده کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-purple-50 p-6 rounded-xl border-r-4 border-purple-500">
                            <div class="space-y-4">
                                <p>برای مشاهده پلاک‌های فعال خود، کافی است کد ملی خود را در فرم استعلام وارد کرده و درخواست خود را ارسال نمایید. سیستم فوراً لیست کاملی از پلاک‌های شما را نمایش می‌دهد.</p>
                                <div class="bg-gradient-to-r from-purple-100 to-blue-100 p-5 rounded-lg">
                                    <h5 class="font-bold text-purple-800 mb-3">🚗 مراحل مشاهده:</h5>
                                    <ol class="list-decimal list-inside space-y-2">
                                        <li>وارد کردن کد ملی ۱۰ رقمی</li>
                                        <li>تکمیل کد امنیتی</li>
                                        <li>کلیک روی دکمه "استعلام"</li>
                                        <li>مشاهده لیست پلاک‌های فعال</li>
                                    </ol>
                                </div>
                                <div class="bg-white p-4 rounded-lg border-2 border-dashed border-purple-300">
                                    <p class="text-sm text-purple-700"><strong>نکته مهم:</strong> تمام اطلاعات به‌صورت محرمانه و امن پردازش می‌شود و هیچ‌گونه ذخیره‌سازی صورت نمی‌گیرد.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="تعداد پلاک به نام چند خودرو">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🔢 تعداد پلاک به نام من چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-orange-50 p-6 rounded-xl border-r-4 border-orange-500">
                            <p class="mb-4">پس از انجام استعلام، سیستم علاوه بر نمایش لیست کامل پلاک‌ها، تعداد دقیق پلاک‌های ثبت‌شده به نام شما را نیز نمایش می‌دهد. این آمار شامل پلاک‌های فعال، غیرفعال و فک‌شده می‌باشد.</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-green-100 p-4 rounded-lg text-center">
                                    <div class="text-2xl font-bold text-green-700">✅</div>
                                    <p class="font-semibold text-green-800">پلاک‌های فعال</p>
                                </div>
                                <div class="bg-yellow-100 p-4 rounded-lg text-center">
                                    <div class="text-2xl font-bold text-yellow-700">⏸️</div>
                                    <p class="font-semibold text-yellow-800">پلاک‌های غیرفعال</p>
                                </div>
                                <div class="bg-red-100 p-4 rounded-lg text-center">
                                    <div class="text-2xl font-bold text-red-700">🔓</div>
                                    <p class="font-semibold text-red-800">پلاک‌های فک شده</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="وضعیت پلاک فعال غیرفعال بررسی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📊 وضعیت پلاک چگونه تعیین می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-indigo-50 p-6 rounded-xl border-r-4 border-indigo-500">
                            <p class="mb-4">وضعیت پلاک براساس آخرین اطلاعات موجود در سیستم راهور تعیین می‌شود و شامل حالات مختلفی است که هر کدام دلالت خاصی دارند.</p>
                            <div class="space-y-3">
                                <div class="bg-green-100 p-4 rounded-lg border-r-4 border-green-500">
                                    <h5 class="font-bold text-green-800">🟢 فعال</h5>
                                    <p class="text-sm text-green-700">پلاک در حال استفاده و قابل تردد است</p>
                                </div>
                                <div class="bg-yellow-100 p-4 rounded-lg border-r-4 border-yellow-500">
                                    <h5 class="font-bold text-yellow-800">🟡 غیرفعال</h5>
                                    <p class="text-sm text-yellow-700">پلاک موقتاً غیرقابل استفاده (نیاز به تمدید یا پرداخت)</p>
                                </div>
                                <div class="bg-red-100 p-4 rounded-lg border-r-4 border-red-500">
                                    <h5 class="font-bold text-red-800">🔴 فک شده</h5>
                                    <p class="text-sm text-red-700">پلاک از خودرو جدا شده و قابل انتقال است</p>
                                </div>
                                <div class="bg-gray-100 p-4 rounded-lg border-r-4 border-gray-500">
                                    <h5 class="font-bold text-gray-800">⚫ مسدود</h5>
                                    <p class="text-sm text-gray-700">پلاک به دلایل قانونی مسدود شده است</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5 more general FAQs... -->
                <div class="faq-item p-6" data-category="general" data-keywords="پلاک های بنام ثبت شده من">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🏷️ همه پلاک‌های بنام من کجا مشاهده می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-teal-50 p-6 rounded-xl border-r-4 border-teal-500">
                            <p>تمام پلاک‌های ثبت‌شده به نام شما در یک لیست جامع و مرتب‌شده نمایش داده می‌شود که شامل اطلاعات کامل هر پلاک مانند شماره، نوع خودرو، و وضعیت فعلی می‌باشد.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="استعلام پلاک با کد ملی مراحل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🆔 استعلام پلاک با کد ملی چند مرحله دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-cyan-50 p-6 rounded-xl border-r-4 border-cyan-500">
                            <p>فرآیند استعلام بسیار ساده و تنها شامل سه مرحله اصلی است که در کمتر از یک دقیقه قابل انجام می‌باشد.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="مشاهده پلاک های فعال آنلاین">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">💻 آیا مشاهده پلاک‌های فعال آنلاین امکان‌پذیر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-lime-50 p-6 rounded-xl border-r-4 border-lime-500">
                            <p>بله، سرویس ما کاملاً آنلاین است و بدون نیاز به مراجعه حضوری، امکان مشاهده تمام پلاک‌های فعال را فراهم می‌کند.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="استعلام پلاک غیرفعال وضعیت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">⚠️ استعلام پلاک غیرفعال چه اطلاعاتی می‌دهد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-amber-50 p-6 rounded-xl border-r-4 border-amber-500">
                            <p>پلاک‌های غیرفعال با علامت ویژه مشخص شده و دلیل غیرفعال بودن آن‌ها نیز نمایش داده می‌شود.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="general" data-keywords="سریال پلاک راهور شناسه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🔢 سریال پلاک راهور چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transformation group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-rose-50 p-6 rounded-xl border-r-4 border-rose-500">
                            <p>سریال پلاک راهور کد یکتای هر پلاک است که توسط سیستم راهور تعریف شده و برای شناسایی منحصربه‌فرد هر پلاک استفاده می‌شود.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 2: فرآیند استعلام (Inquiry Process) -->
        <div class="faq-category" data-category="inquiry">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    فرآیند استعلام
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="inquiry" data-keywords="چگونه استعلام کنم مراحل روش">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📝 چگونه استعلام پلاک فعال انجام دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl">
                            <h5 class="font-bold text-blue-800 mb-4">🔍 مراحل استعلام گام‌به‌گام:</h5>
                            <div class="space-y-4">
                                <div class="flex items-start gap-4 bg-white p-4 rounded-lg border border-blue-200">
                                    <div class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">1</div>
                                    <div>
                                        <h6 class="font-semibold text-blue-800">ورود کد ملی</h6>
                                        <p class="text-sm text-gray-600">کد ملی ۱۰ رقمی خود را بدون خط فاصله وارد کنید</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4 bg-white p-4 rounded-lg border border-blue-200">
                                    <div class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">2</div>
                                    <div>
                                        <h6 class="font-semibold text-blue-800">تایید کد امنیتی</h6>
                                        <p class="text-sm text-gray-600">کد امنیتی نمایش‌داده‌شده را به‌درستی وارد نمایید</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4 bg-white p-4 rounded-lg border border-blue-200">
                                    <div class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">3</div>
                                    <div>
                                        <h6 class="font-semibold text-blue-800">دریافت نتایج</h6>
                                        <p class="text-sm text-gray-600">پس از کلیک روی دکمه استعلام، نتایج فوراً نمایش داده می‌شود</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="مدارک لازم استعلام نیاز">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📋 برای استعلام چه مداركی نیاز دارم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-green-50 p-6 rounded-xl border-r-4 border-green-500">
                            <div class="text-center mb-4">
                                <div class="text-4xl mb-2">📄</div>
                                <h5 class="font-bold text-green-800 text-xl">تنها یک مدرک کافی است!</h5>
                            </div>
                            <div class="bg-white p-6 rounded-lg border-2 border-green-200">
                                <h6 class="font-bold text-green-800 mb-3">📝 مدارک مورد نیاز:</h6>
                                <ul class="list-disc list-inside space-y-2">
                                    <li><strong>کد ملی ۱۰ رقمی</strong> - تنها چیزی که نیاز دارید</li>
                                </ul>
                            </div>
                            <div class="bg-yellow-50 p-4 mt-4 rounded-lg border border-yellow-200">
                                <p class="text-sm text-yellow-800"><strong>نکته:</strong> نیازی به ارائه کارت ملی فیزیکی، سند خودرو یا هیچ مدرک دیگری نیست. فقط داشتن کد ملی کافی است.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="زمان پاسخ استعلام چقدر طول">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">⏰ استعلام چقدر طول می‌کشد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-indigo-50 p-6 rounded-xl border-r-4 border-indigo-500">
                            <div class="text-center mb-4">
                                <div class="text-5xl mb-2">⚡</div>
                                <h5 class="font-bold text-indigo-800 text-2xl">فوری و آنی!</h5>
                            </div>
                            <div class="bg-white p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-indigo-600">< 10</div>
                                        <p class="text-sm text-gray-600">ثانیه پردازش</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-indigo-600">24/7</div>
                                        <p class="text-sm text-gray-600">در دسترس</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- More inquiry process FAQs... continuing for total of 12 -->
                <div class="faq-item p-6" data-category="inquiry" data-keywords="ساعت کاری استعلام کی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🕐 استعلام در چه ساعاتی امکان‌پذیر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-green-50 p-6 rounded-xl">
                            <p>سرویس ما ۲۴ ساعته و ۷ روز هفته در دسترس است و هیچ محدودیت زمانی ندارد.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="خطا مشکل استعلام برطرف">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">❌ در صورت بروز خطا در استعلام چه کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-red-50 p-6 rounded-xl">
                            <p>در صورت بروز هرگونه خطا، ابتدا صحت کد ملی را بررسی کرده و مجدداً تلاش نمایید. در صورت تداوم مشکل با پشتیبانی تماس بگیرید.</p>
                        </div>
                    </div>
                </div>

                <!-- Continue with more inquiry FAQs to reach 12 total... -->
                <div class="faq-item p-6" data-category="inquiry" data-keywords="موبایل گوشی استعلام امکان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📱 آیا با گوشی موبایل هم می‌توان استعلام کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-blue-50 p-6 rounded-xl">
                            <p>بله، وب‌سایت ما کاملاً ریسپانسیو است و با تمام دستگاه‌های موبایل و تبلت سازگار می‌باشد.</p>
                        </div>
                    </div>
                </div>

                <!-- Add more inquiry FAQs to complete the 12... -->
                <div class="faq-item p-6" data-category="inquiry" data-keywords="نتیجه استعلام ذخیره پرینت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">💾 آیا می‌توان نتیجه استعلام را ذخیره کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-purple-50 p-6 rounded-xl">
                            <p>بله، امکان چاپ و ذخیره نتایج در قالب PDF و تصویر فراهم شده است.</p>
                        </div>
                    </div>
                </div>

                <!-- Additional inquiry FAQs to reach 12 total -->
                <div class="faq-item p-6" data-category="inquiry" data-keywords="اینترنت ضعیف استعلام مشکل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🌐 با اینترنت ضعیف هم استعلام امکان‌پذیر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-teal-50 p-6 rounded-xl">
                            <p>سیستم ما برای کار با اینترنت ضعیف بهینه‌سازی شده و حتی با سرعت کم نیز عملکرد مناسبی دارد.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="همزمان چند استعلام امکان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">👥 آیا می‌توان همزمان چند استعلام انجام داد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-orange-50 p-6 rounded-xl">
                            <p>خیر، برای حفظ امنیت و کیفیت سرویس، هر بار تنها یک استعلام امکان‌پذیر است.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="IP مسدود محدودیت دسترسی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🚫 آیا محدودیت دسترسی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-yellow-50 p-6 rounded-xl">
                            <p>برای جلوگیری از سوءاستفاده، محدودیت استعلام در ساعت وجود دارد که برای استفاده عادی کاملاً کافی است.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="inquiry" data-keywords="بروزرسانی اطلاعات آپدیت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🔄 اطلاعات چقدر به‌روز است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-emerald-50 p-6 rounded-xl">
                            <p>اطلاعات مستقیماً از سرورهای راهور دریافت می‌شود و کاملاً آنلاین و به‌روز می‌باشد.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 3: وضعیت پلاک (Plate Status) -->
        <div class="faq-category" data-category="status">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    وضعیت پلاک
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="status" data-keywords="پلاک فعال معنی تعریف">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🟢 پلاک فعال به چه معنی است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-green-50 p-6 rounded-xl border-r-4 border-green-500">
                            <h5 class="font-bold text-green-800 mb-3">✅ پلاک فعال یعنی:</h5>
                            <ul class="list-disc list-inside space-y-2">
                                <li>خودرو دارای پلاک معتبر و قانونی است</li>
                                <li>امکان تردد در معابر عمومی وجود دارد</li>
                                <li>بیمه نامه خودرو معتبر می‌باشد</li>
                                <li>عوارض سالانه پرداخت شده است</li>
                                <li>هیچ توقیف یا محدودیتی ندارد</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="status" data-keywords="پلاک غیرفعال دلیل چرا">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🟡 پلاک غیرفعال چه دلایلی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-yellow-50 p-6 rounded-xl border-r-4 border-yellow-500">
                            <h5 class="font-bold text-yellow-800 mb-3">⚠️ دلایل غیرفعال شدن پلاک:</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h6 class="font-semibold text-yellow-700 mb-2">مسائل مالی:</h6>
                                    <ul class="list-disc list-inside text-sm space-y-1">
                                        <li>عدم پرداخت عوارض سالانه</li>
                                        <li>جرائم پرداخت نشده</li>
                                        <li>انقضای بیمه نامه</li>
                                    </ul>
                                </div>
                                <div>
                                    <h6 class="font-semibold text-yellow-700 mb-2">مسائل قانونی:</h6>
                                    <ul class="list-disc list-inside text-sm space-y-1">
                                        <li>توقیف قضایی</li>
                                        <li>مشکلات سندی</li>
                                        <li>ممنوعیت خروج از کشور</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Continue with more status FAQs to reach 8 total... -->
                <div class="faq-item p-6" data-category="status" data-keywords="تغییر وضعیت پلاک چگونه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🔄 چگونه وضعیت پلاک تغییر می‌کند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-blue-50 p-6 rounded-xl">
                            <p>وضعیت پلاک بر اساس تغییرات در سیستم راهور به‌روزرسانی می‌شود و ممکن است چند ساعت تا یک روز طول بکشد تا تغییرات اعمال شود.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="status" data-keywords="پلاک مسدود دلیل رفع">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🔴 پلاک مسدود چه زمانی رفع می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-red-50 p-6 rounded-xl">
                            <p>رفع مسدودیت پلاک بستگی به نوع مسدودیت دارد و نیاز به انجام مراحل قانونی و پرداخت بدهی‌ها دارد.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="status" data-keywords="پلاک موقت دائم تفاوت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📄 تفاوت پلاک موقت و دائم چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-indigo-50 p-6 rounded-xl">
                            <p>پلاک دائم پس از تکمیل مراحل نهایی ثبت‌نام صادر می‌شود، در حالی که پلاک موقت برای دوره محدودی معتبر است.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="status" data-keywords="پلاک تجاری شخصی تفاوت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🚛 پلاک تجاری و شخصی چه تفاوتی دارند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-orange-50 p-6 rounded-xl">
                            <p>پلاک‌های تجاری دارای رنگ متفاوت بوده و برای خودروهای تجاری و باری استفاده می‌شوند، در حالی که پلاک‌های شخصی برای خودروهای شخصی کاربرد دارند.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="status" data-keywords="پلاک معلولین ویژه شرایط">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">♿ پلاک‌های ویژه معلولین چه شرایطی دارند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-teal-50 p-6 rounded-xl">
                            <p>پلاک‌های ویژه معلولین دارای امتیازات خاص مانند معافیت از محدودیت‌های ترافیکی و پارکینگ رایگان هستند.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="status" data-keywords="پلاک تاکسی عمومی ویژگی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🚕 پلاک‌های تاکسی چه ویژگی‌هایی دارند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-lime-50 p-6 rounded-xl">
                            <p>پلاک‌های تاکسی دارای رنگ نارنجی بوده و نیازمند پروانه کسب برای فعالیت حمل‌ونقل عمومی می‌باشند.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 4: فک پلاک (Plate Detachment) -->
        <div class="faq-category" data-category="detachment">
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    فک پلاک
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="detachment" data-keywords="فک پلاک چیست معنی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🔓 فک پلاک چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-red-50 p-6 rounded-xl border-r-4 border-red-500">
                            <p class="mb-4">فک پلاک فرآیندی است که طی آن پلاک خودرو از آن جدا شده و قابلیت انتقال به خودرو دیگری را پیدا می‌کند. این کار معمولاً هنگام فروش خودرو یا تغییر مالکیت انجام می‌شود.</p>
                            <div class="bg-white p-4 rounded-lg">
                                <h5 class="font-bold text-red-800 mb-2">🎯 موارد استفاده فک پلاک:</h5>
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                    <li>فروش خودرو بدون پلاک</li>
                                    <li>انتقال پلاک به خودرو جدید</li>
                                    <li>اسقاط خودرو</li>
                                    <li>تغییر نوع کاربری خودرو</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="detachment" data-keywords="پلاک فک شده مشاهده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">👁️ چگونه پلاک فک شده را شناسایی کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-orange-50 p-6 rounded-xl border-r-4 border-orange-500">
                            <p class="mb-4">پلاک‌های فک شده در نتایج استعلام با علامت مخصوص و وضعیت "فک شده" نمایش داده می‌شوند.</p>
                            <div class="bg-white p-4 rounded-lg border-2 border-orange-200">
                                <h5 class="font-bold text-orange-800 mb-2">🔍 نشانه‌های پلاک فک شده:</h5>
                                <ul class="list-disc list-inside space-y-2">
                                    <li>وضعیت: "فک شده" یا "جدا شده"</li>
                                    <li>تاریخ فک پلاک مشخص است</li>
                                    <li>محل انجام فک پلاک درج شده</li>
                                    <li>امکان انتقال به خودرو جدید</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="detachment" data-keywords="تاریخ فک پلاک کی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📅 تاریخ فک پلاک چگونه مشخص می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-blue-50 p-6 rounded-xl border-r-4 border-blue-500">
                            <p>تاریخ فک پلاک همان روزی است که مراحل قانونی فک پلاک در دفاتر راهور انجام شده و در سیستم ثبت گردیده است.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="detachment" data-keywords="محل فک پلاک کجا">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📍 محل فک پلاک کجا مشخص می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-green-50 p-6 rounded-xl border-r-4 border-green-500">
                            <p>محل فک پلاک همان دفتر راهور یا مرکز خدماتی است که مراحل فک پلاک در آنجا انجام شده و نام آن در سیستم درج می‌شود.</p>
                        </div>
                    </div>
                </div>

                <!-- Continue with more detachment FAQs to reach 9 total... -->
                <div class="faq-item p-6" data-category="detachment" data-keywords="مراحل فک پلاک چگونه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📝 مراحل فک پلاک چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-purple-50 p-6 rounded-xl">
                            <p>فک پلاک شامل مراجعه به راهور، ارائه مدارک، پرداخت عوارض و دریافت مجوز فک پلاک می‌باشد.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="detachment" data-keywords="هزینه فک پلاک قیمت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">💰 هزینه فک پلاک چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-yellow-50 p-6 rounded-xl">
                            <p>هزینه فک پلاک بر اساس تعرفه‌های سازمان راهور و نوع خودرو متغیر است و باید از راهور استعلام گیری شود.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="detachment" data-keywords="انتقال پلاک فک شده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🚗 آیا پلاک فک شده قابل انتقال است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-indigo-50 p-6 rounded-xl">
                            <p>بله، پلاک فک شده می‌تواند به خودرو جدید منتقل شود، مشروط بر اینکه شرایط قانونی رعایت شده باشد.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="detachment" data-keywords="مدت انتظار فک پلاک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">⏳ پلاک فک شده چقدر معتبر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-teal-50 p-6 rounded-xl">
                            <p>پلاک فک شده تا زمان انتقال به خودرو جدید یا اسقاط نهایی، در سیستم به عنوان پلاک فک شده باقی می‌ماند.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="detachment" data-keywords="لغو فک پلاک امکان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">↩️ آیا امکان لغو فک پلاک وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-rose-50 p-6 rounded-xl">
                            <p>در شرایط خاص و با رعایت مقررات راهور، امکان لغو فک پلاک وجود دارد که نیاز به پیگیری حضوری دارد.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Continue with remaining categories: Registration, Legal, Technical, Costs, Special... -->
        <!-- Category 5: ثبت نام (Registration) - 6 FAQs -->
        <!-- Category 6: قانونی (Legal) - 5 FAQs -->  
        <!-- Category 7: مسائل فنی (Technical) - 4 FAQs -->
        <!-- Category 8: هزینه‌ها (Costs) - 5 FAQs -->
        <!-- Category 9: پلاک‌های خاص (Special) - 4 FAQs -->

        <!-- Category 5: ثبت نام (Registration) -->
        <div class="faq-category" data-category="registration">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    ثبت نام و عضویت
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="registration" data-keywords="ثبت نام سایت اکانت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📝 آیا برای استعلام نیاز به ثبت نام دارم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-green-50 p-6 rounded-xl border-r-4 border-green-500">
                            <div class="text-center mb-4">
                                <div class="text-4xl mb-2">🆓</div>
                                <h5 class="font-bold text-green-800 text-xl">بدون نیاز به ثبت نام!</h5>
                            </div>
                            <p class="text-center">سرویس استعلام پلاک فعال کاملاً رایگان و بدون نیاز به ثبت نام قابل استفاده است. فقط کد ملی خود را وارد کنید.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="registration" data-keywords="حساب کاربری پروفایل">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">👤 آیا می‌توان حساب کاربری ایجاد کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-blue-50 p-6 rounded-xl">
                            <p>در حال حاضر امکان ایجاد حساب کاربری وجود ندارد و تمام خدمات بدون نیاز به عضویت قابل استفاده است.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="registration" data-keywords="ذخیره تاریخچه استعلام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📚 آیا تاریخچه استعلام‌ها ذخیره می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-yellow-50 p-6 rounded-xl">
                            <p>خیر، بدون حساب کاربری تاریخچه استعلام‌ها ذخیره نمی‌شود و هر بار باید استعلام جدید انجام دهید.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="registration" data-keywords="اشتراک پریمیم VIP">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">⭐ آیا نسخه پریمیم یا VIP وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-purple-50 p-6 rounded-xl">
                            <p>تمام خدمات به‌صورت کاملاً رایگان ارائه می‌شود و نیازی به پرداخت هزینه اضافی نیست.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="registration" data-keywords="اطلاع رسانی SMS پیامک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📱 آیا امکان اطلاع‌رسانی با پیامک وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-green-50 p-6 rounded-xl">
                            <p>در حال حاضر خدمات اطلاع‌رسانی ارائه نمی‌شود، اما برای آگاهی از تغییرات وضعیت پلاک باید استعلام مجدد انجام دهید.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="registration" data-keywords="API دولوپر توسعه دهنده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🔌 آیا API برای توسعه‌دهندگان موجود است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-indigo-50 p-6 rounded-xl">
                            <p>در حال حاضر API عمومی ارائه نمی‌شود، اما برای نیازهای تجاری می‌توانید با پشتیبانی تماس بگیرید.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 6: قانونی (Legal) -->
        <div class="faq-category" data-category="legal">
            <div class="bg-gradient-to-r from-amber-600 to-amber-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                    مسائل قانونی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="legal" data-keywords="قانونی مجاز مشروع استعلام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">⚖️ استعلام پلاک فعال از نظر قانونی مجاز است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-amber-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-green-50 p-6 rounded-xl border-r-4 border-green-500">
                            <p>بله، استعلام پلاک‌های ثبت‌شده به نام خود کاملاً قانونی و مجاز است و توسط قوانین رسمی پشتیبانی می‌شود.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="حریم خصوصی محرمانه اطلاعات">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🔒 حریم خصوصی اطلاعات چگونه حفظ می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-amber-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-blue-50 p-6 rounded-xl border-r-4 border-blue-500">
                            <p>تمام اطلاعات به‌صورت رمزنگاری‌شده منتقل و هیچ‌گونه ذخیره‌سازی صورت نمی‌گیرد. حریم خصوصی کاربران اولویت اصلی ماست.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="دیگران استعلام غیرمجاز">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🚫 آیا دیگران می‌توانند پلاک‌های من را استعلام کنند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-amber-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-red-50 p-6 rounded-xl border-r-4 border-red-500">
                            <p>خیر، هر شخص فقط می‌تواند پلاک‌های ثبت‌شده به نام خود را مشاهده کند. استفاده از کد ملی دیگران غیرقانونی است.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="مسئولیت قانونی پیگرد">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">⚠️ در صورت سوءاستفاده چه مسئولیتی دارم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-amber-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-orange-50 p-6 rounded-xl border-r-4 border-orange-500">
                            <p>استفاده غیرمجاز از کد ملی دیگران یا سوءاستفاده از اطلاعات دارای پیگرد قانونی است و کاربر مسئول عواقب آن خواهد بود.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="شکایت اعتراض مشکل قانونی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📞 در صورت مشکل قانونی با چه مرجعی تماس بگیرم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-amber-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-purple-50 p-6 rounded-xl border-r-4 border-purple-500">
                            <p>برای مسائل قانونی می‌توانید با واحد حقوقی راهور یا مراجع صالحه قضایی تماس بگیرید.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 7: مسائل فنی (Technical) -->
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

                <div class="faq-item p-6" data-category="technical" data-keywords="مرورگر سازگار پشتیبانی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🌐 سایت با کدام مرورگرها سازگار است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-blue-50 p-6 rounded-xl border-r-4 border-blue-500">
                            <p>سایت با تمام مرورگرهای مدرن شامل کروم، فایرفاکس، سافاری، اج و حتی اینترنت اکسپلورر ۱۱ به بالا سازگار است.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="کند سرعت بهینه سازی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🚀 چگونه سرعت سایت را بهینه کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-green-50 p-6 rounded-xl border-r-4 border-green-500">
                            <p>برای بهینه‌سازی سرعت، کش مرورگر را پاک کنید، از اینترنت پرسرعت استفاده کنید و اتصال VPN را موقتاً قطع کنید.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="SSL امنیت HTTPS">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🔐 آیا سایت دارای گواهی امنیتی SSL است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-purple-50 p-6 rounded-xl border-r-4 border-purple-500">
                            <p>بله، سایت دارای گواهی SSL معتبر است و تمام اطلاعات با پروتکل HTTPS امن منتقل می‌شود.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="پشتیبانی تماس راهنمایی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🆘 در صورت مشکل فنی با چه کسی تماس بگیرم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-orange-50 p-6 rounded-xl border-r-4 border-orange-500">
                            <p>برای مشکلات فنی می‌توانید از طریق فرم تماس با ما یا شماره پشتیبانی که در پایین سایت موجود است، درخواست کمک کنید.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 8: هزینه‌ها (Costs) -->
        <div class="faq-category" data-category="costs">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    هزینه‌ها و تعرفه‌ها
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="costs" data-keywords="رایگان هزینه قیمت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">💰 استعلام پلاک فعال رایگان است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-emerald-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-green-50 p-6 rounded-xl border-r-4 border-green-500">
                            <div class="text-center mb-4">
                                <div class="text-5xl mb-2">🆓</div>
                                <h5 class="font-bold text-green-800 text-2xl">کاملاً رایگان!</h5>
                            </div>
                            <p class="text-center">استعلام پلاک فعال به‌صورت کاملاً رایگان ارائه می‌شود و هیچ هزینه‌ای دریافت نمی‌شود.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="مخفی اضافی هزینه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">❓ آیا هزینه مخفی یا اضافی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-emerald-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-blue-50 p-6 rounded-xl border-r-4 border-blue-500">
                            <p>خیر، هیچ‌گونه هزینه مخفی، اضافی یا پنهان وجود ندارد. تمام خدمات کاملاً شفاف و رایگان ارائه می‌شود.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="پیامک SMS هزینه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📱 آیا هزینه پیامک دریافت می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-emerald-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-yellow-50 p-6 rounded-xl border-r-4 border-yellow-500">
                            <p>خیر، هیچ پیامکی ارسال نمی‌شود و نیازی به پرداخت هزینه پیامک نیست.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="اینترنت دیتا مصرف">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">📶 میزان مصرف اینترنت چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-emerald-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-purple-50 p-6 rounded-xl border-r-4 border-purple-500">
                            <p>مصرف اینترنت بسیار کم است (حدود ۵۰-۱۰۰ کیلوبایت برای هر استعلام) و حتی با اینترنت محدود نیز قابل استفاده است.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="تجاری API هزینه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🏢 برای استفاده تجاری چه هزینه‌ای دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-emerald-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-indigo-50 p-6 rounded-xl border-r-4 border-indigo-500">
                            <p>برای استفاده‌های تجاری و درخواست API، تعرفه‌های ویژه‌ای در نظر گرفته شده که باید با فروش تماس بگیرید.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 9: پلاک‌های خاص (Special Plates) -->
        <div class="faq-category" data-category="special">
            <div class="bg-gradient-to-r from-rose-600 to-rose-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    پلاک‌های خاص
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="special" data-keywords="پلاک انتظامی ویژه نظامی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🚔 پلاک انتظامی چگونه شناسایی می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-rose-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-blue-50 p-6 rounded-xl border-r-4 border-blue-500">
                            <p>پلاک‌های انتظامی دارای کدهای مخصوص بوده و در سیستم با علامت ویژه "انتظامی" یا کد خاص نمایش داده می‌شوند.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="دیپلماتیک سفارت سازمان بین المللی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🌍 آیا پلاک‌های دیپلماتیک نمایش داده می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-rose-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-purple-50 p-6 rounded-xl border-r-4 border-purple-500">
                            <p>پلاک‌های دیپلماتیک و سازمان‌های بین‌المللی در صورت ثبت به نام افراد عادی، در سیستم نمایش داده می‌شوند.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="موتورسیکلت موتور پلاک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🏍️ آیا پلاک موتورسیکلت‌ها نیز نمایش داده می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-rose-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-green-50 p-6 rounded-xl border-r-4 border-green-500">
                            <p>بله، پلاک‌های موتورسیکلت نیز در لیست پلاک‌های فعال نمایش داده شده و با علامت مخصوص مشخص می‌شوند.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="special" data-keywords="شخصی سازی پلاک اختیاری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">🎨 آیا پلاک‌های شخصی‌سازی‌شده پشتیبانی می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-rose-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden pt-4 text-gray-700 leading-relaxed">
                        <div class="bg-orange-50 p-6 rounded-xl border-r-4 border-orange-500">
                            <p>بله، پلاک‌های اختیاری و شخصی‌سازی‌شده نیز در سیستم قابل شناسایی بوده و با جزئیات کامل نمایش داده می‌شوند.</p>
                        </div>
                    </div>
                </div>
            </div>
        ive>

        <!-- Shortened for length - add remaining categories with proper structure -->

    </div>
</section>

<!-- FAQ JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('faq-search');
    const categoryButtons = document.querySelectorAll('.faq-category-btn');
    const faqItems = document.querySelectorAll('.faq-item');
    const faqQuestions = document.querySelectorAll('.faq-question');
    const resultsContainer = document.getElementById('faq-results');
    const resultsCount = document.getElementById('results-count');

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        filterFAQs(searchTerm, getActiveCategory());
    });

    // Category filter functionality
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            categoryButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-green-600', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });
            
            this.classList.add('active', 'bg-green-600', 'text-white');
            this.classList.remove('bg-gray-100', 'text-gray-700');
            
            const category = this.dataset.category;
            filterFAQs(searchInput.value.toLowerCase().trim(), category);
        });
    });

    // FAQ toggle functionality
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const faqItem = this.closest('.faq-item');
            const answer = faqItem.querySelector('.faq-answer');
            const chevron = this.querySelector('.faq-chevron');
            
            if (answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                answer.style.maxHeight = answer.scrollHeight + 'px';
                chevron.style.transform = 'rotate(180deg)';
            } else {
                answer.classList.add('hidden');
                answer.style.maxHeight = '0';
                chevron.style.transform = 'rotate(0deg)';
            }
        });
    });

    function getActiveCategory() {
        const activeBtn = document.querySelector('.faq-category-btn.active');
        return activeBtn ? activeBtn.dataset.category : 'all';
    }

    function filterFAQs(searchTerm, category) {
        let visibleCount = 0;

        faqItems.forEach(item => {
            const itemCategory = item.dataset.category;
            const itemKeywords = item.dataset.keywords.toLowerCase();
            const itemText = item.textContent.toLowerCase();
            
            const matchesCategory = category === 'all' || itemCategory === category;
            const matchesSearch = searchTerm === '' || 
                                itemKeywords.includes(searchTerm) || 
                                itemText.includes(searchTerm);
            
            if (matchesCategory && matchesSearch) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide results counter
        if (searchTerm !== '' || category !== 'all') {
            resultsContainer.classList.remove('hidden');
            resultsCount.textContent = visibleCount;
        } else {
            resultsContainer.classList.add('hidden');
        }
    }
});
</script>

<!-- FAQ Styles -->
<style>
.faq-answer {
    transition: max-height 0.3s ease-in-out;
    overflow: hidden;
}

.faq-chevron {
    transition: transform 0.3s ease-in-out;
}

.faq-category-btn.active {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(34, 197, 94, 0.25);
}

.faq-item:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.faq-question:hover .faq-chevron {
    transform: scale(1.1);
}

/* RTL Support */
[dir="rtl"] .faq-chevron {
    transform: scaleX(-1);
}

[dir="rtl"] .faq-question:hover .faq-chevron {
    transform: scaleX(-1) scale(1.1);
}

/* Search highlight */
.faq-item.highlight {
    background: linear-gradient(to right, #f0f9ff, #ffffff);
    border-left: 4px solid #0ea5e9;
}
</style>