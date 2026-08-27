{{-- resources/views/components/film-plain-row.blade.php --}}
@props([
    'value',
    'label',
    'class' => 'film-detail-row',
    'valueClass' => null,
])

@if($value)
    <div class="{{ $class }}">
        <span class="film-detail-label">{{ $label }}</span>
        <div class="{{ $valueClass }}">{{ $value }}</div>
    </div>
@endif
