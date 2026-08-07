@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => 'Мінімальний вік',
    'description' => 'Список усіх вікових обмежень на сайті.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Вікові обмеження', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">

            <h1>Вікові обмеження</h1>

            <p class="archive-description">
                Оберіть вікове обмеження, щоб переглянути всі фільми та серіали цієї категорії.
            </p>

            <div class="archive-grid">

                @foreach($ages as $age)
                    <a href="{{ route('ages.show', ['slug' => $age->slug]) }}"
                       class="archive-item">
                        {{ $age->title }}
                    </a>
                @endforeach

            </div>

            <div class="pagination-new">
                {{ $ages->links() }}
            </div>

        </div>

    </div>

@endsection
