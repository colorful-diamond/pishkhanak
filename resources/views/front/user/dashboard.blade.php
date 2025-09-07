@extends('front.layouts.app')

@section('content')

    <div class="container mx-auto px-4 py-3">
        <!-- Header - more compact -->
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-gray-900 mb-1">داشبورد</h1>
                    <p class="text-gray-600 text-xs">مدیریت حساب کاربری و فعالیت‌ها</p>
                </div>
            </div>
        </div>

        <!-- Include Sidebar Component -->

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            @include('front.user.partials.sidebar')

            <!-- Main Content -->
            <div class="lg:col-span-3 col-span-4">
                <!-- Welcome Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-sky-100 rounded-xl flex items-center justify-center ml-3">
                            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900 mb-1">خوش آمدید، {{ Auth::user()->name }}!</h2>
                            <p class="text-gray-600 text-sm">امروز {{ \Verta::instance(now())->format('Y/m/d') }} است</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">موجودی کیف پول</p>
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($wallet->balance ?? 0) }} تومان</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-sky-100 rounded-xl flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">درخواست‌های باز</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $openTickets ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-100 rounded-xl flex items-center justify-center ml-3">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">تراکنش‌های ماه</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $monthlyTransactions ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <h3 class="text-base font-medium text-gray-900 mb-3">عملیات سریع</h3>
                        <div class="space-y-2">
                            <a href="{{ route('app.user.tickets.create') }}" 
                               class="flex items-center p-2 rounded-xl text-gray-700 hover:bg-sky-50 transition-colors">
                                <span class="text-sm">ایجاد درخواست جدید</span>
                                <svg class="w-4 h-4 mr-2 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </a>
                            <a href="{{ route('app.user.wallet') }}" 
                               class="flex items-center p-2 rounded-xl text-gray-700 hover:bg-sky-50 transition-colors">
                                <span class="text-sm">شارژ کیف پول</span>
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </a>
                            <a href="{{ route('app.user.history') }}" 
                               class="flex items-center p-2 rounded-xl text-gray-700 hover:bg-sky-50 transition-colors">
                                <span class="text-sm">مشاهده سوابق</span>
                                <svg class="w-4 h-4 mr-2 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <h3 class="text-base font-medium text-gray-900 mb-3">آخرین فعالیت‌ها</h3>
                        <div class="space-y-2">
                            @forelse($recentActivities ?? [] as $activity)
                                <div class="flex items-center p-2 rounded-xl bg-sky-50">
                                    <div class="w-2 h-2 bg-sky-500 rounded-full ml-2"></div>
                                    <span class="text-xs text-gray-600">{{ $activity->description }}</span>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-xs text-gray-500">فعالیتی یافت نشد</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Tickets -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-medium text-gray-900">آخرین درخواست‌ها</h3>
                        <a href="{{ route('app.user.tickets.index') }}" 
                           class="text-sky-600 hover:text-sky-700 text-xs font-medium transition-colors px-2 py-1 rounded-lg bg-sky-50 hover:bg-sky-100">
                            مشاهده همه
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentTickets ?? [] as $ticket)
                            <div class="flex items-center justify-between p-3 bg-sky-50 rounded-xl">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center {{ $ticket->getStatusColor() }}">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $ticket->subject }}</p>
                                        <p class="text-xs text-gray-500">{{ \Verta::instance($ticket->created_at)->format('Y/m/d') }}</p>
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full {{ $ticket->getStatusColor() }} text-white">
                                    {{ $ticket->getStatusText() }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                <p class="text-xs text-gray-500">هنوز درخواستی ایجاد نکرده‌اید</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 