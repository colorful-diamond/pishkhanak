<!-- Kanban Board View -->
<div class="flex space-x-6 space-x-reverse overflow-x-auto pb-6">
    @foreach($statuses as $status)
        <div class="flex-shrink-0 w-80">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Column Header -->
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-gray-900">{{ $status->name }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-gray-800">
                            {{ $tickets->where('status_id', $status->id)->count() }}
                        </span>
                    </div>
                </div>

                <!-- Cards -->
                <div class="p-4 space-y-3 min-h-96 max-h-96 overflow-y-auto" 
                     wire:sortable="updateTicketStatus" 
                     wire:sortable-group="tickets">
                    @foreach($tickets->where('status_id', $status->id) as $ticket)
                        <div wire:sortable.item="{{ $ticket->id }}" 
                             wire:key="ticket-{{ $ticket->id }}"
                             class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow cursor-move">
                            
                            <!-- Ticket Header -->
                            <div class="flex items-start justify-between mb-2">
                                <span class="text-xs font-medium text-gray-500">#{{ $ticket->id }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                           {{ $ticket->priority->color_class ?? 'bg-sky-100 text-gray-800' }}">
                                    {{ $ticket->priority->name ?? '-' }}
                                </span>
                            </div>

                            <!-- Ticket Subject -->
                            <h4 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">
                                {{ $ticket->subject }}
                            </h4>

                            <!-- Ticket Meta -->
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <div class="flex items-center">
                                    <span>{{ $ticket->user->name ?? 'مهمان' }}</span>
                                </div>
                                <div>
                                    {{ $ticket->updated_at->diffForHumans() }}
                                </div>
                            </div>

                            <!-- Category -->
                            @if($ticket->category)
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-sky-100 text-sky-800">
                                        {{ $ticket->category->name }}
                                    </span>
                                </div>
                            @endif

                            <!-- Assigned Agent -->
                            @if($ticket->assigned_to)
                                <div class="mt-2 flex items-center">
                                    <div class="w-6 h-6 bg-sky-300 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600">
                                            {{ substr($ticket->assignedTo->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <span class="mr-2 text-xs text-gray-600">{{ $ticket->assignedTo->name }}</span>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="mt-3 flex items-center justify-end space-x-2 space-x-reverse">
                                <a href="{{ route('filament.admin.resources.tickets.view', $ticket) }}" 
                                   class="text-sky-600 hover:text-sky-800 text-xs">مشاهده</a>
                                <a href="{{ route('filament.admin.resources.tickets.edit', $ticket) }}" 
                                   class="text-green-600 hover:text-green-800 text-xs">ویرایش</a>
                            </div>
                        </div>
                    @endforeach

                    <!-- Add New Card Placeholder -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <button class="text-gray-400 hover:text-gray-600 text-sm">
                            + افزودن تیکت جدید
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
</style>
