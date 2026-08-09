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

                {{-- Назва фільму --}}
                <div class="film-title">
                    <h1 class="film-title-text">{{ $film->title }}</h1>
                </div>

                {{-- Оригінальна назва --}}
                @if($film->origin_title)
                    <div class="film-origin-title">
                        <span class="film-origin-title-text">{{ $film->origin_title }}</span>
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

                    {{-- Вік --}}
                    @if($film->age)
                        <div class="film-info-row">
        <span class="film-detail-label">
            <a href="{{ route('ages.index') }}">
                Вік:
            </a>
        </span>

                            <div>
                                <a href="{{ route('ages.show', ['slug' => $film->age->slug]) }}">
                                    {{ $film->age->title }}
                                </a>
                            </div>
                        </div>
                    @endif


                    {{-- Якість відео --}}
                    @if($film->quality)
                        <div class="film-info-row">
        <span class="film-detail-label">
            <a href="{{ route('qualities.index') }}">
                Якість відео:
            </a>
        </span>

                            <div class="span-show">
                                <a href="{{ route('qualities.show', ['slug' => $film->quality->slug]) }}">
                                    {{ $film->quality->title }}
                                </a>
                            </div>
                        </div>
                    @endif


                    {{-- Рейтинг --}}
                    @if($film->rating)
                        <div class="film-info-row">
        <span class="film-detail-label">
            <a href="{{ route('ratings.index') }}">
                Рейтинг:
            </a>
        </span>

                            <div class="span-show">
                                <a href="{{ route('ratings.show', ['slug' => $film->rating->slug]) }}">
                                    {{ $film->rating->title }}
                                </a>
                            </div>
                        </div>
                    @endif


                    {{-- Добірки --}}
                    @if($film->selections->count())
                        <div class="film-info-row">
                <span class="film-detail-label">
                    <a href="{{ route('selections.index') }}">
                        Добірки:
                    </a>
                </span>

                            <div class="span-show">
                                @foreach($film->selections as $selection)
                                    <a href="{{ route('selections.show', ['slug' => $selection->slug]) }}">
                                        {{ $selection->title }}
                                    </a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @endif


                    {{-- Озвучка --}}
                    @if($film->languages->count())
                        <div class="film-info-row">
                <span class="film-detail-label">
                    <a href="{{ route('languages.index') }}">
                        Озвучка:
                    </a>
                </span>

                            <div class="span-show">
                                @foreach($film->languages as $language)
                                    <a href="{{ route('languages.show', ['slug' => $language->slug]) }}">
                                        {{ $language->title }}
                                    </a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @endif


                    {{-- Субтитри --}}
                    @if($film->captions->count())
                        <div class="film-info-row">
        <span class="film-detail-label">
            <a href="{{ route('captions.index') }}">
                Субтитри:
            </a>
        </span>

                            <div class="span-show">
                                @foreach($film->captions as $caption)
                                    <a href="{{ route('captions.show', ['slug' => $caption->slug]) }}">
                                        {{ $caption->title }}
                                    </a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @endif





                    {{-- IMDB --}}
                    @if($film->imdb_rating)
                        <div class="film-info-row">
                <span class="film-detail-label">
                    IMDB:
                </span>

                            <div>
                                ⭐ {{ $film->imdb_rating }} / 10
                            </div>
                        </div>
                    @endif


                    {{-- Примітка --}}
                    @isset($film->note)
                        <div class="film-info-note">
                            <span>Примітка:</span>
                            <div>
                                {{ $film->note }}
                            </div>
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






                    @if($film->year)
                        <div class="film-detail-row">
        <span class="film-detail-label">
            <a href="{{ route('years.index') }}">
                Рік випуску:
            </a>
        </span>

                            <div class="span-show">
                                <a href="{{ route('years.show', ['slug' => $film->year->slug]) }}">
                                    {{ $film->year->title }}
                                </a>
                            </div>
                        </div>
                    @endif







                    @if($film->category->isSeries())

                        @if($film->category->isSeries())

                            @if($film->season)
                                <div class="film-detail-row">
                                    <span class="film-detail-label">Сезони:</span>

                                    <div>
                                        {{ $film->season->title }}
                                    </div>
                                </div>
                            @endif

                            @if($film->status)
                                <div class="film-detail-row">
                                    <span class="film-detail-label">Статус:</span>

                                    <div>
                                        {{ $film->status->title }}
                                    </div>
                                </div>
                            @endif

                        @endif

                    @endif




                    @if($film->duration_id)
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
                    @endif


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


                    @if($film->composers->count())
                        <div class="film-detail-row">
        <span class="film-detail-label">
            <a href="{{ route('composers.index') }}">
                Композитор:
            </a>
        </span>

                            <div class="span-show">
                                @foreach($film->composers as $composer)
                                    <a href="{{ route('composers.show', ['slug' => $composer->slug]) }}">
                                        {{ $composer->name }}
                                    </a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @endif





                    @if($film->actors->count())
                        <div class="film-detail-row">
        <span class="film-detail-label">
            <a href="{{ route('actors.index') }}">
                Топ-актори:
            </a>
        </span>

                            <div class="span-show">
                                @foreach($film->actors as $actor)
                                    <a href="{{ route('actors.show', ['slug' => $actor->slug]) }}">
                                        {{ $actor->name }}
                                    </a>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        </div>
                    @endif


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

                <h2 class="related-films-title">
                    Дивитись ще
                    <span class="related-category-title">
        {{ $film->category->title }}
    </span>
                </h2>

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
                <span class="related-film-title">
                    {{ $filmm->title }}
                </span>
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


