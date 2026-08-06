@extends('layouts/layout')
@include('layouts.inc.seo', ['title' => 'Добірки', 'description' => 'Список усіх добірок на сайті.'])
@section('content')


    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Добірки', 'url' => null],
        ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive">

                @foreach($selections as $selection)
                    <p><a href="{{route('selections.show',['slug' => $selection->slug])}}">{{$selection->title}}</a></p>
                @endforeach

                <div class="pagination-new">
                    {{$selections->links()}}
                </div>

            </div>
        </div>
    </div>

@endsection

