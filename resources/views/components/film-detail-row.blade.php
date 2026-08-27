{{-- resources/views/components/film-detail-row.blade.php --}}
@props([
    'item',
    'label',
    'indexRoute' => null,
    'showRoute' => null,
    'class' => null,
])

@if($item)
    <div class="film-detail-row">
        <span class="film-detail-label">
            @if($indexRoute)
                <a href="{{ route($indexRoute) }}">{{ $label }}:</a>
            @else
                {{ $label }}:
            @endif
        </span>
        <div class="{{ $class }}">
            @if($showRoute && $item->slug)
                <a href="{{ route($showRoute, ['slug' => $item->slug]) }}">{{ $item->title }}</a>
            @else
                {{ $item->title }}
            @endif
        </div>
    </div>
@endif
