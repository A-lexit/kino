@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => $selection->title,
    'description' => 'Добірка: ' . $selection->title . '.',
])

@section('content')

    <div class="container-arch flex-arch">
        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Добірки', 'url' => route('selections.index')],
            ['title' => $selection->title, 'url' => null],
        ]])
    </div>

    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">

            <div class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList"><span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a class="breadcrumbs__link" href="{{route('home')}}" itemprop="item"><span itemprop="name">Главная</span></a><meta itemprop="position" content="1" /></span><span class="breadcrumbs__separator"> › </span> > {{$selection->title}} </div><!-- .breadcrumbs -->
            <h1>{{$selection->title}}</h1>
            <div class="container-archive">

                @foreach($films as $film)
                <div class="child-archive">
                    <div>
                       <img src="{{ app(\App\Media\FilmImageResolver::class)->thumb($film) }}" alt="{{ $film->title }}" width="155" height="235" decoding="async">
                    </div>
                        <a href="{{ route('single', [
    'category' => $film->category->slug,
    'slug' => $film->slug,
]) }}">
                        <p>{{$film->title}}</p>
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

