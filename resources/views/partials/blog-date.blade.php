@if($post->publication_date)
    <time datetime="{{ $post->publication_date->toIso8601String() }}">{{ $post->publication_date->locale(app()->getLocale())->translatedFormat('j F Y') }}</time>
@endif
