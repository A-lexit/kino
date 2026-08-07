@extends('layouts/layout')
@include('layouts.inc.seo', [
    'name' => 'Продюсери',
    'description' => 'Список усіх продюсерів на сайті.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Продюсери', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">

            <div class="archive-grid">

                @foreach($producers as $producer)
                    <a href="{{ route('producers.show', ['slug' => $producer->slug]) }}"
                       class="archive-item">
                        {{ $producer->name }}
                    </a>
                @endforeach

            </div>

            <div class="pagination-new">
                {{ $producers->links() }}
            </div>

        </div>

    </div>

@endsection
