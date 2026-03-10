@extends('layouts.landing')

@section('title', 'لماذا نحن | Clean Station')

@section('content')
    <div class="pt-24">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-black text-gray-900">{{ $page->title }}</h1>
            <div class="h-1.5 w-24 bg-brand-500 mx-auto rounded-full mt-4"></div>
        </div>

        @foreach($page->sections as $section)
            @include('landing.sections.' . $section->template)
        @endforeach
    </div>
@endsection
