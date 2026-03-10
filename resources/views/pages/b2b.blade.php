@extends('layouts.landing')

@section('title', 'قطاع الأعمال | Clean Station')

@section('content')
    <div class="pt-20">
        
        @foreach($page->sections as $section)
            @include('landing.sections.' . $section->template)
        @endforeach
    </div>
@endsection
