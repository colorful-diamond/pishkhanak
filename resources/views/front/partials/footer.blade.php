<!-- Footer 2: Centered Layout -->
<footer dir="rtl" class="bg-sky-50 mt-8 w-full mb-14 md:mb-0">
    <div class="container mx-auto max-w-screen-lg px-4 w-full">
        <!-- Desktop Footer -->
        <section class="hidden md:block py-16">
            <!-- Logo and Company Info - Centered -->
            <div class="text-center mb-12">
                <img src="{{ asset('assets/images/logo.png') }}" alt="پیشخوانک" class="w-32 mx-auto mb-6 rounded-lg">
                <p class="text-gray-600 max-w-2xl mx-auto mb-6 leading-relaxed">
                    راهکاری جامع برای دسترسی آنلاین به انواع خدمات استعلامی و مالی. 
                    از استعلام وضعیت چک تا خدمات مربوط به خودرو و سایر نیازهای روزمره، 
                    پیشخوانک فرآیند دریافت این خدمات را برای شما ساده و سریع می‌کند.
                </p>
                <div class="flex justify-center gap-4">
                    <a href="#" class="bg-white rounded-lg p-3 hover:bg-sky-50 transition-colors shadow-sm">
                        <img src="{{ asset('assets/images/enamad.svg') }}" alt="اینماد" class="w-14 h-14 object-contain">
                    </a>
                    <a href="#" class="bg-white rounded-lg p-3 hover:bg-sky-50 transition-colors shadow-sm">
                        <img src="{{ asset('assets/images/ecunion.svg') }}" alt="اتحادیه" class="w-14 h-14 object-contain">
                    </a>
                </div>
            </div>

            <!-- Services Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
                <!-- Banking Services -->
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-sky-900 mb-4">خدمات بانکی</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('services.show', ['slug1' => 'card-iban']) }}" class="text-gray-600 hover:text-sky-600 transition-colors">کارت به شبا</a></li>
                        <li><a href="{{ route('services.show', ['slug1' => 'card-account']) }}" class="text-gray-600 hover:text-sky-600 transition-colors">کارت به حساب</a></li>
                        <li><a href="{{ route('services.show', ['slug1' => 'iban-account']) }}" class="text-gray-600 hover:text-sky-600 transition-colors">شبا به حساب</a></li>
                        <li><a href="{{ route('services.show', ['slug1' => 'account-iban']) }}" class="text-gray-600 hover:text-sky-600 transition-colors">حساب به شبا</a></li>
                        <li><a href="{{ route('services.show', ['slug1' => 'iban-check']) }}" class="text-gray-600 hover:text-sky-600 transition-colors">بررسی شبا</a></li>
                    </ul>
                </div>

                <!-- Check Services -->
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-sky-900 mb-4">خدمات چک</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام چک برگشتی</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام مکنا</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">وضعیت رنگ چک</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">اعتبارسنجی بانکی</a></li>
                    </ul>
                </div>

                <!-- Vehicle Services -->
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-sky-900 mb-4">خدمات خودرو</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام خلافی</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام بیمه</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام معاینه فنی</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام پلاک</a></li>
                    </ul>
                </div>

                <!-- Other Services -->
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-sky-900 mb-4">سایر خدمات</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام کدپستی</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام کد ملی</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام نظام وظیفه</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام تلفن</a></li>
                    </ul>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="bg-white rounded-lg p-8 shadow-sm text-center">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h4 class="font-bold text-sky-900 mb-2">تماس با ما</h4>
                        <p class="text-gray-600 text-sm">{{ \App\Helpers\SettingsHelper::getPhone() }}</p>
                        <p class="text-gray-600 text-sm">{{ \App\Helpers\SettingsHelper::getMobile() }}</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-sky-900 mb-2">آدرس</h4>
                        <p class="text-gray-600 text-sm">مشهد، خیابان جلال آل احمد</p>
                        <p class="text-gray-600 text-sm">جلال آل احمد۱۰، پلاک ۵۴۳</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-sky-900 mb-2">دسترسی سریع</h4>
                        <div class="flex justify-center gap-4 text-sm">
                            <a href="{{ route('app.page.about') }}" class="text-gray-600 hover:text-sky-600 transition-colors">درباره ما</a>
                            <a href="{{ route('app.page.contact') }}" class="text-gray-600 hover:text-sky-600 transition-colors">تماس</a>
                            <a href="{{ route('app.blog.index') }}" class="text-gray-600 hover:text-sky-600 transition-colors">وبلاگ</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-200 mt-8 pt-6 text-center">
                <div class="flex justify-center gap-6 text-sm mb-4">
                    <a href="{{ route('app.page.privacy') }}" class="text-gray-600 hover:text-sky-600 transition-colors">حریم خصوصی</a>
                    <a href="{{ route('app.page.terms') }}" class="text-gray-600 hover:text-sky-600 transition-colors">قوانین و مقررات</a>
                </div>
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} {{ \App\Helpers\SettingsHelper::getLegalName() }} - کلیه حقوق محفوظ است
                </p>
            </div>
        </section>

        <!-- Mobile Footer -->
        <section class="md:hidden py-8">
            <!-- Mobile Logo -->
            <div class="text-center mb-6">
                <img src="{{ asset('assets/images/logo.png') }}" alt="پیشخوانک" class="w-24 mx-auto mb-4 rounded-lg">
                <h2 class="text-lg font-bold text-sky-900 mb-2">پیشخوانک</h2>
                <p class="text-sm text-gray-600 mb-4">خدمات استعلامی آنلاین</p>
                <div class="flex justify-center gap-3">
                    <a href="#" class="bg-white rounded-lg p-2 hover:bg-sky-50 transition-colors shadow-sm">
                        <img src="{{ asset('assets/images/enamad.svg') }}" alt="اینماد" class="w-12 h-12 object-contain">
                    </a>
                    <a href="#" class="bg-white rounded-lg p-2 hover:bg-sky-50 transition-colors shadow-sm">
                        <img src="{{ asset('assets/images/ecunion.svg') }}" alt="اتحادیه" class="w-12 h-12 object-contain">
                    </a>
                </div>
            </div>

            <!-- Mobile Services Grid -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <!-- Banking Services -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h3 class="font-bold text-sky-900 mb-3 text-sm">خدمات بانکی</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('services.show', ['slug1' => 'card-iban']) }}" class="text-gray-600 hover:text-sky-600 transition-colors">کارت به شبا</a></li>
                        <li><a href="{{ route('services.show', ['slug1' => 'card-account']) }}" class="text-gray-600 hover:text-sky-600 transition-colors">کارت به حساب</a></li>
                        <li><a href="{{ route('services.show', ['slug1' => 'iban-account']) }}" class="text-gray-600 hover:text-sky-600 transition-colors">شبا به حساب</a></li>
                        <li><a href="{{ route('services.show', ['slug1' => 'account-iban']) }}" class="text-gray-600 hover:text-sky-600 transition-colors">حساب به شبا</a></li>
                    </ul>
                </div>

                <!-- Check Services -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h3 class="font-bold text-sky-900 mb-3 text-sm">خدمات چک</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام چک برگشتی</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام مکنا</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">وضعیت رنگ چک</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">اعتبارسنجی بانکی</a></li>
                    </ul>
                </div>

                <!-- Vehicle Services -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h3 class="font-bold text-sky-900 mb-3 text-sm">خدمات خودرو</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام خلافی</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام بیمه</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام معاینه فنی</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام پلاک</a></li>
                    </ul>
                </div>

                <!-- Other Services -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h3 class="font-bold text-sky-900 mb-3 text-sm">سایر خدمات</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام کدپستی</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام کد ملی</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام نظام وظیفه</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-sky-600 transition-colors">استعلام تلفن</a></li>
                    </ul>
                </div>
            </div>

            <!-- Mobile Contact -->
            <div class="bg-white rounded-lg p-4 text-center">
                <div>
                    <h4 class="font-bold text-sky-900 mb-2">تماس با ما</h4>
                    <p class="text-gray-600 text-sm">{{ \App\Helpers\SettingsHelper::getPhone() }}</p>
                    <p class="text-gray-600 text-sm">{{ \App\Helpers\SettingsHelper::getMobile() }}</p>
                </div>
                <div class="text-sm text-gray-600 mb-4">
                    مشهد، خیابان جلال آل احمد
                </div>
                <div class="flex justify-center gap-4 text-xs mb-3">
                    <a href="{{ route('app.page.privacy') }}" class="text-gray-600">حریم خصوصی</a>
                    <span class="text-gray-400">|</span>
                    <a href="{{ route('app.page.terms') }}" class="text-gray-600">قوانین</a>
                </div>
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} {{ \App\Helpers\SettingsHelper::getLegalName() }}
                </p>
            </div>
        </section>
    </div>
</footer>