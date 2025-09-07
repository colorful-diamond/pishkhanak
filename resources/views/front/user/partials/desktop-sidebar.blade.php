@props(['currentRoute' => request()->route()->getName()])

<!-- Desktop Sidebar -->
<div class="lg:sticky lg:top-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <!-- User Info -->
        <div class="mb-6 text-center">
            <div class="w-16 h-16 bg-sky-500 rounded-full mx-auto mb-4 flex items-center justify-center">
                <span class="text-white text-xl font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
            <h3 class="font-medium text-gray-900 mb-1">{{ Auth::user()->name }}</h3>
            <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
        </div>

        <!-- Navigation -->
        <nav class="space-y-2">
            <a href="{{ route('app.user.dashboard') }}" 
               class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-lg transition-all duration-200 {{ $currentRoute === 'app.user.dashboard' ? 'bg-sky-50 text-sky-700 border-r-4 border-sky-500' : 'text-gray-700 hover:bg-sky-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
                </svg>
                <span class="font-medium">داشبورد</span>
            </a>

            <a href="{{ route('app.user.history') }}" 
               class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-lg transition-all duration-200 {{ $currentRoute === 'app.user.history' ? 'bg-sky-50 text-sky-700 border-r-4 border-sky-500' : 'text-gray-700 hover:bg-sky-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="font-medium">سوابق تراکنش</span>
            </a>

            <a href="{{ route('app.user.wallet') }}" 
               class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-lg transition-all duration-200 {{ $currentRoute === 'app.user.wallet' ? 'bg-sky-50 text-sky-700 border-r-4 border-sky-500' : 'text-gray-700 hover:bg-sky-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">کیف پول</span>
            </a>

            <a href="{{ route('app.user.tickets.index') }}" 
               class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-lg transition-all duration-200 {{ Str::contains($currentRoute, 'tickets') ? 'bg-sky-50 text-sky-700 border-r-4 border-sky-500' : 'text-gray-700 hover:bg-sky-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <span class="font-medium">پشتیبانی</span>
            </a>

            <a href="{{ route('app.user.profile') }}" 
               class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-lg transition-all duration-200 {{ $currentRoute === 'app.user.profile' ? 'bg-sky-50 text-sky-700 border-r-4 border-sky-500' : 'text-gray-700 hover:bg-sky-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="font-medium">پروفایل</span>
            </a>
        </nav>
    </div>
</div> 