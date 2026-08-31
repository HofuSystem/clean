@extends('layouts.landing')
@section('title', $title ?? (app()->getLocale() == 'ar' ? 'تغطية الأحياء والمدن | كلين ستيشن' : 'Coverage & Active Districts | Clean Station'))

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "{{ $title }}",
  "description": "{{ $description }}"
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "name": "{{ app()->getLocale() == 'ar' ? 'الرئيسية' : 'Home' }}",
    "item": "{{ url('/') }}"
  },{
    "@type": "ListItem",
    "position": 2,
    "name": "{{ app()->getLocale() == 'ar' ? 'تغطية الأحياء والمدن' : 'Coverage' }}"
  }]
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
            @include('landing.sections.coverage')
        @endif
    </div>
@endsection
