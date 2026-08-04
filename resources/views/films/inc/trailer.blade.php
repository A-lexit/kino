{{-- Очікує $film --}}

@if (app(\App\Media\FilmVideoResolver::class)->hasTrailer($film))
    <div class="film-trailer"
         data-poster=
             "{{ app(\App\Media\FilmImageResolver::class)->image($film)}}"
         @if($film->trailer_youtube_id)
             data-type="youtube"
         data-src="{{ app(\App\Media\FilmVideoResolver::class)->youtubeEmbedUrl($film) }}"
         @else
             data-type="file"
         data-src="{{ app(\App\Media\FilmVideoResolver::class)->fileUrl($film) }}"

        @endif
    >
        <img src="{{ app(\App\Media\FilmImageResolver::class)->image($film)}}"
             alt="Трейлер — {{ $film->title }}" class="film-trailer-poster">

        <button type="button" class="film-trailer-play" aria-label="Дивитись трейлер">
            <svg viewBox="0 0 68 48" width="68" height="48">
                <path d="M66.5,7.7c-0.8-2.9-2.4-5.4-4.9-6.9C58.1,0,34,0,34,0S9.9,0,6.4,0.8C3.9,2.3,2.3,4.8,1.5,7.7C0,13.2,0,24,0,24s0,10.8,1.5,16.3c0.8,2.9,2.4,5.4,4.9,6.9C9.9,48,34,48,34,48s24.1,0,27.6-0.8c2.5-1.5,4.1-4,4.9-6.9C68,34.8,68,24,68,24S68,13.2,66.5,7.7z" fill="#c9302c"></path>
                <path d="M45,24L27,14v20L45,24z" fill="#fff"></path>
            </svg>
        </button>
    </div>

    @once
        <style>
            .film-trailer{
                position: relative;
                width: 100%;
                max-width: 640px;
                aspect-ratio: 16 / 9;
                border-radius: 8px;
                overflow: hidden;
                background: #000;
                cursor: pointer;
            }
            .film-trailer-poster{
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                filter: brightness(0.75);
            }
            .film-trailer-play{
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: none;
                border: none;
                padding: 0;
                cursor: pointer;
                filter: drop-shadow(0 2px 6px rgba(0,0,0,0.5));
                transition: transform .2s ease;
            }
            .film-trailer-play:hover{
                transform: translate(-50%, -50%) scale(1.08);
            }
            .film-trailer iframe,
            .film-trailer video{
                width: 100%;
                height: 100%;
                display: block;
                border: 0;
            }
        </style>
    @endonce

    @push('scripts')
        <script>
            document.querySelectorAll('.film-trailer').forEach(function (wrap) {
                wrap.addEventListener('click', function () {
                    const type = wrap.dataset.type;
                    const src = wrap.dataset.src;

                    let player;
                    if (type === 'youtube') {
                        player = document.createElement('iframe');
                        player.src = src + '?autoplay=1&rel=0';
                        player.allow = 'autoplay; encrypted-media; picture-in-picture';
                        player.allowFullscreen = true;
                    } else {
                        player = document.createElement('video');
                        player.src = src;
                        player.controls = true;
                        player.autoplay = true;
                    }

                    wrap.innerHTML = '';
                    wrap.appendChild(player);
                }, { once: true });
            });
        </script>
    @endpush
@endif
