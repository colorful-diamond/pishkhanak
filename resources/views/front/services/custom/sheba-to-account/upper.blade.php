@extends('front.services.custom.upper-base')

@section('service_title', 'تبدیل شماره شبا به حساب')

@section('submit_text', 'تبدیل به حساب')

@section('form_fields')
    @include('front.services.custom.partials.iban-field')
@endsection

@section('results_section')
    @include('front.services.custom.partials.results-section')
@endsection

@section('bank_slider_section')
    @if(isset($banks) && count($banks) > 0)
        @include('front.components.bank-slider', ['banks' => $banks])
    @endif
@endsection
 