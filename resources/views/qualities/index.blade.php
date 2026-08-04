@extends('layouts/layout')
@include('layouts.inc.seo', ['title' => 'Якість відео', 'description' => 'Список усіх варіантів якості відео на сайті.'])
@section('content')


    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Якість відео', 'url' => null],
        ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive">

                @foreach($qualities as $quality)
                    <h3><a href="{{route('qualities.show',['slug' => $quality->slug])}}">{{$quality->title}}</a></h3>
                @endforeach

                <div class="pagination-new">
                    {{$qualities->links()}}
                </div>

            </div>
        </div>
    </div>

@endsection

