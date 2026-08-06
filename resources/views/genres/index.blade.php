@extends('layouts/layout')
@include('layouts.inc.seo', ['title' => 'Жанри', 'description' => 'Повний список жанрів фільмів на сайті.'])
@section('content')


    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Жанри', 'url' => null],
        ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive">

                @foreach($genres as $genre)
                    <p><a href="{{route('genres.show',['slug' => $genre->slug])}}">{{$genre->title}}</a></p>
                @endforeach



            </div>
            <div class="pagination-new">
                {{$genres->links()}}
            </div>

        </div>
    </div>

@endsection

