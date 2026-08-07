@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => 'Компанії',
    'description' => 'Список усіх компаній-виробників кінопродуктів на сайті.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Компанії', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">

            <h1>Кінокомпанії</h1>

            <p class="archive-description">
                Оберіть кінокомпанію, щоб переглянути всі фільми та серіали, випущені нею.
            </p>

            <div class="archive-grid">

                @foreach($companies as $company)
                    <a href="{{ route('companies.show', ['slug' => $company->slug]) }}"
                       class="archive-item">
                        {{ $company->title }}
                    </a>
                @endforeach

            </div>

            <div class="pagination-new">
                {{ $companies->links() }}
            </div>

        </div>

    </div>

@endsection
