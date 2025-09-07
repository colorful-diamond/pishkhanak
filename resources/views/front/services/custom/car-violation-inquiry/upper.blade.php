@extends('front.services.custom.upper-base')

@section('service_title', 'استعلام خلافی خودرو')

@section('submit_text', 'استعلام خلافی')

@section('form_fields')
    @include('front.services.custom.partials.mobile-national-plate-fields')
@endsection

@section('other_services_section')
    <!-- Other Vehicle Services -->
    <div class="mt-8 mb-6">
        <h3 class="text-lg font-bold text-dark-sky-600 mb-4">سایر خدمات خودرویی</h3>
        <div class="grid grid-cols-2 gap-4">
            <!-- Row 1 -->
            <a href="{{ route('services.show', ['slug1' => 'motor-violation-inquiry']) }}" 
               class="flex items-center justify-center p-4 bg-white border border-primary-normal rounded-xl hover:bg-primary-normal hover:text-white transition-all duration-300 group">
                <div class="text-center">
                    <svg class="w-7 h-7 text-primary-normal mx-auto mb-3 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-primary-dark group-hover:text-white transition-colors">خلافی موتور</span>
                </div>
            </a>

            <a href="{{ route('services.show', ['slug1' => 'negative-license-score']) }}" 
               class="flex items-center justify-center p-4 bg-white border border-primary-normal rounded-xl hover:bg-primary-normal hover:text-white transition-all duration-300 group">
                <div class="text-center">
                    <svg class="w-7 h-7 text-primary-normal mx-auto mb-3 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-primary-dark group-hover:text-white transition-colors">نمره منفی گواهینامه</span>
                </div>
            </a>

            <!-- Row 2 -->
            <a href="{{ route('services.show', ['slug1' => 'vehicle-info-inquiry']) }}" 
               class="flex items-center justify-center p-4 bg-white border border-primary-normal rounded-xl hover:bg-primary-normal hover:text-white transition-all duration-300 group">
                <div class="text-center">
                    <svg class="w-7 h-7 text-primary-normal mx-auto mb-3 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-primary-dark group-hover:text-white transition-colors">اطلاعات خودرو</span>
                </div>
            </a>

            <a href="{{ route('services.show', ['slug1' => 'toll-road-inquiry']) }}" 
               class="flex items-center justify-center p-4 bg-white border border-primary-normal rounded-xl hover:bg-primary-normal hover:text-white transition-all duration-300 group">
                <div class="text-center">
                    <svg class="w-7 h-7 text-primary-normal mx-auto mb-3 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-primary-dark group-hover:text-white transition-colors">عوارض آزادراهی</span>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('additional_info')
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-500 mt-0.5 ml-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-700">
                <p class="font-semibold mb-2">نکات مهم:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>شماره پلاک را به صورت کامل وارد کنید</li>
                    <li>کد ملی باید متعلق به مالک خودرو باشد</li>
                    <li>شماره موبایل جهت تایید هویت الزامی است</li>
                    <li>نتایج شامل تمام خلافی‌های قابل پرداخت و غیرقابل پرداخت است</li>
                    <li>امکان پرداخت آنلاین خلافی‌ها وجود دارد</li>
                </ul>
            </div>
        </div>
    </div>
@endsection 