<div class="row-sidebar">

    {{-- =========================
         POSTER
         ========================= --}}

    <x-film-image :film="$film" variant="image" fetchpriority="high"/>


    {{-- =========================
         GALLERY
         ========================= --}}

    @include('films.inc.partials-sidebar.gallery', [
    'images' => app(\App\Media\FilmImageResolver::class)->gallery($film),
    'fancyboxGroup' => 'gallery-desktop',
])



    {{-- =========================
         QUICK INFO
         ========================= --}}

    <div class="film-sidebar-info mt-4">

        {{-- Вік --}}
        @if($film->age)
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
        @endif


        {{-- Якість --}}
        @if($film->quality)
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
        @endif


        {{-- Рейтинг --}}
        @if($film->rating)
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
        @endif




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
                        Рейтинг IMDB:
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

    <x-film-sidebar-list :films="$bestFilms" :title="'Кращі ' . $film->category->title . ' (likes)'" wrapper-class="sidetitle bestfilmss mt-5" />


    {{-- =========================
         FEATURED FILMS
         ========================= --}}

    <x-film-sidebar-list :films="$featuredFilms" title="Обрані Фільми" wrapper-class="sidetitle text-start mt-5" />

    <hr>

    <x-film-sidebar-list :films="$bestFilms" :title="'Кращі ' . $film->category->title . ' (likes)'" wrapper-class="sidetitle bestfilmss mt-5" />



    {{-- =========================
         SUBSCRIBE
         ========================= --}}
    <div class="sidetitle text-start mt-5">

        <x-subscribe-form wrapper-class="sidetitle text-start mt-5" />

    <hr>
</div>


{{-- =========================
     UPCOMING MOVIES
     ========================= --}}

    <x-upcoming-movies :movies="$upcomingMovies" wrapper-class="sidetitle text-start mt-5" />

</div>
