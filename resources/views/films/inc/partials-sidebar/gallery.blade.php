{{-- resources/views/films/inc/gallery.blade.php --}}
{{-- Очікує $images = $film->galleryImages() --}}

@if (!empty($images))
    <div class="gallery nnn film-gallery-grid">
        @foreach ($images as $image)
            <x-gallery-thumb :image="$image" :fancybox-group="$fancyboxGroup" />
        @endforeach
    </div>

    @once
        <style>
            .film-gallery-grid{
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 6px;
                width: 100%;
                margin-bottom: 20px;
            }
            .film-gallery-grid a{
                display: block;
                min-width: 0;
                aspect-ratio: 339 / 193;
                overflow: hidden;
                border-radius: 4px;
                height: 50px;
                width: 100%;
            }
            .film-gallery-grid img{
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            /* Показуємо тільки перші 4 мініатюри, 5-та лишається
               доступною через стрілки перегляду у Fancybox */
            .film-gallery-grid a:nth-child(n + 5){
                display: none;
            }
        </style>
    @endonce
@endif
