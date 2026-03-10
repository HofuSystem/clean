@extends('layouts.landing')

@section('title', 'لماذا نحن | Clean Station')

@section('content')
    <div class="pt-24">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-black text-gray-900">{{ $page->title }}</h1>
            <div class="h-1.5 w-24 bg-brand-500 mx-auto rounded-full mt-4"></div>
        </div>
        <section id="why-us" class="page-section bg-white py-16 md:py-24">
            <div class="max-w-7xl mx-auto px-4">
                
                {!! settings('terms_' . app()->getLocale()) !!}
                
            </div>
        </section>        
    </div>
@endsection
    
