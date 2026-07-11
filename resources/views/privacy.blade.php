@extends('layouts.landing')

@section('title', $page->title . ' | Clean Station')

@section('content')
    <div class="pt-16 pb-20 bg-gray-50/50">
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-black text-gray-900">{{ $page->title }}</h1>
            <div class="h-1.5 w-20 bg-brand-500 mx-auto rounded-full mt-4 animate-pulse"></div>
        </div>
        <section class="page-section">
            <div class="max-w-4xl mx-auto px-4">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-100/50 p-6 md:p-12 policy-content">
                    {!! settings('policy_' . app()->getLocale()) !!}
                </div>
            </div>
        </section>        
    </div>
@endsection
    
