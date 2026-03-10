@extends('layouts.landing')

@section('title', $page->title_ar . ' | Clean Station')

@section('content')
<div class="pt-24 pb-16 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 mb-4">
                <span class="lang-ar">{{ $page->title_ar }}</span>
                <span class="lang-en">{{ $page->title_en }}</span>
            </h1>
            <div class="h-1.5 w-24 bg-brand-500 mx-auto rounded-full"></div>
        </div>

        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-sm border border-gray-100 prose prose-lg max-w-none" data-aos="fade-up" data-aos-delay="100">
            <div class="lang-ar text-gray-600 leading-loose">
                {!! $page->content_ar !!}
            </div>
            <div class="lang-en text-gray-600 leading-loose hidden">
                {!! $page->content_en !!}
            </div>
        </div>
    </div>
</div>
@endsection
