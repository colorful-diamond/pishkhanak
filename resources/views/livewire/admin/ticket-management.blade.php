<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-sky-50">
    <!-- Enhanced Header -->
    <div class="bg-white/80 backdrop-blur-lg border-b border-gray-200/50 shadow-lg shadow-blue-500/5">
        <div class="px-4 md:px-6 py-4 md:py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div>
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="p-2 md:p-3 bg-gradient-to-br from-blue-500 to-sky-600 rounded-xl md:rounded-2xl shadow-lg">
                            <svg class="w-6 h-6 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-l from-blue-600 to-sky-800 bg-clip-text text-transparent">مدیریت پیشرفته تیکت‌ها</h1>
                            <p class="text-xs md:text-sm text-slate-600 font-medium">سیستم جامع پشتیبانی و مدیریت درخواست‌ها • پیشخانه</p>
                        </div>
                    </div>
                </div>
                
                @if($currentAgent)
                <div class="flex items-center space-x-2 md:space-x-4 space-x-reverse">
                    <div class="flex items-center">
                        <div class="w-2 h-2 md:w-3 md:h-3 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                        <span class="text-xs md:text-sm text-gray-700 font-medium">آنلاین</span>
                    </div>
                    <div class="text-xs md:text-sm text-gray-600">
                        {{ $currentAgent->current_tickets }}/{{ $currentAgent->max_tickets }} تیکت
                    </div>
                    <div class="w-12 md:w-16 bg-sky-200 rounded-full h-1.5 md:h-2">
                        <div class="bg-gradient-to-r from-sky-400 to-sky-600 h-1.5 md:h-2 rounded-full transition-all duration-500" style="width: {{ $currentAgent->workload_percentage }}%"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Enhanced Dashboard Stats -->
    <div class="px-4 md:px-6 py-6 md:py-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-6 mb-6 md:mb-8">
            <div class="bg-white/70 backdrop-blur-sm p-4 md:p-6 rounded-xl md:rounded-2xl shadow-lg border border-gray-200/50 hover:shadow-xl transition-all duration-300 group">
                <div class="flex flex-col md:flex-row md:items-center">
                    <div class="w-8 h-8 md:w-12 md:h-12 bg-gradient-to-br from-slate-100 to-slate-200 rounded-lg md:rounded-xl flex items-center justify-center group-hover:from-sky-100 group-hover:to-sky-200 transition-all duration-300 mb-2 md:mb-0">
                        <svg class="w-4 h-4 md:w-6 md:h-6 text-slate-600 group-hover:text-sky-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="md:mr-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">کل تیکت‌ها</p>
                        <p class="text-lg md:text-2xl font-bold text-slate-900">{{ number_format($dashboardStats['total']) }}</p>
                        <div class="w-full bg-slate-200 rounded-full h-1 mt-1 md:mt-2">
                            <div class="bg-gradient-to-r from-slate-400 to-slate-500 h-1 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-sm p-4 md:p-6 rounded-xl md:rounded-2xl shadow-lg border border-gray-200/50 hover:shadow-xl transition-all duration-300 group">
                <div class="flex flex-col md:flex-row md:items-center">
                    <div class="w-8 h-8 md:w-12 md:h-12 bg-gradient-to-br from-emerald-100 to-green-200 rounded-lg md:rounded-xl flex items-center justify-center group-hover:from-emerald-200 group-hover:to-green-300 transition-all duration-300 mb-2 md:mb-0">
                        <svg class="w-4 h-4 md:w-6 md:h-6 text-emerald-600 group-hover:text-emerald-700 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="md:mr-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">باز</p>
                        <p class="text-lg md:text-2xl font-bold text-emerald-700">{{ number_format($dashboardStats['open']) }}</p>
                        <div class="w-full bg-emerald-200 rounded-full h-1 mt-1 md:mt-2">
                            <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-1 rounded-full" style="width: {{ $dashboardStats['total'] > 0 ? ($dashboardStats['open'] / $dashboardStats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-sm p-4 md:p-6 rounded-xl md:rounded-2xl shadow-lg border border-gray-200/50 hover:shadow-xl transition-all duration-300 group {{ $dashboardStats['overdue'] > 0 ? 'ring-2 ring-red-200 shadow-red-100' : '' }}">
                <div class="flex flex-col md:flex-row md:items-center">
                    <div class="w-8 h-8 md:w-12 md:h-12 bg-gradient-to-br from-red-100 to-rose-200 rounded-lg md:rounded-xl flex items-center justify-center group-hover:from-red-200 group-hover:to-rose-300 transition-all duration-300 mb-2 md:mb-0 {{ $dashboardStats['overdue'] > 0 ? 'animate-pulse' : '' }}">
                        <svg class="w-4 h-4 md:w-6 md:h-6 text-red-600 group-hover:text-red-700 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="md:mr-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">معوق</p>
                        <p class="text-lg md:text-2xl font-bold text-red-700">{{ number_format($dashboardStats['overdue']) }}</p>
                        <div class="w-full bg-red-200 rounded-full h-1 mt-1 md:mt-2">
                            <div class="bg-gradient-to-r from-red-400 to-red-600 h-1 rounded-full" style="width: {{ $dashboardStats['overdue'] > 0 ? '100' : '0' }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-sm p-4 md:p-6 rounded-xl md:rounded-2xl shadow-lg border border-gray-200/50 hover:shadow-xl transition-all duration-300 group">
                <div class="flex flex-col md:flex-row md:items-center">
                    <div class="w-8 h-8 md:w-12 md:h-12 bg-gradient-to-br from-purple-100 to-violet-200 rounded-lg md:rounded-xl flex items-center justify-center group-hover:from-purple-200 group-hover:to-violet-300 transition-all duration-300 mb-2 md:mb-0">
                        <svg class="w-4 h-4 md:w-6 md:h-6 text-purple-600 group-hover:text-purple-700 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="md:mr-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">تیکت‌های من</p>
                        <p class="text-lg md:text-2xl font-bold text-purple-700">{{ number_format($dashboardStats['myTickets']) }}</p>
                        <div class="w-full bg-purple-200 rounded-full h-1 mt-1 md:mt-2">
                            <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-1 rounded-full" style="width: {{ $dashboardStats['total'] > 0 ? ($dashboardStats['myTickets'] / $dashboardStats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-sm p-4 md:p-6 rounded-xl md:rounded-2xl shadow-lg border border-gray-200/50 hover:shadow-xl transition-all duration-300 group">
                <div class="flex flex-col md:flex-row md:items-center">
                    <div class="w-8 h-8 md:w-12 md:h-12 bg-gradient-to-br from-orange-100 to-amber-200 rounded-lg md:rounded-xl flex items-center justify-center group-hover:from-orange-200 group-hover:to-amber-300 transition-all duration-300 mb-2 md:mb-0">
                        <svg class="w-4 h-4 md:w-6 md:h-6 text-orange-600 group-hover:text-orange-700 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="md:mr-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">بدون مسئول</p>
                        <p class="text-lg md:text-2xl font-bold text-orange-700">{{ number_format($dashboardStats['unassigned']) }}</p>
                        <div class="w-full bg-orange-200 rounded-full h-1 mt-1 md:mt-2">
                            <div class="bg-gradient-to-r from-orange-400 to-orange-600 h-1 rounded-full" style="width: {{ $dashboardStats['total'] > 0 ? ($dashboardStats['unassigned'] / $dashboardStats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Search & Filters -->
        <div class="bg-white/70 backdrop-blur-sm rounded-xl md:rounded-2xl shadow-lg border border-gray-200/50 p-4 md:p-6 mb-6 md:mb-8">
            <div class="mb-4 md:mb-6">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 md:mb-4 flex items-center">
                    <svg class="w-4 h-4 md:w-5 md:h-5 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    جستجو و فیلترهای پیشرفته
                </h3>
                
                <!-- Quick Search Buttons -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <button wire:click="quickSearch('all')" 
                            class="px-3 md:px-4 py-2 text-xs md:text-sm rounded-full transition-all duration-200 {{ $searchType === 'all' ? 'bg-blue-600 text-white shadow-lg' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                        همه موارد
                    </button>
                    <button wire:click="quickSearch('ticket')" 
                            class="px-3 md:px-4 py-2 text-xs md:text-sm rounded-full transition-all duration-200 {{ $searchType === 'ticket' ? 'bg-blue-600 text-white shadow-lg' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                        تیکت و موضوع
                    </button>
                    <button wire:click="quickSearch('user')" 
                            class="px-3 md:px-4 py-2 text-xs md:text-sm rounded-full transition-all duration-200 {{ $searchType === 'user' ? 'bg-blue-600 text-white shadow-lg' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                        کاربران
                    </button>
                    <button wire:click="quickSearch('content')" 
                            class="px-3 md:px-4 py-2 text-xs md:text-sm rounded-full transition-all duration-200 {{ $searchType === 'content' ? 'bg-blue-600 text-white shadow-lg' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }}">
                        محتوای پیام‌ها
                    </button>
                </div>
            </div>

            <!-- Search Input -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-4 mb-4">
                <div class="lg:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 md:h-5 md:w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" 
                               class="w-full pr-8 md:pr-10 pl-3 md:pl-4 py-2.5 md:py-3 border border-gray-300 rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white/80 backdrop-blur-sm transition-all duration-200"
                               placeholder="جستجو در تیکت‌ها، کاربران، و محتوا...">
                    </div>
                </div>
                <div class="flex space-x-2 space-x-reverse">
                    <input type="date" wire:model.live="dateFrom" 
                           class="flex-1 px-2 md:px-3 py-2.5 md:py-3 border border-gray-300 rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs md:text-sm bg-white/80">
                    <input type="date" wire:model.live="dateTo" 
                           class="flex-1 px-2 md:px-3 py-2.5 md:py-3 border border-gray-300 rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs md:text-sm bg-white/80">
                </div>
            </div>

            <!-- Filter Dropdowns -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-4">
                <select wire:model.live="filterStatus" class="px-2 md:px-3 py-2.5 md:py-3 border border-gray-300 rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs md:text-sm bg-white/80">
                    <option value="">همه وضعیت‌ها</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterCategory" class="px-2 md:px-3 py-2.5 md:py-3 border border-gray-300 rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs md:text-sm bg-white/80">
                    <option value="">همه دسته‌ها</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterPriority" class="px-2 md:px-3 py-2.5 md:py-3 border border-gray-300 rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs md:text-sm bg-white/80">
                    <option value="">همه اولویت‌ها</option>
                    @foreach($priorities as $priority)
                        <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterAssigned" class="px-2 md:px-3 py-2.5 md:py-3 border border-gray-300 rounded-lg md:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs md:text-sm bg-white/80">
                    <option value="">همه مسئولین</option>
                    <option value="unassigned">بدون مسئول</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Quick Actions -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                <div class="flex flex-wrap items-center gap-2 md:gap-3">
                    <button wire:click="clearFilters" 
                            class="px-3 md:px-4 py-2 text-xs md:text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all duration-200 flex items-center">
                        <svg class="w-3 h-3 md:w-4 md:h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        پاک کردن فیلترها
                    </button>
                    
                    <button wire:click="toggleMyTickets" 
                            class="px-3 md:px-4 py-2 text-xs md:text-sm rounded-lg transition-all duration-200 flex items-center {{ $showOnlyMine ? 'bg-purple-600 text-white shadow-lg' : 'bg-purple-100 text-purple-700 hover:bg-purple-200' }}">
                        <svg class="w-3 h-3 md:w-4 md:h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ $showOnlyMine ? 'همه تیکت‌ها' : 'تیکت‌های من' }}
                    </button>

                    <button wire:click="toggleOverdue" 
                            class="px-3 md:px-4 py-2 text-xs md:text-sm rounded-lg transition-all duration-200 flex items-center {{ $showOverdue ? 'bg-red-600 text-white shadow-lg' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                        <svg class="w-3 h-3 md:w-4 md:h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $showOverdue ? 'همه تیکت‌ها' : 'فقط معوق‌ها' }}
                    </button>
                </div>

                <div class="text-xs md:text-sm text-gray-600 bg-gray-100 px-2 md:px-3 py-1.5 md:py-2 rounded-lg">
                    <strong>{{ number_format($tickets->total()) }}</strong> تیکت یافت شد
                </div>
            </div>
        </div>

        <!-- Mobile Card Layout (visible on mobile) -->
        <div class="block md:hidden bg-white/70 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/50 overflow-hidden">
            <div class="space-y-0">
                @forelse($tickets as $ticket)
                    <div class="border-b border-gray-200 last:border-b-0 {{ $ticket->ticketPriority && $ticket->ticketPriority->level >= 8 ? 'border-r-4 border-r-red-400' : '' }}">
                        <div class="p-4 space-y-3">
                            <!-- Header Row -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    @if($ticket->ticketPriority && $ticket->ticketPriority->level >= 8)
                                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                                    @endif
                                    <span class="text-sm font-bold text-gray-900">{{ $ticket->ticket_number }}</span>
                                </div>
                                <span class="text-xs text-gray-500">{{ $this->getTimeAgo($ticket->created_at) }}</span>
                            </div>

                            <!-- Subject and Description -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 leading-tight">{{ $ticket->subject }}</h3>
                                <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ Str::limit($ticket->description, 120) }}</p>
                            </div>

                            <!-- User Info -->
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-sky-500 rounded-full flex items-center justify-center shadow-md flex-shrink-0">
                                    <span class="text-xs font-bold text-white">
                                        {{ mb_substr($ticket->user->name, 0, 1) }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">{{ $ticket->user->name }}</div>
                                    @if($ticket->user->email)
                                        <div class="text-xs text-gray-500 truncate">{{ $ticket->user->email }}</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Status and Priority Row -->
                            <div class="flex items-center justify-between space-x-2 space-x-reverse">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    @if($ticket->ticketStatus)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $this->getStatusColor($ticket->ticketStatus) }}-100 text-{{ $this->getStatusColor($ticket->ticketStatus) }}-800 border border-{{ $this->getStatusColor($ticket->ticketStatus) }}-200">
                                            {{ $ticket->ticketStatus->name }}
                                        </span>
                                    @endif
                                    @if($ticket->ticketPriority)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $this->getPriorityColor($ticket->ticketPriority) }}-100 text-{{ $this->getPriorityColor($ticket->ticketPriority) }}-800 border border-{{ $this->getPriorityColor($ticket->ticketPriority) }}-200">
                                            {{ $ticket->ticketPriority->name }}
                                            @if($ticket->ticketPriority->level >= 8)
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </span>
                                    @endif
                                </div>

                                @if($ticket->messages_count > 0)
                                    <div class="flex items-center text-gray-400">
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span class="text-xs">{{ $ticket->messages_count }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Assigned and Time Info -->
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <div>
                                    @if($ticket->assignedTo)
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 bg-gradient-to-br from-purple-400 to-violet-500 rounded-full flex items-center justify-center ml-1">
                                                <span class="text-xs font-bold text-white" style="font-size: 8px;">{{ mb_substr($ticket->assignedTo->name, 0, 1) }}</span>
                                            </div>
                                            <span>{{ $ticket->assignedTo->name }}</span>
                                        </div>
                                    @else
                                        <button wire:click="quickAssign({{ $ticket->id }})"
                                                class="text-orange-600 hover:text-orange-900 bg-orange-100 hover:bg-orange-200 px-2 py-1 rounded-full transition-all duration-200">
                                            اختصاص به من
                                        </button>
                                    @endif
                                </div>
                                <div class="text-left">
                                    <div>{{ $this->getTimeAgo($ticket->updated_at) }}</div>
                                    @if(!$ticket->first_response_at && $ticket->created_at->diffInHours(now()) > 24)
                                        <div class="text-red-600 font-medium">معوق!</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="pt-2">
                                <button wire:click="openTicket({{ $ticket->id }})"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    مشاهده و پاسخ
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="text-base font-medium text-gray-500 mb-1">هیچ تیکتی یافت نشد</p>
                            <p class="text-sm text-gray-400">فیلترهای خود را بررسی کنید</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Mobile Pagination -->
            @if($tickets->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>

        <!-- Desktop Table Layout (hidden on mobile) -->
        <div class="hidden md:block bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-slate-50 to-blue-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">شماره تیکت</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">موضوع و توضیحات</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">کاربر</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">وضعیت</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">اولویت</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">مسئول</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">زمان‌بندی</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-sky-50 transition-all duration-200 border-r-4 {{ $ticket->ticketPriority && $ticket->ticketPriority->level >= 8 ? 'border-red-400' : 'border-transparent' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($ticket->ticketPriority && $ticket->ticketPriority->level >= 8)
                                            <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse ml-2"></div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $ticket->ticket_number }}</div>
                                            <div class="text-xs text-gray-500">{{ $this->getTimeAgo($ticket->created_at) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 max-w-xs">
                                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $ticket->subject }}</div>
                                    <div class="text-xs text-gray-500 truncate mt-1">{{ Str::limit($ticket->description, 80) }}</div>
                                    @if($ticket->messages_count > 0)
                                        <div class="flex items-center mt-1">
                                            <svg class="w-3 h-3 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <span class="text-xs text-gray-500">{{ $ticket->messages_count }} پیام</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-sky-500 rounded-full flex items-center justify-center shadow-md">
                                            <span class="text-sm font-bold text-white">
                                                {{ mb_substr($ticket->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="mr-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                            @if($ticket->user->email)
                                                <div class="text-xs text-gray-500 truncate max-w-32">{{ $ticket->user->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->ticketStatus)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $this->getStatusColor($ticket->ticketStatus) }}-100 text-{{ $this->getStatusColor($ticket->ticketStatus) }}-800 border border-{{ $this->getStatusColor($ticket->ticketStatus) }}-200">
                                            {{ $ticket->ticketStatus->name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->ticketPriority)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $this->getPriorityColor($ticket->ticketPriority) }}-100 text-{{ $this->getPriorityColor($ticket->ticketPriority) }}-800 border border-{{ $this->getPriorityColor($ticket->ticketPriority) }}-200">
                                            {{ $ticket->ticketPriority->name }}
                                            @if($ticket->ticketPriority->level >= 8)
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->assignedTo)
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 bg-gradient-to-br from-purple-400 to-violet-500 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-bold text-white">{{ mb_substr($ticket->assignedTo->name, 0, 1) }}</span>
                                            </div>
                                            <div class="text-sm text-gray-900 mr-2">{{ $ticket->assignedTo->name }}</div>
                                        </div>
                                    @else
                                        <button wire:click="quickAssign({{ $ticket->id }})"
                                                class="text-sm text-orange-600 hover:text-orange-900 bg-orange-100 hover:bg-orange-200 px-3 py-1 rounded-full transition-all duration-200">
                                            اختصاص به من
                                        </button>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs text-gray-500">
                                        <div>آخرین بروزرسانی:</div>
                                        <div class="font-medium">{{ $this->getTimeAgo($ticket->updated_at) }}</div>
                                        @if(!$ticket->first_response_at && $ticket->created_at->diffInHours(now()) > 24)
                                            <div class="text-red-600 font-medium">معوق!</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button wire:click="openTicket({{ $ticket->id }})"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                                            مشاهده و پاسخ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-500 mb-2">هیچ تیکتی یافت نشد</p>
                                        <p class="text-sm text-gray-400">فیلترهای خود را بررسی کنید یا جستجوی جدیدی انجام دهید</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Desktop Pagination -->
            @if($tickets->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Enhanced Mobile-First Modal -->
    @if($showTicketModal && $selectedTicket)
        <div class="fixed inset-0 z-50 overflow-y-auto" 
             x-data="{ open: @entangle('showTicketModal') }" 
             x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="flex items-end md:items-center justify-center min-h-screen p-0 md:p-4">
                <div class="fixed inset-0 transition-opacity bg-black/50 backdrop-blur-sm" 
                     wire:click="closeTicketModal"></div>

                <!-- Mobile: Full Screen Modal | Desktop: Constrained Modal -->
                <div class="relative w-full h-full md:h-auto md:max-w-4xl md:max-h-[90vh] mx-auto bg-white md:rounded-2xl shadow-2xl transform transition-all overflow-hidden">
                    <!-- Enhanced Modal Header -->
                    <div class="px-4 md:px-8 py-4 md:py-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-sky-50 sticky top-0 z-10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 md:space-x-4 space-x-reverse min-w-0">
                                <div class="p-2 md:p-3 bg-gradient-to-br from-blue-500 to-sky-600 rounded-xl md:rounded-2xl flex-shrink-0">
                                    <svg class="w-4 h-4 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-base md:text-xl font-bold text-gray-900 truncate">
                                        {{ $selectedTicket->subject }}
                                    </h3>
                                    <p class="text-xs md:text-sm text-gray-600 flex items-center mt-1">
                                        <span class="font-medium">{{ $selectedTicket->ticket_number }}</span>
                                        <span class="mx-1 md:mx-2">•</span>
                                        <span class="truncate">{{ $selectedTicket->user->name }}</span>
                                    </p>
                                </div>
                            </div>
                            <button wire:click="closeTicketModal" 
                                    class="p-1.5 md:p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200 flex-shrink-0 ml-2">
                                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile: Stack Layout | Desktop: Side by Side -->
                    <div class="flex flex-col md:flex-row h-full md:h-auto">
                        <!-- Messages Section -->
                        <div class="flex-1 p-4 md:p-8 max-h-96 md:max-h-96 overflow-y-auto bg-gray-50 order-2 md:order-1">
                            <h4 class="font-semibold text-gray-900 mb-3 md:mb-4 text-sm md:text-base">گفتگوها و پیام‌ها</h4>
                            <div class="space-y-3 md:space-y-4">
                                @foreach($selectedTicket->messages as $message)
                                    <div class="flex {{ $message->user_id === $selectedTicket->user_id ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[85%] md:max-w-xs lg:max-w-md">
                                            <div class="flex items-center {{ $message->user_id === $selectedTicket->user_id ? 'justify-end' : 'justify-start' }} mb-1">
                                                <span class="text-xs font-medium text-gray-600">{{ $message->user->name }}</span>
                                                <span class="text-xs text-gray-400 mx-2">{{ $message->created_at->format('Y/m/d H:i') }}</span>
                                            </div>
                                            <div class="rounded-xl md:rounded-2xl px-3 md:px-4 py-2 md:py-3 {{ $message->user_id === $selectedTicket->user_id ? 'bg-blue-500 text-white' : 'bg-white border border-gray-200' }}">
                                                <div class="text-sm whitespace-pre-wrap">{{ $message->message }}</div>
                                                @if($message->is_internal)
                                                    <div class="text-xs opacity-75 mt-1">یادداشت داخلی</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Settings Section -->
                        <div class="w-full md:w-80 bg-white border-r border-gray-200 p-4 md:p-6 order-1 md:order-2">
                            <h4 class="font-semibold text-gray-900 mb-3 md:mb-4 text-sm md:text-base">تنظیمات تیکت</h4>
                            
                            <!-- Ticket Info Grid -->
                            <div class="space-y-3 md:space-y-4 mb-4 md:mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">وضعیت</label>
                                    <select wire:model="newStatus" class="w-full px-3 py-2 md:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">اولویت</label>
                                    <select wire:model="newPriority" class="w-full px-3 py-2 md:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">مسئول</label>
                                    <select wire:model="assignToAgent" class="w-full px-3 py-2 md:py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">انتخاب مسئول</option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Response Section -->
                            <div class="space-y-3 md:space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">قالب پاسخ</label>
                                    <select wire:model="selectedTemplate" wire:change="useTemplate($event.target.value)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">انتخاب قالب...</option>
                                        @foreach($templates as $template)
                                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">پاسخ شما</label>
                                    <textarea wire:model="responseMessage" 
                                              rows="4" 
                                              class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none text-sm"
                                              placeholder="پاسخ خود را بنویسید..."></textarea>
                                    @error('responseMessage') 
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="isInternalNote" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="mr-2 text-sm text-gray-700">یادداشت داخلی</span>
                                    </label>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">فایل‌های پیوست</label>
                                    <input type="file" wire:model="attachments" multiple class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    @error('attachments.*') 
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Modal Footer -->
                    <div class="px-4 md:px-8 py-4 md:py-6 border-t border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between bg-gray-50 space-y-3 md:space-y-0 sticky bottom-0">
                        <div class="flex items-center justify-center md:justify-start">
                            @if(!$selectedTicket->assigned_to)
                                <button wire:click="assignToMe" 
                                        class="px-4 py-2.5 md:py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center text-sm font-medium w-full md:w-auto justify-center">
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    اختصاص به من
                                </button>
                            @endif
                        </div>
                        
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <button wire:click="closeTicketModal" 
                                    class="flex-1 md:flex-none px-6 py-2.5 md:py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                                بستن
                            </button>
                            <button wire:click="submitResponse" 
                                    class="flex-1 md:flex-none px-6 py-2.5 md:py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center justify-center">
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                ارسال پاسخ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Enhanced Toast Notifications -->
    <div x-data="{ show: false, message: '', type: 'success' }" 
         x-on:show-toast.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 4000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 transform translate-y-2 scale-95"
         class="fixed top-4 md:top-8 left-4 md:left-8 z-50">
        <div class="bg-white rounded-xl shadow-2xl border-r-4 p-3 md:p-4 min-w-72 md:min-w-80 backdrop-blur-lg"
             :class="{ 'border-green-500': type === 'success', 'border-red-500': type === 'error' }">
            <div class="flex items-center">
                <div class="flex-shrink-0 ml-3">
                    <div x-show="type === 'success'" class="w-6 h-6 md:w-8 md:h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div x-show="type === 'error'" class="w-6 h-6 md:w-8 md:h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900" x-text="message"></p>
                </div>
            </div>
        </div>
    </div>
</div>