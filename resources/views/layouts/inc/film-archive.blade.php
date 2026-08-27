<div class="container-archive">
    @foreach($films as $film)
        <div class="child-archive">
            <div>

                <x-film-image :film="$film" variant="thumb" :fetchpriority="$loop->first ? 'high' : null"/>

            </div>

            <a href="{{ $film->url }}"><p>{{ $film->title }}</p></a>

        </div>
    @endforeach
</div>
