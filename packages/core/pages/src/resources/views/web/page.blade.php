@extends('pages::web.layout.app')

@section('title', 'Home - Spa')

@section('content')
    @foreach($page->sections as $section)
        @include('pages::web.sections.' . $section->template,['section' => $section])
    @endforeach

@endsection 