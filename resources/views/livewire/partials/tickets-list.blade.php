<!-- Tickets List View -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" wire:model="selectAll" class="rounded border-gray-300">
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">شماره</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">موضوع</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">کاربر</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">دسته‌بندی</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اولویت</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">کارشناس</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">آخرین بروزرسانی</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tickets as $ticket)
                    <tr class="hover:bg-sky-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" 
                                   wire:model="selectedTickets" 
                                   value="{{ $ticket->id }}" 
                                   class="rounded border-gray-300">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $ticket->id }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <a href="{{ route('filament.admin.resources.tickets.view', $ticket) }}" 
                               class="text-sky-600 hover:text-sky-900 font-medium">
                                {{ Str::limit($ticket->subject, 50) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $ticket->user->name ?? 'مهمان' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $ticket->category->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                         {{ $ticket->priority->color_class ?? 'bg-sky-100 text-gray-800' }}">
                                {{ $ticket->priority->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                         {{ $ticket->status->color_class ?? 'bg-sky-100 text-gray-800' }}">
                                {{ $ticket->status->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $ticket->assigned_to ? $ticket->assignedTo->name : 'تخصیص نیافته' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $ticket->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <a href="{{ route('filament.admin.resources.tickets.view', $ticket) }}" 
                                   class="text-sky-600 hover:text-sky-900">مشاهده</a>
                                <a href="{{ route('filament.admin.resources.tickets.edit', $ticket) }}" 
                                   class="text-green-600 hover:text-green-900">ویرایش</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m12 0H6"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">تیکتی یافت نشد</h3>
                                <p class="mt-1 text-sm text-gray-500">هیچ تیکتی با فیلترهای انتخابی وجود ندارد.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $tickets->links() }}
    </div>
</div>
