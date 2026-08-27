@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => $seoTitle,
    'description' => $seoDescription,
])
@section('content')

    <div class="container-arch flex-arch">
        <x-breadcrumbs :items="[
            ['title' => 'Головна', 'url' => route('home')],
            ['title' => $pageTitle, 'url' => null],
        ]" />
    </div>

    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">

            <h1>{{ $pageTitle }}</h1>

            <p class="archive-description">
                {{ $description }}
            </p>

            <div class="archive-grid">
                @foreach($items as $item)
                    <a href="{{ route($showRoute, ['slug' => $item->slug]) }}"
                       class="archive-item">
                        {{ $item->{$labelField} }}
                    </a>
                @endforeach
            </div>

            <div class="pagination-new">
                {{ $items->links() }}
            </div>
        </div>
    </div>

@endsection
