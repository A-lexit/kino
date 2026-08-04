@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => 'Фільми ' . $year->title . ' року',
    'description' => 'Список фільмів ' . $year->title . ' року — дивіться онлайн у високій якості.',
])

@section('content')


    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Роки випуску', 'url' => route('years.index')],
            ['title' => $year->title, 'url' => null],
        ]])
    </div>


    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList"><span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a class="breadcrumbs__link" href="{{route('home')}}" itemprop="item"><span itemprop="name">Головна</span></a><meta itemprop="position" content="1" /></span><span class="breadcrumbs__separator"> › </span> <a class="breadcrumbs__link" href="{{route('years.index')}}" itemprop="item"><span itemprop="name">Роки випуску</span></a> {{--> {{$year->title}}--}} </div><!-- .breadcrumbs -->
            <h1>Рік випуску - {{$year->title}}</h1>
            <div class="container-archive">

                @foreach($films as $film)
                    <div class="child-archive">
                        <div>
                          <img src="{{ app(\App\Media\FilmImageResolver::class)->thumb($film) }}" alt="{{ $film->title }}" width="155" height="235" decoding="async">
                        </div>

                        <a href="{{route('single', ['category' => $film->category->slug,'slug' => $film->slug])}}">
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

