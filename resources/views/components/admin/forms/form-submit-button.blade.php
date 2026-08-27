<div class="card-footer">

    @if(auth()->user()?->isAdmin() || auth()->user()?->isEditor())

        <button type="submit"
                class="btn btn-primary">
            Зберегти
        </button>

    @elseif(auth()->user()?->isViewer())

        <button type="button"
                class="btn btn-primary opacity-50 cursor-not-allowed"
                disabled
                title="Редагування доступне лише Admin та Editor">
            Зберегти
        </button>

    @endif

</div>
