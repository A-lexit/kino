@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => 'Мови',
    'description' => 'Повний список мов для озвучки та субтитрів для фільмів на сайті.'
])

@section('content')

    <div class="container-arch flex-arch">

        @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => 'Мови', 'url' => null],
        ]])

    </div>

    <div class="container-arch flex-arch">

        <div class="archive-area section-archive">

            <h1>Мови озвучки</h1>

            <p class="archive-description">
                Оберіть мову, щоб переглянути всі фільми та серіали з відповідною озвучкою або субтитрами.
            </p>

            <div class="archive-grid">

                @foreach($languages as $language)
                    <a href="{{ route('languages.show', ['slug' => $language->slug]) }}"
                       class="archive-item">
                        {{ $language->title }}
                    </a>
                @endforeach

            </div>

            <div class="pagination-new">
                {{ $languages->links() }}
            </div>

        </div>

    </div>

@endsection
