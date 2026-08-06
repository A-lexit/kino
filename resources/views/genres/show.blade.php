@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => $genre->title,
    'description' => 'Фільми у жанрі «' . $genre->title . '» — дивіться онлайн безкоштовно.',
])

@section('content')

    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Жанри', 'url' => route('genres.index')],
            ['title' => $genre->title, 'url' => null],
        ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">

            <h1>Жанри: {{$genre->title}}</h1>
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

