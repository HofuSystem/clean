@extends('layouts.landing')

@section('title', 'الرئيسية | Clean Station')

@section('content')
    
    @foreach($page->sections as $section)
        @include('landing.sections.' . $section->template)
    @endforeach

@endsection
