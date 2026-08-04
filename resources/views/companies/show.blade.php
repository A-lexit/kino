@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => $company->title,
    'description' => 'Фільми виробництва «' . $company->title . '».',
])

@section('content')

    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Компанії', 'url' => route('companies.index')],
            ['title' => $company->title, 'url' => null],
                    ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">

           <h1>Кінокомпанія - {{$company->title}}</h1>

            <div class="container-archive">
                @foreach($films as $film)
                <div class="child-archive">
                    <div>
                        <img src="{{ app(\App\Media\FilmImageResolver::class)->thumb($film) }}" alt="{{ $film->title }}" width="155" height="235" decoding="async">
                    </div>

                    <a href="{{route('single', ['category' => $film->category->slug, 'slug' => $film->slug])}}">
                        <h3>{{$film->title}}</h3>
                    </a>
                </div>
                @endforeach

                <div class="pagination-new">
                    {{$films->links()}}
                </div>
            </div>
        </div>


    </div>
@endsection

