{{-- resources/views/components/film-detail-list.blade.php --}}
@props([
    'items',
    'label',
    'indexRoute',
    'showRoute',
    'nameField' => 'title',
])

@if($items->count())
    <div class="film-detail-row">
        <span class="film-detail-label">
            <a href="{{ route($indexRoute) }}">{{ $label }}:</a>
        </span>
        <div class="span-show">
            @foreach($items as $item)
                <a href="{{ route($showRoute, ['slug' => $item->slug]) }}">
                    {{ $item->{$nameField} }}
                </a>@if(!$loop->last), @endif
            @endforeach
        </div>
    </div>
@endif
