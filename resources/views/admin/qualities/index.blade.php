@extends('admin.layouts.layout')
@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Якість відео</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Blank Page</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Список якостей відео</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 d-flex align-items-center">
                                <a href="{{ route('admin.qualities.create') }}" class="btn btn-primary me-2">Додати якість відео</a>

                                <button id="global-bulk-delete-btn" class="btn btn-danger d-none" type="button" data-url="{{ route('admin.qualities.bulk-action') }}">
                                    <i class="bi bi-trash"></i> Видалити вибрані (<span id="global-selected-count">0</span>)
                                </button>
                            </div>

                            @if (count($qualities))
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-hover text-nowrap">
                                        <thead>
                                        <tr>
                                            <th style="width: 40px" class="text-center">
                                                <input type="checkbox" id="global-select-all" style="transform: scale(1.2); cursor: pointer;">
                                            </th>
                                            <th style="width: 30px">#</th>
                                            <th>Назва</th>
                                            <th>Slug</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($qualities as $quality)
                                            <tr id="entity-row-{{ $quality->id }}">
                                                <td class="text-center">
                                                    <input type="checkbox" class="entity-checkbox" value="{{ $quality->id }}" style="transform: scale(1.2); cursor: pointer;">
                                                </td>
                                                <td>{{ $quality->id }}</td>
                                                <td>{{ $quality->title }}</td>
                                                <td>{{ $quality->slug }}</td>
                                                <td>
                                                    <a href="{{ route('admin.qualities.edit', ['quality' => $quality->id]) }}"
                                                       class="btn btn-info btn-sm float-start me-1">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>

                                                    <form action="{{ route('admin.qualities.destroy', ['quality' => $quality->id]) }}"
                                                          method="post" class="d-inline-block ms-1 ajax-delete-form" data-id="{{ $quality->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>Якостей відео поки немає...</p>
                            @endif
                        </div>
                        <div class="card-footer clearfix">
                            {{ $qualities->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
