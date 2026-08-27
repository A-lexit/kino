@extends('admin.layouts.layout')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Новий фільм</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Новий фільм</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Додати новий фільм</h3>
                        </div>

                        <form role="form"
                              method="post"
                              action="{{ route('admin.films.store') }}"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="card-body">
                                {{-- Назва --}}
                                <div class="form-group mb-3">
                                    <label for="title">Назва <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="title"
                                           id="title"
                                           class="form-control @error('title') is-invalid @enderror"
                                           placeholder="Назва"
                                           value="{{ old('title') }}"
                                           required>
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="slug">Slug (URL сторінки)</label>
                                    <input type="text"
                                           name="slug"
                                           id="slug"
                                           class="form-control @error('slug') is-invalid @enderror"
                                           value="{{ old('slug', $film->slug ?? '') }}">
                                    <small class="form-text text-muted">
                                        При створенні залиште порожнім для автогенерації. При редагуванні не змінюйте без нагальної потреби, щоб не втратити SEO-трафік!
                                    </small>
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
                                           value="{{ old('origin_title') }}">
                                    @error('origin_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @php
                                    use App\Enums\CategorySlug;
                                @endphp

                                {{-- Категорія --}}
                                <div class="form-group mb-3">
                                    <label for="category_id">Категорія <span class="text-danger">*</span></label>
                                    <select name="category_id"
                                            id="category_id"
                                            class="form-control @error('category_id') is-invalid @enderror"
                                            required>
                                        <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>
                                            Обрати категорію...
                                        </option>
                                        @foreach ($formData['categories'] as $id => $title)
                                            <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Вибір категорії є обов'язковим для створення фільму.
                                    </small>
                                </div>

                                {{-- Поля для серіалів --}}
                                <div id="serial-fields" style="display: none;">
                                    <div class="form-group mb-3">
                                        <label for="season_id">Кількість сезонів</label>
                                        <select name="season_id"
                                                id="season_id"
                                                class="form-control @error('season_id') is-invalid @enderror">
                                            <option value="" {{ old('season_id') ? '' : 'selected' }}>
                                                Обрати кількість сезонів
                                            </option>
                                            @foreach ($formData['seasons'] as $id => $title)
                                                <option value="{{ $id }}"
                                                    {{ old('season_id') == $id ? 'selected' : '' }}>
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
                                            <option value="" {{ old('status_id') ? '' : 'selected' }}>
                                                Обрати статус
                                            </option>
                                            @foreach ($formData['statuses'] as $id => $title)
                                                <option value="{{ $id }}"
                                                    {{ old('status_id') == $id ? 'selected' : '' }}>
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
                                                {{ in_array($id, old('genres', [])) ? 'selected' : '' }}>
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
                                                {{ in_array($id, old('selections', [])) ? 'selected' : '' }}>
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
                                        <option value="" {{ old('year_id') ? '' : 'selected' }}>
                                            Обрати рік випуску
                                        </option>
                                        @foreach ($formData['years'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('year_id') == $id ? 'selected' : '' }}>
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
                                                {{ in_array($id, old('related_films', [])) ? 'selected' : '' }}>
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
                                              placeholder="Цитата ...">{{ old('description') }}</textarea>
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
                                                {{ in_array($id, old('actors', [])) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="other_actor">Інші актори (текст)</label>
                                    <input type="text"
                                           name="other_actor"
                                           id="other_actor"
                                           class="form-control @error('other_actor') is-invalid @enderror"
                                           placeholder="Інші актори"
                                           value="{{ old('other_actor') }}">
                                    @error('other_actor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Режисери / продюсери / композитори --}}
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
                                                {{ in_array($id, old('directors', [])) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

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
                                                {{ in_array($id, old('producers', [])) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

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
                                                {{ in_array($id, old('composers', [])) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Країни / компанії / мови / субтитри --}}
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
                                                {{ in_array($id, old('countries', [])) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

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
                                                {{ in_array($id, old('companies', [])) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

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
                                                {{ in_array($id, old('languages', [])) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

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
                                                {{ in_array($id, old('captions', [])) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Тривалість / якість / рейтинг / вік --}}
                                <div class="form-group mb-3">
                                    <label for="duration_id">Тривалість</label>
                                    <select name="duration_id"
                                            id="duration_id"
                                            class="form-control @error('duration_id') is-invalid @enderror">
                                        <option value="" {{ old('duration_id') ? '' : 'selected' }}>
                                            Обрати тривалість
                                        </option>
                                        @foreach ($formData['durations'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('duration_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('duration_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="quality_id">Якість відео</label>
                                    <select name="quality_id"
                                            id="quality_id"
                                            class="form-control @error('quality_id') is-invalid @enderror">
                                        <option value="" {{ old('quality_id') ? '' : 'selected' }}>
                                            Обрати якість відео
                                        </option>
                                        @foreach ($formData['qualities'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('quality_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('quality_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="rating_id">Рейтинг</label>
                                    <select name="rating_id"
                                            id="rating_id"
                                            class="form-control @error('rating_id') is-invalid @enderror">
                                        <option value="" {{ old('rating_id') ? '' : 'selected' }}>
                                            Обрати рейтинг
                                        </option>
                                        @foreach ($formData['ratings'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('rating_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('rating_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="age_id">Вікові обмеження</label>
                                    <select name="age_id"
                                            id="age_id"
                                            class="form-control @error('age_id') is-invalid @enderror">
                                        <option value="" {{ old('age_id') ? '' : 'selected' }}>
                                            Обрати мінімальний вік
                                        </option>
                                        @foreach ($formData['ages'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('age_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('age_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Примітка / сортування / лайки / перегляди --}}
                                <div class="form-group mb-3">
                                    <label for="note">Примітка</label>
                                    <input type="text"
                                           name="note"
                                           id="note"
                                           class="form-control @error('note') is-invalid @enderror"
                                           placeholder="Примітка"
                                           value="{{ old('note') }}">
                                    @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="sort_order">Порядок сортування</label>
                                    <input type="number"
                                           name="sort_order"
                                           id="sort_order"
                                           class="form-control @error('sort_order') is-invalid @enderror"
                                           min="0"
                                           value="{{ old('sort_order', 0) }}">
                                    @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="likes">Кількість лайків</label>
                                    <input type="number"
                                           name="likes"
                                           id="likes"
                                           class="form-control @error('likes') is-invalid @enderror"
                                           min="0"
                                           value="{{ old('likes', 0) }}">
                                    @error('likes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="views">Кількість переглядів</label>
                                    <input type="number"
                                           name="views"
                                           id="views"
                                           class="form-control @error('views') is-invalid @enderror"
                                           min="0"
                                           value="{{ old('views', 0) }}">
                                    @error('views')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                {{-- Постер --}}
                                <div class="image-section image-section--poster mb-4 mt-4">
                                    <h5 class="image-section__title">Постер фільму</h5>

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
                                             hidden>
                                        </div>

                                        <div class="mt-2">
                                            <img id="preview-thumbnail"
                                                 src=""
                                                 alt=""
                                                 class="img-thumbnail"
                                                 width="200"
                                                 hidden>
                                        </div>

                                        <button type="button"
                                                id="remove-thumbnail"
                                                class="btn btn-sm btn-danger remove-image-btn mt-2"
                                                data-target="thumbnail"
                                                data-preview="#preview-thumbnail"
                                                hidden>
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
                                            @endphp

                                            <div class="form-group mb-3">

                                                <label for="{{ $field }}">
                                                    Зображення галереї {{ $n }}
                                                </label>

                                                <div>
                                                    <input type="file"
                                                           name="{{ $field }}"
                                                           id="{{ $field }}"
                                                           class="form-control image-upload-preview"
                                                           data-preview="#preview-{{ $field }}"
                                                           accept="image/*">
                                                </div>

                                                <div id="filename-{{ $field }}"
                                                     class="mt-2"
                                                     hidden>
                                                </div>

                                                <div class="mt-2">
                                                    <img id="preview-{{ $field }}"
                                                         src=""
                                                         alt=""
                                                         class="img-thumbnail"
                                                         width="200"
                                                         hidden>
                                                </div>

                                                <button type="button"
                                                        id="remove-{{ $field }}"
                                                        class="btn btn-sm btn-danger remove-image-btn mt-2"
                                                        data-target="{{ $field }}"
                                                        data-preview="#preview-{{ $field }}"
                                                        hidden>
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
                                    <label for="trailer_youtube_url">Трейлер — посилання YouTube</label>
                                    <input type="text"
                                           name="trailer_youtube_url"
                                           id="trailer_youtube_url"
                                           class="form-control @error('trailer_youtube_url') is-invalid @enderror"
                                           placeholder="https://www.youtube.com/watch?v=..."
                                           value="{{ old('trailer_youtube_url') }}">
                                    <small class="form-text text-muted">
                                        Або встав власний файл нижче — заповнювати обидва поля не потрібно.
                                    </small>
                                    @error('trailer_youtube_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="trailer_file">Трейлер — власний відеофайл (mp4/webm/ogg)</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file"
                                                   name="trailer_file"
                                                   id="trailer_file"
                                                   class="custom-file-input"
                                                   accept="video/mp4,video/webm,video/ogg">
                                            <label class="custom-file-label" for="trailer_file">Вибрати файл</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Чекбокси --}}
                                <div class="form-group mb-3">
                                    <label>
                                        <input type="checkbox"
                                               class="minimal"
                                               name="is_featured"
                                               value="1"
                                            {{ old('is_featured') ? 'checked' : '' }}>
                                        Рекомендувати
                                    </label>
                                </div>

                                <div class="form-group mb-3">
                                    <label>
                                        <input type="checkbox"
                                               name="publish_status"
                                               value="published"
                                            {{ old('publish_status') === 'published' ? 'checked' : '' }}>
                                        Опублікувати
                                    </label>
                                </div>



                                {{-- Дата --}}
                                <div class="form-group mb-3">
                                    <label>Дата публікації</label>
                                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                        <input type="text"
                                               name="datepicker"
                                               class="form-control datetimepicker-input @error('datepicker') is-invalid @enderror"
                                               data-target="#reservationdate"
                                               value="{{ old('datepicker') }}">
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

                                {{-- IMDb (readonly) --}}
                                <div class="form-group mb-3">
                                    <label class="form-label">Рейтинг IMDb</label>
                                    <input type="text"
                                           class="form-control"
                                           value="Дані про рейтинг IMDb відсутні"
                                           readonly
                                           disabled>
                                    <small class="form-text text-muted">
                                        Рейтинг розраховується автоматично та не підлягає ручному редагуванню.
                                    </small>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary px-5 py-1 fs-6">
                                        Зберегти
                                    </button>
                                </div>
                            </div>
                        </form>
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

