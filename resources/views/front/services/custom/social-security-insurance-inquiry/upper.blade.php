@extends('front.services.custom.upper-base')

@section('submit_text', 'استعلام سوابق تامین اجتماعی')

@section('form_fields')
    @include('front.services.custom.partials.national-code-field')
    @include('front.services.custom.partials.mobile-field')
@endsection 