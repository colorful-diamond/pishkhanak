@extends('front.services.custom.upper-base')

@section('service_title', 'استعلام و اعتبارسنجی شماره شبا')


@section('submit_text', 'استعلام شبا')

@section('form_fields')
    @include('front.services.custom.partials.iban-field')
@endsection

@section('bank_slider_section')
    @if(isset($banks) && count($banks) > 0)
        @include('front.components.bank-slider', ['banks' => $banks])
    @endif
@endsection
 