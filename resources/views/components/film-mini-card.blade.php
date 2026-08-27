{{-- resources/views/components/film-mini-card.blade.php --}}
@props(['film'])

<div class="other-film">
    <x-film-image :film="$film" variant="thumb" />
    <a href="{{ $film->url }}">
        <span class="other-film-title">{{ $film->title }}</span>
    </a>
</div>
