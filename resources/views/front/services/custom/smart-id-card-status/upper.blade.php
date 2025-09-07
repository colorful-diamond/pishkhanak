@extends('front.services.custom.upper-base')

@section('submit_text', 'استعلام وضعیت صدور کارت ملی')

@section('form_fields')
    @include('front.services.custom.partials.national-code-field')
    @include('front.services.custom.partials.mobile-field')
@endsection 