{{-- resources/views/admin/films/partials/trash-table.blade.php --}}
{{-- Очікує $sdelfilms --}}

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Список фільмів (Кошик)</h3>
                </div>
                <div class="card-body">

                    @if(auth()->user()->isAdmin())
                        <div class="mb-3 d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary mr-2 admin-action-btn"
                                    data-url="{{ route('admin.films.restoreAll') }}"
                                    data-method="PATCH"
                                    data-confirm="Ви впевнені, що хочете відновити всі фільми?">
                                Відновити всі фільми
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger admin-action-btn"
                                    data-url="{{ route('admin.films.forceDeleteAll') }}"
                                    data-method="DELETE"
                                    data-confirm="Ви впевнені, що хочете видалити всі м'яко видалені фільми назавжди?">
                                Очистити кошик повністю
                            </button>
                        </div>
                    @endif

                    @if (count($sdelfilms))
                        <div class="mb-3">
                            <button type="button" class="btn btn-success bulk-action-btn mr-1 d-none" data-action="restore" data-confirm="Відновити всі вибрані фільми?">
                                Відновити вибрані (<span class="selected-count">0</span>)
                            </button>
                            <button type="button" class="btn btn-dark bulk-action-btn d-none" data-action="force-delete" data-confirm="Видалити вибрані фільми НАЗАВЖДИ?">
                                Очистити вибрані (<span class="selected-count">0</span>)
                            </button>
                        </div>
                        <div class="table-responsive mt-3">
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
                                @foreach($sdelfilms as $film)
                                    @if(Auth::user()->is_admin == 1 || Auth::user()->id == $film->author_id)
                                        <tr>
                                            <td style="text-align: center;">
                                                <input type="checkbox" name="ids[]" value="{{ $film->id }}" class="form-check-input bulk-checkbox">
                                            </td>
                                            <td>{{ $film->id }}</td>
                                            <td>{{ $film->title }}</td>
                                            <td>{{ $film->slug }}</td>
                                            <td>{{ $film->created_at }}</td>
                                            <td>
                                                @can('restore', $film)
                                                    <button type="button" class="btn btn-success btn-sm row-action-btn mr-1" data-id="{{ $film->id }}" data-action="restore" data-confirm="Відновити цей фільм?">Відновити</button>
                                                @endcan

                                                @can('forceDelete', $film)
                                                    <button type="button" class="btn btn-dark btn-sm row-action-btn" data-id="{{ $film->id }}" data-action="force-delete" data-confirm="Видалити цей фільм НАЗАВЖДИ?">Знищити</button>
                                                @endcan

                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="mt-3">Кошик порожній...</p>
                    @endif
                </div>
                <div class="card-footer clearfix">
                    {{ $sdelfilms->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
