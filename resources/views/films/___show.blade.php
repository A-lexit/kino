@extends('layouts/layout')

@include('layouts.inc.seo', [
    'title' => $film->title,
    'description' => $film->seoDescription()
])

@section('content')

    {{-- Breadcrumbs --}}
    <div class="container-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            [
                'title' => $film->category->title,
                'url' => route('categories.show', ['slug' => $film->category->slug])
            ],
            ['title' => $film->title, 'url' => null],
        ]])
    </div>


    <div class="container-arch film-page">

        {{-- =========================
             DESKTOP SIDEBAR
             ========================= --}}
        <aside class="film-sidebar film-sidebar-desktop">

            @include('films.inc.sidebar')

        </aside>


        {{-- =========================
             MAIN CONTENT
             ========================= --}}
        <main class="film-main">

            <header class="film-header">

                {{--<h1>{{ $film->title }}</h1>--}}
                <div class="film-title"><h1 >{{$film->title}}</h1></div>

                @if($film->origin_title)
                    <div class="film-origin-title">
                        {{ $film->origin_title }}
                    </div>
                @endif

            </header>


            {{-- =========================
                 MOBILE: POSTER
                 ========================= --}}
            <section class="film-mobile-block film-mobile-poster">

                <img
                    src="{{ app(\App\Media\FilmImageResolver::class)->image($film) }}"
                    class="film-poster"
                    alt="{{ $film->title }}"
                    fetchpriority="high"
                >

                @include('films.inc.partials-sidebar.gallery', [
                    'images' => app(\App\Media\FilmImageResolver::class)->gallery($film)
                ])

                <p class="film-release-date">
                    Дата виходу: {{ $film->display_date }}
                </p>

            </section>


            {{-- =========================
                 MOBILE: QUICK INFO
                 ========================= --}}
            <section class="film-mobile-block film-mobile-info">

                <h2>Інформація</h2>

                <div class="film-info-list">

                    <div class="film-info-row">
                        <span>Вік:</span>
                        <a href="{{ route('ages.show', ['slug' => $film->age->slug]) }}">
                            {{ $film->age->title }}
                        </a>
                    </div>

                    <div class="film-info-row">
                        <span>Якість відео:</span>
                        <a href="{{ route('qualities.show', ['slug' => $film->quality->slug]) }}">
                            {{ $film->quality->title }}
                        </a>
                    </div>

                    <div class="film-info-row">
                        <span>Рейтинг:</span>
                        <a href="{{ route('ratings.show', ['slug' => $film->rating->slug]) }}">
                            {{ $film->rating->title }}
                        </a>
                    </div>

                    @if($film->languages->count())
                        <div class="film-info-row">
                            <span>Озвучка:</span>
                            <div>
                                @foreach($film->languages as $language)
                                    <a href="{{ route('languages.show', ['slug' => $language->slug]) }}">
                                        {{ $language->title }}
                                    </a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($film->captions->count())
                        <div class="film-info-row">
                            <span>Субтитри:</span>
                            <div>
                                @foreach($film->captions as $caption)
                                    <a href="{{ route('captions.show', ['slug' => $caption->slug]) }}">
                                        {{ $caption->title }}
                                    </a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="film-info-row">
                            <span>Субтитри:</span>
                            <span>Немає</span>
                        </div>
                    @endif

                    @if($film->imdb_rating)
                        <div class="film-info-row">
                            <span>IMDB:</span>
                            <span>⭐ {{ $film->imdb_rating }} / 10</span>
                        </div>
                    @endif

                    @isset($film->note)
                        <div class="film-info-note">
                            <strong>Примітка:</strong>
                            <div>{{ $film->note }}</div>
                        </div>
                    @endisset

                </div>

            </section>


            {{-- =========================
                 MAIN FILM INFO
                 ========================= --}}
            <section class="film-details">

                @if($film->genres->count())
                    <div class="film-detail-row">
        <span class="film-detail-label">
            <a href="{{ route('genres.index') }}">Жанр:</a>
        </span>

                        <div class="span-show">
                            @foreach($film->genres as $genre)
                                <a href="{{ route('genres.show', ['slug' => $genre->slug]) }}">
                                    {{ $genre->title }}
                                </a>@if(!$loop->last), @endif
                            @endforeach
                        </div>
                    </div>
                @endif


                    <div class="film-detail-row">
    <span class="film-detail-label">
        <a href="{{ route('years.index') }}">Рік випуску:</a>
    </span>

                        <div class="span-show">
                            <a href="{{ route('years.show', ['slug' => $film->year->slug]) }}">
                                {{ $film->year->title }}
                            </a>
                        </div>
                    </div>


                    @if($film->category->isSeries())

                        <div class="film-detail-row">
                            <span class="film-detail-label">Сезони:</span>

                            <div>
                                {{ $film->season->title }}
                            </div>
                        </div>

                        <div class="film-detail-row">
                            <span class="film-detail-label">Статус:</span>

                            <div>
                                {{ $film->status->title }}
                            </div>
                        </div>

                    @endif


                    <div class="film-detail-row">
                        <span class="film-detail-label span-index">Тривалість:</span>

                        <div class="span-show">
                            @if($film->duration_id->totalMinutes < 60)
                                {{ $film->duration_id->getMinutes() }} хв
                            @else
                                {{ $film->duration_id->getHours() }} год
                                {{ $film->duration_id->getMinutes() }} хв
                            @endif
                        </div>
                    </div>


                    @if($film->countries->count())
                        <div class="film-detail-row">
        <span class="film-detail-label">
            <a href="{{ route('countries.index') }}">Країна:</a>
        </span>

                            <div class="span-show">
                                @foreach($film->countries as $country)
                                    <a href="{{ route('countries.show', ['slug' => $country->slug]) }}">
                                        {{ $country->title }}
                                    </a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @endif


                    @if($film->companies->count())
                        <div class="film-detail-row">
        <span class="film-detail-label">
            <a href="{{ route('companies.index') }}">Кінокомпанія:</a>
        </span>

                            <div class="span-show">
                                @foreach($film->companies as $company)
                                    <a href="{{ route('companies.show', ['slug' => $company->slug]) }}">
                                        {{ $company->title }}
                                    </a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @endif


                    @if($film->producers->count())
                        <div class="film-detail-row">
        <span class="film-detail-label">
            <a href="{{ route('producers.index') }}">Продюсер:</a>
        </span>

                            <div class="span-show">
                                @foreach($film->producers as $producer)
                                    <a href="{{ route('producers.show', ['slug' => $producer->slug]) }}">
                                        {{ $producer->name }}
                                    </a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @endif



                    @if($film->directors->count())
                        <div class="film-detail-row">
        <span class="film-detail-label">
            <a href="{{ route('directors.index') }}">Режисер:</a>
        </span>

                            <div class="span-show">
                                @foreach($film->directors as $director)
                                    <a href="{{ route('directors.show', ['slug' => $director->slug]) }}">
                                        {{ $director->name }}
                                    </a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @endif


                    <div class="film-detail-row">
    <span class="film-detail-label">
        <a href="{{ route('composers.index') }}">Композитор:</a>
    </span>

                        <div class="span-show">
                            @foreach($film->composers as $composer)
                                <a href="{{ route('composers.show', ['slug' => $composer->slug]) }}">
                                    {{ $composer->name }}
                                </a>@if(!$loop->last), @endif
                            @endforeach
                        </div>
                    </div>


                    <div class="film-detail-row">
                        <span class="film-detail-label">Користувач:</span>

                        <div class="span-show">
                            {{ $film->user->name }}
                        </div>
                    </div>


                    <div class="film-detail-row">
    <span class="film-detail-label">
        <a href="{{ route('actors.index') }}">Топ-актори:</a>
    </span>

                        <div class="span-show">
                            @foreach($film->actors as $actor)
                                <a href="{{ route('actors.show', ['slug' => $actor->slug]) }}">
                                    {{ $actor->name }}
                                </a>@if(!$loop->last), @endif
                            @endforeach
                        </div>
                    </div>


                    @if($film->other_actor)
                        <div class="film-detail-row">
                            <span class="film-detail-label">Інші актори:</span>

                            <div class="span-show">
                                {{ $film->other_actor }}
                            </div>
                        </div>
                    @endif

            </section>


            {{-- =========================
                 TRAILER
                 ========================= --}}
            <section class="film-section">

                <h2>Трейлер</h2>

                @if(app(\App\Media\FilmVideoResolver::class)->hasTrailer($film))
                    @include('films.inc.trailer', ['film' => $film])
                @else
                    <div class="film-empty-block">
                        🎬 Трейлер буде додано найближчим часом.
                    </div>
                @endif

            </section>


            {{-- =========================
                 DESCRIPTION
                 ========================= --}}
            <section class="film-section">

                <h2>Опис</h2>

                @if(!empty(trim(strip_tags($film->description))))
                    <div class="film-description">
                        {!! $film->description !!}
                    </div>
                @else
                    <div class="film-empty-block">
                        📖 Опис фільму ще готується.
                    </div>
                @endif

            </section>


            {{-- =========================
                 RELATED FILMS
                 ========================= --}}
            <section class="film-section">

                <h2>Дивитись ще {{ $film->category->title }}</h2>

                <div class="related-films">

                    @foreach($relatedFilms as $filmm)

                        <div class="related-film">

                            <img
                                src="{{ app(\App\Media\FilmImageResolver::class)->thumb($filmm) }}"
                                alt="{{ $filmm->title }}"
                                loading="lazy"
                                decoding="async"
                            >

                            <a href="{{ route('single', [
                                'category' => $filmm->category->slug,
                                'slug' => $filmm->slug
                            ]) }}">
                                {{ $filmm->title }}
                            </a>

                        </div>

                    @endforeach

                </div>

            </section>


            {{-- =========================
                 MOBILE: REST OF SIDEBAR
                 ========================= --}}
            <section class="film-mobile-block film-mobile-sidebar">

                <h2>Кращі {{ $film->category->title }} (likes)</h2>

                <ul class="sidebar-list">
                    @foreach($bestFilms as $sidefilm)
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


                <h2>Обрані фільми</h2>

                <ul class="sidebar-list">
                    @foreach($featuredFilms as $featuredFilm)
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


                <h2>Підписатися</h2>

                @include('admin.layouts.alerts')

                <form action="{{ route('subscribe') }}" method="POST">
                    @csrf

                    <div class="input-group">
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Ваш Email"
                        >

                        <button class="btn btn-dark" type="submit">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                </form>


                <h2>Скоро в кіно (API)</h2>

                @foreach($upcomingMovies as $movie)

                    <div class="upcoming-movie">

                        <a href="#">
                            {{ $movie['title'] }}
                        </a>

                        <small>
                            <i class="bi bi-calendar-event me-1"></i>
                            {{ $movie['release_date'] }}
                        </small>

                    </div>

                @endforeach

            </section>


            {{-- =========================
                 COMMENTS
                 ========================= --}}
            <section class="film-comments">

                <div id="app">
                    <film-component :film="{{ json_encode($film) }}"></film-component>

                    <comments-component
                        :film-id="{{ $film->id }}">
                    </comments-component>
                </div>

            </section>

        </main>

    </div>

@endsection


