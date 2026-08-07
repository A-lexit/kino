@extends('layouts/layout')
@include('layouts.inc.seo', ['title' => $film->title, 'description' => $film->seoDescription()])
@section('content')

    <div class="container-arch flex-single">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Головна', 'url' => route('home')],
        ['title' => $film->category->title, 'url' => route('categories.show', ['slug' => $film->category->slug])],
        ['title' => $film->title, 'url' => null],
        ]])
    </div>

    <div class="container-arch flex-single">

        @include('films.inc.sidebar')
        <div class="archive-area container-default section-single">


            <div class="film-title"><h1 >{{$film->title}}</h1></div>
            <div class="mb-4"><h5>{{$film->origin_title}}</h5></div>




            {{--Мобільний блок з сайдбару - Псостер, галерея, дата - START--}}
            <div class="mobile-section-poster">
            <img width="685" height="390" src=
                "{{ app(\App\Media\FilmImageResolver::class)->image($film)}}"
                 class="attachment-large size-large wp-post-image" alt="" decoding="async" fetchpriority="high"  sizes="(max-width: 685px) 100vw, 685px" />


            @include('films.inc.partials-sidebar.gallery',

            [
                'images' => app(\App\Media\FilmImageResolver::class)->gallery($film)
            ])

            <p class="mt-4 mb-2">Дата виходу: {{ $film->display_date }}</p>

            </div>

            {{--Мобільний блок з сайдбару - Псостер, галерея, дата - FINISH--}}










            <table>
                <tr><div class="blog-title-area">
                        @if($film->genres->count())
                            <div class="tag-cloud-single">
                                <td class="first-col-film"><span><a href="{{route('genres.index')}}">Жанр:</a></span></td>
                                <td>@foreach($film->genres as $genre)
                                        <small><a href="{{ route('genres.show', ['slug'=>$genre->slug]) }}" title="">{{$genre->title}}@if (!$loop->last),@endif</a> </small>
                                    @endforeach</td>
                            </div>
                        @endif
                    </div></tr>

                <tr><div class="blog-title-area">

                        <div class="tag-cloud-single">
                            <td class="first-col-film"><span><a href="{{route('years.index')}}">Рік випуску:</a></span></td>
                            <td><small><a href="{{ route('years.show', ['slug'=>$film->year->slug]) }}" title="">{{$film->year->title}}</a> </small></td>
                        </div>

                    </div></tr>

                @if($film->category->isSeries())
                    <tr><div class="blog-title-area">
                            <div class="tag-cloud-single">
                                <td class="first-col-film"><span>Сезони:</span></td>
                                <td><small>{{$film->season->title}} </small></td>
                            </div>
                        </div></tr>
                @endif

                <tr><div class="blog-title-area">


                        <div class="tag-cloud-single">
                            <td class="first-col-film"><span>Тривалість:</span></td>
                            <td>
                                <small>
                                    <p>
                                        @if($film->duration_id->totalMinutes < 60)
                                            {{ $film->duration_id->getMinutes() }} хв
                                        @else
                                            {{ $film->duration_id->getHours() }} год {{ $film->duration_id->getMinutes() }} хв
                                        @endif
                                    </p>
                                </small>
                            </td>
                        </div>

                        @if($film->category->isSeries())
                            <tr><div class="blog-title-area">
                                    <div class="tag-cloud-single">
                                        <td class="first-col-film"><span>Статус:</span></td>
                                        <td><small>{{$film->status->title}} </small></td>
                                    </div>
                                </div></tr>
                        @endif


                        <tr><div class="blog-title-area">
                                @if($film->countries->count())
                                    <div class="tag-cloud-single">
                                        <td class="first-col-film"><span><a href="{{route('countries.index')}}">Країна:</a></span></td>
                                        <td>@foreach($film->countries as $country)
                                                <small><a href="{{ route('countries.show', ['slug'=>$country->slug]) }}" title="">{{$country->title}}@if (!$loop->last),@endif</a> </small>
                                            @endforeach</td>
                                    </div>
                                @endif
                            </div></tr>

                        <tr><div class="blog-title-area">
                                @if($film->companies->count())
                                    <div class="tag-cloud-single">
                                        <td class="first-col-film"><span><a href="{{route('companies.index')}}">Кінокомпанія:</a></span></td>
                                        <td>@foreach($film->companies as $company)
                                                <small><a href="{{ route('companies.show', ['slug'=>$company->slug]) }}" title="">{{$company->title}}@if (!$loop->last),@endif</a> </small>
                                            @endforeach</td>
                                    </div>
                                @endif
                            </div></tr>


                        <tr><div class="blog-title-area">
                                @if($film->producers->count())
                                    <div class="tag-cloud-single">
                                        <td class="first-col-film"><span><a href="{{route('producers.index')}}">Продюсер:</a></span></td>
                                        <td>@foreach($film->producers as $producer)
                                                <small><a href="{{ route('producers.show', ['slug'=>$producer->slug]) }}" title="">{{$producer->name}}@if (!$loop->last),@endif</a> </small>
                                            @endforeach</td>
                                    </div>
                                @endif
                            </div></tr>


                        <tr><div class="blog-title-area">
                                @if($film->directors->count())
                                    <div class="tag-cloud-single">
                                        <td class="first-col-film"><span><a href="{{route('directors.index')}}">Режисер:</a></span></td>
                                        <td>@foreach($film->directors as $director)
                                                <small><a href="{{ route('directors.show', ['slug'=>$director->slug]) }}" title="">{{ $director->name }}@if (!$loop->last),@endif</a> </small>

                                            @endforeach</td>
                                    </div>
                                @endif
                            </div></tr>

                        <tr><div class="blog-title-area">

                                <div class="tag-cloud-single">
                                    <td class="first-col-film"><span><a href="{{route('composers.index')}}">Композитор:</a></span></td>
                                    <td>@foreach($film->composers as $composer)
                                            <small><a href="{{ route('composers.show', ['slug'=>$composer->slug]) }}" title="">{{$composer->name}}@if (!$loop->last),@endif</a> </small>
                                            {{--<small><a href="{{ route('actors.show', ['slug'=>$actor->slug]) }}" title="">{{$actor->pluck('name')->join(', ')}}</a> </small>--}}
                                        @endforeach</td>
                                </div>

                            </div></tr>


                        <tr><div class="blog-title-area">

                                <div class="tag-cloud-single">
                                    <td class="first-col-film"><span>Користувач:</span></td>
                                    <td>{{$film->user->name}}</td>
                                </div>

                            </div></tr>



                        <tr><div class="blog-title-area">

                                <div class="tag-cloud-single">
                                    <td class="first-col-film"><span><a href="{{route('actors.index')}}">Топ-актори:</a></span></td>
                                    <td>
                                        @foreach($film->actors as $actor)
                                            <small><a href="{{ route('actors.show', ['slug'=>$actor->slug]) }}" title="">{{$actor->name}}@if (!$loop->last),@endif</a> </small>
                                        @endforeach
                                    </td>

                                </div>
                            </div></tr>


                        <tr><div class="blog-title-area">

                                <div class="tag-cloud-single">

                                    <td class="first-col-film"><span>Інші актори:</span></td>
                                    <td><small><p>{{$film->other_actor}}</p> </small></td>

                                </div>

                            </div></tr>

            </table>

            <h3 class="text-start mt-5">Трейлер</h3>

            @if(!empty($film->trailer))
                @include('films.inc.trailer', ['film' => $film])
            @else
                <div class="film-empty-block">
                    🎬 Трейлер буде додано найближчим часом.
                </div>
            @endif


            <h3 class="text-start mt-5">Опис</h3>

            @if(!empty(trim(strip_tags($film->description))))
                <div class="film-description">
                    {!! $film->description !!}
                </div>
            @else
                <div class="film-empty-block">
                    📖 Опис фільму ще готується.
                </div>
            @endif





            {{--Мобільний блок з сайдбару - Додаткова інформація - START--}}
            <div class="mob-side-section mob-side-other">

            <h3>Додаткова інформація</h3>
            <tr><div class="blog-title-area">
                    <div class="tag-cloud-single">
                        <td class="first-col-film"><span><a href="{{route('ages.index')}}">Вік:</a></span></td>
                        <td><small><a href="{{ route('ages.show', ['slug'=>$film->age->slug]) }}" title="">{{$film->age->title}} </small></td>
                    </div>
                </div></tr>


            <tr><div class="blog-title-area">

                    <div class="tag-cloud-single">
                        <td class="first-col-film"><span><a href="{{route('qualities.index')}}">Якість відео:</a></span></td>
                        <td><small><a href="{{ route('qualities.show', ['slug'=>$film->quality->slug]) }}" title="">{{$film->quality->title}}</a> </small></td>
                    </div>
                </div>
            </tr>

            <tr>
                <div class="blog-title-area">
                    <div class="tag-cloud-single">
                        <td class="first-col-film"><span><a href="{{route('ratings.index')}}">Рейтинг:</a></span></td>
                        <small> <a href="{{ route('ratings.show', ['slug'=>$film->rating->slug]) }}" title="">{{$film->rating->title}}</a> </small>
                    </div>
                </div>
            </tr>


            <tr><div class="blog-title-area">
                    @if($film->selections->count())
                        <div class="tag-cloud-single">
                            <td class="first-col-film"><span><a href="{{route('selections.index')}}">Добірки:</a></span></td>
                            <td>@foreach($film->selections as $selection)
                                    <small><a href="{{ route('selections.show', ['slug'=>$selection->slug]) }}" title="">{{$selection->title}}</a> </small>
                                @endforeach</td>
                        </div>
                    @endif
                </div></tr>

            <tr><div class="blog-title-area">
                    @if($film->languages->count())
                        <div class="tag-cloud-single">
                            <td class="first-col-film"><span><a href="{{route('languages.index')}}">Озвучка:</a></span></td>
                            <td>@foreach($film->languages as $language)
                                    <small><a href="{{ route('languages.show', ['slug'=>$language->slug]) }}" title="">{{$language->title}}@if (!$loop->last),@endif</a> </small>
                                @endforeach</td>
                        </div>
                    @endif
                </div></tr>

            <tr><div class="blog-title-area">
                    @if($film->captions->count())
                        <div class="tag-cloud-single">
                            <td class="first-col-film"><span><a href="{{route('captions.index')}}">Субтитри:</a></span></td>
                            <td>@foreach($film->captions as $caption)
                                    <small><a href="{{ route('captions.show', ['slug'=>$caption->slug]) }}" title="">{{$caption->title}}@if (!$loop->last),@endif</a> </small>
                                @endforeach</td>
                        </div>
                    @endif
                </div></tr>

            @if($film->imdb_rating)
                <tr><div class="blog-title-area">
                        <div class="tag-cloud-single">
                            <td class="first-col-film"><span>IMDB:</span></td>
                            <td><small>⭐ {{ $film->imdb_rating }} / 10</small></td>
                        </div>
                    </div></tr>
            @endif

            @isset($film->note)
                <tr><div class="blog-title-area">

                        <div class="tag-cloud-single">

                            <td class="first-col-film"><span>Примітка:</span></td>
                            <td><small><h6>{{$film->note}}</h6> </small></td>

                        </div>
                    </div></tr>
            @endisset
            </div>
            {{--Мобільний блок - finish--}}














            <div class="watchmore mt-5">
                <h3>Дивитись ще {{ $film->category->title }}</h3>
            </div>


            <div class="nnn">
                @foreach($relatedFilms as $filmm)
                    <div class="child-infilm">
                        <img src="{{ app(\App\Media\FilmImageResolver::class)->thumb($filmm) }}" alt="{{ $filmm->title }}" loading="lazy" decoding="async">
                        <div class="title_cat_image">
                        </div>

                        <a href="{{route('single', ['category' => $filmm->category->slug,'slug' => $filmm->slug])}}">
                            <p>{{$filmm->title}}</p>
                        </a>
                    </div>
                @endforeach
            </div>







            {{--Мобільний блок з сайдбару - Кращі, обрані фільми, підписка, скоро у кіно  - START--}}
            <div class="mob-side-section mob-side-bestfilms">

            <div class="sidetitle bestfilmss mt-5">
            <h3>Кращі {{ $film->category->title }} (likes)</h3>

            <ul>
                @foreach($bestFilms as $sidefilm)
                    <hr>
                    <li><a href="{{route('single', ['category' => $film->category->slug,
