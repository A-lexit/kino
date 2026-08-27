@extends('admin.layouts.layout')
@section('content')

    <section class="content-header">

        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Користувачі</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">

                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>

                        <li class="breadcrumb-item active">
                            Список користувачів
                        </li>

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
                            <h3 class="card-title">
                                Список користувачів
                            </h3>
                        </div>


                        <div class="card-body">

                            {{-- =====================================================
                                 ДОДАТИ / BULK DELETE
                            ====================================================== --}}

                            <div class="mb-3 d-flex align-items-center">

                                @if(auth()->user()?->isAdmin())

                                    <a href="{{ route('admin.users.create') }}"
                                       class="btn btn-success me-2">
                                        Додати користувача
                                    </a>

                                @elseif(auth()->user()?->isViewer())

                                    <button type="button"
                                            class="btn btn-primary me-2 opacity-50 cursor-not-allowed"
                                            disabled
                                            title="Додавання користувачів доступне лише адміністратору">
                                        Додати користувача
                                    </button>

                                @endif


                                @if(auth()->user()?->isAdmin())

                                    <button id="global-bulk-delete-btn"
                                            class="btn btn-danger d-none"
                                            type="button"
                                            data-url="{{ route('admin.users.bulk-action') }}">

                                        <i class="bi bi-x-circle"></i>

                                        Видалити вибрані
                                        (<span id="global-selected-count">0</span>)

                                    </button>

                                @elseif(auth()->user()?->isViewer())

                                    <button type="button"
                                            class="btn btn-danger opacity-50 cursor-not-allowed"
                                            disabled
                                            title="Масове видалення доступне лише адміністратору">

                                        <i class="bi bi-x-circle"></i>

                                        Видалити вибрані
                                        (<span id="global-selected-count">0</span>)

                                    </button>

                                @endif

                            </div>


                            @if(count($users))

                                <div class="table-responsive">

                                    <table id="example1"
                                           class="table table-bordered table-hover text-nowrap align-middle">

                                        <thead>

                                        <tr>

                                            {{-- Checkbox --}}
                                            <th style="width: 40px"
                                                class="text-center">

                                                @if(auth()->user()?->isAdmin())

                                                    <input type="checkbox"
                                                           id="global-select-all"
                                                           class="form-check-input">

                                                @elseif(auth()->user()?->isViewer())

                                                    <input type="checkbox"
                                                           class="form-check-input"
                                                           disabled>

                                                @endif

                                            </th>

                                            <th style="width: 30px">
                                                #
                                            </th>

                                            <th>
                                                Ім'я
                                            </th>

                                            <th>
                                                Email
                                            </th>

                                            <th>
                                                Роль
                                            </th>

                                            <th>
                                                Статус
                                            </th>

                                            <th>
                                                Аватар
                                            </th>

                                            <th>
                                                Дії
                                            </th>

                                        </tr>

                                        </thead>


                                        <tbody>

                                        @foreach($users as $user)

                                            <tr id="entity-row-{{ $user->id }}">

                                                {{-- Checkbox --}}
                                                <td class="text-center">

                                                    @if(auth()->user()?->isAdmin())

                                                        <input type="checkbox"
                                                               class="form-check-input entity-checkbox"
                                                               value="{{ $user->id }}">

                                                    @elseif(auth()->user()?->isViewer())

                                                        <input type="checkbox"
                                                               class="form-check-input entity-checkbox"
                                                               value="{{ $user->id }}"
                                                               disabled>

                                                    @endif

                                                </td>


                                                <td>
                                                    {{ $user->id }}
                                                </td>


                                                <td>
                                                    {{ $user->name }}
                                                </td>


                                                <td>
                                                    {{ $user->email }}
                                                </td>


                                                <td>

                                                    <span class="badge
                                                        {{
                                                            $user->role->value === 'admin'
                                                                ? 'bg-primary'
                                                                : (
                                                                    $user->role->value === 'editor'
                                                                        ? 'bg-info'
                                                                        : 'bg-secondary'
                                                                )
                                                        }}">

                                                        {{ $user->role->label() }}

                                                    </span>

                                                </td>


                                                <td id="user-status-{{ $user->id }}">

                                                    @if($user->is_banned == 1)

                                                        <span class="badge bg-danger">
                                                            Заблокований
                                                        </span>

                                                    @else

                                                        <span class="badge bg-success">
                                                            Активний
                                                        </span>

                                                    @endif

                                                </td>


                                                <td>

                                                    <img src="{{ app(\App\Media\UserImageResolver::class)->image($user) }}"
                                                         alt="avatar"
                                                         class="img-thumbnail"
                                                         width="60">

                                                </td>


                                                {{-- Дії --}}
                                                <td class="text-nowrap">

                                                    {{-- Toggle ban --}}
                                                    @if(auth()->user()?->isAdmin())

                                                        <button type="button"
                                                                class="btn {{ $user->is_banned == 0 ? 'btn-warning' : 'btn-success' }} btn-sm float-start me-1 ajax-toggle-btn"
                                                                data-url="{{ route('admin.users.toggle', ['id' => $user->id]) }}"
                                                                data-id="{{ $user->id }}"
                                                                title="{{ $user->is_banned == 0 ? 'Заблокувати' : 'Розблокувати' }}">

                                                            <i class="bi {{ $user->is_banned == 0 ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>

                                                        </button>

                                                    @elseif(auth()->user()?->isViewer())

                                                        <span title="Блокування доступне лише адміністратору">

                                                            <button type="button"
                                                                    class="btn {{ $user->is_banned == 0 ? 'btn-warning' : 'btn-success' }} btn-sm float-start me-1 opacity-50 cursor-not-allowed"
                                                                    disabled>

                                                                <i class="bi {{ $user->is_banned == 0 ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>

                                                            </button>

                                                        </span>

                                                    @endif


                                                    {{-- Edit --}}
                                                    @if(auth()->user()?->isAdmin())

                                                        <a href="{{ route('admin.users.edit', ['user' => $user->id]) }}"
                                                           class="btn btn-info btn-sm float-start me-1"
                                                           title="Редагувати">

                                                            <i class="bi bi-pencil"></i>

                                                        </a>

                                                    @elseif(auth()->user()?->isViewer())

                                                        <span title="Редагування доступне лише адміністратору">

                                                            <button type="button"
                                                                    class="btn btn-info btn-sm float-start me-1 opacity-50 cursor-not-allowed"
                                                                    disabled>

                                                                <i class="bi bi-pencil"></i>

                                                            </button>

                                                        </span>

                                                    @endif


                                                    {{-- Delete --}}
                                                    @if(auth()->user()?->isAdmin())

                                                        <form action="{{ route('admin.users.destroy', ['user' => $user->id]) }}"
                                                              method="post"
                                                              class="d-inline-block ms-1 ajax-delete-form"
                                                              data-id="{{ $user->id }}">

                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                    class="btn btn-danger btn-sm"
                                                                    title="Видалити">

                                                                <i class="bi bi-x-circle"></i>

                                                            </button>

                                                        </form>

                                                    @elseif(auth()->user()?->isViewer())

                                                        <span title="Видалення доступне лише адміністратору">

                                                            <button type="button"
                                                                    class="btn btn-danger btn-sm opacity-50 cursor-not-allowed"
                                                                    disabled>

                                                                <i class="bi bi-x-circle"></i>

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

                                <p>
                                    Користувачів поки немає...
                                </p>

                            @endif

                        </div>


                        <div class="card-footer clearfix">
                            {{ $users->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </section>

@endsection


@push('scripts')

    <script>

        // ============================================================
        // Перемикання бану користувача
        // ============================================================

        $(document).on('click', '.ajax-toggle-btn', function (e) {

            e.preventDefault();

            let btn = $(this);
            let url = btn.data('url');
            let id = btn.data('id');

            btn.prop('disabled', true);

            $.ajax({
                url: url,
                type: 'GET',

                success: function (response) {

                    btn.prop('disabled', false);

                    if (response.success) {

                        let statusCell = $('#user-status-' + id);
                        let icon = btn.find('i');

                        if (response.is_banned == 1) {

                            statusCell.html(
                                '<span class="badge bg-danger">Заблокований</span>'
                            );

                            btn
                                .removeClass('btn-warning')
                                .addClass('btn-success')
                                .attr('title', 'Розблокувати');

                            icon
                                .removeClass('bi-lock-fill')
                                .addClass('bi-unlock-fill');

                        } else {

                            statusCell.html(
                                '<span class="badge bg-success">Активний</span>'
                            );

                            btn
                                .removeClass('btn-success')
                                .addClass('btn-warning')
                                .attr('title', 'Заблокувати');

                            icon
                                .removeClass('bi-unlock-fill')
                                .addClass('bi-lock-fill');

                        }

                    }

                },

                error: function (xhr) {

                    btn.prop('disabled', false);

                    let message = xhr.responseJSON
                        ? xhr.responseJSON.message
                        : 'Сталася помилка';

                    alert(message);

                }

            });

        });


        // ============================================================
        // Одиночне видалення
        // ============================================================

        $(document).on('submit', '.ajax-delete-form', function (e) {

            e.preventDefault();

            if (!confirm('Ви впевнені, що хочете видалити цього користувача?')) {
                return false;
            }

            let form = $(this);
            let url = form.attr('action');
            let id = form.data('id');
            let btn = form.find('button[type="submit"]');

            btn.prop('disabled', true);

            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),

                success: function (response) {

                    if (response.success) {

                        $('#entity-row-' + id).fadeOut(300, function () {
                            $(this).remove();
                        });

                    }

                },

                error: function (xhr) {

                    btn.prop('disabled', false);

                    let message = xhr.responseJSON
                        ? xhr.responseJSON.message
                        : 'Сталася помилка при видаленні';

                    alert(message);

                }

            });

        });


        // ============================================================
        // Bulk checkbox
        // ============================================================

        $('#global-select-all').on('change', function () {

            $('.entity-checkbox').prop(
                'checked',
                $(this).prop('checked')
            );

            toggleBulkDeleteButton();

        });


        $(document).on('change', '.entity-checkbox', function () {

            let allChecked =
                $('.entity-checkbox').length ===
                $('.entity-checkbox:checked').length;

            $('#global-select-all').prop(
                'checked',
                allChecked
            );

            toggleBulkDeleteButton();

        });


        function toggleBulkDeleteButton() {

            let selectedCount =
                $('.entity-checkbox:checked').length;

            let bulkBtn =
                $('#global-bulk-delete-btn');

            $('#global-selected-count')
                .text(selectedCount);

            if (selectedCount > 0) {

                bulkBtn.removeClass('d-none');

            } else {

                bulkBtn.addClass('d-none');

            }

        }

    </script>

@endpush
