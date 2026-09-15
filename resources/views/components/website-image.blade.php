@props(['src', 'width' => null, 'height' => null])
@php
    $image = \App\Support\WebsiteImages::attributes($src);
@endphp
<img src="{{ $image['src'] ?? $src }}"
     @if($image)
         width="{{ $image['width'] }}" height="{{ $image['height'] }}" srcset="{{ $image['srcset'] }}"
     @else
         @if($width) width="{{ $width }}" @endif
         @if($height) height="{{ $height }}" @endif
     @endif
     {{ $attributes }}>
