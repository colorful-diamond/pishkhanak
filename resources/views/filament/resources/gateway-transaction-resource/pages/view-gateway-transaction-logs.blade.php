<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Transaction Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">اطلاعات تراکنش</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <span class="text-sm text-gray-500">شناسه:</span>
                    <p class="font-medium">{{ $record->uuid }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">کاربر:</span>
                    <p class="font-medium">{{ $record->user->name }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">درگاه:</span>
                    <p class="font-medium">{{ $record->paymentGateway->name }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">وضعیت:</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                        @if($record->status === 'completed') bg-green-100 text-green-800
                        @elseif($record->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($record->status === 'processing') bg-sky-100 text-sky-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ $record->getStatusLabel() }}
                    </span>
                </div>
                <div>
                    <span class="text-sm text-gray-500">مبلغ:</span>
                    <p class="font-medium">{{ $record->getFormattedTotalAmount() }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">تاریخ ایجاد:</span>
                    <p class="font-medium">{{ \Verta::instance($record->created_at)->format('Y-m-d H:i:s') }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">آی‌پی کاربر:</span>
                    <p class="font-medium">{{ $record->user_ip ?? 'نامشخص' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">دستگاه:</span>
                    <p class="font-medium">{{ $record->user_device ?? 'نامشخص' }}</p>
                </div>
            </div>
        </div>

        <!-- Transaction Logs -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">لاگ‌های تراکنش ({{ $record->logs->count() }} مورد)</h2>
            </div>
            
            @if($record->logs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-sky-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">زمان</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عمل</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">منبع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">پیام</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">آی‌پی</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">زمان پاسخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">جزئیات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($record->logs()->orderBy('created_at', 'desc')->get() as $log)
                                <tr class="hover:bg-sky-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-sky-600">
                                        {{ \Verta::instance($log->created_at)->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($log->hasError()) bg-red-100 text-red-800
                                            @else {{ 'bg-' . $log->getActionBadgeClass() . '-100 text-' . $log->getActionBadgeClass() . '-800' }}
                                            @endif">
                                            {{ $log->getActionLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-sky-600">
                                        {{ $log->getSourceLabel() }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-dark-sky-600 max-w-xs truncate">
                                        {{ $log->message ?? 'بدون پیام' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->ip_address ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->getFormattedResponseTime() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($log->data || $log->request_data || $log->response_data || $log->error_message)
                                            <button type="button" 
                                                    onclick="toggleDetails('log-{{ $log->id }}')"
                                                    class="text-sky-600 hover:text-sky-900 text-xs">
                                                نمایش جزئیات
                                            </button>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                
                                <!-- Expandable Details Row -->
                                @if($log->data || $log->request_data || $log->response_data || $log->error_message)
                                    <tr id="log-{{ $log->id }}" class="hidden bg-sky-50">
                                        <td colspan="7" class="px-6 py-4">
                                            <div class="space-y-4">
                                                @if($log->error_message)
                                                    <div>
                                                        <h4 class="font-medium text-red-800 mb-2">خطا:</h4>
                                                        <div class="bg-red-50 p-3 rounded text-sm">
                                                            <p><strong>کد خطا:</strong> {{ $log->error_code ?? 'نامشخص' }}</p>
                                                            <p><strong>پیام خطا:</strong> {{ $log->error_message }}</p>
                                                            @if($log->stack_trace)
                                                                <details class="mt-2">
                                                                    <summary class="cursor-pointer text-red-700">Stack Trace</summary>
                                                                    <pre class="mt-2 text-xs bg-red-100 p-2 rounded overflow-x-auto">{{ $log->stack_trace }}</pre>
                                                                </details>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @if($log->request_data)
                                                    <div>
                                                        <h4 class="font-medium text-gray-800 mb-2">داده‌های درخواست:</h4>
                                                        <pre class="bg-sky-100 p-3 rounded text-xs overflow-x-auto">{{ json_encode($log->request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    </div>
                                                @endif
                                                
                                                @if($log->response_data)
                                                    <div>
                                                        <h4 class="font-medium text-gray-800 mb-2">داده‌های پاسخ:</h4>
                                                        <pre class="bg-sky-100 p-3 rounded text-xs overflow-x-auto">{{ json_encode($log->response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    </div>
                                                @endif
                                                
                                                @if($log->data)
                                                    <div>
                                                        <h4 class="font-medium text-gray-800 mb-2">داده‌های اضافی:</h4>
                                                        <pre class="bg-sky-100 p-3 rounded text-xs overflow-x-auto">{{ json_encode($log->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    </div>
                                                @endif
                                                
                                                @if($log->user_agent)
                                                    <div>
                                                        <h4 class="font-medium text-gray-800 mb-2">User Agent:</h4>
                                                        <p class="text-sm bg-sky-100 p-2 rounded">{{ $log->user_agent }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-8 text-center text-gray-500">
                    <p>هیچ لاگی برای این تراکنش ثبت نشده است.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleDetails(id) {
            const element = document.getElementById(id);
            element.classList.toggle('hidden');
        }
    </script>
</x-filament-panels::page> 