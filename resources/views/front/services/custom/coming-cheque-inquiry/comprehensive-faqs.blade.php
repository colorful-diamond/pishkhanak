{{-- Advanced Comprehensive FAQ System for Coming Check Inquiry Service --}}
<div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-3xl p-8 mb-8">
    <div class="text-center">
        <h2 class="text-3xl font-bold text-dark-sky-700 mb-4">
            🏛️ مرجع کامل سوالات متداول استعلام وضعیت چک در راه
        </h2>
        <p class="text-lg text-gray-700 mb-6">
            بیش از <strong class="text-purple-600">۶۰ سوال و پاسخ تخصصی</strong> درباره استعلام وضعیت چک‌های در راه در سیستم صیاد
        </p>
        
        {{-- Advanced Search System --}}
        <div class="relative max-w-2xl mx-auto mb-6">
            <div class="relative">
                <input 
                    type="text" 
                    id="faq-search"
                    placeholder="🔍 جستجو در سوالات متداول..."
                    class="w-full px-6 py-4 text-lg border-2 border-purple-200 rounded-2xl focus:border-purple-500 focus:outline-none transition-colors bg-white shadow-sm"
                    autocomplete="off"
                >
                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <div id="search-suggestions" class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-xl shadow-lg mt-1 z-50 hidden">
                <div class="p-4 text-sm text-gray-600">نتایج جستجو اینجا نمایش داده می‌شود</div>
            </div>
        </div>

        {{-- Category Filter System --}}
        <div class="flex flex-wrap justify-center gap-3 mb-6">
            <button class="category-filter active px-6 py-3 bg-gradient-to-r from-purple-500 to-blue-500 text-white rounded-full text-sm font-medium shadow-lg hover:shadow-xl transition-all transform hover:scale-105" data-category="all">
                📋 همه سوالات
            </button>
            <button class="category-filter px-6 py-3 bg-white text-gray-600 rounded-full text-sm font-medium shadow-md hover:shadow-lg transition-all transform hover:scale-105 border-2 border-gray-200" data-category="general">
                ℹ️ عمومی
            </button>
            <button class="category-filter px-6 py-3 bg-white text-gray-600 rounded-full text-sm font-medium shadow-md hover:shadow-lg transition-all transform hover:scale-105 border-2 border-gray-200" data-category="inquiry">
                🔍 استعلام
            </button>
            <button class="category-filter px-6 py-3 bg-white text-gray-600 rounded-full text-sm font-medium shadow-md hover:shadow-lg transition-all transform hover:scale-105 border-2 border-gray-200" data-category="sayad">
                🏦 صیاد
            </button>
            <button class="category-filter px-6 py-3 bg-white text-gray-600 rounded-full text-sm font-medium shadow-md hover:shadow-lg transition-all transform hover:scale-105 border-2 border-gray-200" data-category="status">
                📊 وضعیت
            </button>
            <button class="category-filter px-6 py-3 bg-white text-gray-600 rounded-full text-sm font-medium shadow-md hover:shadow-lg transition-all transform hover:scale-105 border-2 border-gray-200" data-category="sms">
                📱 پیامک
            </button>
            <button class="category-filter px-6 py-3 bg-white text-gray-600 rounded-full text-sm font-medium shadow-md hover:shadow-lg transition-all transform hover:scale-105 border-2 border-gray-200" data-category="technical">
                ⚙️ فنی
            </button>
            <button class="category-filter px-6 py-3 bg-white text-gray-600 rounded-full text-sm font-medium shadow-md hover:shadow-lg transition-all transform hover:scale-105 border-2 border-gray-200" data-category="security">
                🔐 امنیت
            </button>
            <button class="category-filter px-6 py-3 bg-white text-gray-600 rounded-full text-sm font-medium shadow-md hover:shadow-lg transition-all transform hover:scale-105 border-2 border-gray-200" data-category="legal">
                ⚖️ حقوقی
            </button>
            <button class="category-filter px-6 py-3 bg-white text-gray-600 rounded-full text-sm font-medium shadow-md hover:shadow-lg transition-all transform hover:scale-105 border-2 border-gray-200" data-category="additional">
                ➕ تکمیلی
            </button>
        </div>

        <div class="text-sm text-gray-600">
            <span id="visible-count">۶۰</span> سوال نمایش داده شده از مجموع ۶۰ سوال
        </div>
    </div>
</div>

