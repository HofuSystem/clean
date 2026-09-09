@php
    $articleUrl = $resolvedCanonicalUrl ?? \App\Support\CanonicalUrl::fromRequest(request(), config('app.url'));
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $blog->title,
        'description' => trim($blog->meta_description ?? '') ?: \Illuminate\Support\Str::limit(strip_tags($blog->content ?? ''), 300),
        'url' => $articleUrl,
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $articleUrl],
        'inLanguage' => app()->getLocale(),
        'publisher' => ['@type' => 'Organization', 'name' => 'Clean Station', 'url' => rtrim(config('app.url'), '/')],
    ];
    if ($blog->published_at) {
        $articleSchema['datePublished'] = \Carbon\Carbon::parse($blog->published_at)->toIso8601String();
    }
    if ($blog->updated_at) {
        $articleSchema['dateModified'] = \Carbon\Carbon::parse($blog->updated_at)->toIso8601String();
    }
@endphp
<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
