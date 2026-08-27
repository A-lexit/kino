{{-- resources/views/components/home-section.blade.php --}}
@props(['films', 'title', 'categorySlug'])

<div class="section-default-posts">
    <h1>
        <a href="{{ route('categories.show', ['slug' => $categorySlug]) }}">{{ $title }}</a>
    </h1>
    <div class="container-default">
        @foreach($films as $film)
            <x-home-poster-card :film="$film" :fetchpriority="$loop->first ? 'high' : null" />
        @endforeach
    </div>
</div>
