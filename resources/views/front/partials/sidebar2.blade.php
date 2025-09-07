{{-- Enhanced Sidebar 2 - List-Based Layout with Chat Integration --}}
<!-- Mobile Sidebar Backdrop -->
<div id="mobileSideMenuBackdrop" class="fixed inset-0 bg-black/60 backdrop-blur-sm opacity-0 invisible transition-all duration-300 ease-in-out z-40"></div>

<!-- Mobile Side Menu -->
<div id="mobileSideMenu" class="fixed inset-y-0 right-0 w-80 max-w-[85vw] bg-white shadow-2xl transform translate-x-full transition-all duration-500 ease-out z-50 overflow-hidden">
    <!-- Content Container -->
    <div class="relative h-full flex flex-col">
        <!-- Header Section with Chat Toggle -->
        <div class="flex justify-between items-center p-4 bg-sky-600 text-white">
            <button class="closeMobileMenu p-2 rounded-lg text-white hover:bg-sky-700 transition-colors">
                <x-tabler-x class="w-5 h-5" />
            </button>
            <div class="flex items-center space-x-2 space-x-reverse">
                <img src="{{ asset('assets/images/logo.png') }}" alt="پیشخوانک" class="w-20 h-6 brightness-0 invert">
                <button id="chatToggle" class="p-2 rounded-lg text-white hover:bg-sky-700 transition-colors" title="چت با دستیار">
                    <x-tabler-message-circle class="w-5 h-5" />
                </button>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="flex border-b border-gray-200 bg-sky-50">
            <button class="tab-btn flex-1 p-3 text-sm font-medium text-gray-600 hover:text-sky-600 border-b-2 border-transparent hover:border-sky-300 transition-colors active" data-tab="services">
                <x-tabler-apps class="w-4 h-4 mx-auto mb-1" />
                خدمات
            </button>
            <button class="tab-btn flex-1 p-3 text-sm font-medium text-gray-600 hover:text-sky-600 border-b-2 border-transparent hover:border-sky-300 transition-colors" data-tab="chat">
                <x-tabler-message-circle class="w-4 h-4 mx-auto mb-1" />
                چت
            </button>
        </div>

        <!-- Services Tab Content -->
        <div id="services-tab" class="tab-content flex-1 overflow-y-auto">
            <!-- Main Navigation -->
            <div class="p-4 border-b border-gray-200">
                <nav class="space-y-1">
                    <a href="{{ route('app.page.home') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg {{ Request::is('/') ? 'bg-sky-50 text-sky-700' : 'text-gray-700 hover:bg-sky-50' }} transition-colors">
                        <x-tabler-home class="w-5 h-5" />
                        <span class="text-sm {{ Request::is('/') ? 'font-medium' : '' }}">خانه</span>
                    </a>
                    
                    <a href="{{ route('app.blog.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg {{ Request::is('blog*') ? 'bg-sky-50 text-sky-700' : 'text-gray-700 hover:bg-sky-50' }} transition-colors">
                        <x-tabler-article class="w-5 h-5" />
                        <span class="text-sm {{ Request::is('blog*') ? 'font-medium' : '' }}">بلاگ</span>
                    </a>
                    
                    <a href="{{ route('app.page.about') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg {{ Request::is('about*') ? 'bg-sky-50 text-sky-700' : 'text-gray-700 hover:bg-sky-50' }} transition-colors">
                        <x-tabler-info-circle class="w-5 h-5" />
                        <span class="text-sm {{ Request::is('about*') ? 'font-medium' : '' }}">درباره ما</span>
                    </a>
                    
                    <a href="{{ route('app.page.contact') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg {{ Request::is('contact*') ? 'bg-sky-50 text-sky-700' : 'text-gray-700 hover:bg-sky-50' }} transition-colors">
                        <x-tabler-mail class="w-5 h-5" />
                        <span class="text-sm {{ Request::is('contact*') ? 'font-medium' : '' }}">ارتباط با ما</span>
                    </a>
                </nav>
            </div>

            <!-- Expandable Services Section -->
            <div class="p-4" id="services-section">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">خدمات</h3>
                
                <!-- Banking Services Accordion -->
                <div class="border border-gray-200 rounded-lg mb-3">
                    <button class="w-full flex items-center justify-between p-4 text-right hover:bg-sky-50 transition-colors" 
                            onclick="toggleAccordion('banking-services')">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center">
                                <x-tabler-building-bank class="w-5 h-5 text-sky-600" />
                            </div>
                            <span class="font-medium text-gray-800">خدمات بانکی</span>
                        </div>
                        <x-tabler-chevron-down class="w-5 h-5 text-gray-500 transition-transform duration-200" id="banking-services-icon" />
                    </button>
                    <div id="banking-services" class="hidden border-t border-gray-200 bg-sky-50">
                        <div class="p-4 space-y-2">
                            <a href="{{ route('services.show', ['slug1' => 'card-iban']) }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-credit-card class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">تبدیل کارت به شبا</span>
                            </a>
                            <a href="{{ route('services.show', ['slug1' => 'iban-account']) }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-exchange class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">شبا به حساب</span>
                            </a>
                            <a href="{{ route('services.show', ['slug1' => 'iban-check']) }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-check class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">بررسی شبا</span>
                            </a>
                            <a href="{{ route('services.show', ['slug1' => 'credit-score-rating']) }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-star class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">اعتبارسنجی</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Services Accordion -->
                <div class="border border-gray-200 rounded-lg mb-3">
                    <button class="w-full flex items-center justify-between p-4 text-right hover:bg-sky-50 transition-colors" 
                            onclick="toggleAccordion('vehicle-services')">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <x-tabler-car class="w-5 h-5 text-green-600" />
                            </div>
                            <span class="font-medium text-gray-800">خدمات خودرو</span>
                        </div>
                        <x-tabler-chevron-down class="w-5 h-5 text-gray-500 transition-transform duration-200" id="vehicle-services-icon" />
                    </button>
                    <div id="vehicle-services" class="hidden border-t border-gray-200 bg-sky-50">
                        <div class="p-4 space-y-2">
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-alert-triangle class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">خلافی خودرو</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-bike class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">خلافی موتور</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-license class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">پلاک فعال</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-shield class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">بیمه شخص ثالث</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Personal Services Accordion -->
                <div class="border border-gray-200 rounded-lg mb-3">
                    <button class="w-full flex items-center justify-between p-4 text-right hover:bg-sky-50 transition-colors" 
                            onclick="toggleAccordion('personal-services')">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <x-tabler-user-check class="w-5 h-5 text-purple-600" />
                            </div>
                            <span class="font-medium text-gray-800">خدمات اشخاص</span>
                        </div>
                        <x-tabler-chevron-down class="w-5 h-5 text-gray-500 transition-transform duration-200" id="personal-services-icon" />
                    </button>
                    <div id="personal-services" class="hidden border-t border-gray-200 bg-sky-50">
                        <div class="p-4 space-y-2">
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-heart class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">وضعیت حیات</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-id class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">شناسه ملی</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-map-pin class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">کد پستی</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-military-rank class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">نظام وظیفه</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tools Accordion -->
                <div class="border border-gray-200 rounded-lg mb-3">
                    <button class="w-full flex items-center justify-between p-4 text-right hover:bg-sky-50 transition-colors" 
                            onclick="toggleAccordion('tools-services')">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <x-tabler-tool class="w-5 h-5 text-orange-600" />
                            </div>
                            <span class="font-medium text-gray-800">ابزارها</span>
                        </div>
                        <x-tabler-chevron-down class="w-5 h-5 text-gray-500 transition-transform duration-200" id="tools-services-icon" />
                    </button>
                    <div id="tools-services" class="hidden border-t border-gray-200 bg-sky-50">
                        <div class="p-4 space-y-2">
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-calculator class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">ماشین حساب</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-currency-dollar class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">تبدیل ارز</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-white transition-colors">
                                <x-tabler-calendar class="w-4 h-4 text-gray-500" />
                                <span class="text-sm text-gray-700">تقویم</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Tab Content -->
        <div id="chat-tab" class="tab-content flex-1 overflow-hidden hidden">
            <div class="h-full flex flex-col">
                <!-- Chat Header -->
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-sky-500 to-sky-500 text-white">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <x-tabler-message-circle class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="font-bold">دستیار پیشخوانک</h3>
                            <p class="text-sm text-white/80">آنلاین</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-sky-50">
                    <!-- Bot Welcome Message -->
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <div class="w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center">
                            <x-tabler-robot class="w-5 h-5 text-white" />
                        </div>
                        <div class="flex-1">
                            <div class="bg-white rounded-2xl rounded-tr-md p-3 shadow-sm">
                                <p class="text-sm text-gray-800">سلام! به پیشخوانک خوش آمدید 👋</p>
                                <p class="text-sm text-gray-800 mt-1">چطور می‌تونم کمکتون کنم؟</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">همین الان</p>
                        </div>
                    </div>

                    <!-- Quick Action Buttons -->
                    <div class="flex flex-col space-y-2">
                        <p class="text-xs text-gray-500 text-center">انتخاب کنید:</p>
                        <div class="grid grid-cols-2 gap-2">
                            <button class="chat-action p-3 bg-sky-100 hover:bg-sky-200 text-sky-700 rounded-lg transition-colors text-sm" data-action="card-iban">
                                <x-tabler-credit-card class="w-4 h-4 mx-auto mb-1" />
                                کارت به شبا
                            </button>
                            <button class="chat-action p-3 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg transition-colors text-sm" data-action="iban-check">
                                <x-tabler-check class="w-4 h-4 mx-auto mb-1" />
                                بررسی شبا
                            </button>
                            <button class="chat-action p-3 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg transition-colors text-sm" data-action="traffic-fine">
                                <x-tabler-alert-triangle class="w-4 h-4 mx-auto mb-1" />
                                خلافی خودرو
                            </button>
                            <button class="chat-action p-3 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg transition-colors text-sm" data-action="vital-status">
                                <x-tabler-heart class="w-4 h-4 mx-auto mb-1" />
                                وضعیت حیات
                            </button>
                        </div>
                    </div>

                    <!-- Bot Response Area -->
                    <div id="bot-responses" class="space-y-4">
                        <!-- Dynamic responses will be added here -->
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="border-t border-gray-200 p-4 bg-white">
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <div class="flex-1">
                            <input type="text" id="chat-input" placeholder="پیام خود را بنویسید..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-full focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all duration-300">
                        </div>
                        <button id="send-message" class="p-2 bg-sky-500 hover:bg-sky-600 text-white rounded-full transition-colors">
                            <x-tabler-send class="w-5 h-5" />
                        </button>
                    </div>
                    
                    <!-- Quick Suggestions -->
                    <div class="flex flex-wrap gap-2 mt-3">
                        <button class="quick-suggestion px-3 py-1 bg-sky-100 hover:bg-sky-200 text-gray-700 rounded-full text-xs transition-colors" data-text="راهنما">راهنما</button>
                        <button class="quick-suggestion px-3 py-1 bg-sky-100 hover:bg-sky-200 text-gray-700 rounded-full text-xs transition-colors" data-text="قیمت خدمات">قیمت‌ها</button>
                        <button class="quick-suggestion px-3 py-1 bg-sky-100 hover:bg-sky-200 text-gray-700 rounded-full text-xs transition-colors" data-text="تماس با پشتیبانی">پشتیبانی</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        @auth
            <!-- User Profile Section -->
            <div class="p-4 border-t border-gray-200 bg-sky-50">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-sky-500 rounded-full flex items-center justify-center mr-3">
                        <span class="text-white text-sm font-bold">{{ substr(Auth::user()->name ?? 'کاربر', 0, 1) }}</span>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 text-sm">{{ Auth::user()->name ?? 'کاربر' }}</h3>
                        <p class="text-xs text-gray-500">{{ Auth::user()->mobile ?? Auth::user()->email }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <a href="{{ route('app.user.wallet') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg text-gray-700 hover:bg-white transition-colors">
                        <x-tabler-wallet class="w-4 h-4" />
                        <span class="text-sm">کیف پول</span>
                    </a>
                    <a href="{{ route('app.user.history') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg text-gray-700 hover:bg-white transition-colors">
                        <x-tabler-folder class="w-4 h-4" />
                        <span class="text-sm">سوابق</span>
                    </a>
                    <form method="POST" action="{{ route('app.auth.logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center space-x-3 space-x-reverse p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors">
                            <x-tabler-logout class="w-4 h-4" />
                            <span class="text-sm">خروج</span>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- Guest Actions -->
            <div class="p-4 border-t border-gray-200 bg-sky-50">
                <div class="space-y-2">
                    <a href="{{ route('login') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg text-sky-600 hover:bg-sky-50 transition-colors">
                        <x-tabler-login class="w-4 h-4" />
                        <span class="text-sm">ورود</span>
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Remove active class from all tabs
            tabBtns.forEach(b => {
                b.classList.remove('active', 'text-sky-600', 'border-sky-500');
                b.classList.add('text-gray-600', 'border-transparent');
            });
            
            // Add active class to clicked tab
            this.classList.add('active', 'text-sky-600', 'border-sky-500');
            this.classList.remove('text-gray-600', 'border-transparent');
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show target tab content
            document.getElementById(targetTab + '-tab').classList.remove('hidden');
        });
    });
    
    // Chat functionality
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-message');
    const botResponses = document.getElementById('bot-responses');
    const chatActions = document.querySelectorAll('.chat-action');
    const quickSuggestions = document.querySelectorAll('.quick-suggestion');
    
    // Chat responses
    const responses = {
        'card-iban': 'برای تبدیل کارت به شبا، شماره کارت 16 رقمی خود را وارد کنید. این خدمت رایگان است.',
        'iban-check': 'برای بررسی شبا، شماره شبا خود را وارد کنید. سیستم صحت آن را بررسی می‌کند.',
        'traffic-fine': 'برای استعلام خلافی خودرو، شماره پلاک و کد ملی مالک را وارد کنید.',
        'vital-status': 'برای استعلام وضعیت حیات، کد ملی فرد مورد نظر را وارد کنید.',
        'راهنما': 'برای راهنمای استفاده از خدمات، می‌توانید از منوی بالا استفاده کنید یا با پشتیبانی تماس بگیرید.',
        'قیمت خدمات': 'اکثر خدمات پایه رایگان هستند. برای خدمات تخصصی، قیمت‌ها در هر صفحه نمایش داده می‌شود.',
        'تماس با پشتیبانی': 'برای تماس با پشتیبانی از طریق صفحه تماس با ما یا شماره 021-1234-5678 تماس بگیرید.'
    };
    
    function addBotMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex items-start space-x-3 space-x-reverse';
        messageDiv.innerHTML = `
            <div class="w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <div class="bg-white rounded-2xl rounded-tr-md p-3 shadow-sm">
                    <p class="text-sm text-gray-800">${message}</p>
                </div>
                <p class="text-xs text-gray-500 mt-1">همین الان</p>
            </div>
        `;
        botResponses.appendChild(messageDiv);
        botResponses.scrollTop = botResponses.scrollHeight;
    }
    
    function addUserMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex items-start space-x-3 space-x-reverse justify-end';
        messageDiv.innerHTML = `
            <div class="flex-1">
                <div class="bg-sky-500 text-white rounded-2xl rounded-tl-md p-3 shadow-sm mr-8">
                    <p class="text-sm">${message}</p>
                </div>
                <p class="text-xs text-gray-500 mt-1 text-left">همین الان</p>
            </div>
        `;
        botResponses.appendChild(messageDiv);
        botResponses.scrollTop = botResponses.scrollHeight;
    }
    
    // Chat action buttons
    chatActions.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            const actionText = this.textContent.trim();
            
            addUserMessage(actionText);
            setTimeout(() => {
                addBotMessage(responses[action] || 'متشکرم از انتخاب شما. لطفاً کمی صبر کنید...');
            }, 500);
        });
    });
    
    // Quick suggestions
    quickSuggestions.forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.dataset.text;
            addUserMessage(text);
            setTimeout(() => {
                addBotMessage(responses[text] || 'متشکرم از سوال شما. در حال بررسی...');
            }, 500);
        });
    });
    
    // Send message functionality
    function sendMessage() {
        const message = chatInput.value.trim();
        if (message) {
            addUserMessage(message);
            chatInput.value = '';
            
            setTimeout(() => {
                addBotMessage('متشکرم از پیام شما. تیم پشتیبانی در اسرع وقت پاسخ خواهد داد.');
            }, 500);
        }
    }
    
    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    // Event listeners for mobile sidebar
    const backdrop = document.getElementById('mobileSideMenuBackdrop');
    const closeBtn = document.querySelector('.closeMobileMenu');
    
    if (backdrop) {
        backdrop.addEventListener('click', closeMobileSidebar);
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', closeMobileSidebar);
    }
});

// Accordion functionality
function toggleAccordion(id) {
    const content = document.getElementById(id);
    const icon = document.getElementById(id + '-icon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

// Mobile sidebar functions
function openMobileSidebar() {
    const backdrop = document.getElementById('mobileSideMenuBackdrop');
    const menu = document.getElementById('mobileSideMenu');
    
    if (backdrop && menu) {
        backdrop.classList.remove('invisible', 'opacity-0');
        menu.classList.remove('translate-x-full');
    }
}

function closeMobileSidebar() {
    const backdrop = document.getElementById('mobileSideMenuBackdrop');
    const menu = document.getElementById('mobileSideMenu');
    
    if (backdrop && menu) {
        backdrop.classList.add('invisible', 'opacity-0');
        menu.classList.add('translate-x-full');
    }
}
</script>

<style>
.tab-btn.active {
    border-bottom-color: #0ea5e9 !important;
    color: #0ea5e9 !important;
}
</style> 