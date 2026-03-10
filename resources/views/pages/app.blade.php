@extends('layouts.landing')
@section('title', 'تطبيق كلين ستيشن | Clean Station App')
@section('content')
    <div class="pt-20">
        @foreach($page->sections as $section)
            @include('landing.sections.' . $section->template)
        @endforeach
    </div>
@endsection
