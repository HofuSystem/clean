@extends('layouts.landing')
@section('title', $title)
@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Service",
  "name": "{{ $title }}",
  "provider": {
    "@type": "Organization",
    "name": "Clean Station"
  },
  "areaServed": {
    "@type": "City",
    "name": "Riyadh"
  },
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
    "name": "{{ app()->getLocale() == 'ar' ? 'الخدمات' : 'Services' }}",
    "item": "{{ route('services') }}"
  },{
    "@type": "ListItem",
    "position": 3,
    "name": "{{ $title }}"
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
            @include('landing.sections.single-service')
        @endif
    </div>
@endsection
