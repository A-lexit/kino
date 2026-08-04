<a wire:click.prevent="toggle"
   class="bi {{ $comment->status == 1 ? 'bi-lock-fill' : 'bi-hand-thumbs-up-fill' }}"
   style="cursor: pointer; {{ $comment->status != 1 ? 'color: green;' : '' }}">
</a>
