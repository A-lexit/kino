@extends('layouts/layout')

@include('layouts.inc.seo', [
    'title' => 'Актори',
    'description' => 'Список усіх акторів на сайті.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Актори', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">

            <h1>ТОП-Актори</h1>

            <p class="archive-description">
                Оберіть актора, щоб переглянути всі фільми та серіали за його участю.
            </p>

            <div class="archive-grid">

                @foreach($actors as $actor)
                    <a href="{{ route('actors.show', ['slug' => $actor->slug]) }}"
                       class="archive-item">
                        {{ $actor->name }}
                    </a>
                @endforeach

            </div>

            <div class="pagination-new">
                {{ $actors->links() }}
            </div>

        </div>

    </div>

@endsection
