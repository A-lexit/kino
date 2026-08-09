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

                        <form role="form" method="post" action="{{ route('admin.films.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="title">Назва</label>
                                    <input type="text" name="title"
                                           class="form-control @error('title') is-invalid @enderror" id="title"
                                           placeholder="Назва" value="{{old('title')}}">
                                </div>


                                <div class="form-group">
                                    <label for="origin_title">Оригінальна назва</label>
                                    <input type="text" name="origin_title"
                                           class="form-control @error('origin_title') is-invalid @enderror" id="origin_title"
                                           placeholder="Оригінальна назва" value="{{old('origin_title')}}">
                                </div>


                                <div class="form-group">             {{--Бралось з demo adminlte  (textarea)--}}
                                    <label for="description">Опис</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                              id="description" rows="3" placeholder="Цитата ..." >{{old('description')}}</textarea>
                                </div>


                                <div class="form-group">
                                    <label for="duration">Тривалість (текст)</label>
                                    <input type="text" name="duration"
                                           class="form-control @error('duration') is-invalid @enderror" id="duration" value="{{old('duration')}}">
                                </div>


                                <div class="form-group">
                                    <label for="other_actor">Інші актори (текст)</label>
                                    <input type="text" name="other_actor"
                                           class="form-control @error('other_actor') is-invalid @enderror" id="other_actor"
                                           placeholder="Інші актори" value="{{old('other_actor')}}">
                                </div>



                                @php
                                    use App\Enums\CategorySlug;
                                @endphp

                                <div class="form-group">
                                    <label for="category_id">Категорія</label>

                                    <select class="form-control @error('category_id') is-invalid @enderror"
                                            id="category_id"
                                            name="category_id">

                                        <option value="" {{ old('category_id') ? '' : 'selected' }}>
                                            Обрати категорію
                                        </option>

                                        @foreach($formData['categories'] as $id => $title)
                                            @php
                                                $slug = match((int)$id) {
                                                    3 => CategorySlug::SERIALS->value,
                                                    5 => CategorySlug::MULTSERIALS->value,
                                                    default => ''
                                                };
                                            @endphp

                                            <option value="{{ $id }}"
                                                    data-slug="{{ $slug }}"
                                                {{ old('category_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Фільм без категорії зберігається як чернетка і не показується на сайті.</small>
                                </div>



                                <div id="serial-fields" style="display: none;">
                                    <div class="form-group">
                                        <label for="season_id">Кількість сезонів</label>
                                        <select class="form-control @error('season_id') is-invalid @enderror" id="season_id" name="season_id">

                                            <option value="" {{ old('season_id') ? '' : 'selected' }}>
                                                Обрати кількість сезонів
                                            </option>

                                            @foreach($formData['seasons'] as $id => $title)
                                                <option value="{{ $id }}" {{ old('season_id') == $id ? 'selected' : '' }}>
                                                    {{ $title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('season_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="form-group">
                                        <label for="status_id">Статус</label>
                                        <select class="form-control @error('status_id') is-invalid @enderror" id="status_id" name="status_id">

                                            <option value="" {{ old('status_id') ? '' : 'selected' }}>
                                                Обрати статус</option>

                                            @foreach($formData['statuses'] as $id => $title)
                                                <option value="{{ $id }}" {{ old('status_id') == $id ? 'selected' : '' }}>
                                                    {{ $title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>
                                </div>



                                <div class="form-group">
                                    <label for="duration_id">Тривалість</label>
                                    <select class="form-control @error('duration_id') is-invalid @enderror"
                                            id="duration_id"
                                            name="duration_id">

                                        <option value="" {{ old('duration_id') ? '' : 'selected' }}>
                                            Обрати тривалість
                                        </option>

                                        @foreach($formData['durations'] as $id => $title)
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




                                <div class="form-group">
                                    <label for="age_id">Вікові обмеження</label>
                                    <select class="form-control @error('age_id') is-invalid @enderror" id="age_id" name="age_id">

                                        <option value="" {{ old('age_id') ? '' : 'selected' }}>
                                            Обрати мінімальний вік
                                        </option>

                                        @foreach($formData['ages'] as $id => $title)
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



                                <div class="form-group">
                                    <label for="quality_id">Якість відео</label>
                                    <select class="form-control @error('quality_id') is-invalid @enderror"
                                            id="quality_id"
                                            name="quality_id">

                                        <option value="" {{ old('quality_id') ? '' : 'selected' }}>
                                            Обрати якість відео
                                        </option>

                                        @foreach($formData['qualities'] as $id => $title)
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

                                <div class="form-group">
                                    <label for="rating_id">Рейтинг</label>
                                    <select class="form-control @error('rating_id') is-invalid @enderror"
                                            id="rating_id"
                                            name="rating_id">

                                        <option value="" {{ old('rating_id') ? '' : 'selected' }}>
                                            Обрати рейтинг
                                        </option>

                                        @foreach($formData['ratings'] as $id => $title)
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


                                <div class="form-group">
                                    <label for="year_id">Рік випуску</label>
                                    <select class="form-control @error('year_id') is-invalid @enderror" id="year_id" name="year_id">

                                        <option value="" {{ old('year_id') ? '' : 'selected' }}>
                                            Обрати рік випуску
                                        </option>

                                        @foreach($formData['years'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ old('year_id') == $id ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>





                                <div class="form-group">
                                    <label for="genres">Жанри</label>
                                    <select name="genres[]"
                                            id="genres"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір жанрів"
                                            style="width: 100%;">
                                        @foreach($formData['genres'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('genres', [])) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="countries">Країни</label>
                                    <select name="countries[]"
                                            id="countries"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір країн"
                                            style="width: 100%;">
                                        @foreach($formData['countries'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('countries', [])) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="companies">Кінокомпанії</label>
                                    <select name="companies[]"
                                            id="companies"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір кінокомпаній"
                                            style="width: 100%;">
                                        @foreach($formData['companies'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('companies', [])) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="producers">Продюсери</label>
                                    <select name="producers[]"
                                            id="producers"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір продюсерів"
                                            style="width: 100%;">
                                        @foreach($formData['producers'] as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('producers', [])) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="directors">Режисери</label>
                                    <select name="directors[]"
                                            id="directors"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір режисерів"
                                            style="width: 100%;">
                                        @foreach($formData['directors'] as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('directors', [])) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="composers">Композитори</label>
                                    <select name="composers[]"
                                            id="composers"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір композиторів"
                                            style="width: 100%;">
                                        @foreach($formData['composers'] as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('composers', [])) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="languages">Мови озвучки</label>
                                    <select name="languages[]"
                                            id="languages"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір мов озвучки"
                                            style="width: 100%;">
                                        @foreach($formData['languages'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('languages', [])) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="captions">Субтитри</label>
                                    <select name="captions[]"
                                            id="captions"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір субтитрів"
                                            style="width: 100%;">
                                        @foreach($formData['captions'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('captions', [])) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="selections">Добірки</label>
                                    <select name="selections[]"
                                            id="selections"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір добірок"
                                            style="width: 100%;">
                                        @foreach($formData['selections'] as $id => $title)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('selections', [])) ? 'selected' : '' }}>
                                                {{ $title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="actors">ТОП-актори</label>
                                    <select name="actors[]"
                                            id="actors"
                                            class="select2"
                                            multiple="multiple"
                                            data-placeholder="Вибір ТОП-акторів"
                                            style="width: 100%;">
                                        @foreach($formData['actors'] as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ in_array($id, old('actors', [])) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>







                                <div class="form-group">
                                    <label for="note">Примітка</label>
                                    <input type="text"
                                           name="note"
                                           class="form-control @error('note') is-invalid @enderror"
                                           id="note"
                                           placeholder="Примітка"
                                           value="{{ old('note') }}">

                                    @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>



                                <div class="image-section image-section--poster mb-4 mt-4">
                                    <h5 class="image-section__title">Постер фільму</h5>
                                    <div class="form-group">
                                        <label for="thumbnail">Зображення</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="thumbnail" id="thumbnail" class="custom-file-input image-upload-preview" data-preview="#preview-thumbnail">
                                                <label class="custom-file-label" for="thumbnail">Вибрати файл</label>
                                            </div>
                                        </div>
                                        <div>
                                            <img id="preview-thumbnail" alt="" class="img-thumbnail mt-1" width="200" style="display:none;">
                                        </div>
                                    </div>
                                </div>


                                <div class="image-section image-section--gallery">
                                    <h5 class="image-section__title">Галерея зображень</h5>
                                    <div class="image-section__grid">
                                        <div class="form-group">
                                            <label for="gal_image1">Зображення галереї 1</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="gal_image1" id="gal_image1" class="custom-file-input image-upload-preview" data-preview="#preview-gal_image1">
                                                    <label class="custom-file-label" for="gal_image1">Вибрати файл</label>
                                                </div>
                                            </div>
                                            <div>
                                                <img id="preview-gal_image1" alt="" class="img-thumbnail mt-1" width="200" style="display:none;">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="gal_image2">Зображення галереї 2</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="gal_image2" id="gal_image2" class="custom-file-input image-upload-preview" data-preview="#preview-gal_image2">
                                                    <label class="custom-file-label" for="gal_image2">Вибрати файл</label>
                                                </div>
                                            </div>
                                            <div>
                                                <img id="preview-gal_image2" alt="" class="img-thumbnail mt-1" width="200" style="display:none;">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="gal_image3">Зображення галереї 3</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="gal_image3" id="gal_image3" class="custom-file-input image-upload-preview" data-preview="#preview-gal_image3">
                                                    <label class="custom-file-label" for="gal_image3">Вибрати файл</label>
                                                </div>
                                            </div>
                                            <div>
                                                <img id="preview-gal_image3" alt="" class="img-thumbnail mt-1" width="200" style="display:none;">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="gal_image4">Зображення галереї 4</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="gal_image4" id="gal_image4" class="custom-file-input image-upload-preview" data-preview="#preview-gal_image4">
                                                    <label class="custom-file-label" for="gal_image4">Вибрати файл</label>
                                                </div>
                                            </div>
                                            <div>
                                                <img id="preview-gal_image4" alt="" class="img-thumbnail mt-1" width="200" style="display:none;">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="gal_image5">Зображення галереї 5</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="gal_image5" id="gal_image5" class="custom-file-input image-upload-preview" data-preview="#preview-gal_image5">
                                                    <label class="custom-file-label" for="gal_image5">Вибрати файл</label>
                                                </div>
                                            </div>
                                            <div>
                                                <img id="preview-gal_image5" alt="" class="img-thumbnail mt-1" width="200" style="display:none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>



                            @if (session('success'))
                                    <div class="form-group">
                                        <label for="uploaded-image">Завантажене зображення</label>
                                        <img src="{{ asset(session('webpPath')) }}" alt="Uploaded Image" class="img-fluid">
                                    </div>
                            @endif



                                <div class="form-group mt-4">
                                    <label for="trailer_youtube_url">Трейлер — посилання YouTube</label>
                                    <input type="text" name="trailer_youtube_url"
                                           class="form-control @error('trailer_youtube_url') is-invalid @enderror"
                                           id="trailer_youtube_url"
                                           placeholder="https://www.youtube.com/watch?v=..."
                                           value="{{ old('trailer_youtube_url') }}">
                                    <small class="form-text text-muted">Або встав власний файл нижче — заповнювати обидва поля не потрібно.</small>
                                </div>

                                <div class="form-group mt-2">
                                    <label for="trailer_file">Трейлер — власний відеофайл (mp4/webm/ogg)</label>
                                    <div class="input-group">
                                        <div class="custom-file mt-1">
                                            <input type="file" name="trailer_file" id="trailer_file" class="custom-file-input" accept="video/mp4,video/webm,video/ogg">
                                            <label class="custom-file-label" for="trailer_file">Вибрати файл</label>
                                        </div>
                                    </div>
                                </div>


                                <!-- checkbox -->
                                <div class="form-group mt-3">
                                    <label>
                                        <input type="checkbox"
                                               class="minimal"
                                               name="is_featured"
                                               value="1"
                                            {{ old('is_featured') ? 'checked' : '' }}>
                                        Рекомендувати
                                    </label>
                                </div>

                                <!-- checkbox -->
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox"
                                               name="publish_status"
                                               value="published"
                                            {{ old('publish_status') === 'published' ? 'checked' : '' }}>
                                        Опублікувати
                                    </label>
                                </div>


                                <div class="form-group mt-3">
                                    <label>Дата публікації:</label>
                                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                        <input type="text"
                                               class="form-control datetimepicker-input @error('datepicker') is-invalid @enderror"
                                               name="datepicker"
                                               data-target="#reservationdate"
                                               value="{{ old('datepicker') }}">
                                        <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="bi bi-calendar3"></i></div>
                                        </div>
                                    </div>

                                    @error('datepicker')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>


                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Зберегти</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.10/dist/js/tempus-dominus.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('reservationdate');
            if (container) {
                const picker = new tempusDominus.TempusDominus(container, {
                    display: {
                        components: {
                            clock: false
                        }
                    },
                    localization: {
                        format: 'dd.MM.yyyy'
                    }
                });

                const toggleIcon = container.querySelector('[data-toggle="datetimepicker"]');
                if (toggleIcon) {
                    toggleIcon.addEventListener('click', function () {
                        picker.toggle();
                    });
                }
            }

            const categorySelect = document.getElementById('category_id');
            const serialFields = document.getElementById('serial-fields');
            if (categorySelect && serialFields) {
                function toggleSerialFields() {
                    const selectedOption = categorySelect.options[categorySelect.selectedIndex];
                    const slug = selectedOption ? selectedOption.dataset.slug : '';
                    const isSerial = slug === '{{ CategorySlug::SERIALS->value }}' ||
                        slug === '{{ CategorySlug::MULTSERIALS->value }}';
                    serialFields.style.display = isSerial ? 'block' : 'none';
                }
                categorySelect.addEventListener('change', toggleSerialFields);
                toggleSerialFields();
            }

            document.querySelectorAll('.image-upload-preview').forEach(function (input) {
                input.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    const preview = document.querySelector(input.dataset.preview);
                    if (!file || !preview) return;
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = '';
                });
            });
        });
    </script>

@endpush

@push('styles')
    <style>
        .image-section {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 20px;
            background: #f8f9fa;
        }
        .image-section--poster {
            border-left: 4px solid #007bff;
        }
        .image-section--gallery {
            border-left: 4px solid #28a745;
        }
        .image-section__title {
            margin-bottom: 12px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: .5px;
            color: #495057;
        }
        .image-section__grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
        }
        .image-section__grid .form-group {
            margin-bottom: 0;
        }
    </style>
@endpush


