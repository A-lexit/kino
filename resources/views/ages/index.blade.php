@extends('layouts/layout')
@include('layouts.inc.seo', ['title' => 'Мінімальний вік', 'description' => 'Список усіх вікових обмежень на сайті.'])
@section('content')



    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Мінімальний вік', 'url' => null],
        ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">

            <div class="container-archive">

                @foreach($ages as $age)
                    <h3><a href="{{route('ages.show',['slug' => $age->slug])}}">{{$age->title}}</a></h3>
                @endforeach

                <div class="pagination-new">
                    {{$ages->links()}}
                </div>

            </div>
        </div>
    </div>

@endsection

