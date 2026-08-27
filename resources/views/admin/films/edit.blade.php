@extends('admin.layouts.layout')
@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Редагування фільму</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Головна</a></li>
                        <li class="breadcrumb-item active">{{ $film->title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    @cannot('update', $film)
                        <div class="alert alert-info mx-3 mt-3">
                            <i class="bi bi-eye"></i>
                            Ви переглядаєте цей фільм у режимі "тільки читання" —
                            зберегти зміни не можна.
                        </div>
                    @endcannot

                    <div class="card">

                        <div class="card-header">
                            <h3 class="card-title">Фільм "{{ $film->title }}"</h3>
                        </div>

                        <form role="form"
                              method="post"
                              action="{{ route('admin.films.update', ['film' => $film->id]) }}"
                              enctype="multipart/form-data">

                            @csrf
                            @method('PUT')

                            <div class="card-body">

                                {{-- Назва --}}
                                <div class="form-group mb-3">
                                    <label for="title">
                                        Назва <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                           name="title"
                                           id="title"
                                           class="form-control @error('title') is-invalid @enderror"
                                           placeholder="Назва"
                                           value="{{ old('title', $film->title) }}"
                                           required>

                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="slug">Slug (URL сторінки)</label>

                                    @if(auth()->user()->isAdmin())
                                        <input type="text"
                                               name="slug"
                                               id="slug"
                                               class="form-control @error('slug') is-invalid @enderror"
                                               value="{{ old('slug', $film->slug) }}">
                                        <small class="form-text text-muted d-block mt-1">
                                            Останній збережений slug в базі: <strong>{{ $film->getOriginal('slug') }}</strong>
                                        </small>
                                        <small class="form-text text-muted">
                                            Обережно: зміна слага змінить URL сторінки та може вплинути на SEO!
                                        </small>
                                    @else
                                        <input type="text"
                                               id="slug"
                                               class="form-control"
                                               value="{{ $film->slug }}"
                                               readonly>
                                        <small class="form-text text-muted">
                                            Зміна URL доступна лише адміністраторам.
                                        </small>
                                    @endif

                                    @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Оригінальна назва --}}
                                <div class="form-group mb-3">
                                    <label for="origin_title">Оригінальна назва</label>

                                    <input type="text"
                                           name="origin_title"
                                           id="origin_title"
                                           class="form-control @error('origin_title') is-invalid @enderror"
                                           placeholder="Оригінальна назва"
                                           value="{{ old('origin_title', $film->origin_title) }}">

                                    @error('origin_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Категорія --}}
                                <div class="form-group mb-3">
                                    <label for="category_id">
                                        Категорія <span class="text-danger">*</span>
                                    </label>

                                    <select name="category_id"
                                            id="category_id"
                                            class="form-control @error('category_id') is-invalid @enderror"
                                            required>

                                        <option value=""
                                                disabled
                                            {{ old('category_id', $film->category_id ?? null) === null ? 'selected' : '' }}>
                                            Обрати категорію...
                                        </option>

                                        @foreach ($formData['categories'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('category_id', $film->category_id ?? null) == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <small class="form-text text-muted">
                                        Вибір категорії є обов'язковим для збереження змін.
                                    </small>
                                </div>

                                {{-- Поля для серіалів --}}
                                <div id="serial-fields" style="display: none;">

                                    <div class="form-group mb-3">
                                        <label for="season_id">Кількість сезонів</label>

                                        <select name="season_id"
                                                id="season_id"
                                                class="form-control @error('season_id') is-invalid @enderror">

                                            <option value=""
                                                {{ old('season_id', $film->season_id ?? null) === null ? 'selected' : '' }}>
                                                Обрати кількість сезонів
                                            </option>

                                            @foreach ($formData['seasons'] as $id => $title)
                                                <option value="{{ $id }}"
                                                    {{ old('season_id', $film->season_id ?? null) == $id ? 'selected' : '' }}>
                                                    {{ $title }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('season_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="status_id">Статус</label>

                                        <select name="status_id"
                                                id="status_id"
                                                class="form-control @error('status_id') is-invalid @enderror">

                                            <option value=""
                                                {{ old('status_id', $film->status_id ?? null) === null ? 'selected' : '' }}>
                                                Обрати статус
                                            </option>

                                            @foreach ($formData['statuses'] as $id => $title)
                                                <option value="{{ $id }}"
                                                    {{ old('status_id', $film->status_id ?? null) == $id ? 'selected' : '' }}>
                                                    {{ $title }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('status_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>

                                {{-- Жанри --}}
                                <div class="form-group mb-3">
                                    <label for="genres">Жанри</label>

                                    <select name="genres[]"
                                            id="genres"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір жанрів"
                                            style="width: 100%;">

                                        @foreach ($formData['genres'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('genres', $film->genres->pluck('id')->all())) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Добірки --}}
                                <div class="form-group mb-3">
                                    <label for="selections">Добірки</label>

                                    <select name="selections[]"
                                            id="selections"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір добірок"
                                            style="width: 100%;">

                                        @foreach ($formData['selections'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('selections', $film->selections->pluck('id')->all())) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Рік --}}
                                <div class="form-group mb-3">
                                    <label for="year_id">Рік випуску</label>

                                    <select name="year_id"
                                            id="year_id"
                                            class="form-control @error('year_id') is-invalid @enderror">

                                        <option value=""
                                            {{ old('year_id', $film->year_id ?? null) === null ? 'selected' : '' }}>
                                            Обрати рік випуску
                                        </option>

                                        @foreach ($formData['years'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('year_id', $film->year_id) == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Фільми з серії --}}
                                <div class="form-group mb-3">
                                    <label for="related_films">Фільми з серії</label>

                                    <select name="related_films[]"
                                            id="related_films"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір фільмів з серії"
                                            style="width: 100%;">

                                        @foreach ($allFilms as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array(
                                                    $id,
                                                    old('related_films', $film->relatedFilms->pluck('id')->all())
                                                ) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Опис --}}
                                <div class="form-group mb-3">
                                    <label for="description">Опис</label>

                                    <textarea name="description"
                                              id="description"
                                              class="form-control @error('description') is-invalid @enderror"
                                              rows="3"
                                              placeholder="Цитата ...">{{ old('description', $film->description) }}</textarea>

                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Актори --}}
                                <div class="form-group mb-3">
                                    <label for="actors">ТОП-актори</label>

                                    <select name="actors[]"
                                            id="actors"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір ТОП-акторів"
                                            style="width: 100%;">

                                        @foreach ($formData['actors'] as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('actors', $film->actors->pluck('id')->all())) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Інші актори --}}
                                <div class="form-group mb-3">
                                    <label for="other_actor">Інші актори (текст)</label>

                                    <input type="text"
                                           name="other_actor"
                                           id="other_actor"
                                           class="form-control @error('other_actor') is-invalid @enderror"
                                           placeholder="Інші актори"
                                           value="{{ old('other_actor', $film->other_actor) }}">

                                    @error('other_actor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Режисери --}}
                                <div class="form-group mb-3">
                                    <label for="directors">Режисери</label>

                                    <select name="directors[]"
                                            id="directors"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір режисерів"
                                            style="width: 100%;">

                                        @foreach ($formData['directors'] as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('directors', $film->directors->pluck('id')->all())) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Продюсери --}}
                                <div class="form-group mb-3">
                                    <label for="producers">Продюсери</label>

                                    <select name="producers[]"
                                            id="producers"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір продюсерів"
                                            style="width: 100%;">

                                        @foreach ($formData['producers'] as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('producers', $film->producers->pluck('id')->all())) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Композитори --}}
                                <div class="form-group mb-3">
                                    <label for="composers">Композитори</label>

                                    <select name="composers[]"
                                            id="composers"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір композиторів"
                                            style="width: 100%;">

                                        @foreach ($formData['composers'] as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('composers', $film->composers->pluck('id')->all())) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Країни --}}
                                <div class="form-group mb-3">
                                    <label for="countries">Країни</label>

                                    <select name="countries[]"
                                            id="countries"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір країн"
                                            style="width: 100%;">

                                        @foreach ($formData['countries'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('countries', $film->countries->pluck('id')->all())) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Кінокомпанії --}}
                                <div class="form-group mb-3">
                                    <label for="companies">Кінокомпанії</label>

                                    <select name="companies[]"
                                            id="companies"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір кінокомпаній"
                                            style="width: 100%;">

                                        @foreach ($formData['companies'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('companies', $film->companies->pluck('id')->all())) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Мови --}}
                                <div class="form-group mb-3">
                                    <label for="languages">Мови озвучки</label>

                                    <select name="languages[]"
                                            id="languages"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір мов озвучки"
                                            style="width: 100%;">

                                        @foreach ($formData['languages'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('languages', $film->languages->pluck('id')->all())) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Субтитри --}}
                                <div class="form-group mb-3">
                                    <label for="captions">Субтитри</label>

                                    <select name="captions[]"
                                            id="captions"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір субтитрів"
                                            style="width: 100%;">

                                        @foreach ($formData['captions'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('captions', $film->captions->pluck('id')->all())) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                {{-- Тривалість --}}
                                <div class="form-group mb-3">
                                    <label for="duration_id">Тривалість</label>

                                    <select name="duration_id"
                                            id="duration_id"
                                            class="form-control @error('duration_id') is-invalid @enderror">

                                        <option value=""
                                            {{ old('duration_id', $film->getRawOriginal('duration_id')) === null ? 'selected' : '' }}>
                                            Обрати тривалість
                                        </option>

                                        @foreach ($formData['durations'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('duration_id', $film->getRawOriginal('duration_id')) == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('duration_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Якість --}}
                                <div class="form-group mb-3">
                                    <label for="quality_id">Якість відео</label>

                                    <select name="quality_id"
                                            id="quality_id"
                                            class="form-control @error('quality_id') is-invalid @enderror">

                                        <option value=""
                                            {{ old('quality_id', $film->quality_id ?? null) === null ? 'selected' : '' }}>
                                            Обрати якість відео
                                        </option>

                                        @foreach ($formData['qualities'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('quality_id', $film->quality_id) == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('quality_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Рейтинг --}}
                                <div class="form-group mb-3">
                                    <label for="rating_id">Рейтинг</label>

                                    <select name="rating_id"
                                            id="rating_id"
                                            class="form-control @error('rating_id') is-invalid @enderror">

                                        <option value=""
                                            {{ old('rating_id', $film->rating_id ?? null) === null ? 'selected' : '' }}>
                                            Обрати рейтинг
                                        </option>

                                        @foreach ($formData['ratings'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('rating_id', $film->rating_id) == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('rating_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Вікові обмеження --}}
                                <div class="form-group mb-3">
                                    <label for="age_id">Вікові обмеження</label>

                                    <select name="age_id"
                                            id="age_id"
                                            class="form-control @error('age_id') is-invalid @enderror">

                                        <option value=""
                                            {{ old('age_id', $film->age_id ?? null) === null ? 'selected' : '' }}>
                                            Обрати мінімальний вік
                                        </option>

                                        @foreach ($formData['ages'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('age_id', $film->age_id) == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('age_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Примітка --}}
                                <div class="form-group mb-3">
                                    <label for="note">Примітка</label>

                                    <input type="text"
                                           name="note"
                                           id="note"
                                           class="form-control @error('note') is-invalid @enderror"
                                           placeholder="Примітка"
                                           value="{{ old('note', $film->note) }}">

                                    @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Сортування --}}
                                <div class="form-group mb-3">
                                    <label for="sort_order">Порядок сортування</label>

                                    <input type="number"
                                           name="sort_order"
                                           id="sort_order"
                                           class="form-control @error('sort_order') is-invalid @enderror"
                                           min="0"
                                           value="{{ old('sort_order', $film->sort_order ?? 0) }}">

                                    @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Лайки --}}
                                <div class="form-group mb-3">
                                    <label for="likes">Кількість лайків</label>

                                    <input type="number"
                                           name="likes"
                                           id="likes"
                                           class="form-control @error('likes') is-invalid @enderror"
                                           min="0"
                                           value="{{ old('likes', $film->state->likes ?? 0) }}">

                                    @error('likes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Перегляди --}}
                                <div class="form-group mb-3">
                                    <label for="views">Кількість переглядів</label>

                                    <input type="number"
                                           name="views"
                                           id="views"
                                           class="form-control @error('views') is-invalid @enderror"
                                           min="0"
                                           value="{{ old('views', $film->state->views ?? 0) }}">

                                    @error('views')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>



                                {{-- Постер --}}
                                <div class="image-section image-section--poster mb-4 mt-4">
                                    <h5 class="image-section__title">Постер фільму</h5>

                                    @php
                                        $hasThumbnail = $film->thumbnail
                                            && app(\App\Media\ImageMedia::class)->exists($film->thumbnail);
                                    @endphp

                                    <div class="form-group mb-0">
                                        <label for="thumbnail">Зображення</label>

                                        <input type="file"
                                               name="thumbnail"
                                               id="thumbnail"
                                               class="form-control image-upload-preview"
                                               data-preview="#preview-thumbnail"
                                               accept="image/*">

                                        <div id="filename-thumbnail"
                                             class="image-filename mt-2"
                                             @if (!$hasThumbnail) hidden @endif>
                                            {{ $hasThumbnail ? basename($film->thumbnail) : '' }}
                                        </div>

                                        <div class="mt-2">
                                            <img id="preview-thumbnail"
                                                 src="{{ app(\App\Media\FilmImageResolver::class)->thumb($film) }}"
                                                 alt=""
                                                 class="img-thumbnail"
                                                 width="200">
                                        </div>

                                        <button type="button"
                                                id="remove-thumbnail"
                                                class="btn btn-sm btn-danger remove-image-btn mt-2"
                                                data-target="thumbnail"
                                                data-preview="#preview-thumbnail"
                                                @if (!$hasThumbnail) hidden @endif>
                                            Видалити
                                        </button>

                                        <input type="hidden"
                                               name="delete_thumbnail"
                                               id="delete-thumbnail"
                                               value="0">
                                    </div>
                                </div>


                                {{-- Галерея --}}
                                <div class="image-section image-section--gallery mb-4">
                                    <h5 class="image-section__title">Галерея зображень</h5>

                                    <div class="image-section__grid">

                                        @foreach ([1, 2, 3, 4, 5] as $n)
                                            @php
                                                $field = 'gal_image' . $n;
                                                $image = $film->{$field};

                                                $hasImage = $image
                                                    && app(\App\Media\ImageMedia::class)->exists($image);
                                            @endphp

                                            <div class="form-group">

                                                <label for="{{ $field }}">
                                                    Зображення галереї {{ $n }}
                                                </label>

                                                <input type="file"
                                                       name="{{ $field }}"
                                                       id="{{ $field }}"
                                                       class="form-control image-upload-preview"
                                                       data-preview="#preview-{{ $field }}"
                                                       accept="image/*">

                                                <div id="filename-{{ $field }}"
                                                     class="image-filename mt-2"
                                                     @if (!$hasImage) hidden @endif>
                                                    {{ $hasImage ? basename($image) : '' }}
                                                </div>

                                                <div class="mt-2">
                                                    <img id="preview-{{ $field }}"
                                                         src="{{ $hasImage
                             ? app(\App\Media\ImageMedia::class)->url($image)
                             : '' }}"
                                                         alt=""
                                                         class="img-thumbnail"
                                                         width="200"
                                                         @if (!$hasImage) hidden @endif>
                                                </div>

                                                <button type="button"
                                                        id="remove-{{ $field }}"
                                                        class="btn btn-sm btn-danger remove-image-btn mt-2"
                                                        data-target="{{ $field }}"
                                                        data-preview="#preview-{{ $field }}"
                                                        @if (!$hasImage) hidden @endif>
                                                    Видалити
                                                </button>

                                                <input type="hidden"
                                                       name="delete_{{ $field }}"
                                                       id="delete-{{ $field }}"
                                                       value="0">
                                            </div>
                                        @endforeach

                                    </div>
                                </div>










                                {{-- Трейлер --}}
                                <div class="form-group mb-3">
                                    <label for="trailer_youtube_url">
                                        Трейлер — посилання YouTube
                                    </label>

                                    <input type="text"
                                           name="trailer_youtube_url"
                                           id="trailer_youtube_url"
                                           class="form-control @error('trailer_youtube_url') is-invalid @enderror"
                                           placeholder="https://www.youtube.com/watch?v=..."
                                           value="{{ old(
                                               'trailer_youtube_url',
                                               $film->trailer_youtube_id
                                                   ? 'https://www.youtube.com/watch?v=' . $film->trailer_youtube_id
                                                   : ''
                                           ) }}">

                                    <small class="form-text text-muted">
                                        Або встав власний файл нижче — заповнювати обидва поля не потрібно.
                                    </small>

                                    @error('trailer_youtube_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="trailer_file">
                                        Трейлер — власний відеофайл (mp4/webm/ogg)
                                    </label>

                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file"
                                                   name="trailer_file"
                                                   id="trailer_file"
                                                   class="custom-file-input"
                                                   accept="video/mp4,video/webm,video/ogg">

                                            <label class="custom-file-label" for="trailer_file">
                                                Вибрати файл
                                            </label>
                                        </div>
                                    </div>

                                    @if($film->trailer_file)
                                        <div class="mt-2">
                                            <video src="{{ asset($film->trailer_file) }}"
                                                   controls
                                                   width="300">
                                            </video>
                                        </div>
                                    @endif
                                </div>

                                {{-- Чекбокс: рекомендувати --}}
                                <div class="form-group mb-3">
                                    <label>
                                        <input type="checkbox"
                                               class="minimal"
                                               name="is_featured"
                                               value="1"
                                            {{ old('is_featured', $film->is_featured) ? 'checked' : '' }}>

                                        Рекомендувати
                                    </label>
                                </div>

                                {{-- Чекбокс: опублікувати --}}
                                <div class="form-group mb-3">
                                    <label>
                                        <input type="checkbox"
                                               name="publish_status"
                                               value="published"
                                            {{ old(
                                                'publish_status',
                                                $film->publish_status === \App\Enums\FilmStatus::Published
                                                    ? 'published'
                                                    : null
                                            ) === 'published' ? 'checked' : '' }}>

                                        Опублікувати
                                    </label>
                                </div>

                                {{-- Дата --}}
                                <div class="form-group mb-3">
                                    <label>Дата публікації</label>

                                    <div class="input-group date"
                                         id="reservationdate"
                                         data-target-input="nearest">

                                        <input type="text"
                                               name="datepicker"
                                               class="form-control datetimepicker-input @error('datepicker') is-invalid @enderror"
                                               data-target="#reservationdate"
                                               value="{{ old('datepicker', $film->datepicker) }}">

                                        <div class="input-group-append"
                                             data-target="#reservationdate"
                                             data-toggle="datetimepicker">

                                            <div class="input-group-text">
                                                <i class="bi bi-calendar3"></i>
                                            </div>

                                        </div>
                                    </div>

                                    @error('datepicker')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- IMDb --}}
                                <div class="form-group mb-3">
                                    <label class="form-label">Рейтинг IMDb</label>

                                    <input type="text"
                                           class="form-control"
                                           value="{{ $film->imdb_rating
                                               ? number_format($film->imdb_rating, 1) . ' / 10'
                                               : 'Дані про рейтинг IMDb відсутні' }}"
                                           readonly
                                           disabled>

                                    <small class="form-text text-muted">
                                        Рейтинг розраховується автоматично та не підлягає ручному редагуванню.
                                    </small>
                                </div>

                                <div class="mt-4">
                                    @can('update', $film)
                                        <button type="submit"
                                                class="btn btn-primary btn-lg px-5 py-2 fs-5">
                                            Зберегти
                                        </button>
                                    @elseif (auth()->user()?->isViewer())
                                        <span title="Демо-режим: редагування доступне лише для Admin або автора фільму">
                                        <button type="button"
                                            class="btn btn-primary btn-lg px-5 py-2 fs-5 opacity-50 cursor-not-allowed"
                                            disabled>
                                            Зберегти (Demo)
                                            </button>
                                        </span>
                                    @endcan
                                </div>

                            </div>
                        </form>

                        {{-- IMDb fetcher --}}
                        <div class="card mt-3">
                            <div class="card-body">
                                <label class="form-label">Оновлення рейтингу IMDb</label>

                                @livewire('admin.imdb-rating-fetcher', ['film' => $film])
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@push('scripts')

    @include('admin.films.inc-js.serial-fields-script')
    @include('admin.films.inc-js.film-images-script')
    @include('admin.films.inc-js.date-picker-script')

@endpush
