@extends('layouts.landing')
@section('title', $page->meta_title)
@section('content')
    @include('landing.sections.hero')
    @include('landing.sections.services')
    @include('landing.sections.app-features')
    @include('landing.sections.why-us')
    @include('landing.sections.b2b')
    @include('landing.sections.blogs')
    @if(view()->exists('landing.sections.testimonials')) @include('landing.sections.testimonials') @endif
    @include('landing.sections.contact')
@endsection