'slug' => $sidefilm->slug])}}">{{$sidefilm->title}}</a></li>
                @endforeach
            </ul>

            <div class="sidetitle text-start mt-5">

                <h3>Обрані Фільми</h3>
                <ul>
                    @foreach($featuredFilms as $featuredFilm)
                        <hr>
                        <li><a href="{{route('single', ['category' => $film->category->slug,
'slug' => $featuredFilm->slug])}}">{{$featuredFilm->title}}</a></li>


                    @endforeach
                </ul>
            </div>
            <hr>
        </div>


        <div class="sidetitle text-start mt-5">
            <h3 class="sidebar-title">
                Підписатися
            </h3>
        </div>
        @include('admin.layouts.alerts')
        <form action="{{ route('subscribe') }}" method="POST" class="mb-4">
            @csrf
            <div class="input-group">
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Ваш Email">
                <button class="btn btn-dark" type="submit">
                    <i class="bi bi-send"></i>
                </button>
            </div>
        </form>
        <hr>
    </div>

    <div class="sidetitle text-start mt-5">
        <h3 class="sidetitle sidebar-title mt-4">
            Скоро в кіно (API)
        </h3>
    </div>
    @foreach($upcomingMovies as $movie)

        <div class="py-2">
            <a href="#"
               class="d-block fw-semibold">

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

    </div>

        {{--Мобільний блок з сайдбару - Кращі, обрані фільми, підписка, скоро у кіно  - FINISH--}}



            <div id="app">
                <film-component :film="{{ json_encode($film) }}"></film-component>
                <comments-component :film-id="{{ $film->id }}"></comments-component>
            </div>
            <div class="row"></div>
        </div>
    </div>


@endsection
