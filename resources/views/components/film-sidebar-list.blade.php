{{-- resources/views/components/film-sidebar-list.blade.php --}}
@props(['films', 'title', 'wrapperClass' => 'sidetitle mt-5'])

<div class="{{ $wrapperClass }}">
    <h2>{{ $title }}</h2>
    <ul>
        @foreach($films as $film)
            <hr>
            <li>
                <a href="{{ $film->url }}">
                    {{ $film->title }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
