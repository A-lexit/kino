@foreach($films as $film)
    <span>

<img src="{{ app(\App\Media\FilmImageResolver::class)->thumb($film) }}"
     alt="{{ $film->title }}"
     width="133"
     height="211"
     decoding="async"
     @if($loop->iteration > 8)
         loading="lazy"         {{-- Інші слайди чекають своєї черги --}}
             @endif
        />

        <a href="{{route('single', ['category' => $film->category->slug, 'slug' => $film->slug])}}">
            <p class="text-owlkarousel">{{$film->title}}</p>
        </a>
    </span>
@endforeach
