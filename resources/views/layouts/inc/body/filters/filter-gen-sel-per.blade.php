@if(isset($genres) && $genres->isNotEmpty())
    <select name="genre"
            onchange="this.form.submit()"
            class="form-select">
        <option value="">Усі жанри</option>

        @foreach($genres as $genre)
            <option value="{{ $genre->slug }}"
                {{ request('genre') == $genre->slug ? 'selected' : '' }}>
                {{ $genre->title }}
            </option>
        @endforeach
    </select>
@endif

@if(isset($selections) && $selections->isNotEmpty())
    <select name="selection"
            onchange="this.form.submit()"
            class="form-select">
        <option value="">Усі добірки</option>

        @foreach($selections as $selection)
            <option value="{{ $selection->slug }}"
                {{ request('selection') == $selection->slug ? 'selected' : '' }}>
                {{ $selection->title }}
            </option>
        @endforeach
    </select>
@endif

@if(!empty($periods))
    <select name="period"
            onchange="this.form.submit()"
            class="form-select">
        <option value="">Усі роки</option>

        @foreach($periods as $value => $label)
            <option value="{{ $value }}"
                {{ request('period') == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
@endif
