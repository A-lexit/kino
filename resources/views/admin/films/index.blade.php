@extends('admin.layouts.layout')
@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Фільми</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Головна</a></li>
                        <li class="breadcrumb-item active">Список фільмів</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content table-container" id="active-films-section">
        @include('admin.films.partials-index.active-table', ['films' => $films])
    </section>

    <section class="content table-container" id="trash-films-section">
        @if(auth()->user()?->isAdmin() || auth()->user()?->isViewer())
            @include('admin.films.partials-index.trash-table')
        @endif
    </section>

@endsection
