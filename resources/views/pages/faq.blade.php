@extends('layouts.landing')
@section('title', 'الأسئلة الشائعة | Clean Station')
@section('content')
    <div class="">
        @foreach($page->sections as $section)
            @include('landing.sections.' . $section->template)
        @endforeach
    </div>
@endsection

@push('scripts')
@php
    $faqsForSchema = \Core\Pages\Models\Faq::with('translations')->get();
@endphp
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    @foreach($faqsForSchema as $index => $faq)
    {
      "@type": "Question",
      "name": {{ json_encode($faq->translate('ar')->question ?? $faq->question) }},
      "acceptedAnswer": {
        "@type": "Answer",
        "text": {{ json_encode($faq->translate('ar')->answer ?? $faq->answer) }}
      }
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endpush
