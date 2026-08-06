@extends('layouts/layout')
@include('layouts.inc.seo', ['title' => 'Роки випуску', 'description' => 'Фільми за роками випуску.'])
@section('content')


    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Роки випуску', 'url' => null],
        ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive">

                @foreach($years as $year)
                    <p><a href="{{route('years.show',['slug' => $year->slug])}}">{{$year->title}}</a></p>
                @endforeach



            </div>
            <div class="pagination-new">
                {{$years->links()}}
            </div>
        </div>
    </div>

@endsection

