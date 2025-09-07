@extends('front.services.custom.upper-base')

@section('submit_text', 'استعلام حکم بازنشگان تامین اجتماعی')

@section('form_fields')
    @include('front.services.custom.partials.national-code-field')
    @include('front.services.custom.partials.mobile-field')
@endsection 