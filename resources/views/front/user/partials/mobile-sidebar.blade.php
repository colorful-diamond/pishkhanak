@props(['currentRoute' => request()->route()->getName()])

<!-- Mobile Sidebar -->
<div class="mb-4">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-3">
        <!-- User Info (mobile) - more compact -->
        <div class="flex items-center justify-center mb-4 px-2">
            <div class="w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center ml-2">
                <span class="text-white text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
            <div class="text-center">
                <h3 class="font-medium text-gray-900 text-sm">{{ Auth::user()->name }}</h3>
                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
            </div>
        </div>
        
        <!-- Simple Navigation Grid - 4 buttons in one row -->
        <div class="grid grid-cols-4 gap-2">
            <!-- Dashboard -->
            <a href="{{ route('app.user.dashboard') }}" 
               class="flex flex-col items-center p-2 rounded-lg transition-all duration-300 {{ $currentRoute === 'app.user.dashboard' ? 'bg-sky-50 text-sky-700 shadow-md border-2 border-sky-200' : 'text-gray-700 hover:bg-sky-50 border-2 border-transparent' }}">
                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center mb-1 {{ $currentRoute === 'app.user.dashboard' ? 'bg-sky-100' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
                    </svg>
                </div>
                <span class="text-xs text-center font-medium">داشبورد</span>
            </a>

            <!-- History -->
            <a href="{{ route('app.user.history') }}" 
               class="flex flex-col items-center p-2 rounded-lg transition-all duration-300 {{ $currentRoute === 'app.user.history' ? 'bg-sky-50 text-sky-700 shadow-md border-2 border-sky-200' : 'text-gray-700 hover:bg-sky-50 border-2 border-transparent' }}">
                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center mb-1 {{ $currentRoute === 'app.user.history' ? 'bg-sky-100' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-xs text-center font-medium">سوابق</span>
            </a>

            <!-- Wallet -->
            <a href="{{ route('app.user.wallet') }}" 
               class="flex flex-col items-center p-2 rounded-lg transition-all duration-300 {{ $currentRoute === 'app.user.wallet' ? 'bg-sky-50 text-sky-700 shadow-md border-2 border-sky-200' : 'text-gray-700 hover:bg-sky-50 border-2 border-transparent' }}">
                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center mb-1 {{ $currentRoute === 'app.user.wallet' ? 'bg-sky-100' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs text-center font-medium">کیف پول</span>
            </a>

            <!-- Support -->
            <a href="{{ route('app.user.tickets.index') }}" 
               class="flex flex-col items-center p-2 rounded-lg transition-all duration-300 {{ Str::contains($currentRoute, 'tickets') ? 'bg-sky-50 text-sky-700 shadow-md border-2 border-sky-200' : 'text-gray-700 hover:bg-sky-50 border-2 border-transparent' }}">
                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center mb-1 {{ Str::contains($currentRoute, 'tickets') ? 'bg-sky-100' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <span class="text-xs text-center font-medium">پشتیبانی</span>
            </a>
        </div>

        <!-- Profile Link - more compact -->
        <div class="mt-3 pt-3 border-t border-gray-200">
            <a href="{{ route('app.user.profile') }}" 
               class="flex items-center justify-center p-2 rounded-lg transition-all duration-300 {{ $currentRoute === 'app.user.profile' ? 'bg-sky-50 text-sky-700 shadow-md border-2 border-sky-200' : 'text-gray-700 hover:bg-sky-50 border-2 border-transparent' }}">
                <div class="w-6 h-6 rounded-lg bg-sky-100 flex items-center justify-center ml-2 {{ $currentRoute === 'app.user.profile' ? 'bg-sky-100' : '' }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium">تنظیمات پروفایل</span>
            </a>
        </div>
    </div>
</div> 