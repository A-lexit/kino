@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => 'Якість відео',
    'description' => 'Список усіх варіантів якості відео на сайті.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Якість відео', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">

            <h1>Якості відео</h1>

            <p class="archive-description">
                Оберіть якість відео, щоб переглянути всі фільми та серіали у відповідному форматі.
            </p>

            <div class="archive-grid">

                @foreach($qualities as $quality)
                    <a href="{{ route('qualities.show', ['slug' => $quality->slug]) }}"
                       class="archive-item">
                        {{ $quality->title }}
                    </a>
                @endforeach

            </div>

            <div class="pagination-new">
                {{ $qualities->links() }}
            </div>

        </div>

    </div>

@endsection
