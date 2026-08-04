@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <h1>Всі налаштування</h1>
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div>
                <h2 class="mt-1">Метатеги головна сторінка:</h2>
            </div>
            <div class="form-group">
                <label for="title">Назва (title)</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" id="title" placeholder="Назва" value="{{ old('title', $settings->title ?? '') }}" oninput="updateCharacterCount(this, 'title-count', 70)">
                <span id="title-count">0</span> / <span id="title-max">70</span>
                @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mt-3">
                <label for="description">Опис (description)</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="meta-description" rows="3" placeholder="Цитата ..." oninput="updateCharacterCount(this, 'description-count', 160)">{{ old('description', $settings->description ?? '') }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <span id="description-count">0</span> / <span id="description-max">160</span>
            </div>
            <div class="form-group mt-3">
                <label for="favicon">Фавікон</label>
                <input type="file" name="favicon" class="form-control-file @error('favicon') is-invalid @enderror" id="favicon">
                @error('favicon')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if ($settings && $settings->favicon)
                    <div><img src="{{ app(\App\Media\ImageMedia::class)->url($settings->favicon) }}" alt="Favicon" width="32" height="32" class="mt-2"></div>
                @endif
            </div>

            <div class="form-group mt-3">
                <label for="logo">Логотип сайту</label>
                <input type="file" name="logo" class="form-control-file @error('logo') is-invalid @enderror" id="logo">
                @error('logo')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Одне зображення для ПК і мобільної версії — розмір адаптується автоматично.</small>
                @if ($settings && $settings->logo)
                    <div><img src="{{ app(\App\Media\ImageMedia::class)->url($settings->logo) }}" alt="Логотип" class="mt-2 img-thumbnail" style="max-width: 252px;"></div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary mt-3">Зберегти</button>
        </form>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="mt-5">Збережені метатеги головна сторінка:</h2>
                    <div class="meta-info mt-2">
                        <p>Title: {{ $settings->title ?? 'Default Title' }}</p>
                        <p>Description: {!! $settings->description ?? 'Default Description' !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
