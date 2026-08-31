@extends('layouts.landing')
@section('title', $title ?? (app()->getLocale() == 'ar' ? 'قائمة الأسعار | كلين ستيشن' : 'Pricing List | Clean Station'))

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "{{ $title ?? (app()->getLocale() == 'ar' ? 'قائمة الأسعار | كلين ستيشن' : 'Pricing List | Clean Station') }}",
  "description": "{{ $description ?? '' }}"
}
</script>
@endsection

@section('content')
    <div class="">
        @if(isset($page) && isset($page->sections) && $page->sections->isNotEmpty())
            @foreach($page->sections as $section)
                @include('landing.sections.' . $section->template)
            @endforeach
        @else
            @include('landing.sections.pricing')
        @endif
    </div>
@endsection
