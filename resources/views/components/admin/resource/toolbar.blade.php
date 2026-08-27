@props([
    'model',
    'createRoute',
    'createLabel' => 'Додати',
    'bulkDeleteConfirm' => 'Ви впевнені, що хочете видалити вибрані записи?',
])

<div class="mb-3 d-flex align-items-center">

    @can('create', $model)

        <a href="{{ route($createRoute) }}"
           class="btn btn-primary me-2">
            {{ $createLabel }}
        </a>

    @elseif(auth()->user()?->isViewer())

        <span title="Додавання доступне лише адміністратору">

            <button type="button"
                    class="btn btn-primary me-2 opacity-50 cursor-not-allowed"
                    disabled>
                {{ $createLabel }}
            </button>

        </span>

    @endcan


    @if(auth()->user()?->isAdmin())

        <button type="button"
                class="btn btn-danger bulk-action-btn d-none"
                data-action="delete"
                data-confirm="{{ $bulkDeleteConfirm }}">

            <i class="bi bi-x-circle"></i>

            Видалити вибрані
            (<span class="selected-count">0</span>)

        </button>

    @elseif(auth()->user()?->isViewer())

        <button type="button"
                class="btn btn-danger bulk-action-btn d-none opacity-50 cursor-not-allowed"
                disabled
                title="Масове видалення доступне лише адміністратору">

            <i class="bi bi-x-circle"></i>

            Видалити вибрані
            (<span class="selected-count">0</span>)

        </button>

    @endif

</div>
