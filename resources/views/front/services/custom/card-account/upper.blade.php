@extends('front.services.custom.upper-base')

@section('submit_text', 'دریافت شماره حساب')

@section('form_fields')
    @include('front.services.custom.partials.card-field')
@endsection

@section('other_services_section')
    <!-- Other Services -->
    <div class="mt-8 mb-6">
        <div class="grid grid-cols-2 gap-4">
            <!-- Row 1 -->
            <a href="{{ route('services.show', ['slug1' => 'card-iban']) }}" 
               class="flex items-center justify-center p-4 bg-white border border-primary-normal rounded-xl hover:bg-primary-normal hover:text-white transition-all duration-300 group">
                <div class="text-center">
                    <svg class="w-7 h-7 text-primary-normal mx-auto mb-3 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-primary-dark group-hover:text-white transition-colors">کارت به شبا</span>
                </div>
            </a>
            
            <a href="{{ route('services.show', ['slug1' => 'account-iban']) }}" 
               class="flex items-center justify-center p-4 bg-white border border-primary-normal rounded-xl hover:bg-primary-normal hover:text-white transition-all duration-300 group">
                <div class="text-center">
                    <svg class="w-7 h-7 text-primary-normal mx-auto mb-3 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span class="text-sm font-semibold text-primary-dark group-hover:text-white transition-colors">حساب به شبا</span>
                </div>
            </a>
            
            <!-- Row 2 -->
            <a href="{{ route('services.show', ['slug1' => 'iban-account']) }}" 
               class="flex items-center justify-center p-4 bg-white border border-primary-normal rounded-xl hover:bg-primary-normal hover:text-white transition-all duration-300 group">
                <div class="text-center">
                    <svg class="w-7 h-7 text-primary-normal mx-auto mb-3 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span class="text-sm font-semibold text-primary-dark group-hover:text-white transition-colors">شبا به حساب</span>
                </div>
            </a>
            
            <a href="{{ route('services.show', ['slug1' => 'iban-check']) }}" 
               class="flex items-center justify-center p-4 bg-white border border-primary-normal rounded-xl hover:bg-primary-normal hover:text-white transition-all duration-300 group">
                <div class="text-center">
                    <svg class="w-7 h-7 text-primary-normal mx-auto mb-3 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-primary-dark group-hover:text-white transition-colors">بررسی شبا</span>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('bank_slider_section')
    @if(isset($banks) && count($banks) > 0)
        @include('front.components.bank-slider', ['banks' => $banks, 'serviceSlug' => 'card-account'])
    @endif
@endsection 