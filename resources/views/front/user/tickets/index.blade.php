@extends('front.layouts.app')

@section('content')
<div class="">
    <div class="container mx-auto px-4 py-3">
        <!-- Header - more compact -->
        <div class="mb-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                <div>
                    <h1 class="text-lg font-bold text-gray-900 mb-1">درخواست‌های پشتیبانی</h1>
                    <p class="text-gray-600 text-xs">مدیریت و پیگیری درخواست‌های پشتیبانی</p>
                </div>
                <a href="{{ route('app.user.tickets.create') }}" 
                   class="inline-flex items-center justify-center w-full md:w-auto px-4 py-2 text-sm bg-sky-600 text-white rounded-xl hover:bg-sky-700 transition-colors">
                    ایجاد درخواست جدید
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Include Sidebar Component -->

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            @include('front.user.partials.sidebar')

            <!-- Main Content -->
            <div class="lg:col-span-3 col-span-4">
                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
                    <h3 class="text-base font-medium text-gray-900 mb-4">فیلترهای جستجو</h3>
                    <form method="GET" class="space-y-4">
                        <!-- Mobile-friendly filters layout -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Change to a single row layout -->
                            <div class="flex flex-wrap items-center space-x-3 space-x-reverse">
                                <div class="flex-1">
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">وضعیت</label>
                                    <select id="status" name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                        <option value="">همه</option>
                                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>باز</option>
                                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>در حال بررسی</option>
                                        <option value="waiting_for_user" {{ request('status') === 'waiting_for_user' ? 'selected' : '' }}>در انتظار پاسخ شما</option>
                                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>حل شده</option>
                                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>بسته</option>
                                    </select>
                                </div>

                                <div class="flex-1">
                                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">دسته‌بندی</label>
                                    <select id="category" name="category" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                        <option value="">همه</option>
                                        <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>فنی</option>
                                        <option value="billing" {{ request('category') === 'billing' ? 'selected' : '' }}>مالی</option>
                                        <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>عمومی</option>
                                        <option value="bug_report" {{ request('category') === 'bug_report' ? 'selected' : '' }}>گزارش خطا</option>
                                        <option value="feature_request" {{ request('category') === 'feature_request' ? 'selected' : '' }}>درخواست ویژگی</option>
                                    </select>
                                </div>

                                <div class="flex-1">
                                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">جستجو</label>
                                    <input type="text" id="search" name="search" value="{{ request('search') }}" 
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                           placeholder="جستجو در درخواست‌ها...">
                                </div>
                            </div>
                        </div>

                        <!-- Apply filter button -->
                        <div class="flex justify-end">
                            <button type="submit" class="w-full md:w-auto px-4 py-2 text-sm bg-sky-600 text-white rounded-xl hover:bg-sky-700 transition-colors">
                                اعمال فیلتر
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tickets List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-base font-medium text-gray-900">درخواست‌های شما ({{ $tickets->total() }})</h2>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @forelse($tickets as $ticket)
                            <div class="p-4 hover:bg-sky-50 transition-colors">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $ticket->getStatusColor() }}">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium text-gray-900 mb-1">
                                                <a href="{{ route('app.user.tickets.show', $ticket) }}" class="hover:text-sky-600 transition-colors">
                                                    {{ $ticket->subject }}
                                                </a>
                                            </h3>
                                            <div class="flex flex-col md:flex-row md:items-center md:space-x-3 md:space-x-reverse text-xs text-gray-500 space-y-1 md:space-y-0">
                                                <span>شماره: {{ $ticket->ticket_number }}</span>
                                                <span>دسته: {{ $ticket->getCategoryText() }}</span>
                                                <span>تاریخ: {{ \Verta::instance($ticket->created_at)->format('Y/m/d') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-2 md:space-x-reverse">
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-medium {{ $ticket->getStatusColor() }}">
                                            {{ $ticket->getStatusText() }}
                                        </span>
                                        <a href="{{ route('app.user.tickets.show', $ticket) }}" 
                                           class="inline-flex items-center justify-center px-4 py-2 text-sm text-sky-600 hover:text-sky-700 border border-sky-600 hover:border-sky-700 rounded-xl font-medium transition-colors">
                                            مشاهده جزئیات
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900 mb-2">درخواستی یافت نشد</h3>
                                <p class="text-xs text-gray-500 mb-4">هنوز درخواستی ایجاد نکرده‌اید یا درخواست‌های شما با فیلترهای انتخاب شده مطابقت ندارد.</p>
                                <a href="{{ route('app.user.tickets.create') }}" 
                                   class="inline-flex items-center px-4 py-2 text-sm bg-sky-600 text-white rounded-xl hover:bg-sky-700 transition-colors">
                                    ایجاد درخواست جدید
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </a>
                            </div>
                        @endforelse
                    </div>

                    @if($tickets->hasPages())
                        <div class="px-4 py-3 border-t border-gray-200">
                            {{ $tickets->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 