@extends('front.services.custom.upper-base')

@section('submit_text', 'استعلام سوابق')

@section('form_action', url()->current())

@section('form_fields')
    @include('front.services.custom.partials.car-plate-field')
    @include('front.services.custom.partials.national-code-field')
@endsection 