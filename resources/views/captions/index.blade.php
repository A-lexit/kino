@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => 'Субтитри',
    'description' => 'Список усіх субтитрів на сайті.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Субтитри', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">

            <div class="archive-grid">

                @foreach($captions as $caption)
                    <a href="{{ route('captions.show', ['slug' => $caption->slug]) }}"
                       class="archive-item">
                        {{ $caption->title }}
                    </a>
                @endforeach

            </div>

            <div class="pagination-new">
                {{ $captions->links() }}
            </div>

        </div>

    </div>

@endsection
