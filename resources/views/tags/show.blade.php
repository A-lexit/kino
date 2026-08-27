@extends('layouts/layout')
@include('layouts.inc.seo', [
    'title' => $seoTitle,
    'description' => $seoDescription,
])
@section('content')

    <div class="container-arch flex-arch">

        <x-breadcrumbs :items="$breadcrumbs" />

    </div>
    <div class="container-arch flex-arch">
        <div class="archive-area section-archive">
            <h1>{{ $pageTitle }}</h1>
            <div class="sorting-wrapper">

                <form action="{{ url()->current() }}" method="GET" class="filters-form">
                    @include($filterPartial)
                    @include('layouts.inc.body.sorting')
                </form>

            </div>

            @include('layouts.inc.film-archive', [
                'films' => $films,
            ])

            <div class="pagination-new">
                {{ $films->links() }}
            </div>

        </div>
    </div>
@endsection
