@extends('layouts.landing')

@section('title', $page->meta_title)

@section('content')
    
    @include('landing.sections.hero')

    @include('landing.sections.services')

    @include('landing.sections.app-features')

    @include('landing.sections.why-us')

    @include('landing.sections.b2b')

    @include('landing.sections.blogs')

    @include('landing.sections.contact')

@endsection
