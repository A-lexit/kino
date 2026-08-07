@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => 'Добірки',
    'description' => 'Список усіх добірок на сайті.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Добірки', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">

            <div class="archive-grid">

                @foreach($selections as $selection)
                    <a href="{{ route('selections.show', ['slug' => $selection->slug]) }}"
                       class="archive-item">
                        {{ $selection->title }}
                    </a>
                @endforeach

            </div>

            <div class="pagination-new">
                {{ $selections->links() }}
            </div>

        </div>

    </div>

@endsection
