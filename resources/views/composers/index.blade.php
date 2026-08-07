@extends('layouts/layout')

@include('layouts.inc.seo', [
    'title' => 'Композитори',
    'description' => 'Список усіх композиторів на сайті.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Композитори', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">

            <div class="archive-grid">

                @foreach($composers as $composer)
                    <a href="{{ route('composers.show', ['slug' => $composer->slug]) }}"
                       class="archive-item">
                        {{ $composer->name }}
                    </a>
                @endforeach

            </div>

            <div class="pagination-new">
                {{ $composers->links() }}
            </div>

        </div>

    </div>

@endsection
