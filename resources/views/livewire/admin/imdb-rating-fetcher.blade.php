{{-- resources/views/livewire/admin/imdb-rating-fetcher.blade.php --}}

<div>
    <label>Рейтинг IMDB</label>
    <div class="mb-2">
        @if($film->imdb_rating)
            <span class="badge badge-warning" style="font-size: 14px;">⭐ {{ $film->imdb_rating }}</span>
            <small class="text-muted">(imdb_id: {{ $film->imdb_id }})</small>
        @else
            <span class="text-muted">Ще не отримано</span>
        @endif
    </div>

    @if($errorMessage)
        <div class="alert alert-warning py-1 px-2" style="font-size: 13px;">{{ $errorMessage }}</div>
    @endif

    <button
        type="button"
        wire:click="fetch"
        wire:loading.attr="disabled"
        wire:target="fetch"
        class="btn btn-sm btn-outline-secondary"
    >
        <span wire:loading.remove wire:target="fetch">Оновити рейтинг з OMDb</span>
        <span wire:loading wire:target="fetch">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Пошук на OMDb...
        </span>
    </button>
</div>
