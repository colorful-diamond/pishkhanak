<nav id="footerNav" class="block md:hidden fixed bottom-0 left-0 right-0 bg-white shadow-sm border-t border-gray-200 z-30">
    <div class="max-w-screen-sm mx-auto">
        <div class="flex justify-around items-center px-2 py-2">
            <!-- Home -->
            <a href="{{ route('app.page.home') }}" class="flex flex-col items-center space-y-1">
                <x-tabler-home class="h-5 w-5 {{ Request::is('/') ? 'text-sky-600' : 'text-gray-500' }}" />
                <span class="text-xs {{ Request::is('/') ? 'text-sky-600 font-medium' : 'text-gray-500' }}">خانه</span>
            </a>
            
            <!-- Services Button - Opens sidebar and scrolls to services -->
            <button onclick="openSidebarAndScrollToServices()" class="flex flex-col items-center space-y-1">
                <x-tabler-apps class="h-5 w-5 text-gray-500" />
                <span class="text-xs text-gray-500">خدمات</span>
            </button>
            
            <!-- Support - Available for all users -->
            <a href="{{ route('app.user.tickets.index') }}" class="flex flex-col items-center space-y-1">
                <x-tabler-headset class="h-5 w-5 {{ Request::is('tickets*') ? 'text-sky-600' : 'text-gray-500' }}" />
                <span class="text-xs {{ Request::is('tickets*') ? 'text-sky-600 font-medium' : 'text-gray-500' }}">پشتیبانی</span>
            </a>
            
            @auth
                <!-- User Wallet -->
                <a href="{{ route('app.user.wallet') }}" class="flex flex-col items-center space-y-1">
                    <x-tabler-wallet class="h-5 w-5 {{ Request::is('wallet*') ? 'text-sky-600' : 'text-gray-500' }}" />
                    <span class="text-xs {{ Request::is('wallet*') ? 'text-sky-600 font-medium' : 'text-gray-500' }}">کیف‌پول</span>
                </a>
                
                <!-- User History -->
                <a href="{{ route('app.user.history') }}" class="flex flex-col items-center space-y-1">
                    <x-tabler-folder class="h-5 w-5 {{ Request::is('history*') ? 'text-sky-600' : 'text-gray-500' }}" />
                    <span class="text-xs {{ Request::is('history*') ? 'text-sky-600 font-medium' : 'text-gray-500' }}">سوابق</span>
                </a>
                
                <!-- User Profile / Settings -->
                <a href="{{ route('app.user.profile') }}" class="flex flex-col items-center space-y-1">
                    <x-tabler-user class="h-5 w-5 {{ Request::is('profile*') ? 'text-sky-600' : 'text-gray-500' }}" />
                    <span class="text-xs {{ Request::is('profile*') ? 'text-sky-600 font-medium' : 'text-gray-500' }}">پروفایل</span>
                </a>
            @else
                <!-- Blog for guests -->
                <a href="{{ route('app.blog.index') }}" class="flex flex-col items-center space-y-1">
                    <x-tabler-article class="h-5 w-5 {{ Request::is('blog*') ? 'text-sky-600' : 'text-gray-500' }}" />
                    <span class="text-xs {{ Request::is('blog*') ? 'text-sky-600 font-medium' : 'text-gray-500' }}">بلاگ</span>
                </a>
                
                <!-- About for guests -->
                <a href="{{ route('app.page.about') }}" class="flex flex-col items-center space-y-1">
                    <x-tabler-info-circle class="h-5 w-5 {{ Request::is('about*') ? 'text-sky-600' : 'text-gray-500' }}" />
                    <span class="text-xs {{ Request::is('about*') ? 'text-sky-600 font-medium' : 'text-gray-500' }}">درباره</span>
                </a>
                
                <!-- Login for guests -->
                <a href="{{ route('app.auth.login') }}" class="flex flex-col items-center space-y-1">
                    <x-tabler-login class="h-5 w-5 text-gray-500" />
                    <span class="text-xs text-gray-500">ورود</span>
                </a>
            @endauth
        </div>
    </div>
</nav>

<script>
// Function to open sidebar and scroll to services section
function openSidebarAndScrollToServices() {
    // Open the mobile sidebar
    if (typeof window.openMobileSideMenu === 'function') {
        window.openMobileSideMenu();
        
        // Wait for sidebar to open, then scroll to services section
        setTimeout(() => {
            const servicesSection = document.getElementById('services-section');
            if (servicesSection) {
                servicesSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }, 300); // Wait for sidebar animation to complete
    } else {
        console.error('openMobileSideMenu function not found');
    }
}

// Fallback function for scrolling to services on page
function scrollToServices() {
    const servicesSection = document.querySelector('[data-category-slug]');
    if (servicesSection) {
        servicesSection.scrollIntoView({ behavior: 'smooth' });
    }
}
</script>