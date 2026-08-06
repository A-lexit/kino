@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => $source->title,
    'description' => 'Джерело: ' . $source->title . '.',
])
@section('content')


    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Джерела', 'url' => route('sources.index')],
            ['title' => $source->title, 'url' => null],
        ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">

            <h1>{{$source->title}}</h1>
            <div class="container-archive">

                @foreach($films as $film)
                <div class="child-archive">
                    <div>
                      <img src="{{ app(\App\Media\FilmImageResolver::class)->thumb($film) }}" alt="{{ $film->title }}" width="155" height="235" decoding="async">
                    </div>

                    <a href="{{route('single', ['slug' => $film->slug])}}">
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

