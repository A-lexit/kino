@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <h1>Створити нове меню</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.menu.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="title">Назва меню:</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="items">Пункти меню (обирай у тому порядку, у якому вони мають з'явитись у шапці сайту):</label>
                <select id="items" name="items[]" multiple="multiple" class="form-control">
                    <optgroup label="Сторінки">
                        @foreach($staticPages as $key => $page)
                            <option value="static:{{ $key }}">{{ $page['label'] }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Категорії">
                        @foreach($allCategories as $category)
                            <option value="category:{{ $category->id }}">{{ $category->title }}</option>
                        @endforeach
                    </optgroup>
                </select>
                <small class="form-text text-muted">
                    Порядок вибору в списку = порядок пунктів у меню сайту.
                </small>
            </div>

            {{-- Нередагована підказка — просто довідка, категорії підтягуються з БД автоматично --}}
            <div class="alert alert-light border mt-2">
                <strong>Доступні для вибору пункти:</strong><br>
                Головна, Добірки, Актори, Режисери, Жанри
                @if($allCategories->count())
                    , {{ $allCategories->pluck('title')->implode(', ') }}
                @endif
            </div>

            <button type="submit" class="btn btn-primary mt-3">Зберегти</button>
        </form>

        <h2 class="mt-5">Всі меню</h2>
        <form action="{{ route('admin.menu.activate') }}" method="POST">
            @csrf
            <ul class="list-group">
                @foreach($allMenus as $menu)
                    <li class="list-group-item">
                        <input type="radio" name="menu_id" value="{{ $menu->id }}" id="menu_{{ $menu->id }}" {{ $menu->is_active ? 'checked' : '' }}>
                        <label for="menu_{{ $menu->id }}">
                            {{ $menu->title }}
                            <small class="text-muted">
                                {{ $menu->items
                                    ->sortBy('position')
                                    ->map(fn($item) => $item->getTitle())
                                    ->implode(' • ') }}
                            </small>
                        </label>
                    </li>
                @endforeach
            </ul>
            <button type="submit" class="btn btn-primary mt-3">Застосувати</button>
        </form>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#items').select2();
            });
        </script>
    @endpush
@endsection
