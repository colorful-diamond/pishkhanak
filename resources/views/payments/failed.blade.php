@extends('front.layouts.app')

@section('title', 'پرداخت ناموفق')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Failed Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">پرداخت ناموفق</h1>
                <p class="text-red-100">متأسفانه تراکنش شما انجام نشد</p>
            </div>

            <!-- Transaction Details -->
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">شناسه تراکنش</label>
                        <div class="text-lg font-medium text-dark-sky-600 font-mono">{{ $transaction->uuid }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">مبلغ تراکنش</label>
                        <div class="text-lg font-bold text-red-600">{{ number_format($transaction->amount) }} تومان</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">درگاه پرداخت</label>
                        <div class="text-lg font-medium text-dark-sky-600">{{ $transaction->paymentGateway->name }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">وضعیت</label>
                        <div class="text-lg font-medium">
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $transaction->getStatusLabel() }}
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">زمان تلاش</label>
                        <div class="text-lg font-medium text-dark-sky-600">
                            {{ $transaction->failed_at ? \Verta::instance($transaction->failed_at)->format('Y/m/d H:i') : \Verta::instance($transaction->created_at)->format('Y/m/d H:i') }}
                        </div>
                    </div>
                    
                    @if($transaction->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">توضیحات</label>
                            <div class="text-lg font-medium text-dark-sky-600">{{ $transaction->description }}</div>
                        </div>
                    @endif
                </div>

                @if($transaction->metadata && isset($transaction->metadata['failure_reason']))
                    <!-- Failure Reason -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="font-medium text-red-800 mb-2">دلیل عدم موفقیت</h3>
                        <p class="text-red-700">{{ $transaction->metadata['failure_reason'] }}</p>
                    </div>
                @endif

                <!-- Common Failure Reasons -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h3 class="font-medium text-yellow-800 mb-3">دلایل احتمالی عدم موفقیت پرداخت</h3>
                    <ul class="text-yellow-700 text-sm space-y-1">
                        <li>• موجودی کافی در حساب نداشتن</li>
                        <li>• اشتباه در وارد کردن اطلاعات کارت</li>
                        <li>• مشکل در اتصال اینترنت</li>
                        <li>• محدودیت در کارت بانکی</li>
                        <li>• تجاوز از حد مجاز تراکنش روزانه</li>
                        <li>• خطای موقت در سیستم بانکی</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="{{ route('payments.form', ['amount' => $transaction->amount, 'description' => $transaction->description]) }}" 
                       class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-medium py-3 px-6 rounded-lg text-center transition-colors">
                        تلاش مجدد
                    </a>
                    
                    <a href="{{ route('app.user.history') }}" 
                       class="flex-1 bg-sky-100 hover:bg-sky-200 text-dark-sky-500 font-medium py-3 px-6 rounded-lg text-center transition-colors">
                        سوابق تراکنش
                    </a>
                    
                    <a href="{{ url('/') }}" 
                       class="flex-1 bg-sky-100 hover:bg-sky-200 text-dark-sky-500 font-medium py-3 px-6 rounded-lg text-center transition-colors">
                        صفحه اصلی
                    </a>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-6 bg-sky-50 border border-sky-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-sky-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="mr-3">
                    <h3 class="text-sm font-medium text-sky-800">نیاز به کمک دارید؟</h3>
                    <div class="text-sm text-sky-700 mt-1 space-y-1">
                        <p>• در صورت بروز مشکل مکرر، با بانک صادرکننده کارت تماس بگیرید</p>
                        <p>• برای حل مشکلات فنی، با پشتیبانی سایت در تماس باشید</p>
                        <p>• از درگاه پرداخت دیگری استفاده کنید</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Contact -->
        <div class="mt-4 text-center">
            <p class="text-dark-sky-500 text-sm">
                در صورت تکرار مشکل، با پشتیبانی تماس بگیرید:
                <a href="tel:+982112345678" class="text-sky-600 hover:text-sky-800 font-medium">021-12345678</a>
            </p>
        </div>
    </div>
</div>
@endsection 