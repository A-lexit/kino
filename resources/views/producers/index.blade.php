@extends('layouts/layout')
@include('layouts.inc.seo', ['name' => 'Продюсери', 'description' => 'Список усіх продюсерів на сайті.'])
@section('content')


    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Головна', 'url' => route('home')],
        ['title' => 'Продюсери', 'url' => null],
    ]])

    </div>

    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive">

                @foreach($producers as $producer)
                    <h3><a href="{{route('producers.show',['slug' => $producer->slug])}}">{{$producer->name}}</a></h3>
                @endforeach

                <div class="pagination-new">
                    {{$producers->links()}}
                </div>

            </div>
        </div>
    </div>

@endsection

