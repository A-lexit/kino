{{-- resources/views/admin/films/partials/trash-table.blade.php --}}

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Список фільмів (Кошик)</h3>
                </div>

                <div class="card-body">

                    <div class="mb-3 d-flex gap-2">

                        {{-- Відновити всі --}}
                        @if(auth()->user()?->isAdmin())
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary mr-2 admin-action-btn"
                                    data-url="{{ route('admin.films.restoreAll') }}"
                                    data-method="PATCH"
                                    data-confirm="Ви впевнені, що хочете відновити всі фільми?">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                Відновити всі фільми
                            </button>
                        @else
                            <span title="Відновлення фільмів доступне лише для Admin">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary opacity-50 cursor-not-allowed"
                                        disabled>
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    Відновити всі фільми
                                </button>
                            </span>
                        @endif


                        {{-- Очистити кошик повністю --}}
                        @if(auth()->user()?->isAdmin())
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger admin-action-btn"
                                    data-url="{{ route('admin.films.forceDeleteAll') }}"
                                    data-method="DELETE"
                                    data-confirm="Ви впевнені, що хочете видалити всі м'яко видалені фільми назавжди?">
                                <i class="bi bi-trash"></i>
                                Очистити кошик повністю
                            </button>
                        @else
                            <span title="Остаточне видалення доступне лише для Admin">
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger opacity-50 cursor-not-allowed"
                                        disabled>
                                    <i class="bi bi-trash"></i>
                                    Очистити кошик повністю
                                </button>
                            </span>
                        @endif

                    </div>


                    @if (count($sdelfilms))

                        {{-- Масові дії --}}
                        <div class="mb-3">

                            {{-- Масове відновлення --}}
                            @if(auth()->user()?->isAdmin())
                                <button type="button"
                                        class="btn btn-success bulk-action-btn mr-1 d-none"
                                        data-action="restore"
                                        data-confirm="Відновити всі вибрані фільми?">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    Відновити вибрані
                                    (<span class="selected-count">0</span>)
                                </button>
                            @else
                                <span title="Відновлення фільмів доступне лише для Admin">
                                    <button type="button"
                                            class="btn btn-success bulk-action-btn mr-1 d-none opacity-50 cursor-not-allowed"
                                            disabled>
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        Відновити вибрані
                                        (<span class="selected-count">0</span>)
                                    </button>
                                </span>
                            @endif


                            {{-- Масове остаточне видалення --}}
                            @if(auth()->user()?->isAdmin())
                                <button type="button"
                                        class="btn btn-dark bulk-action-btn d-none"
                                        data-action="force-delete"
                                        data-confirm="Видалити вибрані фільми НАЗАВЖДИ?">
                                    <i class="bi bi-x-circle"></i>
                                    Очистити вибрані
                                    (<span class="selected-count">0</span>)
                                </button>
                            @else
                                <span title="Остаточне видалення доступне лише для Admin">
                                    <button type="button"
                                            class="btn btn-dark bulk-action-btn d-none opacity-50 cursor-not-allowed"
                                            disabled>
                                        <i class="bi bi-x-circle"></i>
                                        Очистити вибрані
                                        (<span class="selected-count">0</span>)
                                    </button>
                                </span>
                            @endif

                        </div>


                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover text-nowrap">

                                <thead>
                                <tr>
                                    <th style="width: 40px; text-align: center;">
                                        <input type="checkbox"
                                               class="form-check-input select-all-checkbox">
                                    </th>

                                    <th style="width: 30px">#</th>
                                    <th>Назва</th>
                                    <th>Slug</th>
                                    <th>Дата</th>
                                    <th>Дії</th>
                                </tr>
                                </thead>

                                <tbody>

                                @foreach($sdelfilms as $film)

                                    <tr>

                                        <td style="text-align: center;">
                                            <input type="checkbox"
                                                   name="ids[]"
                                                   value="{{ $film->id }}"
                                                   class="form-check-input bulk-checkbox">
                                        </td>

                                        <td>{{ $film->id }}</td>

                                        <td>{{ $film->title }}</td>

                                        <td>{{ $film->slug }}</td>

                                        <td>{{ $film->created_at }}</td>

                                        <td>

                                            {{-- Відновлення --}}
                                            @if(auth()->user()?->isAdmin())

                                                <button type="button"
                                                        class="btn btn-success btn-sm row-action-btn mr-1"
                                                        data-id="{{ $film->id }}"
                                                        data-action="restore"
                                                        data-confirm="Відновити цей фільм?">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                    Відновити
                                                </button>

                                            @else

                                                <span title="Відновлення доступне лише для Admin">
                                                    <button type="button"
                                                            class="btn btn-success btn-sm opacity-50 cursor-not-allowed mr-1"
                                                            disabled>
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                        Відновити
                                                    </button>
                                                </span>

                                            @endif


                                            {{-- Остаточне видалення --}}
                                            @if(auth()->user()?->isAdmin())

                                                <button type="button"
                                                        class="btn btn-dark btn-sm row-action-btn"
                                                        data-id="{{ $film->id }}"
                                                        data-action="force-delete"
                                                        data-confirm="Видалити цей фільм НАЗАВЖДИ?">
                                                    <i class="bi bi-x-circle"></i>
                                                    Знищити
                                                </button>

                                            @else

                                                <span title="Остаточне видалення доступне лише для Admin">
                                                    <button type="button"
                                                            class="btn btn-dark btn-sm opacity-50 cursor-not-allowed"
                                                            disabled>
                                                        <i class="bi bi-x-circle"></i>
                                                        Знищити
                                                    </button>
                                                </span>

                                            @endif

                                        </td>

                                    </tr>

                                @endforeach

                                </tbody>

                            </table>
                        </div>

                    @else

                        <p class="mt-3">Кошик порожній...</p>

                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
