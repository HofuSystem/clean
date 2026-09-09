@extends('layouts.landing')
@section('title', 'المدونة | Clean Station')
@section('content')
    <h1 class="sr-only">{{ trim($title ?? '') ?: (app()->getLocale() === 'ar' ? 'المدونة' : 'Blog') }}</h1>
    <div class="">
        @foreach($page->sections as $section)
            @include('landing.sections.' . $section->template)
        @endforeach
    </div>
@endsection
