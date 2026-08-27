{{-- Сортування --}}
<select name="sort" id="sort-select" onchange="this.form.submit()" class="form-select">
    <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Новинки</option>
    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Спершу старі</option>
    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Популярні</option>
    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>За алфавітом</option>
    <option value="year_desc" {{ request('sort') == 'year_desc' ? 'selected' : '' }}>За роком (новіші)</option>
    <option value="year_asc" {{ request('sort') == 'year_asc' ? 'selected' : '' }}>За роком (старіші)</option>
</select>
