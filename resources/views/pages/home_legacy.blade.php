@extends('layouts.landing')

@section('title', $title ?? 'الرئيسية (النسخة السابقة) | Clean Station')

@section('content')
    
    @foreach($page->sections as $section)
        @include('landing.sections.' . $section->template)
    @endforeach

@endsection
