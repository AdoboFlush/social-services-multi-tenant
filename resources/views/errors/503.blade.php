@extends('errors::illustrated-layout')

@section('title', __('Service Unavailable'))
@section('code', '503')

@php $setting = \App\Setting::where('name','maintenanceText')->first() @endphp

@section('message')
    {!! $setting->value !!}
@endsection