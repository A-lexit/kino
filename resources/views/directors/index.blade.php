@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => 'Режисери',
    'description' => 'Список усіх режисерів на сайті.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Режисери', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">
            <h1>Режисери</h1>

            <p class="archive-description">
                Оберіть режисера, щоб переглянути всі фільми та серіали, які він створив.
            </p>

            <div class="archive-grid">

                @foreach($directors as $director)
                    <a href="{{ route('directors.show', ['slug' => $director->slug]) }}"
                       class="archive-item">
                        {{ $director->name }}
                    </a>
                @endforeach

            </div>

            <div class="pagination-new">
                {{ $directors->links() }}
            </div>

        </div>

    </div>

@endsection
