@extends('layouts.landing')
@section('title', 'المدونة | Clean Station')
@section('content')
    <div class="">
        @foreach($page->sections as $section)
            @include('landing.sections.' . $section->template)
        @endforeach
    </div>
@endsection
