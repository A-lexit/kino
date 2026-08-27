@extends('admin.layouts.layout')
@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Коментарі</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>

                        <li class="breadcrumb-item active">
                            Коментарі
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
                                Список коментарів
                            </h3>
                        </div>


                        <div class="card-body">

                            {{-- =====================================================
                                 BULK DELETE
                            ====================================================== --}}

                            <div class="d-flex justify-content-start align-items-center mb-3">

                                @if(auth()->user()?->isAdmin())

                                    <button id="global-bulk-delete-btn"
                                            class="btn btn-danger d-none"
                                            type="button"
                                            data-url="{{ route('admin.comments.bulk-action') }}">

                                        <i class="bi bi-x-circle"></i>
                                        Видалити вибрані
                                        (<span id="global-selected-count">0</span>)

                                    </button>

                                @elseif(auth()->user()?->isViewer())

                                    <button id="global-bulk-delete-btn"
                                            class="btn btn-danger d-none opacity-50 cursor-not-allowed"
                                            type="button"
                                            disabled>

                                        <i class="bi bi-x-circle"></i>
                                        Видалити вибрані
                                        (<span id="global-selected-count">0</span>)

                                    </button>

                                @endif

                            </div>


                            @if(count($comments))

                                <div class="table-responsive">

                                    <table id="example1"
                                           class="table table-bordered table-hover align-middle">

                                        <thead>
                                        <tr>

                                            {{-- Checkbox --}}
                                            <th style="width: 40px" class="text-center">
                                                <input type="checkbox"
                                                       id="global-select-all"
                                                       class="form-check-input">
                                            </th>

                                            <th style="width: 20px">
                                                #
                                            </th>

                                            <th style="width: 15%">
                                                Ім'я
                                            </th>

                                            <th style="width: 50%">
                                                Текст коментаря
                                            </th>

                                            <th>
                                                Фільм
                                            </th>

                                            <th>
                                                Статус
                                            </th>

                                            <th style="width: 100px">
                                                Дії
                                            </th>

                                        </tr>
                                        </thead>


                                        <tbody>

                                        @foreach($comments as $comment)

                                            <tr id="entity-row-{{ $comment->id }}">

                                                {{-- Checkbox --}}
                                                <td class="text-center">
                                                    <input type="checkbox"
                                                           class="form-check-input entity-checkbox"
                                                           value="{{ $comment->id }}">
                                                </td>


                                                <td>
                                                    {{ $comment->id }}
                                                </td>


                                                <td>
                                                    {{ $comment->subject }}
                                                </td>


                                                <td>
                                                    {{ $comment->body }}
                                                </td>


                                                <td>
                                                    {{ $comment->film?->title ?? 'Фільм видалено' }}
                                                </td>


                                                <td id="comment-status-{{ $comment->id }}"
                                                    class="text-nowrap">

                                                    @if($comment->status == 1)

                                                        <span class="badge bg-success">
                                                            Активний
                                                        </span>

                                                    @else

                                                        <span class="badge bg-danger">
                                                            В очікуванні
                                                        </span>

                                                    @endif

                                                </td>


                                                <td class="text-nowrap">

                                                    {{-- Toggle --}}
                                                    @if(auth()->user()?->isAdmin())

                                                        <button type="button"
                                                                class="btn {{ $comment->status == 1 ? 'btn-warning' : 'btn-success' }} btn-sm float-start me-1 ajax-comment-toggle-btn"
                                                                data-url="{{ route('admin.comments.toggle', ['id' => $comment->id]) }}"
                                                                data-id="{{ $comment->id }}"
                                                                title="{{ $comment->status == 1 ? 'Деактивувати' : 'Активувати' }}">

                                                            <i class="bi {{ $comment->status == 1 ? 'bi-eye-slash-fill' : 'bi-eye-fill' }}"></i>

                                                        </button>

                                                    @elseif(auth()->user()?->isViewer())

                                                        <span title="Зміна статусу доступна лише адміністратору">

                                                            <button type="button"
                                                                    class="btn {{ $comment->status == 1 ? 'btn-warning' : 'btn-success' }} btn-sm float-start me-1 opacity-50 cursor-not-allowed"
                                                                    disabled>

                                                                <i class="bi {{ $comment->status == 1 ? 'bi-eye-slash-fill' : 'bi-eye-fill' }}"></i>

                                                            </button>

                                                        </span>

                                                    @endif


                                                    {{-- Delete --}}
                                                    @if(auth()->user()?->isAdmin())

                                                        <form action="{{ route('admin.comments.destroy', ['comment' => $comment->id]) }}"
                                                              method="POST"
                                                              class="d-inline-block ajax-delete-form"
                                                              data-id="{{ $comment->id }}">

                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                    class="btn btn-danger btn-sm">

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


                                <div class="d-flex justify-content-center mt-4">
                                    {{ $comments->links() }}
                                </div>

                            @else

                                <p>
                                    Коментарів поки немає...
                                </p>

                            @endif

                        </div>

                    </div>

                </div>
            </div>
        </div>

    </section>

@endsection


@push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const selectAll = document.getElementById('global-select-all');
            const checkboxes = document.querySelectorAll('.entity-checkbox');
            const bulkButton = document.getElementById('global-bulk-delete-btn');
            const selectedCount = document.getElementById('global-selected-count');

            function updateBulkButton() {
                const checked = document.querySelectorAll('.entity-checkbox:checked').length;

                if (selectedCount) {
                    selectedCount.textContent = checked;
                }

                if (bulkButton) {
                    bulkButton.classList.toggle('d-none', checked === 0);
                }

                if (selectAll) {
                    selectAll.checked =
                        checkboxes.length > 0 &&
                        checked === checkboxes.length;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {

                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = selectAll.checked;
                    });

                    updateBulkButton();
                });
            }

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    updateBulkButton();
                });
            });

            updateBulkButton();
        });







        // Перемикання статусу коментаря доступне тільки Admin.
        $(document).on('click', '.ajax-comment-toggle-btn', function (e) {

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

                        let statusCell = $('#comment-status-' + id);
                        let icon = btn.find('i');

                        if (response.status == 1) {

                            statusCell.html(
                                '<span class="badge bg-success">Активний</span>'
                            );

                            btn
                                .removeClass('btn-success')
                                .addClass('btn-warning')
                                .attr('title', 'Деактивувати');

                            icon
                                .removeClass('bi-eye-fill')
                                .addClass('bi-eye-slash-fill');

                        } else {

                            statusCell.html(
                                '<span class="badge bg-danger">В очікуванні</span>'
                            );

                            btn
                                .removeClass('btn-warning')
                                .addClass('btn-success')
                                .attr('title', 'Активувати');

                            icon
                                .removeClass('bi-eye-slash-fill')
                                .addClass('bi-eye-fill');

                        }

                    }
                },

                error: function () {

                    btn.prop('disabled', false);

                    alert('Сталася помилка при зміні статусу коментаря');

                }
            });

        });
    </script>

@endpush
