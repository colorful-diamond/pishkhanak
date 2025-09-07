<div class="min-h-screen/2 bg-sky-50" x-data="{ showBulkActions: @entangle('selectedTickets').defer }" wire:poll.30s="refreshTickets">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">مدیریت تیکت‌ها</h1>
                    <p class="text-sm text-gray-600 mt-1">آخرین بروزرسانی: {{ $lastUpdated->format('Y/m/d H:i') }}</p>
                </div>
                
                <!-- Agent Status -->
                @if($currentAgent)
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-400 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-700">آنلاین</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ $currentAgent->current_tickets }}/{{ $currentAgent->max_tickets }} تیکت
                    </div>
                    <div class="w-16 bg-sky-200 rounded-full h-2">
                        <div class="bg-sky-600 h-2 rounded-full" style="width: {{ $currentAgent->workload_percentage }}%"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Dashboard -->
    <div class="px-6 py-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Total Tickets -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-sky-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">کل تیکت‌ها</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Open Tickets -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">تیکت‌های باز</p>
                        <p class="text-2xl font-bold text-green-700">{{ number_format($stats['open']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Overdue Tickets -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">تأخیر در پاسخ</p>
                        <p class="text-2xl font-bold text-red-700">{{ number_format($stats['overdue']) }}</p>
                    </div>
                </div>
            </div>

            <!-- My Tickets -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">تیکت‌های من</p>
                        <p class="text-2xl font-bold text-purple-700">{{ number_format($stats['my_tickets']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">جستجو</label>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                           placeholder="جستجو در تیکت‌ها...">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">وضعیت</label>
                    <select wire:model.live="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">همه</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">دسته‌بندی</label>
                    <select wire:model.live="filterCategory" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">همه</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">اولویت</label>
                    <select wire:model.live="filterPriority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">همه</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Assigned To Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">مسئول</label>
                    <select wire:model.live="filterAssignedTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">همه</option>
                        <option value="unassigned">بدون مسئول</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between mt-4">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <button wire:click="clearFilters" 
                            class="px-4 py-2 text-sm text-gray-600 bg-sky-100 rounded-lg hover:bg-sky-200 transition-colors">
                        پاک کردن فیلترها
                    </button>
                    
                    <!-- View Mode Toggle -->
                    <div class="flex bg-sky-100 rounded-lg p-1">
                        <button wire:click="changeViewMode('list')" 
                                class="px-3 py-1 text-sm rounded {{ $viewMode === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600' }}">
                            لیست
                        </button>
                        <button wire:click="changeViewMode('kanban')" 
                                class="px-3 py-1 text-sm rounded {{ $viewMode === 'kanban' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600' }}">
                            کانبان
                        </button>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div x-show="showBulkActions.length > 0" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="flex items-center space-x-3 space-x-reverse">
                    <span class="text-sm text-gray-600" x-text="`${showBulkActions.length} تیکت انتخاب شده`"></span>
                    
                    <select wire:model="bulkAction" class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                        <option value="">انتخاب عملیات</option>
                        <option value="assign_to_me">اختصاص به من</option>
                        <option value="change_status">تغییر وضعیت</option>
                        <option value="change_priority">تغییر اولویت</option>
                    </select>
                    
                    <button wire:click="executeBulkAction" 
                            class="px-4 py-2 text-sm bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                        اجرا
                    </button>
                </div>
            </div>
        </div>

        <!-- Tickets List -->
        @if($viewMode === 'list')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-sky-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-right">
                                    <input type="checkbox" wire:click="selectAllTickets" 
                                           class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                    wire:click="sortBy('ticket_number')">
                                    شماره تیکت
                                    @if($sortBy === 'ticket_number')
                                        <span class="mr-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                    wire:click="sortBy('subject')">
                                    موضوع
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    کاربر
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    وضعیت
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    اولویت
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    مسئول
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                    wire:click="sortBy('updated_at')">
                                    آخرین بروزرسانی
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    عملیات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($tickets as $ticket)
                                <tr class="hover:bg-sky-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" wire:click="toggleTicketSelection({{ $ticket->id }})"
                                               @if(in_array($ticket->id, $selectedTickets)) checked @endif
                                               class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $ticket->ticket_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $this->getTimeAgo($ticket->created_at) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $ticket->subject }}</div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($ticket->description, 60) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-sky-300 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-600">
                                                    {{ substr($ticket->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="mr-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $ticket->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($ticket->status)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getTicketStatusClass($ticket->status) }}">
                                                {{ $ticket->status->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-sm">نامشخص</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($ticket->priority)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getTicketPriorityClass($ticket->priority) }}">
                                                {{ $ticket->priority->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-sm">نامشخص</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($ticket->assignedTo)
                                            <div class="text-sm text-gray-900">{{ $ticket->assignedTo->name }}</div>
                                        @else
                                            <button wire:click="assignTicketToMe({{ $ticket->id }})"
                                                    class="text-sm text-sky-600 hover:text-sky-900">
                                                اختصاص به من
                                            </button>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $this->getTimeAgo($ticket->updated_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('filament.access.resources.tickets.view', $ticket) }}" 
                                           class="text-sky-600 hover:text-sky-900 mr-3">
                                            مشاهده
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $tickets->links() }}
                </div>
            </div>
        @endif

        @if($viewMode === 'kanban')
            <!-- Kanban Board -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                @foreach($statuses as $status)
                    <div class="bg-sky-100 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-medium text-gray-900">{{ $status->name }}</h3>
                            <span class="bg-sky-200 text-gray-700 px-2 py-1 rounded-full text-xs">
                                {{ $tickets->where('status_id', $status->id)->count() }}
                            </span>
                        </div>
                        
                        <div class="space-y-3">
                            @foreach($tickets->where('status_id', $status->id) as $ticket)
                                <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200 cursor-pointer hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-medium text-gray-500">{{ $ticket->ticket_number }}</span>
                                        @if($ticket->priority)
                                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $ticket->priority->color }}"></span>
                                        @endif
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">{{ $ticket->subject }}</h4>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">{{ $ticket->user->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $this->getTimeAgo($ticket->updated_at) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Toast Notifications -->
    <div x-data="{ show: false, message: '', type: 'success' }" 
         x-on:show-toast.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="opacity-100 transform translate-y-0 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 transform translate-y-0 sm:translate-x-0"
         x-transition:leave-end="opacity-0 transform translate-y-2 sm:translate-y-0 sm:translate-x-2"
         class="fixed top-4 right-4 z-50">
        <div class="bg-white rounded-lg shadow-lg border-r-4"
             :class="{ 'border-green-400': type === 'success', 'border-red-400': type === 'error' }">
            <div class="p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg x-show="type === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <svg x-show="type === 'error'" class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-900" x-text="message"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 