{{-- resources/views/components/home-poster-card.blade.php --}}
@props(['film', 'fetchpriority' => null])

<div class="child">
    <x-film-image :film="$film" variant="largeThumb" :fetchpriority="$fetchpriority" class="home-poster-img" />
    <a href="{{ $film->url }}">
        <p>{{ $film->title }}</p>
    </a>
</div>
