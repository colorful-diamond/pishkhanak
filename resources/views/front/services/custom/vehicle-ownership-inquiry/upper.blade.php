@extends('front.services.custom.upper-base')

@section('service_title', 'استعلام مالکیت خودرو')

@section('submit_text', 'استعلام مالکیت')

@section('form_fields')
    @include('front.services.custom.partials.vehicle-inquiry-fields')
@endsection 