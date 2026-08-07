<div class="row-sidebar">

    {{-- =========================
         POSTER
         ========================= --}}

    <img
        width="685"
        height="390"
        src="{{ app(\App\Media\FilmImageResolver::class)->image($film) }}"
        class="attachment-large size-large wp-post-image"
        alt="{{ $film->title }}"
        decoding="async"
        fetchpriority="high"
        sizes="(max-width: 685px) 100vw, 685px"
    >


    {{-- =========================
         GALLERY
         ========================= --}}

    @include('films.inc.partials-sidebar.gallery', [
        'images' => app(\App\Media\FilmImageResolver::class)->gallery($film)
    ])


    {{-- =========================
         RELEASE DATE
         ========================= --}}

    <p class="mt-4 mb-2">
        Дата виходу: {{ $film->display_date }}
    </p>


    {{-- =========================
         QUICK INFO
         ========================= --}}

    <div class="film-sidebar-info">

        {{-- Вік --}}
        <div class="blog-title-area">
            <div class="tag-cloud-single">

                <span class="first-col-film">
                    <a href="{{ route('ages.index') }}">
                        Вік:
                    </a>
                </span>

                <span>
                    <small>
                        <a href="{{ route('ages.show', ['slug' => $film->age->slug]) }}">
                            {{ $film->age->title }}
                        </a>
                    </small>
                </span>

            </div>
        </div>


        {{-- Якість --}}
        <div class="blog-title-area">
            <div class="tag-cloud-single">

                <span class="first-col-film">
                    <a href="{{ route('qualities.index') }}">
                        Якість відео:
                    </a>
                </span>

                <span>
                    <small>
                        <a href="{{ route('qualities.show', ['slug' => $film->quality->slug]) }}">
                            {{ $film->quality->title }}
                        </a>
                    </small>
                </span>

            </div>
        </div>


        {{-- Рейтинг --}}
        <div class="blog-title-area">
            <div class="tag-cloud-single">

                <span class="first-col-film">
                    <a href="{{ route('ratings.index') }}">
                        Рейтинг:
                    </a>
                </span>

                <span>
                    <small>
                        <a href="{{ route('ratings.show', ['slug' => $film->rating->slug]) }}">
                            {{ $film->rating->title }}
                        </a>
                    </small>
                </span>

            </div>
        </div>


        {{-- Добірки --}}
        @if($film->selections->count())
            <div class="blog-title-area">
                <div class="tag-cloud-single">

                    <span class="first-col-film">
                        <a href="{{ route('selections.index') }}">
                            Добірки:
                        </a>
                    </span>

                    <span>
                        @foreach($film->selections as $selection)
                            <small>
                                <a href="{{ route('selections.show', ['slug' => $selection->slug]) }}">
                                    {{ $selection->title }}
                                </a>
                            </small>
                        @endforeach
                    </span>

                </div>
            </div>
        @endif


        {{-- Озвучка --}}
        @if($film->languages->count())
            <div class="blog-title-area">
                <div class="tag-cloud-single">

                    <span class="first-col-film">
                        <a href="{{ route('languages.index') }}">
                            Озвучка:
                        </a>
                    </span>

                    <span>
                        @foreach($film->languages as $language)
                            <small>
                                <a href="{{ route('languages.show', ['slug' => $language->slug]) }}">
                                    {{ $language->title }}
                                </a>@if(!$loop->last), @endif
                            </small>
                        @endforeach
                    </span>

                </div>
            </div>
        @endif


        {{-- Субтитри --}}
        @if($film->captions->count())
            <div class="blog-title-area">
                <div class="tag-cloud-single">

                    <span class="first-col-film">
                        <a href="{{ route('captions.index') }}">
                            Субтитри:
                        </a>
                    </span>

                    <span>
                        @foreach($film->captions as $caption)
                            <small>
                                <a href="{{ route('captions.show', ['slug' => $caption->slug]) }}">
                                    {{ $caption->title }}
                                </a>@if(!$loop->last), @endif
                            </small>
                        @endforeach
                    </span>

                </div>
            </div>
        @endif


        {{-- IMDB --}}
        @if($film->imdb_rating)
            <div class="blog-title-area">
                <div class="tag-cloud-single">

                    <span class="first-col-film">
                        IMDB:
                    </span>

                    <span>
                        <small>
                            ⭐ {{ $film->imdb_rating }} / 10
                        </small>
                    </span>

                </div>
            </div>
        @endif


        {{-- Примітка --}}
        @isset($film->note)
            <div class="blog-title-area">
                <div class="tag-cloud-single">

                    <span class="first-col-film">
                        Примітка:
                    </span>

                    <span>
                        <small>
                            {{ $film->note }}
                        </small>
                    </span>

                </div>
            </div>
        @endisset

    </div>


    {{-- =========================
         BEST FILMS
         ========================= --}}

    <div class="sidetitle bestfilmss mt-5">

        <h3>
            Кращі {{ $film->category->title }} (likes)
        </h3>

        <ul>
            @foreach($bestFilms as $sidefilm)

                <hr>

                <li>
                    <a href="{{ route('single', [
                        'category' => $film->category->slug,
                        'slug' => $sidefilm->slug
                    ]) }}">
                        {{ $sidefilm->title }}
                    </a>
                </li>

            @endforeach
        </ul>

    </div>


    {{-- =========================
         FEATURED FILMS
         ========================= --}}

    <div class="sidetitle text-start mt-5">

        <h3>
            Обрані Фільми
        </h3>

        <ul>
            @foreach($featuredFilms as $featuredFilm)

                <hr>

                <li>
                    <a href="{{ route('single', [
                        'category' => $film->category->slug,
                        'slug' => $featuredFilm->slug
                    ]) }}">
                        {{ $featuredFilm->title }}
                    </a>
                </li>

            @endforeach
        </ul>

    </div>

    <hr>


    {{-- =========================
         SUBSCRIBE
         ========================= --}}

    <div class="sidetitle text-start mt-5">

        <h3 class="sidebar-title">
            Підписатися
        </h3>

    </div>

    @include('admin.layouts.alerts')

    <form
        action="{{ route('subscribe') }}"
        method="POST"
        class="mb-4"
    >
        @csrf

        <div class="input-group">

            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Ваш Email"
            >

            <button
                class="btn btn-dark"
                type="submit"
            >
                <i class="bi bi-send"></i>
            </button>

        </div>
    </form>

    <hr>

</div>


{{-- =========================
     UPCOMING MOVIES
     ========================= --}}

<div class="sidetitle text-start mt-5">

    <h3 class="sidebar-title mt-4">
        Скоро в кіно (API)
    </h3>

</div>

@foreach($upcomingMovies as $movie)

    <div class="py-2">

        <a
            href="#"
            class="d-block fw-semibold"
        >
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
