{{-- resources/views/components/film-image.blade.php --}}
@props([
    'film',
    'variant' => 'thumb',
    'loading' => 'lazy',
    'alt' => null,
    'fetchpriority' => null,
])

@php
    $resolver = app(\App\Media\FilmImageResolver::class);

    $variants = [
        'thumb' => [
            'src' => $resolver->thumb($film),
            'width' => \App\Constants\ImageSizes::POSTER_THUMB_WIDTH,
            'height' => \App\Constants\ImageSizes::POSTER_THUMB_HEIGHT,
        ],
        'image' => [
            'src' => $resolver->image($film),
            'width' => \App\Constants\ImageSizes::POSTER_WIDTH,
            'height' => \App\Constants\ImageSizes::POSTER_HEIGHT,
        ],
        'largeThumb' => [
            'src' => $resolver->largeThumb($film),
            'width' => \App\Constants\ImageSizes::LARGE_THUMB_WIDTH,
            'height' => \App\Constants\ImageSizes::LARGE_THUMB_HEIGHT,
        ],
    ];

    $current = $variants[$variant];
@endphp

<img src="{{ $current['src'] }}"
     alt="{{ $alt ?? $film->title }}"
     width="{{ $current['width'] }}"
     height="{{ $current['height'] }}"
     @if($loading) loading="{{ $loading }}" @endif
     @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
     decoding="async"
    {{ $attributes->class(['film-img']) }}>
