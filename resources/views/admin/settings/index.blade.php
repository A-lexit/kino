@extends('admin.layouts.layout')
@section('content')

    @php
        $imageMedia = app(\App\Media\ImageMedia::class);

        $hasFavicon = $settings?->favicon
            && $imageMedia->exists($settings->favicon);

        $hasLogo = $settings?->logo
            && $imageMedia->exists($settings->logo);

        $images = app(\App\Media\SettingsImageResolver::class);
    @endphp

    <div class="container">

        <h1>Всі налаштування</h1>

        <form action="{{ route('admin.settings.update') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <div>
                <h2 class="mt-1">
                    Метатеги головна сторінка:
                </h2>
            </div>


            {{-- TITLE --}}
            <div class="form-group">
                <label for="title">
                    Назва (title)
                </label>

                <input type="text"
                       name="title"
                       class="form-control @error('title') is-invalid @enderror"
                       id="title"
                       placeholder="Назва"
                       value="{{ old('title', $settings->title ?? '') }}"
                       oninput="updateCharacterCount(this, 'title-count', 70)">

                <span id="title-count">0</span>
                /
                <span id="title-max">70</span>

                @error('title')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>


            {{-- DESCRIPTION --}}
            <div class="form-group mt-3">
                <label for="meta-description">
                    Опис (description)
                </label>

                <textarea name="description"
                          class="form-control @error('description') is-invalid @enderror"
                          id="meta-description"
                          rows="3"
                          placeholder="Цитата ..."
                          oninput="updateCharacterCount(this, 'description-count', 160)">{{ old('description', $settings->description ?? '') }}</textarea>

                @error('description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

                <span id="description-count">0</span>
                /
                <span id="description-max">160</span>
            </div>


            {{-- FAVICON --}}
            <div class="image-section mt-4">
                <h5 class="image-section__title">
                    Фавікон
                </h5>

                <div class="form-group mb-0">

                    <label for="favicon">
                        Зображення
                    </label>

                    <input type="file"
                           name="favicon"
                           id="favicon"
                           class="form-control image-upload-preview @error('favicon') is-invalid @enderror"
                           data-preview="#preview-favicon"
                           accept="image/*">

                    @error('favicon')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror

                    <div id="filename-favicon"
                         class="image-filename mt-2"
                         @if (!$hasFavicon) hidden @endif>
                        {{ $hasFavicon ? basename($settings->favicon) : '' }}
                    </div>

                    <div class="mt-2">
                        <img id="preview-favicon"
                             src="{{ $images->favicon($settings) }}"
                             alt="Favicon"
                             width="32"
                             height="32">
                    </div>

                    <button type="button"
                            id="remove-favicon"
                            class="btn btn-sm btn-danger remove-image-btn mt-2"
                            data-target="favicon"
                            data-preview="#preview-favicon"
                            @if (!$hasFavicon) hidden @endif>
                        Видалити
                    </button>

                    <input type="hidden"
                           name="delete_favicon"
                           id="delete-favicon"
                           value="0">

                </div>
            </div>


            {{-- LOGO --}}
            <div class="image-section mt-4">
                <h5 class="image-section__title">
                    Логотип сайту
                </h5>

                <div class="form-group mb-0">

                    <label for="logo">
                        Зображення
                    </label>

                    <input type="file"
                           name="logo"
                           id="logo"
                           class="form-control image-upload-preview @error('logo') is-invalid @enderror"
                           data-preview="#preview-logo"
                           accept="image/*">

                    @error('logo')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror

                    <small class="form-text text-muted">
                        Одне зображення для ПК і мобільної версії —
                        розмір адаптується автоматично.
                    </small>

                    <div id="filename-logo"
                         class="image-filename mt-2"
                         @if (!$hasLogo) hidden @endif>
                        {{ $hasLogo ? basename($settings->logo) : '' }}
                    </div>

                    <div class="mt-2">
                        <img id="preview-logo"
                             src="{{ $images->logo($settings) }}"
                             alt="Логотип"
                             class="img-thumbnail"
                             style="max-width: 252px;">
                    </div>

                    <button type="button"
                            id="remove-logo"
                            class="btn btn-sm btn-danger remove-image-btn mt-2"
                            data-target="logo"
                            data-preview="#preview-logo"
                            @if (!$hasLogo) hidden @endif>
                        Видалити
                    </button>

                    <input type="hidden"
                           name="delete_logo"
                           id="delete-logo"
                           value="0">

                </div>
            </div>


            {{-- SAVE --}}
            <div class="mt-3">

                @if(auth()->user()?->isAdmin())

                    <button type="submit"
                            class="btn btn-primary">
                        Зберегти
                    </button>

                @elseif(auth()->user()?->isViewer())

                    <button type="button"
                            class="btn btn-primary opacity-50 cursor-not-allowed"
                            disabled
                            title="Зміна налаштувань доступна лише адміністратору">
                        Зберегти
                    </button>

                @endif

            </div>

        </form>


        {{-- SAVED META --}}
        <div class="container">

            <div class="row">
                <div class="col-12">

                    <h2 class="mt-5">
                        Збережені метатеги головна сторінка:
                    </h2>

                    <div class="meta-info mt-2">

                        <p>
                            Title:
                            {{ $settings->title ?? 'Default Title' }}
                        </p>

                        <p>
                            Description:
                            {!! $settings->description ?? 'Default Description' !!}
                        </p>

                    </div>

                </div>
            </div>

        </div>

    </div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
         * Видалення зображення
         */
        document.querySelectorAll('.remove-image-btn').forEach(function (button) {

            button.addEventListener('click', function () {

                const target = this.dataset.target;
                const preview = document.querySelector(this.dataset.preview);

                const input = document.getElementById(target);
                const deleteInput = document.getElementById(`delete-${target}`);
                const filename = document.getElementById(`filename-${target}`);

                if (deleteInput) {
                    deleteInput.value = '1';
                }

                if (input) {
                    input.value = '';
                }

                if (filename) {
                    filename.textContent = '';
                    filename.hidden = true;
                }

                if (preview) {
                    preview.src = '';
                    preview.hidden = true;
                }

                this.hidden = true;
            });

        });


        /*
         * Вибір нового зображення
         */
        document.querySelectorAll('.image-upload-preview').forEach(function (input) {

            input.addEventListener('change', function () {

                const target = this.name;
                const preview = document.querySelector(this.dataset.preview);

                const deleteInput = document.getElementById(`delete-${target}`);
                const removeButton = document.getElementById(`remove-${target}`);
                const filename = document.getElementById(`filename-${target}`);

                if (!this.files || !this.files[0]) {
                    return;
                }

                const file = this.files[0];

                if (!file.type.startsWith('image/')) {
                    this.value = '';

                    if (removeButton) {
                        removeButton.hidden = true;
                    }

                    return;
                }

                /*
                 * Новий файл скасовує delete.
                 */
                if (deleteInput) {
                    deleteInput.value = '0';
                }

                /*
                 * Назва файла.
                 */
                if (filename) {
                    filename.textContent = file.name;
                    filename.hidden = false;
                }

                /*
                 * Preview.
                 */
                if (preview) {
                    preview.src = URL.createObjectURL(file);
                    preview.hidden = false;
                }

                /*
                 * Кнопка видалення.
                 */
                if (removeButton) {
                    removeButton.hidden = false;
                }
            });

        });

    });
</script>

