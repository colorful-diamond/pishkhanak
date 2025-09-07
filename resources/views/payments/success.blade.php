@extends('front.layouts.app')

@section('title', 'پرداخت موفق')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Success Header -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">پرداخت موفق</h1>
                <p class="text-green-100">تراکنش شما با موفقیت انجام شد</p>
            </div>

            <!-- Transaction Details -->
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">شناسه تراکنش</label>
                        <div class="text-lg font-medium text-dark-sky-600 font-mono">{{ $transaction->uuid }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">مبلغ پرداختی</label>
                        <div class="text-lg font-bold text-green-600">{{ number_format($transaction->amount) }} تومان</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">درگاه پرداخت</label>
                        <div class="text-lg font-medium text-dark-sky-600">{{ $transaction->paymentGateway->name }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">تاریخ پرداخت</label>
                        <div class="text-lg font-medium text-dark-sky-600">
                            {{ $transaction->completed_at ? \Verta::instance($transaction->completed_at)->format('Y/m/d H:i') : \Verta::instance($transaction->created_at)->format('Y/m/d H:i') }}
                        </div>
                    </div>
                    
                    @if($transaction->gateway_reference)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">شماره مرجع</label>
                            <div class="text-lg font-medium text-dark-sky-600 font-mono">{{ $transaction->gateway_reference }}</div>
                        </div>
                    @endif
                    
                    @if($transaction->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">توضیحات</label>
                            <div class="text-lg font-medium text-dark-sky-600">{{ $transaction->description }}</div>
                        </div>
                    @endif
                </div>

                <!-- Amount Breakdown -->
                <div class="bg-sky-50 rounded-lg p-4">
                    <h3 class="font-medium text-dark-sky-600 mb-3">جزئیات مبلغ</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-dark-sky-500">مبلغ اصلی:</span>
                            <span class="font-medium">{{ number_format($transaction->amount) }} تومان</span>
                        </div>
                        @if($transaction->tax_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-dark-sky-500">مالیات:</span>
                                <span class="font-medium">{{ number_format($transaction->tax_amount) }} تومان</span>
                            </div>
                        @endif
                        @if($transaction->gateway_fee > 0)
                            <div class="flex justify-between">
                                <span class="text-dark-sky-500">کارمزد درگاه:</span>
                                <span class="font-medium">{{ number_format($transaction->gateway_fee) }} تومان</span>
                            </div>
                        @endif
                        <div class="border-t pt-2 flex justify-between">
                            <span class="text-dark-sky-600 font-semibold">مبلغ نهایی:</span>
                            <span class="font-bold text-green-600">{{ number_format($transaction->amount) }} تومان</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="{{ route('payments.receipt', $transaction->uuid) }}" 
                       class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-medium py-3 px-6 rounded-lg text-center transition-colors">
                        دانلود رسید
                    </a>
                    
                    <a href="{{ route('app.user.history') }}" 
                       class="flex-1 bg-sky-100 hover:bg-sky-200 text-dark-sky-500 font-medium py-3 px-6 rounded-lg text-center transition-colors">
                        سوابق تراکنش
                    </a>
                    
                    <a href="{{ route('payments.form') }}" 
                       class="flex-1 bg-green-100 hover:bg-green-200 text-green-700 font-medium py-3 px-6 rounded-lg text-center transition-colors">
                        پرداخت جدید
                    </a>
                </div>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="mt-6 bg-sky-50 border border-sky-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-sky-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="mr-3">
                    <h3 class="text-sm font-medium text-sky-800">نکات مهم</h3>
                    <div class="text-sm text-sky-700 mt-1 space-y-1">
                        <p>• شناسه تراکنش را برای مراجعات آینده نزد خود نگه دارید</p>
                        <p>• رسید پرداخت را دانلود و ذخیره کنید</p>
                        <p>• در صورت نیاز به بازگردانی وجه، از قسمت تاریخچه اقدام کنید</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 