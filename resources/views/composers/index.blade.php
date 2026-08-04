@extends('layouts/layout')
@section('content')


    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Головна', 'url' => route('home')],
        ['title' => 'Композитори', 'url' => null],
    ]])

    </div>

    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive">
                @foreach($composers as $composer)
                    <h3><a href="{{route('composers.show',['slug' => $composer->slug])}}">{{$composer->name}}</a></h3>
                @endforeach

                <div class="pagination-new">
                    {{$composers->links()}}
                </div>
            </div>
        </div>
    </div>

@endsection

