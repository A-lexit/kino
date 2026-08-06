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


            <div ><h1 >{{$film->title}}</h1></div>
            <div ><h2>{{$film->origin_title}}</h2></div>

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
                                        <td class="first-col-film"><span><a href="{{route('companies.index')}}">Компанія:</a></span></td>
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
            @include('films.inc.trailer', ['film' => $film])

            <div id="description">
                <p>{!!$film->description!!}</p>
            </div>



            <div class="watchmore-h4">
                <h4>Дивитись ще {{ $film->category->title }}</h4>
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



            <div id="app">
                <film-component :film="{{ json_encode($film) }}"></film-component>
                <comments-component :film-id="{{ $film->id }}"></comments-component>
            </div>
            <div class="row"></div>
        </div>
    </div>


@endsection
