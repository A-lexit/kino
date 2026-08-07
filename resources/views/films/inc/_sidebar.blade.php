<div class="sidebar-area hide-mobile">

    <div class="row-sidebar">
        <img width="685" height="390" src=
            "{{ app(\App\Media\FilmImageResolver::class)->image($film)}}"
        class="attachment-large size-large wp-post-image" alt="" decoding="async" fetchpriority="high"  sizes="(max-width: 685px) 100vw, 685px" />


        @include('films.inc.partials-sidebar.gallery',

[
    'images' => app(\App\Media\FilmImageResolver::class)->gallery($film)
])

        <p class="mt-4 mb-2">Дата виходу: {{ $film->display_date }}</p>

        <tr><div class="blog-title-area">
                <div class="tag-cloud-single">
                    <td class="first-col-film"><span><a href="{{route('ages.index')}}">Вік:</a></span></td>
                    <td><small><a href="{{ route('ages.show', ['slug'=>$film->age->slug]) }}" title="">{{$film->age->title}} </small></td>
                </div>
            </div></tr>


        <tr><div class="blog-title-area">

                <div class="tag-cloud-single">
                    <td class="first-col-film"><span><a href="{{route('qualities.index')}}">Якість відео:</a></span></td>
                    <td><small><a href="{{ route('qualities.show', ['slug'=>$film->quality->slug]) }}" title="">{{$film->quality->title}}</a> </small></td>
                </div>
            </div>
        </tr>

          <tr>
              <div class="blog-title-area">
                  <div class="tag-cloud-single">
                      <td class="first-col-film"><span><a href="{{route('ratings.index')}}">Рейтинг:</a></span></td>
                      <small> <a href="{{ route('ratings.show', ['slug'=>$film->rating->slug]) }}" title="">{{$film->rating->title}}</a> </small>
                  </div>
              </div>
          </tr>


        <tr><div class="blog-title-area">
                @if($film->selections->count())
                    <div class="tag-cloud-single">
                        <td class="first-col-film"><span><a href="{{route('selections.index')}}">Добірки:</a></span></td>
                        <td>@foreach($film->selections as $selection)
                                <small><a href="{{ route('selections.show', ['slug'=>$selection->slug]) }}" title="">{{$selection->title}}</a> </small>
                            @endforeach</td>
                    </div>
                @endif
            </div></tr>

        <tr><div class="blog-title-area">
                @if($film->languages->count())
                    <div class="tag-cloud-single">
                        <td class="first-col-film"><span><a href="{{route('languages.index')}}">Озвучка:</a></span></td>
                        <td>@foreach($film->languages as $language)
                                <small><a href="{{ route('languages.show', ['slug'=>$language->slug]) }}" title="">{{$language->title}}@if (!$loop->last),@endif</a> </small>
                            @endforeach</td>
                    </div>
                @endif
            </div></tr>

        <tr><div class="blog-title-area">
                @if($film->captions->count())
                    <div class="tag-cloud-single">
                        <td class="first-col-film"><span><a href="{{route('captions.index')}}">Субтитри:</a></span></td>
                        <td>@foreach($film->captions as $caption)
                                <small><a href="{{ route('captions.show', ['slug'=>$caption->slug]) }}" title="">{{$caption->title}}@if (!$loop->last),@endif</a> </small>
                            @endforeach</td>
                    </div>
                @endif
            </div></tr>

        @if($film->imdb_rating)
            <tr><div class="blog-title-area">
                    <div class="tag-cloud-single">
                        <td class="first-col-film"><span>IMDB:</span></td>
                        <td><small>⭐ {{ $film->imdb_rating }} / 10</small></td>
                    </div>
                </div></tr>
        @endif

    @isset($film->note)
            <tr><div class="blog-title-area">

                    <div class="tag-cloud-single">

                        <td class="first-col-film"><span>Примітка:</span></td>
                        <td><small><h6>{{$film->note}}</h6> </small></td>

                    </div>
                </div></tr>
        @endisset


        <div class="sidetitle bestfilmss mt-5">
        <h3>Кращі {{ $film->category->title }} (likes)</h3>

        <ul>
            @foreach($bestFilms as $sidefilm)
            <hr>
                <li><a href="{{route('single', ['category' => $film->category->slug,
'slug' => $sidefilm->slug])}}">{{$sidefilm->title}}</a></li>
            @endforeach
        </ul>

            <div class="sidetitle text-start mt-5">

                <h3>Обрані Фільми</h3>
                <ul>
            @foreach($featuredFilms as $featuredFilm)
                    <hr>
                    <li><a href="{{route('single', ['category' => $film->category->slug,
'slug' => $featuredFilm->slug])}}">{{$featuredFilm->title}}</a></li>


            @endforeach
                </ul>
            </div>
                <hr>
</div>


        <div class="sidetitle text-start mt-5">
        <h3 class="sidebar-title">
            Підписатися
        </h3>
        </div>
        @include('admin.layouts.alerts')
        <form action="{{ route('subscribe') }}" method="POST" class="mb-4">
            @csrf
            <div class="input-group">
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Ваш Email">
                <button class="btn btn-dark" type="submit">
                    <i class="bi bi-send"></i>
                </button>
            </div>
        </form>
        <hr>
    </div>

    <div class="sidetitle text-start mt-5">
    <h3 class="sidetitle sidebar-title mt-4">
        Скоро в кіно (API)
    </h3>
    </div>
    @foreach($upcomingMovies as $movie)

        <div class="py-2">
            <a href="#"
               class="d-block fw-semibold">

                {{ $movie['title'] }}
            </a>

            <small class="text-muted">

                <i class="bi bi-calendar-event me-1"></i>

                {{ $movie['release_date'] }}

            </small>
        </div>

        @unless($loop->last)
            <hr class="my-2">
        @endunless

    @endforeach


</div>

