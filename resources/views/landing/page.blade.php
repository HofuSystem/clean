@extends('layouts.landing')

@section('title', (app()->getLocale() === 'ar' ? $page->title_ar : $page->title_en) . ' | Clean Station')

@section('content')
<div class="pt-24 pb-16 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 mb-4">
                {{ app()->getLocale() === 'ar' ? $page->title_ar : $page->title_en }}
            </h1>
            <div class="h-1.5 w-24 bg-brand-500 mx-auto rounded-full"></div>
        </div>

        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-sm border border-gray-100 prose prose-lg max-w-none" data-aos="fade-up" data-aos-delay="100">
            <div class="text-gray-600 leading-loose">
                {!! app()->getLocale() === 'ar' ? $page->content_ar : $page->content_en !!}
            </div>
        </div>
    </div>
</div>
@endsection
