@extends('layouts/layout')
@include('layouts.inc.seo', ['title' => 'Компанії', 'description' => 'Список усіх компаній-виробників кінопродуктів на сайті.'])
@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
        ['title' => 'Головна', 'url' => route('home')],
        ['title' => 'Компанії', 'url' => null],
    ]])

    </div>

    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive">

                @foreach($companies as $company)
                    <p><a href="{{route('companies.show',['slug' => $company->slug])}}">{{$company->title}}</a></p>
                @endforeach

                <div class="pagination-new">
                    {{$companies->links()}}
                </div>
            </div>
        </div>
    </div>

@endsection

