@extends('layouts/layout')
@include('layouts.inc.seo', ['title' => 'Джерела', 'description' => 'Список усіх джерел на сайті.'])
@section('content')


    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Джерела', 'url' => null],
        ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive">

                @foreach($sources as $source)
                    <h3><a href="{{route('sources.single',['slug' => $source->slug])}}">{{$source->title}}</a></h3>
                @endforeach

                <div class="pagination-new">
                    {{$sources->links()}}
                </div>

            </div>
        </div>
    </div>

@endsection

