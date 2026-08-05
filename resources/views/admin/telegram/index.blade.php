@extends('admin.layouts.layout')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Telegram підписники</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Telegram підписники</li>
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
                            <h3 class="card-title">Список підписників бота</h3>
                        </div>

                        <div class="card-body">
                            @if (count($subscribers))
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-hover text-nowrap align-middle">
                                        <thead>
                                        <tr>
                                            <th style="width: 30px">#</th>
                                            <th>Chat ID</th>
                                            <th>Username</th>
                                            <th>Ім'я</th>
                                            <th>Статус</th>
                                            <th>Дата підписки</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($subscribers as $subscriber)
                                            <tr id="entity-row-{{ $subscriber->id }}">
                                                <td>{{ $subscriber->id }}</td>
                                                <td><code>{{ $subscriber->chat_id }}</code></td>
                                                <td>
                                                    @if($subscriber->username)
                                                        <a href="https://t.me/{{ $subscriber->username }}" target="_blank">@ {{ $subscriber->username }}</a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>{{ $subscriber->first_name ?? '—' }}</td>
                                                <td id="user-status-{{ $subscriber->id }}">
                                                    @if($subscriber->is_banned)
                                                        <span class="badge bg-danger">Заблокований</span>
                                                    @else
                                                        <span class="badge bg-success">Активний</span>
                                                    @endif
                                                </td>
                                                <td>{{ $subscriber->created_at ? $subscriber->created_at->format('d.m.Y H:i') : '—' }}</td>
                                                {{--<td>
                                                    <button type="button"
                                                            class="btn {{ $subscriber->is_banned ? 'btn-success' : 'btn-warning' }} btn-sm float-start me-1 ajax-toggle-btn"
                                                            data-url="{{ route('admin.telegram.toggle-ban', ['subscriber' => $subscriber->id]) }}"
                                                            data-id="{{ $subscriber->id }}"
                                                            title="{{ $subscriber->is_banned ? 'Розблокувати' : 'Заблокувати' }}">
                                                        <i class="bi {{ $subscriber->is_banned ? 'bi-unlock-fill' : 'bi-lock-fill' }}"></i>
                                                    </button>

                                                    <form action="{{ route('admin.telegram.destroy', ['subscriber' => $subscriber->id]) }}"
                                                          method="post" class="d-inline-block ms-1 ajax-delete-form" data-id="{{ $subscriber->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Видалити">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>--}}
                                                <td>
                                                    @if($subscriber->is_admin)
                                                        <span class="badge bg-primary">Admin</span>
                                                    @else
                                                        <button type="button"
                                                                class="btn {{ $subscriber->is_banned ? 'btn-success' : 'btn-warning' }} btn-sm float-start me-1 ajax-toggle-btn"
                                                                data-url="{{ route('admin.telegram.toggle-ban', ['subscriber' => $subscriber->id]) }}"
                                                                data-id="{{ $subscriber->id }}"
                                                                title="{{ $subscriber->is_banned ? 'Розблокувати' : 'Заблокувати' }}">
                                                            <i class="bi {{ $subscriber->is_banned ? 'bi-unlock-fill' : 'bi-lock-fill' }}"></i>
                                                        </button>

                                                        <form action="{{ route('admin.telegram.destroy', ['subscriber' => $subscriber->id]) }}"
                                                              method="post" class="d-inline-block ms-1 ajax-delete-form" data-id="{{ $subscriber->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Видалити">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>Підписників поки немає...</p>
                            @endif
                        </div>

                        <div class="card-footer clearfix">
                            {{ $subscribers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Обробник для перемикання бану підписника
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

        // Обробник для видалення підписника
        $(document).on('submit', '.ajax-delete-form', function (e) {
            e.preventDefault();

            if (!confirm('Ви впевнені, що хочете видалити цього підписника?')) {
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
                        $('#entity-row-' + id).fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                },
                error: function (xhr) {
                    btn.prop('disabled', false);
                    let message = xhr.responseJSON ? xhr.responseJSON.message : 'Сталася помилка при видаленні';
                    alert(message);
                }
            });
        });
    </script>
@endpush
