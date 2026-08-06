@extends('layouts/layout')
@section('content')


    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Країни', 'url' => null],
        ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive">
                @foreach($countries as $country)
                    <p><a href="{{route('countries.show',['slug' => $country->slug])}}">{{$country->title}}</a></p>
                @endforeach

                <div class="pagination-new">
                    {{$countries->links()}}
                </div>

            </div>
        </div>
    </div>

@endsection

