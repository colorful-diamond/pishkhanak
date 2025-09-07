<header class="w-full shadow-sm z-30">
	<nav class="hidden flex-col w-full max-md:flex max-sm:flex">
		<div class="flex justify-between items-center px-4 py-5 bg-white border-b border-zinc-300 shadow-sm">
			<button id="hamburgerBtn" aria-label="Menu"
				class="hover:bg-zinc-100 transition-colors duration-300 p-2 rounded-full focus:ring-2 focus:ring-sky-400">
				<x-tabler-menu-2 class="w-6 h-6 text-sky-900" />
			</button>
			<a href="/" class="h-10 " aria-label="Home">
				<img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="w-auto h-8">
			</a>
			<button aria-label="Search"
				class="hover:bg-zinc-100 transition-colors duration-300 p-2 rounded-full focus:ring-2 focus:ring-sky-400">
				<x-tabler-search class="w-6 h-6 text-sky-900" />
			</button>
		</div>
		<div id="aiSearchBarMobile" class="hidden flex flex-col justify-center py-3 w-full bg-white">
			<form class="flex gap-2 justify-center items-center px-4 w-full" role="search">
				<input type="search" placeholder="جستجو" class="aiSearchInput flex-1 p-3 text-sm leading-6 text-right text-zinc-400 bg-white rounded-lg border border-solid border-zinc-300 focus:border-sky-400 transition-colors duration-300" />
				<button type="button" class="aiVoiceSearchBtn flex justify-center items-center p-2 w-10 h-10 rounded-lg border border-sky-400 hover:bg-sky-50 transition-colors duration-300 focus:ring-2 focus:ring-sky-400">
					<x-tabler-microphone class="w-5 h-5 text-sky-400" />
				</button>
				<button type="submit" class="aiSearchSubmitBtn flex justify-center items-center p-2 w-10 h-10 rounded-lg border border-sky-400 hover:bg-sky-50 transition-colors duration-300 focus:ring-2 focus:ring-sky-400">
					<x-tabler-search class="w-5 h-5 text-sky-400" />
				</button>
			</form>
			<div class="aiSearchTimer mt-2 text-center text-sm text-sky-600 hidden">
				<span class="aiSearchTimerText">00:30</span>
				<span class="aiSearchTimerDot inline-block w-2 h-2 bg-sky-600 rounded-full ml-1"></span>
			</div>
		</div>
	</nav>
	
	<nav class="flex-col w-full max-md:hidden max-sm:hidden">
		<div class="flex z-50 relative justify-center w-full bg-white border-b border-solid border-b-zinc-300 min-h-[88px]">
			<div class="flex relative justify-between items-center mx-auto w-full max-w-screen-lg">
				<a href="/" class="flex shrink-0 gap-2 my-auto h-10 " aria-label="Home">
					<img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="w-full h-auto">
				</a>
				<ul class="flex flex-wrap gap-4 justify-center items-end text-base text-center text-sky-900">
					<li class="services-menu group h-8">
						<a href="/services"
							class="flex gap-1 items-center px-3 py-1 hover:bg-yellow-400 rounded-full transition-colors duration-300 group-[.active]:bg-yellow-400">
							<span>خدمات</span>
							<img
								src="https://cdn.builder.io/api/v1/image/assets/TEMP/8abd688cc4855135ef0a0729103067e8dcf61a393bcaeebadd57ad791ea3881a?placeholderIfAbsent=true&apiKey=5f2a58c33dbd45d18d39d5587c92166f"
								alt="Services icon" class="w-4 h-4 mt-1" />
						</a>

						<!-- Services Dropdown Menu -->
						<nav
							class="services-dropdown absolute right-0 mt-2 w-[1032px] bg-white shadow-lg rounded-b-3xl z-50 opacity-0 transform -translate-y-2 transition-all duration-300 ease-in-out group-hover:opacity-100 group-hover:translate-y-0">
							<div class="p-10 flex justify-end gap-6">
								<section class="flex-1 flex flex-col items-start gap-6">
									<h3 class="text-base font-bold text-sky-900">سایر خدمات</h3>
									<ul class="space-y-4 text-right">
										<li><a href="#"
												class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">استعلام وضعیت
												حیات</a></li>
										<li><a href="#" class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">وضعیت
												نظام وظیفه</a></li>
										<li><a href="#"
												class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">استعلام شناسه
												ملی</a></li>
										<li><a href="#"
												class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">استعلام
												کدپستی</a></li>
									</ul>
								</section>

								<section class="flex-1 flex flex-col items-start gap-6">
									<h3 class="text-base font-bold text-sky-900">خودرو و موتور</h3>
									<ul class="space-y-4 text-right">
										<li><a href="#" class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">خلافی
												موتور</a></li>
										<li><a href="#" class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">خلافی
												خودرو</a></li>
										<li><a href="#" class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">لیست
												پلاک‌های فعال</a></li>
										<li><a href="#" class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">سوابق
												بیمه‌نامه شخص ثالث</a></li>
									</ul>
								</section>

								<section class="flex-1 flex flex-col items-start gap-6">
									<h3 class="text-base font-bold text-sky-900">خدمات بانکی</h3>
									<ul class="space-y-4 text-right">
										<li><a href="#"
												class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">استعلام مکنا</a>
										</li>
										<li><a href="#"
												class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">استعلام وضعیت رنگ
												چک</a></li>
										<li><a href="#"
												class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">استعلام چک
												برگشتی</a></li>
										<li><a href="#" class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">وام و
												تسهیلات</a></li>
										<li><a href="#"
												class="text-sky-900 hover:text-yellow-600 transition-colors duration-200">اعتبارسنجی
												بانکی</a></li>
										<li><a href="#"
												class="text-sky-900 hover:text-yellow-500 transition-colors duration-200">محاسبه شبا</a>
										</li>
									</ul>
								</section>
							</div>
						</nav>
					</li>
					<li class="h-8"><a href="{{ route('app.user.history') }}"
							class="px-3 py-1 hover:bg-yellow-400 rounded-full transition-colors duration-300">سوابق</a></li>
					<li class="h-8"><a href="{{ route('app.user.wallet') }}"
							class="px-3 py-1 hover:bg-yellow-400 rounded-full transition-colors duration-300">کیف‌پول</a></li>
					<li class="h-8"><a href="{{ route('app.blog.index') }}"
							class="px-3 py-1 hover:bg-yellow-400 rounded-full transition-colors duration-300">بلاگ</a></li>
					<li class="h-8"><a href="{{ route('app.page.about') }}"
							class="px-3 py-1 hover:bg-yellow-400 rounded-full transition-colors duration-300">درباره‌ما</a></li>
					<li class="h-8"><a href="{{ route('app.page.contact') }}"
							class="px-3 py-1 hover:bg-yellow-400 rounded-full transition-colors duration-300">ارتباط با ‌ما</a></li>
				</ul>
				@auth
					<!-- Authenticated User Menu -->
					<div class="relative">
						<button class="flex gap-2 items-center py-2 px-4 text-sm font-medium text-sky-400 bg-white rounded-lg border border-sky-400 hover:bg-sky-50 transition-colors duration-300 dropdown-toggle">
							<x-tabler-user class="w-5 h-5" />
							<div class="flex flex-col items-center">
								<span>{{ auth()->user()->mobile ?? auth()->user()->email }}</span>
								<span class="text-xs text-green-600 font-semibold">{{ number_format(auth()->user()->balance ?? 0) }} تومان</span>
							</div>
							<x-tabler-chevron-down class="w-4 h-4" />
						</button>
						
						<!-- User Dropdown Menu -->
						<div class="absolute left-0 mt-2 w-64 bg-white shadow-lg rounded-lg z-0 opacity-0 transform -translate-y-2 transition-all duration-300 ease-in-out pointer-events-none dropdown-menu">
							<div class="p-4">
								<div class="border-b border-gray-100 pb-3 mb-3">
									<p class="text-sm font-medium text-dark-sky-600">{{ auth()->user()->mobile ?? auth()->user()->email }}</p>
									<p class="text-xs text-gray-500">کاربر عزیز پیشخوانک</p>
									<p class="text-xs text-green-600 font-semibold mt-1">موجودی: {{ number_format(auth()->user()->balance ?? 0) }} تومان</p>
								</div>
								<nav class="space-y-2">
									<a href="{{ route('app.user.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-dark-sky-500 hover:bg-sky-50 rounded-lg transition-colors duration-200">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
										</svg>
										<span>داشبورد</span>
									</a>
									<a href="{{ route('app.user.history') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-dark-sky-500 hover:bg-sky-50 rounded-lg transition-colors duration-200">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
										</svg>
										<span>سوابق تراکنش</span>
									</a>
									<a href="{{ route('app.user.wallet') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-dark-sky-500 hover:bg-sky-50 rounded-lg transition-colors duration-200">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
										</svg>
										<span>کیف پول</span>
									</a>
									<a href="{{ route('app.user.tickets.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-dark-sky-500 hover:bg-sky-50 rounded-lg transition-colors duration-200">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
										</svg>
										<span>پشتیبانی</span>
									</a>
									<a href="{{ route('app.user.profile') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-dark-sky-500 hover:bg-sky-50 rounded-lg transition-colors duration-200">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
										</svg>
										<span>پروفایل</span>
									</a>
									<div class="border-t border-gray-100 pt-2">
										<form method="POST" action="{{ route('app.auth.logout') }}">
											@csrf
											<button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 w-full text-right">
												<x-tabler-logout class="w-4 h-4" />
												<span>خروج</span>
											</button>
										</form>
									</div>
								</nav>
							</div>
						</div>
					</div>
					
					<script>
					document.addEventListener('DOMContentLoaded', function() {
						const dropdownToggle = document.querySelector('.dropdown-toggle');
						const dropdownMenu = document.querySelector('.dropdown-menu');
						let isOpen = false;
						
						dropdownToggle.addEventListener('click', function(e) {
							e.preventDefault();
							e.stopPropagation();
							
							if (isOpen) {
								// Close dropdown
								dropdownMenu.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
								dropdownMenu.classList.remove('opacity-100', 'translate-y-0', 'z-50');
								isOpen = false;
							} else {
								// Open dropdown
								dropdownMenu.classList.remove('opacity-0', '-translate-y-2', 'pointer-events-none');
								dropdownMenu.classList.add('opacity-100', 'translate-y-0', 'z-50');
								isOpen = true;
							}
						});
						
						// Close dropdown when clicking outside
						document.addEventListener('click', function(e) {
							if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
								dropdownMenu.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
								dropdownMenu.classList.remove('opacity-100', 'translate-y-0', 'z-50');
								isOpen = false;
							}
						});
						
						// Prevent dropdown from closing when clicking inside it
						dropdownMenu.addEventListener('click', function(e) {
							e.stopPropagation();
						});
					});
					</script>
				@else
					<!-- Guest Login Button -->
					<a href="{{ route('app.auth.login') }}" class="flex gap-2 items-center py-2 px-4 text-sm font-medium text-sky-400 bg-white rounded-lg border border-sky-400 hover:bg-sky-50 transition-colors duration-300">
						<span>ورود | ثبت‌نام</span>
						<x-tabler-login class="w-5 h-5" />
					</a>
				@endauth

			</div>
		</div>
		<div id="aiSearchBarDesktop" class="hidden flex justify-center py-4 w-full bg-white">
    </div>
	</nav>
	@if(strpos(URL::current(), 'services/preview') !== false)
		<style>
			header{
				display: none !important;
			}
		</style>
	@endif
</header>