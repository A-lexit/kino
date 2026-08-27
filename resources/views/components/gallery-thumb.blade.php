{{-- resources/views/components/gallery-thumb.blade.php --}}
@props(['image', 'fancyboxGroup'])

<a href="{{ $image['src'] }}"
   data-fancybox="{{ $fancyboxGroup }}"
   data-caption="{{ $image['title'] }}">
    <img src="{{ $image['thumb'] }}"
         alt="{{ $image['title'] }}"
         width="{{ \App\Constants\ImageSizes::GALLERY_THUMB_WIDTH }}"
         height="{{ \App\Constants\ImageSizes::GALLERY_THUMB_HEIGHT }}"
         loading="lazy"
         decoding="async"
        {{ $attributes->class(['film-img']) }}>
</a>
