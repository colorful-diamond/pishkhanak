<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-dark-sky-600 mb-4">
                لاگ‌های تراکنش: {{ $record->uuid }}
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">کاربر</dt>
                    <dd class="mt-1 text-sm text-dark-sky-600">{{ $record->user->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">مبلغ</dt>
                    <dd class="mt-1 text-sm text-dark-sky-600">{{ $record->getFormattedTotalAmount() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">وضعیت</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $record->getStatusBadgeClass() }}-100 text-{{ $record->getStatusBadgeClass() }}-800">
                            {{ $record->getStatusLabel() }}
                        </span>
                    </dd>
                </div>
            </div>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page> 