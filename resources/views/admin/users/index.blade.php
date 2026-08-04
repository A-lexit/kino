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
                            <h3 class="card-title">Список користувачів</h3>
                        </div>

                        <div class="card-body">
                            <div class="mb-3 d-flex align-items-center">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary me-2">Додати користувача</a>

                                <button id="global-bulk-delete-btn" class="btn btn-danger d-none" type="button" data-url="{{ route('admin.users.bulk-action') }}">
                                    <i class="bi bi-trash"></i> Видалити вибрані (<span id="global-selected-count">0</span>)
                                </button>
                            </div>

                            @if (count($users))
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-hover text-nowrap align-middle">
                                        <thead>
                                        <tr>
                                            <th style="width: 40px" class="text-center">
                                                <input type="checkbox" id="global-select-all" style="transform: scale(1.2); cursor: pointer;">
                                            </th>
                                            <th style="width: 30px">#</th>
                                            <th>Назва</th>
                                            <th>email</th>
                                            <th>Роль</th>
                                            <th>Статус</th>
                                            <th>Аватар</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($users as $user)
                                            <tr id="entity-row-{{ $user->id }}">
                                                <td class="text-center">
                                                    <input type="checkbox" class="entity-checkbox" value="{{ $user->id }}" style="transform: scale(1.2); cursor: pointer;">
                                                </td>
                                                <td>{{ $user->id }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
    <span class="badge {{ $user->role->value === 'admin' ? 'bg-primary' : ($user->role->value === 'editor' ? 'bg-info' : 'bg-secondary') }}">
        {{ $user->role->label() }}
    </span>
                                                </td>

                                                <td id="user-status-{{ $user->id }}">
                                                    @if($user->is_banned == 1)
                                                        <span class="badge bg-danger">Заблокований</span>
                                                    @else
                                                        <span class="badge bg-success">Активний</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <img src="{{ app(\App\Media\UserImageResolver::class)->image($user) }}"
                                                         alt="avatar" class="img-thumbnail" width="60">
                                                </td>


                                                <td>
                                                    <button type="button"
                                                            class="btn {{ $user->is_banned == 0 ? 'btn-warning' : 'btn-success' }} btn-sm float-start me-1 ajax-toggle-btn"
                                                            data-url="{{ route('admin.users.toggle', ['id' => $user->id]) }}"
                                                            data-id="{{ $user->id }}"
                                                            title="{{ $user->is_banned == 0 ? 'Заблокувати' : 'Розблокувати' }}">
                                                        <i class="bi {{ $user->is_banned == 0 ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>
                                                    </button>


                                                    <a href="{{ route('admin.users.edit', ['user' => $user->id]) }}"
                                                       class="btn btn-info btn-sm float-start me-1">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>

                                                    <form action="{{ route('admin.users.destroy', ['user' => $user->id]) }}"
                                                          method="post" class="d-inline-block ms-1 ajax-delete-form" data-id="{{ $user->id }}">
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
                                <p>Користувачів поки немає...</p>
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
        // Обробник для перемикання бану користувачів (тільки тут!)
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
                            statusCell.html('<span class="badge bg-danger">Заблокований</span>');
                            btn.removeClass('btn-warning').addClass('btn-success').attr('title', 'Розблокувати');
                            icon.removeClass('bi-lock-fill').addClass('bi-unlock-fill');
                        } else {
                            statusCell.html('<span class="badge bg-success">Активний</span>');
                            btn.removeClass('btn-success').addClass('btn-warning').attr('title', 'Заблокувати');
                            icon.removeClass('bi-unlock-fill').addClass('bi-lock-fill');
                        }
                    }
                },
                error: function (xhr) {
                    btn.prop('disabled', false);
                    let message = xhr.responseJSON ? xhr.responseJSON.message : 'Сталася помилка';
                    alert(message);
                }
            });
        });


        // Обробник для одиничного видалення користувача
        $(document).on('submit', '.ajax-delete-form', function (e) {
            e.preventDefault(); // Зупиняємо стандартний перехід на іншу сторінку

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
                type: 'POST', // Laravel сам зрозуміє, що це DELETE завдяки @method('DELETE') у формі
                data: form.serialize(), // Передаємо дані форми (включаючи @csrf)
                success: function (response) {
                if (response.success) {
                // Плавно приховуємо рядок таблиці і видаляємо його з DOM
                $('#entity-row-' + id).fadeOut(300, function() {
                    $(this).remove();
                });

                // Опціонально: можна вивести сповіщення
                // alert(response.message);
            }
        },
            error: function (xhr) {
                btn.prop('disabled', false);
                let message = xhr.responseJSON ? xhr.responseJSON.message : 'Сталася помилка при видаленні';
                alert(message);
            }

        });
        });



        // --- Логіка чекбоксів та масового видалення ---

        // Виділити/зняти виділення з усіх
        $('#global-select-all').on('change', function() {
            $('.entity-checkbox').prop('checked', $(this).prop('checked'));
            toggleBulkDeleteButton();
        });

        // Слухач для окремих чекбоксів
        $(document).on('change', '.entity-checkbox', function() {
            // Якщо вибрані всі, ставимо галочку на головному
            let allChecked = $('.entity-checkbox').length === $('.entity-checkbox:checked').length;
            $('#global-select-all').prop('checked', allChecked);

            toggleBulkDeleteButton();
        });

        // Функція відображення кнопки та підрахунку
        function toggleBulkDeleteButton() {
            let selectedCount = $('.entity-checkbox:checked').length;
            let bulkBtn = $('#global-bulk-delete-btn');

            $('#global-selected-count').text(selectedCount);

            if (selectedCount > 0) {
                bulkBtn.removeClass('d-none');
            } else {
                bulkBtn.addClass('d-none');
            }
        }

    </script>
@endpush
