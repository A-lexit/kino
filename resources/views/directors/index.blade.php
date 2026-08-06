@extends('layouts/layout')
@include('layouts.inc.seo', ['name' => 'Режисери', 'description' => 'Список усіх режисерів на сайті.'])
@section('content')


    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Головна', 'url' => route('home')],
        ['title' => 'Режисери', 'url' => null],
    ]])
    </div>

    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive">
                @foreach($directors as $director)
                    <p><a href="{{route('directors.show',['slug' => $director->slug])}}">{{$director->name}}</a></p>
                @endforeach


            </div>
            <div class="pagination-new">
                {{$directors->links()}}
            </div>

        </div>
    </div>

@endsection

