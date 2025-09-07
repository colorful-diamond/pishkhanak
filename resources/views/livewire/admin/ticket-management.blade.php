<div class="min-h-screen/2 bg-sky-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">مدیریت پیشرفته تیکت‌ها</h1>
                    <p class="text-sm text-gray-600">سیستم جامع پشتیبانی و مدیریت درخواست‌ها</p>
                </div>
                
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

    <!-- Dashboard Stats -->
    <div class="px-6 py-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-xs text-gray-500">کل تیکت‌ها</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($dashboardStats['total']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-xs text-gray-500">باز</p>
                        <p class="text-lg font-bold text-green-700">{{ number_format($dashboardStats['open']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-xs text-gray-500">معوق</p>
                        <p class="text-lg font-bold text-red-700">{{ number_format($dashboardStats['overdue']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-xs text-gray-500">تیکت‌های من</p>
                        <p class="text-lg font-bold text-purple-700">{{ number_format($dashboardStats['myTickets']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-xs text-gray-500">بدون مسئول</p>
                        <p class="text-lg font-bold text-gray-700">{{ number_format($dashboardStats['unassigned']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm"
                           placeholder="جستجو در تیکت‌ها...">
                </div>

                <div>
                    <select wire:model.live="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                        <option value="">همه وضعیت‌ها</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select wire:model.live="filterCategory" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                        <option value="">همه دسته‌ها</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select wire:model.live="filterPriority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                        <option value="">همه اولویت‌ها</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select wire:model.live="filterAssigned" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                        <option value="">همه مسئولین</option>
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
                    
                    <button wire:click="toggleMyTickets" 
                            class="px-4 py-2 text-sm {{ $showOnlyMine ? 'bg-sky-600 text-white' : 'bg-sky-100 text-gray-600' }} rounded-lg hover:bg-sky-700 transition-colors">
                        {{ $showOnlyMine ? 'همه تیکت‌ها' : 'تیکت‌های من' }}
                    </button>
                </div>

                <div class="text-sm text-gray-600">
                    {{ $tickets->total() }} تیکت یافت شد
                </div>
            </div>
        </div>

        <!-- Tickets List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-sky-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">شماره</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">موضوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">کاربر</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وضعیت</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">اولویت</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">مسئول</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">آخرین بروزرسانی</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-sky-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $ticket->ticket_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $this->getTimeAgo($ticket->created_at) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $ticket->subject }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($ticket->description, 60) }}</div>
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
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->status)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $this->getStatusColor($ticket->status) }}-100 text-{{ $this->getStatusColor($ticket->status) }}-800">
                                            {{ is_string($ticket->status) ? ucfirst($ticket->status) : $ticket->status->name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->priority)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $this->getPriorityColor($ticket->priority) }}-100 text-{{ $this->getPriorityColor($ticket->priority) }}-800">
                                            {{ is_string($ticket->priority) ? ucfirst($ticket->priority) : $ticket->priority->name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($ticket->assignedTo)
                                        <div class="text-sm text-gray-900">{{ $ticket->assignedTo->name }}</div>
                                    @elseif($ticket->assigned_to)
                                        <div class="text-sm text-gray-900">مسئول: {{ $ticket->assigned_to }}</div>
                                    @else
                                        <button wire:click="quickAssign({{ $ticket->id }})"
                                                class="text-sm text-sky-600 hover:text-sky-900">
                                            اختصاص به من
                                        </button>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $this->getTimeAgo($ticket->updated_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="openTicket({{ $ticket->id }})"
                                            class="text-sky-600 hover:text-sky-900 mr-3">
                                        مشاهده
                                    </button>
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
    </div>

    <!-- Ticket Modal -->
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
            
            <div class="flex items-center justify-center min-h-screen/2 px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-sky-500 bg-opacity-75" 
                     wire:click="closeTicketModal"></div>

                <div class="relative w-full max-w-4xl mx-auto bg-white rounded-lg shadow-xl transform transition-all">
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $selectedTicket->subject }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ $selectedTicket->ticket_number }} • {{ $selectedTicket->user->name }}
                                </p>
                            </div>
                            <button wire:click="closeTicketModal" 
                                    class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-4 max-h-96 overflow-y-auto">
                        <!-- Ticket Info -->
                        <div class="grid grid-cols-3 gap-4 mb-6 p-4 bg-sky-50 rounded-lg">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">وضعیت</label>
                                <select wire:model="newStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">اولویت</label>
                                <select wire:model="newPriority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                    @foreach($priorities as $priority)
                                        <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">مسئول</label>
                                <select wire:model="assignToAgent" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                    <option value="">انتخاب مسئول</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="space-y-4 mb-6">
                            @foreach($selectedTicket->messages as $message)
                                <div class="flex space-x-4 space-x-reverse {{ $message->user_id === $selectedTicket->user_id ? 'flex-row-reverse' : '' }}">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">
                                                {{ substr($message->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-sky-100 rounded-lg p-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="font-medium text-gray-900 text-sm">{{ $message->user->name }}</span>
                                                <span class="text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="text-gray-700 text-sm">
                                                {!! nl2br(e($message->message)) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Response Form -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">قالب پاسخ</label>
                                <select wire:model="selectedTemplate" wire:change="useTemplate($event.target.value)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                    <option value="">انتخاب قالب...</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">پاسخ شما</label>
                                <textarea wire:model="responseMessage" 
                                          rows="4" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors resize-none"
                                          placeholder="پاسخ خود را بنویسید..."></textarea>
                                @error('responseMessage') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="isInternalNote" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                                    <span class="mr-2 text-sm text-gray-700">یادداشت داخلی (غیرقابل مشاهده برای کاربر)</span>
                                </label>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">فایل‌های پیوست</label>
                                <input type="file" wire:model="attachments" multiple class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                @error('attachments.*') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <div class="flex items-center space-x-2 space-x-reverse">
                            @if(!$selectedTicket->assigned_to)
                                <button wire:click="assignToMe" 
                                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                    اختصاص به من
                                </button>
                            @endif
                        </div>
                        
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <button wire:click="closeTicketModal" 
                                    class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                                بستن
                            </button>
                            <button wire:click="submitResponse" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                ارسال پاسخ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Toast Notifications -->
    <div x-data="{ show: false, message: '', type: 'success' }" 
         x-on:show-toast.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed top-4 right-4 z-50">
        <div class="bg-white rounded-lg shadow-lg border-r-4 p-4"
             :class="{ 'border-green-400': type === 'success', 'border-red-400': type === 'error' }">
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