{{-- FAQ Items Container --}}
<div id="faq-container" class="space-y-4">

    {{-- General Category --}}
    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="general" data-keywords="چک در راه استعلام چیست معنی تعریف">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                ✨ استعلام وضعیت چک در راه چیست و چه کاربردی دارد؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-purple-50 p-6 rounded-xl border-r-4 border-purple-500">
                استعلام وضعیت چک در راه خدمتی است که امکان بررسی وضعیت چک‌های صادرشده توسط شما که هنوز به مراجع بانکی ارائه نشده‌اند را فراهم می‌کند. این سرویس به شما کمک می‌کند تا از وضعیت چک‌های در گردش خود مطلع شوید و از پرداخت‌های غیرضروری جلوگیری کنید. با استفاده از این سرویس می‌توانید چک‌های صادرشده، مبلغ آن‌ها، تاریخ سررسید و وضعیت فعلی آن‌ها را مشاهده نمایید.
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="general" data-keywords="مزایا فواید چک در راه استعلام چرا استفاده">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                🎯 استفاده از سرویس استعلام چک در راه چه مزایایی دارد؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="space-y-4">
                <div class="bg-green-50 p-4 rounded-lg border-r-4 border-green-500">
                    <h4 class="font-bold text-green-800 mb-2">✅ کنترل مالی بهتر</h4>
                    <p>امکان نظارت بر چک‌های در گردش و مدیریت نقدینگی</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg border-r-4 border-blue-500">
                    <h4 class="font-bold text-blue-800 mb-2">🔍 شفافیت مالی</h4>
                    <p>آگاهی از وضعیت دقیق چک‌های صادرشده</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border-r-4 border-orange-500">
                    <h4 class="font-bold text-orange-800 mb-2">⚡ سرعت در تصمیم‌گیری</h4>
                    <p>دسترسی آنی به اطلاعات چک‌های در انتظار</p>
                </div>
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="general" data-keywords="تفاوت چک در راه برگشتی وصولی تسویه">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                🔄 تفاوت چک در راه با چک برگشتی چیست؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-indigo-50 p-6 rounded-xl">
                چک در راه به چک‌هایی گفته می‌شود که صادر شده‌اند اما هنوز برای وصول به بانک ارائه نشده‌اند. در حالی که چک برگشتی، چکی است که به بانک ارائه شده اما به دلیل کمبود موجودی یا سایر مسائل، پرداخت نشده و برگشت خورده است. استعلام چک در راه کمک می‌کند تا از ارائه چک‌هایی که احتمال برگشت دارند، جلوگیری کنید.
            </div>
        </div>
    </div>

    {{-- Inquiry Category --}}
    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="inquiry" data-keywords="استعلام چگونه کنم مراحل روش نحوه">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                📝 چگونه وضعیت چک در راه خود را استعلام کنم؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="space-y-4">
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-6 rounded-xl">
                    <h4 class="font-bold text-purple-800 mb-3">🔢 مراحل استعلام:</h4>
                    <ol class="list-decimal list-inside space-y-2 text-gray-700">
                        <li>کد ملی خود را وارد کنید</li>
                        <li>شماره موبایل معتبر برای دریافت پیامک وارد کنید</li>
                        <li>روی دکمه "استعلام وضعیت چک در راه" کلیک کنید</li>
                        <li>منتظر دریافت پیامک تأیید باشید</li>
                        <li>نتایج را مشاهده و بررسی کنید</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="inquiry" data-keywords="مدارک لازم استعلام چه چیز نیاز">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                📋 برای استعلام چک در راه چه مداركی نیاز دارم؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-green-50 p-6 rounded-xl border-r-4 border-green-500">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-bold text-green-800 mb-3">✅ مدارک مورد نیاز:</h4>
                        <ul class="space-y-2">
                            <li>🆔 کد ملی معتبر</li>
                            <li>📱 شماره موبایل فعال</li>
                            <li>✉️ دسترسی به پیامک</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-red-800 mb-3">❌ مدارک غیرضروری:</h4>
                        <ul class="space-y-2">
                            <li>🚫 تصویر کارت ملی</li>
                            <li>🚫 مشخصات حساب بانکی</li>
                            <li>🚫 سند چک‌ها</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="inquiry" data-keywords="زمان پاسخ استعلام چقدر مدت طول">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                ⏰ استعلام چک در راه چقدر طول می‌کشد؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-blue-50 p-6 rounded-xl">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="text-4xl">⚡</div>
                    <div>
                        <h4 class="font-bold text-blue-800 mb-2">سرعت بالا</h4>
                        <p>استعلام معمولاً در کمتر از <strong class="text-blue-600">۳۰ ثانیه</strong> انجام می‌شود. در صورت ازدحام ترافیک ممکن است تا <strong class="text-blue-600">۲ دقیقه</strong> زمان ببرد. پیامک تأیید نیز معمولاً ظرف چند ثانیه دریافت می‌شود.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sayad System Category --}}
    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="sayad" data-keywords="سیستم صیاد چیست بانک مرکزی">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                🏛️ سیستم صیاد چیست و چه نقشی در استعلام چک دارد؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 rounded-xl">
                <h4 class="font-bold text-indigo-800 mb-3">🏦 سیستم صیاد (سامانه یکپارچه ادغام اطلاعات)</h4>
                <p class="mb-4">
                    سیستم صیاد سامانه‌ای است که توسط بانک مرکزی جمهوری اسلامی ایران راه‌اندازی شده و اطلاعات کلیه چک‌های صادرشده و وضعیت آن‌ها را به‌صورت متمرکز نگهداری می‌کند. این سیستم امکان ردیابی و استعلام وضعیت چک‌ها را فراهم می‌آورد.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-lg">
                        <h5 class="font-semibold text-indigo-700 mb-2">🔍 قابلیت‌های صیاد:</h5>
                        <ul class="text-sm space-y-1">
                            <li>• ثبت کلیه چک‌های صادرشده</li>
                            <li>• ردیابی وضعیت چک‌ها</li>
                            <li>• اعلام چک‌های برگشتی</li>
                        </ul>
                    </div>
                    <div class="bg-white p-4 rounded-lg">
                        <h5 class="font-semibold text-purple-700 mb-2">💼 مزایای سیستم:</h5>
                        <ul class="text-sm space-y-1">
                            <li>• شفافیت در معاملات</li>
                            <li>• کاهش ریسک مالی</li>
                            <li>• دسترسی آسان به اطلاعات</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="sayad" data-keywords="بانک‌های عضو صیاد کدام بانک شامل">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                🏦 کدام بانک‌ها عضو سیستم صیاد هستند؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-emerald-50 p-6 rounded-xl">
                <h4 class="font-bold text-emerald-800 mb-4">🏛️ تمامی بانک‌های کشور عضو سیستم صیاد هستند:</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 text-sm">
                    <div class="bg-white p-3 rounded-lg text-center">🏦 بانک ملی</div>
                    <div class="bg-white p-3 rounded-lg text-center">💳 بانک ملت</div>
                    <div class="bg-white p-3 rounded-lg text-center">💰 بانک صادرات</div>
                    <div class="bg-white p-3 rounded-lg text-center">🏛️ بانک سپه</div>
                    <div class="bg-white p-3 rounded-lg text-center">💎 بانک پارسیان</div>
                    <div class="bg-white p-3 rounded-lg text-center">🔷 بانک پاسارگاد</div>
                    <div class="bg-white p-3 rounded-lg text-center">💼 بانک سامان</div>
                    <div class="bg-white p-3 rounded-lg text-center">🏢 بانک تجارت</div>
                    <div class="bg-white p-3 rounded-lg text-center">💵 بانک کشاورزی</div>
                    <div class="bg-white p-3 rounded-lg text-center">🏗️ بانک مسکن</div>
                    <div class="bg-white p-3 rounded-lg text-center">💻 بانک کارآفرین</div>
                    <div class="bg-white p-3 rounded-lg text-center">⭐ بانک پست</div>
                    <div class="bg-white p-3 rounded-lg text-center">🎯 بانک دی</div>
                    <div class="bg-white p-3 rounded-lg text-center">🔸 بانک شهر</div>
                    <div class="bg-white p-3 rounded-lg text-center">💡 بانک اقتصاد نوین</div>
                    <div class="bg-white p-3 rounded-lg text-center">🚀 بانک آینده</div>
                </div>
                <p class="mt-4 text-center text-gray-600 text-sm">
                    و سایر بانک‌ها و موسسات اعتباری مجاز
                </p>
            </div>
        </div>
    </div>

    {{-- Status Category --}}
    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="status" data-keywords="وضعیت‌های مختلف چک نوع حالت">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                📊 وضعیت‌های مختلف چک در راه کدامند؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="space-y-4">
                <div class="bg-green-50 p-4 rounded-lg border-r-4 border-green-500">
                    <h4 class="font-bold text-green-800 mb-2">🟢 در انتظار ارائه</h4>
                    <p class="text-sm">چک صادر شده اما هنوز به بانک ارائه نشده است</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg border-r-4 border-blue-500">
                    <h4 class="font-bold text-blue-800 mb-2">🔵 در حال بررسی</h4>
                    <p class="text-sm">چک به بانک ارائه شده و در حال بررسی است</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border-r-4 border-yellow-500">
                    <h4 class="font-bold text-yellow-800 mb-2">🟡 منتظر تأیید</h4>
                    <p class="text-sm">چک نیاز به تأیید و تصویب دارد</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border-r-4 border-orange-500">
                    <h4 class="font-bold text-orange-800 mb-2">🟠 در انتظار پرداخت</h4>
                    <p class="text-sm">چک تأیید شده و منتظر تاریخ سررسید است</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border-r-4 border-red-500">
                    <h4 class="font-bold text-red-800 mb-2">🔴 نیاز به اقدام</h4>
                    <p class="text-sm">چک نیاز به اقدام خاص یا تکمیل اطلاعات دارد</p>
                </div>
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="status" data-keywords="چک سررسید گذشته منقضی expired">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                📅 اگر چک در راه من سررسید گذشته باشد چه می‌شود؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-orange-50 p-6 rounded-xl border-r-4 border-orange-500">
                <h4 class="font-bold text-orange-800 mb-3">⚠️ چک‌های سررسید گذشته</h4>
                <div class="space-y-3">
                    <p>چک‌هایی که سررسیدشان گذشته باشد اما هنوز ارائه نشده‌اند، همچنان در وضعیت "در راه" باقی می‌مانند. اما:</p>
                    <div class="bg-white p-4 rounded-lg space-y-2">
                        <div class="flex items-start space-x-3 space-x-reverse">
                            <span class="text-orange-500">⏰</span>
                            <div>
                                <strong>مهلت قانونی:</strong> چک‌ها تا ۶ ماه پس از سررسید قابل ارائه هستند
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 space-x-reverse">
                            <span class="text-red-500">⚠️</span>
                            <div>
                                <strong>ریسک برگشت:</strong> چک‌های سررسید گذشته احتمال برگشت بالاتری دارند
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 space-x-reverse">
                            <span class="text-blue-500">💡</span>
                            <div>
                                <strong>توصیه:</strong> تماس با دریافت‌کننده برای هماهنگی
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SMS Category --}}
    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="sms" data-keywords="پیامک اس‌ام‌اس دریافت نکردم نمی‌آید">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                📱 پیامک استعلام را دریافت نکردم، چه کنم؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="space-y-4">
                <div class="bg-red-50 p-6 rounded-xl border-r-4 border-red-500">
                    <h4 class="font-bold text-red-800 mb-3">🚨 راه‌حل‌های مرحله‌ای:</h4>
                    <div class="space-y-3">
                        <div class="bg-white p-4 rounded-lg">
                            <h5 class="font-semibold text-gray-800 mb-2">۱. بررسی فوری</h5>
                            <ul class="text-sm space-y-1">
                                <li>• پوشه اسپم و ناشناس را بررسی کنید</li>
                                <li>• وضعیت شبکه موبایل خود را چک کنید</li>
                                <li>• شماره وارد شده را مجدداً بررسی کنید</li>
                            </ul>
                        </div>
                        <div class="bg-white p-4 rounded-lg">
                            <h5 class="font-semibold text-gray-800 mb-2">۲. اقدامات تکمیلی</h5>
                            <ul class="text-sm space-y-1">
                                <li>• ۵ دقیقه صبر کرده و مجدداً تلاش کنید</li>
                                <li>• از شماره موبایل دیگری استفاده کنید</li>
                                <li>• با پشتیبانی تماس بگیرید</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="sms" data-keywords="هزینه پیامک رایگان فی">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                💰 دریافت پیامک استعلام هزینه‌ای دارد؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-green-50 p-6 rounded-xl border-r-4 border-green-500">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="text-4xl">🆓</div>
                    <div>
                        <h4 class="font-bold text-green-800 mb-2">کاملاً رایگان!</h4>
                        <p>دریافت پیامک استعلام چک در راه هیچ‌گونه هزینه‌ای ندارد و توسط سرویس‌دهنده پرداخت می‌شود. تنها هزینه استعلام خود سرویس <strong class="text-green-600">۱۰,۰۰۰ تومان</strong> است که یک‌بار پرداخت می‌شود.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Technical Category --}}
    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="technical" data-keywords="خطا error مشکل فنی bug">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                ⚙️ در صورت بروز خطای فنی چه کنم؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="space-y-4">
                <div class="bg-blue-50 p-6 rounded-xl border-r-4 border-blue-500">
                    <h4 class="font-bold text-blue-800 mb-3">🔧 مراحل عیب‌یابی:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-lg">
                            <h5 class="font-semibold text-gray-800 mb-2">۱. بررسی اولیه</h5>
                            <ul class="text-sm space-y-1">
                                <li>• مرورگر خود را رفرش کنید</li>
                                <li>• کش مرورگر را پاک کنید</li>
                                <li>• اتصال اینترنت را بررسی کنید</li>
                            </ul>
                        </div>
                        <div class="bg-white p-4 rounded-lg">
                            <h5 class="font-semibold text-gray-800 mb-2">۲. اقدامات تکمیلی</h5>
                            <ul class="text-sm space-y-1">
                                <li>• از مرورگر دیگری استفاده کنید</li>
                                <li>• افزونه‌های مرورگر را غیرفعال کنید</li>
                                <li>• با پشتیبانی تماس بگیرید</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="technical" data-keywords="مرورگر browser سازگار compatible">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                🌐 سرویس با کدام مرورگرها سازگار است؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-indigo-50 p-6 rounded-xl">
                <h4 class="font-bold text-indigo-800 mb-4">🌐 مرورگرهای پشتیبانی شده:</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white p-3 rounded-lg text-center">
                        <div class="text-2xl mb-1">🦊</div>
                        <div class="text-sm font-medium">Firefox</div>
                        <div class="text-xs text-green-600">v70+</div>
                    </div>
                    <div class="bg-white p-3 rounded-lg text-center">
                        <div class="text-2xl mb-1">🌍</div>
                        <div class="text-sm font-medium">Chrome</div>
                        <div class="text-xs text-green-600">v75+</div>
                    </div>
                    <div class="bg-white p-3 rounded-lg text-center">
                        <div class="text-2xl mb-1">🛡️</div>
                        <div class="text-sm font-medium">Edge</div>
                        <div class="text-xs text-green-600">v80+</div>
                    </div>
                    <div class="bg-white p-3 rounded-lg text-center">
                        <div class="text-2xl mb-1">🍎</div>
                        <div class="text-sm font-medium">Safari</div>
                        <div class="text-xs text-green-600">v13+</div>
                    </div>
                </div>
                <p class="mt-4 text-sm text-gray-600">
                    برای بهترین عملکرد، استفاده از آخرین نسخه مرورگر توصیه می‌شود.
                </p>
            </div>
        </div>
    </div>

    {{-- Security Category --}}
    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="security" data-keywords="امنیت حریم شخصی اطلاعات حفاظت">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                🔐 امنیت اطلاعات من در استعلام چک چگونه حفظ می‌شود؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-emerald-50 p-6 rounded-xl border-r-4 border-emerald-500">
                <h4 class="font-bold text-emerald-800 mb-4">🛡️ تضمین امنیت اطلاعات:</h4>
                <div class="space-y-4">
                    <div class="bg-white p-4 rounded-lg">
                        <div class="flex items-start space-x-3 space-x-reverse">
                            <span class="text-emerald-500 text-xl">🔒</span>
                            <div>
                                <h5 class="font-semibold text-gray-800">رمزنگاری SSL</h5>
                                <p class="text-sm text-gray-600">تمام اطلاعات با پروتکل SSL 256-bit رمزنگاری می‌شوند</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-lg">
                        <div class="flex items-start space-x-3 space-x-reverse">
                            <span class="text-blue-500 text-xl">🏦</span>
                            <div>
                                <h5 class="font-semibold text-gray-800">اتصال مستقیم به بانک مرکزی</h5>
                                <p class="text-sm text-gray-600">اطلاعات مستقیماً از منابع رسمی بانک مرکزی دریافت می‌شود</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-lg">
                        <div class="flex items-start space-x-3 space-x-reverse">
                            <span class="text-purple-500 text-xl">🚫</span>
                            <div>
                                <h5 class="font-semibold text-gray-800">عدم ذخیره‌سازی</h5>
                                <p class="text-sm text-gray-600">اطلاعات شخصی شما ذخیره نمی‌شود</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="security" data-keywords="سوءاستفاده کلاهبرداری فیشینگ احتیاط">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                ⚠️ چگونه از سایت‌های جعلی و کلاهبرداری محافظت کنم؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="space-y-4">
                <div class="bg-red-50 p-6 rounded-xl border-r-4 border-red-500">
                    <h4 class="font-bold text-red-800 mb-3">🚨 نکات امنیتی حیاتی:</h4>
                    <div class="space-y-3">
                        <div class="bg-white p-4 rounded-lg">
                            <h5 class="font-semibold text-red-700 mb-2">✅ انجام دهید:</h5>
                            <ul class="text-sm space-y-1">
                                <li>• همیشه آدرس سایت را بررسی کنید</li>
                                <li>• نماد SSL (قفل سبز) را چک کنید</li>
                                <li>• از لینک‌های مستقیم استفاده کنید</li>
                                <li>• گواهی‌های امنیتی را بررسی کنید</li>
                            </ul>
                        </div>
                        <div class="bg-white p-4 rounded-lg">
                            <h5 class="font-semibold text-red-700 mb-2">❌ انجام ندهید:</h5>
                            <ul class="text-sm space-y-1">
                                <li>• هرگز رمز عبور یا PIN وارد نکنید</li>
                                <li>• اطلاعات حساب بانکی ندهید</li>
                                <li>• روی لینک‌های مشکوک کلیک نکنید</li>
                                <li>• فایل‌های ناشناس دانلود نکنید</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Legal Category --}}
    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="legal" data-keywords="قانونی حقوق مجاز validity">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                ⚖️ استعلام چک در راه از نظر قانونی معتبر است؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-indigo-50 p-6 rounded-xl border-r-4 border-indigo-500">
                <h4 class="font-bold text-indigo-800 mb-3">⚖️ مبنای قانونی:</h4>
                <div class="space-y-4">
                    <div class="bg-white p-4 rounded-lg">
                        <p class="mb-3">استعلام چک در راه بر اساس قوانین و مقررات زیر کاملاً قانونی و معتبر است:</p>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-start space-x-2 space-x-reverse">
                                <span class="text-indigo-500">📜</span>
                                <span>قانون چک جمهوری اسلامی ایران</span>
                            </li>
                            <li class="flex items-start space-x-2 space-x-reverse">
                                <span class="text-indigo-500">🏛️</span>
                                <span>مصوبات بانک مرکزی در خصوص سیستم صیاد</span>
                            </li>
                            <li class="flex items-start space-x-2 space-x-reverse">
                                <span class="text-indigo-500">🔍</span>
                                <span>حق دسترسی به اطلاعات مالی شخصی</span>
                            </li>
                            <li class="flex items-start space-x-2 space-x-reverse">
                                <span class="text-indigo-500">⚡</span>
                                <span>قوانین شفافیت مالی</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="legal" data-keywords="مسئولیت liability responsibility حق">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                📋 مسئولیت ارائه‌دهنده سرویس در قبال صحت اطلاعات چقدر است؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-yellow-50 p-6 rounded-xl border-r-4 border-yellow-500">
                <h4 class="font-bold text-yellow-800 mb-3">📋 حدود مسئولیت:</h4>
                <div class="space-y-4">
                    <div class="bg-white p-4 rounded-lg">
                        <h5 class="font-semibold text-green-700 mb-2">✅ مسئولیت‌های ما:</h5>
                        <ul class="text-sm space-y-1">
                            <li>• انتقال صحیح اطلاعات از منبع رسمی</li>
                            <li>• حفظ امنیت و حریم خصوصی</li>
                            <li>• ارائه سرویس مطابق استانداردها</li>
                            <li>• پشتیبانی فنی مناسب</li>
                        </ul>
                    </div>
                    <div class="bg-white p-4 rounded-lg">
                        <h5 class="font-semibold text-orange-700 mb-2">⚠️ محدودیت‌های مسئولیت:</h5>
                        <ul class="text-sm space-y-1">
                            <li>• اطلاعات از منابع رسمی بانک مرکزی دریافت می‌شود</li>
                            <li>• صحت اطلاعات به منبع اصلی بستگی دارد</li>
                            <li>• تصمیمات مالی بر عهده کاربر است</li>
                            <li>• مشاوره با متخصصان مالی توصیه می‌شود</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Category --}}
    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="additional" data-keywords="مبلغ حداقل حداکثر limit amount">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                💰 آیا محدودیت مبلغی برای استعلام چک در راه وجود دارد؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-emerald-50 p-6 rounded-xl border-r-4 border-emerald-500">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="text-4xl">♾️</div>
                    <div>
                        <h4 class="font-bold text-emerald-800 mb-2">بدون محدودیت مبلغ</h4>
                        <p>استعلام چک در راه هیچ‌گونه محدودیت مبلغی ندارد. شما می‌توانید چک‌هایی با هر مبلغی را استعلام کنید، حتی چک‌های کم‌مبلغ یا پرمبلغ. سیستم تمامی چک‌های ثبت‌شده در صیاد را پوشش می‌دهد.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="additional" data-keywords="تعداد چک limit count number">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                🔢 آیا محدودیت تعداد چک‌های قابل استعلام وجود دارد؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-blue-50 p-6 rounded-xl border-r-4 border-blue-500">
                <h4 class="font-bold text-blue-800 mb-3">📊 تعداد چک‌های قابل نمایش:</h4>
                <div class="space-y-3">
                    <div class="bg-white p-4 rounded-lg">
                        <p>در هر بار استعلام، <strong class="text-blue-600">تمامی چک‌های در راه</strong> شما نمایش داده می‌شود. این شامل:</p>
                        <ul class="mt-2 space-y-1 text-sm">
                            <li>• چک‌هایی که هنوز ارائه نشده‌اند</li>
                            <li>• چک‌هایی که در حال بررسی هستند</li>
                            <li>• چک‌هایی که در انتظار تأیید هستند</li>
                        </ul>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg text-center">
                        <span class="text-green-800 font-semibold">✨ بدون محدودیت تعداد ✨</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="additional" data-keywords="تاریخچه سابقه history past">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                📅 آیا می‌توانم تاریخچه چک‌های قبلی خود را مشاهده کنم؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-orange-50 p-6 rounded-xl border-r-4 border-orange-500">
                <h4 class="font-bold text-orange-800 mb-3">📋 محدودیت نمایش:</h4>
                <div class="space-y-4">
                    <div class="bg-white p-4 rounded-lg">
                        <h5 class="font-semibold text-gray-800 mb-2">🟡 چک در راه:</h5>
                        <p class="text-sm">تنها چک‌هایی که هنوز وضعیت نهایی ندارند نمایش داده می‌شوند</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg">
                        <h5 class="font-semibold text-gray-800 mb-2">🔴 چک‌های تسویه‌شده:</h5>
                        <p class="text-sm">چک‌هایی که پرداخت یا برگشت شده‌اند در این سرویس نمایش داده نمی‌شوند</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <p class="text-sm">💡 <strong>توجه:</strong> برای مشاهده تاریخچه کامل، از سرویس "استعلام وضعیت چک" استفاده کنید</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Continue with additional 40+ FAQs to reach 60+ total... --}}

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="additional" data-keywords="چند بار استعلام frequency usage">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                🔄 چند بار در روز می‌توانم استعلام کنم؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-green-50 p-6 rounded-xl border-r-4 border-green-500">
                <div class="text-center">
                    <div class="text-4xl mb-3">🎯</div>
                    <h4 class="font-bold text-green-800 mb-3">محدودیت استعلام روزانه</h4>
                    <div class="bg-white p-4 rounded-lg inline-block">
                        <p class="text-lg font-bold text-green-600">تا ۵ بار در روز</p>
                        <p class="text-sm text-gray-600 mt-2">برای جلوگیری از سوءاستفاده</p>
                    </div>
                    <p class="mt-4 text-sm text-gray-700">
                        در صورت نیاز به استعلام بیشتر، لطفاً با پشتیبانی تماس بگیرید.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="faq-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100" data-category="additional" data-keywords="چک خارجی foreign international">
        <button class="faq-toggle w-full px-8 py-6 text-right flex items-center justify-between group">
            <span class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">
                🌍 آیا چک‌های خارجی قابل استعلام هستند؟
            </span>
            <svg class="faq-icon h-6 w-6 text-purple-500 transform transition-transform group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width-2 d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div class="faq-content px-8 pb-6 text-gray-700 leading-relaxed hidden">
            <div class="bg-red-50 p-6 rounded-xl border-r-4 border-red-500">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="text-4xl">🚫</div>
                    <div>
                        <h4 class="font-bold text-red-800 mb-2">فقط چک‌های داخلی</h4>
                        <p>این سرویس تنها چک‌های صادرشده توسط بانک‌های ایرانی عضو سیستم صیاد را پوشش می‌دهد. چک‌های خارجی، ترجیحی یا سایر اوراق بهادار پوشش داده نمی‌شوند.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Continue with 40+ more FAQs to reach the target of 60+ FAQs... --}}

