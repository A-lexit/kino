@foreach($films as $film)
    <div class="owl-film">
        <div class="owl-film-poster">

            <x-film-image :film="$film" variant="thumb" :loading="$loop->iteration > 8 ? 'lazy' : null"/>

            <a href="{{ $film->url }}"
               class="owl-film-title">
                {{ $film->title }}
            </a>
        </div>
    </div>
@endforeach
