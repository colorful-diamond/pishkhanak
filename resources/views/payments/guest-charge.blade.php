@extends('front.layouts.app')

@section('content')
<div class="bg-sky-50 min-h-screen/2/2">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">درخواست شما آماده پردازش است!</h1>
                    <p class="text-gray-600 text-sm">برای تکمیل فرآیند، لطفاً کیف پول خود را شارژ کنید</p>
                </div>
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-sm">
                        <p class="text-gray-900 font-medium">کاربر مهمان</p>
                        <p class="text-gray-500">{{ $phoneNumber ?? 'شماره موبایل وارد نشده' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Status -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-green-800">درخواست شما با موفقیت ثبت شد</h3>
                    <p class="text-sm text-green-700 mt-1">
                        اطلاعات وارد شده تایید شد و درخواست شما آماده پردازش است. 
                        برای تکمیل فرآیند، لطفاً کیف پول خود را شارژ کنید.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Service Information -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-primary-lighter rounded-full mx-auto mb-4 flex items-center justify-center">
                            @if($service->icon)
                                <img src="{{ $service->icon }}" alt="{{ $service->title }}" class="w-8 h-8">
                            @else
                                <svg class="w-8 h-8 text-primary-normal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $service->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $service->short_title ?? $service->summary }}</p>
                    </div>

                    @if($service->description)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">توضیحات سرویس:</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ Str::limit($service->description, 200) }}</p>
                    </div>
                    @endif

                    <!-- Service Data Preview -->
                    @if($serviceData && count($serviceData) > 0)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">اطلاعات وارد شده:</h4>
                        <div class="space-y-2">
                            @foreach($serviceData as $key => $value)
                                @if(!is_array($value) && !is_object($value) && $value)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span class="text-gray-900 font-medium">{{ $value }}</span>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Cost Breakdown -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">هزینه سرویس:</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">قیمت سرویس:</span>
                                <span class="text-gray-900">{{ number_format($service->price) }} تومان</span>
                            </div>
                            @if($service->cost && $service->cost != $service->price)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">هزینه پردازش:</span>
                                <span class="text-gray-900">{{ number_format($service->cost) }} تومان</span>
                            </div>
                            @endif
                            <div class="border-t border-gray-100 pt-2 flex justify-between">
                                <span class="text-sm font-medium text-gray-900">مجموع:</span>
                                <span class="text-lg font-bold text-primary-normal">{{ number_format($service->price) }} تومان</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="lg:col-span-2">
                <!-- Alert Message -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-amber-400 mt-0.5 ml-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-amber-800 mb-1">برای استفاده از این سرویس، ابتدا کیف پول خود را شارژ کنید</h4>
                            <p class="text-sm text-amber-700">پس از پرداخت موفق، شماره موبایل خود را وارد کرده و پس از ورود، سرویس به طور خودکار پردازش خواهد شد.</p>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mt-0.5 ml-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-red-800 mb-1">خطا در پردازش درخواست</h4>
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Payment Form -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">شارژ کیف پول مهمان</h3>
                        <p class="text-sm text-gray-600">برای پرداخت سرویس کیف پول خود را شارژ کنید</p>
                    </div>

                    <form action="{{ route('guest.payment.charge') }}" method="POST" class="p-6">
                        @csrf
                        
                        <!-- Hidden Fields -->
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <input type="hidden" name="service_data" value="{{ base64_encode(json_encode($serviceData)) }}">
                        <input type="hidden" name="amount" value="{{ $service->price }}">
                        <input type="hidden" name="guest_session_token" value="{{ session('guest_session_token', Str::random(32)) }}">
                        <input type="hidden" name="service_request_hash" value="{{ $serviceRequestHash }}">
                        
                        <!-- Payment Info -->
                        <div class="bg-sky-50 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-sky-600 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="text-sm text-sky-800">
                                    <p class="font-medium mb-1">نحوه دریافت نتیجه:</p>
                                    <p>پس از پرداخت موفق، شماره موبایل خود را برای دریافت نتیجه وارد کنید</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Gateway Selection -->
                        @if($gateways && $gateways->count() > 1)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">انتخاب درگاه پرداخت</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($gateways as $gateway)
                                <label class="relative">
                                    <input type="radio" 
                                           name="gateway_id" 
                                           value="{{ $gateway->id }}"
                                           class="sr-only peer"
                                           {{ $loop->first ? 'checked' : '' }}>
                                    <div class="p-4 border border-gray-300 rounded-lg cursor-pointer peer-checked:border-primary-normal peer-checked:bg-primary-lightest transition-all hover:border-gray-400">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $gateway->name }}</h4>
                                                @if($gateway->description)
                                                <p class="text-xs text-gray-600 mt-1">{{ $gateway->description }}</p>
                                                @endif
                                            </div>
                                            @if($gateway->logo_url)
                                            <img src="{{ $gateway->logo_url }}" alt="{{ $gateway->name }}" class="h-8 w-auto">
                                            @endif
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <input type="hidden" name="gateway_id" value="{{ $gateways->first()->id ?? '' }}">
                        @endif

                        <!-- Cost Summary -->
                        <div class="bg-sky-50 rounded-lg p-4 mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">خلاصه پرداخت</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">نوع سرویس:</span>
                                    <span class="text-gray-900">{{ $service->title }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">هزینه سرویس:</span>
                                    <span class="text-gray-900">{{ number_format($service->price) }} تومان</span>
                                </div>
                                @if($gateways->first() && $gateways->first()->calculateFee($service->price) > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">کارمزد درگاه:</span>
                                    <span class="text-gray-900">{{ number_format($gateways->first()->calculateFee($service->price)) }} تومان</span>
                                </div>
                                @endif
                                <div class="border-t border-gray-200 pt-2 flex justify-between">
                                    <span class="font-medium text-gray-900">مجموع:</span>
                                    <span class="font-bold text-lg text-primary-normal">{{ number_format($service->price) }} تومان</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full bg-primary-normal text-white py-3 px-4 rounded-lg font-medium hover:bg-primary-dark transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-normal focus:ring-offset-2">
                            پرداخت و ادامه
                        </button>
                    </form>
                </div>

                <!-- Process Steps -->
                <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h4 class="font-medium text-gray-900 mb-4">مراحل پردازش سرویس</h4>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-primary-normal text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">1</div>
                            <div>
                                <h5 class="font-medium text-gray-900">پرداخت هزینه سرویس</h5>
                                <p class="text-sm text-gray-600">ابتدا هزینه سرویس را از طریق درگاه پرداخت پرداخت کنید</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-primary-normal text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">2</div>
                            <div>
                                <h5 class="font-medium text-gray-900">تایید شماره موبایل</h5>
                                <p class="text-sm text-gray-600">پس از پرداخت موفق، شماره موبایل خود را تایید کنید</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-primary-normal text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">3</div>
                            <div>
                                <h5 class="font-medium text-gray-900">ورود به حساب کاربری</h5>
                                <p class="text-sm text-gray-600">با همان شماره موبایل وارد حساب کاربری شوید</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-primary-normal text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">4</div>
                            <div>
                                <h5 class="font-medium text-gray-900">پردازش خودکار سرویس</h5>
                                <p class="text-sm text-gray-600">سرویس شما به طور خودکار پردازش و نتیجه نمایش داده می‌شود</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format phone number input
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            e.target.value = value;
        });
    }

    // Store session token in hidden storage for later use
    const guestToken = document.querySelector('input[name="guest_session_token"]').value;
    sessionStorage.setItem('guest_session_token', guestToken);
});
</script>
@endsection 