@extends('layouts.app')

@section('title', 'Grab One Golf Cart Rental | Best Prices in San Pedro, Belize')

@section('content')
    {{-- تقسيم الصفحة إلى أقسام منفصلة لسهولة الصيانة --}}
    @include('sections.hero')
    @include('sections.features')
    @include('sections.fleet')
    @include('sections.gallery')
    @include('sections.faq')
    @include('sections.contact')
@endsection