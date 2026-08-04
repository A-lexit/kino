@extends('layouts/layout')
@include('layouts.inc.seo', ['title' => 'Мови', 'description' => 'Повний список мов для озвучки та субтитрів для фільмів на сайті.'])
@section('content')


    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Мови', 'url' => null],
        ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive">

                @foreach($languages as $language)
                    <h3><a href="{{route('languages.show',['slug' => $language->slug])}}">{{$language->title}}</a></h3>
                @endforeach

                <div class="pagination-new">
                    {{$languages->links()}}
                </div>

            </div>
        </div>
    </div>

@endsection

