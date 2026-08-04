@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => $category->title,
    'description' => 'Дивіться онлайн фільми у категорії «' . $category->title . '» — великий вибір, якісне зображення, українська озвучка.',
])

@section('content')

    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">

             @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
    ['title' => 'Головна', 'url' => route('home')],
    ['title' => $category->title, 'url' => null],
]])

            <h1>{{$category->title}}</h1>

            <div class="container-archive">
                @foreach($films as $film)
                <div class="child-archive">
                    <div>
                      <img src="{{ app(\App\Media\FilmImageResolver::class)->thumb($film) }}" alt="{{ $film->title }}" width="" height="">
                    </div>
                    <a href="{{route('single', ['category' => $film->category->slug, 'slug' => $film->slug])}}">
                        <h3>{{$film->title}}</h3>
                    </a>
                </div>
                @endforeach
                <div class="pagination-new">
                    {{ $films->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection

