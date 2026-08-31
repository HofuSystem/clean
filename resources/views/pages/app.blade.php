@extends('layouts.landing')
@section('title', 'تطبيق كلين ستيشن | Clean Station App')
@section('content')
    <div class="">
        @foreach($page->sections as $section)
            @include('landing.sections.' . $section->template)
        @endforeach
    </div>
@endsection
