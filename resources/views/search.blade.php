@extends('layouts/layout')
@section('title', 'Сторінка сайту Ларавел Блог :: Search')
@section('content')

    <div class="container">
        <h1>Результати пошуку: "{{ $s }}"</h1>
        <h2 class="mt-3 mb-5">Знайдено {{ $films->count() }} {{ pluralize_results($films->count()) }}</h2>
        <div class="section-default-films">
            <div class="container-default">

                @if($films->count())
                    @foreach($films as $film)
                        <div class="child">
                            <img width="339" height="193" src=

                                "{{ app(\App\Media\FilmImageResolver::class)->image($film)}}"
                                 class="attachment-medium size-medium wp-film-image" alt="" decoding="async" srcset="" sizes="(max-width: 339px) 100vw, 339px" />

                            <a href="{{route('single', ['category' => $film->category->slug, 'slug' => $film->slug])}}">
                                <h3>{{$film->title}}</h3>
                            </a>
                        </div>
                    @endforeach
                @else
                    По вашому запиту нічого не знайдено...
                @endif

            </div>
        </div>

        <div class="pagination-new">
            {{ $films->appends(['s' => request()->s])->links() }}
        </div>
    </div>

@endsection
