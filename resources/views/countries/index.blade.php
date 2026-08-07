@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => 'Країни',
    'description' => 'Список усіх країн на сайті.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Країни', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">

            <h1>Країни</h1>

            <p class="archive-description">
                Оберіть країну, щоб переглянути всі фільми та серіали, створені у ній.
            </p>

            <div class="archive-grid">

                @foreach($countries as $country)
                    <a href="{{ route('countries.show', ['slug' => $country->slug]) }}"
                       class="archive-item">
                        {{ $country->title }}
                    </a>
                @endforeach

            </div>

            <div class="pagination-new">
                {{ $countries->links() }}
            </div>

        </div>

    </div>

@endsection
