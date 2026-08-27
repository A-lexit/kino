{{-- resources/views/admin/films/partials/active-table.blade.php --}}

@push('css')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

<div class="container-fluid mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Список фільмів</h3>
                </div>

                <div class="card-body">

                    {{-- =====================================================
                         ДОДАТИ ФІЛЬМ
                    ====================================================== --}}

                    @can('create', \App\Models\Film::class)

                        <a href="{{ route('admin.films.create') }}"
                           class="btn btn-primary mb-3">
                            Додати фільм
                        </a>

                    @elseif(auth()->user()?->isViewer())

                        <span title="Додавання фільмів доступне лише адміністратору">
                            <button type="button"
                                    class="btn btn-primary mb-3 opacity-50 cursor-not-allowed"
                                    disabled>
                                Додати фільм
                            </button>
                        </span>

                    @endcan


                    {{-- =====================================================
                         EXPORT / IMPORT
                    ====================================================== --}}

                    @if(auth()->user()?->isAdmin())

                        <div class="flex justify-between items-center mb-4">
                            <div class="flex gap-2">

                                <a href="{{ route('admin.films.export') }}"
                                   class="btn btn-success">
                                    📥 Експортувати в Excel
                                </a>

                                <button type="button"
                                        class="btn btn-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#importModal">
                                    📤 Імпортувати з Excel
                                </button>

                            </div>
                        </div>

                        {{-- Modal імпорту --}}
                        <div class="modal fade"
                             id="importModal"
                             tabindex="-1"
                             aria-labelledby="importModalLabel"
                             aria-hidden="true">

                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <form action="{{ route('admin.films.excel.import') }}"
                                          method="POST"
                                          enctype="multipart/form-data">

                                        @csrf

                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="importModalLabel">
                                                Імпорт фільмів з Excel
                                            </h5>

                                            <button type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal"
                                                    aria-label="Close">
                                            </button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Оберіть файл (.xlsx, .xls, .csv)
                                                </label>

                                                <input type="file"
                                                       name="file"
                                                       accept=".xlsx, .xls, .csv"
                                                       required
                                                       class="form-control">

                                                @error('file')
                                                <div class="text-danger text-sm mt-1">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Режим імпорту
                                                </label>

                                                <select name="import_mode"
                                                        class="form-control">

                                                    <option value="soft">
                                                        М'який (тільки нові, попередження про зв'язки)
                                                    </option>

                                                    <option value="strict">
                                                        Суворий (зупинка при відсутності зв'язків)
                                                    </option>

                                                    <option value="update_only">
                                                        Тільки оновлення існуючих (з повним перезаписом)
                                                    </option>

                                                    <option value="insert_update">
                                                        Додавання + оновлення (з повним перезаписом)
                                                    </option>

                                                    <option value="update_merge">
                                                        Тільки оновлення (без перезапису заповнених полів)
                                                    </option>

                                                    <option value="insert_update_merge">
                                                        Додавання + оновлення (без перезапису заповнених полів)
                                                    </option>

                                                </select>
                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button"
                                                    class="btn btn-secondary"
                                                    data-bs-dismiss="modal">
                                                Скасувати
                                            </button>

                                            <button type="submit"
                                                    class="btn btn-primary">
                                                Завантажити та імпортувати
                                            </button>

                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>

                    @elseif(auth()->user()?->isViewer())

                        {{-- Viewer бачить, але не може використовувати --}}

                        <div class="flex justify-between items-center mb-4">
                            <div class="flex gap-2">

                                <span title="Експорт доступний лише адміністратору">
                                    <button type="button"
                                            class="btn btn-success opacity-50 cursor-not-allowed"
                                            disabled>
                                        📥 Експортувати в Excel
                                    </button>
                                </span>

                                <span title="Імпорт доступний лише адміністратору">
                                    <button type="button"
                                            class="btn btn-secondary opacity-50 cursor-not-allowed"
                                            disabled>
                                        📤 Імпортувати з Excel
                                    </button>
                                </span>

                            </div>
                        </div>

                    @endif


                    {{-- =====================================================
                         РЕЗУЛЬТАТ ІМПОРТУ
                    ====================================================== --}}

                    @if(session()->has('import_result'))

                        @php
                            $result = session('import_result');
                        @endphp

                        <div class="alert alert-success mb-3">

                            <div>
                                {{ $result['message'] }}
                            </div>

                            <div>
                                <b>Створено нових:</b>
                                {{ $result['successCount'] }}
                            </div>

                            <div>
                                <b>Оновлено існуючих:</b>
                                {{ $result['updatedCount'] }}
                            </div>

                            <div>
                                <b>Помилки:</b>
                                {{ $result['failCount'] }}
                            </div>

                            @if(!empty($result['warnings']))

                                <div class="mt-2 pt-2 border-top border-success">

                                    <b>Попередження:</b>

                                    <ul class="mb-0 ps-3">
                                        @foreach($result['warnings'] as $warning)
                                            <li>{{ $warning }}</li>
                                        @endforeach
                                    </ul>

                                </div>

                            @endif

                        </div>

                    @endif


                    {{-- =====================================================
                         ТАБЛИЦЯ
                    ====================================================== --}}

                    @if(count($films))

                        {{-- Bulk delete доступний тільки Admin --}}
                        @if(auth()->user()?->isAdmin())

                            <div class="mb-3">

                                <button type="button"
                                        class="btn btn-danger bulk-action-btn d-none"
                                        data-action="delete"
                                        data-confirm="Перемістити вибрані фільми в кошик?">

                                    <i class="bi bi-trash"></i>

                                    Видалити вибрані
                                    (<span class="selected-count">0</span>)

                                </button>

                            </div>

                        @elseif(auth()->user()?->isViewer())

                            <div class="mb-3">

                                <button type="button"
                                        class="btn btn-danger bulk-action-btn d-none opacity-50 cursor-not-allowed"
                                        disabled
                                        data-action="delete"
                                        title="Масове видалення доступне лише адміністратору">

                                    <i class="bi bi-trash"></i>

                                    Видалити вибрані
                                    (<span class="selected-count">0</span>)

                                </button>

                            </div>

                        @endif


                        <div class="table-responsive">

                            <table id="filmsTable"
                                   class="table table-bordered table-hover text-nowrap">

                                <thead>
                                <tr>

                                    {{-- Checkbox --}}
                                    <th style="width: 40px; text-align: center;" class="{{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">
                                        <input type="checkbox" class="form-check-input select-all-checkbox">
                                    </th>
                                    <th style="width: 30px">
                                        #
                                    </th>

                                    <th>
                                        Назва
                                    </th>

                                    <th>
                                        Slug
                                    </th>

                                    <th>
                                        Категорія
                                    </th>

                                    <th>
                                        Статус
                                    </th>

                                    <th>
                                        Автор
                                    </th>

                                    <th>
                                        Дата
                                    </th>

                                    <th>
                                        Дії
                                    </th>

                                </tr>
                                </thead>


                                <tbody>

                                @foreach($films as $film)

                                    <tr>

                                        {{-- Checkbox --}}
                                        <td style="text-align: center;" class="{{ (auth()->user()?->isAdmin() || auth()->user()?->isViewer()) ? '' : 'd-none' }}">
                                            <input type="checkbox" name="ids[]" value="{{ $film->id }}" class="form-check-input bulk-checkbox">
                                        </td>


                                        <td>
                                            {{ $film->id }}
                                        </td>


                                        <td>
                                            {{ $film->title }}
                                        </td>


                                        <td>
                                            {{ $film->slug }}
                                        </td>


                                        <td>
                                            {{ $film->category->title ?? 'Без категорії' }}
                                        </td>


                                        <td>

                                            @if($film->publish_status === \App\Enums\FilmStatus::Published)

                                                <span class="badge badge-light text-success">
                                                    Опубліковано
                                                </span>

                                            @else

                                                <span class="badge badge-light text-secondary">
                                                    Чернетка
                                                </span>

                                            @endif

                                        </td>


                                        <td>
                                            {{ $film->user?->name ?? 'Системний' }}
                                        </td>


                                        <td>
                                            {{ $film->created_at }}
                                        </td>


                                        {{-- =================================================
                                             ДІЇ
                                        ================================================== --}}

                                        <td>

                                            {{-- =========================
                                                 EDIT
                                            ========================== --}}

                                            @can('update', $film)

                                                <a href="{{ route('admin.films.edit', ['film' => $film->id]) }}"
                                                   class="btn btn-info btn-sm me-1"
                                                   title="Редагувати">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                            @elseif(auth()->user()?->isViewer())

                                                <a href="{{ route('admin.films.edit', ['film' => $film->id]) }}"
                                                   class="btn btn-info btn-sm me-1"
                                                   title="Переглянути">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                            @endcan


                                            {{-- =========================
                                                 DELETE → TRASH
                                            ========================== --}}

                                            @can('delete', $film)

                                                <button type="button"
                                                        class="btn btn-warning btn-sm row-action-btn me-1"
                                                        data-id="{{ $film->id }}"
                                                        data-action="delete"
                                                        data-confirm="Видалити цей фільм в кошик?">

                                                    <i class="bi bi-trash"></i>
                                                    Кошик

                                                </button>

                                            @elseif(auth()->user()?->isViewer())

                                                <span title="Видалення доступне лише адміністратору">

                                                    <button type="button"
                                                            class="btn btn-warning btn-sm opacity-50 cursor-not-allowed"
                                                            disabled>

                                                        <i class="bi bi-trash"></i>
                                                        Кошик

                                                    </button>

                                                </span>

                                            @endcan


                                            {{-- =========================
                                                 FORCE DELETE
                                            ========================== --}}

                                            @can('forceDelete', $film)

                                                <button type="button"
                                                        class="btn btn-danger btn-sm row-action-btn"
                                                        data-id="{{ $film->id }}"
                                                        data-action="force-delete"
                                                        data-confirm="Остаточно видалити цей фільм без можливості відновлення?">

                                                    <i class="bi bi-x-circle"></i>
                                                    Видалити

                                                </button>

                                            @elseif(auth()->user()?->isViewer())

                                                <span title="Остаточне видалення доступне лише адміністратору">

                                                    <button type="button"
                                                            class="btn btn-danger btn-sm opacity-50 cursor-not-allowed"
                                                            disabled>

                                                        <i class="bi bi-x-circle"></i>
                                                        Видалити

                                                    </button>

                                                </span>

                                            @endcan

                                        </td>

                                    </tr>

                                @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                        <p>
                            Фільмів поки немає...
                        </p>

                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
