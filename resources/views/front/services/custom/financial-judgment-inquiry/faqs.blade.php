{{-- Comprehensive Searchable FAQ Section for Financial Credit & Judgment Inquiry Service --}}
{{-- سوالات متداول جامع و قابل جستجو برای خدمات استعلام اعتبار و محکومیت مالی --}}

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
                بیش از <strong>۶۷ سوال و پاسخ تخصصی</strong> درباره استعلام اعتبار مالی، محکومیت مالی، سامانه صیاد و خدمات پیشخوانک
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
                    placeholder="جستجوی پیشرفته در سوالات (مثال: اعتبار مالی، چک برگشتی، محکومیت)..." 
                    class="w-full pl-3 pr-10 py-4 text-lg border-2 border-purple-200 rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-right"
                >
                <div id="search-suggestions" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-lg border border-gray-200 z-10"></div>
            </div>
        </div>

        <!-- Advanced Category Filter Buttons -->
        <div class="flex flex-wrap gap-2 mt-4">
            <button class="faq-category-btn active px-4 py-2 rounded-lg bg-purple-600 text-white text-sm font-medium transition-colors" data-category="all">
                همه موضوعات (۶۷)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="financial-credit">
                اعتبار مالی (۱۲)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="judgment">
                محکومیت مالی (۹)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="sayad-check">
                سامانه صیاد (۸)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="credit-rating">
                رتبه اعتباری (۷)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="banking">
                تسهیلات بانکی (۶)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="check-fix">
                رفع سوء اثر (۶)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="central-bank">
                بانک مرکزی (۵)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="technical">
                مسائل فنی (۵)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="security">
                امنیت و حریم خصوصی (۴)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="costs">
                هزینه‌ها و پرداخت (۳)
            </button>
            <button class="faq-category-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium transition-colors hover:bg-gray-200" data-category="legal">
                مسائل حقوقی (۲)
            </button>
        </div>

        <!-- Advanced Search Results Counter -->
        <div id="faq-results" class="mt-4 text-sm text-gray-600 hidden">
            <span id="results-count">0</span> نتیجه یافت شد
        </div>
    </div>

    <!-- Advanced FAQ Categories Container -->
    <div id="faq-container" class="space-y-8">

        <!-- Category 1: اعتبار مالی (Financial Credit) - 12 FAQs -->
        <div class="faq-category" data-category="financial-credit">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    استعلام اعتبار مالی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">
                
                <div class="faq-item p-6" data-category="financial-credit" data-keywords="استعلام اعتبار مالی چیست تعریف">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">استعلام اعتبار مالی چیست و چه اطلاعاتی ارائه می‌دهد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>استعلام اعتبار مالی فرآیند بررسی وضعیت اعتباری و مالی اشخاص از طریق <strong>سامانه‌های رسمی بانک مرکزی</strong> است. این گزارش اطلاعات کاملی شامل:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>امتیاز اعتباری (۰ تا ۹۰۰)</li>
                            <li>رتبه‌بندی ریسک (A1 تا E3)</li>
                            <li>سوابق تسهیلات و وام‌ها</li>
                            <li>وضعیت چک‌های برگشتی</li>
                            <li>محکومیت‌های مالی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="financial-credit" data-keywords="کد ملی استعلام اعتبار">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم فقط با کد ملی اعتبار مالی شخص دیگری را استعلام کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، امکان استعلام اعتبار مالی صرفاً با کد ملی وجود ندارد.</strong> برای حفظ حریم خصوصی، علاوه بر کد ملی، <em>شماره تلفن همراه ثبت‌شده</em> نیز الزامی است. کد تأیید به همین شماره ارسال می‌شود و بدون تأیید مالک، امکان دسترسی به اطلاعات نیست.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="financial-credit" data-keywords="زمان اعتبار گزارش">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">گزارش اعتبار مالی چقدر زمان معتبر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        گزارش اعتبار مالی معمولاً <strong>۳ ماه اعتبار</strong> دارد، اما برای موارد حساس مانند دریافت وام کلان، بانک‌ها ممکن است گزارش <em>حداکثر ۱ ماهه</em> درخواست کنند. توصیه می‌شود برای تصمیمات مهم، گزارش جدید دریافت کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="financial-credit" data-keywords="تفاوت گزارش پایه استاندارد جامع">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">تفاوت گزارش پایه، استاندارد و جامع چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>گزارش پایه:</strong> شامل امتیاز کلی، وضعیت چک و رتبه‌بندی اصلی</p>
                        <p><strong>گزارش استاندارد:</strong> شامل جزئیات تسهیلات، سوابق پرداخت و محکومیت‌ها</p>
                        <p><strong>گزارش جامع:</strong> کامل‌ترین نوع شامل تحلیل ریسک، پیش‌بینی اعتباری و راهکارهای بهبود</p>
                        <p class="mt-3"><em>انتخاب نوع گزارش بستگی به هدف و اهمیت تصمیم‌گیری دارد.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="financial-credit" data-keywords="بهبود امتیاز اعتباری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه می‌توانم امتیاز اعتباری خود را بهبود دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>بهبود امتیاز اعتباری نیازمند زمان و اقدامات مستمر است:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>پرداخت به موقع اقساط:</strong> مهم‌ترین عامل</li>
                            <li><strong>رفع سوء اثر چک‌های برگشتی</strong></li>
                            <li><strong>کاهش میزان بدهی کل</strong></li>
                            <li><strong>حفظ روابط طولانی مدت با بانک</strong></li>
                            <li><strong>اجتناب از ضمانت‌های پرریسک</strong></li>
                        </ul>
                        <p class="mt-3"><em>معمولاً ۶ تا ۱۸ ماه برای دیدن تغییرات محسوس نیاز است.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="financial-credit" data-keywords="اعتبار مالی شرکت اشخاص حقوقی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا امکان استعلام اعتبار مالی شرکت‌ها وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، امکان استعلام اعتبار اشخاص حقوقی وجود دارد.</strong> برای شرکت‌ها از <em>شناسه ملی</em> به جای کد ملی استفاده می‌شود. گزارش شرکت‌ها شامل اطلاعات مدیران، سوابق مالی شرکت، تسهیلات دریافتی و وضعیت اعتباری است. این اطلاعات برای تصمیم‌گیری‌های تجاری حیاتی است.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="financial-credit" data-keywords="دقت اطلاعات اعتبار">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">میزان دقت اطلاعات گزارش اعتبار مالی چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        اطلاعات گزارش اعتبار مالی با <strong>دقت بالای ۹۸٪</strong> از منابع رسمی دریافت می‌شود. پیشخوانک اتصال مستقیم به پایگاه‌داده‌های <em>بانک مرکزی</em>، <em>قوه قضاییه</em> و <em>سامانه صیاد</em> دارد. در صورت مشاهده اطلاعات نادرست، می‌توانید از طریق سامانه‌های رسمی اعتراض کنید.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="financial-credit" data-keywords="استعلام اعتبار چندبار">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا استعلام مکرر اعتبار مالی ضرر دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        استعلام شخصی اعتبار مالی <strong>هیچ تأثیر منفی</strong> بر امتیاز اعتباری ندارد. شما می‌توانید هر چند وقت یکبار وضعیت خود را بررسی کنید. اما <em>استعلام‌های متعدد از سوی بانک‌های مختلف</em> در مدت کوتاه ممکن است نشان‌دهنده تقاضای وام از چندین منبع باشد و تأثیر کمی بر امتیاز داشته باشد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="financial-credit" data-keywords="اعتبار مالی ضامن">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">ضمانت دادن چه تأثیری بر اعتبار مالی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        ضمانت دادن <strong>مستقیماً</strong> بر امتیاز اعتباری تأثیر نمی‌گذارد، اما <em>ریسک‌های جانبی</em> دارد:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>در صورت نکول اصیل، ضامن باید بازپرداخت کند</li>
                            <li>تأثیر منفی بر امتیاز اعتباری در صورت عدم پرداخت</li>
                            <li>محدودیت در دریافت تسهیلات جدید</li>
                            <li>ثبت در فهرست بدهکاران بانکی</li>
                        </ul>
                        <p class="mt-3"><em>قبل از ضمانت، حتماً وضعیت اعتباری اصیل را بررسی کنید.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="financial-credit" data-keywords="اعتبار مالی بعد طلاق">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">طلاق چه تأثیری بر وضعیت اعتباری دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>طلاق خود تأثیر مستقیمی بر امتیاز اعتباری ندارد</strong>، اما مسائل جانبی ممکن است تأثیرگذار باشند:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>تقسیم دارایی‌ها و بدهی‌های مشترک</li>
                            <li>ضمانت‌های متقابل که قبلاً ارائه شده</li>
                            <li>حساب‌های بانکی مشترک</li>
                            <li>تسهیلات مشترک که نیاز به تسویه دارد</li>
                        </ul>
                        <p class="mt-3"><em>توصیه می‌شود وضعیت تسهیلات مشترک را پس از طلاق بررسی کنید.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="financial-credit" data-keywords="اعتبار مالی بدون حساب بانکی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر هرگز حساب بانکی نداشته باشم، چه امتیاز اعتباری خواهم داشت؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        افراد بدون سابقه بانکی معمولاً در <strong>رده C (امتیاز ۴۵۰-۵۹۹)</strong> قرار می‌گیرند. این وضعیت <em>نه خوب و نه بد</em> محسوب می‌شود. برای بهبود وضعیت:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>حساب بانکی باز کنید و فعال نگه دارید</li>
                            <li>از کارت‌های اعتباری کوچک استفاده کنید</li>
                            <li>پس‌انداز منظم داشته باشید</li>
                            <li>روابط طولانی مدت با بانک برقرار کنید</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="financial-credit" data-keywords="اعتبار مالی فوت شده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم اعتبار مالی فرد فوت شده را استعلام کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-blue-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، پس از فوت امکان استعلام اعتبار مالی وجود ندارد.</strong> اما <em>ورثه می‌توانند</em> برای تسویه بدهی‌ها و دریافت مطالبات، با ارائه گواهی فوت و سند وراثت از طریق بانک‌ها اطلاعات مالی متوفی را دریافت کنند. این فرآیند از طریق <strong>شعب بانک‌ها</strong> و با مدارک قانونی انجام می‌شود.
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 2: محکومیت مالی (Financial Judgment) - 9 FAQs -->
        <div class="faq-category" data-category="judgment">
            <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                    استعلام محکومیت مالی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="judgment" data-keywords="محکومیت مالی چیست">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">محکومیت مالی چیست و شامل چه مواردی می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>محکومیت مالی</strong> احکام قضایی مربوط به تخلفات مالی و اقتصادی است که شامل:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>محکومیت‌های چکی:</strong> احکام مربوط به چک‌های برگشتی</li>
                            <li><strong>محکومیت‌های بانکی:</strong> عدم بازپرداخت تسهیلات</li>
                            <li><strong>محکومیت‌های مالیاتی:</strong> فرار مالیاتی</li>
                            <li><strong>محکومیت‌های تجاری:</strong> نقض قراردادهای اقتصادی</li>
                            <li><strong>محکومیت‌های گمرکی:</strong> قاچاق و تخلفات گمرکی</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="judgment" data-keywords="سامانه سهام قوه قضاییه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه سهام قوه قضاییه چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>سامانه سهام</strong> سیستم هوشمند استعلامات مالی قوه قضاییه است که <em>محکومیت‌های مالی</em> تمامی اشخاص را ثبت می‌کند. این سامانه براساس ماده ۱۱۶ بند پ برنامه ششم توسعه ایجاد شده و دسترسی به آن محدود به <strong>مقامات قضایی مجاز</strong> است. بانک‌ها و مؤسسات مالی نیز در موارد خاص می‌توانند از این سامانه استفاده کنند.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="judgment" data-keywords="دسترسی محکومیت مالی خودم">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه می‌توانم محکومیت‌های مالی خودم را ببینم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای مشاهده محکومیت‌های مالی شخصی خود می‌توانید از <strong>سامانه سنا</strong> استفاده کنید:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>به آدرس <strong>my.adliran.ir</strong> مراجعه کنید</li>
                            <li>با کد ملی و شماره تلفن وارد شوید</li>
                            <li>کد تأیید پیامکی را وارد کنید</li>
                            <li>گزارش محکومیت‌های مالی را دریافت کنید</li>
                        </ul>
                        <p class="mt-3"><em>این روش تنها برای استعلام شخصی قابل استفاده است.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="judgment" data-keywords="پاک کردن محکومیت مالی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توان محکومیت مالی را پاک کرد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، در موارد محدود امکان پاک کردن محکومیت مالی وجود دارد:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>تسویه کامل بدهی:</strong> پرداخت محکوم‌به و جبران خسارات</li>
                            <li><strong>مصالحه با شاکی:</strong> رضایت طرف مقابل</li>
                            <li><strong>گذشت زمان:</strong> برخی احکام پس از مدت زمان معین</li>
                            <li><strong>تجدیدنظر قضایی:</strong> در صورت اشتباه در رأی</li>
                        </ul>
                        <p class="mt-3"><em>لازم است با وکیل مشورت کنید.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="judgment" data-keywords="محکومیت مالی تأثیر وام">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">محکومیت مالی چه تأثیری بر دریافت وام دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        محکومیت مالی <strong>شدیداً</strong> بر امکان دریافت تسهیلات تأثیر می‌گذارد:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>رد درخواست وام:</strong> اکثر بانک‌ها وام نمی‌دهند</li>
                            <li><strong>افزایش نرخ بهره:</strong> در صورت موافقت</li>
                            <li><strong>نیاز به ضمانت بیشتر</strong></li>
                            <li><strong>محدودیت مبلغ وام</strong></li>
                        </ul>
                        <p class="mt-3">بهترین راه حل <em>تسویه محکومیت</em> قبل از درخواست وام است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="judgment" data-keywords="محکومیت مالی مدت زمان">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">محکومیت مالی چقدر در سیستم باقی می‌ماند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مدت زمان بستگی به نوع محکومیت دارد:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>محکومیت‌های چکی:</strong> تا زمان تسویه</li>
                            <li><strong>محکومیت‌های مالیاتی:</strong> ۷-۱۰ سال</li>
                            <li><strong>محکومیت‌های بانکی:</strong> ۵-۷ سال پس از تسویه</li>
                            <li><strong>محکومیت‌های سنگین:</strong> ممکن است دائمی باشد</li>
                        </ul>
                        <p class="mt-3"><em>بهترین راه تسویه سریع و کامل بدهی است.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="judgment" data-keywords="محکومیت مالی اشتباه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر محکومیت مالی اشتباه در پرونده من ثبت شده باشد چکار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در صورت وجود اشتباه در محکومیت مالی:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>مراجعه به دادگاه صادرکننده حکم</strong></li>
                            <li><strong>ارائه مدارک تسویه یا رد ادعا</strong></li>
                            <li><strong>درخواست تصحیح یا ابطال حکم</strong></li>
                            <li><strong>مشورت با وکیل متخصص</strong></li>
                        </ul>
                        <p class="mt-3">این فرآیند ممکن است <em>۳ تا ۶ ماه</em> زمان ببرد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="judgment" data-keywords="محکومیت مالی شریک تجاری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه قبل از همکاری تجاری، محکومیت مالی طرف مقابل را بررسی کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای بررسی محکومیت مالی شریک تجاری:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>درخواست رضایت کتبی</strong> برای استعلام</li>
                            <li><strong>استفاده از خدمات پیشخوانک</strong> برای استعلام قانونی</li>
                            <li><strong>بررسی سوابق شرکت</strong> در صورت همکاری با شخص حقوقی</li>
                            <li><strong>مشورت با حقوقدان</strong> برای تنظیم قرارداد</li>
                        </ul>
                        <p class="mt-3"><em>این کار می‌تواند از ریسک‌های مالی جلوگیری کند.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="judgment" data-keywords="محکومیت مالی مهریه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا مهریه پرداخت نشده جزو محکومیت مالی محسوب می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، مهریه پرداخت نشده در صورت صدور حکم قطعی جزو محکومیت مالی محسوب می‌شود.</strong> این موضوع تأثیر قابل توجهی بر امتیاز اعتباری دارد. راه‌حل‌های موجود:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>مصالحه با زوجه:</strong> تعیین مبلغ قابل پرداخت</li>
                            <li><strong>پرداخت اقساطی:</strong> با موافقت طرفین</li>
                            <li><strong>تنظیم قرارداد کتبی:</strong> برای شرایط پرداخت</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 3: سامانه صیاد (Sayad System) - 8 FAQs -->
        <div class="faq-category" data-category="sayad-check">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    سامانه صیاد و چک برگشتی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="sayad-check" data-keywords="سامانه صیاد چیست">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">سامانه صیاد چیست و چه کاربردی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>سامانه صیاد</strong> سیستم یکپارچه صدور چک بانک مرکزی است که در سال ۱۴۰۰ راه‌اندازی شد. اهداف اصلی:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>کاهش چک‌های برگشتی</strong> و جلوگیری از کلاهبرداری</li>
                            <li><strong>شفافیت در معاملات</strong> و افزایش اعتماد</li>
                            <li><strong>نظارت مستمر</strong> بر وضعیت اعتباری صاحبان چک</li>
                            <li><strong>امکان استعلام آنلاین</strong> وضعیت چک‌ها</li>
                        </ul>
                        <p class="mt-3">هر چک دارای <em>کد ۱۶ رقمی منحصر به فرد</em> است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sayad-check" data-keywords="رنگ‌بندی سامانه صیاد">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رنگ‌بندی سامانه صیاد چگونه است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        رنگ‌بندی سامانه صیاد براساس تعداد و مبلغ چک‌های برگشتی:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>سفید:</strong> فاقد چک برگشتی یا رفع سوء اثر</li>
                            <li><strong>زرد:</strong> ۱ چک برگشتی تا ۵۰ میلیون ریال</li>
                            <li><strong>نارنجی:</strong> ۲-۴ چک یا تا ۲۰۰ میلیون ریال</li>
                            <li><strong>قهوه‌ای:</strong> ۵-۱۰ چک یا تا ۵۰۰ میلیون ریال</li>
                            <li><strong>قرمز:</strong> بیش از ۱۰ چک یا بالای ۵۰۰ میلیون ریال</li>
                        </ul>
                        <p class="mt-3"><em>رنگ سفید بهترین و قرمز بدترین وضعیت است.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sayad-check" data-keywords="استعلام چک صیادی پیامک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از طریق پیامک چک صیادی را استعلام کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        برای استعلام چک از طریق پیامک:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>شماره مقصد:</strong> 4040701701</li>
                            <li><strong>فرمت پیامک:</strong> *1*1*کد ۱۶ رقمی چک</li>
                            <li><strong>مثال:</strong> *1*1*1234567890123456</li>
                            <li><strong>هزینه:</strong> ۳,۵۰۰ ریال به ازای هر استعلام</li>
                            <li><strong>محدودیت:</strong> حداکثر ۴ استعلام در روز</li>
                        </ul>
                        <p class="mt-3">نتیجه شامل <em>وضعیت چک</em> و <em>رنگ‌بندی صاحب چک</em> ارسال می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sayad-check" data-keywords="چک قدیمی قبل ۱۳۹۶">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا چک‌های قدیمی (قبل از ۱۳۹۶) قابل استعلام هستند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، چک‌های صادر شده قبل از سال ۱۳۹۶ قابل استعلام آنلاین نیستند.</strong> سامانه صیاد از سال ۱۳۹۶ راه‌اندازی شده و تنها چک‌هایی که پس از این تاریخ صادر شده‌اند، کد ۱۶ رقمی دارند و در سامانه ثبت هستند. برای چک‌های قدیمی باید <em>مراجعه حضوری به بانک</em> کرد.
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sayad-check" data-keywords="کد ۱۶ رقمی چک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کد ۱۶ رقمی چک را از کجا پیدا کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        کد ۱۶ رقمی چک معمولاً در <strong>پشت چک</strong> یا در <strong>قسمت پایین چک</strong> قرار دارد:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>این کد با حروف SAYAD شروع نمی‌شود</li>
                            <li>صرفاً شامل ۱۶ رقم است</li>
                            <li>معمولاً در قالب ۴ گروه ۴ رقمی نوشته می‌شود</li>
                            <li>اگر چک قدیمی است ممکن است این کد نداشته باشد</li>
                        </ul>
                        <p class="mt-3"><em>بدون این کد امکان استعلام آنلاین وجود ندارد.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sayad-check" data-keywords="چک جعلی تشخیص">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه چک جعلی را تشخیص دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <li>تطابق امضا با نمونه امضای ثبت شده</li>
                        </ul>
                        <p class="mt-3"><strong>همیشه قبل از پذیرش چک استعلام کنید!</strong></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sayad-check" data-keywords="چک تاریخ‌دار آینده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا چک‌های تاریخ‌دار (آینده) قابل استعلام هستند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، چک‌های تاریخ‌دار قابل استعلام هستند</strong> و می‌توانید وضعیت اعتباری صاحب چک را بررسی کنید. اما نکات مهم:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>چک قبل از تاریخ سررسید قابل وصول نیست</li>
                            <li>وضعیت اعتباری صاحب چک ممکن است تا سررسید تغییر کند</li>
                            <li>توصیه می‌شود در تاریخ سررسید مجدد استعلام کنید</li>
                        </ul>
                        <p class="mt-3"><em>استعلام فعلی نشان‌دهنده وضعیت امروز است.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="sayad-check" data-keywords="استعلام چک دسته‌ای">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم چندین چک را همزمان استعلام کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، پیشخوانک امکان استعلام دسته‌ای چندین چک را فراهم کرده است.</strong> شما می‌توانید:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>کدهای ۱۶ رقمی چندین چک را همزمان وارد کنید</li>
                            <li>نتایج را یکجا و در قالب جدول دریافت کنید</li>
                            <li>گزارش مقایسه‌ای وضعیت چک‌ها را مشاهده کنید</li>
                            <li>امکان ذخیره و پرینت گزارش کامل</li>
                        </ul>
                        <p class="mt-3">این ویژگی برای <em>فروشندگان و کسب‌وکارها</em> بسیار مفید است.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 4: رتبه اعتباری (Credit Rating) - 7 FAQs -->
        <div class="faq-category" data-category="credit-rating">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    رتبه اعتباری و امتیازدهی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="credit-rating" data-keywords="رتبه اعتباری A1 A2 A3 B1 B2 B3">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رتبه‌بندی A1، A2، A3 تا E3 چه معنایی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        رتبه‌بندی اعتباری به ۵ دسته اصلی تقسیم می‌شود:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>A1-A3:</strong> ریسک بسیار کم (امتیاز ۷۵۰-۹۰۰)</li>
                            <li><strong>B1-B3:</strong> ریسک کم (امتیاز ۶۰۰-۷۴۹)</li>
                            <li><strong>C1-C3:</strong> ریسک متوسط (امتیاز ۴۵۰-۵۹۹)</li>
                            <li><strong>D1-D3:</strong> ریسک زیاد (امتیاز ۳۰۰-۴۴۹)</li>
                            <li><strong>E1-E3:</strong> ریسک بسیار زیاد (امتیاز ۰-۲۹۹)</li>
                        </ul>
                        <p class="mt-3">هر چه عدد کوچک‌تر باشد (مثل A1)، وضعیت بهتر است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="credit-rating" data-keywords="امتیاز ۷۰۰ خوب یا بد">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">امتیاز اعتباری ۷۰۰ خوب است یا بد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>امتیاز ۷۰۰ امتیاز خوبی محسوب می‌شود.</strong> این امتیاز شما را در <em>رده B2</em> قرار می‌دهد که به معنای <strong>ریسک کم</strong> است. با این امتیاز:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>احتمال زیاد تأیید وام</li>
                            <li>نرخ بهره مناسب</li>
                            <li>شرایط بازپرداخت انعطاف‌پذیر</li>
                            <li>محدودیت کم در دریافت تسهیلات</li>
                        </ul>
                        <p class="mt-3">برای بهبود، سعی کنید امتیاز خود را به بالای ۷۵۰ برسانید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="credit-rating" data-keywords="بدترین امتیاز اعتباری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">بدترین امتیاز اعتباری که ممکن است کسی داشته باشد چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        بدترین امتیاز اعتباری <strong>صفر (۰)</strong> است که در <em>رده E3</em> قرار می‌گیرد. این وضعیت معمولاً مربوط است به:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>تعداد زیاد چک‌های برگشتی</li>
                            <li>عدم پرداخت تسهیلات متعدد</li>
                            <li>محکومیت‌های مالی سنگین</li>
                            <li>قرار گیری در فهرست سیاه بانکی</li>
                        </ul>
                        <p class="mt-3"><em>بازگشت از این وضعیت ۲-۳ سال زمان می‌برد.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="credit-rating" data-keywords="رتبه اعتباری مثبت منفی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا امکان داشتن رتبه اعتباری منفی وجود دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، امتیاز اعتباری منفی وجود ندارد.</strong> حداقل امتیاز <em>صفر</em> و حداکثر <em>۹۰۰</em> است. اما ممکن است برخی سامانه‌ها وضعیت شما را به صورت توصیفی نمایش دهند:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>فاقد سابقه:</strong> برای تازه‌واردان</li>
                            <li><strong>نامشخص:</strong> اطلاعات کافی موجود نیست</li>
                            <li><strong>محدود:</strong> سابقه کم اما مثبت</li>
                        </ul>
                        <p class="mt-3">همه این وضعیت‌ها بهتر از امتیاز صفر هستند.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="credit-rating" data-keywords="تفاوت امتیاز رتبه اعتباری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">تفاوت امتیاز اعتباری و رتبه اعتباری چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>امتیاز اعتباری:</strong> عدد دقیق بین ۰ تا ۹۰۰</p>
                        <p><strong>رتبه اعتباری:</strong> طبقه‌بندی حروفی (A1 تا E3)</p>
                        <p class="mt-3">مثال:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>امتیاز ۸۵۰ = رتبه A1</li>
                            <li>امتیاز ۷۲۰ = رتبه B2</li>
                            <li>امتیاز ۵۵۰ = رتبه C2</li>
                        </ul>
                        <p class="mt-3">امتیاز دقیق‌تر است اما رتبه برای درک کلی آسان‌تر.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="credit-rating" data-keywords="رتبه اعتباری تغییر سرعت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رتبه اعتباری چقدر سریع تغییر می‌کند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        سرعت تغییر رتبه اعتباری بستگی به نوع تغییر دارد:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>تغییرات منفی:</strong> فوری تا ۱ هفته (چک برگشتی، عدم پرداخت)</li>
                            <li><strong>بهبودهای کوچک:</strong> ۱-۳ ماه</li>
                            <li><strong>بهبودهای قابل توجه:</strong> ۶-۱۲ ماه</li>
                            <li><strong>بازگشت کامل:</strong> ۱۸-۲۴ ماه</li>
                        </ul>
                        <p class="mt-3"><em>صبر و پایداری در بهبود رفتار مالی کلید موفقیت است.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="credit-rating" data-keywords="تأثیر سن بر رتبه اعتباری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا سن تأثیری بر رتبه اعتباری دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-purple-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، سن غیرمستقیم تأثیر دارد:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>جوانان (۱۸-۲۵):</strong> معمولاً فاقد سابقه یا سابقه کم</li>
                            <li><strong>میانسالان (۲۵-۵۰):</strong> بیشترین فعالیت مالی و بالاترین امتیاز</li>
                            <li><strong>سالمندان (۵۰+):</strong> کاهش تدریجی به دلیل کم شدن فعالیت</li>
                        </ul>
                        <p class="mt-3">مهم‌ترین عامل <em>سابقه مالی مثبت</em> است نه سن.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 5: تسهیلات بانکی (Banking Facilities) - 6 FAQs -->
        <div class="faq-category" data-category="banking">
            <div class="bg-gradient-to-r from-teal-600 to-cyan-600 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    استعلام تسهیلات بانکی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="banking" data-keywords="استعلام تسهیلات بانکی کد ملی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه می‌توانم تسهیلات بانکی خود را با کد ملی استعلام کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-teal-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        استعلام تسهیلات بانکی از طریق پیشخوانک:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>ورود کد ملی و شماره تلفن</strong> ثبت شده</li>
                            <li><strong>دریافت کد تأیید</strong> از طریق پیامک</li>
                            <li><strong>انتخاب نوع گزارش</strong> مورد نیاز</li>
                            <li><strong>پرداخت هزینه</strong> (معمولاً ۱۹,۵۰۰ تومان)</li>
                            <li><strong>دریافت گزارش کامل</strong> تسهیلات</li>
                        </ul>
                        <p class="mt-3">گزارش شامل تمام وام‌ها، اقساط و معوقات است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="banking" data-keywords="معوقات بانکی چیست">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">معوقات بانکی چیست و چگونه محاسبه می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-teal-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>معوقات بانکی</strong> اقساطی است که در موعد سررسید پرداخت نشده‌اند:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>تأخیر ۱-۳۰ روز:</strong> هشدار کتبی</li>
                            <li><strong>تأخیر ۳۰-۶۰ روز:</strong> محاسبه جریمه تأخیر</li>
                            <li><strong>تأخیر ۶۰-۹۰ روز:</strong> کاهش امتیاز اعتباری</li>
                            <li><strong>بیش از ۹۰ روز:</strong> طبقه‌بندی معوق</li>
                        </ul>
                        <p class="mt-3"><em>هرچه زودتر پرداخت کنید، کمتر آسیب می‌بینید.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="banking" data-keywords="تسهیلات مشکوک الوصول">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">تسهیلات مشکوک‌الوصول چه معنایی دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-teal-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>تسهیلات مشکوک‌الوصول</strong> وام‌هایی است که احتمال بازپرداخت آن‌ها پایین است:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li>تأخیر در پرداخت بیش از ۱۸۰ روز</li>
                            <li>عدم همکاری مشتری برای تسویه</li>
                            <li>کاهش شدید توان مالی</li>
                            <li>مشکلات قانونی یا قضایی</li>
                        </ul>
                        <p class="mt-3">این وضعیت <em>شدیداً</em> امتیاز اعتباری را کاهش می‌دهد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="banking" data-keywords="تسهیلات همسر طلاق">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا تسهیلات همسرم بر وضعیت اعتباری من تأثیر دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-teal-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>مستقیماً خیر، اما در موارد خاص بله:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>وام مشترک:</strong> هر دو نفر مسئول هستند</li>
                            <li><strong>ضمانت متقابل:</strong> در صورت نکول همسر</li>
                            <li><strong>حساب مشترک:</strong> بدهی‌های مشترک</li>
                            <li><strong>املاک مشترک:</strong> وثیقه مشترک</li>
                        </ul>
                        <p class="mt-3">بهتر است پس از ازدواج، وضعیت تسهیلات را بررسی کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="banking" data-keywords="تسهیلات متوفی ورثه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">تسهیلات متوفی چه تأثیری بر ورثه دارد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transformation group-hover:text-teal-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        تسهیلات متوفی <strong>بر امتیاز اعتباری ورثه تأثیر مستقیم ندارد</strong>، مگر:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>ضمانت داده باشند:</strong> مسئولیت ضامن باقی است</li>
                            <li><strong>وام مشترک:</strong> مسئولیت ادامه دارد</li>
                            <li><strong>وراثت بدهی:</strong> در حد میزان ارث</li>
                        </ul>
                        <p class="mt-3"><em>ورثه می‌توانند ارث را رد کنند تا از بدهی‌ها معاف شوند.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="banking" data-keywords="تسهیلات بدون بیمه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر تسهیلات من بیمه نباشد چه اتفاقی می‌افتد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-teal-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        عدم بیمه تسهیلات ریسک‌های زیادی دارد:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>در صورت فوت:</strong> ورثه باید کل بدهی را بپردازند</li>
                            <li><strong>در صورت ناتوانی:</strong> عدم پوشش اقساط</li>
                            <li><strong>بیماری جدی:</strong> ادامه پرداخت اجباری</li>
                            <li><strong>از کارافتادگی:</strong> مشکل در تسویه</li>
                        </ul>
                        <p class="mt-3"><strong>بیمه عمر و حوادث برای تسهیلات ضروری است.</strong></p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 6: رفع سوء اثر (Bad Effect Removal) - 6 FAQs -->
        <div class="faq-category" data-category="check-fix">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    رفع سوء اثر چک برگشتی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="check-fix" data-keywords="رفع سوء اثر چک چیست">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رفع سوء اثر چک چیست و چگونه انجام می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>رفع سوء اثر چک</strong> فرآیند حذف اثرات منفی چک برگشتی از سامانه بانکی است:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>واریز مبلغ:</strong> واریز مبلغ چک + جریمه به حساب</li>
                            <li><strong>گواهی بانک:</strong> دریافت گواهی رفع سوء اثر</li>
                            <li><strong>ثبت در سیستم:</strong> حذف از فهرست برگشتی</li>
                            <li><strong>بهبود رنگ:</strong> بازگشت به رنگ بهتر در صیاد</li>
                        </ul>
                        <p class="mt-3">این کار معمولاً <em>۲۴-۴۸ ساعت</em> زمان می‌برد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="check-fix" data-keywords="رفع سوء اثر بدون چک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا بدون داشتن چک می‌توانم سوء اثر را رفع کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، امکان رفع سوء اثر بدون چک وجود دارد:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>با کد ۱۶ رقمی:</strong> کد چک کافی است</li>
                            <li><strong>با رضایت‌نامه:</strong> موافقت کتبی دریافت‌کننده</li>
                            <li><strong>با حکم دادگاه:</strong> در موارد اختلافی</li>
                            <li><strong>واریز به حساب:</strong> واریز مبلغ + جریمه</li>
                        </ul>
                        <p class="mt-3"><em>داشتن چک فرآیند را آسان‌تر می‌کند اما ضروری نیست.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="check-fix" data-keywords="هزینه رفع سوء اثر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">هزینه رفع سوء اثر چک چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        هزینه رفع سوء اثر شامل:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>مبلغ اصل چک:</strong> مبلغ کامل چک</li>
                            <li><strong>جریمه تأخیر:</strong> معمولاً ۶٪ سالانه</li>
                            <li><strong>کارمزد بانک:</strong> حدود ۱۰,۰۰۰ تومان</li>
                            <li><strong>کارمزد سامانه:</strong> حدود ۵,۰۰۰ تومان</li>
                        </ul>
                        <p class="mt-3">مجموعاً <em>۱۰۳-۱۰۵٪</em> مبلغ چک + کارمزدها.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="check-fix" data-keywords="مدت زمان رفع سوء اثر">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">رفع سوء اثر چک چقدر زمان می‌برد؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مراحل و زمان‌بندی رفع سوء اثر:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>واریز وجه:</strong> فوری</li>
                            <li><strong>ثبت در بانک:</strong> ۴-۶ ساعت</li>
                            <li><strong>به‌روزرسانی صیاد:</strong> ۲۴ ساعت</li>
                            <li><strong>بازتاب در امتیاز:</strong> ۴۸-۷۲ ساعت</li>
                        </ul>
                        <p class="mt-3">کل فرآیند حداکثر <em>۳ روز کاری</em> طول می‌کشد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="check-fix" data-keywords="رفع سوء اثر چندین چک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا می‌توانم چندین چک را همزمان رفع سوء اثر کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، امکان رفع سوء اثر دسته‌ای وجود دارد:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>تهیه فهرست:</strong> لیست کامل چک‌های برگشتی</li>
                            <li><strong>محاسبه کل هزینه:</strong> جمع مبالغ + جرائم</li>
                            <li><strong>واریز یکجا:</strong> پرداخت کل مبلغ</li>
                            <li><strong>پیگیری مراحل:</strong> بررسی رفع سوء اثر همه</li>
                        </ul>
                        <p class="mt-3">این روش <em>سریع‌تر و اقتصادی‌تر</em> است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="check-fix" data-keywords="رفع سوء اثر فوت صاحب چک">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر صاحب چک فوت کند، آیا سوء اثر باقی می‌ماند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        در صورت فوت صاحب چک:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>سوء اثر باقی می‌ماند</strong> تا زمان تسویه</li>
                            <li><strong>ورثه می‌توانند رفع کنند</strong> با ارائه مدارک</li>
                            <li><strong>تسویه از ترکه</strong> در صورت وجود دارایی</li>
                            <li><strong>بیمه عمر</strong> ممکن است هزینه را پوشش دهد</li>
                        </ul>
                        <p class="mt-3"><em>بهتر است قبل از فوت، مسائل مالی تسویه شود.</em></p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 7: بانک مرکزی (Central Bank) - 5 FAQs -->
        <div class="faq-category" data-category="central-bank">
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                    </svg>
                    سامانه‌های رسمی بانک مرکزی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="central-bank" data-keywords="سامانه رسمی بانک مرکزی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کدام سامانه‌های بانک مرکزی رسمی هستند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        سامانه‌های رسمی بانک مرکزی عبارتند از:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>سامانه صیاد (cbi.ir/EstelamSayad):</strong> استعلام چک</li>
                            <li><strong>NICS24 (ics24.ir):</strong> تنها شرکت رسمی اعتبارسنجی</li>
                            <li><strong>سامانه‌های بانک‌ها:</strong> اتصال مستقیم به مرکز</li>
                            <li><strong>پیشخوانک:</strong> پلتفرم مجاز با اتصال رسمی</li>
                        </ul>
                        <p class="mt-3"><em>هیچ سامانه رایگان رسمی وجود ندارد!</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="central-bank" data-keywords="تشخیص سامانه تقلبی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه سامانه‌های تقلبی را تشخیص دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>علائم سامانه‌های تقلبی:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>ادعای رایگان بودن</strong> خدمات اعتبارسنجی</li>
                            <li><strong>عدم درخواست تأیید هویت</strong> دوعاملی</li>
                            <li><strong>اطلاعات نامشخص یا قدیمی</strong></li>
                            <li><strong>عدم وجود مجوزهای رسمی</strong></li>
                            <li><strong>درخواست اطلاعات بانکی</strong> حساس</li>
                        </ul>
                        <p class="mt-3">همیشه از <em>منابع رسمی و مجاز</em> استفاده کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="central-bank" data-keywords="ارتباط با بانک مرکزی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه می‌توانم با بانک مرکزی ارتباط برقرار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        راه‌های ارتباط با بانک مرکزی:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>وبسایت رسمی:</strong> www.cbi.ir</li>
                            <li><strong>تلفن تماس:</strong> ۰۲۱-۲۹۷۳</li>
                            <li><strong>ایمیل رسمی:</strong> public@cbi.ir</li>
                            <li><strong>مراجعه حضوری:</strong> فردوسی، خیابان میدان فردوسی</li>
                        </ul>
                        <p class="mt-3">برای شکایت از سامانه‌های تقلبی نیز می‌توانید تماس بگیرید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="central-bank" data-keywords="بانک مرکزی مجوز اعتبارسنجی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کدام شرکت‌ها مجوز رسمی اعتبارسنجی دارند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>تنها یک شرکت مجوز رسمی دارد:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>شرکت مشاوره رتبه‌بندی اعتباری ایران (NICS24)</strong></li>
                            <li><strong>وبسایت رسمی:</strong> ics24.ir</li>
                            <li><strong>زیرنظر مستقیم بانک مرکزی</strong></li>
                            <li><strong>تنها منبع معتبر گزارش اعتباری</strong></li>
                        </ul>
                        <p class="mt-3">سایر شرکت‌ها <em>واسطه</em> هستند و از NICS24 اطلاعات دریافت می‌کنند.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="central-bank" data-keywords="قوانین بانک مرکزی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">قوانین جدید بانک مرکزی در مورد اعتبارسنجی چیست؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-orange-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        آخرین قوانین اعتبارسنجی بانک مرکزی:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>اجباری شدن اعتبارسنجی</strong> برای وام‌های بالای ۱۰۰ میلیون</li>
                            <li><strong>محاسبه سقف چک</strong> براساس امتیاز اعتباری</li>
                            <li><strong>ممنوعیت صدور چک</strong> برای رده‌های E</li>
                            <li><strong>به‌روزرسانی ماهانه</strong> اطلاعات اعتباری</li>
                            <li><strong>حق دسترسی شخصی</strong> به گزارش اعتباری</li>
                        </ul>
                        <p class="mt-3">این قوانین از <em>فروردین ۱۴۰۳</em> اجرایی شده‌اند.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 8: مسائل فنی (Technical Issues) - 5 FAQs -->
        <div class="faq-category" data-category="technical">
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    مسائل فنی و رفع مشکل
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="technical" data-keywords="خطا در دریافت گزارش">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">در صورت خطا در دریافت گزارش چه کار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        مراحل رفع خطا در دریافت گزارش:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>بررسی اتصال اینترنت</strong> و تلاش مجدد</li>
                            <li><strong>پاک کردن کش مرورگر</strong> و بارگذاری مجدد</li>
                            <li><strong>تغییر مرورگر</strong> یا استفاده از حالت ناشناس</li>
                            <li><strong>بررسی صحت اطلاعات</strong> وارد شده</li>
                            <li><strong>تماس با پشتیبانی</strong> پیشخوانک</li>
                        </ul>
                        <p class="mt-3">اگر مشکل ادامه داشت، <em>مبلغ کسر شده برگشت</em> داده می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="کد تایید نمی‌آید">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کد تأیید پیامکی ارسال نمی‌شود، چه کار کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        راه‌حل‌های رفع مشکل عدم دریافت کد تأیید:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>بررسی صحت شماره تلفن</strong> وارد شده</li>
                            <li><strong>چک کردن پوشش شبکه</strong> موبایل</li>
                            <li><strong>خالی کردن صندوق پیام</strong> در صورت پر بودن</li>
                            <li><strong>صبر ۲-۳ دقیقه</strong> برای دریافت پیامک</li>
                            <li><strong>تلاش مجدد</strong> یا تماس با پشتیبانی</li>
                        </ul>
                        <p class="mt-3">گاهی در ساعات شلوغی <em>تأخیر در ارسال</em> رخ می‌دهد.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="سرعت بالا سامانه کند">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چرا سامانه کند است و چگونه سرعت را بهبود دهم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>علل کندی سامانه و راه‌حل‌ها:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>ساعات شلوغی (۱۰-۱۲ و ۱۶-۱۸):</strong> تلاش در ساعات دیگر</li>
                            <li><strong>اتصال اینترنت ضعیف:</strong> بررسی سرعت اینترنت</li>
                            <li><strong>مرورگر قدیمی:</strong> به‌روزرسانی مرورگر</li>
                            <li><strong>فایل‌های موقت:</strong> پاک کردن کش</li>
                        </ul>
                        <p class="mt-3">معمولاً استعلام در <em>کمتر از ۳۰ ثانیه</em> انجام می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="مشکل مرورگر سازگاری">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کدام مرورگرها با سامانه سازگار هستند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>مرورگرهای پشتیبانی شده:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>کروم (Chrome):</strong> نسخه ۹۰ و بالاتر</li>
                            <li><strong>فایرفاکس (Firefox):</strong> نسخه ۸۵ و بالاتر</li>
                            <li><strong>سافاری (Safari):</strong> نسخه ۱۴ و بالاتر</li>
                            <li><strong>اج (Edge):</strong> نسخه ۹۰ و بالاتر</li>
                        </ul>
                        <p class="mt-3">برای بهترین عملکرد از <em>آخرین نسخه مرورگر</em> استفاده کنید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="technical" data-keywords="پشتیبان گیری گزارش">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از گزارش دریافتی پشتیبان تهیه کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-gray-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        روش‌های پشتیبان‌گیری از گزارش:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>دانلود PDF:</strong> ذخیره فایل PDF بر روی دستگاه</li>
                            <li><strong>پرینت:</strong> چاپ گزارش برای مدارک فیزیکی</li>
                            <li><strong>اسکرین‌شات:</strong> عکس‌گیری از صفحه گزارش</li>
                            <li><strong>ایمیل:</strong> ارسال گزارش به ایمیل خود</li>
                        </ul>
                        <p class="mt-3">توصیه می‌شود <em>هم فایل PDF و هم پرینت</em> تهیه کنید.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 9: امنیت و حریم خصوصی (Security & Privacy) - 4 FAQs -->
        <div class="faq-category" data-category="security">
            <div class="bg-gradient-to-r from-red-600 to-pink-600 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    امنیت و حریم خصوصی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="security" data-keywords="امنیت اطلاعات حریم خصوصی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">امنیت اطلاعات من در پیشخوانک چگونه حفظ می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p>پیشخوانک از <strong>بالاترین استانداردهای امنیتی</strong> استفاده می‌کند:</p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>رمزنگاری SSL 256-bit:</strong> حفاظت از داده‌ها در انتقال</li>
                            <li><strong>احراز هویت دوعاملی:</strong> کد ملی + تأیید پیامکی</li>
                            <li><strong>عدم ذخیره‌سازی:</strong> اطلاعات در سرور ذخیره نمی‌شود</li>
                            <li><strong>اتصال مستقیم:</strong> ارتباط مستقیم با منابع رسمی</li>
                            <li><strong>لاگ‌های امنیتی:</strong> نظارت بر تمام دسترسی‌ها</li>
                        </ul>
                        <p class="mt-3"><em>اطلاعات شما پس از دریافت گزارش حذف می‌شود.</em></p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="اشتراک اطلاعات سوءاستفاده">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا اطلاعات من با اشخاص ثالث به اشتراک گذاشته می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>قطعاً خیر!</strong> پیشخوانک متعهد به حفظ حریم خصوصی است:
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>عدم فروش اطلاعات</strong> به هیچ فرد یا سازمان</li>
                            <li><strong>عدم اشتراک‌گذاری</strong> با شرکت‌های تبلیغاتی</li>
                            <li><strong>دسترسی محدود</strong> تنها برای ارائه خدمات</li>
                            <li><strong>رعایت قوانین حریم خصوصی</strong> ایران</li>
                        </ul>
                        <p class="mt-3">تنها شما <em>مالک اطلاعات خود</em> هستید.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="شناسه استعلام چک امنیت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا با شناسه چک می‌توان اطلاعات شخصی من را دید؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>خیر، شناسه چک اطلاعات محدودی نشان می‌دهد:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>نام و نام خانوادگی:</strong> تنها نام صاحب چک</li>
                            <li><strong>رنگ‌بندی صیاد:</strong> وضعیت اعتباری کلی</li>
                            <li><strong>وضعیت چک:</strong> معتبر یا نامعتبر بودن</li>
                            <li><strong>عدم نمایش:</strong> کد ملی، آدرس، شماره تلفن</li>
                        </ul>
                        <p class="mt-3">این اطلاعات برای <em>امنیت معاملات</em> طراحی شده‌اند.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="security" data-keywords="کلاهبرداری جعل هویت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">چگونه از کلاهبرداری و سوءاستفاده از اطلاعاتم جلوگیری کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-red-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>راهکارهای حفاظت از خود:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>استفاده از منابع رسمی:</strong> پیشخوانک و سایت‌های مجاز</li>
                            <li><strong>عدم اعطای اطلاعات</strong> به سایت‌های مشکوک</li>
                            <li><strong>بررسی آدرس سایت</strong> قبل از ورود اطلاعات</li>
                            <li><strong>هرگز رمز بانکی</strong> را در سایت‌های استعلام وارد نکنید</li>
                            <li><strong>گزارش کلاهبرداری</strong> به مراجع قانونی</li>
                        </ul>
                        <p class="mt-3">در صورت مشکوک بودن، <em>از ورود اطلاعات خودداری کنید.</em></p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 10: هزینه‌ها و پرداخت (Costs & Payment) - 3 FAQs -->
        <div class="faq-category" data-category="costs">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    هزینه‌ها و روش‌های پرداخت
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="costs" data-keywords="هزینه استعلام اعتبار مالی">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">هزینه استعلام اعتبار مالی و محکومیت مالی چقدر است؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <p><strong>جدول هزینه‌های خدمات پیشخوانک:</strong></p>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>گزارش پایه اعتبار:</strong> ۱۵,۰۰۰ تومان</li>
                            <li><strong>گزارش استاندارد:</strong> ۲۵,۰۰۰ تومان</li>
                            <li><strong>گزارش جامع:</strong> ۳۵,۰۰۰ تومان</li>
                            <li><strong>استعلام محکومیت مالی:</strong> ۱۰,۰۰۰ تومان</li>
                            <li><strong>استعلام تسهیلات:</strong> ۱۹,۵۰۰ تومان</li>
                        </ul>
                        <p class="mt-3">هزینه‌ها شامل <em>تمام مالیات‌ها</em> است.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="روش پرداخت درگاه">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">کدام روش‌های پرداخت در پیشخوانک پذیرفته می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>روش‌های پرداخت موجود:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>کارت‌های بانکی:</strong> تمام کارت‌های عضو شتاب</li>
                            <li><strong>اینترنت بانک:</strong> پرداخت مستقیم از حساب</li>
                            <li><strong>موبایل بانک:</strong> از طریق اپلیکیشن بانک</li>
                            <li><strong>کیف پول الکترونیکی:</strong> رایپی، آپ و...</li>
                            <li><strong>اعتبار حساب:</strong> شارژ حساب کاربری</li>
                        </ul>
                        <p class="mt-3">تمام پرداخت‌ها در <em>محیط امن بانکی</em> انجام می‌شود.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="costs" data-keywords="بازگشت وجه عدم دریافت">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">اگر پس از پرداخت گزارش دریافت نکنم، وجه برگشت داده می‌شود؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-green-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، ضمانت ۱۰۰٪ بازگشت وجه داریم:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>عدم دریافت گزارش:</strong> بازگشت کامل وجه</li>
                            <li><strong>خطا در گزارش:</strong> رایگان تجدید یا بازگشت</li>
                            <li><strong>مدت زمان بازگشت:</strong> حداکثر ۷۲ ساعت</li>
                            <li><strong>روش بازگشت:</strong> به همان کارت یا حساب</li>
                        </ul>
                        <p class="mt-3">برای درخواست بازگشت با <em>پشتیبانی</em> تماس بگیرید.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 11: مسائل حقوقی (Legal Issues) - 2 FAQs -->
        <div class="faq-category" data-category="legal">
            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-t-2xl px-6 py-4">
                <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                    مسائل حقوقی و قانونی
                </h3>
            </div>
            <div class="bg-white rounded-b-2xl border border-gray-200 divide-y divide-gray-100">

                <div class="faq-item p-6" data-category="legal" data-keywords="اعتبار قانونی گزارش">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">آیا گزارش‌های پیشخوانک اعتبار قانونی دارند؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>بله، گزارش‌های پیشخوانک کاملاً معتبر و قانونی هستند:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>منبع رسمی:</strong> دریافت از سامانه‌های رسمی</li>
                            <li><strong>مهر و امضا:</strong> تأیید رسمی پیشخوانک</li>
                            <li><strong>کد پیگیری:</strong> قابل بررسی و ردیابی</li>
                            <li><strong>پذیرش در مراجع:</strong> بانک‌ها و مؤسسات رسمی</li>
                            <li><strong>اعتبار قضایی:</strong> قابل ارائه در دادگاه</li>
                        </ul>
                        <p class="mt-3">این گزارش‌ها <em>حجیت قانونی</em> دارند.</p>
                    </div>
                </div>

                <div class="faq-item p-6" data-category="legal" data-keywords="شکایت اطلاعات نادرست">
                    <button class="faq-question w-full text-right flex items-start justify-between group">
                        <h4 class="font-semibold text-gray-800 text-lg">در صورت وجود اطلاعات نادرست در گزارش، چگونه شکایت کنم؟</h4>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform group-hover:text-indigo-600 faq-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="faq-answer hidden mt-4 text-gray-700 leading-relaxed">
                        <strong>مراحل شکایت و تصحیح اطلاعات:</strong>
                        <ul class="list-disc mr-6 mt-3 space-y-1">
                            <li><strong>تماس با پیشخوانک:</strong> ارسال درخواست تصحیح</li>
                            <li><strong>ارائه مدارک:</strong> اسناد مثبت برای تصحیح</li>
                            <li><strong>پیگیری با منبع اصلی:</strong> بانک مرکزی یا قوه قضاییه</li>
                            <li><strong>درخواست رسمی:</strong> نامه رسمی به مراجع ذی‌ربط</li>
                            <li><strong>مشاوره حقوقی:</strong> در صورت لزوم</li>
                        </ul>
                        <p class="mt-3">فرآیند تصحیح معمولاً <em>۳۰-۶۰ روز</em> زمان می‌برد.</p>
                    </div>
                </div>

            </div>
        </div>
        
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('advanced-faq-search');
    const categoryBtns = document.querySelectorAll('.faq-category-btn');
    const faqItems = document.querySelectorAll('.faq-item');
    const faqQuestions = document.querySelectorAll('.faq-question');
    const resultsDiv = document.getElementById('faq-results');
    const resultsCount = document.getElementById('results-count');

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        let visibleCount = 0;
        
        faqItems.forEach(item => {
            const keywords = item.dataset.keywords.toLowerCase();
            const question = item.querySelector('.faq-question h4').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            if (keywords.includes(searchTerm) || question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        if (searchTerm) {
            resultsDiv.classList.remove('hidden');
            resultsCount.textContent = visibleCount;
        } else {
            resultsDiv.classList.add('hidden');
        }
    });

    // Category filter functionality
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.dataset.category;
            
            // Update active button
            categoryBtns.forEach(b => b.classList.remove('active', 'bg-purple-600', 'text-white'));
            categoryBtns.forEach(b => b.classList.add('bg-gray-100', 'text-gray-700'));
            this.classList.add('active', 'bg-purple-600', 'text-white');
            this.classList.remove('bg-gray-100', 'text-gray-700');
            
            // Filter items
            if (category === 'all') {
                faqItems.forEach(item => item.style.display = 'block');
            } else {
                faqItems.forEach(item => {
                    if (item.dataset.category === category) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
            
            // Clear search
            searchInput.value = '';
            resultsDiv.classList.add('hidden');
        });
    });

    // FAQ accordion functionality
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
});
</script>

<style>
.faq-question:hover h4 {
    color: #3B82F6;
}

.faq-item {
    transition: all 0.3s ease;
}

.faq-item:hover {
    background-color: #F9FAFB;
}

.faq-answer {
    animation: slideDown 0.3s ease;
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

.faq-category-btn {
    transition: all 0.2s ease;
}

.faq-chevron {
    transition: transform 0.3s ease;
}
</style>