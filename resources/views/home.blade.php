@extends('layouts/layout')
@section('title', $title)
@section('description', $description)
@section('content')

<div class="container">

    <div class="section-default-posts">
        <h1> <a href="{{ route('categories.show', ['slug' => 'filmi']) }}">Фільми</a></h1>
        <div class="container-default">

            @foreach($films as $film)
                <div class="child">
                  <img width="194" height="293" src="{{ app(\App\Media\FilmImageResolver::class)->image($film)}}"
                         class="home-poster-img" alt="{{ $film->title }}" loading="eager" decoding="async" />

                    <div class="title_cat_image">
                    </div>
                    <a href="{{route('single', ['category' => $film->category->slug,'slug' => $film->slug])}}">
                        <p>{{$film->title}}</p>
                    </a>
                </div>
            @endforeach

        </div>
    </div>

    <div class="section-default-posts">
        <h1> <a href="{{ route('categories.show', ['slug' => 'seriali']) }}">Серіали</a></h1>
        <div class="container-default">

            @foreach($serials as $serial)
                <div class="child">
                 <img src="{{ app(\App\Media\FilmImageResolver::class)->image($serial)}}"
                         class="home-poster-img" alt="{{ $serial->title }}" width="194" height="293" loading="lazy" decoding="async" />
                    <div class="title_cat_image"></div>
                    <a href="{{route('single', ['category' => $serial->category->slug,'slug' => $serial->slug])}}">
                        <p>{{$serial->title}}</p>
                    </a>
                </div>
            @endforeach

        </div>
    </div>


    <div class="section-default-posts">
        <h1> <a href="{{ route('categories.show', ['slug' => 'multiki']) }}">Мультики</a></h1>
        <div class="container-default">
            @foreach($mults as $mult)
                <div class="child">
                 <img src="{{ app(\App\Media\FilmImageResolver::class)->image($mult)}}"
                         class="home-poster-img" alt="{{ $mult->title }}" width="194" height="293" loading="lazy" decoding="async" />
                    <div class="title_cat_image"></div>
                    <a href="{{route('single', ['category' => $mult->category->slug,'slug' => $mult->slug])}}">
                        <p>{{$mult->title}}</p>
                    </a>
                </div>
            @endforeach

        </div>
    </div>


    <div class="section-default-posts">
    <h1> <a href="{{ route('categories.show', ['slug' => 'multseriali']) }}">Мультеріали</a></h1>
    <div class="container-default">
        @foreach($multserials as $multserial)
            <div class="child">
              <img src="{{ app(\App\Media\FilmImageResolver::class)->image($multserial)}}"
                class="home-poster-img" alt="{{ $multserial->title }}" width="194" height="293" loading="lazy" decoding="async" />
                <a href="{{route('single', ['category' => $multserial->category->slug,'slug' => $multserial->slug])}}">
                    <p>{{$multserial->title}}</p>
                </a>
            </div>
        @endforeach
    </div>
    </div>

</div>

@endsection
