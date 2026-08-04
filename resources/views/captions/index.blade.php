@extends('layouts/layout')
@include('layouts.inc.seo', ['title' => 'Cубтитри', 'description' => 'Список усіх субтитрів на сайті.'])
@section('content')


    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Головна', 'url' => route('home')],
        ['title' => 'Cубтитри', 'url' => null],
    ]])

    </div>

    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive">

                @foreach($captions as $caption)
                    <h3><a href="{{route('captions.show',['slug' => $caption->slug])}}">{{$caption->title}}</a></h3>
                @endforeach

                <div class="pagination-new">
                    {{$captions->links()}}
                </div>

            </div>
        </div>
    </div>

@endsection

