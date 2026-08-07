@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => 'Рейтинги фільмів',
    'description' => 'Список усіх рейтингів на сайті.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Рейтинги', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">

            <div class="archive-grid">

                @foreach($ratings as $rating)
                    <a href="{{ route('ratings.show', ['slug' => $rating->slug]) }}"
                       class="archive-item">
                        {{ $rating->title }}
                    </a>
                @endforeach

            </div>

            <div class="pagination-new">
                {{ $ratings->links() }}
            </div>

        </div>

    </div>

@endsection
