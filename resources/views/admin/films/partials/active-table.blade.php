{{-- resources/views/admin/films/partials/active-table.blade.php --}}
{{-- Очікує $films --}}

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Список фільмів</h3>
                </div>
                <div class="card-body">

                    @can('create', \App\Models\Film::class)
                    <a href="{{ route('admin.films.create') }}" class="btn btn-primary mb-3">Додати фільм</a>
                    @endcan

                    @if (count($films))

                            @if(auth()->user()->isAdmin())
                                <div class="mb-3">
                                    <button type="button" class="btn btn-danger bulk-action-btn d-none" data-action="delete" data-confirm="Перемістити вибрані фільми в кошик?">
                                        <i class="bi bi-trash"></i> Видалити вибрані (<span class="selected-count">0</span>)
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive">
                            <table class="table table-bordered table-hover text-nowrap">
                                <thead>
                                <tr>
                                    <th style="width: 40px; text-align: center;">
                                        <input type="checkbox" class="form-check-input select-all-checkbox">
                                    </th>
                                    <th style="width: 30px">#</th>
                                    <th>Назва</th>
                                    <th>Slug</th>
                                    <th>Дата</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($films as $film)
                                    <tr>
                                        <td style="text-align: center;">
                                            <input type="checkbox" name="ids[]" value="{{ $film->id }}" class="form-check-input bulk-checkbox">
                                        </td>
                                        <td>{{ $film->id }}</td>
                                        <td>{{ $film->title }}</td>
                                        <td>{{ $film->slug }}</td>
                                        <td>{{ $film->created_at }}</td>
                                        <td>
                                            <a href="{{ route('admin.films.edit', ['film' => $film->id]) }}" class="btn btn-info btn-sm float-left mr-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            @can('delete', $film)
                                                <button type="button" class="btn btn-danger btn-sm row-action-btn" data-id="{{ $film->id }}" data-action="delete" data-confirm="Видалити цей фільм в кошик?">
                                                    <i class="bi bi-trash"></i> Кошик
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>Фільмів поки немає...</p>
                    @endif
                </div>
                <div class="card-footer clearfix">
                    {{ $films->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
