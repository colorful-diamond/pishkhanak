@extends('front.services.custom.upper-base')

@section('submit_text', 'بررسی رتبه اعتباری')

@section('form_action', url()->current())

@section('form_fields')
    @include('front.services.custom.partials.national-code-field')
    @include('front.services.custom.partials.mobile-field')
@endsection 