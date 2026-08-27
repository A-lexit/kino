@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => $film->title,
    'description' => $film->seoDescription()
])

@section('content')

    {{-- Breadcrumbs --}}
    <div class="container-arch">

        <x-breadcrumbs :items="[
            ['title' => 'Головна', 'url' => route('home')],
            [
                'title' => $film->category->title,
                'url' => route('categories.show', ['slug' => $film->category->slug])
            ],
            ['title' => $film->title, 'url' => null],
        ]" />
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

                <x-film-image :film="$film" variant="image" fetchpriority="high"/>

                @include('films.inc.partials-sidebar.gallery', [
    'images' => app(\App\Media\FilmImageResolver::class)->gallery($film),
    'fancyboxGroup' => 'gallery-mobile',
])

                {{--<p class="film-release-date">
                    Дата виходу: {{ $film->display_date }}
                </p>--}}

            </section>


            {{-- =========================
                 MOBILE: QUICK INFO
                 ========================= --}}

            <section class="film-mobile-block film-mobile-info">
                <h2>Інформація</h2>
                <div class="film-info-list">
                    <x-film-detail-row :item="$film->age" label="Вік" index-route="ages.index" show-route="ages.show" />
                    <x-film-detail-row :item="$film->quality" label="Якість відео" index-route="qualities.index" show-route="qualities.show" class="span-show" />
                    <x-film-detail-row :item="$film->rating" label="Рейтинг" index-route="ratings.index" show-route="ratings.show" class="span-show" />

                    <x-film-detail-list :items="$film->selections" label="Добірки" index-route="selections.index" show-route="selections.show" />
                    <x-film-detail-list :items="$film->languages" label="Озвучка" index-route="languages.index" show-route="languages.show" />
                    <x-film-detail-list :items="$film->captions" label="Субтитри" index-route="captions.index" show-route="captions.show" />

                    <x-film-plain-row
                        :value="$film->imdb_rating ? '⭐ ' . $film->imdb_rating . ' / 10' : null"
                        label="Рейтинг IMDB:"
                        class="film-info-row"
                    />

                    <x-film-plain-row
                        :value="$film->note"
                        label="Примітка:"
                        class="film-info-note"
                    />
                </div>
            </section>



            {{-- =========================
                 MAIN FILM INFO
                 ========================= --}}
            <section class="film-details">

                <x-film-detail-list :items="$film->genres" label="Жанр" index-route="genres.index" show-route="genres.show" />

                <x-film-detail-row :item="$film->year" label="Рік випуску" index-route="years.index" show-route="years.show" class="span-show" />

            @if($film->category->isSeries())
                    <x-film-detail-row :item="$film->season" label="Сезони" />
                    <x-film-detail-row :item="$film->status" label="Статус" />
                @endif

                <x-film-plain-row
                    :value="$film->duration ? $film->formatted_duration : null"
                    label="Тривалість:"
                    value-class="span-show"
                />

                <x-film-detail-list :items="$film->countries" label="Країна" index-route="countries.index" show-route="countries.show" />
                <x-film-detail-list :items="$film->companies" label="Кінокомпанія" index-route="companies.index" show-route="companies.show" />
                <x-film-detail-list :items="$film->producers" label="Продюсер" index-route="producers.index" show-route="producers.show" name-field="name" />
                <x-film-detail-list :items="$film->directors" label="Режисер" index-route="directors.index" show-route="directors.show" name-field="name" />
                <x-film-detail-list :items="$film->composers" label="Композитор" index-route="composers.index" show-route="composers.show" name-field="name" />
                <x-film-detail-list :items="$film->actors" label="Топ-актори" index-route="actors.index" show-route="actors.show" name-field="name" />

                <x-film-plain-row
                    :value="$film->other_actor"
                    label="Інші актори:"
                    value-class="span-show"
                />

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
            <x-film-section :films="$film->relatedFilms">
                <x-slot:title>Інші частини франшизи</x-slot:title>
            </x-film-section>

            <x-film-section :films="$moreFilms">
                <x-slot:title>
                    Дивитись ще
                    <span class="related-category-title">{{ $film->category->title }}</span>
                </x-slot:title>
            </x-film-section>


            {{-- =========================
                 MOBILE: REST OF SIDEBAR
                 ========================= --}}
            <section class="film-mobile-block film-mobile-sidebar">

                <x-film-sidebar-list :films="$bestFilms" :title="'Кращі ' . $film->category->title . ' (likes)'"
                                     wrapper-class="sidetitle bestfilmss mt-5" />

                <x-film-sidebar-list :films="$featuredFilms" title="Обрані Фільми"
                                     wrapper-class="sidetitle text-start mt-5" />

                <x-subscribe-form wrapper-class="sidetitle text-start mt-5" />

                <x-upcoming-movies :movies="$upcomingMovies" wrapper-class="sidetitle text-start mt-5" />

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


