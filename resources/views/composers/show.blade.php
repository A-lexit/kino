@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => $composer->name,
    'description' => 'Фільми з музикою композитора ' . $composer->name . '.',
])

@section('content')

    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Композитори', 'url' => route('composers.index')],
            ['title' => $composer->name, 'url' => null],
                    ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">

               <h1>Композитор - {{$composer->name}}</h1>
            <div class="container-archive">

                @foreach($films as $film)
                <div class="child-archive">
                    <div>
                         <img src="{{ app(\App\Media\FilmImageResolver::class)->thumb($film) }}" alt="{{ $film->title }}" width="155" height="235" decoding="async">
                    </div>

                    <a href="{{route('single', ['category' => $film->category->slug, 'slug' => $film->slug])}}">
                        <h3>{{$film->title}}</h3>
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

