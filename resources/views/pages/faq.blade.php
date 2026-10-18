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
    $faqLocale = app()->getLocale();
    $faqsForSchema = \Core\Pages\Models\Faq::with('translations')->get()->map(function ($faq) use ($faqLocale) {
        $translation = $faq->translate($faqLocale);
        return [
            '@type' => 'Question',
            'name' => $translation->question ?? $faq->question,
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $translation->answer ?? $faq->answer],
        ];
    })->values()->all();
@endphp
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'inLanguage' => $faqLocale === 'ar' ? 'ar-SA' : 'en-SA', 'mainEntity' => $faqsForSchema], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
@endpush