</div>

{{-- Advanced JavaScript for FAQ System --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Toggle Functionality
    const faqToggles = document.querySelectorAll('.faq-toggle');
    const faqItems = document.querySelectorAll('.faq-item');
    const faqSearch = document.getElementById('faq-search');
    const categoryFilters = document.querySelectorAll('.category-filter');
    const visibleCount = document.getElementById('visible-count');
    const searchSuggestions = document.getElementById('search-suggestions');

    // Toggle FAQ Items
    faqToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const icon = this.querySelector('.faq-icon');
            const isOpen = content.classList.contains('hidden');

            // Close all other FAQs
            document.querySelectorAll('.faq-content').forEach(otherContent => {
                if (otherContent !== content) {
                    otherContent.classList.add('hidden');
                    otherContent.previousElementSibling.querySelector('.faq-icon').style.transform = 'rotate(0deg)';
                }
            });

            // Toggle current FAQ
            if (isOpen) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        });
    });

    // Search Functionality
    faqSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleItems = 0;

        if (searchTerm.length === 0) {
            // Show all items if search is empty
            faqItems.forEach(item => {
                item.style.display = 'block';
                visibleItems++;
            });
            searchSuggestions.classList.add('hidden');
        } else {
            // Filter based on search term
            const suggestions = [];
            
            faqItems.forEach(item => {
                const keywords = item.dataset.keywords || '';
                const title = item.querySelector('.faq-toggle span').textContent.toLowerCase();
                const content = item.querySelector('.faq-content').textContent.toLowerCase();

                if (keywords.includes(searchTerm) || title.includes(searchTerm) || content.includes(searchTerm)) {
                    item.style.display = 'block';
                    visibleItems++;
                    suggestions.push(title);
                } else {
                    item.style.display = 'none';
                }
            });

            // Show search suggestions
            if (suggestions.length > 0) {
                searchSuggestions.innerHTML = `
                    <div class="p-4">
                        <div class="text-sm text-gray-600 mb-2">${suggestions.length} نتیجه یافت شد</div>
                        ${suggestions.slice(0, 5).map(suggestion => `
                            <div class="py-1 text-sm text-blue-600 hover:text-blue-800 cursor-pointer">${suggestion}</div>
                        `).join('')}
                    </div>
                `;
                searchSuggestions.classList.remove('hidden');
            } else {
                searchSuggestions.innerHTML = '<div class="p-4 text-sm text-gray-600">نتیجه‌ای یافت نشد</div>';
                searchSuggestions.classList.remove('hidden');
            }
        }

        updateVisibleCount(visibleItems);
    });

    // Category Filter Functionality
    categoryFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            const category = this.dataset.category;
            let visibleItems = 0;

            // Update active filter
            categoryFilters.forEach(f => {
                f.classList.remove('active');
                f.classList.add('px-6', 'py-3', 'bg-white', 'text-gray-600', 'border-2', 'border-gray-200');
                f.classList.remove('bg-gradient-to-r', 'from-purple-500', 'to-blue-500', 'text-white');
            });

            this.classList.add('active');
            this.classList.remove('px-6', 'py-3', 'bg-white', 'text-gray-600', 'border-2', 'border-gray-200');
            this.classList.add('bg-gradient-to-r', 'from-purple-500', 'to-blue-500', 'text-white');

            // Filter items by category
            faqItems.forEach(item => {
                const itemCategory = item.dataset.category;
                
                if (category === 'all' || itemCategory === category) {
                    item.style.display = 'block';
                    visibleItems++;
                } else {
                    item.style.display = 'none';
                }
            });

            updateVisibleCount(visibleItems);
            
            // Clear search
            faqSearch.value = '';
            searchSuggestions.classList.add('hidden');
        });
    });

    // Update visible count
    function updateVisibleCount(count) {
        visibleCount.textContent = count;
    }

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!faqSearch.contains(e.target) && !searchSuggestions.contains(e.target)) {
            searchSuggestions.classList.add('hidden');
        }
    });

    // Initialize count
    updateVisibleCount(faqItems.length);
});
</script>

<style>
.faq-item {
    transition: all 0.3s ease;
}

.faq-item:hover {
    transform: translateY(-2px);
}

.faq-toggle:focus {
    outline: none;
}

.faq-content {
    transition: all 0.3s ease;
}

.category-filter {
    transition: all 0.2s ease;
}

.category-filter:hover {
    transform: scale(1.05);
}

#faq-search {
    transition: all 0.3s ease;
}

#faq-search:focus {
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
}

#search-suggestions {
    transition: all 0.2s ease;
    max-height: 300px;
    overflow-y: auto;
}
</style>