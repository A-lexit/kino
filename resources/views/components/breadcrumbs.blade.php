{{-- resources/views/components/breadcrumbs.blade.php --}}
@props(['items'])

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach($items as $item)
            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                @if($item['url'] && !$loop->last)
                    <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                @else
                    {{ $item['title'] }}
                @endif
            </li>
        @endforeach
    </ol>
</nav>
