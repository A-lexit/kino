@props([
    'item',
    'editRoute',
    'deleteRoute',
    'deleteConfirm' => 'Ви впевнені, що хочете видалити цей запис?',
])

@can('update', $item)

    <a href="{{ route($editRoute, $item) }}"
       class="btn btn-info btn-sm me-1"
       title="Редагувати">

        <i class="bi bi-pencil"></i>

    </a>

@elseif(auth()->user()?->isViewer())

    <a href="{{ route($editRoute, $item) }}"
       class="btn btn-info btn-sm me-1"
       title="Переглянути">

        <i class="bi bi-pencil"></i>

    </a>

@endcan


@can('delete', $item)

    <button type="button"
            class="btn btn-danger btn-sm row-action-btn"
            data-id="{{ $item->id }}"
            data-action="delete"
            data-url="{{ route($deleteRoute, $item) }}"
            data-method="DELETE"
            data-confirm="{{ $deleteConfirm }}">

        <i class="bi bi-x-circle"></i>

    </button>

@elseif(auth()->user()?->isViewer())

    <span title="Видалення доступне лише адміністратору">

        <button type="button"
                class="btn btn-danger btn-sm opacity-50 cursor-not-allowed"
                disabled>

            <i class="bi bi-x-circle"></i>

        </button>

    </span>

@endcan
