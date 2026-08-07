@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => 'Роки випуску',
    'description' => 'Фільми за роками випуску.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Роки випуску', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">

            <h1 class="archive-title">Роки випуску</h1>

            <p class="archive-description">
                Оберіть рік, щоб переглянути всі фільми та серіали, випущені у цей період.
            </p>

            <div class="archive-grid">

                @foreach($years as $year)

                    <a class="archive-item"
                       href="{{ route('years.show', ['slug' => $year->slug]) }}">
                        {{ $year->title }}
                    </a>

                @endforeach

            </div>

            <div class="pagination-new">
                {{ $years->links() }}
            </div>

        </div>

    </div>

@endsection
