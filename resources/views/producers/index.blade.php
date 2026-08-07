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

            <h1>Продюсери</h1>

            <p class="archive-description">
                Оберіть продюсера, щоб переглянути всі фільми та серіали, над якими він працював.
            </p>

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
