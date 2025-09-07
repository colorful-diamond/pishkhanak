@extends('front.layouts.app')

@section('title', 'جزئیات تراکنش')

@section('content')
<div class="min-h-screen/2 bg-sky-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-gray-900">جزئیات تراکنش</h1>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                            @if($transaction->status === 'completed') bg-green-100 text-green-800
                            @elseif($transaction->status === 'pending' || $transaction->status === 'processing') bg-yellow-100 text-yellow-800
                            @elseif($transaction->status === 'failed' || $transaction->status === 'cancelled' || $transaction->status === 'expired') bg-red-100 text-red-800
                            @elseif($transaction->status === 'refunded' || $transaction->status === 'partially_refunded') bg-sky-100 text-sky-800
                            @else bg-sky-100 text-gray-800
                            @endif">
                            {{ $transaction->getStatusLabel() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Transaction Details -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">اطلاعات پایه</h3>
                        
                        <div class="bg-sky-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">شناسه تراکنش</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $transaction->uuid }}</dd>
                        </div>

                        @if($transaction->gateway_reference_id)
                        <div class="bg-sky-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">شماره مرجع</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $transaction->gateway_reference_id }}</dd>
                        </div>
                        @endif

                        <div class="bg-sky-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">نوع تراکنش</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @switch($transaction->type)
                                    @case('wallet_charge')
                                        شارژ کیف‌پول
                                        @break
                                    @case('wallet_charge_for_service')
                                        شارژ کیف‌پول برای سرویس
                                        @break
                                    @case('service_payment')
                                        پرداخت سرویس
                                        @break
                                    @default
                                        {{ $transaction->type }}
                                @endswitch
                            </dd>
                        </div>

                        @if($transaction->description)
                        <div class="bg-sky-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">توضیحات</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transaction->description }}</dd>
                        </div>
                        @endif
                    </div>

                    <!-- Financial Details -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">جزئیات مالی</h3>
                        
                        <div class="bg-sky-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">مبلغ اصلی</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($transaction->amount) }} تومان</dd>
                        </div>

                        @if($transaction->tax_amount > 0)
                        <div class="bg-sky-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">مالیات</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($transaction->tax_amount) }} تومان</dd>
                        </div>
                        @endif

                        @if($transaction->gateway_fee > 0)
                        <div class="bg-sky-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">کارمزد درگاه</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($transaction->gateway_fee) }} تومان</dd>
                        </div>
                        @endif

                        <div class="bg-sky-50 p-4 rounded-lg border border-sky-200">
                            <dt class="text-sm font-medium text-sky-700">مبلغ نهایی</dt>
                            <dd class="mt-1 text-xl font-bold text-sky-900">{{ number_format($transaction->total_amount) }} تومان</dd>
                        </div>

                        <div class="bg-sky-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">درگاه پرداخت</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transaction->paymentGateway->name ?? 'نامشخص' }}</dd>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">زمان‌بندی</h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-2 h-2 bg-sky-500 rounded-full"></div>
                            <div>
                                <span class="text-sm font-medium text-gray-900">ایجاد تراکنش:</span>
                                <span class="text-sm text-gray-600">{{ \Verta::instance($transaction->created_at)->format('Y/m/d H:i:s') }}</span>
                            </div>
                        </div>

                        @if($transaction->processed_at)
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                            <div>
                                <span class="text-sm font-medium text-gray-900">پردازش:</span>
                                <span class="text-sm text-gray-600">{{ \Verta::instance($transaction->processed_at)->format('Y/m/d H:i:s') }}</span>
                            </div>
                        </div>
                        @endif

                        @if($transaction->completed_at)
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <div>
                                <span class="text-sm font-medium text-gray-900">تکمیل:</span>
                                <span class="text-sm text-gray-600">{{ \Verta::instance($transaction->completed_at)->format('Y/m/d H:i:s') }}</span>
                            </div>
                        </div>
                        @endif

                        @if($transaction->failed_at)
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                            <div>
                                <span class="text-sm font-medium text-gray-900">خرابی:</span>
                                <span class="text-sm text-gray-600">{{ \Verta::instance($transaction->failed_at)->format('Y/m/d H:i:s') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex space-x-4 space-x-reverse">
                        <a href="{{ route('app.user.history') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            بازگشت به سوابق
                        </a>

                        @if($transaction->isCompleted())
                        <a href="{{ route('payments.receipt', $transaction->uuid) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            دانلود رسید
                        </a>
                        @endif

                        @if($transaction->canBeRefunded())
                        <button onclick="openRefundModal('{{ $transaction->uuid }}', '{{ number_format($transaction->amount) }} تومان')"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            درخواست بازگردانی
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 