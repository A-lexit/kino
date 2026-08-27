{{-- Фільтр по категоріях --}}
@if(isset($categories) && $categories->isNotEmpty())
    <select name="category" @if($categories->count() > 1) onchange="this.form.submit()" @endif class="form-select">
        {{-- Показуємо "Усі категорії" тільки якщо їх більше однієї --}}
        @if($categories->count() > 1)
            <option value="">Усі категорії</option>
        @endif

        @foreach($categories as $category)
            <option value="{{ $category->slug }}" {{ (request('category') == $category->slug || $categories->count() == 1) ? 'selected' : '' }}>
                {{ $category->title }}
            </option>
        @endforeach
    </select>
@endif
