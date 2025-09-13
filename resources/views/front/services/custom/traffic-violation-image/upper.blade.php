@extends('front.services.custom.upper-base')

@section('service_title', 'دریافت تصویر تخلف')

@section('submit_text', 'دریافت تصویر')

@section('form_fields')
    @include('front.services.custom.partials.mobile-field')
    
    <div class="mt-4">
        @include('front.services.custom.partials.national-code-field')
    </div>
    
    <div class="mt-4">
        @include('front.services.custom.partials.violation-serial-field')
    </div>
@endsection 