@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => 'Якість відео ' . $quality->title,
    'description' => 'Фільми в якості ' . $quality->title . ' — дивіться онлайн.',
])

@section('content')


    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Якість відео', 'url' => route('qualities.index')],
            ['title' => $quality->title, 'url' => null],
        ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">

            <h1>Якість відео - {{$quality->title}}</h1>
            <div class="container-archive">

                @foreach($films as $film)
                <div class="child-archive">
                    <div>
                      <img src="{{ app(\App\Media\FilmImageResolver::class)->thumb($film) }}" alt="{{ $film->title }}" width="155" height="235" decoding="async">
                    </div>

                    <a href="{{route('single', ['category' => $film->category->slug, 'slug' => $film->slug])}}">
                        <p>{{$film->title}}</p>
                    </a>
                </div>
                @endforeach

                <div class="pagination-new">
                    {{$films->links()}}
                </div>
            </div>
        </div>
    </div>

@endsection

