{{-- resources/views/components/film-section.blade.php --}}
@props(['films'])

@if($films->isNotEmpty())
    <section class="film-section">
        <h2>{{ $title }}</h2>
        <div class="other-films">
            @foreach($films as $film)
                <x-film-mini-card :film="$film" />
            @endforeach
        </div>
    </section>
@endif
