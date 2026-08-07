@extends('layouts/layout')
@include('layouts.inc.seo', ['name' => 'Рейтинги фільмів', 'description' => 'Список усіх рейтингів на сайті.'])
@section('content')


    <div class="container-arch flex-arch">

    @include('layouts.inc.breadcrumbs', ['breadcrumbs' => [
    ['title' => 'Головна', 'url' => route('home')],
    ['title' => 'Актори', 'url' => null],
]])

    </div>

    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <div class="container-archive-block">

                <div class="archive-grid">
                    @foreach($actors as $actor)
                        <a href="{{ route('actors.show', ['slug' => $actor->slug]) }}"
                           class="archive-item">
                            {{ $actor->name }}
                        </a>
                    @endforeach
                </div>


            </div>
            <div class="pagination-new">
                {{$actors->links()}}
            </div>
        </div>
    </div>

@endsection

