@props(['movies', 'wrapperClass' => 'sidetitle mt-5'])

<div class="{{ $wrapperClass }}">
    <h2>Скоро в кіно (API)</h2>
</div>
@foreach($movies as $movie)
    <div class="py-2">
        <a href="#" class="d-block fw-semibold">
            {{ $movie['title'] }}
        </a>
        <small class="text-muted">
            <i class="bi bi-calendar-event me-1"></i>
            {{ $movie['release_date'] }}
        </small>
    </div>
    @unless($loop->last)
        <hr class="my-2">
    @endunless
@endforeach
